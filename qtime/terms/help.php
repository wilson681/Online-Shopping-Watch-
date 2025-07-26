<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center</title>
    <link rel="stylesheet" href="../css/help.css">
    <script src="help.js"></script>
</head>
<body>
<?php include '../head.php'; ?>

    <div class="content-wrapper">
        <h1>How can we help you?</h1>

        <div class="help-search-container">
            <div class="search-input-wrapper">
                <input type="text" id="search-input" placeholder="Enter your question...">
            </div>
            <button class="search-button" onclick="searchQuestions()">Search</button>
        </div>

        <div class="search-results" id="search-results"></div>

        <div class="section">
            <div class="section-header" onclick="toggleSection('refund-policy')">Refund Policy</div>
            <div class="section-content" id="refund-policy">
                <p>To protect your rights, we only offer refunds under the following special circumstances. Please carefully read our refund policy to ensure your request meets the required conditions:</p>
                <ul>
                    <li>Damaged Products</li>
                    <li>Lost Shipment</li>
                </ul>
                <p>⚠ Non-Refundable Situations</p>
                <ul>
                    <li>Refunds cannot be provided for non-quality issues such as personal preference, incorrect size, or change of mind.</li>
                    <li>Minor packaging damage (e.g., slight dents or scratches) caused during transportation does not qualify for a refund if the product itself is intact.</li>
                    <li>Issues caused by improper use, accidental damage, or unauthorized disassembly do not qualify for a refund.</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <div class="section-header" onclick="toggleSection('payment-methods')">Payment Methods</div>
            <div class="section-content" id="payment-methods">
                <p>We offer a variety of secure and convenient payment options. You can choose the most suitable method for your needs:</p>
                <ul>
                    <li>Debit Card / Credit Card: We accept Visa and Mastercard credit and debit card payments to ensure safe and reliable transactions.</li>
                    <li>Touch 'n Go eWallet (TNG): You can use Touch 'n Go eWallet for fast and convenient payments.</li>
                </ul>
                <p>Please ensure your account balance is sufficient before making a payment to avoid transaction failures or order delays. If you encounter any payment issues, please contact our online customer support for assistance.</p>
            </div>
        </div>

        <div class="section">
            <div class="section-header" onclick="toggleSection('account-security')">Account Security</div>
            <div class="section-content" id="account-security">
                <p>To safeguard your account, we recommend the following measures to protect your personal information and transactions:</p>
                <ul>
                    <li>Navigate to "My Account" → "Security" to set up or update your security questions and answers for additional account protection.</li>
                    <li>To change your password, visit the "Security" section. We recommend updating your password regularly and using a strong password containing uppercase and lowercase letters, numbers, and special characters to prevent unauthorized access.</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <div class="section-header" onclick="toggleSection('profile-settings')">How to View and Edit Your Nickname and Profile Picture?</div>
            <div class="section-content" id="profile-settings">
                <p>Click the "My Account" icon in the upper right corner of the page to access your personal profile.</p>
                <p>Under the "Profile" section, you can update your nickname and profile picture and click "Save Changes" to apply the updates.</p>
                <p>Supported profile picture formats include JPEG and PNG. For the best display quality, please upload a high-resolution image.</p>
            </div>
        </div>

        <div class="section">
            <div class="section-header" onclick="toggleSection('order-delivery')">Order Delivery Time</div>
            <div class="section-content" id="order-delivery">
                <p>After successful payment, we typically process and ship orders within 3-5 business days to ensure timely delivery.</p>
                <p>If your shipping address is in a remote area, delivery may be delayed by an additional 1-2 days, depending on local logistics conditions.</p>
                <p>During holidays, promotional events, or peak seasons, processing and delivery times may be extended due to high order volumes. We will do our best to ship orders as soon as possible and appreciate your patience and understanding.</p>
            </div>
        </div>

        <div class="section">
            <div class="section-header" onclick="toggleSection('track-shipment')">How to Track Your Shipment?</div>
            <div class="section-content" id="track-shipment">
                <p>Once your order has been shipped, we will provide you with a tracking number via SMS or email.</p>
                <p>You can check the status of your shipment by entering the tracking number on the "My Orders" → "Track Order" page.</p>
                <p>If your order shows no tracking updates for an extended period, please contact our customer service team. We will work with the courier company to ensure your package is delivered successfully.</p>
            </div>
        </div>

        <div class="section">
            <div class="section-header" onclick="toggleSection('modify-address')">How to Modify Your Address?</div>
            <div class="section-content" id="modify-address">
                <p>If you need to change your shipping address, go to "My Account" → "Address" and update your address information.</p>
                <p>Please confirm that your address details are correct before placing an order to ensure successful delivery.</p>
                <p>Once an order has entered the shipping process, we are unable to change the address. We appreciate your understanding.</p>
            </div>
        </div>
    </div>
    <?php include '../foot.php'; ?>
</body>
</html>