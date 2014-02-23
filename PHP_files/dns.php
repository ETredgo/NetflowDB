<?php
/*

Speedy Whois - A website for whois queries, based on the phpWhois class.
Copyleft by Uri Even-Chen, Speedy Software.
http://www.speedywhois.com/

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

$query= trim(@$_GET['query']);

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>WhoIS/DNS lookup</title>
<script type="text/javascript"><!--

{
   if (top != self)
   {
      top.location= self.location;
   }
}

<?php
if (!(strlen($query) > 0))
{
?>
function setfocus()
{
   document.queryform.query.focus();
   return;
}

<?php
}
?>
//--></script>
<link rel="stylesheet" type="text/css" href="css/page.css">


</head>
<body <?php
if (!(strlen($query) > 0))
{
?> onload="setfocus();"<?php
}
?>>
<center>
<br />
<?php
if (strlen($query) > 0)
{
?>
<table cellpadding="0" cellspacing="0" border="0" width="728" dir="ltr">
<tr align="left" valign="top"><td>
<?php
   include_once('phpwhois/whois.main.php');
   include_once('phpwhoisutils/whois.utils.php');

   $whois= new Whois();
   $result= $whois->Lookup($query);

   echo "<br />\n";
   echo "<b>Results for " . $query . ":</b><br />\n";
   echo "<br />\n";

   if (!empty($result['rawdata']))
   {
      $utils= new utils;
      echo $utils->showHTML($result);
   }
   else
   {
      echo implode($whois->Query['errstr'],"<br />\n");
   }

   echo "<br />\n";
?>
</td></tr>
</table><br />
<?php
}
else
{
?>
<blockquote>
</blockquote>
<?php
}
?>
<form name="queryform" method="get" action="dns.php">
<table cellpadding="6" cellspacing="0" border="0" width="540" dir="ltr">
<tr><td bgcolor="#92CAFE">
<table width="100%" cellpadding="0" cellspacing="0" border="0" dir="ltr">
   <tr class="upperrow">
      <td align="left" valign="top" nowrap="nowrap"><font face="Arial" size="+0"><b>Enter any IP/Hostname:</b></font></td>
   </tr>
   <tr class="middlerow">
      <td align="center" valign="middle" nowrap="nowrap"><input type="text" name="query" value="" class="queryinput" />&nbsp;<input type="submit" name="submit" value="Check Domain" /></td>
   </tr>
   <tr class="lowerrow">
      <td align="right" valign="bottom"></td>
   </tr>
</table>
</td></tr>
</table>
</form>
<br />
</center>
</body>
</html>
