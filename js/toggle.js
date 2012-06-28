
function toggle(name, indicatorOnHtml, indicatorOffHtml)
{
	var cnt = document.getElementById('tg_cnt_'+name);
	var ind = document.getElementById('tg_ind_'+name);

	if(!cnt)
	{
		return;
	}

	if(cnt.style.display == "none")
	{
		cnt.style.display = "block";
		if(typeof(ind) == "object" && ind != null)
		{
			ind.innerHTML = decodeURI(indicatorOnHtml);
		}
		var cookies = new Cookies();
		cookies.setCookie("tgstate_" + name, "on", "/");
	}
	else
	{
		cnt.style.display = "none";
		if(typeof(ind) == "object" && ind != null)
		{
			ind.innerHTML = decodeURI(indicatorOffHtml);
		}
		var cookies = new Cookies();
		cookies.setCookie("tgstate_" + name, "off", "/");
	}

}

var GLOBALS_FILE_TOGGLE_JS_TOGGLE_DISPLAY = new Array();

function toggleDisplay(id, groupId)
{
	var element = document.getElementById(id/* + "_Content"*/);
	//var elementDiv = document.getElementById(id + "_Indicator");

	if(element.style.display == "none")
	{
		if(typeof(groupId) != "undefined")
		{
			var tmpEl = document.getElementById(GLOBALS_FILE_TOGGLE_JS_TOGGLE_DISPLAY[groupId]/* + "_Content"*/);
			//var tmpDiv = document.getElementById(GLOBALS_FILE_TOGGLE_JS_TOGGLE_DISPLAY[groupId] + "_Indicator");
			if(tmpEl)
			{
				tmpEl.style.display = "none";
				//tmpDiv.style.borderBottom = "solid 1px white";
			}

			GLOBALS_FILE_TOGGLE_JS_TOGGLE_DISPLAY[groupId] = id;
		}
		element.style.display = "block";
		//elementDiv.style.borderBottom = "solid 1px #CCC";
	}
	else
	{
		element.style.display = "none";
		//elementDiv.style.borderBottom = "solid 1px white";
	}
}


