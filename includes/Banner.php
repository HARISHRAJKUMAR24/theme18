<!-- ============================================================
     HERO BANNER — Simple (TIGHT GAP · NO DOTS · NO ARROWS)
============================================================ -->
<?php
$primary_color = getData("color",       "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f';
$hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899';

$heroBanners = [];
for ($i = 1; $i <= 5; $i++) {
    $imgColumn  = "homepage_banner" . ($i > 1 ? "_$i" : "");
    $linkColumn = "homepage_banner_link" . ($i > 1 ? "_$i" : "");

    $image = getData($imgColumn,  "seller_banners", "(seller_id = '$sellerId' AND store_id = '$storeId')");
    $link  = getData($linkColumn, "seller_banners", "(seller_id = '$sellerId' AND store_id = '$storeId')");

    if (!empty($image)) {
        $heroBanners[] = [
            'image' => $image,
            'link'  => $link ?: '',
            'alt'   => 'Featured collection ' . $i,
        ];
    }
}
$heroCount = count($heroBanners);
?>

<?php if ($heroCount > 0): ?>
    <section class="z-hero-wrap"
        style="
                --z-primary: <?= htmlspecialchars($primary_color) ?>;
                --z-hover:   <?= htmlspecialchars($hover_color) ?>;
             ">
        <!-- small corner ovals: top-left + bottom-right -->
        <span class="z-corner-oval z-corner-tl" aria-hidden="true"></span>
        <span class="z-corner-oval z-corner-br" aria-hidden="true"></span>

        <div class="z-hero-outer">
            <div id="zHero"
                class="z-hero-box"
                aria-roledescription="carousel">

                <?php foreach ($heroBanners as $index => $banner):
                    $active     = $index === 0 ? 'opacity-100' : 'opacity-0';
                    $ariaHidden = $index === 0 ? 'false' : 'true';
                    $imgUrl     = UPLOADS_URL . $banner['image'];
                ?>
                    <div class="z-hero-slide absolute inset-0 <?= $active ?> transition-opacity duration-1000 ease-in-out"
                        aria-hidden="<?= $ariaHidden ?>">
                        <?php if (!empty($banner['link'])): ?>
                            <a href="<?= htmlspecialchars($banner['link']) ?>"
                                target="_blank" rel="noopener"
                                class="z-hero-img-wrap">
                                <img src="<?= htmlspecialchars($imgUrl) ?>"
                                    alt="<?= htmlspecialchars($banner['alt']) ?>"
                                    class="z-hero-img"
                                    <?= $index === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                            </a>
                        <?php else: ?>
                            <div class="z-hero-img-wrap">
                                <img src="<?= htmlspecialchars($imgUrl) ?>"
                                    alt="<?= htmlspecialchars($banner['alt']) ?>"
                                    class="z-hero-img"
                                    <?= $index === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <!-- Dots & Arrows REMOVED -->

            </div>
        </div>
    </section>

    <!-- ============================================================
         STYLES — Tight gaps · No dots · No arrows
    ============================================================ -->
    <style>
        /* ---------- outer wrapper ---------- */
        .z-hero-wrap {
            position: relative;
            width: 100%;
            max-width: 1450px;
            margin: 8px auto 12px;
            padding: 0 16px;
            isolation: isolate;
            overflow: hidden;
            border-radius: 24px;
        }

        @media (max-width: 1024px) {
            .z-hero-wrap {
                margin: 6px auto 10px;
                padding: 0 14px;
                border-radius: 22px;
            }
        }

        @media (max-width: 768px) {
            .z-hero-wrap {
                margin: 4px auto 8px;
                padding: 0 12px;
                border-radius: 20px;
            }
        }

        @media (max-width: 480px) {
            .z-hero-wrap {
                margin: 4px auto 6px;
                padding: 0 10px;
                border-radius: 18px;
            }
        }

        /* ---------- small corner ovals ---------- */
        .z-corner-oval {
            position: absolute;
            z-index: 0;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(0.5px);
            animation: zCornerFloat 10s ease-in-out infinite alternate;
        }

        .z-corner-tl {
            width: 160px;
            height: 200px;
            top: -60px;
            left: -60px;
            background: radial-gradient(ellipse at 30% 30%, color-mix(in srgb, var(--z-hover) 35%, #ffffff) 0%, color-mix(in srgb, var(--z-primary) 55%, #ffffff) 55%, var(--z-primary) 100%);
            box-shadow: 0 18px 40px color-mix(in srgb, var(--z-primary) 28%, transparent), inset 0 0 30px rgba(255, 255, 255, 0.35);
            opacity: 0.85;
        }

        .z-corner-br {
            width: 140px;
            height: 180px;
            bottom: -55px;
            right: -55px;
            background: radial-gradient(ellipse at 70% 30%, color-mix(in srgb, var(--z-primary) 30%, #ffffff) 0%, color-mix(in srgb, var(--z-hover) 55%, #ffffff) 55%, var(--z-hover) 100%);
            box-shadow: 0 18px 40px color-mix(in srgb, var(--z-hover) 28%, transparent), inset 0 0 30px rgba(255, 255, 255, 0.35);
            opacity: 0.85;
            animation-name: zCornerFloatAlt;
        }

        @keyframes zCornerFloat {
            0% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(6px, -8px) scale(1.06);
            }

            100% {
                transform: translate(-4px, 6px) scale(0.97);
            }
        }

        @keyframes zCornerFloatAlt {
            0% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(-6px, -6px) scale(1.07);
            }

            100% {
                transform: translate(4px, 8px) scale(0.96);
            }
        }

        /* ---------- hero box ---------- */
        .z-hero-outer {
            position: relative;
            z-index: 1;
        }

        .z-hero-box {
            position: relative;
            width: 100%;
            height: 380px;
            max-height: 70vh;
            overflow: hidden;
            border-radius: 20px;
            background: transparent;
            /* Removed margin-bottom since dots are gone */
        }

        .z-hero-img-wrap {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 1%;
        }

        .z-hero-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }

        #zHero .z-hero-slide {
            transition: opacity 1s ease-in-out;
        }

        /* ---------- responsive heights (tight fit) ---------- */
        @media (max-width: 1024px) {
            .z-hero-box {
                height: 340px;
                max-height: 65vh;
            }

            .z-corner-tl {
                width: 130px;
                height: 165px;
                top: -50px;
                left: -50px;
            }

            .z-corner-br {
                width: 115px;
                height: 150px;
                bottom: -45px;
                right: -45px;
            }
        }

        @media (max-width: 768px) {
            .z-hero-box {
                height: 280px;
                max-height: 55vh;
                border-radius: 16px;
            }

            .z-corner-tl {
                width: 105px;
                height: 135px;
                top: -40px;
                left: -40px;
                opacity: 0.75;
            }

            .z-corner-br {
                width: 95px;
                height: 125px;
                bottom: -38px;
                right: -38px;
                opacity: 0.75;
            }
        }

        @media (max-width: 640px) {
            .z-hero-box {
                height: 240px;
                max-height: 48vh;
            }

            .z-hero-img-wrap {
                padding: 0;
            }

            .z-corner-tl {
                width: 80px;
                height: 105px;
                top: -32px;
                left: -32px;
                opacity: 0.65;
            }

            .z-corner-br {
                width: 72px;
                height: 95px;
                bottom: -30px;
                right: -30px;
                opacity: 0.65;
            }
        }

        @media (max-width: 400px) {
            .z-hero-box {
                height: 200px;
                max-height: 42vh;
            }

            .z-corner-tl {
                width: 65px;
                height: 85px;
                top: -26px;
                left: -26px;
                opacity: 0.55;
            }

            .z-corner-br {
                width: 58px;
                height: 78px;
                bottom: -24px;
                right: -24px;
                opacity: 0.55;
            }
        }

        /* ---------- reduced motion ---------- */
        @media (prefers-reduced-motion: reduce) {

            #zHero .z-hero-slide,
            .z-corner-oval {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>

    <!-- ============================================================
         CAROUSEL LOGIC — Autoplay only (no dots/arrows)
    ============================================================ -->
    <script>
        (() => {
            const hero = document.getElementById('zHero');
            if (!hero) return;

            const slides = Array.from(hero.querySelectorAll('.z-hero-slide'));
            if (slides.length < 2) return;

            const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reducedMotion) slides.forEach(s => s.classList.remove('duration-1000'));

            let current = 0;
            let timer;

            function show(index) {
                current = (index + slides.length) % slides.length;
                slides.forEach((slide, i) => {
                    const active = i === current;
                    slide.classList.toggle('opacity-100', active);
                    slide.classList.toggle('opacity-0', !active);
                    slide.setAttribute('aria-hidden', String(!active));
                });
            }

            function stop() {
                clearInterval(timer);
            }

            function start() {
                stop();
                if (!document.hidden && !reducedMotion) {
                    timer = setInterval(() => show(current + 1), 5000);
                }
            }

            // Pause on hover/focus so users can read the banner
            hero.addEventListener('mouseenter', stop);
            hero.addEventListener('mouseleave', start);
            hero.addEventListener('focusin', stop);
            hero.addEventListener('focusout', e => {
                if (!hero.contains(e.relatedTarget)) start();
            });

            document.addEventListener('visibilitychange', start);

            // Swipe support (mobile)
            let touchX = null;
            hero.addEventListener('touchstart', e => {
                touchX = e.changedTouches[0].clientX;
            }, {
                passive: true
            });

            hero.addEventListener('touchend', e => {
                if (touchX === null) return;
                const delta = touchX - e.changedTouches[0].clientX;
                if (Math.abs(delta) > 45) {
                    show(current + (delta > 0 ? 1 : -1));
                    start();
                }
                touchX = null;
            }, {
                passive: true
            });

            show(0);
            start();
        })();
    </script>
<?php endif; ?>