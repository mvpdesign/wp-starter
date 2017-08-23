<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Bail if accessed directly

global $modules;

require_once('utils.php');
require_once('acf-module.php');
require_once('acf-module-handler.php');

$modules = new AcfModulesHandler;
