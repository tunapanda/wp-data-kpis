<?php
/*
Plugin Name: Data KPIs
Plugin URI: http://github.com/tunapanda/wp-data-kpis
GitHub Plugin URI: http://github.com/tunapanda/wp-data-kpis
Description: Measure how your WordPress site is doing.
Version: 0.0.1
*/

require_once __DIR__."/src/utils/AutoLoader.php";

define('DATAKPI_PATH',plugin_dir_path(__FILE__));
define('DATAKPI_URL',plugins_url('',__FILE__));

$autoLoader=new datakpi\AutoLoader("datakpi");
$autoLoader->addSourceTree(DATAKPI_PATH."/src");
$autoLoader->register();

$autoLoader=new datakpi\AutoLoader();
$autoLoader->addSourcePath(DATAKPI_PATH."/ext/wprecord");
$autoLoader->register();

datakpi\DefaultPluginKpis::instance();
datakpi\DataKpiPlugin::instance();
