<?php

//公众号登录
Route::get('/official/login', 'OfficialController@login');                       //公众号登录
Route::get('/official/auth', 'OfficialController@auth');                         //公众号授权
