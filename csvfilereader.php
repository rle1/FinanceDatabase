<!DOCTYPE html>
 <html>
 <head>
 	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
 </head>
 <body>
 	<?php

	if (($handle = fopen("book1.csv", "r")) !== FALSE) {

		$con = mysqli_connect("localhost", "user1", "pass1");
				if (!$con) {
					exit('Connect Error (' . mysqli_connect_errno() . ')' . mysqli_connect_error());
				}
		mysqli_set_charset($con, 'utf-8');

		mysqli_select_db($con, "finance");//need to change database name

	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	    	if($data[0] == "sell"){
				/*$indQuery = "SELECT * FROM individual WHERE Name='data[1]'";
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
	    		//mysqli_query($con, "");*/

	    	} else if($data[0] == "buy"){
				$buyer = $data[1];
				$beingBought = $data[2];
				$moneySpent = $data[3];
				$date = $data[4];
	    		$indQuery = mysqli_query($con, "SELECT * FROM individual WHERE Name='$buyer'");
				$portQuery = mysqli_query($con, "SELECT * FROM portfolio WHERE Name='$buyer'");
				
				$row1 = $indQuery->fetch_assoc();
				$row2 = $portQuery->fetch_assoc();
				//Check if buyer is individual
				if(!empty($row1)){
				
					//Save individual's ID for storing
					$indIDQuery = mysqli_query($con, "SELECT ID FROM individual WHERE Name='$buyer'");
					$row = $indIDQuery->fetch_assoc();
					$indID = $row['ID'];
					//Find individual's current cash amount
					$query = mysqli_query($con, "SELECT Cash FROM individual WHERE Name='$buyer'");
					$row = $query->fetch_assoc();
					$currCash = $row['Cash'];
			
					//if($currCash-'data[3]' < 0){
						//log error that individual does not have sufficient funds to invest
					//}else{
						$newCash = $currCash-(int)$data[3];
					//}
					//update how much cash that individual has
					mysqli_query($con, "UPDATE individual SET Cash='$newCash' WHERE Name='$buyer'");
						
					$stockQuery = mysqli_query($con, "SELECT Stock_name FROM company WHERE Stock_name='$beingBought'");
					$port2Query = mysqli_query($con, "SELECT ID FROM portfolio WHERE Name='$beingBought'");
					
					$row1 = $stockQuery->fetch_assoc();
					$row2 = $port2Query->fetch_assoc();
					//See if buying company stock
					if(!empty($row1)){
						
						//Find Num_stocks value
						$quoteQuery = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date='$date' AND Stock_name='$beingBought'");
						$row = $quoteQuery->fetch_assoc();
						$quote = $row['Quote'];
						echo $quote;
		
						$stocks = (int)$data[3]/$quote;
						//update the list of companies the individual is invested in
						mysqli_query($con, "INSERT INTO individual_has_stocks (Individual_ID, Stock_name, Money_invested, Num_stocks) VALUES (\"$indID\", \"$data[2]\", \"$data[3]\", \"$stocks\")");
						
					//See if buying portfolio share
					}else if(!empty($row2)){
						//Get portfolio ID
						$portIDQuery= mysqli_query($con, "SELECT ID FROM portfolio WHERE Name='$beingBought'");
						$row = $portIDQuery->fetch_assoc();
						$portID = $row['ID'];
						//update the list of portfolios the individual is invested in
						mysqli_query($con, "INSERT INTO  individual_has_portfolios (Individual_ID, Portfolio_ID, Money_invested) VALUES (\"$indID\", \"$portID\", \"$moneySpent\")");
					}
				}else if(!empty($row2)){
					//Save portfolio's ID for storing
					$portIDQuery = mysqli_query($con, "SELECT ID FROM portfolio WHERE Name='$buyer'");
					$row = $portIDQuery->fetch_assoc();
					$portID = $row['ID'];
					
					//Find portfolio's current cash amount
					$query = mysqli_query($con, "SELECT Cash FROM portfolio WHERE Name='$buyer'");
					$row = $query->fetch_assoc();
					$currCash = $row['Cash'];
					
					$newCash = $currCash-(int)$data[3];
					
					//update how much cash that individual has
					mysqli_query($con, "UPDATE portfolio SET Cash='$newCash' WHERE Name='$buyer'");
					
					//Find Num_stocks value
					//echo $date;
					//echo $beingBought;
					$dateSplit = explode("/", $date);
					$newDate = $dateSplit[2]."-".$dateSplit[0]."-".$dateSplit[1];
					//echo $newDate;
					$quoteQuery = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date='$newDate' AND Stock_name='$beingBought'");
					$row = $quoteQuery->fetch_assoc();
					$quote = $row['Quote'];
					//echo $quote;
		
					$stocks = (int)$data[3]/$quote;
					
					//update the list of companies the individual is invested in
					mysqli_query($con, "INSERT INTO portfolio_has_stocks (Portfolio_ID, Stock_name, Money_invested, Num_stocks) VALUES (\"$portID\", \"$data[2]\", \"$data[3]\", \"$stocks\")");
						
				}
				//mysqli_query($con, "");

	    	} else if($data[0] == "sellbuy"){
	    		//mysqli_query($con, "");

	    	} /*else if($data[0] ==  "individual"){
				$indName = $data[1];
				$indCash = $data[2];
				
				$insertQuery = "INSERT INTO individual (Name, Cash) VALUES (\"$indName\", \"$indCash\")";
				
				if (!mysqli_query($con, $insertQuery)) {
						die('Error: '. mysqli_error($conn));
				}
				
				echo "added $indName to individual<br>";
	    	} else if($data[0] == "fund"){
	    		$portName = $data[1];
				$portCash = $data[2];
				
				$insertQuery = "INSERT INTO portfolio (Name, Cash) VALUES (\"$portName\", \"$portCash\")";
				
				if (!mysqli_query($con, $insertQuery)) {
						die('Error: '. mysqli_error($conn));
				}
				
				echo "added $portName to portfolio<br>";
			}*/
	    }

	    mysqli_close($con);
	    echo "<p>finished reading csv file</p>";
	    fclose($handle);
	}
	?>
 </body>
 </html>