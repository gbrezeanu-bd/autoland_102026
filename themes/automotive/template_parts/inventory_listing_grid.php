<?php
/**
 * Lista de mașini din vanzari.php: carduri în grilă, sortare, filtre active și paginare numerotată.
 * Variabile așteptate (din vanzari.php): $vz_f (filtrele), $vz_rows (mașinile paginii), $totalRows_vanzare,
 * $totalPages_vanzare, $pageNum_vanzare. Logica de date e în includes/vanzari_catalog.php.
 */
$vz_label_fuel = array('benzina' => 'Benzină', 'motorina' => 'Motorină', 'hybrid' => 'Hybrid', 'electric' => 'Electric');
$vz_h = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };

$vz_chips = vz_active_chips($vz_f);
$vz_cat_counts = vz_facet($vz_f, 'users');
$vz_cat_total = array_sum($vz_cat_counts);
$vz_first = $totalRows_vanzare > 0 ? $startRow_vanzare + 1 : 0;
$vz_last = min($totalRows_vanzare, $startRow_vanzare + count($vz_rows));
// Un card de mașină. $first = primul de pe pagină (imagine încărcată cu prioritate), $eager = fără încărcare întârziată
$vz_card = function ($car, $first, $eager, $tag) use ($vz_label_fuel, $vz_h, $connection) {
    $id = (int)$car['id'];
    $url = BASE_PATH . 'vanzari_detail.php?id=' . $id;
    $name = vz_car_name($car);
    $pret = (int)$car['pret'];
    $pret_vechi = (int)$car['pret_vechi'];
    $has_old = $pret_vechi > $pret && $pret > 0;
    $rata = vz_monthly_rate($pret);
    $km = (int)$car['km'];
    $cp = (int)$car['cp'];
    $fuel = isset($vz_label_fuel[$car['combustibil']]) ? $vz_label_fuel[$car['combustibil']] : ucfirst($car['combustibil']);
    $cutie = '';
    if (stripos((string)$car['cutie'], 'auto') === 0) { $cutie = 'Automată'; }
    elseif (stripos((string)$car['cutie'], 'manual') === 0) { $cutie = 'Manuală'; }
    $cc = (int)$car['cilindri'];
    $engine = array();
    if ($cc >= 600) { $engine[] = number_format($cc / 1000, 1, '.', '') . 'L'; }
    if ($cp > 0) { $engine[] = $cp . ' CP'; }
    $euro = (int)$car['euro'] >= 3 ? 'Euro ' . (int)$car['euro'] : '';
    $prov = $car['prov_enienta'] === 'Import' ? 'Import' : ($car['prov_enienta'] === 'Romania' ? 'România' : '');
    $notable = array();
    if (in_array(strtolower((string)$car['tractiune']), array('integrala', '4x4'), true)) { $notable[] = '4x4'; }
    if ((int)$car['locuri'] >= 6) { $notable[] = (int)$car['locuri'] . ' locuri'; }
    $alt = trim($car['marca'] . ' ' . $car['tip']) . ($car['an'] ? ' - ' . $car['an'] : '') . ($pret ? ' - ' . number_format($pret, 0, ',', '.') . ' EUR' : '');
    $photo = getCarFirstPicture($id, $connection);
    ?>
        <article class="vz-card">
            <a class="vz-media" href="<?php echo $url; ?>" tabindex="-1" aria-hidden="true">
                <img data-no-retina src="<?php echo BASE_PATH; ?>foto/<?php echo $vz_h($photo['file']); ?>" alt="<?php echo $vz_h($alt); ?>" width="300" height="200"
                     <?php echo $eager ? '' : 'loading="lazy"'; ?> <?php echo $first ? 'fetchpriority="high"' : ''; ?>>
                <span class="vz-badges">
                    <?php if ($car['oferta_speciala'] == 1): ?><span class="vz-badge vz-badge-offer">Ofertă specială</span><?php endif; ?>
                </span>
            </a>
            <div class="vz-body">
                <<?php echo $tag; ?> class="vz-name"><a href="<?php echo $url; ?>" title="<?php echo $vz_h(trim($car['marca'] . ' ' . $car['tip'])); ?>"><?php echo $vz_h($name); ?></a></<?php echo $tag; ?>>
                <ul class="vz-specs">
                    <?php if ($car['an']): ?><li><?php echo (int)$car['an']; ?></li><?php endif; ?>
                    <?php if ($km > 0): ?><li><?php echo number_format($km, 0, ',', '.'); ?> km</li><?php endif; ?>
                    <?php if ($fuel !== ''): ?><li><?php echo $vz_h($fuel); ?></li><?php endif; ?>
                    <?php if ($cutie !== ''): ?><li><?php echo $cutie; ?></li><?php endif; ?>
                    <?php if ($engine): ?><li><?php echo $vz_h(implode(' · ', $engine)); ?></li><?php endif; ?>
                    <?php if ($euro !== ''): ?><li><?php echo $vz_h($euro); ?></li><?php endif; ?>
                    <?php foreach ($notable as $vz_n): ?><li class="is-key"><?php echo $vz_h($vz_n); ?></li><?php endforeach; ?>
                    <?php if ($prov !== ''): ?><li><?php echo $vz_h($prov); ?></li><?php endif; ?>
                </ul>
                <div class="vz-buy">
                    <div class="vz-price">
                        <?php if ($has_old): ?><span class="vz-price-old"><?php echo number_format($pret_vechi, 0, ',', '.'); ?> €</span><?php endif; ?>
                        <span class="vz-price-now<?php echo ($car['oferta_speciala'] == 1 || $has_old) ? ' is-offer' : ''; ?>"><?php echo $pret > 0 ? number_format($pret, 0, ',', '.') . ' €' : 'Preț la cerere'; ?></span>
                        <?php if ($rata > 0): ?><span class="vz-rate">sau rată de ~<?php echo $rata; ?> €/lună</span><?php endif; ?>
                    </div>
                    <span class="vz-more">Vezi detalii <i class="fa fa-angle-right"></i></span>
                </div>
                <div class="vz-meta">Cod #S10<?php echo $id; ?></div>
            </div>
        </article>
    <?php
};
$vz_i = 0;
?>
<div class="vz-list" id="vz-list">

    <?php if ($vz_cat_total > 0 || $vz_f['users'] !== ''): ?>
    <nav class="vz-cats" aria-label="Categorii">
        <a href="<?php echo vz_url($vz_f, array('users' => '')); ?>" class="vz-cat<?php echo $vz_f['users'] === '' ? ' is-active' : ''; ?>">Toate</a>
        <?php foreach (vz_categories() as $vz_cat): ?>
            <?php if (isset($vz_cat_counts[$vz_cat]) || $vz_f['users'] === $vz_cat): ?>
            <a href="<?php echo vz_url($vz_f, array('users' => $vz_cat)); ?>" class="vz-cat<?php echo $vz_f['users'] === $vz_cat ? ' is-active' : ''; ?>"><?php echo $vz_h($vz_cat); ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
    <?php endif; ?>

    <div class="vz-toolbar">
        <form class="vz-sort" action="<?php echo BASE_PATH . vz_base(); ?>" method="get">
            <?php foreach ($vz_f as $vz_k => $vz_v): if ($vz_k === 'sort' || $vz_k === 'pp' || $vz_k === 'oferta' || $vz_v === '' || $vz_v === 0) { continue; } ?>
                <input type="hidden" name="<?php echo $vz_h($vz_k); ?>" value="<?php echo $vz_h($vz_v); ?>">
            <?php endforeach; ?>
            <span class="vz-sort-label">Sortează</span>
            <select name="sort" id="vz-sort-select" aria-label="Sortează mașinile" onchange="this.form.submit()">
                <?php foreach (vz_sort_options() as $vz_val => $vz_lab): ?>
                    <option value="<?php echo $vz_val; ?>"<?php echo $vz_f['sort'] === $vz_val ? ' selected' : ''; ?>><?php echo $vz_h($vz_lab); ?></option>
                <?php endforeach; ?>
            </select>
            <span class="vz-sort-label vz-perpage">Pe pagină</span>
            <select name="pp" class="vz-perpage" aria-label="Mașini pe pagină" onchange="this.form.submit()">
                <?php foreach (vz_per_page_options() as $vz_n): ?>
                    <option value="<?php echo $vz_n; ?>"<?php echo $vz_f['pp'] === $vz_n ? ' selected' : ''; ?>><?php echo $vz_n; ?></option>
                <?php endforeach; ?>
            </select>
            <noscript><button type="submit">Aplică</button></noscript>
        </form>
    </div>

    <?php if ($totalRows_vanzare > 0): ?>
    <ul class="vz-trust">
        <li><i class="fa fa-check-circle"></i> Garanție 1–3 ani, în scris</li>
        <li><i class="fa fa-check-circle"></i> Verificare tehnică și km reali</li>
        <li><i class="fa fa-check-circle"></i> Livrare în toată țara</li>
        <li><i class="fa fa-check-circle"></i> Rate / leasing</li>
    </ul>
    <?php endif; ?>

    <?php if ($vz_chips): ?>
    <div class="vz-active" aria-label="Filtre active">
        <?php foreach ($vz_chips as $vz_chip): ?>
            <a class="vz-chip" href="<?php echo vz_url($vz_f, array($vz_chip[1] => '')); ?>" title="Elimină filtrul"><?php echo $vz_h($vz_chip[0]); ?> <span aria-hidden="true">&times;</span></a>
        <?php endforeach; ?>
        <a class="vz-reset" href="<?php echo vz_url(array('sort' => $vz_f['sort'])); ?>">Șterge toate filtrele</a>
    </div>
    <?php endif; ?>

    <?php if ($totalRows_vanzare == 0): ?>
    <section class="vz-empty">
        <?php
        $vz_brand = $vz_f['marca'];
        $vz_brand_total = $vz_brand !== '' ? vz_count(array_merge(vz_blank_filters(), array('marca' => $vz_brand))) : 0;
        ?>
        <?php if (!empty($vz_f['oferta']) && !$vz_chips): ?>
            <h3>🚗 Momentan nu avem oferte speciale active</h3>
            <p>Ofertele se schimbă des, așa că revino curând. Până atunci, poți vedea toate ofertele auto sau ne poți spune ce cauți, iar îți pregătim noi o ofertă personalizată.</p>
            <p><a class="vz-reset-btn" href="<?php echo BASE_PATH; ?>vanzari.php">Vezi toate ofertele auto</a></p>
        <?php elseif ($vz_brand !== '' && $vz_brand_total == 0): ?>
            <h3>🚗 Momentan nu avem anunțuri pentru <?php echo $vz_h($vz_brand); ?></h3>
            <p>Nu avem acum nicio ofertă <?php echo $vz_h($vz_brand); ?> publicată, dar putem aduce mașina la comandă. Spune-ne ce îți dorești și o căutăm noi pentru tine, iar tu primești o ofertă personalizată.</p>
            <p><a class="vz-reset-btn" href="<?php echo vz_url(array()); ?>">Vezi toate ofertele auto</a></p>
        <?php elseif ($vz_brand !== ''): ?>
            <h3>🚗 Nu avem <?php echo $vz_h($vz_brand); ?> cu aceste criterii</h3>
            <p>Avem <?php echo $vz_brand_total; ?> <?php echo $vz_brand_total == 1 ? 'ofertă' : 'oferte'; ?> <?php echo $vz_h($vz_brand); ?>, dar nici una nu se potrivește cu toate filtrele alese. Încearcă să relaxezi unul dintre ele.</p>
            <p><a class="vz-reset-btn" href="<?php echo vz_url(array('marca' => $vz_brand)); ?>">Vezi toate mașinile <?php echo $vz_h($vz_brand); ?></a></p>
        <?php else: ?>
            <h3>🚗 Nu am găsit mașini pentru criteriile alese</h3>
            <?php if ($vz_chips): ?>
                <p><a class="vz-reset-btn" href="<?php echo vz_url(array()); ?>">Vezi toate ofertele auto</a></p>
            <?php endif; ?>
        <?php endif; ?>
        <p><strong>Avem o veste bună:</strong> poți primi gratuit o ofertă personalizată, exact așa cum o vrei, special pentru tine!</p>
        <p><strong>Furnizăm doar mașini certificate, cu verificare tehnică generală, km reali și garanție până la 3 ani pentru motor și cutia de viteze.</strong> Garantăm un preț bun, o mașină verificată, ne ocupăm profesionist de toate actele, iar la livrare îți dăm și garanție la ea. <strong>Pe bune!</strong> Lasă-ne un mesaj cum vrei să fie mașina, iar în curând vei avea la dispoziție oferte exact pe măsura nevoilor tale.</p>
        <ul>
            <li><strong>Completezi formularul</strong> cu detaliile mașinii pe care o cauți.</li>
            <li><strong>Vei fi contactat telefonic</strong> pentru a discuta detaliile și a înțelege exact ce îți dorești.</li>
            <li><strong>Căutăm printre ofertele disponibile</strong> sau aducem mașina la comandă, selectăm cele mai bune variante, iar tu primești în scurt timp o ofertă personalizată completă.</li>
            <li><strong>Stabilim împreună cu tine</strong> cea mai bună variantă de finanțare (dobândă de la 0%, avans de la zero, credit pentru venituri din străinătate, pentru firme noi sau un leasing avantajos dacă ai firmă).</li>
        </ul>
        <p class="vz-empty-cta"><a href="<?php echo BASE_PATH; ?>cerere_oferta.php" class="vz-btn"><i class="fa fa-file-text"></i> Completează formularul aici</a></p>
    </section>
    <?php else: ?>

    <?php if ($vz_overview): ?>
        <?php foreach ($vz_sections as $vz_cat => $vz_cat_rows): ?>
    <section class="vz-section">
        <h2 class="vz-section-title"><?php echo $vz_h($vz_cat); ?></h2>
        <div class="vz-grid vz-grid-preview">
            <?php foreach ($vz_cat_rows as $car): $vz_i++; $vz_card($car, $vz_i == 1, $vz_i <= 3, 'h3'); endforeach; ?>
        </div>
        <p class="vz-section-more"><a class="vz-btn-outline" href="<?php echo vz_url($vz_f, array('users' => $vz_cat)); ?>">Mai multe oferte <?php echo $vz_h(mb_strtolower($vz_cat, 'UTF-8')); ?> <i class="fa fa-angle-right"></i></a></p>
    </section>
        <?php endforeach; ?>
    <?php else: ?>
    <div class="vz-grid">
        <?php foreach ($vz_rows as $car): $vz_i++; $vz_card($car, $vz_i == 1, $vz_i <= 3, 'h2'); endforeach; ?>
    </div>
    <?php if (!$vz_overview && $totalPages_vanzare > 0): ?>
    <nav class="vz-pager" aria-label="Paginare">
        <?php if ($pageNum_vanzare > 0): ?>
            <a class="vz-page-link vz-page-prev" rel="prev" href="<?php echo vz_url($vz_f, array(), $pageNum_vanzare - 1); ?>">&laquo; <span>Înapoi</span></a>
        <?php endif; ?>
        <?php foreach (vz_page_window($pageNum_vanzare, $totalPages_vanzare) as $vz_p): ?>
            <?php if ($vz_p === null): ?>
                <span class="vz-page-gap">…</span>
            <?php elseif ($vz_p == $pageNum_vanzare): ?>
                <span class="vz-page-link is-current" aria-current="page"><?php echo $vz_p + 1; ?></span>
            <?php else: ?>
                <a class="vz-page-link" href="<?php echo vz_url($vz_f, array(), $vz_p); ?>"><?php echo $vz_p + 1; ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($pageNum_vanzare < $totalPages_vanzare): ?>
            <a class="vz-page-link vz-page-next" rel="next" href="<?php echo vz_url($vz_f, array(), $pageNum_vanzare + 1); ?>"><span>Înainte</span> &raquo;</a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>

    <?php endif; ?>
    <?php endif; ?>
</div>
