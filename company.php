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
                    <form action="companyList.php" method="GET" name="companyList">
                        <input type = "submit" value="Generate List of Companies/Stocks" />
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
            <b>
                <form action="quoteIncDec.php" method="POST" name="quoteIncDec">
                    <input type="submit" name="quoteInc" value="Display Companies with Annual Quote Increase" /><br><br>
                    <input type="submit" name="quoteDec" value="Display Companies with Annual Quote Decrease" /><br><br>
                </form>
            </b>
        </div>
         
    </body>
</html>