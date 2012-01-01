<?php
$search=$_REQUEST['search'];

print "<h1>Search</h1>";
print "<form name='searchform' action='search.php' method='get'>";

print "<input type='text' name='search'>";

print "<input type='submit'>";
print "</form>";

 if ((isset($search)))
{

include('config.php');


$query="select * from history where client like '%$search%'";

$knownsymbol=array();
$knownclient=array();
$symbolquantity=array();


$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {

 $symbol=$row['symbol'];
 $client=$row['client'];
 if (defined($knownsymbol["$symbol"]))
 {
  $seen=1;
  $knownclient["$client"]="1";
  $symbolquantity["$symbol"] += ( $row["action"] *  $row["quantity"] ) ;
 }
 else if ($row['action'] > 0) #if we have not seen this symbol for this client, only count if it is a buy
 {
  $seen=1;
  $knownsymbol["$symbol"]=1;
  $knownclient["$client"]="1";
  $symbolquantity["$symbol"] += ( $row["action"] *  $row["quantity"] ) ;
 }

}

print "<table border='1'> ";
print "<tr><td><u><b>Client</u></b></td></tr> ";
ksort($knownclient);
foreach ($knownclient as $key => $value)
{
 $clientnamea=preg_replace('/"/',"",strtoupper("$key"));
 $clientname=preg_replace('/^\ +/',"",$clientnamea);

 print "<tr><td><a href='profile.php?client=$key'>$clientname</a></td></tr> \n";
}
print "</table>";

















}





?>
