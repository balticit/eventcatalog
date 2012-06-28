
function addOnLoadHandler(onLoadHandler)
{
	if(typeof(window.addEventListener) != "undefined") // DOM2
	{
		var handler = function(e)
		{
			onLoadHandler(e);
		}
		window.addEventListener( "load", handler, false);
	}
	else if(typeof(window.attachEvent) != "undefined") // IE
	{
		var handler = function(e)
		{
			onLoadHandler(e);
		}
		window.attachEvent("onload", handler);
	}
	else
	{
		if(window.onload != null)
		{
			var oldOnLoadHandler = window.onload;
			window.onload = function(e)
			{
				oldOnLoadHandler(e);
				onLoadHandler(e);
			}
		}
		else
		{
			window.onload = function(e)
			{
				onLoadHandler(e);
			}
		}
	}
}
