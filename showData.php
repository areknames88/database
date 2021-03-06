<?php
		
				if (isset($_POST['delete'])) {
					
					
					$connection = pg_connect("host=localhost dbname=inventario port=5432 user=postgres password=postgres") or die('connection failed');	
					$arrayDoll = explode(" ", $_POST['idDoll']);
					
					foreach($arrayDoll as $doll) {
						$query = "DELETE FROM collezione WHERE collezione.id = $doll";
						$result = pg_query($connection, $query) or die("fallito");
					}
					
					//$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?";
					$actual_link = $_SERVER["HTTP_REFERER"];
					
					$queryCounter = "SELECT * FROM collezione";
					$resultCounter = pg_query($connection, $queryCounter) or die("fallito");
					$numberRows = pg_num_rows($resultCounter);
					
					
					$pagineTotali = $numberRows / 7;
					$indexPoint;
					if (is_float($pagineTotali)) {
						$indexPoint = strrpos($pagineTotali, ".");
						$pagineTotali = substr($pagineTotali, 0, $indexPoint) + 1;
					}
					
					
					preg_match('/\?.*/', $actual_link, $output);
					$string = str_replace("?", "", $output[0]);
					$arrayParameters = explode("&", $string);
					
					$numeroPagina;
					foreach($arrayParameters as $value) {
						
						if (strpos($value, 'pag') !== false) {
							$indexUguale = strpos($value, "=");
							$numeroPagina = substr($value, $indexUguale + 1, strlen($value));
						}
					}
					
					
					
					
					
					/*if (isset($_GET['pag'])) {
						$pagUrl = $_GET['pag'];	
						if ($numberRows == 7) {
							$pagUrl = 1;
						}
						$actual_link = $actual_link . "&pag=$pagUrl";
					}
					
					if (isset($_GET['order'])) {
						$order = $_GET['order'];
						
						$actual_link = $actual_link . "&order=$order";
					
					}
					
					if (isset($_GET['sort'])) {
						$sort = $_GET['sort'];
						
						$actual_link = $actual_link . "&sort=$sort";
					} 
					
					
					
					
				 */
					$paginaMeno = $numeroPagina - 1;
						if ($numberRows == 7) {
				$modifiedLink = preg_replace("&pag=.+?&", '&pag=1', $actual_link);
				
					header('Location: '.$modifiedLink);
					} else if ($pagineTotali == $paginaMeno) {
						$modifiedLink = preg_replace("&pag=.+?&", '&pag='.$paginaMeno, $actual_link);
						header('Location: '.$modifiedLink);
					} else {
					header('Location: '.$actual_link);
					
					}
					pg_close($connection); 
				}
?>

<!DOCTYPE html>

<html lang="it">
<head>
<meta charset="UTF-8">
<title>Inventario di Nicola</title>
<meta name="description" content="Inventario di Nicola" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="shortcut icon"  href='./images/favicon.png'/>
<link rel="stylesheet" href="js/jquery-ui-1.12.1/jquery-ui.min.css" />
<script src="js/jquery.min.js"></script>
<script src="js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
<style>



		* {
		font-family: Arial;
	}

	body {
		background-color: lightgrey;
		font-family: Arial, Verdana, Georgia;
	}
	
	.buttons {
		padding: 10px;
		font-size: 16pt;
		background-color: lightblue;
		border: 0.5px solid black;
		box-shadow: 2px 2px 2px 2px grey;
	}
	
	h3 {
		font-variant: small-caps;
		font-weight: normal;
		
	}
	input[type='text'] {
		padding: 5px;
		border: none;
		border-bottom: 2.3px dotted black;
		font-size: 12pt;
		background-color: lightgrey;
	}
	#mainTable {
		width: 90%; 
		font-size: 10pt;
		font-family: Verdana;
		height: 100%;
		z-index: 1;
		position: relative;
		box-shadow: 2px 2px 2px 2px grey;
	}
	
	select {
		
			color: #1f1fc1;
		
	}
	
	#mainTable tr td {
		border: 0.2px solid grey;
		background-color: lightblue;
		color: black;
		width: 10%;
		
		
	}
	
	#scroller {
		height: 80%;
	
			margin-left: 5%;
	}
	
	#intestazione td {
		background-color: lightgrey !important;
		color: ivory;
		font-weight: bold;
		
	}
	
	.coverCell {
		
	}
	
	.hiddenImage {
	
		display: none;
		
	}
	
	
	
	.coverCell {
		position: relative;
		overflow: visible;
	}
	
	.coverCell:hover .hiddenImage {
		
		width: 200px;
		left: -50px;
		top:  -30px;
		display: block;
		position: absolute;
		border: 4px solid grey;
		z-index: 2;
	
	}
	
	.emptyCell {
		background-color: transparent !important;
		border: none !important;
	}
	
	#backward {
		position: absolute;
		top: 85%;
		left: 40%;
	}
	
	#forward {
		position: absolute;
		top: 85%;
		left: 42%;
	}
	
	.intestationLinks {
		color: white; text-decoration: none; font-size: inherit;
		
		
	}
	
	.intestationLinks:hover {
		color: orange;
	}
	
	.intestationLinks:nth-of-type(1) {
		border-bottom: 1px solid white;
	}
	
	
	.intestations {
		position: relative;
	}
	
	
	
	.intestationDropdown {
		position: absolute;
		width: inherit;
		top: 0px;
		left: 21%;
		padding: 5px;
		background-color: rgba(13, 80, 188, 0.5);
		display: none;
	}
	

	
	.intestations:hover .intestationDropdown {
		display: block;
	}
	
	.deleteButton {
			background-color: transparent;
			background-image: url('images/delete.png');
			background-size: contain;
			border: none;
			pointer-events: none;
			opacity: 0.5;
			font-size: 16pt;
			cursor: pointer;
			background-repeat: no-repeat;
	}
	
	.deleteButton.active {
		pointer-events: auto;
		opacity: 1;
	}
	
</style>
</head>
<body>

	<div id="scroller">
	<table cellspacing="0" cellpadding="1" id="mainTable">
	<form action="showData.php" method="POST">
		
		<input type='hidden' id='counter' name="pag" />
		<tr id="intestazione" style="text-align: center">
	
		<td><h3 class="intestations">Titolo <p class="intestationDropdown">
					<a class="intestationLinks" href="<?php echo $_SERVER['PHP_SELF'] . "?pag=1&sort=titolo&order=ascendente" ?>" title="Ordina per titolo in senso ascendente">Ascendente</a>
					<br />
					<a class="intestationLinks" href="<?php echo $_SERVER['PHP_SELF'] . "?pag=1&sort=titolo&order=discendente" ?>" title="Ordina per titolo in senso discendente">Discendente</a>
		</p></h3></td>
		<td><h3 class="intestations">Artista/Gruppo
			<p class="intestationDropdown">
					<a class="intestationLinks" href="<?php echo $_SERVER['PHP_SELF'] . "?pag=1&sort=artista&order=ascendente" ?>" title="Ordina per artista/gruppo in senso ascendente">Ascendente</a>
					<br />
					<a class="intestationLinks" href="<?php echo $_SERVER['PHP_SELF'] . "?pag=1&sort=artista&order=discendente" ?>" title="Ordina per artista/gruppo in senso discendente">Discendente</a>
		</p>
		</h3></td>
		<td><h3 class="intestations">Anno <p class="intestationDropdown">
					<a class="intestationLinks" href="<?php echo $_SERVER['PHP_SELF'] . "?pag=1&sort=anno&order=ascendente" ?>" title="Ordina per anno in senso ascendente">Ascendente</a>
					<br />
					<a class="intestationLinks" href="<?php echo $_SERVER['PHP_SELF'] . "?pag=1&sort=anno&order=discendente" ?>" title="Ordina per anno in senso discendente">Discendente</a>
		</p></h3></td>
		<td><h3 class="intestations">Etichetta<p class="intestationDropdown">
					<a class="intestationLinks" href="<?php echo $_SERVER['PHP_SELF'] . "?pag=1&sort=etichetta&order=ascendente" ?>" title="Ordina per etichetta in senso ascendente">Ascendente</a>
					<br />
					<a class="intestationLinks" href="<?php echo $_SERVER['PHP_SELF'] . "?pag=1&sort=etichetta&order=discendente" ?>" title="Ordina per etichetta in senso discendente">Discendente</a>
		</p></h3></td>
		<td><h3 class="intestations">Tipo</h3></td>
		<td><h3>Copertina</h3></td>	
		
		<td style="width: 2%;background-color: white !important">	<form method='POST'><input type='hidden' id='idDollContainer' name='idDoll' value='' /><input class='deleteButton' id="deleteButton" title='Elimina' type='submit' value='&#160;&#160;&#160;' name='delete' /></form></td>
		</tr>
		
		
	
	<?php

	
	
			$connection = pg_connect("host=localhost dbname=inventario port=5432 user=postgres password=postgres") or die('connection failed');	
			
			
		
			
			$queryCounter = "SELECT * FROM collezione";
			
			$total = pg_query($connection, $queryCounter) or die("fallito");
			$count = pg_num_rows($total);
			
				$x_pag = 7;
			
			$pag = isset($_GET['pag']) ? $_GET['pag'] : 1;
			
			if (!$pag || !is_numeric($pag)) $pag = 1; 
			
			$all_pages = ceil($count / $x_pag);

			$first = ($pag - 1) * $x_pag;
			
			$offset = ($pag - 1) * $x_pag;

	$sort;
	if (isset($_GET['sort'])) {
 
	$sort = $_GET['sort'];
} else {
  
   $sort = "titolo";
}	
	$orderParameter; 

  if (isset($_GET['order'])) {
	$orderParameter = $_GET['order'];
  } else {
	$orderParameter = '';
  }
  
	$order = '';
	if (isset($_GET['order'])) {
	if ($orderParameter == 'discendente') {
		$order = 'DESC';
	} else if ($orderParameter == 'ascendente') {
		$order = 'ASC';
	}
	} else {
		$order = 'ASC';
	}
	
	$query = "SELECT * FROM collezione ORDER BY $sort $order LIMIT $x_pag OFFSET $offset"; 

		$rs = pg_query($connection, $query) or die("Cannot execute query: $query\n");
		
		
		
		$singleurl = "/InventarioNicola/onerecord.php";
		while ($row = pg_fetch_row($rs)) {
				
				$internoCopertina;
				if ($row[7] != "empty.png") {
						$internoCopertina = $row[7];
						
				} else {
						$internoCopertina = $row[6];
				}
				
				
				echo "	<tr style='text-align: center'>
				<td style='padding: 2px'>";
				echo "<a style='color: #1f1fc1; font-size: 12.5pt; text-decoration: none; margin-right: 10px;' href=\"" . $singleurl . "?itemId=$row[0]&pag=" . ($pag) . "&ricerca=false&sort=$sort&order=$orderParameter\">";
				echo "<span style='font-style: italic; font-size: 11pt'>$row[1]</span></a></td> 	<td>$row[2]</td> 
				<td>$row[3]</td><td>$row[4]</td><td>$row[5]</td>
				<td class='coverCell' style='width: 80px; height: 120px; background-repeat: no-repeat; background-position: center center; background-image: url(inventarioImages/$row[6]); background-size: contain; background-color: lightgrey !important'> <img class='hiddenImage' src='inventarioImages/$internoCopertina' /> </td><td style='width: 2%'><input data-id='$row[0]' onclick=\"return fillDelete(this)\" type='checkbox' class='checkDelete' /></td></tr>";
				
				
				
		}

		if ($all_pages > 1){
			$pagUrl;
			if (isset($_GET['pag'])) {
			$pagUrl = $_GET['pag'];
			}
  if ($pag > 1){
    echo "<a title='Pagina Indietro' style='position: fixed; bottom: 0; left: 92%; font-weight: bold; color: #1f1fc1; font-size: 40pt; text-decoration: none; font-variant:small-caps' href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . ($pag - 1) . "&sort=$sort&order=$orderParameter\">";
    echo "<</a>&nbsp;";
	
	

  } 
  if ($all_pages > $pag){
    echo "<a title='Pagina Avanti' style='position: fixed; bottom: 0; left: 95%; font-weight: bold; color: #1f1fc1; font-size: 40pt; text-decoration: none; font-variant:small-caps' href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . ($pag + 1) . "&sort=$sort&order=$orderParameter\">";
    echo "></a>";
  }
		echo "<br />";
		//if ($all_pages < 5) {
	//for ($i = 0; $i < $all_pages; $i++) {
		//echo "<a style='color: rgb(13, 80, 188); font-size: 12.5pt; font-variant:small-caps; margin-right: 10px;' href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . ($i + 1) . "&sort=$sort&order=$orderParameter\">";
		//echo ($i + 1) . "</a>";
	//}
	//} else {
	
	$all_pages_array = array();
	for ($index = 0; $index < $all_pages; $index++) {
		array_push($all_pages_array, $index + 1);
	}
	
	
		
		if ($pag > 1) {
				echo "<a title='Vai alla prima pagina' style='color: #1f1fc1; font-size: 12.5pt; font-variant:small-caps; margin-right: 10px;' href=\"" . $_SERVER['PHP_SELF'] . "?pag=1&sort=$sort&order=$orderParameter\">";
				echo "<<</a>";
		
				echo "<a style='color: #1f1fc1; font-size: 12.5pt; font-variant:small-caps; margin-right: 10px;' href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . ($pag - 1) . "&sort=$sort&order=$orderParameter\">";
				echo "<</a>";
		}
		
		for ($i = $pag - 10; $i < $pag; $i++) {
			if (in_array($i, $all_pages_array)) {
			echo "<a style='color: #1f1fc1; font-size: 12.5pt; font-variant:small-caps; margin-right: 10px;' href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . ($i) . "&sort=$sort&order=$orderParameter\">";
				echo ($i) . "</a>";
				}
				
		}
		
		for ($i = $pag; $i < $pag+10; $i++) {
			if (in_array($i, $all_pages_array)) {
			echo "<a style='color: #1f1fc1; font-size: 12.5pt; font-variant:small-caps; margin-right: 10px;' href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . ($i) . "&sort=$sort&order=$orderParameter\">";
				echo ($i) . "</a>";
				}
				
		}
		 if ($all_pages > $pag){
			echo "<a style='color: #1f1fc1; font-size: 12.5pt; font-variant:small-caps; margin-right: 10px;' href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . ($pag + 1) . "&sort=$sort&order=$orderParameter\">";
				echo "></a>";
				echo "<a title='Vai all\'ultima pagina' style='color: #1f1fc1; font-size: 12.5pt; font-variant:small-caps; margin-right: 10px;' href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . ($all_pages) . "&sort=$sort&order=$orderParameter\">";
				echo ">></a>";
		}
		
	
	//}
  
}

// Chiudo la connessione ad DB

		
		pg_close($connection); 

	
		
		
	
		

			
	
		
		
	?>

	</form>
	
	
	
	</table>
	

		<p style="color: #1f1fc1; font-weight: bold; font-size: 12.5pt;">
		
			<?php
				$connection = pg_connect("host=localhost dbname=inventario port=5432 user=postgres password=postgres") or die('connection failed');	
			
			
		
			
			$queryCounter = "SELECT * FROM collezione";
			
			$total = pg_query($connection, $queryCounter) or die("fallito");
			$count = pg_num_rows($total);
			if ($count == 0) {
			 echo "Il database non ha elementi al suo interno";
			} else if ($count == 1) {
			 echo "C'è un solo elemento";
			} else if ($count > 1) {
				echo "Sono presenti $count elementi";
			}
			pg_close($connection);
			?>
		</p>
		<br />
		<form action="searchResults.php" method="GET">
		
			<h3 style="font-size: 11pt; color: #1f1fc1">Valore esatto:</h3>
	<input style="padding: 5px"  placeholder="Scrivere in minuscolo" autocomplete="off" type="text" name="valoreRicerca" />
	&#160;&#160;&#160;
			<select  style="padding: 5px" name="campoRicerca">
				<option value="titolo">Titolo</option>
				<option value="artista">Artista/Gruppo</option>
				<option value="anno">Anno</option>
				<option value="etichetta">Etichetta</option>
				<option value="tipo">Tipo</option>
				
			</select>
			&#160;&#160;&#160;
			<input style="padding: 3px; font-size: 12pt"  class="buttons" type="submit" value="Cerca" name="submitSearch" />
		</form>
		
		<form action="searchResults.php" method="GET">
		
			<h3 style="font-size: 11pt; color: #1f1fc1">Contiene...:</h3>
	<input style="padding: 5px" placeholder="Scrivere in minuscolo" autocomplete="off" type="text" name="valoreRicercaContiene" />
		&#160;&#160;&#160;
			<select style="padding: 5px" name="campoRicerca">
				<option value="titolo">Titolo</option>
				<option value="artista">Artista/Gruppo</option>
				<option value="anno">Anno</option>
				<option value="etichetta">Etichetta</option>
				<option value="tipo">Tipo</option>
				
			</select>
			&#160;&#160;&#160;
			<input style="padding: 3px; font-size: 12pt" class="buttons" type="submit" value="Cerca" name="submitSearch" />
		</form>
		<?php
		
			
		?>
			
	<br /><br />
<a style="margin-bottom: 100px; background-image: url('images/home.png'); background-size: contain; position: relative; background-repeat: no-repeat;  bottom: 0; font-size: 20pt; text-decoration: none" title="Vai alla pagina iniziale" href="/InventarioNicola/index.html">&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;</a>
	</div>

	<script type="application/javascript">
		$(".intestationLinks").on("click", function() {
			$(this).siblings(".intestationLinks").show();
			$(this).hide();
		});
		
		function fillDelete(elem) {
			if (elem.checked) {
			$(elem).addClass("checked");
			$("#deleteButton").addClass("active");
			var idDoll = elem.getAttribute("data-id");
			if (document.getElementById('idDollContainer').value == '') {
			document.getElementById('idDollContainer').value = idDoll;
			} else {
				document.getElementById('idDollContainer').value = document.getElementById('idDollContainer').value + " " + idDoll;
			}
			} else {
				$(elem).removeClass("checked");
				if ($(document).find(".checked").length == 0) {
					$("#deleteButton").removeClass("active");
				}
				var idDoll = elem.getAttribute("data-id");
				if (document.getElementById('idDollContainer').value.indexOf(" ") == -1) {
			document.getElementById('idDollContainer').value = document.getElementById('idDollContainer').value.replace(idDoll, '');
				} else {
					document.getElementById('idDollContainer').value = document.getElementById('idDollContainer').value.replace(" " + idDoll, '');
				}
			}
		}
		
	
	</script>
	
</body>
</html>