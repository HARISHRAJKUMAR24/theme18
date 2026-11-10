<?php include_once __DIR__ . "/includes/files_includes.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!--Php File Include For Head Links & Scripts-->
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>
</head>

<body class="font-sans min-h-screen" style="background: color-mix(in srgb, <?= htmlspecialchars($primary_color ?? '#ed168b') ?> 5%, #fafafa);">


    <?php include_once __DIR__ . "/includes/minimum-order.php"; ?>

    <!--Php File Include For Nav Bar-->
    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <!-- Your Cart Section Start-->
    <div class="py-6 sm:py-16" id="content">
        <!-- Max width applied so it has left/right gaps and doesn't stretch full screen -->
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            
            <!-- Page Title -->
            <div class="mb-6 sm:mb-10 flex items-end justify-between">
                <div>
                    <h1 class="text-xl sm:text-3xl font-black text-gray-900 tracking-tight">Shopping Cart</h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Review your items before checkout</p>
                </div>
            </div>

            <!-- AJAX Container -->
            <div id="ajaxCartContainer">
                <!-- The AJAX file will load here -->
                <div class="flex justify-center py-16 sm:py-20">
                    <div class="animate-spin rounded-full h-8 w-8 sm:h-10 sm:w-10 border-b-2 border-gray-900"></div>
                </div>
            </div>

        </div>
    </div>
    <!-- Your Cart Section End-->

    <!--Footer File Includes that file has all JS Files includes links-->
    <?php include_once __DIR__ . "/includes/footer.php"; ?>
    
    <!--This File For Cart Page View-->
    <script src="<?= APP_URL ?>shop/javascripts/cart.js"></script>

    <!-- Load Cart via AJAX for smooth rendering -->
    <script>
        $(document).ready(function() {
            // Fetch the cart HTML via AJAX
            $.ajax({
                url: 'shop/ajax/cart.php',
                type: 'GET',
                success: function(response) {
                    $('#ajaxCartContainer').html(response);
                },
                error: function() {
                    // $('#ajaxCartContainer').html('<div class="text-center text-red-500 py-10 font-bold">Failed to load cart. Please refresh the page.</div>');
                }
            });
        });
    </script>

</body>
</html>