<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/BasicTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Contact Us - Continuity Inc. - Disaster Recovery Solutions</title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" type="text/css" href="CSS/MasterCSS.css" media="screen" />
<script src="javascript/MainJS.js" type="text/javascript"></script>
<?php require_once('PurePHP/LoginControl.php');?>
<?php require_once('Connections/conContinuty.php'); ?>
<!-- InstanceBeginEditable name="head" --><?php require_once('PurePHP/MasterFuncitons.php');

//forces the pages to come here to process the page
$Form = $_SERVER['PHP_SELF'];

//checks if the fields that need data do have some data in them
if($_POST['hfSubmit'] != '')
{				
	if($_POST['txtName'] == '')
	{
		echo "<script type=\"text/javascript\">
			window.onload=function(){
				displayMessage('divMessage','You Must Have an Name.',true,true);
		}//end of window.onload=function()</script>";
	}//end of if
	else
	{	
		if($_POST['txtTitle'] == '')
		{
			echo "<script type=\"text/javascript\">
				window.onload=function(){
					displayMessage('divMessage','You Must Have an Title.',true,true);
			}//end of window.onload=function()</script>";
		}//end of if
		else
		{
			if($_POST['txtCompany'] == '')
			{
				echo "<script type=\"text/javascript\">
					window.onload=function(){
						displayMessage('divMessage','You Must Have an Company.',true,true);
				}//end of window.onload=function()</script>";
			}//end of if
			else
			{
				if($_POST['txtStreet'] == '')
				{
					echo "<script type=\"text/javascript\">
						window.onload=function(){
							displayMessage('divMessage','You Must Have an Address.',true,true);
					}//end of window.onload=function()</script>";
				}//end of if
				else
				{
					if($_POST['txtCity'] == '')
					{
						echo "<script type=\"text/javascript\">
							window.onload=function(){
								displayMessage('divMessage','You Must Have an City.',true,true);
						}//end of window.onload=function()</script>";
					}//end of if
					else
					{
						if($_POST['txtPostal'] == '')
						{
							echo "<script type=\"text/javascript\">
								window.onload=function(){
									displayMessage('divMessage','You Must Have a Zip/Postal Code.',true,true);
							}//end of window.onload=function()</script>";
						}//end of if
						else
						{
							if(!preg_match("/^[a-z]\d[a-z] ?\d[a-z]\d$/i",$_POST['txtPostal']) && $_POST['cmbCountry'] == 'CA')
							{
								echo "<script type=\"text/javascript\">
									window.onload=function(){
										displayMessage('divMessage','Your Postal Code Format is not valid',true,true);
								}//end of window.onload=function()</script>";
							}//end of if
							else
							{
								if(!preg_match("/^\d{5}$/",$_POST['txtPostal']) && $_POST['cmbCountry'] == 'US')
								{
									echo "<script type=\"text/javascript\">
										window.onload=function(){
											displayMessage('divMessage','Your Zip Code Format is not valid',true,true);
									}//end of window.onload=function()</script>";
								}//end of if
								else
								{
									if($_POST['txtPhone'] == '')
									{
										echo "<script type=\"text/javascript\">
											window.onload=function(){
												displayMessage('divMessage','You Must Have a Phone Number.',true,true);
										}//end of window.onload=function()</script>";
									}//end of if
									else
									{
										if(!preg_match('/^(\(?[0-9]{3,3}\)?|[0-9]{3,3}[-.]?)[ ]?[0-9]{3,3}[-. ]?[0-9]{4,4}$/',$_POST['txtPhone']) && $_POST['txtPhone'] != '')
										{
											echo "<script type=\"text/javascript\">
												window.onload=function(){
													displayMessage('divMessage','Your Phone Number Format is not valid. ((###) ###-####)',true,true);
											}//end of window.onload=function()</script>";
										}//end of if
										else
										{
											if($_POST['txtEMail'] == '')
											{
												echo "<script type=\"text/javascript\">
													window.onload=function(){
														displayMessage('divMessage','You Must Have an E-Mail.',true,true);
												}//end of window.onload=function()</script>";
											}//end of if
											else
											{
												if(!preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i",$_POST['txtEMail']))
												{
													echo "<script type=\"text/javascript\">
														window.onload=function(){
															displayMessage('divMessage','Your E-Mail Address Format is not valid',true,true);
													}//end of window.onload=function()</script>";
												}//end of if
												else
												{
													if($_POST['txtComments'] == '')
													{
														echo "<script type=\"text/javascript\">
															window.onload=function(){
																displayMessage('divMessage','Your Must Have a Comments',true,true);
														}//end of window.onload=function()</script>";
													}//end of if
													else
													{
														$strSendTo = "";//holds the person who is going to be sent to
														$strReg = "";//holds the Reguarding What part of the e-mail
	
														//finds which person to send it to form cmbEmail 
														switch($_POST['cmbEmail'])
														{
															case "Sales":
															    $strSendTo = "sales";
														    break;
															case "Accounting":
															    $strSendTo = "accounting";
														    break;														
															case "Tech Support":
															    $strSendTo = "support";
														    break;														
															case "General Information":
															    $strSendTo = "info";
														    break;														
														}//end of switch
														
														//checks if there is anything to put intoi the strReg to make the e-mail more sepicfic
														if($_POST['cmbReg'] != "")
															$strReg = " reguarding ".$_POST['cmbReg'];
																										
														//sends out the e-mail
														sendEmail($_POST['txtTitle']." ".$_POST['txtName']." of ".$_POST['txtCompany']." has wants to contact you".$strReg,$_POST['txtName']." Comments:<br/><br/>".$_POST['txtComments']."<br/><br/>Contact Information:<br/><br/>Name: ".$_POST['txtName']."<br/>Company:".$_POST['txtCompany']."<br/>Addtress:".$_POST['txtStreet']."<br/>".$_POST['txtCity'].", ".$_POST['cmbPro']." ".$_POST['cmbCountry']."<br/>".$_POST['txtPostal'],$strSendTo,$_POST['txtEMail'],'PurePHP/Swift EMail/Swift.php','PurePHP/Swift EMail/Swift/Connection/SMTP.php');

														//goes to the Successful
												    	header("Location: ThankYouSending.php?section=ThankYouContact&Footer=1");
													}//end of if else
												}//end of if else
											}//end of if else
										}//end of if else
									}//end of if else
								}//end of if else
							}//end of if else
						}//end of if else
					}//end of if else
				}//end of if else
			}//end of if else
		}//end of if else
	}//end of if else
}//end of if?><!-- InstanceEndEditable -->

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
              <h1><!-- InstanceBeginEditable name="h1Title" -->Contact Us<!-- InstanceEndEditable --></h1>
          	</div><!-- end of Header -->
            <div class="customContent" id="divBasicContent">
                <!-- InstanceBeginEditable name="BasicContent" -->
                <div class="divPageTitle" align="left">
                	<label class="lblPageTitle">Contact Us</label>
                </div>
                <div class="customContainer">
                    <div id="divContactBody" class="customContent">
                        <form action="<?php echo $Form; ?>" method="post" id="frmContact" class="frmBasics"> 
                            <table width="405" border="0" cellspacing="1" cellpadding="3" align="left">
                              <tr>
                                <td colspan="2" class="tdDarkBlueBackColour" align="left"><label class="lblFontBold">Contact Information</label><br />
                                    <div id="divMessage" class="divBasicMessage"></div>
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>Email who?</label><label class="lblFontColorRed">*</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                  <select name="cmbEmail" id="cmbEmail">
                                    <option selected="selected">Sales</option>
                                    <option>Accounting</option>
                                    <option>Tech Support</option>
                                    <option>General Information</option>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>Reguarding What?</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                    <select name="cmbReg" id="cmbReg">
                                        <option value="program1">First Steps Program</option>
                                        <option value="program2">Business Assessment Program</option>
                                        <option value="program3">Plan Design &amp; Development Program</option>   
                                        <option value="program4">Plan Review &amp; Certification Program</option>
                                        <option value="program5">Testing &amp; Exercise Programs</option>
                                        <option value="program6">Plan Implementation Programs</option>
                                        <option value="program7">Business Continuity Seminar</option>
                                        <option value="program8">Additional Programs</option>
                                        <option value="service1">Risk Assessment &amp; Analysis</option>
                                        <option value="service2">Business Impact Analysis</option>
                                        <option value="service3">Business Inventory Services</option>
                                        <option value="service4">Emergency Response Planning</option>
                                        <option value="service5">Fire Saftey Program</option>
                                        <option value="service6">Disaster Clean Up Program</option>
                                        <option value="service7">Disaster Recovery Services</option>
                                        <option value="service8">Item Replacement Service</option>
                                        <option value="service9">Equipment Replacement Services</option>
                                        <option value="service10">Data Back-Up &amp; Recovery Services</option>
                                        <option value="service11">Network Management</option>
                                        <option value="service12">IT Services</option>
                                        <option value="service13">Other Continuity Services</option>
                                        <option value="product1">Emergency Survival Kits</option>
                                        <option value="product2">Disaster Recovery Kits</option>
                                        <option value="product3">Car Emergency Kits</option>
                                        <option value="product4">Power Generators</option>
                                        <option value="product5">Emergency Supplies</option>
                                        <option value="product6">First Aid Supplies</option>
                                        <option value="product7">Other Products</option>
                                    </select>
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>Name</label><label class="lblFontColorRed">*</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                  <input name="txtName" type="text" id="txtName" value = "<?php echo $_POST['txtName'];?>" size = "20" maxlength = "100" />
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>Title</label><label class="lblFontColorRed">*</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                  <input name="txtTitle" type = "text" id="txtTitle" value = "<?php echo $_POST['txtTitle'];?>" size = "20" maxlength = "100" />
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>Company</label><label class="lblFontColorRed">*</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                  <input name = "txtCompany" type = "text" id="txtCompany" value = "<?php echo $_POST['txtCompany'];?>" size = "20" maxlength = "100" />
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>Street Address</label><label class="lblFontColorRed">*</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                  <input name = "txtStreet" type = "text" id="txtStreet" value = "<?php echo $_POST['txtStreet'];?>" size = "20" maxlength = "100" />
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>City</label><label class="lblFontColorRed">*</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                  <input name = "txtCity" type = "text" id="txtCity" value = "<?php echo $_POST['txtCity'];?>" size = "20" maxlength = "100" />
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>State/Province</label><label class="lblFontColorRed">*</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                  <select name="cmbPro">
                                      <option value="AB" selected="selected">Alberta</option>
                                      <option value="BC">British Columbia</option>
                                      <option value="MA">Manitoba</option>
                                      <option value="NB">New Brunswick</option>
                                      <option value="NL">Newfoundland and Labrador</option>
                                      <option value="NW">Northwest Territories</option>
                                      <option value="NS">Nova Scotia</option>
                                      <option value="NN">Nunavut</option>
                                      <option value="ONT">Ontario</option>
                                      <option value="PEI">Prince Edward Island</option>
                                      <option value="QUB">Quebec</option>
                                      <option value="SAS">Saskatchewan</option>
                                      <option value="YUK">Yukon</option>	
                                      <option value="AL">Alabama</option>
                                      <option value="AK">Alaska</option>
                                      <option value="AZ">Arizona</option>
                                      <option value="AR">Arkansas</option>
                                      <option value="CA">California</option>
                                      <option value="CO">Colorado</option>
                                      <option value="CT">Connecticut</option>
                                      <option value="DE">Delaware</option>
                                      <option value="DC">District of Columbia</option>
                                      <option value="FL">Florida</option>
                                      <option value="GA">Georgia</option>
                                      <option value="HI">Hawaii</option>
                                      <option value="ID">Idaho</option>
                                      <option value="IL">Illinois</option>
                                      <option value="IN">Indiana</option>
                                      <option value="IA">Iowa</option>
                                      <option value="KS">Kansas</option>
                                      <option value="KY">Kentucky</option>
                                      <option value="LA">Louisiana</option>
                                      <option value="ME">Maine</option>
                                      <option value="MD">Maryland</option>
                                      <option value="MA">Massachusetts</option>
                                      <option value="MI">Michigan</option>
                                      <option value="MN">Minnesota</option>
                                      <option value="MS">Mississippi</option>
                                      <option value="MO">Missouri</option>
                                      <option value="MT">Montana</option>
                                      <option value="NE">Nebraska</option>
                                      <option value="NV">Nevada</option>
                                      <option value="NH">New Hampshire</option>
                                      <option value="NJ">New Jersey</option>
                                      <option value="NM">New Mexico</option>
                                      <option value="NY">New York</option>
                                      <option value="NC">North Carolina</option>
                                      <option value="ND">North Dakota</option>
                                      <option value="OH">Ohio</option>
                                      <option value="OK">Oklahoma</option>
                                      <option value="OR">Oregon</option>
                                      <option value="PA">Pennsylvania</option>
                                      <option value="RI">Rhode Island</option>
                                      <option value="SC">South Carolina</option>
                                      <option value="SD">South Dakota</option>
                                      <option value="TN">Tennessee</option>
                                      <option value="TX">Texas</option>
                                      <option value="UT">Utah</option>
                                      <option value="VT">Vermont</option>
                                      <option value="VA">Virginia</option>
                                      <option value="WA">Washington</option>
                                      <option value="WV">West Virginia</option>
                                      <option value="WI">Wisconsin</option>
                                      <option value="WY">Wyoming</option>
                                    </select>
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>Zip/Postal Code</label><label class="lblFontColorRed">*</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                  <input name = "txtPostal" type = "text" id="txtPostal" value = "<?php echo $_POST['txtPostal'];?>" size = "20" maxlength = "100" />
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>Country</label><label class="lblFontColorRed">*</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                    <select name="cmbCountry">
                                      <option value="CA" selected="selected">Canada</option>
                                      <option value="US">United States</option>
                                    </select>
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>Phone Number </label><label class="lblFontColorRed">*</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                  <input name = "txtPhone" type = "text" id="txtPhone" value = "<?php echo $_POST['txtPhone'];?>" size = "20" maxlength = "100" />
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>Email Address </label><label class="lblFontColorRed">*</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                  <input name = "txtEMail" type = "text" id="txtEMail" value = "<?php echo $_POST['txtEMail'];?>" size = "20" maxlength = "100" />
                                </td>
                              </tr>
                              <tr>
                                <td class="tdLightGreenBackColour" align="left"><label>Comments</label><label class="lblFontColorRed">*</label></td>
                                <td class="tdDarkGreenBackColour" align="left">
                                    <textarea name="txtComments" cols="17" rows="3" id="txtComments"><?php echo $_POST['txtComments'];?></textarea>
                                </td>
                              </tr>
                              <tr>
                                <td valign="top" class="tdLightGreenBackColour" align="left">
                                    <input type="hidden" name="hfSubmit" value="1" />
                                </td>
                                <td valign="top" class="tdDarkGreenBackColour" align="left">
                                    <input type="submit" name="Submit" value="Submit" />
                                    <input name="Reset" type="reset" id="Reset" value="Reset" />
                               
                                </td>
                              </tr>
                            </table>
                        </form>
                    </div>
                    <div class="customNavigation" align="left">
                    	<div id="divContactAddress">
                            <label><strong>Address:</strong><br /><br /></label>
						</div>
                    	<br /><br />
                    	<div id="divContactAddress">
                          <label><strong>Contact Us:</strong><br /><br />
                            Telephone: <br />
                            Toll Free: <br />
                         	Email: </label><a href="mailto:" class="lblFontColorBlack"></a>
						</div>
                        <br /><br />
                        <img src="images/contacctgirl.jpg" alt="" width="563" height="300" />
                    </div>
                    <div class="customFooter"></div>
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
