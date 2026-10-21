<?php

/**
 * Pagini de conținut din tabelul `pagini` (ex. /finantare/credit-auto/)
 *
 * Conținutul HTML se editează din admin (tehnic/editor_pagini.php), ca la celelalte texte.
 * Adresele vin din .htaccess: /{sectiune}/{slug}/ (sau /{slug}/ la secțiunea 'pagini') -> pagina.php?sectiune=..&slug=..
 *
 * This site requires php version >= 7.0
 */
include_once './includes/config.php';
include_once './includes/json_ld_schemas.php';
include_once './includes/pagini_sectiuni.php';
include_once './includes/home_blocks.php';

$sectiune = isset($_GET['sectiune']) ? trim($_GET['sectiune']) : '';
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (!isset($PAGINI_SECTIUNI[$sectiune]) || !preg_match('/^[a-z0-9\-]+$/', $slug)) {
    header("HTTP/1.0 404 Not Found");
    include('404.php');
    exit;
}

// Paginile nepublicate se văd doar din admin, ca previzualizare
if (!isset($_SESSION)) {
    session_start();
}
$is_admin = !empty($_SESSION['MM_Username']);

$stmt = $connection->prepare("SELECT titlu, h1, meta_title, meta_description, continut, status, data_actualizarii FROM pagini WHERE sectiune = ? AND slug = ? LIMIT 1");
$stmt->bind_param('ss', $sectiune, $slug);
$stmt->execute();
$pagina = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pagina || ($pagina['status'] !== 'published' && !$is_admin)) {
    header("HTTP/1.0 404 Not Found");
    include('404.php');
    exit;
}

$page_title = !empty($pagina['meta_title']) ? $pagina['meta_title'] : $pagina['titlu'];
$page_subtitle = '';
$banner_title = !empty($pagina['h1']) ? $pagina['h1'] : $pagina['titlu'];
$meta_description = $pagina['meta_description'];
$og_type = 'website';
if ($pagina['status'] !== 'published') {
    $robots_meta = 'noindex,nofollow';
}

// Linkurile din conținut sunt scrise cu adresa de producție; local trebuie să rămână pe mediul de test
$content = home_inlocuieste_marcaje(pagini_linkuri_interne((string)$pagina['continut']), $connection);

$canonical_url = BASE_PATH . pagini_cale($sectiune, $slug);

$breadcrumbs = [['name' => 'Acasă', 'url' => BASE_PATH]];
if ($PAGINI_SECTIUNI[$sectiune]['hub'] !== '') {
    $breadcrumbs[] = ['name' => $PAGINI_SECTIUNI[$sectiune]['eticheta'], 'url' => BASE_PATH . $PAGINI_SECTIUNI[$sectiune]['hub']];
}
$breadcrumbs[] = ['name' => $pagina['titlu'], 'url' => $canonical_url];

$schemas = [
    getBreadcrumbSchema($breadcrumbs),
    getOrganizationSchema(),
    getWebPageSchema($canonical_url, $page_title, $meta_description),
];
$faq_items = pagini_extrage_faq($content);
if ($faq_items) {
    $schemas[] = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq_items];
}

$header_scripts = '<link rel="stylesheet" href="' . BASE_PATH . 'themes/automotive/css/pagini.css">' . "\n";
$header_scripts .= '<link rel="canonical" href="' . htmlspecialchars($canonical_url) . '" />' . "\n";
$header_scripts .= getJsonLdString($schemas);

// Calculatorul de rată ({CALCULATOR} în text) are stiluri și script proprii
if (strpos((string)$pagina['continut'], '{CALCULATOR}') !== false) {
    $header_scripts .= '<link rel="stylesheet" href="' . BASE_PATH . 'themes/automotive/css/calculator-rata.css">' . "\n";
    $footer_scripts = (isset($footer_scripts) ? $footer_scripts : '') . "<script type='text/javascript' src='" . BASE_PATH . "themes/automotive/js/calculator-rata.js' defer></script>";
}

// Data ultimei actualizări, în română (ex. 21 octombrie 2026)
$luni_ro = ['', 'ianuarie', 'februarie', 'martie', 'aprilie', 'mai', 'iunie', 'iulie', 'august', 'septembrie', 'octombrie', 'noiembrie', 'decembrie'];
$ts = strtotime($pagina['data_actualizarii']);
$data_afisata = date('j', $ts) . ' ' . $luni_ro[(int)date('n', $ts)] . ' ' . date('Y', $ts);

include './themes/automotive/template_parts/header.php';
?>

<section class="content">
    <div class="container">
        <div class="inner-page">
            <div class="ap-layout">
                <?php include './themes/automotive/template_parts/pagina_sidebar.php'; ?>
                <div class="ap-main">
                    <?php print $content; ?>
                    <p class="ap-updated">Actualizat la <?php echo $data_afisata; ?></p>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>
</section>

<?php
include './themes/automotive/template_parts/footer.php';
