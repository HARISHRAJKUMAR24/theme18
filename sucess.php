<?php include_once __DIR__ . "/includes/files_includes.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>
    <?php
    $successPrimary = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f';
    $successHover = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899';
    ?>
    <style>
        :root {
            --t18-primary: <?= htmlspecialchars($successPrimary, ENT_QUOTES, 'UTF-8') ?>;
            --t18-hover: <?= htmlspecialchars($successHover, ENT_QUOTES, 'UTF-8') ?>;
            --t18-ink: #24221f;
            --t18-muted: #858078;
            --t18-paper: #f5f4f0;
            --t18-line: #dedbd3;
        }

        body.t18-success-page {
            min-height: 100vh;
            margin: 0;
            background: var(--t18-paper);
            color: var(--t18-ink);
            font-family: Arial, Helvetica, sans-serif;
        }

        .t18-success-wrap {
            position: relative;
            overflow: hidden;
            padding: 54px 18px 64px;
            background: var(--t18-paper);
        }

        .t18-success-shell {
            width: 100%;
            max-width: 760px;
            margin: 0 auto;
        }

        .t18-success-kicker {
            margin: 0 0 12px;
            color: var(--t18-muted);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 3px;
            text-align: center;
            text-transform: uppercase;
        }

        .t18-success-heading {
            margin: 0;
            color: var(--t18-ink);
            font-family: Georgia, 'Times New Roman', serif;
            font-size: clamp(30px, 5vw, 48px);
            font-weight: 400;
            line-height: 1.12;
            text-align: center;
            letter-spacing: -1.2px;
        }

        .t18-success-heading span {
            color: var(--t18-primary);
        }

        .t18-success-subtitle {
            max-width: 470px;
            margin: 14px auto 34px;
            color: var(--t18-muted);
            font-size: 13px;
            line-height: 1.8;
            text-align: center;
        }

        .t18-order-ticket {
            position: relative;
            max-width: 620px;
            margin: 0 auto;
            padding: 34px 42px 30px;
            border: 1px solid var(--t18-line);
            background: #fffefa;
            text-align: center;
        }

        .t18-ticket-topline {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--t18-primary);
        }

        .t18-check-mark {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 76px;
            height: 76px;
            margin: 0 auto 20px;
            border: 1px solid var(--t18-primary);
            color: var(--t18-primary);
            background: #fffefa;
        }

        .t18-check-mark::before,
        .t18-check-mark::after {
            position: absolute;
            content: '';
            inset: 6px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 25%, transparent);
        }

        .t18-check-mark svg {
            width: 34px;
            height: 34px;
            position: relative;
            z-index: 1;
        }

        .t18-ticket-label {
            margin: 0 0 7px;
            color: var(--t18-muted);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
        }

        .t18-customer-name {
            margin: 0 0 8px;
            color: var(--t18-ink);
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 22px;
            font-weight: 400;
        }

        .t18-confirmed-title {
            margin: 0;
            color: var(--t18-primary);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .t18-ticket-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 26px 0 20px;
            color: #aaa49a;
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .t18-ticket-divider::before,
        .t18-ticket-divider::after {
            flex: 1;
            height: 1px;
            background: var(--t18-line);
            content: '';
        }

        .t18-order-number {
            display: inline-block;
            padding: 10px 18px;
            border: 1px dashed #c9c3b8;
            color: var(--t18-ink);
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
            text-decoration: none;
        }

        .t18-order-note {
            margin: 17px 0 0;
            color: var(--t18-muted);
            font-size: 12px;
            line-height: 1.7;
        }

        .t18-success-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 26px;
        }

        .t18-action-btn {
            display: inline-flex;
            min-height: 46px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 0 22px;
            border: 1px solid var(--t18-primary);
            background: var(--t18-primary);
            color: #fff !important;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.4px;
            text-decoration: none !important;
            text-transform: uppercase;
            transition: background .2s ease, color .2s ease, transform .2s ease;
        }

        .t18-action-btn:hover {
            transform: translateY(-2px);
            background: var(--t18-hover);
            border-color: var(--t18-hover);
            color: #fff !important;
        }

        .t18-action-btn svg {
            width: 16px;
            height: 16px;
            flex: 0 0 auto;
        }

        .t18-action-btn.is-whatsapp {
            border-color: #277a4b;
            background: #277a4b;
        }

        .t18-action-btn.is-whatsapp:hover {
            border-color: #1f633d;
            background: #1f633d;
        }

        .t18-bottom-note {
            margin: 25px 0 0;
            color: var(--t18-muted);
            font-size: 10px;
            letter-spacing: .5px;
            line-height: 1.7;
            text-align: center;
        }

        .t18-bottom-note span {
            color: var(--t18-primary);
            font-weight: 700;
        }

        .t18-confetti-layer {
            position: absolute;
            inset: 0 0 auto;
            height: 260px;
            overflow: hidden;
            pointer-events: none;
        }

        .t18-confetti {
            position: absolute;
            top: -18px;
            width: 5px;
            height: 12px;
            background: var(--t18-primary);
            opacity: 0;
            animation: t18-confetti-fall 3.8s ease-in infinite;
        }

        .t18-confetti:nth-child(1) {
            left: 12%;
            animation-delay: .1s;
        }

        .t18-confetti:nth-child(2) {
            left: 27%;
            animation-delay: 1.1s;
            background: #c9a96e;
        }

        .t18-confetti:nth-child(3) {
            left: 49%;
            animation-delay: .6s;
        }

        .t18-confetti:nth-child(4) {
            left: 72%;
            animation-delay: 1.5s;
            background: #9d988d;
        }

        .t18-confetti:nth-child(5) {
            left: 88%;
            animation-delay: .3s;
        }

        @keyframes t18-confetti-fall {
            0% {
                transform: translateY(0) rotate(0);
                opacity: 0;
            }

            12% {
                opacity: .7;
            }

            100% {
                transform: translateY(280px) rotate(250deg);
                opacity: 0;
            }
        }

        @media (max-width: 600px) {
            .t18-success-wrap {
                padding: 38px 14px 46px;
            }

            .t18-success-subtitle {
                margin: 11px auto 25px;
                font-size: 12px;
            }

            .t18-order-ticket {
                padding: 29px 18px 24px;
            }

            .t18-check-mark {
                width: 66px;
                height: 66px;
                margin-bottom: 17px;
            }

            .t18-customer-name {
                font-size: 20px;
            }

            .t18-success-actions {
                flex-direction: column;
            }

            .t18-action-btn {
                width: 100%;
                min-height: 45px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .t18-confetti {
                animation: none;
                display: none;
            }

            .t18-action-btn {
                transition: none;
            }
        }
    </style>
</head>
<?php
if (isset($_GET['order_id']) && getData("id", "seller_orders", "order_id = '{$_GET['order_id']}' AND customer_id = '$customerId'")) {
    $order_id = $_GET['order_id'];
    $name = getData("name", "seller_order_address", "order_id = '$order_id' AND type = 'billing'");
} else {
    redirect($storeUrl);
}
?>

<body class="t18-success-page" style="--t18-primary: <?= htmlspecialchars($successPrimary, ENT_QUOTES, 'UTF-8') ?>; --t18-hover: <?= htmlspecialchars($successHover, ENT_QUOTES, 'UTF-8') ?>;">
    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <main class="t18-success-wrap">
        <div class="t18-confetti-layer" aria-hidden="true">
            <span class="t18-confetti"></span>
            <span class="t18-confetti"></span>
            <span class="t18-confetti"></span>
            <span class="t18-confetti"></span>
            <span class="t18-confetti"></span>
        </div>

        <div class="t18-success-shell">
            <p class="t18-success-kicker">The New Collection · Order Update</p>
            <h1 class="t18-success-heading">A little joy is<br><span>on its way.</span></h1>
            <p class="t18-success-subtitle">
                Thank you for shopping with us. Your order is confirmed and our team is getting it ready for you.
            </p>

            <section class="t18-order-ticket" aria-label="Order confirmation">
                <div class="t18-ticket-topline"></div>
                <div class="t18-check-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m5 12.5 4.2 4.2L19.5 6.8"></path>
                    </svg>
                </div>

                <p class="t18-ticket-label">Thank you for your order</p>
                <h2 class="t18-customer-name">Hey <?= htmlspecialchars($name ?? 'there', ENT_QUOTES, 'UTF-8') ?>,</h2>
                <p class="t18-confirmed-title">Your order is confirmed</p>

                <div class="t18-ticket-divider">Order Reference</div>
                <a class="t18-order-number" href="<?= htmlspecialchars($storeUrl . "order?id=" . rawurlencode($order_id), ENT_QUOTES, 'UTF-8') ?>">
                    #<?= htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8') ?>
                </a>
                <p class="t18-order-note">We’ll send you a shipping update as soon as your order is on its way.</p>

                <div class="t18-success-actions">
                    <a href="<?= htmlspecialchars($storeUrl . "order?id=" . rawurlencode($order_id), ENT_QUOTES, 'UTF-8') ?>" class="t18-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 12a9 9 0 1 0 2.6-6.4L3 8"></path>
                            <path d="M3 3v5h5"></path>
                            <path d="M12 7v5l3 2"></path>
                        </svg>
                        Track Your Order
                    </a>

                    <?php if (!empty(getSettings("whatsapp_payment"))) : ?>
                        <a href="<?= htmlspecialchars($storeUrl . "whatsapp-order?id=" . rawurlencode($order_id), ENT_QUOTES, 'UTF-8') ?>" class="t18-action-btn is-whatsapp">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M20.5 3.5A11.9 11.9 0 0 0 12.05 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.9 11.9 0 0 0 5.68 1.45h.01c6.55 0 11.89-5.34 11.89-11.9 0-3.18-1.24-6.17-3.44-8.4ZM12.05 21.8h-.01a9.9 9.9 0 0 1-5.04-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 0 1-1.51-5.27c0-5.45 4.44-9.89 9.9-9.89a9.83 9.83 0 0 1 7 2.9 9.83 9.83 0 0 1 2.89 6.99c0 5.46-4.44 9.9-9.89 9.9Zm5.43-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.23-.64.08-.3-.15-1.26-.47-2.4-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.91-2.21-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.06 2.88 1.21 3.08c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.42-.07-.12-.27-.2-.57-.35Z"></path>
                            </svg>
                            WhatsApp Message
                        </a>
                    <?php endif; ?>
                </div>
            </section>

            <p class="t18-bottom-note">Made with care. Thank you for choosing <span>our store.</span></p>
        </div>
    </main>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>
</body>

</html>