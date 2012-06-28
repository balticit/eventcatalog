//Подпсика на рассылку
function subscribe()
{
	reg =  /^[a-z0-9]+(?:[-\._]?[a-z0-9]+)*@(?:[a-z0-9]+(?:-?[a-z0-9]+)*\.)+[a-z]+$/i;
	var input_email = $("#subscribe_email");
	var email = input_email.val().trim();
	if (email == "введите е-mail")
		email = "";
	if (email.match(reg)) {		
		$.ajax({
			url:    '/ajax/subscribe/',
			data:   input_email.parent().serialize(),
			success: function(responseText) {
				if (responseText == '0') {
					ShowMessage("Спасибо за подписку на рассылку от EVENT КАТАЛОГА. "+
											"Теперь на указанную Вами электронную почту будет "+
											"поступать самая свежая информация о последних "+
											"добавлениях на портале, а также о предстоящих событиях "+
											"и мероприятиях event-индустрии. Желаем удачи и успехов в делах!");
					input_email.val("введите е-mail");
				}	
				else
					ShowMessage("На указанный адрес уже осуществляется рассылка от EVENT КАТАЛОГА");
			}
		});
	}
	else
		ShowMessage('Необходимо ввести правильный e-mail');
	return false;
}
//eventtv
function etvMin()  
{
  $.ajax({
    url:'/ajax/etv_type/',
    data: {type: 1},
    success: function(responseText) {
      if (responseText == 'OK') {
        $('#etv_big').hide();
        $('#etv_small').show();        
      }
    }
  });  
}
function etvMax()  
{
  $.ajax({
    url:'/ajax/etv_type/',
    data: {type: 0},
    success: function(responseText) {
      if (responseText == 'OK') {
        $('#etv_small').hide();
        $('#etv_big').show();  
      }
    }
  });    
}

function OpenEventTVForm()
{
  b = new Boxy.load('/ajax/etv_order',{
	  title: 'Заявка EVENT TV',
		modal: true,
		closeText: "[X]",
		fixed: false,
    unloadOnHide: true});
}

function SubmitEtvOrder(obj)
{
  $.ajax({
	  url:'/ajax/etv_order',
		data: $("#eventtv_order").serialize(),
		type: 'POST',
		success: function(responseText) {
		  if (responseText == 'OK') {
			  $("#etv_err_msg").text('');
			  Boxy.get(obj).hide();
        Boxy.alert("Спасибо, Ваша заявка будет рассмотрена в ближайшее время!"
        ,null, {title: "Заявка принята", unloadOnHide: true}); 

			}
			else {
			  $("#etv_err_msg").text(responseText);
			}
		}
	});
}
function check_gradient(obj)
{
  var d = document.getElementById('ttl_'+obj);
  var g = document.getElementById('ttlg_'+obj);
	var ad = document.getElementById('add_'+obj);
	g.style.display = (d.offsetWidth >= 120) ? 'block' : 'none';
	ad.style.paddingLeft = (d.offsetWidth >= 120) ? '0' : '5px';
}

