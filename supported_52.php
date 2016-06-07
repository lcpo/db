<?php
/**
* Обеспечивает совместимость для PHP 5.2.x
* 
* LICENSE: MIT
* @copyright  Copyright (c) 2011-2016 Korotaev S.S.
* @version    0.0.1
* @since      File available since Release 0.0.1
*/
include_once(dirname(__FILE__).'/PDO.php');
include_once(dirname(__FILE__).'/MYSQLI.php');
include_once(dirname(__FILE__).'/MYSQL.php');
	 if(!isset($GLOBALS['o']['driver'])){
		 
 if(extension_loaded("pdo_mysql")){
	 class DB extends _pdo{}
	 }elseif(extension_loaded("mysqli")){
	 class DB extends _mysqli{} 
		 }elseif(extension_loaded("mysql")){
	 class DB extends _mysql{}
			 }elseif(extension_loaded("pdo_sqlite")){
	 class DB extends _pdo{}
				 }
				 
							}else{
if($GLOBALS['o']['driver']=="pdo_mysql"){class DB extends _pdo{}}
if($GLOBALS['o']['driver']=="pdo_sqlite"){class DB extends _pdo{}}
if($GLOBALS['o']['driver']=="mysqli"){class DB extends _mysqli{}}
 if($GLOBALS['o']['driver']=="mysql"){class DB extends _mysql{} }

				 }

