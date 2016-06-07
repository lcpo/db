<?php
/**
* Обеспечивает совместимость для PHP 5.3.x
* 
* LICENSE: MIT
* @copyright  Copyright (c) 2011-2016 Korotaev S.S.
* @version    0.0.1
* @since      File available since Release 0.0.1
*/

include_once(dirname(__FILE__).'/PDO.php');
include_once(dirname(__FILE__).'/MYSQLI.php');
include_once(dirname(__FILE__).'/MYSQL.php');

class DB{
	public $__res;
	public $__hist;
	public static $_res;
	public static $_hist;
	public static $_option;
	public static $_Instance;


 public static function _construct($o=array('connect'=>array('error_reporting'=>1),'option'=>false,'driver'=>false)){
	 if(!isset($o['driver'])){
		 
 if(extension_loaded("pdo_mysql")){
	 return self::$_res=new _pdo($o);
	 }elseif(extension_loaded("mysqli")){
		return self::$_res=new _mysqli($o); 
		 }elseif(extension_loaded("mysql")){
			 return self::$_res=new _mysql($o); 
			 }elseif(extension_loaded("pdo_sqlite")){
				  return self::$_res=new _pdo($o);
				 }
				 
							}else{
 if($o['driver']=="pdo_mysql"){return self::$_res=new _pdo($o);}
 if($o['driver']=="pdo_sqlite"){return self::$_res=new _pdo($o);}
 if($o['driver']=="mysqli"){return self::$_res=new _mysqli($o);}
 if($o['driver']=="mysql"){return self::$_res=new _mysql($o);}

				 }
								
	 }
//-----------------------------------------------------------------------------------------------------

 public function __construct($o=array('connect'=>array('error_reporting'=>1),'option'=>false,'driver'=>false)){
	 if(!isset($o['driver'])){
		 
 if(extension_loaded("pdo_mysql")){
	 return $this->__res=new _pdo($o);
	 }elseif(extension_loaded("mysqli")){
		return $this->__res=new _mysqli($o); 
		 }elseif(extension_loaded("mysql")){
			 return $this->__res=new _mysql($o); 
			 }elseif(extension_loaded("pdo_sqlite")){
				  return $this->__res=new _pdo($o);
				 }
				 
							}else{
 if($o['driver']=="pdo_mysql"){return $this->__res=new _pdo($o);}
 if($o['driver']=="pdo_sqlite"){return $this->__res=new _pdo($o);}
 if($o['driver']=="mysqli"){return $this->__res=new _mysqli($o);}
 if($o['driver']=="mysql"){return $this->__res=new _mysql($o);}

				 }
							 
	 
	}
	

//-----------------------------------------------------------------------------------------------------
public function __call($method, $args) {
if(!isset($this->__res)){$this->__res=new self($args);}
	$this->__hist=array($method,$args);
	return call_user_func_array(array($this->__res, $method), $args);
}

public static function __callStatic($method, $args) {
if(!isset(self::$_res)){self::$_res=call_user_func_array('self::_construct', $args);}
self::$_hist=array($method,$args);
	return call_user_func_array(array(self::$_res, $method), $args);	
	}
//-----------------------------------------------------------------------------------------------------	

	}
