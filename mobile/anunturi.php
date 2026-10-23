<?php
require_once('/home/autoland/public_html/Connections/autoland.php');
include('paginator.class.2.php');

$pagina_curenta="ANUNTURI";
include 'head.php';
include 'meniu_mobile.php';

$query = $query = "SELECT COUNT(*) FROM masini WHERE stare_anunt LIKE 1 and oferta_speciala='1' AND COALESCE(cat,'') NOT LIKE 'Utilaje%'";
$result = mysql_query($query) or die(mysql_error());
$num_rows = mysql_fetch_row($result);

$pages = new Paginator;
$pages->items_total = $num_rows[0];
$pages->mid_range = 5; // Number of pages to display. Must be odd and > 3
$pages->paginate();

echo $pages->display_pages();
echo "<span class=\"\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";

$query = "SELECT * FROM masini WHERE stare_anunt LIKE 1 and oferta_speciala='1' AND COALESCE(cat,'') NOT LIKE 'Utilaje%' order by id desc $pages->limit";
$result = mysql_query($query) or die(mysql_error());

echo "<br/><br/>";
while ($data = mysql_fetch_assoc($result))
{

$sql_poze = "SELECT * FROM fotov WHERE oferta = ".$data['id'];
$query_poze = mysql_query ($sql_poze);

/*
$query_Recordset1 		= "SELECT DISTINCT(den) FROM zone ORDER BY den ASC";
$Recordset1 			= mysql_query($query_Recordset1, $autoland) or die(mysql_error());
$row_Recordset1 		= mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 	= mysql_num_rows($Recordset1);
*/

$_GET['combustibil']="";
$_GET['an']="";
$_GET['pret']="";
$_GET['prov_enienta']="";
$_GET['users']="";


echo "
<div class=\"anunt\">
	<!-- Place somewhere in the <body> of your page -->
	<div class=\"flexslider\">
	  <ul class=\"slides\">";

	while ($result_poze = mysql_fetch_assoc ($query_poze))
	{
		echo "
		<li>
			<img height=\"480\" width=\"640\" src=\"https://autolandromania.ro/foto/".$result_poze['file']."\" />
		</li>
		
		";
	}
echo "
	  </ul>
	</div>
	<div class=\"titlu\">
		".$data['tip']." <br/>";
		if (empty($data['pret_vechi']))
		{
			echo "Pret: ".$data['pret']."<br/>";
			}
		else 
		{
			echo "Pret: <del>".$data['pret_vechi']."</del> ".$data['pret']."<br/>";
			}
echo "		
		Cod: #S10".$data['id']." <br/>
		An fabricatie: ".$data['an']." <br/>
	</div>
	<div>
		<div class=\"menu2_".$data['id']."\" style=\"display:none;background-color:green;\">";
	echo $data['detalii'];
	echo "
		</div>
		<button id=\"detalii_".$data['id']."\">Detaii despre anunt ...</button>
	</div>
</div>
<br /><br /><br />

<script type=\"text/javascript\">

	$(document).ready(function(){
	    $('#detalii_".$data['id']."').click(function(){
	        $('.menu2_".$data['id']."').toggle();
	    });
	});

</script>";
echo "<br/><br/>";
}

echo $pages->display_pages();
echo "<br/>";
echo "<p class=\"pagination\">Page: $pages->current_page of $pages->num_pages</p>\n";
include 'footer.php'; 
?>