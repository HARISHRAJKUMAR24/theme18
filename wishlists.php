<?php
include_once __DIR__ . "/includes/files_includes.php";
$primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?: '#ff007f';
$hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?: '#ec4899';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>
    <style>
        :root {
            --primary-color: <?= htmlspecialchars($primary_color, ENT_QUOTES, 'UTF-8') ?>;
            --hover-color: <?= htmlspecialchars($hover_color, ENT_QUOTES, 'UTF-8') ?>;
            --wishlist-ink: #18181b;
            --wishlist-muted: #71717a;
            --wishlist-line: #e4e4e7;
        }

        .wishlist-hero {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, color-mix(in srgb, var(--primary-color) 10%, white), #fff 58%, color-mix(in srgb, var(--hover-color) 9%, white));
            border: 1px solid color-mix(in srgb, var(--primary-color) 18%, white);
        }

        .wishlist-hero:after {
            content: '♡';
            position: absolute;
            right: 3%;
            top: -55px;
            font-size: clamp(150px, 22vw, 280px);
            line-height: 1;
            color: color-mix(in srgb, var(--primary-color) 9%, transparent);
            pointer-events: none;
        }

        .wishlist-kicker {
            color: var(--primary-color);
            letter-spacing: .22em;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .wishlist-title {
            font-family: Georgia, serif;
            letter-spacing: -.04em;
        }

        .wishlist-layout {
            display: grid;
            grid-template-columns: minmax(240px, .78fr) minmax(0, 2fr);
            gap: 28px;
            align-items: start;
        }

        .wishlist-panel {
            background: #fff;
            border: 1px solid var(--wishlist-line);
            box-shadow: 0 12px 35px rgba(24, 24, 27, .035);
        }

        .wishlist-avatar {
            width: 86px;
            height: 86px;
            border: 3px solid #fff;
            box-shadow: 0 0 0 1px color-mix(in srgb, var(--primary-color) 35%, white);
        }

        .wishlist-action {
            background: var(--primary-color);
            border: 1px solid var(--primary-color);
            color: #fff;
            transition: background .2s, border-color .2s, transform .2s;
        }

        .wishlist-action:hover {
            background: var(--hover-color);
            border-color: var(--hover-color);
            color: #fff;
            transform: translateY(-1px);
        }

        .wishlist-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid transparent;
            color: #52525b;
            transition: background .2s, color .2s, border-color .2s;
        }

        .wishlist-link:hover {
            background: color-mix(in srgb, var(--primary-color) 6%, white);
            border-color: color-mix(in srgb, var(--primary-color) 20%, white);
            color: var(--primary-color);
        }

        .wishlist-link.active {
            background: color-mix(in srgb, var(--primary-color) 9%, white);
            border-color: color-mix(in srgb, var(--primary-color) 28%, white);
            color: var(--primary-color);
            font-weight: 700;
        }

        .wishlist-link-icon {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            background: #f4f4f5;
            border-radius: 10px;
            font-size: 18px;
        }

        .wishlist-link.active .wishlist-link-icon,
        .wishlist-link:hover .wishlist-link-icon {
            background: var(--primary-color);
            color: #fff;
        }

        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            width: 100%;
        }

        .product-card {
            min-width: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #eeeef0;
            background: #fff;
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s, transform .2s;
        }

        .product-card:hover {
            border-color: color-mix(in srgb, var(--primary-color) 35%, white);
            box-shadow: 0 12px 28px rgba(24, 24, 27, .07);
            transform: translateY(-2px);
        }

        .wishlist-empty-icon {
            width: 82px;
            height: 82px;
            border-radius: 24px;
            display: grid;
            place-items: center;
            background: color-mix(in srgb, var(--primary-color) 9%, white);
            color: var(--primary-color);
            font-size: 36px;
        }

        @media(min-width:768px) {
            .wishlist-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
            }
        }

        @media(min-width:1280px) {
            .wishlist-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media(max-width:767px) {
            .wishlist-layout {
                grid-template-columns: 1fr;
                gap: 20px
            }

            .wishlist-hero:after {
                right: -18px;
                top: -15px;
                font-size: 145px
            }

            .wishlist-grid {
                gap: 10px
            }

            .wishlist-avatar {
                width: 72px;
                height: 72px
            }
        }

        @media(max-width:360px) {
            .wishlist-grid {
                grid-template-columns: 1fr
            }

            .wishlist-hero-content {
                padding: 24px 18px !important
            }
        }

        /* =========================================================
           THEME 18 WISHLIST SKIN — DB COLOR DRIVEN
           Keeps the existing wishlist PHP and product functionality.
        ========================================================= */
        body {
            background: #fff8fc !important;
            color: #24121d;
        }
        .wishlist-hero {
            border: 0;
            border-radius: 26px;
            background:
                radial-gradient(circle at 88% 15%, color-mix(in srgb, var(--primary-color) 13%, transparent) 0 9%, transparent 10%),
                radial-gradient(circle at 78% 90%, color-mix(in srgb, var(--hover-color) 10%, transparent) 0 7%, transparent 8%),
                linear-gradient(125deg, color-mix(in srgb, var(--primary-color) 9%, white), #fff 54%, color-mix(in srgb, var(--hover-color) 8%, white));
        }
        .wishlist-hero:after {
            content: '♡';
            right: 4%;
            top: 50%;
            transform: translateY(-50%) rotate(-12deg);
            font-size: clamp(145px, 20vw, 245px);
            color: color-mix(in srgb, var(--primary-color) 8%, transparent);
        }
        .wishlist-kicker {
            color: var(--primary-color);
            letter-spacing: .16em;
        }
        .wishlist-title {
            font-family: inherit;
            letter-spacing: -.045em;
            color: #24121d;
        }
        .wishlist-panel {
            border: 1px solid color-mix(in srgb, var(--primary-color) 13%, #f1e5ed);
            border-radius: 22px;
            box-shadow: 0 8px 28px rgba(70, 20, 48, .045);
        }
        .wishlist-avatar {
            border-color: #fff;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 18%, white);
        }
        .wishlist-action {
            background: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 12px;
            box-shadow: 0 7px 18px color-mix(in srgb, var(--primary-color) 22%, transparent);
        }
        .wishlist-action:hover {
            background: var(--hover-color);
            border-color: var(--hover-color);
            box-shadow: 0 9px 22px color-mix(in srgb, var(--hover-color) 22%, transparent);
        }
        .wishlist-link {
            border-radius: 13px;
            color: #62515b;
        }
        .wishlist-link:hover,
        .wishlist-link.active {
            background: color-mix(in srgb, var(--primary-color) 8%, white);
            border-color: color-mix(in srgb, var(--primary-color) 18%, white);
            color: var(--primary-color);
        }
        .wishlist-link-icon {
            border-radius: 11px;
            background: #fff1f7;
            color: var(--primary-color);
        }
        .wishlist-link.active .wishlist-link-icon,
        .wishlist-link:hover .wishlist-link-icon {
            background: var(--primary-color);
            color: #fff;
        }
        .wishlist-grid {
            gap: 18px;
        }
        .product-card {
            border: 1px solid #f1e5ed;
            border-radius: 18px;
            background: #fff;
            overflow: hidden;
            transition: border-color .22s ease, transform .22s ease;
        }
        .product-card:hover {
            border-color: color-mix(in srgb, var(--primary-color) 38%, white);
            box-shadow: none;
            transform: translateY(-3px);
        }
        .wishlist-empty-icon {
            border-radius: 22px;
            background: color-mix(in srgb, var(--primary-color) 9%, white);
            color: var(--primary-color);
        }
        @media (max-width: 767px) {
            .wishlist-hero { border-radius: 20px; }
            .wishlist-layout { gap: 16px; }
            .wishlist-panel { border-radius: 18px; }
            .wishlist-grid { gap: 12px; }
            .product-card { border-radius: 14px; }
        }
        @media (max-width: 380px) {
            .wishlist-grid { grid-template-columns: 1fr; }
        }

    </style>
</head>

<body class="min-h-screen bg-neutral-50 font-sans text-neutral-900">

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <main class="mx-auto w-full max-w-[1440px] px-4 py-7 sm:px-6 sm:py-10 lg:px-8 lg:py-12">
        <section class="wishlist-hero mb-7 sm:mb-9">
            <div class="wishlist-hero-content relative z-10 px-6 py-8 sm:px-10 sm:py-10 lg:px-12">
                <p class="wishlist-kicker">Your personal collection</p>
                <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 class="wishlist-title text-4xl font-bold sm:text-5xl lg:text-6xl">My Wishlist<span style="color:var(--primary-color)">.</span></h1>
                        <p class="mt-3 max-w-xl text-sm leading-6 text-neutral-600 sm:text-base">The pieces you love, all in one place. Ready when you are.</p>
                    </div>
                    <div class="hidden items-center gap-2 border border-white/80 bg-white/75 px-4 py-3 text-xs font-bold uppercase tracking-widest text-neutral-600 sm:flex"><span class="text-lg" style="color:var(--primary-color)">♡</span> Saved for later</div>
                </div>
            </div>
        </section>

        <div class="wishlist-layout">
            <aside class="wishlist-panel p-5 sm:p-6">
                <div class="mb-6 flex flex-col items-center border-b border-neutral-100 pb-6 text-center">
                    <div class="relative mb-4">
                        <img id="previewImage" src="<?= !empty(customer('photo')) ? UPLOADS_URL . customer('photo') : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80' ?>" alt="User Profile" class="wishlist-avatar rounded-full object-cover">
                        <a href="<?= $storeUrl ?>profile" aria-label="Edit profile" class="wishlist-action absolute -bottom-1 -right-1 grid h-8 w-8 place-items-center rounded-full text-sm shadow-md"><i class="bx bx-edit"></i></a>
                    </div>
                    <h2 class="wishlist-title text-xl font-bold"><?= htmlspecialchars((string)customer("name"), ENT_QUOTES, 'UTF-8') ?></h2>
                    <p class="mt-1 max-w-full break-all text-sm text-neutral-500"><?= htmlspecialchars((string)customer("email"), ENT_QUOTES, 'UTF-8') ?></p>
                    <span class="mt-3 inline-flex items-center gap-2 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-widest" style="background:color-mix(in srgb,var(--primary-color) 9%,white);color:var(--primary-color)"><span class="h-1.5 w-1.5 rounded-full" style="background:var(--primary-color)"></span> My account</span>
                </div>
                <nav class="space-y-2" aria-label="Account navigation">
                    <a href="<?= $storeUrl ?>profile" class="wishlist-link"><span class="wishlist-link-icon"><i class="bx bx-user"></i></span><span class="text-sm font-medium">My Profile</span><i class="bx bx-chevron-right ml-auto text-lg"></i></a>
                    <a href="<?= $storeUrl ?>orders" class="wishlist-link"><span class="wishlist-link-icon"><i class="bx bx-package"></i></span><span class="text-sm font-medium">My Orders</span><i class="bx bx-chevron-right ml-auto text-lg"></i></a>
                    <a href="<?= $storeUrl ?>wishlists" class="wishlist-link active" aria-current="page"><span class="wishlist-link-icon"><i class="bx bx-heart"></i></span><span class="text-sm">My Wishlist</span><i class="bx bx-chevron-right ml-auto text-lg"></i></a>
                    <a href="<?= $storeUrl ?>logout" class="wishlist-link hover:!border-red-200 hover:!bg-red-50 hover:!text-red-600"><span class="wishlist-link-icon"><i class="bx bx-log-out"></i></span><span class="text-sm font-medium">Logout</span><i class="bx bx-chevron-right ml-auto text-lg"></i></a>
                </nav>
            </aside>

            <section class="min-w-0">
                <?php
                $productsStmt = getWishlists();
                $products = $productsStmt ? $productsStmt->fetchAll(PDO::FETCH_ASSOC) : [];
                ?>
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="wishlist-kicker">Saved items</p>
                        <h2 class="wishlist-title mt-1 text-2xl font-bold sm:text-3xl">Your favourites</h2>
                    </div>
                    <span class="border border-neutral-200 bg-white px-3 py-2 text-xs font-semibold text-neutral-600"><?= count($products) ?> <?= count($products) === 1 ? 'item' : 'items' ?></span>
                </div>
                <?php if (!empty($products)): ?>
                    <div class="wishlist-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card"><?= getProductHtml($product['product_id']) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="wishlist-panel flex min-h-[390px] flex-col items-center justify-center px-5 py-14 text-center sm:px-10">
                        <div class="wishlist-empty-icon"><i class="bx bx-heart"></i></div>
                        <p class="wishlist-kicker mt-6">A little room for love</p>
                        <h3 class="wishlist-title mt-2 text-2xl font-bold sm:text-3xl">Your wishlist is waiting</h3>
                        <p class="mt-3 max-w-sm text-sm leading-6 text-neutral-500">You haven't saved anything just yet. Explore the shop and tap the heart on products you love.</p>
                        <a href="<?= $storeUrl ?>" class="wishlist-action mt-7 inline-flex items-center gap-2 px-6 py-3 text-xs font-bold uppercase tracking-[.16em]"><i class="bx bx-shopping-bag text-base"></i> Explore the shop <span aria-hidden="true">→</span></a>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
       
    <?php include_once __DIR__ . "/includes/footer.php"; ?>
</body>

</html>