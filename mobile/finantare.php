<?php 
require_once('/home/autoland/public_html/Connections/autoland.php');

$query_text = "SELECT id, leasing FROM texte WHERE id = 1";
$text = mysql_query($query_text, $autoland) or die(mysql_error());
$row_text = mysql_fetch_assoc($text);
$totalRows_text = mysql_num_rows($text);
                                                
$query_marci = "SELECT * FROM `search` ORDER BY categorie, marca ASC";
$marci = mysql_query($query_marci, $autoland) or die(mysql_error());
$row_marci = mysql_fetch_assoc($marci);
$totalRows_marci = mysql_num_rows($marci);

$pagina_curenta="FINANTARE";
include 'head.php';
include 'meniu_mobile.php';
?>
<div class="anunt">
<?php echo $row_text['leasing']; ?>
<br /><br />
</div>
<?php include 'footer.php'; ?>
