<?php

mysql_connect("localhost", "creativeminds", "c1sc0w0rks321") or die(mysql_error());
mysql_select_db("creativeminds_") or die(mysql_error());


$symbol=array();


$sql="select * from position";


$result = mysql_query("$sql");
while ($row = mysql_fetch_assoc($result)) {
 $key=$row['symbol'];
 
 $symbol["$key"] = $row['symbol'];
 $price["$key"] =$row['price'];
 $quantity["$key"]=$row['quantity'];
}

$parm=implode("+",$symbol);

/**
* Initialize the cURL session
*/
$ch = curl_init();
/**
* Set the URL of the page or file to download.
*/
curl_setopt($ch, CURLOPT_URL,'http://finance.yahoo.com/d/quotes.csv?x=ns&s='.$parm.'&f=sl1');



/**
* Ask cURL to return the contents in a variable
* instead of simply echoing them to the browser.
*/
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
/**
* Execute the cURL session
*/
$contents = curl_exec ($ch);
/**d
* Close cURL session
*/
curl_close ($ch);


print "$contents";









print "$parm";

foreach ($symbol as $key)
{

print "$key,$price[$key],$price[$key] <br>";
}



?>

