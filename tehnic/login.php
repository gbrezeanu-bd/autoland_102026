<?php 
require_once('../includes/config.php'); 
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['textfield'])) {
  $loginUsername=$_POST['textfield'];
  $password=$_POST['textfield2'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "index.php";
  $MM_redirectLoginFailed = "login.php";
  $MM_redirecttoReferrer = true;                     
  
  // $LoginRS__query=sprintf("SELECT user, pass FROM luser WHERE user='%s' AND pass='%s'",
  // get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), get_magic_quotes_gpc() ? $password : addslashes($password)); 

  // PHP 8+ compatible - magic_quotes_gpc removed
  $loginUsername_safe = $connection->real_escape_string($loginUsername);
  $password_safe = $connection->real_escape_string($password);
  
  $LoginRS__query=sprintf("SELECT user, pass FROM luser WHERE user='%s' AND pass='%s'",
    $loginUsername_safe, $password_safe); 
   
  $LoginRS = $connection->query($LoginRS__query) or die($connection->error);
  $loginFoundUser = mysqli_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && true) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Log-in</title>
<link href="admin-theme.css" rel="stylesheet" type="text/css" />
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #0000FF}
.style3 {color: #009900}
-->
</style>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body class="adm-body">
<div class="adm-login">
  <form id="form1" name="form1" method="post" action="<?php echo $loginFormAction; ?>" class="adm-login-card">
    <div class="adm-login-head">
      <img src="../themes/automotive/images/logo_autoland_2.png" alt="AUTOLAND" width="170" height="45" />
      <span>Secțiune administrare</span>
    </div>
    <div class="adm-login-body">
      <label>Utilizator
        <input type="text" name="textfield" autocomplete="username" autofocus="autofocus" />
      </label>
      <label>Parolă
        <input type="password" name="textfield2" autocomplete="current-password" />
      </label>
      <input type="submit" name="Submit" value="Autentificare" />
    </div>
  </form>
</div>
</body>
</html>
