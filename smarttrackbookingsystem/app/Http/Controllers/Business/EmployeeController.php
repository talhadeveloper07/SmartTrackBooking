<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Service;
use DataTables;
use App\Services\Business\Employee\EmployeeService;


class EmployeeController extends Controller
{
    public function index(Business $business)
    {
        return view('business.admin.employee.index', compact('business'));
    }

    public function data(Request $request, Business $business)
    {
        if ($request->ajax()) {

            $query = Employee::where('business_id', $business->id);

            return DataTables::of($query)
                ->addIndexColumn()

                ->editColumn('joining_date', function ($row) {
                    return $row->joining_date
                        ? date('d M Y', strtotime($row->joining_date))
                        : '-';
                })

                ->editColumn('status', function ($row) {
                    return $row->status == 'active'
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })

                ->addColumn('action', function ($row) use ($business) {
                    $editUrl = route('business.employees', [$business->slug, $row->id]);
                    return '
                         <a href="' . route('business.employees.show', [$business->slug, $row->id]) . '" class="btn btn-warning shadow btn-xs sharp me-1"><i class="fa fa-eye"></i></a>
                        <a href="' . route('business.employees.edit', [$business->slug, $row->id]) . '" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fa fa-pencil"></i></a>
                        <button class="btn btn-xs btn-danger sharp delete-btn" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>
                    ';
                })

                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }
    public function create(Business $business)
    {
        $services = Service::where('business_id', $business->id)->orderBy('name')->get();
        return view('business.admin.employee.create', compact('business', 'services'));
    }

    public function store(Request $request, Business $business, EmployeeService $employeeService)
    {
        $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:255',
                'address' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'joining_date' => 'required|date',
                'status' => 'required|in:active,inactive',

                'services' => 'nullable|array',
                'services.*' => 'integer|exists:services,id',

                'hours' => 'required|array',
                'hours.*.is_off' => 'nullable|in:0,1',
                'hours.*.slots' => 'nullable|array',
                'hours.*.slots.*.start' => 'nullable|date_format:H:i',
                'hours.*.slots.*.end' => 'nullable|date_format:H:i',
            ]);

        try {
            $employeeService->create($business, $validated);

            return redirect()
                ->route('business.employees', $business->slug)
                ->with('success', 'Employee created successfully');

        } catch (\Throwable $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function show_employee(Business $business, Employee $employee)
    {
        // security: employee must belong to this business
        abort_if($employee->business_id !== $business->id, 404);

        $employee->load([
            'user',
            'services:id,name',
            'workingHours' // expects day_of_week, is_off, start_time, end_time
        ]);

        // group schedule by day
        $schedule = $employee->workingHours
            ->groupBy('day_of_week');

        return view('business.admin.employee.show', compact('business', 'employee', 'schedule'));
    }
    public function edit($businessSlug, $id, EmployeeService $employeeService)
    {
        $data = $employeeService->getEmployeeForEdit($businessSlug, $id);

        return view('business.admin.employee.edit', $data);
    }
    public function update(Request $request,Business $business,Employee $employee,EmployeeService $employeeService)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'status' => 'required|in:active,inactive',

            'services' => 'nullable|array',
            'services.*' => 'integer|exists:services,id',

            'hours' => 'required|array',
            'hours.*.is_enabled' => 'nullable|in:0,1',
            'hours.*.slots' => 'nullable|array',
            'hours.*.slots.*.start' => 'nullable|date_format:H:i',
            'hours.*.slots.*.end' => 'nullable|date_format:H:i',
        ]);

        try {

            $employeeService->update($business, $employee, $validated);

            return redirect()
                ->route('business.employees.edit', [$business->slug, $employee->id])
                ->with('success', 'Employee updated successfully.');

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
