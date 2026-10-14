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

$MM_restrictGoTo = "login.php";
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

$headerTitle = 'Upload Video';
include 'header.php';
?>

<!-- InstanceBeginEditable name="main" -->
<style>
.upload-container {
	max-width: 1200px;
	margin: 20px auto;
	padding: 20px;
	background: #f9f9f9;
	border-radius: 8px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.upload-header {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	padding: 20px;
	border-radius: 8px 8px 0 0;
	margin: -20px -20px 20px -20px;
}
.upload-header h2 {
	margin: 0 0 10px 0;
	font-size: 24px;
}
.upload-header .info {
	font-size: 13px;
	opacity: 0.95;
	line-height: 1.6;
}
.upload-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
	gap: 15px;
	margin-bottom: 25px;
}
.upload-item {
	background: white;
	border: 2px solid #e0e0e0;
	border-radius: 8px;
	padding: 15px;
	transition: all 0.3s ease;
}
.upload-item:hover {
	border-color: #667eea;
	box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
}
.upload-item-header {
	display: flex;
	align-items: center;
	margin-bottom: 12px;
	padding-bottom: 10px;
	border-bottom: 1px solid #f0f0f0;
}
.upload-item-number {
	background: #667eea;
	color: white;
	width: 28px;
	height: 28px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	font-weight: bold;
	font-size: 14px;
	margin-right: 10px;
}
.upload-item-title {
	font-weight: 600;
	color: #333;
	font-size: 15px;
}
.upload-field {
	margin-bottom: 12px;
}
.upload-field:last-child {
	margin-bottom: 0;
}
.upload-field label {
	display: block;
	font-size: 13px;
	color: #666;
	margin-bottom: 6px;
	font-weight: 500;
}
.upload-field input[type="file"] {
	width: 100%;
	padding: 8px;
	border: 1px solid #ddd;
	border-radius: 4px;
	background: #fafafa;
	font-size: 13px;
	transition: all 0.2s ease;
}
.upload-field input[type="file"]:hover {
	border-color: #667eea;
	background: #fff;
}
.upload-field input[type="file"]:focus {
	outline: none;
	border-color: #667eea;
	box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
.upload-submit {
	text-align: center;
	margin-top: 30px;
	padding-top: 20px;
	border-top: 2px solid #e0e0e0;
}
.upload-submit input[type="submit"] {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	border: none;
	padding: 14px 40px;
	font-size: 16px;
	font-weight: 600;
	border-radius: 6px;
	cursor: pointer;
	transition: all 0.3s ease;
	box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
}
.upload-submit input[type="submit"]:hover {
	transform: translateY(-2px);
	box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
}
.upload-submit input[type="submit"]:active {
	transform: translateY(0);
}
@media (max-width: 768px) {
	.upload-grid {
		grid-template-columns: 1fr;
	}
	.upload-container {
		margin: 10px;
		padding: 15px;
	}
}
</style>

<td valign="top">
	<div class="upload-container">
		<div class="upload-header">
			<h2>📹 Upload Videouri și Thumbnail-uri</h2>
			<div class="info">
				<strong>Formate video:</strong> MP4, WebM, MOV, AVI (max 500MB)<br />
				<strong>Formate thumbnail:</strong> JPG, PNG, GIF (opțional - se generează automat dacă lipsește)
			</div>
		</div>
		
		<form enctype="multipart/form-data" action="upload_video2.php?id=<?php echo $_GET['id']; ?>" method="POST">
			<div class="upload-grid">
				<?php for($i = 0; $i < 4; $i++) { ?>
				<div class="upload-item">
					<div class="upload-item-header">
						<div class="upload-item-number"><?php echo ($i + 1); ?></div>
						<div class="upload-item-title">Video <?php echo ($i + 1); ?></div>
					</div>
					<div class="upload-field">
						<label for="uploaded_<?php echo $i; ?>">📹 Fișier Video:</label>
						<input name="uploaded[]" id="uploaded_<?php echo $i; ?>" type="file" accept="video/*" />
					</div>
					<div class="upload-field">
						<label for="thumbnails_<?php echo $i; ?>">🖼️ Thumbnail (opțional):</label>
						<input name="thumbnails[]" id="thumbnails_<?php echo $i; ?>" type="file" accept="image/*" />
					</div>
				</div>
				<?php } ?>
			</div>
			
			<div class="upload-submit">
				<input type="submit" value="🚀 Încarcă Videouri" />
			</div>
		</form>
	</div>
</td>
<!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>

