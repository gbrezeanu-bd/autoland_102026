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

$query = 'SELECT * FROM masini WHERE stare_anunt LIKE 1 AND oferta_speciala LIKE 1 ORDER BY pret';
$pager = paginateQuery($connection, $query, NULL, 'pagination');
//$row_vanzare=$res['pager_resultset'];
$totalRows_vanzare=$pager['pager_resultset']->num_rows;

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
$position = 1;
if ($pager['pager_resultset'] && $pager['pager_resultset']->num_rows > 0) {
    mysqli_data_seek($pager['pager_resultset'], 0);
    while ($car = $pager['pager_resultset']->fetch_assoc()) {
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
    mysqli_data_seek($pager['pager_resultset'], 0);
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

// /////////////////////////////////////////////////////////////////////////////
// get header template part
include './themes/automotive/template_parts/header.php';
?>

<section class="content">
    <div class="container">
        <div class="inner-page row">
            <?php
            // get car listing (special offer)
            require_once './themes/automotive/template_parts/inventory_listing.php';
            ?>

            <div class="clearfix"></div>
        </div>
    </div>
    <!--container ends-->
</section>
<!--content ends-->

<?php
// get footer template part
include './themes/automotive/template_parts/footer.php';
