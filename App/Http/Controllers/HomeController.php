<?php
namespace App\Http\Controllers;

use Hub\Http\View;

class HomeController extends \Hub\Base\Controller
{
    public function index()
    {
        return View::render("default/views/index", ['test' => 'arg']);
    }
}
