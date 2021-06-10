<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WebService;
class DocsController extends Controller
{
    //
    
    public function index() {
       
        if(env('APP_DOC') === false && env('APP_DOC_ENABLE') === false){
            return redirect('/');
        }
        $docs = WebService::all()->groupBy('module');
        return view('home', ['docs' =>$docs]);
    }
    
    public function detail($id) {
        if(env('APP_DOC') === false && env('APP_DOC_ENABLE') === false){
            return redirect('/');
        }
        $doc = WebService::findOrFail($id);
        return view('detail', ['doc' =>$doc]);
    }
    public function filePath($folder_name){
        
        return abort(403);
    }
}
