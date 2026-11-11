
<?php
include_once __DIR__ . "/includes/files_includes.php";

$primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f';
$hover_color = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>

    <style>
        :root {
            --t18-primary: <?= htmlspecialchars($primary_color, ENT_QUOTES, 'UTF-8') ?>;
            --t18-hover: <?= htmlspecialchars($hover_color, ENT_QUOTES, 'UTF-8') ?>;
        }

        * {
            box-sizing: border-box;
        }

        .t18-track-page {
            min-height: 75vh;
            padding: 65px 18px;
            background:
                radial-gradient(ellipse at 10% 20%, color-mix(in srgb, var(--t18-primary) 9%, transparent), transparent 40%),
                radial-gradient(ellipse at 90% 80%, color-mix(in srgb, var(--t18-hover) 9%, transparent), transparent 40%),
                #f8f7fb;
            overflow: hidden;
        }

        .t18-track-container {
            max-width: 1050px;
            margin: auto;
            perspective: 1400px;
        }

        .t18-track-card {
            display: grid;
            grid-template-columns: .95fr 1.05fr;
            background: white;
            border: 1px solid rgba(255,255,255,.9);
            border-radius: 28px;
            overflow: hidden;
            transform-style: preserve-3d;
            box-shadow:
                0 30px 70px rgba(38, 24, 65, .10),
                0 8px 20px rgba(38, 24, 65, .05);
            animation: t18-card-enter .8s cubic-bezier(.2,.8,.2,1) both;
            transition: transform .4s ease, box-shadow .4s ease;
        }

        @keyframes t18-card-enter {
            from {
                opacity: 0;
                transform: translateY(35px) rotateX(5deg);
            }
            to {
                opacity: 1;
                transform: translateY(0) rotateX(0);
            }
        }

        .t18-track-left {
            position: relative;
            isolation: isolate;
            min-height: 490px;
            padding: 45px 38px;
            color: white;
            background: linear-gradient(
                145deg,
                var(--t18-primary),
                var(--t18-hover)
            );
            overflow: hidden;
        }

        .t18-track-left::before {
            content: "";
            position: absolute;
            width: 320px;
            height: 320px;
            right: -140px;
            top: -130px;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 50%;
            box-shadow:
                0 0 0 25px rgba(255,255,255,.035),
                0 0 0 55px rgba(255,255,255,.025);
            z-index: -1;
        }

        .t18-track-left::after {
            content: "";
            position: absolute;
            width: 250px;
            height: 250px;
            left: -150px;
            bottom: -120px;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 50%;
            box-shadow: 0 0 0 30px rgba(255,255,255,.04);
            z-index: -1;
        }

        .t18-track-kicker {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 10px 15px;
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 50px;
            background: rgba(255,255,255,.12);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.7px;
        }

        .t18-live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #b6ffd6;
            box-shadow: 0 0 12px #b6ffd6;
        }

        .t18-track-title {
            margin-top: 35px;
            max-width: 360px;
            font-size: clamp(32px, 4vw, 46px);
            line-height: 1.12;
            font-weight: 800;
            letter-spacing: -1.5px;
            color: white;
        }

        .t18-track-title span {
            color: rgba(255,255,255,.65);
        }

        .t18-track-description {
            margin-top: 18px;
            max-width: 330px;
            color: rgba(255,255,255,.78);
            font-size: 13px;
            line-height: 1.9;
        }

        /* Simple responsive parcel icon */
        .t18-parcel-stage {
            width: 100%;
            height: 165px;
            margin: 18px auto 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .t18-simple-parcel {
            width: clamp(90px, 22vw, 125px);
            height: clamp(90px, 22vw, 125px);
            color: #fff;
            opacity: .95;
        }

        .t18-simple-parcel .parcel-body {
            fill: rgba(255,255,255,.18);
            stroke: #fff;
            stroke-width: 1.6;
            stroke-linejoin: round;
        }

        .t18-simple-parcel .parcel-detail {
            fill: none;
            stroke: #fff;
            stroke-width: 1.6;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .t18-left-footer {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 12px;
            padding-top: 22px;
            border-top: 1px solid rgba(255,255,255,.2);
        }

        .t18-left-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            background: rgba(255,255,255,.17);
        }

        .t18-left-icon svg {
            width: 21px;
            height: 21px;
        }

        .t18-left-footer strong {
            display: block;
            font-size: 12px;
        }

        .t18-left-footer small {
            display: block;
            margin-top: 4px;
            color: rgba(255,255,255,.68);
            font-size: 10px;
        }

        /* Right form */

        .t18-track-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 55px 48px;
            background: linear-gradient(145deg, #fff, #fcfaff);
        }

        .t18-form-label {
            color: var(--t18-primary);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2.5px;
        }

        .t18-form-title {
            margin-top: 12px;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
            color: #20192c;
        }

        .t18-form-description {
            margin-top: 10px;
            color: #8b8496;
            font-size: 13px;
            line-height: 1.8;
        }

        .t18-track-form {
            margin-top: 38px;
        }

        .t18-track-form label {
            display: block;
            margin-bottom: 11px;
            color: #373044;
            font-size: 12px;
            font-weight: 700;
        }

        .t18-input-wrap {
            position: relative;
            transform: translateZ(0);
        }

        .t18-input-icon {
            position: absolute;
            top: 50%;
            left: 17px;
            width: 20px;
            height: 20px;
            color: #a29aac;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .t18-track-input {
            width: 100%;
            height: 57px;
            padding: 0 15px 0 49px;
            border: 1px solid #e9e3f0;
            border-radius: 13px;
            background: #faf9fc;
            color: #282132;
            font-size: 13px;
            outline: none;
            transition: .25s ease;
        }

        .t18-track-input:focus {
            border-color: var(--t18-primary);
            background: white;
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--t18-primary) 12%, transparent);
        }

        .t18-track-input::placeholder {
            color: #b5afbd;
        }

        /* Track button — same style as Theme 18 Register button */

        .t18-track-button {
            width: 100%;
            height: 52px;
            margin-top: 10px;
            background: var(--t18-primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: .95rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all .3s ease;
            box-shadow: 0 8px 20px -6px var(--t18-primary);
        }

        .t18-track-button:hover {
            background: var(--t18-hover);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px -6px var(--t18-hover);
        }

        .t18-track-button:active {
            transform: translateY(0);
        }

        .t18-track-button:disabled {
            opacity: .7;
            cursor: not-allowed;
            transform: none;
        }

        .t18-track-button svg {
            width: 19px;
            height: 19px;
        }

        .t18-track-help {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-top: 26px;
            padding: 15px;
            border: 1px solid #eee9f3;
            border-radius: 12px;
            background: #fff;
        }

        .t18-track-help svg {
            flex-shrink: 0;
            width: 17px;
            height: 17px;
            color: var(--t18-primary);
        }

        .t18-track-help p {
            color: #918a9c;
            font-size: 11px;
            line-height: 1.8;
        }

        .t18-bottom-text {
            margin-top: 25px;
            text-align: center;
            color: #a29baa;
            font-size: 10px;
        }

        .t18-bottom-text span {
            color: var(--t18-primary);
            font-weight: 800;
        }

        @media (max-width: 767px) {
            .t18-track-page {
                padding: 30px 13px 45px;
            }

            .t18-track-card {
                grid-template-columns: 1fr;
                border-radius: 21px;
            }

            .t18-track-left {
                min-height: auto;
                padding: 30px 25px 20px;
            }

            .t18-track-title {
                margin-top: 24px;
                font-size: 34px;
            }

            .t18-track-description {
                font-size: 12px;
            }

            .t18-parcel-stage {
                height: 135px;
                margin: 4px auto 0;
            }

            .t18-track-right {
                padding: 34px 23px;
            }

            .t18-form-title {
                font-size: 27px;
            }

            .t18-track-form {
                margin-top: 28px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .t18-track-card {
                animation: none;
            }

            .t18-track-button,
            .t18-track-input {
                transition: none;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-white font-sans text-slate-900">

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <main class="t18-track-page">
        <div class="t18-track-container">

            <div class="t18-track-card">

                <!-- LEFT 3D PANEL -->
                <section class="t18-track-left">

                    <div class="relative">
                        <div class="t18-track-kicker">
                            <span class="t18-live-dot"></span>
                            ORDER UPDATES
                        </div>

                        <h1 class="t18-track-title">
                            Your order,<br>
                            <span>within reach.</span>
                        </h1>

                        <p class="t18-track-description">
                            Every delivery has a destination. Discover where your order is and stay connected with its journey.
                        </p>
                    </div>

                    <!-- Simple parcel icon -->
                    <div class="t18-parcel-stage" aria-hidden="true">
                        <svg class="t18-simple-parcel" viewBox="0 0 120 120" role="img">
                            <path class="parcel-body" d="M18 35 60 14l42 21v51L60 108 18 86V35Z"/>
                            <path class="parcel-detail" d="M18 35 60 57l42-22M60 57v51M39 25l42 22v18"/>
                            <path class="parcel-detail" d="m39 25 42-11 20 21M39 25 18 35"/>
                        </svg>
                    </div>

                    <div class="t18-left-footer">
                        <div class="t18-left-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9Z"/>
                                <path d="m3.5 7.7 8.5 4.5 8.5-4.5M12 12.2V21"/>
                            </svg>
                        </div>

                        <div>
                            <strong>Order tracking</strong>
                            <small>Quick access to your order details</small>
                        </div>
                    </div>

                </section>

                <!-- RIGHT TRACKING FORM -->
                <section class="t18-track-right">

                    <div>
                        <p class="t18-form-label">TRACK AN ORDER</p>

                        <h2 class="t18-form-title">
                            Find your order
                        </h2>

                        <p class="t18-form-description">
                            Enter your order ID or invoice number to view your latest order details.
                        </p>
                    </div>

                    <form
                        class="t18-track-form"
                        action="<?= htmlspecialchars($storeUrl . 'order', ENT_QUOTES, 'UTF-8') ?>"
                        method="get">

                        <label for="order_id">
                            Order ID / Invoice number
                        </label>

                        <div class="t18-input-wrap">

                            <svg class="t18-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="5" y="3.5" width="14" height="17" rx="2"/>
                                <path d="M8.5 8h7M8.5 12h7M8.5 16h4"/>
                            </svg>

                            <input
                                type="text"
                                id="order_id"
                                name="id"
                                required
                                placeholder="e.g. 12345 or invoice number"
                                autocomplete="off"
                                class="t18-track-input">
                        </div>

                        <button type="submit" class="t18-track-button">
                            Track My Order

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M13 6l6 6-6 6"/>
                            </svg>
                        </button>

                    </form>

                    <div class="t18-track-help">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 11v5M12 7.5h.01"/>
                        </svg>

                        <p>
                            Your order ID or invoice number is available in your order confirmation.
                        </p>
                    </div>

                    <p class="t18-bottom-text">
                        Shopping made special. <span>Every order matters.</span>
                    </p>

                </section>

            </div>
        </div>
    </main>

    <script>
        const orderIdInput = document.getElementById("order_id");

        if (orderIdInput) {
            orderIdInput.addEventListener("input", function () {
                this.value = this.value.replace(/#/g, "");
            });
        }
    </script>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

</body>
</html>
