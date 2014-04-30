<?php

	$database = "financedb";
	$table = "company";

	$conn = mysqli_connect("localhost", "user1", "pass1", $database);
	if (!$conn) {
		exit ('Connect Error (' . mysqli_connect_errno(). ')' . mysqli_connect_error());
	}

	$row = 1;
	if (($handle = fopen("Company1.csv", "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			
			$name = $data[1];
			$stock_name = $data[0];
			$sqlQuery = "insert into $table (name, stock_name) values (\"$name\", \"$stock_name\")";
			
			if (!mysqli_query($conn, $sqlQuery)) {
				die('Error: '. mysqli_error($conn));
			}
			
			$row++;
		}
		fclose($handle);
	}
?>