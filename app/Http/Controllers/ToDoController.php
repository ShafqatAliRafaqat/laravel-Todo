<?php

namespace App\Http\Controllers;

use App\Models\ToDo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\QB;
use Auth;
use Illuminate\Support\Facades\Validator;

class ToDoController extends Controller
{
    use \App\Traits\WebServicesDoc;

    // get list of all the ToDos
   
    public function index(Request $request)
    {
        $oInput = $request->all();

        $oQb = ToDo::with(['createdBy','updatedBy'])->where('created_by',Auth::user()->id)->orderByDesc('updated_at');
        $oQb = QB::where($oInput,"id",$oQb);
        $oQb = QB::whereLike($oInput,"title",$oQb);
        $oQb = QB::whereLike($oInput,"description",$oQb);
        $oToDos = $oQb->paginate(10);
        
        $oResponse = responseBuilder()->success(__('message.general.list',["mod"=>"ToDo"]), $oToDos, false);
        $this->urlRec(1, 0, $oResponse);
        return $oResponse;
    }

    // Store new ToDo
    public function store(Request $request)
    {
        $oInput = $request->all();

        $oValidator = Validator::make($oInput,[
            'title'    => 'required|max:50',
            'description'=> 'required|max:200',
        ]);

        if($oValidator->fails()){
            return responseBuilder()->error(__($oValidator->errors()->first()), 400, false);
        }
        
        $oToDo = ToDo::create([
            'title'         =>  $oInput['title'],
            'description'   =>  $oInput['description'],
            'created_by'    =>  Auth::user()->id,
            'updated_by'    =>  Auth::user()->id,
            'created_at'    =>  Carbon::now()->toDateTimeString(),
            'updated_at'    =>  Carbon::now()->toDateTimeString(),
        ]);

        $oToDo= ToDo::with(['createdBy','updatedBy'])->findOrFail($oToDo->id);

        $oResponse = responseBuilder()->success(__('message.general.create',["mod"=>"ToDo"]), $oToDo, false);
        $this->urlRec(1, 1, $oResponse);
        return $oResponse;
    }
    // Show ToDo details

    public function show($id)
    {

        $oToDo= ToDo::with(['createdBy','updatedBy'])->where('created_by',Auth::user()->id)->findOrFail($id);

        $oResponse = responseBuilder()->success(__('message.general.detail',["mod"=>"ToDo"]), $oToDo, false);
        $this->urlRec(1, 2, $oResponse);
        return $oResponse;
    }
    // Update ToDo details
    
    public function update(Request $request, $id)
    {
        $oInput = $request->all();

        $oValidator = Validator::make($oInput,[
            'title'   => 'required|max:50',
            'description'   => 'required|max:200',
        ]);

        if($oValidator->fails()){
            return responseBuilder()->error(__($oValidator->errors()->first()), 400, false);
        }

        $oToDo = ToDo::where('created_by',Auth::user()->id)->findOrFail($id); 

        $oToDos = $oToDo->update([
            'title'         =>  $oInput['title'],
            'description'   =>  $oInput['description'],
            'updated_by'    =>  Auth::user()->id,
            'updated_at'    =>  Carbon::now()->toDateTimeString(),
        ]);
        $oToDo = ToDo::with(['createdBy','updatedBy'])->findOrFail($id);

        $oResponse = responseBuilder()->success(__('message.general.update',["mod"=>"ToDo"]), $oToDo, false);
        
        $this->urlRec(1, 3, $oResponse);
        
        return $oResponse;
    }

    // Delete ToDo 

    public function destroy($id)
    {
        $oToDo = ToDo::where('created_by',Auth::user()->id)->findOrFail($id);
        $oToDo->delete();
        $oResponse = responseBuilder()->success(__('message.general.delete',["mod"=>"ToDo"]));
        $this->urlRec(1, 4, $oResponse);
        return $oResponse;
    }
}
