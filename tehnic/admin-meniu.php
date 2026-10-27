<?php
/*
 * Bara de sus si meniul din stanga ale sectiunii tehnic, comune pentru header.php si header2.php.
 * Deschide tabelul de pagina si celula meniului; pagina adauga apoi propria celula de continut.
 * Grupurile se pot strange; grupul paginii curente e mereu deschis, iar celelalte isi pastreaza
 * starea in browser (localStorage).
 */

// Iconite (contur, 24x24) pentru grupurile din meniu
$adm_iconite = [
    'acasa' => '<path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/>',
    'anunturi' => '<path d="M5 16l1.6-5.2A2 2 0 0 1 8.5 9.4h7a2 2 0 0 1 1.9 1.4L19 16"/><path d="M3 16h18v3H3z"/><circle cx="7" cy="19" r="1.4"/><circle cx="17" cy="19" r="1.4"/>',
    'continut' => '<path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/><path d="M10 13h6M10 17h6"/>',
    'blog' => '<path d="M4 20h4L19 9l-4-4L4 16z"/><path d="M13.5 6.5l4 4"/>',
    'recenzii' => '<path d="M12 4l2.5 5.1 5.5.8-4 3.9.9 5.5L12 16.7 7.1 19.3l.9-5.5-4-3.9 5.5-.8z"/>',
    'nomenclatoare' => '<path d="M4 6h16M4 12h16M4 18h10"/>',
    'statistici' => '<path d="M5 20V10M12 20V4M19 20v-7"/>',
    'cont' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>',
];

// grup => [iconita, [[link, eticheta, pagini care tin de acelasi punct din meniu], ...]]
$adm_meniu = [
    'Anunțuri' => ['anunturi', [
        ['add_anunt.php', 'Adaugă ofertă'],
        ['view_vanzare2.php', 'View Vânzare', ['view_mod_vanzare2.php', 'foto_up2.php', 'video_up2.php']],
        ['view_vanzare_new.php', 'View Vânzare New', ['vezi_anunturi.php']],
        ['view_vanzare_oferta.php', 'Oferte speciale'],
    ]],
    'Conținut site' => ['continut', [
        ['editor_home.php', 'Pagina principală'],
        ['editor_pagini.php', 'Pagini (despre noi, finanțare, garanție)'],
        ['editor_leasing.php', 'Leasing'],
        ['editor_garantie.php', 'Garanție'],
        ['editor_inmatriculari.php', 'Înmatriculări'],
        ['editor_asistenta_rutiera.php', 'Asistență rutieră'],
        ['editor_contact.php', 'Contact'],
        ['editor_tandc.php', 'Politică confidențialitate'],
        ['editor_rent.php', 'Rent-a-car'],
        ['editor_cond_rent.php', 'Condiții închiriere'],
    ]],
    'Blog' => ['blog', [
        ['articole_add.php', 'Adaugă articol'],
        ['articole_manage.php', 'Vezi / șterge articole', ['articole_edit.php']],
    ]],
    'Recenzii' => ['recenzii', [
        ['solicita_recenzii.php', 'Administrare recenzii', ['edit_recenzii.php', 'view_recenzii.php']],
        ['editor_email_recenzii.php', 'Email recenzii'],
        ['raport_recenzii.php', 'Raport recenzii'],
    ]],
    'Categorii și mărci' => ['nomenclatoare', [
        ['cat-auto.php', 'Categorii auto'],
        ['marci-auto.php', 'Mărci auto'],
        ['filtru.php', 'Filtru mărci'],
        ['subcateg-auto.php', 'Subcategorii auto'],
        ['subcateg-filtru.php', 'Filtru subcategorii'],
    ]],
    // Statistici (statistici.php) a fost scoasa din meniu: vizitele nu se mai inregistreaza din decembrie 2018
    'Cont' => ['cont', [
        ['schimbareparola.php', 'Schimbare parolă'],
    ]],
];
$adm_pagina_curenta = basename($_SERVER['PHP_SELF']);
$adm_utilizator = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : '';
// Unele pagini (ex. editor_pagini.php) nu definesc $logoutAction; delogarea o face index.php
$adm_logout = isset($logoutAction) ? $logoutAction : 'index.php?doLogout=true';
$adm_icon = function ($nume) use ($adm_iconite) {
    return '<svg class="adm-nav-icon" viewBox="0 0 24 24" aria-hidden="true">' . $adm_iconite[$nume] . '</svg>';
};
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="adm-layout">
  <tr>
    <td width="250" class="adm-top-logo"><a href="index.php"><img src="../themes/automotive/images/logo_autoland_2.png" alt="AUTOLAND" width="170" height="45" /></a></td>
    <td class="adm-top-bar">
      <span class="adm-top-title">Secțiune administrare</span>
      <span class="adm-top-links">
        <?php if ($adm_utilizator !== '') { ?><span class="adm-top-user"><?php echo htmlspecialchars($adm_utilizator, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></span><?php } ?>
        <a href="../" target="_blank" rel="noopener">Vezi site-ul</a>
        <a href="<?php echo $adm_logout ?>">Logout</a>
      </span>
    </td>
  </tr>
  <tr>
    <td valign="top" class="adm-nav">
      <div class="adm-nav-inner">
        <a class="adm-nav-home<?php echo $adm_pagina_curenta === 'index.php' ? ' is-active' : ''; ?>" href="index.php"><?php echo $adm_icon('acasa'); ?>Panou principal</a>
<?php foreach ($adm_meniu as $adm_grup => $adm_def) {
    list($adm_iconita, $adm_linkuri) = $adm_def;
    $adm_grup_activ = false;
    foreach ($adm_linkuri as $adm_link) {
        if ($adm_link[0] === $adm_pagina_curenta || (isset($adm_link[2]) && in_array($adm_pagina_curenta, $adm_link[2], true))) {
            $adm_grup_activ = true;
        }
    }
    if (count($adm_linkuri) === 1) { ?>
        <a class="adm-nav-home<?php echo $adm_grup_activ ? ' is-active' : ''; ?>" href="<?php echo $adm_linkuri[0][0]; ?>"><?php echo $adm_icon($adm_iconita) . $adm_linkuri[0][1]; ?></a>
<?php continue; } ?>
        <details class="adm-nav-group<?php echo $adm_grup_activ ? ' is-current' : ''; ?>" data-grup="<?php echo $adm_iconita; ?>"<?php echo $adm_grup_activ ? ' open' : ''; ?>>
          <summary><?php echo $adm_icon($adm_iconita) . $adm_grup; ?><span class="adm-nav-count"><?php echo count($adm_linkuri); ?></span></summary>
          <ul class="adm-nav-list">
<?php foreach ($adm_linkuri as $adm_link) {
        $adm_activ = $adm_link[0] === $adm_pagina_curenta || (isset($adm_link[2]) && in_array($adm_pagina_curenta, $adm_link[2], true)); ?>
            <li><a href="<?php echo $adm_link[0]; ?>" target="_self"<?php echo $adm_activ ? ' class="is-active"' : ''; ?>><?php echo $adm_link[1]; ?></a></li>
<?php } ?>
          </ul>
        </details>
<?php } ?>
        <a class="adm-nav-home adm-nav-logout" href="<?php echo $adm_logout ?>"><svg class="adm-nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M15 4h4v16h-4"/><path d="M10 8l-4 4 4 4M6 12h10"/></svg>Logout</a>
      </div>
      <script type="text/javascript">
      (function () {
          // Pastreaza grupurile deschise de utilizator intre pagini; grupul paginii curente ramane deschis oricum.
          var cheie = 'adm-meniu-deschise', deschise = [];
          try { deschise = JSON.parse(localStorage.getItem(cheie) || '[]'); } catch (e) {}
          var grupuri = document.querySelectorAll('.adm-nav-group');
          for (var i = 0; i < grupuri.length; i++) {
              if (deschise.indexOf(grupuri[i].getAttribute('data-grup')) !== -1) { grupuri[i].open = true; }
              grupuri[i].addEventListener('toggle', function () {
                  var lista = [];
                  for (var j = 0; j < grupuri.length; j++) {
                      if (grupuri[j].open && grupuri[j].className.indexOf('is-current') === -1) { lista.push(grupuri[j].getAttribute('data-grup')); }
                  }
                  try { localStorage.setItem(cheie, JSON.stringify(lista)); } catch (e) {}
              });
          }
      })();
      </script>
    </td>
