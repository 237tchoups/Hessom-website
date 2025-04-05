<?php
// Include database connection and functions
require_once 'db_connect.php';
require_once 'functions.php';

// Get products
$dialysisProducts = getProductsByCategory('dialysis');
$consumableProducts = getProductsByCategory('consumable');

// Get notifications
$notificationsData = json_decode(file_get_contents('get_notifications.php'), true);
$notifications = $notificationsData['notifications'] ?? [];
$notificationCount = $notificationsData['unreadCount'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title id="page-title">Hessom Medical Products Enterprise</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      background-color: #104e8b;
    }
    header {
      background-color: #104e8b;
      color: white;
      padding: 2px;
      text-align: center;
    }
    nav {
      display: flex;
      justify-content: right;
      background-color: #08501e;
      padding: 10px;
    }
    nav a {
      color: #fff;
      margin: 0 20px;
      text-decoration: none;
      font-weight: bold;
      cursor: pointer;
    }
    nav a:hover {
      text-decoration: underline;
    }
    .banner, .products, .about-us, .contact-us {
      background-image: url('TYPE.webp');
      background-size: cover;
      background-position: center;
      color: rgb(0, 3, 1);
      padding: 100px 2px;
      text-align: center;
    }
    .home-images {
      display: flex;
      justify-content: center;
      gap: 20px;
      padding: 20px;
    }
    .home-images img {
      width: 250px;
      height: 200px;
      object-fit: cover;
      border-radius: 10px;
    }
    .products {
      padding: 40px 20px;
    }
    table {
      width: 90%;
      margin: 20px auto;
      border-collapse: collapse;
    }
    table, th, td {
      border: 5px solid #02050d;
    }
    th, td {
      padding: 10px;
      text-align: center;
      background-color: #edeff2;
    }
    th {
      background-color: #1f5493;
      color: #04080f;
    }
    .product-image {
      width: 150px;
      height: 100px;
      object-fit: cover;
      border-radius: 5px;
    }
    #order-form {
      margin: 20px auto;
      width: 90%;
      text-align: center;
    }
    #order-form input {
      padding: 5px;
      margin: 5px;
    }
    .social-icons a {
      font-size: 35px;
      color: #0056b3;
      margin-right: 10px;
      text-decoration: none;
    }
    .social-icons a:hover {
      color: #008234;
    }
    footer {
      background-color: #08501e;
      color: white;
      text-align: center;
      padding: 20px;
      margin-top: 90px;
    }
    /* Modal styles for payment */
    #payment-modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      overflow-y: auto;
    }
    #payment-modal > div {
      background-color: white;
      margin: 5% auto;
      padding: 20px;
      border-radius: 8px;
      width: 80%;
      max-width: 500px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
    .payment-header {
      background-color: #104e8b;
      color: white;
      padding: 15px;
      margin: -20px -20px 20px -20px;
      border-radius: 8px 8px 0 0;
      text-align: center;
      font-size: 18px;
      font-weight: bold;
    }
    /* Receipt modal styles - FULL SCREEN */
    #receipt-modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: #f5f5f5;
      overflow-y: auto;
    }
    #receipt-modal > div {
      background-color: white;
      margin: 0;
      padding: 20px;
      width: 100%;
      min-height: 100vh;
      box-sizing: border-box;
    }
    /* Input styling */
    .input-group {
      margin-bottom: 15px;
    }
    .input-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }
    .input-group input, .input-group select {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    button {
      padding: 10px 15px;
      background-color: #104e8b;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin: 5px;
    }
    button:hover {
      background-color: #0d3d6e;
    }
    button:disabled {
      background-color: #cccccc;
      cursor: not-allowed;
    }
    
    /* Receipt Styles */
    .receipt {
      border: 1px solid #ddd;
      padding: 20px;
      margin: 0 auto;
      max-width: 1000px;
      background-color: #fff;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .receipt-header {
      display: flex;
      justify-content: space-between;
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }
    .receipt-title {
      font-size: 28px;
      font-weight: bold;
      color: #104e8b;
    }
    .receipt-date {
      color: #666;
      font-size: 16px;
    }
    .hospital-info {
      margin-bottom: 20px;
    }
    .receipt-table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }
    .receipt-table, .receipt-table th, .receipt-table td {
      border: 1px solid #ddd;
    }
    .receipt-table th {
      background-color: #f2f2f2;
      padding: 12px;
      text-align: left;
    }
    .receipt-table td {
      padding: 12px;
    }
    .total-row {
      font-weight: bold;
      background-color: #f9f9f9;
    }
    .receipt-footer {
      margin-top: 30px;
      text-align: center;
      color: #666;
      font-size: 14px;
      border-top: 1px solid #eee;
      padding-top: 20px;
    }
    /* Order item styles */
    .order-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px;
      border-bottom: 1px solid #eee;
      margin-bottom: 5px;
    }
    .order-item-text {
      flex-grow: 1;
      text-align: left;
    }
    .delete-btn {
      background-color: #e74c3c;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 5px 10px;
      cursor: pointer;
      margin-left: 10px;
    }
    .delete-btn:hover {
      background-color: #c0392b;
    }
    /* Payment info styles */
    .payment-info {
      background-color: #f8f9fa;
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 15px;
      margin: 15px 0;
    }
    .payment-info h4 {
      color: #104e8b;
      margin-top: 0;
    }
    .highlight {
      color: #104e8b;
      font-weight: bold;
    }
    .payment-summary {
      display: flex;
      justify-content: space-between;
      margin: 10px 0;
      padding: 5px 0;
      border-bottom: 1px dashed #ddd;
    }
    /* Receipt actions bar */
    .receipt-actions {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background-color: #104e8b;
      padding: 15px;
      text-align: center;
      box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }
    .receipt-actions button {
      margin: 0 10px;
      padding: 12px 25px;
      font-size: 16px;
      border-radius: 5px;
    }
    .receipt-actions button.print-button {
      background-color: #2ecc71;
    }
    .receipt-actions button.download-button {
      background-color: #3498db;
    }
    .receipt-actions button.close-button {
      background-color: #7f8c8d;
    }
    .company-logo {
      max-width: 150px;
      margin-bottom: 15px;
    }
    .company-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .company-details {
      text-align: right;
    }
    /* Payment form styles */
    .payment-form-section {
      display: none;
      margin-top: 15px;
    }
    .payment-form-section.active {
      display: block;
    }
    .payment-tabs {
      display: flex;
      margin-bottom: 20px;
      border-bottom: 1px solid #ddd;
    }
    .payment-tab {
      padding: 10px 20px;
      cursor: pointer;
      background-color: #f5f5f5;
      border: 1px solid #ddd;
      border-bottom: none;
      margin-right: 5px;
      border-radius: 5px 5px 0 0;
    }
    .payment-tab.active {
      background-color: #104e8b;
      color: white;
      border-color: #104e8b;
    }
    /* Credit Card Form Styles */
    .card-row {
      display: flex;
      gap: 15px;
    }
    .card-row .input-group {
      flex: 1;
    }
    .card-number-container {
      position: relative;
    }
    .card-type-icon {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 24px;
      color: #666;
    }
    /* Credit card preview */
    .credit-card-preview {
      width: 100%;
      height: 200px;
      background: linear-gradient(135deg, #0f4c81 0%, #1a6baa 100%);
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
      color: white;
      position: relative;
      overflow: hidden;
      margin-bottom: 30px;
      box-sizing: border-box;
    }
    .card-chip {
      width: 50px;
      height: 40px;
      background-color: #e6c35c;
      border-radius: 8px;
      margin-bottom: 20px;
      position: relative;
    }
    .card-chip::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 30px;
      height: 20px;
      background-color: rgba(255,255,255,0.2);
      border-radius: 4px;
    }
    .card-number-display {
      font-size: 20px;
      letter-spacing: 2px;
      margin-bottom: 20px;
      font-family: monospace;
    }
    .card-details {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
    }
    .card-holder {
      text-transform: uppercase;
    }
    .card-expiry {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
    }
    .card-expiry-label {
      font-size: 10px;
      margin-bottom: 5px;
    }
    .card-logo {
      position: absolute;
      bottom: 20px;
      right: 20px;
      font-size: 30px;
    }
    @media print {
      .no-print {
        display: none;
      }
      body * {
        visibility: hidden;
      }
      #receipt-to-print, #receipt-to-print * {
        visibility: visible;
      }
      #receipt-to-print {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
      }
      .receipt {
        border: none;
        box-shadow: none;
      }
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<header>
  <img id="header-logo" src="capture.jpg2.jpg" alt="Hessom Medical Supplies Logo" style="display: block; margin: left; width: 150px;">
  <h1 id="header-title"><marquee>Hessom Medical Supplies Company for Hospitals</marquee></h1>
  <nav> 
    <a onclick="showSection('Home')">Home</a>
    <a onclick="showSection('products')">Products</a>
    <a onclick="showSection('about-us')">About Us</a>
    <a onclick="showSection('contact-us')">Contact</a>
    <div id="notification-bar" style="margin-left: 20px; color: yellow; font-weight: bold; cursor: pointer;" onclick="showNotifications()">
      Notifications (<span id="notification-count"><?php echo $notificationCount; ?></span>)
    </div>
  </nav>
</header>

<div id="notifications" style="display: none; background: white; color: black; padding: 10px; border: 1px solid black; max-width: 300px; position: absolute; top: 50px; right: 20px; z-index: 100;">
  <h4>Notifications</h4>
  <ul id="notification-list" style="list-style-type: none; padding: 0;">
    <?php foreach ($notifications as $notification): ?>
      <li><?php echo htmlspecialchars($notification['title'] . ': ' . $notification['message']); ?></li>
    <?php endforeach; ?>
  </ul>
  <button onclick="clearNotifications()">Clear Notifications</button>
</div>

<div class="banner">
  <h1 id="banner-title">Providing Quality Medical Supplies to Hospitals</h1>
  <p id="banner-description">Your trusted partner in healthcare solutions.</p>
</div>

<section id="Home" class="Home">
  <div class="home-images">
    <img src="hos bed.jpg" alt="Hospital Bed">
    <img src="med.glove.webp" alt="Medical Gloves">
    <img src="stepcop.webp" alt="Stethoscope">
  </div>
</section>

<section id="products" class="products">
  <h2>Product Catalog</h2>
  <p><h2>Dialysis Products</h2></p>
  <table id="dialysis-products-table">
    <thead>
      <tr>
        <th>Image</th>
        <th>Product Name</th>
        <th>Designation</th>
        <th>Packaging</th>
        <th>Quantity (Pieces)</th>
        <th>Description</th>
        <th>Expiration Date</th>
        <th>Price (FCFA)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($dialysisProducts as $product): ?>
      <tr id="product-<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>" data-designations="<?php echo htmlspecialchars($product['designation']); ?>">
        <td><img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image"></td>
        <td><?php echo htmlspecialchars($product['name']); ?></td>
        <td><?php echo htmlspecialchars($product['designation']); ?></td>
        <td><?php echo htmlspecialchars($product['packaging']); ?></td>
        <td><?php echo htmlspecialchars($product['quantity']); ?></td>
        <td><?php echo htmlspecialchars($product['description']); ?></td>
        <td><?php echo htmlspecialchars($product['expiration_date']); ?></td>
        <td><?php echo number_format($product['price']); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h2>Consumable Products</h2>
  <table id="consumable-products-table" border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; text-align: center;">
    <thead style="background-color: #f2f2f2;">
      <tr>
        <th>Image</th>
        <th>Product Name</th>
        <th>Designation</th>
        <th>Packaging</th>
        <th>Quantity (Pieces)</th>
        <th>Description</th>
        <th>Expiration Date</th>
        <th>Price (FCFA)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($consumableProducts as $product): ?>
      <tr id="product-<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>" data-designations="<?php echo htmlspecialchars($product['designation']); ?>">
        <td><img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image" width="80"></td>
        <td><?php echo htmlspecialchars($product['name']); ?></td>
        <td><?php echo htmlspecialchars($product['designation']); ?></td>
        <td><?php echo htmlspecialchars($product['packaging']); ?></td>
        <td><?php echo htmlspecialchars($product['quantity']); ?></td>
        <td><?php echo htmlspecialchars($product['description']); ?></td>
        <td><?php echo htmlspecialchars($product['expiration_date']); ?></td>
        <td><?php echo number_format($product['price']); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  
  <h3>Place Your Order</h3>
  <div class="container">
    <h2>Product Catalog</h2>
  
    <div class="input-group">
      <label for="hospitalName">Hospital Name:</label>
      <input type="text" id="hospitalName" required oninput="validateOrder()">
    </div>
  
    <div class="input-group">
      <label for="hospitalLocation">Hospital Location:</label>
      <input type="text" id="hospitalLocation" required oninput="validateOrder()">
    </div>
  
    <div id="order-form">
      <label for="product-name">Product Name:</label>
      <select id="product-name" onchange="updateDesignations()">
        <option value="">Select a Product</option>
        <?php 
        // Combine both product arrays for the dropdown
        $allProducts = array_merge($dialysisProducts, $consumableProducts);
        foreach ($allProducts as $product): 
        ?>
        <option value="product-<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>" data-designations="<?php echo htmlspecialchars($product['designation']); ?>"><?php echo htmlspecialchars($product['name']); ?></option>
        <?php endforeach; ?>
      </select>

      <label for="product-designation">Designation (Form):</label>
      <select id="product-designation" disabled>
        <option value="">Select Designation</option>
      </select>

      <label for="product-quantity">Quantity:</label>
      <input type="number" id="product-quantity" min="1" required>

      <button type="button" onclick="addToOrder()">Add to Order</button>

      <h4>Order Summary:</h4>
      <div id="order-summary"></div>
      <h4>Total Price: <span id="total-price">0</span> FCFA</h4>
      <div class="payment-info">
        <h4>50% Upfront Payment Policy</h4>
        <p>We offer a flexible payment option where you only need to pay 50% of the total amount upfront.</p>
        <p>The remaining balance will be due upon delivery or as agreed in the payment terms.</p>
        <div class="payment-summary">
          <span>Total Order Value:</span>
          <span id="full-price">0 FCFA</span>
        </div>
        <div class="payment-summary">
          <span>Required Upfront Payment (50%):</span>
          <span id="half-price" class="highlight">0 FCFA</span>
        </div>
        <div class="payment-summary">
          <span>Remaining Balance (50%):</span>
          <span id="remaining-balance">0 FCFA</span>
        </div>
      </div>

      <button id="orderNow" disabled onclick="showPaymentModal()">Order Now</button>
    </div>
  </div>
  
  <!-- Payment Modal -->
  <div id="payment-modal">
    <div>
      <div class="payment-header">
        Payment - 50% Upfront
      </div>
      <p>Please select your payment method and enter the required details to proceed with the 50% upfront payment:</p>
      <div class="payment-info">
        <div class="payment-summary">
          <span>Total Order Value:</span>
          <span id="modal-full-price">0 FCFA</span>
        </div>
        <div class="payment-summary">
          <span>Required Upfront Payment (50%):</span>
          <span id="modal-half-price" class="highlight">0 FCFA</span>
        </div>
      </div>
      
      <!-- Payment Method Tabs -->
      <div class="payment-tabs">
        <div class="payment-tab active" onclick="switchPaymentMethod('mobile-money')">
          <i class="fas fa-mobile-alt"></i> Mobile Money
        </div>
        <div class="payment-tab" onclick="switchPaymentMethod('credit-card')">
          <i class="far fa-credit-card"></i> Credit Card
        </div>
      </div>
      
      <!-- Mobile Money Payment Form -->
      <div id="mobile-money-payment" class="payment-form-section active">
        <div class="input-group">
          <label for="mobile-money-provider">Choose Mobile Money Provider:</label>
          <select id="mobile-money-provider">
            <option value="">Select Provider</option>
            <option value="mtn">MTN Mobile Money</option>
            <option value="orange">Orange Mobile Money</option>
          </select>
        </div>
        <div class="input-group">
          <label for="mobile-money-phone">Phone Number:</label>
          <input type="text" id="mobile-money-phone" placeholder="Enter phone number" required>
        </div>
        <div class="input-group">
          <label for="mobile-money-code">Mobile Money Code:</label>
          <input type="text" id="mobile-money-code" placeholder="Enter Mobile Money Code" required>
        </div>
      </div>
      
      <!-- Credit Card Payment Form -->
      <div id="credit-card-payment" class="payment-form-section">
        <!-- Credit Card Preview -->
        <div class="credit-card-preview">
          <div class="card-chip"></div>
          <div class="card-number-display" id="card-number-display">•••• •••• •••• ••••</div>
          <div class="card-details">
            <div class="card-holder">
              <div class="card-holder-label">Card Holder</div>
              <div id="card-holder-display">YOUR NAME</div>
            </div>
            <div class="card-expiry">
              <div class="card-expiry-label">Expires</div>
              <div id="card-expiry-display">MM/YY</div>
            </div>
          </div>
          <div class="card-logo" id="card-logo">
            <i class="far fa-credit-card"></i>
          </div>
        </div>
        
        <div class="input-group">
          <label for="card-holder-name">Cardholder Name:</label>
          <input type="text" id="card-holder-name" placeholder="Name on card" required oninput="updateCardPreview()">
        </div>
        
        <div class="input-group card-number-container">
          <label for="card-number">Card Number:</label>
          <input type="text" id="card-number" placeholder="1234 5678 9012 3456" maxlength="19" required oninput="formatCardNumber(this); updateCardPreview(); detectCardType(this.value)">
          <span class="card-type-icon" id="card-type-icon"><i class="far fa-credit-card"></i></span>
        </div>
        
        <div class="card-row">
          <div class="input-group">
            <label for="expiry-date">Expiry Date:</label>
            <input type="text" id="expiry-date" placeholder="MM/YY" maxlength="5" required oninput="formatExpiryDate(this); updateCardPreview()">
          </div>
          
          <div class="input-group">
            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" placeholder="123" maxlength="4" required>
          </div>
        </div>
      </div>
      
      <div style="margin-top: 20px;">
        <button type="button" onclick="verifyPayment()">Pay Now</button>
        <button type="button" onclick="closePaymentModal()">Cancel</button>
      </div>
    </div>
  </div>
  
  <!-- Receipt Modal - Full Screen -->
  <div id="receipt-modal">
    <div>
      <div id="receipt-to-print">
        <div class="receipt">
          <div class="company-header">
            <div>
              <img src="capture.jpg2.jpg" alt="Hessom Medical Supplies Logo" class="company-logo">
              <div class="receipt-title">OFFICIAL RECEIPT</div>
            </div>
            <div class="company-details">
              <div class="receipt-date" id="receipt-date"></div>
              <div>Receipt #: <span id="receipt-order-number"></span></div>
            </div>
          </div>

          <div class="company-info">
            <h3>Hessom Medical Products Enterprise</h3>
            <p>Location: Cameroun, Douala-bonanjo beside the NFC BANK</p>
            <p>P.O. Box: 2540</p>
            <p>Phone: (237) 691 26 66 01 / 697 00 48 91 / 698 10 00 00</p>
            <p>NIU: MO31913914380Q | Email: hessom.medical@gmail.com</p>
          </div>

          <div class="hospital-info">
            <h3>Hospital Information</h3>
            <p><strong>Hospital Name:</strong> <span id="receipt-hospital-name"></span></p>
            <p><strong>Hospital Location:</strong> <span id="receipt-hospital-location"></span></p>
            <p><strong>Order Number:</strong> <span id="receipt-order-number-display"></span></p>
            <p><strong>Date:</strong> <span id="receipt-order-date"></span></p>
          </div>

          <h3>Order Summary</h3>
          <table class="receipt-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Designation</th>
                <th>Quantity</th>
                <th>Unit Price (FCFA)</th>
                <th>Total (FCFA)</th>
              </tr>
            </thead>
            <tbody id="receipt-product-table-body">
              <!-- Products will be added here dynamically -->
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" style="text-align: right;"><strong>Total Order Value:</strong></td>
                <td id="receipt-total-price">0 FCFA</td>
              </tr>
              <tr class="total-row">
                <td colspan="4" style="text-align: right;"><strong>Paid Amount (50%):</strong></td>
                <td id="receipt-paid-amount">0 FCFA</td>
              </tr>
              <tr>
                <td colspan="4" style="text-align: right;"><strong>Remaining Balance (50%):</strong></td>
                <td id="receipt-remaining-balance">0 FCFA</td>
              </tr>
            </tfoot>
          </table>

          <div class="payment-info">
            <h4>Payment Information</h4>
            <p><strong>Payment Method:</strong> <span id="receipt-payment-method"></span></p>
            <p id="receipt-phone-number-container"><strong>Phone Number:</strong> <span id="receipt-phone-number"></span></p>
            <p id="receipt-card-info-container" style="display: none;"><strong>Card Number:</strong> <span id="receipt-card-number"></span></p>
            <p><strong>Transaction ID:</strong> <span id="receipt-transaction-id"></span></p>
            <p><strong>Payment Status:</strong> <span class="highlight">50% Paid</span></p>
            <p><strong>Payment Date:</strong> <span id="receipt-payment-date"></span></p>
          </div>

          <div class="payment-info">
            <h4>Remaining Balance Payment</h4>
            <p>The remaining 50% balance of <span id="receipt-balance-due">0 FCFA</span> is due upon delivery.</p>
            <p>For any inquiries regarding your payment, please contact our finance department.</p>
          </div>

          <div class="receipt-footer">
            <p>Thank you for your order!</p>
            <p>For any inquiries, please contact us at hessom.medical@gmail.com</p>
          </div>
        </div>
      </div>
      
      <div class="receipt-actions no-print">
        <button onclick="printReceipt()" class="print-button"><i class="fas fa-print"></i> Print Receipt</button>
        <button onclick="downloadReceipt()" class="download-button"><i class="fas fa-download"></i> Download Receipt</button>
        <button onclick="closeReceiptModal()" class="close-button"><i class="fas fa-times"></i> Close</button>
      </div>
    </div>
  </div>
</section>

<section id="about-us" class="about-us">
  <h2>About Us</h2>
  <p id="about-us-content">Hessom Medical Supplies Enterprise is a trusted provider of high-quality medical 
    equipment and supplies. We serve hospitals, clinics, and healthcare professionals 
    with reliable and affordable products.</p>
</section>

<section id="contact-us" class="contact-us">
  <h2>Contact Us</h2>
  <div class="contact-info">
    <p>Location: Cameroun, Douala-bonanjo beside the NFC BANK, P.O. Box: 2540</p>
    <p>Phone: (237) 691 26 66 01 / 697 00 48 91 / 698 10 00 00</p>
    <p>NIU: MO31913914380Q | Email: hessom.medical@gmail.com</p>
    <p>Follow us on:</p>
    <div class="social-icons">
      <a href="https://wa.me/123456789" target="_blank"><i class="fab fa-whatsapp"></i></a>
      <a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook"></i></a>
      <a href="https://www.twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
      <a href="https://www.instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
    </div>
  </div>
</section>

<footer>
  <p>&copy; 2025 Hessom Medical Products Enterprise</p>
</footer>

<script>
  // Show or hide sections based on the clicked navigation link
  function showSection(sectionId) {
    document.querySelectorAll('section').forEach(section => {
      section.style.display = 'none';
    });
    document.getElementById(sectionId).style.display = 'block';
  }
  showSection('Home');

  // Check for content updates from admin panel
  window.addEventListener('load', function() {
    // Set current date for receipt
    const now = new Date();
    document.getElementById('receipt-date').textContent = formatDate(now);
    document.getElementById('receipt-order-date').textContent = formatDate(now);
    document.getElementById('receipt-payment-date').textContent = formatDate(now);
    
    // Set mobile money as default payment method
    document.getElementById('mobile-money-payment').style.display = 'block';
  });

  // Product ordering functions
  let totalPrice = 0;
  let orderItems = [];
  let orderId = null;
  let orderNumber = null;

  function updateDesignations() {
    let productSelect = document.getElementById("product-name");
    let designationSelect = document.getElementById("product-designation");
    let selectedOption = productSelect.options[productSelect.selectedIndex];

    designationSelect.innerHTML = '<option value="">Select Designation</option>';
    if (selectedOption.value) {
      selectedOption.getAttribute("data-designations").split(" / ").forEach(designation => {
        let option = document.createElement("option");
        option.value = designation;
        option.textContent = designation;
        designationSelect.appendChild(option);
      });
      designationSelect.disabled = false;
    } else {
      designationSelect.disabled = true;
    }
  }

  function addToOrder() {
    let productSelect = document.getElementById("product-name");
    let designationSelect = document.getElementById("product-designation");
    let quantityInput = document.getElementById("product-quantity");

    let selectedOption = productSelect.options[productSelect.selectedIndex];
    let designation = designationSelect.value;
    let quantity = parseInt(quantityInput.value);

    if (!selectedOption.value || !designation || quantity < 1) {
      alert("Please select a product, designation, and enter a valid quantity.");
      return;
    }

    let price = parseInt(selectedOption.getAttribute("data-price"));
    let totalProductPrice = price * quantity;
    totalPrice += totalProductPrice;

    // Extract product ID from the value (format: product-X)
    const productId = selectedOption.value.replace('product-', '');

    // Add to order items array for receipt generation
    const itemIndex = orderItems.length;
    orderItems.push({
      productId: productId,
      name: selectedOption.text,
      designation: designation,
      quantity: quantity,
      unitPrice: price,
      totalPrice: totalProductPrice
    });

    // Create order item with delete button
    let orderSummary = document.getElementById("order-summary");
    let itemDiv = document.createElement("div");
    itemDiv.className = "order-item";
    itemDiv.id = `order-item-${itemIndex}`;
    
    let itemText = document.createElement("div");
    itemText.className = "order-item-text";
    itemText.textContent = `${selectedOption.text} (${designation}) - ${quantity} pcs = ${totalProductPrice.toLocaleString()} FCFA`;
    
    let deleteBtn = document.createElement("button");
    deleteBtn.className = "delete-btn";
    deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
    deleteBtn.onclick = function() { removeFromOrder(itemIndex); };
    
    itemDiv.appendChild(itemText);
    itemDiv.appendChild(deleteBtn);
    orderSummary.appendChild(itemDiv);

    // Update total price display
    document.getElementById("total-price").textContent = totalPrice.toLocaleString();
    document.getElementById("full-price").textContent = totalPrice.toLocaleString() + " FCFA";
    
    // Calculate and update half price (50%)
    const halfPrice = Math.round(totalPrice / 2);
    document.getElementById("half-price").textContent = halfPrice.toLocaleString() + " FCFA";
    document.getElementById("remaining-balance").textContent = halfPrice.toLocaleString() + " FCFA";
    
    validateOrder();
    
    // Clear selection fields
    productSelect.selectedIndex = 0;
    designationSelect.innerHTML = '<option value="">Select Designation</option>';
    designationSelect.disabled = true;
    quantityInput.value = "";
  }

  function removeFromOrder(index) {
    // Check if the item exists
    if (index >= 0 && index < orderItems.length) {
      // Subtract the item's price from the total
      totalPrice -= orderItems[index].totalPrice;
      
      // Remove the item from the array
      orderItems.splice(index, 1);
      
      // Update the display
      updateOrderSummary();
      
      // Update total price display
      document.getElementById("total-price").textContent = totalPrice.toLocaleString();
      document.getElementById("full-price").textContent = totalPrice.toLocaleString() + " FCFA";
      
      // Calculate and update half price (50%)
      const halfPrice = Math.round(totalPrice / 2);
      document.getElementById("half-price").textContent = halfPrice.toLocaleString() + " FCFA";
      document.getElementById("remaining-balance").textContent = halfPrice.toLocaleString() + " FCFA";
      
      validateOrder();
    }
  }

  function updateOrderSummary() {
    // Clear the current order summary
    const orderSummary = document.getElementById("order-summary");
    orderSummary.innerHTML = '';
    
    // Rebuild the order summary with the current items
    orderItems.forEach((item, index) => {
      let itemDiv = document.createElement("div");
      itemDiv.className = "order-item";
      itemDiv.id = `order-item-${index}`;
      
      let itemText = document.createElement("div");
      itemText.className = "order-item-text";
      itemText.textContent = `${item.name} (${item.designation}) - ${item.quantity} pcs = ${item.totalPrice.toLocaleString()} FCFA`;
      
      let deleteBtn = document.createElement("button");
      deleteBtn.className = "delete-btn";
      deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
      deleteBtn.onclick = function() { removeFromOrder(index); };
      
      itemDiv.appendChild(itemText);
      itemDiv.appendChild(deleteBtn);
      orderSummary.appendChild(itemDiv);
    });
  }

  function validateOrder() {
    let hospitalName = document.getElementById("hospitalName").value.trim();
    let hospitalLocation = document.getElementById("hospitalLocation").value.trim();
    document.getElementById("orderNow").disabled = !(hospitalName && hospitalLocation && totalPrice > 0);
  }

  function showPaymentModal() {
    // Update payment modal with current prices
    document.getElementById("modal-full-price").textContent = document.getElementById("full-price").textContent;
    document.getElementById("modal-half-price").textContent = document.getElementById("half-price").textContent;
    
    // Create order in the database first
    const hospitalName = document.getElementById("hospitalName").value;
    const hospitalLocation = document.getElementById("hospitalLocation").value;
    
    // Send order data to the server
    fetch('process_order.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        hospitalName: hospitalName,
        hospitalLocation: hospitalLocation,
        items: orderItems
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Store order ID and number for payment processing
        orderId = data.order.order_id;
        orderNumber = data.order.order_number;
        
        // Show payment modal
        document.getElementById("payment-modal").style.display = "block";
      } else {
        alert('Error creating order: ' + data.error);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while processing your order. Please try again.');
    });
  }

  function closePaymentModal() {
    document.getElementById("payment-modal").style.display = "none";
  }

  // Switch between payment methods
  function switchPaymentMethod(method) {
    // Update tabs
    document.querySelectorAll('.payment-tab').forEach(tab => {
      tab.classList.remove('active');
    });
    document.querySelector(`.payment-tab[onclick*="${method}"]`).classList.add('active');
    
    // Update payment forms
    document.querySelectorAll('.payment-form-section').forEach(form => {
      form.style.display = 'none';
    });
    document.getElementById(`${method}-payment`).style.display = 'block';
  }

  // Format card number with spaces
  function formatCardNumber(input) {
    let value = input.value.replace(/\D/g, '');
    let formattedValue = '';
    
    for (let i = 0; i < value.length; i++) {
      if (i > 0 && i % 4 === 0) {
        formattedValue += ' ';
      }
      formattedValue += value[i];
    }
    
    input.value = formattedValue;
  }

  // Format expiry date with slash
  function formatExpiryDate(input) {
    let value = input.value.replace(/\D/g, '');
    
    if (value.length > 2) {
      input.value = value.substring(0, 2) + '/' + value.substring(2);
    } else {
      input.value = value;
    }
  }

  // Detect card type based on first digits
  function detectCardType(cardNumber) {
    const number = cardNumber.replace(/\D/g, '');
    const cardTypeIcon = document.getElementById('card-type-icon');
    const cardLogo = document.getElementById('card-logo');
    
    // Reset
    cardTypeIcon.innerHTML = '<i class="far fa-credit-card"></i>';
    cardLogo.innerHTML = '<i class="far fa-credit-card"></i>';
    
    if (number.startsWith('4')) {
      // Visa
      cardTypeIcon.innerHTML = '<i class="fab fa-cc-visa"></i>';
      cardLogo.innerHTML = '<i class="fab fa-cc-visa"></i>';
    } else if (/^5[1-5]/.test(number)) {
      // Mastercard
      cardTypeIcon.innerHTML = '<i class="fab fa-cc-mastercard"></i>';
      cardLogo.innerHTML = '<i class="fab fa-cc-mastercard"></i>';
    } else if (/^3[47]/.test(number)) {
      // American Express
      cardTypeIcon.innerHTML = '<i class="fab fa-cc-amex"></i>';
      cardLogo.innerHTML = '<i class="fab fa-cc-amex"></i>';
    } else if (/^6(?:011|5)/.test(number)) {
      // Discover
      cardTypeIcon.innerHTML = '<i class="fab fa-cc-discover"></i>';
      cardLogo.innerHTML = '<i class="fab fa-cc-discover"></i>';
    }
  }

  // Update card preview
  function updateCardPreview() {
    const cardNumber = document.getElementById('card-number').value || '•••• •••• •••• ••••';
    const cardHolder = document.getElementById('card-holder-name').value || 'YOUR NAME';
    const expiryDate = document.getElementById('expiry-date').value || 'MM/YY';
    
    document.getElementById('card-number-display').textContent = cardNumber;
    document.getElementById('card-holder-display').textContent = cardHolder.toUpperCase();
    document.getElementById('card-expiry-display').textContent = expiryDate;
  }

  // Verify payment
  function verifyPayment() {
    // Determine which payment method is active
    const activeMethod = document.querySelector('.payment-tab.active').textContent.trim().includes('Mobile') ? 'mobile-money' : 'credit-card';
    
    if (activeMethod === 'mobile-money') {
      // Mobile Money validation
      const provider = document.getElementById('mobile-money-provider').value;
      const phone = document.getElementById('mobile-money-phone').value;
      const code = document.getElementById('mobile-money-code').value;
      
      if (!provider || !phone || !code) {
        alert('Please fill in all mobile money payment details.');
        return;
      }
      
      // Process mobile money payment
      processPayment('mobile', provider, phone);
      
    } else if (activeMethod === 'credit-card') {
      // Credit Card validation
      const cardHolder = document.getElementById('card-holder-name').value;
      const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
      const expiryDate = document.getElementById('expiry-date').value;
      const cvv = document.getElementById('cvv').value;
      
      if (!cardHolder || !cardNumber || !expiryDate || !cvv) {
        alert('Please fill in all credit card details.');
        return;
      }
      
      if (cardNumber.length < 13 || cardNumber.length > 19) {
        alert('Please enter a valid card number.');
        return;
      }
      
      if (!/^\d{2}\/\d{2}$/.test(expiryDate)) {
        alert('Please enter a valid expiry date (MM/YY).');
        return;
      }
      
      if (!/^\d{3,4}$/.test(cvv)) {
        alert('Please enter a valid CVV code.');
        return;
      }
      
      // Get card type
      let cardType = 'Credit Card';
      if (document.getElementById('card-type-icon').innerHTML.includes('visa')) {
        cardType = 'Visa';
      } else if (document.getElementById('card-type-icon').innerHTML.includes('mastercard')) {
        cardType = 'Mastercard';
      } else if (document.getElementById('card-type-icon').innerHTML.includes('amex')) {
        cardType = 'American Express';
      } else if (document.getElementById('card-type-icon').innerHTML.includes('discover')) {
        cardType = 'Discover';
      }
      
      // Process credit card payment
      processPayment('credit', cardType, cardNumber);
    }
  }

  // Process payment (connect to payment gateway)
  function processPayment(method, provider, accountNumber) {
    // Calculate half price (50%)
    const halfPrice = Math.round(totalPrice / 2);
    
    // Send payment data to the server
    fetch('process_payment.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        orderId: orderId,
        paymentMethod: method === 'mobile' ? provider + ' Mobile Money' : provider + ' Card',
        paymentDetails: accountNumber,
        amount: halfPrice
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Generate receipt with payment details
        if (method === 'mobile') {
          generateAndShowReceipt(provider, accountNumber, data.transactionId);
        } else {
          // For credit card, mask the card number for security
          const maskedCardNumber = accountNumber.slice(-4).padStart(accountNumber.length, '•');
          generateAndShowCreditCardReceipt(provider, maskedCardNumber, data.transactionId, document.getElementById('card-holder-name').value);
        }
      } else {
        alert('Error processing payment: ' + data.error);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while processing your payment. Please try again.');
    });
  }

  // Format date as DD/MM/YYYY
  function formatDate(date) {
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
  }

  // Generate and show receipt after successful mobile money payment
  function generateAndShowReceipt(provider, phone, transactionId) {
    const hospitalName = document.getElementById("hospitalName").value;
    const hospitalLocation = document.getElementById("hospitalLocation").value;
    
    // Set receipt information
    document.getElementById("receipt-hospital-name").textContent = hospitalName;
    document.getElementById("receipt-hospital-location").textContent = hospitalLocation;
    document.getElementById("receipt-order-number").textContent = orderNumber;
    document.getElementById("receipt-order-number-display").textContent = orderNumber;
    
    // Set payment information
    document.getElementById("receipt-payment-method").textContent = provider === 'mtn' ? 'MTN Mobile Money' : 'Orange Mobile Money';
    document.getElementById("receipt-phone-number-container").style.display = "block";
    document.getElementById("receipt-card-info-container").style.display = "none";
    document.getElementById("receipt-phone-number").textContent = phone;
    document.getElementById("receipt-transaction-id").textContent = transactionId;
    
    // Generate receipt content
    generateReceiptContent();
  }

  // Generate and show receipt after successful credit card payment
  function generateAndShowCreditCardReceipt(cardType, maskedCardNumber, transactionId, cardHolder) {
    const hospitalName = document.getElementById("hospitalName").value;
    const hospitalLocation = document.getElementById("hospitalLocation").value;
    
    // Set receipt information
    document.getElementById("receipt-hospital-name").textContent = hospitalName;
    document.getElementById("receipt-hospital-location").textContent = hospitalLocation;
    document.getElementById("receipt-order-number").textContent = orderNumber;
    document.getElementById("receipt-order-number-display").textContent = orderNumber;
    
    // Set payment information
    document.getElementById("receipt-payment-method").textContent = cardType + " Credit Card";
    document.getElementById("receipt-phone-number-container").style.display = "none";
    document.getElementById("receipt-card-info-container").style.display = "block";
    document.getElementById("receipt-card-number").textContent = maskedCardNumber;
    document.getElementById("receipt-transaction-id").textContent = transactionId;
    
    // Generate receipt content
    generateReceiptContent();
  }

  // Generate common receipt content
  function generateReceiptContent() {
    // Clear previous receipt items
    const tableBody = document.getElementById("receipt-product-table-body");
    tableBody.innerHTML = '';
    
    // Add order items to receipt
    orderItems.forEach(item => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${item.name}</td>
        <td>${item.designation}</td>
        <td>${item.quantity}</td>
        <td>${item.unitPrice.toLocaleString()} FCFA</td>
        <td>${item.totalPrice.toLocaleString()} FCFA</td>
      `;
      tableBody.appendChild(row);
    });
    
    // Calculate half price (50%)
    const halfPrice = Math.round(totalPrice / 2);
    
    // Set total price and payment details
    document.getElementById("receipt-total-price").textContent = `${totalPrice.toLocaleString()} FCFA`;
    document.getElementById("receipt-paid-amount").textContent = `${halfPrice.toLocaleString()} FCFA`;
    document.getElementById("receipt-remaining-balance").textContent = `${halfPrice.toLocaleString()} FCFA`;
    document.getElementById("receipt-balance-due").textContent = `${halfPrice.toLocaleString()} FCFA`;
    
    // Close payment modal and show receipt
    closePaymentModal();
    document.getElementById("receipt-modal").style.display = "block";
  }

  function closeReceiptModal() {
    document.getElementById("receipt-modal").style.display = "none";
    // Reset order after closing receipt
    resetOrder();
  }

  function resetOrder() {
    // Reset order items and total price
    orderItems = [];
    totalPrice = 0;
    orderId = null;
    orderNumber = null;
    
    // Clear order summary
    document.getElementById("order-summary").innerHTML = '';
    document.getElementById("total-price").textContent = '0';
    document.getElementById("full-price").textContent = '0 FCFA';
    document.getElementById("half-price").textContent = '0 FCFA';
    document.getElementById("remaining-balance").textContent = '0 FCFA';
    
    // Reset form fields
    document.getElementById("hospitalName").value = '';
    document.getElementById("hospitalLocation").value = '';
    document.getElementById("product-name").selectedIndex = 0;
    document.getElementById("product-designation").innerHTML = '<option value="">Select Designation</option>';
    document.getElementById("product-designation").disabled = true;
    document.getElementById("product-quantity").value = '';
    
    // Disable order button
    document.getElementById("orderNow").disabled = true;
  }

  // Print receipt
  function printReceipt() {
    window.print();
  }

  // Download receipt as text file
  function downloadReceipt() {
    // Create text content for the receipt
    let receiptContent = "HESSOM MEDICAL PRODUCTS ENTERPRISE\n";
    receiptContent += "================================\n\n";
    receiptContent += "OFFICIAL RECEIPT\n\n";
    receiptContent += `Date: ${document.getElementById('receipt-date').textContent}\n`;
    receiptContent += `Receipt #: ${document.getElementById('receipt-order-number').textContent}\n\n`;
    receiptContent += "Hospital Information:\n";
    receiptContent += `Hospital Name: ${document.getElementById('receipt-hospital-name').textContent}\n`;
    receiptContent += `Hospital Location: ${document.getElementById('receipt-hospital-location').textContent}\n`;
    receiptContent += `Order Number: ${document.getElementById('receipt-order-number-display').textContent}\n\n`;
    receiptContent += "Order Summary:\n";
    receiptContent += "--------------------------------\n";
    
    orderItems.forEach(item => {
      receiptContent += `${item.name} (${item.designation}) - ${item.quantity} x ${item.unitPrice} FCFA = ${item.totalPrice} FCFA\n`;
    });
    
    receiptContent += "--------------------------------\n";
    receiptContent += `Total Order Value: ${document.getElementById('receipt-total-price').textContent}\n`;
    receiptContent += `Paid Amount (50%): ${document.getElementById('receipt-paid-amount').textContent}\n`;
    receiptContent += `Remaining Balance (50%): ${document.getElementById('receipt-remaining-balance').textContent}\n\n`;
    receiptContent += "Payment Information:\n";
    receiptContent += `Payment Method: ${document.getElementById('receipt-payment-method').textContent}\n`;
    
    // Check which payment method was used
    if (document.getElementById('receipt-phone-number-container').style.display !== 'none') {
      receiptContent += `Phone Number: ${document.getElementById('receipt-phone-number').textContent}\n`;
    } else if (document.getElementById('receipt-card-info-container').style.display !== 'none') {
      receiptContent += `Card Number: ${document.getElementById('receipt-card-number').textContent}\n`;
    }
    
    receiptContent += `Transaction ID: ${document.getElementById('receipt-transaction-id').textContent}\n`;
    receiptContent += `Payment Status: 50% Paid\n`;
    receiptContent += `Payment Date: ${document.getElementById('receipt-payment-date').textContent}\n\n`;
    receiptContent += "The remaining 50% balance is due upon delivery.\n\n";
    receiptContent += "Thank you for your order!\n";
    receiptContent += "For any inquiries, please contact us at hessom.medical@gmail.com\n";
    
    // Create a blob and download
    const blob = new Blob([receiptContent], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Receipt_${document.getElementById('receipt-order-number').textContent}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  }

  // Notification system
  function showNotifications() {
    let notificationDiv = document.getElementById('notifications');
    notificationDiv.style.display = notificationDiv.style.display === 'block' ? 'none' : 'block';
    
    // Mark notifications as read when viewed
    if (notificationDiv.style.display === 'block') {
      fetch('mark_notifications_read.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('notification-count').textContent = '0';
          }
        })
        .catch(error => console.error('Error:', error));
    }
  }

  function clearNotifications() {
    // Clear notifications list
    document.getElementById('notification-list').innerHTML = '';
    document.getElementById('notification-count').textContent = '0';
    
    // Hide notifications panel
    document.getElementById('notifications').style.display = 'none';
  }
</script>

</body>
</html>