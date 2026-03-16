@extends('business.layouts.app')

@section('business_content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Appointments Calendar</h4>
        <a href="{{ route('business.appointments.index', $business->slug) }}" class="btn btn-light">
            <i class="fa fa-list me-2"></i> List View
        </a>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                <div class="col-12 col-md-4">
                    <label class="form-label">Employee</label>
                    <select id="filterEmployee" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}">{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Status</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">All Status</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="button" id="btnToday" class="btn btn-outline-secondary w-100">
                        Today
                    </button>
                    <button type="button" id="btnReset" class="btn btn-outline-danger w-100">
                        Reset
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Calendar --}}
    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>

</div>

{{-- FullCalendar CDN --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

{{-- Optional: bootstrap theme-ish spacing --}}
<style>
    #calendar { min-height: 700px; }
    .fc .fc-toolbar-title { font-size: 1.1rem; }
    .fc-event-title { font-weight: 600; }
    .fc-event-time { font-weight: 600; }
</style>

<script>
(function () {
    const businessSlug = @json($business->slug);

    // Employees map (id -> name) for tooltip display
    const employees = @json($employees->map(fn($e) => ['id'=>$e->id, 'name'=>$e->name])->values());

    // Helper: stable color per employee id
    function employeeColor(employeeId) {
        // stable hash -> HSL color
        const id = String(employeeId || '0');
        let hash = 0;
        for (let i = 0; i < id.length; i++) hash = id.charCodeAt(i) + ((hash << 5) - hash);
        const hue = Math.abs(hash) % 360;
        return `hsl(${hue}, 70%, 45%)`;
    }

    const filterEmployee = document.getElementById('filterEmployee');
    const filterStatus   = document.getElementById('filterStatus');
    const btnToday       = document.getElementById('btnToday');
    const btnReset       = document.getElementById('btnReset');

    const calendarEl = document.getElementById('calendar');

    // Your endpoint must return JSON events
    // Example URL (you will create route/controller): /{business}/admin/appointments/calendar-events
    const eventsUrl = () => {
        const url = new URL(`/${businessSlug}/admin/appointments/calendar-events`, window.location.origin);
        if (filterEmployee.value) url.searchParams.set('employee_id', filterEmployee.value);
        if (filterStatus.value) url.searchParams.set('status', filterStatus.value);
        return url.toString();
    };

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        firstDay: 1,
        nowIndicator: true,
        selectable: false,
        dayMaxEvents: true,

        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: true },

        events: async (info, successCallback, failureCallback) => {
            try {
                // also send current visible range so backend can limit results
                const url = new URL(eventsUrl());
                url.searchParams.set('start', info.startStr);
                url.searchParams.set('end', info.endStr);

                const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Failed to load events');
                const data = await res.json();

                // Expect data.events = [...]
                const events = (data.events || []).map(ev => {
                    const color = employeeColor(ev.employee_id);

                    return {
                        id: ev.id,
                        title: ev.title,              // e.g. "John Doe • 2 services"
                        start: ev.start,              // ISO string
                        end: ev.end,                  // ISO string
                        backgroundColor: color,
                        borderColor: color,
                        textColor: '#fff',
                        extendedProps: ev
                    };
                });

                successCallback(events);
            } catch (e) {
                console.error(e);
                failureCallback(e);
            }
        },

        eventDidMount: function (info) {
            const p = info.event.extendedProps || {};

            // Tooltip via title attribute (simple)
            // You can replace with Bootstrap tooltip if you want.
            const lines = [
                `Customer: ${p.customer_name || '-'}`,
                `Employee: ${p.employee_name || '-'}`,
                `Status: ${p.status || '-'}`,
                `Services: ${p.services_count ?? '-'}`,
                p.services_summary ? `Services: ${p.services_summary}` : null,
            ].filter(Boolean);

            info.el.setAttribute('title', lines.join('\n'));
        },

        eventClick: function (info) {
            const id = info.event.id;
            // Go to appointment show page
            window.location.href = `/${businessSlug}/admin/appointments/${id}`;
        }
    });

    calendar.render();

    function refetch() {
        calendar.refetchEvents();
    }

    filterEmployee.addEventListener('change', refetch);
    filterStatus.addEventListener('change', refetch);

    btnToday.addEventListener('click', () => calendar.today());

    btnReset.addEventListener('click', () => {
        filterEmployee.value = '';
        filterStatus.value = '';
        refetch();
    });
})();
</script>
@endsection