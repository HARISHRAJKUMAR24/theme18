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
            border-radius: 35%; /* Squircle/Rounded shape, not circle */
            background: linear-gradient(135deg, color-mix(in srgb, var(--t18-primary) 15%, #fff) 0%, color-mix(in srgb, var(--t18-hover) 10%, #fff) 100%);
            box-shadow: 0 20px 40px color-mix(in srgb, var(--t18-primary) 10%, transparent);
            filter: blur(2px);
            opacity: 0.6;
            animation: floatShape 12s ease-in-out infinite alternate;
        }

        .t18-shape-1 {
            width: 300px; height: 300px;
            top: -100px; left: -100px;
            transform: rotate(25deg);
        }

        .t18-shape-2 {
            width: 400px; height: 400px;
            top: 100px; right: -150px;
            transform: rotate(-15deg);
            animation-delay: -5s;
        }

        .t18-shape-3 {
            width: 150px; height: 150px;
            top: 250px; left: 15%;
            opacity: 0.4;
            animation-duration: 18s;
        }

        @keyframes floatShape {
            0% { transform: translate(0, 0) rotate(0deg) scale(1); }
            100% { transform: translate(20px, -20px) rotate(15deg) scale(1.05); }
        }

        /* ==================== HERO SECTION ==================== */
        .t18-hero {
            position: relative;
            padding: 30px 16px 30px;
            text-align: center;
            z-index: 1;
        }

        @media (min-width: 768px) {
            .t18-hero { padding: 40px 24px 40px; }
        }

        .t18-hero h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 900;
            color: #111827;
            line-height: 1.1;
            margin-bottom: 16px;
            letter-spacing: -1px;
        }

        .t18-hero p {
            font-size: clamp(0.9rem, 1.8vw, 1.1rem);
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
            background: color-mix(in srgb, var(--t18-primary) 12%, #fff);
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 20px;
        }

        .t18-hero-count {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid #e5e7eb;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* ==================== CATEGORY STRIP ==================== */
        .t18-cat-wrap {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto 30px;
            padding: 0 16px;
        }

        .t18-cat-scroll {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding: 10px 4px 20px;
            scrollbar-width: none;
        }
        .t18-cat-scroll::-webkit-scrollbar { display: none; }

        .t18-cat-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 0 0 auto;
            width: 90px;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .t18-cat-card:hover {
            transform: translateY(-4px);
        }

        .t18-cat-img {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 15%, #e5e7eb);
            box-shadow: 0 8px 16px -6px rgba(0,0,0,0.08);
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .t18-cat-card:hover .t18-cat-img {
            border-color: var(--t18-primary);
            box-shadow: 0 12px 20px -6px var(--t18-primary);
        }

        .t18-cat-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .t18-cat-img span {
            font-size: 24px;
            font-weight: 900;
            color: var(--t18-primary);
        }

        .t18-cat-name {
            font-size: 11px;
            font-weight: 700;
            color: #4b5563;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .t18-cat-card:hover .t18-cat-name {
            color: var(--t18-primary);
        }

        /* ==================== FILTER BAR ==================== */
        .t18-filter-bar {
            position: relative;
            z-index: 10;
            background: #fff;
            border-radius: 16px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 15%, #e5e7eb);
            box-shadow: 0 6px 20px -10px color-mix(in srgb, var(--t18-primary) 20%, transparent);
            padding: 10px 16px;
            margin-bottom: 30px;
            max-width: 1200px;
            margin-inline: auto;
        }

        .t18-filter-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            background: #fff;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            transition: all 0.2s;
        }
        .t18-filter-btn:hover, .t18-filter-btn.active {
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
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.1);
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
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        /* Product Grid Overrides */
        #content {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            position: relative;
            z-index: 1;
        }
        @media (min-width: 640px) { #content { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; } }
        @media (min-width: 1024px) { #content { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 24px; } }
        @media (min-width: 1280px) { #content { grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 24px; } }
    </style>
</head>

<body class="min-h-screen">

    <?php
    $primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f';
    $hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899';
    ?>

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <?php
    // ==================== FETCH PRODUCTS ====================
    $products_stmt = getProducts(["limit" => 40]);
    $products = $products_stmt ? $products_stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    $products_count = count($products);

    // ==================== GET SIZES ====================
    global $db, $sellerId, $storeId;
    $sizes = [];

    if (!empty($products) && !empty($sellerId) && !empty($storeId)) {
        $productPkIds = [];
        foreach ($products as $p) {
            if (!empty($p['id'])) $productPkIds[] = (int)$p['id'];
        }
        $productPkIds = array_values(array_unique($productPkIds));

        if (!empty($productPkIds)) {
            $placeholders = implode(',', array_fill(0, count($productPkIds), '?'));
            $sql = "
                SELECT DISTINCT v.size
                FROM seller_product_advanced_variants v
                INNER JOIN seller_products p ON p.product_id = v.product_id
                WHERE p.seller_id = ?
                  AND p.store_id = ?
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

    $categories = getCategories([
        "add_to_menu"     => 1,
        "parent_category" => " ",
    ]);
    ?>

    <!-- 3D Background Shapes -->
    <div class="t18-shape-wrap">
        <div class="t18-shape t18-shape-1"></div>
        <div class="t18-shape t18-shape-2"></div>
        <div class="t18-shape t18-shape-3"></div>
    </div>

    <!-- ==================== HERO ==================== -->
    <section class="t18-hero">
        <div class="relative z-10 max-w-3xl mx-auto">
            <span class="t18-eyebrow">The Latest Look</span>
            <h1>New Arrivals</h1>
            <p>Fresh finds, just added. Explore the latest pieces selected for your next favourite look.</p>
            
            <div class="t18-hero-count">
                <i class="bi bi-box-seam text-[var(--t18-primary)]"></i>
                <span id="productCount"><?= $products_count ?></span> Items to discover
            </div>
        </div>
    </section>

    <!-- ==================== CATEGORY STRIP ==================== -->
    <?php if (!empty($categories) && $categories->rowCount()): ?>
        <div class="t18-cat-wrap">
            <div id="categoriesScroll" class="t18-cat-scroll">
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= $storeUrl . "category/" . $cat['slug'] ?>" class="t18-cat-card">
                        <div class="t18-cat-img">
                            <?php if (!empty($cat['icon'])): ?>
                                <img src="<?= UPLOADS_URL . $cat['icon'] ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
                            <?php else: ?>
                                <span><?= strtoupper(substr($cat['name'], 0, 1)) ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="t18-cat-name"><?= htmlspecialchars($cat['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- ==================== FILTER BAR ==================== -->
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 mb-8 relative z-10">
        <div class="t18-filter-bar flex flex-col sm:flex-row items-center justify-between gap-4">

            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">

                <!-- Price Range Dropdown -->
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

                <!-- Size Filter -->
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

    <!-- ==================== PRODUCT GRID ==================== -->
    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 pb-16 relative z-10">
        <div id="content">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <?= getProductHtml($product['id']) ?>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="col-span-full text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20 flex items-center justify-center rounded-2xl" style="background: color-mix(in srgb, <?= htmlspecialchars($primary_color) ?> 8%, white); color: var(--t18-primary);">
                            <i class="bi bi-box-seam text-4xl"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 mb-2">No New Products</h2>
                    <p class="text-gray-500 text-sm mb-6 max-w-md mx-auto">Check back soon for new arrivals!</p>
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

            const productContainer = document.getElementById('content');
            const originalProductOrder = productContainer
                ? Array.from(productContainer.querySelectorAll('.t9-product-card'))
                : [];

            const MAX_LIMIT = 10000;
            const currencySym = "<?= currencyToSymbol($storeCurrency) ?>";

            let minPrice = 0;
            let maxPrice = MAX_LIMIT;
            let currentSizes = [];

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

            // ============ SLIDER ============
            $('#minPriceSlider').on('input', function() {
                let v = parseInt($(this).val());
                if (v > maxPrice - 100) { v = maxPrice - 100; $(this).val(v); }
                minPrice = Math.max(0, v);
                updatePriceDisplays();
            });
            $('#maxPriceSlider').on('input', function() {
                let v = parseInt($(this).val());
                if (v < minPrice + 100) { v = minPrice + 100; $(this).val(v); }
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

            // ============ DROPDOWN TOGGLES ============
            $('#priceDropdownBtn').on('click', function(e) {
                e.stopPropagation();
                $('#priceDropdown').toggleClass('hidden');
                $('#sizeDropdown').addClass('hidden');
            });
            $('#sizeDropdownBtn').on('click', function(e) {
                e.stopPropagation();
                $('#sizeDropdown').toggleClass('hidden');
                $('#priceDropdown').addClass('hidden');
            });
            $('#priceDropdown, #sizeDropdown').on('click', function(e) {
                e.stopPropagation();
            });
            $(document).on('click', function() {
                $('#priceDropdown, #sizeDropdown').addClass('hidden');
            });

            // ============ APPLY FILTERS ============
            $('#applyPriceFilter').on('click', function() {
                applyAllFilters();
                $('#priceDropdown').addClass('hidden');
            });

            $('#resetPriceFilter').on('click', function() {
                minPrice = 0;
                maxPrice = MAX_LIMIT;
                $('.price-preset').removeClass('active');
                updatePriceDisplays();
                applyAllFilters();
                $('#priceDropdown').addClass('hidden');
            });

            $('#applySizeFilter').on('click', function() {
                applyAllFilters();
                $('#sizeDropdown').addClass('hidden');
            });

            $('#resetSizeFilter').on('click', function() {
                $('.size-filter').prop('checked', false);
                applyAllFilters();
                $('#sizeDropdown').addClass('hidden');
            });

            // ============ MAIN FILTER LOGIC ============
            function applyAllFilters() {
                currentSizes = $('.size-filter:checked').map((i, el) => $(el).val().trim()).filter(Boolean).get();

                let visibleCount = 0;
                const totalCount = $('#content .t9-product-card').length;

                $('#content .t9-product-card').each(function() {
                    const $p = $(this);

                    // Price check
                    let price = 0;
                    const priceEl = $p.find('.productPrice').first();
                    if (priceEl.length) {
                        const m = priceEl.text().match(/(\d+\.?\d*)/);
                        if (m) price = parseFloat(m[1]);
                    }
                    const priceMatch = price >= minPrice && price <= maxPrice;

                    // Size check
                    let sizeMatch = true;
                    if (currentSizes.length > 0) {
                        const productSizes = ($p.data('size') + '').split(',').map(s => s.trim()).filter(Boolean);
                        sizeMatch = productSizes.length === 0 || productSizes.some(s => currentSizes.includes(s));
                    }

                    if (priceMatch && sizeMatch) {
                        $p.removeClass('hidden-product');
                        visibleCount++;
                    } else {
                        $p.addClass('hidden-product');
                    }
                });

                // Update badges
                if (currentSizes.length > 0) $('#sizeActiveBadge').removeClass('hidden');
                else $('#sizeActiveBadge').addClass('hidden');

                // Update count
                $('#productCount').text(visibleCount);
                
                // Apply sorting
                applySorting();
            }

            // ============ SORTING ============
            $('#sortBy').on('change', function() {
                applySorting($(this).val());
            });

            function applySorting(sortType) {
                sortType = sortType || $('#sortBy').val() || 'default';

                const container = document.getElementById('content');
                if (!container) return;

                if (sortType === 'default') {
                    originalProductOrder.forEach(function(card) {
                        if (card.isConnected || card.parentElement === container) {
                            container.appendChild(card);
                        }
                    });
                    return;
                }
                const visible = Array.from(container.querySelectorAll('.t9-product-card:not(.hidden-product)'));
                const hidden = Array.from(container.querySelectorAll('.t9-product-card.hidden-product'));

                visible.sort((a, b) => {
                    const pa = getPrice(a);
                    const pb = getPrice(b);
                    const ia = parseInt(a.getAttribute('data-id')) || 0;
                    const ib = parseInt(b.getAttribute('data-id')) || 0;

                    switch (sortType) {
                        case 'low-high': return pa - pb;
                        case 'high-low': return pb - pa;
                        case 'latest':   return ib - ia;
                        case 'oldest':   return ia - ib;
                        default:         return 0;
                    }
                });

                container.innerHTML = '';
                visible.forEach(p => container.appendChild(p));
                hidden.forEach(p => container.appendChild(p));
            }

            function getPrice(el) {
                const priceEl = el.querySelector('.productPrice');
                if (priceEl) {
                    const m = priceEl.textContent.match(/(\d+\.?\d*)/);
                    if (m) return parseFloat(m[1]);
                }
                return 0;
            }

            // ============ CLEAR ALL ============
            function clearAll() {
                minPrice = 0;
                maxPrice = MAX_LIMIT;
                currentSizes = [];

                $('.price-preset').removeClass('active');
                $('.size-filter').prop('checked', false);
                $('#sortBy').val('default');

                updatePriceDisplays();

                $('#priceActiveBadge, #sizeActiveBadge').addClass('hidden');
                $('#content .t9-product-card').removeClass('hidden-product');

                originalProductOrder.forEach(function(card) {
                    productContainer.appendChild(card);
                });

                $('#productCount').text(originalProductOrder.length);
                $('#priceDropdown, #sizeDropdown').addClass('hidden');
            }

            $('#clearAllFilters').on('click', clearAll);

            // ============ INIT ============
            updatePriceDisplays();
        });
    </script>

</body>

</html>