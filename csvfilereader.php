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
	    		mysqli_query($con, "");

	    	} else if($data[0] = "buy"){
	    		mysqli_query($con, "");

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