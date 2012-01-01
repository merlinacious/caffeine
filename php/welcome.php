<?php

error_reporting(0); 
include('config.php');
include('lock.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Bulk Fund </title>
    <link href="stylesheets/reset.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="stylesheets/style.css" media="screen" rel="stylesheet" type="text/css" />
</head>


<?php $maxdate = mysql_query("SELECT max(datestamp) FROM history") or die(mysql_error())?>

<body>
	<div id="header">
		<div id="user_info">
			Welcome, </strong><?php echo $login_session; ?></strong> <span>|</span> <a href="logout.php">log out</a>
		</div>
		<?php
		// outputs e.g.  somefile.txt was last modified: December 29 2002 22:16:23.

		//$filename = '/hedge/lastupdate';

		if (file_exists($filename)) {
			echo $filename;
			echo "$filename was last modified: " . date ("F d Y H:i:s.", filemtime($filename));
		}
		?>

		<h1><font COLOR="#FFC401"><b>CA</b></font>f<font COLOR="#FFC401"><b>FEI</b></font>n<font COLOR="#FFC401"><b>E</b></font> - <font COLOR="#FFC401"><b>C</b></font>ollective <font COLOR="#FFC401"><b>A</b></font>utomated <font COLOR="#FFC401"><b>F</b></font>inanc<font COLOR="#FFC401"><b>E</b></font> <font COLOR="#FFC401"><b>I</b></font>d<font COLOR="#FFC401"><b>E</b></font>as</h1>
			<ul id="menu">
				<li class="active"><a href="#">Bulk Deals</a></li>
				<li><a href="#">Future Project2</a></li>
				<li><a href="#">Future Project3</a></li>
			</ul>		
		
		<div style="clear:both;" /></div>
	</div>
	
	<div id="content">
		<aside>
			<div id="sidebar">
				<h1>Database Functions</h1>
				<ul>
					<li><a href="#">Data model diagram</a></li>
					<li><a href="#">Query for bulk by Client Name</a></li>
					<li><a href="#">Query for bulk by Latest Date</a></li>
					<li><a href="#">Query for prices by Client Name</a></li>
					<li><a href="#">Query for prices by Latest Date</a></li>
					<li><a href="#">Query3</a></li>
				</ul>
				<h1>Admin Functions</h1>
				<ul>
					<li><a href="http://www.nse-india.com/content/equities/bulk.csv">Check data @NSE</a></li>
					<li><a href="/hedge/scripts/process.pl" target="_blank">Click to Process latest bulk file from NSE</a></li>
					<li><a href="/hedge/scripts/forceprocess.pl" target="_blank">Click to FORCE process latest bulk file from NSE</a></li>
					<li>FORCE process is for Namit only</li>
					<!-- *** Don't use the FORCE process below unless you have checked the data @ NSE to be the right date *** */
					*** If the data @ NSE  is the latest date & it is not present in our DB, then hit PROCESS *** 
					*** FORCE PROCESS is only for Namit to use in case something goes wrong *** -->
				</ul>
			</div>		
		</aside>
		<article>
			<div id="main">
				<h1>Bulk Deals</h1>
				<h2><a href="all.php">View by Client Name</a></h2>
				<h2><a href="database.php">View by Latest Date</a> 
				<?php
				// Make a MySQL Connection
				$query = "SELECT MAX(datestamp) FROM history"; 
				$result = mysql_query($query) or die(mysql_error());
				// Print out result
				while($row = mysql_fetch_array($result)){
					echo "The max date in the HISTORY table is " .$row['MAX(datestamp)'];
					echo "<br />";
				}
				?></h2>
				<h2><a href="search.php">Search for Client Name</a></h2>
				
				<h1>Daily Prices</h1>
				<h2><a href="#">View by Latest Date</a> 
				<?php
				// Make a MySQL Connection
				$query = "SELECT MAX(timestamp) FROM historicnseprices"; 
				$result = mysql_query($query) or die(mysql_error());
				// Print out result
				while($row = mysql_fetch_array($result)){
					echo "The max date in the HISTORICNSEPRICES table is " .$row['MAX(timestamp)'];
					echo "<br />";
				}
				?></h2>
				
			</div>
		</article>
	</div>
	
	<footer>
		<div id="footer">
			2012 Current Members - Namit, Aman, Kai
		</div>
	</footer>

</body>
</html>