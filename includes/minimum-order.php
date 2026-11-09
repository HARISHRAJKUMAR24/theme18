<!-- ============================================================
     HTML — SECTION 1: MINIMUM ORDER BAR (Mega Offer design)
============================================================ -->
<?php if (!empty(getSettings("minimum_order_amount"))) : ?>
    <?php
    $primary_color = getData("color",       "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f';
    $hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899';
    $minOrder      = getSettings("minimum_order_amount");
    $minOrderText  = currencyToSymbol($storeCurrency) . ' ' . $minOrder;
    ?>
    <div class="offer-bar"
        style="
            --offer-primary: <?= htmlspecialchars($primary_color) ?>;
            --offer-hover:   <?= htmlspecialchars($hover_color) ?>;
         ">
        <div class="offer-content max-w-[1450px] mx-auto h-full px-4 flex items-center justify-center">
            <div class="flex items-center justify-center gap-4">
                <span class="text-yellow-300">⚡</span>
                <b class="text-xs md:text-sm">Minimum Order</b>
                <span class="text-purple-300">|</span>
                <span class="text-xs">
                    Place orders above <b><?= htmlspecialchars($minOrderText) ?></b>
                </span>
                <span class="text-purple-300 hidden sm:block">|</span>
                <span class="hidden sm:block text-xs">Shop now &amp; save more</span>
            </div>

            <!-- right-side amount pill (matches original timer slot) -->
            <div class="hidden sm:flex absolute right-5 top-1/2 -translate-y-1/2 items-center gap-2 border border-white/40 bg-white/15 rounded-full px-3 py-1 text-xs">
                <i data-lucide="shopping-bag" class="w-4 h-4 text-yellow-300"></i>
                Min. <?= htmlspecialchars($minOrderText) ?>
            </div>
        </div>
    </div>
<?php endif; ?>


<!-- ============================================================
     STYLES — SECTION 2: MINIMUM ORDER BAR
     (Same design as Mega Offer bar, DB-color driven)
============================================================ -->
<style>
    .offer-bar {
        height: 42px;
        background: linear-gradient(100deg,
                color-mix(in srgb, var(--offer-primary) 75%, #1a0022) 0%,
                var(--offer-primary) 45%,
                color-mix(in srgb, var(--offer-hover) 80%, #1a0022) 100%);
        color: white;
        position: relative;
        isolation: isolate;
        overflow: hidden;
    }

    /* decorative blob (top-left) */
    .offer-bar::before {
        content: "";
        position: absolute;
        z-index: -1;
        left: -25px;
        top: -40px;
        width: 190px;
        height: 85px;
        background: linear-gradient(120deg,
                color-mix(in srgb, var(--offer-hover) 85%, #ffffff),
                var(--offer-hover));
        border-radius: 0 0 75px 0;
    }

    /* decorative glyph (the ϟ lightning) */
    .offer-bar::after {
        content: "ϟ";
        position: absolute;
        z-index: 0;
        left: 48px;
        top: -25px;
        font-size: 70px;
        color: color-mix(in srgb, var(--offer-hover) 65%, #ffffff);
        transform: rotate(15deg);
        pointer-events: none;
        opacity: .85;
    }

    @media (max-width: 640px) {
        .offer-bar {
            height: 35px;
        }

        .offer-bar::before {
            width: 105px;
            height: 57px;
            top: -27px;
        }

        .offer-bar::after {
            left: 18px;
            top: -14px;
            font-size: 48px;
        }

        .offer-content {
            padding: 0 8px;
        }

        .offer-content>div {
            gap: 8px !important;
        }

        .offer-content span {
            font-size: 10px;
        }
    }
</style>