<?php

// $yii=dirname(__FILE__).'/../../framework/yii.php';
// $config=dirname(__FILE__).'/protected/config/main.php';

$dir = '/var/www'.dirname($_SERVER['PHP_SELF']);

/*
echo '$_SERVER[PHP_SELF]: ' . $_SERVER['PHP_SELF'] . '<br />';
echo 'Dirname($_SERVER[PHP_SELF]: /var/www' . dirname($_SERVER['PHP_SELF']) . '<br>';
echo dirname(__FILE__).'<br>';
$link = dirname(__FILE__);
if (is_link(__FILE__)) {
    echo(readlink(__FILE__));
}
echo print_r(pathinfo(dirname(__FILE__)));
exit;
*/

$yii=$dir.'/../../framework/yii.php';
$config=$dir.'/protected/config/main.php';

require_once($yii);
Yii::createWebApplication($config)->run();
