@extends('business.layouts.app')

@section('business_content')
    @php
        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            0 => 'Sunday'
        ];

        // helper: format 24h to 12h
        $fmt = function ($t) {
            if (!$t)
                return '';
            try {
                return \Carbon\Carbon::createFromFormat('H:i', $t)->format('h:ia');
            } catch (\Exception $e) {
                return $t;
            }
        };
    @endphp


    <div class="container-fluid">
         <div class="d-flex align-items-center justify-content-between mb-3">
           <div>
             <h3 class="m-0">{{ ucwords($employee->name) }}</h3>
            <p class="m-0">{{$employee->employee_id}}</p>
           </div>
            <div>
                <a href="{{ route('business.employees.edit', [$business->slug, $employee->id]) }}"
                    class="btn btn-primary me-2">
                    Edit</a>
                <a href="{{ route('business.employees', $business->slug) }}" class="btn btn-light">
                    Back
                </a>
            </div>
        </div>
        <!-- row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="profile card card-body px-3 pt-3 pb-0">
                    <div class="profile-head">
                        <div class="photo-content">
                            <div class="cover-photo rounded"></div>
                        </div>
                        <div class="profile-info">
                            <div class="profile-photo">
                                <img src="images/profile/profile.png" class="img-fluid rounded-circle" alt="">
                            </div>
                            <div class="profile-details">
                                <div class="profile-name px-3 pt-2">
                                    <h4 class="text-primary mb-0">{{ ucwords($employee->name) }}</h4>
                                    <p>{{$employee->employee_id}}</p>
                                </div>
                                <div class="profile-email px-2 pt-2">
                                    <h4 class="text-muted mb-0"><i class="fa fa-envelope"></i> {{ $employee->email }}</h4>
                                    <p><i class="fa fa-phone"></i> {{ $employee->phone }}</p>
                                </div>
                                <div class="dropdown ms-auto">
                                    <span class="badge bg-success">{{ $employee->status }}</span>
                                    <a href="#" class="btn btn-primary p-1 light" data-bs-toggle="dropdown"
                                        aria-expanded="true"><svg xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <circle fill="#000000" cx="5" cy="12" r="2"></circle>
                                                <circle fill="#000000" cx="12" cy="12" r="2"></circle>
                                                <circle fill="#000000" cx="19" cy="12" r="2"></circle>
                                            </g>
                                        </svg></a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li class="dropdown-item"><i class="fa fa-user-circle text-primary me-2"></i> View
                                            profile</li>
                                        <li class="dropdown-item"><i class="fa fa-users text-primary me-2"></i> Add to
                                            btn-close friends</li>
                                        <li class="dropdown-item"><i class="fa fa-plus text-primary me-2"></i> Add to group
                                        </li>
                                        <li class="dropdown-item"><i class="fa fa-ban text-primary me-2"></i> Block</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-4">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card h-auto">
                            <div class="card-body">
                                <div class="profile-statistics">
                                    <div class="text-center">
                                        <div class="row">
                                            <div class="col">
                                                <h3 class="m-b-0">150</h3><span>Follower</span>
                                            </div>
                                            <div class="col">
                                                <h3 class="m-b-0">140</h3><span>Place Stay</span>
                                            </div>
                                            <div class="col">
                                                <h3 class="m-b-0">45</h3><span>Reviews</span>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <a href="javascript:void(0);" class="btn btn-primary mb-1 me-1">Follow</a>
                                            <a href="javascript:void(0);" class="btn btn-primary mb-1"
                                                data-bs-toggle="modal" data-bs-target="#sendMessageModal">Send Message</a>
                                        </div>
                                    </div>
                                    <!-- Modal -->
                                    <div class="modal fade" id="sendMessageModal">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Send Message</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="comment-form">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label class="text-black font-w600 form-label">Name
                                                                        <span class="required">*</span></label>
                                                                    <input type="text" class="form-control" value="Author"
                                                                        name="Author" placeholder="Author">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label class="text-black font-w600 form-label">Email
                                                                        <span class="required">*</span></label>
                                                                    <input type="text" class="form-control" value="Email"
                                                                        placeholder="Email" name="Email">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="mb-3">
                                                                    <label
                                                                        class="text-black font-w600 form-label">Comment</label>
                                                                    <textarea rows="8" class="form-control" name="comment"
                                                                        placeholder="Comment"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="mb-3 mb-0">
                                                                    <input type="submit" value="Post Comment"
                                                                        class="submit btn btn-primary" name="submit">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="profile-blog">
                                <div class="d-flex justify-content-between">
                                     <h5>Services Offered</h5>
                                            <span class="text-muted small">{{ $employee->services->count() }} assigned</span>
                                </div>    
                                    @if($employee->services->count())
                                        <div class="d-flex flex-wrap gap-2 mt-3">
                                            @foreach($employee->services as $srv)
                                                <span
                                                    class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                                    {{ ucwords($srv->name) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-muted">No services assigned.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card h-auto">
                    <div class="card-body">
                        <div class="profile-tab">
                            <div class="custom-tab-1">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item"><a href="#my-posts" data-bs-toggle="tab"
                                            class="nav-link active show">Working Schedule</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div id="my-posts" class="tab-pane fade active show">
                                        <div class="my-post-content pt-3">
                                            @foreach($days as $dayIndex => $dayName)
                                                @php
                                                    $rows = $schedule[$dayIndex] ?? collect();

                                                    // off if any record says is_off=1 OR no slots and day not present
                                                    $isOff = $rows->count() ? (bool) ($rows->first()->is_off) : true;

                                                    // if present but not off => show all slots
                                                    $slots = $rows->where('is_off', false)->values();
                                                @endphp

                                                <div
                                                    class="border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
                                                    <div
                                                        class="fw-semibold {{ $isOff ? 'text-danger text-decoration-line-through' : '' }}">
                                                        {{ $dayName }}
                                                    </div>

                                                    <div class="text-muted">
                                                        @if($isOff)
                                                            <span class="badge bg-light text-muted border">Off</span>
                                                        @else
                                                            @if($slots->count())
                                                                <div class="d-flex flex-column align-items-end gap-1">
                                                                    @foreach($slots as $s)
                                                                        <div class="small fw-semibold">
                                                                            {{ \Carbon\Carbon::parse($s->start_time)->format('h:i A') }} –
                                                                            {{ \Carbon\Carbon::parse($s->end_time)->format('h:i A') }}
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <span class="badge bg-light text-muted border">No time set</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal -->
                            <div class="modal fade" id="replyModal">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Post Reply</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form>
                                                <textarea class="form-control" rows="4">Message</textarea>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger light"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary">Reply</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  
@endsection