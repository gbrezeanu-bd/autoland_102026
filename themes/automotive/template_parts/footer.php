
<div class="clearfix"></div>
<div class="back_to_top"> <img data-no-retina src="<?php echo BASE_PATH; ?>themes/automotive/images/arrow-up.png" alt="scroll up" loading="lazy"/> </div>

<!--Footer Start-->
<div class="clearfix"></div>
<section class="copyright-wrap">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 footer-brand-section">
                <div class="logo-footer">
                    <a href="<?php echo BASE_PATH; ?>index.php">
                        <h1>Autoland Romania</h1>
                    </a>
                </div>
                <p>Copyright &copy; 2007 - <?php echo date("Y") ?> Autoland S.R.L.<br>Toate drepturile rezervate.</p>
                
                <!-- Social Media Links -->
                <div class="footer-social-section">
                    <h4 class="footer-section-title">Urmărește-ne</h4>
                    <ul class="footer-social-links">
                        <?php
                        // Include JSON-LD constants for social links if not already defined
                        if (!defined('JSONLD_TIKTOK') && !defined('JSONLD_GOOGLE_BUSINESS')) {
                            $json_ld_path = dirname(dirname(dirname(__FILE__))) . '/includes/json_ld_schemas.php';
                            if (file_exists($json_ld_path)) {
                                require_once $json_ld_path;
                            }
                        }
                        $google_business_url = defined('JSONLD_GOOGLE_BUSINESS') ? JSONLD_GOOGLE_BUSINESS : 'https://g.page/autoland+romania+iasi';
                        $tiktok_url = defined('JSONLD_TIKTOK') ? JSONLD_TIKTOK : 'https://www.tiktok.com/@autoland_romania';
                        ?>
                        <li><a href="<?php echo $google_business_url; ?>" target="_blank" rel="noopener noreferrer" title="Google Business Profile" aria-label="Google Business Profile"><i class="fa fa-google"></i></a></li>
                        <li><a href="<?php echo $tiktok_url; ?>" target="_blank" rel="noopener noreferrer" title="TikTok" aria-label="TikTok"><i class="fa fa-video-camera"></i></a></li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <h4 class="footer-section-title">Autovehicule</h4>
                <ul class="footer-links-list">
                    <li><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Autoturisme&subcateg=3&marcat=10">Autoturisme</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Autoutilitare&subcateg=12&marcat=100">Autoutilitare</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>vanzari.php?users=Autospecializate&subcateg=33&marcat=102">Autospecializate</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>vanzari.php?prov_enienta=Romania">Auto din România</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>vanzari.php?prov_enienta=Import">Auto din Import</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>lista_oferta.php">Ofertă Specială</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>cerere_oferta.php">Cere Ofertă</a></li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <h4 class="footer-section-title">Servicii</h4>
                <ul class="footer-links-list">
                    <li><a href="<?php echo BASE_PATH; ?>leasing.php">Leasing / Rate</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>garantie.php">Garanție</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>inmatriculari.php">Înmatriculări / Acte Auto</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>asistenta-rutiera.php">Asistență Rutieră</a></li>
                </ul>
                
                <div class="footer-subsection">
                    <h4 class="footer-section-title">Informații</h4>
                    <ul class="footer-links-list">
                        <li><a href="<?php echo BASE_PATH; ?>articole/">Ghiduri Utile</a></li>
                        <li><a href="<?php echo BASE_PATH; ?>impresii.php">Păreri Clienți</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <h4 class="footer-section-title">Legături Rapide</h4>
                <ul class="footer-links-list">
                    <li><a href="<?php echo BASE_PATH; ?>index.php">Prima Pagină</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>contact.php">Contact</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>politica_de_confidentialitate.php">Politică de Confidențialitate</a></li>
                    <li><a href="#bs-gdpr-cookies-modal" id='cookiesSettings' data-toggle="modal" data-target="#bs-gdpr-cookies-modal">Personalizare Cookies</a></li>
                    <li><a href="https://anpc.ro/comisariate-regionale-comisariate-judetene/" target="_blank" rel="noopener noreferrer">Protecția Consumatorilor - A.N.P.C</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php
if (DEBUG == FALSE && file_exists('public/build/rev-manifest.json')) {
    ?>
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>public/build/js/<?php echo $rev_manifest_version['bundle.js']; ?>" defer></script>
    <?php
} else {
    ?>
    <!-- Critical JavaScript - Load first (jQuery is needed by other scripts) -->
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/jquery.min.js"></script>
    
    <!-- Non-critical JavaScript - Load with defer to prevent blocking -->
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/bootstrap.min.js" defer></script>
    <?php
    // Only load Revolution Slider scripts if not on car detail page (they're not used there)
    if (!defined('IS_CAR_DETAIL_PAGE') || !IS_CAR_DETAIL_PAGE):
    ?>
    <!-- Protection for Revolution Slider to prevent errors when elements don't exist -->
    <script type="text/javascript">
    (function() {
        // Protect CSSStyleDeclaration.cssText setter to prevent errors when setting on undefined/null
        if (typeof CSSStyleDeclaration !== 'undefined' && CSSStyleDeclaration.prototype) {
            try {
                var originalCssTextDescriptor = Object.getOwnPropertyDescriptor(CSSStyleDeclaration.prototype, 'cssText');
                if (originalCssTextDescriptor && originalCssTextDescriptor.set) {
                    var originalSetter = originalCssTextDescriptor.set;
                    Object.defineProperty(CSSStyleDeclaration.prototype, 'cssText', {
                        set: function(value) {
                            try {
                                // Only set if 'this' is a valid CSSStyleDeclaration instance
                                // Check if it's a valid style object (not detached or null)
                                if (this && typeof this === 'object' && this.constructor === CSSStyleDeclaration) {
                                    originalSetter.call(this, value);
                                }
                            } catch(e) {
                                // Silently ignore errors when setting cssText on invalid objects
                                // This prevents Revolution Slider errors when elements don't exist
                            }
                        },
                        get: originalCssTextDescriptor.get || function() { return ''; },
                        configurable: true,
                        enumerable: true
                    });
                }
            } catch(e) {
                // If we can't modify the prototype, use error handler as fallback
            }
        }
        
        // Add comprehensive error handler to catch and suppress Revolution Slider errors
        window.addEventListener('error', function(e) {
            // Suppress errors from Revolution Slider related to cssText
            var isRevolutionSliderError = false;
            
            // Check filename
            if (e.filename) {
                isRevolutionSliderError = (
                    e.filename.indexOf('jquery.themepunch.tools.min.js') !== -1 ||
                    e.filename.indexOf('jquery.themepunch.revolution.min.js') !== -1 ||
                    e.filename.indexOf('index.php') !== -1 ||
                    e.filename.indexOf('revolution') !== -1 ||
                    e.filename.indexOf('themepunch') !== -1
                );
            }
            
            // Check error message
            if (e.message) {
                isRevolutionSliderError = isRevolutionSliderError || (
                    e.message.indexOf('cssText') !== -1 || 
                    e.message.indexOf('Cannot set properties of undefined') !== -1 ||
                    e.message.indexOf('Cannot set properties of null') !== -1
                );
            }
            
            if (isRevolutionSliderError) {
                e.preventDefault();
                e.stopPropagation();
                return true;
            }
            return false;
        }, true);
    })();
    </script>
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/jquery.themepunch.tools.min.js" defer onerror="console.warn('Revolution Slider tools failed to load');"></script>
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/jquery.themepunch.revolution.min.js" defer onerror="console.warn('Revolution Slider failed to load');"></script>
    <?php endif; ?>
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/wow.min.js" defer></script>
    <!-- retina.js removed - not used (all images have data-no-retina attribute) -->
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/jquery.fancybox.js" defer></script>
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/main.js" defer></script>
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/modernizr.custom.js" defer></script>
    <script defer src="<?php echo BASE_PATH; ?>themes/automotive/js/jquery.flexslider.js"></script>
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/jquery.bxslider.js" defer></script>
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/jquery.selectbox-0.2.js" defer></script>
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/jquery.mousewheel.js" defer></script>
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/jquery.easing.js" defer></script>
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/jquery.bs.gdpr.cookies.js" defer></script>
    <script type="text/javascript" src="<?php echo BASE_PATH; ?>themes/automotive/js/custom.js" defer></script>
    <?php
}
?>


<?php print $footer_scripts; ?>

<!-- selectmake.js already loaded in header with defer, no need to load again -->
 


<!-- Google Analytics -->
<?php
// add analytics code only if allowed by GDPR settings
if (isset($_COOKIE['CookiePreferences'])) {
    $cookiePreferences = json_decode($_COOKIE['CookiePreferences'], TRUE);
    if (is_array($cookiePreferences) && in_array('analytics', $cookiePreferences)) {
        ?>
        <script type="text/javascript" src="<?php echo BASE_PATH; ?>js/ganalytics.js" defer></script>
        <?php
    }
}
?>
<!-- End Google Analytics -->
</body>

</html>
<?php
require_once './includes/application_bottom.php';
