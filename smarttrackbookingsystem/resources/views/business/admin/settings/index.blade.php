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

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <!-- Settings Navigation -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Settings Menu</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#general" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-cog me-2"></i> General
                    </a>
                    <a href="#appearance" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-palette me-2"></i> Appearance
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-bell me-2"></i> Notifications
                    </a>
                    <a href="#invoice" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-file-invoice me-2"></i> Invoice
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-shield-alt me-2"></i> Security
                    </a>
                    <a href="#email" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-envelope me-2"></i> Email
                    </a>
                    <a href="#localization" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-globe me-2"></i> Localization
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">General</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="appearance-tab" data-bs-toggle="tab" data-bs-target="#appearance" type="button" role="tab">Appearance</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">Notifications</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice" type="button" role="tab">Invoice</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">Security</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">Email</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="localization-tab" data-bs-toggle="tab" data-bs-target="#localization" type="button" role="tab">Localization</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="settingsTabContent">
                        {{-- General Settings Tab --}}
                        @include('business.admin.settings.tabs.general', ['business' => $business, 'settings' => $settings])
                        
                        {{-- Appearance Settings Tab --}}
                        @include('business.admin.settings.tabs.appearance', ['business' => $business, 'settings' => $settings])
                        
                        {{-- Notifications Settings Tab --}}
                        @include('business.admin.settings.tabs.notifications', ['business' => $business, 'settings' => $settings])
                        
                        {{-- Invoice Settings Tab --}}
                        @include('business.admin.settings.tabs.invoice', ['business' => $business, 'settings' => $settings])
                        
                        {{-- Security Settings Tab --}}
                        @include('business.admin.settings.tabs.security', ['business' => $business, 'settings' => $settings])
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/css/bootstrap-colorpicker.min.css">
<style>
    .color-preview {
        width: 40px;
        height: 38px;
        border-radius: 0 4px 4px 0;
        border: 1px solid #ddd;
        cursor: pointer;
    }
    .logo-preview {
        max-width: 200px;
        max-height: 100px;
        margin-top: 10px;
        object-fit: contain;
    }
    .favicon-preview {
        width: 32px;
        height: 32px;
        margin-top: 10px;
    }
    .input-group .form-control {
        border-right: none;
    }
    .input-group .form-control:focus {
        border-right: none;
        box-shadow: none;
    }
    .colorpicker-component {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/js/bootstrap-colorpicker.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize color pickers with dynamic updates
        function initColorPicker(selector, defaultColor) {
            $(selector).colorpicker({
                format: 'hex',
                color: defaultColor,
                container: true,
                inline: false
            }).on('colorpickerChange', function(event) {
                // Update the input value
                $(this).find('input').val(event.color.toString());
                // Update the preview span background
                $(this).find('.color-preview').css('background-color', event.color.toString());
            }).on('colorpickerCreate', function() {
                // Initial setup
                const input = $(this).find('input');
                const preview = $(this).find('.color-preview');
                preview.css('background-color', input.val());
            });
        }

        // Initialize each color picker with its current value
        @if(isset($settings['primary_color']))
            initColorPicker('#primary_color_picker', '{{ $settings['primary_color'] }}');
        @endif
        
        @if(isset($settings['secondary_color']))
            initColorPicker('#secondary_color_picker', '{{ $settings['secondary_color'] }}');
        @endif
        
        @if(isset($settings['accent_color']))
            initColorPicker('#accent_color_picker', '{{ $settings['accent_color'] }}');
        @endif

        // Handle logo removal with better error handling
        $('.remove-logo').click(function() {
            const type = $(this).data('type');
            const button = $(this);
            
            if (confirm('Are you sure you want to remove the ' + type + '?')) {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Removing...');
                
                $.ajax({
                    url: '{{ route("business.settings.remove-logo", $business->slug) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type: type
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`.${type}-preview-container`).fadeOut(300, function() {
                                $(this).remove();
                            });
                            
                            // Show success message
                            if (typeof toastr !== 'undefined') {
                                toastr.success(type.charAt(0).toUpperCase() + type.slice(1) + ' removed successfully');
                            } else {
                                alert(type.charAt(0).toUpperCase() + type.slice(1) + ' removed successfully');
                            }
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Error removing ' + type;
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);
                    },
                    complete: function() {
                        button.prop('disabled', false).html('<i class="fas fa-trash"></i> Remove ' + type.charAt(0).toUpperCase() + type.slice(1));
                    }
                });
            }
        });

        // Preview uploaded image with better handling
        $('input[type="file"]').change(function(e) {
            const file = this.files[0];
            if (!file) return;
            
            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                $(this).val('');
                return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/x-icon'];
            if (!validTypes.includes(file.type) && !file.name.match(/\.(ico)$/i)) {
                alert('Invalid file type. Please upload an image file.');
                $(this).val('');
                return;
            }
            
            const reader = new FileReader();
            const previewId = $(this).data('preview');
            const previewContainer = $(previewId).closest('.logo-preview-container, .favicon-preview-container');
            
            reader.onload = function(e) {
                $(previewId).attr('src', e.target.result).show();
                previewContainer.removeClass('d-none');
            }
            
            reader.readAsDataURL(file);
        });

        // Handle tab navigation from sidebar links
        $('.list-group-item[data-bs-toggle="tab"]').on('click', function(e) {
            e.preventDefault();
            const target = $(this).attr('href');
            
            // Activate the corresponding tab
            $(`button[data-bs-target="${target}"]`).tab('show');
            
            // Update URL hash without scrolling
            history.pushState(null, null, target);
        });

        // Check if there's a hash in URL and activate that tab
        if (window.location.hash) {
            const hash = window.location.hash;
            const tab = $(`button[data-bs-target="${hash}"]`);
            if (tab.length) {
                tab.tab('show');
            }
        }

        // Fix for colorpicker positioning
        $(document).on('colorpickerShow', function(e) {
            $(e.target).css('z-index', 9999);
        });
    });
</script>
@endpush