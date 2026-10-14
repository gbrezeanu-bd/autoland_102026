<?php
//require_once('/home/autoland/old.autolandromania.ro/Connections/autoland.php');
include_once './includes/config.php';

$autoland = mysql_connect($hostname, $username, $password, $database);

$query_text      = "SELECT id, home FROM texte WHERE id = 1";
$text            = mysql_query($query_text, $autoland) or die(mysql_error());
$row_text        = mysql_fetch_assoc($text);
$totalRows_text  = mysql_num_rows($text);
                                               
$query_marci     = "SELECT * FROM `search` ORDER BY categorie, marca ASC";
$marci           = mysql_query($query_marci, $autoland) or die(mysql_error());
$row_marci       = mysql_fetch_assoc($marci);
$totalRows_marci = mysql_num_rows($marci);


$pagina_curenta="INDEX";
include 'head.php';
include 'meniu_mobile.php';
?>


<div class="anunt">
<?php echo htmlspecialchars_decode($row_text['home']); ?>
</div>


<script src="js/jquery.slicknav.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#menu').slicknav();
});
$(document).ready(function(){
    $("#detalii").click(function(){
        $("#menu2").toggle();
    });
});
$(document).ready(function(){
    $("#detalii2").click(function(){
        $("#menu3").toggle();
    });
});
</script>
<?php include 'footer.php'; ?>
