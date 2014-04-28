<!DOCTYPE html>
 <html>
 <head>
 	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
 </head>
 <body>
 	<?php

	if (($handle = fopen("test.csv", "r")) !== FALSE) {

		$con = mysqli_connect("localhost", "", "");
				if (!$con) {
					exit('Connect Error (' . mysqli_connect_errno() . ')' . mysqli_connect_error());
				}
		mysqli_set_charset($con, 'utf-8');

		mysqli_select_db($con, "test_finance");//need to change database name

	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	    	if($data[0] = "sell"){
				$indQuery = "SELECT * FROM individual WHERE Name='data[1]'";
				$portQuery = "SELECT * FROM portfolio WHERE Name='data[1]'";
				if(mysqli_query($con, $indQuery){
					$stockQuery = "SELECT * FROM company WHERE Stock_name='data[2]'";
					$port2Query = "SELECT * FROM portfolio WHERE Name='data[2]'";
					if(mysqli_query($con, $stockQuery){
						//query current appreciation for company's stock
						//update individual's cash with investment multiplied by appreciation
					}else if(mysqli_query($con, $port2Query){
						//query current appreciation for portfolio
						//update individual's cash with investment multiplied by appreciation
					}
				}else if(mysqli_query($con), $portQuery){
					//query ALL the companies' stock appreciation that the portfolio was invested in.
				}
	    		//mysqli_query($con, "");

	    	} else if($data[0] = "buy"){
	    		$indQuery = "SELECT * FROM individual WHERE Name='data[1]'";
				$portQuery = "SELECT * FROM portfolio WHERE Name='data[1]'";
				if(mysqli_query($con, $indQuery){
				
					$currCash = "SELECT Cash FROM individual WHERE Name='data[1]'";
					if($currCash-'data[3]' < 0){
						//log error that individual does not have sufficient funds to invest
					}else{
						$newCash = $currCash-'data[3]';
					}
					//update how much cash that individual has
					mysqli_query($con, "UPDATE individual SET Cash='$newCash' WHERE Name='data[1]')";
						
					$stockQuery = "SELECT * FROM company WHERE Stock_name='data[2]'";
					$port2Query = "SELECT * FROM portfolio WHERE Name='data[2]'";
					if(mysqli_query($con, $stockQuery){
						//update the list of companies the individual is invested in
						
					}else if(mysqli_query($con, $port2Query){
						//update the list of portfolios the individual is invested in
					}
				}else if(mysqli_query($con), $portQuery){
					
				}
				//mysqli_query($con, "");

	    	} else if($data[0] = "sellbuy"){
	    		mysqli_query($con, "");

	    	} else if($data[0] =  "individual"){

	    		$sql = mysqli_query($con, "INSERT INTO individual (name,cash) VALUES ('$data[1]','$data[2]')");
	    		if (!mysqli_query($con,$sql)) {
				  die('Error: ' . mysqli_error($con));
				}

	    	} else if($data[0] = "fund"){
	    		$sql = mysqli_query($con, "INSERT INTO portfolio (name, cash) VALUES ('$data[1]', '$data[2]')");
	    		if (!mysqli_query($con,$sql)) {
				  die('Error: ' . mysqli_error($con));
				}
	    	}
	    }

	    mysqli_close($con);
	    echo "<p>finished reading csv file</p>";
	    fclose($handle);
	}
	?>
 </body>
 </html>