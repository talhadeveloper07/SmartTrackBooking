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
                                    <option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>
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
                            <input type="text" class="form-control" name="new_customer_name" value="{{ old('new_customer_name') }}">
                            @error('new_customer_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Customer Email</label>
                            <input type="email" class="form-control" name="new_customer_email" value="{{ old('new_customer_email') }}">
                            @error('new_customer_email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Customer Phone</label>
                            <input type="text" class="form-control" name="new_customer_phone" value="{{ old('new_customer_phone') }}">
                            @error('new_customer_phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- CARD 2+: SERVICE ITEMS (MULTI) --}}
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Services</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addServiceBtn">
                            + Add Service
                        </button>
                    </div>

                    <div class="card-body">
                        <div id="serviceItemsWrapper"></div>

                        @error('items') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            {{-- NOTES + SUBMIT --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Notes</h5>
                    </div>
                    <div class="card-body">

                        <div class="row g-3">
                            <div class="col-12">
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

    // Services list for initial dropdown render
    const services = @json($services->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values());

    const wrapper = document.getElementById('serviceItemsWrapper');
    const addBtn  = document.getElementById('addServiceBtn');

    // ===== Helpers =====
    function uid() {
        return Math.random().toString(16).slice(2) + Date.now().toString(16);
    }

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
            if (extra) Object.keys(extra).forEach(k => opt.dataset[k] = extra[k]);
            selectEl.appendChild(opt);
        });
    }

    async function loadServiceDetails(serviceId) {
        const url = `/${businessSlug}/admin/appointments/service/${serviceId}`;
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed to load service details');
        return res.json();
    }

    async function loadSlots({ serviceId, employeeId, date, duration }) {
        const url = new URL(`/${businessSlug}/admin/appointments/available-slots`, window.location.origin);
        url.searchParams.set('service_id', serviceId);
        url.searchParams.set('employee_id', employeeId);
        url.searchParams.set('appointment_date', date);
        url.searchParams.set('duration_minutes', duration);

        const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed to load slots');
        return res.json();
    }

    // ===== Item template =====
    function createServiceItem(index, oldItem = null) {
        const blockId = uid();

        const div = document.createElement('div');
        div.className = 'border rounded p-3 mb-3';
        div.dataset.index = index;
        div.dataset.blockId = blockId;

        div.innerHTML = `
            <div class="d-flex align-items-center justify-content-between mb-2">
                <strong>Service #${index + 1}</strong>
                <button type="button" class="btn btn-sm btn-outline-danger js-remove-item">
                    Remove
                </button>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Select Service</label>
                    <select name="items[${index}][service_id]" class="form-select js-service" required>
                        <option value="">-- Select --</option>
                        ${services.map(s => `<option value="${s.id}">${escapeHtml(s.name)}</option>`).join('')}
                    </select>
                    <small class="text-danger d-block js-error-service"></small>
                </div>

                <div class="col-12">
                    <label class="form-label">Service Duration</label>
                    <select name="items[${index}][duration_minutes]" class="form-select js-duration" required>
                        <option value="">-- Select service first --</option>
                    </select>
                    <small class="text-danger d-block js-error-duration"></small>
                </div>

                <div class="col-12">
                    <label class="form-label">Employee</label>
                    <select name="items[${index}][employee_id]" class="form-select js-employee" required>
                        <option value="">-- Select service first --</option>
                    </select>
                    <small class="text-danger d-block js-error-employee"></small>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Date</label>
                    <input type="date" name="items[${index}][appointment_date]" class="form-control js-date" required>
                    <small class="text-danger d-block js-error-date"></small>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Time Slot</label>
                    <select name="items[${index}][start_time]" class="form-select js-slot" required>
                        <option value="">-- Select Slot --</option>
                    </select>
                    <small class="text-danger d-block js-error-slot"></small>
                </div>
                <div class="col-12 col-md-12">
                    <label class="form-label">Location</label>
                        <input name="items[${index}][location]" class="form-control location">
                    <small class="text-danger d-block js-error-location"></small>
                </div>

                <input type="hidden" name="items[${index}][price]" class="js-price" value="">
            </div>
        `;

        // Apply old values if provided (optional)
        if (oldItem) {
            const serviceEl  = div.querySelector('.js-service');
            const dateEl     = div.querySelector('.js-date');
            serviceEl.value = oldItem.service_id ?? '';
            dateEl.value = oldItem.appointment_date ?? '';
        }

        bindItemEvents(div);

        return div;
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function bindItemEvents(itemEl) {
        const serviceEl  = itemEl.querySelector('.js-service');
        const durationEl = itemEl.querySelector('.js-duration');
        const employeeEl = itemEl.querySelector('.js-employee');
        const dateEl     = itemEl.querySelector('.js-date');
        const slotEl     = itemEl.querySelector('.js-slot');
        const priceEl    = itemEl.querySelector('.js-price');

        // Remove item
        itemEl.querySelector('.js-remove-item').addEventListener('click', () => {
            itemEl.remove();
            reindexItems();
        });

        // Service changed => load durations/employees
        serviceEl.addEventListener('change', async () => {
            const serviceId = serviceEl.value;

            resetSelect(durationEl, '-- Loading --');
            resetSelect(employeeEl, '-- Loading --');
            resetSelect(slotEl, '-- Select employee + date + duration --');
            priceEl.value = '';

            if (!serviceId) {
                resetSelect(durationEl, '-- Select service first --');
                resetSelect(employeeEl, '-- Select service first --');
                return;
            }

            try {
                const data = await loadServiceDetails(serviceId);

                fillOptions(durationEl, data.durations || [], (d) => ({
                    value: d.duration_minutes,
                    label: `${d.duration_minutes} min${(d.price !== null ? ' - $' + Number(d.price).toFixed(2) : '')}`,
                    extra: { price: d.price ?? '' }
                }), '-- Select --');

                fillOptions(employeeEl, data.employees || [], (e) => ({
                    value: e.id,
                    label: e.name
                }), '-- Select --');

            } catch (e) {
                console.error(e);
                resetSelect(durationEl, '-- Error --');
                resetSelect(employeeEl, '-- Error --');
            }
        });

        // Duration changed => set price + reload slots
        durationEl.addEventListener('change', async () => {
            const selected = durationEl.options[durationEl.selectedIndex];
            const p = selected?.dataset?.price;
            priceEl.value = (p !== undefined && p !== '') ? p : '';
            await tryLoadSlots(itemEl);
        });

        employeeEl.addEventListener('change', () => tryLoadSlots(itemEl));
        dateEl.addEventListener('change', () => tryLoadSlots(itemEl));
        dateEl.addEventListener('input', () => tryLoadSlots(itemEl));
        dateEl.addEventListener('blur', () => tryLoadSlots(itemEl));
    }

    async function tryLoadSlots(itemEl) {
        const serviceEl  = itemEl.querySelector('.js-service');
        const durationEl = itemEl.querySelector('.js-duration');
        const employeeEl = itemEl.querySelector('.js-employee');
        const dateEl     = itemEl.querySelector('.js-date');
        const slotEl     = itemEl.querySelector('.js-slot');

        const serviceId  = serviceEl.value;
        const employeeId = employeeEl.value;
        const date       = dateEl.value;
        const duration   = durationEl.value;

        if (!serviceId || !employeeId || !date || !duration) {
            resetSelect(slotEl, '-- Select employee + date + duration --');
            return;
        }

        resetSelect(slotEl, '-- Loading slots --');

        try {
            const data = await loadSlots({ serviceId, employeeId, date, duration });
            fillOptions(slotEl, data.slots || [], (t) => ({ value: t, label: t }), '-- Select --');
        } catch (e) {
            console.error(e);
            resetSelect(slotEl, '-- Error loading slots --');
        }
    }

    // Re-index names after remove
    function reindexItems() {
        const items = [...wrapper.querySelectorAll('[data-index]')];

        items.forEach((el, newIndex) => {
            el.dataset.index = newIndex;
            el.querySelector('strong').textContent = `Service #${newIndex + 1}`;

            // Update all field names
            el.querySelectorAll('select, input').forEach(input => {
                const name = input.getAttribute('name');
                if (!name) return;
                input.setAttribute('name', name.replace(/items\[\d+\]/, `items[${newIndex}]`));
            });
        });
    }

    // ===== Add item =====
    function addServiceItem(oldItem = null) {
        const index = wrapper.querySelectorAll('[data-index]').length;
        const item = createServiceItem(index, oldItem);
        wrapper.appendChild(item);
    }

    addBtn.addEventListener('click', () => addServiceItem());

    // Initial: 1 item by default
    // If validation failed and you want to restore old('items'), you can enhance here later.
    addServiceItem();

})();
</script>
@endsection