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
                                <th>TOTAL INVESTMENTS</th>
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
                                array_push($entry, "Total Investments");
                                
                                array_push($data, $entry);
                                
                                if ($_SESSION['all']) {
                                    $query = "SELECT * FROM individual";
                                    $indResults = mysqli_query($con, $query);
                                    
                                    $invests = array();
                                    $tableEntries = array();
                                    
                                    if (mysqli_num_rows($indResults)) {
                                        while ($row = mysqli_fetch_array($indResults)) {
                                            $individual = $row['Name'];
                               
                                            $totalInvests = 0;
                                           
                                            $query = "SELECT individual_has_portfolios.money_invested AS invest FROM individual, individual_has_portfolios WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_has_portfolios.individual_ID";
                                            
                                            $results = mysqli_query($con, $query);
                                            
                                            if (mysqli_num_rows($results)) {
                                                while ($row = mysqli_fetch_array($results)) {
                                                    $totalInvests += $row['invest'];
                                                }
                                                mysqli_free_result($results);
                                            }
                                            
                                            $query = "SELECT individual_has_stocks.money_invested AS invest FROM individual, individual_has_stocks WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_has_stocks.individual_ID";
                                            
                                            $results = mysqli_query($con, $query);
                                            
                                            if (mysqli_num_rows($results)) {
                                                while ($row = mysqli_fetch_array($results)) {
                                                    $totalInvests += $row['invest'];
                                                }
                                                mysqli_free_result($results);
                                            }
                                            
                                            $invests[$individual] = $totalInvests;
                                            
                                            $tableEntries[$individual] = "<td>" . $individual . "</td>";
                                            $tableEntries[$individual] .= "<td>$" . $totalInvests . "</td></tr>";
                                        }
                                    }
                                    
                                    arsort($invests);
                                    
                                    $idx = 1;
                                    foreach ($invests as $key => $value) {
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
                                    echo "<td></td></tr>";
                                }
                                else {
                                    
                                    $invests = array();
                                    $tableEntries = array();
                                    
                                    $indSplit = explode(",", $_SESSION['individual']);
                                    
                                    foreach ($indSplit as $individual) {
                                        $individual = trim($individual);
                                        
                                        $totalInvests = 0;
                                               
                                        $query = "SELECT individual_has_portfolios.money_invested AS invest FROM individual, individual_has_portfolios WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_has_portfolios.individual_ID";
                                        
                                        $results = mysqli_query($con, $query);
                                        
                                        if (mysqli_num_rows($results)) {
                                            while ($row = mysqli_fetch_array($results)) {
                                                $totalInvests += $row['invest'];
                                            }
                                            mysqli_free_result($results);
                                        }
                                        
                                        $query = "SELECT individual_has_stocks.money_invested AS invest FROM individual, individual_has_stocks WHERE individual.name = \"" . $individual . "\" and individual.ID = individual_has_stocks.individual_ID";
                                        
                                        $results = mysqli_query($con, $query);
                                        
                                        if (mysqli_num_rows($results)) {
                                            while ($row = mysqli_fetch_array($results)) {
                                                $totalInvests += $row['invest'];
                                            }
                                            mysqli_free_result($results);
                                        }
                                        
                                        $invests[$individual] = $totalInvests;
                                        
                                        $tableEntries[$individual] = "<td>" . $individual . "</td>";
                                        $tableEntries[$individual] .= "<td>$" . $totalInvests . "</td></tr>";
                                    }
                                    
                                    arsort($invests);
                                    
                                    $idx = 1;
                                    foreach ($invests as $key => $value) {
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
                                
                                $_SESSION['file'] = "individualTotalInvests";
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