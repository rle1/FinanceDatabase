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
                       <li><a href="individual.php">INDIVIDUAL</a></li>
                       <li class="active"><a href="portfolio.php">PORTFOLIO</a></li>
                       <li><a href="mysteryQuery.php">MYSTERY QUERY</a></li>
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
            <h2><b>LIST OF PORTFOLIOS</b></h2>
            <hr>
                <b>
                    <form action="portfolioList.php" method="GET" name="portfolioList">
                        <input type = "submit" value="Generate List of Portfolios" />
                    </form>
                </b>
            <hr>
            <h2><b>PORTFOLIO INFORMATION</b></h2>
            <hr>
                <b>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>RANK</th>
                                <th>PORTFOLIO NAME</th>
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
                                array_push($entry, "Portfolio Name");
                                array_push($entry, "Final Net Worth");
                                
                                array_push($data, $entry);
                                
                                
                                if ($_SESSION['all']) {
                                    $query = "SELECT * FROM portfolio";
                                    $portResults = mysqli_query($con, $query);
                                    
                                    $worths = array();
                                    $tableEntries = array();
                                    
                                    if (mysqli_num_rows($portResults)) {
                                        while ($row = mysqli_fetch_array($portResults)) {
                                            $portfolio = $row['Name'];
                                
                                            $totalWorth = 0;
                                            $totalCash = 0;
                                           
                                            $query = "SELECT * FROM portfolio WHERE portfolio.name = \"" . $portfolio . "\"";
                                            
                                            $results = mysqli_query($con, $query);
                                            
                                            if (mysqli_num_rows($results)) {
                                                while ($row = mysqli_fetch_array($results)) {
                                                    $totalWorth += $row["Curr_cash"];
                                                    $totalCash += $row["Total_cash"];
                                                }
                                                mysqli_free_result($results);
                                            }
            
                                            $query = "SELECT portfolio_has_stocks.stock_name AS stock, portfolio_has_stocks.percent_invested AS percentInvested FROM portfolio, portfolio_has_stocks WHERE portfolio.name = \"" . $portfolio . "\" and portfolio.ID = portfolio_has_stocks.portfolio_ID";
                                        
                                            $results = mysqli_query($con, $query);
                                            
                                            if (mysqli_num_rows($results)) {
                                                while ($row = mysqli_fetch_array($results)) {
                                                    
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
                                                                            
                                                                            $moneyInvested = doubleval($totalCash) * doubleval($percentInvest);
                                                                            
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
                                            
                                            $query = "SELECT individual_has_portfolios.money_invested AS moneyInvested FROM portfolio, individual_has_portfolios WHERE portfolio.name = \"" . $portfolio . "\" and individual_has_portfolios.portfolio_ID = portfolio.ID";
                                            $results = mysqli_query($con, $query);
                                            
                                            $indInvest = array();
                                            
                                            if (mysqli_num_rows($results)) {
                                                while ($row = mysqli_fetch_array($results)) {
                                                    if (doubleval($totalCash) == 0.0) {
                                                        continue;
                                                    }
                                                    $percentInvest = doubleval($row['moneyInvested']) / doubleval($totalCash);
                                                    
                                                    array_push($indInvest, $percentInvest);
                                                }
                                            }
                                            
                                            $percentWorth = 1.0;
                                            foreach ($indInvest as $val) {
                                                $percentWorth -= $val;
                                            }
                                            
                                            $totalWorth = round($totalWorth * $percentWorth, 2);
                                            
                                            $tableEntries[$portfolio] = "<td>" . $portfolio . "</td>";
                                            $tableEntries[$portfolio] .= "<td>$" . $totalWorth . "</td></tr>";
                                            
                                            $worths[$portfolio] = $totalWorth;
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
                                else if ($_SESSION['portfolio'] == "") {
                                    $entry = array();
                                    array_push($entry, "No Portfolio Input");
                                    array_push($entry, "");
                                    array_push($entry, "");
                            
                                    array_push($data, $entry);
                                    
                                    echo "<tr><td>No Portfolio Input</td>";
                                    echo "<td></td><td></td></tr>";
                                }
                                else {
                                    $portSplit = explode(",", $_SESSION['portfolio']);
                                    
                                    $returns = array();
                                    $tableEntries = array();
                                        
                                    foreach ($portSplit as $portfolio) {
                                        $portfolio = trim($portfolio);
                                        $totalWorth = 0;
                                        
                                        $totalCash = 0;
                                       
                                        $query = "SELECT * FROM portfolio WHERE portfolio.name = \"" . $portfolio . "\"";
                                        
                                        $results = mysqli_query($con, $query);
                                        
                                        if (mysqli_num_rows($results)) {
                                            while ($row = mysqli_fetch_array($results)) {
                                                $totalWorth += $row["Curr_cash"];
                                                $totalCash += $row["Total_cash"];
                                            }
                                            mysqli_free_result($results);
                                        }
        
                                        $query = "SELECT portfolio_has_stocks.stock_name AS stock, portfolio_has_stocks.percent_invested AS percentInvested FROM portfolio, portfolio_has_stocks WHERE portfolio.name = \"" . $portfolio . "\" and portfolio.ID = portfolio_has_stocks.portfolio_ID";
                                    
                                        $results = mysqli_query($con, $query);
                                        
                                        if (mysqli_num_rows($results)) {
                                            while ($row = mysqli_fetch_array($results)) {
                                                
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
                                                                        
                                                                        $moneyInvested = doubleval($totalCash) * doubleval($percentInvest);
                                                                        
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
                                        
                                        $query = "SELECT individual_has_portfolios.money_invested AS moneyInvested FROM portfolio, individual_has_portfolios WHERE portfolio.name = \"" . $portfolio . "\" and individual_has_portfolios.portfolio_ID = portfolio.ID";
                                        $results = mysqli_query($con, $query);
                                        
                                        $indInvest = array();
                                        
                                        if (mysqli_num_rows($results)) {
                                            while ($row = mysqli_fetch_array($results)) {
                                                $percentInvest = doubleval($row['moneyInvested']) / doubleval($totalCash);
                                                
                                                array_push($indInvest, $percentInvest);
                                            }
                                        }
                                        
                                        $percentWorth = 1.0;
                                        foreach ($indInvest as $val) {
                                            $percentWorth -= $val;
                                        }
                                        
                                        $totalWorth = round($totalWorth * $percentWorth, 2);
                                        
                                        $tableEntries[$portfolio] = "<td>" . $portfolio . "</td>";
                                        $tableEntries[$portfolio] .= "<td>$" . $totalWorth . "</td></tr>";
                                        
                                        $worths[$portfolio] = $totalWorth; 
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
                                
                                $_SESSION['file'] = "portfolioFinalNetWorth";
                                $_SESSION['data'] = $data;
                                
                                mysqli_close($con);
                            ?>
                        </tbody>
                    </table>
                    
                    <form action="csvExport.php" method="POST">
                        <input type = "Submit" value="Export into CSV" /> 
                    </form>
                    <br>
                    <form action="portfolio.php" method="GET" name="portfolio">
                        <input type = "submit" value="Go Back" />
                    </form>
                </b>
            <hr>
        </div>
         
    </body>
</html>