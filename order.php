<?php


// DB colors
$primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ff007f';
$hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ec4899';

if (isset($_GET['id']) && getData("id", "seller_orders", "order_id = '{$_GET['id']}' AND (seller_id = '$sellerId' AND store_id = '$storeId') AND customer_id = '$customerId'")) {
    $order_id = $_GET['id'];

    $ordered_date = date('F d, Y', strtotime(getData("created_at", "seller_orders", "order_id = '$order_id'")));
    $status = ucwords(getData("status", "seller_orders", "order_id = '$order_id'"));
    $payment_status = ucwords(getData("payment_status", "seller_orders", "order_id = '$order_id'"));
    $courier_service = getData("courier_service", "seller_orders", "order_id = '$order_id'");
    $tracking_id = getData("tracking_id", "seller_orders", "order_id = '$order_id'");
    $expected_delivery_date = getData("expected_delivery_date", "seller_orders", "order_id = '$order_id'");
    $tax = getData("tax", "seller_orders", "order_id = '$order_id'");
    $customized_gift = getData("customized_gift", "seller_orders", "order_id = '$order_id'");
    $delivery_charges = getData("delivery_charges", "seller_orders", "order_id = '$order_id'");
    $total = getData("total", "seller_orders", "order_id = '$order_id'");
    $discount = getData("discount", "seller_orders", "order_id = '$order_id'");
    $discount_type = getData("discount_type", "seller_orders", "order_id = '$order_id'");
    $currency = getData("currency", "seller_orders", "order_id = '$order_id'");
    $payment_method = getData("payment_method", "seller_orders", "order_id = '$order_id'");

    $gst_number = getData("gst_number", "seller_orders", "order_id = '$order_id'");
    $gst_tax_type = getData("gst_tax_type", "seller_orders", "order_id = '$order_id'");
    $gst_percentage = getData("gst_percentage", "seller_orders", "order_id = '$order_id'");

    $gstTaxAmount = '';
    $totalGst = '';
    if ($tax != 0 && !empty($gst_percentage)) {
        $totalGst = $gst_tax_type == "inclusive" ? "(Inclusive GST -$gst_percentage%)" : "(Exclusive GST +$gst_percentage%)";

        if ($gst_tax_type != "inclusive") {
            $gstTaxAmount = '<li class="flex justify-between items-center text-sm py-1">
                        <span class="text-gray-600">GST (' . $gst_percentage . '%)</span>
                        <span class="font-semibold text-gray-900">' . currencyToSymbol($currency) . number_format($tax, 2) . '</span>
                    </li>';
        }
    }

    $subTotal = 0;
    foreach (getOrderedProducts(array("orderId" => $order_id)) as $key => $product) {
        $subTotal += $product['price'] * $product['quantity'];
    }
} else {
    redirect($storeUrl);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>

    <style>
        :root {
            --t18-primary: <?= htmlspecialchars($primary_color) ?>;
            --t18-hover: <?= htmlspecialchars($hover_color) ?>;
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
            background: linear-gradient(135deg, color-mix(in srgb, var(--t18-primary) 10%, #fff) 0%, color-mix(in srgb, var(--t18-hover) 8%, #fff) 100%);
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

        /* ==================== 3D CARDS ==================== */
        .t18-card {
            background: #fff;
            border-radius: 24px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 12%, #e5e7eb);
            box-shadow: 0 10px 30px -12px color-mix(in srgb, var(--t18-primary) 15%, transparent);
            padding: 24px;
            position: relative;
            z-index: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .t18-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .t18-card-header i {
            font-size: 1.5rem;
            color: var(--t18-primary);
            background: color-mix(in srgb, var(--t18-primary) 10%, #fff);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .t18-card-header h3 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #111827;
            margin: 0;
        }

        /* Info Rows */
        .t18-info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid #f9fafb;
            font-size: 0.85rem;
            gap: 12px;
        }
        .t18-info-row:last-child { border-bottom: none; }

        .t18-info-label { color: #6b7280; font-weight: 500; flex-shrink: 0; }
        .t18-info-value { color: #111827; font-weight: 600; text-align: right; word-break: break-word; }

        /* Table */
        .t18-table-wrap {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
        }

        .t18-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .t18-table th {
            background: #f9fafb;
            color: #4b5563;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .t18-table td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.85rem;
            color: #1f2937;
            vertical-align: middle;
        }

        .t18-table tr:last-child td { border-bottom: none; }

        .t18-table img {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .t18-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--t18-primary);
            color: #fff;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 6px 16px -4px var(--t18-primary);
        }

        .t18-btn:hover {
            background: var(--t18-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -4px var(--t18-hover);
            color: #fff;
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

    <div class="flex-1 py-10 relative z-10">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-[var(--t18-primary)] mb-1">Order Details</p>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Order #<?= htmlspecialchars($order_id) ?></h1>
                    <p class="text-sm text-gray-500 mt-1">Placed on <?= $ordered_date ?></p>
                </div>

                <a href="<?= $storeUrl ?>download-invoice?id=<?= $order_id ?>" class="t18-btn">
                    <i class="bi bi-download"></i> Download Invoice
                </a>
            </div>

            <!-- Order Status + Products -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

                <!-- Order Status Sidebar -->
                <div class="lg:col-span-1">
                    <div class="t18-card">
                        <div class="t18-card-header">
                            <i class="bi bi-info-circle"></i>
                            <h3>Order Status</h3>
                        </div>

                        <div class="space-y-1">
                            <div class="t18-info-row">
                                <span class="t18-info-label">Status</span>
                                <span class="t18-info-value" style="color: var(--t18-hover);"><?= $status ?></span>
                            </div>
                            <div class="t18-info-row">
                                <span class="t18-info-label">Payment</span>
                                <span class="t18-info-value" style="color: var(--t18-primary);"><?= $payment_status ?></span>
                            </div>
                            <?php if ($courier_service) : ?>
                                <div class="t18-info-row">
                                    <span class="t18-info-label">Courier</span>
                                    <span class="t18-info-value"><?= $courier_service ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($expected_delivery_date) : ?>
                                <div class="t18-info-row">
                                    <span class="t18-info-label">Expected Delivery</span>
                                    <span class="t18-info-value"><?= date('M d, Y', strtotime($expected_delivery_date)) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($payment_status == "Unpaid") : ?>
                            <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-xl text-xs text-red-600 leading-relaxed">
                                <strong>Note:</strong> Once you pay manually, it can take 3-5 hours to verify the payment.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Ordered Products -->
                <div class="lg:col-span-2">
                    <div class="t18-card">
                        <div class="t18-card-header">
                            <i class="bi bi-box-seam"></i>
                            <h3>Ordered Products</h3>
                        </div>

                        <div class="t18-table-wrap">
                            <table class="t18-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $products = getOrderedProducts(array("orderId" => $order_id));
                                    $subTotal = 0;

                                    foreach ($products as $key => $product) :
                                        $subTotal += $product['price'] * $product['quantity'];

                                        $image = getData("image", "seller_products", "id = '{$product['product_id']}'");
                                        if ($product['other'] && getData("image", "seller_product_variants", "id = '{$product['other']}'")) {
                                            $image = getData("image", "seller_product_variants", "id = '{$product['other']}'");
                                        }

                                        $other = "";
                                        $advanced_variant = "";
                                        $size = '';
                                        $colorId = '';

                                        if ($product['other']) {
                                            $other = " - " . getData("variation", "seller_product_variants", "id = '{$product['other']}'");
                                        }
                                        if ($product && $product['advanced_variant']) {
                                            $size = getData("size", "seller_product_advanced_variants", "id = '{$product['advanced_variant']}'");
                                            $colorId = getData("color", "seller_product_advanced_variants", "id = '{$product['advanced_variant']}'");
                                            $color = getData("color_name", "product_colors", "id = '$colorId'");

                                            $advanced_variant = "Size: $size | Color: $color";

                                            if (getData("image", "seller_product_advanced_variants", "id = '{$product['advanced_variant']}'")) {
                                                $image = getData("image", "seller_product_advanced_variants", "id = '{$product['advanced_variant']}'");
                                            }
                                        }

                                        $productUrl = $storeUrl . "product/" . getData("slug", "seller_products", "id = '{$product['product_id']}'");
                                        if ($product['other']) {
                                            $productUrl .= "?variation=" . $product['other'];
                                        }
                                        if ($size && $colorId) {
                                            $productUrl .= "?size=" . $size . "&color=" . $colorId;
                                        }
                                    ?>
                                        <tr>
                                            <td>
                                                <a href="<?= $productUrl ?>" class="flex items-center gap-3 group">
                                                    <img src="<?= UPLOADS_URL . $image ?>" alt="Product">
                                                    <div>
                                                        <span class="font-bold text-gray-900 group-hover:text-[var(--t18-primary)] transition">
                                                            <?= htmlspecialchars(getData("name", "seller_products", "id = '{$product['product_id']}'") . $other) ?>
                                                        </span>
                                                        <?php if ($advanced_variant): ?>
                                                            <span class="block text-xs text-gray-500 mt-0.5"><?= htmlspecialchars($advanced_variant) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </a>
                                            </td>
                                            <td class="whitespace-nowrap"><?= currencyToSymbol($currency) . number_format($product['price'], 2) ?></td>
                                            <td><?= $product['quantity'] ?></td>
                                            <td class="text-right font-bold text-[var(--t18-primary)] whitespace-nowrap">
                                                <?= currencyToSymbol($currency) . number_format($product['price'] * $product['quantity'], 2) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary + Addresses Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

                <!-- Order Summary -->
                <div class="t18-card">
                    <div class="t18-card-header">
                        <i class="bi bi-wallet2"></i>
                        <h3>Order Summary</h3>
                    </div>

                    <div class="space-y-1 flex-1">
                        <div class="t18-info-row">
                            <span class="t18-info-label">Sub Total</span>
                            <span class="t18-info-value"><?= currencyToSymbol($storeCurrency) . number_format($subTotal, 2) ?></span>
                        </div>
                        <div class="t18-info-row">
                            <span class="t18-info-label">Delivery</span>
                            <span class="t18-info-value"><?= currencyToSymbol($storeCurrency) . $delivery_charges ?></span>
                        </div>
                        <?= $gstTaxAmount ?>
                        <div class="t18-info-row">
                            <span class="t18-info-label">Discount</span>
                            <span class="t18-info-value">
                                <?= $discount_type == "fixed" ? currencyToSymbol($storeCurrency) . number_format($discount, 2) : number_format($discount, 2) . "%" ?>
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                        <span class="font-bold text-gray-900 text-base">Total</span>
                        <span class="font-black text-xl text-[var(--t18-primary)]"><?= currencyToSymbol($currency) . number_format($total, 2) ?></span>
                    </div>
                </div>

                <!-- Delivery Address -->
                <div class="t18-card">
                    <div class="t18-card-header">
                        <i class="bi bi-geo-alt"></i>
                        <h3>Delivery Address</h3>
                    </div>

                    <div class="space-y-1 flex-1">
                        <div class="t18-info-row">
                            <span class="t18-info-label">Name</span>
                            <span class="t18-info-value"><?= getData("name", "seller_order_address", "type = 'shipping' AND order_id = '$order_id'") ?></span>
                        </div>
                        <div class="t18-info-row">
                            <span class="t18-info-label">Phone</span>
                            <span class="t18-info-value"><?= getData("phone", "seller_order_address", "type = 'shipping' AND order_id = '$order_id'") ?></span>
                        </div>
                        <div class="t18-info-row">
                            <span class="t18-info-label">Email</span>
                            <span class="t18-info-value text-xs break-all"><?= getData("email", "seller_order_address", "type = 'shipping' AND order_id = '$order_id'") ?></span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <span class="block text-xs font-bold text-gray-500 mb-1">Full Address</span>
                        <span class="block text-sm text-gray-900 leading-relaxed">
                            <?= getData("address", "seller_order_address", "type = 'shipping' AND order_id = '$order_id'") ?><br>
                            <?= getData("city", "seller_order_address", "type = 'shipping' AND order_id = '$order_id'") ?>,
                            <?= getData("state", "seller_order_address", "type = 'shipping' AND order_id = '$order_id'") ?>
                            <?= getData("pin_code", "seller_order_address", "type = 'shipping' AND order_id = '$order_id'") ?>
                        </span>
                    </div>
                </div>

                <!-- Billing Address -->
                <div class="t18-card">
                    <div class="t18-card-header">
                        <i class="bi bi-receipt"></i>
                        <h3>Billing Address</h3>
                    </div>

                    <div class="space-y-1 flex-1">
                        <div class="t18-info-row">
                            <span class="t18-info-label">Name</span>
                            <span class="t18-info-value"><?= getData("name", "seller_order_address", "type = 'billing' AND order_id = '$order_id'") ?></span>
                        </div>
                        <div class="t18-info-row">
                            <span class="t18-info-label">Phone</span>
                            <span class="t18-info-value"><?= getData("phone", "seller_order_address", "type = 'billing' AND order_id = '$order_id'") ?></span>
                        </div>
                        <div class="t18-info-row">
                            <span class="t18-info-label">Email</span>
                            <span class="t18-info-value text-xs break-all"><?= getData("email", "seller_order_address", "type = 'billing' AND order_id = '$order_id'") ?></span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <span class="block text-xs font-bold text-gray-500 mb-1">Full Address</span>
                        <span class="block text-sm text-gray-900 leading-relaxed">
                            <?= getData("address", "seller_order_address", "type = 'billing' AND order_id = '$order_id'") ?><br>
                            <?= getData("city", "seller_order_address", "type = 'billing' AND order_id = '$order_id'") ?>,
                            <?= getData("state", "seller_order_address", "type = 'billing' AND order_id = '$order_id'") ?>
                            <?= getData("pin_code", "seller_order_address", "type = 'billing' AND order_id = '$order_id'") ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Courier Info (Full Width) -->
            <?php if (!empty($courier_service)) : ?>
                <div class="t18-card mb-6">
                    <div class="t18-card-header">
                        <i class="bi bi-truck"></i>
                        <h3>Courier Information</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <span class="block text-xs text-gray-500 font-bold mb-1">Courier Service</span>
                            <span class="font-semibold text-gray-900"><?= $courier_service ?></span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 font-bold mb-1">Tracking ID</span>
                            <span class="font-semibold text-gray-900"><?= $tracking_id ?></span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 font-bold mb-1">Expected Date</span>
                            <span class="font-semibold text-gray-900"><?= $expected_delivery_date ? date('M d, Y', strtotime($expected_delivery_date)) : 'Update soon' ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Customized Gift -->
            <?php if ($customized_gift) : ?>
                <div class="t18-card mb-6">
                    <div class="t18-card-header">
                        <i class="bi bi-gift"></i>
                        <h3>Customized Gift</h3>
                    </div>
                    <div class="text-sm text-gray-700 leading-relaxed [&_ul]:ml-6 [&_ul]:mt-2 [&_ul]:list-disc">
                        <?= preg_replace('/customers\/\//', UPLOADS_URL . 'customers//', $customized_gift) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Contact -->
            <p class="mt-10 text-center text-sm text-gray-500">
                Contact us for any help at
                <a href="tel:<?= getSettings("phone") ?>" class="font-bold" style="color: var(--t18-primary);">
                    <?= getSettings("phone") ?>
                </a>
                — We're always here to assist you.
            </p>

        </div>
    </div>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

</body>

</html>