<div class="nav-header">
     
<a href="{{ route('org.dashboard') }}" class="brand-logo">

    @php
        $organization = \App\Models\Organization::first();
        $logo = $organization?->dashboardSetting?->logo ?? null;
    @endphp

    @if($logo)
        <img src="{{ asset('storage/'.$logo) }}"
             alt="Organization Logo"
             style="height:64px;width:auto;">
    @else
        {{-- fallback org logo --}}
        <svg width="64" height="64" viewBox="0 0 64 64">
            <rect width="64" height="64" rx="18" fill="var(--primary)"/>
            <text x="50%" y="54%" text-anchor="middle"
                  fill="#fff" font-size="24" font-weight="bold">
                {{ strtoupper(substr($organization->name ?? 'O',0,1)) }}
            </text>
        </svg>
    @endif

</a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>