<?php
// get config file;
include_once './includes/config.php';

$page_title = "Cerere Ofertă Personalizată | Autoland România";

// Set SEO meta tags
require_once './includes/meta_tags.php';
setRequestOfferPageMeta();

// Add JSON-LD structured data for SEO
require_once './includes/json_ld_schemas.php';
$breadcrumbs = [
    ['name' => 'Acasă', 'url' => BASE_PATH],
    ['name' => 'Cere Oferta Ta', 'url' => BASE_PATH . 'cerere_oferta.php']
];
$canonical_url = BASE_PATH . 'cerere_oferta.php';
$cerere_oferta_schemas = [
    getOrganizationSchema(),
    getBreadcrumbSchema($breadcrumbs),
    getWebPageSchema($canonical_url, $page_title, $meta_description ?? '')
];
$header_scripts .= getJsonLdString($cerere_oferta_schemas);

// Load reCAPTCHA when form becomes visible (Intersection Observer) - Optimized for performance
$footer_scripts .= "<script>
(function() {
    // Prevent duplicate loading
    if (window.recaptchaLoaded || document.querySelector('script[src*=\"recaptcha/api.js\"]')) {
        return;
    }
    
    var recaptchaLoaded = false;
    function loadRecaptcha() {
        if (recaptchaLoaded || window.recaptchaLoaded) return;
        recaptchaLoaded = true;
        window.recaptchaLoaded = true;
        var script = document.createElement('script');
        script.src = 'https://www.google.com/recaptcha/api.js';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }
    
    // Load when form is visible or after DOM is ready
    function initRecaptcha() {
        var formElement = document.querySelector('#cerere-oferta, .g-recaptcha');
        if (formElement) {
            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            loadRecaptcha();
                            observer.disconnect();
                        }
                    });
                }, { rootMargin: '200px' });
                observer.observe(formElement);
            } else {
                // Fallback for browsers without IntersectionObserver
                loadRecaptcha();
            }
        }
        
        // Load only when user interacts with form (focus, click, or scrolls near it)
        var formInputs = formElement.querySelectorAll('input, textarea, select');
        formInputs.forEach(function(input) {
            input.addEventListener('focus', loadRecaptcha, { once: true, passive: true });
            input.addEventListener('click', loadRecaptcha, { once: true, passive: true });
        });
        
        // Load on form submit attempt
        if (formElement.tagName === 'FORM') {
            formElement.addEventListener('submit', function(e) {
                if (!window.recaptchaLoaded) {
                    e.preventDefault();
                    loadRecaptcha();
                    setTimeout(function() { formElement.submit(); }, 1000);
                }
            }, { once: false, passive: false });
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRecaptcha);
    } else {
        initRecaptcha();
    }
})();
</script>";

// get header
include './themes/automotive/template_parts/header.php';

// reCAPTCHA is now loaded lazily via Intersection Observer (see above)
$footer_scripts .= "<script type='text/javascript' src='" . BASE_PATH . "themes/automotive/js/jquery.validate.min.js' defer></script>";
$footer_scripts .= "<script type='text/javascript' src='" . BASE_PATH . "themes/automotive/js/additional-methods.min.js' defer></script>";
$footer_scripts .= "<script type='text/javascript'>" . file_get_contents("./includes/validate_cerere_oferta.js") . "</script>";
?>

<section class="content">
    <div class="container">
        <div class="inner-page row">
            <!-- Sidebar START Here -->
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 padding-right-none sm-padding-left-none xs-padding-left-none padding-bottom-40 scroll_effect fadeInUp" data-wow-delay='.2s' style="z-index:100">
                <?php
                require_once './themes/automotive/template_parts/sidebar_left.php';
                ?>
            </div>
            <!-- Sidebar END Here -->


            <div class="with-sidebar col-lg-9 col-md-9 col-sm-12 col-xs-12 welcome padding-left-none md-padding-left-25 padding-bottom-40 scroll_effect fadeInUp">

                <?php
                // get car listing (all or just a part)
                //require_once './themes/automotive/template_parts/inventory_listing2.php';
                ?>
                <div class="information_head margin-top-45">
                    <h3 class="margin-bottom-45">Solicitare de oferta</h3>
                    <form class="cerere-oferta" id="cerere-oferta" name="cerere-oferta" method="POST" action="sservicii.php">

                        <div class="row">
                            <p>
                                Prezentul formular reprezinta un angajament prin care S.C.  AUTOLAND  S.R.L. se obliga sa furnizeze gratuit solicitantului in cel mai scurt timp  posibil cel putin o oferta care sa intruneasca specificatiile solicitate de  catre acesta. <!-- In cazul in care una dintre oferte va fi acceptata, prezentul  formular completat impreuna cu oferta aleasa vor reprezenta anexe ale  contractului de vanzare - cumparare. -->
                            </p>
                            <p>
                                Campuri obligatorii<br>
                            </p>
                            <p></p>
                        </div>

                        <div class="row">
                            <div class="col col-lg-6">
                                <input type="text" name="t1" id="t1" class="form-control margin-bottom-25" placeholder="Subsemnatul">
                            </div>
                            <div class="col col-lg-6">
                                <input type="text" name="t3" id="t3" class="form-control margin-bottom-25" placeholder="din judetul">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col col-lg-6">
                                <select  data-msg="Te rugam selecteaza" name="t10" class="form_cerere form-control" id="t10">
                                    <option value="selecteaza" disabled selected="selected">In calitate de </option>
                                    <option value="persoana fizica">Persoana fizica</option>
                                    <option value="PFA / II">PFA / II</option>
                                    <option value="Societate">Societate</option>
                                </select>
                            </div>
                            <div class="col col-lg-6">
                                <input type="text" name="t11" id="t11" class="form-control margin-bottom-25" placeholder="din judetul">
                            </div>
                        </div>

                        <div class="row">
                            <p>
                                solicit ofertarea in vederea unei posibile achizitii ulterioare prin intermediul S.C. Autoland S.R.L. Iasi, a unui
                            </p>
                            <div class="col col-lg-6">
                                <input type="text" name="t12" id="t12" class="form-control margin-bottom-25" placeholder="Autoturism / Autoutilitar / Autospeciala">
                            </div>
                            <div class="col col-lg-3">
                                <input type="text" name="t13" id="t13" class="form-control margin-bottom-25" placeholder="marca">
                            </div>
                            <div class="col col-lg-3">
                                <input type="text" name="t14" id="t14" class="form-control margin-bottom-25" placeholder="model">
                            </div>
                        </div>

                        <div class="row">
                            <p>
                                cu urmatoarele caracteristici:
                            <div class="col col-lg-12">
                                <input type="text" name="t15" id="t15" class="form-control margin-bottom-25" placeholder="specificatii tehnice">
                            </div>
                            </p>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <input type="text" name="t16" id="t16" class="form-control margin-bottom-25" placeholder="Tipul caroseriei">
                            </div>
                            <div class="col col-lg-3">
                                <input  data-rule-range="1980,2019" type="text" name="t17" id="t17" class="form-control margin-bottom-25" placeholder="an fabricatie minim">
                            </div>
                            <div class="col col-lg-3">
                                <input  data-rule-range="1980,2019" type="text" name="t18" id="t18" class="form-control margin-bottom-25" placeholder="an fabricatie maxim">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col col-lg-3">
                                <select  data-msg="Te rugam selecteaza" name="t19" class="form_cerere form-control" id="t19">
                                    <option value="" disabled selected="selected">Motor</option>
                                    <option value="diesel">Diesel</option>
                                    <option value="benzina">Benzina</option>
                                    <option value="GPL">GPL</option>
                                    <option value="GPL">Electric</option>
                                    <option value="GPL">Hibrid</option>
                                </select>
                            </div>
                            <div class="col col-lg-3">
                                <input  type="text" name="t20" id="t20" class="form-control margin-bottom-25" placeholder="capacitate (aproximativ)">
                            </div>
                            <div class="col col-lg-3">
                                <select  data-msg="Te rugam selecteaza" name="t21" class="form_cerere form-control" id="t21">
                                    <option value="" disabled selected="selected">Cutie de viteze</option>
                                    <option value="manuala">manuala</option>
                                    <option value="automata">automata</option>
                                </select>
                            </div>
                            <div class="col col-lg-3">
                                <select  data-msg="Te rugam selecteaza" name="t22" class="form_cerere form-control" id="t22">
                                    <option value="" disabled selected="selected">Tractiune</option>
                                    <option value="fata">fata</option>
                                    <option value="spate">spate</option>
                                    <option value="integrala">integrala</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col col-lg-4">
                                <input  type="text" name="t23" id="t23" class="form-control margin-bottom-25" placeholder="Km parcursi - maxim">
                            </div>
                            <div class="col col-lg-4">
                                <input  type="text" name="t24" id="t24" class="form-control margin-bottom-25" placeholder="culoare">
                            </div>
                            <div class="col col-lg-4">
                                <input  type="text" name="t25" id="t25" class="form-control margin-bottom-25" placeholder="numar usi">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col col-lg-12">
                                <input  type="text" name="t26" id="t26" class="form-control margin-bottom-25" placeholder="alte dotari solicitate">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col col-lg-3">
                                <select  data-msg="Te rugam selecteaza" name="t27" class="form_cerere form-control" id="t27">
                                    <option value="" disabled selected="selected">Doresc achizitia in</option>
                                    <option value="leasing">leasing</option>
                                    <option value="rate">rate</option>
                                    <option value="cash">cash</option>
                                </select>
                            </div>

                            <div class="col col-lg-3">
                                <select  data-msg="Te rugam selecteaza" name="t28" class="form_cerere form-control" id="t28">
                                    <option value="" disabled selected="selected">iar pretul in tara</option>
                                    <option value="inclusiv TVA">inclusiv TVA</option>
                                    <option value="fara TVA">fara TVA</option>
                                </select>
                            </div>

                            <div class="col col-lg-3">
                                <input  type="text" name="t29" id="t29" class="form-control margin-bottom-25" placeholder="sa fie intre (euro)">
                            </div>
                            <div class="col col-lg-3">
                                <input  type="text" name="t30" id="t30" class="form-control margin-bottom-25" placeholder="si (euro)">
                                <input type="hidden" name="client_ip" value="<?php echo getClientIP(); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <p>
                                Date de contact in vederea primirii ofertei:
                            </p>
                            <div class="row">
                                <div class="col col-lg-4">
                                    <input type="tel" name="tel" id="tel" class="ignore form-control margin-bottom-25" placeholder="Telefon">
                                </div>
                                <div class="col col-lg-4">
                                    <input  data-rule-email type="text" name="email" id="email" class="form-control margin-bottom-25" placeholder="Email">
                                </div>
<!--
                                <div class="col col-lg-4">
                                    <input  data-rule-phoneUK data-msg="Te rugam sa introduci un numar de telefon valid" type="text" name="mobil" id="mobil" class="form-control margin-bottom-25" placeholder="Mobil">
                                </div>
-->                                
                            </div>
                        </div>

                        <div class="my-dropdown form-group row">
                            <div class="g-recaptcha pull-left" data-sitekey="6LcbK38UAAAAAMAFTZuUcAh346Pp7PDT_YTO-Veq"></div>
                            <input type="hidden" class="hiddenRecaptcha " name="hiddenRecaptcha" id="hiddenRecaptcha">
                        </div>
                                                
                        <div class="row">
                            <input class="pull-left margin-top-25" id="submit_btn" type="submit" value="Trimite">
                        </div>


                    </form>
                </div>


            </div>
            <!--welcome ends-->
        </div>
    </div>

</section>

<?php
include './themes/automotive/template_parts/footer.php';
