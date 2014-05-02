<!DOCTYPE html>
 <html>
 <head>
     <meta charset="utf-8">
     <title>Getting started with Bootstrap</title>
     <link rel="stylesheet" href="http://flip.hr/css/bootstrap.min.css">

     <script>
     function validateCompanyQuote() {
        var x=document.forms["companyQuote"]["company"].value;
        if (x==null || x=="") {
          alert("Company's name must be filled out");
          return false;
        }
      }
     </script>

 </head>
 <body>
         
  <div class="container">
         
         <div class="hero-unit">
         <h1 align="center">Finance Database</h1>
         
         </div><!-- .hero-unit -->
        
 
  </div><!-- .container -->
 
   <form action="CompanyQuote.php" method="GET" name="companyQuote">
      <label> Show info company's quote: </label>
      <input type="text" name="company" value="" />
      <input type="submit" value="Go" />
   </form>

   <form action="IndividialInvestment.php" method="GET" name="invdividualInvestment">
      <label>Show investment appreciation/depreciation for (individual): </label> 
      <input type="text" name="individual" value=""/>
      <input type="submit" value="Go" />
   </form>

   <form action="PortfolioInvestment.php" method="GET" name="portfolioInvestment">
      <label> Show investment appreciation/depreciation for (portfolio):</label>
      <input type="text" name="portfolio" value=""/>
      <input type="submit" value="Go" />
   </form>

   <form action="ListCompanies.php" method="GET" name="listCompanies">
      <label> Show list of Companies </label>
      <input type ="text" name="company" value=""/>
      <input type = "submit" value="Go" />
   </form>

   <form action="StockVolume.php" method="GET" name="stockVolume">
      <label> Show stock volume for (company): </label>
      <input type="text" name="company" value=""/>
      <input type="submit" value="Go" />
   </form>

   <form action="IndividualInvested.php" method="GET" name="individualInvested">
      <label> Show investments made for (individual): </label>
      <input type="text" name="individual" value=""/>
      <input type="submit" value="Go"/>
   </form>

   <form action="PortfolioInvested.php" method="GET" name="portfolioInvested">
      <label> Show investments made for (individual): </label>
      <input type="text" name="portfolio" value=""/>
      <input type="submit" value="Go"/>
   </form>

   <form action="InvestmentsInCompany.php" method="GEt" name="investmentsCompany">
      <label> Show all individuals/portfolios investments made for (company): </label>
      <input type="text" name="investments" value=""/>
      <input type="submit" value="Go"/>
   </form>
     
 </body>
</html>