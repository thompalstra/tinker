<?php
use Hub\Base\Route;

use App\Models\User;

Route::group(["host" => "admin.tinker.com"], function () {
    Route::get("", "Admin/HomeController@index", ["name" => "home"]);
    Route::matches(["get", "post"], "login", "Admin/homeController@login", ["name" => "login"]);
    // Route::post("login", "Admin/HomeController@login", ["nam"])
});

Route::group(["host" => "tinker.com"], function () {
    Route::get("", "HomeController@index");
    Route::get("info/services/{service}", "Info/ServicesController@service");
    Route::get("queue/create/{amount}", "QueueController@create");
});

User::routes();
