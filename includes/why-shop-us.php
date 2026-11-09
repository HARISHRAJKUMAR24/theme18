<!-- Why Shop With Us Start -->
<?php
$whyPrimary = getData("color",       "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#9333ea';
$whyHover   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899';
?>
<section class="t18-why-section"
         style="--t18-why-primary: <?= htmlspecialchars($whyPrimary) ?>; --t18-why-hover: <?= htmlspecialchars($whyHover) ?>;">

    <div class="t18-why-bg" aria-hidden="true">
        <span class="t18-why-orb t18-why-orb-1"></span>
        <span class="t18-why-orb t18-why-orb-2"></span>

        <span class="t18-why-sphere t18-why-sphere-1"></span>
        <span class="t18-why-sphere t18-why-sphere-2"></span>
        <span class="t18-why-sphere t18-why-sphere-3"></span>
        <span class="t18-why-sphere t18-why-sphere-4"></span>
        <span class="t18-why-sphere t18-why-sphere-5"></span>

        <span class="t18-why-torus t18-why-torus-1"></span>
        <span class="t18-why-torus t18-why-torus-2"></span>
        <span class="t18-why-torus t18-why-torus-3"></span>

        <span class="t18-why-cube t18-why-cube-1"></span>
        <span class="t18-why-cube t18-why-cube-2"></span>

        <span class="t18-why-dot t18-why-dot-1"></span>
        <span class="t18-why-dot t18-why-dot-2"></span>
        <span class="t18-why-dot t18-why-dot-3"></span>
        <span class="t18-why-dot t18-why-dot-4"></span>
    </div>

    <div class="t18-why-inner">

        <div class="t18-why-head">
            <span class="t18-why-eyebrow">Why Shop With Us</span>
            <h2 class="t18-why-title">
                <span class="t18-why-title-line">Crafted for a</span>
                <span class="t18-why-title-accent">Better Experience.</span>
            </h2>
        </div>

        <?php
        $img1 = getData("featured_image_1", "seller_banners", "(seller_id = '$sellerId' AND store_id = '$storeId')");
        $img2 = getData("featured_image_2", "seller_banners", "(seller_id = '$sellerId' AND store_id = '$storeId')");
        $img3 = getData("featured_image_3", "seller_banners", "(seller_id = '$sellerId' AND store_id = '$storeId')");

        $card1 = !empty($img1) ? UPLOADS_URL . $img1 : APP_URL . 'assets/image/theme_9_fd.jpg';
        $card2 = !empty($img2) ? UPLOADS_URL . $img2 : APP_URL . 'assets/image/theme_9_s&s.jpg';
        $card3 = !empty($img3) ? UPLOADS_URL . $img3 : APP_URL . 'assets/image/theme_9_hc.jpg';
        ?>

        <div class="t18-why-grid">
            <div class="t18-why-card" style="--i: 0;">
                <div class="t18-why-card-inner">
                    <div class="t18-why-imgwrap">
                        <img src="<?= $card1 ?>" alt="Fast Delivery" loading="lazy">
                        <span class="t18-why-glow"></span>
                    </div>
                </div>
                <span class="t18-why-badge">Fast Delivery</span>
            </div>

            <div class="t18-why-card" style="--i: 1;">
                <div class="t18-why-card-inner">
                    <div class="t18-why-imgwrap">
                        <img src="<?= $card2 ?>" alt="Safe &amp; Secure" loading="lazy">
                        <span class="t18-why-glow"></span>
                    </div>
                </div>
                <span class="t18-why-badge">Safe &amp; Secure</span>
            </div>

            <div class="t18-why-card" style="--i: 2;">
                <div class="t18-why-card-inner">
                    <div class="t18-why-imgwrap">
                        <img src="<?= $card3 ?>" alt="Happy Customers" loading="lazy">
                        <span class="t18-why-glow"></span>
                    </div>
                </div>
                <span class="t18-why-badge">Happy Customers</span>
            </div>
        </div>
    </div>
</section>

<style>
    /* ============================================
       THEME 18 — WHY SHOP WITH US
       Compact padding · single-row on mobile
       ============================================ */
    .t18-why-section {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        padding: 36px 16px 44px; /* reduced top/bottom */
        background:
            radial-gradient(ellipse at 100% 0%,
                color-mix(in srgb, var(--t18-why-primary) 10%, transparent) 0%, transparent 45%),
            radial-gradient(ellipse at 0% 100%,
                color-mix(in srgb, var(--t18-why-hover) 10%, transparent) 0%, transparent 50%),
            #ffffff;
    }
    @media (min-width: 640px) {
        .t18-why-section { padding: 48px 24px 56px; }
    }

    /* ---------- BG LAYER ---------- */
    .t18-why-bg {
        position: absolute;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
    }

    /* soft orbs */
    .t18-why-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(70px);
        opacity: .3;
        animation: t18WhyOrbFloat 16s ease-in-out infinite alternate;
    }
    .t18-why-orb-1 {
        width: 200px; height: 200px;
        top: -60px; left: 8%;
        background: color-mix(in srgb, var(--t18-why-primary) 40%, transparent);
    }
    .t18-why-orb-2 {
        width: 240px; height: 240px;
        bottom: -80px; right: 8%;
        background: color-mix(in srgb, var(--t18-why-hover) 35%, transparent);
        animation-delay: -6s;
    }
    @keyframes t18WhyOrbFloat {
        0%   { transform: translate(0, 0) scale(1); }
        50%  { transform: translate(-16px, 20px) scale(1.05); }
        100% { transform: translate(12px, -14px) scale(.96); }
    }

    /* glossy 3D spheres */
    .t18-why-sphere {
        position: absolute;
        border-radius: 50%;
        background:
            radial-gradient(circle at 32% 28%,
                #ffffff 0%,
                color-mix(in srgb, var(--t18-why-primary) 55%, #ffffff) 45%,
                color-mix(in srgb, var(--t18-why-primary) 90%, #000000) 100%);
        box-shadow:
            0 10px 22px -8px color-mix(in srgb, var(--t18-why-primary) 30%, transparent),
            inset -4px -6px 12px rgba(0, 0, 0, .15),
            inset 4px 5px 10px rgba(255, 255, 255, .7);
        animation: t18WhySphereFloat 9s ease-in-out infinite;
        will-change: transform;
        opacity: .9;
    }
    .t18-why-sphere-1 { width: 36px; height: 36px; top: 8%;  left: 6%;   animation-delay: 0s;    }
    .t18-why-sphere-2 { width: 22px; height: 22px; top: 18%; right: 12%; animation-delay: -2s;   }
    .t18-why-sphere-3 { width: 28px; height: 28px; top: 68%; left: 4%;   animation-delay: -4s;   }
    .t18-why-sphere-4 {
        width: 32px; height: 32px; bottom: 10%; right: 6%;
        background:
            radial-gradient(circle at 32% 28%,
                #ffffff 0%,
                color-mix(in srgb, var(--t18-why-hover) 55%, #ffffff) 45%,
                color-mix(in srgb, var(--t18-why-hover) 90%, #000000) 100%);
        box-shadow:
            0 10px 22px -8px color-mix(in srgb, var(--t18-why-hover) 30%, transparent),
            inset -4px -6px 12px rgba(0, 0, 0, .15),
            inset 4px 5px 10px rgba(255, 255, 255, .7);
        animation-delay: -6s;
    }
    .t18-why-sphere-5 { width: 16px; height: 16px; top: 50%; right: 26%; animation-delay: -1s;   }
    @keyframes t18WhySphereFloat {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50%      { transform: translate(6px, -10px) scale(1.06); }
    }

    /* glossy 3D tori */
    .t18-why-torus {
        position: absolute;
        border-radius: 50%;
        background: transparent;
        border: 6px solid transparent;
        box-shadow:
            0 8px 16px -6px color-mix(in srgb, var(--t18-why-primary) 22%, transparent),
            inset 3px 3px 8px rgba(0, 0, 0, .1),
            inset -3px -3px 8px rgba(255, 255, 255, .55);
        animation: t18WhyTorusSpin 20s linear infinite;
        opacity: .6;
    }
    .t18-why-torus-1 {
        width: 52px; height: 52px;
        top: 14%; left: 30%;
        border-color: color-mix(in srgb, var(--t18-why-primary) 35%, transparent);
    }
    .t18-why-torus-2 {
        width: 40px; height: 40px;
        bottom: 22%; right: 20%;
        border-color: color-mix(in srgb, var(--t18-why-hover) 35%, transparent);
        animation-duration: 24s;
        animation-direction: reverse;
    }
    .t18-why-torus-3 {
        width: 30px; height: 30px;
        top: 55%; left: 46%;
        border-color: color-mix(in srgb, var(--t18-why-primary) 25%, transparent);
        animation-duration: 16s;
    }
    @keyframes t18WhyTorusSpin {
        0%   { transform: rotate(0deg) translate(0, 0); }
        50%  { transform: rotate(180deg) translate(4px, -6px); }
        100% { transform: rotate(360deg) translate(0, 0); }
    }

    /* 3D cubes */
    .t18-why-cube {
        position: absolute;
        width: 22px; height: 22px;
        background: linear-gradient(135deg,
            color-mix(in srgb, var(--t18-why-primary) 55%, #ffffff) 0%,
            color-mix(in srgb, var(--t18-why-primary) 85%, #000000) 100%);
        border-radius: 5px;
        box-shadow:
            0 8px 16px -6px color-mix(in srgb, var(--t18-why-primary) 30%, transparent),
            inset 2px 2px 4px rgba(255, 255, 255, .55),
            inset -2px -3px 5px rgba(0, 0, 0, .2);
        animation: t18WhyCubeFloat 11s ease-in-out infinite;
        transform: rotate(15deg);
        opacity: .85;
    }
    .t18-why-cube-1 { top: 26%; right: 8%; animation-delay: -2s; }
    .t18-why-cube-2 {
        bottom: 18%; left: 16%;
        background: linear-gradient(135deg,
            color-mix(in srgb, var(--t18-why-hover) 55%, #ffffff) 0%,
            color-mix(in srgb, var(--t18-why-hover) 85%, #000000) 100%);
        box-shadow:
            0 8px 16px -6px color-mix(in srgb, var(--t18-why-hover) 30%, transparent),
            inset 2px 2px 4px rgba(255, 255, 255, .55),
            inset -2px -3px 5px rgba(0, 0, 0, .2);
        animation-delay: -5s;
        transform: rotate(-20deg);
        animation-name: t18WhyCubeFloatAlt;
    }
    @keyframes t18WhyCubeFloat {
        0%, 100% { transform: rotate(15deg)  translateY(0); }
        50%      { transform: rotate(25deg)  translateY(-10px); }
    }
    @keyframes t18WhyCubeFloatAlt {
        0%, 100% { transform: rotate(-20deg) translateY(0); }
        50%      { transform: rotate(-30deg) translateY(8px); }
    }

    /* floating dots */
    .t18-why-dot {
        position: absolute;
        width: 6px; height: 6px;
        border-radius: 50%;
        background: color-mix(in srgb, var(--t18-why-primary) 60%, transparent);
        box-shadow: 0 0 10px color-mix(in srgb, var(--t18-why-primary) 40%, transparent);
        animation: t18WhyDotFloat 7s ease-in-out infinite;
    }
    .t18-why-dot-1 { top: 12%; left: 22%; animation-delay: 0s; }
    .t18-why-dot-2 {
        top: 40%; right: 4%;
        background: color-mix(in srgb, var(--t18-why-hover) 60%, transparent);
        box-shadow: 0 0 10px color-mix(in srgb, var(--t18-why-hover) 40%, transparent);
        animation-delay: -2s;
    }
    .t18-why-dot-3 { bottom: 30%; left: 8%; animation-delay: -4s; }
    .t18-why-dot-4 {
        top: 74%; right: 32%;
        background: color-mix(in srgb, var(--t18-why-hover) 60%, transparent);
        box-shadow: 0 0 10px color-mix(in srgb, var(--t18-why-hover) 40%, transparent);
        animation-delay: -6s;
    }
    @keyframes t18WhyDotFloat {
        0%, 100% { transform: translate(0, 0); opacity: .7; }
        50%      { transform: translate(4px, -8px); opacity: 1; }
    }

    /* ---------- INNER ---------- */
    .t18-why-inner {
        position: relative;
        z-index: 2;
        max-width: 1080px;
        margin: 0 auto;
    }

    /* ---------- HEADING ---------- */
    .t18-why-head { text-align: center; margin-bottom: 28px; }
    .t18-why-eyebrow {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: var(--t18-why-primary);
        margin-bottom: 12px;
        padding: 5px 14px;
        border: 1px solid color-mix(in srgb, var(--t18-why-primary) 30%, transparent);
        border-radius: 999px;
        background: color-mix(in srgb, var(--t18-why-primary) 8%, transparent);
        animation: t18WhyFadeUp .7s ease both;
    }
    .t18-why-title {
        margin: 0;
        font-size: clamp(1.6rem, 3.5vw, 2.4rem);
        font-weight: 900;
        letter-spacing: -0.03em;
        line-height: 1.05;
        color: #171717;
        animation: t18WhyFadeUp .7s ease .1s both;
    }
    .t18-why-title-line { display: block; }
    .t18-why-title-accent {
        display: block;
        background: linear-gradient(110deg, var(--t18-why-primary), var(--t18-why-hover));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    @keyframes t18WhyFadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ---------- GRID: single row always ---------- */
    .t18-why-grid {
        display: flex;
        flex-wrap: nowrap;
        align-items: stretch;
        justify-content: center;
        gap: 14px;
        overflow-x: auto;
        padding: 4px 4px 12px;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .t18-why-grid::-webkit-scrollbar { display: none; }

    @media (min-width: 640px) {
        .t18-why-grid {
            gap: 24px;
            max-width: 780px;
            margin: 0 auto;
            overflow: visible;
        }
    }

    /* ---------- CARD ---------- */
    .t18-why-card {
        position: relative;
        flex: 1 1 0;
        min-width: 0;
        max-width: 240px;
        opacity: 0;
        animation:
            t18CardIn .8s cubic-bezier(.22, 1, .36, 1) calc(var(--i, 0) * .12s) forwards,
            t18CardIdleFloat 6s ease-in-out calc(var(--i, 0) * .12s + .8s) infinite;
        will-change: transform;
    }
    @media (max-width: 640px) {
        .t18-why-card {
            flex: 0 0 auto;
            width: 30%;
            min-width: 96px;
            max-width: 140px;
        }
    }

    @keyframes t18CardIn {
        from { opacity: 0; transform: translateY(30px) scale(.94); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes t18CardIdleFloat {
        0%   { transform: translateY(0)     rotate(0deg); }
        25%  { transform: translateY(-6px)  rotate(-1deg); }
        50%  { transform: translateY(-10px) rotate(0deg); }
        75%  { transform: translateY(-6px)  rotate(1deg); }
        100% { transform: translateY(0)     rotate(0deg); }
    }
    .t18-why-card:hover { animation-play-state: paused; }

    .t18-why-card-inner {
        position: relative;
        border-radius: 18px;
        overflow: hidden;
        background: #ffffff;
        box-shadow:
            0 10px 25px -14px color-mix(in srgb, var(--t18-why-primary) 30%, transparent),
            0 4px 10px -6px color-mix(in srgb, var(--t18-why-primary) 15%, transparent),
            inset 0 0 0 1px color-mix(in srgb, var(--t18-why-primary) 15%, transparent);
        transition: box-shadow .4s ease, transform .4s cubic-bezier(.22, 1, .36, 1);
        isolation: isolate;
    }
    @media (min-width: 640px) {
        .t18-why-card-inner { border-radius: 22px; }
    }
    .t18-why-card:hover .t18-why-card-inner {
        transform: scale(1.05);
        box-shadow:
            0 26px 50px -20px color-mix(in srgb, var(--t18-why-primary) 45%, transparent),
            0 12px 22px -10px color-mix(in srgb, var(--t18-why-hover) 30%, transparent),
            inset 0 0 0 1px color-mix(in srgb, var(--t18-why-primary) 30%, transparent);
    }

    .t18-why-imgwrap {
        position: relative;
        aspect-ratio: 1 / 1;
        overflow: hidden;
    }
    .t18-why-imgwrap img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 1.2s cubic-bezier(.22, 1, .36, 1);
    }
    .t18-why-card:hover .t18-why-imgwrap img {
        transform: scale(1.08) rotate(-1deg);
    }

    .t18-why-glow {
        position: absolute;
        inset: 0;
        background: linear-gradient(115deg,
            transparent 35%,
            rgba(255,255,255,.45) 50%,
            transparent 65%);
        transform: translateX(-140%);
        pointer-events: none;
        z-index: 2;
        animation: t18CardShine 5s ease-in-out infinite;
        animation-delay: calc(var(--i, 0) * .4s);
    }
    @keyframes t18CardShine {
        0%   { transform: translateX(-140%); }
        45%  { transform: translateX(140%); }
        100% { transform: translateX(140%); }
    }

    .t18-why-badge {
        position: absolute;
        left: 50%;
        bottom: 10px;
        transform: translateX(-50%);
        padding: 6px 12px;
        background: rgba(255, 255, 255, .95);
        backdrop-filter: blur(12px);
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.3px;
        color: #171717;
        white-space: nowrap;
        box-shadow:
            0 6px 16px rgba(0, 0, 0, .1),
            0 0 0 1px color-mix(in srgb, var(--t18-why-primary) 12%, transparent);
        transition: all .35s cubic-bezier(.22, 1, .36, 1);
        z-index: 3;
    }
    @media (min-width: 640px) {
        .t18-why-badge { font-size: 11px; padding: 7px 14px; bottom: 12px; }
    }
    .t18-why-card:hover .t18-why-badge {
        background: var(--t18-why-primary);
        color: #fff;
        transform: translateX(-50%) translateY(-3px) scale(1.05);
        box-shadow: 0 10px 22px color-mix(in srgb, var(--t18-why-primary) 40%, transparent);
    }

    /* ---------- RESPONSIVE ---------- */
    @media (max-width: 640px) {
        .t18-why-section { padding: 30px 10px 40px; }
        .t18-why-head { margin-bottom: 22px; }
        .t18-why-grid { gap: 10px; }
        .t18-why-orb { filter: blur(50px); opacity: .35; }
        .t18-why-sphere-1 { width: 22px; height: 22px; }
        .t18-why-sphere-2 { width: 14px; height: 14px; }
        .t18-why-sphere-3 { width: 18px; height: 18px; }
        .t18-why-sphere-4 { width: 20px; height: 20px; }
        .t18-why-sphere-5 { width: 10px; height: 10px; }
        .t18-why-torus-1 { width: 30px; height: 30px; border-width: 3px; }
        .t18-why-torus-2 { width: 24px; height: 24px; border-width: 3px; }
        .t18-why-torus-3 { width: 18px; height: 18px; border-width: 2px; }
        .t18-why-cube { width: 14px; height: 14px; border-radius: 3px; }
        .t18-why-dot { width: 4px; height: 4px; }
        .t18-why-badge { font-size: 9px; padding: 4px 8px; bottom: 8px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .t18-why-orb,
        .t18-why-sphere,
        .t18-why-torus,
        .t18-why-cube,
        .t18-why-dot,
        .t18-why-card,
        .t18-why-glow,
        .t18-why-eyebrow,
        .t18-why-title {
            animation: none !important;
            transition: none !important;
            opacity: 1 !important;
        }
        .t18-why-card:hover { transform: none; }
    }
</style>