<?php
namespace App\Http\Controllers;

use Hub\Base\View;

class HomeController extends \Hub\Base\Controller
{
    public function index()
    {
        // for($i = 0; $i < 2; $i++){
        //     $job = new \App\Job();
        //     $job->task = "App\Tasks\Base@execute";
        //     $job->queue = 'default';
        //     $job->arguments = json_encode(["this" => "that"]);
        //     $job->created_at = time();
        //     $job->updated_at = time();
        //     $job->save();
        // }
        return View::render("index", ['test' => 'arg']);
    }
}
