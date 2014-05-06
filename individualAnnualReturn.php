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
                                <th>STOCK</th>
                                <th>ANNUAL RATE OF RETURN</th>
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
                                
                                $query = "SELECT * FROM individual";
                                $indResults = mysqli_query($con, $query);
                                
                                $returns = array();
                                
                                if (mysqli_num_rows($indResults)) {
                                    while ($row = mysqli_fetch_array($indResults)) {
                                        $individual = $row['Name'];
                                        $indID = $row['ID'];
                                        
                                        $query = "SELECT * FROM individual_act_stocks WHERE individual_act_stocks.individual_ID = " . $indID . " and individual_act_stocks.buy_or_sell = 'B'";
                                        $actStockResults = mysqli_query($con, $query);
                                        
                                        if (mysqli_num_rows($actStockResults)) {
                                            while ($row = mysqli_fetch_array($actStockResults)) {
                                                $stock = $row['Stock_name'];
                                                $buyDate = $row['Date'];
                                                
                                                $query = "SELECT * FROM individual_act_stocks WHERE individual_act_stocks.individual_ID = " . $indID . " and individual_act_stocks.stock_name = \"" . $stock . "\" and individual_act_stocks.buy_or_sell = 'S'";
                                                $dateResult = mysqli_query($con, $query);
                                                
                                                if (mysqli_num_rows($dateResult)) {
                                                    while ($row = mysqli_fetch_array($dateResult)) {
                                                        $sellDate = $row['Date'];
                                                        
                                                        $query = "SELECT quote AS buyQuote FROM quotes WHERE stock_name = \"" . $stock . "\" and date = \"" . $buyDate . "\""; 
                                                        $buyQuoteResult = mysqli_query($con, $query);
                                                        
                                                        if (mysqli_num_rows($buyQuoteResult)) {
                                                            while ($row = mysqli_fetch_array($buyQuoteResult)) {
                                                                
                                                                $buyQuote = $row['buyQuote'];
        
                                                                $query = "SELECT quote AS sellQuote FROM quotes WHERE stock_name = \"" . $stock . "\" and date = \"" . $sellDate . "\"";
                                                                $sellQuoteResult = mysqli_query($con, $query);
                                                                
                                                                if (mysqli_num_rows($sellQuoteResult)) {
                                                                    while ($row = mysqli_fetch_array($sellQuoteResult)) {
                                                                        
                                                                        $sellQuote = $row['sellQuote'];
                                                                       
                                                                        $app = doubleval($sellQuote) / doubleval($buyQuote);
                                                                        
                                                                        $buyTime = strtotime($buyDate);
                                                                        $sellTime = strtotime($sellDate);
                                                                        
                                                                        $exp = 1/ ((($sellTime - $buyTime) / (24 * 60 * 60)) / 365);

                                                                        $rate = round((pow($app, $exp) - 1) * 100, 2);
                                                                        
                                                                        $returns[$individual . " / " . $stock] = $rate;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } 
                                    }
                                }
                                
                                arsort($returns);
                                    
                                $idx = 1;
                                foreach ($returns as $key => $value) { 
                                    echo "<tr><td>" . $idx . "</td>";
                                    
                                    $dataSplit = explode(" / ", $key);
                                    
                                    echo "<td>" . $dataSplit[0] . "</td>";
                                    echo "<td>" . $dataSplit[1] . "</td>";
                                    echo "<td>" . $returns[$key] . "%</td></tr>";
                                    $idx++;
                                }
                                    
                                mysqli_close($con);
                            ?>
                        </tbody>
                    </table>
                    
                    <form action="individual.php" method="GET" name="individual">
                        <input type = "submit" value="Go Back" />
                    </form>
                </b>
            <hr>
        </div>
         
    </body>
</html>