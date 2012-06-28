/**
* Класс для работы с Cookies
*
* @author r0man <r0man@programist.ru>
* @version 0.0.1
*/

function Cookies()
{
}

Cookies.prototype.setCookie = function (name, value, path)
{
	if(typeof(path)=="undefined")
	{
		document.cookie=name+"="+value;
	}
	else
	{
		document.cookie=name+"="+value+";path = "+path;
	}
}

Cookies.prototype.getCookie = function (name)
{
	var re = new RegExp(name+"=([^;]+)","ig");

	document.cookie.match(re);

	return RegExp.$1;
}

Cookies.prototype.getCookiesByPrefix = function (prefix)
{
	var rePrefixed = new RegExp(prefix+"[^;]+\=[^;]+","ig");
	var reNameVal  = new RegExp(prefix+"([^;]+)\=([^;]+)","i");

	var matches = document.cookie.match(rePrefixed);

	if(matches==null)
		return new Array();

	var result = new Array(matches.length);

	for(var i=0;i<matches.length;i++)
	{
		matches[i].match(reNameVal);
		result[i]={"name" : RegExp.$1, "value" : RegExp.$2};
	}

	return result;
}

Cookies.prototype.getCookiesByRegExp = function (regexp)
{
	var regexp_str;

	regexp_str = (typeof(regexp)=="object" ? regexp.source: regexp);
	
	var rePrefixed = new RegExp(regexp_str+"\=[^;]+", "ig");
	var reNameVal  = new RegExp("("+regexp_str+")\=([^;]+)", "i");

	var matches = document.cookie.match(rePrefixed);

	if(matches==null)
		return new Array();

	var result = new Array(matches.length);

	for(var i=0;i<matches.length;i++)
	{
		matches[i].match(reNameVal);
		result[i]={"name" : RegExp.$1, "value" : RegExp.$2};
	}

	return result;
}
