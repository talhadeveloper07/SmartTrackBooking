<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentItem;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Employee;
use App\Services\Business\Appointment\AppointmentService;
use App\Services\Business\Service\ServiceManagementService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Business $business)
    {
        $employees = Employee::where('business_id', $business->id)->orderBy('name')->get(['id','name']);
        $services  = Service::where('business_id', $business->id)->orderBy('name')->get(['id','name']);

        return view('business.admin.appointment.index', compact('business','employees','services'));
    }

    public function data(Request $request, Business $business)
{
    $query = Appointment::query()
        ->with(['customer.user','employee','service'])
        ->where('business_id', $business->id);

    // ✅ Filters
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('employee_id')) {
        $query->where('employee_id', (int)$request->employee_id);
    }

    if ($request->filled('service_id')) {
        $query->where('service_id', (int)$request->service_id);
    }

    // Date range: date_from, date_to (Y-m-d)
    if ($request->filled('date_from')) {
        $query->whereDate('appointment_date', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('appointment_date', '<=', $request->date_to);
    }

    return DataTables::eloquent($query)
        ->addIndexColumn()

        ->addColumn('customer', function ($appt) {
            return $appt->customer->user->name
                ?? $appt->customer->name
                ?? '—';
        })

        ->addColumn('service', fn($appt) => $appt->service->name ?? '—')
        ->addColumn('employee', fn($appt) => $appt->employee->name ?? '—')

        ->addColumn('date', function ($appt) {
            return Carbon::parse($appt->appointment_date)->format('d M Y');
        })

        ->addColumn('time', function ($appt) {
            $start = Carbon::parse($appt->start_time)->format('h:i A');
            $end   = Carbon::parse($appt->end_time)->format('h:i A');
            return "$start - $end";
        })

        ->addColumn('price', fn($appt) => '$' . number_format((float)($appt->price ?? 0), 2))

        ->addColumn('status_badge', function ($appt) {
            $s = $appt->status;
            $class = match ($s) {
                'confirmed' => 'badge bg-success',
                'pending'   => 'badge bg-warning',
                'cancelled' => 'badge bg-danger',
                'completed' => 'badge bg-primary',
                default     => 'badge bg-secondary',
            };
            return '<span class="'.$class.'">'.ucfirst($s).'</span>';
        })

        ->addColumn('actions', function ($appt) use ($business) {
            // Adjust routes if you have show/edit/cancel
            // $edit = route('business.appointments.edit', [$business->slug, $appt->id] ?? []);
            $show = route('business.appointments.show', [$business->slug, $appt->id] ?? []);

            return '
                <div class="d-flex gap-1">
                    <a class="btn btn-warning shadow btn-xs sharp me-1" href="'.$show.'"><i class="fa fa-eye"></i></a>
                <a class="btn btn-info shadow btn-xs sharp me-1" href="#"><i class="fa fa-pen"></i></a>
                </div>
            ';
        })

        ->rawColumns(['status_badge','actions'])
        ->toJson();
}

    public function create(Business $business)
    {
       $customers = Customer::with('user')
        ->where('business_id', $business->id)
        ->orderBy('id', 'desc')
        ->get();
        $services  = Service::where('business_id', $business->id)->get();

        return view('business.admin.appointment.create', compact('business', 'customers', 'services'));
    }

   public function serviceDetails(
    Business $business,
    Service $service,
    ServiceManagementService $serviceManagementService
    ) {
        return response()->json(
            $serviceManagementService->getServiceDetails($business, $service)
        );
    }

    /**
     * Slots API for ONE item (works for multi-items UI too)
     * Params:
     *  - service_id
     *  - employee_id
     *  - appointment_date
     *  - duration_minutes
     */
    public function availableSlots(Request $request, Business $business, AppointmentService $appointmentService)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer'],
            'employee_id' => ['required', 'integer'],
            'appointment_date' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ]);

        $date = Carbon::parse($validated['appointment_date'])->format('Y-m-d');

        $slots = $appointmentService->getAvailableSlots(
            business: $business,
            serviceId: (int) $validated['service_id'],
            employeeId: (int) $validated['employee_id'],
            date: $date,
            durationMinutes: (int) $validated['duration_minutes']
        );

        return response()->json([
            'slots' => $slots,
            'slots_count' => count($slots),
            'normalized_date' => $date,
        ]);
    }

    /**
     * Store MULTI service appointment
     *
     * Expected request:
     *  - customer_id OR new_customer_*
     *  - notes
     *  - items[]:
     *      items[0][service_id]
     *      items[0][duration_minutes]
     *      items[0][employee_id]
     *      items[0][appointment_date]
     *      items[0][start_time]
     *      items[0][price]
     */
    public function store(Request $request, Business $business, AppointmentService $appointmentService)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer'],

            'new_customer_name'  => ['nullable', 'string', 'max:255'],
            'new_customer_email' => ['nullable', 'email', 'max:255'],
            'new_customer_phone' => ['nullable', 'string', 'max:255'],

            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['required', 'integer'],
            'items.*.employee_id' => ['required', 'integer'],
            'items.*.appointment_date' => ['required', 'date'],
            'items.*.start_time' => ['required', 'date_format:H:i'],
            'items.*.duration_minutes' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
            'items.*.location' => ['nullable'],
        ]);

        try {
            $appointmentService->bookAppointment($business, auth()->user(), $validated);

            return redirect()
                ->route('business.appointments.index', $business->slug)
                ->with('success', 'Appointment booked successfully.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

 public function show(Business $business, Appointment $appointment)
{
    abort_if($appointment->business_id !== $business->id, 404);

    $appointment->load([
        'customer.user',
        'items.service',
        'items.employee',
    ]);

    return view('business.admin.appointment.show', compact('business', 'appointment'));
}

public function cancel(Request $request, Business $business, Appointment $appointment)
{
    abort_if($appointment->business_id !== $business->id, 404);

    if (in_array($appointment->status, ['cancelled','completed'], true)) {
        return back()->with('error', 'This appointment cannot be cancelled.');
    }

    $appointment->update([
        'status' => 'cancelled',
    ]);

    return back()->with('success', 'Appointment cancelled successfully.');
}

public function calendar(Business $business)
{
    $employees = $business->employees()->orderBy('name')->get(['id','name']);
    return view('business.admin.appointment.calendar', compact('business','employees'));
}
public function calendarEvents(Request $request, Business $business)
{
    $validated = $request->validate([
        'start' => ['required', 'date'],
        'end' => ['required', 'date'],
        'employee_id' => ['nullable', 'integer'],
        'status' => ['nullable', 'in:confirmed,pending,completed,cancelled'],
    ]);

    $start = Carbon::parse($validated['start'])->startOfDay();
    $end   = Carbon::parse($validated['end'])->endOfDay();

    $q = Appointment::query()
        ->where('business_id', $business->id)
        ->whereBetween('appointment_date', [$start->toDateString(), $end->toDateString()])
        ->with([
            'customer.user',
            'items.service:id,name',
            'items.employee:id,name',
        ]);

    if (!empty($validated['status'])) {
        $q->where('status', $validated['status']);
    }

    if (!empty($validated['employee_id'])) {
        $employeeId = (int) $validated['employee_id'];
        $q->whereHas('items', fn($iq) => $iq->where('employee_id', $employeeId));
    }

    $appointments = $q->orderBy('appointment_date')->orderBy('start_time')->get();

    $events = $appointments->map(function ($appt) {
        $date = Carbon::parse($appt->appointment_date)->format('Y-m-d');

        $startTime = $appt->start_time;
        $endTime   = $appt->end_time;

        if (!$startTime || !$endTime) {
            $minStart = $appt->items->min('start_time');
            $maxEnd   = $appt->items->max('end_time');
            $startTime = $startTime ?: $minStart;
            $endTime   = $endTime ?: $maxEnd;
        }

        $employeeNames = $appt->items
            ->pluck('employee.name')
            ->filter()
            ->unique()
            ->values();

        $employeeName = $employeeNames->count() === 1
            ? $employeeNames->first()
            : ($employeeNames->count() > 1 ? 'Multiple' : '—');

        $employeeIdForColor = $appt->items
            ->pluck('employee_id')
            ->filter()
            ->unique()
            ->values();

        $employeeIdForColor = $employeeIdForColor->count() === 1 ? (int)$employeeIdForColor->first() : 0;

        $customer = $appt->customer;
        $customerName = $customer?->user?->name ?? $customer?->name ?? '—';

        $services = $appt->items
            ->pluck('service.name')
            ->filter()
            ->values();

        $servicesCount = $services->count();
        $servicesSummary = $services->take(3)->implode(', ') . ($servicesCount > 3 ? '…' : '');

        $title = $customerName . ' • ' . $servicesCount . ' service' . ($servicesCount === 1 ? '' : 's');

        return [
            'id' => $appt->id,
            'title' => $title,
            'start' => $startTime ? "{$date}T{$this->toHHMMSS($startTime)}" : "{$date}T00:00:00",
            'end'   => $endTime ? "{$date}T{$this->toHHMMSS($endTime)}" : "{$date}T00:00:00",

            'employee_id' => $employeeIdForColor,
            'employee_name' => $employeeName,
            'customer_name' => $customerName,
            'status' => $appt->status,
            'services_count' => $servicesCount,
            'services_summary' => $servicesSummary,
        ];
    })->values();

    return response()->json([
        'events' => $events,
    ]);
}

private function toHHMMSS($time): string
{
    try {
        return Carbon::parse($time)->format('H:i:s');
    } catch (\Throwable $e) {
        $t = (string) $time;
        return strlen($t) === 5 ? $t . ':00' : $t;
    }
}
public function completeItem(Business $business, Appointment $appointment, AppointmentItem $item)
{
    abort_if($appointment->business_id !== $business->id, 404);
    abort_if($item->appointment_id !== $appointment->id, 404);

    $item->update(['status' => 'completed']);

    // ✅ sync parent status
    $appointment->syncStatusFromItems();

    return back()->with('success', 'Service marked as completed.');
}

public function cancelItem(Business $business, Appointment $appointment, AppointmentItem $item)
{
    abort_if($appointment->business_id !== $business->id, 404);
    abort_if($item->appointment_id !== $appointment->id, 404);

    $item->update(['status' => 'cancelled']);

    // ✅ sync parent status
    $appointment->syncStatusFromItems();

    return back()->with('success', 'Service cancelled.');
}

}