<?php

/**
 * Contact page
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
$slug = 'asistenta-rutiera';

// /////////////////////////////////////////////////////////////////////////////
// try to get content
$query_text = "SELECT id, asistenta_rutiera FROM texte WHERE id = 1";

$result = $connection->query($query_text) or db_fail($connection);
$row = $result->fetch_assoc();

$page_title = "Asistență rutieră 24/7, de la 45 EUR + TVA | Autoland";
$banner_title = "Asistență rutieră Autoland România: pachete anuale pentru România și UE";
$page_subtitle = '';
//$page_menu = "";
$content = is_null($row) ? "Content is missing" : pagini_linkuri_interne($row['asistenta_rutiera']);

// Set SEO meta tags
require_once './includes/meta_tags.php';
setRoadsideAssistancePageMeta();

// Add JSON-LD structured data for SEO
require_once './includes/json_ld_schemas.php';
$breadcrumbs = [
    ['name' => 'Acasă', 'url' => BASE_PATH],
    ['name' => 'Servicii', 'url' => BASE_PATH . 'asistenta-rutiera.php'],
    ['name' => 'Asistență Rutieră', 'url' => BASE_PATH . 'asistenta-rutiera.php']
];
$canonical_url = BASE_PATH . 'asistenta-rutiera.php';
$service_schemas = [
    getServiceSchema(
        "Asistență Rutieră",
        "Servicii de Asistență Rutieră",
        "Servicii complete de asistență rutieră 24/7: tractare auto, depanare, ajutor tehnic de urgență. Intervenții rapide în caz de pană, accident sau orice problemă tehnică pe drum.",
        "Iași",
        [
            "hoursAvailable" => [
                "@type" => "OpeningHoursSpecification",
                "opens" => "00:00",
                "closes" => "23:59"
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
        </div>
    </div>
</section>
<!--content ends-->

<?php

// get footer template part
include './themes/automotive/template_parts/footer.php';
