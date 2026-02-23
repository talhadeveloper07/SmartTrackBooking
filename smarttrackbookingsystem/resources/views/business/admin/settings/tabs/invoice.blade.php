{{-- resources/views/business/admin/settings/tabs/invoice.blade.php --}}
<div class="tab-pane fade" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
    <form action="{{ route('business.settings.invoice', $business->slug) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <h5 class="mb-3">Invoice Settings</h5>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="invoice_prefix" class="form-label">Invoice Prefix</label>
                <input type="text" class="form-control @error('invoice_prefix') is-invalid @enderror" 
                       id="invoice_prefix" name="invoice_prefix" 
                       value="{{ old('invoice_prefix', $settings['invoice_prefix']) }}">
                @error('invoice_prefix')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Example: INV-2024-0001</small>
            </div>
            
            <div class="col-md-6">
                <label for="invoice_logo" class="form-label">Invoice Logo</label>
                <input type="file" class="form-control @error('invoice_logo') is-invalid @enderror" 
                       id="invoice_logo" name="invoice_logo" accept="image/*" data-preview="#invoice-logo-preview">
                @error('invoice_logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                <div class="invoice-logo-preview-container mt-2 {{ $settings['invoice_logo_path'] ? '' : 'd-none' }}">
                    <img id="invoice-logo-preview" class="logo-preview" 
                         src="{{ $settings['invoice_logo_path'] ? asset('storage/'.$settings['invoice_logo_path']) : '#' }}" 
                         alt="Invoice Logo Preview">
                    @if($settings['invoice_logo_path'])
                        <button type="button" class="btn btn-sm btn-danger remove-logo mt-2" data-type="invoice_logo">
                            <i class="fas fa-trash"></i> Remove Logo
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="tax_name" class="form-label">Tax Name</label>
                <input type="text" class="form-control @error('tax_name') is-invalid @enderror" 
                       id="tax_name" name="tax_name" 
                       value="{{ old('tax_name', $settings['tax_name']) }}">
                @error('tax_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6">
                <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                <input type="number" step="0.01" min="0" max="100" 
                       class="form-control @error('tax_rate') is-invalid @enderror" 
                       id="tax_rate" name="tax_rate" 
                       value="{{ old('tax_rate', $settings['tax_rate']) }}">
                @error('tax_rate')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="mb-3">
            <label for="invoice_footer" class="form-label">Invoice Footer</label>
            <textarea class="form-control @error('invoice_footer') is-invalid @enderror" 
                      id="invoice_footer" name="invoice_footer" rows="2">{{ old('invoice_footer', $settings['invoice_footer']) }}</textarea>
            @error('invoice_footer')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="invoice_terms" class="form-label">Invoice Terms & Conditions</label>
            <textarea class="form-control @error('invoice_terms') is-invalid @enderror" 
                      id="invoice_terms" name="invoice_terms" rows="4">{{ old('invoice_terms', $settings['invoice_terms']) }}</textarea>
            @error('invoice_terms')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">Save Invoice Settings</button>
    </form>
</div>