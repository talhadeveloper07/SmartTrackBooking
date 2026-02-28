@extends('business.layouts.app')

@section('business_content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">My Profile</h4>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-4">

        {{-- BASIC INFO --}}
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Basic Information</h5>
                </div>

                <div class="card-body">
                    <form method="POST"
                          action="{{ route('business.profile.update', $business->slug) }}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', auth()->user()->name) }}">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email (Read only)</label>
                            <input type="email"
                                   class="form-control"
                                   value="{{ auth()->user()->email }}"
                                   disabled>
                            <small class="text-muted">Email cannot be changed from dashboard.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', auth()->user()->phone ?? '') }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address"
                                      rows="3"
                                      class="form-control @error('address') is-invalid @enderror">{{ old('address', auth()->user()->address ?? '') }}</textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Optional profile image --}}
                        <div class="mb-3">
                            <label class="form-label">Profile Image (optional)</label>
                            <input type="file"
                                   name="avatar"
                                   class="form-control @error('avatar') is-invalid @enderror"
                                   accept="image/*">
                            @error('avatar') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            @if(!empty(auth()->user()->avatar))
                                <div class="mt-3">
                                    <img src="{{ asset('storage/'.auth()->user()->avatar) }}"
                                         alt="Avatar"
                                         style="height:70px;width:70px;border-radius:14px;object-fit:cover;">
                                </div>
                            @endif
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                Save Changes
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- CHANGE PASSWORD (EMAIL LINK) --}}
        <div class="col-12 col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Change Password</h5>
                </div>

                <div class="card-body">
                    <p class="text-muted mb-3">
                        For security, password cannot be changed directly from dashboard.
                        Click the button below and we will send a password reset link to your email:
                        <strong>{{ auth()->user()->email }}</strong>
                    </p>

                    <form method="POST" action="{{ route('business.profile.password.email', $business->slug) }}">
                        @csrf

                        <button type="submit" class="btn btn-outline-primary w-100">
                            Send Password Reset Link
                        </button>
                    </form>

                    <div class="mt-3">
                        <small class="text-muted">
                            If you don’t receive the email, check spam folder or try again.
                        </small>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection