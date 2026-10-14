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

// Helper function: Calculate reading time based on character count
function calculate_reading_time($html) {
    $plain_text = strip_tags($html);
    $char_count = mb_strlen($plain_text, 'UTF-8');
    return ceil($char_count / 1200); // 1200 characters per minute average (Romanian text)
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$message = '';
$message_type = '';

// Handle UPDATE
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $id = intval($_POST['id']);
  
  // Validate and sanitize inputs
  $titlu = trim($_POST['titlu']);
  $meta_title = trim($_POST['meta_title']);
  $meta_description = trim($_POST['meta_description']);
  // Get HTML content - decode any double-encoded entities and strip slashes if magic quotes enabled
  $text = $_POST['text'];
  if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    $text = stripslashes($text);
  }
  // Restore </textarea> placeholder back to actual tag
  $text = str_replace('___TEXTAREA_CLOSE_TAG___', '</textarea>', $text);
  // Also handle any legacy escaped versions
  $text = str_replace('&lt;/textarea&gt;', '</textarea>', $text);
  $text = str_replace('&lt;/textarea>', '</textarea>', $text);
  // Decode HTML entities if they were double-encoded (but preserve actual HTML tags)
  // Only decode if content looks like it has encoded entities but no actual HTML tags
  if (preg_match('/&lt;[a-z]/i', $text) && !preg_match('/<[a-z]/i', $text)) {
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  }
  $excerpt = trim($_POST['excerpt']);
  $categorie = $_POST['categorie'];
  $tags = trim($_POST['tags']);
  $status = $_POST['status'];
  $featured = isset($_POST['featured']) ? 1 : 0;
  $imagine_featured = trim($_POST['imagine_featured']);
  $imagine_alt_text = trim($_POST['imagine_alt_text']);
  $cta_text = trim($_POST['cta_text']);
  $cta_link = trim($_POST['cta_link']);
  $autor = isset($_POST['autor']) && !empty(trim($_POST['autor'])) ? trim($_POST['autor']) : 'AUTOLAND România';
  $autor_bio_raw = isset($_POST['autor_bio']) ? trim($_POST['autor_bio']) : '';
  // Autor bio trebuie să fie text simplu, fără HTML
  $autor_bio = !empty($autor_bio_raw) ? strip_tags($autor_bio_raw) : 'AUTOLAND România este dealer auto de încredere din Iași cu peste 18 ani de experiență în importul și comercializarea de autoturisme rulate, second hand, verificate tehnic. Oferim garanție comercială extinsă, finanțare flexibilă și servicii complete: RAR, omologare, înmatriculare, asistență rutieră. Specialiști în import mașini din Germania și Europa de Vest, cu transparență totală și documentație completă pentru fiecare vehicul.';
  
  // Handle image upload
  if (isset($_FILES['imagine_upload']) && $_FILES['imagine_upload']['error'] == 0) {
    $upload_dir = '../images/articles/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
      mkdir($upload_dir, 0755, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['imagine_upload']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    
    if (in_array($file_extension, $allowed_extensions)) {
      // Get current slug from database for filename
      $slug_query = "SELECT slug FROM articole WHERE id = $id";
      $slug_result = $connection->query($slug_query);
      if ($slug_result && $slug_result->num_rows > 0) {
        $slug_row = $slug_result->fetch_assoc();
        $current_slug = $slug_row['slug'];
        
        // Generate unique filename
        $new_filename = $current_slug . '-' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['imagine_upload']['tmp_name'], $upload_path)) {
          $imagine_featured = 'images/articles/' . $new_filename;
        } else {
          $message = "EROARE: Nu s-a putut încărca imaginea.";
          $message_type = "error";
        }
      }
    } else {
      $message = "EROARE: Format imagine invalid. Folosește JPG, PNG, GIF sau WebP.";
      $message_type = "error";
    }
  }
  
  // Auto-calculate reading time
  $timp_citire = calculate_reading_time($text);
  
  // Auto-generate alt text if empty but image exists
  if (empty($imagine_alt_text) && !empty($imagine_featured)) {
    $imagine_alt_text = $titlu;
  }
  
  // Auto-generate meta_keywords from tags
  $meta_keywords = $tags; // Meta keywords = tags (Google ignoră, dar e util pentru organizare)
  
  // Prepare SQL values
  $titlu_sql = GetSQLValueString($titlu, "text");
  $meta_title_sql = GetSQLValueString($meta_title, "text");
  $meta_description_sql = GetSQLValueString($meta_description, "text");
  $meta_keywords_sql = GetSQLValueString($meta_keywords, "text");
  // Pentru HTML content, folosim mysqli_real_escape_string în loc de addslashes
  $text_sql = "'" . $connection->real_escape_string($text) . "'";
  $excerpt_sql = GetSQLValueString(!empty($excerpt) ? $excerpt : $meta_description, "text");
  $categorie_sql = GetSQLValueString($categorie, "text");
  $tags_sql = GetSQLValueString($tags, "text");
  $status_sql = GetSQLValueString($status, "text");
  $imagine_featured_sql = GetSQLValueString($imagine_featured, "text");
  $imagine_alt_text_sql = GetSQLValueString($imagine_alt_text, "text");
  $cta_text_sql = GetSQLValueString($cta_text, "text");
  $cta_link_sql = GetSQLValueString($cta_link, "text");
  $autor_sql = GetSQLValueString($autor, "text");
  $autor_bio_sql = GetSQLValueString($autor_bio, "text");
  
  $updateSQL = "UPDATE articole SET 
    titlu = $titlu_sql,
    meta_title = $meta_title_sql,
    text = $text_sql,
    excerpt = $excerpt_sql,
    meta_description = $meta_description_sql,
    meta_keywords = $meta_keywords_sql,
    categorie = $categorie_sql,
    tags = $tags_sql,
    status = $status_sql,
    featured = $featured,
    imagine_featured = $imagine_featured_sql,
    imagine_alt_text = $imagine_alt_text_sql,
    cta_text = $cta_text_sql,
    cta_link = $cta_link_sql,
    timp_citire = $timp_citire,
    autor = $autor_sql,
    autor_bio = $autor_bio_sql,
    data_actualizarii = NOW()
  WHERE id = $id";
  
  $Result1 = $connection->query($updateSQL);
  
  if ($Result1) {
    $message = "SUCCESS! Articol actualizat. Timp citire: $timp_citire min (recalculat automat)";
    $message_type = "success";
  } else {
    $message = "EROARE la actualizare: " . $connection->error;
    $message_type = "error";
  }
}

// Load article to edit
$article = null;
if (isset($_GET['id']) || isset($_POST['id'])) {
  $id = isset($_POST['id']) ? intval($_POST['id']) : intval($_GET['id']);
  
  $query = "SELECT * FROM articole WHERE id = $id";
  $result = $connection->query($query);
  
  if ($result && $result->num_rows > 0) {
    $article = $result->fetch_assoc();
  } else {
    header("Location: articole_manage.php");
    exit;
  }
} else {
  header("Location: articole_manage.php");
  exit;
}

$headerTitle = 'Editare Articol: ' . $article['titlu'];
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
	mode : "textareas",
	textarea_trigger : "mce_editable",
	valid_elements : "*[*]",
	extended_valid_elements : "a[href|target|name|title|style|class|id|onclick]",
	cleanup : false,
	cleanup_on_startup : false,
	verify_html : false
});
</script>
';
include 'header.php';
?><!-- InstanceBeginEditable name="main" -->
    <td valign="middle">
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
      
      <?php if ($message): ?>
      <tr>
        <td align="center" bgcolor="<?php echo $message_type == 'success' ? '#d4edda' : '#f8d7da'; ?>" style="padding: 15px; border: 2px solid <?php echo $message_type == 'success' ? '#28a745' : '#dc3545'; ?>;">
          <strong style="color: <?php echo $message_type == 'success' ? '#155724' : '#721c24'; ?>; font-size: 14px;">
            <?php echo $message; ?>
          </strong>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <?php endif; ?>
      
      <tr>
        <td align="left" bgcolor="#CCCCCC"><strong>Editare articol (ID: <?php echo $article['id']; ?>)</strong></td>
      </tr>
      <tr>
        <td align="center" bgcolor="#eeeeee">&nbsp;</td>
      </tr>
      <tr>
        <td bgcolor="#eeeeee">
          <form method="post" name="form1" action="<?php echo $editFormAction; ?>" id="articleForm" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
            
            <table align="center" width="90%">
              
              <!-- Info box -->
              <tr>
                <td colspan="2" bgcolor="#fff3cd" style="padding: 10px; border: 2px solid #ffc107;">
                  <strong style="color: #856404;">ℹ️ Informații:</strong><br />
                  <span style="font-size: 11px; color: #856404;">
                    • Slug-ul NU poate fi modificat după publicare (protecție SEO)<br />
                    • Data publicării: <?php echo date('d.m.Y H:i', strtotime($article['data_publicarii'])); ?><br />
                    • Ultima actualizare: <?php echo date('d.m.Y H:i', strtotime($article['data_actualizarii'])); ?><br />
                    • Vizualizări: <?php echo number_format($article['numar_citiri'], 0, ',', '.'); ?>
                  </span>
                </td>
              </tr>
              
              <tr><td colspan="2">&nbsp;</td></tr>
              
              <!-- Titlu -->
              <tr valign="baseline">
                <td width="180" nowrap align="right"><strong>Titlu:</strong> <span style="color: red;">*</span></td>
                <td>
                  <input type="text" name="titlu" id="titlu" class="add_vanz" style="width: 95%;" required 
                         maxlength="255"
                         value="<?php echo htmlspecialchars($article['titlu']); ?>"
                         placeholder="Ex: Ghid Mașini Second Hand 2025">
                  <div id="titlu_warning" style="font-size: 11px; margin-top: 3px;"></div>
                </td>
              </tr>
              
              <!-- Meta Title -->
              <tr valign="baseline">
                <td nowrap align="right">Meta Title (SEO):</td>
                <td>
                  <input type="text" name="meta_title" id="meta_title" class="add_vanz" style="width: 95%;" 
                         maxlength="70"
                         value="<?php echo htmlspecialchars($article['meta_title']); ?>"
                         placeholder="Ex: Ghid Mașini Second Hand 2025 | AUTOLAND">
                  <div id="meta_title_warning" style="font-size: 11px; margin-top: 3px;"></div>
                  <div style="font-size: 11px; color: #0066cc; margin-top: 3px;">
                    💡 Control total asupra textului din rezultatele Google. Ideal: 50-60 caractere (Google trunchiază după ~60)
                  </div>
                </td>
              </tr>
              
              <!-- Slug (read-only) -->
              <tr valign="baseline">
                <td nowrap align="right"><strong>Slug (URL):</strong></td>
                <td>
                  <input type="text" class="add_vanz" style="width: 95%; background: #f0f0f0; cursor: not-allowed;" 
                         value="<?php echo htmlspecialchars($article['slug']); ?>" readonly>
                  <div style="font-size: 11px; color: #dc3545; margin-top: 3px; font-weight: bold;">
                    ⚠️ SLUG-ul NU se poate modifica (protecție SEO). Contactează echipa tehnică (aka Gabi) pentru redirect 301.
                  </div>
                </td>
              </tr>
              
              <!-- Meta Description (plain text textarea) -->
              <tr valign="baseline">
                <td nowrap align="right" valign="top"><strong>Meta Description:</strong> <span style="color: red;">*</span></td>
                <td>
                  <textarea name="meta_description" id="meta_description" required 
                            class="add_vanz" style="width: 95%;" rows="3"
                            mce_editable="false"
                            placeholder="Descriere 120-160 caractere pentru Google"><?php echo htmlspecialchars($article['meta_description']); ?></textarea>
                  <div id="meta_count" style="font-size: 11px; margin-top: 3px; font-weight: bold;">0 caractere</div>
                  <div style="font-size: 11px; color: #666; margin-top: 3px;">
                    💡 Doar text simplu (fără formatare HTML). Apare în rezultatele Google.
                  </div>
                </td>
              </tr>
              
              <!-- Excerpt (plain text textarea) -->
              <tr valign="baseline">
                <td nowrap align="right" valign="top">Excerpt (rezumat):</td>
                <td>
                  <textarea name="excerpt" id="excerpt" 
                            class="add_vanz" style="width: 95%;" rows="3"
                            mce_editable="false"
                            placeholder="Rezumat scurt pentru listări (opțional)"><?php echo htmlspecialchars($article['excerpt']); ?></textarea>
                  <div id="excerpt_warning" style="font-size: 11px; margin-top: 3px;"></div>
                  <div style="font-size: 11px; color: #666; margin-top: 3px;">
                    💡 Text simplu pentru previzualizări în listări. Fără HTML.
                  </div>
                </td>
              </tr>
              
              <!-- Conținut HTML -->
              <tr valign="baseline">
                <td nowrap align="right" valign="top"><strong>Conținut Articol:</strong> <span style="color: red;">*</span></td>
                <td>
                  <textarea name="text" id="text" cols="80" rows="30" mce_editable="true"><?php 
                    // Output raw HTML for TinyMCE - use a unique placeholder for </textarea> to prevent breaking HTML structure
                    $text_for_textarea = $article['text'];
                    // Use a unique placeholder that won't appear in real content
                    $text_for_textarea = str_replace('</textarea>', '___TEXTAREA_CLOSE_TAG___', $text_for_textarea);
                    echo $text_for_textarea;
                  ?></textarea>
                  <div style="font-size: 11px; color: #0066cc; margin-top: 5px;">
                    💡 Folosește editorul WYSIWYG de mai sus pentru a edita conținutul randat vizual. Timpul de citire se recalculează AUTOMAT la salvare (1200 caractere/minut)
                  </div>
                </td>
              </tr>
              
              <tr valign="baseline">
                <td colspan="2" align="center" bgcolor="#f0f0f0" style="padding: 10px;">
                  <strong>───── Organizare și Categorizare ─────</strong>
                </td>
              </tr>
              
              <!-- Categorie -->
              <tr valign="baseline">
                <td nowrap align="right"><strong>Categorie:</strong> <span style="color: red;">*</span></td>
                <td>
                  <select name="categorie" class="add_vanz" required>
                    <option value="">-- Alege categorie --</option>
                    <option value="Ghiduri" <?php echo ($article['categorie'] == 'Ghiduri') ? 'selected' : ''; ?>>Ghiduri</option>
                    <option value="Sfaturi" <?php echo ($article['categorie'] == 'Sfaturi') ? 'selected' : ''; ?>>Sfaturi</option>
                    <option value="Știri" <?php echo ($article['categorie'] == 'Știri') ? 'selected' : ''; ?>>Știri</option>
                  </select>
                </td>
              </tr>
              
              <!-- Tags -->
              <tr valign="baseline">
                <td nowrap align="right" valign="top">Tags:</td>
                <td>
                  <textarea name="tags" id="tags" class="add_vanz" style="width: 95%;" rows="2"
                            mce_editable="false"
                            placeholder="Ex: second-hand,verificare,germania"><?php echo htmlspecialchars($article['tags']); ?></textarea>
                  <div style="font-size: 11px; color: #666; margin-top: 3px;">
                    Separate prin virgulă. Max 500 caractere.
                  </div>
                  <div id="tags_warning" style="font-size: 11px; margin-top: 3px;"></div>
                </td>
              </tr>
              
              <!-- Status -->
              <tr valign="baseline">
                <td nowrap align="right"><strong>Status:</strong> <span style="color: red;">*</span></td>
                <td>
                  <select name="status" class="add_vanz" required>
                    <option value="draft" <?php echo ($article['status'] == 'draft') ? 'selected' : ''; ?>>Draft (Nu e vizibil)</option>
                    <option value="published" <?php echo ($article['status'] == 'published') ? 'selected' : ''; ?>>Published (Vizibil pe site)</option>
                  </select>
                </td>
              </tr>
              
              <!-- Featured -->
              <tr valign="baseline">
                <td nowrap align="right">Featured:</td>
                <td>
                  <input type="checkbox" name="featured" id="featured" value="1" 
                         <?php echo ($article['featured'] == 1) ? 'checked' : ''; ?>>
                  <label for="featured">Articol evidențiat (apare pe homepage)</label>
                </td>
              </tr>
              
              <tr valign="baseline">
                <td colspan="2" align="center" bgcolor="#f0f0f0" style="padding: 10px;">
                  <strong>───── Imagini și Media (Opțional) ─────</strong>
                </td>
              </tr>
              
              <!-- Imagine Upload -->
              <tr valign="baseline">
                <td nowrap align="right">Upload Imagine Nouă:</td>
                <td>
                  <input type="file" name="imagine_upload" id="imagine_upload" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" 
                         style="padding: 5px;">
                  <div style="font-size: 11px; color: #0066cc; margin-top: 3px;">
                    📸 Încarcă imagine nouă (JPG, PNG, GIF, WebP) - 1200x630px recomandat. Înlocuiește imaginea curentă.
                  </div>
                </td>
              </tr>
              
              <!-- Imagine Featured URL -->
              <tr valign="baseline">
                <td nowrap align="right">URL Imagine Curentă:</td>
                <td>
                  <input type="text" name="imagine_featured" id="imagine_featured" class="add_vanz" style="width: 95%;" 
                         maxlength="500"
                         value="<?php echo htmlspecialchars($article['imagine_featured']); ?>"
                         placeholder="Ex: images/articles/ghid-sh.jpg">
                  <div style="font-size: 11px; color: #666; margin-top: 3px;">
                    SAU introdu URL manual. Max 500 caractere.
                  </div>
                  <div id="imagine_warning" style="font-size: 11px; margin-top: 3px;"></div>
                </td>
              </tr>
              
              <!-- Imagine Alt Text -->
              <tr valign="baseline">
                <td nowrap align="right" valign="top">Alt Text Imagine:</td>
                <td>
                  <textarea name="imagine_alt_text" id="imagine_alt_text" class="add_vanz" style="width: 95%;" rows="2"
                            mce_editable="false"
                            placeholder="Ex: Ghid pentru cumpărare mașini second hand"><?php echo htmlspecialchars($article['imagine_alt_text']); ?></textarea>
                  <div style="font-size: 11px; color: #666; margin-top: 3px;">
                    Text alternativ pentru accesibilitate și SEO. Max 255 caractere.
                  </div>
                  <div id="alt_warning" style="font-size: 11px; margin-top: 3px;"></div>
                </td>
              </tr>
              
              <tr valign="baseline">
                <td colspan="2" align="center" bgcolor="#f0f0f0" style="padding: 10px;">
                  <strong>───── Call-to-Action (Opțional) ─────</strong>
                </td>
              </tr>
              
              <!-- CTA Text -->
              <tr valign="baseline">
                <td nowrap align="right">CTA Text:</td>
                <td>
                  <input type="text" name="cta_text" id="cta_text" class="add_vanz" style="width: 95%;" 
                         maxlength="100"
                         value="<?php echo !empty($article['cta_text']) ? htmlspecialchars($article['cta_text']) : ''; ?>"
                         placeholder="Ex: Cere Ofertă">
                  <div id="cta_text_warning" style="font-size: 11px; margin-top: 3px;"></div>
                </td>
              </tr>
              
              <!-- CTA Link -->
              <tr valign="baseline">
                <td nowrap align="right">CTA Link:</td>
                <td>
                  <input type="text" name="cta_link" id="cta_link" class="add_vanz" style="width: 95%;" 
                         maxlength="500"
                         value="<?php echo !empty($article['cta_link']) ? htmlspecialchars($article['cta_link']) : ''; ?>"
                         placeholder="Ex: cerere_oferta.php">
                  <div id="cta_link_warning" style="font-size: 11px; margin-top: 3px;"></div>
                </td>
              </tr>
              
              <tr valign="baseline">
                <td colspan="2" align="center" bgcolor="#f0f0f0" style="padding: 10px;">
                  <strong>───── Informații Autor ─────</strong>
                </td>
              </tr>
              
              <!-- Autor -->
              <tr valign="baseline">
                <td nowrap align="right"><strong>Autor:</strong></td>
                <td>
                  <input type="text" name="autor" id="autor" class="add_vanz" style="width: 95%;" 
                         maxlength="255"
                         value="<?php echo htmlspecialchars(!empty($article['autor']) ? $article['autor'] : 'AUTOLAND România'); ?>"
                         placeholder="Nume autor">
                  <div style="font-size: 11px; color: #666; margin-top: 3px;">
                    Numele autorului articolului. Dacă este lăsat gol, se va folosi "AUTOLAND România".
                  </div>
                  <div id="autor_warning" style="font-size: 11px; margin-top: 3px;"></div>
                </td>
              </tr>
              
              <!-- Autor Bio -->
              <tr valign="baseline">
                <td nowrap align="right" valign="top"><strong>Autor Bio:</strong></td>
                <td>
                  <textarea name="autor_bio" id="autor_bio" class="add_vanz" style="width: 95%;" rows="4"
                            mce_editable="false" placeholder="Biografia autorului"><?php 
                    $default_bio = 'AUTOLAND România este dealer auto de încredere din Iași cu peste 18 ani de experiență în importul și comercializarea de autoturisme rulate, second hand, verificate tehnic. Oferim garanție comercială extinsă, finanțare flexibilă și servicii complete: RAR, omologare, înmatriculare, asistență rutieră. Specialiști în import mașini din Germania și Europa de Vest, cu transparență totală și documentație completă pentru fiecare vehicul.';
                    echo htmlspecialchars(!empty($article['autor_bio']) ? $article['autor_bio'] : $default_bio);
                  ?></textarea>
                  <div style="font-size: 11px; color: #666; margin-top: 3px;">
                    Biografia autorului care va apărea la finalul articolului (text simplu, fără HTML). Dacă este lăsată goală, se va folosi biografia default.
                  </div>
                  <div id="autor_bio_warning" style="font-size: 11px; margin-top: 3px;"></div>
                </td>
              </tr>
              
              <!-- Submit buttons -->
              <tr valign="baseline">
                <td nowrap align="right">&nbsp;</td>
                <td style="padding-top: 20px;">
                  <input type="submit" value="💾 Salvează Modificările" style="padding: 10px 30px; background: #0066cc; color: white; font-weight: bold; font-size: 14px; cursor: pointer;">
                  <input type="button" value="Anulează" onclick="window.location='articole_manage.php';" style="padding: 10px 30px; margin-left: 10px;">
                </td>
              </tr>
            </table>
            <input type="hidden" name="MM_update" value="form1">
          </form>
        </td>
      </tr>
      <tr>
        <td align="center" bgcolor="#eeeeee">&nbsp;</td>
      </tr>
      <tr>
        <td align="center">
          <input type="button" value="« Vezi Toate Articolele" onclick="window.location='articole_manage.php';" style="padding: 8px 20px;" />
          <input type="button" value="Vezi pe Site »" onclick="window.open('../articole/<?php echo $article['slug']; ?>', '_blank');" style="padding: 8px 20px; background: #009900; color: white; font-weight: bold;" />
        </td>
      </tr>
    </table>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      
      // ==========================================
      // Generic length checker - MUST BE FIRST
      // ==========================================
      function checkLength(fieldId, maxLength) {
        var field = document.getElementById(fieldId);
        var warning = document.getElementById(fieldId + '_warning');
        
        if (!field || !warning) {
          // Silently return if element doesn't exist (optional fields)
          return;
        }
        
        var length = field.value.length;
        var percent = (length / maxLength) * 100;
        
        if (percent >= 90) {
          warning.style.color = '#ff0000';
          warning.style.fontWeight = 'bold';
          warning.textContent = '⚠️ ' + length + '/' + maxLength + ' caractere (APROAPE DE LIMITĂ!)';
        } else if (percent >= 75) {
          warning.style.color = '#ff9900';
          warning.style.fontWeight = 'bold';
          warning.textContent = '⚠️ ' + length + '/' + maxLength + ' caractere';
        } else {
          // Show counter even when empty (0/X caractere)
          warning.style.color = '#666';
          warning.style.fontWeight = 'normal';
          warning.textContent = length + '/' + maxLength + ' caractere';
        }
      }
      
      // ==========================================
      // Event Listeners
      // ==========================================
      
      // Meta description character counter
      document.getElementById('meta_description').addEventListener('input', function() {
      var length = this.value.length;
      var counter = document.getElementById('meta_count');
      counter.textContent = length + ' caractere';
      
      if (length < 120) {
        counter.style.color = '#ff9900';
        counter.textContent += ' ⚠️ (minim 120 recomandat)';
      } else if (length >= 120 && length <= 160) {
        counter.style.color = '#009900';
        counter.textContent += ' ✓ (perfect!)';
      } else if (length > 160 && length <= 500) {
        counter.style.color = '#ff0000';
        counter.textContent += ' ⚠️ (maxim 160 recomandat, dar 500 permis)';
      } else {
        counter.style.color = '#ff0000';
        counter.textContent += ' ❌ (DEPĂȘIT! Max 500)';
      }
    });
    
    // Excerpt counter
    document.getElementById('excerpt').addEventListener('input', function() {
      checkLength('excerpt', 300);
    });
    
    // Title counter
    document.getElementById('titlu').addEventListener('input', function() {
      checkLength('titlu', 255);
    });
    
    // Tags counter
    document.getElementById('tags').addEventListener('input', function() {
      checkLength('tags', 500);
    });
    
    // Imagine counter
    var imagineFeaturedEl = document.getElementById('imagine_featured');
    if (imagineFeaturedEl) {
      imagineFeaturedEl.addEventListener('input', function() {
        checkLength('imagine_featured', 500);
      });
    }
    
    // CTA text counter
    document.getElementById('cta_text').addEventListener('input', function() {
      checkLength('cta_text', 100);
    });
    
    // CTA link counter
    document.getElementById('cta_link').addEventListener('input', function() {
      checkLength('cta_link', 500);
    });
    
    // Meta title counter
    document.getElementById('meta_title').addEventListener('input', function() {
      checkLength('meta_title', 70);
    });
    
    // Autor counter
    document.getElementById('autor').addEventListener('input', function() {
      checkLength('autor', 255);
    });
    
    // Alt text counter
    var imagineAltTextEl = document.getElementById('imagine_alt_text');
    if (imagineAltTextEl) {
      imagineAltTextEl.addEventListener('input', function() {
        checkLength('imagine_alt_text', 255);
      });
    }
    
    // ==========================================
    // Initialize counters on page load
    // ==========================================
    document.getElementById('meta_description').dispatchEvent(new Event('input'));
    
    // Initialize all checkLength counters (always show, even if empty)
    checkLength('meta_title', 70);
    checkLength('titlu', 255);
    checkLength('excerpt', 300);
    checkLength('tags', 500);
    if (document.getElementById('imagine_featured')) {
      checkLength('imagine_featured', 500);
    }
    if (document.getElementById('imagine_alt_text')) {
      checkLength('imagine_alt_text', 255);
    }
    checkLength('cta_text', 100);
    checkLength('cta_link', 500);
    checkLength('autor', 255);
    
    
    // Form validation before submit
    document.getElementById('articleForm').addEventListener('submit', function(e) {
      // Validate TinyMCE content (required field)
      var ed = tinyMCE.getInstanceById('text');
      var tinyMCEContent = ed ? ed.getContent() : '';
      if (!tinyMCEContent || tinyMCEContent.trim().length === 0) {
        alert('⚠️ Conținutul articolului este OBLIGATORIU! Te rugăm să completezi conținutul în editorul WYSIWYG.');
        e.preventDefault();
        return false;
      }
      
      var meta_desc = document.getElementById('meta_description').value;
      var meta_length = meta_desc.length;
      
      if (meta_length < 120) {
        if (!confirm('Meta Description are doar ' + meta_length + ' caractere (minim 120 recomandat). Continui?')) {
          e.preventDefault();
          return false;
        }
      }
      
      if (meta_length > 500) {
        alert('Meta Description depășește 500 caractere! Reduce textul.');
        e.preventDefault();
        return false;
      }
      
      // Check other fields
      var fields = [
        {id: 'titlu', max: 255, name: 'Titlu'},
        {id: 'meta_title', max: 70, name: 'Meta Title'},
        {id: 'excerpt', max: 300, name: 'Excerpt'},
        {id: 'tags', max: 500, name: 'Tags'},
        {id: 'imagine_featured', max: 500, name: 'Imagine Featured'},
        {id: 'imagine_alt_text', max: 255, name: 'Alt Text Imagine'},
        {id: 'cta_text', max: 100, name: 'CTA Text'},
        {id: 'cta_link', max: 500, name: 'CTA Link'},
        {id: 'autor', max: 255, name: 'Autor'}
      ];
      
      for (var i = 0; i < fields.length; i++) {
        var field = document.getElementById(fields[i].id);
        if (field && field.value.length > fields[i].max) {
          alert(fields[i].name + ' depășește ' + fields[i].max + ' caractere! Reduce textul.');
          field.focus();
          e.preventDefault();
          return false;
        }
      }
      
      return true;
    });
    }); // End DOMContentLoaded
    </script>
    
    </td><!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>
<?php
?>

