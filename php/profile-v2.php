<?php


error_reporting(0); 
mysql_connect("localhost", "creativeminds", "c1sc0w0rks321") or die(mysql_error());
mysql_select_db("creativeminds_") or die(mysql_error());

$input=$_REQUEST['client'];
#$input='C D INTEGRATED SERVICES LTD';
$client=mysql_real_escape_string("$input");
$query="select * from history where client like '$client' ";

$knownsymbol=array();
#$symbolquantity=array();
$startdatesymbol==array();
$enddatesymbol==array();
$symbolquantity=array();
$averagebuy=array();
$averagesell=array();
$buycount=array();
$sellcount=array();
$alltransactions=array();

$seen=0;
#as we do not know the original starting point, for any symbol
#we only start to count once we see a purchase

$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {

 $symbol=$row['symbol'];
 $action=$row['action'];
 $price=$row['price'];
 $quantity=$row['quantity'];
 if (preg_match("/^1/",$action)) { $actionname="BUY"; }
 if (preg_match("/^-1/",$action)) { $actionname="SELL"; }


 $datestamp=$row['datestamp'];
 $price=$row['price'];
 if (preg_match("/[A-Z0-9]/",$knownsymbol["$symbol"]))
 { 
  $seen=1;
  $alltransactions["$symbol"] .= "<tr><td>$symbol</td><td>$actionname</td><td>\$" . number_format("$price") . "</td><td>" . number_format("$quantity") . "</td></tr>";  
  $symbolquantity["$symbol"] += ( $row["action"] *  $row["quantity"] ) ;
  if (preg_match("/BUY/",$actionname)) { $averagebuy["$symbol"] += $row["price"]; $buycount["$symbol"]++; }
  if (preg_match("/SELL/",$actionname)) { $averagesell["$symbol"] += $row["price"]; $sellcount["$symbol"]++; }
 }
 else if (preg_match("/BUY/",$actionname)) #if we have not seen this symbol for this client, only count if it is a buy
 { 
  $seen=1;
  $alltransactions["$symbol"] .= "<tr><td>$symbol</td><td>$actionname</td><td>\$" . number_format("$price") . "</td><td>" . number_format("$quantity") . "</td></tr>";  
  $knownsymbol["$symbol"]=$symbol;
  if (!(defined($startdatesymbol["$symbol"]))) { $startdatesymbol["$symbol"]=$datestamp; }
  $symbolquantity["$symbol"] += ( $row["action"] *  $row["quantity"] ) ;
  if (preg_match("/BUY/",$actionname)) { $averagebuy["$symbol"] += $row["price"]; $buycount["$symbol"]++; }
  if (preg_match("/SELL/",$actionname)) { $averagesell["$symbol"] += $row["price"]; $sellcount["$symbol"]++; }
 }

  
 $enddatesymbol["$symbol"]=$datestamp; 
}

#print_r($symbolquantity);
#print_r($alltransactions);
#print_r($sellcount);


print "<a name='top'>&nbsp;</a>";
print "<br>";
print "<h1>Profile for: $client</h1> ";
print "<br><a href='all.php'>Back</a><br> ";
if ($seen)
{
print "<h2>Terminology </h2>";
print "<font size='2'>";
print "<li>Total Shares - (Total Buy Quantity - Total Sell Quantity";
print "<li>Start Trade - the date of the first buy trade seen for this symbol ";
print "<li>End Trade - the date of the last trade for this symbol ";
print "<li>Sell Count - the number of sell transactions for this client ";
print "<li>Avg. Sell - For only Sell Transactions:  Sum ( Price x Quantity )  / Total Quantity ";
print "<li>Buy Count - the number of buy transactions for this client ";
print "<li>Avg. Buy - For only Buy Transactions:  Sum ( Price x Quantity )  / Total Quantity ";

print "</font>";

print "<br>";
print "<br>";
print "<h2>Summary of Position</h2><br>";

print "<table border='1'> ";
print "<tr>";
print "<td><u><b>Symbol</u></b></td>";
print "<td><u><b>Total Shares</u></b></td>";
print "<td><u><b>Start Trade</b></u></td>";
print "<td><u><b>End Trade</b></u></td>";
print "<td><u><b>Sell Count</u></b></td>";
print "<td><u><b>Avg. Sell</b></u></td>";
print "<td><u><b>Buy Count</b></u></td>";
print "<td><b><u>Avg. Buy</u></b></td>";
print "</tr> ";



foreach ($knownsymbol as $key)
{
 $start=$startdatesymbol["$key"];
 $end=$enddatesymbol["$key"];
 $buy=$buycount["$key"];
 $value=$symbolquantity["$key"];
 $sell=$sellcount["$key"]; 
 if (!(preg_match("/[0-9]/",$sellcount["$key"])))
 { $sell=0; }

  if ($sell > 0 )
  { $asell=round($averagesell["$key"]/$sell,2); } 
  else
  { $sell=0; $asell="N/A"; }

  $abuy=round($averagebuy["$key"]/$buy,2);

 print "<tr><td><a href='#$key'>" . $key . "</a></td><td>" . number_format("$value") . "</td><td>" . $start . "</td><td>" . $end . "</td><td>" . number_format("$sell") .  "</td><td>\$" . number_format("$asell") .  "</td><td>" . number_format("$buy") . "</td><td>\$" . number_format("$abuy") . "</td></tr> \n";
}
print "</table>";

print "<h2>All Transactions ordered by Symbol</h2><br>";

foreach ($alltransactions as $key => $value )
{
print "<a name='$key'>&nbsp;</a><a href='#top'>Top</a>";
print "<table border='1'> ";
print "<tr>";
print "<td><u><b>Symbol</u></b></td>";
print "<td><u><b>Action</u></b></td>";
print "<td><u><b>Price</u></b></td>";
print "<td><u><b>Quantity</u></b></td>";
print "$value \n";
print "</table>";
print "<br>";
}




}
else
{
print "<br><br><br><font color='red'>Historical data only shows sells and no buys, therefore position can not be determined. </font>";
}


print "<br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> 
<br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> 
<br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> 
<br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> 
<br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> 
<br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> 
<br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> 
<br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> 
<br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> 
";

?>
