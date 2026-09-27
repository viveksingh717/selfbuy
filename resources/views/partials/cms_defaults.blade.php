{{--
    Starter content for the customer-service pages, shown only until the admin writes the page
    body in Admin > Pages. Store details come from System Settings. Review the policy wording
    (especially Terms and Privacy) before going live - it is a generic template, not legal advice.
--}}
@php
    $store   = setting('site_name', config('app.name', 'SelfBuy'));
    $email   = setting('support_email', setting('contact_email'));
    $phone   = setting('contact_phone');
    $process = setting('order_processing_time', '1 - 2 business days');
    $deliver = setting('delivery_time_estimate', '3 - 7 business days');
    $cutoff  = setting('order_cutoff_time');
    $cod     = (bool) setting('cod_enabled', true);
    $contact = collect([$email, $phone])->filter()->implode(' or ');
@endphp

@switch($slug)
    @case('payment-method')
        <p>We offer secure, convenient ways to pay for your {{ $store }} order.</p>
        <h3>Online payment</h3>
        <ul>
            <li>Credit and debit cards (Visa, Mastercard, RuPay and more)</li>
            <li>UPI (Google Pay, PhonePe, Paytm and any UPI app)</li>
            <li>Net banking and popular wallets</li>
        </ul>
        <p>Online payments are processed by trusted, PCI-DSS compliant payment gateways. {{ $store }} never sees or stores your full card details.</p>
        @if ($cod)
            <h3>Cash on Delivery</h3>
            <p>Prefer to pay when your order arrives? Choose <strong>Cash on Delivery</strong> at checkout and pay the courier in cash when you receive your package.</p>
        @endif
        <h3>Payment problems</h3>
        <p>If money was deducted but your order was not confirmed, don't worry - failed payments are normally reversed automatically by your bank. If it hasn't come back within a few days, contact us{{ $contact ? ' at ' . $contact : '' }} with your order details.</p>
        @break

    @case('shipping')
        <h3>Processing time</h3>
        <p>Orders are packed and handed to our courier partners within <strong>{{ $process }}</strong>.@if ($cutoff) Orders placed before <strong>{{ $cutoff }}</strong> on a business day are usually dispatched the same day.@endif</p>
        <h3>Delivery time</h3>
        <p>Once shipped, most orders arrive within <strong>{{ $deliver }}</strong>, depending on your location. Remote areas may take a little longer.</p>
        <h3>Shipping charges</h3>
        <p>Available shipping options and their charges are shown in your cart and at checkout before you pay.</p>
        <h3>Tracking your order</h3>
        <p>As soon as your order ships we email you the courier name and tracking number. You can also follow every step on the <a href="{{ route('track_order') }}">Track My Order</a> page.</p>
        @break

    @case('refund-policy')
        <h3>Returns</h3>
        <p>If you're not happy with an item, you can request a return within <strong>7 days of delivery</strong>. Items must be unused, in their original packaging, with all tags and accessories.</p>
        <p>For hygiene reasons some items (such as personal care and innerwear) can't be returned unless they arrive damaged or defective.</p>
        <h3>Damaged or wrong items</h3>
        <p>If your order arrives damaged, defective or different from what you ordered, contact us within 48 hours of delivery{{ $contact ? ' at ' . $contact : '' }} with your order number and a photo, and we'll arrange a replacement or refund.</p>
        <h3>Refunds</h3>
        <ul>
            <li>Once we receive and check the returned item, we process your refund within 5-7 business days.</li>
            <li>Online payments are refunded to the original payment method.</li>
            @if ($cod)
                <li>Cash on Delivery orders are refunded by bank transfer or UPI.</li>
            @endif
            <li>If an order is cancelled before it ships, any online payment is refunded in full.</li>
        </ul>
        @break

    @case('money-back-guarantee')
        <p>We want you to love what you buy from {{ $store }}. If you don't, our money-back guarantee has you covered.</p>
        <h3>How it works</h3>
        <ul>
            <li>Request a return within <strong>7 days of delivery</strong> - see our <a href="{{ route('refund_policy') }}">Returns &amp; Refunds</a> policy for eligible items.</li>
            <li>Send the item back unused and in its original packaging.</li>
            <li>We refund the full price of the item once it has been checked.</li>
        </ul>
        <h3>Damaged, defective or wrong items</h3>
        <p>If something arrives damaged or isn't what you ordered, we'll replace it or refund you in full - including any shipping charges.</p>
        <p>Questions? Contact us{{ $contact ? ' at ' . $contact : '' }} and we'll sort it out.</p>
        @break

    @case('terms-and-conditions')
        <p>By using the {{ $store }} website and placing an order, you agree to these terms.</p>
        <h3>Orders and pricing</h3>
        <ul>
            <li>All prices are in Indian Rupees (₹) and include applicable taxes unless stated otherwise.</li>
            <li>An order is confirmed once you receive our order confirmation email. We may cancel an order if an item is out of stock, if there is a pricing error, or if we suspect fraud - any payment made is then refunded in full.</li>
            <li>Coupons and offers are subject to their stated conditions and can't be exchanged for cash.</li>
        </ul>
        <h3>Your account</h3>
        <p>You're responsible for keeping your login details secure and for activity on your account. Please provide accurate contact and delivery information.</p>
        <h3>Shipping, returns and refunds</h3>
        <p>Deliveries follow our <a href="{{ route('shipping') }}">Shipping</a> policy, and returns and refunds follow our <a href="{{ route('refund_policy') }}">Returns &amp; Refunds</a> policy.</p>
        <h3>Changes</h3>
        <p>We may update these terms from time to time. The version on this page applies to orders placed after the date shown below.</p>
        @break

    @case('privacy-policy')
        <p>{{ $store }} respects your privacy. This policy explains what we collect and how we use it.</p>
        <h3>What we collect</h3>
        <ul>
            <li>Account and order details: your name, email, phone number and delivery address.</li>
            <li>Order history, cart and wishlist, so we can fulfil orders and personalise your experience.</li>
            <li>Basic technical data (such as cookies) needed to keep you signed in and the site secure.</li>
        </ul>
        <h3>How we use it</h3>
        <ul>
            <li>To process, ship and support your orders.</li>
            <li>To send order updates and, if you subscribe, our newsletter (you can unsubscribe at any time).</li>
            <li>To prevent fraud and keep your account secure.</li>
        </ul>
        <h3>Payments</h3>
        <p>Card and UPI payments are handled by our payment partners. We never store your full card details.</p>
        <h3>Sharing</h3>
        <p>We share your details only with the partners needed to deliver your order (payment gateways and courier companies). We never sell your personal data.</p>
        <h3>Your choices</h3>
        <p>You can update your details from <a href="{{ route('myaccount') }}">My Account</a>, or contact us{{ $contact ? ' at ' . $contact : '' }} to ask about or delete your data.</p>
        @break

    @default
        <p>Content for this page is coming soon. Please <a href="{{ route('contact') }}">contact us</a> if you have any questions.</p>
@endswitch
