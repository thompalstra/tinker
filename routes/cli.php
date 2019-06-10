<?php
use Hub\Base\Route;

Route::get("404", "HomeController@404");

Route::get("", "HomeController@index");

Route::get("build/model", "BuildController@model");
Route::get("build/controller", "BuildController@controller");
Route::get("build/migration", "BuildController@migration");
Route::get("build/queue", "BuildController@queue");

Route::get("queue/start", "QueueController@start");
Route::get("queue/restart", "QueueController@restart");

Route::get("migrate", "MigrationController@migrate");
Route::get("migrate/rollback", "MigrationController@rollback");

Route::get("optimize/classmap", "OptimizeController@classmap");
