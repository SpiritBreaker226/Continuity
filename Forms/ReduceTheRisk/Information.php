<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/EditionTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>My Continuity Plans - Information Technology - Continuity Inc. - Disaster Recovery Solutions</title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" type="text/css" href="../../CSS/MasterCSS.css" media="screen" />
<script src="../../javascript/MainJS.js" type="text/javascript"></script>
<?php require_once('../../PurePHP/LoginControl.php');?>
<?php require_once('../../Connections/conContinuty.php'); ?>

<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
<?php 		
//checks if the user whats to log off
if (isset($_GET['logoff']))
	logOff(substr($_SERVER['REQUEST_URI'],1));

//checks if the user is logged in as only those that have an account can do the forms
if (getUserID() == 0)
	header("Location: ../../LogIn.php?section=LogIn&Footer=1&accesscheck=Profile.php");

$UserID = getUserID();//holds the user ID
$SectionID = "";//holds the Section ID for this page
$SubFolder = "";//holds the Sub Folder that all images uses to display
$strEdition = "Basic";//Holds the Edition the uses has selected
$strBackgroudColor = "lblBasicBackgroundColor";//Holds the Solution Color Backgorund

if($_GET['Section'] == "")
	$SectionID = $_POST['hfSection'];
else
	$SectionID = $_GET['Section'];

//checks if there is any SQL Injects chars in the URL
if(strpos($SectionID,"--") || strpos($SectionID,"\\") || strpos($SectionID,"'") || strpos($SectionID == "^"))
	header("Location: ../index.php");
		
if($_GET['SubFolder'] == "")
	$SubFolder = $_POST['hfSubFolder'];
else
	$SubFolder = $_GET['SubFolder'];

mysql_select_db($database_conContinuty, $conContinuty);
$LoginRS = mysql_query("SELECT * FROM users WHERE users.id=".$UserID, $conContinuty) or die("User: ".mysql_error());
$row_loginFoundUser = mysql_fetch_assoc($LoginRS);
				  
//does a selection for the users data for this form
mysql_select_db($database_conContinuty, $conContinuty); 
$rsPlans = mysql_query("SELECT * FROM continuityplans WHERE id = ".$SectionID, $conContinuty) or die("Plans: ".mysql_error());
$row_rsPlans = mysql_fetch_assoc($rsPlans);

//checks whick version the users is going to used
if($row_loginFoundUser['Solution'] == 3)
{
	$strEdition = "Enterprise";
	$strBackgroudColor = "lblEnterBackgroundColor";
}//end of if
else if ($row_loginFoundUser['Solution'] == 2)
{
	$strEdition = "Standard";
	$strBackgroudColor = " lblStandardBackgroundColor";
}//end of else if
				  
/* sets the color of this Step Section
	1 = Basic Colour
	2 = Standard Colour
	3 = Continuity Colour
	4 = Enterprise Color
*/

$strEditionColour = "lblBasicColor";//Holds the Edition the uses has selected

//checks whick version the users is going to used
if($row_rsPlans['stepNum'] == 2)
	$strEditionColour = "lblStandardColor";
else if ($row_rsPlans['stepNum'] == 3)
	$strEditionColour = "lblContinuityColor";
else if ($row_rsPlans['stepNum'] == 4)
	$strEditionColour = "lblEnterColor";
	
//does a selection for the users data for this form
mysql_select_db($database_conContinuty, $conContinuty);
$rsForm = mysql_query("SELECT * FROM ".$row_rsPlans['TableName']." WHERE UserID = ".$UserID, $conContinuty) or die("Scope: ".mysql_error());
$row_rsForm = mysql_fetch_assoc($rsForm);
$totalRows_rsForm = mysql_num_rows($rsForm); ?>
<!-- InstanceBeginEditable name="BackUpForSelect" -->
<?php 
//checks if there is any data for this user for this form
if ($totalRows_rsForm > 0)
{
	//does another selection to get the updated data
	  mysql_select_db($database_conContinuty, $conContinuty);
	  $rsForm2 = mysql_query("SELECT * FROM c2information2 WHERE C2ID= ".$row_rsForm['C2ID'], $conContinuty) or die("Scope3: ".mysql_error());
	  $row_rsForm2 = mysql_fetch_assoc($rsForm2);
	  $totalRows_rsForm2 = mysql_num_rows($rsForm2);
}//end of if ?>
<!-- InstanceEndEditable -->
<?php 
//forces the pages to come here to process the page
$Form = $_SERVER['PHP_SELF'];

//checks if the fields that need data do have some data in them
if($_POST['hfSubmit'] != '')
{?>
	<!-- InstanceBeginEditable name="TopAddstionalFunction" -->
    
    <!-- InstanceEndEditable -->
	<?php //checks if there is any data for this user for this form
	if ($totalRows_rsForm == 0)
	{
		//creates a row so that that row will now be update by the next statement
		mysql_select_db($database_conContinuty, $conContinuty);
		mysql_query("INSERT INTO ".$row_rsPlans['TableName']." SET UserID = ".$UserID, $conContinuty) or die(mysql_error());
	}//end of if
	
	//does an update the table
	mysql_select_db($database_conContinuty, $conContinuty); ?>
	<!-- InstanceBeginEditable name="CPUpdate" --><?php mysql_query("UPDATE ".$row_rsPlans['TableName']." SET IT_1SW01 ='".str_replace("'","''",$_POST['IT_1SW01'])."',IT_1SW02 ='".str_replace("'","''",$_POST['IT_1SW02'])."',IT_1SW03 ='".str_replace("'","''",$_POST['IT_1SW03'])."',IT_1SW04 ='".str_replace("'","''",$_POST['IT_1SW04'])."',IT_1SW05 ='".str_replace("'","''",$_POST['IT_1SW05'])."',IT_2SW01 ='".str_replace("'","''",$_POST['IT_2SW01'])."',IT_2SW02 ='".str_replace("'","''",$_POST['IT_2SW02'])."',IT_2SW03 ='".str_replace("'","''",$_POST['IT_2SW03'])."',IT_2SW04 ='".str_replace("'","''",$_POST['IT_2SW04'])."',IT_2SW05 ='".str_replace("'","''",$_POST['IT_2SW05'])."',IT_3SW01 ='".str_replace("'","''",$_POST['IT_3SW01'])."',IT_3SW02 ='".str_replace("'","''",$_POST['IT_3SW02'])."',IT_3SW03 ='".str_replace("'","''",$_POST['IT_3SW03'])."',IT_3SW04 ='".str_replace("'","''",$_POST['IT_3SW04'])."',IT_3SW05 ='".str_replace("'","''",$_POST['IT_3SW05'])."',IT_4SW01 ='".str_replace("'","''",$_POST['IT_4SW01'])."',IT_4SW02 ='".str_replace("'","''",$_POST['IT_4SW02'])."',IT_4SW03 ='".str_replace("'","''",$_POST['IT_4SW03'])."',IT_4SW04 ='".str_replace("'","''",$_POST['IT_4SW04'])."',IT_4SW05 ='".str_replace("'","''",$_POST['IT_4SW05'])."',IT_5SW01 ='".str_replace("'","''",$_POST['IT_5SW01'])."',IT_5SW02 ='".str_replace("'","''",$_POST['IT_5SW02'])."',IT_5SW03 ='".str_replace("'","''",$_POST['IT_5SW03'])."',IT_5SW04 ='".str_replace("'","''",$_POST['IT_5SW04'])."',IT_5SW05 ='".str_replace("'","''",$_POST['IT_5SW05'])."',IT_1HW01 ='".str_replace("'","''",$_POST['IT_1HW01'])."',IT_1HW02 ='".str_replace("'","''",$_POST['IT_1HW02'])."',IT_1HW03 ='".str_replace("'","''",$_POST['IT_1HW03'])."',IT_1HW04 ='".str_replace("'","''",$_POST['IT_1HW04'])."',IT_1HW05 ='".str_replace("'","''",$_POST['IT_1HW05'])."',IT_2HW01 ='".str_replace("'","''",$_POST['IT_2HW01'])."',IT_2HW02 ='".str_replace("'","''",$_POST['IT_2HW02'])."',IT_2HW03 ='".str_replace("'","''",$_POST['IT_2HW03'])."',IT_2HW04 ='".str_replace("'","''",$_POST['IT_2HW04'])."',IT_2HW05 ='".str_replace("'","''",$_POST['IT_2HW05'])."',IT_3HW01 ='".str_replace("'","''",$_POST['IT_3HW01'])."',IT_3HW02 ='".str_replace("'","''",$_POST['IT_3HW02'])."',IT_3HW03 ='".str_replace("'","''",$_POST['IT_3HW03'])."',IT_3HW04 ='".str_replace("'","''",$_POST['IT_3HW04'])."',IT_3HW05 ='".str_replace("'","''",$_POST['IT_3HW05'])."',IT_4HW01 ='".str_replace("'","''",$_POST['IT_4HW01'])."',IT_4HW02 ='".str_replace("'","''",$_POST['IT_4HW02'])."',IT_4HW03 ='".str_replace("'","''",$_POST['IT_4HW03'])."',IT_4HW04 ='".str_replace("'","''",$_POST['IT_4HW04'])."',IT_4HW05 ='".str_replace("'","''",$_POST['IT_4HW05'])."',IT_5HW01 ='".str_replace("'","''",$_POST['IT_5HW01'])."',IT_5HW02 ='".str_replace("'","''",$_POST['IT_5HW02'])."',IT_5HW03 ='".str_replace("'","''",$_POST['IT_5HW03'])."',IT_5HW04 ='".str_replace("'","''",$_POST['IT_5HW04'])."',IT_5HW05 ='".str_replace("'","''",$_POST['IT_5HW05'])."',IT_1SR01 ='".str_replace("'","''",$_POST['IT_1SR01'])."',IT_1SR02 ='".str_replace("'","''",$_POST['IT_1SR02'])."',IT_1SR03 ='".str_replace("'","''",$_POST['IT_1SR03'])."',IT_1SR04 ='".str_replace("'","''",$_POST['IT_1SR04'])."',IT_1SR05 ='".str_replace("'","''",$_POST['IT_1SR05'])."',IT_2SR01 ='".str_replace("'","''",$_POST['IT_2SR01'])."',IT_2SR02 ='".str_replace("'","''",$_POST['IT_2SR02'])."',IT_2SR03 ='".str_replace("'","''",$_POST['IT_2SR03'])."',IT_2SR04 ='".str_replace("'","''",$_POST['IT_2SR04'])."',IT_2SR05 ='".str_replace("'","''",$_POST['IT_2SR05'])."',IT_1TP01 ='".str_replace("'","''",$_POST['IT_1TP01'])."',IT_1TP02 ='".str_replace("'","''",$_POST['IT_1TP02'])."',IT_1TP03 ='".str_replace("'","''",$_POST['IT_1TP03'])."',IT_1TP04 ='".str_replace("'","''",$_POST['IT_1TP04'])."',IT_1IN01 ='".str_replace("'","''",$_POST['IT_1IN01'])."',IT_1IN02 ='".str_replace("'","''",$_POST['IT_1IN02'])."',IT_1IN03 ='".str_replace("'","''",$_POST['IT_1IN03'])."',IT_1IN04 ='".str_replace("'","''",$_POST['IT_1IN04'])."',IT_1IN05 ='".str_replace("'","''",$_POST['IT_1IN05'])."',IT_1IN06 ='".str_replace("'","''",$_POST['IT_1IN06'])."',IT_1IN07 ='".str_replace("'","''",$_POST['IT_1IN07'])."',IT_1IN08 ='".str_replace("'","''",$_POST['IT_1IN08'])."',IT_1IN09 ='".str_replace("'","''",$_POST['IT_1IN09'])."',IT_1IN10 ='".str_replace("'","''",$_POST['IT_1IN10'])."',IT_1IN11 ='".str_replace("'","''",$_POST['IT_1IN11'])."',IT_1IN12 ='".str_replace("'","''",$_POST['IT_1IN12'])."',IT_1IN13 ='".str_replace("'","''",$_POST['IT_1IN13'])."',IT_1IN14 ='".str_replace("'","''",$_POST['IT_1IN14'])."',IT_1IN15 ='".str_replace("'","''",$_POST['IT_1IN15'])."',IT_1IN16 ='".str_replace("'","''",$_POST['IT_1IN16'])."',IT_1IN17 ='".str_replace("'","''",$_POST['IT_1IN17'])."',IT_1IN18 ='".str_replace("'","''",$_POST['IT_1IN18'])."',IT_1IN19 ='".str_replace("'","''",$_POST['IT_1IN19'])."',IT_1IN20 ='".str_replace("'","''",$_POST['IT_1IN20'])."',IT_1IN21 ='".str_replace("'","''",$_POST['IT_1IN21'])."',IT_1IN22 ='".str_replace("'","''",$_POST['IT_1IN22'])."',IT_1IN23 ='".str_replace("'","''",$_POST['IT_1IN23'])."',IT_1IN24 ='".str_replace("'","''",$_POST['IT_1IN24'])."',IT_1IN25 ='".str_replace("'","''",$_POST['IT_1IN25'])."',IT_1IN26 ='".str_replace("'","''",$_POST['IT_1IN26'])."',IT_1IN27 ='".str_replace("'","''",$_POST['IT_1IN27'])."',IT_1IN28 ='".str_replace("'","''",$_POST['IT_1IN28'])."',IT_1IN29 ='".str_replace("'","''",$_POST['IT_1IN29'])."',IT_1IN30 ='".str_replace("'","''",$_POST['IT_1IN30'])."',IT_1IN31 ='".str_replace("'","''",$_POST['IT_1IN31'])."',IT_1IN32 ='".str_replace("'","''",$_POST['IT_1IN32'])."',IT_1IN33 ='".str_replace("'","''",$_POST['IT_1IN33'])."',IT_1IN34 ='".str_replace("'","''",$_POST['IT_1IN34'])."',IT_1IN35 ='".str_replace("'","''",$_POST['IT_1IN35'])."',IT_1IN36 ='".str_replace("'","''",$_POST['IT_1IN36'])."',IT_1IN37 ='".str_replace("'","''",$_POST['IT_1IN37'])."',IT_1IN38 ='".str_replace("'","''",$_POST['IT_1IN38'])."',IT_1IN39 ='".str_replace("'","''",$_POST['IT_1IN39'])."',IT_1IN40 ='".str_replace("'","''",$_POST['IT_1IN40'])."',IT_1IN41 ='".str_replace("'","''",$_POST['IT_1IN41'])."',IT_1IN42 ='".str_replace("'","''",$_POST['IT_1IN42'])."',IT_1IN43 ='".str_replace("'","''",$_POST['IT_1IN43'])."',IT_1IN44 ='".str_replace("'","''",$_POST['IT_1IN44'])."',IT_1IN45 ='".str_replace("'","''",$_POST['IT_1IN45'])."',IT_1IN46 ='".str_replace("'","''",$_POST['IT_1IN46'])."',IT_1IN47 ='".str_replace("'","''",$_POST['IT_1IN47'])."',IT_1IN48 ='".str_replace("'","''",$_POST['IT_1IN48'])."',IT_1IN49 ='".str_replace("'","''",$_POST['IT_1IN49'])."',IT_1IN50 ='".str_replace("'","''",$_POST['IT_1IN50'])."',IT_1IN51 ='".str_replace("'","''",$_POST['IT_1IN51'])."',IT_1IN52 ='".str_replace("'","''",$_POST['IT_1IN52'])."',IT_1IN53 ='".str_replace("'","''",$_POST['IT_1IN53'])."',IT_1IN54 ='".str_replace("'","''",$_POST['IT_1IN54'])."',IT_1IN55 ='".str_replace("'","''",$_POST['IT_1IN55'])."',IT_1IN56 ='".str_replace("'","''",$_POST['IT_1IN56'])."',IT_1IN57 ='".str_replace("'","''",$_POST['IT_1IN57'])."',IT_EMPro01 ='".str_replace("'","''",$_POST['IT_EMPro01'])."',IT_EMP01 ='".str_replace("'","''",$_POST['IT_EMP01'])."',IT_USER01 ='".str_replace("'","''",$_POST['IT_USER01'])."',IT_PASS01 ='".str_replace("'","''",$_POST['IT_PASS01'])."',IT_EMP02 ='".str_replace("'","''",$_POST['IT_EMP02'])."',IT_USER02 ='".str_replace("'","''",$_POST['IT_USER02'])."',IT_PASS02 ='".str_replace("'","''",$_POST['IT_PASS02'])."',IT_EMP03 ='".str_replace("'","''",$_POST['IT_EMP03'])."',IT_USER03 ='".str_replace("'","''",$_POST['IT_USER03'])."',IT_PASS03 ='".str_replace("'","''",$_POST['IT_PASS03'])."',IT_EMP04 ='".str_replace("'","''",$_POST['IT_EMP04'])."',IT_USER04 ='".str_replace("'","''",$_POST['IT_USER04'])."',IT_PASS04 ='".str_replace("'","''",$_POST['IT_PASS04'])."',IT_EMP05 ='".str_replace("'","''",$_POST['IT_EMP05'])."',IT_USER05 ='".str_replace("'","''",$_POST['IT_USER05'])."',IT_PASS05 ='".str_replace("'","''",$_POST['IT_PASS05'])."',IT_EMP06 ='".str_replace("'","''",$_POST['IT_EMP06'])."',IT_USER06 ='".str_replace("'","''",$_POST['IT_USER06'])."',IT_PASS06 ='".str_replace("'","''",$_POST['IT_PASS06'])."',IT_EMP07 ='".str_replace("'","''",$_POST['IT_EMP07'])."',IT_USER07 ='".str_replace("'","''",$_POST['IT_USER07'])."',IT_PASS07 ='".str_replace("'","''",$_POST['IT_PASS07'])."',IT_EMP08 ='".str_replace("'","''",$_POST['IT_EMP08'])."',IT_USER08 ='".str_replace("'","''",$_POST['IT_USER08'])."',IT_PASS08 ='".str_replace("'","''",$_POST['IT_PASS08'])."',IT_EMP09 ='".str_replace("'","''",$_POST['IT_EMP09'])."',IT_USER09 ='".str_replace("'","''",$_POST['IT_USER09'])."',IT_PASS09 ='".str_replace("'","''",$_POST['IT_PASS09'])."',IT_EMP10 ='".str_replace("'","''",$_POST['IT_EMP10'])."',IT_USER10 ='".str_replace("'","''",$_POST['IT_USER10'])."',IT_PASS10 ='".str_replace("'","''",$_POST['IT_PASS10'])."',IT_EMP11 ='".str_replace("'","''",$_POST['IT_EMP11'])."',IT_USER11 ='".str_replace("'","''",$_POST['IT_USER11'])."',IT_PASS11 ='".str_replace("'","''",$_POST['IT_PASS11'])."',IT_EMP12 ='".str_replace("'","''",$_POST['IT_EMP12'])."',IT_USER12 ='".str_replace("'","''",$_POST['IT_USER12'])."',IT_PASS12 ='".str_replace("'","''",$_POST['IT_PASS12'])."',IT_EMP13 ='".str_replace("'","''",$_POST['IT_EMP13'])."',IT_USER13 ='".str_replace("'","''",$_POST['IT_USER13'])."',IT_PASS13 ='".str_replace("'","''",$_POST['IT_PASS13'])."',IT_EMP14 ='".str_replace("'","''",$_POST['IT_EMP14'])."',IT_USER14 ='".str_replace("'","''",$_POST['IT_USER14'])."',IT_PASS14 ='".str_replace("'","''",$_POST['IT_PASS14'])."',IT_EMP15 ='".str_replace("'","''",$_POST['IT_EMP15'])."',IT_USER15 ='".str_replace("'","''",$_POST['IT_USER15'])."',IT_PASS15 ='".str_replace("'","''",$_POST['IT_PASS15'])."',IT_EMP16 ='".str_replace("'","''",$_POST['IT_EMP16'])."',IT_USER16 ='".str_replace("'","''",$_POST['IT_USER16'])."',IT_PASS16 ='".str_replace("'","''",$_POST['IT_PASS16'])."',IT_EMP17 ='".str_replace("'","''",$_POST['IT_EMP17'])."',IT_USER17 ='".str_replace("'","''",$_POST['IT_USER17'])."',IT_PASS17 ='".str_replace("'","''",$_POST['IT_PASS17'])."',IT_EMP18 ='".str_replace("'","''",$_POST['IT_EMP18'])."',IT_USER18 ='".str_replace("'","''",$_POST['IT_USER18'])."',IT_PASS18 ='".str_replace("'","''",$_POST['IT_PASS18'])."',IT_EMP19 ='".str_replace("'","''",$_POST['IT_EMP19'])."',IT_USER19 ='".str_replace("'","''",$_POST['IT_USER19'])."',IT_PASS19 ='".str_replace("'","''",$_POST['IT_PASS19'])."',IT_EMP20 ='".str_replace("'","''",$_POST['IT_EMP20'])."',IT_USER20 ='".str_replace("'","''",$_POST['IT_USER20'])."',IT_PASS20 ='".str_replace("'","''",$_POST['IT_PASS20'])."',IT_EMPro02 ='".str_replace("'","''",$_POST['IT_EMPro02'])."',IT_2EMP01 ='".str_replace("'","''",$_POST['IT_2EMP01'])."',IT_2USER01 ='".str_replace("'","''",$_POST['IT_2USER01'])."',IT_2PASS01 ='".str_replace("'","''",$_POST['IT_2PASS01'])."',IT_2EMP02 ='".str_replace("'","''",$_POST['IT_2EMP02'])."',IT_2USER02 ='".str_replace("'","''",$_POST['IT_2USER02'])."',IT_2PASS02 ='".str_replace("'","''",$_POST['IT_2PASS02'])."',IT_2EMP03 ='".str_replace("'","''",$_POST['IT_2EMP03'])."',IT_2USER03 ='".str_replace("'","''",$_POST['IT_2USER03'])."',IT_2PASS03 ='".str_replace("'","''",$_POST['IT_2PASS03'])."',IT_2EMP04 ='".str_replace("'","''",$_POST['IT_2EMP04'])."',IT_2USER04 ='".str_replace("'","''",$_POST['IT_2USER04'])."',IT_2PASS04 ='".str_replace("'","''",$_POST['IT_2PASS04'])."',IT_2EMP05 ='".str_replace("'","''",$_POST['IT_2EMP05'])."',IT_2USER05 ='".str_replace("'","''",$_POST['IT_2USER05'])."',IT_2PASS05 ='".str_replace("'","''",$_POST['IT_2PASS05'])."',IT_2EMP06 ='".str_replace("'","''",$_POST['IT_2EMP06'])."',IT_2USER06 ='".str_replace("'","''",$_POST['IT_2USER06'])."',IT_2PASS06 ='".str_replace("'","''",$_POST['IT_2PASS06'])."',IT_2EMP07 ='".str_replace("'","''",$_POST['IT_2EMP07'])."',IT_2USER07 ='".str_replace("'","''",$_POST['IT_2USER07'])."',IT_2PASS07 ='".str_replace("'","''",$_POST['IT_2PASS07'])."',IT_2EMP08 ='".str_replace("'","''",$_POST['IT_2EMP08'])."',IT_2USER08 ='".str_replace("'","''",$_POST['IT_2USER08'])."',IT_2PASS08 ='".str_replace("'","''",$_POST['IT_2PASS08'])."',IT_2EMP09 ='".str_replace("'","''",$_POST['IT_2EMP09'])."',IT_2USER09 ='".str_replace("'","''",$_POST['IT_2USER09'])."',IT_2PASS09 ='".str_replace("'","''",$_POST['IT_2PASS09'])."',IT_2EMP10 ='".str_replace("'","''",$_POST['IT_2EMP10'])."',IT_2USER10 ='".str_replace("'","''",$_POST['IT_2USER10'])."',IT_2PASS10 ='".str_replace("'","''",$_POST['IT_2PASS10'])."',IT_2EMP11 ='".str_replace("'","''",$_POST['IT_2EMP11'])."',IT_2USER11 ='".str_replace("'","''",$_POST['IT_2USER11'])."',IT_2PASS11 ='".str_replace("'","''",$_POST['IT_2PASS11'])."',IT_2EMP12 ='".str_replace("'","''",$_POST['IT_2EMP12'])."',IT_2USER12 ='".str_replace("'","''",$_POST['IT_2USER12'])."',IT_2PASS12 ='".str_replace("'","''",$_POST['IT_2PASS12'])."',IT_2EMP13 ='".str_replace("'","''",$_POST['IT_2EMP13'])."',IT_2USER13 ='".str_replace("'","''",$_POST['IT_2USER13'])."',IT_2PASS13 ='".str_replace("'","''",$_POST['IT_2PASS13'])."',IT_2EMP14 ='".str_replace("'","''",$_POST['IT_2EMP14'])."',IT_2USER14 ='".str_replace("'","''",$_POST['IT_2USER14'])."',IT_2PASS14 ='".str_replace("'","''",$_POST['IT_2PASS14'])."',IT_2EMP15 ='".str_replace("'","''",$_POST['IT_2EMP15'])."',IT_2USER15 ='".str_replace("'","''",$_POST['IT_2USER15'])."',IT_2PASS15 ='".str_replace("'","''",$_POST['IT_2PASS15'])."',IT_2EMP16 ='".str_replace("'","''",$_POST['IT_2EMP16'])."',IT_2USER16 ='".str_replace("'","''",$_POST['IT_2USER16'])."',IT_2PASS16 ='".str_replace("'","''",$_POST['IT_2PASS16'])."',IT_2EMP17 ='".str_replace("'","''",$_POST['IT_2EMP17'])."',IT_2USER17 ='".str_replace("'","''",$_POST['IT_2USER17'])."',IT_2PASS17 ='".str_replace("'","''",$_POST['IT_2PASS17'])."',IT_2EMP18 ='".str_replace("'","''",$_POST['IT_2EMP18'])."',IT_2USER18 ='".str_replace("'","''",$_POST['IT_2USER18'])."',IT_2PASS18 ='".str_replace("'","''",$_POST['IT_2PASS18'])."',IT_2EMP19 ='".str_replace("'","''",$_POST['IT_2EMP19'])."',IT_2USER19 ='".str_replace("'","''",$_POST['IT_2USER19'])."',IT_2PASS19 ='".str_replace("'","''",$_POST['IT_2PASS19'])."',IT_2EMP20 ='".str_replace("'","''",$_POST['IT_2EMP20'])."',IT_2USER20 ='".str_replace("'","''",$_POST['IT_2USER20'])."',IT_2PASS20 ='".str_replace("'","''",$_POST['IT_2PASS20'])."',IT_DS01 ='".str_replace("'","''",$_POST['IT_DS01'])."',IT_DS02 ='".str_replace("'","''",$_POST['IT_DS02'])."',IT_DS03 ='".str_replace("'","''",$_POST['IT_DS03'])."',IT_DS04 ='".str_replace("'","''",$_POST['IT_DS04'])."',IT_DS05 ='".str_replace("'","''",$_POST['IT_DS05'])."',IT_DS06 ='".str_replace("'","''",$_POST['IT_DS06'])."',IT_DS07 ='".str_replace("'","''",$_POST['IT_DS07'])."',IT_web01 ='".str_replace("'","''",$_POST['IT_web01'])."',IT_web02 ='".str_replace("'","''",$_POST['IT_web02'])."',IT_web03 ='".str_replace("'","''",$_POST['IT_web03'])."',IT_web04 ='".str_replace("'","''",$_POST['IT_web04'])."',IT_sum01 ='".str_replace("'","''",$_POST['IT_sum01'])."',IT_sum02 ='".str_replace("'","''",$_POST['IT_sum02'])."',IT_sum03 ='".str_replace("'","''",$_POST['IT_sum03'])."',IT_OFF01 ='".str_replace("'","''",$_POST['IT_OFF01'])."',IT_OFF02 ='".str_replace("'","''",$_POST['IT_OFF02'])."',IT_OFF03 ='".str_replace("'","''",$_POST['IT_OFF03'])."',IT_OFF04 ='".str_replace("'","''",$_POST['IT_OFF04'])."',IT_OFF05 ='".str_replace("'","''",$_POST['IT_OFF05'])."',IT_SUPP01 ='".str_replace("'","''",$_POST['IT_SUPP01'])."',IT_SUPP02 ='".str_replace("'","''",$_POST['IT_SUPP02'])."',IT_SUPP03 ='".str_replace("'","''",$_POST['IT_SUPP03'])."',IT_SUPP04 ='".str_replace("'","''",$_POST['IT_SUPP04'])."',IT_SUPP05 ='".str_replace("'","''",$_POST['IT_SUPP05'])."',IT_SUPP06 ='".str_replace("'","''",$_POST['IT_SUPP06'])."',IT_SUPP07 ='".str_replace("'","''",$_POST['IT_SUPP07'])."',IT_SUPP08 ='".str_replace("'","''",$_POST['IT_SUPP08'])."',IT_SUPP09 ='".str_replace("'","''",$_POST['IT_SUPP09'])."',IT_CI01 ='".str_replace("'","''",$_POST['IT_CI01'])."', IT_CI02 ='".str_replace("'","''",$_POST['IT_CI02'])."', IT_CI03 ='".str_replace("'","''",$_POST['IT_CI03'])."', IT_CI04 ='".str_replace("'","''",$_POST['IT_CI04'])."', IT_CI05 ='".str_replace("'","''",$_POST['IT_CI05'])."', IT_CI06 ='".str_replace("'","''",$_POST['IT_CI06'])."', IT_CI07 ='".str_replace("'","''",$_POST['IT_CI07'])."', IT_CI08 ='".str_replace("'","''",$_POST['IT_CI08'])."', IT_CI09 ='".str_replace("'","''",$_POST['IT_CI09'])."', IT_CI10 ='".str_replace("'","''",$_POST['IT_CI10'])."', IT_CI11 ='".str_replace("'","''",$_POST['IT_CI11'])."', IT_CI12 ='".str_replace("'","''",$_POST['IT_CI12'])."', IT_CI13 ='".str_replace("'","''",$_POST['IT_CI13'])."' WHERE UserID = ".$UserID, $conContinuty) or die(mysql_error()); 
	
	/*
	
		Because of the insance amount of fields this one form uses it has to go on two different tables as SQL only allows 1000 fields for 
		one table, this is nuts
	
	*/
	
	//does another selection to get the updated data
	mysql_select_db($database_conContinuty, $conContinuty);
	$rsForm = mysql_query("SELECT C2ID FROM ".$row_rsPlans['TableName']." WHERE UserID = ".$UserID, $conContinuty) or die("To Information: ".mysql_error());
	$row_rsForm = mysql_fetch_assoc($rsForm);
	$totalRows_rsForm = mysql_num_rows($rsForm);
	
	//checks if there is any data for this user for this form
	if ($totalRows_rsForm2 == 0)
	{
		//creates a row so that that row will now be update by the next statement
		mysql_select_db($database_conContinuty, $conContinuty);
		mysql_query("INSERT INTO C2Information2 SET C2ID = ".$row_rsForm['C2ID'], $conContinuty) or die("To Information2: ".mysql_error());
	}//end of if
	
	mysql_select_db($database_conContinuty, $conContinuty);
	mysql_query("UPDATE C2Information2 SET ITStd_001 ='".str_replace("'","''",$_POST['ITStd_001'])."',ITStd_002 ='".str_replace("'","''",$_POST['ITStd_002'])."',ITStd_003 ='".str_replace("'","''",$_POST['ITStd_003'])."',ITStd_004 ='".str_replace("'","''",$_POST['ITStd_004'])."',ITStd_005 ='".str_replace("'","''",$_POST['ITStd_005'])."',ITStd_006 ='".str_replace("'","''",$_POST['ITStd_006'])."',ITStd_007 ='".str_replace("'","''",$_POST['ITStd_007'])."',ITStd_008 ='".str_replace("'","''",$_POST['ITStd_008'])."',ITStd_009 ='".str_replace("'","''",$_POST['ITStd_009'])."',ITStd_010 ='".str_replace("'","''",$_POST['ITStd_010'])."',ITStd_011 ='".str_replace("'","''",$_POST['ITStd_011'])."',ITStd_012 ='".str_replace("'","''",$_POST['ITStd_012'])."',ITStd_013 ='".str_replace("'","''",$_POST['ITStd_013'])."',ITStd_014 ='".str_replace("'","''",$_POST['ITStd_014'])."',ITStd_015 ='".str_replace("'","''",$_POST['ITStd_015'])."',ITStd_016 ='".str_replace("'","''",$_POST['ITStd_016'])."',ITStd_017 ='".str_replace("'","''",$_POST['ITStd_017'])."',ITStd_018 ='".str_replace("'","''",$_POST['ITStd_018'])."',ITStd_019 ='".str_replace("'","''",$_POST['ITStd_019'])."',ITStd_020 ='".str_replace("'","''",$_POST['ITStd_020'])."',ITStd_021 ='".str_replace("'","''",$_POST['ITStd_021'])."',ITStd_022 ='".str_replace("'","''",$_POST['ITStd_022'])."',ITStd_023 ='".str_replace("'","''",$_POST['ITStd_023'])."',ITStd_024 ='".str_replace("'","''",$_POST['ITStd_024'])."',ITStd_025 ='".str_replace("'","''",$_POST['ITStd_025'])."',ITStd_026 ='".str_replace("'","''",$_POST['ITStd_026'])."',ITStd_027 ='".str_replace("'","''",$_POST['ITStd_027'])."',ITStd_028 ='".str_replace("'","''",$_POST['ITStd_028'])."',ITStd_029 ='".str_replace("'","''",$_POST['ITStd_029'])."',ITStd_030 ='".str_replace("'","''",$_POST['ITStd_030'])."',ITStd_031 ='".str_replace("'","''",$_POST['ITStd_031'])."',ITStd_032 ='".str_replace("'","''",$_POST['ITStd_032'])."',ITStd_033 ='".str_replace("'","''",$_POST['ITStd_033'])."',ITStd_034 ='".str_replace("'","''",$_POST['ITStd_034'])."',ITStd_035 ='".str_replace("'","''",$_POST['ITStd_035'])."',ITStd_036 ='".str_replace("'","''",$_POST['ITStd_036'])."',ITStd_037 ='".str_replace("'","''",$_POST['ITStd_037'])."',ITStd_038 ='".str_replace("'","''",$_POST['ITStd_038'])."',ITStd_039 ='".str_replace("'","''",$_POST['ITStd_039'])."',ITStd_040 ='".str_replace("'","''",$_POST['ITStd_040'])."',ITStd_041 ='".str_replace("'","''",$_POST['ITStd_041'])."',ITStd_042 ='".str_replace("'","''",$_POST['ITStd_042'])."',ITStd_043 ='".str_replace("'","''",$_POST['ITStd_043'])."',ITStd_044 ='".str_replace("'","''",$_POST['ITStd_044'])."',ITStd_045 ='".str_replace("'","''",$_POST['ITStd_045'])."',ITStd_046 ='".str_replace("'","''",$_POST['ITStd_046'])."',ITStd_047 ='".str_replace("'","''",$_POST['ITStd_047'])."',ITStd_048 ='".str_replace("'","''",$_POST['ITStd_048'])."',ITStd_049 ='".str_replace("'","''",$_POST['ITStd_049'])."',ITStd_050 ='".str_replace("'","''",$_POST['ITStd_050'])."',ITStd_051 ='".str_replace("'","''",$_POST['ITStd_051'])."',ITStd_052 ='".str_replace("'","''",$_POST['ITStd_052'])."',ITStd_053 ='".str_replace("'","''",$_POST['ITStd_053'])."',ITStd_054 ='".str_replace("'","''",$_POST['ITStd_054'])."',ITStd_055 ='".str_replace("'","''",$_POST['ITStd_055'])."',ITStd_056 ='".str_replace("'","''",$_POST['ITStd_056'])."',ITStd_057 ='".str_replace("'","''",$_POST['ITStd_057'])."',ITStd_058 ='".str_replace("'","''",$_POST['ITStd_058'])."',ITStd_059 ='".str_replace("'","''",$_POST['ITStd_059'])."',ITStd_060 ='".str_replace("'","''",$_POST['ITStd_060'])."',ITStd_061 ='".str_replace("'","''",$_POST['ITStd_061'])."',ITStd_062 ='".str_replace("'","''",$_POST['ITStd_062'])."',ITStd_063 ='".str_replace("'","''",$_POST['ITStd_063'])."',ITStd_064 ='".str_replace("'","''",$_POST['ITStd_064'])."',ITStd_065 ='".str_replace("'","''",$_POST['ITStd_065'])."',ITStd_066 ='".str_replace("'","''",$_POST['ITStd_066'])."',ITStd_067 ='".str_replace("'","''",$_POST['ITStd_067'])."',ITStd_068 ='".str_replace("'","''",$_POST['ITStd_068'])."',ITStd_069 ='".str_replace("'","''",$_POST['ITStd_069'])."',ITStd_070 ='".str_replace("'","''",$_POST['ITStd_070'])."',ITStd_071 ='".str_replace("'","''",$_POST['ITStd_071'])."',ITStd_072 ='".str_replace("'","''",$_POST['ITStd_072'])."',ITStd_073 ='".str_replace("'","''",$_POST['ITStd_073'])."',ITStd_074 ='".str_replace("'","''",$_POST['ITStd_074'])."',ITStd_075 ='".str_replace("'","''",$_POST['ITStd_075'])."',ITStd_076 ='".str_replace("'","''",$_POST['ITStd_076'])."',ITStd_077 ='".str_replace("'","''",$_POST['ITStd_077'])."',ITStd_078 ='".str_replace("'","''",$_POST['ITStd_078'])."',ITStd_079 ='".str_replace("'","''",$_POST['ITStd_079'])."',ITStd_080 ='".str_replace("'","''",$_POST['ITStd_080'])."',ITStd_081 ='".str_replace("'","''",$_POST['ITStd_081'])."',ITStd_082 ='".str_replace("'","''",$_POST['ITStd_082'])."',ITStd_083 ='".str_replace("'","''",$_POST['ITStd_083'])."',ITStd_084 ='".str_replace("'","''",$_POST['ITStd_084'])."',ITStd_085 ='".str_replace("'","''",$_POST['ITStd_085'])."',ITStd_086 ='".str_replace("'","''",$_POST['ITStd_086'])."',ITStd_087 ='".str_replace("'","''",$_POST['ITStd_087'])."',ITStd_088 ='".str_replace("'","''",$_POST['ITStd_088'])."',ITStd_089 ='".str_replace("'","''",$_POST['ITStd_089'])."',ITStd_090 ='".str_replace("'","''",$_POST['ITStd_090'])."',ITStd_091 ='".str_replace("'","''",$_POST['ITStd_091'])."',ITStd_092 ='".str_replace("'","''",$_POST['ITStd_092'])."',ITStd_093 ='".str_replace("'","''",$_POST['ITStd_093'])."',ITStd_094 ='".str_replace("'","''",$_POST['ITStd_094'])."',ITStd_095 ='".str_replace("'","''",$_POST['ITStd_095'])."',ITStd_096 ='".str_replace("'","''",$_POST['ITStd_096'])."',ITStd_097 ='".str_replace("'","''",$_POST['ITStd_097'])."',ITStd_098 ='".str_replace("'","''",$_POST['ITStd_098'])."',ITStd_099 ='".str_replace("'","''",$_POST['ITStd_099'])."',ITStd_100 ='".str_replace("'","''",$_POST['ITStd_100'])."',ITStd_101 ='".str_replace("'","''",$_POST['ITStd_101'])."',ITStd_102 ='".str_replace("'","''",$_POST['ITStd_102'])."',ITStd_103 ='".str_replace("'","''",$_POST['ITStd_103'])."',ITStd_104 ='".str_replace("'","''",$_POST['ITStd_104'])."',ITStd_105 ='".str_replace("'","''",$_POST['ITStd_105'])."',ITStd_106 ='".str_replace("'","''",$_POST['ITStd_106'])."',ITStd_107 ='".str_replace("'","''",$_POST['ITStd_107'])."',ITStd_108 ='".str_replace("'","''",$_POST['ITStd_108'])."',ITStd_109 ='".str_replace("'","''",$_POST['ITStd_109'])."',ITStd_110 ='".str_replace("'","''",$_POST['ITStd_110'])."',ITStd_111 ='".str_replace("'","''",$_POST['ITStd_111'])."',ITStd_112 ='".str_replace("'","''",$_POST['ITStd_112'])."',ITStd_113 ='".str_replace("'","''",$_POST['ITStd_113'])."',ITStd_114 ='".str_replace("'","''",$_POST['ITStd_114'])."',ITStd_115 ='".str_replace("'","''",$_POST['ITStd_115'])."',ITStd_116 ='".str_replace("'","''",$_POST['ITStd_116'])."',ITStd_117 ='".str_replace("'","''",$_POST['ITStd_117'])."',ITStd_118 ='".str_replace("'","''",$_POST['ITStd_118'])."',ITStd_119 ='".str_replace("'","''",$_POST['ITStd_119'])."',ITStd_120 ='".str_replace("'","''",$_POST['ITStd_120'])."',ITStd_121 ='".str_replace("'","''",$_POST['ITStd_121'])."',ITStd_122 ='".str_replace("'","''",$_POST['ITStd_122'])."',ITStd_123 ='".str_replace("'","''",$_POST['ITStd_123'])."',ITStd_124 ='".str_replace("'","''",$_POST['ITStd_124'])."',ITStd_125 ='".str_replace("'","''",$_POST['ITStd_125'])."',ITStd_126 ='".str_replace("'","''",$_POST['ITStd_126'])."',ITStd_127 ='".str_replace("'","''",$_POST['ITStd_127'])."',ITStd_128 ='".str_replace("'","''",$_POST['ITStd_128'])."',ITStd_129 ='".str_replace("'","''",$_POST['ITStd_129'])."',ITStd_130 ='".str_replace("'","''",$_POST['ITStd_130'])."',ITStd_131 ='".str_replace("'","''",$_POST['ITStd_131'])."',ITStd_132 ='".str_replace("'","''",$_POST['ITStd_132'])."',ITStd_133 ='".str_replace("'","''",$_POST['ITStd_133'])."',ITStd_134 ='".str_replace("'","''",$_POST['ITStd_134'])."',ITStd_135 ='".str_replace("'","''",$_POST['ITStd_135'])."',ITStd_136 ='".str_replace("'","''",$_POST['ITStd_136'])."',ITStd_137 ='".str_replace("'","''",$_POST['ITStd_137'])."',ITStd_138 ='".str_replace("'","''",$_POST['ITStd_138'])."',ITStd_139 ='".str_replace("'","''",$_POST['ITStd_139'])."',ITStd_140 ='".str_replace("'","''",$_POST['ITStd_140'])."',ITStd_141 ='".str_replace("'","''",$_POST['ITStd_141'])."',ITStd_142 ='".str_replace("'","''",$_POST['ITStd_142'])."',ITStd_143 ='".str_replace("'","''",$_POST['ITStd_143'])."',ITStd_144 ='".str_replace("'","''",$_POST['ITStd_144'])."',ITStd_145 ='".str_replace("'","''",$_POST['ITStd_145'])."',ITStd_146 ='".str_replace("'","''",$_POST['ITStd_146'])."',ITStd_147 ='".str_replace("'","''",$_POST['ITStd_147'])."',ITStd_148 ='".str_replace("'","''",$_POST['ITStd_148'])."',ITStd_149 ='".str_replace("'","''",$_POST['ITStd_149'])."',ITStd_150 ='".str_replace("'","''",$_POST['ITStd_150'])."',ITStd_151 ='".str_replace("'","''",$_POST['ITStd_151'])."',ITStd_152 ='".str_replace("'","''",$_POST['ITStd_152'])."',ITStd_153 ='".str_replace("'","''",$_POST['ITStd_153'])."',ITStd_154 ='".str_replace("'","''",$_POST['ITStd_154'])."',ITStd_155 ='".str_replace("'","''",$_POST['ITStd_155'])."',ITStd_156 ='".str_replace("'","''",$_POST['ITStd_156'])."',ITStd_157 ='".str_replace("'","''",$_POST['ITStd_157'])."',ITStd_158 ='".str_replace("'","''",$_POST['ITStd_158'])."',ITStd_159 ='".str_replace("'","''",$_POST['ITStd_159'])."',ITStd_160 ='".str_replace("'","''",$_POST['ITStd_160'])."',ITStd_161 ='".str_replace("'","''",$_POST['ITStd_161'])."',ITStd_162 ='".str_replace("'","''",$_POST['ITStd_162'])."',ITStd_163 ='".str_replace("'","''",$_POST['ITStd_163'])."',ITStd_164 ='".str_replace("'","''",$_POST['ITStd_164'])."',ITStd_165 ='".str_replace("'","''",$_POST['ITStd_165'])."',ITStd_166 ='".str_replace("'","''",$_POST['ITStd_166'])."',ITStd_167 ='".str_replace("'","''",$_POST['ITStd_167'])."',ITStd_168 ='".str_replace("'","''",$_POST['ITStd_168'])."',ITStd_169 ='".str_replace("'","''",$_POST['ITStd_169'])."',ITStd_170 ='".str_replace("'","''",$_POST['ITStd_170'])."',ITStd_171 ='".str_replace("'","''",$_POST['ITStd_171'])."',ITStd_172 ='".str_replace("'","''",$_POST['ITStd_172'])."',ITStd_173 ='".str_replace("'","''",$_POST['ITStd_173'])."',ITStd_174 ='".str_replace("'","''",$_POST['ITStd_174'])."',ITStd_175 ='".str_replace("'","''",$_POST['ITStd_175'])."',ITStd_176 ='".str_replace("'","''",$_POST['ITStd_176'])."',ITStd_177 ='".str_replace("'","''",$_POST['ITStd_177'])."',ITStd_178 ='".str_replace("'","''",$_POST['ITStd_178'])."',ITStd_179 ='".str_replace("'","''",$_POST['ITStd_179'])."',ITStd_180 ='".str_replace("'","''",$_POST['ITStd_180'])."',ITStd_181 ='".str_replace("'","''",$_POST['ITStd_181'])."',ITStd_182 ='".str_replace("'","''",$_POST['ITStd_182'])."',ITStd_183 ='".str_replace("'","''",$_POST['ITStd_183'])."',ITStd_184 ='".str_replace("'","''",$_POST['ITStd_184'])."',ITStd_185 ='".str_replace("'","''",$_POST['ITStd_185'])."',ITStd_186 ='".str_replace("'","''",$_POST['ITStd_186'])."',ITStd_187 ='".str_replace("'","''",$_POST['ITStd_187'])."',ITStd_188 ='".str_replace("'","''",$_POST['ITStd_188'])."',ITStd_189 ='".str_replace("'","''",$_POST['ITStd_189'])."',ITStd_190 ='".str_replace("'","''",$_POST['ITStd_190'])."',ITStd_191 ='".str_replace("'","''",$_POST['ITStd_191'])."',ITStd_192 ='".str_replace("'","''",$_POST['ITStd_192'])."',ITStd_193 ='".str_replace("'","''",$_POST['ITStd_193'])."',ITStd_194 ='".str_replace("'","''",$_POST['ITStd_194'])."',ITStd_195 ='".str_replace("'","''",$_POST['ITStd_195'])."',ITStd_196 ='".str_replace("'","''",$_POST['ITStd_196'])."',ITStd_197 ='".str_replace("'","''",$_POST['ITStd_197'])."',ITStd_198 ='".str_replace("'","''",$_POST['ITStd_198'])."',ITStd_199 ='".str_replace("'","''",$_POST['ITStd_199'])."',ITStd_200 ='".str_replace("'","''",$_POST['ITStd_200'])."',ITStd_201 ='".str_replace("'","''",$_POST['ITStd_201'])."',ITStd_202 ='".str_replace("'","''",$_POST['ITStd_202'])."',ITStd_203 ='".str_replace("'","''",$_POST['ITStd_203'])."',ITStd_204 ='".str_replace("'","''",$_POST['ITStd_204'])."',ITStd_205 ='".str_replace("'","''",$_POST['ITStd_205'])."',ITStd_206 ='".str_replace("'","''",$_POST['ITStd_206'])."',ITStd_207 ='".str_replace("'","''",$_POST['ITStd_207'])."',ITStd_208 ='".str_replace("'","''",$_POST['ITStd_208'])."',ITStd_209 ='".str_replace("'","''",$_POST['ITStd_209'])."',ITStd_210 ='".str_replace("'","''",$_POST['ITStd_210'])."',ITStd_211 ='".str_replace("'","''",$_POST['ITStd_211'])."',ITStd_212 ='".str_replace("'","''",$_POST['ITStd_212'])."',ITStd_213 ='".str_replace("'","''",$_POST['ITStd_213'])."',ITStd_214 ='".str_replace("'","''",$_POST['ITStd_214'])."',ITStd_215 ='".str_replace("'","''",$_POST['ITStd_215'])."',ITStd_216 ='".str_replace("'","''",$_POST['ITStd_216'])."',ITStd_217 ='".str_replace("'","''",$_POST['ITStd_217'])."',ITStd_218 ='".str_replace("'","''",$_POST['ITStd_218'])."',ITStd_219 ='".str_replace("'","''",$_POST['ITStd_219'])."',ITStd_220 ='".str_replace("'","''",$_POST['ITStd_220'])."',ITStd_221 ='".str_replace("'","''",$_POST['ITStd_221'])."',ITStd_222 ='".str_replace("'","''",$_POST['ITStd_222'])."',ITStd_223 ='".str_replace("'","''",$_POST['ITStd_223'])."',ITStd_224 ='".str_replace("'","''",$_POST['ITStd_224'])."',ITStd_225 ='".str_replace("'","''",$_POST['ITStd_225'])."',ITStd_226 ='".str_replace("'","''",$_POST['ITStd_226'])."',ITStd_227 ='".str_replace("'","''",$_POST['ITStd_227'])."',ITStd_228 ='".str_replace("'","''",$_POST['ITStd_228'])."',ITStd_229 ='".str_replace("'","''",$_POST['ITStd_229'])."',ITStd_230 ='".str_replace("'","''",$_POST['ITStd_230'])."',ITStd_231 ='".str_replace("'","''",$_POST['ITStd_231'])."',ITStd_232 ='".str_replace("'","''",$_POST['ITStd_232'])."',ITStd_233 ='".str_replace("'","''",$_POST['ITStd_233'])."',ITStd_234 ='".str_replace("'","''",$_POST['ITStd_234'])."',ITStd_235 ='".str_replace("'","''",$_POST['ITStd_235'])."',ITStd_236 ='".str_replace("'","''",$_POST['ITStd_236'])."',ITStd_237 ='".str_replace("'","''",$_POST['ITStd_237'])."',ITStd_238 ='".str_replace("'","''",$_POST['ITStd_238'])."',ITStd_239 ='".str_replace("'","''",$_POST['ITStd_239'])."',ITStd_240 ='".str_replace("'","''",$_POST['ITStd_240'])."',ITStd_241 ='".str_replace("'","''",$_POST['ITStd_241'])."',ITStd_242 ='".str_replace("'","''",$_POST['ITStd_242'])."',ITStd_243 ='".str_replace("'","''",$_POST['ITStd_243'])."',ITStd_244 ='".str_replace("'","''",$_POST['ITStd_244'])."',ITStd_245 ='".str_replace("'","''",$_POST['ITStd_245'])."',ITStd_246 ='".str_replace("'","''",$_POST['ITStd_246'])."',ITStd_247 ='".str_replace("'","''",$_POST['ITStd_247'])."',ITStd_248 ='".str_replace("'","''",$_POST['ITStd_248'])."',ITStd_249 ='".str_replace("'","''",$_POST['ITStd_249'])."',ITStd_250 ='".str_replace("'","''",$_POST['ITStd_250'])."',ITStd_251 ='".str_replace("'","''",$_POST['ITStd_251'])."',ITStd_252 ='".str_replace("'","''",$_POST['ITStd_252'])."',ITStd_253 ='".str_replace("'","''",$_POST['ITStd_253'])."',ITStd_254 ='".str_replace("'","''",$_POST['ITStd_254'])."',ITStd_255 ='".str_replace("'","''",$_POST['ITStd_255'])."',ITStd_256 ='".str_replace("'","''",$_POST['ITStd_256'])."',ITStd_257 ='".str_replace("'","''",$_POST['ITStd_257'])."',ITStd_258 ='".str_replace("'","''",$_POST['ITStd_258'])."',ITStd_259 ='".str_replace("'","''",$_POST['ITStd_259'])."',ITStd_260 ='".str_replace("'","''",$_POST['ITStd_260'])."',ITStd_261 ='".str_replace("'","''",$_POST['ITStd_261'])."',ITStd_262 ='".str_replace("'","''",$_POST['ITStd_262'])."',ITStd_263 ='".str_replace("'","''",$_POST['ITStd_263'])."',ITStd_264 ='".str_replace("'","''",$_POST['ITStd_264'])."',ITStd_265 ='".str_replace("'","''",$_POST['ITStd_265'])."',ITStd_266 ='".str_replace("'","''",$_POST['ITStd_266'])."',ITStd_267 ='".str_replace("'","''",$_POST['ITStd_267'])."',ITStd_268 ='".str_replace("'","''",$_POST['ITStd_268'])."',ITStd_269 ='".str_replace("'","''",$_POST['ITStd_269'])."',ITStd_270 ='".str_replace("'","''",$_POST['ITStd_270'])."',ITStd_271 ='".str_replace("'","''",$_POST['ITStd_271'])."',ITStd_272 ='".str_replace("'","''",$_POST['ITStd_272'])."',ITStd_273 ='".str_replace("'","''",$_POST['ITStd_273'])."',ITStd_274 ='".str_replace("'","''",$_POST['ITStd_274'])."',ITStd_275 ='".str_replace("'","''",$_POST['ITStd_275'])."',ITStd_276 ='".str_replace("'","''",$_POST['ITStd_276'])."',ITStd_277 ='".str_replace("'","''",$_POST['ITStd_277'])."',ITStd_278 ='".str_replace("'","''",$_POST['ITStd_278'])."',ITStd_279 ='".str_replace("'","''",$_POST['ITStd_279'])."',ITStd_280 ='".str_replace("'","''",$_POST['ITStd_280'])."',ITStd_281 ='".str_replace("'","''",$_POST['ITStd_281'])."',ITStd_282 ='".str_replace("'","''",$_POST['ITStd_282'])."',ITStd_283 ='".str_replace("'","''",$_POST['ITStd_283'])."',ITStd_284 ='".str_replace("'","''",$_POST['ITStd_284'])."',ITStd_285 ='".str_replace("'","''",$_POST['ITStd_285'])."',ITStd_286 ='".str_replace("'","''",$_POST['ITStd_286'])."',ITStd_287 ='".str_replace("'","''",$_POST['ITStd_287'])."',ITStd_288 ='".str_replace("'","''",$_POST['ITStd_288'])."',ITStd_289 ='".str_replace("'","''",$_POST['ITStd_289'])."',ITStd_290 ='".str_replace("'","''",$_POST['ITStd_290'])."',ITStd_291 ='".str_replace("'","''",$_POST['ITStd_291'])."',ITStd_292 ='".str_replace("'","''",$_POST['ITStd_292'])."',ITStd_293 ='".str_replace("'","''",$_POST['ITStd_293'])."',ITStd_294 ='".str_replace("'","''",$_POST['ITStd_294'])."',ITStd_295 ='".str_replace("'","''",$_POST['ITStd_295'])."',ITStd_296 ='".str_replace("'","''",$_POST['ITStd_296'])."',ITStd_297 ='".str_replace("'","''",$_POST['ITStd_297'])."',ITStd_298 ='".str_replace("'","''",$_POST['ITStd_298'])."',ITStd_299 ='".str_replace("'","''",$_POST['ITStd_299'])."',ITStd_300 ='".str_replace("'","''",$_POST['ITStd_300'])."',ITStd_301 ='".str_replace("'","''",$_POST['ITStd_301'])."',ITStd_302 ='".str_replace("'","''",$_POST['ITStd_302'])."',ITStd_303 ='".str_replace("'","''",$_POST['ITStd_303'])."',ITStd_304 ='".str_replace("'","''",$_POST['ITStd_304'])."',ITStd_305 ='".str_replace("'","''",$_POST['ITStd_305'])."',ITStd_306 ='".str_replace("'","''",$_POST['ITStd_306'])."',ITStd_307 ='".str_replace("'","''",$_POST['ITStd_307'])."',ITStd_308 ='".str_replace("'","''",$_POST['ITStd_308'])."',ITStd_309 ='".str_replace("'","''",$_POST['ITStd_309'])."',ITStd_310 ='".str_replace("'","''",$_POST['ITStd_310'])."',ITStd_311 ='".str_replace("'","''",$_POST['ITStd_311'])."',ITStd_312 ='".str_replace("'","''",$_POST['ITStd_312'])."',ITStd_313 ='".str_replace("'","''",$_POST['ITStd_313'])."',ITStd_314 ='".str_replace("'","''",$_POST['ITStd_314'])."',ITStd_315 ='".str_replace("'","''",$_POST['ITStd_315'])."',ITStd_316 ='".str_replace("'","''",$_POST['ITStd_316'])."',ITStd_317 ='".str_replace("'","''",$_POST['ITStd_317'])."',ITStd_318 ='".str_replace("'","''",$_POST['ITStd_318'])."',ITStd_319 ='".str_replace("'","''",$_POST['ITStd_319'])."',ITStd_320 ='".str_replace("'","''",$_POST['ITStd_320'])."',ITStd_321 ='".str_replace("'","''",$_POST['ITStd_321'])."',ITStd_322 ='".str_replace("'","''",$_POST['ITStd_322'])."',ITStd_323 ='".str_replace("'","''",$_POST['ITStd_323'])."',ITStd_324 ='".str_replace("'","''",$_POST['ITStd_324'])."',ITStd_325 ='".str_replace("'","''",$_POST['ITStd_325'])."',ITStd_326 ='".str_replace("'","''",$_POST['ITStd_326'])."',ITStd_327 ='".str_replace("'","''",$_POST['ITStd_327'])."',ITStd_328 ='".str_replace("'","''",$_POST['ITStd_328'])."',ITStd_329 ='".str_replace("'","''",$_POST['ITStd_329'])."',ITStd_330 ='".str_replace("'","''",$_POST['ITStd_330'])."',ITStd_331 ='".str_replace("'","''",$_POST['ITStd_331'])."',ITStd_332 ='".str_replace("'","''",$_POST['ITStd_332'])."',ITStd_333 ='".str_replace("'","''",$_POST['ITStd_333'])."',ITStd_334 ='".str_replace("'","''",$_POST['ITStd_334'])."',ITStd_335 ='".str_replace("'","''",$_POST['ITStd_335'])."',ITStd_336 ='".str_replace("'","''",$_POST['ITStd_336'])."',ITStd_337 ='".str_replace("'","''",$_POST['ITStd_337'])."',ITStd_338 ='".str_replace("'","''",$_POST['ITStd_338'])."',ITStd_339 ='".str_replace("'","''",$_POST['ITStd_339'])."',ITStd_340 ='".str_replace("'","''",$_POST['ITStd_340'])."',ITStd_341 ='".str_replace("'","''",$_POST['ITStd_341'])."',ITStd_342 ='".str_replace("'","''",$_POST['ITStd_342'])."',ITStd_343 ='".str_replace("'","''",$_POST['ITStd_343'])."',ITStd_344 ='".str_replace("'","''",$_POST['ITStd_344'])."',ITStd_345 ='".str_replace("'","''",$_POST['ITStd_345'])."',ITStd_346 ='".str_replace("'","''",$_POST['ITStd_346'])."',ITStd_347 ='".str_replace("'","''",$_POST['ITStd_347'])."',ITStd_348 ='".str_replace("'","''",$_POST['ITStd_348'])."',ITStd_349 ='".str_replace("'","''",$_POST['ITStd_349'])."',ITStd_350 ='".str_replace("'","''",$_POST['ITStd_350'])."',ITStd_351 ='".str_replace("'","''",$_POST['ITStd_351'])."',ITStd_352 ='".str_replace("'","''",$_POST['ITStd_352'])."',ITStd_353 ='".str_replace("'","''",$_POST['ITStd_353'])."',ITStd_354 ='".str_replace("'","''",$_POST['ITStd_354'])."',ITStd_355 ='".str_replace("'","''",$_POST['ITStd_355'])."',ITStd_356 ='".str_replace("'","''",$_POST['ITStd_356'])."',ITStd_357 ='".str_replace("'","''",$_POST['ITStd_357'])."',ITStd_358 ='".str_replace("'","''",$_POST['ITStd_358'])."',ITStd_359 ='".str_replace("'","''",$_POST['ITStd_359'])."',ITStd_360 ='".str_replace("'","''",$_POST['ITStd_360'])."',ITStd_361 ='".str_replace("'","''",$_POST['ITStd_361'])."',ITStd_362 ='".str_replace("'","''",$_POST['ITStd_362'])."',ITStd_363 ='".str_replace("'","''",$_POST['ITStd_363'])."',ITStd_364 ='".str_replace("'","''",$_POST['ITStd_364'])."',ITStd_365 ='".str_replace("'","''",$_POST['ITStd_365'])."',ITStd_366 ='".str_replace("'","''",$_POST['ITStd_366'])."',ITStd_367 ='".str_replace("'","''",$_POST['ITStd_367'])."',ITStd_368 ='".str_replace("'","''",$_POST['ITStd_368'])."',ITStd_369 ='".str_replace("'","''",$_POST['ITStd_369'])."',ITStd_370 ='".str_replace("'","''",$_POST['ITStd_370'])."',ITStd_371 ='".str_replace("'","''",$_POST['ITStd_371'])."',ITStd_372 ='".str_replace("'","''",$_POST['ITStd_372'])."',ITStd_373 ='".str_replace("'","''",$_POST['ITStd_373'])."',ITStd_374 ='".str_replace("'","''",$_POST['ITStd_374'])."',ITStd_375 ='".str_replace("'","''",$_POST['ITStd_375'])."',ITStd_376 ='".str_replace("'","''",$_POST['ITStd_376'])."',ITStd_377 ='".str_replace("'","''",$_POST['ITStd_377'])."',ITStd_378 ='".str_replace("'","''",$_POST['ITStd_378'])."',ITStd_379 ='".str_replace("'","''",$_POST['ITStd_379'])."',ITStd_380 ='".str_replace("'","''",$_POST['ITStd_380'])."',ITStd_381 ='".str_replace("'","''",$_POST['ITStd_381'])."',ITStd_382 ='".str_replace("'","''",$_POST['ITStd_382'])."',ITStd_383 ='".str_replace("'","''",$_POST['ITStd_383'])."',ITStd_384 ='".str_replace("'","''",$_POST['ITStd_384'])."',ITStd_385 ='".str_replace("'","''",$_POST['ITStd_385'])."',ITStd_386 ='".str_replace("'","''",$_POST['ITStd_386'])."',ITStd_387 ='".str_replace("'","''",$_POST['ITStd_387'])."',ITStd_388 ='".str_replace("'","''",$_POST['ITStd_388'])."',ITStd_389 ='".str_replace("'","''",$_POST['ITStd_389'])."',ITStd_390 ='".str_replace("'","''",$_POST['ITStd_390'])."',ITStd_391 ='".str_replace("'","''",$_POST['ITStd_391'])."',ITStd_392 ='".str_replace("'","''",$_POST['ITStd_392'])."',ITStd_393 ='".str_replace("'","''",$_POST['ITStd_393'])."',ITStd_394 ='".str_replace("'","''",$_POST['ITStd_394'])."',ITStd_395 ='".str_replace("'","''",$_POST['ITStd_395'])."',ITStd_396 ='".str_replace("'","''",$_POST['ITStd_396'])."',ITStd_397 ='".str_replace("'","''",$_POST['ITStd_397'])."',ITStd_398 ='".str_replace("'","''",$_POST['ITStd_398'])."',ITStd_399 ='".str_replace("'","''",$_POST['ITStd_399'])."',ITStd_400 ='".str_replace("'","''",$_POST['ITStd_400'])."',ITStd_401 ='".str_replace("'","''",$_POST['ITStd_401'])."',ITStd_402 ='".str_replace("'","''",$_POST['ITStd_402'])."',ITStd_403 ='".str_replace("'","''",$_POST['ITStd_403'])."',ITStd_404 ='".str_replace("'","''",$_POST['ITStd_404'])."',ITStd_405 ='".str_replace("'","''",$_POST['ITStd_405'])."',ITStd_406 ='".str_replace("'","''",$_POST['ITStd_406'])."',ITStd_407 ='".str_replace("'","''",$_POST['ITStd_407'])."',ITStd_408 ='".str_replace("'","''",$_POST['ITStd_408'])."',ITStd_409 ='".str_replace("'","''",$_POST['ITStd_409'])."',ITStd_410 ='".str_replace("'","''",$_POST['ITStd_410'])."',ITStd_411 ='".str_replace("'","''",$_POST['ITStd_411'])."',ITStd_412 ='".str_replace("'","''",$_POST['ITStd_412'])."',ITStd_413 ='".str_replace("'","''",$_POST['ITStd_413'])."',ITStd_414 ='".str_replace("'","''",$_POST['ITStd_414'])."',ITStd_415 ='".str_replace("'","''",$_POST['ITStd_415'])."',ITStd_416 ='".str_replace("'","''",$_POST['ITStd_416'])."',ITStd_417 ='".str_replace("'","''",$_POST['ITStd_417'])."',ITStd_418 ='".str_replace("'","''",$_POST['ITStd_418'])."',ITStd_419 ='".str_replace("'","''",$_POST['ITStd_419'])."',ITStd_420 ='".str_replace("'","''",$_POST['ITStd_420'])."',ITStd_421 ='".str_replace("'","''",$_POST['ITStd_421'])."',ITStd_422 ='".str_replace("'","''",$_POST['ITStd_422'])."',ITStd_423 ='".str_replace("'","''",$_POST['ITStd_423'])."',ITStd_424 ='".str_replace("'","''",$_POST['ITStd_424'])."',ITStd_425 ='".str_replace("'","''",$_POST['ITStd_425'])."',ITStd_426 ='".str_replace("'","''",$_POST['ITStd_426'])."',ITStd_427 ='".str_replace("'","''",$_POST['ITStd_427'])."',ITStd_428 ='".str_replace("'","''",$_POST['ITStd_428'])."',ITStd_429 ='".str_replace("'","''",$_POST['ITStd_429'])."',ITStd_430 ='".str_replace("'","''",$_POST['ITStd_430'])."',ITStd_431 ='".str_replace("'","''",$_POST['ITStd_431'])."',ITStd_432 ='".str_replace("'","''",$_POST['ITStd_432'])."',ITStd_433 ='".str_replace("'","''",$_POST['ITStd_433'])."',ITStd_434 ='".str_replace("'","''",$_POST['ITStd_434'])."',ITStd_435 ='".str_replace("'","''",$_POST['ITStd_435'])."',ITStd_436 ='".str_replace("'","''",$_POST['ITStd_436'])."',ITStd_437 ='".str_replace("'","''",$_POST['ITStd_437'])."',ITStd_438 ='".str_replace("'","''",$_POST['ITStd_438'])."',ITStd_439 ='".str_replace("'","''",$_POST['ITStd_439'])."',ITStd_440 ='".str_replace("'","''",$_POST['ITStd_440'])."',ITStd_441 ='".str_replace("'","''",$_POST['ITStd_441'])."',ITStd_442 ='".str_replace("'","''",$_POST['ITStd_442'])."',ITStd_443 ='".str_replace("'","''",$_POST['ITStd_443'])."',ITStd_444 ='".str_replace("'","''",$_POST['ITStd_444'])."',ITStd_445 ='".str_replace("'","''",$_POST['ITStd_445'])."',ITStd_446 ='".str_replace("'","''",$_POST['ITStd_446'])."',ITStd_447 ='".str_replace("'","''",$_POST['ITStd_447'])."',ITStd_448 ='".str_replace("'","''",$_POST['ITStd_448'])."',ITStd_449 ='".str_replace("'","''",$_POST['ITStd_449'])."',ITStd_450 ='".str_replace("'","''",$_POST['ITStd_450'])."',ITStd_451 ='".str_replace("'","''",$_POST['ITStd_451'])."',ITStd_452 ='".str_replace("'","''",$_POST['ITStd_452'])."',ITStd_453 ='".str_replace("'","''",$_POST['ITStd_453'])."',ITStd_454 ='".str_replace("'","''",$_POST['ITStd_454'])."',ITStd_455 ='".str_replace("'","''",$_POST['ITStd_455'])."',ITStd_456 ='".str_replace("'","''",$_POST['ITStd_456'])."',ITStd_457 ='".str_replace("'","''",$_POST['ITStd_457'])."',ITStd_458 ='".str_replace("'","''",$_POST['ITStd_458'])."',ITStd_459 ='".str_replace("'","''",$_POST['ITStd_459'])."',ITStd_460 ='".str_replace("'","''",$_POST['ITStd_460'])."',ITStd_461 ='".str_replace("'","''",$_POST['ITStd_461'])."',ITStd_462 ='".str_replace("'","''",$_POST['ITStd_462'])."',ITStd_463 ='".str_replace("'","''",$_POST['ITStd_463'])."',ITStd_464 ='".str_replace("'","''",$_POST['ITStd_464'])."',ITStd_465 ='".str_replace("'","''",$_POST['ITStd_465'])."',ITStd_466 ='".str_replace("'","''",$_POST['ITStd_466'])."',ITStd_467 ='".str_replace("'","''",$_POST['ITStd_467'])."',ITStd_468 ='".str_replace("'","''",$_POST['ITStd_468'])."',ITStd_469 ='".str_replace("'","''",$_POST['ITStd_469'])."',ITStd_470 ='".str_replace("'","''",$_POST['ITStd_470'])."',ITStd_471 ='".str_replace("'","''",$_POST['ITStd_471'])."',ITStd_472 ='".str_replace("'","''",$_POST['ITStd_472'])."',ITStd_473 ='".str_replace("'","''",$_POST['ITStd_473'])."',ITStd_474 ='".str_replace("'","''",$_POST['ITStd_474'])."',ITStd_475 ='".str_replace("'","''",$_POST['ITStd_475'])."',ITStd_476 ='".str_replace("'","''",$_POST['ITStd_476'])."',ITStd_477 ='".str_replace("'","''",$_POST['ITStd_477'])."',ITStd_478 ='".str_replace("'","''",$_POST['ITStd_478'])."',ITStd_479 ='".str_replace("'","''",$_POST['ITStd_479'])."',ITStd_480 ='".str_replace("'","''",$_POST['ITStd_480'])."',ITStd_481 ='".str_replace("'","''",$_POST['ITStd_481'])."',ITStd_482 ='".str_replace("'","''",$_POST['ITStd_482'])."',ITStd_483 ='".str_replace("'","''",$_POST['ITStd_483'])."',ITStd_484 ='".str_replace("'","''",$_POST['ITStd_484'])."',ITStd_485 ='".str_replace("'","''",$_POST['ITStd_485'])."',ITStd_486 ='".str_replace("'","''",$_POST['ITStd_486'])."',ITStd_487 ='".str_replace("'","''",$_POST['ITStd_487'])."',ITStd_488 ='".str_replace("'","''",$_POST['ITStd_488'])."',ITStd_489 ='".str_replace("'","''",$_POST['ITStd_489'])."',ITStd_490 ='".str_replace("'","''",$_POST['ITStd_490'])."',ITStd_491 ='".str_replace("'","''",$_POST['ITStd_491'])."',ITStd_492 ='".str_replace("'","''",$_POST['ITStd_492'])."',ITStd_493 ='".str_replace("'","''",$_POST['ITStd_493'])."',ITStd_494 ='".str_replace("'","''",$_POST['ITStd_494'])."',ITStd_495 ='".str_replace("'","''",$_POST['ITStd_495'])."',ITStd_496 ='".str_replace("'","''",$_POST['ITStd_496'])."',ITStd_497 ='".str_replace("'","''",$_POST['ITStd_497'])."',ITStd_498 ='".str_replace("'","''",$_POST['ITStd_498'])."',ITStd_499 ='".str_replace("'","''",$_POST['ITStd_499'])."',ITStd_500 ='".str_replace("'","''",$_POST['ITStd_500'])."',ITStd_501 ='".str_replace("'","''",$_POST['ITStd_501'])."',ITStd_502 ='".str_replace("'","''",$_POST['ITStd_502'])."',ITStd_503 ='".str_replace("'","''",$_POST['ITStd_503'])."',ITStd_504 ='".str_replace("'","''",$_POST['ITStd_504'])."',ITStd_505 ='".str_replace("'","''",$_POST['ITStd_505'])."',ITStd_506 ='".str_replace("'","''",$_POST['ITStd_506'])."',ITStd_507 ='".str_replace("'","''",$_POST['ITStd_507'])."',ITStd_508 ='".str_replace("'","''",$_POST['ITStd_508'])."',ITStd_509 ='".str_replace("'","''",$_POST['ITStd_509'])."',ITStd_510 ='".str_replace("'","''",$_POST['ITStd_510'])."',ITStd_511 ='".str_replace("'","''",$_POST['ITStd_511'])."',ITStd_512 ='".str_replace("'","''",$_POST['ITStd_512'])."',ITStd_513 ='".str_replace("'","''",$_POST['ITStd_513'])."',ITStd_514 ='".str_replace("'","''",$_POST['ITStd_514'])."',ITStd_515 ='".str_replace("'","''",$_POST['ITStd_515'])."',ITStd_516 ='".str_replace("'","''",$_POST['ITStd_516'])."',ITStd_517 ='".str_replace("'","''",$_POST['ITStd_517'])."',ITStd_518 ='".str_replace("'","''",$_POST['ITStd_518'])."',ITStd_519 ='".str_replace("'","''",$_POST['ITStd_519'])."',ITStd_520 ='".str_replace("'","''",$_POST['ITStd_520'])."',ITStd_521 ='".str_replace("'","''",$_POST['ITStd_521'])."',ITStd_522 ='".str_replace("'","''",$_POST['ITStd_522'])."',ITStd_523 ='".str_replace("'","''",$_POST['ITStd_523'])."',ITStd_524 ='".str_replace("'","''",$_POST['ITStd_524'])."',ITStd_525 ='".str_replace("'","''",$_POST['ITStd_525'])."',ITStd_526 ='".str_replace("'","''",$_POST['ITStd_526'])."',ITStd_527 ='".str_replace("'","''",$_POST['ITStd_527'])."',ITStd_528 ='".str_replace("'","''",$_POST['ITStd_528'])."',ITStd_529 ='".str_replace("'","''",$_POST['ITStd_529'])."',ITStd_530 ='".str_replace("'","''",$_POST['ITStd_530'])."',ITStd_531 ='".str_replace("'","''",$_POST['ITStd_531'])."',ITStd_532 ='".str_replace("'","''",$_POST['ITStd_532'])."',ITStd_533 ='".str_replace("'","''",$_POST['ITStd_533'])."',ITStd_534 ='".str_replace("'","''",$_POST['ITStd_534'])."',ITStd_535 ='".str_replace("'","''",$_POST['ITStd_535'])."',ITStd_536 ='".str_replace("'","''",$_POST['ITStd_536'])."',ITStd_537 ='".str_replace("'","''",$_POST['ITStd_537'])."',ITStd_538 ='".str_replace("'","''",$_POST['ITStd_538'])."',ITStd_539 ='".str_replace("'","''",$_POST['ITStd_539'])."',ITStd_540 ='".str_replace("'","''",$_POST['ITStd_540'])."',ITStd_541 ='".str_replace("'","''",$_POST['ITStd_541'])."',ITStd_542 ='".str_replace("'","''",$_POST['ITStd_542'])."',ITStd_543 ='".str_replace("'","''",$_POST['ITStd_543'])."',ITStd_544 ='".str_replace("'","''",$_POST['ITStd_544'])."',ITStd_545 ='".str_replace("'","''",$_POST['ITStd_545'])."',ITStd_546 ='".str_replace("'","''",$_POST['ITStd_546'])."',ITStd_547 ='".str_replace("'","''",$_POST['ITStd_547'])."',ITStd_548 ='".str_replace("'","''",$_POST['ITStd_548'])."',ITStd_549 ='".str_replace("'","''",$_POST['ITStd_549'])."',ITStd_550 ='".str_replace("'","''",$_POST['ITStd_550'])."',ITStd_551 ='".str_replace("'","''",$_POST['ITStd_551'])."',ITStd_552 ='".str_replace("'","''",$_POST['ITStd_552'])."',ITStd_553 ='".str_replace("'","''",$_POST['ITStd_553'])."',ITStd_554 ='".str_replace("'","''",$_POST['ITStd_554'])."',ITStd_555 ='".str_replace("'","''",$_POST['ITStd_555'])."',ITStd_556 ='".str_replace("'","''",$_POST['ITStd_556'])."',ITStd_557 ='".str_replace("'","''",$_POST['ITStd_557'])."',ITStd_558 ='".str_replace("'","''",$_POST['ITStd_558'])."',ITStd_559 ='".str_replace("'","''",$_POST['ITStd_559'])."',ITStd_560 ='".str_replace("'","''",$_POST['ITStd_560'])."',ITStd_561 ='".str_replace("'","''",$_POST['ITStd_561'])."',ITStd_562 ='".str_replace("'","''",$_POST['ITStd_562'])."',ITStd_563 ='".str_replace("'","''",$_POST['ITStd_563'])."',ITStd_564 ='".str_replace("'","''",$_POST['ITStd_564'])."',ITStd_565 ='".str_replace("'","''",$_POST['ITStd_565'])."',ITStd_566 ='".str_replace("'","''",$_POST['ITStd_566'])."',ITStd_567 ='".str_replace("'","''",$_POST['ITStd_567'])."',ITStd_568 ='".str_replace("'","''",$_POST['ITStd_568'])."',ITStd_569 ='".str_replace("'","''",$_POST['ITStd_569'])."',ITStd_570 ='".str_replace("'","''",$_POST['ITStd_570'])."',ITStd_571 ='".str_replace("'","''",$_POST['ITStd_571'])."',ITStd_572 ='".str_replace("'","''",$_POST['ITStd_572'])."',ITStd_573 ='".str_replace("'","''",$_POST['ITStd_573'])."',ITStd_574 ='".str_replace("'","''",$_POST['ITStd_574'])."',ITStd_575 ='".str_replace("'","''",$_POST['ITStd_575'])."',ITStd_576 ='".str_replace("'","''",$_POST['ITStd_576'])."',ITStd_577 ='".str_replace("'","''",$_POST['ITStd_577'])."',ITStd_578 ='".str_replace("'","''",$_POST['ITStd_578'])."',ITStd_579 ='".str_replace("'","''",$_POST['ITStd_579'])."',ITStd_580 ='".str_replace("'","''",$_POST['ITStd_580'])."',ITStd_581 ='".str_replace("'","''",$_POST['ITStd_581'])."',ITStd_582 ='".str_replace("'","''",$_POST['ITStd_582'])."',ITStd_583 ='".str_replace("'","''",$_POST['ITStd_583'])."',ITStd_584 ='".str_replace("'","''",$_POST['ITStd_584'])."',ITStd_585 ='".str_replace("'","''",$_POST['ITStd_585'])."',ITStd_586 ='".str_replace("'","''",$_POST['ITStd_586'])."',ITStd_587 ='".str_replace("'","''",$_POST['ITStd_587'])."',ITStd_588 ='".str_replace("'","''",$_POST['ITStd_588'])."',ITStd_589 ='".str_replace("'","''",$_POST['ITStd_589'])."',ITStd_590 ='".str_replace("'","''",$_POST['ITStd_590'])."',ITStd_591 ='".str_replace("'","''",$_POST['ITStd_591'])."',ITStd_592 ='".str_replace("'","''",$_POST['ITStd_592'])."',ITStd_593 ='".str_replace("'","''",$_POST['ITStd_593'])."',ITStd_594 ='".str_replace("'","''",$_POST['ITStd_594'])."',ITStd_595 ='".str_replace("'","''",$_POST['ITStd_595'])."',ITStd_596 ='".str_replace("'","''",$_POST['ITStd_596'])."',ITStd_597 ='".str_replace("'","''",$_POST['ITStd_597'])."',ITStd_598 ='".str_replace("'","''",$_POST['ITStd_598'])."',ITStd_599 ='".str_replace("'","''",$_POST['ITStd_599'])."',ITStd_600 ='".str_replace("'","''",$_POST['ITStd_600'])."',ITStd_601 ='".str_replace("'","''",$_POST['ITStd_601'])."',ITStd_602 ='".str_replace("'","''",$_POST['ITStd_602'])."',ITStd_603 ='".str_replace("'","''",$_POST['ITStd_603'])."',ITStd_604 ='".str_replace("'","''",$_POST['ITStd_604'])."',ITStd_605 ='".str_replace("'","''",$_POST['ITStd_605'])."',ITStd_606 ='".str_replace("'","''",$_POST['ITStd_606'])."',ITStd_607 ='".str_replace("'","''",$_POST['ITStd_607'])."',ITStd_608 ='".str_replace("'","''",$_POST['ITStd_608'])."',ITStd_609 ='".str_replace("'","''",$_POST['ITStd_609'])."',ITStd_610 ='".str_replace("'","''",$_POST['ITStd_610'])."',ITStd_611 ='".str_replace("'","''",$_POST['ITStd_611'])."',ITStd_612 ='".str_replace("'","''",$_POST['ITStd_612'])."',ITStd_613 ='".str_replace("'","''",$_POST['ITStd_613'])."',ITStd_614 ='".str_replace("'","''",$_POST['ITStd_614'])."',ITStd_615 ='".str_replace("'","''",$_POST['ITStd_615'])."',ITStd_616 ='".str_replace("'","''",$_POST['ITStd_616'])."',ITStd_617 ='".str_replace("'","''",$_POST['ITStd_617'])."',ITStd_618 ='".str_replace("'","''",$_POST['ITStd_618'])."',ITStd_619 ='".str_replace("'","''",$_POST['ITStd_619'])."',ITStd_620 ='".str_replace("'","''",$_POST['ITStd_620'])."',ITStd_621 ='".str_replace("'","''",$_POST['ITStd_621'])."',ITStd_622 ='".str_replace("'","''",$_POST['ITStd_622'])."',ITStd_623 ='".str_replace("'","''",$_POST['ITStd_623'])."',ITStd_624 ='".str_replace("'","''",$_POST['ITStd_624'])."',ITStd_625 ='".str_replace("'","''",$_POST['ITStd_625'])."',ITStd_626 ='".str_replace("'","''",$_POST['ITStd_626'])."',ITStd_627 ='".str_replace("'","''",$_POST['ITStd_627'])."',ITStd_628 ='".str_replace("'","''",$_POST['ITStd_628'])."',ITStd_629 ='".str_replace("'","''",$_POST['ITStd_629'])."',ITStd_630 ='".str_replace("'","''",$_POST['ITStd_630'])."',ITStd_631 ='".str_replace("'","''",$_POST['ITStd_631'])."',ITStd_632 ='".str_replace("'","''",$_POST['ITStd_632'])."',ITStd_633 ='".str_replace("'","''",$_POST['ITStd_633'])."',ITStd_634 ='".str_replace("'","''",$_POST['ITStd_634'])."',ITStd_635 ='".str_replace("'","''",$_POST['ITStd_635'])."',ITStd_636 ='".str_replace("'","''",$_POST['ITStd_636'])."',ITStd_637 ='".str_replace("'","''",$_POST['ITStd_637'])."',ITStd_638 ='".str_replace("'","''",$_POST['ITStd_638'])."',ITStd_639 ='".str_replace("'","''",$_POST['ITStd_639'])."',ITStd_640 ='".str_replace("'","''",$_POST['ITStd_640'])."',ITStd_641 ='".str_replace("'","''",$_POST['ITStd_641'])."',ITStd_642 ='".str_replace("'","''",$_POST['ITStd_642'])."',ITStd_643 ='".str_replace("'","''",$_POST['ITStd_643'])."',ITStd_644 ='".str_replace("'","''",$_POST['ITStd_644'])."',ITStd_645 ='".str_replace("'","''",$_POST['ITStd_645'])."',ITStd_646 ='".str_replace("'","''",$_POST['ITStd_646'])."',ITStd_647 ='".str_replace("'","''",$_POST['ITStd_647'])."',ITStd_648 ='".str_replace("'","''",$_POST['ITStd_648'])."',ITStd_649 ='".str_replace("'","''",$_POST['ITStd_649'])."',ITStd_650 ='".str_replace("'","''",$_POST['ITStd_650'])."',ITStd_651 ='".str_replace("'","''",$_POST['ITStd_651'])."',ITStd_652 ='".str_replace("'","''",$_POST['ITStd_652'])."',ITStd_653 ='".str_replace("'","''",$_POST['ITStd_653'])."',ITStd_654 ='".str_replace("'","''",$_POST['ITStd_654'])."',ITStd_655 ='".str_replace("'","''",$_POST['ITStd_655'])."',ITStd_656 ='".str_replace("'","''",$_POST['ITStd_656'])."',ITStd_657 ='".str_replace("'","''",$_POST['ITStd_657'])."',ITStd_658 ='".str_replace("'","''",$_POST['ITStd_658'])."',ITStd_659 ='".str_replace("'","''",$_POST['ITStd_659'])."',ITStd_660 ='".str_replace("'","''",$_POST['ITStd_660'])."',ITStd_661 ='".str_replace("'","''",$_POST['ITStd_661'])."',ITStd_662 ='".str_replace("'","''",$_POST['ITStd_662'])."',ITStd_663 ='".str_replace("'","''",$_POST['ITStd_663'])."',ITStd_664 ='".str_replace("'","''",$_POST['ITStd_664'])."',ITStd_665 ='".str_replace("'","''",$_POST['ITStd_665'])."',ITStd_666 ='".str_replace("'","''",$_POST['ITStd_666'])."',ITStd_667 ='".str_replace("'","''",$_POST['ITStd_667'])."',ITStd_668 ='".str_replace("'","''",$_POST['ITStd_668'])."',ITStd_669 ='".str_replace("'","''",$_POST['ITStd_669'])."',ITStd_670 ='".str_replace("'","''",$_POST['ITStd_670'])."',ITStd_671 ='".str_replace("'","''",$_POST['ITStd_671'])."',ITStd_672 ='".str_replace("'","''",$_POST['ITStd_672'])."',ITStd_673 ='".str_replace("'","''",$_POST['ITStd_673'])."',ITStd_674 ='".str_replace("'","''",$_POST['ITStd_674'])."',ITStd_675 ='".str_replace("'","''",$_POST['ITStd_675'])."',ITStd_676 ='".str_replace("'","''",$_POST['ITStd_676'])."',ITStd_677 ='".str_replace("'","''",$_POST['ITStd_677'])."',ITStd_678 ='".str_replace("'","''",$_POST['ITStd_678'])."',ITStd_679 ='".str_replace("'","''",$_POST['ITStd_679'])."',ITStd_680 ='".str_replace("'","''",$_POST['ITStd_680'])."',ITStd_681 ='".str_replace("'","''",$_POST['ITStd_681'])."',ITStd_682 ='".str_replace("'","''",$_POST['ITStd_682'])."',ITStd_683 ='".str_replace("'","''",$_POST['ITStd_683'])."',ITStd_684 ='".str_replace("'","''",$_POST['ITStd_684'])."',ITStd_685 ='".str_replace("'","''",$_POST['ITStd_685'])."',ITStd_686 ='".str_replace("'","''",$_POST['ITStd_686'])."',ITStd_687 ='".str_replace("'","''",$_POST['ITStd_687'])."',ITStd_688 ='".str_replace("'","''",$_POST['ITStd_688'])."',ITStd_689 ='".str_replace("'","''",$_POST['ITStd_689'])."',ITStd_690 ='".str_replace("'","''",$_POST['ITStd_690'])."',ITStd_691 ='".str_replace("'","''",$_POST['ITStd_691'])."',ITStd_692 ='".str_replace("'","''",$_POST['ITStd_692'])."',ITStd_693 ='".str_replace("'","''",$_POST['ITStd_693'])."',ITStd_694 ='".str_replace("'","''",$_POST['ITStd_694'])."',ITStd_695 ='".str_replace("'","''",$_POST['ITStd_695'])."',ITStd_696 ='".str_replace("'","''",$_POST['ITStd_696'])."',ITStd_697 ='".str_replace("'","''",$_POST['ITStd_697'])."',ITStd_698 ='".str_replace("'","''",$_POST['ITStd_698'])."',ITStd_699 ='".str_replace("'","''",$_POST['ITStd_699'])."',ITStd_700 ='".str_replace("'","''",$_POST['ITStd_700'])."',ITStd_701 ='".str_replace("'","''",$_POST['ITStd_701'])."',ITStd_702 ='".str_replace("'","''",$_POST['ITStd_702'])."',ITStd_703 ='".str_replace("'","''",$_POST['ITStd_703'])."',ITStd_704 ='".str_replace("'","''",$_POST['ITStd_704'])."',ITStd_705 ='".str_replace("'","''",$_POST['ITStd_705'])."',ITStd_706 ='".str_replace("'","''",$_POST['ITStd_706'])."',ITStd_707 ='".str_replace("'","''",$_POST['ITStd_707'])."',ITStd_708 ='".str_replace("'","''",$_POST['ITStd_708'])."',ITStd_709 ='".str_replace("'","''",$_POST['ITStd_709'])."',ITStd_710 ='".str_replace("'","''",$_POST['ITStd_710'])."',ITStd_711 ='".str_replace("'","''",$_POST['ITStd_711'])."',ITStd_712 ='".str_replace("'","''",$_POST['ITStd_712'])."',ITStd_713 ='".str_replace("'","''",$_POST['ITStd_713'])."',ITStd_714 ='".str_replace("'","''",$_POST['ITStd_714'])."',ITStd_715 ='".str_replace("'","''",$_POST['ITStd_715'])."',ITStd_716 ='".str_replace("'","''",$_POST['ITStd_716'])."',ITStd_717 ='".str_replace("'","''",$_POST['ITStd_717'])."',ITStd_718 ='".str_replace("'","''",$_POST['ITStd_718'])."',ITStd_719 ='".str_replace("'","''",$_POST['ITStd_719'])."',ITStd_720 ='".str_replace("'","''",$_POST['ITStd_720'])."',ITStd_721 ='".str_replace("'","''",$_POST['ITStd_721'])."',ITStd_722 ='".str_replace("'","''",$_POST['ITStd_722'])."',ITStd_723 ='".str_replace("'","''",$_POST['ITStd_723'])."',ITStd_724 ='".str_replace("'","''",$_POST['ITStd_724'])."',ITStd_725 ='".str_replace("'","''",$_POST['ITStd_725'])."',ITStd_726 ='".str_replace("'","''",$_POST['ITStd_726'])."',ITStd_727 ='".str_replace("'","''",$_POST['ITStd_727'])."',ITStd_728 ='".str_replace("'","''",$_POST['ITStd_728'])."',ITStd_729 ='".str_replace("'","''",$_POST['ITStd_729'])."',ITStd_730 ='".str_replace("'","''",$_POST['ITStd_730'])."',ITStd_731 ='".str_replace("'","''",$_POST['ITStd_731'])."',ITStd_732 ='".str_replace("'","''",$_POST['ITStd_732'])."',ITStd_733 ='".str_replace("'","''",$_POST['ITStd_733'])."',ITStd_734 ='".str_replace("'","''",$_POST['ITStd_734'])."',ITStd_735 ='".str_replace("'","''",$_POST['ITStd_735'])."',ITStd_736 ='".str_replace("'","''",$_POST['ITStd_736'])."',ITStd_737 ='".str_replace("'","''",$_POST['ITStd_737'])."',ITStd_738 ='".str_replace("'","''",$_POST['ITStd_738'])."',ITStd_739 ='".str_replace("'","''",$_POST['ITStd_739'])."',ITStd_740 ='".str_replace("'","''",$_POST['ITStd_740'])."',ITStd_741 ='".str_replace("'","''",$_POST['ITStd_741'])."',ITStd_742 ='".str_replace("'","''",$_POST['ITStd_742'])."',ITStd_743 ='".str_replace("'","''",$_POST['ITStd_743'])."',ITStd_744 ='".str_replace("'","''",$_POST['ITStd_744'])."',ITStd_745 ='".str_replace("'","''",$_POST['ITStd_745'])."',ITStd_746 ='".str_replace("'","''",$_POST['ITStd_746'])."',ITStd_747 ='".str_replace("'","''",$_POST['ITStd_747'])."',ITStd_748 ='".str_replace("'","''",$_POST['ITStd_748'])."',ITStd_749 ='".str_replace("'","''",$_POST['ITStd_749'])."',ITStd_750 ='".str_replace("'","''",$_POST['ITStd_750'])."',ITStd_751 ='".str_replace("'","''",$_POST['ITStd_751'])."',ITStd_752 ='".str_replace("'","''",$_POST['ITStd_752'])."',ITStd_753 ='".str_replace("'","''",$_POST['ITStd_753'])."',ITStd_754 ='".str_replace("'","''",$_POST['ITStd_754'])."',ITStd_755 ='".str_replace("'","''",$_POST['ITStd_755'])."',ITStd_756 ='".str_replace("'","''",$_POST['ITStd_756'])."',ITStd_757 ='".str_replace("'","''",$_POST['ITStd_757'])."',ITStd_758 ='".str_replace("'","''",$_POST['ITStd_758'])."',ITStd_759 ='".str_replace("'","''",$_POST['ITStd_759'])."',ITStd_760 ='".str_replace("'","''",$_POST['ITStd_760'])."',ITStd_761 ='".str_replace("'","''",$_POST['ITStd_761'])."',ITStd_762 ='".str_replace("'","''",$_POST['ITStd_762'])."',ITStd_763 ='".str_replace("'","''",$_POST['ITStd_763'])."',ITStd_764 ='".str_replace("'","''",$_POST['ITStd_764'])."',ITStd_765 ='".str_replace("'","''",$_POST['ITStd_765'])."',ITStd_766 ='".str_replace("'","''",$_POST['ITStd_766'])."',ITStd_767 ='".str_replace("'","''",$_POST['ITStd_767'])."',ITStd_768 ='".str_replace("'","''",$_POST['ITStd_768'])."',ITStd_769 ='".str_replace("'","''",$_POST['ITStd_769'])."',ITStd_770 ='".str_replace("'","''",$_POST['ITStd_770'])."',ITStd_771 ='".str_replace("'","''",$_POST['ITStd_771'])."',ITStd_772 ='".str_replace("'","''",$_POST['ITStd_772'])."',ITStd_773 ='".str_replace("'","''",$_POST['ITStd_773'])."',ITStd_774 ='".str_replace("'","''",$_POST['ITStd_774'])."',ITStd_775 ='".str_replace("'","''",$_POST['ITStd_775'])."',ITStd_776 ='".str_replace("'","''",$_POST['ITStd_776'])."',ITStd_777 ='".str_replace("'","''",$_POST['ITStd_777'])."',ITStd_778 ='".str_replace("'","''",$_POST['ITStd_778'])."',ITStd_779 ='".str_replace("'","''",$_POST['ITStd_779'])."',ITStd_780 ='".str_replace("'","''",$_POST['ITStd_780'])."' WHERE C2ID= ".$row_rsForm['C2ID'], $conContinuty) or die(mysql_error());
	
	//does another selection to get the updated data
	mysql_select_db($database_conContinuty, $conContinuty);
	$rsForm2 = mysql_query("SELECT * FROM C2Information2 WHERE C2ID= ".$row_rsForm['C2ID'], $conContinuty) or die("Scope3: ".mysql_error());
	$row_rsForm2 = mysql_fetch_assoc($rsForm2);
	$totalRows_rsForm2 = mysql_num_rows($rsForm2);?><!-- InstanceEndEditable -->		    <?php 
	//does another selection to get the updated data
	mysql_select_db($database_conContinuty, $conContinuty);
	$rsForm = mysql_query("SELECT * FROM ".$row_rsPlans['TableName']." WHERE UserID = ".$UserID, $conContinuty) or die("Scope2: ".mysql_error());
	$row_rsForm = mysql_fetch_assoc($rsForm);
	$totalRows_rsForm = mysql_num_rows($rsForm);
	
    $strNextForm = $row_rsPlans['nextSection'];//holds the file name of the next section
	$arrNext = split("#",$strNextForm);//holds the array of section may go depending of the solution they are using

	 //checks if there is a # in $strNextForm meaning that form needs to redirect to another form
	if ($arrNext[1] <> "" && $row_loginFoundUser['Solution'] == 2)
		$strNextForm = $arrNext[1];
	else if ($arrNext[2] <> "" && $row_loginFoundUser['Solution'] == 3)
		$strNextForm = $arrNext[2];
	else
		$strNextForm = $arrNext[0];
    
	//checks if the PreveSection is Welomce which Means that the Page is the First Item
	if($strNextForm == "Welcome")
	{
		//does an update to the users to till that the user has start to submit to the forms
    	mysql_select_db($database_conContinuty, $conContinuty);
    	mysql_query("UPDATE users SET UserSubmit = Now() WHERE id= ".$UserID, $conContinuty) or die(mysql_error());
    }//end of if
	//checks if the nextSection is ThankYou meaning this is the last section
	else if($strNextForm == "ThankYou" && $_POST['hfSave'] != "1")
	{
		//does an update to the users to till that the user has end the submition form to the forms
		mysql_select_db($database_conContinuty, $conContinuty);
		mysql_query("UPDATE users SET UserCompleted = Now(), status = 5 WHERE id= ".$UserID, $conContinuty) or die(mysql_error());	
		
		//Is here for the last page as the saves do not need to go any far thught as it will be no point in keeping a save
		//if they already finsh the forms
		
		//checks if they have already a save point saved
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsLastLoc = mysql_query("SELECT * FROM userLastLoc WHERE userLastLoc.id=".$UserID, $conContinuty) or die(mysql_error());
		$total_lastLoc = mysql_num_rows($rsLastLoc);
		
		//checks if the user has already started to do the Sign Up for Conitinuity Insruance and if so then delete them from the table
		//in order to keep the size of the table to a min
		if ($total_lastLoc > 0)
		{
			//creates a row to save the last area they where there
			mysql_select_db($database_conContinuty, $conContinuty);
			mysql_query("DELETE FROM userlastloc WHERE userLastLoc.id=".$UserID, $conContinuty) or die(mysql_error());
		}//end of if
	}//end of if else
	
    //saves the progress of the user
    if($_POST['hfSave'] == "1")
    {
        //checks if they have already a save point saved
        mysql_select_db($database_conContinuty, $conContinuty);
        $rsLastLoc = mysql_query("SELECT * FROM userLastLoc WHERE userLastLoc.id=".$UserID, $conContinuty) or die(mysql_error());
        $total_lastLoc = mysql_num_rows($rsLastLoc);
        
        //checks if the user has already started to do the Sign Up for Conitinuity Insruance and if so then delete them from the table
        //in order to keep the size of the table to a min
        if ($total_lastLoc > 0)
        {
            //creates a row to save the last area they where there
            mysql_select_db($database_conContinuty, $conContinuty);
            mysql_query("DELETE FROM userlastloc WHERE userLastLoc.id=".$UserID, $conContinuty) or die(mysql_error());
        }//end of if
    
        //creates a row to save the last area they where there
        mysql_select_db($database_conContinuty, $conContinuty);
        mysql_query("INSERT INTO userlastloc VALUES ('".$UserID."', '"."http://".$_SERVER['HTTP_HOST']."/".substr($_SERVER['REQUEST_URI'],1)."?SubFolder=2&Section=".$SectionID."')", $conContinuty) or die(mysql_error());
        
        //tells the user that there procgess has been saved
        echo "<script type=\"text/javascript\">
                window.onload=function(){
                    alert('Your Data Has been Saved.');
                }
              </script>";
    }//end of if
    else if($_POST['hfSave'] == "")
    {
		//gets the next section for its ID
		//does a selection for the users data for this form
		mysql_select_db($database_conContinuty, $conContinuty); 
		$rsNextPlans = mysql_query("SELECT id, mainSectionName FROM continuityplans WHERE TableName = 'C2".$strNextForm."'", $conContinuty) or die("Next Plans: ".mysql_error()); 
		$row_rsNextPlans = mysql_fetch_assoc($rsNextPlans);
        header("Location: ../".str_replace(" ","",$row_rsNextPlans['mainSectionName'])."/".$strNextForm.".php?SubFolder=2&Section=".$row_rsNextPlans['id']);
    }//end of else
}//end of if ?>

<?php $strFilePath = "";//holds the file to go back one folder level to the root dirtory to find the file 
$loginFormAction = $_SERVER['PHP_SELF'];
$strStartURL = "?";//holds the start of the URL types
 
//checks how far the file is from the root dirtory
if ($_GET['SubFolder'] == "1" || $_POST['hfSubFolder'] == "1")
	$strFilePath = "../";
else if ($_GET['SubFolder'] == "2" || $_POST['hfSubFolder'] == "2")
	$strFilePath = "../../";

//checks if the user whats to log off
if (isset($_GET['logoff']))
	logOff(substr($_SERVER['REQUEST_URI'],1));
	
//checks if the user has trird three times to log in and if not then goes to the log in page with the CAPUS to bevent a hacker from
//useing bots to log in
if ($_POST['hfLogInCount'] == "4")
{
	echo "<script type=\"text/javascript\">
	window.onload=function(){
	  getDocID('hfLogInCount').value = '0';
	}
	</script>";

	header("Location: LogIn.php?section=LogIn&Footer=1");
}//end of if
	
if (isset($_POST['txtUserName'])) 
{
	if(logOnSite(base64_encode($_POST['txtUserName']),base64_encode(base64_encode($_POST['txtPassword'])),"",$database_conContinuty, $conContinuty) === FALSE)
	{		
		echo "<script type=\"text/javascript\">
		window.onload=function(){
		  displayMessage('divLogInError','Your user name or password is incorrect.',true,true);
		  toggleLayer('divBasicLogInHidden','divGrayBG');
		  }
		</script>";
	}//end of if
	else
	{
		//resets the hfLogInCount to 0 again
		echo "<script type=\"text/javascript\">
		window.onload=function(){
		  getDocID('hfLogInCount').value = '0';
		}
		</script>";
		
		header("Location: ".$strFilePath."Profile.php");
	}//end of else
}//end of if ?>
</head>

<body>
	<div id="divGrayBG" class="divBasicHiddlenBackground"></div>
	<div align="center">
        <div class="customContainer" id="divBasicContainer">
            <div class="customHeader" id="divBasicHeader">
              <div class="customContainer" id="divBasicHeaderContainer">
                <div class="customContent" id="divBasicHeaderContent" align="left">
                    <a href="../../index.php"><img src="../../images/logo1.jpg" alt="Logo" width="265" height="55" /></a>
                </div><!-- end of Header Content -->
                <div class="customNavigation" id="divBasicHeaderNavigation" align="left">
                   <form action="<?php echo $loginFormAction; ?>" method="post" name="frmLogin" id="frmLogin" class="frmBasics">
                   		<?php if ($_GET['Footer'] == "1" || $_POST['hfFooter'] == "1")
						{ ?>
							<input type="hidden" name="hfFooter" value="1" />
						<?php }//end of if ?>
						<input type="hidden" name="hfSubFolder" value="<?php 
						if($_GET['SubFolder'] != "")
							echo $_GET['SubFolder'];
					   else
							echo $_POST['hfSubFolder']; ?>" />
					   <input type="hidden" name="hfSection" value="<?php 
					   //checks if there is a section
					   if($_GET['section'] != "")
							echo $_GET['section'];
					   else
							echo $_POST['hfSection']; ?>" />
						 <!-- InstanceBeginEditable name="hiddenAreaForLogIn" --><!-- InstanceEndEditable -->
                   
                    	<a href="../../index.php" class="aHeaderFooterLinks">Home &nbsp;&nbsp;| </a>                  
                    	<a href="../../Contact.php?section=Contact&Footer=1" class="aHeaderFooterLinks">Contact Us &nbsp;&nbsp;| </a>
                    	<a href="../../FAQ.php?section=FAQ&Footer=1" class="aHeaderFooterLinks">FAQ's &nbsp;&nbsp;| </a>
                       
                        <?php 
						//checks if the user log in
                        if (checksUserID() !== FALSE)
                        {
							$strLogOutString = "";//holds the log in string for POST data as they will be esse when the Session is deleted this will save then
						
                            if(strstr($_SERVER['REQUEST_URI'],"?") !== FALSE)
                                $strStartURL = "&amp;";
							else
							{
								$strFooter = "";//holds if the footer is being used
							
								if ($_POST['hfFooter'] == "1")
									$strFooter = "&Footer=1";
									
								$strLogOutString = "&section=".$_POST['hfSection']."&SubFolder=".$_POST['hfSubFolder'].$strFooter;
								?><!-- InstanceBeginEditable name="hiddenAreaForLogOut" --><!-- InstanceEndEditable --><?php
							}//end of else
							                                       
                            echo "<a href=\"http://".$_SERVER['HTTP_HOST']."/".substr($_SERVER['REQUEST_URI'],1).$strStartURL."logoff=".$strLogOutString."\" class=\"aHeaderFooterLinks\">Sign Out &nbsp;&nbsp;| </a>
                            <a href=\"".$strFilePath."Profile.php\" class=\"aHeaderFooterLinks\">Edit Profile</a>";
                        }//end of if
                        else
                            echo "<a href=\"javascript:void(0);\" class=\"aHeaderFooterLinks\"  onclick=\"javascript:toggleLayer('divBasicLogInHidden','divGrayBG');\">Sign In &nbsp;&nbsp;| </a>
                            <a href=\"".$strFilePath."SignUp.php?section=SignUp&Footer=1\" class=\"aHeaderFooterLinks\">Build A Plan Today</a>"; ?>
                    
                        <div class="divBasicHidden boardBox" id="divBasicLogInHidden">
                            <div class="customContainer infoLogInContainer lblContinuityBackgroundColor">
                                <div class="customContent infoLogInContent">
                                    <label>Log In</label>
                                </div>
                                <div align="right" class="customContent infoLogInNavigation">
                                    <div class="divClose">
                                       <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divBasicLogInHidden','divGrayBG');">Close</a>
                                    </div>
                                </div>
                                <div class="customFooter infoLogInFooter"></div>
                            </div><!-- end of Cotinater -->
                            <div class="divHiddlenBody">
                                <div class="customContainer divBasicLogInContainer">
                                    <div class="customHeader divBasicLogInHeader">
                                        <div id="divLogInError" class="divBasicMessage"></div>
                                    </div>
                                    <div class="customContent divBasicLogInContent">
                                        <label>User Name:</label>
                                    </div>
                                    <div class="customNavigation divBasicLogInNavigation">
                                        <label>Password:</label>
                                    </div>
                                    <div class="customFooter divBasicLogInFooter"></div>
                                    <div class="customContent divBasicLogInContent">
                                        <input name="txtUserName" type="text" id="txtUserName" size="23" />
                                    </div>
                                    <div class="customNavigation divBasicLogInNavigation">
                                        <input name="txtPassword" type="password" id="txtPassword" size="20" />
                                    </div>
                                  <div class="customThridContent divBasicLogInThridContent">
                                        <input type="hidden" id="hfLogInCount" name="hfLogInCount" value="<?php 
										if($_POST['hfLogInCount'] == "")
											echo "0";
										else
											echo $_POST['hfLogInCount'];?>" />
                                      <input type="submit" value="Log In" onclick="getDocID('hfLogInCount').value = parseInt(getDocID('hfLogInCount').value) + 1"/>
                                   </div>
                                   <div class="customFooter divBasicLogInFooter"></div>
                                </div><!-- end of Cotinater -->
                            </div>
                        </div><!-- end of Hidden Div -->
                    </form>
                </div><!-- end of Header Navigation -->
                <div class="customFooter" id="divBasicFooter" align="left">
               		<ul id="navigation-1">
						<li>
                        	<a href="../../AboutUs.php?section=About&Footer=1" onClick="changeImage('imgAboutUs',<?php echo "'".$strFilePath."images"; ?>/aboutusdown.jpg')" onMouseOver="<?php if($_GET['section'] == "About" || $_POST['hfSection'] == "About")  echo ""; else echo "changeImage('imgAboutUs','".$strFilePath."images/aboutusover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "About" || $_POST['hfSection'] == "About") echo ""; else echo "changeImage('imgAboutUs','".$strFilePath."images/aboutusout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "About" || $_POST['hfSection'] == "About") echo "src=\"".$strFilePath."images/aboutusdown.jpg\""; else echo "src=\"".$strFilePath."images/aboutusout.jpg\""; ?> alt="About Us" width="128" height="49" id="imgAboutUs" /></a>
                        </li>
						<li>
                        	<a href="../../WhyPlan.php?section=Plan&Footer=1" onClick="changeImage('imgPlan',<?php echo "'".$strFilePath."images"; ?>/whyplandown.jpg')" onMouseOver="<?php if($_GET['section'] == "Plan" || $_POST['hfSection'] == "Plan") echo ""; else echo "changeImage('imgPlan','".$strFilePath."images/whyplanover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Plan" || $_POST['hfSection'] == "Plan") echo ""; else echo "changeImage('imgPlan','".$strFilePath."images/whyplanout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "Plan" || $_POST['hfSection'] == "Plan") echo "src=\"".$strFilePath."images/whyplandown.jpg\""; else echo "src=\"".$strFilePath."images/whyplanout.jpg\""; ?> alt="Why Plan?" id="imgPlan" /></a>
							<ul class="navigation-2">
                            	<li><a href="../../Partners.php?section=Partners&Footer=1#People">Protect Your People</a></li>
                            	<li><a href="../../Partners.php?section=Partners&Footer=1#Power">Protect Your Power</a></li>
                            	<li><a href="../../Partners.php?section=Partners&Footer=1#Information">Protect Your Information</a></li>
								<li><a href="../../Partners.php?section=Partners&Footer=1#Space">Protect Your Space</a></li>
                                <li><a href="../../AssessYourBusiness.php?section=Plan&Footer=1">Assess Your Business</a></li>
							</ul>
						</li>
						<li>
                        	<a href="../../Solutions.php?section=Solutions&both=1" onClick="changeImage('imgSolutions',<?php echo "'".$strFilePath."images"; ?>/solutionsdown.jpg')" onMouseOver="<?php if($_GET['section'] == "Solutions" || $_POST['hfSection'] == "Solutions") echo ""; else echo "changeImage('imgSolutions','".$strFilePath."images/solutionsover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Solutions" || $_POST['hfSection'] == "Solutions") echo ""; else echo "changeImage('imgSolutions','".$strFilePath."images/solutionsout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "Solutions" || $_POST['hfSection'] == "Solutions") echo "src=\"".$strFilePath."images/solutionsdown.jpg\""; else echo "src=\"".$strFilePath."images/solutionsout.jpg\""; ?> alt="Our Solutions" id="imgSolutions" /></a>
                            <ul class="navigation-2">
                            	<li><a href="../../Home.php?section=Solutions">Our Home Solutions</a></li>
                                <li><a href="../../Solutions.php?section=Solutions">Our Business Solutions</a></li>
                            </ul>
                        </li>
                        <li>
                        	<a href="../../Services.php?section=Services&Footer=1" onClick="changeImage('imgServices',<?php echo "'".$strFilePath."images"; ?>/servicesdown.jpg')" onMouseOver="<?php if($_GET['section'] == "Services" || $_POST['hfSection'] == "Services") echo ""; else echo "changeImage('imgServices','".$strFilePath."images/servicesover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Services" || $_POST['hfSection'] == "Services") echo ""; else echo "changeImage('imgServices','".$strFilePath."images/servicesout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "Services" || $_POST['hfSection'] == "Services") echo "src=\"".$strFilePath."images/servicesdown.jpg\""; else echo "src=\"".$strFilePath."images/servicesout.jpg\""; ?> alt="Our Services" id="imgServices" /></a>
                            <ul class="navigation-2">
                                <li><a href="../../FourRs.php?section=Services&Footer=1&r=1">Reduce The Risk</a></li>
                                <li><a href="../../FourRs.php?section=Services&Footer=1&r=2">Respond To A Disaster</a></li>
                                <li><a href="../../FourRs.php?section=Services&Footer=1&r=3">Recover After A Loss</a></li>
                                <li><a href="../../FourRs.php?section=Services&Footer=1&r=4">Restore Your Business</a></li>
                            </ul>
                        </li>
						<li>
                        	<a href="../../Partners.php?section=Partners&Footer=1" onClick="changeImage('imgPartners',<?php echo "'".$strFilePath."images"; ?>/partnersdown.jpg')" onMouseOver="<?php if($_GET['section'] == "Partners" || $_POST['hfSection'] == "Partners") echo ""; else echo "changeImage('imgPartners','".$strFilePath."images/partnersover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Partners" || $_POST['hfSection'] == "Partners") echo ""; else echo "changeImage('imgPartners','".$strFilePath."images/partnersout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "Partners" || $_POST['hfSection'] == "Partners") echo "src=\"".$strFilePath."images/partnersdown.jpg\""; else echo "src=\"".$strFilePath."images/partnersout.jpg\""; ?> alt="Our Partners" id="imgPartners" /></a>                        </li>
                        <li>
                        	<a href="../../Media.php?section=Media&Footer=1" onClick="changeImage('imgMedia',<?php echo "'".$strFilePath."images"; ?>/mediadown.jpg')" onMouseOver="<?php if($_GET['section'] == "Media" || $_POST['hfSection'] == "Media") echo ""; else echo "changeImage('imgMedia','".$strFilePath."images/mediaover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Media" || $_POST['hfSection'] == "Media") echo ""; else echo "changeImage('imgMedia','".$strFilePath."images/mediaout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "Media" || $_POST['hfSection'] == "Media") echo "src=\"".$strFilePath."images/mediadown.jpg\""; else echo "src=\"".$strFilePath."images/mediaout.jpg\""; ?> alt="Media" id="imgMedia" /></a>
                       	</li>
                        <li>
                        	<a href="../../Store.php?section=Store&Footer=1" onClick="changeImage('imgStore',<?php echo "'".$strFilePath."images"; ?>/ourstoreoverdown.jpg')" onMouseOver="<?php if($_GET['section'] == "Store" || $_POST['hfSection'] == "Store") echo ""; else echo "changeImage('imgStore','".$strFilePath."images/ourstoreover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Store" || $_POST['hfSection'] == "Store") echo ""; else echo "changeImage('imgStore','".$strFilePath."images/ourstoreout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "Store" || $_POST['hfSection'] == "Store") echo "src=\"".$strFilePath."images/ourstoreoverdown.jpg\""; else  echo "src=\"".$strFilePath."images/ourstoreout.jpg\""; ?> alt="Our Store" width="128" height="49" id="imgStore" /></a>                    </li>
                        <li>
                        	<a href="../../RSA/Offer.php?section=Access&Footer=1&SubFolder=1" onMouseOver="<?php if($_GET['section'] == "Access" || $_POST['hfSection'] == "Access") echo ""; else echo "changeImage('imgAccess','".$strFilePath."images/RSAtopover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Access" || $_POST['hfSection'] == "Access") echo ""; else echo "changeImage('imgAccess','".$strFilePath."images/RSAtopout.jpg');"; ?>">
                           	<img alt="Exclusive Access" name="imgAccess" id="imgAccess" <?php if($_GET['section'] == "Access" || $_POST['hfSection'] == "Access") echo "src=\"".$strFilePath."images/RSAtopover.jpg\""; else echo "src=\"".$strFilePath."images/RSAtopout.jpg\""; ?> /></a>                        </li>
					</ul>
                  		<div class="customFooter"></div>
                    </div>
                <!-- end of Header Footer -->
              </div><!-- end of Header Container -->
              <h1><!-- InstanceBeginEditable name="h1Title" -->My Continuity Plans - <?php echo $strEdition; ?> Edition - Information Technology<!-- InstanceEndEditable --></h1>
          	</div><!-- end of Header -->
            <div class="customContent" id="divBasicContent">
                
		<form action="<?php echo $Form; ?>" method="post" id="frmForm" class="frmBasics" enctype="multipart/form-data"> 
              <div class="customContainer" id="divFloatingMenuContainer">
                <div class="customContent" id="divFloatingMenuContent">
                	<div align="left">
			                <!-- InstanceBeginEditable name="PlanContent" -->                         
							<label>In todays modern world Technology plays a major role in business operation and development. Without technology most business would be unable to function. Use the following section to define you Information Technology requirements so in the time of a disaster you are able to recovery any and all lost items.</label>
                            <br /><br />
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 1
								<br /><br />
								Please List any and all software products used currently by your business:</label>
								<br /><br />
<label class="lblSubQuestion">Software Program #1 Name: </label><input type="text" name="IT_CI01" value="<?php echo $row_rsForm['IT_CI01']; ?>" size="20" maxlength="100" /><br />
<label>Provider:</label> <input type="text" name="IT_1SW01" value="<?php echo $row_rsForm['IT_1SW01']; ?>" size="20" maxlength="100" /><br />
<label>How Many Copies do your use in your office:</label> <input type="text" name="IT_1SW02" value="<?php echo $row_rsForm['IT_1SW02']; ?>" size="20" maxlength="100" /><br />
<label>Serial Number:</label> <input type="text" name="IT_1SW03" value="<?php echo $row_rsForm['IT_1SW03']; ?>" size="20" maxlength="100" /><br />
<label>License #:</label> <input type="text" name="IT_1SW04" value="<?php echo $row_rsForm['IT_1SW04']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please Provide a description of what this product is used for:</label><br /><textarea name="IT_1SW05" cols="85" rows="30"><?php echo $row_rsForm['IT_1SW05']; ?></textarea>
<br /><br />
<label class="lblSubQuestion">Software Program #2 Name:</label> <input type="text" name="IT_CI02" value="<?php echo $row_rsForm['IT_CI02']; ?>" size="20" maxlength="100" /><br />
<label>Provider:</label> <input type="text" name="IT_2SW01" value="<?php echo $row_rsForm['IT_2SW01']; ?>" size="20" maxlength="100" /><br />
<label>How Many Copies do your use in your office:</label> <input type="text" name="IT_2SW02" value="<?php echo $row_rsForm['IT_2SW02']; ?>" size="20" maxlength="100" /><br />
<label>Serial Number:</label> <input type="text" name="IT_2SW03" value="<?php echo $row_rsForm['IT_2SW03']; ?>" size="20" maxlength="100" /><br />
<label>License #:</label> <input type="text" name="IT_2SW04" value="<?php echo $row_rsForm['IT_2SW04']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please Provide a description of what this product is used for:</label><br /><textarea name="IT_2SW05" cols="85" rows="30"><?php echo $row_rsForm['IT_2SW05']; ?></textarea>
<br /><br />
<label class="lblSubQuestion">Software Program #3 Name:</label> <input type="text" name="IT_CI03" value="<?php echo $row_rsForm['IT_CI03']; ?>" size="20" maxlength="100" /><br />
<label>Provider:</label> <input type="text" name="IT_3SW01" value="<?php echo $row_rsForm['IT_3SW01']; ?>" size="20" maxlength="100" /><br />
<label>How Many Copies do your use in your office:</label> <input type="text" name="IT_3SW02" value="<?php echo $row_rsForm['IT_3SW02']; ?>" size="20" maxlength="100" /><br />
<label>Serial Number:</label> <input type="text" name="IT_3SW03" value="<?php echo $row_rsForm['IT_3SW03']; ?>" size="20" maxlength="100" /><br />
<label>License #:</label> <input type="text" name="IT_3SW04" value="<?php echo $row_rsForm['IT_3SW04']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please Provide a description of what this product is used for:</label><br /><textarea name="IT_3SW05" cols="85" rows="30"><?php echo $row_rsForm['IT_3SW05']; ?></textarea>
<br /><br />
<label class="lblSubQuestion">Software Program #4 Name:</label> <input type="text" name="IT_CI04" value="<?php echo $row_rsForm['IT_CI04']; ?>" size="20" maxlength="100" /><br />
<label>Provider:</label> <input type="text" name="IT_4SW01" value="<?php echo $row_rsForm['IT_4SW01']; ?>" size="20" maxlength="100" /><br />
<label>How Many Copies do your use in your office:</label> <input type="text" name="IT_4SW02" value="<?php echo $row_rsForm['IT_4SW02']; ?>" size="20" maxlength="100" /><br />
<label>Serial Number:</label> <input type="text" name="IT_4SW03" value="<?php echo $row_rsForm['IT_4SW03']; ?>" size="20" maxlength="100" /><br />
<label>License #:</label> <input type="text" name="IT_4SW04" value="<?php echo $row_rsForm['IT_4SW04']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please Provide a description of what this product is used for:</label><br /><textarea name="IT_4SW05" cols="85" rows="30"><?php echo $row_rsForm['IT_4SW05']; ?></textarea>
<br /><br />
<label class="lblSubQuestion">Software Program #5 Name:</label> <input type="text" name="IT_CI05" value="<?php echo $row_rsForm['IT_CI05']; ?>" size="20" maxlength="100" /><br />
<label>Provider:</label> <input type="text" name="IT_5SW01" value="<?php echo $row_rsForm['IT_5SW01']; ?>" size="20" maxlength="100" /><br />
<label>How Many Copies do your use in your office:</label> <input type="text" name="IT_5SW02" value="<?php echo $row_rsForm['IT_5SW02']; ?>" size="20" maxlength="100" /><br />
<label>Serial Number:</label> <input type="text" name="IT_5SW03" value="<?php echo $row_rsForm['IT_5SW03']; ?>" size="20" maxlength="100" /><br />
<label>License #:</label> <input type="text" name="IT_5SW04" value="<?php echo $row_rsForm['IT_5SW04']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please Provide a description of what this product is used for:</label><br /><textarea name="IT_5SW05" cols="85" rows="30"><?php echo $row_rsForm['IT_5SW05']; ?></textarea>
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 2
								<br /><br />
								Please List any and all Hardware products used currently by your business:</label>
<br /><br />
<label class="lblSubQuestion">Hardware #1 Name:</label> <input type="text" name="IT_CI06" value="<?php echo $row_rsForm['IT_CI06']; ?>" size="20" maxlength="100" /><br />
<label>Provider:</label> <input type="text" name="IT_1HW01" value="<?php echo $row_rsForm['IT_1HW01']; ?>" size="20" maxlength="100" /><br />
<label>How Many Copies do your use in your office:</label> <input type="text" name="IT_1HW02" value="<?php echo $row_rsForm['IT_1HW02']; ?>" size="20" maxlength="100" /><br />
<label>Serial Number:</label> <input type="text" name="IT_1HW03" value="<?php echo $row_rsForm['IT_1HW03']; ?>" size="20" maxlength="100" /><br />
<label>License #:</label> <input type="text" name="IT_1HW04" value="<?php echo $row_rsForm['IT_1HW04']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please Provide a description of what this Hardware is used for:</label><textarea name="IT_1HW05" cols="85" rows="30"><?php echo $row_rsForm['IT_1HW05']; ?></textarea>
<br /><br />
<label class="lblSubQuestion">Hardware #2 Name:</label> <input type="text" name="IT_CI07" value="<?php echo $row_rsForm['IT_CI07']; ?>" size="20" maxlength="100" /><br />
<label>Provider:</label> <input type="text" name="IT_2HW01" value="<?php echo $row_rsForm['IT_2HW01']; ?>" size="20" maxlength="100" /><br />
<label>How Many Copies do your use in your office:</label> <input type="text" name="IT_2HW02" value="<?php echo $row_rsForm['IT_2HW02']; ?>" size="20" maxlength="100" /><br />
<label>Serial Number:</label> <input type="text" name="IT_2HW03" value="<?php echo $row_rsForm['IT_2HW03']; ?>" size="20" maxlength="100" /><br />
<label>License #:</label> <input type="text" name="IT_2HW04" value="<?php echo $row_rsForm['IT_2HW04']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please Provide a description of what this Hardware is used for:</label><br /><textarea name="IT_2HW05" cols="85" rows="30"><?php echo $row_rsForm['IT_2HW05']; ?></textarea>
<br /><br />
<label class="lblSubQuestion">Hardware #3 Name:</label> <input type="text" name="IT_CI08" value="<?php echo $row_rsForm['IT_CI08']; ?>" size="20" maxlength="100" /><br />
<label>Provider:</label> <input type="text" name="IT_3HW01" value="<?php echo $row_rsForm['IT_3HW01']; ?>" size="20" maxlength="100" /><br />
<label>How Many Copies do your use in your office:</label> <input type="text" name="IT_3HW02" value="<?php echo $row_rsForm['IT_3HW02']; ?>" size="20" maxlength="100" /><br />
<label>Serial Number:</label> <input type="text" name="IT_3HW03" value="<?php echo $row_rsForm['IT_3HW03']; ?>" size="20" maxlength="100" /><br />
<label>License #:</label> <input type="text" name="IT_3HW04" value="<?php echo $row_rsForm['IT_3HW04']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please Provide a description of what this Hardware is used for:</label><br /><textarea name="IT_3HW05" cols="85" rows="30"><?php echo $row_rsForm['IT_3HW05']; ?></textarea>
<br /><br />
<label class="lblSubQuestion">Hardware #4 Name:</label> <input type="text" name="IT_CI09" value="<?php echo $row_rsForm['IT_CI09']; ?>" size="20" maxlength="100" /><br />
<label>Provider:</label> <input type="text" name="IT_4HW01" value="<?php echo $row_rsForm['IT_4HW01']; ?>" size="20" maxlength="100" /><br />
<label>How Many Copies do your use in your office:</label> <input type="text" name="IT_4HW02" value="<?php echo $row_rsForm['IT_4HW02']; ?>" size="20" maxlength="100" /><br />
<label>Serial Number:</label> <input type="text" name="IT_4HW03" value="<?php echo $row_rsForm['IT_4HW03']; ?>" size="20" maxlength="100" /><br />
<label>License #:</label> <input type="text" name="IT_4HW04" value="<?php echo $row_rsForm['IT_4HW04']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please Provide a description of what this Hardware is used for:</label><br /> <textarea name="IT_4HW05" cols="85" rows="30"><?php echo $row_rsForm['IT_4HW05']; ?></textarea>
<br /><br />
<label class="lblSubQuestion">Hardware #5 Name:</label> <input type="text" name="IT_CI10" value="<?php echo $row_rsForm['IT_CI10']; ?>" size="20" maxlength="100" /><br />
<label>Provider:</label> <input type="text" name="IT_5HW01" value="<?php echo $row_rsForm['IT_5HW01']; ?>" size="20" maxlength="100" /><br />
<label>How Many Copies do your use in your office:</label> <input type="text" name="IT_5HW02" value="<?php echo $row_rsForm['IT_5HW02']; ?>" size="20" maxlength="100" /><br />
<label>Serial Number:</label> <input type="text" name="IT_5HW03" value="<?php echo $row_rsForm['IT_5HW03']; ?>" size="20" maxlength="100" /><br />
<label>License #:</label> <input type="text" name="IT_5HW04" value="<?php echo $row_rsForm['IT_5HW04']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please Provide a description of what this Hardware is used for:</label><br /><textarea name="IT_5HW05" cols="85" rows="30"><?php echo $row_rsForm['IT_5HW05']; ?></textarea>
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 3
								<br /><br />
								If you business currently stores your information on an internal or external server please identify your server requirements below. Please be as accurate as possible:</label>
<br /><br />
<label class="lblSubQuestion">Server #1 Name:</label> <input type="text" name="IT_CI11" value="<?php echo $row_rsForm['IT_CI11']; ?>" size="20" maxlength="100" /><br />
<label>Provider:</label> <input type="text" name="IT_1SR01" value="<?php echo $row_rsForm['IT_1SR01']; ?>" size="20" maxlength="100" /><br />
<label>How Many CPU's use this server:</label> <input type="text" name="IT_1SR02" value="<?php echo $row_rsForm['IT_1SR02']; ?>" size="20" maxlength="100" /><br />
<label>Model:</label> <input type="text" name="IT_1SR03" value="<?php echo $row_rsForm['IT_1SR03']; ?>" size="20" maxlength="100" /><br />
<label>Size/Capacity #:</label> <input type="text" name="IT_1SR04" value="<?php echo $row_rsForm['IT_1SR04']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please Provide a description of the setup and over all operation of this server:</label><br /> <textarea name="IT_1SR05" cols="85" rows="30"><?php echo $row_rsForm['IT_1SR05']; ?></textarea>
<br /><br />
<label class="lblSubQuestion">Server #2 Name:</label> <input type="text" name="IT_CI12" value="<?php echo $row_rsForm['IT_CI12']; ?>" size="20" maxlength="100" /><br />
<label>Provider:</label> <input type="text" name="IT_2SR01" value="<?php echo $row_rsForm['IT_2SR01']; ?>" size="20" maxlength="100" /><br />
<label>How Many CPU's use this server:</label> <input type="text" name="IT_2SR02" value="<?php echo $row_rsForm['IT_2SR02']; ?>" size="20" maxlength="100" /><br />
<label>Model:</label> <input type="text" name="IT_2SR03" value="<?php echo $row_rsForm['IT_2SR03']; ?>" size="20" maxlength="100" /><br />
<label>Size/Capacity #:</label> <input type="text" name="IT_2SR04" value="<?php echo $row_rsForm['IT_2SR04']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please Provide a description of the setup and over all operation of this server:</label><br /> <textarea name="IT_2SR05" cols="85" rows="30"><?php echo $row_rsForm['IT_2SR05']; ?></textarea>
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 4
								<br /><br />
								If you currently have a back-up system in place please identify the program and equipment you currently use on a day to day basis.</label>
<br /><br />
<label class="lblSubQuestion">Tape Name:</label> <input type="text" name="IT_CI13" value="<?php echo $row_rsForm['IT_CI13']; ?>" size="20" maxlength="100" /><br />
<label>Name:</label> <input type="text" name="IT_1TP01" value="<?php echo $row_rsForm['IT_1TP01']; ?>" size="20" maxlength="100" /><br />
<label>How Many Tapes do your require:</label> <input type="text" name="IT_1TP02" value="<?php echo $row_rsForm['IT_1TP02']; ?>" size="20" maxlength="100" /><br />
<label>Software program Used for Back-up procedures:</label> <input type="text" name="IT_1TP03" value="<?php echo $row_rsForm['IT_1TP03']; ?>" size="20" maxlength="100" />
<br /><br />
<label>Please provide a brief description of the back-up process:</label><br /> <textarea name="IT_1TP04" cols="85" rows="30"><?php echo $row_rsForm['IT_1TP04']; ?></textarea><br />
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 5 
                                <br /><br />
                                Information Technology Equipment Inventory Checklist:</label>
								<br /><br />
								<label>Please identify how many pieces of each item you business currently uses?</label>
								<br /><br />
								<label class="lblSubQuestion">Computer Monitors:</label>
								<br /><br />
								<label>Brand 1: </label><input type="text" name="IT_1IN01" value="<?php echo $row_rsForm['IT_1IN01']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require: </label><input type="text" name="IT_1IN02" value="<?php echo $row_rsForm['IT_1IN02']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN03" value="<?php echo $row_rsForm['IT_1IN03']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Description of specific details: </label><br /><textarea name="IT_1IN04" cols="85" rows="30"><?php echo $row_rsForm['IT_1IN04']; ?></textarea>
								<br /><br />
								<label>Brand 2: </label><input type="text" name="IT_1IN05" value="<?php echo $row_rsForm['IT_1IN05']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require: </label><input type="text" name="IT_1IN06" value="<?php echo $row_rsForm['IT_1IN06']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN07" value="<?php echo $row_rsForm['IT_1IN07']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Description of specific details: </label><br /><textarea name="IT_1IN08" cols="85" rows="30"><?php echo $row_rsForm['IT_1IN08']; ?></textarea>
								<br /><br />
								<label>Brand 3: </label><input type="text" name="IT_1IN09" value="<?php echo $row_rsForm['IT_1IN09']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require: </label><input type="text" name="IT_1IN10" value="<?php echo $row_rsForm['IT_1IN10']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN11" value="<?php echo $row_rsForm['IT_1IN11']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Description of specific details: </label><br /><textarea name="IT_1IN12" cols="85" rows="30"><?php echo $row_rsForm['IT_1IN12']; ?></textarea>
                                <br /><br />
								<label class="lblSubQuestion">Computer CPU Units:</label>
								<br /><br />
								<label>Brand 1: </label><input type="text" name="IT_1IN13" value="<?php echo $row_rsForm['IT_1IN13']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require: </label><input type="text" name="IT_1IN14" value="<?php echo $row_rsForm['IT_1IN14']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN15" value="<?php echo $row_rsForm['IT_1IN15']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Description of specific details: </label><br /><textarea name="IT_1IN16" cols="85" rows="30"><?php echo $row_rsForm['IT_1IN16']; ?></textarea>
								<br /><br />
								<label>Brand 2: </label><input type="text" name="IT_1IN17" value="<?php echo $row_rsForm['IT_1IN17']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require: </label><input type="text" name="IT_1IN18" value="<?php echo $row_rsForm['IT_1IN18']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN19" value="<?php echo $row_rsForm['IT_1IN19']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Description of specific details: </label><br /><textarea name="IT_1IN20" cols="85" rows="30"><?php echo $row_rsForm['IT_1IN20']; ?></textarea>
								<br /><br />
								<label>Brand 3: </label><input type="text" name="IT_1IN21" value="<?php echo $row_rsForm['IT_1IN21']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require: </label><input type="text" name="IT_1IN22" value="<?php echo $row_rsForm['IT_1IN22']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN23" value="<?php echo $row_rsForm['IT_1IN23']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Description of specific details: </label><br /><textarea name="IT_1IN24" cols="85" rows="30"><?php echo $row_rsForm['IT_1IN24']; ?></textarea>
								<br /><br />
								<label class="lblSubQuestion">Computer Keyboards:</label>
								<br /><br />
								<label>Brand 1: </label><input type="text" name="IT_1IN25" value="<?php echo $row_rsForm['IT_1IN25']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require: </label><input type="text" name="IT_1IN26" value="<?php echo $row_rsForm['IT_1IN26']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN27" value="<?php echo $row_rsForm['IT_1IN27']; ?>" size="20" maxlength="100" />
								<br /><br />
								<label>Brand 2: </label><input type="text" name="IT_1IN28" value="<?php echo $row_rsForm['IT_1IN28']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require: </label><input type="text" name="IT_1IN29" value="<?php echo $row_rsForm['IT_1IN29']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN30" value="<?php echo $row_rsForm['IT_1IN30']; ?>" size="20" maxlength="100" />
								<br /><br />
								<label class="lblSubQuestion">Computer Mouses:</label>
								<br /><br />
								<label>Brand 1:</label><input type="text" name="IT_1IN31" value="<?php echo $row_rsForm['IT_1IN31']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require: </label><input type="text" name="IT_1IN32" value="<?php echo $row_rsForm['IT_1IN32']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN33" value="<?php echo $row_rsForm['IT_1IN33']; ?>" size="20" maxlength="100" />
								<br /><br />
								<label class="lblSubQuestion">Computer Printers:</label>
								<br />
								<label>Brand 1: </label><input type="text" name="IT_1IN34" value="<?php echo $row_rsForm['IT_1IN34']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require: </label><input type="text" name="IT_1IN35" value="<?php echo $row_rsForm['IT_1IN35']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN36" value="<?php echo $row_rsForm['IT_1IN36']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Description of specific details: </label><br /><textarea name="IT_1IN37" cols="85" rows="30"><?php echo $row_rsForm['IT_1IN37']; ?></textarea>
								<br />
								<label>Brand 2: </label><input type="text" name="IT_1IN38" value="<?php echo $row_rsForm['IT_1IN38']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require: </label><input type="text" name="IT_1IN39" value="<?php echo $row_rsForm['IT_1IN39']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN40" value="<?php echo $row_rsForm['IT_1IN40']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Description of specific details: </label><br /><textarea name="IT_1IN41" cols="85" rows="30"><?php echo $row_rsForm['IT_1IN41']; ?></textarea>
								<br />
								<label>Brand 3:</label><input type="text" name="IT_1IN42" value="<?php echo $row_rsForm['IT_1IN42']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require:</label><input type="text" name="IT_1IN43" value="<?php echo $row_rsForm['IT_1IN43']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN44" value="<?php echo $row_rsForm['IT_1IN44']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Description of specific details: </label><br /><textarea name="IT_1IN45" cols="85" rows="30"><?php echo $row_rsForm['IT_1IN45']; ?></textarea>
								<br /><br />
								<label class="lblSubQuestion">Computer Fax/Scanners:</label>
								<br />
								<label>Brand 1:</label><input type="text" name="IT_1IN46" value="<?php echo $row_rsForm['IT_1IN46']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require:</label><input type="text" name="IT_1IN47" value="<?php echo $row_rsForm['IT_1IN47']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN48" value="<?php echo $row_rsForm['IT_1IN48']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Description of specific details: </label><br /><textarea name="IT_1IN49" cols="85" rows="30"><?php echo $row_rsForm['IT_1IN49']; ?></textarea>
								<br />
								<label>Brand 2:</label><input type="text" name="IT_1IN50" value="<?php echo $row_rsForm['IT_1IN50']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require:</label><input type="text" name="IT_1IN51" value="<?php echo $row_rsForm['IT_1IN51']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN52" value="<?php echo $row_rsForm['IT_1IN52']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Description of specific details: </label><br /><textarea name="IT_1IN53" cols="85" rows="30"><?php echo $row_rsForm['IT_1IN53']; ?></textarea>
								<br /><br />
								<label class="lblSubQuestion">Computer Photocopier:</label>
								<br />
								<label>Brand 1: </label><input type="text" name="IT_1IN54" value="<?php echo $row_rsForm['IT_1IN54']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Do You Require: </label><input type="text" name="IT_1IN55" value="<?php echo $row_rsForm['IT_1IN55']; ?>" size="20" maxlength="100" /><br />
								<label>Replacement Value $</label><input type="text" name="IT_1IN56" value="<?php echo $row_rsForm['IT_1IN56']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Description of specific details: </label><br /><textarea name="IT_1IN57" cols="85" rows="30"><?php echo $row_rsForm['IT_1IN57']; ?></textarea>
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 6</label>
								<br /><br />
								<label>This Section is Designed to give you access to your Computer Programs in the event of a disaster and the employees are not available to give you access to their accounts. This information will allow you to access any essential information for the time of recovery. Make it clear to all employees this information will on be accessed in the event they are unavailable.</label>
								<br /><br />
								<label class="lblSubQuestion">Program #1: </label><input type="text" name="IT_EMPro01" value="<?php echo $row_rsForm['IT_EMPro01']; ?>" size="20" maxlength="100" />
								<br /><br />
                                <ol class="olForms">                 
									<li><label>Employee Name: </label><input type="text" name="IT_EMP01" value="<?php echo $row_rsForm['IT_EMP01']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER01" value="<?php echo $row_rsForm['IT_USER01']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS01" value="<?php echo $row_rsForm['IT_PASS01']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP02" value="<?php echo $row_rsForm['IT_EMP02']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER02" value="<?php echo $row_rsForm['IT_USER02']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS02" value="<?php echo $row_rsForm['IT_PASS02']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP03" value="<?php echo $row_rsForm['IT_EMP03']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER03" value="<?php echo $row_rsForm['IT_USER03']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS03" value="<?php echo $row_rsForm['IT_PASS03']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP04" value="<?php echo $row_rsForm['IT_EMP04']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER04" value="<?php echo $row_rsForm['IT_USER04']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS04" value="<?php echo $row_rsForm['IT_PASS04']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP05" value="<?php echo $row_rsForm['IT_EMP05']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER05" value="<?php echo $row_rsForm['IT_USER05']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS05" value="<?php echo $row_rsForm['IT_PASS05']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP06" value="<?php echo $row_rsForm['IT_EMP06']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER06" value="<?php echo $row_rsForm['IT_USER06']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS06" value="<?php echo $row_rsForm['IT_PASS06']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP07" value="<?php echo $row_rsForm['IT_EMP07']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER07" value="<?php echo $row_rsForm['IT_USER07']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS07" value="<?php echo $row_rsForm['IT_PASS07']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP08" value="<?php echo $row_rsForm['IT_EMP08']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER08" value="<?php echo $row_rsForm['IT_USER08']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS08" value="<?php echo $row_rsForm['IT_PASS08']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP09" value="<?php echo $row_rsForm['IT_EMP09']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER09" value="<?php echo $row_rsForm['IT_USER09']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS09" value="<?php echo $row_rsForm['IT_PASS09']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP10" value="<?php echo $row_rsForm['IT_EMP10']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER10" value="<?php echo $row_rsForm['IT_USER10']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS10" value="<?php echo $row_rsForm['IT_PASS10']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP11" value="<?php echo $row_rsForm['IT_EMP11']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER11" value="<?php echo $row_rsForm['IT_USER11']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS11" value="<?php echo $row_rsForm['IT_PASS11']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP12" value="<?php echo $row_rsForm['IT_EMP12']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER12" value="<?php echo $row_rsForm['IT_USER12']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS12" value="<?php echo $row_rsForm['IT_PASS12']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP13" value="<?php echo $row_rsForm['IT_EMP13']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER13" value="<?php echo $row_rsForm['IT_USER13']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS13" value="<?php echo $row_rsForm['IT_PASS13']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP14" value="<?php echo $row_rsForm['IT_EMP14']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER14" value="<?php echo $row_rsForm['IT_USER14']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS14" value="<?php echo $row_rsForm['IT_PASS14']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP15" value="<?php echo $row_rsForm['IT_EMP15']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER15" value="<?php echo $row_rsForm['IT_USER15']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS15" value="<?php echo $row_rsForm['IT_PASS15']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP16" value="<?php echo $row_rsForm['IT_EMP16']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER16" value="<?php echo $row_rsForm['IT_USER16']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS16" value="<?php echo $row_rsForm['IT_PASS16']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP17" value="<?php echo $row_rsForm['IT_EMP17']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER17" value="<?php echo $row_rsForm['IT_USER17']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS17" value="<?php echo $row_rsForm['IT_PASS17']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP18" value="<?php echo $row_rsForm['IT_EMP18']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER18" value="<?php echo $row_rsForm['IT_USER18']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS18" value="<?php echo $row_rsForm['IT_PASS18']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP19" value="<?php echo $row_rsForm['IT_EMP19']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER19" value="<?php echo $row_rsForm['IT_USER19']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS19" value="<?php echo $row_rsForm['IT_PASS19']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_EMP20" value="<?php echo $row_rsForm['IT_EMP20']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_USER20" value="<?php echo $row_rsForm['IT_USER20']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_PASS20" value="<?php echo $row_rsForm['IT_PASS20']; ?>" size="20" maxlength="100" /></li>
         <?php // this will display only for Standard
		if ($row_loginFoundUser['Solution'] == 2)
		{ ?>
  			<li><label>Employee Name: </label><input type="text" name="ITStd_001" value="<?php echo $row_rsForm2['ITStd_001']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_002" value="<?php echo $row_rsForm2['ITStd_002']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_003" value="<?php echo $row_rsForm2['ITStd_003']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_004" value="<?php echo $row_rsForm2['ITStd_004']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_005" value="<?php echo $row_rsForm2['ITStd_005']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_006" value="<?php echo $row_rsForm2['ITStd_006']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_007" value="<?php echo $row_rsForm2['ITStd_007']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_008" value="<?php echo $row_rsForm2['ITStd_008']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_009" value="<?php echo $row_rsForm2['ITStd_009']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_010" value="<?php echo $row_rsForm2['ITStd_010']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_011" value="<?php echo $row_rsForm2['ITStd_011']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_012" value="<?php echo $row_rsForm2['ITStd_012']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_013" value="<?php echo $row_rsForm2['ITStd_013']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_014" value="<?php echo $row_rsForm2['ITStd_014']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_015" value="<?php echo $row_rsForm2['ITStd_015']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_016" value="<?php echo $row_rsForm2['ITStd_016']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_017" value="<?php echo $row_rsForm2['ITStd_017']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_018" value="<?php echo $row_rsForm2['ITStd_018']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_019" value="<?php echo $row_rsForm2['ITStd_019']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_020" value="<?php echo $row_rsForm2['ITStd_020']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_021" value="<?php echo $row_rsForm2['ITStd_021']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_022" value="<?php echo $row_rsForm2['ITStd_022']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_023" value="<?php echo $row_rsForm2['ITStd_023']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_024" value="<?php echo $row_rsForm2['ITStd_024']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_025" value="<?php echo $row_rsForm2['ITStd_025']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_026" value="<?php echo $row_rsForm2['ITStd_026']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_027" value="<?php echo $row_rsForm2['ITStd_027']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_028" value="<?php echo $row_rsForm2['ITStd_028']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_029" value="<?php echo $row_rsForm2['ITStd_029']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_030" value="<?php echo $row_rsForm2['ITStd_030']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_031" value="<?php echo $row_rsForm2['ITStd_031']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_032" value="<?php echo $row_rsForm2['ITStd_032']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_033" value="<?php echo $row_rsForm2['ITStd_033']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_034" value="<?php echo $row_rsForm2['ITStd_034']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_035" value="<?php echo $row_rsForm2['ITStd_035']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_036" value="<?php echo $row_rsForm2['ITStd_036']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_037" value="<?php echo $row_rsForm2['ITStd_037']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_038" value="<?php echo $row_rsForm2['ITStd_038']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_039" value="<?php echo $row_rsForm2['ITStd_039']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_040" value="<?php echo $row_rsForm2['ITStd_040']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_041" value="<?php echo $row_rsForm2['ITStd_041']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_042" value="<?php echo $row_rsForm2['ITStd_042']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_043" value="<?php echo $row_rsForm2['ITStd_043']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_044" value="<?php echo $row_rsForm2['ITStd_044']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_045" value="<?php echo $row_rsForm2['ITStd_045']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_046" value="<?php echo $row_rsForm2['ITStd_046']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_047" value="<?php echo $row_rsForm2['ITStd_047']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_048" value="<?php echo $row_rsForm2['ITStd_048']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_049" value="<?php echo $row_rsForm2['ITStd_049']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_050" value="<?php echo $row_rsForm2['ITStd_050']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_051" value="<?php echo $row_rsForm2['ITStd_051']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_052" value="<?php echo $row_rsForm2['ITStd_052']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_053" value="<?php echo $row_rsForm2['ITStd_053']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_054" value="<?php echo $row_rsForm2['ITStd_054']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_055" value="<?php echo $row_rsForm2['ITStd_055']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_056" value="<?php echo $row_rsForm2['ITStd_056']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_057" value="<?php echo $row_rsForm2['ITStd_057']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_058" value="<?php echo $row_rsForm2['ITStd_058']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_059" value="<?php echo $row_rsForm2['ITStd_059']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_060" value="<?php echo $row_rsForm2['ITStd_060']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_061" value="<?php echo $row_rsForm2['ITStd_061']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_062" value="<?php echo $row_rsForm2['ITStd_062']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_063" value="<?php echo $row_rsForm2['ITStd_063']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_064" value="<?php echo $row_rsForm2['ITStd_064']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_065" value="<?php echo $row_rsForm2['ITStd_065']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_066" value="<?php echo $row_rsForm2['ITStd_066']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_067" value="<?php echo $row_rsForm2['ITStd_067']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_068" value="<?php echo $row_rsForm2['ITStd_068']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_069" value="<?php echo $row_rsForm2['ITStd_069']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_070" value="<?php echo $row_rsForm2['ITStd_070']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_071" value="<?php echo $row_rsForm2['ITStd_071']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_072" value="<?php echo $row_rsForm2['ITStd_072']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_073" value="<?php echo $row_rsForm2['ITStd_073']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_074" value="<?php echo $row_rsForm2['ITStd_074']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_075" value="<?php echo $row_rsForm2['ITStd_075']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_076" value="<?php echo $row_rsForm2['ITStd_076']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_077" value="<?php echo $row_rsForm2['ITStd_077']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_078" value="<?php echo $row_rsForm2['ITStd_078']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_079" value="<?php echo $row_rsForm2['ITStd_079']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_080" value="<?php echo $row_rsForm2['ITStd_080']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_081" value="<?php echo $row_rsForm2['ITStd_081']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_082" value="<?php echo $row_rsForm2['ITStd_082']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_083" value="<?php echo $row_rsForm2['ITStd_083']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_084" value="<?php echo $row_rsForm2['ITStd_084']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_085" value="<?php echo $row_rsForm2['ITStd_085']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_086" value="<?php echo $row_rsForm2['ITStd_086']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_087" value="<?php echo $row_rsForm2['ITStd_087']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_088" value="<?php echo $row_rsForm2['ITStd_088']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_089" value="<?php echo $row_rsForm2['ITStd_089']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_090" value="<?php echo $row_rsForm2['ITStd_090']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_091" value="<?php echo $row_rsForm2['ITStd_091']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_092" value="<?php echo $row_rsForm2['ITStd_092']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_093" value="<?php echo $row_rsForm2['ITStd_093']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_094" value="<?php echo $row_rsForm2['ITStd_094']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_095" value="<?php echo $row_rsForm2['ITStd_095']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_096" value="<?php echo $row_rsForm2['ITStd_096']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_097" value="<?php echo $row_rsForm2['ITStd_097']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_098" value="<?php echo $row_rsForm2['ITStd_098']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_099" value="<?php echo $row_rsForm2['ITStd_099']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_100" value="<?php echo $row_rsForm2['ITStd_100']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_101" value="<?php echo $row_rsForm2['ITStd_101']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_102" value="<?php echo $row_rsForm2['ITStd_102']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_103" value="<?php echo $row_rsForm2['ITStd_103']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_104" value="<?php echo $row_rsForm2['ITStd_104']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_105" value="<?php echo $row_rsForm2['ITStd_105']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_106" value="<?php echo $row_rsForm2['ITStd_106']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_107" value="<?php echo $row_rsForm2['ITStd_107']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_108" value="<?php echo $row_rsForm2['ITStd_108']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_109" value="<?php echo $row_rsForm2['ITStd_109']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_110" value="<?php echo $row_rsForm2['ITStd_110']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_111" value="<?php echo $row_rsForm2['ITStd_111']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_112" value="<?php echo $row_rsForm2['ITStd_112']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_113" value="<?php echo $row_rsForm2['ITStd_113']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_114" value="<?php echo $row_rsForm2['ITStd_114']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_115" value="<?php echo $row_rsForm2['ITStd_115']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_116" value="<?php echo $row_rsForm2['ITStd_116']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_117" value="<?php echo $row_rsForm2['ITStd_117']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_118" value="<?php echo $row_rsForm2['ITStd_118']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_119" value="<?php echo $row_rsForm2['ITStd_119']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_120" value="<?php echo $row_rsForm2['ITStd_120']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_121" value="<?php echo $row_rsForm2['ITStd_121']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_122" value="<?php echo $row_rsForm2['ITStd_122']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_123" value="<?php echo $row_rsForm2['ITStd_123']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_124" value="<?php echo $row_rsForm2['ITStd_124']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_125" value="<?php echo $row_rsForm2['ITStd_125']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_126" value="<?php echo $row_rsForm2['ITStd_126']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_127" value="<?php echo $row_rsForm2['ITStd_127']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_128" value="<?php echo $row_rsForm2['ITStd_128']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_129" value="<?php echo $row_rsForm2['ITStd_129']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_130" value="<?php echo $row_rsForm2['ITStd_130']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_131" value="<?php echo $row_rsForm2['ITStd_131']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_132" value="<?php echo $row_rsForm2['ITStd_132']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_133" value="<?php echo $row_rsForm2['ITStd_133']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_134" value="<?php echo $row_rsForm2['ITStd_134']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_135" value="<?php echo $row_rsForm2['ITStd_135']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_136" value="<?php echo $row_rsForm2['ITStd_136']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_137" value="<?php echo $row_rsForm2['ITStd_137']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_138" value="<?php echo $row_rsForm2['ITStd_138']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_139" value="<?php echo $row_rsForm2['ITStd_139']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_140" value="<?php echo $row_rsForm2['ITStd_140']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_141" value="<?php echo $row_rsForm2['ITStd_141']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_142" value="<?php echo $row_rsForm2['ITStd_142']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_143" value="<?php echo $row_rsForm2['ITStd_143']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_144" value="<?php echo $row_rsForm2['ITStd_144']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_145" value="<?php echo $row_rsForm2['ITStd_145']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_146" value="<?php echo $row_rsForm2['ITStd_146']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_147" value="<?php echo $row_rsForm2['ITStd_147']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_148" value="<?php echo $row_rsForm2['ITStd_148']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_149" value="<?php echo $row_rsForm2['ITStd_149']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_150" value="<?php echo $row_rsForm2['ITStd_150']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_151" value="<?php echo $row_rsForm2['ITStd_151']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_152" value="<?php echo $row_rsForm2['ITStd_152']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_153" value="<?php echo $row_rsForm2['ITStd_153']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_154" value="<?php echo $row_rsForm2['ITStd_154']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_155" value="<?php echo $row_rsForm2['ITStd_155']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_156" value="<?php echo $row_rsForm2['ITStd_156']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_157" value="<?php echo $row_rsForm2['ITStd_157']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_158" value="<?php echo $row_rsForm2['ITStd_158']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_159" value="<?php echo $row_rsForm2['ITStd_159']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_160" value="<?php echo $row_rsForm2['ITStd_160']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_161" value="<?php echo $row_rsForm2['ITStd_161']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_162" value="<?php echo $row_rsForm2['ITStd_162']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_163" value="<?php echo $row_rsForm2['ITStd_163']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_164" value="<?php echo $row_rsForm2['ITStd_164']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_165" value="<?php echo $row_rsForm2['ITStd_165']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_166" value="<?php echo $row_rsForm2['ITStd_166']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_167" value="<?php echo $row_rsForm2['ITStd_167']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_168" value="<?php echo $row_rsForm2['ITStd_168']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_169" value="<?php echo $row_rsForm2['ITStd_169']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_170" value="<?php echo $row_rsForm2['ITStd_170']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_171" value="<?php echo $row_rsForm2['ITStd_171']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_172" value="<?php echo $row_rsForm2['ITStd_172']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_173" value="<?php echo $row_rsForm2['ITStd_173']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_174" value="<?php echo $row_rsForm2['ITStd_174']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_175" value="<?php echo $row_rsForm2['ITStd_175']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_176" value="<?php echo $row_rsForm2['ITStd_176']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_177" value="<?php echo $row_rsForm2['ITStd_177']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_178" value="<?php echo $row_rsForm2['ITStd_178']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_179" value="<?php echo $row_rsForm2['ITStd_179']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_180" value="<?php echo $row_rsForm2['ITStd_180']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_181" value="<?php echo $row_rsForm2['ITStd_181']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_182" value="<?php echo $row_rsForm2['ITStd_182']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_183" value="<?php echo $row_rsForm2['ITStd_183']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_184" value="<?php echo $row_rsForm2['ITStd_184']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_185" value="<?php echo $row_rsForm2['ITStd_185']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_186" value="<?php echo $row_rsForm2['ITStd_186']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_187" value="<?php echo $row_rsForm2['ITStd_187']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_188" value="<?php echo $row_rsForm2['ITStd_188']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_189" value="<?php echo $row_rsForm2['ITStd_189']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_190" value="<?php echo $row_rsForm2['ITStd_190']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_191" value="<?php echo $row_rsForm2['ITStd_191']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_192" value="<?php echo $row_rsForm2['ITStd_192']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_193" value="<?php echo $row_rsForm2['ITStd_193']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_194" value="<?php echo $row_rsForm2['ITStd_194']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_195" value="<?php echo $row_rsForm2['ITStd_195']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_196" value="<?php echo $row_rsForm2['ITStd_196']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_197" value="<?php echo $row_rsForm2['ITStd_197']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_198" value="<?php echo $row_rsForm2['ITStd_198']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_199" value="<?php echo $row_rsForm2['ITStd_199']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_200" value="<?php echo $row_rsForm2['ITStd_200']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_201" value="<?php echo $row_rsForm2['ITStd_201']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_202" value="<?php echo $row_rsForm2['ITStd_202']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_203" value="<?php echo $row_rsForm2['ITStd_203']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_204" value="<?php echo $row_rsForm2['ITStd_204']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_205" value="<?php echo $row_rsForm2['ITStd_205']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_206" value="<?php echo $row_rsForm2['ITStd_206']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_207" value="<?php echo $row_rsForm2['ITStd_207']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_208" value="<?php echo $row_rsForm2['ITStd_208']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_209" value="<?php echo $row_rsForm2['ITStd_209']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_210" value="<?php echo $row_rsForm2['ITStd_210']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_211" value="<?php echo $row_rsForm2['ITStd_211']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_212" value="<?php echo $row_rsForm2['ITStd_212']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_213" value="<?php echo $row_rsForm2['ITStd_213']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_214" value="<?php echo $row_rsForm2['ITStd_214']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_215" value="<?php echo $row_rsForm2['ITStd_215']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_216" value="<?php echo $row_rsForm2['ITStd_216']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_217" value="<?php echo $row_rsForm2['ITStd_217']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_218" value="<?php echo $row_rsForm2['ITStd_218']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_219" value="<?php echo $row_rsForm2['ITStd_219']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_220" value="<?php echo $row_rsForm2['ITStd_220']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_221" value="<?php echo $row_rsForm2['ITStd_221']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_222" value="<?php echo $row_rsForm2['ITStd_222']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_223" value="<?php echo $row_rsForm2['ITStd_223']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_224" value="<?php echo $row_rsForm2['ITStd_224']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_225" value="<?php echo $row_rsForm2['ITStd_225']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_226" value="<?php echo $row_rsForm2['ITStd_226']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_227" value="<?php echo $row_rsForm2['ITStd_227']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_228" value="<?php echo $row_rsForm2['ITStd_228']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_229" value="<?php echo $row_rsForm2['ITStd_229']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_230" value="<?php echo $row_rsForm2['ITStd_230']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_231" value="<?php echo $row_rsForm2['ITStd_231']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_232" value="<?php echo $row_rsForm2['ITStd_232']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_233" value="<?php echo $row_rsForm2['ITStd_233']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_234" value="<?php echo $row_rsForm2['ITStd_234']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_235" value="<?php echo $row_rsForm2['ITStd_235']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_236" value="<?php echo $row_rsForm2['ITStd_236']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_237" value="<?php echo $row_rsForm2['ITStd_237']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_238" value="<?php echo $row_rsForm2['ITStd_238']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_239" value="<?php echo $row_rsForm2['ITStd_239']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_240" value="<?php echo $row_rsForm2['ITStd_240']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_241" value="<?php echo $row_rsForm2['ITStd_241']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_242" value="<?php echo $row_rsForm2['ITStd_242']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_243" value="<?php echo $row_rsForm2['ITStd_243']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_244" value="<?php echo $row_rsForm2['ITStd_244']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_245" value="<?php echo $row_rsForm2['ITStd_245']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_246" value="<?php echo $row_rsForm2['ITStd_246']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_247" value="<?php echo $row_rsForm2['ITStd_247']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_248" value="<?php echo $row_rsForm2['ITStd_248']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_249" value="<?php echo $row_rsForm2['ITStd_249']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_250" value="<?php echo $row_rsForm2['ITStd_250']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_251" value="<?php echo $row_rsForm2['ITStd_251']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_252" value="<?php echo $row_rsForm2['ITStd_252']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_253" value="<?php echo $row_rsForm2['ITStd_253']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_254" value="<?php echo $row_rsForm2['ITStd_254']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_255" value="<?php echo $row_rsForm2['ITStd_255']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_256" value="<?php echo $row_rsForm2['ITStd_256']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_257" value="<?php echo $row_rsForm2['ITStd_257']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_258" value="<?php echo $row_rsForm2['ITStd_258']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_259" value="<?php echo $row_rsForm2['ITStd_259']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_260" value="<?php echo $row_rsForm2['ITStd_260']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_261" value="<?php echo $row_rsForm2['ITStd_261']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_262" value="<?php echo $row_rsForm2['ITStd_262']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_263" value="<?php echo $row_rsForm2['ITStd_263']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_264" value="<?php echo $row_rsForm2['ITStd_264']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_265" value="<?php echo $row_rsForm2['ITStd_265']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_266" value="<?php echo $row_rsForm2['ITStd_266']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_267" value="<?php echo $row_rsForm2['ITStd_267']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_268" value="<?php echo $row_rsForm2['ITStd_268']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_269" value="<?php echo $row_rsForm2['ITStd_269']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_270" value="<?php echo $row_rsForm2['ITStd_270']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_271" value="<?php echo $row_rsForm2['ITStd_271']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_272" value="<?php echo $row_rsForm2['ITStd_272']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_273" value="<?php echo $row_rsForm2['ITStd_273']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_274" value="<?php echo $row_rsForm2['ITStd_274']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_275" value="<?php echo $row_rsForm2['ITStd_275']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_276" value="<?php echo $row_rsForm2['ITStd_276']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_277" value="<?php echo $row_rsForm2['ITStd_277']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_278" value="<?php echo $row_rsForm2['ITStd_278']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_279" value="<?php echo $row_rsForm2['ITStd_279']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_280" value="<?php echo $row_rsForm2['ITStd_280']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_281" value="<?php echo $row_rsForm2['ITStd_281']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_282" value="<?php echo $row_rsForm2['ITStd_282']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_283" value="<?php echo $row_rsForm2['ITStd_283']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_284" value="<?php echo $row_rsForm2['ITStd_284']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_285" value="<?php echo $row_rsForm2['ITStd_285']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_286" value="<?php echo $row_rsForm2['ITStd_286']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_287" value="<?php echo $row_rsForm2['ITStd_287']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_288" value="<?php echo $row_rsForm2['ITStd_288']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_289" value="<?php echo $row_rsForm2['ITStd_289']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_290" value="<?php echo $row_rsForm2['ITStd_290']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_291" value="<?php echo $row_rsForm2['ITStd_291']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_292" value="<?php echo $row_rsForm2['ITStd_292']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_293" value="<?php echo $row_rsForm2['ITStd_293']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_294" value="<?php echo $row_rsForm2['ITStd_294']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_295" value="<?php echo $row_rsForm2['ITStd_295']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_296" value="<?php echo $row_rsForm2['ITStd_296']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_297" value="<?php echo $row_rsForm2['ITStd_297']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_298" value="<?php echo $row_rsForm2['ITStd_298']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_299" value="<?php echo $row_rsForm2['ITStd_299']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_300" value="<?php echo $row_rsForm2['ITStd_300']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_301" value="<?php echo $row_rsForm2['ITStd_301']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_302" value="<?php echo $row_rsForm2['ITStd_302']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_303" value="<?php echo $row_rsForm2['ITStd_303']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_304" value="<?php echo $row_rsForm2['ITStd_304']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_305" value="<?php echo $row_rsForm2['ITStd_305']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_306" value="<?php echo $row_rsForm2['ITStd_306']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_307" value="<?php echo $row_rsForm2['ITStd_307']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_308" value="<?php echo $row_rsForm2['ITStd_308']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_309" value="<?php echo $row_rsForm2['ITStd_309']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_310" value="<?php echo $row_rsForm2['ITStd_310']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_311" value="<?php echo $row_rsForm2['ITStd_311']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_312" value="<?php echo $row_rsForm2['ITStd_312']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_313" value="<?php echo $row_rsForm2['ITStd_313']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_314" value="<?php echo $row_rsForm2['ITStd_314']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_315" value="<?php echo $row_rsForm2['ITStd_315']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_316" value="<?php echo $row_rsForm2['ITStd_316']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_317" value="<?php echo $row_rsForm2['ITStd_317']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_318" value="<?php echo $row_rsForm2['ITStd_318']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_319" value="<?php echo $row_rsForm2['ITStd_319']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_320" value="<?php echo $row_rsForm2['ITStd_320']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_321" value="<?php echo $row_rsForm2['ITStd_321']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_322" value="<?php echo $row_rsForm2['ITStd_322']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_323" value="<?php echo $row_rsForm2['ITStd_323']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_324" value="<?php echo $row_rsForm2['ITStd_324']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_325" value="<?php echo $row_rsForm2['ITStd_325']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_326" value="<?php echo $row_rsForm2['ITStd_326']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_327" value="<?php echo $row_rsForm2['ITStd_327']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_328" value="<?php echo $row_rsForm2['ITStd_328']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_329" value="<?php echo $row_rsForm2['ITStd_329']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_330" value="<?php echo $row_rsForm2['ITStd_330']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_331" value="<?php echo $row_rsForm2['ITStd_331']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_332" value="<?php echo $row_rsForm2['ITStd_332']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_333" value="<?php echo $row_rsForm2['ITStd_333']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_334" value="<?php echo $row_rsForm2['ITStd_334']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_335" value="<?php echo $row_rsForm2['ITStd_335']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_336" value="<?php echo $row_rsForm2['ITStd_336']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_337" value="<?php echo $row_rsForm2['ITStd_337']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_338" value="<?php echo $row_rsForm2['ITStd_338']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_339" value="<?php echo $row_rsForm2['ITStd_339']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_340" value="<?php echo $row_rsForm2['ITStd_340']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_341" value="<?php echo $row_rsForm2['ITStd_341']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_342" value="<?php echo $row_rsForm2['ITStd_342']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_343" value="<?php echo $row_rsForm2['ITStd_343']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_344" value="<?php echo $row_rsForm2['ITStd_344']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_345" value="<?php echo $row_rsForm2['ITStd_345']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_346" value="<?php echo $row_rsForm2['ITStd_346']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_347" value="<?php echo $row_rsForm2['ITStd_347']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_348" value="<?php echo $row_rsForm2['ITStd_348']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_349" value="<?php echo $row_rsForm2['ITStd_349']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_350" value="<?php echo $row_rsForm2['ITStd_350']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_351" value="<?php echo $row_rsForm2['ITStd_351']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_352" value="<?php echo $row_rsForm2['ITStd_352']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_353" value="<?php echo $row_rsForm2['ITStd_353']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_354" value="<?php echo $row_rsForm2['ITStd_354']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_355" value="<?php echo $row_rsForm2['ITStd_355']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_356" value="<?php echo $row_rsForm2['ITStd_356']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_357" value="<?php echo $row_rsForm2['ITStd_357']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_358" value="<?php echo $row_rsForm2['ITStd_358']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_359" value="<?php echo $row_rsForm2['ITStd_359']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_360" value="<?php echo $row_rsForm2['ITStd_360']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_361" value="<?php echo $row_rsForm2['ITStd_361']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_362" value="<?php echo $row_rsForm2['ITStd_362']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_363" value="<?php echo $row_rsForm2['ITStd_363']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_364" value="<?php echo $row_rsForm2['ITStd_364']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_365" value="<?php echo $row_rsForm2['ITStd_365']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_366" value="<?php echo $row_rsForm2['ITStd_366']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_367" value="<?php echo $row_rsForm2['ITStd_367']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_368" value="<?php echo $row_rsForm2['ITStd_368']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_369" value="<?php echo $row_rsForm2['ITStd_369']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_370" value="<?php echo $row_rsForm2['ITStd_370']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_371" value="<?php echo $row_rsForm2['ITStd_371']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_372" value="<?php echo $row_rsForm2['ITStd_372']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_373" value="<?php echo $row_rsForm2['ITStd_373']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_374" value="<?php echo $row_rsForm2['ITStd_374']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_375" value="<?php echo $row_rsForm2['ITStd_375']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_376" value="<?php echo $row_rsForm2['ITStd_376']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_377" value="<?php echo $row_rsForm2['ITStd_377']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_378" value="<?php echo $row_rsForm2['ITStd_378']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_379" value="<?php echo $row_rsForm2['ITStd_379']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_380" value="<?php echo $row_rsForm2['ITStd_380']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_381" value="<?php echo $row_rsForm2['ITStd_381']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_382" value="<?php echo $row_rsForm2['ITStd_382']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_383" value="<?php echo $row_rsForm2['ITStd_383']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_384" value="<?php echo $row_rsForm2['ITStd_384']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_385" value="<?php echo $row_rsForm2['ITStd_385']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_386" value="<?php echo $row_rsForm2['ITStd_386']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_387" value="<?php echo $row_rsForm2['ITStd_387']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_388" value="<?php echo $row_rsForm2['ITStd_388']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_389" value="<?php echo $row_rsForm2['ITStd_389']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_390" value="<?php echo $row_rsForm2['ITStd_390']; ?>" size="20" maxlength="100" /> </li>
  <?php }//end of if ?>                    
								</ol>
								<br /><br />
								<label class="lblSubQuestion">Program #2: </label><input type="text" name="IT_EMPro02" value="<?php echo $row_rsForm['IT_EMPro02']; ?>" size="20" maxlength="100" />
								<br /><br />
								<ol class="olForms">
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP01" value="<?php echo $row_rsForm['IT_2EMP01']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER01" value="<?php echo $row_rsForm['IT_2USER01']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS01" value="<?php echo $row_rsForm['IT_2PASS01']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP02" value="<?php echo $row_rsForm['IT_2EMP02']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER02" value="<?php echo $row_rsForm['IT_2USER02']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS02" value="<?php echo $row_rsForm['IT_2PASS02']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP03" value="<?php echo $row_rsForm['IT_2EMP03']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER03" value="<?php echo $row_rsForm['IT_2USER03']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS03" value="<?php echo $row_rsForm['IT_2PASS03']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP04" value="<?php echo $row_rsForm['IT_2EMP04']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER04" value="<?php echo $row_rsForm['IT_2USER04']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS04" value="<?php echo $row_rsForm['IT_2PASS04']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP05" value="<?php echo $row_rsForm['IT_2EMP05']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER05" value="<?php echo $row_rsForm['IT_2USER05']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS05" value="<?php echo $row_rsForm['IT_2PASS05']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP06" value="<?php echo $row_rsForm['IT_2EMP06']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER06" value="<?php echo $row_rsForm['IT_2USER06']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS06" value="<?php echo $row_rsForm['IT_2PASS06']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP07" value="<?php echo $row_rsForm['IT_2EMP07']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER07" value="<?php echo $row_rsForm['IT_2USER07']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS07" value="<?php echo $row_rsForm['IT_2PASS07']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP08" value="<?php echo $row_rsForm['IT_2EMP08']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER08" value="<?php echo $row_rsForm['IT_2USER08']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS08" value="<?php echo $row_rsForm['IT_2PASS08']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP09" value="<?php echo $row_rsForm['IT_2EMP09']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER09" value="<?php echo $row_rsForm['IT_2USER09']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS09" value="<?php echo $row_rsForm['IT_2PASS09']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP10" value="<?php echo $row_rsForm['IT_2EMP10']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER10" value="<?php echo $row_rsForm['IT_2USER10']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS10" value="<?php echo $row_rsForm['IT_2PASS10']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP11" value="<?php echo $row_rsForm['IT_2EMP11']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER11" value="<?php echo $row_rsForm['IT_2USER11']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS11" value="<?php echo $row_rsForm['IT_2PASS11']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP12" value="<?php echo $row_rsForm['IT_2EMP12']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER12" value="<?php echo $row_rsForm['IT_2USER12']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS12" value="<?php echo $row_rsForm['IT_2PASS12']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP13" value="<?php echo $row_rsForm['IT_2EMP13']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER13" value="<?php echo $row_rsForm['IT_2USER13']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS13" value="<?php echo $row_rsForm['IT_2PASS13']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP14" value="<?php echo $row_rsForm['IT_2EMP14']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER14" value="<?php echo $row_rsForm['IT_2USER14']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS14" value="<?php echo $row_rsForm['IT_2PASS14']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP15" value="<?php echo $row_rsForm['IT_2EMP15']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER15" value="<?php echo $row_rsForm['IT_2USER15']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS15" value="<?php echo $row_rsForm['IT_2PASS15']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP16" value="<?php echo $row_rsForm['IT_2EMP16']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER16" value="<?php echo $row_rsForm['IT_2USER16']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS16" value="<?php echo $row_rsForm['IT_2PASS16']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP17" value="<?php echo $row_rsForm['IT_2EMP17']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER17" value="<?php echo $row_rsForm['IT_2USER17']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS17" value="<?php echo $row_rsForm['IT_2PASS17']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP18" value="<?php echo $row_rsForm['IT_2EMP18']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER18" value="<?php echo $row_rsForm['IT_2USER18']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS18" value="<?php echo $row_rsForm['IT_2PASS18']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP19" value="<?php echo $row_rsForm['IT_2EMP19']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER19" value="<?php echo $row_rsForm['IT_2USER19']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS19" value="<?php echo $row_rsForm['IT_2PASS19']; ?>" size="20" maxlength="100" /></li>
									<li><label>Employee Name: </label><input type="text" name="IT_2EMP20" value="<?php echo $row_rsForm['IT_2EMP20']; ?>" size="20" maxlength="100" /> <label>User Name: </label><input type="text" name="IT_2USER20" value="<?php echo $row_rsForm['IT_2USER20']; ?>" size="15" maxlength="100" /> <label>Password: </label><input type="text" name="IT_2PASS20" value="<?php echo $row_rsForm['IT_2PASS20']; ?>" size="20" maxlength="100" /></li>
	<?php // this will display only for Standard
		if ($row_loginFoundUser['Solution'] == 2)
		{ ?>       
        		<li><label>Employee Name: </label><input type="text" name="ITStd_391" value="<?php echo $row_rsForm2['ITStd_391']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_392" value="<?php echo $row_rsForm2['ITStd_392']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_393" value="<?php echo $row_rsForm2['ITStd_393']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_394" value="<?php echo $row_rsForm2['ITStd_394']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_395" value="<?php echo $row_rsForm2['ITStd_395']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_396" value="<?php echo $row_rsForm2['ITStd_396']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_397" value="<?php echo $row_rsForm2['ITStd_397']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_398" value="<?php echo $row_rsForm2['ITStd_398']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_399" value="<?php echo $row_rsForm2['ITStd_399']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_400" value="<?php echo $row_rsForm2['ITStd_400']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_401" value="<?php echo $row_rsForm2['ITStd_401']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_402" value="<?php echo $row_rsForm2['ITStd_402']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_403" value="<?php echo $row_rsForm2['ITStd_403']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_404" value="<?php echo $row_rsForm2['ITStd_404']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_405" value="<?php echo $row_rsForm2['ITStd_405']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_406" value="<?php echo $row_rsForm2['ITStd_406']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_407" value="<?php echo $row_rsForm2['ITStd_407']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_408" value="<?php echo $row_rsForm2['ITStd_408']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_409" value="<?php echo $row_rsForm2['ITStd_409']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_410" value="<?php echo $row_rsForm2['ITStd_410']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_411" value="<?php echo $row_rsForm2['ITStd_411']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_412" value="<?php echo $row_rsForm2['ITStd_412']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_413" value="<?php echo $row_rsForm2['ITStd_413']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_414" value="<?php echo $row_rsForm2['ITStd_414']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_415" value="<?php echo $row_rsForm2['ITStd_415']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_416" value="<?php echo $row_rsForm2['ITStd_416']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_417" value="<?php echo $row_rsForm2['ITStd_417']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_418" value="<?php echo $row_rsForm2['ITStd_418']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_419" value="<?php echo $row_rsForm2['ITStd_419']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_420" value="<?php echo $row_rsForm2['ITStd_420']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_421" value="<?php echo $row_rsForm2['ITStd_421']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_422" value="<?php echo $row_rsForm2['ITStd_422']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_423" value="<?php echo $row_rsForm2['ITStd_423']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_424" value="<?php echo $row_rsForm2['ITStd_424']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_425" value="<?php echo $row_rsForm2['ITStd_425']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_426" value="<?php echo $row_rsForm2['ITStd_426']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_427" value="<?php echo $row_rsForm2['ITStd_427']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_428" value="<?php echo $row_rsForm2['ITStd_428']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_429" value="<?php echo $row_rsForm2['ITStd_429']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_430" value="<?php echo $row_rsForm2['ITStd_430']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_431" value="<?php echo $row_rsForm2['ITStd_431']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_432" value="<?php echo $row_rsForm2['ITStd_432']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_433" value="<?php echo $row_rsForm2['ITStd_433']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_434" value="<?php echo $row_rsForm2['ITStd_434']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_435" value="<?php echo $row_rsForm2['ITStd_435']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_436" value="<?php echo $row_rsForm2['ITStd_436']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_437" value="<?php echo $row_rsForm2['ITStd_437']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_438" value="<?php echo $row_rsForm2['ITStd_438']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_439" value="<?php echo $row_rsForm2['ITStd_439']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_440" value="<?php echo $row_rsForm2['ITStd_440']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_441" value="<?php echo $row_rsForm2['ITStd_441']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_442" value="<?php echo $row_rsForm2['ITStd_442']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_443" value="<?php echo $row_rsForm2['ITStd_443']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_444" value="<?php echo $row_rsForm2['ITStd_444']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_445" value="<?php echo $row_rsForm2['ITStd_445']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_446" value="<?php echo $row_rsForm2['ITStd_446']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_447" value="<?php echo $row_rsForm2['ITStd_447']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_448" value="<?php echo $row_rsForm2['ITStd_448']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_449" value="<?php echo $row_rsForm2['ITStd_449']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_450" value="<?php echo $row_rsForm2['ITStd_450']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_451" value="<?php echo $row_rsForm2['ITStd_451']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_452" value="<?php echo $row_rsForm2['ITStd_452']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_453" value="<?php echo $row_rsForm2['ITStd_453']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_454" value="<?php echo $row_rsForm2['ITStd_454']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_455" value="<?php echo $row_rsForm2['ITStd_455']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_456" value="<?php echo $row_rsForm2['ITStd_456']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_457" value="<?php echo $row_rsForm2['ITStd_457']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_458" value="<?php echo $row_rsForm2['ITStd_458']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_459" value="<?php echo $row_rsForm2['ITStd_459']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_460" value="<?php echo $row_rsForm2['ITStd_460']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_461" value="<?php echo $row_rsForm2['ITStd_461']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_462" value="<?php echo $row_rsForm2['ITStd_462']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_463" value="<?php echo $row_rsForm2['ITStd_463']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_464" value="<?php echo $row_rsForm2['ITStd_464']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_465" value="<?php echo $row_rsForm2['ITStd_465']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_466" value="<?php echo $row_rsForm2['ITStd_466']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_467" value="<?php echo $row_rsForm2['ITStd_467']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_468" value="<?php echo $row_rsForm2['ITStd_468']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_469" value="<?php echo $row_rsForm2['ITStd_469']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_470" value="<?php echo $row_rsForm2['ITStd_470']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_471" value="<?php echo $row_rsForm2['ITStd_471']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_472" value="<?php echo $row_rsForm2['ITStd_472']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_473" value="<?php echo $row_rsForm2['ITStd_473']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_474" value="<?php echo $row_rsForm2['ITStd_474']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_475" value="<?php echo $row_rsForm2['ITStd_475']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_476" value="<?php echo $row_rsForm2['ITStd_476']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_477" value="<?php echo $row_rsForm2['ITStd_477']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_478" value="<?php echo $row_rsForm2['ITStd_478']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_479" value="<?php echo $row_rsForm2['ITStd_479']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_480" value="<?php echo $row_rsForm2['ITStd_480']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_481" value="<?php echo $row_rsForm2['ITStd_481']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_482" value="<?php echo $row_rsForm2['ITStd_482']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_483" value="<?php echo $row_rsForm2['ITStd_483']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_484" value="<?php echo $row_rsForm2['ITStd_484']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_485" value="<?php echo $row_rsForm2['ITStd_485']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_486" value="<?php echo $row_rsForm2['ITStd_486']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_487" value="<?php echo $row_rsForm2['ITStd_487']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_488" value="<?php echo $row_rsForm2['ITStd_488']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_489" value="<?php echo $row_rsForm2['ITStd_489']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_490" value="<?php echo $row_rsForm2['ITStd_490']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_491" value="<?php echo $row_rsForm2['ITStd_491']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_492" value="<?php echo $row_rsForm2['ITStd_492']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_493" value="<?php echo $row_rsForm2['ITStd_493']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_494" value="<?php echo $row_rsForm2['ITStd_494']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_495" value="<?php echo $row_rsForm2['ITStd_495']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_496" value="<?php echo $row_rsForm2['ITStd_496']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_497" value="<?php echo $row_rsForm2['ITStd_497']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_498" value="<?php echo $row_rsForm2['ITStd_498']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_499" value="<?php echo $row_rsForm2['ITStd_499']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_500" value="<?php echo $row_rsForm2['ITStd_500']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_501" value="<?php echo $row_rsForm2['ITStd_501']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_502" value="<?php echo $row_rsForm2['ITStd_502']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_503" value="<?php echo $row_rsForm2['ITStd_503']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_504" value="<?php echo $row_rsForm2['ITStd_504']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_505" value="<?php echo $row_rsForm2['ITStd_505']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_506" value="<?php echo $row_rsForm2['ITStd_506']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_507" value="<?php echo $row_rsForm2['ITStd_507']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_508" value="<?php echo $row_rsForm2['ITStd_508']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_509" value="<?php echo $row_rsForm2['ITStd_509']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_510" value="<?php echo $row_rsForm2['ITStd_510']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_511" value="<?php echo $row_rsForm2['ITStd_511']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_512" value="<?php echo $row_rsForm2['ITStd_512']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_513" value="<?php echo $row_rsForm2['ITStd_513']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_514" value="<?php echo $row_rsForm2['ITStd_514']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_515" value="<?php echo $row_rsForm2['ITStd_515']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_516" value="<?php echo $row_rsForm2['ITStd_516']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_517" value="<?php echo $row_rsForm2['ITStd_517']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_518" value="<?php echo $row_rsForm2['ITStd_518']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_519" value="<?php echo $row_rsForm2['ITStd_519']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_520" value="<?php echo $row_rsForm2['ITStd_520']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_521" value="<?php echo $row_rsForm2['ITStd_521']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_522" value="<?php echo $row_rsForm2['ITStd_522']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_523" value="<?php echo $row_rsForm2['ITStd_523']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_524" value="<?php echo $row_rsForm2['ITStd_524']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_525" value="<?php echo $row_rsForm2['ITStd_525']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_526" value="<?php echo $row_rsForm2['ITStd_526']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_527" value="<?php echo $row_rsForm2['ITStd_527']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_528" value="<?php echo $row_rsForm2['ITStd_528']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_529" value="<?php echo $row_rsForm2['ITStd_529']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_530" value="<?php echo $row_rsForm2['ITStd_530']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_531" value="<?php echo $row_rsForm2['ITStd_531']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_532" value="<?php echo $row_rsForm2['ITStd_532']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_533" value="<?php echo $row_rsForm2['ITStd_533']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_534" value="<?php echo $row_rsForm2['ITStd_534']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_535" value="<?php echo $row_rsForm2['ITStd_535']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_536" value="<?php echo $row_rsForm2['ITStd_536']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_537" value="<?php echo $row_rsForm2['ITStd_537']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_538" value="<?php echo $row_rsForm2['ITStd_538']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_539" value="<?php echo $row_rsForm2['ITStd_539']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_540" value="<?php echo $row_rsForm2['ITStd_540']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_541" value="<?php echo $row_rsForm2['ITStd_541']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_542" value="<?php echo $row_rsForm2['ITStd_542']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_543" value="<?php echo $row_rsForm2['ITStd_543']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_544" value="<?php echo $row_rsForm2['ITStd_544']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_545" value="<?php echo $row_rsForm2['ITStd_545']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_546" value="<?php echo $row_rsForm2['ITStd_546']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_547" value="<?php echo $row_rsForm2['ITStd_547']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_548" value="<?php echo $row_rsForm2['ITStd_548']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_549" value="<?php echo $row_rsForm2['ITStd_549']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_550" value="<?php echo $row_rsForm2['ITStd_550']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_551" value="<?php echo $row_rsForm2['ITStd_551']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_552" value="<?php echo $row_rsForm2['ITStd_552']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_553" value="<?php echo $row_rsForm2['ITStd_553']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_554" value="<?php echo $row_rsForm2['ITStd_554']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_555" value="<?php echo $row_rsForm2['ITStd_555']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_556" value="<?php echo $row_rsForm2['ITStd_556']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_557" value="<?php echo $row_rsForm2['ITStd_557']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_558" value="<?php echo $row_rsForm2['ITStd_558']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_559" value="<?php echo $row_rsForm2['ITStd_559']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_560" value="<?php echo $row_rsForm2['ITStd_560']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_561" value="<?php echo $row_rsForm2['ITStd_561']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_562" value="<?php echo $row_rsForm2['ITStd_562']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_563" value="<?php echo $row_rsForm2['ITStd_563']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_564" value="<?php echo $row_rsForm2['ITStd_564']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_565" value="<?php echo $row_rsForm2['ITStd_565']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_566" value="<?php echo $row_rsForm2['ITStd_566']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_567" value="<?php echo $row_rsForm2['ITStd_567']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_568" value="<?php echo $row_rsForm2['ITStd_568']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_569" value="<?php echo $row_rsForm2['ITStd_569']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_570" value="<?php echo $row_rsForm2['ITStd_570']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_571" value="<?php echo $row_rsForm2['ITStd_571']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_572" value="<?php echo $row_rsForm2['ITStd_572']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_573" value="<?php echo $row_rsForm2['ITStd_573']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_574" value="<?php echo $row_rsForm2['ITStd_574']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_575" value="<?php echo $row_rsForm2['ITStd_575']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_576" value="<?php echo $row_rsForm2['ITStd_576']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_577" value="<?php echo $row_rsForm2['ITStd_577']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_578" value="<?php echo $row_rsForm2['ITStd_578']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_579" value="<?php echo $row_rsForm2['ITStd_579']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_580" value="<?php echo $row_rsForm2['ITStd_580']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_581" value="<?php echo $row_rsForm2['ITStd_581']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_582" value="<?php echo $row_rsForm2['ITStd_582']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_583" value="<?php echo $row_rsForm2['ITStd_583']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_584" value="<?php echo $row_rsForm2['ITStd_584']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_585" value="<?php echo $row_rsForm2['ITStd_585']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_586" value="<?php echo $row_rsForm2['ITStd_586']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_587" value="<?php echo $row_rsForm2['ITStd_587']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_588" value="<?php echo $row_rsForm2['ITStd_588']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_589" value="<?php echo $row_rsForm2['ITStd_589']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_590" value="<?php echo $row_rsForm2['ITStd_590']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_591" value="<?php echo $row_rsForm2['ITStd_591']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_592" value="<?php echo $row_rsForm2['ITStd_592']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_593" value="<?php echo $row_rsForm2['ITStd_593']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_594" value="<?php echo $row_rsForm2['ITStd_594']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_595" value="<?php echo $row_rsForm2['ITStd_595']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_596" value="<?php echo $row_rsForm2['ITStd_596']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_597" value="<?php echo $row_rsForm2['ITStd_597']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_598" value="<?php echo $row_rsForm2['ITStd_598']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_599" value="<?php echo $row_rsForm2['ITStd_599']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_600" value="<?php echo $row_rsForm2['ITStd_600']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_601" value="<?php echo $row_rsForm2['ITStd_601']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_602" value="<?php echo $row_rsForm2['ITStd_602']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_603" value="<?php echo $row_rsForm2['ITStd_603']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_604" value="<?php echo $row_rsForm2['ITStd_604']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_605" value="<?php echo $row_rsForm2['ITStd_605']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_606" value="<?php echo $row_rsForm2['ITStd_606']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_607" value="<?php echo $row_rsForm2['ITStd_607']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_608" value="<?php echo $row_rsForm2['ITStd_608']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_609" value="<?php echo $row_rsForm2['ITStd_609']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_610" value="<?php echo $row_rsForm2['ITStd_610']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_611" value="<?php echo $row_rsForm2['ITStd_611']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_612" value="<?php echo $row_rsForm2['ITStd_612']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_613" value="<?php echo $row_rsForm2['ITStd_613']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_614" value="<?php echo $row_rsForm2['ITStd_614']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_615" value="<?php echo $row_rsForm2['ITStd_615']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_616" value="<?php echo $row_rsForm2['ITStd_616']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_617" value="<?php echo $row_rsForm2['ITStd_617']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_618" value="<?php echo $row_rsForm2['ITStd_618']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_619" value="<?php echo $row_rsForm2['ITStd_619']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_620" value="<?php echo $row_rsForm2['ITStd_620']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_621" value="<?php echo $row_rsForm2['ITStd_621']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_622" value="<?php echo $row_rsForm2['ITStd_622']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_623" value="<?php echo $row_rsForm2['ITStd_623']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_624" value="<?php echo $row_rsForm2['ITStd_624']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_625" value="<?php echo $row_rsForm2['ITStd_625']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_626" value="<?php echo $row_rsForm2['ITStd_626']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_627" value="<?php echo $row_rsForm2['ITStd_627']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_628" value="<?php echo $row_rsForm2['ITStd_628']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_629" value="<?php echo $row_rsForm2['ITStd_629']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_630" value="<?php echo $row_rsForm2['ITStd_630']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_631" value="<?php echo $row_rsForm2['ITStd_631']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_632" value="<?php echo $row_rsForm2['ITStd_632']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_633" value="<?php echo $row_rsForm2['ITStd_633']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_634" value="<?php echo $row_rsForm2['ITStd_634']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_635" value="<?php echo $row_rsForm2['ITStd_635']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_636" value="<?php echo $row_rsForm2['ITStd_636']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_637" value="<?php echo $row_rsForm2['ITStd_637']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_638" value="<?php echo $row_rsForm2['ITStd_638']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_639" value="<?php echo $row_rsForm2['ITStd_639']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_640" value="<?php echo $row_rsForm2['ITStd_640']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_641" value="<?php echo $row_rsForm2['ITStd_641']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_642" value="<?php echo $row_rsForm2['ITStd_642']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_643" value="<?php echo $row_rsForm2['ITStd_643']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_644" value="<?php echo $row_rsForm2['ITStd_644']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_645" value="<?php echo $row_rsForm2['ITStd_645']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_646" value="<?php echo $row_rsForm2['ITStd_646']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_647" value="<?php echo $row_rsForm2['ITStd_647']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_648" value="<?php echo $row_rsForm2['ITStd_648']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_649" value="<?php echo $row_rsForm2['ITStd_649']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_650" value="<?php echo $row_rsForm2['ITStd_650']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_651" value="<?php echo $row_rsForm2['ITStd_651']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_652" value="<?php echo $row_rsForm2['ITStd_652']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_653" value="<?php echo $row_rsForm2['ITStd_653']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_654" value="<?php echo $row_rsForm2['ITStd_654']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_655" value="<?php echo $row_rsForm2['ITStd_655']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_656" value="<?php echo $row_rsForm2['ITStd_656']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_657" value="<?php echo $row_rsForm2['ITStd_657']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_658" value="<?php echo $row_rsForm2['ITStd_658']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_659" value="<?php echo $row_rsForm2['ITStd_659']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_660" value="<?php echo $row_rsForm2['ITStd_660']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_661" value="<?php echo $row_rsForm2['ITStd_661']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_662" value="<?php echo $row_rsForm2['ITStd_662']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_663" value="<?php echo $row_rsForm2['ITStd_663']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_664" value="<?php echo $row_rsForm2['ITStd_664']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_665" value="<?php echo $row_rsForm2['ITStd_665']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_666" value="<?php echo $row_rsForm2['ITStd_666']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_667" value="<?php echo $row_rsForm2['ITStd_667']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_668" value="<?php echo $row_rsForm2['ITStd_668']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_669" value="<?php echo $row_rsForm2['ITStd_669']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_670" value="<?php echo $row_rsForm2['ITStd_670']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_671" value="<?php echo $row_rsForm2['ITStd_671']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_672" value="<?php echo $row_rsForm2['ITStd_672']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_673" value="<?php echo $row_rsForm2['ITStd_673']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_674" value="<?php echo $row_rsForm2['ITStd_674']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_675" value="<?php echo $row_rsForm2['ITStd_675']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_676" value="<?php echo $row_rsForm2['ITStd_676']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_677" value="<?php echo $row_rsForm2['ITStd_677']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_678" value="<?php echo $row_rsForm2['ITStd_678']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_679" value="<?php echo $row_rsForm2['ITStd_679']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_680" value="<?php echo $row_rsForm2['ITStd_680']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_681" value="<?php echo $row_rsForm2['ITStd_681']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_682" value="<?php echo $row_rsForm2['ITStd_682']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_683" value="<?php echo $row_rsForm2['ITStd_683']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_684" value="<?php echo $row_rsForm2['ITStd_684']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_685" value="<?php echo $row_rsForm2['ITStd_685']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_686" value="<?php echo $row_rsForm2['ITStd_686']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_687" value="<?php echo $row_rsForm2['ITStd_687']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_688" value="<?php echo $row_rsForm2['ITStd_688']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_689" value="<?php echo $row_rsForm2['ITStd_689']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_690" value="<?php echo $row_rsForm2['ITStd_690']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_691" value="<?php echo $row_rsForm2['ITStd_691']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_692" value="<?php echo $row_rsForm2['ITStd_692']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_693" value="<?php echo $row_rsForm2['ITStd_693']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_694" value="<?php echo $row_rsForm2['ITStd_694']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_695" value="<?php echo $row_rsForm2['ITStd_695']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_696" value="<?php echo $row_rsForm2['ITStd_696']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_697" value="<?php echo $row_rsForm2['ITStd_697']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_698" value="<?php echo $row_rsForm2['ITStd_698']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_699" value="<?php echo $row_rsForm2['ITStd_699']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_700" value="<?php echo $row_rsForm2['ITStd_700']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_701" value="<?php echo $row_rsForm2['ITStd_701']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_702" value="<?php echo $row_rsForm2['ITStd_702']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_703" value="<?php echo $row_rsForm2['ITStd_703']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_704" value="<?php echo $row_rsForm2['ITStd_704']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_705" value="<?php echo $row_rsForm2['ITStd_705']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_706" value="<?php echo $row_rsForm2['ITStd_706']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_707" value="<?php echo $row_rsForm2['ITStd_707']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_708" value="<?php echo $row_rsForm2['ITStd_708']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_709" value="<?php echo $row_rsForm2['ITStd_709']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_710" value="<?php echo $row_rsForm2['ITStd_710']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_711" value="<?php echo $row_rsForm2['ITStd_711']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_712" value="<?php echo $row_rsForm2['ITStd_712']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_713" value="<?php echo $row_rsForm2['ITStd_713']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_714" value="<?php echo $row_rsForm2['ITStd_714']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_715" value="<?php echo $row_rsForm2['ITStd_715']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_716" value="<?php echo $row_rsForm2['ITStd_716']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_717" value="<?php echo $row_rsForm2['ITStd_717']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_718" value="<?php echo $row_rsForm2['ITStd_718']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_719" value="<?php echo $row_rsForm2['ITStd_719']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_720" value="<?php echo $row_rsForm2['ITStd_720']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_721" value="<?php echo $row_rsForm2['ITStd_721']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_722" value="<?php echo $row_rsForm2['ITStd_722']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_723" value="<?php echo $row_rsForm2['ITStd_723']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_724" value="<?php echo $row_rsForm2['ITStd_724']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_725" value="<?php echo $row_rsForm2['ITStd_725']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_726" value="<?php echo $row_rsForm2['ITStd_726']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_727" value="<?php echo $row_rsForm2['ITStd_727']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_728" value="<?php echo $row_rsForm2['ITStd_728']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_729" value="<?php echo $row_rsForm2['ITStd_729']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_730" value="<?php echo $row_rsForm2['ITStd_730']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_731" value="<?php echo $row_rsForm2['ITStd_731']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_732" value="<?php echo $row_rsForm2['ITStd_732']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_733" value="<?php echo $row_rsForm2['ITStd_733']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_734" value="<?php echo $row_rsForm2['ITStd_734']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_735" value="<?php echo $row_rsForm2['ITStd_735']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_736" value="<?php echo $row_rsForm2['ITStd_736']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_737" value="<?php echo $row_rsForm2['ITStd_737']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_738" value="<?php echo $row_rsForm2['ITStd_738']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_739" value="<?php echo $row_rsForm2['ITStd_739']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_740" value="<?php echo $row_rsForm2['ITStd_740']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_741" value="<?php echo $row_rsForm2['ITStd_741']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_742" value="<?php echo $row_rsForm2['ITStd_742']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_743" value="<?php echo $row_rsForm2['ITStd_743']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_744" value="<?php echo $row_rsForm2['ITStd_744']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_745" value="<?php echo $row_rsForm2['ITStd_745']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_746" value="<?php echo $row_rsForm2['ITStd_746']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_747" value="<?php echo $row_rsForm2['ITStd_747']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_748" value="<?php echo $row_rsForm2['ITStd_748']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_749" value="<?php echo $row_rsForm2['ITStd_749']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_750" value="<?php echo $row_rsForm2['ITStd_750']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_751" value="<?php echo $row_rsForm2['ITStd_751']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_752" value="<?php echo $row_rsForm2['ITStd_752']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_753" value="<?php echo $row_rsForm2['ITStd_753']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_754" value="<?php echo $row_rsForm2['ITStd_754']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_755" value="<?php echo $row_rsForm2['ITStd_755']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_756" value="<?php echo $row_rsForm2['ITStd_756']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_757" value="<?php echo $row_rsForm2['ITStd_757']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_758" value="<?php echo $row_rsForm2['ITStd_758']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_759" value="<?php echo $row_rsForm2['ITStd_759']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_760" value="<?php echo $row_rsForm2['ITStd_760']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_761" value="<?php echo $row_rsForm2['ITStd_761']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_762" value="<?php echo $row_rsForm2['ITStd_762']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_763" value="<?php echo $row_rsForm2['ITStd_763']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_764" value="<?php echo $row_rsForm2['ITStd_764']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_765" value="<?php echo $row_rsForm2['ITStd_765']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_766" value="<?php echo $row_rsForm2['ITStd_766']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_767" value="<?php echo $row_rsForm2['ITStd_767']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_768" value="<?php echo $row_rsForm2['ITStd_768']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_769" value="<?php echo $row_rsForm2['ITStd_769']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_770" value="<?php echo $row_rsForm2['ITStd_770']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_771" value="<?php echo $row_rsForm2['ITStd_771']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_772" value="<?php echo $row_rsForm2['ITStd_772']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_773" value="<?php echo $row_rsForm2['ITStd_773']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_774" value="<?php echo $row_rsForm2['ITStd_774']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_775" value="<?php echo $row_rsForm2['ITStd_775']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_776" value="<?php echo $row_rsForm2['ITStd_776']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_777" value="<?php echo $row_rsForm2['ITStd_777']; ?>" size="20" maxlength="100" /> </li>
<li><label>Employee Name: </label><input type="text" name="ITStd_778" value="<?php echo $row_rsForm2['ITStd_778']; ?>" size="20" maxlength="100" /> 
<label>User Name: </label><input type="text" name="ITStd_779" value="<?php echo $row_rsForm2['ITStd_779']; ?>" size="15" maxlength="100" />
<label>Password: </label><input type="text" name="ITStd_780" value="<?php echo $row_rsForm2['ITStd_780']; ?>" size="20" maxlength="100" /> </li>
  <?php }//end of if ?>    
								</ol>
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 7</label>
								<br /><br />
								<label>IN THE EVENT OF A DISASTER OR A LOSS YOU WILL NEED TO HAVE ACCESS TO YOU ESSENTIAL DATA. PLEASE CREATE A LIST OF THE DATA OR SYSTEMS YOU WILL NEED TO HAVE ACCESS TO IMMEDIATELY, IN ORDER OR PRIORITY. MOST IMPORTANT INFORMATION FIRST.</label>
								<br />
								<label>Data Source #1: </label><input type="text" name="IT_DS01" value="<?php echo $row_rsForm['IT_DS01']; ?>" size="20" maxlength="100" /><br />
								<label>Data Source #2: </label><input type="text" name="IT_DS02" value="<?php echo $row_rsForm['IT_DS02']; ?>" size="20" maxlength="100" /><br />
								<label>Data Source #3: </label><input type="text" name="IT_DS03" value="<?php echo $row_rsForm['IT_DS03']; ?>" size="20" maxlength="100" /><br />
								<label>Data Source #4: </label><input type="text" name="IT_DS04" value="<?php echo $row_rsForm['IT_DS04']; ?>" size="20" maxlength="100" /><br />
								<label>Data Source #5: </label><input type="text" name="IT_DS05" value="<?php echo $row_rsForm['IT_DS05']; ?>" size="20" maxlength="100" /><br />
								<label>Data Source #6: </label><input type="text" name="IT_DS06" value="<?php echo $row_rsForm['IT_DS06']; ?>" size="20" maxlength="100" /><br />
								<label>Data Source #7: </label><input type="text" name="IT_DS07" value="<?php echo $row_rsForm['IT_DS07']; ?>" size="20" maxlength="100" />
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 8</label>
								<br /><br />
								<label>IN THE EVENT OF A DISASTER YOU MAY WANT TO USE YOUR WEB-SITE AS A COMMUNICATION METHOD FOR YOUR EMPLOYEES, CUSTOMER AND ALL OTHER PEOPLE INVOLVED IN YOUR BUSINESS.</label>
								<br />
								<label>Who is your current Web-site Provider: </label><input type="text" name="IT_web01" value="<?php echo $row_rsForm['IT_web01']; ?>" size="20" maxlength="100" /><br />
								<label>Contact person: </label><input type="text" name="IT_web02" value="<?php echo $row_rsForm['IT_web02']; ?>" size="20" maxlength="100" /><br />
								<label>Phone: </label><input type="text" name="IT_web03" value="<?php echo $row_rsForm['IT_web03']; ?>" size="20" maxlength="100" /><br />
								<label>E-mail: </label><input type="text" name="IT_web04" value="<?php echo $row_rsForm['IT_web04']; ?>" size="20" maxlength="100" />
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 9</label>
								<br /><br />
								<label>Create a list of items you would like to use your web-site for during a disaster and find-out if there are any services you can set-up ahead of time:
								<br /><br />
                                In the event that you experience any data loss due to a disaster or an unforeseen event, please describe your current back-up procedures STEP-BY-STEP. This will give you a chance to review you process and see any areas for improvement. With the help of our professional we can determine you requirements in terms of IT recovery.
								<br /><br />
Please provide a summary of the daily or weekly process you perform for your system back-ups. (If you do not currently have a back-up system in place please contact us for more information). please ensure you includes the following:</label>
								<ol>
									<li><label>Load or Restore Operating Systems</label></li>
									<li><label>Restore Libraries</label></li>
									<li><label>Restore Database</label></li>
									<li><label>Verify All Restores</label></li>
								</ol>
								<textarea name="IT_sum01" cols="85" rows="30"><?php echo $row_rsForm['IT_sum01']; ?></textarea>
								<br /><br />
								<label>Please provide a step by step procedural outline for brining your system up. Please contact your Head of IT, or if you use an external agency please have them provide a basic outline for you:</label>
                                <br /><br />
								<textarea name="IT_sum02" cols="85" rows="30"><?php echo $row_rsForm['IT_sum02']; ?></textarea>
								<br /><br />
								<label>Please provide a step by step procedural outline for restoring your USER FILES. Please contact your Head of IT, or if you use an external agency please have them provide a basic outline for you:</label>
                                <br /><br />
								<textarea name="IT_sum03" cols="85" rows="30"><?php echo $row_rsForm['IT_sum03']; ?></textarea>
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 10</label>
								<br /><br />
								<label>If you currently store your information off-site please provide the information about that company so you can access your information if a disaster were to occur. If you do not have an off-site service provider you may want to consider seeking additional information. Contact us to ask us questions about IT recovery.</label>
								<br />
								<label>Company Name: </label><input type="text" name="IT_OFF01" value="<?php echo $row_rsForm['IT_OFF01']; ?>" size="20" maxlength="100" /><br />
								<label>Address: </label><input type="text" name="IT_OFF02" value="<?php echo $row_rsForm['IT_OFF02']; ?>" size="20" maxlength="100" /><br />
								<label>Phone: </label><input type="text" name="IT_OFF03" value="<?php echo $row_rsForm['IT_OFF03']; ?>" size="20" maxlength="100" /><br />
								<label>Contact: </label><input type="text" name="IT_OFF04" value="<?php echo $row_rsForm['IT_OFF04']; ?>" size="20" maxlength="100" /><br />
								<label>E-Mail: </label><input type="text" name="IT_OFF05" value="<?php echo $row_rsForm['IT_OFF05']; ?>" size="20" maxlength="100" />
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 11</label>
								<br /><br />
								<label>In the event of a disaster and you need to replace any or all of your IT equipment please create a list of potential suppliers for your business.</label>
								<br />
								<label class="lblSubQuestion">Supplier #1 Name: </label><input type="text" name="IT_SUPP01" value="<?php echo $row_rsForm['IT_SUPP01']; ?>" size="20" maxlength="100" /><br />
								<label>Phone: </label><input type="text" name="IT_SUPP02" value="<?php echo $row_rsForm['IT_SUPP02']; ?>" size="20" maxlength="100" /><br />
								<label>E-Mail: </label><input type="text" name="IT_SUPP03" value="<?php echo $row_rsForm['IT_SUPP03']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label class="lblSubQuestion">Supplier #2 Name: </label><input type="text" name="IT_SUPP04" value="<?php echo $row_rsForm['IT_SUPP04']; ?>" size="20" maxlength="100" /><br />
								<label>Phone: </label><input type="text" name="IT_SUPP05" value="<?php echo $row_rsForm['IT_SUPP05']; ?>" size="20" maxlength="100" /><br />
								<label>E-Mail: </label><input type="text" name="IT_SUPP06" value="<?php echo $row_rsForm['IT_SUPP06']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label class="lblSubQuestion">Supplier #3 Name:  </label><input type="text" name="IT_SUPP07" value="<?php echo $row_rsForm['IT_SUPP07']; ?>" size="20" maxlength="100" /><br />
								<label>Phone: </label><input type="text" name="IT_SUPP08" value="<?php echo $row_rsForm['IT_SUPP08']; ?>" size="20" maxlength="100" /><br />
								<label>E-Mail: </label><input type="text" name="IT_SUPP09" value="<?php echo $row_rsForm['IT_SUPP09']; ?>" size="20" maxlength="100" />
							</div>
                			<!-- InstanceEndEditable -->
                	</div>
                </div>
                      <div class="customNavigation" id="divFloatingMenuNavigation">
                        <div class="divFloat divBasicFloat">
                      	  <div class="lblFontBold divFloatingMainTitle <?php echo $strBackgroudColor; ?>">
                          	<label><span class="
                            <?php
                            //checks if the Solution is Starnd as the Color will of thise Solution will
							//match the one in the M change to a different color
							if ($row_loginFoundUser['Solution'] == 2)
								echo "lblEnterColor";
							else 
								echo "lblFontRed";?> lblFontSize24">M</span>y Continuity Plans:<br /> <?php echo $strEdition; ?> Edition</label>
                          </div>
                          <div>
						  	  <div id="divFormMessage" class="divBasicMessage"></div>
                              <br />
                              <label class="lblFontBold <?php echo $strEditionColour; ?>"><?php echo "STEP ".$row_rsPlans['stepNum']." - ".$row_rsPlans['mainSectionName']; ?></label>
                          </div>
                          <div class="divFloatingMenuBody">
                              <label><strong>Area:</strong> <?php echo $row_rsPlans['sectionName']; ?></label>
                          </div>
                            <hr/>
                            <div class="divFloatingMenuBody">
                                <label><strong>Number of Questions:</strong> <?php echo $row_rsPlans['NumberOfQuestions']; ?></label>
                            </div>
                            <hr/>
                          <div class="customHeader divFloatingMenuInsideHeader">
                            <a href="../../Profile.php" class="lblFontColorBlack" onclick="return confirm('Unsaved data will be ease on this page, Are you sure you want to continue?');">Home</a>
                          </div>
                          <div class="customContent divFloatingMenuInsideContent">
                                 <?php 
                                $strPrevForm = $row_rsPlans['prevSection'];//holds the file name of the next section
								$arrPrev = split("#",$strPrevForm);//holds the array of section may go depending of the solution they are using
								
								//checks if there is a # in $strNextForm meaning that form needs to redirect to another form
								if ($arrPrev[1] != "" && $row_loginFoundUser['Solution'] == 2)
									$strPrevForm = $arrPrev[1];
								else if ($arrNext[2] != "" && $row_loginFoundUser['Solution'] == 3)
									$strPrevForm = $arrPrev[2];
								else
									$strPrevForm = $arrPrev[0];
								
								//checks if there was a prev Section
                                if($strPrevForm <> "")
								{
                                	//gets the next section for its ID
									//does a selection for the users data for this form
									mysql_select_db($database_conContinuty, $conContinuty); 
									$rsPrevPlans = mysql_query("SELECT id, mainSectionName FROM continuityplans WHERE TableName = 'C2".$strPrevForm."'", $conContinuty) or die("Prev Plans: ".mysql_error()); 
									$row_rsPrevPlans = mysql_fetch_assoc($rsPrevPlans);
								
									echo "<a href=\"../".str_replace(" ","",$row_rsPrevPlans['mainSectionName'])."/".$strPrevForm.".php?SubFolder=2&Section=".$row_rsPrevPlans['id']."\" onclick=\"return confirm('Unsaved data will be ease on this page, Are you sure you want to continue?');\">
                                            <img alt=\"Previous\" src=\"".$strFilePath."images/buttonPrevious.jpg\" />
                                          </a>";
								}//end of if?>
                          </div>
                            <div class="customNavigation divFloatingMenuInsideNavigation">
                                <input type="image" src="../../images/buttonNext.jpg"  alt="Next"/>
                            </div>
                          <div class="customFooter divFloatingMenuInsideFooter">
                            	<input type="image" src="../../images/buttonSave.jpg" alt="Save" onclick="getDocID('hfSave').value = '1'"/>
                                <input type="hidden" id="hfSave" name="hfSave" value="" />
                                <!-- InstanceBeginEditable name="FormButtonsSection" --><!-- InstanceEndEditable -->
                          </div>
                        </div>
                      </div>
                	  <div class="customFooter" id="divFloatingMenuFooter"></div>
            	</div>
            	<input type="hidden" id="hfSubmit" name="hfSubmit"  value="1" /> 
                <input type="hidden" name="hfSection" value="<?php echo $SectionID; ?>" />
                <input type="hidden" name="hfSubFolder" value="<?php echo $SubFolder; ?>" /> 
            </form>		
				  
            </div>
            <?php if ($_GET['Nav'] == "1" || $_POST['hfNav'] == "1")			
			{ ?>
            <div class="customNavigation" id="divBasicNavigation" align="right">
            </div>
            <?php }//end of if ?>
            <div class="customFooter" id="divBasicFooter" align="left">
			  <!-- InstanceBeginEditable name="BasicFooter" -->			  <!-- InstanceEndEditable -->
              <?php if ($_GET['Footer'] == "1" || $_POST['hfFooter'] == "1")
				{ ?>
		        <div class="divFooterImagesLinks">
                	<a href="../../index.php?section=Basic" onmouseout="changeImage('imgFooterBasic',<?php echo "'".$strFilePath."images"; ?>/PrgBEout.jpg')" onmouseover="changeImage('imgFooterBasic',<?php echo "'".$strFilePath."images"; ?>/PrgBEover.jpg')"><img src="../../images/PrgBEout.jpg" alt="Our Basic Edition" width="215" height="75" id="imgFooterBasic" /></a>
                    <a href="../../index.php?section=Standard" onmouseout="changeImage('imgFooterStandard',<?php echo "'".$strFilePath."images"; ?>/PrgSEout.jpg')" onmouseover="changeImage('imgFooterStandard',<?php echo "'".$strFilePath."images"; ?>/PrgSEover.jpg')"><img src="../../images/PrgSEout.jpg" alt="Our Standard Edition" width="215" height="75" id="imgFooterStandard" /></a>
                    <a href="../../index.php?section=Enterprise" onmouseout="changeImage('imgFooterEnterprise',<?php echo "'".$strFilePath."images"; ?>/PrgEEout.jpg')" onmouseover="changeImage('imgFooterEnterprise',<?php echo "'".$strFilePath."images"; ?>/PrgEEover.jpg')"><img src="../../images/PrgEEout.jpg" alt="Our Enterprise Edition" width="215" height="75" id="imgFooterEnterprise" /></a>
               	</div>
                <?php }//end of if ?>
                <div class="divFooterLinks">
                    <a href="../../index.php" class="aHeaderFooterLinks">Home &nbsp;&nbsp;| </a>                  
                    <a href="../../Contact.php?section=Contact&Footer=1" class="aHeaderFooterLinks">Contact Us &nbsp;&nbsp;| </a>
                    <a href="../../FAQ.php?section=FAQ&Footer=1" class="aHeaderFooterLinks">FAQ's &nbsp;&nbsp;| </a>
                    <a href="../../Terms.php?section=Terms&Footer=1" class="aHeaderFooterLinks">Terms &amp; Conditions &nbsp;&nbsp;| </a>
                    <a href="../../Privacy.php?section=Privacy&Footer=1" class="aHeaderFooterLinks">Privacy Policy</a>
                </div>
            </div><!-- end of Footer -->
        </div><!-- end of Container -->	
	</div>
    
    <!-- has to be down here to loade all of the tags that have yet to be loaded -->
    <script language="javascript" type="text/javascript">
		//does the start up of adjustments to the different broswer
		startUp();
	</script>   
</body>
<!-- InstanceEnd --></html>
