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
                        
                        <?php
                        // Check if on vanzari.php and has category parameters
                        $is_autoturisme = ($currentPage == '/vanzari.php' && isset($_GET['users']) && $_GET['users'] == 'Autoturisme');
                        $is_autoutilitare = ($currentPage == '/vanzari.php' && isset($_GET['users']) && $_GET['users'] == 'Autoutilitare');
                        $is_autospecializate = ($currentPage == '/vanzari.php' && isset($_GET['users']) && $_GET['users'] == 'Autospecializate');

                        // Check provenienta
                        $is_romania = ($currentPage == '/vanzari.php' && isset($_GET['prov_enienta']) && $_GET['prov_enienta'] == 'Romania');
                        $is_import = ($currentPage == '/vanzari.php' && isset($_GET['prov_enienta']) && $_GET['prov_enienta'] == 'Import');

                        // $active_menu_auto_dupa_categorie = ($is_autoturisme || $is_autoutilitare || $is_autospecializate) ? 'active' : '';
                        $active_menu_catalog_auto = ($is_autoturisme || $is_autoutilitare || $is_autospecializate || $is_romania || $is_import) ? 'active' : '';
                        ?>
                        <li class="dropdown <?php echo $active_menu_catalog_auto; ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Catalog AUTO<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="<?php echo $is_autoturisme ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Autoturisme&subcateg=3&marcat=10">Autoturisme</a></li>
                                <li class="<?php echo $is_autoutilitare ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Autoutilitare&subcateg=12&marcat=100">Autoutilitare</a></li>
                                <li class="<?php echo $is_autospecializate ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Autospecializate&subcateg=33&marcat=102">Autospecializate</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="<?php echo $is_romania ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?prov_enienta=Romania">Auto din Romania</a></li>
                                <li class="<?php echo $is_import ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?prov_enienta=Import">Auto din import</a></li>
                            </ul>
                        </li>

                        <li class="<?php echo ($currentPage == '/cerere_oferta.php' ? 'active' : ''); ?>"><a href="<?php echo BASE_PATH; ?>cerere_oferta.php">Solicita oferta unica</a></li>
                        <li class="<?php echo $currentPage == '/lista_oferta.php' ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>lista_oferta.php">Oferte speciale</a></li>

                        <?php
                        //var_dump($_SERVER['SCRIPT_URL'], $_SERVER);exit('FOO');
                        //var_dump(BASE_PATH ."/vanzari.php?prov_enienta=Import");exit;
                        //var_dump($_SERVER['REQUEST_URI']);
                        ?>
			            <?php
                        // Check if on vanzari.php and has Utilaje category parameters
                        $is_utilaje = ($currentPage == '/vanzari.php' && isset($_GET['users']) && $_GET['users'] == 'Utilaje');
                        $is_utilaje_constructii = ($is_utilaje && isset($_GET['subcateg']) && $_GET['subcateg'] == 'Constructii');
                        $is_utilaje_agricole = ($is_utilaje && isset($_GET['subcateg']) && $_GET['subcateg'] == 'Agricole');
                        $is_utilaje_municipale = ($is_utilaje && isset($_GET['subcateg']) && $_GET['subcateg'] == 'Municipale');
                        $active_menu_utilaje = $is_utilaje ? 'active' : '';
                        ?>
                        <li class="dropdown <?php echo $active_menu_utilaje; ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">UTILAJE<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="<?php echo $is_utilaje_constructii ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Utilaje&subcateg=Constructii">Utilaje Construcții</a></li>
                                <li class="<?php echo $is_utilaje_agricole ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Utilaje&subcateg=Agricole">Utilaje Agricole</a></li>
                                <li class="<?php echo $is_utilaje_municipale ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Utilaje&subcateg=Municipale">Utilaje Municipale</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="<?php echo ($is_utilaje && !isset($_GET['subcateg'])) ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Utilaje">Toate Utilajele</a></li>
                            </ul>
                        </li>

                        <li class="dropdown <?php echo (in_array($currentPage, ['/leasing.php', '/asistenta-rutiera.php', '/garantie.php', '/inmatriculari.php']) ? 'active' : ''); ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Servicii <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="<?php echo $currentPage == '/leasing.php' ? 'active' : ''; ?>">
                                    <a href="<?php echo BASE_PATH; ?>leasing.php">Leasing / Rate</a>
                                </li>
                                <li class="<?php echo $currentPage == '/asistenta-rutiera.php' ? 'active' : ''; ?>">
                                    <a href="<?php echo BASE_PATH; ?>asistenta-rutiera.php">Asistenţa rutiera</a>
                                </li>
                                <li class="<?php echo $currentPage == '/garantie.php' ? 'active' : ''; ?>">
                                    <a href="<?php echo BASE_PATH; ?>garantie.php">Garantie</a>
                                </li>
                                <li class="<?php echo $currentPage == '/inmatriculari.php' ? 'active' : ''; ?>">
                                    <a href="<?php echo BASE_PATH; ?>inmatriculari.php">Inmatriculari / Acte auto</a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="<?php echo $isArticlePage ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>articole/">Ghiduri Utile</a></li>
                        
                        <li class="<?php echo $currentPage == '/impresii.php' ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>impresii.php">Pareri Clienti</a></li>
<!-- optiune inlocuita dupa cererea lui stefan de pe Whatsapp 
                        <li><a href="impresii.php">Impresiile clientilor nostri</a></li> -->
                        <li class="<?php echo $currentPage == '/contact.php' ? 'active' : ''; ?>">
                            <a href="<?php echo BASE_PATH; ?>contact.php">Contact</a>
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