//Adds text to any part of the body of a HTML
function addNode(tagParent,strText,boolAddToBack, boolRemoveNode)
{
  var strNode = document.createTextNode(strText);//holds the test which will be added
     
  //gets the properties of the node
  tagParent = getDocID(tagParent);
  
  //checks if the user whats to replace the node in order to start with a clean slate
  //it also checks if there is a chode node to replace
  if (boolRemoveNode == true && tagParent.childNodes.length > 0)
	//replaces the current node with what the user wants
	tagParent.replaceChild(strNode,tagParent.childNodes[0]);
  else
  {
  	//checks if the user whats to added to the back of the id or the front
  	if(boolAddToBack == true)
		tagParent.appendChild(strNode);
  	else
		//This is a built-in function of Javascript will add text to the beginning of the child
  		insertBefore(strNode,tagParent.firstChild);
  }//end of if else
  
  //returns the divParent in order for the user to use it for more uses
  return tagParent;
}//end of addNode()

//does the display the a message in a on the page weather then an alert
function displayMessage(tagMessage,strMessText,boolAddToBack, boolRemoveNode)
{
	//gets the message properties and sets the text furthermore it does the display
	tagMessage = addNode(tagMessage,strMessText,boolAddToBack, boolRemoveNode);
	tagMessage.style.display = "block";	
	
	return tagMessage;
}//end of displayMessage()

//changes the image of tagImage to what is in strImageSrc
function changeImage(tagImage,strImageSrc)
{
	//gets the properties of tagImage
	tagImage = getDocID(tagImage);
	
	//checks if there is a properties
	if(tagImage != null)
		tagImage.src = strImageSrc;
}//end of changeImage()

//removes from view all tags in tagContainer with the expection of tagActive
//It assumes the tagActive and tagContiner already have the properties
function classToggleLayer(tagContainer,tagActive,strClassName,strTAGName)
{
	var arrTAG = tagContainer.getElementsByTagName(strTAGName);//holds all strTAGName in tagContainer
	
	//goes around the for each tag that getElementsByTagName found in tagContainter
	for(var intIndex = arrTAG.length - 1; intIndex > -1 ; intIndex--)
	{
		//checks if the class name is the same as strClassName and it is not active if it is active then change the dispaly to block
		if(arrTAG[intIndex].className == strClassName && arrTAG[intIndex].id != tagActive.id)
			arrTAG[intIndex].style.display = arrTAG[intIndex].style.display? "":"";
		else if(arrTAG[intIndex].id == tagActive.id && tagActive.style.display == "")
			arrTAG[intIndex].style.display = arrTAG[intIndex].style.display? "":"block";
	}//end of for loop
}//end of classToggleLayer()

//gets the document properties in order to use them as there are many types of browers with different versions
function getDocID(tagLayer)
{
	var tagProp = "";//holds the proerties of tagLayer

	//gets the whichLayer Properties depending of the differnt bowers the user is using
	if (document.getElementById)//this is the way the standards work
		tagProp = document.getElementById(tagLayer);
	else if (document.all)//this is the way old msie versions work
		tagProp = document.all[tagLayer];
	else if (document.layers)//this is the way nn4 works
		tagProp = document.layers[tagLayer];
		
	return tagProp;
}//end of getDocID()

//starts up the page
function startUp()
{
	var oldonload=window.onload;//holds any prevs onload function from the js file

	//gets the onload window event checks if there is a function that is already in there
	window.onload=function(){
		if(typeof(oldonload)=='function')
			oldonload();
											
		//finds which browser the user is using for Firefox it is the defult
		//for IE 
		if (navigator.userAgent.indexOf('MSIE') != -1)
		{									
			var tagBasicLogInHidden = getDocID('divBasicLogInHidden');//holds LogIn Hidden
			
			tagBasicLogInHidden.style.marginRight = "215px";
			tagBasicLogInHidden.style.right = "-1px";
		}//end of if
		//for Safari and Mac
		else if (navigator.userAgent.indexOf('Safari') !=-1 || navigator.platform.indexOf("Mac") > -1)
		{
			var tagBasicLogInHidden = getDocID('divBasicLogInHidden');//holds LogIn Hidden
			
			tagBasicLogInHidden.style.marginRight = "10px";
			tagBasicLogInHidden.style.right = "-1px";
		}//end of if else
		//for firefox
		else if (navigator.userAgent.indexOf('Firefox') !=-1)
		{
		}//end of if else
		//for Opera
		else if (navigator.userAgent.indexOf('Opera') !=-1)
		{
		}//end of if else		
	}//end of window.onload=function()
}//end of startUp()

//shoes and hides a <div> using display:block/none from the CSS
function toggleLayer(tagLayer,tagGrayOut)
{
	var tagStyle = '';//holds the style of tagLayer

	//gets the tagLayer and tagGrayOut Properties
	tagStyle = getDocID(tagLayer);
	tagGrayOut = getDocID(tagGrayOut);
	
	if (tagStyle != null){tagStyle.style.display = tagStyle.style.display? "":"block";}
	if (tagGrayOut != null)
	{
		tagGrayOut.style.display = tagGrayOut.style.display? "":"block";

		//for IE
		if (navigator.userAgent.indexOf('MSIE') != -1)
		{
			tagGrayOut.attachEvent('onclick',function () {
				toggleLayer(tagStyle.id,tagGrayOut.id)
			});
		}//end of if
		//for the other browsers
		else
		{
			tagGrayOut.addEventListener('click',function () {
				toggleLayer(tagStyle.id,tagGrayOut.id);
			},false);
		}//end of else
	}//end of if
}//end of toggleLayer()