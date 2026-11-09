<!-- ============================================================
     BACKEND — DB COLORS + FETCH MENU CATEGORIES
============================================================ -->
<?php
$categoryPrimary = getData("color",       "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ed168b';
$categoryHover   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#dc007b';

$stmt = getCategories();
$sliderCategories = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

$sliderCategories = array_filter($sliderCategories, function ($cat) {
    return isset($cat['add_to_menu']) && (int)$cat['add_to_menu'] === 1;
});

$sliderCategories = array_reverse($sliderCategories);
$sliderCategories = array_slice($sliderCategories, 0, 20);
?>


<!-- ============================================================
     HTML — CATEGORY SLIDER (3D leaf on right)
============================================================ -->
<?php if (!empty($sliderCategories)) : ?>
    <section class="category-section max-w-[1450px]"
        style="
            --cat-primary: <?= htmlspecialchars($categoryPrimary) ?>;
            --cat-hover:   <?= htmlspecialchars($categoryHover) ?>;
        ">
        <!-- 3D leaf decoration (right side) -->
        <span class="cat-leaf cat-leaf-1" aria-hidden="true"></span>
        <span class="cat-leaf cat-leaf-2" aria-hidden="true"></span>

        <div class="category-area">
            <button id="categoryPrev" class="category-arrow category-prev" aria-label="Previous">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </button>

            <div id="categoryTrack" class="category-track">
                <?php foreach ($sliderCategories as $i => $cat):
                    $catName = $cat['name'] ?? 'Category';
                    $catSlug = $cat['slug'] ?? '';
                    $catIcon = $cat['icon'] ?? '';
                    $catUrl  = $storeUrl . 'category/' . $catSlug;
                    $iconUrl = !empty($catIcon) ? UPLOADS_URL . $catIcon : '';
                ?>
                    <a href="<?= htmlspecialchars($catUrl) ?>"
                        class="category-card"
                        style="--i: <?= (int)$i ?>;">
                        <div class="category-tilt">
                            <span class="category-shimmer" aria-hidden="true"></span>
                            <div class="category-circle">
                                <?php if (!empty($iconUrl)): ?>
                                    <img src="<?= htmlspecialchars($iconUrl) ?>"
                                        alt="<?= htmlspecialchars($catName) ?>"
                                        class="category-icon-img"
                                        loading="lazy"
                                        onerror="this.style.display='none'; this.parentElement.textContent='<?= htmlspecialchars(mb_substr($catName, 0, 1)) ?>';">
                                <?php else: ?>
                                    <?= htmlspecialchars(mb_substr($catName, 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p class="category-name"><?= htmlspecialchars($catName) ?></p>
                    </a>
                <?php endforeach; ?>
            </div>

            <button id="categoryNext" class="category-arrow category-next" aria-label="Next">
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </button>
        </div>
    </section>
<?php endif; ?>


<!-- ============================================================
     STYLES — CATEGORY SLIDER (3D leaf + tilt)
============================================================ -->
<style>
    .category-section {
        position: relative;
        isolation: isolate;
        margin: 30px auto 35px;
        width: 100%;
        perspective: 900px;
        padding: 0 30px;
    }

    /* ============================================================
       3D LEAF DECORATION — right side
    ============================================================ */
    .cat-leaf {
        position: absolute;
        z-index: -1;
        pointer-events: none;
        border-radius: 0 100% 0 100%;
        background:
            linear-gradient(135deg,
                color-mix(in srgb, var(--cat-hover) 55%, #ffffff) 0%,
                color-mix(in srgb, var(--cat-primary) 75%, #ffffff) 45%,
                var(--cat-primary) 100%);
        box-shadow:
            0 18px 38px color-mix(in srgb, var(--cat-primary) 30%, transparent),
            inset -6px -8px 18px rgba(0, 0, 0, .12),
            inset 6px 8px 16px rgba(255, 255, 255, .55);
        transform-origin: bottom left;
        opacity: .9;
    }

    /* large leaf (top-right, behind card) */
    .cat-leaf-1 {
        width: 190px;
        height: 190px;
        right: -70px;
        top: -50px;
        transform: rotate(-18deg);
        animation: leafSway 9s ease-in-out infinite alternate;
    }

    /* small leaf (bottom-right, in front edge) */
    .cat-leaf-2 {
        width: 110px;
        height: 110px;
        right: -30px;
        bottom: -40px;
        transform: rotate(28deg);
        border-radius: 100% 0 100% 0;
        /* mirrored leaf */
        background:
            linear-gradient(135deg,
                color-mix(in srgb, var(--cat-primary) 40%, #ffffff) 0%,
                color-mix(in srgb, var(--cat-hover) 70%, #ffffff) 50%,
                var(--cat-hover) 100%);
        box-shadow:
            0 14px 28px color-mix(in srgb, var(--cat-hover) 30%, transparent),
            inset -4px -6px 14px rgba(0, 0, 0, .1),
            inset 4px 6px 12px rgba(255, 255, 255, .5);
        opacity: .85;
        animation: leafSwayAlt 11s ease-in-out infinite alternate;
    }

    /* leaf sway animations */
    @keyframes leafSway {
        0% {
            transform: rotate(-18deg) translateY(0) scale(1);
        }

        50% {
            transform: rotate(-14deg) translateY(-8px) scale(1.04);
        }

        100% {
            transform: rotate(-22deg) translateY(6px) scale(0.98);
        }
    }

    @keyframes leafSwayAlt {
        0% {
            transform: rotate(28deg) translateY(0) scale(1);
        }

        50% {
            transform: rotate(24deg) translateY(6px) scale(1.03);
        }

        100% {
            transform: rotate(32deg) translateY(-6px) scale(0.98);
        }
    }

    /* ---- area card ---- */
    .category-area {
        position: relative;
        overflow: hidden;
        padding: 2px 20px;
        border: 1px solid color-mix(in srgb, var(--cat-primary) 22%, #ffffff);
        border-radius: 35px;
        background: linear-gradient(125deg,
                rgba(255, 255, 255, .97),
                color-mix(in srgb, var(--cat-primary) 6%, #ffffff));
        box-shadow:
            0 15px 45px color-mix(in srgb, var(--cat-primary) 18%, transparent),
            0 4px 12px color-mix(in srgb, var(--cat-hover) 8%, transparent);
        backdrop-filter: blur(12px);
    }

    /* ---- track ---- */
    .category-track {
        display: flex;
        align-items: center;
        gap: 17px;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: none;
        padding: 30px 20px 22px;
        min-width: 0;
        max-width: 100%;
        overscroll-behavior-x: contain;
        touch-action: pan-x;
        perspective: 900px;
        transform-style: preserve-3d;
    }

    .category-track::-webkit-scrollbar {
        display: none;
    }

    /* ---- card wrapper ---- */
    .category-card {
        flex: 0 0 100px;
        position: relative;
        z-index: 2;
        text-align: center;
        cursor: pointer;
        transform-style: preserve-3d;
    }

    /* ---- 3D TILT layer ---- */
    .category-tilt {
        position: relative;
        width: 82px;
        height: 82px;
        margin: auto;
        transform-style: preserve-3d;
        transition: transform .5s cubic-bezier(.22, 1, .36, 1);
        animation: tiltIdle 6s ease-in-out infinite;
        animation-delay: calc(var(--i, 0) * -0.35s);
    }

    @keyframes tiltIdle {

        0%,
        100% {
            transform: rotateX(0deg) rotateY(0deg) translateY(0);
        }

        25% {
            transform: rotateX(6deg) rotateY(-6deg) translateY(-3px);
        }

        50% {
            transform: rotateX(-4deg) rotateY(8deg) translateY(-6px);
        }

        75% {
            transform: rotateX(5deg) rotateY(-4deg) translateY(-3px);
        }
    }

    .category-card:hover .category-tilt {
        animation-play-state: paused;
        transform: rotateX(-12deg) rotateY(14deg) translateY(-10px) scale(1.08);
    }

    /* ---- shimmer ring ---- */
    .category-shimmer {
        position: absolute;
        inset: -8px;
        border-radius: 50%;
        background: conic-gradient(from 0deg,
                transparent 0deg,
                color-mix(in srgb, var(--cat-primary) 70%, transparent) 60deg,
                color-mix(in srgb, var(--cat-hover) 90%, transparent) 120deg,
                transparent 200deg,
                color-mix(in srgb, var(--cat-primary) 60%, transparent) 280deg,
                transparent 360deg);
        filter: blur(6px);
        opacity: 0;
        transition: opacity .4s ease;
        animation: shimmerSpin 4s linear infinite;
        z-index: 0;
        pointer-events: none;
    }

    .category-card:hover .category-shimmer {
        opacity: .9;
    }

    @keyframes shimmerSpin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ---- circle ---- */
    .category-circle {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        font-size: 42px;
        font-weight: 700;
        color: var(--cat-primary);
        overflow: hidden;
        background:
            radial-gradient(circle at 30% 25%,
                #ffffff 0%,
                color-mix(in srgb, var(--cat-primary) 12%, #ffffff) 60%,
                color-mix(in srgb, var(--cat-primary) 22%, #ffffff) 100%);
        border: 2px solid color-mix(in srgb, var(--cat-primary) 25%, transparent);
        box-shadow:
            0 6px 16px color-mix(in srgb, var(--cat-primary) 22%, transparent),
            inset 0 -6px 12px color-mix(in srgb, var(--cat-primary) 10%, transparent),
            inset 0 6px 10px rgba(255, 255, 255, .8);
        transition: border-color .3s ease, box-shadow .3s ease;
        transform: translateZ(0);
    }

    .category-card:hover .category-circle {
        border-color: color-mix(in srgb, var(--cat-hover) 55%, transparent);
        box-shadow:
            0 14px 30px color-mix(in srgb, var(--cat-hover) 40%, transparent),
            inset 0 -8px 16px color-mix(in srgb, var(--cat-hover) 15%, transparent),
            inset 0 8px 14px rgba(255, 255, 255, .9);
    }

    .category-icon-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 14%;
        display: block;
    }

    /* ---- name ---- */
    .category-name {
        margin-top: 12px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100px;
        color: #211329;
        transition: color .25s ease;
    }

    .category-card:hover .category-name {
        color: var(--cat-hover);
    }

    /* ---- arrows ---- */
    .category-arrow {
        position: absolute;
        z-index: 5;
        top: 50%;
        transform: translateY(-50%);
        width: 37px;
        height: 37px;
        display: grid;
        place-items: center;
        background: #ffffff;
        color: var(--cat-primary);
        border: 1px solid color-mix(in srgb, var(--cat-primary) 25%, transparent);
        border-radius: 50%;
        box-shadow: 0 5px 16px color-mix(in srgb, var(--cat-primary) 20%, transparent);
        transition: .2s ease;
    }

    .category-arrow:hover {
        background: var(--cat-primary);
        color: #ffffff;
        border-color: var(--cat-primary);
        box-shadow: 0 8px 20px color-mix(in srgb, var(--cat-primary) 40%, transparent);
    }

    .category-prev {
        left: 9px;
    }

    .category-next {
        right: 9px;
    }

    /* ---- responsive ---- */
    @media (max-width: 1023px) {
        .category-section {
            margin-top: 22px;
            padding: 0 12px;
        }

        .category-area {
            padding: 4px 23px;
            border-radius: 25px;
        }

        .category-track {
            gap: 10px;
        }

        .category-card {
            flex-basis: 88px;
        }

        .category-tilt {
            width: 72px;
            height: 72px;
        }

        .category-circle {
            font-size: 37px;
        }

        .category-name {
            font-size: 11px;
            max-width: 88px;
        }

        .cat-leaf-1 {
            width: 150px;
            height: 150px;
            right: -55px;
            top: -40px;
        }

        .cat-leaf-2 {
            width: 85px;
            height: 85px;
            right: -22px;
            bottom: -30px;
        }
    }

    @media (max-width: 640px) {
        .category-section {
            margin-top: 22px;
            padding: 0 9px;
        }

        .category-area {
            padding: 8px 8px;
            border-radius: 23px;
        }

        .category-track {
            gap: 8px;
            padding: 24px 22px 18px;
        }

        .category-card {
            flex-basis: 77px;
        }

        .category-tilt {
            width: 64px;
            height: 64px;
        }

        .category-circle {
            font-size: 32px;
        }

        .category-name {
            font-size: 10px;
            margin-top: 10px;
            max-width: 77px;
        }

        .category-arrow {
            width: 29px;
            height: 29px;
        }

        .category-prev {
            left: 3px;
        }

        .category-next {
            right: 3px;
        }

        .cat-leaf-1 {
            width: 115px;
            height: 115px;
            right: -42px;
            top: -30px;
            opacity: .75;
        }

        .cat-leaf-2 {
            width: 65px;
            height: 65px;
            right: -18px;
            bottom: -22px;
            opacity: .7;
        }
    }

    @media (max-width: 480px) {
        .category-section {
            padding-left: 8px;
            padding-right: 8px;
            margin-top: 18px;
        }

        .category-area {
            padding-left: 5px;
            padding-right: 5px;
        }

        .category-track {
            padding: 20px 22px 14px;
            gap: 7px;
        }

        .category-card {
            flex: 0 0 76px;
        }

        .category-tilt {
            width: 62px;
            height: 62px;
        }

        .category-circle {
            font-size: 30px;
        }

        .category-name {
            font-size: 9px;
            max-width: 76px;
        }

        .cat-leaf-1 {
            width: 85px;
            height: 85px;
            right: -32px;
            top: -24px;
            opacity: .65;
        }

        .cat-leaf-2 {
            width: 50px;
            height: 50px;
            right: -14px;
            bottom: -18px;
            opacity: .6;
        }
    }

    @media (max-width: 360px) {
        .category-card {
            flex-basis: 70px;
        }

        .category-tilt {
            width: 57px;
            height: 57px;
        }

        .category-circle {
            font-size: 27px;
        }

        .category-name {
            max-width: 70px;
        }

        .cat-leaf-1,
        .cat-leaf-2 {
            opacity: .5;
        }
    }

    /* ---- accessibility ---- */
    @media (prefers-reduced-motion: reduce) {

        .category-tilt,
        .category-shimmer,
        .cat-leaf {
            animation: none !important;
            transition: none !important;
        }

        .category-card:hover .category-tilt {
            transform: none;
        }
    }

    .category-section,
    .category-area {
        min-width: 0;
        max-width: 100%;
    }
</style>


<!-- ============================================================
     SCRIPT — CATEGORY SLIDER ARROWS
============================================================ -->
<script>
    (function() {
        const track = document.getElementById('categoryTrack');
        const prev = document.getElementById('categoryPrev');
        const next = document.getElementById('categoryNext');
        if (!track || !prev || !next) return;

        next.addEventListener('click', () => {
            track.scrollBy({
                left: 260,
                behavior: 'smooth'
            });
        });
        prev.addEventListener('click', () => {
            track.scrollBy({
                left: -260,
                behavior: 'smooth'
            });
        });
    })();
</script>