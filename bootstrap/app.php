<?php

/*
$before = microtime(true);
            
            for ($i=0; $i < 10000; $i++) {
            //nieco na meranie 
                }   
$after = microtime(true);
*/

use Slim\Http\UploadedFile;
use \Slim\Middleware\SessionCookie;
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Controllers/LoginController.php';
require __DIR__ . '/../app/Controllers/dbController.php';
require __DIR__ . '/../app/Controllers/uploadController.php';
require __DIR__ . '/../app/Models/LoginModel.php';
require __DIR__ . '/../app/Models/otherModel.php';
require __DIR__ . '/../app/Controllers/adminController.php';
require __DIR__ . '/../resources/Classes/PHPExcel/IOFactory.php';

define("EMAIL_HEADERS",
    "MIME-Version: 1.0" . "\r\n"
                 ."Content-Type: text/html; charset=ISO-8859-1\r\n"
);

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = 'sql19.dnsserver.eu';
$config['db']['user']   = 'db82424xlsxmgr';
$config['db']['pass']   = 'xlsxmgr53!M';
$config['db']['dbname'] = 'db82424xlsxmgr';

$app = new \Slim\App(['settings' => $config]);


$container = $app->getContainer();

// Register component on container
$container['view'] = function ($container) {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../resources/');
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
$container['DbController'] = function ($container) {
    return new DbController($container['db']);
};

$container['resources'] = __DIR__ . '/../resources';
$container['session'] = function ($container) {
  return new \SlimSession\Helper;
};
$container['LoginController'] = function ($container) {
    return new LoginController($container['session'],$container['resources']."/secXML/users.xml");
};
$container['OtherModel'] = function ($container) {
    return new OtherModel();
};
$container['UploadController'] = function ($container) {
    return new UploadController($container['OtherModel'], $container['DbController']);
};

$container['AdminController'] = function ($container) {
	return new AdminController($container['OtherModel'],$container['DbController']);
};
$container['LoginModel'] = function ($container) {
    return new LoginModel($container['session']);
};

require __DIR__ . '/../app/routes.php';

