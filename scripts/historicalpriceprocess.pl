#!/usr/bin/perl
use File::Slurp;
#use strict;
#use warnings;
use DBI;

my $d=`date`; chomp($d); `echo "Starting:$d" >> /home/namit/private/logs/forceprocess.log `;
`touch /home/namit/kathabimb.com/hedge/lastupdate`;

my @folders = </home/namit/kathabimb.com/hedge/data/prices/*>;
my @files = ();

 my $symbol="";
 my $series="";
 my $open="";
 my $high="";
 my $low="";
 my $close="";
 my $last="";
 my $prevclose="";
 my $tottrdqty="";
 my $tottrdval="";
 my $timestamp="";
 my $totaltrades = "";
 my $isin = "";
 
 my $values ="";
 my $sql ="";
 my $dbh ="";
 my @columns=();

foreach my $folder(@folders)
{
	#if($folder == "2011") {next;}
	
	#@files = read_dir $folder;
	
	@foldername = split(/\//,$folder);
	@files = ($folder."/01_".$foldername[7].".csv",$folder."/02_".$foldername[7].".csv",$folder."/03_".$foldername[7].".csv",$folder."/04_".$foldername[7].".csv",$folder."/05_".$foldername[7].".csv",$folder."/06_".$foldername[7].".csv",$folder."/07_".$foldername[7].".csv",$folder."/08_".$foldername[7].".csv",$folder."/09_".$foldername[7].".csv",$folder."/10_".$foldername[7].".csv",$folder."/11_".$foldername[7].".csv",$folder."/12_".$foldername[7].".csv");

	if($foldername[8] == "2011") {next;}
	
	foreach my $file (@files)
	{
		#my @file_type = split(/./,$file);
		
		#if($file_type[1] != "csv") {next;}
		
	    open(IN,"<$file") or die "Can't open $file :$!\n";
	   
	    $dbh = DBI->connect('DBI:mysql:kathabimb1:mysql.kathabimb.com', 'kathabimb', 'vesit!!roxx') || `echo "Unable to connect to database for historical data" `;
	     while(my $line = <IN>)
	     {
	         	 if ($line =~ /^SYMBOL/i ) { next; } 
				 @columns=split(/\,/,$line);
				 
				#SYMBOL,SERIES,OPEN,HIGH,LOW,CLOSE,LAST,PREVCLOSE,TOTTRDQTY,TOTTRDVAL,TIMESTAMP
				 $symbol=$columns[0];
				 $series=$columns[1];
				 $open=$columns[2];
				 $high=$columns[3];
				 $low=$columns[4];
				 $close=$columns[5];
				 $last=$columns[6];
				 $prevclose=$columns[7];
				 $tottrdqty=$columns[8];
				 $tottrdval=$columns[9];
				 $timestamp=$columns[10];
				 
				 $totaltrades=$columns[11];
				 $isin=$columns[12];
				 	 
				
				$values=" '$symbol', '$series', '$open', '$high', '$low', '$close', '$last', '$prevclose', '$tottrdqty', '$tottrdval', '$timestamp', '$totaltrades','$isin'  ";
				$sql="INSERT INTO historicnseprices (symbol,series,open,high,low,close,last,prevclose,tottrdqty,tottrdval,timestamp,totaltrades,isin) VALUES($values) ;";
				
				print "$sql \n";
				$dbh->do("$sql");
		}
				
				$dbh->disconnect();
				  
				if (!($error))
				{ `echo "Error"`; }
				
				
				$d=`date`; chomp($d); `echo "Completed:$d" >> /home/namit/private/logs/forceprocess.log `;
	   
	    
	    close IN;
	    
	    
	# }   #print $fileName . "\n";
	    #print $outfile . "\n";
	}
	
	@files=();
}