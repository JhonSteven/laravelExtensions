<?php
namespace ParraWeb;

use Illuminate\Support\Facades\DB;

trait AditionalEloquent{
  public static function updateAndGet($where,$data=[])
  {
    $primaryKey = 'id';
    if((new static)->primaryKey)
    {
        $primaryKey = (new static)->primaryKey;
    }
    
    $model = self::where($where)->firstOrFail();
    if($model)
    {
        $fillable = self::getFillableColumns($data);
        if($fillable && count($fillable)>0)
        {
        $emptyColumns = false;
        foreach($fillable as $value)
        {
            if($value!==$primaryKey && isset($data[$value]))
            {
            $model[$value] = $data[$value];
            }
            else
            {
            $emptyColumns = true;
            }
        }
        }
        $model->save();
        if($emptyColumns)
        {
        return self::where($primaryKey,$model[$primaryKey])->first();
        }
        return $model;
    }
  }

  public static function updateMany($param1,$id='id')
  {
    DB::beginTransaction();
    try {
        foreach($param1 as $key => $data)
        {
            self::where($id,$data[$id])->update($data);
        }
        DB::commit();
        return true;
    } catch (\Exception $e) {
        DB::rollback();
        throw new \Exception('Error storing.');
    }
  }

  public static function updateManyAndGet($param1,$id='id')
  {
    $updated = [];
    DB::beginTransaction();
    try {
        foreach($param1 as $key => $data)
        {
            $updated[] = self::updateAndGet([$id=>$data[$id]],$data);
        }
        DB::commit();
        return $updated;
    } catch (\Exception $e) {
        DB::rollback();
        throw new \Exception('Error storing.');
    }
  }

  public static function storeMany($allData,$change=[])
  {
      $fillable = self::getFillableColumns($allData);

      $allDataToAdd = [];
      foreach($allData as $key => $data)
      {
          $model = [];
          if($fillable && count($fillable)>0)
          {
              foreach($fillable as $value)
              {
                  if($change && count($change)>0 && isset($change[$value]))
                  {
                      $model[$value] = $data[$change[$value]];
                  }
                  else
                  {
                      if(isset($data[$value]))
                      {
                          $model[$value] = $data[$value];
                      }
                  }
              }
          }
          if($model)
          {
              array_push($allDataToAdd,$model);
          }
      }
      self::insert($allDataToAdd);
      return true;
  }

  public static function storeManyAndGet($allData,$change=[])
  {
      DB::beginTransaction();
      try {
          $fillable = self::getFillableColumns($allData);
          $primaryKey = 'id';
          if((new static)->primaryKey)
          {
              $primaryKey = (new static)->primaryKey;
          }

          $allDataToAdd = [];
          foreach($allData as $key => $data)
          {
              $model = [];
              $emptyColumns = false;
              if($fillable && count($fillable)>0)
              {
                  foreach($fillable as $value)
                  {
                      if($change && count($change)>0 && isset($change[$value]))
                      {
                          $model[$value] = $data[$change[$value]];
                      }
                      else
                      {
                          if(isset($data[$value]))
                          {
                              $model[$value] = $data[$value];
                          }
                          else
                          {
                              $emptyColumns = true;
                          }
                      }
                  }
              }
              if($model)
              {
                  $newCreated = self::create($model);
                  if($emptyColumns)
                  {
                      array_push($allDataToAdd,self::where($primaryKey,$newCreated[$primaryKey])->first());
                  }
                  else
                  {
                      array_push($allDataToAdd,$newCreated);
                  }
              }
          }
          DB::commit();
          return $allDataToAdd;
      } catch (\Exception $e) {
          DB::rollback();
          throw new \Exception('Error storing.');
      }
  }
}