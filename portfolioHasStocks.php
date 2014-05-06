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
                                <th>PORTFOLIO NAME</th>
                                <th>INVESTED COMPANY/STOCK NAME</th>
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
                                array_push($entry, "Portfolio Name");
                                array_push($entry, "Invested Company/Stock Name");
                                
                                array_push($data, $entry);
                                
                                if ($_SESSION['all']) {
                                    $query = "SELECT * FROM portfolio";
                                    $portResults = mysqli_query($con, $query);
                                    
                                    if (mysqli_num_rows($portResults)) {
                                        while ($row = mysqli_fetch_array($portResults)) {
                                            $portfolio = $row['Name'];

                                            $query = "SELECT company.name AS name, company.stock_name AS stock_name FROM portfolio, company, portfolio_has_stocks WHERE portfolio.ID = portfolio_has_stocks.portfolio_ID and company.stock_name = portfolio_has_stocks.stock_name and portfolio.name =\"" . $portfolio . "\";";
                                            $results = mysqli_query($con, $query);
                                            
                                            if (mysqli_num_rows($results)) {
                                                while ($row = mysqli_fetch_array($results)) {
                                                    $entry = array();
                                                    array_push($entry, $portfolio);
                                                    array_push($entry, $row["name"] . " / " . $row["stock_name"]);
                                                    
                                                    array_push($data, $entry);
                                                    
                                                    echo "<tr><td>" . $portfolio . "</td>";
                                                    echo "<td>" . htmlentities($row["name"]) . " / " . htmlentities($row["stock_name"]) . "</td></tr>";
                                                }
                                                
                                                mysqli_free_result($results);
                                            }
                                            else {
                                                $entry = array();
                                                array_push($entry, $portfolio);
                                                array_push($entry, "No Data Available");
                                                
                                                array_push($data, $entry);
                                                    
                                                echo "<tr><td>" . $portfolio . "</td>";
                                                echo "<td>No Data Available</td></tr>";
                                            }
                                        }
                                    }
                                }
                                else if ($_SESSION['portfolio'] == "") {
                                    $entry = array();
                                    array_push($entry, "No Portfolio Input");
                                    array_push($entry, "");
                                    
                                    array_push($data, $entry);
                                    
                                    echo "<tr><td>No Portfolio Input</td>";
                                    echo "<td></td></tr>";
                                }
                                else {
                                    $portSplit = explode(",", $_SESSION['portfolio']);
                                    
                                    foreach ($portSplit as $portfolio) {
                                        $portfolio = trim($portfolio);
                                        
                                        $query = "SELECT company.name AS name, company.stock_name AS stock_name FROM portfolio, company, portfolio_has_stocks WHERE portfolio.ID = portfolio_has_stocks.portfolio_ID and company.stock_name = portfolio_has_stocks.stock_name and portfolio.name =\"" . $portfolio . "\";";
                                        $results = mysqli_query($con, $query);
                                        
                                        if (mysqli_num_rows($results)) {
                                            while ($row = mysqli_fetch_array($results)) {
                                                $entry = array();
                                                array_push($entry, $portfolio);
                                                array_push($entry, $row["name"] . " / " . $row["stock_name"]);
                                                
                                                array_push($data, $entry);
                                                    
                                                echo "<tr><td>" . $portfolio . "</td>";
                                                echo "<td>" . htmlentities($row["name"]) . " / " . htmlentities($row["stock_name"]) . "</td></tr>";
                                            }
                                            
                                            mysqli_free_result($results);
                                        }
                                        else {
                                            $entry = array();
                                            array_push($entry, $portfolio);
                                            array_push($entry, "No Data Available");
                                            
                                            array_push($data, $entry);
                                                
                                            echo "<tr><td>" . $portfolio . "</td>";
                                            echo "<td>No Data Available</td></tr>";
                                        }
                                    }
                                }
                                
                                $_SESSION['file'] = "portfolioHasStocks";
                                $_SESSION['data'] = $data;
                                
                                mysqli_close($con);
                            ?>
                        </tbody>
                    </table>
                    
                    <form action="csvExport.php" method="POST">
                        <input type = "Submit" value="Export into CSV" /> 
                    </form>
                    <br>
                    <form action="portfolio.php" method="GET" name="individual">
                        <input type = "submit" value="Go Back" />
                    </form>
                </b>
            <hr>
        </div>
         
    </body>
</html>