<?php

/**
 * Secțiunile site-ului care folosesc aspectul cu meniu lateral (pagina.php, pagina_sidebar.php).
 *
 * Pentru o secțiune nouă cu pagini din tabelul `pagini`: o adaugi aici, apoi o treci și în regulile
 * "PAGINI DE CONȚINUT" din .htaccess. Paginile ei se adaugă apoi din admin, fără SQL.
 *
 * 'eticheta' = numele din breadcrumb, din admin și din bara laterală
 * 'hub'      = pagina principală a secțiunii (pentru breadcrumb); gol = pagina stă direct sub Acasă
 * 'prefix'   = începutul adresei paginilor din tabelul `pagini`; gol = pagina e la rădăcină (ex. /despre-noi/)
 * 'meniu'    = (opțional) linkurile din bara laterală: [slug => text]; slug gol = pagina principală
 * 'urls'     = (opțional) adresele paginilor care nu sunt în tabelul `pagini` (ex. garantie.php): [slug => adresă]
 */
$PAGINI_SECTIUNI = [
    'finantare' => [
        'eticheta' => 'Finanțare', 'hub' => 'leasing.php', 'prefix' => 'finantare/',
        'meniu' => [
            ''                    => 'Prezentare generală',
            'credit-auto'         => 'Credit auto în rate',
            'leasing-financiar'   => 'Leasing financiar',
            'leasing-operational' => 'Leasing operațional',
            'avans-minim'         => 'Avans minim',
            'acte-necesare'       => 'Acte necesare',
            'calculator-rate'     => 'Calculator rată',
        ],
    ],
    'garantie'  => ['eticheta' => 'Garanție', 'hub' => 'garantie.php', 'prefix' => 'garantie/'],
    // Paginile de servicii care au text propriu în tabelul `texte` (editoare separate în admin)
    'servicii'  => [
        'eticheta' => 'Garanție și servicii', 'hub' => '', 'prefix' => 'servicii/',
        'meniu' => [
            'garantie'          => 'Garanție',
            'inmatriculari'     => 'Înmatriculări și acte',
            'asistenta-rutiera' => 'Asistență rutieră',
        ],
        'urls' => [
            'garantie'          => 'garantie.php',
            'inmatriculari'     => 'inmatriculari.php',
            'asistenta-rutiera' => 'asistenta-rutiera.php',
        ],
    ],
    // Pagini la rădăcină (/despre-noi/ etc.)
    'pagini'    => [
        'eticheta' => 'Despre Autoland', 'hub' => '', 'prefix' => '',
        'meniu' => [
            'despre-noi' => 'Despre noi',
            'recenzii'   => 'Păreri clienți',
            'contact'    => 'Contact',
        ],
        'urls' => [
            'despre-noi' => 'despre-noi/',
            'recenzii'   => 'impresii.php',
            'contact'    => 'contact.php',
        ],
    ],
];

/**
 * Calea relativă a unei pagini, cu slash la final (ex. finantare/credit-auto/ sau despre-noi/)
 */
function pagini_cale($sectiune, $slug)
{
    global $PAGINI_SECTIUNI;
    $prefix = isset($PAGINI_SECTIUNI[$sectiune]) ? $PAGINI_SECTIUNI[$sectiune]['prefix'] : $sectiune . '/';
    return $prefix . $slug . '/';
}

/**
 * Adresa unui element din meniul lateral al unei secțiuni (relativă la rădăcina site-ului)
 */
function pagini_url_meniu($sectiune, $slug)
{
    global $PAGINI_SECTIUNI;
    $s = $PAGINI_SECTIUNI[$sectiune];
    if (isset($s['urls'][$slug])) {
        return $s['urls'][$slug];
    }
    return $slug === '' ? $s['hub'] : $s['prefix'] . $slug . '/';
}

/**
 * Linkurile din textele editate în admin se scriu cu adresa de producție (https://autolandromania.ro/...).
 * Aici ele sunt aduse pe adresa mediului curent, ca pe localhost să nu scoată vizitatorul de pe site.
 * Pe producție BASE_PATH e aceeași adresă, deci nu se schimbă nimic.
 */
function pagini_linkuri_interne($html)
{
    $prod = 'https://autolandromania.ro/';
    if (BASE_PATH === $prod) {
        return $html;
    }
    return str_replace(
        [$prod, 'http://autolandromania.ro/', 'https://www.autolandromania.ro/', 'http://www.autolandromania.ro/'],
        BASE_PATH,
        $html
    );
}
