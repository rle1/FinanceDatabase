<?php

	$database = "financedb";
	$table = "quotes";

	$conn = mysqli_connect("localhost", "user1", "pass1", $database);
	if (!$conn) {
		exit ('Connect Error (' . mysqli_connect_errno(). ')' . mysqli_connect_error());
	}
	
	foreach (glob("Stocks/*.csv") as $file) {
	
		$row = 1;
		if (($handle = fopen($file, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				
				if ($row !== 1) {
				
					$date = $data[0];
					$stock_name = str_replace(".csv", "", substr($file, 7));
					$quote = $data[6];
					$day_hi = $data[2];
					$day_lo = $data[3];
					$volume = $data[5];
					
					$sqlQuery = "insert into $table (Date, Stock_name, Quote, Day_hi, Day_lo, Volume) values (\"$date\", \"$stock_name\", \"$quote\", \"$day_hi\", \"$day_lo\", \"$volume\")";
					
					if (!mysqli_query($conn, $sqlQuery)) {
						die('Error: '. mysqli_error($conn));
					}
				}
				$row++;
			}
			fclose($handle);
			
			echo "$file processed<br>";
		}
	}
?>