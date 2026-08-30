@extends('layouts.insight')
@section('title', 'FAQ')
@section('subTitle', 'FAQ')

@section('style')
@endsection

@section('content')
    <main class="main">
        <div class="page-header text-center" style="background-image: url('{{ asset('assets/images/page-header-bg.jpg') }}')">
            <div class="container">
                <h1 class="page-title">F.A.Q</h1>
            </div><!-- End .container -->
        </div><!-- End .page-header -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">FAQ</li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->

        {{-- Static for now — will move to the admin panel once that management
            screen is built; grouped so a future backend can map cleanly to
            "categories" of FAQ entries. --}}
        <div class="page-content">
            <div class="container">
                <h2 class="title text-center mb-3">Shipping Information</h2><!-- End .title -->
                <div class="accordion accordion-rounded" id="accordion-1">
                    <div class="card card-box card-sm bg-light">
                        <div class="card-header" id="heading-1">
                            <h2 class="card-title">
                                <a role="button" data-toggle="collapse" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1">
                                    How will my parcel be delivered?
                                </a>
                            </h2>
                        </div><!-- End .card-header -->
                        <div id="collapse-1" class="collapse show" aria-labelledby="heading-1" data-parent="#accordion-1">
                            <div class="card-body">
                                We ship every order to the address you provide at checkout. As soon as your order is placed, you'll get an order confirmation by email — and by SMS too, if you gave us a phone number — with your order number and details, so you always have a record of what's on the way.
                            </div><!-- End .card-body -->
                        </div><!-- End .collapse -->
                    </div><!-- End .card -->

                    <div class="card card-box card-sm bg-light">
                        <div class="card-header" id="heading-2">
                            <h2 class="card-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse-2" aria-expanded="false" aria-controls="collapse-2">
                                    Do I pay for delivery?
                                </a>
                            </h2>
                        </div><!-- End .card-header -->
                        <div id="collapse-2" class="collapse" aria-labelledby="heading-2" data-parent="#accordion-1">
                            <div class="card-body">
                                At checkout you can choose Free, Standard, or Express shipping — the cost of each option is shown before you pay, so there's never a surprise at the end. Some coupon codes also include free shipping.
                            </div><!-- End .card-body -->
                        </div><!-- End .collapse -->
                    </div><!-- End .card -->

                    <div class="card card-box card-sm bg-light">
                        <div class="card-header" id="heading-3">
                            <h2 class="card-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse-3" aria-expanded="false" aria-controls="collapse-3">
                                    Will I be charged customs fees?
                                </a>
                            </h2>
                        </div><!-- End .card-header -->
                        <div id="collapse-3" class="collapse" aria-labelledby="heading-3" data-parent="#accordion-1">
                            <div class="card-body">
                                No. SelfBuy currently ships within India only, so no customs duties or import fees apply — the price shown at checkout is exactly what you pay.
                            </div><!-- End .card-body -->
                        </div><!-- End .collapse -->
                    </div><!-- End .card -->

                    <div class="card card-box card-sm bg-light">
                        <div class="card-header" id="heading-4">
                            <h2 class="card-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse-4" aria-expanded="false" aria-controls="collapse-4">
                                    My item has become faulty
                                </a>
                            </h2>
                        </div><!-- End .card-header -->
                        <div id="collapse-4" class="collapse" aria-labelledby="heading-4" data-parent="#accordion-1">
                            <div class="card-body">
                                Get in touch through our <a href="{{ route('contact') }}">Contact page</a> with your order number and a description of the issue, and we'll sort it out — see our Refund Policy for how returns and replacements work.
                            </div><!-- End .card-body -->
                        </div><!-- End .collapse -->
                    </div><!-- End .card -->
                </div><!-- End .accordion -->

                <h2 class="title text-center mb-3">Orders and Returns</h2><!-- End .title -->
                <div class="accordion accordion-rounded" id="accordion-2">
                    <div class="card card-box card-sm bg-light">
                        <div class="card-header" id="heading2-1">
                            <h2 class="card-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse2-1" aria-expanded="false" aria-controls="collapse2-1">
                                    Tracking my order
                                </a>
                            </h2>
                        </div><!-- End .card-header -->
                        <div id="collapse2-1" class="collapse" aria-labelledby="heading2-1" data-parent="#accordion-2">
                            <div class="card-body">
                                If you have a SelfBuy account, sign in and go to <a href="{{ route('myaccount') }}">My Account &gt; Orders</a> to see every order you've placed and its current status. Checked out as a guest? Your order confirmation email has your order number — keep it handy in case you need to reach us.
                            </div><!-- End .card-body -->
                        </div><!-- End .collapse -->
                    </div><!-- End .card -->

                    <div class="card card-box card-sm bg-light">
                        <div class="card-header" id="heading2-2">
                            <h2 class="card-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse2-2" aria-expanded="false" aria-controls="collapse2-2">
                                    I haven't received my order
                                </a>
                            </h2>
                        </div><!-- End .card-header -->
                        <div id="collapse2-2" class="collapse" aria-labelledby="heading2-2" data-parent="#accordion-2">
                            <div class="card-body">
                                First check its status under <a href="{{ route('myaccount') }}">My Account &gt; Orders</a> (or your confirmation email if you checked out as a guest). If it's taking longer than expected, <a href="{{ route('contact') }}">contact us</a> with your order number and we'll look into it right away.
                            </div><!-- End .card-body -->
                        </div><!-- End .collapse -->
                    </div><!-- End .card -->

                    <div class="card card-box card-sm bg-light">
                        <div class="card-header" id="heading2-3">
                            <h2 class="card-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse2-3" aria-expanded="false" aria-controls="collapse2-3">
                                    How can I return an item?
                                </a>
                            </h2>
                        </div><!-- End .card-header -->
                        <div id="collapse2-3" class="collapse" aria-labelledby="heading2-3" data-parent="#accordion-2">
                            <div class="card-body">
                                <a href="{{ route('contact') }}">Contact us</a> with your order number and which item you'd like to return, and we'll walk you through it — see our Refund Policy for the full details of how returns are handled.
                            </div><!-- End .card-body -->
                        </div><!-- End .collapse -->
                    </div><!-- End .card -->
                </div><!-- End .accordion -->

                <h2 class="title text-center mb-3">Payments</h2><!-- End .title -->
                <div class="accordion accordion-rounded" id="accordion-3">
                    <div class="card card-box card-sm bg-light">
                        <div class="card-header" id="heading3-1">
                            <h2 class="card-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse3-1" aria-expanded="false" aria-controls="collapse3-1">
                                    What payment types can I use?
                                </a>
                            </h2>
                        </div><!-- End .card-header -->
                        <div id="collapse3-1" class="collapse" aria-labelledby="heading3-1" data-parent="#accordion-3">
                            <div class="card-body">
                                Cash on Delivery, or pay online by card, UPI, netbanking and more — SelfBuy supports Razorpay, Stripe, PayPal and Instamojo, so you can pick whichever works best for you at checkout.
                            </div><!-- End .card-body -->
                        </div><!-- End .collapse -->
                    </div><!-- End .card -->

                    <div class="card card-box card-sm bg-light">
                        <div class="card-header" id="heading3-2">
                            <h2 class="card-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse3-2" aria-expanded="false" aria-controls="collapse3-2">
                                    Can I pay by Gift Card?
                                </a>
                            </h2>
                        </div><!-- End .card-header -->
                        <div id="collapse3-2" class="collapse" aria-labelledby="heading3-2" data-parent="#accordion-3">
                            <div class="card-body">
                                Not yet. For now you can pay with Cash on Delivery or online through Razorpay, Stripe, PayPal or Instamojo — gift cards may be added down the line.
                            </div><!-- End .card-body -->
                        </div><!-- End .collapse -->
                    </div><!-- End .card -->

                    <div class="card card-box card-sm bg-light">
                        <div class="card-header" id="heading3-3">
                            <h2 class="card-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse3-3" aria-expanded="false" aria-controls="collapse3-3">
                                    I can't make a payment
                                </a>
                            </h2>
                        </div><!-- End .card-header -->
                        <div id="collapse3-3" class="collapse" aria-labelledby="heading3-3" data-parent="#accordion-3">
                            <div class="card-body">
                                Double-check your card, UPI, or account details and try again. If a payment fails partway through, no money is deducted (or it's refunded automatically by your bank), and your cart stays exactly as you left it so you can simply retry. Still stuck? <a href="{{ route('contact') }}">Contact us</a> and we'll help sort it out.
                            </div><!-- End .card-body -->
                        </div><!-- End .collapse -->
                    </div><!-- End .card -->

                    <div class="card card-box card-sm bg-light">
                        <div class="card-header" id="heading3-4">
                            <h2 class="card-title">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse3-4" aria-expanded="false" aria-controls="collapse3-4">
                                    Has my payment gone through?
                                </a>
                            </h2>
                        </div><!-- End .card-header -->
                        <div id="collapse3-4" class="collapse" aria-labelledby="heading3-4" data-parent="#accordion-3">
                            <div class="card-body">
                                You'll land on an order confirmation page and get a confirmation email (and SMS, if provided) the moment your payment succeeds. If a payment doesn't go through, you'll get a "payment not completed" email instead, and no order is created — your cart stays saved so you can try again.
                            </div><!-- End .card-body -->
                        </div><!-- End .collapse -->
                    </div><!-- End .card -->
                </div><!-- End .accordion -->
            </div><!-- End .container -->
        </div><!-- End .page-content -->

        <div class="cta cta-display bg-image pt-4 pb-4" style="background-image: url('{{ asset('assets/images/backgrounds/cta/bg-7.jpg') }}');">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-9 col-xl-7">
                        <div class="row no-gutters flex-column flex-sm-row align-items-sm-center">
                            <div class="col">
                                <h3 class="cta-title text-white">If You Have More Questions</h3><!-- End .cta-title -->
                                <p class="cta-desc text-white">Our team is happy to help — just reach out.</p><!-- End .cta-desc -->
                            </div><!-- End .col -->

                            <div class="col-auto">
                                <a href="{{ route('contact') }}" class="btn btn-outline-white"><span>CONTACT US</span><i class="icon-long-arrow-right"></i></a>
                            </div><!-- End .col-auto -->
                        </div><!-- End .row no-gutters -->
                    </div><!-- End .col-md-10 col-lg-9 -->
                </div><!-- End .row -->
            </div><!-- End .container -->
        </div><!-- End .cta -->
    </main>
@endsection

@section('script')
@endsection
