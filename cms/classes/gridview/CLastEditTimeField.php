<?php
class CLastEditTimeField extends CGridDataField
{
    
    public $editModeTemplate = "<td {class}>{value}<input type=\"hidden\" name=\"{name}\" value=\"{value}\"</td>";

    public $addModeTemplate = "<td {class}>{value}<input type=\"hidden\" name=\"{name}\" value=\"{value}\"</td>";
    
    public function CLastEditTimeField()
    {
        $this->CGridDataField();
    }
    
    public function RenderItem()
    {
        $value = date('Y-m-d H:i:s',strtotime($this->GetValue()));
        return CStringFormatter::Format($this->itemTemplate,array("class"=>$this->BuildItemClassString(),"value"=>$value));
    }
    
   
    public function RenderEditItem()
    {
        $value = date('Y-m-d H:i:s');
        $name = "$this->parentId[$this->dbField]";
        $this->SetValue($value);
        return CStringFormatter::Format($this->editModeTemplate,array("class"=>$this->BuildClassString($this->editClass),"value"=>$value, "name"=>$name));
    }    
    
    public function RenderAddItem()
    {
        $value = date('Y-m-d H:i:s');
        $name = "$this->parentId[$this->dbField]";
        $this->SetValue($value);
        return CStringFormatter::Format($this->addModeTemplate, array("class"=>$this->BuildClassString($this->addClass),"value"=>$value, "name"=>$name));
    }
    
}  