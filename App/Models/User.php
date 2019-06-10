<?php
namespace App\Models;

use Hub\Base\Model;

use Hub\Base\Route;

class User extends Model
{
    protected $timestamps;

    public static $columns = [
        'id' => 'INT(11) PRIMARY KEY AUTO_INCREMENT',
        "username" => "VARCHAR(255) NOT NULL",
        "password" => "VARCHAR(255) DEFAULT NULL",
        'created_at' => 'INT(11) NOT NULL',
        'updated_at' => 'INT(11) NOT NULL'
    ];

    public static function routes()
    {
        Route::group(["host" => "admin.tinker.com", "route" => "users"], function () {
            Route::get("", "Admin/UserController@create", ["name" => "create"]);
            Route::get("{id}", "Admin/UserController@view", ["name" => "view"]);
            Route::post("{id}", "Admin/UserController@update", ["name" => "update"]);
            Route::get("{id}", "Admin/UserController@delete", ["name" => "delete"]);
        });

        Route::group(["route" => "rest", "name" => "rest."], function () {
            Route::group(["route" => "v1", "name" => "v1."], function () {
                Route::group(["route" => "users", "name" => "users."], function () {
                    Route::get("{id}", "Rest/v1/UsersController@view", ["name" => "view"]);
                    Route::put("{id}", "Rest/v1/UsersController@update", ["name" => "update"]);
                    Route::post("", "Rest/v1/UsersController@create", ["name" => "create"]);
                    Route::delete("{id}", "Rest/v1/UsersController@delete", ["name" => "delete"]);
                });
            });
        });
    }

    public function login($username, $password)
    {
        $user = self::findOne([
            ["username", "=", $username]
        ]);
        if ($user && $user->verifyPassword($password)) {
            return Frame::$app->login($user);
        }
        return false;
    }

    public function logout()
    {

    }
}
