<!-- ============================================================
     HERO BANNER — Simple
     + small corner ovals (TOP-LEFT + BOTTOM-RIGHT only)
     + no image background shape
     + DB colors ($primary_color / $hover_color)
     + object-contain image (never cropped)
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

                <?php if ($heroCount > 1): ?>
                    <button type="button" id="zHeroPrev" aria-label="Previous banner"
                        class="z-hero-arrow z-hero-arrow-prev">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </button>
                    <button type="button" id="zHeroNext" aria-label="Next banner"
                        class="z-hero-arrow z-hero-arrow-next">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </button>

                    <div id="zHeroDots" class="z-hero-dots" aria-label="Choose banner">
                        <?php foreach ($heroBanners as $i => $_): ?>
                            <button type="button"
                                aria-label="Banner <?= $i + 1 ?>"
                                aria-current="<?= $i === 0 ? 'true' : 'false' ?>"
                                class="z-hero-dot <?= $i === 0 ? 'is-active' : '' ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ============================================================
         STYLES — simple hero + small corner ovals
    ============================================================ -->
    <style>
        /* ---------- outer wrapper: top/bottom gaps ---------- */
        .z-hero-wrap {
            position: relative;
            width: 100%;
            max-width: 1450px;
            margin: 48px auto 64px;
            padding: 0 16px;
            isolation: isolate;
            overflow: hidden;
            border-radius: 28px;
        }

        @media (max-width: 768px) {
            .z-hero-wrap {
                margin: 32px auto 44px;
                padding: 0 12px;
                border-radius: 22px;
            }
        }

        @media (max-width: 480px) {
            .z-hero-wrap {
                margin: 26px auto 36px;
                padding: 0 10px;
                border-radius: 18px;
            }
        }

        /* ---------- small corner ovals (top-left + bottom-right) ---------- */
        .z-corner-oval {
            position: absolute;
            z-index: 0;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(0.5px);
            animation: zCornerFloat 10s ease-in-out infinite alternate;
        }

        /* TOP-LEFT — uses primary color */
        .z-corner-tl {
            width: 160px;
            height: 200px;
            top: -60px;
            left: -60px;
            background: radial-gradient(ellipse at 30% 30%,
                    color-mix(in srgb, var(--z-hover) 35%, #ffffff) 0%,
                    color-mix(in srgb, var(--z-primary) 55%, #ffffff) 55%,
                    var(--z-primary) 100%);
            box-shadow:
                0 18px 40px color-mix(in srgb, var(--z-primary) 28%, transparent),
                inset 0 0 30px rgba(255, 255, 255, 0.35);
            opacity: 0.85;
        }

        /* BOTTOM-RIGHT — uses hover color */
        .z-corner-br {
            width: 140px;
            height: 180px;
            bottom: -55px;
            right: -55px;
            background: radial-gradient(ellipse at 70% 30%,
                    color-mix(in srgb, var(--z-primary) 30%, #ffffff) 0%,
                    color-mix(in srgb, var(--z-hover) 55%, #ffffff) 55%,
                    var(--z-hover) 100%);
            box-shadow:
                0 18px 40px color-mix(in srgb, var(--z-hover) 28%, transparent),
                inset 0 0 30px rgba(255, 255, 255, 0.35);
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

        /* ---------- plain image container (no bg shape) ---------- */
        .z-hero-outer {
            position: relative;
            z-index: 1;
        }

        .z-hero-box {
            position: relative;
            width: 100%;
            height: 460px;
            max-height: 80vh;
            overflow: hidden;
            border-radius: 20px;
            background: transparent;
        }

        /* image wrapper — centered, contained, no shape on the image */
        .z-hero-img-wrap {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4% 5%;
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

        /* ---------- arrows ---------- */
        .z-hero-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            color: var(--z-primary);
            border: 1px solid rgba(255, 255, 255, 0.95);
            box-shadow: 0 10px 22px color-mix(in srgb, var(--z-primary) 25%, transparent);
            backdrop-filter: blur(10px);
            transition: all .25s ease;
        }

        .z-hero-arrow:hover {
            background: linear-gradient(120deg, var(--z-hover), var(--z-primary));
            color: #fff;
            transform: translateY(-50%) scale(1.08);
            box-shadow: 0 14px 28px color-mix(in srgb, var(--z-primary) 45%, transparent);
        }

        .z-hero-arrow-prev {
            left: 2%;
        }

        .z-hero-arrow-next {
            right: 2%;
        }

        /* ---------- dots ---------- */
        .z-hero-dots {
            position: absolute;
            z-index: 10;
            bottom: 4%;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.7);
        }

        .z-hero-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            border: 1.5px solid color-mix(in srgb, var(--z-primary) 55%, transparent);
            transition: all .35s ease;
            cursor: pointer;
        }

        .z-hero-dot.is-active {
            width: 26px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--z-hover), var(--z-primary));
            border-color: transparent;
        }

        /* ---------- responsive ---------- */
        @media (max-width: 1024px) {
            .z-hero-box {
                height: 400px;
                max-height: 70vh;
            }

            .z-hero-img-wrap {
                padding: 5% 6%;
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
                height: 340px;
                max-height: 60vh;
                border-radius: 16px;
            }

            .z-hero-arrow {
                width: 38px;
                height: 38px;
            }

            .z-hero-arrow-prev {
                left: 1.5%;
            }

            .z-hero-arrow-next {
                right: 1.5%;
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
                height: 260px;
                max-height: 45vh;
            }

            .z-hero-img-wrap {
                padding: 6% 7%;
            }

            .z-hero-dots {
                bottom: 3%;
                padding: 5px 10px;
                gap: 6px;
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
                height: 220px;
                max-height: 40vh;
            }

            .z-hero-arrow {
                width: 34px;
                height: 34px;
            }

            .z-hero-arrow svg {
                width: 16px;
                height: 16px;
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
         CAROUSEL LOGIC
    ============================================================ -->
    <script>
        (() => {
            const hero = document.getElementById('zHero');
            if (!hero) return;

            const slides = Array.from(hero.querySelectorAll('.z-hero-slide'));
            const dots = Array.from(document.querySelectorAll('#zHeroDots button'));
            const prev = document.getElementById('zHeroPrev');
            const next = document.getElementById('zHeroNext');

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

                dots.forEach((dot, i) => {
                    const active = i === current;
                    dot.classList.toggle('is-active', active);
                    dot.setAttribute('aria-current', String(active));
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

            prev.addEventListener('click', () => {
                show(current - 1);
                start();
            });
            next.addEventListener('click', () => {
                show(current + 1);
                start();
            });
            dots.forEach((dot, i) => dot.addEventListener('click', () => {
                show(i);
                start();
            }));

            hero.addEventListener('mouseenter', stop);
            hero.addEventListener('mouseleave', start);
            hero.addEventListener('focusin', stop);
            hero.addEventListener('focusout', e => {
                if (!hero.contains(e.relatedTarget)) start();
            });
            document.addEventListener('visibilitychange', start);

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