<?php
	$_GET['key']  = (isset($_GET['key']) ? $_GET['key'].'/' : 'index/index');
	$key = $_GET['key'];
	$separator = explode('/',$key);
	$controller = $separator[0];
	$action = ($separator[1] == null ? 'indexAction' : $separator[1].'Action');

	require 'lib/core/Controller.php';
	require 'app/controller/'.$controller.'Controller.php';
	$app = new $controller;
	$app->$action();

