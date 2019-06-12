<?php
namespace App\Http\Controllers\Rest\V1;

use App\Models\User;

use Hub\Http\Request;

class UsersController extends \Hub\Base\Controller
{
    public function view($id)
    {
        if ($user = User::findOne([["id", "=", $id]])) {
            echo json_encode($user); exit;
        }
        echo json_encode(0); exit;
    }

    public function create()
    {
        if ($user = new User()) {
            if ($user->load(Request::all()) && $user->validate() && $user->save()) {
                echo json_encode($user->refresh()); exit;
            }
        }

        echo json_encode(0); exit;
    }

    public function update($id)
    {
        if ($user = User::findOne([["id", "=", $id]])) {
            if ($user->load(Request::all()) && $user->validate() && $user->save()) {
                echo json_encode(1); exit;
            }
        }
        echo json_encode(0); exit;
    }

    public function delete($id)
    {
        if ($user = User::findOne([["id", "=", $id]])) {
            echo json_encode($user->delete()); exit;
        }
        echo json_encode(0); exit;
    }
}
