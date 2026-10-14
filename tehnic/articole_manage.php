<?php require_once('../includes/config.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../tehnic/login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

// Handle DELETE
if ((isset($_GET['id'])) && ($_GET['id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM articole WHERE id=%s",
                       GetSQLValueString($_GET['id'], "int"));
                                                    
  $Result1 = $connection->query($deleteSQL) or die($connection->error);

  $deleteGoTo = "articole_manage.php";
  header(sprintf("Location: %s", $deleteGoTo));
}

// Handle EDIT redirect
if ((isset($_GET['edit'])) && ($_GET['edit'] != "")) {
  header("Location: articole_edit.php?id=" . $_GET['edit']);
  exit;
}

// Load all articles
$query_articole = "SELECT * FROM articole ORDER BY data_publicarii DESC, id DESC";
$articole = $connection->query($query_articole) or die($connection->error);
$row_articole = mysqli_fetch_assoc($articole);
$totalRows_articole = mysqli_num_rows($articole);

$headerTitle = 'Vizualizare / Sterge Articole';
include 'header.php';
?><!-- InstanceBeginEditable name="main" -->
    <td valign="middle">
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left" bgcolor="#CCCCCC"><strong>Vizualizare articole existente</strong></td>
      </tr>
      <tr>
        <td align="center" bgcolor="#eeeeee">&nbsp;</td>
      </tr>
      <tr>
        <td align="center" bgcolor="#eeeeee">
          <form id="form_sterge" name="form_sterge" method="get" action="articole_manage.php">
          <select name="id" class="vanzari_box" id="id" style="width: 90%; max-width: 800px;">
            <?php
            if ($totalRows_articole > 0) {
              do {  
                $status_icon = '';
                if ($row_articole['status'] == 'published') {
                  $status_icon = '✓ ';
                } elseif ($row_articole['status'] == 'draft') {
                  $status_icon = '✎ ';
                }
                
                $featured_icon = ($row_articole['featured'] == 1) ? '★ ' : '';
                
                $display_text = $status_icon . $featured_icon . $row_articole['titlu'] . ' (' . $row_articole['categorie'] . ' - ' . date('d.m.Y', strtotime($row_articole['data_publicarii'])) . ')';
            ?>
            <option value="<?php echo $row_articole['id']?>"><?php echo htmlspecialchars($display_text); ?></option>
            <?php
              } while ($row_articole = mysqli_fetch_assoc($articole));
              $rows = mysqli_num_rows($articole);
              if($rows > 0) {
                  mysqli_data_seek($articole, 0);
                  $row_articole = mysqli_fetch_assoc($articole);
              }
            } else {
              echo '<option value="">Niciun articol găsit</option>';
            }
            ?>
          </select>
          <br /><br />
          <input type="submit" name="Submit" value="Sterge" style="margin-right: 10px;" 
                 onclick="return confirm('Sigur vrei să ștergi acest articol?');" />
          <input type="button" value="Editeaza" 
                 onclick="if(document.getElementById('id').value) window.location='articole_edit.php?id='+document.getElementById('id').value;" />
          </form>
        </td>
      </tr>
      <tr>
        <td align="center" bgcolor="#eeeeee">&nbsp;</td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
      </tr>
      <tr>
        <td align="left" bgcolor="#eeeeee">
          <table width="100%" border="0" cellspacing="5" cellpadding="5">
            <tr>
              <td bgcolor="#f0f0f0">
                <strong>Legendă:</strong><br />
                <span style="font-size: 11px;">
                  ✓ = Published (vizibil pe site)<br />
                  ✎ = Draft (nu e vizibil)<br />
                  ★ = Featured (articol evidențiat)
                </span>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
      </tr>
      
      <!-- Detalii articol selectat -->
      <?php if ($totalRows_articole > 0): ?>
      <tr>
        <td align="left" bgcolor="#CCCCCC"><strong>Detalii articol selectat</strong></td>
      </tr>
      <tr>
        <td bgcolor="#eeeeee">
          <table width="100%" border="0" cellspacing="5" cellpadding="8" id="article_details">
            <tr>
              <td width="150" valign="top"><strong>ID:</strong></td>
              <td id="detail_id"><?php echo $row_articole['id']; ?></td>
            </tr>
            <tr>
              <td valign="top"><strong>Titlu:</strong></td>
              <td id="detail_titlu"><?php echo htmlspecialchars($row_articole['titlu']); ?></td>
            </tr>
            <tr>
              <td valign="top"><strong>Slug:</strong></td>
              <td id="detail_slug" style="font-family: monospace; color: #0066cc;"><?php echo htmlspecialchars($row_articole['slug']); ?></td>
            </tr>
            <tr>
              <td valign="top"><strong>Categorie:</strong></td>
              <td id="detail_categorie"><?php echo htmlspecialchars($row_articole['categorie']); ?></td>
            </tr>
            <tr>
              <td valign="top"><strong>Status:</strong></td>
              <td id="detail_status">
                <?php 
                  $status_display = $row_articole['status'];
                  if ($status_display == 'published') {
                    echo '<span style="color: green; font-weight: bold;">✓ Published</span>';
                  } elseif ($status_display == 'draft') {
                    echo '<span style="color: #ff9900; font-weight: bold;">✎ Draft</span>';
                  } else {
                    echo $status_display;
                  }
                ?>
              </td>
            </tr>
            <tr>
              <td valign="top"><strong>Featured:</strong></td>
              <td id="detail_featured">
                <?php echo ($row_articole['featured'] == 1) ? '<span style="color: #ff9900; font-weight: bold;">★ DA</span>' : 'Nu'; ?>
              </td>
            </tr>
            <tr>
              <td valign="top"><strong>Data publicării:</strong></td>
              <td id="detail_data"><?php echo date('d.m.Y H:i', strtotime($row_articole['data_publicarii'])); ?></td>
            </tr>
            <tr>
              <td valign="top"><strong>Timp citire:</strong></td>
              <td id="detail_timp"><?php echo $row_articole['timp_citire']; ?> min</td>
            </tr>
            <tr>
              <td valign="top"><strong>Vizualizări:</strong></td>
              <td id="detail_citiri"><?php echo number_format($row_articole['numar_citiri'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
              <td valign="top"><strong>Meta Description:</strong></td>
              <td id="detail_meta" style="font-size: 12px; color: #666;">
                <?php 
                  $meta = htmlspecialchars($row_articole['meta_description']);
                  $meta_len = strlen($row_articole['meta_description']);
                  echo $meta . '<br /><span style="font-size: 11px;">(' . $meta_len . ' caractere)</span>';
                ?>
              </td>
            </tr>
            <tr>
              <td valign="top"><strong>Tags:</strong></td>
              <td id="detail_tags"><?php echo htmlspecialchars($row_articole['tags'] ? $row_articole['tags'] : '-'); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <?php endif; ?>
      
      <tr>
        <td align="center" bgcolor="#eeeeee">&nbsp;</td>
      </tr>
      <tr>
        <td align="center">
          <input type="button" value="« Înapoi la Meniu" onclick="window.location='index.php';" style="padding: 8px 20px;" />
          <input type="button" value="Adaugă Articol Nou »" onclick="window.location='articole_add.php';" style="padding: 8px 20px; background: #009900; color: white; font-weight: bold;" />
        </td>
      </tr>
    </table>
    
    <script>
    // Store article data as JSON for quick lookup
    var articlesData = {
      <?php
      if ($totalRows_articole > 0) {
        mysqli_data_seek($articole, 0);
        $first = true;
        while ($row = mysqli_fetch_assoc($articole)) {
          if (!$first) echo ",\n      ";
          $first = false;
          echo '"' . $row['id'] . '": {';
          echo '"titlu": ' . json_encode($row['titlu']) . ',';
          echo '"slug": ' . json_encode($row['slug']) . ',';
          echo '"categorie": ' . json_encode($row['categorie']) . ',';
          echo '"status": ' . json_encode($row['status']) . ',';
          echo '"featured": ' . json_encode($row['featured']) . ',';
          echo '"data_publicarii": ' . json_encode(date('d.m.Y H:i', strtotime($row['data_publicarii']))) . ',';
          echo '"timp_citire": ' . json_encode($row['timp_citire']) . ',';
          echo '"numar_citiri": ' . json_encode(number_format($row['numar_citiri'], 0, ',', '.')) . ',';
          echo '"meta_description": ' . json_encode($row['meta_description']) . ',';
          echo '"meta_len": ' . strlen($row['meta_description']) . ',';
          echo '"tags": ' . json_encode($row['tags'] ? $row['tags'] : '-');
          echo '}';
        }
      }
      ?>
    };
    
    // Update details when selection changes
    document.getElementById('id').addEventListener('change', function() {
      var articleId = this.value;
      if (articleId && articlesData[articleId]) {
        var data = articlesData[articleId];
        
        document.getElementById('detail_id').textContent = articleId;
        document.getElementById('detail_titlu').textContent = data.titlu;
        document.getElementById('detail_slug').textContent = data.slug;
        document.getElementById('detail_categorie').textContent = data.categorie;
        
        // Status
        var statusHtml = data.status;
        if (data.status === 'published') {
          statusHtml = '<span style="color: green; font-weight: bold;">✓ Published</span>';
        } else if (data.status === 'draft') {
          statusHtml = '<span style="color: #ff9900; font-weight: bold;">✎ Draft</span>';
        }
        document.getElementById('detail_status').innerHTML = statusHtml;
        
        // Featured
        document.getElementById('detail_featured').innerHTML = 
          (data.featured == 1) ? '<span style="color: #ff9900; font-weight: bold;">★ DA</span>' : 'Nu';
        
        document.getElementById('detail_data').textContent = data.data_publicarii;
        document.getElementById('detail_timp').textContent = data.timp_citire + ' min';
        document.getElementById('detail_citiri').textContent = data.numar_citiri;
        document.getElementById('detail_meta').innerHTML = 
          data.meta_description + '<br /><span style="font-size: 11px;">(' + data.meta_len + ' caractere)</span>';
        document.getElementById('detail_tags').textContent = data.tags;
      }
    });
    </script>
    
    </td><!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($articole);
?>

