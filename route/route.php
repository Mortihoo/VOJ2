<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::miss('index/Index/miss');
Route::get('index', 'index/Index/index');
Route::get('/', 'index/Index/index');


Route::group('user', [
    'login' => 'index/Index/login',
    'register' => 'index/Index/register',
    'logout' => 'index/Index/logout',
])->append(['group_id' => 1]);

Route::group('contest', [
    'problem_list' => 'index/Contest/problem_list',
    'submit' => 'index/Contest/submit',
    'status' => 'index/Contest/status',
    'viewcode/:id' => 'index/Contest/view_code',
])->append(['group_id' => 2])->pattern(['id' => '\d+']);

Route::get('test', 'index/Index/test');

return [

];
