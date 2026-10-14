<?php
// get config file;
include_once './includes/config.php';

// /////////////////////////////////////////////////////////////////////////////
// try to get content
$query_text = "SELECT id, home FROM texte WHERE id = 1";

$result = $connection->query($query_text) or die($connection->error);
$row = $result->fetch_assoc();

// $page_title = "Încă din 2007: experiența noastră - garanția mașinii tale de vis.";
// $page_subtitle = 'Mașini atent verificate, garanție 12 luni cu suport complet la fiecare pas.';
$page_title = "Vânzări Mașini Rulate, Second Hand și Noi cu Garanție | Iași";
$page_subtitle = 'Dealer (Parc) auto de încredere începând din 2007';
$content = is_null($row) ? "Content is missing" : $row['home'];


$query_marci     = "SELECT * FROM `search` ORDER BY categorie, marca ASC";
$marci           = $connection->query($query_marci) or die($connection->error);
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
$header_scripts .= '<script type="application/ld+json">' . json_encode($json_ld_homepage[0], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
$header_scripts .= '<script type="application/ld+json">' . json_encode($json_ld_homepage[1], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';


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
