<?php include_once __DIR__ . "/includes/files_includes.php";
// ==================== DB COLORS ====================
$primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f';
$hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899';

// ==================== CATEGORY DETECTION ====================
if (isset($_GET['slug']) && getData("id", "seller_product_categories", "slug = '{$_GET['slug']}' AND seller_id = '$sellerId'")) {
    $slug = $_GET['slug'];
    $id = getData("id", "seller_product_categories", "slug = '{$_GET['slug']}' AND seller_id = '$sellerId'");
    $name = getData("name", "seller_product_categories", "slug = '{$_GET['slug']}' AND seller_id = '$sellerId'");
    $icon = getData("icon", "seller_product_categories", "slug = '{$_GET['slug']}' AND seller_id = '$sellerId'");
    $db->prepare("UPDATE seller_product_categories SET visitors = visitors+1 WHERE id = :id")->execute(['id' => $id]);
} else {
    redirect($storeUrl);
}

// ==================== GET SIZES — only sizes IN THIS CATEGORY ====================
global $db, $sellerId, $storeId;
$sizes = [];

if (!empty($sellerId) && !empty($storeId) && !empty($id)) {
    $sql = "
        SELECT DISTINCT v.size
        FROM seller_product_advanced_variants v
        INNER JOIN seller_products p 
            ON p.product_id = v.product_id
        WHERE p.seller_id = :seller_id
          AND p.store_id  = :store_id
          AND p.status = 1
          AND p.category = :category_id
          AND (p.visibility = 'publish' 
               OR (p.visibility = 'schedule' AND p.schedule_date <= NOW()))
          AND v.size IS NOT NULL
          AND v.size != ''
        ORDER BY v.size
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':seller_id'   => $sellerId,
        ':store_id'    => $storeId,
        ':category_id' => $id
    ]);
    $sizes = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

$hasSizeFilter = !empty($sizes);

// ==================== GET MAX PRICE ====================
$maxPrice = 10000;
$maxPriceSql = "
    SELECT MAX(price) as max_price 
    FROM seller_products 
    WHERE seller_id = :seller_id 
      AND store_id = :store_id 
      AND status = 1
      AND (visibility = 'publish' 
           OR (visibility = 'schedule' AND schedule_date <= NOW()))
";
$maxPriceStmt = $db->prepare($maxPriceSql);
$maxPriceStmt->execute([':seller_id' => $sellerId, ':store_id' => $storeId]);
$maxPriceResult = $maxPriceStmt->fetch(PDO::FETCH_ASSOC);
if ($maxPriceResult && $maxPriceResult['max_price'] > 0) {
    $maxPrice = ceil($maxPriceResult['max_price'] / 100) * 100;
}

// ==================== MOBILE DETECTION ====================
$isMobile = false;
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (
    stripos($userAgent, 'Mobile') !== false ||
    stripos($userAgent, 'Android') !== false ||
    stripos($userAgent, 'Tablet') !== false
) {
    $isMobile = true;
}

// ==================== CATEGORIES ====================
$categories = getCategories([
    "add_to_menu" => 1,
    "parent_category" => " ",
    "limit" => $isMobile ? 15 : 7
]);

// ==================== PRODUCT COUNT ====================
$countStmt = getProducts(["count" => true, "category" => $id]);
$totalProductsCount = $countStmt ? $countStmt->fetchColumn() : 0;

// ==================== PRICE PRESETS ====================
$presets = [];
if ($maxPrice >= 100) {
    $presets[] = [0, round($maxPrice * 0.05)];
    $presets[] = [round($maxPrice * 0.05), round($maxPrice * 0.10)];
    $presets[] = [round($maxPrice * 0.10), round($maxPrice * 0.25)];
    $presets[] = [round($maxPrice * 0.25), round($maxPrice * 0.50)];
    $presets[] = [round($maxPrice * 0.50), $maxPrice];
} else {
    $presets[] = [0, $maxPrice];
}

// ==================== SUB-CATEGORIES ====================
$currentParent = getData("parent_category", "seller_product_categories", "id = '$id'");
$parentId = ($currentParent > 0) ? $currentParent : $id;
$hasSubCategories = getData("id", "seller_product_categories", "parent_category = '$parentId' AND seller_id = '$sellerId'");

if ($hasSubCategories) {
    $sub_categories = getCategories(["parent_category" => $parentId]);
}
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

        body {
            background: radial-gradient(circle at 10% 20%, color-mix(in srgb, var(--t18-primary) 5%, #fcfcfc) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, color-mix(in srgb, var(--t18-hover) 5%, #fcfcfc) 0%, transparent 40%),
                #fafafa;
            font-family: 'Inter', sans-serif;
        }

        /* ==================== 3D FLOATING CATEGORY STRIP ==================== */
        .t18-cat-strip {
            position: relative;
            padding: 20px clamp(16px, 4vw, 58px) 25px;
            /* Reduced padding */
            background: transparent;
            overflow: hidden;
        }

        .t18-cat-track {
            display: flex;
            gap: 12px;
            /* Reduced gap */
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 5px 4px 10px;
            /* Reduced padding */
            scrollbar-width: none;
        }

        .t18-cat-track::-webkit-scrollbar {
            display: none;
        }

        .t18-cat-card {
            position: relative;
            flex: 0 0 clamp(90px, 12vw, 120px);
            /* Reduced card size */
            display: flex;
            flex-direction: column;
            text-decoration: none;
            transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
            transform-style: preserve-3d;
        }

        .t18-cat-card:hover {
            transform: translateY(-4px);
        }

        .t18-cat-img-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 1/1;
            /* Square instead of tall */
            border-radius: 16px;
            /* Smaller radius */
            overflow: hidden;
            background: #fff;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 15%, #e5e7eb);
            box-shadow: 0 6px 15px -8px color-mix(in srgb, var(--t18-primary) 22%, transparent);
            transition: box-shadow 0.4s ease;
        }

        .t18-cat-card:hover .t18-cat-img-wrap {
            box-shadow: 0 12px 25px -10px color-mix(in srgb, var(--t18-primary) 40%, transparent);
        }

        .t18-cat-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .t18-cat-card:hover .t18-cat-img-wrap img {
            transform: scale(1.08);
        }

        .t18-cat-letter {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            font-size: 28px;
            /* Smaller font */
            font-weight: 900;
            color: var(--t18-primary);
            background: color-mix(in srgb, var(--t18-primary) 6%, #f8f8f8);
        }

        /* Floating number badge */
        .t18-cat-num {
            position: absolute;
            top: 6px;
            left: 6px;
            z-index: 3;
            background: #fff;
            color: var(--t18-primary);
            width: 22px;
            /* Smaller badge */
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 9px;
            font-weight: 800;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .t18-cat-label {
            margin-top: 8px;
            /* Reduced margin */
            text-align: center;
            font-size: 11px;
            /* Smaller font */
            font-weight: 700;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 2px;
        }

        .t18-cat-card.is-current .t18-cat-img-wrap {
            border: 2px solid var(--t18-primary);
        }

        .t18-cat-card.is-current .t18-cat-label {
            color: var(--t18-primary);
        }

        /* Sub-category rail */
        .t18-sub-rail-wrap {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 5px 4px;
            scrollbar-width: none;
        }

        .t18-sub-rail-wrap::-webkit-scrollbar {
            display: none;
        }

        .t18-sub-pill {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px 6px 6px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            text-decoration: none;
            color: #334155;
            font-size: 11px;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        .t18-sub-pill:hover {
            border-color: var(--t18-primary);
            color: var(--t18-primary);
            transform: translateY(-2px);
        }

        .t18-sub-pill.is-active {
            background: color-mix(in srgb, var(--t18-primary) 10%, #fff);
            border-color: var(--t18-primary);
            color: var(--t18-primary);
        }

        .t18-sub-thumb {
            width: 24px;
            /* Smaller thumb */
            height: 24px;
            border-radius: 50%;
            overflow: hidden;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 10px;
            color: var(--t18-primary);
        }

        .t18-sub-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Filter Bar */
        .t18-filter-bar {
            position: relative;
            z-index: 10;
            background: #fff;
            border-radius: 16px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 15%, #e5e7eb);
            box-shadow: 0 6px 20px -10px color-mix(in srgb, var(--t18-primary) 20%, transparent);
            padding: 10px 16px;
            margin-bottom: 20px;
        }

        .t18-filter-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            background: #fff;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            transition: all 0.2s;
        }

        .t18-filter-btn:hover,
        .t18-filter-btn.active {
            border-color: var(--t18-primary);
            color: var(--t18-primary);
            background: color-mix(in srgb, var(--t18-primary) 5%, #fff);
        }

        .t18-filter-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            z-index: 50;
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
            padding: 16px;
            width: 320px;
        }

        /* Slider */
        .t18-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            width: 100%;
            height: 4px;
            background: transparent;
            outline: none;
            margin: 0;
            padding: 0;
            pointer-events: auto;
            cursor: pointer;
            z-index: 3;
        }

        .t18-slider-thumb::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--t18-primary);
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        /* Product Grid Overrides */
        #content {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        @media (min-width: 640px) {
            #content {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            #content {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 16px;
            }
        }

        @media (min-width: 1280px) {
            #content {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .t18-cat-card {
                flex: 0 0 80px;
            }

            .t18-cat-label {
                font-size: 10px;
                margin-top: 6px;
            }

            .t18-cat-num {
                width: 18px;
                height: 18px;
                font-size: 8px;
                top: 4px;
                left: 4px;
            }

            .t18-cat-track {
                gap: 10px;
            }
        }
    </style>
</head>

<body class="font-sans min-h-screen">

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <!-- Hidden category id for JS -->
    <span id="category" style="display:none;"><?= $id ?></span>

    <!-- ==================== 3D CATEGORY STRIP ==================== -->
    <?php if ($categories && $categories->rowCount()): ?>
        <section class="t18-cat-strip">
            <div class="max-w-[1500px] mx-auto">
                <div class="flex items-end justify-between mb-3 px-2">
                    <div>
                        <span class="text-[9px] font-extrabold uppercase tracking-[2px] text-[var(--t18-primary)] mb-1 block">Explore</span>
                        <h2 class="text-lg font-black text-gray-900 m-0 leading-none">Shop by Category</h2>
                    </div>
                    <div class="flex gap-2">
                        <button id="prevBtn" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:text-[var(--t18-primary)] hover:border-[var(--t18-primary)] transition"><i class="bi bi-chevron-left text-xs"></i></button>
                        <button id="nextBtn" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:text-[var(--t18-primary)] hover:border-[var(--t18-primary)] transition"><i class="bi bi-chevron-right text-xs"></i></button>
                    </div>
                </div>

                <div id="categoryCarousel" class="t18-cat-track">
                    <?php $i = 1;
                    foreach ($categories as $category):
                        $catIcon = !empty($category['icon']) ? UPLOADS_URL . $category['icon'] : null;
                        $firstLetter = strtoupper(substr($category['name'], 0, 1));
                        $isCurrentCategory = (($category['slug'] ?? '') === ($slug ?? ''));
                    ?>
                        <a href="<?= htmlspecialchars($storeUrl . 'category/' . $category['slug']) ?>" class="t18-cat-card <?= $isCurrentCategory ? 'is-current' : '' ?>">
                            <div class="t18-cat-img-wrap">
                                <span class="t18-cat-num"><?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?></span>
                                <?php if ($catIcon): ?>
                                    <img src="<?= htmlspecialchars($catIcon) ?>" alt="<?= htmlspecialchars($category['name']) ?>" loading="lazy">
                                <?php else: ?>
                                    <span class="t18-cat-letter"><?= htmlspecialchars($firstLetter) ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="t18-cat-label"><?= htmlspecialchars($category['name']) ?></span>
                        </a>
                    <?php $i++;
                    endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>


    <!-- ==================== SUB-CATEGORY RAIL ==================== -->
    <?php if (!empty($hasSubCategories) && !empty($sub_categories)): ?>
        <section class="max-w-[1500px] mx-auto px-4 sm:px-6 mb-6">
            <div class="flex items-center gap-2 mb-2 px-2">
                <span class="text-[9px] font-extrabold uppercase tracking-[2px] text-gray-400">In this collection</span>
                <span class="text-xs font-bold text-gray-800">Subcategories</span>
            </div>
            <div id="subCategoryCarousel" class="t18-sub-rail-wrap">
                <?php foreach ($sub_categories as $sub):
                    $subIcon = !empty($sub['icon']) ? UPLOADS_URL . $sub['icon'] : null;
                    $firstLetter = strtoupper(substr($sub['name'], 0, 1));
                    $isActive = ($sub['id'] == $id);
                ?>
                    <a href="<?= htmlspecialchars($storeUrl . 'category/' . $sub['slug']) ?>" class="t18-sub-pill <?= $isActive ? 'is-active' : '' ?>">
                        <span class="t18-sub-thumb">
                            <?php if ($subIcon): ?>
                                <img src="<?= htmlspecialchars($subIcon) ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <?= htmlspecialchars($firstLetter) ?>
                            <?php endif; ?>
                        </span>
                        <?= htmlspecialchars($sub['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>


    <!-- ==================== PAGE HEADER ==================== -->
    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 text-center mb-6">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 mb-1">
            <?= htmlspecialchars($name) ?>
        </h1>
        <p class="text-xs font-semibold text-gray-500 tracking-wide uppercase">
            <span id="productCount"><?= $totalProductsCount ?> products</span>
        </p>
    </div>


    <!-- ==================== FILTER BAR ==================== -->
    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <div class="t18-filter-bar flex flex-col sm:flex-row items-center justify-between gap-4">

            <!-- Left: Filter buttons -->
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">

                <!-- Price Range -->
                <div class="relative">
                    <button id="priceDropdownBtn" class="t18-filter-btn">
                        <i class="bi bi-sliders2 text-xs"></i>
                        <span>Price</span>
                        <span id="priceActiveBadge" class="hidden ml-1 text-white text-[8px] px-2 py-0.5 rounded-full bg-[var(--t18-primary)]">Active</span>
                    </button>

                    <div id="priceDropdown" class="t18-filter-dropdown hidden">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-bold uppercase text-gray-600">Price Range</span>
                            <button id="resetPriceFilter" class="text-gray-400 hover:text-[var(--t18-primary)] text-[10px] flex items-center gap-1">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>
                        <div class="flex items-center justify-between mb-3 bg-gray-50 border border-gray-100 p-2 rounded-xl">
                            <div class="flex items-center gap-1">
                                <span class="text-[9px] uppercase text-gray-400 font-bold">Min</span>
                                <span id="minPriceDisplay" class="text-sm font-black text-[var(--t18-primary)]">₹0</span>
                            </div>
                            <span class="text-gray-300">—</span>
                            <div class="flex items-center gap-1">
                                <span id="maxPriceDisplay" class="text-sm font-black text-[var(--t18-primary)]">₹<?= number_format($maxPrice) ?></span>
                                <span class="text-[9px] uppercase text-gray-400 font-bold">Max</span>
                            </div>
                        </div>
                        <div class="mb-3 relative pt-2" style="height: 24px;">
                            <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-200 rounded-full -translate-y-1/2">
                                <div id="sliderRangeFill" class="absolute h-full rounded-full bg-[var(--t18-primary)]" style="left: 0%; right: 0%;"></div>
                            </div>
                            <input type="range" id="minPriceSlider" class="t18-slider-thumb" min="0" max="<?= $maxPrice ?>" value="0" step="100" />
                            <input type="range" id="maxPriceSlider" class="t18-slider-thumb" min="0" max="<?= $maxPrice ?>" value="<?= $maxPrice ?>" step="100" />
                        </div>
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            <?php foreach ($presets as $preset):
                                $label = '₹' . number_format($preset[0]) . ' - ₹' . number_format($preset[1]);
                            ?>
                                <button class="price-preset text-[9px] font-bold px-2 py-1 border border-gray-200 rounded-md hover:border-[var(--t18-primary)] hover:text-[var(--t18-primary)] transition"
                                    data-min="<?= $preset[0] ?>" data-max="<?= $preset[1] ?>"><?= $label ?></button>
                            <?php endforeach; ?>
                        </div>
                        <button id="applyPriceFilter" class="w-full text-white text-[10px] font-bold py-2 rounded-lg bg-[var(--t18-primary)] hover:bg-[var(--t18-hover)] transition">
                            Apply
                        </button>
                    </div>
                </div>

                <!-- Size -->
                <?php if ($hasSizeFilter): ?>
                    <div class="relative">
                        <button id="sizeDropdownBtn" class="t18-filter-btn">
                            <i class="bi bi-rulers text-xs"></i>
                            <span>Size</span>
                            <span id="sizeActiveBadge" class="hidden ml-1 text-white text-[8px] px-2 py-0.5 rounded-full bg-[var(--t18-primary)]">Active</span>
                        </button>
                        <div id="sizeDropdown" class="t18-filter-dropdown hidden" style="width: 240px;">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-[10px] font-bold uppercase text-gray-600">Select Size</span>
                                <button id="resetSizeFilter" class="text-gray-400 hover:text-[var(--t18-primary)] text-[10px] flex items-center gap-1">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                            </div>
                            <div class="grid grid-cols-3 gap-1.5">
                                <?php foreach ($sizes as $size): ?>
                                    <label class="flex items-center gap-1.5 text-[10px] font-semibold cursor-pointer hover:text-[var(--t18-primary)] p-1">
                                        <input type="checkbox" class="size-filter accent-[var(--t18-primary)] w-3 h-3" value="<?= htmlspecialchars($size) ?>">
                                        <span><?= htmlspecialchars($size) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-3">
                                <button id="applySizeFilter" class="w-full text-white text-[10px] font-bold py-2 rounded-lg bg-[var(--t18-primary)] hover:bg-[var(--t18-hover)] transition">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Sort + Clear -->
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                <div class="flex items-center gap-1.5">
                    <span class="text-[9px] font-bold uppercase text-gray-400">Sort</span>
                    <select id="sortBy" class="border border-gray-200 rounded-md px-2 py-1.5 text-[10px] font-bold text-gray-700 focus:outline-none focus:border-[var(--t18-primary)] bg-white cursor-pointer">
                        <option value="featured">Featured</option>
                        <option value="newest">Newest</option>
                        <option value="low-high">Price: Low to High</option>
                        <option value="high-low">Price: High to Low</option>
                    </select>
                </div>
                <button id="clearAllFilters" class="flex items-center gap-1.5 px-3 py-1.5 text-white text-[10px] font-bold rounded-md bg-[var(--t18-primary)] hover:bg-[var(--t18-hover)] transition">
                    <i class="bi bi-x-circle"></i> Clear
                </button>
            </div>
        </div>
    </div>


    <!-- ==================== MOBILE FILTER BUTTON ==================== -->
    <div class="sm:hidden max-w-[1500px] mx-auto px-4 mb-4 flex justify-between items-center">
        <span class="text-[10px] font-bold text-gray-500" id="mobileProductCount"><?= $totalProductsCount ?> Products</span>
        <button id="openMobileFilter" class="flex items-center gap-1.5 text-white text-[10px] font-bold px-3 py-1.5 rounded-md bg-[var(--t18-primary)]">
            <i class="bi bi-sliders"></i> Filter
        </button>
    </div>


    <!-- ==================== PRODUCT AREA ==================== -->
    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div id="contentWrapper" class="w-full">
            <div id="content" class="transition-all duration-300">
                <div id="loaderBox" class="col-span-full flex flex-col items-center justify-center py-16">
                    <div class="relative w-10 h-10 mb-2">
                        <div class="absolute inset-0 border-4 rounded-full border-[var(--t18-primary)] opacity-20"></div>
                        <div class="absolute inset-0 border-4 border-transparent rounded-full animate-spin border-t-[var(--t18-primary)]"></div>
                    </div>
                    <p class="text-gray-500 text-[10px] font-bold uppercase tracking-widest">Loading...</p>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyMessage" class="hidden flex-col items-center justify-center text-center w-full py-20">
                <i class="bi bi-search text-5xl text-gray-300 mb-3"></i>
                <h2 class="text-xl font-black text-gray-900 mb-1">No products found</h2>
                <p class="text-xs text-gray-500 max-w-md mx-auto mb-5">We couldn't find anything matching your filters. Try adjusting your price range or size selection.</p>
                <a href="<?= $storeUrl ?>" class="inline-block px-6 py-2.5 text-white text-[10px] font-bold rounded-full bg-[var(--t18-primary)] hover:bg-[var(--t18-hover)] transition">
                    Back to Shop
                </a>
            </div>
        </div>
    </div>


    <!-- ==================== MOBILE FILTER SIDEBAR ==================== -->
    <div id="mobileFilterSidebar" class="fixed top-0 right-0 w-80 sm:w-96 h-full bg-white transform translate-x-full transition-transform duration-300 ease-in-out z-[9999] overflow-y-auto shadow-2xl rounded-l-2xl">
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <h2 class="text-sm font-black text-gray-900">Filter & Sort</h2>
            <button id="closeMobileFilter" class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:text-gray-900 transition"><i class="bi bi-x-lg text-xs"></i></button>
        </div>
        <div class="p-4 space-y-4">
            <div>
                <h3 class="text-[10px] font-bold uppercase text-gray-600 mb-2">Price Range</h3>
                <div class="flex items-center justify-between mb-2 bg-gray-50 border border-gray-100 p-2 rounded-lg">
                    <div class="flex items-center gap-1">
                        <span class="text-[8px] font-bold uppercase text-gray-400">Min</span>
                        <span id="minPriceMobileDisplay" class="text-sm font-black text-[var(--t18-primary)]">₹0</span>
                    </div>
                    <span class="text-gray-300">—</span>
                    <div class="flex items-center gap-1">
                        <span id="maxPriceMobileDisplay" class="text-sm font-black text-[var(--t18-primary)]">₹<?= number_format($maxPrice) ?></span>
                        <span class="text-[8px] font-bold uppercase text-gray-400">Max</span>
                    </div>
                </div>
                <div class="relative pt-2 mb-2" style="height: 24px;">
                    <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-200 rounded-full -translate-y-1/2">
                        <div id="mobileSliderRangeFill" class="absolute h-full rounded-full bg-[var(--t18-primary)]" style="left: 0%; right: 0%;"></div>
                    </div>
                    <input type="range" id="minPriceMobileSlider" class="t18-slider-thumb" min="0" max="<?= $maxPrice ?>" value="0" step="100" />
                    <input type="range" id="maxPriceMobileSlider" class="t18-slider-thumb" min="0" max="<?= $maxPrice ?>" value="<?= $maxPrice ?>" step="100" />
                </div>
                <div class="flex flex-wrap gap-1">
                    <?php foreach ($presets as $preset): ?>
                        <button class="mobile-preset text-[9px] font-bold px-2 py-1 border border-gray-200 rounded-md hover:border-[var(--t18-primary)] hover:text-[var(--t18-primary)] transition"
                            data-min="<?= $preset[0] ?>" data-max="<?= $preset[1] ?>">₹<?= number_format($preset[0]) ?> - ₹<?= number_format($preset[1]) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ($hasSizeFilter): ?>
                <div>
                    <h3 class="text-[10px] font-bold uppercase text-gray-600 mb-1.5">Size</h3>
                    <div class="grid grid-cols-3 gap-1.5">
                        <?php foreach ($sizes as $size): ?>
                            <label class="flex items-center gap-1.5 text-[10px] font-semibold cursor-pointer hover:text-[var(--t18-primary)]">
                                <input type="checkbox" class="size-filter-mobile accent-[var(--t18-primary)] w-3 h-3" value="<?= htmlspecialchars($size) ?>">
                                <span><?= htmlspecialchars($size) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div>
                <h3 class="text-[10px] font-bold uppercase text-gray-600 mb-1.5">Sort by</h3>
                <select id="sortMobile" class="w-full border border-gray-200 rounded-md px-2 py-1.5 text-[10px] font-bold text-gray-700 focus:outline-none focus:border-[var(--t18-primary)] bg-white">
                    <option value="featured">Featured</option>
                    <option value="newest">Newest</option>
                    <option value="low-high">Price: Low to High</option>
                    <option value="high-low">Price: High to Low</option>
                </select>
            </div>
            <div class="flex gap-2 pt-3">
                <button id="clearMobileFilters" class="w-1/2 border border-gray-200 text-gray-700 font-bold py-2.5 rounded-lg text-[10px] uppercase hover:bg-gray-50 transition">Clear</button>
                <button id="applyMobileFilters" class="w-1/2 text-white font-bold py-2.5 rounded-lg text-[10px] uppercase bg-[var(--t18-primary)] hover:bg-[var(--t18-hover)] transition">Apply</button>
            </div>
        </div>
    </div>

    <div id="mobileBackdrop" class="fixed inset-0 bg-black/40 hidden z-[9998] transition-opacity duration-300 backdrop-blur-sm"></div>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

    <!-- ==================== CAROUSEL SCRIPT ==================== -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const carousel = document.getElementById("categoryCarousel");
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");
            if (carousel && prevBtn && nextBtn) {
                const step = 120; // Smaller step for smaller cards
                nextBtn.addEventListener("click", () => carousel.scrollBy({
                    left: step,
                    behavior: "smooth"
                }));
                prevBtn.addEventListener("click", () => carousel.scrollBy({
                    left: -step,
                    behavior: "smooth"
                }));
            }
        });
    </script>

    <!-- ==================== MAIN FILTER + AJAX SCRIPT ==================== -->
    <script>
        $(document).ready(function() {
            const MAX_LIMIT = <?= $maxPrice ?>;
            let minPrice = 0;
            let maxPrice = MAX_LIMIT;
            let page = 1;
            let isLoading = false;
            let hasMoreData = true;

            function formatPrice(price) {
                return '₹' + Number(price).toLocaleString('en-IN');
            }

            function updatePriceDisplays() {
                $('#minPriceDisplay, #minPriceMobileDisplay').text(formatPrice(minPrice));
                $('#maxPriceDisplay, #maxPriceMobileDisplay').text(formatPrice(maxPrice));
                $('#minPriceSlider, #minPriceMobileSlider').val(minPrice);
                $('#maxPriceSlider, #maxPriceMobileSlider').val(maxPrice);

                const minPercent = (minPrice / MAX_LIMIT) * 100;
                const maxPercent = (maxPrice / MAX_LIMIT) * 100;
                $('#sliderRangeFill, #mobileSliderRangeFill').css({
                    'left': minPercent + '%',
                    'right': (100 - maxPercent) + '%'
                });

                if (minPrice > 0 || maxPrice < MAX_LIMIT) $('#priceActiveBadge').removeClass('hidden');
                else $('#priceActiveBadge').addClass('hidden');
            }

            $('#minPriceSlider, #minPriceMobileSlider').on('input', function() {
                let val = parseInt($(this).val());
                if (val > maxPrice - 100) {
                    val = maxPrice - 100;
                    $(this).val(val);
                }
                minPrice = Math.max(0, val);
                updatePriceDisplays();
            });

            $('#maxPriceSlider, #maxPriceMobileSlider').on('input', function() {
                let val = parseInt($(this).val());
                if (val < minPrice + 100) {
                    val = minPrice + 100;
                    $(this).val(val);
                }
                maxPrice = Math.min(MAX_LIMIT, val);
                updatePriceDisplays();
            });

            $('.price-preset, .mobile-preset').on('click', function() {
                $('.price-preset, .mobile-preset').removeClass('active');
                $(this).addClass('active');
                minPrice = parseInt($(this).data('min')) || 0;
                maxPrice = parseInt($(this).data('max')) || MAX_LIMIT;
                updatePriceDisplays();
            });

            function applySizeFilterToDOM() {
                const unique = [...new Set([
                    ...$(".size-filter:checked").map((i, el) => $(el).val().trim()).get(),
                    ...$(".size-filter-mobile:checked").map((i, el) => $(el).val().trim()).get()
                ])];

                $("#content > .group").each(function() {
                    if (unique.length === 0) {
                        $(this).removeClass("hidden-product");
                    } else {
                        const productSizes = ($(this).data("size") + "").split(",").map(s => s.trim()).filter(Boolean);
                        if (productSizes.length === 0 || productSizes.some(s => unique.includes(s))) {
                            $(this).removeClass("hidden-product");
                        } else {
                            $(this).addClass("hidden-product");
                        }
                    }
                });

                if (unique.length > 0) $("#sizeActiveBadge").removeClass("hidden");
                else $("#sizeActiveBadge").addClass("hidden");
            }

            function sortProducts(sortType) {
                const container = $("#content");
                const products = container.children(".group").get();
                products.sort(function(a, b) {
                    const priceA = parseFloat($(a).find(".selected-price, .productPrice").first().text().replace(/[^0-9.]/g, "")) || 0;
                    const priceB = parseFloat($(b).find(".selected-price, .productPrice").first().text().replace(/[^0-9.]/g, "")) || 0;
                    const idA = parseInt($(a).attr("data-id")) || 0;
                    const idB = parseInt($(b).attr("data-id")) || 0;
                    switch (sortType) {
                        case "low-high":
                            return priceA - priceB;
                        case "high-low":
                            return priceB - priceA;
                        case "newest":
                            return idB - idA;
                        default:
                            return idA - idB;
                    }
                });
                container.empty().append(products);
            }

            function applyAllClientFilters() {
                applySizeFilterToDOM();
                sortProducts($("#sortBy").val() || "featured");
                updateProductCount();
            }

            function updateProductCount() {
                const total = $("#content > .group").filter(":not(.hidden-product)").length;
                const text = total + (total === 1 ? " product" : " products");
                $("#productCount, #mobileProductCount").text(text);
            }

            function showLoader() {
                if (!$("#content #loaderBox").length) {
                    $("#content").prepend(`<div id="loaderBox" class="col-span-full flex flex-col items-center justify-center py-16"><div class="relative w-10 h-10 mb-2"><div class="absolute inset-0 border-4 rounded-full border-[var(--t18-primary)] opacity-20"></div><div class="absolute inset-0 border-4 border-transparent rounded-full animate-spin border-t-[var(--t18-primary)]"></div></div><p class="text-gray-500 text-[10px] font-bold uppercase tracking-widest">Loading...</p></div>`);
                }
                $("#loaderBox").show();
            }

            function hideLoader() {
                $("#loaderBox").remove();
            }

            function showEmptyMessage() {
                hideLoader();
                $("#content").empty();
                $("#emptyMessage").removeClass("hidden").css("display", "flex").hide().fadeIn(250);
            }

            function hideEmptyMessage() {
                $("#emptyMessage").hide().addClass("hidden");
            }

            function loadAllProducts() {
                if (isLoading) return;
                isLoading = true;
                page = 1;
                hasMoreData = true;
                $("#content").empty();
                hideEmptyMessage();
                showLoader();

                function loadNextPage() {
                    $.ajax({
                        url: "shop/ajax/get-category-wise-product.php",
                        type: "POST",
                        data: {
                            page: page,
                            category: $("#category").text(),
                            min_price: minPrice > 0 ? minPrice : 0,
                            max_price: maxPrice < MAX_LIMIT ? maxPrice : 0,
                        },
                        success: function(response) {
                            const stripped = $.trim(response || "").replace(/<script[\s\S]*?<\/script>/gi, "").trim();
                            if (stripped === "" || stripped.length < 10) {
                                hasMoreData = false;
                            } else {
                                if (page === 1) hideLoader();
                                const $products = $(stripped);
                                const $validProducts = $products.filter(".group");
                                $validProducts.each(function() {
                                    const productId = $(this).attr("data-id");
                                    if (productId && $("#content > .group[data-id='" + productId + "']").length) {
                                        $(this).remove();
                                    }
                                });
                                $("#content").append($validProducts);
                                page++;
                            }
                            if (hasMoreData) loadNextPage();
                            else {
                                isLoading = false;
                                hideLoader();
                                if ($("#content .group").length === 0) showEmptyMessage();
                                else applyAllClientFilters();
                            }
                        },
                        error: function() {
                            isLoading = false;
                            hideLoader();
                            if (page === 1) showEmptyMessage();
                        }
                    });
                }
                loadNextPage();
            }

            $("#priceDropdownBtn").on("click", function(e) {
                e.stopPropagation();
                $("#priceDropdown").toggleClass("hidden");
                $("#sizeDropdown").addClass("hidden");
            });
            $("#sizeDropdownBtn").on("click", function(e) {
                e.stopPropagation();
                $("#sizeDropdown").toggleClass("hidden");
                $("#priceDropdown").addClass("hidden");
            });
            $("#priceDropdown, #sizeDropdown").on("click", function(e) {
                e.stopPropagation();
            });
            $(document).on("click", function() {
                $("#priceDropdown, #sizeDropdown").addClass("hidden");
            });

            $("#applyPriceFilter").on("click", function() {
                $("#priceDropdown").addClass("hidden");
                loadAllProducts();
            });
            $("#resetPriceFilter").on("click", function() {
                minPrice = 0;
                maxPrice = MAX_LIMIT;
                $('.price-preset, .mobile-preset').removeClass('active');
                updatePriceDisplays();
                $("#priceDropdown").addClass("hidden");
                loadAllProducts();
            });
            $("#applySizeFilter").on("click", function() {
                applySizeFilterToDOM();
                updateProductCount();
                $("#sizeDropdown").addClass("hidden");
            });
            $("#resetSizeFilter").on("click", function() {
                $(".size-filter, .size-filter-mobile").prop("checked", false);
                applySizeFilterToDOM();
                updateProductCount();
                $("#sizeDropdown").addClass("hidden");
            });

            $("#sortBy").on("change", function() {
                sortProducts($(this).val());
                $("#sortMobile").val($(this).val());
            });
            $("#sortMobile").on("change", function() {
                sortProducts($(this).val());
                $("#sortBy").val($(this).val());
            });

            function clearAllFilters() {
                minPrice = 0;
                maxPrice = MAX_LIMIT;
                $('.price-preset, .mobile-preset').removeClass('active');
                updatePriceDisplays();
                $(".size-filter, .size-filter-mobile").prop("checked", false);
                $("#sizeActiveBadge").addClass("hidden");
                $("#sortBy, #sortMobile").val("featured");
                $("#priceDropdown, #sizeDropdown").addClass("hidden");
                loadAllProducts();
            }
            $("#clearAllFilters").on("click", clearAllFilters);

            const sidebar = $("#mobileFilterSidebar");
            const backdrop = $("#mobileBackdrop");
            $("#openMobileFilter").on("click", function() {
                sidebar.removeClass("translate-x-full");
                backdrop.removeClass("hidden").css("opacity", "1");
            });
            $("#closeMobileFilter, #mobileBackdrop").on("click", function() {
                sidebar.addClass("translate-x-full");
                backdrop.addClass("hidden").css("opacity", "0");
            });
            $("#clearMobileFilters").on("click", function() {
                clearAllFilters();
                sidebar.addClass("translate-x-full");
                backdrop.addClass("hidden").css("opacity", "0");
            });
            $("#applyMobileFilters").on("click", function() {
                $("#sortBy").val($("#sortMobile").val());
                sidebar.addClass("translate-x-full");
                backdrop.addClass("hidden").css("opacity", "0");
                loadAllProducts();
            });

            updatePriceDisplays();
            $("#emptyMessage").hide();
            setTimeout(loadAllProducts, 100);
        });
    </script>

</body>

</html>