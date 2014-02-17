#!/usr/bin/perl  --

# Name: environ.pl
# Purpose: Grabs the data from all configured environmental monitors,
# enters data into database, and generates emails based on set limits
#           
# $Id: updatedb.pl,v 1.2 2007/05/01 16:56:10 idaysh Exp $
# 
# History: 2008-10-01 - initial creation - Ian Daysh
#
# Notes:
#
#
#

use strict;
use DBI;
use Getopt::Long;
use Net::Telnet;
use Mail::Builder; 
use Config::Tiny;
use Email::Send;
use Date::Calc;

my ($second, $minute, $hour, $dayOfMonth, $month, $yearOffset, $dayOfWeek, $dayOfYear, $daylightSavings, $year, $Date, $err);
my ($dsn, $dbh, $telnet, $monhost, $monchan, $monname, $insql, $temp, $humid, $dew, $col, $val, $sth, $errstr, $rows, $sql, $result, $Config);
my ($tmpahi, $tmpwhi, $tmpwlo, $tmpalo, $tmpwarn, $tmpalarm, $humahi, $humwhi, $humwlo, $humalo, $humwarn, $humalarm, $mailsent, $HumidCount, $description, $subject, $content, $msg, $TmpAve, $HumAve, $AvTable, $AvDur);

chdir('/var/www/ait/environ');

my $inifile="environ.ini";

$Config = Config::Tiny->read($inifile);
$Email::Send::Sendmail::SENDMAIL = '/usr/sbin/sendmail';
my $avecnt = 10;
my $reply_to = $Config->{server}->{mailfrom};
my $aldelta = $Config->{server}->{almaildelta};
my $stablefor = $Config->{server}->{stablefor};
$dsn = "DBI:mysql:database=".$Config->{server}->{database}.";host=".$Config->{server}->{host}.";port=".$Config->{server}->{port};
$dbh = DBI->connect($dsn, $Config->{server}->{user}, $Config->{server}->{password});

my @AvailInfo = (['1 WEEK','AvWeek'],['1 MONTH','AvMonth'],['1 YEAR','AvYear']);
my $availlen=@AvailInfo;

$sql="select monitor, tempah, tempwh, tempwl, tempal,humidah, humidwh, humidwl, humidal,
description, TmpAve, HumAve, mail from monitors order by description";

$sth=$dbh->prepare($sql);
$result=$sth->execute;
while (($monname, $tmpahi, $tmpwhi, $tmpwlo, $tmpalo, $humahi, $humwhi, $humwlo, $humalo, $description, $TmpAve, $HumAve, $mailsent) = $sth->fetchrow_array)
{
	$monhost=$Config->{$monname}->{host};
	$monchan=$Config->{$monname}->{chan};
#	print $monhost." ".$monchan." ".$monname." ".$tmpahi." ".$tmpwhi." ".$tmpwlo." ".$tmpalo." ".$tmpwarn." ".$tmpalarm." ".$humahi." ".$humwhi." ".$humwlo." ".$humalo." ".$humwarn." ".$humalarm." ".$TempCount." ".$HumidCount." ".$description." ".$TmpLast." ".$HumLast."\n";
#	print "HumCnt ".$HumidCount."\n";
#	print "HumLst ".$HumLast." \n";
	
	my $mail=0;
	my $send_to = $Config->{$monname}->{email};
	my @addresses=split(/,/, $send_to);
	my $active = $Config->{$monname}->{active};
	
	if ($monchan == "1")
	{
  	$telnet = new Net::Telnet ( Port=>2000, Timeout=>10, Prompt=>'/\r/', Errmode => 'return');
  	$telnet->dump_log('test.log');
  	$telnet->open($monhost);
  	$telnet->put("*SRTC\r");
  	$temp = $telnet->get();
  	$telnet->put("*SRH\r");
  	$humid = $telnet->get();
  	$telnet->put("*SRDC\r");
  	$dew = $telnet->get();
  	$telnet->dump_log("");
  }
	elsif ($monchan == "2")
	{
  	$telnet = new Net::Telnet ( Port=>2000, Timeout=>10, Prompt=>'/\r/', Errmode => 'return');
  	$telnet->dump_log('test.log');
  	$telnet->open($monhost);
  	$telnet->put("*SRTC2\r");
  	$temp = $telnet->get();
  	$telnet->put("*SRH2\r");
  	$humid = $telnet->get();
  	$telnet->put("*SRDC2\r");
  	$dew = $telnet->get();
  	$telnet->dump_log("");
  }


	($second, $minute, $hour, $dayOfMonth, $month, $yearOffset, $dayOfWeek, $dayOfYear, $daylightSavings) = gmtime();
	$year=1900 + $yearOffset;
	$month++;
	$Date="$year-$month-$dayOfMonth $hour:$minute:$second";

	$col="(tmstamp, temp, humid, dew)";
	$val="('$Date','$temp','$humid','$dew')";

	$insql="INSERT INTO $monname $col VALUES $val";
#	print $insql."\n";
	$dbh->do($insql) or die "Unable to execute query: $dbh->errstr\n";
	$errstr = $dbh->errstr;
	#$rows=$dbh->rows;


#	($second, $minute, $hour, $dayOfMonth, $month, $yearOffset, $dayOfWeek, $dayOfYear, $daylightSavings) = gmtime(time()-3600);
#	$year=1900 + $yearOffset;
#	$month++;
#	my $WarnTrig="$year-$month-$dayOfMonth $hour:$minute:$second";
	my $WarnTrig = time()-3530;
	($second, $minute, $hour, $dayOfMonth, $month, $yearOffset, $dayOfWeek, $dayOfYear, $daylightSavings) = gmtime(time()-8*3600);
	$year=1900 + $yearOffset;
	$month++;
	my $graphdate="$year-$month-$dayOfMonth $hour:$minute:$second";
	
#	print "Trig ".$WarnTrig."\n";
#	print "GRaph ".$graphdate."\n";
#	print "HumAl ".$humalarm."\n";
#	print "TempAl ".$tmpalarm."\n";

	system("/usr/bin/php graph.php $monname humid Humidity '$graphdate' > $monname-humid.png");
	system("/usr/bin/php graph.php $monname temp Temperature '$graphdate' > $monname-temp.png");
	$content="<p style=\"font-family: Verdana, Helvetica, sans-serif; font-size: 12px; font-weight: bold; color: black\">This is an automatically generated email.</p>\n";
	
#####################
# Comms error check #
#####################
	if (($temp=="")&($humid==""))
		{
		# Comms Error
		$subject= $description." Communication Error";
		$content.= "<p style=\"font-family: Verdana, Helvetica, sans-serif; font-size: 12px; color: black\">Cannot communicate with ".$description.". Check network connection and power to the logger.</p>\n";
		$send_to=$reply_to;
		$mail=1;
		}

####################
# Generate Average #
####################

  $insql = "select temp,humid from $monname order by tmstamp desc limit $avecnt";
  my $sth=$dbh->prepare($insql);
  $result=$sth->execute;
#  $dbh->do($insql) or die "Unable to execute query: $dbh->errstr\n";
#  $errstr = $dbh->errstr;
  my ($avetemp, $avehum, $tempsum, $humsum);
  while (my ($temp, $hum) = $sth->fetchrow_array)
  {
    $tempsum += $temp;
    $humsum += $hum;
  }
  $avehum = $humsum / $avecnt;
  $avetemp = $tempsum / $avecnt;
  $insql = "UPDATE monitors SET TmpAve = $avetemp, HumAve = $avehum WHERE monitor = '$monname'";
#  print $insql;
  $dbh->do($insql) or die "Unable to execute query: $dbh->errstr\n";
#  $errstr = $dbh->errstr;
  

	for (my $i=0; $i<$availlen; $i++)
	{
		my @av =@{$AvailInfo[$i]};
		my $sql="	SELECT
		(SELECT COUNT(*) FROM $monname WHERE tmstamp > (SELECT DATE_SUB(UTC_TIMESTAMP(),INTERVAL $av[0]))) AS Total,
		(SELECT COUNT(*) FROM $monname WHERE
		$monname.humid > (select humidal from monitors where monitor='$monname')
		and $monname.humid < (select humidah from monitors where monitor='$monname')
		and tmstamp > (SELECT DATE_SUB(UTC_TIMESTAMP(),INTERVAL $av[0]))) AS Avail";
		my $sth=$dbh->prepare($sql);
		my $result=$sth->execute;
		while (my ($Tot, $Avail) = $sth->fetchrow_array)
		{
			my $insql="UPDATE monitors SET $av[1]=100*($Avail/$Tot) where monitor='$monname'";
		  $dbh->do($insql) or die "Unable to execute query: $dbh->errstr\n";
	
	#		print $insql;
		}
		
	}


##################
# Humidity Check #
##################
	my $mailcnt = 15;
	my $GreenChk = 1800;
	$insql = "select humid from $monname order by tmstamp desc limit $mailcnt";
	my $sth=$dbh->prepare($insql);
	$result=$sth->execute;
	#  $dbh->do($insql) or die "Unable to execute query: $dbh->errstr\n";
	#  $errstr = $dbh->errstr;
	my ($avetemp, $avehum, $tempsum, $humsum, $hummail);
	while (my ($hum) = $sth->fetchrow_array)
	{
	  $humsum += $hum;
	}
	$avehum = sprintf ("%.1f", ($humsum / $mailcnt));
	
	if (($humid != "") & (($humahi <= $avehum) | ($humalo >= $avehum)))
	{
	#ALARMALARM

		$content.="<p style=\"font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; line-height: 150%; font-weight: bold; color: red\">".$description." average humidity for the last ".$mailcnt." minutes is ".$avehum."%. All work on spacecraft and any electronic flight hardware should cease until the limits return to normal.</p>\n";
		$subject= "BA G04 Cleanroom ".$description." Humidity Status:- RED";
		$content.="<p style=\"font-family: Verdana, Helvetica, sans-serif; font-size: 12px; color: black\">Humidity should be between $humalo% and $humahi%.</p>\n";
		$content.="<p style=\"font-family: Verdana, Helvetica, sans-serif; font-size: 12px; color: black\">Attached is a graph of humidity for the last 8 hours</p>\n<img src=\"cid:hum\">";
		$content.="<p style=\"font-family: Verdana, Helvetica, sans-serif; font-size: 12px; color: black\">Current data can be viewed at the <a href=\"http://www1.ait.sstl.co.uk/environ/index.php?&mon=$monname\">AIT Environmental Monitor</a></p>\n";
		$hummail=1;
		if ($mailsent == 0)
		{
			$mail=1;
			my $insql="UPDATE monitors SET mail=1, RedTM=".time." where monitor='$monname'";
		  $dbh->do($insql) or die "Unable to execute query: $dbh->errstr\n";
		}
		my $insql="UPDATE monitors SET GreenTM=0 where monitor='$monname'";
	  $dbh->do($insql) or die "Unable to execute query: $dbh->errstr\n";
	}
	else
	{
		$subject= "BA G04 Cleanroom ".$description." Humidity Status:- GREEN";
		$content.= "<p style=\"font-family: Verdana, Helvetica, sans-serif; font-size: 12px; color: black\">".$description." average humidity for the last ".$mailcnt." minutes has returned to within acceptable limits </p>\n";
		if ($mailsent == 1)
		{
			
			$insql = "select GreenTM + ".$GreenChk." from monitors where monitor='$monname'";
			my $sth=$dbh->prepare($insql);
			$result=$sth->execute;
			my $GreenTM;
			while (my ($hum) = $sth->fetchrow_array)
			{
			  $GreenTM= $hum;
			}
			
			if ($GreenTM == 0)
			{
				$mail=3;
				my $insql="UPDATE monitors SET GreenTM=".time." where monitor='$monname'";
			  $dbh->do($insql) or die "Unable to execute query: $dbh->errstr\n";
				$content.= "<p style=\"font-family: Verdana, Helvetica, sans-serif; font-size: 12px; color: black\">Green Time has been set.</p>";
			}
			elsif ($GreenTM < time)
			{
				$mail=3;
				my $insql="UPDATE monitors SET mail=0, GreenTM=0 , RedTM=0 where monitor='$monname'";
			  $dbh->do($insql) or die "Unable to execute query: $dbh->errstr\n";
			  $mailsent=0;
			}
		}
	}
	if ($mailsent & !$mail)
	{
		#Check if RedTM has been set today.
		#If has, delta more than 3 hours and is 1pm send mail
	}
############
# Mail it! #
############

	if ($mail)
		{
		$msg = Mail::Builder->new();
		$msg->from($reply_to);
		$msg->subject($subject);
		foreach (@addresses)
			{
			$msg->to->add($_);
			}
		$msg->priority($mail);
		
		if ($hummail)
		{
			$msg->image->add("$monname-humid.png", "hum");
		}
		$msg->htmltext($content);
		if ($active eq "true")
			{
#			print "active $active\n";
			my $mailer = Email::Send->new({mailer => 'Sendmail'}) -> send($msg->stringify);
#			print $mailer."\n".$msg->stringify."\n";
			}
		}


}
