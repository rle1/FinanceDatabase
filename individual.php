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
                <form action="individualInfo.php" method="POST" name="individualInfo">
                    Individual Name(s): <input type="text" name="individual" value="" size="80" placeholder="Multiple entries, separate by commas"/> &nbsp <input type="checkbox" name="compareAll" /> &nbsp Compare All<br><br><br>
                    
                    <input type="submit" name="stocks" value="Generate List of Invested Companies/Stocks" /> &nbsp&nbsp <input type="submit" name="portfolios" value="Generate List of Invested Portfolios" /><br><br>
                    <input type="submit" name="returns" value="Calculate Total Returns" /> &nbsp&nbsp <input type="submit" name="annualReturn" value="Calculate Annualized Rate of Return" > &nbsp&nbsp <input type="submit" name="investments" value="Calculate Total Investments" /> &nbsp&nbsp <input type="submit" name="worth" value="Calculate Final Net Worth" />
                </form>
            </b>
            <hr>
            <b>
                <form action="individualAppDep.php" method="POST" name="individualAppDep">
                    Individual Name(s): <input type="text" name="individual" value="" size="80" placeholder="Multiple entries, separate by commas"/> &nbsp <input type="checkbox" name="compareAll" /> &nbsp Compare All<br><br>
                    Date: <input type="date" name="date" required="required"/><br><br>
                    <input type="submit" name="appDep" value="Calculate Appreciation/Depreciation Factor" /><br><br>
                </form>
            </b>
        </div>
         
    </body>
</html>