@extends('admin.layouts.app')

@section('title', 'Search')
@section('subTitle', 'Search Results')

@section('style')
    <style>
        .search-group-title { font-size: 12px; text-transform: uppercase; letter-spacing: .06em; color: #98a2b3; }
        .search-hit { display:flex; align-items:center; gap:12px; padding:12px 14px; border-bottom:1px solid #f0f1f4; color:inherit; }
        .search-hit:last-child { border-bottom:0; }
        .search-hit:hover { background:#f6f9ff; text-decoration:none; }
        .search-hit .ico { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:#eef2ff; color:#4e73df; flex:0 0 34px; }
        .search-hit .sub { color:#98a2b3; font-size:12px; }
    </style>
@endsection

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">

            <form method="GET" action="{{ route('admin.search') }}" class="mb-3">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" value="{{ $q }}"
                        placeholder="Search products, categories, brands, coupons, pages, messages…" autofocus>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Search</button>
                    </div>
                </div>
            </form>

            @if (mb_strlen($q) < 2)
                <div class="card"><div class="card-body text-center text-muted">
                    Type at least 2 characters to search.
                </div></div>
            @elseif ($total === 0)
                <div class="card"><div class="card-body text-center text-muted">
                    No results for <strong>“{{ $q }}”</strong>.
                </div></div>
            @else
                <p class="text-muted">{{ $total }} result{{ $total === 1 ? '' : 's' }} for <strong>“{{ $q }}”</strong></p>

                <div class="row">
                    @foreach ($groups as $group)
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title search-group-title">
                                        <i class="fa {{ $group['icon'] }} mr-1"></i> {{ $group['label'] }}
                                        <span class="badge badge-light ml-1">{{ count($group['items']) }}</span>
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    @foreach ($group['items'] as $item)
                                        <a href="{{ $item['url'] }}" class="search-hit">
                                            <span class="ico"><i class="fa {{ $group['icon'] }}"></i></span>
                                            <span>
                                                <div class="font-weight-bold">{{ $item['title'] }}</div>
                                                @if (!empty($item['subtitle']))
                                                    <div class="sub">{{ $item['subtitle'] }}</div>
                                                @endif
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

@endsection

@section('script')
@endsection
