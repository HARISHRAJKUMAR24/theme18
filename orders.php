<?php
include_once __DIR__ . "/includes/files_includes.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>
    
    <style>
        :root {
            --t18-primary: <?= htmlspecialchars(getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#64748b') ?>;
            --t18-hover: <?= htmlspecialchars(getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#475569') ?>;
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

        /* ==================== CARDS ==================== */
        .t18-card {
            background: #fff;
            border-radius: 24px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 12%, #e5e7eb);
            box-shadow: 0 10px 30px -12px color-mix(in srgb, var(--t18-primary) 15%, transparent);
            position: relative;
            z-index: 1;
            height: 100%;
        }

        .t18-profile-top {
            background: linear-gradient(145deg, color-mix(in srgb, var(--t18-primary) 10%, white) 0%, color-mix(in srgb, var(--t18-primary) 4%, white) 100%);
            border-bottom: 1px solid color-mix(in srgb, var(--t18-primary) 10%, white);
            border-radius: 24px 24px 0 0;
            position: relative;
            overflow: hidden;
        }

        .t18-avatar {
            border: 4px solid #fff;
            box-shadow: 0 8px 22px color-mix(in srgb, var(--t18-primary) 15%, transparent);
        }

        /* ==================== SIDEBAR ==================== */
        .t18-sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid transparent;
            border-radius: 14px;
            padding: 14px 16px;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .t18-sidebar-link:hover {
            background: color-mix(in srgb, var(--t18-primary) 5%, white);
            border-color: color-mix(in srgb, var(--t18-primary) 20%, white);
            color: var(--t18-primary);
            transform: translateX(2px);
        }

        .t18-sidebar-link.active {
            background: color-mix(in srgb, var(--t18-primary) 10%, white);
            border-color: color-mix(in srgb, var(--t18-primary) 30%, white);
            color: var(--t18-primary);
        }

        .t18-sidebar-link.logout:hover {
            background: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
        }

        /* ==================== DATATABLES CUSTOMIZATION ==================== */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate { padding: 12px 4px; }
        
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter { color: #6b7280; font-size: 13px; }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 10px 14px;
            background: #f9fafb;
            font-size: 13px;
            color: #1f2937;
            transition: all 0.2s ease;
            outline: none;
            font-weight: 500;
        }

        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--t18-primary);
            background: #fff;
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--t18-primary) 15%, transparent);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid #e5e7eb !important;
            border-radius: 10px !important;
            padding: 8px 14px !important;
            margin: 0 2px !important;
            background: #fff !important;
            color: #4b5563 !important;
            transition: all .2s ease;
            font-weight: 600;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: color-mix(in srgb, var(--t18-primary) 5%, white) !important;
            color: var(--t18-primary) !important;
            border-color: var(--t18-primary) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: var(--t18-primary) !important;
            color: #fff !important;
            border-color: var(--t18-primary) !important;
            box-shadow: 0 4px 12px -2px var(--t18-primary);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #f9fafb !important;
            color: #d1d5db !important;
            border-color: #f3f4f6 !important;
            cursor: not-allowed;
        }

        /* Table */
        #ordersTable {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        
        #ordersTable thead th {
            background: color-mix(in srgb, var(--t18-primary) 8%, #fff);
            color: var(--t18-primary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 16px;
            text-align: left;
            border-bottom: 2px solid color-mix(in srgb, var(--t18-primary) 15%, #fff);
            white-space: nowrap;
        }

        #ordersTable thead th:first-child { border-top-left-radius: 12px; }
        #ordersTable thead th:last-child { border-top-right-radius: 12px; }

        #ordersTable tbody td {
            padding: 16px;
            font-size: 0.85rem;
            color: #374151;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
            font-weight: 500;
        }

        #ordersTable tbody tr { transition: background-color 0.2s ease; }
        #ordersTable tbody tr:hover { background: #f9fafb; }

        .dataTables_empty {
            text-align: center !important;
            padding: 50px 20px !important;
            font-size: 15px !important;
            color: #9ca3af !important;
            font-weight: 500;
        }
    </style>
</head>

<body class="font-sans min-h-screen flex flex-col relative">

    <?php
    $primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#64748b';
    $hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#475569';
    ?>

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <!-- 3D Background Shapes -->
    <div class="t18-shape-wrap">
        <div class="t18-shape t18-shape-1"></div>
        <div class="t18-shape t18-shape-2"></div>
    </div>

    <!-- Main Container -->
    <main class="flex-1 py-10 sm:py-14 relative z-10">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="mb-8 sm:mb-10 text-center">
                <p class="text-xs font-bold uppercase tracking-widest text-[var(--t18-primary)] mb-2">My Account</p>
                <h1 class="text-3xl sm:text-4xl font-black text-gray-900 mb-2">Order History</h1>
                <p class="text-sm text-gray-500 max-w-lg mx-auto">View and track your recent purchases in one place.</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

                <!-- Left Sidebar -->
                <div class="w-full lg:w-1/3">
                    <div class="t18-card flex flex-col">
                        <!-- Profile Info -->
                        <div class="t18-profile-top px-6 pt-8 pb-6 flex flex-col items-center text-center">
                            <div class="relative mb-4">
                                <img id="previewImage"
                                    src="<?= !empty(customer('photo')) ? UPLOADS_URL . customer('photo') : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80' ?>"
                                    alt="User Profile"
                                    class="t18-avatar relative z-10 w-24 h-24 rounded-full object-cover">
                                <a href="<?= $storeUrl ?>profile"
                                    class="absolute bottom-0 right-0 z-20 w-8 h-8 rounded-full flex items-center justify-center border-2 border-white bg-[var(--t18-primary)] text-white shadow-md hover:bg-[var(--t18-hover)] transition">
                                    <i class='bx bx-edit text-sm'></i>
                                </a>
                            </div>
                            <h2 class="relative z-10 font-bold text-xl text-gray-900"><?= customer("name") ?></h2>
                            <p class="relative z-10 text-gray-500 text-sm mt-1 break-all"><?= customer("email") ?></p>
                        </div>

                        <!-- Sidebar Links -->
                        <div class="p-4 sm:p-5 space-y-2 flex-1">
                            <a href="<?= $storeUrl ?>profile" class="t18-sidebar-link">
                                <i class='bx bx-user text-xl'></i>
                                <span>My Profile</span>
                            </a>
                            <a href="<?= $storeUrl ?>orders" class="t18-sidebar-link active">
                                <i class='bx bx-package text-xl'></i>
                                <span>My Orders</span>
                            </a>
                            <a href="<?= $storeUrl ?>wishlists" class="t18-sidebar-link">
                                <i class='bx bx-heart text-xl'></i>
                                <span>Wishlists</span>
                            </a>
                            <a href="<?= $storeUrl ?>logout" class="t18-sidebar-link logout">
                                <i class='bx bx-log-out text-xl'></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right: Orders Table -->
                <div class="w-full lg:w-2/3">
                    <div class="t18-card p-5 sm:p-7 flex flex-col">
                        
                        <div class="flex items-center justify-between gap-3 mb-6">
                            <div>
                                <h2 class="text-xl font-black text-gray-900">Your Orders</h2>
                                <p class="text-xs text-gray-500 mt-1">Showing all your past and current orders</p>
                            </div>
                            <span class="flex-shrink-0 inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold bg-[color-mix(in_srgb,var(--t18-primary)_10%,white)] text-[var(--t18-primary)]">
                                <i class='bx bx-time-five text-base'></i> Recent
                            </span>
                        </div>

                        <!-- Orders Table Container -->
                        <div class="overflow-x-auto">
                            <table id="ordersTable" class="min-w-full table-auto">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

    <!-- DataTables Script -->
    <script>
        $(document).ready(function() {
            // Initialize DataTable with server-side processing
            var table = $('#ordersTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "shop/ajax/orders.php",
                    "type": "POST",
                    "data": function(d) {
                        if (!d.order || d.order.length === 0) {
                            d.order = [[1, 'desc']];
                        }
                    }
                },
                "columns": [
                    { "data": "order_id", "className": "whitespace-nowrap font-bold text-[var(--t18-primary)]" },
                    {
                        "data": "created_at",
                        "className": "whitespace-nowrap",
                        "render": function(data, type, row) {
                            if (!data) return "";
                            const date = new Date(data);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            return `${day}/${month}/${year}`;
                        }
                    },
                    { "data": "total", "className": "whitespace-nowrap font-semibold" },
                    { 
                        "data": "payment_method", 
                        "className": "whitespace-nowrap",
                        "render": function(data) {
                            return `<span class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-bold">${data}</span>`;
                        }
                    },
                    { 
                        "data": "status", 
                        "className": "whitespace-nowrap",
                        "render": function(data) {
                            let colorClass = 'bg-blue-50 text-blue-600';
                            if (data.toLowerCase().includes('complet')) colorClass = 'bg-green-50 text-green-600';
                            if (data.toLowerCase().includes('cancel')) colorClass = 'bg-red-50 text-red-600';
                            if (data.toLowerCase().includes('pending')) colorClass = 'bg-yellow-50 text-yellow-600';
                            return `<span class="px-2.5 py-1 rounded-md text-xs font-bold ${colorClass}">${data}</span>`;
                        }
                    }
                ],
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50], [10, 25, 50]],
                "order": [[1, 'desc']],
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search orders...",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ orders",
                    "infoEmpty": "No orders found",
                    "infoFiltered": "(filtered from _MAX_ total orders)",
                    "zeroRecords": "You haven't placed any orders yet 🛒",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "previous": "‹",
                        "next": "›"
                    },
                    "processing": '<div class="flex items-center gap-2 text-[var(--t18-primary)] font-bold"><i class="bi bi-arrow-repeat animate-spin"></i> Loading...</div>'
                },
                "responsive": true,
                "dom": '<"flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4"<"w-full sm:w-auto"l><"w-full sm:w-auto flex justify-end"f>>rt<"flex flex-col sm:flex-row justify-between items-center gap-4 mt-4"<"text-xs text-gray-500 font-medium"i><"flex gap-2"p>>',
                "initComplete": function() {
                    $('.dataTables_filter')
                        .addClass('w-full sm:w-auto')
                        .find('input')
                        .addClass('w-full sm:w-64 px-4 py-2 border border-gray-200 bg-gray-50 text-gray-800 focus:outline-none transition placeholder-gray-400')
                        .attr('placeholder', 'Search orders...')
                        .css('border-radius', '10px');

                    $('.dataTables_length select')
                        .addClass('px-3 py-2 border border-gray-200 bg-gray-50 text-gray-800 focus:outline-none transition')
                        .css('border-radius', '10px');
                },
                "drawCallback": function() {
                    $('.dataTables_filter')
                        .addClass('w-full sm:w-auto')
                        .find('input')
                        .addClass('w-full sm:w-64 px-4 py-2 border border-gray-200 bg-gray-50 text-gray-800 focus:outline-none transition placeholder-gray-400')
                        .attr('placeholder', 'Search orders...')
                        .css('border-radius', '10px');

                    $('.dataTables_length select')
                        .addClass('px-3 py-2 border border-gray-200 bg-gray-50 text-gray-800 focus:outline-none transition')
                        .css('border-radius', '10px');
                }
            });
        });
    </script>

</body>

</html>