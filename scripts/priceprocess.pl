#!/usr/bin/perl

$d=`date`; chomp($d); `echo "Starting:$d" >> /home/namit/private/logs/priceprocess.log `;
`touch /home/namit/kathabimb.com/hedge/lastpriceupdate`;

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

#download the file and save the zip
$read=`curl --user-agent "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"  http://www.nseindia.com/content/historical/EQUITIES/2001/JAN/cm01JAN2001bhav.csv.zip -o /home/namit/kathabimb.com/hedge/data/cm01JAN2001bhav.csv.zip`;

#now unzip the file
`cd /home/namit/kathabimb.com/hedge/data/;unzip cm01JAN2001bhav.csv.zip`; 

$checkfordata=`grep TIMESTAMP /home/namit/kathabimb.com/hedge/data/cm01JAN2001bhav.csv`;

if (!($checkfordata =~ /TIMESTAMPE/ ))

{ 
$content="failure in loading the feed in $path/$file";
print "sending email \n";
`echo "$content" >> /home/namit/private/logs/priceprocess.log `;
exit 0;
}
