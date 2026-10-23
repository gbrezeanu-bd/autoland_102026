<?php
require_once __DIR__ . '/../../../includes/vanzari_catalog.php';
// Pe vanzari.php formularul își păstrează valorile alese; pe celelalte pagini pornește gol
$sb = isset($vz_f) ? $vz_f : vz_blank_filters();
$sb_h = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
// toate mărcile cu anunțuri active, nu doar cele rămase după celelalte filtre
$sb_marci = vz_facet(vz_blank_filters(), 'marca', true);
$sb_caroserii = vz_facet($sb, 'caroserie');
$sb_combustibili = vz_facet($sb, 'combustibil');
$sb_cutii = vz_facet($sb, 'cutie');
$sb_prov = vz_facet($sb, 'prov_enienta');
$sb_fuel_labels = array('benzina' => 'Benzină', 'motorina' => 'Motorină', 'hybrid' => 'Hybrid', 'electric' => 'Electric');
$sb_active = count(vz_active_chips($sb)) - ($sb['users'] !== '' ? 1 : 0);

// <option>; opțiunea aleasă rămâne mereu în listă
$sb_option = function ($value, $label, $count, $current) use ($sb_h) {
    $selected = ((string)$value === (string)$current) ? ' selected="selected"' : '';
    return '<option value="' . $sb_h($value) . '"' . $selected . '>' . $sb_h($label) . '</option>';
};
// un câmp: etichetă mică deasupra și selectul; "is-set" îl evidențiază când are o valoare aleasă
$sb_field = function ($id, $caption, $first_label, $options_html, $current, $full) use ($sb_h) {
    $set = ($current !== '' && $current !== 0 && $current !== null);
    return '<div class="vz-field' . ($full ? ' vz-full' : '') . '">'
        . '<span class="vz-flabel" id="' . $id . '-l">' . $sb_h($caption) . '</span>'
        . '<select name="' . $id . '" id="' . $id . '" class="vz-select' . ($set ? ' is-set' : '') . '" aria-labelledby="' . $id . '-l">'
        . '<option value="">' . $sb_h($first_label) . '</option>' . $options_html . '</select></div>';
};
?>
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>themes/automotive/css/sidebar-cauta.css?v=<?php echo @filemtime(__DIR__ . '/../css/sidebar-cauta.css'); ?>">
<!-- Mobile Search Toggle Button (visible only on mobile) -->
<button class="mobile-search-toggle vz-toggle" type="button" onclick="toggleMobileSearch()" aria-label="Deschide căutarea">
    <span class="icon"><i class="fa fa-search" aria-hidden="true"></i></span>
    <span class="text">Caută mașina<small><?php echo $sb_active > 0 ? $sb_active . ($sb_active == 1 ? ' filtru activ' : ' filtre active') : 'Marcă, preț, an, caroserie…'; ?></small></span>
    <span class="arrow"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
</button>

<!-- InventoryBox START Here -->
<div class="search-inventory vz-side">
    <div class="search-form">
        <form action="vanzari.php" method="get" name="form2" target="_self" id="form2" onsubmit="return vzCleanForm(this)">
            <?php if ($sb['sort'] !== VZ_DEFAULT_SORT): ?><input type="hidden" name="sort" value="<?php echo $sb_h($sb['sort']); ?>"><?php endif; ?>
            <?php if ($sb['pp'] !== VZ_PER_PAGE): ?><input type="hidden" name="pp" value="<?php echo (int)$sb['pp']; ?>"><?php endif; ?>

            <div class="vz-side-head">
                <span class="vz-side-title"><i class="fa fa-sliders" aria-hidden="true"></i> Caută mașina</span>
                <?php if ($sb_active > 0 || $sb['users'] !== ''): ?>
                <a class="vz-side-reset" href="<?php echo BASE_PATH; ?>vanzari.php">Resetează</a>
                <?php endif; ?>
            </div>

            <div class="vz-search">
                <i class="fa fa-search" aria-hidden="true"></i>
                <input name="txtsrc" type="text" id="txtsrc" placeholder="Marcă sau model" aria-label="Caută după marcă sau model" value="<?php echo $sb_h($sb['txtsrc']); ?>" autocomplete="off">
            </div>

            <div class="vz-fields">
                <?php
                if ($sb['marca'] !== '' && !isset($sb_marci[$sb['marca']])) { $sb_marci[$sb['marca']] = 0; }
                $o = ''; foreach ($sb_marci as $m => $n) { $o .= $sb_option($m, $m, $n, $sb['marca']); }
                echo $sb_field('marca', 'Marca', 'Toate mărcile', $o, $sb['marca'], true);

                // la zero rezultate câmpul nu dispare: arătăm toate tipurile de caroserie din stoc
                if (!$sb_caroserii) { $sb_caroserii = vz_facet(vz_blank_filters(), 'caroserie'); }
                if ($sb_caroserii || $sb['caroserie'] !== '') {
                    if ($sb['caroserie'] !== '' && !isset($sb_caroserii[$sb['caroserie']])) { $sb_caroserii[$sb['caroserie']] = 0; }
                    $o = ''; foreach ($sb_caroserii as $c => $n) { $o .= $sb_option($c, vz_label_caroserie($c), $n, $sb['caroserie']); }
                    echo $sb_field('caroserie', 'Caroserie', 'Toate', $o, $sb['caroserie'], true);
                }

                if ($sb['combustibil'] !== '' && !isset($sb_combustibili[$sb['combustibil']])) { $sb_combustibili[$sb['combustibil']] = 0; }
                $o = ''; foreach ($sb_combustibili as $c => $n) { $o .= $sb_option($c, isset($sb_fuel_labels[$c]) ? $sb_fuel_labels[$c] : ucfirst($c), $n, $sb['combustibil']); }
                echo $sb_field('combustibil', 'Combustibil', 'Toate', $o, $sb['combustibil'], false);

                $o = $sb_option('automata', 'Automată', 0, $sb['cutie']) . $sb_option('manuala', 'Manuală', 0, $sb['cutie']);
                echo $sb_field('cutie', 'Cutie', 'Oricare', $o, $sb['cutie'], false);

                $o = ''; for ($i = 1; $i <= 15; $i++) { $y = (int)date('Y') - $i; $o .= $sb_option($y, 'din ' . $y, null, $sb['an']); }
                echo $sb_field('an', 'An fabricație', 'Oricare', $o, $sb['an'], false);

                $o = ''; foreach (array(5000, 8000, 10000, 13000, 15000, 20000, 30000) as $p) { $o .= $sb_option($p, 'sub ' . number_format($p, 0, ',', '.') . ' €', null, $sb['pret']); }
                echo $sb_field('pret', 'Preț maxim', 'Oricare', $o, $sb['pret'], false);

                $o = ''; foreach (array(50000, 100000, 150000, 200000, 250000) as $k) { $o .= $sb_option($k, 'sub ' . number_format($k, 0, ',', '.'), null, $sb['km']); }
                echo $sb_field('km', 'Km maxim', 'Oricare', $o, $sb['km'], false);

                $o = $sb_option('Romania', 'România', 0, $sb['prov_enienta']) . $sb_option('Import', 'Import', 0, $sb['prov_enienta']);
                echo $sb_field('prov_enienta', 'Proveniență', 'Oricare', $o, $sb['prov_enienta'], false);
                ?>
            </div>

            <button type="submit" class="vz-submit"><i class="fa fa-search" aria-hidden="true"></i> Găsește-mi mașina</button>
        </form>
    </div>
</div>
<!-- InventoryBox END Here -->


<!-- disable due to security risk 
<div class="search-form margin-top-20 padding-vertical-20 padding-left-20 padding-right-20 scroll_effect fadeInLeft">
    <a href="https://www.autoa.ro" target="_blank" title="">
        <img class="center-block" src="<?= BASE_PATH ?>images/piese%20auto%20autoa.png" alt="" data-hoverimg="<?= BASE_PATH ?>images/piese%20auto%20autoa.png">
    </a>
</div>
-->

<!-- TBI Bank Toggle Button -->
<button class="tbi-toggle-button vz-toggle-tbi" type="button" onclick="toggleTBIBank()" aria-label="Descoperă finanțarea TBI Bank">
    <span class="icon"><i class="fa fa-credit-card" aria-hidden="true"></i></span>
    <span class="text">Finanțare auto TBI Bank<small>Rate fără avans · aprobare în câteva minute</small></span>
    <span class="arrow"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
</button>

<!-- TBI Bank Content (collapsible) -->
<div class="search-form margin-top-20 padding-vertical-20 padding-left-20 padding-right-20 scroll_effect fadeInLeft sidebar-tbi-bank vz-card">
    <a href="https://tbibank.ro/" target="_blank" title="">
        <img class="center-block" src="<?= BASE_PATH ?>images/tbi_bank.jpeg" alt="TBI Bank" data-hoverimg="<?= BASE_PATH ?>images/tbi_bank.jpeg">
    </a>

    <div class="featured">
        <h5>🚗 Mașina pe care ți-o dorești poate fi mai aproape decât crezi!</h5>
        <hr class="my-4">
        <p>La <strong>tbi bank</strong> ai dobânzi fixe de la <strong>7,9%</strong> pentru credite de până la 100.000 lei și de la <strong>4,9%</strong> pentru credite de peste 100.000 lei.</p>
        
        <p>✅ Rate fără avans<br>
        ✅ Aprobare rapidă, în câteva minute<br>
        ✅ Doar cu buletinul<br>
        ✅ Acceptăm salariați, pensionari, șoferi cu diurne, PFA, II, venituri din chirii, dividende etc.</p>
        
        <p>📩 <em>Scrie-ne pentru mai multe detalii și află cât de simplu poate fi să-ți îndeplinești planurile!</em></p>
        
        <a class="default-btn" href="https://tbibank.ro/" target="_blank" role="button" rel="sponsored noopener">Aplică Acum</a>
    </div>
</div>

<!-- Vadrexim reclama START Here -->
<div class="search-form margin-top-20 padding-vertical-20 padding-left-20 padding-right-20 scroll_effect rotateIn sidebar-vadrexim vz-card">
    <a style="font-weight: bold;color: red" href="https://www.vadrexim.ro/anvelope-iarna.html" target="_blank" rel="sponsored noopener">ANVELOPE IARNA</a> Ieftine & Jante la Vadrexim!
</div>
<!-- Vadrexim reclama END Here -->

<!-- Reclama START Here -->
<div class="search-form margin-top-20 padding-vertical-60 padding-left-20 padding-right-20 scroll_effect fadeInLeft sidebar-generic-ad vz-card">

    <div class="featured">
        <h5>Spatiu reclama</h5>
        <hr class="my-4">
        <p><strong>Reclama ta poate fi aici pentru doar 50 euro / an</strong></p>
        <a class="default-btn" href="<?php echo BASE_PATH; ?>contact.php" role="button">Vreau</a>
    </div>

</div>
<!-- Reclama END Here -->

<!-- Curs Valutar START Here -->
<?php
// Preia cursul valutar curent (cel mai recent)
$query_curs_valutar_curent = "SELECT * FROM curs_valutar ORDER BY date DESC LIMIT 1";
$curs_valutar_curent = $connection->query($query_curs_valutar_curent) or db_fail($connection);
$current_exchange = mysqli_fetch_assoc($curs_valutar_curent);

// Preia cursul valutar anterior (de la o dată DIFERITĂ, nu doar al doilea rând)
// Compară cu ultima zi anterioară distinctă
$current_date = $current_exchange['date'];
$query_curs_valutar_anterior = "SELECT * FROM curs_valutar WHERE date < '$current_date' ORDER BY date DESC LIMIT 1";
$curs_valutar_anterior = $connection->query($query_curs_valutar_anterior) or db_fail($connection);
$past_exchange = mysqli_fetch_assoc($curs_valutar_anterior);

// Calculează diferențele doar dacă există curs anterior
if ($past_exchange && !empty($past_exchange['EUR'])) {
    $diferenta_EUR = round(floatval($current_exchange['EUR']) - floatval($past_exchange['EUR']), 4);
    $diferenta_USD = round(floatval($current_exchange['USD']) - floatval($past_exchange['USD']), 4);
    $diferenta_HUF = round(floatval($current_exchange['HUF']) - floatval($past_exchange['HUF']), 4);
    $diferenta_CZK = round(floatval($current_exchange['CZK']) - floatval($past_exchange['CZK']), 4);
    $diferenta_PLN = round(floatval($current_exchange['PLN']) - floatval($past_exchange['PLN']), 4);
} else {
    // Dacă nu există curs anterior, diferența este 0
    $diferenta_EUR = 0;
    $diferenta_USD = 0;
    $diferenta_HUF = 0;
    $diferenta_CZK = 0;
    $diferenta_PLN = 0;
}
?>

<div id="curs-valutar-widget" class="search-form margin-top-20 padding-vertical-20 padding-left-20 padding-right-20 scroll_effect rotateIn vz-card">
    <table class="table table-responsive table-hover col-md-12">
        <thead>
        <th class="text-center" colspan="4">Curs valutar <?php echo date("d.m.Y", strtotime($current_exchange['date'])); ?></th>
        </thead>
        <tbody>
            <tr>
                <th>EUR</th>
                <td><?php echo $current_exchange['EUR']; ?></td>
<?php
$trnd = ($diferenta_EUR < 0) ? 'fa-chevron-down' : 'fa-chevron-up';
?>
                <td><?php echo ($diferenta_EUR < 0) ? $diferenta_EUR : "+" . $diferenta_EUR; ?> <span><i class="fa <?php echo $trnd; ?>"></i></span></td>
                <td>lei</td>
            </tr>
            <tr>
                <th>USD</th>
                <td><?php echo $current_exchange['USD']; ?></td>
<?php
$trnd = ($diferenta_USD < 0) ? 'fa-chevron-down' : 'fa-chevron-up';
?>
                <td><?php echo ($diferenta_USD < 0) ? $diferenta_USD : "+" . $diferenta_USD; ?> <span><i class="fa <?php echo $trnd; ?>"></i></span></td>
                <td>lei</td>
            </tr>
            <tr>
                <th>100 HUF</th>
                <td><?php echo $current_exchange['HUF']; ?></td>
<?php
$trnd = ($diferenta_HUF < 0) ? 'fa-chevron-down' : 'fa-chevron-up';
?>
                <td><?php echo ($diferenta_HUF < 0) ? $diferenta_HUF : "+" . $diferenta_HUF; ?> <span><i class="fa <?php echo $trnd; ?>"></i></span></td>
                <td>lei</td>
            </tr>
            <tr>
                <th>CZK</th>
                <td><?php echo $current_exchange['CZK']; ?></td>
<?php
$trnd = ($diferenta_CZK < 0) ? 'fa-chevron-down' : 'fa-chevron-up';
?>
                <td><?php echo ($diferenta_CZK < 0) ? $diferenta_CZK : "+" . $diferenta_CZK; ?> <span><i class="fa <?php echo $trnd; ?>"></i></span></td>
                <td>lei</td>
            </tr>
            <tr>
                <th>PLN</th>
                <td><?php echo $current_exchange['PLN']; ?></td>
<?php
$trnd = ($diferenta_PLN < 0) ? 'fa-chevron-down' : 'fa-chevron-up';
?>
                <td><?php echo ($diferenta_PLN < 0) ? $diferenta_PLN : "+" . $diferenta_PLN; ?> <span><i class="fa <?php echo $trnd; ?>"></i></span></td>
                <td>lei</td>
            </tr>
        </tbody>
    </table>
</div>
<!-- Curs Valutar END Here -->


<!-- Mobile Search Toggle Script -->
<script>
    // Function to clean empty parameters from form submission
    // Trimite doar câmpurile completate (adrese curate) și aplică filtrele imediat, la fiecare schimbare
    function vzCleanForm(form) {
        Array.prototype.forEach.call(form.elements, function(el) {
            if (el.name && el.type !== 'submit' && (el.value === '' || el.value === 'fara')) { el.disabled = true; }
        });
        return true;
    }
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('form2');
        if (!form) { return; }
        form.addEventListener('change', function(e) {
            if (e.target && e.target.tagName === 'SELECT') { vzCleanForm(form); form.submit(); }
        });
    });
    function cleanEmptyParams(form) {
        var inputs = form.querySelectorAll('input, select');
        inputs.forEach(function(input) {
            if (input.value === '' || input.value === null) {
                input.removeAttribute('name');
            }
        });
        return true;
    }
    
    // Mobile search form toggle
    function toggleMobileSearch() {
        const searchForm = document.querySelector('.col-lg-3 .search-inventory');
        const toggleButton = document.querySelector('.mobile-search-toggle');
        
        if (searchForm && toggleButton) {
            // Toggle active class
            searchForm.classList.toggle('active');
            toggleButton.classList.toggle('active');
            
            // Scroll to form if opening
            if (searchForm.classList.contains('active')) {
                setTimeout(function() {
                    searchForm.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest' 
                    });
                }, 100);
            }
        }
    }
    
    // TBI Bank toggle
    function toggleTBIBank() {
        const tbiContent = document.querySelector('.sidebar-tbi-bank');
        const toggleButton = document.querySelector('.tbi-toggle-button');
        
        if (tbiContent && toggleButton) {
            tbiContent.classList.toggle('active');
            toggleButton.classList.toggle('active');
            
            if (tbiContent.classList.contains('active')) {
                setTimeout(function() {
                    tbiContent.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest' 
                    });
                }, 100);
            }
        }
    }
    
    // Hide toggle button on desktop
    function checkScreenSize() {
        const toggleButton = document.querySelector('.mobile-search-toggle');
        if (toggleButton && window.innerWidth > 991) {
            toggleButton.style.display = 'none';
            // Show search form on desktop
            const searchForm = document.querySelector('.col-lg-3 .search-inventory');
            if (searchForm) {
                searchForm.classList.add('active');
            }
        } else if (toggleButton) {
            toggleButton.style.display = 'block';
        }
    }
    
    // Check on load and resize
    window.addEventListener('load', checkScreenSize);
    window.addEventListener('resize', checkScreenSize);
    
        // FLOATING SEARCH BUTTON - Using CSS class from scustomizer.css
        (function() {
        var floatingBtn = document.createElement('button');
        floatingBtn.className = 'floating-search-btn';
        floatingBtn.innerHTML = '<span class="icon">🔍</span><span class="text">Caută</span>';
        floatingBtn.setAttribute('aria-label', 'Open search');
        
        floatingBtn.onclick = function() {
            window.scrollTo({top: 0, behavior: 'smooth'});
            setTimeout(function() {
                var toggleButton = document.querySelector('.mobile-search-toggle');
                if (toggleButton) {
                    toggleButton.click();
                }
            }, 500);
        };
        
        document.body.appendChild(floatingBtn);
        
        // Function to toggle visibility based on scroll and screen size
        function updateFloatingButton() {
            var isMobile = window.innerWidth <= 991;
            var scrollY = window.scrollY || window.pageYOffset;
            var shouldShow = isMobile && scrollY > 200;
            
            if (shouldShow) {
                floatingBtn.classList.add('visible');
            } else {
                floatingBtn.classList.remove('visible');
            }
        }
        
        // Check on scroll
        window.addEventListener('scroll', updateFloatingButton);
        
        // Check on resize
        window.addEventListener('resize', updateFloatingButton);
        
        // Initial check
        updateFloatingButton();
    })();
</script>