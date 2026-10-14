<?PHP

$hostname_autoland = "localhost";
$database_autoland = "autoland_autoland";
$username_autoland = "autoland_autolan";
$password_autoland = "p4zj2J48bG";
$autoland = mysql_connect($hostname_autoland, $username_autoland, $password_autoland) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($database_autoland, $autoland);

$query_curs_valutar = "select * from curs_valutar order by id limit 2";
$result = mysql_query($query_curs_valutar) or die(mysql_error());
$row = mysql_fetch_assoc($query);

echo "<pre>";
	print_r($row);
echo "</pre>";

/*
echo "
	<table>
		<thead>
			<tr>
				<th>Curs Valutar ".$row['data']."</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<table>
						<tbody>
							<tr>
								<td>1 EUR</td>
								<td>".$row['EUR']."</td>
								<td>RON</td>
							</tr>
							<tr>
								<td>1 USD</td>
								<td>".$row['USD']."</td>
								<td>RON</td>
							</tr>
							<tr>
								<td>1 HRK</td>
								<td>".$row['HRK']."</td>
								<td>RON</td>
							</tr>
							<tr>
								<td>1 CZK</td>
								<td>".$row['CZK']."</td>
								<td>RON</td>
							</tr>
							<tr>
								<td>1 PLN</td>
								<td>".$row['PLN']."</td>
								<td>RON</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
";
*/
?>
