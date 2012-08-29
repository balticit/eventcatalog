<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php if(!$this->is_group){?>
    <title><?php CRenderer::RenderControl("title"); ?></title>
	<script type="text/javascript" language="JavaScript" src="/js/artist_find.js"></script>
    <?php }?>
    <?php if($this->is_group){ ?>
		<title><?php CRenderer::RenderControl("titlefilter"); ?>Каталог артистов - EVENT КАТАЛОГ</title>
		<script type="text/javascript" language="JavaScript" src="/js/artist_find.js"></script>
    <?php }?>
	<?php CRenderer::RenderControl("metadata"); ?>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr><td><?php CRenderer::RenderControl("topLine");?></td></tr>
<tr><!-- Заголовок-->
	<td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;"><?php CRenderer::RenderControl("header"); ?></td>
</tr>
<tr><!--Меню-->
<td><?php CRenderer::RenderControl("menu"); ?></td>
</tr>
<tr>
	<td><?php CRenderer::RenderControl("submenu"); ?></td>
</tr>
<tr>
    <?php if($this->is_group){?>
    <td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;" valign="top">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="vertical-align:top;width:230px">
                        <table cellpadding="0" cellspacing="0" border="0"  style="width: 218px; border: 1px solid #dadada;">
                        <tr>
                            <td class="ram5"><?php CRenderer::RenderControl("groupList"); ?></td> 
						</tr>
                        </table>
                    <img src="/images/front/0.gif" width="220" height="10">
					<a class="addArtist" href="/registration/?type=artist">Добавить Артиста</a>
                </td>
                <td style="vertical-align:top">
                    <div style="padding-left: 0px;"> 
                        <?php CRenderer::RenderControl("yaListTop"); ?>
						<?php CRenderer::RenderControl("titlefilterLinks"); ?>
                        <div style="overflow-x:hidden">
							<table border="0" cellpadding="0" cellspacing="0" class="tableInline" style="margin: 0 0px 0 0; width:100%;">
								<?php CRenderer::RenderControl("artistList"); ?>
							</table>
                        </div>
                        <div><?php CRenderer::RenderControl("footerText"); ?></div>
                    </div>
                    <p class="text"><?php CRenderer::RenderControl("pager"); ?></p><br />
                </td>
				<?php require ROOTDIR.'templates/_leftMenuWitgets.php'; ?>
            </tr>
        </table>
    </td></tr>
    <tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
    <tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
    </table>
    <?php CRenderer::RenderControl("googleanalytics"); ?>
    <?php }?>
    <?php if(!$this->is_group){?>
    <td style="padding: 10px 30px 10px 30px;" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top;width:230px">
			<table cellpadding="0" cellspacing="0" border="0"  style="width: 218px; border: 1px solid #dadada;">
			
			<tr> 
				<td class="ram5"><?php CRenderer::RenderControl("groupList"); ?></td>			
			</tr>
			</table>
			<img src="/images/front/0.gif" width="220" height="10">
            <a class="addArtist" href="/registration/?type=artist">Добавить Артиста</a>
		</td>
		<td style="vertical-align:top; padding-left: 5px;">
			<?php CRenderer::RenderControl("yaPersonal"); ?>
			<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr><td valign="top"><?php CRenderer::RenderControl("details"); ?></td></tr></table>
		</td>
                <?php require ROOTDIR.'templates/_leftMenuWitgets.php'; ?>
	</tr>
	</table>
</td></tr>
<tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
<?php }?>

<script type="text/javascript">
$(function() {

	$('#comment_submit').live('click', function() {
	  
	  if($('#comment_text').val()!='' && $('#comment_captcha_input').val()!='' && $('#comment_author').val()!='' ) {
      var itemText = 'Анонимные комментарии проверяются модераторами перед публикацией.<br /> '+
      'Через несколько часов ваш комментарий будет проверен.<br />'+
      'Спасибо!';
  	  var checkItem = $(this).next();
  	   
  		$( '#dialog-confirm' ).dialog({
  			resizable: false,
  			height:200,
  			width:400,
  			dialogClass: 'dialog-confirm onebutton',
  			modal: true,
  			position: 'center',
  			open: function(event, ui) { 
            $(this).find('p').html(itemText);
         },
  			buttons: {
  				'Ok': function() {
  					$('#comment_submit').submit();
  					$( this ).dialog( 'close' );
  				}
  				
  			}
  		});
		
		}
	});
	
		
});
</script>

</body>
</html>


