/*  By Breil Andreas, Germany.
	Web  : http://www.myjavaserver.com/~radion
	Email: breil-andreas@mail.ru
-----------------------------------------------------------------------------------
- function's:																	  -
-	create_PopUp(Name[string], Title[string], src[string], x[int],				  -
-				 y[int], w[int], h[int], BorderColor[#0000FF], BGColor[#FFFFFF]); -
-	remove_PopUp(Name[string]);													  -
-	move_absolut_PopUp(Name[string], x[int], y[int]);							  -
-	move_incremental_PopUp(Name[string], x_step[int], y_step[int]);				  -
-----------------------------------------------------------------------------------
*/
document.write('<style type="text/css">');
document.write('.dragclass { position : relative; cursor : move; font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #FFFFFF; font-size: small; }');
document.write('.dragclass_02 { font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #FFFFFF;font-size: small; padding-top: 2px; padding-right: 0px; }');
document.write('</style>');


var __Name = "", __x,__y,__w,__h;

function create_PopUp(Name, Title, html, x, y, w, h, BorderColor, BGColor){
 __x = x; __y = y; __w = w; __h = h;
 if(null == document.getElementById(Name + 'img_02')){
 __Name = Name
// create LeftLayer
 if (document.createElement && (div_01 = document.createElement('div'))){
  div_01.className = "dragclass";
  div_01.zIndex = "100";
  div_01.name = div_01.id = Name + "LeftLayer";
  div_01.style.position = "absolute";
  div_01.style.left = x + "px";
  div_01.style.top = y + "px";
  div_01.style.width = w - 38 + "px";
  div_01.style.height = 17 + "px";
  div_01.style.backgroundColor = BorderColor;
  div_01.style.visibility = "visible";
  div_01.style.border = "1px solid " + BorderColor;
  document.body.appendChild(div_01);
  }

// insert Title Text in to LeftLayer
  document.getElementById(Name + 'LeftLayer').innerHTML = '&nbsp;' + Title;

// create RightLayer
 if (document.createElement && (div_02 = document.createElement('div'))){
  div_02.className = "dragclass_02";
  div_02.zIndex = "100";
  div_02.name = div_02.id = Name + "RightLayer";
  div_02.style.position = "absolute";
  div_02.style.left = x + w - 38 + "px";
  div_02.style.top = y + "px";
  div_02.style.width = 38 + "px";
  div_02.style.height = 17 + "px";
  div_02.style.backgroundColor = BorderColor;
  div_02.style.visibility = "visible";
  div_02.style.border = "1px solid " + BorderColor;
  document.body.appendChild(div_02);
  }

// insert images in to RightLayer
//  document.getElementById(Name + 'RightLayer').innerHTML = '<img id="' + Name + 'img_01" name="' + Name + 'img_01" src="minimize.gif" width="16" height="14" onclick="alert(this.id);">&nbsp;<img id="' + Name + 'img_02" name="' + Name + 'img_02" src="close.gif" width="16" height="14" onclick="remove_PopUp(this.name.substring(0,(this.name.length-6)));">';
  document.getElementById(Name + 'RightLayer').innerHTML = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img id="' + Name + 'img_02" name="' + Name + 'img_02" src="/images/close.gif" style="cursor:pointer;" onclick="remove_PopUp(this.name.substring(0,(this.name.length-6)));">';

// create BottomLayer
 if (document.createElement && (div_03 = document.createElement('div'))){
  div_03.className = "";
  div_03.zIndex = "100";
  div_03.name = div_03.id = Name + "BottomLayer";
  div_03.style.position = "absolute";
  div_03.style.left = x + "px";
  div_03.style.top = y + 19 + "px";
  div_03.style.width = w + "px";
  div_03.style.height = h + "px";
  div_03.style.backgroundColor = BGColor;
  div_03.style.visibility = "visible";
  div_03.style.border = "1px solid " + BorderColor;
  document.body.appendChild(div_03);
  }

// insert iframe in to BottomLayer
 document.getElementById(Name + "BottomLayer").innerHTML = html;
}
}//end of create_PopUp


function move_absolut_PopUp(Name, x, y) {
 if(((null != document.getElementById(Name + 'LeftLayer')) && (null != document.getElementById(Name + 'RightLayer')) && (null != document.getElementById(Name + 'BottomLayer')))) {
  document.getElementById(Name + 'LeftLayer').style.left = x + "px";
  document.getElementById(Name + 'LeftLayer').style.top = y + "px";
  document.getElementById(Name + 'RightLayer').style.left = x + __w - 38 + "px";
  document.getElementById(Name + 'RightLayer').style.top = y + "px";
  document.getElementById(Name + 'BottomLayer').style.left = x + "px";
  document.getElementById(Name + 'BottomLayer').style.top = y + 19 + "px";
 }
}//end of move_absolut_PopUp


function move_incremental_PopUp(Name, x_step, y_step) {
 if(((null != document.getElementById(Name + 'LeftLayer')) && (null != document.getElementById(Name + 'RightLayer')) && (null != document.getElementById(Name + 'BottomLayer')))) {
  	 document.getElementById(Name + 'LeftLayer').style.left = parseInt(document.getElementById(Name + 'LeftLayer').style.left.substring(0, (document.getElementById(Name + 'LeftLayer').style.left.length - 2))) + x_step + "px";
 	 document.getElementById(Name + 'LeftLayer').style.top = parseInt(document.getElementById(Name + 'LeftLayer').style.top.substring(0, (document.getElementById(Name + 'LeftLayer').style.top.length - 2))) + y_step + "px";
 	 document.getElementById(Name + 'RightLayer').style.left = parseInt(document.getElementById(Name + 'RightLayer').style.left.substring(0, (document.getElementById(Name + 'RightLayer').style.left.length - 2))) + x_step + "px";
 	 document.getElementById(Name + 'RightLayer').style.top = parseInt(document.getElementById(Name + 'RightLayer').style.top.substring(0, (document.getElementById(Name + 'RightLayer').style.top.length - 2))) + y_step + "px";
 	 document.getElementById(Name + 'BottomLayer').style.left = parseInt(document.getElementById(Name + 'BottomLayer').style.left.substring(0, (document.getElementById(Name + 'BottomLayer').style.left.length - 2))) + x_step + "px";
 	 document.getElementById(Name + 'BottomLayer').style.top = parseInt(document.getElementById(Name + 'BottomLayer').style.top.substring(0, (document.getElementById(Name + 'BottomLayer').style.top.length - 2))) + y_step + "px";
	}
}//end of move_incremental_PopUp


function remove_PopUp(Name){
 if(null != document.getElementById(Name + 'LeftLayer'))   document.getElementById(Name + 'LeftLayer').parentNode.removeChild(document.getElementById(Name + 'LeftLayer'));
 if(null != document.getElementById(Name + 'img_01'))      document.getElementById(Name + 'img_01').parentNode.removeChild(document.getElementById(Name + 'img_01'));
 if(null != document.getElementById(Name + 'img_02'))      document.getElementById(Name + 'img_02').parentNode.removeChild(document.getElementById(Name + 'img_02'));
 if(null != document.getElementById(Name + 'RightLayer'))  document.getElementById(Name + 'RightLayer').parentNode.removeChild(document.getElementById(Name + 'RightLayer'));
 if(null != document.getElementById(Name + 'iFrame'))      document.getElementById(Name + 'iFrame').parentNode.removeChild(document.getElementById(Name + 'iFrame'));
 if(null != document.getElementById(Name + 'BottomLayer')) document.getElementById(Name + 'BottomLayer').parentNode.removeChild(document.getElementById(Name + 'BottomLayer'));
}//end of remove_PopUp


// Begin mouse movement
if  (document.getElementById){

(function(){

var n = 500;
var dragok = false;
var y,x,d,dy,dx;

function move(e){
if (!e) e = window.event;
 if (dragok){
  d.style.left = dx + e.clientX - x + "px";
  document.getElementById(__Name + 'BottomLayer').style.left = dx + e.clientX - x + "px";
  document.getElementById(__Name + 'RightLayer').style.left = dx + e.clientX - x + __w - 38 + "px";
  
  d.style.top = dy + e.clientY - y + "px";
  document.getElementById(__Name + 'BottomLayer').style.top = dy + e.clientY - y + 19 + "px";
  document.getElementById(__Name + 'RightLayer').style.top = dy + e.clientY - y + "px";
  return false;
 }
}

function down(e){
if (!e) e = window.event;
var temp = (typeof e.target != "undefined")?e.target:e.srcElement;
if (temp.tagName != "HTML"|"BODY" && temp.className != "dragclass"){
 temp = (typeof temp.parentNode != "undefined")?temp.parentNode:temp.parentElement;
 }
if (temp.className == "dragclass"){
 dragok = true;
 temp.style.zIndex = n++;
 d = temp;
 dx = parseInt(temp.style.left+0);
 dy = parseInt(temp.style.top+0);
 x = e.clientX;
 y = e.clientY;
 document.onmousemove = move;
 return false;
 }
}

function up(){
dragok = false;
document.onmousemove = null;
}

document.onmousedown = down;
document.onmouseup = up;

})();
}// The End mouse movement
