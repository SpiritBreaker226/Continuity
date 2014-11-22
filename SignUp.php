<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/BasicTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Build A Plan Today - Continuity Inc. - Disaster Recovery Solutions</title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" type="text/css" href="CSS/MasterCSS.css" media="screen" />
<script src="javascript/MainJS.js" type="text/javascript"></script>
<?php require_once('PurePHP/LoginControl.php');?>
<?php require_once('Connections/conContinuty.php'); ?>
<!-- InstanceBeginEditable name="head" -->
<?php require_once('Connections/conContinuty.php');
require_once('PurePHP/MasterFuncitons.php');

//forces the pages to come here to process the page
$User = $_SERVER['PHP_SELF'];

//checks if the fields that need data do have some data in them
if($_POST['hfSubmit'] != '')
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
			mysql_select_db($database_conContinuty, $conContinuty);
			$rsUser = mysql_query("SELECT * FROM users WHERE users.login = '".$_POST['txtEMail']."'", $conContinuty) or die(mysql_error());
			$totalRows_rsUser = mysql_num_rows($rsUser);
			
			//checks if the fields for the sign up page is current
			if($totalRows_rsUser > 0)
			{
					echo "<script type=\"text/javascript\">
					var oldonload=window.onload;//holds any prevs onload function from the js file
			
					window.onload=function(){
						if(typeof(oldonload)=='function')
							oldonload();
			
						displayMessage('divMessage','Your E-Mail Address is Already in Our Database',true,true);
					}//end of window.onload=function()</script>";
			}//end of if
			else
			{
				mysql_select_db($database_conContinuty, $conContinuty);
				$rsUser = mysql_query("SELECT * FROM users WHERE Users LIKE '%".$_POST['txtEMail']."%'", $conContinuty) or die(mysql_error());
				$totalRows_rsUser = mysql_num_rows($rsUser);
				
				//checks if the fields for the sign up page is current
				if($totalRows_rsUser > 0)
				{
					echo "<script type=\"text/javascript\">			
						window.onload=function(){			
							displayMessage('divMessage','Your E-Mail Address is Already Attach to an Account',true,true);
					}//end of window.onload=function()</script>";
				}//end of if
				else
				{
					if($_POST['txtPassword'] == '')
					{
						echo "<script type=\"text/javascript\">
							window.onload=function(){
								displayMessage('divMessage','Your Must Have a Password',true,true);
						}//end of window.onload=function()</script>";
					}//end of if
					else
					{
						if($_POST['txtRePassword'] == '')
						{
							echo "<script type=\"text/javascript\">
								window.onload=function(){
									displayMessage('divMessage','Your Must Have a Confirm Password',true,true);
							}//end of window.onload=function()</script>";
						}//end of if
						else
						{
							if($_POST['txtPassword'] != $_POST['txtRePassword'])
							{
								echo "<script type=\"text/javascript\">
									window.onload=function(){
										displayMessage('divMessage','Your Password and Confirm Password Does Not Match',true,true);
								}//end of window.onload=function()</script>";
							}//end of if
							else
							{
								if($_POST['txtFName'] == '' || strpos($_POST['txtFName'],"--") !== false || strpos($_POST['txtFName'],"\\") !== false  || strpos($_POST['txtFName'],"^") !== false)
								{
									echo "<script type=\"text/javascript\">
								
										window.onload=function(){
											displayMessage('divMessage','You Must Have a First Name.',true,true);
										}//end of window.onload=function()</script>";
								}//end of if
								else
								{
									if($_POST['txtLName'] == '' || strpos($_POST['txtLName'],"--") !== false || strpos($_POST['txtLName'],"\\") !== false || strpos($_POST['txtLName'],"^") !== false)
									{
										echo "<script type=\"text/javascript\">
											window.onload=function(){
												displayMessage('divMessage','You Must Have a Last Name.',true,true);
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
												if(!preg_match('/^(\(?[0-9]{3,3}\)?|[0-9]{3,3}[-.]?)[ ]?[0-9]{3,3}[-. ]?[0-9]{4,4}$/',$_POST['txtFax']) && $_POST['txtFax'] != '')
												{
													echo "<script type=\"text/javascript\">
														window.onload=function(){
															displayMessage('divMessage','Your Fax Number Format is not valid. ((###) ###-####)',true,true);
													}//end of window.onload=function()</script>";
												}//end of if
												else
												{
													if($_POST['txtStreet'] == '' || strpos($_POST['txtStreet'],"--") !== false || strpos($_POST['txtStreet'],"\\") !== false || strpos($_POST['txtStreet'],"^") !== false)
													{
														echo "<script type=\"text/javascript\">
															window.onload=function(){
																displayMessage('divMessage','You Must Have a Address.',true,true);
														}//end of window.onload=function()</script>";
													}//end of if
													else
													{
														if($_POST['txtCity'] == '' || strpos($_POST['txtCity'],"--") !== false || strpos($_POST['txtCity'],"\\") !== false || strpos($_POST['txtCity'],"^") !== false)
														{
															echo "<script type=\"text/javascript\">
																window.onload=function(){
																	displayMessage('divMessage','You Must Have a City.',true,true);
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
																		if($_POST['txtPolicy'] == '' && $_POST['hfType'] == "RSA" || strpos($_POST['txtPolicy'],"--") !== false && $_POST['hfType'] == "RSA" || strpos($_POST['txtPolicy'],"\\") !== false && $_POST['hfType'] == "RSA" || strpos($_POST['txtPolicy'],"^") !== false && $_POST['hfType'] == "RSA")
																		{
																			echo "<script type=\"text/javascript\">
																				window.onload=function(){
																					displayMessage('divMessage','You Must Have a Policy Number.',true,true);
																			}//end of window.onload=function()</script>";
																		}//end of if
																		else
																		{
																			if($_POST['txtBrokerName'] == '' && $_POST['hfType'] == "RSA" || strpos($_POST['txtBrokerName'],"--") !== false && $_POST['hfType'] == "RSA" || strpos($_POST['txtBrokerName'],"\\") !== false && $_POST['hfType'] == "RSA" || strpos($_POST['txtBrokerName'],"^") !== false && $_POST['hfType'] == "RSA")
																			{
																				echo "<script type=\"text/javascript\">
																					window.onload=function(){
																						displayMessage('divMessage','You Must Have a Broker Name.',true,true);
																				}//end of window.onload=function()</script>";
																			}//end of if
																			else
																			{
																				if($_POST['txtBrokerAddress'] == '' && $_POST['hfType'] == "RSA" || strpos($_POST['txtBrokerAddress'],"--") !== false && $_POST['hfType'] == "RSA" || strpos($_POST['txtBrokerAddress'],"\\") !== false && $_POST['hfType'] == "RSA" || strpos($_POST['txtBrokerAddress'],"^") !== false && $_POST['hfType'] == "RSA")
																				{
																					echo "<script type=\"text/javascript\">
																						window.onload=function(){
																						displayMessage('divMessage','You Must Have a Broker Address.',true,true);
																					}//end of window.onload=function()</script>";
																				}//end of if
																				else
																				{																				
																					if(strpos($_POST['txtCompany'],"--") !== false || strpos($_POST['txtCompany'],"\\") !== false || strpos($_POST['txtCompany'],"^") !== false)
																					{
																						echo "<script type=\"text/javascript\">
																							window.onload=function(){
																								displayMessage('divMessage','You Must Have a  Company.',true,true);
																						}//end of window.onload=function()</script>";
																					}//end of if
																					else
																					{
																					
																					if(strpos($_POST['txtSuiteNum'],"--") !== false || strpos($_POST['txtSuiteNum'],"\\") !== false || strpos($_POST['txtSuiteNum'],"^") !== false)
																					{
																						echo "<script type=\"text/javascript\">
																							window.onload=function(){
																								displayMessage('divMessage','You Must Have a Suite Number.',true,true);
																						}//end of window.onload=function()</script>";
																					}//end of if
																					else
																					{
																					
																					if(strpos($_POST['txtExt'],"--") !== false || strpos($_POST['txtExt'],"\\") !== false || strpos($_POST['txtExt'],"^") !== false)
																					{
																						echo "<script type=\"text/javascript\">
																							window.onload=function(){
																								displayMessage('divMessage','You Must Have a  Extension.',true,true);
																						}//end of window.onload=function()</script>";
																					}//end of if
																					else
																					{
																					
																					if(strpos($_POST['txtTitle'],"--") !== false || strpos($_POST['txtTitle'],"\\") !== false || strpos($_POST['txtTitle'],"^") !== false)
																					{
																						echo "<script type=\"text/javascript\">
																							window.onload=function(){
																								displayMessage('divMessage','You Must Have a  Title.',true,true);
																						}//end of window.onload=function()</script>";
																					}//end of if
																					else
																					{
																					
																					if($_POST['chkTerms'] == '')
																					{
																						echo "<script type=\"text/javascript\">
																							window.onload=function(){
																								displayMessage('divMessage','You Must Agree with the Terms and  Conditions.',true,true);
																						}//end of window.onload=function()</script>";
																					}//end of if
																					else
																					{
																					//inserts a new entry for this card for this user
																					mysql_select_db($database_conContinuty, $conContinuty);
																					mysql_query("INSERT INTO  users(login,passwd,firstname,lastname,title,company,phone,extension,fax,address,suite,city,state,zip,country,mailinglist,actbus,regbus,policy,brokername,brokeraddress,date,promosShortName,Solution)VALUES('".str_replace("'","''",$_POST['txtEMail'])."',MD5('".str_replace("'","''",$_POST['txtPassword'])."'),'".str_replace("'","''",$_POST['txtFName'])."','".str_replace("'","''",$_POST['txtLName'])."','".str_replace("'","''",$_POST['txtTitle'])."','".str_replace("'","''",$_POST['txtCompany'])."','".str_replace("'","''",$_POST['txtPhone'])."','".str_replace("'","''",$_POST['txtExt'])."','".str_replace("'","''",$_POST['txtFax'])."','".str_replace("'","''",$_POST['txtStreet'])."','".str_replace("'","''",$_POST['txtSuiteNum'])."','".str_replace("'","''",$_POST['txtCity'])."','".$_POST['cmbPro']."','".str_replace("'","''",$_POST['txtPostal'])."','".$_POST['cmbCountry']."','".$_POST['chkInform']."','".$_POST['chkActBus']."','".$_POST['chkRegBus']."','".str_replace("'","''",$_POST['txtPolicy'])."','".str_replace("'","''",$_POST['txtBrokerName'])."','".str_replace("'","''",$_POST['txtBrokerAddress'])."',Now(),'".$_POST['hfType']."','".$_POST['rdoSol']."')", $conContinuty) or die(mysql_error());
																					
																					//sends out the e-mail
																					sendEmail("Registration on Continuity site.","Dear ".$_POST['txtTitle']." ".$_POST['txtFName']." ".$_POST['txtLName'].",<br /><br /> Thank you for Your Registration on Continuity site. <br /> Your User Name: ".$_POST['txtEMail']."<br /> Your Password: ".$_POST['txtPassword']."<br /><br /><a href=\"http://www./index.php\">Click Here</a> to enter Continuity Inc's web site",$_POST['txtEMail'],"",'PurePHP/Swift EMail/Swift.php','PurePHP/Swift EMail/Swift/Connection/SMTP.php');
							
																					//log ins the user so that they can do the forms right away	
																					logOnSite(base64_encode($_POST['txtEMail']),base64_encode(base64_encode($_POST['txtPassword'])),"",$database_conContinuty, $conContinuty);
																					
																					//goes to the Successful
																					header("Location: Profile.php?section=Profile&amp;Footer=1&amp;Ed=".$_POST['rdoSol']);
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
}//end of if?>
<!-- InstanceEndEditable -->

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
              <h1><!-- InstanceBeginEditable name="h1Title" -->Sign Up - Build A Plan Today<!-- InstanceEndEditable --></h1>
          	</div><!-- end of Header -->
            <div class="customContent" id="divBasicContent">
                <!-- InstanceBeginEditable name="BasicContent" -->
                <form action="<?php echo $User; ?>" method="post" id="frmUser" class="frmBasics">
                    <div id="divSignUpContainer" class="customContainer">
                        <div id="divSignUpHeader" class="customHeader">
                            <div class="updateUserHeader customHeader divPageSubTitle" align="left">
                                <label class="lblPageTitle">Options with Continuty Inc</label>
                            </div>
                            <div id="divSignUpEditionSel" align="left">
                                <input type="radio" name="rdoSol" value="1" checked  /> Our Basic Edition
                                <br />
                                <input type="radio" name="rdoSol" value="2" /> Our Standard Edition
                                <br />
                                <input type="radio" name="rdoSol" value="3" /> Our Enterprise Edition
                            </div>
                        </div><!-- end of User Header -->
                        <div id="divSignUpContent" class="customContent">
                            <div class="updateUserContainer customContainer">
                                <div class="updateUserHeader customHeader divPageSubTitle" align="left">
                                    <label class="lblPageTitle">Registration</label>
                                </div>
                                <div class="divRegBody">
                                	<div id="divMessage" class="divBasicMessage"></div>
                                    <br />
                                    <div class="updateUserContent customContent">
                                        <label>Email address:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtEMail" type="text" size="23" value="<?php echo $_POST['txtEMail']; ?>" />
                                        <br/><label class="sectionNum">This will become your UserName</label>
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>Enter a Password:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtPassword" type="password" size="20" maxlength="15" value="" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>Re-Enter Password:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtRePassword" type="password" maxlength="15" value="" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>First Name:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtFName" type="text" value="<?php echo $_POST['txtFName']; ?>" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>Last Name:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtLName" type="text" value="<?php echo $_POST['txtLName']; ?>" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>Title:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtTitle" type="text" value="<?php echo $_POST['txtTitle']; ?>" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>Company:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtCompany" type="text" value="<?php echo $_POST['txtCompany']; ?>" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>Telephone number:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtPhone" type="text" value="<?php echo $_POST['txtPhone']; ?>" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>Extension:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtExt" type="text" value="<?php echo $_POST['txtExt']; ?>" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>Fax number:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtFax" type="text" value="<?php echo $_POST['txtFax']; ?>" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>Street address:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtStreet" type="text" value="<?php echo $_POST['txtStreet']; ?>" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>Suite number:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtSuiteNum" type="text" value="<?php echo $_POST['txtSuiteNum']; ?>" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>City:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtCity" type="text" value="<?php echo $_POST['txtCity']; ?>" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>State/Province:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <select name="cmbPro">
                                          <option value="AB" <?php if($_POST['cmbPro'] == "AB") echo "selected" ?>>Alberta</option>
                                          <option value="BC" <?php if($_POST['cmbPro'] == "BC") echo "selected" ?>>British Columbia</option>
                                          <option value="MA" <?php if($_POST['cmbPro'] == "MA") echo "selected" ?>>Manitoba</option>
                                          <option value="NB" <?php if($_POST['cmbPro'] == "NB") echo "selected" ?>>New Brunswick</option>
                                          <option value="NL" <?php if($_POST['cmbPro'] == "NL") echo "selected" ?>>Newfoundland and Labrador</option>
                                          <option value="NW" <?php if($_POST['cmbPro'] == "NW") echo "selected" ?>>Northwest Territories</option>
                                          <option value="NS" <?php if($_POST['cmbPro'] == "NS") echo "selected" ?>>Nova Scotia</option>
                                          <option value="NN" <?php if($_POST['cmbPro'] == "NN") echo "selected" ?>>Nunavut</option>
                                          <option value="ONT" <?php if($_POST['cmbPro'] == "ONT") echo "selected" ?>>Ontario</option>
                                          <option value="PEI" <?php if($_POST['cmbPro'] == "PEI") echo "selected" ?>>Prince Edward Island</option>
                                          <option value="QUB" <?php if($_POST['cmbPro'] == "QUB") echo "selected" ?>>Quebec</option>
                                          <option value="SAS" <?php if($_POST['cmbPro'] == "SAS") echo "selected" ?>>Saskatchewan</option>
                                          <option value="YUK" <?php if($_POST['cmbPro'] == "YUK") echo "selected" ?>>Yukon</option>	
                                          <option value="AL" <?php if($_POST['cmbPro'] == "AL") echo "selected" ?>>Alabama</option>
                                          <option value="AK" <?php if($_POST['cmbPro'] == "AK") echo "selected" ?>>Alaska</option>
                                          <option value="AZ" <?php if($_POST['cmbPro'] == "AZ") echo "selected" ?>>Arizona</option>
                                          <option value="AR" <?php if($_POST['cmbPro'] == "AR") echo "selected" ?>>Arkansas</option>
                                          <option value="CA" <?php if($_POST['cmbPro'] == "CA") echo "selected" ?>>California</option>
                                          <option value="CO" <?php if($_POST['cmbPro'] == "CO") echo "selected" ?>>Colorado</option>
                                          <option value="CT" <?php if($_POST['cmbPro'] == "CT") echo "selected" ?>>Connecticut</option>
                                          <option value="DE" <?php if($_POST['cmbPro'] == "DE") echo "selected" ?>>Delaware</option>
                                          <option value="DC" <?php if($_POST['cmbPro'] == "DC") echo "selected" ?>>District of Columbia</option>
                                          <option value="FL" <?php if($_POST['cmbPro'] == "FL") echo "selected" ?>>Florida</option>
                                          <option value="GA" <?php if($_POST['cmbPro'] == "GA") echo "selected" ?>>Georgia</option>
                                          <option value="HI" <?php if($_POST['cmbPro'] == "HI") echo "selected" ?>>Hawaii</option>
                                          <option value="ID" <?php if($_POST['cmbPro'] == "ID") echo "selected" ?>>Idaho</option>
                                          <option value="IL" <?php if($_POST['cmbPro'] == "IL") echo "selected" ?>>Illinois</option>
                                          <option value="IN" <?php if($_POST['cmbPro'] == "IN") echo "selected" ?>>Indiana</option>
                                          <option value="IA" <?php if($_POST['cmbPro'] == "IA") echo "selected" ?>>Iowa</option>
                                          <option value="KS" <?php if($_POST['cmbPro'] == "KS") echo "selected" ?>>Kansas</option>
                                          <option value="KY" <?php if($_POST['cmbPro'] == "KY") echo "selected" ?>>Kentucky</option>
                                          <option value="LA" <?php if($_POST['cmbPro'] == "LA") echo "selected" ?>>Louisiana</option>
                                          <option value="ME" <?php if($_POST['cmbPro'] == "ME") echo "selected" ?>>Maine</option>
                                          <option value="MD" <?php if($_POST['cmbPro'] == "MD") echo "selected" ?>>Maryland</option>
                                          <option value="MA" <?php if($_POST['cmbPro'] == "MA") echo "selected" ?>>Massachusetts</option>
                                          <option value="MI" <?php if($_POST['cmbPro'] == "MI") echo "selected" ?>>Michigan</option>
                                          <option value="MN" <?php if($_POST['cmbPro'] == "MN") echo "selected" ?>>Minnesota</option>
                                          <option value="MS" <?php if($_POST['cmbPro'] == "MS") echo "selected" ?>>Mississippi</option>
                                          <option value="MO" <?php if($_POST['cmbPro'] == "MO") echo "selected" ?>>Missouri</option>
                                          <option value="MT" <?php if($_POST['cmbPro'] == "MT") echo "selected" ?>>Montana</option>
                                          <option value="NE" <?php if($_POST['cmbPro'] == "NE") echo "selected" ?>>Nebraska</option>
                                          <option value="NV" <?php if($_POST['cmbPro'] == "NV") echo "selected" ?>>Nevada</option>
                                          <option value="NH" <?php if($_POST['cmbPro'] == "NH") echo "selected" ?>>New Hampshire</option>
                                          <option value="NJ" <?php if($_POST['cmbPro'] == "NJ") echo "selected" ?>>New Jersey</option>
                                          <option value="NM" <?php if($_POST['cmbPro'] == "NM") echo "selected" ?>>New Mexico</option>
                                          <option value="NY" <?php if($_POST['cmbPro'] == "NY") echo "selected" ?>>New York</option>
                                          <option value="NC" <?php if($_POST['cmbPro'] == "NC") echo "selected" ?>>North Carolina</option>
                                          <option value="ND" <?php if($_POST['cmbPro'] == "ND") echo "selected" ?>>North Dakota</option>
                                          <option value="OH" <?php if($_POST['cmbPro'] == "OH") echo "selected" ?>>Ohio</option>
                                          <option value="OK" <?php if($_POST['cmbPro'] == "OK") echo "selected" ?>>Oklahoma</option>
                                          <option value="OR" <?php if($_POST['cmbPro'] == "OR") echo "selected" ?>>Oregon</option>
                                          <option value="PA" <?php if($_POST['cmbPro'] == "PA") echo "selected" ?>>Pennsylvania</option>
                                          <option value="RI" <?php if($_POST['cmbPro'] == "RI") echo "selected" ?>>Rhode Island</option>
                                          <option value="SC" <?php if($_POST['cmbPro'] == "SC") echo "selected" ?>>South Carolina</option>
                                          <option value="SD" <?php if($_POST['cmbPro'] == "SD") echo "selected" ?>>South Dakota</option>
                                          <option value="TN" <?php if($_POST['cmbPro'] == "TN") echo "selected" ?>>Tennessee</option>
                                          <option value="TX" <?php if($_POST['cmbPro'] == "TX") echo "selected" ?>>Texas</option>
                                          <option value="UT" <?php if($_POST['cmbPro'] == "UT") echo "selected" ?>>Utah</option>
                                          <option value="VT" <?php if($_POST['cmbPro'] == "VT") echo "selected" ?>>Vermont</option>
                                          <option value="VA" <?php if($_POST['cmbPro'] == "VA") echo "selected" ?>>Virginia</option>
                                          <option value="WA" <?php if($_POST['cmbPro'] == "WA") echo "selected" ?>>Washington</option>
                                          <option value="WV" <?php if($_POST['cmbPro'] == "WV") echo "selected" ?>>West Virginia</option>
                                          <option value="WI" <?php if($_POST['cmbPro'] == "WI") echo "selected" ?>>Wisconsin</option>
                                          <option value="WY" <?php if($_POST['cmbPro'] == "WY") echo "selected" ?>>Wyoming</option>
                                        </select>
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>                  
                                    <div class="updateUserContent customContent">
                                        <label>Zip/Postal code:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <input name="txtPostal" type="text" value="<?php echo $_POST['txtPostal']; ?>" />
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                    <div class="updateUserContent customContent">
                                        <label>Country:</label>
                                    </div>
                                    <div class="updateUserDivider">&nbsp;</div>
                                    <div class="updateUserNavigation customNavigation">
                                        <select name="cmbCountry">
                                          <option value="CA" <?php if($_POST['cmbCountry'] == "CA") echo "selected" ?>>Canada</option>
                                          <option value="US" <?php if($_POST['cmbCountry'] == "US") echo "selected" ?>>United States</option>
                                        </select>
                                    </div>
                                    <div class="updateUserFooter customFooter"></div>
                                </div><!-- end of divRegBody -->
                
                
                            <?php 
                           	//checks if the sign up is in RSA mode if so then display there Policy fields and pricing
               				if($_GET['type'] == "RSA" || $_POST['hfType'] == "RSA")
                                echo "<div class=\"updateUserHeader customHeader formhead sectionNum\"><label>Policy Details</label></div>
                                    <div class=\"divRegBody\">
                                        <div class=\"updateUserContent customContent\">
                                            <label>Policy #:</label>
                                        </div>
                                        <div class=\"updateUserDivider\">&nbsp;</div>
                                        <div class=\"updateUserNavigation customNavigation\">
                                            <input name=\"txtPolicy\" type=\"text\" value=\"".$_POST['txtPolicy']."\" />
                                        </div>
                                        <div class=\"updateUserFooter customFooter\"></div>
                                        <div class=\"updateUserContent customContent\">
                                            <label>Broker Name:</label>
                                        </div>
                                        <div class=\"updateUserDivider\">&nbsp;</div>
                                        <div class=\"updateUserNavigation customNavigation\">
                                            <input name=\"txtBrokerName\" type=\"text\" value=\"".$_POST['txtBrokerName']."\" />
                                        </div>
                                        <div class=\"updateUserFooter customFooter\"></div>
                                        <div class=\"updateUserContent customContent\">
                                            <label>Broker Address:</label>
                                        </div>
                                        <div class=\"updateUserDivider\">&nbsp;</div>
                                        <div class=\"updateUserNavigation customNavigation\">
                                            <input name=\"txtBrokerAddress\" type=\"text\" value=\"".$_POST['txtBrokerAddress']."\" />
                                        </div>
                                        <div class=\"updateUserFooter customFooter\"></div>
                                    </div>";
                            ?>
                            </div><!-- end of updateUserContainer -->            
                        </div><!-- end of User Content -->
                        <div id="divSignUpFooter" class="customFooter">
                        	<div class="updateUserHeader customHeader divPageSubTitle" align="left">
                                <label class="lblPageTitle">Final Terms and Payment</label>
                            </div>
                        	<div>
                        		<label>Once this form is submited you will be automatically Registered. You will recieve an invoice and will have 30 days to pay in full. For further information on billing please contact <a href="mailto:" class="lblBasicColor aUnderline"></a>. If you have selected a Half-Day Training and Certification Program, a Continuity Inc. Representitive will contact you as soon as possible.</label>
                            	<br /><br />
                                <input name="chkTerms" type="checkbox" value="1" /> <label>I agree to the terms and conditions for use of the Continuity Inc. website.</label>
                            </div>
                            <br/>
                            <div>
                                <input name="chkInform" type="checkbox" value="1" <?php if($_POST['chkInform'] == 1) echo "checked" ?>/> <label>Keep me informed about Continuity inc. promotions and New Releases.</label>
                            </div>
                        </div><!-- end of User footer -->
                    </div><!-- end of User Container -->
                   <div id="divSignUpSubmitFooter">
                        <input type="hidden" name="hfSubmit" value="1" />
                        <input type="hidden" name="hfSection" value="SignUp" />
                        <input type="hidden" name="hfcustomFooter" value="1" />
                        <input type="hidden" name="hfUserID" value="<?php echo $UserID; ?>" />
                        <input type="hidden" name="hfType" value="<?php 
						if ($_POST['hfType'] != "")
							echo $_POST['hfType'];
						else
							echo $_GET['type']; ?>" />
                        <input name="cmdSend" type="submit" value="Sign Up" id="cmdSend" />
                    </div>
                </form>
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
