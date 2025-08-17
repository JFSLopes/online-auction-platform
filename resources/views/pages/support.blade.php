<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Support - {{ config('app.name', 'Auction Bid It') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ url('css/support.css') }}">
</head>
<body>
    @include('partials.authTopBar')

    <div class="support-container">
        <h1>Support Center</h1>

        <div class="support-section" id="faq">
            <h2>FAQ</h2>
            <ul>
                <li><strong>How do I place a bid?</strong><br>Visit the item page, enter your bid amount, and click "Place Bid".</li>
                <li><strong>Can I cancel my bid?</strong><br>Bids are binding, but contact us for exceptional cases.</li>
                <li><strong>Is bidding free?</strong><br>Yes, but winning bids may include a buyer's premium and shipping costs.</li>
            </ul>
        </div>

        <div class="support-section" id="shipping">
            <h2>Shipping</h2>
            <p>We offer various shipping options depending on your location. Shipping costs are calculated during checkout, and tracking information will be provided once your order is shipped.</p>
        </div>

        <div class="support-section" id="returns">
            <h2>Returns</h2>
            <p>Returns are accepted within 14 days of receiving your item, provided it is in original condition. For more information, review our Returns Policy.</p>
        </div>

        <div class="support-section" id="payment-options">
            <h2>Payment Options</h2>
            <p>We accept the following payment methods:</p>
            <ul>
                <li>Credit/Debit Cards</li>
                <li>PayPal</li>
                <li>Bank Transfer</li>
            </ul>
            <p>All transactions are secure and encrypted.</p>
        </div>

        <div class="support-section" id="contact">
            <h2>Contact Us</h2>
            <p>Still have questions? Contact us and we'll be happy to assist you.</p>
            <p>For general inquiries, please email us at <a href="mailto:bidit@example.com">bidit@example.com</a></p>
            <p>Or contact us by phone at <a href="tel:+351912345678">912345678</a></p>
        </div>
    </div>

    @include('partials.footer')
</body>
</html>
