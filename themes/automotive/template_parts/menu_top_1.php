<div class="bottom-header" >
    <div class="container">
        <nav class="navbar navbar-default" role="navigation">
            <div class="container-fluid"> 
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                    <a class="navbar-brand" href="index-2.html">
                        <span class="logo"><span class="primary_text">Autoland</span> <span class="secondary_text">România</span></span>
<!--                        <img data-no-retina src="./themes/automotive/images/logo_Autoland.png">-->
                    </a> </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav pull-right">
                        <li><a href="index-2.html">Prima pagină</a></li>
<!--                        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Pages <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Alternate Footers <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="index-2.html">Footer 1</a></li>
                                        <li><a href="index-footer-2.html">Footer 2</a></li>
                                        <li><a href="index-footer-3.html">Footer 3</a></li>
                                        <li><a href="index-footer-4.html">Footer 4</a></li>
                                        <li><a href="index-footer-5.html">Footer 5</a></li>
                                        <li><a href="index-footer-6.html">Footer 6</a></li>
                                        <li><a href="index-no-footer.html">No Footer</a></li>
                                    </ul>
                                </li>
                                <li><a href="404.html">404</a></li>
                                <li><a href="about.html">About Us</a></li>
                                <li><a href="faq.html">FAQ</a></li>
                                <li><a href="page-fullwidth.html">Fullwidth Page</a></li>
                                <li><a href="page-sidebar.html">Sidebar Page</a></li>
                                <li><a href="pricing-tables.html">Pricing Tables</a></li>
                            </ul>
                        </li>-->
                        <!--                                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Inventory <b class="caret"></b></a>
                                                                <ul class="dropdown-menu">
                                                                    <li><a href="demo.html">Select View Demo</a></li>
                                                                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Boxed Layout <b class="caret"></b></a>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a href="inventory-boxed-fullwidth.html">Boxed Fullwidth</a></li>
                                                                            <li><a href="inventory-boxed-sidebar-left.html">Boxed Sidebar Left</a></li>
                                                                            <li><a href="inventory-boxed-sidebar-right.html">Boxed Sidebar Right</a></li>
                                                                        </ul>
                                                                    </li>
                                                                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Wide Layout <b class="caret"></b></a>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a href="inventory-wide-fullwidth.html">Wide Fullwidth</a></li>
                                                                            <li><a href="inventory-wide-sidebar-left.html">Wide Sidebar Left</a></li>
                                                                            <li><a href="inventory-wide-sidebar-right.html">Wide Sidebar Right</a></li>
                                                                        </ul>
                                                                    </li>
                                                                    <li><a href="vanzari_detail.php?id=1115&pic=Furgon_7locuri_leasing_01_14478580960.jpg">Detaliu masina o sa fie modifi</a></li>
                                                                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Vehicle Comparison <b class="caret"></b></a>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a href="inventory-2-listing-comparison.html">2 Listing Comparison</a></li>
                                                                            <li><a href="inventory-3-listing-comparison.html">3 Listing Comparison</a></li>
                                                                            <li><a href="inventory-4-listing-comparison.html">4 Listing Comparison</a></li>
                                                                        </ul>
                                                                    </li>
                                                                </ul>
                                                            </li>-->
                        <?php
                        $active_menu_auto_dupa_provenienta=(in_array($_SERVER['REQUEST_URI'], ['/vanzari.php?users=Autoturisme&subcateg=3&marcat=10','/vanzari.php?users=Autoutilitare&subcateg=12&marcat=100','/vanzari.php?users=Autospecializate&subcateg=33&marcat=102']))?'active':'';
                        ?>
                        <li class="dropdown <?php echo $active_menu_auto_dupa_provenienta;?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Categorii auto <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="<?php echo $_SERVER['REQUEST_URI'] == '/vanzari.php?users=Autoturisme&subcateg=3&marcat=10' ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Autoturisme&subcateg=3&marcat=10">Autoturisme</a></li>
                                <li class="<?php echo $_SERVER['SCRIPT_URI'] == '/vanzari.php?users=Autoutilitare&subcateg=12&marcat=100' ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Autoutilitare&subcateg=12&marcat=100">Autoutilitare</a></li>
                                <li class="<?php echo $_SERVER['SCRIPT_URI'] == '/vanzari.php?users=Autospecializate&subcateg=33&marcat=102' ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Autospecializate&subcateg=33&marcat=102">Autospecializate</a></li>
                            </ul>
                        </li>
                        <li class="<?php echo $_SERVER['REQUEST_URI'] == ("/vanzari.php?prov_enienta=Romania") ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?prov_enienta=Romania">Auto din România</a></li>
                        <li class="<?php echo $_SERVER['REQUEST_URI'] == ("/vanzari.php?prov_enienta=Import") ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>vanzari.php?prov_enienta=Import">Auto din import</a></li>
                        
                        <?php //var_dump($_SERVER['SCRIPT_URL'], $_SERVER);exit('FOO');
                        //var_dump(BASE_PATH ."/vanzari.php?prov_enienta=Import");exit;
                        //var_dump($_SERVER['REQUEST_URI']);
                        ?>
                        <li class="<?php echo $_SERVER['SCRIPT_URL'] == '/lista_oferta.php' ? 'active' : ''; ?>"><a href="<?php echo BASE_PATH; ?>lista_oferta.php">Ofertă specială</a></li>

                        <li class="dropdown <?php echo (in_array($_SERVER['SCRIPT_URL'], ['/leasing.php', '/asistenta-rutiera.php', 'garantie.php/', '/inmatriculari.php']) ? 'active' : ''); ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Servicii <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="<?php echo $_SERVER['SCRIPT_URL'] == '/leasing.php' ? 'active' : ''; ?>">
                                    <a href="<?php echo BASE_PATH; ?>leasing.php">Leasing / Rate</a>
                                </li>
                                <li class="<?php echo $_SERVER['SCRIPT_URL'] == '/asistenta-rutiera.php' ? 'active' : ''; ?>">
                                    <a href="<?php echo BASE_PATH; ?>asistenta-rutiera.php">Asistenţa rutieră</a>
                                </li>
                                <li class="<?php echo $_SERVER['SCRIPT_URL'] == '/garantie.php' ? 'active' : ''; ?>">
                                    <a href="<?php echo BASE_PATH; ?>garantie.php">Garanţie</a>
                                </li>
                                <li class="<?php echo $_SERVER['SCRIPT_URL'] == '/inmatriculari.php' ? 'active' : ''; ?>">
                                    <a href="<?php echo BASE_PATH; ?>inmatriculari.php">Înmatriculări / Acte auto</a>
                                </li>
                            </ul>
                        </li>
                        <li class="<?php echo $_SERVER['SCRIPT_URL'] == '/contact.php' ? 'active' : ''; ?>">
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