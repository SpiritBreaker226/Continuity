<?php require_once('PurePHP/LoginControl.php');?>
<?php require_once('Connections/conContinuty.php'); 

/* sets the color of this Step Section
	1 = Basic Colour
	2 = Standard Colour
	3 = Continuity Colour
	4 = Enterprise Color
*/
	
//checks if the user whats to log off
if (isset($_GET['logoff']))
	logOff(substr($_SERVER['REQUEST_URI'],1));
		
//checks if the user is logged in as only those that have an account can do the forms
if (getUserID() == 0)
	header("Location: LogIn.php?section=LogIn&Footer=1&accesscheck=Profile.php");
		
//forces the pages to come here to process the page
$User = $_SERVER['PHP_SELF'];
$UserID = getUserID();//holds the user ID
$strEdition = "Basic";//Holds the Edition the uses has selected
$strBackgroudColor = "lblBasicBackgroundColor";//Holds the Current Solution Color Backgorund
$strColor = "lblBasicColor";//Holds the Current Solution Color

//checks if the user has bought a new solution for the Solutions.php and if so then updates the 
if($_GET['Ed'] <> "")
{
	if(strpos($_GET['Ed'],"--") === false || strpos($_GET['Ed'],"\\") === false || strpos($_GET['Ed'],"^") === false)
	{
		//updates the solution this user
		mysql_select_db($database_conContinuty, $conContinuty);
		mysql_query("UPDATE users SET Solution = ".$_GET['Ed']." WHERE users.id=".$UserID, $conContinuty) or die("Update Solution User ".mysql_error());
	}//end of if
}//end of if

mysql_select_db($database_conContinuty, $conContinuty);
$LoginRS = mysql_query("SELECT * FROM users WHERE users.id=".$UserID, $conContinuty) or die("Get User Info".mysql_error());
$row_loginFoundUser = mysql_fetch_assoc($LoginRS);

//for the long sign up process for the basic/solution/enterprise solutions offer by this site
//gets the last location the user was at 
mysql_select_db($database_conContinuty, $conContinuty);
$rsLastLoc = mysql_query("SELECT * FROM userLastLoc WHERE userLastLoc.id=".$UserID, $conContinuty) or die(mysql_error());
$row_lastLoc = mysql_fetch_assoc($rsLastLoc);
$total_lastLoc = mysql_num_rows($rsLastLoc);

//checks whick version the users is going to used
if($row_loginFoundUser['Solution'] == 3)
{
	$strEdition = "Enterprise";
	$strBackgroudColor = "lblEnterBackgroundColor";
	$strColor = "lblEnterColor";
}//end of if
else if ($row_loginFoundUser['Solution'] == 2)
{
	$strEdition = "Standard";
	$strBackgroudColor = " lblStandardBackgroundColor";
	$strColor = "lblStandardColor";
}//end of else if?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/BasicTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php echo getUserName()."'s Profile"; ?> - Continuity Inc. - Disaster Recovery Solutions</title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" type="text/css" href="CSS/MasterCSS.css" media="screen" />
<script src="javascript/MainJS.js" type="text/javascript"></script>
<?php require_once('PurePHP/LoginControl.php');?>
<?php require_once('Connections/conContinuty.php'); ?>
<!-- InstanceBeginEditable name="head" --><?php 
//checks if the fields that need data do have some data in them
if($_POST['hfSubmit'] != '' && $_POST['hfAddUser'] == '' && $_POST['hfRemoveUser'] == '')
{
	if($_POST['txtEMail'] == '')
	{
		echo "<script type=\"text/javascript\">
			window.onload=function(){
				displayMessage('divProfileMessage','You Must Have an E-Mail.',true,true);
				toggleLayer('divEditAccount','divGrayBG');
		}//end of window.onload=function()</script>";
	}//end of if
	else
	{
		if(!preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i",$_POST['txtEMail']))
		{
			echo "<script type=\"text/javascript\">
				window.onload=function(){
					displayMessage('divProfileMessage','Your E-Mail Address Format is not valid',true,true);
					toggleLayer('divEditAccount','divGrayBG');
			}//end of window.onload=function()</script>";
		}//end of if
		else
		{
			if($_POST['txtPassword'] != $_POST['txtRePassword'] && $_POST['txtPassword'] != '')
			{
				echo "<script type=\"text/javascript\">
					window.onload=function(){
						displayMessage('divProfileMessage','Your Password and Confirm Password Does Not Match',true,true);
						toggleLayer('divEditAccount','divGrayBG');
				}//end of window.onload=function()</script>";
			}//end of if
			else
			{
				if($_POST['txtFName'] == '' || strpos($_POST['txtFName'],"--") !== false || strpos($_POST['txtFName'],"\\") !== false  || strpos($_POST['txtFName'],"^") !== false)
				{
					echo "<script type=\"text/javascript\">
				
						window.onload=function(){
							displayMessage('divProfileMessage','You Must Have a First Name.',true,true);
							toggleLayer('divEditAccount','divGrayBG');
						}//end of window.onload=function()</script>";
				}//end of if
				else
				{
					if($_POST['txtLName'] == '' || strpos($_POST['txtLName'],"--") !== false || strpos($_POST['txtLName'],"\\") !== false || strpos($_POST['txtLName'],"^") !== false)
					{
						echo "<script type=\"text/javascript\">
							window.onload=function(){
								displayMessage('divProfileMessage','You Must Have a Last Name.',true,true);
								toggleLayer('divEditAccount','divGrayBG');
						}//end of window.onload=function()</script>";
					}//end of if
					else
					{
						if($_POST['txtPhone'] == '')
						{
							echo "<script type=\"text/javascript\">
								window.onload=function(){
									displayMessage('divProfileMessage','You Must Have a Phone Number.',true,true);
									toggleLayer('divEditAccount','divGrayBG');
							}//end of window.onload=function()</script>";
						}//end of if
						else
						{
							if(!preg_match('/^(\(?[0-9]{3,3}\)?|[0-9]{3,3}[-.]?)[ ]?[0-9]{3,3}[-. ]?[0-9]{4,4}$/',$_POST['txtPhone']) && $_POST['txtPhone'] != '')
							{
								echo "<script type=\"text/javascript\">
									window.onload=function(){
										displayMessage('divProfileMessage','Your Phone Number Format is not valid. ((###) ###-####)',true,true);
										toggleLayer('divEditAccount','divGrayBG');
								}//end of window.onload=function()</script>";
							}//end of if
							else
							{
								if(!preg_match('/^(\(?[0-9]{3,3}\)?|[0-9]{3,3}[-.]?)[ ]?[0-9]{3,3}[-. ]?[0-9]{4,4}$/',$_POST['txtFax']) && $_POST['txtFax'] != '')
								{
									echo "<script type=\"text/javascript\">
										window.onload=function(){
											displayMessage('divProfileMessage','Your Fax Number Format is not valid. ((###) ###-####)',true,true);
											toggleLayer('divEditAccount','divGrayBG');
									}//end of window.onload=function()</script>";
								}//end of if
								else
								{
									if($_POST['txtStreet'] == '' || strpos($_POST['txtStreet'],"--") !== false || strpos($_POST['txtStreet'],"\\") !== false || strpos($_POST['txtStreet'],"^") !== false)
									{
										echo "<script type=\"text/javascript\">
											window.onload=function(){
												displayMessage('divProfileMessage','You Must Have a Address.',true,true);
												toggleLayer('divEditAccount','divGrayBG');
										}//end of window.onload=function()</script>";
									}//end of if
									else
									{
										if($_POST['txtCity'] == '' || strpos($_POST['txtCity'],"--") !== false || strpos($_POST['txtCity'],"\\") !== false || strpos($_POST['txtCity'],"^") !== false)
										{
											echo "<script type=\"text/javascript\">
												window.onload=function(){
													displayMessage('divProfileMessage','You Must Have a City.',true,true);
													toggleLayer('divEditAccount','divGrayBG');
											}//end of window.onload=function()</script>";
										}//end of if
										else
										{
											if($_POST['txtPostal'] == '')
											{
												echo "<script type=\"text/javascript\">
													window.onload=function(){
														displayMessage('divProfileMessage','You Must Have a Zip/Postal Code.',true,true);
														toggleLayer('divEditAccount','divGrayBG');
												}//end of window.onload=function()</script>";
											}//end of if
											else
											{
												if(!preg_match("/^[a-z]\d[a-z] ?\d[a-z]\d$/i",$_POST['txtPostal']) && $_POST['cmbCountry'] == 'CA')
												{
													echo "<script type=\"text/javascript\">
														window.onload=function(){
															displayMessage('divProfileMessage','Your Postal Code Format is not valid',true,true);
															toggleLayer('divEditAccount','divGrayBG');
													}//end of window.onload=function()</script>";
												}//end of if
												else
												{
													if(!preg_match("/^\d{5}$/",$_POST['txtPostal']) && $_POST['cmbCountry'] == 'US')
													{
														echo "<script type=\"text/javascript\">
															window.onload=function(){
																displayMessage('divProfileMessage','Your Zip Code Format is not valid',true,true);
																toggleLayer('divEditAccount','divGrayBG');
														}//end of window.onload=function()</script>";
													}//end of if
													else
													{
														if($_POST['txtPolicy'] == '' && $_POST['hfType'] == "RSA" || strpos($_POST['txtPolicy'],"--") !== false && $_POST['hfType'] == "RSA" || strpos($_POST['txtPolicy'],"\\") !== false && $_POST['hfType'] == "RSA" || strpos($_POST['txtPolicy'],"^") !== false && $_POST['hfType'] == "RSA")
														{
															echo "<script type=\"text/javascript\">
																window.onload=function(){
																	displayMessage('divProfileMessage','You Must Have a Policy Number.',true,true);
																	toggleLayer('divEditAccount','divGrayBG');
															}//end of window.onload=function()</script>";
														}//end of if
														else
														{
															if($_POST['txtBrokerName'] == '' && $_POST['hfType'] == "RSA" || strpos($_POST['txtBrokerName'],"--") !== false && $_POST['hfType'] == "RSA" || strpos($_POST['txtBrokerName'],"\\") !== false && $_POST['hfType'] == "RSA" || strpos($_POST['txtBrokerName'],"^") !== false && $_POST['hfType'] == "RSA")
															{
																echo "<script type=\"text/javascript\">
																	window.onload=function(){
																		displayMessage('divProfileMessage','You Must Have a Broker Name.',true,true);
																		toggleLayer('divEditAccount','divGrayBG');
																}//end of window.onload=function()</script>";
															}//end of if
															else
															{
																if($_POST['txtBrokerAddress'] == '' && $_POST['hfType'] == "RSA" || strpos($_POST['txtBrokerAddress'],"--") !== false && $_POST['hfType'] == "RSA" || strpos($_POST['txtBrokerAddress'],"\\") !== false && $_POST['hfType'] == "RSA" || strpos($_POST['txtBrokerAddress'],"^") !== false && $_POST['hfType'] == "RSA")
																{
																	echo "<script type=\"text/javascript\">
																		window.onload=function(){
																		displayMessage('divProfileMessage','You Must Have a Broker Address.',true,true);
																		toggleLayer('divEditAccount','divGrayBG');
																	}//end of window.onload=function()</script>";
																}//end of if
																else
																{
																	mysql_select_db($database_conContinuty, $conContinuty);
																	$rsUser = mysql_query("SELECT * FROM users WHERE users.login = '".$_POST['txtEMail']."' AND users.id != ".$UserID, $conContinuty) or die(mysql_error());
																	$totalRows_rsUser = mysql_num_rows($rsUser);
									
																	//checks if the fields for the sign up page is current
																	if($totalRows_rsUser > 0)
																	{
																		echo "<script type=\"text/javascript\">
																		var oldonload=window.onload;//holds any prevs onload function from the js file
																
																		window.onload=function(){
																			if(typeof(oldonload)=='function')
																				oldonload();
																
																			displayMessage('divProfileMessage','Your E-Mail Address is Already in Our Database',true,true);
																			toggleLayer('divEditAccount','divGrayBG');
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
																			displayMessage('divProfileMessage','Your E-Mail Address is Already Attach to this Account',true,true);
																			toggleLayer('divEditAccount','divGrayBG');
																			}//end of window.onload=function()</script>";
																		}//end of if
																		else
																		{
																			if(strpos($_POST['txtCompany'],"--") !== false || strpos($_POST['txtCompany'],"\\") !== false || strpos($_POST['txtCompany'],"^") !== false)
																			{
																				echo "<script type=\"text/javascript\">
																					window.onload=function(){
																						displayMessage('divProfileMessage','You Must Have a  Company.',true,true);
																						toggleLayer('divEditAccount','divGrayBG');
																				}//end of window.onload=function()</script>";
																			}//end of if
																			else
																			{
																			
																				if(strpos($_POST['txtSuiteNum'],"--") !== false || strpos($_POST['txtSuiteNum'],"\\") !== false || strpos($_POST['txtSuiteNum'],"^") !== false)
																				{
																					echo "<script type=\"text/javascript\">
																						window.onload=function(){
																							displayMessage('divProfileMessage','You Must Have a Suite Number.',true,true);
																							toggleLayer('divEditAccount','divGrayBG');
																					}//end of window.onload=function()</script>";
																				}//end of if
																				else
																				{
																					if(strpos($_POST['txtExt'],"--") !== false || strpos($_POST['txtExt'],"\\") !== false || strpos($_POST['txtExt'],"^") !== false)
																					{
																						echo "<script type=\"text/javascript\">
																							window.onload=function(){
																								displayMessage('divProfileMessage','You Must Have a  Extension.',true,true);
																								toggleLayer('divEditAccount','divGrayBG');
																						}//end of window.onload=function()</script>";
																					}//end of if
																					else
																					{
																						if(strpos($_POST['txtTitle'],"--") !== false || strpos($_POST['txtTitle'],"\\") !== false || strpos($_POST['txtTitle'],"^") !== false)
																						{
																							echo "<script type=\"text/javascript\">
																								window.onload=function(){
																									displayMessage('divProfileMessage','You Must Have a  Title.',true,true);
																									toggleLayer('divEditAccount','divGrayBG');
																							}//end of window.onload=function()</script>";
																						}//end of if
																						else
																						{																																																																				
																							$strNewPassword = "";//holds the new passowrd
			
																							//changes if the user whats to update there password
																							if($_POST['txtPassword'] != '')
																								$strNewPassword = ",passwd = MD5('".str_replace("'","''",$_POST['txtPassword'])."')";
			
																							//inserts a new entry for this card for this user
																							mysql_select_db($database_conContinuty, $conContinuty);
																							mysql_query("UPDATE users SET login = '".str_replace("'","''",$_POST['txtEMail'])."'".$strNewPassword.",firstname = '".str_replace("'","''",$_POST['txtFName'])."',lastname = '".str_replace("'","''",$_POST['txtLName'])."',title = '".str_replace("'","''",$_POST['txtTitle'])."',company = '".str_replace("'","''",$_POST['txtCompany'])."',phone = '".str_replace("'","''",$_POST['txtPhone'])."',extension = '".str_replace("'","''",$_POST['txtExt'])."',fax = '".str_replace("'","''",$_POST['txtFax'])."',address = '".str_replace("'","''",$_POST['txtStreet'])."',suite = '".str_replace("'","''",$_POST['txtSuiteNum'])."',city = '".str_replace("'","''",$_POST['txtCity'])."',state = '".$_POST['cmbPro']."',zip = '".str_replace("'","''",$_POST['txtPostal'])."',country = '".$_POST['cmbCountry']."',mailinglist = '".$_POST['chkInform']."',actbus = '".$_POST['chkActBus']."',regbus = '".$_POST['chkRegBus']."',policy = '".str_replace("'","''",$_POST['txtPolicy'])."',brokername = '".str_replace("'","''",$_POST['txtBrokerName'])."',brokeraddress = '".str_replace("'","''",$_POST['txtBrokerAddress'])."',Users = '".$row_loginFoundUser['Users']."' WHERE id=".$UserID, $conContinuty) or die(mysql_error());
																							
																							//updates the rows in the selection
																							mysql_select_db($database_conContinuty, $conContinuty);
																							$LoginRS = mysql_query("SELECT * FROM users WHERE users.id=".$UserID, $conContinuty) or die(mysql_error());
																							$row_loginFoundUser = mysql_fetch_assoc($LoginRS);
																									
																							echo "<script type=\"text/javascript\">
																								window.onload=function(){
																									displayMessage('divProfileMessage','Your Profile has been Updated',true,true);
																									toggleLayer('divEditAccount','divGrayBG');
																								}//end of window.onload=function()</script>";
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
}//end of if
else if($_POST['hfAddUser'] == '1')
{
	if($_POST['txtAddEMail'] == '')
	{
		echo "<script type=\"text/javascript\">
			window.onload=function(){
				displayMessage('divAddMessage','Must Have an E-Mail.',true,true);
				toggleLayer('divAddUser','divGrayBG');
		}//end of window.onload=function()</script>";
	}//end of if
	else
	{
		if(!preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i",$_POST['txtAddEMail']))
		{
			echo "<script type=\"text/javascript\">
				window.onload=function(){
					displayMessage('divAddMessage','E-Mail Address Format is not valid',true,true);
					toggleLayer('divAddUser','divGrayBG');
			}//end of window.onload=function()</script>";
		}//end of if
		else
		{
			mysql_select_db($database_conContinuty, $conContinuty);
			$rsUser = mysql_query("SELECT * FROM users WHERE users.login = '".$_POST['txtAddEMail']."'", $conContinuty) or die(mysql_error());
			$totalRows_rsUser = mysql_num_rows($rsUser);

			//checks if the fields for the sign up page is current
			if($totalRows_rsUser > 0)
			{
					echo "<script type=\"text/javascript\">
					var oldonload=window.onload;//holds any prevs onload function from the js file
			
					window.onload=function(){
						if(typeof(oldonload)=='function')
							oldonload();
			
						displayMessage('divAddMessage','E-Mail Address is Already in Our Database',true,true);
						toggleLayer('divAddUser','divGrayBG');
					}//end of window.onload=function()</script>";
			}//end of if
			else
			{
				mysql_select_db($database_conContinuty, $conContinuty);
				$rsUser = mysql_query("SELECT * FROM users WHERE Users LIKE '%".$_POST['txtAddEMail']."%'", $conContinuty) or die(mysql_error());
				$totalRows_rsUser = mysql_num_rows($rsUser);
				
				//checks if the fields for the sign up page is current
				if($totalRows_rsUser > 0)
				{
					echo "<script type=\"text/javascript\">			
						window.onload=function(){			
							displayMessage('divAddMessage','That User\'s E-Mail Address is Already Attach to this Account',true,true);
							toggleLayer('divAddUser','divGrayBG');
					}//end of window.onload=function()</script>";
				}//end of if
				else
				{
					if($_POST['txtAddPassword'] == '')
					{
						echo "<script type=\"text/javascript\">
							window.onload=function(){
								displayMessage('divAddMessage','You Must Have a Password',true,true);
								toggleLayer('divAddUser','divGrayBG');
						}//end of window.onload=function()</script>";
					}//end of if
					else
					{
						if($_POST['txtAddRePassword'] == '')
						{
							echo "<script type=\"text/javascript\">
								window.onload=function(){
									displayMessage('divAddMessage','You Must Have a Confirm Password',true,true);
									toggleLayer('divAddUser','divGrayBG');
							}//end of window.onload=function()</script>";
						}//end of if
						else
						{
							if($_POST['txtAddPassword'] != $_POST['txtAddRePassword'])
							{
								echo "<script type=\"text/javascript\">
									window.onload=function(){
										displayMessage('divAddMessage','Password and Confirm Password Does Not Match',true,true);
										toggleLayer('divAddUser','divGrayBG');
								}//end of window.onload=function()</script>";
							}//end of if
							else
							{
								mysql_query("UPDATE users SET Users = '".str_replace("'","''",$_POST['txtAddEMail'])."~".md5($_POST['txtAddPassword'])."#".$row_loginFoundUser['Users']."' WHERE id = ".$UserID, $conContinuty) or die(mysql_error());
								
								//updates the rows in the selection
								mysql_select_db($database_conContinuty, $conContinuty);
								$LoginRS = mysql_query("SELECT * FROM users WHERE users.id=".$UserID, $conContinuty) or die(mysql_error());
								$row_loginFoundUser = mysql_fetch_assoc($LoginRS);
								
								echo "<script type=\"text/javascript\">
									window.onload=function(){
										displayMessage('divAddMessage','".$_POST['txtAddEMail']." has been Added',true,true);
										toggleLayer('divAddUser','divGrayBG');
									}//end of window.onload=function()
								</script>";
							}//end of if else
						}//end of if else
					}//end of if else
				}//end of if else
			}//end of if else
		}//end of if else
	}//end of if else
}//end of if
else if($_POST['hfRemoveUser'] == '0')
{
	if($_POST['cmbUsers'] == '')
	{
		echo "<script type=\"text/javascript\">
			window.onload=function(){
				displayMessage('divProfileMessage','You Must Selected a User to Be Removed.',true,true);
				toggleLayer('divEditAccount','divGrayBG');
		}//end of window.onload=function()</script>";
	}//end of if
	else
	{
		$arrUser = split("#",$row_loginFoundUser['Users']);//holds the array of Users the account	
		$strAddUser = "";//holds the add user
												
		//goes around each users that is connect to this account
		//for the user to remove it from the account
		foreach ($arrUser as $arrUserValue)
		{
			//checks if the row in arrUser is not blank
			//blank line is in here as it is the last item
			if($arrUserValue != "")
			{
				$arrUserDetails = split("~",$arrUserValue);//holds the array fot details of the Users
				//checks if the user is the one that will not be added to the new Users field for the Account
				if($arrUserDetails[0] != $_POST['cmbUsers'])
					$strAddUser = $arrUserDetails[0]."~".md5($arrUserDetails[1])."#".$strAddUser;
			}//end of if
		}//end of foreach
			
		mysql_query("UPDATE users SET Users = '".str_replace("'","''",$strAddUser)."' WHERE id = ".$UserID, $conContinuty) or die(mysql_error());
		
		//updates the rows in the selection
		mysql_select_db($database_conContinuty, $conContinuty);
		$LoginRS = mysql_query("SELECT * FROM users WHERE users.id=".$UserID, $conContinuty) or die(mysql_error());
		$row_loginFoundUser = mysql_fetch_assoc($LoginRS);
		
		echo "<script type=\"text/javascript\">
			window.onload=function(){
				displayMessage('divProfileMessage','".$_POST['cmbUsers']." has been Removed',true,true);
				toggleLayer('divEditAccount','divGrayBG');
			}//end of window.onload=function()
		</script>";
	}//end of else
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
              <h1><!-- InstanceBeginEditable name="h1Title" --><?php echo getUserName()."'s Profile"; ?> - Continuity Inc.<!-- InstanceEndEditable --></h1>
          	</div><!-- end of Header -->
            <div class="customContent" id="divBasicContent">
                <!-- InstanceBeginEditable name="BasicContent" -->
                <div class="customContainer"  id="divProfileContainer">
                	<div class="customHeader" id="divProfileHeader">
                    	<div class="customContainer lblFontBold divFloatingMainTitle <?php echo $strBackgroudColor; ?>" id="divHeaderProfileContainer">
                        	<div class="customContent" id="divHeaderProfileContent">
                            	<label><span class="<?php
										//checks if the Solution is Starnd as the Color will of thise Solution will
										//match the one in the M change to a different color
										if ($row_loginFoundUser['Solution'] == 2)
											echo "lblEnterColor";
										else 
											echo "lblFontRed";?> lblFontSize24">M</span>y Continuity Plans</label>
                            </div>
                            <div class="customNavigation" id="divHeaderProfileNavigation">
                            	<label><?php echo $strEdition; ?> Edition</label>
                            </div>
                            <div class="customFooter" id="divHeaderProfileFooter"></div>
                        </div>                    
                    </div>
                    <div class="customContent" id="divProfileContent">
                        <label><span class="lblFontBold lblFontSize14">Step 1</span><br />
                        <span class="lblBasicColor">Reduce The Risk</span></label>
                        <br /><br />						
						<?php mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2scope WHERE C2scope.UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
						
						if($total_Form == 0)
	                        echo "<label>Project Summary</label>";
                        else
							echo "<a href=\"Forms/ReduceTheRisk/Scope.php?SubFolder=2&Section=1\" class=\"lblFontColorBlack aUnderline\">Project Summary</a>";
						echo "<br />";
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Employee WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
								
						if($total_Form == 0)
	                        echo "<label>Employee &amp; Emergency Contacts</label>";
                        else
							echo "<a href=\"Forms/ReduceTheRisk/Employee.php?SubFolder=2&Section=2\" class=\"lblFontColorBlack aUnderline\">Employee &amp; Emergency Contacts</a>";
						echo "<br />";
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Information WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
							
                        if($total_Form == 0)
	                        echo "<label>Information Technology</label>";
                        else
							echo "<a href=\"Forms/ReduceTheRisk/Information.php?SubFolder=2&Section=3\" class=\"lblFontColorBlack aUnderline\">Information Technology</a>";
						echo "<br />";
							
						if($row_loginFoundUser['Solution'] == 2)
						{
							mysql_select_db($database_conContinuty, $conContinuty);
							$rsForm = mysql_query("SELECT * FROM C2BusinessImpact WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
							$row_Form = mysql_fetch_assoc($rsForm);
							$total_Form = mysql_num_rows($rsForm);
						
							if($total_Form == 0)
								echo "<label>Business Impact Analysis</label>";
							else
								echo "<a href=\"Forms/ReduceTheRisk/BusinessImpact.php?SubFolder=2&Section=18\" class=\"lblFontColorBlack aUnderline\">Business Impact Analysis</a>";
							echo "<br />";
						}//end of if
                        
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Crisis WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
						
						if($total_Form == 0)
	                        echo "<label>Crisis Communications</label>";
                        else
							echo "<a href=\"Forms/ReduceTheRisk/Crisis.php?SubFolder=2&Section=4\" class=\"lblFontColorBlack aUnderline\">Crisis Communications</a>";
						echo "<br />";						
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Logistics WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);	
						
                        if($total_Form == 0)
	                        echo "<label>Logistics</label>";
                        else
							echo "<a href=\"Forms/ReduceTheRisk/Logistics.php?SubFolder=2&Section=5\" class=\"lblFontColorBlack aUnderline\">Logistics</a>";
						echo "<br />";
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Alternate WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
						
                        if($total_Form == 0)
	                        echo "<label>Alternate Location &amp; Suppliers</label>";
                        else
							echo "<a href=\"Forms/ReduceTheRisk/Alternate.php?SubFolder=2&Section=6\" class=\"lblFontColorBlack aUnderline\">Alternate Location &amp; Suppliers</a>";
						echo "<br />";
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Salvage WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);

                        if($total_Form == 0)
	                        echo "<label>Salvage &amp; Security</label>";
                        else
							echo "<a href=\"Forms/ReduceTheRisk/Salvage.php?SubFolder=2&Section=7\" class=\"lblFontColorBlack aUnderline\">Salvage &amp; Security</a>";
						echo "<br />";
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Customer WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
							
                        if($total_Form == 0)
	                        echo "<label>Customer Service</label>";
                        else
							echo "<a href=\"Forms/ReduceTheRisk/Customer.php?SubFolder=2&Section=8\" class=\"lblFontColorBlack aUnderline\">Customer Service</a>";
						echo "<br />";
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Environment WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
							
                        if($total_Form == 0)
	                        echo "<label>Environment &amp; Privacy</label>";
                        else
							echo "<a href=\"Forms/ReduceTheRisk/Environment.php?SubFolder=2&Section=9\" class=\"lblFontColorBlack aUnderline\">Environment &amp; Privacy</a>";
							
                        echo "<br /><br />
                        <label><span class=\"lblFontBold lblFontSize14\">Step 2</span><br />
                        <span class=\"lblStandardColor\">Respond to an Event</span></label>
                        <br /><br />";
						
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Immediate WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
						
                        if($total_Form == 0)
	                        echo "<label>Immediate Response Team</label>";
                        else
							echo "<a href=\"Forms/RespondtoanEvent/Immediate.php?SubFolder=2&Section=10\" class=\"lblFontColorBlack aUnderline\">Immediate Response Team</a>";
							
                        echo "<br /><br />
                        <label><span class=\"lblFontBold lblFontSize14\">Step 3</span><br />
                        <span class=\"lblContinuityColor\">Recover From an Event</span></label>
                        <br /><br />";
						
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Disaster WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
						
                        if($total_Form == 0)
	                        echo "<label>Disaster Management Team</label>";
                        else
							echo "<a href=\"Forms/RecoverFormanEvent/Disaster.php?SubFolder=2&Section=11\" class=\"lblFontColorBlack aUnderline\">Disaster Management Team</a>";
						echo "<br />";
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Damage WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
							
                        if($total_Form == 0)
	                        echo "<label>Damage Assessment Team</label>";
                        else
							echo "<a href=\"Forms/RecoverFormanEvent/Damage.php?SubFolder=2&Section=12\" class=\"lblFontColorBlack aUnderline\">Damage Assessment Team</a>";
						echo "<br />";
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2ITRecoveryTeam WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
							
                        if($total_Form == 0)
	                        echo "<label>Information Technology Recovery Team</label>";
                        else
							echo "<a href=\"Forms/RecoverFormanEvent/ITRecoveryTeam.php?SubFolder=2&Section=13\" class=\"lblFontColorBlack aUnderline\">Information Technology Recovery Team</a>";
						echo "<br />";
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Administration WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
							
                        if($total_Form == 0)
	                        echo "<label>Administration Recovery Team</label>";
                        else
							echo "<a href=\"Forms/RecoverFormanEvent/Administration.php?SubFolder=2&Section=14\" class=\"lblFontColorBlack aUnderline\">Administration Recovery Team</a>";
						echo "<br />";
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Essential WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
							
                        if($total_Form == 0)
	                        echo "<label>Essential Functions Recovery Team</label>";
                        else
							echo "<a href=\"Forms/RecoverFormanEvent/Essential.php?SubFolder=2&Section=15\" class=\"lblFontColorBlack aUnderline\">Essential Functions Recovery Team</a>";
						echo "<br />";
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsForm = mysql_query("SELECT * FROM C2Business WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
						$row_Form = mysql_fetch_assoc($rsForm);
						$total_Form = mysql_num_rows($rsForm);
							
                        if($total_Form == 0)
	                        echo "<label>Business Recovery Support Team</label>";
                        else
							echo "<a href=\"Forms/RecoverFormanEvent/Business.php?SubFolder=2&Section=16\" class=\"lblFontColorBlack aUnderline\">Business Recovery Support Team</a>";
						
						if($row_loginFoundUser['Solution'] == 2)
						{
							echo "<br /><br />
                        	<label><span class=\"lblFontBold lblFontSize14\">Step 4</span><br />
                        	<span class=\"lblEnterColor\">Restore Your Business</span></label>
                        	<br /><br />";
						
							mysql_select_db($database_conContinuty, $conContinuty);
							$rsForm = mysql_query("SELECT * FROM C2InsuranceInventory WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
							$row_Form = mysql_fetch_assoc($rsForm);
							$total_Form = mysql_num_rows($rsForm);
						
							if($total_Form == 0)
								echo "<label>Insurance Inventory</label>";
							else
								echo "<a href=\"Forms/RestoreYourBusiness/InsuranceInventory.php?SubFolder=2&Section=20\" class=\"lblFontColorBlack aUnderline\">Insurance Inventory</a>";
						}//end of if
						?>
                    </div>
                    <div class="customNavigation" id="divProfileNavigation" align="left">
                    	<!-- Add User To Account -->
                        <div class="divBasicHidden boardBox" id="divAddUser">
                            <div class="customContainer infoProfileEditingContainer <?php echo $strBackgroudColor; ?>">
                                <div class="customContent infoProfileEditingContent">
                                    <label>Add User To This Account</label>
                                </div>
                                <div align="right" class="customContent infoProfileEditingNavigation">
                                    <div class="divClose">
                                      <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divAddUser','divGrayBG');">Close</a>
                                    </div>
                                </div>
                                <div class="customFooter infoProfileEditingFooter"></div>
                            </div><!-- end of Cotinater -->
                            <div class="divHiddlenBody">
                                <form action="<?php echo $User; ?>" method="post" id="frmAddUser" class="frmBasics">
                                	<div id="divAddMessage" class="divBasicMessage"></div>
                                	<div class="updateUserContainer customContainer">
                                        <div class="updateUserContent customContent">
                                            <label>Email address:</label>
                                        </div>
                                        <div class="updateUserDivider">&nbsp;</div>
                                        <div class="updateUserNavigation customNavigation">
                                            <input name="txtAddEMail" type="text" size="23" value="" />
                                            <br/><label class="sectionNum">This will become your UserName</label>
                                        </div>
                                        <div class="updateUserFooter customFooter"></div>
                                        <div class="updateUserContent customContent">
                                            <label>Enter a Password:</label>
                                        </div>
                                        <div class="updateUserDivider">&nbsp;</div>
                                        <div class="updateUserNavigation customNavigation">
                                            <input name="txtAddPassword" type="password" size="20" maxlength="15" value="" />
                                        </div>
                                        <div class="updateUserFooter customFooter"></div>
                                        <div class="updateUserContent customContent">
                                            <label>Re-Enter Password:</label>
                                        </div>
                                        <div class="updateUserDivider">&nbsp;</div>
                                        <div class="updateUserNavigation customNavigation">
                                            <input name="txtAddRePassword" type="password" maxlength="15" value="" />
                                        </div>
                                        <div class="updateUserFooter customFooter"></div>
                                 	</div>
                                    <div align="center" class="updateUserHeader">
                                		<input type="hidden" id="hfAddUser" name="hfAddUser" value="" />
                                		<input type="submit" value="Add User" onclick="getDocID('hfAddUser').value = '1'" />
                                    </div>
                                </form>
                            </div>
                        </div><!-- end of Hidden Div -->
                    
                    	<!-- Print Your Plan -->
                        <div class="divBasicHidden boardBox" id="divPrintHiddlenPlan">
                            <div class="customContainer infoSolContainer <?php echo $strBackgroudColor; ?>">
                                <div class="customContent infoSolContent">
                                    <label>Print Your Plan</label>
                                </div>
                                <div align="right" class="customContent infoSolNavigation">
                                    <div class="divClose">
                                    <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divPrintHiddlenPlan','divGrayBG');">Close</a>
                                    </div>
                                </div>
                                <div class="customFooter infoSolFooter"></div>
                            </div><!-- end of Cotinater -->
                            <div class="divHiddlenBody">
                            	<div class="customContainer" id="divPrintPlanContainer">
                                	<div class="customContent" id="divPrintPlanContent">
	                                	<a href="PurePHP/PrintPDFPlan.php" class="lblFontBold lblFontColorBlack" target="_blank">Print Your Plan in PDF</a>
                                    </div>
                                    <div class="customNavigation" id="divPrintPlanNavigation">
	                            		<a href="PurePHP/PrintPlan.php" class="lblFontBold lblFontColorBlack" target="_blank">Print Your Plan</a>
                                    </div>
                                    <div class="customFooter" id="divPrintPlanFooter"></div>
                                </div>
                            </div>
                        </div><!-- end of Hidden Div -->
                    
                    	<!-- Getting Started -->
                        <div class="divBasicHidden boardBox" id="divStarted">
                            <div class="customContainer infoSolContainer <?php echo $strBackgroudColor; ?>">
                                <div class="customContent infoSolContent">
                                    <label>Getting Started</label>
                                </div>
                                <div align="right" class="customContent infoSolNavigation">
                                    <div class="divClose">
                                      <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divStarted','divGrayBG');">Close</a>
                                    </div>
                                </div>
                                <div class="customFooter infoSolFooter"></div>
                            </div><!-- end of Cotinater -->
                            <div class="divHiddlenBody">
                                <img src="images/demovideo.jpg" alt="Demo Video" width="260" height="125" />
                            </div>
                        </div><!-- end of Hidden Div -->
                        
                        <!-- Right Hand Navigation -->
                        <div class="divBasicHidden boardBox" id="divRight">
                            <div class="customContainer infoSolContainer <?php echo $strBackgroudColor; ?>">
                                <div class="customContent infoSolContent">
                                    <label>Right Hand Navigation</label>
                                </div>
                                <div align="right" class="customContent infoSolNavigation">
                                    <div class="divClose">
                                        <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divRight','divGrayBG');">Close</a>
                                    </div>
                                </div>
                                <div class="customFooter infoSolFooter"></div>
                            </div><!-- end of Cotinater -->
                            <div class="divHiddlenBody">
                                <img src="images/demovideo.jpg" alt="Demo Video" width="260" height="125" />
                            </div>
                        </div><!-- end of Hidden Div -->
                        
                        <!-- Left Hand (Wizard Navigation) -->
                        <div class="divBasicHidden boardBox" id="divLeftHand">
                            <div class="customContainer infoSolContainer <?php echo $strBackgroudColor; ?>">
                                <div class="customContent infoSolContent">
                                    <label>Left Hand (Wizard Navigation)</label>
                                </div>
                                <div align="right" class="customContent infoSolNavigation">
                                    <div class="divClose">
                                        <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divLeftHand','divGrayBG');">Close</a>
                                    </div>
                                </div>
                                <div class="customFooter infoSolFooter"></div>
                            </div><!-- end of Cotinater -->
                            <div class="divHiddlenBody">
                                <img src="images/demovideo.jpg" alt="Demo Video" width="260" height="125" />
                            </div>
                        </div><!-- end of Hidden Div -->
                        
                        <!-- Design Your Plan -->
                        <div class="divBasicHidden boardBox" id="divDesign">
                            <div class="customContainer infoSolContainer <?php echo $strBackgroudColor; ?>">
                                <div class="customContent infoSolContent">
                                    <label>Design Your Plan</label>
                                </div>
                                <div align="right" class="customContent infoSolNavigation">
                                    <div class="divClose">
                                       <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divDesign','divGrayBG');">Close</a>
                                    </div>
                                </div>
                                <div class="customFooter infoSolFooter"></div>
                            </div><!-- end of Cotinater -->
                            <div class="divHiddlenBody">
                                <img src="images/demovideo.jpg" alt="Demo Video" width="260" height="125" />
                            </div>
                        </div><!-- end of Hidden Div -->
                        
                        <!-- Update Your Plan -->
                        <div class="divBasicHidden boardBox" id="divUpdate">
                            <div class="customContainer infoSolContainer <?php echo $strBackgroudColor; ?>">
                                <div class="customContent infoSolContent">
                                    <label>Update Your Plan</label>
                                </div>
                                <div align="right" class="customContent infoSolNavigation">
                                    <div class="divClose">
                                       <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divUpdate','divGrayBG');">Close</a>
                                    </div>
                                </div>
                                <div class="customFooter infoSolFooter"></div>
                            </div><!-- end of Cotinater -->
                            <div class="divHiddlenBody">
                                <img src="images/demovideo.jpg" alt="Demo Video" width="260" height="125" />
                            </div>
                        </div><!-- end of Hidden Div -->
                        
                        <!-- Print Your Plan -->
                        <div class="divBasicHidden boardBox" id="divPrint">
                            <div class="customContainer infoSolContainer <?php echo $strBackgroudColor; ?>">
                                <div class="customContent infoSolContent">
                                    <label>Print Your Plan</label>
                                </div>
                                <div align="right" class="customContent infoSolNavigation">
                                    <div class="divClose">
                                        <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divPrint','divGrayBG');">Close</a>
                                    </div>
                                </div>
                                <div class="customFooter infoSolFooter"></div>
                            </div><!-- end of Cotinater -->
                            <div class="divHiddlenBody">
                                <img src="images/demovideo.jpg" alt="Demo Video" width="260" height="125" />
                            </div>
                        </div><!-- end of Hidden Div -->
                        
                        <!-- Add Users -->
                        <div class="divBasicHidden boardBox" id="divAdd">
                            <div class="customContainer infoSolContainer <?php echo $strBackgroudColor; ?>">
                                <div class="customContent infoSolContent">
                                    <label>Add Users</label>
                                </div>
                                <div align="right" class="customContent infoSolNavigation">
                                    <div class="divClose">
                                        <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divAdd','divGrayBG');">Close</a>
                                    </div>
                                </div>
                                <div class="customFooter infoSolFooter"></div>
                            </div><!-- end of Cotinater -->
                            <div class="divHiddlenBody">
                                <img src="images/demovideo.jpg" alt="Demo Video" width="260" height="125" />
                            </div>
                        </div><!-- end of Hidden Div -->
                        
                        <!-- Edit Account Information -->
                        <div class="divBasicHidden boardBox" id="divEdit">
                            <div class="customContainer infoSolContainer <?php echo $strBackgroudColor; ?>">
                                <div class="customContent infoSolContent">
                                    <label>Edit Account Information</label>
                                </div>
                                <div align="right" class="customContent infoSolNavigation">
                                    <div class="divClose">
                                        <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divEdit','divGrayBG');">Close</a>
                                    </div>
                                </div>
                                <div class="customFooter infoSolFooter"></div>
                            </div><!-- end of Cotinater -->
                            <div class="divHiddlenBody">
                                <img src="images/demovideo.jpg" alt="Demo Video" width="260" height="125" />
                            </div>
                        </div><!-- end of Hidden Div -->
                        
                        <!-- Renew Your Program -->
                    	<div class="divBasicHidden boardBox" id="divRenew">
                            <div class="customContainer infoSolContainer <?php echo $strBackgroudColor; ?>">
                                <div class="customContent infoSolContent">
                                    <label>Renew Your Program</label>
                                </div>
                                <div align="right" class="customContent infoSolNavigation">
                                    <div class="divClose">
                                        <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divRenew','divGrayBG');">Close</a>
                                    </div>
                                </div>
                                <div class="customFooter infoSolFooter"></div>
                            </div><!-- end of Cotinater -->
                            <div class="divHiddlenBody">
                                <img src="images/demovideo.jpg" alt="Demo Video" width="260" height="125" />
                            </div>
                        </div><!-- end of Hidden Div -->
                    	
                    	<form action="<?php echo $User; ?>" method="post" id="frmUser" class="frmBasics">
                            <div class="divBasicHidden boardBox" id="divEditAccount">
                                <div class="customContainer infoProfileEditingContainer <?php echo $strBackgroudColor; ?>">
                                    <div class="customContent infoProfileEditingContent">
                                        <label>Edit Your Account</label>
                                    </div>
                                    <div align="right" class="customContent infoProfileEditingNavigation">
                                        <div class="divClose">
                                           <a href="javascript:void(0);" class="aClose" onclick="javascript:toggleLayer('divEditAccount','divGrayBG');">Close</a>
                                        </div>
                                    </div>
                                    <div class="customFooter infoProfileEditingFooter"></div>
                                </div><!-- end of Cotinater -->
                                <div class="divHiddlenBody">
                                    <div id="divProfileMessage" class="divBasicMessage"></div>
                                    <div id="divUserContainer" class="customContainer">
                                        <div id="divUserContent" class="customContent">
                                            <div class="updateUserContainer customContainer">
                                                <div class="divRegBody">
                                                    <div class="updateUserContent customContent">
                                                        <label>Email address:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <input name="txtEMail" type="text" size="23" value="<?php echo $row_loginFoundUser['login']; ?>" />
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
                                                        <input name="txtFName" type="text" value="<?php echo $row_loginFoundUser['firstname']; ?>" />
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                    <div class="updateUserContent customContent">
                                                        <label>Last Name:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <input name="txtLName" type="text" value="<?php echo $row_loginFoundUser['lastname']; ?>" />
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                    <div class="updateUserContent customContent">
                                                        <label>Title:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <input name="txtTitle" type="text" value="<?php echo $row_loginFoundUser['title']; ?>" />
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                    <div class="updateUserContent customContent">
                                                        <label>Company:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <input name="txtCompany" type="text" value="<?php echo $row_loginFoundUser['company']; ?>" />
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                    <div class="updateUserContent customContent">
                                                        <label>Telephone number:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <input name="txtPhone" type="text" value="<?php echo $row_loginFoundUser['phone']; ?>" />
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                    <div class="updateUserContent customContent">
                                                        <label>Extension:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <input name="txtExt" type="text" value="<?php echo $row_loginFoundUser['extension']; ?>" />
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                    <div class="updateUserContent customContent">
                                                        <label>Fax number:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <input name="txtFax" type="text" value="<?php echo $row_loginFoundUser['fax']; ?>" />
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                    <div class="updateUserContent customContent">
                                                        <label>Street address:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <input name="txtStreet" type="text" value="<?php echo $row_loginFoundUser['address']; ?>" />
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                    <div class="updateUserContent customContent">
                                                        <label>Suite number:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <input name="txtSuiteNum" type="text" value="<?php echo $row_loginFoundUser['suite']; ?>" />
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                    <div class="updateUserContent customContent">
                                                        <label>City:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <input name="txtCity" type="text" value="<?php echo $row_loginFoundUser['city']; ?>" />
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                    <div class="updateUserContent customContent">
                                                        <label>State/Province:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <select name="cmbPro">
                                                          <option value="AB" <?php if($row_loginFoundUser['state'] == "AB") echo "selected" ?>>Alberta</option>
                                                          <option value="BC" <?php if($row_loginFoundUser['state'] == "BC") echo "selected" ?>>British Columbia</option>
                                                          <option value="MA" <?php if($row_loginFoundUser['state'] == "MA") echo "selected" ?>>Manitoba</option>
                                                          <option value="NB" <?php if($row_loginFoundUser['state'] == "NB") echo "selected" ?>>New Brunswick</option>
                                                          <option value="NL" <?php if($row_loginFoundUser['state'] == "NL") echo "selected" ?>>Newfoundland and Labrador</option>
                                                          <option value="NW" <?php if($row_loginFoundUser['state'] == "NW") echo "selected" ?>>Northwest Territories</option>
                                                          <option value="NS" <?php if($row_loginFoundUser['state'] == "NS") echo "selected" ?>>Nova Scotia</option>
                                                          <option value="NN" <?php if($row_loginFoundUser['state'] == "NN") echo "selected" ?>>Nunavut</option>
                                                          <option value="ONT" <?php if($row_loginFoundUser['state'] == "ONT") echo "selected" ?>>Ontario</option>
                                                          <option value="PEI" <?php if($row_loginFoundUser['state'] == "PEI") echo "selected" ?>>Prince Edward Island</option>
                                                          <option value="QUB" <?php if($row_loginFoundUser['state'] == "QUB") echo "selected" ?>>Quebec</option>
                                                          <option value="SAS" <?php if($row_loginFoundUser['state'] == "SAS") echo "selected" ?>>Saskatchewan</option>
                                                          <option value="YUK" <?php if($row_loginFoundUser['state'] == "YUK") echo "selected" ?>>Yukon</option>	
                                                          <option value="AL" <?php if($row_loginFoundUser['state'] == "AL") echo "selected" ?>>Alabama</option>
                                                          <option value="AK" <?php if($row_loginFoundUser['state'] == "AK") echo "selected" ?>>Alaska</option>
                                                          <option value="AZ" <?php if($row_loginFoundUser['state'] == "AZ") echo "selected" ?>>Arizona</option>
                                                          <option value="AR" <?php if($row_loginFoundUser['state'] == "AR") echo "selected" ?>>Arkansas</option>
                                                          <option value="CA" <?php if($row_loginFoundUser['state'] == "CA") echo "selected" ?>>California</option>
                                                          <option value="CO" <?php if($row_loginFoundUser['state'] == "CO") echo "selected" ?>>Colorado</option>
                                                          <option value="CT" <?php if($row_loginFoundUser['state'] == "CT") echo "selected" ?>>Connecticut</option>
                                                          <option value="DE" <?php if($row_loginFoundUser['state'] == "DE") echo "selected" ?>>Delaware</option>
                                                          <option value="DC" <?php if($row_loginFoundUser['state'] == "DC") echo "selected" ?>>District of Columbia</option>
                                                          <option value="FL" <?php if($row_loginFoundUser['state'] == "FL") echo "selected" ?>>Florida</option>
                                                          <option value="GA" <?php if($row_loginFoundUser['state'] == "GA") echo "selected" ?>>Georgia</option>
                                                          <option value="HI" <?php if($row_loginFoundUser['state'] == "HI") echo "selected" ?>>Hawaii</option>
                                                          <option value="ID" <?php if($row_loginFoundUser['state'] == "ID") echo "selected" ?>>Idaho</option>
                                                          <option value="IL" <?php if($row_loginFoundUser['state'] == "IL") echo "selected" ?>>Illinois</option>
                                                          <option value="IN" <?php if($row_loginFoundUser['state'] == "IN") echo "selected" ?>>Indiana</option>
                                                          <option value="IA" <?php if($row_loginFoundUser['state'] == "IA") echo "selected" ?>>Iowa</option>
                                                          <option value="KS" <?php if($row_loginFoundUser['state'] == "KS") echo "selected" ?>>Kansas</option>
                                                          <option value="KY" <?php if($row_loginFoundUser['state'] == "KY") echo "selected" ?>>Kentucky</option>
                                                          <option value="LA" <?php if($row_loginFoundUser['state'] == "LA") echo "selected" ?>>Louisiana</option>
                                                          <option value="ME" <?php if($row_loginFoundUser['state'] == "ME") echo "selected" ?>>Maine</option>
                                                          <option value="MD" <?php if($row_loginFoundUser['state'] == "MD") echo "selected" ?>>Maryland</option>
                                                          <option value="MA" <?php if($row_loginFoundUser['state'] == "MA") echo "selected" ?>>Massachusetts</option>
                                                          <option value="MI" <?php if($row_loginFoundUser['state'] == "MI") echo "selected" ?>>Michigan</option>
                                                          <option value="MN" <?php if($row_loginFoundUser['state'] == "MN") echo "selected" ?>>Minnesota</option>
                                                          <option value="MS" <?php if($row_loginFoundUser['state'] == "MS") echo "selected" ?>>Mississippi</option>
                                                          <option value="MO" <?php if($row_loginFoundUser['state'] == "MO") echo "selected" ?>>Missouri</option>
                                                          <option value="MT" <?php if($row_loginFoundUser['state'] == "MT") echo "selected" ?>>Montana</option>
                                                          <option value="NE" <?php if($row_loginFoundUser['state'] == "NE") echo "selected" ?>>Nebraska</option>
                                                          <option value="NV" <?php if($row_loginFoundUser['state'] == "NV") echo "selected" ?>>Nevada</option>
                                                          <option value="NH" <?php if($row_loginFoundUser['state'] == "NH") echo "selected" ?>>New Hampshire</option>
                                                          <option value="NJ" <?php if($row_loginFoundUser['state'] == "NJ") echo "selected" ?>>New Jersey</option>
                                                          <option value="NM" <?php if($row_loginFoundUser['state'] == "NM") echo "selected" ?>>New Mexico</option>
                                                          <option value="NY" <?php if($row_loginFoundUser['state'] == "NY") echo "selected" ?>>New York</option>
                                                          <option value="NC" <?php if($row_loginFoundUser['state'] == "NC") echo "selected" ?>>North Carolina</option>
                                                          <option value="ND" <?php if($row_loginFoundUser['state'] == "ND") echo "selected" ?>>North Dakota</option>
                                                          <option value="OH" <?php if($row_loginFoundUser['state'] == "OH") echo "selected" ?>>Ohio</option>
                                                          <option value="OK" <?php if($row_loginFoundUser['state'] == "OK") echo "selected" ?>>Oklahoma</option>
                                                          <option value="OR" <?php if($row_loginFoundUser['state'] == "OR") echo "selected" ?>>Oregon</option>
                                                          <option value="PA" <?php if($row_loginFoundUser['state'] == "PA") echo "selected" ?>>Pennsylvania</option>
                                                          <option value="RI" <?php if($row_loginFoundUser['state'] == "RI") echo "selected" ?>>Rhode Island</option>
                                                          <option value="SC" <?php if($row_loginFoundUser['state'] == "SC") echo "selected" ?>>South Carolina</option>
                                                          <option value="SD" <?php if($row_loginFoundUser['state'] == "SD") echo "selected" ?>>South Dakota</option>
                                                          <option value="TN" <?php if($row_loginFoundUser['state'] == "TN") echo "selected" ?>>Tennessee</option>
                                                          <option value="TX" <?php if($row_loginFoundUser['state'] == "TX") echo "selected" ?>>Texas</option>
                                                          <option value="UT" <?php if($row_loginFoundUser['state'] == "UT") echo "selected" ?>>Utah</option>
                                                          <option value="VT" <?php if($row_loginFoundUser['state'] == "VT") echo "selected" ?>>Vermont</option>
                                                          <option value="VA" <?php if($row_loginFoundUser['state'] == "VA") echo "selected" ?>>Virginia</option>
                                                          <option value="WA" <?php if($row_loginFoundUser['state'] == "WA") echo "selected" ?>>Washington</option>
                                                          <option value="WV" <?php if($row_loginFoundUser['state'] == "WV") echo "selected" ?>>West Virginia</option>
                                                          <option value="WI" <?php if($row_loginFoundUser['state'] == "WI") echo "selected" ?>>Wisconsin</option>
                                                          <option value="WY" <?php if($row_loginFoundUser['state'] == "WY") echo "selected" ?>>Wyoming</option>
                                                        </select>
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>                  
                                                    <div class="updateUserContent customContent">
                                                        <label>Zip/Postal code:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <input name="txtPostal" type="text" value="<?php echo $row_loginFoundUser['zip']; ?>" />
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                    <div class="updateUserContent customContent">
                                                        <label>Country:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <select name="cmbCountry">
                                                          <option value="CA" <?php if($row_loginFoundUser['country'] == "CA") echo "selected" ?>>Canada</option>
                                                          <option value="US" <?php if($row_loginFoundUser['country'] == "US") echo "selected" ?>>United States</option>
                                                        </select>
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                    <div class="updateUserContent customContent">
                                                        <label>Remove User From Account:</label>
                                                    </div>
                                                    <div class="updateUserDivider">&nbsp;</div>
                                                    <div class="updateUserNavigation customNavigation">
                                                        <select name="cmbUsers">
                                                        	<option value=""></option>
															<?php 
															$arrUser = split("#",$row_loginFoundUser['Users']);//holds the array of Users the account
															
															//goes around each users that is connect to this account and display it
															//for the user to remove it from the account
                                                            foreach ($arrUser as $arrUserValue)
                                                            {
                                                                //checks if the row in arrUser is not blank
																//blank line is in here as it is the last item
																if($arrUserValue != "")
																{
																	$arrUserDetails = split("~",$arrUserValue);//holds the array fot details of the Users
																	
																	//creates an option for the combo box for the user
                                                                	echo "<option value=\"".$arrUserDetails[0]."\">".$arrUserDetails[0]."</option>";
																}//end of if
                                                            }//end of foreach ?>
                                                        </select>
                                                        <br /><br />
                                                        <input type="hidden" id="hfRemoveUser" name="hfRemoveUser" value="" />
                                                        <input type="submit" value="Removed User" onclick="getDocID('hfRemoveUser').value = '0'" />
                                                    </div>
                                                    <div class="updateUserFooter customFooter"></div>
                                                </div><!-- end of divRegBody -->
                                               
                                            <?php 
                                            if($row_loginFoundUser['policy'] != "")
                                                echo "<div class=\"updateUserHeader customHeader formhead sectionNum\"><label>Policy Details</label></div>
                                                    <div class=\"divRegBody\">
                                                        <div class=\"updateUserContent customContent\">
                                                            <label>Policy #:</label>
                                                        </div>
                                                        <div class=\"updateUserDivider\">&nbsp;</div>
                                                        <div class=\"updateUserNavigation customNavigation\">
                                                            <input name=\"txtPolicy\" type=\"text\" value=\"".$row_loginFoundUser['policy']."\" />
                                                        </div>
                                                        <div class=\"updateUserFooter customFooter\"></div>
                                                        <div class=\"updateUserContent customContent\">
                                                            <label>Broker Name:</label>
                                                        </div>
                                                        <div class=\"updateUserDivider\">&nbsp;</div>
                                                        <div class=\"updateUserNavigation customNavigation\">
                                                            <input name=\"txtBrokerName\" type=\"text\" value=\"".$row_loginFoundUser['brokername']."\" />
                                                        </div>
                                                        <div class=\"updateUserFooter customFooter\"></div>
                                                        <div class=\"updateUserContent customContent\">
                                                            <label>Broker Address:</label>
                                                        </div>
                                                        <div class=\"updateUserDivider\">&nbsp;</div>
                                                        <div class=\"updateUserNavigation customNavigation\">
                                                            <input name=\"txtBrokerAddress\" type=\"text\" value=\"".$row_loginFoundUser['brokeraddress']."\" />
                                                        </div>
                                                        <div class=\"updateUserFooter customFooter\"></div>
                                                    </div>";?>
                                            </div><!-- end of updateUserContainer -->            
                                        </div><!-- end of User Content -->
                                        <div id="divUserFooter" class="customFooter">
                                            <div class="checkBoxArea">
                                                <input name="chkInform" type="checkbox" value="1" <?php if($row_loginFoundUser['mailinglist'] == 1) echo "checked" ?>/><label>Keep me informed about Continuity inc. promotions and New Releases.</label>
                                            </div>
                                            <div class="updateUserFooter customFooter"></div>
                                        </div><!-- end of User footer -->
                                    </div><!-- end of User Container -->
                                    
                                   <div align="center" class="updateUserHeader">
                                        <input type="hidden" name="hfSubmit" value="1" />
                                        <input type="hidden" name="hfSection" value="Profile" />
                                        <input type="hidden" name="hfcustomFooter" value="1" />
                                        <input name="cmdSend" type="submit" value="Send Update" />
                                    </div>
                                </div>
                            </div><!-- end of Hidden Div -->
                        </form>
                    	<label>Welcome: <?php echo getUserName(); ?>
                        <br /><br />
                        to your Continuity Plans <span class="<?php echo $strColor; ?>"><?php echo $strEdition; ?> Edition</span>. This program has been seet to give you more then just plan to help you recovery from a disaster, but give you the ability to reduce the risk and respond to an event so you can remain in business.
                        <br /><br />
                        Click On the Topic Headings below to hear an instructional video of the program features.</label>
                        <div class="customContainer" id="divNavProfileContainer">
                        	<div class="customContent" id="divNavProfileContent">
                            	<label>Video Tutorials:</label>
                                <ul>
                                	<li><a href="javascript:void(0);" class="<?php echo $strColor; ?>" onclick="javascript:toggleLayer('divStarted','divGrayBG');">Getting Started</a></li>
                                	<li><a href="javascript:void(0);" class="<?php echo $strColor; ?>" onclick="javascript:toggleLayer('divRight','divGrayBG');">Right Hand Navigation</a></li>
                                	<li><a href="javascript:void(0);" class="<?php echo $strColor; ?>" onclick="javascript:toggleLayer('divLeftHand','divGrayBG');">Left Hand (Wizard Navigation)</a></li>
                                	<li><a href="javascript:void(0);" class="<?php echo $strColor; ?>" onclick="javascript:toggleLayer('divDesign','divGrayBG');">Design Your Plan</a></li>
                                	<li><a href="javascript:void(0);" class="<?php echo $strColor; ?>" onclick="javascript:toggleLayer('divUpdate','divGrayBG');">Update Your Plan</a></li>
                                	<li><a href="javascript:void(0);" class="<?php echo $strColor; ?>" onclick="javascript:toggleLayer('divPrint','divGrayBG');">Print Your Plan</a></li>
                                	<li><a href="javascript:void(0);" class="<?php echo $strColor; ?>" onclick="javascript:toggleLayer('divAdd','divGrayBG');">Add Users</a></li>
                                	<li><a href="javascript:void(0);" class="<?php echo $strColor; ?>" onclick="javascript:toggleLayer('divEdit','divGrayBG');">Edit Account Information</a></li>
                                	<li><a href="javascript:void(0);" class="<?php echo $strColor; ?>" onclick="javascript:toggleLayer('divRenew','divGrayBG');">Renew Your Program</a></li>
                                </ul>
                            </div>
                            <div class="customNavigation" id="divNavProfileNavigation">
                            
                            </div>
                            <div class="customFooter" id="divNavProfileFooter"></div> 
                        </div>
                        <label>Quick Start:
                        <br /><br />
                        To get started immediatly, proceed to click on the Complete Your Plan button on the right side Wizard program to begin completing your program.
                        <br /><br />
                        Please feel free to contact one of our professionals at any time for questions, support or technical assistance.
                        <br /><br />
                        Thank you for taking the time to build a Business Continuity &amp; Disaster Recovery plan.</label>
                        <div class="divPageSubTitle" align="center"> 
                        	<label class="lblFontColorGray">"The Future of business lies in the hands of the prepared" Continuity Inc.</label>
                        </div>
                    </div>
                    <div class="customThridContent" id="divProfileThridContent">
                    	<div class="customContainer divThridProfileContainer">
                        	<div class="customContent divThridProfileContent">
                            	<img src="images/FaqarrowIngreen.gif" />
                            </div>
                            <div class="customNavigation divThridProfileNavigation" align="center">
                            	<a href="javascript:void(0);" class="lblFontBold lblFontColorBlack" onclick="javascript:toggleLayer('divPrintHiddlenPlan','divGrayBG');">Print Your Plan</a>
                            </div>
                            <div class="customFooter divThridProfileFooter"></div>
                            <br />
                            <div class="customContent divThridProfileContent">
                            	<?php 
								//checks whick version the users is going to used
								if(($row_loginFoundUser['Solution'] + 1) >= 3)
								{
									echo "<img src=\"images/FaqarrowInred.gif\" />"; 
								}//end of if
								else if (($row_loginFoundUser['Solution'] + 1) == 2)
								{
									echo "<img src=\"images/FaqarrowInblue.gif\" />"; 
								}//end of else if?>
                            </div>
                            <div class="customNavigation divThridProfileNavigation">
                            	<?php 
								//checks if the user has already started to do the Sign Up for Conitinuity Insruance
                        		if ($total_lastLoc > 0)
		                        	echo "<a href=\"".$row_lastLoc["UserLastLoc"]."\" class=\"lblFontColorBlack\">Resume Wizard</a>";
                        		else
                            		echo "<a href=\"Forms/ReduceTheRisk/Welcome.php?section=Welcome&amp;SubFolder=2\" class=\"lblFontColorBlack\">Start Wizard</a>"; ?>
                            </div>
                            <div class="customFooter divThridProfileFooter"></div>
                             <?php 
							//checks if an upgrade to the next solution is need as they can update
							if($row_loginFoundUser['login'] == base64_decode($_SESSION['UserEMail']))
							{ ?>
                                <div class="customContent divThridProfileContent">
                                    <?php 
                                    //checks whick version the users is going to used
                                    if(($row_loginFoundUser['Solution'] + 1) >= 3)
                                    {
                                        echo "<img src=\"images/FaqarrowInred.gif\" />"; 
                                    }//end of if
                                    else if (($row_loginFoundUser['Solution'] + 1) == 2)
                                    {
                                        echo "<img src=\"images/FaqarrowInblue.gif\" />"; 
                                    }//end of else if?>
                                </div>
                                <div class="customNavigation divThridProfileNavigation">
                                    <a href="javascript:void(0);" class="lblFontColorBlack" onclick="javascript:toggleLayer('divEditAccount','divGrayBG');">Edit Your Account</a>
                                </div>
                                <div class="customFooter divThridProfileFooter"></div>
                                <div class="customContent divThridProfileContent">
                                    <?php 
                                    //checks whick version the users is going to used
                                    if(($row_loginFoundUser['Solution'] + 1) >= 3)
                                    {
                                        echo "<img src=\"images/FaqarrowInred.gif\" />"; 
                                    }//end of if
                                    else if (($row_loginFoundUser['Solution'] + 1) == 2)
                                    {
                                        echo "<img src=\"images/FaqarrowInblue.gif\" />"; 
                                    }//end of else if?>
                                </div>
                                <div class="customNavigation divThridProfileNavigation">
                                    <a href="javascript:void(0);" class="lblFontColorBlack" onclick="javascript:toggleLayer('divAddUser','divGrayBG');">Add Users To This Account</a>
                                </div>
                                <div class="customFooter divThridProfileFooter"></div>
                            <?php }//end of if ?>
                            <?php 
							//checks if an upgrade to the next solution is need as they can update
							//this will disable the ability to update to a Enterprise
							if(($row_loginFoundUser['Solution'] + 1) < 3)
							{ ?>
                                <div class="customContent divThridProfileContent">
                                    <?php 
                                    //checks whick version the users is going to used
                                    if(($row_loginFoundUser['Solution'] + 1) == 3)
                                    {
                                        echo "<img src=\"images/FaqarrowInblue.gif\" />"; 
                                    }//end of if
                                    else if (($row_loginFoundUser['Solution'] + 1) == 2)
                                    {
                                        echo "<img src=\"images/FaqarrowInred.gif\" />"; 
                                    }//end of else if?>
                                </div>
                                <div class="customNavigation divThridProfileNavigation">
                                    <?php 
                                   /* //checks whick version the users is going to used
                                    if(($row_loginFoundUser['Solution'] + 1) == 3)
                                    {
                                        echo "<a href=\"Profile.php?Ed=3\" class=\"lblFontColorBlack lblEnterColor spanUpdateTo\" onclick=\"return confirm('Do you Want to Upgrade To Enterprise Edition?'); \">Upgrade To Enterprise Edition</a>"; 
                                    }//end of if
                                    else if (($row_loginFoundUser['Solution'] + 1) == 2)
                                    {
                                        echo "<a href=\"Profile.php?Ed=2\" class=\"lblStandardColor spanUpdateTo\" onclick=\"return confirm('Do you Want to Upgrade To Standard Edition?'); \">Upgrade To Standard Edition</a>"; 
                                    }//end of else if*/
									 //checks whick version the users is going to used
                                    if(($row_loginFoundUser['Solution'] + 1) == 3)
                                    {
                                        echo "<a href=\"Contact.php?section=Contact&Footer=1\" class=\"lblFontColorBlack lblEnterColor spanUpdateTo\" onclick=\"return confirm('Do you Want to Upgrade To Enterprise Edition?'); \">Upgrade To Enterprise Edition</a>"; 
                                    }//end of if
                                    else if (($row_loginFoundUser['Solution'] + 1) == 2)
                                    {
                                        echo "<a href=\"Contact.php?section=Contact&Footer=1\" class=\"lblStandardColor spanUpdateTo\" onclick=\"return confirm('Do you Want to Upgrade To Standard Edition?'); \">Upgrade To Standard Edition</a>"; 
                                    }//end of else if
									?>
                                </div>
                                <div class="customFooter divThridProfileFooter"></div>
                         <?php }//end of if ?>
                        </div>
                    </div>
                	<div class="customFooter" id="divProfileFooter"></div>
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
