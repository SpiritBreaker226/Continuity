<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/EditionTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>My Continuity Plans - Insurance Inventory - Continuity Inc. - Disaster Recovery Solutions</title>
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
<?php 
//checks if there is any data for this user for this form
if ($totalRows_rsForm > 0)
{
	//does another selection to get the updated data
	  mysql_select_db($database_conContinuty, $conContinuty);
	  $rsForm2 = mysql_query("SELECT * FROM c2insuranceinventory2 WHERE C2ID= ".$row_rsForm['C2ID'], $conContinuty) or die("Scope3: ".mysql_error());
	  $row_rsForm2 = mysql_fetch_assoc($rsForm2);
	  $totalRows_rsForm2 = mysql_num_rows($rsForm2);
}//end of if ?>
<!-- InstanceEndEditable -->
<?php 
//forces the pages to come here to process the page
$Form = $_SERVER['PHP_SELF'];

//checks if the fields that need data do have some data in them
if($_POST['hfSubmit'] != '')
{?>
	<!-- InstanceBeginEditable name="TopAddstionalFunction" -->
    <?php 
	//checks if the user wants to do a image uploaded
	if($_POST['hfSave'] == "2")
	{
		$intIndex = 1;//holds the count of the fileImage on the page in order to give a unqie file name for each file
		$intUploadedIndex = 1;//holds the number of times the uploaded process has started this is different from intIndex as it does all 
		//file fields on the page
		$intMessageHandler = 0;//holds the control that tells if any images have been uploaded 

		/*0 = None images have been uploaded (default)
		1 = All images have been uploaded
		2 = Some images have been uploaded */

		$strMessages = "";//holds the different messages that will be display to the user as the uploaded file could have many 
		//messages for each file

		//goes around each file field on the page and trys to uploaded the that file on to the server
		foreach ($_FILES["fileImage"]["error"] as $key => $error) 
		{
			//checks if there is no errror in the uploaded
			if ($error == UPLOAD_ERR_OK) 
			{
				//checks that the number of uploads is below 5
				if($intUploadedIndex < 5)
				{
					$strDir = "../../images/".$UserID."/";
					$strFormat = substr($_FILES['fileImage']['name'][$key],-4);//holds the format of the file

					//checks if the file is a really file and not a file that can damage the server
					if (is_uploaded_file($_FILES['fileImage']['tmp_name'][$key]))
					{
						//check against a regexp for an actual http url and for a valid filename, also extract that filename using a submatch
						if($strFormat != ".jpg" && $strFormat != ".jpeg" && $strFormat != ".gif" && $strFormat != ".png")
						{
							$strMessages .= "Filename you gave to get the picture is invalid, for ".$_FILES['fileImage']['name'][$key]."\\n";
	
							//checks to see the default is still on or that there was a suceessful uploaded but now there was an error
							if($intMessageHandler == 1)
								$intMessageHandler = 2;
//echo "Format ".$intMessageHandler."<br/>";
						}//end of if
						else
						{
							//holds the info of the picture
							list($width, $height, $type, $attr) = getimagesize($UserID.$intIndex.".jpg");

							//check against a regexp for an actual http url and for a valid filename, also extract that filename using a submatch
							/*if($width == 250 && $height == 250)
							{
								$strMessages .= "Dimensions of ".$_FILES['fileImage']['name'][$key]." is too large make the Dimensions below 250x250\\n";

								//checks to see the default is still on or that there was a suceessful uploaded but now there was an error
								if($intMessageHandler == 1)
									$intMessageHandler = 2;
//echo "Scale ".$intMessageHandler."<br/>";
							}//end of if
							else
							{*/
								//checks if the Directory exsit and if not then create one
								if(chdir($strDir) === FALSE)
									mkdir($strDir);
	
								//does the moving of the temp uploaded files to the actully picture name and location
								if (move_uploaded_file($_FILES['fileImage']['tmp_name'][$key], $strDir.$UserID.$intIndex.".jpg"))
								{
									//checks to see if there was errors in one of the other uploads meaning that only some of the uploads have occued
									if($intMessageHandler != 2 && $intUploadedIndex == 1)
										$intMessageHandler = 1;
									else if($intMessageHandler != 1)
										$intMessageHandler = 2;
//echo "Uploaded ".$intMessageHandler."<br/>";
								}//end of if
								else
								{
									$strMessages .= "Picture is not able to uploaded ".$_FILES['fileImage']['name'][$key]."\\n";

									//checks to see the default is still on or that there was a suceessful uploaded but now there was an error
									if($intMessageHandler == 1)
										$intMessageHandler = 2;
//echo "Not Uploaded ".$intMessageHandler."<br/>";
								}//end of else
							//}//end of else
						}//end of else
					}//end of if
					else
					{
						$strMessages .= "Possible file upload attack! for ".$_FILES['fileImage']['name'][$key]."\\n";

						//checks to see the default is still on or that there was a suceessful uploaded but now there was an error
						if($intMessageHandler == 1)
							$intMessageHandler = 2;
//echo "Attack ".$intMessageHandler."<br/>";
					}//end of else
				}//end of if

				//adds one to intUploadedIndex
				$intUploadedIndex = $intUploadedIndex + 1;
			}//end of if
			else if($error != UPLOAD_ERR_NO_FILE)
			{
				//cehcks if the error is the too large error else do the everthing else message
				if($error == 2)
					$strMessages .= "Error in Uploading ".$_FILES['fileImage']['name'][$key]." File Size of 2MB or more is Too Large.\\n";
				else
					$strMessages .= "Error in Uploading ".$_FILES['fileImage']['name'][$key]." Error Code: ".$error."\\n";

				//checks to see the default is still on or that there was a suceessful uploaded but now there was an error
				if($intMessageHandler == 1)
					$intMessageHandler = 2;
//echo "Error ".$intMessageHandler."<br/>";
			}//end of else if

			//breaks out of the loop after four uploads the 5th one does not count as it may be the extra one when the loop goes around
			if($intUploadedIndex == 6)
				break;

			//adds to intIndex
			$intIndex = $intIndex + 1;
		}//end of foreach

		echo "<script type=\"text/javascript\">
			var oldonload=window.onload;//holds any prevs onload function from the js file

			window.onload=function(){
			if(typeof(oldonload)=='function')
				oldonload();";

			//checks if the $intUploadedIndex is 6 meaning the user is trying to upload more then four uploads
			if($intUploadedIndex == 6)
				$strMessages .= "\\n\\nMaximum Uploads of 4 has been reach";

			//finds which message to display none, all, some, or Maxium Uploads
			switch($intMessageHandler)
			{
				case 1:
					echo "alert(\"All Images have been uploaded".$strMessages."\");";
				break;
				case 2:
					echo "alert(\"Some Images have been uploaded\\n\\nThe Error Messages:\\n\\n".$strMessages."\");";
				break;
				case 3:
					echo "alert(\"No Images have been uploaded\\n\\nThe Error Messages:\\n\\n".$strMessages."\");";
				break;
			}//end of switch
		echo "}//end of window.onload=function()</script>";
	}//end of if ?>
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
	<!-- InstanceBeginEditable name="CPUpdate" --><?php mysql_query("UPDATE ".$row_rsPlans['TableName']." SET ii_001 ='".str_replace("'","''",$_POST['ii_001'])."',ii_002 ='".str_replace("'","''",$_POST['ii_002'])."',ii_003 ='".str_replace("'","''",$_POST['ii_003'])."',ii_004 ='".str_replace("'","''",$_POST['ii_004'])."',ii_005 ='".str_replace("'","''",$_POST['ii_005'])."',ii_006 ='".str_replace("'","''",$_POST['ii_006'])."',ii_007 ='".str_replace("'","''",$_POST['ii_007'])."',ii_008 ='".str_replace("'","''",$_POST['ii_008'])."',ii_009 ='".str_replace("'","''",$_POST['ii_009'])."',ii_010 ='".str_replace("'","''",$_POST['ii_010'])."',ii_011 ='".str_replace("'","''",$_POST['ii_011'])."',ii_012 ='".str_replace("'","''",$_POST['ii_012'])."',ii_013 ='".str_replace("'","''",$_POST['ii_013'])."',ii_014 ='".str_replace("'","''",$_POST['ii_014'])."',ii_015 ='".str_replace("'","''",$_POST['ii_015'])."',ii_016 ='".str_replace("'","''",$_POST['ii_016'])."',ii_017 ='".str_replace("'","''",$_POST['ii_017'])."',ii_018 ='".str_replace("'","''",$_POST['ii_018'])."',ii_019 ='".str_replace("'","''",$_POST['ii_019'])."',ii_020 ='".str_replace("'","''",$_POST['ii_020'])."',ii_021 ='".str_replace("'","''",$_POST['ii_021'])."',ii_022 ='".str_replace("'","''",$_POST['ii_022'])."',ii_023 ='".str_replace("'","''",$_POST['ii_023'])."',ii_024 ='".str_replace("'","''",$_POST['ii_024'])."',ii_025 ='".str_replace("'","''",$_POST['ii_025'])."',ii_026 ='".str_replace("'","''",$_POST['ii_026'])."',ii_027 ='".str_replace("'","''",$_POST['ii_027'])."',ii_028 ='".str_replace("'","''",$_POST['ii_028'])."',ii_029 ='".str_replace("'","''",$_POST['ii_029'])."',ii_030 ='".str_replace("'","''",$_POST['ii_030'])."',ii_031 ='".str_replace("'","''",$_POST['ii_031'])."',ii_032 ='".str_replace("'","''",$_POST['ii_032'])."',ii_033 ='".str_replace("'","''",$_POST['ii_033'])."',ii_034 ='".str_replace("'","''",$_POST['ii_034'])."',ii_035 ='".str_replace("'","''",$_POST['ii_035'])."',ii_036 ='".str_replace("'","''",$_POST['ii_036'])."',ii_037 ='".str_replace("'","''",$_POST['ii_037'])."',ii_038 ='".str_replace("'","''",$_POST['ii_038'])."',ii_039 ='".str_replace("'","''",$_POST['ii_039'])."',ii_040 ='".str_replace("'","''",$_POST['ii_040'])."',ii_041 ='".str_replace("'","''",$_POST['ii_041'])."',ii_042 ='".str_replace("'","''",$_POST['ii_042'])."',ii_043 ='".str_replace("'","''",$_POST['ii_043'])."',ii_044 ='".str_replace("'","''",$_POST['ii_044'])."',ii_045 ='".str_replace("'","''",$_POST['ii_045'])."',ii_046 ='".str_replace("'","''",$_POST['ii_046'])."',ii_047 ='".str_replace("'","''",$_POST['ii_047'])."',ii_048 ='".str_replace("'","''",$_POST['ii_048'])."',ii_049 ='".str_replace("'","''",$_POST['ii_049'])."',ii_050 ='".str_replace("'","''",$_POST['ii_050'])."',ii_051 ='".str_replace("'","''",$_POST['ii_051'])."',ii_052 ='".str_replace("'","''",$_POST['ii_052'])."',ii_053 ='".str_replace("'","''",$_POST['ii_053'])."',ii_054 ='".str_replace("'","''",$_POST['ii_054'])."',ii_055 ='".str_replace("'","''",$_POST['ii_055'])."',ii_056 ='".str_replace("'","''",$_POST['ii_056'])."',ii_057 ='".str_replace("'","''",$_POST['ii_057'])."',ii_058 ='".str_replace("'","''",$_POST['ii_058'])."',ii_059 ='".str_replace("'","''",$_POST['ii_059'])."',ii_060 ='".str_replace("'","''",$_POST['ii_060'])."',ii_061 ='".str_replace("'","''",$_POST['ii_061'])."',ii_062 ='".str_replace("'","''",$_POST['ii_062'])."',ii_063 ='".str_replace("'","''",$_POST['ii_063'])."',ii_064 ='".str_replace("'","''",$_POST['ii_064'])."',ii_065 ='".str_replace("'","''",$_POST['ii_065'])."',ii_066 ='".str_replace("'","''",$_POST['ii_066'])."',ii_067 ='".str_replace("'","''",$_POST['ii_067'])."',ii_068 ='".str_replace("'","''",$_POST['ii_068'])."',ii_069 ='".str_replace("'","''",$_POST['ii_069'])."',ii_070 ='".str_replace("'","''",$_POST['ii_070'])."',ii_071 ='".str_replace("'","''",$_POST['ii_071'])."',ii_072 ='".str_replace("'","''",$_POST['ii_072'])."',ii_073 ='".str_replace("'","''",$_POST['ii_073'])."',ii_074 ='".str_replace("'","''",$_POST['ii_074'])."',ii_075 ='".str_replace("'","''",$_POST['ii_075'])."',ii_076 ='".str_replace("'","''",$_POST['ii_076'])."',ii_077 ='".str_replace("'","''",$_POST['ii_077'])."',ii_078 ='".str_replace("'","''",$_POST['ii_078'])."',ii_079 ='".str_replace("'","''",$_POST['ii_079'])."',ii_080 ='".str_replace("'","''",$_POST['ii_080'])."',ii_081 ='".str_replace("'","''",$_POST['ii_081'])."',ii_082 ='".str_replace("'","''",$_POST['ii_082'])."',ii_083 ='".str_replace("'","''",$_POST['ii_083'])."',ii_084 ='".str_replace("'","''",$_POST['ii_084'])."',ii_085 ='".str_replace("'","''",$_POST['ii_085'])."',ii_086 ='".str_replace("'","''",$_POST['ii_086'])."',ii_087 ='".str_replace("'","''",$_POST['ii_087'])."',ii_088 ='".str_replace("'","''",$_POST['ii_088'])."',ii_089 ='".str_replace("'","''",$_POST['ii_089'])."',ii_090 ='".str_replace("'","''",$_POST['ii_090'])."',ii_091 ='".str_replace("'","''",$_POST['ii_091'])."',ii_092 ='".str_replace("'","''",$_POST['ii_092'])."',ii_093 ='".str_replace("'","''",$_POST['ii_093'])."',ii_094 ='".str_replace("'","''",$_POST['ii_094'])."',ii_095 ='".str_replace("'","''",$_POST['ii_095'])."',ii_096 ='".str_replace("'","''",$_POST['ii_096'])."',ii_097 ='".str_replace("'","''",$_POST['ii_097'])."',ii_098 ='".str_replace("'","''",$_POST['ii_098'])."',ii_099 ='".str_replace("'","''",$_POST['ii_099'])."',ii_100 ='".str_replace("'","''",$_POST['ii_100'])."',ii_101 ='".str_replace("'","''",$_POST['ii_101'])."',ii_102 ='".str_replace("'","''",$_POST['ii_102'])."',ii_103 ='".str_replace("'","''",$_POST['ii_103'])."',ii_104 ='".str_replace("'","''",$_POST['ii_104'])."',ii_105 ='".str_replace("'","''",$_POST['ii_105'])."',ii_106 ='".str_replace("'","''",$_POST['ii_106'])."',ii_107 ='".str_replace("'","''",$_POST['ii_107'])."',ii_108 ='".str_replace("'","''",$_POST['ii_108'])."',ii_109 ='".str_replace("'","''",$_POST['ii_109'])."',ii_110 ='".str_replace("'","''",$_POST['ii_110'])."',ii_111 ='".str_replace("'","''",$_POST['ii_111'])."',ii_112 ='".str_replace("'","''",$_POST['ii_112'])."',ii_113 ='".str_replace("'","''",$_POST['ii_113'])."',ii_114 ='".str_replace("'","''",$_POST['ii_114'])."',ii_115 ='".str_replace("'","''",$_POST['ii_115'])."',ii_116 ='".str_replace("'","''",$_POST['ii_116'])."',ii_117 ='".str_replace("'","''",$_POST['ii_117'])."',ii_118 ='".str_replace("'","''",$_POST['ii_118'])."',ii_119 ='".str_replace("'","''",$_POST['ii_119'])."',ii_120 ='".str_replace("'","''",$_POST['ii_120'])."',ii_121 ='".str_replace("'","''",$_POST['ii_121'])."',ii_122 ='".str_replace("'","''",$_POST['ii_122'])."',ii_123 ='".str_replace("'","''",$_POST['ii_123'])."',ii_124 ='".str_replace("'","''",$_POST['ii_124'])."',ii_125 ='".str_replace("'","''",$_POST['ii_125'])."',ii_126 ='".str_replace("'","''",$_POST['ii_126'])."',ii_127 ='".str_replace("'","''",$_POST['ii_127'])."',ii_128 ='".str_replace("'","''",$_POST['ii_128'])."',ii_129 ='".str_replace("'","''",$_POST['ii_129'])."',ii_130 ='".str_replace("'","''",$_POST['ii_130'])."',ii_131 ='".str_replace("'","''",$_POST['ii_131'])."',ii_132 ='".str_replace("'","''",$_POST['ii_132'])."',ii_133 ='".str_replace("'","''",$_POST['ii_133'])."',ii_134 ='".str_replace("'","''",$_POST['ii_134'])."',ii_135 ='".str_replace("'","''",$_POST['ii_135'])."',ii_136 ='".str_replace("'","''",$_POST['ii_136'])."',ii_137 ='".str_replace("'","''",$_POST['ii_137'])."',ii_138 ='".str_replace("'","''",$_POST['ii_138'])."',ii_139 ='".str_replace("'","''",$_POST['ii_139'])."',ii_140 ='".str_replace("'","''",$_POST['ii_140'])."',ii_141 ='".str_replace("'","''",$_POST['ii_141'])."',ii_142 ='".str_replace("'","''",$_POST['ii_142'])."',ii_143 ='".str_replace("'","''",$_POST['ii_143'])."',ii_144 ='".str_replace("'","''",$_POST['ii_144'])."',ii_145 ='".str_replace("'","''",$_POST['ii_145'])."',ii_146 ='".str_replace("'","''",$_POST['ii_146'])."',ii_147 ='".str_replace("'","''",$_POST['ii_147'])."',ii_148 ='".str_replace("'","''",$_POST['ii_148'])."',ii_149 ='".str_replace("'","''",$_POST['ii_149'])."',ii_150 ='".str_replace("'","''",$_POST['ii_150'])."',ii_151 ='".str_replace("'","''",$_POST['ii_151'])."',ii_152 ='".str_replace("'","''",$_POST['ii_152'])."',ii_153 ='".str_replace("'","''",$_POST['ii_153'])."',ii_154 ='".str_replace("'","''",$_POST['ii_154'])."',ii_155 ='".str_replace("'","''",$_POST['ii_155'])."',ii_156 ='".str_replace("'","''",$_POST['ii_156'])."',ii_157 ='".str_replace("'","''",$_POST['ii_157'])."',ii_158 ='".str_replace("'","''",$_POST['ii_158'])."',ii_159 ='".str_replace("'","''",$_POST['ii_159'])."',ii_160 ='".str_replace("'","''",$_POST['ii_160'])."',ii_161 ='".str_replace("'","''",$_POST['ii_161'])."',ii_162 ='".str_replace("'","''",$_POST['ii_162'])."',ii_163 ='".str_replace("'","''",$_POST['ii_163'])."',ii_164 ='".str_replace("'","''",$_POST['ii_164'])."',ii_165 ='".str_replace("'","''",$_POST['ii_165'])."',ii_166 ='".str_replace("'","''",$_POST['ii_166'])."',ii_167 ='".str_replace("'","''",$_POST['ii_167'])."',ii_168 ='".str_replace("'","''",$_POST['ii_168'])."',ii_169 ='".str_replace("'","''",$_POST['ii_169'])."',ii_170 ='".str_replace("'","''",$_POST['ii_170'])."',ii_171 ='".str_replace("'","''",$_POST['ii_171'])."',ii_172 ='".str_replace("'","''",$_POST['ii_172'])."',ii_173 ='".str_replace("'","''",$_POST['ii_173'])."',ii_174 ='".str_replace("'","''",$_POST['ii_174'])."',ii_175 ='".str_replace("'","''",$_POST['ii_175'])."',ii_176 ='".str_replace("'","''",$_POST['ii_176'])."',ii_177 ='".str_replace("'","''",$_POST['ii_177'])."',ii_178 ='".str_replace("'","''",$_POST['ii_178'])."',ii_179 ='".str_replace("'","''",$_POST['ii_179'])."',ii_180 ='".str_replace("'","''",$_POST['ii_180'])."',ii_181 ='".str_replace("'","''",$_POST['ii_181'])."',ii_182 ='".str_replace("'","''",$_POST['ii_182'])."',ii_183 ='".str_replace("'","''",$_POST['ii_183'])."',ii_184 ='".str_replace("'","''",$_POST['ii_184'])."',ii_185 ='".str_replace("'","''",$_POST['ii_185'])."',ii_186 ='".str_replace("'","''",$_POST['ii_186'])."',ii_187 ='".str_replace("'","''",$_POST['ii_187'])."',ii_188 ='".str_replace("'","''",$_POST['ii_188'])."',ii_189 ='".str_replace("'","''",$_POST['ii_189'])."',ii_190 ='".str_replace("'","''",$_POST['ii_190'])."',ii_191 ='".str_replace("'","''",$_POST['ii_191'])."',ii_192 ='".str_replace("'","''",$_POST['ii_192'])."',ii_193 ='".str_replace("'","''",$_POST['ii_193'])."',ii_194 ='".str_replace("'","''",$_POST['ii_194'])."',ii_195 ='".str_replace("'","''",$_POST['ii_195'])."',ii_196 ='".str_replace("'","''",$_POST['ii_196'])."',ii_197 ='".str_replace("'","''",$_POST['ii_197'])."',ii_198 ='".str_replace("'","''",$_POST['ii_198'])."',ii_199 ='".str_replace("'","''",$_POST['ii_199'])."',ii_200 ='".str_replace("'","''",$_POST['ii_200'])."',ii_201 ='".str_replace("'","''",$_POST['ii_201'])."',ii_202 ='".str_replace("'","''",$_POST['ii_202'])."',ii_203 ='".str_replace("'","''",$_POST['ii_203'])."',ii_204 ='".str_replace("'","''",$_POST['ii_204'])."',ii_205 ='".str_replace("'","''",$_POST['ii_205'])."',ii_206 ='".str_replace("'","''",$_POST['ii_206'])."',ii_207 ='".str_replace("'","''",$_POST['ii_207'])."',ii_208 ='".str_replace("'","''",$_POST['ii_208'])."',ii_209 ='".str_replace("'","''",$_POST['ii_209'])."',ii_210 ='".str_replace("'","''",$_POST['ii_210'])."',ii_211 ='".str_replace("'","''",$_POST['ii_211'])."',ii_212 ='".str_replace("'","''",$_POST['ii_212'])."',ii_213 ='".str_replace("'","''",$_POST['ii_213'])."',ii_214 ='".str_replace("'","''",$_POST['ii_214'])."',ii_215 ='".str_replace("'","''",$_POST['ii_215'])."',ii_216 ='".str_replace("'","''",$_POST['ii_216'])."',ii_217 ='".str_replace("'","''",$_POST['ii_217'])."',ii_218 ='".str_replace("'","''",$_POST['ii_218'])."',ii_219 ='".str_replace("'","''",$_POST['ii_219'])."',ii_220 ='".str_replace("'","''",$_POST['ii_220'])."',ii_221 ='".str_replace("'","''",$_POST['ii_221'])."',ii_222 ='".str_replace("'","''",$_POST['ii_222'])."',ii_223 ='".str_replace("'","''",$_POST['ii_223'])."',ii_224 ='".str_replace("'","''",$_POST['ii_224'])."',ii_225 ='".str_replace("'","''",$_POST['ii_225'])."',ii_226 ='".str_replace("'","''",$_POST['ii_226'])."',ii_227 ='".str_replace("'","''",$_POST['ii_227'])."',ii_228 ='".str_replace("'","''",$_POST['ii_228'])."',ii_229 ='".str_replace("'","''",$_POST['ii_229'])."',ii_230 ='".str_replace("'","''",$_POST['ii_230'])."',ii_231 ='".str_replace("'","''",$_POST['ii_231'])."',ii_232 ='".str_replace("'","''",$_POST['ii_232'])."',ii_233 ='".str_replace("'","''",$_POST['ii_233'])."',ii_234 ='".str_replace("'","''",$_POST['ii_234'])."',ii_235 ='".str_replace("'","''",$_POST['ii_235'])."',ii_236 ='".str_replace("'","''",$_POST['ii_236'])."',ii_237 ='".str_replace("'","''",$_POST['ii_237'])."',ii_238 ='".str_replace("'","''",$_POST['ii_238'])."',ii_239 ='".str_replace("'","''",$_POST['ii_239'])."',ii_240 ='".str_replace("'","''",$_POST['ii_240'])."',ii_241 ='".str_replace("'","''",$_POST['ii_241'])."',ii_242 ='".str_replace("'","''",$_POST['ii_242'])."',ii_243 ='".str_replace("'","''",$_POST['ii_243'])."',ii_244 ='".str_replace("'","''",$_POST['ii_244'])."',ii_245 ='".str_replace("'","''",$_POST['ii_245'])."',ii_246 ='".str_replace("'","''",$_POST['ii_246'])."',ii_247 ='".str_replace("'","''",$_POST['ii_247'])."',ii_248 ='".str_replace("'","''",$_POST['ii_248'])."',ii_249 ='".str_replace("'","''",$_POST['ii_249'])."',ii_250 ='".str_replace("'","''",$_POST['ii_250'])."',ii_251 ='".str_replace("'","''",$_POST['ii_251'])."',ii_252 ='".str_replace("'","''",$_POST['ii_252'])."',ii_253 ='".str_replace("'","''",$_POST['ii_253'])."',ii_254 ='".str_replace("'","''",$_POST['ii_254'])."',ii_255 ='".str_replace("'","''",$_POST['ii_255'])."',ii_256 ='".str_replace("'","''",$_POST['ii_256'])."',ii_257 ='".str_replace("'","''",$_POST['ii_257'])."',ii_258 ='".str_replace("'","''",$_POST['ii_258'])."',ii_259 ='".str_replace("'","''",$_POST['ii_259'])."',ii_260 ='".str_replace("'","''",$_POST['ii_260'])."',ii_261 ='".str_replace("'","''",$_POST['ii_261'])."',ii_262 ='".str_replace("'","''",$_POST['ii_262'])."',ii_263 ='".str_replace("'","''",$_POST['ii_263'])."',ii_264 ='".str_replace("'","''",$_POST['ii_264'])."',ii_265 ='".str_replace("'","''",$_POST['ii_265'])."',ii_266 ='".str_replace("'","''",$_POST['ii_266'])."',ii_267 ='".str_replace("'","''",$_POST['ii_267'])."',ii_268 ='".str_replace("'","''",$_POST['ii_268'])."',ii_269 ='".str_replace("'","''",$_POST['ii_269'])."',ii_270 ='".str_replace("'","''",$_POST['ii_270'])."',ii_271 ='".str_replace("'","''",$_POST['ii_271'])."',ii_272 ='".str_replace("'","''",$_POST['ii_272'])."',ii_273 ='".str_replace("'","''",$_POST['ii_273'])."',ii_274 ='".str_replace("'","''",$_POST['ii_274'])."',ii_275 ='".str_replace("'","''",$_POST['ii_275'])."',ii_276 ='".str_replace("'","''",$_POST['ii_276'])."',ii_277 ='".str_replace("'","''",$_POST['ii_277'])."',ii_278 ='".str_replace("'","''",$_POST['ii_278'])."',ii_279 ='".str_replace("'","''",$_POST['ii_279'])."',ii_280 ='".str_replace("'","''",$_POST['ii_280'])."',ii_281 ='".str_replace("'","''",$_POST['ii_281'])."',ii_282 ='".str_replace("'","''",$_POST['ii_282'])."',ii_283 ='".str_replace("'","''",$_POST['ii_283'])."',ii_284 ='".str_replace("'","''",$_POST['ii_284'])."',ii_285 ='".str_replace("'","''",$_POST['ii_285'])."',ii_286 ='".str_replace("'","''",$_POST['ii_286'])."',ii_287 ='".str_replace("'","''",$_POST['ii_287'])."',ii_288 ='".str_replace("'","''",$_POST['ii_288'])."',ii_289 ='".str_replace("'","''",$_POST['ii_289'])."',ii_290 ='".str_replace("'","''",$_POST['ii_290'])."',ii_291 ='".str_replace("'","''",$_POST['ii_291'])."',ii_292 ='".str_replace("'","''",$_POST['ii_292'])."',ii_293 ='".str_replace("'","''",$_POST['ii_293'])."',ii_294 ='".str_replace("'","''",$_POST['ii_294'])."',ii_295 ='".str_replace("'","''",$_POST['ii_295'])."',ii_296 ='".str_replace("'","''",$_POST['ii_296'])."',ii_297 ='".str_replace("'","''",$_POST['ii_297'])."',ii_298 ='".str_replace("'","''",$_POST['ii_298'])."',ii_299 ='".str_replace("'","''",$_POST['ii_299'])."',ii_300 ='".str_replace("'","''",$_POST['ii_300'])."',ii_301 ='".str_replace("'","''",$_POST['ii_301'])."',ii_302 ='".str_replace("'","''",$_POST['ii_302'])."',ii_303 ='".str_replace("'","''",$_POST['ii_303'])."',ii_304 ='".str_replace("'","''",$_POST['ii_304'])."',ii_305 ='".str_replace("'","''",$_POST['ii_305'])."',ii_306 ='".str_replace("'","''",$_POST['ii_306'])."',ii_307 ='".str_replace("'","''",$_POST['ii_307'])."',ii_308 ='".str_replace("'","''",$_POST['ii_308'])."',ii_309 ='".str_replace("'","''",$_POST['ii_309'])."',ii_310 ='".str_replace("'","''",$_POST['ii_310'])."',ii_311 ='".str_replace("'","''",$_POST['ii_311'])."',ii_312 ='".str_replace("'","''",$_POST['ii_312'])."',ii_313 ='".str_replace("'","''",$_POST['ii_313'])."',ii_314 ='".str_replace("'","''",$_POST['ii_314'])."',ii_315 ='".str_replace("'","''",$_POST['ii_315'])."',ii_316 ='".str_replace("'","''",$_POST['ii_316'])."',ii_317 ='".str_replace("'","''",$_POST['ii_317'])."',ii_318 ='".str_replace("'","''",$_POST['ii_318'])."',ii_319 ='".str_replace("'","''",$_POST['ii_319'])."',ii_320 ='".str_replace("'","''",$_POST['ii_320'])."',ii_321 ='".str_replace("'","''",$_POST['ii_321'])."',ii_322 ='".str_replace("'","''",$_POST['ii_322'])."',ii_323 ='".str_replace("'","''",$_POST['ii_323'])."',ii_324 ='".str_replace("'","''",$_POST['ii_324'])."',ii_325 ='".str_replace("'","''",$_POST['ii_325'])."',ii_326 ='".str_replace("'","''",$_POST['ii_326'])."',ii_327 ='".str_replace("'","''",$_POST['ii_327'])."',ii_328 ='".str_replace("'","''",$_POST['ii_328'])."',ii_329 ='".str_replace("'","''",$_POST['ii_329'])."',ii_330 ='".str_replace("'","''",$_POST['ii_330'])."',ii_331 ='".str_replace("'","''",$_POST['ii_331'])."',ii_332 ='".str_replace("'","''",$_POST['ii_332'])."',ii_333 ='".str_replace("'","''",$_POST['ii_333'])."',ii_334 ='".str_replace("'","''",$_POST['ii_334'])."',ii_335 ='".str_replace("'","''",$_POST['ii_335'])."',ii_336 ='".str_replace("'","''",$_POST['ii_336'])."',ii_337 ='".str_replace("'","''",$_POST['ii_337'])."',ii_338 ='".str_replace("'","''",$_POST['ii_338'])."',ii_339 ='".str_replace("'","''",$_POST['ii_339'])."',ii_340 ='".str_replace("'","''",$_POST['ii_340'])."',ii_341 ='".str_replace("'","''",$_POST['ii_341'])."',ii_342 ='".str_replace("'","''",$_POST['ii_342'])."',ii_343 ='".str_replace("'","''",$_POST['ii_343'])."',ii_344 ='".str_replace("'","''",$_POST['ii_344'])."',ii_345 ='".str_replace("'","''",$_POST['ii_345'])."',ii_346 ='".str_replace("'","''",$_POST['ii_346'])."',ii_347 ='".str_replace("'","''",$_POST['ii_347'])."',ii_348 ='".str_replace("'","''",$_POST['ii_348'])."',ii_349 ='".str_replace("'","''",$_POST['ii_349'])."',ii_350 ='".str_replace("'","''",$_POST['ii_350'])."',ii_351 ='".str_replace("'","''",$_POST['ii_351'])."',ii_352 ='".str_replace("'","''",$_POST['ii_352'])."',ii_353 ='".str_replace("'","''",$_POST['ii_353'])."',ii_354 ='".str_replace("'","''",$_POST['ii_354'])."',ii_355 ='".str_replace("'","''",$_POST['ii_355'])."',ii_356 ='".str_replace("'","''",$_POST['ii_356'])."',ii_357 ='".str_replace("'","''",$_POST['ii_357'])."',ii_358 ='".str_replace("'","''",$_POST['ii_358'])."',ii_359 ='".str_replace("'","''",$_POST['ii_359'])."',ii_360 ='".str_replace("'","''",$_POST['ii_360'])."',ii_361 ='".str_replace("'","''",$_POST['ii_361'])."',ii_362 ='".str_replace("'","''",$_POST['ii_362'])."',ii_363 ='".str_replace("'","''",$_POST['ii_363'])."',ii_364 ='".str_replace("'","''",$_POST['ii_364'])."',ii_365 ='".str_replace("'","''",$_POST['ii_365'])."',ii_366 ='".str_replace("'","''",$_POST['ii_366'])."',ii_367 ='".str_replace("'","''",$_POST['ii_367'])."',ii_368 ='".str_replace("'","''",$_POST['ii_368'])."',ii_369 ='".str_replace("'","''",$_POST['ii_369'])."',ii_370 ='".str_replace("'","''",$_POST['ii_370'])."',ii_371 ='".str_replace("'","''",$_POST['ii_371'])."',ii_372 ='".str_replace("'","''",$_POST['ii_372'])."',ii_373 ='".str_replace("'","''",$_POST['ii_373'])."',ii_374 ='".str_replace("'","''",$_POST['ii_374'])."',ii_375 ='".str_replace("'","''",$_POST['ii_375'])."',ii_376 ='".str_replace("'","''",$_POST['ii_376'])."',ii_377 ='".str_replace("'","''",$_POST['ii_377'])."',ii_378 ='".str_replace("'","''",$_POST['ii_378'])."',ii_379 ='".str_replace("'","''",$_POST['ii_379'])."',ii_380 ='".str_replace("'","''",$_POST['ii_380'])."',ii_381 ='".str_replace("'","''",$_POST['ii_381'])."',ii_382 ='".str_replace("'","''",$_POST['ii_382'])."',ii_383 ='".str_replace("'","''",$_POST['ii_383'])."',ii_384 ='".str_replace("'","''",$_POST['ii_384'])."',ii_385 ='".str_replace("'","''",$_POST['ii_385'])."',ii_386 ='".str_replace("'","''",$_POST['ii_386'])."',ii_387 ='".str_replace("'","''",$_POST['ii_387'])."',ii_388 ='".str_replace("'","''",$_POST['ii_388'])."',ii_389 ='".str_replace("'","''",$_POST['ii_389'])."',ii_390 ='".str_replace("'","''",$_POST['ii_390'])."',ii_391 ='".str_replace("'","''",$_POST['ii_391'])."',ii_392 ='".str_replace("'","''",$_POST['ii_392'])."',ii_393 ='".str_replace("'","''",$_POST['ii_393'])."',ii_394 ='".str_replace("'","''",$_POST['ii_394'])."',ii_395 ='".str_replace("'","''",$_POST['ii_395'])."',ii_396 ='".str_replace("'","''",$_POST['ii_396'])."',ii_397 ='".str_replace("'","''",$_POST['ii_397'])."',ii_398 ='".str_replace("'","''",$_POST['ii_398'])."',ii_399 ='".str_replace("'","''",$_POST['ii_399'])."',ii_400 ='".str_replace("'","''",$_POST['ii_400'])."',ii_401 ='".str_replace("'","''",$_POST['ii_401'])."',ii_402 ='".str_replace("'","''",$_POST['ii_402'])."',ii_403 ='".str_replace("'","''",$_POST['ii_403'])."',ii_404 ='".str_replace("'","''",$_POST['ii_404'])."',ii_405 ='".str_replace("'","''",$_POST['ii_405'])."',ii_406 ='".str_replace("'","''",$_POST['ii_406'])."',ii_407 ='".str_replace("'","''",$_POST['ii_407'])."',ii_408 ='".str_replace("'","''",$_POST['ii_408'])."',ii_409 ='".str_replace("'","''",$_POST['ii_409'])."',ii_410 ='".str_replace("'","''",$_POST['ii_410'])."',ii_411 ='".str_replace("'","''",$_POST['ii_411'])."',ii_412 ='".str_replace("'","''",$_POST['ii_412'])."',ii_413 ='".str_replace("'","''",$_POST['ii_413'])."',ii_414 ='".str_replace("'","''",$_POST['ii_414'])."',ii_415 ='".str_replace("'","''",$_POST['ii_415'])."',ii_416 ='".str_replace("'","''",$_POST['ii_416'])."',ii_417 ='".str_replace("'","''",$_POST['ii_417'])."',ii_418 ='".str_replace("'","''",$_POST['ii_418'])."',ii_419 ='".str_replace("'","''",$_POST['ii_419'])."',ii_420 ='".str_replace("'","''",$_POST['ii_420'])."',ii_421 ='".str_replace("'","''",$_POST['ii_421'])."',ii_422 ='".str_replace("'","''",$_POST['ii_422'])."',ii_423 ='".str_replace("'","''",$_POST['ii_423'])."',ii_424 ='".str_replace("'","''",$_POST['ii_424'])."',ii_425 ='".str_replace("'","''",$_POST['ii_425'])."',ii_426 ='".str_replace("'","''",$_POST['ii_426'])."',ii_427 ='".str_replace("'","''",$_POST['ii_427'])."',ii_428 ='".str_replace("'","''",$_POST['ii_428'])."',ii_429 ='".str_replace("'","''",$_POST['ii_429'])."',ii_430 ='".str_replace("'","''",$_POST['ii_430'])."',ii_431 ='".str_replace("'","''",$_POST['ii_431'])."',ii_432 ='".str_replace("'","''",$_POST['ii_432'])."',ii_433 ='".str_replace("'","''",$_POST['ii_433'])."',ii_434 ='".str_replace("'","''",$_POST['ii_434'])."',ii_435 ='".str_replace("'","''",$_POST['ii_435'])."',ii_436 ='".str_replace("'","''",$_POST['ii_436'])."',ii_437 ='".str_replace("'","''",$_POST['ii_437'])."',ii_438 ='".str_replace("'","''",$_POST['ii_438'])."',ii_439 ='".str_replace("'","''",$_POST['ii_439'])."',ii_440 ='".str_replace("'","''",$_POST['ii_440'])."',ii_441 ='".str_replace("'","''",$_POST['ii_441'])."',ii_442 ='".str_replace("'","''",$_POST['ii_442'])."',ii_443 ='".str_replace("'","''",$_POST['ii_443'])."',ii_444 ='".str_replace("'","''",$_POST['ii_444'])."',ii_445 ='".str_replace("'","''",$_POST['ii_445'])."',ii_446 ='".str_replace("'","''",$_POST['ii_446'])."',ii_447 ='".str_replace("'","''",$_POST['ii_447'])."',ii_448 ='".str_replace("'","''",$_POST['ii_448'])."',ii_449 ='".str_replace("'","''",$_POST['ii_449'])."',ii_450 ='".str_replace("'","''",$_POST['ii_450'])."',ii_451 ='".str_replace("'","''",$_POST['ii_451'])."',ii_452 ='".str_replace("'","''",$_POST['ii_452'])."',ii_453 ='".str_replace("'","''",$_POST['ii_453'])."',ii_454 ='".str_replace("'","''",$_POST['ii_454'])."',ii_455 ='".str_replace("'","''",$_POST['ii_455'])."',ii_456 ='".str_replace("'","''",$_POST['ii_456'])."',ii_457 ='".str_replace("'","''",$_POST['ii_457'])."',ii_458 ='".str_replace("'","''",$_POST['ii_458'])."',ii_459 ='".str_replace("'","''",$_POST['ii_459'])."',ii_460 ='".str_replace("'","''",$_POST['ii_460'])."',ii_461 ='".str_replace("'","''",$_POST['ii_461'])."',ii_462 ='".str_replace("'","''",$_POST['ii_462'])."',ii_463 ='".str_replace("'","''",$_POST['ii_463'])."',ii_464 ='".str_replace("'","''",$_POST['ii_464'])."',ii_465 ='".str_replace("'","''",$_POST['ii_465'])."',ii_466 ='".str_replace("'","''",$_POST['ii_466'])."',ii_467 ='".str_replace("'","''",$_POST['ii_467'])."',ii_468 ='".str_replace("'","''",$_POST['ii_468'])."',ii_469 ='".str_replace("'","''",$_POST['ii_469'])."',ii_470 ='".str_replace("'","''",$_POST['ii_470'])."',ii_471 ='".str_replace("'","''",$_POST['ii_471'])."',ii_472 ='".str_replace("'","''",$_POST['ii_472'])."',ii_473 ='".str_replace("'","''",$_POST['ii_473'])."',ii_474 ='".str_replace("'","''",$_POST['ii_474'])."',ii_475 ='".str_replace("'","''",$_POST['ii_475'])."',ii_476 ='".str_replace("'","''",$_POST['ii_476'])."',ii_477 ='".str_replace("'","''",$_POST['ii_477'])."',ii_478 ='".str_replace("'","''",$_POST['ii_478'])."',ii_479 ='".str_replace("'","''",$_POST['ii_479'])."',ii_480 ='".str_replace("'","''",$_POST['ii_480'])."',ii_481 ='".str_replace("'","''",$_POST['ii_481'])."',ii_482 ='".str_replace("'","''",$_POST['ii_482'])."',ii_483 ='".str_replace("'","''",$_POST['ii_483'])."',ii_484 ='".str_replace("'","''",$_POST['ii_484'])."',ii_485 ='".str_replace("'","''",$_POST['ii_485'])."',ii_486 ='".str_replace("'","''",$_POST['ii_486'])."',ii_487 ='".str_replace("'","''",$_POST['ii_487'])."',ii_488 ='".str_replace("'","''",$_POST['ii_488'])."',ii_489 ='".str_replace("'","''",$_POST['ii_489'])."',ii_490 ='".str_replace("'","''",$_POST['ii_490'])."',ii_491 ='".str_replace("'","''",$_POST['ii_491'])."',ii_492 ='".str_replace("'","''",$_POST['ii_492'])."',ii_493 ='".str_replace("'","''",$_POST['ii_493'])."',ii_494 ='".str_replace("'","''",$_POST['ii_494'])."',ii_495 ='".str_replace("'","''",$_POST['ii_495'])."',ii_496 ='".str_replace("'","''",$_POST['ii_496'])."',ii_497 ='".str_replace("'","''",$_POST['ii_497'])."',ii_498 ='".str_replace("'","''",$_POST['ii_498'])."',ii_499 ='".str_replace("'","''",$_POST['ii_499'])."',ii_500 ='".str_replace("'","''",$_POST['ii_500'])."',ii_501 ='".str_replace("'","''",$_POST['ii_501'])."',ii_502 ='".str_replace("'","''",$_POST['ii_502'])."',ii_503 ='".str_replace("'","''",$_POST['ii_503'])."',ii_504 ='".str_replace("'","''",$_POST['ii_504'])."',ii_505 ='".str_replace("'","''",$_POST['ii_505'])."',ii_506 ='".str_replace("'","''",$_POST['ii_506'])."',ii_507 ='".str_replace("'","''",$_POST['ii_507'])."',ii_508 ='".str_replace("'","''",$_POST['ii_508'])."',ii_509 ='".str_replace("'","''",$_POST['ii_509'])."',ii_510 ='".str_replace("'","''",$_POST['ii_510'])."',ii_511 ='".str_replace("'","''",$_POST['ii_511'])."',ii_512 ='".str_replace("'","''",$_POST['ii_512'])."',ii_513 ='".str_replace("'","''",$_POST['ii_513'])."',ii_514 ='".str_replace("'","''",$_POST['ii_514'])."',ii_515 ='".str_replace("'","''",$_POST['ii_515'])."',ii_516 ='".str_replace("'","''",$_POST['ii_516'])."',ii_517 ='".str_replace("'","''",$_POST['ii_517'])."',ii_518 ='".str_replace("'","''",$_POST['ii_518'])."',ii_519 ='".str_replace("'","''",$_POST['ii_519'])."',ii_520 ='".str_replace("'","''",$_POST['ii_520'])."',ii_521 ='".str_replace("'","''",$_POST['ii_521'])."',ii_522 ='".str_replace("'","''",$_POST['ii_522'])."',ii_523 ='".str_replace("'","''",$_POST['ii_523'])."',ii_524 ='".str_replace("'","''",$_POST['ii_524'])."',ii_525 ='".str_replace("'","''",$_POST['ii_525'])."',ii_526 ='".str_replace("'","''",$_POST['ii_526'])."',ii_527 ='".str_replace("'","''",$_POST['ii_527'])."',ii_528 ='".str_replace("'","''",$_POST['ii_528'])."',ii_529 ='".str_replace("'","''",$_POST['ii_529'])."',ii_530 ='".str_replace("'","''",$_POST['ii_530'])."',ii_531 ='".str_replace("'","''",$_POST['ii_531'])."',ii_532 ='".str_replace("'","''",$_POST['ii_532'])."',ii_533 ='".str_replace("'","''",$_POST['ii_533'])."',ii_534 ='".str_replace("'","''",$_POST['ii_534'])."',ii_535 ='".str_replace("'","''",$_POST['ii_535'])."',ii_536 ='".str_replace("'","''",$_POST['ii_536'])."',ii_537 ='".str_replace("'","''",$_POST['ii_537'])."',ii_538 ='".str_replace("'","''",$_POST['ii_538'])."',ii_539 ='".str_replace("'","''",$_POST['ii_539'])."',ii_540 ='".str_replace("'","''",$_POST['ii_540'])."',ii_541 ='".str_replace("'","''",$_POST['ii_541'])."',ii_542 ='".str_replace("'","''",$_POST['ii_542'])."',ii_543 ='".str_replace("'","''",$_POST['ii_543'])."',ii_544 ='".str_replace("'","''",$_POST['ii_544'])."',ii_545 ='".str_replace("'","''",$_POST['ii_545'])."',ii_546 ='".str_replace("'","''",$_POST['ii_546'])."',ii_547 ='".str_replace("'","''",$_POST['ii_547'])."',ii_548 ='".str_replace("'","''",$_POST['ii_548'])."',ii_549 ='".str_replace("'","''",$_POST['ii_549'])."',ii_550 ='".str_replace("'","''",$_POST['ii_550'])."',ii_551 ='".str_replace("'","''",$_POST['ii_551'])."',ii_552 ='".str_replace("'","''",$_POST['ii_552'])."',ii_553 ='".str_replace("'","''",$_POST['ii_553'])."',ii_554 ='".str_replace("'","''",$_POST['ii_554'])."',ii_555 ='".str_replace("'","''",$_POST['ii_555'])."',ii_556 ='".str_replace("'","''",$_POST['ii_556'])."',ii_557 ='".str_replace("'","''",$_POST['ii_557'])."',ii_558 ='".str_replace("'","''",$_POST['ii_558'])."',ii_559 ='".str_replace("'","''",$_POST['ii_559'])."',ii_560 ='".str_replace("'","''",$_POST['ii_560'])."',ii_561 ='".str_replace("'","''",$_POST['ii_561'])."',ii_562 ='".str_replace("'","''",$_POST['ii_562'])."',ii_563 ='".str_replace("'","''",$_POST['ii_563'])."',ii_564 ='".str_replace("'","''",$_POST['ii_564'])."',ii_565 ='".str_replace("'","''",$_POST['ii_565'])."',ii_566 ='".str_replace("'","''",$_POST['ii_566'])."',ii_567 ='".str_replace("'","''",$_POST['ii_567'])."',ii_568 ='".str_replace("'","''",$_POST['ii_568'])."',ii_569 ='".str_replace("'","''",$_POST['ii_569'])."',ii_570 ='".str_replace("'","''",$_POST['ii_570'])."',ii_571 ='".str_replace("'","''",$_POST['ii_571'])."',ii_572 ='".str_replace("'","''",$_POST['ii_572'])."',ii_573 ='".str_replace("'","''",$_POST['ii_573'])."',ii_574 ='".str_replace("'","''",$_POST['ii_574'])."',ii_575 ='".str_replace("'","''",$_POST['ii_575'])."',ii_576 ='".str_replace("'","''",$_POST['ii_576'])."',ii_577 ='".str_replace("'","''",$_POST['ii_577'])."',ii_578 ='".str_replace("'","''",$_POST['ii_578'])."',ii_579 ='".str_replace("'","''",$_POST['ii_579'])."',ii_580 ='".str_replace("'","''",$_POST['ii_580'])."',ii_581 ='".str_replace("'","''",$_POST['ii_581'])."',ii_582 ='".str_replace("'","''",$_POST['ii_582'])."',ii_583 ='".str_replace("'","''",$_POST['ii_583'])."',ii_584 ='".str_replace("'","''",$_POST['ii_584'])."',ii_585 ='".str_replace("'","''",$_POST['ii_585'])."',ii_586 ='".str_replace("'","''",$_POST['ii_586'])."',ii_587 ='".str_replace("'","''",$_POST['ii_587'])."',ii_588 ='".str_replace("'","''",$_POST['ii_588'])."',ii_589 ='".str_replace("'","''",$_POST['ii_589'])."',ii_590 ='".str_replace("'","''",$_POST['ii_590'])."',ii_591 ='".str_replace("'","''",$_POST['ii_591'])."',ii_592 ='".str_replace("'","''",$_POST['ii_592'])."',ii_593 ='".str_replace("'","''",$_POST['ii_593'])."',ii_594 ='".str_replace("'","''",$_POST['ii_594'])."',ii_595 ='".str_replace("'","''",$_POST['ii_595'])."',ii_596 ='".str_replace("'","''",$_POST['ii_596'])."',ii_597 ='".str_replace("'","''",$_POST['ii_597'])."',ii_598 ='".str_replace("'","''",$_POST['ii_598'])."',ii_599 ='".str_replace("'","''",$_POST['ii_599'])."',ii_600 ='".str_replace("'","''",$_POST['ii_600'])."',ii_601 ='".str_replace("'","''",$_POST['ii_601'])."',ii_602 ='".str_replace("'","''",$_POST['ii_602'])."',ii_603 ='".str_replace("'","''",$_POST['ii_603'])."',ii_604 ='".str_replace("'","''",$_POST['ii_604'])."',ii_605 ='".str_replace("'","''",$_POST['ii_605'])."',ii_606 ='".str_replace("'","''",$_POST['ii_606'])."',ii_607 ='".str_replace("'","''",$_POST['ii_607'])."',ii_608 ='".str_replace("'","''",$_POST['ii_608'])."',ii_609 ='".str_replace("'","''",$_POST['ii_609'])."',ii_610 ='".str_replace("'","''",$_POST['ii_610'])."',ii_611 ='".str_replace("'","''",$_POST['ii_611'])."',ii_612 ='".str_replace("'","''",$_POST['ii_612'])."',ii_613 ='".str_replace("'","''",$_POST['ii_613'])."',ii_614 ='".str_replace("'","''",$_POST['ii_614'])."',ii_615 ='".str_replace("'","''",$_POST['ii_615'])."',ii_616 ='".str_replace("'","''",$_POST['ii_616'])."',ii_617 ='".str_replace("'","''",$_POST['ii_617'])."',ii_618 ='".str_replace("'","''",$_POST['ii_618'])."',ii_619 ='".str_replace("'","''",$_POST['ii_619'])."',ii_620 ='".str_replace("'","''",$_POST['ii_620'])."',ii_621 ='".str_replace("'","''",$_POST['ii_621'])."',ii_622 ='".str_replace("'","''",$_POST['ii_622'])."',ii_623 ='".str_replace("'","''",$_POST['ii_623'])."',ii_624 ='".str_replace("'","''",$_POST['ii_624'])."',ii_625 ='".str_replace("'","''",$_POST['ii_625'])."',ii_626 ='".str_replace("'","''",$_POST['ii_626'])."',ii_627 ='".str_replace("'","''",$_POST['ii_627'])."',ii_628 ='".str_replace("'","''",$_POST['ii_628'])."',ii_629 ='".str_replace("'","''",$_POST['ii_629'])."',ii_630 ='".str_replace("'","''",$_POST['ii_630'])."',ii_631 ='".str_replace("'","''",$_POST['ii_631'])."',ii_632 ='".str_replace("'","''",$_POST['ii_632'])."',ii_633 ='".str_replace("'","''",$_POST['ii_633'])."',ii_634 ='".str_replace("'","''",$_POST['ii_634'])."',ii_635 ='".str_replace("'","''",$_POST['ii_635'])."',ii_636 ='".str_replace("'","''",$_POST['ii_636'])."',ii_637 ='".str_replace("'","''",$_POST['ii_637'])."',ii_638 ='".str_replace("'","''",$_POST['ii_638'])."',ii_639 ='".str_replace("'","''",$_POST['ii_639'])."',ii_640 ='".str_replace("'","''",$_POST['ii_640'])."',ii_641 ='".str_replace("'","''",$_POST['ii_641'])."',ii_642 ='".str_replace("'","''",$_POST['ii_642'])."',ii_643 ='".str_replace("'","''",$_POST['ii_643'])."',ii_644 ='".str_replace("'","''",$_POST['ii_644'])."',ii_645 ='".str_replace("'","''",$_POST['ii_645'])."',ii_646 ='".str_replace("'","''",$_POST['ii_646'])."',ii_647 ='".str_replace("'","''",$_POST['ii_647'])."',ii_648 ='".str_replace("'","''",$_POST['ii_648'])."',ii_649 ='".str_replace("'","''",$_POST['ii_649'])."',ii_650 ='".str_replace("'","''",$_POST['ii_650'])."',ii_651 ='".str_replace("'","''",$_POST['ii_651'])."',ii_652 ='".str_replace("'","''",$_POST['ii_652'])."',ii_653 ='".str_replace("'","''",$_POST['ii_653'])."',ii_654 ='".str_replace("'","''",$_POST['ii_654'])."',ii_655 ='".str_replace("'","''",$_POST['ii_655'])."',ii_656 ='".str_replace("'","''",$_POST['ii_656'])."',ii_657 ='".str_replace("'","''",$_POST['ii_657'])."',ii_658 ='".str_replace("'","''",$_POST['ii_658'])."',ii_659 ='".str_replace("'","''",$_POST['ii_659'])."',ii_660 ='".str_replace("'","''",$_POST['ii_660'])."',ii_661 ='".str_replace("'","''",$_POST['ii_661'])."',ii_662 ='".str_replace("'","''",$_POST['ii_662'])."',ii_663 ='".str_replace("'","''",$_POST['ii_663'])."',ii_664 ='".str_replace("'","''",$_POST['ii_664'])."',ii_665 ='".str_replace("'","''",$_POST['ii_665'])."',ii_666 ='".str_replace("'","''",$_POST['ii_666'])."',ii_667 ='".str_replace("'","''",$_POST['ii_667'])."',ii_668 ='".str_replace("'","''",$_POST['ii_668'])."',ii_669 ='".str_replace("'","''",$_POST['ii_669'])."',ii_670 ='".str_replace("'","''",$_POST['ii_670'])."',ii_671 ='".str_replace("'","''",$_POST['ii_671'])."',ii_672 ='".str_replace("'","''",$_POST['ii_672'])."',ii_673 ='".str_replace("'","''",$_POST['ii_673'])."',ii_674 ='".str_replace("'","''",$_POST['ii_674'])."',ii_675 ='".str_replace("'","''",$_POST['ii_675'])."',ii_676 ='".str_replace("'","''",$_POST['ii_676'])."',ii_677 ='".str_replace("'","''",$_POST['ii_677'])."',ii_678 ='".str_replace("'","''",$_POST['ii_678'])."',ii_679 ='".str_replace("'","''",$_POST['ii_679'])."',ii_680 ='".str_replace("'","''",$_POST['ii_680'])."',ii_681 ='".str_replace("'","''",$_POST['ii_681'])."',ii_682 ='".str_replace("'","''",$_POST['ii_682'])."',ii_683 ='".str_replace("'","''",$_POST['ii_683'])."',ii_684 ='".str_replace("'","''",$_POST['ii_684'])."',ii_685 ='".str_replace("'","''",$_POST['ii_685'])."',ii_686 ='".str_replace("'","''",$_POST['ii_686'])."',ii_687 ='".str_replace("'","''",$_POST['ii_687'])."',ii_688 ='".str_replace("'","''",$_POST['ii_688'])."',ii_689 ='".str_replace("'","''",$_POST['ii_689'])."',ii_690 ='".str_replace("'","''",$_POST['ii_690'])."',ii_691 ='".str_replace("'","''",$_POST['ii_691'])."',ii_692 ='".str_replace("'","''",$_POST['ii_692'])."',ii_693 ='".str_replace("'","''",$_POST['ii_693'])."',ii_694 ='".str_replace("'","''",$_POST['ii_694'])."',ii_695 ='".str_replace("'","''",$_POST['ii_695'])."',ii_696 ='".str_replace("'","''",$_POST['ii_696'])."',ii_697 ='".str_replace("'","''",$_POST['ii_697'])."',ii_698 ='".str_replace("'","''",$_POST['ii_698'])."',ii_699 ='".str_replace("'","''",$_POST['ii_699'])."',ii_700 ='".str_replace("'","''",$_POST['ii_700'])."',ii_701 ='".str_replace("'","''",$_POST['ii_701'])."',ii_702 ='".str_replace("'","''",$_POST['ii_702'])."',ii_703 ='".str_replace("'","''",$_POST['ii_703'])."',ii_704 ='".str_replace("'","''",$_POST['ii_704'])."',ii_705 ='".str_replace("'","''",$_POST['ii_705'])."',ii_706 ='".str_replace("'","''",$_POST['ii_706'])."',ii_707 ='".str_replace("'","''",$_POST['ii_707'])."',ii_708 ='".str_replace("'","''",$_POST['ii_708'])."',ii_709 ='".str_replace("'","''",$_POST['ii_709'])."',ii_710 ='".str_replace("'","''",$_POST['ii_710'])."',ii_711 ='".str_replace("'","''",$_POST['ii_711'])."',ii_712 ='".str_replace("'","''",$_POST['ii_712'])."',ii_713 ='".str_replace("'","''",$_POST['ii_713'])."',ii_714 ='".str_replace("'","''",$_POST['ii_714'])."',ii_715 ='".str_replace("'","''",$_POST['ii_715'])."',ii_716 ='".str_replace("'","''",$_POST['ii_716'])."',ii_717 ='".str_replace("'","''",$_POST['ii_717'])."',ii_718 ='".str_replace("'","''",$_POST['ii_718'])."',ii_719 ='".str_replace("'","''",$_POST['ii_719'])."',ii_720 ='".str_replace("'","''",$_POST['ii_720'])."',ii_721 ='".str_replace("'","''",$_POST['ii_721'])."',ii_722 ='".str_replace("'","''",$_POST['ii_722'])."',ii_723 ='".str_replace("'","''",$_POST['ii_723'])."',ii_724 ='".str_replace("'","''",$_POST['ii_724'])."',ii_725 ='".str_replace("'","''",$_POST['ii_725'])."',ii_726 ='".str_replace("'","''",$_POST['ii_726'])."',ii_727 ='".str_replace("'","''",$_POST['ii_727'])."',ii_728 ='".str_replace("'","''",$_POST['ii_728'])."',ii_729 ='".str_replace("'","''",$_POST['ii_729'])."',ii_730 ='".str_replace("'","''",$_POST['ii_730'])."',ii_731 ='".str_replace("'","''",$_POST['ii_731'])."',ii_732 ='".str_replace("'","''",$_POST['ii_732'])."',ii_733 ='".str_replace("'","''",$_POST['ii_733'])."',ii_734 ='".str_replace("'","''",$_POST['ii_734'])."',ii_735 ='".str_replace("'","''",$_POST['ii_735'])."',ii_736 ='".str_replace("'","''",$_POST['ii_736'])."',ii_737 ='".str_replace("'","''",$_POST['ii_737'])."',ii_738 ='".str_replace("'","''",$_POST['ii_738'])."',ii_739 ='".str_replace("'","''",$_POST['ii_739'])."',ii_740 ='".str_replace("'","''",$_POST['ii_740'])."',ii_741 ='".str_replace("'","''",$_POST['ii_741'])."',ii_742 ='".str_replace("'","''",$_POST['ii_742'])."',ii_743 ='".str_replace("'","''",$_POST['ii_743'])."',ii_744 ='".str_replace("'","''",$_POST['ii_744'])."',ii_745 ='".str_replace("'","''",$_POST['ii_745'])."',ii_746 ='".str_replace("'","''",$_POST['ii_746'])."',ii_747 ='".str_replace("'","''",$_POST['ii_747'])."',ii_748 ='".str_replace("'","''",$_POST['ii_748'])."',ii_749 ='".str_replace("'","''",$_POST['ii_749'])."',ii_750 ='".str_replace("'","''",$_POST['ii_750'])."',ii_751 ='".str_replace("'","''",$_POST['ii_751'])."',ii_752 ='".str_replace("'","''",$_POST['ii_752'])."',ii_753 ='".str_replace("'","''",$_POST['ii_753'])."',ii_754 ='".str_replace("'","''",$_POST['ii_754'])."',ii_755 ='".str_replace("'","''",$_POST['ii_755'])."',ii_756 ='".str_replace("'","''",$_POST['ii_756'])."',ii_757 ='".str_replace("'","''",$_POST['ii_757'])."',ii_758 ='".str_replace("'","''",$_POST['ii_758'])."',ii_759 ='".str_replace("'","''",$_POST['ii_759'])."',ii_760 ='".str_replace("'","''",$_POST['ii_760'])."',ii_761 ='".str_replace("'","''",$_POST['ii_761'])."',ii_762 ='".str_replace("'","''",$_POST['ii_762'])."',ii_763 ='".str_replace("'","''",$_POST['ii_763'])."',ii_764 ='".str_replace("'","''",$_POST['ii_764'])."',ii_765 ='".str_replace("'","''",$_POST['ii_765'])."',ii_766 ='".str_replace("'","''",$_POST['ii_766'])."',ii_767 ='".str_replace("'","''",$_POST['ii_767'])."',ii_768 ='".str_replace("'","''",$_POST['ii_768'])."',ii_769 ='".str_replace("'","''",$_POST['ii_769'])."',ii_770 ='".str_replace("'","''",$_POST['ii_770'])."',ii_771 ='".str_replace("'","''",$_POST['ii_771'])."',ii_772 ='".str_replace("'","''",$_POST['ii_772'])."',ii_773 ='".str_replace("'","''",$_POST['ii_773'])."',ii_774 ='".str_replace("'","''",$_POST['ii_774'])."',ii_775 ='".str_replace("'","''",$_POST['ii_775'])."',ii_776 ='".str_replace("'","''",$_POST['ii_776'])."',ii_777 ='".str_replace("'","''",$_POST['ii_777'])."',ii_778 ='".str_replace("'","''",$_POST['ii_778'])."',ii_779 ='".str_replace("'","''",$_POST['ii_779'])."',ii_780 ='".str_replace("'","''",$_POST['ii_780'])."',ii_781 ='".str_replace("'","''",$_POST['ii_781'])."',ii_782 ='".str_replace("'","''",$_POST['ii_782'])."',ii_783 ='".str_replace("'","''",$_POST['ii_783'])."',ii_784 ='".str_replace("'","''",$_POST['ii_784'])."',ii_785 ='".str_replace("'","''",$_POST['ii_785'])."',ii_786 ='".str_replace("'","''",$_POST['ii_786'])."',ii_787 ='".str_replace("'","''",$_POST['ii_787'])."',ii_788 ='".str_replace("'","''",$_POST['ii_788'])."',ii_789 ='".str_replace("'","''",$_POST['ii_789'])."',ii_790 ='".str_replace("'","''",$_POST['ii_790'])."',ii_791 ='".str_replace("'","''",$_POST['ii_791'])."',ii_792 ='".str_replace("'","''",$_POST['ii_792'])."',ii_793 ='".str_replace("'","''",$_POST['ii_793'])."',ii_794 ='".str_replace("'","''",$_POST['ii_794'])."',ii_795 ='".str_replace("'","''",$_POST['ii_795'])."',ii_796 ='".str_replace("'","''",$_POST['ii_796'])."',ii_797 ='".str_replace("'","''",$_POST['ii_797'])."',ii_798 ='".str_replace("'","''",$_POST['ii_798'])."',ii_799 ='".str_replace("'","''",$_POST['ii_799'])."' WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
	
	/*
	
		Because of the insance amount of fields this one form uses it has to go on two different tables as SQL only allows 1000 fields for 
		one table, this is nuts
	
	*/
	
	//does another selection to get the updated data
	mysql_select_db($database_conContinuty, $conContinuty);
	$rsForm = mysql_query("SELECT * FROM ".$row_rsPlans['TableName']." WHERE UserID = ".$UserID, $conContinuty) or die("To insuranceinventory: ".mysql_error());
	$row_rsForm = mysql_fetch_assoc($rsForm);
	$totalRows_rsForm = mysql_num_rows($rsForm);
	
	//checks if there is any data for this user for this form
	if ($totalRows_rsForm2 == 0)
	{
		//creates a row so that that row will now be update by the next statement
		mysql_select_db($database_conContinuty, $conContinuty);
		mysql_query("INSERT INTO c2insuranceinventory2 SET C2ID = ".$row_rsForm['C2ID'], $conContinuty) or die("To insuranceinventory2: ".mysql_error());
	}//end of if
	
	mysql_select_db($database_conContinuty, $conContinuty);
	mysql_query("UPDATE c2insuranceinventory2 SET ii_800 ='".str_replace("'","''",$_POST['ii_800'])."',ii_801 ='".str_replace("'","''",$_POST['ii_801'])."',ii_802 ='".str_replace("'","''",$_POST['ii_802'])."',ii_803 ='".str_replace("'","''",$_POST['ii_803'])."',ii_804 ='".str_replace("'","''",$_POST['ii_804'])."',ii_805 ='".str_replace("'","''",$_POST['ii_805'])."',ii_806 ='".str_replace("'","''",$_POST['ii_806'])."',ii_807 ='".str_replace("'","''",$_POST['ii_807'])."',ii_808 ='".str_replace("'","''",$_POST['ii_808'])."',ii_809 ='".str_replace("'","''",$_POST['ii_809'])."',ii_810 ='".str_replace("'","''",$_POST['ii_810'])."',ii_811 ='".str_replace("'","''",$_POST['ii_811'])."',ii_812 ='".str_replace("'","''",$_POST['ii_812'])."',ii_813 ='".str_replace("'","''",$_POST['ii_813'])."',ii_814 ='".str_replace("'","''",$_POST['ii_814'])."',ii_815 ='".str_replace("'","''",$_POST['ii_815'])."',ii_816 ='".str_replace("'","''",$_POST['ii_816'])."',ii_817 ='".str_replace("'","''",$_POST['ii_817'])."',ii_818 ='".str_replace("'","''",$_POST['ii_818'])."',ii_819 ='".str_replace("'","''",$_POST['ii_819'])."',ii_820 ='".str_replace("'","''",$_POST['ii_820'])."',ii_821 ='".str_replace("'","''",$_POST['ii_821'])."',ii_822 ='".str_replace("'","''",$_POST['ii_822'])."',ii_823 ='".str_replace("'","''",$_POST['ii_823'])."',ii_824 ='".str_replace("'","''",$_POST['ii_824'])."',ii_825 ='".str_replace("'","''",$_POST['ii_825'])."',ii_826 ='".str_replace("'","''",$_POST['ii_826'])."',ii_827 ='".str_replace("'","''",$_POST['ii_827'])."',ii_828 ='".str_replace("'","''",$_POST['ii_828'])."',ii_829 ='".str_replace("'","''",$_POST['ii_829'])."',ii_830 ='".str_replace("'","''",$_POST['ii_830'])."',ii_831 ='".str_replace("'","''",$_POST['ii_831'])."',ii_832 ='".str_replace("'","''",$_POST['ii_832'])."',ii_833 ='".str_replace("'","''",$_POST['ii_833'])."',ii_834 ='".str_replace("'","''",$_POST['ii_834'])."',ii_835 ='".str_replace("'","''",$_POST['ii_835'])."',ii_836 ='".str_replace("'","''",$_POST['ii_836'])."',ii_837 ='".str_replace("'","''",$_POST['ii_837'])."',ii_838 ='".str_replace("'","''",$_POST['ii_838'])."',ii_839 ='".str_replace("'","''",$_POST['ii_839'])."',ii_840 ='".str_replace("'","''",$_POST['ii_840'])."',ii_841 ='".str_replace("'","''",$_POST['ii_841'])."',ii_842 ='".str_replace("'","''",$_POST['ii_842'])."',ii_843 ='".str_replace("'","''",$_POST['ii_843'])."',ii_844 ='".str_replace("'","''",$_POST['ii_844'])."',ii_845 ='".str_replace("'","''",$_POST['ii_845'])."',ii_846 ='".str_replace("'","''",$_POST['ii_846'])."',ii_847 ='".str_replace("'","''",$_POST['ii_847'])."',ii_848 ='".str_replace("'","''",$_POST['ii_848'])."',ii_849 ='".str_replace("'","''",$_POST['ii_849'])."',ii_850 ='".str_replace("'","''",$_POST['ii_850'])."',ii_851 ='".str_replace("'","''",$_POST['ii_851'])."',ii_852 ='".str_replace("'","''",$_POST['ii_852'])."',ii_853 ='".str_replace("'","''",$_POST['ii_853'])."',ii_854 ='".str_replace("'","''",$_POST['ii_854'])."',ii_855 ='".str_replace("'","''",$_POST['ii_855'])."',ii_856 ='".str_replace("'","''",$_POST['ii_856'])."',ii_857 ='".str_replace("'","''",$_POST['ii_857'])."',ii_858 ='".str_replace("'","''",$_POST['ii_858'])."',ii_859 ='".str_replace("'","''",$_POST['ii_859'])."',ii_860 ='".str_replace("'","''",$_POST['ii_860'])."',ii_861 ='".str_replace("'","''",$_POST['ii_861'])."',ii_862 ='".str_replace("'","''",$_POST['ii_862'])."',ii_863 ='".str_replace("'","''",$_POST['ii_863'])."',ii_864 ='".str_replace("'","''",$_POST['ii_864'])."',ii_865 ='".str_replace("'","''",$_POST['ii_865'])."',ii_866 ='".str_replace("'","''",$_POST['ii_866'])."',ii_867 ='".str_replace("'","''",$_POST['ii_867'])."',ii_868 ='".str_replace("'","''",$_POST['ii_868'])."',ii_869 ='".str_replace("'","''",$_POST['ii_869'])."',ii_870 ='".str_replace("'","''",$_POST['ii_870'])."',ii_871 ='".str_replace("'","''",$_POST['ii_871'])."',ii_872 ='".str_replace("'","''",$_POST['ii_872'])."',ii_873 ='".str_replace("'","''",$_POST['ii_873'])."',ii_874 ='".str_replace("'","''",$_POST['ii_874'])."',ii_875 ='".str_replace("'","''",$_POST['ii_875'])."',ii_876 ='".str_replace("'","''",$_POST['ii_876'])."',ii_877 ='".str_replace("'","''",$_POST['ii_877'])."',ii_878 ='".str_replace("'","''",$_POST['ii_878'])."',ii_879 ='".str_replace("'","''",$_POST['ii_879'])."',ii_880 ='".str_replace("'","''",$_POST['ii_880'])."',ii_881 ='".str_replace("'","''",$_POST['ii_881'])."',ii_882 ='".str_replace("'","''",$_POST['ii_882'])."',ii_883 ='".str_replace("'","''",$_POST['ii_883'])."',ii_884 ='".str_replace("'","''",$_POST['ii_884'])."',ii_885 ='".str_replace("'","''",$_POST['ii_885'])."',ii_886 ='".str_replace("'","''",$_POST['ii_886'])."',ii_887 ='".str_replace("'","''",$_POST['ii_887'])."',ii_888 ='".str_replace("'","''",$_POST['ii_888'])."',ii_889 ='".str_replace("'","''",$_POST['ii_889'])."',ii_890 ='".str_replace("'","''",$_POST['ii_890'])."',ii_891 ='".str_replace("'","''",$_POST['ii_891'])."',ii_892 ='".str_replace("'","''",$_POST['ii_892'])."',ii_893 ='".str_replace("'","''",$_POST['ii_893'])."',ii_894 ='".str_replace("'","''",$_POST['ii_894'])."',ii_895 ='".str_replace("'","''",$_POST['ii_895'])."',ii_896 ='".str_replace("'","''",$_POST['ii_896'])."',ii_897 ='".str_replace("'","''",$_POST['ii_897'])."',ii_898 ='".str_replace("'","''",$_POST['ii_898'])."',ii_899 ='".str_replace("'","''",$_POST['ii_899'])."',ii_900 ='".str_replace("'","''",$_POST['ii_900'])."',ii_901 ='".str_replace("'","''",$_POST['ii_901'])."',ii_902 ='".str_replace("'","''",$_POST['ii_902'])."',ii_903 ='".str_replace("'","''",$_POST['ii_903'])."',ii_904 ='".str_replace("'","''",$_POST['ii_904'])."',ii_905 ='".str_replace("'","''",$_POST['ii_905'])."',ii_906 ='".str_replace("'","''",$_POST['ii_906'])."',ii_907 ='".str_replace("'","''",$_POST['ii_907'])."',ii_908 ='".str_replace("'","''",$_POST['ii_908'])."',ii_909 ='".str_replace("'","''",$_POST['ii_909'])."',ii_910 ='".str_replace("'","''",$_POST['ii_910'])."',ii_911 ='".str_replace("'","''",$_POST['ii_911'])."',ii_912 ='".str_replace("'","''",$_POST['ii_912'])."',ii_913 ='".str_replace("'","''",$_POST['ii_913'])."',ii_914 ='".str_replace("'","''",$_POST['ii_914'])."',ii_915 ='".str_replace("'","''",$_POST['ii_915'])."',ii_916 ='".str_replace("'","''",$_POST['ii_916'])."',ii_917 ='".str_replace("'","''",$_POST['ii_917'])."',ii_918 ='".str_replace("'","''",$_POST['ii_918'])."',ii_919 ='".str_replace("'","''",$_POST['ii_919'])."',ii_920 ='".str_replace("'","''",$_POST['ii_920'])."',ii_921 ='".str_replace("'","''",$_POST['ii_921'])."',ii_922 ='".str_replace("'","''",$_POST['ii_922'])."',ii_923 ='".str_replace("'","''",$_POST['ii_923'])."',ii_924 ='".str_replace("'","''",$_POST['ii_924'])."',ii_925 ='".str_replace("'","''",$_POST['ii_925'])."',ii_926 ='".str_replace("'","''",$_POST['ii_926'])."',ii_927 ='".str_replace("'","''",$_POST['ii_927'])."',ii_928 ='".str_replace("'","''",$_POST['ii_928'])."',ii_929 ='".str_replace("'","''",$_POST['ii_929'])."',ii_930 ='".str_replace("'","''",$_POST['ii_930'])."',ii_931 ='".str_replace("'","''",$_POST['ii_931'])."',ii_932 ='".str_replace("'","''",$_POST['ii_932'])."',ii_933 ='".str_replace("'","''",$_POST['ii_933'])."',ii_934 ='".str_replace("'","''",$_POST['ii_934'])."',ii_935 ='".str_replace("'","''",$_POST['ii_935'])."',ii_936 ='".str_replace("'","''",$_POST['ii_936'])."',ii_937 ='".str_replace("'","''",$_POST['ii_937'])."',ii_938 ='".str_replace("'","''",$_POST['ii_938'])."',ii_939 ='".str_replace("'","''",$_POST['ii_939'])."',ii_940 ='".str_replace("'","''",$_POST['ii_940'])."',ii_941 ='".str_replace("'","''",$_POST['ii_941'])."',ii_942 ='".str_replace("'","''",$_POST['ii_942'])."',ii_943 ='".str_replace("'","''",$_POST['ii_943'])."',ii_944 ='".str_replace("'","''",$_POST['ii_944'])."',ii_945 ='".str_replace("'","''",$_POST['ii_945'])."',ii_946 ='".str_replace("'","''",$_POST['ii_946'])."',ii_947 ='".str_replace("'","''",$_POST['ii_947'])."',ii_948 ='".str_replace("'","''",$_POST['ii_948'])."',ii_949 ='".str_replace("'","''",$_POST['ii_949'])."',ii_950 ='".str_replace("'","''",$_POST['ii_950'])."',ii_951 ='".str_replace("'","''",$_POST['ii_951'])."',ii_952 ='".str_replace("'","''",$_POST['ii_952'])."',ii_953 ='".str_replace("'","''",$_POST['ii_953'])."',ii_954 ='".str_replace("'","''",$_POST['ii_954'])."',ii_955 ='".str_replace("'","''",$_POST['ii_955'])."',ii_956 ='".str_replace("'","''",$_POST['ii_956'])."',ii_957 ='".str_replace("'","''",$_POST['ii_957'])."',ii_958 ='".str_replace("'","''",$_POST['ii_958'])."',ii_959 ='".str_replace("'","''",$_POST['ii_959'])."',ii_960 ='".str_replace("'","''",$_POST['ii_960'])."',ii_961 ='".str_replace("'","''",$_POST['ii_961'])."',ii_962 ='".str_replace("'","''",$_POST['ii_962'])."',ii_963 ='".str_replace("'","''",$_POST['ii_963'])."',ii_964 ='".str_replace("'","''",$_POST['ii_964'])."',ii_965 ='".str_replace("'","''",$_POST['ii_965'])."',ii_966 ='".str_replace("'","''",$_POST['ii_966'])."',ii_967 ='".str_replace("'","''",$_POST['ii_967'])."',ii_968 ='".str_replace("'","''",$_POST['ii_968'])."',ii_969 ='".str_replace("'","''",$_POST['ii_969'])."',ii_970 ='".str_replace("'","''",$_POST['ii_970'])."',ii_971 ='".str_replace("'","''",$_POST['ii_971'])."',ii_972 ='".str_replace("'","''",$_POST['ii_972'])."',ii_973 ='".str_replace("'","''",$_POST['ii_973'])."',ii_974 ='".str_replace("'","''",$_POST['ii_974'])."',ii_975 ='".str_replace("'","''",$_POST['ii_975'])."',ii_976 ='".str_replace("'","''",$_POST['ii_976'])."',ii_977 ='".str_replace("'","''",$_POST['ii_977'])."',ii_978 ='".str_replace("'","''",$_POST['ii_978'])."',ii_979 ='".str_replace("'","''",$_POST['ii_979'])."',ii_980 ='".str_replace("'","''",$_POST['ii_980'])."',ii_981 ='".str_replace("'","''",$_POST['ii_981'])."',ii_982 ='".str_replace("'","''",$_POST['ii_982'])."',ii_983 ='".str_replace("'","''",$_POST['ii_983'])."',ii_984 ='".str_replace("'","''",$_POST['ii_984'])."',ii_985 ='".str_replace("'","''",$_POST['ii_985'])."',ii_986 ='".str_replace("'","''",$_POST['ii_986'])."',ii_987 ='".str_replace("'","''",$_POST['ii_987'])."',ii_988 ='".str_replace("'","''",$_POST['ii_988'])."',ii_989 ='".str_replace("'","''",$_POST['ii_989'])."',ii_990 ='".str_replace("'","''",$_POST['ii_990'])."' WHERE C2ID= ".$row_rsForm['C2ID'], $conContinuty) or die(mysql_error()); 
	
	//does another selection to get the updated data
	mysql_select_db($database_conContinuty, $conContinuty);
	$rsForm2 = mysql_query("SELECT * FROM c2insuranceinventory2 WHERE C2ID= ".$row_rsForm['C2ID'], $conContinuty) or die("Scope3: ".mysql_error());
	$row_rsForm2 = mysql_fetch_assoc($rsForm2);
	$totalRows_rsForm2 = mysql_num_rows($rsForm2); ?><!-- InstanceEndEditable -->		    <?php 
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
              <h1><!-- InstanceBeginEditable name="h1Title" -->My Continuity Plans - <?php echo $strEdition; ?> Edition - Insurance Inventory<!-- InstanceEndEditable --></h1>
          	</div><!-- end of Header -->
            <div class="customContent" id="divBasicContent">
                
		<form action="<?php echo $Form; ?>" method="post" id="frmForm" class="frmBasics" enctype="multipart/form-data"> 
              <div class="customContainer" id="divFloatingMenuContainer">
                <div class="customContent" id="divFloatingMenuContent">
                	<div align="left">
			                <!-- InstanceBeginEditable name="PlanContent" --> 
            	        	 <?php $arrYesNo = array(1 =>"YES","NO");
				$intFileImageIndex = 1;//holds the count of the fileImage on the page in order to give a unqie file name for each file?>

				<div align="center">
					<!-- Uploading Image -->
        	                	<div class="divBasicHidden boardBox divBasicFloat" id="divUploadingImage">
						<div class="infoUploading divFloatingMainTitle <?php echo $strBackgroudColor; ?>">
							<label><span class="
                            				<?php
				                        //checks if the Solution is Starnd as the Color will of thise Solution will
							//match the one in the M change to a different color
							if ($row_loginFoundUser['Solution'] == 2)
								echo "lblEnterColor";
							else 
								echo "lblFontRed";?> lblFontSize24">M</span>y Continuity Plans:<br /> <?php echo $strEdition; ?> Edition</label>
						</div>
	                       			<div class="divHiddlenBody" id="divUploadingImageBody">
							<label class="lblFontRed lblFontBold lblFontSize14">Uploading Image... <br/><br/>Please Do Not Click on Back <br/>or Close the Browser</label>
						</div>
					</div><!-- end of Hidden Div -->
				</div>

            	        	<label>This page will allow you to upload photos of your office or place of business. You can use this document to improve the accuracy of your insurance coverage and improve the speed of recovering lost items. 
							<br /><br />
							<strong>Please take picture of the following locations at your place of business:</strong></label>
                            <div class="customContainer divInsuranceTitleContainer">
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Exterior" class="lblFontColorBlack aUnderline">Exterior of Office</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_001">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                           
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_001'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_002" value="<?php echo $row_rsForm['ii_002']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_003" value="1" <?php if ($row_rsForm['ii_003'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Front" class="lblFontColorBlack aUnderline">Front Enterance/Reception</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_004">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_004'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_005" value="<?php echo $row_rsForm['ii_005']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_006" value="1" <?php if ($row_rsForm['ii_006'] == "1") echo " checked"; ?>>  <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Kitchen" class="lblFontColorBlack aUnderline">Office Kitchen Area</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                     <select name="ii_007">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_007'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_008" value="<?php echo $row_rsForm['ii_008']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_009" value="1" <?php if ($row_rsForm['ii_009'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Board" class="lblFontColorBlack aUnderline">Board Room/War Room</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_010">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_010'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_011" value="<?php echo $row_rsForm['ii_011']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_012" value="1" <?php if ($row_rsForm['ii_012'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Office1" class="lblFontColorBlack aUnderline">Office #1</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_013">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_013'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_014" value="<?php echo $row_rsForm['ii_014']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_015" value="1" <?php if ($row_rsForm['ii_015'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Office2" class="lblFontColorBlack aUnderline">Office #2</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_016">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_016'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_017" value="<?php echo $row_rsForm['ii_017']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_018" value="1" <?php if ($row_rsForm['ii_018'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Office3" class="lblFontColorBlack aUnderline">Office #3</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_019">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_019'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_020" value="<?php echo $row_rsForm['ii_020']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_021" value="1" <?php if ($row_rsForm['ii_021'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Office4" class="lblFontColorBlack aUnderline">Office #4</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_022">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_022'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_023" value="<?php echo $row_rsForm['ii_023']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_024" value="1" <?php if ($row_rsForm['ii_024'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Office5" class="lblFontColorBlack aUnderline">Office #5</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_025">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_025'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_026" value="<?php echo $row_rsForm['ii_026']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_027" value="1" <?php if ($row_rsForm['ii_027'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Office6" class="lblFontColorBlack aUnderline">Office #6</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_028">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_028'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_029" value="<?php echo $row_rsForm['ii_029']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_030" value="1" <?php if ($row_rsForm['ii_030'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Office7" class="lblFontColorBlack aUnderline">Office #7</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_031">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_031'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_032" value="<?php echo $row_rsForm['ii_032']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_033" value="1" <?php if ($row_rsForm['ii_033'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Office8" class="lblFontColorBlack aUnderline">Office #8</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_034">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_034'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_035" value="<?php echo $row_rsForm['ii_035']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_036" value="1" <?php if ($row_rsForm['ii_036'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Office9" class="lblFontColorBlack aUnderline">Office #9</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_037">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_037'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_038" value="<?php echo $row_rsForm['ii_038']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_039" value="1" <?php if ($row_rsForm['ii_039'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Office10" class="lblFontColorBlack aUnderline">Office #10</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_040">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_040'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_041" value="<?php echo $row_rsForm['ii_041']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_042" value="1" <?php if ($row_rsForm['ii_042'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Cubicle1" class="lblFontColorBlack aUnderline">Cubicle Area #1</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_043">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_043'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_044" value="<?php echo $row_rsForm['ii_044']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_045" value="1" <?php if ($row_rsForm['ii_045'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Cubicle2" class="lblFontColorBlack aUnderline">Cubicle Area #2</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_046">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_046'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_047" value="<?php echo $row_rsForm['ii_047']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_048" value="1" <?php if ($row_rsForm['ii_048'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Cubicle3" class="lblFontColorBlack aUnderline">Cubicle Area #3</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_049">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_049'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_050" value="<?php echo $row_rsForm['ii_050']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_051" value="1" <?php if ($row_rsForm['ii_051'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Computer" class="lblFontColorBlack aUnderline">Computer Room</a><br />
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_052">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_052'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_053" value="<?php echo $row_rsForm['ii_053']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_054" value="1" <?php if ($row_rsForm['ii_054'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Printer" class="lblFontColorBlack aUnderline">IT Room/Printer/Fax</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                     <select name="ii_055">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_055'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_056" value="<?php echo $row_rsForm['ii_056']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_057" value="1" <?php if ($row_rsForm['ii_057'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Supply" class="lblFontColorBlack aUnderline">Supply/Main. Room</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_058">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_058'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_059" value="<?php echo $row_rsForm['ii_059']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_060" value="1" <?php if ($row_rsForm['ii_060'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                            	<div class="customFooter divInsuranceTitleFooter"></div>
                                <div class="customContent divInsuranceTitleContent">
									<a href="#Collectables" class="lblFontColorBlack aUnderline">Artwork/Collectables</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_061">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_061'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_062" value="<?php echo $row_rsForm['ii_062']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_063" value="1" <?php if ($row_rsForm['ii_063'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Miscellaneous" class="lblFontColorBlack aUnderline">Misc. Room</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_064">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_064'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_065" value="<?php echo $row_rsForm['ii_065']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_066" value="1" <?php if ($row_rsForm['ii_066'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<a href="#Other" class="lblFontColorBlack aUnderline">Other Room</a>
								</div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label># of Items: </label>
                                    <select name="ii_067">
									   <?php $intIndex = 1;//holdes the number of radio button there will be
                            
                                    	//goes around creating the items for the combo box
                                        while($intIndex <> 5)
                                        {
                                            echo "<option value=\"".$intIndex."\"";
                                        
                                            if ($row_rsForm['ii_067'] == $intIndex)
                                                echo " selected >".$intIndex;
                                            else
                                                echo " >".$intIndex;
                            
                                            echo "</option>";
                            
                                            //adds on to $intIndex
                                            $intIndex = $intIndex + 1;
                                        }//end of while loop?>
                                    </select>
                                    <br/><br/>
                                    <label>Total Value: $</label><input type="text" name="ii_068" value="<?php echo $row_rsForm['ii_068']; ?>" size="10" maxlength="20" />
                                    <input type="checkbox" name="ii_069" value="1" <?php if ($row_rsForm['ii_069'] == "1") echo " checked"; ?>> <label>DONE </label>
								</div>
                                <div class="customFooter divInsuranceTitleFooter"></div>
                            	<div class="customContent divInsuranceTitleContent">
									<label>Total Estimated Value of Office Contents</label>                           
                                </div>
                            	<div class="customNavigation divInsuranceTitleNavigation">
									<label>.......................Total Contents $</label><input type="text" name="ii_070" value="<?php echo $row_rsForm['ii_070']; ?>" size="20" maxlength="100" />
                                </div>
				 <div class="customFooter divInsuranceTitleFooter"></div>
				</div>

				<!-- uses with INPUT FILE to determine the size of the file -->
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000">

				<div class="customContainer divInsuranceBodyContainer">
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">			
						<a name="Exterior"></a>
						<label class="lblQuestion">EXTERIOR OF OFFICE: Upload Photos to this Category</label>
						<br /><br />
						<label class="lblSubQuestion">ITEM #1: Exterior of Office </label>
					</div>
					<div class="customContent divInsuranceBodyContent">
						<label>Name: </label><input type="text" name="ii_071" id="ii_071" value="<?php echo $row_rsForm['ii_071']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_072" value="<?php echo $row_rsForm['ii_072']; ?>" size="20" maxlength="100" /><br/>
						<label>Room: </label><input type="text" name="ii_073" value="<?php echo $row_rsForm['ii_073']; ?>" size="20" maxlength="100" /> <label>Purchase Date: </label><input type="text" name="ii_074" value="<?php echo $row_rsForm['ii_074']; ?>" size="20" maxlength="100" /><br/> <label>Receipt: </label>  <?php $intIndex = 1;//holdes the number of radio button there will be
						
						//goes around creating the radio button group
						while($intIndex <> 3)
						{
							echo "<input type=\"radio\" name=\"ii_075\" value=\"".$intIndex."\"";
									
							if ($row_rsForm['ii_075'] == $intIndex)
								echo " checked />".$arrYesNo[$intIndex];
							else
								echo " />".$arrYesNo[$intIndex];
						
							//adds on to $intIndex
							$intIndex = $intIndex + 1;
						}//end of while loop?><br/>
						<label>Make: </label><input type="text" name="ii_076" value="<?php echo $row_rsForm['ii_076']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_077" value="<?php echo $row_rsForm['ii_077']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_078" value="<?php echo $row_rsForm['ii_078']; ?>" size="20" maxlength="100" /><br/>
						<label>Serial #: </label><input type="text" name="ii_079" value="<?php echo $row_rsForm['ii_079']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_080" value="<?php echo $row_rsForm['ii_080']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">						
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Exterior</label>
								<br/><br/>
								<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #2: Exterior of Office</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
						<label>Name: </label><input type="text" name="ii_081" value="<?php echo $row_rsForm['ii_081']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_082" value="<?php echo $row_rsForm['ii_082']; ?>" size="20" maxlength="100" /><br/>
						<label>Room: </label><input type="text" name="ii_083" value="<?php echo $row_rsForm['ii_083']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_084" value="<?php echo $row_rsForm['ii_084']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

						//goes around creating the radio button group
						while($intIndex <> 3)
						{
							echo "<input type=\"radio\" name=\"ii_085\" value=\"".$intIndex."\"";
							
							if ($row_rsForm['ii_085'] == $intIndex)
								echo " checked />".$arrYesNo[$intIndex];
							else
								echo " />".$arrYesNo[$intIndex];
						
							//adds on to $intIndex
							$intIndex = $intIndex + 1;
						}//end of while loop?><br/>
						<label>Make: </label><input type="text" name="ii_086" value="<?php echo $row_rsForm['ii_086']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_087" value="<?php echo $row_rsForm['ii_087']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_088" value="<?php echo $row_rsForm['ii_088']; ?>" size="20" maxlength="100" /><br/>
						<label>Serial #: </label><input type="text" name="ii_089" value="<?php echo $row_rsForm['ii_089']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_090" value="<?php echo $row_rsForm['ii_090']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Exterior</label>
								<br/><br/>
								<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #3: Exterior of Office</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_091" value="<?php echo $row_rsForm['ii_091']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_092" value="<?php echo $row_rsForm['ii_092']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_093" value="<?php echo $row_rsForm['ii_093']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_094" value="<?php echo $row_rsForm['ii_094']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_095\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_095'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_096" value="<?php echo $row_rsForm['ii_096']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_097" value="<?php echo $row_rsForm['ii_097']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_098" value="<?php echo $row_rsForm['ii_098']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_099" value="<?php echo $row_rsForm['ii_099']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_100" value="<?php echo $row_rsForm['ii_100']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Exterior</label>
								<br/><br/>
								<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #4: Exterior of Office</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_101" value="<?php echo $row_rsForm['ii_101']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_102" value="<?php echo $row_rsForm['ii_102']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_103" value="<?php echo $row_rsForm['ii_103']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_104" value="<?php echo $row_rsForm['ii_104']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_105\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_105'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_106" value="<?php echo $row_rsForm['ii_106']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_107" value="<?php echo $row_rsForm['ii_107']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_108" value="<?php echo $row_rsForm['ii_108']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_109" value="<?php echo $row_rsForm['ii_109']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_110" value="<?php echo $row_rsForm['ii_110']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Exterior<label>
								<br/><br/>
								<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Front"></a>
						<label class="lblQuestion">FRONT ENTRENCE/RECEPTION: Upload Photos to this Category</label>
						<br/><br/> 
						<label class="lblSubQuestion">ITEM #1: Front Entrence / Reception</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_111" value="<?php echo $row_rsForm['ii_111']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_112" value="<?php echo $row_rsForm['ii_112']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_113" value="<?php echo $row_rsForm['ii_113']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_114" value="<?php echo $row_rsForm['ii_114']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_115\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_115'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_116" value="<?php echo $row_rsForm['ii_116']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_117" value="<?php echo $row_rsForm['ii_117']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_118" value="<?php echo $row_rsForm['ii_118']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_119" value="<?php echo $row_rsForm['ii_119']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_120" value="<?php echo $row_rsForm['ii_120']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Entrence/Reception</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #2: Front Entrence / Reception</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_121" value="<?php echo $row_rsForm['ii_121']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_122" value="<?php echo $row_rsForm['ii_122']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_123" value="<?php echo $row_rsForm['ii_123']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_124" value="<?php echo $row_rsForm['ii_124']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_125\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_125'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_126" value="<?php echo $row_rsForm['ii_126']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_127" value="<?php echo $row_rsForm['ii_127']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_128" value="<?php echo $row_rsForm['ii_128']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_129" value="<?php echo $row_rsForm['ii_129']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_130" value="<?php echo $row_rsForm['ii_130']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Entrence/Reception</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #3: Front Entrence / Reception</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_131" value="<?php echo $row_rsForm['ii_131']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_132" value="<?php echo $row_rsForm['ii_132']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_133" value="<?php echo $row_rsForm['ii_133']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_134" value="<?php echo $row_rsForm['ii_134']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_135\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_135'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_136" value="<?php echo $row_rsForm['ii_136']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_137" value="<?php echo $row_rsForm['ii_137']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_138" value="<?php echo $row_rsForm['ii_138']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_139" value="<?php echo $row_rsForm['ii_139']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_140" value="<?php echo $row_rsForm['ii_140']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Entrence/Reception
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #4: Front Entrence / Reception </label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_141" value="<?php echo $row_rsForm['ii_141']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_142" value="<?php echo $row_rsForm['ii_142']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_143" value="<?php echo $row_rsForm['ii_143']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_144" value="<?php echo $row_rsForm['ii_144']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_145\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_145'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_146" value="<?php echo $row_rsForm['ii_146']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_147" value="<?php echo $row_rsForm['ii_147']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_148" value="<?php echo $row_rsForm['ii_148']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_149" value="<?php echo $row_rsForm['ii_149']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_150" value="<?php echo $row_rsForm['ii_150']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Entrence/Reception</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Kitchen"></a>
						<label class="lblQuestion">OFFICE KITCHEN AREA: Upload Photos to this Category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: OFFICE KITCHEN AREA</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_151" value="<?php echo $row_rsForm['ii_151']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_152" value="<?php echo $row_rsForm['ii_152']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_153" value="<?php echo $row_rsForm['ii_153']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_154" value="<?php echo $row_rsForm['ii_154']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_155\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_155'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_156" value="<?php echo $row_rsForm['ii_156']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_157" value="<?php echo $row_rsForm['ii_157']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_158" value="<?php echo $row_rsForm['ii_158']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_159" value="<?php echo $row_rsForm['ii_159']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_160" value="<?php echo $row_rsForm['ii_160']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Office Kitchen</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #2: OFFICE KITCHEN AREA</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_161" value="<?php echo $row_rsForm['ii_161']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_162" value="<?php echo $row_rsForm['ii_162']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_163" value="<?php echo $row_rsForm['ii_163']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_164" value="<?php echo $row_rsForm['ii_164']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_165\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_165'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_166" value="<?php echo $row_rsForm['ii_166']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_167" value="<?php echo $row_rsForm['ii_167']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_168" value="<?php echo $row_rsForm['ii_168']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_169" value="<?php echo $row_rsForm['ii_169']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_170" value="<?php echo $row_rsForm['ii_170']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Office Kitchen</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #3: OFFICE KITCHEN AREA</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_171" value="<?php echo $row_rsForm['ii_171']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_172" value="<?php echo $row_rsForm['ii_172']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_173" value="<?php echo $row_rsForm['ii_173']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_174" value="<?php echo $row_rsForm['ii_174']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_175\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_175'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_176" value="<?php echo $row_rsForm['ii_176']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_177" value="<?php echo $row_rsForm['ii_177']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_178" value="<?php echo $row_rsForm['ii_178']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_179" value="<?php echo $row_rsForm['ii_179']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_180" value="<?php echo $row_rsForm['ii_180']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Office Kitchen</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #4: OFFICE KITCHEN AREA</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_181" value="<?php echo $row_rsForm['ii_181']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_182" value="<?php echo $row_rsForm['ii_182']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_183" value="<?php echo $row_rsForm['ii_183']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_184" value="<?php echo $row_rsForm['ii_184']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_185\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_185'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_186" value="<?php echo $row_rsForm['ii_186']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_187" value="<?php echo $row_rsForm['ii_187']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_188" value="<?php echo $row_rsForm['ii_188']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_189" value="<?php echo $row_rsForm['ii_189']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_190" value="<?php echo $row_rsForm['ii_190']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Office Kitchen</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Board"></a>
						<label class="lblQuestion">BOARD ROOM/WAR ROOM : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: BOARD ROOM/WAR ROOM</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_191" value="<?php echo $row_rsForm['ii_191']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_192" value="<?php echo $row_rsForm['ii_192']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_193" value="<?php echo $row_rsForm['ii_193']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_194" value="<?php echo $row_rsForm['ii_194']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_195\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_195'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_196" value="<?php echo $row_rsForm['ii_196']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_197" value="<?php echo $row_rsForm['ii_197']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_198" value="<?php echo $row_rsForm['ii_198']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_199" value="<?php echo $row_rsForm['ii_199']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_200" value="<?php echo $row_rsForm['ii_200']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Board Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #2: BOARD ROOM/WAR ROOM</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_201" value="<?php echo $row_rsForm['ii_201']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_202" value="<?php echo $row_rsForm['ii_202']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_203" value="<?php echo $row_rsForm['ii_203']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_204" value="<?php echo $row_rsForm['ii_204']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_205\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_205'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_206" value="<?php echo $row_rsForm['ii_206']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_207" value="<?php echo $row_rsForm['ii_207']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_208" value="<?php echo $row_rsForm['ii_208']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_209" value="<?php echo $row_rsForm['ii_209']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_210" value="<?php echo $row_rsForm['ii_210']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Board Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #3: BOARD ROOM/WAR ROOM</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_211" value="<?php echo $row_rsForm['ii_211']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_212" value="<?php echo $row_rsForm['ii_212']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_213" value="<?php echo $row_rsForm['ii_213']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_214" value="<?php echo $row_rsForm['ii_214']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_215\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_215'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_216" value="<?php echo $row_rsForm['ii_216']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_217" value="<?php echo $row_rsForm['ii_217']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_218" value="<?php echo $row_rsForm['ii_218']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_219" value="<?php echo $row_rsForm['ii_219']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_220" value="<?php echo $row_rsForm['ii_220']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Board Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #4: BOARD ROOM/WAR ROOM</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_221" value="<?php echo $row_rsForm['ii_221']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_222" value="<?php echo $row_rsForm['ii_222']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_223" value="<?php echo $row_rsForm['ii_223']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_224" value="<?php echo $row_rsForm['ii_224']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_225\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_225'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_226" value="<?php echo $row_rsForm['ii_226']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_227" value="<?php echo $row_rsForm['ii_227']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_228" value="<?php echo $row_rsForm['ii_228']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_229" value="<?php echo $row_rsForm['ii_229']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_230" value="<?php echo $row_rsForm['ii_230']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Board Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Office1"></a>
						<label class="lblQuestion">OFFICE 1 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: OFFICE 1</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_231" value="<?php echo $row_rsForm['ii_231']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_232" value="<?php echo $row_rsForm['ii_232']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_233" value="<?php echo $row_rsForm['ii_233']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_234" value="<?php echo $row_rsForm['ii_234']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_235\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_235'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_236" value="<?php echo $row_rsForm['ii_236']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_237" value="<?php echo $row_rsForm['ii_237']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_238" value="<?php echo $row_rsForm['ii_238']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_239" value="<?php echo $row_rsForm['ii_239']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_240" value="<?php echo $row_rsForm['ii_240']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Office 1</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblFontBold">ITEM #2: OFFICE 1</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_241" value="<?php echo $row_rsForm['ii_241']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_242" value="<?php echo $row_rsForm['ii_242']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_243" value="<?php echo $row_rsForm['ii_243']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_244" value="<?php echo $row_rsForm['ii_244']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_245\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_245'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_246" value="<?php echo $row_rsForm['ii_246']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_247" value="<?php echo $row_rsForm['ii_247']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_248" value="<?php echo $row_rsForm['ii_248']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_249" value="<?php echo $row_rsForm['ii_249']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_250" value="<?php echo $row_rsForm['ii_250']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Office 1</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #3: OFFICE 1</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_251" value="<?php echo $row_rsForm['ii_251']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_252" value="<?php echo $row_rsForm['ii_252']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_253" value="<?php echo $row_rsForm['ii_253']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_254" value="<?php echo $row_rsForm['ii_254']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_255\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_255'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_256" value="<?php echo $row_rsForm['ii_256']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_257" value="<?php echo $row_rsForm['ii_257']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_258" value="<?php echo $row_rsForm['ii_258']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_259" value="<?php echo $row_rsForm['ii_259']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_260" value="<?php echo $row_rsForm['ii_260']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Office 1</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #4: OFFICE 1</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_261" value="<?php echo $row_rsForm['ii_261']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_262" value="<?php echo $row_rsForm['ii_262']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_263" value="<?php echo $row_rsForm['ii_263']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_264" value="<?php echo $row_rsForm['ii_264']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_265\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_265'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_266" value="<?php echo $row_rsForm['ii_266']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_267" value="<?php echo $row_rsForm['ii_267']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_268" value="<?php echo $row_rsForm['ii_268']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_269" value="<?php echo $row_rsForm['ii_269']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_270" value="<?php echo $row_rsForm['ii_270']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Office 1</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Office2"></a>
						<label class="lblQuestion">OFFICE 2 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: OFFICE 2</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_271" value="<?php echo $row_rsForm['ii_271']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_272" value="<?php echo $row_rsForm['ii_272']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_273" value="<?php echo $row_rsForm['ii_273']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_274" value="<?php echo $row_rsForm['ii_274']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_275\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_275'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_276" value="<?php echo $row_rsForm['ii_276']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_277" value="<?php echo $row_rsForm['ii_277']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_278" value="<?php echo $row_rsForm['ii_278']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_279" value="<?php echo $row_rsForm['ii_279']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_280" value="<?php echo $row_rsForm['ii_280']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Office 2</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: OFFICE 2</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_281" value="<?php echo $row_rsForm['ii_281']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_282" value="<?php echo $row_rsForm['ii_282']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_283" value="<?php echo $row_rsForm['ii_283']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_284" value="<?php echo $row_rsForm['ii_284']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_285\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_285'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_286" value="<?php echo $row_rsForm['ii_286']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_287" value="<?php echo $row_rsForm['ii_287']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_288" value="<?php echo $row_rsForm['ii_288']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_289" value="<?php echo $row_rsForm['ii_289']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_290" value="<?php echo $row_rsForm['ii_290']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Office 2</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: OFFICE 2</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_291" value="<?php echo $row_rsForm['ii_291']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_292" value="<?php echo $row_rsForm['ii_292']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_293" value="<?php echo $row_rsForm['ii_293']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_294" value="<?php echo $row_rsForm['ii_294']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_295\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_295'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_296" value="<?php echo $row_rsForm['ii_296']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_297" value="<?php echo $row_rsForm['ii_297']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_298" value="<?php echo $row_rsForm['ii_298']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_299" value="<?php echo $row_rsForm['ii_299']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_300" value="<?php echo $row_rsForm['ii_300']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Office 2</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: OFFICE 2</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_301" value="<?php echo $row_rsForm['ii_301']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_302" value="<?php echo $row_rsForm['ii_302']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_303" value="<?php echo $row_rsForm['ii_303']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_304" value="<?php echo $row_rsForm['ii_304']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_305\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_305'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_306" value="<?php echo $row_rsForm['ii_306']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_307" value="<?php echo $row_rsForm['ii_307']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_308" value="<?php echo $row_rsForm['ii_308']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_309" value="<?php echo $row_rsForm['ii_309']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_310" value="<?php echo $row_rsForm['ii_310']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Office 2</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Office3"></a>
						<label class="lblQuestion">OFFICE 3 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: OFFICE 3</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_311" value="<?php echo $row_rsForm['ii_311']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_312" value="<?php echo $row_rsForm['ii_312']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_313" value="<?php echo $row_rsForm['ii_313']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_314" value="<?php echo $row_rsForm['ii_314']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_315\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_315'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_316" value="<?php echo $row_rsForm['ii_316']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_317" value="<?php echo $row_rsForm['ii_317']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_318" value="<?php echo $row_rsForm['ii_318']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_319" value="<?php echo $row_rsForm['ii_319']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_320" value="<?php echo $row_rsForm['ii_320']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Office 3</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: OFFICE 3</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_321" value="<?php echo $row_rsForm['ii_321']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_322" value="<?php echo $row_rsForm['ii_322']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_323" value="<?php echo $row_rsForm['ii_323']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_324" value="<?php echo $row_rsForm['ii_324']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_325\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_325'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_326" value="<?php echo $row_rsForm['ii_326']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_327" value="<?php echo $row_rsForm['ii_327']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_328" value="<?php echo $row_rsForm['ii_328']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_329" value="<?php echo $row_rsForm['ii_329']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_330" value="<?php echo $row_rsForm['ii_330']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Office 3</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: OFFICE 3</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_331" value="<?php echo $row_rsForm['ii_331']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_332" value="<?php echo $row_rsForm['ii_332']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_333" value="<?php echo $row_rsForm['ii_333']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_334" value="<?php echo $row_rsForm['ii_334']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_335\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_335'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_336" value="<?php echo $row_rsForm['ii_336']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_337" value="<?php echo $row_rsForm['ii_337']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_338" value="<?php echo $row_rsForm['ii_338']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_339" value="<?php echo $row_rsForm['ii_339']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_340" value="<?php echo $row_rsForm['ii_340']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Office 3</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: OFFICE 3</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_341" value="<?php echo $row_rsForm['ii_341']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_342" value="<?php echo $row_rsForm['ii_342']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_343" value="<?php echo $row_rsForm['ii_343']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_344" value="<?php echo $row_rsForm['ii_344']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_345\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_345'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_346" value="<?php echo $row_rsForm['ii_346']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_347" value="<?php echo $row_rsForm['ii_347']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_348" value="<?php echo $row_rsForm['ii_348']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_349" value="<?php echo $row_rsForm['ii_349']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_350" value="<?php echo $row_rsForm['ii_350']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Office 3</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Office4"></a>
						<label class="lblQuestion">OFFICE 4 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: OFFICE 4</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_351" value="<?php echo $row_rsForm['ii_351']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_352" value="<?php echo $row_rsForm['ii_352']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_353" value="<?php echo $row_rsForm['ii_353']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_354" value="<?php echo $row_rsForm['ii_354']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_355\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_355'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_356" value="<?php echo $row_rsForm['ii_356']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_357" value="<?php echo $row_rsForm['ii_357']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_358" value="<?php echo $row_rsForm['ii_358']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_359" value="<?php echo $row_rsForm['ii_359']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_360" value="<?php echo $row_rsForm['ii_360']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Office 4</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: OFFICE 4</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_361" value="<?php echo $row_rsForm['ii_361']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_362" value="<?php echo $row_rsForm['ii_362']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_363" value="<?php echo $row_rsForm['ii_363']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_364" value="<?php echo $row_rsForm['ii_364']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_365\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_365'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_366" value="<?php echo $row_rsForm['ii_366']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_367" value="<?php echo $row_rsForm['ii_367']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_368" value="<?php echo $row_rsForm['ii_368']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_369" value="<?php echo $row_rsForm['ii_369']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_370" value="<?php echo $row_rsForm['ii_370']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Office 4</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: OFFICE 4</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_371" value="<?php echo $row_rsForm['ii_371']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_372" value="<?php echo $row_rsForm['ii_372']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_373" value="<?php echo $row_rsForm['ii_373']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_374" value="<?php echo $row_rsForm['ii_374']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_375\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_375'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_376" value="<?php echo $row_rsForm['ii_376']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_377" value="<?php echo $row_rsForm['ii_377']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_378" value="<?php echo $row_rsForm['ii_378']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_379" value="<?php echo $row_rsForm['ii_379']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_380" value="<?php echo $row_rsForm['ii_380']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Office 4</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: OFFICE 4</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_381" value="<?php echo $row_rsForm['ii_381']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_382" value="<?php echo $row_rsForm['ii_382']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_383" value="<?php echo $row_rsForm['ii_383']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_384" value="<?php echo $row_rsForm['ii_384']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_385\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_385'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_386" value="<?php echo $row_rsForm['ii_386']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_387" value="<?php echo $row_rsForm['ii_387']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_388" value="<?php echo $row_rsForm['ii_388']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_389" value="<?php echo $row_rsForm['ii_389']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_390" value="<?php echo $row_rsForm['ii_390']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Office 4</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Office5"></a>
						<label class="lblQuestion">OFFICE 5 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: OFFICE 5</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_391" value="<?php echo $row_rsForm['ii_391']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_392" value="<?php echo $row_rsForm['ii_392']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_393" value="<?php echo $row_rsForm['ii_393']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_394" value="<?php echo $row_rsForm['ii_394']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_395\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_395'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_396" value="<?php echo $row_rsForm['ii_396']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_397" value="<?php echo $row_rsForm['ii_397']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_398" value="<?php echo $row_rsForm['ii_398']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_399" value="<?php echo $row_rsForm['ii_399']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_400" value="<?php echo $row_rsForm['ii_400']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Office 5</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: OFFICE 5</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_401" value="<?php echo $row_rsForm['ii_401']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_402" value="<?php echo $row_rsForm['ii_402']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_403" value="<?php echo $row_rsForm['ii_403']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_404" value="<?php echo $row_rsForm['ii_404']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_405\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_405'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_406" value="<?php echo $row_rsForm['ii_406']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_407" value="<?php echo $row_rsForm['ii_407']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_408" value="<?php echo $row_rsForm['ii_408']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_409" value="<?php echo $row_rsForm['ii_409']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_410" value="<?php echo $row_rsForm['ii_410']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Office 5</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: OFFICE 5</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_411" value="<?php echo $row_rsForm['ii_411']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_412" value="<?php echo $row_rsForm['ii_412']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_413" value="<?php echo $row_rsForm['ii_413']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_414" value="<?php echo $row_rsForm['ii_414']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_415\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_415'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_416" value="<?php echo $row_rsForm['ii_416']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_417" value="<?php echo $row_rsForm['ii_417']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_418" value="<?php echo $row_rsForm['ii_418']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_419" value="<?php echo $row_rsForm['ii_419']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_420" value="<?php echo $row_rsForm['ii_420']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Office 5</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: OFFICE 5</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_421" value="<?php echo $row_rsForm['ii_421']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_422" value="<?php echo $row_rsForm['ii_422']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_423" value="<?php echo $row_rsForm['ii_423']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_424" value="<?php echo $row_rsForm['ii_424']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_425\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_425'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_426" value="<?php echo $row_rsForm['ii_426']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_427" value="<?php echo $row_rsForm['ii_427']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_428" value="<?php echo $row_rsForm['ii_428']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_429" value="<?php echo $row_rsForm['ii_429']; ?>" size="20" maxlength="100" />><br/><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_430" value="<?php echo $row_rsForm['ii_430']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Office 5</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Office6"></a>
						<label class="lblQuestion">OFFICE 6 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: OFFICE 6</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_431" value="<?php echo $row_rsForm['ii_431']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_432" value="<?php echo $row_rsForm['ii_432']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_433" value="<?php echo $row_rsForm['ii_433']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_434" value="<?php echo $row_rsForm['ii_434']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_435\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_435'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_436" value="<?php echo $row_rsForm['ii_436']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_437" value="<?php echo $row_rsForm['ii_437']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_438" value="<?php echo $row_rsForm['ii_438']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_439" value="<?php echo $row_rsForm['ii_439']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_440" value="<?php echo $row_rsForm['ii_440']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Office 6</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: OFFICE 6</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_441" value="<?php echo $row_rsForm['ii_441']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_442" value="<?php echo $row_rsForm['ii_442']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_443" value="<?php echo $row_rsForm['ii_443']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_444" value="<?php echo $row_rsForm['ii_444']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_445\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_445'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_446" value="<?php echo $row_rsForm['ii_446']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_447" value="<?php echo $row_rsForm['ii_447']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_448" value="<?php echo $row_rsForm['ii_448']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_449" value="<?php echo $row_rsForm['ii_449']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_450" value="<?php echo $row_rsForm['ii_450']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Office 6</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: OFFICE 6</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_451" value="<?php echo $row_rsForm['ii_451']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_452" value="<?php echo $row_rsForm['ii_452']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_453" value="<?php echo $row_rsForm['ii_453']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_454" value="<?php echo $row_rsForm['ii_454']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_455\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_455'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_456" value="<?php echo $row_rsForm['ii_456']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_457" value="<?php echo $row_rsForm['ii_457']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_458" value="<?php echo $row_rsForm['ii_458']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_459" value="<?php echo $row_rsForm['ii_459']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_460" value="<?php echo $row_rsForm['ii_460']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Office 6</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: OFFICE 6</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_461" value="<?php echo $row_rsForm['ii_461']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_462" value="<?php echo $row_rsForm['ii_462']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_463" value="<?php echo $row_rsForm['ii_463']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_464" value="<?php echo $row_rsForm['ii_464']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_465\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_465'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_466" value="<?php echo $row_rsForm['ii_466']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_467" value="<?php echo $row_rsForm['ii_467']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_468" value="<?php echo $row_rsForm['ii_468']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_469" value="<?php echo $row_rsForm['ii_469']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_470" value="<?php echo $row_rsForm['ii_470']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Office 6</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Office7"></a>
						<label class="lblQuestion">OFFICE 7 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: OFFICE 7</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_471" value="<?php echo $row_rsForm['ii_471']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_472" value="<?php echo $row_rsForm['ii_472']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_473" value="<?php echo $row_rsForm['ii_473']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_474" value="<?php echo $row_rsForm['ii_474']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_475\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_475'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>									
									<label>Make: </label><input type="text" name="ii_476" value="<?php echo $row_rsForm['ii_476']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_477" value="<?php echo $row_rsForm['ii_477']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_478" value="<?php echo $row_rsForm['ii_478']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_479" value="<?php echo $row_rsForm['ii_479']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_480" value="<?php echo $row_rsForm['ii_480']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Office 7</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: OFFICE 7</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_481" value="<?php echo $row_rsForm['ii_481']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_482" value="<?php echo $row_rsForm['ii_482']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_483" value="<?php echo $row_rsForm['ii_483']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_484" value="<?php echo $row_rsForm['ii_484']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_485\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_485'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_486" value="<?php echo $row_rsForm['ii_486']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_487" value="<?php echo $row_rsForm['ii_487']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_488" value="<?php echo $row_rsForm['ii_488']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_489" value="<?php echo $row_rsForm['ii_489']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_490" value="<?php echo $row_rsForm['ii_490']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Office 7</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: OFFICE 7</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_491" value="<?php echo $row_rsForm['ii_491']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_492" value="<?php echo $row_rsForm['ii_492']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_493" value="<?php echo $row_rsForm['ii_493']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_494" value="<?php echo $row_rsForm['ii_494']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_495\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_495'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_496" value="<?php echo $row_rsForm['ii_496']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_497" value="<?php echo $row_rsForm['ii_497']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_498" value="<?php echo $row_rsForm['ii_498']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_499" value="<?php echo $row_rsForm['ii_499']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_500" value="<?php echo $row_rsForm['ii_500']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Office 7</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: OFFICE 7</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_501" value="<?php echo $row_rsForm['ii_501']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_502" value="<?php echo $row_rsForm['ii_502']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_503" value="<?php echo $row_rsForm['ii_503']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_504" value="<?php echo $row_rsForm['ii_504']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_505\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_505'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_506" value="<?php echo $row_rsForm['ii_506']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_507" value="<?php echo $row_rsForm['ii_507']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_508" value="<?php echo $row_rsForm['ii_508']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_509" value="<?php echo $row_rsForm['ii_509']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_510" value="<?php echo $row_rsForm['ii_510']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Office 7</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Office8"></a>
						<label class="lblQuestion">OFFICE 8 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: OFFICE 8</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_511" value="<?php echo $row_rsForm['ii_511']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_512" value="<?php echo $row_rsForm['ii_512']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_513" value="<?php echo $row_rsForm['ii_513']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_514" value="<?php echo $row_rsForm['ii_514']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_515\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_515'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_516" value="<?php echo $row_rsForm['ii_516']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_517" value="<?php echo $row_rsForm['ii_517']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_518" value="<?php echo $row_rsForm['ii_518']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_519" value="<?php echo $row_rsForm['ii_519']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_520" value="<?php echo $row_rsForm['ii_520']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Office 8</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: OFFICE 8</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_521" value="<?php echo $row_rsForm['ii_521']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_522" value="<?php echo $row_rsForm['ii_522']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_523" value="<?php echo $row_rsForm['ii_523']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_524" value="<?php echo $row_rsForm['ii_524']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_525\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_525'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_526" value="<?php echo $row_rsForm['ii_526']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_527" value="<?php echo $row_rsForm['ii_527']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_528" value="<?php echo $row_rsForm['ii_528']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_529" value="<?php echo $row_rsForm['ii_529']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_530" value="<?php echo $row_rsForm['ii_530']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Office 8</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: OFFICE 8</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_531" value="<?php echo $row_rsForm['ii_531']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_532" value="<?php echo $row_rsForm['ii_532']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_533" value="<?php echo $row_rsForm['ii_533']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_534" value="<?php echo $row_rsForm['ii_534']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_535\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_535'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_536" value="<?php echo $row_rsForm['ii_536']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_537" value="<?php echo $row_rsForm['ii_537']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_538" value="<?php echo $row_rsForm['ii_538']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_539" value="<?php echo $row_rsForm['ii_539']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_540" value="<?php echo $row_rsForm['ii_540']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Office 8</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: OFFICE 8</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_541" value="<?php echo $row_rsForm['ii_541']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_542" value="<?php echo $row_rsForm['ii_542']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_543" value="<?php echo $row_rsForm['ii_543']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_544" value="<?php echo $row_rsForm['ii_544']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_545\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_545'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_546" value="<?php echo $row_rsForm['ii_546']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_547" value="<?php echo $row_rsForm['ii_547']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_548" value="<?php echo $row_rsForm['ii_548']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_549" value="<?php echo $row_rsForm['ii_549']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_550" value="<?php echo $row_rsForm['ii_550']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Office 8</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Office9"></a>
						<label class="lblQuestion">OFFICE 9 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: OFFICE 9</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_551" value="<?php echo $row_rsForm['ii_551']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_552" value="<?php echo $row_rsForm['ii_552']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_553" value="<?php echo $row_rsForm['ii_553']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_554" value="<?php echo $row_rsForm['ii_554']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_555\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_555'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_556" value="<?php echo $row_rsForm['ii_556']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_557" value="<?php echo $row_rsForm['ii_557']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_558" value="<?php echo $row_rsForm['ii_558']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_559" value="<?php echo $row_rsForm['ii_559']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_560" value="<?php echo $row_rsForm['ii_560']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Office 9</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: OFFICE 9</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_561" value="<?php echo $row_rsForm['ii_561']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_562" value="<?php echo $row_rsForm['ii_562']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_563" value="<?php echo $row_rsForm['ii_563']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_564" value="<?php echo $row_rsForm['ii_564']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_565\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_565'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_566" value="<?php echo $row_rsForm['ii_566']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_567" value="<?php echo $row_rsForm['ii_567']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_568" value="<?php echo $row_rsForm['ii_568']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_569" value="<?php echo $row_rsForm['ii_569']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_570" value="<?php echo $row_rsForm['ii_570']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Office 9</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: OFFICE 9</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_571" value="<?php echo $row_rsForm['ii_571']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_572" value="<?php echo $row_rsForm['ii_572']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_573" value="<?php echo $row_rsForm['ii_573']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_574" value="<?php echo $row_rsForm['ii_574']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_575\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_575'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_576" value="<?php echo $row_rsForm['ii_576']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_577" value="<?php echo $row_rsForm['ii_577']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_578" value="<?php echo $row_rsForm['ii_578']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_579" value="<?php echo $row_rsForm['ii_579']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_580" value="<?php echo $row_rsForm['ii_580']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Office 9</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: OFFICE 9</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_581" value="<?php echo $row_rsForm['ii_581']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_582" value="<?php echo $row_rsForm['ii_582']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_583" value="<?php echo $row_rsForm['ii_583']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_584" value="<?php echo $row_rsForm['ii_584']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_585\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_585'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_586" value="<?php echo $row_rsForm['ii_586']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_587" value="<?php echo $row_rsForm['ii_587']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_588" value="<?php echo $row_rsForm['ii_588']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_589" value="<?php echo $row_rsForm['ii_589']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_590" value="<?php echo $row_rsForm['ii_590']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Office 9</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Office10"></a>
						<label class="lblQuestion">OFFICE 10 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: OFFICE 10</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_591" value="<?php echo $row_rsForm['ii_591']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_592" value="<?php echo $row_rsForm['ii_592']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_593" value="<?php echo $row_rsForm['ii_593']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_594" value="<?php echo $row_rsForm['ii_594']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_595\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_595'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_596" value="<?php echo $row_rsForm['ii_596']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_597" value="<?php echo $row_rsForm['ii_597']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_598" value="<?php echo $row_rsForm['ii_598']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_599" value="<?php echo $row_rsForm['ii_599']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_600" value="<?php echo $row_rsForm['ii_600']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Office 10</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: OFFICE 10</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_601" value="<?php echo $row_rsForm['ii_601']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_602" value="<?php echo $row_rsForm['ii_602']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_603" value="<?php echo $row_rsForm['ii_603']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_604" value="<?php echo $row_rsForm['ii_604']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_605\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_605'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_606" value="<?php echo $row_rsForm['ii_606']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_607" value="<?php echo $row_rsForm['ii_607']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_608" value="<?php echo $row_rsForm['ii_608']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_609" value="<?php echo $row_rsForm['ii_609']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_610" value="<?php echo $row_rsForm['ii_610']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Office 10</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: OFFICE 10</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_611" value="<?php echo $row_rsForm['ii_611']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_612" value="<?php echo $row_rsForm['ii_612']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_613" value="<?php echo $row_rsForm['ii_613']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_614" value="<?php echo $row_rsForm['ii_614']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_615\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_615'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_616" value="<?php echo $row_rsForm['ii_616']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_617" value="<?php echo $row_rsForm['ii_617']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_618" value="<?php echo $row_rsForm['ii_618']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_619" value="<?php echo $row_rsForm['ii_619']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_620" value="<?php echo $row_rsForm['ii_620']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Office 10</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: OFFICE 10</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_621" value="<?php echo $row_rsForm['ii_621']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_622" value="<?php echo $row_rsForm['ii_622']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_623" value="<?php echo $row_rsForm['ii_623']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_624" value="<?php echo $row_rsForm['ii_624']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_625\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_625'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_626" value="<?php echo $row_rsForm['ii_626']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_627" value="<?php echo $row_rsForm['ii_627']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_628" value="<?php echo $row_rsForm['ii_628']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_629" value="<?php echo $row_rsForm['ii_629']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_630" value="<?php echo $row_rsForm['ii_630']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Office 10</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Cubicle1"></a>
						<label class="lblQuestion">Cubicle Area 1 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: Cubicle Area 1</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_631" value="<?php echo $row_rsForm['ii_631']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_632" value="<?php echo $row_rsForm['ii_632']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_633" value="<?php echo $row_rsForm['ii_633']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_634" value="<?php echo $row_rsForm['ii_634']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_635\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_635'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_636" value="<?php echo $row_rsForm['ii_636']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_637" value="<?php echo $row_rsForm['ii_637']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_638" value="<?php echo $row_rsForm['ii_638']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_639" value="<?php echo $row_rsForm['ii_639']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_640" value="<?php echo $row_rsForm['ii_640']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Cubicle 1</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: Cubicle Area 1</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_641" value="<?php echo $row_rsForm['ii_641']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_642" value="<?php echo $row_rsForm['ii_642']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_643" value="<?php echo $row_rsForm['ii_643']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_644" value="<?php echo $row_rsForm['ii_644']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_645\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_645'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_646" value="<?php echo $row_rsForm['ii_646']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_647" value="<?php echo $row_rsForm['ii_647']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_648" value="<?php echo $row_rsForm['ii_648']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_649" value="<?php echo $row_rsForm['ii_649']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_650" value="<?php echo $row_rsForm['ii_650']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Cubicle 1</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: Cubicle Area 1</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_651" value="<?php echo $row_rsForm['ii_651']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_652" value="<?php echo $row_rsForm['ii_652']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_653" value="<?php echo $row_rsForm['ii_653']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_654" value="<?php echo $row_rsForm['ii_654']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_655\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_655'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_656" value="<?php echo $row_rsForm['ii_656']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_657" value="<?php echo $row_rsForm['ii_657']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_658" value="<?php echo $row_rsForm['ii_658']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_659" value="<?php echo $row_rsForm['ii_659']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_660" value="<?php echo $row_rsForm['ii_660']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Cubicle 1</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: Cubicle Area 1</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_661" value="<?php echo $row_rsForm['ii_661']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_662" value="<?php echo $row_rsForm['ii_662']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_663" value="<?php echo $row_rsForm['ii_663']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_664" value="<?php echo $row_rsForm['ii_664']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_665\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_665'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_666" value="<?php echo $row_rsForm['ii_666']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_667" value="<?php echo $row_rsForm['ii_667']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_668" value="<?php echo $row_rsForm['ii_668']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_669" value="<?php echo $row_rsForm['ii_669']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_670" value="<?php echo $row_rsForm['ii_670']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Cubicle 1</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Cubicle2"></a>
						<label class="lblQuestion">Cubicle Area 2 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: Cubicle Area 2</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_671" value="<?php echo $row_rsForm['ii_671']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_672" value="<?php echo $row_rsForm['ii_672']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_673" value="<?php echo $row_rsForm['ii_673']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_674" value="<?php echo $row_rsForm['ii_674']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_675\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_675'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_676" value="<?php echo $row_rsForm['ii_676']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_677" value="<?php echo $row_rsForm['ii_677']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_678" value="<?php echo $row_rsForm['ii_678']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_679" value="<?php echo $row_rsForm['ii_679']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_680" value="<?php echo $row_rsForm['ii_680']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Cubicle 2</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: Cubicle Area 2</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_681" value="<?php echo $row_rsForm['ii_681']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_682" value="<?php echo $row_rsForm['ii_682']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_683" value="<?php echo $row_rsForm['ii_683']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_684" value="<?php echo $row_rsForm['ii_684']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_685\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_685'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_686" value="<?php echo $row_rsForm['ii_686']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_687" value="<?php echo $row_rsForm['ii_687']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_688" value="<?php echo $row_rsForm['ii_688']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_689" value="<?php echo $row_rsForm['ii_689']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_690" value="<?php echo $row_rsForm['ii_690']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Cubicle 2</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: Cubicle Area 2</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_691" value="<?php echo $row_rsForm['ii_691']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_692" value="<?php echo $row_rsForm['ii_692']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_693" value="<?php echo $row_rsForm['ii_693']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_694" value="<?php echo $row_rsForm['ii_694']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_695\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_695'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_696" value="<?php echo $row_rsForm['ii_696']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_697" value="<?php echo $row_rsForm['ii_697']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_698" value="<?php echo $row_rsForm['ii_698']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_699" value="<?php echo $row_rsForm['ii_699']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_700" value="<?php echo $row_rsForm['ii_700']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Cubicle 2</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: Cubicle Area 2</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_701" value="<?php echo $row_rsForm['ii_701']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_702" value="<?php echo $row_rsForm['ii_702']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_703" value="<?php echo $row_rsForm['ii_703']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_704" value="<?php echo $row_rsForm['ii_704']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_705\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_705'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_706" value="<?php echo $row_rsForm['ii_706']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_707" value="<?php echo $row_rsForm['ii_707']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_708" value="<?php echo $row_rsForm['ii_708']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_709" value="<?php echo $row_rsForm['ii_709']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_710" value="<?php echo $row_rsForm['ii_710']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Cubicle 2</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Cubicle3"></a>
						<label class="lblQuestion">Cubicle Area 3 : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: Cubicle Area 3</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_711" value="<?php echo $row_rsForm['ii_711']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_712" value="<?php echo $row_rsForm['ii_712']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_713" value="<?php echo $row_rsForm['ii_713']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_714" value="<?php echo $row_rsForm['ii_714']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_715\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_715'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_716" value="<?php echo $row_rsForm['ii_716']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_717" value="<?php echo $row_rsForm['ii_717']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_718" value="<?php echo $row_rsForm['ii_718']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_719" value="<?php echo $row_rsForm['ii_719']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_720" value="<?php echo $row_rsForm['ii_720']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Cubicle 3</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: Cubicle Area 3</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_721" value="<?php echo $row_rsForm['ii_721']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_722" value="<?php echo $row_rsForm['ii_722']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_723" value="<?php echo $row_rsForm['ii_723']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_724" value="<?php echo $row_rsForm['ii_724']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_725\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_725'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_726" value="<?php echo $row_rsForm['ii_726']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_727" value="<?php echo $row_rsForm['ii_727']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_728" value="<?php echo $row_rsForm['ii_728']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_729" value="<?php echo $row_rsForm['ii_729']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_730" value="<?php echo $row_rsForm['ii_730']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Cubicle 3</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: Cubicle Area 3</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_731" value="<?php echo $row_rsForm['ii_731']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_732" value="<?php echo $row_rsForm['ii_732']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_733" value="<?php echo $row_rsForm['ii_733']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_734" value="<?php echo $row_rsForm['ii_734']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_735\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_735'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_736" value="<?php echo $row_rsForm['ii_736']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_737" value="<?php echo $row_rsForm['ii_737']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_738" value="<?php echo $row_rsForm['ii_738']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_739" value="<?php echo $row_rsForm['ii_739']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_740" value="<?php echo $row_rsForm['ii_740']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Cubicle 3</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: Cubicle Area 3</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_741" value="<?php echo $row_rsForm['ii_741']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_742" value="<?php echo $row_rsForm['ii_742']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_743" value="<?php echo $row_rsForm['ii_743']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_744" value="<?php echo $row_rsForm['ii_744']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_745\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_745'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_746" value="<?php echo $row_rsForm['ii_746']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_747" value="<?php echo $row_rsForm['ii_747']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_748" value="<?php echo $row_rsForm['ii_748']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_749" value="<?php echo $row_rsForm['ii_749']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_750" value="<?php echo $row_rsForm['ii_750']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Cubicle 3</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Computer"></a>
						<label class="lblQuestion">Computer Room : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: Computer Room</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_751" value="<?php echo $row_rsForm['ii_751']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_752" value="<?php echo $row_rsForm['ii_752']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_753" value="<?php echo $row_rsForm['ii_753']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_754" value="<?php echo $row_rsForm['ii_754']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_755\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_755'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_756" value="<?php echo $row_rsForm['ii_756']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_757" value="<?php echo $row_rsForm['ii_757']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_758" value="<?php echo $row_rsForm['ii_758']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_759" value="<?php echo $row_rsForm['ii_759']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_760" value="<?php echo $row_rsForm['ii_760']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Computer Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: Computer Room</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_761" value="<?php echo $row_rsForm['ii_761']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_762" value="<?php echo $row_rsForm['ii_762']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_763" value="<?php echo $row_rsForm['ii_763']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_764" value="<?php echo $row_rsForm['ii_764']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_765\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_765'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_766" value="<?php echo $row_rsForm['ii_766']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_767" value="<?php echo $row_rsForm['ii_767']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_768" value="<?php echo $row_rsForm['ii_768']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_769" value="<?php echo $row_rsForm['ii_769']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_770" value="<?php echo $row_rsForm['ii_770']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Computer Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: Computer Room</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_771" value="<?php echo $row_rsForm['ii_771']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_772" value="<?php echo $row_rsForm['ii_772']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_773" value="<?php echo $row_rsForm['ii_773']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_774" value="<?php echo $row_rsForm['ii_774']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_775\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_775'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_776" value="<?php echo $row_rsForm['ii_776']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_777" value="<?php echo $row_rsForm['ii_777']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_778" value="<?php echo $row_rsForm['ii_778']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_779" value="<?php echo $row_rsForm['ii_779']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_780" value="<?php echo $row_rsForm['ii_780']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Computer Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: Computer Room</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_781" value="<?php echo $row_rsForm['ii_781']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_782" value="<?php echo $row_rsForm['ii_782']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_783" value="<?php echo $row_rsForm['ii_783']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_784" value="<?php echo $row_rsForm['ii_784']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_785\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_785'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_786" value="<?php echo $row_rsForm['ii_786']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_787" value="<?php echo $row_rsForm['ii_787']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_788" value="<?php echo $row_rsForm['ii_788']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_789" value="<?php echo $row_rsForm['ii_789']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_790" value="<?php echo $row_rsForm['ii_790']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Computer Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Printer"></a>
						<label class="lblQuestion">IT Room/Printer/Fax : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: IT Room/Printer/Fax</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_791" value="<?php echo $row_rsForm['ii_791']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_792" value="<?php echo $row_rsForm['ii_792']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_793" value="<?php echo $row_rsForm['ii_793']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_794" value="<?php echo $row_rsForm['ii_794']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_795\" value=\"".$intIndex."\"";
									
										if ($row_rsForm['ii_795'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_796" value="<?php echo $row_rsForm['ii_796']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_797" value="<?php echo $row_rsForm['ii_797']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_798" value="<?php echo $row_rsForm['ii_798']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_799" value="<?php echo $row_rsForm['ii_799']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_800" value="<?php echo $row_rsForm2['ii_800']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 IT Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: IT Room/Printer/Fax</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_801" value="<?php echo $row_rsForm2['ii_801']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_802" value="<?php echo $row_rsForm2['ii_802']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_803" value="<?php echo $row_rsForm2['ii_803']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_804" value="<?php echo $row_rsForm2['ii_804']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_805\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_805'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_806" value="<?php echo $row_rsForm2['ii_806']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_807" value="<?php echo $row_rsForm2['ii_807']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_808" value="<?php echo $row_rsForm2['ii_808']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_809" value="<?php echo $row_rsForm2['ii_809']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_810" value="<?php echo $row_rsForm2['ii_810']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 IT Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: IT Room/Printer/Fax</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_811" value="<?php echo $row_rsForm2['ii_811']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_812" value="<?php echo $row_rsForm2['ii_812']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_813" value="<?php echo $row_rsForm2['ii_813']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_814" value="<?php echo $row_rsForm2['ii_814']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_815\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_815'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_816" value="<?php echo $row_rsForm2['ii_816']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_817" value="<?php echo $row_rsForm2['ii_817']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_818" value="<?php echo $row_rsForm2['ii_818']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_819" value="<?php echo $row_rsForm2['ii_819']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_820" value="<?php echo $row_rsForm2['ii_820']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 IT Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: IT Room/Printer/Fax</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_821" value="<?php echo $row_rsForm2['ii_821']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_822" value="<?php echo $row_rsForm2['ii_822']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_823" value="<?php echo $row_rsForm2['ii_823']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_824" value="<?php echo $row_rsForm2['ii_824']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_825\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_825'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_826" value="<?php echo $row_rsForm2['ii_826']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_827" value="<?php echo $row_rsForm2['ii_827']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_828" value="<?php echo $row_rsForm2['ii_828']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_829" value="<?php echo $row_rsForm2['ii_829']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_830" value="<?php echo $row_rsForm2['ii_830']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 IT Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Supply"></a>
						<label class="lblQuestion">Supply/Main. Room : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: Supply/Main. Room</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_831" value="<?php echo $row_rsForm2['ii_831']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_832" value="<?php echo $row_rsForm2['ii_832']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_833" value="<?php echo $row_rsForm2['ii_833']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_834" value="<?php echo $row_rsForm2['ii_834']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_835\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_835'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_836" value="<?php echo $row_rsForm2['ii_836']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_837" value="<?php echo $row_rsForm2['ii_837']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_838" value="<?php echo $row_rsForm2['ii_838']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_839" value="<?php echo $row_rsForm2['ii_839']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_840" value="<?php echo $row_rsForm2['ii_840']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Supply</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: Supply/Main. Room</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_841" value="<?php echo $row_rsForm2['ii_841']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_842" value="<?php echo $row_rsForm2['ii_842']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_843" value="<?php echo $row_rsForm2['ii_843']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_844" value="<?php echo $row_rsForm2['ii_844']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_845\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_845'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_846" value="<?php echo $row_rsForm2['ii_846']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_847" value="<?php echo $row_rsForm2['ii_847']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_848" value="<?php echo $row_rsForm2['ii_848']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_849" value="<?php echo $row_rsForm2['ii_849']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_850" value="<?php echo $row_rsForm2['ii_850']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Supply</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: Supply/Main. Room</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_851" value="<?php echo $row_rsForm2['ii_851']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_852" value="<?php echo $row_rsForm2['ii_852']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_853" value="<?php echo $row_rsForm2['ii_853']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_854" value="<?php echo $row_rsForm2['ii_854']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_855\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_855'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_856" value="<?php echo $row_rsForm2['ii_856']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_857" value="<?php echo $row_rsForm2['ii_857']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_858" value="<?php echo $row_rsForm2['ii_858']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_859" value="<?php echo $row_rsForm2['ii_859']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_860" value="<?php echo $row_rsForm2['ii_860']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Supply</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: Supply/Main. Room</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_861" value="<?php echo $row_rsForm2['ii_861']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_862" value="<?php echo $row_rsForm2['ii_862']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_863" value="<?php echo $row_rsForm2['ii_863']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_864" value="<?php echo $row_rsForm2['ii_864']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_865\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_865'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_866" value="<?php echo $row_rsForm2['ii_866']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_867" value="<?php echo $row_rsForm2['ii_867']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_868" value="<?php echo $row_rsForm2['ii_868']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_869" value="<?php echo $row_rsForm2['ii_869']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_870" value="<?php echo $row_rsForm2['ii_870']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Supply</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Collectables"></a>
						<label class="lblQuestion">Artwork/Collectables : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: Artwork/Collectables</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_871" value="<?php echo $row_rsForm2['ii_871']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_872" value="<?php echo $row_rsForm2['ii_872']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_873" value="<?php echo $row_rsForm2['ii_873']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_874" value="<?php echo $row_rsForm2['ii_874']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_875\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_875'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_876" value="<?php echo $row_rsForm2['ii_876']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_877" value="<?php echo $row_rsForm2['ii_877']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_878" value="<?php echo $row_rsForm2['ii_878']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_879" value="<?php echo $row_rsForm2['ii_879']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_880" value="<?php echo $row_rsForm2['ii_880']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Artwork/Collectables</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: Artwork/Collectables</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_881" value="<?php echo $row_rsForm2['ii_881']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_882" value="<?php echo $row_rsForm2['ii_882']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_883" value="<?php echo $row_rsForm2['ii_883']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_884" value="<?php echo $row_rsForm2['ii_884']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_885\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_885'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_886" value="<?php echo $row_rsForm2['ii_886']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_887" value="<?php echo $row_rsForm2['ii_887']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_888" value="<?php echo $row_rsForm2['ii_888']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_889" value="<?php echo $row_rsForm2['ii_889']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_890" value="<?php echo $row_rsForm2['ii_890']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Artwork/Collectables</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: Artwork/Collectables</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_891" value="<?php echo $row_rsForm2['ii_891']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_892" value="<?php echo $row_rsForm2['ii_892']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_893" value="<?php echo $row_rsForm2['ii_893']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_894" value="<?php echo $row_rsForm2['ii_894']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_895\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_895'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_896" value="<?php echo $row_rsForm2['ii_896']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_897" value="<?php echo $row_rsForm2['ii_897']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_898" value="<?php echo $row_rsForm2['ii_898']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_899" value="<?php echo $row_rsForm2['ii_899']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_900" value="<?php echo $row_rsForm2['ii_900']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Artwork/Collectables</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<label class="lblSubQuestion">ITEM #4: Artwork/Collectables</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_901" value="<?php echo $row_rsForm2['ii_901']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_902" value="<?php echo $row_rsForm2['ii_902']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_903" value="<?php echo $row_rsForm2['ii_903']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_904" value="<?php echo $row_rsForm2['ii_904']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_905\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_905'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_906" value="<?php echo $row_rsForm2['ii_906']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_907" value="<?php echo $row_rsForm2['ii_907']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_908" value="<?php echo $row_rsForm2['ii_908']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_909" value="<?php echo $row_rsForm2['ii_909']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_910" value="<?php echo $row_rsForm2['ii_910']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Artwork/Collectables</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Miscellaneous"></a>
						<label class="lblQuestion">Miscellaneous Room : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: Miscellaneous Room</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_911" value="<?php echo $row_rsForm2['ii_911']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_912" value="<?php echo $row_rsForm2['ii_912']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_913" value="<?php echo $row_rsForm2['ii_913']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_914" value="<?php echo $row_rsForm2['ii_914']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_915\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_915'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_916" value="<?php echo $row_rsForm2['ii_916']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_917" value="<?php echo $row_rsForm2['ii_917']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_918" value="<?php echo $row_rsForm2['ii_918']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_919" value="<?php echo $row_rsForm2['ii_919']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_920" value="<?php echo $row_rsForm2['ii_920']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Misc. Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: Miscellaneous Room</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_921" value="<?php echo $row_rsForm2['ii_921']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_922" value="<?php echo $row_rsForm2['ii_922']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_923" value="<?php echo $row_rsForm2['ii_923']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_924" value="<?php echo $row_rsForm2['ii_924']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_925\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_925'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_926" value="<?php echo $row_rsForm2['ii_926']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_927" value="<?php echo $row_rsForm2['ii_927']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_928" value="<?php echo $row_rsForm2['ii_928']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_929" value="<?php echo $row_rsForm2['ii_929']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_930" value="<?php echo $row_rsForm2['ii_930']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Misc. Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: Miscellaneous Room</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_931" value="<?php echo $row_rsForm2['ii_931']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_932" value="<?php echo $row_rsForm2['ii_932']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_933" value="<?php echo $row_rsForm2['ii_933']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_934" value="<?php echo $row_rsForm2['ii_934']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_935\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_935'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_936" value="<?php echo $row_rsForm2['ii_936']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_937" value="<?php echo $row_rsForm2['ii_937']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_938" value="<?php echo $row_rsForm2['ii_938']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_939" value="<?php echo $row_rsForm2['ii_939']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_940" value="<?php echo $row_rsForm2['ii_940']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Misc. Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: Miscellaneous Room</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_941" value="<?php echo $row_rsForm2['ii_941']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_942" value="<?php echo $row_rsForm2['ii_942']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_943" value="<?php echo $row_rsForm2['ii_943']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_944" value="<?php echo $row_rsForm2['ii_944']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_945\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_945'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_946" value="<?php echo $row_rsForm2['ii_946']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_947" value="<?php echo $row_rsForm2['ii_947']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_948" value="<?php echo $row_rsForm2['ii_948']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_949" value="<?php echo $row_rsForm2['ii_949']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_950" value="<?php echo $row_rsForm2['ii_950']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Misc. Room</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
						<a name="Other"></a>
						<label class="lblQuestion">Other Addidtional Items : Upload Photos to this category</label>
						<br/><br/>
						<label class="lblSubQuestion">ITEM #1: Other Addidtional Items</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_951" value="<?php echo $row_rsForm2['ii_951']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_952" value="<?php echo $row_rsForm2['ii_952']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_953" value="<?php echo $row_rsForm2['ii_953']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_954" value="<?php echo $row_rsForm2['ii_954']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_955\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_955'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_956" value="<?php echo $row_rsForm2['ii_956']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_957" value="<?php echo $row_rsForm2['ii_957']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_958" value="<?php echo $row_rsForm2['ii_958']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_959" value="<?php echo $row_rsForm2['ii_959']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_960" value="<?php echo $row_rsForm2['ii_960']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #1 Other Items</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #2: Other Addidtional Items</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_961" value="<?php echo $row_rsForm2['ii_961']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_962" value="<?php echo $row_rsForm2['ii_962']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_963" value="<?php echo $row_rsForm2['ii_963']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_964" value="<?php echo $row_rsForm2['ii_964']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_965\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_965'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_966" value="<?php echo $row_rsForm2['ii_966']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_967" value="<?php echo $row_rsForm2['ii_967']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_968" value="<?php echo $row_rsForm2['ii_968']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_969" value="<?php echo $row_rsForm2['ii_969']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_970" value="<?php echo $row_rsForm2['ii_970']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #2 Other Items</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #3: Other Addidtional Items</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_971" value="<?php echo $row_rsForm2['ii_971']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_972" value="<?php echo $row_rsForm2['ii_972']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_973" value="<?php echo $row_rsForm2['ii_973']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_974" value="<?php echo $row_rsForm2['ii_974']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_975\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_975'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_976" value="<?php echo $row_rsForm2['ii_976']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_977" value="<?php echo $row_rsForm2['ii_977']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_978" value="<?php echo $row_rsForm2['ii_978']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_979" value="<?php echo $row_rsForm2['ii_979']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_980" value="<?php echo $row_rsForm2['ii_980']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #3 Other Items</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
					<div class="divQuestionForms">
					<div class="customHeader divInsuranceBodyHeader">
									<label class="lblSubQuestion">ITEM #4: Other Addidtional Items</label>
					</div>
					<div class="customContent divInsuranceBodyContent">
									<label>Name: </label><input type="text" name="ii_981" value="<?php echo $row_rsForm2['ii_981']; ?>" size="20" maxlength="100" /><label> Catagory: </label><input type="text" name="ii_982" value="<?php echo $row_rsForm2['ii_982']; ?>" size="20" maxlength="100" /><br/>
									<label>Room: </label><input type="text" name="ii_983" value="<?php echo $row_rsForm2['ii_983']; ?>" size="20" maxlength="100" /><label>Purchase Date: </label><input type="text" name="ii_984" value="<?php echo $row_rsForm2['ii_984']; ?>" size="20" maxlength="100" /><br/><label>Receipt: </label><?php $intIndex = 1;//holdes the number of radio button there will be

									//goes around creating the radio button group
									while($intIndex <> 3)
									{
										echo "<input type=\"radio\" name=\"ii_985\" value=\"".$intIndex."\"";
									
										if ($row_rsForm2['ii_985'] == $intIndex)
											echo " checked />".$arrYesNo[$intIndex];
										else
											echo " />".$arrYesNo[$intIndex];
						
										//adds on to $intIndex
										$intIndex = $intIndex + 1;
									}//end of while loop?><br/>
									<label>Make: </label><input type="text" name="ii_986" value="<?php echo $row_rsForm2['ii_986']; ?>" size="20" maxlength="100" /><label> Model: </label><input type="text" name="ii_987" value="<?php echo $row_rsForm2['ii_987']; ?>" size="20" maxlength="100" /><br/><label>Place Purchased: </label><input type="text" name="ii_988" value="<?php echo $row_rsForm2['ii_988']; ?>" size="20" maxlength="100" /><br/>
									<label>Serial #: </label><input type="text" name="ii_989" value="<?php echo $row_rsForm2['ii_989']; ?>" size="20" maxlength="100" /><br/><label>Estimated Purchase Price: </label><input type="text" name="ii_990" value="<?php echo $row_rsForm2['ii_990']; ?>" size="20" maxlength="100" />
					</div>
	                            	<div class="customNavigation divInsuranceBodyNavigation">
						<input type="file" name="fileImage[]" />
						<br/><br/>
						<?php if(file_exists("../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg"))
							echo "<label class=\"lblFontBold\">ITEM #4 Other Items</label>
						<br/><br/>
						<a href=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\"><img src=\"../../images/".$UserID."/".$UserID.$intFileImageIndex.".jpg\" /></a>";
		
						//adds to FileImageIndex
						$intFileImageIndex = $intFileImageIndex + 1;?>
					</div>
		                        <div class="customFooter divInsuranceBodyFooter"></div>
					</div>
				</div>
 				<label>*To save changes made submit must be pressed, if you leave this page without selecting submit all changes will not be saved</label>
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
                                <!-- InstanceBeginEditable name="FormButtonsSection" --><hr/><input type="Submit" value="Uploaded Images" onclick="getDocID('hfSave').value = '2';getDocID('divUploadingImage').style.display = 'block';getDocID('divGrayBG').style.display = 'block';//window.scrollTo(0,0);"/><!-- InstanceEndEditable -->
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
			  <!-- InstanceBeginEditable name="BasicFooter" --><!-- InstanceEndEditable -->
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
