<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/BasicTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php

$strHolderS = $_GET['s'];//holds the start of the URL types
 
//checks if there is anything in strHolderS
if ($strHolderS == "")
	$strHolderS = $_POST['hfs'];

		if($strHolderS == "1")  							
			echo "Solutions - Basic Edition - Continuity Inc. - Disaster Recovery Solutions";
		else if($strHolderS == "2")
			echo "Solutions - Standard Edition - Continuity Inc. - Disaster Recovery Solutions";
		else if($strHolderS == "3") 
			echo "Solutions - Enterprise Edition - Continuity Inc. - Disaster Recovery Solutions";?></title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" type="text/css" href="CSS/MasterCSS.css" media="screen" />
<script src="javascript/MainJS.js" type="text/javascript"></script>
<?php require_once('PurePHP/LoginControl.php');?>
<?php require_once('Connections/conContinuty.php'); ?>
<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->

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
                    <a href="index.php"><img src="images/logo1.jpg" alt="Logo" width="265" height="55" /></a>
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
						 <!-- InstanceBeginEditable name="hiddenAreaForLogIn" --> <input type="hidden" name="hfs" value="<?php echo $strHolderS; ?>" /><!-- InstanceEndEditable -->
                   
                    	<a href="index.php" class="aHeaderFooterLinks">Home &nbsp;&nbsp;| </a>                  
                    	<a href="Contact.php?section=Contact&Footer=1" class="aHeaderFooterLinks">Contact Us &nbsp;&nbsp;| </a>
                    	<a href="FAQ.php?section=FAQ&Footer=1" class="aHeaderFooterLinks">FAQ's &nbsp;&nbsp;| </a>
                       
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
								?><!-- InstanceBeginEditable name="hiddenAreaForLogOut" --><?php $strLogOutString = $strLogOutString."&s=".$strHolderS; ?><!-- InstanceEndEditable --><?php
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
                        	<a href="AboutUs.php?section=About&Footer=1" onClick="changeImage('imgAboutUs',<?php echo "'".$strFilePath."images"; ?>/aboutusdown.jpg')" onMouseOver="<?php if($_GET['section'] == "About" || $_POST['hfSection'] == "About")  echo ""; else echo "changeImage('imgAboutUs','".$strFilePath."images/aboutusover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "About" || $_POST['hfSection'] == "About") echo ""; else echo "changeImage('imgAboutUs','".$strFilePath."images/aboutusout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "About" || $_POST['hfSection'] == "About") echo "src=\"".$strFilePath."images/aboutusdown.jpg\""; else echo "src=\"".$strFilePath."images/aboutusout.jpg\""; ?> alt="About Us" width="128" height="49" id="imgAboutUs" /></a>
                        </li>
						<li>
                        	<a href="WhyPlan.php?section=Plan&Footer=1" onClick="changeImage('imgPlan',<?php echo "'".$strFilePath."images"; ?>/whyplandown.jpg')" onMouseOver="<?php if($_GET['section'] == "Plan" || $_POST['hfSection'] == "Plan") echo ""; else echo "changeImage('imgPlan','".$strFilePath."images/whyplanover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Plan" || $_POST['hfSection'] == "Plan") echo ""; else echo "changeImage('imgPlan','".$strFilePath."images/whyplanout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "Plan" || $_POST['hfSection'] == "Plan") echo "src=\"".$strFilePath."images/whyplandown.jpg\""; else echo "src=\"".$strFilePath."images/whyplanout.jpg\""; ?> alt="Why Plan?" id="imgPlan" /></a>
							<ul class="navigation-2">
                            	<li><a href="Partners.php?section=Partners&Footer=1#People">Protect Your People</a></li>
                            	<li><a href="Partners.php?section=Partners&Footer=1#Power">Protect Your Power</a></li>
                            	<li><a href="Partners.php?section=Partners&Footer=1#Information">Protect Your Information</a></li>
								<li><a href="Partners.php?section=Partners&Footer=1#Space">Protect Your Space</a></li>
                                <li><a href="AssessYourBusiness.php?section=Plan&Footer=1">Assess Your Business</a></li>
							</ul>
						</li>
						<li>
                        	<a href="Solutions.php?section=Solutions&both=1" onClick="changeImage('imgSolutions',<?php echo "'".$strFilePath."images"; ?>/solutionsdown.jpg')" onMouseOver="<?php if($_GET['section'] == "Solutions" || $_POST['hfSection'] == "Solutions") echo ""; else echo "changeImage('imgSolutions','".$strFilePath."images/solutionsover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Solutions" || $_POST['hfSection'] == "Solutions") echo ""; else echo "changeImage('imgSolutions','".$strFilePath."images/solutionsout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "Solutions" || $_POST['hfSection'] == "Solutions") echo "src=\"".$strFilePath."images/solutionsdown.jpg\""; else echo "src=\"".$strFilePath."images/solutionsout.jpg\""; ?> alt="Our Solutions" id="imgSolutions" /></a>
                            <ul class="navigation-2">
                            	<li><a href="Home.php?section=Solutions">Our Home Solutions</a></li>
                                <li><a href="Solutions.php?section=Solutions">Our Business Solutions</a></li>
                            </ul>
                        </li>
                        <li>
                        	<a href="Services.php?section=Services&Footer=1" onClick="changeImage('imgServices',<?php echo "'".$strFilePath."images"; ?>/servicesdown.jpg')" onMouseOver="<?php if($_GET['section'] == "Services" || $_POST['hfSection'] == "Services") echo ""; else echo "changeImage('imgServices','".$strFilePath."images/servicesover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Services" || $_POST['hfSection'] == "Services") echo ""; else echo "changeImage('imgServices','".$strFilePath."images/servicesout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "Services" || $_POST['hfSection'] == "Services") echo "src=\"".$strFilePath."images/servicesdown.jpg\""; else echo "src=\"".$strFilePath."images/servicesout.jpg\""; ?> alt="Our Services" id="imgServices" /></a>
                            <ul class="navigation-2">
                                <li><a href="FourRs.php?section=Services&Footer=1&r=1">Reduce The Risk</a></li>
                                <li><a href="FourRs.php?section=Services&Footer=1&r=2">Respond To A Disaster</a></li>
                                <li><a href="FourRs.php?section=Services&Footer=1&r=3">Recover After A Loss</a></li>
                                <li><a href="FourRs.php?section=Services&Footer=1&r=4">Restore Your Business</a></li>
                            </ul>
                        </li>
						<li>
                        	<a href="Partners.php?section=Partners&Footer=1" onClick="changeImage('imgPartners',<?php echo "'".$strFilePath."images"; ?>/partnersdown.jpg')" onMouseOver="<?php if($_GET['section'] == "Partners" || $_POST['hfSection'] == "Partners") echo ""; else echo "changeImage('imgPartners','".$strFilePath."images/partnersover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Partners" || $_POST['hfSection'] == "Partners") echo ""; else echo "changeImage('imgPartners','".$strFilePath."images/partnersout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "Partners" || $_POST['hfSection'] == "Partners") echo "src=\"".$strFilePath."images/partnersdown.jpg\""; else echo "src=\"".$strFilePath."images/partnersout.jpg\""; ?> alt="Our Partners" id="imgPartners" /></a>                        </li>
                        <li>
                        	<a href="Media.php?section=Media&Footer=1" onClick="changeImage('imgMedia',<?php echo "'".$strFilePath."images"; ?>/mediadown.jpg')" onMouseOver="<?php if($_GET['section'] == "Media" || $_POST['hfSection'] == "Media") echo ""; else echo "changeImage('imgMedia','".$strFilePath."images/mediaover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Media" || $_POST['hfSection'] == "Media") echo ""; else echo "changeImage('imgMedia','".$strFilePath."images/mediaout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "Media" || $_POST['hfSection'] == "Media") echo "src=\"".$strFilePath."images/mediadown.jpg\""; else echo "src=\"".$strFilePath."images/mediaout.jpg\""; ?> alt="Media" id="imgMedia" /></a>
                       	</li>
                        <li>
                        	<a href="Store.php?section=Store&Footer=1" onClick="changeImage('imgStore',<?php echo "'".$strFilePath."images"; ?>/ourstoreoverdown.jpg')" onMouseOver="<?php if($_GET['section'] == "Store" || $_POST['hfSection'] == "Store") echo ""; else echo "changeImage('imgStore','".$strFilePath."images/ourstoreover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Store" || $_POST['hfSection'] == "Store") echo ""; else echo "changeImage('imgStore','".$strFilePath."images/ourstoreout.jpg');"; ?>">
                           	<img <?php if($_GET['section'] == "Store" || $_POST['hfSection'] == "Store") echo "src=\"".$strFilePath."images/ourstoreoverdown.jpg\""; else  echo "src=\"".$strFilePath."images/ourstoreout.jpg\""; ?> alt="Our Store" width="128" height="49" id="imgStore" /></a>                    </li>
                        <li>
                        	<a href="RSA/Offer.php?section=Access&Footer=1&SubFolder=1" onMouseOver="<?php if($_GET['section'] == "Access" || $_POST['hfSection'] == "Access") echo ""; else echo "changeImage('imgAccess','".$strFilePath."images/RSAtopover.jpg');"; ?>" onMouseOut="<?php if($_GET['section'] == "Access" || $_POST['hfSection'] == "Access") echo ""; else echo "changeImage('imgAccess','".$strFilePath."images/RSAtopout.jpg');"; ?>">
                           	<img alt="Exclusive Access" name="imgAccess" id="imgAccess" <?php if($_GET['section'] == "Access" || $_POST['hfSection'] == "Access") echo "src=\"".$strFilePath."images/RSAtopover.jpg\""; else echo "src=\"".$strFilePath."images/RSAtopout.jpg\""; ?> /></a>                        </li>
					</ul>
                  		<div class="customFooter"></div>
                    </div>
                <!-- end of Header Footer -->
              </div><!-- end of Header Container -->
              <h1><!-- InstanceBeginEditable name="h1Title" --><?php
		if($strHolderS == "1")  							
			echo "Solutions - Basic Edition";
		else if($strHolderS == "2")
			echo "Solutions - Standard Edition";
		else if($strHolderS == "3") 
			echo "Solutions - Enterprise Edition";?><!-- InstanceEndEditable --></h1>
          	</div><!-- end of Header -->
            <div class="customContent" id="divBasicContent">
                <!-- InstanceBeginEditable name="BasicContent" -->
                <div class="lblBlackBackgroundColor borderBottom20">
					<?php //Section Picture 1
					if($strHolderS == "1")  							
						echo "<img src=\"images/BETOP.jpg\" alt=\"Business Continuity Plans: Basic Edition\" />";
					else if($strHolderS == "2")
    		            echo "<img src=\"images/SETOP.jpg\" alt=\"Business Continuity Plans: Standard Edition\" />";
					else if($strHolderS == "3") 
                 		echo "<img src=\"images/EETOP.jpg\" alt=\"Business Continuity Plans: Enterprise Edition\" />";?>
                    <a href="FourRs.php?section=Services&Footer=1&r=1" onmouseout="changeImage('imgReduce','images/Reduce3out.jpg')" onmouseover="changeImage('imgReduce','images/Reduce3over.jpg')"><img src="images/Reduce3out.jpg" alt="Reduce The Risk" id="imgReduce" /></a>
                                                <a href="FourRs.php?section=Services&Footer=1&r=2" onmouseout="changeImage('imgRespond','images/Respond3out.jpg')" onmouseover="changeImage('imgRespond','images/Respond3over.jpg')"><img src="images/Respond3out.jpg" alt="Respond To A Disaster" id="imgRespond" /></a>
                                                <a href="FourRs.php?section=Services&Footer=1&r=3" onmouseout="changeImage('imgRecover','images/Recover3out.jpg')" onmouseover="changeImage('imgRecover','images/Recover3over.jpg')"><img src="images/Recover3out.jpg" alt="Recover After A Disaster" id="imgRecover" /></a>
                                                <a href="FourRs.php?section=Services&Footer=1&r=4" onmouseout="changeImage('imgRestore','images/Restore3out.jpg')" onmouseover="changeImage('imgRestore','images/Restore3over.jpg')"><img src="images/Restore3out.jpg" alt="Restore Your Business As Usual" id="imgRestore" /></a>
                </div>
                <div id="divSolThreeBody">
                    <div class="borderBottom20" align="left">
                        <div>
                            <?php //Section Picture 1
                                if($strHolderS == "1")  							
                                    echo "<br /><br />
                                    <label><span class=\"lblBasicColor lblFontSize24 lblFontBold\">Our Basic Edition Program Outline</span>
                                    <br /><br />
                                    Our programs were developed with the Small-Medium sized business owner in mind, with that said it is important for every business owner to understand the need to have a plan in place. Our Basic Edition is design to follow the 4 Areas of Business Continuity. Reduce The Risk, Respond To A Disaster, Recovery After A Disaster, and Restore You business back to normal as quickly as possible. Don't Wait for a Disaster to strike your business, put a plan in place to today to ensure your business is prepared for anything!</label>";
                                else if($strHolderS == "2")
                                    echo "<br /><br />
                                    <label><span class=\"lblStandardColor lblFontSize24 lblFontBold\">Our Standard Edition Program Outline</span>
                                    <br /><br />
                                    Our programs were developed with the Small-Medium sized business owner in mind, with that said it is important for every business owner to understand the need to have a plan in place. Our Standard Edition is design to follow the 4 Areas of Business Continuity. Reduce The Risk, Respond To A Disaster, Recovery After A Disaster, and Restore You business back to normal as quickly as possible. Don't Wait for a Disaster to strike your business, put a plan in place to today to ensure your business is prepared for anything!
                                    <br/><br/>
                                    In addition to the features of our Basic Edition we have added some unique features as a bonus for our Standard Edition Users. We added 3 new unique areas to our Standard Edition Which you will see highlighted below with a Red Star.</span></p>
                                    <br/><br/>
                                    <span class=\"lblFontSize10\">New Program Features: <span class=\"lblStandardColor\">Risk Assessment &amp; Analysis, Business Impact Analysis &amp; Our Insurnace Inventory</span></span></label>";
                                else if($strHolderS == "3") 
                                    echo "<br /><br />
                                    <label><span class=\"lblEnterColor lblFontSize24 lblFontBold\">Our Enterprise Edition  Outline</span> 
                                    <br /><br />
                                    This programs was developed with the Small-Medium sized business owner in mind, with that said it is important for every business owner to understand the need to have a plan in place. Our <span class=\"lblEnterColor lblFontBold\">Enterprise Edition</span> is design to follow the 4 Areas of Business Continuity. Reduce The Risk, Respond To A Disaster, Recovery After A Disaster, and Restore You business back to normal as quickly as possible. Don't Wait for a Disaster to strike your business, put a plan in place to today to ensure your business is prepared for anything!</label>";?>
                            <br /><br />
                        </div>
                        <div class="divHomeWhyPlan lblFontSize10 divFourRs">
                            <a href="FourRs.php?section=Services&Footer=1&r=1"><img src="images/PIC1.jpg" alt="" /><img src="images/BarR1.jpg" alt="Reduce The Risk" /></a>
                            <br />
                            <label><span class="lblBasicColor lblFontBold">Section 1 of our Basic Program is Reduce.</span> This section will help you organize and understand your business needs so you will ultimatly reduce the impact a disaster could have.</label>
                            <br /><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblBasicColor">Project Scope &amp; Objectives</label><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblBasicColor">Employee &amp; Emergency Contacts</label><br />
                            <?php if($strHolderS == "2")
                                    echo "<img src=\"images/dot2.jpg\" alt=\"Super Dot\" />
                                    <label class=\"lblFontBold lblStandardColor\">Risk Assessment &amp; Analysis</label><br />
                                    <img src=\"images/dot2.jpg\" alt=\"Super Dot\" />
                                    <label class=\"lblFontBold lblStandardColor\">Business Impact Analysis</label><br />"; ?>
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblBasicColor">Business Continuity Plans</label><br />
                            <?php if($strHolderS != "2")
                                    echo "<img src=\"images/dot1.jpg\" alt=\"Dot\" />
                                    <label class=\"lblFontBold lblBasicColor\">Business Unit Descriptions</label><br />"; ?>
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblBasicColor">Alternate Locations &amp; Suppliers</label><br />
                            <?php if($strHolderS != "2")
                                    echo "<img src=\"images/dot1.jpg\" alt=\"Dot\" />
                                    <label class=\"lblFontBold lblBasicColor\">Other Continuity Operations</label>"; ?>
                        </div>
                        <div class="divHomeWhyPlan lblFontSize10 divFourRs">
                            <a href="FourRs.php?section=Services&Footer=1&r=2"><img src="images/PIC2.jpg" alt="" /><img src="images/BarR2.jpg" alt="Respond To An Event" /></a>
                            <br />
                            <label><span class="lblStandardColor lblFontBold">Section 2 of our Basic Program is Respond.</span> This section will provide your business with the necessary emergency response actions to help your business survive during an event.</label>
                            <br /><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblStandardColor">Emergency Response Guidelines</label><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblStandardColor">Disaster Declaration Guide</label><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblStandardColor">Immediate Response Plans</label><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblStandardColor">First-Aid &amp; CPR Guidelines</label>
                        </div>
                        <div class="divHomeWhyPlan lblFontSize10 divFourRs">
                            <a href="FourRs.php?section=Services&Footer=1&r=3"><img src="images/PIC3.jpg" alt="" /><img src="images/BarR3.jpg" alt="Recover After An Event" /></a>
                            <br />
                            <label><span class="lblContinuityColor lblFontBold">Section 3 of our Basic Program is Recover.</span> This section will provide your business with a organizational Disaster Recovery Plan. Each team will have specific actions to perform after an event.</label>
                            <br /><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblContinuityColor">Disaster Management Team</label><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblContinuityColor">Damage Assessment Team</label><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblContinuityColor">Information Technology Team</label><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblContinuityColor">Adminsitration Recovery Team</label><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblContinuityColor">Business Unit Recovery Team</label><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblContinuityColor">Business Support Team</label>
                        </div>
                        <div class="divHomeWhyPlan lblFontSize10 divNoBoarder divFourRs">
                            <a href="FourRs.php?section=Services&Footer=1&r=4"><img src="images/PIC4.jpg" alt="" /><img src="images/BarR4.jpg" alt="Restore Your Business" /></a>
                            <br />
                            <label><span class="lblEnterColor lblFontBold">Section 4 of our Basic Program is Restore.</span> This section will provide your business with the information and resources to assist with the restoration of your business. Our Continuity Connections will be made available to you.</label>
                            <br /><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblEnterColor">Disaster Restoration Plans</label><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblEnterColor">Continuity Connections</label><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblEnterColor">ServiceMaster Information</label><br />
                            <img src="images/dot1.jpg" alt="Dot" />
                            <label class="lblFontBold lblEnterColor">Other Restoration Facts</label><br />
                            <?php if($strHolderS == "2")
                                    echo "<img src=\"images/dot2.jpg\" alt=\"Super Dot\" />
                            <label class=\"lblFontBold lblStandardColor\">Insurance Inventory</label>"; ?>
                        </div>
                        <div class="customFooter"></div>
                        <div align="right">
                            <div class="divHomeWhyPlan divHomeWhyPlanLinks">
                                <a href="FourRs.php?section=Services&Footer=1&r=1" onmouseout="changeImage('imgMore1','images/More1out.jpg')" onmouseover="changeImage('imgMore1','images/More1over.jpg')"><img src="images/More1out.jpg" alt="Find Out More" id="imgMore1" width="247" /></a>
                            </div>
                            <div class="divHomeWhyPlan divHomeWhyPlanLinks">
                                <a href="FourRs.php?section=Services&Footer=1&r=2" onmouseout="changeImage('imgMore2','images/More2out.jpg')" onmouseover="changeImage('imgMore2','images/More2over.jpg')"><img src="images/More2out.jpg" alt="Find Out More" id="imgMore2" width="247" /></a>
                            </div>
                            <div class="divHomeWhyPlan divHomeWhyPlanLinks">
                                <a href="FourRs.php?section=Services&Footer=1&r=3" onmouseout="changeImage('imgMore3','images/More3out.jpg')" onmouseover="changeImage('imgMore3','images/More3over.jpg')"><img src="images/More3out.jpg" alt="Find Out More" id="imgMore3" width="247" /></a>
                            </div>
                            <div class="divHomeWhyPlan divHomeWhyPlanLinks">
                                <a href="FourRs.php?section=Services&Footer=1&r=4" onmouseout="changeImage('imgMore4','images/More4out.jpg')" onmouseover="changeImage('imgMore4','images/More4over.jpg')"><img src="images/More4out.jpg" alt="Find Out More" id="imgMore4" width="247" /></a>
                            </div>
                            <div class="customFooter" id="divHomeWhyPlanFooter"></div>
                        </div>
                    </div>
                    <?php if($strHolderS == "2")
                        echo "<div class=\"borderBottom20\" align=\"left\">
                            <label class=\"lblStandardColor lblFontSize24\">Additional Standard Edition Program Feature
                            <br/><br/>
                            Taking An Insurance Inventory</label>
                            <div class=\"customContainer\" id=\"divThreeSsIIContainer\">
                                <div class=\"customContent\" id=\"divThreeSsIIContent\">
                                    <img src=\"images/II1.jpg\" alt=\"New Insurance Inventory\" />
                                </div>
                                <div class=\"customNavigation\" id=\"divThreeSsIINavigation\">
                                    <label><span class=\"lblFontBold lblStandardColor\">A New Feature of Our Standard Edition:</span>
                                    <br/>
                                    In the event of a disaster or disruption to your business, you may or may not have incurred some damage or even a severe loss.
                                    <br/><br/>
                                    The replacement and recovery process can be very tedious and stressful, by creating an up to date inventory of your premises and assets, you will create a stress free replacement process, that will allow you to focus on other areas of recovery.
                                    <br/><br/>
                                    Our Insurance Inventory Program will provide your business with an accurate Photographic database of your  assets, that will ensure the replacement &amp; recovery process  is accurate, efficient and very effective. Your Photo's and description of those items will be store on our database with your Continuity Plans.</label>
                                    <br/><br/>
                                    <img src=\"images/dot2.jpg\" alt=\"Super Dot\" />
                                    <label class=\"lblFontBold lblStandardColor\">Take Photographs Of Your Office Building and Items that dwell within it.</label><br/>
                                    <img src=\"images/dot2.jpg\" alt=\"Super Dot\" />
                                    <label class=\"lblFontBold lblStandardColor\">Upload Photos to your Continuity Standard Edition Program.</label><br/>
                                    <img src=\"images/dot2.jpg\" alt=\"Super Dot\" />
                                    <label class=\"lblFontBold lblStandardColor\">Save, Store, Access and update your Insurance inventory as needed with your access.</label>
                                </div>
                                <div class=\"customFooter\" id=\"divThreeSsIIFooter\"></div>
                            </div>
                        </div>"; ?>                    
                    <div class="borderBottom20" align="left">
                        <?php $strColor = "lblBasicColor";//Holds the Current Solution Color
                        $strEdition = "Basic";//Holds the Edition the uses has selected
                        
                        //checks whick version the users is going to used
                        if($strHolderS == "3")
                        {
                            $strEdition = "Enterprise";
                            $strColor = "lblEnterColor";
                        }//end of if
                        else if ($strHolderS == "2")
                        {
                            $strEdition = "Standard";
                            $strColor = "lblStandardColor";
                        }//end of else if
                        
                        if($strHolderS == "3")
                        { ?>
                             <div class="divPageTitle" align="left">
                                <label class="lblPageTitle">How Does Our Enterprise Edition Work?</label>
                             </div>
                             <label class="lblFontSize14">A team of certified professionals will work with your company to ensure you are able to complete the required areas of Business Continuity & Disaster Recovery.</label>
                             <div class="customContainer divThreeSsBasicStdContainer">
                                <div class="customContent divThreeSsBasicStdContent">
                                    <img src="images/Step7.jpg" alt="" />
                                </div>
                                <div class="customNavigation divThreeSsBasicStdNavigation">
                                    <label class="lblEnterColor lblFontSize18">Program Summary &amp; Overview</label>
                                    <div class="divStepPoint">
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Project Scope &amp; Objectives.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Checklist Development.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Crisis Communications.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Testing &amp; Exercise.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Emergency Response Plans.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Maintenance &amp; Updating.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Disaster Recovery.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Implementation of Plans.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Business Continuity Plans.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Scenario Development.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Training &amp; Awareness.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Coordination with External Agencies.</label>
                                    </div>
                                </div>
                                <div class="customFooter divThreeSsBasicStdFooter"></div>
                                <div class="customContent divThreeSsBasicStdContent">
                                    <img src="images/Step8.jpg" alt="" />
                                </div>
                                <div class="customNavigation divThreeSsBasicStdNavigation">
                                    <label class="lblEnterColor lblFontSize18">Plan Management Framework &amp; Tools</label>
                                    <div class="divStepPoint">
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Project Plan For Development of Continuity Plans.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Introductory Training Materials for Plan Supporters.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Checklist for Risk Assessment.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Readiness Benchmark Assessment.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Tools For Implementation.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Example Communication Materials.</label>
                                    </div>
                                </div>
                                <div class="customFooter divThreeSsBasicStdFooter"></div>
                                <div class="customContent divThreeSsBasicStdContent">
                                    <img src="images/Step9.jpg" alt="" />
                                </div>
                                <div class="customNavigation divThreeSsBasicStdNavigation">
                                    <label class="lblEnterColor lblFontSize18">2 - 3 Days of Training &amp; Consulting Work With Trained Professionals</label>
                                    <div class="divStepPoint">
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Help with Defining Continuity Planning Scope and Strategy.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Assistance To understand Plan Steps and Customize it Appropriatly to your Business.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Identification of Needed Inputs, Gaps, and/or Support Issues.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Help To Identify Prevention and Mitigation Stratigies.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Assistance with Development of Detailed Implementation Plan.</label>
                                    </div>
                                </div>
                                <div class="customFooter divThreeSsBasicStdFooter"></div>
                             </div>
                        <?php }//end of if
                        else
                        {?>
                             <div class="divPageTitle" align="left">
                                <label class="lblPageTitle">How Does Our Web-Based Program Work?</label>
                             </div>
                             <label class="lblFontSize14">Now that you are aware of what is included in our <?php echo $strEdition; ?> Edition Program, the following will outline the functionallity and overall process that our Web-Based planning program can provide you with in terms of access, storage, maintenance, mutilpul copies and other unique functions.</label>
                             <div class="customContainer divThreeSsBasicStdContainer">
                                <div class="customContent divThreeSsBasicStdContent">
                                    <img src="images/Step1.jpg" alt="" />
                                </div>
                                <div class="customNavigation divThreeSsBasicStdNavigation">
                                    <label class="lblContinuityColor lblFontSize18">Step 1 - Complete Our On-Line Planning Forms Using Our New Wizard Navigation Process.</label>
                                    <div class="divStepPoint">
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Access Your Plans Through The Internet and begin Inputting The Required Company Information</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Follow The Step by Step Continuity Planning Wizard Program</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Save Progress At Any Point and Pick Up Planning Where You last Stoped</label>
                                    </div>
                                </div>
                                <div class="customFooter divThreeSsBasicStdFooter"></div>
                                <div class="customContent divThreeSsBasicStdContent">
                                    <img src="images/Step2.jpg" alt="" />
                                </div>
                                <div class="customNavigation divThreeSsBasicStdNavigation">
                                    <label class="lblContinuityColor lblFontSize18">Step 2 - Once The Forms Are Completed, Print Various PDF Copies for Internal Office Use.</label>
                                    <div class="divStepPoint">
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">When Forms Are Completed You will be able to generate a PDF version to print mutipul copies for office use.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">In addition to the on-line version of your plans, you can e-mail, print and access additional copies during an event.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">It is important to have various copies of your plans stored in different locations, our printing function will help.</label>
                                    </div>
                                </div>
                                <div class="customFooter divThreeSsBasicStdFooter"></div>
                                <div class="customContent divThreeSsBasicStdContent">
                                    <img src="images/Step3.jpg" alt="" />
                                </div>
                                <div class="customNavigation divThreeSsBasicStdNavigation">
                                    <label class="lblContinuityColor lblFontSize18">Step 3 - Ensure That your Plan in Continually maintaned and updated as change occures.</label>
                                    <div class="divStepPoint">
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Update Your Plans as you experience change in your business.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">By renewing your program with Continuity Inc. you will be able to continually update your plans.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Regular Review and Tests can be completed on your plans as you grow in business.</label>
                                    </div>
                                </div>
                                <div class="customFooter divThreeSsBasicStdFooter"></div>
                                <div class="customContent divThreeSsBasicStdContent">
                                    <img src="images/Step4.jpg" alt="" />
                                </div>
                                <div class="customNavigation divThreeSsBasicStdNavigation">
                                    <label class="lblContinuityColor lblFontSize18">Step 4 - Access Your Continuity Plans Anytime, Anywhere with remote access.</label>
                                    <div class="divStepPoint">
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">During A Disaster you may not be able to access the Hard Copy of your Continuity Plans.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                        <label class="<?php echo $strColor; ?>">Access Your Plans on-line from any remote terminal that has internet access.</label><br />
                                        <img src="images/dot1.jpg" alt="Dot" />
                                       <label class="<?php echo $strColor; ?>">Use Your Mobile Device to access essential information through read only files on your on-line program.</label>
                                    </div>
                                </div>
                                <div class="customFooter divThreeSsBasicStdFooter"></div>
                             </div>
                        <?php }//end of else ?>
                    </div>
                    <div id="divSolThreeFooter">
                        <?php
                        if($strHolderS == "1")  							
                            echo "<a href=\"Solutions.php?section=Solutions#Basic\" onmouseout=\"changeImage('imgBEDemo','images/Step5.jpg')\" onmouseover=\"changeImage('imgBEDemo','images/Step5over.jpg')\"><img src=\"images/Step5.jpg\" alt=\"View Our Program Demo\" id=\"imgBEDemo\" /></a>
                            <img src=\"images/BEBOTTOM.jpg\" alt=\"Why Use Web-Based Continuity Planning?\" />";
                        else if($strHolderS == "2")
                            echo "<a href=\"Solutions.php?section=Solutions#Standard\" onmouseout=\"changeImage('imgSEDemo','images/Step6.jpg')\" onmouseover=\"changeImage('imgSEDemo','images/Step6over.jpg')\"><img src=\"images/Step6.jpg\" alt=\"View Our Program Demo\" id=\"imgSEDemo\"/></a>
                            <img src=\"images/SEBOTTOM.jpg\" alt=\"Why Use Web-Based Continuity Planning?\" />";
                        else if($strHolderS == "3") 
                            echo "<a href=\"Contact.php?section=Contact&amp;Footer=1\" onmouseout=\"changeImage('imgEEContact','images/ENT1out.jpg')\" onmouseover=\"changeImage('imgEEContact','images/ENT1over.jpg',1)\"><img src=\"images/ENT1out.jpg\" id=\"imgEEContact\" /></a>
                            <a href=\"AssessYourBusiness.php?section=Plan&amp;Footer=1\" onmouseout=\"changeImage('imgEEAssess','images/ENT2out.jpg')\" onmouseover=\"changeImage('imgEEAssess','images/ENT2over.jpg',1)\"><img src=\"images/ENT2out.jpg\" id=\"imgEEAssess\" /></a>
                            <a href=\"Solutions.php?section=Solutions\" onmouseout=\"changeImage('imgEESol','images/ENT3out.jpg')\" onmouseover=\"changeImage('imgEESol','images/ENT3over.jpg',1)\"><img src=\"images/ENT3out.jpg\" id=\"imgEESol\" /></a>";?>                        
                    </div>
                </div>
                <!-- InstanceEndEditable -->
            </div>
            <?php if ($_GET['Nav'] == "1" || $_POST['hfNav'] == "1")			
			{ ?>
            <div class="customNavigation" id="divBasicNavigation" align="right">
            </div>
            <?php }//end of if ?>
            <div class="customFooter" id="divBasicFooter" align="left">
			  <!-- InstanceBeginEditable name="BasicFooter" -->
			  
			  <!-- InstanceEndEditable -->
              <?php if ($_GET['Footer'] == "1" || $_POST['hfFooter'] == "1")
				{ ?>
		        <div class="divFooterImagesLinks">
                	<a href="index.php?section=Basic" onmouseout="changeImage('imgFooterBasic',<?php echo "'".$strFilePath."images"; ?>/PrgBEout.jpg')" onmouseover="changeImage('imgFooterBasic',<?php echo "'".$strFilePath."images"; ?>/PrgBEover.jpg')"><img src="images/PrgBEout.jpg" alt="Our Basic Edition" width="215" height="75" id="imgFooterBasic" /></a>
                    <a href="index.php?section=Standard" onmouseout="changeImage('imgFooterStandard',<?php echo "'".$strFilePath."images"; ?>/PrgSEout.jpg')" onmouseover="changeImage('imgFooterStandard',<?php echo "'".$strFilePath."images"; ?>/PrgSEover.jpg')"><img src="images/PrgSEout.jpg" alt="Our Standard Edition" width="215" height="75" id="imgFooterStandard" /></a>
                    <a href="index.php?section=Enterprise" onmouseout="changeImage('imgFooterEnterprise',<?php echo "'".$strFilePath."images"; ?>/PrgEEout.jpg')" onmouseover="changeImage('imgFooterEnterprise',<?php echo "'".$strFilePath."images"; ?>/PrgEEover.jpg')"><img src="images/PrgEEout.jpg" alt="Our Enterprise Edition" width="215" height="75" id="imgFooterEnterprise" /></a>
               	</div>
                <?php }//end of if ?>
                <div class="divFooterLinks">
                    <a href="index.php" class="aHeaderFooterLinks">Home &nbsp;&nbsp;| </a>                  
                    <a href="Contact.php?section=Contact&Footer=1" class="aHeaderFooterLinks">Contact Us &nbsp;&nbsp;| </a>
                    <a href="FAQ.php?section=FAQ&Footer=1" class="aHeaderFooterLinks">FAQ's &nbsp;&nbsp;| </a>
                    <a href="Terms.php?section=Terms&Footer=1" class="aHeaderFooterLinks">Terms &amp; Conditions &nbsp;&nbsp;| </a>
                    <a href="Privacy.php?section=Privacy&Footer=1" class="aHeaderFooterLinks">Privacy Policy</a>
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
