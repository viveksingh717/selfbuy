{{--
    Order tracking journey - milestone progress tracker.
    Used on the admin order page and the customer's order page.
    Expects $order (App\Models\Order). Self-contained styles (prefixed .ojt-) so it
    looks the same in the admin theme and the storefront theme.
--}}
@php $steps = $order->milestones(); @endphp

@once
<style>
    .ojt { display: flex; align-items: flex-start; margin: 4px 0; padding: 0; list-style: none; }
    .ojt-step { flex: 1; position: relative; text-align: center; min-width: 0; }
    /* connector line to the next step */
    .ojt-step:not(:last-child):after {
        content: ''; position: absolute; top: 17px; left: calc(50% + 20px); right: calc(-50% + 20px);
        height: 3px; border-radius: 2px; background: #e3e6ea;
    }
    .ojt-step.is-done:not(:last-child):after { background: #21ba45; }
    .ojt-dot {
        width: 36px; height: 36px; margin: 0 auto 8px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; font-weight: 700; line-height: 1;
        background: #fff; color: #9aa0ac; border: 3px solid #e3e6ea; position: relative; z-index: 1;
    }
    .ojt-step.is-done .ojt-dot { background: #21ba45; border-color: #21ba45; color: #fff; }
    .ojt-step.is-current .ojt-dot { border-color: #2185d0; color: #2185d0; box-shadow: 0 0 0 5px rgba(33, 133, 208, .15); animation: ojt-pulse 1.8s ease-in-out infinite; }
    .ojt-step.is-cancelled .ojt-dot { background: #db2828; border-color: #db2828; color: #fff; }
    .ojt-label { font-weight: 600; font-size: 14px; color: #3d4451; }
    .ojt-step.is-upcoming .ojt-label { color: #9aa0ac; font-weight: 500; }
    .ojt-step.is-current .ojt-label { color: #2185d0; }
    .ojt-step.is-cancelled .ojt-label { color: #db2828; }
    .ojt-date { font-size: 12px; color: #8a919c; margin-top: 2px; }
    .ojt-extra { font-size: 12px; margin-top: 6px; color: #3d4451; word-break: break-word; }
    .ojt-extra a { font-weight: 600; }
    @keyframes ojt-pulse { 50% { box-shadow: 0 0 0 9px rgba(33, 133, 208, .06); } }

    /* Phones: vertical timeline */
    @media (max-width: 575.98px) {
        .ojt { flex-direction: column; }
        .ojt-step { display: flex; text-align: left; padding-bottom: 18px; width: 100%; }
        .ojt-step:not(:last-child):after { top: 38px; bottom: 2px; left: 17px; right: auto; width: 3px; height: auto; }
        .ojt-dot { margin: 0 14px 0 0; flex: 0 0 36px; }
        .ojt-body { padding-top: 6px; }
    }
</style>
@endonce

<ol class="ojt" aria-label="Order progress">
    @foreach ($steps as $i => $step)
        <li class="ojt-step is-{{ $step['state'] }}" @if ($step['state'] === 'current') aria-current="step" @endif>
            <span class="ojt-dot">
                @if ($step['state'] === 'done') &#10003;
                @elseif ($step['state'] === 'cancelled') &#10005;
                @else {{ $i + 1 }}
                @endif
            </span>
            <div class="ojt-body">
                <div class="ojt-label">{{ $step['label'] }}</div>
                <div class="ojt-date">
                    @if ($step['date'])
                        {{ $step['date']->format('d M Y') }}<br>{{ $step['date']->format('h:i A') }}
                    @elseif ($step['state'] === 'current')
                        {{ $step['caption'] ?? 'In progress' }}
                    @endif
                </div>

                @if ($step['key'] === 'shipped' && $order->tracking_number && $step['state'] !== 'upcoming')
                    <div class="ojt-extra">
                        {{ $order->tracking_courier }} {{ $order->tracking_number }}
                        @if ($order->tracking_url)
                            <br><a href="{{ $order->tracking_url }}" target="_blank" rel="noopener">Track package &rarr;</a>
                        @endif
                    </div>
                @endif

                @if ($step['key'] === 'cancelled' && $order->cancel_reason)
                    <div class="ojt-extra text-muted">{{ $order->cancel_reason }}</div>
                @endif
            </div>
        </li>
    @endforeach
</ol>
