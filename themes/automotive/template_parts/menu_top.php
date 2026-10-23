<?php
// Helper function to get current page for menu highlighting
function getCurrentPage() {
    $scriptName = basename($_SERVER['SCRIPT_NAME']);
    return '/' . $scriptName;
}
$currentPage = getCurrentPage();

// Check if we're on an article page (including rewritten URLs)
$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$isArticlePage = ($currentPage == '/articole.php' || $currentPage == '/articol.php' || 
                  (strpos($requestUri, '/articole/') !== false && strpos($requestUri, '/articole/') === 0));
?>
<style>
/* Meniul principal: aspect curat, o singură bară, butonul "Cere ofertă" aliniat cu restul */
@media (min-width: 768px) {
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-collapse {
        border-top: 1px solid rgba(255, 255, 255, .14) !important;
        margin-top: 4px;
    }
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li > a {
        background: transparent !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        transform: none !important;
        height: 54px !important;
        line-height: 54px !important;
        padding: 0 15px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        letter-spacing: .05em !important;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .86) !important;
        transition: color .15s ease, box-shadow .15s ease;
    }
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li > a::before,
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li > a::after { display: none !important; }
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li > a:hover,
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li > a:focus {
        color: #fff !important;
        box-shadow: inset 0 -3px 0 rgba(255, 255, 255, .4) !important;
    }
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li.active > a,
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li.open > a,
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li.active > a:hover,
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li.open > a:hover {
        color: #fff !important;
        box-shadow: inset 0 -3px 0 #2bbad4 !important;
    }
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li > a .caret { margin-left: 4px; border-top-color: currentColor; opacity: .8; }

    /* butonul "Cere ofertă", centrat pe verticală în aceeași bară */
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li.nav-cta > a {
        height: 38px !important;
        line-height: 38px !important;
        margin: 8px 0 0 14px !important;
        padding: 0 20px !important;
        background: #0891b2 !important;
        color: #fff !important;
        border-radius: 3px !important;
        box-shadow: none !important;
        letter-spacing: .06em !important;
    }
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li.nav-cta > a:hover,
    body header .bottom-header.bottom-header .navbar-default.navbar-default .navbar-nav > li.nav-cta.active > a { background: #0e7490 !important; }

    /* listele derulante: panou alb, text lizibil */
    body header .navbar-nav > li > .dropdown-menu {
        background: #fff !important;
        -webkit-backdrop-filter: none;
        backdrop-filter: none;
        border: 1px solid rgba(15, 35, 45, .08) !important;
        border-radius: 0 0 4px 4px !important;
        box-shadow: 0 16px 36px rgba(8, 25, 35, .24) !important;
        margin-top: 0 !important;
        padding: 6px 0 !important;
        min-width: 240px;
        text-align: left !important;
        animation: none !important;
    }
    body header .navbar-nav > li > .dropdown-menu > li > a {
        margin: 0 !important;
        padding: 10px 22px !important;
        border-radius: 0 !important;
        background: transparent !important;
        transform: none !important;
        color: #22343d !important;
        font-family: 'Open Sans', sans-serif;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.4;
        text-transform: none;
        letter-spacing: 0;
        white-space: nowrap;
        text-align: left !important;
    }
    body header .navbar-nav > li > .dropdown-menu > li > a:hover,
    body header .navbar-nav > li > .dropdown-menu > li > a:focus {
        background: #eef4f6 !important;
        color: #0b5e73 !important;
        padding-left: 22px !important;
    }
    body header .navbar-nav > li > .dropdown-menu > li.active > a,
    body header .navbar-nav > li > .dropdown-menu > li.active > a:hover {
        background: #f3f7f9 !important;
        color: #0b5e73 !important;
        font-weight: 700;
        box-shadow: inset 3px 0 0 #0e7490;
    }
    body header .navbar-nav > li > .dropdown-menu > li.dropdown-header {
        padding: 10px 22px 4px;
        text-align: left;
        color: #6b7a83;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    body header .navbar-nav > li > .dropdown-menu > .divider { margin: 6px 0; background: #e3e8eb; }
}
@media (max-width: 767px) {
    header .bottom-header .navbar-default .navbar-nav > li.nav-cta > a {
        background: #0891b2 !important;
        color: #fff !important;
        margin: 10px 15px;
        text-align: center;
        border-radius: 3px;
        padding: 11px 14px !important;
    }
}
</style>
<div class="bottom-header-wrapper">
    <div class="bottom-header">
        <div class="container">
            <nav class="navbar navbar-default" role="navigation">
            <div class="container-fluid"> 
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="<?php echo BASE_PATH; ?>index.php">
                        <span class="logo">
                            <img src="<?php echo BASE_PATH; ?>themes/automotive/images/logo_autoland_2.png" alt="Autoland Romania - Mașini Second Hand și Noi cu Garanție" class="brand-logo" fetchpriority="high" width="200" height="60" />
                            <span class="primary_text" style="font-family: 'Verdana'; font-weight: bolder; font-style: italic; font-size: 40px;">Autoland Romania</span> 
                            <span class="secondary_text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </span>
                    </a>
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-center">
                        <li class="<?php echo ($currentPage == '/index.php' || $currentPage == '/' ? 'active' : ''); ?>"><a href="<?php echo BASE_PATH; ?>index.php">Acasă</a></li>

                        <li class="dropdown <?php echo $is_despre ? 'active' : ''; ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Despre noi <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="<?php echo ($is_pagina && isset($slug) && $slug == 'despre-noi') ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>despre-noi/">Despre Autoland România</a></li>
                                <li class="<?php echo in_array($currentPage, ['/impresii.php', '/completare_recenzie.php']) ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>impresii.php">Păreri clienți</a></li>
                            </ul>
                        </li>

                        <?php
                        // Stoc: filtrele din vanzari.php (parametrii din adresă)
                        $is_vanzari = ($currentPage == '/vanzari.php');
                        $get_users = isset($_GET['users']) ? $_GET['users'] : '';
                        $get_subcateg = isset($_GET['subcateg']) ? $_GET['subcateg'] : '';
                        $get_prov = isset($_GET['prov_enienta']) ? $_GET['prov_enienta'] : '';
                        $is_autoturisme = ($is_vanzari && $get_users == 'Autoturisme');
                        $is_autoutilitare = ($is_vanzari && $get_users == 'Autoutilitare');
                        $is_autospecializate = ($is_vanzari && $get_users == 'Autospecializate');
                        $is_utilaje = ($is_vanzari && $get_users == 'Utilaje');
                        $is_utilaje_constructii = ($is_utilaje && $get_subcateg == 'Constructii');
                        $is_utilaje_agricole = ($is_utilaje && $get_subcateg == 'Agricole');
                        $is_utilaje_municipale = ($is_utilaje && $get_subcateg == 'Municipale');
                        $is_romania = ($is_vanzari && $get_prov == 'Romania');
                        $is_import = ($is_vanzari && $get_prov == 'Import');
                        $is_oferte = ($currentPage == '/lista_oferta.php');
                        $is_stoc_toate = ($is_vanzari && !$is_autoturisme && !$is_autoutilitare && !$is_autospecializate && !$is_utilaje && !$is_romania && !$is_import);
                        $active_masini = ($is_vanzari || $is_oferte || $currentPage == '/vanzari_detail.php');
                        ?>
                        <li class="dropdown <?php echo $active_masini ? 'active' : ''; ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Mașini second hand <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="<?php echo $is_stoc_toate ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php">Tot stocul</a></li>
                                <li class="<?php echo $is_autoturisme ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Autoturisme&subcateg=3&marcat=10">Autoturisme</a></li>
                                <li class="<?php echo $is_autoutilitare ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Autoutilitare&subcateg=12&marcat=100">Autoutilitare</a></li>
                                <li class="<?php echo $is_autospecializate ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Autospecializate&subcateg=33&marcat=102">Autospecializate</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="<?php echo $is_romania ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?prov_enienta=Romania">Mașini din România</a></li>
                                <li class="<?php echo $is_import ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?prov_enienta=Import">Mașini din import</a></li>
                                <li class="<?php echo $is_oferte ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>lista_oferta.php">Oferte speciale</a></li>
                            </ul>
                        </li>

                        <?php
                        // Finanțare: pagina principală (leasing.php) și subpaginile din tabelul pagini (/finantare/...)
                        $is_pagina = ($currentPage == '/pagina.php');
                        $finSlug = ($is_pagina && isset($sectiune) && $sectiune === 'finantare' && isset($slug)) ? $slug : '';
                        $is_finantare = ($currentPage == '/leasing.php' || $finSlug !== '');
                        $finantare_links = [
                            'credit-auto'         => 'Credit auto în rate',
                            'leasing-financiar'   => 'Leasing financiar',
                            'leasing-operational' => 'Leasing operațional',
                            'avans-minim'         => 'Avans minim',
                            'acte-necesare'       => 'Acte necesare',
                            'calculator-rate'     => 'Calculator rată',
                        ];
                        ?>
                        <li class="dropdown <?php echo $is_finantare ? 'active' : ''; ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Finanțare <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="<?php echo $currentPage == '/leasing.php' ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>leasing.php">Leasing și rate: prezentare</a></li>
                                <li role="separator" class="divider"></li>
                                <?php foreach ($finantare_links as $k => $label): ?>
                                <li class="<?php echo $finSlug === $k ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>finantare/<?php echo $k; ?>/"><?php echo $label; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>

                        <?php
                        $is_servicii = in_array($currentPage, ['/garantie.php', '/inmatriculari.php', '/asistenta-rutiera.php']);
                        $is_despre = (in_array($currentPage, ['/impresii.php', '/completare_recenzie.php']) || ($is_pagina && isset($sectiune) && $sectiune === 'pagini' && isset($slug) && $slug == 'despre-noi'));
                        ?>
                        <li class="dropdown <?php echo $is_servicii ? 'active' : ''; ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Garanție și servicii <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="<?php echo $currentPage == '/garantie.php' ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>garantie.php">Garanție</a></li>
                                <li class="<?php echo $currentPage == '/inmatriculari.php' ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>inmatriculari.php">Înmatriculări și acte</a></li>
                                <li class="<?php echo $currentPage == '/asistenta-rutiera.php' ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>asistenta-rutiera.php">Asistență rutieră</a></li>
                            </ul>
                        </li>

                        <li class="<?php echo $isArticlePage ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>articole/">Ghiduri utile</a></li>

                        <li class="<?php echo $currentPage == '/contact.php' ? 'active' : ''; ?>">
                            <a href="<?php echo BASE_PATH; ?>contact.php">Contact</a>
                        </li>

                        <li class="nav-cta<?php echo $currentPage == '/cerere_oferta.php' ? ' active' : ''; ?>">
                            <a href="<?php echo BASE_PATH; ?>cerere_oferta.php">Cere ofertă</a>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse --> 
            </div>
            <!-- /.container-fluid --> 
        </nav>
        </div>
        <div class="header_shadow"></div>
    </div>
</div>