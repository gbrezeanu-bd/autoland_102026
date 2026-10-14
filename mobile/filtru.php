<?php
require_once('/home/autoland/public_html/Connections/autoland.php');
$pagina_curenta="FILTRU";
include 'head.php';
include 'meniu_mobile.php';

$query_Recordset1 		= "SELECT DISTINCT(den) FROM zone ORDER BY den ASC";
$Recordset1 			= mysql_query($query_Recordset1, $autoland) or die(mysql_error());
$row_Recordset1 		= mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 	= mysql_num_rows($Recordset1);

$_GET['combustibil']="";
$_GET['an']="";
$_GET['pret']="";
$_GET['prov_enienta']="";
$_GET['users']="";

?>
<br/>
<center><h1 style="color: black;">Ne cerem scuze, filtru in constructie, inca nu este functional</h1></center>
<br/>
<!--
<form action="" method="post" name="form2" target="_self" id="form2" onsubmit="selectProv()">
	<div class="">
		Cuvant Cheie:<br/>
		<input name="txtsrc" type="text" class="vanzari_box"/>
	</div>
	<hr align="left" width="95%" />
	<div class="">
	Categorie :<br/>
		<select name="users" id="users" onchange="updateSubCateg(this.value);showUser(this.value);" class="vanzari_box">
			<option value="selectati" <?php if (!(strcmp("selectati", $_GET['users']))) {echo "selected=\"selected\"";} ?>>selectati</option>
				<?php  
					while ($row_Recordset1 = mysql_fetch_assoc($Recordset1))
					{
						echo '<option value="'.$row_Recordset1['den'].'"';
						if (!(strcmp($row_Recordset1['den'], $_GET['users']))) {echo "selected=\"selected\"";} 
						echo '>'.$row_Recordset1['den'].'</option>';                                                                  
					} 
					$rows 	= mysql_num_rows($Recordset1);
					if($rows > 0) { mysql_data_seek($Recordset1, 0); $row_Recordset1 = mysql_fetch_assoc($Recordset1); }
				?>
		</select>
	</div>
	<div class="">
		Subcategorie :<br/>
		<div id="subcategHint">Selectati o categorie intai</div>
	</div>
	<div class="">
		Marca :<br/>
		<div id="txtHint">Selectati o categorie intai </div>
	</div>
	<div class="">
		Comb. : <br/>
		<select name="combustibil" class="vanzari_box" id="combustibil">
			<option value="fara" <?php {echo "selected=\"selected\"";} ?>>selectati</option> 
			<option value="benzina" <?php if (!(strcmp("benzina", $_GET['combustibil']))) {echo "selected=\"selected\"";} ?>>benzina</option>
			<option value="motorina" <?php if (!(strcmp("motorina", $_GET['combustibil']))) {echo "selected=\"selected\"";} ?>>motorina</option>
		</select>  
	</div>
	<div class="">
		An fabr. :<br/>
		<select name="an" class="vanzari_box" id="an">        
			<?php 
			for ($i = 11; $i >= 1; $i--)  
			  {
			    $data_pv = date("Y")-$i;
			     echo '<option value="'.$data_pv.'"'; 
			     if (!(strcmp($data_pv, $_GET['an']))) {echo 'selected="selected"';} 
			     echo ">".$data_pv." sau mai nou</option>";
			    }                                                
			?>                                                      
		</select>		
	</div>
	<div class="">
		Pret :<br/>
		<select name="pret" class="vanzari_box" id="pret">
			<option value="14000000" selected="selected" <?php if (!(strcmp(14000000, $_GET['pret']))) {echo "selected=\"selected\"";} ?>>selectati</option>
			<option value="5000" <?php if (!(strcmp(5000, $_GET['pret']))) {echo "selected=\"selected\"";} ?>>sub 5.000 euro</option>
			<option value="8000" <?php if (!(strcmp(8000, $_GET['pret']))) {echo "selected=\"selected\"";} ?>>sub 8.000 euro</option>
			<option value="13000" <?php if (!(strcmp(13000, $_GET['pret']))) {echo "selected=\"selected\"";} ?>>sub 13.000 euro</option>
			<option value="20000" <?php if (!(strcmp(20000, $_GET['pret']))) {echo "selected=\"selected\"";} ?>>sub 20.000 euro</option>
		</select>		
	</div>
	<div class="">
		Provenienta :<br/>
		<select name="prov_enienta" class="vanzari_box" id="prov_enienta">
			<option value="fara" <?php {echo "selected=\"selected\"";} ?>>selectati</option>
			<option value="Romania" <?php if (!(strcmp("Romania", $_GET['prov_enienta']))) { echo "selected=\"selected\""; } ?>>Romania</option>
			<option value="Import" <?php if (!(strcmp("Import", $_GET['prov_enienta']))) { echo "selected=\"selected\""; } ?>>Import</option>                                                  
		</select>		
	</div>
	<div class="" style="padding-top:10px; padding-bottom:10px;">
		<input name="image" type="image" src="https://www.autolandromania.ro/images/search-button.jpg"/>
	</div>
</form>
-->
<?php
include 'footer.php'; 
?>
