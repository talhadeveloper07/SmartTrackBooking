<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Employee;
use App\Models\AppointmentItem;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\BusinessSubscription;
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
    $plan = $business->plan; 

    // Case 1: No Plan Assigned
    if (!$plan) {
        return redirect()->back()->with('plan_error', 'No active subscription found. Please assign a plan to this business.');
    }

    // Case 2: Limit Reached
    $currentEmployeeCount = $business->employees()->count();
    if ($currentEmployeeCount >= $plan->max_employees) {
        return redirect()->back()->with('plan_error', "Limit Reached: Your '{$plan->name}' plan allows max {$plan->max_employees} employees. Please upgrade.");
    }

    // Case 3: Success - Show Form
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
    abort_if($employee->business_id !== $business->id, 404);

    $employee->load([
        'user',
        'services:id,name',
        'workingHours'
    ]);

    $schedule = $employee->workingHours->groupBy('day_of_week');

    $today   = Carbon::today()->toDateString();
    $nowTime = Carbon::now()->format('H:i:s');

    // Base query for this employee + business (only active statuses)
    $base = AppointmentItem::query()
        ->where('business_id', $business->id)
        ->where('employee_id', $employee->id)
        ->whereHas('appointment', function ($q) {
            $q->whereIn('status', ['confirmed', 'pending', 'completed']);
        });

    // ✅ Completed count
    $completedAppointments = (clone $base)
        ->whereHas('appointment', fn ($q) => $q->where('status', 'completed'))
        ->count();

    // ✅ In Progress count (today and current time between start/end)
    $inProgressAppointments = (clone $base)
        ->whereHas('appointment', fn ($q) => $q->whereIn('status', ['confirmed', 'pending']))
        ->whereDate('appointment_date', $today)
        ->whereTime('start_time', '<=', $nowTime)
        ->whereTime('end_time', '>=', $nowTime)
        ->count();

    // ✅ Upcoming base query (IMPORTANT: reuse same filter for count + list)
    $upcomingBase = AppointmentItem::query()
        ->where('business_id', $business->id)
        ->where('employee_id', $employee->id)
        ->whereHas('appointment', fn ($q) => $q->whereIn('status', ['confirmed', 'pending']))
        ->where(function ($q) use ($today, $nowTime) {
            $q->whereDate('appointment_date', '>', $today)
              ->orWhere(function ($q2) use ($today, $nowTime) {
                  $q2->whereDate('appointment_date', $today)
                     ->whereTime('start_time', '>', $nowTime);
              });
        });

    // ✅ Upcoming count (same logic)
    $upcomingAppointments = (clone $upcomingBase)->count();

    // ✅ Upcoming list (latest 4)
    $upcomingItems = (clone $upcomingBase)
        ->with([
            'service:id,name',
            'appointment:id,customer_id,status,notes',
            'appointment.customer:id,user_id,customer_id',
            'appointment.customer.user:id,name,email',
        ])
        ->orderBy('appointment_date')
        ->orderBy('start_time')
        ->limit(4)
        ->get();

    // ✅ total upcoming count to show in header (same as upcomingAppointments)
    $upcomingCount = $upcomingAppointments;

    return view('business.admin.employee.show', compact(
        'business',
        'employee',
        'schedule',
        'completedAppointments',
        'inProgressAppointments',
        'upcomingAppointments',
        'upcomingCount',
        'upcomingItems'     // ✅ MUST PASS THIS
    ));
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
