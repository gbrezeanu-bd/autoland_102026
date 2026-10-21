<!doctype html>
<!--[if IE 7 ]> <html lang="en" class="ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9 ]> <html lang="en" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="ro">
    <!--<![endif]-->

    <head>
        <!-- Google Tag Manager -->
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            
            // Setăm starea inițială ca "denied" (refuzat) conform GDPR
            gtag('consent', 'default', {
                'ad_storage': 'denied',
                'ad_user_data': 'denied',
                'ad_personalization': 'denied',
                'analytics_storage': 'denied'
            });

            // Alegerea salvată de bannerul de cookies (CookiePreferences) se aplică înainte să pornească GTM
            function cookieConsentState(prefs) {
                var has = function (p) { return prefs.indexOf(p) !== -1 ? 'granted' : 'denied'; };
                return {
                    'analytics_storage': has('analytics'),
                    'ad_storage': has('googleads'),
                    'ad_user_data': has('googleads'),
                    'ad_personalization': has('googleads')
                };
            }
            (function () {
                var m = document.cookie.match(/(?:^|;\s*)CookiePreferences=([^;]*)/);
                if (!m) return;
                try {
                    var prefs = JSON.parse(decodeURIComponent(m[1]));
                    if (prefs && prefs.length !== undefined) {
                        gtag('consent', 'update', cookieConsentState(prefs));
                    }
                } catch (e) {}
            })();
        </script>

        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-N85GS6ZP');
        </script>
        <!-- End Google Tag Manager -->

        <?php
        // Universal canonical URL logic for all important pages (calculate before meta tags)
        // NOTĂ: Pentru articole (articol.php), canonical URL-ul este setat în articol.php și nu trebuie suprascris aici
        $canonical_url = rtrim(BASE_PATH, '/');
        $current_page = $_SERVER['PHP_SELF'];
        $basename = basename($current_page);

        // Skip canonical URL generation for articles - it's handled in articol.php and articole.php
        if ($basename == 'articol.php' || $basename == 'articole.php' || $basename == 'pagina.php') {
            // Canonical URL will be set by articol.php/articole.php/pagina.php via $header_scripts
            $canonical_url = ''; // Empty, will be set by the respective file
        }
        // Homepage
        elseif ($basename == 'index.php') {
            $canonical_url .= '/';
        }
        // Static pages (contact, garantie, leasing, etc.)
        elseif (in_array($basename, [
            'contact.php', 'garantie.php', 'leasing.php', 'inmatriculari.php', 'asistenta-rutiera.php',
            'politica_de_confidentialitate.php', 'sservicii.php', 'completare_recenzie.php', 'cerere_oferta.php',
            'impresii.php', 'vanzari3.php', 'thumb.php', 'lista_oferta.php', 'maintenance.php', '404.shtml'
        ])) {
            $canonical_url .= '/' . $basename;
        }
        // Car detail page
        elseif ($basename == 'vanzari_detail.php' && isset($_GET['id'])) {
            $canonical_url .= '/vanzari_detail.php?id=' . urlencode($_GET['id']);
        }
        // Listings and category/filter pages
        elseif ($basename == 'vanzari.php') {
            $canonical_url .= '/vanzari.php';
            // Canonical doar din categorie + pagină. URL-urile de categorie din meniu (care aduc trafic)
            // rămân exact cum erau, caracter cu caracter.
            $category_urls = [
                'Autoturisme'      => 'users=Autoturisme&subcateg=3&marcat=10',
                'Autoutilitare'    => 'users=Autoutilitare&subcateg=12&marcat=100',
                'Autospecializate' => 'users=Autospecializate&subcateg=33&marcat=102',
            ];
            $get_str = function ($key) {
                return (isset($_GET[$key]) && is_string($_GET[$key])) ? trim($_GET[$key]) : '';
            };
            $users = $get_str('users');
            $canonical_params = [];
            if (isset($category_urls[$users])) {
                $canonical_params[] = $category_urls[$users];
            } elseif ($users === 'Utilaje') {
                $canonical_params[] = 'users=Utilaje';
                if (in_array($get_str('subcateg'), ['Constructii', 'Agricole', 'Municipale'], true)) {
                    $canonical_params[] = 'subcateg=' . $get_str('subcateg');
                }
            } elseif (in_array($get_str('prov_enienta'), ['Import', 'Romania'], true)) {
                $canonical_params[] = 'prov_enienta=' . $get_str('prov_enienta');
            }
            $canonical_page = isset($_GET['pageNum_vanzare']) ? (int)$_GET['pageNum_vanzare'] : 0;
            if ($canonical_page > 0) {
                $canonical_params[] = 'pageNum_vanzare=' . $canonical_page;
            }
            if (!empty($canonical_params)) {
                $canonical_url .= '?' . implode('&', $canonical_params);
            }

            // Căutări și combinații de filtre: utile vizitatorilor, dar nu le indexăm.
            // Când e setată o categorie validă, vanzari.php ignoră filtrele, deci pagina e chiar categoria.
            $has_category = isset($category_urls[$users]) || $users === 'Utilaje';
            $is_filtered = !$has_category && (
                $get_str('txtsrc') !== ''
                || !in_array($get_str('marca'), ['', '%'], true)
                || !in_array($get_str('an'), ['', '1995'], true)
                || !in_array($get_str('pret'), ['', '14000000'], true)
                || !in_array($get_str('combustibil'), ['', '%', 'fara'], true)
            );
            if (($is_filtered || isset($_GET['message'])) && !isset($robots_meta)) {
                $robots_meta = 'noindex,follow';
            }
        }    
        // Fallback: just use current page
        else {
            $canonical_url .= '/' . $basename;
        }
        ?>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- Resource hints for faster loading -->
        <link rel="dns-prefetch" href="https://www.googletagmanager.com">
        <link rel="dns-prefetch" href="https://www.google-analytics.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="dns-prefetch" href="https://www.gstatic.com">
        <link rel="dns-prefetch" href="https://fonts.googleapis.com">
        
        <!-- Preload critical above-the-fold resources -->
        <link rel="preload" href="<?php echo BASE_PATH; ?>themes/automotive/images/logo_autoland_2.png" as="image" fetchpriority="high">
        
        <!-- Preload Google Fonts for faster loading (reduce 695-698ms latency) -->
        <link rel="preload" href="https://fonts.gstatic.com/s/opensans/v15/mem8YaGs126MiZpBA-UFVZ0e.ttf" as="font" type="font/ttf" crossorigin>
        <link rel="preload" href="https://fonts.gstatic.com/s/opensans/v15/mem5YaGs126MiZpBA-UN_r8OUuhs.ttf" as="font" type="font/ttf" crossorigin>
        
        <!-- Preload FontAwesome font for faster loading -->
        <link rel="preload" href="<?php echo BASE_PATH; ?>themes/automotive/fonts/fontawesome-webfont3e6e.woff2?v=4.7.0" as="font" type="font/woff2" crossorigin>
        
        <meta name="description" content="<?php echo !empty($meta_description) ? htmlspecialchars($meta_description) : 'Autoland România - dealer auto autorizat Iași. Autoturisme, autoutilitare și autospecializate rulate, second hand și noi, import Europa. Finanțare/leasing avans 0%, garanție 12 luni.'; ?>">
        <meta name="keywords" content="<?php echo !empty($meta_keywords) ? htmlspecialchars($meta_keywords) : 'dealer auto Iași, mașini second hand, autoturisme second hand, autoutilitare, autospecializate, mașini rulate, mașini noi, import Europa, finanțare auto, leasing auto, credit auto avans 0, garanție auto, dealer auto autorizat, înmatriculări auto'; ?>">
        <meta name="author" content="Autoland România">
        
        <!-- Favicon - Autoland Logo -->
        <link rel="icon" type="image/x-icon" href="<?php echo BASE_PATH; ?>favicon.ico">
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo BASE_PATH; ?>favicon.ico">
        
        <title><?php 
            if (empty($page_title)) {
                echo 'Autoland România - Vânzări Mașini Rulate, Second Hand și Noi cu Garanție | Iași';
            } else {
                // Verifică dacă brand-ul este deja în titlu pentru a evita duplicate
                $brand_variants = ['Autoland România', 'AUTOLAND România', 'Autoland Romania', 'AUTOLAND'];
                $has_brand = false;
                foreach ($brand_variants as $brand) {
                    if (stripos($page_title, $brand) !== false) {
                        $has_brand = true;
                        break;
                    }
                }
                // Adaugă brand doar dacă nu există deja. Paginile de mașini nu îl folosesc
                // ($title_no_brand): titlul e lung, iar Google afișează oricum numele site-ului separat.
                $skip_brand = $has_brand || !empty($title_no_brand);
                echo htmlspecialchars($page_title) . ($skip_brand ? '' : ' | Autoland România');
            }
        ?></title>
        <!-- <meta name="robots" content="index,follow" /> -->
        <meta name="robots" content="<?php echo (isset($robots_meta) ? $robots_meta : 'index,follow'); ?>" />

        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="<?php echo !empty($og_type) ? $og_type : 'website'; ?>">
        <?php if (!empty($canonical_url)): ?>
        <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
        <?php endif; ?>
        <meta property="og:title" content="<?php echo !empty($page_title) ? htmlspecialchars($page_title) : 'Autoland România'; ?>">
        <meta property="og:description" content="<?php echo !empty($meta_description) ? htmlspecialchars($meta_description) : 'Dealer auto second hand Iași cu garanție'; ?>">
        <meta property="og:image" content="<?php echo !empty($og_image) ? $og_image : BASE_PATH . 'themes/automotive/images/logo_Autoland.png'; ?>">
        <meta property="og:locale" content="ro_RO">
        <meta property="og:site_name" content="Autoland România">
        
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
        <meta name="twitter:title" content="<?php echo !empty($page_title) ? htmlspecialchars($page_title) : 'Autoland România'; ?>">
        <meta name="twitter:description" content="<?php echo !empty($meta_description) ? htmlspecialchars($meta_description) : 'Dealer auto second hand Iași cu garanție'; ?>">
        <meta name="twitter:image" content="<?php echo !empty($og_image) ? $og_image : BASE_PATH . 'themes/automotive/images/logo_Autoland.png'; ?>">
        
        <?php
        // Only output canonical URL if it's not empty (articol.php and articole.php set it in $header_scripts)
        if (!empty($canonical_url)) {
            echo '<link rel="canonical" href="' . htmlspecialchars($canonical_url) . '" />' . PHP_EOL;
        }
        
        // Add structured data if available
        // $json_ld can be either:
        // 1. A string with already formatted <script> tags (from getJsonLdString())
        // 2. A JSON string that needs wrapping in <script> tags
        if (isset($json_ld) && !empty($json_ld)) {
            // Check if it already contains <script> tags
            if (strpos($json_ld, '<script') !== false) {
                // Already formatted, just echo it
                echo $json_ld;
            } else {
                // Plain JSON, wrap it
                echo '<script type="application/ld+json">' . PHP_EOL;
                echo $json_ld . PHP_EOL;
                echo '</script>' . PHP_EOL;
            }
        }
        ?>

        <!-- Add support for IE shit -->
        <!--[if lt IE 9]>
              <script src="<?php echo BASE_PATH; ?>themes/automotive/js/html5shiv.js"></script>
              <script src="<?php echo BASE_PATH; ?>themes/automotive/js/respond.min.js"></script>
              <script src="<?php echo BASE_PATH; ?>themes/automotive/js/selectivizr.js"></script>
            <![endif]-->

        <?php
        // Preload critical CSS for faster initial render
        if (DEBUG == FALSE && file_exists('public/build/rev-manifest.json')) {
            $rev_manifest_version = json_decode(file_get_contents('public/build/rev-manifest.json'), TRUE);
            ?>
            <link rel="preload" href="<?php echo BASE_PATH; ?>public/build/css/<?php echo $rev_manifest_version['stylesheet.css']; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link href="<?php echo BASE_PATH; ?>public/build/css/<?php echo $rev_manifest_version['stylesheet.css']; ?>" type="text/css" rel="stylesheet"></noscript>
            <?php
        } else {
            // Load critical CSS normally to prevent FOUC (Flash of Unstyled Content)
            // These are above-the-fold styles needed for initial render
            // Note: Keeping them blocking prevents layout shift and ensures proper rendering
            ?>
            <!-- Font display optimization override for FontAwesome - Applied immediately for faster font loading -->
            <style>
            /* Override FontAwesome @font-face to add font-display: swap - Applied before CSS loads */
            @font-face {
                font-family: 'FontAwesome';
                src: url('<?php echo BASE_PATH; ?>themes/automotive/fonts/fontawesome-webfont3e6e.eot?v=4.7.0');
                src: url('<?php echo BASE_PATH; ?>themes/automotive/fonts/fontawesome-webfontd41d.eot?#iefix&v=4.7.0') format('embedded-opentype'),
                     url('<?php echo BASE_PATH; ?>themes/automotive/fonts/fontawesome-webfont3e6e.woff2?v=4.7.0') format('woff2'),
                     url('<?php echo BASE_PATH; ?>themes/automotive/fonts/fontawesome-webfont3e6e.woff?v=4.7.0') format('woff'),
                     url('<?php echo BASE_PATH; ?>themes/automotive/fonts/fontawesome-webfont3e6e.ttf?v=4.7.0') format('truetype'),
                     url('<?php echo BASE_PATH; ?>themes/automotive/fonts/fontawesome-webfont3e6e.svg?v=4.7.0#fontawesomeregular') format('svg');
                font-weight: normal;
                font-style: normal;
                font-display: swap !important;
            }
            </style>
            
            <!-- Critical CSS - Load immediately to prevent layout shift and FOUC -->
            <!-- Optimized order: smaller/more critical files first for faster initial render -->
            <link href="<?php echo BASE_PATH; ?>themes/automotive/css/bootstrap.min.css" rel="stylesheet">
            <link href="<?php echo BASE_PATH; ?>themes/automotive/css/theme_fonts.css" type="text/css" rel="stylesheet">
            <link href="<?php echo BASE_PATH; ?>themes/automotive/css/style.css" rel="stylesheet">
            <link href="<?php echo BASE_PATH; ?>themes/automotive/css/mobile.css" rel="stylesheet">
            
            <!-- Non-critical CSS - Load asynchronously -->
            <link rel="preload" href="<?php echo BASE_PATH; ?>themes/automotive/css/font-awesome.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link href="<?php echo BASE_PATH; ?>themes/automotive/css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all"></noscript>
            
            <link rel="preload" href="<?php echo BASE_PATH; ?>themes/automotive/css/flexslider.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link rel="stylesheet" href="<?php echo BASE_PATH; ?>themes/automotive/css/flexslider.css" type="text/css" media="screen"></noscript>
            
            <link rel="preload" href="<?php echo BASE_PATH; ?>themes/automotive/css/jquery.bxslider.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link rel="stylesheet" href="<?php echo BASE_PATH; ?>themes/automotive/css/jquery.bxslider.css" type="text/css" media="screen"></noscript>
            
            <link rel="preload" href="<?php echo BASE_PATH; ?>themes/automotive/css/jquery.fancybox.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link href="<?php echo BASE_PATH; ?>themes/automotive/css/jquery.fancybox.css" rel="stylesheet"></noscript>
            
            <link rel="preload" href="<?php echo BASE_PATH; ?>themes/automotive/css/jquery.selectbox.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link href="<?php echo BASE_PATH; ?>themes/automotive/css/jquery.selectbox.css" rel="stylesheet"></noscript>
            
            <link rel="preload" href="<?php echo BASE_PATH; ?>themes/automotive/css/settings.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH; ?>themes/automotive/css/settings.css" media="screen"></noscript>
            
            <link rel="preload" href="<?php echo BASE_PATH; ?>themes/automotive/css/animate.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link href="<?php echo BASE_PATH; ?>themes/automotive/css/animate.min.css" rel="stylesheet"></noscript>
            
            <link rel="preload" href="<?php echo BASE_PATH; ?>themes/automotive/css/ts.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link href="<?php echo BASE_PATH; ?>themes/automotive/css/ts.css" type="text/css" rel="stylesheet"></noscript>
            
            <link rel="preload" href="<?php echo BASE_PATH; ?>themes/automotive/css/scustomizer.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link href="<?php echo BASE_PATH; ?>themes/automotive/css/scustomizer.css" type="text/css" rel="stylesheet"></noscript>
            
            <!-- LoadCSS polyfill for browsers that don't support preload -->
            <script>
            /*! loadCSS. [c]2017 Filament Group, Inc. MIT License */
            (function(w){"use strict";if(!w.loadCSS){w.loadCSS=function(){}}
            var loadCSS=function(href,before,media,attributes){var doc=w.document;var ss=doc.createElement("link");var ref;if(before){ref=before}else{var refs=(doc.body||doc.getElementsByTagName("head")[0]).childNodes;ref=refs[refs.length-1]}
            var sheets=doc.styleSheets;ss.rel="stylesheet";ss.href=href;if(media){ss.media="only x"}function addSheet(){ss.media=media||"all";if(attributes){for(var key in attributes){if(attributes.hasOwnProperty(key)){ss.setAttribute(key,attributes[key])}}}
            ref.parentNode.insertBefore(ss,before?ref:ref.nextSibling)}
            if(ss.addEventListener){ss.addEventListener("load",function(){ss.media=media||"all"})}
            ss.onloadcssdefined=function(cb){for(var i=0;i<sheets.length;i++){if(sheets[i].href===ss.href){return cb()}}
            setTimeout(function(){ss.onloadcssdefined(cb)})}
            if(ss.relList&&ss.relList.supports&&ss.relList.supports("preload")){ss.rel="preload";ss.as="style";ss.onloadcssdefined(addSheet);addSheet()}else{addSheet()}
            return ss}
            w.loadCSS=loadCSS})(typeof global!=="undefined"?global:this);
            </script>
            <?php
        }
        ?>
        <?php print $header_scripts; ?>
    
        <!-- Non-critical JavaScript - Load with defer -->
        <script type="text/JavaScript" src="<?php echo BASE_PATH; ?>js/selectmake.js" defer></script>

        <!-- Google Analytics (G-23845PZRLE) se încarcă din GTM (eticheta "GT – Google tag"), nu direct aici:
             încărcat de două ori ar număra fiecare vizită dublu -->

        <script>
            // Make BASE_PATH available to JavaScript
            var BASE_PATH = '<?= BASE_PATH ?>';
        </script>
    </head>

    <body class="boxed_layout margin">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N85GS6ZP"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
        <!--Header Start-->
        <header class="clearfix affix-top header-separated">
            <!-- Top Toolbar - Contact Info Section (Full Width Background) -->
            <div class="toolbar-wrapper">
                <section class="toolbar toolbar-standalone">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <ul class="company_info">
                                    <li class="contact-phone">
                                        <a href="tel:+40742377001">
                                            <i class="fa fa-phone"></i> 
                                            <span class="contact-text">+40 742 377 001</span>
                                        </a>
                                    </li>
                                    <li class="contact-email">
                                        <a href="mailto:office@autolandromania.ro">
                                            <i class="fa fa-envelope"></i> 
                                            <span class="contact-text">office@autolandromania.ro</span>
                                        </a>
                                    </li>
                                    <li class="contact-address">
                                        <a href="https://maps.app.goo.gl/cuxVc4u2BkxwVWXt6" target="_blank">
                                            <i class="fa fa-map-marker"></i> 
                                            <span class="contact-text">Strada Luca Arbore nr. 3, Iași, 700440</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- Navigation Menu Section -->
            <?php include './themes/automotive/template_parts/menu_top.php'; ?>
        </header>
        <!--Header End-->
        <section id="secondary-banner" class="dynamic-image-17"><!--for other images just change the class name of this section block like, class="dynamic-image-2" and add css for the changed class-->
            <div class="container">
                <div class="row">
                    <div class="pull-right padding-right-20">
                        <h1><?php echo !empty($banner_title) ? htmlspecialchars($banner_title) : $page_title; ?></h1>
                        <?php
                        if (!empty($page_subtitle)) {
                            echo "<h2 class='float-right'>$page_subtitle</h2>";
                        }
                        ?>
                    </div>
                    <!--                    <div class="col-lg-4 col-md-4 col-sm-6 ">
                                            <ul class="breadcrumb">
                                                <li><a href="index.php">Prima pagină</a></li>
                                                <li><?php echo $page_title; ?></li>
                                            </ul>
                                        </div>-->
                </div>
            </div>
        </section>
        <!--secondary-banner ends-->
        <div class="message-shadow"></div>
        <div class="clearfix"></div>