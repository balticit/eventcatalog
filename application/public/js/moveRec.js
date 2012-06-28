function moveRec(formName)
{
	document.forms[formName].submit();
}

function moveRecEnd(formName)
{
	document.forms[formName].elements['num'].value = 0;
	document.forms[formName].submit();
}