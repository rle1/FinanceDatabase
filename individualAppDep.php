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
                                <th>INVESTED STOCK</th>
                                <th>
                                    <?php
                                        echo $_POST['date'];
                                    ?>
                                    <br>APPRECIATION/DEPRECIATION FACTOR
                                </th>
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
                                array_push($entry, "Invested Stock");
                                array_push($entry, $_POST['date'] . " Appreciation/Depreciation Factor");
                                
                                array_push($data, $entry);
                                
                                if (isset($_POST['compareAll'])) {
                                    $query = "SELECT * FROM individual";
                                    $indResults = mysqli_query($con, $query);
                                    
                                    $apps = array();
                                    $tableEntries = array();
                                    $index = 0;
                                    
                                    if (mysqli_num_rows($indResults)) {
                                        while ($row = mysqli_fetch_array($indResults)) {
                                            $individual = $row['Name'];
                               
                                            $query = "SELECT * FROM individual, individual_has_stocks WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_has_stocks.individual_ID";
                                            $results = mysqli_query($con, $query);
                                            
                                            if (mysqli_num_rows($results)) {
                                                while ($row = mysqli_fetch_array($results)) {
                                                    $stock_name = $row['Stock_name'];
                                                    
                                                    $query = "SELECT individual_act_stocks.date AS date FROM individual, individual_act_stocks WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_act_stocks.individual_ID and individual_act_stocks.stock_name = \"" . $stock_name . "\" and individual_act_stocks.buy_or_sell = 'B'"; 
                                                    $dateResult = mysqli_query($con, $query);
                                                    
                                                    if (mysqli_num_rows($dateResult)) {
                                                        while ($row = mysqli_fetch_array($dateResult)) {
                                                            
                                                            $buyDate = $row["date"];
            
                                                            $buyDateSplit = explode("-", $buyDate);
                                                            $sellDateSplit = explode("-", $_POST['date']);
                                                            
                                                            $check = false;
                                                            
                                                            if ($sellDateSplit[0] < $buyDateSplit[0]) {
                                                                $check = true;
                                                            }
                                                            else if($sellDateSplit[0] == $buyDateSplit[0]) {
                                                                if ($sellDateSplit[1] < $buyDateSplit[1]) {
                                                                    $check = true;
                                                                }
                                                                else if ($sellDateSplit[1] == $buyDateSplit[1]) {
                                                                    if ($sellDateSplit[2] < $buyDateSplit[2]) {
                                                                        $check = true;
                                                                    }
                                                                }
                                                            }
                                                            
                                                            if ($check) {
                                                                $tableEntries[$index . "_" . $individual] = "<td>" . $individual . "</td>";
                                                                $tableEntries[$index . "_" . $individual] .= "<td>" . $stock_name . "</td>";
                                                                $tableEntries[$index . "_" . $individual] .= "<td>Error: Date Provided Is Before Buy Date</td></tr>";
                                                                
                                                                $apps[$index . "_" . $individual] = -1 * INF;
                                                                $index++;
                                                                
                                                                continue;
                                                            }
                                                            
                                                            $query = "SELECT quote AS buyQuote FROM quotes WHERE stock_name = \"" . $stock_name . "\" and date = \"" . $buyDate . "\""; 
                                                            $buyQuoteResult = mysqli_query($con, $query);
                                                            
                                                            if (mysqli_num_rows($buyQuoteResult)) {
                                                                while ($row = mysqli_fetch_array($buyQuoteResult)) {
                                                                    
                                                                    $buyQuote = $row['buyQuote'];
            
                                                                    $query = "SELECT quote AS sellQuote FROM quotes WHERE stock_name = \"" . $stock_name . "\" and date = \"" . $_POST['date'] . "\"";
                                                                    $sellQuoteResult = mysqli_query($con, $query);
                                                                    
                                                                    if (mysqli_num_rows($sellQuoteResult)) {
                                                                        while ($row = mysqli_fetch_array($sellQuoteResult)) {
                                                                            
                                                                            $sellQuote = $row['sellQuote'];
                                                                           
                                                                            $app = doubleval($sellQuote) / doubleval($buyQuote);
                                                                            
                                                                            $tableEntries[$index . "_" . $individual] = "<td>" . $individual . "</td>";
                                                                            $tableEntries[$index . "_" . $individual] .= "<td>" . $stock_name . "</td>";
                                                                            $tableEntries[$index . "_" . $individual] .= "<td>" . $app . "</td></tr>";
                                                                            
                                                                            $apps[$index . "_" . $individual] = $app;
                                                                            $index++;
                                                                        }
                                                                    }
                                                                    else {
                                                                        $tableEntries[$index . "_" . $individual] = "<td>" . $individual . "</td>";
                                                                        $tableEntries[$index . "_" . $individual] .= "<td>" . $stock_name . "</td>";
                                                                        $tableEntries[$index . "_" . $individual] .= "<td>Error: Date Provided Has No Quote Data</td></tr>";
                                                                        
                                                                        $apps[$index . "_" . $individual] = -1 * INF;
                                                                        $index++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }   
                                                }
                                                mysqli_free_result($results);
                                            } 
                                        }  
                                    }
   
                                    arsort($apps);
                            
                                    $idx = 1;
                                    foreach ($apps as $key => $value) {
                                        $entry = array();
                                        array_push($entry, $idx);
  
                                        $dataSplit = explode("</td>", $tableEntries[$key]);
                                        
                                        array_push($entry, explode("<td>", $dataSplit[0])[1]);
                                        array_push($entry, explode("<td>", $dataSplit[1])[1]);
                                        array_push($entry, explode("<td>", $dataSplit[2])[1]);
                                
                                        array_push($data, $entry);
                                        
                                        echo "<tr><td>" . $idx . "</td>";
                                        echo $tableEntries[$key];
                                        $idx++;
                                    }
                                    
                                    $entry = array();
                                    array_push($entry, "");
                                    array_push($entry, "");
                                    array_push($entry, "");
                                    array_push($entry, "");
                            
                                    array_push($data, $entry);
                                    
                                    $entry = array();
                                    array_push($entry, "Rank");
                                    array_push($entry, "Individual Name");
                                    array_push($entry, "Invested Portfolio");
                                    array_push($entry, $_POST['date'] . " Appreciation/Depreciation Factor");
                                    
                                    array_push($data, $entry);
                                            
                                    echo "</tbody></table>";
                                    echo "<table class=\"table table-striped\"><thead><th>RANK</th><th>INDIVIDUAL NAME</th><th>INVESTED PORTFOLIO</th><th>" . $_POST['date'] . "<br>APPRECIATION/DEPRECIATION FACTOR</th><tbody>";
                                            
                                    $query = "SELECT * FROM individual";
                                    $indResults = mysqli_query($con, $query);
                                    
                                    $apps = array();
                                    $tableEntries = array();
                                    
                                    if (mysqli_num_rows($indResults)) {
                                        while ($row = mysqli_fetch_array($indResults)) {
                                            $individual = $row['Name'];
                                            
                                            $query = "SELECT individual_has_portfolios.portfolio_ID AS portfolio, individual_has_portfolios.money_invested AS moneyInvested FROM individual, individual_has_portfolios WHERE individual.name = \"" . $individual . "\" and individual_has_portfolios.individual_ID = individual.ID";
                                            $results = mysqli_query($con, $query);
                          
                                            if (mysqli_num_rows($results)) {
                                                while ($row = mysqli_fetch_array($results)) {
                                                    
                                                    $indMoneyInvested = $row['moneyInvested'];
                                                    $portID = $row['portfolio'];
                                                    
                                                    $query = "SELECT * FROM portfolio WHERE ID = $portID";
                                                    $portResults = mysqli_query($con, $query);
                                                    
                                                    $portTotalWorth = 0;
                                                    
                                                    if (mysqli_num_rows($portResults)) {
                                                        while ($row = mysqli_fetch_array($portResults)) {
                                                            $portfolio = $row['Name'];
                                                            $portTotalCash = $row['Total_cash'];
                                                            $portCurrCash = $row['Curr_cash'];
            
                                                            $query = "SELECT individual_act_portfolios.date AS date FROM individual, individual_act_portfolios WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_act_portfolios.individual_ID and individual_act_portfolios.portfolio_ID = $portID and individual_act_portfolios.buy_or_sell = 'B'";
                                                            $checkDateResults = mysqli_query($con, $query);
                                                            
                                                            $buyPortDate = "";
                                                            if (mysqli_num_rows($checkDateResults)) {
                                                                while ($row = mysqli_fetch_array($checkDateResults)) {
                                                                    $buyPortDate = $row['date'];
                                                                }
                                                            }
                                                            
                                                            $buyDateSplit = explode("-", $buyPortDate);
                                                            $sellDateSplit = explode("-", $_POST['date']);
                                                            
                                                            $check = false;
                                                            
                                                            if ($sellDateSplit[0] < $buyDateSplit[0]) {
                                                                $check = true;
                                                            }
                                                            else if($sellDateSplit[0] == $buyDateSplit[0]) {
                                                                if ($sellDateSplit[1] < $buyDateSplit[1]) {
                                                                    $check = true;
                                                                }
                                                                else if ($sellDateSplit[1] == $buyDateSplit[1]) {
                                                                    if ($sellDateSplit[2] < $buyDateSplit[2]) {
                                                                        $check = true;
                                                                    }
                                                                }
                                                            }
                                                            
                                                            if ($check) {
                                                                $tableEntries[$index . "_" . $individual] = "<td>" . $individual . "</td>";
                                                                $tableEntries[$index . "_" . $individual] .= "<td>" . $portfolio . "</td>";
                                                                $tableEntries[$index . "_" . $individual] .= "<td>Error: Date Provided Is Before Buy Date</td></tr>";
                                                                
                                                                $apps[$index . "_" . $individual] = -1 * INF;
                                                                $index++;
                                                                
                                                                continue;
                                                            }
                                                    
                                                            
                                                            $indPercentInvest = doubleval($indMoneyInvested) / doubleval($portTotalCash);
                            
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
                                                            
                                                            $appFactor = doubleval($totalReturn) / doubleval($indMoneyInvested);
                                                            
                                                            $tableEntries[$index . "_" . $individual] = "<td>" . $individual . "</td>";
                                                            $tableEntries[$index . "_" . $individual] .= "<td>" . $portfolio . "</td>";
                                                            $tableEntries[$index . "_" . $individual] .= "<td>" . $appFactor . "</td></tr>";
                                                            
                                                            $apps[$index . "_" . $individual] = $appFactor;
                                                            $index++;
                                                        }
                                                    }
                                                }   
                                            }
                                        }
                                    }
                                    
                                    arsort($apps);
                                            
                                    $idx = 1;
                                    foreach ($apps as $key => $value) {
                                        $entry = array();
                                        array_push($entry, $idx);
  
                                        $dataSplit = explode("</td>", $tableEntries[$key]);
                                        
                                        array_push($entry, explode("<td>", $dataSplit[0])[1]);
                                        array_push($entry, explode("<td>", $dataSplit[1])[1]);
                                        array_push($entry, explode("<td>", $dataSplit[2])[1]);
                                
                                        array_push($data, $entry);
                                        
                                        echo "<tr><td>" . $idx . "</td>";
                                        echo $tableEntries[$key];
                                        $idx++;
                                    }
                                }
                                else if ($_POST['individual'] == "") {
                                    $entry = array();
                                    array_push($entry, "No Individual Input");
                                    array_push($entry, "");
                                    array_push($entry, "");
                                    array_push($entry, "");
                            
                                    array_push($data, $entry);
                                        
                                    echo "<tr><td>No Individual Input</td>";
                                    echo "<td></td><td></td><td></td></tr>";
                                    echo "</tbody></table>";
                                    echo "<table class=\"table table-striped\"><thead><th>RANK</th><th>INDIVIDUAL NAME</th><th>INVESTED PORTFOLIO</th><th>" . $_POST['date'] . "<br>APPRECIATION/DEPRECIATION FACTOR</th><tbody>";
                                    echo "<tr><td>No Individual Input</td>";
                                    echo "<td></td><td></td><td></td></tr>";
                                    echo "</tbody></table>";
                                }
                                else {
                                    $indSplit = explode(",", $_POST['individual']);
                                    
                                    $apps = array();
                                    $tableEntries = array();
                                    $index = 0;
                                    
                                    foreach ($indSplit as $individual) {
                                        $individual = trim($individual);
                                        
                                        $query = "SELECT * FROM individual, individual_has_stocks WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_has_stocks.individual_ID";
                                        $results = mysqli_query($con, $query);
                                        
                                        if (mysqli_num_rows($results)) {
                                            while ($row = mysqli_fetch_array($results)) {
                                                $stock_name = $row['Stock_name'];
                                                $query = "SELECT individual_act_stocks.date AS date FROM individual, individual_act_stocks WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_act_stocks.individual_ID and individual_act_stocks.stock_name = \"" . $stock_name . "\" and individual_act_stocks.buy_or_sell = 'B'"; 
                                                $dateResult = mysqli_query($con, $query);
                                                
                                                if (mysqli_num_rows($dateResult)) {
                                                    while ($row = mysqli_fetch_array($dateResult)) {
                                                        
                                                        $buyDate = $row["date"];
        
                                                        $buyDateSplit = explode("-", $buyDate);
                                                        $sellDateSplit = explode("-", $_POST['date']);
                                                        
                                                        $check = false;
                                                        
                                                        if ($sellDateSplit[0] < $buyDateSplit[0]) {
                                                            $check = true;
                                                        }
                                                        else if($sellDateSplit[0] == $buyDateSplit[0]) {
                                                            if ($sellDateSplit[1] < $buyDateSplit[1]) {
                                                                $check = true;
                                                            }
                                                            else if ($sellDateSplit[1] == $buyDateSplit[1]) {
                                                                if ($sellDateSplit[2] < $buyDateSplit[2]) {
                                                                    $check = true;
                                                                }
                                                            }
                                                        }
                                                        
                                                        if ($check) {
                                                            $tableEntries[$index . "_" . $individual] = "<td>" . $individual . "</td>";
                                                            $tableEntries[$index . "_" . $individual] .= "<td>" . $stock_name . "</td>";
                                                            $tableEntries[$index . "_" . $individual] .= "<td>Error: Date Provided Is Before Buy Date</td></tr>";
                                                            
                                                            $apps[$index . "_" . $individual] = -1 * INF;
                                                            $index++;
                                                            
                                                            continue;
                                                        }
                                                        
                                                        $query = "SELECT quote AS buyQuote FROM quotes WHERE stock_name = \"" . $stock_name . "\" and date = \"" . $buyDate . "\""; 
                                                        $buyQuoteResult = mysqli_query($con, $query);
                                                        
                                                        if (mysqli_num_rows($buyQuoteResult)) {
                                                            while ($row = mysqli_fetch_array($buyQuoteResult)) {
                                                                
                                                                $buyQuote = $row['buyQuote'];
        
                                                                $query = "SELECT quote AS sellQuote FROM quotes WHERE stock_name = \"" . $stock_name . "\" and date = \"" . $_POST['date'] . "\"";
                                                                $sellQuoteResult = mysqli_query($con, $query);
                                                                
                                                                if (mysqli_num_rows($sellQuoteResult)) {
                                                                    while ($row = mysqli_fetch_array($sellQuoteResult)) {
                                                                        
                                                                        $sellQuote = $row['sellQuote'];
                                                                       
                                                                        $app = doubleval($sellQuote) / doubleval($buyQuote);
                                                                        
                                                                        $tableEntries[$index . "_" . $individual] = "<td>" . $individual . "</td>";
                                                                        $tableEntries[$index . "_" . $individual] .= "<td>" . $stock_name . "</td>";
                                                                        $tableEntries[$index . "_" . $individual] .= "<td>" . $app . "</td></tr>";
                                                                        
                                                                        $apps[$index . "_" . $individual] = $app;
                                                                        $index++;
                                                                    }
                                                                }
                                                                else {
                                                                    $tableEntries[$index . "_" . $individual] = "<td>" . $individual . "</td>";
                                                                    $tableEntries[$index . "_" . $individual] .= "<td>" . $stock_name . "</td>";
                                                                    $tableEntries[$index . "_" . $individual] .= "<td>Error: Date Provided Has No Quote Data</td></tr>";
                                                                    
                                                                    $apps[$index . "_" . $individual] = -1 * INF;
                                                                    $index++;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            mysqli_free_result($results);
                                        }   
                                    }
                                    
                                    arsort($apps);
                            
                                    $idx = 1;
                                    foreach ($apps as $key => $value) {
                                        $entry = array();
                                        array_push($entry, $idx);
  
                                        $dataSplit = explode("</td>", $tableEntries[$key]);
                                        
                                        array_push($entry, explode("<td>", $dataSplit[0])[1]);
                                        array_push($entry, explode("<td>", $dataSplit[1])[1]);
                                        array_push($entry, explode("<td>", $dataSplit[2])[1]);
                                
                                        array_push($data, $entry);
                                        
                                        echo "<tr><td>" . $idx . "</td>";
                                        echo $tableEntries[$key];
                                        $idx++;
                                    }
                                    
                                    $entry = array();
                                    array_push($entry, "");
                                    array_push($entry, "");
                                    array_push($entry, "");
                                    array_push($entry, "");
                            
                                    array_push($data, $entry);
                                    
                                    $entry = array();
                                    array_push($entry, "Rank");
                                    array_push($entry, "Individual Name");
                                    array_push($entry, "Invested Portfolio");
                                    array_push($entry, $_POST['date'] . " Appreciation/Depreciation Factor");
                                    
                                    array_push($data, $entry);
                                            
                                    echo "</tbody></table>";
                                    echo "<table class=\"table table-striped\"><thead><th>RANK</th><th>INDIVIDUAL NAME</th><th>INVESTED PORTFOLIO</th><th>" . $_POST['date'] . "<br>APPRECIATION/DEPRECIATION FACTOR</th><tbody>";
                                            
                                    $apps = array();
                                    $tableEntries = array();
                                    
                                    foreach ($indSplit as $individual) {
                                        $individual = trim($individual);

                                        $query = "SELECT individual_has_portfolios.portfolio_ID AS portfolio, individual_has_portfolios.money_invested AS moneyInvested FROM individual, individual_has_portfolios WHERE individual.name = \"" . $individual . "\" and individual_has_portfolios.individual_ID = individual.ID";
                                        $results = mysqli_query($con, $query);
                      
                                        if (mysqli_num_rows($results)) {
                                            while ($row = mysqli_fetch_array($results)) {
                                                
                                                $indMoneyInvested = $row['moneyInvested'];
                                                $portID = $row['portfolio'];
                                                
                                                $query = "SELECT * FROM portfolio WHERE ID = $portID";
                                                $portResults = mysqli_query($con, $query);
                                                
                                                $portTotalWorth = 0;
                                                
                                                if (mysqli_num_rows($portResults)) {
                                                    while ($row = mysqli_fetch_array($portResults)) {
                                                        $portfolio = $row['Name'];
                                                        $portTotalCash = $row['Total_cash'];
                                                        $portCurrCash = $row['Curr_cash'];
        
                                                        $query = "SELECT individual_act_portfolios.date AS date FROM individual, individual_act_portfolios WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_act_portfolios.individual_ID and individual_act_portfolios.portfolio_ID = $portID and individual_act_portfolios.buy_or_sell = 'B'";
                                                        $checkDateResults = mysqli_query($con, $query);
                                                        
                                                        $buyPortDate = "";
                                                        if (mysqli_num_rows($checkDateResults)) {
                                                            while ($row = mysqli_fetch_array($checkDateResults)) {
                                                                $buyPortDate = $row['date'];
                                                            }
                                                        }
                                                        
                                                        $buyDateSplit = explode("-", $buyPortDate);
                                                        $sellDateSplit = explode("-", $_POST['date']);
                                                        
                                                        $check = false;
                                                        
                                                        if ($sellDateSplit[0] < $buyDateSplit[0]) {
                                                            $check = true;
                                                        }
                                                        else if($sellDateSplit[0] == $buyDateSplit[0]) {
                                                            if ($sellDateSplit[1] < $buyDateSplit[1]) {
                                                                $check = true;
                                                            }
                                                            else if ($sellDateSplit[1] == $buyDateSplit[1]) {
                                                                if ($sellDateSplit[2] < $buyDateSplit[2]) {
                                                                    $check = true;
                                                                }
                                                            }
                                                        }
                                                        
                                                        if ($check) {
                                                            $tableEntries[$index . "_" . $individual] = "<td>" . $individual . "</td>";
                                                            $tableEntries[$index . "_" . $individual] .= "<td>" . $portfolio . "</td>";
                                                            $tableEntries[$index . "_" . $individual] .= "<td>Error: Date Provided Is Before Buy Date</td></tr>";
                                                            
                                                            $apps[$index . "_" . $individual] = -1 * INF;
                                                            $index++;
                                                            
                                                            continue;
                                                        }
                                                
                                                        
                                                        $indPercentInvest = doubleval($indMoneyInvested) / doubleval($portTotalCash);
                        
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
                                                        
                                                        $appFactor = doubleval($totalReturn) / doubleval($indMoneyInvested);
                                                        
                                                        $tableEntries[$index . "_" . $individual] = "<td>" . $individual . "</td>";
                                                        $tableEntries[$index . "_" . $individual] .= "<td>" . $portfolio . "</td>";
                                                        $tableEntries[$index . "_" . $individual] .= "<td>" . $appFactor . "</td></tr>";
                                                        
                                                        $apps[$index . "_" . $individual] = $appFactor;
                                                        $index++;
                                                    }
                                                }
                                            }   
                                        }
                                    }
                                    
                                    arsort($apps);
                                            
                                    $idx = 1;
                                    foreach ($apps as $key => $value) {
                                        $entry = array();
                                        array_push($entry, $idx);
  
                                        $dataSplit = explode("</td>", $tableEntries[$key]);
                                        
                                        array_push($entry, explode("<td>", $dataSplit[0])[1]);
                                        array_push($entry, explode("<td>", $dataSplit[1])[1]);
                                        array_push($entry, explode("<td>", $dataSplit[2])[1]);
                                
                                        array_push($data, $entry);
                                        
                                        echo "<tr><td>" . $idx . "</td>";
                                        echo $tableEntries[$key];
                                        $idx++;
                                    }
                                }
                                
                                $_SESSION['file'] = "individualAppDep";
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