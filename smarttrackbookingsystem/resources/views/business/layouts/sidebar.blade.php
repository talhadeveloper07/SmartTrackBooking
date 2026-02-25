
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
                        <a href="{{ route('business.settings', $business->slug) }}">
                            Settings
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        
        {{-- Business Info Box with Dynamic Colors using CSS variables --}}
        <div class="plus-box" style="background: linear-gradient(135deg, var(--primary-color, #216FED) 0%, var(--secondary-color, #38c172) 100%);">
            <p class="fs-14 font-w600 mb-2 text-white">{{ $business->name ?? 'Business' }}<br>Manage your settings<br>and preferences</p>
            <a class="btn btn-light btn-sm fs-14" href="{{ route('business.settings', $business->slug) }}">Go to Settings</a>
        </div>
        <div class="copyright">
            <p><strong>{{ $business->name ?? 'Business' }}</strong> © {{ date('Y') }} All Rights Reserved</p>
        </div>
    </div>
</div>

<style>
/* Dynamic sidebar active states */
.metismenu .active > a {
    background-color: var(--primary-color) !important;
    color: white !important;
}

.metismenu .active > a i {
    color: white !important;
}

.metismenu a:hover {
    color: var(--primary-color) !important;
}

.metismenu a:hover i {
    color: var(--primary-color) !important;
}
</style>

  <div class="deznav">
            <div class="deznav-scroll">
				<ul class="metismenu" id="menu">
                    <li><a href="{{route('business.dashboard', $business->slug)}}" aria-expanded="false">
							<i class="flaticon-025-dashboard"></i>
							<span class="nav-text">Dashboard</span>
						</a>
                    </li>
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
						<i class="flaticon-050-info"></i>
							<span class="nav-text">Services</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('business.services', $business->slug) }}">All Services</a></li>
							<li><a href="{{ route('business.add.service', $business->slug) }}">Add New</a></li>
                        </ul>
                    </li>
					 <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
						<i class="flaticon-050-info"></i>
							<span class="nav-text">Employees</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('business.employees', $business->slug) }}">All Employees</a></li>
							<li><a href="{{ route('business.employees.create', $business->slug) }}">Add New</a></li>
                        </ul>
                    </li>
                </ul>
				
			</div>
        </div>

