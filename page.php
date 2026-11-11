<?php include_once __DIR__ . "/includes/files_includes.php"; ?>

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

        /* ==================== PAGE HEADER ==================== */
        .t18-page-head {
            position: relative;
            z-index: 1;
            padding: 60px 16px 40px;
            text-align: center;
        }

        .t18-page-eyebrow {
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

        .t18-page-title {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 900;
            color: #111827;
            line-height: 1.15;
            letter-spacing: -0.5px;
        }

        /* ==================== MAIN CARD ==================== */
        .t18-page-container {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 0 auto 60px;
            padding: 0 16px;
        }

        .t18-page-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 50px -15px color-mix(in srgb, var(--t18-primary) 20%, transparent),
                        0 10px 20px -5px rgba(0, 0, 0, 0.02);
            padding: clamp(24px, 5vw, 48px);
        }

        /* ==================== CONTENT TYPOGRAPHY ==================== */
        .t18-content {
            color: #374151;
            font-size: 15px;
            line-height: 1.8;
            overflow-wrap: anywhere;
            word-wrap: break-word;
        }

        .t18-content h1, .t18-content h2, .t18-content h3, .t18-content h4 {
            margin: 2rem 0 1rem;
            color: #111827;
            font-weight: 800;
            line-height: 1.3;
            letter-spacing: -0.02em;
        }

        .t18-content h1 { font-size: 1.8rem; }
        .t18-content h2 { font-size: 1.5rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 10px; }
        .t18-content h3 { font-size: 1.25rem; }
        .t18-content h4 { font-size: 1.1rem; }

        .t18-content p { margin: 0 0 1.2rem; }
        .t18-content p:last-child { margin-bottom: 0; }

        .t18-content a {
            color: var(--t18-primary);
            text-decoration: underline;
            text-underline-offset: 3px;
            font-weight: 600;
        }
        .t18-content a:hover { color: var(--t18-hover); }

        .t18-content strong, .t18-content b { color: #111827; font-weight: 700; }
        .t18-content em, .t18-content i { font-style: italic; }

        .t18-content ul, .t18-content ol { margin: 1.2rem 0; padding-left: 1.5rem; }
        .t18-content li { margin-bottom: 0.5rem; }
        .t18-content ul li { list-style: disc; }
        .t18-content ol li { list-style: decimal; }

        .t18-content blockquote {
            margin: 1.5rem 0;
            padding: 16px 20px;
            border-left: 4px solid var(--t18-primary);
            background: color-mix(in srgb, var(--t18-primary) 5%, #fff);
            border-radius: 0 12px 12px 0;
            color: #4b5563;
            font-style: italic;
        }

        .t18-content img {
            max-width: 100%;
            height: auto;
            margin: 1.5rem 0;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .t18-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            font-size: 14px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .t18-content th, .t18-content td {
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 16px;
            text-align: left;
        }
        .t18-content th {
            background: #f9fafb;
            color: #111827;
            font-weight: 700;
        }

        .t18-content hr {
            margin: 2rem 0;
            border: 0;
            border-top: 1px solid #e5e7eb;
        }

        /* ==================== FOOTER ACTION ==================== */
        .t18-page-actions {
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #f3f4f6;
            text-align: center;
        }

        .t18-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 28px;
            background: var(--t18-primary);
            color: #fff;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px -6px var(--t18-primary);
        }

        .t18-page-btn:hover {
            background: var(--t18-hover);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px -6px var(--t18-hover);
        }

        @media (max-width: 640px) {
            .t18-page-head { padding: 40px 16px 30px; }
            .t18-content { font-size: 14px; line-height: 1.7; }
            .t18-content h1 { font-size: 1.5rem; }
            .t18-content h2 { font-size: 1.3rem; }
            .t18-content h3 { font-size: 1.15rem; }
        }
    </style>
</head>

<body class="font-sans min-h-screen flex flex-col relative">

    <?php
    if (isset($_GET['slug']) && getData("id", "seller_pages", "slug = '{$_GET['slug']}' AND seller_id = '$sellerId'")) {
        $slug    = $_GET['slug'];
        $id      = getData("id", "seller_pages", "slug = '{$_GET['slug']}' AND seller_id = '$sellerId'");
        $name    = getData("name", "seller_pages", "slug = '{$_GET['slug']}' AND seller_id = '$sellerId'");
        $content = getData("content", "seller_pages", "slug = '{$_GET['slug']}' AND seller_id = '$sellerId'");
    } else {
        redirect($storeUrl);
    }

    $primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?: '#64748b';
    $hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?: '#475569';
    ?>

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <!-- 3D Background Shapes -->
    <div class="t18-shape-wrap">
        <div class="t18-shape t18-shape-1"></div>
        <div class="t18-shape t18-shape-2"></div>
    </div>

    <main class="flex-1 flex flex-col">
        
        <!-- Header -->
        <header class="t18-page-head">
            <div class="max-w-3xl mx-auto">
                <span class="t18-page-eyebrow">Information</span>
                <h1 class="t18-page-title"><?= htmlspecialchars($name) ?></h1>
            </div>
        </header>

        <!-- Content Card -->
        <section class="t18-page-container">
            <div class="t18-page-card">
                <article class="t18-content">
                    <?= $content ?>
                </article>

                <div class="t18-page-actions">
                    <a href="<?= $storeUrl ?>" class="t18-page-btn">
                        <i class="bi bi-arrow-left"></i> Back to Home
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

</body>
</html>