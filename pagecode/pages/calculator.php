<?php
class calculator_php extends CPageCodeHandler
{
    public function calculator_php()
    {
        $this->CPageCodeHandler();
    }

    public function PreRender()
    {
        $errors = $this->GetControl("errors");
        $errorsDS = array();
        $inputDS = array();
        $inputDS["guestsCount"] = GP("guestsCount");
        $banquetBuffet = new CSelect();
        $banquetBuffet->class = "alko";
        $banquetBuffet->name = "common[banquetBuffet]";
        $banquetBuffet->AddItem("������", "banquet", GP(array("common", "banquetBuffet")) == "banquet");
        $banquetBuffet->AddItem("������", "buffet", GP(array("common", "banquetBuffet")) == "buffet");
        $inputDS["banquetBuffet"] = $banquetBuffet->Render();

        $eventType = new CSelect();
        $eventType->class = "alko";
        $eventType->name = "common[eventType]";
        $eventType->AddItem("�����������", "presentation", GP(array("common", "eventType")) == "presentation");
        $eventType->AddItem("����������", "corporative", GP(array("common", "eventType")) == "corporative");
        $eventType->AddItem("����", "reception", GP(array("common", "eventType")) == "presentation");
        $inputDS["eventType"] = $eventType->Render();

        $season = new CSelect();
        $season->class = "alko";
        $season->name = "specific[season]";
        $seaz = GP(array("specific", "season"));
        $season->AddItem("����", "winter", $seaz == "winter");
        $season->AddItem("�����", "spring", $seaz == "spring");
        $season->AddItem("����", "summer", $seaz == "summer");
        $season->AddItem("�����", "autumn", $seaz == "autumn");
        $inputDS["season"] = $season->Render();

        $duration = new CSelect();
        $duration->class = "alko";
        $duration->name = "common[duration]";
        $dur = GP(array("common", "duration"));
        for ($i = 2; $i < 11; $i++)
        {
            $duration->AddItem($i, $i, $i == $dur);
        }
        $inputDS["duration"] = $duration->Render();

        $areaType = new CSelect();
        $areaType->class = "alko";
        $areaType->name = "common[areaType]";
        $area = GP(array("common", "areaType"));
        $areaType->AddItem("��������", "open", $area == "open");
        $areaType->AddItem("��������", "closed", $area == "closed");
        $areaType->AddItem("��������", "restaraunt", $area == "restaraunt");
        $areaType->AddItem("����", "club", $area == "club");
        $inputDS["areaType"] = $areaType->Render();

        $ch = 'checked="checked"';
        $inputDS["wine"] = (GP(array("alcoholDrinks", "wine"), false)) ? $ch : "";
        $inputDS["champagne"] = (GP(array("alcoholDrinks", "champagne"), false)) ? $ch : "";
        $inputDS["cognac"] = (GP(array("alcoholDrinks", "cognac"), false)) ? $ch : "";
        $inputDS["vodka"] = (GP(array("alcoholDrinks", "vodka"), false)) ? $ch : "";
        $inputDS["cocktail"] = (GP(array("alcoholDrinks", "cocktail"), false)) ? $ch : "";
        $inputDS["juice"] = (GP(array("softDrinks", "juice"), false)) ? $ch : "";
        $inputDS["water"] = (GP(array("softDrinks", "water"), false)) ? $ch : "";


        $input = $this->GetControl("input");
        $input->dataSource = $inputDS;

        if (empty($_REQUEST["guestsCount"])) {
            array_push($errorsDS, array("error" => "������� ���������� ������"));
            $errors->dataSource = $errorsDS;

            return;
        }

        $alcocalc = new AlcoholCalculator();

        if (!isset($_REQUEST["alcoholDrinks"]) || !is_array($_REQUEST["alcoholDrinks"])) {
            $_REQUEST["alcoholDrinks"] = array();
        }

        if (!isset($_REQUEST["softDrinks"]) || !is_array($_REQUEST["softDrinks"])) {
            $_REQUEST["softDrinks"] = array();
        }

        $keys = array_keys($_REQUEST["alcoholDrinks"]);
        foreach ($keys as $key)
        {
            if ($_REQUEST["alcoholDrinks"][$key] == "on") {
                $_REQUEST["alcoholDrinks"][$key] = 1;
            }
        }

        $keys = array_keys($_REQUEST["softDrinks"]);
        foreach ($keys as $key)
        {
            if ($_REQUEST["softDrinks"][$key] == "on") {
                $_REQUEST["softDrinks"][$key] = 1;
            }
        }

        $AlcoholDrinksPackaging = array(
            "wine" => "����: {wine} ���. �� 0.75 �.",
            "vodka" => "�����: {vodka} ���. �� 1 �.",
            "cognac" => "������: {cognac} ���. �� 0.5 �.",
            "champagne" => "����������: {champagne} ���. �� 0.75 �.",
            "cocktail" => "��������: {cocktail} ����. �� 0.2 �."
        );

        $emp = "�������� ����������� �������";
        $SoftDrinksPackaging = array(
            "juice" => "���: {juice} ��. �� 1 �.",
            "water" => "�������� ����: {water} ���. �� 0.5 �."
        );

        $result = $alcocalc->calc("alcoholDrinks", $_REQUEST["guestsCount"],
            $_REQUEST["alcoholDrinks"], $alcocalc->defaultAlcoholDrinksPackaging,
            $_REQUEST["common"], $_REQUEST["specific"]
        );
        $alcohal = array();
        foreach ($result as $key => $value) {
            array_push($alcohal, array("item" => CStringFormatter::Format($AlcoholDrinksPackaging[$key], array($key => $value))));
        }
        if (sizeof($alcohal) == 0) {
            array_push($alcohal, array("item" => $emp));
        }
        $alcoOutput = $this->GetControl("alcoOutput");
        $alcoOutput->dataSource = $alcohal;
        $result = $alcocalc->calc("softDrinks", $_REQUEST["guestsCount"],
            $_REQUEST["softDrinks"], $alcocalc->defaultSoftDrinksPackaging,
            $_REQUEST["common"], $_REQUEST["specific"]
        );
        $nalcohal = array();
        foreach ($result as $key => $value) {
            array_push($nalcohal, array("item" => CStringFormatter::Format($SoftDrinksPackaging[$key], array($key => $value))));
        }
        if (sizeof($nalcohal) == 0) {
            array_push($nalcohal, array("item" => $emp));
        }
        $softOutput = $this->GetControl("softOutput");
        $softOutput->dataSource = $nalcohal;
    }
}

?>
