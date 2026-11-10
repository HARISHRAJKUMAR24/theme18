<!-- ============================================================
     THEME 18 — SHOP BY AGE (3D FLOATING ORBITS · tighter)
     - name hidden if empty
     - tighter top/bottom padding
     - stronger 3D shading + real preserve-3d layering
     - wave layout flows left↔right responsively
============================================================ -->
<?php
$advance_categories = [];
for ($i = 1; $i <= 6; $i++) {
    $name  = getData("advance_category_name_$i",  "seller_banners", "seller_id = '$sellerId' AND store_id = '$storeId'");
    $link  = getData("advance_category_link_$i",  "seller_banners", "seller_id = '$sellerId' AND store_id = '$storeId'");
    $image = getData("advance_category_image_$i", "seller_banners", "seller_id = '$sellerId' AND store_id = '$storeId'");
    if (!empty($image)) {
        $advance_categories[] = [
            'name'  => trim((string)$name),
            'link'  => $link,
            'image' => $image,
        ];
    }
}

if (!empty($advance_categories)):

    $main_heading  = getData("advance_category_main_heading", "seller_banners", "seller_id = '$sellerId' AND store_id = '$storeId'");
    $primary_color = getData("color",       "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ed168b';
    $hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#dc007b';
?>

    <section class="t18orb-section"
        style="
            --t18o-primary: <?= htmlspecialchars($primary_color) ?>;
            --t18o-hover:   <?= htmlspecialchars($hover_color) ?>;
         ">

        <span class="t18orb-dust t18orb-dust-1" aria-hidden="true"></span>
        <span class="t18orb-dust t18orb-dust-2" aria-hidden="true"></span>
        <span class="t18orb-dust t18orb-dust-3" aria-hidden="true"></span>

        <div class="t18orb-inner">

            <?php if (!empty($main_heading)): ?>
                <div class="t18orb-head">
                    <span class="t18orb-kicker">Explore by Age</span>
                    <h2 class="t18orb-title"><?= htmlspecialchars($main_heading) ?></h2>
                    <span class="t18orb-rule" aria-hidden="true"></span>
                </div>
            <?php endif; ?>

            <div class="t18orb-orbit">


                <div class="t18orb-track">
                    <?php foreach ($advance_categories as $index => $category):
                        $num    = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                        $link   = !empty($category['link']) ? htmlspecialchars($category['link']) : '#';
                        $isLink = !empty($category['link']);
                        $label  = $category['name'];
                        $hasName = $label !== '';
                        $wave   = ['-14', '10', '-6', '14', '-10', '6'][$index % 6];
                    ?>
                        <a href="<?= $link ?>"
                            <?= $isLink ? 'target="_blank" rel="noopener"' : '' ?>
                            class="t18orb-item<?= $hasName ? '' : ' is-noname' ?>"
                            style="--i: <?= $index ?>; --wave: <?= $wave ?>px;">

                            <span class="t18orb-sphere">
                                <span class="t18orb-halo" aria-hidden="true"></span>
                                <span class="t18orb-face">
                                    <img src="<?= UPLOADS_URL . $category['image'] ?>"
                                        alt="<?= $hasName ? htmlspecialchars($label) : 'Category ' . $num ?>"
                                        loading="lazy">
                                    <span class="t18orb-shine" aria-hidden="true"></span>
                                    <span class="t18orb-shadow" aria-hidden="true"></span>
                                </span>
                                <span class="t18orb-num"><?= $num ?></span>
                            </span>

                            <span class="t18orb-ring" aria-hidden="true"></span>

                            <?php if ($hasName): ?>
                                <span class="t18orb-name"><?= htmlspecialchars($label) ?></span>
                            <?php endif; ?>

                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </section>


    <style>
        .t18orb-section {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            margin: 36px auto;
            padding: 38px 20px 46px;
            /* tighter top/bottom */
            border-radius: 34px;
            background:
                radial-gradient(circle at 15% 20%,
                    color-mix(in srgb, var(--t18o-hover) 10%, #ffffff) 0%,
                    transparent 45%),
                radial-gradient(circle at 90% 85%,
                    color-mix(in srgb, var(--t18o-primary) 12%, #ffffff) 0%,
                    transparent 50%),
                linear-gradient(180deg, #ffffff 0%, #fffafd 100%);
            box-shadow:
                0 24px 60px -28px color-mix(in srgb, var(--t18o-primary) 30%, transparent),
                inset 0 1px 0 rgba(255, 255, 255, .9);
        }

        .t18orb-dust {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            background: radial-gradient(circle at 30% 30%,
                    color-mix(in srgb, var(--t18o-hover) 40%, #ffffff) 0%,
                    color-mix(in srgb, var(--t18o-primary) 75%, #ffffff) 55%,
                    var(--t18o-primary) 100%);
            opacity: .10;
            filter: blur(1px);
            animation: t18OrbDust 12s ease-in-out infinite alternate;
        }

        .t18orb-dust-1 {
            width: 220px;
            height: 220px;
            top: -80px;
            left: -60px
        }

        .t18orb-dust-2 {
            width: 160px;
            height: 160px;
            bottom: -70px;
            right: 4%;
            animation-delay: -4s
        }

        .t18orb-dust-3 {
            width: 90px;
            height: 90px;
            top: 30%;
            right: 22%;
            opacity: .06;
            animation-delay: -8s
        }

        @keyframes t18OrbDust {
            0% {
                transform: translate(0, 0) scale(1)
            }

            50% {
                transform: translate(12px, -14px) scale(1.06)
            }

            100% {
                transform: translate(-8px, 10px) scale(.97)
            }
        }

        .t18orb-inner {
            position: relative;
            z-index: 1;
            max-width: 1180px;
            margin: 0 auto
        }

        /* heading — tighter */
        .t18orb-head {
            text-align: center;
            margin-bottom: 34px
        }

        .t18orb-kicker {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--t18o-primary);
            margin-bottom: 10px;
            padding: 5px 13px;
            border-radius: 999px;
            background: color-mix(in srgb, var(--t18o-primary) 10%, #ffffff);
        }

        .t18orb-title {
            margin: 0;
            font-size: clamp(1.65rem, 3.4vw, 2.35rem);
            font-weight: 900;
            letter-spacing: -.6px;
            color: #1f1235;
            line-height: 1.15;
        }

        .t18orb-rule {
            display: block;
            width: 56px;
            height: 4px;
            margin: 12px auto 0;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--t18o-primary), var(--t18o-hover));
        }

        /* orbit stage */
        .t18orb-orbit {
            position: relative;
            padding: 26px 0 4px;
            /* tighter */
            perspective: 1400px;
            perspective-origin: 50% 40%;
        }



        /* track — 2-col on mobile flows left↔right nicely with wave */
        .t18orb-track {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 34px 28px;
            justify-items: center;
            transform-style: preserve-3d;
        }

        @media(min-width:640px) {
            .t18orb-track {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 40px 34px
            }
        }

        @media(min-width:1024px) {
            .t18orb-track {
                grid-template-columns: repeat(6, minmax(0, 1fr));
                gap: 20px;
                align-items: end;
            }
        }

        /* one item */
        .t18orb-item {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            transform: translateY(var(--wave, 0px));
            transform-style: preserve-3d;
            animation: t18OrbIn 1s cubic-bezier(.22, .61, .36, 1) both;
            animation-delay: calc(var(--i, 0) * .1s);
        }

        @keyframes t18OrbIn {
            from {
                opacity: 0;
                transform: translateY(calc(var(--wave, 0px) + 40px)) scale(.7)
            }

            to {
                opacity: 1;
                transform: translateY(var(--wave, 0px)) scale(1)
            }
        }

        /* SPHERE — much deeper 3D */
        .t18orb-sphere {
            position: relative;
            width: 142px;
            height: 142px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transform-style: preserve-3d;
            transition: transform .7s cubic-bezier(.22, .61, .36, 1);
            animation: t18OrbFloat 6s ease-in-out infinite;
            animation-delay: calc(var(--i, 0) * -.9s);
        }

        @keyframes t18OrbFloat {

            0%,
            100% {
                transform: translateY(0) rotateY(0deg) rotateX(0deg)
            }

            25% {
                transform: translateY(-6px) rotateY(7deg) rotateX(3deg)
            }

            50% {
                transform: translateY(-10px) rotateY(0deg) rotateX(-2deg)
            }

            75% {
                transform: translateY(-4px) rotateY(-7deg) rotateX(2deg)
            }
        }

        /* halo — larger + tighter blur for a real glow */
        .t18orb-halo {
            position: absolute;
            inset: -20px;
            border-radius: 50%;
            background: radial-gradient(circle,
                    color-mix(in srgb, var(--t18o-primary) 45%, transparent) 0%,
                    color-mix(in srgb, var(--t18o-hover) 20%, transparent) 35%,
                    transparent 65%);
            filter: blur(14px);
            z-index: 0;
            opacity: .75;
            transition: opacity .4s ease, transform .4s ease;
        }

        /* face — real sphere with layered shadows + ambient occlusion */
        .t18orb-face {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
            box-shadow:
                0 24px 44px -14px color-mix(in srgb, var(--t18o-primary) 40%, transparent),
                0 10px 20px -8px rgba(0, 0, 0, .22),
                inset 0 -18px 28px rgba(0, 0, 0, .28),
                inset 0 16px 24px rgba(255, 255, 255, .42),
                inset -10px 0 18px rgba(0, 0, 0, .12),
                inset 10px 0 14px rgba(255, 255, 255, .28);
            z-index: 1;
            transform: translateZ(1px);
            transition: box-shadow .4s ease, transform .4s ease;
        }

        .t18orb-face img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            transition: transform .8s ease;
        }

        /* specular shine — brighter, more focused */
        .t18orb-shine {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: radial-gradient(circle at 28% 22%,
                    rgba(255, 255, 255, .72) 0%,
                    rgba(255, 255, 255, .22) 25%,
                    transparent 50%);
            pointer-events: none;
            z-index: 2;
        }

        /* ambient occlusion at bottom of the sphere — adds mass */
        .t18orb-shadow {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: -2px;
            width: 70%;
            height: 22%;
            background: radial-gradient(ellipse at center,
                    rgba(0, 0, 0, .22) 0%,
                    transparent 70%);
            pointer-events: none;
            z-index: 2;
            filter: blur(3px);
        }

        /* number chip floats in true 3D */
        .t18orb-num {
            position: absolute;
            top: 6px;
            left: 6px;
            z-index: 3;
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: var(--t18o-primary);
            color: #fff;
            font-weight: 900;
            font-size: 11px;
            letter-spacing: .6px;
            box-shadow:
                0 8px 16px -6px color-mix(in srgb, var(--t18o-primary) 55%, transparent),
                inset 0 -2px 4px rgba(0, 0, 0, .15);
            transform: translateZ(12px);
            transition: background .3s ease, transform .3s ease;
        }

        /* orbit ring — thinner, more 3D, brighter */
        .t18orb-ring {
            display: block;
            width: 186px;
            height: 30px;
            margin-top: -24px;
            border-radius: 50%;
            border: 1.5px solid color-mix(in srgb, var(--t18o-primary) 45%, transparent);
            background: radial-gradient(ellipse at center,
                    color-mix(in srgb, var(--t18o-primary) 10%, transparent) 0%,
                    transparent 70%);
            transform: rotateX(72deg);
            transform-style: preserve-3d;
            box-shadow:
                0 0 20px color-mix(in srgb, var(--t18o-primary) 30%, transparent),
                0 2px 0 color-mix(in srgb, var(--t18o-primary) 15%, transparent) inset;
            animation: t18OrbRing 6s ease-in-out infinite;
            animation-delay: calc(var(--i, 0) * -.9s);
            transition: border-color .4s ease, box-shadow .4s ease;
        }

        @keyframes t18OrbRing {

            0%,
            100% {
                transform: rotateX(72deg) scale(1)
            }

            50% {
                transform: rotateX(72deg) scale(1.08)
            }
        }

        /* name — hidden only if text empty (handled in PHP with .is-noname) */
        .t18orb-name {
            margin-top: 12px;
            display: inline-block;
            padding: 6px 14px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid color-mix(in srgb, var(--t18o-primary) 18%, transparent);
            box-shadow:
                0 6px 14px -6px rgba(0, 0, 0, .12),
                inset 0 1px 0 rgba(255, 255, 255, .9);
            font-size: 12px;
            font-weight: 800;
            color: #1f1235;
            letter-spacing: .2px;
            white-space: nowrap;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color .3s ease, border-color .3s ease, transform .3s ease;
        }

        /* if a category has no name, the label is not rendered — no blank pill */
        .t18orb-item.is-noname .t18orb-ring {
            margin-bottom: 0
        }

        /* HOVER — deeper lift */
        .t18orb-item:hover .t18orb-sphere {
            animation-play-state: paused;
            transform: translateY(-18px) rotateY(20deg) rotateX(4deg) scale(1.07);
        }

        .t18orb-item:hover .t18orb-face {
            box-shadow:
                0 34px 66px -20px color-mix(in srgb, var(--t18o-primary) 55%, transparent),
                0 14px 28px -10px rgba(0, 0, 0, .22),
                inset 0 -20px 30px rgba(0, 0, 0, .30),
                inset 0 18px 28px rgba(255, 255, 255, .5),
                inset -12px 0 20px rgba(0, 0, 0, .14),
                inset 12px 0 16px rgba(255, 255, 255, .34);
        }

        .t18orb-item:hover .t18orb-face img {
            transform: scale(1.08)
        }

        .t18orb-item:hover .t18orb-num {
            background: var(--t18o-hover);
            transform: translateZ(18px) scale(1.1);
        }

        .t18orb-item:hover .t18orb-halo {
            opacity: 1;
            transform: scale(1.08);
        }

        .t18orb-item:hover .t18orb-ring {
            border-color: var(--t18o-primary);
            box-shadow:
                0 0 28px color-mix(in srgb, var(--t18o-primary) 55%, transparent),
                0 2px 0 color-mix(in srgb, var(--t18o-primary) 20%, transparent) inset;
        }

        .t18orb-item:hover .t18orb-name {
            color: var(--t18o-primary);
            border-color: var(--t18o-primary);
            transform: translateY(-2px);
        }

        /* mobile — tighter, wave still plays left↔right */
        @media(max-width:640px) {
            .t18orb-section {
                margin: 24px auto;
                padding: 28px 12px 34px;
                border-radius: 22px
            }

            .t18orb-head {
                margin-bottom: 26px
            }

            .t18orb-title {
                font-size: 1.55rem
            }

            .t18orb-track {
                gap: 26px 20px
            }

            .t18orb-sphere {
                width: 116px;
                height: 116px
            }

            .t18orb-halo {
                inset: -16px
            }

            .t18orb-ring {
                width: 150px;
                height: 24px;
                margin-top: -18px
            }

            .t18orb-num {
                min-width: 26px;
                height: 26px;
                font-size: 10px
            }

            .t18orb-name {
                font-size: 11px;
                padding: 5px 11px;
                margin-top: 10px
            }
        }

        /* accessibility */
        @media (prefers-reduced-motion:reduce) {

            .t18orb-dust,
            .t18orb-item,
            .t18orb-sphere,
            .t18orb-ring {
                animation: none !important
            }

            .t18orb-item:hover .t18orb-sphere {
                transform: none
            }
        }
    </style>

<?php endif; ?>
<!-- ============================================================
     THEME 18 — SHOP BY AGE END
============================================================ -->