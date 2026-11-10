<!-- ============================================================
     THEME 18 — FOOTER
     Matches theme-18 card design: soft rounded blocks,
     bubble accents, DB primary/hover colors.
     Uses Bootstrap Icons (bi-*) for contact + socials.
     Requires: bootstrap-icons stylesheet in head_links.php
============================================================ -->
<?php
$footerPrimary = getData('color',       'seller_settings', "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#ed168b';
$footerHover   = getData('hover_color', 'seller_settings', "(seller_id='$sellerId' AND store_id='$storeId')") ?? '#dc007b';

if (!function_exists('hexToRgba18')) {
    function hexToRgba18($hex, $opacity = 0.08)
    {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) $hex = "$hex[0]$hex[0]$hex[1]$hex[1]$hex[2]$hex[2]";
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba($r, $g, $b, $opacity)";
    }
}
?>

<footer class="t18-footer"
    style="
            --t18f-primary: <?= htmlspecialchars($footerPrimary) ?>;
            --t18f-hover:   <?= htmlspecialchars($footerHover) ?>;
        ">

    <!-- soft bubble accents -->
    <span class="t18-footer-bubble t18-footer-bubble-1" aria-hidden="true"></span>
    <span class="t18-footer-bubble t18-footer-bubble-2" aria-hidden="true"></span>
    <span class="t18-footer-bubble t18-footer-bubble-3" aria-hidden="true"></span>

    <div class="t18-footer-inner">

        <!-- Column 1: Logo + address -->
        <div class="t18-footer-col">
            <div class="t18-footer-brand">
                <?php if (getSettings("logo")): ?>
                    <img src="<?= UPLOADS_URL . getSettings("logo") ?>" alt="Logo" class="t18-footer-logo">
                <?php else: ?>
                    <div class="t18-footer-brand-badge">
                        <?= htmlspecialchars(strtoupper(mb_substr($storeName ?? 'S', 0, 1))) ?>
                    </div>
                <?php endif; ?>
                <h3 class="t18-footer-brand-name"><?= htmlspecialchars($storeName ?? 'Store') ?></h3>
            </div>

            <p class="t18-footer-text">
                <?= getSettings("address") ?><br>
                <?php if (getSettings("gst_number")): ?>
                    <span class="t18-footer-meta">GSTIN: <?= getSettings("gst_number") ?></span><br>
                <?php endif; ?>
                <?php if (getSettings("fssai_number")): ?>
                    <span class="t18-footer-meta">FSSAI: <?= getSettings("fssai_number") ?></span>
                <?php endif; ?>
            </p>
        </div>

        <!-- Column 2: Quick links -->
        <div class="t18-footer-col">
            <h3 class="t18-footer-heading">Quick Links</h3>
            <ul class="t18-footer-links">
                <?php
                $pages = getPages();
                if ($pages->rowCount()):
                    foreach ($pages as $page):
                ?>
                        <li>
                            <a href="<?= $storeUrl . "pages/" . $page['slug'] ?>" class="t18-footer-link">
                                <?= htmlspecialchars($page['name']) ?>
                            </a>
                        </li>
                <?php
                    endforeach;
                endif;
                ?>
                <li><a href="<?= $storeUrl ?>profile" class="t18-footer-link">Profile</a></li>
                <li><a href="<?= $storeUrl ?>delivery-areas" class="t18-footer-link">Delivery Areas</a></li>
                <li><a href="<?= $storeUrl ?>cart" class="t18-footer-link">Cart</a></li>
                <li><a href="<?= $storeUrl ?>track-order" class="t18-footer-link">Track Order</a></li>
                <li><a href="<?= $storeUrl ?>wishlists" class="t18-footer-link">Wishlists</a></li>
                <li><a href="<?= $storeUrl ?>blog" class="t18-footer-link">Blogs</a></li>
            </ul>
        </div>

        <!-- Column 3: Contact -->
        <div class="t18-footer-col">
            <h3 class="t18-footer-heading">Contact</h3>
            <ul class="t18-footer-contact">
                <?php if (getSettings("email")): ?>
                    <li>
                        <span class="t18-footer-ic"><i class="bi bi-envelope"></i></span>
                        <a href="mailto:<?= getSettings("email") ?>" class="t18-footer-link">
                            <?= getSettings("email") ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (getSettings("phone")): ?>
                    <li>
                        <span class="t18-footer-ic"><i class="bi bi-telephone"></i></span>
                        <a href="tel:<?= getSettings("phone") ?>" class="t18-footer-link">
                            <?= formatPhoneNumber(getSettings("phone")) ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (getSettings("address")): ?>
                    <li>
                        <span class="t18-footer-ic"><i class="bi bi-geo-alt"></i></span>
                        <span class="t18-footer-text-inline"><?= getSettings("address") ?></span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Column 4: Socials -->
        <?php
        $hasSocialLinks = getSettings("facebook") || getSettings("instagram") || getSettings("twitter")
            || getSettings("linkedin") || getSettings("youtube") || getSettings("pinterest") || getSettings("sharechat");

        if ($hasSocialLinks):
        ?>
            <div class="t18-footer-col">
                <h3 class="t18-footer-heading">Follow Us</h3>
                <div class="t18-footer-socials">

                    <?php if (getSettings("facebook")): ?>
                        <a href="<?= getSettings("facebook") ?>" target="_blank" rel="noopener"
                            class="t18-footer-social" aria-label="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (getSettings("instagram")): ?>
                        <a href="<?= getSettings("instagram") ?>" target="_blank" rel="noopener"
                            class="t18-footer-social" aria-label="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (getSettings("twitter")): ?>
                        <a href="<?= getSettings("twitter") ?>" target="_blank" rel="noopener"
                            class="t18-footer-social" aria-label="Twitter">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (getSettings("linkedin")): ?>
                        <a href="<?= getSettings("linkedin") ?>" target="_blank" rel="noopener"
                            class="t18-footer-social" aria-label="LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (getSettings("youtube")): ?>
                        <a href="<?= getSettings("youtube") ?>" target="_blank" rel="noopener"
                            class="t18-footer-social" aria-label="YouTube">
                            <i class="bi bi-youtube"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (getSettings("pinterest")): ?>
                        <a href="<?= getSettings("pinterest") ?>" target="_blank" rel="noopener"
                            class="t18-footer-social" aria-label="Pinterest">
                            <i class="bi bi-pinterest"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (getSettings("sharechat")): ?>
                        <a href="<?= getSettings("sharechat") ?>" target="_blank" rel="noopener"
                            class="t18-footer-social" aria-label="ShareChat">
                            <img src="<?= APP_URL ?>assets/img/sharechat.webp" alt="ShareChat" class="t18-footer-sharechat">
                        </a>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- Copyright -->
    <div class="t18-footer-copyright-wrap">
        <p class="t18-footer-copyright">
            Copyright © <?= date('Y') ?>, <?= htmlspecialchars($storeName) ?> All Rights Reserved.
            <span class="t18-footer-copyright-sep">·</span>
            Powered by
            <a href="https://ztorespot.com/?utm_campaign=poweredby&utm_medium=ztorespot&utm_source=onlinestore&utm_tag=<?= urlencode($storeName) ?>"
                target="_blank" rel="noopener" class="t18-footer-copyright-link">
                Ztorespot
            </a>
        </p>
    </div>

    <!-- Disclaimer -->
    <?php if (getData("disclaimer", "settings") && !$isCustomDomain): ?>
        <div class="t18-footer-disclaimer">
            <h4 class="t18-footer-disclaimer-heading">Disclaimer</h4>
            <p class="t18-footer-disclaimer-text">
                <?= getData("disclaimer", "settings") ?>
            </p>
        </div>
    <?php endif; ?>

</footer>


<!-- ============================================================
     STYLES — Theme 18 Footer
============================================================ -->
<style>
    .t18-footer {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        margin-top: 48px;
        padding: 56px 16px 32px;
        background:
            radial-gradient(ellipse at top left,
                color-mix(in srgb, var(--t18f-primary) 10%, #ffffff) 0%,
                #ffffff 60%),
            radial-gradient(ellipse at bottom right,
                color-mix(in srgb, var(--t18f-hover) 8%, #ffffff) 0%,
                #ffffff 70%);
        border-radius: 36px 36px 0 0;
        box-shadow:
            0 -18px 40px -20px color-mix(in srgb, var(--t18f-primary) 25%, transparent),
            inset 0 1px 0 rgba(255, 255, 255, .9);
    }

    /* soft bubble accents */
    .t18-footer-bubble {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        z-index: 0;
        background: radial-gradient(circle at 30% 30%,
                color-mix(in srgb, var(--t18f-hover) 30%, #ffffff) 0%,
                color-mix(in srgb, var(--t18f-primary) 70%, #ffffff) 60%,
                var(--t18f-primary) 100%);
        opacity: .14;
        animation: t18FooterFloat 10s ease-in-out infinite alternate;
    }

    .t18-footer-bubble-1 {
        width: 200px;
        height: 200px;
        top: -70px;
        right: 4%;
    }

    .t18-footer-bubble-2 {
        width: 140px;
        height: 140px;
        bottom: 8%;
        left: -40px;
        animation-delay: -4s;
    }

    .t18-footer-bubble-3 {
        width: 90px;
        height: 90px;
        top: 40%;
        right: 40%;
        opacity: .08;
        animation-delay: -7s;
    }

    @keyframes t18FooterFloat {
        0% {
            transform: translate(0, 0) scale(1);
        }

        50% {
            transform: translate(10px, -14px) scale(1.05);
        }

        100% {
            transform: translate(-8px, 12px) scale(.97);
        }
    }

    /* inner grid */
    .t18-footer-inner {
        position: relative;
        z-index: 1;
        max-width: 1450px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr;
        gap: 34px;
    }

    @media (min-width: 640px) {
        .t18-footer-inner {
            grid-template-columns: 1fr 1fr;
            gap: 36px;
        }
    }

    @media (min-width: 1024px) {
        .t18-footer-inner {
            grid-template-columns: 1.3fr 1fr 1fr 1fr;
            gap: 40px;
        }
    }

    .t18-footer-col {
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    /* brand */
    .t18-footer-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .t18-footer-logo {
        height: 46px;
        width: auto;
        max-width: 160px;
        object-fit: contain;
        display: block;
    }

    .t18-footer-brand-badge {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--t18f-primary);
        color: #fff;
        font-weight: 900;
        font-size: 22px;
        box-shadow:
            0 10px 22px color-mix(in srgb, var(--t18f-primary) 30%, transparent),
            inset 0 -2px 4px rgba(0, 0, 0, .1);
    }

    .t18-footer-brand-name {
        margin: 0;
        font-size: 18px;
        font-weight: 900;
        letter-spacing: -.4px;
        color: var(--t18f-primary);
    }

    .t18-footer-text {
        color: #4b5563;
        font-size: 13px;
        line-height: 1.7;
        margin: 0;
    }

    .t18-footer-meta {
        color: #6b7280;
        font-size: 12px;
    }

    /* headings */
    .t18-footer-heading {
        margin: 0 0 14px;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--t18f-primary);
    }

    .t18-footer-heading::after {
        content: "";
        display: block;
        width: 28px;
        height: 3px;
        margin-top: 8px;
        background: var(--t18f-hover);
        border-radius: 4px;
    }

    /* links grid */
    .t18-footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 16px;
        font-size: 13px;
    }

    .t18-footer-link {
        color: #4b5563;
        text-decoration: none;
        transition: color .2s ease, transform .2s ease;
        display: inline-block;
    }

    .t18-footer-link:hover {
        color: var(--t18f-hover);
        transform: translateX(2px);
    }

    /* contact */
    .t18-footer-contact {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
        font-size: 13px;
    }

    .t18-footer-contact li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .t18-footer-ic {
        width: 28px;
        height: 28px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: color-mix(in srgb, var(--t18f-primary) 12%, #ffffff);
        color: var(--t18f-primary);
        font-size: 14px;
        box-shadow: 0 3px 8px color-mix(in srgb, var(--t18f-primary) 15%, transparent);
    }

    .t18-footer-text-inline {
        color: #4b5563;
        line-height: 1.6;
    }

    /* socials */
    .t18-footer-socials {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .t18-footer-social {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #ffffff;
        color: var(--t18f-primary);
        border: 1px solid color-mix(in srgb, var(--t18f-primary) 15%, transparent);
        box-shadow:
            0 8px 18px rgba(0, 0, 0, .08),
            0 3px 6px rgba(0, 0, 0, .04);
        transition: all .25s ease;
    }

    .t18-footer-social i {
        font-size: 18px;
        line-height: 1;
    }

    .t18-footer-social:hover {
        background: var(--t18f-primary);
        color: #fff;
        border-color: var(--t18f-primary);
        transform: translateY(-3px) scale(1.06);
        box-shadow: 0 12px 24px color-mix(in srgb, var(--t18f-primary) 40%, transparent);
    }

    .t18-footer-sharechat {
        width: 20px;
        height: 20px;
        object-fit: contain;
        display: block;
    }

    /* copyright */
    .t18-footer-copyright-wrap {
        position: relative;
        z-index: 1;
        margin-top: 40px;
        padding: 16px 0 0;
        border-top: 1px solid color-mix(in srgb, var(--t18f-primary) 12%, transparent);
    }

    .t18-footer-copyright {
        text-align: center;
        margin: 0;
        font-size: 12px;
        color: #4b5563;
        line-height: 1.7;
    }

    .t18-footer-copyright-sep {
        margin: 0 6px;
        opacity: .5;
    }

    .t18-footer-copyright-link {
        color: var(--t18f-primary);
        font-weight: 700;
        text-decoration: none;
        transition: color .2s ease;
    }

    .t18-footer-copyright-link:hover {
        color: var(--t18f-hover);
        text-decoration: underline;
    }

    /* disclaimer */
    .t18-footer-disclaimer {
        position: relative;
        z-index: 1;
        margin-top: 28px;
        padding: 18px 20px;
        border-radius: 16px;
        background: color-mix(in srgb, var(--t18f-primary) 5%, #ffffff);
        border: 1px solid color-mix(in srgb, var(--t18f-primary) 12%, transparent);
        text-align: center;
    }

    .t18-footer-disclaimer-heading {
        margin: 0 0 6px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--t18f-primary);
    }

    .t18-footer-disclaimer-text {
        margin: 0;
        font-size: 11px;
        line-height: 1.65;
        color: #6b7280;
    }

    /* responsive */
    @media (max-width: 1023px) {
        .t18-footer {
            padding: 44px 14px 28px;
            border-radius: 28px 28px 0 0;
        }
    }

    @media (max-width: 640px) {
        .t18-footer {
            padding: 36px 12px 24px;
            border-radius: 22px 22px 0 0;
            margin-top: 32px;
        }

        .t18-footer-links {
            grid-template-columns: 1fr;
        }

        .t18-footer-brand-name {
            font-size: 16px;
        }

        .t18-footer-logo {
            height: 40px;
        }

        .t18-footer-social {
            width: 36px;
            height: 36px;
        }

        .t18-footer-social i {
            font-size: 16px;
        }

        .t18-footer-bubble-1 {
            width: 140px;
            height: 140px;
            top: -50px;
            right: -20px;
        }

        .t18-footer-bubble-2 {
            width: 100px;
            height: 100px;
            bottom: 6%;
            left: -30px;
        }

        .t18-footer-bubble-3 {
            display: none;
        }
    }

    /* accessibility */
    @media (prefers-reduced-motion: reduce) {
        .t18-footer-bubble {
            animation: none !important;
        }

        .t18-footer-social,
        .t18-footer-link {
            transition: none !important;
        }
    }
</style>
<script>
    lucide.createIcons();
</script>
<script>
    $(document).on("click", ".searchBtn", function(e) {
        e.preventDefault();
        console.log('ssss');
        const searchInput = $(".searchInput");
        const searchCategory = $(".searchCategory");
        let url = '';
        console.log('searchCategory ' + searchCategory.val());
        if (searchCategory.val() == "undefined") {
            url = '<?= $storeUrl ?>search/' + searchInput.val();
        } else {
            url = '<?= $storeUrl ?>search/' + searchInput.val() + "?category=" + searchCategory.val();
        }
        if (searchInput.val() !== "") window.location.href = url;
    });
</script>
<!--JS File Include -->

<!--This File For my Customise Js For my Theme 9-->
<script src="<?= APP_URL ?>themes/theme9/js/script.js"></script>

<!--This File For Cart Functions For Theme 9-->
<script src="<?= APP_URL ?>themes/theme9/ajax/common-cart.js"></script>

<!--This File For Track Cart & Checkout Theme 9-->
<script src="<?= APP_URL ?>shop/javascripts/utm-tracker.js"></script>

<!--This File For Wishlist-->
<script src="<?= APP_URL ?>themes/theme9/ajax/common-wishlist.js"></script>

<!--This File For Wishlist-->
<script src="<?= APP_URL ?>assets/js/global.js"></script>
<script src="<?= APP_URL ?>assets/js/theme3/app.js"></script>




<!--This File For Checkout -->
<script src="<?= APP_URL ?>assets/js/theme3/checkout.js"></script>
<script src="<?= APP_URL ?>shop/javascripts/checkout.js"></script>


<!-- Datatable JS -->
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="<?= APP_URL ?>shop/javascripts/orders.js"></script>