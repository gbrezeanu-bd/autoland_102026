<?php

/**
 * Garantie page
 * 
 * This site requires php version >= 7.0
 * 
 * @copyright (c) 2018, Marius Ionel <webmaster@grupnet.ro>
 * @version 0.0.1
 */
// get config file;
include_once './includes/config.php';
include_once './includes/pagini_sectiuni.php';

// Aspectul cu meniu lateral al secțiunii Garanție și servicii
$sectiune = 'servicii';
$slug = 'garantie';

// /////////////////////////////////////////////////////////////////////////////
// try to get content
$query_text = "SELECT id, garantie FROM texte WHERE id = 1";

$result = $connection->query($query_text) or db_fail($connection);
$row = $result->fetch_assoc();

$page_title = "Garanție auto 1–3 ani, în orice service RAR | Autoland";
$banner_title = "Garanția Autoland România: ce acoperă, cât ține și cât costă";
$page_subtitle = "";
$content = is_null($row) ? "Content is missing" : pagini_linkuri_interne($row['garantie']);

// Set SEO meta tags
require_once './includes/meta_tags.php';
setWarrantyPageMeta();

// Add JSON-LD structured data for SEO
require_once './includes/json_ld_schemas.php';
$breadcrumbs = [
    ['name' => 'Acasă', 'url' => BASE_PATH],
    ['name' => 'Servicii', 'url' => BASE_PATH . 'garantie.php'],
    ['name' => 'Garanție Auto', 'url' => BASE_PATH . 'garantie.php']
];
$canonical_url = BASE_PATH . 'garantie.php';
$service_schemas = [
    getServiceSchema(
        "Garanție Auto",
        "Garanție Auto Second Hand",
        "Garanție extinsă de 12 luni în scris pentru toate mașinile second hand. Acoperire completă, transparență totală și siguranță la achiziția vehiculului dumneavoastră.",
        "Iași",
        [
            "warranty" => [
                "@type" => "WarrantyPromise",
                "durationOfWarranty" => "P12M",
                "warrantyScope" => "Garanție extinsă pentru mașini second hand"
            ]
        ]
    ),
    getBreadcrumbSchema($breadcrumbs),
    getOrganizationSchema(),
    getWebPageSchema($canonical_url, $page_title, $meta_description ?? '')
];
$header_scripts .= '<link rel="stylesheet" href="' . BASE_PATH . 'themes/automotive/css/pagini.css">' . "\n";
$header_scripts .= getJsonLdString($service_schemas);

// /////////////////////////////////////////////////////////////////////////////
// get header template part
include './themes/automotive/template_parts/header.php';
?>

<section class="content">
    <div class="container">
        <div class="inner-page">
            <div class="ap-layout">
                <?php include './themes/automotive/template_parts/pagina_sidebar.php'; ?>
                <div class="ap-main">
                    <?php print $content; ?>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>
    <!--container ends--> 
</section>
<!--content ends-->

<?php

// get footer template part
include './themes/automotive/template_parts/footer.php';
