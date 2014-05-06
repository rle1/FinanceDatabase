<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Finance/Stock Administrative System</title>
        <link rel="stylesheet" href="bootstrap.min.css">
        <link rel="stylesheet" href="pagelayout.css">
    </head>
     
    <body>
        
        <!--NAVIGATION BAR-->
        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                       <li><a href="index.php">HOME</a></li>
                       <li><a href="dataimport.php">DATA IMPORT</a></li>
                       <li><a href="company.php">COMPANY</a></li>
                       <li class="active"><a href="individual.php">INDIVIDUAL</a></li>
                       <li><a href="portfolio.php">PORTFOLIO</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!--BANNER-->
        <div class="container theme-showcase" role="main">
            <div class="jumbotron banner">
               <h1><b>Finance/Stock<br> Administrative System</b></h1>
            </div>
        </div>
        
        <div class="container content">
            <h2><b>LIST OF INDIVIDUALS</b></h2>
            <hr>
                <b>
                    <form action="individualList.php" method="GET" name="individualList">
                        <input type = "submit" value="Generate List of Individuals" />
                    </form>
                </b>
            <hr>
            <h2><b>INDIVIDUAL INFORMATION</b></h2>
            <hr>
                <b>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>RANK</th>
                                <th>INDIVIDUAL NAME</th>
                                <th>FINAL NET WORTH</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php
                                session_start();
                                
                                $con = mysqli_connect("localhost", "user1", "pass1");
                                if(!$con){
                                    exit('Connect Error (' . mysqli_connect_errno() . ')' . mysqli_connect_error());
                                }
                    
                                mysqli_set_charset($con, 'utf-8');
                                mysqli_select_db($con, "finance");
                                
                                $data = array();
                            
                                $entry = array();
                                array_push($entry, "Rank");
                                array_push($entry, "Individual Name");
                                array_push($entry, "Final Net Worth");
                                
                                array_push($data, $entry);
                                
                                if ($_SESSION['all']) {
                                    $query = "SELECT * FROM individual";
                                    $indResults = mysqli_query($con, $query);
                                    
                                    $worths = array();
                                    $tableEntries = array();
                                    
                                    if (mysqli_num_rows($indResults)) {
                                        while ($row = mysqli_fetch_array($indResults)) {
                                            $individual = $row['Name'];
                                
                                            $totalWorth = 0;
                                            $cash = 0;
                                           
                                            $query = "SELECT * FROM individual WHERE individual.name = \"" . $individual . "\"";
                                            
                                            $results = mysqli_query($con, $query);
                                            
                                            if (mysqli_num_rows($results)) {
                                                while ($row = mysqli_fetch_array($results)) {
                                                    $totalWorth += $row["Cash"];
                                                    $cash += $row["Cash"];
                                                }
                                                mysqli_free_result($results);
                                            }
            
                                            $query = "SELECT individual_has_stocks.stock_name AS stock, individual_has_stocks.money_invested AS moneyInvested FROM individual, individual_has_stocks WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_has_stocks.individual_ID";
                                        
                                            $results = mysqli_query($con, $query);
                                            
                                            if (mysqli_num_rows($results)) {
                                                while ($row = mysqli_fetch_array($results)) {
                                                    
                                                    $stock_name = $row["stock"];
                                                    $moneyInvested = $row["moneyInvested"];
            
                                                    $query = "SELECT individual_act_stocks.date AS date FROM individual, individual_act_stocks WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_act_stocks.individual_ID and individual_act_stocks.stock_name = \"" . $stock_name . "\" and individual_act_stocks.buy_or_sell = 'B'"; 
                                                    
                                                    $dateResult = mysqli_query($con, $query);
                                                    
                                                    if (mysqli_num_rows($dateResult)) {
                                                        while ($row = mysqli_fetch_array($dateResult)) {
                                                            
                                                            $buyDate = $row["date"];
                                                            
                                                            $query = "SELECT quote AS buyQuote FROM quotes WHERE stock_name = \"" . $stock_name . "\" and date = \"" . $buyDate . "\""; 
                                                            $buyQuoteResult = mysqli_query($con, $query);
                                                            
                                                            if (mysqli_num_rows($buyQuoteResult)) {
                                                                while ($row = mysqli_fetch_array($buyQuoteResult)) {
                                                                    
                                                                    $buyQuote = $row['buyQuote'];
                                                                    
                                                                    $query = "SELECT quote AS sellQuote FROM quotes WHERE stock_name = \"" . $stock_name . "\" and date = \"2013-12-31\"";
                                                                    $sellQuoteResult = mysqli_query($con, $query);
                                                                    
                                                                    if (mysqli_num_rows($sellQuoteResult)) {
                                                                        while ($row = mysqli_fetch_array($sellQuoteResult)) {
                                                                            
                                                                            $sellQuote = $row['sellQuote'];
                                                                           
                                                                            $app = doubleval($sellQuote) / doubleval($buyQuote);
                                                                            
                                                                            $moneyMade = round($moneyInvested * $app, 2);
            
                                                                            $totalWorth += $moneyMade;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                mysqli_free_result($results);
                                            }
                                            
                                            $query = "SELECT individual_has_portfolios.portfolio_ID AS portfolio, individual_has_portfolios.money_invested AS moneyInvested FROM individual, individual_has_portfolios WHERE individual.name = \"" . $individual . "\" and individual_has_portfolios.individual_ID = individual.ID";
                                            $results = mysqli_query($con, $query);
                          
                                            if (mysqli_num_rows($results)) {
                                                while ($row = mysqli_fetch_array($results)) {
                                                    
                                                    $moneyInvested = $row['moneyInvested'];
                                                    $portID = $row['portfolio'];
            
                                                    $query = "SELECT * FROM portfolio WHERE ID = $portID";
                                                    $portResults = mysqli_query($con, $query);
                                                    
                                                    $portTotalWorth = 0;
                                                    
                                                    if (mysqli_num_rows($portResults)) {
                                                        while ($row = mysqli_fetch_array($portResults)) {
                                                            $portfolio = $row['Name'];
                                                            $portTotalCash = $row['Total_cash'];
                                                            $portCurrCash = $row['Curr_cash'];
                                                            
                                                            if (doubleval($portTotalCash) == 0.0) {
                                                                continue;
                                                            }
                                                            $indPercentInvest = doubleval($moneyInvested) / doubleval($portTotalCash);
                            
                                                            $query = "SELECT portfolio_has_stocks.stock_name AS stock, portfolio_has_stocks.percent_invested AS percentInvested FROM portfolio, portfolio_has_stocks WHERE portfolio.name = \"" . $portfolio . "\" and portfolio.ID = portfolio_has_stocks.portfolio_ID";
                                                        
                                                            $stockResults = mysqli_query($con, $query);
                                                            
                                                            if (mysqli_num_rows($stockResults)) {
                                                                while ($row = mysqli_fetch_array($stockResults)) {
                                                                    
                                                                    $stock_name = $row["stock"];
                                                                    $percentInvest = $row["percentInvested"];
                                                                    
                                                                    $query = "SELECT portfolio_act_stocks.date AS date FROM portfolio, portfolio_act_stocks WHERE portfolio.name = \"" . $portfolio . "\" and portfolio.ID = portfolio_act_stocks.portfolio_ID and portfolio_act_stocks.stock_name = \"" . $stock_name . "\" and portfolio_act_stocks.buy_or_sell = 'B'"; 
                                                                    
                                                                    $dateResult = mysqli_query($con, $query);
                                                                    
                                                                    if (mysqli_num_rows($dateResult)) {
                                                                        while ($row = mysqli_fetch_array($dateResult)) {
                                                                            
                                                                            $buyDate = $row["date"];
                                                                            
                                                                            $query = "SELECT quote AS buyQuote FROM quotes WHERE stock_name = \"" . $stock_name . "\" and date = \"" . $buyDate . "\""; 
                                                                            $buyQuoteResult = mysqli_query($con, $query);
                                                                            
                                                                            if (mysqli_num_rows($buyQuoteResult)) {
                                                                                while ($row = mysqli_fetch_array($buyQuoteResult)) {
                                                                                    
                                                                                    $buyQuote = $row['buyQuote'];
                                                                                    
                                                                                    $query = "SELECT quote AS sellQuote FROM quotes WHERE stock_name = \"" . $stock_name . "\" and date = \"2013-12-31\"";
                                                                                    $sellQuoteResult = mysqli_query($con, $query);
                                                                                    
                                                                                    if (mysqli_num_rows($sellQuoteResult)) {
                                                                                        while ($row = mysqli_fetch_array($sellQuoteResult)) {
                                                                                            
                                                                                            $sellQuote = $row['sellQuote'];
                                                                                           
                                                                                            $app = doubleval($sellQuote) / doubleval($buyQuote);
                                                                                            
                                                                                            $moneyInvested = doubleval($portTotalCash) * doubleval($percentInvest);
                                                                                            
                                                                                            $moneyMade = round($moneyInvested * $app, 2);
                            
                                                                                            $portTotalWorth += $moneyMade;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            
                                                            $portTotalWorth += $portCurrCash;
                                                            
                                                            $totalReturn = $indPercentInvest * $portTotalWorth;
                                                            
                                                            $totalWorth += $totalReturn;
                                                        }
                                                    }
                                                }   
                                            }
                                            
                                            $worths[$individual] = round($totalWorth, 2);
                                            
                                            $tableEntries[$individual] = "<td>" . $individual . "</td>";
                                            $tableEntries[$individual] .= "<td>$" . round($totalWorth, 2) . "</td></tr>";
                                        }
                                    }
                                    
                                    arsort($worths);
                                    
                                    $idx = 1;
                                    foreach ($worths as $key => $value) {
                                        $entry = array();
                                        array_push($entry, $idx);
  
                                        $dataSplit = explode("</td>", $tableEntries[$key]);
                                        
                                        array_push($entry, explode("<td>", $dataSplit[0])[1]);
                                        array_push($entry, explode("<td>", $dataSplit[1])[1]);
                                
                                        array_push($data, $entry);
                                        
                                        echo "<tr><td>" . $idx . "</td>";
                                        echo $tableEntries[$key];
                                        $idx++;
                                    }
                                }
                                else if ($_SESSION['individual'] == "") {
                                    $entry = array();
                                    array_push($entry, "No Individual Input");
                                    array_push($entry, "");
                                    array_push($entry, "");
                            
                                    array_push($data, $entry);
                                    
                                    echo "<tr><td>No Individual Input</td>";
                                    echo "<td></td><td></td></tr>";
                                }
                                else {
                                    $worths = array();
                                    $tableEntries = array();
                                    
                                    $indSplit = explode(",", $_SESSION['individual']);
                                    
                                    foreach ($indSplit as $individual) {
                                        $individual = trim($individual);
                                        
                                        $totalWorth = 0;
                                        $cash = 0;
                                       
                                        $query = "SELECT * FROM individual WHERE individual.name = \"" . $individual . "\"";
                                        
                                        $results = mysqli_query($con, $query);
                                        
                                        if (mysqli_num_rows($results)) {
                                            while ($row = mysqli_fetch_array($results)) {
                                                $totalWorth += $row["Cash"];
                                                $cash += $row["Cash"];
                                            }
                                            mysqli_free_result($results);
                                        }
        
                                        $query = "SELECT individual_has_stocks.stock_name AS stock, individual_has_stocks.money_invested AS moneyInvested FROM individual, individual_has_stocks WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_has_stocks.individual_ID";
                                    
                                        $results = mysqli_query($con, $query);
                                        
                                        if (mysqli_num_rows($results)) {
                                            while ($row = mysqli_fetch_array($results)) {
                                                
                                                $stock_name = $row["stock"];
                                                $moneyInvested = $row["moneyInvested"];
        
                                                $query = "SELECT individual_act_stocks.date AS date FROM individual, individual_act_stocks WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_act_stocks.individual_ID and individual_act_stocks.stock_name = \"" . $stock_name . "\" and individual_act_stocks.buy_or_sell = 'B'"; 
                                                
                                                $dateResult = mysqli_query($con, $query);
                                                
                                                if (mysqli_num_rows($dateResult)) {
                                                    while ($row = mysqli_fetch_array($dateResult)) {
                                                        
                                                        $buyDate = $row["date"];
                                                        
                                                        $query = "SELECT quote AS buyQuote FROM quotes WHERE stock_name = \"" . $stock_name . "\" and date = \"" . $buyDate . "\""; 
                                                        $buyQuoteResult = mysqli_query($con, $query);
                                                        
                                                        if (mysqli_num_rows($buyQuoteResult)) {
                                                            while ($row = mysqli_fetch_array($buyQuoteResult)) {
                                                                
                                                                $buyQuote = $row['buyQuote'];
                                                                
                                                                $query = "SELECT quote AS sellQuote FROM quotes WHERE stock_name = \"" . $stock_name . "\" and date = \"2013-12-31\"";
                                                                $sellQuoteResult = mysqli_query($con, $query);
                                                                
                                                                if (mysqli_num_rows($sellQuoteResult)) {
                                                                    while ($row = mysqli_fetch_array($sellQuoteResult)) {
                                                                        
                                                                        $sellQuote = $row['sellQuote'];
                                                                       
                                                                        $app = doubleval($sellQuote) / doubleval($buyQuote);
                                                                        
                                                                        $moneyMade = round($moneyInvested * $app, 2);
        
                                                                        $totalWorth += $moneyMade;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            mysqli_free_result($results);
                                        }
                                        
                                        $query = "SELECT individual_has_portfolios.portfolio_ID AS portfolio, individual_has_portfolios.money_invested AS moneyInvested FROM individual, individual_has_portfolios WHERE individual.name = \"" . $individual . "\" and individual_has_portfolios.individual_ID = individual.ID";
                                        $results = mysqli_query($con, $query);
                      
                                        if (mysqli_num_rows($results)) {
                                            while ($row = mysqli_fetch_array($results)) {
                                                
                                                $moneyInvested = $row['moneyInvested'];
                                                $portID = $row['portfolio'];
        
                                                $query = "SELECT * FROM portfolio WHERE ID = $portID";
                                                $portResults = mysqli_query($con, $query);
                                                
                                                $portTotalWorth = 0;
                                                
                                                if (mysqli_num_rows($portResults)) {
                                                    while ($row = mysqli_fetch_array($portResults)) {
                                                        $portfolio = $row['Name'];
                                                        $portTotalCash = $row['Total_cash'];
                                                        $portCurrCash = $row['Curr_cash'];
        
                                                        $indPercentInvest = doubleval($moneyInvested) / doubleval($portTotalCash);
                        
                                                        $query = "SELECT portfolio_has_stocks.stock_name AS stock, portfolio_has_stocks.percent_invested AS percentInvested FROM portfolio, portfolio_has_stocks WHERE portfolio.name = \"" . $portfolio . "\" and portfolio.ID = portfolio_has_stocks.portfolio_ID";
                                                    
                                                        $stockResults = mysqli_query($con, $query);
                                                        
                                                        if (mysqli_num_rows($stockResults)) {
                                                            while ($row = mysqli_fetch_array($stockResults)) {
                                                                
                                                                $stock_name = $row["stock"];
                                                                $percentInvest = $row["percentInvested"];
                                                                
                                                                $query = "SELECT portfolio_act_stocks.date AS date FROM portfolio, portfolio_act_stocks WHERE portfolio.name = \"" . $portfolio . "\" and portfolio.ID = portfolio_act_stocks.portfolio_ID and portfolio_act_stocks.stock_name = \"" . $stock_name . "\" and portfolio_act_stocks.buy_or_sell = 'B'"; 
                                                                
                                                                $dateResult = mysqli_query($con, $query);
                                                                
                                                                if (mysqli_num_rows($dateResult)) {
                                                                    while ($row = mysqli_fetch_array($dateResult)) {
                                                                        
                                                                        $buyDate = $row["date"];
                                                                        
                                                                        $query = "SELECT quote AS buyQuote FROM quotes WHERE stock_name = \"" . $stock_name . "\" and date = \"" . $buyDate . "\""; 
                                                                        $buyQuoteResult = mysqli_query($con, $query);
                                                                        
                                                                        if (mysqli_num_rows($buyQuoteResult)) {
                                                                            while ($row = mysqli_fetch_array($buyQuoteResult)) {
                                                                                
                                                                                $buyQuote = $row['buyQuote'];
                                                                                
                                                                                $query = "SELECT quote AS sellQuote FROM quotes WHERE stock_name = \"" . $stock_name . "\" and date = \"2013-12-31\"";
                                                                                $sellQuoteResult = mysqli_query($con, $query);
                                                                                
                                                                                if (mysqli_num_rows($sellQuoteResult)) {
                                                                                    while ($row = mysqli_fetch_array($sellQuoteResult)) {
                                                                                        
                                                                                        $sellQuote = $row['sellQuote'];
                                                                                       
                                                                                        $app = doubleval($sellQuote) / doubleval($buyQuote);
                                                                                        
                                                                                        $moneyInvested = doubleval($portTotalCash) * doubleval($percentInvest);
                                                                                        
                                                                                        $moneyMade = round($moneyInvested * $app, 2);
                        
                                                                                        $portTotalWorth += $moneyMade;
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        
                                                        $portTotalWorth += $portCurrCash;
                                                        
                                                        $totalReturn = $indPercentInvest * $portTotalWorth;
                                                        
                                                        $totalWorth += $totalReturn;
                                                    }
                                                }
                                            }   
                                        }
                                        
                                        $worths[$individual] = round($totalWorth, 2);
                                        
                                        $tableEntries[$individual] = "<td>" . $individual . "</td>";
                                        $tableEntries[$individual] .= "<td>$" . round($totalWorth, 2) . "</td></tr>";
                                    }
                                    
                                    arsort($worths);
                                    
                                    $idx = 1;
                                    foreach ($worths as $key => $value) {
                                        $entry = array();
                                        array_push($entry, $idx);
  
                                        $dataSplit = explode("</td>", $tableEntries[$key]);
                                        
                                        array_push($entry, explode("<td>", $dataSplit[0])[1]);
                                        array_push($entry, explode("<td>", $dataSplit[1])[1]);
                                
                                        array_push($data, $entry);
                                        
                                        echo "<tr><td>" . $idx . "</td>";
                                        echo $tableEntries[$key];
                                        $idx++;
                                    }
                                }

                                $_SESSION['file'] = "individualFinalNetWorth";
                                $_SESSION['data'] = $data;
                                
                                mysqli_close($con);
                            ?>
                        </tbody>
                    </table>
                    
                    <form action="csvExport.php" method="POST">
                        <input type = "Submit" value="Export into CSV" /> 
                    </form>
                    <br>
                    <form action="individual.php" method="GET" name="individual">
                        <input type = "submit" value="Go Back" />
                    </form>
                </b>
            <hr>
        </div>
         
    </body>
</html>