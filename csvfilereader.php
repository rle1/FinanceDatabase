<!DOCTYPE html>
 <html>
 <head>
 	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
 </head>
 <body>
 	<?php

	if (($handle = fopen("book2.csv", "r")) !== FALSE) {

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

				//Check if seller is a portfolio
				}else if(!empty($row2)){
					//query ALL the companies' stock appreciation that the portfolio was invested in.
					
						$portID = $row2['ID'];

						$stockQuery = mysqli_query($con, "SELECT * FROM company WHERE Name = '$gettingSold'");

						$stockRow = $stockQuery->fetch_assoc();
						if(!empty($stockRow)){
							//port id, stock_name, percent_invested(invested / how much they have at the time)
							$stockPercentageQuery = mysqli_query($con, "SELECT Percentage FROM portfolio_has_stocks WHERE Portfolio_ID='$portID'");
							$stockPercentageRow = $stockPercentageQuery->fetch_assoc();
							$percentage = $stockPercentageRow['percentage'];

							$currentQuote = mysqli_query($con, "SELECT Quote From quotes WHERE Date='$newDate' AND Stock_name='$gettingSold'");
							$row = $currentQuote->fetch_assoc();
							$sellQuote = $row['Quote'];

							$buyDate = mysqli_query($con, "SELECT Date FROM portfolio_act_stocks WHERE Portfolio_ID = '$portID' AND Stock_name='$gettingSold'");
							$dateRow = $buyDate->fetch_assoc();
							$date = $dateRow['Date'];

							$prevQuote = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date = 'date' AND Stock_name = '$gettingSold'");
							$row = $prevQuote->fetch_assoc();
							$buyQuote = $row['Quote'];

							//appreciation factor - sell/buy quote
							//appreciation factor * (percent invested into stock * totalCash)
							$totalCashQuery =mysqli_query($con, "SELECT TotalCash FROM portfolio WHERE Portfolio_ID='$portID'");
							$row = $totalCashQuery->fetch_assoc();
							$totalCash = $totalCashQuery['TotalCash'];

							$currentCashQuery = mysqli_query($con, "SELECT CurrentCash FROM portfolio WHERE Portfolio_ID='$portID'");
							$row = $currentCashQuery->fetch_assoc();
							$currentCash = $row['CurrentCash'];

							$returnCash = (double)$totalCash * (double)$percentage * ((double)$sellQuote/(double)$buyQuote);
							
							$totalCash = (double)$returnCash + ((double)$totalCash - ((double)$totalCash * (double)$percentage)); 
							
							$currentCash = $currentCash + $returnCash;

							mysqli_query($con, "UPDATE portfolio SET CurrentCash='$currentCash' WHERE Portfolio_ID='$portID'");
							mysqli_query($con, "UPDATE portfolio SET TotalCash='$totalCash' WHERE Portfolio_ID='$portID'");

							mysqli_query($con, "DELETE FROM portfolio_has_stocks WHERE Portfolio_ID='$portID' AND Stock_name='$gettingSold'");
						}
					}
				}

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
					echo "buyer is an individual<br>";
					$indID = $row1['ID'];
					$currCash = $row1['Cash'];
		
					$newCash = $currCash-(int)$moneySpent; //POSSIBLY error check for going below 0
					
					//update how much cash that individual has
					mysqli_query($con, "UPDATE individual SET Cash='$newCash' WHERE Name='$buyer'");
						
					$stockQuery = mysqli_query($con, "SELECT * FROM company WHERE Stock_name='$beingBought'");
					$port2Query = mysqli_query($con, "SELECT * FROM portfolio WHERE Name='$beingBought'");
					
					$stockRow = $stockQuery->fetch_assoc();
					$portRow = $port2Query->fetch_assoc();
					//See if buying company stock
					if(!empty($stockRow)){
						echo "Buying company stock<br>";
						//Find Num_stocks value
						$quoteQuery = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date='$newDate' AND Stock_name='$beingBought'");
						$row = $quoteQuery->fetch_assoc();
						$quote = $row['Quote'];
						echo "$quote<br>";
		
						$stocks = (int)$moneySpent/$quote;
						//update the list of companies the individual is invested in
						mysqli_query($con, "INSERT INTO individual_has_stocks (Individual_ID, Stock_name, Money_invested, Num_stocks) VALUES (\"$indID\", \"$beingBought\", \"$moneySpent\", \"$stocks\")");
						
						mysqli_query($con, "INSERT INTO individual_act_stocks (Individaul_ID, Stock_name, Buy_or_sell, Date) VALUES (\"$indID\", \"beingBought\", \"B\", \"$newDate\")");
					//See if buying portfolio share
					}else if(!empty($portRow)){
						echo "Buying share in portfolio<br>";
						//Get portfolio ID
						$portID = $portRow['ID'];
						//update the list of portfolios the individual is invested in
						mysqli_query($con, "INSERT INTO individual_has_portfolios (Individual_ID, Portfolio_ID, Money_invested) VALUES (\"$indID\", \"$portID\", \"$moneySpent\")");
						echo "$indID has invested $moneySpent in $beingBought<br>";
						
						//Updating the portfolio's total/current cash after individual has invested
						$portTotalMoney = $portRow['Total_cash'];
						echo "$portID 's current total cash is $portTotalMoney<br>";
						$newPortTMoney = $portTotalMoney+(double)$moneySpent;
						echo "$portID 's new total cash is $newPortTMoney<br>";
						$portCurrMoney = $portRow['Curr_cash'];
						echo "$portID 's current current cash is $portCurrMoney<br>";
						$newPortCMoney = (double)$portCurrMoney+(double)$moneySpent;
						echo "$portID 's new curr cash is $newPortCMoney<br>";
						mysqli_query($con, "UPDATE portfolio SET Total_cash='$newPortTMoney', Curr_cash='$newPortCMoney' WHERE ID='$portID'");
						
						mysqli_query($con, "INSERT INTO individual_act_portfolios (Individual_ID, Portfolio_ID, Buy_or_sell, Date) VALUES (\"$indID\", \"$portID\", \"B\", \"$newDate\")");
						
					}
				}else if(!empty($row2)){
					$portID = $row2['ID'];
					$currCash = $row2['Curr_cash'];
					echo "portfolio's current cash is $currCash<br>";
					$newCash = $currCash-(double)$moneySpent;
					echo "portfolio's new cash is $newCash<br>";
					//update how much cash that individual has
					mysqli_query($con, "UPDATE portfolio SET Curr_cash='$newCash' WHERE Name='$buyer'");
					
					//update the list of companies the individual is invested in
					$totalCash = $row2['Total_cash'];
					$percent = (double)$moneySpent/$totalCash;
					echo "$portID has a $percent investment in $beingBought<br>";
					mysqli_query($con, "INSERT INTO portfolio_has_stocks (Portfolio_ID, Stock_name, percent_invested) VALUES (\"$portID\", \"$beingBought\", \"$percent\")");
					
					mysqli_query($con, "INSERT INTO portfolio_act_stocks (Portfolio_ID, Stock_name, Buy_or_sell, Date) VALUES (\"$portID\", \"$beingBought\", \"B\", \"$newDate\")");
						
				}

	    	} else if($data[0] == "sellbuy"){
	    		//mysqli_query($con, "");

	    	} else if($data[0] ==  "individual"){
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
				
				$insertQuery = "INSERT INTO portfolio (Name, Total_cash, Curr_cash) VALUES (\"$portName\", \"$portCash\", \"$portCash\")";
				
				if (!mysqli_query($con, $insertQuery)) {
						die('Error: '. mysqli_error($conn));
				}
				
				echo "added $portName to portfolio<br>";
			}
	    }

	    mysqli_close($con);
	    echo "<p>finished reading csv file</p>";
	    fclose($handle);
	}
	?>
 </body>
 </html>