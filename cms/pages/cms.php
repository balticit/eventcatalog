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
<li><span>Основное</span>
  <ul>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/about/');\">О сайте</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/contact/');\">Контакты</a></li>
      <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/partners/');\">Партнёры</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/partners_text/');\">Партнеры (текст)</a></li>
      <li><a href=\"\" onclick=\"return SetTable('/cms/search_queries');\">Статистика поиска</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=city');\">Города</a></li>
  <li> <a href=\"#\" onclick=\"javascript:return SetTable('/cms/magazine/');\">Издания</a></li>
  <li><a href=\"\" onclick=\"return SetTable('/cms/comments/');\">Комментарии</a></li>
    <li><a href=\"\" onclick=\"return SetTable('/cms/messages');\">Личные сообщения</a></li>
</ul>
</li>
<li><span>Пользователи</span>
  <ul>
    <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/users/');\">Каталог пользователей </a></li>
  <li>                        <a href=\"/cms/export?table=users\" target=\"_blank\">Экспорт </a></li>
  </ul>
</li>
<li><span>Подрядчики</span>
  <ul>
    <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/contractors/');\">Каталог подрядчиков </a></li>
  <li>            <a href=\"/cms/export?table=contractors\" target=\"_blank\">Экспорт </a></li>
  <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=activity_type');\">Категории подрядчиков</a></li>
  </ul>
 </li>
<li><span>Площадки</span>
  <ul>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/areas/');\">Каталог площадок </a></li>
    <li><a href=\"/cms/export?table=areas\" target=\"_blank\">Экспорт </a></li>

    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=area_type');\">Разделы площадок</a></li>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=area_subtype');\">Категории площадок</a></li>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=moscow_districts');\">Округа Москвы</a></li>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=metro_lines');\">Линии метро</a></li>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=metro_stations');\">Станции метро</a></li>
  </ul>
</li>
<li><span>Артисты</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/artists/');\">Каталог артистов </a></li>
  <li><a href=\"/cms/export?table=artists\" target=\"_blank\">Экспорт </a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=artist_group');\">Разделы артистов</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=artist_subgroup');\">Категории артистов</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=countries');\">Страна</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=styles_groups');\">Группы стилей</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=styles');\">Стиль</a></li>
  </ul>
</li>
<li><span>Агентства</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/agencies/');\">Каталог агентств </a></li>
  <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=agency_type');\">Категории агентств</a></li>
  <li>   <a href=\"/cms/export?table=agencies\" target=\"_blank\"> Экспорт </a></li>
  </ul>
</li>
<li><span>Учебник организатора</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/book/');\">Каталог статей</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=book_dir');\">Категории учебника</a></li>
  </ul>
</li>
<li><span>EVENT TV</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/eventtv/');\">Cтатьи</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=eventtv_topics');\">Рубрики</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=eventtv_photos');\">Фото на белом фоне</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/eventtv_invite');\">Пригласить EVENTTV</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/eventtv_main');\">Главная EVENTTV</a></li>
  </ul>
</li>
<li><span>Новостной блок</span>
  <ul>
  <li><a href=\"\" onclick=\"return SetTable('/cms/resident_news/');\">Новости резидентов</a></li>
  <li><a href=\"\" onclick=\"return SetTable('/cms/event_calendar/');\">Event календарь</a></li>
  <li><a href=\"\" onclick=\"return SetTable('/cms/news/');\">Новости индустрии</a></li>
  </ul>
</li>
<li><span>Реклама</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/pr/');\">Текст раздела</a></li>
  <li><a href=\"\" onclick=\"return SetTable('/cms/pricelist');\">Прайс-лист</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/ad_blocks');\">Текстовые блоки</a></li>
      <!--<li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/banners/locations/');\">Размещение баннеров </a></li>-->
  </ul>
</li>
<li><span>Рассылка</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/subscribe/');\">Ежедневная рассылка </a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/weeksubscribe/');\">Еженедельная рассылка </a></li>
      <li><a href=\"\" onclick=\"return SetTable('/cms/stop_subscribe');\">Отписавшиеся пользователи</a></li>
      <li><a href=\"\" onclick=\"return SetTable('/cms/user_subscribed');\">Незарегестрированные пользователи</a></li>
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
<li><span>Подрядчики</span>
  <ul>
    <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/contractors');\">Каталог подрядчиков </a></li>
  <li>            <a href=\"/cms/export?table=contractors\" target=\"_blank\">Экспорт </a></li>
  <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=activity_type');\">Категории подрядчиков</a></li>
      <li><a href=\"\" onclick=\"return SetTable('/cms/show_technologies');\">Новые шоу-технологии</a></li>
  </ul>
 </li>
<li><span>Площадки</span>
  <ul>
    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/areas');\">Каталог площадок </a></li>
    <li><a href=\"/cms/export?table=areas\" target=\"_blank\">Экспорт </a></li>

  <li> <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=area_subtype');\">Категории площадок</a></li>
      <li>            <a href=\"\" onclick=\"return SetTable('/cms/opened_areas');\">Новые площадки Москвы</a></li>
  </ul>
</li>
<li><span>Артисты</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/artists');\">Каталог артистов </a></li>
  <li>            <a href=\"/cms/export?table=artists\" target=\"_blank\">Экспорт </a></li>


  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=artist_subgroup');\">Категории артистов</a>	</li>
      <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=countries');\">Страна</a></li>
      <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=styles');\">Стиль</a></li>
    <li>            <a href=\"\" onclick=\"javascript:return SetTable('/cms/star_calendar/');\">Звёздный календарь</a></li>
  <li> <a href=\"\" onclick=\"javascript:return SetTable('/cms/star_shedule/');\">Звёздный календарь (текст)</a></li>
  </ul>
</li>
<li><span>Агентства</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/agencies');\">Каталог агентств </a></li>
  <li>            <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=agency_type');\">Категории агентств</a></li>
  <li>   <a href=\"/cms/export?table=agencies\" target=\"_blank\"> Экспорт </a></li>
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
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/agencies');\">Агентства</a>
        </td>
    <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=agency_type');\">Вид деятельности агентства</a>
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
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/areas');\">Площадки</a>
        </td>
    <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=area_type');\">Вид площадки</a>
        </td>
    <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=area_subtype');\">Тип площадки</a>
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
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/artists');\">Артисты</a>
        </td>
     <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=artist_group');\">Раздел</a>
        </td>
     <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=artist_subgroup');\">Подгруппа</a>
        </td>
     <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=styles');\">Стиль</a>
        </td>
     <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=countries');\">Страна</a>
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
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/personalcv');\">Резюме</a>
        </td>
        <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/personalvacancy');\">Вакансии</a>
        </td>
    <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=personal_type');\">Вид персонала</a>
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
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/junksalesell');\">Барахолка - Продажа</a>
        </td>
        <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/junksalebuy');\">Барахолка - Покупка</a>
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
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/news/');\">Новости (скоро)</a>
        </td>
        <td nowrap=\"nowrap\">
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/recent/');\">Новости (недавно)</a>
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
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/subscribe/');\">Рассылка</a>
        </td>
        <td nowrap=\"nowrap\">
          <a href=\"\" onclick=\"return SetTable('/cms/stop_subscribe');\">Пользователи, отказавшиеся от рассылки</a>
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
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/users');\">Пользователи</a>
        </td>

        <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/contractors');\">Подрядчики</a>
        </td>



        <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/agencies');\">Агентства</a>
        </td>


         <td nowrap=\"nowrap\">
          <a href=\"\" onclick=\"return SetTable('/cms/resident_news');\">Новости резидентов</a>
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
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/areas');\">Площадки</a>
        </td>

        <td>
          <a href=\"#\" onclick=\"javascript:return SetTable('/cms/artists');\">Артисты</a>
        </td>



       <td nowrap=\"nowrap\">
          <a href=\"\" onclick=\"javascript:return SetTable('/cms/star_calendar/');\">Звёздный календарь</a>

        </td>




      </tr>
    </table>";
        break;

case "12" :
  $admtype['firsttable'] = "/cms/book/";
  $admtype['admtype'] = "
<div style=\"height:32px; padding:5px 5px 5px 5px; width:145px;\" >
<ul id=\"navadm\">
<li><span>Учебник организатора</span>
  <ul>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/book/');\">Каталог статей</a></li>
  <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/refs/?table=book_dir');\">Категории учебника</a></li>
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
			<li><a href=\"\" onclick=\"return SetTable('/cms/comments/');\">Комментарии</a></li>
            <li><a href=\"\" onclick=\"return SetTable('/cms/messages');\">Личные сообщения</a></li>
		    <li><a href=\"#\" onclick=\"javascript:return SetTable('/cms/users/');\">Каталог пользователей </a></li>
		 
			<li><a href=\"\" onclick=\"return SetTable('/cms/resident_news/');\">Новости резидентов</a></li>
		    <li><a href=\"\" onclick=\"return SetTable('/cms/event_calendar/');\">Event календарь</a></li>
            <li><a href=\"\" onclick=\"return SetTable('/cms/news/');\">Новости индустрии</a></li>
         </ul>
	";
      }

    }
    $admdata = $this->GetControl("admtype");
    $admdata->dataSource = $admtype;
  }
}
?>