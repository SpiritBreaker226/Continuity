<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/EditionTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>My Continuity Plans - Logistics - Continuity Inc. - Disaster Recovery Solutions</title>
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
	<!-- InstanceBeginEditable name="CPUpdate" --><?php mysql_query("UPDATE ".$row_rsPlans['TableName']." SET LOG_01 ='".str_replace("'","''",$_POST['LOG_01'])."',LOG_02 ='".str_replace("'","''",$_POST['LOG_02'])."',LOG_03 ='".str_replace("'","''",$_POST['LOG_03'])."',LOG_04 ='".str_replace("'","''",$_POST['LOG_04'])."',LOG_05 ='".str_replace("'","''",$_POST['LOG_05'])."',LOG_06 ='".str_replace("'","''",$_POST['LOG_06'])."',LOG_07 ='".str_replace("'","''",$_POST['LOG_07'])."',LOG_08 ='".str_replace("'","''",$_POST['LOG_08'])."',LOG_09 ='".str_replace("'","''",$_POST['LOG_09'])."',LOG_10 ='".str_replace("'","''",$_POST['LOG_10'])."',LOG_11 ='".str_replace("'","''",$_POST['LOG_11'])."',LOG_12 ='".str_replace("'","''",$_POST['LOG_12'])."',LOG_13 ='".str_replace("'","''",$_POST['LOG_13'])."',LOG_14 ='".str_replace("'","''",$_POST['LOG_14'])."',LOG_15 ='".str_replace("'","''",$_POST['LOG_15'])."',LOG_16 ='".str_replace("'","''",$_POST['LOG_16'])."',LOG_17 ='".str_replace("'","''",$_POST['LOG_17'])."',LOG_18 ='".str_replace("'","''",$_POST['LOG_18'])."',LOG_19 ='".str_replace("'","''",$_POST['LOG_19'])."',LOG_20 ='".str_replace("'","''",$_POST['LOG_20'])."',LOG_21 ='".str_replace("'","''",$_POST['LOG_21'])."',LOG_22 ='".str_replace("'","''",$_POST['LOG_22'])."',LOG_23 ='".str_replace("'","''",$_POST['LOG_23'])."',LOG_24 ='".str_replace("'","''",$_POST['LOG_24'])."',LOG_25 ='".str_replace("'","''",$_POST['LOG_25'])."',LOG_26 ='".str_replace("'","''",$_POST['LOG_26'])."',LOG_27 ='".str_replace("'","''",$_POST['LOG_27'])."',LOG_28 ='".str_replace("'","''",$_POST['LOG_28'])."',LOG_29 ='".str_replace("'","''",$_POST['LOG_29'])."',LOG_30 ='".str_replace("'","''",$_POST['LOG_30'])."',LOG_31 ='".str_replace("'","''",$_POST['LOG_31'])."',LOG_32 ='".str_replace("'","''",$_POST['LOG_32'])."',LOG_33 ='".str_replace("'","''",$_POST['LOG_33'])."',LOG_34 ='".str_replace("'","''",$_POST['LOG_34'])."',LOG_35 ='".str_replace("'","''",$_POST['LOG_35'])."',LOG_36 ='".str_replace("'","''",$_POST['LOG_36'])."',LOG_37 ='".str_replace("'","''",$_POST['LOG_37'])."',LOG_38 ='".str_replace("'","''",$_POST['LOG_38'])."',LOG_39 ='".str_replace("'","''",$_POST['LOG_39'])."',LOG_40 ='".str_replace("'","''",$_POST['LOG_40'])."',LOG_41 ='".str_replace("'","''",$_POST['LOG_41'])."',LOG_42 ='".str_replace("'","''",$_POST['LOG_42'])."',LOG_43 ='".str_replace("'","''",$_POST['LOG_43'])."',LOG_44 ='".str_replace("'","''",$_POST['LOG_44'])."',LOG_45 ='".str_replace("'","''",$_POST['LOG_45'])."',LOG_46 ='".str_replace("'","''",$_POST['LOG_46'])."',LOG_47 ='".str_replace("'","''",$_POST['LOG_47'])."',LOG_48 ='".str_replace("'","''",$_POST['LOG_48'])."',LOG_49 ='".str_replace("'","''",$_POST['LOG_49'])."',LOG_50 ='".str_replace("'","''",$_POST['LOG_50'])."',LOG_51 ='".str_replace("'","''",$_POST['LOG_51'])."',LOG_52 ='".str_replace("'","''",$_POST['LOG_52'])."',LOG_53 ='".str_replace("'","''",$_POST['LOG_53'])."',LOG_54 ='".str_replace("'","''",$_POST['LOG_54'])."',LOG_55 ='".str_replace("'","''",$_POST['LOG_55'])."',LOG_56 ='".str_replace("'","''",$_POST['LOG_56'])."',LOG_57 ='".str_replace("'","''",$_POST['LOG_57'])."',LOG_58 ='".str_replace("'","''",$_POST['LOG_58'])."',LOG_59 ='".str_replace("'","''",$_POST['LOG_59'])."',LOG_60 ='".str_replace("'","''",$_POST['LOG_60'])."',LOG_61 ='".str_replace("'","''",$_POST['LOG_61'])."',LOG_62 ='".str_replace("'","''",$_POST['LOG_62'])."',LOG_63 ='".str_replace("'","''",$_POST['LOG_63'])."',LOG_64 ='".str_replace("'","''",$_POST['LOG_64'])."',LOG_65 ='".str_replace("'","''",$_POST['LOG_65'])."',LOG_66 ='".str_replace("'","''",$_POST['LOG_66'])."',LOG_67 ='".str_replace("'","''",$_POST['LOG_67'])."',LOG_68 ='".str_replace("'","''",$_POST['LOG_68'])."',LOG_69 ='".str_replace("'","''",$_POST['LOG_69'])."',LOG_70 ='".str_replace("'","''",$_POST['LOG_70'])."',LOG_71 ='".str_replace("'","''",$_POST['LOG_71'])."',LOG_72 ='".str_replace("'","''",$_POST['LOG_72'])."',LOG_73 ='".str_replace("'","''",$_POST['LOG_73'])."',LOG_74 ='".str_replace("'","''",$_POST['LOG_74'])."',LOG_75 ='".str_replace("'","''",$_POST['LOG_75'])."',LOG_76 ='".str_replace("'","''",$_POST['LOG_76'])."',LOG_77 ='".str_replace("'","''",$_POST['LOG_77'])."',LOG_78 ='".str_replace("'","''",$_POST['LOG_78'])."',LOG_79 ='".str_replace("'","''",$_POST['LOG_79'])."',LOG_80 ='".str_replace("'","''",$_POST['LOG_80'])."',LOG_81 ='".str_replace("'","''",$_POST['LOG_81'])."',LOG_82 ='".str_replace("'","''",$_POST['LOG_82'])."',LOG_83 ='".str_replace("'","''",$_POST['LOG_83'])."',LOG_84 ='".str_replace("'","''",$_POST['LOG_84'])."',LOG_85 ='".str_replace("'","''",$_POST['LOG_85'])."',LOG_86 ='".str_replace("'","''",$_POST['LOG_86'])."',LOG_87 ='".str_replace("'","''",$_POST['LOG_87'])."',LOG_DESC01 ='".str_replace("'","''",$_POST['LOG_DESC01'])."' WHERE UserID = ".$UserID, $conContinuty) or die(mysql_error()); ?><!-- InstanceEndEditable -->		    <?php 
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
              <h1><!-- InstanceBeginEditable name="h1Title" -->My Continuity Plans - <?php echo $strEdition; ?> Edition - Logistics<!-- InstanceEndEditable --></h1>
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
								<label>In the Event of A Disaster you may require the assistance of speciality companies to assist in the recovery and or moving process. Please seek out and identify the company that is closest and willing to provide service even in a disaster.
								<br /><br />
								Please identify a company in your area that currently provides the following services:</label>
								<br /><br />
								<label>Airline: </label><input type="text" name="LOG_01" value="<?php echo $row_rsForm['LOG_01']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_02" value="<?php echo $row_rsForm['LOG_02']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_03" value="<?php echo $row_rsForm['LOG_03']; ?>" size="20" maxlength="100" /><br />
								<label>Office Space: </label><input type="text" name="LOG_04" value="<?php echo $row_rsForm['LOG_04']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_05" value="<?php echo $row_rsForm['LOG_05']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_06" value="<?php echo $row_rsForm['LOG_06']; ?>" size="20" maxlength="100" /><br />
								<label>Hotel: </label><input type="text" name="LOG_07" value="<?php echo $row_rsForm['LOG_07']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_08" value="<?php echo $row_rsForm['LOG_08']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_09" value="<?php echo $row_rsForm['LOG_09']; ?>" size="20" maxlength="100" /><br />
								<label>Chartered Bus: </label><input type="text" name="LOG_10" value="<?php echo $row_rsForm['LOG_10']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_11" value="<?php echo $row_rsForm['LOG_11']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_12" value="<?php echo $row_rsForm['LOG_12']; ?>" size="20" maxlength="100" /><br />
								<label>Car Rentals: </label><input type="text" name="LOG_13" value="<?php echo $row_rsForm['LOG_13']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_14" value="<?php echo $row_rsForm['LOG_14']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_15" value="<?php echo $row_rsForm['LOG_15']; ?>" size="20" maxlength="100" /><br />
								<label>Courier Local: </label><input type="text" name="LOG_16" value="<?php echo $row_rsForm['LOG_16']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_17" value="<?php echo $row_rsForm['LOG_17']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_18" value="<?php echo $row_rsForm['LOG_18']; ?>" size="20" maxlength="100" /><br />
								<label>Courier Long Distance: </label><input type="text" name="LOG_19" value="<?php echo $row_rsForm['LOG_19']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_20" value="<?php echo $row_rsForm['LOG_20']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_21" value="<?php echo $row_rsForm['LOG_21']; ?>" size="20" maxlength="100" /><br />
								<label>Local Transit: </label><input type="text" name="LOG_22" value="<?php echo $row_rsForm['LOG_22']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_23" value="<?php echo $row_rsForm['LOG_23']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_24" value="<?php echo $row_rsForm['LOG_24']; ?>" size="20" maxlength="100" /><br />
								<label>Moving Company: </label><input type="text" name="LOG_25" value="<?php echo $row_rsForm['LOG_25']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_26" value="<?php echo $row_rsForm['LOG_26']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_27" value="<?php echo $row_rsForm['LOG_27']; ?>" size="20" maxlength="100" /><br />
								<label>Postal Service: </label><input type="text" name="LOG_28" value="<?php echo $row_rsForm['LOG_28']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_29" value="<?php echo $row_rsForm['LOG_29']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_30" value="<?php echo $row_rsForm['LOG_30']; ?>" size="20" maxlength="100" /><br />
								<label>Telephone System: </label><input type="text" name="LOG_31" value="<?php echo $row_rsForm['LOG_31']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_32" value="<?php echo $row_rsForm['LOG_32']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_33" value="<?php echo $row_rsForm['LOG_33']; ?>" size="20" maxlength="100" /><br />
								<label>Travel Agent: </label><input type="text" name="LOG_34" value="<?php echo $row_rsForm['LOG_34']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_35" value="<?php echo $row_rsForm['LOG_35']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_36" value="<?php echo $row_rsForm['LOG_36']; ?>" size="20" maxlength="100" /><br />
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 2:</label>
                                <br /><br />
								<label>Please identify a company in your area that could provide the following services if your current provide is unavailable:</label>
								<br /><br />
								<label>Airline: </label><input type="text" name="LOG_37" value="<?php echo $row_rsForm['LOG_37']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_38" value="<?php echo $row_rsForm['LOG_38']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_39" value="<?php echo $row_rsForm['LOG_39']; ?>" size="20" maxlength="100" /><br />
								<label>Apartments: </label><input type="text" name="LOG_40" value="<?php echo $row_rsForm['LOG_40']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_41" value="<?php echo $row_rsForm['LOG_41']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_42" value="<?php echo $row_rsForm['LOG_42']; ?>" size="20" maxlength="100" /><br />
								<label>Hotel: </label><input type="text" name="LOG_43" value="<?php echo $row_rsForm['LOG_43']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_44" value="<?php echo $row_rsForm['LOG_44']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_45" value="<?php echo $row_rsForm['LOG_45']; ?>" size="20" maxlength="100" /><br />
								<label>Chartered Bus: </label><input type="text" name="LOG_46" value="<?php echo $row_rsForm['LOG_46']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_47" value="<?php echo $row_rsForm['LOG_47']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_48" value="<?php echo $row_rsForm['LOG_48']; ?>" size="20" maxlength="100" /><br />
								<label>Car Rentals: </label><input type="text" name="LOG_49" value="<?php echo $row_rsForm['LOG_49']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_50" value="<?php echo $row_rsForm['LOG_50']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_51" value="<?php echo $row_rsForm['LOG_51']; ?>" size="20" maxlength="100" /><br />
								<label>Courier Local: </label><input type="text" name="LOG_52" value="<?php echo $row_rsForm['LOG_52']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_53" value="<?php echo $row_rsForm['LOG_53']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_54" value="<?php echo $row_rsForm['LOG_54']; ?>" size="20" maxlength="100" /><br />
								<label>Courier Long Distance: </label><input type="text" name="LOG_55" value="<?php echo $row_rsForm['LOG_55']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_56" value="<?php echo $row_rsForm['LOG_56']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_57" value="<?php echo $row_rsForm['LOG_57']; ?>" size="20" maxlength="100" /><br />
								<label>Freight: </label><input type="text" name="LOG_58" value="<?php echo $row_rsForm['LOG_58']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_59" value="<?php echo $row_rsForm['LOG_59']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_60" value="<?php echo $row_rsForm['LOG_60']; ?>" size="20" maxlength="100" /><br />
								<label>Local Transit: </label><input type="text" name="LOG_61" value="<?php echo $row_rsForm['LOG_61']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_62" value="<?php echo $row_rsForm['LOG_62']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_63" value="<?php echo $row_rsForm['LOG_63']; ?>" size="20" maxlength="100" /><br />
								<label>Moving Company: </label><input type="text" name="LOG_64" value="<?php echo $row_rsForm['LOG_64']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_65" value="<?php echo $row_rsForm['LOG_65']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_66" value="<?php echo $row_rsForm['LOG_66']; ?>" size="20" maxlength="100" /><br />
								<label>Postal Service: </label><input type="text" name="LOG_67" value="<?php echo $row_rsForm['LOG_67']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_68" value="<?php echo $row_rsForm['LOG_68']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_69" value="<?php echo $row_rsForm['LOG_69']; ?>" size="20" maxlength="100" /><br />
								<label>Telephone System:  </label><input type="text" name="LOG_70" value="<?php echo $row_rsForm['LOG_70']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_71" value="<?php echo $row_rsForm['LOG_71']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_72" value="<?php echo $row_rsForm['LOG_72']; ?>" size="20" maxlength="100" /><br />
								<label>Travel Agent: </label><input type="text" name="LOG_73" value="<?php echo $row_rsForm['LOG_73']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_74" value="<?php echo $row_rsForm['LOG_74']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_75" value="<?php echo $row_rsForm['LOG_75']; ?>" size="20" maxlength="100" /><br />
								<br /><br />
								<label>Puralator: </label><input type="text" name="LOG_76" value="<?php echo $row_rsForm['LOG_76']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_77" value="<?php echo $row_rsForm['LOG_77']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_78" value="<?php echo $row_rsForm['LOG_78']; ?>" size="20" maxlength="100" /><br />
								<label>UPS: </label><input type="text" name="LOG_79" value="<?php echo $row_rsForm['LOG_79']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_80" value="<?php echo $row_rsForm['LOG_80']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_81" value="<?php echo $row_rsForm['LOG_81']; ?>" size="20" maxlength="100" /><br />
								<label>FEDEX: </label><input type="text" name="LOG_82" value="<?php echo $row_rsForm['LOG_82']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_83" value="<?php echo $row_rsForm['LOG_83']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_84" value="<?php echo $row_rsForm['LOG_84']; ?>" size="20" maxlength="100" /><br />
								<label>DSL: </label><input type="text" name="LOG_85" value="<?php echo $row_rsForm['LOG_85']; ?>" size="20" maxlength="100" /> <label>Phone: </label><input type="text" name="LOG_86" value="<?php echo $row_rsForm['LOG_86']; ?>" size="20" maxlength="100" /> <label>Fax: </label><input type="text" name="LOG_87" value="<?php echo $row_rsForm['LOG_87']; ?>" size="20" maxlength="100" /><br />
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 3:</label>
                                <br /><br />
								<label>Please give a brief overview of the logistics of your company. What, if any are the products your ship and receive on a regular basis, If possible provide a brief paragraph that outlines the day-to day operations:</label>
								<br /><br />
								<textarea name="LOG_DESC01" cols="85" rows="30"><?php echo $row_rsForm['LOG_DESC01']; ?></textarea>
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
