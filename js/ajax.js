// JavaScript Document
// Создание объекта XmlHttp
function createXmlHttpRequestObject() {
	// Ссылка на объект XmlHttpRequest
	var xmlHttp;
	// Это работает во всех броузерах кроме IE6
	try {
		xmlHttp = new XMLHttpRequest();
	}
	catch (e) {
		// IE6 или более ранняя версия
		try {
			xmlHttp = new ActiveXObject("Microsoft.XMLHttp");
		}
		catch(e) {
		}
	}
	if (!xmlHttp)
		alert("Ошибка! Запрос неудачен");
	else
		return xmlHttp;
        return null;
}


function handleRequestStateChange() {
	// Продолжить если процесс завершен
	if (xmlHttp.readyState == 4) {
		// Продолжить только если статус HTTP равен "OK"
		if (xmlHttp.status == 200) {
			var response = xmlHttp.responseXML;
			var rootxml = response.documentElement;
			var code = rootxml.getElementsByTagName('code').item(0).firstChild.data;
			if (code=='200') {
				var coords = rootxml.getElementsByTagName('coordinates').item(0).firstChild.data;
				document.getElementById('example').innerHTML = 'Адрес найден';
				var coordss = new String(coords+" ");
				var latLng = coordss.split(',');
				//alert(latLng.length);
				document.getElementById('example').innerHTML ="1";
				map.setCenter(new GLatLng(latLng[1],latLng[0]), 16);
				document.getElementById('example').innerHTML ="2";
				var marker = new GMarker(new GLatLng(latLng[1],latLng[0]));
				document.getElementById('example').innerHTML ="3";
				map.addOverlay(marker);
				//document.getElementById('example').innerHTML +=latLng[1]+' '+latLng[0];
			}
			else {
				document.getElementById('example').innerHTML = 'Адрес не найден';
			}
		}
	}
}

function replace_string(txt,cut_str,paste_str){
	var f=0;
	var ht='';
	ht = ht + txt;
	f=ht.indexOf(cut_str);
	while (f!=-1){
		//цикл для вырезания всех имеющихся подстрок
		f=ht.indexOf(cut_str);
		if (f>0){
			ht = ht.substr(0,f) + paste_str + ht.substr(f+cut_str.length);
		};
	};
	return ht;
};

function process(query) {
	if (xmlHttp) {
		try {
			query = replace_string(query," ","+");
			xmlHttp.open("GET","/ajax/query/"+query+"/", true);
			xmlHttp.onreadystatechange = handleRequestStateChange;
			xmlHttp.send(null);
			document.getElementById('example').innerHTML = "Ожидание ответа сервера...";
		}
		catch (e) {
			alert("Невозможно соединиться с сервером:\n"+e.toString()+"\n"+"http://maps.google.com/maps/geo?q="+query+"&output=xml&key="+googlekey);
			//alert("Ошибка! Запрос неудачен!\n"+"http://maps.google.com/maps/geo?q="+query+"&output=xml&key="+googlekey);			
		}
	}
}



function WinOpen(text, title, w, h) {
	var myWindow = window.open ("","","width="+w+",height="+h+",resizable=no,scrollbars=no,status=no");
	myWindow.document.write ('<html><title>'+title+'</title><body leftmargin=0 topmargin=0 rightmargin=0 bottommargin=0><pre>'+text+'</pre></body></html>');
}