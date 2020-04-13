<?php
namespace ParraWeb;

trait ValidationRules{

  public static function rules($before=null,$method=null) 
  {
    return self::getRules((new static)->rules,$method,$before); 
  }

  private static function getRules($validationRules,$selectedMethod=null,$before=null)
  {
    $method = request()->method();
    if(gettype($before)=='array')
    {
      $method = $before[0];
    }
    else if($selectedMethod)
    {
      $method = $selectedMethod;
    }
    if($method=='patch')
    {
      $method = 'put';
    }
    $method = strtolower($method);

    $rules = [];
    foreach($validationRules as $key => $rulesV)
    {
      if(in_array($method,explode(",", strtolower($key))))
      {
        if($before==null || gettype($before)=='array')
        {
          $rules = array_merge($rules,$rulesV);
        }
        else
        {
          $rulesWithBefore = [];
          foreach($rulesV as $keyV => $ruleEnd)
          {
            $rulesWithBefore[$before.'.'.$keyV] = $ruleEnd;
          }
          $rules = array_merge($rules,$rulesWithBefore);
        }
      }
    }
    return $rules;
  }
}