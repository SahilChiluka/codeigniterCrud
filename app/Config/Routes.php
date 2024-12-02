<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::index');

$routes->get('/home', 'Home::index');
$routes->post('/saveUser', 'Home::saveUser');
$routes->get('/getSingleUser/(:num)', 'Home::getSingleUser/$1');
$routes->post('/updateUser','Home::updateUser');
$routes->post('/deleteUser','Home::deleteUser');
$routes->post('/deleteMultiUser', 'Home::deleteMultiUser');
$routes->get('/download', 'Home::download');
// $routes->post('/upload', 'Home::upload');
// $routes->post('/home','Home::filterUser');

$routes->get('/register','Register::index');
$routes->post('/toLogin','Register::toLogin');

$routes->get('/logout','Home::logout');
$routes->post('/toHome','Login::toHome');


$routes->post('/upload', 'Home::upload');
