<?php

namespace ParraWeb\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MultipleResourceController extends Controller
{   
    public function storeMultiples(Request $request)
    {
        $models = (new static)->models;
    }
}
