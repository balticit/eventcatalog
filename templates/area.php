<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title><?php CRenderer::RenderControl("titlefilter"); ?>������� �������� - EVENT �������</title>
		<?php CRenderer::RenderControl("metadata"); ?>
		<script language="javascript"><?php CRenderer::RenderControl("jsArea"); ?></script>
		<link rel="stylesheet" type="text/css" href="/styles/fm.css">
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
<td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top; padding-left:5px;  width:230px">
			<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">
			<!--<tr><td class="ram1"></td><td class="ram2"></td><td class="ram3"></td></tr>-->
			<tr>
				<!--<td class="ram4"></td>-->
				<td class="ram5"><?php CRenderer::RenderControl("typeList"); ?></td>
			</tr>
			</table>
			<img src="/images/front/0.gif" width="220" height="10">
            <a class="addArea" href="/registration/?type=area">�������� ��������</a>
		</td>
		<td style="vertical-align:top">
				<?php if(!$this->is_main) //CRenderer::RenderControl("titlefilterLinks"); ?>
				<div>
                                <form method="get" style="display: none" id="find_params"></form>
                                <?php CRenderer::RenderControl("currentFind"); ?></div>
				<div>
					<?php CRenderer::RenderControl("currentCapacity"); ?>
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr><?php CRenderer::RenderControl("capacityFilter"); ?></tr>
					</table>
				</div>
				<div style="padding-top:15px; padding-left: 0px;"> 
						<?php if ($this->is_main) { ?>
						<!--<div style="margin-bottom: 10px;"><?php //CRenderer::RenderControl("yaResTop"); ?></div>-->
						<?php CRenderer::RenderControl("pro2List"); ?>
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr style="vertical-align: top;">
							<td width="49%">      
								<div class="recomendTitle area h3">EVENT ������� �����������</div>
								<table class="recomended"><?php CRenderer::RenderControl("RecomedList_2"); ?></table>
							</td> 
							<td  colspan="2" style="padding: 32px 0 0 10px; vertical-align: top;"> 
								<table class="recomended"><?php CRenderer::RenderControl("RecomedList_1"); ?></table>
							</td>							
						</tr>
						<tr>
							<td width="49%">
								<div class="recomendTitle area h3">
									<a class="area" href="/added">����� ��������</a>
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
                  <div class="recomendTitle area"><a class="area" href="/resident_news">������� ��������</a></div>
                  <table class="news"><?php CRenderer::RenderControl("NewsList"); ?></table></td>
                </td>
                <td rowspan="2" style="width: 10px;"><img src="/images/front/0.gif" height="1" width="10" alt="" /></td>
                <td class="resident_comments" width="50%">
                  <div class="commentTitle area">����� �����������</div>
                  <iframe src="/scroll/msg?res_type=area" class="comment" frameborder="0" scrolling="no">��� ������� �� ������������ ������</iframe>
                </td>
              </tr>
              <tr style="vertical-align: top;">
                <td><br />
                <div class="resident_news" style="height:375px;">
                  <div class="recomendTitle area"><a class="area" href="/area/rating">������� ��������</a></div>
                  <table class="rate"><?php CRenderer::RenderControl("RatingList"); ?></table>
                </div>
                </td>
              </tr>
            </table>
            					
                <?php /*
                <div class="commentTitle area"><a href="/random_resident/type/area" target="rnd"><img src="/images/refresh_area.png" style="margin: 1px 10px 0 0;"><span class="area" style="vertical-align: top;">��������� ��������</span></a></div>
								<iframe src="/random_resident/type/area" class="random" name="rnd" frameborder="0" scrolling="no">��� ������� �� ������������ ������</iframe>
                */ ?>			
           
								<div>
									<h1 class="recomendTitle area">����� ��� ��������� � ������ ��������</h1>
									<p>����� ������ ������� ������������ ����������� � ��� ������� ��������� ������ �������������� �����-���������, �������� �����������, � ������ ������������ �����. ��� ���� ����� ������� � ����������  ��������, ��������������� ��������� ������������ �������, ���������� ������������ � ����� ��������. ������ ��������� ���� �����-�������, ������ �� ������, ���������� ��������������, ������� ������ (���� �������� ��������� �� � ���������) � ��������� ���������. � ����� �������� ����� �������� ��������� �������� ������ �������� ����, ������������ ����������������� ��� ������. ����� ��������� �������� ������� ��� �������� ��������,  ���������� ����� ��� ������ �����, ��������� ���� ��� ������� ������ � ������ �������� �������������� ��������� ��������� ��������, ���������� � ��������������. ����� ����������� ������� ������ ����������, ������ � ������� ��� �����������. ��������, � ��������� ��������� ������������� ����� ������� ����������, � ������� �������� � ������� ������ ���� ����������� ��� �����������. ����������� ���� ��� �����-������, ��������, ������ ��������������� ��������� ������� � ������������ �������, � �������� �������������� �� ���������� �������� �������������� ���������� ����������, ���������������� ��������������. � �������-������� ������ �������� ����� ����������� ������������, � ����������� ����� � ���������������, � ������ ������ � ���������� ��������� � ��� �����, ������ ����� ���������� ����������� ����������, � ����� ������� ���� � ������ ����������� ����� ���� ��������� ������ ����� ��� ����������.</p>
									<p>���������, ������� ���������� �� ��� ���� �����-�������� ����� ��������� ������� �������� ������������ ����� � �����������, ���������� � ������ ����� ��������� �����������. ��� �������� ���������� �� ��������, � ��������� ����� ��������� �������������. ����� ������� ������� � ������������ �����-������� ��������� ����� ��������� <a href="/resident_news"><b>������� � ���������</b></a> � ���������� �� ��� ������������, � � ������� <a href="/eventoteka"><b>����������</b></a> ���������� ������ ����� ���� ��� ������.</p>
								</div>
						<?php } else { ?>
								<?php CRenderer::RenderControl("yaListTop"); ?>
                                <?php CRenderer::RenderControl("titlefilterLinks"); ?>
								<table border="0" cellpadding="0" cellspacing="0" class="tableInline" style="margin: 0 0px 0 0; width:auto;"><?php CRenderer::RenderControl("areaList"); ?></table><br>
								<div><?php CRenderer::RenderControl("footerText"); ?></div>
						<?php } ?>
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
</body>
</html>
