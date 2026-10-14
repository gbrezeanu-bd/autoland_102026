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

$colname_selector_marca = "-1";
if (isset($_GET['marca']) && !empty($_GET['marca'])) 
{
  // Properly handle URL-encoded values and special characters
  $colname_selector_marca = urldecode($_GET['marca']);
  // Now escape the value for SQL
  $colname_selector_marca = $connection->real_escape_string($colname_selector_marca);
  
  // Now using marca directly as a string with quotes for SQL
  $query_selector_marca = "SELECT * FROM `masini` WHERE marca LIKE '" . $colname_selector_marca . "'";
  $selector_marca = $connection->query($query_selector_marca) or db_fail($connection);
  $row_selector_marca = mysqli_fetch_assoc($selector_marca);
  $totalRows_selector_marca = mysqli_num_rows($selector_marca);
}
/* 
   All references to categories and subcategories have been removed 
*/

// Subcategory logic has been removed


$maxRows_vanzare 		= 10;
$pageNum_vanzare 		= (isset($_GET['pageNum_vanzare'])) ? max(0, (int)$_GET['pageNum_vanzare']) : 0;
$startRow_vanzare 		= $pageNum_vanzare * $maxRows_vanzare;


// Category logic has been removed
// an și pret intră în SQL fără ghilimele, deci trebuie să fie numere
$col_an_vanzare = 1995;
if (isset($_GET['an']) && (int)$_GET['an'] > 0) {
  $col_an_vanzare = (int)$_GET['an'];
}
$col_pret_vanzare = 14000000;
if (isset($_GET['pret']) && (int)$_GET['pret'] > 0) {
  $col_pret_vanzare = (int)$_GET['pret'];
}
$col_marca_vanzare = "%";
if (isset($_GET['marca']) && !empty($_GET['marca'])) {
  // Using exact marca string from the form with proper handling of special characters
  $col_marca_vanzare = urldecode($_GET['marca']);
  $col_marca_vanzare = $connection->real_escape_string($col_marca_vanzare);
} else if (isset($marca_auto)) {
  $col_marca_vanzare = $connection->real_escape_string($marca_auto);
}
// Subcategory variable has been removed
$col_combustibil_vanzare = "%";
if (isset($_GET['combustibil']) && !empty($_GET['combustibil'])) {
  $col_combustibil_vanzare = $connection->real_escape_string($_GET['combustibil']);
}             
$col_prov_enienta_vanzare = "%";
if (isset($_GET['prov_enienta']) && !empty($_GET['prov_enienta'])) {
	$col_prov_enienta_vanzare = $connection->real_escape_string($_GET['prov_enienta']);
}

// Handle users parameter for specific category filtering
$users_filter = null;
$subcateg_filter = null;
if (isset($_GET['users']) && !empty($_GET['users'])) {
	$users_input = trim($_GET['users']);
	// Validate input - only allow specific values (whitelist approach)
	$allowed_categories = array('Autoturisme', 'Autospecializate', 'Autoutilitare', 'Utilaje');
	if (in_array($users_input, $allowed_categories, true)) {
		$users_filter = $connection->real_escape_string($users_input);
		
		// For Utilaje, also check for subcateg filter (Constructii, Agricole, Municipale)
		if ($users_filter === 'Utilaje' && isset($_GET['subcateg']) && !empty($_GET['subcateg'])) {
			$subcateg_input = trim($_GET['subcateg']);
			$allowed_subcateg = array('Constructii', 'Agricole', 'Municipale');
			if (in_array($subcateg_input, $allowed_subcateg, true)) {
				$subcateg_filter = $connection->real_escape_string($subcateg_input);
			}
		}
	}
}

// Initialize textt variable (used in pagination links)
$textt = '';

// Check if we need to use the users filter (highest priority)
if ($users_filter !== null) {
    if ($users_filter === 'Utilaje' && $subcateg_filter !== null) {
		// Filter by category and subcateg for Utilaje
		$query_vanzare = sprintf("SELECT * FROM masini WHERE stare_anunt = 1 AND cat = '%s' AND subcateg = '%s' ORDER BY pret ASC", $users_filter, $subcateg_filter);
	} else {
		// Filter by category only
		$query_vanzare = sprintf("SELECT * FROM masini WHERE stare_anunt = 1 AND cat = '%s' ORDER BY pret ASC", $users_filter);
	}
}
elseif(isset($_GET['txtsrc']) && is_string($_GET['txtsrc']) && $_GET['txtsrc'] !== ''){
	if (!strcmp("#S10", substr($_GET['txtsrc'], 0, 4)))
		{
			$textt = (int)substr($_GET['txtsrc'], 4, 10);
			$query_vanzare 	= 'SELECT * FROM masini WHERE stare_anunt LIKE 1 AND id = '.$textt.' ORDER BY pret';
		}
	else
	{
	$textt = $_GET['txtsrc'];
	$words = explode(' ',$textt);
	$words = array_slice($words, 0, 2);
	$fields = array('marca', 'tip');
	$whereText = array();
	foreach ($words as $word) {
		$word = $connection->real_escape_string($word);
		$whereSubText = array();
		foreach ($fields as $field) {
			$whereSubText[] = ' ('.$field.' LIKE "%'.$word.'%") ';
		}
		$whereText[] = '('. join(' OR ', $whereSubText) .')';
	}
	$whereText = '('. join(' AND ',$whereText) .')';
    
	if (!strcmp("fara", $col_prov_enienta_vanzare))
		{ $whereprov_enienta = ""; }
	else
		{ 
		$whereprov_enienta = $col_prov_enienta_vanzare == '%' ?  '':' (prov_enienta=\''.$col_prov_enienta_vanzare.'\') AND '; 
		}
	$query_vanzare 	= 'SELECT * FROM masini WHERE stare_anunt LIKE 1 AND '.$whereprov_enienta.' '.$whereText.' ORDER BY pret';
	}
}
else
{
	// Check if we have an exact marca match or need to use LIKE with wildcard
	if ($col_marca_vanzare == "%") {
		$marca_condition = "marca LIKE '%'";
	} else {
		$marca_condition = "marca LIKE '" . $col_marca_vanzare . "'";
	}
	
	if ((!strcmp("fara", $col_combustibil_vanzare)) && (!strcmp("fara",$col_prov_enienta_vanzare)))
	{ $query_vanzare = "SELECT * FROM masini WHERE stare_anunt LIKE 1 AND an>=" . $col_an_vanzare . " AND pret<=" . $col_pret_vanzare . " AND " . $marca_condition . " ORDER BY pret ASC"; }
	elseif (!strcmp("fara", $col_combustibil_vanzare))
	{ $query_vanzare = "SELECT * FROM masini WHERE stare_anunt LIKE 1 AND an>=" . $col_an_vanzare . " AND pret<=" . $col_pret_vanzare . " AND " . $marca_condition . " AND prov_enienta LIKE '" . $col_prov_enienta_vanzare . "' ORDER BY pret ASC"; }
	elseif (!strcmp("fara",$col_prov_enienta_vanzare))
	{ $query_vanzare = "SELECT * FROM masini WHERE stare_anunt LIKE 1 AND an>=" . $col_an_vanzare . " AND pret<=" . $col_pret_vanzare . " AND combustibil LIKE '" . $col_combustibil_vanzare . "' AND " . $marca_condition . " ORDER BY pret ASC"; }
	else
	{ $query_vanzare = "SELECT * FROM masini WHERE stare_anunt LIKE 1 AND an>=" . $col_an_vanzare . " AND pret<=" . $col_pret_vanzare . " AND combustibil LIKE '" . $col_combustibil_vanzare . "' AND " . $marca_condition . " AND prov_enienta LIKE '" . $col_prov_enienta_vanzare . "' ORDER BY pret ASC"; }
}

$query_limit_vanzare 	= $query_vanzare . " LIMIT " . $startRow_vanzare . ", " . $maxRows_vanzare;
$vanzare 		= $connection->query($query_limit_vanzare) or db_fail($connection);
$row_vanzare 		= mysqli_fetch_assoc($vanzare);

// totalRows_vanzare din URL nu mai e folosit: se calculează mereu.
// COUNT(*) cu aceleași condiții, ca să nu aducem toate rândurile doar pentru a le număra.
$count_query_vanzare = preg_replace(array('/^SELECT \* FROM/', '/ ORDER BY .*$/'), array('SELECT COUNT(*) FROM', ''), $query_vanzare);
if (strpos($count_query_vanzare, 'SELECT COUNT(*) FROM') === 0) {
    $count_vanzare = $connection->query($count_query_vanzare);
    $totalRows_vanzare = $count_vanzare ? (int)$count_vanzare->fetch_row()[0] : 0;
} else {
    // Interogare cu altă formă: numărăm rândurile ca înainte
    $all_vanzare = $connection->query($query_vanzare);
    $totalRows_vanzare = $all_vanzare ? mysqli_num_rows($all_vanzare) : 0;
}
$totalPages_vanzare = ceil($totalRows_vanzare/$maxRows_vanzare)-1;

$queryString_vanzare = "";
if (!empty($_SERVER['QUERY_STRING'])) 
{
  $params 	= explode("&", $_SERVER['QUERY_STRING']);
  $newParams 	= array();
  $seenParams = array(); // Track which parameters we've already seen

  foreach ($params as $param) 
  {
    // Skip pagination parameters
    if (stristr($param, "pageNum_vanzare") == false && stristr($param, "totalRows_vanzare") == false) 
    {
      // Parse parameter name and value
      $paramParts = explode("=", $param, 2);
      $paramName = $paramParts[0];
      $paramValue = isset($paramParts[1]) ? $paramParts[1] : '';
      
      // Skip empty parameters and duplicates
      if (!empty($paramValue) && !isset($seenParams[$paramName])) 
      {
        array_push($newParams, $param);
        $seenParams[$paramName] = true; // Mark this parameter as seen
      }
    }
  }
  if (count($newParams) != 0) 
  {
    $queryString_vanzare = "&amp;" . htmlentities(implode("&", $newParams));
  }
}
// totalRows_vanzare nu mai e transmis în URL (crea duplicate pentru Google)
      
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
$category = isset($_GET['users']) && !empty($_GET['users']) ? $_GET['users'] : null;
$origin = isset($_GET['prov_enienta']) && !empty($_GET['prov_enienta']) && $_GET['prov_enienta'] != 'fara' ? $_GET['prov_enienta'] : null;

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

$title_parts[] = "Second Hand Iași";
$title_parts[] = "Autoland România";

$page_title = implode(" | ", $title_parts);

// Add JSON-LD structured data for car listings: doar ItemList (URL-urile mașinilor) + BreadcrumbList.
// Schema Product completă rămâne doar pe paginile de mașini (vanzari_detail.php).
require_once './includes/json_ld_schemas.php';

$item_list_elements = [];
$position = $startRow_vanzare + 1;

if ($totalRows_vanzare > 0) {
    // Execute the same query again specifically for JSON-LD
    $vanzare_jsonld = $connection->query($query_limit_vanzare);
    if ($vanzare_jsonld) {
        while ($car = $vanzare_jsonld->fetch_assoc()) {
            $item_list_elements[] = [
                "@type" => "ListItem",
                "position" => $position++,
                "name" => trim($car['marca'] . " " . $car['tip']),
                "url" => BASE_PATH . "vanzari_detail.php?id=" . (int)$car['id']
            ];
        }
        mysqli_free_result($vanzare_jsonld);
    }
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


include './themes/automotive/template_parts/header.php';

?>

<section class="content">
    <div class="container">
        <?php if (!empty($expired_message)) { echo $expired_message; } ?>
        <div class="inner-page row">
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
                require_once './themes/automotive/template_parts/inventory_listing2.php';
                ?>
            </div>
            <!--welcome ends-->
        </div>
    </div>

</section>

<?php
include './themes/automotive/template_parts/footer.php';