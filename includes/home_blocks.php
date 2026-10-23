<?php

/**
 * Blocuri dinamice pentru textele din admin (homepage, Despre noi).
 * În text se scrie un marcaj, iar aici se înlocuiește cu conținut actual din baza de date:
 *   {NR_MASINI}         numărul de vehicule active, rotunjit în jos la zeci (ex. 480)
 *   {ULTIMELE_MASINI}   ultimele 8 oferte adăugate, cu link spre fiecare
 *   {CATEGORII}         plăcuțe cu poză pe categorii (fără număr de vehicule)
 *   {HARTA}             harta Google cu adresa parcului
 *   {CALCULATOR}        calculatorul de rată (model orientativ), cu câmp pentru preț
 *   {DOBANDA} {TAXA_MANAGEMENT} {COMISION_LUNAR} {CHELTUIELI_SERVICII}   valorile din includes/calculator_rata_config.php
 *   {EXEMPLU_CALCUL}    exemplu de calcul (preț 12.765,50 EUR, avans 30%, 60 de luni), cu aceleași formule
 *   {RECENZII}          ultimele 3 păreri ale clienților, din pagina Păreri clienți (tabelul recenzii2)
 * Dacă un marcaj lipsește din text, blocul respectiv nu se afișează.
 *
 * Interogările rulează doar dacă marcajul există în text și sunt simple (LIMIT mic, chei primare).
 */

function home_nr_masini($connection)
{
    $res = $connection->query("SELECT COUNT(*) FROM masini WHERE stare_anunt = 1 AND COALESCE(cat,'') NOT LIKE 'Utilaje%'");
    $row = $res ? $res->fetch_row() : null;
    $n = $row ? (int)$row[0] : 0;
    return $n >= 10 ? (string)(floor($n / 10) * 10) : (string)$n;
}

function home_ultimele_masini($connection, $limit = 8)
{
    $limit = max(1, (int)$limit);
    $res = $connection->query(
        "SELECT m.id, m.marca, m.tip, m.an, m.pret, m.cat, m.combustibil, c.kilometraj
         FROM masini m LEFT JOIN for_csv c ON c.id = m.id
         WHERE m.stare_anunt = 1 AND COALESCE(m.cat,'') NOT LIKE 'Utilaje%' ORDER BY m.id DESC LIMIT " . $limit
    );
    if (!$res) {
        return '';
    }
    $cars = [];
    while ($r = $res->fetch_assoc()) {
        $cars[$r['id']] = $r;
    }
    if (!$cars) {
        return '';
    }

    // prima poză a fiecărei mașini, într-o singură interogare
    $photos = [];
    $res = $connection->query("SELECT oferta, file FROM fotov WHERE oferta IN (" . implode(',', array_map('intval', array_keys($cars))) . ") ORDER BY nota ASC");
    while ($res && ($p = $res->fetch_assoc())) {
        if (!isset($photos[$p['oferta']])) {
            $photos[$p['oferta']] = $p['file'];
        }
    }

    $html = '<div class="ap-cars">';
    foreach ($cars as $id => $c) {
        $is_utilaje = ($c['cat'] === 'Utilaje');
        $tip = clean_car_tip($c['tip']);
        $name = trim(($is_utilaje ? '' : $c['marca'] . ' ') . $tip);
        $specs = [];
        if (!empty($c['an'])) {
            $specs[] = (int)$c['an'];
        }
        if (!empty($c['kilometraj']) && (int)$c['kilometraj'] > 0) {
            $specs[] = number_format((int)$c['kilometraj'], 0, ',', '.') . ' km';
        }
        if (!empty($c['combustibil'])) {
            $specs[] = ucfirst(mb_strtolower($c['combustibil'], 'UTF-8'));
        }
        $photo = isset($photos[$id]) ? $photos[$id] : 'image-not-found.png';
        $html .= '<a class="ap-car" href="' . BASE_PATH . 'vanzari_detail.php?id=' . (int)$id . '">'
            . '<img src="' . BASE_PATH . 'foto/' . htmlspecialchars($photo) . '" alt="' . htmlspecialchars($name . ' ' . $c['an']) . '" loading="lazy" width="300" height="200">'
            . '<span class="ap-car-name">' . htmlspecialchars($name) . '</span>'
            . '<span class="ap-car-specs">' . htmlspecialchars(implode(' · ', $specs)) . '</span>'
            . ($c['pret'] > 0 ? '<span class="ap-car-price">' . number_format((int)$c['pret'], 0, ',', '.') . ' EUR</span>' : '')
            . '</a>';
    }
    return $html . '</div>';
}

function home_recenzii($connection, $limit = 3)
{
    $limit = max(1, (int)$limit);
    $res = $connection->query(
        "SELECT prenume, nume, recenzie, data_recenzie FROM recenzii2 WHERE status = '1' AND CHAR_LENGTH(recenzie) > 80 ORDER BY data_recenzie DESC LIMIT " . $limit
    );
    if (!$res) {
        return '';
    }
    $html = '<div class="ap-reviews">';
    while ($r = $res->fetch_assoc()) {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($r['recenzie'])));
        if (mb_strlen($text, 'UTF-8') > 260) {
            $text = rtrim(mb_substr($text, 0, 260, 'UTF-8')) . '…';
        }
        $autor = trim(trim($r['prenume']) . ' ' . trim($r['nume']));
        $html .= '<blockquote class="ap-review"><p>' . htmlspecialchars($text) . '</p>'
            . '<span class="ap-review-by">' . htmlspecialchars($autor) . '</span></blockquote>';
    }
    return $html . '</div>';
}

/**
 * Categoriile de vehicule, ca plăcuțe cu poză reală (ultima mașină din categorie)
 */
function home_categorii($connection)
{
    $cats = [
        'Autoturisme'      => 'vanzari.php?users=Autoturisme&subcateg=3&marcat=10',
        'Autoutilitare'    => 'vanzari.php?users=Autoutilitare&subcateg=12&marcat=100',
        'Autospecializate' => 'vanzari.php?users=Autospecializate&subcateg=33&marcat=102',
    ];
    $res = $connection->query("SELECT cat, MAX(id) AS last_id FROM masini WHERE stare_anunt = 1 GROUP BY cat");
    $info = [];
    while ($res && ($r = $res->fetch_assoc())) {
        $info[$r['cat']] = $r;
    }
    $ids = [];
    foreach ($cats as $name => $url) {
        if (isset($info[$name])) {
            $ids[] = (int)$info[$name]['last_id'];
        }
    }
    $photos = [];
    if ($ids) {
        $res = $connection->query("SELECT oferta, file FROM fotov WHERE oferta IN (" . implode(',', $ids) . ") ORDER BY nota ASC");
        while ($res && ($p = $res->fetch_assoc())) {
            if (!isset($photos[$p['oferta']])) {
                $photos[$p['oferta']] = $p['file'];
            }
        }
    }
    $html = '<div class="ap-tiles">';
    foreach ($cats as $name => $url) {
        if (!isset($info[$name])) {
            continue;
        }
        $photo = isset($photos[$info[$name]['last_id']]) ? $photos[$info[$name]['last_id']] : 'image-not-found.png';
        $html .= '<a class="ap-tile" href="' . BASE_PATH . htmlspecialchars($url) . '">'
            . '<img src="' . BASE_PATH . 'foto/' . htmlspecialchars($photo) . '" alt="' . htmlspecialchars($name) . ' second hand" loading="lazy" width="300" height="200">'
            . '<span class="ap-tile-text"><span class="ap-tile-name">' . htmlspecialchars($name) . '</span></span></a>';
    }
    return $html . '</div>';
}

/**
 * Harta Google cu adresa parcului auto (încărcată leneș, fără cheie API)
 */
function home_harta()
{
    return '<iframe class="ap-map" title="Harta: Autoland România, Strada Luca Arbore nr. 3, Iași" loading="lazy" referrerpolicy="no-referrer-when-downgrade" '
        . 'src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1356.170548429924!2d27.543852130163575!3d47.1707578635384!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40cae4b8f493025f%3A0x9e0b250521007dad!2sAutoland+Romania!5e0!3m2!1sro!2sro!4v1544009637536"></iframe>';
}

/**
 * Calculatorul de rată (aceleași formule ca pe fișa mașinii), cu preț introdus de vizitator
 */
function home_calculator()
{
    $calc_price = 15000;
    $calc_price_editable = true;
    $calc_cta_url = BASE_PATH . 'cerere_oferta.php';
    $calc_cta_label = 'Cere o ofertă personalizată';
    $calc_wa_base = 'Bună ziua, vreau o ofertă de leasing.';
    ob_start();
    include dirname(__DIR__) . '/themes/automotive/template_parts/calculator_rata.php';
    return ob_get_clean();
}

/**
 * Exemplu de calcul pentru pagina Calculator rată, cu valorile din includes/calculator_rata_config.php
 */
function home_exemplu_calcul()
{
    require_once dirname(__DIR__) . '/includes/calculator_rata_config.php';
    $pret = 12765.5;
    $avans_pct = 30;
    $luni = 60;
    $r = calculator_rata_calculeaza($pret, $avans_pct, $luni);
    $n = function ($x) { return calculator_rata_nr($x, 2) . ' EUR'; };
    $html = '<p><strong>Mașină cu prețul afișat de ' . $n($pret) . ', avans ' . $avans_pct . '%, ' . $luni . ' de luni: rata este de ' . $n($r['rata']) . ' pe lună, cu TVA.</strong> Prețul fără TVA este ' . $n($r['pret_net']) . '.</p>';
    $html .= '<div class="ap-table"><table>'
        . '<tr><th></th><th>Fără TVA</th><th>Cu TVA</th></tr>'
        . '<tr><td>Preț</td><td>' . $n($r['pret_net']) . '</td><td>' . $n($r['pret']) . '</td></tr>'
        . '<tr><td>Avans ' . $avans_pct . '%</td><td>' . $n($r['avans_net']) . '</td><td>' . $n($r['avans']) . '</td></tr>'
        . '<tr><td>Total primă plată</td><td>' . $n($r['prima_net']) . '</td><td>' . $n($r['prima']) . '</td></tr>'
        . '<tr><td>Rată lunară (' . $luni . ' de luni)</td><td>' . $n($r['rata_net']) . '</td><td>' . $n($r['rata']) . '</td></tr>'
        . '</table></div>';
    return $html;
}

/**
 * Înlocuiește marcajele din text cu blocurile dinamice (doar pe cele prezente în text)
 */
function home_inlocuieste_marcaje($html, $connection)
{
    if (strpos($html, '{NR_MASINI}') !== false) {
        $html = str_replace('{NR_MASINI}', home_nr_masini($connection), $html);
    }
    if (strpos($html, '{ULTIMELE_MASINI}') !== false) {
        $html = str_replace('{ULTIMELE_MASINI}', home_ultimele_masini($connection), $html);
    }
    if (strpos($html, '{CATEGORII}') !== false) {
        $html = str_replace('{CATEGORII}', home_categorii($connection), $html);
    }
    if (strpos($html, '{EXEMPLU_CALCUL}') !== false) {
        $html = str_replace('{EXEMPLU_CALCUL}', home_exemplu_calcul(), $html);
    }
    if (strpos($html, '{DOBANDA}') !== false || strpos($html, '{TAXA_MANAGEMENT}') !== false || strpos($html, '{COMISION_LUNAR}') !== false || strpos($html, '{CHELTUIELI_SERVICII}') !== false) {
        require_once dirname(__DIR__) . '/includes/calculator_rata_config.php';
        global $CALCULATOR_RATA;
        $html = str_replace(
            ['{DOBANDA}', '{TAXA_MANAGEMENT}', '{COMISION_LUNAR}', '{CHELTUIELI_SERVICII}'],
            [calculator_rata_procent($CALCULATOR_RATA['dobanda']), calculator_rata_procent($CALCULATOR_RATA['taxa_management']),
             calculator_rata_procent($CALCULATOR_RATA['comision_lunar']), calculator_rata_nr($CALCULATOR_RATA['cheltuieli_servicii'], 0)],
            $html
        );
    }
    if (strpos($html, '{CALCULATOR}') !== false) {
        $html = str_replace('{CALCULATOR}', home_calculator(), $html);
    }
    if (strpos($html, '{HARTA}') !== false) {
        $html = str_replace('{HARTA}', home_harta(), $html);
    }
    if (strpos($html, '{RECENZII}') !== false) {
        $html = str_replace('{RECENZII}', home_recenzii($connection), $html);
    }
    return $html;
}

/**
 * Întrebările frecvente se citesc din conținut (blocul #faq: <h3> întrebare + <p> răspuns),
 * ca schema FAQPage să rămână identică cu textul editat în admin.
 */
function pagini_extrage_faq($html)
{
    $faq = [];
    if (stripos($html, 'id="faq"') === false || !class_exists('DOMDocument')) {
        return $faq;
    }
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8"?><body>' . $html . '</body>');
    libxml_clear_errors();
    $block = (new DOMXPath($dom))->query('//*[@id="faq"]')->item(0);
    if (!$block) {
        return $faq;
    }
    $question = null;
    foreach ($block->childNodes as $node) {
        if ($node->nodeName === 'h3') {
            $question = trim($node->textContent);
        } elseif ($node->nodeName === 'p' && $question !== null) {
            $answer = trim($node->textContent);
            if ($question !== '' && $answer !== '') {
                $faq[] = [
                    '@type' => 'Question',
                    'name' => $question,
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer],
                ];
            }
            $question = null;
        }
    }
    return $faq;
}
