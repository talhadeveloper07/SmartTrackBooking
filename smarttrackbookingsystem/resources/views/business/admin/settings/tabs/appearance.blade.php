{{-- resources/views/business/admin/settings/tabs/appearance.blade.php --}}
<div class="tab-pane fade" id="appearance" role="tabpanel" aria-labelledby="appearance-tab">
    <form action="{{ route('business.settings.appearance', $business->slug) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <h5 class="mb-3">Color Scheme</h5>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="primary_color" class="form-label">Primary Color</label>
                <div class="input-group colorpicker-component" id="primary_color_picker">
                    <input type="text" class="form-control @error('primary_color') is-invalid @enderror" 
                           id="primary_color" name="primary_color" 
                           value="{{ old('primary_color', $settings['primary_color'] ?? '#3490dc') }}">
                    <span class="input-group-text color-preview" style="background-color: {{ $settings['primary_color'] ?? '#3490dc' }}; width: 40px; border-radius: 0 4px 4px 0;"></span>
                </div>
                @error('primary_color')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-4">
                <label for="secondary_color" class="form-label">Secondary Color</label>
                <div class="input-group colorpicker-component" id="secondary_color_picker">
                    <input type="text" class="form-control @error('secondary_color') is-invalid @enderror" 
                           id="secondary_color" name="secondary_color" 
                           value="{{ old('secondary_color', $settings['secondary_color'] ?? '#38c172') }}">
                    <span class="input-group-text color-preview" style="background-color: {{ $settings['secondary_color'] ?? '#38c172' }}; width: 40px; border-radius: 0 4px 4px 0;"></span>
                </div>
                @error('secondary_color')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-4">
                <label for="accent_color" class="form-label">Accent Color</label>
                <div class="input-group colorpicker-component" id="accent_color_picker">
                    <input type="text" class="form-control @error('accent_color') is-invalid @enderror" 
                           id="accent_color" name="accent_color" 
                           value="{{ old('accent_color', $settings['accent_color'] ?? '#f6993f') }}">
                    <span class="input-group-text color-preview" style="background-color: {{ $settings['accent_color'] ?? '#f6993f' }}; width: 40px; border-radius: 0 4px 4px 0;"></span>
                </div>
                @error('accent_color')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="mb-4">
            <label for="font_family" class="form-label">Font Family</label>
            <select class="form-select @error('font_family') is-invalid @enderror" id="font_family" name="font_family">
                <option value="Inter, sans-serif" {{ ($settings['font_family'] ?? 'Inter, sans-serif') == 'Inter, sans-serif' ? 'selected' : '' }}>Inter</option>
                <option value="Arial, sans-serif" {{ ($settings['font_family'] ?? '') == 'Arial, sans-serif' ? 'selected' : '' }}>Arial</option>
                <option value="Helvetica, sans-serif" {{ ($settings['font_family'] ?? '') == 'Helvetica, sans-serif' ? 'selected' : '' }}>Helvetica</option>
                <option value="Times New Roman, serif" {{ ($settings['font_family'] ?? '') == 'Times New Roman, serif' ? 'selected' : '' }}>Times New Roman</option>
                <option value="Georgia, serif" {{ ($settings['font_family'] ?? '') == 'Georgia, serif' ? 'selected' : '' }}>Georgia</option>
                <option value="Verdana, sans-serif" {{ ($settings['font_family'] ?? '') == 'Verdana, sans-serif' ? 'selected' : '' }}>Verdana</option>
            </select>
            @error('font_family')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <h5 class="mb-3">Logo & Favicon</h5>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="logo" class="form-label">Business Logo</label>
                <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                       id="logo" name="logo" accept="image/*" data-preview="#logo-preview">
                @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                <div class="logo-preview-container mt-2 {{ isset($settings['logo_path']) && $settings['logo_path'] ? '' : 'd-none' }}">
                    <img id="logo-preview" class="logo-preview" 
                         src="{{ isset($settings['logo_path']) && $settings['logo_path'] ? asset('storage/'.$settings['logo_path']) : '#' }}" 
                         alt="Logo Preview">
                    @if(isset($settings['logo_path']) && $settings['logo_path'])
                        <button type="button" class="btn btn-sm btn-danger remove-logo mt-2" data-type="logo">
                            <i class="fas fa-trash"></i> Remove Logo
                        </button>
                    @endif
                </div>
                <small class="text-muted">Recommended size: 200x200px (max 2MB)</small>
            </div>
            
            <div class="col-md-6">
                <label for="favicon" class="form-label">Favicon</label>
                <input type="file" class="form-control @error('favicon') is-invalid @enderror" 
                       id="favicon" name="favicon" accept=".ico,.png" data-preview="#favicon-preview">
                @error('favicon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                <div class="favicon-preview-container mt-2 {{ isset($settings['favicon_path']) && $settings['favicon_path'] ? '' : 'd-none' }}">
                    <img id="favicon-preview" class="favicon-preview" 
                         src="{{ isset($settings['favicon_path']) && $settings['favicon_path'] ? asset('storage/'.$settings['favicon_path']) : '#' }}" 
                         alt="Favicon Preview">
                    @if(isset($settings['favicon_path']) && $settings['favicon_path'])
                        <button type="button" class="btn btn-sm btn-danger remove-logo mt-2" data-type="favicon">
                            <i class="fas fa-trash"></i> Remove Favicon
                        </button>
                    @endif
                </div>
                <small class="text-muted">Format: ICO or PNG, size: 32x32px</small>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Save Appearance Settings</button>
    </form>
</div>
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/js/bootstrap-colorpicker.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize color pickers with proper configuration
        $('#primary_color_picker').colorpicker({
            format: 'hex',
            color: '{{ $settings['primary_color'] ?? '#3490dc' }}'
        }).on('colorpickerChange', function(e) {
            // Update the preview span background color
            $(this).find('.color-preview').css('background-color', e.color.toString());
            // Update the input value
            $(this).find('input').val(e.color.toString());
        });

        $('#secondary_color_picker').colorpicker({
            format: 'hex',
            color: '{{ $settings['secondary_color'] ?? '#38c172' }}'
        }).on('colorpickerChange', function(e) {
            $(this).find('.color-preview').css('background-color', e.color.toString());
            $(this).find('input').val(e.color.toString());
        });

        $('#accent_color_picker').colorpicker({
            format: 'hex',
            color: '{{ $settings['accent_color'] ?? '#f6993f' }}'
        }).on('colorpickerChange', function(e) {
            $(this).find('.color-preview').css('background-color', e.color.toString());
            $(this).find('input').val(e.color.toString());
        });

        // Handle logo removal
        $('.remove-logo').click(function() {
            const type = $(this).data('type');
            if (confirm('Are you sure you want to remove the ' + type + '?')) {
                $.ajax({
                    url: '{{ route("business.settings.remove-logo", $business->slug) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type: type
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`.${type}-preview-container`).fadeOut();
                            if (typeof toastr !== 'undefined') {
                                toastr.success(type.charAt(0).toUpperCase() + type.slice(1) + ' removed successfully');
                            } else {
                                alert(type.charAt(0).toUpperCase() + type.slice(1) + ' removed successfully');
                            }
                        }
                    },
                    error: function(xhr) {
                        alert('Error removing ' + type + ': ' + (xhr.responseJSON?.message || xhr.responseText));
                    }
                });
            }
        });

        // Preview uploaded image
        $('input[type="file"]').change(function(e) {
            const reader = new FileReader();
            const previewId = $(this).data('preview');
            
            reader.onload = function(e) {
                $(previewId).attr('src', e.target.result).show();
                $(previewId).closest('.logo-preview-container, .favicon-preview-container').removeClass('d-none');
            }
            
            if (this.files && this.files[0]) {
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Handle tab navigation from sidebar links
        $('.list-group-item[data-bs-toggle="tab"]').on('click', function(e) {
            e.preventDefault();
            const target = $(this).attr('href');
            
            // Activate the corresponding tab
            $(`button[data-bs-target="${target}"]`).tab('show');
            
            // Update URL hash
            window.location.hash = target;
        });

        // Check if there's a hash in URL and activate that tab
        if (window.location.hash) {
            const hash = window.location.hash;
            $(`button[data-bs-target="${hash}"]`).tab('show');
        }
    });
</script>
@endpush