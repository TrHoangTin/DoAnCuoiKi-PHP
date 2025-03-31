<?php
require_once __DIR__ . '/../app/core/App.php';
require_once __DIR__ . '/../app/core/Router.php';

// Khởi tạo session
require_once __DIR__ . '/../app/helpers/SessionHelper.php';
SessionHelper::startSession();

// Autoload models
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../app/models/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Routes
Router::route('/', 'HomeController@index');
Router::route('/product', 'ProductController@index');
Router::route('/product/add', 'ProductController@add');
Router::route('/product/edit/{id}', 'ProductController@edit');
Router::route('/account/login', 'AccountController@login');
Router::route('/account/register', 'AccountController@register');
Router::route('/cart', 'CartController@index');

// Khởi chạy ứng dụng
new App();