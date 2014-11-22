<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/BasicTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Assess Your Business - Continuity Inc. - Disaster Recovery Solutions</title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" type="text/css" href="CSS/MasterCSS.css" media="screen" />
<script src="javascript/MainJS.js" type="text/javascript"></script>
<?php require_once('PurePHP/LoginControl.php');?>
<?php require_once('Connections/conContinuty.php'); ?>
<!-- InstanceBeginEditable name="head" --><?php
 require_once('PurePHP/MasterFuncitons.php');

//forces the pages to come here to process the page
$AssessBusiness = $_SERVER['PHP_SELF'];

//checks if the fields that need data do have some data in them
if($_POST['hfSubmit'] != '')
{				
	if($_POST['txtName'] == '' || strpos($_POST['txtName'],"--") !== false || strpos($_POST['txtName'],"\\") !== false  || strpos($_POST['txtName'],"^") !== false)
	{
		echo "<script type=\"text/javascript\">
			window.onload=function(){
				displayMessage('divMessage','You Must Have an Name.',true,true);
		}//end of window.onload=function()</script>";
	}//end of if
	else
	{	
		if($_POST['txtTitle'] == '' || strpos($_POST['txtTitle'],"--") !== false || strpos($_POST['txtTitle'],"\\") !== false  || strpos($_POST['txtTitle'],"^") !== false)
		{
			echo "<script type=\"text/javascript\">
				window.onload=function(){
					displayMessage('divMessage','You Must Have an Title.',true,true);
			}//end of window.onload=function()</script>";
		}//end of if
		else
		{
			if($_POST['txtCompany'] == '' || strpos($_POST['txtCompany'],"--") !== false || strpos($_POST['txtCompany'],"\\") !== false  || strpos($_POST['txtCompany'],"^") !== false)
			{
				echo "<script type=\"text/javascript\">
					window.onload=function(){
						displayMessage('divMessage','You Must Have an Company.',true,true);
				}//end of window.onload=function()</script>";
			}//end of if
			else
			{
				if($_POST['txtEmail'] == '')
				{
					echo "<script type=\"text/javascript\">
						window.onload=function(){
							displayMessage('divMessage','You Must Have an E-Mail.',true,true);
					}//end of window.onload=function()</script>";
				}//end of if
				else
				{
					if(!preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i",$_POST['txtEmail']))
					{
						echo "<script type=\"text/javascript\">
							window.onload=function(){
								displayMessage('divMessage','Your E-Mail Address Format is not valid',true,true);
						}//end of window.onload=function()</script>";
					}//end of if
					else
					{
						mysql_select_db($database_conContinuty, $conContinuty);
						$rsUser = mysql_query("SELECT * FROM C2Assess WHERE Email = '".$_POST['txtEmail']."'", $conContinuty) or die(mysql_error());
						$totalRows_rsUser = mysql_num_rows($rsUser);

						//checks if the fields for the sign up page is current
						if($totalRows_rsUser > 0)
						{
								echo "<script type=\"text/javascript\">						
								window.onload=function(){						
									displayMessage('divMessage','Your E-Mail Address is Already in Our Database',true,true);
								}//end of window.onload=function()</script>";
						}//end of if
						else
						{
						
						if($_POST['rdoQ1'] == '' || $_POST['rdoQ2'] == '' || $_POST['rdoQ3'] == '' || $_POST['rdoQ4'] == '' || $_POST['rdoQ5'] == '' || $_POST['rdoQ6'] == '' || $_POST['rdoQ7'] == '' || $_POST['rdoQ8'] == '' || $_POST['rdoQ9'] == '' || $_POST['rdoQ10'] == '')
						{
							$strQuestionMiss = '';//holds the Question that are missed
							
							if($_POST['rdoQ1'] == '')
								$strQuestionMiss = '1, ';
							if($_POST['rdoQ2'] == '')
								$strQuestionMiss = $strQuestionMiss.'2, ';
							if($_POST['rdoQ3'] == '')
								$strQuestionMiss = $strQuestionMiss.'3, ';
							if($_POST['rdoQ4'] == '')
								$strQuestionMiss = $strQuestionMiss.'4, ';
							if($_POST['rdoQ5'] == '')
								$strQuestionMiss = $strQuestionMiss.'5, ';
							if($_POST['rdoQ6'] == '')
								$strQuestionMiss = $strQuestionMiss.'6, ';
							if($_POST['rdoQ7'] == '')
								$strQuestionMiss = $strQuestionMiss.'7, ';
							if($_POST['rdoQ8'] == '')
								$strQuestionMiss = $strQuestionMiss.'8, ';
							if($_POST['rdoQ9'] == '')
								$strQuestionMiss = $strQuestionMiss.'9, ';
							if($_POST['rdoQ10'] == '')
								$strQuestionMiss = $strQuestionMiss.'10';
						
							echo "<script type=\"text/javascript\">
								window.onload=function(){
									displayMessage('divMessage','You Must Awnser Questions: $strQuestionMiss',true,true);
							}//end of window.onload=function()</script>";
						}//end of if
						else
						{				
							 $strSendTo = "";
							 
							 //inserts a new entry for this Assessment
							mysql_select_db($database_conContinuty, $conContinuty);
							mysql_query("INSERT INTO C2Assess(Name,Title,Company,Email,Q1,Q2,Q3,Q4,Q5,Q6,Q7,Q8,Q9,Q10)VALUES('".str_replace("'","''",$_POST['txtName'])."','".str_replace("'","''",$_POST['txtTitle'])."','".str_replace("'","''",$_POST['txtCompany'])."','".str_replace("'","''",$_POST['txtEmail'])."',".$_POST['rdoQ1'].",".$_POST['rdoQ2'].",".$_POST['rdoQ3'].",".$_POST['rdoQ4'].",".$_POST['rdoQ5'].",".$_POST['rdoQ6'].",".$_POST['rdoQ7'].",".$_POST['rdoQ8'].",".$_POST['rdoQ9'].",".$_POST['rdoQ10'].")", $conContinuty) or die(mysql_error());
							mysql_select_db($database_conContinuty, $conContinuty);
							$rsAssess = mysql_query("SELECT id FROM C2Assess WHERE Email = '".$_POST['txtEmail']."'", $conContinuty) or die(mysql_error());
							$row_Assess = mysql_fetch_assoc($rsAssess);
																																		
							//sends out the e-mail to tell the Admin that there are new assessments
							sendEmail("New Business Assessments Have Been Filled Out By ".$_POST['txtName']." Of ".$_POST['txtCompany'],"New Business Assessments have been filled out by ".$_POST['txtName']." of ".$_POST['txtCompany']."<br/><br/>Please click this link to review this assessment<br/><br><a href=\"http://".$_SERVER['HTTP_HOST']."/J/Continuity2/Admin/AdminAssessDetail.php?id=".$row_Assess['id']."\">".$_POST['txtName']."'s Business Assessment<a>",$strSendTo,$_POST['txtEmail'],'PurePHP/Swift EMail/Swift.php','PurePHP/Swift EMail/Swift/Connection/SMTP.php');

							//goes to the Successful
							header("Location: ThankYouSending.php?section=ThankYouContact&Footer=1&Modes=1&id=".$row_Assess['id']);
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
              <h1><!-- InstanceBeginEditable name="h1Title" -->Assess Your Business<!-- InstanceEndEditable --></h1>
          	</div><!-- end of Header -->
            <div class="customContent" id="divBasicContent">
                <!-- InstanceBeginEditable name="BasicContent" -->
                <div align="left">
                	<img src="images/evaltop.jpg" alt="Evaluate Image" width="894" height="144" />
                </div>
                <div class="divPageTitle">
                	<label>Complete the Form below to access our On-Line Disaster Preparedness Assessment Survey. Once you complete the questioner you will be presented with an analysis based on your answers. If you have any further questions please contact one of our professionals.</label>
                </div>
                <div>
                	<label><strong>Please Complete All of The Following Information:</strong></label>
                </div>
                <br />
                    <form action="<?php echo $AssessBusiness; ?>" method="post" id="frmAssessBusiness" class="frmBasics">
		                <div id="divMessage" class="divBasicMessage"></div>
                        <div class="customContainer divAssessContactContainer">
                            <div class="customContent divAssessContactContent">
                                <label>Name:</label>
                            </div>
                            <div class="divAssessContactDivider">&nbsp;</div>
                            <div class="divAssessContactNavigation">
                                <input name="txtName" type="text" id="txtName" size="30" value="<?php echo $_POST['txtName']; ?>" />
                            </div>
                            <div class="customFooter divAssessContactFooter"></div>
                            <div class="customContent divAssessContactContent">
                                <label>Title:</label>
                            </div>
                            <div class="divAssessContactDivider">&nbsp;</div>
                            <div class="divAssessContactNavigation">
                                <input name="txtTitle" type="text" id="txtTitle" size="31" value="<?php echo $_POST['txtTitle']; ?>" />
                            </div>
                            <div class="customFooter divAssessContactFooter"></div>
                            <div class="customContent divAssessContactContent">
                                <label>Company:</label>
                            </div>
                            <div class="divAssessContactDivider">&nbsp;</div>
                            <div class="divAssessContactNavigation">
                                <input name="txtCompany" type="text" id="txtCompany" size="27" value="<?php echo $_POST['txtCompany']; ?>" />
                            </div>
                            <div class="customFooter divAssessContactFooter"></div>
                            <div class="customContent divAssessContactContent">
                                <label>E-Mail:</label>
                            </div>
                            <div class="divAssessContactDivider">&nbsp;</div>
                            <div class="divAssessContactNavigation">
                                <input name="txtEmail" type="text" id="txtEmail" size="40" value="<?php echo $_POST['txtEmail']; ?>" />
                            </div>
                            <div class="customFooter divAssessContactFooter"></div>
           			    </div>
                        <div id="divAssessScale">
                        	<label><strong>Please Rate Your Business Based on a scale of 1 - 10. </strong>
                            <br /><br />                            
    						1 = You Agree Completly With The Statement Made on the LEFT SIDE. 
                            <br /><br />
                            10 = You Agree Completly With The Statement made on the RIGHT SIDE.</label>
                        </div>
                        <div class="customContainer divAssessScaleContainer lblFontSize14">
                        	<div class="customContent divAssessScaleContent">
                            	<label>We <strong>DO NOT</strong> feel prepared in the event of a disaster.</label>
                            </div>
                            <div class="customThridContent divAssessScaleThridContent">
                            	 <?php $intIndex = 1;//holdes the number of radio button there will be

								//goes around creating the radio button group
								while($intIndex <= 10)
								{
									echo $intIndex." <input type=\"radio\" name=\"rdoQ1\" value=\"".$intIndex."\"";
								//$row_rsUser['Q1'] == $intIndex || 
									if ($_POST['rdoQ1'] == $intIndex)
										echo " checked /> ";
									else
										echo " /> ";
					
									//adds on to $intIndex
									$intIndex = $intIndex + 1;
								}//end of while loop?>
                            </div>
                            <div class="customNavigation divAssessScaleNavigation">
                            	<label>We feel <strong>FULLY</strong> prepared in the event of a disaster.</label>
                            </div>
                            <div class="customFooter divAssessScaleFooter"></div>
                            <div class="customContent divAssessScaleContent">
                               	<label>We <strong>DO NOT</strong> have a documented Disaster Recovery Plan.</label>
                            </div>
                            <div class="customThridContent divAssessScaleThridContent">
                                <?php $intIndex = 1;//holdes the number of radio button there will be

								//goes around creating the radio button group
								while($intIndex <= 10)
								{
									echo $intIndex." <input type=\"radio\" name=\"rdoQ2\" value=\"".$intIndex."\"";
								//$row_rsUser['Q2'] == $intIndex || 
									if ($_POST['rdoQ2'] == $intIndex)
										echo " checked /> ";
									else
										echo " /> ";
					
									//adds on to $intIndex
									$intIndex = $intIndex + 1;
								}//end of while loop?>
                            </div>
                            <div class="customNavigation divAssessScaleNavigation">
                            	<label>We <strong>HAVE</strong> a documented and implemented Disaster Recovery Plan for our business.</label>
                            </div>
                            <div class="customFooter divAssessScaleFooter"></div>
							<div class="customContent divAssessScaleContent">
                            	<label>We <strong>DO NOT</strong> regularly review and update our Disaster Recovery Plan.</label>
                            </div>
                            <div class="customThridContent divAssessScaleThridContent">
                                <?php $intIndex = 1;//holdes the number of radio button there will be

								//goes around creating the radio button group
								while($intIndex <= 10)
								{
									echo $intIndex." <input type=\"radio\" name=\"rdoQ3\" value=\"".$intIndex."\"";
								//$row_rsUser['Q3'] == $intIndex || 
									if ($_POST['rdoQ3'] == $intIndex)
										echo " checked /> ";
									else
										echo " /> ";
					
									//adds on to $intIndex
									$intIndex = $intIndex + 1;
								}//end of while loop?>
                            </div>
                            <div class="customNavigation divAssessScaleNavigation">
                            	<label>We <strong>Regularly</strong> review and update our businesses Disaster Recovery Plans.</label>
                            </div>
                            <div class="customFooter divAssessScaleFooter"></div>
                            <div class="customContent divAssessScaleContent">
                            	<label>We <strong>DO NOT</strong> train and educate our staff on concerning Disaster Recovery Plans.</label>
                            </div>
                            <div class="customThridContent divAssessScaleThridContent">
                                <?php $intIndex = 1;//holdes the number of radio button there will be

								//goes around creating the radio button group
								while($intIndex <= 10)
								{
									echo $intIndex." <input type=\"radio\" name=\"rdoQ4\" value=\"".$intIndex."\"";
								//$row_rsUser['Q4'] == $intIndex || 
									if ($_POST['rdoQ4'] == $intIndex)
										echo " checked /> ";
									else
										echo " /> ";
					
									//adds on to $intIndex
									$intIndex = $intIndex + 1;
								}//end of while loop?>
                            </div>
                            <div class="customNavigation divAssessScaleNavigation">
                            	<label>We <strong>Have</strong> regular training and awareness session for our employees.</label>
                            </div>
                            <div class="customFooter divAssessScaleFooter"></div>
                            <div class="customContent divAssessScaleContent">
                            	<label>We <strong>DO NOT</strong> have the appropriate medical, emergency and essential equipment to survive a disaster.</label>
                            </div>
                            <div class="customThridContent divAssessScaleThridContent">
                                <?php $intIndex = 1;//holdes the number of radio button there will be

								//goes around creating the radio button group
								while($intIndex <= 10)
								{
									echo $intIndex." <input type=\"radio\" name=\"rdoQ5\" value=\"".$intIndex."\"";
								//$row_rsUser['Q5'] == $intIndex || 
									if ($_POST['rdoQ5'] == $intIndex)
										echo " checked /> ";
									else
										echo " /> ";
					
									//adds on to $intIndex
									$intIndex = $intIndex + 1;
								}//end of while loop?>
                            </div>
                            <div class="customNavigation divAssessScaleNavigation">
                            	<label>We <strong>Have</strong> all of the required medical, emergency and survival equipment.</label>
                            </div>
                            <div class="customFooter divAssessScaleFooter"></div>
							<div class="customContent divAssessScaleContent">
                            	<label>We <strong>DO NOT</strong> have all of the required back-up water, sanitation and power systems.</label>
                            </div>
                            <div class="customThridContent divAssessScaleThridContent">
                                <?php $intIndex = 1;//holdes the number of radio button there will be

								//goes around creating the radio button group
								while($intIndex <= 10)
								{
									echo $intIndex." <input type=\"radio\" name=\"rdoQ6\" value=\"".$intIndex."\"";
								//$row_rsUser['Q6'] == $intIndex || 
									if ($_POST['rdoQ6'] == $intIndex)
										echo " checked /> ";
									else
										echo " /> ";
					
									//adds on to $intIndex
									$intIndex = $intIndex + 1;
								}//end of while loop?>
                            </div>
                            <div class="customNavigation divAssessScaleNavigation">
                            	<label>We <strong>have</strong> sufficent back-up water, sanitation and power supply to survive a disaster.</label>
                            </div>
                            <div class="customFooter divAssessScaleFooter"></div>
                            <div class="customContent divAssessScaleContent">
                            	<label>We <strong>DO NOT</strong> feel our Disaster Recovery Plan will allow our business to survive for 72 hrs.</label>
                            </div>
                            <div class="customThridContent divAssessScaleThridContent">
                                <?php $intIndex = 1;//holdes the number of radio button there will be

								//goes around creating the radio button group
								while($intIndex <= 10)
								{
									echo $intIndex." <input type=\"radio\" name=\"rdoQ7\" value=\"".$intIndex."\"";
								//$row_rsUser['Q7'] == $intIndex || 
									if ($_POST['rdoQ7'] == $intIndex)
										echo " checked /> ";
									else
										echo " /> ";
					
									//adds on to $intIndex
									$intIndex = $intIndex + 1;
								}//end of while loop?>
                            </div>
                            <div class="customNavigation divAssessScaleNavigation">
                            	<label>We <strong>Feel</strong> our current Disaster Recovery Plan will allow us to survive for upto 72 Hrs.</label>
                            </div>
                            <div class="customFooter divAssessScaleFooter"></div>
							<div class="customContent divAssessScaleContent">
                            	<label>We <strong>DO NOT</strong> feel in the event of a disaster we would be able to identify all of the lost items.</label>
                            </div>
                            <div class="customThridContent divAssessScaleThridContent">
                                <?php $intIndex = 1;//holdes the number of radio button there will be

								//goes around creating the radio button group
								while($intIndex <= 10)
								{
									echo $intIndex." <input type=\"radio\" name=\"rdoQ8\" value=\"".$intIndex."\"";
								//$row_rsUser['Q8'] == $intIndex || 
									if ($_POST['rdoQ8'] == $intIndex)
										echo " checked /> ";
									else
										echo " /> ";
					
									//adds on to $intIndex
									$intIndex = $intIndex + 1;
								}//end of while loop?>
                            </div>
                            <div class="customNavigation divAssessScaleNavigation">
                            	<label>We <strong>have</strong> an accurate inventory of all equipment, IT supplies and essential assets, to recover.</label>
                            </div>
                            <div class="customFooter divAssessScaleFooter"></div>
                            <div class="customContent divAssessScaleContent">
                            	<label>We <strong>DO NOT</strong> feel we would be able to resume business within 1 week of a disaster.</label>
                            </div>
                            <div class="customThridContent divAssessScaleThridContent">
                                <?php $intIndex = 1;//holdes the number of radio button there will be

								//goes around creating the radio button group
								while($intIndex <= 10)
								{
									echo $intIndex." <input type=\"radio\" name=\"rdoQ9\" value=\"".$intIndex."\"";
								//$row_rsUser['Q9'] == $intIndex || 
									if ($_POST['rdoQ9'] == $intIndex)
										echo " checked /> ";
									else
										echo " /> ";
					
									//adds on to $intIndex
									$intIndex = $intIndex + 1;
								}//end of while loop?>
                            </div>
                            <div class="customNavigation divAssessScaleNavigation">
                            	<label>We <strong>Feel</strong> we have the ability to restore our business within 72 hrs of a disaster.</label>
                            </div>
                            <div class="customFooter divAssessScaleFooter"></div>
							<div class="customContent divAssessScaleContent">
                            	<label>We <strong>DO NOT</strong> feel that we need a Disaster Recovery Plan in order to recover from a Disaster.</label>
                            </div>
                            <div class="customThridContent divAssessScaleThridContent">
                                <?php $intIndex = 1;//holdes the number of radio button there will be

								//goes around creating the radio button group
								while($intIndex <= 10)
								{
									echo $intIndex." <input type=\"radio\" name=\"rdoQ10\" value=\"".$intIndex."\"";
								//$row_rsUser['Q10'] == $intIndex || 
									if ($_POST['rdoQ10'] == $intIndex)
										echo " checked /> ";
									else
										echo " /> ";
					
									//adds on to $intIndex
									$intIndex = $intIndex + 1;
								}//end of while loop?>
                            </div>
                            <div class="customNavigation divAssessScaleNavigation">
                            	<label>We <strong>Understand</strong> the importance of having a Disaster Recovery Plan at our place of business.</label>
                            </div>
                            <div class="customFooter divAssessScaleFooter"></div>
                        </div>
                        <div class="lblFontBold">
	                        <label>To View Your Results, Please make sure you have completed each questions and the information at the top of the page.
                            <br /><br />
                            Click The Submit button to review your results.</label>
                        </div>
                        <div>
                        	<br />
		        			<input type="hidden" name="hfSubmit" value="1" />
    	       				<input name="cmdSend" type="submit" value="Send Assessment" id="cmdSend" />
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
