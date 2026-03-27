  <div class="deznav">
            <div class="deznav-scroll">
				<ul class="metismenu" id="menu">
                    <li><a href="{{ route('org.dashboard') }}" aria-expanded="false">
							<i class="flaticon-025-dashboard"></i>
							<span class="nav-text">Dashboard</span>
						</a>
                    </li>
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
						<i class="flaticon-050-info"></i>
							<span class="nav-text">Businesses</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('org.business-accounts') }}">See all accounts</a></li>
							<li><a href="{{ route('org.add-new-business') }}">Add new account</a></li>
                        </ul>
                    </li>
                     <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
						<i class="flaticon-050-info"></i>
							<span class="nav-text">Plans</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('org.plans.index') }}">See All</a></li>
							<li><a href="{{ route('org.plans.create') }}">Add new</a></li>
                        </ul>
                    </li>
                    <li>
    <a href="{{ route('org.settings.edit') }}">
        <i class="flaticon-017-clipboard"></i>
        <span class="nav-text">Settings</span>
    </a>
</li>
                </ul>
			</div>
        </div>