<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Employee;
use App\Models\User;
use App\Models\Service;
use DataTables;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Notifications\EmployeeSetPasswordNotification;


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
                        <a href="' . route('org.business.edit', $row->id) . '" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fa fa-pencil"></i></a>
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

    public function store(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:255',
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

        DB::beginTransaction();
        try {
            // create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make('password'),
                'user_type' => 'employee'
            ]);

            // employee code if not given (TPR-xxxxxxx)
            if (empty($validated['employee_id'])) {
                $initials = strtoupper(
                    collect(preg_split('/\s+/', trim($business->name)))
                        ->filter()
                        ->map(fn($w) => Str::substr($w, 0, 1))
                        ->join('')
                );

                do {
                    $number = str_pad(rand(1, 9999999), 7, '0', STR_PAD_LEFT);
                    $code = $initials . '-' . $number;
                } while (Employee::where('employee_id', $code)->exists());

                $validated['employee_id'] = $code;
            }

            // create employee
            $employee = Employee::create([
                'business_id' => $business->id,
                'user_id' => $user->id,
                'employee_id' => $validated['employee_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'joining_date' => $validated['joining_date'],
                'status' => $validated['status'],
            ]);

            // sync services (only within business)
            $allowedServiceIds = Service::where('business_id', $business->id)->pluck('id')->toArray();
            $selected = array_values(array_intersect($validated['services'] ?? [], $allowedServiceIds));

            $syncData = [];
            foreach ($selected as $sid) {
                $syncData[$sid] = ['status' => 'active'];
            }
            $employee->services()->sync($syncData);

            // save working hours (delete/insert)
            foreach ($validated['hours'] as $day => $payload) {
                $isOff = !empty($payload['is_off']);

                if ($isOff) {
                    $employee->workingHours()->create([
                        'day_of_week' => (int) $day,
                        'is_off' => true,
                        'start_time' => null,
                        'end_time' => null,
                    ]);
                    continue;
                }

                foreach (($payload['slots'] ?? []) as $slot) {
                    if (empty($slot['start']) || empty($slot['end']))
                        continue;

                    $employee->workingHours()->create([
                        'day_of_week' => (int) $day,
                        'is_off' => false,
                        'start_time' => $slot['start'],
                        'end_time' => $slot['end'],
                    ]);
                }
            }

            DB::commit();

            $token = Password::broker()->createToken($user);
            $user->notify(new EmployeeSetPasswordNotification($token));

            return redirect()
                ->route('business.employees', $business->slug)
                ->with('success', 'Employee created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
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
    public function edit(Business $business, Employee $employee)
    {
        abort_if($employee->business_id !== $business->id, 404);

        $services = Service::where('business_id', $business->id)->get();

        $employee->load(['services', 'workingHours']);

        // prepare working hours grouped by day
        $hours = $employee->workingHours->groupBy('day_of_week');

        return view('business.admin.employee.edit', compact('business', 'employee', 'services', 'hours'));
    }
    public function update(Request $request, Business $business, Employee $employee)
    {
        // Security: employee must belong to this business
        abort_if($employee->business_id !== $business->id, 404);

        $validated = $request->validate([
            'employee_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'joining_date' => 'required|date',
            'status' => 'required|in:active,inactive',

            'services' => 'nullable|array',
            'services.*' => 'integer|exists:services,id',

            'hours' => 'required|array',
            'hours.*.is_enabled' => 'nullable|in:0,1',
            'hours.*.slots' => 'nullable|array',
            'hours.*.slots.*.start' => 'nullable|date_format:H:i',
            'hours.*.slots.*.end' => 'nullable|date_format:H:i',
        ]);

        DB::beginTransaction();
        try {
            // 1) Update user (login account)
            $employee->user()->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // 2) Update employee table
            $employee->update([
                'employee_id' => $validated['employee_id'] ?? $employee->employee_id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'joining_date' => $validated['joining_date'],
                'status' => $validated['status'],
            ]);

            // 3) Sync services (only those selected)
            $employee->services()->sync($validated['services'] ?? []);

            // 4) Replace working hours completely
            $employee->workingHours()->delete();

            foreach ($validated['hours'] as $day => $payload) {

                $isEnabled = !empty($payload['is_enabled']); // ✅ ON = enabled
                $isOff = !$isEnabled;

                if ($isOff) {
                    // store day-off marker
                    $employee->workingHours()->create([
                        'day_of_week' => (int) $day,
                        'is_off' => 1,
                        'start_time' => null,
                        'end_time' => null,
                    ]);
                    continue;
                }

                $slots = $payload['slots'] ?? [];

                // if enabled but no slots, you can either skip or create empty marker
                if (empty($slots)) {
                    $employee->workingHours()->create([
                        'day_of_week' => (int) $day,
                        'is_off' => 0,
                        'start_time' => null,
                        'end_time' => null,
                    ]);
                    continue;
                }

                foreach ($slots as $slot) {
                    if (empty($slot['start']) || empty($slot['end']))
                        continue;

                    $employee->workingHours()->create([
                        'day_of_week' => (int) $day,
                        'is_off' => 0,
                        'start_time' => $slot['start'],
                        'end_time' => $slot['end'],
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('business.employees.edit', [$business->slug, $employee->id])
                ->with('success', 'Employee updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
