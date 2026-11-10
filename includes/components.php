<?php
// ============================================================
// THEME 18 — product_card.php (single-file)
// - Real DB cart tracking (refresh-safe)
// - Bubbles + glows use DB primary/hover colors
// - Card bg uses DB color mix
// ============================================================

$primary_color = getData("color",       "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ed168b';
$hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#dc007b';

if (!function_exists('calculateSavePercent')) {
    function calculateSavePercent($mrp_price, $price)
    {
        if ($mrp_price && $mrp_price > $price) {
            return round((($mrp_price - $price) / $mrp_price) * 100);
        }
        return 0;
    }
}

/* ---------- Theme 9 compatible cart lookup (refresh-safe) ----------
   Theme 9 stores:
   customer_cart.customer_id     = $cookie_id
   customer_cart.product_id      = seller_products.id
   customer_cart.other           = basic variant ID
   customer_cart.advanced_variant= advanced variant ID
*/
if (!function_exists('getCartVariantIds')) {
    function getCartVariantIds($cookieId, $productRowId, $db)
    {
        $result = [
            'main'         => false,
            'basic_ids'    => [],
            'advanced_ids' => [],
        ];

        if (empty($cookieId) || empty($productRowId) || !($db instanceof PDO)) {
            return $result;
        }

        try {
            $stmt = $db->prepare("
                SELECT other, advanced_variant
                FROM customer_cart
                WHERE customer_id = ? AND product_id = ?
            ");
            $stmt->execute([$cookieId, $productRowId]);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $basicId = trim((string)($row['other'] ?? ''));
                $advancedId = trim((string)($row['advanced_variant'] ?? ''));

                if ($advancedId !== '') {
                    $result['advanced_ids'][$advancedId] = true;
                } elseif ($basicId !== '') {
                    $result['basic_ids'][$basicId] = true;
                } else {
                    $result['main'] = true;
                    $result['basic_ids']['main'] = true; // <--- FIX APPLIED HERE
                }
            }
        } catch (Throwable $e) {
            error_log('[Theme 18 cart lookup] ' . $e->getMessage());
        }

        return $result;
    }
}

if (!function_exists('getProductHtml')) {
    function getProductHtml($id)
    {
        global $storeCurrency, $storeUrl, $customerId, $cookie_id, $db, $sellerId, $storeId,
            $primary_color, $hover_color;

        $product_id  = getData("product_id", "seller_products", "id='$id'");
        $slug        = getData("slug",       "seller_products", "id='$id'");
        $name        = getData("name",       "seller_products", "id='$id'");
        if (!$product_id || !$slug || !$name) return '';

        $image       = getData("image",        "seller_products", "id='$id'");
        $price       = getData("price",        "seller_products", "id='$id'") ?: 0;
        $mrp_price   = getData("mrp_price",    "seller_products", "id='$id'") ?: 0;
        $totalStocks = getData("total_stocks", "seller_products", "id='$id'") ?: 0;
        $variation   = getData("variation",    "seller_products", "id='$id'");
        $description = getData("description",  "seller_products", "id='$id'") ?? '';
        $isUnlimited = getData("unlimited_stock", "seller_products", "id='$id'");

        $savePercent = calculateSavePercent($mrp_price, $price);

        $hasAdvanced = getData("id", "seller_product_advanced_variants", "product_id='$product_id'");
        $hasBasic    = getData("id", "seller_product_variants",          "product_id='$product_id'");

        // ---- Cart from DB (same lookup used by Theme 9) ----
        // Important: customer_id is the cart cookie ID and product_id is seller_products.id.
        $cart = getCartVariantIds($cookie_id ?? null, $id, $db);
        $isMainInCart = !empty($cart['main']);

        $advVariants     = [];
        $anyAdvInCart    = false;
        $advIdsInCart    = [];
        if ($hasAdvanced) {
            $avStmt = readData("*", "seller_product_advanced_variants", "product_id='$product_id'");
            while ($av = $avStmt->fetch(PDO::FETCH_ASSOC)) {
                $colorId   = $av['color'] ?? '';
                $colorName = $colorId ? (getData("color_name", "product_colors", "id='$colorId'") ?? '') : '';
                $colorCode = $colorId ? (getData("color_code", "product_colors", "id='$colorId'") ?? '#ccc') : '#ccc';
                $inCart    = isset($cart['advanced_ids'][(string)$av['id']]) ? 1 : 0;
                if ($inCart) {
                    $anyAdvInCart   = true;
                    $advIdsInCart[] = (string)$av['id'];
                }
                $advVariants[] = [
                    'id'        => (string)$av['id'],
                    'size'      => $av['size'] ?? '',
                    'colorId'   => $colorId,
                    'color'     => $colorName,
                    'colorCode' => $colorCode,
                    'price'     => (float)$av['price'],
                    'mrp'       => (float)($av['mrp_price'] ?? 0),
                    'stock'     => (int)($av['stock'] ?? 0),
                    'unlimited' => (int)($av['unlimited_stock'] ?? 0),
                    'image'     => !empty($av['image']) ? UPLOADS_URL . $av['image'] : UPLOADS_URL . $image,
                    'inCart'    => $inCart,
                ];
            }
        }

        $basicVariants  = [];
        $anyBasicInCart = false;
        if ($hasBasic && !$hasAdvanced) {
            $basicVariants[] = [
                'id'        => 'main',
                'label'     => !empty($variation) ? $variation : $name,
                'price'     => (float)$price,
                'mrp'       => (float)$mrp_price,
                'stock'     => (int)$totalStocks,
                'unlimited' => (int)$isUnlimited,
                'image'     => UPLOADS_URL . $image,
                'inCart'    => $isMainInCart ? 1 : 0,
            ];
            $bvStmt = readData("*", "seller_product_variants", "product_id='$product_id'");
            while ($bv = $bvStmt->fetch(PDO::FETCH_ASSOC)) {
                $inCart = isset($cart['basic_ids'][(string)$bv['id']]) ? 1 : 0;
                if ($inCart) $anyBasicInCart = true;
                $basicVariants[] = [
                    'id'        => (string)$bv['id'],
                    'label'     => $bv['variation'] ?? $bv['name'] ?? 'Option',
                    'price'     => (float)$bv['price'],
                    'mrp'       => (float)($bv['mrp_price'] ?? 0),
                    'stock'     => (int)($bv['stock'] ?? 0),
                    'unlimited' => (int)($bv['unlimited_stock'] ?? 0),
                    'image'     => !empty($bv['image']) ? UPLOADS_URL . $bv['image'] : UPLOADS_URL . $image,
                    'inCart'    => $inCart,
                ];
            }
        }

        $variantJson = htmlspecialchars(json_encode([
            'advanced' => $advVariants,
            'basic'    => $basicVariants
        ]), ENT_QUOTES, 'UTF-8');

        // ---- Also bought ----
        $alsoBought = [];
        try {
            $abStmt = $db->prepare("
                SELECT id, name, slug, image, price, mrp_price
                FROM seller_products
                WHERE seller_id = ? AND store_id = ? AND status = 1
                  AND id != ?
                  AND (visibility = 'publish' OR (visibility = 'schedule' AND schedule_date <= NOW()))
                ORDER BY RAND() LIMIT 3
            ");
            $abStmt->execute([$sellerId, $storeId, $id]);
            while ($ab = $abStmt->fetch(PDO::FETCH_ASSOC)) {
                $alsoBought[] = [
                    'name'  => $ab['name'],
                    'slug'  => $ab['slug'],
                    'image' => UPLOADS_URL . $ab['image'],
                    'price' => (float)$ab['price'],
                    'mrp'   => (float)($ab['mrp_price'] ?? 0),
                ];
            }
        } catch (Throwable $e) {
        }

        $descB64  = base64_encode($description);
        $alsoJson = htmlspecialchars(json_encode($alsoBought), ENT_QUOTES, 'UTF-8');

        $escName  = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $escSlug  = htmlspecialchars($slug, ENT_QUOTES, 'UTF-8');
        $escImage = htmlspecialchars(UPLOADS_URL . $image, ENT_QUOTES, 'UTF-8');

        $inWishlist = false;
        if (function_exists('isLoggedIn') && isLoggedIn() && !empty($customerId)) {
            $inWishlist = getData("id", "customer_wishlists", "customer_id='$customerId' AND product_id='$id'") ? true : false;
        }

        /* ============================================================
           REFRESH-SAFE CART STATE (same logic as Theme 9)
           ============================================================ */
        $hasAnyVariant    = ($hasAdvanced || $hasBasic);
        $anyVariantInCart = ($anyAdvInCart || $anyBasicInCart);

        $showViewCartOnCard = false;
        if (!$hasAnyVariant && $isMainInCart) {
            $showViewCartOnCard = true;
        } elseif ($hasBasic && !$hasAdvanced && $isMainInCart) {
            $showViewCartOnCard = true;
        }

        $outOfStock = (!$hasAnyVariant && $totalStocks <= 0 && !$isUnlimited);

        $html  = '<article class="t18-card group min-w-0"';
        $html .= ' data-id="' . (int)$id . '"';
        $html .= ' data-product-id="' . htmlspecialchars($product_id) . '"';
        $html .= ' data-name="' . $escName . '"';
        $html .= ' data-slug="' . $escSlug . '"';
        $html .= ' data-image="' . $escImage . '"';
        $html .= ' data-price="' . (float)$price . '"';
        $html .= ' data-mrp="' . (float)$mrp_price . '"';
        $html .= ' data-desc-b64="' . $descB64 . '"';
        $html .= ' data-has-advanced="' . ($hasAdvanced ? '1' : '0') . '"';
        $html .= ' data-has-basic="' . ($hasBasic && !$hasAdvanced ? '1' : '0') . '"';
        $html .= ' data-primary="' . htmlspecialchars($primary_color) . '"';
        $html .= ' data-hover="' . htmlspecialchars($hover_color) . '"';
        $html .= ' data-variants="' . $variantJson . '"';
        $html .= ' data-in-cart="' . ($showViewCartOnCard ? '1' : '0') . '"';
        $html .= ' data-also-bought="' . $alsoJson . '">';

        // ---- MEDIA ----
        $html .= '<div class="t18-media">';
        $html .= '<div class="t18-bubbles" aria-hidden="true">';
        $html .= '<span class="t18-bubble bubble-bl"></span>';
        $html .= '</div>';

        $html .= '<a href="' . $storeUrl . 'product/' . $escSlug . '" class="t18-media-link">';
        $html .= '<img src="' . $escImage . '" alt="' . $escName . '" loading="lazy" class="t18-img">';
        $html .= '</a>';

        if ($savePercent > 0) {
            $html .= '<span class="t18-badge">-' . $savePercent . '% OFF</span>';
        }

        $wishClass = $inWishlist ? 'is-in' : '';
        $html .= '<button type="button" class="t18-wish handleWishlist ' . $wishClass . '"';
        $html .= ' data-id="' . (int)$id . '" aria-label="Wishlist" aria-pressed="' . ($inWishlist ? 'true' : 'false') . '">';
        $html .= '<i class="bi bi-heart"></i></button>';

        // Close the image area before the product information.
        $html .= '</div>';

        // ---- INFO ----
        $html .= '<div class="t18-info">';
        $html .= '<h3 class="t18-title"><a href="' . $storeUrl . 'product/' . $escSlug . '">' . $escName . '</a></h3>';
        $html .= '<div class="t18-rating"><i class="bi bi-star-fill"></i><span class="t18-rating-score">4.8</span><span class="t18-rating-count">(124)</span></div>';
        $html .= '<div class="t18-price-row">';
        $html .= '<span class="t18-price">' . currencyToSymbol($storeCurrency) . number_format($price, 2) . '</span>';
        if ($mrp_price && $mrp_price > $price) {
            $html .= '<span class="t18-mrp">' . currencyToSymbol($storeCurrency) . number_format($mrp_price, 2) . '</span>';
        }
        $html .= '</div>';

        /* ============================================================
           ADD-TO-CART / VIEW-CART BUTTON (refresh-safe)
           ============================================================ */
        $html .= '<div class="t18-actions t18-card-cart-row">';

        if ($showViewCartOnCard) {
            // Already in cart → View Cart
            $html .= '<a href="' . htmlspecialchars($storeUrl . 'cart', ENT_QUOTES, 'UTF-8') . '" class="t18-icon-btn t18-view-cart-link">';
            $html .= '<i class="bi bi-cart-check-fill"></i><span>View Cart</span></a>';
        } elseif ($outOfStock) {
            // Sold out
            $html .= '<button type="button" class="t18-icon-btn t18-cart-btn is-disabled" disabled aria-label="Sold out">';
            $html .= '<i class="bi bi-x-circle"></i><span class="t18-add-label">Sold Out</span></button>';
        } elseif ($hasAnyVariant) {
            // Variant product → open Quick View. If any variant already in cart,
            // show the tick (is-in-cart class) so user knows.
            $partialClass = $anyVariantInCart ? ' is-in-cart' : '';
            $html .= '<button type="button" class="t18-icon-btn t18-cart-btn openQuickView' . $partialClass . '" data-id="' . (int)$id . '" aria-label="Add to cart">';
            $html .= '<i class="bi bi-cart-plus-fill t18-icon-plus"></i>';
            $html .= '<i class="bi bi-cart-check-fill t18-icon-tick"></i><span class="t18-add-label">Add to Cart</span></button>';
        } else {
            // Simple product → direct add
            $html .= '<button type="button" class="t18-icon-btn t18-cart-btn t18-direct-add" data-id="' . (int)$id . '" aria-label="Add to cart">';
            $html .= '<i class="bi bi-cart-plus-fill t18-icon-plus"></i>';
            $html .= '<i class="bi bi-cart-check-fill t18-icon-tick"></i><span class="t18-add-label">Add to Cart</span></button>';
        }

        $html .= '</div>';
        $html .= '</div>';

        $html .= '</article>';
        return $html;
    }
}

/* ---------- Section heading ---------- */
if (!function_exists('getProductSectionHeading')) {
    function getProductSectionHeading($titleMain = 'Trending', $titleAccent = 'Now.', $viewAllUrl = '')
    {
        global $storeUrl;
        $url = !empty($viewAllUrl) ? $viewAllUrl : ($storeUrl . 'shop-all');
        ob_start(); ?>
        <div class="t18-head">
            <div class="t18-head-content">
                <div>
                    <span class="t18-eyebrow">Curated for you</span>
                    <h2 class="t18-heading"><?= htmlspecialchars($titleMain) ?> <span class="t18-heading-accent"><?= htmlspecialchars($titleAccent) ?></span></h2>
                    <p class="t18-subheading">Discover pieces everyone is loving.</p>
                </div>
                <a href="<?= htmlspecialchars($url) ?>" class="t18-view-all">View All <i class="bi bi-arrow-up-right"></i></a>
            </div>
        </div>
<?php return ob_get_clean();
    }
}
?>

<!-- ============================================================ -->
<!-- QUICK VIEW MODAL                                             -->
<!-- ============================================================ -->
<div id="t9QuickViewModal" class="t9-qv-modal" aria-hidden="true">
    <div class="t9-qv-backdrop"></div>
    <div class="t9-qv-dialog" role="dialog" aria-modal="true">
        <div class="t18-bubbles" aria-hidden="true">
            <span class="t18-bubble bubble-bl"></span>
        </div>

        <button class="t9-qv-close" id="t9QvClose" aria-label="Close">&times;</button>
        <div class="t9-qv-layout">
            <div class="t9-qv-gallery">
                <div class="t9-qv-thumbs" id="t9QvThumbs"></div>
                <div class="t9-qv-main-img">
                    <img id="t9QvImg" src="" alt="">
                    <span class="t9-qv-sale-badge">SALE</span>
                </div>
            </div>
            <div class="t9-qv-info">
                <h2 class="t9-qv-title" id="t9QvTitle"></h2>
                <div class="t9-qv-price-row">
                    <span class="t9-qv-mrp" id="t9QvMrp"></span>
                    <span class="t9-qv-price" id="t9QvPrice"></span>
                    <span class="t9-qv-save" id="t9QvSave"></span>
                </div>
                <div class="t9-qv-stock" id="t9QvStock"></div>
                <div class="t9-qv-field" id="t9QvColorField" style="display:none;">
                    <p class="t9-qv-label">Available Styles</p>
                    <div class="t9-qv-colors" id="t9QvColors"></div>
                </div>
                <div class="t9-qv-field" id="t9QvSizeField" style="display:none;">
                    <p class="t9-qv-label">Select Size</p>
                    <div class="t9-qv-sizes" id="t9QvSizes"></div>
                </div>
                <div class="t9-qv-field" id="t9QvBasicField" style="display:none;">
                    <p class="t9-qv-label">Available Styles</p>
                    <div class="t9-qv-basics" id="t9QvBasics"></div>
                </div>
                <button type="button" class="t9-qv-addbtn" id="t9QvAddBtn">ADD TO CART</button>
                <div class="t9-qv-acc">
                    <div class="t9-acc-item">
                        <button type="button" class="t9-acc-trigger" aria-expanded="false">
                            <span>DESCRIPTION</span>
                            <svg class="t9-acc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div class="t9-acc-panel">
                            <div class="t9-acc-inner">
                                <div id="t9QvDescHtml" class="t9-rich-desc"></div>
                            </div>
                        </div>
                    </div>
                    <div class="t9-acc-item" id="t9QvAlsoAccItem">
                        <button type="button" class="t9-acc-trigger" aria-expanded="false">
                            <span>CUSTOMERS ALSO BOUGHT</span>
                            <svg class="t9-acc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div class="t9-acc-panel">
                            <div class="t9-acc-inner">
                                <div id="t9QvAlsoGrid" class="t9-also-grid"></div>
                            </div>
                        </div>
                    </div>
                    <div class="t9-acc-item">
                        <button type="button" class="t9-acc-trigger" aria-expanded="false">
                            <span>SHIPPING INFORMATION</span>
                            <svg class="t9-acc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div class="t9-acc-panel">
                            <div class="t9-acc-inner">Shipping charges and estimated delivery times depend on your location. Check the final details during checkout.</div>
                        </div>
                    </div>
                    <div class="t9-acc-item">
                        <button type="button" class="t9-acc-trigger" aria-expanded="false">
                            <span>RETURNS &amp; EXCHANGES</span>
                            <svg class="t9-acc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div class="t9-acc-panel">
                            <div class="t9-acc-inner">Please refer to your store's return and exchange policy for eligibility and applicable conditions.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- CSS                                                          -->
<!-- ============================================================ -->
<style>
    :root {
        --t9-primary: <?= htmlspecialchars($primary_color) ?>;
        --t9-hover: <?= htmlspecialchars($hover_color) ?>;
    }

    /* ===================== SINGLE BUBBLE (BOTTOM-LEFT) ===================== */
    .t18-bubbles {
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        overflow: hidden;
        border-radius: inherit;
    }

    .t18-bubble {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle at 30% 25%,
                #ffffff 0%,
                color-mix(in srgb, var(--t9-primary) 45%, #ffffff) 55%,
                color-mix(in srgb, var(--t9-primary) 90%, #ffffff) 100%);
        box-shadow:
            0 12px 30px color-mix(in srgb, var(--t9-primary) 22%, transparent),
            inset 0 -6px 12px rgba(0, 0, 0, .06),
            inset 0 6px 12px rgba(255, 255, 255, .85);
        opacity: .55;
        will-change: transform;
        animation: t18BubbleFloat 9s ease-in-out infinite alternate;
    }

    .t18-bubble.bubble-bl {
        width: 88px;
        height: 88px;
        bottom: -30px;
        left: -26px;
        animation-delay: -3s;
        opacity: .55;
    }

    @keyframes t18BubbleFloat {
        0% {
            transform: translate(0, 0) scale(1);
        }

        50% {
            transform: translate(6px, -10px) scale(1.06);
        }

        100% {
            transform: translate(-4px, 8px) scale(0.96);
        }
    }

    @media (max-width: 640px) {
        .t18-bubble.bubble-bl {
            width: 60px;
            height: 60px;
            bottom: -20px;
            left: -18px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .t18-bubble {
            animation: none !important;
        }
    }

    /* ===================== SECTION HEADING ===================== */
    .t18-head {
        position: relative;
        padding: 4px 0 24px;
        margin-bottom: 20px;
    }

    .t18-head-content {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .t18-eyebrow {
        display: block;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 3px;
        text-transform: uppercase;
        background: linear-gradient(90deg, var(--t9-primary), var(--t9-hover));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .t18-heading {
        margin: 8px 0 0;
        font-size: 28px;
        font-weight: 900;
        letter-spacing: -1px;
        line-height: 1.1;
        color: #281333;
    }

    @media (min-width: 640px) {
        .t18-heading {
            font-size: 36px;
        }
    }

    .t18-heading-accent {
        background: linear-gradient(90deg, var(--t9-primary), var(--t9-hover));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .t18-subheading {
        margin: 8px 0 0;
        font-size: 13px;
        color: #6b7280;
    }

    .t18-view-all {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 700;
        color: var(--t9-primary);
        text-decoration: none;
        transition: color .2s ease;
        flex-shrink: 0;
    }

    .t18-view-all:hover {
        color: var(--t9-hover);
    }

    /* ===================== QUICK VIEW MODAL ===================== */
    .t9-qv-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
        visibility: hidden;
        opacity: 0;
        pointer-events: none;
        transition: opacity .3s ease, visibility 0s linear .3s
    }

    .t9-qv-modal.is-open {
        visibility: visible;
        opacity: 1;
        pointer-events: auto;
        transition: opacity .3s ease, visibility 0s linear 0s
    }

    .t9-qv-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, .55);
        backdrop-filter: blur(3px)
    }

    .t9-qv-dialog {
        position: relative;
        width: 100%;
        max-width: 1050px;
        max-height: 94vh;
        background: #fff;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .28);
        overflow: hidden;
        transform: translateY(20px) scale(.98);
        opacity: 0;
        transition: transform .35s cubic-bezier(.22, 1, .36, 1), opacity .35s ease;
        border-radius: 14px;
        isolation: isolate
    }

    .t9-qv-dialog .t18-bubbles {
        z-index: 0
    }

    .t9-qv-modal.is-open .t9-qv-dialog {
        transform: translateY(0) scale(1);
        opacity: 1
    }

    @media (min-width:640px) {
        .t9-qv-modal {
            padding: 20px
        }
    }

    .t9-qv-close {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 10;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        line-height: 1;
        background: #fff;
        border: 1px solid #e5e5e5;
        color: #171717;
        cursor: pointer;
        transition: all .2s ease;
        border-radius: 50%
    }

    .t9-qv-close:hover {
        background: var(--t9-primary);
        color: #fff;
        border-color: var(--t9-primary)
    }

    .t9-qv-layout {
        position: relative;
        z-index: 2;
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        padding: 16px;
        max-height: 94vh;
        overflow-y: auto
    }

    @media (min-width:768px) {
        .t9-qv-layout {
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            padding: 24px
        }
    }

    .t9-qv-gallery {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch;
        gap: 10px;
        min-width: 0;
        width: 100%
    }

    .t9-qv-gallery .t9-qv-main-img {
        order: 1 !important;
        display: block;
        width: 100%;
        min-width: 0;
        flex: 0 0 auto
    }

    .t9-qv-gallery .t9-qv-thumbs {
        order: 2 !important;
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: flex-start;
        justify-content: flex-start;
        gap: 8px;
        width: 100%;
        min-width: 0;
        max-width: 100%;
        height: auto;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        padding: 2px 1px 7px;
        flex: 0 0 auto;
        scrollbar-width: thin
    }

    .t9-qv-gallery .t9-qv-thumbs button {
        display: block;
        width: 58px !important;
        height: 72px !important;
        min-width: 58px !important;
        max-width: 58px;
        flex: 0 0 58px !important
    }

    @media (min-width:640px) {
        .t9-qv-gallery .t9-qv-thumbs button {
            width: 68px !important;
            height: 84px !important;
            min-width: 68px !important;
            max-width: 68px;
            flex-basis: 68px !important
        }
    }

    .t9-qv-thumbs button {
        border: 2px solid transparent;
        padding: 0;
        cursor: pointer;
        background: #f4f4f4;
        overflow: hidden;
        aspect-ratio: 3/4;
        transition: border-color .15s ease;
        border-radius: 4px;
        flex: 0 0 auto
    }

    .t9-qv-thumbs button.is-active {
        border-color: var(--t9-hover)
    }

    .t9-qv-thumbs img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block
    }

    .t9-qv-main-img {
        flex: 1;
        min-width: 0;
        background: #f4f4f4;
        overflow: hidden;
        position: relative;
        border-radius: 6px
    }

    .t9-qv-main-img img {
        width: 100%;
        aspect-ratio: 3/4;
        object-fit: cover;
        display: block
    }

    .t9-qv-sale-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: var(--t9-hover);
        color: #fff;
        padding: 4px 10px;
        font-size: 9px;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        border-bottom-left-radius: 6px
    }

    .t9-qv-info {
        min-width: 0
    }

    .t9-qv-title {
        margin: 0;
        padding-right: 34px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: .8px;
        line-height: 1.5;
        text-transform: uppercase;
        color: #171717
    }

    @media (min-width:640px) {
        .t9-qv-title {
            font-size: 16px
        }
    }

    .t9-qv-price-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
        font-size: 14px
    }

    .t9-qv-mrp {
        color: #a3a3a3;
        text-decoration: line-through
    }

    .t9-qv-price {
        font-weight: 700;
        color: #171717
    }

    .t9-qv-save {
        font-size: 10px;
        font-weight: 700;
        color: var(--t9-hover);
        letter-spacing: .5px
    }

    .t9-qv-stock {
        margin-top: 10px;
        font-size: 11px;
        font-weight: 600
    }

    .t9-qv-field {
        margin-top: 18px
    }

    .t9-qv-label {
        margin: 0 0 8px;
        font-size: 10px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #737373;
        font-weight: 600
    }

    .t9-qv-colors {
        display: flex;
        flex-wrap: wrap;
        gap: 8px
    }

    .t9-qv-color-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 2px solid #d4d4d4;
        padding: 0;
        cursor: pointer;
        transition: all .2s ease
    }

    .t9-qv-color-btn:hover {
        transform: scale(1.06)
    }

    .t9-qv-color-btn.is-active {
        box-shadow: 0 0 0 2px var(--t9-hover), 0 0 0 4px #fff;
        border-color: var(--t9-hover)
    }

    .t9-qv-sizes,
    .t9-qv-basics {
        display: flex;
        flex-wrap: wrap;
        gap: 6px
    }

    .t9-qv-size-btn,
    .t9-qv-basic-btn {
        min-width: 46px;
        padding: 8px 14px;
        border: 1px solid #d4d4d4;
        background: #fff;
        color: #171717;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .8px;
        text-transform: uppercase;
        cursor: pointer;
        transition: all .15s ease;
        border-radius: 4px
    }

    .t9-qv-size-btn:hover:not(:disabled),
    .t9-qv-basic-btn:hover:not(:disabled) {
        border-color: var(--t9-hover);
        color: var(--t9-primary)
    }

    .t9-qv-size-btn.is-active,
    .t9-qv-basic-btn.is-active {
        background: var(--t9-hover);
        color: #171717 !important;
        border-color: var(--t9-hover)
    }

    .t9-qv-size-btn:disabled,
    .t9-qv-basic-btn:disabled {
        opacity: .4;
        text-decoration: line-through;
        cursor: not-allowed
    }

    .t9-qv-addbtn {
        margin-top: 22px;
        width: 100%;
        padding: 16px;
        background: var(--t9-hover);
        color: #171717 !important;
        border: 1px solid var(--t9-hover);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        cursor: pointer;
        transition: background .2s ease, border-color .2s ease;
        border-radius: 4px
    }

    .t9-qv-addbtn:hover:not(:disabled) {
        background: var(--t9-primary);
        border-color: var(--t9-primary);
        color: #fff !important
    }

    .t9-qv-addbtn:disabled {
        opacity: 1;
        background: #f3f3f3;
        color: #9ca3af !important;
        border-color: #e5e5e5;
        cursor: not-allowed
    }

    .t9-qv-addbtn.is-in-cart {
        background: #ffe4e6;
        color: var(--t9-primary) !important;
        border-color: #fbcfe8;
        opacity: 1
    }

    .t9-qv-acc {
        margin-top: 22px;
        border-top: 1px solid #e5e5e5;
        border-bottom: 1px solid #e5e5e5
    }

    .t9-acc-item {
        border-bottom: 1px solid #e5e5e5
    }

    .t9-acc-item:last-child {
        border-bottom: 0
    }

    .t9-acc-trigger {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 0;
        background: none;
        border: 0;
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 1.3px;
        text-transform: uppercase;
        text-align: left;
        color: #171717
    }

    .t9-acc-icon {
        width: 14px;
        height: 14px;
        transition: transform .3s ease;
        flex-shrink: 0
    }

    .t9-acc-trigger[aria-expanded="true"] .t9-acc-icon {
        transform: rotate(180deg)
    }

    .t9-acc-panel {
        display: grid;
        grid-template-rows: 0fr;
        opacity: 0;
        transition: grid-template-rows .3s ease-in-out, opacity .3s ease-in-out;
        overflow: hidden
    }

    .t9-acc-panel>.t9-acc-inner {
        overflow: hidden
    }

    .t9-acc-trigger[aria-expanded="true"]+.t9-acc-panel {
        grid-template-rows: 1fr;
        opacity: 1
    }

    .t9-acc-inner {
        padding: 0 0 16px;
        font-size: 12px;
        line-height: 1.75;
        color: #525252;
        word-break: break-word
    }

    .t9-rich-desc {
        font-size: 12px;
        line-height: 1.75;
        color: #525252
    }

    .t9-rich-desc p {
        margin: 0 0 10px
    }

    .t9-rich-desc p:last-child {
        margin-bottom: 0
    }

    .t9-rich-desc strong,
    .t9-rich-desc b {
        font-weight: 700;
        color: #171717
    }

    .t9-rich-desc ul,
    .t9-rich-desc ol {
        padding-left: 18px;
        margin: 0 0 10px
    }

    .t9-rich-desc li {
        margin-bottom: 4px;
        list-style: disc
    }

    .t9-rich-desc a {
        color: var(--t9-hover);
        text-decoration: underline
    }

    .t9-rich-desc img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 8px 0
    }

    .t9-also-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px
    }

    .t9-also-card {
        display: block;
        text-decoration: none;
        color: inherit;
        border: 1px solid #eee;
        border-radius: 6px;
        overflow: hidden;
        transition: border-color .2s ease;
        background: #fff
    }

    .t9-also-card:hover {
        border-color: var(--t9-hover)
    }

    .t9-also-card img {
        width: 100%;
        aspect-ratio: 1;
        object-fit: cover;
        display: block
    }

    .t9-also-info {
        padding: 10px 8px
    }

    .t9-also-name {
        font-size: 11px;
        line-height: 1.35;
        color: #333;
        margin-bottom: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 28px
    }

    .t9-also-price {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap
    }

    .t9-also-price-current {
        font-size: 12px;
        font-weight: 700;
        color: #171717
    }

    .t9-also-price-mrp {
        font-size: 10px;
        color: #a3a3a3;
        text-decoration: line-through
    }

    .t9-toast {
        position: fixed;
        left: 50%;
        bottom: 22px;
        transform: translateX(-50%) translateY(20px);
        background: #171717;
        color: #fff;
        padding: 14px 24px;
        font-size: 13px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .28);
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transition: all .3s ease;
        max-width: 90%;
        border-radius: 4px
    }

    .t9-toast.is-open {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0)
    }

    /* ===================== THEME 18 CARD ===================== */
    .t18-card {
        --t18-primary: <?= htmlspecialchars($primary_color) ?>;
        --t18-hover: <?= htmlspecialchars($hover_color) ?>;
        display: flex;
        flex-direction: column;
        min-width: 0;
        position: relative;
        background: transparent;
        font-family: inherit;
        transition: transform .3s ease;
    }

    .t18-card:hover {
        transform: translateY(-4px)
    }

    /* MEDIA — DB COLOR GRADIENT (not hard-coded rose) */
    .t18-media {
        position: relative;
        overflow: hidden;
        aspect-ratio: 4/5;
        background: linear-gradient(135deg,
                color-mix(in srgb, var(--t18-primary) 12%, #ffffff) 0%,
                color-mix(in srgb, var(--t18-hover) 14%, #ffffff) 100%);
        border-radius: 20px;
        isolation: isolate;
        box-shadow:
            0 10px 25px -12px color-mix(in srgb, var(--t18-primary) 22%, transparent),
            0 4px 12px -6px color-mix(in srgb, var(--t18-primary) 12%, transparent),
            inset 0 0 0 1px color-mix(in srgb, var(--t18-primary) 18%, transparent);
        transition: box-shadow .35s ease;
    }

    .t18-card:hover .t18-media {
        box-shadow:
            0 20px 40px -16px color-mix(in srgb, var(--t18-primary) 40%, transparent),
            0 8px 18px -8px color-mix(in srgb, var(--t18-hover) 25%, transparent),
            inset 0 0 0 1px color-mix(in srgb, var(--t18-primary) 28%, transparent);
    }

    @media (min-width:640px) {
        .t18-media {
            border-radius: 28px
        }
    }

    .t18-media .t18-bubbles {
        z-index: 1
    }

    .t18-media-link {
        display: block;
        width: 100%;
        height: 100%;
        position: relative;
        z-index: 2
    }

    .t18-img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .7s ease
    }

    .t18-card:hover .t18-img {
        transform: scale(1.08)
    }

    .t18-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 4;
        background: var(--t18-primary);
        color: #fff;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .4px;
        text-transform: uppercase;
        box-shadow: 0 4px 12px color-mix(in srgb, var(--t18-primary) 35%, transparent);
    }

    @media (min-width:640px) {
        .t18-badge {
            font-size: 10px;
            padding: 6px 12px
        }
    }

    .t18-wish {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 5;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .85);
        border: 0;
        border-radius: 50%;
        box-shadow: 0 6px 16px rgba(0, 0, 0, .10);
        color: #6b7280;
        cursor: pointer;
        transition: all .25s ease;
        backdrop-filter: blur(8px);
    }

    .t18-wish i {
        font-size: 16px;
        line-height: 1;
        transition: all .2s ease
    }

    .t18-wish:hover {
        color: var(--t18-hover);
        transform: scale(1.1)
    }

    .t18-wish.is-in {
        color: #e11d48
    }

    .t18-wish.is-in i::before {
        content: "\F415"
    }

    .t18-actions {
        position: absolute;
        bottom: 12px;
        right: 12px;
        z-index: 6;
        display: flex;
        flex-direction: column;
        gap: 10px;
        opacity: 0;
        transform: translateY(10px);
        transition: opacity .3s ease, transform .3s ease;
    }

    .t18-card:hover .t18-actions {
        opacity: 1;
        transform: translateY(0)
    }

    @media (max-width:768px) {
        .t18-actions {
            opacity: 1;
            transform: none
        }
    }

    .t18-icon-btn {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #fff;
        color: var(--t18-primary);
        border: 1px solid rgba(0, 0, 0, .04);
        box-shadow: 0 8px 18px rgba(0, 0, 0, .12), 0 3px 6px rgba(0, 0, 0, .06);
        cursor: pointer;
        transition: all .25s ease;
        padding: 0;
    }

    .t18-icon-btn i {
        font-size: 18px;
        line-height: 1
    }

    .t18-icon-btn:hover {
        background: var(--t18-primary);
        color: #fff;
        transform: scale(1.08);
        box-shadow: 0 12px 24px color-mix(in srgb, var(--t18-primary) 40%, transparent),
            0 4px 10px rgba(0, 0, 0, .10);
    }

    .t18-icon-btn.is-disabled {
        background: #f3f3f3;
        color: #9ca3af;
        cursor: not-allowed;
        box-shadow: none
    }

    .t18-icon-btn.is-disabled:hover {
        background: #f3f3f3;
        color: #9ca3af;
        transform: none;
        box-shadow: none
    }

    .t18-cart-btn {
        position: relative
    }

    .t18-cart-btn .t18-icon-tick {
        display: none;
        color: #16a34a
    }

    .t18-cart-btn.is-in-cart {
        background: #dcfce7;
        color: #16a34a;
        border-color: #86efac;
        cursor: default
    }

    .t18-cart-btn.is-in-cart .t18-icon-plus {
        display: none
    }

    .t18-cart-btn.is-in-cart .t18-icon-tick {
        display: inline-flex
    }

    .t18-cart-btn.is-in-cart:hover {
        background: #dcfce7;
        color: #16a34a;
        transform: none;
        box-shadow: 0 8px 18px rgba(0, 0, 0, .12)
    }

    .t18-info {
        padding: 14px 4px 0;
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0
    }

    .t18-title {
        margin: 0;
        font-size: 13px;
        line-height: 1.45;
        font-weight: 700;
        color: #111827;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        letter-spacing: .1px;
    }

    @media (min-width:640px) {
        .t18-title {
            font-size: 14px
        }
    }

    .t18-title a {
        color: inherit;
        text-decoration: none
    }

    .t18-title a:hover {
        color: var(--t18-hover)
    }

    .t18-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 10px
    }

    .t18-rating i {
        color: #fbbf24;
        font-size: 12px
    }

    .t18-rating-score {
        font-weight: 700;
        color: #111827
    }

    .t18-rating-count {
        color: #9ca3af
    }

    .t18-price-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 2px
    }

    .t18-price {
        font-size: 15px;
        font-weight: 900;
        color: #111827;
        letter-spacing: -.2px
    }

    @media (min-width:640px) {
        .t18-price {
            font-size: 17px
        }
    }

    .t18-mrp {
        font-size: 12px;
        color: #9ca3af;
        text-decoration: line-through
    }

    /* Bottom-right glow — uses DB colors */
    .t18-card::after {
        content: "";
        position: absolute;
        right: -40px;
        bottom: -40px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: radial-gradient(circle,
                color-mix(in srgb, var(--t18-primary) 28%, transparent) 0%,
                transparent 70%);
        filter: blur(28px);
        pointer-events: none;
        z-index: 0;
        opacity: .55;
        transition: opacity .4s ease;
    }

    .t18-card:hover::after {
        opacity: .85
    }

    @media (max-width:640px) {
        .t18-wish {
            width: 32px;
            height: 32px;
            top: 10px;
            right: 10px
        }

        .t18-wish i {
            font-size: 14px
        }

        .t18-badge {
            font-size: 9px;
            padding: 4px 9px;
            top: 10px;
            left: 10px
        }

        .t18-actions {
            bottom: 10px;
            right: 10px;
            gap: 8px
        }

        .t18-icon-btn {
            width: 36px;
            height: 36px
        }

        .t18-icon-btn i {
            font-size: 16px
        }

        .t18-title {
            font-size: 12px
        }

        .t18-price {
            font-size: 14px
        }

        .t18-mrp {
            font-size: 11px
        }
    }

    /* ==========================================================
   THEME 18 — 3D SHAPE PRODUCT CAROUSEL REDESIGN
   Retains existing cart AJAX, quick view and wishlist handlers.
   ========================================================== */
    .t18-card {
        background: #fff !important;
        border-radius: 13px !important;
        overflow: hidden;
        border: 1px solid color-mix(in srgb, var(--t18-primary) 15%, #e5e7eb);
        transform: none !important;
        padding-bottom: 52px;
        height: 100%;
        box-shadow: none !important;
    }

    .t18-card::after {
        display: none !important
    }

    .t18-media {
        aspect-ratio: 1/1 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .t18-card:hover .t18-media {
        box-shadow: none !important
    }

    .t18-img {
        object-fit: contain !important;
        padding: 10px;
    }

    .t18-card:hover .t18-img {
        transform: scale(1.035)
    }

    .t18-info {
        padding: 12px 13px 5px !important;
        gap: 7px !important;
    }

    .t18-title {
        font-size: 14px !important;
        line-height: 1.35 !important;
        display: block !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .t18-rating {
        display: none !important
    }

    .t18-price {
        font-size: 14px !important
    }

    .t18-badge {
        top: 10px;
        left: 10px;
        padding: 5px 8px;
        font-size: 9px
    }

    .t18-actions {
        position: absolute !important;
        left: 12px !important;
        right: 12px !important;
        bottom: 10px !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center;
        justify-content: center;
        opacity: 1 !important;
        transform: none !important;
        gap: 0 !important;
        pointer-events: none;
    }

    .t18-actions .openQuickView:not(.t18-cart-btn) {
        display: none !important
    }

    .t18-actions .t18-cart-btn {
        pointer-events: auto;
        width: auto !important;
        height: 34px !important;
        min-width: 130px;
        max-width: 100%;
        padding: 0 16px !important;
        border-radius: 100px !important;
        display: inline-flex !important;
        gap: 8px;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: color-mix(in srgb, var(--t18-primary) 65%, #30251c) !important;
        border: 0 !important;
        box-shadow: none !important;
        font-size: 12px;
        font-weight: 700;
    }

    .t18-actions .t18-cart-btn i {
        font-size: 15px !important
    }

    .t18-actions .t18-cart-btn:hover:not(:disabled) {
        background: var(--t18-hover) !important;
        transform: translateY(-2px)
    }

    .t18-actions .t18-cart-btn.is-in-cart {
        background: color-mix(in srgb, var(--t18-primary) 12%, #fff) !important;
        color: var(--t18-primary) !important;
        border: 1px solid var(--t18-primary) !important;
    }

    .t18-actions .t18-cart-btn.is-disabled {
        background: #e5e7eb !important;
        color: #6b7280 !important;
    }

    .t18-wish {
        top: 9px !important;
        right: 9px !important;
        background: #fff !important;
    }

    .t18-card .t18-bubbles {
        pointer-events: none
    }

    .t18-product-carousel-section {
        max-width: 1450px;
        margin: 35px auto;
        padding: 0 16px;
        position: relative;
        --t18-primary: var(--t9-primary);
        --t18-hover: var(--t9-hover);
    }

    .t18-carousel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 16px
    }

    .t18-carousel-title {
        margin: 0;
        font-size: clamp(21px, 2.6vw, 30px);
        font-weight: 800;
        color: #20202a
    }

    .t18-carousel-view {
        font-size: 13px;
        color: #4b5563;
        text-decoration: none
    }

    .t18-carousel-shell {
        position: relative;
        border: 1px solid color-mix(in srgb, var(--t9-primary) 20%, #c7c7c7);
        border-radius: 16px;
        background: color-mix(in srgb, var(--t9-primary) 5%, #f6f6f6);
        padding: 22px 32px;
        isolation: isolate;
        overflow: hidden
    }

    .t18-carousel-shape {
        position: absolute;
        pointer-events: none;
        z-index: -1;
        opacity: .55;
        filter: blur(.5px);
        border-radius: 50%;
        background: radial-gradient(circle at 30% 25%, #fff 0%, color-mix(in srgb, var(--t9-primary) 35%, #fff) 45%, color-mix(in srgb, var(--t9-hover) 65%, #fff) 100%);
        box-shadow: inset -9px -12px 20px color-mix(in srgb, var(--t9-primary) 15%, transparent), 0 12px 26px color-mix(in srgb, var(--t9-primary) 15%, transparent);
        animation: t18ShapeDrift 8s ease-in-out infinite alternate;
    }

    .t18-carousel-shape.a {
        width: 120px;
        height: 120px;
        top: -65px;
        left: 24%
    }

    .t18-carousel-shape.b {
        width: 160px;
        height: 160px;
        bottom: -95px;
        right: 12%;
        animation-delay: -4s
    }

    @keyframes t18ShapeDrift {
        from {
            transform: translate3d(0, 0, 0) rotate(-8deg)
        }

        to {
            transform: translate3d(14px, -13px, 0) rotate(10deg)
        }
    }

    .t18-carousel-track {
        display: flex !important;
        gap: 16px;
        overflow-x: auto;
        overflow-y: hidden;
        scroll-snap-type: x mandatory;
        scroll-behavior: smooth;
        scrollbar-width: none;
        overscroll-behavior-inline: contain;
        padding: 2px 0 8px;
    }

    .t18-carousel-track::-webkit-scrollbar {
        display: none
    }

    .t18-carousel-track>.t18-card {
        flex: 0 0 calc((100% - 64px)/5) !important;
        width: calc((100% - 64px)/5) !important;
        scroll-snap-align: start
    }

    .t18-carousel-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #334155;
        display: grid;
        place-items: center;
        cursor: pointer;
    }

    .t18-carousel-nav.prev {
        left: 9px
    }

    .t18-carousel-nav.next {
        right: 9px
    }

    .t18-carousel-nav:disabled {
        opacity: .35;
        cursor: default
    }

    @media(max-width:1100px) {
        .t18-carousel-track>.t18-card {
            flex-basis: calc((100% - 48px)/4) !important;
            width: calc((100% - 48px)/4) !important
        }
    }

    @media(max-width:800px) {
        .t18-carousel-track>.t18-card {
            flex-basis: calc((100% - 16px)/2.4) !important;
            width: calc((100% - 16px)/2.4) !important
        }
    }

    @media(max-width:540px) {
        .t18-product-carousel-section {
            padding: 0 12px
        }

        .t18-carousel-shell {
            padding: 12px 10px
        }

        .t18-carousel-track {
            gap: 10px
        }

        .t18-carousel-track>.t18-card {
            flex-basis: calc((100% - 10px)/2.05) !important;
            width: calc((100% - 10px)/2.05) !important
        }

        .t18-carousel-nav {
            width: 30px;
            height: 30px
        }

        .t18-carousel-nav.prev {
            left: 3px
        }

        .t18-carousel-nav.next {
            right: 3px
        }

        .t18-title {
            font-size: 11px !important
        }

        .t18-info {
            padding: 9px !important
        }

        .t18-price {
            font-size: 12px !important
        }

        .t18-actions .t18-cart-btn {
            min-width: 0;
            font-size: 10px;
            padding: 0 9px !important;
            height: 30px !important
        }

        .t18-card {
            padding-bottom: 47px
        }

        .t18-wish {
            width: 30px !important;
            height: 30px !important
        }
    }

    @media(prefers-reduced-motion:reduce) {
        .t18-carousel-shape {
            animation: none
        }

        .t18-carousel-track {
            scroll-behavior: auto
        }
    }


    /* ============================================================
   THEME 18 — FINAL PRODUCT CARD: CART BUTTON BELOW PRICE
   ============================================================ */
    .t18-card {
        display: flex !important;
        flex-direction: column !important;
        height: 100%;
        padding: 0 !important;
        overflow: hidden !important;
        background: #fff !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        border-radius: 10px !important;
        box-shadow: none !important;
    }

    .t18-card::after {
        display: none !important;
    }

    .t18-media {
        position: relative !important;
        flex: 0 0 auto;
        width: calc(100% - 14px);
        margin: 7px 7px 0;
        aspect-ratio: 1 / 1 !important;
        overflow: hidden !important;
        border-radius: 11px !important;
        background: color-mix(in srgb, var(--t18-primary) 5%, #f8f8f8);
        box-shadow: none !important;
    }

    .t18-media-link {
        position: absolute;
        inset: 0;
        z-index: 1;
    }

    .t18-img {
        width: 100%;
        height: 100%;
        object-fit: cover !important;
        padding: 0 !important;
    }

    .t18-wish {
        top: 9px !important;
        right: 9px !important;
        z-index: 5;
        width: 35px !important;
        height: 35px !important;
        background: #fff !important;
        color: #334155 !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .12) !important;
    }

    .t18-actions {
        position: static !important;
        inset: auto !important;
        width: 100% !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 8px 0 0 !important;
        padding: 0 !important;
        opacity: 1 !important;
        visibility: visible !important;
        transform: none !important;
        pointer-events: auto !important;
        transition: none !important;
    }

    .t18-actions>.openQuickView:not(.t18-cart-btn) {
        display: none !important;
    }

    .t18-actions .t18-cart-btn {
        position: static !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: none !important;
        height: 37px !important;
        min-height: 37px !important;
        padding: 0 16px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: var(--t18-primary) !important;
        color: #fff !important;
        box-shadow: none !important;
        white-space: nowrap;
        font-size: 13px !important;
        font-weight: 600 !important;
        line-height: 1 !important;
        cursor: pointer;
    }

    .t18-actions .t18-view-cart-link {
        pointer-events: auto;
        width: auto !important;
        min-width: 130px;
        max-width: 100%;
        min-height: 34px;
        padding: 0 16px !important;
        border-radius: 100px !important;
        display: inline-flex !important;
        gap: 8px;
        align-items: center;
        justify-content: center;
        background: var(--t18-hover) !important;
        color: #fff !important;
        border: 0 !important;
        box-shadow: none !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        text-decoration: none !important;
        white-space: nowrap;
    }

    .t18-actions .t18-view-cart-link:hover {
        background: var(--t18-primary) !important;
        color: #fff !important;
        transform: translateY(-2px);
    }

    .t18-actions .t18-view-cart-link i {
        font-size: 15px !important;
        color: #fff !important;
    }

    .t18-actions .t18-cart-btn i {
        display: inline-block !important;
        color: #fff !important;
        font-size: 15px !important;
        line-height: 1;
    }

    .t18-actions .t18-cart-btn .t18-icon-tick {
        display: none !important;
    }

    .t18-actions .t18-cart-btn.is-in-cart .t18-icon-plus {
        display: none !important;
    }

    .t18-actions .t18-cart-btn.is-in-cart .t18-icon-tick {
        display: inline-block !important;
    }

    .t18-actions .t18-cart-btn.is-in-cart {
        background: var(--t18-hover) !important;
        color: #fff !important;
    }

    .t18-actions .t18-cart-btn.is-disabled {
        background: #777 !important;
        color: #fff !important;
        cursor: not-allowed;
    }

    .t18-add-label {
        display: inline !important;
        color: #fff !important;
        font-size: 13px !important;
    }

    .t18-info {
        display: flex !important;
        flex: 1 1 auto;
        flex-direction: column !important;
        align-items: stretch !important;
        padding: 13px 14px 16px !important;
        gap: 8px !important;
        background: #fff !important;
        min-width: 0;
    }

    .t18-card-cart-row {
        margin-top: 8px !important;
        padding-top: 11px !important;
        border-top: 1px solid color-mix(in srgb, var(--t18-primary) 18%, #e5e7eb);
    }

    .t18-title {
        margin: 0 !important;
        font-size: 14px !important;
        line-height: 1.35 !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .t18-rating {
        display: none !important;
    }

    .t18-price-row {
        display: flex;
        align-items: center;
        gap: 9px;
        flex-wrap: wrap;
    }

    .t18-price {
        font-size: 14px !important;
        font-weight: 700 !important;
    }

    .t18-mrp {
        font-size: 12px !important;
    }

    .t18-badge {
        top: 10px !important;
        left: 10px !important;
        z-index: 4;
    }

    /* D-shaped, rounded horizontal scrollbar for the carousel. */
    .t18-carousel-track {
        scrollbar-width: thin !important;
        scrollbar-color: var(--t9-primary) color-mix(in srgb, var(--t9-primary) 13%, #e5e7eb) !important;
        padding: 2px 0 13px !important;
        scroll-behavior: smooth;
    }

    .t18-carousel-track::-webkit-scrollbar {
        display: block !important;
        height: 8px !important;
    }

    .t18-carousel-track::-webkit-scrollbar-track {
        background: color-mix(in srgb, var(--t9-primary) 13%, #e5e7eb) !important;
        border-radius: 999px !important;
        margin: 0 3px !important;
    }

    .t18-carousel-track::-webkit-scrollbar-thumb {
        background: linear-gradient(90deg, var(--t9-primary), var(--t9-hover)) !important;
        border: 1px solid rgba(255, 255, 255, .8) !important;
        border-radius: 999px !important;
        min-width: 42px;
    }

    .t18-carousel-track::-webkit-scrollbar-thumb:hover {
        background: var(--t9-hover) !important;
    }

    @media (max-width: 768px) {
        .t18-info {
            padding: 10px 10px 12px !important;
            gap: 6px !important;
        }

        .t18-card-cart-row {
            margin-top: 6px !important;
            padding-top: 8px !important;
        }

        .t18-title {
            font-size: 11px !important;
        }

        .t18-price {
            font-size: 12px !important;
        }

        .t18-mrp {
            font-size: 10px !important;
        }

        .t18-actions {
            margin-top: 7px !important;
        }

        .t18-actions .t18-cart-btn {
            height: 31px !important;
            min-height: 31px !important;
            padding: 0 9px !important;
            gap: 5px !important;
            font-size: 10px !important;
        }

        .t18-actions .t18-cart-btn i {
            font-size: 12px !important;
        }

        .t18-add-label {
            font-size: 10px !important;
        }
    }

    @media (max-width: 540px) {
        .t18-carousel-track {
            padding-bottom: 12px !important;
        }

        .t18-carousel-track::-webkit-scrollbar {
            height: 7px !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .t18-carousel-track {
            scroll-behavior: auto !important;
        }

        .t18-carousel-shape {
            animation: none !important;
        }
    }
</style>

<!-- ============================================================ -->
<!-- JS                                                           -->
<!-- ============================================================ -->
<script>
    (function() {
        'use strict';
        if (window.__t9QvLoaded) return;
        window.__t9QvLoaded = true;

        const CURRENCY = <?= json_encode(currencyToSymbol($storeCurrency)) ?>;
        const STORE_URL = <?= json_encode($storeUrl) ?>;
        const ADD_URL = 'shop/ajax/add-to-cart.php';
        const DB_PRIMARY = <?= json_encode($primary_color) ?>;
        const DB_HOVER = <?= json_encode($hover_color) ?>;

        document.documentElement.style.setProperty('--t9-primary', DB_PRIMARY);
        document.documentElement.style.setProperty('--t9-hover', DB_HOVER);

        const modal = document.getElementById('t9QuickViewModal');
        if (!modal) return;

        const elImg = document.getElementById('t9QvImg');
        const elThumbs = document.getElementById('t9QvThumbs');
        const elTitle = document.getElementById('t9QvTitle');
        const elPrice = document.getElementById('t9QvPrice');
        const elMrp = document.getElementById('t9QvMrp');
        const elSave = document.getElementById('t9QvSave');
        const elStock = document.getElementById('t9QvStock');
        const elDesc = document.getElementById('t9QvDescHtml');
        const elAlsoAccItem = document.getElementById('t9QvAlsoAccItem');
        const elAlsoGrid = document.getElementById('t9QvAlsoGrid');
        const elColors = document.getElementById('t9QvColors');
        const elSizes = document.getElementById('t9QvSizes');
        const elBasics = document.getElementById('t9QvBasics');
        const elColorFld = document.getElementById('t9QvColorField');
        const elSizeFld = document.getElementById('t9QvSizeField');
        const elBasicFld = document.getElementById('t9QvBasicField');
        const elAddBtn = document.getElementById('t9QvAddBtn');

        let state = {
            product: null,
            variants: {
                advanced: [],
                basic: []
            },
            selectedColorId: null,
            selectedVariant: null
        };

        function fmt(n) {
            return CURRENCY + Number(n || 0).toLocaleString();
        }

        function toast(msg, isErr) {
            let t = document.getElementById('t9Toast');
            if (!t) {
                t = document.createElement('div');
                t.id = 't9Toast';
                t.className = 't9-toast';
                document.body.appendChild(t);
            }
            t.textContent = msg;
            t.style.background = isErr ? '#ef4444' : DB_PRIMARY;
            t.classList.add('is-open');
            clearTimeout(t._timer);
            t._timer = setTimeout(() => t.classList.remove('is-open'), 2600);
        }

        function decodeB64(encoded) {
            if (!encoded) return '';
            try {
                return decodeURIComponent(escape(atob(encoded)));
            } catch (e) {
                try {
                    return atob(encoded);
                } catch (e2) {
                    return '';
                }
            }
        }

        function renderQuickViewThumbs(images, activeImage) {
            elThumbs.innerHTML = '';
            const uniqueImages = [...new Set((images || []).filter(Boolean))];
            if (!uniqueImages.length) return;
            uniqueImages.forEach((src, index) => {
                const thumbBtn = document.createElement('button');
                thumbBtn.type = 'button';
                thumbBtn.className = (src === activeImage || (!activeImage && index === 0)) ? 'is-active' : '';
                thumbBtn.setAttribute('aria-label', 'View product image ' + (index + 1));
                const thumbImg = document.createElement('img');
                thumbImg.src = src;
                thumbImg.alt = '';
                thumbBtn.appendChild(thumbImg);
                thumbBtn.addEventListener('click', () => {
                    elImg.src = src;
                    elThumbs.querySelectorAll('button').forEach(b => b.classList.remove('is-active'));
                    thumbBtn.classList.add('is-active');
                });
                elThumbs.appendChild(thumbBtn);
            });
        }

        function setActiveQuickViewThumb(src) {
            elThumbs.querySelectorAll('button').forEach(btn => {
                const img = btn.querySelector('img');
                btn.classList.toggle('is-active', !!img && img.src === new URL(src, window.location.href).href);
            });
        }

        function openQuickView(card) {
            if (!card) return;
            state.product = {
                id: card.dataset.id,
                name: card.dataset.name,
                slug: card.dataset.slug,
                image: card.dataset.image,
                price: parseFloat(card.dataset.price) || 0,
                mrp: parseFloat(card.dataset.mrp) || 0,
                descB64: card.dataset.descB64 || '',
                hasAdvanced: card.dataset.hasAdvanced === '1',
                hasBasic: card.dataset.hasBasic === '1',
            };
            try {
                state.variants = JSON.parse(card.dataset.variants || '{"advanced":[],"basic":[]}');
            } catch (e) {
                state.variants = {
                    advanced: [],
                    basic: []
                };
            }
            let alsoBought = [];
            try {
                alsoBought = JSON.parse(card.dataset.alsoBought || '[]');
            } catch (e) {
                alsoBought = [];
            }
            state.selectedColorId = null;
            state.selectedVariant = null;
            const p = state.product;

            elImg.src = p.image;
            elImg.alt = p.name;
            elTitle.textContent = p.name;
            elPrice.textContent = fmt(p.price);
            if (p.mrp > p.price) {
                elMrp.textContent = fmt(p.mrp);
                elMrp.style.display = '';
                const off = Math.round(((p.mrp - p.price) / p.mrp) * 100);
                elSave.textContent = 'SAVE ' + off + '%';
                elSave.style.display = '';
            } else {
                elMrp.style.display = 'none';
                elSave.style.display = 'none';
            }
            elStock.innerHTML = '';

            const descHTML = decodeB64(p.descB64).trim();
            elDesc.innerHTML = descHTML || '<p style="color:#999;">No description available.</p>';

            elAlsoGrid.innerHTML = '';
            if (alsoBought.length > 0) {
                alsoBought.forEach(ab => {
                    const link = document.createElement('a');
                    link.href = STORE_URL + 'product/' + ab.slug;
                    link.className = 't9-also-card';
                    link.innerHTML =
                        '<img src="' + ab.image + '" alt="' + ab.name + '">' +
                        '<div class="t9-also-info"><p class="t9-also-name">' + ab.name + '</p>' +
                        '<div class="t9-also-price"><span class="t9-also-price-current">' + fmt(ab.price) + '</span>' +
                        (ab.mrp > ab.price ? '<span class="t9-also-price-mrp">' + fmt(ab.mrp) + '</span>' : '') +
                        '</div></div>';
                    elAlsoGrid.appendChild(link);
                });
                elAlsoAccItem.style.display = '';
            } else {
                elAlsoAccItem.style.display = 'none';
            }

            const galleryImages = [p.image]
                .concat((state.variants.advanced || []).map(v => v.image))
                .concat((state.variants.basic || []).map(v => v.image));
            renderQuickViewThumbs(galleryImages, p.image);

            document.querySelectorAll('.t9-acc-trigger').forEach(t => t.setAttribute('aria-expanded', 'false'));
            elColorFld.style.display = 'none';
            elSizeFld.style.display = 'none';
            elBasicFld.style.display = 'none';
            elColors.innerHTML = '';
            elSizes.innerHTML = '';
            elBasics.innerHTML = '';
            elAddBtn.disabled = true;
            elAddBtn.classList.remove('is-in-cart');
            elAddBtn.textContent = 'SELECT OPTIONS';

            if (p.hasAdvanced && state.variants.advanced.length) buildAdvanced();
            else if (p.hasBasic && state.variants.basic.length) buildBasic();
            else {
                state.selectedVariant = {
                    variantId: '',
                    variantType: 'main',
                    price: p.price,
                    mrp: p.mrp,
                    image: p.image,
                    stock: 999,
                    unlimited: 1,
                    inCart: card.dataset.inCart === '1'
                };
                updatePriceAndStock();
            }
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function buildAdvanced() {
            const grouped = {};
            state.variants.advanced.forEach(v => {
                const cid = v.colorId || 'default';
                if (!grouped[cid]) grouped[cid] = {
                    colorId: cid,
                    name: v.color || 'Style',
                    code: v.colorCode || '#ccc',
                    sizes: []
                };
                grouped[cid].sizes.push(v);
            });
            elColorFld.style.display = '';
            elSizeFld.style.display = '';
            const colorKeys = Object.keys(grouped);
            colorKeys.forEach((cid, idx) => {
                const c = grouped[cid];
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 't9-qv-color-btn' + (idx === 0 ? ' is-active' : '');
                btn.style.background = c.code;
                btn.title = c.name;
                btn.addEventListener('click', () => {
                    elColors.querySelectorAll('.t9-qv-color-btn').forEach(b => b.classList.remove('is-active'));
                    btn.classList.add('is-active');
                    state.selectedColorId = cid;
                    renderSizes(c);
                });
                elColors.appendChild(btn);
            });
            if (colorKeys.length) {
                state.selectedColorId = colorKeys[0];
                renderSizes(grouped[colorKeys[0]]);
            }
        }

        function renderSizes(colorData) {
            elSizes.innerHTML = '';
            state.selectedVariant = null;
            elAddBtn.disabled = true;
            elAddBtn.textContent = 'SELECT SIZE';
            let firstAvail = null;
            colorData.sizes.forEach(v => {
                const out = (v.stock <= 0 && v.unlimited != 1);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 't9-qv-size-btn';
                btn.textContent = v.size || 'One Size';
                if (out) btn.disabled = true;
                btn.addEventListener('click', () => {
                    elSizes.querySelectorAll('.t9-qv-size-btn').forEach(b => b.classList.remove('is-active'));
                    btn.classList.add('is-active');
                    state.selectedVariant = {
                        variantId: v.id,
                        variantType: 'advanced',
                        price: v.price,
                        mrp: v.mrp,
                        image: v.image,
                        stock: v.stock,
                        unlimited: v.unlimited,
                        inCart: v.inCart
                    };
                    updatePriceAndStock();
                });
                elSizes.appendChild(btn);
                if (!firstAvail && !out) firstAvail = btn;
            });
            if (firstAvail) firstAvail.click();
        }

        function buildBasic() {
            elBasicFld.style.display = '';
            state.variants.basic.forEach((v, idx) => {
                const out = (v.stock <= 0 && v.unlimited != 1);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 't9-qv-basic-btn' + (idx === 0 ? ' is-active' : '');
                btn.textContent = v.label || 'Option';
                if (out) btn.disabled = true;
                btn.addEventListener('click', () => {
                    elBasics.querySelectorAll('.t9-qv-basic-btn').forEach(b => b.classList.remove('is-active'));
                    btn.classList.add('is-active');
                    state.selectedVariant = {
                        variantId: v.id === 'main' ? '' : v.id,
                        variantType: v.id === 'main' ? 'main' : 'basic',
                        price: v.price,
                        mrp: v.mrp,
                        image: v.image,
                        stock: v.stock,
                        unlimited: v.unlimited,
                        inCart: v.inCart
                    };
                    updatePriceAndStock();
                });
                elBasics.appendChild(btn);
            });
            const first = elBasics.querySelector('.t9-qv-basic-btn:not([disabled])');
            if (first) first.click();
        }

        function updatePriceAndStock() {
            const v = state.selectedVariant;
            if (!v) return;
            elPrice.textContent = fmt(v.price);
            if (v.mrp > v.price) {
                elMrp.textContent = fmt(v.mrp);
                elMrp.style.display = '';
                const off = Math.round(((v.mrp - v.price) / v.mrp) * 100);
                elSave.textContent = 'SAVE ' + off + '%';
                elSave.style.display = '';
            } else {
                elMrp.style.display = 'none';
                elSave.style.display = 'none';
            }
            if (v.image) {
                elImg.src = v.image;
                setActiveQuickViewThumb(v.image);
            }
            if (v.unlimited) elStock.innerHTML = '<span style="color:#16a34a">In Stock</span>';
            else if (v.stock > 0) elStock.innerHTML = '<span style="color:#16a34a">' + v.stock + ' in stock</span>';
            else elStock.innerHTML = '<span style="color:#ef4444">Out of Stock</span>';
            if (v.inCart) {
                elAddBtn.disabled = false;
                elAddBtn.textContent = 'VIEW CART';
                elAddBtn.classList.add('is-in-cart');
                elAddBtn.dataset.viewCart = '1';
            } else {
                elAddBtn.dataset.viewCart = '0';
                elAddBtn.classList.remove('is-in-cart');
                elAddBtn.disabled = (v.stock <= 0 && !v.unlimited);
                elAddBtn.textContent = elAddBtn.disabled ? 'SOLD OUT' : 'ADD TO CART';
            }
        }

        function setCardViewCart(card) {
            if (!card) return;
            const actions = card.querySelector('.t18-actions');
            if (!actions) return;
            actions.innerHTML = '<a href="' + STORE_URL + 'cart" class="t18-icon-btn t18-view-cart-link"><i class="bi bi-cart-check-fill"></i><span>View Cart</span></a>';
            card.dataset.inCart = '1';
        }

        function markProductCardInCart(productId, selectedVariant) {
            const cards = document.querySelectorAll('.t18-card[data-id]');
            cards.forEach(card => {
                if (String(card.dataset.id) !== String(productId)) return;
                try {
                    const variants = JSON.parse(card.dataset.variants || '{"advanced":[],"basic":[]}');
                    const type = selectedVariant && selectedVariant.variantType;
                    const id = selectedVariant && String(selectedVariant.variantId || '');
                    if (type === 'advanced')(variants.advanced || []).forEach(item => {
                        if (String(item.id) === id) item.inCart = 1;
                    });
                    else if (type === 'basic')(variants.basic || []).forEach(item => {
                        if (String(item.id) === id) item.inCart = 1;
                    });
                    else {
                        card.dataset.inCart = '1';
                        (variants.basic || []).forEach(item => {
                            if (String(item.id) === 'main') item.inCart = 1;
                        });
                    }
                    card.dataset.variants = JSON.stringify(variants);

                    const isMainSelection = !selectedVariant || selectedVariant.variantType === 'main';
                    if (isMainSelection) {
                        setCardViewCart(card);
                    }
                    if (state.product && String(state.product.id) === String(productId)) {
                        if (type === 'advanced')(state.variants.advanced || []).forEach(item => {
                            if (String(item.id) === id) item.inCart = 1;
                        });
                        else if (type === 'basic')(state.variants.basic || []).forEach(item => {
                            if (String(item.id) === id) item.inCart = 1;
                        });
                        else(state.variants.basic || []).forEach(item => {
                            if (String(item.id) === 'main') item.inCart = 1;
                        });
                        if (state.selectedVariant) {
                            const selectedId = String(state.selectedVariant.variantId || '');
                            const selectedType = state.selectedVariant.variantType;
                            if (selectedType === type && selectedId === id) {
                                state.selectedVariant.inCart = true;
                                updatePriceAndStock();
                            }
                        }
                    }
                } catch (error) {
                    console.warn('[Cart] Could not refresh card state', error);
                }
            });
        }

        function closeModal() {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
        document.getElementById('t9QvClose').addEventListener('click', closeModal);
        modal.querySelector('.t9-qv-backdrop').addEventListener('click', closeModal);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
        });

        document.addEventListener('click', e => {
            const btn = e.target.closest('.openQuickView');
            if (!btn) return;
            e.preventDefault();
            const card = btn.closest('.t18-card');
            if (card) openQuickView(card);
        });

        function sendAddToCart(payload, onOk, onErr) {
            const requestData = {
                product_id: payload.product_id || '',
                quantity: 1,
                variant: payload.variant || '',
                advanced_variant: payload.advanced_variant || '',
                other: '',
                redirectUrl: payload.redirectUrl || ''
            };
            if (!window.jQuery || !jQuery.ajax) {
                onErr('jQuery is not loaded.');
                return;
            }
            jQuery.ajax({
                url: ADD_URL,
                type: 'POST',
                data: requestData,
                dataType: 'text',
                xhrFields: {
                    withCredentials: true
                },
                success: function(raw) {
                    let response = null;
                    try {
                        response = (typeof raw === 'object' && raw !== null) ? raw : JSON.parse(String(raw || '').trim());
                    } catch (error) {
                        response = {
                            success: true,
                            message: 'Product added successfully.'
                        };
                    }
                    if (!response || typeof response !== 'object') {
                        onErr('Unexpected response.');
                        return;
                    }
                    const message = String(response.message || '').toLowerCase();
                    if (response.alreadyInCart === true || response.duplicate === true || /already|duplicate|already exists|in cart|in bag/.test(message)) {
                        response.alreadyInCart = true;
                    }
                    if (response.success || response.alreadyInCart) {
                        if (typeof window.getProductCountAndPrice === 'function') window.getProductCountAndPrice();
                        if (typeof window.getCartData === 'function') window.getCartData();
                        if (typeof window.updateHeaderCounts === 'function') window.updateHeaderCounts();
                        if (typeof window.updateCartCounts === 'function') window.updateCartCounts();
                        onOk(response);
                    } else {
                        onErr(response.message || 'Could not add.');
                    }
                },
                error: function(xhr, status, error) {
                    let message = 'Cart request failed.';
                    try {
                        const r = JSON.parse(xhr.responseText || '{}');
                        if (r.message) message = r.message;
                    } catch (_) {}
                    onErr(message);
                }
            });
        }

        elAddBtn.addEventListener('click', function() {
            if (!state.selectedVariant) return;
            if (state.selectedVariant.inCart || elAddBtn.dataset.viewCart === '1') {
                window.location.href = STORE_URL + 'cart';
                return;
            }
            if (elAddBtn.disabled) return;
            const orig = elAddBtn.textContent;
            elAddBtn.disabled = true;
            elAddBtn.textContent = 'ADDING...';
            const payload = {
                product_id: state.product.id,
                quantity: 1,
                variant: '',
                advanced_variant: '',
                other: '',
                redirectUrl: ''
            };
            const v = state.selectedVariant;
            if (v.variantType === 'advanced') payload.advanced_variant = v.variantId;
            else if (v.variantType === 'basic') payload.variant = v.variantId;
            sendAddToCart(payload, res => {
                if (res.success || (res.message && /already|duplicate|exist/i.test(res.message))) {
                    toast(res.message || 'Added to bag!', false);
                    elAddBtn.textContent = 'ALREADY IN CART';
                    elAddBtn.disabled = true;
                    elAddBtn.classList.add('is-in-cart');
                    markProductCardInCart(state.product.id, state.selectedVariant);
                } else {
                    toast(res.message || 'Could not add.', true);
                    elAddBtn.disabled = false;
                    elAddBtn.textContent = orig;
                }
            }, msg => {
                toast(msg, true);
                elAddBtn.disabled = false;
                elAddBtn.textContent = orig;
            });
        });

        document.addEventListener('click', e => {
            const btn = e.target.closest('.t18-direct-add');
            if (!btn || btn.disabled) return;
            e.preventDefault();
            btn.disabled = true;
            const payload = {
                product_id: btn.dataset.id,
                quantity: 1,
                variant: '',
                advanced_variant: '',
                other: '',
                redirectUrl: ''
            };
            sendAddToCart(payload, res => {
                if (res.success || (res.message && /already|duplicate|exist/i.test(res.message))) {
                    toast(res.message || 'Added to bag!', false);
                    markProductCardInCart(btn.dataset.id, {
                        variantType: 'main',
                        variantId: ''
                    });
                } else {
                    toast(res.message || 'Could not add.', true);
                    btn.disabled = false;
                }
            }, msg => {
                toast(msg, true);
                btn.disabled = false;
            });
        });

        if (window.jQuery) {
            $(function() {
                if (typeof window.getProductCountAndPrice === 'function') window.getProductCountAndPrice();
                if (typeof window.getCartData === 'function') window.getCartData();
            });
        }
    })();
</script>

<!-- Delegated accordion handler -->
<script>
    document.addEventListener('click', function(event) {
        const trigger = event.target.closest('#t9QuickViewModal .t9-acc-trigger');
        if (!trigger) return;
        const panel = trigger.nextElementSibling;
        if (!panel || !panel.classList.contains('t9-acc-panel')) return;
        const accordion = trigger.closest('.t9-qv-acc');
        if (!accordion) return;
        const shouldOpen = trigger.getAttribute('aria-expanded') !== 'false';
        accordion.querySelectorAll('.t9-acc-trigger').forEach(function(button) {
            button.setAttribute('aria-expanded', 'false');
        });
        trigger.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
    });
</script>

<?php
/* ============================================================
   THEME 18 — COMPLETE PRODUCT CAROUSEL COMPONENT
   Include this file once, then call:
   <?= getProductCarouselSection('Most loved 💗', $storeUrl . 'shop-all', 10) ?>
   ============================================================ */
if (!function_exists('getProductCarouselSection')) {
    function getProductCarouselSection($heading = 'Most loved 💗', $viewAllUrl = '', $limit = 10)
    {
        global $storeUrl, $primary_color, $hover_color;
        $primary = $primary_color ?? (getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f');
        $hover   = $hover_color ?? (getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899');
        $viewUrl = $viewAllUrl ?: ($storeUrl . 'shop-all');
        $limit   = max(1, min(30, (int)$limit));

        $stmt = getProducts();
        $products = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        if (empty($products)) return '';

        ob_start(); ?>
        <section class="t18-product-carousel-section"
            style="--t9-primary:<?= htmlspecialchars($primary, ENT_QUOTES, 'UTF-8') ?>;--t9-hover:<?= htmlspecialchars($hover, ENT_QUOTES, 'UTF-8') ?>">
            <div class="t18-carousel-head">
                <h2 class="t18-carousel-title"><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h2>
                <a class="t18-carousel-view" href="<?= htmlspecialchars($viewUrl, ENT_QUOTES, 'UTF-8') ?>">View All</a>
            </div>
            <div class="t18-carousel-shell">
                <span class="t18-carousel-shape a" aria-hidden="true"></span>
                <span class="t18-carousel-shape b" aria-hidden="true"></span>
                <button type="button" class="t18-carousel-nav prev" aria-label="Previous products"><i class="bi bi-chevron-left"></i></button>
                <div class="t18-carousel-track" tabindex="0" aria-label="Product carousel">
                    <?php foreach (array_slice($products, 0, $limit) as $product): ?>
                        <?= getProductHtml($product['id']) ?>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="t18-carousel-nav next" aria-label="Next products"><i class="bi bi-chevron-right"></i></button>
            </div>
        </section>
        <script>
            (function() {
                document.querySelectorAll('.t18-product-carousel-section:not([data-carousel-ready])').forEach(function(section) {
                    section.setAttribute('data-carousel-ready', '1');
                    var track = section.querySelector('.t18-carousel-track');
                    var prev = section.querySelector('.t18-carousel-nav.prev');
                    var next = section.querySelector('.t18-carousel-nav.next');
                    if (!track || !prev || !next) return;

                    function update() {
                        var max = Math.max(0, track.scrollWidth - track.clientWidth);
                        prev.disabled = track.scrollLeft <= 2;
                        next.disabled = track.scrollLeft >= max - 2;
                    }

                    function move(direction) {
                        var firstCard = track.querySelector('.t18-card');
                        if (!firstCard) return;
                        var gap = parseFloat(getComputedStyle(track).gap) || 0;
                        var step = (firstCard.getBoundingClientRect().width + gap) * 2;
                        track.scrollBy({
                            left: direction * step,
                            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
                        });
                    }
                    prev.addEventListener('click', function() {
                        move(-1);
                    });
                    next.addEventListener('click', function() {
                        move(1);
                    });
                    track.addEventListener('scroll', update, {
                        passive: true
                    });
                    window.addEventListener('resize', update);
                    update();
                });
            })();
        </script>
<?php
        return ob_get_clean();
    }
}
?>


<?php
/* ============================================================
   THEME 18 — PRODUCT CAROUSEL COMPONENT (RANDOM MINGLE)
   - Heading: default "Popular Products"
   - PHP shuffles products → different every request
   - JS re-shuffles the DOM → different every page load
   ============================================================ */
if (!function_exists('getProductCarouselSection_random')) {
    function getProductCarouselSection_random($heading = 'Popular Products', $viewAllUrl = '', $limit = 10)
    {
        global $storeUrl, $primary_color, $hover_color;
        $primary = $primary_color ?? (getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f');
        $hover   = $hover_color ?? (getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899');
        $viewUrl = $viewAllUrl ?: ($storeUrl . 'shop-all');
        $limit   = max(1, min(30, (int)$limit));

        // 1. Fetch all products
        $stmt = getProducts();
        $products = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        if (empty($products)) return '';

        // 2. PHP shuffle → random on server
        shuffle($products);

        // 3. Take only what we need
        $randomProducts = array_slice($products, 0, $limit);

        ob_start(); ?>
        <section class="t18-product-carousel-section"
            style="--t9-primary:<?= htmlspecialchars($primary, ENT_QUOTES, 'UTF-8') ?>;--t9-hover:<?= htmlspecialchars($hover, ENT_QUOTES, 'UTF-8') ?>">
            <div class="t18-carousel-head">
                <h2 class="t18-carousel-title"><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h2>
                <a class="t18-carousel-view" href="<?= htmlspecialchars($viewUrl, ENT_QUOTES, 'UTF-8') ?>">View All</a>
            </div>
            <div class="t18-carousel-shell">
                <span class="t18-carousel-shape a" aria-hidden="true"></span>
                <span class="t18-carousel-shape b" aria-hidden="true"></span>
                <button type="button" class="t18-carousel-nav prev" aria-label="Previous products"><i class="bi bi-chevron-left"></i></button>

                <div class="t18-carousel-track" tabindex="0" aria-label="Product carousel">
                    <?php foreach ($randomProducts as $product): ?>
                        <?= getProductHtml($product['id']) ?>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="t18-carousel-nav next" aria-label="Next products"><i class="bi bi-chevron-right"></i></button>
            </div>
        </section>

        <script>
            (function() {
                document.querySelectorAll('.t18-product-carousel-section:not([data-carousel-ready])').forEach(function(section) {
                    section.setAttribute('data-carousel-ready', '1');

                    var track = section.querySelector('.t18-carousel-track');
                    var prev = section.querySelector('.t18-carousel-nav.prev');
                    var next = section.querySelector('.t18-carousel-nav.next');
                    if (!track || !prev || !next) return;

                    // === JS SHUFFLE ===
                    // Grab all cards, shuffle them in memory, and re-append.
                    var cards = Array.from(track.querySelectorAll('.t18-card'));
                    for (let i = cards.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [cards[i], cards[j]] = [cards[j], cards[i]];
                    }
                    cards.forEach(card => track.appendChild(card));

                    // === CAROUSEL NAVIGATION ===
                    function update() {
                        var max = Math.max(0, track.scrollWidth - track.clientWidth);
                        prev.disabled = track.scrollLeft <= 2;
                        next.disabled = track.scrollLeft >= max - 2;
                    }

                    function move(direction) {
                        var firstCard = track.querySelector('.t18-card');
                        if (!firstCard) return;
                        var gap = parseFloat(getComputedStyle(track).gap) || 0;
                        var step = (firstCard.getBoundingClientRect().width + gap) * 2;
                        track.scrollBy({
                            left: direction * step,
                            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
                        });
                    }

                    prev.addEventListener('click', function() {
                        move(-1);
                    });
                    next.addEventListener('click', function() {
                        move(1);
                    });
                    track.addEventListener('scroll', update, {
                        passive: true
                    });
                    window.addEventListener('resize', update);

                    // Delay the initial button check slightly to let DOM settle
                    setTimeout(update, 100);
                });
            })();
        </script>
<?php
        return ob_get_clean();
    }
}
?>