// JavaScript Document
	function ConfirmDelete()
	{
		return confirm("Удалить запись?");
	}
	function RefreshFrame(id)
	{
		var tf = document.getElementById(id).contentWindow;
		s = tf.location;
		var r = RegExp("#", "i");
		var t = r.exec(s);
		tf.location = (t==null)?s:RegExp.leftContext;
		return false;
	}
	function SetFrameURL(id,url)
	{
		var tf = document.getElementById(id);
		tf.src = url;
		//dyniframesize([id]);
		return false;
	}
/***********************************************
* IFrame SSI script II- © Dynamic Drive DHTML code library (http://www.dynamicdrive.com)
* Visit DynamicDrive.com for hundreds of original DHTML scripts
* This notice must stay intact for legal use
***********************************************/

//Input the IDs of the IFRAMES you wish to dynamically resize to match its content height:
//Separate each ID with a comma. Examples: ["myframe1", "myframe2"] or ["myframe"] or [] for none:
var iframeids=[];

//Should script hide iframe from browsers that don't support this script (non IE5+/NS6+ browsers. Recommended):
var iframehide="yes";

var getFFVersion=navigator.userAgent.substring(navigator.userAgent.indexOf("Firefox")).split("/")[1];
var FFextraHeight=parseFloat(getFFVersion)>=0.1? 16 : 0 //extra height in px to add to iframe in FireFox 1.0+ browsers
function RegisterIFrameAutosize(id)
{
    iframeids.push(id);
}

function resizeCaller() 
{
    var dyniframe=new Array();
    for (i=0; i<iframeids.length; i++)
    {
        if (document.getElementById)
            resizeIframe(iframeids[i]);
        //reveal iframe for lower end browsers? (see var above):
        if ((document.all || document.getElementById) && iframehide=="no")
        {
            var tempobj=document.all? document.all[iframeids[i]] : document.getElementById(iframeids[i]);
            tempobj.style.display="block";
        }
    }
}

function resizeIframe(frameid)
{
    var currentfr=document.getElementById(frameid);
    if (currentfr)
    {
        currentfr.style.display="block"
        if (currentfr.contentDocument && currentfr.contentDocument.body.offsetHeight) //ns6 syntax
            currentfr.height = currentfr.contentDocument.body.offsetHeight+FFextraHeight; 
        else if (currentfr.Document && currentfr.Document.body.scrollHeight) //ie5+ syntax
            currentfr.style.height = currentfr.Document.body.scrollHeight;
        if (currentfr.addEventListener)
            currentfr.addEventListener("load", readjustIframe, false);
        else if (currentfr.attachEvent)
        {
            currentfr.detachEvent("onload", readjustIframe); // Bug fix line
            currentfr.attachEvent("onload", readjustIframe);
        }
    }
}

function readjustIframe(loadevt) 
{
    var crossevt=(window.event)? event : loadevt;
    var iframeroot=(crossevt.currentTarget)? crossevt.currentTarget : crossevt.srcElement;
    if (iframeroot)
        resizeIframe(iframeroot.id);
    if (window.self!=window.top)
    {
        window.parent.resizeCaller();
    }
}

function loadintoIframe(iframeid, url)
{
    if (document.getElementById)
        document.getElementById(iframeid).src=url
}

if (window.addEventListener)
    window.addEventListener("load", resizeCaller, false)
else if (window.attachEvent)
    window.attachEvent("onload", resizeCaller)
else
    window.onload=resizeCaller
//Debug
function GC(obj)
{
	var s = '';
	for (a in obj)
	{
		try
		{
			s+=a+' = '+obj[a]+'\t';
		}
		catch(ex){}
	}
	return s;
}
//Hierachy
var hierarchyArray = new Array();
function RegisterHierarchy(pid,pevent,cid,cevent)
{
	var hObj = new Object();
	hObj.pid = pid;
	hObj.pevent = pevent;
	hObj.cid = cid;
	hObj.cevent = cevent;
	hierarchyArray.push(hObj);
}

function TriggerHierarchy(evt)
{
	var l = hierarchyArray.length;
	if (l>0)
	{
		var cbe = (window.event)?window.event:evt;
		var telm=(cbe.currentTarget)? cbe.currentTarget : cbe.srcElement;
		if ((cbe.type)&&(telm.id))
		{
			var i =0;
			for (i=0;i<l;i++)
			{
				if ((hierarchyArray[i].pid==telm.id)&&(hierarchyArray[i].pevent==cbe.type))
				{
					var celm = document.getElementById(hierarchyArray[i].cid);
					if (celm!=null)
					{
						if (celm[hierarchyArray[i].cevent])
						{
							celm[hierarchyArray[i].cevent](cbe);
						}
					}
				}
			}
		}
	}
}
//binding
function DataBind(evt)
{
	try
	{
		var l = this.dataSource.length;
		if (!(l>0)) return;
		var usePfilter = false;
		var pVal = null;
		if (this.parentControl)
		{
			var pObj = document.getElementById(this.parentControl);
			if (pObj)
			{
				var pVal = pObj.value;
				usePfilter = true;
			}
		}
		var cValue = this.value;
		var useItem = true;
		this.innerHTML = '';
		var k=0;
		for (i=0;i<l;i++)
		{
			useItem = true;
			if (usePfilter)
			{
				if (this.dataSource[i].pkey)
				{
					useItem = (this.dataSource[i].pkey==pVal);
				}
			}
			if (useItem)
			{
				var newOpt = new Option (this.dataSource[i].text, this.dataSource[i].key, 
					false, this.dataSource[i].key==cValue );
				this.options[k] = newOpt;
				k++;
			}
		}
	}
	catch (ex)
	{
		alert(GC(ex));
	} 
}

function CheckBoxDataBind(evt)
{
	try
	{
		var l = this.dataSource.length;
		if (!(l>0)) return;
		var usePfilter = false;
		var pVal = null;
		if (this.parentControl)
		{
			var pObj = document.getElementById(this.parentControl);
			if (pObj)
			{
				var pVal = pObj.value;
				usePfilter = true;
			}
		}
		var isChecked = false;
		if (usePfilter)
		{
			var i =0;
			for (i=0;i<l;i++)
			{
			
				if ((this.dataSource[i].pkey)&&(this.dataSource[i].key))
				{
					if((this.dataSource[i].pkey==pVal)&&(this.dataSource[i].key==this.value))
					{
						isChecked = true;
					}
				}
			}
		}
		//this.parentNode.style.visibility = (isChecked)?"visible":"hidden";
		this.parentNode.style.display = (isChecked)?"":"none";
		if (!isChecked)
		{
			this.checked = false;
		}
	}
	catch (ex)
	{
		alert(GC(ex));
	} 
}
