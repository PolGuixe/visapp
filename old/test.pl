#!/usr/bin/perl  --

# Name: updatedb.pl
# Purpose: Updates the databse from tab separated files
#           
# $Id: updatedb.pl,v 1.2 2007/05/01 16:56:10 idaysh Exp $
# 
# History: 2006-06-22 - initial creation - Ian Daysh
#
# Notes:
#	TLM file is tlmupdate.txt
#	TCMD file it scmdupdate.txt
#	Edit variable definitions to suit
#	2006-06-22 Only TLM update works at the moment
#	Use Docs #113496 as a template for TLM update files

use strict;
use DBI;
use Getopt::Long;
use Net::Telnet;

my ($second, $minute, $hour, $dayOfMonth, $month, $yearOffset, $dayOfWeek, $dayOfYear, $daylightSavings, $year, $Date);
my ($dsn, $dbh, $telnet, $monip, $monname, $insql, $temp, $humid, $dew, $col, $val, $sth, $errstr, $rows);

$dsn = "DBI:mysql:database=environment;host=ttc.ait.sstl.co.uk;port=3306";
$dbh = DBI->connect($dsn, "egseuser", "EgseUser");


$monip='10.1.206.200';
$monname='ev1';

$telnet = new Net::Telnet ( Port=>2000, Timeout=>10, Prompt=>'/\r/');
$telnet->dump_log('test.log');
$telnet->open($monip);
$telnet->put("*SRT\r");
$temp = $telnet->get();
$telnet->put("*SRH\r");
$humid = $telnet->get();
$telnet->put("*SRD\r");
$dew = $telnet->get();
$telnet->dump_log("");

($second, $minute, $hour, $dayOfMonth, $month, $yearOffset, $dayOfWeek, $dayOfYear, $daylightSavings) = localtime();
$year=1900 + $yearOffset;
$month++;
$Date="$year-$month-$dayOfMonth $hour:$minute:$second";

$col="(timestamp, temp, humid, dew)";
$val="('$Date','$temp','$humid','$dew')";

$insql="INSERT INTO $monname $col VALUES $val";
$sth=$dbh->do($insql) or die "Unable to execute query: $dbh->errstr\n";
$errstr = $dbh->errstr;
$rows=$dbh->rows;

