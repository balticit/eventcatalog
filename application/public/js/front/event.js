
function changeTypeOfPersonal(selector)
{
	var value = selector.options.item(selector.selectedIndex).value.split(/\|/);

	var formP = document.getElementById("registrationForm_personal");
	var formA = document.getElementById("registrationForm_personal_agency");

	if(value[1] == "agency")
	{
		formP.style.display = "none";
		formA.style.display = "block";

		var element = formA.elements["properties_type"];
		element.value = value[0];
	}
	else
	{
		formP.style.display = "block";
		formA.style.display = "none";

		var element = formP.elements["properties_type"];
		element.value = value[0];
	}
}
