@extends($layout)

@section($content)
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Dashboard Settings</h4>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ $updateRoute }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">

                            {{-- Logo --}}
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Logo</label>
                                <input type="file" name="logo" class="form-control">
                                @error('logo') <small class="text-danger">{{ $message }}</small> @enderror

                                @if($setting?->logo)
                                    <div class="mt-3">
                                        <img src="{{ asset('storage/'.$setting->logo) }}" style="height:60px;">
                                    </div>
                                @endif
                            </div>

                            {{-- Favicon --}}
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Favicon</label>
                                <input type="file" name="favicon" class="form-control">
                                @error('favicon') <small class="text-danger">{{ $message }}</small> @enderror

                                @if($setting?->favicon)
                                    <div class="mt-3">
                                        <img src="{{ asset('storage/'.$setting->favicon) }}" style="height:40px;">
                                    </div>
                                @endif
                            </div>

                            {{-- Colors --}}
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Primary Color</label>
                                <input type="color" name="primary_color"
                                    class="form-control form-control-color"
                                    value="{{ old('primary_color', $setting->primary_color ?? '#0d6efd') }}">
                                @error('primary_color') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">Secondary Color</label>
                                <input type="color" name="secondary_color"
                                    class="form-control form-control-color"
                                    value="{{ old('secondary_color', $setting->secondary_color ?? '#6c757d') }}">
                                @error('secondary_color') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">Sidebar Background</label>
                                <input type="color" name="sidebar_bg"
                                    class="form-control form-control-color"
                                    value="{{ old('sidebar_bg', $setting->sidebar_bg ?? '#111827') }}">
                                @error('sidebar_bg') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">Sidebar Text</label>
                                <input type="color" name="sidebar_text"
                                    class="form-control form-control-color"
                                    value="{{ old('sidebar_text', $setting->sidebar_text ?? '#ffffff') }}">
                                @error('sidebar_text') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">Topbar Background</label>
                                <input type="color" name="topbar_bg"
                                    class="form-control form-control-color"
                                    value="{{ old('topbar_bg', $setting->topbar_bg ?? '#ffffff') }}">
                                @error('topbar_bg') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">Topbar Text</label>
                                <input type="color" name="topbar_text"
                                    class="form-control form-control-color"
                                    value="{{ old('topbar_text', $setting->topbar_text ?? '#111827') }}">
                                @error('topbar_text') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                        </div>

                        <div class="text-end">
                            <button class="btn btn-primary" type="submit">Save Settings</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection