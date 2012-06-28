<?php
function range_search(&$result)
{
    switch ($result['searchtype']) {
        case 'title':
            return 3;
        case 'category':
            return 2;
        case 'other':
            return 1;
    }
}

function search_sort(&$a, &$b)
{
    $relev_a = intval($a['relev']);
    $relev_b = intval($b['relev']);

    if ($relev_a == $relev_b) {
        $relev_a = range_search($a);
        $relev_b = range_search($b);
        if ($relev_a == $relev_b) {
            return strcasecmp($a['title'], $b['title']);
        }
    }
    return ($relev_a > $relev_b) ? -1 : 1;
}

usort($searchResult, "search_sort");

$number = ($page - 1) * $this->pageSize + 1;

$searchResult = array_slice($searchResult, ($page - 1) * $this->pageSize, $this->pageSize);


foreach ($searchResult as &$result) {

    $result['number'] = $number++;
    if (isset($result['title_url'])) {
        $result['link'] = "/" . $result['alias'] . "/" . $result['title_url'];
    }
    else {
        $resType = $result['alias'];
        if ($resType == 'contractor' ||
            $resType == "area" ||
            $resType == 'artist' ||
            $resType == 'agency' ||
            $resType == 'eventtv' ||
            $resType == 'public'
        ) {
            $title_url = SQLProvider::ExecuteScalar("select title_url from tbl__" . $result['alias'] . "_doc where tbl_obj_id = " . $result['id']);
            $result['link'] = "/" . $result['alias'] . "/" . $title_url;
        }
        else
            $result['link'] = "/" . $result['alias'] . "/details/id/" . $result['id'];
    }


    if (!isset($result['category'])) {
        $category_mustset = true;
        switch ($result['alias']) {
            case 'agency':
                $sql_category = "select GROUP_CONCAT(tbl__agency_type.title SEPARATOR ', ') as title from tbl__agency_type, tbl__agency_doc where tbl__agency_type.tbl_obj_id=tbl__agency_doc.kind_of_activity and tbl__agency_doc.tbl_obj_id=" . $result['id'];
                $sql_category2 = "select GROUP_CONCAT(tbl__agency_type.title SEPARATOR ', ') as title from tbl__agency_type, tbl__agency_doc where tbl__agency_type.tbl_obj_id=tbl__agency_doc.kind2 and tbl__agency_doc.tbl_obj_id=" . $result['id'];
                break;
            case 'area':
                $sql_category = "select GROUP_CONCAT(tbl__area_subtypes.title SEPARATOR ', ') as title from tbl__area_subtypes, tbl__area2subtype where tbl__area2subtype.area_id=" . $result['id'] . " and
                                tbl__area2subtype.subtype_id=tbl__area_subtypes.tbl_obj_id and
                                tbl__area2subtype.advanced=0";
                $sql_category2 = "select GROUP_CONCAT(tbl__area_subtypes.title SEPARATOR ', ') as title from tbl__area_subtypes, tbl__area2subtype where tbl__area2subtype.area_id=" . $result['id'] . " and
                                tbl__area2subtype.subtype_id=tbl__area_subtypes.tbl_obj_id and
                                tbl__area2subtype.advanced=1";
                break;
            case 'contractor':
                $sql_category = "select GROUP_CONCAT(tbl__activity_type.title SEPARATOR ', ' ) as title from tbl__activity_type,tbl__contractor2activity where tbl__contractor2activity.tbl_obj_id=" . $result['id'] . " and
                                tbl__contractor2activity.kind_of_activity=tbl__activity_type.tbl_obj_id and
                                tbl__contractor2activity.advanced=0";
                $sql_category2 = "select GROUP_CONCAT(tbl__activity_type.title SEPARATOR ', ' ) as title from tbl__activity_type,tbl__contractor2activity where tbl__contractor2activity.tbl_obj_id=" . $result['id'] . " and
                                tbl__contractor2activity.kind_of_activity=tbl__activity_type.tbl_obj_id and
                                tbl__contractor2activity.advanced=1";
                break;
            case 'artist':
                $sql_category = "select GROUP_CONCAT(tbl__artist_subgroup.title SEPARATOR ', ') as title from tbl__artist_subgroup,tbl__artist2subgroup where tbl__artist_subgroup.tbl_obj_id=tbl__artist2subgroup.subgroup_id and
                                " . $result['id'] . "=tbl__artist2subgroup.artist_id";
                $sql_category2 = "select GROUP_CONCAT(tbl__artist_subgroup.title SEPARATOR ', ') as title from tbl__artist_subgroup,tbl__artist2advancedsubgroup where tbl__artist_subgroup.tbl_obj_id=tbl__artist2advancedsubgroup.subgroup_id and
                                " . $result['id'] . "=tbl__artist2advancedsubgroup.artist_id";
                break;
            default:
                $result['category'] = 'не задана';
                $result['category2'] = 'не задана';
                $category_mustset = false;
                break;
        }
        if ($category_mustset) {
            $category = SQLProvider::ExecuteQuery($sql_category);
            if ($category) {
                $result['category'] = $category[0]['title'];
            }
            else {
                $result['category'] = 'не задана';
            }

            $category2 = SQLProvider::ExecuteQuery($sql_category2);
            if ($category2) {
                $result['category2'] = $category2[0]['title'];
            }
            else {
                $result['category2'] = 'не задана';
            }
        }
    }
}
