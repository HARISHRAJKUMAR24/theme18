<?php include_once __DIR__ . "/includes/files_includes.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>

    <!-- ============================================================
         STYLES — SECTION 1: GLOBAL BASE
    ============================================================ -->
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, sans-serif;
            overflow-x: hidden;
            background:
                radial-gradient(ellipse at 100% 45%, #f8d8ff, transparent 35%),
                radial-gradient(ellipse at 0% 70%, #f8e4ff, transparent 30%),
                #fffaff;
            color: #211329;
        }

        html {
            width: 100%;
            max-width: 100%;
            overflow-x: clip;
            scroll-behavior: smooth;
        }

        body {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            overflow-x: clip;
        }

        img,
        svg,
        video,
        canvas {
            max-width: 100%;
        }
    </style>







</head>

<body>

    <?php include_once __DIR__ . "/includes/minimum-order.php"; ?>

    <!-- ============================================================
     NO COUNTDOWN SCRIPT NEEDED — amount is static from DB
============================================================ -->
    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <?php include_once __DIR__ . "/includes/category.php"; ?>

    <!-- ============================================================
         HTML — SECTION 4: BANNER (external include)
    ============================================================ -->
    <?php include_once __DIR__ . "/includes/Banner.php"; ?>



    <section class="product-section max-w-[1450px] mx-auto px-4 sm:px-6 py-10">
        <div class="product-bg" aria-hidden="true">
            <span class="orb orb-1"></span>
            <span class="orb orb-2"></span>
            <span class="orb orb-3"></span>
            <span class="orb orb-4"></span>
            <span class="glow-ring"></span>
        </div>
        <div class="relative z-10">
            <?= getProductSectionHeading('Trending', 'Now.', $storeUrl . 'shop-all') ?>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
                <?php
                $stmt = getProducts();
                $products = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                $i = 0;
                foreach ($products as $p) {
                    if ($i++ >= 10) break;
                    echo getProductHtml($p['id']);
                }
                ?>
            </div>
        </div>
    </section>



    <?php include_once __DIR__ . "/includes/offers.php" ?>

        <?php include_once __DIR__ . "/includes/why-shop-us.php" ?>


    <!-- ============================================================
         SCRIPTS
    ============================================================ -->

    <!-- 1. Global Lucide init -->
    <script>
        lucide.createIcons();
    </script>







</body>

</html>