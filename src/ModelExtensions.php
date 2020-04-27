<?php

namespace ParraWeb;

use ParraWeb\ValidationRules;
use ParraWeb\AditionalEloquent;

trait ModelExtensions{
  use ValidationRules,AditionalEloquent;

  public static function getFillableColumns($data=null)
  {
      $fillable = (new static)->fillable;
      if($fillable && count($fillable)>0)
      {
          return $fillable;
      }
      if(!$data)
      {
          throw new \Exception('$fillable or data to get keys are required.');
      }
      else
      {
          $guarded = (new static)->guarded;
          if($guarded && count($guarded)>0)
          {
              $columns = [];
              foreach((is_array($data) ? get_object_vars($data[0]) : get_object_vars($data)) as $value)
              {
                  if(!in_array($value,$guarded))
                  {
                      $columns[] = $value;
                  }
              }
              return $columns;
          }
          else
          {
              throw new \Exception('$fillable or data to get keys are required.');
          }
      }
  }
}