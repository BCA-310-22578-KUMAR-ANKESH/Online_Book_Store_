<?php
session_start();
include_once('includes/config.php');
if(strlen($_SESSION['id'])==0 || !isset($_SESSION['paymenttype']) || !isset($_SESSION['gtotal'])) {
    header('location:login.php');
    exit();
}
$payment_type = $_SESSION['paymenttype'];
$total_amount = $_SESSION['gtotal'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Customer';
$approx_inr = $total_amount * 83.00; // 1 USD = 83 INR approximate conversion
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Book Store || Secure Checkout</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/linearicons-v1.0.0/icon-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <!--===============================================================================================-->	
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!--===============================================================================================-->
    
    <style>
        /* Light Gateway Custom Styles to Blend Seamlessly with Coza Store Template */
        .gateway-card {
            background: #ffffff;
            border: 1px solid #e6e6e6;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 40px;
            margin-bottom: 30px;
        }

        .gateway-sidebar {
            background: #f9f9f9;
            border: 1px solid #e6e6e6;
            border-radius: 12px;
            padding: 30px;
        }

        .gateway-badge {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #eefdf7;
            border: 1px dashed #a3e8cc;
            padding: 15px;
            border-radius: 8px;
            font-size: 13px;
            color: #10b981;
            margin-top: 30px;
        }

        .gateway-amount-display {
            font-size: 32px;
            font-weight: 700;
            color: #333333;
            margin-bottom: 5px;
        }

        .gateway-amount-sub {
            font-size: 15px;
            color: #888888;
            margin-bottom: 25px;
            font-weight: 500;
        }

        /* Light Card Visualization */
        .preview-card-container {
            perspective: 1000px;
            margin-bottom: 30px;
            height: 180px;
            width: 100%;
        }

        .preview-card {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .preview-card.flipped {
            transform: rotateY(180deg);
        }

        .preview-card-front, .preview-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .preview-card-front {
            background: linear-gradient(135deg, #1c3d5a, #386fa4);
        }

        .preview-card-back {
            background: linear-gradient(135deg, #2d3748, #1a202c);
            transform: rotateY(180deg);
            padding: 20px 0;
            justify-content: flex-start;
            gap: 15px;
        }

        .preview-card-chip {
            background: linear-gradient(135deg, #f39c12, #f1c40f);
            width: 38px;
            height: 28px;
            border-radius: 4px;
        }

        .preview-card-logo {
            font-size: 20px;
            font-weight: bold;
            font-style: italic;
            text-align: right;
        }

        .preview-card-number {
            font-family: monospace;
            font-size: 18px;
            letter-spacing: 2px;
            margin: 15px 0 5px 0;
            text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
        }

        .preview-card-bottom {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .preview-card-holder-label, .preview-card-exp-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #cfd8dc;
        }

        .preview-card-holder-val, .preview-card-exp-val {
            text-transform: uppercase;
            font-weight: 500;
        }

        .preview-card-strip {
            width: 100%;
            height: 35px;
            background: #111;
        }

        .preview-card-signature {
            background: #eceff1;
            margin: 0 20px;
            height: 30px;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 10px;
            color: #333;
            font-family: monospace;
            font-weight: bold;
        }

        /* Indian Bank & UPI grids */
        .pay-selection-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .pay-grid-item {
            border: 1px solid #e6e6e6;
            background: #fafafa;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .pay-grid-item:hover {
            border-color: #717fe0;
            background: #f7f8ff;
        }

        .pay-grid-item.selected {
            border-color: #717fe0;
            background: #eff1ff;
            box-shadow: 0 0 0 2px rgba(113, 127, 224, 0.2);
        }

        .pay-grid-item-icon {
            font-size: 18px;
            color: #717fe0;
        }

        .pay-grid-item-name {
            font-size: 13px;
            font-weight: 500;
            color: #333333;
        }

        /* QR Code Container style */
        .qr-container {
            border: 1px solid #e6e6e6;
            border-radius: 8px;
            padding: 20px;
            background: #fcfcfc;
            text-align: center;
            margin-bottom: 25px;
        }

        .qr-svg {
            width: 140px;
            height: 140px;
            margin: 10px auto;
        }

        /* Animated processing overlay */
        .gateway-processing-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 12px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .gateway-processing-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .gateway-spinner {
            width: 45px;
            height: 45px;
            border: 4px solid rgba(113, 127, 224, 0.1);
            border-top: 4px solid #717fe0;
            border-radius: 50%;
            animation: gatewaySpin 1s linear infinite;
            margin-bottom: 15px;
        }

        .gateway-success-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #eefdf7;
            border: 2px solid #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            color: #10b981;
            margin-bottom: 15px;
        }

        /* OTP Dialog Modal */
        .otp-modal-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1050;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .otp-modal-container.active {
            opacity: 1;
            pointer-events: auto;
        }

        .otp-modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.2s ease;
        }

        .otp-modal-container.active .otp-modal-content {
            transform: scale(1);
        }

        .otp-modal-inputs input {
            width: 40px;
            height: 45px;
            border: 1px solid #d9d9d9;
            border-radius: 6px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            margin: 0 4px;
            outline: none;
        }

        .otp-modal-inputs input:focus {
            border-color: #717fe0;
        }

        @keyframes gatewaySpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="animsition">

    <!-- Header -->
    <?php include_once('includes/header.php');?>

    <!-- Banner Title -->
    <section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('images/bg-01.jpg');">
        <h2 class="ltext-105 cl0 txt-center">
            Secure Payment Gateway
        </h2>
    </section>

    <!-- Content Area -->
    <section class="bg0 p-t-65 p-b-60">
        <div class="container">
            <div class="row">
                <!-- Sidebar Summary -->
                <div class="col-md-4 col-lg-4 p-b-30">
                    <div class="gateway-sidebar">
                        <h4 class="mtext-103 cl2 p-b-20">Order Summary</h4>
                        
                        <div class="gateway-amount-display">
                            ₹<?php echo number_format($total_amount, 2); ?>
                        </div>

                        <div class="p-b-10 border-bottom d-flex justify-content-between font-14">
                            <span class="cl6">Customer</span>
                            <span class="cl2 font-weight-bold"><?php echo htmlspecialchars($username); ?></span>
                        </div>
                        <div class="p-t-10 p-b-10 border-bottom d-flex justify-content-between font-14">
                            <span class="cl6">Payment Mode</span>
                            <span class="cl2 font-weight-bold" style="text-transform: capitalize;"><?php echo htmlspecialchars($payment_type); ?></span>
                        </div>
                        <div class="p-t-10 d-flex justify-content-between font-14">
                            <span class="cl6">Status</span>
                            <span class="text-success font-weight-bold"><i class="fa fa-check-circle"></i> Connected</span>
                        </div>

                        <div class="gateway-badge">
                            <i class="fa fa-shield" style="font-size: 24px; color: #10b981;"></i>
                            <div>
                                <strong style="display: block; font-size: 12px; line-height: 1.2;">Secured by 256-bit SSL</strong>
                                <span style="font-size: 10px; color: #666;">Your information is fully encrypted.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Form Body -->
                <div class="col-md-8 col-lg-8 p-b-30">
                    <div class="gateway-card position-relative">
                        <!-- Loading / Success Screen -->
                        <div class="gateway-processing-overlay" id="gatewayOverlay">
                            <div class="gateway-spinner" id="gatewaySpinner"></div>
                            <div class="gateway-success-icon" id="gatewaySuccessCheck" style="display: none;"><i class="fa fa-check"></i></div>
                            <h4 class="mtext-103 cl2 text-center" id="gatewayOverlayText">Connecting to secure host...</h4>
                            <p class="stext-113 cl6 text-center mt-2">Do not refresh or click the back button.</p>
                        </div>

                        <h3 class="mtext-105 cl2 p-b-25">Authorize Payment</h3>

                        <?php if($payment_type == 'Debit/Credit Card'): ?>
                            <!-- Card Preview -->
                            <div class="preview-card-container">
                                <div class="preview-card" id="cardPreview">
                                    <div class="preview-card-front">
                                        <div class="preview-card-chip"></div>
                                        <div class="preview-card-logo" id="cardLogo">VISA</div>
                                        <div class="preview-card-number" id="cardNumDisplay">•••• •••• •••• ••••</div>
                                        <div class="preview-card-bottom">
                                            <div>
                                                <div class="preview-card-holder-label">Holder Name</div>
                                                <div class="preview-card-holder-val" id="cardHolderDisplay">John Doe</div>
                                            </div>
                                            <div>
                                                <div class="preview-card-exp-label">Expires</div>
                                                <div class="preview-card-exp-val" id="cardExpiryDisplay">MM/YY</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="preview-card-back">
                                        <div class="preview-card-strip"></div>
                                        <div style="text-align: right; font-size: 8px; margin-right: 20px; color: #ccc;">CVV</div>
                                        <div class="preview-card-signature" id="cardCvvDisplay">•••</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card inputs -->
                            <form id="gatewayCardForm" onsubmit="submitCardPayment(event)">
                                <div class="form-group">
                                    <label class="cl6 font-weight-bold small">Card Number</label>
                                    <input type="text" id="cardNo" class="form-control size-116 p-l-20" placeholder="4111 1111 1111 1111" pattern="[0-9 ]{19}" title="Enter valid 16-digit card number" required>
                                </div>
                                <div class="form-group">
                                    <label class="cl6 font-weight-bold small">Cardholder Name</label>
                                    <input type="text" id="cardName" class="form-control size-116 p-l-20" placeholder="JOHN DOE" required>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="cl6 font-weight-bold small">Expiration Date</label>
                                            <input type="text" id="cardExpiry" class="form-control size-116 p-l-20" placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/([0-9]{2})" title="MM/YY format" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="cl6 font-weight-bold small">CVV Code</label>
                                            <input type="password" id="cardCvv" class="form-control size-116 p-l-20" placeholder="•••" pattern="[0-9]{3}" maxlength="3" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="flex-c-m stext-101 cl0 size-116 bg1 bor1 hov-btn1 p-lr-15 trans-04 font-weight-bold mt-4">
                                    Authorize & Pay ₹<?php echo number_format($total_amount, 2); ?>
                                </button>
                            </form>

                        <?php elseif($payment_type == 'Internet Banking'): ?>
                            <!-- Net Banking Indian Banks -->
                            <p class="cl6 p-b-20">Select your banking partner to authorize the transaction:</p>
                            <div class="pay-selection-grid">
                                <div class="pay-grid-item" onclick="selectBank(this, 'State Bank of India')">
                                    <i class="fa fa-university pay-grid-item-icon"></i>
                                    <span class="pay-grid-item-name">State Bank of India</span>
                                </div>
                                <div class="pay-grid-item" onclick="selectBank(this, 'HDFC Bank')">
                                    <i class="fa fa-university pay-grid-item-icon"></i>
                                    <span class="pay-grid-item-name">HDFC Bank</span>
                                </div>
                                <div class="pay-grid-item" onclick="selectBank(this, 'ICICI Bank')">
                                    <i class="fa fa-university pay-grid-item-icon"></i>
                                    <span class="pay-grid-item-name">ICICI Bank</span>
                                </div>
                                <div class="pay-grid-item" onclick="selectBank(this, 'Axis Bank')">
                                    <i class="fa fa-university pay-grid-item-icon"></i>
                                    <span class="pay-grid-item-name">Axis Bank</span>
                                </div>
                                <div class="pay-grid-item" onclick="selectBank(this, 'Punjab National Bank')">
                                    <i class="fa fa-university pay-grid-item-icon"></i>
                                    <span class="pay-grid-item-name">Punjab National Bank</span>
                                </div>
                                <div class="pay-grid-item" onclick="selectBank(this, 'Kotak Mahindra Bank')">
                                    <i class="fa fa-university pay-grid-item-icon"></i>
                                    <span class="pay-grid-item-name">Kotak Mahindra Bank</span>
                                </div>
                            </div>
                            <button type="button" class="flex-c-m stext-101 cl0 size-116 bg1 bor1 hov-btn1 p-lr-15 trans-04 font-weight-bold" onclick="submitAltPayment()">
                                Continue to Bank Portal
                            </button>

                        <?php else: ?>
                            <!-- UPI Payment Mode -->
                            <p class="cl6 p-b-20">Choose your preferred UPI authorization mode:</p>
                            
                            <div class="pay-selection-grid">
                                <div class="pay-grid-item selected" onclick="toggleUpiMode('vpa', this)">
                                    <i class="fa fa-at pay-grid-item-icon"></i>
                                    <span class="pay-grid-item-name">UPI ID / VPA</span>
                                </div>
                                <div class="pay-grid-item" onclick="toggleUpiMode('qr', this)">
                                    <i class="fa fa-qrcode pay-grid-item-icon"></i>
                                    <span class="pay-grid-item-name">Scan QR Code</span>
                                </div>
                            </div>

                            <!-- UPI ID Input -->
                            <div id="upiVpaArea">
                                <div class="form-group">
                                    <label class="cl6 font-weight-bold small">Enter UPI ID</label>
                                    <input type="text" id="upiVpa" class="form-control size-116 p-l-20" placeholder="username@sbi" required>
                                    <small class="form-text text-muted">Example: name@sbi, mobile@paytm, name@okhdfcbank</small>
                                </div>
                            </div>

                            <!-- UPI QR Area -->
                            <div id="upiQrArea" class="qr-container" style="display: none;">
                                <p class="small text-muted mb-2">Scan QR code using Google Pay, PhonePe, Paytm, or BHIM</p>
                                <svg class="qr-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <!-- QR Code Mock Vector representation -->
                                    <rect width="100" height="100" fill="white"/>
                                    <!-- Corner Squares -->
                                    <rect x="5" y="5" width="25" height="25" fill="#333" stroke="white" stroke-width="4"/>
                                    <rect x="10" y="10" width="15" height="15" fill="white"/>
                                    <rect x="13" y="13" width="9" height="9" fill="#333"/>
                                    
                                    <rect x="70" y="5" width="25" height="25" fill="#333" stroke="white" stroke-width="4"/>
                                    <rect x="75" y="10" width="15" height="15" fill="white"/>
                                    <rect x="78" y="13" width="9" height="9" fill="#333"/>
                                    
                                    <rect x="5" y="70" width="25" height="25" fill="#333" stroke="white" stroke-width="4"/>
                                    <rect x="10" y="75" width="15" height="15" fill="white"/>
                                    <rect x="13" y="78" width="9" height="9" fill="#333"/>
                                    
                                    <!-- Small corner identifier -->
                                    <rect x="75" y="75" width="10" height="10" fill="#333"/>
                                    
                                    <!-- Random QR Code Dots/Grid Pattern -->
                                    <rect x="35" y="5" width="5" height="20" fill="#333"/>
                                    <rect x="45" y="10" width="10" height="5" fill="#333"/>
                                    <rect x="60" y="5" width="5" height="15" fill="#333"/>
                                    <rect x="35" y="35" width="15" height="15" fill="#333"/>
                                    <rect x="55" y="30" width="25" height="5" fill="#333"/>
                                    <rect x="55" y="40" width="5" height="25" fill="#333"/>
                                    <rect x="65" y="50" width="15" height="5" fill="#333"/>
                                    <rect x="5" y="35" width="25" height="5" fill="#333"/>
                                    <rect x="15" y="45" width="5" height="20" fill="#333"/>
                                    <rect x="35" y="60" width="15" height="5" fill="#333"/>
                                    <rect x="35" y="70" width="5" height="25" fill="#333"/>
                                    <rect x="45" y="80" width="20" height="5" fill="#333"/>
                                    <rect x="70" y="60" width="5" height="10" fill="#333"/>
                                    <rect x="80" y="45" width="15" height="5" fill="#333"/>
                                    <rect x="85" y="65" width="10" height="5" fill="#333"/>
                                    <rect x="85" y="80" width="10" height="15" fill="#333"/>
                                </svg>
                                <strong class="d-block text-dark mt-2">₹<?php echo number_format($total_amount, 2); ?></strong>
                            </div>

                            <button type="button" id="upiSubmitBtn" class="flex-c-m stext-101 cl0 size-116 bg1 bor1 hov-btn1 p-lr-15 trans-04 font-weight-bold" onclick="submitAltPayment()">
                                Verify & Authorize Payment
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- OTP Modal Popup Dialog -->
    <div class="otp-modal-container" id="otpModal">
        <div class="otp-modal-content animate__animated animate__zoomIn animate__fast">
            <i class="fa fa-shield-alt text-primary mb-3" style="font-size: 50px;"></i>
            <h4 class="mtext-103 cl2 mb-2">2-Step Secure Verification</h4>
            <p class="stext-113 cl6 mb-4">A 6-digit OTP code has been sent to your bank registered mobile. Enter code <strong class="text-primary">123456</strong> to verify.</p>
            
            <div class="otp-modal-inputs d-flex justify-content-center mb-4" id="otpInputs">
                <input type="text" maxlength="1" onkeyup="otpInputKey(this, 0)">
                <input type="text" maxlength="1" onkeyup="otpInputKey(this, 1)">
                <input type="text" maxlength="1" onkeyup="otpInputKey(this, 2)">
                <input type="text" maxlength="1" onkeyup="otpInputKey(this, 3)">
                <input type="text" maxlength="1" onkeyup="otpInputKey(this, 4)">
                <input type="text" maxlength="1" onkeyup="otpInputKey(this, 5)">
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary w-50 mr-2" onclick="closeOtpPopup()">Cancel</button>
                <button type="button" class="btn btn-primary w-50" onclick="verifyOtpCode()">Verify & Pay</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once('includes/footer.php');?>

    <!--===============================================================================================-->	
    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/animsition/js/animsition.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/select2/select2.min.js"></script>
    <script>
        $(".js-select2").each(function(){
            $(this).select2({
                minimumResultsForSearch: 20,
                dropdownParent: $(this).next('.dropDownSelect2')
            });
        })
    </script>
    <!--===============================================================================================-->
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script>
        $('.js-pscroll').each(function(){
            $(this).css('position','relative');
            $(this).css('overflow','hidden');
            var ps = new PerfectScrollbar(this, {
                wheelSpeed: 1,
                scrollingThreshold: 1000,
                wheelPropagation: false,
            });

            $(window).on('resize', function(){
                ps.update();
            })
        });
    </script>
    <!--===============================================================================================-->
    <script src="js/main.js"></script>

    <!-- Gateway scripting -->
    <script type="text/javascript">
        // Card Interactive Visualization
        const cardNo = document.getElementById('cardNo');
        const cardName = document.getElementById('cardName');
        const cardExpiry = document.getElementById('cardExpiry');
        const cardCvv = document.getElementById('cardCvv');

        const cardPreview = document.getElementById('cardPreview');
        const displayNo = document.getElementById('cardNumDisplay');
        const displayName = document.getElementById('cardHolderDisplay');
        const displayExpiry = document.getElementById('cardExpiryDisplay');
        const displayCvv = document.getElementById('cardCvvDisplay');
        const logoText = document.getElementById('cardLogo');

        if (cardNo) {
            // Card number spaces formatting
            cardNo.addEventListener('input', (e) => {
                let clean = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                let formatted = '';
                for (let i = 0; i < clean.length; i++) {
                    if (i > 0 && i % 4 === 0) formatted += ' ';
                    formatted += clean[i];
                }
                e.target.value = formatted.substring(0, 19);
                displayNo.innerText = e.target.value || '•••• •••• •••• ••••';

                if (clean.startsWith('4')) logoText.innerText = 'VISA';
                else if (clean.startsWith('5')) logoText.innerText = 'MASTERCARD';
                else if (clean.startsWith('3')) logoText.innerText = 'AMEX';
                else logoText.innerText = 'CARD';
            });

            // Card name
            cardName.addEventListener('input', (e) => {
                displayName.innerText = e.target.value.toUpperCase() || 'JOHN DOE';
            });

            // Expiry date format MM/YY
            cardExpiry.addEventListener('input', (e) => {
                let digits = e.target.value.replace(/\D/g, '');
                if (digits.length >= 2) {
                    let m = parseInt(digits.substring(0, 2));
                    if (m > 12) m = 12;
                    if (m === 0) m = 1;
                    e.target.value = m.toString().padStart(2, '0') + (digits.length > 2 ? '/' + digits.substring(2, 4) : '');
                } else {
                    e.target.value = digits;
                }
                displayExpiry.innerText = e.target.value || 'MM/YY';
            });

            // Flip on CVV focus
            cardCvv.addEventListener('focus', () => cardPreview.classList.add('flipped'));
            cardCvv.addEventListener('blur', () => cardPreview.classList.remove('flipped'));
            cardCvv.addEventListener('input', (e) => {
                displayCvv.innerText = '•'.repeat(e.target.value.length) || '•••';
            });
        }

        // Net banking Selection
        let selectedBankName = '';
        function selectBank(element, name) {
            const siblings = element.parentElement.children;
            for(let card of siblings) {
                card.classList.remove('selected');
            }
            element.classList.add('selected');
            selectedBankName = name;
        }

        // UPI mode switcher
        let selectedUpiMode = 'vpa';
        function toggleUpiMode(mode, element) {
            const siblings = element.parentElement.children;
            for(let card of siblings) {
                card.classList.remove('selected');
            }
            element.classList.add('selected');
            selectedUpiMode = mode;

            const upiVpa = document.getElementById('upiVpa');
            if (mode === 'vpa') {
                document.getElementById('upiVpaArea').style.display = 'block';
                document.getElementById('upiQrArea').style.display = 'none';
                upiVpa.setAttribute('required', 'true');
            } else {
                document.getElementById('upiVpaArea').style.display = 'none';
                document.getElementById('upiQrArea').style.display = 'block';
                upiVpa.removeAttribute('required');
            }
        }

        // Overlays & Modals
        const gatewayOverlay = document.getElementById('gatewayOverlay');
        const gatewayOverlayText = document.getElementById('gatewayOverlayText');
        const gatewaySpinner = document.getElementById('gatewaySpinner');
        const gatewayCheck = document.getElementById('gatewaySuccessCheck');
        const otpModal = document.getElementById('otpModal');

        function triggerOverlay(text, duration, callback) {
            gatewayOverlayText.innerText = text;
            gatewayOverlay.classList.add('active');
            setTimeout(() => {
                gatewayOverlay.classList.remove('active');
                if (callback) callback();
            }, duration);
        }

        function submitCardPayment(e) {
            e.preventDefault();
            triggerOverlay('Contacting card issuer...', 1500, () => {
                openOtpPopup();
            });
        }

        function submitAltPayment() {
            if ("<?php echo $payment_type; ?>" === 'Internet Banking' && !selectedBankName) {
                alert('Please select a banking partner to proceed.');
                return;
            }
            if ("<?php echo $payment_type; ?>" === 'UPI' && selectedUpiMode === 'vpa') {
                const upiVpaVal = document.getElementById('upiVpa').value;
                if(!upiVpaVal.includes('@')) {
                    alert('Please enter a valid UPI ID (containing @).');
                    return;
                }
            }

            triggerOverlay('Initializing secure gateway session...', 1500, () => {
                openOtpPopup();
            });
        }

        // OTP Controls
        const otpInputs = document.querySelectorAll('#otpInputs input');
        
        function openOtpPopup() {
            otpModal.classList.add('active');
            otpInputs.forEach(input => input.value = '');
            otpInputs[0].focus();
        }

        function closeOtpPopup() {
            otpModal.classList.remove('active');
        }

        function otpInputKey(element, index) {
            element.value = element.value.replace(/[^0-9]/g, '');
            if(element.value && index < 5) {
                otpInputs[index + 1].focus();
            }
        }

        function verifyOtpCode() {
            let code = '';
            otpInputs.forEach(input => code += input.value);

            if (code.length < 6) {
                alert('Please enter the full 6-digit OTP code.');
                return;
            }

            if(code === '123456') {
                closeOtpPopup();
                
                // Show Success Screen inside the gateway container
                gatewaySpinner.style.display = 'none';
                gatewayCheck.style.display = 'flex';
                gatewayOverlayText.innerText = 'Payment Authorized Successfully!';
                gatewayOverlay.classList.add('active');

                setTimeout(() => {
                    // Generate txn code
                    const chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    let txn = 'TXN_IND_';
                    for (let i = 0; i < 8; i++) {
                        txn += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    window.location.href = `payment.php?gateway_success=true&txnnumber=${txn}`;
                }, 1800);
            } else {
                alert('Verification Code incorrect. Use standard mock code 123456.');
                otpInputs.forEach(input => input.value = '');
                otpInputs[0].focus();
            }
        }
    </script>

</body>
</html>
