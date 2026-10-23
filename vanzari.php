<?php
// get config file;
include_once './includes/config.php';

// Redirect URLs with empty txtsrc parameter to clean URLs (for Google cleanup)
// Handles multiple txtsrc parameters and removes all empty ones
$has_empty_txtsrc = false;

// Check if txtsrc exists and is empty (handles both single and array cases)
if (isset($_GET['txtsrc'])) {
    if (is_array($_GET['txtsrc'])) {
        // Multiple txtsrc parameters - check if any are empty
        foreach ($_GET['txtsrc'] as $value) {
            if ($value === '') {
                $has_empty_txtsrc = true;
                break;
            }
        }
    } else {
        // Single txtsrc parameter - check if empty
        if ($_GET['txtsrc'] === '') {
            $has_empty_txtsrc = true;
        }
    }
}

if ($has_empty_txtsrc) {
    // Build clean URL without empty txtsrc
    $clean_params = $_GET;
    
    // Remove txtsrc if it's empty (handles both single value and array)
    if (isset($clean_params['txtsrc'])) {
        if (is_array($clean_params['txtsrc'])) {
            // Remove all empty values from array
            $clean_params['txtsrc'] = array_filter($clean_params['txtsrc'], function($val) {
                return $val !== '';
            });
            // If no values left, remove the parameter completely
            if (empty($clean_params['txtsrc'])) {
                unset($clean_params['txtsrc']);
            }
        } else {
            // Single value - remove if empty
            if ($clean_params['txtsrc'] === '') {
                unset($clean_params['txtsrc']);
            }
        }
    }
    
    // Build the clean query string
    $clean_query = !empty($clean_params) ? http_build_query($clean_params) : '';
    
    // Only redirect if the query string actually changed
    if ($clean_query !== $_SERVER['QUERY_STRING']) {
        $clean_url = BASE_PATH . 'vanzari.php';
        if (!empty($clean_query)) {
            $clean_url .= '?' . $clean_query;
        }
        
        // 301 permanent redirect to tell Google the new URL is canonical
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $clean_url);
        exit;
    }
}

// Filtre, sortare și paginare: logica e în includes/vanzari_catalog.php
require_once './includes/vanzari_catalog.php';

$vz_f = vz_filters();
$maxRows_vanzare 	= $vz_f['pp'];
$pageNum_vanzare 	= (isset($_GET['pageNum_vanzare'])) ? max(0, (int)$_GET['pageNum_vanzare']) : 0;
$totalRows_vanzare 	= vz_count($vz_f);
$totalPages_vanzare 	= max(0, (int)ceil($totalRows_vanzare / $maxRows_vanzare) - 1);
// O pagină inexistentă (de exemplu după schimbarea filtrelor) duce la ultima pagină, nu la o listă goală
$pageNum_vanzare 	= min($pageNum_vanzare, $totalPages_vanzare);
$startRow_vanzare 	= $pageNum_vanzare * $maxRows_vanzare;
$vz_rows 		= array();
$vz_sections 		= array();
// Fără filtre, cu sortarea implicită, pe prima pagină: câteva mașini din fiecare categorie, cu buton spre categorie
$vz_overview = $totalRows_vanzare > 0 && $pageNum_vanzare === 0 && !vz_active_chips($vz_f)
	&& $vz_f['sort'] === VZ_DEFAULT_SORT && $vz_f['pp'] === VZ_PER_PAGE;
if ($vz_overview) {
	foreach (vz_categories() as $vz_cat) {
		$vz_res = vz_fetch_page(array_merge($vz_f, array('users' => $vz_cat)), 0, VZ_OVERVIEW_PER_CATEGORY) or db_fail($connection);
		while ($vz_row = mysqli_fetch_assoc($vz_res)) { $vz_sections[$vz_cat][] = $vz_row; $vz_rows[] = $vz_row; }
	}
	$vz_overview = count($vz_rows) > 0;
}
if (!$vz_overview) {
	$vanzare 		= vz_fetch_page($vz_f, $startRow_vanzare, $maxRows_vanzare) or db_fail($connection);
	while ($vz_row = mysqli_fetch_assoc($vanzare)) { $vz_rows[] = $vz_row; }
}
$textt 			= $vz_f['txtsrc'];

      
$header_currentOption = 'vauto';

// added by Gabriela Brezeanu
// Check if user was redirected from an expired car listing
$expired_message = '';
if (isset($_GET['message']) && $_GET['message'] == 'expired') {
    $expired_message = '<div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>Anunțul nu mai este disponibil!</strong> Mașina pe care o căutați a fost deja vândută sau anunțul a expirat.
    </div>';
}

// Set SEO meta tags
require_once './includes/meta_tags.php';
$marca_filter = isset($_GET['users']) && !empty($_GET['users']) ? urldecode($_GET['users']) : null;
setListingsPageMeta($marca_filter);

// Set optimized page title based on category or origin filter (direct in file for easier maintenance)
$page_subtitle = '';

// Get filters
$category = $vz_f['users'] !== '' ? $vz_f['users'] : null;
$origin = $vz_f['prov_enienta'] !== '' ? $vz_f['prov_enienta'] : null;

// Build optimized title
$title_parts = [];
if ($category) {
    $title_parts[] = $category;
} else {
    $title_parts[] = "Autovehicule";
}

if ($origin && $origin != 'fara') {
    if ($origin == 'Romania') {
        $title_parts[] = "din România";
    } elseif ($origin == 'Import') {
        $title_parts[] = "din Import";
    } else {
        $title_parts[] = htmlspecialchars($origin);
    }
}

// Paginile 2+ au titlu propriu (altfel toate paginile unei categorii au același titlu)
if ($pageNum_vanzare > 0) {
    $title_parts[count($title_parts) - 1] .= " – Pagina " . ($pageNum_vanzare + 1);
}

$title_parts[] = "second hand în rate, livrare în toată țara | Autoland România";

$page_title = implode(" | ", $title_parts);

// Add JSON-LD structured data for car listings: doar ItemList (URL-urile mașinilor) + BreadcrumbList.
// Schema Product completă rămâne doar pe paginile de mașini (vanzari_detail.php).
require_once './includes/json_ld_schemas.php';

$item_list_elements = [];
$position = $startRow_vanzare + 1;

foreach ($vz_rows as $car) {
    $item_list_elements[] = [
        "@type" => "ListItem",
        "position" => $position++,
        "name" => trim($car['marca'] . " " . $car['tip']),
        "url" => BASE_PATH . "vanzari_detail.php?id=" . (int)$car['id']
    ];
}

// Create the ItemList schema
$item_list_schema = [
    "@context" => "https://schema.org",
    "@type" => "ItemList",
    "name" => "Stoc Auto Second Hand",
    "description" => "Lista completă de autovehicule second hand disponibile la Autoland România",
    "numberOfItems" => count($item_list_elements),
    "itemListElement" => $item_list_elements
];

// Add breadcrumbs
$breadcrumb_name = 'Stoc Auto';
if (isset($_GET['marca']) && !empty($_GET['marca'])) {
    $breadcrumb_name = 'Stoc ' . urldecode($_GET['marca']);
}
$breadcrumbs = [
    ['name' => 'Acasă', 'url' => BASE_PATH],
    ['name' => $breadcrumb_name, 'url' => BASE_PATH . 'vanzari.php']
];

$canonical_url = BASE_PATH . 'vanzari.php';
// Combine all schemas: ItemList + Breadcrumb + Organization + WebPage
$vanzari_schemas = array_merge(
    [$item_list_schema],           // ItemList for all cars
    [
        getBreadcrumbSchema($breadcrumbs),
        getOrganizationSchema(),
        getWebPageSchema($canonical_url, $page_title, $meta_description ?? '')
    ]
);
$header_scripts .= getJsonLdString($vanzari_schemas);
$header_scripts .= '<link rel="stylesheet" href="' . BASE_PATH . 'themes/automotive/css/vanzari-lista.css?v=' . @filemtime(__DIR__ . '/themes/automotive/css/vanzari-lista.css') . '">' . PHP_EOL;


include './themes/automotive/template_parts/header.php';

?>

<section class="content">
    <div class="container">
        <?php if (!empty($expired_message)) { echo $expired_message; } ?>
        <div class="inner-page row vz-page">
            <!-- Sidebar START Here -->
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 padding-right-none sm-padding-left-none xs-padding-left-none padding-bottom-40 scroll_effect fadeInUp" data-wow-delay='.2s' style="z-index:100">
                <?php
                require_once './themes/automotive/template_parts/sidebar_left.php';
                ?>
            </div>
            <!-- Sidebar END Here -->


            <div class="with-sidebar col-lg-9 col-md-9 col-sm-12 col-xs-12 welcome padding-left-none md-padding-left-25 padding-bottom-40 scroll_effect fadeInUp">
                <?php
                // get car listing (all or just a part)
                require_once './themes/automotive/template_parts/inventory_listing_grid.php';
                ?>
            </div>
            <!--welcome ends-->
        </div>
    </div>

</section>

<?php
include './themes/automotive/template_parts/footer.php';