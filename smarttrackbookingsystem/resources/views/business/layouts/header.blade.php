<div class="header">
	<div class="header-content">
		<nav class="navbar navbar-expand">
			<div class="collapse navbar-collapse justify-content-between">
				<div class="header-left">
					<div class="nav-item">
						<div class="input-group search-area">
							<input type="text" id="global-search" class="form-control" placeholder="Search anything..."
								autocomplete="off">
							<div>
								<div id="search-dropdown" class="card search-dropdown d-none"></div>
							</div>
						</div>

					</div>
				</div>
				<ul class="navbar-nav header-right">
					<li class="nav-item">
						@php
							$user = auth()->user();
							// Use the relationship we just defined
							$business = $user ? $user->business : null;
							$showUpgradeAlert = false;

							if ($business) {
								// Get the active plan through the business
								$plan = $business->plan;

								if ($plan) {
									$currentCount = $business->employees()->count();
									$limit = $plan->max_employees;

									if ($currentCount >= $limit) {
										$showUpgradeAlert = true;
									}
								}
							}
						@endphp

						@if($showUpgradeAlert)

								<a href="{{ route('business.subscription.index', $business->slug) }}" class="gradient-btn">
    <svg class="gradient-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 24">
        <path d="m18 0 8 12 10-8-4 20H4L0 4l10 8 8-12z"></path>
    </svg>
    Upgrade Plan
</a>

						@endif
					</li>
					<li class="nav-item dropdown notification_dropdown">
						<a class="nav-link bell dz-theme-mode" href="javascript:void(0);">
							<i id="icon-light" class="fas fa-sun"></i>
							<i id="icon-dark" class="fas fa-moon"></i>

						</a>
					</li>
					<li class="nav-item dropdown notification_dropdown">
						<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
							<svg width="28" height="28" viewBox="0 0 28 28" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path
									d="M10.4524 25.6682C11.0605 27.0357 12.409 28 14.0005 28C15.592 28 16.9405 27.0357 17.5487 25.6682C16.4265 25.7231 15.2594 25.76 14.0005 25.76C12.7417 25.76 11.5746 25.723 10.4524 25.6682Z"
									fill="#737B8B" />
								<path
									d="M26.3532 19.74C24.877 17.8785 22.3996 14.2195 22.3996 10.64C22.3996 7.09073 20.1193 3.89758 16.7996 2.72382C16.7593 1.21406 15.5183 0 14.0007 0C12.482 0 11.2422 1.21406 11.2018 2.72382C7.88101 3.89758 5.6007 7.09073 5.6007 10.64C5.6007 14.2207 3.1244 17.8785 1.64712 19.74C1.15433 20.3616 1.00197 21.1825 1.24058 21.9363C1.47354 22.6721 2.05367 23.2422 2.79288 23.4595C4.08761 23.8415 6.20997 24.2715 9.44682 24.491C10.8479 24.5851 12.3543 24.64 14.0008 24.64C15.646 24.64 17.1525 24.5851 18.5535 24.491C21.7915 24.2715 23.9128 23.8415 25.2086 23.4595C25.9478 23.2422 26.5268 22.6722 26.7598 21.9363C26.9983 21.1825 26.8449 20.3616 26.3532 19.74Z"
									fill="#737B8B" />
							</svg>
							<span class="badge light text-white bg-primary rounded-circle">4</span>
						</a>
						<div class="dropdown-menu dropdown-menu-end">
							<div id="DZ_W_Notification1" class="widget-media dz-scroll p-3" style="height:380px;">
								<ul class="timeline">
									<li>
										<div class="timeline-panel">
											<div class="media me-2">
												<img alt="image" width="50" src="images/avatar/1.jpg">
											</div>
											<div class="media-body">
												<h6 class="mb-1">Dr sultads Send you Photo</h6>
												<small class="d-block">29 July 2020 - 02:26 PM</small>
											</div>
										</div>
									</li>
									<li>
										<div class="timeline-panel">
											<div class="media me-2 media-info">
												KG
											</div>
											<div class="media-body">
												<h6 class="mb-1">Resport created successfully</h6>
												<small class="d-block">29 July 2020 - 02:26 PM</small>
											</div>
										</div>
									</li>
									<li>
										<div class="timeline-panel">
											<div class="media me-2 media-success">
												<i class="fa fa-home"></i>
											</div>
											<div class="media-body">
												<h6 class="mb-1">Reminder : Treatment Time!</h6>
												<small class="d-block">29 July 2020 - 02:26 PM</small>
											</div>
										</div>
									</li>
									<li>
										<div class="timeline-panel">
											<div class="media me-2">
												<img alt="image" width="50" src="images/avatar/1.jpg">
											</div>
											<div class="media-body">
												<h6 class="mb-1">Dr sultads Send you Photo</h6>
												<small class="d-block">29 July 2020 - 02:26 PM</small>
											</div>
										</div>
									</li>
									<li>
										<div class="timeline-panel">
											<div class="media me-2 media-danger">
												KG
											</div>
											<div class="media-body">
												<h6 class="mb-1">Resport created successfully</h6>
												<small class="d-block">29 July 2020 - 02:26 PM</small>
											</div>
										</div>
									</li>
									<li>
										<div class="timeline-panel">
											<div class="media me-2 media-primary">
												<i class="fa fa-home"></i>
											</div>
											<div class="media-body">
												<h6 class="mb-1">Reminder : Treatment Time!</h6>
												<small class="d-block">29 July 2020 - 02:26 PM</small>
											</div>
										</div>
									</li>
								</ul>
							</div>
							<a class="all-notification" href="javascript:void(0);">See all notifications <i
									class="ti-arrow-end"></i></a>
						</div>
					</li>
				
					
					<li class="nav-item dropdown header-profile">
						<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
							<div class="header-info me-3">
								<span class="fs-18 font-w500 text-end">{{ ucwords(Auth::user()->name) }}</span>
								<small class="text-end fs-14 font-w400">{{Auth::user()->email}}</small>
							</div>
							<img src="/icons/user.png" width="20" alt="">
						</a>
						<div class="dropdown-menu dropdown-menu-end">
							<a href="app-profile.html" class="dropdown-item ai-icon d-flex">
								<svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18"
									height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
									stroke-linecap="round" stroke-linejoin="round">
									<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
									<circle cx="12" cy="7" r="4"></circle>
								</svg>
								<span class="ms-2">Profile </span>
							</a>
							<a class="dropdown-item d-flex gap-2" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
								<svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18"
									height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
									stroke-linecap="round" stroke-linejoin="round">
									<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
									<polyline points="16 17 21 12 16 7"></polyline>
									<line x1="21" y1="12" x2="9" y2="12"></line>
								</svg>
								{{ __('Logout') }}
							</a>

							<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
								@csrf
							</form>
						</div>
					</li>
					<li class="nav-item">

					</li>
				</ul>
			</div>
		</nav>
	</div>
</div>