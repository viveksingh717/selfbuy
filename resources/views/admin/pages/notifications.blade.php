@extends('admin.layouts.app')

@section('title', 'Notifications')
@section('subTitle', 'All Notifications')

@section('style')
    <style>
        .notif-row { display:flex; align-items:flex-start; gap:14px; padding:14px 16px; border-bottom:1px solid #f0f1f4; }
        .notif-row:last-child { border-bottom:0; }
        .notif-row.is-unread { background:#f6f9ff; }
        .notif-ico { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:#eef2ff; color:#4e73df; flex:0 0 38px; }
        .notif-row .meta { color:#98a2b3; font-size:12px; }
    </style>
@endsection

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Notifications</h3>
                    <div class="card-options">
                        <button type="button" class="btn btn-sm btn-outline-primary js-notif-readall">
                            <i class="fa fa-check-double"></i> Mark all as read
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse ($notifications as $n)
                        <a href="{{ route('admin.notifications.open', $n->id) }}"
                            class="notif-row text-body {{ is_null($n->read_at) ? 'is-unread' : '' }}">
                            <span class="notif-ico"><i class="fa {{ $n->icon }}"></i></span>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $n->title }}</div>
                                @if ($n->body)<div class="text-muted">{{ $n->body }}</div>@endif
                                <div class="meta mt-1">
                                    {{ $n->created_at->format('d M Y, h:i A') }} · {{ $n->created_at->diffForHumans() }}
                                    @if (is_null($n->read_at))
                                        · <span class="text-primary">Unread</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted">No notifications yet.</div>
                    @endforelse
                </div>
                @if ($notifications->hasPages())
                    <div class="card-footer">
                        {{ $notifications->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('script')
@endsection
