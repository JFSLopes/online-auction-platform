<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/about.css')}}">
    <script src="{{asset('js/about.js')}}" defer></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
    <title>About Us</title>
</head>
<body>
        @include('partials.authTopBar')
    <main>
        <section id="about">
            <h1>About Us</h1>
            <p>Welcome to Bid It, your premier online auction platform! We offer an exciting marketplace where buyers and sellers connect to bid on a wide variety of items, from collectibles to electronics, rare antiques to everyday goods. Whether you're a seasoned bidder or just starting, Bid It makes it easy and fun to engage in the thrill of auctions.</p>
            <p>Our mission is to create a dynamic and secure environment where anyone can participate in the auction process. We strive to offer transparent bidding, a wide selection of items, and unbeatable customer support. Every item has a unique story, and with each bid, you have the opportunity to own something extraordinary at a price you’re comfortable with.</p>
            <p>Join Bid It today, and experience the excitement of online auctions with a community of passionate buyers and sellers.</p>
        </section>

            <section id="services">
                <h1>Our Services</h1>
                <ul>
                    <li>We provide a safe and secure platform for users to participate in real-time auctions for a variety of items.</li>
                    <li>Our website allows you to list your own items for auction or browse through ongoing auctions to place bids.</li>
                    <li>We offer a reliable payment and shipping system to ensure a smooth transaction once the auction is complete.</li>
                    <li>Our dedicated customer support team is always available to assist you with any questions or concerns during the bidding process.</li>
                </ul>
            </section>
            
            <section id="privacy-policy">
                <h1>Privacy Policy</h1>
                <p>Last Updated: 17/05/2024</p>
            
                <p>Thank you for using Bid It. This Privacy Policy outlines how we collect, use, disclose, and protect your personal information when you use our website and services. By accessing or using Bid It, you agree to the terms of this Privacy Policy.</p>
            
                <h3>Information Collection and Use:</h3>
                <p>We collect personal information such as your name, email address, mailing address, phone number, payment details, and any other data you provide when registering on our platform or participating in auctions. This information is used to process your bids, transactions, and to improve your experience on our site.</p>
            
                <h3>Information Sharing:</h3>
                <p>We do not sell, trade, or transfer your personal information to third parties without your consent, except when required for the auction process or to comply with legal obligations. We may share your information with trusted third-party service providers, such as payment processors or shipping partners, who help us operate our site and complete transactions.</p>
            
                <h3>Data Security:</h3>
                <p>We implement industry-standard security measures to protect your personal information from unauthorized access, alteration, or destruction. However, no method of transmission or electronic storage is completely secure, and we cannot guarantee absolute security.</p>
            
                <h3>Cookies:</h3>
                <p>We may use cookies and similar technologies to improve your experience on our website. You can choose to accept or decline cookies through your browser settings. Please note that if you disable cookies, some features of the site may not function correctly.</p>
            
                <h3>Children's Privacy:</h3>
                <p>Bid It is not intended for individuals under the age of 18. We do not knowingly collect or solicit personal information from anyone under 18. If we become aware that we have collected such information, we will take steps to delete it.</p>
            
                <h3>Changes to This Privacy Policy:</h3>
                <p>We may update this Privacy Policy from time to time. Any changes will be posted on this page, and the updated date will be indicated. We encourage you to review this policy periodically.</p>
            
                <h3>Contact Us:</h3>
                <p>If you have any questions or concerns about this Privacy Policy, please contact us at <a href="mailto:support@bidit.com">support@bidit.com</a>.</p>
            
                <p>By using our website, you consent to the collection and use of your personal information as described in this Privacy Policy.</p>
            </section>
            
            <section id="affiliate-program">
                <h1>Affiliate Program</h1>
                <p>Want to earn money while sharing your love for auctions? Join our Bid It Affiliate Program today! As an affiliate, you'll earn commissions for every new user you refer who places a bid or wins an auction on our platform. It's simple – sign up, share your unique referral link, and earn money as your friends and followers explore exciting online auctions.</p>
                <p>Whether you're a blogger, social media influencer, or auction enthusiast, our affiliate program offers an easy way to monetize your network. Start earning commissions while helping others discover the fun and excitement of Bid It!</p>
            </section>

            <section id="location">
                <h1>Our Location</h1>
                <p>We are located at the heart of the city. Feel free to visit us anytime. Here's where you can find us:</p>
                
                <address>
                    <strong>Bid It Office</strong><br>
                    <div>Rua Adelino da Costa Campos, 4760-719 Ribeirão</div>
                </address>

                <?php
            function geocodeAddress($address) {

                $apiKey = getenv('LOCATION_IQ_KEY');

                $url = "https://us1.locationiq.com/v1/search.php?key=" . urlencode($apiKey) . "&q=" . urlencode($address) . "&format=json";

                $ch = curl_init();
            
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
                $response = curl_exec($ch);
            
                if ($response === false) {
                    echo "cURL Error: " . curl_error($ch);
                    curl_close($ch);
                    return null;
                }
            
                curl_close($ch);
        
                $data = json_decode($response, true);
            
                if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                    $lat = $data[0]['lat'];
                    $lon = $data[0]['lon'];
                    return ['lat' => $lat, 'lon' => $lon];
                } else {
                    return ['lat' => -82.8628, 'lon' => 135.0000];
                }
            }
        ?>
            
                <div class="map-container">
                    @php
                        $address = "Rua Adelino da Costa Campos, 4760-719 Ribeirão Portugal";
                        $coordinates = geocodeAddress($address);
                    @endphp
                    <input type="hidden" id="latitudine-hidden" value ="{{$coordinates['lat']}}"></hidden>
                    <input type="hidden" id="longitudine-hidden" value="{{$coordinates['lon']}}"></hidden>
                    <div id="map" style="height: 400px;"></div>
                </div>
            
            </section>
            

        </main>
            
        @include('partials.footer')
</body>
</html>