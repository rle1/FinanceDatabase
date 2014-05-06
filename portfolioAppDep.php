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
                                array_push($entry, "Portfolio Name");
                                array_push($entry, "Invested Stock");
                                array_push($entry, $_POST['date'] . " Appreciation/Depreciation Factor");
                                
                                array_push($data, $entry);
                                
                                if (isset($_POST['compareAll'])) {
                                    $query = "SELECT * FROM portfolio";
                                    $portResults = mysqli_query($con, $query);
                                    
                                    $apps = array();
                                    $tableEntries = array();
                                    
                                    $index = 0;
                                    
                                    if (mysqli_num_rows($portResults)) {
                                        while ($row = mysqli_fetch_array($portResults)) {
                                            $portfolio = $row['Name'];
                               
                                            $query = "SELECT * FROM portfolio, portfolio_has_stocks WHERE portfolio.name = \"" . $portfolio . "\" and portfolio.ID = portfolio_has_stocks.portfolio_ID";
                                            $results = mysqli_query($con, $query);
                                            
                                            if (mysqli_num_rows($results)) {
                                                while ($row = mysqli_fetch_array($results)) {
                                                    $stock_name = $row['Stock_name'];
                                                    
                                                    $query = "SELECT portfolio_act_stocks.date AS date FROM portfolio, portfolio_act_stocks WHERE portfolio.name = \"" . $portfolio . "\" and portfolio.ID = portfolio_act_stocks.portfolio_ID and portfolio_act_stocks.stock_name = \"" . $stock_name . "\" and portfolio_act_stocks.buy_or_sell = 'B'"; 
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
                                                                $tableEntries[$index . "_" . $portfolio] = "<td>" . $portfolio . "</td>";
                                                                $tableEntries[$index . "_" . $portfolio] .= "<td>" . $stock_name . "</td>";
                                                                $tableEntries[$index . "_" . $portfolio] .= "<td>Error: Date Provided Is Before Buy Date</td></tr>";
                                                                
                                                                $apps[$index . "_" . $portfolio] = -1 * INF;
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
                                                                            
                                                                            $tableEntries[$index . "_" . $portfolio] = "<td>" . $portfolio . "</td>";
                                                                            $tableEntries[$index . "_" . $portfolio] .= "<td>" . $stock_name . "</td>";
                                                                            $tableEntries[$index . "_" . $portfolio] .= "<td>" . $app . "</td></tr>";
                                                                            
                                                                            $apps[$index . "_" . $portfolio] = $app;
                                                                            $index++;
                                                                        }
                                                                    }
                                                                    else {
                                                                        $tableEntries[$index . "_" . $portfolio] = "<td>" . $portfolio . "</td>";
                                                                        $tableEntries[$index . "_" . $portfolio] .= "<td>" . $stock_name . "</td>";
                                                                        $tableEntries[$index . "_" . $portfolio] .= "<td>Error: Date Provided Has No Quote Data</td></tr>";
                                                                        
                                                                        $apps[$index . "_" . $portfolio] = -1 * INF;
                                                                        $index++;
                                                                    }
                                                                }
                                                            }
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
                                else if ($_POST['portfolio'] == "") {
                                    $entry = array();
                                    array_push($entry, "No Portfolio Input");
                                    array_push($entry, "");
                                    array_push($entry, "");
                                    array_push($entry, "");
                            
                                    array_push($data, $entry);
                                        
                                    echo "<tr><td>No Portfolio Input</td>";
                                    echo "<td></td><td></td><td></td></tr>";
                                }
                                else {
                                    $portSplit = explode(",", $_POST['portfolio']);
                                    
                                    $apps = array();
                                    $tableEntries = array();
                                    $index = 0;
                                        
                                    foreach ($portSplit as $portfolio) {
                                        $portfolio = trim($portfolio);
                                        
                                        $query = "SELECT * FROM portfolio, portfolio_has_stocks WHERE portfolio.name = \"" . $portfolio . "\" and portfolio.ID = portfolio_has_stocks.portfolio_ID";
                                        $results = mysqli_query($con, $query);
                                        
                                        if (mysqli_num_rows($results)) {
                                            while ($row = mysqli_fetch_array($results)) {
                                                $stock_name = $row['Stock_name'];
                                                
                                                $query = "SELECT portfolio_act_stocks.date AS date FROM portfolio, portfolio_act_stocks WHERE portfolio.name = \"" . $portfolio . "\" and portfolio.ID = portfolio_act_stocks.portfolio_ID and portfolio_act_stocks.stock_name = \"" . $stock_name . "\" and portfolio_act_stocks.buy_or_sell = 'B'"; 
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
                                                            $tableEntries[$index . "_" . $portfolio] = "<td>" . $portfolio . "</td>";
                                                            $tableEntries[$index . "_" . $portfolio] .= "<td>" . $stock_name . "</td>";
                                                            $tableEntries[$index . "_" . $portfolio] .= "<td>Error: Date Provided Is Before Buy Date</td></tr>";
                                                            
                                                            $apps[$index . "_" . $portfolio] = -1 * INF;
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
                                                                        
                                                                        $tableEntries[$index . "_" . $portfolio] = "<td>" . $portfolio . "</td>";
                                                                        $tableEntries[$index . "_" . $portfolio] .= "<td>" . $stock_name . "</td>";
                                                                        $tableEntries[$index . "_" . $portfolio] .= "<td>" . $app . "</td></tr>";
                                                                        
                                                                        $apps[$index . "_" . $portfolio] = $app;
                                                                        $index++;
                                                                    }
                                                                }
                                                                else {
                                                                    $tableEntries[$index . "_" . $portfolio] = "<td>" . $portfolio . "</td>";
                                                                    $tableEntries[$index . "_" . $portfolio] .= "<td>" . $stock_name . "</td>";
                                                                    $tableEntries[$index . "_" . $portfolio] .= "<td>Error: Date Provided Has No Quote Data</td></tr>";
                                                                    
                                                                    $apps[$index . "_" . $portfolio] = -1 * INF;
                                                                    $index++;
                                                                }
                                                            }
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
                                
                                $_SESSION['file'] = "portfolioAppDep";
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