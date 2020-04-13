<?php
namespace ParraWeb;

trait UpdateAndGet{
  public static function updateAndGet($param1=[],$param2=null,$param3=null)
  {
      $strict = true;
      $id = 'id';
      
      if($param2!=null)
      {
        if(gettype($param2)=='string')
        {
          $id = $param2;
        }
        else if(gettype($param2)=='boolean')
        {
          $strict = $param2;
        }
        else
        {
          throw new \Exception('Error in parameter number 2.');
        }
      }
      if($param3!=null)
      {
        if(gettype($param3)=='boolean')
        {
          $strict = $param3;
        }
        else
        {
          throw new \Exception('Error in parameter number 3.');
        }
      }
      
      $data = $param1;
      $model = self::findOrFail($data[$id]);
      if(count($data)>0)
      {
          if($strict)
          {
              $fillable = (new static)->fillable;
              if($fillable && count($fillable)>0)
              {
                  foreach($fillable as $value)
                  {
                      $model[$value] = $data[$value];
                  }
              }
              else
              {
                  $guarded = (new static)->guarded;
                  if($guarded && count($guarded)>0)
                  {
                      foreach($data as $key => $value)
                      {
                          if(!in_array($key,$guarded))
                          {
                              $model[$key] = $value;
                          }
                      }
                  }
                  else
                  {
                      throw new \Exception('$fillable and $guard are empty, if you '."don't ".'want to use them, you must send second parameter in updateAndGet (false)');
                  }
                  
              }
          }
          else
          {
              foreach($data as $key => $value)
              {
                  $model[$key] = $value;
              }
          }
          $model->save();
      }
      return $model;
  }
}