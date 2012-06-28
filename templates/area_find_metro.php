<script language="JavaScript">
var fmStationsByLine = new Array();
var fmStations = new Array();
<?php echo $this->stationsByLine; ?>
<?php echo $this->stations; ?>
var hover_show;

function RenderFMStations()
{
	html_text = '';
	$('.fm_station_marker:visible').each(function(){
	  id = this.id;
		id = id.substring(10,id.length);
		if (html_text)
		  html_text += ', ';
		html_text += '<label title="Удалить" id="fm_sel_'+id+'" onclick="DelFMStation(this);" class="fm_selected"><input type="hidden" name="metro['+id+']" value="'+id+'">'+fmStations[id].title+'</label>';
	});
  $('#fm_sel_stations').html(html_text);
}

function ClickFMLine(obj)
{
  var id = obj.id;
  id = id.substring(8, id.length)
	var arr = fmStationsByLine[id];
	var chk_box = $('input#fm_line_'+id);
	l_checked = chk_box.attr('checked');
	if (obj.tagName != 'INPUT')
	  l_checked = !l_checked;
	chk_box.attr('checked',l_checked);
	for (i in arr) {
	  $('#fm_marker_' + arr[i]).toggle(l_checked);
	}
	RenderFMStations();
}

function CheckFMLine(line_id)
{
	var arr = fmStationsByLine[line_id];
	all_visible = true;
	for (i in arr) {
	  if (!$('#fm_marker_' + arr[i]).is(':visible')) {
		  all_visible = false;
			break;
    }
	}
	$('input#fm_line_'+line_id).attr('checked',all_visible);
}

function ClickFMStation(obj)
{
  var id = obj.id;
  id = id.substring(11, id.length);
	var mark = $('#fm_marker_'+id);
	mark.toggle(!mark.is(':visible'));
	CheckFMLine(fmStations[id].line);
	RenderFMStations();
}

function ClickFMMarker(obj)
{
  var id = obj.id;
  id = id.substring(10, id.length);
	var mark = $('#fm_marker_'+id);
	mark.toggle(!mark.is(':visible'));
	CheckFMLine(fmStations[id].line);
	RenderFMStations();
}

function DelFMStation(obj)
{
  var id = obj.id;
  id = id.substring(7, id.length);
	$('#fm_marker_'+id).hide();
	CheckFMLine(fmStations[id].line);
	RenderFMStations();
}

function ClickFMType(obj)
{
  var btns = $('td.fm_btn_type');
  btns.toggleClass('fm_btn_type_active',false);
  btns.toggleClass('fm_btn_type_noactive',true);
  $(obj).toggleClass('fm_btn_type_active',true);
  $(obj).toggleClass('fm_btn_type_noactive',false);    
  var id = obj.id.substring(7,obj.id.length);
  $('div.fm_layer').hide();
  $('#fm_'+id).show();
}

function FMWayShow(obj)
{
  id = obj.id;
  id = id.substring(11,id.length);
  $('.fm_mway_map').each(function() {
    eid = this.id.substring(8,this.id.length); 
    if (eid != id) {
      $(this).hide();
      $('#mway_window_'+eid).hide();
    }  
  });  
  var way = $('#mway_map'+id);
  way.toggle(!way.is(':visible') || hover_show);
  hover_show = false;
  $('#mway_window_'+id).toggle(way.is(':visible'));
}

function FMWayIn(obj)
{
  id = obj.id;
  id = id.substring(11,id.length);
  var way = $('#mway_map'+id);
  if (!way.is(':visible')) {
    way.show();
    hover_show = true;
  }
}

function FMWayOut(obj)
{
  id = obj.id;
  id = id.substring(11,id.length);
  var way = $('#mway_map'+id);
  if (hover_show && way.is(':visible')) {
    way.hide();
    hover_show = false;
  }
}

function FMWayClose(obj)
{
  id = obj.id;
  id = id.substring(10,id.length);
  $('#mway_map'+id).hide();
  $('#mway_window_'+id).hide();
}

function FMDelCity(obj)
{
  id = obj.id;
  id = id.substring(14,id.length);
  $('#mway_sld_city_'+id).remove();
	$('#mway_city_'+id).attr('checked',false);
	<?php if ($this->reg_dlg == 1) { ?>
		$("#mcity_id"+id).remove();
		CheckMWayTitle();
	<?php } ?>
}

function FMClickCity(obj)
{
  id = obj.id;
  id = id.substring(10,id.length);
	if (obj.checked) {
    $('#mway_sel_city').append('<label id="mway_sld_city_'+id+'" title="Удалить" class="fm_selected" onclick="FMDelCity(this)"><input type="hidden" name="mcity['+id+']" value="'+id+'">'+obj.value+' </label>');
		<?php if ($this->reg_dlg == 1) { ?>
      $("#sel_mway").append('<div id="mcity_id'+id+
                '"><input type="hidden" name="properties[mway_city]['+id+
                ']" value="'+id+'">'+obj.value+
                ' <a href="" class="reg_del_group" onclick="javascript: MCityDel('+id+
                '); return false;">удалить</a></div>');
		<?php } ?>
	}
	else {
	  $('#mway_sld_city_'+id).remove();
		<?php if ($this->reg_dlg == 1) { ?>
			$("#mcity_id"+id).remove();
		<?php } ?>
	}
	<?php if ($this->reg_dlg == 1) { ?>
    CheckMWayTitle();
	<?php } ?>
}

function FMDelHighway(obj)
{
  id = obj.id;
  id = id.substring(17,id.length);
  $('#mway_sld_highway_'+id).remove();
	$('#mway_highway_'+id).attr('checked',false);
	<?php if ($this->reg_dlg == 1) { ?>
		$("#highway_id"+id).remove();
		CheckMWayTitle();
	<?php } ?>
}

function FMClickHighway(obj)
{
  id = obj.id;
  id = id.substring(13,id.length);
	if (obj.checked) {
    $('#mway_sel_highway').append('<label id="mway_sld_highway_'+id+'" title="Удалить" class="fm_selected" onclick="FMDelHighway(this)"><input type="hidden" name="mhighway['+id+']" value="'+id+'">'+obj.value+' </label>');

		<?php if ($this->reg_dlg == 1) { ?>
      $("#sel_mway").append('<div id="highway_id'+id+
                '"><input type="hidden" name="properties[mway_highway]['+id+
                ']" value="'+id+'">'+obj.value+
                ' ш. <a href="" class="reg_del_group" onclick="javascript: HighwayDel('+id+
                '); return false;">удалить</a></div>');
		  
		<?php } ?>
	}
	else {
	  $('#mway_sld_highway_'+id).remove();
		<?php if ($this->reg_dlg == 1) { ?>
      $("#highway_id"+id).remove();
		<?php } ?>
	}
	<?php if ($this->reg_dlg == 1) { ?>
    CheckMWayTitle();
	<?php } ?>
}
</script>
<div style="padding:0; width: 840px; height: <?php if ($this->reg_dlg != 1) { ?>750<?php } else { ?>530<?php } ?>px;" id="FMarea">
<?php if ($this->reg_dlg != 1) { ?>
<?php if ($this->fa == 0) { ?>
  <div id="FM_type_btn" style="padding: 3px 5px 0 5px;">
	<table width="100%"><tr><td class="fm_btn_type fm_btn_type_active" id="fm_btn_moscow" onclick="ClickFMType(this)">Поиск по Москве</td>
	<td class="fm_btn_type fm_btn_type_noactive" id="fm_btn_moscow_ways" onclick="ClickFMType(this)">Поиск по Подмосковью</td>
	<td align="right"><a href="" onclick="javascript: Boxy.get(this).hide(); return false;">[X]</a></td>
	</tr></table></div> <?php } ?>

	<div class="fm_layer" id="fm_moscow" style="padding: 3px; <?php if ($this->fa == 2) print("display: none;"); ?>">
		<?php if($this->fa == 0) { ?> <form method="GET" action="/area/"> <?php } ?>
		<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr valign="top">	
		<td>
			<div style="overflow: hidden; height: 717px;"><div style="position: relative; margin: -15px 0 0 0;">
				<img src="/images/map_moscow_metro.png" usemap="#MoscowMetroImageMap">
				<map name="MoscowMetroImageMap" id="MoscowMetroImageMap">
					<?php CRenderer::RenderControl("areas"); ?>
					<?php CRenderer::RenderControl("areas_lines"); ?>
				</map>
				<?php CRenderer::RenderControl("markers"); ?>
			</div></div>
		</td>
    <td><img src="/images/front/0.gif" width="10" height="1"></td>
		<td class="fm_list_panel">
			<div class="fm_list_title">Округа:</div>
			<div style="height: 220px; overflow: hidden;" id="fm_sel_districts"><?php CRenderer::RenderControl("districts"); ?></div>
			<div class="fm_list_title">Линии метро:</div>
			<div style="height: 250px; overflow: hidden;"><?php CRenderer::RenderControl("metro_lines"); ?></div>
			<div class="fm_list_title">Выбранные станции метро:</div>
			<div style="height: 100px; overflow: scroll; border: 1px solid #999999; margin: 0 5px 0 0;" id="fm_sel_stations"></div>
			<div style="text-align: right; margin: 15px 5px 0 0;"><input type="submit" value="OK"<?php if ($this->fa > 0) { ?> onclick="Boxy.get(this).hide();" <?php } ?> ></div>
		</td></tr></table>
		<?php if($this->fa == 0) { ?> </form> <?php } ?>
	</div>
<?php } ?>
	<div class="fm_layer" id="fm_moscow_ways" style="<?php if ($this->reg_dlg == 1 || $this->fa == 2) { ?> margin: 10px 0 0 0; <?php } else { ?> display: none; <?php } ?>">
		<?php if ($this->reg_dlg != 1 && $this->fa == 0) { ?><form method="GET" action="/area/"><?php } ?>
		<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr valign="top">	
		<td width="605">
      <div style="position: relative;">
        <div style="position: absolute;"><img src="/images/mways/map.gif" height="506px" width="605px"></div>
        <div style="position: absolute;"><?php CRenderer::RenderControl("mways_maps"); ?></div>
        <div style="position: absolute;">
          <img src="/images/front/0.gif" usemap="#mways_areas" height="506px" width="605px">
          <map id="mways_areas" name="mways_areas"><?php CRenderer::RenderControl("mways_areas"); ?></map>
        </div>
        <div style="position: absolute;"><?php CRenderer::RenderControl("mways_windows"); ?></div>
      </div>
		</td>
    <td width="10"><img src="/images/front/0.gif" width="10" height="1"></td>
		<td class="fm_list_panel">
			<div class="fm_list_title">Выбранные шоссе:</div>
			<div style="height: 150px; overflow: scroll; border: 1px solid #999999; margin: 0 5px 0 0; padding: 2px;" id="mway_sel_highway"></div>
      <br>
			<div class="fm_list_title">Выбранные города:</div>
			<div style="height: 150px; overflow: scroll; border: 1px solid #999999; margin: 0 5px 0 0; padding: 2px;" id="mway_sel_city"></div>
			<div style="text-align: right; margin: 15px 5px 0 0;"><input type="submit" value="OK" <?php if ($this->reg_dlg == 1 || $this->fa > 0) { ?> onclick="Boxy.get(this).hide();" <?php } ?> ></div>
		</td></tr></table>
		<?php if ($this->reg_dlg != 1) { ?></form><?php } ?>
	</div>
</div>
