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

        /* ==================== 3D HERO SECTION ==================== */
        .t18-hero {
            position: relative;
            padding: 50px 16px 60px;
            background: linear-gradient(135deg, color-mix(in srgb, var(--t18-primary) 8%, #fff) 0%, color-mix(in srgb, var(--t18-hover) 10%, #fff) 100%);
            border-bottom-left-radius: 32px;
            border-bottom-right-radius: 32px;
            overflow: hidden;
            isolation: isolate;
            text-align: center;
        }

        @media (min-width: 768px) {
            .t18-hero {
                padding: 80px 24px 90px;
                border-bottom-left-radius: 48px;
                border-bottom-right-radius: 48px;
            }
        }

        /* Decorative floating shapes */
        .t18-hero::before,
        .t18-hero::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: -1;
            filter: blur(50px);
        }
        .t18-hero::before {
            width: 200px;
            height: 200px;
            top: -60px;
            left: -50px;
            background: color-mix(in srgb, var(--t18-primary) 25%, transparent);
            opacity: 0.6;
            animation: floatShape1 10s ease-in-out infinite alternate;
        }
        .t18-hero::after {
            width: 250px;
            height: 250px;
            bottom: -100px;
            right: -60px;
            background: color-mix(in srgb, var(--t18-hover) 22%, transparent);
            opacity: 0.5;
            animation: floatShape2 12s ease-in-out infinite alternate;
        }

        @media (min-width: 768px) {
            .t18-hero::before { width: 300px; height: 300px; top: -100px; left: -80px; }
            .t18-hero::after { width: 400px; height: 400px; bottom: -150px; right: -100px; }
        }

        @keyframes floatShape1 {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(20px, 30px) scale(1.1); }
        }
        @keyframes floatShape2 {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-30px, -20px) scale(1.05); }
        }

        .t18-hero h1 {
            font-size: clamp(1.75rem, 5vw, 3.5rem);
            font-weight: 900;
            color: #111827;
            line-height: 1.15;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .t18-hero p {
            font-size: clamp(0.9rem, 1.8vw, 1.1rem);
            color: #4b5563;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            padding: 0 8px;
        }

        .t18-eyebrow {
            display: inline-block;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--t18-primary);
            background: color-mix(in srgb, var(--t18-primary) 12%, #fff);
            padding: 6px 14px;
            border-radius: 999px;
            margin-bottom: 16px;
        }

        @media (min-width: 768px) {
            .t18-eyebrow { font-size: 11px; letter-spacing: 3px; padding: 6px 16px; margin-bottom: 20px; }
        }

        /* ==================== 3D CARDS ==================== */
        .t18-card {
            position: relative;
            background: #fff;
            border-radius: 20px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 12%, #e5e7eb);
            box-shadow: 0 8px 24px -10px color-mix(in srgb, var(--t18-primary) 15%, transparent);
            padding: 20px;
            transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.4s ease;
            height: 100%;
            overflow: hidden;
            isolation: isolate;
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 768px) {
            .t18-card { padding: 28px; border-radius: 24px; }
        }

        .t18-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -15px color-mix(in srgb, var(--t18-primary) 30%, transparent);
            border-color: color-mix(in srgb, var(--t18-primary) 30%, transparent);
        }

        /* Subtle inner glow */
        .t18-card::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: radial-gradient(circle, color-mix(in srgb, var(--t18-primary) 10%, transparent) 0%, transparent 70%);
            z-index: -1;
            pointer-events: none;
            transition: opacity 0.4s;
            opacity: 0.5;
        }
        .t18-card:hover::before { opacity: 1; }

        /* Card header: keep the number badge and title in separate columns */
        .t18-card-header {
            display: grid !important;
            grid-template-columns: 38px minmax(0, 1fr);
            align-items: center;
            column-gap: 16px;
            width: 100%;
            min-width: 0;
            margin-bottom: 16px;
        }

        .t18-card-header > .t18-badge {
            grid-column: 1;
            grid-row: 1;
            position: relative;
            z-index: 1;
            justify-self: start;
        }

        .t18-card-header > div {
            grid-column: 2;
            grid-row: 1;
            min-width: 0;
            width: 100%;
        }

        .t18-card-header h3,
        .t18-card-header .t18-subtitle {
            overflow-wrap: anywhere;
        }

        @media (min-width: 768px) {
            .t18-card-header {
                grid-template-columns: 46px minmax(0, 1fr);
                column-gap: 18px;
                margin-bottom: 20px;
            }
        }

        /* Number Badge */
        .t18-badge {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: var(--t18-primary);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 14px;
            box-shadow: 0 4px 12px -3px var(--t18-primary);
            flex-shrink: 0;
        }

        @media (min-width: 768px) {
            .t18-badge { width: 46px; height: 46px; font-size: 16px; border-radius: 14px; }
        }

        .t18-card h3 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
            line-height: 1.3;
            text-align: left;
            margin-top: 0;
        }

        @media (min-width: 768px) {
            .t18-card h3 { font-size: 1.25rem; }
        }

        .t18-card .t18-subtitle {
            font-size: 0.78rem;
            color: #6b7280;
            display: block;
            text-align: left;
            margin-bottom: 0;
        }

        @media (min-width: 768px) {
            .t18-card .t18-subtitle { font-size: 0.85rem; }
        }

        /* Info Rows */
        .t18-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.82rem;
            gap: 10px;
        }

        @media (min-width: 768px) {
            .t18-info-row { padding: 12px 0; font-size: 0.9rem; }
        }

        .t18-info-row:last-child { border-bottom: none; }

        .t18-info-label {
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            flex-shrink: 0;
        }

        .t18-info-label i {
            color: var(--t18-primary);
            font-size: 1rem;
        }

        @media (min-width: 768px) {
            .t18-info-label { gap: 8px; }
            .t18-info-label i { font-size: 1.1rem; }
        }

        .t18-info-value {
            font-weight: 700;
            color: #111827;
            text-align: right;
            word-break: break-word;
        }

        .t18-free-label { color: #16a34a !important; }

        /* Note Box */
        .t18-note {
            margin-top: 16px;
            padding: 12px;
            background: color-mix(in srgb, var(--t18-primary) 5%, #f9fafb);
            border-radius: 10px;
            border: 1px dashed color-mix(in srgb, var(--t18-primary) 25%, transparent);
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 0.75rem;
            color: #4b5563;
            line-height: 1.5;
        }

        @media (min-width: 768px) {
            .t18-note { padding: 14px 16px; font-size: 0.8rem; gap: 10px; border-radius: 12px; }
        }

        .t18-note i {
            color: var(--t18-primary);
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        @media (min-width: 768px) {
            .t18-note i { font-size: 1.1rem; margin-top: 2px; }
        }

        /* Section Heading */
        .t18-section-heading {
            text-align: center;
            margin-bottom: 32px;
            padding: 0 16px;
        }

        @media (min-width: 768px) {
            .t18-section-heading { margin-bottom: 48px; }
        }

        .t18-section-heading h2 {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 900;
            color: #111827;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .t18-section-heading p {
            color: #6b7280;
            font-size: clamp(0.82rem, 1.5vw, 0.95rem);
            max-width: 500px;
            margin: 0 auto;
        }

        /* Contact Rows in Location Cards */
        .t18-contact-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 8px 0;
            color: #4b5563;
            font-size: 0.8rem;
            line-height: 1.5;
        }

        @media (min-width: 768px) {
            .t18-contact-row { gap: 10px; font-size: 0.85rem; }
        }

        .t18-contact-row i {
            color: var(--t18-primary);
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        @media (min-width: 768px) {
            .t18-contact-row i { font-size: 1.1rem; }
        }

        .t18-contact-row a {
            color: var(--t18-primary);
            font-weight: 600;
            text-decoration: none;
            word-break: break-word;
        }
        .t18-contact-row a:hover { text-decoration: underline; }

        /* Hours */
        .t18-hours-list {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f3f4f6;
        }

        @media (min-width: 768px) {
            .t18-hours-list { margin-top: 16px; padding-top: 16px; }
        }

        .t18-hour-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            padding: 5px 0;
            color: #4b5563;
        }

        @media (min-width: 768px) {
            .t18-hour-row { font-size: 0.8rem; padding: 6px 0; }
        }

        .t18-hour-row span:last-child {
            font-weight: 700;
            color: #111827;
            text-align: right;
        }

        /* CTA Section */
        .t18-cta {
            background: var(--t18-primary);
            border-radius: 24px;
            padding: 40px 20px;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
            isolation: isolate;
            margin: 48px auto;
            max-width: 1300px;
            width: calc(100% - 32px);
        }

        @media (min-width: 768px) {
            .t18-cta { border-radius: 32px; padding: 60px 30px; margin: 80px auto; width: calc(100% - 40px); }
        }

        .t18-cta::before {
            content: "";
            position: absolute;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            top: -80px;
            left: -80px;
            z-index: -1;
        }
        .t18-cta::after {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.1);
            bottom: -80px;
            right: -80px;
            z-index: -1;
        }

        @media (min-width: 768px) {
            .t18-cta::before { width: 400px; height: 400px; top: -100px; left: -100px; }
            .t18-cta::after { width: 300px; height: 300px; bottom: -100px; right: -100px; }
        }

        .t18-cta h2 {
            font-size: clamp(1.5rem, 4vw, 3rem);
            font-weight: 900;
            margin-bottom: 12px;
            color: #fff;
        }

        @media (min-width: 768px) {
            .t18-cta h2 { margin-bottom: 16px; }
        }

        .t18-cta p {
            font-size: clamp(0.85rem, 1.5vw, 1.05rem);
            opacity: 0.9;
            max-width: 500px;
            margin: 0 auto 24px;
            padding: 0 8px;
        }

        @media (min-width: 768px) {
            .t18-cta p { margin-bottom: 32px; }
        }

        .t18-cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: var(--t18-primary);
            padding: 12px 28px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.2);
        }

        @media (min-width: 768px) {
            .t18-cta-btn { padding: 16px 36px; font-size: 1rem; gap: 10px; }
        }

        .t18-cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 22px -4px rgba(0, 0, 0, 0.3);
            background: #f9fafb;
            color: var(--t18-hover);
        }

        /* Empty state */
        .t18-empty {
            text-align: center;
            padding: 40px 20px;
            background: #fff;
            border-radius: 20px;
            border: 1px dashed #e5e7eb;
            color: #9ca3af;
            font-size: 0.85rem;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .t18-empty { padding: 60px 20px; font-size: 0.95rem; }
        }
    </style>
</head>

<body class="font-sans min-h-screen">

    <?php
    $primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f';
    $hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899';
    ?>

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <!-- ==================== HERO ==================== -->
    <section class="t18-hero">
        <div class="max-w-3xl mx-auto relative z-10">
            <span class="t18-eyebrow">Delivery & Locations</span>
            <h1>Where We Deliver</h1>
            <p>Check our delivery areas and visit our stores. We bring your order straight to your doorstep.</p>
        </div>
    </section>

    <!-- ==================== DELIVERY AREAS ==================== -->
    <section class="max-w-[1300px] mx-auto px-4 sm:px-6 lg:px-8 mt-12 md:mt-16">
        <div class="t18-section-heading">
            <h2>Delivery Areas</h2>
            <p>Areas we serve with fast and reliable shipping.</p>
        </div>

        <?php
        $areasStmt = getDeliveryAreas();
        if ($areasStmt instanceof PDOStatement) {
            $areas = $areasStmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif (is_array($areasStmt)) {
            $areas = $areasStmt;
        } else {
            $areas = [];
        }

        if (!empty($areas)):
        ?>
            <div class="flex flex-wrap justify-center gap-4 md:gap-6">
                <?php foreach ($areas as $index => $area): ?>
                    <!-- The Card -->
                    <div class="t18-card w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] max-w-md">
                        
                        <!-- Flex row for Badge + Text -->
                        <div class="t18-card-header p-6">
                            <span class="t18-badge"><?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?></span>
                            <div class="flex flex-col">
                                <h3><?= htmlspecialchars($area['value']) ?></h3>
                                <span class="t18-subtitle">Delivery Area</span>
                            </div>
                        </div>

                        <div class="flex-1 p-6">
                            <div class="t18-info-row">
                                <span class="t18-info-label">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <?= getData("delivery_area_type", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") == "zip_code" ? 'Postal Code' : 'State' ?>
                                </span>
                                <span class="t18-info-value"><?= htmlspecialchars($area['value']) ?></span>
                            </div>

                            <div class="t18-info-row">
                                <span class="t18-info-label">
                                    <i class="bi bi-truck"></i>
                                    Delivery Fee
                                </span>
                                <?php if (getSettings("delivery_charges_type")): ?>
                                    <span class="t18-info-value"><?= ucwords(str_replace("_", " ", getSettings("delivery_charges_type"))) ?></span>
                                <?php elseif ($area['charges'] > 0): ?>
                                    <span class="t18-info-value"><?= currencyToSymbol($storeCurrency) . $area['charges'] ?></span>
                                <?php else: ?>
                                    <span class="t18-info-value t18-free-label">Free</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($area['delivery_notes'])): ?>
                            <div class="t18-note">
                                <i class="bi bi-info-circle-fill"></i>
                                <p><?= htmlspecialchars($area['delivery_notes']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="t18-empty">No delivery areas configured yet.</div>
        <?php endif; ?>
    </section>

    <!-- ==================== STORE LOCATIONS ==================== -->
    <section class="max-w-[1300px] mx-auto px-4 sm:px-6 lg:px-8 mt-14 md:mt-20">
        <div class="t18-section-heading">
            <h2>Our Stores</h2>
            <p>Visit us at our physical locations for a hands-on experience.</p>
        </div>

        <?php
        $locationsStmt = readData("*", "seller_locations", "(seller_id = '$sellerId' AND store_id = '$storeId')");
        $locations = $locationsStmt ? $locationsStmt->fetchAll(PDO::FETCH_ASSOC) : [];

        if (!empty($locations)):
        ?>
            <div class="flex flex-wrap justify-center gap-4 md:gap-6">
                <?php foreach ($locations as $index => $location): ?>
                    <div class="t18-card w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] max-w-md">
                        
                        <!-- Flex row for Badge + Text -->
                        <div class="t18-card-header">
                            <span class="t18-badge"><?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?></span>
                            <div class="flex flex-col">
                                <h3><?= htmlspecialchars($location['name']) ?></h3>
                                <?php if ($location['phone']): ?>
                                    <a href="tel:<?= $location['phone'] ?>" class="t18-subtitle hover:underline" style="color: var(--t18-primary);">
                                        <i class="bi bi-telephone-fill mr-1"></i> <?= formatPhoneNumber($location['phone']) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex-1 mt-2">
                            <div class="t18-contact-row">
                                <i class="bi bi-geo-alt-fill"></i>
                                <div>
                                    <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold block mb-1">Address</span>
                                    <?= htmlspecialchars($location['address_1']) ?>
                                    <?php if ($location['address_2']): ?>
                                        <br><?= htmlspecialchars($location['address_2']) ?>
                                    <?php endif; ?>
                                    <br><?= htmlspecialchars($location['city']) ?>, <?= htmlspecialchars($location['state']) ?> <?= htmlspecialchars($location['zip_code']) ?>
                                    <br><?= codeToCountry($location['country']) ?>
                                </div>
                            </div>

                            <?php if ($location['email']): ?>
                                <div class="t18-contact-row">
                                    <i class="bi bi-envelope-fill"></i>
                                    <a href="mailto:<?= $location['email'] ?>"><?= htmlspecialchars($location['email']) ?></a>
                                </div>
                            <?php endif; ?>

                            <?php
                            $business_hours = getLocationBusinessHour($location['location_id']);
                            if (!empty($business_hours)):
                            ?>
                                <div class="t18-hours-list">
                                    <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold block mb-2">Business Hours</span>
                                    <?php foreach ($business_hours as $hour): ?>
                                        <div class="t18-hour-row">
                                            <span><?= htmlspecialchars($hour['day']) ?></span>
                                            <span><?= strtoupper(date('h:i a', strtotime($hour['time_from']))) ?> – <?= strtoupper(date('h:i a', strtotime($hour['time_to']))) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="t18-empty">No store locations added yet.</div>
        <?php endif; ?>
    </section>

    <!-- ==================== CTA ==================== -->
    <section class="t18-cta">
        <div class="relative z-10">
            <h2>Ready to shop?</h2>
            <p>Order now and let us bring your favourites right to your door.</p>
            <a href="<?= $storeUrl ?>shop-all" class="t18-cta-btn">
                Shop Now <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </section>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

</body>

</html>