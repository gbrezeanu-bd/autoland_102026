<?php require_once('../includes/config.php'); require_once('../includes/pagini_sectiuni.php'); ?>
<?php
// Editor pentru paginile din tabelul `pagini` (ex. subpaginile Finanțare).
// Fără id: lista paginilor. Cu ?id=N: formularul de editare.
if (!isset($_SESSION)) {
  session_start();
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
  header("Location: ../index.php");
  exit;
}

// Doar utilizatorii autentificați în admin
if (empty($_SESSION['MM_Username'])) {
  header("Location: ../tehnic/login.php?accesscheck=" . urlencode($_SERVER['PHP_SELF']));
  exit;
}

header('Content-Type: text/html; charset=utf-8');

function h($value) {
  return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$message = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Pagină nouă: se creează ca ciornă, apoi se completează textul
if (isset($_POST['MM_add'])) {
  $sect = $_POST['sectiune'];
  $titlu = trim($_POST['titlu']);
  $slug = trim($_POST['slug']) !== '' ? trim($_POST['slug']) : $titlu;
  $slug = strtolower(strtr($slug, ['ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ş' => 's', 'ț' => 't', 'ţ' => 't', 'Ă' => 'a', 'Â' => 'a', 'Î' => 'i', 'Ș' => 's', 'Ț' => 't']));
  $slug = trim(preg_replace('/[^a-z0-9]+/', '-', $slug), '-');
  if (!isset($PAGINI_SECTIUNI[$sect]) || $titlu === '' || $slug === '') {
    $message = 'Alege sectiunea si completeaza titlul.';
  } else {
    $stmt = $connection->prepare("INSERT INTO pagini (sectiune, slug, titlu, h1, continut, status, ordine) VALUES (?, ?, ?, ?, '', 'draft', 99)");
    $stmt->bind_param('ssss', $sect, $slug, $titlu, $titlu);
    if ($stmt->execute()) {
      header('Location: editor_pagini.php?id=' . $stmt->insert_id);
      exit;
    }
    $message = 'Exista deja o pagina cu adresa /' . pagini_cale($sect, $slug);
    $stmt->close();
  }
}

if (isset($_POST['MM_update']) && $_POST['MM_update'] == 'form1') {
  $id = (int)$_POST['id'];
  $h1 = trim($_POST['h1']);
  $meta_title = trim($_POST['meta_title']);
  $meta_description = trim($_POST['meta_description']);
  $status = ($_POST['status'] === 'published') ? 'published' : 'draft';
  $continut = $_POST['continut'];

  $stmt = $connection->prepare("UPDATE pagini SET h1 = ?, meta_title = ?, meta_description = ?, status = ?, continut = ? WHERE id = ?");
  $stmt->bind_param('sssssi', $h1, $meta_title, $meta_description, $status, $continut, $id);
  $stmt->execute() or die($connection->error);
  $stmt->close();

  header("Location: editor_pagini.php?id=" . $id . "&salvat=1");
  exit;
}

if (isset($_GET['salvat'])) {
  $message = 'Salvat.';
}

$pagina = null;
if ($id > 0) {
  $stmt = $connection->prepare("SELECT * FROM pagini WHERE id = ?");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $pagina = $stmt->get_result()->fetch_assoc();
  $stmt->close();
}

$headerTitle = 'Continut pagini';
$headerScripts = '';
if ($pagina) {
  $headerScripts = '<script type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
tinyMCE_GZ.init({
	plugins : "style,layer,table,save,advhr,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
	themes : "simple,advanced",
	languages : "en",
	disk_cache : true,
	debug : false
});
</script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	theme_advanced_buttons1_add : "fontselect,fontsizeselect,forecolor",
	mode : "specific_textareas",
	editor_selector : "editor_html",
	body_class : "inner-page",
	content_css : "../themes/automotive/css/pagini.css"
});
</script>
';
}
include 'header.php';
?>
    <td>&nbsp;
<?php if (!$pagina) { ?>
      <table align="center" cellpadding="6">
        <tr><td colspan="4" bgcolor="#CCCCCC"><strong>Pagini de continut (Finantare etc.)</strong></td></tr>
        <tr><td><strong>Sectiune</strong></td><td><strong>Pagina</strong></td><td><strong>Status</strong></td><td><strong>Modificat</strong></td><td>&nbsp;</td></tr>
<?php
  $rows = $connection->query("SELECT id, sectiune, slug, titlu, status, data_actualizarii FROM pagini ORDER BY sectiune, ordine, id");
  while ($r = $rows->fetch_assoc()) { ?>
        <tr>
          <td><?php echo h(isset($PAGINI_SECTIUNI[$r['sectiune']]) ? $PAGINI_SECTIUNI[$r['sectiune']]['eticheta'] : $r['sectiune']); ?></td>
          <td><a href="editor_pagini.php?id=<?php echo (int)$r['id']; ?>"><?php echo h($r['titlu']); ?></a></td>
          <td><?php echo $r['status'] === 'published' ? 'publicata' : '<em>ciorna (nu se vede public)</em>'; ?></td>
          <td><?php echo h($r['data_actualizarii']); ?></td>
          <td><a href="../<?php echo h(pagini_cale($r['sectiune'], $r['slug'])); ?>" target="_blank">vezi pagina</a></td>
        </tr>
<?php } ?>
      </table>
      <form method="post" action="editor_pagini.php">
        <table align="center" cellpadding="4">
          <tr><td colspan="2" bgcolor="#CCCCCC"><strong>Pagina noua</strong> <?php if ($message) { echo ' &nbsp; <strong style="color:#cc0000">' . h($message) . '</strong>'; } ?></td></tr>
          <tr><td>Sectiune:</td><td><select name="sectiune"><?php foreach ($PAGINI_SECTIUNI as $k => $v) { echo '<option value="' . h($k) . '">' . h($v['eticheta']) . '</option>'; } ?></select></td></tr>
          <tr><td>Titlu:</td><td><input type="text" name="titlu" size="60" maxlength="150"></td></tr>
          <tr><td>Adresa (optional):</td><td><input type="text" name="slug" size="40" maxlength="100"> <em>se genereaza din titlu; nu se mai poate schimba dupa creare</em></td></tr>
          <tr><td>&nbsp;</td><td><input type="submit" name="MM_add" value="Creeaza pagina"></td></tr>
        </table>
      </form>
<?php } else { ?>
      <form method="post" name="form1" action="editor_pagini.php?id=<?php echo (int)$pagina['id']; ?>">
        <table align="center" cellpadding="4">
          <tr><td bgcolor="#CCCCCC"><strong>Modificare: <?php echo h($pagina['titlu']); ?></strong>
            &nbsp; <a href="editor_pagini.php">&laquo; lista</a>
            &nbsp; <a href="../<?php echo h(pagini_cale($pagina['sectiune'], $pagina['slug'])); ?>" target="_blank">vezi pagina</a>
            <?php if ($message) { echo ' &nbsp; <strong style="color:#009900">' . h($message) . '</strong>'; } ?>
          </td></tr>
          <tr><td>Status:
            <select name="status">
              <option value="published"<?php echo $pagina['status'] === 'published' ? ' selected' : ''; ?>>publicata (vizibila pe site)</option>
              <option value="draft"<?php echo $pagina['status'] !== 'published' ? ' selected' : ''; ?>>ciorna (doar tu o vezi, logat)</option>
            </select>
          </td></tr>
          <tr><td>Titlu pagina (H1, apare in banner, cu cuvantul-cheie principal):<br>
            <input type="text" name="h1" size="90" maxlength="200" value="<?php echo h($pagina['h1']); ?>"></td></tr>
          <tr><td>Titlu in Google (meta title, ideal sub 60 caractere):<br>
            <input type="text" name="meta_title" size="90" maxlength="70" value="<?php echo h($pagina['meta_title']); ?>"></td></tr>
          <tr><td>Descriere in Google (meta description, ideal sub 155 caractere):<br>
            <textarea name="meta_description" cols="90" rows="3" maxlength="300"><?php echo h($pagina['meta_description']); ?></textarea></td></tr>
          <tr><td valign="top">Continut:<br>
            <textarea name="continut" class="editor_html" cols="80" rows="25"><?php echo h($pagina['continut']); ?></textarea></td></tr>
          <tr><td><input type="submit" value="Update record"></td></tr>
        </table>
        <input type="hidden" name="id" value="<?php echo (int)$pagina['id']; ?>">
        <input type="hidden" name="MM_update" value="form1">
      </form>
<?php } ?>
    <p>&nbsp;</p></td>
  </tr>
</table>
</body>
</html>
