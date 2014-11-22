<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/BasicTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Solutions - Continuity Inc. - Disaster Recovery Solutions</title>
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
						 <!-- InstanceBeginEditable name="hiddenAreaForLogIn" -->
                                        <?php 
										$strSolPath = $_GET['both'];//holds the start of the URL types
 
										//checks if there is anything in strHolderS
										if ($strSolPath == "")
											$strSolPath = $_POST['hfBoth'];  ?>
                                        <input type="hidden" name="hfBoth" value="<?php echo $strSolPath; ?>" /><!-- InstanceEndEditable -->
                   
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
								?><!-- InstanceBeginEditable name="hiddenAreaForLogOut" --><?php $strLogOutString = $strLogOutString."&both=".$strSolPath; ?><!-- InstanceEndEditable --><?php
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
              <h1><!-- InstanceBeginEditable name="h1Title" -->Solutions<!-- InstanceEndEditable --></h1>
          	</div><!-- end of Header -->
            <div class="customContent" id="divBasicContent">
                <!-- InstanceBeginEditable name="BasicContent" -->
                <?php 
                	//checks if 
					if($strSolPath == "1")
						echo "<div class=\"divPageTitle\" align=\"left\">
                        	<label class=\"lblPageTitle\">Our Home Solutions</label>
                        </div>
						<div align=\"left\">
                					<a href=\"Home.php?section=Solutions\"><img src=\"images/Homerecoverytop.jpg\" alt=\"Home Recovery Planning\" width=\"894\" height=\"232\" /></a>
                			</div>
						<div class=\"divPageTitle\" align=\"left\">
                        	<label class=\"lblPageTitle\">Our Business Solutions</label>
                        </div>";
                ?>
                <a name="Basic"></a>
                <div class="divBasicSolution" align="left">
				   	<div class="divBasicHidden boardBox" id="divBasicSolHidden">
        				<div class="customContainer infoSolContainer lblBasicBackgroundColor">
                			<div class="customContent infoSolContent">
                       			<label>Basic Edition: Pricing</label>
               				</div>
                    		<div align="right" class="customContent infoSolNavigation">
                        		<div class="divClose">
                          			<a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divBasicSolHidden','divGrayBG');">Close</a>
                        		</div>
                    		</div>
                    		<div class="customFooter infoSolFooter"></div>
            			</div><!-- end of Cotinater -->
	              		<div class="divHiddlenBody">
                    		<img src="images/BasicPRGPRICE.jpg" alt="Basic Edition Pricing" width="260" height="294" />
    		        	</div>
    				</div><!-- end of Hidden Div -->
                    <div class="divBasicHidden boardBox" id="divBasicDemoHidden">
        				<div class="customContainer infoSolContainer lblBasicBackgroundColor">
                			<div class="customContent infoSolContent">
                       			<label>Basic Edition: Demo</label>
               				</div>
                    		<div align="right" class="customContent infoSolNavigation">
                        		<div class="divClose">
                          			<a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divBasicDemoHidden','divGrayBG');">Close</a>
                        		</div>
                    		</div>
                    		<div class="customFooter infoSolFooter"></div>
            			</div><!-- end of Cotinater -->
	              		<div class="divHiddlenBody">
                    		<img src="images/demovideo.jpg" alt="Basic Edition: Demo Video" width="260" height="125" />
    		        	</div>
    				</div><!-- end of Hidden Div -->
                    <div class="customContainer divPricingDisplayContainer">
                        <div class="customContent divPricingDisplayContent">
                            <img src="images/BasicPRGSUMM.jpg" alt="My Continuity Plans: Basic Edition" width="882" height="479" />
                        </div>
                        <div class="customNavigation divPricingDisplayNavigation">
                            <br />
                            <a href="javascript:void(0);" onclick="javascript:toggleLayer('divBasicSolHidden','divGrayBG');" class="aUnderline lblBasicColor">Pricing</a>
                            <br /><br />
                            <a href="javascript:void(0);" onclick="javascript:toggleLayer('divBasicDemoHidden','divGrayBG');" class="aUnderline lblBasicColor">View Demo</a>
                            <br /><br />
                            <?php //checks if the user log in
                    		/*if (checksUserID() !== FALSE)
                            	echo "<a href=\"Profile.php?Ed=1\" class=\"aUnderline lblBasicColor\">Buy Now - <br />Basic Edition</a>";
							else
                            	echo "<a href=\"LogIn.php?accesscheck=Profile.php?Ed=1\" class=\"aUnderline lblBasicColor\">Buy Now - <br />Basic Edition</a>";*/
								echo "<a href=\"Contact.php?section=Contact&Footer=1\" class=\"aUnderline lblBasicColor\">Buy Now - <br />Basic Edition</a>";?>
                       </div>
                       <div class="customFooter divPricingDisplayFooter"></div>
                	</div>
                </div>
                <a name="Standard"></a>
                <div class="divStandardSolution" align="left">
                	<div class="divBasicHidden boardBox" id="divStandardSolHidden">
        				<div class="customContainer infoSolContainer lblStandardBackgroundColor">
                			<div class="customContent infoSolContent">
                       			<label>Standard Edition: Pricing</label>
               				</div>
                    		<div align="right" class="customContent infoSolNavigation">
                        		<div class="divClose">
                          			<a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divStandardSolHidden','divGrayBG');">Close</a>
                        		</div>
                    		</div>
                    		<div class="customFooter infoSolFooter"></div>
            			</div><!-- end of Cotinater -->
	              		<div class="divHiddlenBody">
                    		<img src="images/StandardPRGPRICE.jpg" alt="Standard Edition Pricing" width="260" height="294" />
    		        	</div>
    				</div><!-- end of Hidden Div -->
                    <div class="divBasicHidden boardBox" id="divStandardDemoHidden">
        				<div class="customContainer infoSolContainer lblStandardBackgroundColor">
                			<div class="customContent infoSolContent">
                       			<label>Standard Edition: Demo</label>
               				</div>
                    		<div align="right" class="customContent infoSolNavigation">
                        		<div class="divClose">
                          			<a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divStandardDemoHidden','divGrayBG');">Close</a>
                        		</div>
                    		</div>
                    		<div class="customFooter infoSolFooter"></div>
            			</div><!-- end of Cotinater -->
	              		<div class="divHiddlenBody">
                    		<img src="images/demovideo.jpg" alt="Standard Edition: Demo Video" width="260" height="125" />
    		        	</div>
    				</div><!-- end of Hidden Div -->
                	<div class="customContainer divPricingDisplayContainer">
                        <div class="customContent divPricingDisplayContent">
		                	<img src="images/StandardPRGSUMM.jpg" alt="My Continuity Plans: Standard Edition" width="882" height="479" />
                    	</div>
                        <div class="customNavigation divPricingDisplayNavigation">
                            <br />
                            <a href="javascript:void(0);" onclick="javascript:toggleLayer('divStandardSolHidden','divGrayBG');" class="aUnderline lblStandardColor">Pricing</a>
                            <br /><br />
                            <a href="javascript:void(0);" onclick="javascript:toggleLayer('divStandardDemoHidden','divGrayBG');" class="aUnderline lblStandardColor">View Demo</a>
                            <br /><br />
                             <?php //checks if the user log in
                    		/*if (checksUserID() !== FALSE)
                            	echo "<a href=\"Profile.php?Ed=2\" class=\"aUnderline lblStandardColor\">Buy Now - <br />Standard Edition</a>";
							else
                            	echo "<a href=\"LogIn.php?accesscheck=Profile.php?Ed=2\" class=\"aUnderline lblStandardColor\">Buy Now - <br />Standard Edition</a>";*/
								echo "<a href=\"Contact.php?section=Contact&Footer=1\" class=\"aUnderline lblStandardColor\">Buy Now - <br />Standard Edition</a>";
							?>                            
                       </div>
                       <div class="customFooter divPricingDisplayFooter"></div>
                	</div>
                </div>
                <a name="Enter"></a>
                <div class="divEnterSolution" align="left">
                	<div class="divBasicHidden boardBox" id="divEnterpriseSolHidden">
        				<div class="customContainer infoSolContainer lblEnterBackgroundColor">
                			<div class="customContent infoSolContent">
                       			<label>Enterprise Edition: Pricing</label>
               				</div>
                    		<div align="right" class="customNavigation infoSolNavigation">
                        		<div class="divClose">
                          			<a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divEnterpriseSolHidden','divGrayBG');">Close</a>
                        		</div>
                    		</div>
                    		<div class="customFooter infoSolFooter"></div>
            			</div><!-- end of Cotinater -->
	              		<div class="divHiddlenBody">
                    		<img src="images/EnterprisePRGPricing.jpg" alt="Enterprise Edition Pricing" width="260" height="294" />
    		        	</div>
    				</div><!-- end of Hidden Div -->
                    <div class="divBasicHidden boardBox" id="divEnterpriseDemoHidden">
        				<div class="customContainer infoSolContainer lblEnterBackgroundColor">
                			<div class="customContent infoSolContent">
                       			<label>Enterprise Edition: Demo</label>
               				</div>
                    		<div align="right" class="customContent infoSolNavigation">
                        		<div class="divClose">
                          			<a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divEnterpriseDemoHidden','divGrayBG');">Close</a>
                        		</div>
                    		</div>
                    		<div class="customFooter infoSolFooter"></div>
            			</div><!-- end of Cotinater -->
	              		<div class="divHiddlenBody">
                    		<img src="images/demovideo.jpg" alt="Enterprise Edition: Demo Video" width="260" height="125" />
    		        	</div>
    				</div><!-- end of Hidden Div -->
                	<div class="customContainer divPricingDisplayContainer">
                        <div class="customContent divPricingDisplayContent">
		                	<img src="images/EnterprisePRGSUMM.jpg" alt="My Continuity Plans: Enterprise Edition" width="882" height="476" />
	                    </div>
                        <div class="customNavigation divPricingDisplayNavigation">
                            <br />
                            <a href="javascript:void(0);" onclick="javascript:toggleLayer('divEnterpriseSolHidden','divGrayBG');" class="aUnderline lblEnterColor">Pricing</a>
                            <!--<br /><br />
                            <a href="javascript:void(0);" onclick="javascript:toggleLayer('divEnterpriseDemoHidden','divGrayBG');" class="aUnderline lblEnterColor">View Demo</a>-->
                            <br /><br />
                            <?php //checks if the user log in
                    		/*if (checksUserID() !== FALSE)
                            	echo "<a href=\"Profile.php?Ed=3\" class=\"aUnderline lblEnterColor\">Buy Now - <br />Enterprise Edition</a>";
							else
                            	echo "<a href=\"LogIn.php?accesscheck=Profile.php?Ed=3\" class=\"aUnderline lblEnterColor\">Buy Now - <br />Enterprise Edition</a>";*/
								echo "<a href=\"Contact.php?section=Contact&Footer=1\" class=\"aUnderline lblEnterColor\">Buy Now - <br />Enterprise Edition</a>";
							?>
                       </div>
                       <div class="customFooter divPricingDisplayFooter"></div>
                	</div>
                </div><!-- InstanceEndEditable -->
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
