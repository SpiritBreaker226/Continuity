<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/BasicTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Home - Continuity Inc. - Disaster Recovery Solutions</title>
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
						 <!-- InstanceBeginEditable name="hiddenAreaForLogIn" --><!-- InstanceEndEditable -->
                   
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
              <h1><!-- InstanceBeginEditable name="h1Title" -->Home<!-- InstanceEndEditable --></h1>
          	</div><!-- end of Header -->
            <div class="customContent" id="divBasicContent">
                <!-- InstanceBeginEditable name="BasicContent" -->
                <div id="divHome">
                	<div class="customContainer borderBottom20" id="divHomeContainer">
                        <div class="customContent" id="divHomeContent">
                             <!-- Solutions for Are You Prepared Image -->
                            <div class="divBasicHidden boardBox" id="divSolArePre">
                                <div class="divHiddlenBody">
                                    <img src="images/indexIMG2.jpg" alt="Solutions To Help You.. Reduce, Respond, Recover, Restore" onmouseout="toggleLayer('divSolArePre','');" onmouseover="toggleLayer('divSolArePre','');" />
                                </div>
                            </div><!-- end of Hidden Div -->
                            
                            <div class="divBasicHide" <?php if($_GET['section'] == "") echo "style=\"display:block;\""; ?>>
                                <a href="Solutions.php?section=Solutions" onmouseout="toggleLayer('divSolArePre','');" onmouseover="toggleLayer('divSolArePre','');"><img src="images/indexIMG1.jpg" alt="Are You Prepared?" height="412" width="694" /></a>
                            </div>
                            <div class="divBasicHide" id="divBasicPlanning" <?php if($_GET['section'] == "Basic") echo "style=\"display:block;\""; ?>>
                                <div class="divBasicStandBuyNow">
                                    <?php //checks if the user log in
									 echo " <a href=\"Contact.php?section=Contact&Footer=1\" onmouseout=\"changeImage('imgBasicBuy','images/BEbuynowout.jpg')\" onmouseover=\"changeImage('imgBasicBuy','images/BEbuynowover.jpg')\"><img src=\"images/BEbuynowout.jpg\" alt=\"Buy Now\" id=\"imgBasicBuy\" /></a>";
									/*if (checksUserID() !== FALSE)
                                        echo " <a href=\"Profile.php?Ed=1\" onmouseout=\"changeImage('imgBasicBuy','images/BEbuynowout.jpg')\" onmouseover=\"changeImage('imgBasicBuy','images/BEbuynowover.jpg')\"><img src=\"images/BEbuynowout.jpg\" alt=\"Buy Now\" id=\"imgBasicBuy\" /></a>";
                                    else
                                        echo " <a href=\"LogIn.php?accesscheck=Profile.php?Ed=1\" onmouseout=\"changeImage('imgBasicBuy','images/BEbuynowout.jpg')\" onmouseover=\"changeImage('imgBasicBuy','images/BEbuynowover.jpg')\"><img src=\"images/BEbuynowout.jpg\" alt=\"Buy Now\" id=\"imgBasicBuy\" /></a>";*/?>
                                </div>
                                <div class="divBasicStandLearn">
                                    <a href="ThreeSs.php?section=Three&s=1" onmouseout="changeImage('imgBasicLearn','images/learn1out.jpg')" onmouseover="changeImage('imgBasicLearn','images/learn1over.jpg')"><img src="images/learn1out.jpg" alt="Learn About Out Basic Edition Program Click Here" id="imgBasicLearn" /></a>
                                </div>
                                <img src="images/BE6.jpg" alt="Basic Planning" height="412" width="732" />
                               <!-- <a href="Solutions.php?section=Solutions#Basic"></a>-->
                            </div>
                            <div class="divBasicHide" id="divStandardPlanning" <?php if($_GET['section'] == "Standard") echo "style=\"display:block;\""; ?>>
                                <div class="divBasicStandBuyNow">
                                <?php //checks if the user log in
                                    /*if (checksUserID() !== FALSE)
                                        echo " <a href=\"Profile.php?Ed=2\" onmouseout=\"changeImage('imgStandardBuy','images/SEbuynow2out.jpg')\" onmouseover=\"changeImage('imgStandardBuy','images/SEbuynow2over.jpg')\"><img src=\"images/SEbuynow2out.jpg\" alt=\"Buy Now\" id=\"imgStandardBuy\" /></a>";
                                    else
                                        echo " <a href=\"LogIn.php?accesscheck=Profile.php?Ed=2\" onmouseout=\"changeImage('imgStandardBuy','images/SEbuynow2out.jpg')\" onmouseover=\"changeImage('imgStandardBuy','images/SEbuynow2over.jpg')\"><img src=\"images/SEbuynow2out.jpg\" alt=\"Buy Now\" id=\"imgStandardBuy\" /></a>";*/
									 echo " <a href=\"Contact.php?section=Contact&Footer=1\" onmouseout=\"changeImage('imgStandardBuy','images/SEbuynow2out.jpg')\" onmouseover=\"changeImage('imgStandardBuy','images/SEbuynow2over.jpg')\"><img src=\"images/SEbuynow2out.jpg\" alt=\"Buy Now\" id=\"imgStandardBuy\" /></a>";	?>
                                       
                                </div>
                                <div class="divBasicStandLearn">
                                    <a href="ThreeSs.php?section=Three&s=2" onmouseout="changeImage('imgStandardLearn','images/learn2out.jpg')" onmouseover="changeImage('imgStandardLearn','images/learn2over.jpg')"><img src="images/learn2out.jpg" alt="Learn About Out Standard Edition Program Click Here" id="imgStandardLearn" /></a>
                                </div>
                                <img src="images/BE7.jpg" alt="Standard Planning" height="412" width="732" />
                                <!--<a href="Solutions.php?section=Solutions#Standard"></a>-->
                            </div>
                            <div class="divBasicHide" id="divEnterprisePlanning" <?php if($_GET['section'] == "Enterprise") echo "style=\"display:block;\""; ?>>
                                <div id="divEnterFreeBus">
                                    <a href="Contact.php?section=Contact&Footer=1" onmouseout="changeImage('imgEnterBuy','images/EEbuynow3out.jpg')" onmouseover="changeImage('imgEnterBuy','images/EEbuynow3over.jpg')"><img src="images/EEbuynow3out.jpg" alt="Buy Now" id="imgEnterBuy" /></a>
                                </div>
                                <div id="divEnterLearn">
                                    <a href="ThreeSs.php?section=Three&s=3" onmouseout="changeImage('imgEnterLearn','images/learn3out.jpg')" onmouseover="changeImage('imgEnterLearn','images/learn3over.jpg')"><img src="images/learn3out.jpg" alt="Learn About Out Enterprise Edition Program Click Here" id="imgEnterLearn" /></a>
                                </div>
                                <img src="images/BE8.jpg" alt="Enterprise Planning" height="412" width="732" />
                                <!--<a href="Solutions.php?section=Solutions#Enter"></a>-->
                            </div>
                        </div>
                        <div class="customNavigation" id="divHomeNavigation">
                            <a href="javascript:void(0);" onclick="classToggleLayer(getDocID('divHomeContainer'),getDocID('divBasicPlanning'),'divBasicHide','div');changeImage('imgHomeBasic','images/BEflapover.jpg');changeImage('imgHomeStandard','images/SEflapout.jpg');changeImage('imgHomeEnterprise','images/EEflapout.jpg');" onmouseout="if(getDocID('divBasicPlanning').style.display != 'block') changeImage('imgHomeBasic','images/BEflapout.jpg')" onmouseover="changeImage('imgHomeBasic','images/BEflapover.jpg')">
                                <img src="<?php if($_GET['section'] == "Basic") echo "images/BEflapover.jpg"; else echo "images/BEflapout.jpg"; ?>" alt="Basic Planning Program" width="80" height="414" id="imgHomeBasic" />
                            </a>
                            
                            <a href="javascript:void(0);" onclick="classToggleLayer(getDocID('divHomeContainer'),getDocID('divStandardPlanning'),'divBasicHide','div');changeImage('imgHomeBasic','images/BEflapout.jpg');changeImage('imgHomeStandard','images/SEflapover.jpg');changeImage('imgHomeEnterprise','images/EEflapout.jpg');" onmouseout="if(getDocID('divStandardPlanning').style.display != 'block') changeImage('imgHomeStandard','images/SEflapout.jpg')" onmouseover="changeImage('imgHomeStandard','images/SEflapover.jpg')">
                                <img src="<?php if($_GET['section'] == "Standard") echo "images/SEflapover.jpg"; else echo "images/SEflapout.jpg"; ?>" alt="Standard Planning Program" width="80" height="414" id="imgHomeStandard" />
                            </a>
                            
                            <a href="javascript:void(0);" onclick="classToggleLayer(getDocID('divHomeContainer'),getDocID('divEnterprisePlanning'),'divBasicHide','div');changeImage('imgHomeBasic','images/BEflapout.jpg');changeImage('imgHomeStandard','images/SEflapout.jpg');changeImage('imgHomeEnterprise','images/EEflapover.jpg');" onmouseout="if(getDocID('divEnterprisePlanning').style.display != 'block') changeImage('imgHomeEnterprise','images/EEflapout.jpg')" onmouseover="changeImage('imgHomeEnterprise','images/EEflapover.jpg')">
                                <img src="<?php if($_GET['section'] == "Enterprise") echo "images/EEflapover.jpg"; else echo "images/EEflapout.jpg"; ?>" alt="Enterprise Planning Program" width="80" height="414" id="imgHomeEnterprise" />
                            </a>
                        </div>
                        <div class="customFooter" id="divHomeFooter">
                    </div>
	                    <!--<div align="left" class="lblFontSize14 lblFontBold" id="divLearnRecovery">
        	                <label>Learn About The <a href="Services.php?section=Services&Footer=1" class="lblFontSize14 aUnderline lblBasicColor">Four Areas of Disaster Recovery</a> and how they can help protect the survival of your business.</label>
                        </div>-->
                        <div align="left">
                        	<a href="FourRs.php?section=Services&Footer=1&r=1" onmouseout="changeImage('imgReduce','images/Reduce3out.jpg')" onmouseover="changeImage('imgReduce','images/Reduce3over.jpg')"><img src="images/Reduce3out.jpg" alt="Reduce The Risk" id="imgReduce" /></a>
                            <a href="FourRs.php?section=Services&Footer=1&r=2" onmouseout="changeImage('imgRespond','images/Respond3out.jpg')" onmouseover="changeImage('imgRespond','images/Respond3over.jpg')"><img src="images/Respond3out.jpg" alt="Respond To A Disaster" id="imgRespond" /></a>
                            <a href="FourRs.php?section=Services&Footer=1&r=3" onmouseout="changeImage('imgRecover','images/Recover3out.jpg')" onmouseover="changeImage('imgRecover','images/Recover3over.jpg')"><img src="images/Recover3out.jpg" alt="Recover After A Disaster" id="imgRecover" /></a>
                            <a href="FourRs.php?section=Services&Footer=1&r=4" onmouseout="changeImage('imgRestore','images/Restore3out.jpg')" onmouseover="changeImage('imgRestore','images/Restore3over.jpg')"><img src="images/Restore3out.jpg" alt="Restore Your Business As Usual" id="imgRestore" /></a>
                        </div>
                    </div>
                    <div align="left">
                    	<p class="lblFontBold"><label>At Continuity, we believe that the future of business lies in the hands of the prepared. Any business, no matter what size, needs to have a back up plan in place.</label></p>
                        <p class="lblFontSize10"><label>There are more then 300 different disaster that can affect your business. Knowing which threats are most likely to happen, and preparing your business for them, can prevent you from experenceing a catastrophic loss. With that said, we understand how tedious and costly putting a plan in place can be, so we have created various solutions to assist in the creation of a Business Continuity & Disaster Recovery plan. Don't wait for a disaster to strike at your business, be proactive with our On-Line planning programs.</label></p>
                    </div>
                     <div class="borderBottom20" align="left">
                    	<div class="divHomeWhyPlan lblFontSize10">
	                        <!--<label class="lblContinuityColor lblFontSize10 lblFontBold">I already have Insurance, Why do I need a Disaster Recovery Plan?</label>
    	                    <br /><br />
        	                <label class="lblFontSize9">Insurance is known as the best way to protect your business in the event of a disaster, however it will not help protect the most valuabe asset you have: your customers.</label>-->
                            <a href="WhyPlan.php?section=Plan&Footer=1" class="lblFontSize9 lblContinuityColor aUnderline"> <img src="images/MIDIMG1.jpg" alt="" /><img src="images/Bar1.jpg" alt="I Already Have Insurance" id="imgWhy1" onmouseout="changeImage('imgWhy1','images/Bar1.jpg')" onmouseover="changeImage('imgWhy1','images/Bar1over.jpg')" /></a>
                            <br />
                            <label><span class="lblBasicColor lblFontBold">Insurance is known</span> as the best way to protect your business in the event of a disaster, however it will not help protect the most valuabe asset you have: your customers.</label>
                        </div>
                        <div class="divHomeWhyPlan lblFontSize10">
	                        <!--<label class="lblBasicColor lblFontSize10 lblFontBold">Isn't my business too small to need a Business Continuity plan?</label>
    	                    <br /><br />
        	                <label class="lblFontSize9">Protecting the future of a business whatever size has to be the number one priority for every business owner.
                            <br /><br />
                            The smaller your business the more important it is to have a back-up plan. Any incident, no matter how small is capable of impacting your business on a long term basis.</label>-->
                            <a href="AssessYourBusiness.php?section=Plan&Footer=1" class="lblFontSize9 lblContinuityColor aUnderline"><img src="images/MIDIMG2.jpg" alt="" /><img src="images/Bar2.jpg" alt="Is My Business To Small" id="imgWhy2" onmouseout="changeImage('imgWhy2','images/Bar2.jpg')" onmouseover="changeImage('imgWhy2','images/Bar2over.jpg')" /></a>
                            <br />
                            <label><span class="lblStandardColor lblFontBold">Protecting the future of a business</span> whatever size has to be the number one priority for every business owner.
                            <br /><br />
                             The smaller your business the more important it is to have a back-up plan. Any incident, no matter how small is capable of impacting your business on a long term basis.</label>
                        </div>
                        <div class="divHomeWhyPlan lblFontSize10">
	                        <!--<label class="lblStandardColor lblFontSize10 lblFontBold">Doesn't it cost a lot of money to implement a Continuity plan?</label>
    	                    <br /><br />
        	                <label class="lblFontSize9">It does not need to be a costly program.
                            <br /><br />                            
                            The Business Continuity Plan will be designed to fit your business as it accounts for your key risks and outline your essential business needs. 
      <br /><br />
      The Main objective is to recover all business critical processes and minimize the impact on your employees, customer and your reputation after a disaster. The cost of not doing something greatly out weighs the cost of building a plan.</label>-->
                            
      						<a href="Solutions.php?section=Solutions&both=1" class="lblFontSize9 lblStandardColor aUnderline"><img src="images/MIDIMG3.jpg" alt="" /><img src="images/Bar3.jpg" alt="What Is This Going To Cost?" id="imgWhy3" onmouseout="changeImage('imgWhy3','images/Bar3.jpg')" onmouseover="changeImage('imgWhy3','images/Bar3over.jpg')" /></a>
                            <br />
      						<label><span class="lblContinuityColor lblFontBold">It does not need to be a costly program.</span>
                            <br /><br />
      						The Business Continuity Plan will be designed to fit your business as it accounts for your key risks and outline your essential business needs.
                            <br /><br />
         					The Main objective is to recover all business critical processes and minimize the impact on your employees, customer and your reputation after a disaster. The cost of not doing something greatly out weighs the cost of building a plan.</label>
                        </div>
                        <div class="divHomeWhyPlan lblFontSize10 divNoBoarder">
	                        <!--<label class="lblEnterColor lblFontSize10 lblFontBold">Why should my firm care about Business Continuity Planning?</label>
    	                    <br /><br />
        	                <label class="lblFontSize9">Business success is as much about protecting as it is growth. In an uncertain world, that means creating a business with flexibility to profit in changing conditions. 
                            <br /><br />
                            Ensuring your business is strong enough to survive should a disaster strike will give your business an advantage over those businesses who are not prepared. 
      						<br /><br />
      The future of business lies in the hands of the business who are able to continue to function in the event of a disaster or business disruption.</label>-->
      						<a href="Media.php?section=Media&Footer=1" class="lblFontSize9 lblEnterColor aUnderline"><img src="images/MIDIMG4.jpg" alt="" /><img src="images/Bar4.jpg" alt="Why Should We Care?" id="imgWhy4" onmouseout="changeImage('imgWhy4','images/Bar4.jpg')" onmouseover="changeImage('imgWhy4','images/Bar4over.jpg')" /></a>
      						<br />
                            <label><span class="lblEnterColor lblFontBold">Business success is as much about protecting as it is growth.</span>
                            <br/><br/>
				            In an uncertain world, that means creating a business with flexibility to profit in changing conditions.
             				<br/><br />
            				Ensuring your business is strong enough to survive should a disaster strike will give your business an advantage over those businesses who are not prepared.
				            <br /><br />
					        The future of business lies in the hands of the business who are able to continue to function in the event of a disaster or business disruption.</label>
                        </div>
                        <div class="customFooter"></div>
                        <div align="right">
                        	<div class="divHomeWhyPlan divHomeWhyPlanLinks">
                            	<a href="WhyPlan.php?section=Plan&Footer=1" class="lblFontSize9 lblContinuityColor aUnderline" onmouseout="changeImage('imgMore3','images/More3out.jpg')" onmouseover="changeImage('imgMore3','images/More3over.jpg')"><img src="images/More3out.jpg" alt="Find Out More" id="imgMore3" width="247" /></a>
                        	</div>
                            <div class="divHomeWhyPlan divHomeWhyPlanLinks">
                            	<a href="AssessYourBusiness.php?section=Plan&Footer=1" class="lblFontSize9 lblBasicColor aUnderline" onmouseout="changeImage('imgMore1','images/More1out.jpg')" onmouseover="changeImage('imgMore1','images/More1over.jpg')"><img src="images/More1out.jpg" alt="Find Out More" id="imgMore1" width="247" /></a>
                            </div>
                            <div class="divHomeWhyPlan divHomeWhyPlanLinks">
    							<a href="Solutions.php?section=Solutions&both=1" class="lblFontSize9 lblStandardColor aUnderline" onmouseout="changeImage('imgMore2','images/More2out.jpg')" onmouseover="changeImage('imgMore2','images/More2over.jpg')"><img src="images/More2out.jpg" alt="Find Out More" id="imgMore2" width="247" /></a>
                            </div>
                            <div class="divHomeWhyPlan divHomeWhyPlanLinks">
	                            <a href="Media.php?section=Media&Footer=1" class="lblFontSize9 lblEnterColor aUnderline" onmouseout="changeImage('imgMore4','images/More4out.jpg')" onmouseover="changeImage('imgMore4','images/More4over.jpg')"><img src="images/More4out.jpg" alt="Find Out More" id="imgMore4" width="247" /></a>
                            </div>
                            <div class="customFooter" id="divHomeWhyPlanFooter"></div>
                        </div>
                    </div>
                    <div class="customContainer" id="divHomeSoutionsContainer" align="left">
                    	<div class="customHeader" id="divHomeSoutionsHeader">
	                        <div class="divPageTitle" align="left">
    	                        <label class="lblPageTitle">How Can our Products Help Your Business?</label>
        	                </div>
                       		<label>Put simply, Business Continuity is about is about anticipating the crises that could afftect your business and planning for them, to make sure that your business can continue to function in the event of an emergency. For many companies, the real tragedy occurs after a disaster; they had excellent <em>Insurance Coverage</em>, but no <span class="lblFontBold lblContinuityColor">Disaster Recovery Plan</span>.
                            <br /><br />
                           	By using our Web Based Continuity Programs you will be able to cover the four essential areas of disaster recovery.</label>
                            <br /><br />
                            <img src="images/Check.jpg" alt="Checkmark" />
                           	<label class="lblFontBold lblFontSize16"><span class="lblBasicColor">Reduce</span> The Impact of A Disaster On Your Business.</label><br />
							<img src="images/Check.jpg" alt="Checkmark" />
        					<label class="lblFontBold lblFontSize16"><span class="lblStandardColor">Respond</span> To An Event That Has Occurred or is Occuring.</label><br />
							<img src="images/Check.jpg" alt="Checkmark" />
                            <label class="lblFontBold lblFontSize16"><span class="lblContinuityColor">Recover</span> Any and All Lost Items After A Disaster.</label><br />
							<img src="images/Check.jpg" alt="Checkmark" />
                            <label class="lblFontBold lblFontSize16"><span class="lblEnterColor">Restore</span> Your Business Back To Normal Operations.</label>
                            <div class="divPageTitle" align="left">
                            	<label class="lblPageTitle">What Are You Getting When You Use Our Products?</label>
                            </div>
                            <label>When you activate your Web Based Continuity Plans you business will begin the process of putting a Continuity Plan and Disaster Recovery Plan in place. Once completed you will have a Hard Copy of your detailed Action Plan to help your business recovery after an event. You will also have access to your plans anytime, anywhere with our remote access capabilities.
                            <br /><br />
                            Our programs have variuos unique features that will help your business prepare for anything. When you activate a Plan with Continuity Inc. you are getting the following.....</label>
                            <br /><br />
                            <img src="images/Check.jpg" alt="Checkmark" />
                			<label class="lblFontBold lblFontSize16">Access To Your Business Continuity &amp; Disaster Recovery Plans Anytime, Anywhere!		</label><br />
                            <img src="images/Check.jpg" alt="Checkmark" />
							<label class="lblFontBold lblFontSize16">Detailed Descrpition of Your Key Business Personnal,Units &amp; Operations.</label><br />
                            <img src="images/Check.jpg" alt="Checkmark" />
							<label class="lblFontBold lblFontSize16">Detailed Immediate Response Team Action Plans.</label><br />
							<img src="images/Check.jpg" alt="Checkmark" />
							<label class="lblFontBold lblFontSize16">Emergency Response Actions Plan &amp; Guidelines.</label><br />
							<img src="images/Check.jpg" alt="Checkmark" />
							<label class="lblFontBold lblFontSize16">Detailed Disaster Recovery Team Operations &amp; Action Plan.</label><br />
							<img src="images/Check.jpg" alt="Checkmark" />
							<label class="lblFontBold lblFontSize16">The Ability To Create A Photographic Document of Your Premises &amp; Company Assets for Recovery Purposes.</label><br />
							<img src="images/Check.jpg" alt="Checkmark" />
							<label class="lblFontBold lblFontSize16">Many More Disaster Recovery Tools &amp; Resources.</label>
                        </div>
                    	<div class="customContent" id="divHomeSoutionsContent">
                        	<div class="lblFontSize9 divHomeSol">
                            	<label class="lblBasicColor lblFontBold">Our Basic Edition</label><label> will provide your organization with an On-line Program that will  provide the tools and information required at the time of a disaster.</label>
                             </div>
                        	<img src="images/BE3.jpg" width="317" height="195" alt="Basic" />
                            <p align="left" class="lblFontSize10 lblBasicColor lblFontBold">Recommended for Businesses with 0 - 20 Employees</p>
      <p class="lblFontSize9"><label><strong>This is not a software product</strong>. Our program is based on-line to give you access to your recovery plans anytime, anywhere.</label></p>
      <p class="lblFontSize9"><label>Once you sign up you will be able to design, develop, implement, print, maintain and continue to update your plans as change occurs in your business.</label></p>
      <p class="lblFontSize9"><label>Our interactive program will give you the tools you need to ensure you are able to stay in business after a disaster has occurred.</label></p>
	  <p align="right"><a href="Solutions.php?section=Solutions#Basic" class="lblFontSize9 lblBasicColor aUnderline" onmouseout="changeImage('imgMoreBasic','images/More1out.jpg')" onmouseover="changeImage('imgMoreBasic','images/More1over.jpg')"><img src="images/More1out.jpg" alt="Find Out More" id="imgMoreBasic" width="247" /></a></p>
                        </div>
                        <div class="customThridContent" id="divHomeSoutionsThridContent">
                        	<div class="lblFontSize9 divHomeSol">
                                <label class="lblStandardColor lblFontBold">Our Standard Edition</label><label> in addition to our standard program features, will also provide you with some advanced tools that will assist in the recovery process.</label>
                             </div>
                        	<img src="images/BE4.jpg" alt="Standard" width="317" height="195" />
                            <p align="left" class="lblFontSize10 lblStandardColor lblFontBold">Recommended for Businesses  20 - 100 Employees</p>
      <p class="lblFontSize9"><strong>This is not a software product.</strong> Our program is based on-line to give you access to your recovery plans anytime, anywhere. </p>
      <p class="lblFontSize9">In addition to all of the basic program features, our standard edition will provide you with some advanced programs that will make your recovery process as accurate as possible.</p>
    <p class="lblFontSize9">This version is highly recommended for any general business that wants to have a plan in place to ensure business can continue, even after a disaster or business disruption.</p>
    <p align="right"><a href="Solutions.php?section=Solutions#Standard" class="lblFontSize9 lblStandardColor aUnderline" onmouseout="changeImage('imgMoreStandard','images/More2out.jpg')" onmouseover="changeImage('imgMoreStandard','images/More2over.jpg')"><img src="images/More2out.jpg" alt="Find Out More" id="imgMoreStandard" width="247" /></a></p>
                        </div>
                        <div class="customNavigation" id="divHomeSoutionsNavigation">
                        	<div class="lblFontSize9 divHomeSol">
                                <label class="lblEnterColor lblFontBold">Our Enterprise Edition</label><label> will team you up with our certified professionals to begin the process of designing, developing and implementing continuity strategies.</label>
                             </div>
                        	<img src="images/BE5.jpg" alt="Enterprise" width="317" height="195" />
                            <p align="left" class="lblFontSize10 lblEnterColor lblFontBold">Recommended for any Small - Medium Sized Business</p>
      <p class="lblFontSize9"><strong>This is not a software product.</strong> Our Enterprise Edition is based on a full facilitation program. Our team of professionals will work directly with your organization to design, develop and implement continuity plans.</p>
    <p class="lblFontSize9">Our assessment team will meet with you to determine the extent of planning required for your business. Once we have completed our initial assessment we will be able to inform you of what is required for your business, as it applies to your needs directly.</p>
    <p align="right"><a href="Solutions.php?section=Solutions#Enter" class="lblFontSize9 lblEnterColor aUnderline" onmouseout="changeImage('imgMoreEnter','images/More4out.jpg')" onmouseover="changeImage('imgMoreEnter','images/More4over.jpg')"><img src="images/More4out.jpg" alt="Find Out More" id="imgMoreEnter" width="247" /></a></p>
                        </div>                        
                    	<div class="customFooter" id="divHomeSoutionsFooter"></div>
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
			  <!-- InstanceBeginEditable name="BasicFooter" --><!-- InstanceEndEditable -->
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
