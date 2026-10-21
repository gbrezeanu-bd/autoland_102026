<?php
/**
 * Contact page
 * 
 * This site requires php version >= 7.0
 * 
 * @copyright (c) 2018, Marius Ionel <webmaster@grupnet.ro>
 * @version 0.0.1
 */
// get config file;
include_once './includes/config.php';
include_once './includes/pagini_sectiuni.php';
include_once './includes/home_blocks.php';

// Aspectul cu meniu lateral (secțiunea Despre Autoland)
$sectiune = 'pagini';
$slug = 'contact';

// /////////////////////////////////////////////////////////////////////////////
// try to get content
$query_text = "SELECT id, contact FROM texte WHERE id = 1";

$result = $connection->query($query_text) or db_fail($connection);
$row = $result->fetch_assoc();

// Set optimized page title (direct in file for easier maintenance)
$page_title = "Contact, adresă și program, parc auto Iași | Autoland România";
$banner_title = "Contact Autoland România: adresă, program, telefon";
$page_subtitle = '';

// Set SEO meta tags
require_once './includes/meta_tags.php';
setContactPageMeta();

$footer_scripts = "<script src='https://www.google.com/recaptcha/api.js' async></script>";
$footer_scripts .= "<script type='text/javascript' src='/themes/automotive/js/jquery.validate.min.js'></script>";
$footer_scripts .= "<script type='text/javascript' src='/themes/automotive/js/additional-methods.min.js'></script>";

$footer_scripts .= "<script type='text/javascript' async>" . file_get_contents("./includes/contact_footer.js") . "</script>";

$content = is_null($row) ? "Content is missing" : home_inlocuieste_marcaje(pagini_linkuri_interne($row['contact']), $connection);

// Add JSON-LD structured data for SEO (LocalBusiness + Breadcrumbs)
require_once './includes/json_ld_schemas.php';
$breadcrumbs = [
    ['name' => 'Acasă', 'url' => BASE_PATH],
    ['name' => 'Contact', 'url' => BASE_PATH . 'contact.php']
];
$canonical_url = BASE_PATH . 'contact.php';
// Firma (AutoDealer) cu denumire legală, CUI, program și punct de contact, plus pagina de contact și întrebările frecvente din text
$contact_schemas = [
    getOrganizationSchema(),
    [
        "@context" => "https://schema.org",
        "@type" => "ContactPage",
        "@id" => $canonical_url . "#contactpage",
        "url" => $canonical_url,
        "name" => $page_title,
        "description" => isset($meta_description) ? $meta_description : '',
        "inLanguage" => "ro-RO",
        "about" => ["@id" => JSONLD_BASE_URL . "#organization"],
        "mainEntity" => ["@id" => JSONLD_BASE_URL . "#organization"],
    ],
    getBreadcrumbSchema($breadcrumbs),
];
$contact_faq = pagini_extrage_faq($content);
if ($contact_faq) {
    $contact_schemas[] = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $contact_faq];
}
$header_scripts .= '<link rel="stylesheet" href="' . BASE_PATH . 'themes/automotive/css/pagini.css">' . "\n";
$header_scripts .= getJsonLdString($contact_schemas);

// /////////////////////////////////////////////////////////////////////////////
// get header template part
include './themes/automotive/template_parts/header.php';
?>

<section class="content">
    <div class="container">
        <div class="inner-page">
            <div class="ap-layout">
                <?php include './themes/automotive/template_parts/pagina_sidebar.php'; ?>
                <div class="ap-main">
                    <?php print $content; ?>

                    <div class="ap ap-form-section">
                        <h2 id="formular">Trimite-ne un mesaj</h2>
                        <p><strong>Completează formularul și revenim către tine în programul de lucru.</strong> Pentru o întrebare urgentă, sună la <a href="tel:+40742377001">0742 377 001</a>.</p>

                        <div id="result"></div>
                        <form id="formular-contact" class="ap-form" method="post" action="">
                            <fieldset id="contact_form">
                                <input type="hidden" name="form_title" value="Mesaj din formularul de contact">
                                <div class="ap-form-grid">
                                    <div class="ap-field">
                                        <label for="contact-name">Nume și prenume</label>
                                        <input type="text" id="contact-name" name="name" class="form-control" autocomplete="name">
                                    </div>
                                    <div class="ap-field">
                                        <label for="contact-telefon">Telefon</label>
                                        <input type="tel" id="contact-telefon" name="telefon" class="form-control" autocomplete="tel" placeholder="de exemplu 0742123456">
                                    </div>
                                    <div class="ap-field">
                                        <label for="contact-email">E-mail</label>
                                        <input type="email" id="contact-email" name="email" class="form-control" autocomplete="email">
                                    </div>
                                    <div class="ap-field">
                                        <label for="contact-subiect">Subiect</label>
                                        <input type="text" id="contact-subiect" name="subiect" class="form-control">
                                    </div>
                                    <div class="ap-field ap-field-full">
                                        <label for="contact-mesaj">Mesaj</label>
                                        <textarea id="contact-mesaj" name="mesaj" class="form-control contact_textarea" rows="6"></textarea>
                                    </div>
                                </div>

                                <div class="ap-recaptcha">
                                    <div class="g-recaptcha" data-sitekey="6LcbK38UAAAAAMAFTZuUcAh346Pp7PDT_YTO-Veq"></div>
                                    <input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">
                                </div>

                                <p class="ap-note">Prin trimiterea mesajului ești de acord cu <a href="<?php echo BASE_PATH; ?>politica_de_confidentialitate.php">politica de confidențialitate</a>.</p>
                                <input id="submit_btn" class="ap-submit" type="submit" value="Trimite mesajul">
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <!--container ends-->
</section>
<!--content ends-->

<?php
// get footer template part
include './themes/automotive/template_parts/footer.php';
