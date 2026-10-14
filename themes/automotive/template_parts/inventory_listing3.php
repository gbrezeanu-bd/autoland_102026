<div class="content-wrap car_listings">
                    <div class="row">
                        <?php
                    if ($totalRows_vanzare == 0) {
                        ?>
                        <section class="information_head">
                            <h3 class="margin-top-25">Ne pare rau</h3>
                            <p>Ne pare rau, dar criteriul de selectie introdus nu permite selectia vreunui autovehicul din baza de date Autoland.</p>
                            <p>Va rugam folositi formularul din stanga pentru a introduce un alt criteriu de selectie. </p>
                            <p>Va multumim ! </p>
                        </section>
                        <?php
                    } else {
                        ?>
                        <!-- Pagination START Here -->
                        <div class="margin-bottom-20 margin-top-30 text-center">
                        <?php echo $pager['pager_html']; ?>
                            
                            <?php
                            //var_dump($_SERVER['REQUEST_URI']);
                            //var_dump($_GET);
                            ?>
                        </div>
                        <!-- Pagination START Here -->
                        <?php
                        
                        while ($row_vanzare = mysqli_fetch_assoc($pager['pager_resultset'])) {
                            ?>
                            <!-- Inventory Listing START Here -->
                            <div class="inventory margin-bottom-20 clearfix scroll_effect fadeIn" style="visibility: visible; animation-name: fadeIn;">
                                <?php
                                if($row_vanzare['oferta_speciala']==1){
                                    ?>
<!--                                <div class="angled_badge red">
                                    <span>La reducere</span>
                                </div>-->
                                    <?php
                                }
                                ?>
                                <a class="inventory" href="<?php echo BASE_PATH; ?>vanzari_detail.php?id=<?php echo (int)$row_vanzare['id']; ?>">
                                    <div class="title"><?php echo $row_vanzare['marca'] . ' ' . $row_vanzare['tip']; ?></div>
                                    <div class="price"><b>Preţ:</b><br>
                                        <?php
                                        if($row_vanzare['oferta_speciala']==1){
                                            if ($row_vanzare['pret_vechi'] != 0) {
                                            ?>
                                                <div class="figure"><span style="text-decoration: line-through"><?php echo $row_vanzare['pret_vechi']; ?> €</span><br></div>
                                                <div class="figure"><span style="color: red"><?php echo $row_vanzare['pret']; ?> €</span><br></div>
                                                <?php
                                            } else {
                                                ?>
                                                <div class="figure"><span style="color: red"><?php echo $row_vanzare['pret']; ?> €</span><br></div>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                                <div class="figure"><span><?php echo $row_vanzare['pret']; ?> €</span><br></div>
                                            <?php
                                        }
                                        
                                        ?>
                                    </div>
                                    
                                    <?php
                                    // Build alt text for image from car title parts (similar to vanzari_detail.php)
                                    $car_alt_parts = [];
                                    $car_alt_parts[] = trim($row_vanzare['marca'] . " " . $row_vanzare['tip']);
                                    if (!empty($row_vanzare['an'])) {
                                        $car_alt_parts[] = $row_vanzare['an'];
                                    }
                                    if (!empty($row_vanzare['pret'])) {
                                        $car_alt_parts[] = number_format($row_vanzare['pret'], 0, ',', '.') . " EUR";
                                    }
                                    $car_image_alt = htmlspecialchars(implode(" - ", $car_alt_parts));
                                    ?>
                                    <img data-no-retina src="foto/<?php echo getCarFirstPicture($row_vanzare['id'], $connection)['file']; ?>" class="preview" alt="<?php echo $car_image_alt; ?>">
                                    <table class="options-primary">
                                        <tbody><tr>
                                                <td class="option primary">Cod:</td>
                                                <td class="spec">#S10<?php echo $row_vanzare['id']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="option primary">Categorie:</td>
                                                <td class="spec"><?php echo $row_vanzare['cat']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="option primary">Subcategorie:</td>
                                                <td class="spec"><?php echo $row_vanzare['subcateg']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="option primary">Provenienta:</td>
                                                <td class="spec"><?php echo $row_vanzare['prov_enienta']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="option primary">Combustibil:</td>
                                                <td class="spec"><?php echo $row_vanzare['combustibil']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="option primary">An:</td>
                                                <td class="spec"><?php echo $row_vanzare['an']; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="options-secondary">
                                        <?php echo $row_vanzare['detalii']; ?>
                                    </div>
                                    
                                    <div class="view-details gradient_button"><i class="fa fa-plus-circle"></i> Vezi detalii ofertă</div>
                                    <div class="clearfix"></div>
                                </a>
                            </div>
                            <!-- Inventory Listing END Here -->
                            <?php
                        };
                        ?>
                        <!-- Pagination START Here -->
                        <div class="text-center">
                        <?php echo $pager['pager_html']; ?>
                        </div>
                        <!-- Pagination START Here -->
                        <?php
                    }
                    ?>
                    </div>
                </div>