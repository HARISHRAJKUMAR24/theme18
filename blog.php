<?php include_once __DIR__ . "/includes/files_includes.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>

    <style>
        :root {
            --t18-primary: <?= htmlspecialchars(getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?: '#64748b') ?>;
            --t18-hover: <?= htmlspecialchars(getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?: '#334155') ?>;
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
            height: 600px; /* Limit height so they stay at top */
        }

        .t18-shape {
            position: absolute;
            border-radius: 35%;
            background: linear-gradient(135deg, color-mix(in srgb, var(--t18-primary) 18%, #fff) 0%, color-mix(in srgb, var(--t18-hover) 12%, #fff) 100%);
            box-shadow: 0 20px 40px color-mix(in srgb, var(--t18-primary) 15%, transparent);
            filter: blur(2px);
            opacity: 0.7;
            animation: floatShape 12s ease-in-out infinite alternate;
        }

        .t18-shape-1 {
            width: 350px; height: 350px;
            top: -100px; left: -100px;
            transform: rotate(25deg);
        }

        .t18-shape-2 {
            width: 450px; height: 450px;
            top: 50px; right: -150px;
            transform: rotate(-15deg);
            animation-delay: -5s;
        }

        .t18-shape-3 {
            width: 200px; height: 200px;
            top: 250px; left: 20%;
            opacity: 0.4;
            animation-duration: 18s;
        }

        @keyframes floatShape {
            0% { transform: translate(0, 0) rotate(0deg) scale(1); }
            100% { transform: translate(20px, -20px) rotate(15deg) scale(1.05); }
        }

        /* ==================== BLOG HEADER ==================== */
        .t18-blog-head {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 60px 16px 40px;
        }

        .t18-blog-kicker {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--t18-primary);
            background: color-mix(in srgb, var(--t18-primary) 10%, #fff);
            padding: 8px 18px;
            border-radius: 999px;
            margin-bottom: 20px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 20%, transparent);
        }

        .t18-blog-title {
            margin: 0 0 16px;
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 900;
            color: #111827;
            line-height: 1.1;
            letter-spacing: -1px;
        }

        .t18-blog-sub {
            display: block;
            font-size: clamp(1rem, 1.5vw, 1.15rem);
            color: #4b5563;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* ==================== BLOG GRID ==================== */
        .t18-blog-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            position: relative;
            z-index: 1;
        }

        @media(min-width: 640px) {
            .t18-blog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media(min-width: 1024px) {
            .t18-blog-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 32px; }
        }

        /* ==================== BLOG CARD ==================== */
        .t18-blog-card {
            display: flex;
            flex-direction: column;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            overflow: hidden;
            text-decoration: none;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .t18-blog-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px color-mix(in srgb, var(--t18-primary) 25%, transparent);
            border-color: color-mix(in srgb, var(--t18-primary) 30%, transparent);
        }

        /* Image Wrapper */
        .t18-blog-imgwrap {
            position: relative;
            display: block;
            width: 100%;
            aspect-ratio: 16/10;
            overflow: hidden;
            background: #f3f4f6;
        }

        .t18-blog-imgwrap img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .t18-blog-card:hover .t18-blog-imgwrap img {
            transform: scale(1.08);
        }

        /* Date Badge */
        .t18-blog-datebadge {
            position: absolute;
            top: 16px;
            left: 16px;
            background: #fff;
            color: var(--t18-primary);
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 2;
        }

        /* Content Area */
        .t18-blog-content {
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 24px;
        }

        .t18-blog-eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            color: #9ca3af;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .t18-blog-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: color-mix(in srgb, var(--t18-primary) 10%, #fff);
            color: var(--t18-primary);
        }

        .t18-blog-card-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0 0 12px;
            color: #111827;
            font-size: 1.2rem;
            line-height: 1.4;
            font-weight: 800;
            letter-spacing: -0.3px;
            transition: color 0.3s ease;
        }

        .t18-blog-card:hover .t18-blog-card-title {
            color: var(--t18-primary);
        }

        .t18-blog-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            color: #6b7280;
            font-size: 12px;
            font-weight: 600;
        }

        .t18-blog-meta i {
            color: var(--t18-primary);
            font-size: 14px;
        }

        .t18-blog-excerpt {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0 0 24px;
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .t18-blog-readmore {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: auto;
            color: var(--t18-primary);
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .t18-blog-card:hover .t18-blog-readmore {
            gap: 12px;
        }

        /* ==================== PAGINATION ==================== */
        .t18-blog-pager {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 48px;
            position: relative;
            z-index: 1;
        }

        .t18-blog-page {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            height: 44px;
            padding: 0 12px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            color: #4b5563;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }

        .t18-blog-page:hover {
            border-color: var(--t18-primary);
            color: var(--t18-primary);
        }

        .t18-blog-page.is-active {
            border-color: var(--t18-primary);
            background: var(--t18-primary);
            color: #fff;
            box-shadow: 0 6px 16px -4px var(--t18-primary);
        }

        /* ==================== EMPTY STATE ==================== */
        .t18-blog-empty {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px dashed #d1d5db;
            position: relative;
            z-index: 1;
        }

        .t18-blog-empty-ico {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            border-radius: 20px;
            background: color-mix(in srgb, var(--t18-primary) 10%, #fff);
            color: var(--t18-primary);
            font-size: 36px;
        }

        .t18-blog-empty h3 {
            margin: 0 0 8px;
            color: #111827;
            font-size: 1.5rem;
            font-weight: 800;
        }

        .t18-blog-empty p {
            margin: 0;
            color: #6b7280;
            font-size: 0.95rem;
        }

        @media(max-width: 640px) {
            .t18-blog-head { padding: 40px 16px 30px; }
            .t18-blog-content { padding: 20px; }
            .t18-blog-card-title { font-size: 1.1rem; }
            .t18-blog-datebadge { top: 12px; left: 12px; padding: 4px 10px; font-size: 10px; }
        }
    </style>
</head>

<body class="font-sans min-h-screen flex flex-col relative">

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <!-- 3D Background Shapes -->
    <div class="t18-shape-wrap">
        <div class="t18-shape t18-shape-1"></div>
        <div class="t18-shape t18-shape-2"></div>
        <div class="t18-shape t18-shape-3"></div>
    </div>

    <!-- ==================== BLOG LIST ==================== -->
    <main class="flex-1 relative z-10 pb-20">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="t18-blog-head">
                <span class="t18-blog-kicker">The Journal</span>
                <h1 class="t18-blog-title">Our Blogs</h1>
                <span class="t18-blog-sub">Explore our latest articles, tips, and insights.</span>
            </div>

            <?php
            $posts = getBlogPosts();
            if ($posts && $posts->rowCount() > 0):
                $i = 0;
            ?>
                <div class="t18-blog-grid">
                    <?php foreach ($posts as $post): $i++;
                        $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $dateFull  = date("d M Y", strtotime($post['created_at']));
                    ?>
                        <a href="<?= $storeUrl . 'post/' . $post['slug'] ?>" class="t18-blog-card">

                            <div class="t18-blog-imgwrap">
                                <img src="<?= UPLOADS_URL . $post['image'] ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                                <span class="t18-blog-datebadge"><?= $dateFull ?></span>
                            </div>

                            <div class="t18-blog-content">
                                <div class="t18-blog-eyebrow">
                                    <span class="t18-blog-num"><?= $num ?></span>
                                    <span>Article</span>
                                </div>

                                <h3 class="t18-blog-card-title">
                                    <?= htmlspecialchars($post['title']) ?>
                                </h3>

                                <div class="t18-blog-meta">
                                    <i class='bx bx-time-five'></i>
                                    <span><?= $dateFull ?></span>
                                    <?php if (!empty($post['author'])): ?>
                                        <span class="mx-1">•</span>
                                        <i class='bx bx-user'></i>
                                        <span><?= htmlspecialchars($post['author']) ?></span>
                                    <?php endif; ?>
                                </div>

                                <p class="t18-blog-excerpt">
                                    <?= limit_characters(strip_tags($post['description']), 120) ?>
                                </p>

                                <div class="t18-blog-readmore">
                                    Read More <i class="bi bi-arrow-right"></i>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="t18-blog-empty">
                    <div class="t18-blog-empty-ico"><i class='bx bx-book-open'></i></div>
                    <h3>No Blog Posts Yet</h3>
                    <p>Check back soon for our latest articles and updates.</p>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <div class="t18-blog-pager">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a href="?page=<?= $p ?>" class="t18-blog-page <?= ($currentPage == $p) ? 'is-active' : '' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

</body>

</html>