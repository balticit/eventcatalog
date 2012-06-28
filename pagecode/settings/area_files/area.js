var fm_dialog = null;
function DlgByPlace()
{
  if ({show_metro}) {
    fm_dialog = new Boxy('<div style="width:100px; height: 100px;"><div style="margin: auto;">Загрузка...</div></div>',{
				modal: true,
				closeText: "[X]",
				fixed: false,
        unloadOnHide: true});
	  $.ajax({  
      url: "/area/find_metro/",  
      cache: false,  
      success: function(html){  
        fm_dialog.setContent(html);
				fm_dialog.center('x');
				fm_dialog.moveToY($(window).scrollTop());
      }  
    });
  }
  else {    
    Boxy.alert(
      "Данная фнукция доступна только для г. Москвы и Подмосковья"
      ,null, {title: "Сообщение", unloadOnHide: true}); 
  }
}

function ChangeCapacity(obj) {
  
  var sl = $("#find_params");
  if (obj.checked) {
      sl.append('<input type="hidden" name="capacity['+obj.value+']" value="'+obj.value+'">');
  }
  else {
      sl.find('input[name="capacity['+obj.value+']"]').remove();
  }
}

function FillCapacity()
{
  $("#find_params").find('input[name^="capacity"]').each(function() {
    var t = $(this);
    $("#cap"+t.val()).attr('checked','checked');
  });
  $("#cap_list").find('input').change(function() {
    ChangeCapacity(this);
  });
}

function DlgByCapacity()
{
  new Boxy('<div style="width:300px;" id="cap_list">{cap_list}</div><div style="text-align: right;"><input type="button" value="OK" onclick="StartFind()"></div>',{
        afterShow: function() { FillCapacity();},
        title: "Выберите вместимость/ кол-во персон",
        closeText: "[X]",
        modal: true,
        clickToFront: true,
        unloadOnHide: true});
}

function ChangeCost(obj) {

  var sl = $("#find_params");
  if (obj.checked) {
      sl.append('<input type="hidden" name="cost['+obj.value+']" value="'+obj.value+'">');
  }
  else {
      sl.find('input[name="cost['+obj.value+']"]').remove();
  }
}

function FillCost()
{
  $("#find_params").find('input[name^="cost"]').each(function() {
        var t = $(this);
        $("#cost"+t.val()).attr('checked','checked');
  });
  $("#cost_list").find('input').change(function() {
    ChangeCost(this);
  });
}

function DlgByCost()
{
  new Boxy('<div style="width:300px;" id="cost_list">{cost_list}</div><div style="text-align: right;"><input type="button" value="OK" onclick="StartFind()"></div>',{
        afterShow: function() { FillCost();},
        title: "Выберите стоимость на персону",
        closeText: "[X]",
        modal: true,
        clickToFront: true,
        unloadOnHide: true});
}

function DlgAdditional()
{
  Boxy.load('/area/find_add/{params}',{
        afterShow: function() { },
				title: "Выведите параметры поиска",
				closeText: "[X]",
				modal: true,
				fixed: false,
        unloadOnHide: true});
}

function StartFind()
{
  var f = $("#find_params");
  if (f.children().length > 0)
    f.submit();
  else {
	  
  }
}
