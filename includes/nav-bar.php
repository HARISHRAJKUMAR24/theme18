<!-- ============================================================
     BACKEND — DB COLORS + COUNTS + CATEGORIES
============================================================ -->
<?php
$headerPrimary = getData("color",       "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ed168b';
$headerHover   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#dc007b';

$cart_count     = 0;
$wishlist_count = 0;
if (isset($cookie_id) && !empty($cookie_id)) {
    $cartQuery = $db->prepare("SELECT SUM(quantity) FROM customer_cart WHERE customer_id = ?");
    $cartQuery->execute([$cookie_id]);
    $cart_count = (int)$cartQuery->fetchColumn();

    $wishQuery = $db->prepare("SELECT COUNT(*) FROM customer_wishlists WHERE customer_id = ?");
    $wishQuery->execute([$cookie_id]);
    $wishlist_count = (int)$wishQuery->fetchColumn();
}

$stmt = getCategories();
$allHeaderCategories = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
$allHeaderCategories = array_filter($allHeaderCategories, function ($cat) {
    return isset($cat['add_to_menu']) && (int)$cat['add_to_menu'] === 1;
});
$allHeaderCategories = array_reverse($allHeaderCategories);

$parentCats   = [];
$childCatsMap = [];
foreach ($allHeaderCategories as $cat) {
    $parentId = (int)($cat['parent_category'] ?? 0);
    if ($parentId <= 0) $parentCats[] = $cat;
    else {
        if (!isset($childCatsMap[$parentId])) $childCatsMap[$parentId] = [];
        $childCatsMap[$parentId][] = $cat;
    }
}
if (empty($parentCats)) $parentCats = $allHeaderCategories;

$appBasePath = parse_url(APP_URL, PHP_URL_PATH) ?: '/';
$appBasePath = '/' . trim($appBasePath, '/');
$ajaxBase    = ($appBasePath === '/') ? '/' : rtrim($appBasePath, '/') . '/';
?>


<!-- ============================================================
     HTML — NAVBAR
============================================================ -->
<header class="main-header">
    <div class="max-w-[1450px] mx-auto px-3 md:px-7">
        <div class="header-top">

            <!-- LEFT: mobile menu trigger + desktop logo -->
            <div class="header-left">
                <div class="mobile-menu-wrap">
                    <button id="mobileToggle" class="mobile-menu-btn" aria-label="Open menu">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                    <div id="mobilePopup" class="mobile-popup hidden">
                        <div class="popup-heading">
                            <div>
                                <p class="text-sm font-extrabold">Explore <?= htmlspecialchars($storeName ?? 'Store') ?></p>
                                <p class="text-[10px] text-gray-400 mt-1">Everything you love, in one place</p>
                            </div>
                            <button id="closePopup" class="text-gray-500 hover:opacity-70">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <a href="<?= $storeUrl ?>" class="popup-link"><i data-lucide="house"></i> Home</a>
                        <a href="<?= $storeUrl ?>shop-all" class="popup-link"><i data-lucide="shopping-bag"></i> Shop All</a>

                        <?php foreach (array_slice($parentCats, 0, 5) as $cat): ?>
                            <a href="<?= $storeUrl . 'category/' . htmlspecialchars($cat['slug']) ?>" class="popup-link">
                                <i data-lucide="tag"></i> <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        <?php endforeach; ?>

                        <a href="<?= $storeUrl ?>new-arrivals" class="popup-link"><i data-lucide="star"></i> New Arrivals</a>

                        <a href="<?= $storeUrl ?>shop-all" class="popup-link popup-deal">
                            <i data-lucide="tag"></i> Today's Deals
                            <i data-lucide="arrow-up-right" class="ml-auto"></i>
                        </a>
                    </div>
                </div>

                <!-- logo (desktop left, mobile hidden here — moved to center) -->
                <a href="<?= $storeUrl ?>" class="flex items-center gap-2 shrink-0 logo-link logo-desktop">
                    <?php if (getSettings("logo")): ?>
                        <img src="<?= UPLOADS_URL . getSettings("logo") ?>"
                            alt="<?= htmlspecialchars($storeName ?? 'Store') ?>"
                            class="logo-img">
                    <?php else: ?>
                        <h1 class="logo-title" style="color: <?= htmlspecialchars($headerPrimary) ?>">
                            <?= htmlspecialchars(strtoupper($storeName ?? 'STORE')) ?>
                        </h1>
                    <?php endif; ?>
                </a>
            </div>

            <!-- CENTER: logo on mobile/tablet · search button on desktop -->
            <div class="header-center">
                <!-- mobile/tablet centered logo -->
                <a href="<?= $storeUrl ?>" class="logo-link logo-mobile">
                    <?php if (getSettings("logo")): ?>
                        <img src="<?= UPLOADS_URL . getSettings("logo") ?>"
                            alt="<?= htmlspecialchars($storeName ?? 'Store') ?>"
                            class="logo-img">
                    <?php else: ?>
                        <h1 class="logo-title" style="color: <?= htmlspecialchars($headerPrimary) ?>">
                            <?= htmlspecialchars(strtoupper($storeName ?? 'STORE')) ?>
                        </h1>
                    <?php endif; ?>
                </a>

                <!-- desktop search button -->
                <button id="desktopSearchOpen" class="desktop-search-btn hidden md:flex" aria-label="Search">
                    <i data-lucide="search" class="w-5 h-5"></i>
                    <span>Search products, brands, categories...</span>
                </button>
            </div>

            <!-- RIGHT: actions + mobile search icon -->
            <div class="header-actions flex items-center gap-3 sm:gap-5">
                <a href="<?= $storeUrl ?>contact" class="support-block hidden xl:flex items-center gap-2">
                    <i data-lucide="headset" class="w-7 h-7"></i>
                    <div>
                        <p class="font-bold text-xs">Support</p>
                        <p class="text-[9px] text-gray-500">10 AM – 8 PM</p>
                    </div>
                </a>

                <!-- mobile search icon (opens popup) -->
                <button id="mobileSearchOpen" class="header-action mobile-search-open" aria-label="Search">
                    <i data-lucide="search"></i>
                </button>

                <!-- wishlist -->
                <a href="<?= $storeUrl ?>wishlists" class="header-action" aria-label="Wishlist">
                    <span class="relative">
                        <i data-lucide="heart"></i>
                        <span class="count-badge wishlistItemsCount"
                            style="display: <?= $wishlist_count > 0 ? 'grid' : 'none' ?>; background: <?= htmlspecialchars($headerPrimary) ?>;">
                            <?= $wishlist_count ?>
                        </span>
                    </span>
                    <span class="action-label">Wishlist</span>
                </a>

                <!-- account -->
                <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                    <a href="<?= $storeUrl ?>profile" class="header-action" aria-label="Account">
                        <i data-lucide="user-round"></i>
                        <span class="action-label">Account</span>
                    </a>
                <?php else: ?>
                    <a href="<?= $storeUrl ?>login" class="header-action" aria-label="Login">
                        <i data-lucide="user-round"></i>
                        <span class="action-label">Account</span>
                    </a>
                <?php endif; ?>

                <!-- cart -->
                <a href="<?= $storeUrl ?>cart" class="header-action" aria-label="Cart">
                    <span class="relative">
                        <i data-lucide="shopping-cart"></i>
                        <span class="count-badge cartItemsCount"
                            style="display: <?= $cart_count > 0 ? 'grid' : 'none' ?>; background: <?= htmlspecialchars($headerPrimary) ?>;">
                            <?= $cart_count ?>
                        </span>
                    </span>
                    <span class="action-label">Cart</span>
                </a>
            </div>
        </div>
    </div>

    <!-- desktop navigation -->
    <div class="nav-shell hidden lg:block">
        <div class="max-w-[1450px] mx-auto px-6">
            <div class="nav-content">
                <div class="relative shrink-0">
                    <button id="allCategories" class="all-category">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                        <span>All Categories</span>
                        <i data-lucide="chevron-down" id="categoryChevron"></i>
                    </button>

                    <div id="categoryDropdown" class="category-dropdown hidden">
                        <?php foreach ($parentCats as $cat): ?>
                            <a href="<?= $storeUrl . 'category/' . htmlspecialchars($cat['slug']) ?>" class="category-option">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <nav class="nav-links">
                    <a href="<?= $storeUrl ?>" class="nav-item active"><i data-lucide="house"></i>Home</a>
                    <a href="<?= $storeUrl ?>shop-all" class="nav-item"><i data-lucide="shopping-bag"></i>Shop</a>
                    <a href="<?= $storeUrl ?>new-arrivals" class="nav-item"><i data-lucide="star"></i>New Arrivals</a>
                    <a href="<?= $storeUrl ?>offers" class="nav-item"><i data-lucide="percent"></i>Offers</a>
                    <a href="<?= $storeUrl ?>best-seller" class="nav-item"><i data-lucide="flame"></i>Best Sellers</a>
                    <a href="<?= $storeUrl ?>brands" class="nav-item"><i data-lucide="grid-2x2"></i>Brands</a>
                </nav>

                <a href="<?= $storeUrl ?>shop-all" class="deal-button">
                    <i data-lucide="tag" class="w-5 h-5"></i>
                    Today's Deals
                </a>
            </div>
        </div>
    </div>
</header>


<!-- ============================================================
     SEARCH POPUP (top → bottom slide) — used by both desktop & mobile
============================================================ -->
<div id="searchOverlay" class="search-overlay" aria-hidden="true">
    <div id="searchPanel" class="search-panel" role="dialog" aria-modal="true" aria-label="Search">

        <div class="search-head">
            <span class="search-brand" style="color: <?= htmlspecialchars($headerPrimary) ?>">
                <?= htmlspecialchars(strtoupper($storeName ?? 'SEARCH')) ?>
            </span>
            <button id="searchClose" class="search-close" aria-label="Close search">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="searchForm" class="search-form"
            action="<?= $storeUrl ?>search/" method="GET" autocomplete="off">
            <i data-lucide="search" class="w-5 h-5"></i>
            <input id="searchInput"
                name="q"
                type="search"
                placeholder="Search for products, brands, categories..."
                autocomplete="off">
            <button type="submit" class="search-submit"
                style="background: <?= htmlspecialchars($headerPrimary) ?>">
                Search
            </button>
        </form>

        <?php if (!empty($parentCats)): ?>
            <div class="search-popular">
                <p class="search-label">Popular Categories</p>
                <div class="search-chips">
                    <?php foreach (array_slice($parentCats, 0, 6) as $qc): ?>
                        <a href="<?= $storeUrl . 'category/' . htmlspecialchars($qc['slug']) ?>"
                            class="search-chip"
                            style="color: <?= htmlspecialchars($headerPrimary) ?>; border-color: <?= htmlspecialchars($headerPrimary) ?>55;">
                            <?= htmlspecialchars($qc['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- ============================================================
     STYLES
============================================================ -->
<style>
    /* ---- main header ---- */
    .main-header {
        position: sticky;
        top: 0;
        z-index: 100;
        background: rgba(255, 255, 255, .96);
        backdrop-filter: blur(18px);
        border-radius: 0 0 28px 28px;
        box-shadow: 0 12px 35px rgba(0, 0, 0, .06);
    }

    .header-top {
        min-height: 88px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    /* ---- header layout wrappers (desktop: left / center / right) ---- */
    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
        flex: 0 0 auto;
        min-width: 0;
    }

    .header-center {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 0;
    }

    .header-actions {
        flex: 0 0 auto;
        min-width: 0;
    }

    /* ---- logo ---- */
    .logo-link {
        min-width: 0;
        display: inline-flex;
    }

    .logo-img {
        height: 46px;
        width: auto;
        max-width: 200px;
        object-fit: contain;
        display: block;
    }

    .logo-title {
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -1px;
        white-space: nowrap;
        line-height: 1.1;
    }

    /* ---- desktop search button (opens popup) ---- */
    .desktop-search-btn {
        width: 100%;
        max-width: 510px;
        height: 51px;
        padding: 0 18px;
        align-items: center;
        gap: 10px;
        border-radius: 50px;
        border: 2px solid <?= htmlspecialchars($headerPrimary) ?>55;
        background: #fff;
        color: #9ca3af;
        font-size: 13px;
        text-align: left;
        transition: border-color .2s ease, box-shadow .2s ease;
        cursor: pointer;
    }

    .desktop-search-btn:hover {
        border-color: <?= htmlspecialchars($headerPrimary) ?>;
        box-shadow: 0 6px 18px <?= htmlspecialchars($headerPrimary) ?>22;
    }

    .desktop-search-btn i {
        color: <?= htmlspecialchars($headerPrimary) ?>;
    }

    /* ---- header actions ---- */
    .header-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        position: relative;
        transition: .2s;
        background: none;
        border: 0;
        cursor: pointer;
        color: inherit;
        font: inherit;
    }

    .header-action:hover {
        color: <?= htmlspecialchars($headerPrimary) ?>;
    }

    .header-action svg {
        width: 23px;
        height: 23px;
    }

    .action-label {
        font-size: 11px;
        font-weight: 600;
    }

    .count-badge {
        position: absolute;
        right: -10px;
        top: -8px;
        min-width: 17px;
        height: 17px;
        padding: 0 4px;
        color: white;
        border-radius: 30px;
        display: grid;
        place-items: center;
        font-size: 9px;
        font-weight: 800;
    }

    /* ---- desktop nav ---- */
    .nav-shell {
        border-top: 1px solid #f2e9f5;
    }

    .nav-content {
        height: 61px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .all-category {
        height: 45px;
        min-width: 190px;
        padding: 0 17px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        color: white;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        background: <?= htmlspecialchars($headerPrimary) ?>;
        box-shadow: 0 7px 18px <?= htmlspecialchars($headerPrimary) ?>29;
    }

    .nav-links {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-around;
        gap: 12px;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        font-size: 13px;
        font-weight: 700;
        position: relative;
        transition: .2s;
    }

    .nav-item svg {
        width: 19px;
        height: 19px;
    }

    .nav-item:hover,
    .nav-item.active {
        color: <?= htmlspecialchars($headerPrimary) ?>;
    }

    .nav-item.active::after {
        content: "";
        position: absolute;
        bottom: -19px;
        width: 100%;
        height: 3px;
        background: <?= htmlspecialchars($headerPrimary) ?>;
        border-radius: 10px;
    }

    .deal-button {
        height: 44px;
        padding: 0 17px;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        border: 1.5px solid <?= htmlspecialchars($headerPrimary) ?>66;
        border-radius: 50px;
        color: <?= htmlspecialchars($headerPrimary) ?>;
        font-size: 13px;
        font-weight: 800;
        background: <?= htmlspecialchars($headerPrimary) ?>11;
    }

    /* ---- category dropdown ---- */
    .category-dropdown {
        position: absolute;
        top: 55px;
        left: 0;
        width: 250px;
        padding: 10px;
        background: white;
        border: 1px solid #f1e4f6;
        border-radius: 17px;
        box-shadow: 0 15px 40px #4a14551c;
        z-index: 200;
        max-height: 420px;
        overflow-y: auto;
    }

    .category-option {
        display: block;
        padding: 11px 13px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
    }

    .category-option:hover {
        color: <?= htmlspecialchars($headerPrimary) ?>;
        background: <?= htmlspecialchars($headerPrimary) ?>11;
    }

    /* ---- mobile floating menu ---- */
    .mobile-menu-wrap {
        position: relative;
        display: none;
    }

    .mobile-menu-btn {
        width: 39px;
        height: 39px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: <?= htmlspecialchars($headerPrimary) ?>11;
        color: <?= htmlspecialchars($headerPrimary) ?>;
        border: 1px solid <?= htmlspecialchars($headerPrimary) ?>33;
    }

    .mobile-popup {
        position: absolute;
        top: calc(100% + 12px);
        left: 0;
        width: min(285px, calc(100vw - 28px));
        padding: 13px;
        background: rgba(255, 255, 255, .97);
        backdrop-filter: blur(22px);
        border: 1px solid #f0d9f4;
        border-radius: 21px;
        box-shadow:
            0 20px 60px rgba(81, 0, 105, .20),
            0 4px 15px rgba(225, 0, 145, .08);
        z-index: 500;
        transform-origin: top left;
        animation: popupOpen .22s ease-out;
        max-height: 75vh;
        overflow-y: auto;
    }

    @keyframes popupOpen {
        from {
            opacity: 0;
            transform: translateY(-8px) scale(.96);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .popup-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 9px 12px;
        border-bottom: 1px solid #f3e8f6;
        margin-bottom: 6px;
    }

    .popup-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        transition: .2s;
    }

    .popup-link svg {
        width: 18px;
        height: 18px;
        color: <?= htmlspecialchars($headerPrimary) ?>;
    }

    .popup-link:hover {
        background: <?= htmlspecialchars($headerPrimary) ?>11;
        color: <?= htmlspecialchars($headerPrimary) ?>;
        transform: translateX(3px);
    }

    .popup-deal {
        background: <?= htmlspecialchars($headerPrimary) ?>;
        color: white;
        margin-top: 8px;
    }

    .popup-deal svg {
        color: white;
    }

    .popup-deal:hover {
        color: white;
        background: <?= htmlspecialchars($headerHover) ?>;
    }

    /* ---- mobile search icon (opens popup) ---- */
    .mobile-search-open {
        display: none;
    }

    /* ============================================================
       SEARCH POPUP (slides down from top, used by both desktop & mobile)
    ============================================================ */
    .search-overlay {
        position: fixed;
        inset: 0;
        z-index: 200;
        background: rgba(0, 0, 0, .4);
        opacity: 0;
        visibility: hidden;
        transition: opacity .3s ease, visibility .3s ease;
    }

    .search-overlay.is-open {
        opacity: 1;
        visibility: visible;
    }

    .search-panel {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background: #fff;
        padding: 22px 6% 28px;
        transform: translateY(-100%);
        transition: transform .4s cubic-bezier(.22, 1, .36, 1);
        box-shadow: 0 20px 40px rgba(0, 0, 0, .15);
    }

    .search-overlay.is-open .search-panel {
        transform: translateY(0);
    }

    .search-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .search-brand {
        font-weight: 900;
        font-size: 20px;
        letter-spacing: -.5px;
    }

    .search-close {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        border-radius: 50%;
        border: 1px solid #e5e7eb;
        color: #6b7280;
        background: #fff;
        transition: background .2s ease, color .2s ease;
    }

    .search-close:hover {
        background: #f9fafb;
        color: <?= htmlspecialchars($headerPrimary) ?>;
    }

    .search-form {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 6px 6px 6px 18px;
        border: 2px solid <?= htmlspecialchars($headerPrimary) ?>;
        border-radius: 50px;
        background: #fff;
    }

    .search-form i {
        color: <?= htmlspecialchars($headerPrimary) ?>;
        flex-shrink: 0;
    }

    .search-form input {
        flex: 1;
        min-width: 0;
        border: 0;
        outline: 0;
        background: transparent;
        font-size: 15px;
        padding: 10px 0;
        color: #211329;
    }

    .search-submit {
        height: 42px;
        padding: 0 22px;
        border-radius: 50px;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity .2s ease;
    }

    .search-submit:hover {
        opacity: .9;
    }

    .search-popular {
        margin-top: 20px;
    }

    .search-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 10px;
    }

    .search-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .search-chip {
        padding: 8px 16px;
        border-radius: 50px;
        border: 1.5px solid;
        background: #fff;
        font-size: 12px;
        font-weight: 600;
        transition: background .2s ease, transform .2s ease;
    }

    .search-chip:hover {
        background: <?= htmlspecialchars($headerPrimary) ?>11;
        transform: translateY(-1px);
    }

    /* ---- desktop only: logo-desktop visible, logo-mobile hidden ---- */
    .logo-mobile {
        display: none;
    }

    .logo-desktop {
        display: inline-flex;
    }

    /* ============================================================
       RESPONSIVE
    ============================================================ */
    @media (max-width: 1100px) {
        .nav-links {
            gap: 8px;
        }

        .nav-item {
            font-size: 11px;
        }

        .all-category {
            min-width: 155px;
        }

        .deal-button {
            font-size: 11px;
            padding: 0 11px;
        }

        .support-block {
            display: none !important;
        }
    }

    /* ---- tablet & mobile: logo centered, search icon in actions ---- */
    @media (max-width: 1023px) {
        .header-top {
            min-height: 72px;
            gap: 10px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            /* menu | centered logo | actions */
            align-items: center;
        }

        .header-left {
            gap: 0;
            grid-column: 1;
        }

        .header-left .logo-desktop {
            display: none;
        }

        .header-center {
            grid-column: 2;
            justify-content: center;
        }

        .header-center .desktop-search-btn {
            display: none;
        }

        .logo-mobile {
            display: inline-flex;
        }

        .header-actions {
            grid-column: 3;
            gap: 14px;
        }

        .mobile-menu-wrap {
            display: block;
            flex-shrink: 0;
        }

        /* show search icon on tablet/mobile */
        .mobile-search-open {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }

    @media (max-width: 640px) {
        .header-top {
            min-height: 65px;
            gap: 8px;
        }

        .main-header {
            border-radius: 0 0 22px 22px;
        }

        .logo-img {
            height: 36px;
            max-width: 150px;
        }

        .logo-title {
            font-size: 17px;
            letter-spacing: -.5px;
        }

        .header-action svg {
            width: 20px;
            height: 20px;
        }

        .action-label {
            display: none;
        }

        .header-actions {
            gap: 12px;
        }

        .mobile-menu-btn {
            width: 34px;
            height: 34px;
        }

        .mobile-popup {
            left: 0;
            width: min(275px, calc(100vw - 24px));
        }

        .search-panel {
            padding: 18px 5% 22px;
        }

        .search-form {
            padding: 5px 5px 5px 14px;
        }

        .search-form input {
            font-size: 14px;
            padding: 9px 0;
        }

        .search-submit {
            height: 38px;
            padding: 0 16px;
            font-size: 12px;
        }
    }

    @media (max-width: 480px) {
        .main-header>div:first-child {
            padding-left: 8px;
            padding-right: 8px;
        }

        .header-top {
            min-height: 62px;
            gap: 6px;
        }

        .mobile-menu-wrap {
            flex: 0 0 30px;
        }

        .mobile-menu-btn {
            width: 30px;
            height: 32px;
            border-radius: 9px;
        }

        .logo-img {
            height: 32px;
            max-width: 120px;
        }

        .logo-title {
            font-size: clamp(14px, 4.4vw, 17px);
            letter-spacing: -.7px;
        }

        .header-actions {
            gap: 10px;
        }

        .header-action svg {
            width: 19px;
            height: 19px;
        }

        .count-badge {
            right: -7px;
            top: -7px;
            min-width: 15px;
            height: 15px;
            font-size: 8px;
        }

        .mobile-popup {
            left: 0;
            width: min(260px, calc(100vw - 20px));
        }

        .search-panel {
            padding: 16px 4% 20px;
        }
    }

    @media (max-width: 360px) {
        .header-top {
            gap: 4px;
        }

        .mobile-menu-wrap {
            flex-basis: 27px;
        }

        .mobile-menu-btn {
            width: 27px;
            height: 30px;
        }

        .header-actions {
            gap: 7px;
        }

        .logo-title {
            font-size: 14px;
        }
    }

    /* overflow guards */
    .main-header,
    .main-header>div,
    .header-top,
    .nav-shell,
    .nav-content {
        min-width: 0;
        max-width: 100%;
    }

    .header-top>*,
    .header-actions,
    .header-top>a {
        min-width: 0;
    }

    .header-actions {
        flex-shrink: 0;
    }
</style>


<!-- ============================================================
     SCRIPT
============================================================ -->
<script>
    (function() {
        'use strict';

        const AJAX_BASE_PATH = <?= json_encode($ajaxBase) ?>;
        const AJAX_BASE = window.location.origin + AJAX_BASE_PATH;
        const CART_COUNT_URL = AJAX_BASE + 'shop/ajax/get-product-count-and-price.php';
        const STORE_SLUG = <?= json_encode($storeSlug ?? '') ?>;

        if (typeof lucide !== 'undefined') lucide.createIcons();

        // ---------- Mobile popup ----------
        const mobileToggle = document.getElementById('mobileToggle');
        const mobilePopup = document.getElementById('mobilePopup');
        const closePopup = document.getElementById('closePopup');

        function openPopup() {
            mobilePopup.classList.remove('hidden');
            mobileToggle.innerHTML = '<i data-lucide="x" class="w-5 h-5"></i>';
            lucide.createIcons();
        }

        function hidePopup() {
            mobilePopup.classList.add('hidden');
            mobileToggle.innerHTML = '<i data-lucide="menu" class="w-5 h-5"></i>';
            lucide.createIcons();
        }

        mobileToggle?.addEventListener('click', function(e) {
            e.stopPropagation();
            if (mobilePopup.classList.contains('hidden')) openPopup();
            else hidePopup();
        });
        closePopup?.addEventListener('click', hidePopup);
        document.addEventListener('click', function(e) {
            if (!mobilePopup.contains(e.target) && !mobileToggle.contains(e.target)) hidePopup();
        });
        document.querySelectorAll('.popup-link').forEach(link => {
            link.addEventListener('click', hidePopup);
        });

        // ---------- Desktop category dropdown ----------
        const allCategories = document.getElementById('allCategories');
        const categoryDropdown = document.getElementById('categoryDropdown');
        const categoryChevron = document.getElementById('categoryChevron');

        allCategories?.addEventListener('click', function(e) {
            e.stopPropagation();
            categoryDropdown.classList.toggle('hidden');
            categoryChevron.style.transform =
                categoryDropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        });
        document.addEventListener('click', function(e) {
            if (!allCategories?.contains(e.target) && !categoryDropdown?.contains(e.target)) {
                categoryDropdown?.classList.add('hidden');
                if (categoryChevron) categoryChevron.style.transform = 'rotate(0deg)';
            }
        });

        // ---------- SEARCH POPUP (both desktop & mobile) ----------
        const searchOverlay = document.getElementById('searchOverlay');
        const searchPanel = document.getElementById('searchPanel');
        const searchClose = document.getElementById('searchClose');
        const searchInput = document.getElementById('searchInput');
        const desktopSearchBtn = document.getElementById('desktopSearchOpen');
        const mobileSearchBtn = document.getElementById('mobileSearchOpen');

        function openSearch() {
            searchOverlay.classList.add('is-open');
            searchOverlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            setTimeout(() => searchInput?.focus(), 350);
        }

        function closeSearch() {
            searchOverlay.classList.remove('is-open');
            searchOverlay.setAttribute('aria-hidden', 'true');
            if (!searchOverlay.classList.contains('is-open')) {
                document.body.style.overflow = '';
            }
        }

        desktopSearchBtn?.addEventListener('click', openSearch);
        mobileSearchBtn?.addEventListener('click', openSearch);
        searchClose?.addEventListener('click', closeSearch);

        searchOverlay?.addEventListener('click', function(e) {
            if (!searchPanel.contains(e.target)) closeSearch();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSearch();
                hidePopup();
                categoryDropdown?.classList.add('hidden');
                if (categoryChevron) categoryChevron.style.transform = 'rotate(0deg)';
            }
        });

        // ---------- Prevent empty search submit ----------
        document.getElementById('searchForm')?.addEventListener('submit', function(e) {
            const input = this.querySelector('input[name="q"]');
            if (!input || !input.value.trim()) {
                e.preventDefault();
                input?.focus();
            }
        });

        // ---------- Active nav ----------
        document.querySelectorAll('.nav-item').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // ---------- Live cart count refresh ----------
        window.updateHeaderCounts = function() {
            const url = new URL(CART_COUNT_URL, window.location.origin);
            if (STORE_SLUG) url.searchParams.set('store', STORE_SLUG);
            url.searchParams.set('_', Date.now().toString());

            fetch(url.toString(), {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(data => {
                    const rawCount = data.itemsCount ?? data.items_count ?? data.cartItemsCount ??
                        data.count ?? data.cart_count ?? data.total_items ?? 0;
                    const count = Math.max(0, parseInt(rawCount, 10) || 0);
                    document.querySelectorAll('.cartItemsCount').forEach(badge => {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = count > 0 ? 'grid' : 'none';
                    });
                })
                .catch(error => console.warn('[cart-count]', error));
        };

        window.refreshCartUI = window.refreshCartUI || window.updateHeaderCounts;
        document.addEventListener('DOMContentLoaded', () => setTimeout(window.updateHeaderCounts, 300));
    })();
</script>