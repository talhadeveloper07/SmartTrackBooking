@extends('business.layouts.app')

@section('business_content')
    <div class="container-fluid">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0">New Appointment</h4>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('business.appointments.store', $business->slug) }}" id="appointmentForm">
            @csrf

            <div class="row g-4">

                {{-- CARD 1: CUSTOMER --}}
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Customer</h5>
                        </div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label">Select Customer</label>
                                <select name="customer_id" id="customer_id" class="form-select">
                                    <option value="">-- Select --</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">
                                            {{ $c->user->name ?? $c->name ?? 'No Name' }}
                                            ({{ $c->user->email ?? $c->email ?? 'No Email' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id') <small class="text-danger">{{ $message }}</small> @enderror
                                <small class="text-muted">Or add new customer below.</small>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" class="form-control" name="new_customer_name"
                                    value="{{ old('new_customer_name') }}">
                                @error('new_customer_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Customer Email</label>
                                <input type="email" class="form-control" name="new_customer_email"
                                    value="{{ old('new_customer_email') }}">
                                @error('new_customer_email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Customer Phone</label>
                                <input type="text" class="form-control" name="new_customer_phone"
                                    value="{{ old('new_customer_phone') }}">
                                @error('new_customer_phone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- CARD 2: SERVICE --}}
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Service</h5>
                        </div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label">Select Service</label>
                                <select name="service_id" id="service_id" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    @foreach($services as $s)
                                        <option value="{{ $s->id }}" @selected(old('service_id') == $s->id)>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Service Duration</label>
                                <select name="duration_minutes" id="duration_minutes" class="form-select" required>
                                    <option value="">-- Select service first --</option>
                                </select>
                                @error('duration_minutes') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Employee</label>
                                <select name="employee_id" id="employee_id" class="form-select" required>
                                    <option value="">-- Select service first --</option>
                                </select>
                                @error('employee_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <input type="hidden" name="price" id="price" value="{{ old('price') }}">

                        </div>
                    </div>
                </div>

                {{-- CARD 3: DATE/TIME --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Date & Time</h5>
                        </div>
                        <div class="card-body">

                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="appointment_date" id="appointment_date" class="form-control"
                                        value="{{ old('appointment_date') }}" required>
                                    @error('appointment_date') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label">Time Slot</label>
                                    <select name="start_time" id="start_time" class="form-select" required>
                                        <option value="">-- Select Slot --</option>
                                    </select>
                                    @error('start_time') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label">Notes (optional)</label>
                                    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}">
                                    @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    Booking Done
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>

    <script>
        (function () {
            const businessSlug = @json($business->slug);

            const serviceSelect = document.getElementById('service_id');
            const durationSelect = document.getElementById('duration_minutes');
            const employeeSelect = document.getElementById('employee_id');
            const dateInput = document.getElementById('appointment_date');
            const slotSelect = document.getElementById('start_time');
            const priceInput = document.getElementById('price');

            function resetSelect(selectEl, placeholder) {
                selectEl.innerHTML = '';
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = placeholder;
                selectEl.appendChild(opt);
            }

            function fillOptions(selectEl, items, mapFn, placeholder) {
                resetSelect(selectEl, placeholder);
                items.forEach(item => {
                    const { value, label, extra } = mapFn(item);
                    const opt = document.createElement('option');
                    opt.value = value;
                    opt.textContent = label;
                    if (extra) {
                        Object.keys(extra).forEach(k => opt.dataset[k] = extra[k]);
                    }
                    selectEl.appendChild(opt);
                });
            }

            async function loadServiceDetails(serviceId) {
                resetSelect(durationSelect, '-- Loading --');
                resetSelect(employeeSelect, '-- Loading --');
                resetSelect(slotSelect, '-- Select employee + date + duration --');

                const url = `/${businessSlug}/admin/appointments/service/${serviceId}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Failed to load service details');
                return res.json();
            }

            async function loadSlots() {
                const serviceId = serviceSelect.value;
                const employeeId = employeeSelect.value;
                const date = dateInput.value;
                const duration = durationSelect.value;

                if (!serviceId || !employeeId || !date || !duration) {
                    resetSelect(slotSelect, '-- Select employee + date + duration --');
                    return;
                }

                resetSelect(slotSelect, '-- Loading slots --');

                const url = new URL(`/${businessSlug}/admin/appointments/available-slots`, window.location.origin);
                url.searchParams.set('service_id', serviceId);
                url.searchParams.set('employee_id', employeeId);
                url.searchParams.set('appointment_date', date);
                url.searchParams.set('duration_minutes', duration);

                const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Failed to load slots');
                const data = await res.json();

                fillOptions(slotSelect, data.slots || [], (t) => ({ value: t, label: t }), '-- Select --');
            }

            serviceSelect.addEventListener('change', async function () {
                const serviceId = this.value;
                if (!serviceId) {
                    resetSelect(durationSelect, '-- Select service first --');
                    resetSelect(employeeSelect, '-- Select service first --');
                    resetSelect(slotSelect, '-- Select employee + date + duration --');
                    return;
                }

                try {
                    const data = await loadServiceDetails(serviceId);

                    // durations
                    fillOptions(durationSelect, data.durations || [], (d) => ({
                        value: d.duration_minutes,
                        label: `${d.duration_minutes} min${(d.price !== null ? ' - $' + Number(d.price).toFixed(2) : '')}`,
                        extra: { price: d.price ?? '' }
                    }), '-- Select --');

                    // employees
                    fillOptions(employeeSelect, data.employees || [], (e) => ({
                        value: e.id,
                        label: e.name
                    }), '-- Select --');

                } catch (e) {
                    console.error(e);
                    resetSelect(durationSelect, '-- Error --');
                    resetSelect(employeeSelect, '-- Error --');
                }
            });

            durationSelect.addEventListener('change', function () {
                const selected = durationSelect.options[durationSelect.selectedIndex];
                const p = selected?.dataset?.price;
                priceInput.value = (p !== undefined && p !== '') ? p : '';
                loadSlots();
            });

            employeeSelect.addEventListener('change', loadSlots);
            dateInput.addEventListener('change', loadSlots);
            dateInput.addEventListener('input', loadSlots);
dateInput.addEventListener('blur', loadSlots);

        })();
    </script>
@endsection