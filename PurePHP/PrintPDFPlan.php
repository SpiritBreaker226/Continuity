<?php require('FPDF/fpdf.php'); ?>
<?php require_once('LoginControl.php'); ?>
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
$strBusName = "";//holds the Business of that this PDF uses as it is used thought the PDF
$intColorR = 6;//ho;ds the Red part of the Solution Color
$intColorG = 145;//ho;ds the Green part of the Solution Color
$intColorB = 52;//ho;ds the Blue part of the Solution Color
$intTableIndex = 1;//contorls which tables will be used to display there data
$intSectionTableIndex = 1;//contorls holds the number of area for that section
$intStepNum = 0;//holds the number of the steps for the Continunity Plan table
$arrTablesName = array(1 =>"C2Scope","C2Employee","C2Information","C2BusinessImpact","C2Crisis","C2Logistics","C2Alternate","C2Salvage","C2Customer","C2Environment","EXOperations","EXDisasterDeclarationGuidelines","C2Immediate","C2Disaster","C2Damage","C2ITRecoveryTeam","C2Administration","C2Essential","C2Business","EXServiceMaster","C2InsuranceInventory");
$arrSectionName = array(2 =>"Disaster Response Plans","Disaster Recover Plans","Disaster Restoration Plans");
$arrSectionSideName = array(2 =>"Disaster Response","Disaster Recovery","Disaster Restoration");
$arrAreaName = array(1 =>"Continuity Section ","Response Section ","Recovery Team ","Disaster Restoration ");

mysql_select_db($database_conContinuty, $conContinuty);
$LoginRS = mysql_query("SELECT * FROM users WHERE id=".$UserID, $conContinuty) or die("Get User Info".mysql_error());
$row_loginFoundUser = mysql_fetch_assoc($LoginRS);

//checks whick version the users is going to used
if($row_loginFoundUser['Solution'] == 3)
{
	$strEdition = "Enterprise";
	$intColorR = 71;//ho;ds the Red part of the Solution Color
	$intColorG = 143;//ho;ds the Green part of the Solution Color
	$intColorB = 191;//ho;ds the Blue part of the Solution Color
}//end of if
else if ($row_loginFoundUser['Solution'] == 2)
{
	$strEdition = "Standard";
	$intColorR = 185;//ho;ds the Red part of the Solution Color
	$intColorG = 13;//ho;ds the Green part of the Solution Color
	$intColorB = 37;//ho;ds the Blue part of the Solution Color
}//end of else if

class PDF extends FPDF
{
	/* 
	
		System Functions
	
	*/
	
	var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $ALIGN='';
	
	function WriteHTML($html)
    {
        //HTML parser
        $html=str_replace("\n",' ',$html);
        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                //Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                elseif($this->ALIGN == 'center')
                    $this->Cell(0,5,$e,0,1,'C');
				else
                    $this->Write(5,$e);
            }
            else
            {
                //Tag
                if($e{0}=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    //Extract properties
                    $a2=split(' ',$e);
                    $tag=strtoupper(array_shift($a2));
                    $prop=array();
                    foreach($a2 as $v)
                        if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
                            $prop[strtoupper($a3[1])]=$a3[2];
                    $this->OpenTag($tag,$prop);
                }
            }
        }
    }

    function OpenTag($tag,$prop)
    {
        //Opening tag
        if($tag=='B' or $tag=='I' or $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF=$prop['HREF'];
        if($tag=='BR')
            $this->Ln(5);
        if($tag=='P')
            $this->ALIGN=$prop['ALIGN'];
        if($tag=='HR')
        {
            if( $prop['WIDTH'] != '' )
                $Width = $prop['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $this->Ln(2);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.4);
            $this->Line($x,$y,$x+$Width,$y);
            $this->SetLineWidth(0.2);
            $this->Ln(2);
        }
		if($tag=='IMG')
		{
            if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
                if(!isset($attr['WIDTH']))
                    $attr['WIDTH'] = 0;
                if(!isset($attr['HEIGHT']))
                    $attr['HEIGHT'] = 0;
                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
            }
		}
    }

    function CloseTag($tag)
    {
        //Closing tag
        if($tag=='B' or $tag=='I' or $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='P')
            $this->ALIGN='';
    }

    function SetStyle($tag,$enable)
    {
        //Modify style and select corresponding font
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s)
            if($this->$s>0)
                $style.=$s;
        $this->SetFont('',$style);
    }

    function PutLink($URL,$txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }

	//Page header
	function Header()
	{		
		//Page header
		global $strAreaName;
		
		//Page Section for the Main
		global $strSectionSideName;
		
		//Page Section for the Sub
		global $strSectionName;
		
		//Holds the area for the section the page is currently on
		global $intSectionArea;		
		
		//Checks if the Page is not the first page as it does not need
		if($this->PageNo() > 1)
		{
			//Arial bold 16 for the $strAreaName changes the text back to black
			$this->SetFont('Arial','B',16);
			$this->SetTextColor(0,0,0);

			//sets the Area Name which is the naome at the top left
			$this->SetY(5);
			$this->SetX(0);
			$this->Cell(0,0,$strAreaName);
			$this->SetTextColor(255,255,255);
			
			//finds which section the user is currently on and changes the color to the section color
			switch($intSectionArea)
			{
				case 1://basic color
					$this->SetFillColor(6,145,52);
					break;
				case 2://standard color 
					$this->SetFillColor(185,13,37);
					break;
				case 3://Contnuity color
					$this->SetFillColor(225,80,22);
					break;
				case 4://Enterprise color
					$this->SetFillColor(71,143,191);
					break;
				case 5://Glossary
					$this->SetFillColor(56,152,81);
					break;
			}//end of switch
			
			//draws the Rectalls for the header and sides
			$this->Rect(0,8,300,6,'F');
			$this->Rect(202,0,8,300,'F');
			
			//Start Transformation
			$this->StartTransform();

			//resets the font to 12 no bold
			$this->SetFont('Arial','',12);
			
			//Rotates the text by 270 degrees to be on the side
			$this->Rotate(270,50,60);
			$this->Text(-5,-95,$strSectionSideName);

			//Stop Transformation
			$this->StopTransform();
			
			$this->SetTextColor(0,0,0);
			$this->SetY(18);
			$this->SetX(0);
			$this->FormatSectionHeader($strSectionName);

			//resets the font to 12 no bold
			$this->SetFont('Arial','',12);
			
			//Line break
			$this->SetY(24);
		}//end of if
	}//end of Header()
	
	function Footer()
	{		
		//Logo at the buttom left of every page
		$this->Image('../images/LogoOrg.gif',3,270);
	}//end of Footer()
				
	/*
	
		Transform Text to move verical
	
	*/
	
	function StartTransform(){
        //save the current graphic state
        $this->_out('q');
    }

    function ScaleX($s_x, $x='', $y=''){
        $this->Scale($s_x, 100, $x, $y);
    }
    function ScaleY($s_y, $x='', $y=''){
        $this->Scale(100, $s_y, $x, $y);
    }
    function ScaleXY($s, $x='', $y=''){
        $this->Scale($s, $s, $x, $y);
    }
    function Scale($s_x, $s_y, $x='', $y=''){
        if($x === '')
            $x=$this->x;
        if($y === '')
            $y=$this->y;
        if($s_x == 0 || $s_y == 0)
            $this->Error('Please use values unequal to zero for Scaling');
        $y=($this->h-$y)*$this->k;
        $x*=$this->k;
        //calculate elements of transformation matrix
        $s_x/=100;
        $s_y/=100;
        $tm[0]=$s_x;
        $tm[1]=0;
        $tm[2]=0;
        $tm[3]=$s_y;
        $tm[4]=$x*(1-$s_x);
        $tm[5]=$y*(1-$s_y);
        //scale the coordinate system
        $this->Transform($tm);
    }

    function MirrorH($x=''){
        $this->Scale(-100, 100, $x);
    }
    function MirrorV($y=''){
        $this->Scale(100, -100, '', $y);
    }
    function MirrorP($x='',$y=''){
        $this->Scale(-100, -100, $x, $y);
    }
    function MirrorL($angle=0, $x='',$y=''){
        $this->Scale(-100, 100, $x, $y);
        $this->Rotate(-2*($angle-90),$x,$y);
    }

    function TranslateX($t_x){
        $this->Translate($t_x, 0, $x, $y);
    }
    function TranslateY($t_y){
        $this->Translate(0, $t_y, $x, $y);
    }
    function Translate($t_x, $t_y){
        //calculate elements of transformation matrix
        $tm[0]=1;
        $tm[1]=0;
        $tm[2]=0;
        $tm[3]=1;
        $tm[4]=$t_x*$this->k;
        $tm[5]=-$t_y*$this->k;
        //translate the coordinate system
        $this->Transform($tm);
    }

    function Rotate($angle, $x='', $y=''){
        if($x === '')
            $x=$this->x;
        if($y === '')
            $y=$this->y;
        $y=($this->h-$y)*$this->k;
        $x*=$this->k;
        //calculate elements of transformation matrix
        $tm[0]=cos(deg2rad($angle));
        $tm[1]=sin(deg2rad($angle));
        $tm[2]=-$tm[1];
        $tm[3]=$tm[0];
        $tm[4]=$x+$tm[1]*$y-$tm[0]*$x;
        $tm[5]=$y-$tm[0]*$y-$tm[1]*$x;
        //rotate the coordinate system around ($x,$y)
        $this->Transform($tm);
    }

    function SkewX($angle_x, $x='', $y=''){
        $this->Skew($angle_x, 0, $x, $y);
    }
    function SkewY($angle_y, $x='', $y=''){
        $this->Skew(0, $angle_y, $x, $y);
    }
    function Skew($angle_x, $angle_y, $x='', $y=''){
        if($x === '')
            $x=$this->x;
        if($y === '')
            $y=$this->y;
        if($angle_x <= -90 || $angle_x >= 90 || $angle_y <= -90 || $angle_y >= 90)
            $this->Error('Please use values between -90° and 90° for skewing');
        $x*=$this->k;
        $y=($this->h-$y)*$this->k;
        //calculate elements of transformation matrix
        $tm[0]=1;
        $tm[1]=tan(deg2rad($angle_y));
        $tm[2]=tan(deg2rad($angle_x));
        $tm[3]=1;
        $tm[4]=-$tm[2]*$y;
        $tm[5]=-$tm[1]*$x;
        //skew the coordinate system
        $this->Transform($tm);
    }

    function Transform($tm){
        $this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm', $tm[0],$tm[1],$tm[2],$tm[3],$tm[4],$tm[5]));
    }

    function StopTransform(){
        //restore previous graphic state
        $this->_out('Q');
    }
		
	/*
	
		Custom Function
	
	*/
	
	//Does the Bold Text
	function BoldText($data,$intQColorR = 0,$intQColorG = 0,$intQColorB = 0)
	{
		$this->SetTextColor($intQColorR,$intQColorG,$intQColorB);
		$this->SetFont('Arial','B',12);
		$this->WriteHTML($data);
		$this->SetTextColor(0,0,0);	
	}//end of BoldText()
	
	//Does the Forms Name
	function FormName($data)
	{
		$this->SetFont('Arial','',15);
		$this->SetTextColor(225,80,22);
		$this->WriteHTML($data);
		$this->SetTextColor(0,0,0);
	}//end of FormName()
	
	//formats the Section Name for the Header
	function FormatHeader($data,$intFormatColorR = -1,$intFormatColorG = -1,$intFormatColorB = -1)
	{
		//checks if the user whats to change the color to the Solutions Color
		if($intColorR > -1)
			$this->SetTextColor($intFormatColorR,$intFormatColorG,$intFormatColorB);
		
		$this->SetFont('Arial','B',15);
		$this->Cell(0,0, $data);
		$this->SetTextColor(0,0,0);	
	}//end of FormatHeader()
	
	//formats the Section Name for the Header
	function FormatSectionHeader($data,$intFormatColorR = -1,$intFormatColorG = -1,$intFormatColorB = -1)
	{
		//checks if the user whats to change the color to the Solutions Color
		if($intColorR > -1)
			$this->SetTextColor($intFormatColorR,$intFormatColorG,$intFormatColorB);
		
		$this->SetFont('Arial','B',20);
		$this->Cell(0,0, $data);
		$this->SetTextColor(0,0,0);	
	}//end of FormatSectionHeader()
		
	//Does the Page were just the Title is display
	function PageTitlePage($data,$intYAxe = 75)
	{
		$this->SetFont('Arial','B',40);
		$this->SetY($intYAxe);
		$this->WriteHTML($data);
		$this->SetTextColor(0,0,0);
	}//end of PageTitlePage()
	
	//Does the Questions
	function Questions($data,$intQColorR = 0,$intQColorG = 0,$intQColorB = 0)
	{
		$this->SetTextColor($intQColorR,$intQColorG,$intQColorB);
		$this->SetFont('Arial','BI',14);
		$this->WriteHTML($data);
		$this->SetTextColor(0,0,0);	
	}//end of Questions()
	
	//Does the Questions Body also display the awnsers along side the questions for a smaller font
	function QuestionsSmallBody($data,$intQColorR = 0,$intQColorG = 0,$intQColorB = 0)
	{
		$this->SetTextColor($intQColorR,$intQColorG,$intQColorB);
		$this->SetFont('Arial','',10);
		$this->WriteHTML($data);
		$this->SetTextColor(0,0,0);	
	}//end of QuestionsBody()
	
	//Does the Questions Body also display the awnsers along side the questions
	function QuestionsBody($data,$intQColorR = 0,$intQColorG = 0,$intQColorB = 0)
	{
		$this->SetTextColor($intQColorR,$intQColorG,$intQColorB);
		$this->SetFont('Arial','',12);
		$this->WriteHTML($data);
		$this->SetTextColor(0,0,0);	
	}//end of QuestionsBody()
		
	//Does the Sub Questions
	function SubQuestions($data,$intQColorR = 0,$intQColorG = 0,$intQColorB = 0)
	{
		$this->SetTextColor($intQColorR,$intQColorG,$intQColorB);
		$this->SetFont('Arial','BI',13);
		$this->WriteHTML($data);
		$this->SetTextColor(0,0,0);	
	}//end of SubQuestions()
}//end of class PDF

$pdf=new PDF();

// Title Page

//sets the title and author of the page
$pdf->SetTitle("My Continuity Plans: ".$strEdition." Edition");
$pdf->SetAuthor(getUserName());

//creates the page to be used
$pdf->Open();
$pdf->AddPage();

//creates the large rect that is on the left side
$pdf->SetFillColor(185,13,37);
$pdf->Rect(0,0,43.6,224,'F');

//goes around creating the 5 section with the colors and names Title Page for the right side
for($intForIndex = 1;$intForIndex < 6;$intForIndex++)
{
	//Start Transformation
	$pdf->StartTransform();
	
	//Rotates the text by 270 degrees to be on the side
	$pdf->Rotate(270,50,60);

	//finds which section the user is currently on and changes the color to the section color
	switch($intForIndex)
	{
		case 1://basic color
			$pdf->SetFillColor(6,145,52);
			$pdf->Rect(-13,-100,60,6,'F');
			//$pdf->Text(-5,-95,"Continuity Operations");
			break;
		case 2://standard color 
			$pdf->SetFillColor(185,13,37);
			$pdf->Rect(48,-100,60,6,'F');
			//$pdf->Text(-5,-95,"Disaster Response");
			break;
		case 3://Contnuity color
			$pdf->SetFillColor(225,80,22);
			$pdf->Rect(109,-100,60,6,'F');
			//$pdf->Text(-5,-95,"Disaster Recovery");
			break;
		case 4://Enterprise color
			$pdf->SetFillColor(71,143,191);
			$pdf->Rect(170,-100,60,6,'F');
			//$pdf->Text(-5,-95,"Disaster Restoration");
			break;
		case 5://Glossary
			$pdf->SetFillColor(56,152,81);
			$pdf->Rect(231,-100,60,6,'F');
			//$pdf->Text(234,-100,"Glossary");
			break;
	}//end of switch

	//Stop Transformation
	$pdf->StopTransform();
}//end of for loop

//sets the images that goes into the center
$pdf->Image('../images/imgBusConPlans.jpg',44,0,159,198);

//Addeds the Page Title and Company,Address, Phone, Contact(Name)
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','B',14);
$pdf->SetY(201);
$pdf->SetX(45);
$pdf->BoldText($row_loginFoundUser['company']);
$pdf->SetY(206);
$pdf->SetX(45);
$pdf->BoldText($row_loginFoundUser['address']);
$pdf->SetY(211);
$pdf->SetX(45);
$pdf->BoldText($row_loginFoundUser['phone']);
$pdf->SetY(216);
$pdf->SetX(45);
$pdf->BoldText(getUserName());

$pdf->SetTextColor(185,13,37);
$pdf->SetFont('Arial','B',25);
$pdf->SetY(241);
$pdf->WriteHTML("<P ALIGN='center'>PLEASE USE THIS DOCUMENT IN<BR>THE EVENT OF A DISASTER</P>");
$pdf->SetTextColor(0,0,0);

// Table of Contents

//updates the title and both parrs of the section
$strAreaName = "Table of Contents";
$strSectionSideName = "Business Continuity Plan Layout & Set Up";
$strSectionName = "";

//sets the frist section
$intSectionArea = 1;

//creates the page to be used
$pdf->AddPage();

$pdf->SetY(18);
$pdf->SetX(0);
$pdf->FormatHeader($strEdition." Edition - Layout & Set Up",$intColorR,$intColorG,$intColorB);

//Calls the section Name which is black then the color for that section and name
$pdf->SetY(26);
$pdf->SetX(4);
$pdf->FormatHeader("Section 1 - ");
$pdf->SetY(26);
$pdf->SetX(32);
$pdf->FormatHeader("Continuity Plans",6,145,52);
$pdf->SetDrawColor(0,0,0);
$pdf->Line(0,29,201.8,29);

$pdf->Image('../images/PDFArrowInGreen.gif',4,34);
$pdf->SetY(34.8);
$pdf->SetX(12);
$pdf->BoldText("Project Scope & Objectives");

$pdf->Image('../images/PDFArrowInGreen.gif',4,39);
$pdf->SetY(39.8);
$pdf->SetX(12);
$pdf->BoldText("Employee & Emergency Contacts");

//checks if there is user is doing a Standard Solution
if ($row_loginFoundUser['Solution'] == 2)
{
	//Until the client gives me a Risk Assessment Question there will be no Risk Assesment Area
	/*$pdf->Image('../images/PDFArrowInGreen.gif',4,44);
	$pdf->SetY(44.8);
	$pdf->SetX(12);
	$pdf->BoldText("Risk Assessment & Analysis");

	$pdf->Image('../images/PDFArrowInGreen.gif',4,49);
	$pdf->SetY(49.8);
	$pdf->SetX(12);
	$pdf->BoldText("Business Impact Analysis");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,54);
	$pdf->SetY(54.8);
	$pdf->SetX(12);
	$pdf->BoldText("Information Technology Operations");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,59);
	$pdf->SetY(59.8);
	$pdf->SetX(12);
	$pdf->BoldText("Crisis Communications");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,64);
	$pdf->SetY(64.8);
	$pdf->SetX(12);
	$pdf->BoldText("Company Logistics");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,69);
	$pdf->SetY(69.8);
	$pdf->SetX(12);
	$pdf->BoldText("Alternate Locations & Suppliers");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,74);
	$pdf->SetY(74.8);
	$pdf->SetX(12);
	$pdf->BoldText("Customer Service");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,79);
	$pdf->SetY(79.8);
	$pdf->SetX(12);
	$pdf->BoldText("Salvage & Security");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,84);
	$pdf->SetY(84.8);
	$pdf->SetX(12);
	$pdf->BoldText("Environmental & Privacy");*/
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,44);
	$pdf->SetY(44.8);
	$pdf->SetX(12);
	$pdf->BoldText("Business Impact Analysis");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,49);
	$pdf->SetY(49.8);
	$pdf->SetX(12);
	$pdf->BoldText("Information Technology Operations");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,54);
	$pdf->SetY(54.8);
	$pdf->SetX(12);
	$pdf->BoldText("Crisis Communications");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,59);
	$pdf->SetY(59.8);
	$pdf->SetX(12);
	$pdf->BoldText("Company Logistics");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,64);
	$pdf->SetY(64.8);
	$pdf->SetX(12);
	$pdf->BoldText("Alternate Locations & Suppliers");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,69);
	$pdf->SetY(69.8);
	$pdf->SetX(12);
	$pdf->BoldText("Customer Service");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,74);
	$pdf->SetY(74.8);
	$pdf->SetX(12);
	$pdf->BoldText("Salvage & Security");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,79);
	$pdf->SetY(79.8);
	$pdf->SetX(12);
	$pdf->BoldText("Environmental & Privacy");
}//end of if
else
{
	//puts the basic soltuoion items where the Standard items would go
	$pdf->Image('../images/PDFArrowInGreen.gif',4,44);
	$pdf->SetY(44.8);
	$pdf->SetX(12);
	$pdf->BoldText("Information Technology Operations");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,49);
	$pdf->SetY(49.8);
	$pdf->SetX(12);
	$pdf->BoldText("Crisis Communications");

	$pdf->Image('../images/PDFArrowInGreen.gif',4,54);
	$pdf->SetY(54.8);
	$pdf->SetX(12);
	$pdf->BoldText("Company Logistics");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,59);
	$pdf->SetY(59.8);
	$pdf->SetX(12);
	$pdf->BoldText("Alternate Locations & Suppliers");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,64);
	$pdf->SetY(64.8);
	$pdf->SetX(12);
	$pdf->BoldText("Customer Service");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,69);
	$pdf->SetY(69.8);
	$pdf->SetX(12);
	$pdf->BoldText("Salvage & Security");
	
	$pdf->Image('../images/PDFArrowInGreen.gif',4,74);
	$pdf->SetY(74.8);
	$pdf->SetX(12);
	$pdf->BoldText("Environmental & Privacy");
}//end of else

//Calls the section Name which is black then the color for that section and name
$pdf->SetY(95);
$pdf->SetX(4);
$pdf->FormatHeader("Section 2 - ");
$pdf->SetY(95);
$pdf->SetX(32);
$pdf->FormatHeader("Disaster Response",185,13,37);
$pdf->Line(0,98,201.8,98);

//puts the basic soltuoion items where the Standard items would go
$pdf->Image('../images/PDFArrowInRed.gif',4,103);
$pdf->SetY(103);
$pdf->SetX(12);
$pdf->BoldText("Disaster Declaration");

//checks if there is user is doing a Standard Solution
if ($row_loginFoundUser['Solution'] == 2)
{
	//puts the basic soltuoion items where the Standard items would go
	$pdf->Image('../images/PDFArrowInRed.gif',4,108);
	$pdf->SetY(108.8);
	$pdf->SetX(12);
	$pdf->BoldText("Disaster Declaration Guidelines");
	
	//puts the basic soltuoion items where the Standard items would go
	$pdf->Image('../images/PDFArrowInRed.gif',4,113);
	$pdf->SetY(113.8);
	$pdf->SetX(12);
	$pdf->BoldText("Immediate Response Team Operations");
}//end of if
else
{
	//puts the basic soltuoion items where the Standard items would go
	$pdf->Image('../images/PDFArrowInRed.gif',4,108);
	$pdf->SetY(108.8);
	$pdf->SetX(12);
	$pdf->BoldText("Immediate Response Team Operations");
}//end of else

//Calls the section Name which is black then the color for that section and name
$pdf->SetY(124);
$pdf->SetX(4);
$pdf->FormatHeader("Section 3 - ");
$pdf->SetY(124);
$pdf->SetX(32);
$pdf->FormatHeader("Continuity Plans",225,80,22);
$pdf->Line(0,127,201.8,127);

//puts the basic soltuoion items where the Standard items would go
$pdf->Image('../images/PDFArrowInOrange.gif',4,132);
$pdf->SetY(132.8);
$pdf->SetX(12);
$pdf->BoldText("Disaster Management Team Operations");

//puts the basic soltuoion items where the Standard items would go
$pdf->Image('../images/PDFArrowInOrange.gif',4,137);
$pdf->SetY(137.8);
$pdf->SetX(12);
$pdf->BoldText("Damage Assessment Team Operations");

//puts the basic soltuoion items where the Standard items would go
$pdf->Image('../images/PDFArrowInOrange.gif',4,142);
$pdf->SetY(142.8);
$pdf->SetX(12);
$pdf->BoldText("Information Technology Recovery Team Operations");

//puts the basic soltuoion items where the Standard items would go
$pdf->Image('../images/PDFArrowInOrange.gif',4,147);
$pdf->SetY(147.8);
$pdf->SetX(12);
$pdf->BoldText("Administration Recovery Team Operations");

//puts the basic soltuoion items where the Standard items would go
$pdf->Image('../images/PDFArrowInOrange.gif',4,152);
$pdf->SetY(152.8);
$pdf->SetX(12);
$pdf->BoldText("Essential Functions Recovery Operations");

//puts the basic soltuoion items where the Standard items would go
$pdf->Image('../images/PDFArrowInOrange.gif',4,157);
$pdf->SetY(157.8);
$pdf->SetX(12);
$pdf->BoldText("Business Recovery Support Team Operations");

//Calls the section Name which is black then the color for that section and name
$pdf->SetY(169);
$pdf->SetX(4);
$pdf->FormatHeader("Section 4 - ");
$pdf->SetY(169);
$pdf->SetX(32);
$pdf->FormatHeader("Disaster Restoration Plans",71,143,191);
$pdf->Line(0,172,201.8,172);

//puts the basic soltuoion items where the Standard items would go
$pdf->Image('../images/PDFArrowInBlue.gif',4,177);
$pdf->SetY(177.8);
$pdf->SetX(12);
$pdf->BoldText("Disaster Restoration Operations");

//puts the basic soltuoion items where the Standard items would go
/*$pdf->Image('../images/PDFArrowInBlue.gif',4,177);
$pdf->SetY(177.8);
$pdf->SetX(12);
$pdf->BoldText("ServiceMaster Operations Guideline & Checklist");*/

//checks if there is user is doing a Standard Solution
if ($row_loginFoundUser['Solution'] == 2)
{
	//puts the basic soltuoion items where the Standard items would go
	$pdf->Image('../images/PDFArrowInBlue.gif',4,182);
	$pdf->SetY(182.8);
	$pdf->SetX(12);
	$pdf->BoldText("Insurance Inventory");
}//end of if

//Calls the section Name which is black then the color for that section and name
$pdf->SetY(199);
$pdf->SetX(4);
$pdf->FormatHeader("Section 5 - ");
$pdf->SetY(199);
$pdf->SetX(32);
$pdf->FormatHeader("Glossary",56,152,81);
$pdf->Line(0,202,201.8,202);

//puts the basic soltuoion items where the Standard items would go
$pdf->Image('../images/PDFArrowInGlossary.gif',4,207);
$pdf->SetY(207);
$pdf->SetX(12);
$pdf->BoldText("Terms & Generally Accepted Phrases");

$pdf->QuestionsBody("");

// Business Continuity Plans Title Page for the First Section

//updates the title and both parrs of the section
$strAreaName = "Continuity Plans";
$strSectionSideName = "Continuity Operations";
$strSectionName = "";

//creates the page to be used
$pdf->AddPage();

//displays the Name of the Page for this Title Page
$pdf->PageTitlePage("Business Continuity Plans");

// Body of the PDF

//goes around each section in arrTablesName
while($intTableIndex <= count($arrTablesName))
{
	//checks if the value in $arrTablesName is a table name as some there are Extra pages
	if(strstr($arrTablesName[$intTableIndex],"EX") === FALSE)
	{
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsForm = mysql_query("SELECT * FROM ".$arrTablesName[$intTableIndex]." WHERE UserID =".$UserID, $conContinuty) or die(mysql_error());
		$row_rsForm = mysql_fetch_assoc($rsForm);
		$total_rsForm = mysql_num_rows($rsForm);
	}//end of if
		
	//gets the next section for its ID
	//does a selection for the users data for this form
	mysql_select_db($database_conContinuty, $conContinuty); 
	$rsPlans = mysql_query("SELECT * FROM continuityplans WHERE TableName = '".$arrTablesName[$intTableIndex]."'", $conContinuty) or die(mysql_error());
	$row_rsPlans = mysql_fetch_assoc($rsPlans);

	//checks if the step number has changing meaning the page is on a differnt section and if so 
	//updates $intSectionArea and resets $intSectionTableIndex
	if($intSectionArea != $row_rsPlans['stepNum'])
	{
		$intSectionArea =  $row_rsPlans['stepNum'];
		$intSectionTableIndex = 1;
		
		//updates the title and both parrs of the section
		$strAreaName = $arrAreaName[$intSectionArea];
		$strSectionSideName = $arrSectionSideName[$intSectionArea];
		$strSectionName = "";
		
		//creates the page to be used
		$pdf->AddPage();
		
		//displays the Name of the Page for this Title Page
		$pdf->PageTitlePage($arrSectionName[$intSectionArea]);
	}//end of if
		
	//updates the title and the Sub section
	$strAreaName = $arrAreaName[$intSectionArea].$intSectionTableIndex;
	$strSectionName = "";
		
	//checks if to make sure that there the solution fits the data the user has entered
	if($arrTablesName[$intTableIndex] != "C2BusinessImpact" && $arrTablesName[$intTableIndex] != "C2InsuranceInventory" && $arrTablesName[$intTableIndex] != "EXDisasterDeclarationGuidelines")
	{
		//adds one to the Section Table Index
		$intSectionTableIndex = $intSectionTableIndex + 1;
	
		//creates the page to be used
		$pdf->AddPage();
			
		//checks if there is more then 3 words in the sectionName meaing it ineeds to be cut up
		if(strlen($row_rsPlans['sectionName']) >= 20)
		{
			$intIndex = 0;//contorls the while loop
			$strNewSectionName = "";//holds the name of the Section with newline chars 
		
			//breaks up the sectionName to the different words in order to put a newline char
			$arrSectionPieces = explode(" ", $row_rsPlans['sectionName']);
			
			while($intIndex < count($arrSectionPieces))
			{
				//checks if the $intIndex is at the fouth peice
				if($intIndex == 3 || strlen($arrSectionPieces[$intIndex]) >= 8)
					//gets the new section prices and adds the new line char
					$strNewSectionName = $strNewSectionName."<BR><BR>".$arrSectionPieces[$intIndex];
				else
					//gets the new section prices
					$strNewSectionName = $strNewSectionName." ".$arrSectionPieces[$intIndex];

				//adds to the intIndex
				$intIndex = $intIndex + 1;
			}//end of while loop

			//dispays the Section Title in the Page Title
			$pdf->PageTitlePage("<P align='center'>".$strNewSectionName."</P>");
		}//end of if
		else
			//dispays the Section Title in the Page Title
			$pdf->PageTitlePage("<P align='center'>".$row_rsPlans['sectionName']."</P>");
				
		//updates the Sub Section		
		$strSectionName = $row_rsPlans['sectionName'];
		
		//sets the Form Name
		//$pdf->FormName("<P ALIGN='left'>".$row_rsPlans['sectionName']."<BR><BR></P>");
		
		//Speical case when it comes to EXOperations the Area and Section Name are differnet to start th
		if($arrTablesName[$intTableIndex] == "EXOperations")
		{
			//updates the Sub Section
			$strSectionName = "Phase 1 - Business Disruption";

			//updates the title and the Sub Section	
			$strAreaName = "Disaster Declaration Phase 1";
		}//end of if
	
		//creates the page to be used
		$pdf->AddPage();
	}//end of if
	
	//Scope 
	if($intTableIndex == 1)
	{
		$pdf->Questions("<P align='left'>Business Continuity Planning:<BR><BR></P>",6,145,52);
		$pdf->QuestionsBody("<P align='left'>Business Continuity Planning is about identifying, protecting and recovering key business operations, equipment and assets. The planning process will give an overview of each area of your organization and how it operates. In the event of a disaster this guide can be used to restore each area of business back to its original state.<BR><BR>The following Continuity Plans will outline and review each of the listed areas of business below. Within each area you will have an outlined description of how each area operates so you are able to understand you requirements in terms of Recovering from a disaster.<BR><BR></P>");
		$pdf->Questions("<P align='left'>Section 1: Introduction, Project Scope & Objectives<BR>Section 2: Employee & Emergency Contacts<BR>Section 3: Information Technology Operations<BR>Section 4: Communication Operations<BR>Section 5: Logistics<BR>Section 6: Alternate Location & Suppliers<BR>Section 7: Salvage & Security<BR>Section 8: Customer Service Operations<BR>Section 9: Environment & Privacy<BR><BR></P>",6,145,52);
		$pdf->QuestionsBody("<P align='left'>The Business Continuity Plan for ".$row_rsForm['intro_01']." located ".$row_rsForm['intro_02'].",".$row_rsForm['intro_03']." presents a management framework for command and control, establishes operational procedures to sustain essential activities if normal operations are not feasible and guides the restoration of the building’s full functions. The plan provides for attaining operational capability within 2-12 hours and sustaining operations for 30 days or longer in the event of an outage or disaster affecting the ".$row_rsForm['intro_04']." Regional Area.<BR><BR>The plan is divided into Five elements:
		<BR><BR>(1) Continuity Plans </P>");
		$pdf->QuestionsBody("<P align='left'>(GREEN) </P>",6,145,52);
		$pdf->QuestionsBody("<P align='left'>provides basic information about the organization;<BR>(2) Disaster Response </P>");
		$pdf->QuestionsBody("<P align='left'>(RED) </P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>are the operational details of how the BCP Plan will function during an emergency and;<BR>(3) Disaster Recovery </P>");
		$pdf->QuestionsBody("<P align='left'>(ORANGE) </P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>provides recovery strategies for all business elements.<BR>(4) Disaster Restoration </P>");
		$pdf->QuestionsBody("<P align='left'>(BLUE) </P>",71,143,191);
		$pdf->QuestionsBody("<P align='left'>A detailed documentation of restoration procedures and identification of physical assets.<BR>(5) Glossary </P>");
		$pdf->QuestionsBody("<P align='left'>(LIGHTGREEN) </P>",56,152,81);
		$pdf->QuestionsBody("<P align='left'>A basic outline of general terms and phrases<BR><BR></P>
<P align='left'>
			1. Provide for the continuation of the organization’s essential functions and operations<BR>
				2. Identify and protect essential equipment, records, and other assets<BR>
				3. Assess and minimize damage and losses<BR>
				4. Provide organizational and operational stability<BR>
				5. Facilitate decision-making during an emergency<BR>
				6. Achieve an orderly recovery from emergency operations.<BR><BR>".$row_rsForm['intro_01']." is required to have the capability to maintain continuous operations. Each ".$row_rsForm['intro_01']."  organizational element therefore must be prepared to continue to function during an emergency or threat of an emergency, and to efficiently and effectively resume critical operations if they are interrupted. Planning for meeting the demands of a wide spectrum of emergency scenarios is necessary, and is accomplished by developing continuity of operations plans. This plan is to identify emergency personnel and outline the course of action to be taken during an emergency.<BR><BR></P>");
		
		$pdf->Questions("<P align='left'>Planning Objectives & Assumptions:<BR><BR></P>",6,145,52);
		$pdf->QuestionsBody("<P align='left'>The business environment is constantly evolving. Emergencies and events that can impact the ability to satisfy an organization’s operational mission are based on a combination of assumptions. An additional consideration when developing your organization’s planning assumptions is the geographic location of the business unit. An organization may have facilities or business units in different buildings within a city or located at different regions of the country. In any case, assumptions will be specific to the location for which the plan is developed.<BR><BR>Based on the summary of ".$row_rsForm['intro_01']." it is the goal of the organization to:<BR><BR>".$row_rsForm['intro_07']."<BR><BR>It is also the objective of ".$row_rsForm['intro_01']." to ensure that:<BR><BR>".$row_rsForm['intro_08']."</P>");

		//gets the name of the Business as it is used thought the PDF
		$strBusName = $row_rsForm['intro_01'];
	}//end of if
	
	//Employee 
	if($intTableIndex == 2)
	{
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsForm2 = mysql_query("SELECT * FROM C2Employee2 WHERE C2ID= ".$row_rsForm['C2ID'], $conContinuty) or die("C2Employee: ".mysql_error());
		$row_Form2 = mysql_fetch_assoc($rsForm2);
		$total_Form2 = mysql_num_rows($rsForm2);
		
		$pdf->Questions("<P align='left'>Business Continuity Coordinator:<BR><BR></P>",6,145,52);
		$pdf->QuestionsBody("<P align='left'>The following section will outline the employees roles and responsibilities for the completion of your Business Continuity Plan.<BR><BR>Business Continuity Coordinator: Will be responsible for accurate and timely delivery of information concerning to continuity of this organization.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Your Business Continuity Coordinator:<BR><BR>Name: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['contact_001']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Job Title: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['contact_002']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phone: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['contact_003']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Cell: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['contact_004']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>E-Mail: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['contact_005']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Team 1: Business Continuity Plan Operations Team<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>This individual or individuals will be responsible for assisting in completing these on-line forms. Should you require any additional information that needs to be collected you will be able to use these individual who have an extensive knowledge of the business.
<BR><BR>
Name: ".$row_rsForm['contact_006']." Job Title : ".$row_rsForm['contact_007']." Phone: ".$row_rsForm['contact_008']." Cell : ".$row_rsForm['contact_009']." E-Mail : ".$row_rsForm['contact_010']."<BR>
Name: ".$row_rsForm['contact_011']." Job Title : ".$row_rsForm['contact_012']." Phone: ".$row_rsForm['contact_013']." Cell : ".$row_rsForm['contact_014']." E-Mail : ".$row_rsForm['contact_015']."<BR>
Name: ".$row_rsForm['contact_016']." Job Title : ".$row_rsForm['contact_017']." Phone: ".$row_rsForm['contact_018']." Cell : ".$row_rsForm['contact_019']." E-Mail : ".$row_rsForm['contact_020']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Team 2: Immediate Disaster Response Team<BR><BR></P>");
		$pdf->QuestionsBody("This individual or individuals will be the immediate response team members that will begin to perform pre-assigned tasks immediately after a disaster or disruption has occurred.
<BR><BR>
Name: ".$row_rsForm['contact_021']." Job Title : ".$row_rsForm['contact_022']." Phone: ".$row_rsForm['contact_023']." Cell : ".$row_rsForm['contact_024']." E-Mail : ".$row_rsForm['contact_025']."<BR>
Name: ".$row_rsForm['contact_026']." Job Title : ".$row_rsForm['contact_027']." Phone: ".$row_rsForm['contact_028']." Cell : ".$row_rsForm['contact_029']." E-Mail : ".$row_rsForm['contact_030']."<BR>
Name: ".$row_rsForm['contact_031']." Job Title : ".$row_rsForm['contact_032']." Phone: ".$row_rsForm['contact_033']." Cell : ".$row_rsForm['contact_034']." E-Mail : ".$row_rsForm['contact_035']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Team 3: Disaster Management Team<BR><BR></P>");
		$pdf->QuestionsBody("This individual or individuals should have at least one person from management on it. The team will operate as the decision makers in the recovery process. In the event of a disaster this team will assemble and be prepared to receive all incoming information and make decision accordingly.
<BR><BR>
Name: ".$row_rsForm['contact_036']." Job Title : ".$row_rsForm['contact_037']." Phone: ".$row_rsForm['contact_038']." Cell : ".$row_rsForm['contact_039']." E-Mail : ".$row_rsForm['contact_040']."<BR>
Name: ".$row_rsForm['contact_041']." Job Title : ".$row_rsForm['contact_042']." Phone: ".$row_rsForm['contact_043']." Cell : ".$row_rsForm['contact_044']." E-Mail : ".$row_rsForm['contact_045']."<BR>
Name: ".$row_rsForm['contact_046']." Job Title : ".$row_rsForm['contact_047']." Phone: ".$row_rsForm['contact_048']." Cell : ".$row_rsForm['contact_049']." E-Mail : ".$row_rsForm['contact_050']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Team 4: Damage Assessment Team<BR><BR></P>");
		$pdf->QuestionsBody("This individual or team of individuals will have the responsibilities of performing a damage assessment of your business should a disaster or disruption occur. Once they have completed the assessment they will report all findings back to the Disaster Management Team.
<BR><BR>
Name: ".$row_rsForm['contact_051']." Job Title : ".$row_rsForm['contact_052']." Phone: ".$row_rsForm['contact_053']." Cell : ".$row_rsForm['contact_054']." E-Mail : ".$row_rsForm['contact_055']."<BR>
Name: ".$row_rsForm['contact_056']." Job Title : ".$row_rsForm['contact_057']." Phone: ".$row_rsForm['contact_058']." Cell : ".$row_rsForm['contact_059']." E-Mail : ".$row_rsForm['contact_060']."<BR>
Name: ".$row_rsForm['contact_061']." Job Title : ".$row_rsForm['contact_062']." Phone: ".$row_rsForm['contact_063']." Cell : ".$row_rsForm['contact_064']." E-Mail : ".$row_rsForm['contact_065']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Team 5: Information Technology Recovery Team<BR><BR></P>");
		$pdf->QuestionsBody("This individual or team of individuals should have an extensive knowledge of your business and it's IT infrastructure. In the event of a disaster their main goal will be to ensure all IT components of your business are recovered and restored.
<BR><BR>
Name: ".$row_rsForm['contact_066']." Job Title : ".$row_rsForm['contact_067']." Phone: ".$row_rsForm['contact_068']." Cell : ".$row_rsForm['contact_069']." E-Mail : ".$row_rsForm['contact_070']."<BR>
Name: ".$row_rsForm['contact_071']." Job Title : ".$row_rsForm['contact_072']." Phone: ".$row_rsForm['contact_073']." Cell : ".$row_rsForm['contact_074']." E-Mail : ".$row_rsForm['contact_075']."<BR>
Name: ".$row_rsForm['contact_076']." Job Title : ".$row_rsForm['contact_077']." Phone: ".$row_rsForm['contact_078']." Cell : ".$row_rsForm['contact_079']." E-Mail : ".$row_rsForm['contact_080']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Team 6: Administration Recovery Team<BR><BR></P>");
		$pdf->QuestionsBody("This individual or team of individuals should be composed of individuals that have knowledge of the administrative functions of your business. In the event of a disaster they will be responsible for ensuring that all functions continue to operate.
<BR><BR>
Name: ".$row_rsForm['contact_081']." Job Title : ".$row_rsForm['contact_082']." Phone: ".$row_rsForm['contact_083']." Cell : ".$row_rsForm['contact_084']." E-Mail : ".$row_rsForm['contact_085']."<BR>
Name: ".$row_rsForm['contact_086']." Job Title : ".$row_rsForm['contact_087']." Phone: ".$row_rsForm['contact_088']." Cell : ".$row_rsForm['contact_089']." E-Mail : ".$row_rsForm['contact_090']."<BR>
Name: ".$row_rsForm['contact_091']." Job Title : ".$row_rsForm['contact_092']." Phone: ".$row_rsForm['contact_093']." Cell : ".$row_rsForm['contact_094']." E-Mail : ".$row_rsForm['contact_095']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Team 7: Essential Functions Recovery Team<BR><BR></P>");
		$pdf->QuestionsBody("This individual or team of individuals should be knowledgeable in the specific essential functions that you business performs on a day to day basis. In the event of a disaster or disruption this team will be responsible for ensuring this essential function is fully operational.
<BR><BR>
Name: ".$row_rsForm['contact_096']." Job Title : ".$row_rsForm['contact_097']." Phone: ".$row_rsForm['contact_098']." Cell : ".$row_rsForm['contact_099']." E-Mail : ".$row_rsForm['contact_100']."<BR>
Name: ".$row_rsForm['contact_101']." Job Title : ".$row_rsForm['contact_102']." Phone: ".$row_rsForm['contact_103']." Cell : ".$row_rsForm['contact_104']." E-Mail : ".$row_rsForm['contact_105']."<BR>
Name: ".$row_rsForm['contact_106']." Job Title : ".$row_rsForm['contact_107']." Phone: ".$row_rsForm['contact_108']." Cell : ".$row_rsForm['contact_109']." E-Mail : ".$row_rsForm['contact_110']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Team 8: Business Recovery Support Team<BR><BR></P>");
		$pdf->QuestionsBody("This individual or team of individuals will provide services to all critical operations and functions of your business. In the event of a disaster or disruption this team will ensure support is delivered to all essential areas of your business.
<BR><BR>
Name: ".$row_rsForm['contact_111']." Job Title : ".$row_rsForm['contact_112']." Phone: ".$row_rsForm['contact_113']." Cell : ".$row_rsForm['contact_114']." E-Mail : ".$row_rsForm['contact_115']."<BR>
Name: ".$row_rsForm['contact_116']." Job Title : ".$row_rsForm['contact_117']." Phone: ".$row_rsForm['contact_118']." Cell : ".$row_rsForm['contact_119']." E-Mail : ".$row_rsForm['contact_120']."<BR>
Name: ".$row_rsForm['contact_121']." Job Title : ".$row_rsForm['contact_122']." Phone: ".$row_rsForm['contact_123']." Cell : ".$row_rsForm['contact_124']." E-Mail : ".$row_rsForm['contact_125']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Sr. Management - Executive Decision Makers<BR><BR></P>");
		$pdf->QuestionsBody("In ever disaster there needs to be a Sr. Personnel that is able to make final decisions in terms of recovery. This individual will make all final decisions and will be consulted in the event any team needs to know the next plan of action. All decision and final says will be left up to this Sr. Management member.
<BR><BR>
Name: ".$row_rsForm['contact_126']." Job Title : ".$row_rsForm['contact_127']." Phone: ".$row_rsForm['contact_128']." Cell : ".$row_rsForm['contact_129']." E-Mail : ".$row_rsForm['contact_130']."<BR>
Name: ".$row_rsForm['contact_131']." Job Title : ".$row_rsForm['contact_132']." Phone: ".$row_rsForm['contact_133']." Cell : ".$row_rsForm['contact_134']." E-Mail : ".$row_rsForm['contact_135']."<BR>
Name: ".$row_rsForm['contact_136']." Job Title : ".$row_rsForm['contact_137']." Phone: ".$row_rsForm['contact_138']." Cell : ".$row_rsForm['contact_139']." E-Mail : ".$row_rsForm['contact_140']."
<BR><BR>
".$strBusName." Complete Employee Contact List
<BR><BR>
The following is a list of all employees who currently work at ".$strBusName.".
<BR><BR>
1. Name:".$row_rsForm['contact_141']." Title: ".$row_rsForm['contact_142']." Phone: ".$row_rsForm['contact_143']." Cell: ".$row_rsForm['contact_144']." E-Mail: ".$row_rsForm['contact_145']."<BR>
2. Name: ".$row_rsForm['contact_146']." Title: ".$row_rsForm['contact_147']." Phone: ".$row_rsForm['contact_148']." Cell: ".$row_rsForm['contact_149']." E-Mail: ".$row_rsForm['contact_150']."<BR>
3. Name: ".$row_rsForm['contact_151']." Title: ".$row_rsForm['contact_152']." Phone: ".$row_rsForm['contact_153']." Cell: ".$row_rsForm['contact_154']." E-Mail: ".$row_rsForm['contact_155']."<BR>
4. Name: ".$row_rsForm['contact_156']." Title: ".$row_rsForm['contact_157']." Phone: ".$row_rsForm['contact_158']." Cell: ".$row_rsForm['contact_159']." E-Mail: ".$row_rsForm['contact_160']."<BR>
5. Name: ".$row_rsForm['contact_161']." Title: ".$row_rsForm['contact_162']." Phone: ".$row_rsForm['contact_163']." Cell: ".$row_rsForm['contact_164']." E-Mail: ".$row_rsForm['contact_165']."<BR>
6. Name: ".$row_rsForm['contact_166']." Title: ".$row_rsForm['contact_167']." Phone: ".$row_rsForm['contact_168']."Cell: ".$row_rsForm['contact_169']." E-Mail: ".$row_rsForm['contact_170']."<BR>
7. Name: ".$row_rsForm['contact_171']." Title: ".$row_rsForm['contact_172']." Phone: ".$row_rsForm['contact_173']." Cell: ".$row_rsForm['contact_174']." E-Mail: ".$row_rsForm['contact_175']."<BR>
8. Name: ".$row_rsForm['contact_176']." Title: ".$row_rsForm['contact_177']." Phone: ".$row_rsForm['contact_178']." Cell: ".$row_rsForm['contact_179']." E-Mail: ".$row_rsForm['contact_180']."<BR>
9. Name: ".$row_rsForm['contact_181']." Title: ".$row_rsForm['contact_182']." Phone: ".$row_rsForm['contact_183']." Cell: ".$row_rsForm['contact_184']." E-Mail: ".$row_rsForm['contact_185']."<BR>
10. Name: ".$row_rsForm['contact_186']." Title: ".$row_rsForm['contact_187']." Phone: ".$row_rsForm['contact_188']." Cell: ".$row_rsForm['contact_189']." E-Mail: ".$row_rsForm['contact_190']."<BR>
11. Name: ".$row_rsForm['contact_191']." Title: ".$row_rsForm['contact_192']." Phone: ".$row_rsForm['contact_193']." Cell: ".$row_rsForm['contact_194']." E-Mail: ".$row_rsForm['contact_195']."<BR>
12. Name: ".$row_rsForm['contact_196']." Title: ".$row_rsForm['contact_197']." Phone: ".$row_rsForm['contact_198']." Cell: ".$row_rsForm['contact_199']." E-Mail: ".$row_rsForm['contact_200']."<BR>
13. Name: ".$row_rsForm['contact_201']." Title: ".$row_rsForm['contact_202']." Phone: ".$row_rsForm['contact_203']." Cell: ".$row_rsForm['contact_204']." E-Mail: ".$row_rsForm['contact_205']."<BR>
14. Name: ".$row_rsForm['contact_206']." Title: ".$row_rsForm['contact_207']." Phone: ".$row_rsForm['contact_208']." Cell: ".$row_rsForm['contact_209']." E-Mail: ".$row_rsForm['contact_210']."<BR>
15. Name: ".$row_rsForm['contact_211']." Title: ".$row_rsForm['contact_212']." Phone: ".$row_rsForm['contact_213']." Cell: ".$row_rsForm['contact_214']." E-Mail: ".$row_rsForm['contact_215']."<BR>
16. Name: ".$row_rsForm['contact_216']." Title: ".$row_rsForm['contact_217']." Phone: ".$row_rsForm['contact_218']." Cell: ".$row_rsForm['contact_219']." E-Mail: ".$row_rsForm['contact_220']."<BR>
17. Name: ".$row_rsForm['contact_221']." Title: ".$row_rsForm['contact_222']." Phone: ".$row_rsForm['contact_223']." Cell: ".$row_rsForm['contact_224']." E-Mail: ".$row_rsForm['contact_225']."<BR>
18. Name: ".$row_rsForm['contact_226']." Title: ".$row_rsForm['contact_227']." Phone: ".$row_rsForm['contact_228']." Cell: ".$row_rsForm['contact_229']." E-Mail: ".$row_rsForm['contact_230']."<BR>
19. Name: ".$row_rsForm['contact_231']." Title: ".$row_rsForm['contact_232']." Phone: ".$row_rsForm['contact_233']." Cell: ".$row_rsForm['contact_234']." E-Mail: ".$row_rsForm['contact_235']."<BR>
20. Name: ".$row_rsForm['contact_236']." Title: ".$row_rsForm['contact_237']." Phone: ".$row_rsForm['contact_238']." Cell: ".$row_rsForm['contact_239']." E-Mail:".$row_rsForm['contact_240']."</P>");

		// this will display only for Standard
		if ($row_loginFoundUser['Solution'] == 2)
		{
			$pdf->QuestionsBody("<BR>
<P align='left'>21. Name: ".$row_rsForm['contact_375']." Title: ".$row_rsForm['contact_376']." Phone: ".$row_rsForm['contact_377']." Cell: ".$row_rsForm['contact_378']." E-Mail: ".$row_rsForm['contact_379']."<BR>
22. Name: ".$row_rsForm['contact_380']." Title: ".$row_rsForm['contact_381']." Phone: ".$row_rsForm['contact_382']." Cell: ".$row_rsForm['contact_383']." E-Mail: ".$row_rsForm['contact_384']."<BR>
23. Name: ".$row_rsForm['contact_385']." Title: ".$row_rsForm['contact_386']." Phone:".$row_rsForm['contact_387']." Cell: ".$row_rsForm['contact_388']." E-Mail: ".$row_rsForm['contact_389']."<BR>
24. Name: ".$row_rsForm['contact_390']." Title: ".$row_rsForm['contact_391']." Phone: ".$row_rsForm['contact_392']." Cell: ".$row_rsForm['contact_393']." E-Mail: ".$row_rsForm['contact_394']."<BR>
25. Name: ".$row_rsForm['contact_395']." Title: ".$row_rsForm['contact_396']." Phone: ".$row_rsForm['contact_397']." Cell: ".$row_rsForm['contact_398']." E-Mail: ".$row_rsForm['contact_399']."<BR>
26. Name: ".$row_rsForm2['contact_400']." Title: ".$row_rsForm['contact_401']." Phone: ".$row_rsForm['contact_402']." Cell: ".$row_rsForm['contact_403']." E-Mail: ".$row_rsForm['contact_404']."<BR>
27. Name: ".$row_rsForm['contact_405']." Title: ".$row_rsForm['contact_406']." Phone: ".$row_rsForm['contact_407']." Cell: ".$row_rsForm['contact_408']." E-Mail: ".$row_rsForm['contact_409']."<BR>
28. Name: ".$row_rsForm['contact_410']." Title: ".$row_rsForm['contact_411']." Phone: ".$row_rsForm['contact_412']." Cell: ".$row_rsForm['contact_413']." E-Mail: ".$row_rsForm['contact_414']."<BR>
29. Name: ".$row_rsForm['contact_415']." Title: ".$row_rsForm['contact_416']." Phone: ".$row_rsForm['contact_417']." Cell: ".$row_rsForm['contact_418']." E-Mail: ".$row_rsForm['contact_419']."<BR>
30. Name: ".$row_rsForm['contact_420']." Title: ".$row_rsForm['contact_421']." Phone: ".$row_rsForm['contact_422']." Cell: ".$row_rsForm['contact_423']." E-Mail: ".$row_rsForm['contact_424']."<BR>
31. Name: ".$row_rsForm['contact_425']." Title: ".$row_rsForm['contact_426']." Phone: ".$row_rsForm['contact_427']." Cell: ".$row_rsForm['contact_428']." E-Mail: ".$row_rsForm['contact_429']."<BR>
32. Name: ".$row_rsForm['contact_430']." Title: ".$row_rsForm['contact_431']." Phone: ".$row_rsForm['contact_432']." Cell: ".$row_rsForm['contact_433']." E-Mail: ".$row_rsForm['contact_434']."<BR>
33. Name: ".$row_rsForm['contact_435']." Title: ".$row_rsForm['contact_436']." Phone: ".$row_rsForm['contact_437']." Cell: ".$row_rsForm['contact_438']." E-Mail: ".$row_rsForm['contact_439']."<BR>
34. Name: ".$row_rsForm['contact_440']." Title: ".$row_rsForm['contact_441']." Phone: ".$row_rsForm['contact_442']." Cell: ".$row_rsForm['contact_443']." E-Mail: ".$row_rsForm['contact_444']."<BR>
35. Name: ".$row_rsForm['contact_445']." Title: ".$row_rsForm['contact_446']." Phone: ".$row_rsForm['contact_447']." Cell: ".$row_rsForm['contact_448']." E-Mail: ".$row_rsForm['contact_449']."<BR>
36. Name: ".$row_rsForm['contact_450']." Title: ".$row_rsForm['contact_451']." Phone: ".$row_rsForm['contact_452']." Cell: ".$row_rsForm['contact_453']." E-Mail: ".$row_rsForm['contact_454']."<BR>
37. Name: ".$row_rsForm['contact_455']." Title: ".$row_rsForm['contact_456']." Phone: ".$row_rsForm['contact_457']." Cell: ".$row_rsForm['contact_458']." E-Mail: ".$row_rsForm['contact_459']."<BR>
38. Name: ".$row_rsForm['contact_460']." Title: ".$row_rsForm['contact_461']." Phone: ".$row_rsForm['contact_462']." Cell: ".$row_rsForm['contact_463']." E-Mail: ".$row_rsForm['contact_464']."<BR>
39. Name: ".$row_rsForm['contact_465']." Title: ".$row_rsForm['contact_466']." Phone: ".$row_rsForm['contact_467']." Cell: ".$row_rsForm['contact_468']." E-Mail: ".$row_rsForm['contact_469']."<BR>
40. Name: ".$row_rsForm['contact_470']." Title: ".$row_rsForm['contact_471']." Phone: ".$row_rsForm['contact_472']." Cell: ".$row_rsForm['contact_473']." E-Mail: ".$row_rsForm['contact_474']."<BR>
41. Name: ".$row_rsForm['contact_475']." Title: ".$row_rsForm['contact_476']." Phone: ".$row_rsForm['contact_477']." Cell: ".$row_rsForm['contact_478']." E-Mail: ".$row_rsForm['contact_479']."<BR>
42. Name: ".$row_rsForm['contact_480']." Title: ".$row_rsForm['contact_481']." Phone: ".$row_rsForm['contact_482']." Cell: ".$row_rsForm['contact_483']." E-Mail: ".$row_rsForm['contact_484']."<BR>
43. Name: ".$row_rsForm['contact_485']." Title: ".$row_rsForm['contact_486']." Phone: ".$row_rsForm['contact_487']." Cell: ".$row_rsForm['contact_488']." E-Mail: ".$row_rsForm['contact_489']."<BR>
44. Name: ".$row_rsForm['contact_490']." Title: ".$row_rsForm['contact_491']." Phone: ".$row_rsForm['contact_492']." Cell: ".$row_rsForm['contact_493']." E-Mail: ".$row_rsForm['contact_494']."<BR>
45. Name: ".$row_rsForm['contact_495']." Title: ".$row_rsForm['contact_496']." Phone: ".$row_rsForm['contact_497']." Cell: ".$row_rsForm['contact_498']." E-Mail: ".$row_rsForm['contact_499']."<BR>
46. Name: ".$row_rsForm['contact_500']." Title: ".$row_rsForm['contact_501']." Phone: ".$row_rsForm['contact_502']." Cell: ".$row_rsForm['contact_503']." E-Mail: ".$row_rsForm['contact_504']."<BR>
47. Name: ".$row_rsForm['contact_505']." Title: ".$row_rsForm['contact_506']." Phone: ".$row_rsForm['contact_507']." Cell: ".$row_rsForm['contact_508']." E-Mail: ".$row_rsForm['contact_509']."<BR>
48. Name: ".$row_rsForm['contact_510']." Title: ".$row_rsForm['contact_511']." Phone: ".$row_rsForm['contact_512']." Cell: ".$row_rsForm['contact_513']." E-Mail: ".$row_rsForm['contact_514']."<BR>
49. Name: ".$row_rsForm['contact_515']." Title: ".$row_rsForm['contact_516']." Phone: ".$row_rsForm['contact_517']." Cell: ".$row_rsForm['contact_518']." E-Mail: ".$row_rsForm['contact_519']."<BR>
50. Name: ".$row_rsForm['contact_520']." Title: ".$row_rsForm['contact_521']." Phone: ".$row_rsForm['contact_522']." Cell: ".$row_rsForm['contact_523']." E-Mail: ".$row_rsForm['contact_524']."<BR>
51. Name: ".$row_rsForm['contact_525']." Title: ".$row_rsForm['contact_526']." Phone: ".$row_rsForm['contact_527']." Cell: ".$row_rsForm['contact_528']." E-Mail: ".$row_rsForm['contact_529']."<BR>
52. Name: ".$row_rsForm['contact_530']." Title: ".$row_rsForm['contact_531']." Phone: ".$row_rsForm['contact_532']." Cell: ".$row_rsForm['contact_533']." E-Mail: ".$row_rsForm['contact_534']."<BR>
53. Name: ".$row_rsForm['contact_535']." Title: ".$row_rsForm['contact_536']." Phone: ".$row_rsForm['contact_537']." Cell: ".$row_rsForm['contact_538']." E-Mail: ".$row_rsForm['contact_539']."<BR>
54. Name: ".$row_rsForm['contact_540']." Title: ".$row_rsForm['contact_541']." Phone: ".$row_rsForm['contact_542']." Cell: ".$row_rsForm['contact_543']." E-Mail: ".$row_rsForm['contact_544']."<BR>
55. Name: ".$row_rsForm['contact_545']." Title: ".$row_rsForm['contact_546']." Phone: ".$row_rsForm['contact_547']." Cell: ".$row_rsForm['contact_548']." E-Mail: ".$row_rsForm['contact_549']."<BR>
56. Name: ".$row_rsForm['contact_550']." Title: ".$row_rsForm['contact_551']." Phone: ".$row_rsForm['contact_552']." Cell: ".$row_rsForm['contact_553']." E-Mail: ".$row_rsForm['contact_554']."<BR>
57. Name: ".$row_rsForm['contact_555']." Title: ".$row_rsForm['contact_556']." Phone: ".$row_rsForm['contact_557']." Cell: ".$row_rsForm['contact_558']." E-Mail: ".$row_rsForm['contact_559']."<BR>
58. Name: ".$row_rsForm['contact_560']." Title: ".$row_rsForm['contact_561']." Phone: ".$row_rsForm['contact_562']." Cell: ".$row_rsForm['contact_563']." E-Mail: ".$row_rsForm['contact_564']."<BR>
59. Name: ".$row_rsForm['contact_565']." Title: ".$row_rsForm['contact_566']." Phone: ".$row_rsForm['contact_567']." Cell: ".$row_rsForm['contact_568']." E-Mail: ".$row_rsForm['contact_569']."<BR>
60. Name: ".$row_rsForm['contact_570']." Title: ".$row_rsForm['contact_571']." Phone: ".$row_rsForm['contact_572']." Cell: ".$row_rsForm['contact_573']." E-Mail: ".$row_rsForm['contact_574']."<BR>
61. Name: ".$row_rsForm['contact_575']." Title: ".$row_rsForm['contact_576']." Phone: ".$row_rsForm['contact_577']." Cell: ".$row_rsForm['contact_578']." E-Mail: ".$row_rsForm['contact_579']."<BR>
62. Name: ".$row_rsForm['contact_580']." Title: ".$row_rsForm['contact_581']." Phone: ".$row_rsForm['contact_582']." Cell: ".$row_rsForm['contact_583']." E-Mail: ".$row_rsForm['contact_584']."<BR>
63. Name: ".$row_rsForm['contact_585']." Title: ".$row_rsForm['contact_586']." Phone: ".$row_rsForm['contact_587']." Cell: ".$row_rsForm['contact_588']." E-Mail: ".$row_rsForm['contact_589']."<BR>
64. Name: ".$row_rsForm['contact_590']." Title: ".$row_rsForm['contact_591']." Phone: ".$row_rsForm['contact_592']." Cell: ".$row_rsForm['contact_593']." E-Mail: ".$row_rsForm['contact_594']."<BR>
65. Name: ".$row_rsForm['contact_595']." Title: ".$row_rsForm['contact_596']." Phone: ".$row_rsForm['contact_597']." Cell: ".$row_rsForm['contact_598']." E-Mail: ".$row_rsForm['contact_599']."<BR>
66. Name: ".$row_rsForm['contact_600']." Title: ".$row_rsForm['contact_601']." Phone: ".$row_rsForm['contact_602']." Cell: ".$row_rsForm['contact_603']." E-Mail: ".$row_rsForm['contact_604']."<BR>
67. Name: ".$row_rsForm['contact_605']." Title: ".$row_rsForm['contact_606']." Phone: ".$row_rsForm['contact_607']." Cell: ".$row_rsForm['contact_608']." E-Mail: ".$row_rsForm['contact_609']."<BR>
68. Name: ".$row_rsForm['contact_610']." Title: ".$row_rsForm['contact_611']." Phone: ".$row_rsForm['contact_612']." Cell: ".$row_rsForm['contact_613']." E-Mail: ".$row_rsForm['contact_614']."<BR>
69. Name: ".$row_rsForm['contact_615']." Title: ".$row_rsForm['contact_616']." Phone: ".$row_rsForm['contact_617']." Cell: ".$row_rsForm['contact_618']." E-Mail: ".$row_rsForm['contact_619']."<BR>
70. Name: ".$row_rsForm['contact_620']." Title: ".$row_rsForm['contact_621']." Phone: ".$row_rsForm['contact_622']." Cell: ".$row_rsForm['contact_623']." E-Mail: ".$row_rsForm['contact_624']."<BR>
71. Name: ".$row_rsForm['contact_625']." Title: ".$row_rsForm['contact_626']." Phone: ".$row_rsForm['contact_627']." Cell: ".$row_rsForm['contact_628']." E-Mail: ".$row_rsForm['contact_629']."<BR>
72. Name: ".$row_rsForm['contact_630']." Title: ".$row_rsForm['contact_631']." Phone: ".$row_rsForm['contact_632']." Cell: ".$row_rsForm['contact_633']." E-Mail: ".$row_rsForm['contact_634']."<BR>
73. Name: ".$row_rsForm['contact_635']." Title: ".$row_rsForm['contact_636']." Phone: ".$row_rsForm['contact_637']." Cell: ".$row_rsForm['contact_638']." E-Mail: ".$row_rsForm['contact_639']."<BR>
74. Name: ".$row_rsForm['contact_640']." Title: ".$row_rsForm['contact_641']." Phone: ".$row_rsForm['contact_642']." Cell: ".$row_rsForm['contact_643']." E-Mail: ".$row_rsForm['contact_644']."<BR>
75. Name: ".$row_rsForm['contact_645']." Title: ".$row_rsForm['contact_646']." Phone: ".$row_rsForm['contact_647']." Cell: ".$row_rsForm['contact_648']." E-Mail: ".$row_rsForm['contact_649']."<BR>
76. Name: ".$row_rsForm['contact_650']." Title: ".$row_rsForm['contact_651']." Phone: ".$row_rsForm['contact_652']." Cell: ".$row_rsForm['contact_653']." E-Mail: ".$row_rsForm['contact_654']."<BR>
77. Name: ".$row_rsForm['contact_655']." Title: ".$row_rsForm['contact_656']." Phone: ".$row_rsForm['contact_657']." Cell: ".$row_rsForm['contact_658']." E-Mail: ".$row_rsForm['contact_659']."<BR>
78. Name: ".$row_rsForm['contact_660']." Title: ".$row_rsForm['contact_661']." Phone: ".$row_rsForm['contact_662']." Cell: ".$row_rsForm['contact_663']." E-Mail: ".$row_rsForm['contact_664']."<BR>
79. Name: ".$row_rsForm['contact_665']." Title: ".$row_rsForm['contact_666']." Phone: ".$row_rsForm['contact_667']." Cell: ".$row_rsForm['contact_668']." E-Mail: ".$row_rsForm['contact_669']."<BR>
80. Name: ".$row_rsForm['contact_670']." Title: ".$row_rsForm['contact_671']." Phone: ".$row_rsForm['contact_672']." Cell: ".$row_rsForm['contact_673']." E-Mail: ".$row_rsForm['contact_674']."<BR>
81. Name: ".$row_rsForm['contact_675']." Title: ".$row_rsForm['contact_676']." Phone: ".$row_rsForm['contact_677']." Cell: ".$row_rsForm['contact_678']." E-Mail: ".$row_rsForm['contact_679']."<BR>
82. Name: ".$row_rsForm['contact_680']." Title: ".$row_rsForm['contact_681']." Phone: ".$row_rsForm['contact_682']." Cell: ".$row_rsForm['contact_683']." E-Mail: ".$row_rsForm['contact_684']."<BR>
83. Name: ".$row_rsForm['contact_685']." Title: ".$row_rsForm['contact_686']." Phone: ".$row_rsForm['contact_687']." Cell: ".$row_rsForm['contact_688']." E-Mail: ".$row_rsForm['contact_689']."<BR>
84. Name: ".$row_rsForm['contact_690']." Title: ".$row_rsForm['contact_691']." Phone: ".$row_rsForm['contact_692']." Cell: ".$row_rsForm['contact_693']." E-Mail: ".$row_rsForm['contact_694']."<BR>
85. Name: ".$row_rsForm['contact_695']." Title: ".$row_rsForm['contact_696']." Phone: ".$row_rsForm['contact_697']." Cell: ".$row_rsForm['contact_698']." E-Mail: ".$row_rsForm['contact_699']."<BR>
86. Name: ".$row_rsForm['contact_700']." Title: ".$row_rsForm['contact_701']." Phone: ".$row_rsForm['contact_702']." Cell: ".$row_rsForm['contact_703']." E-Mail: ".$row_rsForm['contact_704']."<BR>
87. Name: ".$row_rsForm['contact_705']." Title: ".$row_rsForm['contact_706']." Phone: ".$row_rsForm['contact_707']." Cell: ".$row_rsForm['contact_708']." E-Mail: ".$row_rsForm['contact_709']."<BR>
88. Name: ".$row_rsForm['contact_710']." Title: ".$row_rsForm['contact_711']." Phone: ".$row_rsForm['contact_712']." Cell: ".$row_rsForm['contact_713']." E-Mail: ".$row_rsForm['contact_714']."<BR>
89. Name: ".$row_rsForm['contact_715']." Title: ".$row_rsForm['contact_716']." Phone: ".$row_rsForm['contact_717']." Cell: ".$row_rsForm['contact_718']." E-Mail: ".$row_rsForm['contact_719']."<BR>
90. Name: ".$row_rsForm['contact_720']." Title: ".$row_rsForm['contact_721']." Phone: ".$row_rsForm['contact_722']." Cell: ".$row_rsForm['contact_723']." E-Mail: ".$row_rsForm['contact_724']."<BR>
91. Name: ".$row_rsForm['contact_725']." Title: ".$row_rsForm['contact_726']." Phone: ".$row_rsForm['contact_727']." Cell: ".$row_rsForm['contact_728']." E-Mail: ".$row_rsForm['contact_729']."<BR>
92. Name: ".$row_rsForm['contact_730']." Title: ".$row_rsForm['contact_731']." Phone: ".$row_rsForm['contact_732']." Cell: ".$row_rsForm['contact_733']." E-Mail: ".$row_rsForm['contact_734']."<BR>
93. Name: ".$row_rsForm['contact_735']." Title: ".$row_rsForm['contact_736']." Phone: ".$row_rsForm['contact_737']." Cell: ".$row_rsForm['contact_738']." E-Mail: ".$row_rsForm['contact_739']."<BR>
94. Name: ".$row_rsForm['contact_740']." Title: ".$row_rsForm['contact_741']." Phone: ".$row_rsForm['contact_742']." Cell: ".$row_rsForm['contact_743']." E-Mail: ".$row_rsForm['contact_744']."<BR>
95. Name: ".$row_rsForm['contact_745']." Title: ".$row_rsForm['contact_746']." Phone: ".$row_rsForm['contact_747']." Cell: ".$row_rsForm['contact_748']." E-Mail: ".$row_rsForm['contact_749']."<BR>
96. Name: ".$row_rsForm['contact_750']." Title: ".$row_rsForm['contact_751']." Phone: ".$row_rsForm['contact_752']." Cell: ".$row_rsForm['contact_753']." E-Mail: ".$row_rsForm['contact_754']."<BR>
97. Name: ".$row_rsForm['contact_755']." Title: ".$row_rsForm['contact_756']." Phone: ".$row_rsForm['contact_757']." Cell: ".$row_rsForm['contact_758']." E-Mail: ".$row_rsForm['contact_759']."<BR>
98. Name: ".$row_rsForm['contact_760']." Title: ".$row_rsForm['contact_761']." Phone: ".$row_rsForm['contact_762']." Cell: ".$row_rsForm['contact_763']." E-Mail: ".$row_rsForm['contact_764']."<BR>
99. Name: ".$row_rsForm['contact_765']." Title: ".$row_rsForm['contact_766']." Phone: ".$row_rsForm['contact_767']." Cell: ".$row_rsForm['contact_768']." E-Mail: ".$row_rsForm['contact_769']."<BR>
100. Name: ".$row_rsForm['contact_770']." Title: ".$row_rsForm['contact_771']." Phone: ".$row_rsForm['contact_772']." Cell: ".$row_rsForm['contact_773']." E-Mail: ".$row_rsForm['contact_774']."<BR>
101. Name: ".$row_rsForm['contact_775']." Title: ".$row_rsForm['contact_776']." Phone: ".$row_rsForm['contact_777']." Cell: ".$row_rsForm['contact_778']." E-Mail: ".$row_rsForm['contact_779']."<BR>
102. Name: ".$row_rsForm['contact_780']." Title: ".$row_rsForm['contact_781']." Phone: ".$row_rsForm['contact_782']." Cell: ".$row_rsForm['contact_783']." E-Mail: ".$row_rsForm['contact_784']."<BR>
103. Name: ".$row_rsForm['contact_785']." Title: ".$row_rsForm['contact_786']." Phone: ".$row_rsForm['contact_787']." Cell: ".$row_rsForm['contact_788']." E-Mail: ".$row_rsForm['contact_789']."<BR>
104. Name: ".$row_rsForm['contact_790']." Title: ".$row_rsForm['contact_791']." Phone: ".$row_rsForm['contact_792']." Cell: ".$row_rsForm['contact_793']." E-Mail: ".$row_rsForm['contact_794']."<BR>
105. Name: ".$row_rsForm['contact_795']." Title: ".$row_rsForm['contact_796']." Phone: ".$row_rsForm['contact_797']." Cell: ".$row_rsForm2['contact_798']." E-Mail: ".$row_rsForm2['contact_799']."<BR>
106. Name: ".$row_rsForm2['contact_800']." Title: ".$row_rsForm2['contact_801']." Phone: ".$row_rsForm2['contact_802']." Cell: ".$row_rsForm2['contact_803']." E-Mail: ".$row_rsForm2['contact_804']."<BR>
107. Name: ".$row_rsForm2['contact_805']." Title: ".$row_rsForm2['contact_806']." Phone: ".$row_rsForm2['contact_807']." Cell: ".$row_rsForm2['contact_808']." E-Mail: ".$row_rsForm2['contact_809']."<BR>
108. Name: ".$row_rsForm2['contact_810']." Title: ".$row_rsForm2['contact_811']." Phone: ".$row_rsForm2['contact_812']." Cell: ".$row_rsForm2['contact_813']." E-Mail: ".$row_rsForm2['contact_814']."<BR>
109. Name: ".$row_rsForm2['contact_815']." Title: ".$row_rsForm2['contact_816']." Phone: ".$row_rsForm2['contact_817']." Cell: ".$row_rsForm2['contact_818']." E-Mail: ".$row_rsForm2['contact_819']."<BR>
110. Name: ".$row_rsForm2['contact_820']." Title: ".$row_rsForm2['contact_821']." Phone: ".$row_rsForm2['contact_822']." Cell: ".$row_rsForm2['contact_823']." E-Mail: ".$row_rsForm2['contact_824']."<BR>
111. Name: ".$row_rsForm2['contact_825']." Title: ".$row_rsForm2['contact_826']." Phone: ".$row_rsForm2['contact_827']." Cell: ".$row_rsForm2['contact_828']." E-Mail: ".$row_rsForm2['contact_829']."<BR>
112. Name: ".$row_rsForm2['contact_830']." Title: ".$row_rsForm2['contact_831']." Phone: ".$row_rsForm2['contact_832']." Cell: ".$row_rsForm2['contact_833']." E-Mail: ".$row_rsForm2['contact_834']."<BR>
113. Name: ".$row_rsForm2['contact_835']." Title: ".$row_rsForm2['contact_836']." Phone: ".$row_rsForm2['contact_837']." Cell: ".$row_rsForm2['contact_838']." E-Mail: ".$row_rsForm2['contact_839']."<BR>
114. Name: ".$row_rsForm2['contact_840']." Title: ".$row_rsForm2['contact_841']." Phone: ".$row_rsForm2['contact_842']." Cell: ".$row_rsForm2['contact_843']." E-Mail: ".$row_rsForm2['contact_844']."<BR>
115. Name: ".$row_rsForm2['contact_845']." Title: ".$row_rsForm2['contact_846']." Phone: ".$row_rsForm2['contact_847']." Cell: ".$row_rsForm2['contact_848']." E-Mail: ".$row_rsForm2['contact_849']."<BR>
116. Name: ".$row_rsForm2['contact_850']." Title: ".$row_rsForm2['contact_851']." Phone: ".$row_rsForm2['contact_852']." Cell: ".$row_rsForm2['contact_853']." E-Mail: ".$row_rsForm2['contact_854']."<BR>
117. Name: ".$row_rsForm2['contact_855']." Title: ".$row_rsForm2['contact_856']." Phone: ".$row_rsForm2['contact_857']." Cell: ".$row_rsForm2['contact_858']." E-Mail: ".$row_rsForm2['contact_859']."<BR>
118. Name: ".$row_rsForm2['contact_860']." Title: ".$row_rsForm2['contact_861']." Phone: ".$row_rsForm2['contact_862']." Cell: ".$row_rsForm2['contact_863']." E-Mail: ".$row_rsForm2['contact_864']."<BR>
119. Name: ".$row_rsForm2['contact_865']." Title: ".$row_rsForm2['contact_866']." Phone: ".$row_rsForm2['contact_867']." Cell: ".$row_rsForm2['contact_868']." E-Mail: ".$row_rsForm2['contact_869']."<BR>
120. Name: ".$row_rsForm2['contact_870']." Title: ".$row_rsForm2['contact_871']." Phone: ".$row_rsForm2['contact_872']." Cell: ".$row_rsForm2['contact_873']." E-Mail: ".$row_rsForm2['contact_874']."<BR>
121. Name: ".$row_rsForm2['contact_875']." Title: ".$row_rsForm2['contact_876']." Phone: ".$row_rsForm2['contact_877']." Cell: ".$row_rsForm2['contact_878']." E-Mail: ".$row_rsForm2['contact_879']."<BR>
122. Name: ".$row_rsForm2['contact_880']." Title: ".$row_rsForm2['contact_881']." Phone: ".$row_rsForm2['contact_882']." Cell: ".$row_rsForm2['contact_883']." E-Mail: ".$row_rsForm2['contact_884']."<BR>
123. Name: ".$row_rsForm2['contact_885']." Title: ".$row_rsForm2['contact_886']." Phone: ".$row_rsForm2['contact_887']." Cell: ".$row_rsForm2['contact_888']." E-Mail: ".$row_rsForm2['contact_889']."<BR>
124. Name: ".$row_rsForm2['contact_890']." Title: ".$row_rsForm2['contact_891']." Phone: ".$row_rsForm2['contact_892']." Cell: ".$row_rsForm2['contact_893']." E-Mail: ".$row_rsForm2['contact_894']."<BR>
125. Name: ".$row_rsForm2['contact_895']." Title: ".$row_rsForm2['contact_896']." Phone: ".$row_rsForm2['contact_897']." Cell: ".$row_rsForm2['contact_898']." E-Mail: ".$row_rsForm2['contact_899']."<BR>
126. Name: ".$row_rsForm2['contact_900']." Title: ".$row_rsForm2['contact_901']." Phone: ".$row_rsForm2['contact_902']." Cell: ".$row_rsForm2['contact_903']." E-Mail: ".$row_rsForm2['contact_904']."<BR>
127. Name: ".$row_rsForm2['contact_905']." Title: ".$row_rsForm2['contact_906']." Phone: ".$row_rsForm2['contact_907']." Cell: ".$row_rsForm2['contact_908']." E-Mail: ".$row_rsForm2['contact_909']."<BR>
128. Name: ".$row_rsForm2['contact_910']." Title: ".$row_rsForm2['contact_911']." Phone: ".$row_rsForm2['contact_912']." Cell: ".$row_rsForm2['contact_913']." E-Mail: ".$row_rsForm2['contact_914']."<BR>
129. Name: ".$row_rsForm2['contact_915']." Title: ".$row_rsForm2['contact_916']." Phone: ".$row_rsForm2['contact_917']." Cell: ".$row_rsForm2['contact_918']." E-Mail: ".$row_rsForm2['contact_919']."<BR>
130. Name: ".$row_rsForm2['contact_920']." Title: ".$row_rsForm2['contact_921']." Phone: ".$row_rsForm2['contact_922']." Cell: ".$row_rsForm2['contact_923']." E-Mail: ".$row_rsForm2['contact_924']."<BR>
131. Name: ".$row_rsForm2['contact_925']." Title: ".$row_rsForm2['contact_926']." Phone: ".$row_rsForm2['contact_927']." Cell: ".$row_rsForm2['contact_928']." E-Mail: ".$row_rsForm2['contact_929']."<BR>
132. Name: ".$row_rsForm2['contact_930']." Title: ".$row_rsForm2['contact_931']." Phone: ".$row_rsForm2['contact_932']." Cell: ".$row_rsForm2['contact_933']." E-Mail: ".$row_rsForm2['contact_934']."<BR>
133. Name: ".$row_rsForm2['contact_935']." Title: ".$row_rsForm2['contact_936']." Phone: ".$row_rsForm2['contact_937']." Cell: ".$row_rsForm2['contact_938']." E-Mail: ".$row_rsForm2['contact_939']."<BR>
134. Name: ".$row_rsForm2['contact_940']." Title: ".$row_rsForm2['contact_941']." Phone: ".$row_rsForm2['contact_942']." Cell: ".$row_rsForm2['contact_943']." E-Mail: ".$row_rsForm2['contact_944']."<BR>
135. Name: ".$row_rsForm2['contact_945']." Title: ".$row_rsForm2['contact_946']." Phone: ".$row_rsForm2['contact_947']." Cell: ".$row_rsForm2['contact_948']." E-Mail: ".$row_rsForm2['contact_949']."<BR>
136. Name: ".$row_rsForm2['contact_950']." Title: ".$row_rsForm2['contact_951']." Phone: ".$row_rsForm2['contact_952']." Cell: ".$row_rsForm2['contact_953']." E-Mail: ".$row_rsForm2['contact_954']."<BR>
137. Name: ".$row_rsForm2['contact_955']." Title: ".$row_rsForm2['contact_956']." Phone: ".$row_rsForm2['contact_957']." Cell: ".$row_rsForm2['contact_958']." E-Mail: ".$row_rsForm2['contact_959']."<BR>
138. Name: ".$row_rsForm2['contact_960']." Title: ".$row_rsForm2['contact_961']." Phone: ".$row_rsForm2['contact_962']." Cell: ".$row_rsForm2['contact_963']." E-Mail: ".$row_rsForm2['contact_964']."<BR>
139. Name: ".$row_rsForm2['contact_965']." Title: ".$row_rsForm2['contact_966']." Phone: ".$row_rsForm2['contact_967']." Cell: ".$row_rsForm2['contact_968']." E-Mail: ".$row_rsForm2['contact_969']."<BR>
140. Name: ".$row_rsForm2['contact_970']." Title: ".$row_rsForm2['contact_971']." Phone: ".$row_rsForm2['contact_972']." Cell: ".$row_rsForm2['contact_973']." E-Mail: ".$row_rsForm2['contact_974']."<BR>
141. Name: ".$row_rsForm2['contact_975']." Title: ".$row_rsForm2['contact_976']." Phone: ".$row_rsForm2['contact_977']." Cell: ".$row_rsForm2['contact_978']." E-Mail: ".$row_rsForm2['contact_979']."<BR>
142. Name: ".$row_rsForm2['contact_980']." Title: ".$row_rsForm2['contact_981']." Phone: ".$row_rsForm2['contact_982']." Cell: ".$row_rsForm2['contact_983']." E-Mail: ".$row_rsForm2['contact_984']."<BR>
143. Name: ".$row_rsForm2['contact_985']." Title: ".$row_rsForm2['contact_986']." Phone: ".$row_rsForm2['contact_987']." Cell: ".$row_rsForm2['contact_988']." E-Mail: ".$row_rsForm2['contact_989']."<BR>
144. Name: ".$row_rsForm2['contact_990']." Title: ".$row_rsForm2['contact_991']." Phone: ".$row_rsForm2['contact_992']." Cell: ".$row_rsForm2['contact_993']." E-Mail: ".$row_rsForm2['contact_994']."<BR>
145. Name: ".$row_rsForm2['contact_995']." Title: ".$row_rsForm2['contact_996']." Phone: ".$row_rsForm2['contact_997']." Cell: ".$row_rsForm2['contact_998']." E-Mail: ".$row_rsForm2['contact_999']."<BR>
146. Name: ".$row_rsForm2['contact_1000']." Title: ".$row_rsForm2['contact_1001']." Phone: ".$row_rsForm2['contact_1002']." Cell: ".$row_rsForm2['contact_1003']." E-Mail: ".$row_rsForm2['contact_1004']."<BR>
147. Name: ".$row_rsForm2['contact_1005']." Title: ".$row_rsForm2['contact_1006']." Phone: ".$row_rsForm2['contact_1007']." Cell: ".$row_rsForm2['contact_1008']." E-Mail: ".$row_rsForm2['contact_1009']."<BR>
148. Name: ".$row_rsForm2['contact_1010']." Title: ".$row_rsForm2['contact_1011']." Phone: ".$row_rsForm2['contact_1012']." Cell: ".$row_rsForm2['contact_1013']." E-Mail: ".$row_rsForm2['contact_1014']."<BR>
149. Name: ".$row_rsForm2['contact_1015']." Title: ".$row_rsForm2['contact_1016']." Phone: ".$row_rsForm2['contact_1017']." Cell: ".$row_rsForm2['contact_1018']." E-Mail: ".$row_rsForm2['contact_1019']."<BR>
150. Name: ".$row_rsForm2['contact_1020']." Title: ".$row_rsForm2['contact_1021']." Phone: ".$row_rsForm2['contact_1022']." Cell: ".$row_rsForm2['contact_1023']." E-Mail: ".$row_rsForm2['contact_1024']."</P>");
		}//end of if

		$pdf->BoldText("<P align='left'><BR><BR>".$strBusName." Current Suppliers List<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>The following is a list of all the current suppliers to ".$strBusName.".
<BR><BR>
1. Supplier : ".$row_rsForm['contact_241']." Prod/Ser.: ".$row_rsForm['contact_242']." Phone: ".$row_rsForm['contact_243']." Cell: ".$row_rsForm['contact_244']." E-Mail: ".$row_rsForm['contact_245']."<BR>
2. Supplier : ".$row_rsForm['contact_246']." Prod/Ser: ".$row_rsForm['contact_247']." Phone: ".$row_rsForm['contact_248']." Cell: ".$row_rsForm['contact_249']." E-Mail: ".$row_rsForm['contact_250']."<BR>
3. Supplier : ".$row_rsForm['contact_251']." Prod/Ser: ".$row_rsForm['contact_252']." Phone: ".$row_rsForm['contact_253']." Cell: ".$row_rsForm['contact_254']." E-Mail: ".$row_rsForm['contact_255']."<BR>
4. Supplier : ".$row_rsForm['contact_256']." Prod/Ser: ".$row_rsForm['contact_257']." Phone: ".$row_rsForm['contact_258']." Cell: ".$row_rsForm['contact_259']." E-Mail: ".$row_rsForm['contact_260']."<BR>
5. Supplier : ".$row_rsForm['contact_261']." Prod/Ser: ".$row_rsForm['contact_262']." Phone: ".$row_rsForm['contact_263']." Cell: ".$row_rsForm['contact_264']." E-Mail: ".$row_rsForm['contact_265']."<BR>
6. Supplier : ".$row_rsForm['contact_266']." Prod/Ser: ".$row_rsForm['contact_267']." Phone: ".$row_rsForm['contact_268']." Cell: ".$row_rsForm['contact_269']." E-Mail: ".$row_rsForm['contact_270']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Local & Regional Emergency Response Units<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
The following is a list of Emegency Response Units:
<BR><BR>
1. Local Police : ".$row_rsForm['contact_271']." Location: ".$row_rsForm['contact_272']." Phone: ".$row_rsForm['contact_273']." Cell: ".$row_rsForm['contact_274']." E-Mail: ".$row_rsForm['contact_275']."<BR>
2. Reg. Police : ".$row_rsForm['contact_276']." Location.: ".$row_rsForm['contact_277']." Phone: ".$row_rsForm['contact_278']." Cell: ".$row_rsForm['contact_279']." E-Mail: ".$row_rsForm['contact_280']."<BR>
3. Fire : ".$row_rsForm['contact_281']." Location: ".$row_rsForm['contact_282']." Phone: ".$row_rsForm['contact_283']." Cell: ".$row_rsForm['contact_284']." E-Mail: ".$row_rsForm['contact_285']."<BR>
4. Hospital : ".$row_rsForm['contact_286']." Location: ".$row_rsForm['contact_287']." Phone: ".$row_rsForm['contact_288']." Cell: ".$row_rsForm['contact_289']." E-Mail: ".$row_rsForm['contact_290']."<BR>
5. Environ. : ".$row_rsForm['contact_291']." Location.: ".$row_rsForm['contact_292']." Phone: ".$row_rsForm['contact_293']." Cell: ".$row_rsForm['contact_294']." E-Mail: ".$row_rsForm['contact_295']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Possible Reconstruction & Contractor Contractors<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
The following is a list of potential Contractors and Other Service Providers
<BR><BR>
1. Contractor : ".$row_rsForm['contact_296']." Location: ".$row_rsForm['contact_297']." Phone: ".$row_rsForm['contact_298']." Cell: ".$row_rsForm['contact_299']." E-Mail: ".$row_rsForm['contact_300']."<BR>
2. Contractor : ".$row_rsForm['contact_301']." Location: ".$row_rsForm['contact_302']." Phone: ".$row_rsForm['contact_303']." Cell: ".$row_rsForm['contact_304']." E-Mail: ".$row_rsForm['contact_305']."<BR>
3. Contractor : ".$row_rsForm['contact_306']." Location: ".$row_rsForm['contact_307']." Phone: ".$row_rsForm['contact_308']." Cell: ".$row_rsForm['contact_309']." E-Mail: ".$row_rsForm['contact_310']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Local and regional Emergency Response Units:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
The following is a list of Local and Regional Emergency Numbers
<BR><BR>
1. Local Police: ".$row_rsForm['contact_271']." Location: ".$row_rsForm['contact_272']." Phone: ".$row_rsForm['contact_273']." Cell: ".$row_rsForm['contact_274']." E-Mail: ".$row_rsForm['contact_275']."<BR>
2. Reg. Police: ".$row_rsForm['contact_276']." Location: ".$row_rsForm['contact_277']." Phone: ".$row_rsForm['contact_278']." Cell: ".$row_rsForm['contact_279']." E-Mail: ".$row_rsForm['contact_280']."<BR>
3. Fire: ".$row_rsForm['contact_281']." Location:".$row_rsForm['contact_282']." Phone: ".$row_rsForm['contact_283']." Cell: ".$row_rsForm['contact_284']." E-Mail: ".$row_rsForm['contact_285']."<BR>
4. Hospital: ".$row_rsForm['contact_286']." Location:".$row_rsForm['contact_287']." Phone: ".$row_rsForm['contact_288']." Cell: ".$row_rsForm['contact_289']." E-Mail: ".$row_rsForm['contact_290']."<BR>
5. Enviro : ".$row_rsForm['contact_291']." Location: ".$row_rsForm['contact_292']." Phone: ".$row_rsForm['contact_293']." Cell: ".$row_rsForm['contact_294']." E-Mail: ".$row_rsForm['contact_295']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>External Additional Services Provider:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
The following is a list of External Service Providers
<BR><BR>
Security Cpy : ".$row_rsForm['contact_355']." Location: ".$row_rsForm['contact_356']." Phone: ".$row_rsForm['contact_357']." Cell: ".$row_rsForm['contact_358']." E-Mail: ".$row_rsForm['contact_359']."<BR>
INS Broker: ".$row_rsForm['contact_360']." Location: ".$row_rsForm['contact_361']." Phone: ".$row_rsForm['contact_362']." Cell: ".$row_rsForm['contact_363']." E-Mail: ".$row_rsForm['contact_364']."<BR>
INS Agency: ".$row_rsForm['contact_365']." Location: ".$row_rsForm['contact_366']." Phone: ".$row_rsForm['contact_367']." Cell: ".$row_rsForm['contact_368']." E-Mail: ".$row_rsForm['contact_369']."<BR>
Attorney: ".$row_rsForm['contact_370']." Location: ".$row_rsForm['contact_371']." Phone: ".$row_rsForm['contact_372']." Cell: ".$row_rsForm['contact_373']." E-Mail: ".$row_rsForm['contact_374']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Landlord or Building Management Contacts:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
Landlord Name: ".$row_rsForm['contact_311']." Phone: ".$row_rsForm['contact_312']." Cell: ".$row_rsForm['contact_313']." E-Mail: ".$row_rsForm['contact_314']."<BR>
Management: ".$row_rsForm['contact_315']." Phone: ".$row_rsForm['contact_316']." Cell: ".$row_rsForm['contact_317']." E-Mail: ".$row_rsForm['contact_318']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Additional Service Provider for the following items:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
Equipment Services: ".$row_rsForm['contact_319']." Phone: ".$row_rsForm['contact_320']." Cell: ".$row_rsForm['contact_321']." E-Mail: ".$row_rsForm['contact_322']."<BR>
Alternate Equipment Service: ".$row_rsForm['contact_323']." Phone: ".$row_rsForm['contact_324']." Cell: ".$row_rsForm['contact_325']." E-Mail: ".$row_rsForm['contact_326']."<BR>
Alternate Equipment Service: ".$row_rsForm['contact_327']." Phone: ".$row_rsForm['contact_328']." Cell: ".$row_rsForm['contact_329']." E-Mail: ".$row_rsForm['contact_330']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Furnishings Services:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
Furnishing Services: ".$row_rsForm['contact_331']." Phone: ".$row_rsForm['contact_332']." Cell: ".$row_rsForm['contact_333']." E-Mail: ".$row_rsForm['contact_334']."<BR>
Alternate Furnishing Service: ".$row_rsForm['contact_335']." Phone: ".$row_rsForm['contact_336']." Cell: ".$row_rsForm['contact_337']." E-Mail: ".$row_rsForm['contact_338']."<BR>
Alternate Furnishing Service: ".$row_rsForm['contact_339']." Phone: ".$row_rsForm['contact_340']." Cell: ".$row_rsForm['contact_341']." E-Mail: ".$row_rsForm['contact_342']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Unique Services:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
Unique Services: ".$row_rsForm['contact_343']." Phone: ".$row_rsForm['contact_344']." Cell: ".$row_rsForm['contact_345']." E-Mail: ".$row_rsForm['contact_346']."<BR>
Alternate Unique Service: ".$row_rsForm['contact_347']." Phone: ".$row_rsForm['contact_348']." Cell: ".$row_rsForm['contact_349']." E-Mail: ".$row_rsForm['contact_350']."<BR>
Alternate Unique Service: ".$row_rsForm['contact_351']." Phone: ".$row_rsForm['contact_352']." Cell: ".$row_rsForm['contact_353']." E-Mail: ".$row_rsForm['contact_354']."</P>");
	}//end of if
	
	//Information 
	if($intTableIndex == 3)
	{
		//does another selection to get the updated data
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsForm2 = mysql_query("SELECT * FROM c2information2 WHERE C2ID= ".$row_rsForm['C2ID'], $conContinuty) or die("Information2: ".mysql_error());
		$row_rsForm2 = mysql_fetch_assoc($rsForm2);
		$totalRows_rsForm2 = mysql_num_rows($rsForm2);

		$pdf->BoldText("<P align='left'>In todays modern world Technology plays a major role in business operation and development. Without
technology most business would be unable to function. Use the following section to define you Information
Technology requirements so in the time of a disaster you are able to recovery any and all lost items.<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>The following is a list of the current </P>");
		$pdf->BoldText("<P align='left'>Software Programs </P>");
		$pdf->QuestionsBody("<P align='left'>Used by ".$strBusName."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>1. Software Program: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI01']."<BR><BR>
Provider: ".$row_rsForm['IT_1SW01']."<BR>
Required Copies: ".$row_rsForm['IT_1SW02']."<BR>
Serial Number: ".$row_rsForm['IT_1SW03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:<BR><BR></P>");
		
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_1SW05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>2. Software Program: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI02']."<BR><BR>
Provider: ".$row_rsForm['IT_2SW01']."<BR>
Required Copies: ".$row_rsForm['IT_2SW02']."<BR>
Serial Number: ".$row_rsForm['IT_2SW03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_2SW05']."<BR><BR></P>");
		
		$pdf->BoldText("<P align='left'>3. Software Program: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI03']."<BR><BR>
Provider: ".$row_rsForm['IT_3SW01']."<BR>
Required Copies: ".$row_rsForm['IT_3SW02']."<BR>
Serial Number: (IT_3SW03)<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_3SW05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>4. Software Program: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI04']."<BR><BR>
Provider: ".$row_rsForm['IT_4SW01']."<BR>
Required Copies: ".$row_rsForm['IT_4SW02']."<BR>
Serial Number: ".$row_rsForm['IT_4SW03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_4SW05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>5. Software Program: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI05']."<BR><BR>
Provider: ".$row_rsForm['IT_5SW01']."<BR>
Required Copies: ".$row_rsForm['IT_5SW02']."<BR>
Serial Number: ".$row_rsForm['IT_5SW03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_5SW05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>In todays modern world Technology plays a major role in business operation and development. Without
technology most business would be unable to function. Use the following section to define you Information
Technology requirements so in the time of a disaster you are able to recovery any and all lost items.<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>The following is a list of the current </P>");
		$pdf->BoldText("<P align='left'>Hardware Programs </P>");
		$pdf->QuestionsBody("<P align='left'>Used by ".$strBusName."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>1. Hardware Program: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI06']."<BR><BR>
Provider: ".$row_rsForm['IT_1HW01']."<BR>
Required Copies: ".$row_rsForm['IT_1HW02']."<BR>
Serial Number: ".$row_rsForm['IT_1HW03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_1HW05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>2. Hardware Program: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI07']."<BR><BR>
Provider: ".$row_rsForm['IT_2HW01']."<BR>
Required Copies: ".$row_rsForm['IT_2HW02']."<BR>
Serial Number: ".$row_rsForm['IT_2HW03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_2HW05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>3. Hardware Program: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI08']."<BR><BR>
Provider: ".$row_rsForm['IT_3HW01']."<BR>
Required Copies: ".$row_rsForm['IT_3HW02']."<BR>
Serial Number: ".$row_rsForm['IT_3HW03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_3HW05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>4. Hardware Program: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI09']."<BR><BR>
Provider: ".$row_rsForm['IT_4HW01']."<BR>
Required Copies: ".$row_rsForm['IT_4HW02']."<BR>
Serial Number: ".$row_rsForm['IT_4HW03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:</P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_4HW05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>5. Hardware Program: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI10']."<BR><BR>
Provider: ".$row_rsForm['IT_5HW01']."<BR>
Required Copies: ".$row_rsForm['IT_5HW02']."<BR>
Serial Number: ".$row_rsForm['IT_5HW03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_5HW05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If your business currently stores your information on an internal server. Please find the details and specifications below to help in the recovery of your IT systems.<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>The following is a list of the current </P>"); 
		$pdf->BoldText("<P align='left'>Servers </P>");
		$pdf->QuestionsBody("<P align='left'>Used by ".$strBusName."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>1. Server Name: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI11']."<BR><BR>
Provider: ".$row_rsForm['IT_1SR01']."<BR>
Users/Employees: ".$row_rsForm['IT_1SR02']."<BR>
Size/Capacity: ".$row_rsForm['IT_1SR04']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_1SR05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>2. Server Name: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI12']."<BR><BR>
Provider: ".$row_rsForm['IT_2SR01']."<BR>
Users/Employees: ".$row_rsForm['IT_2SR02']."<BR>
Size/Capacity: ".$row_rsForm['IT_2SR04']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_2SR05']."<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>The following is a list of the current</P>"); 
		$pdf->BoldText("<P align='left'>Back-Up Tapes </P>");
		$pdf->QuestionsBody("<P align='left'>Used by ".$strBusName."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>1. Back-Up Tape Name: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_CI13']."<BR><BR>
Required Copies: ".$row_rsForm['IT_1TP02']."<BR>
Software Program: ".$row_rsForm['IT_1TP03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Basic Description of Program:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_1TP04']."<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>The following is a list of the current </P>");
		$pdf->BoldText("<P align='left'>Back-Up Procedures </P>");
		$pdf->QuestionsBody("<P align='left'>Used by ".$strBusName."<BR><BR>If you currently store your information off-site please provide the information about that company so you can access your information if a disaster were to occur. If you do not have an off-site service provider you may want to consider seeking additional information. Contact us to ask us questions about IT recovery.
<BR><BR>".$row_rsForm['IT_sum01']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Off-Site Information Storage Provider:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Company Name: ".$row_rsForm['IT_OFF01']."<BR>
Address: ".$row_rsForm['IT_OFF02']."<BR>
Phone: ".$row_rsForm['IT_OFF03']."<BR>
Contact: ".$row_rsForm['IT_OFF04']."<BR>
E-Mail: ".$row_rsForm['IT_OFF05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>In the event of a loss the following will identify the key IT equipment used by your business.
Use this basic inventory summary to understand your minimum requirements to operate your business.<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>The following is a list of the current </P>");
		$pdf->BoldText("<P align='left'>IT Equipment </P>");
		$pdf->QuestionsBody("<P align='left'>Used by ".$strBusName."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>1. Computer Monitors<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 1: ".$row_rsForm['IT_1IN01']." Required Units: ".$row_rsForm['IT_1IN02']." Replacement Value($): ".$row_rsForm['IT_1IN03']."<BR><BR>
Description of Specific Details:
<BR>
".$row_rsForm['IT_1IN04']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>2. (Other) Computer Monitors<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 2: ".$row_rsForm['IT_1IN05']." Required Units: ".$row_rsForm['IT_1IN06']." Replacement Value($): ".$row_rsForm['IT_1IN07']."<BR><BR>
Description of Specific Details:
<BR>
".$row_rsForm['IT_1IN08']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>3. (Other) Computer Monitors<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 3: ".$row_rsForm['IT_1IN09']." Required Units: ".$row_rsForm['IT_1IN10']." Replacement Value($): ".$row_rsForm['IT_1IN11']."<BR><BR>
Description of Specific Details:
<BR>
".$row_rsForm['IT_1IN12']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>4. Computer CPU Units<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 1: ".$row_rsForm['IT_1IN13']." Required Units: ".$row_rsForm['IT_1IN14']." Replacement Value($): ".$row_rsForm['IT_1IN15']."<BR><BR>
Description of Specific Details:
<BR>
".$row_rsForm['IT_1IN16']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>5. (Other) Computer CPU Units<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 2: ".$row_rsForm['IT_1IN17']." Required Units: ".$row_rsForm['IT_1IN18']." Replacement Value($): ".$row_rsForm['IT_1IN19']."<BR><BR>
Description of Specific Details:
<BR>
".$row_rsForm['IT_1IN20']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>6. (Other) Computer CPU Units<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 3: ".$row_rsForm['IT_1IN21']." Required Units: ".$row_rsForm['IT_1IN22']." Replacement Value($): ".$row_rsForm['IT_1IN23']."<BR><BR>
Description of Specific Details:
<BR>
".$row_rsForm['IT_1IN24']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>7. Computer Keyboards<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 1: ".$row_rsForm['IT_1IN25']." Required Units: ".$row_rsForm['IT_1IN26']." Replacement Value($): ".$row_rsForm['IT_1IN27']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>8. (Other) Computer Keyboards<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 2: ".$row_rsForm['IT_1IN28']." Required Units: ".$row_rsForm['IT_1IN29']." Replacement Value($): ".$row_rsForm['IT_1IN30']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>9. Computer Mouse<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 1: ".$row_rsForm['IT_1IN31']." Required Units: ".$row_rsForm['IT_1IN32']." Replacement Value($): ".$row_rsForm['IT_1IN33']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>10. Computer Printers<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 1: ".$row_rsForm['IT_1IN34']." Required Units: ".$row_rsForm['IT_1IN35']." Replacement Value($): ".$row_rsForm['IT_1IN36']."
Description of Specific Details:
<BR>
".$row_rsForm['IT_1IN37']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>11. (Other) Computer Printers<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 2: ".$row_rsForm['IT_1IN38']." Required Units: ".$row_rsForm['IT_1IN39']." Replacement Value($): ".$row_rsForm['IT_1IN40']."<BR><BR>
Description of Specific Details:
<BR>
".$row_rsForm['IT_1IN41']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>12. (Other) Computer Printers<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 3: ".$row_rsForm['IT_1IN42']." Required Units: ".$row_rsForm['IT_1IN43']." Replacement Value($): ".$row_rsForm['IT_1IN44']."<BR><BR>
Description of Specific Details:
<BR>
".$row_rsForm['IT_1IN45']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>13. Computer Fax/Scanners<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 1: ".$row_rsForm['IT_1IN46']." Required Units: ".$row_rsForm['IT_1IN47']." Replacement Value($): ".$row_rsForm['IT_1IN48']."<BR><BR>
Description of Specific Details:
<BR>".$row_rsForm['IT_1IN49']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>14. (Other) Computer Fax/Scanners<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 2: ".$row_rsForm['IT_1IN50']." Required Units: ".$row_rsForm['IT_1IN51']." Replacement Value($): ".$row_rsForm['IT_1IN52']."<BR><BR>
Description of Specific Details:
<BR>".$row_rsForm['IT_1IN53']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>15. Computer Photocopier<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Brand Name 3: ".$row_rsForm['IT_1IN54']." Required Units: ".$row_rsForm['IT_1IN55']." Replacement Value($): ".$row_rsForm['IT_1IN56']."<BR><BR>
Description of Specific Details:
<BR>".$row_rsForm['IT_1IN57']."</P>");
	
		//creates the page to be used
		$pdf->AddPage();

		$pdf->BoldText("<P align='left'>User Names & Passwords:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>The following is a list of the current User Names & Password Used by ".$strBusName.". In the event of a disaster and access is needed you may use this information to access any required information. Information is protected and secured by our online
security features.
<BR><BR>
Program #1 : ".$row_rsForm['IT_EMPro01']."
<BR><BR>
1. Employee Name: ".$row_rsForm['IT_EMP01']."
 User Name: ".$row_rsForm['IT_USER01']."
 Password: ".$row_rsForm['IT_PASS01']."<BR>
2. Employee Name: ".$row_rsForm['IT_EMP02']."
 User Name: ".$row_rsForm['IT_USER02']."
 Password: ".$row_rsForm['IT_PASS02']."<BR>
3. Employee Name: ".$row_rsForm['IT_EMP03']."
 User Name: ".$row_rsForm['IT_USER03']."
 Password: ".$row_rsForm['IT_PASS03']."<BR>
4. Employee Name: ".$row_rsForm['IT_EMP04']."
 User Name: ".$row_rsForm['IT_USER04']."
 Password: ".$row_rsForm['IT_PASS04']."<BR>
5. Employee Name: ".$row_rsForm['IT_EMP05']."
 User Name: ".$row_rsForm['IT_USER05']."
 Password: ".$row_rsForm['IT_PASS05']."<BR>
6. Employee Name: ".$row_rsForm['IT_EMP06']."
 User Name: ".$row_rsForm['IT_USER06']."
 Password: ".$row_rsForm['IT_PASS06']."<BR>
7. Employee Name: ".$row_rsForm['IT_EMP07']."
 User Name: ".$row_rsForm['IT_USER07']."
 Password: ".$row_rsForm['IT_PASS07']."<BR>
8. Employee Name: ".$row_rsForm['IT_EMP08']."
 User Name: ".$row_rsForm['IT_USER08']."
 Password: ".$row_rsForm['IT_PASS08']."<BR>
9. Employee Name: ".$row_rsForm['IT_EMP09']."
 User Name: ".$row_rsForm['IT_USER09']."
 Password: ".$row_rsForm['IT_PASS09']."<BR>
10. Employee Name: ".$row_rsForm['IT_EMP10']."
 User Name: ".$row_rsForm['IT_USER10']."
 Password: ".$row_rsForm['IT_PASS10']."<BR>
11. Employee Name: ".$row_rsForm['IT_EMP11']."
 User Name: ".$row_rsForm['IT_USER11']."
 Password: ".$row_rsForm['IT_PASS11']."<BR>
12. Employee Name: ".$row_rsForm['IT_EMP12']."
 User Name: ".$row_rsForm['IT_USER12']."
 Password: ".$row_rsForm['IT_PASS12']."<BR>
13. Employee Name: ".$row_rsForm['IT_EMP13']."
 User Name: ".$row_rsForm['IT_USER13']."
 Password: ".$row_rsForm['IT_PASS13']."<BR>
14. Employee Name: ".$row_rsForm['IT_EMP14']."
 User Name: ".$row_rsForm['IT_USER14']."
 Password: ".$row_rsForm['IT_PASS14']."<BR>
15. Employee Name: ".$row_rsForm['IT_EMP15']."
 User Name: ".$row_rsForm['IT_USER15']."
 Password: ".$row_rsForm['IT_PASS15']."<BR>
16. Employee Name: ".$row_rsForm['IT_EMP16']."
 User Name: ".$row_rsForm['IT_USER16']."
 Password: ".$row_rsForm['IT_PASS16']."<BR>
17. Employee Name: ".$row_rsForm['IT_EMP17']."
 User Name: ".$row_rsForm['IT_USER17']."
 Password: ".$row_rsForm['IT_PASS17']."<BR>
18. Employee Name: ".$row_rsForm['IT_EMP18']."
 User Name: ".$row_rsForm['IT_USER18']."
 Password: ".$row_rsForm['IT_PASS18']."<BR>
19. Employee Name: ".$row_rsForm['IT_EMP19']."
 User Name: ".$row_rsForm['IT_USER19']."
 Password: ".$row_rsForm['IT_PASS19']."<BR>
20. Employee Name: ".$row_rsForm['IT_EMP20']."
 User Name: ".$row_rsForm['IT_USER20']."
 Password: ".$row_rsForm['IT_PASS20']."<BR></P>");


		// this will display only for Standard
		if ($row_loginFoundUser['Solution'] == 2)
		{
			$pdf->QuestionsBody("<P align='left'>
21. Employee Name: ".$row_rsForm2['ITStd_001']."
 User Name: ".$row_rsForm2['ITStd_002']."
 Password: ".$row_rsForm2['ITStd_003']."<BR>
22. Employee Name: ".$row_rsForm2['ITStd_004']."
 User Name: ".$row_rsForm2['ITStd_005']."
 Password: ".$row_rsForm2['ITStd_006']."<BR>
23. Employee Name: ".$row_rsForm2['ITStd_007']."
 User Name: ".$row_rsForm2['ITStd_008']."
 Password: ".$row_rsForm2['ITStd_009']."<BR>
24. Employee Name: ".$row_rsForm2['ITStd_010']."
 User Name: ".$row_rsForm2['ITStd_011']."
 Password: ".$row_rsForm2['ITStd_012']."<BR>
25. Employee Name: ".$row_rsForm2['ITStd_013']."
 User Name: ".$row_rsForm2['ITStd_014']."
 Password: ".$row_rsForm2['ITStd_015']."<BR>
26. Employee Name: ".$row_rsForm2['ITStd_016']."
 User Name: ".$row_rsForm2['ITStd_017']."
 Password: ".$row_rsForm2['ITStd_018']."<BR>
27. Employee Name: ".$row_rsForm2['ITStd_019']."
 User Name: ".$row_rsForm2['ITStd_020']."
 Password: ".$row_rsForm2['ITStd_021']."<BR>
28. Employee Name: ".$row_rsForm2['ITStd_022']."
 User Name: ".$row_rsForm2['ITStd_023']."
 Password: ".$row_rsForm2['ITStd_024']."<BR>
29. Employee Name: ".$row_rsForm2['ITStd_025']."
 User Name: ".$row_rsForm2['ITStd_026']."
 Password: ".$row_rsForm2['ITStd_027']."<BR>
30. Employee Name: ".$row_rsForm2['ITStd_028']."
 User Name: ".$row_rsForm2['ITStd_029']."
 Password: ".$row_rsForm2['ITStd_030']."<BR>
31. Employee Name: ".$row_rsForm2['ITStd_031']."
 User Name: ".$row_rsForm2['ITStd_032']."
 Password: ".$row_rsForm2['ITStd_033']."<BR>
32. Employee Name: ".$row_rsForm2['ITStd_034']." User Name: ".$row_rsForm2['ITStd_035']." Password: ".$row_rsForm2['ITStd_036']."<BR>
33. Employee Name: ".$row_rsForm2['ITStd_037']." User Name: ".$row_rsForm2['ITStd_038']." Password: ".$row_rsForm2['ITStd_039']."<BR>
34. Employee Name: ".$row_rsForm2['ITStd_040']." User Name: ".$row_rsForm2['ITStd_041']." Password: ".$row_rsForm2['ITStd_042']."<BR>
35. Employee Name: ".$row_rsForm2['ITStd_043']." User Name: ".$row_rsForm2['ITStd_044']." Password: ".$row_rsForm2['ITStd_045']."<BR>
36. Employee Name: ".$row_rsForm2['ITStd_046']." User Name: ".$row_rsForm2['ITStd_047']." Password: ".$row_rsForm2['ITStd_048']."<BR>
37. Employee Name: ".$row_rsForm2['ITStd_049']." User Name: ".$row_rsForm2['ITStd_050']." Password: ".$row_rsForm2['ITStd_051']."<BR>
38. Employee Name: ".$row_rsForm2['ITStd_052']." User Name: ".$row_rsForm2['ITStd_053']." Password: ".$row_rsForm2['ITStd_054']."<BR>
39. Employee Name: ".$row_rsForm2['ITStd_055']." User Name: ".$row_rsForm2['ITStd_056']." Password: ".$row_rsForm2['ITStd_057']."<BR>
40. Employee Name: ".$row_rsForm2['ITStd_058']." User Name: ".$row_rsForm2['ITStd_059']." Password: ".$row_rsForm2['ITStd_060']."<BR>
41. Employee Name: ".$row_rsForm2['ITStd_061']." User Name: ".$row_rsForm2['ITStd_062']." Password: ".$row_rsForm2['ITStd_063']."<BR>
42. Employee Name: ".$row_rsForm2['ITStd_064']." User Name: ".$row_rsForm2['ITStd_065']." Password: ".$row_rsForm2['ITStd_066']."<BR>
43. Employee Name: ".$row_rsForm2['ITStd_067']." User Name: ".$row_rsForm2['ITStd_068']." Password: ".$row_rsForm2['ITStd_069']."<BR>
44. Employee Name: ".$row_rsForm2['ITStd_070']." User Name: ".$row_rsForm2['ITStd_071']." Password: ".$row_rsForm2['ITStd_072']."<BR>
45. Employee Name: ".$row_rsForm2['ITStd_073']." User Name: ".$row_rsForm2['ITStd_074']." Password: ".$row_rsForm2['ITStd_075']."<BR>
46. Employee Name: ".$row_rsForm2['ITStd_076']." User Name: ".$row_rsForm2['ITStd_077']." Password: ".$row_rsForm2['ITStd_078']."<BR>
47. Employee Name: ".$row_rsForm2['ITStd_079']." User Name: ".$row_rsForm2['ITStd_080']." Password: ".$row_rsForm2['ITStd_081']."<BR>
48. Employee Name: ".$row_rsForm2['ITStd_082']." User Name: ".$row_rsForm2['ITStd_083']." Password: ".$row_rsForm2['ITStd_084']."<BR>
49. Employee Name: ".$row_rsForm2['ITStd_085']." User Name: ".$row_rsForm2['ITStd_086']." Password: ".$row_rsForm2['ITStd_087']."<BR>
50. Employee Name: ".$row_rsForm2['ITStd_088']." User Name: ".$row_rsForm2['ITStd_089']." Password: ".$row_rsForm2['ITStd_090']."<BR>
51. Employee Name: ".$row_rsForm2['ITStd_091']." User Name: ".$row_rsForm2['ITStd_092']." Password: ".$row_rsForm2['ITStd_093']."<BR>
52. Employee Name: ".$row_rsForm2['ITStd_094']." User Name: ".$row_rsForm2['ITStd_095']." Password: ".$row_rsForm2['ITStd_096']."<BR>
53. Employee Name: ".$row_rsForm2['ITStd_097']." User Name: ".$row_rsForm2['ITStd_098']." Password: ".$row_rsForm2['ITStd_099']."<BR>
54. Employee Name: ".$row_rsForm2['ITStd_100']." User Name: ".$row_rsForm2['ITStd_101']." Password: ".$row_rsForm2['ITStd_102']."<BR>
55. Employee Name: ".$row_rsForm2['ITStd_103']." User Name: ".$row_rsForm2['ITStd_104']." Password: ".$row_rsForm2['ITStd_105']."<BR>
56. Employee Name: ".$row_rsForm2['ITStd_106']." User Name: ".$row_rsForm2['ITStd_107']." Password: ".$row_rsForm2['ITStd_108']."<BR>
57. Employee Name: ".$row_rsForm2['ITStd_109']." User Name: ".$row_rsForm2['ITStd_110']." Password: ".$row_rsForm2['ITStd_111']."<BR>
58. Employee Name: ".$row_rsForm2['ITStd_112']." User Name: ".$row_rsForm2['ITStd_113']." Password: ".$row_rsForm2['ITStd_114']."<BR>
59. Employee Name: ".$row_rsForm2['ITStd_115']." User Name: ".$row_rsForm2['ITStd_116']." Password: ".$row_rsForm2['ITStd_117']."<BR>
60. Employee Name: ".$row_rsForm2['ITStd_118']." User Name: ".$row_rsForm2['ITStd_119']." Password: ".$row_rsForm2['ITStd_120']."<BR>
61. Employee Name: ".$row_rsForm2['ITStd_121']." User Name: ".$row_rsForm2['ITStd_122']." Password: ".$row_rsForm2['ITStd_123']."<BR>
62. Employee Name: ".$row_rsForm2['ITStd_124']." User Name: ".$row_rsForm2['ITStd_125']." Password: ".$row_rsForm2['ITStd_126']."<BR>
63. Employee Name: ".$row_rsForm2['ITStd_127']." User Name: ".$row_rsForm2['ITStd_128']." Password: ".$row_rsForm2['ITStd_129']."<BR>
64. Employee Name: ".$row_rsForm2['ITStd_130']." User Name: ".$row_rsForm2['ITStd_131']." Password: ".$row_rsForm2['ITStd_132']."<BR>
65. Employee Name: ".$row_rsForm2['ITStd_133']." User Name: ".$row_rsForm2['ITStd_134']." Password: ".$row_rsForm2['ITStd_135']."<BR>
66. Employee Name: ".$row_rsForm2['ITStd_136']." User Name: ".$row_rsForm2['ITStd_137']." Password: ".$row_rsForm2['ITStd_138']."<BR>
67. Employee Name: ".$row_rsForm2['ITStd_139']." User Name: ".$row_rsForm2['ITStd_140']." Password: ".$row_rsForm2['ITStd_141']."<BR>
68. Employee Name: ".$row_rsForm2['ITStd_142']." User Name: ".$row_rsForm2['ITStd_143']." Password: ".$row_rsForm2['ITStd_144']."<BR>
69. Employee Name: ".$row_rsForm2['ITStd_145']." User Name: ".$row_rsForm2['ITStd_146']." Password: ".$row_rsForm2['ITStd_147']."<BR>
70. Employee Name: ".$row_rsForm2['ITStd_148']." User Name: ".$row_rsForm2['ITStd_149']." Password: ".$row_rsForm2['ITStd_150']."<BR>
71. Employee Name: ".$row_rsForm2['ITStd_151']." User Name: ".$row_rsForm2['ITStd_152']." Password: ".$row_rsForm2['ITStd_153']."<BR>
72. Employee Name: ".$row_rsForm2['ITStd_154']." User Name: ".$row_rsForm2['ITStd_155']." Password: ".$row_rsForm2['ITStd_156']."<BR>
73. Employee Name: ".$row_rsForm2['ITStd_157']." User Name: ".$row_rsForm2['ITStd_158']." Password: ".$row_rsForm2['ITStd_159']."<BR>
74. Employee Name: ".$row_rsForm2['ITStd_160']." User Name: ".$row_rsForm2['ITStd_161']." Password: ".$row_rsForm2['ITStd_162']."<BR>
75. Employee Name: ".$row_rsForm2['ITStd_163']." User Name: ".$row_rsForm2['ITStd_164']." Password: ".$row_rsForm2['ITStd_165']."<BR>
76. Employee Name: ".$row_rsForm2['ITStd_166']." User Name: ".$row_rsForm2['ITStd_167']." Password: ".$row_rsForm2['ITStd_168']."<BR>
77. Employee Name: ".$row_rsForm2['ITStd_169']." User Name: ".$row_rsForm2['ITStd_170']." Password: ".$row_rsForm2['ITStd_171']."<BR>
78. Employee Name: ".$row_rsForm2['ITStd_172']." User Name: ".$row_rsForm2['ITStd_173']." Password: ".$row_rsForm2['ITStd_174']."<BR>
79. Employee Name: ".$row_rsForm2['ITStd_175']." User Name: ".$row_rsForm2['ITStd_176']." Password: ".$row_rsForm2['ITStd_177']."<BR>
80. Employee Name: ".$row_rsForm2['ITStd_178']." User Name: ".$row_rsForm2['ITStd_179']." Password: ".$row_rsForm2['ITStd_180']."<BR>
81. Employee Name: ".$row_rsForm2['ITStd_181']." User Name: ".$row_rsForm2['ITStd_182']." Password: ".$row_rsForm2['ITStd_183']."<BR>
82. Employee Name: ".$row_rsForm2['ITStd_184']." User Name: ".$row_rsForm2['ITStd_185']." Password: ".$row_rsForm2['ITStd_186']."<BR>
83. Employee Name: ".$row_rsForm2['ITStd_187']." User Name: ".$row_rsForm2['ITStd_188']." Password: ".$row_rsForm2['ITStd_189']."<BR>
84. Employee Name: ".$row_rsForm2['ITStd_190']." User Name: ".$row_rsForm2['ITStd_191']." Password: ".$row_rsForm2['ITStd_192']."<BR>
85. Employee Name: ".$row_rsForm2['ITStd_193']." User Name: ".$row_rsForm2['ITStd_194']." Password: ".$row_rsForm2['ITStd_195']."<BR>
86. Employee Name: ".$row_rsForm2['ITStd_196']." User Name: ".$row_rsForm2['ITStd_197']." Password: ".$row_rsForm2['ITStd_198']."<BR>
87. Employee Name: ".$row_rsForm2['ITStd_199']." User Name: ".$row_rsForm2['ITStd_200']." Password: ".$row_rsForm2['ITStd_201']."<BR>
88. Employee Name: ".$row_rsForm2['ITStd_202']." User Name: ".$row_rsForm2['ITStd_203']." Password: ".$row_rsForm2['ITStd_204']."<BR>
89. Employee Name: ".$row_rsForm2['ITStd_205']." User Name: ".$row_rsForm2['ITStd_206']." Password: ".$row_rsForm2['ITStd_207']."<BR>
90. Employee Name: ".$row_rsForm2['ITStd_208']." User Name: ".$row_rsForm2['ITStd_209']." Password: ".$row_rsForm2['ITStd_210']."<BR>
91. Employee Name: ".$row_rsForm2['ITStd_211']." User Name: ".$row_rsForm2['ITStd_212']." Password: ".$row_rsForm2['ITStd_213']."<BR>
92. Employee Name: ".$row_rsForm2['ITStd_214']." User Name: ".$row_rsForm2['ITStd_215']." Password: ".$row_rsForm2['ITStd_216']."<BR>
93. Employee Name: ".$row_rsForm2['ITStd_217']." User Name: ".$row_rsForm2['ITStd_218']." Password: ".$row_rsForm2['ITStd_219']."<BR>
94. Employee Name: ".$row_rsForm2['ITStd_220']." User Name: ".$row_rsForm2['ITStd_221']." Password: ".$row_rsForm2['ITStd_222']."<BR>
95. Employee Name: ".$row_rsForm2['ITStd_223']." User Name: ".$row_rsForm2['ITStd_224']." Password: ".$row_rsForm2['ITStd_225']."<BR>
96. Employee Name: ".$row_rsForm2['ITStd_226']." User Name: ".$row_rsForm2['ITStd_227']." Password: ".$row_rsForm2['ITStd_228']."<BR>
97. Employee Name: ".$row_rsForm2['ITStd_229']." User Name: ".$row_rsForm2['ITStd_230']." Password: ".$row_rsForm2['ITStd_231']."<BR>
98. Employee Name: ".$row_rsForm2['ITStd_232']." User Name: ".$row_rsForm2['ITStd_233']." Password: ".$row_rsForm2['ITStd_234']."<BR>
99. Employee Name: ".$row_rsForm2['ITStd_235']." User Name: ".$row_rsForm2['ITStd_236']." Password: ".$row_rsForm2['ITStd_237']."<BR>
100. Employee Name: ".$row_rsForm2['ITStd_238']." User Name: ".$row_rsForm2['ITStd_239']." Password: ".$row_rsForm2['ITStd_240']."<BR>
101. Employee Name: ".$row_rsForm2['ITStd_241']." User Name: ".$row_rsForm2['ITStd_242']." Password: ".$row_rsForm2['ITStd_243']."<BR>
102. Employee Name: ".$row_rsForm2['ITStd_244']." User Name: ".$row_rsForm2['ITStd_245']." Password: ".$row_rsForm2['ITStd_246']."<BR>
103. Employee Name: ".$row_rsForm2['ITStd_247']." User Name: ".$row_rsForm2['ITStd_248']." Password: ".$row_rsForm2['ITStd_249']."<BR>
104. Employee Name: ".$row_rsForm2['ITStd_250']." User Name: ".$row_rsForm2['ITStd_251']." Password: ".$row_rsForm2['ITStd_252']."<BR>
105. Employee Name: ".$row_rsForm2['ITStd_253']." User Name: ".$row_rsForm2['ITStd_254']." Password: ".$row_rsForm2['ITStd_255']."<BR>
106. Employee Name: ".$row_rsForm2['ITStd_256']." User Name: ".$row_rsForm2['ITStd_257']." Password: ".$row_rsForm2['ITStd_258']."<BR>
107. Employee Name: ".$row_rsForm2['ITStd_259']." User Name: ".$row_rsForm2['ITStd_260']." Password: ".$row_rsForm2['ITStd_261']."<BR>
108. Employee Name: ".$row_rsForm2['ITStd_262']." User Name: ".$row_rsForm2['ITStd_263']." Password: ".$row_rsForm2['ITStd_264']."<BR>
109. Employee Name: ".$row_rsForm2['ITStd_265']." User Name: ".$row_rsForm2['ITStd_266']." Password: ".$row_rsForm2['ITStd_267']."<BR>
110. Employee Name: ".$row_rsForm2['ITStd_268']." User Name: ".$row_rsForm2['ITStd_269']." Password: ".$row_rsForm2['ITStd_270']."<BR>
111. Employee Name: ".$row_rsForm2['ITStd_271']." User Name: ".$row_rsForm2['ITStd_272']." Password: ".$row_rsForm2['ITStd_273']."<BR>
112. Employee Name: ".$row_rsForm2['ITStd_274']." User Name: ".$row_rsForm2['ITStd_275']." Password: ".$row_rsForm2['ITStd_276']."<BR>
113. Employee Name: ".$row_rsForm2['ITStd_277']." User Name: ".$row_rsForm2['ITStd_278']." Password: ".$row_rsForm2['ITStd_279']."<BR>
114. Employee Name: ".$row_rsForm2['ITStd_280']." User Name: ".$row_rsForm2['ITStd_281']." Password: ".$row_rsForm2['ITStd_282']."<BR>
115. Employee Name: ".$row_rsForm2['ITStd_283']." User Name: ".$row_rsForm2['ITStd_284']." Password: ".$row_rsForm2['ITStd_285']."<BR>
116. Employee Name: ".$row_rsForm2['ITStd_286']." User Name: ".$row_rsForm2['ITStd_287']." Password: ".$row_rsForm2['ITStd_288']."<BR>
117. Employee Name: ".$row_rsForm2['ITStd_289']." User Name: ".$row_rsForm2['ITStd_290']." Password: ".$row_rsForm2['ITStd_291']."<BR>
118. Employee Name: ".$row_rsForm2['ITStd_292']." User Name: ".$row_rsForm2['ITStd_293']." Password: ".$row_rsForm2['ITStd_294']."<BR>
119. Employee Name: ".$row_rsForm2['ITStd_295']." User Name: ".$row_rsForm2['ITStd_296']." Password: ".$row_rsForm2['ITStd_297']."<BR>
120. Employee Name: ".$row_rsForm2['ITStd_298']." User Name: ".$row_rsForm2['ITStd_299']." Password: ".$row_rsForm2['ITStd_300']."<BR>
121. Employee Name: ".$row_rsForm2['ITStd_301']." User Name: ".$row_rsForm2['ITStd_302']." Password: ".$row_rsForm2['ITStd_303']."<BR>
122. Employee Name: ".$row_rsForm2['ITStd_304']." User Name: ".$row_rsForm2['ITStd_305']." Password: ".$row_rsForm2['ITStd_306']."<BR>
123. Employee Name: ".$row_rsForm2['ITStd_307']." User Name: ".$row_rsForm2['ITStd_308']." Password: ".$row_rsForm2['ITStd_309']."<BR>
124. Employee Name: ".$row_rsForm2['ITStd_310']." User Name: ".$row_rsForm2['ITStd_311']." Password: ".$row_rsForm2['ITStd_312']."<BR>
125. Employee Name: ".$row_rsForm2['ITStd_313']." User Name: ".$row_rsForm2['ITStd_314']." Password: ".$row_rsForm2['ITStd_315']."<BR>
126. Employee Name: ".$row_rsForm2['ITStd_316']." User Name: ".$row_rsForm2['ITStd_317']." Password: ".$row_rsForm2['ITStd_318']."<BR>
127. Employee Name: ".$row_rsForm2['ITStd_319']." User Name: ".$row_rsForm2['ITStd_320']." Password: ".$row_rsForm2['ITStd_321']."<BR>
128. Employee Name: ".$row_rsForm2['ITStd_322']." User Name: ".$row_rsForm2['ITStd_323']." Password: ".$row_rsForm2['ITStd_324']."<BR>
129. Employee Name: ".$row_rsForm2['ITStd_325']." User Name: ".$row_rsForm2['ITStd_326']." Password: ".$row_rsForm2['ITStd_327']."<BR>
130. Employee Name: ".$row_rsForm2['ITStd_328']." User Name: ".$row_rsForm2['ITStd_329']." Password: ".$row_rsForm2['ITStd_330']."<BR>
131. Employee Name: ".$row_rsForm2['ITStd_331']." User Name: ".$row_rsForm2['ITStd_332']." Password: ".$row_rsForm2['ITStd_333']."<BR>
132. Employee Name: ".$row_rsForm2['ITStd_334']." User Name: ".$row_rsForm2['ITStd_335']." Password: ".$row_rsForm2['ITStd_336']."<BR>
133. Employee Name: ".$row_rsForm2['ITStd_337']." User Name: ".$row_rsForm2['ITStd_338']." Password: ".$row_rsForm2['ITStd_339']."<BR>
134. Employee Name: ".$row_rsForm2['ITStd_340']." User Name: ".$row_rsForm2['ITStd_341']." Password: ".$row_rsForm2['ITStd_342']."<BR>
135. Employee Name: ".$row_rsForm2['ITStd_343']." User Name: ".$row_rsForm2['ITStd_344']." Password: ".$row_rsForm2['ITStd_345']."<BR>
136. Employee Name: ".$row_rsForm2['ITStd_346']." User Name: ".$row_rsForm2['ITStd_347']." Password: ".$row_rsForm2['ITStd_348']."<BR>
137. Employee Name: ".$row_rsForm2['ITStd_349']." User Name: ".$row_rsForm2['ITStd_350']." Password: ".$row_rsForm2['ITStd_351']."<BR>
138. Employee Name: ".$row_rsForm2['ITStd_352']." User Name: ".$row_rsForm2['ITStd_353']." Password: ".$row_rsForm2['ITStd_354']."<BR>
139. Employee Name: ".$row_rsForm2['ITStd_355']." User Name: ".$row_rsForm2['ITStd_356']." Password: ".$row_rsForm2['ITStd_357']."<BR>
140. Employee Name: ".$row_rsForm2['ITStd_358']." User Name: ".$row_rsForm2['ITStd_359']." Password: ".$row_rsForm2['ITStd_360']."<BR>
141. Employee Name: ".$row_rsForm2['ITStd_361']." User Name: ".$row_rsForm2['ITStd_362']." Password: ".$row_rsForm2['ITStd_363']."<BR>
142. Employee Name: ".$row_rsForm2['ITStd_364']." User Name: ".$row_rsForm2['ITStd_365']." Password: ".$row_rsForm2['ITStd_366']."<BR>
143. Employee Name: ".$row_rsForm2['ITStd_367']." User Name: ".$row_rsForm2['ITStd_368']." Password: ".$row_rsForm2['ITStd_369']."<BR>
144. Employee Name: ".$row_rsForm2['ITStd_370']." User Name: ".$row_rsForm2['ITStd_371']." Password: ".$row_rsForm2['ITStd_372']."<BR>
145. Employee Name: ".$row_rsForm2['ITStd_373']." User Name: ".$row_rsForm2['ITStd_374']." Password: ".$row_rsForm2['ITStd_375']."<BR>
146. Employee Name: ".$row_rsForm2['ITStd_376']." User Name: ".$row_rsForm2['ITStd_377']." Password: ".$row_rsForm2['ITStd_378']."<BR>
147. Employee Name: ".$row_rsForm2['ITStd_379']." User Name: ".$row_rsForm2['ITStd_380']." Password: ".$row_rsForm2['ITStd_381']."<BR>
148. Employee Name: ".$row_rsForm2['ITStd_382']." User Name: ".$row_rsForm2['ITStd_383']." Password: ".$row_rsForm2['ITStd_384']."<BR>
149. Employee Name: ".$row_rsForm2['ITStd_385']." User Name: ".$row_rsForm2['ITStd_386']." Password: ".$row_rsForm2['ITStd_387']."<BR>
150. Employee Name: ".$row_rsForm2['ITStd_388']." User Name: ".$row_rsForm2['ITStd_389']." Password: ".$row_rsForm2['ITStd_390']."<BR></P>");
		}//end of if

		$pdf->BoldText("<BR><BR><P align='left'>User Names & Passwords:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Program 2<BR>
The following is a list of the current User Names & Password Used by ".$strBusName.". In the event of a disaster and access is needed you may use this information to access any required information. Information is protected and secured by our online security features.
<BR><BR>
Program #2 : ".$row_rsForm['IT_2EMPro02']."
<BR><BR>
1. Employee Name: ".$row_rsForm['IT_2EMP01']." User Name: ".$row_rsForm['IT_2USER01']." Password: ".$row_rsForm['IT_2PASS01']."<BR>
2. Employee Name: ".$row_rsForm['IT_2EMP02']." User Name: ".$row_rsForm['IT_2USER02']." Password: ".$row_rsForm['IT_2PASS02']."<BR>
3. Employee Name: ".$row_rsForm['IT_2EMP03']." User Name: ".$row_rsForm['IT_2USER03']." Password: ".$row_rsForm['IT_2PASS03']."<BR>
4. Employee Name: ".$row_rsForm['IT_2EMP04']." User Name: ".$row_rsForm['IT_2USER04']." Password: ".$row_rsForm['IT_2PASS04']."<BR>
5. Employee Name: ".$row_rsForm['IT_2EMP05']." User Name: ".$row_rsForm['IT_2USER05']." Password: ".$row_rsForm['IT_2PASS05']."<BR>
6. Employee Name: ".$row_rsForm['IT_2EMP06']." User Name: ".$row_rsForm['IT_2USER06']." Password: ".$row_rsForm['IT_2PASS06']."<BR>
7. Employee Name: ".$row_rsForm['IT_2EMP07']." User Name: ".$row_rsForm['IT_2USER07']." Password: ".$row_rsForm['IT_2PASS07']."<BR>
8. Employee Name: ".$row_rsForm['IT_2EMP08']." User Name: ".$row_rsForm['IT_2USER08']." Password: ".$row_rsForm['IT_2PASS08']."<BR>
9. Employee Name: ".$row_rsForm['IT_2EMP09']." User Name: ".$row_rsForm['IT_2USER09']." Password: ".$row_rsForm['IT_2PASS09']."<BR>
10. Employee Name: ".$row_rsForm['IT_2EMP10']." User Name: ".$row_rsForm['IT_2USER10']." Password: ".$row_rsForm['IT_2PASS10']."<BR>
11. Employee Name: ".$row_rsForm['IT_2EMP11']." User Name: ".$row_rsForm['IT_2USER11']." Password: ".$row_rsForm['IT_2PASS11']."<BR>
12. Employee Name: ".$row_rsForm['IT_2EMP12']." User Name: ".$row_rsForm['IT_2USER12']." Password: ".$row_rsForm['IT_2PASS12']."<BR>
13. Employee Name: ".$row_rsForm['IT_2EMP13']." User Name: ".$row_rsForm['IT_2USER13']." Password: ".$row_rsForm['IT_2PASS13']."<BR>
14. Employee Name: ".$row_rsForm['IT_2EMP14']." User Name: ".$row_rsForm['IT_2USER14']." Password: ".$row_rsForm['IT_2PASS14']."<BR>
15. Employee Name: ".$row_rsForm['IT_2EMP15']." User Name: ".$row_rsForm['IT_2USER15']." Password: ".$row_rsForm['IT_2PASS15']."<BR>
16. Employee Name: ".$row_rsForm['IT_2EMP16']." User Name: ".$row_rsForm['IT_2USER16']." Password: ".$row_rsForm['IT_2PASS16']."<BR>
17. Employee Name: ".$row_rsForm['IT_2EMP17']." User Name: ".$row_rsForm['IT_2USER17']." Password: ".$row_rsForm['IT_2PASS17']."<BR>
18. Employee Name: ".$row_rsForm['IT_2EMP18']." User Name: ".$row_rsForm['IT_2USER18']." Password: ".$row_rsForm['IT_2PASS18']."<BR>
19. Employee Name: ".$row_rsForm['IT_2EMP19']." User Name: ".$row_rsForm['IT_2USER19']." Password: ".$row_rsForm['IT_2PASS19']."<BR>
20. Employee Name: ".$row_rsForm['IT_2EMP20']." User Name: ".$row_rsForm['IT_2USER20']." Password: ".$row_rsForm['IT_2PASS20']."<BR></P>");


		// this will display only for Standard
		if ($row_loginFoundUser['Solution'] == 2)
		{
			$pdf->QuestionsBody("<P align='left'>
21. Employee Name: ".$row_rsForm2['ITStd_391']." User Name: ".$row_rsForm2['ITStd_392']." Password: ".$row_rsForm2['ITStd_393']."<BR>
22. Employee Name: ".$row_rsForm2['ITStd_394']." User Name: ".$row_rsForm2['ITStd_395']." Password: ".$row_rsForm2['ITStd_396']."<BR>
23. Employee Name: ".$row_rsForm2['ITStd_397']." User Name: ".$row_rsForm2['ITStd_398']." Password: ".$row_rsForm2['ITStd_399']."<BR>
24. Employee Name: ".$row_rsForm2['ITStd_400']." User Name: ".$row_rsForm2['ITStd_401']." Password: ".$row_rsForm2['ITStd_402']."<BR>
25. Employee Name: ".$row_rsForm2['ITStd_403']." User Name: ".$row_rsForm2['ITStd_404']." Password: ".$row_rsForm2['ITStd_405']."<BR>
26. Employee Name: ".$row_rsForm2['ITStd_406']." User Name: ".$row_rsForm2['ITStd_407']." Password: ".$row_rsForm2['ITStd_408']."<BR>
27. Employee Name: ".$row_rsForm2['ITStd_409']." User Name: ".$row_rsForm2['ITStd_410']." Password: ".$row_rsForm2['ITStd_411']."<BR>
28. Employee Name: ".$row_rsForm2['ITStd_412']." User Name: ".$row_rsForm2['ITStd_413']." Password: ".$row_rsForm2['ITStd_414']."<BR>
29. Employee Name: ".$row_rsForm2['ITStd_415']." User Name: ".$row_rsForm2['ITStd_416']." Password: ".$row_rsForm2['ITStd_417']."<BR>
30. Employee Name: ".$row_rsForm2['ITStd_418']." User Name: ".$row_rsForm2['ITStd_419']." Password: ".$row_rsForm2['ITStd_420']."<BR>
31. Employee Name: ".$row_rsForm2['ITStd_421']." User Name: ".$row_rsForm2['ITStd_422']." Password: ".$row_rsForm2['ITStd_423']."<BR>
32. Employee Name: ".$row_rsForm2['ITStd_424']." User Name: ".$row_rsForm2['ITStd_425']." Password: ".$row_rsForm2['ITStd_426']."<BR>
33. Employee Name: ".$row_rsForm2['ITStd_427']." User Name: ".$row_rsForm2['ITStd_428']." Password: ".$row_rsForm2['ITStd_429']."<BR>
34. Employee Name: ".$row_rsForm2['ITStd_430']." User Name: ".$row_rsForm2['ITStd_431']." Password: ".$row_rsForm2['ITStd_432']."<BR>
35. Employee Name: ".$row_rsForm2['ITStd_433']." User Name: ".$row_rsForm2['ITStd_434']." Password: ".$row_rsForm2['ITStd_435']."<BR>
36. Employee Name: ".$row_rsForm2['ITStd_436']." User Name: ".$row_rsForm2['ITStd_437']." Password: ".$row_rsForm2['ITStd_438']."<BR>
37. Employee Name: ".$row_rsForm2['ITStd_439']." User Name: ".$row_rsForm2['ITStd_440']." Password: ".$row_rsForm2['ITStd_441']."<BR>
38. Employee Name: ".$row_rsForm2['ITStd_442']." User Name: ".$row_rsForm2['ITStd_443']." Password: ".$row_rsForm2['ITStd_444']."<BR>
39. Employee Name: ".$row_rsForm2['ITStd_445']." User Name: ".$row_rsForm2['ITStd_446']." Password: ".$row_rsForm2['ITStd_447']."<BR>
40. Employee Name: ".$row_rsForm2['ITStd_448']." User Name: ".$row_rsForm2['ITStd_449']." Password: ".$row_rsForm2['ITStd_450']."<BR>
41. Employee Name: ".$row_rsForm2['ITStd_451']." User Name: ".$row_rsForm2['ITStd_452']." Password: ".$row_rsForm2['ITStd_453']."<BR>
42. Employee Name: ".$row_rsForm2['ITStd_454']." User Name: ".$row_rsForm2['ITStd_455']." Password: ".$row_rsForm2['ITStd_456']."<BR>
43. Employee Name: ".$row_rsForm2['ITStd_457']." User Name: ".$row_rsForm2['ITStd_458']." Password: ".$row_rsForm2['ITStd_459']."<BR>
44. Employee Name: ".$row_rsForm2['ITStd_460']." User Name: ".$row_rsForm2['ITStd_461']." Password: ".$row_rsForm2['ITStd_462']."<BR>
45. Employee Name: ".$row_rsForm2['ITStd_463']." User Name: ".$row_rsForm2['ITStd_464']." Password: ".$row_rsForm2['ITStd_465']."<BR>
46. Employee Name: ".$row_rsForm2['ITStd_466']." User Name: ".$row_rsForm2['ITStd_467']." Password: ".$row_rsForm2['ITStd_468']."<BR>
47. Employee Name: ".$row_rsForm2['ITStd_469']." User Name: ".$row_rsForm2['ITStd_470']." Password: ".$row_rsForm2['ITStd_471']."<BR>
48. Employee Name: ".$row_rsForm2['ITStd_472']." User Name: ".$row_rsForm2['ITStd_473']." Password: ".$row_rsForm2['ITStd_474']."<BR>
49. Employee Name: ".$row_rsForm2['ITStd_475']." User Name: ".$row_rsForm2['ITStd_476']." Password: ".$row_rsForm2['ITStd_477']."<BR>
50. Employee Name: ".$row_rsForm2['ITStd_478']." User Name: ".$row_rsForm2['ITStd_479']." Password: ".$row_rsForm2['ITStd_480']."<BR>
51. Employee Name: ".$row_rsForm2['ITStd_481']." User Name: ".$row_rsForm2['ITStd_482']." Password: ".$row_rsForm2['ITStd_483']."<BR>
52. Employee Name: ".$row_rsForm2['ITStd_484']." User Name: ".$row_rsForm2['ITStd_485']." Password: ".$row_rsForm2['ITStd_486']."<BR>
53. Employee Name: ".$row_rsForm2['ITStd_487']." User Name: ".$row_rsForm2['ITStd_488']." Password: ".$row_rsForm2['ITStd_489']."<BR>
54. Employee Name: ".$row_rsForm2['ITStd_490']." User Name: ".$row_rsForm2['ITStd_491']." Password: ".$row_rsForm2['ITStd_492']."<BR>
55. Employee Name: ".$row_rsForm2['ITStd_493']." User Name: ".$row_rsForm2['ITStd_494']." Password: ".$row_rsForm2['ITStd_495']."<BR>
56. Employee Name: ".$row_rsForm2['ITStd_496']." User Name: ".$row_rsForm2['ITStd_497']." Password: ".$row_rsForm2['ITStd_498']."<BR>
57. Employee Name: ".$row_rsForm2['ITStd_499']." User Name: ".$row_rsForm2['ITStd_500']." Password: ".$row_rsForm2['ITStd_501']."<BR>
58. Employee Name: ".$row_rsForm2['ITStd_502']." User Name: ".$row_rsForm2['ITStd_503']." Password: ".$row_rsForm2['ITStd_504']."<BR>
59. Employee Name: ".$row_rsForm2['ITStd_505']." User Name: ".$row_rsForm2['ITStd_506']." Password: ".$row_rsForm2['ITStd_507']."<BR>
60. Employee Name: ".$row_rsForm2['ITStd_508']." User Name: ".$row_rsForm2['ITStd_509']." Password: ".$row_rsForm2['ITStd_510']."<BR>
61. Employee Name: ".$row_rsForm2['ITStd_511']." User Name: ".$row_rsForm2['ITStd_512']." Password: ".$row_rsForm2['ITStd_513']."<BR>
62. Employee Name: ".$row_rsForm2['ITStd_514']." User Name: ".$row_rsForm2['ITStd_515']." Password: ".$row_rsForm2['ITStd_516']."<BR>
63. Employee Name: ".$row_rsForm2['ITStd_517']." User Name: ".$row_rsForm2['ITStd_518']." Password: ".$row_rsForm2['ITStd_519']."<BR>
64. Employee Name: ".$row_rsForm2['ITStd_520']." User Name: ".$row_rsForm2['ITStd_521']." Password: ".$row_rsForm2['ITStd_522']."<BR>
65. Employee Name: ".$row_rsForm2['ITStd_523']." User Name: ".$row_rsForm2['ITStd_524']." Password: ".$row_rsForm2['ITStd_525']."<BR>
66. Employee Name: ".$row_rsForm2['ITStd_526']." User Name: ".$row_rsForm2['ITStd_527']." Password: ".$row_rsForm2['ITStd_528']."<BR>
67. Employee Name: ".$row_rsForm2['ITStd_529']." User Name: ".$row_rsForm2['ITStd_530']." Password: ".$row_rsForm2['ITStd_531']."<BR>
68. Employee Name: ".$row_rsForm2['ITStd_532']." User Name: ".$row_rsForm2['ITStd_533']." Password: ".$row_rsForm2['ITStd_534']."<BR>
69. Employee Name: ".$row_rsForm2['ITStd_535']." User Name: ".$row_rsForm2['ITStd_536']." Password: ".$row_rsForm2['ITStd_537']."<BR>
70. Employee Name: ".$row_rsForm2['ITStd_538']." User Name: ".$row_rsForm2['ITStd_539']." Password: ".$row_rsForm2['ITStd_540']."<BR>
71. Employee Name: ".$row_rsForm2['ITStd_541']." User Name: ".$row_rsForm2['ITStd_542']." Password: ".$row_rsForm2['ITStd_543']."<BR>
72. Employee Name: ".$row_rsForm2['ITStd_544']." User Name: ".$row_rsForm2['ITStd_545']." Password: ".$row_rsForm2['ITStd_546']."<BR>
73. Employee Name: ".$row_rsForm2['ITStd_547']." User Name: ".$row_rsForm2['ITStd_548']." Password: ".$row_rsForm2['ITStd_549']."<BR>
74. Employee Name: ".$row_rsForm2['ITStd_550']." User Name: ".$row_rsForm2['ITStd_551']." Password: ".$row_rsForm2['ITStd_552']."<BR>
75. Employee Name: ".$row_rsForm2['ITStd_553']." User Name: ".$row_rsForm2['ITStd_554']." Password: ".$row_rsForm2['ITStd_555']."<BR>
76. Employee Name: ".$row_rsForm2['ITStd_556']." User Name: ".$row_rsForm2['ITStd_557']." Password: ".$row_rsForm2['ITStd_558']."<BR>
77. Employee Name: ".$row_rsForm2['ITStd_559']." User Name: ".$row_rsForm2['ITStd_560']." Password: ".$row_rsForm2['ITStd_561']."<BR>
78. Employee Name: ".$row_rsForm2['ITStd_562']." User Name: ".$row_rsForm2['ITStd_563']." Password: ".$row_rsForm2['ITStd_564']."<BR>
79. Employee Name: ".$row_rsForm2['ITStd_565']." User Name: ".$row_rsForm2['ITStd_566']." Password: ".$row_rsForm2['ITStd_567']."<BR>
80. Employee Name: ".$row_rsForm2['ITStd_568']." User Name: ".$row_rsForm2['ITStd_569']." Password: ".$row_rsForm2['ITStd_570']."<BR>
81. Employee Name: ".$row_rsForm2['ITStd_571']." User Name: ".$row_rsForm2['ITStd_572']." Password: ".$row_rsForm2['ITStd_573']."<BR>
82. Employee Name: ".$row_rsForm2['ITStd_574']." User Name: ".$row_rsForm2['ITStd_575']." Password: ".$row_rsForm2['ITStd_576']."<BR>
83. Employee Name: ".$row_rsForm2['ITStd_577']." User Name: ".$row_rsForm2['ITStd_578']." Password: ".$row_rsForm2['ITStd_579']."<BR>
84. Employee Name: ".$row_rsForm2['ITStd_580']." User Name: ".$row_rsForm2['ITStd_581']." Password: ".$row_rsForm2['ITStd_582']."<BR>
85. Employee Name: ".$row_rsForm2['ITStd_583']." User Name: ".$row_rsForm2['ITStd_584']." Password: ".$row_rsForm2['ITStd_585']."<BR>
86. Employee Name: ".$row_rsForm2['ITStd_586']." User Name: ".$row_rsForm2['ITStd_587']." Password: ".$row_rsForm2['ITStd_588']."<BR>
87. Employee Name: ".$row_rsForm2['ITStd_589']." User Name: ".$row_rsForm2['ITStd_590']." Password: ".$row_rsForm2['ITStd_591']."<BR>
88. Employee Name: ".$row_rsForm2['ITStd_592']." User Name: ".$row_rsForm2['ITStd_593']." Password: ".$row_rsForm2['ITStd_594']."<BR>
89. Employee Name: ".$row_rsForm2['ITStd_595']." User Name: ".$row_rsForm2['ITStd_596']." Password: ".$row_rsForm2['ITStd_597']."<BR>
90. Employee Name: ".$row_rsForm2['ITStd_598']." User Name: ".$row_rsForm2['ITStd_599']." Password: ".$row_rsForm2['ITStd_600']."<BR>
91. Employee Name: ".$row_rsForm2['ITStd_601']." User Name: ".$row_rsForm2['ITStd_602']." Password: ".$row_rsForm2['ITStd_603']."<BR>
92. Employee Name: ".$row_rsForm2['ITStd_604']." User Name: ".$row_rsForm2['ITStd_605']." Password: ".$row_rsForm2['ITStd_606']."<BR>
93. Employee Name: ".$row_rsForm2['ITStd_607']." User Name: ".$row_rsForm2['ITStd_608']." Password: ".$row_rsForm2['ITStd_609']."<BR>
94. Employee Name: ".$row_rsForm2['ITStd_610']." User Name: ".$row_rsForm2['ITStd_611']." Password: ".$row_rsForm2['ITStd_612']."<BR>
95. Employee Name: ".$row_rsForm2['ITStd_613']." User Name: ".$row_rsForm2['ITStd_614']." Password: ".$row_rsForm2['ITStd_615']."<BR>
96. Employee Name: ".$row_rsForm2['ITStd_616']." User Name: ".$row_rsForm2['ITStd_617']." Password: ".$row_rsForm2['ITStd_618']."<BR>
97. Employee Name: ".$row_rsForm2['ITStd_619']." User Name: ".$row_rsForm2['ITStd_620']." Password: ".$row_rsForm2['ITStd_621']."<BR>
98. Employee Name: ".$row_rsForm2['ITStd_622']." User Name: ".$row_rsForm2['ITStd_623']." Password: ".$row_rsForm2['ITStd_624']."<BR>
99. Employee Name: ".$row_rsForm2['ITStd_625']." User Name: ".$row_rsForm2['ITStd_626']." Password: ".$row_rsForm2['ITStd_627']."<BR>
100. Employee Name: ".$row_rsForm2['ITStd_628']." User Name: ".$row_rsForm2['ITStd_629']." Password: ".$row_rsForm2['ITStd_630']."<BR>
101. Employee Name: ".$row_rsForm2['ITStd_631']." User Name: ".$row_rsForm2['ITStd_632']." Password: ".$row_rsForm2['ITStd_633']."<BR>
102. Employee Name: ".$row_rsForm2['ITStd_634']." User Name: ".$row_rsForm2['ITStd_635']." Password: ".$row_rsForm2['ITStd_636']."<BR>
103. Employee Name: ".$row_rsForm2['ITStd_637']." User Name: ".$row_rsForm2['ITStd_638']." Password: ".$row_rsForm2['ITStd_639']."<BR>
104. Employee Name: ".$row_rsForm2['ITStd_640']." User Name: ".$row_rsForm2['ITStd_641']." Password: ".$row_rsForm2['ITStd_642']."<BR>
105. Employee Name: ".$row_rsForm2['ITStd_643']." User Name: ".$row_rsForm2['ITStd_644']." Password: ".$row_rsForm2['ITStd_645']."<BR>
106. Employee Name: ".$row_rsForm2['ITStd_646']." User Name: ".$row_rsForm2['ITStd_647']." Password: ".$row_rsForm2['ITStd_648']."<BR>
107. Employee Name: ".$row_rsForm2['ITStd_649']." User Name: ".$row_rsForm2['ITStd_650']." Password: ".$row_rsForm2['ITStd_651']."<BR>
108. Employee Name: ".$row_rsForm2['ITStd_652']." User Name: ".$row_rsForm2['ITStd_653']." Password: ".$row_rsForm2['ITStd_654']."<BR>
109. Employee Name: ".$row_rsForm2['ITStd_655']." User Name: ".$row_rsForm2['ITStd_656']." Password: ".$row_rsForm2['ITStd_657']."<BR>
110. Employee Name: ".$row_rsForm2['ITStd_658']." User Name: ".$row_rsForm2['ITStd_659']." Password: ".$row_rsForm2['ITStd_660']."<BR>
111. Employee Name: ".$row_rsForm2['ITStd_661']." User Name: ".$row_rsForm2['ITStd_662']." Password: ".$row_rsForm2['ITStd_663']."<BR>
112. Employee Name: ".$row_rsForm2['ITStd_664']." User Name: ".$row_rsForm2['ITStd_665']." Password: ".$row_rsForm2['ITStd_666']."<BR>
113. Employee Name: ".$row_rsForm2['ITStd_667']." User Name: ".$row_rsForm2['ITStd_668']." Password: ".$row_rsForm2['ITStd_669']."<BR>
114. Employee Name: ".$row_rsForm2['ITStd_670']." User Name: ".$row_rsForm2['ITStd_671']." Password: ".$row_rsForm2['ITStd_672']."<BR>
115. Employee Name: ".$row_rsForm2['ITStd_673']." User Name: ".$row_rsForm2['ITStd_674']." Password: ".$row_rsForm2['ITStd_675']."<BR>
116. Employee Name: ".$row_rsForm2['ITStd_676']." User Name: ".$row_rsForm2['ITStd_677']." Password: ".$row_rsForm2['ITStd_678']."<BR>
117. Employee Name: ".$row_rsForm2['ITStd_679']." User Name: ".$row_rsForm2['ITStd_680']." Password: ".$row_rsForm2['ITStd_681']."<BR>
118. Employee Name: ".$row_rsForm2['ITStd_682']." User Name: ".$row_rsForm2['ITStd_683']." Password: ".$row_rsForm2['ITStd_684']."<BR>
119. Employee Name: ".$row_rsForm2['ITStd_685']." User Name: ".$row_rsForm2['ITStd_686']." Password: ".$row_rsForm2['ITStd_687']."<BR>
120. Employee Name: ".$row_rsForm2['ITStd_688']." User Name: ".$row_rsForm2['ITStd_689']." Password: ".$row_rsForm2['ITStd_690']."<BR>
121. Employee Name: ".$row_rsForm2['ITStd_691']." User Name: ".$row_rsForm2['ITStd_692']." Password: ".$row_rsForm2['ITStd_693']."<BR>
122. Employee Name: ".$row_rsForm2['ITStd_694']." User Name: ".$row_rsForm2['ITStd_695']." Password: ".$row_rsForm2['ITStd_696']."<BR>
123. Employee Name: ".$row_rsForm2['ITStd_697']." User Name: ".$row_rsForm2['ITStd_698']." Password: ".$row_rsForm2['ITStd_699']."<BR>
124. Employee Name: ".$row_rsForm2['ITStd_700']." User Name: ".$row_rsForm2['ITStd_701']." Password: ".$row_rsForm2['ITStd_702']."<BR>
125. Employee Name: ".$row_rsForm2['ITStd_703']." User Name: ".$row_rsForm2['ITStd_704']." Password: ".$row_rsForm2['ITStd_705']."<BR>
126. Employee Name: ".$row_rsForm2['ITStd_706']." User Name: ".$row_rsForm2['ITStd_707']." Password: ".$row_rsForm2['ITStd_708']."<BR>
127. Employee Name: ".$row_rsForm2['ITStd_709']." User Name: ".$row_rsForm2['ITStd_710']." Password: ".$row_rsForm2['ITStd_711']."<BR>
128. Employee Name: ".$row_rsForm2['ITStd_712']." User Name: ".$row_rsForm2['ITStd_713']." Password: ".$row_rsForm2['ITStd_714']."<BR>
129. Employee Name: ".$row_rsForm2['ITStd_715']." User Name: ".$row_rsForm2['ITStd_716']." Password: ".$row_rsForm2['ITStd_717']."<BR>
130. Employee Name: ".$row_rsForm2['ITStd_718']." User Name: ".$row_rsForm2['ITStd_719']." Password: ".$row_rsForm2['ITStd_720']."<BR>
131. Employee Name: ".$row_rsForm2['ITStd_721']." User Name: ".$row_rsForm2['ITStd_722']." Password: ".$row_rsForm2['ITStd_723']."<BR>
132. Employee Name: ".$row_rsForm2['ITStd_724']." User Name: ".$row_rsForm2['ITStd_725']." Password: ".$row_rsForm2['ITStd_726']."<BR>
133. Employee Name: ".$row_rsForm2['ITStd_727']." User Name: ".$row_rsForm2['ITStd_728']." Password: ".$row_rsForm2['ITStd_729']."<BR>
134. Employee Name: ".$row_rsForm2['ITStd_730']." User Name: ".$row_rsForm2['ITStd_731']." Password: ".$row_rsForm2['ITStd_732']."<BR>
135. Employee Name: ".$row_rsForm2['ITStd_733']." User Name: ".$row_rsForm2['ITStd_734']." Password: ".$row_rsForm2['ITStd_735']."<BR>
136. Employee Name: ".$row_rsForm2['ITStd_736']." User Name: ".$row_rsForm2['ITStd_737']." Password: ".$row_rsForm2['ITStd_738']."<BR>
137. Employee Name: ".$row_rsForm2['ITStd_739']." User Name: ".$row_rsForm2['ITStd_740']." Password: ".$row_rsForm2['ITStd_741']."<BR>
138. Employee Name: ".$row_rsForm2['ITStd_742']." User Name: ".$row_rsForm2['ITStd_743']." Password: ".$row_rsForm2['ITStd_744']."<BR>
139. Employee Name: ".$row_rsForm2['ITStd_745']." User Name: ".$row_rsForm2['ITStd_746']." Password: ".$row_rsForm2['ITStd_747']."<BR>
140. Employee Name: ".$row_rsForm2['ITStd_748']." User Name: ".$row_rsForm2['ITStd_749']." Password: ".$row_rsForm2['ITStd_750']."<BR>
141. Employee Name: ".$row_rsForm2['ITStd_751']." User Name: ".$row_rsForm2['ITStd_752']." Password: ".$row_rsForm2['ITStd_753']."<BR>
142. Employee Name: ".$row_rsForm2['ITStd_754']." User Name: ".$row_rsForm2['ITStd_755']." Password: ".$row_rsForm2['ITStd_756']."<BR>
143. Employee Name: ".$row_rsForm2['ITStd_757']." User Name: ".$row_rsForm2['ITStd_758']." Password: ".$row_rsForm2['ITStd_759']."<BR>
144. Employee Name: ".$row_rsForm2['ITStd_760']." User Name: ".$row_rsForm2['ITStd_761']." Password: ".$row_rsForm2['ITStd_762']."<BR>
145. Employee Name: ".$row_rsForm2['ITStd_763']." User Name: ".$row_rsForm2['ITStd_764']." Password: ".$row_rsForm2['ITStd_765']."<BR>
146. Employee Name: ".$row_rsForm2['ITStd_766']." User Name: ".$row_rsForm2['ITStd_767']." Password: ".$row_rsForm2['ITStd_768']."<BR>
147. Employee Name: ".$row_rsForm2['ITStd_769']." User Name: ".$row_rsForm2['ITStd_770']." Password: ".$row_rsForm2['ITStd_771']."<BR>
148. Employee Name: ".$row_rsForm2['ITStd_772']." User Name: ".$row_rsForm2['ITStd_773']." Password: ".$row_rsForm2['ITStd_774']."<BR>
149. Employee Name: ".$row_rsForm2['ITStd_775']." User Name: ".$row_rsForm2['ITStd_776']." Password: ".$row_rsForm2['ITStd_777']."<BR>
150. Employee Name: ".$row_rsForm2['ITStd_778']." User Name: ".$row_rsForm2['ITStd_779']." Password: ".$row_rsForm2['ITStd_780']."<BR></P>");
		}//end of if

		//creates the page to be used
		$pdf->AddPage();

		$pdf->BoldText("<P align='left'>IN THE EVENT OF A DISASTER OR A LOSS YOU WILL NEED TO HAVE ACCESS TO YOU ESSENTIAL DATA.
PLEASE CREATE A LIST OF THE DATA OR SYSTEMS YOU WILL NEED TO HAVE ACCESS TO IMMEDIATELY, IN
ORDER OR PRIORITY. MOST IMPORTANT INFORMATION FIRST.
<BR><BR>
Essential Data Source - Priority #1: <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_DS01']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Data Source - Priority #2: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_DS02']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Data Source - Priority #3: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_DS03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Data Source - Priority #4: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_DS04']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Data Source - Priority #5: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_DS05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Data Source - Priority #6: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_DS06']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Data Source - Priority #7: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_DS07']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>WEB-MASTER - SERVICE PROVIDER:
IN THE EVENT OF A DISASTER YOU MAY WANT TO USE YOUR WEB-SITE AS A COMMUNICATION METHOD FOR YOUR EMPLOYEES, CUSTOMER AND ALL OTHER PEOPLE INVOLVED IN YOUR BUSINESS.<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Company Name: ".$row_rsForm['IT_web01']."
Contact Person: ".$row_rsForm['IT_web02']."
Phone: ".$row_rsForm['IT_web03']."
E-Mail: ".$row_rsForm['IT_web04']."
<BR><BR>
It Equipment & Suplies<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>The following is a list of the potential alternate suppliers of IT equipment, supplies and facilities if required:
<BR><BR>
IT Equipment Suppliers:
<BR><BR></P>");
		$pdf->BoldText("Supplier 1 Name: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_SUPP01']."<BR><BR>Phone: ".$row_rsForm['IT_SUPP02']."<BR>E-Mail: ".$row_rsForm['IT_SUPP03']."<BR><BR></P>");
		$pdf->BoldText("Supplier 2 Name: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_SUPP04']."<BR><BR>Phone: ".$row_rsForm['IT_SUPP05']."<BR>E-Mail: ".$row_rsForm['IT_SUPP06']."<BR><BR></P>");
		$pdf->BoldText("Supplier 3 Name: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['IT_SUPP07']."<BR><BR>Phone: ".$row_rsForm['IT_SUPP08']."<BR>E-Mail: ".$row_rsForm['IT_SUPP09']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Summary of Disaster Recovery Memberships:
<BR><BR>
For access to additional supplies, products and services, contact one of our professionals at Continuity Inc. who
can set you up with one of our Continuity Connections. These connections can allocate the appropriate resources,
products and service that you require before and after an event has occured.
<BR><BR>
Continuity Inc.</P>");
$pdf->QuestionsBody("<p align='left'> helpnow</p>",185,13,37);
	}//end of if
	
	//BusinessImpact 
	if($arrTablesName[$intTableIndex] == "C2BusinessImpact" && $row_loginFoundUser['Solution'] == 2)
	{	
		//updates the title and the Sub Section	
		$strAreaName = $arrAreaName[$intSectionArea].$intSectionTableIndex;
		$strSectionName = "";
				
		//adds one to the Section Table Index
		$intSectionTableIndex = $intSectionTableIndex + 1;
		
		//creates the page to be used
		$pdf->AddPage();
							
		//displays the Name of the Page for this Title Page
		$pdf->PageTitlePage("<P align='center'>".$row_rsPlans['sectionName']."</P>");
		
		//updates the Sub Section		
		$strSectionName = $row_rsPlans['sectionName'];
	
		//sets the Form Name
		//$pdf->FormName($row_rsPlans['sectionName']."<BR><BR></P>");
	
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->QuestionsBody("<P align='left'>This is the introduction to the idea of providing uninterrupted products and service. You will begin to determine acceptable down-times for each and identify the priority of recovery for these products and services that your business provides.
		<BR><BR>Every business has or should have an organizational chart that define each key Business Unit that exists within your
company. Based on your business units you will need to determine the main functions of these UNITS and what the essential functions are. You will also determine the amount of time you can accept as responsible.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The following will outline the essential Business Units and the operational requirements for each.<BR><BR>Essential Business Units:<BR><BR>Business Unit 1: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['BIA01_01']."<BR></P>");
		$pdf->BoldText("<P align='left'>Business Unit 2: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['BIA02_01']."<BR></P>");
		$pdf->BoldText("<P align='left'>Business Unit 3: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['BIA03_01']."<BR></P>");
		$pdf->BoldText("<P align='left'>Business Unit 4: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['BIA04_01']."<BR></P>");
		$pdf->BoldText("<P align='left'>Business Unit 5: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['BIA05_01']."<BR></P>");
		$pdf->BoldText("<P align='left'>Business Unit 6: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['BIA06_01']."<BR></P>");
		$pdf->BoldText("<P align='left'>Business Unit 7: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['BIA07_01']."<BR></P>");
		$pdf->BoldText("<P align='left'>Business Unit 8: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['BIA08_01']."<BR></P>");
		$pdf->BoldText("<P align='left'>Business Unit 9: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['BIA09_01']."<BR></P>");
		$pdf->BoldText("<P align='left'>Business Unit 10: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['BIA10_01']."<BR><BR></P>");
		
		$pdf->BoldText("<P align='left'>Essential Business Unit #1<BR><BR></P>");		
		$pdf->QuestionsBody("<P align='left'>Business Unit Name #1: ".$row_rsForm['BIA01_01']."<BR>Number of Employee’s Required for Operation: ".$row_rsForm['BIA01_05']."
		<BR><BR>Summary of Unit:<BR>".$row_rsForm['BIA01_02']."
		<BR><BR>Maximum Down-Time for Business Unit: ".$row_rsForm['BIA01_06']."<BR>Busiest Month for Business Unit: ".$row_rsForm['BIA01_04']."
		<BR><BR>Critical Documents that exists in this Unit: ".$row_rsForm['BIA01_03']."
		<BR><BR>In the event of a disaster you will need a minimum of: ".$row_rsForm['BIA01_07']." employees to operate this unit.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Business Unit #2<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Business Unit Name #1: ".$row_rsForm['BIA02_01']."<BR>Number of Employee’s Required for Operation: ".$row_rsForm['BIA02_05']."
		<BR><BR>Summary of Unit:<BR>".$row_rsForm['BIA02_02']."
		<BR><BR>Maximum Down-Time for Business Unit: ".$row_rsForm['BIA02_06']."<BR>Busiest Month for Business Unit: ".$row_rsForm['BIA02_04']."
		<BR><BR>Critical Documents that exists in this Unit: ".$row_rsForm['BIA02_03']."
		<BR><BR>In the event of a disaster you will need a minimum of: ".$row_rsForm['BIA02_07']." employees to operate this unit.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Business Unit #3<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Business Unit Name #1: ".$row_rsForm['BIA03_01']."<BR>Number of Employee’s Required for Operation: ".$row_rsForm['BIA03_05']."
		<BR><BR>Summary of Unit:<BR>".$row_rsForm['BIA03_02']."
		<BR><BR>Maximum Down-Time for Business Unit: ".$row_rsForm['BIA03_06']."<BR>Busiest Month for Business Unit: ".$row_rsForm['BIA03_04']."
		<BR><BR>Critical Documents that exists in this Unit: ".$row_rsForm['BIA03_03']."
		<BR><BR>In the event of a disaster you will need a minimum of: ".$row_rsForm['BIA03_07']." employees to operate this unit.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Business Unit #4<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Business Unit Name #1: ".$row_rsForm['BIA04_01']."<BR>Number of Employee’s Required for Operation: ".$row_rsForm['BIA04_05']."
		<BR><BR>Summary of Unit:<BR>".$row_rsForm['BIA04_02']."
		<BR><BR>Maximum Down-Time for Business Unit: ".$row_rsForm['BIA04_06']."<BR>Busiest Month for Business Unit: ".$row_rsForm['BIA04_04']."
		<BR><BR>Critical Documents that exists in this Unit: ".$row_rsForm['BIA04_03']."
		<BR><BR>In the event of a disaster you will need a minimum of: ".$row_rsForm['BIA04_07']." employees to operate this unit.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Business Unit #5<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Business Unit Name #1: ".$row_rsForm['BIA05_01']."<BR>Number of Employee’s Required for Operation: ".$row_rsForm['BIA05_05']."
		<BR><BR>Summary of Unit:<BR>".$row_rsForm['BIA05_02']."
		<BR><BR>Maximum Down-Time for Business Unit: ".$row_rsForm['BIA05_06']."<BR>Busiest Month for Business Unit: ".$row_rsForm['BIA05_04']."
		<BR><BR>Critical Documents that exists in this Unit: ".$row_rsForm['BIA05_03']."
		<BR><BR>In the event of a disaster you will need a minimum of: ".$row_rsForm['BIA05_07']." employees to operate this unit.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Business Unit #6<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Business Unit Name #1: ".$row_rsForm['BIA06_01']."<BR>Number of Employee’s Required for Operation: ".$row_rsForm['BIA06_05']."
		<BR><BR>Summary of Unit:<BR>".$row_rsForm['BIA06_02']."
		<BR><BR>Maximum Down-Time for Business Unit: ".$row_rsForm['BIA06_06']."<BR>Busiest Month for Business Unit: ".$row_rsForm['BIA06_04']."
		<BR><BR>Critical Documents that exists in this Unit: ".$row_rsForm['BIA06_03']."
		<BR><BR>In the event of a disaster you will need a minimum of: ".$row_rsForm['BIA06_07']." employees to operate this unit.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Business Unit #7<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Business Unit Name #1: ".$row_rsForm['BIA07_01']."<BR>Number of Employee’s Required for Operation: ".$row_rsForm['BIA07_05']."
		<BR><BR>Summary of Unit:<BR>".$row_rsForm['BIA07_02']."
		<BR><BR>Maximum Down-Time for Business Unit: ".$row_rsForm['BIA07_06']."<BR>Busiest Month for Business Unit: ".$row_rsForm['BIA07_04']."
		<BR><BR>Critical Documents that exists in this Unit: ".$row_rsForm['BIA07_03']."
		<BR><BR>In the event of a disaster you will need a minimum of: ".$row_rsForm['BIA07_07']." employees to operate this unit.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Business Unit #8<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Business Unit Name #1: ".$row_rsForm['BIA08_01']."<BR>Number of Employee’s Required for Operation: ".$row_rsForm['BIA08_05']."
		<BR><BR>Summary of Unit:<BR>".$row_rsForm['BIA08_02']."
		<BR><BR>Maximum Down-Time for Business Unit: ".$row_rsForm['BIA08_06']."<BR>Busiest Month for Business Unit: ".$row_rsForm['BIA08_04']."
		<BR><BR>Critical Documents that exists in this Unit: ".$row_rsForm['BIA08_03']."
		<BR><BR>In the event of a disaster you will need a minimum of: ".$row_rsForm['BIA08_07']." employees to operate this unit.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Business Unit #9<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Business Unit Name #1: ".$row_rsForm['BIA09_01']."<BR>Number of Employee’s Required for Operation: ".$row_rsForm['BIA09_05']."
		<BR><BR>Summary of Unit:<BR>".$row_rsForm['BIA09_02']."
		<BR><BR>Maximum Down-Time for Business Unit: ".$row_rsForm['BIA09_06']."<BR>Busiest Month for Business Unit: ".$row_rsForm['BIA09_04']."
		<BR><BR>Critical Documents that exists in this Unit: ".$row_rsForm['BIA09_03']."
		<BR><BR>In the event of a disaster you will need a minimum of: ".$row_rsForm['BIA09_07']." employees to operate this unit.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Essential Business Unit #10<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Business Unit Name #1: ".$row_rsForm['BIA10_01']."<BR>Number of Employee’s Required for Operation: ".$row_rsForm['BIA10_05']."
		<BR><BR>Summary of Unit:<BR>".$row_rsForm['BIA10_02']."
		<BR><BR>Maximum Down-Time for Business Unit: ".$row_rsForm['BIA10_06']."<BR>Busiest Month for Business Unit: ".$row_rsForm['BIA10_04']."
		<BR><BR>Critical Documents that exists in this Unit: ".$row_rsForm['BIA10_03']."
		<BR><BR>In the event of a disaster you will need a minimum of: ".$row_rsForm['BIA10_07']." employees to operate this unit</P>");
	}//end of if
	
	//Crisis 
	if($intTableIndex == 5)
	{
		$pdf->BoldText("<P align='left'>Crisis Communications Coordinator<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Name: ".$row_rsForm['comm_coor01']."<BR>
Titile: ".$row_rsForm['comm_coor02']."<BR>
Phone: ".$row_rsForm['comm_coor03']."<BR>
Cell: ".$row_rsForm['comm_coor04']."<BR>
E-Mail: ".$row_rsForm['comm_coor05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Objective<BR></P>");
		$pdf->QuestionsBody("<P align='left'>This plan will ensure ".$strBusName." has adequate resources in place to quickly and effectively meet the information needs of internal and external audiences in a crisis.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>1.2 Use<BR></P>");
		$pdf->QuestionsBody("<P align='left'>This plan is in support of ".$strBusName." Emergency Plan, and will be used when the business needs to respond to the information needs and concerns of their stakeholders in a crisis. At all times the crisis communications team will support emergency
operations and take a pro-active role with regard to internal and external communications; including public and
media relations.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>1.3 Notification<BR></P>");
		$pdf->QuestionsBody("<P align='left'>When notification of a crisis has occurred, the head Communications Officer or designate will immediately contact the internal and external contacts as required to jointly establish the level of communications support that may be required to assist
operations.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>1.4 Authority<BR></P>");
		$pdf->QuestionsBody("<P align='left'>The Coordinator will call in the communications resources that are required to effectively meet the needs of the crisis. All members of the crisis communications team will report to the Coordinator, who will act as the primary communications link
with operational staff and Disaster Management Team.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>2.1 General<BR></P>");
		$pdf->QuestionsBody("<P align='left'>At all times open lines of communication will be established with internal and external audiences. The type and severity of the crisis will determine which stakeholder audiences are involved.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>2.2 Internal Audiences<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Emergency operations personnel.<BR>
Immediate Response Team.<BR>
Disaster Recovery Teams.<BR>
Employees and other personnel.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>2.3 External Audiences<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Family members of Employees.<BR>
Neighboring communities.<BR>
Emergency response and recovery stakeholders<BR>
Media.<BR>
General public.<BR>
People directly impacted by the crisis.<BR>
Special interest groups.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>2.4 General Audience Information Needs<BR></P>");
		$pdf->QuestionsBody("<P align='left'>At the onset of a crisis the following information will need to be communicated to all audiences:
		<BR><BR>
An incident has occurred.<BR>
Nature, location and time of incident.<BR>
Status of public safety.<BR>
Actions to be taken.<BR>
Actions being taken to manage the crisis.<BR>
How and when further information will be available.<BR>
Where to go for further information.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>3.1 Media Relations Policy (Ensure all staff know the policy prior to an emergency).<BR></P>");
		$pdf->QuestionsBody("<P align='left'>At the onset of a crisis, the Crisis Communications Coordinator, will appoint a senior staff person to act as the official spokesperson for the community. The official spokesperson (or designate) will be the only person to speak on the community's
overall crisis response and recovery efforts. Political, strategic operational decisions and policy issues will be communicated
to the media through the spokesperson.
<BR><BR>
The Spokesperson, in conjunction with the Coordinator, will assign key operational personnel to support the spokesperson
and speak about matters within their area of expertise. Staff should refrain from speaking to the media on political, strategic
operational decisions or policy issues.
<BR><BR>
In their official emergency response capacity, ".$strBusName." employees with an emergency response or recovery role (i.e.Fire
Chief or designate) may agree to be interviewed by the media provided they only speak about matters within their area of
responsibility. At no time should these people speak to the media on political, strategic operational decisions or policy
issues. ".$strBusName." employees, who don't have a role in emergency response or recovery efforts, should not speak to the
media about the emergency unless they have received clearance through the Coordinator.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Response Personnel…<BR></P>");
		$pdf->QuestionsBody("<P align='left'>
	- May seek advice in advance from the Coordinator if desired or feasible.<BR>
	- May respond or refer the information request to their superior or the Coordinator.<BR>
	- Must refer questions that fall outside of their personal experience or expertise to their supervisor, subject matter experts,
or the Coordinator.<BR>
	- Must inform the Coordinator of the interview, and questions that fell outside of their area of expertise.<BR>
	- Must ensure the accuracy of any information provided.<BR>
	- Must ensure interviews are on the record and for attribution by name/title unless otherwise authorized.<BR>
	- Must inform the Coordinator of the results of the interview and any speculative questions.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>3.2 Media Relations Guidelines<BR></P>");
		$pdf->QuestionsBody("<P align='left'>When dealing with the media in an official capacity…<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Never<BR></P>");
		$pdf->QuestionsBody("<P align='left'>- Respond to media inquiries that fall outside personal experience or expertise, unless otherwise approved.<BR>
	- Undermine the safety of response personnel or the success of response and recovery operations.<BR>
	- Speculate about events, incidents, issues or future policy decisions. Must ensure interviews are on the record and for attribution
by name/title unless<BR>
	- Offer personal opinions.<BR>
	- Discuss advice given to superiors.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Always<BR></P>");
		$pdf->QuestionsBody("<P align='left'>- Seek advice and support from the Coordinator when desired or when in doubt about how to respond.<BR>
	- Agree to be interviewed only if you personally want to do it.<BR>
	- Respect the principal of security, policy, the judicial process and laws governing the disclosure of information.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>3.3 Preparing for Media Interviews<BR></P>");
		$pdf->QuestionsBody("<P align='left'>When possible and appropriate, the Coordinator and designates will prepare ".$strBusName." personnel for media interviews/briefings as follows:
<BR><BR>
	- Situation update.<BR>
	- Needs of reporter(s) – story angle, type of reporter, reporter's attitude, questions likely
to be asked, other organizations or people the reporter will be interviewing.<BR>
	- Public’s attitudes (general public, stakeholder organizations, special interest groups).<BR>
	- Potentially tough questions or issues that might come up.<BR>
	- Key messages.<BR>
	- Public Directives – i.e. listen to CFRB 1010, FOXY 88.5, 680 News on the FM dial for updates.<BR>
	- Issues to avoid.<BR>
	- Interview or briefing logistics (time, location, format, and time limit).<BR><BR></P>");
		$pdf->BoldText("<P align='left'>4.1 General Overview<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Key messages will help the ".$strBusName." effectively communicate to all audiences. Although key messages will change and evolve throughout emergency response and recovery operations, the messages should include:
<BR><BR>
	- The Business agenda and priorities.<BR>
	- Fact about what went well.<BR>
	- Facts that refute negatives.<BR>
	- Facts that support the Business’s story.<BR>
	- Public Info/ Directives.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>4.2 Key Messages<BR></P>");
		$pdf->QuestionsBody("<P align='left'>- A message of empathy for the impact the crisis has on people or the environment. (This does not mean the business is taking responsibility for the incident – only showing compassion toward those who have been impacted).
The businesses first priority is for public safety (include other priorities such as environmental impact).<BR>
	- We are working cooperatively with partner response agencies Continuity Inc. to effectively manage the crisis and minimize
its impact on people, the environment and our community as a whole.<BR>
	- Include a message about what is being done to manage the situation.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>SAMPLE MEDIA RESPONSE<BR></P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>".$strBusName." has an effective emergency response system in place. We have well-trained personnel who have practiced and tested the rollout of the emergency response plan. Our personnel are now doing what they have been trained to do.
Everything that can be done will be done. ".$strBusName." is taking a proactive role with regard to the release of information. New
information will be released to all interested parties when it becomes available. (Add any new information)
<BR><BR>
	- Support what is being done to manage the crisis.<BR>
	- Support what was done in advance of the crisis to reduce its occurrence and impact.<BR>
	- Reassure the public and help reduce their emotional reaction to the crisis.<BR>
	- Contain safety information.<BR>
	- Help emergency response personnel do their job.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Business Template Statements:<BR><BR>For Employees:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_EMP01']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>For Your Customers:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_CUST01']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>For Your Suppliers:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_SUPP01']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>5.1 Purpose<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Internal communications.<BR>
	- External stakeholder notification and coordination.<BR>
	- Media relations.<BR>
	- Public notification and inquiry.<BR>
	- Web site management.<BR>
	- Issues management.<BR>
	- Media monitoring.<BR>
	- Maintaining accurate (Communications) records.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>5.2 Levels of Communications Response<BR></P>");
		$pdf->QuestionsBody("<P align='left'>At the onset of the crisis, the Coordinator, in conjunction with the Spokesmen, will determine the potential level of public and media interest in the crisis. At this time, the Coordinator will determine what resources will be required to effectively
manage communication issues. The set up of the crisis communications team will depend on the scale of the crisis, and the
anticipated level of public concern and media interest. Only those resources that are needed to effectively respond to the
incident will be brought in. All key components of the crisis communications system will need to be implemented in a Level
III crisis, where public concern and media interest is extremely high. Additional Communications resources may be necessary
to properly manage a crisis.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Level I<BR></P>");
		$pdf->QuestionsBody("<P align='left'>An incident has occurred that does not pose a threat to public safety or the reputation of the community; its elected officials, administration or emergency response personnel.
	- Communications needs are on an internal basis.
	- There is little or no interest from the public or media.
	- The Communications/Marketing Coordinator can manage all internal and external information requests or notifications.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Level II<BR></P>");
		$pdf->QuestionsBody("<P align='left'>An incident has occurred that may potentially impact or pose a threat to public safety or the reputation of the community; its elected officials, administration or emergency response personnel.
<BR><BR>
	- There is a threat to public safety.<BR>
	- A serious injury or fatality has occurred.<BR>
	- There is a threat or minor disruption to the public or a sector of the public.<BR>
	- There is moderate interest or concern from the media, general public or other audiences.<BR>
	- External stakeholder audiences are involved and there is some local or political involvement.<BR>
	- There may be a question with regard to the community's liability.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Level III<BR></P>");
		$pdf->QuestionsBody("<P align='left'>An incident has resulted in multiple injuries or fatalities and has the potential to threaten the community or the reputation of elected officials, administration and emergency response personnel on many levels.
		<BR><BR>
	- There is a serious threat to public safety.<BR>
	- Multiple injuries or fatalities have occurred.<BR>
	- There is serious economical threat to the community.<BR>
	- There is high interest from the public, media and many other audiences.<BR>
	- All levels of political involvement are high.<BR>
	- The business’s performance or reputation may be in question.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>5.3 Level III Set Up<BR></P>");
		$pdf->QuestionsBody("<P align='left'>In a Level III crisis members of the crisis communications team may be required to staff the following key areas:
<BR><BR>
	- Emergency Operations Centre<BR>
	- Site<BR>
	- Media Centre<BR>
	- Call Centre<BR><BR>
Additional Communications resources may be necessary to properly manage a level III crisis<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Crisis Communications Coordinator Task List<BR><BR>6.1 Communications/Marketing Coordinator Start-up Checklist<BR><BR></P>");
						
mysql_select_db($database_conContinuty, $conContinuty);
$rsInteralForm = mysql_query("SELECT * FROM C2Damage WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
				
		$pdf->QuestionsBody("<P align='left'>Upon Notification<BR>
Receive briefing from Damage Assessment Coordinator ".$row_InteralForm['DAT_EMP01']." on the following:
<BR><BR>
	- Summary of incident.<BR>
	- Key messages.<BR>
	- Level of public and media interest anticipated.<BR>
	- Information the media may want.<BR>
	- Location of incident.<BR>
	- How you or the Site Information Officer(s) can gain access to the site(s).<BR>
	- Name of Incident Commander, if known.<BR>
	- How lines of communication will be maintained<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Discuss the following with the Spokesmen:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>- Level of crisis -- expected level of public and media interest.<BR>
	- Communications support required.<BR>
	- Communication strategies. Examples:<BR>
	- internal communications issues<BR>
	- spokesperson set up of the Call Centre/Media Centre<BR>
	- media relations support needed at the site<BR>
	- security issues around the public and media<BR>
	- approximate time and location of first media briefing key messages<BR>
	- Emergency Operations Centre<BR>
	- Site<BR>
	- Media Centre (if required)<BR>
	- Call Centre<BR>
	- Determine how long it will be before the key operational components of the crisis communications system are established.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Report to the Emergency Operations Centre.<BR></P>");
		
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Immediate WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
		
		$pdf->QuestionsBody("<P align='left'>Location: ".$row_InteralForm['IRT_EOC01']."<BR>
Address: ".$row_InteralForm['IRT_EOC02']."<BR>
Phone: ".$row_InteralForm['IRT_EOC03']."<BR>
Additional Information: ".$row_InteralForm['IRT_EOC04']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Media Response Record Template<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If you are contacted by a Media representative you will need to record the information they are interested in
obtaining. Please use the following as a reference point.<BR><BR></P>",185,13,37);
		$pdf->BoldText("<P align='left'>Date/Time:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Television Radio Newspaper Wire Service<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Update Requested<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Interview Requested<BR>
Pictures/Footage Requested<BR>
Request Completed</P>");
		$pdf->BoldText("<P align='left'>Request Referred To<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Spokesperson<BR>
Spokesperson Contact No.<BR>
Time of Interview<BR>
Location of Interview<BR>
Airtime/Publication Date (If Known)
<BR><BR>
Call taken by: ___________________ (Name of Information/Inquiry Officer)<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Focus of Interest : (Notes)
<BR><BR><BR>
		<P align='left'>Media Contact Person:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Fax :<BR>
Email:<BR>
Affiliation :<BR>
Reporter :<BR>
Telephone :</P>");
		$pdf->BoldText("<P align='left'>Sample Media Response<BR><BR>Sample Media Notice<BR><BR>Media Notice Template –<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Date of Release:<BR>
Time of Release:<BR>
News Release Number:<BR>
RE: Incident<BR>
EVENT: Media Briefing<BR>
WHEN: (date and time)<BR>
WHERE: Media Centre (location)<BR>
WHY: To update the media on the latest developments in the (name of incident).<BR>
WHO: Spokesperson(s} Available:<BR>
	-(list spokespersons)<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>PARKING: Media parking is available (name parking location). Access to the Media Centre will only be granted
through (name location) entrance. Once you have parked proceed to the (name of building) located (give directions) of the parking lot.
<BR><BR>Media Contacts:
<BR><BR>Newspaper)<BR>Name: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med02']."</P>");
		$pdf->BoldText("<P align='left'>Contact: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med03']." </P>");
		$pdf->BoldText("<P align='left'>Phone: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med04']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Radio)<BR>Name: </P>"); 
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med05']." </P>");
		$pdf->BoldText("<P align='left'>Contact: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med06']." </P>");
		$pdf->BoldText("<P align='left'>Phone: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med07']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(News Reporter)<BR>Name: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med08']." </P>");
		$pdf->BoldText("<P align='left'>Contact: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med09']." </P>");
		$pdf->BoldText("<P align='left'>Phone: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med10']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Television - Local)<BR>Name: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med11']." </P>");
		$pdf->BoldText("<P align='left'>Contact: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med12']." </P>");
		$pdf->BoldText("<P align='left'>Phone: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med13']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Television - Regional)<BR>Name: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med14']." </P>");
		$pdf->BoldText("<P align='left'>Contact: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med15']." </P>");
		$pdf->BoldText("<P align='left'>Phone: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['comm_med16']."</P>");
	}//end of if
	
	//Logistics 
	if($intTableIndex == 6)
	{
		$pdf->QuestionsBody("<P align='left'>In the event of a disaster you may require the services of various logistical and transportation companies. Your business most likely already uses some of these companies.
<BR><BR>Please List the Current Providers of the following Logistical Services.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Current Providers:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
Airline Company: ".$row_rsForm['LOG_01']." Phone: ".$row_rsForm['LOG_02']." Fax: ".$row_rsForm['LOG_03']."<BR>
Office Space Company: ".$row_rsForm['LOG_04']." Phone: ".$row_rsForm['LOG_05']." Fax: ".$row_rsForm['LOG_06']."<BR>
Hotel Company: ".$row_rsForm['LOG_07']." Phone: ".$row_rsForm['LOG_08']." Fax: ".$row_rsForm['LOG_09']."<BR>
Chartered Bus Company: ".$row_rsForm['LOG_10']." Phone: ".$row_rsForm['LOG_11']." Fax: ".$row_rsForm['LOG_12']."<BR>
Car Rental Company: ".$row_rsForm['LOG_13']." Phone: ".$row_rsForm['LOG_14']." Fax: ".$row_rsForm['LOG_15']."<BR>
Local Courier Company: ".$row_rsForm['LOG_16']." Phone: ".$row_rsForm['LOG_17']." Fax: ".$row_rsForm['LOG_18']."<BR>
Long Distance Courier Company: ".$row_rsForm['LOG_19']." Phone: ".$row_rsForm['LOG_20']." Fax: ".$row_rsForm['LOG_21']."<BR>
Local Transit Company: ".$row_rsForm['LOG_22']." Phone: ".$row_rsForm['LOG_23']." Fax: ".$row_rsForm['LOG_24']."<BR>
Moving Company: ".$row_rsForm['LOG_25']." Phone: ".$row_rsForm['LOG_26']." Fax: ".$row_rsForm['LOG_27']."<BR>
Postal Service Company: ".$row_rsForm['LOG_28']." Phone: ".$row_rsForm['LOG_29']." Fax: ".$row_rsForm['LOG_30']."<BR>
Telephone Systems Company: ".$row_rsForm['LOG_31']." Phone: ".$row_rsForm['LOG_32']." Fax: ".$row_rsForm['LOG_33']."<BR>
Travel Agent Company: ".$row_rsForm['LOG_34']." Phone: ".$row_rsForm['LOG_35']." Fax: ".$row_rsForm['LOG_36']."
<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Durning a disaster these companies may be unavailable. Please provide additional suppliers of these service that you could contact during an event to assist with any logistics<BR><BR></P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>
Alt. Airline Company: ".$row_rsForm['LOG_37']." Phone: ".$row_rsForm['LOG_38']." Fax: ".$row_rsForm['LOG_39']."<BR>
Alt. Office Space Company: ".$row_rsForm['LOG_40']." Phone: ".$row_rsForm['LOG_41']." Fax: ".$row_rsForm['LOG_42']."<BR>
Alt. Hotel Company: ".$row_rsForm['LOG_43']." Phone: ".$row_rsForm['LOG_44']." Fax: ".$row_rsForm['LOG_45']."<BR>
Alt. Chartered Bus Company: ".$row_rsForm['LOG_46']." Phone: ".$row_rsForm['LOG_47']." Fax: ".$row_rsForm['LOG_48']."<BR>
Alt. Car Rental Company: ".$row_rsForm['LOG_49']." Phone: ".$row_rsForm['LOG_50']." Fax: ".$row_rsForm['LOG_51']."<BR>
Alt. Local Courier Company: ".$row_rsForm['LOG_52']." Phone: ".$row_rsForm['LOG_53']." Fax: ".$row_rsForm['LOG_54']."<BR>
Alt. Long Distance Courier Company: ".$row_rsForm['LOG_55']." Phone: ".$row_rsForm['LOG_56']." Fax: ".$row_rsForm['LOG_57']."<BR>
Alt. Local Transit Company: ".$row_rsForm['LOG_58']." Phone: ".$row_rsForm['LOG_59']." Fax: ".$row_rsForm['LOG_60']."<BR>
Alt. Moving Company: ".$row_rsForm['LOG_61']." Phone: ".$row_rsForm['LOG_62']." Fax: ".$row_rsForm['LOG_63']."<BR>
Alt. Postal Service Company: ".$row_rsForm['LOG_64']." Phone: ".$row_rsForm['LOG_65']." Fax: ".$row_rsForm['LOG_66']."<BR>
Alt. Telephone Systems Company: ".$row_rsForm['LOG_67']." Phone: ".$row_rsForm['LOG_68']." Fax: ".$row_rsForm['LOG_69']."<BR>
Alt. Travel Agent Company: ".$row_rsForm['LOG_70']." Phone: ".$row_rsForm['LOG_71']." Fax: ".$row_rsForm['LOG_72']."
<BR><BR>
Company Description of Day-To Day Logistical Operations:
<BR><BR>".$row_rsForm['LOG_DESC01']."</P>");
	}//end of if
	
	//Alternate 
	if($intTableIndex == 7)
	{
		$pdf->BoldText("<P align='left'>In the event of a disaster you may require to access a temporary location or semi-permanent location to continue
in business. Whether it is a friendly competitor or your home, a location must be pre-determined, and all of the
tools and equipment necessary to resume business as quickly as possible.
<BR><BR>
In the event of a disaster and your business is not able to remain open, please identify a temporary location that
you could potentially use to remain in business.
<BR><BR>
In the Event of A Disaster, ".$strBusName." has determine that a Temporary Location that will be used for business operations will be:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>TEMPORARY ALTERNATE LOCATIONS:<BR></P>",6,145,52);
		$pdf->BoldText("<P align='left'>Temporary Location Name: ".$row_rsForm['ALT_TempLOG01']."<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Address: ".$row_rsForm['ALT_TempLOG02']."<BR>
Phone: ".$row_rsForm['ALT_TempLOG03']."<BR>
Alt. Phone: ".$row_rsForm['ALT_TempLOG04']."<BR><BR>
Site Agreement & Conditions: ".$row_rsForm['ALT_TempLOG05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If unavailable;<BR>Temporary Location (Back-Up) Name: ".$row_rsForm['ALT_Temp2LOG01']."<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Address: ".$row_rsForm['ALT_Temp2LOG02']."<BR>
Phone: ".$row_rsForm['ALT_Temp2LOG03']."<BR>
Alt. Phone: ".$row_rsForm['ALT_Temp2LOG04']."
<BR><BR>
Site Agreement & Conditions: ".$row_rsForm['ALT_Temp2LOG05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>SEMI-PERMANENT ALTERNATE LOGATIONS:<BR></P>",71,143,191);
		$pdf->QuestionsBody("<P align='left'>Semi-Permanent Location Name: ".$row_rsForm['ALT_SemiLOG01']."<BR>Address: ".$row_rsForm['ALT_SemiLOG02']."<BR>
Phone: ".$row_rsForm['ALT_SemiLOG03']."<BR>
Alt. Phone: ".$row_rsForm['ALT_SemiLOG04']."
<BR><BR>
Site Agreement & Conditions: ".$row_rsForm['ALT_SemiLOG05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If unavailable;<BR>Semi-Permanent Location (Back-Up) Name: ".$row_rsForm['ALT_Semi2LOG01']."<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Address: ".$row_rsForm['ALT_Semi2LOG02']."<BR>
Phone: ".$row_rsForm['ALT_Semi2LOG03']."<BR>
Alt. Phone: ".$row_rsForm['ALT_Semi2LOG04']."
<BR><BR>
Site Agreement & Conditions: ".$row_rsForm['ALT_Semi2LOG05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>PERMANENT ALTERNATE LOGATIONS:<BR></P>",185,13,37);
		$pdf->BoldText("<P align='left'>Permanent Location Name: ".$row_rsForm['ALT_PermLOG01']."<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Address: ".$row_rsForm['ALT_PermLOG02']."
Phone: ".$row_rsForm['ALT_PermLOG03']."<BR>
Alt. Phone: ".$row_rsForm['ALT_PermLOG04']."
<BR><BR>
Site Agreement & Conditions: ".$row_rsForm['ALT_PermLOG05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If unavailable;<BR>Permanent Location (Back-Up) Name: ".$row_rsForm['ALT_Perm2LOG01']."<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Address: ".$row_rsForm['ALT_Perm2LOG02']."
Phone: ".$row_rsForm['ALT_Perm2LOG03']."<BR>
Alt. Phone: ".$row_rsForm['ALT_Perm2LOG04']."
<BR><BR>
Site Agreement & Conditions: ".$row_rsForm['ALT_Perm2LOG05']."<BR><BR></P>");
		$pdf->BoldText("In the event of a disaster you may be unable to use your current supplies of products or services.
Should your current supplier be unable to provide you with service you will need to be able to access additional
resources.<BR>Alternate suppliers of your products and services:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>ALTERNATE SUPPLIERS OF YOUR PRODUCTS<BR><BR></P>",6,145,52);
		$pdf->BoldText("<P align='left'>Product 1<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Name of Product: ".$row_rsForm['ALT_SUPPD01']."<BR>
Name of Supplier: ".$row_rsForm['ALT_SUPPD02']."<BR>
Address: ".$row_rsForm['ALT_SUPPD03']."<BR>
Phone: ".$row_rsForm['ALT_SUPPD04']."<BR>
Alt. Phone: ".$row_rsForm['ALT_SUPPD05']."<BR>
Agreement & Conditions: ".$row_rsForm['ALT_SUPPD06']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Product 2<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Name of Product: ".$row_rsForm['ALT_SUPPD07']."<BR>
Name of Supplier: ".$row_rsForm['ALT_SUPPD08']."<BR>
Address: ".$row_rsForm['ALT_SUPPD09']."<BR>
Phone: ".$row_rsForm['ALT_SUPPD10']."<BR>
Alt. Phone: ".$row_rsForm['ALT_SUPPD11']."<BR>
Agreement & Conditions: ".$row_rsForm['ALT_SUPPD12']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Product 3<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Name of Product: ".$row_rsForm['ALT_SUPPD13']."<BR>
Name of Supplier: ".$row_rsForm['ALT_SUPPD14']."<BR>
Address: ".$row_rsForm['ALT_SUPPD15']."<BR>
Phone: ".$row_rsForm['ALT_SUPPD16']."<BR>
Alt. Phone: ".$row_rsForm['ALT_SUPPD17']."<BR>
Agreement & Conditions: ".$row_rsForm['ALT_SUPPD18']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Product 4<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Name of Product: ".$row_rsForm['ALT_SUPPD19']."<BR>
Name of Supplier: ".$row_rsForm['ALT_SUPPD20']."<BR>
Address: ".$row_rsForm['ALT_SUPPD21']."<BR>
Phone: ".$row_rsForm['ALT_SUPPD22']."<BR>
Alt. Phone: ".$row_rsForm['ALT_SUPPD23']."<BR>
Agreement & Conditions: ".$row_rsForm['ALT_SUPPD24']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Product 5<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Name of Product: ".$row_rsForm['ALT_SUPPD25']."<BR>
Name of Supplier: ".$row_rsForm['ALT_SUPPD26']."<BR>
Address: ".$row_rsForm['ALT_SUPPD27']."<BR>
Phone: ".$row_rsForm['ALT_SUPPD28']."<BR>
Alt. Phone: ".$row_rsForm['ALT_SUPPD29']."<BR>
Agreement & Conditions: ".$row_rsForm['ALT_SUPPD30']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>ALTERNATE SUPPLIERS OF YOUR SERVICES<BR><BR></P>",6,145,52);
		$pdf->BoldText("<P align='left'>Service 1<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Name of Service: ".$row_rsForm['ALT_SUPSR01']."<BR>
Name of Supplier: ".$row_rsForm['ALT_SUPSR02']."<BR>
Address: ".$row_rsForm['ALT_SUPSR03']."<BR>
Phone: ".$row_rsForm['ALT_SUPSR04']."<BR>
Alt. Phone: ".$row_rsForm['ALT_SUPSR05']."<BR>
Agreement & Conditions: ".$row_rsForm['ALT_SUPSR06']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Service 2<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Name of Service: ".$row_rsForm['ALT_SUPSR07']."<BR>
Name of Supplier: ".$row_rsForm['ALT_SUPSR08']."<BR>
Address: ".$row_rsForm['ALT_SUPSR09']."<BR>
Phone: ".$row_rsForm['ALT_SUPSR10']."<BR>
Alt. Phone: ".$row_rsForm['ALT_SUPSR11']."<BR>
Agreement & Conditions: ".$row_rsForm['ALT_SUPSR12']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Service 3<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Name of Service: ".$row_rsForm['ALT_SUPSR13']."<BR>
Name of Supplier: ".$row_rsForm['ALT_SUPSR14']."<BR>
Address: ".$row_rsForm['ALT_SUPSR15']."<BR>
Phone: ".$row_rsForm['ALT_SUPSR16']."<BR>
Alt. Phone: ".$row_rsForm['ALT_SUPSR17']."<BR>
Agreement & Conditions: ".$row_rsForm['ALT_SUPSR18']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Service 4<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Name of Service: ".$row_rsForm['ALT_SUPSR19']."<BR>
Name of Supplier: ".$row_rsForm['ALT_SUPSR20']."<BR>
Address: ".$row_rsForm['ALT_SUPSR21']."<BR>
Phone: ".$row_rsForm['ALT_SUPSR22']."<BR>
Alt. Phone: ".$row_rsForm['ALT_SUPSR23']."<BR>
Agreement & Conditions: ".$row_rsForm['ALT_SUPSR01']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>In the event you need to relocate to an alternate site you will need to ensure you have the following items so you can continue to function efficiently and effectively.
<BR><BR>
Alternate Location Item Checklist:<BR>
The objectives for acquiring an alternate facility include:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>	1. Identify a facility from which to continue to perform critical functions/operations
	2. Reduce or mitigate disruption to operations
	3. Achieve a timely and orderly recovery and resumption of full service to customers<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The Acquisition Process<BR></P>");
		$pdf->QuestionsBody("<P align='left'>The term alternate facility can include anything from a borrowed conference room for a few key people on a temporary basis, to a complete turn-key facility to house the entire organization. The size and scope of the alternate facility is dependent upon the individual organization, departments and their identified critical functions. Facility requirements, selection and occupancy planning should include provisions for a worst case scenario.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The alternate facility acquisition process should consist, at a minimum, of four (4) steps:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>	1. Alternate Facilities Requirements Identification<BR>
	2. Candidate Alternate Facilities Selection<BR>
	3. Alternate Facility Acquisition<BR>
	4. Alternate Facility Reevaluation.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Checklist 1:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Can the Critical Operations and Functions be performed at the Alternate Facility under consideration?
<BR><BR>
Did you select an area where the ability to initiate, maintain, and terminate operations will not be disrupted?
<BR><BR>
Did you consider using existing field infrastructures, Telecommuting Centers, Virtualenvironment or joint or shared space?
<BR><BR>
Have you thought about who needs to work at the facility, who can work from home and who should be on standby?
<BR><BR>
What is your immediate capability to perform critical functions under various threat conditions (e.g. threats involving weapons of mass destruction)?
<BR><BR>
Does the facility have the ability to be operational within 12 hours after activation?
<BR><BR>
Can you sustain operations for 30 days or longer?<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Checklist 2:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Are the Facility Requirements and Risks associated with the Alternate Facility within acceptable limits (risk assessment, infrastructure, personnel related, all Phases)?
<BR><BR>
Did you perform a risk analysis of current alternate facility?
<BR><BR>
Did you consider all possible scenarios for BCP relocation (e.g. fire flooding potential threats of terrorism?
<BR><BR>
Did you consider the distance from the threat area of any other facilities/locations such as hazardous materials/area subject to natural disasters or civil unrest?
<BR><BR>
How many associates per shift will be required to accomplish these functions for 30 days or until the event is terminated?
<BR><BR>
Do you have sufficient space for your Phase I personnel and Phase II personnel?
<BR><BR>
Do you have reliable logistical support, services and infrastructure system;include water, electric power, heating and air conditioning etc?
<BR><BR>
Do you have access to essential resources such as food, water, fuel, and medical facilities?
<BR><BR>
If the alternate facility is located at a distance from the primary site, did you develop plans to address housing for emergency staff (billeting within facility or local motels)?
<BR><BR>
How will you handle housekeeping requirements including supplies?
<BR><BR>
Have you thought about your transportation requirements?
<BR><BR>
Do you need vehicles at the facility?
<BR><BR>
What about the availability of rental vehicles? Have you identified a source?
<BR><BR>
What about the availability of surface and/or air transportation?
<BR><BR>
What mode of transportation will your associates use?
<BR><BR>
Does cellular phone coverage limit the facility from consideration?
<BR><BR>
What are the equipment and furniture requirements for the facility?
<BR><BR>
Have you determine the power requirements for the facility?
<BR><BR>
Have you identified backup power to the facility?
<BR><BR>
Have you identified your communications requirement?
<BR><BR>
Is the alternate facility outside the communications and data grid of the primary facility?
<BR><BR>
Do you have sufficient telecommunication line and data lines?
<BR><BR>
Do you need a secure phone or fax machine?
<BR><BR>
Do you have a requirement for secure storage containers?
<BR><BR>
What type of computers and software do you need?
<BR><BR>
Do you need security personnel to provide perimeter access, and internal security functions?
<BR><BR>
Has your organization selected and acquired an alternate facility?
<BR><BR>
Have you identified a facility?
<BR><BR>
Did you consider using existing organization space (e.g. remote/offsite training facility; regional or field office;
<BR><BR>
remote headquarters operations) Did you consider Virtual Offices such as Work at Home, Telecommuting facilities, and Mobile office concept?
Do you have the authority to procure your own space?
<BR><BR>
Did you include the alternate facility reevaluation as a part of the annual BCP review and update process (Plan Maintenance)?
<BR><BR>
Does your facility still meet the needs as determined by the organization’s plan?<BR><BR></P>");
		$pdf->BoldText("<P align='left'>In the event of an emergency or threat, ".$strBusName." will re-deploy their critical staff to work remotely from home.<BR>
The Call List and Essential personnel are defined in the Emergency Contacts.<BR>
The staff will continue the operation of their critical business functions until the main facility is capable of supporting operations in a threat-free environment.
<BR><BR>".$strBusName." will address services which may include:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>	1. Delivery of mail by the US Postal Service<BR>
	2. Local and national courier services<BR>
	3. Lodging and food for associates relocated to the alternate facility and<BR>
	4. Transportation to the alternate facility and between the alternate facility, if not the employee’s residence, and location of lodging, food and other necessities.</P>");
	}//end of if
	
	//Salvage 
	if($intTableIndex == 8)
	{
		$pdf->QuestionsBody("<P align='left'>In the event of a disaster you will still need to have security at your new and old locations. Please ensure that the following areas are covered.
		<BR><BR>
Do you have Heat requirements?	YES		NO (IF YES)<BR>
What are the specifics?
		<BR><BR><BR>
Do you have humidity control requirements?		YES		NO (IF YES)<BR>
What are the specifics?
		<BR><BR><BR>
Do you require 24 hr. Security?		YES		NO (IF YES)<BR>
What are the specifics?
		<BR><BR><BR>
Do you have have lighting requirements?		YES		NO (IF YES)<BR>
What are the specifics?
		<BR><BR><BR>
Are guard dogs or security personnel required?		YES		NO (IF YES)<BR>
What are the specifics?
		<BR><BR><BR>
Does access need to be restricted?		YES		NO (IF YES)<BR>
What are the specifics?
		<BR><BR><BR>
You may require additional Security at your current, temporary or new locations. Below are some Security contacts that can
provide you with those essential services.
		<BR><BR>
Security Company 1: ".$row_rsForm['SAL_CPN22']."<BR>
Phone: ".$row_rsForm['SAL_CPN23']."<BR>
Contact: ".$row_rsForm['SAL_CPN24']."<BR>
Description of Service: ".$row_rsForm['SAL_CPN25']."
		<BR><BR>
Security Company 2: ".$row_rsForm['SAL_CPN26']."<BR>
Phone: ".$row_rsForm['SAL_CPN27']."<BR>
Contact: ".$row_rsForm['SAL_CPN28']."<BR>
Description of Service: ".$row_rsForm['SAL_CPN29']."</P>");
	}//end of if
	
	//Customer 
	if($intTableIndex == 9)
	{
		$pdf->BoldText("<P align='left'>In every business the most valuable asset is it’s customers. In the event of a disaster you may require to act quickly to restore your various products and service so you can continue to provide your customers with service. In the Event of A Disaster, (intro_01) has determine that a coordinator within their company will help ensure that customer service is restored as quickly as possible.
<BR><BR>
This person will be:
<BR><BR>
Customer Service Coordinator: ".$row_rsForm['cust_coor01']." <BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Job Title: ".$row_rsForm['cust_coor02']."<BR>
Phone: ".$row_rsForm['cust_coor03']."<BR>
Cell: ".$row_rsForm['cust_coor04']."<BR>
E-Mail: ".$row_rsForm['cust_coor05']."
<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The following are the products and service that are required in the event of a disaster:
<BR><BR>
Required Services Performed:
<BR><BR>
Service 1: ".$row_rsForm['cust_ser01']." Purpose: ".$row_rsForm['cust_ser02']."
<BR><BR>
Required Personnel: ".$row_rsForm['cust_ser03']."
<BR><BR>
Service 2: ".$row_rsForm['cust_ser04']." Purpose: ".$row_rsForm['cust_ser05']."
<BR><BR>
Required Personnel: ".$row_rsForm['cust_ser06']."
<BR><BR>
Service 3: ".$row_rsForm['cust_ser07']." Purpose: ".$row_rsForm['cust_ser08']."
<BR><BR>
Required Personnel: ".$row_rsForm['cust_ser09']."
<BR><BR>
Service 4: ".$row_rsForm['cust_ser10']." Purpose: ".$row_rsForm['cust_ser11']."
<BR><BR>
Required Personnel: ".$row_rsForm['cust_ser12']."
<BR><BR>
Service 5: ".$row_rsForm['cust_ser13']." Purpose: ".$row_rsForm['cust_ser14']."
<BR><BR>
Required Personnel: ".$row_rsForm['cust_ser15']."
<BR><BR>
Service 6: ".$row_rsForm['cust_ser16']." Purpose: ".$row_rsForm['cust_ser17']."
<BR><BR>
Required Personnel: ".$row_rsForm['cust_ser18']."
<BR><BR>
Service 7: ".$row_rsForm['cust_ser19']." Purpose: ".$row_rsForm['cust_ser20']."
<BR><BR>
Required Personnel: ".$row_rsForm['cust_ser21']."
<BR><BR>
Service 8: ".$row_rsForm['cust_ser22']." Purpose: ".$row_rsForm['cust_ser23']."
<BR><BR>
Required Personnel: ".$row_rsForm['cust_ser24']."
<BR><BR>
Service 9: ".$row_rsForm['cust_ser25']." Purpose: ".$row_rsForm['cust_ser26']."
<BR><BR>
Required Personnel: ".$row_rsForm['cust_ser27']."
<BR><BR>
The following are the products and service that are required in the event of a disaster:
<BR><BR>
Required Products Performed:<BR>
Product 1: ".$row_rsForm['cust_Pro01']." Purpose: ".$row_rsForm['cust_Pro02']."<BR>
Required Personnel: ".$row_rsForm['cust_Pro03']."<BR>
Product 2: ".$row_rsForm['cust_Pro04']." Purpose: ".$row_rsForm['cust_Pro05']."<BR>
Required Personnel: ".$row_rsForm['cust_Pro06']."<BR>
Product 3: ".$row_rsForm['cust_Pro07']." Purpose: ".$row_rsForm['cust_Pro08']."<BR>
Required Personnel: ".$row_rsForm['cust_Pro09']."<BR>
Product 4: ".$row_rsForm['cust_Pro10']." Purpose:".$row_rsForm['cust_Pro11']."<BR>
Required Personnel: ".$row_rsForm['cust_Pro12']."<BR>
Product 5: ".$row_rsForm['cust_Pro13']." Purpose: ".$row_rsForm['cust_Pro14']."<BR>
Required Personnel: ".$row_rsForm['cust_Pro15']."
<BR><BR>
You will need to let your customers know that a disaster has occurred to your business and you are doing everything in your power to contain and control the situation. Work with the Crisis Communications team to establish a Statement for your Customers in ensure them that you will be able to provide Products and Service to them as soon as possible.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>You may need to use some competitors or partners in your industry to help you provide certain products or service.<BR>
Below you will find a list of potential Alliances that will assist you in the time off need.
<BR><BR>
Alliance Partner 1: ".$row_rsForm['cust_all01']." Contact: ".$row_rsForm['cust_all02']."<BR>
Address: ".$row_rsForm['cust_all03']." Phone: ".$row_rsForm['cust_all04']."<BR>
E-Mail: ".$row_rsForm['cust_all05']."
<BR><BR>
Product or Service they Provide: ".$row_rsForm['cust_all06']."<BR>
Terms of Agreement: ".$row_rsForm['cust_all07']."
<BR><BR>
Alliance Partner 2: ".$row_rsForm['cust_all08']." Contact: ".$row_rsForm['cust_all09']."<BR>
Address: ".$row_rsForm['cust_all10']." Phone: ".$row_rsForm['cust_all11']."<BR>
E-Mail: ".$row_rsForm['cust_all12']."
<BR><BR>
Product or Service they Provide: ".$row_rsForm['cust_all13']."<BR>
Terms of Agreement: ".$row_rsForm['cust_all14']."<BR>
<BR><BR></P>");
		
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>The following tasks will be required to be completed by the Customer Service Coordinator:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInGreen.gif',15,33);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	Establish alternate sources for all lost Products or Services<BR></P>");
		$pdf->Image('../images/PDFArrowInGreen.gif',15,38);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	Notify the Disaster Management Team of all alternate methods being taken<BR></P>");
		$pdf->Image('../images/PDFArrowInGreen.gif',15,43);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	Notify Disaster Management Team of Staff requirements to complete all required tasks<BR></P>");
		$pdf->Image('../images/PDFArrowInGreen.gif',15,48);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	Work with Crisis Communications Team to coordinate with the notification process<BR></P>");
		$pdf->Image('../images/PDFArrowInGreen.gif',15,53);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	Work with alliance companies to ensure alternate methods or products and services are available<BR></P>");
		$pdf->Image('../images/PDFArrowInGreen.gif',15,63);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	Once complete ensure all products and service as restored back to normal.<BR></P>");
		$pdf->Image('../images/PDFArrowInGreen.gif',15,68);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	Report all findings and results back to the Disaster Management Team.<BR></P>");
		
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->QuestionsBody("<P align='left'>The following Can be Used as a template for communication to your customers following a disaster:
<BR><BR><BR>
To Our Customers;
<BR><BR>
We have recently experienced a brief interruption to our business due to a disaster. We are now in the process of executing out Business Continuity Plan to ensure that we are able to continue to provide products & Service to you as quickly as possible.
<BR><BR>
We apologize for this brief delay in our services.
<BR><BR>
We would like to inform you that we have establish lines of communications with Alternate suppliers of our products and services so will still have access to the products you may require. Please contact us at your convenience to find out how we can continue to offer our business to you.
<BR><BR>
We will be up and running again in a few short days and would like to remind you that we are still available for communications or to answer any questions you may have.
<BR><BR>
We thank you for your business and look forward to continuing to service you.
<BR><BR><BR>
Sincerely
<BR><BR><BR>
Management</P>");
	}//end of if
	
	//Environment 
	if($intTableIndex == 10)
	{
		$pdf->BoldText("<P align='left'>Environmental Concerns:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The Ministry of the Environment</P>"); 
		$pdf->QuestionsBody("<P align='left'>wants to be sure that small and medium size business owners understand Ontario’s environmental regulations and assess whether your business is operating within them. These regulations direct businesses to perform a variety of functions, such as obtaining environmental permits (Certificates of Approval), reporting spills, registering hazardous wastes and co-operating with Environmental Officers performing inspections. Knowing the environmental requirements and regularly reviewing your operation to confirm that you are complying with them will make your company a responsible player in protecting our air, land and water resources.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Approvals, Permits and Licenses:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
- Do you have an industrial air emission point that may require a Certificate of Approval?<BR>
- Have approvals been obtained for all waste water discharges into drains, sanitary or storm sewers, streams or other
water bodies?<BR>
- If you already have a Certificate of Approval, is it up to date, do you have a current copy and are you operating
according to its requirements?<BR>
- Are there any plans that could increase discharges and if so has an application been made to amend your Certificate
of Approval?<BR><
- Have you obtained the appropriate license for the use or sale of pesticides?<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Operational Management:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
- Do you conduct regular maintenance on your pollution control equipment?<BR>
- Has adequate spill containment for hazardous materials/wastes been provided?<BR>
- Do you ensure that industrial waste waters are not discharged into on-site septic systems without a Certificate of Approval?
- Are employees provided with necessary environmental training?<BR>
- Is waste properly stored?<BR>
- Is your hazardous and liquid industrial waste disposed of through an MOE licensed carrier?<BR>
- Do you ensure that waste is not stockpiled or thrown into an unapproved on-site disposal area?<BR>
- Is any analytical testing of environmental samples performed by a laboratory with the proper license and/or accreditation?<BR>
- If you serve drinking water to the public and you are not connected to the municipal system, do you follow all regulatory requirements?<BR>
- Are all spills or discharges that may impact the environment or human health reported immediately to the proper authorities?<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Documentation:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
- Are transportation manifests completed for the disposal of hazardous or liquid wastes as required by Ontario Regulation 347?<BR>
- Are all hazardous and liquid wastes properly evaluated, labeled and registered with the MOE?<BR>
- Have 90-day hazardous waste storage reports been submitted when necessary?<BR>
- Are all records required by MOE accurate and up to date and are you keeping copies?<BR>
- Does the company have a written contingency plan to respond to environmental emergencies?<BR>
- Is the MOE Spills Action Centre number easily accessible, in the event of an environmental emergency?<BR>
- If you answered any question with a “no” or if you are unsure of how to answer a specific question, this likely means that your company needs to take additional steps to achieve compliance.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Environmental Assistance:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
Environment agencies developed this fact sheet to assist small businesses in reviewing and improving compliance with environmental regulations. It does not include a comprehensive listing of all provincial environmental regulations that are applicable to all small businesses, nor does it deal with municipal or federal requirements. Further research beyond this guide, by your business, may be necessary. The checklist is not a guarantee that a small business meets all applicable regulations. It is a tool to be used as a first step in evaluating compliance.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>For further information on:<BR>(Canada)<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Environmental regulations and your business refer to the ‘Business and the Environment’ link on the </P>");
		$pdf->BoldText("<P align='left'>Ministry of the Environment website at :</P>");
		$pdf->QuestionsBody("<P align='left'><BR><BR>Canada: www.ene.gov.on.ca or call the Public Information Centre at 1-800-565-4923<BR><BR>(United States of America)<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Environmental regulations and your business refer to the </P>");
		$pdf->BoldText("<P align='left'>Enviromental Protection Agency Website:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>United States: www.epa.gov or call the Public Information Centre at 1-800-424-8802</P>");
		$pdf->QuestionsBody("<P align='left'><BR><BR>Enviromental Contacts:<BR><BR></P>");
		$pdf->BoldText("Enviromental Provinical Police (Canada):<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Phone: ".$row_rsForm['ENV_CPN01']." Alt. Phone: ".$row_rsForm['ENV_CPN02']." Contact: ".$row_rsForm['ENV_CPN03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Enviromental State Police (US):<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Phone: ".$row_rsForm['ENV_CPNus01']." Alt. Phone: ".$row_rsForm['ENV_CPNus02']." Contact: ".$row_rsForm['ENV_CPNus03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Enviromental Municipal Police (Canada):<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Phone: ".$row_rsForm['ENV_CPN04']." Alt. Phone: ".$row_rsForm['ENV_CPN05']." Contact: ".$row_rsForm['ENV_CPN06']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Enviromental Fire Department Spill Response Team:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Phone: ".$row_rsForm['ENV_CPN07']." Alt. Phone: ".$row_rsForm['ENV_CPN08']." Contact: ".$row_rsForm['ENV_CPN09']."<BR><BR></P>");	
		$pdf->BoldText("<P align='left'>Electronics Recycling Depot:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Phone: ".$row_rsForm['ENV_CPN10']." Alt. Phone: ".$row_rsForm['ENV_CPN11']." Contact: ".$row_rsForm['ENV_CPN12']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Environmental Spill Response Plans (Generic):<BR><BR>FOR SMALL SPILLS (less than 10 liters)<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
1. Make sure area is safe for entry and the spill does not pose an immediate threat to health or safety of responder<BR>
2. Stop source of spill (plug hole, upright the container, shut off valve)<BR>
3. Check for hazards (flammable material, noxious fumes, cause of spill). If flammable liquid is spilled, turn off engines and nearby electrical equipment. If serious hazards are present leave the area and call 911. When in doubt, consult the applicable Material Safety Data Sheets for hazards.<BR>
4. Stop spill from entering drain (use absorbent or other material as necessary, close valve to drain, cover or plug drain)<BR>
5. If spilled material has entered a storm sewer, then check oil/water interceptor or catch basins then notify the municipality:<BR>
6. If spilled material has entered the sanitary sewer then after checking oil/water interceptor or catch basins contact Regional Source Control<BR>
Program, Phone: . Section 7 of the CRD Sewer Use Bylaw contains notification procedures for all spills to the sanitary sewer.<BR>
7. Clean up spilled material/absorbent (do not flush area with water)<BR>
8. Dispose of cleaned material/absorbent into secure container for disposal as hazardous waste<BR>
9. Make sure cleaned area is not slippery (if slippery, put down no-slip material or mark area with a “slippery when wet” sign)<BR>
10. Notify Supervisor<BR>
11. Complete a Spill Reporting Sheet<BR><BR></P>");
		$pdf->BoldText("<P align='left'>MEDIUM SPILLS (10-100 liters)<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
1. Make sure area is safe for entry and the spill does not pose an immediate threat to health or safety of responder<BR>
2. Stop source of spill (Plug hole, upright the container, shut off valve)<BR>
3. Check for hazards (flammable material, noxious fumes, cause of spill) - If flammable liquid, turn off engines and nearby electrical equipment.<BR>
If serious hazards are present leave area and call 911. When in doubt, consult the Material Safety Data Sheet for hazards.<BR>
4. Call co-workers and supervisor for assistance and to make them aware of the spill and potential dangers<BR>
5. Stop spill from entering drain (use absorbent or other material as necessary, close valve to drain, cover or plug drain)<BR>
6. Stop spill from spreading (use absorbent or other material)<BR>
7. If spilled material has entered a storm sewer, then check oil/water interceptor or catch basins then notify the municipality.<BR>
8. If spilled material has entered the sanitary sewer then after checking oil/water interceptor or catch basins contact Regional Source Control<BR>
Program, Phone: Section 7 of the CRD Sewer Use Bylaw contains notification procedures for all spills to the sanitary sewer.<BR>
9. Clean up spilled material/absorbent (do not flush area with water) - If outside clean-up service is<BR>
10. Dispose of cleaned material/absorbent into secure container for disposal as hazardous waste<BR>
11. Make sure cleaned area is not slippery (if slippery, put down no-slip material or mark area with a “slippery when wet” sign)<BR>
12. Complete Spill Reporting Sheet<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Environmental Spill Response Plans (Generic):<BR><BR>LARGE SPILLS (Greater than 100 liters)<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
1. Make sure area is safe for entry and the spill does not pose an immediate threat to health or safety of responder<BR>
2. Stop source of spill (Plug hole, upright the container, shut off valve)<BR>
3. Check for hazards (flammable material, noxious fumes, cause of spill) - If flammable liquid, turn off engines and nearby electrical equipment.<BR>
If serious hazards are present leave area and call 911. LARGE SPILLS ARE LIKELY TO PRESENT A HAZARD.<BR>
4. Call co-workers and supervisor for assistance and to make them aware of the spill and potential dangers.<BR>
5. If possible, stop spill from entering drain (use absorbent or other material as necessary, close valve to drain, cover or plug drain)<BR>
6. Stop spill from spreading (use absorbent or other material)<BR>
7. Call the appropriate Emergency Program<BR>
8. If spilled material has entered a storm sewer , then check oil/water interceptor or catch basins then notify the municipality.<BR>
9. If spilled material has entered the sanitary sewer then after checking oil/water interceptor or catch basins contact Regional Source Control<BR>
Program, Phone: . Section 7 of the CRD Sewer Use Bylaw contains notification procedures for all spills to the sanitary sewer.<BR>
10. Clean up spilled material/absorbent (do not flush area with water) - If outside clean-up service is<BR>
11. Dispose of cleaned material/absorbent into secure container for disposal as hazardous waste<BR>
12. Make sure cleaned area is not slippery (if slippery, put down no-slip material or mark area with a “slippery when wet” sign)<BR>
13. Complete a Spill Reporting Sheet
<BR><BR>
Privacy Concerns:
<BR><BR>
Regardless of which phase an organization starts with, it must go through them all. If a disaster occurs before a plan is developed,
the institution must enter mid-cycle and start with whatever rudimentary recovery is necessary. It may even catapult
directly to interim management.
<BR><BR>
The privacy rule, normally more prescriptive than the security rule, implies rather than mandates the need for a decisionmaking
process that provides guidance regarding when to follow existing procedures, under what circumstances to operate
in emergency mode, and how that decision will be communicated. Both the decision-making process and the emergency
mode functions should be developed in advance of a disaster, when risks and benefits can be thoroughly and carefully explored.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The plan should lay out and standardize emergency mode functions such as:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
		A communication plan for informing staff the scope of the outage, the extent of resources disabled, 
		and the extent of recovery and restoration 	as it occurs Minimal documentation requirements Emergency registration sets that can double as a 
		source of patient identification mid-crisis and, ultimately, a means of filing the patient’s information An emergency paper chart that enables 
		and expedites the standards agreed upon Downtime procedures for paper documentation Stickers for allergies and other emergency flags
		Standardized filing procedures based on a predetermined manual numbering system that can be accessed at a later date to retrieve emergency mode
		documentation
<BR><BR>
The plan should account for the security of the premises, since the greater the disaster, the less likely that the perimeter of the premises will be secured and the greater the risk that your business will be accessed inappropriately. Plans for physical security of your business should be developed in advance for various degrees of disaster, including designating or preparing metal cabinets, preparing schemas for maintaining staffing rosters, and creating temporary ID badges for volunteers and other first responders who will need access.
<BR><BR>
The privacy rule permits use or disclosure of employee information for treatment, payment, and operations without patient authorization. Even where a stricter state law pre-empts HIPAA and requires authorization prior to disclosing employee information for treatment, any information necessary for treatment may be shared in an emergency without authorization. If a patient has a personal representative, information may be shared with that individual as if he or she were the patient. In the absence of a personal representative the minimal amount of information necessary may be shared with an individual caring for a patient to the extent that it is necessary to provide such care.
<BR><BR>
Disclosure of information may be made to any individual directly involved in assisting the patient in making payments or resolving a payment issue, including a relative, a friend, or even a public official, provided that there is indication that the patient has requested the individual to intercede or it is in the patient’s best interest to do so.
<BR><BR>
Any other questions or concerns about privacy can be answered by your local authority at:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Canada: www.canada.gc.ca/home.html or CALL 1 800 O-Canada (1 800 622-6232)<BR><BR>United States: www.business.gov/guides/privacy or CALL 1-800-U-ASK-SBA (1-800-827-5722)</P>");
	}//end of if

	//Disaster Declaration
	if($intTableIndex == 11)
	{			
		$pdf->BoldText("<P align='left'>After an event has occurred, it is the responsibility of the organization to determine the actual result of this event. Some events are more significant then others, and as a result will determine the action plan of the organization. The following Disaster Declaration Phase has been establish to help organizations determine what actions should be taken if this event happens at their location.<BR><BR>Please use the following reference as a definition of your event and what recommended actions should be taken by your organization.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phase 1 Disaster Declaration: <BR><BR></P>",6,145,52);
		$pdf->QuestionsBody("<P align='left'>An event has occurred at your place of business or in the same area as your business. There is minimal damage to your business location. Please Complete the following Checklist to determine if this Phase of the Disaster Declaration is the definition of your event.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Checklist:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,108.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	1. The event has occurred at your location, but will not directly impact you? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,113.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	2. There is minimal damage to your business, equipment and location? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,118.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	3. The event can be mitigated by your employees with no outside help? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,123.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	4. Can you resume business as usual within less then 1 full work day (8 hrs.)? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,128.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	5. You will require no additional outside help to ensure this event is over? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,133.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	6. The damaged equipment can be easily replaced? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,138.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	7. Your Insurance policy should cover all expenses due to this event? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,143.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	8. You do not require the activation of your Disaster Recovery Plans? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,148.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	9. Your current business location is still useable and accessible? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,153.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	10. The event will be ended and business will resume with no down time? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If you answered YES to over 50% of the questions above then your business will classify this event as a Phase 1
level event. Based on this decision you will begin the perform the following actions and define this event as follows.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phase 1 Definition:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Your business has experienced a Business Disruption with minimal or no damage to your business, equipment and operations. Your business will be able to mitigate and control the situation with no assistance or requirement of your disaster recovery plans.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phase 1 Action Plan:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,218.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	1. Mitigate and Control the disaster<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,223.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	2. Assess the damage<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,228.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	3. Replace any damaged or lost items<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,233.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	4. Report incident to Insurance Broker and determine if claim is worth the deductible<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,238.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	5. Resume Business As Usual as soon as possible.<BR></P>");
		
		//updates the Sub Section	
		$strSectionName = "Phase 2 - Level 1 Disaster";

		//updates the title and the Sub Section	
		$strAreaName = "Disaster Declaration Phase 2";

		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>After an event has occurred, it is the responsibility of the organization to determine the actual result of this event. Some events are more significant then others, and as a result will determine the action plan of the organization. The following Disaster Declaration Phase has been establish to help organizations determine what actions should be taken if this event happens at their location.<BR><BR>Please use the following reference as a definition of your event and what recommended actions should be taken by your organization.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phase 2 Disaster Declaration:<BR><BR></P>",224,216,21);
		$pdf->QuestionsBody("<P align='left'>An event has occurred at your place of business or in the same area as your business. There is minimal damage to your business location. Please Complete the following Checklist to determine if this Phase of the Disaster Declaration is the definition of your event.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Checklist:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,108.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	1. The event has occurred at your location, and has directly impacted your business? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,113.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	2. There damage to your business, equipment and location? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,118.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	3. The event can be mitigated by your employees and your Recovery Plans? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,123.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	4. Can you resume business as usual within less then 1 full work day (8 hrs.)? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,128.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	5. You may require additional outside help to ensure this event is mitigated? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,133.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	6. The damaged equipment can be easily replaced? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,138.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	7. Your Insurance policy should cover all expenses due to this event? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,143.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	8. You require the activation of your Disaster Recovery Plans? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,148.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	9. Your current business location is still useable and accessible? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,153.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	10. The event will be ended and business will resume with minimal down time? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If you answered YES to over 50% of the questions above then your business will classify this event as a Phase 1 level event. Based on this decision you will begin the perform the following actions and define this event as follows.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phase 2 Definition:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Your business has experienced a Minor Disaster with some damage to your business, equipment and operations. Your business will be able to mitigate and control the situation with the activation of your disaster recovery plans, and the action plan you have developed.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phase 1 Action Plan:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,218.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	1. Activate Your Immediate Response Plans<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,223.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	2. Complete the Disaster Recovery Plans<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,228.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	3. Replace any damaged or lost items<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,233.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	4. Report incident to Insurance Broker and determine if claim is worth the deductible<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,238.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	5. Resume Business As Usual as soon as possible.<BR></P>");

		//updates the Sub Section		
		$strSectionName = "Phase 3 - Level 2 Disaster";

		//updates the title and the Sub Section	
		$strAreaName = "Disaster Declaration Phase 3";

		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>After an event has occurred, it is the responsibility of the organization to determine the actual result of this event. Some events are more significant then others, and as a result will determine the action plan of the organization. The following Disaster Declaration Phase has been establish to help organizations determine what actions should be taken if this event happens at their location.<BR><BR>Please use the following reference as a definition of your event and what recommended actions should be taken by your organization.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phase 3 Disaster Declaration:<BR><BR></P>",225,80,22);
$pdf->QuestionsBody("<P align='left'>An event has occurred at your place of business or in the same area as your business. There is significant damage to your location, equipment and operations. You will need to begin the Disaster Recovery process as soon as possible in order to minimize the down-time.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Checklist:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,108.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	1. The event has occurred at your location, and has directly impacted your business? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,113.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	2. There is severe damage to your business, equipment and location? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,118.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	3. The event can be mitigated by your employees and your Recovery Plans? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,123.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	4. It will be difficult to resume business within less then 1 full work day (8 hrs.)? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,128.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	5. You will require additional outside help to ensure this event is mitigated? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,133.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	6. The damage to your location and equipment may take a few days to replace? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,138.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	7. Your Insurance policy should cover all expenses due to this event? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,143.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	8. You require the activation of your Disaster Recovery Plans? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,148.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	9. You may require the use of an alternate location or facility to resume business? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,153.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	10. The event will be mitigated and business will resume with some down-time? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If you answered YES to over 50% of the questions above then your business will classify this event as a Phase 3
level event. Based on this decision you will begin the perform the following actions and define this event as follows.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phase 3 Definition:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Your business has experienced a Disaster with significant damage to your business, equipment and operations. Your business will be able to mitigate and control the situation with the activation of your disaster recovery plans, and the action plan you have developed.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phase 3 Action Plan:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>
					PROCEED IMMEDIATELY TO YOUR IMMEDIATE RESPONSE PLANS<BR><BR></P>",185,13,37);
		$pdf->Image('../images/PDFArrowInRed.gif',15,228.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	1. Activate Your Immediate Response Plans<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,233.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	2. Complete the Disaster Recovery Plans<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,238.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	3. Report incident to Insurance Broker and determine if claim is worth the deductible<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,243.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	4. Replace any damaged or lost items due to the event<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,248.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	5. Move to alternate location and resume business operations<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,253.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	6. Begin recovery and restoration operations for your business location.<BR></P>");

		//updates the Sub Section		
		$strSectionName = "Phase 4 - Level 3 Disaster - HIGHEST LEVEL";

		//updates the title and the Sub Section	
		$strAreaName = "Disaster Declaration Phase 4";

		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>After an event has occurred, it is the responsibility of the organization to determine the actual result of this event. Some events are more significant then others, and as a result will determine the action plan of the organization. The following Disaster Declaration Phase has been establish to help organizations determine what actions should be taken if this event happens at their location.<BR><BR>Please use the following reference as a definition of your event and what recommended actions should be taken by your organization.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phase 4 Disaster Declaration:<BR><BR></P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>An event has occurred at your place of business or in the same area as your business. There is significant damage to your location, equipment and operations. You require immediate assistance in the relocation and activation of your Disaster Recovery Plans. You will need to begin the recovery and restoration processes as soon as possible to keep the down-time to a minimal.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Checklist:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,113.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	1. The event has occurred at your location, and has directly impacted your business? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,118.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	2. There is severe damage to your business, equipment and location? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,123.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	3. The event can be mitigated by your employees and your Recovery Plans? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,128.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	4. It will be difficult to resume business within less then 1 full work day (8 hrs.)? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,133.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	5. You need immediate additional outside help to ensure this event is mitigated? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,138.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	6. The damage to your location and equipment require to to relocate? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,143.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	7. Your Insurance policy should cover all expenses due to this event? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,148.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	8. You require immediate activation of your Disaster Recovery Plans? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,153.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	9. You require the immediate use of an alternate location or facility to resume business? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,163.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	10. The event will be mitigated and business will resume with significant down-time? </P>");
		$pdf->BoldText("<P align='left'>YES NO<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If you answered YES to over 50% of the questions above then your business will classify this event as a Phase 3 level event. Based on this decision you will begin the perform the following actions and define this event as follows.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phase 4 Definition:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Your business has experienced a Disaster with sever damage to your business, equipment and operations. Your business will require the immediate re-location or alternate method to ensure you can remain in business. If not handled properly you will experience significant financial and business impacts.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Phase 4 Action Plan:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>
					PROCEED IMMEDIATELY TO YOUR IMMEDIATE RESPONSE PLANS<BR><BR></P>",185,13,37);
		$pdf->Image('../images/PDFArrowInRed.gif',15,243.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	1. Activate Your Immediate Response Plans<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,248.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	2. Complete the Disaster Recovery Plans<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,253.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	3. Report incident to Insurance Broker and determine if claim is worth the deductible<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,258.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	4. Replace any damaged or lost items due to the event<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,263.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	5. Move to alternate location and resume business operations<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,268.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>	6. Begin recovery and restoration operations for your business location.</P>");
	}//end of if
	
	//Disaster Declaration Guidelines
	if($arrTablesName[$intTableIndex] == "EXDisasterDeclarationGuidelines" && $row_loginFoundUser['Solution'] == 2)
	{		
		//updates the title and the Sub Section	
		$strAreaName = $arrAreaName[$intSectionArea].$intSectionTableIndex;
		$strSectionName = "";
				
		//adds one to the Section Table Index
		$intSectionTableIndex = $intSectionTableIndex + 1;
		
		//creates the page to be used
		$pdf->AddPage();							
		
		$intIndex = 0;//contorls the while loop
		$strNewSectionName = "";//holds the name of the Section with newline chars 
		$arrDDGuidelines = array(1 =>"FIRE","FLOOD","POWER OUTAGE","THUNDERSTORM","EARTHQUAKE","TORNADO","WINTER STORM","TERRORISM","NUCLEAR EVENT","HAZMAT EVENT");
	
		//breaks up the sectionName to the different words in order to put a newline char
		$arrSectionPieces = explode(" ", $row_rsPlans['sectionName']);
		
		while($intIndex < count($arrSectionPieces))
		{
			//checks if the $intIndex is at the fouth peice
			if($intIndex == 3 || strlen($arrSectionPieces[$intIndex]) >= 8)
				//gets the new section prices and adds the new line char
				$strNewSectionName = $strNewSectionName."<BR><BR>".$arrSectionPieces[$intIndex];
			else
				//gets the new section prices
				$strNewSectionName = $strNewSectionName." ".$arrSectionPieces[$intIndex];

			//adds to the intIndex
			$intIndex = $intIndex + 1;
		}//end of while loop

		//dispays the Section Title in the Page Title
		$pdf->PageTitlePage("<P align='center'>".$strNewSectionName."</P>");
					
		//sets the Form Name
		//$pdf->FormName($row_rsPlans['sectionName']."<BR><BR></P>");
		
		//updates the Sub Section		
		$strSectionName = $row_rsPlans['sectionName'];
			
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->QuestionsBody("<P align='left'>The following will give your business a breif overview of what you should do in the event of a disaster. Listed as the top 10 most likely disasters to occur at a business.<BR><BR>These are quick response suggestions and are not guaranteed to work for every business. If you use these as guidelines you will be able to have a basic knowledge of the specific event.<BR><BR></P>");				
		$pdf->BoldText("<P align='left'>Disaster Event #1: FIRE<BR>Disaster Event #2: FLOODING<BR>Disaster Event #3: POWER OUTAGE<BR>Disaster Event #4: THUNDERSTORM<BR>Disaster Event #5: EARTHQUAKE<BR>Disaster Event #6: TORNADO<BR>Disaster Event #7: WINTER STORM<BR>Disaster Event #8: TERRORISM<BR>Disaster Event #9: NUCLEAR POWER PLANT EMERGENCY<BR>Disaster Event #10: HAZARDOUS MATERIAL EMERGENCYBR<BR><BR></P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>There are many others events that can potentially effect you business. If you want to find out more about a specific event please visit the Canada Government Disaster Response Web-site or the F.E.M.A. web-site for the Untied States.</P>");

		//goes around creating the 5 section with the colors and names Title Page for the right side
		for($intForIndex = 1;$intForIndex <= count($arrDDGuidelines);$intForIndex++)
		{
			//updates the Sub Section		
			$strSectionName = "Disaster Response Guide - ".$arrDDGuidelines[$intForIndex];
		
			//creates the page to be used
			$pdf->AddPage();
		
			//the images of the Disaster Declaration Guidelines
			$pdf->Image("../images/DDGuidelines/Guidelines".$intForIndex.".jpg",4,22,196);
		}//end of for loop
	}//end of if
	
	//Immediate 
	if($intTableIndex == 13)
	{
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Employee WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
		
		$pdf->BoldText("<P align='left'>(IRT) is formed immediately after disaster occurs. It is the team starting the recovery process in motion.<BR><BR>The primary functions of this team, in order are:<BR><BR></P>");

		$pdf->Image('../images/PDFArrowInRed.gif',15,48.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>The safety and care of staff and customers.<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,53.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Elimination or control of the source of the disaster.<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,58.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start the notification process.<BR></P>");
		$pdf->Image('../images/PDFArrowInRed.gif',15,63.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>If possible, make notes of damage and cause of disaster.<BR><BR></P>");
		
		$pdf->QuestionsBody("<P align='left'>When the Disaster Management Team arrives, the leadership role of the IRT is turned over and the team is disbanded.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Immediately After Event, Identify Team Leader<BR><BR></P>",185,13,37);
		$pdf->BoldText("<P align='left'>Team Leader:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: Employee 1 Name: ".$row_InteralForm['contact_021']." Title: ".$row_InteralForm['contact_022']." Phone: ".$row_InteralForm['contact_023']." Cell: ".$row_InteralForm['contact_024']." E-Mail: ".$row_InteralForm['contact_025']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Supporting Employees:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 2 Name: ".$row_InteralForm['contact_026']." Title: ".$row_InteralForm['contact_027']." Phone: ".$row_InteralForm['contact_028']." Cell: ".$row_InteralForm['contact_029']." E-Mail: ".$row_InteralForm['contact_030']."<BR>Employee 3 Name: ".$row_InteralForm['contact_031']." Title: ".$row_InteralForm['contact_032']." Phone: ".$row_InteralForm['contact_033']." Cell: ".$row_InteralForm['contact_034']." E-Mail: ".$row_InteralForm['contact_035']."<BR><BR>The most senior and able-bodied staff member at the facility will need to step forward and announce that he/she is taking control of the staff. At that point he/she becomes the team leader and is in charge.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The first 2 acts you will perform will be to:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Until local or emergency response authority arrives, this individual will have full authority over the situation.<BR>
				1. Ensure the safety and well being of staff, customers and all other people around.<BR>
				2. Direct individuals to perform the duties that have been pre-determined to them.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Place emergency call(s) as appropriate<BR><BR>Emergency Services<BR><BR></P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>Local Police: ".$row_InteralForm['contact_271']." Location: ".$row_InteralForm['contact_272']." Phone: ".$row_InteralForm['contact_273']." Cell: ".$row_InteralForm['contact_274']." E-Mail: ".$row_InteralForm['contact_275']."<BR><BR>Reg. Police: ".$row_InteralForm['contact_276']." Location: ".$row_InteralForm['contact_277']." Phone: ".$row_InteralForm['contact_278']." Cell: ".$row_InteralForm['contact_279']." E-Mail: ".$row_InteralForm['contact_280']."<BR><BR>Fire: ".$row_InteralForm['contact_281']." Location: ".$row_InteralForm['contact_282']." Phone: ".$row_InteralForm['contact_283']." Cell: ".$row_InteralForm['contact_284']." E-Mail: ".$row_InteralForm['contact_285']."<BR><BR>Hospital: ".$row_InteralForm['contact_286']." Location: ".$row_InteralForm['contact_287']." Phone: ".$row_InteralForm['contact_288']." Cell: ".$row_InteralForm['contact_289']." E-Mail: ".$row_InteralForm['contact_290']."<BR><BR>Environment: ".$row_InteralForm['contact_291']." Location: ".$row_InteralForm['contact_292']." Phone: ".$row_InteralForm['contact_293']." Cell: ".$row_InteralForm['contact_294']." E-Mail: ".$row_InteralForm['contact_295']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>You should be able to identify the following to any and all of the above services listed:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
				1. Nature of the situation<BR>
				2. Where you are currently located<BR>
				3. Extent of injuries if any<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Start first Aid to injured<BR><BR></P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>Identify any and all personnel with first aid, CPR or any other medical training. During an event these individuals may be called upon to attend to injured employees, customer or other individuals affected.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Employees with CPR/First Aid Training:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name : ".$row_rsForm['IRT_CPR01']." Phone :  ".$row_rsForm['IRT_CPR02']." Cell : ".$row_rsForm['IRT_CPR03']." Training : ".$row_rsForm['IRT_CPR04']." <BR>Employee 2 Name : ".$row_rsForm['IRT_CPR05']." Phone : ".$row_rsForm['IRT_CPR06']." Cell : ".$row_rsForm['IRT_CPR07']." Training : ".$row_rsForm['IRT_CPR08']."<BR>Employee 3 Name : ".$row_rsForm['IRT_CPR09']." Phone : ".$row_rsForm['IRT_CPR10']." Cell : ".$row_rsForm['IRT_CPR11']." Training : ".$row_rsForm['IRT_CPR12']."<BR>Employee 4 Name : ".$row_rsForm['IRT_CPR13']." Phone : ".$row_rsForm['IRT_CPR14']." Cell : ".$row_rsForm['IRT_CPR15']." Training : ".$row_rsForm['IRT_CPR16']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 4 - Eliminate/Control the source of the disaster<BR><BR></P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>If and when possible, you will try to eliminate or contain of the source of the disaster if it is still a threat. This should be attempted only if you are trained in the techniques you will be using. The two most common events that would possibly be within your control are:<BR><BR>Fire – Record the location of any and all fire extinguishers, and use them if the situation is containable. Use caution when performing these tasks. ( Never use water on a fire, use available extinguishers as available)<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Fire Extinguishers<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Location #1 : ".$row_rsForm['IRT_FIRE01']." Maintenance Date : ".$row_rsForm['IRT_FIRE02']."<BR>Location #2 : ".$row_rsForm['IRT_FIRE03']." Maintenance Date : ".$row_rsForm['IRT_FIRE04']."<BR>Location #3 : ".$row_rsForm['IRT_FIRE05']." Maintenance Date : ".$row_rsForm['IRT_FIRE06']."<BR>Location #4 : ".$row_rsForm['IRT_FIRE07']." Maintenance Date : ".$row_rsForm['IRT_FIRE08']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Electricity Turn off electricity to building or office, if the situation calls for it.<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Main Breaker Location : ".$row_rsForm['IRT_UTIL01']." Maintenance Date : ".$row_rsForm['IRT_UTIL02']."
Other Breaker Location : ".$row_rsForm['IRT_UTIL03']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Gas - Turn off gas at nearest source<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Main Breaker Location : ".$row_rsForm['IRT_UTIL04']." Maintenance Date : ".$row_rsForm['IRT_UTIL05']."
Other Breaker Location : ".$row_rsForm['IRT_UTIL06']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Flooding/Water - Turn the water off at its source<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Main Breaker Location : ".$row_rsForm['IRT_UTIL07']." Maintenance Date : ".$row_rsForm['IRT_UTIL08']."
Other Breaker Location : ".$row_rsForm['IRT_UTIL09']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Air Cononditioners - Turn off air conditioner at nearest source<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Main Breaker Location : ".$row_rsForm['IRT_UTIL10']." Maintenance Date : ".$row_rsForm['IRT_UTIL11']."
Other Breaker Location : ".$row_rsForm['IRT_UTIL12']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>
						Make sure to close the doors as you exit the disaster locations.<BR><BR></P>",185,13,37);
		$pdf->BoldText("<P align='left'>Section 5 - Evacuate the Facility<BR><BR></P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>As a disaster or significant disruption occurs at your business location, you will need to pre-determine a evacuation method and meeting point for all employees to meet. This place should be outside the office building at a decently safe distance away.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The meeting location for all employees to meet after an evacuation order is:<BR><BR>Meeting Location #1: ".$row_rsForm['IRT_MEET01']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If that location is unavailable<BR><BR></P>",185,13,37);
		$pdf->BoldText("<P align='left'>Meeting Location #2: ".$row_rsForm['IRT_MEET02']."<BR><BR>The start the evacuation process the Immediate Response Team leader must:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
				1. Issue the evacuation order directly<BR>
				2. Assign a premises search team<BR>
				3. Search office for any personnel<BR>
				4. Perform a “head count” once the search is complete and you reach the meeting location.<BR><BR>If any individuals are missing, cross reference with all department heads to see if they person is away or can be accounted for. Should a person be unaccounted for, wait for the arrival of the emergency response unit and immediately tell them who is missing.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 6 - Direct Emergency Services, vehicles & staff to problem areas<BR><BR></P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>The Team Leader needs to send someone out to the front of the facility, at the street, and direct the emergency response units into the scene.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 7 - Notify Other Team leaders and back-ups<BR><BR></P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>Once the emergency response units have arrived and the situation is under control, the team leader will begin to notify the other response teams and members to begin reassigned recovery tasks.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The Immediately Response Team Leader will notify the following personnel:<BR><BR>Disaster Management Team:<BR>Team Leader:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_036']." Title: ".$row_InteralForm['contact_037']." Phone: ".$row_InteralForm['contact_038']." Cell: ".$row_InteralForm['contact_039']." E-Mail: ".$row_InteralForm['contact_040']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Supporting Employees:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 2 Name: ".$row_InteralForm['contact_041']." Title: ".$row_InteralForm['contact_042']." Phone: ".$row_InteralForm['contact_043']." Cell: ".$row_InteralForm['contact_044']." E-Mail: ".$row_InteralForm['contact_045']."<BR>Employee 3 Name: ".$row_InteralForm['contact_046']." Title: ".$row_InteralForm['contact_047']." Phone: ".$row_InteralForm['contact_048']." Cell: ".$row_InteralForm['contact_049']." E-Mail: ".$row_InteralForm['contact_050']."<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>You will activate these two teams, and they will report to the pre-determined <BR><BR></P>");
		$pdf->BoldText("<P align='left'>Emergency Operations Center. </P>");
		$pdf->QuestionsBody("<P align='left'>This location will be the meeting point for all teams off-site to run the recovery process<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The Emergency Operations Center is Located at:<BR><BR>Emergency Operations Centre #1:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Location: ".$row_rsForm['IRT_EOC01']."<BR>Address: ".$row_rsForm['IRT_EOC02']." Phone: ".$row_rsForm['IRT_EOC03']."<BR>Additional Information: ".$row_rsForm['IRT_EOC04']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Back-Up) Emergency OPerations Centre:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Location: ".$row_rsForm['IRT_EOC05']."<BR>Address: ".$row_rsForm['IRT_EOC06']." Phone: ".$row_rsForm['IRT_EOC07']."<BR>Additional Information: ".$row_rsForm['IRT_EOC08']."<BR><BR>Any other specifics or instructions that will be passed a long to other team members should be coordinated through the EOC.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 8 - Record Events<BR><BR></P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>Until the Disaster Management Team arrives, it is important that you record events as soon as possible. The primary reason is that you, or any other member of your staff, may not be able to enter the facility for days or even weeks. As such, before your memory fades, it is important that you start writing now. <BR><BR>To help reconstruct what happened, write down the following information:<BR><BR>Record what you remember immediately before and after the event.<BR>Record any warning sounds or visual sightings.<BR>Record names of people in the area.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Observations:<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Record any thing you noticed as “unusual” before it happened.<BR><BR><BR><BR><BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 9 - Brief Arriving DMT (Disaster Management Team)<BR><BR></P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>Provide a complete turnover to the incoming Disaster Management Team. Ensure that you turn over all forms and notes that you have completed during your response to the event. This recovery plan should also be turned over at this time.<BR><BR></P>");
		$pdf->BoldText("<P align='center'>Section 10 - Disband Immediate Response Team</P>",185,13,37);
	}//end of if
	
	//Disaster 
	if($intTableIndex == 14)
	{
		$pdf->BoldText("<P align='left'>(DMT) is comprised of the senior management from (your company). The Team is identified ahead of time, with
the team leader, backup and team members known.
<BR><BR>
The primary functions of this team, in the general order of sequence are:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
			1. Phone additional DMT members and members of the Administration Team (ADMT).<BR>
			2. Receive turnover from the IRT and take control of the recovery process.<BR>
			3. Control the recovery process by managing the various recovery teams as they rebuild.<BR>
			4. Work with insurance agents, attorneys, disbursement of funds.<BR>
			5. Work with suppliers and shippers.<BR>
			6. Make general management related business decisions.<BR>
			7. Keep your business going and retain clients and customers.<BR>
			8. The DMT is activated by a call from the Immediate Response Team (IRT).<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Receive Notification Call<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>As Team Leader and Backup you will be receiving a call from the IRT advising of an apparent disaster related event. You should have this recovery plan near by. At a minimum, you will need to know the following information:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Questions:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,133.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	WHAT HAPPENED?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,143.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	WHEN DID IT HAPPEN?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,153.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	ARE THERE ANY INJURIES?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,163.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	HAVE THE PROPER AUTHORITIES ARRIVED?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,173.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	IS THE SITUATION UNDER CONTROL?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,183.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	HOW EXTENSIVE IS THE DAMAGE?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,193.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	CAN WE CURRENTLY ENTER THE BUILDING?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,203.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	IS THE EOC ACCESSIBLE?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,213.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	WHAT ARE YOUR GREATEST CONCERNS?<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>This is the point of your first decision. Predicated on the above information you receive, you need to determine if this truly is a disastrous situation. If it is, the full recovery plan and process needs to be activated.
		<BR><BR>
1. Provide disaster instructions to the IRT caller<BR>
2. Tell him/her when you and your team members anticipate arriving at the facility<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Identify emergency operations<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>At this point you will need to determine if you can use the pre-determined EOC location, or will you need to find a alternate location.<BR><BR></P>");
				
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Immediate WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
				
		$pdf->BoldText("<P align='left'>The Emergency Operations Center is Located at:<BR>Emergency Operations Centre #1:<BR><BR></P>");
$pdf->QuestionsBody("<P align='left'>Location: ".$row_InteralForm['IRT_EOC01']."<BR>Address: ".$row_InteralForm['IRT_EOC02']." Phone: ".$row_InteralForm['IRT_EOC03']."<BR>Additional Information: ".$row_InteralForm['IRT_EOC04']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Back-Up) Emergency Perations Centre:<BR><BR></P>");
$pdf->QuestionsBody("<P align='left'>Location: ".$row_InteralForm['IRT_EOC05']."<BR>Address: ".$row_InteralForm['IRT_EOC06']." Phone: ".$row_InteralForm['IRT_EOC07']."<BR>Additional Information: ".$row_InteralForm['IRT_EOC08']."<BR><BR>Make sure to remind everyone to bring their copy of the Business Continuity Plan/Disaster Recovery Plan with them.<BR>Alert all recovery team leaders to activate there recovery plans as required.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>IMMEDIATE RESPONSE TEAM PLAN :<BR><BR></P>");

		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Employee WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
		
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_021']." Title: ".$row_InteralForm['contact_022']." Phone: ".$row_InteralForm['contact_023']." Cell: ".$row_InteralForm['contact_024']." E-Mail: ".$row_InteralForm['contact_025']."<BR>Employee 2 Name: ".$row_InteralForm['contact_026']." Title: ".$row_InteralForm['contact_027']." Phone: ".$row_InteralForm['contact_028']." Cell: ".$row_InteralForm['contact_029']." E-Mail: ".$row_InteralForm['contact_030']."<BR>Employee 3 Name: ".$row_InteralForm['contact_031']." Title: ".$row_InteralForm['contact_032']." Phone: ".$row_InteralForm['contact_033']." Cell: ".$row_InteralForm['contact_034']." E-Mail: ".$row_InteralForm['contact_035']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>DISASTER MANAGEMENT RECOVERY PLAN :<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_036']." Title: ".$row_InteralForm['contact_037']." Phone: ".$row_InteralForm['contact_038']." Cell: ".$row_InteralForm['contact_039']." E-Mail: ".$row_InteralForm['contact_040']."<BR>Employee 2 Name: ".$row_InteralForm['contact_041']." Title: ".$row_InteralForm['contact_042']." Phone: ".$row_InteralForm['contact_043']." Cell: ".$row_InteralForm['contact_044']." E-Mail: ".$row_InteralForm['contact_045']."<BR>Employee 3 Name: ".$row_InteralForm['contact_046']." Title: ".$row_InteralForm['contact_047']." Phone: ".$row_InteralForm['contact_048']." Cell: ".$row_InteralForm['contact_049']." E-Mail: ".$row_InteralForm['contact_050']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>DAMAGE ASSESSMENT TEAM:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_051']." Title: ".$row_InteralForm['contact_052']." Phone: ".$row_InteralForm['contact_053']." Cell: ".$row_InteralForm['contact_054']." E-Mail: ".$row_InteralForm['contact_055']."<BR>Employee 2 Name: ".$row_InteralForm['contact_056']." Title: ".$row_InteralForm['contact_057']." Phone: ".$row_InteralForm['contact_058']." Cell: ".$row_InteralForm['contact_059']." E-Mail: ".$row_InteralForm['contact_060']."<BR>Employee 3 Name: ".$row_InteralForm['contact_061']." Title: ".$row_InteralForm['contact_062']." Phone: ".$row_InteralForm['contact_063']." Cell: ".$row_InteralForm['contact_064']." E-Mail: ".$row_InteralForm['contact_065']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>INFORMATION TECHNOLOGY RECOVERY TEAM:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_066']." Title: ".$row_InteralForm['contact_067']." Phone: ".$row_InteralForm['contact_068']." Cell: ".$row_InteralForm['contact_069']." E-Mail: ".$row_InteralForm['contact_070']."<BR>Employee 2 Name: ".$row_InteralForm['contact_071']." Title: ".$row_InteralForm['contact_072']." Phone: ".$row_InteralForm['contact_073']." Cell: ".$row_InteralForm['contact_074']." E-Mail: ".$row_InteralForm['contact_075']."<BR>Employee 3 Name: ".$row_InteralForm['contact_076']." Title: ".$row_InteralForm['contact_077']." Phone: ".$row_InteralForm['contact_078']." Cell: ".$row_InteralForm['contact_079']." E-Mail: ".$row_InteralForm['contact_080']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>ADMINISTRATION TEAM RECOVERY PLAN:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_081']." Title: ".$row_InteralForm['contact_082']." Phone: ".$row_InteralForm['contact_083']." Cell: ".$row_InteralForm['contact_084']." E-Mail: ".$row_InteralForm['contact_085']."<BR>Employee 2 Name: ".$row_InteralForm['contact_086']." Title: ".$row_InteralForm['contact_087']." Phone: ".$row_InteralForm['contact_088']." Cell: ".$row_InteralForm['contact_089']." E-Mail: ".$row_InteralForm['contact_090']."<BR>Employee 3 Name: ".$row_InteralForm['contact_091']." Title: ".$row_InteralForm['contact_092']." Phone: ".$row_InteralForm['contact_093']." Cell: ".$row_InteralForm['contact_094']." E-Mail: ".$row_InteralForm['contact_095']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>ESSENTIAL FUNCTIONS TEAM PLAN :<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_096']." Title: ".$row_InteralForm['contact_097']." Phone: ".$row_InteralForm['contact_098']." Cell: ".$row_InteralForm['contact_099']." E-Mail: ".$row_InteralForm['contact_100']."<BR>Employee 2 Name: ".$row_InteralForm['contact_101']." Title: ".$row_InteralForm['contact_102']." Phone: ".$row_InteralForm['contact_103']." Cell: ".$row_InteralForm['contact_104']." E-Mail: ".$row_InteralForm['contact_105']."<BR>Employee 3 Name: ".$row_InteralForm['contact_106']." Title: ".$row_InteralForm['contact_107']." Phone: ".$row_InteralForm['contact_108']." Cell: ".$row_InteralForm['contact_109']." E-Mail: ".$row_InteralForm['contact_110']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>BUSINESS SUPPORT TEAM PLAN :<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_111']." Title: ".$row_InteralForm['contact_112']." Phone: ".$row_InteralForm['contact_113']." Cell: ".$row_InteralForm['contact_114']." E-Mail: ".$row_InteralForm['contact_115']."<BR>Employee 2 Name: ".$row_InteralForm['contact_116']." Title: ".$row_InteralForm['contact_117']." Phone: ".$row_InteralForm['contact_118']." Cell: ".$row_InteralForm['contact_119']." E-Mail: ".$row_InteralForm['contact_120']."<BR>Employee 3 Name: ".$row_InteralForm['contact_121']." Title: ".$row_InteralForm['contact_122']." Phone: ".$row_InteralForm['contact_123']." Cell: ".$row_InteralForm['contact_124']." E-Mail: ".$row_InteralForm['contact_125']."<BR><BR>Those who are not requested to come in are to remain at home and be available to be on site within 2 hours notice. The intent is to have enough people to do the job, and at the same time allow others to rest as they will eventually join the recovery process and ultimately relieve the leaders.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Notify other team members, provide instructions<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>You will not notify all team leaders to report to the Emergency Operations Center, and convey any specific instructions that could prove useful under the circumstances.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 4 - All teams assemble at EOC, Arrive at facility<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Even before your arrival, you will have developed a fairly good idea of what the situation looks like. disasterly join the staff that has been evacuated. Look for the IRT Leader who phoned you earlier. At that point, start the following: 
<BR><BR>
1. Obtain a solid understanding of the disaster situation from what is known.<BR>
2. Talk with authority in charge at scene and determine their position on the available occupancy of the facility.
<BR><BR>
Record:<BR><BR>
Name of Authority Figure: _________________________<BR><BR>
Job Title: ______________________________________<BR><BR>
Agency: ______________________________________<BR><BR>
Contact Number: _______________________________<BR><BR>
If the building can be occupied, find out from the authorities what the next step(s) will be, and what steps you can take starting now.Write down names and numbers.
<BR><BR>
Step #1: Name: Phone:<BR>
Step #2: Name: Phone:<BR>
Step #3: Name: Phone:<BR>
Step #4: Name: Phone:<BR>
Step #5: Name: Phone:<BR>
Step #6: Name: Phone:<BR>
Step #7: Name: Phone:<BR><BR></P>");
		
		//creates the page to be used
		$pdf->AddPage();
				
		$pdf->QuestionsBody("<P align='left'>Obtain a complete turnover from the IRT Leader, making sure you collect all of his/her notes However, the fire authority may indicate that the building can not be entered, as it will either be “yellow tagged” (limited entry under controlled conditions), or “red tagged” (no entry under any situation until major or structural damage is corrected).<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If either of the above two conditions exist, attempt to find out:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,57.8);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	What Happens Now?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,67.8);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	When will this happen?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,77.8);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	Who should contact you?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,87.8);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	Can you provide any assistance?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,97.8);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>	Other?<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>When finished with the fire authority, request the IRT leader to come with you to the EOC to provide additional information as questions arise.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 5 - Begin recovery process<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Arrive at the EOC:
<BR><BR>
Start setting up the location to ensure that all the items you need are there. Ask the first Administrative Team (ADMT) Member who arrives to start unpacking the contents of the emergency supply cabinet.
<BR><BR>
Collect all of the call listings from each of the team leaders and backups. The ones who could not be reached should have a notation next to their name.
1. If the Fire Department has not “red tagged” the facility, then entry into the building should be possible. If entry is allowed, you will, as soon as the Damage Assessment and Reconstruction Team (DART) arrive:<BR>
2. Advise them of the situation as if it is known at the time<BR>
3. Have them start their initial damage assessment, if feasible at this time, by entering the building and following their plan instructions. Vendors and manufacturers are not present during this first assessment, but you should start calling them in at a predetermined time to start a detailed damage assessment. You will need to line them up.<BR>
4. Remind them that they have a 2-hour period to complete their initial assessment of the situation. After the first 60 minutes, a single team member is required to report back to the EOC for a status update. Instruct them to take notes, and pictures, on each area of the building. (Use the camera in EOC emergency supplies along with the rolls of film. This is great for visual review, and is excellent as evidence to the insurance company). If you
do not have a camera obtain one by any means necessary.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Make a list of items that are not present at the EOC that need to be acquired immediately:<BR><BR>
ITEM #1 :<BR>
ITEM #2 :<BR>
ITEM #3 :<BR>
ITEM #4 :<BR>
ITEM #5 :<BR>
ITEM #6 :<BR>
ITEM #7 :<BR>
ITEM #8 :<BR>
ITEM #9 :<BR>
ITEM #10 :<BR>
ITEM #11 :<BR>
ITEM #12 :<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 6 - Dealing with the Media<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>If this is a regional event, do not expect any media, e.g., radio, television or press. However, if this is a local disaster situation, they may be there before you even arrive.
<BR><BR>
They are extremely well trained in getting information from organizations, accurate or not. Furthermore, they will report it, accurate or not. Conflicting stories are noted and reported as “no one seems to be in charge or understands the situation”. Your clients and customers do not like to hear that.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>As such, it is important that:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>1. Only one individual, acting as the company spokesperson, will talk to the media.<BR>
2. All other staff members need to be polite, but request that the media talk to the spokesperson.<BR>
3. The person needs to be “polished” and initially read only a brief statement which may sound like( you can find the draft version in the Business Continuity Plan)<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Prepare a preliminary report to be released to the media immediately after an event has occurred:<BR><BR>This should include:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>1. Company Name<BR>
2. The fact that you have experienced a disaster<BR>
3. What injuries have occurred due to the disaster<BR>
4. Number of employees injured and status<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Media Spokesmen:<BR><BR></P>");
	   $pdf->QuestionsBody("<P align='left'>Employee: ".$row_rsForm['DMT_MED01']." Title: ".$row_rsForm['DMT_MED02']." Phone: ".$row_rsForm['DMT_MED03']." Cell: ".$row_rsForm['DMT_MED04']." E-Mail: ".$row_rsForm['DMT_MED05']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Media Statement: (Pre-determined)<BR><BR></P>");
		
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Crisis WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
			
		$pdf->QuestionsBody("<P align='left'>".$row_InteralForm['comm_med01']."<BR><BR>(Example of Key Points to Make)<BR><BR>
We have a business recovery plan in place and have already begun to rebuild the business.<BR>
Have the person then answer any questions directly, honestly and with short answers.<BR>
Do not volunteer information.<BR>
End interview as soon as possible.<BR>
If the event was big enough, your customers will either see it on TV or read about it.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 7 - Start notification of key contacts<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>While the damage assessment team performs their assessment, have a member of the Disaster Management Team place all important calls.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 8 - Damage Assessment Team returns<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>After two (2) hours, the DART is required to report back to you. The information they have gathered, recorded and subsequently formulated a recommendation on will then be presented to the entire DMT for your evaluation.<BR><BR>Simply put, you will either:<BR><BR>
Be able to rebuild your facilities within an acceptable period of time to accommodate sufficient staff to resume business, or You will need to move either all or part of your business operations to another location You are now about to make that decision.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 9 - Disaster Management Team to make decision<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Predicated on the damage and the amount of time to rebuild, this is the biggest decision since the initial event.
<BR><BR>
Can you move back in a few days or will it be weeks or months?<BR>
Do you need temporary space and work area, or can you wait?<BR>
What commitments do you have to constituencies?<BR>
Can work stop completely for a week, or do certain areas need to be functioning tomorrow?<BR>
Where are you going to go now?<BR><BR>
At this time you have a reduced and finite set of resources in the form of work area and staff. You need to decide where your recovery will take place and this information needs to now be conveyed to your staff. At this point they are ready to go, and are looking for directions.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Prepare a plan of action:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>ACTION PLAN:<BR><BR></P>");
		
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>Section 10 - Disaster Management Team to assemble all teams and hold a meeting<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>You have just made a decision and are ready to start the actual recovery process. Up to now, there really hasn’t been any recovery effort. Remember, Stage 1 for all teams is the preliminary fact finding and preparation for the actual recovery process from the time the event happened.
<BR><BR>
As soon as possible, call that meeting. As this is a complete team meeting, you should schedule it in about 2 hours, giving time for others to arrive. Give the assignment to the Administration Team to call the members in.
<BR><BR>
This meeting should address all teams initial steps of assessment and recovery. Team leaders will present findings and overall discussion of next steps.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>THE FIRST STAGE IS NOW AT AN END FOR THE DART AND ALL OTHER TEAMS.<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Anything that was not completed during this stage will need to be noted as an outstanding deliverable and carried over to Stage 2.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Stage 2 - Recovery<BR>CLAIMS COORDINATOR:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee Name: ".$row_rsForm['DMT_CLAIM01']." Title: ".$row_rsForm['DMT_CLAIM02']." Phone: ".$row_rsForm['DMT_CLAIM03']." Cell: ".$row_rsForm['DMT_CLAIM04']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If Unavailable:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>(Back-Up) Employee Name: ".$row_rsForm['DMT_CLAIM05']." Title: ".$row_rsForm['DMT_CLAIM06']." Phone: ".$row_rsForm['DMT_CLAIM07']." Cell: ".$row_rsForm['DMT_CLAIM08']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The claims coordinator will need to perform the following to the best of their ability<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>1. Mitigate further damage to property.<BR>
2. Contact a security company to provide 24/7 coverage to protect your assets.<BR>
3. Notify your insurance carrier as soon as possible.<BR>
4. Develop and maintain a standardized form for tracking and logging all damage related information.<BR>
5. Initiate the claims process by following the procedure identified below.<BR>
6. Contact independent insurance expert if damage is extensive.<BR>
7. Ensure that extensive pictures are taken.<BR>
8. Work with the DART Team.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 11 - Assign Business Recovery Coordinator<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>The selected Business Recovery Coordinator will assist the DMT and focus on the various business units’ recovery and resumption process. That person will be responsible for:<BR><BR>
1. Ensuring that the Recovery Business Team(s) are recovering according to timeline objectives.<BR>
2. Escalating problems to the DMT Team Leader.<BR>
3. Ensuring the Recovery Business Team(s) have the resources they need to recover and resume operations.<BR>
4. Other<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 12 - Recovery Activity List<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Provide support and assistance to your recovery teams as needed. YES NO<BR><BR>
Acquire temporary space if required. YES NO<BR><BR>
Attend to staff related issues and concerns. YES NO<BR><BR>
Work with the bank and insurance company. YES NO<BR><BR>
Work with attorneys on any legal issues. YES NO<BR><BR>
Place business related calls to vendors, contacts, clients and suppliers. YES NO<BR><BR>
Reroute mail and deliveries. YES NO<BR><BR>
Address transportation and delivery issues. YES NO<BR><BR>
Ensure accurate records are being maintained. YES NO<BR><BR>
Get 4 copies of each roll of film developed, ASAP. YES NO<BR><BR>
Provide offsite food and “surprise items” to all recovery locations. YES NO<BR><BR>
Authorize payments of funds. YES NO<BR><BR>
Ensure your information systems department is functional. YES NO<BR><BR>
Verify accounts payable and receivable are functioning. YES NO<BR><BR>
Ensure time sheets and payroll procedures are in place and deliveries made on time. YES NO<BR><BR>
Confirm all required voice and data circuits are active and available. YES NO<BR><BR>
Provide twice daily briefings to your team leaders and backups on the progress and challenges. YES NO<BR><BR>
Receive the damage assessment reports back. YES NO<BR><BR>
Ensure that a complete damage assessment is made with vendors and contractors. YES NO<BR><BR>
Work with your landlord as required. YES NO<BR><BR>
Obtain a general contractor to coordinate the rebuilding. YES NO<BR><BR>
Decide if you will rebuild or move. YES NO<BR><BR>
Request, review and modify the new building prints YES NO<BR><BR>
Keep operations going by ensuring that requests are met for space, staff, equipment, supplies, etc. YES NO<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 13 - Monitor and document recovery activities<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>At this point in time, recovery is underway, no matter where or how that is taking place. To coordinate this effort, you will need to constantly monitor the changing events, both challenges and progress. Document all major decisions and actions that are involved in the recovery effort.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>This type of information is received from multiple sources:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>1. Receive status reports from computer recovery facility.<BR><BR>
2. Receive reports from all Team Leaders, at a minimum, twice daily.<BR><BR>
3. Monitor weather and environmental status.<BR><BR>
4. Monitor pending labor, manufacturing or transportation stoppages or strikes.<BR><BR>
5. Monitor critical supply deliveries and follow-up.<BR><BR>
6. Monitor delays in completion of installations, repairs or services.<BR><BR>
7. Monitor personnel activities hours, strain, etc.<BR><BR>
8. Receive outage impact assessments & reports from government agencies.<BR><BR>
9. Assure adequate funds, resources, etc.<BR><BR>
10. Request additional support as needed<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 14 - Normal business operations<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>As recover steps begin to wind down, you will need to start performing normal business activities to ensure continuity of your business exists.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 15 - Final recovery activities<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>1. Verify all building permits are in place and approved<BR><BR>
2. Perform a final walk through with your general contractor<BR><BR>
3. Accept delivery of furniture and equipment<BR><BR>
4. Ebsure movers and materials are in place for move<BR><BR>
5. Send a \"house warming\" invitation to all clients and customers<BR><BR>
6. Follow-up with a thank you and token gifts to all outside help</P>");
	}//end of if
	
	//Damage 
	if($intTableIndex == 15)
	{
		$pdf->BoldText("<P align='left'>The Damage Assessment and Reconstruction Team<BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>(DART) is comprised of individuals knowledgeable about your company from a facilities and hardware
standpoint. The Team is identified ahead of time, with the team leader, backup and team members known. It is
the team that controls and directs the facilities recovery process.
<BR><BR>
The DART is activated by a call from the Immediate Response Team (IRT).
<BR><BR>
The primary functions of this team, in the general order of sequence are:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,67.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Phone additional DART members and members of the Information Systems Team<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,77.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Perform both the preliminary and detailed damage assessment function<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,87.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Provide a written recommendation to the DMT on the condition of the business<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,97.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work directly with the general contractor, sub-contractor and building department<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,107.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Coordinate the rebuilding of the facility<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,117.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure any equipment supportive of the business is ordered and installed<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,127.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that all environmental support systems are available prior to move back<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,137.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with maintenance providers<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>In summary, the team provides the strategic and necessary planning, scheduling, coordination and implementation of all the hardware and facility related tasks key to rebuilding the facility and restoring the work units’ environment.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Receive notification call<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Both the Team Leader and Backup will be receiving a call from the IRT advising you of an apparent disaster related event. You should have quick access to this recovery plan.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>At a minimum, you will need to know the below information:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,203.4);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>What Happened? :<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,213.4);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>When did it happen? :<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,223.4);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Have the authorities arrived?:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,233.4);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Is it under control?:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,243.4);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>How extensive in the damage?:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,253.4);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Can we access the building?:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Notify all other DART team members<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Team Leader:<BR><BR></P>");

		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Employee WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
		
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_051']." Title: ".$row_InteralForm['contact_052']." Phone: ".$row_InteralForm['contact_053']." Cell: ".$row_InteralForm['contact_054']." E-Mail: ".$row_InteralForm['contact_055']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Supporting Employees:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 2 Name: ".$row_InteralForm['contact_056']." Title: ".$row_InteralForm['contact_057']." Phone: ".$row_InteralForm['contact_058']." Cell: ".$row_InteralForm['contact_059']." E-Mail: ".$row_InteralForm['contact_060']."<BR><BR>
Employee 3 Name: ".$row_InteralForm['contact_061']." Title: ".$row_InteralForm['contact_062']." Phone: ".$row_InteralForm['contact_063']." Cell: ".$row_InteralForm['contact_064']." E-Mail: ".$row_InteralForm['contact_065']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>When notifying individuals, you will need to:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>1. Provide the location of the EOC.<BR>2. Convey any special instructions pertinent to them.
		<BR><BR>When you have reached, or attempted to reach the above individuals, get ready to depart for the EOC. Do not remain
and try again. Make note of anyone you couldn’t reach.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Arrive at facility<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Even before your arrival, you will have developed a fairly good idea of the situation. Immediately join staff that has been evacuated. Look for the DMT and IRT Leader. If you cannot locate the Disaster Management Team (DMT) Leader, at that point start the following:<BR><BR>1. Obtain solid understanding of immediate situation.<BR>2. Talk with the authority in charge at the scene and determine their position on the available occupancy of the facility.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Name of authority figure in charge: ___________________________________________<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>If the building is occupied, find out from this individual what the next steps that your business is allowed to take. What are the most important tasks of those identified to begin to perform. List the tasks and names of people who can perform these tasks.<BR><BR>
Task #1: ______________________________Name: ____________________<BR>Back-Up Name:___________________<BR><BR>
Task #2: ______________________________Name: ____________________<BR>Back-Up Name:___________________<BR><BR>
Task #3: ______________________________Name: ____________________<BR>Back-Up Name:___________________<BR><BR>
Task #4: ______________________________Name: ____________________<BR>Back-Up Name:___________________<BR><BR>
Task #5: ______________________________Name: ____________________<BR>Back-Up Name:___________________<BR><BR>
Task #6: ______________________________Name: ____________________<BR>Back-Up Name:___________________<BR><BR>
Task #7: ______________________________Name: ____________________<BR>Back-Up Name:___________________<BR><BR>
Task #8: ______________________________Name: ____________________<BR>Back-Up Name:___________________<BR><BR></P>");
		
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->QuestionsBody("<P align='left'>Consider your worst nightmare, to be </P>");
		$pdf->QuestionsBody("<P align='left'>\"red tagged,\"</P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'> with no entry under any situation until major or structural damage is corrected. The restriction would be due to safety concerns and could be caused by chemicals, asbestos, structural risk, electrical exposure, or other reasons.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If either of the above two conditions exist, attempt to immediately find out:<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,48.2);
		$pdf->SetX(20);
		$pdf->BoldText("What happens now?<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,53.2);
		$pdf->SetX(20);
		$pdf->BoldText("When will this happen?<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,58.2);
		$pdf->SetX(20);
		$pdf->BoldText("Who should contact you?<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.2);
		$pdf->SetX(20);
		$pdf->BoldText("Can you do anything to speed up the process?<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>When finished with the fire authority, you are to go to the Recovery area to obtain additional information.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 4 - Goto the EOC and perform preliminary Damage Assessment<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Go to the EOC and wait for the DMT to instruct you to start your preliminary two-hour damage assessment of the facility. The DMT needs more solid information to make a business decision on how they want to proceed with the recovery process.
		<BR><BR>
Your findings and reports WILL determine the course of action they will be taking during the following days, weeks or even months.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Simply put, your company will either:<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,138);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Be able to rebuild your facilities within an acceptable period of time to accommodate sufficient staff to resume business, or;<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,148);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>You’ll need to move either all or part of your business operations to another location.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,153);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>You will be given two hours to accomplish the assessment. A substantial amount of information can be gathered during that time.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>When you are told to do so:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Start the preliminary damage assessment by entering the building and following your plan instructions. Vendors and manufacturers will not be present during this assessment, but the DMT will start calling them. They will need to come in later at a predetermined time to start a detailed damage assessment. You will need to schedule and coordinate their arrival.
		<BR><BR>
You will be given two hours to complete your initial assessment of the situation.
		<BR><BR>
After the first 60 minutes, a single team member is required to report back to the EOC for a status update to the DMT.
		<BR><BR>
NOTE: If the DMT has contacted the company’s insurance carrier, an adjuster may request that they accompany you during this initial assessment.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 5 - Mitigate any further damage<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Take any and all emergency steps necessary to mitigate future damage and to protect yourself and others. This includes water, gas, electrical and other building support services.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 6 - Enter damaged facility and begin assessment<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>The team now enters the facility to get orientated. If there is any odor of diesel or gas, exit immediately and report the findings to the DMT.
		<BR><BR>
Perform a quick walk-through of the facility to get a general understanding of the buildings condition. This will give you a good overview of where you and your team will want to focus.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>To do this:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Walk the entire facility to understand the challenges you face.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,68.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Note any significant or obvious problems which could prevent the restoration of the facility in the immediate future.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Now go back and start a more complete assessment of the areas that you noted as being more heavily damaged.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The following check list will help you avoid missing key items. Make multiple copies for all team members before
using it: <BR><BR></P>");
		$pdf->BoldText("<P align='left'>ASSESSMENT CHECKLIST - CATEGORY #1<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Support Functions - Air Conditioning - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Support Functions - Chillers - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Support Functions - Cooling Tower- Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Support Functions - Water Pumps- Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Support Functions - City Water- Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Support Functions - Electrical - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Support Functions - City Power- Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Support Functions - UPS System- Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Support Functions - Diesel Generator- Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Support Functions - Batteries - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Support Functions - Other - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>ASSESSMENT CHECKLIST - CATEGORY#2<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Structural - External & Exterior - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Structural - Foundation Cracks - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Structural - ROOF - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Structural - OTHER - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>ASSESSMENT CHECKLIST - CATEGORY #3<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Computer Equipment - LAN System - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Computer Equipment - DASD - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Computer Equipment - TAPE- Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Computer Equipment - COMM. Hub - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Computer Equipment - Printers - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Computer Equipment - Other - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>ASSESSMENT CHECKLIST - CATEGORY #4<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Media - Tapes - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Media - Cartridges - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Media - Other - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>ASSESSMENT CHECKLIST - CATEGORY #5<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Network - Modems - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Network - Telco-Voice - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Network - Other - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>ASSESSMENT CHECKLIST - CATEGORY #6<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Office Equipment - Copiers - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Office Equipment - Mailling Equipment - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Office Equipment - Faxes - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Office Equipment - Other - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>ASSESSMENT CHECKLIST - CATEGORY #7<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Office Equipment - Desks- Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Office Equipment - File Cabinets - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Office Equipment - Other - Affected? YES NO<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Description:<BR><BR><BR></P>");
		$pdf->BoldText("<P align='center'>Do not apply power to test anything. This should be left to technicians or electricians.<BR></P>");
		$pdf->QuestionsBody("<P align='left'>After one hour, send an individual back to the EOC to do a quick report on your findings to this point. Immediately return and continue the assessment. You have one more hour.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 7 - Return to EOC and present findings to the Disaster Management Team<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>It has now been two (2) hours, and you must report back to the DMT. The information you have gathered and recorded will be presented to the entire DMT for their evaluation. They need information and your best possible recommendations of what to do.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 8 - Attend DMT briefing and decision making meeting<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>STAGE 1 IS NOW AT AN END FOR THE DART AND ALL OTHER TEAMS. Anything that was not completed during this stage will need to be noted as an outstanding deliverable and carried over to Stage 2.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Meet with the DMT to receive your specific instructions<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>IMMEDIATELY after the management meeting, you will want to meet with the DMT to determine what your restrictions and limitations are. Also, if they are looking for anything specific, or within a particular time frame, find out now.
		<BR><BR>
During the assessment stage of the recovery process it is extremely important that you remain in constant dialogue with the DMT. This is important so that they have the required information to manage the recovery process. If possible, at a minimum, agree to meet face to face a couple of times a day.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Restoration services<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>During your preliminary assessment, you noticed many things needing attention. What is easily overlooked is immediate restoration of areas not heavily damaged or destroyed. This type of work is generally done by a restoration services company.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Services of particular importance are:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>1. Water extraction<BR><BR>
2. Document drying - - this process needs to be started soon after the damage.<BR><BR>
3. Smoke and soot removal<BR><BR>
4. Contents cleaning<BR><BR></P>");
		$pdf->BoldText("<P align='left'>DISASTER RESTORATION SERVICES:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>ServiceMaster Agreement:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Continuity Inc. has an exclusive partnership with the largest supplier of Disaster Restoration Services in North America. ServiceMaster Clean can provide your business with any and all of your disaster restoration services. Please find the
contact and agreement information below:<BR><BR></P>");
		$pdf->Image('../images/ServiceMasterClean.jpg',110,157);	
		$pdf->BoldText("<P align='left'>ServiceMaster Phone:<BR><BR>
What You Need to Know:
<BR><BR>
1.<BR>
2.<BR>
3.<BR>
4.<BR>
5.<BR>
6.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Perform complete assessment of business with help from an outside source<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Depending on the situation and the scope of the damage you found during your preliminary assessment, you will want to call in outside services. Remember, you are still in assessment mode. The building, building support equipment, office area and office related equipment needs a detailed assessment and determination of action made. As such, using the<BR><BR></P>");
		$pdf->BoldText("<P align='left'>following guide, you will need to phone for outside support:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>As appropriate, phone the numbers below and schedule soon. This is a priority item.<BR><BR>It is recommended that you use standard forms to fit your needs. If you don’t standardize, you will get responses back that range from formal proposals which are indexed and tabbed, to hand written notes on scraps of paper.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>CONSTRUCTION:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Landlord Name: ".$row_InteralForm['contact_311']." Phone: ".$row_InteralForm['contact_312']." Cell: ".$row_InteralForm['contact_313']." E-Mail: ".$row_InteralForm['contact_314']."<BR>Other Management: ".$row_InteralForm['contact_315']." Phone: ".$row_InteralForm['contact_316']." Cell: ".$row_InteralForm['contact_317']." E-Mail: ".$row_InteralForm['contact_318']."<BR></P>");
		$pdf->QuestionsBody("<P align='left'>
Contractor 1: ".$row_InteralForm['contact_296']." Where: ".$row_InteralForm['contact_297']." Phone: ".$row_InteralForm['contact_298']." Cell: ".$row_InteralForm['contact_299']." E-Mail: ".$row_InteralForm['contact_300']."<BR>
Contractor 2: ".$row_InteralForm['contact_301']." Where: ".$row_InteralForm['contact_302']." Phone: ".$row_InteralForm['contact_303']." Cell: ".$row_InteralForm['contact_304']." E-Mail: ".$row_InteralForm['contact_305']."<BR>
Contractor 3: ".$row_InteralForm['contact_306']." Where: ".$row_InteralForm['contact_307']." Phone: ".$row_InteralForm['contact_308']." Cell: ".$row_InteralForm['contact_309']." E-Mail: ".$row_InteralForm['contact_310']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Office Equipment - For any damaged equipment, a technician needs to be brought in to run diagnostics. This
would include copiers, mailing machines, faxes or other equipment specific to your business.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Current) </P>");
		$pdf->QuestionsBody("<P align='left'>Equipment Service: ".$row_InteralForm['contact_319']." Phone: ".$row_InteralForm['contact_320']." Cell: ".$row_InteralForm['contact_321']." E-Mail: ".$row_InteralForm['contact_322']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Alternate) </P>");
		$pdf->QuestionsBody("<P align='left'>Equipment Service: ".$row_InteralForm['contact_323']." Phone: ".$row_InteralForm['contact_324']." Cell: ".$row_InteralForm['contact_325']." E-Mail: ".$row_InteralForm['contact_326']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Alternate) </P>");
		$pdf->QuestionsBody("<P align='left'>Equipment Service: ".$row_InteralForm['contact_327']." Phone: ".$row_InteralForm['contact_328']." Cell: ".$row_InteralForm['contact_329']." E-Mail: ".$row_InteralForm['contact_330']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Interior Furnishings - </P>");
		$pdf->QuestionsBody("<P align='left'>For any furniture of which damage is noted, or suspected, an interior designer will need to be brought in to provide a proposal.<BR>This would include: partitions, desks, chairs, file cabinets or other equipment specific to your business.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Current) </P>");
		$pdf->QuestionsBody("<P align='left'>Furnishing Service: ".$row_InteralForm['contact_331']." Phone: ".$row_InteralForm['contact_332']." Cell: ".$row_InteralForm['contact_333']." E-Mail: ".$row_InteralForm['contact_334']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Alternate) </P>"); 
		$pdf->QuestionsBody("<P align='left'>Furnishing Service: ".$row_InteralForm['contact_335']." Phone: ".$row_InteralForm['contact_336']." Cell: ".$row_InteralForm['contact_337']." E-Mail: ".$row_InteralForm['contact_338']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Alternate) </P>");
		$pdf->QuestionsBody("<P align='left'>Furnishing Service: ".$row_InteralForm['contact_339']." Phone: ".$row_InteralForm['contact_340']." Cell: ".$row_InteralForm['contact_341']." E-Mail: ".$row_InteralForm['contact_342']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Unique Items - </P>");
		$pdf->QuestionsBody("<P align='left'>For any items unique to your business, you will need to make contact with specific suppliers. Items such as: safes, security wiring, custom work areas, etc. will need to be addressed by you.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Current) </P>");
		$pdf->QuestionsBody("<P align='left'>Unique Service: ".$row_InteralForm['contact_343']." Phone: ".$row_InteralForm['contact_344']." Cell: ".$row_InteralForm['contact_345']." E-Mail: ".$row_InteralForm['contact_346']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Alternate) </P>");
		$pdf->QuestionsBody("<P align='left'>Unique Service: ".$row_InteralForm['contact_347']." Phone: ".$row_InteralForm['contact_348']." Cell: ".$row_InteralForm['contact_349']." E-Mail: ".$row_InteralForm['contact_350']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>(Alternate) </P>");
		$pdf->QuestionsBody("<P align='left'>Unique Service: ".$row_InteralForm['contact_351']." Phone: ".$row_InteralForm['contact_352']." Cell: ".$row_InteralForm['contact_353']." E-Mail: ".$row_InteralForm['contact_354']."<BR><BR></P>");
		$pdf->Image('../images/ServiceMasterClean.jpg');	
		$pdf->BoldText("<P align='left'><BR><BR>Disaster Restoration & Reconstruction Services:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The Information Technology Team </P>");
		$pdf->QuestionsBody("<P align='left'>will handle all Information Systems equipment. However, related facilities issues need to be addressed by you. On all of the above construction and repair, the following needs to be provided to you in the form of a written proposal to include:
		<BR><BR>
				1. Nature of the work<BR>
				2. Detail of the work to be performed><BR>
				3. Completion time<BR>
				4. Restrictions<BR>
				5. Financial breakdown of the cost(s)<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 4 - Meet with each vendor & contractor<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>For each arriving person or group do the following:
		<BR><BR>
				1. Explain the situation as it exists now.<BR><BR>
				2. Explain what your objectives are.<BR><BR>
				3. Explain why they are here.<BR><BR>
				4. Walk them through the facility, pointing out and discussing items of concern.<BR><BR>
				5. Return to the meeting room and explain your expectations.<BR><BR>
				6. Include: proposal, scope of work, timelines and financial implications.<BR><BR>
				7. Provide them a format in which they need to respond back to you.<BR><BR>
				8. They need a fixed format, or they’ll give you answers in too many different ways.<BR><BR>
				9. Ask that they voice ANY concerns now.<BR><BR>
				10. Set a date for them to respond.<BR><BR>
				11. Be aggressive, as their dates will slip.<BR><BR>
				12. Inform the DMT of your progress and expectations.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 5 - Receive all proposals back and make decision<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Receive responses, in person, on the date you stipulate. This is important so that you can review the proposal with them present. This will permit clarification and eliminate delays (and excuses) due to mail.<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
1. Have the General Contractor start a set of preliminary drawings if he has not already done so.<BR>
2. Compile all the responses you have received and schedule a meeting with the DMT.<BR>
3. Prepare a set of recommendations.<BR>
4. Provide supporting documentation and be ready to suggest options.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 6 - Meet with DMT and present proposals<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Present all possible scenarios for rebuilding or repairs. Make a quick and accurate decision so the process can begin as soon as possible.<BR><BR></P>");
			
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>Section 7 - Receive instructions from the DMT<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>The outcome of this meeting will determine what direction and subsequent activities you and your team will be performing. After this meeting you need to leave with a clear and concise understanding of what the expectations are for your
team.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>As such, do not leave the meeting until you know<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63);
		$pdf->SetX(20);
		$pdf->BoldText("What does management want to do?<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,68);
		$pdf->SetX(20);
		$pdf->BoldText("How will the bids be awarded?<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,73);
		$pdf->SetX(20);
		$pdf->BoldText("Who will notify the vendors? When? How?<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78);
		$pdf->SetX(20);
		$pdf->BoldText("Are there any restrictions?<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83);
		$pdf->SetX(20);
		$pdf->BoldText("Are there areas where accelerated reconstruction needs to be addressed?<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>You are now finished with assessing the damage. You have received responses back for the reconstruction and have presented the options to management. They have given you directions. It is now time to actually start the rebuilding of the facility.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Below is a list of suggested activities which you will want to consider Schedule and conduct the kick-off meeting.<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,129);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Go over the preliminary drawings and develop a draft schedule of activities.<BR></P>");	
		$pdf->Image('../images/PDFArrowInOrange.gif',15,134);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with the General Contractor and any sub-contractors.<BR></P>");	
		$pdf->Image('../images/PDFArrowInOrange.gif',15,139);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Provide assistance and direction as needed.<BR></P>");	
		$pdf->Image('../images/PDFArrowInOrange.gif',15,144);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive final drawings, including mechanical.<BR></P>");	
		$pdf->Image('../images/PDFArrowInOrange.gif',15,149);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive solid schedule.<BR></P>");	
		$pdf->Image('../images/PDFArrowInOrange.gif',15,154);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continue to monitor progress and note/handle/ resolve problems as appropriate.<BR></P>");	
		$pdf->Image('../images/PDFArrowInOrange.gif',15,159);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Keep the DMT informed of progress and situations<BR><BR></P>");
					
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>Stage 4 - RESTORE<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Below is a list of suggested activities which you will want to consider including in this plan as you write it for your company:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,48.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Schedule and conduct the kick-off meeting, particularly if one service organization will be handling a lot of equipment.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,58.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Develop a repair schedule.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Provide assistance and direction as needed.<BR></P>");	
		$pdf->Image('../images/PDFArrowInOrange.gif',15,68.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continue to monitor progress and note/handle/resolve problems as appropriate.<BR></P>");	
		$pdf->Image('../images/PDFArrowInOrange.gif',15,73.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Keep the DMT informed of progress and situations.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contact a salvage company.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive from the General Contractor the tentative occupancy date of the facility. With that, the hardware  and equipment previously ordered can be scheduled for installation.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,93.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Close coordination with the facilities staff at the site needs to be done.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>To accomplish this:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,113.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Request delivery of the ordered hardware from the vendors.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,118.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Schedule loading dock and elevator if appropriate.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,123.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Select the installer and schedule the installation date(s), based on the delivery schedule.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,128.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Track and expedite deliveries when required.<BR></P>");
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive incoming hardware and inventory upon arrival.<BR></P>");
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contact the installer and verify installation date and time.<BR></P>");
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Install equipment and obtain serial number.<BR></P>");
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Conduct user tests on the equipment.<BR></P>");
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Place equipment on maintenance with maintenance vendor.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>
				During the above sequence of events it is essential that you keep the DMT updated on the activities.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Stage 5 - RELOCATE<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Schedule the date of the move with the company. Request that they show up at least a week ahead of time to walkthrough the move. You should order boxes and moving labels.<BR><BR></P>");
	}//end of if
	
	//ITRecoveryTeam 
	if($intTableIndex == 16)
	{
		$pdf->BoldText("<P align='left'>The Information Technology Recovery & Response Plan<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>The Information Systems Team (IST) is comprised of individuals knowledgeable in the information systems hardware and software of (your company). The Team is identified ahead of time, with the team leader, backup and team members know. It is the team that provides the critical information systems support functions during the recovery.<BR>The IST is activated by a call from the Damage Assessment and Reconstruction Team (DART).<BR><BR>The primary functions of this team, in the general order of sequence, are:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,77.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Phone additional IST members<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,82.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Assess the damage<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,87.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Identify and secure a location to install or use a recovery service’s hardware<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,92.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Locate, order and install hardware<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,97.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Restore the system(s) and user data<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,102.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>IPL or Boot up the system<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,107.9);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start production again<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The above are all critical functions that must be accomplished if the recovery is to be successful.<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Prior to attempting to perform any recovery, it is important that you have critical information and files stored offsite, at an offsite storage provider’s facility. At a minimum, the following should be stored there, and available for a recall in the
event of an emergency:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,153.8);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Full Volume Weekend Backups<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,158.8);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Disaster Recovery Team Plans<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,163.8);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Operating Procedures<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,168.8);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Program Listings<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,173.8);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Technical Library Material<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Stage 1 - RESPOND<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Receive notification call<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Both the Team Leader and Backup will be receiving a call from the Damage Assessment Team advising you of an apparent disaster related event. You should have quick access to this recovery plan.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>At a minimum, you will need to know the below information:<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,228.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>What happened?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,238.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>When did it happen?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,248.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Have the authorities arrived?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,258.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Is it under control?<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,268.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>How extensive is the damage?<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Notify all other IT team members<BR><BR></P>",225,80,22);

		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Employee WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
					
		$pdf->BoldText("<P align='left'>Team Leader:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_066']." Title: ".$row_InteralForm['contact_067']." Phone: ".$row_InteralForm['contact_068']." Cell: ".$row_InteralForm['contact_069']." E-Mail: ".$row_InteralForm['contact_070']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Supporting Employees:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 2 Name: ".$row_InteralForm['contact_071']." Title: ".$row_InteralForm['contact_072']." Phone: ".$row_InteralForm['contact_073']." Cell: ".$row_InteralForm['contact_074']." E-Mail: ".$row_InteralForm['contact_075']."<BR>Employee 3 Name: ".$row_InteralForm['contact_076']." Title: ".$row_InteralForm['contact_077']." Phone: ".$row_InteralForm['contact_078']." Cell: ".$row_InteralForm['contact_079']." E-Mail: ".$row_InteralForm['contact_080']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Goto EOC and begin IT damage assessment<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Arrive at the EOC and receive an update from the DMT. If conditions permit, one member of your team needs to join the DART, which may have already started their preliminary damage assessment. Participate in producing the preliminary report, as it pertains to the condition of the information systems area, and contribute to the recommendation For other team members, the damage assessment will not be back for a period of time. In the interim, it is suggested that you and your team members start to do some preparation and planning: Review the recovery process you previously outlined in your recovery plan.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 4 - Damage Assessment Team returns to EOC<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>After two (2) hours, the DART is required to report back to the DMT. You will be asked to attend the briefing and assist as required.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 5 - the DMT must call a meeting to report findings and take actions<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>STAGE 1 IS NOW AT AN END FOR ALL TEAMS. </P>");
		$pdf->QuestionsBody("<P align='left'>Any thing that was not completed during this stage will need to be noted as an outstanding deliverable and carried over to Stage 2. At this point, EVERYONE enters Stage 2 of their recovery plan and starts the recovery process. To this point, no recovery has begun, just information gathering, assessment and strategic decision-making.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Stage 2 - RECOVERY<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>All individuals who will be involved in the information systems recovery need to meet. Using the information obtained during the damage assessment, you will need to develop a strategy, prioritize and assign responsibility. Remember, each server, mid-range system or mainframe had certain applications running on them to support a business function. It is the business units that determine the priorities, and it is those priorities that you must follow when installing or repairing hardware, and subsequently recovering the systems.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Communication<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Maintaining accurate and timely communications, both oral and written, is CRITICAL. This will be repeated for each and every stage, for incoming calls, outgoing calls and relaying messages. If you have any clue that there are not enough phones now, contact the Essential Functions Team immediately and have them place an emergency order for lines and instruments (telephones). The shortage of phones will only get worse. The constant exchange of information is critical in the process.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Extent of damage will determine your recovery options<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>At this point you will need to locate, or make available, a processor to recover your system three situations (scenarios) could exist as a result of the disaster. You will need to respond to the one that is applicable to the situation:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Damage Scenario 1 - </P>");
		$pdf->QuestionsBody("<P align='left'>There has been extensive damage to the facility, prohibiting you from entering and gaining access to the hardware. The DART team has estimated that it will be days to weeks, before you can start any recovery. If this is the case, you will need to use Strategy 1 or 2 as identified below.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Damage Scenario 2 - </P>");
		$pdf->QuestionsBody("<P align='left'>The hardware has been destroyed. If this is the case, you will need to use Strategy 1 or 2 as identified below.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Damage Scenario 3 - </P>"); 
		$pdf->QuestionsBody("<P align='left'>The hardware has been damage but is repairable, and the facility will be available for occupancy within a few days. If this is the case, you will use Strategy 3 as identified below. The following pages identify three different recovery strategies available to you for securing a processor. Though each strategy is different and unique, all follow the same general path with certain variations on themes. No matter which strategy you choose to use, you need to select one now and start your system recovery process:
<BR><BR>
Determine Quick Response plan for each scenario:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Scenario #1:<BR><BR><BR><BR><BR><BR><BR><BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Who will be responsible?<BR>
Name: ______________________________ Phone:_________________<BR>
Alternate Employee: ______________________________ Phone:_________________<BR><BR></P>");

		$pdf->BoldText("<P align='left'>Scenario#2:<BR><BR><BR><BR><BR><BR><BR><BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Who will be responsible?<BR>
Name: _____________________________ Phone:_________________<BR>
Alternate Employee: _____________________________ Phone:________________<BR><BR>
Alternate Phone:_________________<BR></P>");
		$pdf->BoldText("<P align='left'>Scenario #3:<BR><BR><BR><BR><BR><BR><BR><BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Who will be responsible?<BR>
Name: _____________________________ Phone:_________________<BR>
Alternate Employee: _____________________________ Phone:_________________<BR><BR></P>");
					
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>Example of Response actions:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,33.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Immediately call your recovery services provider, and declare a disaster.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,38.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Provide them with your authorization code or password.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,43.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Obtain from the recovery services provider, the location where you will be recovering.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,48.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Call your offsite storage provider, and advise them where to ship your backup tape storage containers.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,58.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Identify team coverage at the offsite computer recovery facility.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Depart for the location.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,68.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Arrive at the recovery facility and check-in.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,73.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Receive the hardware from the recovery services provider.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Verify that the hardware configuration you received, matches your prior configuration.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Receive the previously recalled backup storage containers from your offsite storage provider, unpack and inventory.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,93.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Notify offsite storage provider to bring empty storage containers to the facility where will be recovering.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,103.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Notify offsite storage provider to start picking up your backup tapes effective __ (enter date) __.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,113.5);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Go to Operations section.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Operations Considerations Prior to production<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>You are about to start restoring volumes, and hope to be back into production in the near future. It is easy to loose tract that once in production, operations will need day-to-day items and supplies. Therefore, at this point, have one of your team members make a list of items that will be needed. Request your assigned team member to locate a member of the ADMT and request that they emergency order and ship the following items, supportive of a production environment<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - System Restore<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>All system restores and recoveries are different, and unique, depending on:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,183.2);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>The manufacturer of the processor<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,188.2);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>The processor model<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,193.2);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>The operating system, and level you are at<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,198.2);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>The program product backup software package you used, and<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,203.2);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>The frequency and types of backups you previously had taken.<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Despite all the above differences, when you go to restore the system and databases from the backups, there are many similarities. In essence, you will essentially be following a three-step process, or combination of the three. Remember; during this entire restore process, that at this point you will be using your last complete set of full volume backups.
<BR><BR>
For most organizations, the last complete set of full volume backups would have been taken during the previous weekend. And finally, those would be the volumes that you just previously recalled from your offsite storage provider.<BR><BR></P>");
		
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Information WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
		
		$pdf->BoldText("<P align='left'>Step by Step procedural outline for restoring your internal IT system.<BR><BR>".$row_InteralForm['IT_sum01']."<BR><BR>
Make sure the following areas are outlined:<BR><BR>
1. Load or Restore operating system<BR><BR>
2. Restore Libraries<BR><BR>
3. Restore data base<BR><BR>
4. Verify all restores<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Bringing up the system<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>At this juncture, all the system restores are complete. It is now time to bring the system up following the below procedures:<BR><BR>Bring the system up ( IPL, Boot, etc. ) using the below instructions you wrote earlier<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Please develop a Step by Step procedural outline for bringing your systems up.
<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR></P>");
//".$row_InteralForm['IT_sum02']."<BR><BR>

		$pdf->BoldText("<P align='left'>Make sure to include the following:<BR><BR>
			1. Perform a system checkout<BR><BR>
				2. Check Utilities<BR><BR>
				3. Check program products<BR><BR>
				4. Check security, passwords and ID's<BR><BR>
				5. Verify network connectivity<BR><BR>
				6. Restart Transmission groups<BR><BR></P>");
		
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>Section 3 - Operations considerations prior to productions<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>The system is now up, and forward recovery of user data is about to start. You are almost complete with your Stage 3 activities, and have little time left before production starts.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>At this point, you will need to:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Follow up with the ADMT to ensure they have ordered the tape and printer items you previously requested that they order.<BR>
Obtain estimated delivery time of each. Assign someone on your team to ensure that critical operational materials are available, and if not, develop them:<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Operator Procedures<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Forms used by Operators (Logs, Etc.)<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,88.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Operations Documentation<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,93.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>Forms Setup Instructions<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,98.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>IPL/Boot Procedures<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,103.3);
		$pdf->SetX(20);
		$pdf->BoldText("<P align='left'>System Shutdown Instructions<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 4 - Restoring and brining up user files current<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>In </P>");
		$pdf->BoldText("<P align='left'>Section 1, </P>");
		$pdf->QuestionsBody("<P align='left'>you restored the database(s), and user data, back to the previous weekend. That set of full volume restores included recovery of user data. If you or your users also perform daily/nightly backups, and if you rotate these backups offsite to an offsite storage provider, additional recovery may be required at this point. That is, you may now need to perform forward recovery by using log tapes, journals, etc.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>WRITE THE ENTIRE PROCESS, STEP-BY-STEP, THAT IS REQUIRED TO RESTORE YOUR USER FILES:
<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR></P>");
//".$row_InteralForm['IT_sum03']."<BR><BR>
		$pdf->BoldText("<P align='left'>If the daily/nightly backups are not available to apply forward recovery:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,218.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Their files have been restored back to the previous weekend<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,223.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>There has been a loss of data<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,228.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>They will need to re-enter data and transactions<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,233.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>The system is now ready to be verified prior to turning the system over to the users for production<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,243.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Notify the Disaster Management Team of system availability<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,248.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Request the users to verify their network connectivity back to the restored system<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,253.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Have the users verify data integrity<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,258.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Perform a complete system backup and immediately rotate the backups to offsite storage<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,263.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>At this point the system is ready for the startup of production, as normally scheduled.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Stage 4- RESTORE<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Normal Production<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Everyone in your company should be aware that the system has been recovered and is available for usage. At this point, if appropriate, the system is turned over to who ever supports the normal day to day operations functions:<BR><BR></P>");		
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Establish I/O Control Area<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,68.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Institute Software Security Procedures Start Preparing Schedules by:<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,73.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contacting Users<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receiving Instructions From User Departments<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Using Published Schedules<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,88.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Coordinate Start Up With Your User Departments<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,93.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Institute Normal Operating Discipline<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Resume Processing, Which Will Include Such Items As:<BR><BR></P>");	
		$pdf->BoldText("<P align='left'>Starting and stopping on-lines - - </P>");
		$pdf->QuestionsBody("<P align='left'>document the procedures here, or reference a document stored offsite that has the information<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Nightly production runs - - </P>");
		$pdf->QuestionsBody("<P align='left'>document the procedures here, or reference a document stored offsite that has the information<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Daily backups - - </P>");
		$pdf->QuestionsBody("<P align='left'>document the procedures here, or reference a document stored offsite that has the information<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Full volume weekend backups and offsite rotation - - </P>");
		$pdf->QuestionsBody("<P align='left'>document the procedures here or reference a document offsite that has the info<BR><BR></P>");
		$pdf->BoldText("<P align='left'>IPLs or re-booting - - </P>");
		$pdf->QuestionsBody("<P align='left'>document the procedures here, or reference a document stored offsite that has the information<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Printing And Distribution - - </P>");
		$pdf->QuestionsBody("<P align='left'>document the procedures here, or reference a document stored offsite that has the information<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Nightly transmissions - - </P>");
		$pdf->QuestionsBody("<P align='left'>document the procedures here, or reference a document stored offsite that has the information<BR><BR></P>");
				
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>Section 2 - Test systems<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Until now all of your work has been focused on providing stable hardware and system restoration. This activity has addressed only existing production systems. However, now that production is returning to normal, you need to address “test systems”.<BR><BR></P>");		
		$pdf->Image('../images/PDFArrowInOrange.gif',15,53.6);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Normally testing has a low or lower priority than production. However, there certain cases where test applications<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.6);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>need to become production in order to:<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,68.6);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Meet and comply with new city, state, or federal regulations<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,73.6);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Meet changes to tax codes<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78.6);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Fixes to existing production problems<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83.6);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Payroll modifications<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,88.6);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Tax filing changes<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,93.6);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Support a marketing campaign that has already been launched<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Getting ready to return to facility<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Now that the system is back to normal, it is time to focus on migrating your system back to the rebuilt location. (Skip this if you recovered at your facility.) Note that the term migration was used, not a recovery. A recovery is a response to an unplanned event, where as, a migration is a planned event.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Stage 5 - RELOCATE<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - System Shut down<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>During this stage, you and the other work units will actually be moving back to the rebuilt facility. There are numerous activities that you will need to assist in, and others you will be asked to assist with. Therefore, on the day of the move:<BR></P>");		
		$pdf->Image('../images/PDFArrowInOrange.gif',15,173.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Remind all teams in the morning of the time you plan to shut the system down<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,178.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Verify with field engineering the scheduled time of their arrival<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,183.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Send a broadcast message to the users that the system is coming down in 30 minutes<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,188.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Terminate any online(s), cc: Mail, batch jobs, etc.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,193.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure the system is stopped, with nothing running<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,198.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Perform a complete set of backups and send offsite<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,203.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Perform a 2nd set of backups and retain in your possession<BR><BR></P>");
		$pdf->BoldText("<P align='left'>If are running at a temporary facility on your own hardware, and not at a recovery service provider, you will need to perform these additional tasks:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,228.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Power off the system and turn over to field engineering<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,233.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Have field engineering de-install and pack for shipment<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,238.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Verify that all components are included in shipment, including all cables, tapes and manuals<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,243.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Coordinate with movers to load the items<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,248.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Depart for your facility<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - System start up<BR><BR><BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>When you arrive at the rebuilt facility, you will need to:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,33.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Coordinate with movers to unload and stage the items<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,38.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure field engineering arrives to install the system(s)<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,43.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive the system from field engineering<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>The system is now connected and in place for you to start your recovery and system checkout prior to turning the system over to the DMT.<BR><BR></P>");		
		$pdf->BoldText("<P align='left'>If you are migrating back from a recovery services provider, you will need to:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>
			Obtain the full volume backup tapes which were taken before the system shut down and shipped to your facility<BR>
				Perform a complete system restore, using the backup tapes
		<BR><BR>
If you are migrating back, and have been using your own hardware, you may be able to avoid the restore functions previously identified. That is, if the system was shut down properly, and moved correctly, all the system and user data may still be good. As such, no restores will be required at this time.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>To check it out, you will need to:<BR><BR>
			Bring the systems up ( IPL, Boot, etc. ) using the instructions you wrote earlier</P>");
	}//end of if
	
	//Administration 
	if($intTableIndex == 17)
	{
		$pdf->BoldText("<P align='left'>The Administration Team Recovery and Response Plan<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>(ADMT) is comprised of individuals knowledgeable in the administrative functions of your business. The Team is identified ahead of time, with the team leader, backup and team members. It is the team that provides the critical support functions at the temporary facility during recovery.<BR><BR>The ADMT is activated by a call from the Disaster Management Team (DMT).<BR><BR>The primary functions of this team, in the general order of sequence, are:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Phone additional ADMT members.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Phone the leader and backup of the Voice and Data Team.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,88.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Phone the leader and backup of the Corporate Support Team.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,93.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Phone the leader and backup of the Core Business Team.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,98.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive names of team leaders and backups who could not be reached. Try again.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,103.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Set up the Emergency Operations Command (EOC).<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,108.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>At request of the DMT, work with insurance agents, attorneys, disbursement of funds.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,113.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with suppliers and shippers.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,118.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Handle travel and lodging.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,123.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>If applicable, keep your normal business functions going.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>These are all critical functions, which must be accomplished if the recovery is to be successful.<BR><BR>Stage 1 - RESPOND<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Receive notification call<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>As the Team Leader and/or Backup, you will be receiving a call from the DMT advising of an apparent disaster related event. You should have this recovery plan near by. Find out the location of the EOC and get ready to go there. However, first you will need to do a few more things.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Notify all other Team Members:<BR><BR></P>");

		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Employee WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
		
		$pdf->BoldText("<P align='left'>Team Leader:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_081']." Title: ".$row_InteralForm['contact_082']." Phone: ".$row_InteralForm['contact_083']." Cell: ".$row_InteralForm['contact_084']." E-Mail: ".$row_InteralForm['contact_085']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Supporting Employees:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 2 Name: ".$row_InteralForm['contact_086']." Title: ".$row_InteralForm['contact_087']." Phone: ".$row_InteralForm['contact_088']." Cell: ".$row_InteralForm['contact_089']." E-Mail: ".$row_InteralForm['contact_090']."<BR>Employee 3 Name: ".$row_InteralForm['contact_091']." Title: ".$row_InteralForm['contact_092']." Phone: ".$row_InteralForm['contact_093']." Cell: ".$row_InteralForm['contact_094']." E-Mail: ".$row_InteralForm['contact_095']."<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>You may have a prolonged stay at the EOC site, and you will be putting in long hours initially. Afterwards, a nearby hotel may appear more attractive than a long commute home. As such, it is suggested that you pack some personal items and a change of clothes.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Goto EOC and get Command center operational<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Arrive at the EOC and start setting up the location to ensure that the items you need are there. If you are the first Administrative Team Member who arrives, start unpacking the contents of the emergency supply cabinet. This is the Command Center and it will serve as the management and administrative work area during the recovery process. Once you have it set up, this location will be staffed 24 hours a day until the DMT issues other instructions. The location should have the appropriate equipment and supplies already there.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Below is a list of items you’ll most likely need to start the process. Other items will need to be acquired as they are identified. Take a complete inventory of items in stock and make note of items missing.<BR><BR>
Emergency Operations Centre Supplies:<BR><BR>
Sample List:<BR><BR>
Cell:								Req. Stock													Date Usage								Have Need<BR>
Radio:								Req. Stock													Date Usage								Have Need<BR>
Batteries:								Req. Stock													Date Usage								Have Need<BR>
Camera:								Req. Stock													Date Usage								Have Need<BR>
Water:								Req. Stock													Date Usage								Have Need<BR>
Food:								Req. Stock													Date Usage								Have Need<BR>
First Aid :								Req. Stock													Date Usage								Have Need<BR>
Answering:								Req. Stock													Date Usage								Have Need<BR>
PC:								Req. Stock													Date Usage								Have Need<BR>
Generator:								Req. Stock													Date Usage								Have Need<BR>
Phone Line :								Req. Stock													Date Usage								Have Need<BR>
DR Plan :								Req. Stock													Date Usage								Have Need<BR>
Equipment:								Req. Stock													Date Usage								Have Need<BR>
Tool Kit :								Req. Stock													Date Usage								Have Need<BR>
Duct Tape :								Req. Stock													Date Usage								Have Need<BR>
Legal Pad:								Req. Stock													Date Usage								Have Need<BR>
Pen/Pencil:								Req. Stock													Date Usage								Have Need<BR>
Flip Charts :								Req. Stock													Date Usage								Have Need<BR>
MSG Pad:								Req. Stock													Date Usage								Have Need<BR>
Coffee:								Req. Stock													Date Usage								Have Need<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Start calling as required<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Collect all of the call listings from each of the team leaders and backups as they arrive. The ones who could not be reached should have a notation next to the name. Start the follow-up process to individuals previously not reached. Each one should be phoned again and paged. Accurate notes are important a reference. All incoming calls need to be recorded and logged. If and when they have been handled, they can be crossed off as “connected”.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 4 - Assist in key contact notification as required<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>While waiting on the DART to complete their initial assessment, most likely there will be numerous calls that will need to be made. You may be requested to contact any of the below:<BR><BR></P>");		
		$pdf->BoldText("<P align='left'>Key Contacts:<BR><BR></P>");		
		$pdf->QuestionsBody("<P align='left'>Security Firm: ".$row_InteralForm['contact_355']." Loc.: ".$row_InteralForm['contact_356']." Phone: ".$row_InteralForm['contact_357']." Cell: ".$row_InteralForm['contact_358']." E-Mail: ".$row_InteralForm['contact_359']."<BR><BR>Insurance Broker: ".$row_InteralForm['contact_360']." Loc.: ".$row_InteralForm['contact_361']." Phone: ".$row_InteralForm['contact_362']." Cell: ".$row_InteralForm['contact_363']." E-Mail: ".$row_InteralForm['contact_364']."<BR><BR>Insurance Agency: ".$row_InteralForm['contact_365']." Loc.: ".$row_InteralForm['contact_366']." Phone: ".$row_InteralForm['contact_367']." Cell: ".$row_InteralForm['contact_368']." E-Mail: ".$row_InteralForm['contact_369']."<BR><BR>Attorney: ".$row_InteralForm['contact_370']." Loc.: ".$row_InteralForm['contact_371']." Phone: ".$row_InteralForm['contact_372']." Cell: ".$row_InteralForm['contact_373']." E-Mail: ".$row_InteralForm['contact_374']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>See Emergency Contacts Section for More Resources.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 5 - Damage assessment team returns, report to meeting to address the situation<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>After two (2) hours, the DART is required to report back to the CMT. You may be asked to attend the briefing and assist as required.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>STAGE 1 IS NOW AT AN END FOR ALL TEAMS.<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Anything that was not completed during this stage will need to be noted as an outstanding deliverable and carried over to Stage 2.<BR><BR>At this point, EVERYONE enters Stage 2 of their recovery plan and starts the recovery process.<BR>To this point, no recovery has begun, just information gathering, assessment and strategic decision-making.<BR><BR></P>");

		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Information WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
		
		$pdf->BoldText("<P align='left'>Stage 2 - RECOVERY<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Your primary working location will be at the EOC. However, remember that you may be supporting other teams at other sites. The needs of those teams are just as important as the DMT. To get the business up and running as normal requires that all teams recover, not just a few. Do not forget those teams.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Call your off-site storage provider<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Phone your Offsite Storage Provider and advise them of the situation. The numbers can be found below:<BROff-site Storage Provider: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_InteralForm['IT_OFF01']."<BR>Address: ".$row_InteralForm['IT_OFF02']." Phone: ".$row_InteralForm['IT_OFF03']." Contact: ".$row_InteralForm['IT_OFF04']."<BR>E-Mail: ".$row_InteralForm['IT_OFF05']."<BR>Inform them that your company has had a major disaster and you are recalling certain, or all, storage containers to the address you will provide. You will need to provide them with the below information in order for them to ship the containers.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Communication<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Maintaining accurate and timely communications, both oral and written, is CRITICAL. This will be repeated for each and every stage, for incoming calls, outgoing calls and relaying messages. If there are not enough phones now, contact the Essential Functions Recovery Team immediately and have them place an emergency order for lines and instruments (telephones). The shortage of phones will only get worse. The constant exchange of information is critical in this process.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Travel and Lodging<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>The first week(s) after a major disaster are extremely exhausting. Sixteen to eighteen hour days will not be uncommon initially. After about a week or so it could taper off to twelve to sixteen hours a day. Team members will not want to drive home any distance after a shift like that, and you don’t want them to either. As such, call the below hotel(s) and advise them of the situation. You may want to book a block of rooms.<BR><BR></P>");

		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Logistics WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
		
		$pdf->BoldText("<P align='left'>If you require lodging for your employees here are some local Hotel Addresses and contact information:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Hotel 1: ".$row_rsForm['LOG_07']." Phone: ".$row_rsForm['LOG_08']." Fax: ".$row_rsForm['LOG_09']."<BR><BR>Hotel 1: ".$row_rsForm['LOG_43']." Phone: ".$row_rsForm['LOG_44']." Fax: ".$row_rsForm['LOG_45']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 4 - Assure personal issues are addressed<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>If you have a Human Resources Department, you will need to coordinate activities with them. Items to consider are:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Ensure payroll checks are delivered to the staff members’ locations<BR>
Collect time sheets and send to internal payroll, or outside payroll provider. The contacts are as follows:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>PAYROLL CONTACT:<BR><BR>Employee 1: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['ADMIN_PAY01']." Title: ".$row_rsForm['ADMIN_PAY02']."<BR>Phone: ".$row_rsForm['ADMIN_PAY03']." Cell: ".$row_rsForm['ADMIN_PAY04']." E-Mail: ".$row_rsForm['ADMIN_PAY05']."<BR></P>");
		$pdf->BoldText("<P align='left'>Employee 1: </P>");
		$pdf->QuestionsBody("<P align='left'>".$row_rsForm['ADMIN_PAY06']." Title: ".$row_rsForm['ADMIN_PAY07']."><BR>Phone: ".$row_rsForm['ADMIN_PAY08']." Cell: ".$row_rsForm['ADMIN_PAY09']." E-Mail: ".$row_rsForm['ADMIN_PAY10']."<BR><BR></P>");
						
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>Section 5 - Ensure mail and deliveries are made to proper locations<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'Deliveries to the normal address may not be possible. Ensure that the Post Office and all of the below delivery
services are contacted and a new address provided:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Post Office Name: ".$row_rsForm['LOG_28']." Phone: ".$row_rsForm['LOG_29']." Fax: ".$row_rsForm['LOG_30']."<BR><BR>
UPS Name : ".$row_rsForm['LOG_79']." Phone: ".$row_rsForm['LOG_80']." Fax: ".$row_rsForm['LOG_81']."<BR><BR>
FedEX Name: ".$row_rsForm['LOG_82']." Phone: ".$row_rsForm['LOG_83']." Fax: ".$row_rsForm['LOG_84']."<BR><BR>
Purolator Name: ".$row_rsForm['LOG_76']." Phone: ".$row_rsForm['LOG_77']." Fax: ".$row_rsForm['LOG_78']."<BR><BR>
DSL Name: ".$row_rsForm['LOG_85']." Phone: ".$row_rsForm['LOG_86']." Fax: ".$row_rsForm['LOG_87']."<BR><BR>
Couier Serv. Name: ".$row_rsForm['LOG_19']." Phone: ".$row_rsForm['LOG_20']." Fax: ".$row_rsForm['LOG_21']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'Stage 3 - RESUME<BR><BR></P>");

		$pdf->Image('../images/PDFArrowInOrange.gif',15,103.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continued Business Recovery and Starting Reconstruction<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,108.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>You will need to start to perform your normal job duties<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,113.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continue to support activities outlined in Stage 2<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,118.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>(Add others here)<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Although the above list is short, adding more would be purely filling space at this time. From an administrative support role, you will simply be providing ad-hoc support as requested. That is, don’t worry about the lack of specific activities not  being identified here. These will come to you.<BR><BR></P>");		
		$pdf->BoldText("<P align='left'>Section 1 - Written Correspondence – External<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Generate, as requested, written correspondence to vendors, suppliers, insurance agencies, customers, etc.<BR><BR></P>");	
		$pdf->BoldText("<P align='left'Stage 4 - RESTORE<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Reconstruction continues and businesses are functioning as normal, only away from home<BR><BR></P>",225,80,22);
		$pdf->Image('../images/PDFArrowInOrange.gif',15,193.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start to perform your normal job duties<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,198.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continue to support activities outlined in Stage 2<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Although the above list is short, adding more would be purely filling space at this time. From an administrative support role, you will simply be providing ad-hoc support as requested. That is, don’t worry about the lack of specific activities not
being identified here. These will come to you.<BR><BR></P>");
		$pdf->BoldText("<P align='left'Stage 5 - RELOCATE<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Construction is complete and migration back to the final facility is done.<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>During this stage, you and the other work units will actually be moving back to the rebuilt facility. There are numerous activities that you will need to assist in, and others you will be asked to assist with.<BR><BR></P>");
						
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->Image('../images/PDFArrowInOrange.gif',15,23.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that someone canceled the lease or agreement on the EOC if applicable<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,28.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that someone has canceled all of the utilities, including: electrical, gas and particularly the phones at the temporary facility<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,38.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Check to see that moving materials arrive<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,43.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Assist in ordering supplies for the rebuilt facility<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,48.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Notify FEDX, UPS, AirBorne and the Local Post Office<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,53.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Pack all materials not going back to the emergency storage area<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,58.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Return supply items which were not used to the emergency storage area<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Take an inventory of what is there, and what will need to be reordered<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,68.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that all notes and papers are picked up, boxed and sent to the DMT leader for disposition<BR></P>");
	}//end of if
	
	//Essential 
	if($intTableIndex == 18)
	{
		$pdf->BoldText("<P align='left'>The Essential Functions Team Recovery & Response Plan<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>(EFT) is comprised of individuals knowledgeable in a particular mission that your institution does. The Team(s) is identified ahead of time, with the team leader, backup and team members known. It is any team that shares common skills and works together to fulfill a critical mission to your constituencies. There may be many of these teams, each requiring a continuity plan.
		<BR><BR>
All other teams exist to provide support to this team during the recovery. The EFT is activated by a call from the Administration Team (ADMT).
		<BR><BR>
The primary functions of this team, in the general order of sequence are:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,88.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Phone additional EFT members.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,93.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Re-establish or secure a work area to conduct your business.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,98.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Obtain the required resources you need to produce your product/service.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,103.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with other recovery teams to direct their efforts.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,108.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contact your suppliers to discuss your company’s situation and secure their commitment.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,113.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contact your key customer base to discuss your company’s situation and reassure them of your confidence in your company’s ability to quickly start to deliver.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,123.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start your business unit going.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,128.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Prioritize your production if resources are limited.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,133.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continue you your normal business functions.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,138.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Establish emergency voice communications connectivity<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,143.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Redirect voice and data lines to a temporary location<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,148.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Establish data connectivity<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,153.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Assist telecommunication carrier in redesign of network for recovering facility<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,158.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Coordinate building rewiring<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,163.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Keep your normal business functions going if applicable<BR><BR></P>");
		$pdf->BoldText("<P align='left'>These are all critical functions that must be accomplished if the recovery is to be successful.<BR><BR>Stage 1 - RESPONSE<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Receive Notification calls<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>As the Team Leader and Backup, you will be receiving a call from the ADMT advising of an apparent disaster related event. You should have this recovery plan nearby. Find out the location of the EOC and get ready to go there. However you will first need to do a few more things.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Notify all other IT team members<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Team Leader:<BR><BR></P>");
		
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Employee WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
				
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_096']." Title:  ".$row_InteralForm['contact_097']." Phone:  ".$row_InteralForm['contact_098']." Cell:  ".$row_InteralForm['contact_099']." E-Mail:  ".$row_InteralForm['contact_100']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Supporting Employees:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name:  ".$row_InteralForm['contact_101']." Title:  ".$row_InteralForm['contact_102']." Phone:  ".$row_InteralForm['contact_103']." Cell:  ".$row_InteralForm['contact_104']." E-Mail:  ".$row_InteralForm['contact_105']."<BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name:  ".$row_InteralForm['contact_106']." Title:  ".$row_InteralForm['contact_107']." Phone:  ".$row_InteralForm['contact_108']." Cell:  ".$row_InteralForm['contact_109']." E-Mail:  ".$row_InteralForm['contact_110']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Goto EOC and get settled in<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Arrive at the EOC and receive an update from the DMT. If conditions permit, one member of your team needs to join the DART, which may have already started their preliminary damage assessment. For other team members, the damage assessment will not be back for a period of time. In the interim, it is suggested that you and your team members start to do some preparation and planning. Using your recovery plan, review the recovery process you previously outlined.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 4 - Damage assessment team returns, report to meeting to address the situation<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>After two (2) hours, the DART is required to report back to the DMT. You may be asked to attend the briefing and assist as required.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>STAGE 1 IS NOW AT AN END FOR ALL TEAMS. </P>");
		$pdf->QuestionsBody("<P align='left'>Anything that was not completed during this stage will need to be noted as an outstanding deliverable and carried over to Stage 2.<BR><BR>At this point, EVERYONE enters Stage 2 of their recovery plan and starts the recovery process. To this point, no recovery has begun, just information gathering, assessment and strategic decision-making.<BR><BR>");
		$pdf->BoldText("<P align='left'>Stage 2 - RECOVERY<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Recovering or Establishing Your Work Area <BR><BR>As a team, you will want to meet to discuss the total situation, and the strategy you are going to take to get your business going again. That should be your primary focus, nothing else. When that meeting ends, the entire team will know what the plans are and who will be doing what.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Start with identifying your work location:<BR><BR>Your recovery team’s primary working location will be:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Location Name:___________________________________ <BR>Located at:______________________________<BR><BR>
Phone : ____________________________<BR><BR>
Alternate Number :______________________________<BR><BR>Description of Location :<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Start bringing additional staff in<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Contact all team members not present, and those who work in your business unit, and advise them of:<BR><BR></P>");			
		$pdf->QuestionsBody("<P align='left'>
			1. What the situation is<BR><BR>
				2. What the strategy is<BR><BR>
				3. When they are to report back again<BR><BR>
				4. Where they are to report to<BR><BR>
				5. Any specific instructions<BR><BR></P>");
						
		//creates the page to be used
		$pdf->AddPage();
			
		$pdf->BoldText("<P align='left'>Section 2 - Start Recovery<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Proceed to the area where you will be working. The assumption is that you are starting from nothing. From there, anticipate the process to take between 3 to 7 days to restart your business unit. To that, add days if special items or equipment are required with long lead times.<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,53.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Phone the DMT and request permission to enter the damaged facility, if feasible, in order to retrieve work and personal items.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Identify and phone key customers and/or clients to advise them of the situation and recovery projection.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,73.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Identify and phone key suppliers to advise them of the situation and recovery projection.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Report immediately to the DMT any customer, client or supplier who is critical and may need more attention.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,88.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contact your printer to secure preprinted continuous or sheet forms.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,93.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Coordinate and direct the arrival and placement of incoming items.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,98.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Arrange for installation of incoming items requiring technical assistance.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,103.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Schedule staff to meet the business requirements.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,108.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with the Information Systems Team to reload your LAN or PC from backup tapes they will retrieve from an offsite storage unit.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,118.5);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Determine when you will begin receiving your daily or weekly reports.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Report to the DMT any situations which you determine to be lost or unrecoverable as it pertains to information, either hard copy or on-line.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Communications<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Maintaining accurate and timely communications, both oral and written, is CRITICAL.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Stage 3 - RESUME<BR><BR>Recovery of Operations<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>There will be a point where you feel you can start production again. It may be in a limited capacity for a while, but you are producing.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Recovery & Production<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>In this stage, you are in a transition. That is, you will reach a cross over point where recovery starts to give way to productivity.<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,223.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continue recovery activity.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,228.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Adjust your staff’s schedule to accommodate both business and recovery.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,233.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure your normal business procedures and work flow are in place and functional.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,238.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Implement normal security procedures.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,243.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start performing backups of your PC(s) or LANs as soon as they are up and running.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,248.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Rotate back-ups offsite.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,253.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Coordinate and schedule with your shippers and receivers the movement of mail, product or material.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,263.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Recall critical hard copy records from your offsite storage provider.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,268.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with management and marketing to determine priorities.<BR><BR></P>");
		
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Information WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
			
		$pdf->BoldText("<P align='left'>Section 2 - Establish remaining circuits<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Continue establishing reconnection to the remainder of the circuits not labeled as critical. Using the list you developed from assessing the damage, take the necessary steps to contact the carriers and hardware providers.<BR><BR>Based on the identified problems, you will need to call in the following support:<BR><BR>Supplier 1 Name: ".$row_InteralForm['IT_SUPP01']." Name: ".$row_InteralForm['IT_SUPP02']." Name: ".$row_InteralForm['IT_SUPP03']."<BR><BR>Supplier 2 Name: ".$row_InteralForm['IT_SUPP04']." Name: ".$row_InteralForm['IT_SUPP05']." Name: ".$row_InteralForm['IT_SUPP06']."<BR><BR>Supplier 3 Name: ".$row_InteralForm['IT_SUPP07']." Name: ".$row_InteralForm['IT_SUPP08']." Name: ".$row_InteralForm['IT_SUPP09']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Fully document the extent of damage and prioritize your recovery<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>The extent and severity of the disaster will dictate what the requirements are. Assign one individual to start the identification process of what circuits are active and what circuits need to be restored or re-routed.<BR><BR></P>");
						
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>Section 4 - Establish EOC Communications<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Ensuring that the EOC communications structure is fully functional is your number one priority. At the EOC, meet with the ADMT and CMT to determine what their immediate requirements are:<BR><BR>Requirements:<BR><BR><BR><BR>Circuit From Circuit to<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with your telecommunications carrier to request emergency connectivity<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,88.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Find out what the anticipated connection time is and advise the ADMT<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,93.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contact a wiring company to assist in running cable<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,98.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continue follow up if this is not completed as scheduled<BR><BR><BR></P>");
						
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>Section 5 - Establish priority circuits<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Next you will need to focus on establishing reconnection of circuits labeled as critical. Using the list you developed from assessing the damage, take the necessary steps to contact the carriers and hardware providers. Based on the identified problems, you will need to call in the following support:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Supplier Name:<BR><BR>Equipment or Service<BR>Problem<BR><BR><BR><BR>Supplier Name:<BR><BR>Equipment or Service<BR>Problem<BR><BR><BR><BR>Supplier Name:<BR><BR>Equipment or Service<BR>Problem<BR><BR><BR><BR>At this point:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,168.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start further identification and restoration of known communication problems, and record on the below form<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,178.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Notify telecommunications carrier(s) central office<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,183.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contact equipment providers<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,188.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Assist the IST in network connectivity<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,193.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Address ISDN, T1 circuits<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,198.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Request rerouting of critical 800 numbers<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,203.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Verify that cc:Mail or email is available<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,208.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Verify that the Internet is up and functional<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Supplier Name:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Equipment or Service<BR>Problem<BR><BR><BR><BR>Supplier Name:<BR><BR>Equipment or Service<BR>Problem<BR><BR><BR><BR>Supplier Name:<BR><BR>Equipment or Service<BR>Problem<BR><BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Stage 4 - RESTORE<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Normal Production and Migration Preparation During this stage you are back to full production of your business unit. It is sort of business as usual, only away from “home”. All your normal day-to-day activities are being performed.<BR><BR>The DART is in the final stages of coordinating the restoration of the old facility. You will need to start planning for the move back to that facility.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - normal productivity with plans to move back<BR><BR></P>",225,80,22);
		$pdf->Image('../images/PDFArrowInOrange.gif',15,113.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continue to increase your volume.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,118.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Obtain a copy of the facility plans from DART.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,123.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Lay out your work area on the plan and get a copy to use in setting up your move.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,128.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Obtain, from DART, your group’s sequence of move numbers.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,133.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with DART, and IST to coordinate your recovery requirements with the other teams.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,138.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start planning how you’ll migrate your business back to the facility.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,143.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Schedule a move date.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Paper work catch up<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Due to the emergency nature of the situation, a lot of the work has been done verbally. You will now need to take the time to document these formally and in letter form. Consider the following:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,183.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Write the telecommunications company a request for all circuits installed and send a copy to purchasing<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,193.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Cancel any circuits which were destroyed and not reconnected to avoid billing<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,198.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Generate an order letter for new equipment just installed, verbally on order or which you want to order<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,208.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Cancel maintenance on destroyed equipment and order maintenance on equipment recently installed<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,218.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Provide a list of all destroyed or new equipment to accounting, and be sure to include serial numbers<BR><BR></P>");
						
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>Section 3 - Design network and coordinate installation<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>The Damage Assessment and Reconstruction Team are actively rebuilding the damaged facility. You will need to do the following:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,48.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Determine occupancy date<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,53.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Obtain floor plan<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,58.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Identify voice and data origination points and note on floor plan<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Identify which points are not functional<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,68.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contact carrier<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,73.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contact equipment providers<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contact wiring company for voice and data lines<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Schedule a meeting with ALL of the above groups present<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,88.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Document requirements and provide authorization in written format<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,93.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Assist in installation<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,98.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Provide resources to verify connectivity<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Stage 5 - RELOCATE<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Construction is complete and migration back to rebuilt facility is done. During this stage, you and the other work units will actually be moving back to the rebuilt facility. There are numerous activities that you will need to assist in, and others you will be asked to assist with. About 6 days before the move back date:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - move day is approaching<BR><BR></P>",225,80,22);
		$pdf->Image('../images/PDFArrowInOrange.gif',15,153.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Complete your migration plan and go over it with the other teams so that they understand your requirements<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,163.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Meet with your staff and explain what will be happening over the next few days or weeks<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,168.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Request a walk through of your area about 3 days before your scheduled move<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,173.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive packing material and move numbers<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,178.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Write on the floor plan where each staff member is going, and assign them a unique number<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,183.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Write the number on the plan<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,188.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Provide each staff member that unique number to be used on all items being moved<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,193.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continue business as usual<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Three days before the move<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Walk through the rebuilt facility to:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,223.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that all furniture related items are in place: cabinets, desks, chairs, etc.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,228.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Check for wiring and electricity.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,233.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Verify phones and lines are active.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,238.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Confirm that copiers, faxes and special equipment have been installed.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,243.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Meet with your staff briefly to cover move issues.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,248.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Notify suppliers and shippers of your move date.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,253.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Notify all couriers and the Local Post Office.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,258.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Request that the IST do full volume backups prior to moving any PCs, LANs or mid-range systems.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,268.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that any equipment which needs to be relocated is de-installed and ready for transportation.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Actual move day<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>On move day, you will want to keep your business going as long as possible without disrupting service to your clients or customers.<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,58.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start packing at a predetermined time, preferably mid-Friday afternoon.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Place labels on all items and be sure to include the location number as it corresponds to the number on the floor plan.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,73.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Assign one person to ensure the phones are being answered.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that all of your PCs and LANs are backed up prior to disconnect of the systems.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that phone lines are switched over to the facility where you are moving.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,88.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start move.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,93.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Complete move.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,98.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Check out operations.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,103.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Report any problems.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,108.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start closing down your temporary facility.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,113.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that all services were disconnected.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 4 - Assist in migration back to facility<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Staff will be moving back to the rebuilt facility. You and your team will need to:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,143.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Be at rebuilt facility to assist and document problems.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,148.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Report problems to carrier and equipment providers and ensure their arrival and correction of problems.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,158.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with carrier(s) to reroute circuits as prearranged.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 5 - Paperwork after temporary location is vacated<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>You will now need to formally request the following in letter form:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,188.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Write the telecommunications company a request for all temporary circuits to be disconnected.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,193.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Generate a maintenance letter on new equipment just installed.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,198.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Provide a list of all new equipment to accounting, and be sure to include serial numbers</P>");
	}//end of if
	
	//Business 
	if($intTableIndex == 19)
	{
		$pdf->BoldText("<P align='left'>The Business Recovery Support Team Recovery & Response Plan<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>(BRST) provides services to all critical operations of the institution. They are there to support the Essential Functions Teams (EFTs) and all other recovery teams.<BR><BR>Primary Functions included within this team are:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,58.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Purchasing - Needs to be up and functional first<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Human Resources<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,68.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Payroll - Needs to be up and functional second<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,73.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Accounts Payable<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Accounts Receivable<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Security<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>The Team(s) is identified ahead of time, with the team leader, backup and team members. It is a team that shares common skills and works together to provide the normal corporate support functions required of any typical company. They make the staff happy, appropriate the items you need, process revenue into the business, and ensure prompt payment to creditors. This helps to keep orders flowing. If your company experiences a disaster, they need to be up and functional as soon as feasible.<BR><BR>The size of your business will determine how these functions are grouped, and how big each one is. As such, the Institutional Support Team is grouped together as a single recovery team. In a disaster they will work together as a team to assess the situation, provide assistance in the rebuilding of the facility and continue to support the business. The team is activated by a call from the Administration Team (ADMT).<BR><BR></P>");
		$pdf->BoldText("<P align='left'>The primary functions of this team, in the general order of sequence are:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,168.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Phone additional INSBT members.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,173.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Re-establish or secure a work area to conduct your business.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,178.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Obtain the required resources you need to support the organization.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,183.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with other recovery teams to assist in their efforts.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,188.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contact your suppliers to discuss your company’s situation and secure their commitment.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,193.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start your support functions going again.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,198.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Prioritize your efforts if resources are limited.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,203.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>These are all critical functions that must be accomplished if the recovery is to be successful.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Stage 1 - RESPOND<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Receive notification call<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>As the Team Leader and Backup, you will be receiving a call from the ADMT advising of an apparent disaster related event. You should have this recovery plan nearby. Find out the location of the EOC and get ready to go there. However, you will first need to do a few more things.<BR><BR></P>");
				
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsInteralForm = mysql_query("SELECT * FROM C2Employee WHERE UserID= ".$UserID, $conContinuty) or die(mysql_error());
		$row_InteralForm = mysql_fetch_assoc($rsInteralForm);
		
		$pdf->BoldText("<P align='left'>Section 2 - Notify all other INSBT team members<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Team Leader:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 1 Name: ".$row_InteralForm['contact_111']." Title: ".$row_InteralForm['contact_112']." Phone: ".$row_InteralForm['contact_113']." Cell: ".$row_InteralForm['contact_114']." E-Mail: ".$row_InteralForm['contact_115']."<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Supporting Employees:<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Employee 2 Name: ".$row_InteralForm['contact_116']." Title: ".$row_InteralForm['contact_117']." Phone: ".$row_InteralForm['contact_118']." Cell: ".$row_InteralForm['contact_119']." E-Mail: ".$row_InteralForm['contact_120']."<BR>Employee 3 Name: ".$row_InteralForm['contact_121']." Title: ".$row_InteralForm['contact_122']." Phone: ".$row_InteralForm['contact_123']." Cell: ".$row_InteralForm['contact_124']." E-Mail: ".$row_InteralForm['contact_125']."<BR><BR></P>");
								
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>Section 3 - Goto the EOC and get settled in <BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Arrive at the EOC and receive an update from the CMT.<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,43.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>For all DMT members, the damage assessment will not be back for a period of time.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,48.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start to do some preparation and planning<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,53.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Use your recovery plan and review the recovery process you previously outlined<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 4 - Damage Assessment Team returns<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>After two (2) hours, the DART is required to report back to the DMT. Your participation in this process will probably be requested Provide your assessment of the situation and your concerns about the business. Attend the general meeting hosted by the Disaster management team and provide input and observations. <BR><BR></P>");
		$pdf->BoldText("<P align='left'>STAGE 1 IS NOW AT AN END FOR ALL TEAMS. </P>");
		$pdf->QuestionsBody("<P align='left'>Any thing that was not completed during this stage will need to be noted as an outstanding deliverable and carried over to Stage 2.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Stage 2 - RECOVERY<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Recovering or Establishing Your Work Area As a team, you will want to meet to discuss the total situation, and the strategy you are going to take to get your support functions going again. That should be your primary focus, nothing else. When that meeting ends, the entire team will know what the plans are and who will be doing what. Start with identifying your work location:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Your recovery team’s primary working location will be:<BR><BR>Location Name:_______________________________ <BR>Located at :______________________________________<BR>Phone : ____________________________<BR>Alternate Number : ____________________________<BR><BR>Description of Location :<BR><BR><BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Start brining in additional staff<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Contact all team members not present, and those who work in your business unit, and advise them of:<BR><BR>
				1. What the situation is<BR>
				2. What the strategy is<BR>
				3. When they are to report back again<BR>
				4. Where they are to report to<BR>
				5. Any specific instructions<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Start recovery<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Proceed to the area where you will be working. The assumption is that you are starting from nothing. From there, anticipate the process to take between 3 to 7 days to restart your business unit. To that, add days if special items or equipment is required which require long lead times.<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,38.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Phone the DMT and request permission to enter the damaged facility, if feasible, to retrieve work and personal items.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,48.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Make a list of all items, that you need to acquire:<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,53.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with the DART team to acquire furniture<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,58.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with the DART team to acquire fax machines, copiers, mailers, etc.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Identify and order supply items which will be needed<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,68.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Order from the printer any custom forms you use in your business<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,73.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Obtain the required PCs necessary to support your combined activities.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,78.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Forward the requirements to the DMT for approval and acquisition.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Recall from offsite storage all critical records.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,88.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Identify and phone key customers and/or clients to advise them of the situation and recovery projection.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,98.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Identify and phone key suppliers to advise them of the situation and recovery projection.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,103.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Report immediately to the DMT any supplier who is critical and may need more attention.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,108.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Coordinate and direct the arrival and placement of incoming office related items.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,113.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Arrange for installation of incoming items requiring technical assistance.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,118.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with the Information Systems Team to reload your LAN or PC from backup tapes they will retrieve from an offsite storage unit.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,128.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Report to the DMT any situations which you determine to be lost or unrecoverable as it pertains to information, either hard copy or on-line.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,138.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Schedule staff to meet the business requirements.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,143.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Contact temporary agency for assistance if required.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Communications<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Maintaining accurate and timely communications, both oral and written, is CRITICAL. This will be repeated for each and every stage, for incoming calls and out going calls, and for relaying messages. If you have any clue that there are not enough phones now, contact the Essential Functions Team immediately and have them place an emergency order for lines and instruments (telephones).<BR><BR></P>");
										
		//creates the page to be used
		$pdf->AddPage();
		
		$pdf->BoldText("<P align='left'>Stage 3 - RESUME<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>Recovery and Starting Operations there will be a point where you feel you can start production again. It may be in a limited capacity for a while, but you are producing.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Recovery and Production<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>In this stage, you are in a transition. That is, you will reach a cross over point where recovery starts to give way to productivity.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Human Resource and Payroll<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Just because you have experienced a disaster, Human Resources don’t cease to function. In fact, in a time of adversity, their presence and support to employees’ needs becomes even more prevalent. Some of the immediate issues at hand, which need to be addressed, are:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,103.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Coordinate benefits<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,108.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Monitor 401k<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,113.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Process and make available medical forms<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,118.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Handle terminations<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,123.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Perform job search<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,128.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continue hiring as required<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,133.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Conduct orientation<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,138.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Collect and verify time sheets<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,143.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Calculate and record hours<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,148.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Record vacations<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,153.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Record sick leave<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,158.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Submit payroll<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,163.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive and distribute payroll checks<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,168.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Handle W2’s and W4’s<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,173.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>File taxes<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,178.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Process performance appraisals<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,183.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Process salary increases<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,188.3);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Process promotion<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Accounts Payable<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>This is another area that must be brought up to speed soon after the disaster. Purchasing is required to order 10 fold the number of items as usual. The suppliers have heard about your company and may be concerned about payment. You need to get the checks out to the vendors and suppliers.<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,233.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive invoices<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,238.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Log invoices<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,243.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Match invoices against closed purchase orders<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,248.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Generate requests to cut checks<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,253.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive checks and match to invoices<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,258.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Mail out or hold for pickup<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 4 - Accounts Receivable<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Cash flow becomes critical at this point. The accounts payable is writing checks as fast as they can. The DMT will be visiting the bank and will be working with the insurance adjusters to obtain relief. <BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,38.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive shipping documents, new policies, statement of billable work performed, etc.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,43.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Enter information into the system<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,48.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Generate and mail invoices<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,53.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive payments<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,58.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Generate deposits<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 5 - Security<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>Your company had previously established, implemented and enforced certain security procedures that were predicated on business needs. During the recovery process, these procedures cannot be compromised and need to be tightened up quickly, back to the original level which was in place prior to the disaster.<BR><BR></P>");
		$pdf->BoldText("<P align='left'>To do that you will need to:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,113.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that physical security to the premise(s) is in place<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,118.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Verify that sensitive or confidential records are secured in appropriate storage locations<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 6 - Plan to move back<BR><BR></P>",225,80,22);
		$pdf->Image('../images/PDFArrowInOrange.gif',15,138.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continue to increase your volume<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,143.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Obtain a copy of the facility plans from the DART Team<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,148.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Lay out your work area on the plan and get a copy to use in setting up your move<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,153.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Obtain, from DART, your group’s sequence of move numbers<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,158.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Work with DART, VADT and IST to coordinate your recovery requirements<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,163.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start planning how to migrate your business back to the rebuilt facility<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,168.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Schedule a move date<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Stage 5 - RELOCATE<BR><BR></P>");
		$pdf->QuestionsBody("<P align='left'>During this stage, you and the other work units will actually be moving back to the rebuilt facility. There are numerous activities that you will need to assist in, and others you will be asked to assist with. About 6 days before the move back:<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 1 - Move time is approaching<BR><BR></P>",225,80,22);
		$pdf->Image('../images/PDFArrowInOrange.gif',15,218.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Complete your migration plan and go over it with the other teams so that they understand your requirements<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,228.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Meet with your staff and explain what will be happening over the next few days or weeks<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,233.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Request a walk through of your area about 3 days before your scheduled move<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,238.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Receive packing material and move numbers<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,243.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Write on the floor plan where each staff member is going, and assign them a unique number<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,248.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Write the number on the plan<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,253.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Provide each staff member that unique number to be used on all items being moved<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,258.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Continue business as usual<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 2 - Three days before the move<BR><BR></P>",225,80,22);
		$pdf->BoldText("<P align='left'>Walk through the rebuilt facility to:<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,33.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that all furniture related items are in place: cabinets, desks, chairs, etc.<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,38.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Check for wiring and electricity<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,43.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Verify phones and lines are active<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,48.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Confirm that copiers, fax’s and special equipment have been installed<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,53.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Meet with your staff briefly to cover move issues<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,58.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Notify suppliers and shippers of your move date<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,63.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that the ADMT has notified all couriers and the local Post Office<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,68.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Verify with the EST that your telephone numbers will be rolled over on the day of the move<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,73.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Request that the IT do full volume backups prior to moving any PCs, LANs or mid-range systems<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,83.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that any equipment which needs to be relocated is de-installed and ready for transportation<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,93.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Conduct an actual walk through of the rebuilt facility<BR><BR></P>");
		$pdf->BoldText("<P align='left'>Section 3 - Actual move day<BR><BR></P>",225,80,22);
		$pdf->QuestionsBody("<P align='left'>On move day, you will want to keep your business going as long as possible without disrupting service to your clients or customers.<BR><BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,128.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start packing at a predetermined time, preferably mid-Friday afternoon<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,133.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Place labels on all items and be sure you include the location number as it corresponds to the number on the floor plan<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,143.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Assign one person to ensure the phones are being answered<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,148.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that all of your PCs and LANs are backed up prior to disconnect of the systems<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,153.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that phone lines are switched over to the facility you are moving to<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,158.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start move<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,163.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Complete move<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,168.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Check out operations<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,173.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Report any problems<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,178.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Start closing down your temporary facility<BR></P>");
		$pdf->Image('../images/PDFArrowInOrange.gif',15,183.1);
		$pdf->SetX(20);
		$pdf->QuestionsBody("<P align='left'>Ensure that all services were disconnected</P>");
	}//end of if
	
	//Disaster Response Plans Disaster Report Form
	if($arrTablesName[$intTableIndex] == "EXServiceMaster")
	{				
		$pdf->Image('../images/ServiceMasterClean.jpg');
		$pdf->Image('../images/ServiceMaster.jpg',140, 15);
		$pdf->BoldText("<P align='left'><BR><BR><BR>Call 1-800 RESPOND </P>",185,13,37);
		$pdf->QuestionsBody("<P align='left'>(7663)<BR></P>");
		$pdf->BoldText("<P align='left'>If you have experienced a Disaster Please take the time to complete this Disaster Report form so our
ServiceMaster Response team can serve you as accurately as possible.<BR><BR></P>",71,143,191);
		$pdf->QuestionsBody("<P align='left'>Business Name<BR><BR>Contact/Title<BR><BR>Address<BR><BR>City/Province/Postal<BR><BR>E-Mail Address<BR><BR>Phone<BR><BR></P>");
		$pdf->SetXY(110,90);
		$pdf->QuestionsBody("<P align='left'>What Happened?<BR><BR></P>");
		$pdf->SetXY(110,100);
		$pdf->QuestionsBody("<P align='left'>FIRE<BR><BR></P>");
		$pdf->SetXY(110,110);
		$pdf->QuestionsBody("<P align='left'>FLOOD<BR><BR></P>");
		$pdf->SetXY(110,120);
		$pdf->QuestionsBody("<P align='left'>VANDALISM<BR><BR></P>");
		$pdf->SetXY(110,130);
		$pdf->QuestionsBody("<P align='left'>GAS LEAK<BR><BR></P>");
		$pdf->SetXY(110,140);
		$pdf->QuestionsBody("<P align='left'>OTHER:</P>");
		//$pdf->SetXY(4,0);		
		$pdf->BoldText("<P align='left'><BR><BR>Where Did it happen?<BR><BR><BR><BR>What Type of Damage has occurred?<BR><BR><BR><BR>Is your buildling accessable?<BR><BR><BR><BR>What is the square footage of your building?<BR><BR><BR><BR>Which area of your building was affected?<BR><BR></P>");				
		$pdf->QuestionsSmallBody("<BR><BR><P align='left'>In order to Respond Efficiently & Effectively we will need to know the following:
Please Have this Document ready for the arriving Servicemaster team. If possible please fax a copy of this report to our Disaster Response Location at 800-737-7663</P>");
	}//end of if
	
	//Insurance Inventory
	if($arrTablesName[$intTableIndex] == "C2InsuranceInventory" && $row_loginFoundUser['Solution'] == 2)
	{
		//does another selection to get the updated data
		mysql_select_db($database_conContinuty, $conContinuty);
		$rsForm2 = mysql_query("SELECT * FROM c2insuranceinventory2 WHERE C2ID= ".$row_rsForm['C2ID'], $conContinuty) or die(mysql_error());
		$row_rsForm2 = mysql_fetch_assoc($rsForm2);
		$totalRows_rsForm2 = mysql_num_rows($rsForm2);
		
		$arrYesNo = array(1 =>"YES","NO");
		$intFileImageIndex = 1;//holds the count of the fileImage on the page in order to give a unqie file name for each file
		
		//updates the title and the Sub Section	
		$strAreaName = $arrAreaName[$intSectionArea].$intSectionTableIndex;
		$strSectionName = "";
				
		//adds one to the Section Table Index
		$intSectionTableIndex = $intSectionTableIndex + 1;
		
		//creates the page to be used
		$pdf->AddPage();							
		
		//displays the Name of the Page for this Title Page
		$pdf->PageTitlePage("<P align='center'>".$row_rsPlans['sectionName']."</P>");
					
		//sets the Form Name
		//$pdf->FormName($row_rsPlans['sectionName']."<BR><BR></P>");
		
		//updates the Sub Section		
		$strSectionName = $row_rsPlans['sectionName'];
			
		//creates the page to be used
		$pdf->AddPage();
				
		$pdf->BoldText("<P align='left'>The following is a photographic representation of your office building and premesis. You have previously
taken the time to photograph and provide a breif description of each area of your business. in the event of a loss you can use this Inventory to help with the recovery and replacement process.<BR><BR>Included in this Insurance Inventory are the followin areas:<BR><BR>
				Area 1: EXTERIOR OF OFFICE<BR>
				Area 2: FRONT ENTERANCE<BR>
				Area 3: OFFICE KITCHEN AREA<BR>
				Area 4: BOARD ROOM<BR>
				Area 5: OFFICE # 1<BR>
				Area 6: OFFICE # 2<BR>
				Area 7: OFFICE # 3<BR>
				Area 8: OFFICE # 4<BR>
				Area 9: OFFICE # 5<BR>
				Area 10: OFFICE # 6<BR>
				Area 11: OFFICE # 7<BR>
				Area 12: OFFICE # 8<BR>
				Area 13: OFFICE # 9<BR>
				Area 14: OFFICE # 10<BR>
				Area 15: CUBICLE AREA # 1<BR>
				Area 16: CUBICLE AREA # 2<BR>
				Area 17: CUBICLE AREA # 3<BR>
				Area 18: CUBICLE AREA # 4<BR>
				Area 19: CUBICLE AREA # 5<BR>
				Area 20: INFORMATION TECHNOLOGY ROOM<BR>
				Area 21: SUPPLY ROOM<BR>
				Area 22: ARTWORK/COLLECTABLIES<BR>
				Area 23: MISC. ROOM<BR>
				Area 24: ADDITIONAL ROOM
<BR><BR>EACH AREA WILL INCLUDE MUTIPULE PHOTO’S AND DESCRPTIONS OF EACH AREA.<BR><BR></P>");
										
		//creates the page to be used
		$pdf->AddPage();
		
		$arrPicArea = array(1 =>"Exterior of Office","Front Enterance/Reception","Office Kitchen Area","Board Room/War Room","Office #1","Office #2","Office #3","Office #4","Office #5","Office #6","Office #7","Office #8","Office #9","Office #10","Cubicle Area #1","Cubicle Area #2","Cubicle Area #3","Computer Room","IT Room/Printer/Fax","Supply/Main. Room","Artwork/Collectables","Misc. Room","Other Room");
		$arrPicField = array(1 =>"Name: ","Catagory: ","Room: ","Purchase Date: ","Receipt: ","Make: ","Model: ","Place Purchased: ","Serial #: ","Estimated Purchase Price: ");
		$intIIFieldIndex = 71;//contorls which field is being used
		$intPicIndex = 1;//contorls which picture to use
		$strFieldName = "0";//holds the field name so that can put 00 for 1 digit and 0 for two digit
		
		//goes around for creates for each of the section of pictures the user has done
		for($intIIIndex = 1;$intIIIndex <= count($arrPicArea);$intIIIndex++)
		{
			$intPicY = 10;//holds the location of the Picture in the Y Axes
		
			$pdf->QuestionsBody("<P align='left'>The following is a photographic representation of your office building and premesis. You have previously taken the time to photograph and provide a breif description of each area of your business. in the event of a loss you can use this Inventory to help with the recovery and replacement process.<BR><BR></P>");
			$pdf->BoldText("<P align='left'>SECTION ".$intIIIndex." of 23				".$arrPicArea[$intIIIndex]."<BR><BR></P>");
			
			//goes around for each photo for this area of ii
			for($intPhotoIndex = 1;$intPhotoIndex <= 4;$intPhotoIndex++)
			{
				/*if($intPicIndex <= 12)
					$pdf->Image("../images/".$UserID."/".$UserID."".$intPicIndex.".jpg",135,50 + $intPicY,50);*/
				
				$pdf->BoldText("<P align='left'>".$arrPicArea[$intIIIndex]." - PHOTO # ".$intPhotoIndex."<BR><BR></P>",71,143,191);
				
				//goes around adding each field to descripted the Picture
				for($intPhotoFieldIndex = 1;$intPhotoFieldIndex <= count($arrPicField);$intPhotoFieldIndex++)
				{
					//checks if the $intPhotoFieldIndex is 5 meaning that is it the Receipt which uses yes and no and not test
					if($intPhotoFieldIndex == 5)
					{
						//checks which rsform to use rsform2 for above 800 and rsform fro the blow 800
						if($intIIFieldIndex >= 800)
							$pdf->BoldText("<P align='left'>".$arrPicField[$intPhotoFieldIndex].$arrYesNo[$row_rsForm2["ii_".$strFieldName.$intIIFieldIndex]]."</P>");
						else
							$pdf->BoldText("<P align='left'>".$arrPicField[$intPhotoFieldIndex].$arrYesNo[$row_rsForm["ii_".$strFieldName.$intIIFieldIndex]]."</P>");
					}//end of if
					else
					{
						//checks which rsform to use rsform2 for above 800 and rsform fro the blow 800
						if($intIIFieldIndex >= 800)
							$pdf->BoldText("<P align='left'>".$arrPicField[$intPhotoFieldIndex].$row_rsForm2["ii_".$strFieldName.$intIIFieldIndex]."</P> ");						
						else
							$pdf->BoldText("<P align='left'>".$arrPicField[$intPhotoFieldIndex].$row_rsForm["ii_".$strFieldName.$intIIFieldIndex]."</P> ");
					}//end of else
				
					//checks if the $intPhotoFieldIndex is 3,5,7 or 9 for the last field as it is far from teh rest of the fields
					if($intPhotoFieldIndex == 3 || $intPhotoFieldIndex == 5 || $intPhotoFieldIndex == 7)
						$pdf->QuestionsBody("<BR>");
					else if($intPhotoFieldIndex == 9)
						$pdf->QuestionsBody("<BR><BR>");
						
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

				//New line to sperpates the the Photos				
				$pdf->QuestionsBody("<BR><BR>");
									
				//adds one to the Pic Index
				$intPicIndex = $intPicIndex + 1;
				
				//adds 10 more so that the pic can move down
				$intPicY = $intPicY + 45;
			}//end of for loop
		
			//creates the page to be used
			$pdf->AddPage();
		}//end of for loop
		
		$pdf->BoldText("<P align='left'>This is Insurance Inventory is a basic representation and should not be used as the only reference.<BR>
Multiple copies of this inventory should be produced and stored at different locations.
		<BR><BR><BR>
In order the obtain the most effectiveness from this Insurance Inventory please ensure that you photo’s and details are up-to date, and updated as change occurs at your place of business.
		<BR><BR><BR><BR>
Continuity Inc. is not responsible for any loss of damage to your business and is not liable for the operations and effeteness of this inventory. Continuity Inc. agrees to store and make these plans accessible to their customers.
		<BR><BR><BR>
If you are unable to produce a copy of your Inventory please contact Continuity Inc. and we will have one sent to you, or any other their party at your expense.<BR><BR></P>");
	}//end of if
	
	//adds one to $intTableIndex
    $intTableIndex = $intTableIndex + 1;
}//end of while

//updates the title and both parrs of the section
$strAreaName = "Glossary";
$strSectionSideName = "Glossary";
$strSectionName = "";

//sets the frist section
$intSectionArea = 5;

//creates the page to be used
$pdf->AddPage();

//dispays the Section Title in the Page Title
$pdf->PageTitlePage("<P align='center'>Business Continuity &<BR><BR> Disaster Recovery<BR><BR>Glossary</P>");

//updates the Sub Section		
$strSectionName = "Business Continuity - Glossary";
$strAreaName = "Terms & Definitions";

//sets the Form Name
//$pdf->FormName("<P ALIGN='left'>".$row_rsPlans['sectionName']."<BR><BR></P>");

//goes around creating the 5 section with the colors and names Title Page for the right side
for($intForIndex = 1;$intForIndex < 16;$intForIndex++)
{
	//creates the page to be used
	$pdf->AddPage();

	//the images of the Glossarry
	$pdf->Image("../images/PDFGlossary/Glossary".$intForIndex.".jpg",0,22,200);
}//end of for loop

$pdf->Output(); ?>