<?php
Route::get('/', 'ForumController@index');
Route::get('/create', 'ForumController@create');
Route::get('/create', 'ForumController@create');
Route::post('/store', 'ForumController@store');
Route::post('/show', 'ForumController@show');
Route::put('/edit', 'ForumController@edit');
Route::put('/update', 'ForumController@update');
Route::delete('/destroy', 'ForumController@destroy');

