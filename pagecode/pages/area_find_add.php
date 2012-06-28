<?php
class area_find_add_php extends CPageCodeHandler
{
    public function area_find_add_php()
    {
        $this->CPageCodeHandler();
    }

    public function PreRender()
    {
        header('Content-type: text/html;charset=windows-1251');

        $city = GP("city", -1);
        $type = GP("type", -1);
        $subtype = GP("subtype", -1);
        $metro = GP("metro", -1);
        if (is_array($metro))
            $metro = array_shift($metro);
        $capacity = GP("capacity", array());
        if (isset($capacity) and !is_array($capacity))
            $capacity = array();

        $cost = GP("cost", array());
        $my_catering = GP("my_cathering");
        if (isset($cost) and !is_array($cost))
            $cost = array();
        $invite_catering = GP("invite_catering");
        $car_into = GP("car_into");

        $cities = SQLProvider::ExecuteQuery("select * from `tbl__city` where `active`=1 order by priority desc,title asc");
        array_unshift($cities, array("tbl_obj_id" => -1, "title" => "Все города", "priority" => 999));
        $city_list = "";
        foreach ($cities as $key => $value)
        {

            $city_list .= '<option';
            if ($value["priority"] > 0)
                $city_list .= ' style="font-weight:bold"';

            if ($value["tbl_obj_id"] == $city)
                $city_list .= ' selected';
            $city_list .= ' value="' . $value["tbl_obj_id"] . '">' . $value["title"] . '</option>';
        }

        $types = SQLProvider::ExecuteQuery("select tbl_obj_id, title from `tbl__area_types` order by priority desc,title asc");

        array_unshift($types, array("tbl_obj_id" => -1, "title" => "Все"));
        $category_list = "";
        foreach ($types as $key => $value)
        {
            $category_list .= '<option style="font-weight:bold"';
            if ($value["tbl_obj_id"] == $type and $subtype == -1)
                $category_list .= ' selected';
            $category_list .= ' value="' . $value["tbl_obj_id"] . '">' . $value["title"] . '</option>';
            $subtypes = SQLProvider::ExecuteQuery("select tbl_obj_id, title from `tbl__area_subtypes`
        where parent_id = " . $value["tbl_obj_id"] . " order by priority desc,title asc");
            foreach ($subtypes as $key => $svalue)
            {
                $category_list .= '<option';
                if ($value["tbl_obj_id"] == $type and $svalue["tbl_obj_id"] == $subtype)
                    $category_list .= ' selected';
                $category_list .= ' value="' . $value["tbl_obj_id"] . '_' . $svalue["tbl_obj_id"] . '">' . $svalue["title"] . '</option>';
            }
        }

        $lines = SQLProvider::ExecuteQuery("select tbl_obj_id, title, color from `tbl__metro_lines` order by order_num");
        array_unshift($lines, array("tbl_obj_id" => -1, "title" => "----------"));
        $metro_list = "";
        foreach ($lines as $key => $value)
        {
            if ($value["tbl_obj_id"] == -1) {
                $metro_list .= '<option style="font-weight:bold"';
                if ($value["tbl_obj_id"] == $metro)
                    $metro_list .= ' selected';
                $metro_list .= ' value="' . $value["tbl_obj_id"] . '">' . $value["title"] . '</option>';
            }
            else {
                $metro_list .= '<optgroup label="' . $value["title"] . '" style="color:#' . $value["color"] . '">';
                $stations = SQLProvider::ExecuteQuery("select tbl_obj_id, title from `tbl__metro_stations`
            where metro_line = " . $value["tbl_obj_id"] . " order by order_num");
                foreach ($stations as $key => $svalue)
                {
                    $metro_list .= '<option';
                    if ($svalue["tbl_obj_id"] == $metro)
                        $metro_list .= ' selected';
                    $metro_list .= ' value="' . $svalue["tbl_obj_id"] . '">' . $svalue["title"] . '</option>';
                }
                $metro_list .= '</optgroup>';
            }
        }

        $capacityRanges = array(
            array("from" => 0, "to" => 10, "title" => "до 10", "checked" => ""),
            array("from" => 10, "to" => 50, "title" => "10-50", "checked" => ""),
            array("from" => 50, "to" => 100, "title" => "50-100", "checked" => ""),
            array("from" => 100, "to" => 200, "title" => "100-200", "checked" => ""),
            array("from" => 200, "to" => 300, "title" => "200-300", "checked" => ""),
            array("from" => 300, "to" => 400, "title" => "300-400", "checked" => ""),
            array("from" => 400, "to" => 500, "title" => "400-500", "checked" => ""),
            array("from" => 500, "to" => 600, "title" => "500-600", "checked" => ""),
            array("from" => 600, "to" => 700, "title" => "600-700", "checked" => ""),
            array("from" => 700, "to" => 800, "title" => "700-800", "checked" => ""),
            array("from" => 800, "to" => 900, "title" => "800-900", "checked" => ""),
            array("from" => 900, "to" => 1000, "title" => "900-1000", "checked" => ""),
            array("from" => 1000, "to" => 1500, "title" => "1000-1500", "checked" => ""),
            array("from" => 1500, "to" => 10000, "title" => "1500-...", "checked" => "")
        );
        foreach ($capacityRanges as &$val)
        {
            $val['id'] = 'cap' . $val['from'] . '_' . $val['to'];
            $val['value'] = $val['from'] . '_' . $val['to'];
            if (array_search($val['value'], $capacity) !== false)
                $val['checked'] = 'checked';
        }

        $caps = new CCheckBoxList();
        $caps->dataSource = $capacityRanges;
        $caps->valueName = "value";
        $caps->titleName = "title";
        $caps->baseName = "capacity";
        $caps->class = "";
        $caps->checkedValue = "";
        $caps->col_count = 3;

        $costRanges = array(
            array("from" => 0, "to" => 1000, "title" => "до 1000 руб.", "checked" => ""),
            array("from" => 1000, "to" => 1500, "title" => "1000-1500 руб.", "checked" => ""),
            array("from" => 1500, "to" => 2000, "title" => "1500-2000 руб.", "checked" => ""),
            array("from" => 2000, "to" => 2500, "title" => "2000-2500 руб.", "checked" => ""),
            array("from" => 2500, "to" => 3000, "title" => "2500-3000 руб.", "checked" => ""),
            array("from" => 3000, "to" => 3500, "title" => "3000-3500 руб.", "checked" => ""),
            array("from" => 3500, "to" => 99999, "title" => "более 3500  руб.", "checked" => "")
        );
        foreach ($costRanges as &$val)
        {
            $val['id'] = 'cap' . $val['from'] . '_' . $val['to'];
            $val['value'] = $val['from'] . '_' . $val['to'];
            if (array_search($val['value'], $cost) !== false)
                $val['checked'] = 'checked';
        }
        $costs = new CCheckBoxList();
        $costs->dataSource = $costRanges;
        $costs->valueName = "value";
        $costs->titleName = "title";
        $costs->baseName = "cost";
        $costs->class = "";
        $costs->checkedValue = "";
        $costs->col_count = 2;

        $body = $this->GetControl("content");
        $body->dataSource = array(
            "city_list" => $city_list,
            "category_list" => $category_list,
            "cap_list" => $caps->RenderHTML(),
            "cost_list" => $costs->RenderHTML(),
            "metro_list" => $metro_list,
            "my_catering"=>$my_catering,
            "invite_catering" => $invite_catering,
            "car_into" => $car_into
        );
    }
}

?>
