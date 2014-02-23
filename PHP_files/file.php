<?php

if ($_GET["update"] == "true"){

echo "<html><head><title>Netflow File Manager</title>";
echo '<script type="text/javascript" language="javascript" src="jquery-latest.js"></script>';
echo '<script type="text/javascript" language="javascript" src="picnet.table.filter.min.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="css/page.css" />';
//echo '<meta http-equiv="refresh" content="300" />';
echo '<body>';
echo '<div class="console">';
echo exec('/usr/bin/python /var/netflowdb/netflowdb.py');
echo "<p>Database update complete.</p>";
echo '</div>';
echo '</body>';
} else if ($_GET["update"] == "auto"){

echo "<html><head><title>Netflow File Manager</title>";
echo '<script type="text/javascript" language="javascript" src="jquery-latest.js"></script>';
echo '<script type="text/javascript" language="javascript" src="picnet.table.filter.min.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="css/page.css" />';
echo '<meta http-equiv="refresh" content="300" />';
echo '<body>';
echo '<div class="console">';
echo exec('/usr/bin/python /var/netflowdb/netflowdb.py');
echo "<p>Database update complete.</p>";
echo '</div>';
echo '</body>';

} else if ($_GET["update"] == "blacklist"){

echo "<html><head><title>Netflow File Manager</title>";
echo '<script type="text/javascript" language="javascript" src="jquery-latest.js"></script>';
echo '<script type="text/javascript" language="javascript" src="picnet.table.filter.min.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="css/page.css" />';
echo '<body>';
echo '<div class="console">';
echo exec('/usr/bin/python /var/netflowdb/tor_bl.py');
echo "<p>Blacklist update complete.</p>";
echo '</div>';
echo '</body>';

} else {





echo "<html><head><title>Netflow File Manager</title>";
echo '<script type="text/javascript" language="javascript" src="jquery-latest.js"></script>';
echo '<script type="text/javascript" language="javascript" src="picnet.table.filter.min.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="css/page.css" />';

include("config.php");

if (!mysql_connect($db_host, $db_user, $db_pwd))
    die("Can't connect to database");

if (!mysql_select_db($database))
    die("Can't select database");
$table = "nf_files";
// sending query
$result = mysql_query("SELECT * FROM {$table}");
if (!$result) {
    die("Query to show fields from table failed");
}

$fields_num = mysql_num_fields($result);

echo "<h1>Files Processed</h1>";
echo "<table border='1'><tr>";
// printing table headers
for($i=0; $i<$fields_num; $i++)
{
    $field = mysql_fetch_field($result);
    echo "<td>{$field->name}</td>";
}
echo "</tr>\n";
// printing table rows
while($row = mysql_fetch_row($result))
{
    echo "<tr>";

    // $row is array... foreach( .. ) puts every element
    // of $row to $cell variable
    foreach($row as $cell)
        echo "<td>$cell</td>";

    echo "</tr>\n";
}
mysql_free_result($result);

}
?>
</body></html>
