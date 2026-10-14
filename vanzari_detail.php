<?php
/**
 * Inmatriculari page
 *
 * This site requires php version >= 7.0
 *
 * @copyright (c) 2018, Marius Ionel <webmaster@grupnet.ro>
 * @version 0.0.1
 */
// get config file;
include_once './includes/config.php';

// Flag to indicate this is a car detail page (for conditional script loading)
define('IS_CAR_DETAIL_PAGE', true);


// /////////////////////////////////////////////////////////////////////////////
// try to get content


if (!isset($_SESSION)) {
    session_start();
}


//$messs = (isset($_SESSION['success_message'])) ? '<script language="JavaScript">alert("' . $_SESSION['success_message'] . '");</script>' : '';
//unset($_SESSION['success_message']);

if (isset($_POST['vizUrl'])) {
    echo '<pre>' . var_export($_POST, true) . '</pre>';
    die();
}

$maxRows_vanzare = 20;
$pageNum_vanzare = 0;
if (isset($_GET['pageNum_vanzare'])) {
    $pageNum_vanzare = mysqli_real_escape_string($connection, $_GET['pageNum_vanzare']);
}
$startRow_vanzare = $pageNum_vanzare * $maxRows_vanzare;

$colname_vanzare = "-1";
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    $http_status = 404;
    include './404.php';
    exit();
}

$colname_vanzare = $id;

$query_vanzare = sprintf("SELECT * FROM masini WHERE id = %d", $colname_vanzare);
$query_limit_vanzare = sprintf("%s LIMIT %d, %d", $query_vanzare, $startRow_vanzare, $maxRows_vanzare);
//var_dump($query_limit_vanzare);exit;

$vanzare = $connection->query($query_limit_vanzare) or die($connection->error);
//var_dump($vanzare);exit;
if (mysqli_num_rows($vanzare) == 0) {
    // Mașina nu mai există: 410 Gone, ca Google s-o scoată repede din index
    error_log("Missing car ID requested: " . $id . " from " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'direct'));
    $http_status = 410;
    include './404.php';
    exit();
}
$row_vanzare = $vanzare->fetch_assoc();

// Mașină dezactivată / vândută: pagina rămâne pentru vizitatori, dar nu se indexează
$is_sold = ((string)$row_vanzare['stare_anunt'] !== '1');
if ($is_sold) {
    $robots_meta = 'noindex,follow';
}

// Try to get additional data from for_csv table (if exists)
$row_for_csv = null;
$query_for_csv = sprintf("SELECT * FROM for_csv WHERE id = %d LIMIT 1", $row_vanzare['id']);
$result_for_csv = $connection->query($query_for_csv);
if ($result_for_csv && $result_for_csv->num_rows > 0) {
    $row_for_csv = $result_for_csv->fetch_assoc();
}

if (isset($_GET['totalRows_vanzare'])) {
    $totalRows_vanzare = mysqli_real_escape_string($connection, $_GET['totalRows_vanzare']);
} else {
    $all_vanzare = $connection->query($query_vanzare);
    $totalRows_vanzare = mysqli_num_rows($all_vanzare);
}
$totalPages_vanzare = ceil($totalRows_vanzare / $maxRows_vanzare) - 1;

if ($row_vanzare['prov_enienta'] == 'Romania') {
    $header_currentOption = 'vautor';
} else {
    $header_currentOption = 'vauto';
}

// get images associated with this car
$query_assoc = sprintf("SELECT * FROM fotov WHERE oferta = %d ORDER BY nota ASC", $row_vanzare['id']);
$assoc = $connection->query($query_assoc) or die($connection->error);
$totalRows_assoc = mysqli_num_rows($assoc);


// get an array of pictures associated with the curent car
// to avoid double fetch the pictures
$car_pictures = getCarPictures($row_vanzare['id'], $connection);

// get an array of videos associated with the current car
$car_videos = getCarVideos($row_vanzare['id'], $connection);


// get the next and previous id of published cars
$prev_query = sprintf("SELECT id FROM `masini` WHERE id<%d AND `stare_anunt`='1' ORDER BY id DESC LIMIT 1", $row_vanzare['id']);
$next_query = sprintf("SELECT id  FROM `masini` WHERE id > %d AND `stare_anunt`='1' ORDER BY id ASC LIMIT 1 ", $row_vanzare['id']);
$prev_id = mysqli_fetch_assoc($connection->query($prev_query))['id'];
$next_id = mysqli_fetch_assoc($connection->query($next_query))['id'];
//var_dump($prev_id, $next_id);exit;
$page_subtitle = "An fabricaţie: " . $row_vanzare['an'];


/**
 * added by Gabriela Brezeanu on 30.10.2025
 * Parse vehicle specifications from HTML detalii field
 * Extracts detailed information using regex patterns
 */
function parseVehicleDetails($html_details) {
    $details = [];
    $text = strip_tags($html_details);
    
    // Parse engine displacement (e.g., "Motor 2.3L DCi EURO5;")
    if (preg_match('/Motor\s+(?:Turbo-?)?(?:DIESEL|benzina)?\s*(\d+\.?\d*)\s*L[;\s,\.]*/ui', $text, $matches)) {
        $details['engineDisplacement'] = (float)$matches[1] * 1000; // Convert to CC
    }
    
    // Parse engine power in kW and CP/HP - supports both formats: "92kW / 125CP" or "125 CP / 92 kW"
    if (preg_match('/Putere\s+motor[:\s]+(\d+)\s*kW\s*\/\s*(\d+)\s*(?:CP|HP)[;\s,\.]*/ui', $text, $matches)) {
        $details['enginePowerKW'] = (int)$matches[1];
        $details['enginePowerHP'] = (int)$matches[2];
    } elseif (preg_match('/Putere\s+motor[:\s]+(\d+)\s*(?:CP|HP)\s*\/\s*(\d+)\s*kW[;\s,\.]*/ui', $text, $matches)) {
        $details['enginePowerHP'] = (int)$matches[1];
        $details['enginePowerKW'] = (int)$matches[2];
    }
    
    // Parse mileage/kilometers (e.g., "Km parcursi: 172 500;" or "Km. parcursi: 172500;")
    if (preg_match('/Km\.?\s+parcur[şs]i[:\s]+([0-9\s]+)[;\s,\.]*/ui', $text, $matches)) {
        $km_value = preg_replace('/\s+/', '', $matches[1]); // Remove spaces
        $details['mileage'] = (int)$km_value;
    }
    
    // Parse transmission type (e.g., "Cutie de viteze automata;", "Cutie manuala;")
    if (preg_match('/[Cc]utie(?:\s+de\s+viteze)?\s+(automat[aă]|manual[aă]|semi-?automat[aă]|robotizat[aă])[;\s,\.]*/ui', $text, $matches)) {
        $transmission_mapping = [
            'automata' => 'Automatic',
            'manuala' => 'Manual',
            'semi-automata' => 'SemiAutomatic',
            'semiautomata' => 'SemiAutomatic',
            'robotizata' => 'SemiAutomatic'
        ];
        $trans_key = strtolower(str_replace(['ă', '-'], ['a', ''], trim($matches[1])));
        $details['transmissionType'] = $transmission_mapping[$trans_key] ?? 'Manual';
    }
    
    // Parse transmission gears (e.g., "Cutie de viteze manuala 6+1 trepte;")
    if (preg_match('/[Cc]utie(?:\s+de\s+viteze)?(?:\s+(?:manuala|automata))?\s*(?:cu)?\s*(\d+)\+1\s+(?:trepte|viteze)[;\s,\.]*/ui', $text, $matches)) {
        $details['numberOfGears'] = (int)$matches[1];
    }
    
    // Parse fuel consumption (e.g., "Consum extra-urban: 4.3L / 100km;")
    if (preg_match('/Consum\s+(?:extra-?urban|mediu)[:\s]+(\d+\.?\d*)\s*L[;\s,\/\.]*/ui', $text, $matches)) {
        $details['fuelConsumption'] = (float)$matches[1];
    }
    
    // Parse drive configuration (e.g., "Transmisie: fata;" or "Tractiune: fata;")
    if (preg_match('/(?:Transmisie|Tractiune)[:\s]+(fata|spate|4x4|4WD|AWD|integrala)[;\s,\.]*/ui', $text, $matches)) {
        $drive_mapping = [
            'fata' => 'FrontWheelDriveConfiguration',
            'spate' => 'RearWheelDriveConfiguration',
            '4x4' => 'FourWheelDriveConfiguration',
            '4wd' => 'FourWheelDriveConfiguration',
            'awd' => 'FourWheelDriveConfiguration',
            'integrala' => 'FourWheelDriveConfiguration'
        ];
        $drive_key = strtolower(trim($matches[1]));
        $details['driveWheelConfiguration'] = $drive_mapping[$drive_key] ?? 'FrontWheelDriveConfiguration';
    }
    
    // Parse number of doors (e.g., "3 usi;", "5 usi;", "Nr. usi: 5;")
    if (preg_match('/(?:Nr\.?\s+u[șs]i|(\d+)\s+u[șs]i)[:\s]*(\d+)?[;\s,\.]*/ui', $text, $matches)) {
        $details['numberOfDoors'] = (int)($matches[2] ?? $matches[1]);
    }
    
    // Parse number of seats (e.g., "5 locuri;", "Nr. locuri: 7;")
    if (preg_match('/(?:Nr\.?\s+locuri|(\d+)\s+locuri)[:\s]*(\d+)?[;\s,\.]*/ui', $text, $matches)) {
        $details['seatingCapacity'] = (int)($matches[2] ?? $matches[1]);
    }
    
    // Parse Euro emission standard (e.g., "EURO5;", "EURO 6;")
    if (preg_match('/EURO\s*(\d+\w?)[;\s,\.]*/ui', $text, $matches)) {
        $details['euroStandard'] = 'Euro ' . $matches[1];
    }
    
    // Parse interior material (stofa/piele/textil) (e.g., "Tapițerie din stofa;")
    if (preg_match('/(?:Tapi[țt]erie|interior)(?:\s+din)?\s+(stofa|piele|textil|alcantara)[;\s,\.]*/ui', $text, $matches)) {
        $details['vehicleInteriorType'] = ucfirst(strtolower($matches[1]));
    }
    
    // Parse color if mentioned in details (e.g., "Vopsea metalizata;")
    if (preg_match('/(?:culoare|vopsea)[:\s]*(metalizat[aă]?|nemetalizat[aă]?|alb[aă]?|negr[uă]?|ro[șs]u|albastru|gri|argintiu)[;\s,\.]*/ui', $text, $matches)) {
        $details['paintType'] = trim($matches[1]);
    }
    
    // Parse cargo volume (e.g., "Volum util: 19,5 mc" or "Volum portbagaj: 520L")
    if (preg_match('/Volum\s+(?:util|portbagaj)[:\s]+([0-9,\.]+)\s*(mc|m3|L|litri)/ui', $text, $matches)) {
        $volume = (float)str_replace(',', '.', $matches[1]);
        $unit = strtolower($matches[2]);
        
        // Convert to liters (L) as standard unit
        if (in_array($unit, ['mc', 'm3'])) {
            $details['cargoVolume'] = $volume * 1000; // Convert cubic meters to liters
        } else {
            $details['cargoVolume'] = $volume; // Already in liters
        }
    }
    
    // Parse payload/useful load (e.g., "Sarcina utila: aprox. 1350kg" or "Sarcina utila: 750 kg;")
    if (preg_match('/Sarcin[aă]\s+util[aă][:\s]+(?:(?:aprox|cca|circa|~)\.?\s*)?([0-9]+)\s*kg[;\s,\.]*/ui', $text, $matches)) {
        $details['payload'] = (int)$matches[1];
    }
    
    // Parse maximum authorized weight (e.g., "Sarcina maxima autorizata: 3500kg" or "Sarcina maxima: 3500 kg;")
    if (preg_match('/Sarcin[aă]\s+maxim[aă](?:\s+autorizat[aă])?[:\s]+([0-9]+)\s*kg[;\s,\.]*/ui', $text, $matches)) {
        $details['maxWeight'] = (int)$matches[1];
    }
    
    return $details;
}

// Parse details from HTML
$parsed_details = parseVehicleDetails($row_vanzare['detalii']);

// Enhance parsed_details with data from for_csv if available (prioritize for_csv data)
if ($row_for_csv) {
    // Override/complete parsed details with for_csv data (more accurate)
    if (!empty($row_for_csv['kilometraj']) && is_numeric($row_for_csv['kilometraj'])) {
        $parsed_details['mileage'] = (int)$row_for_csv['kilometraj'];
    }
    if (!empty($row_for_csv['cilindri']) && is_numeric($row_for_csv['cilindri'])) {
        $parsed_details['engineDisplacement'] = (int)$row_for_csv['cilindri'];
    }
    if (!empty($row_for_csv['putere_motor']) && is_numeric($row_for_csv['putere_motor'])) {
        // Use for_csv only when text parse didn't find power (prefer values from detalii text)
        if (empty($parsed_details['enginePowerHP']) || empty($parsed_details['enginePowerKW'])) {
            $parsed_details['enginePowerHP'] = (int)$row_for_csv['putere_motor'];
            $parsed_details['enginePowerKW'] = (int)round($row_for_csv['putere_motor'] * 0.745699872);
        }
    }
    if (!empty($row_for_csv['norma_euro'])) {
        $parsed_details['euroStandard'] = 'Euro ' . $row_for_csv['norma_euro'];
    }
    if (!empty($row_for_csv['transmisie'])) {
        $transmission_mapping = [
            'automata' => 'Automatic',
            'manuala' => 'Manual',
            'semi-automata' => 'SemiAutomatic',
            'semiautomata' => 'SemiAutomatic',
            'robotizata' => 'SemiAutomatic'
        ];
        $trans_key = strtolower(str_replace(['ă', '-'], ['a', ''], trim($row_for_csv['transmisie'])));
        $parsed_details['transmissionType'] = $transmission_mapping[$trans_key] ?? 'Manual';
    }
    if (!empty($row_for_csv['tractiune'])) {
        $drive_mapping = [
            'fata' => 'FrontWheelDriveConfiguration',
            'spate' => 'RearWheelDriveConfiguration',
            '4x4' => 'FourWheelDriveConfiguration',
            '4wd' => 'FourWheelDriveConfiguration',
            'awd' => 'FourWheelDriveConfiguration',
            'integrala' => 'FourWheelDriveConfiguration'
        ];
        $drive_key = strtolower(trim($row_for_csv['tractiune']));
        $parsed_details['driveWheelConfiguration'] = $drive_mapping[$drive_key] ?? 'FrontWheelDriveConfiguration';
    }
    if (!empty($row_for_csv['nr_usi']) && is_numeric($row_for_csv['nr_usi'])) {
        $parsed_details['numberOfDoors'] = (int)$row_for_csv['nr_usi'];
    }
    if (!empty($row_for_csv['nr_locuri']) && is_numeric($row_for_csv['nr_locuri'])) {
        $parsed_details['seatingCapacity'] = (int)$row_for_csv['nr_locuri'];
    }
    if (!empty($row_for_csv['culoarea'])) {
        $parsed_details['paintType'] = trim($row_for_csv['culoarea']);
    }
    if (!empty($row_for_csv['caroserie'])) {
        $parsed_details['bodyType'] = trim($row_for_csv['caroserie']);
    }
}

// DEBUG: Log parsed details
// error_log("DEBUG Car ID {$row_vanzare['id']}: Parsed details = " . print_r($parsed_details, true));

// Set SEO meta tags for car detail page
require_once './includes/meta_tags.php';

// Verifică dacă este utilaj sau mașină
$is_utilaje = (!empty($row_vanzare['cat']) && $row_vanzare['cat'] === 'Utilaje');

// Construiește numele pentru meta tags și title
if ($is_utilaje) {
	// Pentru utilaje: tip-ul conține deja marca + model (din add_anunt.php)
	// Dacă tip este gol, construim din marca + model + subcateg
	if (!empty($row_vanzare['tip'])) {
		// Tip-ul conține deja marca + model, doar adăugăm subcateg dacă există
		$name_parts = array();
		$name_parts[] = $row_vanzare['tip'];
		if (!empty($row_vanzare['subcateg'])) {
			$name_parts[] = '- Utilaje ' . $row_vanzare['subcateg'];
		} else {
			$name_parts[] = '- Utilaje';
		}
		$car_name_meta = trim(implode(' ', $name_parts));
	} else {
		// Dacă tip este gol, construim din marca + model + subcateg
		$name_parts = array();
		if (!empty($row_vanzare['marca'])) {
			$name_parts[] = $row_vanzare['marca'];
		}
		// Pentru model, ar trebui să fie în for_csv, dar folosim ce avem
		if (!empty($row_for_csv['model'])) {
			$name_parts[] = $row_for_csv['model'];
		}
		if (!empty($row_vanzare['subcateg'])) {
			$name_parts[] = '- Utilaje ' . $row_vanzare['subcateg'];
		} else {
			$name_parts[] = '- Utilaje';
		}
		$car_name_meta = trim(implode(' ', $name_parts));
	}
} else {
	// Pentru mașini: "Marca Tip"
	$car_name_meta = trim($row_vanzare['marca'] . " " . $row_vanzare['tip']);
}

$car_year = !empty($row_vanzare['an']) ? (int)$row_vanzare['an'] : null;
$car_price_formatted = number_format($row_vanzare['pret'], 0, ',', '.');
$car_price = !empty($row_vanzare['pret']) ? (float)$row_vanzare['pret'] : null;
$car_main_image = !empty($car_pictures) && !empty($car_pictures[0]['file']) ? BASE_PATH . "foto/" . $car_pictures[0]['file'] : null;
$car_keywords = !empty($row_vanzare['keywords']) ? $row_vanzare['keywords'] : null;
setCarDetailMeta($car_name_meta, $car_year, $car_price_formatted, $car_main_image, $car_keywords);

// Set optimized page title (direct in file for easier maintenance)
$title_parts = [$car_name_meta];
if ($car_year) {
    $title_parts[] = $car_year;
}
if ($car_price) {
    $title_parts[] = number_format($car_price, 0, ',', '.') . " EUR";
}

// Build base alt text for images BEFORE removing price (for better SEO)
// Images should always have price in alt text even if removed from page title
$image_alt_base = implode(" - ", $title_parts);

// Check length - if too long, remove price from page title only
$suffix = $is_utilaje ? " | Utilaje - Autoland România" : " | Second Hand - Autoland România";
$temp_title = implode(" - ", $title_parts) . $suffix;
if (mb_strlen($temp_title, 'UTF-8') > 60) {
    array_pop($title_parts); // Remove price if title too long (only for page title)
}
$page_title = implode(" - ", $title_parts) . $suffix;

// Build comprehensive structured data following Google's Product + Car schema
// Using Product wrapper with itemOffered Car for better SEO (like AutoScout24)
$car_name = $car_name_meta;
$car_url = BASE_PATH . "vanzari_detail.php?id=" . $row_vanzare['id'];

// Build SEO-optimized description with key details
$description_parts = [];

// Add car name
$description_parts[] = $car_name;

// Add year
if (!empty($row_vanzare['an'])) {
    $description_parts[] = "an fabricație " . $row_vanzare['an'];
}

// Add mileage if available
if (!empty($parsed_details['mileage']) && $parsed_details['mileage'] > 0) {
    $description_parts[] = number_format($parsed_details['mileage'], 0, ',', '.') . " km";
}

// Add engine info if available
if (!empty($parsed_details['engineDisplacement']) && !empty($row_vanzare['combustibil'])) {
    $engine_cc = round($parsed_details['engineDisplacement'] / 1000, 1);
    $description_parts[] = $engine_cc . "L " . $row_vanzare['combustibil'];
} elseif (!empty($row_vanzare['combustibil'])) {
    $description_parts[] = $row_vanzare['combustibil'];
}

// Add power if available
if (!empty($parsed_details['enginePowerHP'])) {
    $description_parts[] = $parsed_details['enginePowerHP'] . " CP";
}

// Add transmission if available
if (!empty($parsed_details['transmissionType'])) {
    $trans_ro = ($parsed_details['transmissionType'] == 'Automatic') ? 'automată' : 'manuală';
    $description_parts[] = "cutie " . $trans_ro;
}

// Build the description
$description = implode(', ', $description_parts);

// Add condition and dealer info
if (!empty($row_vanzare['stare_masina'])) {
    $description .= ". " . ucfirst($row_vanzare['stare_masina']);
}
$description .= ". Disponibil la Autoland România - dealer autorizat cu garanție.";

// Fallback if description is too short
if (strlen($description) < 50) {
    $description = "Descoperă " . $car_name;
    if (!empty($row_vanzare['an'])) {
        $description .= " din " . $row_vanzare['an'];
    }
    $description .= " la Autoland România - dealer auto second hand de încredere cu garanție și posibilități de finanțare.";
}

// Prepare main image
$main_image_url = BASE_PATH . "themes/automotive/images/logo_Autoland.png";
if (!empty($car_pictures) && !empty($car_pictures[0]['file'])) {
    $main_image_url = BASE_PATH . "foto/" . $car_pictures[0]['file'];
}

// Currency mapping (EUR is default, as prices are shown in euro)
$currency = 'EUR';

// Start building the Product structured data
$structured_data = [
    "@context" => "https://schema.org",
    "@type" => "Product",
    "@id" => $car_url . "#product",  // UNIQUE ID pentru Product
    "name" => $car_name,
    "description" => $description,
    "brand" => [
        "@type" => "Brand",
        "name" => trim($row_vanzare['marca'])
    ],
    "image" => $main_image_url,
    "color" => !empty($parsed_details['paintType']) ? ucfirst($parsed_details['paintType']) : null,
    "mainEntityOfPage" => [
        "@type" => "WebPage",
        "@id" => $car_url . "#webpage"  // CONECTARE cu WebPage
    ]
];

// Remove null color if not found
if (is_null($structured_data["color"])) {
    unset($structured_data["color"]);
}

// Build the Offer with seller information
$structured_data["offers"] = [
    "@type" => "Offer",
    "price" => (float)$row_vanzare['pret'],
    "priceCurrency" => $currency,
    "itemCondition" => "UsedCondition",
    "availability" => $is_sold ? "https://schema.org/SoldOut" : "https://schema.org/InStock",
    "url" => $car_url,
    "seller" => [
        "@id" => "https://autolandromania.ro/#organization"  // CONECTARE cu Organization prin @id
    ],
    "offeredBy" => [
        "@id" => "https://autolandromania.ro/#organization"  // CONECTARE cu Organization prin @id (duplicate pentru compatibilitate)
    ]
];

// Add itemOffered (the actual Car object with all specifications)
// Use model from for_csv if available, otherwise use tip from masini
$car_model = ($row_for_csv && !empty($row_for_csv['model'])) 
    ? trim($row_for_csv['model']) 
    : trim($row_vanzare['tip']);

$car_data = [
    "@type" => "Car",
    "name" => $car_name,
    "brand" => trim($row_vanzare['marca']),
    "model" => $car_model,
    "identifier" => "#S10" . $row_vanzare['id'],
    "url" => $car_url,
    "image" => $main_image_url
];

// Add production date/year
$current_year = (int)date('Y');
if (!empty($row_vanzare['an']) && $row_vanzare['an'] <= $current_year && $row_vanzare['an'] >= 1900) {
    $car_data["productionDate"] = $row_vanzare['an'] . "-01-01";
    $car_data["vehicleModelDate"] = (int)$row_vanzare['an'];
}

// Add manufacturer (same as brand for simplicity)
$car_data["manufacturer"] = trim($row_vanzare['marca']);

// Add body type - use caroserie from for_csv if available, otherwise map from category
if ($row_for_csv && !empty($row_for_csv['caroserie'])) {
    $body_type_mapping_csv = [
        'sedan' => 'Sedan',
        'hatchback' => 'Hatchback',
        'break' => 'StationWagon',
        'combi' => 'StationWagon',
        'suv' => 'SUV',
        'coupe' => 'Coupe',
        'cabriolet' => 'Convertible',
        'monovolume' => 'Minivan',
        'pickup' => 'PickupTruck',
        'van' => 'Van',
        'truck' => 'Truck'
    ];
    $caroserie_key = strtolower(trim($row_for_csv['caroserie']));
    $car_data["bodyType"] = $body_type_mapping_csv[$caroserie_key] ?? ucfirst($row_for_csv['caroserie']);
} else {
    $body_type_mapping = [
        'Autoturisme' => 'Sedan',
        'Autoutilitare' => 'Van',
        'Autospecializate' => 'Truck'
    ];
    $body_type = $body_type_mapping[$row_vanzare['cat']] ?? 'Automobile';
    $car_data["bodyType"] = $body_type;
}

// Add color - prioritize for_csv, then parsed details
if ($row_for_csv && !empty($row_for_csv['culoarea'])) {
    $car_data["color"] = ucfirst(trim($row_for_csv['culoarea']));
} elseif (!empty($parsed_details['paintType'])) {
    $car_data["color"] = ucfirst($parsed_details['paintType']);
}

// Add fuel type
if (!empty($row_vanzare['combustibil']) && $row_vanzare['combustibil'] != 'fara') {
    $fuel_mapping = [
        'benzina' => 'Gasoline',
        'motorina' => 'Diesel',
        'diesel' => 'Diesel',
        'hybrid' => 'Hybrid',
        'electric' => 'Electric',
        'gpl' => 'LPG',
        'lpg' => 'LPG'
    ];
    $fuel_key = strtolower(trim($row_vanzare['combustibil']));
    if (isset($fuel_mapping[$fuel_key])) {
        $car_data["fuelType"] = $fuel_mapping[$fuel_key];
    }
}

// Add mileage from parsed details
if (!empty($parsed_details['mileage']) && $parsed_details['mileage'] > 0 && $parsed_details['mileage'] < 1000000) {
    $car_data["mileageFromOdometer"] = [
        "@type" => "QuantitativeValue",
        "value" => $parsed_details['mileage'],
        "unitText" => "KMT"
    ];
}

// Add drive wheel configuration
if (!empty($parsed_details['driveWheelConfiguration'])) {
    $car_data["driveWheelConfiguration"] = $parsed_details['driveWheelConfiguration'];
}

// Add transmission type - parsed from details or default to Manual
if (!empty($parsed_details['transmissionType'])) {
    $car_data["vehicleTransmission"] = $parsed_details['transmissionType'];
} else {
    // Default to Manual if not specified
    $car_data["vehicleTransmission"] = "Manual";
}

// Add number of forward gears
if (!empty($parsed_details['numberOfGears'])) {
    $car_data["numberOfForwardGears"] = $parsed_details['numberOfGears'];
}

// Add number of doors
if (!empty($parsed_details['numberOfDoors'])) {
    $car_data["numberOfDoors"] = $parsed_details['numberOfDoors'];
}

// Add seating capacity
if (!empty($parsed_details['seatingCapacity'])) {
    $car_data["seatingCapacity"] = $parsed_details['seatingCapacity'];
}

// Add interior type
if (!empty($parsed_details['vehicleInteriorType'])) {
    $car_data["vehicleInteriorType"] = $parsed_details['vehicleInteriorType'];
} else {
    $car_data["vehicleInteriorType"] = "Fabric"; // Default
}

// Add vehicle engine specifications
if (!empty($parsed_details['enginePowerKW']) || !empty($parsed_details['engineDisplacement'])) {
    $engine_spec = [
        "@type" => "EngineSpecification"
    ];
    
    if (!empty($parsed_details['enginePowerKW']) && !empty($parsed_details['enginePowerHP'])) {
        $engine_spec["enginePower"] = [
            [
                "@type" => "QuantitativeValue",
                "value" => $parsed_details['enginePowerHP'],
                "unitCode" => "BHP"
            ],
            [
                "@type" => "QuantitativeValue",
                "value" => $parsed_details['enginePowerKW'],
                "unitCode" => "KWT"
            ]
        ];
    }
    
    if (!empty($parsed_details['engineDisplacement'])) {
        $engine_spec["engineDisplacement"] = [
            "@type" => "QuantitativeValue",
            "value" => $parsed_details['engineDisplacement'],
            "unitCode" => "CMQ"
        ];
    }
    
    $car_data["vehicleEngine"] = [$engine_spec];
}

// Add fuel consumption
if (!empty($parsed_details['fuelConsumption'])) {
    $car_data["fuelConsumption"] = [
        "@type" => "QuantitativeValue",
        "value" => $parsed_details['fuelConsumption'],
        "unitText" => "l/100km"
    ];
}

// Add energy efficiency / Euro standard
if (!empty($parsed_details['euroStandard'])) {
    $car_data["hasEnergyConsumptionDetails"] = [
        "@type" => "EnergyConsumptionDetails",
        "energyEfficiencyScaleMax" => "Euro 6d",
        "energyEfficiencyScaleMin" => "Euro 1",
        "fuelEfficiency" => $parsed_details['euroStandard']
    ];
}

// Add cargo volume (especially important for vans/commercial vehicles)
if (!empty($parsed_details['cargoVolume'])) {
    $car_data["cargoVolume"] = [
        "@type" => "QuantitativeValue",
        "value" => $parsed_details['cargoVolume'],
        "unitCode" => "LTR"
    ];
}

// Add payload capacity (for commercial vehicles)
if (!empty($parsed_details['payload'])) {
    $car_data["payload"] = [
        "@type" => "QuantitativeValue",
        "value" => $parsed_details['payload'],
        "unitCode" => "KGM"
    ];
    // DEBUG: Log that payload was added
    // error_log("DEBUG: Payload added to JSON-LD for car ID {$row_vanzare['id']}: {$parsed_details['payload']} kg");
}

// Add weight (total authorized weight for commercial vehicles)
if (!empty($parsed_details['maxWeight'])) {
    $car_data["weightTotal"] = [
        "@type" => "QuantitativeValue",
        "value" => $parsed_details['maxWeight'],
        "unitCode" => "KGM"
    ];
}

// Add the car as itemOffered in the offer
$structured_data["offers"]["itemOffered"] = $car_data;

// Final validation
$required_fields = ['@context', '@type', 'name', 'brand', 'offers'];
$is_valid = true;

foreach ($required_fields as $field) {
    if (!isset($structured_data[$field])) {
        $is_valid = false;
        error_log("Missing required structured data field: $field for car ID: " . $row_vanzare['id']);
        break;
    }
}

// Only create JSON-LD if valid
if ($is_valid && !empty($structured_data['name']) && !empty($structured_data['brand']['name'])) {
    // Include JSON-LD schemas helper for WebPage connection
    require_once './includes/json_ld_schemas.php';
    
    // Create connected schemas: Product + WebPage + Organization + BreadcrumbList
    $page_title = $car_name . " - Second Hand - Autoland România";
    $page_description = $description;
    $webpage_schema = getWebPageSchema($car_url, $page_title, $page_description);
    
    // Build breadcrumbs for car detail page
    $breadcrumbs = [
        ['name' => 'Acasă', 'url' => BASE_PATH],
        ['name' => 'Stoc Auto', 'url' => BASE_PATH . 'vanzari.php'],
        ['name' => $car_name, 'url' => $car_url]
    ];
    
    // Combine all schemas: Product + WebPage + Organization + BreadcrumbList
    $all_schemas = [
        $structured_data,              // Product schema
        $webpage_schema,               // WebPage schema (connected to WebSite and Organization)
        getOrganizationSchema(),       // Organization schema (AutoDealer)
        getBreadcrumbSchema($breadcrumbs)  // BreadcrumbList schema
    ];
    
    // Output as JSON-LD string and add to header_scripts
    $header_scripts .= getJsonLdString($all_schemas);
} else {
    error_log("Invalid structured data for car ID: " . $row_vanzare['id'] . " - JSON-LD not generated");
}

// Preload LCP image (Largest Contentful Paint optimization)
// The first car image is typically the LCP element
if (!empty($car_pictures) && !empty($car_pictures[0]['file'])) {
    $lcp_image_url = BASE_PATH . "foto/" . $car_pictures[0]['file'];
    // Preload LCP image with high priority
    $header_scripts .= '<link rel="preload" as="image" href="' . htmlspecialchars($lcp_image_url) . '" fetchpriority="high">' . PHP_EOL;
}

// Load reCAPTCHA when form becomes visible (Intersection Observer) - Optimized and working
$footer_scripts .= "<script>
(function() {
    // Prevent duplicate loading
    if (window.recaptchaLoaded || document.querySelector('script[src*=\"recaptcha/api.js\"]')) {
        return;
    }
    
    var recaptchaLoaded = false;
    function loadRecaptcha() {
        if (recaptchaLoaded || window.recaptchaLoaded) return;
        recaptchaLoaded = true;
        window.recaptchaLoaded = true;
        var script = document.createElement('script');
        script.src = 'https://www.google.com/recaptcha/api.js';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }
    
    // Load when form is visible or after DOM is ready
    function initRecaptcha() {
        var formElement = document.querySelector('#informatii, .g-recaptcha');
        if (formElement) {
            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            loadRecaptcha();
                            observer.disconnect();
                        }
                    });
                }, { rootMargin: '200px' });
                observer.observe(formElement);
            } else {
                // Fallback for browsers without IntersectionObserver
                loadRecaptcha();
            }
        }
        
        // Load only when user interacts with form (focus, click, or scrolls near it)
        var formInputs = formElement.querySelectorAll('input, textarea, select');
        formInputs.forEach(function(input) {
            input.addEventListener('focus', loadRecaptcha, { once: true, passive: true });
            input.addEventListener('click', loadRecaptcha, { once: true, passive: true });
        });
        
        // Load on form submit attempt
        if (formElement.tagName === 'FORM') {
            formElement.addEventListener('submit', function(e) {
                if (!window.recaptchaLoaded) {
                    e.preventDefault();
                    loadRecaptcha();
                    setTimeout(function() { formElement.submit(); }, 1000);
                }
            }, { once: false, passive: false });
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRecaptcha);
    } else {
        initRecaptcha();
    }
})();
</script>";
$footer_scripts .= "<script type='text/javascript' src='" . BASE_PATH . "themes/automotive/js/jquery.validate.min.js' defer></script>";
$footer_scripts .= "<script type='text/javascript' src='" . BASE_PATH . "themes/automotive/js/additional-methods.min.js' defer></script>";
$footer_scripts .= "<script type='text/javascript'>" . file_get_contents("./includes/sunt_interesat_de_masina_footer.js") . "</script>";

//$content = is_null($row) ? "Content is missing" : $row['inmatriculari'];
// /////////////////////////////////////////////////////////////////////////////
// get header template part
include './themes/automotive/template_parts/header.php';
?>

<section class="content">
    <div class="container">
        <div class="inner-page inventory-listing">
            <!-- <div class="inventory-heading margin-bottom-10 clearfix">
                <div class="row">
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                        <h2><?php echo $row_vanzare['marca'] . " - " . $row_vanzare['tip']; ?></h2>
                        <span class="margin-top-10">Cod #S10<?php echo $row_vanzare['id']; ?></span>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <?php
                        if (!(strcmp("0", $row_vanzare['pret_vechi']))) {
                            echo "<h2>" . $row_vanzare['pret'] . " &euro;</h2>";
                        } else {
                            echo '<h2 class="badge"><del>' . $row_vanzare['pret_vechi'] . ' &euro;</del></h2>';
                            echo '<h2 class="badge danger" style="background-color: #c7081b">' . $row_vanzare['pret'] . '&euro;</h2>';
                        }
                        ?>
                    </div>
                </div>
            </div> -->
            
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 left-content padding-left-none">
                    <!--OPEN OF SLIDER-->
                    <div class="listing-slider">
                        <?php
//                        if ((strcmp("0", $row_vanzare['pret_vechi']))) {
//                            ?>
<!--                            <div class="angled_badge red">
                                <span>La reducere</span>
                            </div>-->
                            <?php
//                        }
                        ?>
                        <section class="slider home-banner">
                            <div class="flexslider" id="home-slider-canvas">
                                <ul class="slides">
                                    <!--                                    <li data-thumb="themes/automotive/images/boxster1_slide.jpg">
                                                                            <img data-no-retina src="themes/automotive/images/boxster1_slide.jpg" alt="" data-full-image="themes/automotive/images/boxster1.jpg" />
                                                                        </li>-->
                                    <?php
                                    // Combine images and videos, sorted by nota (order)
                                    $all_media = [];
                                    
                                    // Add images
                                    foreach ($car_pictures as $key => $picture) {
                                        $all_media[] = [
                                            'type' => 'image',
                                            'nota' => isset($picture['nota']) ? (int)$picture['nota'] : $key,
                                            'data' => $picture,
                                            'key' => $key
                                        ];
                                    }
                                    
                                    // Add videos
                                    foreach ($car_videos as $key => $video) {
                                        $all_media[] = [
                                            'type' => 'video',
                                            'nota' => isset($video['nota']) ? (int)$video['nota'] : (1000 + $key), // Videos after images by default
                                            'data' => $video,
                                            'key' => $key
                                        ];
                                    }
                                    
                                    // Sort by nota (order)
                                    usort($all_media, function($a, $b) {
                                        if ($a['nota'] == $b['nota']) {
                                            return 0;
                                        }
                                        return ($a['nota'] < $b['nota']) ? -1 : 1;
                                    });
                                    
                                    // Display all media in order
                                    foreach ($all_media as $index => $media) {
                                        if ($media['type'] == 'image') {
                                            $picture = $media['data'];
                                            $image_number = $index + 1;
                                            $image_alt = htmlspecialchars($image_alt_base . " - imagine " . $image_number);
                                            $image_src = "foto/" . $picture['file'];
                                            $is_first = ($index === 0);
                                            $should_preload = ($index < 5); // Preload first 5 images (visible in thumbnails - FIX for mobile blank slides)
                                            ?>
                                            <li data-thumb="<?php echo $image_src; ?>">
                                                <img data-no-retina 
                                                     src="<?php echo $image_src; ?>" 
                                                     <?php if (!$should_preload): ?>loading="lazy"<?php endif; ?>
                                                     <?php if ($is_first): ?>fetchpriority="high"<?php endif; ?>
                                                     alt="<?php echo $image_alt; ?>" 
                                                     data-full-image="<?php echo $image_src; ?>" />
                                            </li>
                                            <?php
                                        } else {
                                            // Video
                                            $video = $media['data'];
                                            $video_number = $index + 1;
                                            $video_alt = htmlspecialchars($image_alt_base . " - video " . $video_number);
                                            $video_src = "video/" . $video['file'];
                                            $video_thumb = !empty($video['thumb']) ? $video['thumb'] : '';
                                            
                                            // Fallback thumbnail
                                            if (empty($video_thumb) && !empty($car_pictures)) {
                                                $video_thumb = "foto/" . $car_pictures[0]['file'];
                                            } elseif (empty($video_thumb)) {
                                                $video_thumb = "themes/automotive/images/video_placeholder.jpg";
                                            }
                                            ?>
                                            <li data-thumb="<?php echo $video_thumb; ?>" class="video-slide">
                                                <div style="position: relative; width: 100%; height: 100%; background: #000; display: flex; align-items: center; justify-content: center;">
                                                    <img data-no-retina 
                                                         src="<?php echo $video_thumb; ?>" 
                                                         loading="lazy"
                                                         alt="<?php echo $video_alt; ?>" 
                                                         data-full-image="<?php echo $video_thumb; ?>"
                                                         data-video-url="<?php echo htmlspecialchars($video_src); ?>" />
                                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10; pointer-events: none;">
                                                        <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 2px 8px rgba(0,0,0,0.5));">
                                                            <circle cx="40" cy="40" r="40" fill="rgba(0,0,0,0.6)"/>
                                                            <path d="M32 24L32 56L56 40L32 24Z" fill="white"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </section>
                        <section class="home-slider-thumbs">
                            <div class="flexslider" id="home-slider-thumbs">
                                <ul class="slides">
                                    <!--                                    <li data-thumb="themes/automotive/images/thumbnail1.jpg">
                                                                            <a href="#">
                                                                                <img data-no-retina src="themes/automotive/images/thumbnail1.jpg" alt="" />
                                                                            </a>
                                                                        </li>-->
                                    <?php
                                    // Use the same sorted media array for thumbnails
                                    foreach ($all_media as $index => $media) {
                                        if ($media['type'] == 'image') {
                                            $picture = $media['data'];
                                            $image_number = $index + 1;
                                            $image_alt = htmlspecialchars($image_alt_base . " - imagine " . $image_number);
                                            $image_src = "foto/" . $picture['file'];
                                            $is_first = ($index === 0);
                                            $should_preload = ($index < 5); // Preload first 5 thumbnails (visible on screen)
                                            ?>
                                            <li data-thumb="<?php echo $image_src; ?>">
                                                <a href="#">
                                                    <img data-no-retina 
                                                         src="<?php echo $image_src; ?>" 
                                                         <?php if (!$should_preload): ?>loading="lazy"<?php endif; ?>
                                                         alt="<?php echo $image_alt; ?>" />
                                                </a>
                                            </li>
                                            <?php
                                        } else {
                                            // Video thumbnail
                                            $video = $media['data'];
                                            $video_number = $index + 1;
                                            $video_alt = htmlspecialchars($image_alt_base . " - video " . $video_number);
                                            $video_thumb = !empty($video['thumb']) ? $video['thumb'] : '';
                                            
                                            // Fallback thumbnail
                                            if (empty($video_thumb) && !empty($car_pictures)) {
                                                $video_thumb = "foto/" . $car_pictures[0]['file'];
                                            } elseif (empty($video_thumb)) {
                                                $video_thumb = "themes/automotive/images/video_placeholder.jpg";
                                            }
                                            ?>
                                            <li data-thumb="<?php echo $video_thumb; ?>" class="video-thumb">
                                                <a href="#">
                                                    <div style="position: relative; display: inline-block;">
                                                        <img data-no-retina 
                                                             src="<?php echo $video_thumb; ?>" 
                                                             loading="lazy"
                                                             alt="<?php echo $video_alt; ?>" />
                                                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); pointer-events: none;">
                                                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));">
                                                                <circle cx="20" cy="20" r="20" fill="rgba(0,0,0,0.6)"/>
                                                                <path d="M16 12L16 28L28 20L16 12Z" fill="white"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </section>
                    </div>
                    <!--CLOSE OF SLIDER-->
                    <!--Slider End-->
                    <div class="clearfix"></div>
                    
                    <!-- Mobile Sidebar Placeholder - will be filled by JS on mobile -->
                    <div class="mobile-sidebar-placeholder"></div>
                    
                    <div class="bs-example bs-example-tabs example-tabs margin-top-50">
<!--
                        <ul id="myTab" class="nav nav-tabs">
                            <li class="active"><a href="#vehicle" data-toggle="tab">Detalii</a></li>
                            <li class="request gradient_button"><a id="moreinfo" href="#informatii" data-toggle="tab">Cere mai multe informaţii</a></li>
                        </ul>
-->                        
                        <div id="myTabContent" class="tab-content margin-top-15 margin-bottom-20">
                            <div id="vehicle" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
                                    Descriere Detaliată
                                </h2>
                                <div style="font-size: 16px; line-height: 1.8; color: #334155;">
                                    <?php echo $row_vanzare['detalii']; ?>
                                </div>
                            </div>
                            
                            <!-- Contact CTA Section -->
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-top-30 margin-bottom-20">
                                <?php
                                // WhatsApp configuration
                                $whatsapp_phone_cta = '40742377001';
                                $whatsapp_message_cta = 'Bună ziua, mă interesează ' . $row_vanzare['marca'] . ' ' . $row_vanzare['tip'] . ' (Cod #S10' . $row_vanzare['id'] . ' - ' . $car_url . '). Aș dori mai multe informații.';
                                $whatsapp_url_cta = 'https://wa.me/' . $whatsapp_phone_cta . '?text=' . urlencode($whatsapp_message_cta);
                                ?>
                                <p style="font-size: 16px; line-height: 1.7; color: #333; text-align: center;">
                                    <strong>Vă interesează acest auto sau căutați ceva similar?</strong><br>
                                    Contactați-ne rapid prin 
                                    <a href="<?php echo $whatsapp_url_cta; ?>" target="_blank" rel="noopener" style="color: #25D366; font-weight: bold; text-decoration: none;  #25D366;">
                                        <svg style="width: 18px; height: 18px; vertical-align: middle; margin-right: 3px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" fill="currentColor"/>
                                        </svg>WhatsApp
                                    </a>, 
                                    <a href="tel:+40742377001" style="color: #c7081b; font-weight: bold; text-decoration: none; #c7081b;">
                                        <svg style="width: 16px; height: 16px; vertical-align: middle; margin-right: 3px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z" fill="currentColor"/>
                                        </svg>telefonic: 0742 377 001
                                    </a> 
                                    sau completați formularul de mai jos cu specificațiile personale dorite.
                                </p>
                                <p style="font-size: 16px; text-align: center;">
                                    <strong>Vă răspundem în cel mai scurt timp!</strong>
                                </p>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-top-50">
                                <h4>Mă interesează mașina sau ceva similar</h4>
                                <p>Pentru caracteristici specifice dorite, specificați în formularul de mai jos.</p>
                            </div>
                            <div id="informatii">
                                <form id="frmInteresat" name="frmInteresat" method="post" action="<?= BASE_PATH ?>mailer.php">
                                    <div id="result"></div>
                                    <input type="hidden" name="form_title" value="Sunt interesat de <?php echo $page_title; ?>">
                                    <input type="hidden" name="vizUrl" id="vizUrl" value="" />

                                    <div class="form-field">
                                        <input type="text" id="vizNume" name="vizNume" placeholder="Nume și prenume *" required />
                                    </div>
                                    <div class="form-field">
                                        <input type="tel" id="vizTelefon" name="vizTelefon" placeholder="Telefon (ex: 0742123456) *" required />
                                    </div>
                                    <div class="form-field">
                                        <input type="email" id="vizEmail" name="vizEmail" placeholder="Adresa de email *" required />
                                    </div>
                                    <div class="form-field">
                                        <select name="vizJudet" required>
                                            <option value="" disabled selected>Selectați județul *</option>
                                            <option value="Alba">Alba</option>
                                            <option value="Arad">Arad</option>
                                            <option value="Arges">Arges</option>
                                            <option value="Bacau">Bacau</option>
                                            <option value="Bihor">Bihor</option>
                                            <option value="Bistrita-Nasaud">Bistrita-Nasaud</option>
                                            <option value="Botosani">Botosani</option>
                                            <option value="Brasov">Brasov</option>
                                            <option value="Braila">Braila</option>
                                            <option value="Bucuresti">Bucuresti</option>
                                            <option value="Buzau">Buzau</option>
                                            <option value="Caras-Severin">Caras-Severin</option>
                                            <option value="Calarasi">Calarasi</option>
                                            <option value="Cluj">Cluj</option>
                                            <option value="Constanta">Constanta</option>
                                            <option value="Covasna">Covasna</option>
                                            <option value="Dambovita">Dambovita</option>
                                            <option value="Dolj">Dolj</option>
                                            <option value="Galati">Galati</option>
                                            <option value="Giurgiu">Giurgiu</option>
                                            <option value="Gorj">Gorj</option>
                                            <option value="Harghita">Harghita</option>
                                            <option value="Hunedoara">Hunedoara</option>
                                            <option value="Ialomita">Ialomita</option>
                                            <option value="Iasi">Iasi</option>
                                            <option value="Ilfov">Ilfov</option>
                                            <option value="Maramures">Maramures</option>
                                            <option value="Mehedinti">Mehedinti</option>
                                            <option value="Mures">Mures</option>
                                            <option value="Neamt">Neamt</option>
                                            <option value="Olt">Olt</option>
                                            <option value="Prahova">Prahova</option>
                                            <option value="Satu Mare">Satu Mare</option>
                                            <option value="Salaj">Salaj</option>
                                            <option value="Sibiu">Sibiu</option>
                                            <option value="Suceava">Suceava</option>
                                            <option value="Teleorman">Teleorman</option>
                                            <option value="Timis">Timis</option>
                                            <option value="Tulcea">Tulcea</option>
                                            <option value="Vaslui">Vaslui</option>
                                            <option value="Valcea">Valcea</option>
                                            <option value="Vrancea">Vrancea</option>
                                        </select>
                                    </div>
                                    <div class="form-field">
                                        <select name="vizAchizitie" required>
                                            <option value="" disabled selected>Selectați tipul de achiziție *</option>
                                            <option value="societate_comerciala">Societate Comercială</option>
                                            <option value="persoana_fizica">Persoană Fizică</option>
                                            <option value="persoana_fizica_autorizata">PFA</option>
                                        </select>
                                    </div>

                                    <div class="form-field">
                                        <select name="vizPlatain" required>
                                            <option value="" disabled selected>Modalitatea de plată dorită *</option>
                                            <option value="rate">Rate</option>
                                            <option value="leasing">Leasing</option>
                                            <option value="cash">Cash</option>
                                        </select>
                                    </div>

                                    <div class="form-field">
                                        <textarea name="vizMesaj" rows="6" placeholder="Mesajul dumneavoastră (specificați configurația dorită, întrebări despre garanție, etc.)">sunt interesat de auto <?php echo $row_vanzare['marca']; ?> <?php echo $row_vanzare['tip']; ?>, cod(#S10<?php echo $row_vanzare['id']; ?>)</textarea>
                                    </div>
                                    <div class="form-field recaptcha-wrapper">
                                        <div class="g-recaptcha" data-sitekey="6LcbK38UAAAAAMAFTZuUcAh346Pp7PDT_YTO-Veq"></div>
                                        <input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">
                                    </div>
                                    <div class="form-field">
                                        <button id="submit_btn" type="submit">Trimite cererea</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 right-content padding-right-none">
                    <div class="side-content car-detail-sidebar">
                        <!-- Preț & CTA Box - Consolidat -->
                        <div class="car-price-cta-box">
                            <!-- Preț Section -->
                            <div class="car-price-section">
                                <?php if ($is_sold): ?>
                                    <div class="alert alert-warning"><strong>Această mașină a fost vândută.</strong>
                                        Vedeți <a href="<?php echo BASE_PATH; ?>vanzari.php?users=<?php echo urlencode($row_vanzare['cat']); ?>">alte mașini din aceeași categorie</a>.</div>
                                <?php endif; ?>
                                <?php if (!(strcmp("0", $row_vanzare['pret_vechi']))): ?>
                                    <div class="car-price-main">
                                        <?php echo number_format($row_vanzare['pret'], 0, ',', '.'); ?> <span class="car-price-currency">€</span>
                                    </div>
                                <?php else: ?>
                                    <div class="car-price-old">
                                        <?php echo number_format($row_vanzare['pret_vechi'], 0, ',', '.'); ?> €
                                    </div>
                                    <div class="car-price-main car-price-discounted">
                                        <?php echo number_format($row_vanzare['pret'], 0, ',', '.'); ?> <span class="car-price-currency">€</span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Garanție Badge - Integrat -->
                                <?php if (!empty($row_vanzare['pre_anunt'])): ?>
                                    <div class="car-warranty-badge">
                                        <div class="car-warranty-text">
                                            <?php echo htmlspecialchars($row_vanzare['pre_anunt']); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- CTA Buttons & Navigation -->
                            <?php
                            $whatsapp_phone = '40742377001';
                            $whatsapp_message = 'Bună ziua, mă interesează ' . $row_vanzare['marca'] . ' ' . $row_vanzare['tip'] . ' (Cod #S10' . $row_vanzare['id'] . ' - ' . $car_url . '). Aș dori mai multe informații.';
                            $whatsapp_url = 'https://wa.me/' . $whatsapp_phone . '?text=' . urlencode($whatsapp_message);
                            ?>
                            <div class="car-cta-buttons">
                                <a href="<?php echo $whatsapp_url; ?>" target="_blank" rel="noopener" class="car-cta-btn car-cta-btn-whatsapp">
                                    <svg class="car-cta-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" fill="currentColor"/>
                                    </svg>
                                    WhatsApp
                                </a>
                                <a href="tel:+40742377001" class="car-cta-btn car-cta-btn-phone">
                                    <svg class="car-cta-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z" fill="currentColor"/>
                                    </svg>
                                    Apel direct
                                </a>
                                <a href="#informatii" class="car-cta-btn car-cta-btn-contact">
                                    <svg class="car-cta-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" fill="currentColor"/>
                                    </svg>
                                    Formular Contact
                                </a>
                        
                                
                                <div class="car-action-buttons">
                                    <a href="#vehicle" class="car-action-btn">
                                        <span class="car-action-btn-text">Vezi Descrierea</span>
                                    </a>
                                    <a href="<?= BASE_PATH ?>cerere_oferta.php" class="car-action-btn">
                                        <span class="car-action-btn-text">Ofertă Personalizată</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="car-nav-buttons">
                            <?php if (!is_null($prev_id)): ?>
                                <a href="<?php echo BASE_PATH . 'vanzari_detail.php?id=' . $prev_id; ?>" class="car-nav-btn car-nav-btn-prev">
                                    Masina anterioara
                                </a>
                            <?php else: ?>
                                <span class="car-nav-btn car-nav-btn-disabled">
                                    Masina anterioara
                                </span>
                            <?php endif; ?>
                            <?php if (!is_null($next_id)): ?>
                                <a href="<?php echo BASE_PATH . 'vanzari_detail.php?id=' . $next_id; ?>" class="car-nav-btn car-nav-btn-next">
                                    Masina urmatoare
                                </a>
                            <?php else: ?>
                                <span class="car-nav-btn car-nav-btn-disabled">
                                    Masina urmatoare
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Specifications Professional Table -->
                        <div class="car-specs-box">
                            <div class="car-specs-header">
                                <h3 class="car-specs-title">
                                    Specificații Tehnice
                                </h3>
                            </div>
                            <div class="car-specs-content">
                                <table class="car-specs-table specs-table-responsive">
                                    <tbody>
                                        <?php 
                                        // Check if this is a Utilaje (equipment)
                                        $is_utilaje = (!empty($row_vanzare['cat']) && $row_vanzare['cat'] === 'Utilaje');
                                        
                                        $spec_row_count = 0;
                                        if (!empty($row_vanzare['prov_enienta'])) $spec_row_count++;
                                        if ($is_utilaje) {
                                            // Count fields specific to Utilaje - only count if not null and not empty
                                            if (!empty($car_year) && $car_year >= 1900 && $car_year <= (int)date('Y')) $spec_row_count++;
                                            if (isset($row_vanzare['subcateg']) && $row_vanzare['subcateg'] !== null && $row_vanzare['subcateg'] !== '') $spec_row_count++;
                                            if ($row_for_csv && isset($row_for_csv['combustibil']) && $row_for_csv['combustibil'] !== null && $row_for_csv['combustibil'] !== '' && $row_for_csv['combustibil'] != 'fara') $spec_row_count++;
                                            if ((!empty($parsed_details['enginePowerHP']) || ($row_for_csv && isset($row_for_csv['putere_motor']) && $row_for_csv['putere_motor'] !== null && $row_for_csv['putere_motor'] !== '' && is_numeric($row_for_csv['putere_motor'])))) $spec_row_count++;
                                            // Count tonaj only if it has a valid value (not null, not empty, not 'NULL' string)
                                            $tonaj_val = isset($row_for_csv['tonaj']) ? trim($row_for_csv['tonaj']) : '';
                                            if ($row_for_csv && !empty($tonaj_val) && strtoupper($tonaj_val) !== 'NULL') $spec_row_count++;
                                            if ($row_for_csv && isset($row_for_csv['tip_deplasare']) && $row_for_csv['tip_deplasare'] !== null && $row_for_csv['tip_deplasare'] !== '') $spec_row_count++;
                                            if ($row_for_csv && isset($row_for_csv['tractiune']) && $row_for_csv['tractiune'] !== null && $row_for_csv['tractiune'] !== '') $spec_row_count++;
                                            if ($row_for_csv && isset($row_for_csv['kilometraj']) && $row_for_csv['kilometraj'] !== null && $row_for_csv['kilometraj'] !== '' && is_numeric($row_for_csv['kilometraj'])) $spec_row_count++;
                                        } else {
                                            // Count fields specific to vehicles
                                            if (!empty($car_year) && $car_year >= 1900 && $car_year <= (int)date('Y')) $spec_row_count++;
                                            if ($row_for_csv && !empty($row_for_csv['cilindri']) && is_numeric($row_for_csv['cilindri'])) $spec_row_count++;
                                            if ((!empty($parsed_details['enginePowerHP']) || ($row_for_csv && !empty($row_for_csv['putere_motor']) && is_numeric($row_for_csv['putere_motor'])))) $spec_row_count++;
                                            if ($row_for_csv && !empty($row_for_csv['norma_euro'])) $spec_row_count++;
                                            if (!empty($row_vanzare['combustibil']) && $row_vanzare['combustibil'] != 'fara') $spec_row_count++;
                                            if ($row_for_csv && !empty($row_for_csv['transmisie'])) $spec_row_count++;
                                            if ($row_for_csv && !empty($row_for_csv['tractiune'])) $spec_row_count++;
                                            if ($row_for_csv && !empty($row_for_csv['nr_usi']) && is_numeric($row_for_csv['nr_usi'])) $spec_row_count++;
                                            if ($row_for_csv && !empty($row_for_csv['nr_locuri']) && is_numeric($row_for_csv['nr_locuri'])) $spec_row_count++;
                                            if ($row_for_csv && !empty($row_for_csv['culoarea'])) $spec_row_count++;
                                            if ($row_for_csv && !empty($row_for_csv['caroserie'])) $spec_row_count++;
                                            if ($row_for_csv && !empty($row_for_csv['inmatriculata'])) $spec_row_count++;
                                            if ($row_for_csv && !empty($row_for_csv['varianta'])) $spec_row_count++;
                                        }
                                        
                                        $current_row = 0;
                                        ?>
                                        <?php if (!empty($row_vanzare['prov_enienta'])): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Proveniență</td>
                                                <td class="car-spec-value"><?php echo htmlspecialchars($row_vanzare['prov_enienta']); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($car_year) && $car_year >= 1900 && $car_year <= (int)date('Y')): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">An fabricație</td>
                                                <td class="car-spec-value"><?php echo $car_year; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($is_utilaje && isset($row_vanzare['subcateg']) && $row_vanzare['subcateg'] !== null && $row_vanzare['subcateg'] !== ''): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Tip</td>
                                                <td class="car-spec-value"><?php echo htmlspecialchars($row_vanzare['subcateg']); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($is_utilaje && $row_for_csv && isset($row_for_csv['combustibil']) && $row_for_csv['combustibil'] !== null && $row_for_csv['combustibil'] !== '' && $row_for_csv['combustibil'] != 'fara'): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Motor</td>
                                                <td class="car-spec-value"><?php echo htmlspecialchars(ucfirst($row_for_csv['combustibil'])); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php 
                                        // Check tonaj - must be not null, not empty, and not the string 'NULL'
                                        $tonaj_value = isset($row_for_csv['tonaj']) ? trim($row_for_csv['tonaj']) : '';
                                        $has_tonaj = $is_utilaje && $row_for_csv && !empty($tonaj_value) && strtoupper($tonaj_value) !== 'NULL';
                                        if ($has_tonaj): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Tonaj</td>
                                                <td class="car-spec-value"><?php echo htmlspecialchars($row_for_csv['tonaj']); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($is_utilaje && $row_for_csv && isset($row_for_csv['tip_deplasare']) && $row_for_csv['tip_deplasare'] !== null && $row_for_csv['tip_deplasare'] !== ''): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Tip deplasare</td>
                                                <td class="car-spec-value"><?php echo htmlspecialchars($row_for_csv['tip_deplasare']); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($row_for_csv && !empty($row_for_csv['cilindri']) && is_numeric($row_for_csv['cilindri']) && !$is_utilaje): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Motor</td>
                                                <td class="car-spec-value"><?php echo number_format($row_for_csv['cilindri'] / 1000, 1, ',', '.'); ?> L</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php 
                                        // Putere: prefer parsed from text (CP + kW), fallback to for_csv
                                        $spec_cp = !empty($parsed_details['enginePowerHP']) ? (int)$parsed_details['enginePowerHP'] : null;
                                        $spec_kw = !empty($parsed_details['enginePowerKW']) ? (int)$parsed_details['enginePowerKW'] : null;
                                        if (empty($spec_cp) && $row_for_csv && isset($row_for_csv['putere_motor']) && is_numeric($row_for_csv['putere_motor'])) {
                                            $spec_cp = (int)$row_for_csv['putere_motor'];
                                            if (empty($spec_kw)) $spec_kw = (int)round($row_for_csv['putere_motor'] * 0.745699872);
                                        }
                                        if (!empty($spec_cp)): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Putere</td>
                                                <td class="car-spec-value">
                                                    <?php 
                                                    if ($is_utilaje) {
                                                        echo $spec_cp . ' CP';
                                                    } else {
                                                        echo $spec_cp . ' CP' . (!empty($spec_kw) ? ' (' . $spec_kw . ' kW)' : '');
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($row_for_csv && isset($row_for_csv['kilometraj']) && $row_for_csv['kilometraj'] !== null && $row_for_csv['kilometraj'] !== '' && is_numeric($row_for_csv['kilometraj'])): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <?php if ($is_utilaje): ?>
                                                    <td class="car-spec-label">Ore de funcționare</td>
                                                <?php else: ?>
                                                    <td class="car-spec-label">Km parcursi</td>
                                                <?php endif; ?>
                                                <td class="car-spec-value"><?php echo number_format($row_for_csv['kilometraj'], 0, ',', '.'); ?> <?php if ($is_utilaje): ?>ore<?php else: ?>km<?php endif; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($row_for_csv && !empty($row_for_csv['norma_euro'])): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Norma Euro</td>
                                                <td class="car-spec-value">Euro <?php echo htmlspecialchars($row_for_csv['norma_euro']); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($row_vanzare['combustibil']) && $row_vanzare['combustibil'] != 'fara'): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Combustibil</td>
                                                <td class="car-spec-value"><?php echo htmlspecialchars(ucfirst($row_vanzare['combustibil'])); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($row_for_csv && !empty($row_for_csv['transmisie'])): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Transmisie</td>
                                                <td class="car-spec-value"><?php echo htmlspecialchars(ucfirst($row_for_csv['transmisie'])); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($row_for_csv && isset($row_for_csv['tractiune']) && $row_for_csv['tractiune'] !== null && $row_for_csv['tractiune'] !== ''): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Tracțiune</td>
                                                <td class="car-spec-value"><?php echo htmlspecialchars(ucfirst($row_for_csv['tractiune'])); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!$is_utilaje && $row_for_csv && !empty($row_for_csv['nr_usi']) && is_numeric($row_for_csv['nr_usi'])): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Număr uși</td>
                                                <td class="car-spec-value"><?php echo $row_for_csv['nr_usi']; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!$is_utilaje && $row_for_csv && !empty($row_for_csv['nr_locuri']) && is_numeric($row_for_csv['nr_locuri'])): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Număr locuri</td>
                                                <td class="car-spec-value"><?php echo $row_for_csv['nr_locuri']; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!$is_utilaje && $row_for_csv && !empty($row_for_csv['culoarea'])): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Culoare</td>
                                                <td class="car-spec-value"><?php echo htmlspecialchars(ucfirst($row_for_csv['culoarea'])); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!$is_utilaje && $row_for_csv && !empty($row_for_csv['caroserie'])): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Caroserie</td>
                                                <td class="car-spec-value"><?php echo htmlspecialchars(ucfirst($row_for_csv['caroserie'])); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!$is_utilaje && $row_for_csv && !empty($row_for_csv['inmatriculata'])): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Înmatriculată</td>
                                                <td class="car-spec-value"><?php echo htmlspecialchars(ucfirst($row_for_csv['inmatriculata'])); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!$is_utilaje && $row_for_csv && !empty($row_for_csv['varianta'])): ?>
                                            <?php $current_row++; $bg_class = ($current_row % 2 == 0) ? 'car-spec-row-even' : 'car-spec-row-odd'; ?>
                                            <tr class="car-spec-row <?php echo $bg_class; ?>">
                                                <td class="car-spec-label">Variantă</td>
                                                <td class="car-spec-value"><?php echo htmlspecialchars($row_for_csv['varianta']); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="clearfix"></div>
        </div>
    </div>
    <!--container ends-->
</section>
<!--content ends-->


<script>
// Move sidebar to mobile placeholder on small screens
function reorganizeLayoutForMobile() {
    var sidebar = document.querySelector('.right-content');
    var placeholder = document.querySelector('.mobile-sidebar-placeholder');
    
    if (window.innerWidth <= 767) {
        if (sidebar && placeholder) {
            // Clear placeholder first
            placeholder.innerHTML = '';
            
            // Clone sidebar content and insert into placeholder
            var sidebarClone = sidebar.cloneNode(true);
            sidebarClone.classList.remove('right-content');
            sidebarClone.classList.add('mobile-sidebar-clone');
            sidebarClone.style.display = 'block';
            sidebarClone.style.width = '100%';
            sidebarClone.style.paddingLeft = '0';
            sidebarClone.style.paddingRight = '0';
            sidebarClone.style.marginBottom = '20px';
            placeholder.appendChild(sidebarClone);
        }
    } else {
        // On desktop, clear placeholder
        if (placeholder) {
            placeholder.innerHTML = '';
        }
    }
}

// Run on page load - with delay to ensure DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(reorganizeLayoutForMobile, 100);
    });
} else {
    setTimeout(reorganizeLayoutForMobile, 100);
}

// Run on window resize with debounce
var resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(reorganizeLayoutForMobile, 250);
});

// Fancybox 3.x has native touch support - minimal enhancements only
if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function($) {
        // Handle select placeholder styling - update style when user selects a value
        $('#informatii select[required]').on('change', function() {
            var $select = $(this);
            if ($select.val() === '' || $select.val() === null) {
                $select.addClass('placeholder-selected');
            } else {
                $select.removeClass('placeholder-selected');
            }
        });
        
        // Initialize placeholder style on page load
        $('#informatii select[required]').each(function() {
            var $select = $(this);
            if ($select.val() === '' || $select.val() === null) {
                $select.addClass('placeholder-selected');
            }
        });
    });
}
</script>

<?php
// get footer template part
include './themes/automotive/template_parts/footer.php';


