<?PHP
 
$file="files/bulk2.csv";
$data = file_get_contents($file); // (PHP 4 >= 4.3.0, PHP 5) 

mysql_connect("localhost", "creativeminds", "c1sc0w0rks321") or die(mysql_error());
mysql_select_db("creativeminds_") or die(mysql_error());



#$data = str_replace("\"", "", $data);
$line = explode("\n",$data);

$i=0;



foreach ($line as $value) {
if(!($i==0))
{
	
    
$subject = "abcdef";
$pattern = '/[a-zA-z-0-9]/';


if(preg_match($pattern, $value))
{
$columns = explode("\",",$value);

$date  = explode("-",$columns[0]);

switch ($date[1]) {
    case "Jan":
       $date[1]="1";
	break;
    case "Feb":
         $date[1]="2";
	break;
 case "Mar":
         $date[1]="3";
        break;
 case "Apr":
         $date[1]="4";
        break;
 case "May":
         $date[1]="5";
        break;
 case "Jun":
         $date[1]="6";
        break;
 case "Jul":
         $date[1]="7";
        break;
 case "Aug":
         $date[1]="8";
        break;
 case "Sep":
         $date[1]="9";
        break;
 case "Oct":
         $date[1]="10";
        break;
 case "Nov":
         $date[1]="11";
        break;
 case "Dec":
         $date[1]="12";
        break;

}

$dateh[0]=$date[2];
$dateh[1]=$date[1];
$dateh[2]=$date[0];


$columns[0]=implode("-",$dateh);

$values="'$columns[0]','$columns[1]','$columns[2]','$columns[3]','$columns[4]','$columns[5]','$columns[6]','$columns[7]'";

$values = str_replace("\"", "", $values);

$pattern = '/SELL/';

if(preg_match($pattern, $columns[4]))
{

$sql = "INSERT INTO SELL (DATE, SYMBOL,SECURITY_NAME,CLIENT_NAME,BUY_SELL,QUANTITY_TRADED,TRADE_PRICE_WEIGHT_AVG,REMARK) VALUES($values);";
}else
{
$sql = "INSERT INTO BUY (DATE, SYMBOL,SECURITY_NAME,CLIENT_NAME,BUY_SELL,QUANTITY_TRADED,TRADE_PRICE_WEIGHT_AVG,REMARK) VALUES($values);";
}
print "$sql <br>";

mysql_query("$sql") or die(mysql_error());  


}
}
$i++;
}




?>
