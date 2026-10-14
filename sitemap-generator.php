<?php
/**
 * Dynamic XML Sitemap Generator
 * Accesează scriptul (browser sau cron) — scrie singur sitemap.xml în UTF-8, fără BOM.
 * NU mai copia nimic manual.
 */
include_once './includes/config.php';

$base_url    = 'https://autolandromania.ro';
$sitemap_file = __DIR__ . '/sitemap.xml';
$backup_dir   = __DIR__ . '/_backups';
$today       = date('Y-m-d');

// Acumulăm TOT în $xml (nu echo) ca să scriem un singur fișier UTF-8.
$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

$count = 0;

// helper: adaugă un <url>. $lastmod poate fi null (atunci se omite).
$add_url = function ($loc, $changefreq, $priority, $lastmod = null) use (&$xml, &$count) {
    $xml .= '  <url>' . PHP_EOL;
    $xml .= '    <loc>' . htmlspecialchars($loc, ENT_XML1) . '</loc>' . PHP_EOL;
    if ($lastmod) {
        $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
    }
    $xml .= '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
    $xml .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
    $xml .= '  </url>' . PHP_EOL;
    $count++;
};

// --- Pagini statice ---
$static_pages = [
    ['url' => '/',                     'priority' => '1.00', 'changefreq' => 'weekly'],
    ['url' => '/lista_oferta.php',     'priority' => '0.99', 'changefreq' => 'daily'],
    ['url' => '/cerere_oferta.php',    'priority' => '0.99', 'changefreq' => 'weekly'],
    ['url' => '/contact.php',          'priority' => '0.98', 'changefreq' => 'weekly'],
    ['url' => '/impresii.php',         'priority' => '0.98', 'changefreq' => 'weekly'],
    ['url' => '/leasing.php',          'priority' => '0.98', 'changefreq' => 'weekly'],
    ['url' => '/asistenta-rutiera.php','priority' => '0.5',  'changefreq' => 'weekly'],
    ['url' => '/garantie.php',         'priority' => '0.5',  'changefreq' => 'weekly'],
    ['url' => '/inmatriculari.php',    'priority' => '0.5',  'changefreq' => 'monthly'],
];
foreach ($static_pages as $p) {
    $add_url($base_url . $p['url'], $p['changefreq'], $p['priority']);
}

// --- Mașini ---
if (isset($connection)) {
    // Priority 1: oferte speciale
    $special_ids = [];
    $res = $connection->query("SELECT id FROM masini WHERE stare_anunt = '1' AND oferta_speciala = 1 ORDER BY id DESC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $special_ids[] = (int) $row['id'];
            $add_url($base_url . '/vanzari_detail.php?id=' . $row['id'], 'daily', '0.97', $today);
        }
    }

    // Priority 2: cele mai recente 50 (fără ofertele speciale)
    $recent_ids = [];
    $excl = $special_ids ? ' AND id NOT IN (' . implode(',', $special_ids) . ')' : '';
    $res = $connection->query("SELECT id FROM masini WHERE stare_anunt = '1'{$excl} ORDER BY id DESC LIMIT 50");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $recent_ids[] = (int) $row['id'];
            $add_url($base_url . '/vanzari_detail.php?id=' . $row['id'], 'daily', '0.95', $today);
        }
    }

    // Priority 3: restul mașinilor active
    $exclude = array_merge($special_ids, $recent_ids);
    $excl    = $exclude ? ' AND id NOT IN (' . implode(',', $exclude) . ')' : '';
    $res = $connection->query("SELECT id FROM masini WHERE stare_anunt = '1'{$excl} ORDER BY id DESC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            // fără lastmod inventat — îl omitem când nu avem o dată reală de modificare
            $add_url($base_url . '/vanzari_detail.php?id=' . $row['id'], 'weekly', '0.92');
        }
    }
}

// --- Pagini de categorie ---
$categories = [
    ['url' => 'vanzari.php?prov_enienta=Import',                               'priority' => '0.97', 'changefreq' => 'daily'],
    ['url' => 'vanzari.php?prov_enienta=Romania',                              'priority' => '0.96', 'changefreq' => 'daily'],
    ['url' => 'vanzari.php?users=Autoturisme&subcateg=3&marcat=10',            'priority' => '0.98', 'changefreq' => 'daily'],
    ['url' => 'vanzari.php?users=Autoutilitare&subcateg=12&marcat=100',        'priority' => '0.98', 'changefreq' => 'daily'],
    ['url' => 'vanzari.php?users=Autospecializate&subcateg=33&marcat=102',     'priority' => '0.98', 'changefreq' => 'daily'],
];
foreach ($categories as $c) {
    $add_url($base_url . '/' . $c['url'], $c['changefreq'], $c['priority']);
}

// --- Articole publicate ---
if (isset($connection)) {
    $res = $connection->query("SELECT slug, data_publicarii, data_actualizarii FROM articole WHERE status = 'published' ORDER BY data_publicarii DESC");
    if ($res) {
        while ($a = $res->fetch_assoc()) {
            $lastmod = !empty($a['data_actualizarii'])
                ? date('Y-m-d', strtotime($a['data_actualizarii']))
                : date('Y-m-d', strtotime($a['data_publicarii']));
            $add_url($base_url . '/articole/' . $a['slug'], 'monthly', '0.8', $lastmod);
        }
    }
}

$xml .= '</urlset>' . PHP_EOL;

// --- Backup automat al sitemap-ului existent inainte de suprascriere ---
$backup_msg = '';
if (is_file($sitemap_file)) {
    if (!is_dir($backup_dir)) {
        @mkdir($backup_dir, 0755, true);
    }
    if (is_dir($backup_dir) && is_writable($backup_dir)) {
        $backup_path = $backup_dir . '/sitemap.' . date('Ymd-His') . '.xml';
        if (@copy($sitemap_file, $backup_path)) {
            $backup_msg = 'Backup salvat: ' . $backup_path;
        } else {
            $backup_msg = 'ATENTIE: nu am putut copia backup-ul in ' . $backup_dir;
        }

        // pastreaza doar ultimele 10 backup-uri
        $old = glob($backup_dir . '/sitemap.*.xml');
        if ($old && count($old) > 10) {
            usort($old, function ($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            $to_delete = array_slice($old, 0, count($old) - 10);
            foreach ($to_delete as $f) {
                @unlink($f);
            }
        }
    } else {
        $backup_msg = 'ATENTIE: folderul de backup nu exista sau nu e writable: ' . $backup_dir;
    }
} else {
    $backup_msg = 'Info: nu exista sitemap.xml anterior, nimic de salvat.';
}

// --- Scriem fisierul nou (UTF-8, fara BOM) ---
$ok = file_put_contents($sitemap_file, $xml);

header('Content-Type: text/plain; charset=utf-8');
if ($ok === false) {
    http_response_code(500);
    echo "EROARE: nu am putut scrie " . $sitemap_file . ". Verifica permisiunile de scriere in webroot.\n";
    echo $backup_msg . "\n";
} else {
    echo "OK: sitemap.xml scris cu " . $count . " URL-uri, " . number_format($ok) . " bytes, UTF-8 fara BOM.\n";
    echo $backup_msg . "\n";
    echo "Verifica: " . $base_url . "/sitemap.xml apoi retrimite-l in Search Console.\n";
}
