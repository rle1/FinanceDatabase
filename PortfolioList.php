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
            <h2><b>LIST OF PORTFOLIOS</b></h2>
            <hr>
	 		 <table class = "table table-striped">
				<tr>
					<th>Portfolio Name</th>
				</tr>
				<?php
					$con = mysqli_connect("localhost", "user1", "pass1");
		 		 	if(!$con){
		 		 		exit('Connect Error (' . mysqli_connect_errno() . ')' . mysqli_connect_error());
		 		 	}

		 		 	mysqli_set_charset($con, 'utf-8');
					
					mysqli_select_db($con, "finance");
					$result = mysqli_query($con, "SELECT * FROM portfolio");

					while($row = mysqli_fetch_array($result)){
						echo "<tr><td>" . htmlentities($row["Name"]) . "</td></tr>";
					}
					mysqli_free_result($result);
					mysqli_close($con);
				?>
			</table>
			<hr>
				<form action="company.php" method="GET">
					<input type = "Submit" value="Go Back" /> 
				</form>
			<hr>
 		</div>
	</body>
</html>