<div id="page_top" class="section-body top_dark">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <a href="javascript:void(0)" class="icon menu_toggle mr-3"><i class="fa  fa-align-left"></i></a>
                <h1 class="page-title">@yield('subTitle')</h1>
            </div>
            <div class="right">
                <form method="GET" action="{{ route('admin.search') }}" class="input-icon xs-hide mr-4" role="search">
                    <input type="text" name="q" class="form-control" placeholder="Search for..."
                        value="{{ request()->routeIs('admin.search') ? request('q') : '' }}" autocomplete="off">
                    <button type="submit" class="input-icon-addon border-0 bg-transparent p-0" aria-label="Search">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
                <div class="notification d-flex">
                    {{-- <div class="dropdown d-flex">
                        <a class="nav-link icon d-none d-md-flex btn btn-default btn-icon ml-2" data-toggle="dropdown"><i class="fa fa-language"></i></a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                            <a class="dropdown-item" href="#"><img class="w20 mr-2" src="assets/images/flags/us.svg">English</a>
                            <div class="dropdown-divider"></div>
                        </div>
                    </div> --}}
                    {{-- ── Contact messages ───────────────────────────── --}}
                    @php $contactUnread = $headerContactUnread ?? 0; @endphp
                    <div class="dropdown d-flex">
                        <a class="nav-link icon d-none d-md-flex btn btn-default btn-icon ml-2"
                            data-toggle="dropdown" title="Contact messages">
                            <i class="fa fa-envelope"></i>
                            <span class="badge badge-success nav-unread js-contact-badge"
                                @if (!$contactUnread) style="display:none" @endif>{{ $contactUnread }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                            <div class="dropdown-header px-3 pt-2 d-flex justify-content-between">
                                <strong>Messages</strong>
                                <span class="text-muted">{{ $contactUnread }} unread</span>
                            </div>
                            <ul class="right_chat list-unstyled w350 p-0">
                                @forelse ($headerContactRecent ?? [] as $msg)
                                    <li>
                                        <a href="{{ route('admin.contact_us') }}" class="media">
                                            <span class="media-object contact-avatar d-inline-flex align-items-center justify-content-center"
                                                style="width:40px;height:40px;border-radius:50%;color:#fff;font-weight:600;background:{{ $msg->avatar_color }}">
                                                {{ $msg->initial }}
                                            </span>
                                            <div class="media-body">
                                                <span class="name">{{ $msg->name }}</span>
                                                <div class="message">{{ \Illuminate\Support\Str::limit($msg->subject ?: strip_tags($msg->message), 34) }}</div>
                                                <small>{{ $msg->created_at->diffForHumans() }}</small>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li><span class="dropdown-item text-muted small">No unread messages</span></li>
                                @endforelse
                            </ul>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('admin.contact_us') }}" class="dropdown-item text-center text-muted-dark">View all messages</a>
                        </div>
                    </div>

                    {{-- ── Notifications ──────────────────────────────── --}}
                    @php $notifUnread = $headerNotifUnread ?? 0; @endphp
                    <div class="dropdown d-flex">
                        <a class="nav-link icon d-none d-md-flex btn btn-default btn-icon ml-2"
                            data-toggle="dropdown" title="Notifications">
                            <i class="fa fa-bell"></i>
                            <span class="badge badge-primary nav-unread js-notif-badge"
                                @if (!$notifUnread) style="display:none" @endif>{{ $notifUnread }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                            <div class="dropdown-header px-3 pt-2 d-flex justify-content-between">
                                <strong>Notifications</strong>
                                <a href="javascript:void(0)" class="js-notif-readall small">Mark all read</a>
                            </div>
                            <ul class="list-unstyled feeds_widget">
                                @forelse ($headerNotifications ?? [] as $n)
                                    <li>
                                        <a href="{{ route('admin.notifications.open', $n->id) }}" class="d-flex align-items-start p-2 {{ $n->is_unread ? 'bg-light' : '' }}">
                                            <div class="feeds-left mr-2"><i class="fa {{ $n->icon }}"></i></div>
                                            <div class="feeds-body">
                                                <h4 class="title mb-0" style="font-size:13px">
                                                    {{ $n->title }}
                                                    <small class="float-right text-muted">{{ $n->created_at->diffForHumans() }}</small>
                                                </h4>
                                                @if ($n->body)<small class="text-muted">{{ $n->body }}</small>@endif
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li><span class="dropdown-item text-muted small">You're all caught up</span></li>
                                @endforelse
                            </ul>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('admin.notifications') }}" class="dropdown-item text-center text-muted-dark">View all notifications</a>
                        </div>
                    </div>
                    @php $authAdmin = Auth::guard('admin')->user(); @endphp
                    <div class="dropdown d-flex">
                        <a class="nav-link icon d-none d-md-flex btn btn-default btn-icon ml-2" data-toggle="dropdown">
                            @if ($authAdmin && $authAdmin->avatar_url)
                                <img src="{{ $authAdmin->avatar_url }}" alt="{{ $authAdmin->name }}"
                                    style="width:22px;height:22px;border-radius:50%;object-fit:cover;">
                            @else
                                <i class="fa fa-user"></i>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                            <div class="px-3 py-2">
                                <div class="font-weight-bold">{{ $authAdmin?->name }}</div>
                                <small class="text-muted">{{ $authAdmin?->email }}</small>
                            </div>
                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                <i class="dropdown-icon fa fa-user"></i> My Profile
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.profile') }}#change-password">
                                <i class="dropdown-icon fa fa-key"></i> Change Password
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.settings') }}">
                                <i class="dropdown-icon fa fa-cog"></i> System Settings
                            </a>

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="{{ route('admin.logout') }}">
                                <i class="dropdown-icon fa fa-sign-out"></i>
                                Sign out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
