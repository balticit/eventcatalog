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
  <tr>
    <td><?php CRenderer::RenderControl("topLine");?></td>
  </tr>
  <tr><!-- Заголовок-->
    <td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;">
    <?php CRenderer::RenderControl("header"); ?>
    </td>
  </tr>
  <tr><!--Меню-->
    <td>
      <?php CRenderer::RenderControl("menu"); ?>
      <?php CRenderer::RenderControl("submenu"); ?>

    </td>
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
        
        <?php CRenderer::RenderControl("artistsearch"); ?>
        
	<!--LIST RESIDENT-->
  <div class="resident-category-title artist"><?php CRenderer::RenderControl("titlefilter"); ?></div>
  <div class="category-desc"><?php CRenderer::RenderControl("footerText"); ?></div>
  <?php // CRenderer::RenderControl("yaListTop"); ?>
  <div class="subcategory-resident-list contractor"><?php CRenderer::RenderControl("artistList"); ?></div>
  <div class="pager"><?php CRenderer::RenderControl("pager"); ?></div>
  <!--END LIST RESIDENT-->
        

        </td>
        <?php require ROOTDIR.'templates/_leftMenuWitgets.php'; ?>
      </tr>
    </table>
  </td>
  </tr>
  <tr>
    <td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td>
  </tr>
  <tr>
    <td class="foot"><?php CRenderer::RenderControl("footer"); ?></td>
  </tr>
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

<?php CRenderer::RenderControl("artistsearch"); ?>

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
  $(".ui-dialog-titlebar-close, .ui-dialog-buttonset button").live('click',function(){
    $('.dialog-confirm').hide();
  });
});
</script>

</body>
</html>