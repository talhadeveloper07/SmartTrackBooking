@extends('business.layouts.app')

@section('business_content')
<div class="container">

    <div class="d-flex align-items-center mb-3">
        <h3 class="me-auto">Add Employee — {{ ucwords($business->name) }}</h3>
        <a href="{{ route('business.employees', $business->slug) }}" class="btn btn-light">Back</a>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('business.employees.store', $business->slug) }}" method="POST">
        @csrf

        {{-- ================= BASIC INFO ================= --}}
        <div class="card mb-4">
            <div class="card-header"><strong>Employee Details</strong></div>
            <div class="card-body">
                <div class="row">
                    
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone') }}">
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Joining Date *</label>
                        <input type="date" name="joining_date" class="form-control"
                               value="{{ old('joining_date') }}" required>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control"
                               value="{{ old('date_of_birth') }}">
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="default-select form-control wide">
                            <option value="active" {{ old('status','active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3 col-md-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>

                </div>
            </div>
        </div>

      {{-- ================= SERVICES ================= --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <strong>Offered Services</strong>

        <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
            <input type="checkbox" id="selectAllServices" class="form-check-input m-0">
            <span class="fw-semibold">Select All</span>
        </label>
    </div>

    <div class="card-body">
        @php $oldServices = old('services', []); @endphp

        <div class="service-list">
            @forelse($services as $srv)
                @php $checked = in_array($srv->id, $oldServices); @endphp

                <div class="service-item">
                    {{-- hidden checkbox --}}
                    <input
                        type="checkbox"
                        class="service-check"
                        name="services[]"
                        id="srv_{{ $srv->id }}"
                        value="{{ $srv->id }}"
                        {{ $checked ? 'checked' : '' }}
                    >

                    {{-- clickable UI --}}
                    <label for="srv_{{ $srv->id }}" class="service-pill">
                        <span class="service-left">
                            <span class="service-tick">
                                <i class="fa fa-check"></i>
                            </span>
                            <span class="service-icon"></span>
                        </span>

                        <span class="service-name">{{ ucwords($srv->name) }}</span>
                    </label>
                </div>
            @empty
                <div class="text-muted">No services found. Please add services first.</div>
            @endforelse
        </div>
    </div>
</div>

        {{-- ================= WORKING HOURS (Enabled/Disabled toggle) ================= --}}
        @php
            // Order like your screenshot
            $days = [
                1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday',
                5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'
            ];

            $oldHours = old('hours', []);

            // default ON days (Mon-Sat on, Sun off) — change if you want
            $defaultEnabled = [1,2,3,4,5,6]; // Sunday (0) off
        @endphp

        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <strong>Agent Schedule</strong>
            </div>

            <div class="card-body p-0">

                @foreach($days as $dayIndex => $dayName)

                    @php
                        // old payload for day
                        $dayOld = $oldHours[$dayIndex] ?? null;

                        // enabled logic:
                        // 1) if old exists: enabled if is_enabled present
                        // 2) else use defaultEnabled list
                        $isEnabled = $dayOld
                            ? !empty($dayOld['is_enabled'])
                            : in_array($dayIndex, $defaultEnabled);

                        // slots
                        $slots = $dayOld['slots'] ?? [
                            ['start' => '10:30', 'end' => '17:00']
                        ];
                    @endphp

                    <div class="schedule-day-row border-bottom">

                        {{-- top row --}}
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
                                <div class="day-summary text-muted small" id="summary-{{ $dayIndex }}"></div>

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
                                <div class="schedule-editor rounded-3 border p-3 {{ !$isEnabled ? 'd-none' : '' }}"
                                     id="editor-{{ $dayIndex }}">

                                    <div class="slot-container" data-day="{{ $dayIndex }}">

                                        @foreach($slots as $k => $slot)
                                            @php
                                                $start = $slot['start'] ?? '10:30'; // 24h
                                                $end   = $slot['end'] ?? '17:00';
                                            @endphp

                                            <div class="slot-row py-2 border-bottom">
                                                <div class="row g-2 align-items-end">

                                                    <div class="col-md-6">
                                                        <div class="small text-muted mb-1">Start</div>

                                                        <div class="d-flex align-items-center gap-2">
                                                            <input type="text"
                                                                   class="form-control time-text time-start"
                                                                   placeholder="10:30"
                                                                   style="max-width:110px;">

                                                            <div class="ampm-toggle" data-target="start">
                                                                <button type="button" class="btn btn-sm ampm-btn">am</button>
                                                                <button type="button" class="btn btn-sm ampm-btn">pm</button>
                                                            </div>
                                                        </div>

                                                        <input type="hidden"
                                                               class="hidden-start"
                                                               name="hours[{{ $dayIndex }}][slots][{{ $k }}][start]"
                                                               value="{{ $start }}">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="small text-muted mb-1">Finish</div>

                                                        <div class="d-flex align-items-center gap-2">
                                                            <input type="text"
                                                                   class="form-control time-text time-end"
                                                                   placeholder="05:00"
                                                                   style="max-width:110px;">

                                                            <div class="ampm-toggle" data-target="end">
                                                                <button type="button" class="btn btn-sm ampm-btn">am</button>
                                                                <button type="button" class="btn btn-sm ampm-btn">pm</button>
                                                            </div>

                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-slot ms-auto">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </div>

                                                        <input type="hidden"
                                                               class="hidden-end"
                                                               name="hours[{{ $dayIndex }}][slots][{{ $k }}][end]"
                                                               value="{{ $end }}">
                                                    </div>

                                                </div>
                                            </div>
                                        @endforeach

                                    </div>

                                    <button type="button"
                                            class="btn btn-outline-primary w-100 mt-3 add-slot"
                                            data-day="{{ $dayIndex }}"
                                            data-dayname="{{ $dayName }}">
                                        <i class="fa fa-plus me-2"></i> Add another work period for {{ $dayName }}
                                    </button>

                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Employee</button>
        <a href="{{ route('business.employees', $business->slug) }}" class="btn btn-light">Cancel</a>

    </form>

</div>

<script>
function pad2(n){ return String(n).padStart(2,'0'); }

function to12h(hhmm){
    if(!hhmm) return {time:'', ampm:'am'};
    let [h,m] = hhmm.split(':').map(Number);
    let ampm = h >= 12 ? 'pm' : 'am';
    let h12 = h % 12; if(h12 === 0) h12 = 12;
    return { time: `${pad2(h12)}:${pad2(m)}`, ampm };
}

function to24h(time12, ampm){
    if(!time12) return '';
    let [h,m] = time12.split(':').map(Number);
    if(Number.isNaN(h) || Number.isNaN(m)) return '';
    if(ampm === 'pm' && h !== 12) h += 12;
    if(ampm === 'am' && h === 12) h = 0;
    return `${pad2(h)}:${pad2(m)}`;
}

function prettyRange(start24, end24){
    if(!start24 || !end24) return '';
    const s = to12h(start24); const e = to12h(end24);
    return `${s.time}${s.ampm}–${e.time}${e.ampm}`;
}

function initSlotRow(slotRow){
    const startHidden = slotRow.querySelector('.hidden-start');
    const endHidden   = slotRow.querySelector('.hidden-end');
    const startTxt = slotRow.querySelector('.time-start');
    const endTxt   = slotRow.querySelector('.time-end');

    const s = to12h(startHidden.value);
    const e = to12h(endHidden.value);

    startTxt.value = s.time;
    endTxt.value = e.time;

    const startBtns = slotRow.querySelectorAll('.ampm-toggle[data-target="start"] .ampm-btn');
    const endBtns   = slotRow.querySelectorAll('.ampm-toggle[data-target="end"] .ampm-btn');

    startBtns.forEach(b => b.classList.toggle('active', b.textContent.trim() === s.ampm));
    endBtns.forEach(b => b.classList.toggle('active', b.textContent.trim() === e.ampm));
}

function updateDaySummary(day){
    const container = document.querySelector(`.slot-container[data-day="${day}"]`);
    const summaryEl = document.getElementById(`summary-${day}`);
    if(!container || !summaryEl) return;

    const start = container.querySelector('.hidden-start')?.value || '';
    const end   = container.querySelector('.hidden-end')?.value || '';

    const slots = container.querySelectorAll('.slot-row').length;

    let text = prettyRange(start, end);
    if(slots > 1 && text) text += ` (+${slots-1})`;

    summaryEl.textContent = text;
}

// init
document.querySelectorAll('.slot-row').forEach(initSlotRow);
[0,1,2,3,4,5,6].forEach(d => updateDaySummary(d));

/** Toggle enabled/disabled day **/
document.addEventListener('change', function(e){
    if(!e.target.classList.contains('day-toggle')) return;

    const day = e.target.getAttribute('data-day');
    const editor = document.getElementById(`editor-${day}`);
    const dayName = e.target.closest('.schedule-day-row').querySelector('.day-name');
    const summaryEl = document.getElementById(`summary-${day}`);

    const isEnabled = e.target.checked; // ✅ checked = ON

    if(editor) editor.classList.toggle('d-none', !isEnabled);
    if(dayName) dayName.classList.toggle('day-off', !isEnabled);

    if(!isEnabled){
        if(summaryEl) summaryEl.textContent = '';
    }else{
        updateDaySummary(day);
    }
});

/** AM/PM buttons **/
document.addEventListener('click', function(e){
    if(!e.target.classList.contains('ampm-btn')) return;

    const wrap = e.target.closest('.ampm-toggle');
    const slotRow = e.target.closest('.slot-row');
    wrap.querySelectorAll('.ampm-btn').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');

    const target = wrap.getAttribute('data-target');
    const timeInput = slotRow.querySelector(target === 'start' ? '.time-start' : '.time-end');
    const hidden    = slotRow.querySelector(target === 'start' ? '.hidden-start' : '.hidden-end');

    const ampm = e.target.textContent.trim();
    hidden.value = to24h(timeInput.value, ampm);

    const day = slotRow.closest('.slot-container').getAttribute('data-day');
    updateDaySummary(day);
});

/** typing time **/
document.addEventListener('input', function(e){
    if(!e.target.classList.contains('time-text')) return;

    const slotRow = e.target.closest('.slot-row');
    const isStart = e.target.classList.contains('time-start');

    const ampmBtn = slotRow.querySelector(
        isStart
            ? '.ampm-toggle[data-target="start"] .ampm-btn.active'
            : '.ampm-toggle[data-target="end"] .ampm-btn.active'
    );
    const ampm = ampmBtn ? ampmBtn.textContent.trim() : 'am';

    const hidden = slotRow.querySelector(isStart ? '.hidden-start' : '.hidden-end');
    hidden.value = to24h(e.target.value, ampm);

    const day = slotRow.closest('.slot-container').getAttribute('data-day');
    updateDaySummary(day);
});

/** Add slot **/
document.addEventListener('click', function(e){
    const btn = e.target.closest('.add-slot');
    if(!btn) return;

    const day = btn.getAttribute('data-day');
    const container = document.querySelector(`.slot-container[data-day="${day}"]`);
    const k = container.querySelectorAll('.slot-row').length;

    const html = `
        <div class="slot-row py-2 border-bottom">
            <div class="row g-2 align-items-end">

                <div class="col-md-6">
                    <div class="small text-muted mb-1">Start</div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control time-text time-start" placeholder="10:30" style="max-width:110px;">
                        <div class="ampm-toggle" data-target="start">
                            <button type="button" class="btn btn-sm ampm-btn active">am</button>
                            <button type="button" class="btn btn-sm ampm-btn">pm</button>
                        </div>
                    </div>
                    <input type="hidden" class="hidden-start" name="hours[${day}][slots][${k}][start]" value="10:30">
                </div>

                <div class="col-md-6">
                    <div class="small text-muted mb-1">Finish</div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control time-text time-end" placeholder="05:00" style="max-width:110px;">
                        <div class="ampm-toggle" data-target="end">
                            <button type="button" class="btn btn-sm ampm-btn">am</button>
                            <button type="button" class="btn btn-sm ampm-btn active">pm</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-slot ms-auto">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <input type="hidden" class="hidden-end" name="hours[${day}][slots][${k}][end]" value="17:00">
                </div>

            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);

    const newRow = container.querySelectorAll('.slot-row')[k];
    initSlotRow(newRow);
    updateDaySummary(day);
});

/** Remove slot **/
document.addEventListener('click', function(e){
    const btn = e.target.closest('.remove-slot');
    if(!btn) return;

    const row = btn.closest('.slot-row');
    const day = row.closest('.slot-container').getAttribute('data-day');

    row.remove();
    updateDaySummary(day);
});
</script>

<script>
(function(){
    const selectAll = document.getElementById('selectAllServices');
    const checks = () => Array.from(document.querySelectorAll('.service-check'));

    function syncSelectAll(){
        const list = checks();
        if(!list.length) return;
        selectAll.checked = list.every(c => c.checked);
    }

    // initial sync
    syncSelectAll();

    // clicking Select All
    selectAll?.addEventListener('change', function(){
        checks().forEach(c => c.checked = this.checked);
    });

    // when any single checkbox changes
    document.addEventListener('change', function(e){
        if(e.target.classList.contains('service-check')){
            syncSelectAll();
        }
    });
})();
</script>

@endsection