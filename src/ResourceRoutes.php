<?php
namespace ParraWeb;

use Illuminate\Support\Facades\Route;

class aditionalResourceRoutes{
  public $allMethods = ["index","store","store-many","update","update-many","destroy","destroy-many","show","show-many","edit","create"];

  public function exceptRoutes($methods)
  {
    $filteredMethods = [];
    if(is_array($methods))
    {
      foreach($this->allMethods as $method)
      {
        if(!in_array($method,$methods))
        {
          $filteredMethods[] = $method;
        }
      }
    }
    else
    {
      foreach($this->allMethods as $method)
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
      $filteredMethods = [];
      foreach($methods as $method)
      {
        if(in_array($method,$this->allMethods))
        {
          $filteredMethods[] = $method;
        }
      }
      return $filteredMethods;
    }
    else
    {
      return in_array($methods,$this->allMethods) ? [$methods] : [];
    }
  }
  
  public function getAditionalRoute($path,$controller,$methods)
  {
    $aditionalRoutes = [];
    if(in_array('store-many',$methods))
    {
      $aditionalRoutes [] = Route::post($path.'/store-many',$controller.'@storeMany')->name($path.'.store-many');
    }
    if(in_array('update-many',$methods))
    {
      $aditionalRoutes [] = Route::put($path.'/update-many',$controller.'@updateMany')->name($path.'.update-many');
    }
    if(in_array('destroy-many',$methods))
    {
      $aditionalRoutes [] = Route::delete($path.'/destroy-many',$controller.'@destroyMany')->name($path.'.destroy-many');
    }
    if(in_array('show-many',$methods))
    {
      $aditionalRoutes [] = Route::get($path.'/show-many',$controller.'@showMany')->name($path.'.show-many');
    }
    return $aditionalRoutes;
  }
}

trait ResourceRoutes {
  public static function routes($routes,$filtered=[])
  {
    $aditional = new aditionalResourceRoutes;
    $collectionsRoutes = [];
    $methods = $aditional->allMethods;
    $controller = (isset($filtered['controller'])) ? $filtered['controller'] : 'ResourceController';


    if($filtered)
    {
      if(isset($filtered['except']))
      {
        $methods = $aditional->exceptRoutes($filtered['except']);
      }
      else if(isset($filtered['only']))
      {
        $methods = $aditional->onlyRoutes($filtered['only']);
      }
    }

    if(is_array($routes))
    {
      foreach($routes as $key => $route)
      {
        if(is_string($key))
        {
          $collectionsRoutes[] = Route::resource($key,$route ? $route : $controller)->only($methods);
          array_merge($collectionsRoutes,$aditional->getAditionalRoute($key,$route ? $route : $controller,$methods));
        }
        else
        {
          $collectionsRoutes[] = Route::resource($route,$controller)->only($methods);
          array_merge($collectionsRoutes,$aditional->getAditionalRoute($route,$controller,$methods));
        }
      }
    }
    else{
      $collectionsRoutes[] = Route::resource($routes,$controller)->only($methods);
      array_merge($collectionsRoutes,$aditional->getAditionalRoute($routes,$controller,$methods));

    }
    return $collectionsRoutes;
  }


}