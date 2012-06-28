<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<link rel="stylesheet" type="text/css" href="/cms/templates/css/cms.css"  >
<script type="text/javascript" language="javascript" src="/cms/templates/scripts/cms.js"></script>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo GOOGLEMAPKEY; ?>" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" language="javascript">

var googlekey = '<?php echo GOOGLEMAPKEY; ?>';
var map;
String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g, ''); }

    //<![CDATA[

	function load() {
		if (document.getElementById("map")!=null) {
		  if (GBrowserIsCompatible()) {
			map = new GMap2(document.getElementById("map"));
			map.setCenter(new GLatLng(latlng[0],latlng[1]), 16);
			map.setMapType(G_NORMAL_MAP);
			map.addControl(new GMapTypeControl());
			map.addControl(new GLargeMapControl());
			if (is==1) map.openInfoWindowHtml(new GLatLng(latlng[0],latlng[1]),  '<div style="border: 0px red solid; color: black;">Это здесь</div>');
					
			
			GEvent.addListener(map, "click", function(overlay,latlng) {
				//if (marker!=null)
				//	map.removeOverlay(marker);
				//marker = new GMarker(latlng);
				//map.addOverlay(marker);
				//var point=map.fromLatLngToContainerPixel(latlng);
				map.openInfoWindowHtml(latlng,  '<div style="border: 0px red solid; color: black;">Это здесь</div>');
				//document.getElementById('coords').innerHTML='Широта '+latlng.x+', долгота '+latlng.y;
				document.getElementById('mapcoords').value=latlng.y+','+latlng.x;
			});
			
		  }
		}
	}		
	
    //]]>


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
}


function handleRequestStateChange() {
	// Продолжить если процесс завершен
	if (xmlHttp.readyState == 4) {
		// Продолжить только если статус HTTP равен "OK"
		if (xmlHttp.status == 200) {
			//document.getElementById('example').innerHTML = '1';
			var response = xmlHttp.responseXML;
			//alert(xmlHttp.responseText);
			//document.getElementById('example').innerHTML = '2';
			var rootxml = response.documentElement;
			//document.getElementById('example').innerHTML = '3';
			var code = rootxml.getElementsByTagName('code').item(0).firstChild.data;
			//document.getElementById('example').innerHTML = '4';
			if (code==200) {
				var coords = rootxml.getElementsByTagName('coordinates').item(0).firstChild.data;
				//document.getElementById('example').innerHTML = '5';
				document.getElementById('example').innerHTML = 'Адрес найден';
				//document.getElementById('example').innerHTML = '6';
				//var coordss = new String(coords+" ");
				var latLng = coords.split(',');
				//document.getElementById('example').innerHTML = '7';
				//alert(latLng.length);
				map.setCenter(new GLatLng(latLng[1],latLng[0]), 16);
				//document.getElementById('example').innerHTML = '8';
				//document.getElementById('example').innerHTML = '9';
				var marker = new GMarker(new GLatLng(latLng[1],latLng[0]));
				//document.getElementById('example').innerHTML = '10';
				map.addOverlay(marker);
				//document.getElementById('example').innerHTML +=latLng[1]+' '+latLng[0];
        document.getElementById('mapcoords').value=latLng[1]+','+latLng[0];
			}
			else {
				//document.getElementById('example').innerHTML = '11';
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
	return ht
};

function process(query) {
	if (xmlHttp) {
		try {
			query = replace_string(query.trim()," ","+");
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


	
</script>


  </head>
  <body onload="load()" onunload="GUnload()">
<div style="padding-bottom:7px;"><form action="/cms/areas" method="get">		
	<center>
		<table cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td width="100%" valign="bottom">
				<table width="100%"  cellpadding="0" cellspacing="0">
				  <tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td><img src="/images/search/leftind.gif"></td>
								<td background="/images/search/bgind.gif" width="100%" style="vertical-align: top; padding-top: 5px;">
								
									<input  value="" onchange="this.value=value + '%';" type="text" name="99_5$dataTable[$autoValues][title]" style="width: 100%; border: 0px black solid; color: gray;">
								</td>
								<td><img src="/images/search/rightind.gif"></td>
							</tr>
						</table>
					</td>
				  </tr>
				</table>

			</td>
			
			<td style="vertical-align:middle; padding-top:0px; padding-left:20px; padding-right: 10px; " valign="top"><input type="image"  src="/images/search/butind.gif"></td>
          </tr>
        </table>
	</center>
	</form>	</div>
<?php CRenderer::RenderControl("dataTable"); ?>

</body>
</html>
