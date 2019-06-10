<?php
namespace App\Http\Controllers\Rest\V1;

use App\Models\User;

use Hub\Http\Request;

class UsersController extends \Hub\Base\Controller
{
    public function view($id)
    {
        $user = User::findOne([["id", "=", $id]]);
        echo json_encode($user); exit;
    }

    public function create()
    {
        $user = new User();
        if ($user->load(Request::all()) && $user->validate() && $user->save()) {
            echo json_encode($user->refresh()); exit;
        }
    }

    public function update($id)
    {
        $user = User::findOne([["id", "=", $id]]);
        if (!$user->isNewRecord && $user->load(Request::all()) && $user->validate() && $user->save()) {
            echo json_encode(1); exit;
        }
    }

    public function delete($id)
    {
        $user = User::findOne([["id", "=", $id]]);
        echo json_encode($user->delete()); exit;
    }
}
