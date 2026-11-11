<?php
include_once __DIR__ . "/includes/files_includes.php";

$primary_color = getData("color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?: '#64748b';
$hover_color   = getData("hover_color", "seller_settings", "(seller_id='$sellerId' AND store_id='$storeId')") ?: '#475569';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/head_links.php"; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    
    <style>
        :root {
            --t18-primary: <?= htmlspecialchars($primary_color, ENT_QUOTES, 'UTF-8') ?>;
            --t18-hover: <?= htmlspecialchars($hover_color, ENT_QUOTES, 'UTF-8') ?>;
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

        .t18-shape-3 {
            width: 150px; height: 150px;
            top: 30%; right: 15%;
            opacity: 0.4;
            animation-duration: 18s;
        }

        @keyframes floatShape {
            0% { transform: translate(0, 0) rotate(0deg) scale(1); }
            100% { transform: translate(20px, -20px) rotate(15deg) scale(1.05); }
        }

        /* ==================== MAIN LAYOUT ==================== */
        .t18-profile-container {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 16px;
        }

        .t18-profile-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 960px) {
            .t18-profile-layout {
                grid-template-columns: 300px 1fr;
            }
        }

        /* ==================== SIDEBAR ==================== */
        .t18-sidebar {
            background: #18181b;
            border-radius: 24px;
            border: 1px solid #27272a;
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            padding: 32px 24px;
            color: #fff;
            height: fit-content;
        }

        .t18-profile-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid #3f3f46;
        }

        .t18-avatar-wrap {
            position: relative;
            margin-bottom: 16px;
        }

        .t18-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #3f3f46;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .t18-avatar-edit {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--t18-primary);
            border: 2px solid #18181b;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            cursor: pointer;
            transition: all 0.2s;
        }

        .t18-avatar-edit:hover {
            background: var(--t18-hover);
            transform: scale(1.1);
        }

        .t18-sidebar h2 {
            font-size: 1.25rem;
            font-weight: 800;
            color: #fff;
            margin: 0 0 4px;
        }

        .t18-sidebar p {
            font-size: 0.85rem;
            color: #a1a1aa;
            margin: 0;
            word-break: break-all;
        }

        /* Sidebar Links */
        .t18-sidebar-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .t18-side-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 14px;
            color: #d4d4d8;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .t18-side-link:hover {
            background: #27272a;
            color: #fff;
            border-color: #3f3f46;
        }

        .t18-side-link.active {
            background: color-mix(in srgb, var(--t18-primary) 15%, #18181b);
            color: var(--t18-primary);
            border-color: color-mix(in srgb, var(--t18-primary) 30%, transparent);
        }

        .t18-side-link.logout:hover {
            background: #450a0a;
            color: #fca5a5;
            border-color: #7f1d1d;
        }

        .t18-side-link i {
            font-size: 1.2rem;
        }

        /* ==================== EDIT FORM ==================== */
        .t18-editor {
            background: #fff;
            border-radius: 24px;
            border: 1px solid color-mix(in srgb, var(--t18-primary) 12%, #e5e7eb);
            box-shadow: 0 10px 30px -12px color-mix(in srgb, var(--t18-primary) 15%, transparent);
            padding: 32px;
        }

        @media (min-width: 768px) {
            .t18-editor { padding: 40px; }
        }

        .t18-editor h3 {
            font-size: 1.5rem;
            font-weight: 900;
            color: #111827;
            margin: 0 0 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .t18-form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .t18-form-grid { grid-template-columns: 1fr 1fr; }
        }

        .t18-form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .t18-form-group.full-width {
            grid-column: 1 / -1;
        }

        .t18-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #374151;
        }

        .t18-input {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            background: #f9fafb;
            color: #111827;
            font-size: 0.95rem;
            font-weight: 500;
            outline: none;
            transition: all 0.2s ease;
        }

        .t18-input:focus {
            border-color: var(--t18-primary);
            background: #fff;
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--t18-primary) 15%, transparent);
        }

        .t18-file-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 48px;
            padding: 0 16px;
            background: #f9fafb;
            border: 1.5px dashed #d1d5db;
            border-radius: 12px;
            color: #4b5563;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .t18-file-label:hover {
            background: color-mix(in srgb, var(--t18-primary) 5%, #fff);
            border-color: var(--t18-primary);
            color: var(--t18-primary);
        }

        .t18-old-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            margin-top: 12px;
        }

        .t18-submit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 48px;
            padding: 0 32px;
            background: var(--t18-primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 20px -6px var(--t18-primary);
        }

        .t18-submit-btn:hover:not(:disabled) {
            background: var(--t18-hover);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px -6px var(--t18-hover);
        }

        .t18-submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Fix intl-tel-input */
        .t18-editor .iti { width: 100%; }
        .t18-editor .iti input[type="tel"] { padding-left: 95px !important; }
        .t18-editor .iti__selected-flag { background: transparent !important; }

        /* Toast */
        .custom-product-toast {
            z-index: 9999;
        }
    </style>
</head>

<body class="font-sans min-h-screen relative">

    <?php include_once __DIR__ . "/includes/nav-bar.php"; ?>

    <!-- 3D Background Shapes -->
    <div class="t18-shape-wrap">
        <div class="t18-shape t18-shape-1"></div>
        <div class="t18-shape t18-shape-2"></div>
        <div class="t18-shape t18-shape-3"></div>
    </div>

    <!-- Profile Section -->
    <div class="t18-profile-container">
        
        <!-- Page Title -->
        <div class="mb-8 text-center md:text-left">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">My Profile</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your personal information and account settings.</p>
        </div>

        <div class="t18-profile-layout">

            <?php $page = basename($_SERVER['PHP_SELF']); ?>

            <!-- Left Sidebar -->
            <div class="t18-sidebar">
                
                <!-- Profile Info -->
                <div class="t18-profile-info">
                    <div class="t18-avatar-wrap">
                        <img id="previewImage"
                            src="<?= !empty(customer('photo')) ? UPLOADS_URL . customer('photo') : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80' ?>"
                            alt="User Profile"
                            class="t18-avatar">
                        <label for="photo" class="t18-avatar-edit">
                            <i class='bx bx-edit text-sm'></i>
                        </label>
                    </div>
                    <h2><?= customer("name") ?></h2>
                    <p><?= customer("email") ?></p>
                </div>

                <!-- Sidebar Links -->
                <div class="t18-sidebar-links">
                    <a href="<?= $storeUrl ?>profile" class="t18-side-link <?= $page == 'profile.php' ? 'active' : '' ?>">
                        <i class='bx bx-user'></i>
                        <span>My Profile</span>
                    </a>
                    <a href="<?= $storeUrl ?>orders" class="t18-side-link <?= $page == 'orders.php' ? 'active' : '' ?>">
                        <i class='bx bx-package'></i>
                        <span>My Orders</span>
                    </a>
                    <a href="<?= $storeUrl ?>wishlists" class="t18-side-link <?= $page == 'wishlists.php' ? 'active' : '' ?>">
                        <i class='bx bx-heart'></i>
                        <span>Wishlists</span>
                    </a>
                    <a href="<?= $storeUrl ?>logout" class="t18-side-link logout">
                        <i class='bx bx-log-out'></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>


            <!-- Right Edit Form -->
            <div class="t18-editor">
                <h3>Edit Profile</h3>

                <form id="form" method="post" enctype="multipart/form-data">
                    <div class="t18-form-grid">
                        
                        <!-- Name -->
                        <div class="t18-form-group">
                            <label for="name" class="t18-label">Full Name</label>
                            <input type="text" name="name" id="name" value="<?= customer('name') ?>" class="t18-input" placeholder="Enter your name" required>
                        </div>

                        <!-- Email -->
                        <div class="t18-form-group">
                            <label for="email" class="t18-label">Email Address</label>
                            <input type="email" name="email" id="email" value="<?= customer('email') ?>" class="t18-input" placeholder="Enter your email">
                        </div>

                        <!-- Phone -->
                        <div class="t18-form-group full-width">
                            <label for="phone" class="t18-label">Phone Number</label>
                            <input type="tel" name="phone[main]" id="phone" value="<?= customer('phone') ?>" class="t18-input" placeholder="Enter your phone" required>
                        </div>

                        <!-- Photo Upload -->
                        <div class="t18-form-group full-width">
                            <label class="t18-label">Profile Photo</label>
                            <label class="t18-file-label">
                                <i class='bx bx-upload text-lg'></i> Choose File
                                <input type="file" name="photo" id="photo" class="hidden" accept="image/*">
                            </label>

                            <?php if (!empty(customer("photo"))): ?>
                                <img id="oldImage" src="<?= UPLOADS_URL . customer("photo") ?>" alt="Profile Photo" class="t18-old-image">
                            <?php endif; ?>
                        </div>

                        <!-- Submit Button -->
                        <div class="t18-form-group full-width mt-4 pt-6 border-t border-gray-100 flex justify-end">
                            <button type="submit" class="t18-submit-btn">
                                Save Changes
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>

    <!-- IntlTelInput -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

    <script>
        // Custom toast function
        function showCustomToast(message, type) {
            const existingToasts = document.querySelectorAll('.custom-product-toast');
            existingToasts.forEach(toast => toast.remove());

            const toast = document.createElement('div');
            toast.className = 'custom-product-toast fixed top-6 right-6 z-[9999] transform transition-all duration-300 ease-out translate-x-full opacity-0';
            toast.innerHTML = `
                <div style="background-color: var(--t18-primary) !important; color: white !important;" class="px-5 py-4 rounded-xl shadow-2xl flex items-center gap-3 border border-white/20">
                    <i class='bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'} text-xl'></i>
                    <span class="font-bold text-sm">${message}</span>
                </div>
            `;

            document.body.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
                toast.classList.add('translate-x-0', 'opacity-100');
            });

            setTimeout(() => {
                toast.classList.remove('translate-x-0', 'opacity-100');
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (toast.parentNode) toast.remove();
                }, 300);
            }, 3000);
        }

        // Phone Input (India only)
        const phoneInputField = document.querySelector("#phone");
        const phoneInput = window.intlTelInput(phoneInputField, {
            separateDialCode: true,
            onlyCountries: ["in"],
            initialCountry: "in",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });

        // Live Photo Preview
        document.getElementById("photo").addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("previewImage").src = e.target.result;
                    const oldImage = document.getElementById("oldImage");
                    if (oldImage) oldImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // AJAX Save Profile
        $(document).on("submit", "#form", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const fullPhoneNumber = phoneInput.getNumber();
            formData.append("phone[full]", fullPhoneNumber);

            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html(`<i class='bx bx-loader-alt animate-spin mr-2'></i> Saving...`);

            $.ajax({
                url: "shop/ajax/profile.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(result) {
                    submitBtn.prop('disabled', false).html(originalText);
                    try {
                        const response = result && JSON.parse(result);
                        if (response) {
                            if (response.success) {
                                showCustomToast(response.message || "Profile updated successfully!", 'success');
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                showCustomToast(response.message || "Failed to update profile.", 'error');
                            }
                        } else {
                            showCustomToast("Invalid response from server.", 'error');
                        }
                    } catch (error) {
                        console.error("Error parsing response:", error);
                        showCustomToast("Invalid response from server.", 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    submitBtn.prop('disabled', false).html(originalText);
                    showCustomToast("Something went wrong. Please try again.", 'error');
                }
            });
        });
    </script>

</body>

</html>