<?php
/**
 * Calculator de rată (leasing), model orientativ, compact.
 * Folosit pe fișa mașinii (vanzari_detail.php, pliat) și pe pagina Calculator rată ({CALCULATOR}).
 *
 * Variabile așteptate (înainte de include):
 *   $calc_price          float   prețul afișat pe site, considerat final (cu TVA)
 *   $calc_price_editable bool    true = câmp pentru preț (pagina Calculator rată)
 *   $calc_collapsible    bool    true = calculatorul e pliat într-o bară care arată rata estimată
 *   $calc_cta_url        string  adresa butonului principal
 *   $calc_cta_label      string  textul butonului principal
 *   $calc_wa_base        string  (opțional) mesaj WhatsApp de început; dacă e gol, nu apare linkul WhatsApp
 * Setările (dobândă, taxe, limite) sunt în includes/calculator_rata_config.php.
 */
require_once __DIR__ . '/../../../includes/calculator_rata_config.php';
global $CALCULATOR_RATA;
$C = $CALCULATOR_RATA;
$calc_price = isset($calc_price) ? (float)$calc_price : 0;
$calc_price_editable = !empty($calc_price_editable);
$calc_collapsible = !empty($calc_collapsible);
$calc_cta_url = isset($calc_cta_url) ? $calc_cta_url : BASE_PATH . 'cerere_oferta.php';
$calc_cta_label = isset($calc_cta_label) ? $calc_cta_label : 'Contactează-ne';
$calc_wa_base = isset($calc_wa_base) ? $calc_wa_base : '';
$calc_uid = 'c' . substr(md5(uniqid('', true)), 0, 6);
?>
<?php if ($calc_collapsible): ?>
<details class="ap-calc-wrap">
    <summary>
        <span class="ap-calc-sum-icon" aria-hidden="true"><i class="fa fa-calculator"></i></span>
        <span class="ap-calc-sum-title">Calculează rata lunară<small>Leasing · estimare orientativă</small></span>
        <span class="ap-calc-sum-rate"><strong data-calc="summary">–</strong>€ / lună</span>
    </summary>
<?php endif; ?>
<div class="ap-calc"
     data-price="<?php echo htmlspecialchars((string)$calc_price); ?>"
     data-vat="<?php echo (float)$C['tva']; ?>"
     data-rate="<?php echo (float)$C['dobanda']; ?>"
     data-mgmt="<?php echo (float)$C['taxa_management']; ?>"
     data-monthly-fee="<?php echo (float)$C['comision_lunar']; ?>"
     data-services="<?php echo (float)$C['cheltuieli_servicii']; ?>"
     data-residual="<?php echo (float)$C['valoare_reziduala']; ?>"
     data-adv-min="<?php echo (int)$C['avans_min']; ?>" data-adv-max="<?php echo (int)$C['avans_max']; ?>" data-adv-step="<?php echo (int)$C['avans_pas']; ?>" data-adv-value="<?php echo (int)$C['avans_implicit']; ?>"
     data-months-min="<?php echo (int)$C['luni_min']; ?>" data-months-max="<?php echo (int)$C['luni_max']; ?>" data-months-step="<?php echo (int)$C['luni_pas']; ?>" data-months-value="<?php echo (int)$C['luni_implicit']; ?>">

    <div class="ap-calc-head">
        <span class="ap-calc-title">Estimare rată</span>
        <span class="ap-calc-tag">model orientativ</span>
    </div>

    <?php if ($calc_price_editable): ?>
    <div class="ap-calc-price">
        <label for="<?php echo $calc_uid; ?>-price">Prețul mașinii (cu TVA), în EUR</label>
        <input type="text" inputmode="decimal" id="<?php echo $calc_uid; ?>-price" data-calc="price" value="<?php echo $calc_price > 0 ? number_format($calc_price, 0, ',', '.') : ''; ?>" autocomplete="off">
    </div>
    <?php endif; ?>

    <div class="ap-calc-main" aria-live="polite">
        <div class="ap-calc-rate"><strong data-calc="monthly">–</strong><span class="ap-calc-cur">€ / lună</span></div>
        <div class="ap-calc-note">cu TVA · <span data-calc="monthly-net"></span> € fără TVA</div>
    </div>

    <div class="ap-calc-controls">
        <div class="ap-calc-ctl">
            <span class="ap-calc-label" id="<?php echo $calc_uid; ?>-l1">Avans</span>
            <div class="ap-calc-stepper" role="group" aria-labelledby="<?php echo $calc_uid; ?>-l1">
                <button type="button" data-calc-step="adv" data-dir="-1" aria-label="Scade avansul">&minus;</button>
                <output data-calc="adv-value"><?php echo (int)$C['avans_implicit']; ?>%</output>
                <button type="button" data-calc-step="adv" data-dir="1" aria-label="Crește avansul">+</button>
            </div>
        </div>
        <div class="ap-calc-ctl">
            <span class="ap-calc-label" id="<?php echo $calc_uid; ?>-l2">Perioadă</span>
            <div class="ap-calc-stepper" role="group" aria-labelledby="<?php echo $calc_uid; ?>-l2">
                <button type="button" data-calc-step="months" data-dir="-1" aria-label="Scade perioada">&minus;</button>
                <output data-calc="months-value"><?php echo (int)$C['luni_implicit']; ?> luni</output>
                <button type="button" data-calc-step="months" data-dir="1" aria-label="Crește perioada">+</button>
            </div>
        </div>
    </div>

    <div class="ap-calc-first">
        <div class="ap-calc-first-title">Prima plată, cu TVA</div>
        <dl class="ap-calc-list">
            <div><dt>Avans (<span data-calc="adv-label"><?php echo (int)$C['avans_implicit']; ?>%</span>)</dt><dd><span data-calc="advance">–</span> €</dd></div>
            <div><dt>Taxă de management (<?php echo calculator_rata_procent($C['taxa_management']); ?>%)</dt><dd><span data-calc="mgmt">–</span> €</dd></div>
            <div><dt>Cheltuieli prestări servicii</dt><dd><span data-calc="services">–</span> €</dd></div>
            <div class="ap-calc-total"><dt>Total primă plată</dt><dd><span data-calc="first">–</span> €</dd></div>
        </dl>
        <p class="ap-calc-first-note">Se achită o singură dată, la începutul contractului. Rata lunară nu include aceste sume.</p>
    </div>
    <dl class="ap-calc-list ap-calc-list-rate">
        <div><dt>Dobândă</dt><dd><?php echo calculator_rata_procent($C['dobanda']); ?>% pe an</dd></div>
        <div><dt>Comision de gestiune lunar</dt><dd><?php echo calculator_rata_procent($C['comision_lunar']); ?>% (inclus în rată)</dd></div>
    </dl>

    <a class="ap-calc-cta" href="<?php echo htmlspecialchars($calc_cta_url); ?>"><?php echo htmlspecialchars($calc_cta_label); ?></a>
    <?php if ($calc_wa_base !== ''): ?>
    <a class="ap-calc-wa" data-calc="cta-wa" data-wa-base="<?php echo htmlspecialchars($calc_wa_base); ?>" href="https://wa.me/40742377001" target="_blank" rel="noopener">sau trimite estimarea pe WhatsApp</a>
    <?php endif; ?>

    <p class="ap-calc-disclaimer">Estimare orientativă. Rata finală poate suferi modificări, în funcție de profilul clientului și de condițiile din contract.</p>
    <details class="ap-calc-more">
        <summary>Cum se calculează</summary>
        <p>Prima plată fără TVA este <span data-calc="first-net">–</span> €. Taxa de management se calculează la prețul fără TVA, iar cheltuielile de prestări servicii sunt o sumă fixă; la ambele se adaugă TVA.</p>
        <p>Rata și avansul pot suferi modificări în funcție de profilul clientului, de finanțator și de condițiile de la data contractului, și pot exista costuri adiționale (de exemplu asigurări). Oferta finală se stabilește după analiza dosarului.</p>
        <p>Calculul pornește de la prețul afișat, considerat preț final cu TVA <?php echo (int)$C['tva']; ?>% (prețul fără TVA se obține împărțind la <?php echo number_format(1 + $C['tva'] / 100, 2, ',', ''); ?>). Pentru mașinile vândute într-un alt regim de TVA, valorile pot diferi.</p>
    </details>
    <noscript><p class="ap-calc-disclaimer">Calculatorul necesită JavaScript activat.</p></noscript>
</div>
<?php if ($calc_collapsible): ?>
</details>
<?php endif; ?>
