function clickRadio(obj){	var id = obj.id;
	var id_num = id.substr(2);	$("#pro_type").val(id_num);
	$(".pro_radio").each(function(){		if (this.id != id)
		  this.src = '/images/pro/radio.png';	});
	var sel_text = '������� ';
	var koeff = 1;
	if (id_num%2 == 0){ sel_text += '����������� '; koeff = 2; }
	if (koeff == 2)
	  obj.src = '/images/pro/radio_checked.png';
  else
	  obj.src = '/images/pro/radio_checked1.png';
	var period = Math.round(id_num/2);
	sel_text += '�� ';
	var cost = 750;
	switch (period){
	  case 1: sel_text += '1 �����'; cost = 750; break;
	  case 2: sel_text += '3 ������'; cost = 1950; break;
	  case 3: sel_text += '6 �������'; cost = 3450; break;
	  case 4: sel_text += '1 ���'; cost = 6000; break;
  }
  if (koeff == 2)
	  $("#pro_selected_text").css("color","#febf01");
  else
		$("#pro_selected_text").css("color","#bce247");
	$("#pro_selected_text").text(sel_text);
	var txt = (cost*koeff).toString();
	if (txt.length>3)
		 txt = txt.slice(0,-3) + ' ' + txt.slice(-3);
		  
  $("#cost").text(txt + ' �.');
}
function clickSubmit(){	$("#pro_cost").submit();
}
/*
$("tr.highlight").hover(function(obj){
	alert('ggg');
	obj.children("td").css('background','#eeeeee');
}, function(obj){
	obj.children("td").css('background','none');
});*/