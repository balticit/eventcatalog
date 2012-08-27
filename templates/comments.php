<form id="comments_form" enctype="multipart/form-data" method="post">
	<input type="hidden" name="comments_action" id="comments_action" value="load">
	<input type="hidden" name="comment_reply_id" id="comment_reply_id">
	<input type="hidden" name="comment_page_no" id="comment_page_no">
	<input type="hidden" name="comment_page_size" id="comment_page_size">
	<?php if ($this->hasComments) { ?>
	<h4 class="detailsBlockTitle"><a name="comments">Комментарии</a></h4>
	<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
<?php
  foreach ($this->comments as $comment){
    if ($comment["is_deleted"]==1) continue; /*Сообщение удалено пользователем*/
?>
	<tr>
			<td><a name="comment<?php echo $comment["tbl_obj_id"] ?>" id="comment<?php echo $comment["tbl_obj_id"] ?>" href="#"></a>
			<table width="100%" id="comment_table_<?php echo $comment["tbl_obj_id"] ?>" style="border-top: 1px solid #f2f2f2; padding-left:<?php echo $comment["level"]*20 ?>px; margin-bottom:10px;">
			
      <?php if ($comment["active"] == 1) { ?>
      
      <tr valign="top"><td width="60">
			<div style="float:left; width:60px; height:40px; border: 1px solid #D5D5D5;"><img border="0" height="40" width="60" src="/<?php echo $comment["logo"]==''?"images/nologo.png":"upload/".$comment["logo"]; ?>"></div>
			</td><td>
			<div style="padding:6px;">
			<div style="float:left; position:relative; margin-right:8px;"><b><?php echo $comment["sender_id"]!=''?'<a target="_blank" style="text-decoration:underline;" href="/u_profile/?type='.$comment["type"].'&id='.$comment["user_id"].'">'.$comment["title"].'</a>':'<span style="color:#BEBEBE;">'.$comment["sender_name"].'</span>'; ?></b></div>
			<?php if (isset($comment["r500"]) && $comment["r500"]>0){ ?><div style="float:left; position:relative; background-image:url('/images/award-500.gif'); margin-right:20px; height:27px; width:<?php echo $comment["r500"]*23; ?>px;"></div><?php }?>
			<?php if (isset($comment["r100"]) && $comment["r100"]>0){ ?><div style="float:left; position:relative; background-image:url('/images/award-100.gif'); margin-right:20px; height:27px; width:<?php echo $comment["r100"]*26; ?>px;"></div><?php }?>
			<div style="float:left; position:relative; color:#999999;"><?php echo $comment["time_mess"]?></div>
			<br>
			</div>
			<div style="padding:6px;"><div style="display:none;" id="comment_hidden_text_<?php echo $comment["tbl_obj_id"] ?>"><?php echo $comment["u_text"] ?></div>
			
      <div class="url_replace">
      <?php
				echo $comment["text"];
			?>
			</div>
			<br />
				<?php if ( ($comment["image"]!='')&&($comment["active"]==1)&&($comment["is_deleted"]==0)){?>
				<div id="comment_image_container_<?php echo $comment["tbl_obj_id"] ?>">
					<a href="/<?php echo $comment["image_large"];?>" style="text-decoration:none;" title="Нажмите чтобы увеличить" target="_blank">
						<span style="color:#999999;"><small>Нажмите чтобы увеличить</small></span><br>
						<img border="0" src="/<?php echo  $comment["image"];?>">
					</a>
				</div>
				<br>
				<?php }?>
				<a href="#" style="text-decoration:underline;" onClick="javascript:return AddComment(<?php echo $comment["tbl_obj_id"]; ?>);">Ответить</a>
				<?php if (($comment["sender_id"]!='')&&(!isset($comment["is_owner"]) || !$comment["is_owner"])){?>
				<?php /* | <a target="_blank" style="text-decoration:underline;margin-left:20px;" href="/u_cabinet/?data=my_messages&action=compose&type=<?php echo $comment["type"]; ?>&id=<?php echo $comment["user_id"]; ?>" target="_blank">Написать личное сообщение</a>*/ ?>
				<?php }?>
				<?php if (isset($comment["is_owner"]) && $comment["is_owner"] && ($comment["is_deleted"]==0)){ ?>| <a href="#" style="text-decoration:underline;margin-left:20px;" onClick="javascript:return AddComment(<?php echo $comment["tbl_obj_id"]; ?>,true);">Редактировать</a> | <a style="text-decoration:underline;margin-left:20px;" href="#" onClick="javascript:return DeleteComment(<?php echo $comment["tbl_obj_id"]?>);">Удалить</a> <?php }?>
			</div></td></tr>
			
			<?php } ?>
			
			
			<tr>
				<td colspan="2">
					<div id="comment_post_container_<?php echo $comment["tbl_obj_id"];?>" style="display:none; background-color:#f6f6f6;">
					</div>
				</td></tr></table>
			</td>
	</tr>
	<?php } ?>
	</table>
	<?php } ?>

<script>
  var elements = document.getElementsByClassName('url_replace')
  for (var i=0; i<elements.length; i++)
  {
    elements[i].innerHTML = elements[i].innerHTML.replace(/(?:https?:\/\/)?(?:www\.)?([a-z0-9-]+\.[a-z0-9-\.\/_]+)/g, '<noindex><a target="_blank" href="http://$1">$1</a></noindex>')
  }
</script>

	<?php if ($this->is_cabinet){ ?>
	<?php CRenderer::RenderControl("pager"); ?>
	<?php }else{ ?>
	<a href="#" id="comment_new_link" onClick="javascript:return AddComment(null);">
    <span class="link_add_comment">Написать комментарий</span>
  </a>
	<?php }?>
	<div id="comment_post_container" style="<?php if (!$this->c_error&&($this->auth_error==null)) {?>display:none;<?php }?> background-color:#F6F6F6; width:100%;">
		<div style="margin: 5px; padding-top:15px;">
	<?php if ($this->anonymous) {?>
		<div style="display:block; padding-left:8px;">
			<input type="radio" name="comment_auth" id="comment_authorozed" <?php if(!$this->c_error) { ?> checked="checked" <?php } ?> onClick="javascript:SwitchCommentLogin(true);" value="1"><label style="font-weight:bold;" for="comment_authorozed">Пользователь eventcatalog.ru</label>
			<br>
			<br>
			<div style="<?php if ($this->c_error) {?>display:none;<?php } else {?> display:block; <?php } ?>" id="comments_auth_contianer">
				<table border="0" cellspacing="0" cellpadding="0" style="margin-left:20px;">
					<tr>
						<td><label for="comment_login">Логин:&nbsp;&nbsp;&nbsp;</label></td>
						<td><input type="text" name="login" id="comment_login" style="width:200px;" value="<?php if ($this->auth_error!=null) echo $_POST["login"]; ?>"></td>
					</tr>
					<tr><td colspan="2" style="height:4px;"></td></tr>
					<tr>
						<td><label for="comment_password">Пароль:&nbsp;&nbsp;&nbsp;</label></td>
						<td><input type="password" name="password" id="comment_password" style="width:200px;"></td>
					</tr>
					<tr><td colspan="2" style="height:2px;"></td></tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="remember" id="comment_remember" value="remember" style="margin-left:0px; float:left; padding:0px;">&nbsp;&nbsp;<label for="comment_remember">Помнить меня две недели</label></td>
					</tr>
					<?php if ($this->auth_error!=null){?>
					<tr>
						<td colspan="2" style="color:#990000;"><?php echo $this->auth_error;?>
						<input type="hidden" id="comments_bad_auth" value="<?php echo $_POST["comment_reply_id"];?>"></td>
					</tr>
					<?php }?>
				</table>
			</div>
			<input type="radio" name="comment_auth" id="comment_unauthorozed" <?php if($this->c_error) { ?> checked="checked" <?php } ?> onClick="javascript:SwitchCommentLogin(false);" value="0"><label style="font-weight:bold;" for="comment_unauthorozed">Гость</label>
			<br>
			<br>
			<div style="<?php if (!$this->c_error) {?>display:none;<?php }?> margin-left:20px;" id="comments_unauth_contianer"><label for="comment_author">Имя:</label>&nbsp;&nbsp;&nbsp;<input type="text" name="comment_author" id="comment_author" value="<?php echo $this->c_author; ?>"></div>
			<br>
		</div>
	<?php }?>
		<div style="width:100%; text-align:center; display:block; padding-left:10px;">
			<div style="width:98%; display:block; text-align:left;">
				<textarea name="comment_text" id="comment_text" style="height:150px; width:100%; margin-bottom:10px; margin-right:0px; font-size:12px;"><?php if ($this->auth_error!=null){ echo htmlspecialchars ($_POST["comment_text"]);} else {echo $this->c_text; }?></textarea>
				<br>
				<div id="delete_image_container">
				(<a href="#" onClick="javascript: if (confirm('Действительно удалить. Уверены?')){ document.getElementById('comment_image_upload').style.display='';document.getElementById('delete_image_container').style.display='none';document.getElementById('comment_image_overwrite').value='1';} return false;">удалить картинку</a>)<br>
				</div>
				<div id="comment_image_upload">
					<input type="hidden" id="comment_image_overwrite" name="comment_image_overwrite" value="0">
					<div style="display:inline; float:left; width:150px;">Прикрепить картинку:</div><input type="file" name="comment_file"><br>
					<small style="color:#333333;">Разрешены .jpg, .gif и .png, максимальный размер 500 Килобайт</small>
				</div>
	<?php if ($this->anonymous) {?>


				<div style="<?php if (!$this->c_error) {?>display:none;<?php }?>"  id="comments_cookie_contianer">
				<input type="hidden" name="comment_captcha" id="comment_captcha" value="<?php echo $this->captcha_sid; ?>">
				<div id="comment_captcha_reenter" style="display:<?php if ($this->c_error) {?>block<?php } else {?>none<?php } ?>; width:100%; height:23px; padding-left:5px; padding-top:3px; margin-bottom:15px; border: 1px solid #FF0000;"><font color="red">Введите цифры еще раз, пожалуйста... </font></div>
				<div id="comment_captcha_enter" style="display:<?php if (!$this->c_error) {?>block<?php } else {?>none<?php } ?>;">Введите цифры, которые Вы видите на картинке:</div>
				<div>
				<div style="float:left; width:120px; height:50px; border: 1px solid #000000;"><img border="0" width="120" height="50" src="/captcha/sid.php?<?php echo session_name()?>=<?php echo session_id()?>&sid=<?php echo $this->captcha_sid; ?>"></div>
				<input type="text" name="comment_captcha_input" id="comment_captcha_input" style="width:120px; height:50px; font-size:32px; font-family:sans-serif; margin-left:10px; padding:4px;">
				</div><br>
				</div>
	<?php }?>
				<div style="display:block; padding:10px; background-color:#FFFFFF; width:98%; height:20px;">
					<input style="float:left;" type="button" value="Предпросмотр" onClick="javascript:return PreviewComment();">
					<input style="float:right;" id="comment_submit" type="submit" value="Написать" onClick="javascript:return SubmitComment();">

				</div>
				<br>
			</div>
			<p style="font-size:10px; margin:0px; padding:0px;">&nbsp;</p>
		<div id="comment_preview" style="width: 95%; margin-left:5px; padding-left:20px; background-color:#FFFFFF; display:none; height:100px; padding-top:10px;">
			<div style="float:left; width:60px; height:40px;border: 1px solid #D5D5D5; margin-right:15px;"><img border="0" height="40" width="60" src="<?php echo $this->user_logo==null?"/images/nologo.png":$this->user_logo; ?>"></div>
			<div style="padding:5px;">
			<div style="float:left; position:relative; margin-right:20px;"><b id="comment_preview_author" <?php echo ($this->anonymous)?'style="color:#BEBEBE;"':""; ?> ><?php  $this->user_title; ?></b></div>
			<?php if ($this->r500>0){ ?><div style="float:left; position:relative; background-image:url('/images/award-500.gif'); margin-right:20px; height:27px; width:<?php echo $this->r500*23; ?>px;"></div><?php }?>
			<?php if ($this->r100>0){ ?><div style="float:left; position:relative; background-image:url('/images/award-100.gif'); margin-right:20px; height:27px; width:<?php echo $this->r100*26; ?>px;"></div><?php }?>
			<div style="float:left; position:relative; color:#999999;" id="comment_preview_time"></div>
			<br>
			</div>
			<div style="padding:5px; text-align:left;" id="comment_preview_text"></div>
		</div>
		</div>

		<p style="font-size:10px; margin:5px; padding:0px;">&nbsp;</p>
		</div>
	</div>
</form>

<div id="dialog-confirm" title="Спасибо за комментарий">
  <p></p>
</div>


