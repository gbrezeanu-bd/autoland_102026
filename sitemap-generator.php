<?php
/**
 * XML Sitemap Generator
 * Rulează doar din cron (linia de comandă), o dată pe zi, și rescrie sitemap.xml în UTF-8, fără BOM.
 * Din browser răspunde 404.
 *
 * Cron (cPanel → Cron Jobs), zilnic la 04:00:
 *   0 4 * * * /usr/local/bin/php /home/autoland/public_html/sitemap-generator.php >/dev/null 2>&1
 *
 * lastmod pentru mașini vine din masini.data_actualizare, completată automat de MySQL la fiecare
 * modificare a anunțului (ALTER TABLE masini ADD COLUMN data_actualizare TIMESTAMP NOT NULL
 * DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP).
 */
// Pe server, `php` din cron e varianta CGI (nu CLI), deci verificăm dacă e o cerere web,
// nu numele SAPI: o cerere din browser are mereu REQUEST_METHOD, rularea din cron nu.
if (isset($_SERVER['REQUEST_METHOD'])) {
    http_response_code(404);
    exit();
}
chdir(__DIR__); // include-urile relative funcționează și din cron

include_once './includes/config.php';

$base_url     = 'https://autolandromania.ro';
$sitemap_file = __DIR__ . '/sitemap.xml';
$backup_dir   = __DIR__ . '/_backups';

// Acumulăm TOT în $xml (nu echo) ca să scriem un singur fișier UTF-8.
$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

$count = 0;

// helper: adaugă un <url>. $lastmod poate fi null (atunci se omite).
// Fără changefreq/priority: Google le ignoră.
$add_url = function ($loc, $lastmod = null) use (&$xml, &$count) {
    $xml .= '  <url>' . PHP_EOL;
    $xml .= '    <loc>' . htmlspecialchars($loc, ENT_XML1) . '</loc>' . PHP_EOL;
    if ($lastmod) {
        $xml .= '    <lastmod>' . date('Y-m-d', strtotime($lastmod)) . '</lastmod>' . PHP_EOL;
    }
    $xml .= '  </url>' . PHP_EOL;
    $count++;
};

// Data ultimei modificări a unei mașini active (din tot stocul sau dintr-o categorie)
$latest_change = function ($where = '') use ($connection) {
    $res = $connection->query("SELECT MAX(data_actualizare) FROM masini WHERE stare_anunt = 1" . ($where ? " AND $where" : ''));
    $row = $res ? $res->fetch_row() : null;
    return $row ? $row[0] : null;
};
$stock_lastmod = $latest_change();

// --- Pagini statice (fără formulare) ---
$static_pages = [
    '/'                       => $stock_lastmod, // homepage-ul afișează stocul
    '/lista_oferta.php'       => $latest_change('oferta_speciala = 1'),
    '/leasing.php'            => null,
    '/contact.php'            => null,
    '/impresii.php'           => null,
    '/garantie.php'           => null,
    '/asistenta-rutiera.php'  => null,
    '/inmatriculari.php'      => null,
    '/articole/'              => null,
];
foreach ($static_pages as $url => $lastmod) {
    $add_url($base_url . $url, $lastmod);
}

// --- Pagini de categorie (exact URL-urile din meniu) ---
$categories = [
    'vanzari.php'                                               => '',
    'vanzari.php?users=Autoturisme&subcateg=3&marcat=10'        => "cat = 'Autoturisme'",
    'vanzari.php?users=Autoutilitare&subcateg=12&marcat=100'    => "cat = 'Autoutilitare'",
    'vanzari.php?users=Autospecializate&subcateg=33&marcat=102' => "cat = 'Autospecializate'",
    'vanzari.php?users=Utilaje'                                 => "cat = 'Utilaje'",
    'vanzari.php?users=Utilaje&subcateg=Constructii'            => "cat = 'Utilaje' AND subcateg = 'Constructii'",
    'vanzari.php?users=Utilaje&subcateg=Agricole'               => "cat = 'Utilaje' AND subcateg = 'Agricole'",
    'vanzari.php?users=Utilaje&subcateg=Municipale'             => "cat = 'Utilaje' AND subcateg = 'Municipale'",
    'vanzari.php?prov_enienta=Import'                           => "prov_enienta = 'Import'",
    'vanzari.php?prov_enienta=Romania'                          => "prov_enienta = 'Romania'",
];
foreach ($categories as $url => $where) {
    $lastmod = $latest_change($where);
    // categoriile fără nicio mașină activă nu intră în sitemap
    if ($where === '' || $lastmod) {
        $add_url($base_url . '/' . $url, $lastmod);
    }
}

// --- Mașini active, cele mai noi primele ---
$res = $connection->query("SELECT id, data_actualizare FROM masini WHERE stare_anunt = 1 ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $add_url($base_url . '/vanzari_detail.php?id=' . (int)$row['id'], $row['data_actualizare']);
    }
}

// --- Articole publicate ---
$res = $connection->query("SELECT slug, data_publicarii, data_actualizarii FROM articole WHERE status = 'published' ORDER BY data_publicarii DESC");
if ($res) {
    while ($a = $res->fetch_assoc()) {
        $lastmod = !empty($a['data_actualizarii']) ? $a['data_actualizarii'] : $a['data_publicarii'];
        $add_url($base_url . '/articole/' . rawurlencode($a['slug']), $lastmod);
    }
}

$xml .= '</urlset>' . PHP_EOL;

// --- Backup al sitemap-ului existent înainte de suprascriere (păstrăm ultimele 10) ---
$backup_msg = '';
if (is_file($sitemap_file)) {
    if (!is_dir($backup_dir)) {
        @mkdir($backup_dir, 0755, true);
    }
    if (is_dir($backup_dir) && is_writable($backup_dir)) {
        $backup_path = $backup_dir . '/sitemap.' . date('Ymd-His') . '.xml';
        $backup_msg = @copy($sitemap_file, $backup_path)
            ? 'Backup salvat: ' . $backup_path
            : 'ATENTIE: nu am putut copia backup-ul in ' . $backup_dir;

        $old = glob($backup_dir . '/sitemap.*.xml');
        if ($old && count($old) > 10) {
            usort($old, function ($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            foreach (array_slice($old, 0, count($old) - 10) as $f) {
                @unlink($f);
            }
        }
    } else {
        $backup_msg = 'ATENTIE: folderul de backup nu exista sau nu e writable: ' . $backup_dir;
    }
}

// Nu suprascriem un sitemap bun cu unul gol (de ex. dacă baza de date nu a răspuns)
if ($count < 20) {
    fwrite(STDERR, "EROARE: doar $count URL-uri generate, sitemap.xml NU a fost suprascris.\n");
    exit(1);
}

// --- Scriem fișierul nou (UTF-8, fără BOM) ---
$ok = file_put_contents($sitemap_file, $xml);
if ($ok === false) {
    fwrite(STDERR, "EROARE: nu am putut scrie $sitemap_file. Verifica permisiunile de scriere.\n");
    exit(1);
}
echo "OK: sitemap.xml scris cu $count URL-uri, " . number_format($ok) . " bytes.\n";
echo $backup_msg . "\n";
