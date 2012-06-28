{dsGetObjectByStrId str_id="sections" assign="dsSections"}
{dsFetchObjectChildren object=$dsSections assign="dsSections" onlyDirectChildren="1" activityRequired="1"}

function over()
{ldelim}
{foreach from=$dsSections->elements item="dsObject"}
	document.getElementById('{$dsObject->data->id}').className='colon_over';
{/foreach}
{rdelim}


function normal()
{ldelim}
{foreach from=$dsSections->elements item="dsObject"}
document.getElementById('{$dsObject->data->id}').className='colon';
{/foreach}
{rdelim}

