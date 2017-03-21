<?php 
	session_start();
	include './class/config.php';
	//自动加载函数,用于自动加载类文件
	function __autoload($className)
	{ 
		include './class/'.$className.'.class.php';
	}
	
	$action = $_GET['a'] ? $_GET['a'] :'login';
	$comm = new Index();
	$comm->$action();