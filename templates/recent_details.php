<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>������� - <?php CRenderer::RenderControl("title"); ?> - EVENT �������</title>
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
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px; height: 100%" valign="top">

     			<div id="h_img"><div id="recentlyPNG"><a href="/recent" tppabs="id3"><img src="/images/front/recently.gif" alt="" width="311" height="95" tppabs="images/front/recently.png"/></a></div>
     			</div>
<div id="menu">
	<table cellpadding="0" border="0" cellspacing="0" class="m_table">
		<tr>

				
												<td valign="top"><a href="/contractor" tppabs="id10"><img src="/images/front/menu/contractors.gif" tppabs="/images/front/menu/contractors.gif" alt="������� �����������"></a></td>
					<td><img width="7" height="1" src="/images/front/0.gif" tppabs="/images/front/0.gif" alt=""></td>
				
				
												<td valign="top"><a href="/area" tppabs="id24"><img src="/images/front/menu/areas.gif" tppabs="/images/front/menu/areas.gif" alt="������� ��������"></a></td>
					<td><img width="7" height="1" src="/images/front/0.gif" tppabs="/images/front/0.gif" alt=""></td>
				
				
												<td valign="top"><a href="/artist" tppabs="id39"><img src="/images/front/menu/artists.gif" tppabs="/images/front/menu/artists.gif" alt="������� ��������"></a></td>
					<td><img width="7" height="1" src="/images/front/0.gif" tppabs="/images/front/0.gif" alt=""></td>
				
				
												<td valign="top"><a href="/agency" tppabs="id55"><img src="/images/front/menu/agencies.gif" tppabs="/images/front/menu/agencies.gif" alt="������� ��������"></a></td>
					<td><img width="7" height="1" src="/images/front/0.gif" tppabs="/images/front/0.gif" alt=""></td>
				
				
												<td valign="top"><a href="/personal" tppabs="id54"><img src="/images/front/menu/personals.gif" tppabs="/images/front/menu/personals.gif" alt="������� ���������"></a></td>
					</tr>
	</table>
	<table border="0" cellpadding="0" cellspacing="0" class="reg_top">
		<tr><td></td>
			<td valign="center" width="135px" align="left" colspan="5">
							</td>
		</tr>
		<tr>
			<td valign="center" align="left">
							</td>
						
		</tr>
	</table>
</div>
<!-- end layers -->

	
	

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="one">
	<tr>
		<td valign="top" style="background:url('/images/front/all_02.gif') repeat-x top left;" class="head">
		<!-- EDITED -->
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<a href="/" tppabs="/"><img class="logo" src="/images/front/all_01.gif" tppabs="/images/front/all_01.gif" alt=""></a>
				</td>
				<td style="text-align: right">
		<?php CRenderer::RenderControl("headBanner"); ?>			
		
		</td>
				<td style="width: 10px;"></td>
			</tr>
		</table>
		<!-- EDITED -->
		</td>
	</tr>
	<tr>
			
			<td class="content" style="padding-bottom:0px; padding-top:60px; width:100%">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="one">
			<tr>
				<td valign="top" class="tdl1">
				<?php CRenderer::RenderControl("details"); ?>
					</td>
								
			
					</tr>
		<tr>
				<td  style="padding-left:30px; padding-top:10px; padding-bottom:10px; height:1px;">
					<?php CRenderer::RenderControl("menufooter"); ?>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td valign="bottom" class="foot">
		<!-- footer -->
<?php CRenderer::RenderControl("footer"); ?><!--LiveInternet counter--><script type="text/javascript"><!--
document.write('<a href="http://www.liveinternet.ru/click" '+
'target=_blank><img src="http://counter.yadro.ru/hit?t44.1;r'+
escape(document.referrer)+((typeof(screen)=='undefined')?'':
';s'+screen.width+'*'+screen.height+'*'+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+';u'+escape(document.URL)+
';'+Math.random()+
'" alt="" title="LiveInternet" '+
'border=0 width=31 height=31><\/a>')//--></script><!--/LiveInternet--></div>
		<!-- end footer -->
		</td>
	</tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
