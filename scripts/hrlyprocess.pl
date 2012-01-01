#!/usr/bin/perl

$d=`date`; chomp($d); `echo "Starting:$d" >> /home/namit/private/logs/process.log `;
`touch /home/namit/kathabimb.com/hedge/lastupdate`;

use DBI;

$admin='namit.saksena@gmail.com';

$path=`pwd`; chomp($path);
$file=$0;
$file =~ s/\.\///g;
$today=`date +%Y-%m-%d`; chomp($today);
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

$read=`curl --user-agent "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"  http://www.nseindia.com/content/equities/bulk.csv 2> /dev/null`;

if (!($read =~ /Date/ ))
{ 
$content="failure in loading the feed in $path/$file";
print "sending email \n";
`echo "$content" | /usr/bin/mail -s "Feed Error1 at $date" "$admin" `;

exit 0;
}

$dbh = DBI->connect('DBI:mysql:kathabimb1:mysql.kathabimb.com', 'kathabimb', 'vesit!!roxx') || `echo "Unable to connect to database for feed" | /usr/bin/mail -s "Feed Error2 at $date" "$admin" `;

@t=split(/\r?\n/,$read);
foreach $line (@t)
{
 if ($line =~ /^Date/i ) { next; } 
 @details=split(/\,/,$line);
 
 #Date,Symbol,Security Name,Client Name,Buy/Sell,Quantity Traded,Trade Price / Wght. Avg. Price,Remarks
 $date=$details[0];
 $symbol=$details[1];
 $security=$details[2];
 $client=$details[3];
 $action=$details[4];
 $quantity=$details[5];
 $price=$details[6];
 $remarks=$details[7];
 
 @breakup=split(/\-/,$date);
 $day=$breakup[0];
 $month=$breakup[1];
 $year=$breakup[2];
 $month=$lookupmonth{"$month"};
 $date="$year-$month-$day";

#find max date in database to see if you should insert the csv records into the history database
 $sql = "SELECT max(DATESTAMP) from history";
 $sth = $dbh->prepare($sql);
 $sth->execute  or die "SQL Error: $DBI::errstr\n";
 while (@row = $sth->fetchrow_array) {
	$maxdateindb = $row[0];
	print "Max date in DB is $maxdateindb \n";
 } 

# if (!($date =~ /^$today$/ )) - old code which was checking if csv records were for today's run
# this is negative test, hence it reads - if (not (date in file < date in database))

  if (!($date < $maxdateindb))
 { 
   $error="1";
   print "Date in file is $date \n";
   print "Date in file ($date) is not more than that in database ($maxdateindb). Sending alert \n";
   `echo "Max date in history table is $maxdateindb and the data is from $date" | /usr/bin/mail -s "Hourly Error on $d" "$admin" `;
   exit 0;
 }

if ($action =~ /BUY/) { $action="1"; }
elsif ($action =~ /SELL/) { $action="-1"; }
else  {$action="0"; }

$values=" '$date', '$symbol', '$security', '$client', '$action', '$quantity', '$price', '$remarks'  ";
$sql="INSERT INTO history (DATESTAMP, SYMBOL,SECURITY,CLIENT,ACTION,QUANTITY,PRICE,REMARK) VALUES($values) ;";

print "$sql \n";
$dbh->do("$sql");
}

$dbh->disconnect();
  
if (!($error))
{ `echo "Today is $today and the data is from $date" | /usr/bin/mail -s "Feed Loaded Successfully on $date" "$admin" `; }


$d=`date`; chomp($d); `echo "Completed:$d" >> /home/namit/private/logs/process.log `;