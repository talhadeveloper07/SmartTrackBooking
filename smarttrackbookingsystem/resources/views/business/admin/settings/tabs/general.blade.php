{{-- resources/views/business/admin/settings/tabs/general.blade.php --}}
<div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
    <form action="{{ route('business.settings.general', $business->slug) }}" method="POST">
        @csrf
        @method('PUT')
        
        <h5 class="mb-3">General Information</h5>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="business_name" class="form-label">Business Name</label>
                <input type="text" class="form-control @error('business_name') is-invalid @enderror" 
                       id="business_name" name="business_name" 
                       value="{{ old('business_name', $business->name) }}">
                @error('business_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6">
                <label for="business_email" class="form-label">Business Email</label>
                <input type="email" class="form-control @error('business_email') is-invalid @enderror" 
                       id="business_email" name="business_email" 
                       value="{{ old('business_email', $business->email) }}">
                @error('business_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="business_phone" class="form-label">Business Phone</label>
                <input type="text" class="form-control @error('business_phone') is-invalid @enderror" 
                       id="business_phone" name="business_phone" 
                       value="{{ old('business_phone', $business->phone) }}">
                @error('business_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6">
                <label for="timezone" class="form-label">Timezone</label>
                <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone">
                    @foreach(timezone_identifiers_list() as $timezone)
                        <option value="{{ $timezone }}" {{ $settings['timezone'] == $timezone ? 'selected' : '' }}>
                            {{ $timezone }}
                        </option>
                    @endforeach
                </select>
                @error('timezone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="mb-3">
            <label for="business_address" class="form-label">Business Address</label>
            <textarea class="form-control @error('business_address') is-invalid @enderror" 
                      id="business_address" name="business_address" rows="3">{{ old('business_address', $business->address) }}</textarea>
            @error('business_address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="date_format" class="form-label">Date Format</label>
                <select class="form-select @error('date_format') is-invalid @enderror" id="date_format" name="date_format">
                    <option value="Y-m-d" {{ $settings['date_format'] == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2024-01-01)</option>
                    <option value="m/d/Y" {{ $settings['date_format'] == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (01/01/2024)</option>
                    <option value="d/m/Y" {{ $settings['date_format'] == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (01/01/2024)</option>
                    <option value="d M Y" {{ $settings['date_format'] == 'd M Y' ? 'selected' : '' }}>DD Mon YYYY (01 Jan 2024)</option>
                </select>
                @error('date_format')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-4">
                <label for="time_format" class="form-label">Time Format</label>
                <select class="form-select @error('time_format') is-invalid @enderror" id="time_format" name="time_format">
                    <option value="H:i" {{ $settings['time_format'] == 'H:i' ? 'selected' : '' }}>24 Hour (14:30)</option>
                    <option value="h:i A" {{ $settings['time_format'] == 'h:i A' ? 'selected' : '' }}>12 Hour (02:30 PM)</option>
                </select>
                @error('time_format')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-4">
                <label for="currency" class="form-label">Currency</label>
                <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency">
                    <option value="USD" {{ $settings['currency'] == 'USD' ? 'selected' : '' }}>USD ($)</option>
                    <option value="EUR" {{ $settings['currency'] == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                    <option value="GBP" {{ $settings['currency'] == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                    <option value="JPY" {{ $settings['currency'] == 'JPY' ? 'selected' : '' }}>JPY (¥)</option>
                </select>
                @error('currency')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="mb-3">
            <label for="week_start" class="form-label">Week Starts On</label>
            <select class="form-select @error('week_start') is-invalid @enderror" id="week_start" name="week_start">
                <option value="monday" {{ $settings['week_start'] == 'monday' ? 'selected' : '' }}>Monday</option>
                <option value="sunday" {{ $settings['week_start'] == 'sunday' ? 'selected' : '' }}>Sunday</option>
                <option value="saturday" {{ $settings['week_start'] == 'saturday' ? 'selected' : '' }}>Saturday</option>
            </select>
            @error('week_start')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">Save General Settings</button>
    </form>
</div>