function openImageWindow(url, title)
{
	var image = new Image();
	image.src = url;

	var wnd = window.open('', '', 'location=no,toolbar=no,scrollbars,top=100,left=100,width='+image.width+',height='+image.height);
	wnd.document.open();
	wnd.document.write(
		'<html>'+"\n"+
		'<body leftmargin=0 rightmargin="0" bottommargin="0" topmargin=0 marginheight=0 marginwidth=0>'+"\n"+
		'<img onclick="window.close();" src="'+url+'" style="width100%; height:100%;" title="'+title+'" alt="'+title+'" />'+"\n"+
		'</body>'+"\n"+
		'</html>'
	);
	wnd.document.close();

	return false;
}

function showHideRequests(id)
{
	var div = document.getElementById(id);
	if(div != null)
	{
		if(div.style.display == 'block')
		{
			div.style.display = 'none';
		}
		else
		{
			div.style.display = 'block';
		}
	}

	return false;
}
function showHidePost(id, firstText, lastText)
{
	var div = document.getElementById('a'+id);
	var span = document.getElementById('post'+id);
	if(div != null && span != null)
	{
		if(div.text == firstText + '...')
		{
			div.innerHTML = firstText + lastText;
		}
		else
		{
			div.innerHTML = '<b>' + firstText + '...</b>';
		}
	}

	return false;
}

function showMp3PopupWindow(src, title)
{
	/*
	var date = new Date();
	var random = Math.floor(1000 * Math.abs(Math.sin(date.getTime())));

	var wnd = window.open('', '', 'location=no,toolbar=no,scrollbars,top=100,left=100,width=400,height=300');
	wnd.document.open();
	wnd.document.write(
		'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'+"\n"+
		'<html>'+"\n"+
		'<head>'+"\n"+
		'<meta http-equiv="content-type" content="text/html; charset=windows-1251">'+"\n"+
		'<link rel="stylesheet" type="text/css" href="/styles/front.css">'+"\n"+
		'</head>'+"\n"+
		'<body leftmargin=0 rightmargin="0" bottommargin="0" topmargin=0 marginheight=0 marginwidth=0>'+"\n"+
		'<div class="popup">'+"\n"+
		'</div>'+"\n"+
		'<div class="popup_center">' + "\n"+
		'<b>'+ title +'</b><br /><br />'+"\n"+
		'<embed type="application/x-mplayer2" src="'+ src +'" name="MediaPlayer2" ShowControls="1" ShowDisplay="0" AutoStart="1" ' +
		'ShowStatusBar="1" width="200" height="70" pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/"></embed>'+"\n"+
		'</div>' + "\n"+
		'</body>'+"\n"+
		'</html>'
	);
	wnd.document.close();
	*/

	var musicWindow = window.open('http://'+location.hostname+'/music.php?src='+src+'&title='+title, 'musicWindow', 'location=no,toolbar=no,scrollbars=no,top=100,left=100,width=400,height=230');
	return false;
}


function selectThumb(id, mini)
{
	var img = document.getElementById(id);
	var miniImg = document.getElementById('img'+mini);
	var miniSpan = document.getElementById('span'+mini);
	if(img != null && miniImg != null && miniSpan != null)
	{
		for(var i=1; i<=allImgs; i++)
		{
			//document.getElementById('img'+i).style.border = '';
			document.getElementById('span'+i).style.color = 'black';
		}
		img.src = midiImg[mini];
		selectingImg = mini;
		//miniImg.style.border = 'red 1px solid';
		miniSpan.style.color = 'red';
	}
	return false;
}

function listPrevImg(id)
{
	selectingImg = selectingImg - 1;
	if(selectingImg <= 0)
	{
		selectingImg = allImgs;
	}
	selectThumb(id, selectingImg);

	if(popupImgWindow != null)
	{
		popupImgWindow.document.getElementById('maxiImg').src = maxiImg[selectingImg];
	}
}

function listNextImg(id)
{
	selectingImg = selectingImg + 1;
	if(selectingImg > allImgs)
	{
		selectingImg = 1;
	}
	selectThumb(id, selectingImg);

	if(popupImgWindow != null)
	{
		popupImgWindow.document.getElementById('maxiImg').src = maxiImg[selectingImg];
	}
}

function openLargeImg(url, title)
{
	var openWindow = document.getElementById('popupImgWindow');
	if(openWindow != null)
	{
		openWindow.close;
	}

	var width = maxiImgWidth[selectingImg];
	var height = maxiImgHeight[selectingImg];

	popupImgWindow = window.open("http://"+url+"/popupImgWindow.php?src="+maxiImg[selectingImg]+"&title="+title, 'popupImgWindow', 'location=no,toolbar=no,scrollbars=no,top=100,left=100,width=640,height=400');

	return false;
}