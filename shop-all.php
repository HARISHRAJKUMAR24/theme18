<?php
include_once __DIR__ . "/includes/files_includes.php";

$primary_color = getData("color",       "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ed168b';
$hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#dc007b';

$categoryStmt   = getCategories();
$allCategories  = $categoryStmt ? $categoryStmt->fetchAll(PDO::FETCH_ASSOC) : [];
$allCategories  = array_reverse($allCategories);
$parentCategories = [];
$subCategoriesMap = [];
$allSubs          = [];

foreach ($allCategories as $cat) {
    $parentId = (int)($cat['parent_category'] ?? 0);
    if ($parentId <= 0) {
        $parentCategories[] = $cat;
    } else {
        $subCategoriesMap[$parentId][] = $cat;
        $allSubs[] = $cat;
    }
}
if (empty($parentCategories)) $parentCategories = $allCategories;

$topCategories  = array_slice($parentCategories, 0, 5);
$productStmt    = getProducts();
$allProducts    = $productStmt ? $productStmt->fetchAll(PDO::FETCH_ASSOC) : [];
$categoryCount  = count($parentCategories);
$productCount   = count($allProducts);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>
    <style>
        :root {
            --t18-primary: <?= htmlspecialchars($primary_color, ENT_QUOTES) ?>;
            --t18-hover: <?= htmlspecialchars($hover_color, ENT_QUOTES) ?>;
            --t18-ink: #0f1115;
            --t18-bg: #f8f9fc;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            overflow-x: clip;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--t18-bg);
            color: var(--t18-ink);
            overflow-x: clip;
        }

        /* ============================================================
           GLOBAL AMBIENT BACKGROUND (3D Orbs & Glow)
           ============================================================ */
        .ambient-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .ambient-orb {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #ffffff 0%, color-mix(in srgb, var(--t18-primary) 50%, #ffffff) 50%, color-mix(in srgb, var(--t18-hover) 80%, #ffffff) 100%);
            box-shadow:
                0 40px 80px -20px color-mix(in srgb, var(--t18-primary) 30%, transparent),
                inset 0 -20px 40px rgba(0, 0, 0, 0.08),
                inset 0 20px 40px rgba(255, 255, 255, 0.9);
            opacity: 0.6;
            animation: floatOrb 15s ease-in-out infinite alternate;
        }

        .orb-1 {
            width: 350px;
            height: 350px;
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 250px;
            height: 250px;
            top: 30%;
            right: -80px;
            animation-delay: -5s;
            opacity: 0.4;
        }

        .orb-3 {
            width: 180px;
            height: 180px;
            bottom: 10%;
            left: 5%;
            animation-delay: -10s;
            opacity: 0.35;
        }

        @keyframes floatOrb {
            0% {
                transform: translate(0, 0) scale(1);
            }

            100% {
                transform: translate(40px, -40px) scale(1.05);
            }
        }

        /* ============================================================
           WRAPPER — compact padding
           ============================================================ */
        .uniq-wrap {
            position: relative;
            z-index: 1;
            max-width: 1600px;
            margin: 0 auto;
            padding: 16px clamp(12px, 2vw, 28px) 24px;
        }

        /* ============================================================
           HERO SECTION — tight margins
           ============================================================ */
        .uniq-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: clamp(20px, 3vw, 40px);
            padding: clamp(22px, 3.2vw, 40px);
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(24px);
            border-radius: 32px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow:
                0 30px 60px -30px color-mix(in srgb, var(--t18-primary) 20%, transparent),
                inset 0 1px 0 rgba(255, 255, 255, 1);
            margin-bottom: 22px;
            /* reduced */
            position: relative;
            overflow: hidden;
        }

        .uniq-hero-text h1 {
            font-size: clamp(2.4rem, 4.5vw, 4rem);
            font-weight: 900;
            line-height: 0.95;
            letter-spacing: -0.06em;
            margin: 0 0 12px;
        }

        .uniq-hero-text h1 span {
            background: linear-gradient(90deg, var(--t18-primary), var(--t18-hover));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .uniq-hero-text p {
            font-size: 1rem;
            color: #555;
            max-width: 450px;
            line-height: 1.55;
            margin: 0 0 18px;
        }

        .uniq-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: var(--t18-ink);
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            border-radius: 99px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -10px rgba(0, 0, 0, 0.3);
        }

        .uniq-btn:hover {
            background: var(--t18-primary);
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -10px var(--t18-primary);
        }

        /* --- HERO 3D LOGO ORB --- */
        .hero-logo-orb {
            width: clamp(120px, 17vw, 200px);
            height: clamp(120px, 17vw, 200px);
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #ffffff 0%, color-mix(in srgb, var(--t18-primary) 50%, #ffffff) 50%, color-mix(in srgb, var(--t18-hover) 80%, #ffffff) 100%);
            box-shadow:
                0 30px 60px -20px color-mix(in srgb, var(--t18-primary) 40%, transparent),
                inset 0 -15px 30px rgba(0, 0, 0, 0.1),
                inset 0 15px 30px rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            flex-shrink: 0;
            animation: floatOrb 6s ease-in-out infinite alternate;
            transform-style: preserve-3d;
        }

        .hero-logo-orb img {
            width: 55%;
            height: 55%;
            object-fit: contain;
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.15));
            transform: translateZ(30px);
            transition: transform 0.4s ease;
        }

        .hero-logo-orb:hover img {
            transform: translateZ(50px) scale(1.1);
        }

        .hero-logo-fallback {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 900;
            color: #fff;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateZ(30px);
        }

        /* ============================================================
           SECTION HEADER — tight bottom margin
           ============================================================ */
        .uniq-sec-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 12px;
            /* reduced */
            padding: 0 4px;
        }

        .uniq-sec-head h2 {
            font-size: clamp(1.7rem, 3vw, 2.4rem);
            font-weight: 900;
            letter-spacing: -0.04em;
            margin: 0;
        }

        .uniq-sec-head h2 span {
            color: var(--t18-primary);
        }

        .uniq-sec-head a {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--t18-primary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .uniq-sec-head a:hover {
            color: var(--t18-hover);
        }

        /* ============================================================
           MAGNETIC 3D CATEGORY GRID — tight bottom margin
           ============================================================ */
        .uniq-cat-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 14px;
            margin-bottom: 28px;
            /* reduced */
            perspective: 1200px;
        }

        .uniq-cat-card {
            position: relative;
            aspect-ratio: 1;
            border-radius: 24px;
            background: linear-gradient(135deg, #ffffff, color-mix(in srgb, var(--t18-primary) 5%, #ffffff));
            border: 1px solid rgba(255, 255, 255, 1);
            box-shadow: 0 16px 32px -20px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--t18-ink);
            transform-style: preserve-3d;
            transition: transform 0.1s ease, box-shadow 0.3s ease;
            will-change: transform;
        }

        .uniq-cat-orb {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #fff, var(--t18-primary));
            box-shadow:
                0 15px 30px -10px color-mix(in srgb, var(--t18-primary) 50%, transparent),
                inset 0 -8px 15px rgba(0, 0, 0, 0.1),
                inset 0 8px 15px rgba(255, 255, 255, 0.8);
            margin-bottom: 12px;
            transform: translateZ(40px);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .uniq-cat-orb img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 12px;
        }

        .uniq-cat-name {
            font-size: 1rem;
            font-weight: 800;
            transform: translateZ(20px);
            text-align: center;
            padding: 0 8px;
        }

        .uniq-cat-card:hover .uniq-cat-orb {
            transform: translateZ(80px) scale(1.1);
        }

        .uniq-cat-card:hover {
            box-shadow: 0 30px 60px -20px color-mix(in srgb, var(--t18-primary) 30%, transparent);
        }

        /* ============================================================
           PRODUCT SECTIONS — tight bottom margin
           ============================================================ */
        .uniq-product-section {
            margin-bottom: 20px;
            /* reduced */
        }

        /* ============================================================
           FLOATING PILL SUB-CATEGORIES — tight padding
           ============================================================ */
        .uniq-sub-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            padding: 16px 0 0;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .uniq-sub-pill {
            padding: 8px 18px;
            background: #fff;
            border-radius: 99px;
            font-weight: 700;
            font-size: 0.82rem;
            color: var(--t18-ink);
            text-decoration: none;
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .uniq-sub-pill:hover {
            background: var(--t18-primary);
            color: #fff;
            border-color: var(--t18-primary);
            transform: translateY(-5px) scale(1.08);
            box-shadow: 0 15px 25px -10px var(--t18-primary);
        }

        /* ============================================================
           RESPONSIVE — extra-tight on mobile
           ============================================================ */
        @media (max-width: 1200px) {
            .uniq-cat-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 900px) {
            .uniq-hero {
                flex-direction: column-reverse;
                text-align: center;
                padding: 24px 16px;
                border-radius: 24px;
                margin-bottom: 16px;
                gap: 12px;
            }

            .uniq-hero-text p {
                margin: 0 auto 14px;
                font-size: 0.95rem;
            }

            .hero-logo-orb {
                margin-bottom: 6px;
            }

            .uniq-cat-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
                margin-bottom: 20px;
            }

            .uniq-cat-card {
                border-radius: 18px;
            }

            .uniq-cat-orb {
                width: 56px;
                height: 56px;
                margin-bottom: 8px;
            }

            .uniq-cat-name {
                font-size: 0.9rem;
            }

            .uniq-sec-head {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
                margin-bottom: 10px;
            }

            .uniq-sec-head h2 {
                font-size: 1.5rem;
            }

            .uniq-product-section {
                margin-bottom: 14px;
            }
        }

        @media (max-width: 600px) {
            .uniq-wrap {
                padding: 10px 10px 20px;
            }

            .uniq-hero {
                padding: 18px 12px;
                border-radius: 20px;
                margin-bottom: 14px;
            }

            .uniq-hero-text h1 {
                font-size: 1.9rem;
                margin-bottom: 8px;
            }

            .uniq-hero-text p {
                font-size: 0.88rem;
                margin-bottom: 12px;
            }

            .uniq-btn {
                padding: 10px 20px;
                font-size: 0.82rem;
            }

            .hero-logo-orb {
                width: 100px;
                height: 100px;
            }

            .uniq-cat-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                margin-bottom: 16px;
            }

            .uniq-cat-card {
                aspect-ratio: 1.05;
                border-radius: 14px;
                padding: 8px;
            }

            .uniq-cat-orb {
                width: 42px;
                height: 42px;
                transform: translateZ(20px);
                margin-bottom: 6px;
            }

            .uniq-cat-card:hover .uniq-cat-orb {
                transform: translateZ(30px) scale(1.1);
            }

            .uniq-cat-name {
                font-size: 0.78rem;
                transform: translateZ(10px);
            }

            .uniq-sub-pill {
                padding: 7px 13px;
                font-size: 0.72rem;
            }

            .uniq-sec-head h2 {
                font-size: 1.3rem;
            }

            .uniq-sub-cloud {
                padding: 12px 0 0;
                gap: 6px;
            }

            .uniq-product-section {
                margin-bottom: 10px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body>

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <!-- Ambient Background Orbs -->
    <div class="ambient-bg">
        <div class="ambient-orb orb-1"></div>
        <div class="ambient-orb orb-2"></div>
        <div class="ambient-orb orb-3"></div>
    </div>

    <div class="uniq-wrap">

        <!-- ==================== HERO ==================== -->
        <section class="uniq-hero">
            <div class="uniq-hero-text">
                <h1>The new <br><span>standard.</span></h1>
                <p>Elevate your everyday. Discover a handpicked selection of products designed to stand out. No clutter, just the best.</p>
                <a href="#categories" class="uniq-btn">
                    Explore Collection <i class="bi bi-arrow-down"></i>
                </a>
            </div>

            <!-- 3D Logo Orb -->
            <div class="hero-logo-orb">
                <?php if (getSettings("logo")): ?>
                    <img src="<?= UPLOADS_URL . getSettings("logo") ?>" alt="<?= htmlspecialchars($storeName) ?>">
                <?php else: ?>
                    <div class="hero-logo-fallback">
                        <?= htmlspecialchars(strtoupper(mb_substr($storeName ?? 'S', 0, 1))) ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ==================== MAGNETIC 3D CATEGORIES ==================== -->
        <div id="categories">
            <div class="uniq-sec-head">
                <h2>Shop by <span>Category</span></h2>
                <a href="<?= $storeUrl ?>shop-all">View All &rarr;</a>
            </div>

            <div class="uniq-cat-grid">
                <?php foreach ($topCategories as $cat):
                    $catImage = !empty($cat['icon']) ? UPLOADS_URL . $cat['icon'] : '';
                ?>
                    <a href="<?= htmlspecialchars($storeUrl . 'category/' . $cat['slug'], ENT_QUOTES) ?>" class="uniq-cat-card">
                        <div class="uniq-cat-orb">
                            <?php if ($catImage): ?>
                                <img src="<?= htmlspecialchars($catImage, ENT_QUOTES) ?>" alt="">
                            <?php else: ?>
                                <i class="bi bi-box-seam" style="font-size: 1.8rem; display: flex; justify-content: center; align-items: center; height: 100%; color: var(--t18-primary);"></i>
                            <?php endif; ?>
                        </div>
                        <span class="uniq-cat-name"><?= htmlspecialchars($cat['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ==================== CURATED FOR YOU (CAROUSEL 1) ==================== -->
        <section class="uniq-product-section">
            <div class="uniq-sec-head">
                <h2>Curated <span>For You</span></h2>
            </div>

            <div>
                <?= getProductCarouselSection('', $storeUrl . 'shop-all', 10) ?>
            </div>
        </section>

        <!-- ==================== POPULAR PRODUCTS (CAROUSEL 2) ==================== -->
        <section class="uniq-product-section">
            <div class="uniq-sec-head">
                <h2>Popular <span>Products</span></h2>
            </div>

            <div>
                <?= getProductCarouselSection_random('', $storeUrl . 'shop-all', 10) ?>
            </div>
        </section>

        <!-- ==================== FLOATING SUB-CATEGORIES ==================== -->
        <?php if (!empty($allSubs)): ?>
            <div class="uniq-sub-cloud">
                <?php foreach (array_slice($allSubs, 0, 15) as $sub): ?>
                    <a href="<?= htmlspecialchars($storeUrl . 'category/' . $sub['slug'], ENT_QUOTES) ?>" class="uniq-sub-pill">
                        <?= htmlspecialchars($sub['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

    <!-- JavaScript for the Magnetic 3D Tilt Effect -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.matchMedia("(pointer: fine)").matches) {
                const cards = document.querySelectorAll('.uniq-cat-card');

                cards.forEach(card => {
                    card.addEventListener('mousemove', handleTilt);
                    card.addEventListener('mouseleave', resetTilt);
                });

                function handleTilt(e) {
                    const card = this;
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    const rotateX = ((y - centerY) / centerY) * -12;
                    const rotateY = ((x - centerX) / centerX) * 12;
                    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.03, 1.03, 1.03)`;
                }

                function resetTilt() {
                    this.style.transform = `perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)`;
                }
            }
        });
    </script>
</body>

</html>