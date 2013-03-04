<?php
require_once(ROOTDIR . "captcha/kcaptcha.php");
class registration_php extends CPageCodeHandler
{
    public $mailpath;
    public $mailtitle;

    public function registration_php()
    {
        $this->CPageCodeHandler();
    }

    private function ValidateCaptcha()
    {
        $sess_id = GP("comment_captcha", 0);
        $captcha = GP("comment_captcha_input", 0);
        return !IsNullOrEmpty($sess_id) &&
            !IsNullOrEmpty($captcha) &&
            ($_SESSION[$sess_id] == $captcha);
    }

    private function CreateImages($photos, $key, $title, &$lFilename, &$mFilename, &$sFilename)
    {
        if (isset($photos["name"][$key]) &&
            $photos["error"][$key] === 0 &&
            is_file($photos["tmp_name"][$key]) &&
            $photos["size"][$key] <= IMAGE_LAGRE_SIZE_LIMIT
        ) {
            $uploadtable = new CNativeDataTable("tbl__photo");
            $imgRow = $uploadtable->CreateNewRow(true);
            $imgRow->title = $title;
            $uploadtable->InsertObject(&$imgRow);
            $ext = "." . pathinfo($photos["name"][$key], PATHINFO_EXTENSION);

            $lFilename = "$imgRow->tbl_obj_id" . IMAGE_PATH_SEMISECTOR .
                IMAGE_FOTO_PREFIX . $ext;
            $mFilename = "$imgRow->tbl_obj_id" . IMAGE_PATH_SEMISECTOR .
                IMAGE_FOTO_PREFIX . "_medi" . $ext;
            $sFilename = "$imgRow->tbl_obj_id" . IMAGE_PATH_SEMISECTOR .
                IMAGE_FOTO_PREFIX . "_thumb" . $ext;
            if (is_file(ROOTDIR . IMAGES_UPLOAD_DIR . $lFilename))
                unlink(ROOTDIR . IMAGES_UPLOAD_DIR . $lFilename);
            if (is_file(ROOTDIR . IMAGES_UPLOAD_DIR . $mFilename))
                unlink(ROOTDIR . IMAGES_UPLOAD_DIR . $mFilename);
            if (is_file(ROOTDIR . IMAGES_UPLOAD_DIR . $sFilename))
                unlink(ROOTDIR . IMAGES_UPLOAD_DIR . $sFilename);
            $res = new ResizeImage($photos["tmp_name"][$key]);
            $res->resize(IMAGE_RES_MAX_WIDTH, IMAGE_RES_MAX_HEIGHT,
                ROOTDIR . IMAGES_UPLOAD_DIR . $lFilename, false);
            $res->resize(IMAGE_RES_MEDIUM_WIDTH, IMAGE_RES_MEDIUM_HEIGHT,
                ROOTDIR . IMAGES_UPLOAD_DIR . $mFilename, false);
            $res->resize(IMAGE_RES_THUMB_WIDTH, IMAGE_RES_THUMB_HEIGHT,
                ROOTDIR . IMAGES_UPLOAD_DIR . $sFilename, false);
            unset($res);
            $imgRow->l_image = $lFilename;
            $imgRow->m_image = $mFilename;
            $imgRow->s_image = $sFilename;
            $uploadtable->UpdateObject(&$imgRow);
            unset($uploadtable);
            return $imgRow->tbl_obj_id;
        }
        return null;
    }

    private function CreateLogo($login, $files, $key = "logo_file", $prefix = IMAGE_LOGO_PREFIX)
    {
        if (isset($files["name"][$key]) &&
            $files["error"][$key] === 0 &&
            is_file($files["tmp_name"][$key])
        ) {
            $login = PrepareImagePathPart($login);
            $ext = "." . pathinfo($files["name"][$key], PATHINFO_EXTENSION);
            $newfile = $login . IMAGE_PATH_SEMISECTOR . $prefix . $ext;
            $newpath = IMAGES_UPLOAD_DIR . $newfile;
            if (is_file(ROOTDIR . $newpath))
                unlink(ROOTDIR . $newpath);
            $res = new ResizeImage($files["tmp_name"][$key]);
            $res->resize(120, 80, ROOTDIR . $newpath, true);
            unset($res);
            return $newfile;
        }
        return null;
    }

    private function CleanLogo(&$file)
    {
        if (is_file(ROOTDIR . $file))
            unlink(ROOTDIR . $file);
        $file = "";
    }

    private function CheckCity($city)
    {
        $cities = SQLProvider::ExecuteQuery("select tbl_obj_id from `tbl__city`
          where `active`=1 and LOWER(`title`) = LOWER('$city')");
        if (sizeof($cities) > 0)
            return $cities[0]['tbl_obj_id'];
        else
            return null;
    }

    private function PrepareCity(&$udata)
    {
        if (is_numeric($udata->city)) {
            $udata->other_city = SQLProvider::ExecuteScalar("select title from `tbl__city`
          where `active`=1 and tbl_obj_id = $udata->city");
        }
    }

    private function CheckCountry($country)
    {
        $contries = SQLProvider::ExecuteQuery("select tbl_obj_id from `tbl__countries`
          where LOWER(`title`) = LOWER('$country')");
        if (sizeof($contries) > 0)
            return $contries[0]['tbl_obj_id'];
        else
            return null;
    }


    private function UpdateHalls(&$userData)
    {
        $halls = GP(array("properties", "hall"), array());
        $hallTable = new CNativeDataTable("tbl__area_halls");
        $hallTable->Delete(new CEqFilter(&$hallTable->fields["area_id"], $userData->tbl_obj_id));
        if (sizeof($halls) > 0) {
            foreach ($halls as $hall) {
                $hall["title"] = isset($hall["title"]) ? VType($hall["title"], "string", "") : "";
                $hRow = $hallTable->CreateNewRow(true);
                $hRow->FromHashMap($hall);
                $hRow->area_id = $userData->tbl_obj_id;
                $hallTable->InsertObject(&$hRow, false);
            }
        }
    }

    private function SetArtistStyles(&$userData)
    {
        $styles = GP(array("properties", "style"), array());
        SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2style where artist_id=$userData->tbl_obj_id");
        foreach ($styles as $style)
            SQLProvider::ExecuteNonReturnQuery("insert into tbl__artist2style values($userData->tbl_obj_id,$style)");

        $subgroups = GP(array("properties", "selected_group"), array());
        SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2subgroup where artist_id=$userData->tbl_obj_id");
        foreach ($subgroups as $subgroup)
            SQLProvider::ExecuteNonReturnQuery("insert into tbl__artist2subgroup values($userData->tbl_obj_id,$subgroup)");
    }

    private function CheckURL($type, $title_url)
    {
        $r = SQLProvider::ExecuteQuery("
      select tbl_obj_id from tbl__" . $type . "_doc
      where title_url = '" . mysql_real_escape_string($title_url) . "'");
        return sizeof($r);
    }


    public function PreRender()
    {
        $av_rwParams = array("type");
        CURLHandler::CheckRewriteParams($av_rwParams);

        $type = GP("type", "choose");
        $user = new CSessionUser($type);
        CAuthorizer::AuthentificateUserFromCookie(&$user);
        CAuthorizer::RestoreUserFromSession(&$user);
        if ($user->authorized) {
            switch ($user->type) {
                case "user":
                    CURLHandler::Redirect("/u_cabinet");
                    break;
                default:
                    CURLHandler::Redirect("/r_cabinet");
            }
        }

        $user->id = 0;

        $account = $this->GetControl("account");
        $account->key = $type;
        $errors = $this->GetControl("errors");
        $errors->dataSource = array("message" => "");

        $this->mailpath = "pagecode/settings/registration_files/registration_resident.htm";
        $c_id = md5(uniqid(rand(), true));
        $captcha = array("captcha_sid" => $c_id,
            "captcha_link" => "/captcha/sid.php?" . session_name() . "=" . session_id() . "&sid=" . $c_id);

        switch ($user->type) {
            // USER -------------------------------------------------------------------
            case "user":
                {
                $utdata = array(
                    "user_type_1" => "false",
                    "user_type_2" => "false",
                    "user_type_3" => "false",
                    "user_type_4" => "false",
                    "user_type_5" => "false",
                    "user_type_6" => "false",
                    "user_type_7" => "false",
                    "user_typeID_3" => "",
                    "user_typeID_4" => "",
                    "user_typeID_5" => "",
                    "user_typeID_6" => "",
                    "ut_other" => "");
                $this->mailpath = "pagecode/settings/registration_files/registration_user.htm";

                $table = new CNativeDataTable("tbl__registered_user");
                $userData = null;

                $citySelect = new CSelect();
                $citySelect->dataSource = SQLProvider::ExecuteQuery("select * from `tbl__city` ");
                array_push($citySelect->dataSource, array("tbl_obj_id" => -1, "title" => "другой город..."));
                $citySelect->titleName = "title";
                $citySelect->valueName = "tbl_obj_id";
                $citySelect->name = "properties[city]";
                $citySelect->style = array("width" => "300px");

                $regData = "Регистрация не подтверждена";
                $userData = $table->CreateNewRow(true);

                if ($this->IsPostBack) {
                    $utdata = array(
                        "user_type_1" => "false",
                        "user_type_2" => "false",
                        "user_type_3" => "false",
                        "user_type_4" => "false",
                        "user_type_5" => "false",
                        "user_type_6" => "false",
                        "user_type_7" => "false",
                        "user_typeID_3" => "",
                        "user_typeID_4" => "",
                        "user_typeID_5" => "",
                        "user_typeID_6" => "",
                        "ut_other" => "");
                    $props = GP("properties");
                    if (is_array($props)) {
                        if (IsNullOrEmpty($props["subscribe"]))
                            $props["subscribe"] = 0;
                        $userValidator = $this->GetControl("userValidator");
                        $errorsData = $userValidator->Validate(&$props);
                        if (!$this->ValidateCaptcha())
                            array_push($errorsData, "не верно введены цифры");

                        $citySelect->selectedValue = $props["city"];
                        $user_types = GP("user_type");
                        $user_typesIDs = GP("user_typeID");
                        foreach ($user_types as $key => $val) {
                            $err_ut = "";
                            $utdata["user_type_" . $key] = true;
                            $utdata["user_type_" . $key . "_list"] = "";
                            if ($key > 2 && $key < 7)
                                $utdata["user_typeID_" . $key] = $user_typesIDs[$key];
                            else if ($key == 7)
                                $utdata["ut_other"] = $user_typesIDs[$key];

                            if ($key > 2 && $key < 7) {
                                if (IsNullOrEmpty($user_typesIDs[$key]))
                                    $err_ut = "не выбрано ни одного ";
                                else {
                                    $ids = preg_split("/[\s,]+/", $user_typesIDs[$key]);
                                    foreach ($ids as $num => $rid) {
                                        if (!IsNullOrEmpty($rid) && !is_numeric($rid))
                                            $err_ut = "не верно задан ID";
                                        else {
                                            $tbl = "";
                                            switch ($key) {
                                                case 3 :
                                                    $tbl = "contractor";
                                                    break;
                                                case 4 :
                                                    $tbl = "area";
                                                    break;
                                                case 5 :
                                                    $tbl = "artist";
                                                    break;
                                                case 6 :
                                                    $tbl = "agency";
                                                    break;
                                            }
                                            $title = SQLProvider::ExecuteScalar("select title from tbl__" . $tbl . "_doc where tbl_obj_id = $rid");
                                            $utdata["user_type_" . $key . "_list"] .= '<div id="sl_' . $rid . '"><a href="" class="' . $tbl . '" title="Удалить" onclick="DeleteSelected(this,\'' . $tbl . '\'); return false;">' . $title . '</a></div>';
                                        }
                                    }
                                }
                                if (!IsNullOrEmpty($err_ut))
                                    switch ($key) {
                                        case 3 :
                                            $err_ut .= " подрядчика";
                                            break;
                                        case 4 :
                                            $err_ut .= " площадки";
                                            break;
                                        case 5 :
                                            $err_ut .= " артиста";
                                            break;
                                        case 6 :
                                            $err_ut .= " агентства";
                                            break;
                                    }
                            }
                            elseif ($key == 7 && IsNullOrEmpty($user_typesIDs[$key]))
                                $err_ut = "не задан другой тип пользователя";
                            if (!IsNullOrEmpty($err_ut))
                                array_push($errorsData, $err_ut);
                        }
                        if (!sizeof($user_types))
                            array_push($errorsData, "необходимо указать тип пользователя");
                        $userData->FromHashMap($props);
                        if (IsNullOrEmpty($userData->nikname))
                            $userData->nikname = null;
                        $userData->forum_name = IsNullOrEmpty($userData->nikname) ? $userData->login : $userData->nikname;

                        $flogo = $_FILES["properties"];
                        if (is_array($flogo)) {
                            $logo = $this->CreateLogo($userData->login, $flogo);
                            if (!is_null($logo))
                                $userData->logo = $logo;
                        }

                        if (sizeof($errorsData) > 0)
                            $errors->dataSource = array("message" => $errorsData[0]);
                        else {
								$login = $table->SelectUnique(new CEqFilter(&$table->fields["login"], $props["login"]), false);
								$r = SQLProvider::ExecuteQuery("select * from vw__all_users where login='".mysql_real_escape_string($props["login"])."'");
                            if (sizeof($r)!=0) {
                                $errors->dataSource = array("message" => "такой логин уже существует");
                            }
                            elseif (is_null($login))
                            {

                                $completeData = array();
                                $completeData["login"] = iconv($this->encoding, "utf-8", $userData->login);
                                $completeData["password"] = iconv($this->encoding, "utf-8", $userData->password);

                                $userData->active = 0;
                                $userData->email = $userData->login;
                                $userData->new_user = 1;
                                $userData->registration_date = strftime('%Y-%m-%d %H:%M:%S', time());
                                $userData->registration_confirmed = 0;
                                $userData->registration_confirm_code = md5(rand(0, getrandmax()));
                                $userData->password = md5($userData->password);

                                $userData->edit_date = time();
                                
                                if($props["display_type"][0] == '') {$props["display_type"][0] = '0';}
                                if($props["display_type"][1] == '') {$props["display_type"][1] = '0';}
                                if($props["display_type"][2] == '') {$props["display_type"][2] = '0';}
                                if($props["display_type"][3] == '') {$props["display_type"][3] = '0';}
                                if($props["display_type"][4] == '') {$props["display_type"][4] = '0';}
                                if($props["display_type"][5] == '') {$props["display_type"][5] = '0';}
                                if($props["display_type"][6] == '') {$props["display_type"][6] = '0';}
                                if($props["display_type"][7] == '') {$props["display_type"][7] = '0';}
                                if($props["display_type"][8] == '') {$props["display_type"][8] = '0';}
                                if($props["display_type"][9] == '') {$props["display_type"][9] = '0';}
                                if($props["display_type"][10] == '') {$props["display_type"][10] = '0';}
                                if($props["display_type"][11] == '') {$props["display_type"][11] = '0';}
                                
                                $userData->display_type = $props["display_type"][0].
                                '|'.$props["display_type"][1].
                                '|'.$props["display_type"][2].
                                '|'.$props["display_type"][3].
                                '|'.$props["display_type"][4].
                                '|'.$props["display_type"][5].
                                '|'.$props["display_type"][6].
                                '|'.$props["display_type"][7].
                                '|'.$props["display_type"][8].
                                '|'.$props["display_type"][9].
                                '|'.$props["display_type"][10].
                                '|'.$props["display_type"][11];
                                
                                
                                if(empty($props["skype"])) {$props["skype"] = '0';}
                                if(empty($props["icq"])) {$props["icq"] = '0';}
                                
                                $userData->skype = $props["skype"];
                                $userData->icq = $props["icq"];
                                
                                $userData->title = trim($props['title']);
                                
                                if($props["birthday"] == '') { $userData->birthday = strftime('%Y-%m-%d %H:%M:%S', time());}
                                
                                
                                $table->InsertObject(&$userData);
                                $user->id = $userData->tbl_obj_id;

                                if ($user->id > 0) {
                                    SQLProvider::ExecuteNonReturnQuery("delete from tbl__registered_user_types where user_id = " . $user->id);
                                    SQLProvider::ExecuteNonReturnQuery("delete from tbl__registered_user_link_resident where user_id = " . $user->id);
                                    foreach ($user_types as $key) {
                                        $resident_type = "";
                                        switch ($key) {
                                            case 3 :
                                                $resident_type = "contractor";
                                                break;
                                            case 4 :
                                                $resident_type = "area";
                                                break;
                                            case 5 :
                                                $resident_type = "artist";
                                                break;
                                            case 6 :
                                                $resident_type = "agency";
                                                break;
                                        }
                                        if (!IsNullOrEmpty($resident_type)) {
                                            $ids = preg_split("/[\s,]+/", $user_typesIDs[$key]);
                                            foreach ($ids as $num => $r_id)
                                            {
                                                if (is_numeric($r_id))
                                                    SQLProvider::ExecuteNonReturnQuery("insert into tbl__registered_user_link_resident(user_id,resident_type,resident_id)
																		    values (" . $user->id . ", '" . $resident_type . "', " . intval($r_id) . ")");
                                            }

                                        }
                                        $ut = "";
                                        switch ($key) {
                                            case 1 :
                                                $ut = "заказчик мероприятий";
                                                break;
                                            case 2 :
                                                $ut = "организатор мероприятий";
                                                break;
                                            case 3 :
                                                $ut = "представитель подрядчика";
                                                break;
                                            case 4 :
                                                $ut = "представитель площадки";
                                                break;
                                            case 5 :
                                                $ut = "представитель артиста";
                                                break;
                                            case 6 :
                                                $ut = "представитель агентства";
                                                break;
                                            case 7 :
                                                $ut = $user_typesIDs[$key];
                                                break;
                                        }
                                        SQLProvider::ExecuteNonReturnQuery("insert into tbl__registered_user_types(user_id,user_type)
																		    values (" . $user->id . ", '" . $ut . "')");
                                    }
                                }

                                $title = iconv($this->encoding, "utf-8", 'Регистрация на EventCatalog.ru');
                                $completeData["link"] = "http://" . $_SERVER['HTTP_HOST'] . "/registration/confirm/code/" . $userData->registration_confirm_code;
                                $mbody = CStringFormatter::Format(file_get_contents(RealFile($this->mailpath)), $completeData);
                                SendHTMLMail($userData->email, $mbody, $title);

                                CURLHandler::Redirect(CURLHandler::$currentPath . "type/complete");
                            }
                            else
                                $errors->dataSource = array("message" => "такой логин уже существует");
                        }
                    }
                }

                for ($i = 3; $i <= 6; $i++)
                    if (!isset($utdata['user_type_' . $i . '_list']))
                        $utdata['user_type_' . $i . '_list'] = "";
                $logos = preg_split("/\//", $userData->logo);
                $ls = sizeof($logos);
                $udata = array("login_readonly" => $user->authorized ? "readonly" : "",
                    "submit_text" => $this->GetMessage($user->authorized ? "save" : "reg"),
                    "city_list" => $citySelect->RenderHTML(),
                    "regdate" => $regData);
                $userData->logo = GetFilename($userData->logo);
                $account->dataSource = array_merge($udata, $userData->GetData(), $utdata, $captcha);
                break;
                }

            // CONTRACTOR -----------------------------------------------------------
            case "contractor":
                {
                $groups_items = SQLProvider::ExecuteQuery("select * from tbl__activity_type where parent_id = 0 or parent_id is null
                                                       order by priority desc");
                $groups_list = "";
                $subgroups_list = "";
                $sel_groups = null;

                $images = array();
                $table = new CNativeDataTable("tbl__contractor_doc");
                $userData = null;

                $userData = $table->CreateNewRow(true);

                if ($this->IsPostBack) {
                    $props = GP("properties");
                    if (is_array($props)) {
                        $userValidator = $this->GetControl("contractorValidator");

                        $props["login"] = $props["email"];
                        $props['city'] = trim($props['city']);
                        $props['other_city'] = $props['city'];
                        $errorsData = $userValidator->Validate(&$props);
                        $props['title'] = trim($props['title']);
                        $props['title_url'] = translitURL($props['title']);
                        if ($this->CheckURL("contractor", $props['title_url']))
                            array_push($errorsData, "Подрядчик с таким названием уже существует");

                        $sel_groups = $props['selected_group'];
                        if (!sizeof($sel_groups))
                            array_push($errorsData, "не указана категория");

                        if (!$this->ValidateCaptcha())
                            array_push($errorsData, "не верно введены цифры");

                        $userData->FromHashMap($props);
                        if (sizeof($errorsData) > 0)
                            $errors->dataSource = array("message" => $errorsData[0]);
                        else {
                            $city_id = $this->CheckCity($props['city']);
                            if ($city_id) {
                                $props['city'] = $city_id;
                                $props['other_city'] = '';
                            }
                            else {
                                $props['other_city'] = $props['city'];
                                $props['city'] = 0;
                            }
                            $userData->city = $props['city'];
                            $userData->other_city = $props['other_city'];

                            $login = $table->SelectUnique(new CEqFilter(&$table->fields["login"], $props["login"]), false);
                            $r = SQLProvider::ExecuteQuery("select * from vw__all_users where login='".mysql_real_escape_string($props["login"])."'");
                            if (sizeof($r)!=0) {
                                $errors->dataSource = array("message" => "такой логин уже существует");
                            }
                            elseif (is_null($login)) {
                                $completeData = array();
                                $completeData["login"] = iconv($this->encoding, "utf-8", $userData->login);
                                $completeData["password"] = iconv($this->encoding, "utf-8", $userData->password);
                                $userData->active = 0;

                                $userData->registration_date = strftime('%Y-%m-%d %H:%M:%S', time());
                                $userData->registration_confirmed = 0;
                                $userData->registration_confirm_code = md5(rand(0, getrandmax()));
                                $userData->password = md5($userData->password);
                                $flogo = $_FILES["properties"];
                                if (is_array($flogo)) {
                                    $logo = $this->CreateLogo($userData->login, $flogo, "logo");
                                    if (!is_null($logo))
                                        $userData->logo_image = $logo;
                                }
                                $userData->edit_date = time();
                                $userData->short_description = $userData->description;
                                $table->InsertObject(&$userData);

                                $completeData["id"] = $userData->tbl_obj_id;

                                SQLProvider::ExecuteNonReturnQuery("delete from tbl__contractor2activity where tbl_obj_id=$userData->tbl_obj_id");
                                foreach ($sel_groups as $kind)
                                    SQLProvider::ExecuteNonReturnQuery("insert into tbl__contractor2activity (tbl_obj_id,kind_of_activity) values ($userData->tbl_obj_id,$kind)");

                                $photos = $_FILES["photo_file"];
                                $ptitles = GP("photo_title", array());
                                if (is_array($ptitles)) {
                                    foreach ($ptitles as $j => $ptj) {
                                        $lFilename = "";
                                        $mFilename = "";
                                        $sFilename = "";
                                        $photo_id = $this->CreateImages($photos, $j, $ptj, $lFilename, $mFilename, $sFilename);
                                        if ($photo_id > 0) {
                                            SQLProvider::ExecuteNonReturnQuery("delete from tbl__contractor_photos where child_id=$photo_id");
                                            SQLProvider::ExecuteNonReturnQuery("insert into tbl__contractor_photos values($photo_id,$userData->tbl_obj_id)");
                                        }
                                    }
                                }

                                $title = iconv($this->encoding, "utf-8", 'Регистрация на EventCatalog.ru');
                                $completeData["link"] = "http://" . $_SERVER['HTTP_HOST'] . "/registration/confirm/code/" . $userData->registration_confirm_code;
                                $mbody = CStringFormatter::Format(file_get_contents(RealFile($this->mailpath)), $completeData);
                                SendHTMLMail($userData->email, $mbody, $title);

                                CURLHandler::Redirect(CURLHandler::$currentPath . "type/completere");
                            }
                            else
                                $errors->dataSource = array("message" => "такой логин уже существует");
                        }
                    }
                }

                $logos = preg_split("/\//", $userData->logo);
                $ls = sizeof($logos);

                $ikeys = array_keys($images);
                foreach ($ikeys as $ikey)
                {
                    $images[$ikey]["ptype"] = "contractor";
                }
                $imagesList = $this->GetControl("imagesList");

                $imagesList->itemTemplate = file_get_contents(RealFile("pagecode/settings/registration_files/fileUploadItem.htm"));
                $images = array();
                for ($i = 0; $i < 8; $i++)
                {
                    array_push($images, array("title_name" => "photo_title[$i]", "file_name" => "photo_file[$i]"));
                }

                $imagesList->dataSource = $images;

                $selected_groups = "";
                if (isset($sel_groups) and is_array($sel_groups)) {
                    foreach ($sel_groups as $sg) {
                        $sg_title = SQLProvider::ExecuteScalar("select title from tbl__activity_type where tbl_obj_id = $sg");
                        $selected_groups .= '<div id="selected_id' . $sg .
                            '"><input type="hidden" name="properties[selected_group][' . $sg .
                            ']" value="' . $sg . '">' . $sg_title .
                            ' <a href="" class="reg_del_group" onclick="javascript: SelectedGroupDel(' . $sg .
                            '); return false;">удалить</a></div>';
                    }
                }

                foreach ($groups_items as $group) {
                    $groups_list .= '<option value="' . $group['tbl_obj_id'] . '">' . $group['title'] . '</option>';
                    $subgroups_items = SQLProvider::ExecuteQuery("select * from tbl__activity_type where parent_id = " . $group['tbl_obj_id'] . "
                                                     order by priority desc");
                    $subgroups_list .= '<div id="subgroup_id' . $group['tbl_obj_id'] . '" style="display: none;">';
                    foreach ($subgroups_items as $sgroup) {
                        $chk = "";
                        if (isset($sel_groups) and is_array($sel_groups) and
                            !(array_search($sgroup['tbl_obj_id'], $sel_groups) === false)
                        ) {
                            $chk = "checked=\"checked\"";
                        }

                        $subgroups_list .= '<input id="checkbox_id' . $sgroup['tbl_obj_id'] . '" type="checkbox" value="' . $sgroup['tbl_obj_id'] . '" onclick="SelectSubGroup(this,\'' . $sgroup['title'] . '\')" ' . $chk . '>' . $sgroup['title'] . '<br>';
                    }
                    $subgroups_list .= "</div>";
                }

                $udata = array(
                    "submit_text" => $this->GetMessage($user->authorized ? "save" : "reg"),
                    "imagesList" => $imagesList->RenderHTML(),
                    "groups_list" => $groups_list,
                    "subgroups_list" => $subgroups_list,
                    "selected_groups" => $selected_groups,
                    "st_visible" => sizeof($sel_groups) > 0 ? "" : "display:none;");
                $userData->logo_image = GetFilename($userData->logo_image);
                $account->dataSource = array_merge($userData->GetData(), $udata, $captcha);
                break;
                }

            // AREA------------------------------------------------------------------
            case "area":
                {
                $table = new CNativeDataTable("tbl__area_doc");
                $userData = null;

                $groups_items = SQLProvider::ExecuteQuery("select * from tbl__area_types
                                                     order by priority desc");
                $groups_list = "";
                $subgroups_list = "";
                $sel_groups = array();
                $sel_types = array();
                $sel_metro = array();
                $sel_mway_highway = array();
                $sel_mway_city = array();

                $halls = array();
                $hallsJS = "";
                $images = array();

                $userData = $table->CreateNewRow(true);

                if ($this->IsPostBack) {
                    $props = GP("properties");
                    if (is_array($props)) {
                        $userValidator = $this->GetControl("areaValidator");
                        if (isset($props["parking"]) && $props["parking"])
                            $props["parking"] = $props["parking_count"];
                        unset($props["parking_count"]);

                        if (isset($props["wardrobe"]) && $props["wardrobe"])
                            $props["wardrobe"] = $props["wardrobe_count"];
                        unset($props["wardrobe_count"]);

                        if (isset($props["stage"]) && $props["stage"])
                            $props["stage"] = $props["stage_count"];
                        unset($props["stage_count"]);

                        if (isset($props["makeup_rooms"]) && $props["makeup_rooms"])
                            $props["makeup_rooms"] = $props["makeup_rooms_count"];
                        unset($props["makeup_rooms_count"]);

                        if (isset($props["dancing_size"]) && $props["dancing_size"])
                            $props["dancing_size"] = $props["dancing_size_count"];
                            $props["dancing"] = 1;
                        unset($props["dancing_size_count"]);

                        if (isset($props["sound"]) && $props["sound"])
                            $props["sound"] = $props["sound_count"];
                        unset($props["sound_count"]);

                        if (isset($props["panels"]) && $props["panels"])
                            $props["panels"] = $props["panels_count"];
                        unset($props["panels_count"]);

                        if (isset($props["projector"]) && $props["projector"])
                            $props["projector"] = $props["projector_count"];
                        unset($props["projector_count"]);
                        
                        if($props['kitchen'] == '') { $props['kitchen'] = 0;}

                        $props["login"] = $props["email"];
                        $props['city'] = trim($props['city']);
                        $props['other_city'] = $props['city'];
                        $errorsData = $userValidator->Validate(&$props);
                        $props['title'] = trim($props['title']);
                        
                        $props['title_url'] = translitURL($props['title']);
                        if ($this->CheckURL("area", $props['title_url']))
                            array_push($errorsData, "Площадка с таким названием уже существует");

                        if (isset($props['selected_group']))
                            $sel_groups = $props['selected_group'];
                        if (isset($props['metro']))
                            $sel_metro = $props['metro'];
                        if (isset($props['mway_highway']))
                            $sel_mway_highway = $props['mway_highway'];
                        if (isset($props['mway_city']))
                            $sel_mway_city = $props['mway_city'];

                        if (sizeof($sel_groups) > 0) {
                            $types = SQLProvider::ExecuteQuery("select distinct parent_id from tbl__area_subtypes where tbl_obj_id in (" . implode(",", $sel_groups) . ")");
                            foreach ($types as $t)
                                array_push($sel_types, $t["parent_id"]);
                        }
                        else
                            array_push($errorsData, "не указана категория");

                        if (isset($props["hall"]))
                            $halls = $props["hall"];
                        if (!sizeof($halls))
                            array_push($errorsData, "не добавлен ни один зал");

                        if (!$this->ValidateCaptcha())
                            array_push($errorsData, "не верно введены цифры");

                        $city_id = $this->CheckCity($props['city']);
                        if ($city_id) {
                            $props['city'] = $city_id;
                            $props['other_city'] = '';
                        }
                        else {
                            $props['other_city'] = $props['city'];
                            $props['city'] = 0;
                        }

                        $userData->FromHashMap($props);

                        if (sizeof($errorsData) > 0)
                            $errors->dataSource = array("message" => $errorsData[0]);
                        else {
                            $login = $table->SelectUnique(new CEqFilter(&$table->fields["login"], $props["login"]), false);
                            $r = SQLProvider::ExecuteQuery("select * from vw__all_users where login='".mysql_real_escape_string($props["login"])."'");
                            if (sizeof($r)!=0) {
                                $errors->dataSource = array("message" => "такой логин уже существует");
                            }
                            elseif (is_null($login)) {
                                $completeData = array();
                                $completeData["login"] = iconv($this->encoding, "utf-8", $userData->login);
                                $completeData["password"] = iconv($this->encoding, "utf-8", $userData->password);

                                $userData->active = 0;

                                $userData->registration_date = strftime('%Y-%m-%d %H:%M:%S', time());
                                $userData->registration_confirmed = 0;
                                $userData->registration_confirm_code = md5(rand(0, getrandmax()));
                                $userData->password = md5($userData->password);
                                $flogo = $_FILES["properties"];
                                if (is_array($flogo)) {
                                    $logo = $this->CreateLogo($userData->login, $flogo, "logo");
                                    if (!is_null($logo))
                                        $userData->logo = $logo;
                                }
                                $userData->edit_date = time();
                                $userData->rent = 0;
                                //$userData->dancing = 0;
                                $userData->date_open = '';
                                $table->InsertObject(&$userData);

                                $completeData["id"] = $userData->tbl_obj_id;

                                $typeTable = new CNativeDataTable("tbl__area2type");
                                $typeTable->Delete(new CEqFilter(&$typeTable->fields["area_id"], $userData->tbl_obj_id));
                                foreach ($sel_types as $areaType) {
                                    $Row = $typeTable->CreateNewRow(true);
                                    $Row->type_id = $areaType;
                                    $Row->area_id = $userData->tbl_obj_id;
                                    $typeTable->InsertObject(&$Row, false);
                                }
                                $subtypeTable = new CNativeDataTable("tbl__area2subtype");
                                $subtypeTable->Delete(new CEqFilter(&$subtypeTable->fields["area_id"], $userData->tbl_obj_id));
                                foreach ($sel_groups as $areaSubtype)
                                {
                                    $Row = $subtypeTable->CreateNewRow(true);
                                    $Row->subtype_id = $areaSubtype;
                                    $Row->area_id = $userData->tbl_obj_id;
                                    $subtypeTable->InsertObject(&$Row, false);
                                }

                                //metro
                                if ($userData->city == 204 || $userData->city == 1465) {
                                    $metroTable = new CNativeDataTable("tbl__area_metro");
                                    $metroTable->Delete(new CEqFilter(&$metroTable->fields["area"], $userData->tbl_obj_id));
                                    foreach ($sel_metro as $ms)
                                    {
                                        $Row = $metroTable->CreateNewRow(true);
                                        $Row->metro_station = $ms;
                                        $Row->area = $userData->tbl_obj_id;
                                        $metroTable->InsertObject(&$Row, false);
                                    }
                                }
                                //mway_highway
                                if ($userData->city == 1465) {
                                    $m_highwayTable = new CNativeDataTable("tbl__area_m_highways");
                                    $m_highwayTable->Delete(new CEqFilter(&$m_highwayTable->fields["area_id"], $userData->tbl_obj_id));
                                    foreach ($sel_mway_highway as $mh)
                                    {
                                        $Row = $m_highwayTable->CreateNewRow(true);
                                        $Row->moscow_highway_id = $mh;
                                        $Row->area_id = $userData->tbl_obj_id;
                                        $m_highwayTable->InsertObject(&$Row, false);
                                    }
                                }
                                //mway_city
                                if ($userData->city == 1465) {
                                    $m_cityTable = new CNativeDataTable("tbl__area_m_cities");
                                    $m_cityTable->Delete(new CEqFilter(&$m_cityTable->fields["area_id"], $userData->tbl_obj_id));
                                    foreach ($sel_mway_city as $mc)
                                    {
                                        $Row = $m_cityTable->CreateNewRow(true);
                                        $Row->moscow_city_id = $mc;
                                        $Row->area_id = $userData->tbl_obj_id;
                                        $m_cityTable->InsertObject(&$Row, false);
                                    }
                                }

                                $this->UpdateHalls($userData);

                                $photos = $_FILES["photo_file"];
                                $ptitles = GP("photo_title", array());
                                if (is_array($ptitles)) {
                                    foreach ($ptitles as $j => $ptj) {
                                        $lFilename = "";
                                        $mFilename = "";
                                        $sFilename = "";
                                        $photo_id = $this->CreateImages($photos, $j, $ptj, $lFilename, $mFilename, $sFilename);
                                        if ($photo_id > 0) {
                                            SQLProvider::ExecuteNonReturnQuery("delete from tbl__area_photos where child_id=$photo_id");
                                            SQLProvider::ExecuteNonReturnQuery("insert into tbl__area_photos values($photo_id,$userData->tbl_obj_id)");
                                        }
                                    }
                                }

                                $title = iconv($this->encoding, "utf-8", 'Регистрация на EventCatalog.ru');
                                $completeData["link"] = "http://" . $_SERVER['HTTP_HOST'] . "/registration/confirm/code/" . $userData->registration_confirm_code;
                                $mbody = CStringFormatter::Format(file_get_contents(RealFile($this->mailpath)), $completeData);
                                SendHTMLMail($userData->email, $mbody, $title);

                                CURLHandler::Redirect(CURLHandler::$currentPath . "type/completere");
                            }
                            else
                                $errors->dataSource = array("message" => "такой логин уже существует");
                        }
                    }
                }
                if (sizeof($halls) > 0) {
                    $th = array();
                    foreach ($halls as &$hall) {
                        $hall["title"] = isset($hall["title"]) ? VType($hall["title"], "string", "") : "";
                        $hall["max_places_banquet"] = isset($hall["max_places_banquet"]) ? VType($hall["max_places_banquet"], "int", 0) : 0;
                        $hall["max_places_official_buffet"] = isset($hall["max_places_official_buffet"]) ? VType($hall["max_places_official_buffet"], "int", 0) : 0;
                        $hall["max_places_conference"] = isset($hall["max_places_conference"]) ? VType($hall["max_places_conference"], "int", 0) : 0;
                        $hall["cost_conference"] = isset($hall["cost_conference"]) ? VType($hall["cost_conference"], "int", 0) : 0;
                        $tr = CStringFormatter::Format('{"title":"{title}", "max_places_banquet":{max_places_banquet},"max_places_official_buffet":{max_places_official_buffet},"max_places_conference":{max_places_conference}, "cost_conference":{cost_conference}}', $hall);
                        array_push($th, $tr);
                    }
                    $hallsJS = CStringFormatter::FromArray($th);
                }
                $logos = preg_split("/\//", $userData->logo);
                $ls = sizeof($logos);

                $checked_groups = "";
                if (isset($sel_groups) and is_array($sel_groups)) {
                    foreach ($sel_groups as $sg) {
                        $sg_title = SQLProvider::ExecuteScalar("select title from tbl__area_subtypes where tbl_obj_id = $sg");
                        $checked_groups .= '<div id="checked_id' . $sg .
                            '"><input type="hidden" name="properties[checked_group][' . $sg .
                            ']" value="' . $sg . '">' . $sg_title .
                            ' <a href="" class="reg_del_group" onclick="javascript: SelectedGroupDel(' . $sg .
                            '); return false;">удалить</a></div>';
                    }
                }

                foreach ($groups_items as $group) {
                    $groups_list .= '<option value="' . $group['tbl_obj_id'] . '">' . $group['title'] . '</option>';
                    $subgroups_items = SQLProvider::ExecuteQuery("select * from tbl__area_subtypes where parent_id = " . $group['tbl_obj_id'] . "
                                                       order by priority desc");
                    $subgroups_list .= '<div id="subgroup_id' . $group['tbl_obj_id'] . '" style="display: none;">';
                    foreach ($subgroups_items as $sgroup) {
                        $chk = "";
                        if (isset($sel_groups) and is_array($sel_groups) and
                            !(array_search($sgroup['tbl_obj_id'], $sel_groups) === false)
                        ) {
                            $chk = "checked=\"checked\"";
                        }

                        $subgroups_list .= '<input id="checkbox_id' . $sgroup['tbl_obj_id'] . '" type="checkbox" value="' . $sgroup['tbl_obj_id'] . '" onclick="SelectSubGroup(this,\'' . $sgroup['title'] . '\')" ' . $chk . '>' . $sgroup['title'] . '<br>';
                    }
                    $subgroups_list .= "</div>";
                }

                $checked_metro = "";
                if (isset($sel_metro) and is_array($sel_metro)) {
                    foreach ($sel_metro as $ms) {
                        $ms_title = SQLProvider::ExecuteScalar("select title from tbl__metro_stations where tbl_obj_id = $ms");
                        $checked_metro .= '<div id="metro_id' . $ms .
                            '"><input type="hidden" name="properties[metro][' . $ms .
                            ']" value="' . $ms . '">' . $ms_title .
                            ' <a href="" class="reg_del_group" onclick="javascript: MetroDel(' . $ms .
                            '); return false;">удалить</a></div>';
                    }
                }
                $areaData = array();
                if ($userData->invite_catering) {
                    $areaData['invite_catering_on'] = "checked";
                    $areaData['invite_catering_off'] = "";
                }
                else {
                    $areaData['invite_catering_on'] = "checked";
                    $areaData['invite_catering_off'] = "";
                }

                if ($userData->parking) {
                    $areaData['parking_on'] = "checked";
                    $areaData['parking_off'] = "";
                    $areaData['parking_count'] = $userData->parking;
                }
                else {
                    $areaData['parking_on'] = "";
                    $areaData['parking_off'] = "checked";
                    $areaData['parking_count'] = "";
                }

                if ($userData->service_entrance) {
                    $areaData['service_entrance_on'] = "checked";
                    $areaData['service_entrance_off'] = "";
                }
                else {
                    $areaData['service_entrance_on'] = "";
                    $areaData['service_entrance_off'] = "checked";
                }

                if ($userData->wardrobe) {
                    $areaData['wardrobe_on'] = "checked";
                    $areaData['wardrobe_off'] = "";
                    $areaData['wardrobe_count'] = $userData->wardrobe;
                }
                else {
                    $areaData['wardrobe_on'] = "";
                    $areaData['wardrobe_off'] = "checked";
                    $areaData['wardrobe_count'] = "";
                }

                if ($userData->stage) {
                    $areaData['stage_on'] = "checked";
                    $areaData['stage_off'] = "";
                    $areaData['stage_count'] = $userData->stage;
                }
                else {
                    $areaData['stage_on'] = "";
                    $areaData['stage_off'] = "checked";
                    $areaData['stage_count'] = "";
                }

                if ($userData->makeup_rooms) {
                    $areaData['makeup_rooms_on'] = "checked";
                    $areaData['makeup_rooms_off'] = "";
                    $areaData['makeup_rooms_count'] = $userData->makeup_rooms;
                }
                else {
                    $areaData['makeup_rooms_on'] = "";
                    $areaData['makeup_rooms_off'] = "checked";
                    $areaData['makeup_rooms_count'] = "";
                }

                if ($userData->dancing_size) {
                    $areaData['dancing_size_on'] = "checked";
                    $areaData['dancing_size_off'] = "";
                    $areaData['dancing_size_count'] = $userData->dancing_size;
                }
                else {
                    $areaData['dancing_size_on'] = "";
                    $areaData['dancing_size_off'] = "checked";
                    $areaData['dancing_size_count'] = "";
                }

                if ($userData->car_into) {
                    $areaData['car_into_on'] = "checked";
                    $areaData['car_into_off'] = "";
                }
                else {
                    $areaData['car_into_on'] = "";
                    $areaData['car_into_off'] = "checked";
                }

                if ($userData->light) {
                    $areaData['light_on'] = "checked";
                    $areaData['light_off'] = "";
                }
                else {
                    $areaData['light_on'] = "";
                    $areaData['light_off'] = "checked";
                }

                if ($userData->sound) {
                    $areaData['sound_on'] = "checked";
                    $areaData['sound_off'] = "";
                    $areaData['sound_count'] = $userData->sound;
                }
                else {
                    $areaData['sound_on'] = "";
                    $areaData['sound_off'] = "checked";
                    $areaData['sound_count'] = "";
                }

                if ($userData->panels) {
                    $areaData['panels_on'] = "checked";
                    $areaData['panels_off'] = "";
                    $areaData['panels_count'] = $userData->panels;
                }
                else {
                    $areaData['panels_on'] = "";
                    $areaData['panels_off'] = "checked";
                    $areaData['panels_count'] = "";
                }

                if ($userData->projector) {
                    $areaData['projector_on'] = "checked";
                    $areaData['projector_off'] = "";
                    $areaData['projector_count'] = $userData->projector;
                }
                else {
                    $areaData['projector_on'] = "";
                    $areaData['projector_off'] = "checked";
                    $areaData['projector_count'] = "";
                }
                $moscow_districts = new CSelect();
                $moscow_districts->dataSource = SQLProvider::ExecuteQuery("
          select tbl_obj_id, title from tbl__moscow_districts");
                array_unshift($moscow_districts->dataSource, array("tbl_obj_id" => 0, "title" => " --выберите округ-- "));
                $moscow_districts->titleName = "title";
                $moscow_districts->valueName = "tbl_obj_id";
                $moscow_districts->selectedValue = $userData->moscow_district;
                $moscow_districts->name = "properties[moscow_district]";
                $moscow_districts->class = "reg_input_text";
                $moscow_districts->size = "1";

                $this->PrepareCity(&$userData);

                $sel_mway = "";
                if (isset($sel_mway_highway) and is_array($sel_mway_highway)) {
                    foreach ($sel_mway_highway as $mh) {
                        $mh_title = SQLProvider::ExecuteScalar("select title from tbl__moscow_highways where tbl_obj_id = $mh");
                        $sel_mway .= '<div id="highway_id' . $mh .
                            '"><input type="hidden" name="properties[mway_highway][' . $mh .
                            ']" value="' . $mh . '">' . $mh_title .
                            ' ш.<a href="" class="reg_del_group" onclick="javascript: HighwayDel(' . $mh .
                            '); return false;">удалить</a></div>';
                    }
                }
                if (isset($sel_mway_city) and is_array($sel_mway_city)) {
                    foreach ($sel_mway_city as $mc) {
                        $mc_title = SQLProvider::ExecuteScalar("select title from tbl__moscow_cities where tbl_obj_id = $mc");
                        $sel_mway .= '<div id="mcity_id' . $mc .
                            '"><input type="hidden" name="properties[mway_city][' . $mc .
                            ']" value="' . $mc . '">' . $mc_title .
                            ' <a href="" class="reg_del_group" onclick="javascript: MCityDel(' . $mc .
                            '); return false;">удалить</a></div>';
                    }
                }
                $udata = array("login_readonly" => $user->authorized ? "readonly=\"true\"" : "",
                    "submit_text" => $this->GetMessage($user->authorized ? "save" : "reg"),
                    "hallsJS" => $hallsJS,
                    "groups_list" => $groups_list,
                    "subgroups_list" => $subgroups_list,
                    "selected_groups" => $checked_groups,
                    "st_visible" => sizeof($sel_groups) > 0 ? "" : "display:none;",
                    "sel_metro" => $checked_metro,
                    "mt_visible" => sizeof($sel_metro) > 0 ? "" : "display:none;",
                    "mmetro_visible" => ($userData->city == 204 || $userData->city == 1465) ? "" : "display:none",
                    "sel_mway" => $sel_mway,
                    "mw_visible" => (!IsNullOrEmpty($sel_mway)) ? "" : "display:none",
                    "mways_visible" => ($userData->city == 1465) ? "" : "display:none",
                    "mdistricts_visible" => ($userData->city == 204) ? "" : "display:none",
                    "mdistricts" => $moscow_districts->Render());
                $userData->logo = GetFilename($userData->logo);
                $account->dataSource = array_merge($udata, $userData->GetData(), $captcha, $areaData);
                break;
                }

            // ARTIST ---------------------------------------------------------------
            case "artist":
                {
                $table = new CNativeDataTable("tbl__artist_doc");
                $userData = null;

                $groups_items = SQLProvider::ExecuteQuery("select * from tbl__artist_group
                                                       order by priority desc");
                $groups_list = "";
                $subgroups_list = "";
                $sel_groups = null;

                $selstyles = array();

                $images = array();
                $mp3s = array();
                $videos = array();

                $userData = $table->CreateNewRow(true);
                $newUser = true;

                if ($this->IsPostBack) {
                    $props = GP("properties");
                    if (is_array($props)) {
                        $userValidator = $this->GetControl("artistValidator");
                        $props["login"] = $props["email"];
                        $props['city'] = trim($props['city']);
                        $props['country'] = trim($props['country']);
                        $errorsData = $userValidator->Validate(&$props);
                        $props['title'] = trim($props['title']);
                        $props['title_url'] = translitURL($props['title']);
                        if ($this->CheckURL("artist", $props['title_url']))
                            array_push($errorsData, "Артист с таким названием уже существует");
                        $sel_groups = $props['selected_group'];
                        if (!sizeof($sel_groups))
                            array_push($errorsData, "не указана категория");
                        if (!$this->ValidateCaptcha())
                            array_push($errorsData, "не верно введены цифры");
                        $props['other_city'] = $props['city'];
                        $props['other_country'] = $props['country'];
                        $userData->FromHashMap($props);
                        $selstyles = $props['style'];
                        $setStyles = false;

                        if (sizeof($errorsData) > 0)
                            $errors->dataSource = array("message" => $errorsData[0]);
                        else
                        {
                            $city_id = $this->CheckCity($props['city']);
                            if ($city_id) {
                                $props['city'] = $city_id;
                                $props['other_city'] = '';
                            }
                            else {
                                $props['other_city'] = $props['city'];
                                $props['city'] = 0;
                            }
                            $userData->city = $props['city'];
                            $userData->other_city = $props['other_city'];

                            $country_id = $this->CheckCountry($props['country']);
                            if ($country_id) {
                                $props['country'] = $country_id;
                                $props['other_country'] = '';
                            }
                            else {
                                $props['other_country'] = $props['country'];
                                $props['country'] = 0;
                            }
                            $userData->country = $props['country'];
                            $userData->other_country = $props['other_country'];

                            $login = $table->SelectUnique(new CEqFilter(&$table->fields["login"], $props["login"]), false);
                            $r = SQLProvider::ExecuteQuery("select * from vw__all_users where login='".mysql_real_escape_string($props["login"])."'");
                            if (sizeof($r)!=0) {
                                $errors->dataSource = array("message" => "такой логин уже существует");
                            }
                            elseif (is_null($login)) {
                                $completeData = array();
                                $completeData["login"] = iconv($this->encoding, "utf-8", $userData->login);
                                $completeData["password"] = iconv($this->encoding, "utf-8", $userData->password);

                                $userData->active = 0;

                                $userData->registration_date = strftime('%Y-%m-%d %H:%M:%S', time());
                                $userData->registration_confirmed = 0;
                                $userData->registration_confirm_code = md5(rand(0, getrandmax()));
                                $userData->password = md5($userData->password);
                                $flogo = $_FILES["properties"];
                                if (is_array($flogo)) {
                                    $logo = $this->CreateLogo($userData->login, $flogo, "logo");
                                    if (!is_null($logo))
                                        $userData->logo = $logo;
                                }
                                
                                $userData->edit_date = time();
                                foreach ($sel_groups as $sg) {
                                    $userData->group = SQLProvider::ExecuteScalar("select parent_id from tbl__artist_subgroup where tbl_obj_id = $sg");
                                    break;
                                }
                                if ($userData->country)
                                    $userData->region = SQLProvider::ExecuteScalar("select region from tbl__countries where tbl_obj_id = " . $userData->country);
                                else
                                    $userData->region = 31;

                                $table->InsertObject(&$userData);

                                $completeData["id"] = $userData->tbl_obj_id;

                                $setStyles = true;
                                $this->SetArtistStyles($userData);

                                $photos = $_FILES["photo_file"];
                                $ptitles = GP("photo_title", array());
                                if (is_array($ptitles)) {
                                    foreach ($ptitles as $j => $ptj) {
                                        $lFilename = "";
                                        $mFilename = "";
                                        $sFilename = "";
                                        $photo_id = $this->CreateImages($photos, $j, $ptj, $lFilename, $mFilename, $sFilename);
                                        if ($photo_id > 0) {
                                            SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2photos where child_id=$photo_id");
                                            SQLProvider::ExecuteNonReturnQuery("insert into tbl__artist2photos values($photo_id,$userData->tbl_obj_id)");
                                        }
                                    }
                                }

                                //$photos = $_FILES["mp3_file"];
                                /*
                                $ptitles = GP("mp3_title", array());
                                if (is_array($ptitles)) {
                                    $uploader = new registration_upload_php();
                                    $uploadtable = new CNativeDataTable("tbl__upload");
                                    for ($j = 0; $j < 5; $j++) {
                                        if (isset($ptitles[$j])) {
                                            $imgRow = $uploadtable->CreateNewRow(true);
                                            $imgRow->title = $ptitles[$j];
                                            $uploadtable->InsertObject(&$imgRow);
                                            $lim = $uploader->CreateImage($imgRow->tbl_obj_id, $photos, $j);
                                            if (!is_null($lim)) {
                                                $imgRow->file = $lim;
                                                $uploadtable->UpdateObject(&$imgRow);
                                                SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2mp3file where file_id=$imgRow->tbl_obj_id");
                                                SQLProvider::ExecuteNonReturnQuery("insert into tbl__artist2mp3file(file_id,artist_id) values($imgRow->tbl_obj_id,$userData->tbl_obj_id)");
                                            }
                                        }
                                    }
                                }
                                */

                                $title = iconv($this->encoding, "utf-8", 'Регистрация на EventCatalog.ru');
                                $completeData["link"] = "http://" . $_SERVER['HTTP_HOST'] . "/registration/confirm/code/" . $userData->registration_confirm_code;
                                $mbody = CStringFormatter::Format(file_get_contents(RealFile($this->mailpath)), $completeData);
                                SendHTMLMail($userData->email, $mbody, $title);

                                CURLHandler::Redirect(CURLHandler::$currentPath . "type/completere");
                            }
                            else
                                $errors->dataSource = array("message" => "такой логин уже существует");

                            if ($setStyles) {
                                $this->SetArtistStyles($userData);
                                $skeys = array_keys($subgroupLists);
                                $subgroupsRaw = SQLProvider::ExecuteQueryReverse("select subgroup_id from tbl__artist2subgroup where artist_id=$userData->tbl_obj_id");
                                $subgroups = isset($subgroupsRaw["subgroup_id"]) ? $subgroupsRaw["subgroup_id"] : array();
                                foreach ($skeys as $skey)
                                    $subgroupLists[$skey]->selectedValue = $subgroups;
                            }
                        }
                    }
                }
                $logos = preg_split("/\//", $userData->logo);
                $ls = sizeof($logos);

                $imagesList = $this->GetControl("imagesList");
                $ikeys = array_keys($images);
                foreach ($ikeys as $ikey)
                    $images[$ikey]["ptype"] = "artist";
                $imagesList->itemTemplate = file_get_contents(RealFile("pagecode/settings/registration_files/fileUploadItem.htm"));
                $images = array();
                for ($i = 0; $i < 4; $i++)
                    array_push($images, array("title_name" => "photo_title[$i]", "file_name" => "photo_file[$i]"));

                $imagesList->dataSource = $images;

                $mp3List = $this->GetControl("mp3List");
                $ikeys = array_keys($mp3s);
                foreach ($ikeys as $ikey)
                    $mp3s[$ikey]["ptype"] = "artist";
                $mp3List->itemTemplate = file_get_contents(RealFile("pagecode/settings/registration_files/fileUploadItem.htm"));
                $mp3s = array();
                for ($i = 0; $i < 5; $i++)
                    array_push($mp3s, array("title_name" => "mp3_title[$i]", "file_name" => "mp3_file[$i]"));
                $mp3List->dataSource = $mp3s;

                $selected_groups = "";
                if (isset($sel_groups) and is_array($sel_groups)) {
                    foreach ($sel_groups as $sg) {
                        $sg_title = SQLProvider::ExecuteScalar("select title from tbl__artist_subgroup where tbl_obj_id = $sg");
                        $selected_groups .= '<div id="selected_id' . $sg .
                            '"><input type="hidden" name="properties[selected_group][' . $sg .
                            ']" value="' . $sg . '">' . $sg_title .
                            ' <a href="" class="reg_del_group" onclick="javascript: SelectedGroupDel(' . $sg .
                            '); return false;">удалить</a></div>';
                    }
                }
                foreach ($groups_items as $group) {
                    $groups_list .= '<option value="' . $group['tbl_obj_id'] . '">' . $group['title'] . '</option>';
                    $subgroups_items = SQLProvider::ExecuteQuery("select * from tbl__artist_subgroup where parent_id = " . $group['tbl_obj_id'] . "
                                                       order by priority desc");
                    $subgroups_list .= '<div id="subgroup_id' . $group['tbl_obj_id'] . '" style="display: none;">';
                    foreach ($subgroups_items as $sgroup) {
                        $chk = "";
                        if (isset($sel_groups) and is_array($sel_groups) and
                            !(array_search($sgroup['tbl_obj_id'], $sel_groups) === false)
                        ) {
                            $chk = "checked=\"checked\"";
                        }
                        $subgroups_list .= '<input id="checkbox_id' . $sgroup['tbl_obj_id'] . '" type="checkbox" value="' . $sgroup['tbl_obj_id'] . '" onclick="SelectSubGroup(this,\'' . $sgroup['title'] . '\')" ' . $chk . '>' . $sgroup['title'] . '<br>';
                    }
                    $subgroups_list .= "</div>";
                }

                $artist_mus_ids = SQLProvider::ExecuteQuery("
          SELECT tbl_obj_id, style_group from tbl__artist_group
          where style_group > 0");
                $stls_q = SQLProvider::ExecuteQuery("
          SELECT * FROM `tbl__styles` where style_group>0
          order by title");
                $stls = array();
                foreach ($stls_q as $stl) {
                    if (!isset($stls[$stl['style_group']]))
                        $stls[$stl['style_group']] = array();
                    array_push($stls[$stl['style_group']], $stl);
                }
                $stl_gs = SQLProvider::ExecuteQuery("
          SELECT * FROM tbl__styles_groups order by title");

                $s_artist_mus_ids = "";
                $s_artist_stl_groups = "";
                foreach ($artist_mus_ids as $gr_id) {
                    if (!IsNullOrEmpty($s_artist_mus_ids))
                        $s_artist_mus_ids .= ",";
                    $s_artist_mus_ids .= $gr_id['tbl_obj_id'];
                    $s_artist_stl_groups .= "\n artist_stl_groups[" . $gr_id['tbl_obj_id'] . "] = " . $gr_id['style_group'] . ";";
                }
                $s_artist_stl_group_ttl = "";
                $stylesArray = "";
                foreach ($stl_gs as $stl_g) {
                    $s_artist_stl_group_ttl .= "\n artist_stl_group_ttl[" . $stl_g['tbl_obj_id'] . "] = '" . $stl_g['title'] . "';";
                    $stl_arr = "";
                    if (isset($stls[$stl_g['tbl_obj_id']]))
                        foreach ($stls[$stl_g['tbl_obj_id']] as $stl) {
                            if (!IsNullOrEmpty($stl_arr))
                                $stl_arr .= ",";
                            $stl_arr .= "{id:" . $stl['tbl_obj_id'] . ", title:'" . $stl['title'] . "'}";
                        }
                    $stylesArray .= "\n artist_styles[" . $stl_g['tbl_obj_id'] . "] = new Array($stl_arr);";
                }

                $s_selstyles = "";
                foreach ($selstyles as $ss) {
                    if (!IsNullOrEmpty($s_selstyles))
                        $s_selstyles .= ",";
                    $s_selstyles .= $ss;
                }


                $udata = array(
                    "submit_text" => $this->GetMessage($user->authorized ? "save" : "reg"),
                    "imagesList" => $imagesList->RenderHTML(),
                    "mp3List" => $mp3List->RenderHTML(),
                    "images_visible" => sizeof($images) >= 4 ? "hidden" : "visible",
                    "mp3s_visible" => sizeof($mp3s) >= 5 ? "hidden" : "visible",
                    "groups_list" => $groups_list,
                    "subgroups_list" => $subgroups_list,
                    "selected_groups" => $selected_groups,
                    "st_visible" => sizeof($sel_groups) > 0 ? "" : "display:none;",
                    "artist_mus_ids" => "[$s_artist_mus_ids]",
                    "artist_stl_groups" => $s_artist_stl_groups,
                    "artist_stl_group_ttl" => $s_artist_stl_group_ttl,
                    "checked_styles" => "[$s_selstyles]",
                    "stylesArray" => $stylesArray
                );

                $userData->logo = GetFilename($userData->logo);
                $account->dataSource = array_merge($udata, $userData->GetData(), $captcha);
                break;
                }

            // AGENCY ---------------------------------------------------------------
            case "agency":
                {
                $table = new CNativeDataTable("tbl__agency_doc");

                $userData = null;
                $activityList = new CCheckBoxList();
                $activityList->dataSource = SQLProvider::ExecuteQuery("select * from tbl__agency_type ");
                $activityList->valueName = "tbl_obj_id";
                $activityList->titleName = "title";
                $activityList->baseName = "properties[kind_of_activity]";
                $activityList->class = "";
                $activityList->htmlEvents = array("onclick" => "javascript: CheckCountAct(this);");
                $activityList->checkedValue = "";

                $userData = $table->CreateNewRow(true);

                if ($this->IsPostBack) {
                    $props = GP("properties");
                    if (is_array($props)) {
                        $activityList->checkedValue = $props['kind_of_activity'];

                        $userValidator = $this->GetControl("agencyValidator");
                        $props["login"] = $props["email"];
                        $props['city'] = trim($props['city']);
                        $errorsData = $userValidator->Validate(&$props);
                        $props['title'] = trim($props['title']);
                        $props['title_url'] = translitURL($props['title']);
                        if ($this->CheckURL("agency", $props['title_url']))
                            array_push($errorsData, "Агентство с таким названием уже существует");

                        $kindList = $props['kind_of_activity'];
                        if (!sizeof($kindList))
                            array_push($errorsData, "не указан тип деятельности");

                        if (!$this->ValidateCaptcha())
                            array_push($errorsData, "не верно введены цифры");
                        $props['other_city'] = $props['city'];
                        $userData->FromHashMap($props);
                        if (sizeof($errorsData) > 0)
                            $errors->dataSource = array("message" => $errorsData[0]);
                        else {
                            $city_id = $this->CheckCity($props['city']);
                            if ($city_id) {
                                $props['city'] = $city_id;
                                $props['other_city'] = '';
                            }
                            else {
                                $props['other_city'] = $props['city'];
                                $props['city'] = 0;
                            }
                            $userData->city = $props['city'];
                            $userData->other_city = $props['other_city'];

                            $login = $table->SelectUnique(new CEqFilter(&$table->fields["login"], $props["login"]), false);
                            $r = SQLProvider::ExecuteQuery("select * from vw__all_users where login='".mysql_real_escape_string($props["login"])."'");
                            if (sizeof($r)!=0) {
                                $errors->dataSource = array("message" => "такой логин уже существует");
                            }
                            elseif (is_null($login)) {
                                $completeData = array();
                                $completeData["login"] = iconv($this->encoding, "utf-8", $userData->login);
                                $completeData["password"] = iconv($this->encoding, "utf-8", $userData->password);

                                $userData->active = 0;

                                $userData->registration_date = strftime('%Y-%m-%d %H:%M:%S', time());
                                $userData->registration_confirmed = 0;
                                $userData->registration_confirm_code = md5(rand(0, getrandmax()));
                                $userData->password = md5($userData->password);
                                $flogo = $_FILES["properties"];
                                if (is_array($flogo)) {
                                    $logo = $this->CreateLogo($userData->login, $flogo, "logo_image");
                                    if (!is_null($logo))
                                        $userData->logo_image = $logo;
                                }
                                $userData->edit_date = time();
                                $userData->kind_of_activity = 0;
                                $table->InsertObject(&$userData);

                                $completeData["id"] = $userData->tbl_obj_id;

                                SQLProvider::ExecuteNonReturnQuery("delete from tbl__agency2activity where tbl_obj_id=$userData->tbl_obj_id");
                                foreach ($kindList as $kind)
                                    SQLProvider::ExecuteNonReturnQuery("insert into tbl__agency2activity (tbl_obj_id,kind_of_activity) values ($userData->tbl_obj_id,$kind)");

                                $photos = $_FILES["photo_file"];
                                $ptitles = GP("photo_title", array());
                                if (is_array($ptitles)) {
                                    foreach ($ptitles as $j => $ptj) {
                                        $lFilename = "";
                                        $mFilename = "";
                                        $sFilename = "";
                                        $photo_id = $this->CreateImages($photos, $j, $ptj, $lFilename, $mFilename, $sFilename);
                                        if ($photo_id > 0) {
                                            SQLProvider::ExecuteNonReturnQuery("delete from tbl__agency_photos where child_id=$photo_id");
                                            SQLProvider::ExecuteNonReturnQuery("insert into tbl__agency_photos values($photo_id,$userData->tbl_obj_id)");
                                        }
                                    }
                                }

                                $title = iconv($this->encoding, "utf-8", 'Регистрация на EventCatalog.ru');
                                $completeData["link"] = "http://" . $_SERVER['HTTP_HOST'] . "/registration/confirm/code/" . $userData->registration_confirm_code;
                                $mbody = CStringFormatter::Format(file_get_contents(RealFile($this->mailpath)), $completeData);
                                SendHTMLMail($userData->email, $mbody, $title);

                                CURLHandler::Redirect(CURLHandler::$currentPath . "type/completere");
                            }
                            else
                                $errors->dataSource = array("message" => "такой логин уже существует");

                        }
                    }
                }
                $logos = preg_split("/\//", $userData->logo_image);
                $ls = sizeof($logos);
                $udata = array(
                    "submit_text" => $this->GetMessage($user->authorized ? "save" : "reg"),
                    "activityList" => $activityList->RenderHTML());
                $userData->logo_image = GetFilename($userData->logo_image);
                $account->dataSource = array_merge($udata, $userData->GetData(), $captcha);
                break;
                }
        }
    }
}

?>
