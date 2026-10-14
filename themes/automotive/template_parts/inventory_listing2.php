<div class="content-wrap car_listings">
    <div class="row">
        <?php
        if ($totalRows_vanzare == 0) {
            ?>
            <section class="information_head">
                <h3 class="margin-top-25">🚗 Nu ai găsit mașina potrivită?</h3>
                <p><strong>Avem o veste bună:</strong> Poți primi gratuit o ofertă personalizată, exact așa cum o vrei, special pentru tine!</p>
                <p><strong>Furnizam doar mașini certificate, cu verificare tehnica generală, km reali și garanție până la 3 ani pentru motor si cutia de viteze.</strong> Garantăm un preț bun, o mașină verificată, ne ocupăm profesionist de toate actele iar la livrare îți dăm și garanție la ea. <strong>Pe bune!</strong> Doar lasă-ne mesaj cum vrei să fie, ai formularul mai jos pentru a completa datele necesare. <strong>În curând vei avea la dispoziție oferte de mașini, exact așa cum ai nevoie</strong>.</p>
                <p>📋 <strong>Ce se va întâmpla:</strong></p>
                <ul style="margin-left: 20px; margin-bottom: 20px;">
                    <li><strong>Completezi formularul</strong> cu detaliile mașinii pe care o cauți, fix așa cum ai vrea să fie.</li>
                    <li><strong>Vei fi contactat telefonic</strong> pentru a discuta mai multe detalii și a înțelege exact ce îți dorești;</li>
                    <li><strong>Verificăm în stocuri mașinile disponibile, selectăm cele mai bune variante</strong> iar tu primești în scurt timp o ofertă personalizată completă.</li>
                    <li><strong>Stabilim împreună cu tine</strong> cea mai bună variantă de finanțare (poți opta pentru dobandă de la 0%, avans de la zero%, un credit pentru venituri din străinătate, pentru firme noi, recent înființate sau un leasing super avantajos dacă ai firmă).</li>
		    <li><strong>Te ajutăm să conduci mașina de care ai nevoie!</strong></li>
                </ul>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="<?php echo BASE_PATH; ?>cerere_oferta.php" class="gradient_button" style="padding: 15px 30px; font-size: 18px; display: inline-block; text-decoration: none;">
                        <i class="fa fa-file-text"></i> Completează Formularul Aici
                    </a>
                </div>
                <p style="text-align: center; color: #666; font-style: italic;">💡 Sau modifică criteriile de căutare din formular pentru a vedea alte mașini disponibile în stoc.</p>
            </section>
            <?php
        }


        if ($totalRows_vanzare > 0) {
            ?>
        <table border="0" width="100%" align="center" style="margin-bottom: 20px;padding: 0">
                <tr>
                    <td width="30" align="left">
                        <?php 
                        $currentPage = "vanzari.php";
                        // Check if txtsrc is already in queryString, if not and textt has value, add it
                        $txtsrc_param = '';
                        if (!empty($textt) && stripos($queryString_vanzare, 'txtsrc=') === false) {
                            $txtsrc_param = '&amp;txtsrc=' . urlencode($textt);
                        }
                        
                        if ($pageNum_vanzare > 0) {
                            ?>
                            <a href="<?php printf("%s?pageNum_vanzare=%d%s%s", $currentPage, 0, $queryString_vanzare, $txtsrc_param); ?>" class="navLink">
                                <img data-no-retina src="<?= BASE_PATH ?>images/btn_first.png" border="0" alt="primul" />
                            </a><?php } ?>
                    </td>
                    <td width="30" align="left">
    <?php if ($pageNum_vanzare > 0) { ?>
                            <a href="<?php printf("%s?pageNum_vanzare=%d%s%s", $currentPage, max(0, $pageNum_vanzare - 1), $queryString_vanzare, $txtsrc_param); ?>" class="navLink">
                                <img data-no-retina src="<?= BASE_PATH ?>images/btn_prev.png" border="0" alt="anteriorul" />
                            </a><?php } ?>
                    </td>
                    <td>&nbsp;</td>
                    <td width="30" align="right">
    <?php if ($pageNum_vanzare < $totalPages_vanzare) { ?>
                            <a href="<?php printf("%s?pageNum_vanzare=%d%s%s", $currentPage, min($totalPages_vanzare, $pageNum_vanzare + 1), $queryString_vanzare, $txtsrc_param); ?>" class="navLink">
                                <img data-no-retina src="<?= BASE_PATH ?>images/btn_next.png" border="0" alt="urmatorul" />
                            </a><?php } ?>
                    </td>
                    <td width="30" align="right">
    <?php if ($pageNum_vanzare < $totalPages_vanzare) { ?>
                            <a href="<?php printf("%s?pageNum_vanzare=%d%s%s", $currentPage, $totalPages_vanzare, $queryString_vanzare, $txtsrc_param); ?>" class="navLink">
                                <img data-no-retina src="<?= BASE_PATH ?>images/btn_last.png" border="0" alt="ultimul" />
                            </a><?php } ?>
                    </td>
                </tr>
            </table>



        <?php
                        $image_counter = 0; // Counter for lazy loading optimization
                        do {
                            $image_counter++;
                            $is_above_fold = ($image_counter <= 4); // First 4 images load immediately
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
                                <a class="inventory" href="vanzari_detail.php?id=<?php echo $row_vanzare['id']; ?>&pic=<?php echo getCarFirstPicture($row_vanzare['id'], $connection)['file']; ?>">
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
                                    <img data-no-retina 
                                         src="foto/<?php echo getCarFirstPicture($row_vanzare['id'], $connection)['file']; ?>" 
                                         class="preview" 
                                         alt="<?php echo $car_image_alt; ?>"
                                         <?php if (!$is_above_fold): ?>loading="lazy"<?php endif; ?>
                                         <?php if ($is_above_fold && $image_counter == 1): ?>fetchpriority="high"<?php endif; ?>
                                         width="300" 
                                         height="200">
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
                        } while ($row_vanzare = mysqli_fetch_assoc($vanzare));
                        ?>
        
        


            <table border="0" width="100%" align="center">
                <tr>
                    <td width="30" align="left">
    <?php if ($pageNum_vanzare > 0) { ?>
                            <a href="<?php printf("%s?pageNum_vanzare=%d%s%s", $currentPage, 0, $queryString_vanzare, $txtsrc_param); ?>" class="navLink">
                                <img data-no-retina src="images/btn_first.png" border="0" alt="primul" />
                            </a><?php } ?>
                    </td>
                    <td width="30" align="left">
    <?php if ($pageNum_vanzare > 0) { ?>
                            <a href="<?php printf("%s?pageNum_vanzare=%d%s%s", $currentPage, max(0, $pageNum_vanzare - 1), $queryString_vanzare, $txtsrc_param); ?>" class="navLink">
                                <img data-no-retina src="images/btn_prev.png" border="0" alt="anteriorul" />
                            </a><?php } ?>
                    </td>
                    <td>&nbsp;</td>
    <td width="30" align="right">
    <?php if ($pageNum_vanzare < $totalPages_vanzare) { ?>
                            <a href="<?php printf("%s?pageNum_vanzare=%d%s%s", $currentPage, min($totalPages_vanzare, $pageNum_vanzare + 1), $queryString_vanzare, $txtsrc_param); ?>" class="navLink">
                                <img data-no-retina src="images/btn_next.png" border="0" alt="urmatorul" />
                            </a><?php } ?>
                    </td>
                    <td width="30" align="right">
    <?php if ($pageNum_vanzare < $totalPages_vanzare) { ?>
                            <a href="<?php printf("%s?pageNum_vanzare=%d%s%s", $currentPage, $totalPages_vanzare, $queryString_vanzare, $txtsrc_param); ?>" class="navLink">
                                <img data-no-retina src="images/btn_last.png" border="0" alt="ultimul" />
                            </a><?php } ?>
                    </td>
                </tr>
            </table>



            <?php
        }
        ?>
    </div>
</div>