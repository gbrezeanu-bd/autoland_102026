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


// /////////////////////////////////////////////////////////////////////////////
// try to get content
$query_text = "SELECT id, inmatriculari FROM texte WHERE id = 1";

$result = $connection->query($query_text) or die($connection->error);
$row = $result->fetch_assoc();

$page_title = "Înmatriculări Auto Iași | Servicii Complete | Autoland România";
$page_subtitle = "";
$footer_scripts = "<script src='https://www.google.com/recaptcha/api.js'></script>";
$footer_scripts .= "<script type='text/javascript' src='/themes/automotive/js/jquery.validate.min.js'></script>";
$footer_scripts .= "<script type='text/javascript' src='/themes/automotive/js/additional-methods.min.js'></script>";

$footer_scripts .= "<script type='text/javascript'>" . file_get_contents("./includes/inmatriculari_footer.js") . "</script>";
$content = is_null($row) ? "Content is missing" : $row['inmatriculari'];

// Set SEO meta tags
require_once './includes/meta_tags.php';
setRegistrationPageMeta();

// Add JSON-LD structured data for SEO
require_once './includes/json_ld_schemas.php';
$breadcrumbs = [
    ['name' => 'Acasă', 'url' => BASE_PATH],
    ['name' => 'Servicii', 'url' => BASE_PATH . 'inmatriculari.php'],
    ['name' => 'Înmatriculări Auto', 'url' => BASE_PATH . 'inmatriculari.php']
];
$canonical_url = BASE_PATH . 'inmatriculari.php';
$service_schemas = [
    getServiceSchema(
        "Înmatriculări Auto",
        "Servicii Înmatriculări și Acte Auto",
        "Servicii complete de înmatriculare auto: înmatriculări permanente și provizorii, transcrierea, radierea, schimbarea permisului de circulație. Rezolvăm toate actele necesare pentru vehiculul dumneavoastră.",
        "Iași"
    ),
    getBreadcrumbSchema($breadcrumbs),
    getOrganizationSchema(),
    getWebPageSchema($canonical_url, $page_title, $meta_description ?? '')
];
$header_scripts .= getJsonLdString($service_schemas);

// /////////////////////////////////////////////////////////////////////////////
// get header template part
include './themes/automotive/template_parts/header.php';
?>

<section class="content">
    <div class="container">
        <div class="inner-page">
            <?php print $content; ?>
            <section class="row container">

                <form class="margin-top-25 form-horizontal" id="cerere-inmatriculare" method="post" action="">
                    <div class="form_contact margin-bottom-20">
                        <div id="result"></div>
                        <fieldset id="contact_form">
                            <input type="hidden" name="form_title" value="Mesaj din formularul de inmatriculari">
                            <div class="">
                                <input type="text" class="form-control margin-bottom-25" id="nume" name="nume" placeholder="Nume si prenume" />
                            </div>

                            <div class="">
                                <input type="text" class="form-control margin-bottom-25" id="email" name="email" placeholder="Email" />
                            </div>

                            <div class="">
                                <input type="tel" class="form-control margin-bottom-25" id="telefon" name="telefon" placeholder="Telefon sub formatul 0742123456" />
                            </div>

                            <div class="">
                                <textarea class="form-control margin-bottom-25" name="mesaj" id="mesaj" rows="7" placeholder="Mesaj"></textarea>
                            </div>


                            <div class="form-group row">
                                <div class="g-recaptcha pull-left" data-sitekey="6LcbK38UAAAAAMAFTZuUcAh346Pp7PDT_YTO-Veq"></div>
                                <input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">
                            </div>



                            <div class="">
                                <input class="pull-left margin-top-25 validate-before-submit" id="submit_btn" type="submit" value="Trimite mesaj">
                            </div>

                        </fieldset>
                    </div>                                 
                </form>


            </section>

            <div class="clearfix"></div>
        </div>
    </div>
    <!--container ends--> 
</section>
<!--content ends-->

<?php

// get footer template part
include './themes/automotive/template_parts/footer.php';
