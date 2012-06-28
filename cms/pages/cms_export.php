<?php
/****************************************************************
* Script         : PHP скрипт формирования отчетов в Excell файл
* Author         : LiO 
* Version        : 0.1
* Copyright      : GNU LGPL
* URL            : http://lio.kz
* Last modified  : 08.07.2004
* Description    : Объект формирования xls файла. Файл может быть
* сохранен физически на диск для дальнейшей работы (передача,
* отправка на e-mail и т.д.). Так же можно сразу "показать" структуру
* Данный скрипт поддерживает формат Excell 5, с ограничениями -
* не более 255 столбцов, и 65535 строк.
*
* Отлечистельные особенности от других скриптов
*  1. Формирования атрибутов ячеек (обрамление)
*  2. Отдельная запись целых чисел и вещественных
*  3. Корректировка стилей шрифтов и размера
******************************************************************/

class PhpToExcell {
        var $data = "";          // данные структуры
        
        // формирования заголовка открытия
        function ExBOF() {
                // begin of the excel file header
                $this->data = pack("c*", 0x09, 0x00, 0x04, 0x00, 0x02, 0x00, 0x10, 0x0);
        }
        
        // формирования заголовка закрытия
        function ExEOF() {
                $this->data .= pack("cc", 0x0A, 0x00);
        }
        
        // Переводит в строку attr1..3
        function RowAttr($attr1,$attr2,$attr3) {
                return chr($attr1).chr($attr2).chr($attr3);
        }
        
        // устанавливает русский язык в структура xls
        function Rus() {
                $this->data .= (chr(0x42).chr(0x00).chr(0x02).chr(0x00).chr(0x01).chr(0x80));
        }
        
        // запись строки
        // Col,Row - колонка и строка
        // attr1 - атрибут показа ячейки и защита от записи
        // attr2 - размер шрифта
        // attr3 - обрамление ячейки
        // для формирования атрибутов используйте соотвествующие функции
        function WriteLabel($Col,$Row,$attr1=0,$attr2=0,$attr3=0,$value) {// { Запись String }
                $i=strlen($value);
                $this->data .= pack("v*",0x04,8+$i,$Col,$Row);
                $this->data .= $this->RowAttr($attr1,$attr2,$attr3);
                $this->data .= pack("c",$i);
                $this->data .= $value;
        }
        
        // установка ширины колонки  Width*1/256
        //                           3000 - 100% }
        function ColWidth($ColFirst,$ColLast,$Width) {
                $this->data .= (CHR(0x24).CHR(00).Chr(04).CHR(00).chr($ColFirst).chr($ColLast).pack('s',$Width));
        }
        
        // Управляет видом колонок и строк при
        // ReferenceMode=1 Стиль ссылок = R1C1
        // ReferenceMode=0 Стиль ссылок стандартный A1...
        function RefMode($ReferenceMode=1) {
                if ($ReferenceMode==1) {
                        $this->data .= (CHR(0x0f).chr(0x00).chr(2).chr(0x00).chr(0x00).chr(0x00));
                } else {
                        $this->data .= (CHR(0x0f).chr(0x00).chr(2).chr(0x00).chr(0x00).chr(0x01));
                }
        }
        
        // запись целого числа
        function WriteInteger($Col,$Row,$attr1,$attr2,$attr3,$value=0) {
                $this->data.=pack("v*",0x02,0x09,$Col,$Row);
                $this->data.=$this->RowAttr($attr1,$attr2,$attr3);
                $this->data.=pack("v",$value);
        }
        
        // запись дробного числа
        function WriteNumber($Col,$Row,$attr1,$attr2,$attr3,$value=0.00) {
                $this->data.=pack("v*",0x03,0x0F,$Col,$Row);
                $this->data.=$this->RowAttr($attr1,$attr2,$attr3);
                $this->data.=pack("d",$value);
        }
        
        // запись пустой ячейки
        function WriteBlank($Col,$Row,$attr1,$attr2,$attr3)     {
                $this->data.=pack("v*",0x01,0x07,$Col,$Row);
                $this->data.=$this->RowAttr($attr1,$attr2,$attr3);
        }
        
        // Установка шрифта. Height*1/20
        //      Для 10 пунктов Height = 200   }
        function Font($Height,$Bold=0,$Italic=0,$Underline=0,$StrikeOut=0,$FontName) {
                $i=strlen($FontName);
                $this->data.=CHR(0x31).chr(0x00).Chr($i+5).chr(0x00);
                $this->data.=pack("v",$Height);
                $k=0;
                if ($Bold==1) $k=$k|1;
                if ($Italic==1) $k=$k|2;
                if ($Underline==1) $k=$k|4;
                if ($StrikeOut==1) $k=$k|8;
                $this->data.=pack("v",$k);
                $this->data.=chr($i);
                $this->data.=$FontName;
        }
        
        // Формируем аттрибут №1 ($Attr1)
        // $CellHidden - скрыть формулы
        // $CellLocked - защищенная ячейка
        function Attr1($CellHidden=0,$CellLocked=0) {
                $r=0x0;
                if ($CellHidden==1)$r=$r|128;
                if ($CellLocked==1)$r=$r|64;
                return $r;
        }
        
        // Формируем аттрибут №2 ($Attr2)
        // $FontNumber 0..3
        function Attr2($FontNumber=0) {
                $r=0;
                switch ($FontNumber) {
                        case 1:
                                $r=64;
                                break;
                        case 2:
                                $r=128;
                                break;
                        case 3:
                                $r=129;
                                break;
                }
                return $r;
        }
        
        // Формируем аттрибут №3 ($Attr3)
        // Alignment  0 - General
        //            1 - left
        //            2 - center
        //            3 - Right
        //            4 - Fill
        function Attr3($Shaded=0,$BottomBorder=0,$TopBorder=0,$RightBorder=0,$LeftBorder=0,$Alignment=0) {
                $r=0;
                if ($Shaded==1) $r=$r|128;
                if ($BottomBorder==1) $r=$r|64;
                if ($TopBorder==1) $r=$r|32;
                if ($RightBorder==1) $r=$r|16;
                if ($LeftBorder==1) $r= $r|8;
                if ($Alignment<8) $r=$r|$Alignment;
                return $r;
        }
        
        // сохранение данных в xls файл
        function SaveToFileXls($FName='file.xls') {
                $fp = fopen( $FName, "wb" );
                fwrite( $fp,$this->data);
                fclose( $fp );
        }
        
        // показать структуру в web
        function SendFileToHTTP($FName='file.xls') {
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/x-msexcel" );
                header ( "Content-Disposition: attachment; filename=".$FName );
                print $this->data;
        }
}



	class cms_export_php extends CCMSPageCodeHandler 
	{
		public function cms_export_php()
		{
			$this->CCMSPageCodeHandler();
		}
		
		public function PreRender()
		{
			$table = GP("table");
			/*//header("Content-type: multipart/mixed");
			header('Content-Disposition: attachment; filename="'.$table.'.xml"');
			
			if ($table!='data')
			{
				$exTable = $this->GetControl($table);
				if (!is_null($exTable))
				{
					$data = $this->GetControl("data");
					$data->template = $exTable->Render();
				}
			}*/
			
			$xls = new PhpToExcell(); // создание объекта
			
			$xls->ExBOF(); // маркер начала структуры
			$xls->Rus(); // установка русского языка
			$xls->RefMode(0);
			
			$filename = "data_table.xls";
			
			switch ($table) {
			
				case "areas" :
					$xls->ColWidth(0,0,10000);
					$xls->ColWidth(1,1,8000);
					$xls->ColWidth(2,2,10000);
					$xls->ColWidth(3,3,4000);
					$xls->ColWidth(4,4,12000);
					$xls->ColWidth(5,6,12000);
					$areas = SQLProvider::ExecuteQuery("select title,
						address,
						date_open,
						phone,
						site_address,
						max_count_man,
						(select GROUP_CONCAT(tbl__area_types.title SEPARATOR ', ') from tbl__area_types,tbl__area2type where tbl__area_types.tbl_obj_id=tbl__area2type.type_id and tbl__area2type.area_id=tbl__area_doc.tbl_obj_id) as type,
						(select GROUP_CONCAT(tbl__area_subtypes.title SEPARATOR ', ') from tbl__area_subtypes,tbl__area2subtype where tbl__area_subtypes.tbl_obj_id=tbl__area2subtype.subtype_id and tbl__area2subtype.area_id=tbl__area_doc.tbl_obj_id) as subtype,
						
						(select GROUP_CONCAT(tbl__area_halls.title SEPARATOR ', ') from tbl__area_halls where tbl__area_halls.area_id =tbl__area_doc.tbl_obj_id) as zal,
						(select GROUP_CONCAT(tbl__area_halls.max_places_banquet SEPARATOR ', ') from tbl__area_halls where tbl__area_halls.area_id =tbl__area_doc.tbl_obj_id) as banquet,
						(select GROUP_CONCAT(tbl__area_halls.max_places_official_buffet SEPARATOR ', ') from tbl__area_halls where tbl__area_halls.area_id =tbl__area_doc.tbl_obj_id) as buffet,
						(select tbl__city.title from tbl__city where tbl__city.tbl_obj_id = tbl__area_doc.city) city_name,
						email
						from tbl__area_doc where active=1 order by title");
					foreach ($areas as $key=>$area) {
						$xls->WriteLabel($key,0,0,0,0,$area["title"]);
						$xls->WriteLabel($key,1,0,0,0,$area["phone"]);
						$xls->WriteLabel($key,2,0,0,0,$area["site_address"]);
						$xls->WriteLabel($key,3,0,0,0,$area["max_count_man"]);
						$xls->WriteLabel($key,4,0,0,0,$area["type"]);
						$xls->WriteLabel($key,5,0,0,0,$area["subtype"]);
						$xls->WriteLabel($key,6,0,0,0,$area["address"]);
						$xls->WriteLabel($key,7,0,0,0,$area["zal"]);
						$xls->WriteLabel($key,8,0,0,0,$area["banquet"]);
						$xls->WriteLabel($key,9,0,0,0,$area["buffet"]);
						$xls->WriteLabel($key,10,0,0,0,$area["date_open"]);
						$xls->WriteLabel($key,11,0,0,0,$area["city_name"]);
						$xls->WriteLabel($key,12,0,0,0,$area["email"]);
					}
					$filename = "areas_table.xls";
				break;
				
				case "contractors" :
					$xls->ColWidth(0,0,10000);
					$xls->ColWidth(1,1,4000);
					$xls->ColWidth(2,2,4000);
					$xls->ColWidth(3,3,10000);
					$xls->ColWidth(4,4,8000);
					$xls->ColWidth(5,6,12000);
					$areas = SQLProvider::ExecuteQuery("select title,
						address,
						phone,
						phone2,
						site_address,
						(select GROUP_CONCAT(tbl__activity_type.title SEPARATOR ', ') from tbl__activity_type,tbl__contractor2activity where tbl__activity_type.tbl_obj_id=tbl__contractor2activity.kind_of_activity and tbl__contractor2activity.tbl_obj_id=tbl__contractor_doc.tbl_obj_id and tbl__activity_type.parent_id=0) as type,
						(select GROUP_CONCAT(tbl__activity_type.title SEPARATOR ', ') from tbl__activity_type,tbl__contractor2activity where tbl__activity_type.tbl_obj_id=tbl__contractor2activity.kind_of_activity and tbl__contractor2activity.tbl_obj_id=tbl__contractor_doc.tbl_obj_id and tbl__activity_type.parent_id<>0) as subtype,
						(select tbl__city.title from tbl__city where tbl__city.tbl_obj_id = tbl__contractor_doc.city) as city_name,
						email,
						description
						from tbl__contractor_doc where active=1 order by title");
					foreach ($areas as $key=>$area) {
						$xls->WriteLabel($key,0,0,0,0,$area["title"]);
						$xls->WriteLabel($key,1,0,0,0,$area["phone"]);
						$xls->WriteLabel($key,2,0,0,0,$area["phone2"]);
						$xls->WriteLabel($key,3,0,0,0,$area["site_address"]);
						$xls->WriteLabel($key,4,0,0,0,$area["type"]);
						$xls->WriteLabel($key,5,0,0,0,$area["subtype"]);
						$xls->WriteLabel($key,6,0,0,0,$area["address"]);
						$xls->WriteLabel($key,7,0,0,0,$area["city_name"]);
						$xls->WriteLabel($key,8,0,0,0,$area["email"]);
						$ddd = $area["description"];
						$n = strlen($ddd);
						$col = 9;
						for ($i = 0; $i < $n; $i += 200) 
						{
							$xls->WriteLabel($key,$col,0,0,0,substr($ddd,$i,200));
							$col++;
						}						
					}
					$filename = "contractors_table.xls";
				break;
				
				case "artists" :
					$xls->ColWidth(0,0,10000);
					$xls->ColWidth(1,1,5000);
					$xls->ColWidth(2,2,10000);
					$xls->ColWidth(3,3,8000);
					$xls->ColWidth(4,4,12000);
					$areas = SQLProvider::ExecuteQuery("select title,
						manager_phone,
						site_address,
						(select title from tbl__artist_group where tbl__artist_group.tbl_obj_id=tbl__artist_doc.group limit 1) as type,
						(select GROUP_CONCAT(tbl__artist_subgroup.title SEPARATOR ', ') from tbl__artist_subgroup,tbl__artist2subgroup where tbl__artist_subgroup.tbl_obj_id=tbl__artist2subgroup.subgroup_id and tbl__artist2subgroup.artist_id=tbl__artist_doc.tbl_obj_id) as subtype,
						(select tbl__countries.title from tbl__countries where tbl__countries.tbl_obj_id = tbl__artist_doc.country) country_name,
						email,
						manager_name,
						description
						from tbl__artist_doc where active=1 order by title");
					foreach ($areas as $key=>$area) {
						$xls->WriteLabel($key,0,0,0,0,$area["title"]);
						$xls->WriteLabel($key,1,0,0,0,$area["manager_phone"]);
						$xls->WriteLabel($key,2,0,0,0,$area["site_address"]);
						$xls->WriteLabel($key,3,0,0,0,$area["type"]);
						$xls->WriteLabel($key,4,0,0,0,$area["subtype"]);
						$xls->WriteLabel($key,5,0,0,0,$area["country_name"]);
						$xls->WriteLabel($key,6,0,0,0,$area["email"]);
						$xls->WriteLabel($key,7,0,0,0,$area["manager_name"]);
						$ddd = $area["description"];
						$n = strlen($ddd);
						$col = 8;
						for ($i = 0; $i < $n; $i += 200) 
						{
							$xls->WriteLabel($key,$col,0,0,0,substr($ddd,$i,200));
							$col++;
						}
					}
					$filename = "artists_table.xls";
				break;
				
				case "agencies" :
					$xls->ColWidth(0,0,10000);
					$xls->ColWidth(1,1,5000);
					$xls->ColWidth(2,2,5000);
					$xls->ColWidth(3,3,10000);
					$xls->ColWidth(4,4,8000);
					$areas = SQLProvider::ExecuteQuery("select title,
						address,
						phone,
						phone2,
						site_address,
						(select title from tbl__agency_type where tbl__agency_type.tbl_obj_id=tbl__agency_doc.kind_of_activity limit 1) as type,
						(select tbl__city.title from tbl__city where tbl__city.tbl_obj_id = tbl__agency_doc.city) as city_name,
						email,
						description
						from tbl__agency_doc where active=1 order by title");
					foreach ($areas as $key=>$area) {
						$xls->WriteLabel($key,0,0,0,0,$area["title"]);
						$xls->WriteLabel($key,1,0,0,0,$area["phone"]);
						$xls->WriteLabel($key,2,0,0,0,$area["phone2"]);
						$xls->WriteLabel($key,3,0,0,0,$area["site_address"]);
						$xls->WriteLabel($key,4,0,0,0,$area["type"]);
						$xls->WriteLabel($key,5,0,0,0,$area["address"]);
						$xls->WriteLabel($key,6,0,0,0,$area["city_name"]);
						$xls->WriteLabel($key,7,0,0,0,$area["email"]);
						$ddd = $area["description"];
						$n = strlen($ddd);
						$col = 8;
						for ($i = 0; $i < $n; $i += 200) 
						{
							$xls->WriteLabel($key,$col,0,0,0,substr($ddd,$i,200));
							$col++;
						}					
					}
					$filename = "agencies_table.xls";
				break;

				case "users" :
					$xls->ColWidth(0,0,4000);
					$xls->ColWidth(1,1,10000);
					$xls->ColWidth(1,2,10000);
					$xls->ColWidth(2,3,5000);
					$xls->ColWidth(3,4,5000);
					$xls->ColWidth(4,5,5000);
					$xls->ColWidth(5,6,10000);
					$xls->ColWidth(6,7,10000);
					$xls->ColWidth(7,8,8000);
					$xls->ColWidth(8,9,5000);
					$xls->ColWidth(9,10,10000);
					$xls->ColWidth(10,11,8000);
					$xls->ColWidth(11,12,8000);
					$xls->ColWidth(12,13,8000);
					$xls->ColWidth(13,14,5000);                                    
                    $users = SQLProvider::ExecuteQuery("					select 
                    ru.tbl_obj_id,
                    rut.user_types,
                    ru.title,
                    if(ru.city>0,c.title,ru.sity) as city,
                    contact_phone,
                    email,
                    company,
                    field_of_activity,
                    `position`,
                    company_phone,
                    address,
                    login,
                    nikname,
                    forum_name,
                    registration_date,
                    registration_confirmed
                    from tbl__registered_user ru
                    left join tbl__city c on ru.city=c.tbl_obj_id
                    left join 
                      (select user_id, GROUP_CONCAT(user_type SEPARATOR ', ') user_types
                       from tbl__registered_user_types group by user_id) rut on rut.user_id = ru.tbl_obj_id
                            where ru.active=1 order by tbl_obj_id desc");	
					$xls->WriteLabel(0,0,0,0,$xls->Attr3(0,0,0,0,0,2),"ID");
                    $xls->WriteLabel(0,1,0,0,$xls->Attr3(0,0,0,0,0,2),"Имя");
                    $xls->WriteLabel(0,2,0,0,$xls->Attr3(0,0,0,0,0,2),"Тип пользователя");					
					$xls->WriteLabel(0,3,0,0,$xls->Attr3(0,0,0,0,0,2),"Город");
					$xls->WriteLabel(0,4,0,0,$xls->Attr3(0,0,0,0,0,2),"Телефон");
					$xls->WriteLabel(0,5,0,0,$xls->Attr3(0,0,0,0,0,2),"E-mail");
					$xls->WriteLabel(0,6,0,0,$xls->Attr3(0,0,0,0,0,2),"Компания");
					$xls->WriteLabel(0,7,0,0,$xls->Attr3(0,0,0,0,0,2),"field_of_activity");
					$xls->WriteLabel(0,8,0,0,$xls->Attr3(0,0,0,0,0,2),"Должность");
					$xls->WriteLabel(0,9,0,0,$xls->Attr3(0,0,0,0,0,2),"Телефон компании");
					$xls->WriteLabel(0,10,0,0,$xls->Attr3(0,0,0,0,0,2),"Адрес");
					$xls->WriteLabel(0,11,0,0,$xls->Attr3(0,0,0,0,0,2),"Логин");
                    $xls->WriteLabel(0,12,0,0,$xls->Attr3(0,0,0,0,0,2),"Никнэйм");
					$xls->WriteLabel(0,13,0,0,$xls->Attr3(0,0,0,0,0,2),"Имя на форуме");
					$xls->WriteLabel(0,14,0,0,$xls->Attr3(0,0,0,0,0,2),"Дата регистрации");
					$xls->WriteLabel(0,15,0,0,$xls->Attr3(0,0,0,0,0,2),"Рег. подтверждена");
					foreach ($users as $key=>$user) {
						$xls->WriteInteger($key+1,0,0,0,0,$user["tbl_obj_id"]);
						$xls->WriteLabel($key+1,1,0,0,0,$user["title"]);
                        $xls->WriteLabel($key+1,2,0,0,0,$user["user_types"]);
						$xls->WriteLabel($key+1,3,0,0,0,$user["city"]);
						$xls->WriteLabel($key+1,4,0,0,0,$user["contact_phone"]);
						$xls->WriteLabel($key+1,5,0,0,0,$user["email"]);
						$xls->WriteLabel($key+1,6,0,0,0,$user["company"]);
						$xls->WriteLabel($key+1,7,0,0,0,$user["field_of_activity"]);
						$xls->WriteLabel($key+1,8,0,0,0,$user["position"]);
						$xls->WriteLabel($key+1,9,0,0,0,$user["company_phone"]);
						$xls->WriteLabel($key+1,10,0,0,0,$user["address"]);
						$xls->WriteLabel($key+1,11,0,0,0,$user["login"]);
						$xls->WriteLabel($key+1,12,0,0,0,$user["nikname"]);
						$xls->WriteLabel($key+1,13,0,0,0,$user["forum_name"]);
						$xls->WriteLabel($key+1,14,0,0,0,date("d.m.Y H:i:s",strtotime($user["registration_date"])));
						$xls->WriteLabel($key+1,15,0,0,0,$user["registration_confirmed"]=="1"?"да":"нет");
					}
					$filename = "users_table.xls";
				break;			
			}
			
			/*$xls->ColWidth(3,3,15000); // установка ширины колонки
			$xls->ColWidth(2,2,25600);
			
			$xls->WriteLabel(0,3,0,0,0,'Тестовая строка'); // запись строки
			$xls->WriteInteger(1,3,0,0,0,1223); // запись целого числа
			$xls->WriteNumber(0,0,0,0,0,0.20); // запись дробного числа
			$xls->WriteBlank(1,5,0,0,0); // запись пустой ячейки*/
			
			$xls->ExEOF(); // маркер конца структуры
			
			//$xls->SaveToFileXls('data_table.xls'); // сохранить файл
			$xls->SendFileToHTTP($filename); // вывести клиенту 
		}
	}
?>
