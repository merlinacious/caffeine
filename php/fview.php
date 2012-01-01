<?php


error_reporting(0); 
mysql_connect('mysql.kathabimb.com', 'kathabimb', 'vesit!!roxx') or die(mysql_error());
mysql_select_db("kathabimb1") or die(mysql_error());





$aquery="select * from history";

$aknownsymbol=array();
$aknownclient=array();
$asymbolquantity=array();


$aresult = mysql_query($aquery);
while ($arow = mysql_fetch_assoc($aresult)) {

 $asymbol=$arow['symbol'];
 $aclient=$arow['client'];
 if (defined($aknownsymbol["$asymbol"]))
 {
  $aseen=1;
  $aknownclient["$aclient"]="1";
  $asymbolquantity["$asymbol"] += ( $arow["action"] *  $arow["quantity"] ) ;
 }
 else if ($arow['action'] > 0) #if we have not seen this symbol for this client, only count if it is a buy
 {
  $aseen=1;
  $aknownsymbol["$asymbol"]=1;
  $aknownclient["$aclient"]="1";
  $asymbolquantity["$asymbol"] += ( $arow["action"] *  $arow["quantity"] ) ;
 }

}

ksort($aknownclient);
foreach ($aknownclient as $key => $value)
{

 $clientnamea=preg_replace('/"/',"",strtoupper("$key"));
 $clientname=preg_replace('/^\ +/',"",$clientnamea);

$input=$clientname;
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
$totalbuy=array();
$totalsell=array();

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
  $alltransactions["$symbol"] .= "<tr><td>$datestamp</td><td>$symbol</td><td>$actionname</td><td>&#x20a8; " . number_format("$price", 2, '.', '') . "</td><td>" . number_format("$quantity", 2, '.', '') . "</td></tr>";  
  $symbolquantity["$symbol"] += ( $row["action"] *  $row["quantity"] ) ;
  if (preg_match("/BUY/",$actionname)) { $averagebuy["$symbol"] += $row["price"]; $buycount["$symbol"]++; $totalbuy["$symbol"]+= $row["quantity"]; }
  if (preg_match("/SELL/",$actionname)) { $averagesell["$symbol"] += $row["price"]; $sellcount["$symbol"]++; $totalsell["$symbol"] += $row["quantity"]; }
 }
 else if (preg_match("/BUY/",$actionname)) #if we have not seen this symbol for this client, only count if it is a buy
 { 
  $seen=1;
  $alltransactions["$symbol"] .= "<tr><td>$datestamp</td><td>$symbol</td><td>$actionname</td><td>&#x20a8; " . number_format("$price", 2, '.', '') . "</td><td>" . number_format("$quantity", 2, '.', '') . "</td></tr>";  
  $knownsymbol["$symbol"]=$symbol;
  if (!(defined($startdatesymbol["$symbol"]))) { $startdatesymbol["$symbol"]=$datestamp; }
  $symbolquantity["$symbol"] += ( $row["action"] *  $row["quantity"] ) ;
  if (preg_match("/BUY/",$actionname)) { $averagebuy["$symbol"] += $row["price"]; $buycount["$symbol"]++; $totalbuy["$symbol"]+= $row["quantity"]; }
  if (preg_match("/SELL/",$actionname)) { $averagesell["$symbol"] += $row["price"]; $sellcount["$symbol"]++; $totalsell["$symbol"] += $row["quantity"];}
 }

  
 $enddatesymbol["$symbol"]=$datestamp; 
}

#print_r($symbolquantity);
#print_r($alltransactions);
#print_r($sellcount);


print "<a name='top'>&nbsp;</a>";
print "<h3>Profile for: $client </h3>";
if ($seen)
{
print "Summary of Position<br>";

print "<table border='1'> ";
print "<tr>";
print "<td><u><b>Symbol</u></b></td>";
print "<td><u><b>Total Shares</u></b></td>";
print "<td><u><b>Start Trade</b></u></td>";
print "<td><u><b>End Trade</b></u></td>";
print "<td><u><b>Sell Count</u></b></td>";
print "<td><u><b>Avg. Sell</b></u></td>";
print "<td><u><b>Total Sell</b></u></td>";
print "<td><u><b>Buy Count</b></u></td>";
print "<td><b><u>Avg. Buy</u></b></td>";
print "<td><u><b>Total Buy</b></u></td>";
print "<td><u><b>Booked PNL</b></u></td>";
print "<td><u><b>Percent Profit</b></u></td>";
print "</tr> ";



foreach ($knownsymbol as $key)
{
 $start=$startdatesymbol["$key"];
 $end=$enddatesymbol["$key"];
 $buy=$buycount["$key"];
 $value=$symbolquantity["$key"];
 $sell=$sellcount["$key"]; 
 $tsell=$totalsell["$key"];
 $tbuy=$totalbuy["$key"];
 $PNL="";
 $percentprofit="";

 if (!(preg_match("/[0-9]/",$totalsell["$key"])))
 { $tsell=0; }

 if (!(preg_match("/[0-9]/",$totalbuy["$key"])))
 { $tbuy=0; }



 if (!(preg_match("/[0-9]/",$sellcount["$key"])))
 { $sell=0; }

  if ($sell > 0 )
  { $asell=round($averagesell["$key"]/$sell,2); } 
  else
  { $sell=0; $asell="N/A"; }

  $abuy=round($averagebuy["$key"]/$buy,2);

$ttsell=$tsell;
$ttbuy=$tbuy;

if($tsell > $tbuy)
{ $ttsell=$tbuy; }

if ($tbuy > $tsell)
{	
$ttbuy=$tsell;}


$PNL=($ttsell*$asell)-($ttbuy*$abuy);
$percentprofit=(($asell-$abuy)/$abuy)*100;




 print "<tr><td><a href='#$key'>" . $key . "</a></td><td>" . number_format("$value", 2, '.', '') . "</td><td>" . $start . "</td><td>" . $end . "</td><td>" . number_format("$sell", 2, '.', '') .  "</td><td>&#x20a8; " . number_format("$asell", 2, '.', '') .  "</td><td>". $tsell . "</td><td>" . number_format("$buy", 2, '.', '') . "</td><td>&#x20a8; " . number_format("$abuy", 2, '.', '') . "</td><td>". $tbuy."</td><td>". number_format("$PNL", 2, '.', ',') ."</td><td>" . number_format("$percentprofit", 2, '.', ',') . "</td></tr> \n";
}
print "</table>";


}
else
{
print "<br><br><br><font color='red'>Historical data only shows sells and no buys, therefore position can not be determined. </font>";
}

}

?>
