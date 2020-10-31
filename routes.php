<?php
require_once("Router/Route.php");
use Router\Route;

// User Controller
Route::get("/user","Controllers\UserController@index");
Route::get("/showuser","Controllers\UserController@show");
Route::post("/upuser","Controllers\UserController@update");
Route::post("/cuser","Controllers\UserController@create");
Route::post("/duser","Controllers\UserController@delete");

Route::post("/login","Controllers\UserController@login");
Route::post("/register","Controllers\UserController@register");

// Book Controller
Route::get("/book","Controllers\BookController@index");
Route::get("/showbook","Controllers\BookController@show");
Route::post("/createbook","Controllers\BookController@create");
Route::post("/updatebook","Controllers\BookController@update");
Route::post("/deletebook","Controllers\BookController@delete");