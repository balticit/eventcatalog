<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title><?php CRenderer::RenderControl("titlefilter"); ?>������� ����������� - EVENT �������</title>
	<?php CRenderer::RenderControl("metadata"); ?>
	<link href="/styles/dont_remove.css" rel="stylesheet" type="text/css"></link>
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
<td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top; padding-left:5px;  width:230px"> 
			<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">
			<!--<tr><td class="ram1"></td><td class="ram2"></td><td class="ram3"></td></tr>-->
			<tr>
				<!--<td class="ram4"></td>-->
				<td class="ram5"><?php CRenderer::RenderControl("actList"); ?></td>
				<!--<td class="ram6"></td>-->
				</tr>
			<!--<tr><td class="ram7"></td><td class="ram8"></td><td class="ram9"></td></tr>-->
			</table>
			<img src="/images/front/0.gif" width="220" height="10">
                        <a id="witgetAddResident" href="/registration/?type=user">�������� ���������</a>
		</td>
		<td style="vertical-align:top">
				<?php CRenderer::RenderControl("titlefilterLinks"); ?>	
				<!--
				<table cellpadding="1" cellspacing="0" class="letterFilter" style="width: 100%;" height="25">
				<tr><?php CRenderer::RenderControl("letterFilter"); ?><td style="width:80px;"></td></tr></table>
				-->
				<div style="padding-left:0px;"> 
						<?php if ($this->is_main) { ?>
						<!--<div style="margin-bottom: 10px;"><?php //CRenderer::RenderControl("yaResTop"); ?></div>-->
						<?php CRenderer::RenderControl("pro2List"); ?>
						<table  border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr style="vertical-align: top;">
								<td width="50%"><div class="recomendTitle contractor h3">EVENT ������� �����������</div>
								<table class="recomended"><?php CRenderer::RenderControl("RecomedList"); ?></table></td>
								<td style="width: 10px;"><img src="/images/front/0.gif" height="1" width="10"></td>
								<td width="50%"><div class="recomendTitle common h3"><a class="common" href="/added">����� ����������</a></div>
								<table class="recomended"><?php CRenderer::RenderControl("NewList"); ?></table></td></tr>
						<tr style="vertical-align: top;">
								<td class="resident_news"><div class="recomendTitle contractor h3"><a class="contractor" href="/resident_news">������� �����������</a></div>
								<table class="news"><?php CRenderer::RenderControl("NewsList"); ?></table></td>
								<td style="width: 10px;"><img src="/images/front/0.gif" height="1" width="10"></td>
								<td class="resident_comments"><div class="commentTitle contractor h3">����� �����������</div>
								<iframe src="/scroll/msg?res_type=contractor" class="comment" frameborder="0" scrolling="no">��� ������� �� ������������ ������</iframe></td></tr>
						<tr><td colspan="3"><img src="/images/front/0.gif" height="10" width="1"></td></tr>
						<tr style="vertical-align: top;">
								<td><div class="recomendTitle contractor"><a class="contractor h3" href="/contractor/rating">������� �����������</a></div>
								<table class="rate"><?php CRenderer::RenderControl("RatingList"); ?></table></td>
								<td style="width: 10px;"><img src="/images/front/0.gif" height="1" width="10"></td>
								<td><div class="commentTitle contractor h3"><a href="/random_resident/type/contractor" target="rnd"><img src="/images/refresh_contractor.png" style="margin: 1px 10px 0 0;"><span class="contractor" style="vertical-align: top;">��������� ��������</span></a></div>
								<iframe src="/random_resident/type/contractor" class="random" name="rnd" frameborder="0" scrolling="no">��� ������� �� ������������ ������</iframe></td></tr></table>
						
						<div>
							<div class="recomendTitle contractor h3">������ ���� � ����� ����������</div>
							<p>������ �����������, ���� ��� ������ �� ������ ������ ��� ������� ���������, ���������� ������������ ��������� ����� ������������� �� �������������� � ����������� ��������� ��� ����� �������� � ������ �����. ���������� �������� ���������� ��������, ��� ������� ������� ���������� ���������� ������� ����� ������ � �������, � �������� ������� ����� � ���� �������� � ��� ����� ���������� � �������� �����������, ��������� ��������� ���� ������������� � ��������� ������ �������� �������� � ����������� �����������. ��� ����� �������� ����������, ��� ��������� ��������� ���� ��� ���������� ����� ������, ����� ������� ���� ���������� � ��� ������������ �����? ��� �������� ������ � ����������� ��������� ����, ������������ ���������� ������, � ��� ������ �����-�������, � �������  �� ���������� ��� ������ ������������ �����, ��� �������� ����������� �����������. ������ ����� � ��� �������� ����������: ��� ������ �������� � �������� ������ ������������ �������� �� �������� ������������. � ��� ����������� ����������� ������, � ��� ������, ��� ������� �����, ����������� ���������� ���������� ���������� �����������, ������������� ����������� � �������� ��������� ������������� ��������� ����������. �����-������� ����� �������� ����������� � ������ ������������� �����������, ���������� ������������ ����������� ������ ������������ ���� ��� ����� ����������. ����� � ����� ��������������� �������� � �����-�������� � ��� ������������ ��������� �������� ����������� �����������. �����������, ��� ��� ������ ����� �������� ����, � �� ����������� �� � �������� ���������� ����������. ������� ��������� �����: ������ ������� ���� � ���� ����, � �� ����� - �������, ����� �����������, � ��������� �� ���� ���������, ����������� ���������� ���������� �������� �����. ����� � �������� ��������� �������� �������� � �� �������� ��������� ��������, � �����-�������� ���������� ����� ���������� � ������ ����������, � ������ ���������� �������� ������� ���������� � ������ �������� ��������.</p>
							<p>���� �� ����� ���-�� ����� � ��������� � �������� ��� ������������� � ����������� ��������� ������ �� ��������� <a href="/eventoteka"><b>����������</b></a>. �������� ����� ���������� ��������� � ������������� ������������ � ����������������� ����������. ���������� ���� ��� ������� ��������� ��� ����������� �����������, � ����� ������� ����� ���� � ��� ������. ��������, ������� <a href="/book/details932/"><b>����������</b></a> ����� ���� ���������� �� ��������� � ������������,  � 3D ���������� ������������ � �������� ������������ �����-��������.</p>
						</div>
						<?php } else { ?>
								<?php CRenderer::RenderControl("yaListTop"); ?>
								<table border="0" cellpadding="0" cellspacing="0" class="tableInline" style="margin: 0 0px 0 0; width:auto;"><?php CRenderer::RenderControl("contList"); ?></table>
								<div><?php CRenderer::RenderControl("footerText"); ?></div>
						<?php } ?>
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
</body>
</html>
