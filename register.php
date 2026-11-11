<?php
include_once __DIR__ . "/includes/files_includes.php";

$primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?: '#ed168b';
$hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?: '#dc007b';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />

    <style>
        :root {
            --t18-primary: <?= htmlspecialchars($primary_color, ENT_QUOTES, 'UTF-8') ?>;
            --t18-hover: <?= htmlspecialchars($hover_color, ENT_QUOTES, 'UTF-8') ?>;
        }

        body {
            background: radial-gradient(circle at 10% 20%, color-mix(in srgb, var(--t18-primary) 5%, #fcfcfc) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, color-mix(in srgb, var(--t18-hover) 5%, #fcfcfc) 0%, transparent 40%),
                #fafafa;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
        }

        .iti,
        .iti input {
            width: 100% !important;
        }

        .iti__country-list {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        /* ==================== 3D FLOATING SHAPES (NO CIRCLES) ==================== */
        .t18-shape-wrap {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .t18-shape {
            position: absolute;
            border-radius: 35%;
            /* Squircle/Rounded shape */
            background: linear-gradient(135deg, color-mix(in srgb, var(--t18-primary) 15%, #fff) 0%, color-mix(in srgb, var(--t18-hover) 10%, #fff) 100%);
            box-shadow: 0 20px 40px color-mix(in srgb, var(--t18-primary) 10%, transparent);
            filter: blur(2px);
            opacity: 0.6;
            animation: floatShape 12s ease-in-out infinite alternate;
        }

        .t18-shape-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
            transform: rotate(25deg);
        }

        .t18-shape-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -100px;
            transform: rotate(-15deg);
            animation-delay: -5s;
        }

        .t18-shape-3 {
            width: 150px;
            height: 150px;
            top: 30%;
            right: 10%;
            opacity: 0.4;
            animation-duration: 18s;
        }

        @keyframes floatShape {
            0% {
                transform: translate(0, 0) rotate(0deg) scale(1);
            }

            100% {
                transform: translate(20px, -20px) rotate(15deg) scale(1.05);
            }
        }

        /* ==================== MAIN CARD ==================== */
        .t18-auth-container {
            position: relative;
            z-index: 1;
            max-width: 1050px;
            /* Slightly wider for register */
            margin: 40px auto;
            border-radius: 32px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 25px 60px -15px color-mix(in srgb, var(--t18-primary) 25%, transparent),
                0 10px 20px -5px rgba(0, 0, 0, 0.05);
            display: grid;
            grid-template-columns: 1fr;
            overflow: hidden;
            width: calc(100% - 32px);
        }

        @media (min-width: 860px) {
            .t18-auth-container {
                grid-template-columns: 1fr 1fr;
                margin: 60px auto;
            }
        }

        /* ==================== LEFT SIDE (INFO) ==================== */
        .t18-auth-aside {
            background: linear-gradient(135deg, var(--t18-primary) 0%, color-mix(in srgb, var(--t18-hover) 80%, #000) 100%);
            padding: 40px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 860px) {
            .t18-auth-aside {
                padding: 60px;
            }
        }

        /* Floating rounded rectangle inside the dark panel */
        .t18-aside-shape {
            position: absolute;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 40px;
            bottom: -80px;
            left: -80px;
            transform: rotate(45deg);
            pointer-events: none;
        }

        .t18-aside-shape-2 {
            width: 150px;
            height: 150px;
            top: -40px;
            right: -40px;
            bottom: auto;
            left: auto;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.08);
            transform: rotate(-20deg);
        }

        .t18-eyebrow {
            width: fit-content;
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 24px;
        }

        .t18-auth-aside h1 {
            font-size: clamp(2rem, 4vw, 2.75rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .t18-auth-aside p {
            font-size: 1rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 32px;
            max-width: 320px;
        }

        .t18-benefits-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .t18-benefit-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #fff;
        }

        .t18-benefit-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        /* ==================== RIGHT SIDE (FORM) ==================== */
        .t18-auth-form {
            padding: 32px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (min-width: 860px) {
            .t18-auth-form {
                padding: 50px;
            }
        }

        .t18-brand-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 32px;
        }

        .t18-logo-box {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: var(--t18-primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 900;
            box-shadow: 0 8px 16px -4px var(--t18-primary);
        }

        .t18-logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 14px;
            background: #fff;
        }

        .t18-brand-text p:first-child {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            margin: 0 0 2px;
        }

        .t18-brand-text p:last-child {
            font-size: 1.1rem;
            font-weight: 800;
            color: #111827;
            margin: 0;
        }

        .t18-form-title {
            font-size: 1.75rem;
            font-weight: 900;
            color: #111827;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .t18-form-subtitle {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .t18-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 8px;
        }

        .t18-input {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            background: #f9fafb;
            color: #111827;
            font-size: 0.95rem;
            font-weight: 500;
            outline: none;
            transition: all 0.2s ease;
        }

        .t18-input:focus {
            border-color: var(--t18-primary);
            background: #fff;
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--t18-primary) 15%, transparent);
        }

        .t18-btn {
            width: 100%;
            height: 52px;
            background: var(--t18-primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px -6px var(--t18-primary);
            margin-top: 10px;
        }

        .t18-btn:hover {
            background: var(--t18-hover);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px -6px var(--t18-hover);
        }

        .t18-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .t18-msg-box {
            padding: 14px 16px;
            border-radius: 12px;
            background: color-mix(in srgb, var(--t18-primary) 8%, #fff);
            border: 1px solid color-mix(in srgb, var(--t18-primary) 20%, transparent);
            color: var(--t18-primary);
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .t18-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #9ca3af;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .t18-divider::before,
        .t18-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .t18-security-text {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #9ca3af;
            font-size: 0.75rem;
            margin-bottom: 24px;
        }

        .t18-footer-text {
            text-align: center;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .t18-footer-text a {
            color: var(--t18-primary);
            font-weight: 800;
            text-decoration: none;
        }

        .t18-footer-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <!-- 3D Background Shapes -->
    <div class="t18-shape-wrap">
        <div class="t18-shape t18-shape-1"></div>
        <div class="t18-shape t18-shape-2"></div>
        <div class="t18-shape t18-shape-3"></div>
    </div>

    <!-- Main Container -->
    <main class="px-4 py-8 sm:py-12 relative flex-1 flex items-center justify-center">
        <div class="t18-auth-container">

            <!-- Left Side: Info -->
            <aside class="t18-auth-aside">
                <div class="t18-aside-shape"></div>
                <div class="t18-aside-shape t18-aside-shape-2"></div>

                <span class="t18-eyebrow">Welcome to <?= htmlspecialchars($storeName) ?></span>
                <h1>Your next<br>favourite thing<br>is waiting.</h1>
                <p>Create your account for a smoother shopping experience. Keep your details ready and get started in just a few steps.</p>

                <div class="t18-benefits-list">
                    <div class="t18-benefit-item">
                        <span class="t18-benefit-icon"><i class="bi bi-check-lg"></i></span>
                        Quick and easy registration
                    </div>
                    <div class="t18-benefit-item">
                        <span class="t18-benefit-icon"><i class="bi bi-envelope"></i></span>
                        OTP verification for security
                    </div>
                    <div class="t18-benefit-item">
                        <span class="t18-benefit-icon"><i class="bi bi-heart"></i></span>
                        A convenient shopping experience
                    </div>
                </div>
            </aside>

            <!-- Right Side: Form -->
            <section class="t18-auth-form">

                <div class="t18-brand-header">
                    <div class="t18-logo-box">
                        <?php if (getSettings("logo")) : ?>
                            <img src="<?= UPLOADS_URL . getSettings("logo") ?>" alt="Logo">
                        <?php else : ?>
                            <?= htmlspecialchars(substr($storeName, 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="t18-brand-text">
                        <p>Join</p>
                        <p><?= htmlspecialchars($storeName) ?></p>
                    </div>
                </div>

                <h2 class="t18-form-title">Create your account</h2>
                <p class="t18-form-subtitle">Enter your details below to get started.</p>

                <form id="form" class="space-y-4">

                    <!-- Step 1 -->
                    <div id="step1">
                        <div class="mb-4">
                            <label for="name" class="t18-label">Full Name</label>
                            <input type="text" name="name" id="name" placeholder="Enter your full name" autocomplete="name" required class="t18-input">
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="t18-label">Phone Number</label>
                            <input type="tel" name="phone[main]" id="phone" placeholder="Enter your phone number" autocomplete="tel" required class="t18-input">
                        </div>

                        <div class="mb-4">
                            <label for="email" class="t18-label">Email Address <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <input type="email" name="email" id="email" placeholder="Enter your email address" autocomplete="email" class="t18-input">
                        </div>

                        <div class="mb-5">
                            <label for="password" class="t18-label">Password</label>
                            <input type="password" name="password" id="password" placeholder="Create a password" autocomplete="new-password" required class="t18-input">
                        </div>

                        <button type="button" id="sendOtp" class="t18-btn">
                            CONTINUE WITH OTP <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>

                    <!-- Step 2 -->
                    <div id="step2" style="display:none;">
                        <div id="msg" class="t18-msg-box"></div>

                        <div class="mb-5">
                            <label for="otp" class="t18-label">Verification Code</label>
                            <input type="number" name="otp" id="otp" placeholder="Enter the OTP you received" inputmode="numeric" autocomplete="one-time-code" required class="t18-input">
                        </div>

                        <button type="button" id="create" class="t18-btn">
                            CREATE ACCOUNT <i class="bi bi-check2-circle"></i>
                        </button>
                    </div>

                </form>

                <div class="t18-divider">Secure Registration</div>

                <div class="t18-security-text">
                    <i class="bi bi-shield-lock-fill"></i> Your information is submitted securely.
                </div>

                <!-- Login Link -->
                <p class="t18-footer-text">
                    Already have an account?
                    <a href="<?= htmlspecialchars($storeUrl) ?>login<?= isset($_GET['step']) ? '?step' : '' ?>">
                        Sign in
                    </a>
                </p>

            </section>
        </div>
    </main>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

    <!-- Phone Number Configuration -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInputField = document.querySelector("#phone");

            if (phoneInputField && typeof window.intlTelInput === "function") {
                window.intlTelInput(phoneInputField, {
                    separateDialCode: true,
                    onlyCountries: ['in'],
                    preferredCountries: ["<?= strtolower(htmlspecialchars((string) getSettings("country"), ENT_QUOTES, 'UTF-8')) ?>"],
                    hiddenInput: "full",
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
                });
            }
        });
    </script>

    <!-- Registration JavaScript -->
    <script src="<?= APP_URL ?>shop/javascripts/register.js"></script>

</body>

</html>