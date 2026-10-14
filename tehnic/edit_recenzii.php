<?php 

require_once('../includes/config.php');
$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

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

$currentPage = $_SERVER["PHP_SELF"];

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

$headerTitle = 'Vizualizare recenzii';
include 'header2.php';
?>
<td valign="top">
    
<?php

if (isset($_POST['submit']))
{
    unset($_POST['submit']);

	
	$update = "UPDATE `recenzii2` SET";
	$n=1;
	$nr=count($_POST);
	foreach($_POST as $key => $val)
	{
	    if ($n == $nr)
	    {
	        $update .= " `$key` = '$val'";    
	    } else {
	        $update .= " `$key` = '$val',";
	    }
	    $n++;
	}
	$update .= " WHERE `recenzii2`.`id` = '$id'";

	if ($connection->query($update))
	{
	    echo "<h3 class=\"alert alert-success\">Informatiile au fost updatate cu succes.</h3>";
	} else {
	    echo "<h3 class=\"alert alert-danger\">".$connection->error."</h3>";
	}

//    echo "<pre>"; var_dump($_POST); echo "</pre>";
}

//    echo "<h3 class='alert alert-danger'>La pagina asta inca se lucreaza, ai vrut sa editezi recenzia cu id $id</h3>";
    // Aici poti sa te joci

$id=(filter_var($_GET['id'], FILTER_VALIDATE_INT))?$_GET['id']:'NULL';

if (strcmp($id,'NULL') == 0) 
{
	echo "Papuceasca matii, vrei sa ma pacalesti ai?";
} else
{
    $q = $connection->query("SELECT * FROM recenzii2 WHERE id=$id");
    $r = $q->fetch_assoc();    

?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center alert alert-info">Editare recenzie id <?php echo $id; ?></h3>
            <form id="update_recenzii" action="" method="post">

              <h5>Data si ora solicitarii (sub formatul 2017-07-16 23:17:42)</h5>
              <div class="form-group">
                <input type="text" class="form-control" id="data_solicitare" name="data_solicitare" value="<?php echo $r['data_solicitare'];?>">
              </div>

              <h5>Data si ora recenziei (sub formatul 2017-07-16 23:17:42)</h5>
              <div class="form-group">
                <input type="text" class="form-control" id="data_recenzie" name="data_recenzie" value="<?php echo $r['data_recenzie'];?>">
              </div>

              <h5>Status</h5>
              <div class="form-group">
                <select class="form-control" id="status" name="status">
<?php
                if($r['status'] == 1)
                {
                    echo "<option value=\"1\">Online</option>;<option value=\"0\">Offline</option>";
                } else {
                    echo "<option value=\"0\">Offline</option>;<option value=\"1\">Online</option>";
                }
?>
                </select>
              </div>

              <div class="form-group">
                <label for="nume">Numele</label>
                <input type="text" class="form-control" id="nume" name="nume" value="<?php echo $r['nume'];?>">
              </div>
            
              <div class="form-group">
                <label for="prenume">Prenumele</label>
                <input type="text" class="form-control" id="prenume" name="prenume" value="<?php echo $r['prenume'];?>">
              </div>

              <div class="form-group">
                <label for="adresa_email">Email</label>
                <input type="text" class="form-control" id="adresa_email" name="adresa_email" value="<?php echo $r['adresa_email'];?>">
              </div>
             
              <div class="form-group">
                <textarea class="form-control" id="recenzie" name="recenzie" rows="15"><?php echo $r['recenzie'];?></textarea>
              </div>

              <button type="submit" class="btn btn-primary" name="submit">Update</button>
              <br />
            </form>
        </div>
    </div>
</div>
<?php } ?>
</td>
  </tr>
</table>
</body>
</html>
