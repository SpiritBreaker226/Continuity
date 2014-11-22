<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/EditionTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>My Continuity Plans - Customer Service - Continuity Inc. - Disaster Recovery Solutions</title>
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
	<!-- InstanceBeginEditable name="CPUpdate" --><?php mysql_query("UPDATE ".$row_rsPlans['TableName']." SET cust_coor01 ='".str_replace("'","''",$_POST['cust_coor01'])."',cust_coor02 ='".str_replace("'","''",$_POST['cust_coor02'])."',cust_coor03 ='".str_replace("'","''",$_POST['cust_coor03'])."',
cust_coor04 ='".str_replace("'","''",$_POST['cust_coor04'])."',cust_coor05 ='".str_replace("'","''",$_POST['cust_coor05'])."',cust_ser01 ='".str_replace("'","''",$_POST['cust_ser01'])."',cust_ser02 ='".str_replace("'","''",$_POST['cust_ser02'])."',cust_ser03 ='".str_replace("'","''",$_POST['cust_ser03'])."',cust_ser04 ='".str_replace("'","''",$_POST['cust_ser04'])."',cust_ser05 ='".str_replace("'","''",$_POST['cust_ser05'])."',cust_ser06 ='".str_replace("'","''",$_POST['cust_ser06'])."',cust_ser07 ='".str_replace("'","''",$_POST['cust_ser07'])."',cust_ser08 ='".str_replace("'","''",$_POST['cust_ser08'])."',cust_ser09 ='".str_replace("'","''",$_POST['cust_ser09'])."',cust_ser10 ='".str_replace("'","''",$_POST['cust_ser10'])."',cust_ser11 ='".str_replace("'","''",$_POST['cust_ser11'])."',cust_ser12 ='".str_replace("'","''",$_POST['cust_ser12'])."',cust_ser13 ='".str_replace("'","''",$_POST['cust_ser13'])."',cust_ser14 ='".str_replace("'","''",$_POST['cust_ser14'])."',cust_ser15 ='".str_replace("'","''",$_POST['cust_ser15'])."',cust_ser16 ='".str_replace("'","''",$_POST['cust_ser16'])."',cust_ser17 ='".str_replace("'","''",$_POST['cust_ser17'])."',cust_ser18 ='".str_replace("'","''",$_POST['cust_ser18'])."',cust_ser19 ='".str_replace("'","''",$_POST['cust_ser19'])."',cust_ser20 ='".str_replace("'","''",$_POST['cust_ser20'])."',cust_ser21 ='".str_replace("'","''",$_POST['cust_ser21'])."',cust_ser22 ='".str_replace("'","''",$_POST['cust_ser22'])."',cust_ser23 ='".str_replace("'","''",$_POST['cust_ser23'])."',cust_ser24 ='".str_replace("'","''",$_POST['cust_ser24'])."',cust_ser25 ='".str_replace("'","''",$_POST['cust_ser25'])."',cust_ser26 ='".str_replace("'","''",$_POST['cust_ser26'])."',cust_ser27 ='".str_replace("'","''",$_POST['cust_ser27'])."',cust_Pro01 ='".str_replace("'","''",$_POST['cust_Pro01'])."',cust_Pro02 ='".str_replace("'","''",$_POST['cust_Pro02'])."',cust_Pro03 ='".str_replace("'","''",$_POST['cust_Pro03'])."',cust_Pro04 ='".str_replace("'","''",$_POST['cust_Pro04'])."',cust_Pro05 ='".str_replace("'","''",$_POST['cust_Pro05'])."',cust_Pro06 ='".str_replace("'","''",$_POST['cust_Pro06'])."',cust_Pro07 ='".str_replace("'","''",$_POST['cust_Pro07'])."',cust_Pro08 ='".str_replace("'","''",$_POST['cust_Pro08'])."',cust_Pro09 ='".str_replace("'","''",$_POST['cust_Pro09'])."',cust_Pro10 ='".str_replace("'","''",$_POST['cust_Pro10'])."',cust_Pro11 ='".str_replace("'","''",$_POST['cust_Pro11'])."',cust_Pro12 ='".str_replace("'","''",$_POST['cust_Pro12'])."',cust_Pro13 ='".str_replace("'","''",$_POST['cust_Pro13'])."',cust_Pro14 ='".str_replace("'","''",$_POST['cust_Pro14'])."',cust_Pro15 ='".str_replace("'","''",$_POST['cust_Pro15'])."',cust_statement ='".str_replace("'","''",$_POST['cust_statement'])."',cust_all01 ='".str_replace("'","''",$_POST['cust_all01'])."',cust_all02 ='".str_replace("'","''",$_POST['cust_all02'])."',cust_all03 ='".str_replace("'","''",$_POST['cust_all03'])."',cust_all04 ='".str_replace("'","''",$_POST['cust_all04'])."',cust_all05 ='".str_replace("'","''",$_POST['cust_all05'])."',cust_all06 ='".str_replace("'","''",$_POST['cust_all06'])."',cust_all07 ='".str_replace("'","''",$_POST['cust_all07'])."',cust_all08 ='".str_replace("'","''",$_POST['cust_all08'])."',cust_all09 ='".str_replace("'","''",$_POST['cust_all09'])."',cust_all10 ='".str_replace("'","''",$_POST['cust_all10'])."',cust_all11 ='".str_replace("'","''",$_POST['cust_all11'])."',cust_all12 ='".str_replace("'","''",$_POST['cust_all12'])."',cust_all13 ='".str_replace("'","''",$_POST['cust_all13'])."',cust_all14 ='".str_replace("'","''",$_POST['cust_all14'])."' WHERE
	 UserID = ".$UserID, $conContinuty) or die(mysql_error()); ?><!-- InstanceEndEditable -->		    <?php 
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
              <h1><!-- InstanceBeginEditable name="h1Title" -->My Continuity Plans - <?php echo $strEdition; ?> Edition - Customer Service<!-- InstanceEndEditable --></h1>
          	</div><!-- end of Header -->
            <div class="customContent" id="divBasicContent">
                
		<form action="<?php echo $Form; ?>" method="post" id="frmForm" class="frmBasics" enctype="multipart/form-data"> 
              <div class="customContainer" id="divFloatingMenuContainer">
                <div class="customContent" id="divFloatingMenuContent">
                	<div align="left">
			                <!-- InstanceBeginEditable name="PlanContent" -->                             
							<label>In the Event of a disaster there are many areas in business that are over looked in terms of protecting your business.
Please identify a Customer Service Coordinator that will be responsible for ensuring you are able to continue to provide products and service to your customers.</label>
                            <br />
                            <div class="divQuestionForms">
                                <label>Name:</label> <input type="text" name="cust_coor01" value="<?php echo $row_rsForm['cust_coor01']; ?>" size="20" maxlength="100" /><br />
                                <label>Title:</label> <input type="text" name="cust_coor02" value="<?php echo $row_rsForm['cust_coor02']; ?>" size="20" maxlength="100" /><br />
                                <label>Phone:</label> <input type="text" name="cust_coor03" value="<?php echo $row_rsForm['cust_coor03']; ?>" size="20" maxlength="100" /><br />
                                <label>Cell:</label> <input type="text" name="cust_coor04" value="<?php echo $row_rsForm['cust_coor04']; ?>" size="20" maxlength="100" /><br />
                                <label>E-Mail:</label> <input type="text" name="cust_coor05" value="<?php echo $row_rsForm['cust_coor05']; ?>" size="20" maxlength="100" />
                                <br /><br />
                                <label>Customer Service is one of the essential areas you need to be able to provide, even in a disaster scenario.</label>
                                <br />
                            </div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 1:</label>
                                <br /><br />
								<label>Please idenitfy the key services that you currently provide to your customer in order of priority.</label>
                                <br /><br />
								<label>Service 1:</label> <input type="text" name="cust_ser01" value="<?php echo $row_rsForm['cust_ser01']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_ser02" value="<?php echo $row_rsForm['cust_ser02']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to perform this Service?</label> <input type="text" name="cust_ser03" value="<?php echo $row_rsForm['cust_ser03']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label>Service 2:</label> <input type="text" name="cust_ser04" value="<?php echo $row_rsForm['cust_ser04']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_ser05" value="<?php echo $row_rsForm['cust_ser05']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to perform this Service?</label> <input type="text" name="cust_ser06" value="<?php echo $row_rsForm['cust_ser06']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label>Service 3:</label> <input type="text" name="cust_ser07" value="<?php echo $row_rsForm['cust_ser07']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_ser08" value="<?php echo $row_rsForm['cust_ser08']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to perform this Service?</label> <input type="text" name="cust_ser09" value="<?php echo $row_rsForm['cust_ser09']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label>Service 4:</label> <input type="text" name="cust_ser10" value="<?php echo $row_rsForm['cust_ser10']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_ser11" value="<?php echo $row_rsForm['cust_ser11']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to perform this Service?</label> <input type="text" name="cust_ser12" value="<?php echo $row_rsForm['cust_ser12']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label>Service 5:</label> <input type="text" name="cust_ser13" value="<?php echo $row_rsForm['cust_ser13']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_ser14" value="<?php echo $row_rsForm['cust_ser14']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to perform this Service?</label> <input type="text" name="cust_ser15" value="<?php echo $row_rsForm['cust_ser15']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label>Service 6:</label> <input type="text" name="cust_ser16" value="<?php echo $row_rsForm['cust_ser16']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_ser17" value="<?php echo $row_rsForm['cust_ser17']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to perform this Service?</label> <input type="text" name="cust_ser18" value="<?php echo $row_rsForm['cust_ser18']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label>Service 7:</label> <input type="text" name="cust_ser19" value="<?php echo $row_rsForm['cust_ser19']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_ser20" value="<?php echo $row_rsForm['cust_ser20']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to perform this Service?</label> <input type="text" name="cust_ser21" value="<?php echo $row_rsForm['cust_ser21']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label>Service 8:</label> <input type="text" name="cust_ser22" value="<?php echo $row_rsForm['cust_ser22']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_ser23" value="<?php echo $row_rsForm['cust_ser23']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to perform this Service?</label> <input type="text" name="cust_ser24" value="<?php echo $row_rsForm['cust_ser24']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label>Service 9:</label> <input type="text" name="cust_ser25" value="<?php echo $row_rsForm['cust_ser25']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_ser26" value="<?php echo $row_rsForm['cust_ser26']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to perform this Service?</label> <input type="text" name="cust_ser27" value="<?php echo $row_rsForm['cust_ser27']; ?>" size="20" maxlength="100" />
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 2:</label>
                                <br /><br />
								<label>Please idenitfy the key Product that you currently provide to your customer in order of priority.</label>
                                <br /><br />
								<label>Product 1:</label> <input type="text" name="cust_Pro01" value="<?php echo $row_rsForm['cust_Pro01']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_Pro02" value="<?php echo $row_rsForm['cust_Pro02']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to Recover this Product?</label> <input type="text" name="cust_Pro03" value="<?php echo $row_rsForm['cust_Pro03']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label>Product 2:</label> <input type="text" name="cust_Pro04" value="<?php echo $row_rsForm['cust_Pro04']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_Pro05" value="<?php echo $row_rsForm['cust_Pro05']; ?>" size="20" maxlength="100" /><br /><br />
								<label>How Many Employees are required to Recover this Product?</label> <input type="text" name="cust_Pro06" value="<?php echo $row_rsForm['cust_Pro06']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label>Product 3:</label> <input type="text" name="cust_Pro07" value="<?php echo $row_rsForm['cust_Pro07']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_Pro08" value="<?php echo $row_rsForm['cust_Pro08']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to Recover this Product?</label> <input type="text" name="cust_Pro09" value="<?php echo $row_rsForm['cust_Pro09']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label>Product 4:</label> <input type="text" name="cust_Pro10" value="<?php echo $row_rsForm['cust_Pro10']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_Pro11" value="<?php echo $row_rsForm['cust_Pro11']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to Recover this Product?</label> <input type="text" name="cust_Pro12" value="<?php echo $row_rsForm['cust_Pro12']; ?>" size="20" maxlength="100" />
                                <br /><br />
								<label>Product 5:</label> <input type="text" name="cust_Pro13" value="<?php echo $row_rsForm['cust_Pro13']; ?>" size="20" maxlength="100" /><br />
								<label>Purpose:</label> <input type="text" name="cust_Pro14" value="<?php echo $row_rsForm['cust_Pro14']; ?>" size="20" maxlength="100" /><br />
								<label>How Many Employees are required to Recover this Product?</label> <input type="text" name="cust_Pro15" value="<?php echo $row_rsForm['cust_Pro15']; ?>" size="20" maxlength="100" /><br />
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 3:</label>
                                <br /><br />
								<label>Please prepare a temporary statement for you customers informing them that you have experienced a disaster and are working diligently to ensure you are able to continue to provide Products and Service to them.</label>
                                <br /><br />
								<label>Statement for Customers:</label><br/>
								<textarea name="cust_statement" cols="85" rows="30"><?php echo $row_rsForm['cust_statement']; ?></textarea><br />
							</div>
                            <div class="divQuestionForms">
								<label class="lblQuestion">Question 4: </label>
                                <br /><br />
								<label>In the event you are unable to recover as quickly as you though, you may need to use your relationships with your competitors or partners in the industry. You may need to temporarily use them to complete your orders or send you customer to them.</label>
                                <br /><br />
								<label>Please identify some potential alliances that you are able to make with competitors or partners to use their service in the event of a disaster.</label>
                                <br />
								<label>Potential Alliance Partner Company 1:</label> <input type="text" name="cust_all01" value="<?php echo $row_rsForm['cust_all01']; ?>" size="20" maxlength="100" /><br />
								<label>Contact:</label> <input type="text" name="cust_all02" value="<?php echo $row_rsForm['cust_all02']; ?>" size="20" maxlength="100" /><br />

								<label>Address:</label> <input type="text" name="cust_all03" value="<?php echo $row_rsForm['cust_all03']; ?>" size="20" maxlength="100" /><br />
								<label>Phone:</label> <input type="text" name="cust_all04" value="<?php echo $row_rsForm['cust_all04']; ?>" size="20" maxlength="100" /><br />
								<label>E-Mail:</label> <input type="text" name="cust_all05" value="<?php echo $row_rsForm['cust_all05']; ?>" size="20" maxlength="100" /><br />
								<label>Product or Service They Provide:</label> <input type="text" name="cust_all06" value="<?php echo $row_rsForm['cust_all06']; ?>" size="20" maxlength="100" /><br /><br />
								<label>(you will need to contact them to make sure you are able to use them in the event of a disaster. Once you have an agreement provide some basic guidelines for that agreement)</label>
                                <br /><br />
								<label>Terms of Agreement:</label><br/> 
                                <textarea name="cust_all07" cols="85" rows="30"><?php echo $row_rsForm['cust_all07']; ?></textarea>
                                <br />
								<label>Potential Alliance Partner Company 2:</label> <input type="text" name="cust_all08" value="<?php echo $row_rsForm['cust_all08']; ?>" size="20" maxlength="100" /><br />
								<label>Contact:</label> <input type="text" name="cust_all09" value="<?php echo $row_rsForm['cust_all09']; ?>" size="20" maxlength="100" /><br />
								<label>Address:</label> <input type="text" name="cust_all10" value="<?php echo $row_rsForm['cust_all10']; ?>" size="20" maxlength="100" /><br />
								<label>Phone:</label> <input type="text" name="cust_all11" value="<?php echo $row_rsForm['cust_all11']; ?>" size="20" maxlength="100" /><br />
								<label>E-Mail:</label> <input type="text" name="cust_all12" value="<?php echo $row_rsForm['cust_all12']; ?>" size="20" maxlength="100" /><br />
								<label>Product or Service They Provide:</label> <input type="text" name="cust_all13" value="<?php echo $row_rsForm['cust_all13']; ?>" size="20" maxlength="100" /><br /><br />
								<label>(you will need to contact them to make sure you are able to use them in the event of a disaster. Once you have an agreement provide some basic guidelines for that agreement)</label>
                                <br /><br />
								<label>Terms of Agreement:</label><br/> 
                                <textarea name="cust_all14" cols="85" rows="30"><?php echo $row_rsForm['cust_all14']; ?></textarea>
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
