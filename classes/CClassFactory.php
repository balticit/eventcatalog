<?php
class CClassFactory
{
  private static  $instance;

    public static function GetInstance()
    {
      if (is_null(CClassFactory::$instance))
      {
        CClassFactory::$instance = new CClassFactory();
      }
      return CClassFactory::$instance;
    }

    public static function  CreateClassIntance($className,$params = null)
    {
      $cls = new $className();
      $setAny = method_exists($cls,"__set");
      if (is_array( $params))
      {
        foreach ($params as $key=>$value)
        {
          if (property_exists($className,$key)||$setAny)
          {
            $cls->$key = $value;
          }
        }
        return $cls;
      }
      else
      {
        return $cls;
      }
    }
}
?>