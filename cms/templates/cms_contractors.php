<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<link rel="stylesheet" type="text/css" href="/cms/templates/css/cms.css"  >
<script type="text/javascript" language="javascript" src="/cms/templates/scripts/cms.js"></script>
</head>

<body>
<div style="padding-bottom:7px;"><form action="/cms/contractors" method="get">		
	<center>
		<table cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td width="100%" valign="bottom">
				<table width="100%"  cellpadding="0" cellspacing="0">
				  <tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td><img src="/images/search/leftind.gif"></td>
								<td background="/images/search/bgind.gif" width="100%" style="vertical-align: top; padding-top: 5px;">
								
									<input  value="" onchange="this.value=value + '%';" type="text" name="99_4$dataTable[$autoValues][title]" style="width: 100%; border: 0px black solid; color: gray;">
								</td>
								<td><img src="/images/search/rightind.gif"></td>
							</tr>
						</table>
					</td>
				  </tr>
				</table>

			</td>
			
			<td style="vertical-align:middle; padding-top:0px; padding-left:20px; padding-right: 10px; " valign="top"><input type="image"  src="/images/search/butind.gif"></td>
          </tr>
        </table>
	</center>
	</form>	</div>
<?php CRenderer::RenderControl("dataTable"); ?>

</body>
</html>
