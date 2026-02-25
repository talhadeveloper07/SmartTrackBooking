<div class="nav-header">
    <a href="{{ route('business.dashboard', $business->slug ?? '#') }}" class="brand-logo">
        @php
            $logo = App\Helpers\BusinessSettingsHelper::getLogo($business ?? null);
        @endphp
        
        @if($logo)
            <img src="{{ $logo }}" alt="{{ $business->name ?? 'Logo' }}" class="logo-abbr" style="max-height: 100px;">
        @else
            <svg class="logo-abbr" width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect class="rect-primary-rect" width="64" height="64" rx="18" fill="var(--primary-color, #216FED)"/>
                <path d="M33.9126 48.6459H16.7709C15.9917 48.6459 15.3542 48.0084 15.3542 47.2292V22.9334C15.3542 22.1542 15.9917 21.5167 16.7709 21.5167H17.6209C27.3959 21.5167 35.3292 29.45 35.3292 39.225V47.2292C35.2584 48.0084 34.6917 48.6459 33.9126 48.6459ZM18.1167 45.8834H32.4959V39.225C32.4959 31.15 26.1209 24.6334 18.1167 24.35V45.8834Z" fill="#F2F6FC"/>
                <path d="M47.2291 48.6459H30.0874C29.3083 48.6459 28.6708 48.0084 28.6708 47.2292C28.6708 46.45 29.3083 45.8125 30.0874 45.8125H45.8833V33.0625C45.8833 24.9875 39.5083 18.4709 31.5041 18.1875V28.2459C31.5041 29.025 30.8666 29.6625 30.0874 29.6625C29.3083 29.6625 28.6708 29.025 28.6708 28.2459V16.7709C28.6708 15.9917 29.3083 15.3542 30.0874 15.3542H30.9374C40.7124 15.3542 48.6458 23.2875 48.6458 33.0625V47.3C48.6458 48.0084 48.0083 48.6459 47.2291 48.6459Z" fill="#F2F6FC"/>
                <path d="M28.246 48.6458H22.296C21.5169 48.6458 20.8794 48.0083 20.8794 47.2292V37.95C20.8794 37.1709 21.5169 36.5334 22.296 36.5334H28.246C29.0252 36.5334 29.6627 37.1709 29.6627 37.95V47.2292C29.6627 48.0083 29.0252 48.6458 28.246 48.6458ZM23.7127 45.8833H26.8294V39.3667H23.7127V45.8833Z" fill="#F2F6FC"/>
            </svg>
            <span class="brand-title" style="color: var(--primary-color); font-weight: bold; margin-left: 10px;">
                {{ $business->name ?? 'Business' }}
            </span>
        @endif
    </a>
    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>

            <a href="index.html" class="brand-logo">
				<img src="{{ asset('storage/' . $business->logo) }}" class="logo-abbr" width="64" height="64">
				<div class="brand-title" width="108" height="44">
					{{ $business->name }}
				</div>
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>

