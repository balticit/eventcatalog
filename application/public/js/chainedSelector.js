
/**
* Жуткая функция для смены значения в подчиняющемся
* селекте, при смене значения в главенствуюшем селекте.
* При этом, если подчиняющийся селект - является главенствующим
* по отношению к другому, то реакция идет по цепочке...жуть
*
*
* @param string masterSelectId Идентификатор главенствующего селекта
* @param string slaveSelectId Идентификатор подчиняющегося селекта
* @return void
* @since 2005-08-01 21:50:03
*/
/**
	USAGE:

<select id="masterId" onchange="chainedSelector_OnChange('masterId', 'slaveId');">
...
</select>
<script language="JavaScript">
	
	// Для самого главного селекта
	document.getElementById("masterId")._values = values;
	document.getElementById("masterId")._rootId = "{$dataSource->data->id}";

	// Для всех селектов
	document.getElementById("masterId")._currentValue = "{$currentValue}";

</script>

*/
function chainedSelector_OnChange(masterSelectId, slaveSelectId, withEmpty, hideNodes)
{
	/**
	* Получаем обьекты селектов
	*
	*/
	var masterSelect = document.getElementById(masterSelectId);
	var slaveSelect = document.getElementById(slaveSelectId);

	/**
	* Текущее выбранное значение
	*
	*/
	var masterValue = "";
	if(masterSelect.selectedIndex != -1)
	{
		masterValue = masterSelect.options.item(masterSelect.selectedIndex).value;
	}

	/**
	* Чистим подчиняющися select
	*
	*/
	while(slaveSelect.options.length > 0)
	{
		slaveSelect.remove(0);
	}

	/**
	* Передаем подчиняющемуся селекту данные
	*
	*/
	slaveSelect._values = masterSelect._values;

	slaveSelect._rootId = "";

	/**
	* Находим id-корня для подчиняющегося
	* селекта
	*/
	for(i in masterSelect._values)
	{
		var item = masterSelect._values[i];

		if(item.p_id == masterSelect._rootId &&
			item.value == masterValue)
		{
			slaveSelect._rootId = item.id;
		}
	}

	if(typeof(withEmpty) != "undefined" && withEmpty)
	{
		var option = document.createElement("OPTION");
		if(withEmpty === true)
		{
			option.text  = "все";
		}
		else
		{
			option.text  = withEmpty;
		}
		option.value = "";
		slaveSelect.options.add(option);
	}

	var optionsAdded = 0;

	for(i in slaveSelect._values)
	{
		var item = slaveSelect._values[i];

		if(item.p_id == slaveSelect._rootId)
		{
			var option = document.createElement("OPTION");
			option.text  = item.title;
			option.value = item.value;

			if(slaveSelect._currentValue !== null && slaveSelect._currentValue == item.value)
			{
				option.selected = true;
				slaveSelect._currentValue = null;
			}

			slaveSelect.options.add(option);
			optionsAdded++;
		}

		if(typeof(hideNodes) != "undefined")
		{
			var ids = hideNodes.split(",");

			if(optionsAdded > 0)
			{
				var display = "block";
			}
			else
			{
				var display = "none";
			}

			for(var i in ids)
			{
				document.getElementById(ids[i]).style.display = display;
			}
		}
	}

	if(slaveSelect.onchange)
	{
		slaveSelect.onchange.call();
	}
}

