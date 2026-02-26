@extends('business.layouts.app')

@section('business_content')

    @php
        $days = [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
        $employeeServices = $employee->services->pluck('id')->toArray();
    @endphp

 @if(session('success'))
                <script>
                    toastr.success("{{ session('success') }}");
                </script>
            @endif

            @if(session('error'))
                <script>
                    toastr.error("{{ session('error') }}");
                </script>
            @endif
    <div class="container">

        <div class="d-flex align-items-center mb-3">
            <h3 class="me-auto">Edit Employee — {{ ucwords($employee->name) }}</h3>
            <a href="{{ route('business.employees', $business->slug) }}" class="btn btn-light">Back</a>
        </div>

        <form method="POST" action="{{ route('business.employees.update', [$business->slug, $employee->id]) }}">
            @csrf
            @method('PUT')

            {{-- BASIC INFO --}}
            <div class="card mb-4">
                <div class="card-header"><strong>Employee Details</strong></div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Name</label>
                            <input name="name" class="form-control" value="{{ old('name', $employee->name) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input name="email" class="form-control" value="{{ old('email', $employee->email) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Phone</label>
                            <input name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            {{-- SERVICES --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <strong>Offered Services</strong>

        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
            <input type="checkbox" id="selectAllServices" class="form-check-input m-0">
            <span class="fw-semibold">Select All</span>
        </label>
    </div>

    <div class="card-body">
        @php
            // create blade: $oldServices = old('services', [])
            // edit blade: $oldServices = old('services', $employee->services->pluck('id')->toArray())
            $selectedServices = $oldServices ?? [];
        @endphp

        <div class="service-grid">
            @forelse($services as $srv)
                @php $checked = in_array($srv->id, $selectedServices); @endphp

                <div class="service-item">
                    <input type="checkbox"
                           class="service-check"
                           name="services[]"
                           id="srv_{{ $srv->id }}"
                           value="{{ $srv->id }}"
                           {{ $checked ? 'checked' : '' }}>

                    <label for="srv_{{ $srv->id }}" class="service-pill">
                        <span class="service-pill-icon"></span>
                        <span class="service-pill-text">{{ ucwords($srv->name) }}</span>
                    </label>
                </div>
            @empty
                <div class="text-muted">No services found. Please add services first.</div>
            @endforelse
        </div>
    </div>
</div>

            {{-- WORKING HOURS --}}
           {{-- ================= WORKING HOURS (UI like screenshot) ================= --}}
@php
    $days = [
        1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday',
        5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'
    ];

    // create: old('hours', [])
    // edit: $existingHours prepared from DB -> convert to this structure OR pass $oldHours as old(...)
    $oldHours = old('hours', $oldHours ?? []);

    // default enabled days (create only)
    $defaultEnabled = $defaultEnabled ?? [1,2,3,4,5,6]; // sunday off
@endphp

<div class="card mb-4">
    <div class="card-header"><strong>Agent Schedule</strong></div>

    <div class="card-body p-0">
        @foreach($days as $dayIndex => $dayName)

            @php
                $dayOld = $oldHours[$dayIndex] ?? null;

                // enabled?
                $isEnabled = $dayOld
                    ? !empty($dayOld['is_enabled'])
                    : in_array($dayIndex, $defaultEnabled);

                $slots = $dayOld['slots'] ?? [['start' => '10:30', 'end' => '17:00']];

                // summary from first slot
                $sumStart = $slots[0]['start'] ?? null;
                $sumEnd   = $slots[0]['end'] ?? null;
            @endphp

            <div class="schedule-row border-bottom">

                {{-- row header --}}
                <div class="d-flex align-items-center justify-content-between px-4 py-3">
                    <div class="d-flex align-items-center gap-3">
                        <label class="switch mb-0">
                            <input type="checkbox"
                                   class="day-toggle"
                                   data-day="{{ $dayIndex }}"
                                   name="hours[{{ $dayIndex }}][is_enabled]"
                                   value="1"
                                   {{ $isEnabled ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>

                        <div class="day-name {{ !$isEnabled ? 'day-off' : '' }}">
                            {{ $dayName }}
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="day-summary text-muted small" id="summary-{{ $dayIndex }}">
                            {{-- filled by JS --}}
                        </div>

                        <button type="button"
                                class="btn btn-link p-0 text-primary edit-day"
                                data-bs-toggle="collapse"
                                data-bs-target="#editDay{{ $dayIndex }}">
                            <i class="fa fa-pen"></i>
                        </button>
                    </div>
                </div>

                {{-- editor --}}
                <div class="collapse" id="editDay{{ $dayIndex }}">
                    <div class="px-4 pb-4">
                        <div class="schedule-editor border rounded-3 p-3 {{ !$isEnabled ? 'd-none' : '' }}"
                             id="editor-{{ $dayIndex }}">

                            <div class="slot-container" data-day="{{ $dayIndex }}">

                                @foreach($slots as $k => $slot)
                                    <div class="slot-row mb-2">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-5">
                                                <label class="form-label small text-muted">Start</label>
                                                <input type="time"
                                                       name="hours[{{ $dayIndex }}][slots][{{ $k }}][start]"
                                                       class="form-control"
                                                       value="{{ $slot['start'] ?? '' }}">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label small text-muted">Finish</label>
                                                <input type="time"
                                                       name="hours[{{ $dayIndex }}][slots][{{ $k }}][end]"
                                                       class="form-control"
                                                       value="{{ $slot['end'] ?? '' }}">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-outline-danger w-100 remove-slot">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                            <button type="button"
                                    class="btn btn-outline-primary w-100 add-slot"
                                    data-day="{{ $dayIndex }}">
                                <i class="fa fa-plus me-2"></i> Add another work period for {{ $dayName }}
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        @endforeach
    </div>
</div>

            <button class="btn btn-primary">Update Employee</button>
        </form>
    </div>


<script>
/** SERVICES: Select All **/
(function(){
    const selectAll = document.getElementById('selectAllServices');
    const checks = () => Array.from(document.querySelectorAll('.service-check'));

    function syncSelectAll(){
        const list = checks();
        if(!list.length) return;
        selectAll.checked = list.every(c => c.checked);
    }

    syncSelectAll();

    selectAll?.addEventListener('change', function(){
        checks().forEach(c => c.checked = this.checked);
    });

    document.addEventListener('change', function(e){
        if(e.target.classList.contains('service-check')) syncSelectAll();
    });
})();

/** SCHEDULE: toggle + summary + add/remove slots **/
function pad2(n){ return String(n).padStart(2,'0'); }
function to12(hhmm){
    if(!hhmm) return '';
    const [h,m] = hhmm.split(':').map(Number);
    let ampm = h >= 12 ? 'pm' : 'am';
    let h12 = h % 12; if(h12 === 0) h12 = 12;
    return `${pad2(h12)}:${pad2(m)}${ampm}`;
}
function updateDaySummary(day){
    const editor = document.getElementById(`editor-${day}`);
    const summaryEl = document.getElementById(`summary-${day}`);
    if(!summaryEl) return;

    // if editor hidden => clear
    if(editor && editor.classList.contains('d-none')){
        summaryEl.textContent = '';
        return;
    }

    const firstStart = document.querySelector(`input[name="hours[${day}][slots][0][start]"]`)?.value;
    const firstEnd   = document.querySelector(`input[name="hours[${day}][slots][0][end]"]`)?.value;

    if(firstStart && firstEnd){
        summaryEl.textContent = `${to12(firstStart)}–${to12(firstEnd)}`;
    }else{
        summaryEl.textContent = '';
    }
}

// init all summaries
[0,1,2,3,4,5,6].forEach(updateDaySummary);

// toggle enable/disable
document.addEventListener('change', function(e){
    if(!e.target.classList.contains('day-toggle')) return;
    const day = e.target.getAttribute('data-day');
    const editor = document.getElementById(`editor-${day}`);
    const dayName = e.target.closest('.schedule-row').querySelector('.day-name');
    const isEnabled = e.target.checked;

    if(editor) editor.classList.toggle('d-none', !isEnabled);
    if(dayName) dayName.classList.toggle('day-off', !isEnabled);

    updateDaySummary(day);
});

// update summary on time change
document.addEventListener('input', function(e){
    if(e.target.type === 'time' && e.target.name.includes('hours[')){
        const match = e.target.name.match(/hours\[(\d+)\]/);
        if(match) updateDaySummary(match[1]);
    }
});

// add slot
document.addEventListener('click', function(e){
    const btn = e.target.closest('.add-slot');
    if(!btn) return;

    const day = btn.getAttribute('data-day');
    const container = document.querySelector(`.slot-container[data-day="${day}"]`);
    const k = container.querySelectorAll('.slot-row').length;

    const html = `
        <div class="slot-row mb-2">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small text-muted">Start</label>
                    <input type="time" name="hours[${day}][slots][${k}][start]" class="form-control">
                </div>
                <div class="col-md-5">
                    <label class="form-label small text-muted">Finish</label>
                    <input type="time" name="hours[${day}][slots][${k}][end]" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger w-100 remove-slot">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
});

// remove slot
document.addEventListener('click', function(e){
    const btn = e.target.closest('.remove-slot');
    if(!btn) return;

    const row = btn.closest('.slot-row');
    const container = row.closest('.slot-container');
    const day = container.getAttribute('data-day');

    row.remove();
    updateDaySummary(day);
});
</script>

@endsection