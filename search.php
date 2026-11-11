<?php include_once __DIR__ . "/includes/files_includes.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>

    <style>
        :root {
            --t18-primary: <?= htmlspecialchars(getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f') ?>;
            --t18-hover: <?= htmlspecialchars(getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899') ?>;
        }

        body {
            background: radial-gradient(circle at 10% 20%, color-mix(in srgb, var(--t18-primary) 5%, #fcfcfc) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, color-mix(in srgb, var(--t18-hover) 5%, #fcfcfc) 0%, transparent 40%),
                #fafafa;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
        }

        /* ==================== 3D FLOATING SHAPES ==================== */
        .t18-shape-wrap {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
            height: 500px;
        }

        .t18-shape {
            position: absolute;
            border-radius: 35%;
            background: linear-gradient(135deg, color-mix(in srgb, var(--t18-primary) 12%, #fff) 0%, color-mix(in srgb, var(--t18-hover) 8%, #fff) 100%);
            box-shadow: 0 20px 40px color-mix(in srgb, var(--t18-primary) 10%, transparent);
            filter: blur(4px);
            opacity: 0.5;
            animation: floatShape 15s ease-in-out infinite alternate;
        }

        .t18-shape-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
            transform: rotate(25deg);
        }

        .t18-shape-2 {
            width: 400px;
            height: 400px;
            top: 50px;
            right: -150px;
            transform: rotate(-15deg);
            animation-delay: -5s;
        }

        .t18-shape-3 {
            width: 150px;
            height: 150px;
            top: 250px;
            left: 20%;
            opacity: 0.4;
            animation-duration: 18s;
        }

        @keyframes floatShape {
            0% {
                transform: translate(0, 0) rotate(0deg) scale(1);
            }

            100% {
                transform: translate(20px, -20px) rotate(15deg) scale(1.05);
            }
        }

        /* ==================== PAGE HEADER ==================== */
        .t18-hero {
            position: relative;
            z-index: 1;
            padding: 60px 16px 40px;
            text-align: center;
        }

        .t18-hero h1 {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 900;
            color: #111827;
            line-height: 1.1;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .t18-hero p {
            font-size: clamp(0.9rem, 1.8vw, 1.05rem);
            color: #4b5563;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .t18-eyebrow {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--t18-primary);
            background: color-mix(in srgb, var(--t18-primary) 10%, #fff);
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 16px;
        }

        /* ==================== FILTER BAR ==================== */
        .t18-filter-bar {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 10px 30px -12px color-mix(in srgb, var(--t18-primary) 15%, transparent);
            padding: 16px 20px;
            margin-bottom: 30px;
            max-width: 1200px;
            margin-inline: auto;
        }

        .t18-filter-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            background: #fff;
            font-size: 12px;
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

        /* ==================== RESPONSIVE PRODUCT ROW GRID ==================== */
        #content {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            justify-content: stretch;
            align-items: stretch;
            gap: 22px;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        #content>.group {
            width: 100% !important;
            max-width: none !important;
            min-width: 0;
            justify-self: stretch;
        }

        /* Make the product card use the full grid-column width. */
        #content>.group>* {
            width: 100% !important;
            max-width: none !important;
            min-width: 0;
            box-sizing: border-box;
        }

        .t18-filter-bar {
            width: 100%;
            box-sizing: border-box;
        }

        .t18-filter-dropdown {
            width: min(320px, calc(100vw - 32px));
            max-width: calc(100vw - 32px);
        }

        @media (max-width: 1023px) {
            #content {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
            }
        }

        @media (max-width: 639px) {
            .t18-hero {
                padding: 42px 16px 28px;
            }

            .t18-filter-bar {
                padding: 14px;
                border-radius: 16px;
                align-items: stretch;
            }

            .t18-filter-bar>div {
                min-width: 0;
            }

            .t18-filter-bar select {
                max-width: 100%;
            }

            .t18-filter-dropdown {
                left: 0;
                right: auto;
            }

            #content {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
            }
        }

        @media (max-width: 360px) {
            #content {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }
        }
    </style>
</head>

<body class="font-sans min-h-screen flex flex-col relative">

    <?php
    $primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f';
    $hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899';
    ?>

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <?php
    if (isset($_GET['q'])) {
        $q = $_GET['q'];
    } else {
        redirect($storeUrl);
    }

    $count = getProducts(array("count" => true, "q" => $q));
    foreach ($count as $key => $value) {
        $count = $value[0];
    }

    $category = $_GET['category'] ?? '';

    $search_products_stmt = getProducts(array("q" => $q, "category" => $category));
    $search_products = $search_products_stmt ? $search_products_stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    $search_products_count = count($search_products);

    global $db, $sellerId, $storeId;
    $sizes = [];

    if (!empty($search_products) && !empty($sellerId) && !empty($storeId)) {
        $productPkIds = [];
        foreach ($search_products as $sp) {
            if (!empty($sp['id'])) $productPkIds[] = (int)$sp['id'];
        }
        $productPkIds = array_values(array_unique($productPkIds));

        if (!empty($productPkIds)) {
            $placeholders = implode(',', array_fill(0, count($productPkIds), '?'));
            $sql = "
                SELECT DISTINCT v.size
                FROM seller_product_advanced_variants v
                INNER JOIN seller_products p ON p.product_id = v.product_id
                WHERE p.seller_id = ?
                  AND p.store_id  = ?
                  AND p.status = 1
                  AND (p.visibility = 'publish' OR (p.visibility = 'schedule' AND p.schedule_date <= NOW()))
                  AND p.id IN ($placeholders)
                  AND v.size IS NOT NULL
                  AND v.size != ''
                ORDER BY v.size
            ";
            $stmt = $db->prepare($sql);
            $params = array_merge([$sellerId, $storeId], $productPkIds);
            $stmt->execute($params);
            $sizes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    }

    $hasSizeFilter = !empty($sizes);
    ?>

    <!-- 3D Background Shapes -->
    <div class="t18-shape-wrap">
        <div class="t18-shape t18-shape-1"></div>
        <div class="t18-shape t18-shape-2"></div>
        <div class="t18-shape t18-shape-3"></div>
    </div>

    <!-- Hero -->
    <div class="t18-hero">
        <span class="t18-eyebrow">Search Results</span>
        <h1><?= htmlspecialchars($q) ?></h1>
        <p>
            <span id="productCount"><?= $search_products_count ?></span>
            <?= $search_products_count == 1 ? 'product' : 'products' ?> found
        </p>
    </div>

    <!-- Filter Bar -->
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 mb-8 relative z-10">
        <div class="t18-filter-bar flex flex-col sm:flex-row items-center justify-between gap-4">

            <!-- Left: Filter buttons -->
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">

                <!-- Price Range -->
                <div class="relative">
                    <button id="priceDropdownBtn" type="button" class="t18-filter-btn">
                        <i class="bi bi-sliders2"></i>
                        <span>Price</span>
                        <span id="priceActiveBadge" class="hidden ml-1 text-white text-[9px] px-2 py-0.5 rounded-full bg-[var(--t18-primary)]">Active</span>
                    </button>

                    <div id="priceDropdown" class="t18-filter-dropdown hidden">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase text-gray-600">Price Range</span>
                            <button id="resetPriceFilter" type="button" class="text-gray-400 hover:text-[var(--t18-primary)] text-xs flex items-center gap-1">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>

                        <div class="flex items-center justify-between mb-4 bg-gray-50 border border-gray-100 p-3 rounded-xl">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] uppercase text-gray-400 font-bold">Min</span>
                                <span id="minPriceDisplay" class="text-base font-black text-[var(--t18-primary)]">₹0</span>
                            </div>
                            <span class="text-gray-300">—</span>
                            <div class="flex items-center gap-2">
                                <span id="maxPriceDisplay" class="text-base font-black text-[var(--t18-primary)]">₹10000</span>
                                <span class="text-[10px] uppercase text-gray-400 font-bold">Max</span>
                            </div>
                        </div>

                        <div class="mb-4 relative pt-2" style="height: 28px;">
                            <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-200 rounded-full -translate-y-1/2">
                                <div id="sliderRangeFill" class="absolute h-full rounded-full bg-[var(--t18-primary)]" style="left: 0%; right: 0%;"></div>
                            </div>
                            <input type="range" id="minPriceSlider" class="t18-slider-thumb" min="0" max="10000" value="0" step="100" />
                            <input type="range" id="maxPriceSlider" class="t18-slider-thumb" min="0" max="10000" value="10000" step="100" />
                        </div>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <?php
                            $presets = [[0, 500], [500, 1000], [1000, 2500], [2500, 5000], [5000, 10000]];
                            foreach ($presets as $preset):
                                $pMin = $preset[0];
                                $pMax = $preset[1];
                                $sym = currencyToSymbol($storeCurrency);
                            ?>
                                <button type="button" class="price-preset text-[10px] font-bold px-3 py-1.5 border border-gray-200 rounded-lg hover:border-[var(--t18-primary)] hover:text-[var(--t18-primary)] transition"
                                    data-min="<?= $pMin ?>" data-max="<?= $pMax ?>">
                                    <?= $sym . number_format($pMin) ?> - <?= $sym . number_format($pMax) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <button id="applyPriceFilter" type="button" class="w-full text-white text-xs font-bold py-3 rounded-xl bg-[var(--t18-primary)] hover:bg-[var(--t18-hover)] transition">
                            Apply Price Range
                        </button>
                    </div>
                </div>

                <!-- Size -->
                <?php if ($hasSizeFilter): ?>
                    <div class="relative">
                        <button id="sizeDropdownBtn" type="button" class="t18-filter-btn">
                            <i class="bi bi-rulers"></i>
                            <span>Size</span>
                            <span id="sizeActiveBadge" class="hidden ml-1 text-white text-[9px] px-2 py-0.5 rounded-full bg-[var(--t18-primary)]">Active</span>
                        </button>
                        <div id="sizeDropdown" class="t18-filter-dropdown hidden" style="width: 260px;">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-xs font-bold uppercase text-gray-600">Select Size</span>
                                <button id="resetSizeFilter" type="button" class="text-gray-400 hover:text-[var(--t18-primary)] text-xs flex items-center gap-1">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <?php foreach ($sizes as $size): ?>
                                    <label class="flex items-center gap-2 text-xs font-semibold cursor-pointer hover:text-[var(--t18-primary)] p-1">
                                        <input type="checkbox" class="size-filter accent-[var(--t18-primary)]" value="<?= htmlspecialchars($size) ?>">
                                        <span><?= htmlspecialchars($size) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-4">
                                <button id="applySizeFilter" type="button" class="w-full text-white text-xs font-bold py-3 rounded-xl bg-[var(--t18-primary)] hover:bg-[var(--t18-hover)] transition">
                                    Apply Size
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Right: Sort + Clear -->
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase text-gray-400">Sort</span>
                    <select id="sortBy" class="border border-gray-200 rounded-lg px-3 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:border-[var(--t18-primary)] bg-white cursor-pointer">
                        <option value="default">Featured</option>
                        <option value="latest">Newest</option>
                        <option value="low-high">Price: Low to High</option>
                        <option value="high-low">Price: High to Low</option>
                        <option value="oldest">Oldest</option>
                    </select>
                </div>
                <button id="clearAllFilters" type="button" class="flex items-center gap-2 px-4 py-2 text-white text-xs font-bold rounded-lg bg-[var(--t18-primary)] hover:bg-[var(--t18-hover)] transition">
                    <i class="bi bi-x-circle"></i> Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Products Area -->
    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 pb-16 relative z-10">
        <div id="content">
            <?php if (!empty($search_products)): ?>
                <?php foreach ($search_products as $product): ?>
                    <?= getProductHtml($product['id']) ?>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="col-span-full text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20 flex items-center justify-center rounded-2xl" style="background: color-mix(in srgb, <?= htmlspecialchars($primary_color) ?> 8%, white); color: var(--t18-primary);">
                            <i class="bi bi-search text-4xl"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 mb-2">No Products Found</h2>
                    <p class="text-gray-500 text-sm mb-6 max-w-md mx-auto">We couldn't find anything matching "<?= htmlspecialchars($q) ?>"</p>
                    <a href="<?= $storeUrl ?>" class="inline-flex items-center justify-center px-8 py-3 rounded-full bg-[var(--t18-primary)] text-white text-xs font-bold uppercase tracking-wider hover:bg-[var(--t18-hover)] transition">
                        Back to Store
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

    <script>
        $(document).ready(function() {

            const MAX_LIMIT = 10000;
            const urlParams = new URLSearchParams(window.location.search);
            let minPrice = parseInt(urlParams.get('min_price')) || 0;
            let maxPrice = parseInt(urlParams.get('max_price')) || MAX_LIMIT;
            const currencySym = "<?= currencyToSymbol($storeCurrency) ?>";

            function formatPrice(p) {
                return currencySym + Number(p).toLocaleString('en-IN');
            }

            function updatePriceDisplays() {
                $('#minPriceDisplay').text(formatPrice(minPrice));
                $('#maxPriceDisplay').text(formatPrice(maxPrice));
                $('#minPriceSlider').val(minPrice);
                $('#maxPriceSlider').val(maxPrice);

                const minPct = (minPrice / MAX_LIMIT) * 100;
                const maxPct = (maxPrice / MAX_LIMIT) * 100;
                $('#sliderRangeFill').css({
                    'left': minPct + '%',
                    'right': (100 - maxPct) + '%'
                });

                if (minPrice > 0 || maxPrice < MAX_LIMIT) $('#priceActiveBadge').removeClass('hidden');
                else $('#priceActiveBadge').addClass('hidden');
            }

            $('#minPriceSlider').on('input', function() {
                let v = parseInt($(this).val());
                if (v > maxPrice - 100) {
                    v = maxPrice - 100;
                    $(this).val(v);
                }
                minPrice = Math.max(0, v);
                updatePriceDisplays();
            });
            $('#maxPriceSlider').on('input', function() {
                let v = parseInt($(this).val());
                if (v < minPrice + 100) {
                    v = minPrice + 100;
                    $(this).val(v);
                }
                maxPrice = Math.min(MAX_LIMIT, v);
                updatePriceDisplays();
            });

            $('.price-preset').on('click', function() {
                $('.price-preset').removeClass('active');
                $(this).addClass('active');
                minPrice = parseInt($(this).data('min')) || 0;
                maxPrice = parseInt($(this).data('max')) || MAX_LIMIT;
                updatePriceDisplays();
            });

            $('#priceDropdownBtn').on('click', function(e) {
                e.stopPropagation();
                $('#priceDropdown').toggleClass('hidden');
                $('#sizeDropdown').addClass('hidden');
            });
            $('#priceDropdown').on('click', function(e) {
                e.stopPropagation();
            });
            $(document).on('click', function() {
                $('#priceDropdown').addClass('hidden');
                $('#sizeDropdown').addClass('hidden');
            });

            $('#applyPriceFilter').on('click', function() {
                const url = new URL(window.location.href);
                if (minPrice > 0) url.searchParams.set('min_price', minPrice);
                else url.searchParams.delete('min_price');
                if (maxPrice < MAX_LIMIT) url.searchParams.set('max_price', maxPrice);
                else url.searchParams.delete('max_price');
                window.location.href = url.toString();
            });

            $('#resetPriceFilter').on('click', function() {
                const url = new URL(window.location.href);
                url.searchParams.delete('min_price');
                url.searchParams.delete('max_price');
                window.location.href = url.toString();
            });

            function applySizeFilterToDOM() {
                const checked = $(".size-filter:checked").map((i, el) => $(el).val().trim()).get();
                const unique = [...new Set(checked)];

                $("#content > .group").each(function() {
                    if (unique.length === 0) {
                        $(this).removeClass("hidden-product");
                    } else {
                        const productSizes = ($(this).data("size") + "").split(",").map(s => s.trim()).filter(Boolean);
                        if (productSizes.length === 0) {
                            $(this).removeClass("hidden-product");
                        } else if (productSizes.some(s => unique.includes(s))) {
                            $(this).removeClass("hidden-product");
                        } else {
                            $(this).addClass("hidden-product");
                        }
                    }
                });

                if (unique.length > 0) $("#sizeActiveBadge").removeClass("hidden");
                else $("#sizeActiveBadge").addClass("hidden");

                updateProductCount();
            }

            function updateProductCount() {
                const total = $("#content > .group").filter(":not(.hidden-product)").length;
                $("#productCount").text(total);
            }

            $('#sizeDropdownBtn').on('click', function(e) {
                e.stopPropagation();
                $('#sizeDropdown').toggleClass('hidden');
                $('#priceDropdown').addClass('hidden');
            });
            $('#sizeDropdown').on('click', function(e) {
                e.stopPropagation();
            });

            $('#applySizeFilter').on('click', function() {
                applySizeFilterToDOM();
                $('#sizeDropdown').addClass('hidden');
            });

            $('#resetSizeFilter').on('click', function() {
                $('.size-filter').prop('checked', false);
                applySizeFilterToDOM();
                $('#sizeDropdown').addClass('hidden');
            });

            $('#clearAllFilters').on('click', function() {
                const url = new URL(window.location.href);
                url.searchParams.delete('min_price');
                url.searchParams.delete('max_price');
                window.location.href = url.toString();
            });

            $('#sortBy').on('change', function() {
                sortProducts($(this).val());
            });

            function sortProducts(sortType) {
                const container = document.getElementById('content');
                if (!container) return;
                const products = Array.from(container.querySelectorAll('.group'));
                if (!products.length) return;

                switch (sortType) {
                    case 'low-high':
                        products.sort((a, b) => getProductPrice(a) - getProductPrice(b));
                        break;
                    case 'high-low':
                        products.sort((a, b) => getProductPrice(b) - getProductPrice(a));
                        break;
                    case 'latest':
                        products.sort((a, b) => getProductId(b) - getProductId(a));
                        break;
                    case 'oldest':
                        products.sort((a, b) => getProductId(a) - getProductId(b));
                        break;
                    default:
                        return;
                }
                container.innerHTML = '';
                products.forEach(p => container.appendChild(p));
            }

            function getProductPrice(el) {
                const selectors = ['.productPrice', '[class*="price"]'];
                for (const s of selectors) {
                    const node = el.querySelector(s);
                    if (node) {
                        const m = (node.textContent || '').match(/(\d+\.?\d*)/);
                        if (m) return parseFloat(m[1]);
                    }
                }
                return 0;
            }

            function getProductId(el) {
                const idAttr = el.getAttribute('data-id') || el.getAttribute('data-product-id');
                if (idAttr) return parseInt(idAttr);
                const btn = el.querySelector('.customAddToCartBtn');
                if (btn && btn.dataset.id) return parseInt(btn.dataset.id);
                return 0;
            }

            updatePriceDisplays();

        });
    </script>

</body>

</html>