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
                  <li class="active"><a href="index.php">HOME</a></li>
                  <li><a href="dataImport.php">DATA IMPORT</a></li>
                  <li><a href="company.php">COMPANY</a></li>
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
         <h2><b>ABOUT</b></h2>
         <hr>
         <p>
            <b>
               CMSC424 0101 - Database Design Project - Stocks<br>
               Developers: Richard Le, Philip Phlek, Anh Tran<br><br>
               The database keeps track of the following types of information/entities:<br>
               <div class="pad">
                  - Companies: descriptive information about a company<br>
                  - Quotes: the share price of a company at a specific point in time<br>
                  - Portfolios: a set of stocks/cash, and a percentage of each<br>
                  - Individuals: an individual can be thought of as a portfolio that is not contained by any other portfolio<br>
                  - Activity: historical record of portfolio activity
               </div>
            </b>
         </p>
         <hr>
         <h2><b>FUNCTIONALITY PROVIDED</b></h2>
         <hr>
            <p>
            <b>
               - Generate List of Companies/Stocks<br>
               - Generate List of Individuals<br>
               - Generate List of Portfolios<br>
               <br>
               - Display Quotes of Companies for specified date range<br>
               - Display Day Hi of Companies for specified date range<br>
               - Display Day Lo of Companies for specified date range<br>
               - Display Volume of Companies for specified date range<br>
               <br>
               - Display list of Individual's current invested Companies/Stocks<br>
               - Display list of Individual's current invested Portfolios<br>
               - Calculate Total Returns for Individuals<br>
               - Calculate Total Investments for Individuals<br>
               - Calculate Final Net Worth for Individuals<br>
               - Calculate Appreciation/Depreciation Factor for Individuals' Stocks/Portfolios for specified date range<br>
               <br>
               - Display list of Portfolio's current invested Companies/Stocks<br>
               - Calculate Total Returns for Portfolios<br>
               - Calculate Total Investments for Portfolios<br>
               - Calculate Final Net Worth for Portfolios<br>
               - Calculate Appreciation/Depreciation Factor for Portfolios' Stocks for specified date range<br>
               <br>
               - CSV export of any data behavior display/calculation<br>
               - Rankings for calculations on behaviors for individuals/portfolios
            </b>
         </p>
         <hr>
      </div>
        
   </body>
</html>