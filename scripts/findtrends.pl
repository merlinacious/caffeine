#!/usr/bin/perl

use DBI;

$dbh = DBI->connect('DBI:mysql:creativeminds_', 'creativeminds', 'c1sc0w0rks321') || `echo "Unable to connect to database for feed" | /b
in/mail -s "Feed Error at $date" "$admin" `;



$showme=10;


open("DAT","csv");
@data=<DAT>;
close("DAT");

%lookupmonth=();
$lookupmonth{"JAN"}="01";
$lookupmonth{"FEB"}="02";
$lookupmonth{"MAR"}="03";
$lookupmonth{"APR"}="04";
$lookupmonth{"MAY"}="05";
$lookupmonth{"JUN"}="06";
$lookupmonth{"JUL"}="07";
$lookupmonth{"AUG"}="08";
$lookupmonth{"SEP"}="09";
$lookupmonth{"OCT"}="10";
$lookupmonth{"NOV"}="11";
$lookupmonth{"DEC"}="12";


%howmanyclientsbought=();
%totalbought=();
%mosttransactions=();
# Date,Symbol,Security Name,Client Name,Buy / Sell,Quantity Traded ,Trade Price / Wght. Avg. Price,Remarks

foreach $line (@data)
{
 @getbadcomma=split(/\"/,$line);
 $bad=$getbadcomma[1];
 $good=$bad;
 $good =~ s/\,//g;
 if ($bad =~ /[0-9]/ )
 {
 $line =~ s/$bad/$good/g;
 $line =~ s/\"//g;
 }

 @cut=split(/\,/,$line);
 $date=uc($cut[0]);
 @breakup=split(/\-/,$date);
 $day=$breakup[0]; @size=split(//,$day); $count=@size; if ($count < 2) { $day="0" . $day; }
 $month=$breakup[1];
 @size=split(//,$month); $count=@size; if ($count < 2) { $month="0" . $month; }
 $year="20" . $breakup[2];
 $month=$lookupmonth{"$month"};
 $date="$year-$month-$day";
 $symbol=$cut[1];
 $security=$cut[2];
 $client=$cut[3];
 $action=$cut[4];
 $quantity=$cut[5];
 $price=$cut[6];
 $remark=$cut[7];

 if ($action =~ /BUY/ ) { $action="1"; }
 elsif ($action =~ /SELL/ ) { $action="-1"; }

$values=" '$date', '$symbol', '$security', '$client', '$action', '$quantity', '$price', '$remarks'  ";
$sql="INSERT INTO history (DATESTAMP, SYMBOL,SECURITY,CLIENT,ACTION,QUANTITY,PRICE,REMARK) VALUES($values) ;";

print "$sql \n";
$dbh->do("$sql");
}

$dbh->disconnect();

