#!/usr/bin/perl -wT
# updtttcxls.pl

use CGI qw(:standard);
use CGI::Carp qw( fatalsToBrowser );
use DBI;
use strict;
use File::Basename;
use Spreadsheet::WriteExcel;

my $query=new CGI;

my $Monitor = $query->param('Monitor');
my $Start = $query->param('Start');
my $End = $query->param('End');
my $CSV = $query->param('CSV');
my ($sql, $sth, $min, $max, $fh, $str, $desc);

my $hostname='ttc.ait.sstl.co.uk';
my $port=3306;
my $user='egseadmin';
my $password='enterprise';
#Connect to database
my $dsn = "DBI:mysql:database=environment;host=$hostname;port=$port";
my $dbh = DBI->connect($dsn, $user, $password);

if (!$Start || !$End)
{
  $sql="select DATE(MIN(tmstamp)), DATE(MAX(tmstamp)) from $Monitor";
  $sth = $dbh->prepare("$sql");
  $sth->execute;
  while ( my @row = $sth->fetchrow_array )
  {
    $min = $row[0];
    $max = $row[1];
  }
  print header;
  print start_html(
            -title=>'Get Environmental Data',
  			    -author=>'i.daysh@sstl.co.uk',
  			    -base=>'true',
  			    -style=>[{'src'=>'/css/all.css'},
  			              {'src'=>'datepickercontrol.css'},
                      {'src'=>'/css/format.css'}],
            -script=>[{ -type => 'text/javascript',
                        -src => 'datepickercontrol.js'}]
                 );
	
  
  my $myself = self_url;
  
  print "<h1>Environment Data Export</h1>";
  print "<p>Enter the dates below by entering the date in YYYY-MM-DD format, or click on the calendar icon.</p>";
  print "<p>The start date is the first day you want information for, and the end date is the last day.</p>";
  print start_form;
  
  print "<p>Start Date</p>";
  print "<div><input type=\"text\" name=\"Start\" id=\"Start\" size=\"13\" datepicker=\"true\" datepicker_format=\"YYYY-MM-DD\" datepicker_min=\"$min\" datepicker_max=\"$max\" /></div>\n";
  print "<p>End Date</p>";
  print "<div><input type=\"text\" name=\"End\" id=\"End\" size=\"13\" datepicker=\"true\" datepicker_format=\"YYYY-MM-DD\" datepicker_min=\"$min\" datepicker_max=\"$max\" /></div>\n";
  print "<p>CSV Output<input type=\"checkbox\" name=\"CSV\" id=\"CSV\" /></p>";
  print "<!-- English -->
  <input type=\"hidden\" id=\"DPC_TODAY_TEXT\" value=\"today\">
  <input type=\"hidden\" id=\"DPC_BUTTON_TITLE\" value=\"Open calendar...\">
  <input type=\"hidden\" id=\"DPC_MONTH_NAMES\" value=\"['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']\">
  <input type=\"hidden\" id=\"DPC_DAY_NAMES\" value=\"['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']\">\n";
  print "<p>".hidden(-name=>'Monitor', -default=>$Monitor)."</p>\n";
  print submit(-value => 'Get Data');
  print end_form;
  print end_html;
}
else
{
  $sql="select description from monitors where monitor='$Monitor'";
  $sth = $dbh->prepare("$sql");
  $sth->execute;
  while ( my @row = $sth->fetchrow_array )
  {
    $desc = $row[0];
  }
  if ($CSV)
  {
    print header(-attachment => $desc.'.txt');
    open $fh, '>', \$str or die "Failed to open filehandle: $!";
    print $fh "Date Time,Temperature,Humidity,Dew Point\r\n";
    $sql="select DATE_FORMAT(tmstamp,'%Y-%m-%d %H:%i:%s'), temp, humid, dew from $Monitor where tmstamp > '$Start' and tmstamp < (select max(tmstamp) from ev1 where tmstamp like '%$End%')";
    $sth = $dbh->prepare("$sql");
    $sth->execute;
    while ( my @row = $sth->fetchrow_array )
    {
      print $fh "$row[0],$row[1],$row[2],$row[3]\r\n";
    }
    
    
    print $str;
  }
  else
  {
    print header(-attachment => $desc.'.xls');
    open $fh, '>', \$str or die "Failed to open filehandle: $!";
    binmode ($fh);
    my $workbook  = Spreadsheet::WriteExcel->new($fh);
    my %font = (font => 'Arial', size => 10, text_wrap => 1);
    my %border = (bottom => 1, top => 1, left =>1, right => 1);
    my %borderL = (bottom => 1, top => 1, left =>2, right => 1);
    my %borderR = (bottom => 1, top => 1, left =>1, right => 2);
    my %borderRL = (bottom => 1, top => 1, left =>2, right => 2);
    my $date_format = $workbook->add_format(num_format => 'dd/mm/yyyy hh:mm:ss');
    my $sheetno=1;
    my $Sheet = $workbook->add_worksheet('Data'.$sheetno);
    $Sheet->set_column(0,0,18);
    $Sheet->set_column(1, 3, 11);
    $Sheet->write_string('A1', 'Date Time');
    $Sheet->write_string('B1', 'Temperature');
    $Sheet->write_string('C1', 'Humidity');
    $Sheet->write_string('D1', 'Dew Point');
    my $rowcnt=1;
    $sql="select DATE_FORMAT(tmstamp,'%Y-%m-%dT%H:%i:%s'), temp, humid, dew from $Monitor where tmstamp > '$Start' and tmstamp < (select max(tmstamp) from ev1 where tmstamp like '%$End%')";
  #  $Sheet->write_string('E1', $sql);
    $sth = $dbh->prepare("$sql");
    $sth->execute;
    while ( my @row = $sth->fetchrow_array )
    {
  #    my $row=\@row;
  #    $Sheet->write_row('A'.$rowcnt, $row);
      $Sheet->write_date_time($rowcnt,0, $row[0], $date_format);
      $Sheet->write_number($rowcnt,1, $row[1]);
      $Sheet->write_number($rowcnt,2, $row[2]);
      $Sheet->write_number($rowcnt,3, $row[3]);
      $rowcnt++;
      if ($rowcnt==65535)
      {
        $sheetno++;
        $Sheet = $workbook->add_worksheet('Data'.$sheetno);
        $Sheet->set_column(0, 0, 18);
        $Sheet->set_column(1, 3, 11);
        $Sheet->write_string('A1', 'Date Time');
        $Sheet->write_string('B1', 'Temperature');
        $Sheet->write_string('C1', 'Humidity');
        $Sheet->write_string('D1', 'Dew Point');
        $rowcnt=1;
      }
    }
    $workbook -> close();
    print $str;
  }
 
}
