<?php 
//Start the session so we can store what the security code actually is
session_start();

//Send a generated image to the browser
createCaptcha();
exit(); 

//creates a dynamic Image which will be used to tell if there user is an human and not a bot
function createCaptcha()
{
	//Now lets use md5 to generate a totally random string, dont need a 32 character long string 	so we trim it down to 5
	//$strKeyString = substr(md5(microtime() * mktime()),0,5);
	
	$strKeyString = '';//holds the string of numbers and chars to be display to the user

	// initialise image with dimensions of 200 x 60 pixels  
	$imgCaptcha = @imagecreatetruecolor(200, 60) or die("Cannot Initialize new GD image stream");

	//set background to white and allocate drawing colours  
	$imgBackground = imagecolorallocate($imgCaptcha, 0xFF, 0xFF, 0xFF); 
	imagefill ($imgCaptcha, 0, 0, $imgBackground); 
	$imgColLineColor = imagecolorallocate($imgCaptcha, 0xCC, 0xCC, 0xCC); 
	$imgColTextCol = imagecolorallocate($imgCaptcha, 0x33, 0x33, 0x33); 
	
	//draw random lines on canvas in order to make it a little hard for bots to create accoutns
	for($intIndex=0; $intIndex < 6; $intIndex++) 
	{ 
		imagesetthickness($imgCaptcha, rand(1,3)); 
		imageline($imgCaptcha, 0, rand(0,60), 200, rand(0,60), $imgColLineColor); 
	}//end of for loop
		
	//add random digits to canvas
	for($intIndex = 75; $intIndex <= 155; $intIndex += 20) 
	{ 
		//$strKeyString .= ($intTempNum = rand(0, 9));
		$strKeyString .= ($intTempNum = randString(1,TRUE,TRUE,TRUE));
		imagechar($imgCaptcha, rand(55, 175), $intIndex, rand(14, 25), $intTempNum, $imgColTextCol); 
	}//end of for loop
	
	//encrpyts the keystring in order not to be hack
	$_SESSION['key'] = md5($strKeyString);
 
    //Tell the browser what kind of file is come in 
    header("Content-Type: image/jpeg"); 

    //Output the newly created image in jpeg format 
    imagejpeg($imgCaptcha);
   
    //Free up resources
    imagedestroy($imgCaptcha); 
}//end of createCaptcha()

//creates a string with different types of number and upper/loower case ahplibet
function randString($intLenght, $boolUseNum, $boolUseUpperChar, $boolUseLowChar) {
   	//makes sure there is a lenther that is not negitive and not bigger the 100 for speed
    if (!$intLenght || $intLenght < 1 || $intLenght > 100) 
	{
        print "Error: \"Length\" out of range (1-100)<br>\n";
        return;
    }//end of if
    
	//make sure that there is something charter to use
	if ($boolUseNum === False && $boolUseUpperChar === False && $boolUseLowrChar === False) 
	{
        print "Error: No character types specified<br>\n";
        return;
    }//end of if

    $strKey = "";//holds the string
    $intIndex = 0;//controls the do while loop
	
	//goes around for each charter that the use whats for there key string using $intLenght
    do {
        switch(mt_rand(1,3)) 
		{
            // get number - ASCII characters (0:48 through 9:57)
            case 1:
                if ($boolUseNum !== False) 
				{
                    $strKey .= chr(mt_rand(48,57));
                    $intIndex++;
                }//end of if
                break;

            // get uppercase letter - ASCII characters (a:65 through z:90)
            case 2:
                if ($boolUseUpperChar !== False) 
				{
                    $strKey .= chr(mt_rand(65,90));
                    $intIndex++;
                }//end of if
                break;

            // get lowercase letter - ASCII characters (A:97 through Z:122)
            case 3:
                if ($boolUseLowrChar !== False) 
				{
                    $strKey .= chr(mt_rand(97,122));
                    $intIndex++;
                }//end of if
                break;
        }//end of switch
    } while ($intIndex < $intLenght);

    return $strKey;
}//end of randString()?>