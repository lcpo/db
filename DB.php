<?php
/**
* Файл загружаемый в проект
* 
* LICENSE: MIT
* @copyright  Copyright (c) 2011-2016 Korotaev S.S.
* @version    0.0.1
* @since      File available since Release 0.0.1
*/

$php_ver=str_replace('.','',phpversion());
if(strstr($php_ver,"-")===true) 
	{
		$php_ver=explode("-",$php_ver);
		$php_ver=$php_ver[0];
	}
if(strlen($php_ver)>3) 
	{
		$php_ver=substr($php_ver, 0, 3);
	}
		if($php_ver<530){
			include_once(dirname(__FILE__).'/supported_52.php');
		}else{
			include_once(dirname(__FILE__).'/supported_53.php');
			}
 
