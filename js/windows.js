function openImageWindow(url, title) {
    var image = new Image();
    image.src = url;

    var wnd = window.open('', '', 'location=no,toolbar=no,scrollbars,top=100,left=100,width=' + image.width + ',height=' + image.height);
    wnd.document.open();
    wnd.document.write(
        '<html>' + "\n" +
            '<body leftmargin=0 rightmargin="0" bottommargin="0" topmargin=0 marginheight=0 marginwidth=0>' + "\n" +
            '<img onclick="window.close();" src="' + url + '" style="width100%; height:100%;" title="' + title + '" alt="' + title + '" />' + "\n" +
            '</body>' + "\n" +
            '</html>'
    );
    wnd.document.close();

    return false;
}

function showHideRequests(id) {
    var div = document.getElementById(id);
    if (div != null) {
        if (div.style.display == 'block') {
            div.style.display = 'none';
        }
        else {
            div.style.display = 'block';
        }
    }
    return false;
}
function showHidePost(id, firstText, lastText) {
    var div = document.getElementById('a' + id);
    var span = document.getElementById('post' + id);
    if (div != null && span != null) {
        if (div.text == firstText + '...') {
            div.innerHTML = firstText + lastText;
        }
        else {
            div.innerHTML = '<b>' + firstText + '...</b>';
        }
    }
    return false;
}

function showMp3PopupWindow(src, title) {
    var date = new Date();
    var random = Math.floor(1000 * Math.abs(Math.sin(date.getTime())));

    var wnd = window.open('', '', 'location=no,toolbar=no,scrollbars,top=100,left=100,width=400,height=300');
    wnd.document.open();
    wnd.document.write(
        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">' + "\n" +
            '<html>' + "\n" +
            '<head><title>' + title + '</title>' + "\n" +
            '<meta http-equiv="content-type" content="text/html; charset=windows-1251">' + "\n" +
            '<link rel="stylesheet" type="text/css" href="/styles/front.css">' + "\n" +
            '</head>' + "\n" +
            '<body leftmargin=0 rightmargin="0" bottommargin="0" topmargin=0 marginheight=0 marginwidth=0>' + "\n" +

            '<div class="popup_center">' + "\n" +
            '<b>' + title + '</b><br /><br />' + "\n" +
            '<OBJECT id="mediaPlayer" width="380" height="250"' + "\n" +
            'classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95"' + "\n" +
            "codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701'" + "\n" +
            "standby='Loading Microsoft Windows Media Player components...' type='application/x-oleobject'>" + "\n" +
            "<param name='fileName' value='" + src + "'>" + "\n" +
            "<param name='animationatStart' value='true'>" + "\n" +
            "<param name='transparentatStart' value='true'>" + "\n" +
            "<param name='autoStart' value='true'>" + "\n" +
            "<param name='showControls' value='true'>" + "\n" +
            "<param name='loop' value='true'>" + "\n" +
            "<EMBED type='application/x-mplayer2'" + "\n" +
            "pluginspage='http://microsoft.com/windows/mediaplayer/en/download/'" + "\n" +
            " id='mediaPlayer' name='mediaPlayer' displaysize='4' autosize='-1' " + "\n" +
            " bgcolor='darkblue' showcontrols='true' showtracker='-1' " + "\n" +
            " showdisplay='0' showstatusbar='-1' videoborder3d='-1' width='380' height='250'" + "\n" +
            ' src="' + src + '" autostart="true" designtimesp="5311" loop="true">' + "\n" +
            '</EMBED>' + "\n" +
            '</OBJECT>' + "\n" +
            '</div>' + "\n" +
            '</body>' + "\n" +
            '</html>'
    );
    wnd.document.close();
}

function SetDynamicImage(pid, src) {
    if (src != "") {
        var pcont = document.getElementById(pid);
        if (pcont) {
            var imgHtm = '<img src="/upload/' + src + '" class="logo120" alt="Логотип">';
            pcont.innerHTML = imgHtm;
        }
    }
}

function ShowMessage(text) {
    Boxy.alert(text, null, {title:"Сообщение", unloadOnHide:true});
}

function ShowRegUser() {
    new Boxy($('#reguser').parent(), {
        title:"Регистрация пользователя",
        closeText:"[X]",
        modal:true,
        clickToFront:true});
}

function ShowLogonDialog() {
    new Boxy($('#loginform').parent(), {
        behaviours:function (c) {
            c.find('#loginform_form').bind('submit', function () {
                var loginmessage = c.find('#loginmessage');
                loginmessage.hide();
                var authForm = $(this);
                $.ajax({
                    url:'/ajax/authorize/',
                    data:authForm.serialize(),
                    success:function (responseText) {
                        if (responseText == '1')
                            document.location = document.location + '';
                        else
                            loginmessage.show();
                    }
                });
                return false;
            });
        },
        afterShow: function(){
          $("#login_input").focus();
        },
        title:"Вход на сайт",
        closeText:"[X]",
        modal:true,
        clickToFront:true});
}

function ShowLikeMessage() {
    Boxy.alert(
        "Возможность участвовать в рейтинге доступна только зарегистрированным пользователям. " +
            "Пожалуйста, <a href=\"\" onclick=\"javascript: Boxy.get(this).hide(); ShowLogonDialog(); return false;\">введите</a> " +
            "Ваш логин и пароль или <a href=\"\" onclick=\"javascript: Boxy.get(this).hide(); ShowRegUser(); return false;\">зарегистрируйтесь</a>."
        , null, {title:"Сообщение", unloadOnHide:true});
}

function ShowMsgMessage() {
    Boxy.alert(
        "Возможность оставлять сообщения доступна только зарегистрированным пользователям. " +
            "Для отправки сообщения <a href=\"\" onclick=\"javascript: Boxy.get(this).hide(); ShowLogonDialog(); return false;\">введите</a> " +
            "Ваш логин и пароль или <a href=\"\" onclick=\"javascript: Boxy.get(this).hide(); ShowRegUser(); return false;\">зарегистрируйтесь</a>."
        , null, {title:"Сообщение", unloadOnHide:true});
}

function ShowFavMessage() {
    Boxy.alert(
        "Добавить в избранное может только зарегистрированный пользователь. " +
            "Для добавления в избранное <a href=\"\" onclick=\"javascript: Boxy.get(this).hide(); ShowLogonDialog(); return false;\">введите</a> " +
            "Ваш логин и пароль или <a href=\"\" onclick=\"javascript: Boxy.get(this).hide(); ShowRegUser(); return false;\">зарегистрируйтесь</a>."
        , null, {title:"Сообщение", unloadOnHide:true});
}

function highlightName(element) {
    $(element).children(".name").addClass("text_highlight");  
}
function blacklightName(element) {
    $(element).children(".name").removeClass("text_highlight");
}