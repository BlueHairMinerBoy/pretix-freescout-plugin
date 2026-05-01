@php
    $statusMap = [
        'n' => ['label' => __('Pending'),   'class' => 'warning'],
        'p' => ['label' => __('Paid'),       'class' => 'success'],
        'e' => ['label' => __('Expired'),    'class' => 'default'],
        'c' => ['label' => __('Cancelled'),  'class' => 'danger'],
    ];

@endphp

@if (empty($orders))
    <p class="pretix-empty">
        {{ __('No Pretix bookings found for :email.', ['email' => $email]) }}
    </p>
@else
    @foreach ($orders as $order)
        @php
            $status     = $statusMap[$order['status']] ?? ['label' => strtoupper($order['status']), 'class' => 'default'];
            $backendUrl = rtrim($base_url, '/') . '/control/event/'
                        . rawurlencode($organizer) . '/'
                        . rawurlencode($order['event']) . '/orders/'
                        . rawurlencode($order['code']) . '/';
            $name       = $order['invoice_address']['name'] ?? ($order['invoice_address']['company'] ?? '');
            $date       = \Carbon\Carbon::parse($order['datetime'])->format('d M Y');

            $total = '£' . $order['total'];

            // Build locale candidates: exact match first, then language prefix, then 'en'
            $locale     = $order['locale'] ?? 'en';
            $lang       = substr($locale, 0, 2);
            $locales    = array_unique([$locale, $lang, 'en']);

            $positions   = $order['positions'] ?? [];
            $ticketCount = count($positions);
            $tickets     = [];
            foreach ($positions as $position) {
                $item = $position['item'] ?? null;
                if (!is_array($item)) {
                    continue;
                }
                $names = $item['name'] ?? '';
                if (is_array($names)) {
                    $label = '';
                    foreach ($locales as $try) {
                        if (!empty($names[$try])) {
                            $label = $names[$try];
                            break;
                        }
                    }
                    if ($label === '' && !empty($names)) {
                        $label = array_values($names)[0];
                    }
                } else {
                    $label = (string) $names;
                }
                if ($label !== '') {
                    $tickets[] = $label;
                }
            }
        @endphp
        <div class="pretix-order-card">
            <div class="pretix-order-header">
                <a href="{{ $backendUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="pretix-order-code"
                   title="{{ __('Open order in Pretix') }}">
                    {{ $order['code'] }}
                    <span class="glyphicon glyphicon-new-window pretix-ext-icon" aria-hidden="true"></span>
                </a>
                <span class="label label-{{ $status['class'] }} pretix-status-badge">
                    {{ $status['label'] }}
                </span>
            </div>

            <div class="pretix-order-meta">
                <span class="pretix-event-name" title="{{ __('Event') }}">{{ $order['event'] }}</span>
                <span class="pretix-meta-sep">&middot;</span>
                <span class="pretix-order-date" title="{{ __('Order date') }}">{{ $date }}</span>
            </div>

            @if ($name)
                <div class="pretix-order-attendee">
                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                    {{ $name }}
                </div>
            @endif

            @if (!empty($tickets))
                <ul class="pretix-ticket-list">
                    @foreach ($tickets as $ticket)
                        <li>{{ $ticket }}</li>
                    @endforeach
                </ul>
            @elseif ($ticketCount > 0)
                <div class="pretix-ticket-count">
                    {{ trans_choice(':count ticket|:count tickets', $ticketCount) }}
                </div>
            @endif

            <div class="pretix-order-footer">
                <span class="pretix-order-total">{{ $total }}</span>
            </div>
        </div>
    @endforeach
@endif
