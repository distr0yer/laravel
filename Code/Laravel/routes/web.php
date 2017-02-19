<?php
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('git', 'Controller@getGit');

Route::get('api', ['middleware' => 'api']);

Route::get('callback', 'Controller@getCallback');

Route::get('math', 'Controller@getMath');

Route::get('repos', ['as' => 'repos', 'uses'=>'Controller@getRepos']);

Route::get('loops', 'Controller@getLoops');

Route::get('/', 'Controller@getIndex');
