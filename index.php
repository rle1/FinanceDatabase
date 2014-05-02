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
                  <li><a href="dataimport.php">DATA IMPORT</a></li>
                  <li><a href="dataexport.php">DATA EXPORT</a></li>
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
               - Calculate an individual's total return<br>
               - Calculate a fund's total return<br>
               - Determine a Company's quote/volume/day_hi/day_lo at a certain time<br>
               - How much money an individual/portfolio has invested in a company/portfolio ordered by ID number<br>
               - What companies/portfolios an individual/portfolio is invested in at a certain time<br>
               - How much a user's investment has appreciated or depreciated at time of query<br>
            </b>
         </p>
         <hr>
      </div>
        
   </body>
</html>