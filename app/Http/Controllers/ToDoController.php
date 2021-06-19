<?php

namespace App\Http\Controllers;

use App\Helpers\QB;
use App\Models\ToDo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\ToDoRequest;
use App\Http\Resources\TodoResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ToDoController extends Controller
{

    // get list of all the ToDos
   
    public function index(Request $request)
    {
        $oInput = $request->all();

        $oQb = ToDo::with('user')->where('user_id',Auth::user()->id)->orderByDesc('updated_at');
        $oQb = QB::where($oInput,"id",$oQb);
        $oQb = QB::whereLike($oInput,"title",$oQb);
        $oQb = QB::whereLike($oInput,"description",$oQb);
        $oToDos = $oQb->paginate(10);
        
        return [
            'message'=>__('message.general.list',["mod"=>"ToDo"]),
            'data' => TodoResource::collection($oToDos),
        ];
    }

    // Store new ToDo
    public function store(ToDoRequest $request)
    {
        $oInput = $request->all();
        
        $oToDo = ToDo::create([
            'title'         =>  $oInput['title'],
            'description'   =>  $oInput['description'],
            'user_id'       =>  Auth::user()->id,
            'created_at'    =>  Carbon::now()->toDateTimeString(),
            'updated_at'    =>  Carbon::now()->toDateTimeString(),
        ]);

        $oToDo= ToDo::with('user')->findOrFail($oToDo->id);
        
        return [
            'message'=>__('message.general.create',["mod"=>"ToDo"]),
            'data' => TodoResource::make($oToDo),
        ];
    }
    // Show ToDo details

    public function show($id)
    {

        $oToDo= ToDo::with('user')->where('user_id',Auth::user()->id)->findOrFail($id);

        return [
            'message'=>__('message.general.detail',["mod"=>"ToDo"]),
            'data' => TodoResource::make($oToDo),
        ];
    }
    // Update ToDo details
    
    public function update(ToDoRequest $request, $id)
    {
        $oInput = $request->all();

        $oToDo = ToDo::where('user_id',Auth::user()->id)->findOrFail($id); 

        $oToDos = $oToDo->update([
            'title'         =>  $oInput['title'],
            'description'   =>  $oInput['description'],
            'updated_at'    =>  Carbon::now()->toDateTimeString(),
        ]);
        $oToDo = ToDo::with('user')->findOrFail($id);

        return [
            'message'=>__('message.general.update',["mod"=>"ToDo"]),
            'data' => TodoResource::make($oToDo),
        ];
    }

    // Delete ToDo 

    public function destroy($id)
    {
        $oToDo = ToDo::where('user_id',Auth::user()->id)->findOrFail($id);
        $oToDo->delete();
        return [
            'message'=>__('message.general.delete',["mod"=>"ToDo"])
        ];
    }
}
