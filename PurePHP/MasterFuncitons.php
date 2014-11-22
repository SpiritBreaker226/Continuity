<?php //sends the e-mails
function sendEmail($strSubject,$strMessageBody,$strSendTo,$strSendFrom,$strSwiftLocation = '../PurePHP/Swift EMail/Swift.php',$strSwiftSMTP = '../PurePHP/Swift EMail/Swift/Connection/SMTP.php',$boolSendBatchEmail = FALSE)
{
	require_once($strSwiftLocation);
	require_once($strSwiftSMTP);

	//sets up swift which is the email library to send out a e-mail
	//$swift =& new Swift(new Swift_Connection_SMTP("Mail.nisgroup.com"));
	$swift =& new Swift(new Swift_Connection_SMTP("Mail.ifgcanada.com"));

	//creates the subject, body of the e-mail
	$smessMessage =& new Swift_Message($strSubject, nl2br($strMessageBody), "text/html");

	//checks if the user whats to send a batch e-mail and not just one
	if ($boolSendBatchEmail === TRUE)
	{
		//breaks $strSendTo
		$arrSendTo = explode(", ",$strSendTo);
	
		//sents the Swift_RecipientList which controls the batch emails uses $strSendTo
		//$smessMessage->setTo("undisclosed-recipients:;");
		$strSendTo = new Swift_RecipientList();
				
		//goes around adding in all of the into $strSendTo
		for ($intIndex = (count($arrSendTo) - 1);$intIndex > -1;$intIndex--)
		{
			$strSendTo->addTo($arrSendTo[$intIndex]);
		}//end of for loop
	}//end of if
		
	//sends the email to the admins and tells the user that the message has been sent
	if ($swift->send($smessMessage,$strSendTo,$strSendFrom))
	{
		//disconnect from the server for the e-mail system
		$swift->disconnect();
		return TRUE;
	}//end of if
 		
	//disconnect from the server for the e-mail system
	$swift->disconnect();
	return FALSE;
}//end of sendEmail()

//sends a e-mail to the mod of a section for approval
function sendSectionEmail($strSubject,$strMessageBody,$strSendFrom,$strContacts,$strAppLoc = "../",$strSwiftLocation = '../PurePHP/Swift EMail/Swift.php',$strSwiftSMTP = '../PurePHP/Swift EMail/Swift/Connection/SMTP.php')
{
	$strBatchEMail = '';//holds all of the e-mails that will be sent out

	//send it off to the person in chage of this game
	$lines = file($strAppLoc."Approvals/".$strContacts); 
	
	//gets each user that is in this section
	foreach ($lines as $line_num => $line) 
	{
		$strBatchEMail .= "$line, ";
	}//end of for each

	//removes the ", " from the end of $strBatchEMail
	$strBatchEMail = substr($strBatchEMail, 0, strlen($strBatchEMail) - 2);
	
	//set up the e-mail to be sent out
	return sendEmail($strSubject,$strMessageBody,$strBatchEMail,$strSendFrom,$strSwiftLocation,$strSwiftSMTP,TRUE);
}//end of sendSectionEmail() ?>