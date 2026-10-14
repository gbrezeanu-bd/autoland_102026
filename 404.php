<?php
/**
 * Pagină 404 - Pagină Inexistentă
 */

// Get config file
include_once './includes/config.php';

// 404 implicit; vanzari_detail.php setează 410 pentru mașinile șterse
$http_status = isset($http_status) ? (int)$http_status : 404;
http_response_code($http_status);

$page_title = "Pagină Inexistentă (404) | AUTOLAND Iași";
$page_subtitle = 'Dealer auto expert în mașini rulate verificate tehnic';
$meta_description = "Pagina pe care o căutați nu a fost găsită pe site-ul AUTOLAND Iași. Vă rugăm să reveniți la pagina principală sau să contactați echipa noastră.";
$meta_keywords = "404, pagină inexistentă, autoland iași";
$robots_meta = 'noindex,follow';

// Set base URL for links
$base_url = defined('BASE_PATH') ? BASE_PATH : './';

// Include meta tags pentru SEO
require_once './includes/meta_tags.php';

// Include header-ul site-ului
include './themes/automotive/template_parts/header.php';
?>

<section class="content">
    <div class="container">
        <div class="inner-page row">
            <!-- Sidebar START Here -->
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 padding-right-none sm-padding-left-none xs-padding-left-none padding-bottom-40 scroll_effect fadeInUp" data-wow-delay='.2s' style="z-index:100">
                <?php
                require_once './themes/automotive/template_parts/sidebar_left.php';
                ?>
            </div>
            <!-- Sidebar END Here -->

            <div class="with-sidebar col-lg-9 col-md-9 col-sm-12 col-xs-12 welcome padding-left-none md-padding-left-25 padding-bottom-40 scroll_effect fadeInUp information_head welcome-wrap">
                <div class="content-wrap car_listings">
                    
                    <div style="text-align: center; padding: 60px 20px;">
                        <h1 style="font-size: 120px; font-weight: bold; color: #d32f2f; margin: 0;">404</h1>
                        <h2 style="font-size: 32px; margin: 20px 0;">Pagină Inexistentă</h2>
                        <p style="font-size: 18px; color: #666; margin: 30px 0;">
                            Ne pare rău, dar pagina pe care o căutați nu există sau a fost mutată.
                        </p>
                        
                        <div style="margin: 40px 0;">
                            <a href="<?php echo BASE_PATH; ?>" style="display: inline-block; padding: 15px 40px; background: #d32f2f; color: white; text-decoration: none; border-radius: 5px; font-size: 18px; font-weight: bold;">
                                🏠 Înapoi la Homepage
                            </a>
                        </div>
                        
                        <div style="margin-top: 50px; padding-top: 30px; border-top: 1px solid #e0e0e0;">
                            <h3 style="margin-bottom: 20px;">Linkuri Utile:</h3>
                            <ul style="list-style: none; padding: 0;">
                                <li style="margin: 10px 0;"><a href="<?php echo BASE_PATH; ?>lista_oferta.php" style="color: #d32f2f; text-decoration: none; font-size: 16px;">📋 Vezi Ofertele Noastre</a></li>
                                <li style="margin: 10px 0;"><a href="<?php echo BASE_PATH; ?>articole/ghid-masini-second-hand" style="color: #d32f2f; text-decoration: none; font-size: 16px;">📖 Ghid Mașini Second Hand</a></li>
                                <li style="margin: 10px 0;"><a href="<?php echo BASE_PATH; ?>articole/ghid-complet-leasing-finantare-auto-2026" style="color: #d32f2f; text-decoration: none; font-size: 16px;">💶 Ghid Leasing și Finanțare Auto</a></li>
                                <li style="margin: 10px 0;"><a href="<?php echo BASE_PATH; ?>contact.php" style="color: #d32f2f; text-decoration: none; font-size: 16px;">📞 Contactează-ne</a></li>
                            </ul>
                        </div>
                    </div>
                    
                </div>
            </div>
            <!--welcome ends-->
        </div>
    </div>
</section>

<?php
// Include footer-ul site-ului
include './themes/automotive/template_parts/footer.php';
?>

