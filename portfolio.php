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
                <form action="portfolioInfo.php" method="POST" name="portfolioInfo">
                    Portfolio Name(s): <input type="text" name="portfolio" value="" size="80" placeholder="Multiple entries, separate by commas" /> &nbsp <input type="checkbox" name="compareAll" /> &nbsp Compare All<br><br><br>
                    <input type="submit" name="stocks" value="Generate List of Invested Companies/Stocks" /><br><br>
                    <input type="submit" name="returns" value="Calculate Total Returns" /> &nbsp&nbsp
                    <input type="submit" name="investments" value="Calculate Total Investments" /> &nbsp&nbsp <input type="submit" name="worth" value="Calculate Final Net Worth" />
                </form>
            </b>
            <hr>
            <b>
                <form action="portfolioAppDep.php" method="POST" name="portfolioAppDep">
                    Portfolio Name(s): <input type="text" name="portfolio" value="" size="80" placeholder="Multiple entries, separate by commas" /> &nbsp <input type="checkbox" name="compareAll" /> &nbsp Compare All<br><br>
                    Date: <input type="date" name="date" required="required"/><br><br>
                    <input type="submit" name="appDep" value="Calculate Appreciation/Depreciation Factor" /><br><br>
                </form>
            </b>
        </div>
         
    </body>
</html>