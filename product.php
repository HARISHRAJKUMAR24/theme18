<?php include_once __DIR__ . "/includes/files_includes.php"; ?>
<?php
// -------------------------
// Get Random Products For Customer Also Bought
// -------------------------
function getRandomProducts($limit = 3)
{
    $stmt = getProducts();
    $allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    shuffle($allProducts);
    return array_slice($allProducts, 0, $limit);
}

// Fetch DB Colors
$primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ed168b';
$hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#dc007b';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>
    <style>
        :root {
            --t18-primary: <?= htmlspecialchars($primary_color) ?>;
            --t18-hover: <?= htmlspecialchars($hover_color) ?>;
        }

        /* ---------- Page background: DB-coloured radial glows ---------- */
        body {
            background:
                radial-gradient(circle at 8% 12%,
                    color-mix(in srgb, var(--t18-primary) 8%, #fcfcfc) 0%,
                    transparent 42%),
                radial-gradient(circle at 92% 88%,
                    color-mix(in srgb, var(--t18-hover) 8%, #fcfcfc) 0%,
                    transparent 42%),
                #fafafa;
            min-height: 100vh;
        }

        /* Floating bubble accents (page-wide, low-key) */
        .t18-page-bubbles {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .t18-page-bubbles .bubble {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 25%,
                    #ffffff 0%,
                    color-mix(in srgb, var(--t18-primary) 45%, #ffffff) 55%,
                    color-mix(in srgb, var(--t18-primary) 90%, #ffffff) 100%);
            box-shadow:
                0 20px 50px color-mix(in srgb, var(--t18-primary) 25%, transparent),
                inset 0 -8px 16px rgba(0, 0, 0, .05),
                inset 0 8px 16px rgba(255, 255, 255, .85);
            opacity: .35;
            animation: t18PageFloat 14s ease-in-out infinite alternate;
            will-change: transform;
        }

        .t18-page-bubbles .bubble-1 {
            width: 240px;
            height: 240px;
            top: -60px;
            left: -70px;
            animation-delay: -2s;
        }

        .t18-page-bubbles .bubble-2 {
            width: 160px;
            height: 160px;
            top: 40%;
            right: -50px;
            animation-delay: -6s;
            opacity: .25;
        }

        .t18-page-bubbles .bubble-3 {
            width: 100px;
            height: 100px;
            bottom: 6%;
            left: 8%;
            animation-delay: -10s;
            opacity: .2;
        }

        @keyframes t18PageFloat {
            0% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(14px, -18px) scale(1.05);
            }

            100% {
                transform: translate(-8px, 12px) scale(.97);
            }
        }

        @media (max-width: 640px) {
            .t18-page-bubbles .bubble-1 {
                width: 140px;
                height: 140px;
            }

            .t18-page-bubbles .bubble-2 {
                width: 90px;
                height: 90px;
            }

            .t18-page-bubbles .bubble-3 {
                display: none;
            }
        }

        /* ---------- 3D glass main panel ---------- */
        .t18-main-glass {
            position: relative;
            z-index: 1;
            background:
                linear-gradient(145deg,
                    rgba(255, 255, 255, .90) 0%,
                    color-mix(in srgb, var(--t18-primary) 3%, rgba(255, 255, 255, .85)) 100%);
            backdrop-filter: blur(18px);
            border-radius: 32px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 15%, rgba(255, 255, 255, .6));
            box-shadow:
                0 24px 60px -20px color-mix(in srgb, var(--t18-primary) 25%, transparent),
                0 8px 22px -8px color-mix(in srgb, var(--t18-hover) 12%, transparent),
                inset 0 1px 0 rgba(255, 255, 255, .9);
        }

        @media (max-width: 640px) {
            .t18-main-glass {
                border-radius: 22px;
            }
        }

        /* ---------- 3D Product Image Container ---------- */
        .t18-product-media {
            position: relative;
            background:
                radial-gradient(circle at 30% 25%,
                    color-mix(in srgb, #ffffff 80%, var(--t18-primary)) 0%,
                    color-mix(in srgb, var(--t18-primary) 12%, #fff) 45%,
                    color-mix(in srgb, var(--t18-hover) 18%, #fff) 100%);
            border-radius: 28px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 20%, transparent);
            box-shadow:
                0 20px 50px -20px color-mix(in srgb, var(--t18-primary) 35%, transparent),
                0 10px 22px -10px color-mix(in srgb, var(--t18-hover) 18%, transparent),
                inset 0 0 0 1px rgba(255, 255, 255, .6);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            transform-style: preserve-3d;
            transition: transform .4s ease, box-shadow .4s ease;
        }

        .t18-product-media:hover {
            transform: translateY(-5px);
            box-shadow:
                0 30px 60px -20px color-mix(in srgb, var(--t18-primary) 45%, transparent),
                0 14px 28px -10px color-mix(in srgb, var(--t18-hover) 25%, transparent),
                inset 0 0 0 1px rgba(255, 255, 255, .75);
        }

        #mainProductImage {
            width: 100%;
            max-height: 60vh;
            min-height: 250px;
            object-fit: contain;
            filter: drop-shadow(0 15px 25px rgba(0, 0, 0, .15)) drop-shadow(0 6px 10px color-mix(in srgb, var(--t18-primary) 20%, transparent));
            transition: transform .5s cubic-bezier(.22, 1, .36, 1);
        }

        .t18-product-media:hover #mainProductImage {
            transform: scale(1.05) rotate(1deg);
        }

        /* ---------- 3D Thumbnails ---------- */
        .t18-thumb {
            width: 70px;
            height: 70px;
            border-radius: 14px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            background: #fff;
            transition: all .3s cubic-bezier(.22, 1, .36, 1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
        }

        .t18-thumb:hover,
        .t18-thumb.active {
            border-color: var(--t18-primary);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 20px color-mix(in srgb, var(--t18-primary) 30%, transparent);
        }

        /* ---------- Variant Buttons ---------- */
        .variant-btn {
            padding: 10px 16px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #fff;
            font-weight: 600;
            font-size: 13px;
            transition: all .2s ease;
        }

        .variant-btn:hover {
            border-color: var(--t18-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px color-mix(in srgb, var(--t18-primary) 15%, transparent);
        }

        .variant-btn.ring-2 {
            border-color: var(--t18-primary) !important;
            background: color-mix(in srgb, var(--t18-primary) 8%, #fff) !important;
            color: var(--t18-primary);
        }

        /* ---------- Colour / Size Buttons ---------- */
        .color-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
            transition: all .2s ease;
        }

        .color-btn:hover,
        .color-btn.active-color {
            transform: scale(1.1);
            border-color: var(--t18-primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--t18-primary) 25%, transparent);
        }

        .size-btn {
            padding: 10px 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #fff;
            font-weight: 600;
            font-size: 13px;
            transition: all .2s ease;
        }

        .size-btn:hover:not(:disabled) {
            border-color: var(--t18-primary);
        }

        .size-btn.active-size {
            background: var(--t18-primary);
            color: #fff;
            border-color: var(--t18-primary);
            box-shadow: 0 4px 15px color-mix(in srgb, var(--t18-primary) 45%, transparent);
        }

        /* ---------- Add to Cart Button ---------- */
        .t18-add-btn {
            background: var(--t18-primary);
            color: #fff;
            border-radius: 999px;
            padding: 14px 32px;
            font-weight: 700;
            font-size: 15px;
            transition: all .3s ease;
            box-shadow:
                0 10px 24px -8px color-mix(in srgb, var(--t18-primary) 60%, transparent),
                inset 0 1px 0 rgba(255, 255, 255, .3);
        }

        .t18-add-btn:hover:not(:disabled) {
            background: var(--t18-hover);
            transform: translateY(-3px);
            box-shadow:
                0 14px 30px -8px color-mix(in srgb, var(--t18-hover) 65%, transparent),
                inset 0 1px 0 rgba(255, 255, 255, .4);
        }

        .t18-add-btn:disabled {
            background: #d1d5db;
            box-shadow: none;
            cursor: not-allowed;
        }

        /* ---------- View Cart ---------- */
        .t18-view-btn {
            border: 2px solid color-mix(in srgb, var(--t18-primary) 25%, #e5e7eb);
            color: #1f2937;
            background: #fff;
        }

        .t18-view-btn:hover {
            border-color: var(--t18-primary);
            color: var(--t18-primary);
            box-shadow: 0 8px 20px -8px color-mix(in srgb, var(--t18-primary) 40%, transparent);
        }

        /* ---------- Combo Builder ---------- */
        .combo-slot {
            background: color-mix(in srgb, var(--t18-primary) 3%, #f8fafc);
            border: 1px solid color-mix(in srgb, var(--t18-primary) 10%, #e2e8f0);
            border-radius: 14px;
            transition: all .3s ease;
        }

        .combo-slot:hover {
            border-color: color-mix(in srgb, var(--t18-primary) 35%, transparent);
            box-shadow: 0 6px 18px -8px color-mix(in srgb, var(--t18-primary) 25%, transparent);
        }

        /* ---------- Description & Related panels ---------- */
        .t18-panel {
            border-radius: 24px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 10%, #e5e7eb);
            background:
                linear-gradient(145deg, #ffffff 0%, color-mix(in srgb, var(--t18-primary) 2%, #ffffff) 100%);
            box-shadow: 0 12px 40px -20px color-mix(in srgb, var(--t18-primary) 20%, transparent);
        }

        /* ---------- Also Bought Card ---------- */
        .t18-also-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 10%, #e5e7eb);
            box-shadow: 0 8px 20px -14px color-mix(in srgb, var(--t18-primary) 20%, transparent);
            transition: all .3s ease;
        }

        .t18-also-card:hover {
            border-color: color-mix(in srgb, var(--t18-primary) 30%, transparent);
            transform: translateY(-4px);
            box-shadow: 0 16px 32px -14px color-mix(in srgb, var(--t18-primary) 35%, transparent);
        }

        .t18-also-media {
            background: color-mix(in srgb, var(--t18-primary) 6%, #f8fafc);
        }

        /* ---------- Toast animations (unchanged) ---------- */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .toast-slide-in {
            animation: slideInRight .5s cubic-bezier(.22, 1, .36, 1) forwards;
        }

        .toast-slide-out {
            animation: slideOutRight .5s ease-in forwards;
        }

        /* ---------- Responsive tweaks ---------- */
        @media (max-width: 768px) {
            #mainProductImage {
                max-height: 40vh;
            }

            .t18-product-media {
                padding: 10px;
                border-radius: 20px;
            }

            .t18-main-glass {
                border-radius: 22px;
            }
        }

        /* ---------- Reduced motion ---------- */
        @media (prefers-reduced-motion: reduce) {
            .t18-page-bubbles .bubble {
                animation: none !important;
            }

            #mainProductImage,
            .t18-product-media,
            .t18-thumb,
            .variant-btn,
            .color-btn,
            .size-btn,
            .t18-add-btn,
            .t18-also-card {
                transition: none !important;
            }
        }
    </style>
</head>

<body class="font-sans min-h-screen relative">

    <!-- Page background bubbles -->
    <div class="t18-page-bubbles" aria-hidden="true">
        <span class="bubble bubble-1"></span>
        <span class="bubble bubble-2"></span>
        <span class="bubble bubble-3"></span>
    </div>

    <?php include_once __DIR__ . "/includes/minimum-order.php"; ?>

    <!--Php File Include For Nav Bar-->
    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <?php
    if (!isset($_GET['slug'])) redirect($storeUrl);
    $slug = $_GET['slug'];

    $productQuery = $db->prepare("SELECT * FROM seller_products WHERE slug = ? AND seller_id = ? AND store_id = ? AND status = 1 AND (visibility = 'publish' OR (visibility = 'schedule' AND schedule_date <= NOW()))");
    $productQuery->execute([$slug, $sellerId, $storeId]);
    $product = $productQuery->fetch(PDO::FETCH_ASSOC);
    if (!$product) redirect($storeUrl);

    $id = $product['id'];
    $product_id = $product['product_id'];
    $name = $product['name'];
    $variation = $product['variation'];
    $price = $product['price'];
    $mrp_price = $product['mrp_price'];
    $description = $product['description'];
    $image = $product['image'];
    $unlimited_stock = $product['unlimited_stock'];
    $total_stocks = $product['total_stocks'] ?? 0;
    $unit_type = $product['unit_type'] ?? 'pcs';
    $maxQty = ($unlimited_stock == 1) ? "Unlimited" : (int)$total_stocks;
    $comboEnabled = !empty($product['combo_enabled']) && (int)$product['combo_enabled'] === 1;
    $comboSelectionCount = (int)($product['combo_selection_count'] ?? 0);

    $initialVariantId = isset($_GET['variation']) && $_GET['variation'] != '' && $_GET['variation'] !== 'main' ? $_GET['variation'] : null;

    $db->prepare("UPDATE seller_products SET visitors=visitors+1 WHERE id=?")->execute([$id]);
    $visitors = $product['visitors'] + 1;

    $advancedVariantsCheck = readData("*", "seller_product_advanced_variants", "product_id='$product_id'");
    if ($advancedVariantsCheck && $advancedVariantsCheck->rowCount() > 0) {
        $basicVariants = readData("*", "seller_product_advanced_variants", "product_id='$product_id'");
        $isAdvanced = true;
    } else {
        $basicVariants = readData("*", "seller_product_variants", "product_id='$product_id'");
        $isAdvanced = false;
    }

    $groupedVariants = [];
    if ($isAdvanced && $basicVariants && $basicVariants->rowCount() > 0) {
        while ($bv = $basicVariants->fetch()) {
            $colorId = $bv['color'] ?? '';
            $colorName = getData("color_name", "product_colors", "id='$colorId'");
            $colorCode = getData("color_code", "product_colors", "id='$colorId'") ?? '#ccc';
            $size = $bv['size'] ?? '';

            if (!isset($groupedVariants[$colorId])) {
                $groupedVariants[$colorId] = [
                    'color_name' => $colorName,
                    'color_code' => $colorCode,
                    'sizes' => []
                ];
            }
            $groupedVariants[$colorId]['sizes'][] = [
                'id' => $bv['id'],
                'size' => $size,
                'price' => $bv['price'],
                'mrp_price' => $bv['mrp_price'],
                'stock' => $bv['stock'] ?? 0,
                'unlimited_stock' => $bv['unlimited_stock'] ?? 0,
                'unit_type' => $bv['unit_type'] ?? $unit_type,
                'image' => $bv['image'] ?? $image
            ];
        }
    }

    $cartVariantsQuery = $db->prepare("SELECT other, advanced_variant FROM customer_cart WHERE customer_id=? AND product_id=?");
    $cartVariantsQuery->execute([$cookie_id, $id]);
    $cartItems = $cartVariantsQuery->fetchAll(PDO::FETCH_ASSOC);

    $cartVariants = [];
    foreach ($cartItems as $item) {
        if (!empty($item['advanced_variant'])) {
            $cartVariants[] = $item['advanced_variant'];
        } else if (!empty($item['other'])) {
            $cartVariants[] = $item['other'];
        } else {
            $cartVariants[] = 'main';
        }
    }

    $inCartInitial = false;
    if ($initialVariantId === null) {
        $inCartInitial = in_array('main', $cartVariants);
    } else {
        $inCartInitial = in_array($initialVariantId, $cartVariants);
    }

    if ($unlimited_stock == 1) {
        $mainStockDisplay = "Unlimited stock";
    } else {
        if ($unit_type == "kg") $mainStockDisplay = ((int)$total_stocks / 1000) . " kg";
        else if ($unit_type == "litre") $mainStockDisplay = ((int)$total_stocks / 1000) . " litre";
        else if ($unit_type == "meter") $mainStockDisplay = (int)$total_stocks * 0.3048 . " meter";
        else $mainStockDisplay = (int)$total_stocks . " " . $unit_type;
    }

    $variantStocksDisplay = [];
    if ($basicVariants && $basicVariants->rowCount() > 0) {
        $basicVariants->execute();
        while ($bv = $basicVariants->fetch()) {
            if (($bv['unlimited_stock'] ?? 0) == 1) {
                $variantStocksDisplay[$bv['id']] = "Unlimited stock";
            } else {
                $vUnit = $bv['unit_type'] ?? $unit_type;
                $vStock = (int)($bv['stock'] ?? 0);
                $variantStocksDisplay[$bv['id']] = "$vStock $vUnit";
            }
        }
    }

    $additionalImages = [];
    $data = readData("*", "seller_product_additional_images", "product_id='$product_id'");
    while ($row = $data->fetch()) $additionalImages[] = UPLOADS_URL . $row['image'];
    ?>

    <!-- Main Product Section -->
    <section class="py-10 sm:py-16 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- 3D Glass Card -->
            <div class="t18-main-glass p-5 sm:p-8 lg:p-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

                    <!-- LEFT: IMAGES -->
                    <div class="flex flex-col-reverse lg:flex-row gap-4 justify-center items-start">
                        <div id="thumbnailContainer" class="flex lg:flex-col gap-3 overflow-x-auto lg:overflow-y-auto w-full lg:w-auto pb-2 lg:pb-0 scrollbar-hide">
                            <img src="<?= UPLOADS_URL . $image ?>" class="t18-thumb active" onclick="document.getElementById('mainProductImage').src=this.src; document.querySelectorAll('.t18-thumb').forEach(t=>t.classList.remove('active')); this.classList.add('active');">
                            <?php foreach ($additionalImages as $img): ?>
                                <img src="<?= $img ?>" class="t18-thumb" onclick="document.getElementById('mainProductImage').src=this.src; document.querySelectorAll('.t18-thumb').forEach(t=>t.classList.remove('active')); this.classList.add('active');">
                            <?php endforeach; ?>
                        </div>

                        <div class="t18-product-media w-full">
                            <img id="mainProductImage" src="<?= UPLOADS_URL . $image ?>" alt="<?= htmlspecialchars($name) ?>">
                        </div>
                    </div>

                    <!-- RIGHT: INFO -->
                    <div class="space-y-6">

                        <div>
                            <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 leading-tight mb-2">
                                <?= htmlspecialchars($name) ?>
                            </h1>
                            <p id="variantName" class="text-sm sm:text-base text-gray-500 font-medium">
                                <?= htmlspecialchars($variation) ?>
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <?php
                            $ratingData = readData("AVG(rating) as avg_rating, COUNT(*) as total_ratings", "product_ratings", "product_id='$id'");
                            $ratingRow = $ratingData->fetch();
                            $avgRating = round($ratingRow['avg_rating'] ?? 0, 1);
                            $totalRatings = $ratingRow['total_ratings'] ?? 0;
                            ?>
                            <div class="flex gap-1" id="productRatingUI">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star cursor-pointer text-xl transition-colors <?= $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-300' ?>" data-value="<?= $i ?>">&#9733;</span>
                                <?php endfor; ?>
                            </div>
                            <span id="ratingInfo" class="text-sm font-semibold text-gray-600">
                                <?= $avgRating ?> / 5 <span class="text-gray-400 font-normal">(<?= $totalRatings ?> reviews)</span>
                            </span>
                        </div>

                        <div class="flex items-end gap-3">
                            <span class="text-3xl font-black productPrice" style="color: var(--t18-primary);">
                                <?= currencyToSymbol($storeCurrency) . number_format($price, 2) ?>
                            </span>
                            <?php if ($mrp_price): ?>
                                <span class="text-lg text-gray-400 line-through font-medium productMrp mb-1">
                                    <?= currencyToSymbol($storeCurrency) . number_format($mrp_price, 2) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center gap-3">
                            <p id="stockDisplay" class="text-sm font-bold px-3 py-1.5 rounded-full <?= ($total_stocks <= 5 && $unlimited_stock != 1) ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' ?>">
                                <?= $mainStockDisplay ?>
                            </p>
                            <p class="text-sm text-gray-500 flex items-center gap-1.5">
                                <span class="text-red-500 animate-pulse">❤️</span> Viewed <?= (int)$visitors ?> times
                            </p>
                        </div>

                        <!-- Variant Selections ... same as before ... -->
                        <?php if ($comboEnabled): ?>
                            <div id="comboProductBuilder" class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100 mt-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2"><span class="text-xl">🎁</span> Build Your Combo</h3>
                                        <p class="text-xs text-gray-500">Choose <?= $comboSelectionCount ?> different variants</p>
                                    </div>
                                    <div id="comboSelectionCounter" class="px-3 py-1 rounded-full bg-white border border-gray-200 text-xs font-bold text-gray-600">
                                        0 / <?= $comboSelectionCount ?>
                                    </div>
                                </div>
                                <div id="comboSlots" class="space-y-3">
                                    <?php for ($i = 1; $i <= $comboSelectionCount; $i++): ?>
                                        <div class="combo-slot <?= $i === 1 ? '' : 'hidden' ?> p-3 rounded-xl" data-slot="<?= $i ?>">
                                            <div class="flex items-center justify-between gap-2 mb-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-6 h-6 shrink-0 rounded-full bg-[var(--t18-primary)] text-white flex items-center justify-center text-[11px] font-bold"><?= $i ?></span>
                                                    <span class="text-sm font-bold text-gray-800">Product <?= $i ?></span>
                                                </div>
                                                <span class="combo-slot-status text-[11px] font-semibold text-gray-400">Not selected</span>
                                            </div>

                                            <?php if ($isAdvanced): ?>
                                                <div class="combo-selection-inputs">
                                                    <div class="grid grid-cols-2 gap-2 mb-2">
                                                        <div>
                                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Color</label>
                                                            <select class="combo-color-select w-full h-10 px-3 border border-gray-200 rounded-lg bg-white text-sm focus:outline-none focus:border-[var(--t18-primary)]">
                                                                <option value="">Select Color</option>
                                                                <?php foreach ($groupedVariants as $colorId => $colorData): ?>
                                                                    <option value="<?= htmlspecialchars($colorId) ?>" data-color-name="<?= htmlspecialchars($colorData['color_name']) ?>">
                                                                        <?= htmlspecialchars($colorData['color_name']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Size</label>
                                                            <select class="combo-size-select w-full h-10 px-3 border border-gray-200 rounded-lg bg-gray-100 text-sm text-gray-400 focus:outline-none" disabled>
                                                                <option value="">Select Size</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="combo-selection-inputs">
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Variant</label>
                                                    <select class="combo-variant-select w-full h-10 px-3 border border-gray-200 rounded-lg bg-white text-sm focus:outline-none focus:border-[var(--t18-primary)]">
                                                        <option value="">Select Variant</option>
                                                        <?php $basicVariants->execute();
                                                        while ($bv = $basicVariants->fetch()):
                                                            $variantId = $bv['id'];
                                                            $variantLabel = $bv['variation'] ?? $bv['name'] ?? "Variant " . $variantId;
                                                            $stock = (int)($bv['stock'] ?? 0);
                                                            $unlimited = (int)($bv['unlimited_stock'] ?? 0);
                                                            $isOutOfStock = !$unlimited && $stock <= 0;
                                                        ?>
                                                            <option value="<?= htmlspecialchars($variantId) ?>" data-variant-name="<?= htmlspecialchars($variantLabel) ?>" data-price="<?= htmlspecialchars($bv['price']) ?>" data-mrp="<?= htmlspecialchars($bv['mrp_price']) ?>" data-stock="<?= $unlimited ? 'Unlimited' : $stock ?>" data-image="<?= htmlspecialchars(UPLOADS_URL . ($bv['image'] ?? $image)) ?>" <?= $isOutOfStock ? 'disabled' : '' ?>>
                                                                <?= htmlspecialchars($variantLabel) ?> <?= $isOutOfStock ? ' (Sold Out)' : '' ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                            <?php endif; ?>
                                            <div class="combo-selected-display hidden"></div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                        <?php elseif ($isAdvanced && !empty($groupedVariants)): ?>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-bold text-gray-800 mb-2">Select Color:</p>
                                    <div class="flex flex-wrap gap-3" id="colorContainer">
                                        <?php $firstColor = true;
                                        foreach ($groupedVariants as $colorId => $colorData): ?>
                                            <button class="color-btn <?= $firstColor ? 'active-color ring-2 ring-offset-2 ring-[var(--t18-primary)]' : '' ?>"
                                                style="background-color: <?= $colorData['color_code'] ?>;"
                                                data-color-id="<?= $colorId ?>"
                                                data-color-name="<?= htmlspecialchars($colorData['color_name']) ?>"
                                                title="<?= htmlspecialchars($colorData['color_name']) ?>">
                                            </button>
                                        <?php $firstColor = false;
                                        endforeach; ?>
                                    </div>
                                </div>
                                <?php foreach ($groupedVariants as $colorId => $colorData): ?>
                                    <div class="size-group" data-color-id="<?= $colorId ?>" style="<?= $colorId === array_key_first($groupedVariants) ? '' : 'display: none;' ?>">
                                        <p class="text-sm font-bold text-gray-800 mb-2">Select Size:</p>
                                        <div class="flex flex-wrap gap-2">
                                            <?php $firstSize = true;
                                            foreach ($colorData['sizes'] as $sizeData):
                                                $isOutOfStock = (!$sizeData['unlimited_stock'] && $sizeData['stock'] <= 0);
                                                $isInCart = in_array($sizeData['id'], $cartVariants);
                                            ?>
                                                <button class="size-btn <?= $isOutOfStock ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : '' ?> <?= (!$isOutOfStock && $firstSize) ? 'active-size' : '' ?> <?= $isInCart ? 'bg-green-50 border-green-500 text-green-700' : '' ?>"
                                                    data-variant-id="<?= $sizeData['id'] ?>"
                                                    data-color-id="<?= $colorId ?>"
                                                    data-size="<?= htmlspecialchars($sizeData['size']) ?>"
                                                    data-price="<?= $sizeData['price'] ?>"
                                                    data-mrp="<?= $sizeData['mrp_price'] ?>"
                                                    data-stock="<?= $sizeData['unlimited_stock'] ? 'Unlimited' : $sizeData['stock'] . ' ' . ($sizeData['unit_type'] ?? $unit_type) ?>"
                                                    data-image="<?= UPLOADS_URL . ($sizeData['image'] ?? $image) ?>"
                                                    data-in-cart="<?= $isInCart ? 1 : 0 ?>"
                                                    data-out-of-stock="<?= $isOutOfStock ? 1 : 0 ?>">
                                                    <?= htmlspecialchars($sizeData['size']) ?>
                                                    <?php if ($isOutOfStock): ?>
                                                        <span class="text-xs ml-1 text-red-500">(Sold Out)</span>
                                                    <?php endif; ?>
                                                </button>
                                            <?php $firstSize = false;
                                            endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php elseif ($basicVariants && $basicVariants->rowCount() > 0 && !$isAdvanced): ?>
                            <div>
                                <p class="text-sm font-bold text-gray-800 mb-2">Select Variant:</p>
                                <div class="flex flex-wrap gap-2">
                                    <button class="variant-btn <?= $initialVariantId === null ? 'ring-2' : '' ?>"
                                        data-variant-id="main"
                                        data-variant-image="<?= UPLOADS_URL . $image ?>"
                                        data-variant-name="<?= htmlspecialchars(!empty($variation) ? $variation : $name) ?>"
                                        data-price="<?= $price ?>"
                                        data-mrp="<?= $mrp_price ?>"
                                        data-stock="<?= $mainStockDisplay ?>"
                                        data-in-cart="<?= in_array('main', $cartVariants) ? 1 : 0 ?>">
                                        <?= htmlspecialchars(!empty($variation) ? $variation : $name) ?>
                                    </button>
                                    <?php $basicVariants->execute();
                                    while ($bv = $basicVariants->fetch()):
                                        $variantId = $bv['id'];
                                        $isInCart = in_array($variantId, $cartVariants);
                                        $variantLabel = $bv['variation'] ?? $bv['name'] ?? "Variant " . $variantId;
                                    ?>
                                        <button class="variant-btn <?= $initialVariantId == $variantId ? 'ring-2' : '' ?>"
                                            data-variant-id="<?= $variantId ?>"
                                            data-variant-image="<?= UPLOADS_URL . ($bv['image'] ?? $image) ?>"
                                            data-variant-name="<?= htmlspecialchars($variantLabel) ?>"
                                            data-price="<?= $bv['price'] ?>"
                                            data-mrp="<?= $bv['mrp_price'] ?>"
                                            data-stock="<?= $variantStocksDisplay[$variantId] ?? 'Out of stock' ?>"
                                            data-in-cart="<?= $isInCart ? 1 : 0 ?>">
                                            <?= htmlspecialchars($variantLabel) ?>
                                        </button>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- Add to Cart / View Cart -->
                        <div class="flex flex-wrap items-center gap-4 mt-6 pt-6 border-t border-gray-100">
                            <?php
                            $isOutOfStock = ($total_stocks <= 0 && $unlimited_stock != 1);
                            // If initially in cart, hide Add button and show View Cart
                            $showAddBtn = !$inCartInitial && !$isOutOfStock;
                            $showViewBtn = $inCartInitial;
                            ?>

                            <button id="addToCartBtn"
                                class="t18-add-btn flex items-center justify-center gap-2 <?= !$showAddBtn ? 'hidden' : '' ?>"
                                data-id="<?= $id ?>"
                                data-variant="<?= $initialVariantId ?? '' ?>"
                                <?= !$showAddBtn ? 'disabled' : '' ?>>
                                <i class="bi bi-cart-plus-fill text-lg"></i>
                                <?= $isOutOfStock ? 'Sold Out' : 'Add to Cart' ?>
                            </button>

                            <button id="soldOutBtn"
                                class="t18-add-btn flex items-center justify-center gap-2 bg-gray-300 cursor-not-allowed <?= ($isOutOfStock && !$inCartInitial) ? '' : 'hidden' ?>"
                                disabled>
                                <i class="bi bi-x-circle text-lg"></i> Sold Out
                            </button>

                            <a id="viewCartBtn" href="<?= $storeUrl ?>cart"
                                class="t18-view-btn flex items-center gap-2 px-8 py-3.5 rounded-full font-bold text-[15px] transition <?= !$showViewBtn ? 'hidden' : '' ?>">
                                <i class="bi bi-bag-check-fill"></i> View Cart
                            </a>

                            <?php if (isLoggedIn()):
                                $inWishlist = getData("id", "customer_wishlists", "customer_id='$customerId' AND product_id='$id' AND other=''") ? true : false;
                            ?>
                                <button id="wishlistBtn" data-id="<?= $id ?>" data-variant=""
                                    class="w-12 h-12 flex items-center justify-center rounded-full border border-gray-200 shadow-sm transition hover:scale-105 handleWishlist <?= $inWishlist ? 'bg-rose-50 border-rose-200 text-rose-500' : 'bg-white text-gray-400 hover:text-rose-500' ?>">
                                    <i class="bi bi-heart<?= $inWishlist ? '-fill' : '' ?> text-xl"></i>
                                </button>
                            <?php else: ?>
                                <a href="<?= $storeUrl ?>wishlists" class="w-12 h-12 flex items-center justify-center rounded-full border border-gray-200 shadow-sm transition hover:scale-105 bg-white text-gray-400 hover:text-rose-500">
                                    <i class="bi bi-heart text-xl"></i>
                                </a>
                            <?php endif; ?>

                            <button type="button" id="reportThisProduct"
                                class="w-12 h-12 flex items-center justify-center rounded-full border border-gray-200 shadow-sm bg-white text-gray-400 hover:text-red-500 transition hover:scale-105">
                                <i class="bi bi-flag text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <?php if (!empty($description)): ?>
                    <div class="t18-panel mt-10 p-6 sm:p-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Product Description</h3>
                        <div class="prose prose-sm sm:prose-base max-w-none text-gray-600 leading-relaxed">
                            <?= $description ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Customers Also Bought -->
                <?php
                $randomProducts = getRandomProducts(3);
                if ($randomProducts && count($randomProducts) > 0):
                ?>
                    <div class="mt-12 pt-8 border-t border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Customers Also Bought</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <?php foreach ($randomProducts as $rp):
                                $isRpoOut = (!$rp['total_stocks'] && !$rp['unlimited_stock']);
                            ?>
                                <a href="<?= $storeUrl ?>product/<?= $rp['slug'] ?>" class="t18-also-card group p-3">
                                    <div class="relative rounded-xl overflow-hidden mb-3 t18-also-media aspect-square">
                                        <img src="<?= UPLOADS_URL . $rp['image'] ?>" alt="<?= htmlspecialchars($rp['name']) ?>" class="w-full h-full object-cover <?= $isRpoOut ? 'opacity-50 grayscale' : 'group-hover:scale-105 transition-transform duration-500' ?>">
                                        <?php if ($isRpoOut): ?>
                                            <div class="absolute inset-0 flex items-center justify-center bg-white/40 backdrop-blur-sm">
                                                <span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">Sold Out</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-800 line-clamp-2 mb-1"><?= htmlspecialchars($rp['name']) ?></h4>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-[var(--t18-primary)]"><?= currencyToSymbol($storeCurrency) . number_format($rp['price'], 2) ?></span>
                                        <?php if ($rp['mrp_price'] && $rp['mrp_price'] > $rp['price']): ?>
                                            <span class="text-xs text-gray-400 line-through"><?= currencyToSymbol($storeCurrency) . number_format($rp['mrp_price'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Report Modal (Z-Index maxed out) -->
    <div id="reportProductContent" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center opacity-0 invisible transition-all duration-300 z-[9999] p-4">
        <div class="bg-white rounded-3xl w-full max-w-sm p-6 relative shadow-2xl transform scale-95 transition-transform duration-300">
            <button id="closeReportProductContent" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
            <div class="text-center mb-6">
                <div class="text-4xl mb-3">😢</div>
                <h2 class="text-xl font-bold text-gray-900">Report This Product</h2>
                <p class="text-gray-500 text-sm mt-1">Let us know what's wrong</p>
            </div>
            <form id="reportForm" class="flex flex-col gap-3" method="post">
                <input type="hidden" name="product_id" value="<?= $id ?>">
                <input type="text" name="name" placeholder="Your Name" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[var(--t18-primary)] focus:border-[var(--t18-primary)] outline-none text-sm" value="<?= customer('name') ?>">
                <input type="email" name="email" placeholder="Your Email" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[var(--t18-primary)] focus:border-[var(--t18-primary)] outline-none text-sm" value="<?= customer('email') ?>">
                <textarea name="reason" placeholder="Reason for reporting..." rows="3" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[var(--t18-primary)] focus:border-[var(--t18-primary)] outline-none text-sm resize-none"></textarea>
                <button type="submit" class="mt-2 px-6 py-3.5 bg-primary-500 hover:bg-[var(--t18-hover)] text-white font-bold rounded-xl shadow-lg transition-transform hover:-translate-y-0.5">Submit Report</button>
            </form>
        </div>
    </div>

    <!-- New Collections (Related Products) -->
    <section class="py-16 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900">New Collections</h2>
                <p class="text-gray-500 mt-2">Fresh arrivals designed for you</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 gap-y-8">
                <?php
                $productsStmt = getProducts();
                $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
                shuffle($products);
                $counter = 0;
                foreach ($products as $p) {
                    if ($counter >= 10) break;
                    echo getProductHtml($p["id"], "t18-card");
                    $counter++;
                }
                ?>
            </div>
        </div>
    </section>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

    <script>
        var currentUserId = <?= isset($customerId) && !empty($customerId) ? json_encode($customerId) : (isset($_SESSION['customer_id']) ? json_encode($_SESSION['customer_id']) : 'null') ?>;
        var productId = <?= $id ?>;
        var productName = <?= json_encode($name) ?>;
        var productPrice = <?= $price ?>;
        var storeCurrencySymbol = <?= json_encode(currencyToSymbol($storeCurrency)) ?>;

        // --- FIXED TOAST ---
        function showCustomToast(message, type) {
            const existingToasts = document.querySelectorAll('.custom-product-toast');
            existingToasts.forEach(toast => toast.remove());

            const toast = document.createElement('div');
            toast.className = 'custom-product-toast fixed top-6 right-6 z-[99999]';
            toast.innerHTML = `
            <div style="background-color: ${type === 'success' ? 'var(--t18-primary)' : '#ef4444'} !important; color: white !important;" class="px-5 py-4 rounded-xl shadow-2xl flex items-center gap-3 border border-white/20">
                <i class='bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'} text-xl'></i>
                <span class="font-bold text-sm">${message}</span>
            </div>
        `;
            document.body.appendChild(toast);
            const toastContent = toast.querySelector('div');
            toastContent.classList.add('toast-slide-in');
            setTimeout(() => {
                toastContent.classList.remove('toast-slide-in');
                toastContent.classList.add('toast-slide-out');
                setTimeout(() => {
                    if (toast.parentNode) toast.remove();
                }, 500);
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // --- GLOBAL BUTTONS ---
            const addBtn = document.getElementById('addToCartBtn');
            const viewBtn = document.getElementById('viewCartBtn');
            const soldOutBtn = document.getElementById('soldOutBtn');

            // --- REPORT MODAL LOGIC ---
            const reportModal = document.getElementById('reportProductContent');
            const reportForm = document.getElementById('reportForm');
            const reportBtn = document.getElementById('reportThisProduct');
            const closeReportBtn = document.getElementById('closeReportProductContent');

            if (reportBtn && reportModal) {
                reportBtn.addEventListener('click', function() {
                    reportModal.classList.remove('opacity-0', 'invisible');
                    reportModal.classList.add('opacity-100', 'visible');
                    const modalContent = reportModal.querySelector('.bg-white');
                    if (modalContent) {
                        modalContent.classList.remove('scale-95');
                        modalContent.classList.add('scale-100');
                    }
                });
            }

            function closeReportModal() {
                if (!reportModal) return;
                reportModal.classList.add('opacity-0', 'invisible');
                reportModal.classList.remove('opacity-100', 'visible');
                const modalContent = reportModal.querySelector('.bg-white');
                if (modalContent) {
                    modalContent.classList.remove('scale-100');
                    modalContent.classList.add('scale-95');
                }
            }

            if (closeReportBtn) closeReportBtn.addEventListener('click', closeReportModal);
            if (reportModal) {
                reportModal.addEventListener('click', function(e) {
                    if (e.target === this) closeReportModal();
                });
            }

            if (reportForm) {
                $("#reportForm").on("submit", function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const submitBtn = $(this).find('button[type="submit"]');
                    const origText = submitBtn.text();

                    submitBtn.prop('disabled', true).text('Sending...');

                    $.ajax({
                        url: "shop/ajax/report-product.php",
                        type: "POST",
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function(result) {
                            submitBtn.prop('disabled', false).text(origText);
                            try {
                                const response = JSON.parse(result);
                                if (response.success) {
                                    closeReportModal();
                                    $("#reportForm")[0].reset();
                                    showCustomToast("Report sent successfully!", 'success');
                                } else {
                                    showCustomToast(response.message || "Failed to send report", 'error');
                                }
                            } catch (err) {
                                console.error("Invalid JSON:", result);
                                showCustomToast("Server error. Please try again.", 'error');
                            }
                        },
                        error: function() {
                            submitBtn.prop('disabled', false).text(origText);
                            showCustomToast('Network error occurred', 'error');
                        }
                    });
                });
            }


            // --- VARIANTS & CART LOGIC ---
            const isAdvanced = <?= $isAdvanced ? 'true' : 'false' ?>;
            const comboEnabled = <?= $comboEnabled ? 'true' : 'false' ?>;

            if (comboEnabled) {
                // [COMBO LOGIC]
                const comboSlots = document.querySelectorAll('.combo-slot');
                const comboCounter = document.getElementById('comboSelectionCounter');
                const comboSelectionRequired = <?= (int)$comboSelectionCount ?>;
                const comboSelections = new Array(comboSelectionRequired).fill(null);
                const comboVariantData = <?= json_encode($groupedVariants, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

                function getSlotIndex(slot) {
                    return parseInt(slot.dataset.slot, 10) - 1;
                }

                function getSelectedVariantIds(exceptIndex = -1) {
                    return comboSelections.map((selection, index) => {
                        if (index === exceptIndex || !selection) return null;
                        return String(selection.variantId);
                    }).filter(Boolean);
                }

                function updateCounter() {
                    const selectedCount = comboSelections.filter(Boolean).length;
                    if (addBtn) {
                        const comboComplete = selectedCount === comboSelectionRequired;
                        addBtn.disabled = !comboComplete;
                        if (comboComplete) {
                            addBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
                            addBtn.classList.add('bg-primary-500');
                        } else {
                            addBtn.classList.remove('bg-primary-500');
                            addBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
                        }
                    }
                    if (!comboCounter) return;
                    comboCounter.textContent = `${selectedCount} / ${comboSelectionRequired}`;
                    comboCounter.classList.remove('bg-gray-100', 'text-gray-600', 'bg-pink-100', 'text-pink-700', 'bg-green-100', 'text-green-700');
                    if (selectedCount === comboSelectionRequired) {
                        comboCounter.classList.add('bg-green-100', 'text-green-700');
                    } else if (selectedCount > 0) {
                        comboCounter.classList.add('bg-pink-100', 'text-pink-700');
                    } else {
                        comboCounter.classList.add('bg-gray-100', 'text-gray-600');
                    }
                }

                function updateSlotStatus(slot, selected) {
                    const status = slot.querySelector('.combo-slot-status');
                    if (!status) return;
                    if (selected) {
                        status.textContent = '✓ Selected';
                        status.classList.remove('text-gray-400');
                        status.classList.add('text-green-600');
                    } else {
                        status.textContent = 'Not selected';
                        status.classList.remove('text-green-600');
                        status.classList.add('text-gray-400');
                    }
                }

                function showSelectedVariant(slot, data) {
                    const inputContainer = slot.querySelector('.combo-selection-inputs');
                    const selectedDisplay = slot.querySelector('.combo-selected-display');
                    if (!inputContainer || !selectedDisplay) {
                        updateSlotStatus(slot, !!data);
                        return;
                    }
                    if (data) {
                        inputContainer.classList.add('hidden');
                        selectedDisplay.classList.remove('hidden');
                        selectedDisplay.innerHTML = `
                        <div class="flex items-center justify-between gap-2 w-full bg-white border border-green-200 rounded-lg px-3 py-2 shadow-sm">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="flex items-center justify-center w-5 h-5 shrink-0 rounded-full bg-green-100 text-green-600 text-[10px]">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span class="text-xs sm:text-sm font-semibold text-gray-800 truncate">${data.label || ''}</span>
                            </div>
                            <button type="button" class="combo-remove-selection shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition" title="Remove selection">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    `;
                        const removeButton = selectedDisplay.querySelector('.combo-remove-selection');
                        if (removeButton) {
                            removeButton.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                const slotIndex = getSlotIndex(slot);
                                comboSelections[slotIndex] = null;
                                resetSlot(slotIndex, false);
                                inputContainer.classList.remove('hidden');
                                selectedDisplay.classList.add('hidden');
                                selectedDisplay.innerHTML = '';
                                updateCounter();
                            });
                        }
                    } else {
                        inputContainer.classList.remove('hidden');
                        selectedDisplay.classList.add('hidden');
                        selectedDisplay.innerHTML = '';
                    }
                    updateSlotStatus(slot, !!data);
                }

                function resetSlot(slotIndex, keepColor = false) {
                    const slot = comboSlots[slotIndex];
                    if (!slot) return;
                    const selectedDisplay = slot.querySelector('.combo-selected-display');
                    const inputContainer = slot.querySelector('.combo-selection-inputs');
                    if (selectedDisplay) {
                        selectedDisplay.classList.add('hidden');
                        selectedDisplay.innerHTML = '';
                    }
                    if (inputContainer) {
                        inputContainer.classList.remove('hidden');
                    }
                    const colorSelect = slot.querySelector('.combo-color-select');
                    const sizeSelect = slot.querySelector('.combo-size-select');
                    const variantSelect = slot.querySelector('.combo-variant-select');
                    comboSelections[slotIndex] = null;
                    if (colorSelect && !keepColor) colorSelect.value = '';
                    if (sizeSelect) {
                        sizeSelect.innerHTML = '<option value="">Select Size</option>';
                        sizeSelect.value = '';
                        sizeSelect.disabled = true;
                        sizeSelect.classList.remove('bg-white', 'text-gray-800', 'cursor-pointer');
                        sizeSelect.classList.add('bg-gray-100', 'text-gray-400');
                    }
                    if (variantSelect) variantSelect.value = '';
                    updateSlotStatus(slot, false);
                    updateCounter();
                }

                function showNextComboSlot(slotIndex) {
                    const nextIndex = slotIndex + 1;
                    if (nextIndex >= comboSlots.length) return;
                    comboSlots[nextIndex].classList.remove('hidden');
                }

                comboSlots.forEach((slot, slotIndex) => {
                    const colorSelect = slot.querySelector('.combo-color-select');
                    const sizeSelect = slot.querySelector('.combo-size-select');
                    if (colorSelect && sizeSelect) {
                        colorSelect.addEventListener('change', function() {
                            const colorId = this.value;
                            comboSelections[slotIndex] = null;
                            updateSlotStatus(slot, false);
                            sizeSelect.innerHTML = '<option value="">Select Size</option>';
                            sizeSelect.value = '';
                            sizeSelect.disabled = true;
                            if (!colorId) {
                                sizeSelect.classList.remove('bg-white', 'text-gray-800', 'cursor-pointer');
                                sizeSelect.classList.add('bg-gray-100', 'text-gray-400');
                                updateCounter();
                                return;
                            }
                            const colorData = comboVariantData[colorId];
                            if (!colorData || !Array.isArray(colorData.sizes)) {
                                updateCounter();
                                return;
                            }
                            let availableCount = 0;
                            colorData.sizes.forEach(sizeData => {
                                const variantId = String(sizeData.id || '');
                                const alreadySelected = getSelectedVariantIds(slotIndex).includes(variantId);
                                if (alreadySelected) return;
                                const stock = parseInt(sizeData.stock || 0, 10);
                                const unlimited = parseInt(sizeData.unlimited_stock || 0, 10) === 1;
                                const outOfStock = !unlimited && stock <= 0;
                                const option = document.createElement('option');
                                option.value = variantId;
                                option.dataset.variantId = variantId;
                                option.dataset.size = sizeData.size || '';
                                option.dataset.price = sizeData.price || 0;
                                option.dataset.mrp = sizeData.mrp_price || 0;
                                option.dataset.stock = unlimited ? 'Unlimited' : String(stock);
                                option.dataset.image = sizeData.image || '';
                                option.textContent = sizeData.size + (outOfStock ? ' — Sold Out' : '');
                                if (outOfStock) option.disabled = true;
                                else availableCount++;
                                sizeSelect.appendChild(option);
                            });

                            if (availableCount > 0) {
                                sizeSelect.disabled = false;
                                sizeSelect.classList.remove('bg-gray-100', 'text-gray-400');
                                sizeSelect.classList.add('bg-white', 'text-gray-800', 'cursor-pointer');
                            } else {
                                sizeSelect.innerHTML = '<option value="">No sizes available</option>';
                                sizeSelect.disabled = true;
                            }
                            updateCounter();
                        });

                        sizeSelect.addEventListener('change', function() {
                            const variantId = String(this.value || '');
                            if (!variantId) {
                                comboSelections[slotIndex] = null;
                                updateSlotStatus(slot, false);
                                updateCounter();
                                return;
                            }
                            const selectedOption = this.options[this.selectedIndex];
                            if (!selectedOption) return;
                            const colorOption = colorSelect.options[colorSelect.selectedIndex];
                            const colorName = colorOption?.dataset.colorName || colorOption?.textContent.trim() || '';
                            const size = selectedOption.dataset.size || '';
                            const data = {
                                variantId,
                                colorId: colorSelect.value,
                                colorName,
                                size,
                                price: parseFloat(selectedOption.dataset.price || 0),
                                mrp: parseFloat(selectedOption.dataset.mrp || 0),
                                stock: selectedOption.dataset.stock || '',
                                image: selectedOption.dataset.image || '',
                                label: `${colorName} • ${size}`
                            };
                            comboSelections[slotIndex] = data;
                            showSelectedVariant(slot, data);
                            updateSlotStatus(slot, true);
                            showNextComboSlot(slotIndex);
                            updateCounter();
                        });
                    }

                    const variantSelect = slot.querySelector('.combo-variant-select');
                    if (variantSelect) {
                        variantSelect.addEventListener('change', function() {
                            const variantId = String(this.value || '');
                            if (!variantId) {
                                comboSelections[slotIndex] = null;
                                showSelectedVariant(slot, null);
                                updateSlotStatus(slot, false);
                                updateCounter();
                                return;
                            }
                            const duplicateIds = getSelectedVariantIds(slotIndex);
                            if (duplicateIds.includes(variantId)) {
                                showCustomToast('This variant is already selected in another slot.', 'error');
                                this.value = '';
                                comboSelections[slotIndex] = null;
                                showSelectedVariant(slot, null);
                                updateSlotStatus(slot, false);
                                updateCounter();
                                return;
                            }
                            const option = this.options[this.selectedIndex];
                            const data = {
                                variantId,
                                price: parseFloat(option.dataset.price || 0),
                                mrp: parseFloat(option.dataset.mrp || 0),
                                stock: option.dataset.stock || '',
                                image: option.dataset.image || '',
                                label: option.dataset.variantName || option.textContent.trim()
                            };
                            comboSelections[slotIndex] = data;
                            showSelectedVariant(slot, data);
                            updateSlotStatus(slot, true);
                            showNextComboSlot(slotIndex);
                            updateCounter();
                        });
                    }
                });

                updateCounter();

                if (addBtn) {
                    addBtn.addEventListener('click', function() {
                        if (this.disabled) return;
                        const selectedComboVariants = comboSelections.filter(Boolean);
                        if (selectedComboVariants.length !== comboSelectionRequired) {
                            showCustomToast(`Please select all ${comboSelectionRequired} variants.`, 'error');
                            return;
                        }
                        const comboData = selectedComboVariants.map(item => ({
                            variantId: item.variantId,
                            colorId: item.colorId || '',
                            colorName: item.colorName || '',
                            size: item.size || '',
                            label: item.label || '',
                            price: item.price || 0,
                            mrp: item.mrp || 0,
                            image: item.image || ''
                        }));
                        const product_id = this.dataset.id;
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                        this.disabled = true;
                        $.ajax({
                            url: "shop/ajax/add-to-cart.php",
                            type: "POST",
                            data: {
                                product_id: product_id,
                                combo_variants: JSON.stringify(comboData)
                            },
                            success: function(result) {
                                addBtn.innerHTML = originalText;
                                addBtn.disabled = false;
                                try {
                                    const response = typeof result === 'string' ? JSON.parse(result) : result;
                                    if (response && response.success) {
                                        addBtn.classList.add('hidden');
                                        if (viewBtn) viewBtn.classList.remove('hidden');
                                        showCustomToast(response.message || 'Combo added to cart successfully!', 'success');
                                        if (typeof getProductCountAndPrice === 'function') getProductCountAndPrice();
                                        if (typeof getCartData === 'function') getCartData();
                                    } else {
                                        showCustomToast(response?.message || 'Failed to add combo to cart.', 'error');
                                    }
                                } catch (e) {
                                    console.error('Combo response parse error:', e, result);
                                    showCustomToast('Error adding combo to cart.', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Combo AJAX Error:', error, xhr.responseText);
                                addBtn.innerHTML = originalText;
                                addBtn.disabled = false;
                                showCustomToast('Network error occurred.', 'error');
                            }
                        });
                    });
                }

            } else if (isAdvanced) {
                // [ADVANCED VARIANTS]
                const colorButtons = document.querySelectorAll('.color-btn');
                const sizeGroups = document.querySelectorAll('.size-group');
                const sizeButtons = document.querySelectorAll('.size-btn');
                const mainImage = document.getElementById('mainProductImage');
                const priceEl = document.querySelector('.productPrice');
                const mrpEl = document.querySelector('.productMrp');
                const stockEl = document.getElementById('stockDisplay');
                const variantNameEl = document.getElementById('variantName');

                let selectedVariantId = null;
                let selectedVariantData = null;

                colorButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        colorButtons.forEach(b => b.classList.remove('active-color', 'ring-2', 'ring-[var(--t18-primary)]', 'ring-offset-2'));
                        this.classList.add('active-color', 'ring-2', 'ring-[var(--t18-primary)]', 'ring-offset-2');

                        const colorId = this.dataset.colorId;
                        sizeGroups.forEach(group => {
                            group.style.display = group.dataset.colorId === colorId ? 'block' : 'none';
                        });

                        selectedVariantId = null;
                        selectedVariantData = null;

                        const currentSizeGroup = document.querySelector(`.size-group[data-color-id="${colorId}"]`);
                        const firstAvailableSize = currentSizeGroup?.querySelector('.size-btn:not([data-out-of-stock="1"])');
                        const firstSize = firstAvailableSize || currentSizeGroup?.querySelector('.size-btn');
                        if (firstSize) {
                            firstSize.click();
                        } else {
                            // No variants exist for this colour: reset stock and CTA state.
                            if (stockEl) {
                                stockEl.textContent = 'Out of stock';
                                stockEl.classList.remove('bg-green-50', 'text-green-600');
                                stockEl.classList.add('bg-red-50', 'text-red-600');
                            }
                            addBtn?.classList.add('hidden');
                            viewBtn?.classList.add('hidden');
                            soldOutBtn?.classList.remove('hidden');
                        }
                    });
                });

                sizeButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (this.disabled) return;
                        const currentGroup = this.closest('.size-group');
                        if (currentGroup) {
                            currentGroup.querySelectorAll('.size-btn').forEach(b => {
                                b.classList.remove('active-size');
                                b.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                            });
                        }
                        this.classList.add('active-size');
                        this.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');

                        selectedVariantId = this.dataset.variantId;
                        selectedVariantData = {
                            id: this.dataset.variantId,
                            colorId: this.dataset.colorId,
                            size: this.dataset.size,
                            price: parseFloat(this.dataset.price),
                            mrp: parseFloat(this.dataset.mrp) || 0,
                            stock: this.dataset.stock,
                            image: this.dataset.image,
                            inCart: this.dataset.inCart == 1
                        };

                        if (selectedVariantData.image) mainImage.src = selectedVariantData.image;
                        if (priceEl) priceEl.textContent = storeCurrencySymbol + selectedVariantData.price.toLocaleString();
                        if (mrpEl) mrpEl.textContent = (selectedVariantData.mrp && selectedVariantData.mrp > selectedVariantData.price) ? storeCurrencySymbol + selectedVariantData.mrp.toLocaleString() : '';

                        if (stockEl) {
                            const stockText = String(selectedVariantData.stock || '0').trim();
                            const stockNumber = stockText.toLowerCase().includes('unlimited') ?
                                Infinity :
                                (parseFloat(stockText.replace(/[^0-9.\-]/g, '')) || 0);
                            const isOutOfStock = stockNumber <= 0;

                            stockEl.textContent = isOutOfStock ? 'Out of stock' : stockText;
                            if (isOutOfStock) {
                                stockEl.classList.remove('bg-green-50', 'text-green-600');
                                stockEl.classList.add('bg-red-50', 'text-red-600');
                            } else {
                                stockEl.classList.remove('bg-red-50', 'text-red-600');
                                stockEl.classList.add('bg-green-50', 'text-green-600');
                            }
                        }

                        if (variantNameEl) {
                            const colorName = document.querySelector(`.color-btn[data-color-id="${selectedVariantData.colorId}"]`)?.dataset.colorName || '';
                            variantNameEl.textContent = `${colorName} - ${selectedVariantData.size}`;
                        }
                        updateAddToCartButton(selectedVariantData);
                    });
                });

                function updateAddToCartButton(variantData) {
                    if (!addBtn || !viewBtn || !soldOutBtn) return;

                    const stockText = String(variantData.stock || '0').trim();
                    const stockNumber = stockText.toLowerCase().includes('unlimited') ?
                        Infinity :
                        (parseFloat(stockText.replace(/[^0-9.\-]/g, '')) || 0);
                    const isOutOfStock = stockNumber <= 0;

                    // Always reset all three controls before showing the correct one.
                    addBtn.classList.add('hidden');
                    viewBtn.classList.add('hidden');
                    soldOutBtn.classList.add('hidden');
                    addBtn.disabled = false;

                    if (variantData.inCart) {
                        viewBtn.classList.remove('hidden');
                    } else if (isOutOfStock) {
                        soldOutBtn.classList.remove('hidden');
                    } else {
                        addBtn.innerHTML = '<i class="bi bi-cart-plus-fill text-lg"></i> Add to Cart';
                        addBtn.classList.remove('hidden');
                    }

                    addBtn.dataset.variant = '';
                    addBtn.dataset.advancedVariant = variantData.id;
                }

                if (addBtn) {
                    addBtn.addEventListener('click', function() {
                        if (this.disabled || !selectedVariantId) return;
                        const product_id = this.dataset.id;
                        const advanced_variant = selectedVariantId;

                        if (window.utmTracker && currentUserId) {
                            const colorName = document.querySelector(`.color-btn[data-color-id="${selectedVariantData?.colorId}"]`)?.dataset.colorName || '';
                            const size = selectedVariantData?.size || '';
                            window.utmTracker.trackAddToCart({
                                productId: product_id,
                                variantId: advanced_variant,
                                variantType: 'advanced',
                                variantLabel: `${colorName} - ${size}`,
                                productName: productName,
                                userId: currentUserId
                            });
                        }

                        const originalText = this.innerHTML;
                        const originalState = this.disabled;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                        this.disabled = true;

                        $.ajax({
                            url: "shop/ajax/add-to-cart.php",
                            type: "POST",
                            data: {
                                product_id: product_id,
                                variant: '',
                                advanced_variant: advanced_variant
                            },
                            success: function(result) {
                                addBtn.innerHTML = originalText;
                                addBtn.disabled = originalState;
                                try {
                                    const response = result && JSON.parse(result);
                                    if (response && response.success) {
                                        addBtn.classList.add('hidden');
                                        viewBtn.classList.remove('hidden');
                                        sizeButtons.forEach(btn => {
                                            if (btn.dataset.variantId === advanced_variant) {
                                                btn.dataset.inCart = '1';
                                            }
                                        });
                                        showCustomToast(response.message || "Product added to cart successfully!", 'success');
                                        if (typeof getProductCountAndPrice === 'function') getProductCountAndPrice();
                                        if (typeof getCartData === 'function') getCartData();
                                    } else {
                                        const errorMessage = response?.message || "Failed to add product to cart.";
                                        if (!(errorMessage.includes('already') || errorMessage.includes('duplicate') || errorMessage.includes('exist'))) {
                                            showCustomToast(errorMessage, 'error');
                                        } else {
                                            if (typeof getProductCountAndPrice === 'function') getProductCountAndPrice();
                                        }
                                    }
                                } catch (e) {
                                    console.error('Error parsing response:', e);
                                    showCustomToast('Error adding to cart', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                addBtn.innerHTML = originalText;
                                addBtn.disabled = originalState;
                                showCustomToast('Network error occurred', 'error');
                            }
                        });
                    });
                }

                const firstColor = document.querySelector('.color-btn');
                if (firstColor) firstColor.click();

                const urlParams = new URLSearchParams(window.location.search);
                const urlVariant = urlParams.get('variation');
                if (urlVariant) {
                    sizeButtons.forEach(btn => {
                        if (btn.dataset.variantId === urlVariant && !btn.disabled) btn.click();
                    });
                }

            } else {
                // [BASIC VARIANTS / SIMPLE PRODUCT]
                const variantButtons = document.querySelectorAll('.variant-btn');
                const mainImage = document.getElementById('mainProductImage');
                const priceEl = document.querySelector('.productPrice');
                const mrpEl = document.querySelector('.productMrp');
                const stockEl = document.getElementById('stockDisplay');
                const variantNameEl = document.getElementById('variantName');
                const productKey = "selectedVariant_<?= $id ?>";
                let currentVariantId = '';
                let currentVariantType = '';
                let currentVariantLabel = '';

                function selectVariant(btn) {
                    variantButtons.forEach(b => b.classList.remove('ring-2', 'ring-[var(--t18-primary)]'));
                    btn.classList.add('ring-2', 'ring-[var(--t18-primary)]');

                    const variantId = btn.dataset.variantId;
                    const variantImage = btn.dataset.variantImage;
                    const variantPrice = parseFloat(btn.dataset.price);
                    const variantMrp = parseFloat(btn.dataset.mrp);
                    const stockText = btn.dataset.stock;
                    const inCart = btn.dataset.inCart == 1;
                    const variantName = btn.dataset.variantName;

                    currentVariantId = variantId;
                    currentVariantType = variantId === 'main' ? 'main' : 'basic';
                    currentVariantLabel = variantName || btn.textContent.trim() || 'Variant';

                    if (variantImage) mainImage.src = variantImage;
                    if (priceEl) priceEl.textContent = storeCurrencySymbol + variantPrice.toLocaleString();
                    if (mrpEl) mrpEl.textContent = (variantMrp && variantMrp > variantPrice) ? storeCurrencySymbol + variantMrp.toLocaleString() : '';

                    if (stockEl) {
                        stockEl.textContent = stockText;
                        let stockNumber = parseInt(stockText);
                        if (stockText.toLowerCase().includes('unlimited')) stockNumber = Infinity;
                        if (stockNumber > 0) {
                            stockEl.classList.remove('bg-red-50', 'text-red-600');
                            stockEl.classList.add('bg-green-50', 'text-green-600');
                        } else {
                            stockEl.classList.remove('bg-green-50', 'text-green-600');
                            stockEl.classList.add('bg-red-50', 'text-red-600');
                        }
                    }
                    if (variantNameEl) variantNameEl.textContent = variantName;

                    if (addBtn && viewBtn && soldOutBtn) {
                        addBtn.dataset.variant = currentVariantType === 'main' ? '' : currentVariantId;
                        addBtn.dataset.advancedVariant = '';
                        let stockNumber = parseInt(stockText);
                        if (stockText.toLowerCase().includes('unlimited')) stockNumber = Infinity;
                        const isOutOfStock = stockNumber < 1;

                        addBtn.classList.add('hidden');
                        viewBtn.classList.add('hidden');
                        soldOutBtn.classList.add('hidden');

                        if (inCart) {
                            viewBtn.classList.remove('hidden');
                        } else if (isOutOfStock) {
                            soldOutBtn.classList.remove('hidden');
                        } else {
                            addBtn.classList.remove('hidden');
                            addBtn.disabled = false;
                        }
                    }
                    localStorage.setItem(productKey, btn.dataset.variantId);
                }

                if (addBtn) {
                    addBtn.addEventListener('click', function() {
                        if (this.disabled) return;
                        const product_id = this.dataset.id;
                        let variant = currentVariantType === 'main' ? '' : currentVariantId;

                        if (window.utmTracker && currentUserId) {
                            window.utmTracker.trackAddToCart({
                                productId: product_id,
                                variantId: currentVariantId,
                                variantType: currentVariantType,
                                variantLabel: currentVariantLabel,
                                productName: productName,
                                userId: currentUserId
                            });
                        }

                        const originalText = this.innerHTML;
                        const originalState = this.disabled;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                        this.disabled = true;

                        $.ajax({
                            url: "shop/ajax/add-to-cart.php",
                            type: "POST",
                            data: {
                                product_id: product_id,
                                variant: variant,
                                advanced_variant: ''
                            },
                            success: function(result) {
                                addBtn.innerHTML = originalText;
                                addBtn.disabled = originalState;
                                try {
                                    const response = result && JSON.parse(result);
                                    if (response && response.success) {
                                        addBtn.classList.add('hidden');
                                        viewBtn.classList.remove('hidden');
                                        variantButtons.forEach(btn => {
                                            if (btn.dataset.variantId === currentVariantId) btn.dataset.inCart = '1';
                                        });
                                        showCustomToast(response.message || "Product added to cart successfully!", 'success');
                                        if (typeof getProductCountAndPrice === 'function') getProductCountAndPrice();
                                        if (typeof getCartData === 'function') getCartData();
                                    } else {
                                        const errorMessage = response?.message || "Failed to add product to cart.";
                                        if (!(errorMessage.includes('already') || errorMessage.includes('duplicate') || errorMessage.includes('exist'))) {
                                            showCustomToast(errorMessage, 'error');
                                        } else {
                                            if (typeof getProductCountAndPrice === 'function') getProductCountAndPrice();
                                        }
                                    }
                                } catch (e) {
                                    console.error('Error parsing response:', e);
                                    showCustomToast('Error adding to cart', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                addBtn.innerHTML = originalText;
                                addBtn.disabled = originalState;
                                showCustomToast('Network error occurred', 'error');
                            }
                        });
                    });
                }

                const urlParams = new URLSearchParams(window.location.search);
                const urlVariant = urlParams.get('variation');
                let savedVariantId = (urlVariant && urlVariant !== 'main') ? urlVariant : localStorage.getItem(productKey);

                if (savedVariantId) {
                    let found = false;
                    variantButtons.forEach(btn => {
                        if (btn.dataset.variantId === savedVariantId) {
                            selectVariant(btn);
                            found = true;
                        }
                    });
                    if (!found && variantButtons.length > 0) selectVariant(variantButtons[0]);
                } else {
                    if (variantButtons.length > 0) selectVariant(variantButtons[0]);
                }
                variantButtons.forEach(btn => btn.addEventListener('click', () => selectVariant(btn)));
            }

            // --- RATING LOGIC ---
            const stars = $('#productRatingUI .star');
            const ratingInfo = $('#ratingInfo');
            stars.on('click', function() {
                const rating = $(this).data('value');
                stars.each(function(idx) {
                    $(this).toggleClass('text-yellow-400', idx < rating);
                    $(this).toggleClass('text-gray-300', idx >= rating);
                });

                $.post("shop/ajax/rate-product.php", {
                    product_id: <?= $id ?>,
                    rating: rating
                }, function(res) {
                    if (res.success) {
                        ratingInfo.html(` ${res.avg_rating} / 5 <span class="text-gray-400 font-normal">(${res.total_ratings} reviews)</span>`);
                        showCustomToast("Rating submitted successfully!", 'success');
                    } else {
                        showCustomToast(res.message || "Failed to submit rating", 'error');
                    }
                }, 'json');
            });
        });
    </script>

</body>

</html>