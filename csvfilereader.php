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
				$seller = $data[1];
				$gettingSold = $data[2];
				$date = $data[3];
				$dateSplit = explode("/", $date);
				$newDate = $dateSplit[2]."-".$dateSplit[0]."-".$dateSplit[1];
				
				$indQuery = mysqli_query($con, "SELECT * FROM individual WHERE Name='$seller'");
				$portQuery = mysqli_query($con, "SELECT * FROM portfolio WHERE Name='$seller'");
				
				$row1 = $indQuery->fetch_assoc();
				$row2 = $portQuery->fetch_assoc();
				//Check if seller is individual
				if(!empty($row1)){
					//$indRow = $indQuery->fetch_assoc();
					$indID = $row1['ID'];
					//echo "$indID<br>";
					$stockQuery = mysqli_query($con, "SELECT * FROM company WHERE Stock_name='$gettingSold'");
					$port2Query = mysqli_query($con, "SELECT * FROM portfolio WHERE Name='$gettingSold'");
					
					$stockRow = $stockQuery->fetch_assoc();
					$portRow = $portQuery->fetch_assoc();
					//Check if gettingSold is a company's stock
					if(!empty($stockRow)){
						//get number of stocks individual has in company
						//echo "GOT HERE<br>";
						$stockNumQuery = mysqli_query($con, "SELECT Num_stocks FROM individual_has_stocks WHERE Individual_ID='$indID'");
						$stockNumRow = $stockNumQuery->fetch_assoc();
						$numStocks = $stockNumRow['Num_stocks'];
						//echo "$numStocks<br>";
						//query current appreciation for company's stock
						$quoteQuery = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date='$newDate' AND Stock_name='$gettingSold'");
						$row = $quoteQuery->fetch_assoc();
						$quote = $row['Quote'];
						//echo "$quote<br>";
						//update individual's cash with investment multiplied by appreciation
						$moneyMade = $quote*(int)$numStocks;
						//echo "$moneyMade<br>";
						$currMoney = $row1['Cash'];
						//echo "$currMoney<br>";
						$totalMoney = $currMoney+$moneyMade;
						//echo "$totalMoney<br>";
						mysqli_query($con, "UPDATE individual SET Cash='$totalMoney' WHERE Name='$seller'"); //MIGHT NOT WORK
						//remove ind-comp relationship from individual_has_stocks table
						mysqli_query($con, "DELETE FROM individual_has_stocks WHERE Individual_ID='$indID' AND Stock_name='$gettingSold'");
					}else if(!empty($portRow)){
						//query current appreciation for portfolio
						//update individual's cash with investment multiplied by appreciation
					}
				//Check if seller is a portfolio
				}else if(!empty($row2)){
					//query ALL the companies' stock appreciation that the portfolio was invested in.
				}
	    		//mysqli_query($con, "");

	    	} else if($data[0] == "buy"){
				$buyer = $data[1];
				$beingBought = $data[2];
				$moneySpent = $data[3];
				$date = $data[4];
				
				$dateSplit = explode("/", $date);
				$newDate = $dateSplit[2]."-".$dateSplit[0]."-".$dateSplit[1];
				
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
						$quoteQuery = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date='$newDate' AND Stock_name='$beingBought'");
						$row = $quoteQuery->fetch_assoc();
						$quote = $row['Quote'];
						echo $quote;
		
						$stocks = (int)$moneySpent/$quote;
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
					//$dateSplit = explode("/", $date);
					//$newDate = $dateSplit[2]."-".$dateSplit[0]."-".$dateSplit[1];
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