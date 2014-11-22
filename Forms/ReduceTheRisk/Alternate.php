<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/EditionTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>My Continuity Plans - Alternate Location &amp; Suppliers - Continuity Inc. - Disaster Recovery Solutions</title>
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
	<!-- InstanceBeginEditable name="CPUpdate" --><?php mysql_query("UPDATE ".$row_rsPlans['TableName']." SET ALT_TempLOG01 ='".str_replace("'","''",$_POST['ALT_TempLOG01'])."',ALT_TempLOG02 ='".str_replace("'","''",$_POST['ALT_TempLOG02'])."',ALT_TempLOG03 ='".str_replace("'","''",$_POST['ALT_TempLOG03'])."',ALT_TempLOG04 ='".str_replace("'","''",$_POST['ALT_TempLOG04'])."',ALT_TempLOG05 ='".str_replace("'","''",$_POST['ALT_TempLOG05'])."',ALT_Temp2LOG01 ='".str_replace("'","''",$_POST['ALT_Temp2LOG01'])."',ALT_Temp2LOG02 ='".str_replace("'","''",$_POST['ALT_Temp2LOG02'])."',ALT_Temp2LOG03 ='".str_replace("'","''",$_POST['ALT_Temp2LOG03'])."',ALT_Temp2LOG04 ='".str_replace("'","''",$_POST['ALT_Temp2LOG04'])."',ALT_Temp2LOG05 ='".str_replace("'","''",$_POST['ALT_Temp2LOG05'])."',ALT_SemiLOG01 ='".str_replace("'","''",$_POST['ALT_SemiLOG01'])."',ALT_SemiLOG02 ='".str_replace("'","''",$_POST['ALT_SemiLOG02'])."',ALT_SemiLOG03 ='".str_replace("'","''",$_POST['ALT_SemiLOG03'])."',ALT_SemiLOG04 ='".str_replace("'","''",$_POST['ALT_SemiLOG04'])."',ALT_SemiLOG05 ='".str_replace("'","''",$_POST['ALT_SemiLOG05'])."',ALT_Semi2LOG01 ='".str_replace("'","''",$_POST['ALT_Semi2LOG01'])."',ALT_Semi2LOG02 ='".str_replace("'","''",$_POST['ALT_Semi2LOG02'])."',ALT_Semi2LOG03 ='".str_replace("'","''",$_POST['ALT_Semi2LOG03'])."',ALT_Semi2LOG04 ='".str_replace("'","''",$_POST['ALT_Semi2LOG04'])."',ALT_Semi2LOG05 ='".str_replace("'","''",$_POST['ALT_Semi2LOG05'])."',ALT_PermLOG01 ='".str_replace("'","''",$_POST['ALT_PermLOG01'])."',ALT_PermLOG02 ='".str_replace("'","''",$_POST['ALT_PermLOG02'])."',ALT_PermLOG03 ='".str_replace("'","''",$_POST['ALT_PermLOG03'])."',ALT_PermLOG04 ='".str_replace("'","''",$_POST['ALT_PermLOG04'])."',ALT_PermLOG05 ='".str_replace("'","''",$_POST['ALT_PermLOG05'])."',ALT_Perm2LOG01 ='".str_replace("'","''",$_POST['ALT_Perm2LOG01'])."',ALT_Perm2LOG02 ='".str_replace("'","''",$_POST['ALT_Perm2LOG02'])."',ALT_Perm2LOG03 ='".str_replace("'","''",$_POST['ALT_Perm2LOG03'])."',ALT_Perm2LOG04 ='".str_replace("'","''",$_POST['ALT_Perm2LOG04'])."',ALT_Perm2LOG05 ='".str_replace("'","''",$_POST['ALT_Perm2LOG05'])."',ALT_SUPPD01 ='".str_replace("'","''",$_POST['ALT_SUPPD01'])."',ALT_SUPPD02 ='".str_replace("'","''",$_POST['ALT_SUPPD02'])."',ALT_SUPPD03 ='".str_replace("'","''",$_POST['ALT_SUPPD03'])."',ALT_SUPPD04 ='".str_replace("'","''",$_POST['ALT_SUPPD04'])."',ALT_SUPPD05 ='".str_replace("'","''",$_POST['ALT_SUPPD05'])."',ALT_SUPPD06 ='".str_replace("'","''",$_POST['ALT_SUPPD06'])."',ALT_SUPPD07 ='".str_replace("'","''",$_POST['ALT_SUPPD07'])."',ALT_SUPPD08 ='".str_replace("'","''",$_POST['ALT_SUPPD08'])."',ALT_SUPPD09 ='".str_replace("'","''",$_POST['ALT_SUPPD09'])."',ALT_SUPPD10 ='".str_replace("'","''",$_POST['ALT_SUPPD10'])."',ALT_SUPPD11 ='".str_replace("'","''",$_POST['ALT_SUPPD11'])."',ALT_SUPPD12 ='".str_replace("'","''",$_POST['ALT_SUPPD12'])."',ALT_SUPPD13 ='".str_replace("'","''",$_POST['ALT_SUPPD13'])."',ALT_SUPPD14 ='".str_replace("'","''",$_POST['ALT_SUPPD14'])."',ALT_SUPPD15 ='".str_replace("'","''",$_POST['ALT_SUPPD15'])."',ALT_SUPPD16 ='".str_replace("'","''",$_POST['ALT_SUPPD16'])."',ALT_SUPPD17 ='".str_replace("'","''",$_POST['ALT_SUPPD17'])."',ALT_SUPPD18 ='".str_replace("'","''",$_POST['ALT_SUPPD18'])."',ALT_SUPPD19 ='".str_replace("'","''",$_POST['ALT_SUPPD19'])."',ALT_SUPPD20 ='".str_replace("'","''",$_POST['ALT_SUPPD20'])."',ALT_SUPPD21 ='".str_replace("'","''",$_POST['ALT_SUPPD21'])."',ALT_SUPPD22 ='".str_replace("'","''",$_POST['ALT_SUPPD22'])."',ALT_SUPPD23 ='".str_replace("'","''",$_POST['ALT_SUPPD23'])."',ALT_SUPPD24 ='".str_replace("'","''",$_POST['ALT_SUPPD24'])."',ALT_SUPPD25 ='".str_replace("'","''",$_POST['ALT_SUPPD25'])."',ALT_SUPPD26 ='".str_replace("'","''",$_POST['ALT_SUPPD26'])."',ALT_SUPPD27 ='".str_replace("'","''",$_POST['ALT_SUPPD27'])."',ALT_SUPPD28 ='".str_replace("'","''",$_POST['ALT_SUPPD28'])."',ALT_SUPPD29 ='".str_replace("'","''",$_POST['ALT_SUPPD29'])."',ALT_SUPPD30 ='".str_replace("'","''",$_POST['ALT_SUPPD30'])."',ALT_SUPSR01 ='".str_replace("'","''",$_POST['ALT_SUPSR01'])."',ALT_SUPSR02 ='".str_replace("'","''",$_POST['ALT_SUPSR02'])."',ALT_SUPSR03 ='".str_replace("'","''",$_POST['ALT_SUPSR03'])."',ALT_SUPSR04 ='".str_replace("'","''",$_POST['ALT_SUPSR04'])."',ALT_SUPSR05 ='".str_replace("'","''",$_POST['ALT_SUPSR05'])."',ALT_SUPSR06 ='".str_replace("'","''",$_POST['ALT_SUPSR06'])."',ALT_SUPSR07 ='".str_replace("'","''",$_POST['ALT_SUPSR07'])."',ALT_SUPSR08 ='".str_replace("'","''",$_POST['ALT_SUPSR08'])."',ALT_SUPSR09 ='".str_replace("'","''",$_POST['ALT_SUPSR09'])."',ALT_SUPSR10 ='".str_replace("'","''",$_POST['ALT_SUPSR10'])."',ALT_SUPSR11 ='".str_replace("'","''",$_POST['ALT_SUPSR11'])."',ALT_SUPSR12 ='".str_replace("'","''",$_POST['ALT_SUPSR12'])."',ALT_SUPSR13 ='".str_replace("'","''",$_POST['ALT_SUPSR13'])."',ALT_SUPSR14 ='".str_replace("'","''",$_POST['ALT_SUPSR14'])."',ALT_SUPSR15 ='".str_replace("'","''",$_POST['ALT_SUPSR15'])."',ALT_SUPSR16 ='".str_replace("'","''",$_POST['ALT_SUPSR16'])."',ALT_SUPSR17 ='".str_replace("'","''",$_POST['ALT_SUPSR17'])."',ALT_SUPSR18 ='".str_replace("'","''",$_POST['ALT_SUPSR18'])."',ALT_SUPSR19 ='".str_replace("'","''",$_POST['ALT_SUPSR19'])."',ALT_SUPSR20 ='".str_replace("'","''",$_POST['ALT_SUPSR20'])."',ALT_SUPSR21 ='".str_replace("'","''",$_POST['ALT_SUPSR21'])."',ALT_SUPSR22 ='".str_replace("'","''",$_POST['ALT_SUPSR22'])."',ALT_SUPSR23 ='".str_replace("'","''",$_POST['ALT_SUPSR23'])."',ALT_SUPSR24 ='".str_replace("'","''",$_POST['ALT_SUPSR24'])."' WHERE UserID = ".$UserID, $conContinuty) or die(mysql_error()); ?><!-- InstanceEndEditable -->		    <?php 
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
              <h1><!-- InstanceBeginEditable name="h1Title" -->My Continuity Plans - <?php echo $strEdition; ?> Edition - Alternate Location &amp; Suppliers<!-- InstanceEndEditable --></h1>
          	</div><!-- end of Header -->
            <div class="customContent" id="divBasicContent">
                
		<form action="<?php echo $Form; ?>" method="post" id="frmForm" class="frmBasics" enctype="multipart/form-data"> 
              <div class="customContainer" id="divFloatingMenuContainer">
                <div class="customContent" id="divFloatingMenuContent">
                	<div align="left">
			                <!-- InstanceBeginEditable name="PlanContent" --> 
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 1:</label>
                                <br /><br />
								<label>In the event of a disaster you may require to access a temporary location or semi-permanent location to continue in business. Whether it is a friendly competitor or your home, a location must be pre-determined, and all of the tools and equipment necessary to resume business as quickly as possible.
								<br /><br />
								In the event of a disaster and your business is not able to remain open, please identify a temporary location that you could potentially use to remain in business.</label>
                                <br /><br />
								<label class="lblSubQuestion">Alternate Location #1: Temporary Office Location</label><br />
								<label>Name of Location: </label><input type="text" name="ALT_TempLOG01" value="<?php echo $row_rsForm['ALT_TempLOG01']; ?>" size="20" maxlength="100" /><br />
								<label>Location Address: </label><input type="text" name="ALT_TempLOG02" value="<?php echo $row_rsForm['ALT_TempLOG02']; ?>" size="20" maxlength="100" /><br />
								<label>Phone: </label><input type="text" name="ALT_TempLOG03" value="<?php echo $row_rsForm['ALT_TempLOG03']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_TempLOG04" value="<?php echo $row_rsForm['ALT_TempLOG04']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Site Agreement &amp; Conditions: </label><textarea name="ALT_TempLOG05" cols="85" rows="30"><?php echo $row_rsForm['ALT_TempLOG05']; ?></textarea><br /><br />
								<label class="lblSubQuestion">Alternate Location #1: Temporary Office Location (Back-Up)</label><br />
								<label>Name of Location: </label><input type="text" name="ALT_Temp2LOG01" value="<?php echo $row_rsForm['ALT_Temp2LOG01']; ?>" size="20" maxlength="100" /><br />
								<label>Location Address: </label><input type="text" name="ALT_Temp2LOG02" value="<?php echo $row_rsForm['ALT_Temp2LOG02']; ?>" size="20" maxlength="100" /><br />
								<label>Phone: </label><input type="text" name="ALT_Temp2LOG03" value="<?php echo $row_rsForm['ALT_Temp2LOG03']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_Temp2LOG04" value="<?php echo $row_rsForm['ALT_Temp2LOG04']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Site Agreement &amp; Conditions: </label><textarea name="ALT_Temp2LOG05" cols="85" rows="30"><?php echo $row_rsForm['ALT_Temp2LOG05']; ?></textarea><br /><br />
								<label class="lblSubQuestion">Alternate Location #2: Semi-Permanent Office Location</label><br />
								<label>Name of Location: </label><input type="text" name="ALT_SemiLOG01" value="<?php echo $row_rsForm['ALT_SemiLOG01']; ?>" size="20" maxlength="100" /><br />
								<label>Location Address: </label><input type="text" name="ALT_SemiLOG02" value="<?php echo $row_rsForm['ALT_SemiLOG02']; ?>" size="20" maxlength="100" /><br />
								<label>Phone: </label><input type="text" name="ALT_SemiLOG03" value="<?php echo $row_rsForm['ALT_SemiLOG03']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_SemiLOG04" value="<?php echo $row_rsForm['ALT_SemiLOG04']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Site Agreement &amp; Conditions: </label><textarea name="ALT_SemiLOG05" cols="85" rows="30"><?php echo $row_rsForm['ALT_SemiLOG05']; ?></textarea>
								<br /><br />
								<label class="lblSubQuestion">Alternate Location #2: Semi-Permanent Office Location (Back-Up)</label><br />
								<label>Name of Location: </label><input type="text" name="ALT_Semi2LOG01" value="<?php echo $row_rsForm['ALT_Semi2LOG01']; ?>" size="20" maxlength="100" /><br />
								<label>Location Address: </label><input type="text" name="ALT_Semi2LOG02" value="<?php echo $row_rsForm['ALT_Semi2LOG02']; ?>" size="20" maxlength="100" /><br />
								<label>Phone: </label><input type="text" name="ALT_Semi2LOG03" value="<?php echo $row_rsForm['ALT_Semi2LOG03']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_Semi2LOG04" value="<?php echo $row_rsForm['ALT_Semi2LOG04']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Site Agreement &amp; Conditions: </label><textarea name="ALT_Semi2LOG05" cols="85" rows="30"><?php echo $row_rsForm['ALT_Semi2LOG05']; ?></textarea>
								<br /><br />
								<label class="lblSubQuestion">Alternate Location #3: Permanent New Office Location</label><br />
								<label>Name of Location: </label><input type="text" name="ALT_PermLOG01" value="<?php echo $row_rsForm['ALT_PermLOG01']; ?>" size="20" maxlength="100" /><br />
								<label>Location Address: </label><input type="text" name="ALT_PermLOG02" value="<?php echo $row_rsForm['ALT_PermLOG02']; ?>" size="20" maxlength="100" /><br />
								<label>Phone: </label><input type="text" name="ALT_PermLOG03" value="<?php echo $row_rsForm['ALT_PermLOG03']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_PermLOG04" value="<?php echo $row_rsForm['ALT_PermLOG04']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Site Agreement &amp; Conditions: </label><textarea name="ALT_PermLOG05" cols="85" rows="30"><?php echo $row_rsForm['ALT_PermLOG05']; ?></textarea><br /><br />
								<label class="lblSubQuestion">Alternate Location #3: Permanent New Office Location (Back-Up)</label><br />
								<label>Name of Location: </label><input type="text" name="ALT_Perm2LOG01" value="<?php echo $row_rsForm['ALT_Perm2LOG01']; ?>" size="20" maxlength="100" /><br />
								<label>Location Address: </label><input type="text" name="ALT_Perm2LOG02" value="<?php echo $row_rsForm['ALT_Perm2LOG02']; ?>" size="20" maxlength="100" /><br />
								<label>Phone: </label><input type="text" name="ALT_Perm2LOG03" value="<?php echo $row_rsForm['ALT_Perm2LOG03']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_Perm2LOG04" value="<?php echo $row_rsForm['ALT_Perm2LOG04']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Site Agreement &amp; Conditions: </label><textarea name="ALT_Perm2LOG05" cols="85" rows="30"><?php echo $row_rsForm['ALT_Perm2LOG05']; ?></textarea><br />
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 2:</label>
                                <br /><br />
								<label>In the event of a disaster you may be unable to use your current supplies of products or services
								<br /><br />
								Who else could supply you with the Products & Services that you require to remain in business. Should your current supplier be unable to provide you with service you will need to be able to access additional resources. Please identify some of the alternate suppliers of your products and services you can use as a back-up</label>
                                <br /><br />
								<label class="lblSubQuestion">Alternate Supplier #1: Product</label><br />
								<label>Name of Product: </label><input type="text" name="ALT_SUPPD01" value="<?php echo $row_rsForm['ALT_SUPPD01']; ?>" size="20" maxlength="100" /><br />
								<label>Name of Supplier: </label><input type="text" name="ALT_SUPPD02" value="<?php echo $row_rsForm['ALT_SUPPD02']; ?>" size="20" maxlength="100" /><br />
								<label>Address: </label><input type="text" name="ALT_SUPPD03" value="<?php echo $row_rsForm['ALT_SUPPD03']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="ALT_SUPPD04" value="<?php echo $row_rsForm['ALT_SUPPD04']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_SUPPD05" value="<?php echo $row_rsForm['ALT_SUPPD05']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Agreement &amp; Conditions: </label><textarea name="ALT_SUPPD06" cols="85" rows="30"><?php echo $row_rsForm['ALT_SUPPD06']; ?></textarea><br /><br />
								<label class="lblSubQuestion">Alternate Supplier #2: Product</label><br />
								<label>Name of Product: </label><input type="text" name="ALT_SUPPD07" value="<?php echo $row_rsForm['ALT_SUPPD07']; ?>" size="20" maxlength="100" /><br />
								<label>Name of Supplier: </label><input type="text" name="ALT_SUPPD08" value="<?php echo $row_rsForm['ALT_SUPPD08']; ?>" size="20" maxlength="100" /><br />
								<label>Address: </label><input type="text" name="ALT_SUPPD09" value="<?php echo $row_rsForm['ALT_SUPPD09']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="ALT_SUPPD10" value="<?php echo $row_rsForm['ALT_SUPPD10']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_SUPPD11" value="<?php echo $row_rsForm['ALT_SUPPD11']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Agreement &amp; Conditions: </label><textarea name="ALT_SUPPD12" cols="85" rows="30"><?php echo $row_rsForm['ALT_SUPPD12']; ?></textarea><br /><br />
								<label class="lblSubQuestion">Alternate Supplier #3: Product</label><br />
								<label>Name of Product: </label><input type="text" name="ALT_SUPPD13" value="<?php echo $row_rsForm['ALT_SUPPD13']; ?>" size="20" maxlength="100" /><br />
								<label>Name of Supplier: </label><input type="text" name="ALT_SUPPD14" value="<?php echo $row_rsForm['ALT_SUPPD14']; ?>" size="20" maxlength="100" /><br />
								<label>Address: </label><input type="text" name="ALT_SUPPD15" value="<?php echo $row_rsForm['ALT_SUPPD15']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="ALT_SUPPD16" value="<?php echo $row_rsForm['ALT_SUPPD16']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_SUPPD17" value="<?php echo $row_rsForm['ALT_SUPPD17']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Agreement &amp; Conditions: </label><textarea name="ALT_SUPPD18" cols="85" rows="30"><?php echo $row_rsForm['ALT_SUPPD18']; ?></textarea><br /><br />
								<label class="lblSubQuestion">Alternate Supplier #4: Product</label><br />
								<label>Name of Product: </label><input type="text" name="ALT_SUPPD19" value="<?php echo $row_rsForm['ALT_SUPPD19']; ?>" size="20" maxlength="100" /><br />
								<label>Name of Supplier: </label><input type="text" name="ALT_SUPPD20" value="<?php echo $row_rsForm['ALT_SUPPD20']; ?>" size="20" maxlength="100" /><br />
								<label>Address: </label><input type="text" name="ALT_SUPPD21" value="<?php echo $row_rsForm['ALT_SUPPD21']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="ALT_SUPPD22" value="<?php echo $row_rsForm['ALT_SUPPD22']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_SUPPD23" value="<?php echo $row_rsForm['ALT_SUPPD23']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Agreement &amp; Conditions: </label><textarea name="ALT_SUPPD24" cols="85" rows="30"><?php echo $row_rsForm['ALT_SUPPD24']; ?></textarea><br /><br />
								<label class="lblSubQuestion">Alternate Supplier #5: Product</label><br />
								<label>Name of Product: </label><input type="text" name="ALT_SUPPD25" value="<?php echo $row_rsForm['ALT_SUPPD25']; ?>" size="20" maxlength="100" /><br />
								<label>Name of Supplier: </label><input type="text" name="ALT_SUPPD26" value="<?php echo $row_rsForm['ALT_SUPPD26']; ?>" size="20" maxlength="100" /><br />
								<label>Address: </label><input type="text" name="ALT_SUPPD27" value="<?php echo $row_rsForm['ALT_SUPPD27']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="ALT_SUPPD28" value="<?php echo $row_rsForm['ALT_SUPPD28']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_SUPPD29" value="<?php echo $row_rsForm['ALT_SUPPD29']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Agreement &amp; Conditions: </label><textarea name="ALT_SUPPD30" cols="85" rows="30"><?php echo $row_rsForm['ALT_SUPPD30']; ?></textarea>
								<br /><br />
								<label class="lblSubQuestion">Alternate Supplier #1: Service</label><br />
								<label>Name of Service: </label><input type="text" name="ALT_SUPSR01" value="<?php echo $row_rsForm['ALT_SUPSR01']; ?>" size="20" maxlength="100" /><br />
								<label>Name of Supplier: </label><input type="text" name="ALT_SUPSR02" value="<?php echo $row_rsForm['ALT_SUPSR02']; ?>" size="20" maxlength="100" /><br />
								<label>Address: </label><input type="text" name="ALT_SUPSR03" value="<?php echo $row_rsForm['ALT_SUPSR03']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="ALT_SUPSR04" value="<?php echo $row_rsForm['ALT_SUPSR04']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_SUPSR05" value="<?php echo $row_rsForm['ALT_SUPSR05']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Agreement &amp; Conditions: </label><textarea name="ALT_SUPSR06" cols="85" rows="30"><?php echo $row_rsForm['ALT_SUPSR06']; ?></textarea><br /><br />
								<label class="lblSubQuestion">Alternate Supplier #2: Service</label><br />
								<label>Name of Service: </label><input type="text" name="ALT_SUPSR07" value="<?php echo $row_rsForm['ALT_SUPSR07']; ?>" size="20" maxlength="100" /><br />
								<label>Name of Supplier: </label><input type="text" name="ALT_SUPSR08" value="<?php echo $row_rsForm['ALT_SUPSR08']; ?>" size="20" maxlength="100" /><br />
								<label>Address: </label><input type="text" name="ALT_SUPSR09" value="<?php echo $row_rsForm['ALT_SUPSR09']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="ALT_SUPSR10" value="<?php echo $row_rsForm['ALT_SUPSR10']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_SUPSR11" value="<?php echo $row_rsForm['ALT_SUPSR11']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Agreement &amp; Conditions: </label><textarea name="ALT_SUPSR12" cols="85" rows="30"><?php echo $row_rsForm['ALT_SUPSR12']; ?></textarea><br /><br />
								<label class="lblSubQuestion">Alternate Supplier #3: Service</label><br />
								<label>Name of Service: </label><input type="text" name="ALT_SUPSR13" value="<?php echo $row_rsForm['ALT_SUPSR13']; ?>" size="20" maxlength="100" /><br />
								<label>Name of Supplier: </label><input type="text" name="ALT_SUPSR14" value="<?php echo $row_rsForm['ALT_SUPSR14']; ?>" size="20" maxlength="100" /><br />
								<label>Address: </label><input type="text" name="ALT_SUPSR15" value="<?php echo $row_rsForm['ALT_SUPSR15']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="ALT_SUPSR16" value="<?php echo $row_rsForm['ALT_SUPSR16']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_SUPSR17" value="<?php echo $row_rsForm['ALT_SUPSR17']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Agreement &amp; Conditions: </label><textarea name="ALT_SUPSR18" cols="85" rows="30"><?php echo $row_rsForm['ALT_SUPSR18']; ?></textarea><br /><br />
								<label class="lblSubQuestion">Alternate Supplier #4: Service</label><br />
								<label>Name of Service: </label><input type="text" name="ALT_SUPSR19" value="<?php echo $row_rsForm['ALT_SUPSR19']; ?>" size="20" maxlength="100" /><br />
								<label>Name of Supplier: </label><input type="text" name="ALT_SUPSR20" value="<?php echo $row_rsForm['ALT_SUPSR20']; ?>" size="20" maxlength="100" /><br />
								<label>Address: </label><input type="text" name="ALT_SUPSR21" value="<?php echo $row_rsForm['ALT_SUPSR21']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="ALT_SUPSR22" value="<?php echo $row_rsForm['ALT_SUPSR22']; ?>" size="20" maxlength="100" /><br />
								<label>Alt. phone: </label><input type="text" name="ALT_SUPSR23" value="<?php echo $row_rsForm['ALT_SUPSR23']; ?>" size="20" maxlength="100" /><br /><br />
								<label>Agreement &amp; Conditions: </label><textarea name="ALT_SUPSR24" cols="85" rows="30"><?php echo $row_rsForm['ALT_SUPSR24']; ?></textarea>
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
