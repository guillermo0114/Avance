<?php


ob_start();
header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-Type: text/html; charset=utf-8");
session_start();
$_SESSION["loc"] = dirname($_SERVER['PHP_SELF']);
if($_SESSION["loc"]=="/") $_SESSION["loc"] = "";
$_SESSION["locr"] = dirname(__FILE__);
if($_SESSION["locr"]=="/") $_SESSION["locr"] = "";

require_once("globals.php");
require_once("db.php");

if (!isset($_GET["name"])) {
	if (ValidSession())
		DBLogOut($_SESSION["usertable"]["contestnumber"],
				 $_SESSION["usertable"]["usersitenumber"], $_SESSION["usertable"]["usernumber"],
				 $_SESSION["usertable"]["username"]=='admin');
	session_unset();
	session_destroy();
	session_start();
	$_SESSION["loc"] = dirname($_SERVER['PHP_SELF']);
	if($_SESSION["loc"]=="/") $_SESSION["loc"] = "";
	$_SESSION["locr"] = dirname(__FILE__);
	if($_SESSION["locr"]=="/") $_SESSION["locr"] = "";
}
if(isset($_GET["getsessionid"])) {
	echo session_id();
	exit;
}
ob_end_flush();

require_once('version.php');

?>
<title>BOCA Online Contest Administrator <?php echo $BOCAVERSION; ?> - Login</title>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel=stylesheet href="Css.php" type="text/css">
<script language="JavaScript" src="sha256.js"></script>
<script language="JavaScript">
function computeHASH()
{
	var userHASH, passHASH;
	userHASH = document.form1.name.value;
	passHASH = js_myhash(js_myhash(document.form1.password.value)+'<?php echo session_id(); ?>');
	document.form1.name.value = '';
	document.form1.password.value = '                                                                                 ';
	document.location = 'index.php?name='+userHASH+'&password='+passHASH;
}
</script>
<?php
if(function_exists("globalconf") && function_exists("sanitizeVariables")) {
  if(isset($_GET["name"]) && $_GET["name"] != "" ) {
	$name = $_GET["name"];
	$password = $_GET["password"];
	$usertable = DBLogIn($name, $password);
	if(!$usertable) {
		ForceLoad("index.php");
	}
	else {
		if(($ct = DBContestInfo($_SESSION["usertable"]["contestnumber"])) == null)
			ForceLoad("index.php");
		if($ct["contestlocalsite"]==$ct["contestmainsite"]) $main=true; else $main=false;
		if(isset($_GET['action']) && $_GET['action'] == 'scoretransfer') {
			echo "SCORETRANSFER OK";
		} else {
			if($main && $_SESSION["usertable"]["usertype"] == 'site') {
				MSGError('Direct login of this user is not allowed');
				unset($_SESSION["usertable"]);
				ForceLoad("index.php");
				exit;
			}
			echo "<script language=\"JavaScript\">\n";
			echo "document.location='" . $_SESSION["usertable"]["usertype"] . "/index.php';\n";
			echo "</script>\n";
		}
		exit;
	}
  }
} else {
  echo "<script language=\"JavaScript\">\n";
  echo "alert('Unable to load config files. Possible file permission problem in the BOCA directory.');\n";
  echo "</script>\n";
}
?>
</head>
<body onload="document.form1.submit()">
<table width="100%" height="100%" border="0">
  <tr align="center" valign="middle">
    <td>
      <form name="form1" action="javascript:computeHASH()">
        <div align="center">
          <table border="0" align="center">
            <tr>
              <td nowrap>
                <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1">
				BOCA Login</font></div>
              </td>
            </tr>
            <tr>
              <td valign="top">
                <table border="0" align="left">
                  <tr>
                    <td><font face="Verdana, Arial, Helvetica, sans-serif" >
                      Name
                      </font></td>
                    <td>
                      <input type="text" name="name" value="score">
                    </td>
                  </tr>
                  <tr>
                    <td><font face="Verdana, Arial, Helvetica, sans-serif" >Password</font></td>
                    <td>
                      <input type="password" name="password" value="score">
                    </td>
                  </tr>
                </table>
                <input type="submit" name="Submit" value="Login">
              </td>
            </tr>
          </table>
        </div>
      </form>
    </td>
  </tr>
</table>
<?php include('footnote.php'); ?>
