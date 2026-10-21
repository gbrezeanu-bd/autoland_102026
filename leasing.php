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
include_once './includes/pagini_sectiuni.php';

// Bara laterală a secțiunii Finanțare (pagina principală, fără slug)
$sectiune = 'finantare';
$slug = '';

// /////////////////////////////////////////////////////////////////////////////
// try to get content
$query_text = "SELECT id, leasing FROM texte WHERE id = 1";

$result = $connection->query($query_text) or db_fail($connection);
$row = $result->fetch_assoc();

$page_title = "Leasing auto second hand, avans de la 0% | Autoland România";
$banner_title = "Leasing și rate pentru mașini second hand, pentru persoane fizice și firme";
$page_subtitle = '';
$content = is_null($row) ? "Content is missing" : pagini_linkuri_interne($row['leasing']);

// Set SEO meta tags
require_once './includes/meta_tags.php';
setLeasingPageMeta();

// Add JSON-LD structured data for SEO
require_once './includes/json_ld_schemas.php';
$breadcrumbs = [
    ['name' => 'Acasă', 'url' => BASE_PATH],
    ['name' => 'Servicii', 'url' => BASE_PATH . 'leasing.php'],
    ['name' => 'Leasing / Rate', 'url' => BASE_PATH . 'leasing.php']
];
$canonical_url = BASE_PATH . 'leasing.php';
$service_schemas = [
    getServiceSchema(
        "Finanțare Auto",
        "Leasing și Credit Auto",
        "Servicii complete de finanțare auto: leasing operațional și financiar, credit auto cu avans zero, rate flexibile. Soluții personalizate pentru achiziția de mașini second hand și autoutilitare.",
        "Iași",
        [
            "offers" => [
                "@type" => "Offer",
                "name" => "Leasing auto",
                "description" => "Leasing operațional și financiar pentru autoturisme și autoutilitare"
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
