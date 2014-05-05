<!DOCTYPE html>
 <html>
 <head>
 	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
 </head>
 <body>
 	<?php

	if (($handle = fopen("script4.csv", "r")) !== FALSE) {

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
				$newDate = $data[3];
				//$dateSplit = explode("/", $date);
				//$newDate = $dateSplit[2]."-".$dateSplit[0]."-".$dateSplit[1];
				
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
					$portRow = $port2Query->fetch_assoc();
					
					//$portName = $portRow['Name'];
					//echo "$portName<br>";
					
					//Check if gettingSold is a company's stock
					if(!empty($stockRow)){
						//get number of stocks individual has in company
						$stockNumQuery = mysqli_query($con, "SELECT Num_stocks FROM individual_has_stocks WHERE Individual_ID='$indID'");
						$stockNumRow = $stockNumQuery->fetch_assoc();
						$numStocks = $stockNumRow['Num_stocks'];
						if(empty($stockNumRow) || $numStocks <= 0){
							continue;
						}
						
						//query current appreciation for company's stock
						$quoteQuery = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date='$newDate' AND Stock_name='$gettingSold'");
						$row = $quoteQuery->fetch_assoc();
						$quote = $row['Quote'];
						if($quote == 0){
							continue;
						}
						
						//update individual's cash with investment multiplied by appreciation
						$moneyMade = $quote*(int)$numStocks;
						$currMoney = $row1['Cash'];
						$totalMoney = $currMoney+$moneyMade;

						if($totalMoney < 0){
							continue;
						}
						
						mysqli_query($con, "UPDATE individual SET Cash='$totalMoney' WHERE Name='$seller'");
						
						//remove ind-comp relationship from individual_has_stocks table
						mysqli_query($con, "DELETE FROM individual_has_stocks WHERE Individual_ID='$indID' AND Stock_name='$gettingSold'");
						
						mysqli_query($con, "INSERT INTO individual_act_stocks (Individual_ID, Stock_name, Buy_or_sell, Date) VALUES (\"$indID\", \"$gettingSold\", \"S\", \"$newDate\")");
						
					}else if(!empty($portRow)){
						
						//query current appreciation for portfolio
						//update individual's cash with investment multiplied by appreciation
						$portID = $portRow['ID'];
						$indPort = mysqli_query($con, "SELECT * FROM individual_has_portfolios WHERE Individual_ID='$indID' AND Portfolio_ID='$portID'");
						$indPortRow = $indPort->fetch_assoc();
						//grab all investments made by the portfolio
						if(!empty($indPortRow)){
							$investmentsQuery = mysqli_query($con, "SELECT Stock_name, percent_invested FROM portfolio_has_stocks WHERE Portfolio_ID='$portID'");

							$totalCashQuery = mysqli_query($con, "SELECT * FROM portfolio WHERE ID='$portID'");
							$row = $totalCashQuery->fetch_assoc();
							$totalCash = $row['Total_cash'];
							$currCash = $row['Curr_cash'];


							$totalFundValue = 0.0;
							$totalPortInvestment = 0.0;

							while($row = mysqli_fetch_array($investmentsQuery)){
								$stock = $row['Stock_name'];
								$percentage = $row['percent_invested'];


								$currentQuote = mysqli_query($con, "SELECT Quote From quotes WHERE Date='$newDate' AND Stock_name='$stock'");
								$row = $currentQuote->fetch_assoc();
								$sellQuote = $row['Quote'];
								if(empty($row) || $sellQuote <=0){
									continue;
								}

								$buyDateQuery = mysqli_query($con, "SELECT Date FROM portfolio_act_stocks WHERE Portfolio_ID = '$portID' AND Stock_name='$stock' AND Buy_or_sell='B'");
								$dateRow = $buyDateQuery->fetch_assoc();
								$buyDate = $dateRow['Date'];

								$prevQuote = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date = '$buyDate' AND Stock_name = '$stock'");
								$row = $prevQuote->fetch_assoc();
								$buyQuote = $row['Quote'];
								
								$appFactor = $sellQuote / $buyQuote;

								$totalPortInvestment += $percentage;

								$moneyInvested = $totalCash * $percentage;

								$totalFundValue = $totalFundValue + ($moneyInvested * $appFactor);
							}

							$totalFundValue = $totalFundValue + ($totalCash * (1.0 - $totalPortInvestment));

							$indInvestPortQuery = mysqli_query($con, "SELECT Money_invested FROM individual_has_portfolios WHERE Individual_ID='$indID' AND Portfolio_ID='$portID'");
							$row = $indInvestPortQuery->fetch_assoc();
							$indInvestPort = $row['Money_invested'];

							$indInvestmentPercentage = $indInvestPort / $totalCash;

							$returnCash = $indInvestmentPercentage * $totalFundValue; 
							
							$indCashQuery = mysqli_query($con, "SELECT Cash FROM individual WHERE ID='$indID'");
							$row = $indCashQuery->fetch_assoc();
							$indCash = $row['Cash'];

							$finalCash = $indCash + $returnCash;
							mysqli_query($con, "UPDATE individual SET Cash='$finalCash' WHERE ID='$indID'");
							mysqli_query($con, "DELETE FROM individual_has_portfolios WHERE Individual_ID='$indID' AND Portfolio_ID='$portID'");
							
							$newTotalCash = $totalCash - $indInvestPort;
							$newCurrCash = $currCash - $indInvestPort;
							
							mysqli_query($con, "UPDATE portfolio SET Total_cash='$newTotalCash', Curr_cash='$newCurrCash' WHERE ID='$portID'");
							mysqli_query($con, "INSERT INTO individual_act_portfolios (Individual_ID, Portfolio_ID, Buy_or_sell, Date) VALUES (\"$indID\", \"$portID\", \"S\", \"$newDate\")");
							
						}
					}
				
				//Check if seller is a portfolio
				}else if(!empty($row2)){
					//query ALL the companies' stock appreciation that the portfolio was invested in.

					$portID = $row2['ID'];
		
					//find percent portfolio invested into company
					$stockPercentageQuery = mysqli_query($con, "SELECT percent_invested FROM portfolio_has_stocks WHERE Portfolio_ID='$portID' AND Stock_name='$gettingSold'");
					$stockPercentageRow = $stockPercentageQuery->fetch_assoc();
					$percentInvested = $stockPercentageRow['percent_invested'];

					//find quote at time of selling
					$currentQuote = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date='$newDate' AND Stock_name='$gettingSold'");
					$row = $currentQuote->fetch_assoc();
					$sellQuote = $row['Quote'];
						/************************************************************************************************
						not sure if we should check if buyQuote or sellQuote is zero
					**************************************************************************************************/
					if($sellQuote == 0){
						echo "no quote exists<br>";
						continue;
					}

					//find date portfolio bought stock
					$buyDateQuery = mysqli_query($con, "SELECT Date FROM portfolio_act_stocks WHERE Portfolio_ID = '$portID' AND Stock_name='$gettingSold' AND Buy_or_sell='B'");
					$dateRow = $buyDateQuery->fetch_assoc();
					$buyDate = $dateRow['Date'];

					//find quote on date portfolio bought stock
					$prevQuote = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date = '$buyDate' AND Stock_name = '$gettingSold'");
					$row = $prevQuote->fetch_assoc();
					$buyQuote = $row['Quote'];	
						/************************************************************************************************
						not sure if we should check if buyQuote or sellQuote is zero
					**************************************************************************************************/
					if(empty($row) || $buyQuote <= 0){
						continue;
					}

					//appreciation factor - sell/buy quote
					$appFactor = $sellQuote/$buyQuote;
					
					$totalCash = $row2['Total_cash'];
					$currentCash = $row2['Curr_cash'];
					
					$moneyInvested = $totalCash*$percentInvested;

					$returnCash = ($moneyInvested)*$appFactor;

					$totalCash = ($totalCash - ($returnCash/$appFactor)) + $returnCash; 

					$currentCash = $currentCash + $returnCash;

					echo "$seller (ID: $portID ) sold $gettingSold stock at $sellQuote a stock . Bought $moneyInvested dollars with stock at $buyQuote a stock . Stock grew by a factor of $appFactor .<br>";
					echo "Received $returnCash dollars. Made total value of $seller $totalCash dollars and spending cash is $currentCash dollars.<br><br><br>";
					mysqli_query($con, "UPDATE portfolio SET Total_cash='$totalCash', Curr_cash='$currentCash' WHERE ID='$portID'");

					mysqli_query($con, "DELETE FROM portfolio_has_stocks WHERE Portfolio_ID='$portID' AND Stock_name='$gettingSold'");
						
					mysqli_query($con, "INSERT INTO portfolio_act_stocks (Portfolio_ID, Stock_name, Buy_or_sell, Date) VALUES (\"$portID\", \"$gettingSold\", \"S\", \"$newDate\")");
				}

			} else if($data[0] == "buy"){
				
				$buyer = $data[1];
				$beingBought = $data[2];
				$moneySpent = $data[3];
				$newDate = $data[4];
				
				//$dateSplit = explode("-", $date);
				//echo $date;
				//$newDate = $dateSplit[2]."-".$dateSplit[0]."-".$dateSplit[1];
				
				
				
	    		$indQuery = mysqli_query($con, "SELECT * FROM individual WHERE Name='$buyer'");
				$portQuery = mysqli_query($con, "SELECT * FROM portfolio WHERE Name='$buyer'");
				
				$row1 = $indQuery->fetch_assoc();
				$row2 = $portQuery->fetch_assoc();
				//Check if buyer is individual
				if(!empty($row1)){
					$indID = $row1['ID'];
					$currCash = $row1['Cash'];
		
					$newCash = $currCash-(int)$moneySpent; 

					if($newCash <= 0){
						continue;
					}
					
					
					//update how much cash that individual has
					echo "updating how much cash $buyer ($indID) has to $newCash<br>";
					mysqli_query($con, "UPDATE individual SET Cash='$newCash' WHERE ID='$indID'");
						
					$stockQuery = mysqli_query($con, "SELECT * FROM company WHERE Stock_name='$beingBought'");
					$port2Query = mysqli_query($con, "SELECT * FROM portfolio WHERE Name='$beingBought'");
					
					$stockRow = $stockQuery->fetch_assoc();
					$portRow = $port2Query->fetch_assoc();
					
					//See if buying company stock
					if(!empty($stockRow)){
						
						//Find Num_stocks value
						$quoteQuery = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date='$newDate' AND Stock_name='$beingBought'");
						$row = $quoteQuery->fetch_assoc();
						$quote = $row['Quote'];
						if($quote == 0 || empty($row)){
							continue;
						}
		
						$stocks = (int)$moneySpent/$quote;
						
						//update the list of companies the individual is invested in
						mysqli_query($con, "INSERT INTO individual_has_stocks (Individual_ID, Stock_name, Money_invested, Num_stocks) VALUES (\"$indID\", \"$beingBought\", \"$moneySpent\", \"$stocks\")");
						echo "$buyer bought $moneySpent worth of $beingBought stock. Recieved $stocks stocks.<br>";
						mysqli_query($con, "INSERT INTO individual_act_stocks (Individual_ID, Stock_name, Buy_or_sell, Date) VALUES (\"$indID\", \"$beingBought\", \"B\", \"$newDate\")");
					
					//See if buying portfolio share
					}else if(!empty($portRow)){
						//Get portfolio ID
						$portID = $portRow['ID'];
						//update the list of portfolios the individual is invested in
						echo "adding relationship between $buyer ($indID) and $beingBought ($portID). Invested $moneySpent<br>";
						mysqli_query($con, "INSERT INTO individual_has_portfolios (Individual_ID, Portfolio_ID, Money_invested) VALUES (\"$indID\", \"$portID\", \"$moneySpent\")");
						
						//Updating the portfolio's total/current cash after individual has invested
						$portTotalMoney = $portRow['Total_cash'];
						$newPortTMoney = $portTotalMoney+(double)$moneySpent;
						$portCurrMoney = $portRow['Curr_cash'];
						$newPortCMoney = (double)$portCurrMoney+(double)$moneySpent;
						
						echo "updating $beingBought ($portID) total value to $newPortTMoney and current cash amount to $newPortCMoney<br>";
						mysqli_query($con, "UPDATE portfolio SET Total_cash='$newPortTMoney', Curr_cash='$newPortCMoney' WHERE ID='$portID'");
						echo "logging $buyer ($indID) buying $beingBought ($portID)";
						mysqli_query($con, "INSERT INTO individual_act_portfolios (Individual_ID, Portfolio_ID, Buy_or_sell, Date) VALUES (\"$indID\", \"$portID\", \"B\", \"$newDate\")");	
					}
				}else if(!empty($row2)){
					$portID = $row2['ID'];
					$currCash = $row2['Curr_cash'];
					$newCash = $currCash-(double)$moneySpent;
					
					//update how much cash that portfolio has
					echo "update $buyer 's ($portID) current cash amount to $newCash<br>";
					mysqli_query($con, "UPDATE portfolio SET Curr_cash='$newCash' WHERE ID='$portID'");
					
					//update the list of companies the portfolio is invested in
					$totalCash = $row2['Total_cash'];
					$percent = (double)$moneySpent/$totalCash;
					
					echo "add new relationship between $buyer ($portID) and $beingBought. $buyer has $percent invested in $beingBought<br>";
					mysqli_query($con, "INSERT INTO portfolio_has_stocks (Portfolio_ID, Stock_name, percent_invested) VALUES (\"$portID\", \"$beingBought\", \"$percent\")");
					echo "logging $buyer ($portID) buying $beingBought<br>";
					mysqli_query($con, "INSERT INTO portfolio_act_stocks (Portfolio_ID, Stock_name, Buy_or_sell, Date) VALUES (\"$portID\", \"$beingBought\", \"B\", \"$newDate\")");
						
				}

	    	} else if($data[0] == "sellbuy"){
				
				$seller = $data[1];
				$selling = $data[2];
				$buying = $data[3];
				$newDate = $data[4];
				
				//$dateSplit = explode("/", $date);
				//$newDate = $dateSplit[2]."-".$dateSplit[0]."-".$dateSplit[1];
				
				$indQueryCheck = mysqli_query($con, "SELECT * FROM individual WHERE Name='$seller'");
				$portQueryCheck = mysqli_query($con, "SELECT * FROM portfolio WHERE Name='$seller'");

				$indRowCheck = $indQueryCheck->fetch_assoc();
				$portRowCheck = $portQueryCheck->fetch_assoc();
			
				if(!empty($indRowCheck)){
					$indID = $indRowCheck['ID'];
					
					$sellingStockQuery = mysqli_query($con, "SELECT * FROM company WHERE Stock_name='$selling'");
					$sellingPortQuery = mysqli_query($con, "SELECT * FROM portfolio WHERE Name='$selling'");
					
					$sellingStockRow = $sellingStockQuery->fetch_assoc();
					$sellingPortRow = $sellingPortQuery->fetch_assoc();
					
					if(!empty($sellingStockRow)){ 
						//get number of stocks individual has in company
						$stockNumQuery = mysqli_query($con, "SELECT Num_stocks FROM individual_has_stocks WHERE Individual_ID='$indID'");
						$stockNumRow = $stockNumQuery->fetch_assoc();
						$numStocks = $stockNumRow['Num_stocks'];

						if($numStocks <= 0 || empty($stockNumRow)){
							continue;
						}
						
						//query current appreciation for company's stock
						$quoteQuery = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date='$newDate' AND Stock_name='$selling'");
						$row = $quoteQuery->fetch_assoc();
						$quote = $row['Quote'];
							/************************************************************************************************
						not sure if we should check if buyQuote or sellQuote is zero
					**************************************************************************************************/
						if($quote == 0 || empty($row)){
							continue;
						}
						
						//query info for company buying into
						$buyingQuery = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date='$newDate' AND Stock_name='$buying'");
						$buyingRow = $buyingQuery->fetch_assoc();
						$buyingQuote = $buyingRow['Quote'];
							/************************************************************************************************
						not sure if we should check if buyQuote or sellQuote is zero
					**************************************************************************************************/
						if(empty($buyingQuote) || $buying <=0){
							continue;
						}

						
						$moneyMade = $quote*(int)$numStocks;
						$newStockNum = $moneyMade/$buyingQuote;
						
						$buyingStockQuery = mysqli_query($con, "SELECT * FROM company WHERE Stock_name='$buying'");
						$buyingPortQuery = mysqli_query($con, "SELECT * FROM portfolio WHERE Name='$buying'");
						
						$buyingStockRow = $buyingStockQuery->fetch_assoc();
						$buyingPortRow = $buyingPortQuery->fetch_assoc();
						
						if(!empty($buyingStockRow)){
						echo "adding new relationship between $seller ($indID) and $buying. Invested $moneyMade dollars and bought $newStockNum stocks<br>";
						mysqli_query($con, "INSERT INTO individual_has_stocks (Individual_ID, Stock_name, Money_invested, Num_stocks) VALUES (\"$indID\", \"$buying\", \"$moneyMade\", \"$newStockNum\")");
						echo"logging $seller ($indID) buying $buying<br>";
						mysqli_query($con, "INSERT INTO individual_act_stocks (Individual_ID, Stock_name, Buy_or_sell, Date) VALUES (\"$indID\", \"$buying\", \"B\", \"$newDate\")");
						}else if(!empty($buyingPortRow)){
							$buyingPortID = $buyingPortRow['ID'];
							$buyingPortTC = $buyingPortRow['Total_cash'];
							$buyingPortCC = $buyingPortRow['Curr_cash'];
							$newTC = $buyingPortTC+$moneyMade;
							$newCC = $buyingPortCC+$moneyMade;
							
							echo "adding new relationship between $seller ($indID) and $buying ($buyingPortID). $seller invested $moneyMade<br>";
							mysqli_query($con, "INSERT INTO individual_has_portfolios (Individual_ID, Portfolio_ID, Money_invested) VALUES (\"$indID\", \"$buyingPortID\", \"$moneyMade\")");
							echo "updating $buying 's ($buyingPortID) total value to $newTC and current cash amount to $newCC.<br>";
							mysqli_query($con, "UPDATE portfolio SET Total_cash='$newTC', Curr_cash='$newCC' WHERE ID='$buyingPortID'");
							echo "logging $seller ($indID) buying $buying ($buyingPortID)<br>";
							mysqli_query($con, "INSERT INTO individual_act_portfolios (Individual_ID, Portfolio_ID, Buy_or_sell, Date) VALUES (\"$indID\", \"$buyingPortID\", \"B\", \"$newDate\")");
						}
						echo "logging $seller ($indID) selling $selling<br>";
						mysqli_query($con, "INSERT INTO individual_act_stocks (Individual_ID, Stock_name, Buy_or_sell, Date) VALUES (\"$indID\", \"$selling\", \"S\", \"$newDate\")");
						echo "removing old relationship between $seller ($indID) and $selling<br>";
						mysqli_query($con, "DELETE FROM individual_has_stocks WHERE Individual_ID='$indID' AND Stock_name='$selling'");

						
					}else if(!empty($sellingPortRow)){
					
						//update individual's cash with investment multiplied by appreciation
						$portID = $sellingPortRow['ID'];
						$indPort = mysqli_query($con, "SELECT * FROM individual_has_portfolios WHERE Individual_ID='$indID' AND Portfolio_ID='$portID'");
						$indPortRow = $indPort->fetch_assoc();
						//grab all investments made by the portfolio
						
							$investmentsQuery = mysqli_query($con, "SELECT Stock_name, percent_invested FROM portfolio_has_stocks WHERE Portfolio_ID='$portID'");

							$totalCashQuery = mysqli_query($con, "SELECT * FROM portfolio WHERE ID='$portID'");
							$row = $totalCashQuery->fetch_assoc();
							$totalCash = $row['Total_cash'];
							$currCash = $row['Curr_cash'];
							
							$totalFundValue = 0.0;
							$totalPortInvestment = 0.0;

							while($row = mysqli_fetch_array($investmentsQuery)){
								$stock = $row['Stock_name'];
								$percentage = $row['percent_invested'];

								$currentQuote = mysqli_query($con, "SELECT Quote From quotes WHERE Date='$newDate' AND Stock_name='$stock'");
								$row = $currentQuote->fetch_assoc();
								$sellQuote = $row['Quote'];
									/************************************************************************************************
						not sure if we should check if buyQuote or sellQuote is zero
					**************************************************************************************************/
								if(empty($row) || $sellQuote <=0){
									continue;
								}

								$buyDateQuery = mysqli_query($con, "SELECT Date FROM portfolio_act_stocks WHERE Portfolio_ID = '$portID' AND Stock_name='$stock' AND Buy_or_sell='B'");
								$dateRow = $buyDateQuery->fetch_assoc();
								$buyDate = $dateRow['Date'];

								$prevQuote = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date = '$buyDate' AND Stock_name = '$stock'");
								$row = $prevQuote->fetch_assoc();
								$buyQuote = $row['Quote'];
									/************************************************************************************************
						not sure if we should check if buyQuote or sellQuote is zero
					**************************************************************************************************/
								if(empty($row) || $buyQuote <= 0){
									continue;
								}
								

								$appFactor = $sellQuote / $buyQuote;

								$totalPortInvestment += $percentage;

								$moneyInvested = $totalCash * $percentage;

								$totalFundValue = $totalFundValue + ($moneyInvested * $appFactor);
							
							}
							$totalFundValue = $totalFundValue + ($totalCash * (1.0 - $totalPortInvestment));

							$indInvestPortQuery = mysqli_query($con, "SELECT Money_invested FROM individual_has_portfolios WHERE Individual_ID='$indID' AND Portfolio_ID='$portID'");
							$row = $indInvestPortQuery->fetch_assoc();
							$indInvestPort = $row['Money_invested'];

							$indInvestmentPercentage = $indInvestPort / $totalCash;

							$returnCash = $indInvestmentPercentage * $totalFundValue; 
							
							$indCashQuery = mysqli_query($con, "SELECT Cash FROM individual WHERE ID='$indID'");
							$row = $indCashQuery->fetch_assoc();
							$indCash = $row['Cash'];

							$finalCash = $indCash + $returnCash;
						
						$buyingStockQuery = mysqli_query($con, "SELECT * FROM company WHERE Stock_name='$buying'");
						$buyingPortQuery = mysqli_query($con, "SELECT * FROM portfolio WHERE Name='$buying'");
						
						$buyingStockRow = $buyingStockQuery->fetch_assoc();
						$buyingPortRow = $buyingPortQuery->fetch_assoc();
						
						
						if(!empty($buyingStockRow)){
							$buyingQuoteQuery = mysqli_query($con, "SELECT Quote FROM quotes WHERE Stock_name='$buying' AND Date='$newDate'");
							$buyingQuoteRow = $buyingQuoteQuery->fetch_assoc();
							$buyingQuote = $buyingQuoteRow['Quote'];
							$boughtStockNum = $returnCash/$buyingQuote;
							
							echo "adding new relationship between $seller ($indID) and $buying. Invested $returnCash dollars and bought $boughtStockNum stocks<br>";
							mysqli_query($con, "INSERT INTO individual_has_stocks (Individual_ID, Stock_name, Money_invested, Num_stocks) VALUES (\"$indID\", \"$buying\", \"$returnCash\", \"$boughtStockNum\")");
							echo"logging $seller ($indID) buying $buying<br>";
							mysqli_query($con, "INSERT INTO individual_act_stocks (Individual_ID, Stock_name, Buy_or_sell, Date) VALUES (\"$indID\", \"$buying\", \"B\", \"$newDate\")");
						}else if(!empty($buyingPortRow)){
							$buyingPortID = $buyingPortRow['ID'];
							$TC = $buyingPortRow['Total_cash'];
							$CC = $buyingPortRow['Curr_cash'];
							$newTC = $TC+$returnCash;
							$newCC = $CC+$returnCash;
							
							echo "adding new realtionship between $seller ($indID) and $buying ($buyingPortID). $seller invested $returnCash<br>";
							mysqli_query($con, "INSERT INTO individual_has_portfolios (Individual_ID, Portfolio_ID, Money_invested) VALUES (\"$indID\", \"$buyingPortID\", \"returnCash\")");
							echo "updating $buying 's ($buyingPortID) total value to $newTC and current cash amount to $newCC.<br>";
							mysqli_query($con, "UPDATE portfolio SET Total_cash='$newTC' AND Curr_cash='$newCC' WHERE ID='$buyingPortID'");
							echo "logging $seller ($indID) buying $buying ($buyingPortID)<br>";
							mysqli_query($con, "INSERT INTO individual_act_portfolios (Individual_ID, Portfolio_ID, Buy_or_sell, Date) VALUES (\"$indID\", \"$buyingPortID\", \"B\", \"$newDate\")");
						}
						echo "logging $seller ($indID) selling $selling<br>";
						mysqli_query($con, "INSERT INTO individual_act_portfolios (Individual_ID, Portfolio_ID, Buy_or_sell, Date) VALUES (\"$indID\", \"$portID\", \"S\", \"$newDate\")");
						echo "removing old relationship between $seller ($indID) and $selling ($portID)<br>";
						mysqli_query($con, "DELETE FROM individual_has_portfolios WHERE Individual_ID='$indID' AND Portfolio_ID='$portID'");
					}
				}else if(!empty($portRowCheck)){
					$portID = $portRowCheck['ID'];
					
					//find percent portfolio invested into company
					$stockPercentageQuery = mysqli_query($con, "SELECT percent_invested FROM portfolio_has_stocks WHERE Portfolio_ID='$portID' AND Stock_name='$selling'");
					$stockPercentageRow = $stockPercentageQuery->fetch_assoc();
					$percentInvested = $stockPercentageRow['percent_invested'];

					if($percentInvested <= 0 || empty($stockPercentageRow)){
						continue;
					}
					
					//find quote at time of selling
					$currentQuote = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date='$newDate' AND Stock_name='$selling'");
					$row = $currentQuote->fetch_assoc();
					$sellQuote = $row['Quote'];
					/************************************************************************************************
						not sure if we should check if buyQuote or sellQuote is zero
					**************************************************************************************************/
					if(empty($row) || $sellQuote <=0){
						continue;
					}
					
					//find date portfolio bought stock
					$buyDateQuery = mysqli_query($con, "SELECT Date FROM portfolio_act_stocks WHERE Portfolio_ID = '$portID' AND Stock_name='$selling' AND Buy_or_sell='B'");
					$dateRow = $buyDateQuery->fetch_assoc();
					$buyDate = $dateRow['Date'];

					//find quote on date portfolio bought stock
					$prevQuote = mysqli_query($con, "SELECT Quote FROM quotes WHERE Date = '$buyDate' AND Stock_name = '$selling'");
					$row = $prevQuote->fetch_assoc();
					$buyQuote = $row['Quote'];
					/************************************************************************************************
						not sure if we should check if buyQuote or sellQuote is zero
					**************************************************************************************************/
					if(empty($row) || $buyQuote <=0){
						continue;
					}
					
					$totalCash = $portRowCheck['Total_cash'];
					
					//appreciation factor - sell/buy quote
					$appFactor = $sellQuote/$buyQuote;
					
					$moneyInvested = $totalCash*$percentInvested;

					$returnCash = ($moneyInvested)*$appFactor;
					
					$totalCash = ($totalCash - ($returnCash/$appFactor)) + $returnCash;

					$percentOfNewComp = $returnCash/$totalCash;
					
					//Adding portfolio-new company relationship
					echo "adding the relationship between $seller ($portID) and $buying. $seller has $percentOfNewComp invested<br>";
					mysqli_query($con, "INSERT INTO portfolio_has_stocks (Portfolio_ID, Stock_name, percent_invested) VALUES (\"$portID\", \"$buying\", \"$percentOfNewComp\")");
					
					//Update total value of portfolio
					echo "updating total value of $seller ($portID) to $totalCash dollars<br>";
					mysqli_query($con, "UPDATE portfolio SET Total_cash='$totalCash' WHERE ID='$portID'");
					
					//Log action
					echo "logging $seller ($portID) selling $selling<br>";
					mysqli_query($con, "INSERT INTO portfolio_act_stocks (Portfolio_ID, Stock_name, Buy_or_sell, Date) VALUES (\"$portID\", \"$selling\", \"S\", \"$newDate\")");
					echo "logging $seller ($portID) buying $buying<br>";
					mysqli_query($con, "INSERT INTO portfolio_act_stocks (Portfolio_ID, Stock_name, Buy_or_sell, Date) VALUES (\"$portID\", \"$buying\", \"B\", \"$newDate\")");
					
					//Remove porfolio-old company relationship
					echo "removing old relationship between $seller ($portID) and $selling.<br>";
					mysqli_query($con, "DELETE FROM portfolio_has_stocks WHERE Portfolio_ID='$portID' AND Stock_name='$selling'");
				}

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