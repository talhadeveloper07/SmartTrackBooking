<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="nav-item">
                        <div class="input-group search-area">
                            <input type="text" class="form-control" placeholder="Search here">
                            <span class="input-group-text"><a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a></span>
                        </div>
                    </div>
                </div>
                <ul class="navbar-nav header-right">
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link bell dz-theme-mode" href="javascript:void(0);">
                            <i id="icon-light" class="fas fa-sun"></i>
                            <i id="icon-dark" class="fas fa-moon"></i>
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.4524 25.6682C11.0605 27.0357 12.409 28 14.0005 28C15.592 28 16.9405 27.0357 17.5487 25.6682C16.4265 25.7231 15.2594 25.76 14.0005 25.76C12.7417 25.76 11.5746 25.723 10.4524 25.6682Z" fill="#737B8B"/>
                                <path d="M26.3532 19.74C24.877 17.8785 22.3996 14.2195 22.3996 10.64C22.3996 7.09073 20.1193 3.89758 16.7996 2.72382C16.7593 1.21406 15.5183 0 14.0007 0C12.482 0 11.2422 1.21406 11.2018 2.72382C7.88101 3.89758 5.6007 7.09073 5.6007 10.64C5.6007 14.2207 3.1244 17.8785 1.64712 19.74C1.15433 20.3616 1.00197 21.1825 1.24058 21.9363C1.47354 22.6721 2.05367 23.2422 2.79288 23.4595C4.08761 23.8415 6.20997 24.2715 9.44682 24.491C10.8479 24.5851 12.3543 24.64 14.0008 24.64C15.646 24.64 17.1525 24.5851 18.5535 24.491C21.7915 24.2715 23.9128 23.8415 25.2086 23.4595C25.9478 23.2422 26.5268 22.6722 26.7598 21.9363C26.9983 21.1825 26.8449 20.3616 26.3532 19.74Z" fill="#737B8B"/>
                            </svg>
                            <span class="badge light text-white rounded-circle" style="background-color: var(--primary-color);">4</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="widget-media dz-scroll p-3" style="height:380px;">
                                <ul class="timeline">
                                    <li>
                                        <div class="timeline-panel">
                                            <div class="media me-2">
                                                <div class="avatar text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: var(--primary-color);">
                                                    <span>JD</span>
                                                </div>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="mb-1">Dr. Sultana Send you Photo</h6>
                                                <small class="d-block">29 July 2020 - 02:26 PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <a class="all-notification" href="javascript:void(0);">See all notifications <i class="ti-arrow-end"></i></a>
                        </div>
                    </li>
                    
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link" href="javascript:void(0);" data-bs-toggle="dropdown">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.8257 17.5282C14.563 17.6783 14.2627 17.7534 14 17.7534C13.7373 17.7534 13.437 17.6783 13.1743 17.5282L0 9.49598V20.193C0 22.4826 1.83914 24.3217 4.12869 24.3217H23.8713C26.1609 24.3217 28 22.4826 28 20.193V9.49598L14.8257 17.5282Z" fill="#737B8B"/>
                                <path d="M23.8713 3.67829H4.12863C2.17689 3.67829 0.525417 5.06703 0.112549 6.90617L13.9999 15.3887L27.8873 6.90617C27.4745 5.06703 25.823 3.67829 23.8713 3.67829Z" fill="#737B8B"/>
                            </svg>
                            <span class="badge light text-white rounded-circle" style="background-color: var(--secondary-color);">15</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="widget-timeline dz-scroll style-1 p-3 height370">
                                <ul class="timeline">
                                    <li>
                                        <div class="timeline-badge" style="background-color: var(--primary-color);"></div>
                                        <a class="timeline-panel text-muted" href="javascript:void(0);">
                                            <span>10 minutes ago</span>
                                            <h6 class="mb-0">New order placed <strong style="color: var(--primary-color);">#XF-2356</strong></h6>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <div class="header-info me-3">
                                <span class="fs-18 font-w500 text-end">{{ Auth::user()->name ?? 'User' }}</span>
                                <small class="text-end fs-14 font-w400">{{ Auth::user()->email ?? 'user@example.com' }}</small>
                            </div>
                            <div class="avatar text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: var(--primary-color);">
                                <span>{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('business.settings', $business->slug ?? '#') }}" class="dropdown-item ai-icon">
                                <i class="fas fa-user me-2" style="color: var(--primary-color);"></i>
                                <span class="ms-2">Profile</span>
                            </a>
                            <a href="{{ route('business.settings', $business->slug ?? '#') }}" class="dropdown-item ai-icon">
                                <i class="fas fa-cog me-2" style="color: var(--secondary-color);"></i>
                                <span class="ms-2">Settings</span>
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2" style="color: #dc3545;"></i>
                                <span class="ms-2">{{ __('Logout') }}</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>