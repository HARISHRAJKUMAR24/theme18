<!-- ============================================================
     BACKEND — DB COLORS + COUNTS + CATEGORIES
============================================================ -->
<?php
$headerPrimary = getData("color",       "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ed168b';
$headerHover   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#dc007b';

$cart_count = 0;
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

                <button id="desktopSearchOpen" class="desktop-search-btn hidden md:flex" aria-label="Search">
                    <i data-lucide="search" class="w-5 h-5"></i>
                    <span>Search products, brands, categories...</span>
                </button>
            </div>

            <!-- RIGHT: actions + mobile search icon -->
            <div class="header-actions flex items-center gap-3 sm:gap-5 px-4 sm:px-6 lg:px-8">
                <?php if (getSettings("phone")): ?>
                    <a href="tel:<?= getSettings("phone") ?>" class="support-block hidden xl:flex items-center gap-2">
                        <i data-lucide="headset" class="w-7 h-7"></i>
                        <div>
                            <p class="font-bold text-xs">Support</p>
                            <p class="text-[9px] text-gray-500"><?= formatPhoneNumber(getSettings("phone")) ?></p>
                        </div>
                    </a>
                <?php endif; ?>

                <button id="mobileSearchOpen" class="header-action mobile-search-open" aria-label="Search">
                    <i data-lucide="search"></i>
                </button>

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
     SEARCH POPUP (with SUGGESTIONS — this is what was missing)
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

        <!-- ★ SUGGESTIONS DROPDOWN (NEW) -->
        <div id="searchSuggestions" class="search-suggestions hidden">
            <div id="suggestionsList" class="search-suggestions-list"></div>
        </div>

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
     STYLES — unchanged + SUGGESTIONS STYLES (NEW)
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
        box-shadow: 0 12px 35px rgba(0, 0, 0, .06)
    }

    .header-top {
        min-height: 88px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
        flex: 0 0 auto;
        min-width: 0
    }

    .header-center {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 0
    }

    .header-actions {
        flex: 0 0 auto;
        min-width: 0
    }

    .logo-link {
        min-width: 0;
        display: inline-flex
    }

    .logo-img {
        height: 46px;
        width: auto;
        max-width: 200px;
        object-fit: contain;
        display: block
    }

    .logo-title {
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -1px;
        white-space: nowrap;
        line-height: 1.1
    }

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
        cursor: pointer
    }

    .desktop-search-btn:hover {
        border-color: <?= htmlspecialchars($headerPrimary) ?>;
        box-shadow: 0 6px 18px <?= htmlspecialchars($headerPrimary) ?>22
    }

    .desktop-search-btn i {
        color: <?= htmlspecialchars($headerPrimary) ?>
    }

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
        font: inherit
    }

    .header-action:hover {
        color: <?= htmlspecialchars($headerPrimary) ?>
    }

    .header-action svg {
        width: 23px;
        height: 23px
    }

    .action-label {
        font-size: 11px;
        font-weight: 600
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
        font-weight: 800
    }

    .nav-shell {
        border-top: 1px solid #f2e9f5
    }

    .nav-content {
        height: 61px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px
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
        box-shadow: 0 7px 18px <?= htmlspecialchars($headerPrimary) ?>29
    }

    .nav-links {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-around;
        gap: 12px
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        font-size: 13px;
        font-weight: 700;
        position: relative;
        transition: .2s
    }

    .nav-item svg {
        width: 19px;
        height: 19px
    }

    .nav-item:hover,
    .nav-item.active {
        color: <?= htmlspecialchars($headerPrimary) ?>
    }

    .nav-item.active::after {
        content: "";
        position: absolute;
        bottom: -19px;
        width: 100%;
        height: 3px;
        background: <?= htmlspecialchars($headerPrimary) ?>;
        border-radius: 10px
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
        background: <?= htmlspecialchars($headerPrimary) ?>11
    }

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
        overflow-y: auto
    }

    .category-option {
        display: block;
        padding: 11px 13px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600
    }

    .category-option:hover {
        color: <?= htmlspecialchars($headerPrimary) ?>;
        background: <?= htmlspecialchars($headerPrimary) ?>11
    }

    .mobile-menu-wrap {
        position: relative;
        display: none
    }

    .mobile-menu-btn {
        width: 39px;
        height: 39px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: <?= htmlspecialchars($headerPrimary) ?>11;
        color: <?= htmlspecialchars($headerPrimary) ?>;
        border: 1px solid <?= htmlspecialchars($headerPrimary) ?>33
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
        box-shadow: 0 20px 60px rgba(81, 0, 105, .20), 0 4px 15px rgba(225, 0, 145, .08);
        z-index: 500;
        transform-origin: top left;
        animation: popupOpen .22s ease-out;
        max-height: 75vh;
        overflow-y: auto
    }

    @keyframes popupOpen {
        from {
            opacity: 0;
            transform: translateY(-8px) scale(.96)
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1)
        }
    }

    .popup-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 9px 12px;
        border-bottom: 1px solid #f3e8f6;
        margin-bottom: 6px
    }

    .popup-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        transition: .2s
    }

    .popup-link svg {
        width: 18px;
        height: 18px;
        color: <?= htmlspecialchars($headerPrimary) ?>
    }

    .popup-link:hover {
        background: <?= htmlspecialchars($headerPrimary) ?>11;
        color: <?= htmlspecialchars($headerPrimary) ?>;
        transform: translateX(3px)
    }

    .popup-deal {
        background: <?= htmlspecialchars($headerPrimary) ?>;
        color: white;
        margin-top: 8px
    }

    .popup-deal svg {
        color: white
    }

    .popup-deal:hover {
        color: white;
        background: <?= htmlspecialchars($headerHover) ?>
    }

    .mobile-search-open {
        display: none
    }

    /* ============================================================
       SEARCH POPUP
    ============================================================ */
    .search-overlay {
        position: fixed;
        inset: 0;
        z-index: 200;
        background: rgba(0, 0, 0, .4);
        opacity: 0;
        visibility: hidden;
        transition: opacity .3s ease, visibility .3s ease
    }

    .search-overlay.is-open {
        opacity: 1;
        visibility: visible
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
        position: absolute
    }

    .search-overlay.is-open .search-panel {
        transform: translateY(0)
    }

    .search-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px
    }

    .search-brand {
        font-weight: 900;
        font-size: 20px;
        letter-spacing: -.5px
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
        transition: background .2s ease, color .2s ease
    }

    .search-close:hover {
        background: #f9fafb;
        color: <?= htmlspecialchars($headerPrimary) ?>
    }

    .search-form {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 6px 6px 6px 18px;
        border: 2px solid <?= htmlspecialchars($headerPrimary) ?>;
        border-radius: 50px;
        background: #fff
    }

    .search-form i {
        color: <?= htmlspecialchars($headerPrimary) ?>;
        flex-shrink: 0
    }

    .search-form input {
        flex: 1;
        min-width: 0;
        border: 0;
        outline: 0;
        background: transparent;
        font-size: 15px;
        padding: 10px 0;
        color: #211329
    }

    .search-submit {
        height: 42px;
        padding: 0 22px;
        border-radius: 50px;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity .2s ease
    }

    .search-submit:hover {
        opacity: .9
    }

    /* ★ SUGGESTIONS (NEW) */
    .search-suggestions {
        position: relative;
        margin-top: 14px;
        max-height: 55vh;
        overflow-y: auto;
        border-radius: 14px;
        border: 1px solid #f1e4f6;
        background: #fff;
        box-shadow: 0 12px 40px rgba(120, 0, 168, .10);
    }

    .search-suggestions.hidden {
        display: none
    }

    .search-suggestions-list {
        padding: 6px 0
    }

    .suggestion-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 16px;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f6f1f9;
        transition: background-color .15s ease;
    }

    .suggestion-item:last-child {
        border-bottom: none
    }

    .suggestion-item:hover {
        background: #faf5ff
    }

    .suggestion-image {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
        background: #f3f4f6;
        border: 1px solid #eee;
    }

    .suggestion-info {
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 8px
    }

    .suggestion-name {
        font-size: 14px;
        font-weight: 500;
        color: #1f2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .suggestion-badge {
        font-size: 10px;
        background: #fef3c7;
        color: #d97706;
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: 600;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .suggestion-item .highlight {
        color: <?= htmlspecialchars($headerHover) ?>;
        font-weight: 600
    }

    .suggestion-empty,
    .suggestion-loading {
        padding: 20px 16px;
        text-align: center;
        font-size: 14px;
        color: #9ca3af;
    }

    @media (max-width:768px) {
        .suggestion-item {
            padding: 8px 12px
        }

        .suggestion-image {
            width: 34px;
            height: 34px
        }

        .suggestion-name {
            font-size: 13px
        }
    }

    .search-popular {
        margin-top: 20px
    }

    .search-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 10px
    }

    .search-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px
    }

    .search-chip {
        padding: 8px 16px;
        border-radius: 50px;
        border: 1.5px solid;
        background: #fff;
        font-size: 12px;
        font-weight: 600;
        transition: background .2s ease, transform .2s ease
    }

    .search-chip:hover {
        background: <?= htmlspecialchars($headerPrimary) ?>11;
        transform: translateY(-1px)
    }

    .logo-mobile {
        display: none
    }

    .logo-desktop {
        display: inline-flex
    }

    /* RESPONSIVE (unchanged) */
    @media (max-width:1100px) {
        .nav-links {
            gap: 8px
        }

        .nav-item {
            font-size: 11px
        }

        .all-category {
            min-width: 155px
        }

        .deal-button {
            font-size: 11px;
            padding: 0 11px
        }

        .support-block {
            display: none !important
        }
    }

    @media (max-width:1023px) {
        .header-top {
            min-height: 72px;
            gap: 10px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center
        }

        .header-left {
            gap: 0;
            grid-column: 1
        }

        .header-left .logo-desktop {
            display: none
        }

        .header-center {
            grid-column: 2;
            justify-content: center
        }

        .header-center .desktop-search-btn {
            display: none
        }

        .logo-mobile {
            display: inline-flex
        }

        .header-actions {
            grid-column: 3;
            gap: 14px
        }

        .mobile-menu-wrap {
            display: block;
            flex-shrink: 0
        }

        .mobile-search-open {
            display: flex;
            align-items: center;
            justify-content: center
        }
    }

    @media (max-width:640px) {
        .header-top {
            min-height: 65px;
            gap: 8px
        }

        .main-header {
            border-radius: 0 0 22px 22px
        }

        .logo-img {
            height: 36px;
            max-width: 150px
        }

        .logo-title {
            font-size: 17px;
            letter-spacing: -.5px
        }

        .header-action svg {
            width: 20px;
            height: 20px
        }

        .action-label {
            display: none
        }

        .header-actions {
            gap: 12px
        }

        .mobile-menu-btn {
            width: 34px;
            height: 34px
        }

        .mobile-popup {
            left: 0;
            width: min(275px, calc(100vw - 24px))
        }

        .search-panel {
            padding: 18px 5% 22px
        }

        .search-form {
            padding: 5px 5px 5px 14px
        }

        .search-form input {
            font-size: 14px;
            padding: 9px 0
        }

        .search-submit {
            height: 38px;
            padding: 0 16px;
            font-size: 12px
        }
    }

    @media (max-width:480px) {
        .main-header>div:first-child {
            padding-left: 8px;
            padding-right: 8px
        }

        .header-top {
            min-height: 62px;
            gap: 6px
        }

        .mobile-menu-wrap {
            flex: 0 0 30px
        }

        .mobile-menu-btn {
            width: 30px;
            height: 32px;
            border-radius: 9px
        }

        .logo-img {
            height: 32px;
            max-width: 120px
        }

        .logo-title {
            font-size: clamp(14px, 4.4vw, 17px);
            letter-spacing: -.7px
        }

        .header-actions {
            gap: 10px
        }

        .header-action svg {
            width: 19px;
            height: 19px
        }

        .count-badge {
            right: -7px;
            top: -7px;
            min-width: 15px;
            height: 15px;
            font-size: 8px
        }

        .mobile-popup {
            left: 0;
            width: min(260px, calc(100vw - 20px))
        }

        .search-panel {
            padding: 16px 4% 20px
        }
    }

    @media (max-width:360px) {
        .header-top {
            gap: 4px
        }

        .mobile-menu-wrap {
            flex-basis: 27px
        }

        .mobile-menu-btn {
            width: 27px;
            height: 30px
        }

        .header-actions {
            gap: 7px
        }

        .logo-title {
            font-size: 14px
        }
    }

    .main-header,
    .main-header>div,
    .header-top,
    .nav-shell,
    .nav-content {
        min-width: 0;
        max-width: 100%
    }

    .header-top>*,
    .header-actions,
    .header-top>a {
        min-width: 0
    }

    .header-actions {
        flex-shrink: 0
    }
</style>


<!-- ============================================================
     SCRIPT — WITH SEARCH SUGGESTIONS (NEW)
============================================================ -->
<script>
    (function() {
        'use strict';

        /* ---- URLs from PHP ---- */
        const AJAX_BASE_PATH = <?= json_encode($ajaxBase) ?>;
        const AJAX_BASE = window.location.origin + AJAX_BASE_PATH;
        const STORE_URL = <?= json_encode($storeUrl) ?>;
        const UPLOADS_URL_JS = <?= json_encode(UPLOADS_URL) ?>;
        const APP_URL_JS = <?= json_encode(APP_URL) ?>;
        const STORE_SLUG = <?= json_encode($storeSlug ?? '') ?>;
        const SEARCH_SUGG_URL = AJAX_BASE + 'shop/ajax/get-search-suggestions.php';
        const CART_COUNT_URL = AJAX_BASE + 'shop/ajax/get-product-count-and-price.php';

        if (typeof lucide !== 'undefined') lucide.createIcons();

        /* ---- Elements ---- */
        const mobileToggle = document.getElementById('mobileToggle');
        const mobilePopup = document.getElementById('mobilePopup');
        const closePopup = document.getElementById('closePopup');
        const allCategories = document.getElementById('allCategories');
        const categoryDropdown = document.getElementById('categoryDropdown');
        const categoryChevron = document.getElementById('categoryChevron');
        const searchOverlay = document.getElementById('searchOverlay');
        const searchPanel = document.getElementById('searchPanel');
        const searchClose = document.getElementById('searchClose');
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        const desktopSearchBtn = document.getElementById('desktopSearchOpen');
        const mobileSearchBtn = document.getElementById('mobileSearchOpen');
        const suggestionsBox = document.getElementById('searchSuggestions');
        const suggestionsList = document.getElementById('suggestionsList');

        /* ---------- Mobile popup ---------- */
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

        mobileToggle?.addEventListener('click', e => {
            e.stopPropagation();
            mobilePopup.classList.contains('hidden') ? openPopup() : hidePopup();
        });
        closePopup?.addEventListener('click', hidePopup);
        document.addEventListener('click', e => {
            if (!mobilePopup.contains(e.target) && !mobileToggle.contains(e.target)) hidePopup();
        });
        document.querySelectorAll('.popup-link').forEach(l => l.addEventListener('click', hidePopup));

        /* ---------- Category dropdown ---------- */
        allCategories?.addEventListener('click', e => {
            e.stopPropagation();
            categoryDropdown.classList.toggle('hidden');
            categoryChevron.style.transform = categoryDropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        });
        document.addEventListener('click', e => {
            if (!allCategories?.contains(e.target) && !categoryDropdown?.contains(e.target)) {
                categoryDropdown?.classList.add('hidden');
                if (categoryChevron) categoryChevron.style.transform = 'rotate(0deg)';
            }
        });

        /* ---------- Search popup ---------- */
        function openSearch() {
            searchOverlay.classList.add('is-open');
            searchOverlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            setTimeout(() => searchInput?.focus(), 350);
        }

        function closeSearch() {
            searchOverlay.classList.remove('is-open');
            searchOverlay.setAttribute('aria-hidden', 'true');
            suggestionsBox?.classList.add('hidden');
            if (!searchOverlay.classList.contains('is-open')) document.body.style.overflow = '';
        }
        desktopSearchBtn?.addEventListener('click', openSearch);
        mobileSearchBtn?.addEventListener('click', openSearch);
        searchClose?.addEventListener('click', closeSearch);
        searchOverlay?.addEventListener('click', e => {
            if (!searchPanel.contains(e.target)) closeSearch();
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeSearch();
                hidePopup();
                categoryDropdown?.classList.add('hidden');
                if (categoryChevron) categoryChevron.style.transform = 'rotate(0deg)';
            }
        });
        searchForm?.addEventListener('submit', e => {
            const val = (searchInput?.value || '').trim();
            if (!val) {
                e.preventDefault();
                searchInput?.focus();
            }
        });

        /* ---------- Search suggestions (NEW) ---------- */
        let debounceTimer = null;
        let lastQuery = '';

        const escapeRegExp = s => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const highlightText = (text, q) => !q ? text : text.replace(
            new RegExp('(' + escapeRegExp(q) + ')', 'gi'),
            '<span class="highlight">$1</span>'
        );

        function fetchSuggestions(query) {
            if (!query || query.length < 1) {
                suggestionsBox?.classList.add('hidden');
                return;
            }
            lastQuery = query;
            suggestionsList.innerHTML = '<div class="suggestion-loading">Searching...</div>';
            suggestionsBox?.classList.remove('hidden');

            const form = new FormData();
            form.append('q', query);
            form.append('store', STORE_SLUG);

            fetch(SEARCH_SUGG_URL, {
                    method: 'POST',
                    body: form
                })
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(res => {
                    if (lastQuery !== query) return;
                    if (res.success && Array.isArray(res.data) && res.data.length > 0) {
                        let html = '';
                        res.data.forEach(p => {
                            const image = (p.image && p.image !== 'no-image.png') ?
                                UPLOADS_URL_JS + p.image :
                                APP_URL_JS + 'assets/image/no-image.png';
                            const url = STORE_URL + 'product/' + (p.slug || '');
                            const badge = p.badge || '';
                            html += `
                            <a href="${url}" class="suggestion-item">
                                <img src="${image}" alt="${p.name || ''}" class="suggestion-image"
                                     onerror="this.src='${APP_URL_JS}assets/image/no-image.png'">
                                <div class="suggestion-info">
                                    <div class="suggestion-name">${highlightText(p.name || '', query)}</div>
                                    ${badge ? `<span class="suggestion-badge">${badge}</span>` : ''}
                                </div>
                            </a>`;
                        });
                        suggestionsList.innerHTML = html;
                    } else {
                        suggestionsList.innerHTML =
                            '<div class="suggestion-empty">No products found for "<strong>' +
                            query.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</strong>"</div>';
                    }
                })
                .catch(err => {
                    console.error('[search-suggestions]', err, 'URL:', SEARCH_SUGG_URL);
                    suggestionsList.innerHTML =
                        '<div class="suggestion-empty">Could not load suggestions.<br><small style="font-size:11px;color:#bbb">' +
                        SEARCH_SUGG_URL + '</small></div>';
                });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const q = this.value.trim();
                if (!q) {
                    suggestionsBox?.classList.add('hidden');
                    return;
                }
                debounceTimer = setTimeout(() => fetchSuggestions(q), 300);
            });
            searchInput.addEventListener('focus', function() {
                const q = this.value.trim();
                if (q.length > 0) fetchSuggestions(q);
            });
        }

        /* hide suggestions when clicking outside the form / list */
        document.addEventListener('click', e => {
            if (!searchPanel.contains(e.target)) return;
            if (!e.target.closest('#searchForm') && !e.target.closest('#searchSuggestions')) {
                suggestionsBox?.classList.add('hidden');
            }
        });

        /* ---------- Cart count refresh ---------- */
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
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(data => {
                    const raw = data.itemsCount ?? data.items_count ?? data.cartItemsCount ??
                        data.count ?? data.cart_count ?? data.total_items ?? 0;
                    const count = Math.max(0, parseInt(raw, 10) || 0);
                    document.querySelectorAll('.cartItemsCount').forEach(badge => {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = count > 0 ? 'grid' : 'none';
                    });
                })
                .catch(err => console.warn('[cart-count]', err));
        };
        window.refreshCartUI = window.refreshCartUI || window.updateHeaderCounts;
        document.addEventListener('DOMContentLoaded', () => setTimeout(window.updateHeaderCounts, 300));
    })();
</script>