{{-- resources/views/business/admin/settings/tabs/security.blade.php --}}
<div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
    <form action="{{ route('business.settings.security', $business->slug) }}" method="POST">
        @csrf
        @method('PUT')
        
        <h5 class="mb-3">Security Settings</h5>
        
        <div class="mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="two_factor_auth" 
                       name="two_factor_auth" value="1" {{ $settings['two_factor_auth'] ? 'checked' : '' }}>
                <label class="form-check-label" for="two_factor_auth">Require Two-Factor Authentication for Admin Users</label>
            </div>
            <small class="text-muted">When enabled, all admin users will be required to set up 2FA</small>
        </div>
        
        <div class="mb-3">
            <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
            <input type="number" class="form-control @error('session_timeout') is-invalid @enderror" 
                   id="session_timeout" name="session_timeout" min="5" max="480"
                   value="{{ old('session_timeout', $settings['session_timeout']) }}">
            @error('session_timeout')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Automatically log users out after inactivity</small>
        </div>
        
        <div class="mb-3">
            <label for="password_expiry_days" class="form-label">Password Expiry (days)</label>
            <input type="number" class="form-control @error('password_expiry_days') is-invalid @enderror" 
                   id="password_expiry_days" name="password_expiry_days" min="0" max="365"
                   value="{{ old('password_expiry_days', $settings['password_expiry_days']) }}">
            @error('password_expiry_days')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Set to 0 for passwords to never expire</small>
        </div>
        
        <h5 class="mb-3 mt-4">Login Attempts</h5>
        
        <div class="mb-3">
            <label for="max_login_attempts" class="form-label">Maximum Login Attempts</label>
            <input type="number" class="form-control" id="max_login_attempts" 
                   name="max_login_attempts" min="3" max="10" value="5">
            <small class="text-muted">Number of failed attempts before account lockout</small>
        </div>
        
        <div class="mb-3">
            <label for="lockout_duration" class="form-label">Lockout Duration (minutes)</label>
            <input type="number" class="form-control" id="lockout_duration" 
                   name="lockout_duration" min="1" max="60" value="15">
            <small class="text-muted">How long to lock the account after too many failed attempts</small>
        </div>
        
        <button type="submit" class="btn btn-primary">Save Security Settings</button>
    </form>
</div>