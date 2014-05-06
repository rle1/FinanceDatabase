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
                            <th>COMPANY/STOCK WITH ANNUAL QUOTE DECREASE</th>
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
                            
                            $query = "SELECT * FROM company";
                            $compResults = mysqli_query($con, $query);
                            
                            $list = array();
                            $check = array();
                            $index = 0;
                            if (mysqli_num_rows($compResults)) {
                                while ($row = mysqli_fetch_array($compResults)) {
                                    $company = $row['Name'];
                                    $stock_name = $row['Stock_name'];

                                    $query = "SELECT quote FROM quotes WHERE quotes.stock_name = \"" . $stock_name . "\" and date LIKE '20%-01-03'";
                                    $quoteResults = mysqli_query($con, $query);
                                 
                                    if (mysqli_num_rows($quoteResults)) {
                                        while ($row = mysqli_fetch_array($quoteResults)) {
                                           
                                            array_push($check, $row['quote']);
                                        }
                                    }
                                    
                                    $default = $check;
                                    rsort($check);
                                    
                                    $flag = true;
                                    $idx = 0;
                                    foreach ($check as $val) {
                                        if ($val != $default[$idx]) {
                                            $flag = false;
                                            break;
                                        }
                                        $idx++;
                                    }
                                    
                                    if ($flag) {
                                        array_push($list, $company . " / " . $stock_name);
                                    }
                                    
                                    $check = array();
                                }
                            }
                            
                            foreach ($list as $val) {
                                echo "<tr><td>" . $val . "</td></tr>";
                            }
                            
                            mysqli_close($con);          
                        ?>
                    </tbody>
                </table>
                
                <form action="company.php" method="GET">
                    <input type = "Submit" value="Go Back" /> 
                </form>
                <br>
            </b>
        </div>
         
    </body>
</html>