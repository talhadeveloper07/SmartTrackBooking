{{-- resources/views/business/layouts/sidebar.blade.php --}}
<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            <!-- Dashboard Link -->
            <li>
                <a href="{{ route('business.dashboard', $business->slug) }}" aria-expanded="false">
                    <i class="flaticon-025-dashboard"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!-- Settings Link with Submenu -->
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-050-info"></i>
                    <span class="nav-text">Settings</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('business.settings', $business->slug) }}#general">
                            <i class="fas fa-cog me-2"></i>General Settings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.settings', $business->slug) }}#appearance">
                            <i class="fas fa-palette me-2"></i>Appearance
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.settings', $business->slug) }}#notifications">
                            <i class="fas fa-bell me-2"></i>Notifications
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.settings', $business->slug) }}#invoice">
                            <i class="fas fa-file-invoice me-2"></i>Invoice
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.settings', $business->slug) }}#security">
                            <i class="fas fa-shield-alt me-2"></i>Security
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.settings', $business->slug) }}#email">
                            <i class="fas fa-envelope me-2"></i>Email
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.settings', $business->slug) }}#localization">
                            <i class="fas fa-globe me-2"></i>Localization
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        
        {{-- Business Info Box with Dynamic Colors --}}
        @php
            $colors = App\Helpers\BusinessSettingsHelper::getColors($business ?? null);
        @endphp
        <div class="plus-box" style="background: linear-gradient(135deg, {{ $colors['primary'] }} 0%, {{ $colors['secondary'] }} 100%);">
            <p class="fs-14 font-w600 mb-2 text-white">{{ $business->name ?? 'Business' }}<br>Manage your settings<br>and preferences</p>
            <a class="btn btn-light btn-sm fs-14" href="{{ route('business.settings', $business->slug) }}">Go to Settings</a>
        </div>
        <div class="copyright">
            <p><strong>{{ $business->name ?? 'Business' }}</strong> © {{ date('Y') }} All Rights Reserved</p>
        </div>
    </div>
</div>