<?php
Route::get('/', 'ForumController@index');
Route::get('/create', 'ForumController@create');
Route::post('/store/', 'ForumController@store');
Route::post('/show', 'ForumController@show');

