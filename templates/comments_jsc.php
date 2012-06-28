function LoadComments()
{
	var loader = document.getElementById('comments_loader');
	var container = document.getElementById('comments_container');
	container.innerHTML = loader.contentWindow.document.body.innerHTML;
	var comment_form = document.getElementById('comments_form');
	if (comment_form!=null)
	{
		comment_form.target = loader.name;
		comment_form.action = loader.contentWindow.location;
	}

	if (document.getElementById("comments_bad_auth")!=null)
	{
		AddComment(document.getElementById("comments_bad_auth").value);
		document.getElementById("comment_login").focus();
	}
	else
	{
		var anchor;
		var URL = document.location.toString().split('#');
		if((URL.length > 1)&&(URL[1].substring(0,7)=="comment")) {
			if (URL[1].length>7)
			{
				var comment_id = parseInt(URL[1].substring(7));
				if (!isNaN(comment_id))
				{
					var container_table = document.getElementById("comment_table_"+comment_id);
					if (container_table!=null)
					{
						container_table.style.border = "1px solid #D5D5D5";
					}
				}
			}
		   window.location.href = window.location.href;
		}
	}
}

function AddComment(reply_id,is_edit)
{
	if (is_edit==null)
	{
		is_edit = false;
	}
	var reply_id_container = document.getElementById('comment_reply_id');
	var old_reply_id = parseInt(reply_id_container.value);
	if (!old_reply_id >0)
	{
		old_reply_id=0;
	}
	reply_id = parseInt(reply_id);
	if (!reply_id >0)
	{
		reply_id=0;
	}
	var old_container_name = 'comment_post_container' + (old_reply_id>0?"_"+old_reply_id:'');
	var container_name = 'comment_post_container' + (reply_id>0?"_"+reply_id:'');
	
	var auth = null;
	
	if (document.getElementById('comment_authorozed')!=null)
	{
		auth = document.getElementById('comment_authorozed').checked;
	}
	
	var post_container = document.getElementById(container_name);
	if (old_container_name!=container_name)
	{
		var old_post_container = document.getElementById(old_container_name);
		post_container.innerHTML = old_post_container.innerHTML;
		old_post_container.innerHTML = '';
		old_post_container.style.display = "none";
	}
	
	post_container.style.display = "";
	reply_id_container.value = reply_id;
	document.getElementById('comment_preview').style.display = "none";
	document.getElementById('comment_new_link').style.display = reply_id==0?"none":"";

	if (document.getElementById('comment_captcha_enter')!=null)
	{
		document.getElementById('comment_captcha_enter').style.display = "";
		document.getElementById('comment_captcha_reenter').style.display = "none";
	}
	
	if (auth!=null)
	{
		document.getElementById('comment_authorozed').checked = auth;
		document.getElementById('comment_unauthorozed').checked = !auth;
		SwitchCommentLogin(auth);
	}
	
	/*edit mode*/
	document.getElementById('comment_submit').value=is_edit?"Сохранить изменения...":"Написать";
	document.getElementById('comment_image_overwrite').value=0;
	document.getElementById('comment_image_upload').style.display=is_edit?"none":"";
	
	var has_image = document.getElementById('comment_image_container_'+reply_id)!=null;
	document.getElementById('delete_image_container').style.display=is_edit&&has_image?"":"none";
	document.getElementById('comment_image_upload').style.display=is_edit&&has_image?"none":"";

	document.getElementById('comment_submit')["comment_action"] = is_edit?"edit":"add";
	if (is_edit)
	{
		document.getElementById('comment_text').value=document.getElementById('comment_hidden_text_'+reply_id).innerHTML;
	}
	return false;
}

function DeleteComment(reply_id)
{
	if (confirm("Вы действительнно хотите удалить данный комментарий?"))
	{
		var action = document.getElementById('comments_action');
		action.value = "delete";
		var reply_id_container = document.getElementById('comment_reply_id');
		reply_id_container.value = reply_id;
		document.getElementById('comments_form').submit();
	}
	return false;
}

function ValidateComment(preview)
{
        var unauth = document.getElementById("comment_unauthorozed");
	if ((unauth!=null)&&unauth.checked)
	{
		if (document.getElementById('comment_author').value=="")
		{
			alert("Укажите ваше имя");
			return false;
		}
	}
	var auth = document.getElementById("comment_authorozed");
	if ((auth!=null)&&auth.checked)
	{
		if (document.getElementById("comment_login").value=="")
		{
			alert("Укажите ваш логин");
			return false;
		}
		if (document.getElementById("comment_password").value=="")
		{
			alert("Укажите ваш пароль");
			return false;
		}	
	}
	if (document.getElementById('comment_text').value=="")
	{
		alert("Комментарий не содержит текста");
		return false;
	}	
	if ((unauth!=null)&&unauth.checked&&(!preview))
	{
		if (document.getElementById('comment_captcha_input').value=="")
		{
			alert("Не введены контрольные цифры");
			return false;
		}
	}
	return true;
}

function PreviewComment()
{
	if (!ValidateComment(true))
	{
		return false;
	}
	var author = document.getElementById('comment_author');
	if (author!=null)
	{
		var preview_author = document.getElementById('comment_preview_author');
		preview_author.innerHTML = author.value;
	}
	document.getElementById('comment_preview_text').innerHTML = document.getElementById('comment_text').value;
	document.getElementById('comment_preview').style.display = "";
	
	var date = new Date();
	
	document.getElementById('comment_preview_time').innerHTML = (date.getDate()<10?"0":0)+ date.getDate()+"."+((date.getMonth()+1)<10?"0":0)+(date.getMonth()+1)+"."+date.getFullYear()+" г.  "+date.getHours()+":"+date.getMinutes();
}

function SubmitComment()
{
	if (!ValidateComment(false))
	{
		return false;
	}
	var action = document.getElementById('comments_action');
	action.value = document.getElementById('comment_submit')["comment_action"];
	return true;
}

function SetCommentPage(page_id)
{
	var comment_fr =  document.getElementById('comments_loader');
	comment_fr.src = '/comments?cabinet=1&page='+page_id;
	return false;
}

function SwitchCommentLogin(auth)
{
	var comments_cookie_contianer = document.getElementById('comments_cookie_contianer');
	var comments_unauth_contianer = document.getElementById('comments_unauth_contianer');
	var comments_auth_contianer = document.getElementById('comments_auth_contianer');
	
	comments_cookie_contianer.style.display = auth?"none":"";
	comments_unauth_contianer.style.display = auth?"none":"";
	comments_auth_contianer.style.display = !auth?"none":"";
	
	if (auth)
	{
		document.getElementById("comment_login").focus();
	}
	else
	{
		document.getElementById("comment_author").focus();
	}
}

var comment_type = "<?php echo GP("type"); ?>";
var comment_type_id = "<?php echo GP("id"); ?>";
var cabinet = "<?php echo GP("cabinet"); ?>";

document.write('<div id="comments_container"></div>');
document.write('<iframe id="comments_loader" name="comments_loader" style="display:none;" onload="javascript:LoadComments();"></iframe>');
var comment_frame = document.getElementById('comments_loader');
comment_frame.src = (cabinet=="1")?'/comments?cabinet=1':('/comments?type='+comment_type+'&id='+comment_type_id);
