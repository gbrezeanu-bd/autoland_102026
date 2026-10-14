<?php
// Setează valori default pentru filtru
$_GET['combustibil'] = "";
$_GET['an'] = "";
$_GET['pret'] = "";
$_GET['prov_enienta'] = "";
?>
<?php
// Get all unique makes directly from the masini table
$query_makes = "SELECT DISTINCT(marca) FROM masini ORDER BY marca ASC";
$makes_result = $connection->query($query_makes) or db_fail($connection);
?>
<!-- Removed old JavaScript code -->

<!-- Mobile Search Toggle Button (visible only on mobile) -->
<button class="mobile-search-toggle" onclick="toggleMobileSearch()" aria-label="Toggle search form">
    <span class="icon">🔍</span>
    <span class="text">Caută mașina</span>
    <span class="arrow">▼</span>
</button>

<!-- InventoryBox START Here -->
<div class="search-inventory">
    <h4 class="margin-bottom-25 margin-top-none"><strong>CAUTĂ</strong> MAŞINI</h4>
    <div class="search-form margin-top-20 padding-vertical-20">
        <form action="vanzari.php" method="get" name="form2" target="_self" id="form2">
            <div class="select-wrapper clearfix">
                <div class="col-md-12 about_team margin-bottom-10">
                    <div class="my-dropdowns">
                    <input name="txtsrc" type="text" class="vanzari_box" id="txtsrc" placeholder="Caută după marcă sau model" value="">
                    </div>
                </div>
            </div>

            <!-- Category selector -->
            <!-- <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div>Categorie: </div>
                    <select name="users" class="vanzari_box" id="users">
                        <option value="" <?php if (!isset($_GET['users']) || empty($_GET['users'])) {echo 'selected="selected"';} ?>>Toate categoriile</option>
                        <option value="Autoturisme" <?php if (isset($_GET['users']) && $_GET['users'] == 'Autoturisme') {echo 'selected="selected"';} ?>>Autoturisme</option>
                        <option value="Autoutilitare" <?php if (isset($_GET['users']) && $_GET['users'] == 'Autoutilitare') {echo 'selected="selected"';} ?>>Autoutilitare</option>
                        <option value="Autospecializate" <?php if (isset($_GET['users']) && $_GET['users'] == 'Autospecializate') {echo 'selected="selected"';} ?>>Autospecializate</option>
                        <option value="Utilaje" <?php if (isset($_GET['users']) && $_GET['users'] == 'Utilaje') {echo 'selected="selected"';} ?>>Utilaje</option>
                    </select>
                </div>
            </div> -->
            
            <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div>Marca: </div>
                    <select name="marca" class="vanzari_box" id="marca">
                        <option value="" <?php if (!isset($_GET['marca']) || empty($_GET['marca'])) {echo 'selected="selected"';} ?>>Toate mărcile</option>
                        <?php
                        while ($row_marca = mysqli_fetch_assoc($makes_result)) {
                            // Using the actual marca string value instead of ID
                            echo '<option value="' . $row_marca['marca'] . '"';
                            if (isset($_GET['marca']) && $row_marca['marca'] == $_GET['marca']) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $row_marca['marca'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div>Combustibil:</div>
                    <select name="combustibil" class="vanzari_box" id="combustibil">
                                                  <option value="fara" <?php if (!isset($_GET['combustibil']) || empty($_GET['combustibil']) || $_GET['combustibil'] == 'fara') {echo "selected=\"selected\"";} ?>>selectati</option> 
                                                  <option value="benzina" <?php if (isset($_GET['combustibil']) && $_GET['combustibil'] == 'benzina') {echo "selected=\"selected\"";} ?>>benzina</option>
                                                  <option value="motorina" <?php if (isset($_GET['combustibil']) && $_GET['combustibil'] == 'motorina') {echo "selected=\"selected\"";} ?>>motorina</option>
                                                  <option value="electric" <?php if (isset($_GET['combustibil']) && $_GET['combustibil'] == 'electric') {echo "selected=\"selected\"";} ?>>electric</option>
                                                </select>
                </div>
            </div>
            
            <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div>An fabricatie :</div>
                    <select name="an" class="vanzari_box" id="an">
                        <option value="" <?php if (!isset($_GET['an']) || empty($_GET['an'])) {echo 'selected="selected"';} ?>>selectati</option>      
<?php 

for ($i = 15; $i >= 0; $i--)  
  {
    $data_pv = date("Y")-$i;
     echo '<option value="'.$data_pv.'"'; 
     if (!(strcmp($data_pv, $_GET['an']))) {echo 'selected="selected"';} 
     echo ">".$data_pv." sau mai nou</option>";
    }                                                
?>                                                      
                                                      
                                                    </select>
                </div>
            </div>
            
            <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div>Pret:</div>
                    <select name="pret" class="vanzari_box" id="pret">
                                                  <option value="14000000" <?php if (!isset($_GET['pret']) || empty($_GET['pret']) || $_GET['pret'] == '14000000') {echo "selected=\"selected\"";} ?>>selectati</option>
                                                  <option value="5000" <?php if (isset($_GET['pret']) && $_GET['pret'] == '5000') {echo "selected=\"selected\"";} ?>>sub 5.000 euro</option>
                                                  <option value="8000" <?php if (isset($_GET['pret']) && $_GET['pret'] == '8000') {echo "selected=\"selected\"";} ?>>sub 8.000 euro</option>
                                                  <option value="13000" <?php if (isset($_GET['pret']) && $_GET['pret'] == '13000') {echo "selected=\"selected\"";} ?>>sub 13.000 euro</option>
                                                  <option value="20000" <?php if (isset($_GET['pret']) && $_GET['pret'] == '20000') {echo "selected=\"selected\"";} ?>>sub 20.000 euro</option>
                                                </select>
                </div>
            </div>
            
            <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div>Provenienta:</div>
                    <select name="prov_enienta" class="vanzari_box" id="prov_enienta">
                                                  <option value="fara" <?php if (!isset($_GET['prov_enienta']) || empty($_GET['prov_enienta']) || $_GET['prov_enienta'] == 'fara') {echo "selected=\"selected\"";} ?>>selectati</option>
                                                  <option value="Romania" <?php if (isset($_GET['prov_enienta']) && $_GET['prov_enienta'] == 'Romania') { echo "selected=\"selected\""; } ?>>Romania</option>
                                                  <option value="Import" <?php if (isset($_GET['prov_enienta']) && $_GET['prov_enienta'] == 'Import') { echo "selected=\"selected\""; } ?>>Import</option>                                                  
                                                </select>
                </div>
            </div>
            
            
            <div class="select-wrapper clearfix">

                <div class="form-element pull-right">
                    <input type="submit" value="Găseşte-mi maşina" class="find_new_vehicle md-button pull-right">
                </div>

            </div>
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
<button class="tbi-toggle-button" onclick="toggleTBIBank()" aria-label="Descoperă oferta TBI Bank">
    <span class="icon">🎉</span>
    <span class="text">1 tonă de carburant pe zi — campanie TBI Bank!</span>
    <span class="arrow">▼</span>
</button>

<!-- TBI Bank Content (collapsible) -->
<div class="search-form margin-top-20 padding-vertical-20 padding-left-20 padding-right-20 scroll_effect fadeInLeft sidebar-tbi-bank">
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
<div class="search-form margin-top-20 padding-vertical-20 padding-left-20 padding-right-20 scroll_effect rotateIn sidebar-vadrexim">
    <a style="font-weight: bold;color: red" href="https://www.vadrexim.ro/anvelope-iarna.html" target="_blank" rel="sponsored noopener">ANVELOPE IARNA</a> Ieftine & Jante la Vadrexim!
</div>
<!-- Vadrexim reclama END Here -->

<!-- Reclama START Here -->
<div class="search-form margin-top-20 padding-vertical-60 padding-left-20 padding-right-20 scroll_effect fadeInLeft sidebar-generic-ad">

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

<div id="curs-valutar-widget" class="search-form margin-top-20 padding-vertical-20 padding-left-20 padding-right-20 scroll_effect rotateIn">
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