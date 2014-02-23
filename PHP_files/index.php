<html>
<head>
<title>Netflow Output</title>
<script type="text/javascript" language="javascript" src="jquery-latest.js"></script>
<script type="text/javascript" language="javascript" src="jquery-ui-1.10.3/ui/jquery-ui.js"></script>
<script type="text/javascript" language="javascript" src="date.js"></script>
<script type="text/javascript" language="javascript" src="picnet.table.filter.min.js"></script>
<script>
var bludp = 0;
var nfudp = 0;
function updateNF()
{

if (nfudp == 0){
document.getElementById("nfud").src="images/updateNFb.png";
nfudp = 1;
var xmlhttp;
if (window.XMLHttpRequest)
  {
// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("nfconsole").innerHTML=xmlhttp.responseText;
    document.getElementById("nfud").src="images/updateNF.png";
    nfudp = 0;
    }
}
xmlhttp.open("GET","file.php?update=true",true);
xmlhttp.send();
}
else {
alert("Please be patient!!");
}
}

function updateBL()
{

if (bludp == 0){

document.getElementById("blud").src="images/updateBLb.png";
bludp = 1;
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("nfconsole").innerHTML=xmlhttp.responseText;
    document.getElementById("blud").src="images/updateBL.png";
    bludp = 0;
    }
}
xmlhttp.open("GET","file.php?update=blacklist",true);
xmlhttp.send();

} else {

alert("Please be patient!!");

}
}


</script>




<style>
/* css for timepicker */
.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
.ui-timepicker-div dl { text-align: left; }
.ui-timepicker-div dl dt { float: left; clear:left; padding: 0 0 0 5px; }
.ui-timepicker-div dl dd { margin: 0 10px 10px 45%; }
.ui-timepicker-div td { font-size: 90%; }
.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }

.ui-timepicker-rtl{ direction: rtl; }
.ui-timepicker-rtl dl { text-align: right; padding: 0 5px 0 0; }
.ui-timepicker-rtl dl dt{ float: right; clear: right; }
.ui-timepicker-rtl dl dd { margin: 0 45% 10px 10px; }
</style>

<link rel="stylesheet" type="text/css" href="css/page.css" />
<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
</head>
<body>
<div id="header">
<img src="images/LS.gif" align="left" width="32px" height="32px"></img>
<a href="file.php" target="_BLANK"><img src="images/card_file.png" align="right" width="32px" height="32px"></img></a>
<img onclick="updateNF()" id="nfud" src="images/updateNF.png" align="right" width="32px" height="32px"></img>
<img onclick="updateBL()" id="blud" src="images/updateBL.png" align="right" width="32px" height="32px"></img>
<span class="title_main">NetflowDB</span><span class="title_sub"> by Logically Secure Ltd</span>
</div>
<p class="toggle">Netflow Search [+]</p>
<div class="toggle_content">
<form action="search.php" method="POST" id="search" name="search" target="resframe">
<table id='output' width='100%' class='table'><thead><tr>
<tr><td>Date/Time From: </td><td><input id="dfrom" type="text" name="dfrom"><br></td></tr>
<tr><td>Date/Time To: </td><td><input id="dto" type="text" name="dto"><br></td></tr>
<tr><td>Source Address(s): </td><td><input type="text" name="srca">Comma separated(Blank = All)<br></td></tr>
<tr><td>Source Port(s): </td><td><input type="text" name="srcp">Comma separated(Blank = All)<br></td></tr>
<tr><td>Src/Dst IP and/or: </td><td>
<select name="ipor" form="search">
  <option value="0">AND</option>
  <option value="1">OR</option>
</select>
<br></td></tr>
<tr><td>Destination Address(s): </td><td><input type="text" name="dsta">Comma separated(Blank = All)<br></td></tr>
<tr><td>Destination Port(s): </td><td><input type="text" name="dstp">Comma separated(Blank = All)<br></td></tr>
<tr><td>Protocol: </td><td>
<select name="prot" form="search">
  <option value="4">ALL</option>
  <option value="1">TCP</option>
  <option value="2">UDP</option>
  <option value="3">ICMP</option>
  <option value="0">Other</option>
</select>
<br></td></tr>
<tr><td>Blacklist Filter: </td><td>
<select name="blists" form="search">
  <option value="1">None</option>
  <option value="2">TOR</option>
  <option value="3">Open Blacklist</option>
  <!-- <option value="4">User Specified</option> -->
  <option value="5">All</option>
</select>Source/Dst IP input fields are ignored.
<br></td></tr>
<tr><td>Record limit: </td><td><input type="number" value="5000" name="limit">Default is 5000<br></td></tr>
<tr><td>Output to CSV: <input type="radio" name="CSV" value="1">yes
<input type="radio" name="CSV" value="0" checked="true">no</td><td><input class="butt" type="submit" value="GO!" name="go" id="go" onclick="this.form.submit(); this.disabled = 1;"><input class="butt2" type="button" value="Reset" onclick="location.reload();"></td></tr>
</table>
</form>
</div>
<iframe id="resframe" class="resframe" seamless="seamless" border=0 name="resframe" width="100%" src="" ></iframe>
</body>
<script>
function validateForm()
{
var x=document.forms["search"]["dfrom"].value;
if (x==null || x=="")
  {
  alert("You must enter an start date/time");
  return false;
  }
var x=document.forms["search"]["dto"].value;
if (x==null || x=="")
  {
  alert("You must enter an end date/time");
  return false;
  }
}
$('#resframe').load(function(){
	$('#go').removeAttr('disabled');
	var sheight = $(window).height();
    $('#resframe').animate({height: sheight + 'px'}, 500);
    $('.toggle_content').slideToggle(300);
    //this method increases the height to 72px
});

jQuery(document).ready(function() {
	$('#dfrom').datetimepicker({
	dateFormat: "yy-mm-dd",
	timeFormat: "HH:mm"
});
$('#dto').datetimepicker({
	dateFormat: "yy-mm-dd",
	timeFormat: "HH:mm"
});
  jQuery(".toggle_content").show();
  //toggle the component with class msg_body
  jQuery(".toggle").click(function()
  {
    jQuery(this).next(".toggle_content").slideToggle(300);
  });
});



</script>

<div id="nfconsole"></div>

</html>
