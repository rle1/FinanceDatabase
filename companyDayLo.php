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
                       <li class="active"><a href="company.php">COMPANY</a></li>
                       <li><a href="individual.php">INDIVIDUAL</a></li>
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
            <h2><b>LIST OF COMPANIES/STOCKS</b></h2>
            <hr>
                <b>
                    <form action="companyList.php" method="GET" name="companyList">
                        <input type = "submit" value="Generate List of Companies/Stocks" />
                    </form>
                </b>
            <hr>
            <h2><b>COMPANY INFORMATION</b></h2>
            <hr>
            <b>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>COMPANY NAME</th>
                            <th>STOCK NAME</th>
                            <th>DATE</th>
                            <th>DAY_LO</th>
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
                            array_push($entry, "Company Name");
                            array_push($entry, "Stock Name");
                            array_push($entry, "Date");
                            array_push($entry, "Day_lo");
                            
                            array_push($data, $entry);
                            
                            if ($_SESSION['all']) {
                                $query = "SELECT * FROM company";
                                $compResults = mysqli_query($con, $query);
                                
                                if (mysqli_num_rows($compResults)) {
                                    while ($row = mysqli_fetch_array($compResults)) {
                                        $company = $row['Name'];
                                        $stock_name = $row['Stock_name'];
                                                    
                                        $query = "SELECT quotes.Day_lo AS day_lo, quotes.date AS date FROM company, quotes WHERE company.name = \"" . $company . "\" and quotes.Stock_name = \"" . $stock_name . "\" and quotes.date >= \"" . $_SESSION['startDate'] . "\" and quotes.date <= \"" . $_SESSION['endDate'] . "\"";
                                        
                                        $results = mysqli_query($con, $query);
                                           
                                        if (mysqli_num_rows($results)) {
                                            while($row = mysqli_fetch_array($results)){
                                                
                                                $entry = array();
                                                array_push($entry, $company);
                                                array_push($entry, $stock_name);
                                                array_push($entry, $row["date"]);
                                                array_push($entry, $row["day_lo"]);
                                                
                                                array_push($data, $entry);
                                                
                                                echo "<tr><td>" . $company . "</td>";
                                                echo "<td>" . $stock_name . "</td>";
                                                echo "<td>" . htmlentities($row["date"]) . "</td>";
                                                echo "<td>$" . htmlentities($row["day_lo"]) . "</td></tr>";
                                            }
                                            mysqli_free_result($results);
                                        }
                                        else {
                                            
                                            $entry = array();
                                            array_push($entry, $company);
                                            array_push($entry, $stock_name);
                                            array_push($entry, "-");
                                            array_push($entry, "No Data Available");
                                            
                                            array_push($data, $entry);
                                            
                                            echo "<tr><td>" . $company . "</td>";
                                            echo "<td>" . $stock_name . "</td>";
                                            echo "<td>-</td>";
                                            echo "<td>No Data Available</td></tr>";
                                        }
                                    }
                                }
                            }
                            else if ($_SESSION['stock'] == "") {
                                echo "<tr><td>No Company/Stock Input</td>";
                                echo "<td></td><td></td><td></td></tr>";
                            }
                            else {
                                $stockSplit = explode(",", $_SESSION['stock']);
                                    
                                foreach ($stockSplit as $stock) {
                                    $stock = trim($stock);
                                    
                                    $query = "SELECT company.name as company, quotes.Day_lo AS day_lo, quotes.date AS date FROM company, quotes WHERE company.stock_name = \"" . $stock . "\" and quotes.Stock_name = \"" . $stock . "\" and quotes.date >= \"" . $_SESSION['startDate'] . "\" and quotes.date <= \"" . $_SESSION['endDate'] . "\"";
                                        
                                    $results = mysqli_query($con, $query);
                                       
                                    if (mysqli_num_rows($results)) {
                                        while($row = mysqli_fetch_array($results)){
                                            $entry = array();
                                            array_push($entry, $row["company"]);
                                            array_push($entry, $stock);
                                            array_push($entry, $row["date"]);
                                            array_push($entry, $row["day_lo"]);
                                            
                                            array_push($data, $entry);
                                            
                                            echo "<tr><td>" . htmlentities($row["company"]). "</td>";
                                            echo "<td>" . $stock . "</td>";
                                            echo "<td>" . htmlentities($row["date"]) . "</td>";
                                            echo "<td>$" . htmlentities($row["day_lo"]) . "</td></tr>";
                                        }
                                        mysqli_free_result($results);
                                    }
                                    else {
                                        $entry = array();
                                        array_push($entry, "-");
                                        array_push($entry, $stock);
                                        array_push($entry, "-");
                                        array_push($entry, "No Data Available");
                                        
                                        array_push($data, $entry);
                                        
                                        echo "<tr><td>-</td>";
                                        echo "<td>" . $stock . "</td>";
                                        echo "<td>-</td>";
                                        echo "<td>No Data Available</td></tr>";
                                    }
                                }
                            }
                            
                            $_SESSION['file'] = "day_lo";
                            $_SESSION['data'] = $data;
                            
                            mysqli_close($con);
                        ?>
                    </tbody>
                </table>
                
                <form action="csvExport.php" method="POST">
                    <input type = "Submit" value="Export into CSV" /> 
                </form>
                <br>
                <form action="company.php" method="GET">
                    <input type = "Submit" value="Go Back" /> 
                </form>
            </b>
            <hr>
        </div>
         
    </body>
</html>