<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="favicon.ico" type="image/x-icon" />

    <title>Terms & Conditions</title>

    <!-- Bootstrap Core and vandor -->
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/bootstrap/css/bootstrap.min.css') }}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Core css -->
    <link rel="stylesheet" href="{{ asset('admin_assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin_assets/css/theme1.css') }}" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" title="SelfBuy">

</head>

<body class="font-montserrat">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-10 mx-auto">

                <div class="card shadow-sm">
                    <div class="card-body p-5">

                        <h1 class="mb-4">Terms & Conditions</h1>

                        <p class="text-muted">
                            Last Updated: {{ date('d F Y') }}
                        </p>

                        <p>
                            Welcome to <strong>SelfBuy</strong>. By accessing and using our website,
                            you agree to comply with and be bound by the following Terms and Conditions.
                            Please read them carefully before using our services.
                        </p>

                        <hr>

                        <h4>1. Acceptance of Terms</h4>
                        <p>
                            By using SelfBuy, you confirm that you are at least 18 years old or are
                            accessing the website under the supervision of a parent or legal guardian.
                        </p>

                        <h4>2. User Account</h4>
                        <p>By creating an account, you agree to abide by our platform's terms of service,
                            data usage policy, and acceptable use guidelines...
                        </p>
                        <p>
                            You are responsible for maintaining the confidentiality of your account
                            credentials and for all activities that occur under your account.
                        </p>

                        <ul>
                            <li>Provide accurate and complete information.</li>
                            <li>Keep your login credentials secure.</li>
                            <li>Notify us immediately of any unauthorized access.</li>
                        </ul>

                        <h4>3. Products and Pricing</h4>
                        <p>
                            All products displayed on SelfBuy are subject to availability.
                            We reserve the right to modify prices, discontinue products,
                            or limit quantities at any time without prior notice.
                        </p>

                        <h4>4. Orders and Payments</h4>
                        <p>
                            By placing an order, you agree that:
                        </p>

                        <ul>
                            <li>All information provided is accurate.</li>
                            <li>You are authorized to use the selected payment method.</li>
                            <li>Orders may be canceled if fraudulent activity is suspected.</li>
                        </ul>

                        <h4>5. Shipping and Delivery</h4>
                        <p>
                            Delivery times are estimates and may vary depending on location,
                            courier services, and unforeseen circumstances.
                        </p>

                        <h4>6. Returns and Refunds</h4>
                        <p>
                            Products may be eligible for return or refund according to our
                            Return and Refund Policy. Returned products must be unused and in
                            original packaging unless otherwise specified.
                        </p>

                        <h4>7. Prohibited Activities</h4>
                        <p>You agree not to:</p>

                        <ul>
                            <li>Use the website for unlawful purposes.</li>
                            <li>Attempt to gain unauthorized access to our systems.</li>
                            <li>Upload malicious software or harmful content.</li>
                            <li>Engage in fraudulent or deceptive activities.</li>
                        </ul>

                        <h4>8. Intellectual Property</h4>
                        <p>
                            All website content, including text, graphics, logos, images,
                            and software, is the property of SelfBuy and is protected by
                            applicable intellectual property laws.
                        </p>

                        <h4>9. Limitation of Liability</h4>
                        <p>
                            SelfBuy shall not be liable for any indirect, incidental,
                            special, or consequential damages arising from the use or inability
                            to use our services.
                        </p>

                        <h4>10. Privacy Policy</h4>
                        <p>
                            Your use of SelfBuy is also governed by our Privacy Policy.
                            Please review it to understand how we collect and use your information.
                        </p>

                        <h4>11. Changes to Terms</h4>
                        <p>
                            We reserve the right to update these Terms and Conditions at any time.
                            Continued use of the website after changes constitutes acceptance of
                            the revised terms.
                        </p>

                        <h4>12. Contact Information</h4>
                        <p>
                            If you have any questions regarding these Terms & Conditions,
                            please contact us:
                        </p>

                        <p>
                            <strong>Email:</strong> vivek.singh57@ymail.com<br>
                            <strong>Website:</strong> www.selfbuy.com
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('admin_assets/bundles/lib.vendor.bundle.js') }}"></script>
    <script src="{{ asset('admin_assets/js/core.js') }}"></script>

</body>

</html>
