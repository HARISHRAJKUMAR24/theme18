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
    <?php include_once __DIR__ . "/includes/banner.php"; ?>

    <?php include_once __DIR__ . "/includes/advance-category.php"; ?>

<?= getProductCarouselSection('Most loved 💗', $storeUrl . 'shop-all', 10) ?>



    <?php include_once __DIR__ . "/includes/offers.php" ?>

        <?php include_once __DIR__ . "/includes/why-shop-us.php" ?>
        <?= getProductCarouselSection_random('Popular Products', $storeUrl . 'shop-all', 10) ?>

    <!--Footer File Includes that file has all JS Files includes links-->
    <?php include_once __DIR__ . "/includes/footer.php"; ?>
    <!-- ============================================================
         SCRIPTS
    ============================================================ -->





</body>

</html>