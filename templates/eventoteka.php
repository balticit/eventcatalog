<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Эвентотека - EVENT КАТАЛОГ</title>
    <?php CRenderer::RenderControl("metadata"); ?>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr><td><?php CRenderer::RenderControl("topLine");?></td></tr>
<tr><!-- Заголовок-->
	<td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;"><?php CRenderer::RenderControl("header"); ?></td>
</tr>
<tr><!--Меню-->
<td><?php CRenderer::RenderControl("menu"); ?>
<?php CRenderer::RenderControl("submenu"); ?>
<?php CRenderer::RenderControl("submenu1"); ?>
<?php CRenderer::RenderControl("submenu2"); ?>
<?php CRenderer::RenderControl("submenu3"); ?>
<?php CRenderer::RenderControl("submenu4"); ?>
</td>
</tr>
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 10px; height: 100%" valign="top">
<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td style="vertical-align:top; padding-left:5px;  width:230px">
		<table cellpadding="0" cellspacing="0" style="width: 218px; border: 1px solid #dadada;">		
		<tr>
		 
			<td class="ram5">
			  <div style="margin: 0 0 3px 0;" class="eventotekaMenu_title level2"><a href="/eventtv" class="black">EVENT TV</a></div><?php CRenderer::RenderControl("tvTopicList"); ?>                          
			  <div style="margin: 0 0 3px 0;" class="eventotekaMenu_title level2"><a href="/book" class="black">Event энциклопедия</a></div><?php CRenderer::RenderControl("topicList"); ?>
                        </td>
		
		</tr>		
		</table>
		<img src="/images/front/0.gif" width="220" height="10">
	</td>
    <td valign="top">
        <style  type="text/css">
           .isNew {color: red}
           .sortTypes a {
               border-bottom: dotted 1px #6096CA; 
               padding: 2px 3px 0 3px;
           }
           .sortTypes a:hover {
               text-decoration: none !important;
               border-bottom-style: solid;
           }
           .sortTypes a.currentSort {
               background: #6096CA;
               border-bottom-style: solid;
               color: #FFF;
           }
        </style>
        <script type="text/javascript">
           function gotoHref(url) {
               window.location.href = url;
           }
        </script>
		<!--
            <div class="sortTypes" style="color:#6096CA;margin-bottom:10px">
                    <a href="/eventoteka?filter=eventtv" <?php if ($this->sort == 2) print('class="currentSort"'); ?>>EVENT TV</a>&nbsp;&nbsp;&nbsp;
                    <a href="/eventoteka?filter=book" <?php if ($this->sort == 1) print('class="currentSort"'); ?>>Event энциклопедия</a>
            </div>
			-->
            <div class="recomendTitle eventoteka h3">
				Эвентотека 
				<a target="_blank" class="yaWitgetLink" id="eventotekaYaWitget" href="http://www.yandex.ru/?add=83172&from=promocode">Виджет Эвентотеки на Яндексе</a>
			</div>
			 
		   <?php CRenderer::RenderControl("publicList"); ?>
		   
            <div><?php // CRenderer::RenderControl("footerText"); ?></div>
            
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
</body>
</html>
