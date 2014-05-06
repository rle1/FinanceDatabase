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
                  <li><a href="portfolio.php">PORTFOLIO</a></li>
				  <li class="active"><a href="mysteryQuery.php">MYSTERY QUERY</a></li>
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
            <h2><b>MYSTERY QUERY</b></h2>
            <hr>
			<b>
				<form action="mysteryQueryProcess.php" method="POST">
					<input type = "Submit" value="Process Mystery Query" /> 
				</form>
			</b>
			<hr>
 		 </div>
	</body>
</html>