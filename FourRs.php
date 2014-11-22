<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/BasicTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php 

$strHolderR = $_GET['r'];//holds the start of the URL types
 
//checks if there is anything in strHolderS
if ($strHolderR == "")
	$strHolderR = $_POST['hfr'];

//Title for the Section 
		if($strHolderR == "1")  							
			echo "Services - Reduce The Risk - Continuity Inc. - Disaster Recovery Solutions";
		else if($strHolderR == "2")
			echo "Services - Respond To A Disaster - Continuity Inc. - Disaster Recovery Solutions";
		else if($strHolderR == "3") 
			echo "Services - Recover From A Disaster - Continuity Inc. - Disaster Recovery Solutions";
		if($strHolderR == "4") 
			 echo "Services - Restore Your Business - Continuity Inc. - Disaster Recovery Solutions";?></title>
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
						 <!-- InstanceBeginEditable name="hiddenAreaForLogIn" --><input type="hidden" name="hfr" value="<?php echo $strHolderR; ?>" /><!-- InstanceEndEditable -->
                   
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
								?><!-- InstanceBeginEditable name="hiddenAreaForLogOut" --><?php $strLogOutString = $strLogOutString."&r=".$strHolderR; ?><!-- InstanceEndEditable --><?php
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
              <h1><!-- InstanceBeginEditable name="h1Title" -->
		              <?php //H1 for the Section 
						 	if($strHolderR == "1")  							
                           		echo "Services - Reduce The Risk";
                            else if($strHolderR == "2")
                            	echo "Services - Respond To A Disaster";
                            else if($strHolderR == "3") 
                            	echo "Services - Recover From A Disaster";
                            if($strHolderR == "4") 
                            	echo "Services - Restore Your Business";?><!-- InstanceEndEditable --></h1>
          	</div><!-- end of Header -->
            <div class="customContent" id="divBasicContent">
                <!-- InstanceBeginEditable name="BasicContent" -->
                <div align="left">
				<?php //Section Picture 1
					if($strHolderR == "1")  							
						echo "<img src=\"images/reduceIMG1.jpg\" alt=\"Reduce The Risk Getting To Know Your Stuff ...\" width=\"894\" height=\"232\" />";
					else if($strHolderR == "2")
    		            echo "<img src=\"images/respondIMG1.jpg\" alt=\"Respond To A Disaster Knowing what to do during a disaster.\" width=\"894\" height=\"232\" />";
					else if($strHolderR == "3") 
                 		echo "<img src=\"images/recoverIMG1.jpg\" alt=\"Recover From A Disaster Knowing what to do after a disaster has occurred.\" width=\"894\" height=\"232\" />";
					if($strHolderR == "4") 
                		 echo "<img src=\"images/restoreIMG1.jpg\" alt=\"Restore Your Business Fast restoration will minimze the down-time.\" width=\"894\" height=\"232\" />";?>
                    </div>
					<div class="customContainer" id="divFourRDesContainer" align="left">
                    	<div class="customContent" id="divFourRDesContent">
						 <?php //Section Picture 2
						if($strHolderR == "1")  							
							echo "<img src=\"images/reduceIMG2.jpg\" alt=\"Step 1: Reduce The Risk\" width=\"246\" height=\"68\" />";
						else if($strHolderR == "2")
							echo "<img src=\"images/respondIMG2.jpg\" alt=\"Step 2: Respond To A Disaster\" width=\"246\" height=\"68\" />";
						else if($strHolderR == "3") 
							echo "<img src=\"images/recoverIMG2.jpg\" alt=\"Step 3: Recover From A Disaster\" width=\"246\" height=\"68\" />";
						if($strHolderR == "4") 
							echo "<img src=\"images/restoreIMG2.jpg\" alt=\"Step 4: Restore Your Business\" width=\"246\" height=\"68\" />";?>
                        </div>
                        <div class="customNavigation" id="divFourRDesNavigation">
                        	<div>
								<label class="lblFontSize9"><?php //Description for the Section
                                if($strHolderR == "1")  							
                                    echo "<span class=\"lblReduceColor lblFontBold\">Reducing the risk</span> will provide your busines with a better understanding on how a disaster could potentially impact your business. By assessing the risk, understand the impacts, and ensure you have accurate insurance coverage, you will be able to reduce the impact a disaster could have on your business. This is the first step on the road to preparing your business, click on the <span class=\"lblNextStepColor\">(Next Step)</span> button to find out what your next step is.";
                                else if($strHolderR == "2")
                                    echo "<span class=\"lblRespondColor lblFontBold\">Responding To A Disaster</span> will provide your busines with a step by step action plan to ensure the safety and evacuation of your people. In the event of a disaster, knowing how to respond cto a disaster situation could potentially reduce the impact the disaster has on your business. By perfroms immeidate response actions you will already have a head start on the recovery process.";
                                else if($strHolderR == "3") 
                                    echo "<span class=\"lblRecoverColor lblFontBold\">Recovering from a disaster</span> will provide your business with a step by step action plan to assess what damages have occurred, and what items were lost in the event. From this action plan you will be able to begin the recover and replacement process of all essential items that were lost. By having a recovery plan you will be able to recover the essential business functions that will allow you to remain in business.";
                                if($strHolderR == "4") 
                                     echo "<span class=\"lblRestoreColor lblFontBold\">Restoring your business</span> back to normal after disaster will provide you and your customers with the protection you need to remain in business. Most companies make the mistake of waiting around for someone else to help them. Businesses need to be pro-active in order to remain in business after a disaster. It is up to every business individually to ensure they are prepared! Don’t wait for a disaster to strike, build a plan to protect your business today!";?></label>
                            </div>
                        </div>
                        <div class="customFooter" id="divFourRDesFooter"></div>
                    </div>
                    <div class="customContainer" id="divFourRVideoContainer" align="left">
                    	<div class="customContent" id="divFourRVideoContent">
                                                   
                        
						 <?php //Mouseover for the Section
						 	if($strHolderR == "1")  							
								echo "<a href=\"#\" onmouseout=\"changeImage('imgRiskAssessment','images/riskout.jpg');\" onmouseover=\"changeImage('imgRiskAssessment','images/riskover.jpg')\"><img src=\"images/riskout.jpg\" alt=\"Risk Assessment &amp; Analysis\" width=\"589\" height=\"75\" id=\"imgRiskAssessment\" /></a>
                            <a href=\"#\" onmouseout=\"changeImage('imgBusinessImpactAnalysis','images/BIAout.jpg');\" onmouseover=\"changeImage('imgBusinessImpactAnalysis','images/BIAover.jpg')\"><img src=\"images/BIAout.jpg\" alt=\"Business Impact Analysis\" width=\"589\" height=\"75\" id=\"imgBusinessImpactAnalysis\" /></a>
                            <a href=\"#\" onmouseout=\"changeImage('imgBusinessContinuity','images/BCPout.jpg');\" onmouseover=\"changeImage('imgBusinessContinuity','images/BCPover.jpg')\"><img src=\"images/BCPout.jpg\" alt=\"Business Continuity Planning\" width=\"589\" height=\"75\" id=\"imgBusinessContinuity\" /></a>
                            <a href=\"#\" onmouseout=\"changeImage('imgInsuranceInventory','images/INSinvout.jpg');\" onmouseover=\"changeImage('imgInsuranceInventory','images/INSinvover.jpg')\"><img src=\"images/INSinvout.jpg\" alt=\"Create An Insurance Inventory\" width=\"589\" height=\"75\" id=\"imgInsuranceInventory\" /></a>";
							else if($strHolderR == "2")
								echo "<a href=\"#\" onmouseout=\"changeImage('imgImmediate','images/IRTout.jpg')\" onmouseover=\"changeImage('imgImmediate','images/IRTover.jpg',1)\"><img src=\"images/IRTout.jpg\" alt=\"Immediate Response Plans\" width=\"589\" height=\"75\" id=\"imgImmediate\" /></a>
								
							<a href=\"#\" onmouseout=\"changeImage('imgEmergency','images/ERPout.jpg')\" onmouseover=\"changeImage('imgEmergency','images/ERPover.jpg',1)\"><img src=\"images/ERPout.jpg\" alt=\"Emergency Response Plans\" width=\"589\" height=\"75\" id=\"imgEmergency\" /></a>
							
							<a href=\"#\" onmouseout=\"changeImage('imgFirstAid','images/CPRout.jpg')\" onmouseover=\"changeImage('imgFirstAid','images/CPRover.jpg',1)\"><img src=\"images/CPRout.jpg\" alt=\"FirstAid &amp; CPR Training\" width=\"589\" height=\"75\" id=\"imgFirstAid\" /></a>
							
							<a href=\"ourstore.html\" onmouseout=\"changeImage('imgSupplies','images/survivalkitsout.jpg')\" onmouseover=\"changeImage('imgSupplies','images/survivalkitsover.jpg',1)\"><img src=\"images/survivalkitsout.jpg\" alt=\"Emergency Surivial Kits\" width=\"589\" height=\"75\" id=\"imgSupplies\" /></a>";
							else if($strHolderR == "3") 				
								echo "<a href=\"#\" onmouseout=\"changeImage('imgDisasterRecovery','images/DRPout.jpg')\" onmouseover=\"changeImage('imgDisasterRecovery','images/DRPover.jpg',1)\"><img src=\"images/DRPout.jpg\" alt=\"Disaster Recovery\" width=\"589\" height=\"75\" id=\"imgDisasterRecovery\" /></a>
							<a href=\"#\" onmouseout=\"changeImage('imgDamageAssessment','images/DASout.jpg')\" onmouseover=\"changeImage('imgDamageAssessment','images/DASover.jpg',1)\"><img src=\"images/DASout.jpg\" alt=\"Damage Assessment\" width=\"589\" height=\"75\" id=\"imgDamageAssessment\" /></a>
							
							<a href=\"#\" onmouseout=\"changeImage('imgReplacement','images/equipout.jpg')\" onmouseover=\"changeImage('imgReplacement','images/equipover.jpg',1)\"><img src=\"images/equipout.jpg\" alt=\"Data &amp; Equipment Replacement\" width=\"589\" height=\"75\" id=\"imgReplacement\" /></a>
							
							<a href=\"#\" onmouseout=\"changeImage('imgAlternate','images/ALTout.jpg')\" onmouseover=\"changeImage('imgAlternate','images/ALTover.jpg',1)\"><img src=\"images/ALTout.jpg\" alt=\"Alternate Arrangements\" width=\"589\" height=\"75\" id=\"imgAlternate\" /></a>";
							if($strHolderR == "4") 
								 echo "<a href=\"#\" onmouseout=\"changeImage('imgRestoration','images/Drestout.jpg')\" onmouseover=\"changeImage('imgRestoration','images/Drestover.jpg',1)\"><img src=\"images/Drestout.jpg\" alt=\"Disaster Restoration\" width=\"589\" height=\"75\" id=\"imgRestoration\" /></a>
								 
							<a href=\"#\" onmouseout=\"changeImage('imgReconstruction','images/BConstructout.jpg')\" onmouseover=\"changeImage('imgReconstruction','images/BConstructover.jpg',1)\"><img src=\"images/BConstructout.jpg\" alt=\"Disaster Reconstruction\" width=\"589\" height=\"75\" id=\"imgReconstruction\" /></a>
							
							<a href=\"#\" onmouseout=\"changeImage('imgResiliance','images/resilout.jpg')\" onmouseover=\"changeImage('imgResiliance','images/resilover.jpg',1)\"><img src=\"images/resilout.jpg\" alt=\"Business Resiliance\" width=\"589\" height=\"75\" id=\"imgResiliance\" /></a>
							
							<a href=\"solutions.html\" onmouseout=\"changeImage('imgBusinessUsual','images/BAUout.jpg')\" onmouseover=\"changeImage('imgBusinessUsual','images/BAUover.jpg',1)\"><img src=\"images/BAUout.jpg\" alt=\"Business As Usual\" width=\"589\" height=\"75\" id=\"imgBusinessUsual\" /></a>";?>
                        </div>
                        <div class="customNavigation" id="divFourRVideoNavigation">
						 <?php //Video for the Section
						 	if($strHolderR == "1")  							
								echo "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0\" width=\"275\" height=\"250\" title=\"Reduce Video\">
  <param name=\"movie\" value=\"FlashVideos/reducevid.swf\" />
  <param name=\"quality\" value=\"high\" />
  <embed src=\"FlashVideos/reducevid.swf\" quality=\"high\" pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"275\" height=\"250\"></embed>
  </object>";
							else if($strHolderR == "2")							
								echo "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0\" width=\"275\" height=\"250\" title=\"Reduce Video\">
  <param name=\"movie\" value=\"FlashVideos/respondvid.swf\" />
  <param name=\"quality\" value=\"high\" />
  <embed src=\"FlashVideos/respondvid.swf\" quality=\"high\" pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"275\" height=\"250\"></embed>
  </object>";
							else if($strHolderR == "3") 							
								echo "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0\" width=\"275\" height=\"250\" title=\"Reduce Video\">
  <param name=\"movie\" value=\"FlashVideos/recovervid.swf\" />
  <param name=\"quality\" value=\"high\" />
  <embed src=\"FlashVideos/recovervid.swf\" quality=\"high\" pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"275\" height=\"250\"></embed>
  </object>";
							if($strHolderR == "4") 
								echo "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0\" width=\"275\" height=\"250\" title=\"Reduce Video\">
  <param name=\"movie\" value=\"FlashVideos/restorevid.swf\" />
  <param name=\"quality\" value=\"high\" />
  <embed src=\"FlashVideos/restorevid.swf\" quality=\"high\" pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"275\" height=\"250\"></embed>
  </object>";?>						                    
                        </div>
                        <div class="customFooter" id="divFourRVideoFooter"></div>
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
			  <div align="left" id="divFourRFooter" class="customContent"> 
                <?php if($strHolderR != "1")  							
								 echo "<a href=\"FourRs.php?section=Services&Footer=1&r=1\" onmouseout=\"changeImage('imgReduce','images/Reduce2out.jpg')\" onmouseover=\"changeImage('imgReduce','images/Reduce2over.jpg')\"><img src=\"images/Reduce2out.jpg\" alt=\"Reduce The Risk\" id=\"imgReduce\" /></a>";
					if($strHolderR != "2")
    		            echo "<a href=\"FourRs.php?section=Services&Footer=1&r=2\" onmouseout=\"changeImage('imgRespond','images/Respond2out.jpg')\" onmouseover=\"changeImage('imgRespond','images/Respond2over.jpg')\"><img src=\"images/Respond2out.jpg\" alt=\"Respond To A Disaster\" id=\"imgRespond\" /></a>";
					if($strHolderR != "3") 
                 		echo "<a href=\"FourRs.php?section=Services&Footer=1&r=3\" onmouseout=\"changeImage('imgRecover','images/Recover2out.jpg')\" onmouseover=\"changeImage('imgRecover','images/Recover2over.jpg')\"><img src=\"images/Recover2out.jpg\" alt=\"Recover After A Loss\" id=\"imgRecover\" /></a>";
					if($strHolderR != "4") 
                		 echo "<a href=\"FourRs.php?section=Services&Footer=1&r=4\" onmouseout=\"changeImage('imgRestore','images/Restore2out.jpg')\" onmouseover=\"changeImage('imgRestore','images/Restore2over.jpg')\"><img src=\"images/Restore2out.jpg\" alt=\"Restore Business As Usual\" id=\"imgRestore\" /></a>";?>      
              </div>
              <div class="customNavigation">
              	 <?php //Goes to Next Part Link
					if($strHolderR == "1")  							
						echo "<a href=\"FourRs.php?section=Services&Footer=1&r=2\" onmouseout=\"changeImage('imgStep2','images/nxtstep1out.jpg');\" onmouseover=\"changeImage('imgStep2','images/nxtstep1over.jpg')\"><img src=\"images/nxtstep1out.jpg\" alt=\"Step 2: Respond To A Disaster\" width=\"225\" height=\"150\" id=\"imgStep2\" /></a>";
					else if($strHolderR == "2")
						echo "<a href=\"FourRs.php?section=Services&Footer=1&r=3\" onmouseout=\"changeImage('imgStep3','images/nxtstep2out.jpg')\" onmouseover=\"changeImage('imgStep3','images/nxtstep2over.jpg')\"><img src=\"images/nxtstep2out.jpg\" alt=\"Step 3: Recover After A Disaster\" width=\"225\" height=\"150\" id=\"imgStep3\" /></a>";
					else if($strHolderR == "3")
						echo "<a href=\"FourRs.php?section=Services&Footer=1&r=4\" onmouseout=\"changeImage('imgStep4','images/nxtstep3out.jpg')\" onmouseover=\"changeImage('imgStep4','images/nxtstep3over.jpg')\"><img src=\"images/nxtstep3out.jpg\" alt=\"Step 4: Restore Business As Usual\" width=\"225\" height=\"150\" id=\"imgStep4\" /></a>";
					if($strHolderR == "4") 
						 echo "<a href=\"Solutions.php?section=Solutions\" onmouseout=\"changeImage('imgSignUp','images/nxtstep4out.jpg')\" onmouseover=\"changeImage('imgSignUp','images/nxtstep4over.jpg')\"><img src=\"images/nxtstep4out.jpg\" alt=\"Build A Plan Today!\" width=\"225\" height=\"150\" id=\"imgSignUp\" /></a>";?>
              </div>
              <div class="customFooter"></div>
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
