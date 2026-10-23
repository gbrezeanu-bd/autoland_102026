<?php
/**
 * Catalogul de mașini (vanzari.php): filtre, sortare, interogări, numărători pentru filtre și adrese.
 *
 * Parametrii din adresă (toți opționali): txtsrc, users (categorie), marca, combustibil, caroserie, cutie,
 * an (an minim), pret (preț maxim), km (kilometraj maxim), prov_enienta, sort, pageNum_vanzare.
 * Valorile vechi "fara", "%" și "14000000" (preț), respectiv "1995" (an), înseamnă "fără filtru".
 */
require_once __DIR__ . '/calculator_rata_config.php';

define('VZ_PER_PAGE', 12);
define('VZ_DEFAULT_SORT', 'recomandate');
// Vederea de ansamblu (fără filtre): câte mașini se arată din fiecare categorie
define('VZ_OVERVIEW_PER_CATEGORY', 4);

/**
 * Pagina pe care duc adresele generate (vanzari.php; lista_oferta.php o schimbă prin $vz_base_page).
 */
function vz_base()
{
    return isset($GLOBALS['vz_base_page']) ? $GLOBALS['vz_base_page'] : 'vanzari.php';
}

function vz_categories()
{
    return array('Autoturisme', 'Autoutilitare', 'Autospecializate');
}

function vz_per_page_options()
{
    return array(12, 24, 48);
}

function vz_sort_options()
{
    return array(
        'recomandate' => 'Recomandate',
        'pret_asc'    => 'Preț crescător',
        'pret_desc'   => 'Preț descrescător',
        'an_desc'     => 'An: cele mai noi',
        'km_asc'      => 'Km: cei mai puțini',
    );
}

/**
 * Niciun filtru activ (de exemplu pentru lista completă de mărci din formular).
 */
function vz_blank_filters()
{
    return array('txtsrc' => '', 'users' => '', 'marca' => '', 'combustibil' => '', 'caroserie' => '', 'cutie' => '', 'an' => 0, 'pret' => 0, 'km' => 0,
        'prov_enienta' => '', 'sort' => VZ_DEFAULT_SORT, 'pp' => VZ_PER_PAGE, 'oferta' => 0);
}

function vz_param($key)
{
    return (isset($_GET[$key]) && is_string($_GET[$key])) ? trim($_GET[$key]) : '';
}

function vz_text_param($key)
{
    $v = vz_param($key);
    return in_array($v, array('fara', '%'), true) ? '' : $v;
}

/**
 * Filtrele curente, validate. Valorile goale înseamnă "fără filtru".
 */
function vz_filters()
{
    $f = array(
        'txtsrc'       => vz_param('txtsrc'),
        'users'        => in_array(vz_param('users'), vz_categories(), true) ? vz_param('users') : '',
        'marca'        => vz_text_param('marca'),
        'combustibil'  => vz_text_param('combustibil'),
        'caroserie'    => vz_text_param('caroserie'),
        'cutie'        => in_array(vz_param('cutie'), array('automata', 'manuala'), true) ? vz_param('cutie') : '',
        'an'           => (int)vz_param('an'),
        'pret'         => (int)vz_param('pret'),
        'km'           => (int)vz_param('km'),
        'prov_enienta' => in_array(vz_param('prov_enienta'), array('Romania', 'Import'), true) ? vz_param('prov_enienta') : '',
        'pp'           => in_array((int)vz_param('pp'), vz_per_page_options(), true) ? (int)vz_param('pp') : VZ_PER_PAGE,
        'sort'         => isset($_GET['sort']) && is_string($_GET['sort']) && array_key_exists($_GET['sort'], vz_sort_options()) ? $_GET['sort'] : VZ_DEFAULT_SORT,
    );
    if ($f['an'] <= 1995) { $f['an'] = 0; }
    $f['oferta'] = 0; // doar lista_oferta.php îl pornește
    if ($f['pret'] <= 0 || $f['pret'] >= 14000000) { $f['pret'] = 0; }
    if ($f['km'] <= 0) { $f['km'] = 0; }
    return $f;
}

/**
 * Condițiile WHERE. $skip = filtrul ignorat (pentru numărătorile din lista aceluiași filtru).
 */
function vz_where($f, $skip = '')
{
    global $connection;
    $esc = function ($v) use ($connection) { return $connection->real_escape_string($v); };
    $w = array('m.stare_anunt = 1', "COALESCE(m.cat,'') NOT LIKE 'Utilaje%'");

    if (!empty($f['oferta'])) { $w[] = 'm.oferta_speciala = 1'; }
    if ($skip !== 'users' && $f['users'] !== '') { $w[] = "m.cat = '" . $esc($f['users']) . "'"; }
    if ($skip !== 'marca' && $f['marca'] !== '') { $w[] = "m.marca = '" . $esc($f['marca']) . "'"; }
    if ($skip !== 'combustibil' && $f['combustibil'] !== '') { $w[] = "m.combustibil = '" . $esc($f['combustibil']) . "'"; }
    if ($skip !== 'caroserie' && $f['caroserie'] !== '') { $w[] = "f.caroserie = '" . $esc($f['caroserie']) . "'"; }
    if ($skip !== 'cutie' && $f['cutie'] !== '') { $w[] = "f.transmisie LIKE '" . ($f['cutie'] === 'automata' ? 'auto' : 'manual') . "%'"; }
    if ($skip !== 'prov_enienta' && $f['prov_enienta'] !== '') { $w[] = "m.prov_enienta = '" . $esc($f['prov_enienta']) . "'"; }
    if ($f['an'] > 0) { $w[] = 'm.an >= ' . (int)$f['an']; }
    if ($f['pret'] > 0) { $w[] = 'm.pret <= ' . (int)$f['pret']; }
    if ($f['km'] > 0) { $w[] = 'f.kilometraj <= ' . (int)$f['km']; }

    if ($f['txtsrc'] !== '') {
        if (strncmp($f['txtsrc'], '#S10', 4) === 0) {
            // Codul afișat pe anunț (#S10 + id)
            $w[] = 'm.id = ' . (int)substr($f['txtsrc'], 4, 10);
        } else {
            // Cel mult două cuvinte, fiecare căutat în marcă sau în model
            $words = array_slice(preg_split('/\s+/', $f['txtsrc'], -1, PREG_SPLIT_NO_EMPTY), 0, 2);
            foreach ($words as $word) {
                $word = $esc($word);
                $w[] = "(m.marca LIKE '%" . $word . "%' OR m.tip LIKE '%" . $word . "%')";
            }
        }
    }
    return implode(' AND ', $w);
}

function vz_order_by($sort)
{
    switch ($sort) {
        case 'pret_asc':  return 'm.pret ASC, m.id DESC';
        case 'pret_desc': return 'm.pret DESC, m.id DESC';
        case 'an_desc':   return 'm.an DESC, m.id DESC';
        case 'km_asc':    return '(f.kilometraj IS NULL OR f.kilometraj = 0), f.kilometraj ASC, m.id DESC';
        default:          return 'm.oferta_speciala DESC, m.id DESC';
    }
}

function vz_from()
{
    return 'FROM masini m LEFT JOIN for_csv f ON f.id = m.id';
}

function vz_count($f)
{
    global $connection;
    $r = $connection->query('SELECT COUNT(*) ' . vz_from() . ' WHERE ' . vz_where($f));
    return $r ? (int)$r->fetch_row()[0] : 0;
}

function vz_fetch_page($f, $offset, $limit)
{
    global $connection;
    $sql = 'SELECT m.id, m.marca, m.tip, m.cat, m.combustibil, m.an, m.pret, m.pret_vechi, m.oferta_speciala, m.prov_enienta,'
        . ' f.kilometraj AS km, f.transmisie AS cutie, f.putere_motor AS cp, f.caroserie AS caroserie, f.cilindri, f.norma_euro AS euro, f.tractiune, f.nr_locuri AS locuri '
        . vz_from() . ' WHERE ' . vz_where($f)
        . ' ORDER BY ' . vz_order_by($f['sort'])
        . ' LIMIT ' . (int)$offset . ', ' . (int)$limit;
    return $connection->query($sql);
}

/**
 * Valorile disponibile pentru un filtru, cu numărul de mașini, ținând cont de celelalte filtre active.
 * Returnează [valoare => număr], sortat după număr (sau alfabetic pentru $alpha).
 */
function vz_facet($f, $key, $alpha = false)
{
    global $connection;
    $cols = array(
        'users'        => 'm.cat',
        'marca'        => 'm.marca',
        'combustibil'  => 'm.combustibil',
        'caroserie'    => 'f.caroserie',
        'prov_enienta' => 'm.prov_enienta',
    );
    $out = array();
    if ($key === 'cutie') {
        $sql = "SELECT CASE WHEN f.transmisie LIKE 'auto%' THEN 'automata' WHEN f.transmisie LIKE 'manual%' THEN 'manuala' END AS v, COUNT(*) AS c "
            . vz_from() . ' WHERE ' . vz_where($f, 'cutie') . ' GROUP BY v';
    } else {
        $col = $cols[$key];
        $sql = 'SELECT ' . $col . ' AS v, COUNT(*) AS c ' . vz_from() . ' WHERE ' . vz_where($f, $key)
            . " AND $col IS NOT NULL AND $col <> '' GROUP BY v ORDER BY " . ($alpha ? 'v ASC' : 'c DESC');
    }
    $r = $connection->query($sql);
    if ($r) {
        while ($row = $r->fetch_assoc()) {
            if ($row['v'] !== null && $row['v'] !== '') { $out[$row['v']] = (int)$row['c']; }
        }
    }
    return $out;
}

/**
 * Adresa paginii cu filtrele curente, modificate prin $over (cheie => valoare nouă; '' sau 0 = șterge filtrul).
 * Sortarea implicită și pagina 0 nu apar în adresă.
 */
function vz_url($f, $over = array(), $page = 0)
{
    $p = array_merge($f, $over);
    $order = array('txtsrc', 'users', 'marca', 'combustibil', 'caroserie', 'cutie', 'an', 'pret', 'km', 'prov_enienta', 'sort', 'pp');
    $q = array();
    foreach ($order as $k) {
        if (!isset($p[$k]) || $p[$k] === '' || $p[$k] === 0) { continue; }
        if ($k === 'sort' && $p[$k] === VZ_DEFAULT_SORT) { continue; }
        if ($k === 'pp' && $p[$k] === VZ_PER_PAGE) { continue; }
        $q[$k] = $p[$k];
    }
    if ($page > 0) { $q['pageNum_vanzare'] = $page; }
    return BASE_PATH . vz_base() . ($q ? '?' . http_build_query($q, '', '&amp;') : '');
}

/**
 * Filtrele active, ca listă de [etichetă, cheie] pentru butoanele care le elimină.
 */
function vz_active_chips($f)
{
    $chips = array();
    if ($f['txtsrc'] !== '') { $chips[] = array('„' . $f['txtsrc'] . '”', 'txtsrc'); }
    if ($f['users'] !== '') { $chips[] = array($f['users'], 'users'); }
    if ($f['marca'] !== '') { $chips[] = array($f['marca'], 'marca'); }
    if ($f['caroserie'] !== '') { $chips[] = array(vz_label_caroserie($f['caroserie']), 'caroserie'); }
    if ($f['combustibil'] !== '') { $chips[] = array(ucfirst($f['combustibil']), 'combustibil'); }
    if ($f['cutie'] !== '') { $chips[] = array($f['cutie'] === 'automata' ? 'Cutie automată' : 'Cutie manuală', 'cutie'); }
    if ($f['an'] > 0) { $chips[] = array('din ' . $f['an'], 'an'); }
    if ($f['pret'] > 0) { $chips[] = array('sub ' . number_format($f['pret'], 0, ',', '.') . ' €', 'pret'); }
    if ($f['km'] > 0) { $chips[] = array('sub ' . number_format($f['km'], 0, ',', '.') . ' km', 'km'); }
    if ($f['prov_enienta'] !== '') { $chips[] = array($f['prov_enienta'] === 'Import' ? 'Import' : 'România', 'prov_enienta'); }
    return $chips;
}

function vz_label_caroserie($v)
{
    $v = trim($v);
    return mb_strlen($v, 'UTF-8') <= 3 ? mb_strtoupper($v, 'UTF-8') : mb_strtoupper(mb_substr($v, 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($v, 1, null, 'UTF-8'), 'UTF-8');
}

/**
 * Numele afișat pe card: fără textele de promovare din titlu (", leasing, rate, garanție...").
 */
function vz_car_name($row)
{
    $tip = preg_replace('/\s*[,\-–]?\s*\b(leasing|rate|avans)\b.*$/iu', '', $row['tip']);
    $tip = trim($tip, " \t,-–");
    return trim($row['marca'] . ' ' . ($tip !== '' ? $tip : $row['tip']));
}

/**
 * Rata lunară estimată (cu TVA), cu setările implicite ale calculatorului de rată.
 */
function vz_monthly_rate($pret)
{
    global $CALCULATOR_RATA;
    if ($pret <= 0) { return 0; }
    $r = calculator_rata_calculeaza((float)$pret, $CALCULATOR_RATA['avans_implicit'], $CALCULATOR_RATA['luni_implicit']);
    return (int)round($r['rata']);
}

/**
 * Pagini de afișat în paginare: primele și ultimele două și vecinii paginii curente (±2); null = "…".
 */
function vz_page_window($current, $last)
{
    $pages = array();
    for ($i = 0; $i <= $last; $i++) {
        if ($i <= 1 || $i >= $last - 1 || abs($i - $current) <= 2) { $pages[] = $i; }
        elseif (end($pages) !== null) { $pages[] = null; }
    }
    return $pages;
}
