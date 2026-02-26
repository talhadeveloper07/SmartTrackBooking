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
