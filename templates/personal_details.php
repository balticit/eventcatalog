<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>������� ��������� - EVENT �������</title>
	<?php CRenderer::RenderControl("metadata"); ?>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr><td><?php CRenderer::RenderControl("topLine");?></td></tr>
<tr><!-- ���������-->
	<td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;"><?php CRenderer::RenderControl("header"); ?></td>
</tr>
<tr><!--����-->
<td><?php CRenderer::RenderControl("menu"); ?>
<?php CRenderer::RenderControl("submenu"); ?>
<?php CRenderer::RenderControl("submenu1"); ?>
<?php CRenderer::RenderControl("submenu2"); ?>
<?php CRenderer::RenderControl("submenu3"); ?>
<?php CRenderer::RenderControl("submenu4"); ?>
</td>
</tr>
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px; height: 100%" valign="top">

	<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top;width:220px">
                    <div style="height:32px; padding-top:3px; width:213px; " ></div><div style="height:20px;"></div>
                    <table cellpadding="0" cellspacing="0" border="0" width="241">
                    <tr><td class="ram1"></td><td class="ram2"></td><td class="ram3"></td></tr>
                    <tr><td class="ram4"></td><td class="ram5">
                        <?php CRenderer::RenderControl("personalTypeList"); ?>
                        </td><td class="ram6"></td></tr>
                    <tr><td class="ram7"></td><td class="ram8"></td><td class="ram9"></td></tr>
                    </table>
                    <a id="witgetAddResident" href="/registration/?type=user">�������� ���������</a>
		</td>
		<td valign="top" >
			<div align="center" style="padding-left:24px;">
                <table border="0" cellpadding="0" cellspacing="0" >
                <tr>
                    <td valign="center" align="left"><a class="regis_top" href="/registration/personal/type/vacancy" style="color: rgb(113, 113, 113);">�������� ��������</a></td>
            		<td align="left" valign="center"><a class="regis_top" href="/registration/personal/type/cv" style="color: rgb(113, 113, 113);">�������� ������</a></td>				
                </tr>										
                </table>
            </div>
            <div class="screen_content" >
                <div class="left_list">
                    <div style="margin-bottom: 15px;">
                        <div>
                            <table width=100% cellspacing=4><tr><?php CRenderer::RenderControl("typemenu"); ?></tr></table>
                        </div>
                        <br>
                        <div class="left_list" style="vertical-align:top">
                            <table border="0" cellpadding="0" cellspacing="0" class="tableInline" width="50%">
                            <tr>
                                <td align="left" valign="top" style="color: #AAAAAA; font-size:10px;"><div style=" padding-left:7px">���������</div></td>
                                <td align="right" valign="top" style="color: #AAAAAA; font-size:10px;"><div style="margin-right:3px">���� ���������� ����������</div></td>
                            </tr></table>
                            <?php CRenderer::RenderControl("cvdetails"); ?>
                            <?php CRenderer::RenderControl("vacancydetails"); ?>
                        </div>
                    </div>
                </div>
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
