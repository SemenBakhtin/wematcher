<?php

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

Route::get('/', function () {
    return redirect()->route('videochat.random.index');
});

Route::get('/home', function () {
    return redirect()->route('videochat.random.index');
});

Route::get('lang/{locale}', function ($locale) {
    App::setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
})->name('locale');

Route::get('/auth/redirect/{provider}', 'SocialController@redirect');
Route::get('/callback/{provider}', 'SocialController@callback');

Route::get('/translate', 'TranslateController@translate');

Route::group(['as' => 'profile.', 'prefix' => 'profile', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', 'ProfileController@index')->name('index');
    Route::get('/view/{id}', 'ProfileController@view')->name('view');
    Route::get('/edit', 'ProfileController@edit')->name('edit');
    Route::post('/edit', 'ProfileController@update');
    Route::get('/getuser/{id}', 'ProfileController@get')->name('get');
});

Route::group(['as' => 'friend.', 'prefix' => 'friend', 'middleware' => ['auth', 'verified', 'profiled']], function () {
    Route::get('/', 'FriendController@index')->name('index');
    Route::get('/invites', 'FriendController@invites')->name('invites');
    Route::get('/addrequest', 'FriendController@addRequest')->name('addrequest');
    Route::get('/addaccept/{from}/{to}', 'FriendController@addAccept')->name('addaccept');
    Route::get('/addreject/{from}/{to}', 'FriendController@addReject')->name('addreject');
});

Route::group(['as' => 'membership.', 'prefix' => 'membership', 'middleware' => ['profiled']], function () {
    Route::get('/', 'MembershipController@index')->name('index');
    Route::get('/update', 'MembershipController@update')->name('update');
});

Route::group(['as' => 'videochat.', 'prefix' => 'videochat'], function () {
    Route::get('/random/mygender', 'RandomChatController@mygenderSelect')->name('mygender');
    Route::get('/random/mygenderupdate/{gender}', 'RandomChatController@mygenderUpdate')->name('mygenderupdate');

    Route::group(['as' => 'random.', 'prefix' => 'random', 'middleware' => ['gender']], function () {
        Route::get('/', 'RandomChatController@index')->name('index');
        Route::get('/yourgender', 'RandomChatController@yourgenderSelect')->name('yourgender');
        Route::get('/yourgenderupdate/{gender}', 'RandomChatController@yourgenderUpdate')->name('yourgenderupdate');
    });

    Route::group(['as' => 'dating.', 'prefix' => 'dating', 'middleware' => ['auth', 'verified', 'profiled']], function () {
        Route::get('/', 'DatingController@index')->name('index');
        Route::get('/call/{to}', 'DatingController@call')->name('call');
        Route::get('/receive/{to}', 'DatingController@received')->name('receive');
        Route::get('/end/{to}', 'DatingController@end')->name('end');
        Route::get('/accept/{to}', 'DatingController@accept')->name('accept');
        Route::get('/reject/{to}', 'DatingController@reject')->name('reject');
        Route::get('/meet/{sessionid}/{partner}', 'DatingController@meet')->name('meet');
    });
});

Route::group(['as' => 'message.', 'prefix' => 'message', 'middleware' => ['auth', 'verified', 'profiled']], function () {
    Route::get('/', 'MessageController@index')->name('index');
    Route::get('/room/{to}/{pagecnt}', 'MessageController@room')->name('room');
    Route::get('/translateconf/{lang}/{auto}', 'MessageController@translateconf')->name('translateconf');
    Route::get('/send', 'MessageController@send')->name('send');
    Route::get('/sendbyemail', 'MessageController@sendbyemail')->name('sendbyemail');
    Route::get('/sendasread', 'MessageController@sendas')->name('sendasread');
    Route::get('/read/{msgid}', 'MessageController@read')->name('read');
    Route::get('/readwithtrans', 'MessageController@readwithtrans')->name('readwithtrans');
    Route::get('/readwithnotrans', 'MessageController@readwithnotrans')->name('readwithnotrans');
});

Route::get('/privacypolicy', 'PublicController@privacypolicy');
Route::get('/termsofservice', 'PublicController@termsofservice');
Route::get('/cookiepolicy', 'PublicController@cookiepolicy');

Auth::routes(['verify' => true]);