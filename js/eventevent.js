/*
var nodes = document.getElementsByTagName("TABLE");
	for(var i = 0; i < nodes.length; i++)
	{
		var node = nodes.item(i);
		if(node.className == "pane")
		{
			node.style.display = "none";

			document.getElementById("activator_" + node.id.substr(5)).className = "paneActivator";
		}
	}
*/
function showPane(paneNumber)
{
	var pane = document.getElementById("pane_" + paneNumber);
	var activator = document.getElementById("activator_" + paneNumber);

	pane.style.display = "block";
	activator.className = "paneActivatorActive";
}

function killPane(paneNumber)
{
	var pane = document.getElementById("pane_" + paneNumber);
	var activator = document.getElementById("activator_" + paneNumber);

	if(pane.isMouseOver || activator.isMouseOver)
	{
		return;
	}
	pane.style.display = "none";
	activator.className = "paneActivator";
}

function killPaneSure(paneNumber)
{
	var pane = document.getElementById("pane_" + paneNumber);
	var activator = document.getElementById("activator_" + paneNumber);
	pane.style.display = "none";
	activator.className = "paneActivator";
}


function tryToKillPane(paneNumber)
{
	window.setTimeout("killPane('" + paneNumber + "');", 1);
}
