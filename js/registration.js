var max_sel_groups = 5;
var artist_mus_ids = new Array();
var artist_stl_groups = new Array();
var artist_stl_group_ttl = new Array();
var artist_styles = new Array();
var checked_styles = new Array();

function ToggleNameInfo(info_id)
{
    var info = $("#"+info_id);
    info.slideToggle('fast');
}

function RegAddPhotos()
{
	var n = 0;
    for (i = 1; i <= 8; i++) {
        var ddd = $('div[id|=photos['+i+']]');
        if (ddd.css('display') == 'none') {
            n = i;
            ddd.slideToggle('fast');
            break;
        }
	}
    $("#btnAddPhoto").toggle(n<8);
}

function RegAddVideo()
{
	var n = 0;
    for (i = 1; i <= 8; i++) {
        var ddd = $('div[id|=video['+i+']]');
        if (ddd.css('display') == 'none') {
            n = i;
            ddd.slideToggle('fast');
            break;
        }
	}
    $("#btnAddVideo").toggle(n<3);
}



function RegAddAudio()
{
	var n = 0;
    for (i = 1; i <= 8; i++) {
        var ddd = $('div[id|=audio['+i+']]');
        if (ddd.css('display') == 'none') {
            n = i;
            ddd.slideToggle('fast');
            break;
        }
	}
    $("#btnAddAudio").toggle(n<3);
}


function RegAddMp3()
{
	var n = 0;
    for (i = 1; i <= 8; i++) {
        var ddd = $('div[id|=mp3s['+i+']]');
        if (ddd.css('display') == 'none') {
            n = i;
            ddd.slideToggle('fast');
            break;
        }
	}
    $("#btnAddMp3").toggle(n<8);
}

function CheckCountAct(cur_obj)
{
    var o = $(cur_obj);
    var chl = o.children('input');
    var flag = o.children('input:checked').length < 3;
    for(var i in chl) {
        if (!chl[i].checked)
            if (flag)
                chl[i].disabled='';
            else
                chl[i].disabled='disabled';
    }
}

function SiteAdrFocus(cur_obj)
{
    if (cur_obj.value == '')
        cur_obj.value = 'http://www.';
}

function SiteAdrBlur(cur_obj)
{
    if (cur_obj.value == 'http://www.')
        cur_obj.value = '';
    else if (cur_obj.value.substr(0,11) != 'http://www.')
        if (cur_obj.value.substr(0,4) == 'www.')
            cur_obj.value = 'http://'+cur_obj.value;
        else
            cur_obj.value = 'http://www.'+cur_obj.value;
}

function SetCity(city_name)
{
  $('#input_city').val(city_name);
	if (window.AreaCityChange)
		AreaCityChange(city_name);
}

function ShowCityList()
{
    new Boxy.load("/ajax/citylist_reg/",{
        behaviours: function(c) {
            c.find('.city-selector-item a').click(function(){
                var selcity = $(this);
                SetCity(selcity.html());
		var bbb = Boxy.get(this);
		bbb.hide();
		bbb.unload();
                return false;
            });
        },
        title: "Выберите Ваш город",
        closeText: "[X]",
        modal: true,
        clickToFront: true});
    return false; 
}

function SetCountry(country_name)
{
    $('#input_country').val(country_name);
}

function ShowCountryList()
{
    new Boxy.load("/ajax/countrylist_reg/",{
        behaviours: function(c) {
            c.find('.city-selector-item a').click(function(){
                var selcity = $(this);
                $('#input_country').val(selcity.html());
		var bbb = Boxy.get(this);
		bbb.hide();
		bbb.unload();
                return false;
            });
        },
        title: "Выберите Вашу страну",
        closeText: "[X]",
        modal: true,
        clickToFront: true});
    return false; 
}

function SelectGroup()
{		
		var sl = $("#selected_groups").children('div');
		var flag = sl.length < max_sel_groups;
		var sg = $("#subgroups");
		
		var parent_id = $("#sel_group").val();
		if (parent_id > 0) {
				sg.children("div").each(function() {$(this).hide();});
				var csg = $("#subgroup_id"+parent_id);
				csg.children("input").each(function() {
						var t = $(this);
						if (!t.attr('checked'))
								if (flag)
										t.removeAttr('disabled');
								else
										t.attr('disabled','disabled');
				});
				csg.show();
				sg.show();
		}
		else {
				sg.hide();
		}
}

function RenderArtistStyles(sel_ids)
{
  var stylesList = document.getElementById("stylesList");
  if (stylesList) {
		if (artist_styles.length>0 && sel_ids.length>0)
		{
			var s = "";
			n = sel_ids.length;
			for (k in sel_ids) {
				idx = sel_ids[k];
				s += artist_stl_group_ttl[idx]+':<br>';

				col_count = 2;
				cnt = artist_styles[idx].length;

				if (cnt < col_count)
					col_count = cnt;
				per_col = Math.floor(cnt/col_count);
				s += "<table><tr><td>";      
				ccol = 1;
				num = 0;  
				for (i in artist_styles[idx]) {
					if ((num - (ccol-1)*per_col) >= per_col) {
						ccol++;
						s += "</td><td>";
					}
					if ((num - (ccol-1)*per_col) != 0)
						s += "<br>";

					checked = $.inArray(artist_styles[idx][i].id, checked_styles)>=0;
						s += '<label><input type="checkbox" name="properties[style]['+
						artist_styles[idx][i].id+']" value="'+
						artist_styles[idx][i].id+'" '+(checked?'checked':'')+' style="vertical-align: middle;"> '+
						artist_styles[idx][i].title+'</label>';

					num++;  
				}
				s += "</td></tr></table>";

			}
			stylesList.innerHTML = s;
		}
		else
		{
			stylesList.innerHTML="";
		}
	}
}

function CheckSelectedTitle()
{
		var asr = $('#artist_style_row');
		if (asr) {
				var has_mus = false;
        var sel_ids = Array();
				$("#selected_groups").children('div').each(function() {
						var t = $(this);
						var id = t.attr('id');
						id = id.substring(11,id.length);
						id = $("#checkbox_id"+id).parent().attr('id');
						id = id.substring(11,id.length);
            i_id = parseInt(id);
						if ($.inArray(i_id, artist_mus_ids)>=0) {
								has_mus = true;
                stl_g_id = artist_stl_groups[i_id];
                if ($.inArray(stl_g_id,sel_ids) == -1)
                  sel_ids.push(stl_g_id);
						}
				});
				if (has_mus) {
						RenderArtistStyles(sel_ids);
            asr.show();
						$('#artist_mp3_row').show();
				}
				else {
						RenderArtistStyles(sel_ids);
            asr.hide();
						$('#artist_mp3_row').hide();
				}
		}
		
		if ($("#selected_groups").children('div').length)
				$("#selected_title").show();
		else
				$("#selected_title").hide();
		SelectGroup();
}

function SelectSubGroup(obj, cap_text)
{
		var sl = $("#selected_groups");
		if (obj.checked) {
				sl.append('<div id="selected_id'+obj.value+
									'"><input type="hidden" name="properties[selected_group]['+obj.value+
									']" value="'+obj.value+'">'+cap_text+
									' <a href="" class="reg_del_group" onclick="javascript: SelectedGroupDel('+obj.value+
									'); return false;">удалить</a></div>');
		}
		else {
				$("#selected_id"+obj.value).remove();
		}
		CheckSelectedTitle();
}

function SelectedGroupDel(id)
{
		$("#selected_id"+id).remove();
		$("#checkbox_id"+id).removeAttr('checked');
		CheckSelectedTitle();
}

function CheckMetroTitle()
{
  if($("#sel_metro").children('div').length)
      $("#sel_metro_title").show();
  else
      $("#sel_metro_title").hide();
}

function MetroDel(id)
{
	$("#metro_id"+id).remove();
  CheckMetroTitle();
}

function ShowMetroDlg()
{
    Boxy.load('/ajax/metro/forcity/204',{
        afterShow: function() { FillChecks();},
				title: "Выберите станции метро (не более трех)",
				closeText: "[X]",
				modal: true,
				clickToFront: true,
        unloadOnHide: true});
}

$(document).ready(function(){CheckSelectedTitle();});
