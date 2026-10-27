<?php

/**
 * Cardul unui anunt din listele de admin (view_vanzare2.php, view_vanzare_new.php).
 * Pe langa datele din `masini`, afiseaza poza principala, numarul de poze si videouri,
 * datele tehnice din `for_csv` si avertizari pentru anunturile incomplete.
 */

/**
 * Datele suplimentare pentru anunturile de pe pagina curenta, citite cu cate o interogare pentru toata pagina.
 * Primeste interogarea paginii (cu LIMIT) si intoarce un tablou indexat dupa id.
 */
function adm_info_anunturi($connection, $query_pagina)
{
    $info = [];
    $r = $connection->query($query_pagina);
    while ($r && ($x = $r->fetch_assoc())) {
        $info[(int)$x['id']] = ['poza' => '', 'poze' => 0, 'video' => 0, 'csv' => []];
    }
    if (!$info) {
        return $info;
    }
    $ids = implode(',', array_keys($info));

    $r = $connection->query("SELECT oferta, file FROM fotov WHERE oferta IN ($ids) ORDER BY oferta, nota ASC, id ASC");
    while ($r && ($x = $r->fetch_assoc())) {
        $o = (int)$x['oferta'];
        if ($info[$o]['poze']++ === 0) {
            $info[$o]['poza'] = $x['file'];
        }
    }

    $r = $connection->query("SELECT oferta, COUNT(*) AS n FROM fotov_video WHERE oferta IN ($ids) GROUP BY oferta");
    while ($r && ($x = $r->fetch_assoc())) {
        $info[(int)$x['oferta']]['video'] = (int)$x['n'];
    }

    $r = $connection->query("SELECT id, kilometraj, putere_motor, transmisie, moneda, stare_csv FROM for_csv WHERE id IN ($ids)");
    while ($r && ($x = $r->fetch_assoc())) {
        $info[(int)$x['id']]['csv'] = $x;
    }
    return $info;
}

function adm_h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Afiseaza cardul unui anunt. $url_modifica si $url_sterge sunt paginile de editare si stergere folosite de lista respectiva.
 */
function adm_card($row, $info, $url_modifica, $url_sterge = null)
{
    $id = (int)$row['id'];
    if ($url_sterge === null) {
        $url_sterge = 'view_vanzare2.php?del=' . $id;
    }
    $i = isset($info[$id]) ? $info[$id] : ['poza' => '', 'poze' => 0, 'video' => 0, 'csv' => []];
    $csv = $i['csv'];
    $on = $row['stare_anunt'] == '1';

    $tehnic = [];
    if (!empty($csv['kilometraj'])) {
        $tehnic[] = ['Km', number_format((int)$csv['kilometraj'], 0, ',', '.')];
    }
    if (!empty($csv['putere_motor'])) {
        $tehnic[] = ['Putere', (int)$csv['putere_motor'] . ' CP'];
    }
    if (!empty($csv['transmisie'])) {
        $tehnic[] = ['Cutie', $csv['transmisie']];
    }

    $lipsuri = [];
    if ($i['poze'] == 0) {
        $lipsuri[] = 'Fără poze';
    }
    if ((int)$row['pret'] <= 0) {
        $lipsuri[] = 'Fără preț';
    }
    if (trim(strip_tags((string)$row['detalii'])) === '') {
        $lipsuri[] = 'Fără detalii';
    }
    if (trim((string)$row['title']) === '') {
        $lipsuri[] = 'Fără titlu SEO';
    }

    $moneda = !empty($csv['moneda']) ? $csv['moneda'] : 'EUR';
    ?>
              <div class="adm-card<?php echo $on ? '' : ' is-off'; ?>">
                <div class="adm-card-id">ID <strong><?php echo $id; ?></strong>
                  <span class="adm-badge <?php echo $on ? 'is-on' : 'is-off'; ?>"><?php echo $on ? 'ON' : 'OFF'; ?></span>
                  <?php if ($row['oferta_speciala'] == '1') { ?><span class="adm-badge is-offer">OFERTĂ</span><?php } ?>
                  <?php if (!empty($csv['stare_csv'])) { ?><span class="adm-badge is-csv" title="Inclus în exportul CSV">CSV</span><?php } ?>
                </div>
                <a class="adm-card-photo" href="<?php echo adm_h($url_modifica); ?>">
                  <?php if ($i['poza'] !== '') { ?>
                  <img data-no-retina src="<?php echo BASE_PATH . 'foto/' . adm_h($i['poza']); ?>" alt="" loading="lazy" />
                  <?php } else { ?>
                  <span class="adm-card-nophoto">fără poză</span>
                  <?php } ?>
                  <span class="adm-card-count"><?php echo $i['poze']; ?> poze<?php echo $i['video'] ? ' &middot; ' . $i['video'] . ' video' : ''; ?></span>
                </a>
                <div class="adm-card-main">
                  <div class="adm-card-title"><?php echo $row['marca']; ?> <span><?php echo $row['tip']; ?></span></div>
                  <?php if (trim((string)$row['pre_anunt']) !== '') { ?><div class="adm-card-sub"><?php echo adm_h($row['pre_anunt']); ?></div><?php } ?>
                  <div class="adm-card-meta">
                    <span><em>Categorie:</em> <?php echo $row['cat']; ?></span>
                    <span><em>Subcategorie:</em> <?php echo $row['subcateg']; ?></span>
                    <span><em>Combustibil:</em> <?php echo $row['combustibil']; ?></span>
                    <span><em>An:</em> <?php echo $row['an']; ?></span>
                    <?php foreach ($tehnic as $t) { ?><span><em><?php echo $t[0]; ?>:</em> <?php echo adm_h($t[1]); ?></span>
                    <?php } ?>
                    <span><em>Proveniență:</em> <?php echo $row['prov_enienta'] === 'Romania' ? 'România' : $row['prov_enienta']; ?></span>
                  </div>
                  <?php if ($lipsuri) { ?>
                  <div class="adm-card-warn"><?php foreach ($lipsuri as $l) { ?><span><?php echo $l; ?></span><?php } ?></div>
                  <?php } ?>
                  <div class="adm-card-actions">
                    <a class="adm-btn" href="<?php echo adm_h($url_modifica); ?>">modifică oferta</a>
                    <a class="adm-btn-ghost" href="<?php echo BASE_PATH . 'vanzari_detail.php?id=' . $id; ?>" target="_blank" rel="noopener">vezi pe site</a>
                    <a class="adm-del" href="<?php echo adm_h($url_sterge); ?>" onclick="return checkDelete()">șterge oferta</a>
                  </div>
                </div>
                <div class="adm-card-price">Preț
                  <strong><?php echo $row['pret']; ?> <small><?php echo adm_h($moneda); ?></small></strong>
                  <?php if ($row['pret_vechi'] > 0) { ?><del><?php echo $row['pret_vechi']; ?></del><?php } ?>
                </div>
              </div>
<?php
}

/**
 * Adresa butonului "Inapoi" de pe paginile de editare: lista de anunturi din care s-a deschis anuntul,
 * tinuta minte in sesiune ca sa ramana valabila si dupa salvare; altfel lista implicita.
 */
function adm_link_inapoi($implicit)
{
    $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    if ($ref !== '' && parse_url($ref, PHP_URL_HOST) === parse_url('//' . $host, PHP_URL_HOST)
        && preg_match('~/tehnic/(view_vanzare2|view_vanzare_new|view_vanzare_oferta)\.php~', (string)parse_url($ref, PHP_URL_PATH))) {
        $_SESSION['adm_lista_anunturi'] = $ref;
    }
    return isset($_SESSION['adm_lista_anunturi']) ? $_SESSION['adm_lista_anunturi'] : $implicit;
}
