<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>Info for: <?php echo htmlentities($_GET["user"])."<br/>";?>
        <?php
			$con = mysqli_connect("localhost", "", "");
				if (!$con) {
					exit('Connect Error (' . mysqli_connect_errno() . ')' . mysqli_connect_error());
				}
				
			mysqli_set_charset($con, 'utf-8');
			
			mysqli_select_db($con, "test_finance");
			
			$user = mysqli_real_escape_string($con, htmlentities($_GET["user"]));
			
			$individual = mysqli_query($con, "SELECT * FROM individuals WHERE Name='" . $user . "'");
			
			if(mysqli_num_rows($individual) < 1) {
				exitexit("The person " . htmlentities($_GET["user"]) . " is not found. Please check the spelling and try again");
			}
			
			$row = mysqli_fetch_row($individual);
			$individualID = $row[0];
			mysqli_free_result($individual);
        ?>
		<table border="black">
			<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Cash</th>
			</tr>
			
			<?php
				$result = mysqli_query($con, "SELECT ID, Name, Cash FROM individuals WHERE ID=" . $individualID);
				while ($row = mysqli_fetch_array($result)) {
					echo "<tr><td>" . htmlentities($row["ID"]) . "</td>";
					echo "<td>" . htmlentities($row["Name"]) . "</td>";
					echo "<td>" . htmlentities($row["Cash"]) . "</td></tr>\n";
				}
				mysqli_free_result($result);
				mysqli_close($con);
			?>
		</table>
    </body>
</html>