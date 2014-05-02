<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Company Quote</title>
     	<link rel="stylesheet" href="http://flip.hr/css/bootstrap.min.css">


	</head>
	<body>
		 <div class="container">
         
        	<div class="hero-unit">
        	<h1 align="center">Finance Database</h1>
         	
         	</div>
 
 		 </div><!-- .container -->

 		 <h3 align ="left"> Info for: Company Quote</h3>

 		 <?php
 		 	$con = mysqli_connect("localhost", "", "");
 		 	if(!$con){
 		 		exit('Connect Error (' . mysqli_connect_errno() . ')' . mysqli_connect_error());
 		 	}

 		 	mysqli_set_charset($con, 'utf-8');
			
			mysqli_select_db($con, "test_finance");
			$stock_name = mysqli_real_escape_string($con, htmlentities($_GET["company"]));

			//SELECT quote, stock_name FROM quote, company WHERE company = ? AND company.stock_name = quote.stock_name 

			//$result = mysqli_query($con, "SELECT quote, stock_name FROM quote, company WHERE company.stock_name = quote.stock_name AND (");
 		 	$companies = $stock_name.explode(", ", $stock_name);

 		 	for(int i = 0; i < count($companies); i++){
 		 		$result = $result . "quote.stock_name = `" . $companies[i] ."`";
 		 		if(i != count($companies) - 1){
 		 			$result = $result ." OR "
 		 		}
 		 	}

 		 	$result = $result . ")"
		
 		 ?>

 		 <table class = "table table-hover">
			<tr>
				<th>Stock Name</th>
				<th>Quote</th>
			</tr>
			<?php
				while($row = mysqli_fetch_array($result)){
					echo "<tr><td>" . htmlentities($row["stock_name"]) . "</td>";
					echo "<td>" . htmlentities($row["quote"]) . "</td></tr>"
				}
				mysqli_free_result($result);
				mysqli_close($con);
			?>
		</table>
 		 
	</body>
</html>