<?php
/**
 * Leasing page
 *
 * This site requires php version >= 7.0
 *
 * @copyright (c) 2018, Marius Ionel <webmaster@grupnet.ro>
 * @version 0.0.1
 */
// get config file;
include_once './includes/config.php';

// /////////////////////////////////////////////////////////////////////////////
// try to get content
// Set optimized page title (direct in file for easier maintenance)
$page_title = "Oferte Speciale Mașini Second Hand | Iași | Autoland România";
$page_subtitle = '';

// Același catalog ca pe vanzari.php (filtre, sortare, paginare), limitat la mașinile cu ofertă specială
require_once './includes/vanzari_catalog.php';
$vz_base_page = 'lista_oferta.php';
$vz_f = vz_filters();
$vz_f['oferta'] = 1;
$maxRows_vanzare = $vz_f['pp'];
$pageNum_vanzare = isset($_GET['pageNum_vanzare']) ? max(0, (int)$_GET['pageNum_vanzare']) : 0;
$totalRows_vanzare = vz_count($vz_f);
$totalPages_vanzare = max(0, (int)ceil($totalRows_vanzare / $maxRows_vanzare) - 1);
$pageNum_vanzare = min($pageNum_vanzare, $totalPages_vanzare);
$startRow_vanzare = $pageNum_vanzare * $maxRows_vanzare;
$vz_rows = array();
$vz_res = vz_fetch_page($vz_f, $startRow_vanzare, $maxRows_vanzare) or db_fail($connection);
while ($vz_row = mysqli_fetch_assoc($vz_res)) { $vz_rows[] = $vz_row; }
$vz_overview = false;
$vz_sections = array();

// Combinațiile de filtre și sortările nu se indexează (pagina simplă rămâne indexabilă)
if ($vz_f['sort'] !== VZ_DEFAULT_SORT || $vz_f['pp'] !== VZ_PER_PAGE || vz_active_chips($vz_f)) {
    $robots_meta = 'noindex,follow';
}
// Set SEO meta tags
require_once './includes/meta_tags.php';
setSpecialOffersPageMeta();

// Add JSON-LD structured data for special offers
require_once './includes/json_ld_schemas.php';
$breadcrumbs = [
    ['name' => 'Acasă', 'url' => BASE_PATH],
    ['name' => 'Ofertă Specială', 'url' => BASE_PATH . 'lista_oferta.php']
];

// Build ItemList for special offers
$item_list_elements = [];
$position = $startRow_vanzare + 1;
foreach ($vz_rows as $car) {
    $car_url = BASE_PATH . "vanzari_detail.php?id=" . $car['id'];
    $car_name = trim($car['marca'] . " " . $car['tip']);

    $item_list_elements[] = [
        "@type" => "ListItem",
        "position" => $position++,
        "item" => [
            "@type" => "Car",
            "@id" => $car_url,
            "name" => $car_name,
            "url" => $car_url,
            "brand" => $car['marca'],
            "model" => $car['tip'],
            "vehicleModelDate" => !empty($car['an']) ? (int)$car['an'] : null,
            "offers" => [
                "@type" => "Offer",
                "price" => (float)$car['pret'],
                "priceCurrency" => "EUR",
                "url" => $car_url,
                "availability" => "InStock",
                "priceSpecification" => [
                    "@type" => "PriceSpecification",
                    "price" => (float)$car['pret'],
                    "priceCurrency" => "EUR"
                ]
            ]
        ]
    ];
}
$special_offer_schema = [
    "@context" => "https://schema.org",
    "@type" => "ItemList",
    "name" => "Oferte Speciale Auto",
    "description" => "Oferte speciale la mașini second hand - prețuri reduse și promoții exclusive la AutoLand România",
    "numberOfItems" => count($item_list_elements),
    "itemListElement" => $item_list_elements
];

$canonical_url = BASE_PATH . 'lista_oferta.php';
$special_offers_schemas = [
    $special_offer_schema,
    getBreadcrumbSchema($breadcrumbs),
    getOrganizationSchema(),
    getWebPageSchema($canonical_url, $page_title, $meta_description ?? '')
];
$header_scripts .= getJsonLdString($special_offers_schemas);
$header_scripts .= '<link rel="stylesheet" href="' . BASE_PATH . 'themes/automotive/css/vanzari-lista.css?v=' . @filemtime(__DIR__ . '/themes/automotive/css/vanzari-lista.css') . '">' . PHP_EOL;

// /////////////////////////////////////////////////////////////////////////////
// get header template part
include './themes/automotive/template_parts/header.php';
?>

<section class="content">
    <div class="container">
        <div class="inner-page row vz-page">
            <!-- Sidebar START Here -->
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 padding-right-none sm-padding-left-none xs-padding-left-none padding-bottom-40 scroll_effect fadeInUp" data-wow-delay='.2s' style="z-index:100">
                <?php
                // formularul de căutare pornește gol: duce la lista completă, nu rămâne limitat la oferte
                $vz_f_offers = $vz_f;
                unset($vz_f);
                require_once './themes/automotive/template_parts/sidebar_left.php';
                $vz_f = $vz_f_offers;
                ?>
            </div>
            <!-- Sidebar END Here -->

            <div class="with-sidebar col-lg-9 col-md-9 col-sm-12 col-xs-12 welcome padding-left-none md-padding-left-25 padding-bottom-40 scroll_effect fadeInUp">
                <?php
                // lista de oferte speciale (același șablon ca la vanzari.php)
                require_once './themes/automotive/template_parts/inventory_listing_grid.php';
                ?>
            </div>
        </div>
    </div>
    <!--container ends-->
</section>
<!--content ends-->

<?php
// get footer template part
include './themes/automotive/template_parts/footer.php';
