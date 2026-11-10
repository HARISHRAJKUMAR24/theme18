<?php
// Get cart items
$cartItems = getCartData()->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals & stock initially
$outOfStockCount = 0;
$subTotal = 0.0;
$subTotalWithTax = 0.0;
$gstAmount = 0.0;
$discountAmount = 0;

foreach ($cartItems as $key => $cart) {
    $productId = $cart['product_id'];
    $productData = readData("*", "seller_products", "id='$productId'")->fetch(PDO::FETCH_ASSOC);

    $image = $productData['image'] ?? '';
    $productName = $productData['name'] ?? '';
    $mrp_price = $cart['mrp_price'] ?? $cart['price'];

    // Stock Logic
    $isOutOfStock = false;
    $unlimitedStock = $productData['unlimited_stock'] ?? 0;
    $totalStocks = $productData['total_stocks'] ?? 0;
    $availableStock = 0;

    $variantId = $cart['other'] ?? null;
    $advVarId = $cart['advanced_variant'] ?? null;

    if (!empty($advVarId)) {
        $advVarData = readData("*", "seller_product_advanced_variants", "id='$advVarId'")->fetch(PDO::FETCH_ASSOC);
        if ($advVarData) {
            if (!empty($advVarData['image'])) $image = $advVarData['image'];
            $availableStock = $advVarData['stock'] ?? 0;
            if (($advVarData['unlimited_stock'] ?? 0) != 1 && $availableStock <= 0) $isOutOfStock = true;
        }
    } elseif (!empty($variantId)) {
        $varData = readData("*", "seller_product_variants", "id='$variantId'")->fetch(PDO::FETCH_ASSOC);
        if ($varData) {
            if (!empty($varData['image'])) $image = $varData['image'];
            $availableStock = $varData['stock'] ?? 0;
            if (($varData['unlimited_stock'] ?? 0) != 1 && $availableStock <= 0) $isOutOfStock = true;
        }
    } else {
        $availableStock = $totalStocks;
        if ($unlimitedStock != 1 && $totalStocks <= 0) $isOutOfStock = true;
    }

    if ($isOutOfStock) $outOfStockCount++;

    // Variant Text
    $variationParts = [];
    if (!empty($variantId)) {
        $varText = getData("variation", "seller_product_variants", "id='$variantId'");
        if ($varText) $variationParts[] = $varText;
    }
    if (!empty($advVarId)) {
        $size = getData("size", "seller_product_advanced_variants", "id='$advVarId'");
        $colorId = getData("color", "seller_product_advanced_variants", "id='$advVarId'");
        $color = $colorId ? getData("color_name", "product_colors", "id='$colorId'") : '';
        if ($size) $variationParts[] = "Size: $size";
        if ($color) $variationParts[] = "Color: $color";
    }
    $variationText = implode(" | ", $variationParts);

    // Save processed data back
    $cartItems[$key]['is_out_of_stock'] = $isOutOfStock;
    $cartItems[$key]['product_name'] = $productName;
    $cartItems[$key]['image'] = $image;
    $cartItems[$key]['variation_text'] = $variationText;
    $cartItems[$key]['available_stock'] = $availableStock;

    // Totals calculation
    if (!$isOutOfStock) {
        $qty = (int)$cart['quantity'];
        $price = (float)$cart['price'];
        $subTotal += $price * $qty;

        $gstPercentage = getData("gst_percentage", "seller_products", "id='$productId'");
        if ($gstPercentage !== null && $gstPercentage !== '') {
            $gstCalc = calculateGst($price * $qty, $gstPercentage);
        } else {
            $gstCalc = calculateGst($price * $qty);
        }
        $subTotalWithTax += isset($gstCalc['getPrice']) ? (float)$gstCalc['getPrice'] : ($price * $qty);
        $gstAmount += isset($gstCalc['gstAmount']) ? (float)$gstCalc['gstAmount'] : 0;
    }
}

// Minimum order logic
$minOrderAmount = (float)getSettings("minimum_order_amount");
$isMinimumOrderMet = true;
if ($minOrderAmount > 0 && $subTotal < $minOrderAmount) {
    $isMinimumOrderMet = false;
}
$disableCheckout = ($outOfStockCount > 0 || !$isMinimumOrderMet);
?>

<style>
/* Theme 18 Custom Cart Styles */
:root {
    --t18-primary: <?= htmlspecialchars($primary_color ?? '#ed168b') ?>;
    --t18-hover: <?= htmlspecialchars($hover_color ?? '#dc007b') ?>;
}

.t18-cart-card {
    background: #fff;
    border: 1px solid rgba(15, 23, 42, .08);
    border-radius: 12px;
    box-shadow: 0 4px 20px -10px rgba(0,0,0,0.05);
}

.t18-cart-row {
    transition: background-color 0.2s ease;
    border-bottom: 1px solid #f1f5f9;
}
.t18-cart-row:last-child {
    border-bottom: none;
}
.t18-cart-row:hover {
    background-color: #f8fafc;
}

.t18-qty-box {
    display: inline-flex;
    align-items: center;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
}

.t18-qty-btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    color: #475569;
    font-size: 16px;
    font-weight: 500;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}
@media (min-width: 640px) {
    .t18-qty-btn {
        width: 36px;
        height: 36px;
        font-size: 18px;
    }
}
.t18-qty-btn:hover {
    background: #f1f5f9;
    color: var(--t18-primary);
}

.t18-qty-input {
    width: 36px;
    text-align: center;
    border: none;
    background: transparent;
    font-weight: 600;
    color: #0f172a;
    outline: none;
    -moz-appearance: textfield;
    font-size: 14px;
}
@media (min-width: 640px) {
    .t18-qty-input {
        width: 44px;
        font-size: 16px;
    }
}
.t18-qty-input::-webkit-outer-spin-button,
.t18-qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.t18-summary-card {
    background: #fff;
    border: 1px solid color-mix(in srgb, var(--t18-primary) 15%, #e5e7eb);
    border-radius: 14px;
    box-shadow: 0 10px 30px -15px color-mix(in srgb, var(--t18-primary) 20%, transparent);
    overflow: hidden;
}

.t18-checkout-btn {
    background: color-mix(in srgb, var(--t18-primary) 65%, #30251c);
    transition: all 0.3s ease;
    border-radius: 999px;
}
.t18-checkout-btn:hover:not(:disabled) {
    background: var(--t18-hover);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -8px var(--t18-hover);
}
.t18-checkout-btn:disabled {
    background: #9ca3af !important;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.t18-remove-btn {
    color: #94a3b8;
    transition: all 0.2s ease;
}
.t18-remove-btn:hover {
    color: #ef4444;
    transform: scale(1.1);
}
</style>

<script>
    var currentUserId = <?= isset($customerId) && !empty($customerId) ? json_encode($customerId) : 'null' ?>;
</script>

<!-- ================= CART LAYOUT (Stacked: Summary Top, Products Bottom) ================= -->
<div class="flex flex-col gap-6 sm:gap-8 w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- ================= TOP: ORDER SUMMARY ================= -->
    <?php if (!empty($cartItems)): ?>
        <div class="w-full">
            <div class="t18-summary-card">
                <div class="p-5 sm:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-5 sm:gap-6">
                    
                    <!-- Left side of Summary: Title and Totals -->
                    <div class="flex-1">
                        <h2 class="mb-3 sm:mb-4 text-base sm:text-lg font-black uppercase tracking-wide text-gray-900 flex items-center gap-2">
                            <i class="bi bi-receipt text-[var(--t18-primary)]"></i> Order Summary
                        </h2>
                        
                        <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs sm:text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 font-medium">Subtotal:</span>
                                <span class="font-bold text-gray-900"><?= currencyToSymbol($storeCurrency) . number_format($subTotal, 2) ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 font-medium">Shipping:</span>
                                <span class="text-gray-500 font-medium text-[10px] sm:text-xs bg-gray-100 px-2 py-1 rounded">Calculated at checkout</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 font-medium">GST:</span>
                                <span class="font-bold text-gray-900"><?= getSettings("gst_tax_type") == "inclusive" ? currencyToSymbol($storeCurrency) . "0.00" : currencyToSymbol($storeCurrency) . number_format($gstAmount, 2) ?></span>
                            </div>
                        </div>

                        <?php if (!$isMinimumOrderMet): ?>
                            <div class="mt-3 p-2.5 bg-red-50 border border-red-100 rounded-lg text-[11px] sm:text-xs text-red-600 font-medium flex items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill mt-0.5"></i>
                                <span>Minimum order amount is <?= currencyToSymbol($storeCurrency) . number_format($minOrderAmount, 2) ?>. You need <?= currencyToSymbol($storeCurrency) . number_format($minOrderAmount - $subTotal, 2) ?> more to checkout.</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Right side of Summary: Total & Checkout -->
                    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                        <div class="text-center sm:text-right w-full sm:w-auto">
                            <span class="block text-[10px] sm:text-xs font-bold uppercase text-gray-500 tracking-wide mb-1">Total Amount</span>
                            <span class="text-2xl sm:text-3xl font-black" style="color: var(--t18-primary);"><?= currencyToSymbol($storeCurrency) . number_format($subTotalWithTax, 2) ?></span>
                        </div>
                        
                        <button onclick="proceedToCheckout()" class="t18-checkout-btn flex h-11 sm:h-14 w-full sm:w-auto px-6 sm:px-10 items-center justify-center text-xs sm:text-sm font-bold uppercase tracking-widest text-white whitespace-nowrap" <?= $disableCheckout ? 'disabled' : '' ?>>
                            <?php if ($outOfStockCount > 0): ?>
                                Remove Out of Stock
                            <?php elseif (!$isMinimumOrderMet): ?>
                                Min Order: <?= currencyToSymbol($storeCurrency) . number_format($minOrderAmount, 0) ?>
                            <?php else: ?>
                                Checkout
                            <?php endif; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ================= BOTTOM: CART ITEMS ================= -->
    <section class="w-full">
        <!-- CART HEADER ROW (desktop only) -->
        <div class="mb-4 hidden grid-cols-[1fr_100px_140px_100px_50px] gap-4 px-4 text-xs font-bold uppercase tracking-wider text-gray-400 sm:grid items-center">
            <span>Product</span>
            <span class="text-center">Price</span>
            <span class="text-center">Quantity</span>
            <span class="text-center">Total</span>
            <span class="text-right pr-2">Remove</span>
        </div>

        <!-- CART ITEMS CONTAINER -->
        <div id="cartItemsContainer" class="t18-cart-card px-2 sm:px-4">
            <?php if (empty($cartItems)): ?>
                <!-- Empty State -->
                <div class="py-16 sm:py-20 text-center">
                    <div class="mx-auto mb-6 flex h-20 w-20 sm:h-24 sm:w-24 items-center justify-center rounded-full bg-gray-50 text-gray-300">
                        <svg class="h-10 w-10 sm:h-12 sm:w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M2.5 3.5h2l2.4 12h11.8l2.2-8.5H5.3" stroke-linecap="round" stroke-linejoin="round" />
                            <circle cx="9" cy="19.5" r="1.2" />
                            <circle cx="17" cy="19.5" r="1.2" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg sm:text-xl font-bold text-gray-800">Your cart is empty</h3>
                    <p class="mb-6 sm:mb-8 text-xs sm:text-sm text-gray-500">Looks like you haven't added anything yet.</p>
                    <a href="<?= $storeUrl ?>shop-all" class="inline-flex items-center gap-2 rounded-full px-6 sm:px-8 py-3 sm:py-3.5 text-xs sm:text-sm font-bold text-white transition hover:opacity-90 t18-checkout-btn">
                        Continue Shopping <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($cartItems as $cart): ?>
                    <article class="cart-row t18-cart-row py-4 sm:py-5 sm:grid sm:grid-cols-[1fr_100px_140px_100px_50px] sm:items-center sm:gap-4"
                        data-id="<?= $cart['product_id'] ?>"
                        data-variant="<?= $cart['other'] ?? '' ?>"
                        data-advancedvariant="<?= $cart['advanced_variant'] ?? '' ?>"
                        data-available-stock="<?= $cart['available_stock'] ?>">

                        <!-- Mobile/Desktop: Image & Details -->
                        <div class="flex items-start gap-3 sm:gap-4 mb-3 sm:mb-0 relative">
                            
                            <!-- MOBILE REMOVE BUTTON (TOP RIGHT) -->
                            <button type="button" class="t18-remove-btn removeQtyBtn absolute top-0 right-0 sm:hidden" data-id="<?= $cart['product_id'] ?>" data-variant="<?= $cart['other'] ?? '' ?>" data-advancedvariant="<?= $cart['advanced_variant'] ?? '' ?>" title="Remove item">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>

                            <a href="<?= $storeUrl ?>product/<?= getData('slug', 'seller_products', "id='{$cart['product_id']}'") ?>" class="shrink-0 relative">
                                <div class="flex h-[70px] w-[70px] sm:h-[90px] sm:w-[90px] items-center justify-center overflow-hidden rounded-lg sm:rounded-xl border border-gray-100 bg-gray-50 p-1.5 sm:p-2 <?= $cart['is_out_of_stock'] ? 'opacity-50 grayscale' : '' ?>">
                                    <img src="<?= UPLOADS_URL . $cart['image'] ?>" alt="<?= htmlspecialchars($cart['product_name']) ?>" class="h-full w-full object-contain mix-blend-multiply">
                                </div>
                            </a>
                            
                            <!-- Added pr-6 on mobile so text doesn't overlap the absolute top-right remove button -->
                            <div class="min-w-0 flex-1 pt-0.5 sm:pt-1 pr-6 sm:pr-0">
                                <a href="<?= $storeUrl ?>product/<?= getData('slug', 'seller_products', "id='{$cart['product_id']}'") ?>" class="text-xs sm:text-base font-semibold leading-tight text-gray-900 transition hover:text-[var(--t18-hover)] line-clamp-2">
                                    <?= htmlspecialchars($cart['product_name']) ?>
                                </a>

                                <?php if (!empty($cart['variation_text'])): ?>
                                    <p class="mt-1 inline-block rounded-md bg-gray-50 px-1.5 py-0.5 text-[10px] sm:text-xs text-gray-500 border border-gray-100 line-clamp-1"><?= htmlspecialchars($cart['variation_text']) ?></p>
                                <?php endif; ?>

                                <?php if ($cart['is_out_of_stock']): ?>
                                    <p class="mt-1.5 text-[10px] sm:text-xs font-bold text-red-500 flex items-center gap-1"><i class="bi bi-x-circle"></i> Out of stock</p>
                                <?php else: ?>
                                    <p class="mt-1.5 text-[10px] sm:text-xs font-medium text-green-600 flex items-center gap-1"><i class="bi bi-check-circle"></i> In stock</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Mobile/Desktop: Price -->
                        <div class="text-xs sm:text-sm text-gray-700 sm:text-center flex justify-between sm:block mb-2 sm:mb-0">
                            <span class="font-medium text-gray-500 sm:hidden">Price:</span>
                            <span class="font-semibold text-gray-900"><?= currencyToSymbol($storeCurrency) . number_format($cart['price'], 2) ?></span>
                        </div>

                        <!-- Mobile/Desktop: Quantity Controls -->
                        <div class="flex items-center justify-between sm:justify-center mb-2 sm:mb-0">
                            <span class="font-medium text-gray-500 text-xs sm:hidden">Quantity:</span>
                            <?php if ($cart['is_out_of_stock']): ?>
                                <span class="text-[10px] sm:text-xs text-gray-400 italic bg-gray-50 px-2 py-1 rounded-md">Unavailable</span>
                            <?php else: ?>
                                <div class="t18-qty-box">
                                    <button type="button" class="t18-qty-btn decreaseQtyBtn" data-id="<?= $cart['product_id'] ?>" data-variant="<?= $cart['other'] ?? '' ?>" data-advancedVariant="<?= $cart['advanced_variant'] ?? '' ?>">&minus;</button>
                                    <input type="number" class="t18-qty-input currentQty" value="<?= $cart['quantity'] ?>" min="1" max="99" readonly>
                                    <button type="button" class="t18-qty-btn increaseQtyBtn" data-id="<?= $cart['product_id'] ?>" data-variant="<?= $cart['other'] ?? '' ?>" data-advancedVariant="<?= $cart['advanced_variant'] ?? '' ?>">+</button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Mobile/Desktop: Total -->
                        <div class="text-xs sm:text-sm sm:text-center flex justify-between sm:block mb-2 sm:mb-0">
                            <span class="font-medium text-gray-500 sm:hidden">Total:</span>
                            <span class="font-bold text-[var(--t18-primary)]"><?= currencyToSymbol($storeCurrency) . number_format($cart['price'] * $cart['quantity'], 2) ?></span>
                        </div>

                        <!-- DESKTOP REMOVE BUTTON (FAR RIGHT) -->
                        <div class="hidden sm:flex justify-end sm:justify-end sm:pr-2">
                            <button type="button" class="t18-remove-btn removeQtyBtn" data-id="<?= $cart['product_id'] ?>" data-variant="<?= $cart['other'] ?? '' ?>" data-advancedvariant="<?= $cart['advanced_variant'] ?? '' ?>" title="Remove item">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10 11v6M14 11v6" stroke-linecap="round" />
                                </svg>
                            </button>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Continue Shopping -->
        <?php if (!empty($cartItems)): ?>
            <div class="mt-6 sm:mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a href="<?= $storeUrl ?>shop-all" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-gray-500 transition hover:text-[var(--t18-primary)]">
                    <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 12H5M11 6l-6 6 6 6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Continue Shopping
                </a>
            </div>
        <?php endif; ?>
    </section>

</div>

<!-- ================= JAVASCRIPT LOGIC ================= -->
<script>
    // Smooth Slide-in Toast Notification
    function showCustomToast(message, type = 'success') {
        const existing = document.querySelectorAll('.simple-toast');
        existing.forEach(t => t.remove());

        const toast = document.createElement('div');
        // Slide in from the right animation using Tailwind
        toast.className = 'simple-toast fixed top-20 sm:top-24 right-4 z-[999] px-4 sm:px-6 py-3 sm:py-4 rounded-xl shadow-2xl text-xs sm:text-sm font-bold text-white flex items-center gap-2 sm:gap-3 border border-white/20 transform translate-x-full opacity-0 transition-all duration-300 ease-out';
        
        // Use primary color for success, red for error
        toast.style.backgroundColor = type === 'success' ? 'var(--t18-primary)' : '#ef4444';
        
        toast.innerHTML = `
            <i class="bi ${type === 'success' ? 'bi-check-circle-fill text-base sm:text-lg' : 'bi-exclamation-circle-fill text-base sm:text-lg'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);

        // Trigger Slide-in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });

        // Auto dismiss
        setTimeout(() => {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Handle Quantity Changes
        document.addEventListener('click', function(e) {
            if (e.target.closest('.increaseQtyBtn')) {
                const btn = e.target.closest('.increaseQtyBtn');
                const input = btn.parentElement.querySelector('.currentQty');
                const cartRow = btn.closest('.cart-row');
                const availableStock = parseInt(cartRow.dataset.availableStock) || 0;
                const currentQty = parseInt(input.value);

                if (availableStock > 0 && currentQty >= availableStock) {
                    showCustomToast(`Low stock: Only ${availableStock} available`, 'error');
                    return;
                }

                updateCartQty(btn.dataset.id, btn.dataset.variant, btn.dataset.advancedvariant, currentQty + 1);
            }

            if (e.target.closest('.decreaseQtyBtn')) {
                const btn = e.target.closest('.decreaseQtyBtn');
                const input = btn.parentElement.querySelector('.currentQty');
                if (parseInt(input.value) > 1) {
                    updateCartQty(btn.dataset.id, btn.dataset.variant, btn.dataset.advancedvariant, parseInt(input.value) - 1);
                }
            }

            if (e.target.closest('.removeQtyBtn')) {
                const btn = e.target.closest('.removeQtyBtn');
                if (confirm('Remove item from cart?')) {
                    removeCartItem(btn.dataset.id, btn.dataset.variant, btn.dataset.advancedvariant);
                }
            }
        });

        function updateCartQty(id, variant, advancedVariant, qty) {
            $.ajax({
                url: 'shop/ajax/update-cart.php',
                type: 'POST',
                data: {
                    product_id: id,
                    variant: variant,
                    advanced_variant: advancedVariant,
                    quantity: qty
                },
                success: function(res) {
                    try {
                        const response = JSON.parse(res);
                        if (response.success) {
                            location.reload();
                        } else {
                            if (response.message && (response.message.includes('stock') || response.message.includes('available'))) {
                                const stockMatch = response.message.match(/\d+/);
                                if (stockMatch) {
                                    showCustomToast(`Low stock: Only ${stockMatch[0]} available`, 'error');
                                } else {
                                    showCustomToast(response.message, 'error');
                                }
                            } else {
                                showCustomToast(response.message || 'Failed to update', 'error');
                            }
                        }
                    } catch (err) {
                        location.reload();
                    }
                }
            });
        }

        function removeCartItem(id, variant, advancedVariant) {
            $.ajax({
                url: 'shop/ajax/remove-from-cart.php',
                type: 'POST',
                data: {
                    product_id: id,
                    variant: variant,
                    advanced_variant: advancedVariant
                },
                success: function(res) {
                    try {
                        const response = JSON.parse(res);
                        if (response.success) {
                            location.reload();
                        } else {
                            showCustomToast(response.message || 'Failed to remove', 'error');
                        }
                    } catch (err) {
                        location.reload();
                    }
                }
            });
        }
    });

    // Checkout Tracking Function
    function proceedToCheckout() {
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Processing...';

        const userId = typeof currentUserId !== 'undefined' ? currentUserId : null;
        if (!userId) {
            showCustomToast('Please login to continue checkout', 'error');
            setTimeout(() => {
                window.location.href = '<?= $storeUrl ?>login';
            }, 1500);
            return;
        }

        window.location.href = '<?= $storeUrl ?>checkout';
    }
</script>