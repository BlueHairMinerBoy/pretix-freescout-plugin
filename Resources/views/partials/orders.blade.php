@php
    $statusMap = [
        'n' => ['label' => __('Pending'),   'class' => 'warning'],
        'p' => ['label' => __('Paid'),       'class' => 'success'],
        'e' => ['label' => __('Expired'),    'class' => 'default'],
        'c' => ['label' => __('Cancelled'),  'class' => 'danger'],
    ];

    $currencySymbols = [
        'GBP' => '£',   'EUR' => '€',   'USD' => '$',   'CAD' => 'CA$',
        'AUD' => 'A$',  'NZD' => 'NZ$', 'CHF' => 'CHF', 'JPY' => '¥',
        'CNY' => '¥',   'DKK' => 'kr',  'NOK' => 'kr',  'SEK' => 'kr',
        'PLN' => 'zł',  'CZK' => 'Kč',  'HUF' => 'Ft',  'RON' => 'lei',
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

            $currency   = $order['currency'] ?? '';
            $symbol     = $currencySymbols[$currency] ?? $currency . ' ';
            $total      = $symbol . $order['total'];

            $locale     = $order['locale'] ?? 'en';
            $tickets    = [];
            foreach ($order['positions'] ?? [] as $position) {
                if (!empty($position['is_bundled'])) {
                    continue;
                }
                $item = $position['item'] ?? null;
                if (is_array($item)) {
                    $names  = $item['name'] ?? [];
                    $label  = $names[$locale] ?? $names['en'] ?? reset($names) ?? '';
                } else {
                    $label = '';
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
            @endif

            <div class="pretix-order-footer">
                <span class="pretix-order-total">{{ $total }}</span>
            </div>
        </div>
    @endforeach
@endif
