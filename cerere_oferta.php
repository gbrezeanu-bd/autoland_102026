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
$header_scripts .= '<link rel="stylesheet" href="' . BASE_PATH . 'themes/automotive/css/cerere-oferta.css?v=' . @filemtime(__DIR__ . '/themes/automotive/css/cerere-oferta.css') . '">' . PHP_EOL;

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
// întrebarea despre TVA are sens doar pentru PFA / II și societăți
$footer_scripts .= "<script type='text/javascript'>(function(){var sel=document.getElementById('t10'),box=document.getElementById('cr-tva');if(!sel||!box){return;}function upd(){var show=(sel.value==='PFA / II'||sel.value==='Societate');box.hidden=!show;Array.prototype.forEach.call(box.querySelectorAll('input'),function(i){i.disabled=!show;});}sel.addEventListener('change',upd);upd();})();</script>";
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
                <div class="cr-page">

                    <div class="cr-intro">
                        <h2>Cere o ofertă personalizată</h2>
                        <p>Spune-ne ce mașină cauți și ce buget ai. Completează doar ce știi, restul stabilim împreună la telefon.</p>
                        <ul class="cr-trust">
                            <li><i class="fa fa-check-circle" aria-hidden="true"></i> Ofertă gratuită</li>
                            <li><i class="fa fa-check-circle" aria-hidden="true"></i> Mașini verificate, cu garanție 1–3 ani</li>
                            <li><i class="fa fa-check-circle" aria-hidden="true"></i> Leasing, rate sau cash</li>
                        </ul>
                    </div>

                    <form class="cerere-oferta" id="cerere-oferta" name="cerere-oferta" method="POST" action="sservicii.php" novalidate>

                        <div class="cr-section">
                            <h3><span class="cr-num">1</span> Despre tine</h3>
                            <div class="cr-grid">
                                <div class="cr-field c6">
                                    <label class="cr-label" for="t1">Nume și prenume <span class="cr-req">*</span></label>
                                    <input type="text" name="t1" id="t1" required autocomplete="name" placeholder="ex. Ion Popescu">
                                </div>
                                <div class="cr-field c6">
                                    <label class="cr-label" for="t3">Județul tău</label>
                                    <input type="text" name="t3" id="t3" autocomplete="address-level1" placeholder="ex. Iași">
                                </div>
                                <div class="cr-field c6">
                                    <label class="cr-label" for="t10">În calitate de</label>
                                    <select data-msg="Te rugăm selectează" name="t10" class="form_cerere" id="t10">
                                        <option value="selecteaza" disabled selected="selected">Alege</option>
                                        <option value="persoana fizica">Persoană fizică</option>
                                        <option value="PFA / II">PFA / II</option>
                                        <option value="Societate">Societate</option>
                                    </select>
                                </div>
                                <div class="cr-field c6" id="cr-tva" hidden>
                                    <span class="cr-label" id="cr-tva-l">Firma este plătitoare de TVA?</span>
                                    <div class="cr-seg" role="radiogroup" aria-labelledby="cr-tva-l">
                                        <input type="radio" name="t11" id="t11_da" value="DA" disabled><label for="t11_da">DA</label>
                                        <input type="radio" name="t11" id="t11_nu" value="NU" disabled><label for="t11_nu">NU</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="cr-section">
                            <h3><span class="cr-num">2</span> Mașina pe care o cauți</h3>
                            <div class="cr-grid">
                                <div class="cr-field c6">
                                    <label class="cr-label" for="t12">Tip de vehicul</label>
                                    <select name="t12" id="t12">
                                        <option value="" disabled selected="selected">Alege</option>
                                        <option value="Autoturism">Autoturism</option>
                                        <option value="Autoutilitar">Autoutilitar</option>
                                        <option value="Autospecializat">Autospecializat</option>
                                    </select>
                                </div>
                                <div class="cr-field c3 half">
                                    <label class="cr-label" for="t13">Marca</label>
                                    <input type="text" name="t13" id="t13" placeholder="ex. Volkswagen">
                                </div>
                                <div class="cr-field c3 half">
                                    <label class="cr-label" for="t14">Modelul</label>
                                    <input type="text" name="t14" id="t14" placeholder="ex. Tiguan">
                                </div>
                                <div class="cr-field c12" style="grid-column: span 12;">
                                    <label class="cr-label" for="t15">Specificații dorite</label>
                                    <input type="text" name="t15" id="t15" placeholder="ex. 2.0 TDI, 150 CP, 4x4, culoare gri, 5 uși">
                                    <span class="cr-hint">Motor, putere, tracțiune, culoare, număr de uși sau orice altceva contează pentru tine.</span>
                                </div>
                                <div class="cr-field c6">
                                    <label class="cr-label" for="t16">Tipul caroseriei</label>
                                    <input type="text" name="t16" id="t16" placeholder="ex. SUV, break, hatchback, van">
                                </div>
                                <div class="cr-field c3 half">
                                    <label class="cr-label" for="t17">An fabricație minim</label>
                                    <input data-rule-range="1980,<?php echo date('Y'); ?>" type="text" inputmode="numeric" name="t17" id="t17" placeholder="ex. 2018">
                                </div>
                                <div class="cr-field c3 half">
                                    <label class="cr-label" for="t18">Preț până la</label>
                                    <select name="t18" id="t18">
                                        <option value="" disabled selected="selected">Alege</option>
                                        <?php foreach (range(5000, 30000, 5000) as $vz_pret): ?>
                                        <option value="<?php echo $vz_pret; ?>"><?php echo number_format($vz_pret, 0, ',', '.'); ?> €</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="cr-field c3 half">
                                    <label class="cr-label" for="t19">Combustibil</label>
                                    <select data-msg="Te rugăm selectează" name="t19" class="form_cerere" id="t19">
                                        <option value="" disabled selected="selected">Alege</option>
                                        <option value="diesel">Diesel</option>
                                        <option value="benzina">Benzină</option>
                                        <option value="GPL">GPL</option>
                                        <option value="electric">Electric</option>
                                        <option value="hibrid">Hibrid</option>
                                    </select>
                                </div>
                                <div class="cr-field c3 half">
                                    <label class="cr-label" for="t20">Capacitate cilindrică</label>
                                    <input type="text" name="t20" id="t20" placeholder="ex. 2.0 sau 1998 cmc">
                                </div>
                                <div class="cr-field c3 half">
                                    <label class="cr-label" for="t21">Cutie de viteze</label>
                                    <select data-msg="Te rugăm selectează" name="t21" class="form_cerere" id="t21">
                                        <option value="" disabled selected="selected">Alege</option>
                                        <option value="manuala">Manuală</option>
                                        <option value="automata">Automată</option>
                                    </select>
                                </div>
                                <div class="cr-field c3 half">
                                    <label class="cr-label" for="t23">Rulaj maxim</label>
                                    <select name="t23" id="t23">
                                        <option value="" disabled selected="selected">Alege</option>
                                        <?php foreach (range(50000, 300000, 50000) as $vz_km): ?>
                                        <option value="<?php echo $vz_km; ?>"><?php echo number_format($vz_km, 0, ',', '.'); ?> km</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="cr-field" style="grid-column: span 12;">
                                    <label class="cr-label" for="t26">Alte dotări solicitate</label>
                                    <textarea name="t26" id="t26" rows="3" placeholder="ex. navigație, cameră de mers înapoi, scaune încălzite"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="cr-section">
                            <h3><span class="cr-num">3</span> Buget și finanțare</h3>
                            <input type="hidden" name="client_ip" value="<?php echo getClientIP(); ?>">
                            <div class="cr-grid">
                                <div class="cr-field c6">
                                    <label class="cr-label" for="t27">Doresc achiziția în</label>
                                    <select data-msg="Te rugăm selectează" name="t27" class="form_cerere" id="t27">
                                        <option value="" disabled selected="selected">Alege</option>
                                        <option value="leasing">Leasing</option>
                                        <option value="rate">Rate</option>
                                        <option value="cash">Cash</option>
                                    </select>
                                </div>
                                <div class="cr-field c6">
                                    <label class="cr-label" for="t28">Prețul în țară</label>
                                    <select data-msg="Te rugăm selectează" name="t28" class="form_cerere" id="t28">
                                        <option value="" disabled selected="selected">Alege</option>
                                        <option value="inclusiv TVA">Inclusiv TVA</option>
                                        <option value="fara TVA">Fără TVA</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="cr-section">
                            <h3><span class="cr-num">4</span> Cum te contactăm <small>telefonul este obligatoriu</small></h3>
                            <div class="cr-grid">
                                <div class="cr-field c6">
                                    <label class="cr-label" for="tel">Telefon <span class="cr-req">*</span></label>
                                    <input type="tel" name="tel" id="tel" required autocomplete="tel" placeholder="ex. 07xx xxx xxx">
                                </div>
                                <div class="cr-field c6">
                                    <label class="cr-label" for="email">Email</label>
                                    <input data-rule-email type="text" name="email" id="email" autocomplete="email" placeholder="ex. nume@email.ro">
                                </div>
                            </div>
                        </div>

                        <div class="cr-send">
                            <p class="cr-agreement">Prezentul formular reprezintă un angajament prin care S.C. AUTOLAND S.R.L. se obligă să furnizeze gratuit solicitantului, în cel mai scurt timp posibil, cel puțin o ofertă care să întrunească specificațiile solicitate de către acesta.</p>
                            <div class="cr-captcha my-dropdown form-group">
                                <div class="g-recaptcha" data-sitekey="6LcbK38UAAAAAMAFTZuUcAh346Pp7PDT_YTO-Veq"></div>
                                <input type="hidden" class="hiddenRecaptcha " name="hiddenRecaptcha" id="hiddenRecaptcha">
                            </div>
                            <input id="submit_btn" type="submit" value="Trimite cererea">
                            <p class="cr-legal">Prin trimiterea formularului ești de acord ca datele introduse să fie folosite pentru a te contacta în legătură cu solicitarea ta. Mai multe în <a href="<?php echo BASE_PATH; ?>politica_de_confidentialitate.php">Politica de confidențialitate</a>.</p>
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
