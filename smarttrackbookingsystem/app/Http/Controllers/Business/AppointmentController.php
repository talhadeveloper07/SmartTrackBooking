<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Employee;
use App\Services\Business\Appointment\AppointmentService;
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

    public function serviceDetails(Business $business, Service $service)
    {
        abort_if($service->business_id !== $business->id, 404);

        $service->load([
            'durations' => fn($q) => $q->orderBy('duration_minutes'),
            'employees' => fn($q) => $q->orderBy('name'),
        ]);

        return response()->json([
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
            ],
            'durations' => $service->durations->map(fn($d) => [
                'id' => $d->id,
                'duration_minutes' => (int) $d->duration_minutes,
                'price' => $d->price !== null ? (float) $d->price : null,
            ])->values(),
            'employees' => $service->employees->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->name,
            ])->values(),
        ]);
    }

    public function availableSlots(Request $request, Business $business, AppointmentService $appointmentService)
    {
       $rawDate = $request->get('appointment_date');
$date = \Carbon\Carbon::parse($rawDate)->format('Y-m-d');
$dayIndex = (int)\Carbon\Carbon::parse($date)->format('w'); // 0 Sun ... 6 Sat

$employee = $business->employees()->findOrFail((int)$request->get('employee_id'));

$slots = $appointmentService->getAvailableSlots(
    business: $business,
    serviceId: (int)$request->get('service_id'),
    employeeId: (int)$request->get('employee_id'),
    date: $date,
    durationMinutes: (int)$request->get('duration_minutes')
);

return response()->json([
    'input' => $request->all(),
    'normalized_date' => $date,
    'dayIndex' => $dayIndex,
    'employee_hours' => $employee->hours,
    'slots_count' => count($slots),
    'slots' => $slots,
]);
    }

    public function store(Request $request, Business $business, AppointmentService $appointmentService)
    {
       $validated = $request->validate([
    'customer_id' => ['nullable','integer'],

    'new_customer_name'  => ['nullable','string','max:255'],
    'new_customer_email' => ['nullable','email','max:255'],
    'new_customer_phone' => ['nullable','string','max:255'],

    'service_id' => ['required','integer'],
    'employee_id' => ['required','integer'],
    'appointment_date' => ['required','date'],
    'start_time' => ['required','date_format:H:i'],
    'duration_minutes' => ['required','integer','min:1'],
    'price' => ['nullable','numeric','min:0'],
    'notes' => ['nullable','string'],
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
        'service',
        'employee',
        'customer.user', // if you have customer -> user
    ]);

    return view('business.admin.appointment.show', compact('business', 'appointment'));
}

public function cancel(Request $request, Business $business, Appointment $appointment)
{
    abort_if($appointment->business_id !== $business->id, 404);

    // only allow cancel if not completed/cancelled
    if (in_array($appointment->status, ['cancelled','completed'], true)) {
        return back()->with('error', 'This appointment cannot be cancelled.');
    }

    $appointment->update([
        'status' => 'cancelled',
    ]);

    return back()->with('success', 'Appointment cancelled successfully.');
}
}