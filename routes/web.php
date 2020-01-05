<?php

Route::namespace('Home')->name('home.')->group(function () {
    Route::get('/', 'IndexController@index')->name('index');
    Route::get('category/{category}/{slug?}', 'IndexController@category')->name('category');
    Route::get('tag/{tag}/{slug?}', 'IndexController@tag')->name('tag');
    Route::get('note', 'IndexController@note')->name('note');
    Route::get('git', 'IndexController@git')->name('git');
    Route::get('article/{article}/{slug?}', 'IndexController@article')->name('article');
    Route::get('checkLogin', 'IndexController@checkLogin')->name('checkLogin');
    Route::get('search', 'IndexController@search')->name('search');
    Route::get('feed', 'IndexController@feed')->name('feed');
    Route::prefix('site')->name('site.')->group(function () {
        Route::get('/', 'SiteController@index')->name('index');
        Route::post('store', 'SiteController@store')->middleware('auth.socialite', 'clean.xss')->name('store');
    });
    Route::middleware('auth.socialite')->group(function () {
        Route::post('comment', 'IndexController@comment')->name('comment.store');
        Route::prefix('like')->name('like.')->group(function () {
            Route::post('store', 'LikeController@store')->name('store');
            Route::delete('destroy', 'LikeController@destroy')->name('destroy');
        });
    });
});

// auth
Route::namespace('Auth')->prefix('auth')->as('auth.')->group(function () {
    // Socialite
    Route::prefix('socialite')->as('socialite.')->group(function () {
        Route::get('redirectToProvider/{service}', 'SocialiteController@redirectToProvider')->name('redirectToProvider');
        Route::get('handleProviderCallback/{service}', 'SocialiteController@handleProviderCallback')->name('handleProviderCallback');
        Route::get('logout', 'SocialiteController@logout')->name('logout');
    });

    Route::prefix('admin')->group(function () {
        Route::post('login', 'AdminController@login');
    });
});

Route::namespace('Admin')->prefix('admin')->group(function () {
    Route::redirect('/', url('admin/login/'));
    Route::prefix('login')->group(function () {
        Route::get('/', 'LoginController@index')->middleware('admin.login');
    });
    Route::get('logout', 'LoginController@logout');
});

// Admin
Route::namespace('Admin')->prefix('admin')->middleware('admin.auth')->group(function () {
        Route::get('/', 'IndexController@index');
        Route::get('upgrade', 'IndexController@upgrade');
        Route::get('loginUserForTest', 'IndexController@loginUserForTest');

    Route::prefix('article')->group(function () {
        Route::get('index', 'ArticleController@index');
        Route::get('create', 'ArticleController@create');
        Route::post('store', 'ArticleController@store');
        Route::get('edit/{id}', 'ArticleController@edit');
        Route::post('update/{id}', 'ArticleController@update');
        Route::post('uploadImage', 'ArticleController@uploadImage');
        Route::get('destroy/{id}', 'ArticleController@destroy');
        Route::get('restore/{id}', 'ArticleController@restore');
        Route::get('forceDelete/{id}', 'ArticleController@forceDelete');
        Route::get('replaceView', 'ArticleController@replaceView');
        Route::post('replace', 'ArticleController@replace');
    });

    Route::prefix('category')->group(function () {
        Route::get('index', 'CategoryController@index');
        Route::get('create', 'CategoryController@create');
        Route::post('store', 'CategoryController@store');
        Route::get('edit/{id}', 'CategoryController@edit');
        Route::post('update/{id}', 'CategoryController@update');
        Route::post('sort', 'CategoryController@sort');
        Route::get('destroy/{id}', 'CategoryController@destroy');
        Route::get('restore/{id}', 'CategoryController@restore');
        Route::get('forceDelete/{id}', 'CategoryController@forceDelete');
    });

    Route::prefix('tag')->group(function () {
        Route::get('index', 'TagController@index');
        Route::get('create', 'TagController@create');
        Route::post('store', 'TagController@store');
        Route::get('edit/{id}', 'TagController@edit');
        Route::post('update/{id}', 'TagController@update');
        Route::get('destroy/{id}', 'TagController@destroy');
        Route::get('restore/{id}', 'TagController@restore');
        Route::get('forceDelete/{id}', 'TagController@forceDelete');
    });

    Route::prefix('comment')->group(function () {
        Route::get('index', 'CommentController@index');
        Route::get('edit/{id}', 'CommentController@edit');
        Route::post('update/{id}', 'CommentController@update');
        Route::get('destroy/{id}', 'CommentController@destroy');
        Route::get('restore/{id}', 'CommentController@restore');
        Route::get('forceDelete/{id}', 'CommentController@forceDelete');
        Route::get('replaceView', 'CommentController@replaceView');
        Route::post('replace', 'CommentController@replace');
    });

    Route::prefix('user')->group(function () {
        Route::get('index', 'UserController@index');
        Route::get('edit/{id}', 'UserController@edit');
        Route::post('update/{id}', 'UserController@update');
        Route::get('destroy/{id}', 'UserController@destroy');
        Route::get('restore/{id}', 'UserController@restore');
        Route::get('forceDelete/{id}', 'UserController@forceDelete');
    });

    // Socialite client
    Route::prefix('socialiteClient')->group(function () {
        Route::get('index', 'SocialiteClientController@index');
        Route::get('edit/{id}', 'SocialiteClientController@edit');
        Route::post('update/{id}', 'SocialiteClientController@update');
    });

    Route::prefix('socialiteUser')->group(function () {
        Route::get('index', 'SocialiteUserController@index');
        Route::get('edit/{id}', 'SocialiteUserController@edit');
        Route::post('update/{id}', 'SocialiteUserController@update');
    });

    Route::prefix('friendshipLink')->group(function () {
        Route::get('index', 'FriendshipLinkController@index');
        Route::get('create', 'FriendshipLinkController@create');
        Route::post('store', 'FriendshipLinkController@store');
        Route::get('edit/{id}', 'FriendshipLinkController@edit');
        Route::post('update/{id}', 'FriendshipLinkController@update');
        Route::post('sort', 'FriendshipLinkController@sort');
        Route::get('destroy/{id}', 'FriendshipLinkController@destroy');
        Route::get('restore/{id}', 'FriendshipLinkController@restore');
        Route::get('forceDelete/{id}', 'FriendshipLinkController@forceDelete');
    });

    Route::prefix('site')->group(function () {
        Route::get('index', 'SiteController@index');
        Route::get('create', 'SiteController@create');
        Route::post('store', 'SiteController@store');
        Route::get('edit/{id}', 'SiteController@edit');
        Route::post('update/{id}', 'SiteController@update');
        Route::post('sort', 'SiteController@sort');
        Route::get('destroy/{id}', 'SiteController@destroy');
        Route::get('restore/{id}', 'SiteController@restore');
        Route::get('forceDelete/{id}', 'SiteController@forceDelete');
    });

    Route::prefix('note')->group(function () {
        Route::get('index', 'NoteController@index');
        Route::get('create', 'NoteController@create');
        Route::post('store', 'NoteController@store');
        Route::get('edit/{id}', 'NoteController@edit');
        Route::post('update/{id}', 'NoteController@update');
        Route::get('destroy/{id}', 'NoteController@destroy');
        Route::get('restore/{id}', 'NoteController@restore');
        Route::get('forceDelete/{id}', 'NoteController@forceDelete');
    });

    Route::prefix('config')->group(function () {
        Route::get('edit', 'ConfigController@edit');
        Route::get('email', 'ConfigController@email');
        Route::get('socialite', 'ConfigController@socialite');
        Route::get('commentAudit', 'ConfigController@commentAudit');
        Route::get('qqQun', 'ConfigController@qqQun');
        Route::get('backup', 'ConfigController@backup');
        Route::get('seo', 'ConfigController@seo');
        Route::get('socialShare', 'ConfigController@socialShare');
        Route::get('socialLinks', 'ConfigController@socialLinks');
        Route::get('search', 'ConfigController@search');
        Route::post('update', 'ConfigController@update');
        Route::get('clear', 'ConfigController@clear');
    });

    Route::prefix('gitProject')->group(function () {
        Route::get('index', 'GitProjectController@index');
        Route::get('create', 'GitProjectController@create');
        Route::post('store', 'GitProjectController@store');
        Route::get('edit/{id}', 'GitProjectController@edit');
        Route::post('update/{id}', 'GitProjectController@update');
        Route::post('sort', 'GitProjectController@sort');
        Route::get('destroy/{id}', 'GitProjectController@destroy');
        Route::get('restore/{id}', 'GitProjectController@restore');
        Route::get('forceDelete/{id}', 'GitProjectController@forceDelete');
    });

    Route::prefix('nav')->group(function () {
        Route::get('index', 'NavController@index');
        Route::get('create', 'NavController@create');
        Route::post('store', 'NavController@store');
        Route::get('edit/{id}', 'NavController@edit');
        Route::post('update/{id}', 'NavController@update');
        Route::post('sort', 'NavController@sort');
        Route::get('destroy/{id}', 'NavController@destroy');
        Route::get('restore/{id}', 'NavController@restore');
        Route::get('forceDelete/{id}', 'NavController@forceDelete');
    });
});
