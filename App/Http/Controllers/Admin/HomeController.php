<?php
namespace App\Http\Controllers\Admin;

use Frame;

use Hub\Http\View;

use App\Models\User;

class HomeController extends \Hub\Base\Controller
{

    public $layout = "admin/layouts/main";

    public function index()
    {
        return View::render("admin/views/index", ['test' => 'arg']);
    }

    public function login()
    {
        $user = new User();

        if (Frame::$app->request->getMethod() == "post") {
            if ($user->load() && $user->login()) {
                header("Location: /succces");
            }
        }

        return View::render("admin/views/login", [
            "model" => $user
        ]);
    }
}
