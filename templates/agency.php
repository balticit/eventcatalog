<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title><?php CRenderer::RenderControl("title"); ?>EVENT КАТАЛОГ</title>
	<?php CRenderer::RenderControl("metadata"); ?>
</head>
<body>
<script type="text/javascript" type="text/javascript" src="/orphus/orphus.js" ></script>
<div style="position:absolute; visibility:collapse"><a href="http://orphus.ru" id="orphus" target="_blank"><img alt="Система Orphus" src="/orphus/orphus.gif" border="0" width="141" height="25" /></a></div>
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
<tr><!--содержание-->
<td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" >
		<tr>
		<td style="vertical-align:top; width:230px">  
			<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">
			
			<tr>			
				<td class="ram5"><?php CRenderer::RenderControl("activityList"); ?></td>					
			</tr>
			
			</table>
			<img src="/images/front/0.gif" width="220" height="10">
            <a class="addAgency" href="/registration/?type=agency">Добавить Агентство</a>
		</td>                
		<td style="vertical-align:top">
				<div style="padding-left: 5px;">
						<?php if ($this->is_main) { ?>
						<?php CRenderer::RenderControl("pro2List"); ?>
						<table border="0" cellpadding="0" cellspacing="0" width="100%">						
						<tr style="vertical-align: top;">
							<td width="49%">      
								<div class="recomendTitle agency h3">EVENT КАТАЛОГ рекомендует</div>
								<table class="recomended"><?php CRenderer::RenderControl("RecomedList_2"); ?></table>
							</td> 
							<td  colspan="2" style="padding: 32px 0 0 10px; vertical-align: top;"> 
								<table class="recomended"><?php CRenderer::RenderControl("RecomedList_1"); ?></table>
							</td>							
						</tr>
						<tr>
							<td width="49%">
								<div class="recomendTitle common h3">
									<a class="agency" href="/added">Новые агентства</a>
								</div>
								<table class="recomended"><?php CRenderer::RenderControl("NewList_2"); ?></table>
							</td>
							<td colspan="2" style="padding: 32px 0 0 10px; vertical-align: top;">
								<table class="recomended"><?php CRenderer::RenderControl("NewList_1"); ?></table>
							</td>							
						</tr>
						</table>
						
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr style="vertical-align: top;">
                <td rowspan="2" class="resident_news" width="50%">
                  <div class="recomendTitle agency"><a class="agency" href="/resident_news">Новости агентств</a></div>
                  <table class="news"><?php CRenderer::RenderControl("NewsList"); ?></table></td>
                </td>
                <td rowspan="2" style="width: 10px;"><img src="/images/front/0.gif" height="1" width="10" alt="" /></td>
                <td class="resident_comments" width="50%">
                  <div class="commentTitle agency">Новые комментарии</div>
                  <iframe src="/scroll/msg?res_type=agency" class="comment" frameborder="0" scrolling="no">Ваш браузер не поддерживает фреймы</iframe>
                </td>
              </tr>
              <tr style="vertical-align: top;">
                <td><br />
                <div class="resident_news">
                  <div class="recomendTitle agency"><a class="agency" href="/agency/rating">Рейтинг агентств</a></div>
                  <table class="rate"><?php CRenderer::RenderControl("RatingList"); ?></table>
                </div>
                </td>
              </tr>
            </table>
						
						<?php /*
						<div class="commentTitle agency"><a href="/random_resident/type/agency" target="rnd"><img src="/images/refresh_agency.png" style="margin: 1px 10px 0 0;"><span class="agency" style="vertical-align: top;">Случайные агентства</span></a></div>
					  <iframe src="/random_resident/type/agency" class="random" name="rnd" frameborder="0" scrolling="no">Ваш браузер не поддерживает фреймы</iframe>
					  */ ?>
					  
					  
						<div><?php CRenderer::RenderControl("footerText"); ?></div>
						<?php } else { ?>
								<?php CRenderer::RenderControl("yaListTop"); ?>
								<table border="0" cellpadding="0" cellspacing="0" class="tableInline" style="margin: 0 0px 0 0; width:auto;"><?php CRenderer::RenderControl("agencyList"); ?></table>
                                <p class="text"><?php CRenderer::RenderControl("pager"); ?></p><br>
                                <?php CRenderer::RenderControl("footerText"); ?>
						<?php } ?>
				</div>
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
