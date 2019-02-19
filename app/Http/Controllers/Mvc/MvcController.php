<?php
    namespace App\Http\Controllers\Mvc;

    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    class MvcController extends Controller{
        public function bst(){
            $data = [
                'title' => 'MVC-Test',
            ];
            return view('mvc.bst',$data);
        }
    }