#!/usr/bin/perl
use DBI;

# capture today's date and log it
$d=`date`; 
chomp($d); 
`echo "Starting:$d" >> /home/namit/private/logs/process.log `;
`touch /home/namit/kathabimb.com/hedge/lastupdate`;

# email address
$admin='namit.saksena@gmail.com';

# get the name of this script file
$path=`pwd`; 
chomp($path);
$file=$0;
$file =~ s/\.\///g;

# get today's date in specific format
$today=`date +%Y-%m-%d`; 
chomp($today);

# create a lookup to express months in number
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

# get the file from NSE
$read=`curl --user-agent "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"  http://www.nseindia.com/content/equities/bulk.csv 2> /dev/null`;

#Exception handling in case of failure to connect to bulk.csv file
if (!($read =~ /Date/ ))
{ 
	$content="ERR01: Failure in loading the feed in $path/$file";
	print "sending email to say not able to connect to bulk.csv file\n";
	`echo "$content" | /usr/bin/mail -s "Feed Error for run on $d" "$admin" `;
	exit 0;
}

# finding the date in the file
@readlines = split(/\n/,$read);
@readdata = split(/,/,$readlines[1]);
@filedate = split(/-/,$readdata[0]);
$mn = $filedate[1];
$mn=$lookupmonth{"$filedate[1]"};

$dateInFile="$filedate[2]-$mn-$filedate[0]";

# connect to the database
$error_content="ERR02: Unable to connect to database for feed";
$dbh = DBI->connect('DBI:mysql:kathabimb1:mysql.kathabimb.com', 'kathabimb', 'vesit!!roxx') || `echo "$error_content" | /usr/bin/mail -s "Feed Error for run on $d" "$admin" `;

# Get the latest date in the database
$select_query = "SELECT datestamp FROM `history` WHERE id = (SELECT max(id) FROM `history`)";

# PREPARE THE QUERY
$query_handle = $dbh->prepare($select_query);

# EXECUTE THE QUERY
$query_handle->execute();

# BIND TABLE COLUMNS TO VARIABLES
$query_handle->bind_columns(undef, \$dateInTable);

# FETCH RESULTS
$query_handle->fetch();
`echo "Latest Date in history Table:$dateInTable" >> /home/namit/private/logs/process.log `;

#Compare the Latest date in datatable with todays Date
$result = DateComparison($dateInFile,$dateInTable);

$query_handle->finish();

if( $result == 1)
{
# Latest date in table is smaller then the date in file. So insert the data.

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

		 #if (!($date =~ /^$today$/ ))
		 #{ 
		 #  $error="1";
		 #  print "dates do not match up. sending alert \n";
		 #  $content="ERR03: Today is $today and the data is from $date";
		 #  `echo "$content" | /usr/bin/mail -s "Feed Error for run on $d" "$admin" `;
		 #  exit 0;
		 #}

		if ($action =~ /BUY/) { $action="1"; }
		elsif ($action =~ /SELL/) { $action="-1"; }
		else  {$action="0"; }

		$values=" '$date', '$symbol', '$security', '$client', '$action', '$quantity', '$price', '$remarks'  ";
		$sql="INSERT INTO history (DATESTAMP, SYMBOL,SECURITY,CLIENT,ACTION,QUANTITY,PRICE,REMARK) VALUES($values) ;";

		print "$sql \n";
		$dbh->do("$sql");

	}
}
elsif( $result == 2)
{
# Latest date in table is bigger then the date in file. So discard the insert
	$error="1";
	`echo "Latest date in history table is: $dateInTable, and the data in Bulk.csv file of date: $dateInFile. So discarding the insert operation.">> /home/namit/private/logs/process.log`;
}
elsif( $result == 0)
{
# can do Data duplication check here.
	$error="1";
	`echo "SAME DATES -- DUPLICATE DATA: Latest date in history table is: $dateInTable, and the data in Bulk.csv file also of date: $dateInFile. So discarding the insert operation.">> /home/namit/private/logs/process.log`;
}


$dbh->disconnect();
  
if (!($error))
{ 
	`echo "Today is $today and the data is from $date" | /usr/bin/mail -s "Feed Loaded Successfully for $date on run on $d" "$admin" `; 
}


$d=`date`; chomp($d); `echo "Completed:$d" >> /home/namit/private/logs/process.log `;




# Returns 0 : if dates are equal
#	1 : if first date is bigger
#	2 : if second date is bigger
sub DateComparison
{
	$FirstDate = $_[0];
	$SecondDate = $_[1];

	@first = split(/-/,$FirstDate);
	@second = split(/-/,$SecondDate);

	if($first[0] > $second[0])
	{
		return 1;
	}
	elsif($first[0] < $second[0])
	{
		return 2;
	}
	elsif($first[0] = $second[0])
	{
		if($first[1] > $second[1])
		{
			return 1;
		}
		elsif($first[1] < $second[1])
		{
			return 2;
		}
		elsif($first[1] = $second[1])
		{
			if($first[2] > $second[2])
			{
				return 1;
			}
			elsif($first[2] < $second[2])
			{
				return 2;
			}
			elsif($first[2] = $second[2])
			{
				return 0;
			}
		}
	}
}
