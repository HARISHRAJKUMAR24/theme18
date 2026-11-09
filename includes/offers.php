<!-- Special Offer Section Start -->
<?php
    $offerSlides = [];
    for ($i = 1; $i <= 2; $i++) {
        $imgColumn = "offer_image_$i";
        $img = getData($imgColumn, "seller_banners", "(seller_id = '$sellerId' AND store_id = '$storeId')");
        if (!empty($img)) {
            $offerSlides[] = $img;
        }
    }

    if (!empty($offerSlides)):
        $primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f';
        $hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899';
        $slideCount    = count($offerSlides);
    ?>
    <section class="t18-offer-section">
        <div class="t18-offer-bg" aria-hidden="true">
            <span class="t18-offer-orb orb-a"></span>
            <span class="t18-offer-orb orb-b"></span>
            <span class="t18-offer-orb orb-c"></span>
        </div>

        <div class="t18-offer-inner">

            <!-- HEADING -->
            <div class="t18-offer-head">
                <div class="t18-offer-head-left">
                    <span class="t18-offer-eyebrow">Limited Time Only</span>
                    <h2 class="t18-offer-title">
                        <span class="t18-offer-title-line">Flash</span>
                        <span class="t18-offer-title-accent">Deals.</span>
                    </h2>
                    <p class="t18-offer-subtitle">
                        Handpicked offers, refreshed daily. Grab them before they're gone.
                    </p>
                </div>
                <div class="t18-offer-counter">
                    <span class="t18-offer-counter-num"><?= str_pad($slideCount, 2, '0', STR_PAD_LEFT) ?></span>
                    <span class="t18-offer-counter-label">Live<br>Deals</span>
                </div>
            </div>

            <!-- STAGE -->
            <div class="t18-offer-stage" id="offerWrapper">
                <div class="t18-offer-track" id="offerTrack">

                    <?php foreach ($offerSlides as $index => $slideImg): ?>
                        <div class="t18-offer-slide <?= $index === 0 ? 'is-active' : '' ?>" data-index="<?= $index ?>">

                            <!-- MEDIA — big image -->
                            <a href="<?= $storeUrl ?>shop-all" class="t18-offer-media">
                                <img src="<?= UPLOADS_URL . $slideImg ?>" alt="Offer <?= $index + 1 ?>">
                                <span class="t18-offer-tag">
                                    <span class="t18-offer-tag-dot"></span>
                                    Offer <?= $index + 1 ?> of <?= $slideCount ?>
                                </span>
                                <span class="t18-offer-shine"></span>
                            </a>

                            <!-- INFO CARD — floating glass panel -->
                            <div class="t18-offer-info">
                                <div class="t18-offer-info-inner">
                                  
                                    <h3 class="t18-offer-card-title">
                                        Save big on this<br>limited drop.
                                    </h3>
                                    <p class="t18-offer-card-desc">
                                        Curated deals with real value. Shop now and get more for less.
                                    </p>
                                    <a href="<?= $storeUrl ?>shop-all" class="t18-offer-btn">
                                        <span>Grab this deal</span>
                                        <span class="t18-offer-btn-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="5" y1="12" x2="19" y2="12"/>
                                                <polyline points="12 5 19 12 12 19"/>
                                            </svg>
                                        </span>
                                    </a>
                                </div>
                                <div class="t18-offer-panel-glow" aria-hidden="true"></div>
                            </div>

                        </div>
                    <?php endforeach; ?>

                </div>
            </div>

            <!-- NAV -->
            <div class="t18-offer-nav">
                <div class="t18-offer-progress">
                    <?php foreach ($offerSlides as $i => $s): ?>
                        <button class="t18-offer-progress-bar <?= $i === 0 ? 'is-active' : '' ?>"
                                data-index="<?= $i ?>"
                                aria-label="Go to offer <?= $i + 1 ?>">
                            <span class="t18-offer-progress-fill"></span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="t18-offer-arrows">
                    <button id="prevOffer" class="t18-offer-arrow" aria-label="Previous">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </button>
                    <button id="toggleAutoplay" class="t18-offer-arrow t18-offer-play" aria-label="Play/Pause">
                        <svg class="t18-ico-pause" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                             viewBox="0 0 24 24" fill="currentColor">
                            <rect x="6" y="4" width="4" height="16"/>
                            <rect x="14" y="4" width="4" height="16"/>
                        </svg>
                        <svg class="t18-ico-play" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                             viewBox="0 0 24 24" fill="currentColor" style="display:none">
                            <polygon points="6 4 20 12 6 20"/>
                        </svg>
                    </button>
                    <button id="nextOffer" class="t18-offer-arrow" aria-label="Next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>
            </div>

        </div>
    </section>

    <style>
        /* ============================================
           THEME 18 — SPECIAL OFFERS
           Unique editorial + floating glass panel
           ============================================ */

        .t18-offer-section {
            --t18-primary: <?= htmlspecialchars($primary_color) ?>;
            --t18-hover: <?= htmlspecialchars($hover_color) ?>;
            position: relative;
            isolation: isolate;
            overflow: hidden;
            padding: 60px 16px 70px;
            background:
                radial-gradient(ellipse at 100% 0%, color-mix(in srgb, var(--t18-primary) 8%, transparent), transparent 55%),
                radial-gradient(ellipse at 0% 100%, color-mix(in srgb, var(--t18-hover) 8%, transparent), transparent 55%),
                #ffffff;
        }
        @media (min-width: 640px) {
            .t18-offer-section { padding: 80px 24px 90px; }
        }

        /* soft orbs in bg */
        .t18-offer-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }
        .t18-offer-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .55;
        }
        .t18-offer-orb.orb-a {
            width: 280px; height: 280px;
            top: -80px; left: 10%;
            background: color-mix(in srgb, var(--t18-primary) 40%, transparent);
        }
        .t18-offer-orb.orb-b {
            width: 340px; height: 340px;
            bottom: -120px; right: 5%;
            background: color-mix(in srgb, var(--t18-hover) 35%, transparent);
        }
        .t18-offer-orb.orb-c {
            width: 180px; height: 180px;
            top: 45%; right: 30%;
            background: color-mix(in srgb, var(--t18-primary) 25%, transparent);
        }

        .t18-offer-inner {
            position: relative;
            z-index: 1;
            max-width: 1320px;
            margin: 0 auto;
        }

        /* ---------- HEADING ---------- */
        .t18-offer-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        .t18-offer-eyebrow {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--t18-primary);
            margin-bottom: 14px;
            padding: 4px 12px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 30%, transparent);
            border-radius: 999px;
            background: color-mix(in srgb, var(--t18-primary) 6%, transparent);
        }
        .t18-offer-title {
            margin: 0;
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 900;
            letter-spacing: -0.03em;
            line-height: 1;
            color: #171717;
        }
        .t18-offer-title-line { display: block; }
        .t18-offer-title-accent {
            display: block;
            background: linear-gradient(120deg, var(--t18-primary), var(--t18-hover));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .t18-offer-subtitle {
            margin: 16px 0 0;
            font-size: 14px;
            color: #6b7280;
            max-width: 460px;
            line-height: 1.6;
        }

        /* Counter — floating stat block */
        .t18-offer-counter {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            background: #fff;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 20%, transparent);
            border-radius: 20px;
            box-shadow: 0 12px 32px -12px color-mix(in srgb, var(--t18-primary) 25%, transparent);
            flex-shrink: 0;
        }
        .t18-offer-counter-num {
            font-size: 32px;
            font-weight: 900;
            line-height: 1;
            color: var(--t18-primary);
            letter-spacing: -0.03em;
        }
        .t18-offer-counter-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #9ca3af;
            line-height: 1.3;
        }

        /* ---------- STAGE ---------- */
        .t18-offer-stage {
            position: relative;
            width: 100%;
        }
        .t18-offer-track {
            position: relative;
            width: 100%;
        }

        .t18-offer-slide {
            display: none;
            position: relative;
            grid-template-columns: 1fr;
            gap: 0;
        }
        @media (min-width: 900px) {
            .t18-offer-slide {
                grid-template-columns: 1.15fr 0.85fr;
                gap: 0;
                min-height: 460px;
            }
        }

        .t18-offer-slide.is-active {
            display: grid;
            animation: t18SlideIn .7s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes t18SlideIn {
            from {
                opacity: 0;
                transform: translateY(24px) scale(.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ---------- MEDIA ---------- */
        .t18-offer-media {
            position: relative;
            display: block;
            overflow: hidden;
            border-radius: 28px 28px 0 0;
            background: color-mix(in srgb, var(--t18-primary) 8%, #f5f5f7);
            isolation: isolate;
        }
        @media (min-width: 900px) {
            .t18-offer-media {
                border-radius: 28px 0 0 28px;
                min-height: 460px;
            }
        }

        .t18-offer-media img {
            display: block;
            width: 100%;
            height: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            object-position: center;
            transition: transform 1.2s cubic-bezier(.22,1,.36,1);
        }
        @media (min-width: 900px) {
            .t18-offer-media img { aspect-ratio: auto; }
        }
        .t18-offer-media:hover img {
            transform: scale(1.06);
        }

        /* shimmer sweep on hover */
        .t18-offer-shine {
            position: absolute;
            inset: 0;
            background: linear-gradient(115deg,
                transparent 30%,
                rgba(255,255,255,.35) 50%,
                transparent 70%);
            transform: translateX(-120%);
            transition: transform .9s ease;
            pointer-events: none;
        }
        .t18-offer-media:hover .t18-offer-shine {
            transform: translateX(120%);
        }

        /* top-left floating tag */
        .t18-offer-tag {
            position: absolute;
            top: 18px;
            left: 18px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(12px);
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            color: #171717;
            box-shadow: 0 8px 20px rgba(0,0,0,.08);
            z-index: 2;
        }
        .t18-offer-tag-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--t18-primary);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--t18-primary) 25%, transparent);
            animation: t18Pulse 1.8s ease-in-out infinite;
        }
        @keyframes t18Pulse {
            0%, 100% { box-shadow: 0 0 0 4px color-mix(in srgb, var(--t18-primary) 25%, transparent); }
            50%      { box-shadow: 0 0 0 8px color-mix(in srgb, var(--t18-primary) 8%, transparent); }
        }

        /* ---------- INFO CARD ---------- */
        .t18-offer-info {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            isolation: isolate;
        }
        @media (min-width: 900px) {
            .t18-offer-info {
                padding: 0;
            }
        }

        .t18-offer-info-inner {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 380px;
            padding: 36px 32px;
            background: #ffffff;
            border-radius: 0 0 28px 28px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 12%, transparent);
            border-top: none;
            box-shadow: 0 30px 60px -25px color-mix(in srgb, var(--t18-primary) 22%, transparent);
            transition: transform .4s ease, box-shadow .4s ease;
        }
        @media (min-width: 900px) {
            .t18-offer-info-inner {
                border-radius: 0 28px 28px 0;
                border-left: none;
                border-top: 1px solid color-mix(in srgb, var(--t18-primary) 12%, transparent);
                padding: 48px 40px;
                max-width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
        }
        .t18-offer-slide.is-active .t18-offer-info-inner {
            animation: t18InfoSlide .7s cubic-bezier(.22,1,.36,1) .15s both;
        }
        @keyframes t18InfoSlide {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* soft inner glow */
        .t18-offer-panel-glow {
            position: absolute;
            inset: 0;
            z-index: 1;
            border-radius: inherit;
            background: radial-gradient(circle at 80% 20%,
                color-mix(in srgb, var(--t18-primary) 15%, transparent),
                transparent 60%);
            pointer-events: none;
        }

        .t18-offer-chip {
            display: inline-block;
            padding: 5px 12px;
            background: color-mix(in srgb, var(--t18-primary) 10%, transparent);
            color: var(--t18-primary);
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 18px;
        }
        .t18-offer-card-title {
            margin: 0 0 14px;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.02em;
            color: #171717;
        }
        @media (min-width: 640px) {
            .t18-offer-card-title { font-size: 28px; }
        }
        .t18-offer-card-desc {
            margin: 0 0 24px;
            font-size: 13px;
            line-height: 1.6;
            color: #6b7280;
        }

        /* CTA */
        .t18-offer-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            background: #171717;
            color: #fff;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all .35s cubic-bezier(.22,1,.36,1);
            align-self: flex-start;
        }
        .t18-offer-btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--t18-primary);
            color: #fff;
            transition: transform .35s ease;
        }
        .t18-offer-btn:hover {
            background: var(--t18-primary);
            padding-right: 26px;
        }
        .t18-offer-btn:hover .t18-offer-btn-icon {
            background: #fff;
            color: var(--t18-primary);
            transform: rotate(-45deg);
        }

        /* ---------- NAV ---------- */
        .t18-offer-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-top: 32px;
            flex-wrap: wrap;
        }

        .t18-offer-progress {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .t18-offer-progress-bar {
            position: relative;
            width: 60px;
            height: 4px;
            padding: 0;
            border: none;
            background: color-mix(in srgb, var(--t18-primary) 15%, #e5e7eb);
            border-radius: 999px;
            cursor: pointer;
            overflow: hidden;
            transition: background .3s ease;
        }
        .t18-offer-progress-bar:hover {
            background: color-mix(in srgb, var(--t18-primary) 30%, #e5e7eb);
        }
        .t18-offer-progress-fill {
            position: absolute;
            inset: 0;
            width: 0;
            background: linear-gradient(90deg, var(--t18-primary), var(--t18-hover));
            border-radius: 999px;
            transition: width .5s cubic-bezier(.22,1,.36,1);
        }
        .t18-offer-progress-bar.is-active .t18-offer-progress-fill {
            width: 100%;
        }

        .t18-offer-arrows {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .t18-offer-arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 20%, transparent);
            background: #fff;
            color: #171717;
            cursor: pointer;
            transition: all .3s ease;
        }
        .t18-offer-arrow:hover {
            background: var(--t18-primary);
            color: #fff;
            border-color: var(--t18-primary);
            transform: scale(1.05);
        }
        .t18-offer-arrow:active {
            transform: scale(.95);
        }
        .t18-offer-play {
            background: #171717;
            color: #fff;
            border-color: #171717;
        }
        .t18-offer-play:hover {
            background: var(--t18-hover);
            border-color: var(--t18-hover);
        }

        /* ---------- MOBILE ---------- */
        @media (max-width: 899px) {
            .t18-offer-head {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
            .t18-offer-counter {
                align-self: flex-start;
            }
            .t18-offer-info-inner {
                max-width: 100%;
            }
        }
        @media (max-width: 640px) {
            .t18-offer-section { padding: 44px 12px 54px; }
            .t18-offer-head { margin-bottom: 24px; }
            .t18-offer-title { font-size: 2rem; }
            .t18-offer-subtitle { font-size: 13px; }
            .t18-offer-info-inner { padding: 26px 22px; }
            .t18-offer-card-title { font-size: 22px; }
            .t18-offer-progress-bar { width: 40px; }
            .t18-offer-arrow { width: 40px; height: 40px; }
            .t18-offer-orb { filter: blur(40px); opacity: .4; }
        }

        @media (prefers-reduced-motion: reduce) {
            .t18-offer-slide.is-active,
            .t18-offer-slide.is-active .t18-offer-info-inner,
            .t18-offer-tag-dot {
                animation: none !important;
            }
            .t18-offer-media img,
            .t18-offer-shine,
            .t18-offer-progress-fill {
                transition: none !important;
            }
        }
    </style>

    <script>
        (function () {
            const wrapper = document.getElementById('offerWrapper');
            const track = document.getElementById('offerTrack');
            const prevBtn = document.getElementById('prevOffer');
            const nextBtn = document.getElementById('nextOffer');
            const toggleBtn = document.getElementById('toggleAutoplay');
            const dotsWrap = document.getElementById('offerDots');
            if (!wrapper || !track || !prevBtn || !nextBtn || !toggleBtn) return;

            const slides = Array.from(track.querySelectorAll('.t18-offer-slide'));
            if (!slides.length) return;

            const bars = Array.from(document.querySelectorAll('.t18-offer-progress-bar'));
            const icoPause = toggleBtn.querySelector('.t18-ico-pause');
            const icoPlay = toggleBtn.querySelector('.t18-ico-play');
            const slideCount = slides.length;
            const AUTOPLAY_MS = 5500;

            let index = 0;
            let autoplay = true;
            let timer = null;

            function show(i) {
                index = ((i % slideCount) + slideCount) % slideCount;
                slides.forEach((s, idx) => s.classList.toggle('is-active', idx === index));
                bars.forEach((b, idx) => b.classList.toggle('is-active', idx === index));
            }
            function next() { show(index + 1); }
            function prev() { show(index - 1); }

            function startAutoplay() {
                stopAutoplay();
                timer = setInterval(next, AUTOPLAY_MS);
                autoplay = true;
                if (icoPause) icoPause.style.display = '';
                if (icoPlay) icoPlay.style.display = 'none';
            }
            function stopAutoplay() {
                if (timer) clearInterval(timer);
                timer = null;
                autoplay = false;
                if (icoPause) icoPause.style.display = 'none';
                if (icoPlay) icoPlay.style.display = '';
            }
            function restart() { if (autoplay) startAutoplay(); }

            nextBtn.addEventListener('click', () => { next(); restart(); });
            prevBtn.addEventListener('click', () => { prev(); restart(); });
            toggleBtn.addEventListener('click', () => {
                autoplay ? stopAutoplay() : startAutoplay();
            });

            bars.forEach(b => b.addEventListener('click', () => {
                show(parseInt(b.dataset.index, 10) || 0);
                restart();
            }));

            wrapper.addEventListener('mouseenter', () => { if (autoplay) stopAutoplay(); });
            wrapper.addEventListener('mouseleave', () => { if (!autoplay) startAutoplay(); });

            let tx = 0;
            wrapper.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
            wrapper.addEventListener('touchend', e => {
                const diff = e.changedTouches[0].clientX - tx;
                if (Math.abs(diff) > 40) {
                    diff < 0 ? next() : prev();
                    restart();
                }
            });

            document.addEventListener('keydown', e => {
                if (e.key === 'ArrowRight') { next(); restart(); }
                if (e.key === 'ArrowLeft')  { prev(); restart(); }
            });

            show(0);
            startAutoplay();
        })();
    </script>

<?php endif; ?>
<!-- Special Offer Section End -->