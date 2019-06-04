<?php
namespace App\Http\Controllers;

use View;

class HomeController extends \Hub\Base\Controller
{
    public function index()
    {
        return View::render("index", ['test' => 'arg']);
    }
}
