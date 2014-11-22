<?php 
//Starts the session in order to use the $_SESSION arrary
if (!isset($_SESSION))
	session_start();

//checks if the user has is log on
function checksUserID()
{
	//checks if the user has log on
	if(isset($_COOKIE['User']) || isset($_SESSION['UserID']))
		return TRUE;
	else
		return FALSE;
}//end of checksUserID()

//checks which title the user is using
function checkUserTitle($strTitle)
{
	//checks if the user is using a cookie or a seasion
	if (isset($_COOKIE['User']) || isset($_SESSION['UserID']))
	{
		//checks if it is a sesion if not then it is a cookie
		if (isset($_SESSION['UserID']))
		{
			//checks if the user is the $strTitle
			if(strpos(base64_decode(base64_decode($_SESSION['UserTitle'])),$strTitle) !== FALSE)
				return TRUE;
			else
				return FALSE;
		}//end of if
		else
		{
			//checks if the user is the $strTitle
			if(strpos(base64_decode(base64_decode($_COOKIE['User']['one'])),$strTitle) !== FALSE)
				return TRUE;
			else
				return FALSE; 
		}//end of if else
	}//end of if
	else
		return FALSE;
}//end of strTitle

//Gets the users id in order to enter database *TEMP Until some better is find*
function getUserID()
{
	//checks if the user is using a cookie or a seasion then decodes it then returns it
	if (isset($_COOKIE['User']) || isset($_SESSION['UserID']))
	{
		if (isset($_SESSION['UserID']))
			return base64_decode($_SESSION['UserID']); 
		else
			return base64_decode($_COOKIE['User']['three']); 
	}//end of if
	else
		return 0;
}//end of getUserID()

//Gets the users id in order to enter database *TEMP Until some better is find*
function getUserName()
{
	//checks if the user is using a cookie or a seasion then decodes it then returns it
	if (isset($_COOKIE['User']) || isset($_SESSION['UserID']))
	{
		if (isset($_SESSION['UserID']))
			return base64_decode($_SESSION['UserName']); 
		else
			return base64_decode($_COOKIE['User']['one']); 
	}//end of if
	else
		return 0;
}//end of getUserName()

//Logs the user on to the site
function logOnSite($strUserName,$strPassword,$strRemPass,$database_conContinuty, $conContinuty)
{
	$strAttachEmail = "";//holds the attach user's e-mail for $_SESSION['UserEMail'] and User[two]

  	mysql_select_db($database_conContinuty, $conContinuty);
	$LoginRS = mysql_query("SELECT * FROM users WHERE users.passwd=MD5('".str_replace("'","''",base64_decode(base64_decode($strPassword)))."') AND users.login='".str_replace("'","''",base64_decode($strUserName))."'", $conContinuty) or die(mysql_error());
	$row_loginFoundUser = mysql_fetch_assoc($LoginRS);
	$loginFoundUser = mysql_num_rows($LoginRS);

	//checks again if ther is no user find as they made be attach to the account
	if($loginFoundUser == 0)
	{	
		//finds the user in the database that matchs to the user that is trying to log in as they may be attach to a account 
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsAttachUser = mysql_query("SELECT * FROM users WHERE Users LIKE '%".str_replace("'","''",base64_decode($strUserName))."%'", $conContinuty) or die(mysql_error());
		$row_AttachUser = mysql_fetch_assoc($rsAttachUser);
		$totalRows_rsAttachUser = mysql_num_rows($rsAttachUser);
		
		//checks if the user has other users that are attach to this account
		if($totalRows_rsAttachUser > 0)
		{
			$arrUser = split("#",$row_AttachUser['Users']);//holds the array of Users the account
			
			//goes around each users that is connect to this account and display it
			//for the user to remove it from the account
			foreach ($arrUser as $arrUserValue)
			{
				//checks if the row in arrUser is not blank
				//blank line is in here as it is the last item
				if($arrUserValue != "")
				{
					$arrUserDetails = split("~",$arrUserValue);//holds the array fot details of the Users
					
					if(md5(str_replace("'","''",base64_decode(base64_decode($strPassword)))) == $row_AttachUser['passwd'])
					{
						mysql_select_db($database_conContinuty, $conContinuty);
						$LoginRS = mysql_query("SELECT * FROM users WHERE id=".$row_AttachUser['id'], $conContinuty) or die(mysql_error());
						$row_loginFoundUser = mysql_fetch_assoc($LoginRS);
						$loginFoundUser = mysql_num_rows($LoginRS);
						
						$_SESSION['UserEMail'] = base64_encode($arrUserDetails[0]);
						
						//checks if the user click yes to remeber them and creates a cookie for them
						if($strRemPass)
						{
							$expire = time() + 1728000; // cookie will Expire in 20 days
																	
							setcookie('User[two]',base64_encode($row_loginFoundUser['login']), $expire);
						}//end of if
						
						//leaves the foreach loop
						break;
					}//end of if
				}//end of if
			}//end of foreach
		}//end of if
	}//end of if

  	if ($loginFoundUser > 0) 
	{    
     	//declare three session variables and assign them
	 	$_SESSION['UserID'] = base64_encode($row_loginFoundUser['id']);
		$_SESSION['UserName'] = base64_encode($row_loginFoundUser['firstname']." ".$row_loginFoundUser['lastname']);
		$_SESSION['UserTitle'] = base64_encode(base64_encode($row_loginFoundUser['UserTitle']));	
		
		//checks if the User EMail is already meaning the attach user is already in log in for this account
		if($_SESSION['UserEMail'] == "")
	 		$_SESSION['UserEMail'] = base64_encode($row_loginFoundUser['login']);
		
		//checks if the user click yes to remeber them and creates a cookie for them
		if($strRemPass)
		{
			$expire = time() + 1728000; // cookie will Expire in 20 days
	
			//sets the cookies in the sever side in order to know what didi the user do 
			setcookie('User[one]',base64_encode($row_loginFoundUser['id']), $expire);
			setcookie('User[three]',base64_encode($row_loginFoundUser['firstname']." ".$row_loginFoundUser['lastname']), $expire);
			setcookie('User[four]',base64_encode(base64_encode($row_loginFoundUser['UserTitle'])), $expire);
						
			//checks if the User[two] is already meaning the attach user is already in log in for this account
			if($_COOKIE['User']['two'] == "")
				setcookie('User[two]',base64_encode($row_loginFoundUser['login']), $expire);
		}//end of if
		
		return TRUE;
  	}//end of if
		
	return FALSE;
}//end of logOnSite()

//Logs off the user from the site
function logOff($strAccessContorl)
{
	//checks if there is a cookie
	if (isset($_COOKIE['User']))
	{
		$expire = time() - 10; // cookie will Expires 10 secs ago

		//destorys that cookie on the users computer
		setcookie('User[four]',"", $expire);
		setcookie('User[three]',"", $expire);
		setcookie('User[two]',"", $expire);
		setcookie('User[one]',"", $expire);
	}//end of if
			
	//checks if there is a session still on
	if (isset($_SESSION['UserID']))
	{
		//desotroys the session
		session_unset(); 
		session_destroy();
	}//end of if
	
	//tells the user that they have log off
	header($strAccessContorl);
}//end of LogOff()?>