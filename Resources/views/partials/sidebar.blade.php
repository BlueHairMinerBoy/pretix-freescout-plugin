<div class="conv-sidebar-block pretix-sidebar-block"
     data-email="{{ $customer_email }}"
     data-conversation="{{ $conversation_id }}">
    <div class="conv-sidebar-block-title">
        <span class="glyphicon glyphicon-tag" aria-hidden="true"></span>
        {{ __('Pretix Bookings') }}
    </div>
    <div id="pretix-orders-{{ $conversation_id }}" class="pretix-orders-container">
        <div class="pretix-loading">
            <span class="glyphicon glyphicon-refresh glyphicon-spin" aria-hidden="true"></span>
            {{ __('Loading bookings…') }}
        </div>
    </div>
</div>
