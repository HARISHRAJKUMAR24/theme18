<?php
include_once __DIR__ . "/includes/files_includes.php";

if (isset($_GET['slug']) && getData("id", "seller_blog", "slug = '{$_GET['slug']}' AND (seller_id = '$sellerId' AND store_id = '$storeId')")) {

    $slug = $_GET['slug'];
    $id = getData("id", "seller_blog", "slug = '{$_GET['slug']}' AND (seller_id = '$sellerId' AND store_id = '$storeId')");
    $title = getData("title", "seller_blog", "slug = '{$_GET['slug']}' AND (seller_id = '$sellerId' AND store_id = '$storeId')");
    $image = getData("image", "seller_blog", "slug = '{$_GET['slug']}' AND (seller_id = '$sellerId' AND store_id = '$storeId')");
    $description = getData("description", "seller_blog", "slug = '{$_GET['slug']}' AND (seller_id = '$sellerId' AND store_id = '$storeId')");
    $created_at = getData("created_at", "seller_blog", "slug = '{$_GET['slug']}' AND (seller_id = '$sellerId' AND store_id = '$storeId')");
    $visitors = getData("visitors", "seller_blog", "slug = '{$_GET['slug']}' AND (seller_id = '$sellerId' AND store_id = '$storeId')");

    $db->prepare("UPDATE seller_blog SET visitors = visitors+1 WHERE id = :id")->execute(['id' => $id]);
} else {
    redirect($storeUrl);
}

$relatedPosts = getBlogPosts(3, $id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>

    <style>
        :root {
            --t18-primary: <?= htmlspecialchars(getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?: '#64748b') ?>;
            --t18-hover: <?= htmlspecialchars(getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?: '#475569') ?>;
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
            height: 100%;
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
            width: 300px; height: 300px;
            top: -100px; left: -100px;
            transform: rotate(25deg);
        }

        .t18-shape-2 {
            width: 400px; height: 400px;
            bottom: -150px; right: -100px;
            transform: rotate(-15deg);
            animation-delay: -5s;
        }

        @keyframes floatShape {
            0% { transform: translate(0, 0) rotate(0deg) scale(1); }
            100% { transform: translate(20px, -20px) rotate(15deg) scale(1.05); }
        }

        /* ==================== BACK BUTTON ==================== */
        .t18-post-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            color: var(--t18-primary);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            background: #fff;
            padding: 8px 16px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        .t18-post-back:hover {
            color: var(--t18-hover);
            transform: translateX(-3px);
            border-color: var(--t18-primary);
        }

        /* ==================== MAIN CARD ==================== */
        .t18-post-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 25px 60px -15px color-mix(in srgb, var(--t18-primary) 20%, transparent),
                        0 10px 20px -5px rgba(0, 0, 0, 0.02);
            padding: clamp(24px, 5vw, 56px);
            position: relative;
            z-index: 1;
        }

        /* ==================== HEADER ==================== */
        .t18-post-head {
            text-align: center;
            margin-bottom: 40px;
        }

        .t18-post-kicker {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--t18-primary);
            background: color-mix(in srgb, var(--t18-primary) 10%, #fff);
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 20px;
        }

        .t18-post-title {
            margin: 0 0 20px;
            color: #111827;
            font-size: clamp(1.8rem, 4vw, 3rem);
            line-height: 1.15;
            font-weight: 900;
            letter-spacing: -0.5px;
            overflow-wrap: anywhere;
        }

        .t18-post-meta {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px 24px;
            color: #6b7280;
            font-size: 13px;
            font-weight: 600;
        }

        .t18-post-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .t18-post-meta-item i {
            color: var(--t18-primary);
            font-size: 16px;
        }

        .t18-post-meta-sep {
            width: 4px;
            height: 4px;
            display: inline-block;
            border-radius: 50%;
            background: #d1d5db;
        }

        /* ==================== HERO IMAGE ==================== */
        .t18-post-hero {
            position: relative;
            width: 100%;
            aspect-ratio: 16/8;
            overflow: hidden;
            border-radius: 20px;
            margin-bottom: 40px;
            box-shadow: 0 15px 30px -10px rgba(0,0,0,0.15);
            background: #f3f4f6;
        }

        .t18-post-hero img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ==================== BODY CONTENT ==================== */
        .t18-post-body {
            color: #374151;
            font-size: 16px;
            line-height: 1.85;
            overflow-wrap: anywhere;
        }

        .t18-post-body p {
            margin: 0 0 1.5rem;
        }

        .t18-post-body > p:first-of-type::first-letter {
            float: left;
            padding: 4px 10px 0 0;
            color: var(--t18-primary);
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 0.85;
        }

        .t18-post-body h1, .t18-post-body h2, .t18-post-body h3, .t18-post-body h4 {
            margin: 2rem 0 1rem;
            color: #111827;
            font-weight: 800;
            line-height: 1.3;
            letter-spacing: -0.02em;
        }

        .t18-post-body h1 { font-size: 1.8rem; }
        .t18-post-body h2 { font-size: 1.5rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 10px; }
        .t18-post-body h3 { font-size: 1.25rem; }
        .t18-post-body h4 { font-size: 1.1rem; }

        .t18-post-body a {
            color: var(--t18-primary);
            text-decoration: underline;
            text-underline-offset: 3px;
            font-weight: 600;
        }
        .t18-post-body a:hover { color: var(--t18-hover); }

        .t18-post-body ul, .t18-post-body ol {
            margin: 1.2rem 0;
            padding-left: 1.5rem;
        }
        .t18-post-body li { margin-bottom: 0.5rem; }

        .t18-post-body img {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 2rem auto;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .t18-post-body blockquote {
            margin: 2rem 0;
            padding: 20px 24px;
            border-left: 4px solid var(--t18-primary);
            background: color-mix(in srgb, var(--t18-primary) 5%, #fff);
            border-radius: 0 12px 12px 0;
            color: #4b5563;
            font-style: italic;
            font-size: 1.1rem;
        }

        .t18-post-body hr {
            border: 0;
            border-top: 1px solid #e5e7eb;
            margin: 2.5rem 0;
        }

        /* ==================== RELATED POSTS ==================== */
        .t18-post-related {
            margin-top: 60px;
            padding-top: 40px;
            border-top: 1px solid #f3f4f6;
        }

        .t18-post-related-title {
            margin: 0 0 24px;
            color: #111827;
            font-size: 1.5rem;
            font-weight: 900;
            letter-spacing: -0.5px;
            text-align: center;
        }

        .t18-related-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media(min-width: 640px) {
            .t18-related-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        .t18-related-card {
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        .t18-related-card:hover {
            transform: translateY(-5px);
            border-color: var(--t18-primary);
            box-shadow: 0 15px 30px -10px color-mix(in srgb, var(--t18-primary) 20%, transparent);
        }

        .t18-related-imgwrap {
            display: block;
            width: 100%;
            aspect-ratio: 16/9;
            overflow: hidden;
            background: #f3f4f6;
        }

        .t18-related-imgwrap img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .t18-related-card:hover .t18-related-imgwrap img {
            transform: scale(1.05);
        }

        .t18-related-content {
            padding: 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .t18-related-date {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .t18-related-date i { color: var(--t18-primary); }

        .t18-related-name {
            color: #111827;
            font-size: 1rem;
            font-weight: 800;
            line-height: 1.4;
            transition: color 0.2s;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .t18-related-card:hover .t18-related-name {
            color: var(--t18-primary);
        }

        @media(max-width: 640px) {
            .t18-post-card { padding: 24px 20px; border-radius: 24px; }
            .t18-post-head { text-align: left; }
            .t18-post-meta { justify-content: flex-start; }
            .t18-post-hero { aspect-ratio: 4/3; margin-bottom: 24px; }
            .t18-post-body { font-size: 15px; line-height: 1.75; }
        }
    </style>
</head>

<body class="font-sans min-h-screen flex flex-col relative">

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <!-- 3D Background Shapes -->
    <div class="t18-shape-wrap">
        <div class="t18-shape t18-shape-1"></div>
        <div class="t18-shape t18-shape-2"></div>
    </div>

    <main class="flex-1 py-10 sm:py-16 relative z-10">
        <div class="max-w-[900px] mx-auto px-4 sm:px-6">

            <!-- Back -->
            <a href="<?= $storeUrl ?>blog" class="t18-post-back">
                <i class="bi bi-arrow-left"></i> Back to Journal
            </a>

            <!-- Main Content Card -->
            <article class="t18-post-card">
                
                <!-- Header -->
                <header class="t18-post-head">
                    <span class="t18-post-kicker">Article / Journal</span>
                    <h1 class="t18-post-title"><?= htmlspecialchars($title) ?></h1>

                    <div class="t18-post-meta">
                        <span class="t18-post-meta-item">
                            <i class='bx bx-time-five'></i>
                            <span><?= date("d M Y", strtotime($created_at)) ?></span>
                        </span>
                        <span class="t18-post-meta-sep"></span>
                        <span class="t18-post-meta-item">
                            <i class='bx bx-user'></i>
                            <span><?= htmlspecialchars($storeName ?? 'Admin') ?></span>
                        </span>
                        <span class="t18-post-meta-sep"></span>
                        <span class="t18-post-meta-item">
                            <i class='bx bx-show'></i>
                            <span><?= (int)$visitors + 1 ?> views</span>
                        </span>
                    </div>
                </header>

                <!-- Feature Image -->
                <figure class="t18-post-hero">
                    <img src="<?= UPLOADS_URL . $image ?>" alt="<?= htmlspecialchars($title) ?>">
                </figure>

                <!-- Body -->
                <div class="t18-post-body description">
                    <?= $description ?>
                </div>

                <!-- Related Posts -->
                <?php if ($relatedPosts && $relatedPosts->rowCount() > 0): ?>
                    <section class="t18-post-related">
                        <h2 class="t18-post-related-title">You Might Also Like</h2>

                        <div class="t18-related-grid">
                            <?php
                            $shown = 0;
                            while ($rp = $relatedPosts->fetch()):
                                if ($rp['id'] == $id) continue;
                                if ($shown >= 3) break;
                                $shown++;
                            ?>
                                <a href="<?= $storeUrl . 'post/' . $rp['slug'] ?>" class="t18-related-card">
                                    <span class="t18-related-imgwrap">
                                        <img src="<?= UPLOADS_URL . $rp['image'] ?>" alt="<?= htmlspecialchars($rp['title']) ?>" loading="lazy">
                                    </span>
                                    <span class="t18-related-content">
                                        <span class="t18-related-date">
                                            <i class='bx bx-time-five'></i>
                                            <?= date("d M Y", strtotime($rp['created_at'])) ?>
                                        </span>
                                        <span class="t18-related-name"><?= htmlspecialchars($rp['title']) ?></span>
                                    </span>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </section>
                <?php endif; ?>

            </article>

        </div>
    </main>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

</body>

</html>