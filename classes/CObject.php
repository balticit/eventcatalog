<?php
class CObject
{

  public $tag;

  public $objtype ;

  protected $eventMap = array();

  public function  CObject()
  {
    $this->objtype =get_class($this);
  }

  public function ToHashMap($tree=true)
  {
    $result = array();
    foreach ($this as $key => $value)
    {
      $result[$key] = (method_exists($value,"ToHashMap")&&(is_object($value)))?($tree)?$value->ToHashMap():null:$value;
    }
    return $result;
  }

  public function GetType()
  {
    return get_class($this);
  }

  public function AddEvent($key,$function,&$object = null)
  {
    $this->eventMap[$key] = new CEventHandler($function,&$object);
  }

  public function RemoveEvent($key)
  {
    if (isset($this->eventMap[$key]))
    {
      unset($this->eventMap[$key]);
    }
  }

  public function RaiseEvent($key,&$args = array())
  {
    if (isset($this->eventMap[$key]))
    {
      if (is_a($this->eventMap[$key],"CEventHandler"))
      {
        $this->eventMap[$key]->RaiseEvent(&$this,&$args);
      }
    }
  }

  public function FromHashMap($hashMap)
  {
    $hKeys = array_keys($hashMap);
    $Iset = method_exists($this,"__set");
    $type = $this->GetType();
    foreach ($hKeys as $hKey)
    {
      if (property_exists($type,$hKey)||$Iset)
      {
        if (is_array($hashMap[$hKey])&&method_exists($this->$hKey,"FromHashMap"))
        {
          $this->$hKey->FromHashMap($hashMap[$hKey]);
        }
        else
        {
          $this->$hKey = $hashMap[$hKey];
        }
      }
    }
  }
}
?>