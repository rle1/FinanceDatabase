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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>COMPANY NAME</th>
                                <th>STOCK NAME</th>
                            </tr>
                        </thead>
                  
                        <tbody>
                            <?php
                                $con = mysqli_connect("localhost", "user1", "pass1");
                                if(!$con){
                                    exit('Connect Error (' . mysqli_connect_errno() . ')' . mysqli_connect_error());
                                }
                
                                mysqli_set_charset($con, 'utf-8');
                                mysqli_select_db($con, "finance");
                                
                                $result = mysqli_query($con, "SELECT * FROM company");
                
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr><td>" . htmlentities($row["Name"]) . "</td>";
                                    echo "<td>" . htmlentities($row["Stock_name"]) . "</td></tr>";
                                }
                                mysqli_free_result($result);
                                mysqli_close($con);
                            ?>
                        </tbody>     
                    </table>
                   
                    <br>
                    <form action="company.php" method="GET">
                        <input type = "Submit" value="Go Back" /> 
                    </form>
                </b>
            <hr>
            <h2><b>COMPANY INFORMATION</b></h2>
            <hr>
            <b>
                <form action="companyInfo.php" method="POST" name="companyInfo">
                    Stock Name: <input type="text" name="stock" value="" size="80" placeholder="Multiple entries, separate by commas"/> &nbsp <input type="checkbox" name="compareAll" /> &nbsp Compare All<br><br>
                    Start Date: <input type="date" name="startDate" required="required"/> &nbsp&nbsp End Date: <input type="date" name="endDate" required="required"/>
                    <br><br><br>
                    <input type="submit" name="quote" value="Display Quote" /> &nbsp&nbsp <input type="submit" name="day_hi" value="Display Day Hi" /> &nbsp&nbsp <input type="submit" name="day_lo" value="Display Day Lo" /> &nbsp&nbsp <input type="submit" name="volume" value="Display Volume" />
                </form>
            </b>
            <hr>
        </div>
         
    </body>
</html>