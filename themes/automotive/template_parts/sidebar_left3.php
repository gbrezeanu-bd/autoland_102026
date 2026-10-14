<?php
if(!isset($_GET['txtsrc'])){
    $_GET['txtsrc']='';
}
?>
<!-- InventoryBox START Here -->
<div class="search-inventory">
    <h4 class="margin-bottom-25 margin-top-none"><strong>CAUTĂ</strong> MAŞINI</h4>
    <div class="search-form margin-top-20 padding-vertical-20">
        <form name="findmycar" id="findmycar" method="GET" action="vanzari.php" target="_self" onsubmit="cleanEmptyParams(this)">
            <div class="select-wrapper clearfix">
                <div class="col-md-12 about_team margin-bottom-10">
                    <div class="my-dropdown">
                        <input name="txtsrc" id="txtsrc" class="full-width" type="search" value="<?php echo (isset($_GET['txtsrc']) ? $_GET['txtsrc'] : ''); ?>" placeholder="Cuvant cheie">
                    </div>
                </div>
            </div>



            <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div class="my-dropdown">
                        <select name="categorie" class="css-dropdowns" tabindex="1" onchange="updateSubCateg(this.value);showUser(this.value);" onload="updateSubCateg(this.value)">
                            <?php
                            $selected = (!(strcmp("Selecteaza Categorie", $categorie))) ? 'selected' : '';
                            ?>

                            <option value="Selecteaza Categorie" disabled="" selected="<?php echo $selected; ?>">Selecteaza Categorie</option>

                            <?php
                            // get categories from database
                            $query_categories = "SELECT DISTINCT(den) FROM `zone` WHERE `den`<>'A HEADER' ORDER BY den ASC";
                            $query_categories_results = $connection->query($query_categories) or die($connection->error);
                            $catOptions = '';


                            while ($query_categories_result = mysqli_fetch_assoc($query_categories_results)) {
                                $select_selected = (!strcmp($query_categories_result['den'], $categorie)) ? ' selected="selected"' : '';


                                $catOptions .= "<option{$select_selected}>{$query_categories_result['den']}</option>" . PHP_EOL;
                            }
                            $rows = mysqli_num_rows($query_categories_results);
                            if ($rows > 0) {
                                mysqli_data_seek($query_categories_results, 0);
                                $query_categories_results = mysqli_fetch_assoc($query_categories_results);
                            }
                            echo $catOptions;
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div class="my-dropdown subcateg">
                        <select name="subcateg" class="css-dropdowns" tabindex="1" >
                            <option value="" disabled="" selected="selected">Subcategorie</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div class="my-dropdown marcat">
                        <select name="marcat" class="css-dropdowns" tabindex="1" >
                            <option value="">Marca</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div class="my-dropdown">
                        <select name="combustibil" class="css-dropdowns" tabindex="1" >
                            <option value="" disabled="" selected="selected">Combustibil</option>
                            <?php
                            if (!isset($_GET['combustibil'])) {
                                $_GET['combustibil'] = '';
                            }
                            ?>
                            <option value="benzina" <?php
                            if (!(strcmp("benzina", $_GET['combustibil']))) {
                                echo "selected=\"selected\"";
                            }
                            ?>>benzina</option>
                            <option value="motorina" <?php
                            if (!(strcmp("motorina", $_GET['combustibil']))) {
                                echo "selected=\"selected\"";
                            }
                            ?>>motorina</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div class="my-dropdown">
                        <select name="an" class="css-dropdowns an" tabindex="1" >
                            <option value="" disabled="" selected="selected">An de fabricatie</option>
                            <?php
                            // we need distinct values of 'an' from 'masini' table
                            foreach ($query_an_result_arr as $key => $row) {
                                //var_dump($row);                                                
                                echo '<option value="' . $row['an'] . '"';
                                if (!(strcmp($row['an'], $_GET['an']))) {
                                    echo 'selected="selected"';
                                }
                                echo ">" . $row['an'] . " sau mai nou</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div class="my-dropdown pret">
                        <select name="pret" class="css-dropdowns" tabindex="1" >
                            <?php
                            if (!isset($_GET['pret'])) {
                                $_GET['pret'] = '14000000';
                            }
                            ?>
                            <option value="14000000" disabled="" selected="selected">Pret</option>
                            <option value="5000" <?php
                            if (!(strcmp(5000, $_GET['pret']))) {
                                echo "selected=\"selected\"";
                            }
                            ?>>sub 5.000 euro</option>
                            <option value="8000" <?php
                            if (!(strcmp(8000, $_GET['pret']))) {
                                echo "selected=\"selected\"";
                            }
                            ?>>sub 8.000 euro</option>
                            <option value="13000" <?php
                            if (!(strcmp(13000, $_GET['pret']))) {
                                echo "selected=\"selected\"";
                            }
                            ?>>sub 13.000 euro</option>
                            <option value="20000" <?php
                            if (!(strcmp(20000, $_GET['pret']))) {
                                echo "selected=\"selected\"";
                            }
                            ?>>sub 20.000 euro</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="select-wrapper clearfix">
                <div class="col-md-12">
                    <div class="my-dropdown prov_enienta">
                        <select name="prov_enienta" class="css-dropdowns" tabindex="1" >
                            <option value="fara" selected="" disabled="disabled">Provenienta</option>
                            <?php
                            if (!isset($_GET['prov_enienta'])) {
                                $_GET['prov_enienta'] = '';
                            }
                            ?>
                            <option value="Romania" <?php
                            if (!(strcmp("Romania", $_GET['prov_enienta']))) {
                                echo "selected=\"selected\"";
                            }
                            ?>>Romania</option>
                            <option value="Import" <?php
                            if (!(strcmp("Import", $_GET['prov_enienta']))) {
                                echo "selected=\"selected\"";
                            }
                            ?>>Import</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="clear"></div>
            <div class="select-wrapper clearfix">

                <div class="form-element pull-right">
                    <input type="submit" value="Găseşte-mi maşina" class="find_new_vehicle md-button pull-right">
                </div>

            </div>
        </form>
    </div>
</div>
<!-- InventoryBox END Here -->

<!-- Curs Valutar START Here -->
<?php
$query_curs_valutar_curent = "SELECT * FROM curs_valutar ORDER BY date DESC LIMIT 1";
$curs_valutar_curent = $connection->query($query_curs_valutar_curent) or die($connection->error);
$current_exchange = mysqli_fetch_assoc($curs_valutar_curent);

$query_curs_valutar_anterior = "SELECT * FROM curs_valutar ORDER BY date DESC LIMIT 1,1";
$curs_valutar_anterior = $connection->query($query_curs_valutar_anterior) or die($connection->error);
$past_exchange = mysqli_fetch_assoc($curs_valutar_anterior);

$diferenta_EUR = round(floatval($current_exchange['EUR']) - floatval($past_exchange['EUR']), 4);
$diferenta_EUR = round(floatval($current_exchange['EUR']) - floatval($past_exchange['EUR']), 4);
$diferenta_USD = round(floatval($current_exchange['USD']) - floatval($past_exchange['USD']), 4);
$diferenta_HUF = round(floatval($current_exchange['HUF']) - floatval($past_exchange['HUF']), 4);
$diferenta_CZK = round(floatval($current_exchange['CZK']) - floatval($past_exchange['CZK']), 4);
$diferenta_PLN = round(floatval($current_exchange['PLN']) - floatval($past_exchange['PLN']), 4);
?>

<div class="search-form margin-top-20 padding-vertical-20 padding-left-20 padding-right-20 scroll_effect rotateIn">
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

<!-- Reclama START Here -->

<div class="search-form margin-top-20 padding-vertical-20 padding-left-20 padding-right-20 scroll_effect fadeInLeft">
    <img class="center-block" src="<?= BASE_PATH ?>images/piese%20auto%20autoa.png" alt="" data-hoverimg="<?= BASE_PATH ?>images/piese%20auto%20autoa.png">
</div>
<div class="search-form margin-top-20 padding-vertical-60 padding-left-20 padding-right-20 scroll_effect fadeInLeft">

    <div class="featured">
        <h5>Spatiu reclama</h5>
        <hr class="my-4">
        <p>Reclama ta poate fi aici pentru doar 24 euro / an</p>
        <a class="default-btn" href="<?php echo BASE_PATH; ?>contact.php" role="button">Vreau</a>
    </div>

</div>
<!-- Reclama END Here -->

<!-- added by Gabriela Brezeanu -->
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
</script>