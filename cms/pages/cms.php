<?php
class cms_php extends CCMSPageCodeHandler
{
  public function cms_php()
  {
    $this->CCMSPageCodeHandler();
  }

  public function preRender()
  {
    $admtype = Array();
    $su = new CSessionUser(CMS_ADMIN_SESSION_KEY);
    if ($su->authorized) {
      switch ($su->admintype) {
case "1" :
  $admtype['firsttable'] = "/cms/users/";
  $admtype['admtype'] = "
<div style=\"height:32px; padding:5px 5px 5px 5px; width:145px;\" >

<ul id=\"navadm\">
<li><span>��������</span>
  <ul>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/about/');\">� �����</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/contact/');\">��������</a></li>
      <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/partners/');\">�������</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/partners_text/');\">�������� (�����)</a></li>
      <li><a href=\"\" onclick=\"return SetTable('/cms/search_queries');\">���������� ������</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=city');\">������</a></li>
  <li> <a href=\"#\" onclick=\"javascript:return SetTable('/cms/magazine/');\">�������</a></li>
  <li><a href=\"\" onclick=\"return SetTable('/cms/comments/');\">�����������</a></li>
    <li><a href=\"\" onclick=\"return SetTable('/cms/messages');\">������ ���������</a></li>
</ul>
</li>
<li><span>������������</span>
  <ul>
    <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/users/');\">������� ������������� </a></li>
  <li>                        <a href=\"/cms/export?table=users\" target=\"_blank\">������� </a></li>
  </ul>
</li>
<li><span>����������</span>
  <ul>
    <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/contractors/');\">������� ����������� </a></li>
  <li>            <a href=\"/cms/export?table=contractors\" target=\"_blank\">������� </a></li>
  <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=activity_type');\">��������� �����������</a></li>
  </ul>
 </li>
<li><span>��������</span>
  <ul>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/areas/');\">������� �������� </a></li>
    <li><a href=\"/cms/export?table=areas\" target=\"_blank\">������� </a></li>

    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=area_type');\">������� ��������</a></li>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=area_subtype');\">��������� ��������</a></li>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=moscow_districts');\">������ ������</a></li>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=metro_lines');\">����� �����</a></li>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=metro_stations');\">������� �����</a></li>
  </ul>
</li>
<li><span>�������</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/artists/');\">������� �������� </a></li>
  <li><a href=\"/cms/export?table=artists\" target=\"_blank\">������� </a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=artist_group');\">������� ��������</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=artist_subgroup');\">��������� ��������</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=countries');\">������</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=styles_groups');\">������ ������</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=styles');\">�����</a></li>
  </ul>
</li>
<li><span>���������</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/agencies/');\">������� �������� </a></li>
  <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=agency_type');\">��������� ��������</a></li>
  <li>   <a href=\"/cms/export?table=agencies\" target=\"_blank\"> ������� </a></li>
  </ul>
</li>
<li><span>������� ������������</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/book/');\">������� ������</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=book_dir');\">��������� ��������</a></li>
  </ul>
</li>
<li><span>EVENT TV</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/eventtv/');\">C�����</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=eventtv_topics');\">�������</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=eventtv_photos');\">���� �� ����� ����</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/eventtv_invite');\">���������� EVENTTV</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/eventtv_main');\">������� EVENTTV</a></li>
  </ul>
</li>
<li><span>��������� ����</span>
  <ul>
  <li><a href=\"\" onclick=\"return SetTable('/cms/resident_news/');\">������� ����������</a></li>
  <li><a href=\"\" onclick=\"return SetTable('/cms/event_calendar/');\">Event ���������</a></li>
  <li><a href=\"\" onclick=\"return SetTable('/cms/news/');\">������� ���������</a></li>
  </ul>
</li>
<li><span>�������</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/pr/');\">����� �������</a></li>
  <li><a href=\"\" onclick=\"return SetTable('/cms/pricelist');\">�����-����</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/ad_blocks');\">��������� �����</a></li>
      <!--<li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/banners/locations/');\">���������� �������� </a></li>-->
  </ul>
</li>
<li><span>��������</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/subscribe/');\">���������� �������� </a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/weeksubscribe/');\">������������ �������� </a></li>
      <li><a href=\"\" onclick=\"return SetTable('/cms/stop_subscribe');\">������������ ������������</a></li>
      <li><a href=\"\" onclick=\"return SetTable('/cms/user_subscribed');\">�������������������� ������������</a></li>
  </ul>
</li>
</ul>
</div>

  ";
        break;

        case "2" :
          $admtype['firsttable'] = "/cms/contractors";
          $admtype['admtype'] = "

  <div style=\"height:32px; padding:5px 5px 5px 5px; width:135px;\" >

<ul id=\"navadm\">
<li><span>����������</span>
  <ul>
    <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/contractors');\">������� ����������� </a></li>
  <li>            <a href=\"/cms/export?table=contractors\" target=\"_blank\">������� </a></li>
  <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=activity_type');\">��������� �����������</a></li>
      <li><a href=\"\" onclick=\"return SetTable('/cms/show_technologies');\">����� ���-����������</a></li>
  </ul>
 </li>
<li><span>��������</span>
  <ul>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/areas');\">������� �������� </a></li>
    <li><a href=\"/cms/export?table=areas\" target=\"_blank\">������� </a></li>

  <li> <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=area_subtype');\">��������� ��������</a></li>
      <li>            <a href=\"\" onclick=\"return SetTable('/cms/opened_areas');\">����� �������� ������</a></li>
  </ul>
</li>
<li><span>�������</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/artists');\">������� �������� </a></li>
  <li>            <a href=\"/cms/export?table=artists\" target=\"_blank\">������� </a></li>


  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=artist_subgroup');\">��������� ��������</a>	</li>
      <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=countries');\">������</a></li>
      <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=styles');\">�����</a></li>
    <li>            <a href=\"\" onclick=\"javascript:return SetTable('/cms/star_calendar/');\">������� ���������</a></li>
  <li> <a href=\"\" onclick=\"javascript:return SetTable('/cms/star_shedule/');\">������� ��������� (�����)</a></li>
  </ul>
</li>
<li><span>���������</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/agencies');\">������� �������� </a></li>
  <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=agency_type');\">��������� ��������</a></li>
  <li>   <a href=\"/cms/export?table=agencies\" target=\"_blank\"> ������� </a></li>
  </ul>
</li>
</ul>
</div>
  ";

          break;



        case "3" :
          $admtype['firsttable'] = "/cms/agencies";
          $admtype['admtype'] = "
    <table class=\"menuTable\" border=\"1\">
      <tr>
        <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/agencies');\">���������</a>
        </td>
    <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=agency_type');\">��� ������������ ���������</a>
        </td>
  </tr>
  </table>";

          break;



        case "4" :
          $admtype['firsttable'] = "/cms/areas";
          $admtype['admtype'] = "
    <table class=\"menuTable\" border=\"1\">
      <tr>
        <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/areas');\">��������</a>
        </td>
    <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=area_type');\">��� ��������</a>
        </td>
    <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=area_subtype');\">��� ��������</a>
        </td>
  </tr>
  </table>";

          break;



        case "5" :
          $admtype['firsttable'] = "/cms/artists";
          $admtype['admtype'] = "
    <table class=\"menuTable\" border=\"1\">
      <tr>
       <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/artists');\">�������</a>
        </td>
     <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=artist_group');\">������</a>
        </td>
     <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=artist_subgroup');\">���������</a>
        </td>
     <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=styles');\">�����</a>
        </td>
     <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=countries');\">������</a>
        </td>
  </tr>
  </table>";

          break;



        case "6" :
          $admtype['firsttable'] = "/cms/personalcv";
          $admtype['admtype'] = "
    <table class=\"menuTable\" border=\"1\">
      <tr>
        <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/personalcv');\">������</a>
        </td>
        <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/personalvacancy');\">��������</a>
        </td>
    <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=personal_type');\">��� ���������</a>
        </td>
  </tr>
  </table>";

          break;



        case "7" :
          $admtype['firsttable'] = "/cms/junksalesell";
          $admtype['admtype'] = "
    <table class=\"menuTable\" border=\"1\">
      <tr>
        <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/junksalesell');\">��������� - �������</a>
        </td>
        <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/junksalebuy');\">��������� - �������</a>
        </td>
  </tr>
  </table>";

          break;



        case "8" :
          $admtype['firsttable'] = "/cms/news";
          $admtype['admtype'] = "
    <table class=\"menuTable\" border=\"1\">
      <tr>
        <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/news/');\">������� (�����)</a>
        </td>
        <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/recent/');\">������� (�������)</a>
        </td>
  </tr>
  </table>";

          break;


        case "9" :
          $admtype['firsttable'] = "/cms/subscribe";
          $admtype['admtype'] = "
    <table class=\"menuTable\" border=\"1\">
      <tr>
        <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/subscribe/');\">��������</a>
        </td>
        <td nowrap=\"nowrap\">
          <a href=\"\" onclick=\"return SetTable('/cms/stop_subscribe');\">������������, ������������ �� ��������</a>
        </td>
  </tr>
  </table>";

          break;

        case "10" :
          $admtype['firsttable'] = "/cms/users";
          $admtype['admtype'] = "
    <table class=\"menuTable\" border=\"1\">
      <tr>
        <td nowrap=\"nowrap\" valign=\"middle\" align=\"center\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/users');\">������������</a>
        </td>

        <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/contractors');\">����������</a>
        </td>



        <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/agencies');\">���������</a>
        </td>


         <td nowrap=\"nowrap\">
          <a href=\"\" onclick=\"return SetTable('/cms/resident_news');\">������� ����������</a>
        </td>

      </tr>
    </table>";
        break;

      case "11" :
          $admtype['firsttable'] = "/cms/users";
          $admtype['admtype'] = "
    <table class=\"menuTable\" border=\"1\">
      <tr>
         <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/areas');\">��������</a>
        </td>

        <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/artists');\">�������</a>
        </td>



       <td nowrap=\"nowrap\">
          <a href=\"\" onclick=\"javascript:return SetTable('/cms/star_calendar/');\">������� ���������</a>

        </td>




      </tr>
    </table>";
        break;

case "12" :
  $admtype['firsttable'] = "/cms/book/";
  $admtype['admtype'] = "
<div style=\"height:32px; padding:5px 5px 5px 5px; width:145px;\" >
<ul id=\"navadm\">
<li><span>������� ������������</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/book/');\">������� ������</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=book_dir');\">��������� ��������</a></li>
  </ul>
</li>
</ul>
</div>
  ";
  break;
case "13":
    $admtype['firsttable'] = "/cms/comments/";
	$admtype['admtype'] = "	
<ul id=\"navadm\">
			<li><a href=\"\" onclick=\"return SetTable('/cms/comments/');\">�����������</a></li>
            <li><a href=\"\" onclick=\"return SetTable('/cms/messages');\">������ ���������</a></li>
		    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/users/');\">������� ������������� </a></li>
		 
			<li><a href=\"\" onclick=\"return SetTable('/cms/resident_news/');\">������� ����������</a></li>
		    <li><a href=\"\" onclick=\"return SetTable('/cms/event_calendar/');\">Event ���������</a></li>
            <li><a href=\"\" onclick=\"return SetTable('/cms/news/');\">������� ���������</a></li>
         </ul>
	";
      }

    }
    $admdata = $this->GetControl("admtype");
    $admdata->dataSource = $admtype;
  }
}
?>