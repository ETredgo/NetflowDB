<?
if ($_POST["CSV"] != 1){
echo "<html><head><title>Netflow Output</title>";
echo '<script type="text/javascript" language="javascript" src="jquery-latest.js"></script>';
echo '<script type="text/javascript" language="javascript" src="table2CSV.js"></script>';
echo '<script type="text/javascript" language="javascript" src="picnet.table.filter.min.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="css/page.css" />';
echo '</head><body>';
echo '<script>function goBack(){window.history.back()}</script>';

}
error_reporting(E_ALL);

include("config.php");

if (!mysql_connect($db_host, $db_user, $db_pwd))
    die("Can't connect to database");

if (!mysql_select_db($database))
    die("Can't select database");

// build query

$sfrom = $_POST["dfrom"];
$sto = $_POST["dto"];
//echo $sfrom;
$ssrcadd = $_POST["srca"];
$ssrcport = $_POST["srcp"];
$sdstadd = $_POST["dsta"];
$sdstprt = $_POST["dstp"];
$sprot = $_POST["prot"];
$limit = $_POST["limit"];
$blists = $_POST["blists"];
$ipor = $_POST["ipor"];
$torarray = gettorarray();
$blarray = getblarray();
$frombl = 0;
//print_r(implode(",", $torarray));


if ($blists == 2){
if (empty($torarray)){
$torarray = array(1,2);
}
$tmparray = implode(",",$torarray);
$ssrcadd = $tmparray;
$sdstadd = $tmparray; 
$ipor = 1;
$frombl = 1;

} else if ($blists == 3){
if (empty($blarray)){
$blarray = array(1,2);
}
$tmparray = implode(",",$blarray);
$ssrcadd = $tmparray;
$sdstadd = $tmparray; 
$ipor = 1;
$frombl = 1;

} else if ($blists == 5){
if (empty($blarray)){
$blarray = array(1,2);
}
if (empty($torarray)){
$torarray = array(1,2);
}
$tmparray = implode(",",$torarray);
$ssrcadd = $tmparray;
$sdstadd = $tmparray; 
$tmparray = implode(",",$blarray);
$ssrcadd .= ',' . $tmparray;
$sdstadd .= ',' . $tmparray; 
$ipor = 1;
$frombl = 1;
}













$query = converttosearch($sfrom,$sto,$ssrcadd,$ssrcport,$sdstadd,$sdstprt,$sprot,$ipor,$frombl);

$query .= " ORDER BY `ts` ASC LIMIT " . $limit;
//echo '<h4>'.$query.'</h4>';
$result = mysql_query($query);
if (!$result) {
    die("Query to show fields from table failed");
}

$fields_num = mysql_num_fields($result);


if ($_POST["CSV"] != 1){
//$torarray = gettorarray();
//$blarray = array();

//sort($torarray);
//sort($blarray);
//print_r($blarray);
echo '<script>$(document).ready(function() {
 
  $(\'output\').each(function() {
    var $table = $(this);
    var $button2 = $("<button type=\'button\'>");
    $button2.text("Export to spreadsheet");
    $button2.insertAfter($table);
 
    $button2.click(function() {
      var csv = $table.table2CSV({delivery:\'value\'});
      window.location.href =\'data:text/csv;charset=UTF-8,\'
                            + encodeURIComponent(csv);
    });
  });
})</script>';




//echo "<h4>NetflowDb by Logically Secure Ltd</h4>";
//echo '<button class="butt" onclick="goBack()">Go Back</button><br>';
echo "<table id='output' width='100%' class='table'><thead><tr>";
// printing table headers

echo "<th>Date</th>";
echo "<th>Duration (ms)</th>";
echo "<th>Src IP</th>";
echo "<th>Src Port</th>";
echo "<th>Dst IP</th>";
echo "<th>Dst Port</th>";
echo "<th>Protocol</th>";
echo "<th>TCP Flags</th>";
echo "<th>Bytes</th>";
echo "<th>Packets</th>";


echo "</tr></thead><tbody>";
// printing table rows
while($row = mysql_fetch_row($result))
{
    echo "<tr>";

    // $row is array... foreach( .. ) puts every element
    // of $row to $cell variable

		$ts = backtodate($row[1]);
        $td = $row[2];
        $sa = long2ip($row[3]);
        $da = long2ip($row[4]);
        $sp = $row[5];
        $dp = $row[6];
        $pr = getprot($row[7]);
        $flg = $row[8];
        $byt = $row[9];
        $pkt = $row[10];
		
		
		echo "<td>" . $ts ."</td>";
        echo "<td>". $td . "</td>";
		if (is_int(array_search($row[3], $torarray)) === TRUE){
		
		if (is_int(array_search($row[3], $blarray)) === TRUE){
		echo "<td class='torbl'><a href='dns.php?query=". $sa ."&output=nice' target='_blank'>" . $sa . "</a></td>";
		} else {
		echo "<td class='tor'><a href='dns.php?query=". $sa ."&output=nice' target='_blank'>" . $sa . "</a></td>";
		}
		} else {
		if (is_int(array_search($row[3], $blarray)) === TRUE){
		echo "<td class='blist'><a href='dns.php?query=". $sa ."&output=nice' target='_blank'>" . $sa . "</a></td>";
		} else {
		echo "<td class='clean'><a href='dns.php?query=". $sa ."&output=nice' target='_blank'>" . $sa . "</a></td>";
		}
		
		}
		
		echo "<td>" . $sp . "</td>";
		if (is_int(array_search($row[4], $torarray)) === TRUE){
		
		if (is_int(array_search($row[4], $blarray)) === TRUE){
		echo "<td class='torbl'><a href='dns.php?query=". $da ."&output=nice' target='_blank'>" . $da . "</a></td>";
		} else {
		echo "<td class='tor'><a href='dns.php?query=". $da ."&output=nice' target='_blank'>" . $da . "</a></td>";
		}
		} else {
		if (is_int(array_search($row[4], $blarray)) === TRUE){
		echo "<td class='blist'><a href='dns.php?query=". $da ."&output=nice' target='_blank'>" . $da . "</a></td>";
		} else {
		echo "<td class='clean'><a href='dns.php?query=". $da ."&output=nice' target='_blank'>" . $da . "</a></td>";
		}
		
		}
		echo "<td>" . $dp . "</td>";
        echo "<td>" . $pr . "</td>";
        echo "<td>" . $flg . "</td>";
        echo "<td>" . $byt . "</td>";
        echo "<td>" . $pkt . "</td>";





    echo "</tr>\n";
}
echo "</tbody></table>";
} else {

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings


fputcsv($output, array('Date','Duration (ms)','Src IP','Src Port','Dst IP','Dst Port','Protocol','TCP Flags','Bytes','Packets'));

// fetch the data

// loop over the rows, outputting them
while($row = mysql_fetch_row($result))
{

    // $row is array... foreach( .. ) puts every element
    // of $row to $cell variable

		$ts = backtodate($row[1]);
        $td = $row[2];
        $sa = long2ip($row[3]);
        $da = long2ip($row[4]);
        $sp = $row[5];
        $dp = $row[6];
        $pr = getprot($row[7]);
        $flg = $row[8];
        $byt = $row[9];
        $pkt = $row[10];
		
		$current_row = array($ts,$td,$sa,$sp,$da,$dp,$pr,$flg,$byt,$pkt);
		fputcsv($output, $current_row,","," ");
}


}
mysql_free_result($result);

function getprot($i){
if ($i == 1){
return "TCP";
} else if ($i == 2){
return "UDP";
} else if ($i == 3){
return "ICMP";
} else {
return "Other";
}

}

function backtodate($input){
$dash = "-";
$dot = ".";
$colon = ":";
$space = " ";

$input = substr_replace($input, $dot, 14, 0);
$input = substr_replace($input, $colon, 12, 0);
$input = substr_replace($input, $colon, 10, 0);
$input = substr_replace($input, $space, 8, 0);
$input = substr_replace($input, $dash, 6, 0);
$input = substr_replace($input, $dash, 4, 0);

return $input;
}

function converttosearch($sfrom,$sto,$ssrcadd,$ssrcport,$sdstadd,$sdstport,$sprot,$ipor,$frombl){

$select = "SELECT * FROM nf_data";

if ($sfrom != ""){
$sfrom = tosearchtime($sfrom);
$select .= " WHERE (`ts` > " . $sfrom;
} else {
$select .= " WHERE (`ts` > 0";
}
if ($sto != ""){
$sto = tosearchtime($sto);
$select .= " AND `ts` < " . $sto . ")";
} else {
$select .= ")";
}

/////////////////////////////// src

if ($ssrcadd != ""){
$temparray = explode(",",$ssrcadd);
$x = 0;
foreach ($temparray as $value) {
	if ($frombl == 1){
	$value = long2ip($value);
	}
    if ($x == 0){
	$select .= " AND ((`sa` = '" . ip2long($value);
	$x = 1;
}	else {
	$select .= "' OR `sa` = '" . ip2long($value);
}
}

if ($sdstadd != ""){
$select .= "')";
} else {
$select .= "'))";
}

} else {
$ipor = 0;
}


/////////////////////////////////// dst

if ($sdstadd != ""){
$temparray = explode(",",$sdstadd);
$x = 0;
foreach ($temparray as $value) {
	if ($frombl == 1){
	$value = long2ip($value);
	}
	//echo $value;
    if ($x == 0){
    if ($ipor == 1){
	$select .= " OR (`da` = '" . ip2long($value);
	} else{
	if ($ssrcadd != ""){
	$select .= " AND (`da` = '" . ip2long($value);
	}else {
	$select .= " AND ((`da` = '" . ip2long($value);
	}
	}
	
	$x = 1;
}	else {
	$select .= "' OR `da` = '" . ip2long($value);
}
}
if ($ipor == 1){
$select .= "'))";
} else {
$select .= "'))";
}
}


////////////////////////////////////// srcprt

if ($ssrcport != ""){
$temparray = explode(",",$ssrcport);
$x = 0;
foreach ($temparray as &$value) {
    if ($x == 0){
	$select .= " AND (`sp` = '" . $value;
	$x = 1;
}	else {
	$select .= "' OR `sp` = '" . $value;
}
}
$select .= "')";
}


if ($sdstport != ""){
$temparray = explode(",",$sdstport);
$x = 0;
foreach ($temparray as &$value) {
    if ($x == 0){
	$select .= " AND (`dp` = '" . $value;
	$x = 1;
}	else {
	$select .= "' OR `dp` = '" . $value;
}
}
$select .= "')";
}

if ($sprot > -1){
if ($sprot != 4){
$select .= " AND (`pr` = " . $sprot . ")";
}
}

return $select;

}

function tosearchtime($in){
// 2013-07-12T00:00
$rep = array(" ","-","T",":");
$in = str_replace($rep,"",$in);
return $in . "00000";
}

function gettorarray(){
$toraarayout = array();
$torout = mysql_query("SELECT ip FROM tor_nodes WHERE found = 1 ORDER BY ip ASC");

while($row = mysql_fetch_row($torout)){

array_push($toraarayout,$row[0]);
}

return $toraarayout;
}


function getblarray(){
$blarrayout = array();
$torout = mysql_query("SELECT ip FROM open_bl WHERE found = 1 ORDER BY ip ASC");
while($row = mysql_fetch_row($torout)){

array_push($blarrayout,$row[0]);
}

return $blarrayout;
}





if ($_POST["CSV"] != 1){
echo "</body></html>";
echo "<script>";
echo "$(document).ready(function() {";
echo "$('#output').tableFilter();";
echo "} );";
echo "</script>";
}
?>