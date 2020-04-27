<?php

namespace ParraWeb\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ResourceController extends Controller
{   
    public function index(Request $request)
    {
        $data = ((new static)->model)::query();
        if($request->has('order'))
        {
            $order = json_decode($request->order,true);

            $validator = Validator::make($order, [
                'column' => 'required|string|min:1',
                'dir' => 'nullable|string|in:asc,desc',
            ]);

            if ($validator->fails()) {
                return $this->getError('validation');
            }

            $column = $order['column'];
            $dir = isset($order['dir']) ? $order['dir'] : 'desc';

            $data->orderBy($column,$dir);
        }
        return response()->json(['data' => $data->get()]);
    }

    public function show($id)
    {
        return ((new static)->model)::findOrFail($id);
    }
    
    public function store(Request $request)
    {
        $this->validateRules(((new static)->model)::rules(['post']));
        $added = ((new static)->model)::create($request->all());
        return response()->json(['added' => $added],200);
    }

    public function storeMany(Request $request)
    {
        $this->validateRules(((new static)->model)::rules('*','post'));
        $added = ((new static)->model)::storeManyAndGet($request->all());
        return response()->json(['added' => $added],200);
    }

    public function update(Request $request, $id)
    {
        $this->validateRules(((new static)->model)::rules(['put']),$request->all(),['id'=>$id]);
        $update = ((new static)->model)::updateAndGet(['id'=>$id],$request->all());
        return response()->json(['updated'=> $update]);
    }

    public function updateMany(Request $request)
    {
        $this->validateRules(((new static)->model)::rules('*','put'),$request->all());
        $updated = ((new static)->model)::updateManyAndGet($request->all());
        return response()->json(['updated'=> $updated]);
    }

    public function destroy($id)
    {
        $this->validateRules(((new static)->model)::rules(['delete']),['id'=>$id]);
        $deleted = ((new static)->model)::findOrFail($id);
        $deleted->delete();
        return response()->json(['deleted'=> $id]);
    }

    public function destroyMany(Request $request)
    {
        $request->validate(['id'=>'required|array','id.*' => 'integer|min:1']);
        ((new static)->model)::findOrFail($request->id);
        ((new static)->model)::destroy($request->id);
        return response()->json(['deleted'=> $request->id]);
    }
}
