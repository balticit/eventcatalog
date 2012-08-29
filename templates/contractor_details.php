<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php if($this->is_type){ ?>
        <title><?php CRenderer::RenderControl("titlefilter"); ?></title>
		<link href="/styles/dont_remove.css" rel="stylesheet" type="text/css"></link>
    <?php }?>
    <?php if(!$this->is_type){ ?>
        <title><?php CRenderer::RenderControl("title"); ?> - ������� ����������� - EVENT �������</title>
    <?php }?>
	<?php CRenderer::RenderControl("metadata"); ?>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr><td><?php CRenderer::RenderControl("topLine");?></td></tr>
<tr><!-- ���������-->
	<td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;"><?php CRenderer::RenderControl("header"); ?></td>
</tr>
<tr><!--����-->
<td><?php CRenderer::RenderControl("menu"); ?></td>
</tr>
<tr>
	<td><?php CRenderer::RenderControl("submenu"); ?></td>
</tr>
<tr><!--����������-->
<?php if($this->is_type){ ?>
    <td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="vertical-align:top;width:230px">
                    <table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">                    
                        <tr>
                            <td class="ram5"><?php CRenderer::RenderControl("actList"); ?></td>                           
						</tr>                        
                    </table>
                    <img src="/images/front/0.gif" width="220" height="10">
					<a class="addConstractor" href="/registration/?type=contractor">�������� ����������</a>
                </td>
                <td style="vertical-align:top">
                    <?php CRenderer::RenderControl("titlefilterLinks"); ?>
					<div style="padding-left:0px;"> 
						<?php CRenderer::RenderControl("yaListTop"); ?>                      
                        <table border="0" cellpadding="0" cellspacing="0" class="tableInline" width="100%"><?php CRenderer::RenderControl("contList"); ?></table>
                        <div><?php CRenderer::RenderControl("footerText"); ?></div>
                    </div>
                    <p class="text"><?php CRenderer::RenderControl("pager"); ?></p><br>
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
<?php if(!$this->is_type){ ?>
<td style="padding: 10px 30px 10px 30px;">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top;width:230px">
			<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">
			
			<tr>				
				<td class="ram5"><?php CRenderer::RenderControl("actList"); ?></td>			
			</tr>
			
			</table>
			<img src="/images/front/0.gif" width="220" height="10" alt="" />                      
			<a class="addConstractor" href="/registration/?type=contractor">�������� ����������</a>
		</td>
		<td style="vertical-align:top; padding-left: 5px;">
			<?php CRenderer::RenderControl("yaPersonal"); ?>
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr><td><?php CRenderer::RenderControl("contDetails"); ?></td></tr></table>
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
      var itemText = '��������� ����������� ����������� ������������ ����� �����������.<br /> '+
      '����� ��������� ����� ��� ����������� ����� ��������.<br />'+
      '�������!';
  	  var checkItem = $(this).next();
  	   
  		$( '#dialog-confirm' ).dialog({
  			resizable: false,
  			height:200,
  			width:400,
  			dialogClass: 'dialog-confirm onebutton',
  			modal: false,
  			position: "center",
  			open: function(event, ui) { 
            $(this).find('p').html(itemText);
         },
  			buttons: {
  				'��': function() {
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
