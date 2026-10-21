<?php
// get config file;
include_once './includes/config.php';
include_once './includes/pagini_sectiuni.php';
include_once './includes/home_blocks.php';

// /////////////////////////////////////////////////////////////////////////////
// try to get content
$query_text = "SELECT id, home FROM texte WHERE id = 1";

$result = $connection->query($query_text) or db_fail($connection);
$row = $result->fetch_assoc();

// $page_title = "Încă din 2007: experiența noastră - garanția mașinii tale de vis.";
// $page_subtitle = 'Mașini atent verificate, garanție 12 luni cu suport complet la fiecare pas.';
$page_title = "Mașini second hand verificate, Iași | Autoland România";
$banner_title = "Mașini second hand verificate, cu garanție, livrate din Iași în toată România";
$page_subtitle = '';
// Marcajele {NR_MASINI}, {ULTIMELE_MASINI}, {RECENZII} din text se înlocuiesc cu date actuale (includes/home_blocks.php)
$content = is_null($row) ? "Content is missing" : home_inlocuieste_marcaje(pagini_linkuri_interne($row['home']), $connection);


$query_marci     = "SELECT * FROM `search` ORDER BY categorie, marca ASC";
$marci           = $connection->query($query_marci) or db_fail($connection);
$row_marci       = mysqli_fetch_assoc($marci);
$totalRows_marci = mysqli_num_rows($marci);

// Set base URL for links
$base_url = defined('BASE_PATH') ? BASE_PATH : './';

// Set SEO meta tags
require_once './includes/meta_tags.php';
setHomePageMeta();

// Add JSON-LD structured data for SEO
require_once './includes/json_ld_schemas.php';
$json_ld_homepage = [
    getOrganizationSchema(),
    getWebSiteSchema()
];
$header_scripts .= '<link rel="stylesheet" href="' . BASE_PATH . 'themes/automotive/css/pagini.css">' . "\n";
$header_scripts .= '<script type="application/ld+json">' . json_encode($json_ld_homepage[0], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
$header_scripts .= '<script type="application/ld+json">' . json_encode($json_ld_homepage[1], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
$faq_items = pagini_extrage_faq($content);
if ($faq_items) {
    $header_scripts .= '<script type="application/ld+json">' . json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq_items], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}


include './themes/automotive/template_parts/header.php';
$footer_scripts .= '';

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
                    <?php
                    print $content;
                    ?>
                </div>
            </div>
            <!--welcome ends-->
        </div>
    </div>

</section>

<?php
include './themes/automotive/template_parts/footer.php';
