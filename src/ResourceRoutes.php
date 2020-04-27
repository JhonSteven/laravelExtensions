<?php
namespace ParraWeb;

use Illuminate\Support\Facades\Route;

trait ResourceRoutes {
  protected static $methods = ["store","update","destroy","show","edit","create"];

  public function exceptRoutes($methods)
  {
    $filteredMethods = [];
    if(is_array($methods))
    {
      foreach($this->$methods as $method)
      {
        if(!in_array($method,$methods))
        {
          $filteredMethods[] = $method;
        }
      }
    }
    else
    {
      foreach($this->$methods as $method)
      {
        if($method!=$methods)
        {
          $filteredMethods[] = $method;
        }
      }
    }
    return $filteredMethods;
  }

  public function onlyRoutes($methods)
  {
    if(is_array($methods))
    {
      return $methods;
    }
    else
    {
      return [$methods];
    }
  }

  public static function routes($routes,$filtered=[]){
    $collectionsRoutes = [];
    $methods = self::$methods;
    $controller = (isset($filtered['controller'])) ? $filtered['controller'] : 'ResourceController';


    if($filtered)
    {
      if(isset($filtered['Routes']))
      {
        $methods = (new self)->exceptRoutes($filtered['except']);
      }
      else if(isset($filtered['only']))
      {
        $methods = (new self)->onlyRoutes($filtered['only']);
      }
    }
    if(is_array($routes))
    {
      foreach($routes as $key => $route)
      {
          if(is_string($key))
          {
            $collectionsRoutes[] = Route::resource($key,$route ? $route : $controller)->only($methods);
          }
          else
          {
            $collectionsRoutes[] = Route::resource($route,$controller)->only($methods);
          }
      }
    }
    else{

      $collectionsRoutes[] = Route::resource($routes,$controller)->only($methods);
    }
    return $collectionsRoutes;
  }
}