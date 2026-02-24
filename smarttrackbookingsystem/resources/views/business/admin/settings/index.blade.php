@extends('business.layouts.app')

@section('title', 'Business Settings')

@section('business_content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mt-4">Settings</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard', $business->slug) }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Settings</li>
            </ol>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- General Settings --}}
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-cog me-2" style="color: var(--primary-color);"></i> General Settings
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('business.settings.general', $business->slug) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="business_name" class="form-label">Business Name</label>
                        <input type="text" class="form-control @error('business_name') is-invalid @enderror" 
                               id="business_name" name="business_name" 
                               value="{{ old('business_name', $business->name) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="business_email" class="form-label">Business Email</label>
                        <input type="email" class="form-control @error('business_email') is-invalid @enderror" 
                               id="business_email" name="business_email" 
                               value="{{ old('business_email', $business->email) }}">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="business_phone" class="form-label">Business Phone</label>
                        <input type="text" class="form-control @error('business_phone') is-invalid @enderror" 
                               id="business_phone" name="business_phone" 
                               value="{{ old('business_phone', $business->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="timezone" class="form-label">Timezone</label>
                        <select class="form-select" id="timezone" name="timezone">
                            @foreach(timezone_identifiers_list() as $timezone)
                                <option value="{{ $timezone }}" {{ ($settings['timezone'] ?? 'UTC') == $timezone ? 'selected' : '' }}>
                                    {{ $timezone }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="business_address" class="form-label">Business Address</label>
                    <textarea class="form-control" id="business_address" name="business_address" rows="3">{{ old('business_address', $business->address) }}</textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="date_format" class="form-label">Date Format</label>
                        <select class="form-select" id="date_format" name="date_format">
                            <option value="Y-m-d" {{ ($settings['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                            <option value="d/m/Y" {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="time_format" class="form-label">Time Format</label>
                        <select class="form-select" id="time_format" name="time_format">
                            <option value="H:i" {{ ($settings['time_format'] ?? 'H:i') == 'H:i' ? 'selected' : '' }}>24 Hour</option>
                            <option value="h:i A" {{ ($settings['time_format'] ?? '') == 'h:i A' ? 'selected' : '' }}>12 Hour</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="currency" class="form-label">Currency</label>
                        <select class="form-select" id="currency" name="currency">
                            <option value="USD" {{ ($settings['currency'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="EUR" {{ ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            <option value="GBP" {{ ($settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Save General Settings</button>
            </form>
        </div>
    </div>

    {{-- Appearance Settings --}}
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-palette me-2" style="color: var(--primary-color);"></i> Appearance Settings
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('business.settings.appearance', $business->slug) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <h6 class="mb-3">Color Scheme</h6>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Primary Color</label>
                        <div class="d-flex">
                            <input type="color" class="form-control form-control-color me-2" 
                                   id="primary_color_picker" 
                                   value="{{ $settings['primary_color'] ?? '#110093' }}" 
                                   style="width: 60px; height: 38px;">
                            <input type="text" class="form-control" 
                                   id="primary_color" name="primary_color" 
                                   value="{{ old('primary_color', $settings['primary_color'] ?? '#110093') }}">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Secondary Color</label>
                        <div class="d-flex">
                            <input type="color" class="form-control form-control-color me-2" 
                                   id="secondary_color_picker" 
                                   value="{{ $settings['secondary_color'] ?? '#38c172' }}" 
                                   style="width: 60px; height: 38px;">
                            <input type="text" class="form-control" 
                                   id="secondary_color" name="secondary_color" 
                                   value="{{ old('secondary_color', $settings['secondary_color'] ?? '#38c172') }}">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Accent Color</label>
                        <div class="d-flex">
                            <input type="color" class="form-control form-control-color me-2" 
                                   id="accent_color_picker" 
                                   value="{{ $settings['accent_color'] ?? '#f6993f' }}" 
                                   style="width: 60px; height: 38px;">
                            <input type="text" class="form-control" 
                                   id="accent_color" name="accent_color" 
                                   value="{{ old('accent_color', $settings['accent_color'] ?? '#f6993f') }}">
                        </div>
                    </div>
                </div>
                
                <h6 class="mb-3">Logo & Favicon</h6>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Business Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        
                        @if(isset($settings['logo_path']) && $settings['logo_path'])
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$settings['logo_path']) }}" alt="Logo" class="img-thumbnail" style="max-height: 100px;">
                                <button type="button" class="btn btn-sm btn-danger mt-2 remove-logo" data-type="logo">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Favicon</label>
                        <input type="file" class="form-control" id="favicon" name="favicon" accept=".ico,.png">
                        
                        @if(isset($settings['favicon_path']) && $settings['favicon_path'])
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$settings['favicon_path']) }}" alt="Favicon" class="img-thumbnail" style="max-height: 32px;">
                                <button type="button" class="btn btn-sm btn-danger mt-2 remove-logo" data-type="favicon">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Appearance Settings</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Sync color pickers with text inputs
    $('#primary_color_picker, #secondary_color_picker, #accent_color_picker').on('input', function() {
        $('#' + this.id.replace('_picker', '')).val(this.value);
    });
    
    $('#primary_color, #secondary_color, #accent_color').on('input', function() {
        $('#' + this.id + '_picker').val(this.value);
    });

    // Preview font selection
    $('#font_family').on('change', function() {
        const fontFamily = $(this).val();
        // Apply temporarily to show preview
        $('body').css('font-family', fontFamily);
    });

    // Handle form submission
    $('form').on('submit', function() {
        const form = $(this);
        if (form.attr('action').includes('appearance')) {
            setTimeout(function() {
                if (typeof window.refreshSettings === 'function') {
                    window.refreshSettings();
                }
                localStorage.setItem('settings_updated', Date.now());
            }, 500);
        }
    });

    // Handle logo removal
    $('.remove-logo').click(function() {
        const type = $(this).data('type');
        const button = $(this);
        
        if (confirm('Remove ' + type + '?')) {
            $.ajax({
                url: '{{ route("business.settings.remove-logo", $business->slug) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    type: type
                },
                success: function() {
                    button.closest('div').fadeOut();
                    localStorage.setItem('settings_updated', Date.now());
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            });
        }
    });
});
</script>
@endpush