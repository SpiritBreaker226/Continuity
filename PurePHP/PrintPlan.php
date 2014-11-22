<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Print My Plan - My Continuity Plans - Continuity Inc. - Disaster Recovery Solutions</title>
<link rel="stylesheet" type="text/css" href="../CSS/MasterCSS.css" media="all" />

<?php require_once('LoginControl.php');?>
<?php require_once('../Connections/conContinuty.php'); 

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
	header("Location: ../LogIn.php?section=LogIn&Footer=1");	

$UserID = getUserID();//holds the user ID
$strEdition = "Basic";//Holds the Edition the uses has selected
$strBackgroudColor = "lblBasicBackgroundColor";//Holds the Current Solution Color Backgorund
$strColor = "lblBasicColor";//Holds the Current Solution Color

mysql_select_db($database_conContinuty, $conContinuty);
$LoginRS = mysql_query("SELECT * FROM users WHERE id=".$UserID, $conContinuty) or die("Get User Info".mysql_error());
$row_loginFoundUser = mysql_fetch_assoc($LoginRS);

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
}//end of else if

$intTableIndex = 1;//contorls which tables will be used to display there data?>

<script language="javascript" type="text/javascript">
	var oldonload=window.onload;//holds any prevs onload function from the js file

	//gets the onload window event checks if there is a function that is already in there
	window.onload=function(){
		//window.print();
	}//end of window.onload=function()
</script>

</head>
<body>
    <div align="center">
        <div id="divPrintPlan" align="left">
            <div align="left" class="divPrintPlanHeader">
                <img src="../images/logo1.jpg" alt="Logo" width="265" height="55" />
            </div>
            <div class="lblFontBold divFloatingMainTitle divPrintPlanHeader <?php echo $strBackgroudColor; ?>" align="center">
                <label><span class="
                <?php
                //checks if the Solution is Starnd as the Color will of thise Solution will
                //match the one in the M change to a different color
                if ($row_loginFoundUser['Solution'] == 2)
                    echo "lblEnterColor";
                else 
                    echo "lblFontRed";?> lblFontSize24">M</span>y Continuity Plans:<br /> <?php echo $strEdition; ?> Edition</label>
            </div><?php 
        
            while($intTableIndex <= 18)
            {
                $arrTablesName = array(1 =>"c2scope","C2Employee","C2Information","C2BusinessImpact","C2Crisis","C2Logistics","C2Alternate","C2Salvage","C2Customer","C2Environment","C2Immediate","C2Disaster","C2Damage","C2ITRecoveryTeam","C2Administration","C2Essential","C2Business","C2InsuranceInventory");
            
                mysql_select_db($database_conContinuty, $conContinuty);
                $rsForm = mysql_query("SELECT * FROM ".$arrTablesName[$intTableIndex]." WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
                $row_rsForm = mysql_fetch_assoc($rsForm);
                $total_rsForm = mysql_num_rows($rsForm);
                    
                //gets the next section for its ID
                //does a selection for the users data for this form
                mysql_select_db($database_conContinuty, $conContinuty); 
                $rsPlans = mysql_query("SELECT * FROM continuityplans WHERE TableName = '".$arrTablesName[$intTableIndex]."'", $conContinuty) or die(mysql_error());
                $row_rsPlans = mysql_fetch_assoc($rsPlans);
                
				//checks if to make sure that there the solution fits the data the user has entered
				if($intTableIndex != 18 && $intTableIndex != 4)
				{ ?>
					<div align="left">
						<div class="divPageTitle" align="left">
							<label class="lblPageTitle"><?php echo $row_rsPlans['sectionName']; ?></label>
						</div>
					</div>
		   <?php }//end of if ?>
				
				<!-- Scope -->
				
				<?php 
				if($intTableIndex == 1)
				{?>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 1: </label>
						<br /><br />
						<label>What is the name of your business? 
						<br />
						Business Name: <?php echo $row_rsForm['intro_01']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 2:</label>
						<br /><br />
						<label>
						What is the address of your business?
						<br /><br />
						Street Name Address: <?php echo $row_rsForm['intro_02']; ?>
						<br />
						City: <?php echo $row_rsForm['intro_03']; ?> Province: <?php echo $row_rsForm['intro_04']; ?> Postal/ZIP Code: <?php echo $row_rsForm['intro_05']; ?>
						<br />
						Country: <?php echo $row_rsForm['intro_06']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 3:</label>
						<br /><br />
						<label>Text:
						<br /><br />
						Please give a brief overview of what your business is trying to accomplish in terms of a back-up plan. If an individual were to read this it would identify the reason for your business is developing a plan?
						<br /><br />
						<?php echo $row_rsForm['intro_07']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 4:</label>
						<br /><br />
						<label>Text:
						<br /><br />
						Please identify some of the objectives you wish to reach by putting a Business Continuity Plan in place. ie. What do you expect from this plan?
						<br /><br />
						<?php echo $row_rsForm['intro_08']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- Employee -->
				
				<?php 
				if($intTableIndex == 2)
				{		
					mysql_select_db($database_conContinuty, $conContinuty);
					$rsForm2 = mysql_query("SELECT * FROM C2Employee2 WHERE C2ID= ".$row_rsForm['C2ID'], $conContinuty) or die("C2Employee:".mysql_error());
					$row_Form2 = mysql_fetch_assoc($rsForm2);
					$total_Form2 = mysql_num_rows($rsForm2);?>
							
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 1: </label>
						<label><br /><br />Please identify a Business Continuity Coordinator
						<br /><br />
						Text:
						<br /><br />
						They Will be responsible for accurate and timely delivery of information concerning to continuity of this organization.
						<br /><br />
						Name: <?php echo $row_rsForm['contact_001']; ?>
						<br />
						Job Title: <?php echo $row_rsForm['contact_002']; ?>
						<br />
						Phone: <?php echo $row_rsForm['contact_003']; ?>
						<br />
						Cell: <?php echo $row_rsForm['contact_004']; ?>
						<br />
						E-Mail: <?php echo $row_rsForm['contact_005']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 2: Business Continuity Operations Team</label>
						<br /><br />
						<label>Text:
						<br /><br />
			This individual or individuals will be responsible for assisting in completing these on-line forms. Should you require any additional information that needs to be collected you will be able to use these individual who have an extensive knowledge of the business.</label>
						<ol class="olForms">
							<li><label>Name: <?php echo $row_rsForm['contact_006']; ?> Job Title: <?php echo $row_rsForm['contact_007']; ?> Phone: <?php echo $row_rsForm['contact_008']; ?> Cell: <?php echo $row_rsForm['contact_009']; ?> E-Mail: <?php echo $row_rsForm['contact_010']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_011']; ?>Job Title: <?php echo $row_rsForm['contact_012']; ?> Phone: <?php echo $row_rsForm['contact_013']; ?> Cell: <?php echo $row_rsForm['contact_014']; ?> E-Mail: <?php echo $row_rsForm['contact_015']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_016']; ?> Job Title: <?php echo $row_rsForm['contact_017']; ?> Phone: <?php echo $row_rsForm['contact_018']; ?> Cell: <?php echo $row_rsForm['contact_019']; ?> E-Mail: <?php echo $row_rsForm['contact_020']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 3: Immediate Response Team</label>
						<br /><br />
						<label>Text:
						<br /><br />
			This individual or individuals will be the immediate response team members that will begin to perform pre-assigned tasks immediately after a disaster or disruption has occurred.
			Team Leader:</label>
						<ol class="olForms">
							<li><label>Name: <?php echo $row_rsForm['contact_021']; ?> Job Title: <?php echo $row_rsForm['contact_022']; ?> Phone: <?php echo $row_rsForm['contact_023']; ?> Cell: <?php echo $row_rsForm['contact_024']; ?> E-Mail: <?php echo $row_rsForm['contact_025']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_026']; ?> Job Title: <?php echo $row_rsForm['contact_027']; ?> Phone: <?php echo $row_rsForm['contact_028']; ?> Cell: <?php echo $row_rsForm['contact_029']; ?> E-Mail: <?php echo $row_rsForm['contact_030']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_031']; ?> Job Title: <?php echo $row_rsForm['contact_032']; ?> Phone: <?php echo $row_rsForm['contact_033']; ?> Cell: <?php echo $row_rsForm['contact_034']; ?> E-Mail: <?php echo $row_rsForm['contact_035']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 4: Disaster Management Team</label>
						<br /><br />
						<label>Text:
						<br /><br />
			This individual or individuals should have at least one person from management on it. The team will operate as the decision makers in the recovery process. In the event of a disaster this team will assemble and be prepared to receive all incoming information and make decision accordingly.</label>
						<ol class="olForms">
							<li><label>Name: <?php echo $row_rsForm['contact_036']; ?> Job Title: <?php echo $row_rsForm['contact_037']; ?> Phone: <?php echo $row_rsForm['contact_038']; ?> Cell: <?php echo $row_rsForm['contact_039']; ?> E-Mail: <?php echo $row_rsForm['contact_040']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_041']; ?> Job Title: <?php echo $row_rsForm['contact_042']; ?> Phone: <?php echo $row_rsForm['contact_043']; ?> Cell: <?php echo $row_rsForm['contact_044']; ?> E-Mail: <?php echo $row_rsForm['contact_045']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_046']; ?> Job Title: <?php echo $row_rsForm['contact_047']; ?> Phone: <?php echo $row_rsForm['contact_048']; ?> Cell: <?php echo $row_rsForm['contact_049']; ?> E-mail: <?php echo $row_rsForm['contact_050']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 5: Damage Assessment Team</label>
						<br /><br />
						<label>Text:
						<br /><br />
			This individual or team of individuals will have the responsibilities of performing a damage assessment of your business should a disaster or disruption occur. Once they have completed the assessment they will report all findings back to the Disaster Management Team.</label>
						<ol class="olForms">
							<li><label>Name: <?php echo $row_rsForm['contact_051']; ?> Job Title: <?php echo $row_rsForm['contact_052']; ?> Phone: <?php echo $row_rsForm['contact_053']; ?> Cell: <?php echo $row_rsForm['contact_054']; ?> E-Mail: <?php echo $row_rsForm['contact_055']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_056']; ?> Job Title: <?php echo $row_rsForm['contact_057']; ?> Phone: <?php echo $row_rsForm['contact_058']; ?> Cell: <?php echo $row_rsForm['contact_059']; ?> E-Mail: <?php echo $row_rsForm['contact_060']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_061']; ?> Job Title: <?php echo $row_rsForm['contact_062']; ?> Phone: <?php echo $row_rsForm['contact_063']; ?> Cell: <?php echo $row_rsForm['contact_064']; ?> E-mail: <?php echo $row_rsForm['contact_065']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 6: Information Technology Recovery Team</label>
						<br /><br />
						<label>Text:
						<br /><br />
			This individual or team of individuals should have an extensive knowledge of your business and it's IT infrastructure. In the event of a disaster their main goal will be to ensure all IT components of your business are recovered and restored</label>
						<ol class="olForms">
							<li><label>Name:  <?php echo $row_rsForm['contact_066']; ?> Job Title: <?php echo $row_rsForm['contact_067']; ?> Phone: <?php echo $row_rsForm['contact_068']; ?> Cell: <?php echo $row_rsForm['contact_069']; ?> E-Mail: <?php echo $row_rsForm['contact_070']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_071']; ?> Job Title: <?php echo $row_rsForm['contact_072']; ?> Phone: <?php echo $row_rsForm['contact_073']; ?> Cell: <?php echo $row_rsForm['contact_074']; ?> E-Mail: <?php echo $row_rsForm['contact_075']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_076']; ?> Job Title: <?php echo $row_rsForm['contact_077']; ?> Phone: <?php echo $row_rsForm['contact_078']; ?> Cell: <?php echo $row_rsForm['contact_079']; ?> E-mail: <?php echo $row_rsForm['contact_080']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 7: Administration Recovery Team</label>
						<br /><br />
						<label>Text:
						<br /><br />
			This individual or team of individuals should be composed of individuals that have knowledge of the administrative functions of your business. In the event of a disaster they will be responsible for ensuring that all functions continue to operate.</label>
						<ol class="olForms">
							<li><label>Name: <?php echo $row_rsForm['contact_081']; ?> Job Title: <?php echo $row_rsForm['contact_082']; ?> Phone: <?php echo $row_rsForm['contact_083']; ?> Cell: <?php echo $row_rsForm['contact_084']; ?> E-Mail: <?php echo $row_rsForm['contact_085']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_086']; ?> Job Title: <?php echo $row_rsForm['contact_087']; ?> Phone: <?php echo $row_rsForm['contact_088']; ?> Cell: <?php echo $row_rsForm['contact_089']; ?> E-Mail: <?php echo $row_rsForm['contact_090']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_091']; ?> Job Title: <?php echo $row_rsForm['contact_092']; ?> Phone: <?php echo $row_rsForm['contact_093']; ?> Cell: <?php echo $row_rsForm['contact_094']; ?> E-mail: <?php echo $row_rsForm['contact_095']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 8: Essential Functions Recovery Team</label>
						<br /><br />
						<label>Text:
						<br /><br />
			This individual or team of individuals should be knowledgeable in the specific essential functions that you business performs on a day to day basis. In the event of a disaster or disruption this team will be responsible for ensuring this essential function is fully operational.</label>
						<ol class="olForms">
							<li><label>Name: <?php echo $row_rsForm['contact_096']; ?> Job Title: <?php echo $row_rsForm['contact_097']; ?> Phone: <?php echo $row_rsForm['contact_098']; ?> Cell: <?php echo $row_rsForm['contact_099']; ?> E-Mail: <?php echo $row_rsForm['contact_100']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_101']; ?> Job Title: <?php echo $row_rsForm['contact_102']; ?> Phone: <?php echo $row_rsForm['contact_103']; ?> Cell: <?php echo $row_rsForm['contact_104']; ?> E-Mail: <?php echo $row_rsForm['contact_105']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_106']; ?> Job Title: <?php echo $row_rsForm['contact_107']; ?> Phone: <?php echo $row_rsForm['contact_108']; ?> Cell: <?php echo $row_rsForm['contact_109']; ?> E-mail: <?php echo $row_rsForm['contact_110']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 9: Business Recovery Support Team</label>
						<br /><br />
						<label>Text:
						<br /><br />
			This individual or team of individuals will provide services to all critical operations and functions of your business. In the event of a disaster or disruption this team will ensure support is delivered to all essential areas of your business.</label>
						<ol class="olForms">
							<li><label>Name: <?php echo $row_rsForm['contact_111']; ?> Job Title: <?php echo $row_rsForm['contact_112']; ?> Phone: <?php echo $row_rsForm['contact_113']; ?> Cell: <?php echo $row_rsForm['contact_114']; ?> E-Mail: <?php echo $row_rsForm['contact_115']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_116']; ?> Job Title: <?php echo $row_rsForm['contact_117']; ?> Phone: <?php echo $row_rsForm['contact_118']; ?> Cell: <?php echo $row_rsForm['contact_119']; ?> E-Mail: <?php echo $row_rsForm['contact_120']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_121']; ?> Job Title: <?php echo $row_rsForm['contact_122']; ?> Phone: <?php echo $row_rsForm['contact_123']; ?> Cell: <?php echo $row_rsForm['contact_124']; ?> E-mail: <?php echo $row_rsForm['contact_125']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 10: Sr. Management &amp; Executive Decision Maker</label>
						<br /><br />
						<label>Text:
						<br /><br />
			In ever disaster there needs to be a Sr. Personnel that is able to make final decisions in terms of recovery. This individual will make all final decisions and will be consulted in the event any team needs to know the next plan of action. All decision and final says will be left up to this Sr. Management member.</label>
						<ol class="olForms">
							<li><label>Name: <?php echo $row_rsForm['contact_126']; ?> Job Title: <?php echo $row_rsForm['contact_127']; ?> Phone: <?php echo $row_rsForm['contact_128']; ?> Cell: <?php echo $row_rsForm['contact_129']; ?> E-Mail: <?php echo $row_rsForm['contact_130']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_131']; ?> Job Title: <?php echo $row_rsForm['contact_132']; ?> Phone: <?php echo $row_rsForm['contact_133']; ?> Cell: <?php echo $row_rsForm['contact_134']; ?> E-Mail: <?php echo $row_rsForm['contact_135']; ?></label></li>
							<li><label>Name: <?php echo $row_rsForm['contact_136']; ?> Job Title: <?php echo $row_rsForm['contact_137']; ?> Phone: <?php echo $row_rsForm['contact_138']; ?> Cell: <?php echo $row_rsForm['contact_139']; ?> E-mail: <?php echo $row_rsForm['contact_140']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 11: Please Identify any and all employee contacts and essential information:</label>
						<br /><br />
					   <?php 
						  //checks which Solution the use is using Standard for 150 Employess or Basic at 20 Employees							                           
						  if($row_loginFoundUser['Solution'] == 2)
							echo "<label><strong>x 150 Employees</strong></label>";
						  else
							echo "<label><strong>x 20 Employees</strong></label>";?>
						<ol class="olForms">
							<li><label>Employee Name: <?php echo $row_rsForm['contact_141']; ?> Title: <?php echo $row_rsForm['contact_142']; ?> Phone: <?php echo $row_rsForm['contact_143']; ?> Cell: <?php echo $row_rsForm['contact_144']; ?> E-Mail: <?php echo $row_rsForm['contact_145']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_146']; ?> Title: <?php echo $row_rsForm['contact_147']; ?> Phone: <?php echo $row_rsForm['contact_148']; ?> Cell: <?php echo $row_rsForm['contact_149']; ?> E-Mail: <?php echo $row_rsForm['contact_150']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_151']; ?> Title: <?php echo $row_rsForm['contact_152']; ?> Phone: <?php echo $row_rsForm['contact_153']; ?> Cell: <?php echo $row_rsForm['contact_154']; ?> E-Mail: <?php echo $row_rsForm['contact_155']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_156']; ?> Title: <?php echo $row_rsForm['contact_157']; ?> Phone: <?php echo $row_rsForm['contact_158']; ?> Cell: <?php echo $row_rsForm['contact_159']; ?> E-Mail: <?php echo $row_rsForm['contact_160']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_161']; ?> Title: <?php echo $row_rsForm['contact_162']; ?> Phone: <?php echo $row_rsForm['contact_163']; ?> Cell: <?php echo $row_rsForm['contact_164']; ?> E-Mail: <?php echo $row_rsForm['contact_165']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_166']; ?> Title: <?php echo $row_rsForm['contact_167']; ?> Phone: <?php echo $row_rsForm['contact_168']; ?> Cell: <?php echo $row_rsForm['contact_169']; ?> E-Mail: <?php echo $row_rsForm['contact_170']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_171']; ?> Title: <?php echo $row_rsForm['contact_172']; ?> Phone: <?php echo $row_rsForm['contact_173']; ?> Cell: <?php echo $row_rsForm['contact_174']; ?> E-Mail: <?php echo $row_rsForm['contact_175']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_176']; ?> Title: <?php echo $row_rsForm['contact_177']; ?> Phone: <?php echo $row_rsForm['contact_178']; ?> Cell: <?php echo $row_rsForm['contact_179']; ?> E-Mail: <?php echo $row_rsForm['contact_180']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_181']; ?> Title: <?php echo $row_rsForm['contact_182']; ?> Phone: <?php echo $row_rsForm['contact_183']; ?> Cell: <?php echo $row_rsForm['contact_184']; ?> E-Mail: <?php echo $row_rsForm['contact_185']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_186']; ?> Title: <?php echo $row_rsForm['contact_187']; ?> Phone: <?php echo $row_rsForm['contact_188']; ?> Cell: <?php echo $row_rsForm['contact_189']; ?>E-Mail: <?php echo $row_rsForm['contact_190']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_191']; ?> Title: <?php echo $row_rsForm['contact_192']; ?> Phone: <?php echo $row_rsForm['contact_193']; ?> Cell: <?php echo $row_rsForm['contact_194']; ?> E-Mail: <?php echo $row_rsForm['contact_195']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_196']; ?> Title: <?php echo $row_rsForm['contact_197']; ?> Phone: <?php echo $row_rsForm['contact_198']; ?> Cell: <?php echo $row_rsForm['contact_199']; ?> E-Mail: <?php echo $row_rsForm['contact_200']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_201']; ?> Title: <?php echo $row_rsForm['contact_202']; ?> Phone: <?php echo $row_rsForm['contact_203']; ?> Cell: <?php echo $row_rsForm['contact_204']; ?> E-Mail: <?php echo $row_rsForm['contact_205']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_206']; ?> Title: <?php echo $row_rsForm['contact_207']; ?> Phone: <?php echo $row_rsForm['contact_208']; ?> Cell: <?php echo $row_rsForm['contact_209']; ?> E-Mail: <?php echo $row_rsForm['contact_210']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_211']; ?> Title: <?php echo $row_rsForm['contact_212']; ?> Phone: <?php echo $row_rsForm['contact_213']; ?> Cell: <?php echo $row_rsForm['contact_214']; ?> E-Mail: <?php echo $row_rsForm['contact_215']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_216']; ?> Title: <?php echo $row_rsForm['contact_217']; ?> Phone: <?php echo $row_rsForm['contact_218']; ?> Cell: <?php echo $row_rsForm['contact_219']; ?> E-Mail: <?php echo $row_rsForm['contact_220']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_221']; ?> Title: <?php echo $row_rsForm['contact_222']; ?> Phone: <?php echo $row_rsForm['contact_223']; ?> Cell: <?php echo $row_rsForm['contact_224']; ?> E-Mail: <?php echo $row_rsForm['contact_225']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_226']; ?> Title: <?php echo $row_rsForm['contact_227']; ?> Phone: <?php echo $row_rsForm['contact_228']; ?> Cell: <?php echo $row_rsForm['contact_229']; ?> E-Mail: <?php echo $row_rsForm['contact_230']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_231']; ?> Title: <?php echo $row_rsForm['contact_232']; ?> Phone: <?php echo $row_rsForm['contact_233']; ?> Cell: <?php echo $row_rsForm['contact_234']; ?> E-Mail: <?php echo $row_rsForm['contact_235']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['contact_236']; ?> Title: <?php echo $row_rsForm['contact_237']; ?> Phone: <?php echo $row_rsForm['contact_238']; ?> Cell: <?php echo $row_rsForm['contact_239']; ?> E-Mail: <?php echo $row_rsForm['contact_240']; ?></label></li>
							
							<?php 
							// this will display only for Standard
							if ($row_loginFoundUser['Solution'] == 2)
							{ ?>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_375']; ?> Title: <?php echo $row_rsForm['contact_376']; ?> Phone: <?php echo $row_rsForm['contact_377']; ?> Cell: <?php echo $row_rsForm['contact_378']; ?> E-Mail: <?php echo $row_rsForm['contact_379']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_380']; ?> Title: <?php echo $row_rsForm['contact_381']; ?> Phone: <?php echo $row_rsForm['contact_382']; ?> Cell: <?php echo $row_rsForm['contact_383']; ?> E-Mail: <?php echo $row_rsForm['contact_384']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_385']; ?> Title: <?php echo $row_rsForm['contact_386']; ?> Phone: <?php echo $row_rsForm['contact_387']; ?> Cell: <?php echo $row_rsForm['contact_388']; ?> E-Mail: <?php echo $row_rsForm['contact_389']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_390']; ?> Title: <?php echo $row_rsForm['contact_391']; ?> Phone: <?php echo $row_rsForm['contact_392']; ?> Cell: <?php echo $row_rsForm['contact_393']; ?> E-Mail: <?php echo $row_rsForm['contact_394']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_395']; ?> Title: <?php echo $row_rsForm['contact_396']; ?> Phone: <?php echo $row_rsForm['contact_397']; ?> Cell: <?php echo $row_rsForm['contact_398']; ?> E-Mail: <?php echo $row_rsForm['contact_399']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_400']; ?> Title: <?php echo $row_rsForm['contact_401']; ?> Phone: <?php echo $row_rsForm['contact_402']; ?> Cell: <?php echo $row_rsForm['contact_403']; ?> E-Mail: <?php echo $row_rsForm['contact_404']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_405']; ?> Title: <?php echo $row_rsForm['contact_406']; ?> Phone: <?php echo $row_rsForm['contact_407']; ?> Cell: <?php echo $row_rsForm['contact_408']; ?> E-Mail: <?php echo $row_rsForm['contact_409']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_410']; ?> Title: <?php echo $row_rsForm['contact_411']; ?> Phone: <?php echo $row_rsForm['contact_412']; ?> Cell: <?php echo $row_rsForm['contact_413']; ?> E-Mail: <?php echo $row_rsForm['contact_414']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_415']; ?> Title: <?php echo $row_rsForm['contact_416']; ?> Phone: <?php echo $row_rsForm['contact_417']; ?> Cell: <?php echo $row_rsForm['contact_418']; ?> E-Mail: <?php echo $row_rsForm['contact_419']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_420']; ?> Title: <?php echo $row_rsForm['contact_421']; ?> Phone: <?php echo $row_rsForm['contact_422']; ?> Cell: <?php echo $row_rsForm['contact_423']; ?> E-Mail: <?php echo $row_rsForm['contact_424']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_425']; ?> Title: <?php echo $row_rsForm['contact_426']; ?> Phone: <?php echo $row_rsForm['contact_427']; ?> Cell: <?php echo $row_rsForm['contact_428']; ?> E-Mail: <?php echo $row_rsForm['contact_429']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_430']; ?> Title: <?php echo $row_rsForm['contact_431']; ?> Phone: <?php echo $row_rsForm['contact_432']; ?> Cell: <?php echo $row_rsForm['contact_433']; ?> E-Mail: <?php echo $row_rsForm['contact_434']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_435']; ?> Title: <?php echo $row_rsForm['contact_436']; ?> Phone: <?php echo $row_rsForm['contact_437']; ?> Cell: <?php echo $row_rsForm['contact_438']; ?> E-Mail: <?php echo $row_rsForm['contact_439']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_440']; ?> Title: <?php echo $row_rsForm['contact_441']; ?> Phone: <?php echo $row_rsForm['contact_442']; ?> Cell: <?php echo $row_rsForm['contact_443']; ?> E-Mail: <?php echo $row_rsForm['contact_444']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_445']; ?> Title: <?php echo $row_rsForm['contact_446']; ?> Phone: <?php echo $row_rsForm['contact_447']; ?> Cell: <?php echo $row_rsForm['contact_448']; ?> E-Mail: <?php echo $row_rsForm['contact_449']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_450']; ?> Title: <?php echo $row_rsForm['contact_451']; ?> Phone: <?php echo $row_rsForm['contact_452']; ?> Cell: <?php echo $row_rsForm['contact_453']; ?> E-Mail: <?php echo $row_rsForm['contact_454']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_455']; ?> Title: <?php echo $row_rsForm['contact_456']; ?> Phone: <?php echo $row_rsForm['contact_457']; ?> Cell: <?php echo $row_rsForm['contact_458']; ?> E-Mail: <?php echo $row_rsForm['contact_459']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_460']; ?> Title: <?php echo $row_rsForm['contact_461']; ?> Phone: <?php echo $row_rsForm['contact_462']; ?> Cell: <?php echo $row_rsForm['contact_463']; ?> E-Mail: <?php echo $row_rsForm['contact_464']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_465']; ?> Title: <?php echo $row_rsForm['contact_466']; ?> Phone: <?php echo $row_rsForm['contact_467']; ?> Cell: <?php echo $row_rsForm['contact_468']; ?> E-Mail: <?php echo $row_rsForm['contact_469']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_470']; ?> Title: <?php echo $row_rsForm['contact_471']; ?> Phone: <?php echo $row_rsForm['contact_472']; ?> Cell: <?php echo $row_rsForm['contact_473']; ?> E-Mail: <?php echo $row_rsForm['contact_474']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_475']; ?> Title: <?php echo $row_rsForm['contact_476']; ?> Phone: <?php echo $row_rsForm['contact_477']; ?> Cell: <?php echo $row_rsForm['contact_478']; ?> E-Mail: <?php echo $row_rsForm['contact_479']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_480']; ?> Title: <?php echo $row_rsForm['contact_481']; ?> Phone: <?php echo $row_rsForm['contact_482']; ?> Cell: <?php echo $row_rsForm['contact_483']; ?> E-Mail: <?php echo $row_rsForm['contact_484']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_485']; ?> Title: <?php echo $row_rsForm['contact_486']; ?> Phone: <?php echo $row_rsForm['contact_487']; ?> Cell: <?php echo $row_rsForm['contact_488']; ?> E-Mail: <?php echo $row_rsForm['contact_489']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_490']; ?> Title: <?php echo $row_rsForm['contact_491']; ?> Phone: <?php echo $row_rsForm['contact_492']; ?> Cell: <?php echo $row_rsForm['contact_493']; ?> E-Mail: <?php echo $row_rsForm['contact_494']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_495']; ?> Title: <?php echo $row_rsForm['contact_496']; ?> Phone: <?php echo $row_rsForm['contact_497']; ?> Cell: <?php echo $row_rsForm['contact_498']; ?> E-Mail: <?php echo $row_rsForm['contact_499']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_500']; ?> Title: <?php echo $row_rsForm['contact_501']; ?> Phone: <?php echo $row_rsForm['contact_502']; ?> Cell: <?php echo $row_rsForm['contact_503']; ?> E-Mail: <?php echo $row_rsForm['contact_504']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_505']; ?> Title: <?php echo $row_rsForm['contact_506']; ?> Phone: <?php echo $row_rsForm['contact_507']; ?> Cell: <?php echo $row_rsForm['contact_508']; ?> E-Mail: <?php echo $row_rsForm['contact_509']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_510']; ?> Title: <?php echo $row_rsForm['contact_511']; ?> Phone: <?php echo $row_rsForm['contact_512']; ?> Cell: <?php echo $row_rsForm['contact_513']; ?> E-Mail: <?php echo $row_rsForm['contact_514']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_515']; ?> Title: <?php echo $row_rsForm['contact_516']; ?> Phone: <?php echo $row_rsForm['contact_517']; ?> Cell: <?php echo $row_rsForm['contact_518']; ?> E-Mail: <?php echo $row_rsForm['contact_519']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_520']; ?> Title: <?php echo $row_rsForm['contact_521']; ?> Phone: <?php echo $row_rsForm['contact_522']; ?> Cell: <?php echo $row_rsForm['contact_523']; ?> E-Mail: <?php echo $row_rsForm['contact_524']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_525']; ?> Title: <?php echo $row_rsForm['contact_526']; ?> Phone: <?php echo $row_rsForm['contact_527']; ?> Cell: <?php echo $row_rsForm['contact_528']; ?> E-Mail: <?php echo $row_rsForm['contact_529']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_530']; ?> Title: <?php echo $row_rsForm['contact_531']; ?> Phone: <?php echo $row_rsForm['contact_532']; ?> Cell: <?php echo $row_rsForm['contact_533']; ?> E-Mail: <?php echo $row_rsForm['contact_534']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_535']; ?> Title: <?php echo $row_rsForm['contact_536']; ?> Phone: <?php echo $row_rsForm['contact_537']; ?> Cell: <?php echo $row_rsForm['contact_538']; ?> E-Mail: <?php echo $row_rsForm['contact_539']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_540']; ?> Title: <?php echo $row_rsForm['contact_541']; ?> Phone: <?php echo $row_rsForm['contact_542']; ?> Cell: <?php echo $row_rsForm['contact_543']; ?> E-Mail: <?php echo $row_rsForm['contact_544']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_545']; ?> Title: <?php echo $row_rsForm['contact_546']; ?> Phone: <?php echo $row_rsForm['contact_547']; ?> Cell: <?php echo $row_rsForm['contact_548']; ?> E-Mail: <?php echo $row_rsForm['contact_549']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_550']; ?> Title: <?php echo $row_rsForm['contact_551']; ?> Phone: <?php echo $row_rsForm['contact_552']; ?> Cell: <?php echo $row_rsForm['contact_553']; ?> E-Mail: <?php echo $row_rsForm['contact_554']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_555']; ?> Title: <?php echo $row_rsForm['contact_556']; ?> Phone: <?php echo $row_rsForm['contact_557']; ?> Cell: <?php echo $row_rsForm['contact_558']; ?> E-Mail: <?php echo $row_rsForm['contact_559']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_560']; ?> Title: <?php echo $row_rsForm['contact_561']; ?> Phone: <?php echo $row_rsForm['contact_562']; ?> Cell: <?php echo $row_rsForm['contact_563']; ?> E-Mail: <?php echo $row_rsForm['contact_564']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_565']; ?> Title: <?php echo $row_rsForm['contact_566']; ?> Phone: <?php echo $row_rsForm['contact_567']; ?> Cell: <?php echo $row_rsForm['contact_568']; ?> E-Mail: <?php echo $row_rsForm['contact_569']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_570']; ?> Title: <?php echo $row_rsForm['contact_571']; ?> Phone: <?php echo $row_rsForm['contact_572']; ?> Cell: <?php echo $row_rsForm['contact_573']; ?> E-Mail: <?php echo $row_rsForm['contact_574']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_575']; ?> Title: <?php echo $row_rsForm['contact_576']; ?> Phone: <?php echo $row_rsForm['contact_577']; ?> Cell: <?php echo $row_rsForm['contact_578']; ?> E-Mail: <?php echo $row_rsForm['contact_579']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_580']; ?> Title: <?php echo $row_rsForm['contact_581']; ?> Phone: <?php echo $row_rsForm['contact_582']; ?> Cell: <?php echo $row_rsForm['contact_583']; ?> E-Mail: <?php echo $row_rsForm['contact_584']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_585']; ?> Title: <?php echo $row_rsForm['contact_586']; ?> Phone: <?php echo $row_rsForm['contact_587']; ?> Cell: <?php echo $row_rsForm['contact_588']; ?> E-Mail: <?php echo $row_rsForm['contact_589']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_590']; ?> Title: <?php echo $row_rsForm['contact_591']; ?> Phone: <?php echo $row_rsForm['contact_592']; ?> Cell: <?php echo $row_rsForm['contact_593']; ?> E-Mail: <?php echo $row_rsForm['contact_594']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_595']; ?> Title: <?php echo $row_rsForm['contact_596']; ?> Phone: <?php echo $row_rsForm['contact_597']; ?> Cell: <?php echo $row_rsForm['contact_598']; ?> E-Mail: <?php echo $row_rsForm['contact_599']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_600']; ?> Title: <?php echo $row_rsForm['contact_601']; ?> Phone: <?php echo $row_rsForm['contact_602']; ?> Cell: <?php echo $row_rsForm['contact_603']; ?> E-Mail: <?php echo $row_rsForm['contact_604']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_605']; ?> Title: <?php echo $row_rsForm['contact_606']; ?> Phone: <?php echo $row_rsForm['contact_607']; ?> Cell: <?php echo $row_rsForm['contact_608']; ?> E-Mail: <?php echo $row_rsForm['contact_609']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_610']; ?> Title: <?php echo $row_rsForm['contact_611']; ?> Phone: <?php echo $row_rsForm['contact_612']; ?> Cell: <?php echo $row_rsForm['contact_613']; ?> E-Mail: <?php echo $row_rsForm['contact_614']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_615']; ?> Title: <?php echo $row_rsForm['contact_616']; ?> Phone: <?php echo $row_rsForm['contact_617']; ?> Cell: <?php echo $row_rsForm['contact_618']; ?> E-Mail: <?php echo $row_rsForm['contact_619']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_620']; ?> Title: <?php echo $row_rsForm['contact_621']; ?> Phone: <?php echo $row_rsForm['contact_622']; ?> Cell: <?php echo $row_rsForm['contact_623']; ?> E-Mail: <?php echo $row_rsForm['contact_624']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_625']; ?> Title: <?php echo $row_rsForm['contact_626']; ?> Phone: <?php echo $row_rsForm['contact_627']; ?> Cell: <?php echo $row_rsForm['contact_628']; ?> E-Mail: <?php echo $row_rsForm['contact_629']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_630']; ?> Title: <?php echo $row_rsForm['contact_631']; ?> Phone: <?php echo $row_rsForm['contact_632']; ?> Cell: <?php echo $row_rsForm['contact_633']; ?> E-Mail: <?php echo $row_rsForm['contact_634']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_635']; ?> Title: <?php echo $row_rsForm['contact_636']; ?> Phone: <?php echo $row_rsForm['contact_637']; ?> Cell: <?php echo $row_rsForm['contact_638']; ?> E-Mail: <?php echo $row_rsForm['contact_639']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_640']; ?> Title: <?php echo $row_rsForm['contact_641']; ?> Phone: <?php echo $row_rsForm['contact_642']; ?> Cell: <?php echo $row_rsForm['contact_643']; ?> E-Mail: <?php echo $row_rsForm['contact_644']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_645']; ?> Title: <?php echo $row_rsForm['contact_646']; ?> Phone: <?php echo $row_rsForm['contact_647']; ?> Cell: <?php echo $row_rsForm['contact_648']; ?> E-Mail: <?php echo $row_rsForm['contact_649']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_650']; ?> Title: <?php echo $row_rsForm['contact_651']; ?> Phone: <?php echo $row_rsForm['contact_652']; ?> Cell: <?php echo $row_rsForm['contact_653']; ?> E-Mail: <?php echo $row_rsForm['contact_654']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_655']; ?> Title: <?php echo $row_rsForm['contact_656']; ?> Phone: <?php echo $row_rsForm['contact_657']; ?> Cell: <?php echo $row_rsForm['contact_658']; ?> E-Mail: <?php echo $row_rsForm['contact_659']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_660']; ?> Title: <?php echo $row_rsForm['contact_661']; ?> Phone: <?php echo $row_rsForm['contact_662']; ?> Cell: <?php echo $row_rsForm['contact_663']; ?> E-Mail: <?php echo $row_rsForm['contact_664']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_665']; ?> Title: <?php echo $row_rsForm['contact_666']; ?> Phone: <?php echo $row_rsForm['contact_667']; ?> Cell: <?php echo $row_rsForm['contact_668']; ?> E-Mail: <?php echo $row_rsForm['contact_669']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_670']; ?> Title: <?php echo $row_rsForm['contact_671']; ?> Phone: <?php echo $row_rsForm['contact_672']; ?> Cell: <?php echo $row_rsForm['contact_673']; ?> E-Mail: <?php echo $row_rsForm['contact_674']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_675']; ?> Title: <?php echo $row_rsForm['contact_676']; ?> Phone: <?php echo $row_rsForm['contact_677']; ?> Cell: <?php echo $row_rsForm['contact_678']; ?> E-Mail: <?php echo $row_rsForm['contact_679']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_680']; ?> Title: <?php echo $row_rsForm['contact_681']; ?> Phone: <?php echo $row_rsForm['contact_682']; ?> Cell: <?php echo $row_rsForm['contact_683']; ?> E-Mail: <?php echo $row_rsForm['contact_684']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_685']; ?> Title: <?php echo $row_rsForm['contact_686']; ?> Phone: <?php echo $row_rsForm['contact_687']; ?> Cell: <?php echo $row_rsForm['contact_688']; ?> E-Mail: <?php echo $row_rsForm['contact_689']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_690']; ?> Title: <?php echo $row_rsForm['contact_691']; ?> Phone: <?php echo $row_rsForm['contact_692']; ?> Cell: <?php echo $row_rsForm['contact_693']; ?> E-Mail: <?php echo $row_rsForm['contact_694']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_695']; ?> Title: <?php echo $row_rsForm['contact_696']; ?> Phone: <?php echo $row_rsForm['contact_697']; ?> Cell: <?php echo $row_rsForm['contact_698']; ?> E-Mail: <?php echo $row_rsForm['contact_699']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_700']; ?> Title: <?php echo $row_rsForm['contact_701']; ?> Phone: <?php echo $row_rsForm['contact_702']; ?> Cell: <?php echo $row_rsForm['contact_703']; ?> E-Mail: <?php echo $row_rsForm['contact_704']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_705']; ?> Title: <?php echo $row_rsForm['contact_706']; ?> Phone: <?php echo $row_rsForm['contact_707']; ?> Cell: <?php echo $row_rsForm['contact_708']; ?> E-Mail: <?php echo $row_rsForm['contact_709']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_710']; ?> Title: <?php echo $row_rsForm['contact_711']; ?> Phone: <?php echo $row_rsForm['contact_712']; ?> Cell: <?php echo $row_rsForm['contact_713']; ?> E-Mail: <?php echo $row_rsForm['contact_714']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_715']; ?> Title: <?php echo $row_rsForm['contact_716']; ?> Phone: <?php echo $row_rsForm['contact_717']; ?> Cell: <?php echo $row_rsForm['contact_718']; ?> E-Mail: <?php echo $row_rsForm['contact_719']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_720']; ?> Title: <?php echo $row_rsForm['contact_721']; ?> Phone: <?php echo $row_rsForm['contact_722']; ?> Cell: <?php echo $row_rsForm['contact_723']; ?> E-Mail: <?php echo $row_rsForm['contact_724']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_725']; ?> Title: <?php echo $row_rsForm['contact_726']; ?> Phone: <?php echo $row_rsForm['contact_727']; ?> Cell: <?php echo $row_rsForm['contact_728']; ?> E-Mail: <?php echo $row_rsForm['contact_729']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_730']; ?> Title: <?php echo $row_rsForm['contact_731']; ?> Phone: <?php echo $row_rsForm['contact_732']; ?> Cell: <?php echo $row_rsForm['contact_733']; ?> E-Mail: <?php echo $row_rsForm['contact_734']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_735']; ?> Title: <?php echo $row_rsForm['contact_736']; ?> Phone: <?php echo $row_rsForm['contact_737']; ?> Cell: <?php echo $row_rsForm['contact_738']; ?> E-Mail: <?php echo $row_rsForm['contact_739']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_740']; ?> Title: <?php echo $row_rsForm['contact_741']; ?> Phone: <?php echo $row_rsForm['contact_742']; ?> Cell: <?php echo $row_rsForm['contact_743']; ?> E-Mail: <?php echo $row_rsForm['contact_744']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_745']; ?> Title: <?php echo $row_rsForm['contact_746']; ?> Phone: <?php echo $row_rsForm['contact_747']; ?> Cell: <?php echo $row_rsForm['contact_748']; ?> E-Mail: <?php echo $row_rsForm['contact_749']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_750']; ?> Title: <?php echo $row_rsForm['contact_751']; ?> Phone: <?php echo $row_rsForm['contact_752']; ?> Cell: <?php echo $row_rsForm['contact_753']; ?> E-Mail: <?php echo $row_rsForm['contact_754']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_755']; ?> Title: <?php echo $row_rsForm['contact_756']; ?> Phone: <?php echo $row_rsForm['contact_757']; ?> Cell: <?php echo $row_rsForm['contact_758']; ?> E-Mail: <?php echo $row_rsForm['contact_759']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_760']; ?> Title: <?php echo $row_rsForm['contact_761']; ?> Phone: <?php echo $row_rsForm['contact_762']; ?> Cell: <?php echo $row_rsForm['contact_763']; ?> E-Mail: <?php echo $row_rsForm['contact_764']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_765']; ?> Title: <?php echo $row_rsForm['contact_766']; ?> Phone: <?php echo $row_rsForm['contact_767']; ?> Cell: <?php echo $row_rsForm['contact_768']; ?> E-Mail: <?php echo $row_rsForm['contact_769']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_770']; ?> Title: <?php echo $row_rsForm['contact_771']; ?> Phone: <?php echo $row_rsForm['contact_772']; ?> Cell: <?php echo $row_rsForm['contact_773']; ?> E-Mail: <?php echo $row_rsForm['contact_774']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_775']; ?> Title: <?php echo $row_rsForm['contact_776']; ?> Phone: <?php echo $row_rsForm['contact_777']; ?> Cell: <?php echo $row_rsForm['contact_778']; ?> E-Mail: <?php echo $row_rsForm['contact_779']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_780']; ?> Title: <?php echo $row_rsForm['contact_781']; ?> Phone: <?php echo $row_rsForm['contact_782']; ?> Cell: <?php echo $row_rsForm['contact_783']; ?> E-Mail: <?php echo $row_rsForm['contact_784']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_785']; ?> Title: <?php echo $row_rsForm['contact_786']; ?> Phone: <?php echo $row_rsForm['contact_787']; ?> Cell: <?php echo $row_rsForm['contact_788']; ?> E-Mail: <?php echo $row_rsForm['contact_789']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_790']; ?> Title: <?php echo $row_rsForm['contact_791']; ?> Phone: <?php echo $row_rsForm['contact_792']; ?> Cell: <?php echo $row_rsForm['contact_793']; ?> E-Mail: <?php echo $row_rsForm['contact_794']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm['contact_795']; ?> Title: <?php echo $row_rsForm['contact_796']; ?> Phone: <?php echo $row_rsForm['contact_797']; ?> Cell: <?php echo $row_rsForm2['contact_798']; ?> E-Mail: <?php echo $row_rsForm2['contact_799']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_800']; ?> Title: <?php echo $row_rsForm2['contact_801']; ?> Phone: <?php echo $row_rsForm2['contact_802']; ?> Cell: <?php echo $row_rsForm2['contact_803']; ?> E-Mail: <?php echo $row_rsForm2['contact_804']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_805']; ?> Title: <?php echo $row_rsForm2['contact_806']; ?> Phone: <?php echo $row_rsForm2['contact_807']; ?> Cell: <?php echo $row_rsForm2['contact_808']; ?> E-Mail: <?php echo $row_rsForm2['contact_809']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_810']; ?> Title: <?php echo $row_rsForm2['contact_811']; ?> Phone: <?php echo $row_rsForm2['contact_812']; ?> Cell: <?php echo $row_rsForm2['contact_813']; ?> E-Mail: <?php echo $row_rsForm2['contact_814']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_815']; ?> Title: <?php echo $row_rsForm2['contact_816']; ?> Phone: <?php echo $row_rsForm2['contact_817']; ?> Cell: <?php echo $row_rsForm2['contact_818']; ?> E-Mail: <?php echo $row_rsForm2['contact_819']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_820']; ?> Title: <?php echo $row_rsForm2['contact_821']; ?> Phone: <?php echo $row_rsForm2['contact_822']; ?> Cell: <?php echo $row_rsForm2['contact_823']; ?> E-Mail: <?php echo $row_rsForm2['contact_824']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_825']; ?> Title: <?php echo $row_rsForm2['contact_826']; ?> Phone: <?php echo $row_rsForm2['contact_827']; ?> Cell: <?php echo $row_rsForm2['contact_828']; ?> E-Mail: <?php echo $row_rsForm2['contact_829']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_830']; ?> Title: <?php echo $row_rsForm2['contact_831']; ?> Phone: <?php echo $row_rsForm2['contact_832']; ?> Cell: <?php echo $row_rsForm2['contact_833']; ?> E-Mail: <?php echo $row_rsForm2['contact_834']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_835']; ?> Title: <?php echo $row_rsForm2['contact_836']; ?> Phone: <?php echo $row_rsForm2['contact_837']; ?> Cell: <?php echo $row_rsForm2['contact_838']; ?> E-Mail: <?php echo $row_rsForm2['contact_839']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_840']; ?> Title: <?php echo $row_rsForm2['contact_841']; ?> Phone: <?php echo $row_rsForm2['contact_842']; ?> Cell: <?php echo $row_rsForm2['contact_843']; ?> E-Mail: <?php echo $row_rsForm2['contact_844']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_845']; ?> Title: <?php echo $row_rsForm2['contact_846']; ?> Phone: <?php echo $row_rsForm2['contact_847']; ?> Cell: <?php echo $row_rsForm2['contact_848']; ?> E-Mail: <?php echo $row_rsForm2['contact_849']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_850']; ?> Title: <?php echo $row_rsForm2['contact_851']; ?> Phone: <?php echo $row_rsForm2['contact_852']; ?> Cell: <?php echo $row_rsForm2['contact_853']; ?> E-Mail: <?php echo $row_rsForm2['contact_854']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_855']; ?> Title: <?php echo $row_rsForm2['contact_856']; ?> Phone: <?php echo $row_rsForm2['contact_857']; ?> Cell: <?php echo $row_rsForm2['contact_858']; ?> E-Mail: <?php echo $row_rsForm2['contact_859']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_860']; ?> Title: <?php echo $row_rsForm2['contact_861']; ?> Phone: <?php echo $row_rsForm2['contact_862']; ?> Cell: <?php echo $row_rsForm2['contact_863']; ?> E-Mail: <?php echo $row_rsForm2['contact_864']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_865']; ?> Title: <?php echo $row_rsForm2['contact_866']; ?> Phone: <?php echo $row_rsForm2['contact_867']; ?> Cell: <?php echo $row_rsForm2['contact_868']; ?> E-Mail: <?php echo $row_rsForm2['contact_869']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_870']; ?> Title: <?php echo $row_rsForm2['contact_871']; ?> Phone: <?php echo $row_rsForm2['contact_872']; ?> Cell: <?php echo $row_rsForm2['contact_873']; ?> E-Mail: <?php echo $row_rsForm2['contact_874']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_875']; ?> Title: <?php echo $row_rsForm2['contact_876']; ?> Phone: <?php echo $row_rsForm2['contact_877']; ?> Cell: <?php echo $row_rsForm2['contact_878']; ?> E-Mail: <?php echo $row_rsForm2['contact_879']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_880']; ?> Title: <?php echo $row_rsForm2['contact_881']; ?> Phone: <?php echo $row_rsForm2['contact_882']; ?> Cell: <?php echo $row_rsForm2['contact_883']; ?> E-Mail: <?php echo $row_rsForm2['contact_884']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_885']; ?> Title: <?php echo $row_rsForm2['contact_886']; ?> Phone: <?php echo $row_rsForm2['contact_887']; ?> Cell: <?php echo $row_rsForm2['contact_888']; ?> E-Mail: <?php echo $row_rsForm2['contact_889']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_890']; ?> Title: <?php echo $row_rsForm2['contact_891']; ?> Phone: <?php echo $row_rsForm2['contact_892']; ?> Cell: <?php echo $row_rsForm2['contact_893']; ?> E-Mail: <?php echo $row_rsForm2['contact_894']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_895']; ?> Title: <?php echo $row_rsForm2['contact_896']; ?> Phone: <?php echo $row_rsForm2['contact_897']; ?> Cell: <?php echo $row_rsForm2['contact_898']; ?> E-Mail: <?php echo $row_rsForm2['contact_899']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_900']; ?> Title: <?php echo $row_rsForm2['contact_901']; ?> Phone: <?php echo $row_rsForm2['contact_902']; ?> Cell: <?php echo $row_rsForm2['contact_903']; ?> E-Mail: <?php echo $row_rsForm2['contact_904']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_905']; ?> Title: <?php echo $row_rsForm2['contact_906']; ?> Phone: <?php echo $row_rsForm2['contact_907']; ?> Cell: <?php echo $row_rsForm2['contact_908']; ?> E-Mail: <?php echo $row_rsForm2['contact_909']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_910']; ?> Title: <?php echo $row_rsForm2['contact_911']; ?> Phone: <?php echo $row_rsForm2['contact_912']; ?> Cell: <?php echo $row_rsForm2['contact_913']; ?> E-Mail: <?php echo $row_rsForm2['contact_914']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_915']; ?> Title: <?php echo $row_rsForm2['contact_916']; ?> Phone: <?php echo $row_rsForm2['contact_917']; ?> Cell: <?php echo $row_rsForm2['contact_918']; ?> E-Mail: <?php echo $row_rsForm2['contact_919']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_920']; ?> Title: <?php echo $row_rsForm2['contact_921']; ?> Phone: <?php echo $row_rsForm2['contact_922']; ?> Cell: <?php echo $row_rsForm2['contact_923']; ?> E-Mail: <?php echo $row_rsForm2['contact_924']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_925']; ?> Title: <?php echo $row_rsForm2['contact_926']; ?> Phone: <?php echo $row_rsForm2['contact_927']; ?> Cell: <?php echo $row_rsForm2['contact_928']; ?> E-Mail: <?php echo $row_rsForm2['contact_929']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_930']; ?> Title: <?php echo $row_rsForm2['contact_931']; ?> Phone: <?php echo $row_rsForm2['contact_932']; ?> Cell: <?php echo $row_rsForm2['contact_933']; ?> E-Mail: <?php echo $row_rsForm2['contact_934']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_935']; ?> Title: <?php echo $row_rsForm2['contact_936']; ?> Phone: <?php echo $row_rsForm2['contact_937']; ?> Cell: <?php echo $row_rsForm2['contact_938']; ?> E-Mail: <?php echo $row_rsForm2['contact_939']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_940']; ?> Title: <?php echo $row_rsForm2['contact_941']; ?> Phone: <?php echo $row_rsForm2['contact_942']; ?> Cell: <?php echo $row_rsForm2['contact_943']; ?> E-Mail: <?php echo $row_rsForm2['contact_944']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_945']; ?> Title: <?php echo $row_rsForm2['contact_946']; ?> Phone: <?php echo $row_rsForm2['contact_947']; ?> Cell: <?php echo $row_rsForm2['contact_948']; ?> E-Mail: <?php echo $row_rsForm2['contact_949']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_950']; ?> Title: <?php echo $row_rsForm2['contact_951']; ?> Phone: <?php echo $row_rsForm2['contact_952']; ?> Cell: <?php echo $row_rsForm2['contact_953']; ?> E-Mail: <?php echo $row_rsForm2['contact_954']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_955']; ?> Title: <?php echo $row_rsForm2['contact_956']; ?> Phone: <?php echo $row_rsForm2['contact_957']; ?> Cell: <?php echo $row_rsForm2['contact_958']; ?> E-Mail: <?php echo $row_rsForm2['contact_959']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_960']; ?> Title: <?php echo $row_rsForm2['contact_961']; ?> Phone: <?php echo $row_rsForm2['contact_962']; ?> Cell: <?php echo $row_rsForm2['contact_963']; ?> E-Mail: <?php echo $row_rsForm2['contact_964']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_965']; ?> Title: <?php echo $row_rsForm2['contact_966']; ?> Phone: <?php echo $row_rsForm2['contact_967']; ?> Cell: <?php echo $row_rsForm2['contact_968']; ?> E-Mail: <?php echo $row_rsForm2['contact_969']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_970']; ?> Title: <?php echo $row_rsForm2['contact_971']; ?> Phone: <?php echo $row_rsForm2['contact_972']; ?> Cell: <?php echo $row_rsForm2['contact_973']; ?> E-Mail: <?php echo $row_rsForm2['contact_974']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_975']; ?> Title: <?php echo $row_rsForm2['contact_976']; ?> Phone: <?php echo $row_rsForm2['contact_977']; ?> Cell: <?php echo $row_rsForm2['contact_978']; ?> E-Mail: <?php echo $row_rsForm2['contact_979']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_980']; ?> Title: <?php echo $row_rsForm2['contact_981']; ?> Phone: <?php echo $row_rsForm2['contact_982']; ?> Cell: <?php echo $row_rsForm2['contact_983']; ?> E-Mail: <?php echo $row_rsForm2['contact_984']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_985']; ?> Title: <?php echo $row_rsForm2['contact_986']; ?> Phone: <?php echo $row_rsForm2['contact_987']; ?> Cell: <?php echo $row_rsForm2['contact_988']; ?> E-Mail: <?php echo $row_rsForm2['contact_989']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_990']; ?> Title: <?php echo $row_rsForm2['contact_991']; ?> Phone: <?php echo $row_rsForm2['contact_992']; ?> Cell: <?php echo $row_rsForm2['contact_993']; ?> E-Mail: <?php echo $row_rsForm2['contact_994']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_995']; ?> Title: <?php echo $row_rsForm2['contact_996']; ?> Phone: <?php echo $row_rsForm2['contact_997']; ?> Cell: <?php echo $row_rsForm2['contact_998']; ?> E-Mail: <?php echo $row_rsForm2['contact_999']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_1000']; ?> Title: <?php echo $row_rsForm2['contact_1001']; ?> Phone: <?php echo $row_rsForm2['contact_1002']; ?> Cell: <?php echo $row_rsForm2['contact_1003']; ?> E-Mail: <?php echo $row_rsForm2['contact_1004']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_1005']; ?> Title: <?php echo $row_rsForm2['contact_1006']; ?> Phone: <?php echo $row_rsForm2['contact_1007']; ?> Cell: <?php echo $row_rsForm2['contact_1008']; ?> E-Mail: <?php echo $row_rsForm2['contact_1009']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_1010']; ?> Title: <?php echo $row_rsForm2['contact_1011']; ?> Phone: <?php echo $row_rsForm2['contact_1012']; ?> Cell: <?php echo $row_rsForm2['contact_1013']; ?> E-Mail: <?php echo $row_rsForm2['contact_1014']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_1015']; ?> Title: <?php echo $row_rsForm2['contact_1016']; ?> Phone: <?php echo $row_rsForm2['contact_1017']; ?> Cell: <?php echo $row_rsForm2['contact_1018']; ?> E-Mail: <?php echo $row_rsForm2['contact_1019']; ?></label></li>
								<li><label>Employee Name: <?php echo $row_rsForm2['contact_1020']; ?> Title: <?php echo $row_rsForm2['contact_1021']; ?> Phone: <?php echo $row_rsForm2['contact_1022']; ?> Cell: <?php echo $row_rsForm2['contact_1023']; ?> E-Mail: <?php echo $row_rsForm2['contact_1024']; ?></label></li>
					  <?php }//end of if ?>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 12: Letter To Your Employees</label>
						<br /><br />
						<label>Text:
						<br /><br />
						Please create a temporary letter that will provide any and all employees with instructions should a disaster or disruption occur.
						<br /><br />
						<?php echo $row_rsForm['letterEMP_01']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 13: Please Identify any and all suppliers of Products & Services that you currently use:</label>
						<ol class="olForms">
							<li><label>Supplier Name: <?php echo $row_rsForm['contact_241']; ?> What?: <?php echo $row_rsForm['contact_242']; ?> Phone: <?php echo $row_rsForm['contact_243']; ?> Cell: <?php echo $row_rsForm['contact_244']; ?> E-Mail: <?php echo $row_rsForm['contact_245']; ?></label></li>
							<li><label>Supplier Name: <?php echo $row_rsForm['contact_246']; ?>  What?: <?php echo $row_rsForm['contact_247']; ?> Phone: <?php echo $row_rsForm['contact_248']; ?> Cell: <?php echo $row_rsForm['contact_249']; ?> E-Mail: <?php echo $row_rsForm['contact_250']; ?></label></li>
							<li><label>Supplier Name: <?php echo $row_rsForm['contact_251']; ?>  What?: <?php echo $row_rsForm['contact_252']; ?> Phone: <?php echo $row_rsForm['contact_253']; ?> Cell: <?php echo $row_rsForm['contact_254']; ?> E-Mail: <?php echo $row_rsForm['contact_255']; ?></label></li>
							<li><label>Supplier Name: <?php echo $row_rsForm['contact_256']; ?>  What?: <?php echo $row_rsForm['contact_257']; ?> Phone: <?php echo $row_rsForm['contact_258']; ?> Cell: <?php echo $row_rsForm['contact_259']; ?> E-Mail: <?php echo $row_rsForm['contact_260']; ?></label></li>
							<li><label>Supplier Name: <?php echo $row_rsForm['contact_261']; ?>  What?: <?php echo $row_rsForm['contact_262']; ?> Phone: <?php echo $row_rsForm['contact_263']; ?> Cell: <?php echo $row_rsForm['contact_264']; ?> E-Mail: <?php echo $row_rsForm['contact_265']; ?></label></li>
							<li><label>Supplier Name: <?php echo $row_rsForm['contact_266']; ?>  What?: <?php echo $row_rsForm['contact_267']; ?> Phone: <?php echo $row_rsForm['contact_268']; ?> Cell: <?php echo $row_rsForm['contact_269']; ?> E-Mail: <?php echo $row_rsForm['contact_270']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 14: Please Identify all local and regional Emergency Response Units:</label>
						<ol class="olForms">
							<li><label>Local Police: <?php echo $row_rsForm['contact_271']; ?>  Where: <?php echo $row_rsForm['contact_272']; ?> Phone: <?php echo $row_rsForm['contact_273']; ?> Cell: <?php echo $row_rsForm['contact_274']; ?> E-Mail: <?php echo $row_rsForm['contact_275']; ?></label></li>
							<li><label>Regional Police: <?php echo $row_rsForm['contact_276']; ?> Where: <?php echo $row_rsForm['contact_277']; ?> Phone: <?php echo $row_rsForm['contact_278']; ?> Cell: <?php echo $row_rsForm['contact_279']; ?> E-Mail: <?php echo $row_rsForm['contact_280']; ?></label></li>
							<li><label>Fire: <?php echo $row_rsForm['contact_281']; ?> Where: <?php echo $row_rsForm['contact_282']; ?> Phone: <?php echo $row_rsForm['contact_283']; ?> Cell: <?php echo $row_rsForm['contact_284']; ?> E-Mail: <?php echo $row_rsForm['contact_285']; ?></label></li>
							<li><label>Hospital: <?php echo $row_rsForm['contact_286']; ?>  Where: <?php echo $row_rsForm['contact_287']; ?> Phone: <?php echo $row_rsForm['contact_288']; ?> Cell: <?php echo $row_rsForm['contact_289']; ?> E-Mail: <?php echo $row_rsForm['contact_290']; ?></label></li>
							<li><label>Environmental: <?php echo $row_rsForm['contact_291']; ?> Where: <?php echo $row_rsForm['contact_292']; ?> Phone: <?php echo $row_rsForm['contact_293']; ?> Cell: <?php echo $row_rsForm['contact_294']; ?> E-Mail: <?php echo $row_rsForm['contact_295']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 15: Please Identify a few local contractors for the Recovery Process:</label>
						<ol class="olForms">
							<li><label>Contractor 1: <?php echo $row_rsForm['contact_296']; ?> Where: <?php echo $row_rsForm['contact_297']; ?> Phone: <?php echo $row_rsForm['contact_298']; ?> Cell: <?php echo $row_rsForm['contact_299']; ?> E-Mail: <?php echo $row_rsForm['contact_300']; ?></label></li>
							<li><label>Contractor 2: <?php echo $row_rsForm['contact_301']; ?> Where: <?php echo $row_rsForm['contact_302']; ?> Phone: <?php echo $row_rsForm['contact_303']; ?> Cell: <?php echo $row_rsForm['contact_304']; ?> E-Mail: <?php echo $row_rsForm['contact_305']; ?></label></li>
							<li><label>Contractor 3: <?php echo $row_rsForm['contact_306']; ?> Where: <?php echo $row_rsForm['contact_307']; ?> Phone: <?php echo $row_rsForm['contact_308']; ?> Cell: <?php echo $row_rsForm['contact_309']; ?> E-Mail: <?php echo $row_rsForm['contact_310']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 16: Please Identify a few External Service Providers:</label>
						<ol class="olForms">
							<li><label>Security Company: <?php echo $row_rsForm['contact_355']; ?> Where: <?php echo $row_rsForm['contact_356']; ?> Phone: <?php echo $row_rsForm['contact_357']; ?> Cell: <?php echo $row_rsForm['contact_358']; ?> E-Mail: <?php echo $row_rsForm['contact_359']; ?></label></li>
							<li><label>Insurance Broker: <?php echo $row_rsForm['contact_360']; ?> Where: <?php echo $row_rsForm['contact_361']; ?> Phone: <?php echo $row_rsForm['contact_362']; ?> Cell: <?php echo $row_rsForm['contact_363']; ?> E-Mail: <?php echo $row_rsForm['contact_364']; ?></label></li>
							<li><label>Insurance Agency: <?php echo $row_rsForm['contact_365']; ?> Where: <?php echo $row_rsForm['contact_366']; ?> Phone: <?php echo $row_rsForm['contact_367']; ?> Cell: <?php echo $row_rsForm['contact_368']; ?> E-Mail: <?php echo $row_rsForm['contact_369']; ?></label></li>
							<li><label>Attorney: <?php echo $row_rsForm['contact_370']; ?> Where: <?php echo $row_rsForm['contact_371']; ?> Phone: <?php echo $row_rsForm['contact_372']; ?> Cell: <?php echo $row_rsForm['contact_373']; ?> E-Mail: <?php echo $row_rsForm['contact_374']; ?></label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 17: Please Identify your landlord or building management contact:</label>
						<ol class="olForms">
							<li><label>Landlord Name: <?php echo $row_rsForm['contact_311']; ?> Phone: <?php echo $row_rsForm['contact_312']; ?> Cell: <?php echo $row_rsForm['contact_313']; ?> E-Mail: <?php echo $row_rsForm['contact_314']; ?> </label></li>
							<li><label>Management: <?php echo $row_rsForm['contact_315']; ?>  Phone: <?php echo $row_rsForm['contact_316']; ?> Cell: <?php echo $row_rsForm['contact_317']; ?> E-Mail: <?php echo $row_rsForm['contact_318']; ?> </label></li>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 18: Please Identify your current Service Provider for the following items:</label>
						<ol class="olForms">
							<li><label>Equipment Services: <?php echo $row_rsForm['contact_319']; ?> Phone: <?php echo $row_rsForm['contact_320']; ?><br />Cell: <?php echo $row_rsForm['contact_321']; ?> E-Mail: <?php echo $row_rsForm['contact_322']; ?></label></li>
							<li><label>Alternate Equipment Service: <?php echo $row_rsForm['contact_323']; ?> Phone: <?php echo $row_rsForm['contact_324']; ?><br />Cell: <?php echo $row_rsForm['contact_325']; ?> E-Mail: <?php echo $row_rsForm['contact_326']; ?></label></li>
							<li><label>Alternate Equipment Service: <?php echo $row_rsForm['contact_327']; ?> Phone: <?php echo $row_rsForm['contact_328']; ?><br />Cell: <?php echo $row_rsForm['contact_329']; ?> E-Mail: <?php echo $row_rsForm['contact_330']; ?></label></li>
						</ol>
						<label class="lblFontBold">Furnishings Services:</label>
						<br />
						<ol class="olForms">
							<li><label>Furnishing Services: <?php echo $row_rsForm['contact_331']; ?> Phone: <?php echo $row_rsForm['contact_332']; ?><br />Cell: <?php echo $row_rsForm['contact_333']; ?> E-Mail: <?php echo $row_rsForm['contact_334']; ?></label></li>
							<li><label>Alternate Furnishing Service: <?php echo $row_rsForm['contact_335']; ?> Phone: <?php echo $row_rsForm['contact_336']; ?><br />Cell: <?php echo $row_rsForm['contact_337']; ?> E-Mail: <?php echo $row_rsForm['contact_338']; ?></label></li>
							<li><label>Alternate Furnishing Service: <?php echo $row_rsForm['contact_339']; ?> Phone: <?php echo $row_rsForm['contact_340']; ?><br />Cell: <?php echo $row_rsForm['contact_341']; ?> E-Mail: <?php echo $row_rsForm['contact_342']; ?></label></li>
						</ol>
						<br /><br />
						<label class="lblFontBold">Unique Services:</label>
						<br />
						<ol class="olForms">
							<li><label>Unique Services: <?php echo $row_rsForm['contact_343']; ?> Phone: <?php echo $row_rsForm['contact_344']; ?><br />Cell: <?php echo $row_rsForm['contact_345']; ?> E-Mail: <?php echo $row_rsForm['contact_346']; ?></label></li>
							<li><label>Alternate Unique Service: <?php echo $row_rsForm['contact_347']; ?> Phone: <?php echo $row_rsForm['contact_348']; ?> <br />Cell: <?php echo $row_rsForm['contact_349']; ?> E-Mail: <?php echo $row_rsForm['contact_350']; ?></label></li>
							<li><label>Alternate Unique Service: <?php echo $row_rsForm['contact_351']; ?> Phone: <?php echo $row_rsForm['contact_352']; ?><br />Cell: <?php echo $row_rsForm['contact_353']; ?> E-Mail: <?php echo $row_rsForm['contact_354']; ?></label></li>
						</ol>
					</div>
				<?php }//end of if ?>
				
				<!-- Information -->
				
				<?php 
				if($intTableIndex == 3)
				{
					//does another selection to get the updated data
					mysql_select_db($database_conContinuty, $conContinuty);
					$rsForm2 = mysql_query("SELECT * FROM c2information2 WHERE C2ID= ".$row_rsForm['C2ID'], $conContinuty) or die("Information2: ".mysql_error());
					$row_rsForm2 = mysql_fetch_assoc($rsForm2);
					$totalRows_rsForm2 = mysql_num_rows($rsForm2);?>
                    
					<label>In todays modern world Technology plays a major role in business operation and development. Without technology most business would be unable to function. Use the following section to define you Information Technology requirements so in the time of a disaster you are able to recovery any and all lost items.</label>
					<br /><br />
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 1
						<br /><br />
						Please List any and all software products used currently by your business:</label>
						<br /><br />
						<label class="lblSubQuestion">Software Program #1 Name: <?php echo $row_rsForm['IT_CI01']; ?></label><br />
						<label>Provider: <?php echo $row_rsForm['IT_1SW01']; ?></label><br />
						<label>How Many Copies do your use in your office: <?php echo $row_rsForm['IT_1SW02']; ?></label><br />
						<label>Serial Number: <?php echo $row_rsForm['IT_1SW03']; ?></label><br />
						<label>License #: <?php echo $row_rsForm['IT_1SW04']; ?></label>
						<br /><br />
						<label>Please Provide a description of what this product is used for:<br /><?php echo $row_rsForm['IT_1SW05']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Software Program #2 Name: <?php echo $row_rsForm['IT_CI02']; ?></label><br />
						<label>Provider: <?php echo $row_rsForm['IT_2SW01']; ?></label><br />
						<label>How Many Copies do your use in your office: <?php echo $row_rsForm['IT_2SW02']; ?></label><br />
						<label>Serial Number: <?php echo $row_rsForm['IT_2SW03']; ?></label><br />
						<label>License #: <?php echo $row_rsForm['IT_2SW04']; ?></label>
						<br /><br />
						<label>Please Provide a description of what this product is used for:<br /><?php echo $row_rsForm['IT_2SW05']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Software Program #3 Name: <?php echo $row_rsForm['IT_CI03']; ?></label><br />
						<label>Provider: <?php echo $row_rsForm['IT_3SW01']; ?></label><br />
						<label>How Many Copies do your use in your office: <?php echo $row_rsForm['IT_3SW02']; ?></label><br />
						<label>Serial Number: <?php echo $row_rsForm['IT_3SW03']; ?></label><br />
						<label>License #: <?php echo $row_rsForm['IT_3SW04']; ?></label>
						<br /><br />
						<label>Please Provide a description of what this product is used for:<br /><?php echo $row_rsForm['IT_3SW05']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Software Program #4 Name: <?php echo $row_rsForm['IT_CI04']; ?></label><br />
						<label>Provider: <?php echo $row_rsForm['IT_4SW01']; ?></label><br />
						<label>How Many Copies do your use in your office: <?php echo $row_rsForm['IT_4SW02']; ?></label><br />
						<label>Serial Number: <?php echo $row_rsForm['IT_4SW03']; ?></label><br />
						<label>License #: <?php echo $row_rsForm['IT_4SW04']; ?></label>
						<br /><br />
						<label>Please Provide a description of what this product is used for:<br /><?php echo $row_rsForm['IT_4SW05']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Software Program #5 Name: <?php echo $row_rsForm['IT_CI05']; ?></label><br />
						<label>Provider: <?php echo $row_rsForm['IT_5SW01']; ?></label><br />
						<label>How Many Copies do your use in your office: <?php echo $row_rsForm['IT_5SW02']; ?></label><br />
						<label>Serial Number: <?php echo $row_rsForm['IT_5SW03']; ?></label><br />
						<label>License #: <?php echo $row_rsForm['IT_5SW04']; ?></label>
						<br /><br />
						<label>Please Provide a description of what this product is used for:<br /><?php echo $row_rsForm['IT_5SW05']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 2
						<br /><br />
						Please List any and all Hardware products used currently by your business:</label>
						<br /><br />
						<label class="lblSubQuestion">Hardware #1 Name: <?php echo $row_rsForm['IT_CI06']; ?></label><br />
						<label>Provider: <?php echo $row_rsForm['IT_1HW01']; ?></label><br />
						<label>How Many Copies do your use in your office: <?php echo $row_rsForm['IT_1HW02']; ?></label><br />
						<label>Serial Number: <?php echo $row_rsForm['IT_1HW03']; ?></label><br />
						<label>License #: <?php echo $row_rsForm['IT_1HW04']; ?></label>
						<br /><br />
						<label>Please Provide a description of what this Hardware is used for: <?php echo $row_rsForm['IT_1HW05']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Hardware #2 Name: <?php echo $row_rsForm['IT_CI07']; ?></label><br />
						<label>Provider: <?php echo $row_rsForm['IT_2HW01']; ?></label><br />
						<label>How Many Copies do your use in your office: <?php echo $row_rsForm['IT_2HW02']; ?></label><br />
						<label>Serial Number: <?php echo $row_rsForm['IT_2HW03']; ?></label><br />
						<label>License #: <?php echo $row_rsForm['IT_2HW04']; ?></label>
						<br /><br />
						<label>Please Provide a description of what this Hardware is used for:<br /><?php echo $row_rsForm['IT_2HW05']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Hardware #3 Name: <?php echo $row_rsForm['IT_CI08']; ?></label><br />
						<label>Provider: <?php echo $row_rsForm['IT_3HW01']; ?></label><br />
						<label>How Many Copies do your use in your office: <?php echo $row_rsForm['IT_3HW02']; ?></label><br />
						<label>Serial Number: <?php echo $row_rsForm['IT_3HW03']; ?></label><br />
						<label>License #: <?php echo $row_rsForm['IT_3HW04']; ?></label>
						<br /><br />
						<label>Please Provide a description of what this Hardware is used for:<br /><?php echo $row_rsForm['IT_3HW05']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Hardware #4 Name: <?php echo $row_rsForm['IT_CI09']; ?></label><br />
						<label>Provider: <?php echo $row_rsForm['IT_4HW01']; ?></label><br />
						<label>How Many Copies do your use in your office: <?php echo $row_rsForm['IT_4HW02']; ?></label><br />
						<label>Serial Number: <?php echo $row_rsForm['IT_4HW03']; ?></label><br />
						<label>License #: <?php echo $row_rsForm['IT_4HW04']; ?></label>
						<br /><br />
						<label>Please Provide a description of what this Hardware is used for:<br /><?php echo $row_rsForm['IT_4HW05']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Hardware #5 Name: <?php echo $row_rsForm['IT_CI10']; ?></label><br />
						<label>Provider: <?php echo $row_rsForm['IT_5HW01']; ?></label><br />
						<label>How Many Copies do your use in your office: <?php echo $row_rsForm['IT_5HW02']; ?></label><br />
						<label>Serial Number: <?php echo $row_rsForm['IT_5HW03']; ?></label><br />
						<label>License #: <?php echo $row_rsForm['IT_5HW04']; ?></label>
						<br /><br />
						<label>Please Provide a description of what this Hardware is used for:<br /><?php echo $row_rsForm['IT_5HW05']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 3
						<br /><br />
						If you business currently stores your information on an internal or external server please identify your server requirements below. Please be as accurate as possible:</label>
						<br /><br />
						<label class="lblSubQuestion">Server #1 Name: <?php echo $row_rsForm['IT_CI11']; ?></label><br />
						<label>Provider: <?php echo $row_rsForm['IT_1SR01']; ?></label><br />
						<label>How Many CPU's use this server: <?php echo $row_rsForm['IT_1SR02']; ?></label><br />
						<label>Model: <?php echo $row_rsForm['IT_1SR03']; ?></label><br />
						<label>Size/Capacity #: <?php echo $row_rsForm['IT_1SR04']; ?></label>
						<br /><br />
						<label>Please Provide a description of the setup and over all operation of this server:<br /><?php echo $row_rsForm['IT_1SR05']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Server #2 Name: <?php echo $row_rsForm['IT_CI12']; ?></label><br />
						<label>Provider: <?php echo $row_rsForm['IT_2SR01']; ?></label><br />
						<label>How Many CPU's use this server: <?php echo $row_rsForm['IT_2SR02']; ?></label><br />
						<label>Model: <?php echo $row_rsForm['IT_2SR03']; ?></label><br />
						<label>Size/Capacity #: <?php echo $row_rsForm['IT_2SR04']; ?></label>
						<br /><br />
						<label>Please Provide a description of the setup and over all operation of this server:<br /><?php echo $row_rsForm['IT_2SR05']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 4
						<br /><br />
						If you currently have a back-up system in place please identify the program and equipment you currently use on a day to day basis.</label>
						<br /><br />
						<label class="lblSubQuestion">Tape Name: <?php echo $row_rsForm['IT_CI13']; ?></label><br />
						<label>Name: <?php echo $row_rsForm['IT_1TP01']; ?></label><br />
						<label>How Many Tapes do your require: <?php echo $row_rsForm['IT_1TP02']; ?></label><br />
						<label>Software program Used for Back-up procedures: <?php echo $row_rsForm['IT_1TP03']; ?>
						<br /><br />
						Please provide a brief description of the back-up process:</label><br /> <label><?php echo $row_rsForm['IT_1TP04']; ?></label><br />
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 5 
						<br /><br />
						Information Technology Equipment Inventory Checklist:</label>
						<br /><br />
						<label>Please identify how many pieces of each item you business currently uses?</label>
						<br /><br />
						<label class="lblSubQuestion">Computer Monitors:</label>
						<br /><br />
						<label>Brand 1: <?php echo $row_rsForm['IT_1IN01']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN02']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN03']; ?></label><br /><br />
						<label>Description of specific details: <br /><?php echo $row_rsForm['IT_1IN04']; ?></label>
						<br /><br />
						<label>Brand 2: <?php echo $row_rsForm['IT_1IN05']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN06']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN07']; ?></label><br /><br />
						<label>Description of specific details: <br /><?php echo $row_rsForm['IT_1IN08']; ?></label>
						<br /><br />
						<label>Brand 3: <?php echo $row_rsForm['IT_1IN09']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN10']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN11']; ?></label><br /><br />
						<label>Description of specific details: <br /><?php echo $row_rsForm['IT_1IN12']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Computer CPU Units:</label>
						<br /><br />
						<label>Brand 1: <?php echo $row_rsForm['IT_1IN13']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN14']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN15']; ?></label><br /><br />
						<label>Description of specific details: <br /><?php echo $row_rsForm['IT_1IN16']; ?></label>
						<br /><br />
						<label>Brand 2: <?php echo $row_rsForm['IT_1IN17']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN18']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN19']; ?></label><br /><br />
						<label>Description of specific details: <br /><?php echo $row_rsForm['IT_1IN20']; ?></label>
						<br /><br />
						<label>Brand 3: <?php echo $row_rsForm['IT_1IN21']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN22']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN23']; ?></label><br /><br />
						<label>Description of specific details: <br /><?php echo $row_rsForm['IT_1IN24']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Computer Keyboards:</label>
						<br /><br />
						<label>Brand 1: <?php echo $row_rsForm['IT_1IN25']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN26']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN27']; ?></label>
						<br /><br />
						<label>Brand 2: <?php echo $row_rsForm['IT_1IN28']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN29']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN30']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Computer Mouses:</label>
						<br /><br />
						<label>Brand 1: <?php echo $row_rsForm['IT_1IN31']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN32']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN33']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Computer Printers:</label>
						<br />
						<label>Brand 1: <?php echo $row_rsForm['IT_1IN34']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN35']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN36']; ?></label><br /><br />
						<label>Description of specific details: <br /><?php echo $row_rsForm['IT_1IN37']; ?></label>
						<br />
						<label>Brand 2: <?php echo $row_rsForm['IT_1IN38']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN39']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN40']; ?></label><br /><br />
						<label>Description of specific details: <br /><?php echo $row_rsForm['IT_1IN41']; ?></label>
						<br />
						<label>Brand 3: <?php echo $row_rsForm['IT_1IN42']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN43']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN44']; ?></label><br /><br />
						<label>Description of specific details: <br /><?php echo $row_rsForm['IT_1IN45']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Computer Fax/Scanners:</label>
						<br />
						<label>Brand 1: <?php echo $row_rsForm['IT_1IN46']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN47']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN48']; ?></label><br /><br />
						<label>Description of specific details: <br /><?php echo $row_rsForm['IT_1IN49']; ?></label>
						<br />
						<label>Brand 2: <?php echo $row_rsForm['IT_1IN50']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN51']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN52']; ?></label><br /><br />
						<label>Description of specific details: <br /><?php echo $row_rsForm['IT_1IN53']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Computer Photocopier:</label>
						<br />
						<label>Brand 1: <?php echo $row_rsForm['IT_1IN54']; ?></label><br />
						<label>How Many Do You Require: <?php echo $row_rsForm['IT_1IN55']; ?></label><br />
						<label>Replacement Value $<?php echo $row_rsForm['IT_1IN56']; ?></label><br /><br />
						<label>Description of specific details: <br /><?php echo $row_rsForm['IT_1IN57']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 6</label>
						<br /><br />
						<label>This Section is Designed to give you access to your Computer Programs in the event of a disaster and the employees are not available to give you access to their accounts. This information will allow you to access any essential information for the time of recovery. Make it clear to all employees this information will on be accessed in the event they are unavailable.</label>
						<br /><br />
						<label class="lblSubQuestion">Program #1: <?php echo $row_rsForm['IT_EMPro01']; ?></label>
						<br /><br />
						<ol class="olForms">                 
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP01']; ?> User Name: <?php echo $row_rsForm['IT_USER01']; ?> Password: <?php echo $row_rsForm['IT_PASS01']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP02']; ?> User Name: <?php echo $row_rsForm['IT_USER02']; ?> Password: <?php echo $row_rsForm['IT_PASS02']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP03']; ?> User Name: <?php echo $row_rsForm['IT_USER03']; ?> Password: <?php echo $row_rsForm['IT_PASS03']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP04']; ?> User Name: <?php echo $row_rsForm['IT_USER04']; ?> Password: <?php echo $row_rsForm['IT_PASS04']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP05']; ?> User Name: <?php echo $row_rsForm['IT_USER05']; ?> Password: <?php echo $row_rsForm['IT_PASS05']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP06']; ?> User Name: <?php echo $row_rsForm['IT_USER06']; ?> Password: <?php echo $row_rsForm['IT_PASS06']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP07']; ?> User Name: <?php echo $row_rsForm['IT_USER07']; ?> Password: <?php echo $row_rsForm['IT_PASS07']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP08']; ?> User Name: <?php echo $row_rsForm['IT_USER08']; ?> Password: <?php echo $row_rsForm['IT_PASS08']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP09']; ?> User Name: <?php echo $row_rsForm['IT_USER09']; ?> Password: <?php echo $row_rsForm['IT_PASS09']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP10']; ?> User Name: <?php echo $row_rsForm['IT_USER10']; ?> Password: <?php echo $row_rsForm['IT_PASS10']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP11']; ?> User Name: <?php echo $row_rsForm['IT_USER11']; ?> Password: <?php echo $row_rsForm['IT_PASS11']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP12']; ?> User Name: <?php echo $row_rsForm['IT_USER12']; ?> Password: <?php echo $row_rsForm['IT_PASS12']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP13']; ?> User Name: <?php echo $row_rsForm['IT_USER13']; ?> Password: <?php echo $row_rsForm['IT_PASS13']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP14']; ?> User Name: <?php echo $row_rsForm['IT_USER14']; ?> Password: <?php echo $row_rsForm['IT_PASS14']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP15']; ?> User Name: <?php echo $row_rsForm['IT_USER15']; ?> Password: <?php echo $row_rsForm['IT_PASS15']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP16']; ?> User Name: <?php echo $row_rsForm['IT_USER16']; ?> Password: <?php echo $row_rsForm['IT_PASS16']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP17']; ?> User Name: <?php echo $row_rsForm['IT_USER17']; ?> Password: <?php echo $row_rsForm['IT_PASS17']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP18']; ?> User Name: <?php echo $row_rsForm['IT_USER18']; ?> Password: <?php echo $row_rsForm['IT_PASS18']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP19']; ?> User Name: <?php echo $row_rsForm['IT_USER19']; ?> Password: <?php echo $row_rsForm['IT_PASS19']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_EMP20']; ?> User Name: <?php echo $row_rsForm['IT_USER20']; ?> Password: <?php echo $row_rsForm['IT_PASS20']; ?></label></li>
                            
	<?php	// this will display only for Standard
		if ($row_loginFoundUser['Solution'] == 2)
		{ ?>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_001']; ?> User Name: <?php echo $row_rsForm2['ITStd_002']; ?> Password: <?php echo $row_rsForm2['ITStd_003']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_004']; ?> User Name: <?php echo $row_rsForm2['ITStd_005']; ?> Password: <?php echo $row_rsForm2['ITStd_006']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_007']; ?> User Name: <?php echo $row_rsForm2['ITStd_008']; ?> Password: <?php echo $row_rsForm2['ITStd_009']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_010']; ?> User Name: <?php echo $row_rsForm2['ITStd_011']; ?> Password: <?php echo $row_rsForm2['ITStd_012']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_013']; ?> User Name: <?php echo $row_rsForm2['ITStd_014']; ?> Password: <?php echo $row_rsForm2['ITStd_015']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_016']; ?> User Name: <?php echo $row_rsForm2['ITStd_017']; ?> Password: <?php echo $row_rsForm2['ITStd_018']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_019']; ?> User Name: <?php echo $row_rsForm2['ITStd_020']; ?> Password: <?php echo $row_rsForm2['ITStd_021']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_022']; ?> User Name: <?php echo $row_rsForm2['ITStd_023']; ?> Password: <?php echo $row_rsForm2['ITStd_024']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_025']; ?> User Name: <?php echo $row_rsForm2['ITStd_026']; ?> Password: <?php echo $row_rsForm2['ITStd_027']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_028']; ?> User Name: <?php echo $row_rsForm2['ITStd_029']; ?> Password: <?php echo $row_rsForm2['ITStd_030']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_031']; ?> User Name: <?php echo $row_rsForm2['ITStd_032']; ?> Password: <?php echo $row_rsForm2['ITStd_033']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_034']; ?> User Name: <?php echo $row_rsForm2['ITStd_035']; ?> Password: <?php echo $row_rsForm2['ITStd_036']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_037']; ?> User Name: <?php echo $row_rsForm2['ITStd_038']; ?> Password: <?php echo $row_rsForm2['ITStd_039']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_040']; ?> User Name: <?php echo $row_rsForm2['ITStd_041']; ?> Password: <?php echo $row_rsForm2['ITStd_042']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_043']; ?> User Name: <?php echo $row_rsForm2['ITStd_044']; ?> Password: <?php echo $row_rsForm2['ITStd_045']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_046']; ?> User Name: <?php echo $row_rsForm2['ITStd_047']; ?> Password: <?php echo $row_rsForm2['ITStd_048']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_049']; ?> User Name: <?php echo $row_rsForm2['ITStd_050']; ?> Password: <?php echo $row_rsForm2['ITStd_051']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_052']; ?> User Name: <?php echo $row_rsForm2['ITStd_053']; ?> Password: <?php echo $row_rsForm2['ITStd_054']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_055']; ?> User Name: <?php echo $row_rsForm2['ITStd_056']; ?> Password: <?php echo $row_rsForm2['ITStd_057']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_058']; ?> User Name: <?php echo $row_rsForm2['ITStd_059']; ?> Password: <?php echo $row_rsForm2['ITStd_060']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_061']; ?> User Name: <?php echo $row_rsForm2['ITStd_062']; ?> Password: <?php echo $row_rsForm2['ITStd_063']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_064']; ?> User Name: <?php echo $row_rsForm2['ITStd_065']; ?> Password: <?php echo $row_rsForm2['ITStd_066']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_067']; ?> User Name: <?php echo $row_rsForm2['ITStd_068']; ?> Password: <?php echo $row_rsForm2['ITStd_069']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_070']; ?> User Name: <?php echo $row_rsForm2['ITStd_071']; ?> Password: <?php echo $row_rsForm2['ITStd_072']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_073']; ?> User Name: <?php echo $row_rsForm2['ITStd_074']; ?> Password: <?php echo $row_rsForm2['ITStd_075']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_076']; ?> User Name: <?php echo $row_rsForm2['ITStd_077']; ?> Password: <?php echo $row_rsForm2['ITStd_078']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_079']; ?> User Name: <?php echo $row_rsForm2['ITStd_080']; ?> Password: <?php echo $row_rsForm2['ITStd_081']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_082']; ?> User Name: <?php echo $row_rsForm2['ITStd_083']; ?> Password: <?php echo $row_rsForm2['ITStd_084']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_085']; ?> User Name: <?php echo $row_rsForm2['ITStd_086']; ?> Password: <?php echo $row_rsForm2['ITStd_087']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_088']; ?> User Name: <?php echo $row_rsForm2['ITStd_089']; ?> Password: <?php echo $row_rsForm2['ITStd_090']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_091']; ?> User Name: <?php echo $row_rsForm2['ITStd_092']; ?> Password: <?php echo $row_rsForm2['ITStd_093']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_094']; ?> User Name: <?php echo $row_rsForm2['ITStd_095']; ?> Password: <?php echo $row_rsForm2['ITStd_096']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_097']; ?> User Name: <?php echo $row_rsForm2['ITStd_098']; ?> Password: <?php echo $row_rsForm2['ITStd_099']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_100']; ?> User Name: <?php echo $row_rsForm2['ITStd_101']; ?> Password: <?php echo $row_rsForm2['ITStd_102']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_103']; ?> User Name: <?php echo $row_rsForm2['ITStd_104']; ?> Password: <?php echo $row_rsForm2['ITStd_105']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_106']; ?> User Name: <?php echo $row_rsForm2['ITStd_107']; ?> Password: <?php echo $row_rsForm2['ITStd_108']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_109']; ?> User Name: <?php echo $row_rsForm2['ITStd_110']; ?> Password: <?php echo $row_rsForm2['ITStd_111']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_112']; ?> User Name: <?php echo $row_rsForm2['ITStd_113']; ?> Password: <?php echo $row_rsForm2['ITStd_114']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_115']; ?> User Name: <?php echo $row_rsForm2['ITStd_116']; ?> Password: <?php echo $row_rsForm2['ITStd_117']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_118']; ?> User Name: <?php echo $row_rsForm2['ITStd_119']; ?> Password: <?php echo $row_rsForm2['ITStd_120']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_121']; ?> User Name: <?php echo $row_rsForm2['ITStd_122']; ?> Password: <?php echo $row_rsForm2['ITStd_123']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_124']; ?> User Name: <?php echo $row_rsForm2['ITStd_125']; ?> Password: <?php echo $row_rsForm2['ITStd_126']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_127']; ?> User Name: <?php echo $row_rsForm2['ITStd_128']; ?> Password: <?php echo $row_rsForm2['ITStd_129']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_130']; ?> User Name: <?php echo $row_rsForm2['ITStd_131']; ?> Password: <?php echo $row_rsForm2['ITStd_132']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_133']; ?> User Name: <?php echo $row_rsForm2['ITStd_134']; ?> Password: <?php echo $row_rsForm2['ITStd_135']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_136']; ?> User Name: <?php echo $row_rsForm2['ITStd_137']; ?> Password: <?php echo $row_rsForm2['ITStd_138']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_139']; ?> User Name: <?php echo $row_rsForm2['ITStd_140']; ?> Password: <?php echo $row_rsForm2['ITStd_141']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_142']; ?> User Name: <?php echo $row_rsForm2['ITStd_143']; ?> Password: <?php echo $row_rsForm2['ITStd_144']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_145']; ?> User Name: <?php echo $row_rsForm2['ITStd_146']; ?> Password: <?php echo $row_rsForm2['ITStd_147']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_148']; ?> User Name: <?php echo $row_rsForm2['ITStd_149']; ?> Password: <?php echo $row_rsForm2['ITStd_150']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_151']; ?> User Name: <?php echo $row_rsForm2['ITStd_152']; ?> Password: <?php echo $row_rsForm2['ITStd_153']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_154']; ?> User Name: <?php echo $row_rsForm2['ITStd_155']; ?> Password: <?php echo $row_rsForm2['ITStd_156']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_157']; ?> User Name: <?php echo $row_rsForm2['ITStd_158']; ?> Password: <?php echo $row_rsForm2['ITStd_159']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_160']; ?> User Name: <?php echo $row_rsForm2['ITStd_161']; ?> Password: <?php echo $row_rsForm2['ITStd_162']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_163']; ?> User Name: <?php echo $row_rsForm2['ITStd_164']; ?> Password: <?php echo $row_rsForm2['ITStd_165']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_166']; ?> User Name: <?php echo $row_rsForm2['ITStd_167']; ?> Password: <?php echo $row_rsForm2['ITStd_168']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_169']; ?> User Name: <?php echo $row_rsForm2['ITStd_170']; ?> Password: <?php echo $row_rsForm2['ITStd_171']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_172']; ?> User Name: <?php echo $row_rsForm2['ITStd_173']; ?> Password: <?php echo $row_rsForm2['ITStd_174']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_175']; ?> User Name: <?php echo $row_rsForm2['ITStd_176']; ?> Password: <?php echo $row_rsForm2['ITStd_177']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_178']; ?> User Name: <?php echo $row_rsForm2['ITStd_179']; ?> Password: <?php echo $row_rsForm2['ITStd_180']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_181']; ?> User Name: <?php echo $row_rsForm2['ITStd_182']; ?> Password: <?php echo $row_rsForm2['ITStd_183']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_184']; ?> User Name: <?php echo $row_rsForm2['ITStd_185']; ?> Password: <?php echo $row_rsForm2['ITStd_186']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_187']; ?> User Name: <?php echo $row_rsForm2['ITStd_188']; ?> Password: <?php echo $row_rsForm2['ITStd_189']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_190']; ?> User Name: <?php echo $row_rsForm2['ITStd_191']; ?> Password: <?php echo $row_rsForm2['ITStd_192']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_193']; ?> User Name: <?php echo $row_rsForm2['ITStd_194']; ?> Password: <?php echo $row_rsForm2['ITStd_195']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_196']; ?> User Name: <?php echo $row_rsForm2['ITStd_197']; ?> Password: <?php echo $row_rsForm2['ITStd_198']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_199']; ?> User Name: <?php echo $row_rsForm2['ITStd_200']; ?> Password: <?php echo $row_rsForm2['ITStd_201']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_202']; ?> User Name: <?php echo $row_rsForm2['ITStd_203']; ?> Password: <?php echo $row_rsForm2['ITStd_204']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_205']; ?> User Name: <?php echo $row_rsForm2['ITStd_206']; ?> Password: <?php echo $row_rsForm2['ITStd_207']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_208']; ?> User Name: <?php echo $row_rsForm2['ITStd_209']; ?> Password: <?php echo $row_rsForm2['ITStd_210']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_211']; ?> User Name: <?php echo $row_rsForm2['ITStd_212']; ?> Password: <?php echo $row_rsForm2['ITStd_213']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_214']; ?> User Name: <?php echo $row_rsForm2['ITStd_215']; ?> Password: <?php echo $row_rsForm2['ITStd_216']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_217']; ?> User Name: <?php echo $row_rsForm2['ITStd_218']; ?> Password: <?php echo $row_rsForm2['ITStd_219']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_220']; ?> User Name: <?php echo $row_rsForm2['ITStd_221']; ?> Password: <?php echo $row_rsForm2['ITStd_222']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_223']; ?> User Name: <?php echo $row_rsForm2['ITStd_224']; ?> Password: <?php echo $row_rsForm2['ITStd_225']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_226']; ?> User Name: <?php echo $row_rsForm2['ITStd_227']; ?> Password: <?php echo $row_rsForm2['ITStd_228']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_229']; ?> User Name: <?php echo $row_rsForm2['ITStd_230']; ?> Password: <?php echo $row_rsForm2['ITStd_231']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_232']; ?> User Name: <?php echo $row_rsForm2['ITStd_233']; ?> Password: <?php echo $row_rsForm2['ITStd_234']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_235']; ?> User Name: <?php echo $row_rsForm2['ITStd_236']; ?> Password: <?php echo $row_rsForm2['ITStd_237']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_238']; ?> User Name: <?php echo $row_rsForm2['ITStd_239']; ?> Password: <?php echo $row_rsForm2['ITStd_240']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_241']; ?> User Name: <?php echo $row_rsForm2['ITStd_242']; ?> Password: <?php echo $row_rsForm2['ITStd_243']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_244']; ?> User Name: <?php echo $row_rsForm2['ITStd_245']; ?> Password: <?php echo $row_rsForm2['ITStd_246']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_247']; ?> User Name: <?php echo $row_rsForm2['ITStd_248']; ?> Password: <?php echo $row_rsForm2['ITStd_249']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_250']; ?> User Name: <?php echo $row_rsForm2['ITStd_251']; ?> Password: <?php echo $row_rsForm2['ITStd_252']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_253']; ?> User Name: <?php echo $row_rsForm2['ITStd_254']; ?> Password: <?php echo $row_rsForm2['ITStd_255']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_256']; ?> User Name: <?php echo $row_rsForm2['ITStd_257']; ?> Password: <?php echo $row_rsForm2['ITStd_258']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_259']; ?> User Name: <?php echo $row_rsForm2['ITStd_260']; ?> Password: <?php echo $row_rsForm2['ITStd_261']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_262']; ?> User Name: <?php echo $row_rsForm2['ITStd_263']; ?> Password: <?php echo $row_rsForm2['ITStd_264']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_265']; ?> User Name: <?php echo $row_rsForm2['ITStd_266']; ?> Password: <?php echo $row_rsForm2['ITStd_267']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_268']; ?> User Name: <?php echo $row_rsForm2['ITStd_269']; ?> Password: <?php echo $row_rsForm2['ITStd_270']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_271']; ?> User Name: <?php echo $row_rsForm2['ITStd_272']; ?> Password: <?php echo $row_rsForm2['ITStd_273']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_274']; ?> User Name: <?php echo $row_rsForm2['ITStd_275']; ?> Password: <?php echo $row_rsForm2['ITStd_276']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_277']; ?> User Name: <?php echo $row_rsForm2['ITStd_278']; ?> Password: <?php echo $row_rsForm2['ITStd_279']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_280']; ?> User Name: <?php echo $row_rsForm2['ITStd_281']; ?> Password: <?php echo $row_rsForm2['ITStd_282']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_283']; ?> User Name: <?php echo $row_rsForm2['ITStd_284']; ?> Password: <?php echo $row_rsForm2['ITStd_285']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_286']; ?> User Name: <?php echo $row_rsForm2['ITStd_287']; ?> Password: <?php echo $row_rsForm2['ITStd_288']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_289']; ?> User Name: <?php echo $row_rsForm2['ITStd_290']; ?> Password: <?php echo $row_rsForm2['ITStd_291']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_292']; ?> User Name: <?php echo $row_rsForm2['ITStd_293']; ?> Password: <?php echo $row_rsForm2['ITStd_294']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_295']; ?> User Name: <?php echo $row_rsForm2['ITStd_296']; ?> Password: <?php echo $row_rsForm2['ITStd_297']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_298']; ?> User Name: <?php echo $row_rsForm2['ITStd_299']; ?> Password: <?php echo $row_rsForm2['ITStd_300']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_301']; ?> User Name: <?php echo $row_rsForm2['ITStd_302']; ?> Password: <?php echo $row_rsForm2['ITStd_303']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_304']; ?> User Name: <?php echo $row_rsForm2['ITStd_305']; ?> Password: <?php echo $row_rsForm2['ITStd_306']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_307']; ?> User Name: <?php echo $row_rsForm2['ITStd_308']; ?> Password: <?php echo $row_rsForm2['ITStd_309']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_310']; ?> User Name: <?php echo $row_rsForm2['ITStd_311']; ?> Password: <?php echo $row_rsForm2['ITStd_312']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_313']; ?> User Name: <?php echo $row_rsForm2['ITStd_314']; ?> Password: <?php echo $row_rsForm2['ITStd_315']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_316']; ?> User Name: <?php echo $row_rsForm2['ITStd_317']; ?> Password: <?php echo $row_rsForm2['ITStd_318']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_319']; ?> User Name: <?php echo $row_rsForm2['ITStd_320']; ?> Password: <?php echo $row_rsForm2['ITStd_321']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_322']; ?> User Name: <?php echo $row_rsForm2['ITStd_323']; ?> Password: <?php echo $row_rsForm2['ITStd_324']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_325']; ?> User Name: <?php echo $row_rsForm2['ITStd_326']; ?> Password: <?php echo $row_rsForm2['ITStd_327']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_328']; ?> User Name: <?php echo $row_rsForm2['ITStd_329']; ?> Password: <?php echo $row_rsForm2['ITStd_330']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_331']; ?> User Name: <?php echo $row_rsForm2['ITStd_332']; ?> Password: <?php echo $row_rsForm2['ITStd_333']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_334']; ?> User Name: <?php echo $row_rsForm2['ITStd_335']; ?> Password: <?php echo $row_rsForm2['ITStd_336']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_337']; ?> User Name: <?php echo $row_rsForm2['ITStd_338']; ?> Password: <?php echo $row_rsForm2['ITStd_339']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_340']; ?> User Name: <?php echo $row_rsForm2['ITStd_341']; ?> Password: <?php echo $row_rsForm2['ITStd_342']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_343']; ?> User Name: <?php echo $row_rsForm2['ITStd_344']; ?> Password: <?php echo $row_rsForm2['ITStd_345']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_346']; ?> User Name: <?php echo $row_rsForm2['ITStd_347']; ?> Password: <?php echo $row_rsForm2['ITStd_348']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_349']; ?> User Name: <?php echo $row_rsForm2['ITStd_350']; ?> Password: <?php echo $row_rsForm2['ITStd_351']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_352']; ?> User Name: <?php echo $row_rsForm2['ITStd_353']; ?> Password: <?php echo $row_rsForm2['ITStd_354']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_355']; ?> User Name: <?php echo $row_rsForm2['ITStd_356']; ?> Password: <?php echo $row_rsForm2['ITStd_357']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_358']; ?> User Name: <?php echo $row_rsForm2['ITStd_359']; ?> Password: <?php echo $row_rsForm2['ITStd_360']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_361']; ?> User Name: <?php echo $row_rsForm2['ITStd_362']; ?> Password: <?php echo $row_rsForm2['ITStd_363']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_364']; ?> User Name: <?php echo $row_rsForm2['ITStd_365']; ?> Password: <?php echo $row_rsForm2['ITStd_366']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_367']; ?> User Name: <?php echo $row_rsForm2['ITStd_368']; ?> Password: <?php echo $row_rsForm2['ITStd_369']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_370']; ?> User Name: <?php echo $row_rsForm2['ITStd_371']; ?> Password: <?php echo $row_rsForm2['ITStd_372']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_373']; ?> User Name: <?php echo $row_rsForm2['ITStd_374']; ?> Password: <?php echo $row_rsForm2['ITStd_375']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_376']; ?> User Name: <?php echo $row_rsForm2['ITStd_377']; ?> Password: <?php echo $row_rsForm2['ITStd_378']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_379']; ?> User Name: <?php echo $row_rsForm2['ITStd_380']; ?> Password: <?php echo $row_rsForm2['ITStd_381']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_382']; ?> User Name: <?php echo $row_rsForm2['ITStd_383']; ?> Password: <?php echo $row_rsForm2['ITStd_384']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_385']; ?> User Name: <?php echo $row_rsForm2['ITStd_386']; ?> Password: <?php echo $row_rsForm2['ITStd_387']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_388']; ?> User Name: <?php echo $row_rsForm2['ITStd_389']; ?> Password: <?php echo $row_rsForm2['ITStd_390']; ?></label></li>
		<?php }//end of if ?>
						</ol>
						<br /><br />
						<label class="lblSubQuestion">Program #2: <?php echo $row_rsForm['IT_EMPro02']; ?></label>
						<br /><br />
						<ol class="olForms">
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP01']; ?> User Name: <?php echo $row_rsForm['IT_2USER01']; ?> Password: <?php echo $row_rsForm['IT_2PASS01']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP02']; ?> User Name: <?php echo $row_rsForm['IT_2USER02']; ?> Password: <?php echo $row_rsForm['IT_2PASS02']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP03']; ?> User Name: <?php echo $row_rsForm['IT_2USER03']; ?> Password: <?php echo $row_rsForm['IT_2PASS03']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP04']; ?> User Name: <?php echo $row_rsForm['IT_2USER04']; ?> Password: <?php echo $row_rsForm['IT_2PASS04']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP05']; ?> User Name: <?php echo $row_rsForm['IT_2USER05']; ?> Password: <?php echo $row_rsForm['IT_2PASS05']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP06']; ?> User Name: <?php echo $row_rsForm['IT_2USER06']; ?> Password: <?php echo $row_rsForm['IT_2PASS06']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP07']; ?> User Name: <?php echo $row_rsForm['IT_2USER07']; ?> Password: <?php echo $row_rsForm['IT_2PASS07']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP08']; ?> User Name: <?php echo $row_rsForm['IT_2USER08']; ?> Password: <?php echo $row_rsForm['IT_2PASS08']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP09']; ?> User Name: <?php echo $row_rsForm['IT_2USER09']; ?> Password: <?php echo $row_rsForm['IT_2PASS09']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP10']; ?> User Name: <?php echo $row_rsForm['IT_2USER10']; ?> Password: <?php echo $row_rsForm['IT_2PASS10']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP11']; ?> User Name: <?php echo $row_rsForm['IT_2USER11']; ?> Password: <?php echo $row_rsForm['IT_2PASS11']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP12']; ?> User Name: <?php echo $row_rsForm['IT_2USER12']; ?> Password: <?php echo $row_rsForm['IT_2PASS12']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP13']; ?> User Name: <?php echo $row_rsForm['IT_2USER13']; ?> Password: <?php echo $row_rsForm['IT_2PASS13']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP14']; ?> User Name: <?php echo $row_rsForm['IT_2USER14']; ?> Password: <?php echo $row_rsForm['IT_2PASS14']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP15']; ?> User Name: <?php echo $row_rsForm['IT_2USER15']; ?> Password: <?php echo $row_rsForm['IT_2PASS15']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP16']; ?> User Name: <?php echo $row_rsForm['IT_2USER16']; ?> Password: <?php echo $row_rsForm['IT_2PASS16']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP17']; ?> User Name: <?php echo $row_rsForm['IT_2USER17']; ?> Password: <?php echo $row_rsForm['IT_2PASS17']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP18']; ?> User Name: <?php echo $row_rsForm['IT_2USER18']; ?> Password: <?php echo $row_rsForm['IT_2PASS18']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP19']; ?> User Name: <?php echo $row_rsForm['IT_2USER19']; ?> Password: <?php echo $row_rsForm['IT_2PASS19']; ?></label></li>
							<li><label>Employee Name: <?php echo $row_rsForm['IT_2EMP20']; ?> Name: <?php echo $row_rsForm['IT_2USER20']; ?> Password: <?php echo $row_rsForm['IT_2PASS20']; ?></label></li>
                            
		<?php // this will display only for Standard
		if ($row_loginFoundUser['Solution'] == 2)
		{ ?>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_391']; ?> User Name: <?php echo $row_rsForm2['ITStd_392']; ?> Password: <?php echo $row_rsForm2['ITStd_393']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_394']; ?> User Name: <?php echo $row_rsForm2['ITStd_395']; ?> Password: <?php echo $row_rsForm2['ITStd_396']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_397']; ?> User Name: <?php echo $row_rsForm2['ITStd_398']; ?> Password: <?php echo $row_rsForm2['ITStd_399']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_400']; ?> User Name: <?php echo $row_rsForm2['ITStd_401']; ?> Password: <?php echo $row_rsForm2['ITStd_402']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_403']; ?> User Name: <?php echo $row_rsForm2['ITStd_404']; ?> Password: <?php echo $row_rsForm2['ITStd_405']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_406']; ?> User Name: <?php echo $row_rsForm2['ITStd_407']; ?> Password: <?php echo $row_rsForm2['ITStd_408']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_409']; ?> User Name: <?php echo $row_rsForm2['ITStd_410']; ?> Password: <?php echo $row_rsForm2['ITStd_411']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_412']; ?> User Name: <?php echo $row_rsForm2['ITStd_413']; ?> Password: <?php echo $row_rsForm2['ITStd_414']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_415']; ?> User Name: <?php echo $row_rsForm2['ITStd_416']; ?> Password: <?php echo $row_rsForm2['ITStd_417']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_418']; ?> User Name: <?php echo $row_rsForm2['ITStd_419']; ?> Password: <?php echo $row_rsForm2['ITStd_420']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_421']; ?> User Name: <?php echo $row_rsForm2['ITStd_422']; ?> Password: <?php echo $row_rsForm2['ITStd_423']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_424']; ?> User Name: <?php echo $row_rsForm2['ITStd_425']; ?> Password: <?php echo $row_rsForm2['ITStd_426']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_427']; ?> User Name: <?php echo $row_rsForm2['ITStd_428']; ?> Password: <?php echo $row_rsForm2['ITStd_429']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_430']; ?> User Name: <?php echo $row_rsForm2['ITStd_431']; ?> Password: <?php echo $row_rsForm2['ITStd_432']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_433']; ?> User Name: <?php echo $row_rsForm2['ITStd_434']; ?> Password: <?php echo $row_rsForm2['ITStd_435']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_436']; ?> User Name: <?php echo $row_rsForm2['ITStd_437']; ?> Password: <?php echo $row_rsForm2['ITStd_438']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_439']; ?> User Name: <?php echo $row_rsForm2['ITStd_440']; ?> Password: <?php echo $row_rsForm2['ITStd_441']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_442']; ?> User Name: <?php echo $row_rsForm2['ITStd_443']; ?> Password: <?php echo $row_rsForm2['ITStd_444']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_445']; ?> User Name: <?php echo $row_rsForm2['ITStd_446']; ?> Password: <?php echo $row_rsForm2['ITStd_447']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_448']; ?> User Name: <?php echo $row_rsForm2['ITStd_449']; ?> Password: <?php echo $row_rsForm2['ITStd_450']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_451']; ?> User Name: <?php echo $row_rsForm2['ITStd_452']; ?> Password: <?php echo $row_rsForm2['ITStd_453']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_454']; ?> User Name: <?php echo $row_rsForm2['ITStd_455']; ?> Password: <?php echo $row_rsForm2['ITStd_456']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_457']; ?> User Name: <?php echo $row_rsForm2['ITStd_458']; ?> Password: <?php echo $row_rsForm2['ITStd_459']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_460']; ?> User Name: <?php echo $row_rsForm2['ITStd_461']; ?> Password: <?php echo $row_rsForm2['ITStd_462']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_463']; ?> User Name: <?php echo $row_rsForm2['ITStd_464']; ?> Password: <?php echo $row_rsForm2['ITStd_465']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_466']; ?> User Name: <?php echo $row_rsForm2['ITStd_467']; ?> Password: <?php echo $row_rsForm2['ITStd_468']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_469']; ?> User Name: <?php echo $row_rsForm2['ITStd_470']; ?> Password: <?php echo $row_rsForm2['ITStd_471']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_472']; ?> User Name: <?php echo $row_rsForm2['ITStd_473']; ?> Password: <?php echo $row_rsForm2['ITStd_474']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_475']; ?> User Name: <?php echo $row_rsForm2['ITStd_476']; ?> Password: <?php echo $row_rsForm2['ITStd_477']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_478']; ?> User Name: <?php echo $row_rsForm2['ITStd_479']; ?> Password: <?php echo $row_rsForm2['ITStd_480']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_481']; ?> User Name: <?php echo $row_rsForm2['ITStd_482']; ?> Password: <?php echo $row_rsForm2['ITStd_483']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_484']; ?> User Name: <?php echo $row_rsForm2['ITStd_485']; ?> Password: <?php echo $row_rsForm2['ITStd_486']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_487']; ?> User Name: <?php echo $row_rsForm2['ITStd_488']; ?> Password: <?php echo $row_rsForm2['ITStd_489']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_490']; ?> User Name: <?php echo $row_rsForm2['ITStd_491']; ?> Password: <?php echo $row_rsForm2['ITStd_492']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_493']; ?> User Name: <?php echo $row_rsForm2['ITStd_494']; ?> Password: <?php echo $row_rsForm2['ITStd_495']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_496']; ?> User Name: <?php echo $row_rsForm2['ITStd_497']; ?> Password: <?php echo $row_rsForm2['ITStd_498']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_499']; ?> User Name: <?php echo $row_rsForm2['ITStd_500']; ?> Password: <?php echo $row_rsForm2['ITStd_501']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_502']; ?> User Name: <?php echo $row_rsForm2['ITStd_503']; ?> Password: <?php echo $row_rsForm2['ITStd_504']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_505']; ?> User Name: <?php echo $row_rsForm2['ITStd_506']; ?> Password: <?php echo $row_rsForm2['ITStd_507']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_508']; ?> User Name: <?php echo $row_rsForm2['ITStd_509']; ?> Password: <?php echo $row_rsForm2['ITStd_510']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_511']; ?> User Name: <?php echo $row_rsForm2['ITStd_512']; ?> Password: <?php echo $row_rsForm2['ITStd_513']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_514']; ?> User Name: <?php echo $row_rsForm2['ITStd_515']; ?> Password: <?php echo $row_rsForm2['ITStd_516']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_517']; ?> User Name: <?php echo $row_rsForm2['ITStd_518']; ?> Password: <?php echo $row_rsForm2['ITStd_519']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_520']; ?> User Name: <?php echo $row_rsForm2['ITStd_521']; ?> Password: <?php echo $row_rsForm2['ITStd_522']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_523']; ?> User Name: <?php echo $row_rsForm2['ITStd_524']; ?> Password: <?php echo $row_rsForm2['ITStd_525']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_526']; ?> User Name: <?php echo $row_rsForm2['ITStd_527']; ?> Password: <?php echo $row_rsForm2['ITStd_528']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_529']; ?> User Name: <?php echo $row_rsForm2['ITStd_530']; ?> Password: <?php echo $row_rsForm2['ITStd_531']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_532']; ?> User Name: <?php echo $row_rsForm2['ITStd_533']; ?> Password: <?php echo $row_rsForm2['ITStd_534']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_535']; ?> User Name: <?php echo $row_rsForm2['ITStd_536']; ?> Password: <?php echo $row_rsForm2['ITStd_537']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_538']; ?> User Name: <?php echo $row_rsForm2['ITStd_539']; ?> Password: <?php echo $row_rsForm2['ITStd_540']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_541']; ?> User Name: <?php echo $row_rsForm2['ITStd_542']; ?> Password: <?php echo $row_rsForm2['ITStd_543']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_544']; ?> User Name: <?php echo $row_rsForm2['ITStd_545']; ?> Password: <?php echo $row_rsForm2['ITStd_546']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_547']; ?> User Name: <?php echo $row_rsForm2['ITStd_548']; ?> Password: <?php echo $row_rsForm2['ITStd_549']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_550']; ?> User Name: <?php echo $row_rsForm2['ITStd_551']; ?> Password: <?php echo $row_rsForm2['ITStd_552']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_553']; ?> User Name: <?php echo $row_rsForm2['ITStd_554']; ?> Password: <?php echo $row_rsForm2['ITStd_555']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_556']; ?> User Name: <?php echo $row_rsForm2['ITStd_557']; ?> Password: <?php echo $row_rsForm2['ITStd_558']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_559']; ?> User Name: <?php echo $row_rsForm2['ITStd_560']; ?> Password: <?php echo $row_rsForm2['ITStd_561']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_562']; ?> User Name: <?php echo $row_rsForm2['ITStd_563']; ?> Password: <?php echo $row_rsForm2['ITStd_564']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_565']; ?> User Name: <?php echo $row_rsForm2['ITStd_566']; ?> Password: <?php echo $row_rsForm2['ITStd_567']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_568']; ?> User Name: <?php echo $row_rsForm2['ITStd_569']; ?> Password: <?php echo $row_rsForm2['ITStd_570']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_571']; ?> User Name: <?php echo $row_rsForm2['ITStd_572']; ?> Password: <?php echo $row_rsForm2['ITStd_573']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_574']; ?> User Name: <?php echo $row_rsForm2['ITStd_575']; ?> Password: <?php echo $row_rsForm2['ITStd_576']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_577']; ?> User Name: <?php echo $row_rsForm2['ITStd_578']; ?> Password: <?php echo $row_rsForm2['ITStd_579']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_580']; ?> User Name: <?php echo $row_rsForm2['ITStd_581']; ?> Password: <?php echo $row_rsForm2['ITStd_582']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_583']; ?> User Name: <?php echo $row_rsForm2['ITStd_584']; ?> Password: <?php echo $row_rsForm2['ITStd_585']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_586']; ?> User Name: <?php echo $row_rsForm2['ITStd_587']; ?> Password: <?php echo $row_rsForm2['ITStd_588']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_589']; ?> User Name: <?php echo $row_rsForm2['ITStd_590']; ?> Password: <?php echo $row_rsForm2['ITStd_591']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_592']; ?> User Name: <?php echo $row_rsForm2['ITStd_593']; ?> Password: <?php echo $row_rsForm2['ITStd_594']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_595']; ?> User Name: <?php echo $row_rsForm2['ITStd_596']; ?> Password: <?php echo $row_rsForm2['ITStd_597']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_598']; ?> User Name: <?php echo $row_rsForm2['ITStd_599']; ?> Password: <?php echo $row_rsForm2['ITStd_600']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_601']; ?> User Name: <?php echo $row_rsForm2['ITStd_602']; ?> Password: <?php echo $row_rsForm2['ITStd_603']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_604']; ?> User Name: <?php echo $row_rsForm2['ITStd_605']; ?> Password: <?php echo $row_rsForm2['ITStd_606']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_607']; ?> User Name: <?php echo $row_rsForm2['ITStd_608']; ?> Password: <?php echo $row_rsForm2['ITStd_609']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_610']; ?> User Name: <?php echo $row_rsForm2['ITStd_611']; ?> Password: <?php echo $row_rsForm2['ITStd_612']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_613']; ?> User Name: <?php echo $row_rsForm2['ITStd_614']; ?> Password: <?php echo $row_rsForm2['ITStd_615']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_616']; ?> User Name: <?php echo $row_rsForm2['ITStd_617']; ?> Password: <?php echo $row_rsForm2['ITStd_618']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_619']; ?> User Name: <?php echo $row_rsForm2['ITStd_620']; ?> Password: <?php echo $row_rsForm2['ITStd_621']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_622']; ?> User Name: <?php echo $row_rsForm2['ITStd_623']; ?> Password: <?php echo $row_rsForm2['ITStd_624']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_625']; ?> User Name: <?php echo $row_rsForm2['ITStd_626']; ?> Password: <?php echo $row_rsForm2['ITStd_627']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_628']; ?> User Name: <?php echo $row_rsForm2['ITStd_629']; ?> Password: <?php echo $row_rsForm2['ITStd_630']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_631']; ?> User Name: <?php echo $row_rsForm2['ITStd_632']; ?> Password: <?php echo $row_rsForm2['ITStd_633']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_634']; ?> User Name: <?php echo $row_rsForm2['ITStd_635']; ?> Password: <?php echo $row_rsForm2['ITStd_636']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_637']; ?> User Name: <?php echo $row_rsForm2['ITStd_638']; ?> Password: <?php echo $row_rsForm2['ITStd_639']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_640']; ?> User Name: <?php echo $row_rsForm2['ITStd_641']; ?> Password: <?php echo $row_rsForm2['ITStd_642']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_643']; ?> User Name: <?php echo $row_rsForm2['ITStd_644']; ?> Password: <?php echo $row_rsForm2['ITStd_645']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_646']; ?> User Name: <?php echo $row_rsForm2['ITStd_647']; ?> Password: <?php echo $row_rsForm2['ITStd_648']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_649']; ?> User Name: <?php echo $row_rsForm2['ITStd_650']; ?> Password: <?php echo $row_rsForm2['ITStd_651']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_652']; ?> User Name: <?php echo $row_rsForm2['ITStd_653']; ?> Password: <?php echo $row_rsForm2['ITStd_654']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_655']; ?> User Name: <?php echo $row_rsForm2['ITStd_656']; ?> Password: <?php echo $row_rsForm2['ITStd_657']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_658']; ?> User Name: <?php echo $row_rsForm2['ITStd_659']; ?> Password: <?php echo $row_rsForm2['ITStd_660']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_661']; ?> User Name: <?php echo $row_rsForm2['ITStd_662']; ?> Password: <?php echo $row_rsForm2['ITStd_663']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_664']; ?> User Name: <?php echo $row_rsForm2['ITStd_665']; ?> Password: <?php echo $row_rsForm2['ITStd_666']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_667']; ?> User Name: <?php echo $row_rsForm2['ITStd_668']; ?> Password: <?php echo $row_rsForm2['ITStd_669']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_670']; ?> User Name: <?php echo $row_rsForm2['ITStd_671']; ?> Password: <?php echo $row_rsForm2['ITStd_672']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_673']; ?> User Name: <?php echo $row_rsForm2['ITStd_674']; ?> Password: <?php echo $row_rsForm2['ITStd_675']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_676']; ?> User Name: <?php echo $row_rsForm2['ITStd_677']; ?> Password: <?php echo $row_rsForm2['ITStd_678']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_679']; ?> User Name: <?php echo $row_rsForm2['ITStd_680']; ?> Password: <?php echo $row_rsForm2['ITStd_681']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_682']; ?> User Name: <?php echo $row_rsForm2['ITStd_683']; ?> Password: <?php echo $row_rsForm2['ITStd_684']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_685']; ?> User Name: <?php echo $row_rsForm2['ITStd_686']; ?> Password: <?php echo $row_rsForm2['ITStd_687']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_688']; ?> User Name: <?php echo $row_rsForm2['ITStd_689']; ?> Password: <?php echo $row_rsForm2['ITStd_690']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_691']; ?> User Name: <?php echo $row_rsForm2['ITStd_692']; ?> Password: <?php echo $row_rsForm2['ITStd_693']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_694']; ?> User Name: <?php echo $row_rsForm2['ITStd_695']; ?> Password: <?php echo $row_rsForm2['ITStd_696']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_697']; ?> User Name: <?php echo $row_rsForm2['ITStd_698']; ?> Password: <?php echo $row_rsForm2['ITStd_699']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_700']; ?> User Name: <?php echo $row_rsForm2['ITStd_701']; ?> Password: <?php echo $row_rsForm2['ITStd_702']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_703']; ?> User Name: <?php echo $row_rsForm2['ITStd_704']; ?> Password: <?php echo $row_rsForm2['ITStd_705']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_706']; ?> User Name: <?php echo $row_rsForm2['ITStd_707']; ?> Password: <?php echo $row_rsForm2['ITStd_708']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_709']; ?> User Name: <?php echo $row_rsForm2['ITStd_710']; ?> Password: <?php echo $row_rsForm2['ITStd_711']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_712']; ?> User Name: <?php echo $row_rsForm2['ITStd_713']; ?> Password: <?php echo $row_rsForm2['ITStd_714']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_715']; ?> User Name: <?php echo $row_rsForm2['ITStd_716']; ?> Password: <?php echo $row_rsForm2['ITStd_717']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_718']; ?> User Name: <?php echo $row_rsForm2['ITStd_719']; ?> Password: <?php echo $row_rsForm2['ITStd_720']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_721']; ?> User Name: <?php echo $row_rsForm2['ITStd_722']; ?> Password: <?php echo $row_rsForm2['ITStd_723']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_724']; ?> User Name: <?php echo $row_rsForm2['ITStd_725']; ?> Password: <?php echo $row_rsForm2['ITStd_726']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_727']; ?> User Name: <?php echo $row_rsForm2['ITStd_728']; ?> Password: <?php echo $row_rsForm2['ITStd_729']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_730']; ?> User Name: <?php echo $row_rsForm2['ITStd_731']; ?> Password: <?php echo $row_rsForm2['ITStd_732']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_733']; ?> User Name: <?php echo $row_rsForm2['ITStd_734']; ?> Password: <?php echo $row_rsForm2['ITStd_735']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_736']; ?> User Name: <?php echo $row_rsForm2['ITStd_737']; ?> Password: <?php echo $row_rsForm2['ITStd_738']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_739']; ?> User Name: <?php echo $row_rsForm2['ITStd_740']; ?> Password: <?php echo $row_rsForm2['ITStd_741']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_742']; ?> User Name: <?php echo $row_rsForm2['ITStd_743']; ?> Password: <?php echo $row_rsForm2['ITStd_744']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_745']; ?> User Name: <?php echo $row_rsForm2['ITStd_746']; ?> Password: <?php echo $row_rsForm2['ITStd_747']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_748']; ?> User Name: <?php echo $row_rsForm2['ITStd_749']; ?> Password: <?php echo $row_rsForm2['ITStd_750']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_751']; ?> User Name: <?php echo $row_rsForm2['ITStd_752']; ?> Password: <?php echo $row_rsForm2['ITStd_753']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_754']; ?> User Name: <?php echo $row_rsForm2['ITStd_755']; ?> Password: <?php echo $row_rsForm2['ITStd_756']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_757']; ?> User Name: <?php echo $row_rsForm2['ITStd_758']; ?> Password: <?php echo $row_rsForm2['ITStd_759']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_760']; ?> User Name: <?php echo $row_rsForm2['ITStd_761']; ?> Password: <?php echo $row_rsForm2['ITStd_762']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_763']; ?> User Name: <?php echo $row_rsForm2['ITStd_764']; ?> Password: <?php echo $row_rsForm2['ITStd_765']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_766']; ?> User Name: <?php echo $row_rsForm2['ITStd_767']; ?> Password: <?php echo $row_rsForm2['ITStd_768']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_769']; ?> User Name: <?php echo $row_rsForm2['ITStd_770']; ?> Password: <?php echo $row_rsForm2['ITStd_771']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_772']; ?> User Name: <?php echo $row_rsForm2['ITStd_773']; ?> Password: <?php echo $row_rsForm2['ITStd_774']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_775']; ?> User Name: <?php echo $row_rsForm2['ITStd_776']; ?> Password: <?php echo $row_rsForm2['ITStd_777']; ?></label></li>
			<li><label>Employee Name: <?php echo $row_rsForm2['ITStd_778']; ?> User Name: <?php echo $row_rsForm2['ITStd_779']; ?> Password: <?php echo $row_rsForm2['ITStd_780']; ?></label></li>
		<?php }//end of if ?>
						</ol>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 7</label>
						<br /><br />
						<label>IN THE EVENT OF A DISASTER OR A LOSS YOU WILL NEED TO HAVE ACCESS TO YOU ESSENTIAL DATA. PLEASE CREATE A LIST OF THE DATA OR SYSTEMS YOU WILL NEED TO HAVE ACCESS TO IMMEDIATELY, IN ORDER OR PRIORITY. MOST IMPORTANT INFORMATION FIRST.</label>
						<br />
						<label>Data Source #1: <?php echo $row_rsForm['IT_DS01']; ?></label><br />
						<label>Data Source #2: <?php echo $row_rsForm['IT_DS02']; ?></label><br />
						<label>Data Source #3: <?php echo $row_rsForm['IT_DS03']; ?></label><br />
						<label>Data Source #4: <?php echo $row_rsForm['IT_DS04']; ?></label><br />
						<label>Data Source #5: <?php echo $row_rsForm['IT_DS05']; ?></label><br />
						<label>Data Source #6: <?php echo $row_rsForm['IT_DS06']; ?></label><br />
						<label>Data Source #7: <?php echo $row_rsForm['IT_DS07']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 8</label>
						<br /><br />
						<label>IN THE EVENT OF A DISASTER YOU MAY WANT TO USE YOUR WEB-SITE AS A COMMUNICATION METHOD FOR YOUR EMPLOYEES, CUSTOMER AND ALL OTHER PEOPLE INVOLVED IN YOUR BUSINESS.</label>
						<br />
						<label>Who is your current Web-site Provider: <?php echo $row_rsForm['IT_web01']; ?></label><br />
						<label>Contact person: <?php echo $row_rsForm['IT_web02']; ?><br />
						Phone: <?php echo $row_rsForm['IT_web03']; ?></label><br />
						<label>E-mail: <?php echo $row_rsForm['IT_web04']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 9</label>
						<br /><br />
						<label>Create a list of items you would like to use your web-site for during a disaster and find-out if there are any services you can set-up ahead of time:
						<br /><br />
						In the event that you experience any data loss due to a disaster or an unforeseen event, please describe your current back-up procedures STEP-BY-STEP. This will give you a chance to review you process and see any areas for improvement. With the help of our professional we can determine you requirements in terms of IT recovery.
						<br /><br />
		Please provide a summary of the daily or weekly process you perform for your system back-ups. (If you do not currently have a back-up system in place please contact us for more information). please ensure you includes the following:</label>
						<ol>
							<li><label>Load or Restore Operating Systems</label></li>
							<li><label>Restore Libraries</label></li>
							<li><label>Restore Database</label></li>
							<li><label>Verify All Restores</label></li>
						</ol>
						<label><?php echo $row_rsForm['IT_sum01']; ?></label>
						<br /><br />
						<label>Please provide a step by step procedural outline for brining your system up. Please contact your Head of IT, or if you use an external agency please have them provide a basic outline for you:</label>
						<br /><br />
						<label><?php echo $row_rsForm['IT_sum02']; ?></label>
						<br /><br />
						<label>Please provide a step by step procedural outline for restoring your USER FILES. Please contact your Head of IT, or if you use an external agency please have them provide a basic outline for you:</label>
						<br /><br />
						<label><?php echo $row_rsForm['IT_sum03']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 10</label>
						<br /><br />
						<label>If you currently store your information off-site please provide the information about that company so you can access your information if a disaster were to occur. If you do not have an off-site service provider you may want to consider seeking additional information. Contact us to ask us questions about IT recovery.</label>
						<br />
						<label>Company Name: <?php echo $row_rsForm['IT_OFF01']; ?></label><br />
						<label>Address: <?php echo $row_rsForm['IT_OFF02']; ?><br />
						Phone: <?php echo $row_rsForm['IT_OFF03']; ?><br />
						Contact: <?php echo $row_rsForm['IT_OFF04']; ?></label><br />
						<label>E-Mail: <?php echo $row_rsForm['IT_OFF05']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 11</label>
						<br /><br />
						<label>In the event of a disaster and you need to replace any or all of your IT equipment please create a list of potential suppliers for your business.</label>
						<br />
						<label class="lblSubQuestion">Supplier #1 Name: <?php echo $row_rsForm['IT_SUPP01']; ?><br />
						Phone: <?php echo $row_rsForm['IT_SUPP02']; ?></label><br />
						<label>E-Mail: <?php echo $row_rsForm['IT_SUPP03']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Supplier #2 Name: <?php echo $row_rsForm['IT_SUPP04']; ?><br />
						Phone: <?php echo $row_rsForm['IT_SUPP05']; ?></label><br />
						<label>E-Mail: <?php echo $row_rsForm['IT_SUPP06']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Supplier #3 Name: <?php echo $row_rsForm['IT_SUPP07']; ?><br />
						Phone: <?php echo $row_rsForm['IT_SUPP08']; ?></label><br />
						<label>E-Mail: <?php echo $row_rsForm['IT_SUPP09']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- Business Impact -->
				
				<?php 
				if($intTableIndex == 4 && $row_loginFoundUser['Solution'] == 2)
				{?>
					<div align="left">
						<div class="divPageTitle" align="left">
							<label class="lblPageTitle"><?php echo $row_rsPlans['sectionName']; ?></label>
						</div>
					</div>
				
					<label><strong>Your Business Impact Analysis:</strong>
					<br /><br />
					This is the introduction to the idea of providing uninterrupted products and service. You will begin to determine acceptable down-times for each and identify the priority of recovery for these products and services that your business provides.
					<br /><br />
					Every business has or should have an organizational chart that define each key Business Unit that exists within your company.
					A Example of a Business Unit would be the following:
					<br /><br />
					Ex1. Admin<br />
					Ex2. Production<br />
					Ex3. Sales &amp; Marketing<br />
					<br />
					Based on your business units you will need to determine the main functions of these UNITS and what the essential functions are. You will also determine the amount of time you can accept as responsible.</label>
					<br /><br />
					<div class="divQuestionForms">
						<label class="lblQuestion">Business Unit #1</label>
						<br /><br />
						<label class="lblSubQuestion">Question 1: Please Identify an existing Business Unit</label><br />
						<label>Business Unit Name: <?php echo $row_rsForm['BIA01_01']; ?></label><br />
						<label>Purpose of this Business Unit: <?php echo $row_rsForm['BIA01_02']; ?></label><br /><br />
						<label>Identify Any Critical Documents that exist in this Unit: <?php echo $row_rsForm['BIA01_03']; ?></label><br />
						<label>What Month is the busiest for this Business Unit: <?php echo $row_rsForm['BIA01_04']; ?></label><br />
						<label>Number of People Required to keep this Unit Running: <?php echo $row_rsForm['BIA01_05']; ?></label><br />
						<label>What is the maximum time your can operate without this Business Unit: <?php echo $row_rsForm['BIA01_06']; ?></label><br />
						<label>In the event of a disaster how many people do you require to keep this operational: <?php echo $row_rsForm['BIA01_07']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Business Unit #2</label>
						<br /><br />
						<label class="lblSubQuestion">Question 1: Please Identify an existing Business Unit</label><br />
						<label>Business Unit Name: <?php echo $row_rsForm['BIA02_01']; ?></label><br />
						<label>Purpose of this Business Unit: <?php echo $row_rsForm['BIA02_02']; ?></label><br /><br />
						<label>Identify Any Critical Documents that exist in this Unit: <?php echo $row_rsForm['BIA02_03']; ?></label><br />
						<label>What Month is the busiest for this Business Unit: <?php echo $row_rsForm['BIA02_04']; ?></label><br />
						<label>Number of People Required to keep this Unit Running: <?php echo $row_rsForm['BIA02_05']; ?></label><br />
						<label>What is the maximum time your can operate without this Business Unit: <?php echo $row_rsForm['BIA02_06']; ?></label><br />
						<label>In the event of a disaster how many people do you require to keep this operational: <?php echo $row_rsForm['BIA02_07']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Business Unit #3</label>
						<br /><br />
						<label class="lblSubQuestion">Question 1: Please Identify an existing Business Unit</label><br />
						<label>Business Unit Name: <?php echo $row_rsForm['BIA03_01']; ?></label><br />
						<label>Purpose of this Business Unit: <?php echo $row_rsForm['BIA03_02']; ?></label><br /><br />
						<label>Identify Any Critical Documents that exist in this Unit: <?php echo $row_rsForm['BIA03_03']; ?></label><br />
						<label>What Month is the busiest for this Business Unit: <?php echo $row_rsForm['BIA03_04']; ?></label><br />
						<label>Number of People Required to keep this Unit Running: <?php echo $row_rsForm['BIA03_05']; ?></label><br />
						<label>What is the maximum time your can operate without this Business Unit: <?php echo $row_rsForm['BIA03_06']; ?></label><br />
						<label>In the event of a disaster how many people do you require to keep this operational: <?php echo $row_rsForm['BIA03_07']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Business Unit #4</label>
						<br /><br />
						<label class="lblSubQuestion">Question 1: Please Identify an existing Business Unit</label><br />
						<label>Business Unit Name: <?php echo $row_rsForm['BIA04_01']; ?></label><br />
						<label>Purpose of this Business Unit: <?php echo $row_rsForm['BIA04_02']; ?></label><br /><br />
						<label>Identify Any Critical Documents that exist in this Unit: <?php echo $row_rsForm['BIA04_03']; ?></label><br />
						<label>What Month is the busiest for this Business Unit: <?php echo $row_rsForm['BIA04_04']; ?></label><br/>
						<label>Number of People Required to keep this Unit Running: <?php echo $row_rsForm['BIA04_05']; ?></label><br />
						<label>What is the maximum time your can operate without this Business Unit: <?php echo $row_rsForm['BIA04_06']; ?></label><br />
						<label>In the event of a disaster how many people do you require to keep this operational: <?php echo $row_rsForm['BIA04_07']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Business Unit #5</label>
						<br /><br />
						<label class="lblSubQuestion">Question 1: Please Identify an existing Business Unit</label><br />
						<label>Business Unit Name: <?php echo $row_rsForm['BIA05_01']; ?></label><br />
						<label>Purpose of this Business Unit: <?php echo $row_rsForm['BIA05_02']; ?></label><br /><br />
						<label>Identify Any Critical Documents that exist in this Unit: <?php echo $row_rsForm['BIA05_03']; ?></label><br />
						<label>What Month is the busiest for this Business Unit: <?php echo $row_rsForm['BIA05_04']; ?></label><br />
						<label>Number of People Required to keep this Unit Running: <?php echo $row_rsForm['BIA05_05']; ?></label><br />
						<label>What is the maximum time your can operate without this Business Unit: <?php echo $row_rsForm['BIA05_06']; ?></label><br />
						<label>In the event of a disaster how many people do you require to keep this operational: <?php echo $row_rsForm['BIA05_07']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Business Unit #6</label>
						<br /><br />
						<label class="lblSubQuestion">Question 1: Please Identify an existing Business Unit</label><br />
						<label>Business Unit Name: <?php echo $row_rsForm['BIA06_01']; ?></label><br />
						<label>Purpose of this Business Unit: <?php echo $row_rsForm['BIA06_02']; ?></label><br /><br />
						<label>Identify Any Critical Documents that exist in this Unit: <?php echo $row_rsForm['BIA06_03']; ?></label><br />
						<label>What Month is the busiest for this Business Unit: <?php echo $row_rsForm['BIA06_04']; ?></label><br />
						<label>Number of People Required to keep this Unit Running: <?php echo $row_rsForm['BIA06_05']; ?></label><br />
						<label>What is the maximum time your can operate without this Business Unit: <?php echo $row_rsForm['BIA06_06']; ?></label><br />
						<label>In the event of a disaster how many people do you require to keep this operational: <?php echo $row_rsForm['BIA06_07']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Business Unit #7</label>
						<br /><br />
						<label class="lblSubQuestion">Question 1: Please Identify an existing Business Unit</label><br />
						<label>Business Unit Name: <?php echo $row_rsForm['BIA07_01']; ?></label><br />
						<label>Purpose of this Business Unit: <?php echo $row_rsForm['BIA07_02']; ?></label><br /><br />
						<label>Identify Any Critical Documents that exist in this Unit: <?php echo $row_rsForm['BIA07_03']; ?></label><br />
						<label>What Month is the busiest for this Business Unit: <?php echo $row_rsForm['BIA07_04']; ?></label><br />
						<label>Number of People Required to keep this Unit Running: <?php echo $row_rsForm['BIA07_05']; ?></label><br />
						<label>What is the maximum time your can operate without this Business Unit: <?php echo $row_rsForm['BIA07_06']; ?></label><br />
						<label>In the event of a disaster how many people do you require to keep this operational: <?php echo $row_rsForm['BIA07_07']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Business Unit #8</label>
						<br /><br />
						<label class="lblSubQuestion">Question 1: Please Identify an existing Business Unit</label><br />
						<label>Business Unit Name: <?php echo $row_rsForm['BIA08_01']; ?></label><br />
						<label>Purpose of this Business Unit: <?php echo $row_rsForm['BIA08_02']; ?></label><br /><br />
						<label>Identify Any Critical Documents that exist in this Unit: <?php echo $row_rsForm['BIA08_03']; ?></label><br />
						<label>What Month is the busiest for this Business Unit: <?php echo $row_rsForm['BIA08_04']; ?></label><br />
						<label>Number of People Required to keep this Unit Running: <?php echo $row_rsForm['BIA08_05']; ?></label><br />
						<label>What is the maximum time your can operate without this Business Unit: <?php echo $row_rsForm['BIA08_06']; ?></label><br />
						<label>In the event of a disaster how many people do you require to keep this operational: <?php echo $row_rsForm['BIA08_07']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Business Unit #9</label>
						<br /><br />
						<label class="lblSubQuestion">Question 1: Please Identify an existing Business Unit</label><br />
						<label>Business Unit Name: <?php echo $row_rsForm['BIA09_01']; ?></label><br />
						<label>Purpose of this Business Unit: <?php echo $row_rsForm['BIA09_02']; ?></label><br /><br />
						<label>Identify Any Critical Documents that exist in this Unit: <?php echo $row_rsForm['BIA09_03']; ?></label><br />
						<label>What Month is the busiest for this Business Unit: <?php echo $row_rsForm['BIA09_04']; ?></label><br />
						<label>Number of People Required to keep this Unit Running: <?php echo $row_rsForm['BIA09_05']; ?></label><br />
						<label>What is the maximum time your can operate without this Business Unit: <?php echo $row_rsForm['BIA09_06']; ?></label><br />
						<label>In the event of a disaster how many people do you require to keep this operational: <?php echo $row_rsForm['BIA09_07']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Business Unit #10</label>
						<br /><br />
						<label class="lblSubQuestion">Question 1: Please Identify an existing Business Unit</label><br />
						<label>Business Unit Name: <?php echo $row_rsForm['BIA10_01']; ?></label><br />
						<label>Purpose of this Business Unit: <?php echo $row_rsForm['BIA10_02']; ?></label><br /><br />
						<label>Identify Any Critical Documents that exist in this Unit: <?php echo $row_rsForm['BIA10_03']; ?></label><br />
						<label>What Month is the busiest for this Business Unit: <?php echo $row_rsForm['BIA10_04']; ?></label><br />
						<label>Number of People Required to keep this Unit Running: <?php echo $row_rsForm['BIA10_05']; ?></label><br />
						<label>What is the maximum time your can operate without this Business Unit: <?php echo $row_rsForm['BIA10_06']; ?></label><br />
						<label>In the event of a disaster how many people do you require to keep this operational: <?php echo $row_rsForm['BIA10_07']; ?></label>
					</div>
			<?php }//end of if ?>
				
				<!-- Crisis -->
				
				<?php 
				if($intTableIndex == 5)
				{?>
					<label>In the Event of a disaster there are many areas in business that are over looked in terms of protecting your business.</label>
					<br /><br />
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 1:</label>
						<br /><br />
						<label>Please identify a Crisis Communcations Coordinator that will be responsible for notifying and coordinating the emergency operations and communications of your business.</label>
						<br /><br />
						<label>Name: <?php echo $row_rsForm['comm_coor01']; ?></label><br />
						<label>Title: <?php echo $row_rsForm['comm_coor02']; ?><br />
						Phone: <?php echo $row_rsForm['comm_coor03']; ?></label><br />
						<label>Cell: <?php echo $row_rsForm['comm_coor04']; ?></label><br />
						<label>E-Mail: <?php echo $row_rsForm['comm_coor05']; ?></label>
						<br /><br />
						<label>Communication is the basis for business survival in term of reputation, customers and overall business operation. Use the following form to create a communications strategy to address each specific area of business, and how you have prepared you company to protect yours and their best interests.</label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 2:</label>
						<br /><br />
						<label>Please prepare a basic statement for the media that you can use in the event of a disaster. Please provide a basic response with details of your plan of action.</label>
						<br /><br />
						<label class="lblSubQuestion">Statement For the Media
						<br /><br />
						<?php echo $row_rsForm['comm_med01']; ?></label>
						<br /><br />
						<label  class="lblSubQuestion">Media Contact List</label>
						<br /><br />
						<label>(Newspaper) Media Company 1: <?php echo $row_rsForm['comm_med02']; ?>
						Contact: <?php echo $row_rsForm['comm_med03']; ?> Phone: <?php echo $row_rsForm['comm_med04']; ?></label>
						<br />
						<label>(Radio) Media Company 2: <?php echo $row_rsForm['comm_med05']; ?>
						Contact: <?php echo $row_rsForm['comm_med06']; ?> Phone: <?php echo $row_rsForm['comm_med07']; ?></label>
						<br />
						<label>(News Reporter) Media Company 2: <?php echo $row_rsForm['comm_med08']; ?>
						Contact: <?php echo $row_rsForm['comm_med09']; ?> Phone: <?php echo $row_rsForm['comm_med10']; ?></label>
						<br />
						<label>(Television - Local)	Media Company 2: <?php echo $row_rsForm['comm_med11']; ?>
						Contact: <?php echo $row_rsForm['comm_med12']; ?> Phone: <?php echo $row_rsForm['comm_med13']; ?></label>
						<br />
						<label>(Television - Regional) Media Company 2: <?php echo $row_rsForm['comm_med14']; ?>
						Contact: <?php echo $row_rsForm['comm_med15']; ?> Phone: <?php echo $row_rsForm['comm_med16']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 3:</label>
						<br /><br />
						<label>Please prepare a basic statement for your employees that you can use in the event of a disaster. Please provide details of what you want your employees to do immediately after an event has occurred.
	Statement For your Employees
						<br /><br />
						<?php echo $row_rsForm['comm_EMP01']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 4:</label>
						<br /><br />
						<label>Please prepare a basic statement for your Customer that you can use in the event of a disaster. Please provide details of what you will be doing to ensure they continue to get products and services.
	Statement For your Customers
						<br /><br />
						<?php echo $row_rsForm['comm_CUST01']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 5:</label>
						<br /><br />
						<label>Please prepare a basic statement for your Suppliers that you can use in the event of a disaster. Please provide details of what you will be doing to ensure they will be informed of your status and what to do while you are recovering.
	Statement For your Suppliers
						<br /><br />
						<?php echo $row_rsForm['comm_SUPP01']; ?></label>
					</div>
			<?php }//end of if ?>
				
				<!-- Logistics -->
				
				<?php 
				if($intTableIndex == 6)
				{?>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 1:</label>
						<br /><br />
						<label>In the Event of A Disaster you may require the assistance of speciality companies to assist in the recovery and or moving process. Please seek out and identify the company that is closest and willing to provide service even in a disaster.
						<br /><br />
						Please identify a company in your area that currently provides the following services:
						<br /><br />
						Airline: <?php echo $row_rsForm['LOG_01']; ?> Phone: <?php echo $row_rsForm['LOG_02']; ?> Fax: <?php echo $row_rsForm['LOG_03']; ?><br />
						Office Space: <?php echo $row_rsForm['LOG_04']; ?> Phone: <?php echo $row_rsForm['LOG_05']; ?> Fax: <?php echo $row_rsForm['LOG_06']; ?><br />
						Hotel: <?php echo $row_rsForm['LOG_07']; ?> Phone: <?php echo $row_rsForm['LOG_08']; ?> Fax: <?php echo $row_rsForm['LOG_09']; ?><br />
						Chartered Bus: <?php echo $row_rsForm['LOG_10']; ?> Phone: <?php echo $row_rsForm['LOG_11']; ?> Fax: <?php echo $row_rsForm['LOG_12']; ?><br />
						Car Rentals: <?php echo $row_rsForm['LOG_13']; ?> Phone: <?php echo $row_rsForm['LOG_14']; ?> Fax: <?php echo $row_rsForm['LOG_15']; ?><br />
						Courier Local: <?php echo $row_rsForm['LOG_16']; ?> Phone: <?php echo $row_rsForm['LOG_17']; ?> Fax: <?php echo $row_rsForm['LOG_18']; ?><br />
						Courier Long Distance: <?php echo $row_rsForm['LOG_19']; ?> Phone: <?php echo $row_rsForm['LOG_20']; ?> Fax: <?php echo $row_rsForm['LOG_21']; ?><br />
						Local Transit: <?php echo $row_rsForm['LOG_22']; ?> Phone: <?php echo $row_rsForm['LOG_23']; ?> Fax: <?php echo $row_rsForm['LOG_24']; ?><br />
						Moving Company: <?php echo $row_rsForm['LOG_25']; ?> Phone: <?php echo $row_rsForm['LOG_26']; ?> Fax: <?php echo $row_rsForm['LOG_27']; ?><br />
						Postal Service: <?php echo $row_rsForm['LOG_28']; ?> Phone: <?php echo $row_rsForm['LOG_29']; ?> Fax: <?php echo $row_rsForm['LOG_30']; ?><br />
						Telephone System: <?php echo $row_rsForm['LOG_31']; ?> Phone: <?php echo $row_rsForm['LOG_32']; ?> Fax: <?php echo $row_rsForm['LOG_33']; ?><br />
						Travel Agent: <?php echo $row_rsForm['LOG_34']; ?> Phone: <?php echo $row_rsForm['LOG_35']; ?> Fax: <?php echo $row_rsForm['LOG_36']; ?></label><br />
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 2:</label>
						<br /><br />
						<label>Please identify a company in your area that could provide the following services if your current provide is unavailable:
						<br /><br />
						Airline: <?php echo $row_rsForm['LOG_37']; ?> Phone: <?php echo $row_rsForm['LOG_38']; ?> Fax: <?php echo $row_rsForm['LOG_39']; ?><br />
						Apartments: <?php echo $row_rsForm['LOG_40']; ?> Phone: <?php echo $row_rsForm['LOG_41']; ?> Fax: <?php echo $row_rsForm['LOG_42']; ?><br />
						Hotel: <?php echo $row_rsForm['LOG_43']; ?> Phone: <?php echo $row_rsForm['LOG_44']; ?> Fax: <?php echo $row_rsForm['LOG_45']; ?><br />
						Chartered Bus: <?php echo $row_rsForm['LOG_46']; ?> Phone: <?php echo $row_rsForm['LOG_47']; ?> Fax: <?php echo $row_rsForm['LOG_48']; ?><br />
						Car Rentals: <?php echo $row_rsForm['LOG_49']; ?> Phone: <?php echo $row_rsForm['LOG_50']; ?> Fax: <?php echo $row_rsForm['LOG_51']; ?><br />
						Courier Local: <?php echo $row_rsForm['LOG_52']; ?> Phone: <?php echo $row_rsForm['LOG_53']; ?> Fax: <?php echo $row_rsForm['LOG_54']; ?><br />
						Courier Long Distance: <?php echo $row_rsForm['LOG_55']; ?> Phone: <?php echo $row_rsForm['LOG_56']; ?> Fax: <?php echo $row_rsForm['LOG_57']; ?><br />
						Freight: <?php echo $row_rsForm['LOG_58']; ?> Phone: <?php echo $row_rsForm['LOG_59']; ?> Fax: <?php echo $row_rsForm['LOG_60']; ?><br />
						Local Transit: <?php echo $row_rsForm['LOG_61']; ?> Phone: <?php echo $row_rsForm['LOG_62']; ?> Fax: <?php echo $row_rsForm['LOG_63']; ?><br />
						Moving Company: <?php echo $row_rsForm['LOG_64']; ?> Phone: <?php echo $row_rsForm['LOG_65']; ?> Fax: <?php echo $row_rsForm['LOG_66']; ?><br />
						Postal Service: <?php echo $row_rsForm['LOG_67']; ?> Phone: <?php echo $row_rsForm['LOG_68']; ?> Fax: <?php echo $row_rsForm['LOG_69']; ?><br />
						Telephone System:  <?php echo $row_rsForm['LOG_70']; ?> Phone: <?php echo $row_rsForm['LOG_71']; ?> Fax: <?php echo $row_rsForm['LOG_72']; ?><br />
						Travel Agent: <?php echo $row_rsForm['LOG_73']; ?> Phone: <?php echo $row_rsForm['LOG_74']; ?> Fax: <?php echo $row_rsForm['LOG_75']; ?><br />
						<br /><br />
						Puralator: <?php echo $row_rsForm['LOG_76']; ?> Phone: <?php echo $row_rsForm['LOG_77']; ?> Fax: <?php echo $row_rsForm['LOG_78']; ?><br />
						UPS: <?php echo $row_rsForm['LOG_79']; ?> Phone: <?php echo $row_rsForm['LOG_80']; ?> Fax: <?php echo $row_rsForm['LOG_81']; ?><br />
						FEDEX: <?php echo $row_rsForm['LOG_82']; ?> Phone: <?php echo $row_rsForm['LOG_83']; ?> Fax: <?php echo $row_rsForm['LOG_84']; ?><br />
						DSL: <?php echo $row_rsForm['LOG_85']; ?> Phone: <?php echo $row_rsForm['LOG_86']; ?> Fax: <?php echo $row_rsForm['LOG_87']; ?></label><br />
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 3:</label>
						<br /><br />
						<label>Please give a brief overview of the logistics of your company. What, if any are the products your ship and receive on a regular basis, If possible provide a brief paragraph that outlines the day-to day operations:
						<br /><br />
						<?php echo $row_rsForm['LOG_DESC01']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- Alternate -->
				
				<?php 
				if($intTableIndex == 7)
				{?>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 1:</label>
						<br /><br />
						<label>In the event of a disaster you may require to access a temporary location or semi-permanent location to continue in business. Whether it is a friendly competitor or your home, a location must be pre-determined, and all of the tools and equipment necessary to resume business as quickly as possible.
						<br /><br />
						In the event of a disaster and your business is not able to remain open, please identify a temporary location that you could potentially use to remain in business.</label>
						<br /><br />
						<label class="lblSubQuestion">Alternate Location #1: Temporary Office Location</label><br />
						<label>Name of Location: <?php echo $row_rsForm['ALT_TempLOG01']; ?><br />
						Location Address: <?php echo $row_rsForm['ALT_TempLOG02']; ?><br />
						Phone: <?php echo $row_rsForm['ALT_TempLOG03']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_TempLOG04']; ?><br /><br />
						Site Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_TempLOG05']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Location #1: Temporary Office Location (Back-Up)</label><br />
						<label>Name of Location: <?php echo $row_rsForm['ALT_Temp2LOG01']; ?><br />
						Location Address: <?php echo $row_rsForm['ALT_Temp2LOG02']; ?><br />
						Phone: <?php echo $row_rsForm['ALT_Temp2LOG03']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_Temp2LOG04']; ?><br /><br />
						Site Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_Temp2LOG05']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Location #2: Semi-Permanent Office Location</label><br />
						<label>Name of Location: <?php echo $row_rsForm['ALT_SemiLOG01']; ?><br />
						Location Address: <?php echo $row_rsForm['ALT_SemiLOG02']; ?><br />
						Phone: <?php echo $row_rsForm['ALT_SemiLOG03']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_SemiLOG04']; ?><br /><br />
						Site Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_SemiLOG05']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Location #2: Semi-Permanent Office Location (Back-Up)</label><br />
						<label>Name of Location: <?php echo $row_rsForm['ALT_Semi2LOG01']; ?><br />
						Location Address: <?php echo $row_rsForm['ALT_Semi2LOG02']; ?><br />
						Phone: <?php echo $row_rsForm['ALT_Semi2LOG03']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_Semi2LOG04']; ?><br /><br />
						Site Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_Semi2LOG05']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Location #3: Permanent New Office Location</label><br />
						<label>Name of Location: <?php echo $row_rsForm['ALT_PermLOG01']; ?><br />
						Location Address: <?php echo $row_rsForm['ALT_PermLOG02']; ?><br />
						Phone: <?php echo $row_rsForm['ALT_PermLOG03']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_PermLOG04']; ?><br /><br />
						Site Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_PermLOG05']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Location #3: Permanent New Office Location (Back-Up)</label><br />
						<label>Name of Location: <?php echo $row_rsForm['ALT_Perm2LOG01']; ?><br />
						Location Address: <?php echo $row_rsForm['ALT_Perm2LOG02']; ?><br />
						Phone: <?php echo $row_rsForm['ALT_Perm2LOG03']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_Perm2LOG04']; ?><br /><br />
						Site Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_Perm2LOG05']; ?></label><br />
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 2:</label>
						<br /><br />
						<label>In the event of a disaster you may be unable to use your current supplies of products or services
						<br /><br />
						Who else could supply you with the Products & Services that you require to remain in business. Should your current supplier be unable to provide you with service you will need to be able to access additional resources. Please identify some of the alternate suppliers of your products and services you can use as a back-up</label>
						<br /><br />
						<label class="lblSubQuestion">Alternate Supplier #1: Product</label><br />
						<label>Name of Product: <?php echo $row_rsForm['ALT_SUPPD01']; ?><br />
						Name of Supplier: <?php echo $row_rsForm['ALT_SUPPD02']; ?><br />
						Address: <?php echo $row_rsForm['ALT_SUPPD03']; ?> Phone: <?php echo $row_rsForm['ALT_SUPPD04']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_SUPPD05']; ?><br /><br />
						Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_SUPPD06']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Supplier #2: Product</label><br />
						<label>Name of Product: <?php echo $row_rsForm['ALT_SUPPD07']; ?><br />
						Name of Supplier: <?php echo $row_rsForm['ALT_SUPPD08']; ?><br />
						Address: <?php echo $row_rsForm['ALT_SUPPD09']; ?> Phone: <?php echo $row_rsForm['ALT_SUPPD10']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_SUPPD11']; ?><br /><br />
						Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_SUPPD12']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Supplier #3: Product</label><br />
						<label>Name of Product: <?php echo $row_rsForm['ALT_SUPPD13']; ?><br />
						Name of Supplier: <?php echo $row_rsForm['ALT_SUPPD14']; ?><br />
						Address: <?php echo $row_rsForm['ALT_SUPPD15']; ?> Phone: <?php echo $row_rsForm['ALT_SUPPD16']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_SUPPD17']; ?><br /><br />
						Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_SUPPD18']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Supplier #4: Product</label><br />
						<label>Name of Product: <?php echo $row_rsForm['ALT_SUPPD19']; ?><br />
						Name of Supplier: <?php echo $row_rsForm['ALT_SUPPD20']; ?><br />
						Address: <?php echo $row_rsForm['ALT_SUPPD21']; ?> Phone: <?php echo $row_rsForm['ALT_SUPPD22']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_SUPPD23']; ?><br /><br />
						Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_SUPPD24']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Supplier #5: Product</label><br />
						<label>Name of Product: <?php echo $row_rsForm['ALT_SUPPD25']; ?><br />
						Name of Supplier: <?php echo $row_rsForm['ALT_SUPPD26']; ?><br />
						Address: <?php echo $row_rsForm['ALT_SUPPD27']; ?> Phone: <?php echo $row_rsForm['ALT_SUPPD28']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_SUPPD29']; ?><br /><br />
						Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_SUPPD30']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Supplier #1: Service</label><br />
						<label>Name of Service: <?php echo $row_rsForm['ALT_SUPSR01']; ?><br />
						Name of Supplier: <?php echo $row_rsForm['ALT_SUPSR02']; ?><br />
						Address: <?php echo $row_rsForm['ALT_SUPSR03']; ?> Phone: <?php echo $row_rsForm['ALT_SUPSR04']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_SUPSR05']; ?><br /><br />
						Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_SUPSR06']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Supplier #2: Service</label><br />
						<label>Name of Service: <?php echo $row_rsForm['ALT_SUPSR07']; ?><br />
						Name of Supplier: <?php echo $row_rsForm['ALT_SUPSR08']; ?><br />
						Address: <?php echo $row_rsForm['ALT_SUPSR09']; ?> Phone: <?php echo $row_rsForm['ALT_SUPSR10']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_SUPSR11']; ?><br /><br />
						Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_SUPSR12']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Supplier #3: Service</label><br />
						<label>Name of Service: <?php echo $row_rsForm['ALT_SUPSR13']; ?><br />
						Name of Supplier: <?php echo $row_rsForm['ALT_SUPSR14']; ?><br />
						Address: <?php echo $row_rsForm['ALT_SUPSR15']; ?> Phone: <?php echo $row_rsForm['ALT_SUPSR16']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_SUPSR17']; ?><br /><br />
						Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_SUPSR18']; ?></label><br /><br />
						<label class="lblSubQuestion">Alternate Supplier #4: Service</label><br />
						<label>Name of Service: <?php echo $row_rsForm['ALT_SUPSR19']; ?><br />
						Name of Supplier: <?php echo $row_rsForm['ALT_SUPSR20']; ?><br />
						Address: <?php echo $row_rsForm['ALT_SUPSR21']; ?> Phone: <?php echo $row_rsForm['ALT_SUPSR22']; ?><br />
						Alt. phone: <?php echo $row_rsForm['ALT_SUPSR23']; ?><br /><br />
						Agreement &amp; Conditions: <?php echo $row_rsForm['ALT_SUPSR24']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- Salvage -->
				
				<?php 
				if($intTableIndex == 8)
				{?>
					<label>In the event of a disaster you may need to use a external company to store or transport any and all material and equipment that has been salvaged from the disaster.</label>
					<br /><br />    
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 1:</label>
						<br /><br />
						<label>Please Identify some external companies that can provide you with the following services.</label>
						<br /><br />
						<label class="lblSubQuestion">Freight &amp; Transportation 1:</label><br/>
						<label>Company Name: <?php echo $row_rsForm['SAL_CPN01']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN02']; ?><br/>
						Contact: <?php echo $row_rsForm['SAL_CPN03']; ?></label><br/>
						<br /><br />
						<label class="lblSubQuestion">Freight &amp; Transportation 2:</label><br/>
						<label>Company Name: <?php echo $row_rsForm['SAL_CPN04']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN05']; ?><br/>
						Contact: <?php echo $row_rsForm['SAL_CPN06']; ?></label><br/>
						<br /><br />
						<label class="lblSubQuestion">Freight &amp; Transportation 3:</label><br/>
						<label>Company Name: <?php echo $row_rsForm['SAL_CPN07']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN08']; ?><br/>
						Contact: <?php echo $row_rsForm['SAL_CPN09']; ?></label><br/>
						<br /><br />
						<label class="lblSubQuestion">Off-Site Storage 2:</label><br/>
						<label>Company Name: <?php echo $row_rsForm['SAL_CPN10']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN11']; ?><br/>
						Contact: <?php echo $row_rsForm['SAL_CPN12']; ?></label><br/>
						<br /><br />
						<label class="lblSubQuestion">Off-Site Storage 3:</label><br/>
						<label>Company Name: <?php echo $row_rsForm['SAL_CPN13']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN14']; ?><br/>
						Contact: <?php echo $row_rsForm['SAL_CPN15']; ?></label><br/>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 2:</label>
						<br /><br />
						<label>You may need to import some resources from another country. Please identify a customs broker who can assist you with the entire process.</label>
						<br /><br />
						<label class="lblSubQuestion">Customs Broker 1:</label><br/>
						<label>Company Name: <?php echo $row_rsForm['SAL_CPN16']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN17']; ?><br/>
						Contact: <?php echo $row_rsForm['SAL_CPN18']; ?></label><br/>
						<br /><br />
						<label class="lblSubQuestion">Customs Broker 2:</label><br/>
						<label>Company Name: <?php echo $row_rsForm['SAL_CPN19']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN20']; ?><br/>
						Contact: <?php echo $row_rsForm['SAL_CPN21']; ?></label><br/>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 3:</label>
						<br /><br />
						<label>If in fact you need to move to an additional location, or your current location has experienced a loss, you will need to ensure that you still have security at your premises. This section will allow you to better prepare you business to remain secure during an event.</label>
						<br /><br />
						<label class="lblSubQuestion">Security Company 1:</label><br/>
						<label>Company Name: <?php echo $row_rsForm['SAL_CPN22']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN23']; ?><br/>
						Contact: <?php echo $row_rsForm['SAL_CPN24']; ?>
						<br/><br/>
						Description of Services:<br />
					   <?php echo $row_rsForm['SAL_CPN25']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Security Company 2:</label><br/>
						<label>Company Name: <?php echo $row_rsForm['SAL_CPN26']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN27']; ?><br/>
						Contact: <?php echo $row_rsForm['SAL_CPN28']; ?>
						<br/><br/>
						Description of Services:<br />
						<?php echo $row_rsForm['SAL_CPN29']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 4:</label>
						<br /><br />
						<label>In the event of a disaster you will need to ensure certain key areas of your business are not affected and continue to function. Should you need to access or use additional resources in these areas please identify the essential contact information concerning these providers.</label>
						<br /><br />
						<label class="lblSubQuestion">Bank &amp; Financial Information 1:</label><br/>
						<label>Institution Name: <?php echo $row_rsForm['SAL_CPN30']; ?><br/>
						Account #: <?php echo $row_rsForm['SAL_CPN31']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN32']; ?><br/>
						Account Rep: <?php echo $row_rsForm['SAL_CPN33']; ?></label><br/>
						<br /><br />
						<label class="lblSubQuestion">Bank &amp; Financial Information 2:</label><br/>
						<label>Institution Name: <?php echo $row_rsForm['SAL_CPN34']; ?><br/>
						Account #: <?php echo $row_rsForm['SAL_CPN35']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN36']; ?><br/>
						Account Rep: <?php echo $row_rsForm['SAL_CPN37']; ?></label><br/>
						<br /><br />
						<label class="lblSubQuestion">Office Furniture Supplier 1:</label><br/>
						<label>Company Name: <?php echo $row_rsForm['SAL_CPN38']; ?><br/>
						Account #: <?php echo $row_rsForm['SAL_CPN39']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN40']; ?><br/>
						Account Rep: <?php echo $row_rsForm['SAL_CPN41']; ?></label><br/>
						<br /><br />
						<label class="lblSubQuestion">Insurance Company 1:</label><br/>
						<label>Company Name: <?php echo $row_rsForm['SAL_CPN42']; ?><br/>
						Policy #: <?php echo $row_rsForm['SAL_CPN43']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN44']; ?><br/>
						Account Rep: <?php echo $row_rsForm['SAL_CPN45']; ?></label><br/>
						<br /><br />
						<label class="lblSubQuestion">Off-Site IT Provider 1:</label><br/>
						<label>Company Name: <?php echo $row_rsForm['SAL_CPN46']; ?><br/>
						Phone: <?php echo $row_rsForm['SAL_CPN47']; ?><br/>
						Contact: <?php echo $row_rsForm['SAL_CPN48']; ?></label><br/>
					</div>
				<?php }//end of if ?>
				
				<!-- Customer -->
				
				<?php 
				if($intTableIndex == 9)
				{?>
					<label>In the Event of a disaster there are many areas in business that are over looked in terms of protecting your business.
		Please identify a Customer Service Coordinator that will be responsible for ensuring you are able to continue to provide products and service to your customers.</label>
					<br />
					<div class="divQuestionForms">
						<label>Name: <?php echo $row_rsForm['cust_coor01']; ?><br />
						Title: <?php echo $row_rsForm['cust_coor02']; ?><br />
						Phone: <?php echo $row_rsForm['cust_coor03']; ?><br />
						Cell: <?php echo $row_rsForm['cust_coor04']; ?><br />
						E-Mail: <?php echo $row_rsForm['cust_coor05']; ?>
						<br /><br />
						Customer Service is one of the essential areas you need to be able to provide, even in a disaster scenario.</label>
						<br />
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 1:</label>
						<br /><br />
						<label>Please idenitfy the key services that you currently provide to your customer in order of priority.
						<br /><br />
						Service 1: <?php echo $row_rsForm['cust_ser01']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_ser02']; ?><br />
						How Many Employees are required to perform this Service? <?php echo $row_rsForm['cust_ser03']; ?>
						<br /><br />
						Service 2: <?php echo $row_rsForm['cust_ser04']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_ser05']; ?><br />
						How Many Employees are required to perform this Service? <?php echo $row_rsForm['cust_ser06']; ?>
						<br /><br />
						Service 3: <?php echo $row_rsForm['cust_ser07']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_ser08']; ?><br />
						How Many Employees are required to perform this Service? <?php echo $row_rsForm['cust_ser09']; ?>
						<br /><br />
						Service 4: <?php echo $row_rsForm['cust_ser10']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_ser11']; ?><br />
						How Many Employees are required to perform this Service? <?php echo $row_rsForm['cust_ser12']; ?>
						<br /><br />
						Service 5: <?php echo $row_rsForm['cust_ser13']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_ser14']; ?><br />
						How Many Employees are required to perform this Service? <?php echo $row_rsForm['cust_ser15']; ?>
						<br /><br />
						Service 6: <?php echo $row_rsForm['cust_ser16']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_ser17']; ?><br />
						How Many Employees are required to perform this Service? <?php echo $row_rsForm['cust_ser18']; ?>
						<br /><br />
						Service 7: <?php echo $row_rsForm['cust_ser19']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_ser20']; ?><br />
						How Many Employees are required to perform this Service? <?php echo $row_rsForm['cust_ser21']; ?>
						<br /><br />
						Service 8: <?php echo $row_rsForm['cust_ser22']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_ser23']; ?><br />
						How Many Employees are required to perform this Service? <?php echo $row_rsForm['cust_ser24']; ?>
						<br /><br />
						Service 9: <?php echo $row_rsForm['cust_ser25']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_ser26']; ?><br />
						How Many Employees are required to perform this Service? <?php echo $row_rsForm['cust_ser27']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 2:</label>
						<br /><br />
						<label>Please idenitfy the key Product that you currently provide to your customer in order of priority.
						<br /><br />
						Product 1: <?php echo $row_rsForm['cust_Pro01']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_Pro02']; ?><br />
						How Many Employees are required to Recover this Product? Pro<?php echo $row_rsForm['cust_Pro03']; ?>
						<br /><br />
						Product 2: <?php echo $row_rsForm['cust_Pro04']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_Pro05']; ?><br /><br />
						How Many Employees are required to Recover this Product? Pro<?php echo $row_rsForm['cust_Pro06']; ?>
						<br /><br />
						Product 3: <?php echo $row_rsForm['cust_Pro07']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_Pro08']; ?><br />
						How Many Employees are required to Recover this Product? Pro<?php echo $row_rsForm['cust_Pro09']; ?>
						<br /><br />
						Product 4: <?php echo $row_rsForm['cust_Pro10']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_Pro11']; ?><br />
						How Many Employees are required to Recover this Product? Pro<?php echo $row_rsForm['cust_Pro12']; ?>
						<br /><br />
						Product 5: <?php echo $row_rsForm['cust_Pro13']; ?><br />
						Purpose: <?php echo $row_rsForm['cust_Pro14']; ?><br />
						How Many Employees are required to Recover this Product? Pro<?php echo $row_rsForm['cust_Pro15']; ?></label><br />
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 3:</label>
						<br /><br />
						<label>Please prepare a temporary statement for you customers informing them that you have experienced a disaster and are working diligently to ensure you are able to continue to provide Products and Service to them.
						<br /><br />
						Statement for Customers:<br/>
						<?php echo $row_rsForm['cust_statement']; ?></label><br />
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 4: </label>
						<br /><br />
						<label>In the event you are unable to recover as quickly as you though, you may need to use your relationships with your competitors or partners in the industry. You may need to temporarily use them to complete your orders or send you customer to them.
						<br /><br />
						Please identify some potential alliances that you are able to make with competitors or partners to use their service in the event of a disaster.
						<br />
						Potential Alliance Partner Company 1: <?php echo $row_rsForm['cust_all01']; ?><br />
						Contact: <?php echo $row_rsForm['cust_all02']; ?><br />
						Address: <?php echo $row_rsForm['cust_all03']; ?><br />
						Phone: <?php echo $row_rsForm['cust_all04']; ?><br />
						E-Mail: <?php echo $row_rsForm['cust_all05']; ?><br />
						Product or Service They Provide: <?php echo $row_rsForm['cust_all06']; ?><br /><br />
						(you will need to contact them to make sure you are able to use them in the event of a disaster. Once you have an agreement provide some basic guidelines for that agreement)
						<br /><br />
						Terms of Agreement:<br/> 
						<?php echo $row_rsForm['cust_all07']; ?>
						<br />
						Potential Alliance Partner Company 2: <?php echo $row_rsForm['cust_all08']; ?><br />
						Contact: <?php echo $row_rsForm['cust_all09']; ?><br />
						Address: <?php echo $row_rsForm['cust_all10']; ?><br />
						Phone: <?php echo $row_rsForm['cust_all11']; ?><br />
						E-Mail: <?php echo $row_rsForm['cust_all12']; ?><br />
						Product or Service They Provide: <?php echo $row_rsForm['cust_all13']; ?><br /><br />
						(you will need to contact them to make sure you are able to use them in the event of a disaster. Once you have an agreement provide some basic guidelines for that agreement)
						<br /><br />
						Terms of Agreement:<br/> 
						<?php echo $row_rsForm['cust_all14']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- Environment -->
				
				<?php 
				if($intTableIndex == 10)
				{?>
					 <label>Should an event occur at your business, it is important to take the environment and your privacy into consideration. Use this section to ensure that you are able to meet the needs of some environmental and privacy threats.</label>
					<br /><br />
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 1:</label>
						<br /><br />
						<label class="lblSubQuestion">Environmental Provincial Police (Canada):</label><br />
						<label>Phone: <?php echo $row_rsForm['ENV_CPN01']; ?><br />
						Alt. Phone: <?php echo $row_rsForm['ENV_CPN02']; ?><br />
						Contact: <?php echo $row_rsForm['ENV_CPN03']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Environmental State Police (US):</label><br />
						<label>Phone: <?php echo $row_rsForm['ENV_CPNus01']; ?><br />
						Alt. Phone: <?php echo $row_rsForm['ENV_CPNus02']; ?><br />
						Contact: <?php echo $row_rsForm['ENV_CPNus03']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Environmental Municipal Police:</label><br />
						<label>Phone: <?php echo $row_rsForm['ENV_CPN04']; ?><br />
						Alt. Phone: <?php echo $row_rsForm['ENV_CPN05']; ?><br />
						Contact: <?php echo $row_rsForm['ENV_CPN06']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Fire Department Spill Response Team:</label><br />
						<label>Phone: <?php echo $row_rsForm['ENV_CPN07']; ?><br />
						Alt. Phone: <?php echo $row_rsForm['ENV_CPN08']; ?><br />
						Contact: <?php echo $row_rsForm['ENV_CPN09']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Electronics Recycling Depot:</label><br />
						<label>Phone: <?php echo $row_rsForm['ENV_CPN10']; ?><br />
						Alt. Phone: <?php echo $row_rsForm['ENV_CPN11']; ?><br />
						Contact: <?php echo $row_rsForm['ENV_CPN12']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- Immediate -->
				
				<?php 
				if($intTableIndex == 11)
				{?>
					<label>In the event of a disaster any and all employees with CPR and First Aid training will need to be identify so they can help those who have been injured in the event.</label>
					<br /><br />
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 1</label>
						<br /><br />
						<label>Please identify the employees with training and certification in CPR/First-Aid
						<br />
						Employee Name 1: <?php echo $row_rsForm['IRT_CPR01']; ?> Phone: <?php echo $row_rsForm['IRT_CPR02']; ?> Cell: <?php echo $row_rsForm['IRT_CPR03']; ?><br /><br /> Training:<br /> <?php echo $row_rsForm['IRT_CPR04']; ?><br />
						Employee Name 2: <?php echo $row_rsForm['IRT_CPR05']; ?> Phone: <?php echo $row_rsForm['IRT_CPR06']; ?> Cell: <?php echo $row_rsForm['IRT_CPR07']; ?><br /><br /> Training:<br /> <?php echo $row_rsForm['IRT_CPR08']; ?><br />
						Employee Name 3: <?php echo $row_rsForm['IRT_CPR09']; ?> Phone: <?php echo $row_rsForm['IRT_CPR10']; ?> Cell: <?php echo $row_rsForm['IRT_CPR11']; ?><br /><br /> Training:<br /> <?php echo $row_rsForm['IRT_CPR12']; ?><br />
						Employee Name 4: <?php echo $row_rsForm['IRT_CPR13']; ?> Phone: <?php echo $row_rsForm['IRT_CPR14']; ?> Cell: <?php echo $row_rsForm['IRT_CPR15']; ?><br /><br /> Training:<br /> <?php echo $row_rsForm['IRT_CPR16']; ?><br /></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 2:</label>
						<br /><br />
						<label>If and when possible, you will try and eliminate or contain the source of the disaster if it is still a threat. This should be attempted only if you are trained in the techniques you will be using. The two most common events that would possible be within your control are:
						<br /><br />
						Fire - Record the location of any and all fire extinguishers, use them if the situation is containable. 
						<br /><br />
						Utilities - Record the location of any shut off values and operations.</label>
						<br /><br />
						<label class="lblSubQuestion">Fire Extinguisher Locations:</label>
						<br />
						<label>Location 1: <?php echo $row_rsForm['IRT_FIRE01']; ?><br />
						Extinguisher Maintenance Date: <?php echo $row_rsForm['IRT_FIRE02']; ?><br />
						Location 2: <?php echo $row_rsForm['IRT_FIRE03']; ?><br />
						Extinguisher Maintenance Date: <?php echo $row_rsForm['IRT_FIRE04']; ?><br />
						Location 3: <?php echo $row_rsForm['IRT_FIRE05']; ?><br />
						Extinguisher Maintenance Date: <?php echo $row_rsForm['IRT_FIRE06']; ?><br />
						Location 4: <?php echo $row_rsForm['IRT_FIRE07']; ?><br />
						Extinguisher Maintenance Date: <?php echo $row_rsForm['IRT_FIRE08']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Utilities Locations:</label><br /><br />
						<label class="lblSubQuestion">Electricity:</label><br />
						<label>Main Breaker Location: <?php echo $row_rsForm['IRT_UTIL01']; ?><br />
						Maintenance Date: <?php echo $row_rsForm['IRT_UTIL02']; ?><br />
						Other Breaker Location: <?php echo $row_rsForm['IRT_UTIL03']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Gas:</label><br />
						<label>Main Breaker Location: <?php echo $row_rsForm['IRT_UTIL04']; ?><br />
						Maintenance Date: <?php echo $row_rsForm['IRT_UTIL05']; ?><br />
						Other Breaker Location: <?php echo $row_rsForm['IRT_UTIL06']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Flooding/Water:</label><br />
						<label>Main Breaker Location: <?php echo $row_rsForm['IRT_UTIL07']; ?><br />
						Maintenance Date: <?php echo $row_rsForm['IRT_UTIL08']; ?><br />
						Other Breaker Location: <?php echo $row_rsForm['IRT_UTIL09']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Air Conditioners:</label><br />
						<label>Main Breaker Location: <?php echo $row_rsForm['IRT_UTIL10']; ?><br />
						Maintenance Date: <?php echo $row_rsForm['IRT_UTIL11']; ?><br />
						Other Breaker Location: <?php echo $row_rsForm['IRT_UTIL12']; ?></label><br />
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 3:</label>
						<br /><br />
						<label>As a disaster or significant disruption occurs at your business location, you will need to pre-determine a evacuation method and meeting point for all employees to meet. This place should be outside the office building at a decently safe distance away.
		Please identify the meeting location for all employees to meet after an evacuation is ordered.
						<br />
						Meeting Location 1: <?php echo $row_rsForm['IRT_MEET01']; ?><br />
						<br />
						If that location in unavailable
						<br />
						Meeting Location 2: <?php echo $row_rsForm['IRT_MEET02']; ?></label><br />
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 4:</label>
						<br /><br />
						<label>After you have successfully evacuated the building you will need to proceed to a place where all of the recovery teams can meet and plan to execute all areas of the recovery process. This will be called your Emergency Operations Centre
		Please identify the Emergency Operations Centre for your business.</label>
						<br /><br />
						<label class="lblSubQuestion">The Emergency Operations Centre:</label>
						<br /><br />
						<label>Location: <?php echo $row_rsForm['IRT_EOC01']; ?><br />
						Address: <?php echo $row_rsForm['IRT_EOC02']; ?><br />
						Phone: <?php echo $row_rsForm['IRT_EOC03']; ?>
						<br /><br />
						Additional Information: <?php echo $row_rsForm['IRT_EOC04']; ?></label>
						<br /><br />
						<label class="lblSubQuestion">Back-Up Emergency Operations Centre:</label>
						<br /><br />
						<label>Location: <?php echo $row_rsForm['IRT_EOC05']; ?><br />
						Address: <?php echo $row_rsForm['IRT_EOC06']; ?><br />
						Phone: <?php echo $row_rsForm['IRT_EOC07']; ?>
						<br /><br />
						Additional Information: <?php echo $row_rsForm['IRT_EOC08']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- Disaster -->
				
				<?php 
				if($intTableIndex == 12)
				{?>
					 <div class="divQuestionForms">
						<label class="lblQuestion">Question 1:</label>
						<br /><br />
						<label>Please Identify a Claims Coordinator that will work with the Insurance company and adjusters to help with the recovery process.</label>
						<br /><br />
						<label class="lblSubQuestion">Claims Coordinator</label><br />
						<label>Employee 1 Name: <?php echo $row_rsForm['DMT_CLAIM01']; ?><br />
						Title: <?php echo $row_rsForm['DMT_CLAIM02']; ?><br />
						Phone: <?php echo $row_rsForm['DMT_CLAIM03']; ?><br />
						Cell: <?php echo $row_rsForm['DMT_CLAIM04']; ?>
						<br /><br />
						If unavailable:</label>
						<br /><br />
						<label class="lblSubQuestion">Back-Up Claims Coordinator</label><br />
						<label>Employee 2 Name: <?php echo $row_rsForm['DMT_CLAIM05']; ?><br />
						Title: <?php echo $row_rsForm['DMT_CLAIM06']; ?><br />
						Phone: <?php echo $row_rsForm['DMT_CLAIM07']; ?><br />
						Cell: <?php echo $row_rsForm['DMT_CLAIM08']; ?><br /></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 2:</label>
						<br /><br />
						<label>In the event of a disaster the MEDIA may become involved. Please identify a Organizational Spokesmen for your business. This individual should not be the CEO or President, however they must be a significant figure in the management of the business.
						<br /><br />
						Employee Name: <?php echo $row_rsForm['DMT_MED01']; ?><br />
						Job Title: <?php echo $row_rsForm['DMT_MED02']; ?><br />
						Phone: <?php echo $row_rsForm['DMT_MED03']; ?><br />
						Cell: <?php echo $row_rsForm['DMT_MED04']; ?><br />
						E-Mail: <?php echo $row_rsForm['DMT_MED05']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- Damage -->
				
				<?php 
				if($intTableIndex == 13)
				{?>
					<div class="divQuestionForms">
						<label>Please Identify the employee who will be response for leading this team through the recovery process. This person will report all progress and status back to the Disaster Management Team.</label>
						<br /><br />                            
						<label class="lblSubQuestion">Damage Assessment Team Coordinator:</label><br />
						<label>Employee Name: <?php echo $row_rsForm['DAT_EMP01']; ?><br />
						Phone: <?php echo $row_rsForm['DAT_EMP02']; ?><br />
						Cell: <?php echo $row_rsForm['DAT_EMP03']; ?><br />
						E-Mail: <?php echo $row_rsForm['DAT_EMP04']; ?>
						<br /><br />
						If unavailable:</label>
						<br /><br />
						<label class="lblSubQuestion">Back-Up Damage Assessment Team Coordinator:</label><br />
						<label>Employee Name: <?php echo $row_rsForm['DAT_EMP05']; ?><br />
						Phone: <?php echo $row_rsForm['DAT_EMP06']; ?><br />
						Cell: <?php echo $row_rsForm['DAT_EMP07']; ?><br />
						E-Mail: <?php echo $row_rsForm['DAT_EMP08']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- ITRecoveryTeam -->
				
				<?php 
				if($intTableIndex == 14)
				{?>
					<label>Please Identify the employee who will be response for leading this team through the recovery process. This person will report all progress and status back to the Disaster Management Team.</label>
					<br /><br />
					<div class="divQuestionForms">
						<label class="lblSubQuestion">Information Technology Team Coordinator:</label><br />
						<label>Employee Name: <?php echo $row_rsForm['ITR_EMP01']; ?><br />
						Phone: <?php echo $row_rsForm['ITR_EMP02']; ?><br />
						Cell: <?php echo $row_rsForm['ITR_EMP03']; ?><br />
						E-Mail: <?php echo $row_rsForm['ITR_EMP04']; ?>
						<br /><br />
						If unavailable:</label>
						<br /><br />
						<label class="lblSubQuestion">Back-Up Information Technology Team Coordinator:</label><br />
						<label>Employee Name: <?php echo $row_rsForm['ITR_EMP05']; ?><br />
						Phone: <?php echo $row_rsForm['ITR_EMP06']; ?><br />
						Cell: <?php echo $row_rsForm['ITR_EMP07']; ?><br />
						E-Mail: <?php echo $row_rsForm['ITR_EMP08']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- Administration -->
				
				<?php 
				if($intTableIndex == 15)
				{?>
					<label>Please Identify the employee who will be response for leading this team through the recovery process. This person will report all progress and status back to the Disaster Management Team.</label>
					<br /><br />
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 1:</label>
						<br /><br />
						<label class="lblSubQuestion">Administration Recovery Team Coordinator:</label><br />
						<label>Employee Name: <?php echo $row_rsForm['ADMIN_EMP01']; ?><br />
						Phone: <?php echo $row_rsForm['ADMIN_EMP02']; ?><br />
						Cell: <?php echo $row_rsForm['ADMIN_EMP03']; ?><br />
						E-Mail: <?php echo $row_rsForm['ADMIN_EMP04']; ?>
						<br /><br />
						If unavailable:</label>
						<br /><br />
						<label class="lblSubQuestion">Back-Up Administration Recovery Team Coordinator:</label><br />
						<label>Employee Name: <?php echo $row_rsForm['ADMIN_EMP05']; ?><br />
						Phone: <?php echo $row_rsForm['ADMIN_EMP06']; ?><br />
						Cell: <?php echo $row_rsForm['ADMIN_EMP07']; ?><br />
						E-Mail: <?php echo $row_rsForm['ADMIN_EMP08']; ?></label>
					</div>
					<div class="divQuestionForms">
						<label class="lblQuestion">Question 2:</label>
						<br /><br />
						<label>Who at your business knows the procedures and steps involved in completing payroll for your organization. In the event of a disaster they will need to continue to do so even in the worst case scenario.</label>
						<br /><br />
						<label class="lblSubQuestion">Payroll Employee:</label><br />
						<label>Employee Name: <?php echo $row_rsForm['ADMIN_PAY01']; ?><br />
						Title: <?php echo $row_rsForm['ADMIN_PAY02']; ?><br />
						Phone: <?php echo $row_rsForm['ADMIN_PAY03']; ?><br />
						Cell: <?php echo $row_rsForm['ADMIN_PAY04']; ?><br />
						E-Mail: <?php echo $row_rsForm['ADMIN_PAY05']; ?>
						<br /><br />
						If unavailable:</label>
						<br /><br />
						<label class="lblSubQuestion">(Back-Up) Payroll Employee:</label><br />
						<label>Employee Name: <?php echo $row_rsForm['ADMIN_PAY06']; ?><br />
						Title: <?php echo $row_rsForm['ADMIN_PAY07']; ?><br />
						Phone: <?php echo $row_rsForm['ADMIN_PAY08']; ?><br />
						Cell: <?php echo $row_rsForm['ADMIN_PAY09']; ?><br />
						E-Mail: <?php echo $row_rsForm['ADMIN_PAY10']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- Essential -->
				
				<?php 
				if($intTableIndex == 16)
				{?>
					<label>Please Identify the employee who will be response for leading this team through the recovery process. This person will report all progress and status back to the Disaster Management Team.</label>
					<br /><br />
					<div class="divQuestionForms">
						<label class="lblSubQuestion">Essential Functions Team Coordinator:</label><br />
						<label>Employee Name: <?php echo $row_rsForm['EFT_EMP01']; ?><br />
						Phone: <?php echo $row_rsForm['EFT_EMP02']; ?><br />
						Cell: <?php echo $row_rsForm['EFT_EMP03']; ?><br />
						E-Mail: <?php echo $row_rsForm['EFT_EMP04']; ?>
						<br /><br />
						If unavailable:</label>
						<br /><br />
						<label class="lblSubQuestion">Back-Up Essential Functions Team Coordinator:</label><br />
						<label>Employee Name: <?php echo $row_rsForm['EFT_EMP05']; ?><br />
						Phone: <?php echo $row_rsForm['EFT_EMP06']; ?><br />
						Cell: <?php echo $row_rsForm['EFT_EMP07']; ?><br />
						E-Mail: <?php echo $row_rsForm['EFT_EMP08']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- Business -->
				
				<?php 
				if($intTableIndex == 17)
				{?>
					<label>Please Identify the employee who will be response for leading this team through the recovery process. This person will report all progress and status back to the Disaster Management Team.</label>
					<br /><br />
					<div class="divQuestionForms">
						<label class="lblSubQuestion">Business Support Team Coordinator:</label><br />
						<label>Employee Name: <?php echo $row_rsForm['BST_EMP01']; ?><br />
						Phone: <?php echo $row_rsForm['BST_EMP02']; ?><br />
						Cell: <?php echo $row_rsForm['BST_EMP03']; ?><br />
						E-Mail: <?php echo $row_rsForm['BST_EMP04']; ?>
						<br /><br />
						If unavailable:</label>
						<br /><br />
						<label class="lblSubQuestion">Back-Up Business Support Team Coordinator:</label><br />
						<label>Employee Name: <?php echo $row_rsForm['BST_EMP05']; ?><br />
						Phone: <?php echo $row_rsForm['BST_EMP06']; ?><br />
						Cell: <?php echo $row_rsForm['BST_EMP07']; ?><br />
						E-Mail: <?php echo $row_rsForm['BST_EMP08']; ?></label>
					</div>
				<?php }//end of if ?>
				
				<!-- Insurance Inventory -->
				
				<?php 
				if($intTableIndex == 18 && $row_loginFoundUser['Solution'] == 2)
				{
					//does another selection to get the updated data
					mysql_select_db($database_conContinuty, $conContinuty);
					$rsForm2 = mysql_query("SELECT * FROM c2insuranceinventory2 WHERE C2ID= ".$row_rsForm['C2ID'], $conContinuty) or die(mysql_error());
					$row_rsForm2 = mysql_fetch_assoc($rsForm2);
					$totalRows_rsForm2 = mysql_num_rows($rsForm2);
					
					$arrYesNo = array(1 =>"YES","NO");
					$intFileImageIndex = 1;//holds the count of the fileImage on the page in order to give a unqie file name for each file?>
					
					<div align="left">
						<div class="divPageTitle" align="left">
							<label class="lblPageTitle"><?php echo $row_rsPlans['sectionName']; ?></label>
						</div>
					</div>
										   
					<label>This page will allow you to upload photos of your office or place of business. You can use this document to improve the accuracy of your insurance coverage and improve the speed of recovering lost items. 
						<br /><br />
						<strong>Please take picture of the following locations at your place of business:</strong></label>
							<br /><br />
								<label class="lblSubQuestion">Exterior of Office</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_001'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_002']; ?>
								<?php if ($row_rsForm['ii_003'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Front Enterance/Reception</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_004'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_005']; ?>
								<?php if ($row_rsForm['ii_006'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Office Kitchen Area</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_007'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_008']; ?>
								<?php if ($row_rsForm['ii_009'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Board Room/War Room</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_010'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_011']; ?>
								<?php if ($row_rsForm['ii_012'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Office #1</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_013'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_014']; ?>
								<?php if ($row_rsForm['ii_015'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Office #2</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_016'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_017']; ?>
								<?php if ($row_rsForm['ii_018'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Office #3</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_019'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_020']; ?>
								<?php if ($row_rsForm['ii_021'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Office #4</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_022'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_023']; ?>
								<?php if ($row_rsForm['ii_024'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Office #5</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_025'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_026']; ?>
								<?php if ($row_rsForm['ii_027'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Office #6</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_028'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_029']; ?>
								<?php if ($row_rsForm['ii_030'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Office #7</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_031'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_032']; ?>
								<?php if ($row_rsForm['ii_033'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Office #8</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_034'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_035']; ?>
								<?php if ($row_rsForm['ii_036'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Office #9</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_037'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_038']; ?>
								<?php if ($row_rsForm['ii_039'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Office #10</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_040'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_041']; ?>
								<?php if ($row_rsForm['ii_042'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Cubicle Area #1</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_043'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_044']; ?>
								<?php if ($row_rsForm['ii_045'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Cubicle Area #2</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_046'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_047']; ?>
								<?php if ($row_rsForm['ii_048'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Cubicle Area #3</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_049'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_050']; ?>
								<?php if ($row_rsForm['ii_051'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Computer Room</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_052'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_053']; ?>
								<?php if ($row_rsForm['ii_054'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">IT Room/Printer/Fax</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_055'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_056']; ?>
								<?php if ($row_rsForm['ii_057'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Supply/Main. Room</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_058'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_059']; ?>
								<?php if ($row_rsForm['ii_060'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Artwork/Collectables</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_061'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_062']; ?>
								<?php if ($row_rsForm['ii_063'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Misc. Room</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_064'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_065']; ?>
								<?php if ($row_rsForm['ii_066'] == "1") echo " X"; ?> DONE </label>
							<br /><br />
								<label class="lblSubQuestion">Other Room</label>
							<br />
								<label># of Items: <?php echo $row_rsForm['ii_067'];?>
								<br/>
								Total Value: $<?php echo $row_rsForm['ii_068']; ?>
								<?php if ($row_rsForm['ii_069'] == "1") echo " X"; ?> DONE </label>
								<br /><br />
								<label>Total Estimated Value of Office Contents...Total Contents $<?php echo $row_rsForm['ii_070']; ?></label>
						<br /><br />
                        
                        <?php 
						$arrPicArea = array(1 =>"Exterior of Office","Front Enterance/Reception","Office Kitchen Area","Board Room/War Room","Office #1","Office #2","Office #3","Office #4","Office #5","Office #6","Office #7","Office #8","Office #9","Office #10","Cubicle Area #1","Cubicle Area #2","Cubicle Area #3","Computer Room","IT Room/Printer/Fax","Supply/Main. Room","Artwork/Collectables","Misc. Room","Other Room");
						$arrPicField = array(1 =>"Name: ","Catagory: ","Room: ","Purchase Date: ","Receipt: ","Make: ","Model: ","Place Purchased: ","Serial #: ","Estimated Purchase Price: ");
						$intIIFieldIndex = 71;//contorls which field is being used
						$intPicIndex = 1;//contorls which picture to use
						$strFieldName = "0";//holds the field name so that can put 00 for 1 digit and 0 for two digit
						
						//goes around for creates for each of the section of pictures the user has done
						for($intIIIndex = 1;$intIIIndex <= count($arrPicArea);$intIIIndex++)
						{							
							//echo "<div align='left'>SECTION ".$intIIIndex." of 23 ".$arrPicArea[$intIIIndex]."<br /><br /></div>");
							
							echo "<table class=\"tblIIPic\">
									<tr>
										<td>";
							
							//goes around for each photo for this area of ii
							for($intPhotoIndex = 1;$intPhotoIndex <= 4;$intPhotoIndex++)
							{
								/*if($intPicIndex <= 16)
									$pdf->Image("../images/".$UserID."/".$UserID."".$intPicIndex.".jpg",135,50 + $intPicY,50);*/
								
								echo "<table>
									<tr>
										<td class=\"tdIIPicTitle\" colspan=\"2\">";
									
								//checks so that only the firt Photo displays this title
								if($intPhotoIndex == 1)
									echo "<label class=\"lblQuestion\">".strtoupper($arrPicArea[$intIIIndex]).": Upload Photos to this Category</label><br /><br />";
								
								//displays the title of the Photo and the table to hold the data 
								echo "<label class=\"lblSubQuestion\">".$arrPicArea[$intIIIndex]." - PHOTO # ".$intPhotoIndex."</label><br /><br />
								
									</td>
								</tr>
								<tr>
									<td class=\"tdIIPicData\">";
								
								//goes around adding each field to descripted the Picture
								for($intPhotoFieldIndex = 1;$intPhotoFieldIndex <= count($arrPicField);$intPhotoFieldIndex++)
								{
									//checks if the $intPhotoFieldIndex is 5 meaning that is it the Receipt which uses yes and no and not test
									if($intPhotoFieldIndex == 5)
									{
										//checks which rsform to use rsform2 for above 800 and rsform fro the blow 800
										if($intIIFieldIndex >= 800)
											echo "<label>".$arrPicField[$intPhotoFieldIndex].$arrYesNo[$row_rsForm2["ii_".$strFieldName.$intIIFieldIndex]]." </label>";
										else
											echo "<label>".$arrPicField[$intPhotoFieldIndex].$arrYesNo[$row_rsForm["ii_".$strFieldName.$intIIFieldIndex]]." </label>";
									}//end of if
									else
									{
										//checks which rsform to use rsform2 for above 800 and rsform fro the blow 800
										if($intIIFieldIndex >= 800)
											echo "<label>".$arrPicField[$intPhotoFieldIndex].$row_rsForm2["ii_".$strFieldName.$intIIFieldIndex]." </label>";					
										else
											echo "<label>".$arrPicField[$intPhotoFieldIndex].$row_rsForm["ii_".$strFieldName.$intIIFieldIndex]." </label>";
									}//end of else
								
									//checks if the $intPhotoFieldIndex is 3,5,7 or 9 for the last field as it is far from teh rest of the fields
									if($intPhotoFieldIndex == 3 || $intPhotoFieldIndex == 5 || $intPhotoFieldIndex == 7)
										echo "<br />";
									else if($intPhotoFieldIndex == 9)
										echo "<br /><br />";
										
									//adds to the $intIIFieldIndex field to the next field
									$intIIFieldIndex = $intIIFieldIndex + 1;
								
									//checks hold make digit to uses for 1-9 and for 10-99
									 if($intIIFieldIndex <= 9)
									   $strFieldName = "00";
									 else if($intIIFieldIndex <= 99)
										$strFieldName = "0";
									else
										$strFieldName = "";
								}//end of for loop
								
								echo "</td>
								
								<td class=\"tdIIPic\">
								
									<img src=\"../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" />";

								//New line to sperpates the the Photos				
								echo "</td>
									</tr>
										</table><br /><br />";
					
								//adds one to the Pic Index and ont to
								$intPicIndex = $intPicIndex + 1;
								$intFileImageIndex = $intFileImageIndex + 1;
							}//end of for loop
							
							echo "</td></tr></table>";
							
						}//end of for loop?>
						<label>*To save changes made submit must be pressed, if you leave this page without selecting submit all changes will not be saved</label>
				<?php }//end of if ?>
                <?php                
                //adds one to $intTableIndex
                $intTableIndex = $intTableIndex + 1;
            }//end of while?>
        </div>
    </div>
</body>
</html>