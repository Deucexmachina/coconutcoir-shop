<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/health', static fn () => service('response')->setStatusCode(200)->setBody('ok'));

$routes->get('/', 'Home::index');

$routes->get('/login', 'AuthController::login');
$routes->get('/seller/login', 'AuthController::sellerLogin');
$routes->post('/login', 'AuthController::loginPost');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::registerPost');
$routes->get('/logout', 'AuthController::logout');

$routes->get('/storefront', 'BuyerController::storefront');
$routes->get('/products', 'BuyerController::products');
$routes->get('/products/(:num)', 'BuyerController::product/$1');
$routes->get('/cart', 'BuyerController::cart');
$routes->post('/cart/add/(:num)', 'BuyerController::addToCart/$1');
$routes->post('/cart/update/(:num)', 'BuyerController::updateCart/$1');
$routes->post('/cart/remove/(:num)', 'BuyerController::removeCart/$1');
$routes->get('/checkout', 'BuyerController::checkout');
$routes->post('/checkout', 'BuyerController::placeOrder');
$routes->get('/transactions', 'BuyerController::transactions');
$routes->post('/reviews/submit/(:num)', 'BuyerController::submitReview/$1');
$routes->get('/profile', 'BuyerController::profile');

$routes->get('/seller/storefront', 'SellerController::storefront');
$routes->post('/seller/storefront', 'SellerController::updateStorefront');
$routes->get('/seller/inventory', 'SellerController::inventory');
$routes->post('/seller/inventory/create', 'SellerController::createProduct');
$routes->post('/seller/inventory/update/(:num)', 'SellerController::updateProduct/$1');
$routes->get('/seller/reports', 'SellerController::reports');
