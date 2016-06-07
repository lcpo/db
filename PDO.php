<?php
/**
* Обертка для драйвера PDO
* 
* LICENSE: MIT
* @copyright  Copyright (c) 2011-2016 Korotaev S.S.
* @version    0.0.1
* @since      File available since Release 0.0.1
*/
class _pdo{
	public static $_option;
	public $_res;
	public $_hist;
	public static $_p;

//-----------------------------------------------------------------------------------------------------	
public static function backup(){}//backup db
//-----------------------------------------------------------------------------------------------------	
public static function connect(array $o = NULL){//connect
if(self::$_p === NULL){if($o === NULL) {
	throw new InvalidArgumentException('You need to specify connection parameters when you first start!');
	}
	self::$_p = new self($o);}
	return self::$_p;
}

//-----------------------------------------------------------------------------------------------------
public static function delete($s,$p=array(),$n=true){//delete
return self::query("delete from ".$s,$p);
	}
//-----------------------------------------------------------------------------------------------------	
public static function error($e,$i=1){//error
switch($i){
case 0: break;
case 1:	echo $e->getMessage();break;
case 2: @file_put_contents($_SERVER['DOCUMENT_ROOT'].'/PDO_errors.log', $e->getMessage(), FILE_APPEND); break;   
						}
	}

//-----------------------------------------------------------------------------------------------------
public static function get($s,$p=array(),$n=true,$t=true){//get
$query=''; $out=false;
$type_s=gettype($s);
if($type_s=="boolean" or $type_s=="integer" or $type_s=="double" or $type_s=="resource" or $type_s=="NULL" or $type_s=="unknown type"){return false;}
if(!is_array($p)){$p = array($p);}

if(is_array($s)){
	if(isset($s['s'])){$query='select '.$s['s'];}else{$query='select *';}
	if(isset($s['sh'])){$query='show tables ';}
	if(isset($s['ds'])){$query='describe '.$s['ds'];}
	if(isset($s['f'])){$query.=' from '.$s['f'];}
	if(isset($s['w'])){$query.=' where '.$s['w'];}
	$s=$query;
	}

if(is_string($s)){
		if(stripos($s,'select')===false 
		&& stripos($s,'from')===false 
		&& stripos($s,'describe')===false 
		&& stripos($s,'show')===false){
			
			$query='select ';
			if(isset($p['*'])){
				if(is_array($p['*'])){$query.=implode(', ',$p['*']);}
				if(is_string($p['*'])){$query.=$p['*'];}
				}else{$query.='*';}
				
				$query.=' from '.$s.' ';
				if(isset($p['w'])){$query.=' where '.$p['w'];}
				$s=$query;
		}
	}
	
if(is_object($s)){
	$q=$s;
}else{
$q=array();	
$q = self::$_option->prepare($s);
$q->execute($p); 
	}	

if($n){$out=$q->fetchAll(($t)?PDO::FETCH_ASSOC:false);}else{$out=$q->fetch(($t)?PDO::FETCH_ASSOC:false);}
return $out;
													   }
//-----------------------------------------------------------------------------------------------------
public static function fetchAll($str,$params=array(),$all_items=true,$assoc_array=true){
	return self::get($str,$params,$all_items,$assoc_array);
	}
//-----------------------------------------------------------------------------------------------------
public static function fetchRow($str,$params=array()){
return self::get($str,$params,false,true);
}
//-----------------------------------------------------------------------------------------------------	
public static function getFieldNames($table){$res=self::get(array('ds'=>$table));$out = array();foreach($res as $r){$out[] = $r['Field'];}return $out;}	
//-----------------------------------------------------------------------------------------------------
public static function handler(){}//TODO:HANDLER
//-----------------------------------------------------------------------------------------------------
public static function insert($to,$params = array(),$prefix = false){//insert
$fields=self::get(array('ds'=>$to));
$field=array();
foreach($fields as $i){$field[]=$i['Field'];}

	foreach($params as $k=>$r){
	if((!$prefix || $prefix == substr($k,0,strlen($prefix))) && in_array($k,$field)){$q[] = '`'.$k."` = ?";  $p[]=$r;}
							  }
	$q = implode(', ',$q);
	$q = "INSERT INTO `".$to.'` SET '.$q;
	$req = self::$_option->prepare($q);
	$req->execute($p);
	return self::$_option->lastInsertId();
	}
	
//-----------------------------------------------------------------------------------------------------	
public static function count(){}
//-----------------------------------------------------------------------------------------------------
public static function query($s,$p=array()){//query
try {
if(!is_array($p)){$p = array($p);}
$q = self::$_option->prepare($s);
@$q->execute($p);
} catch (PDOException $e) {self::error($e,1);}
return $q;
	}
//-----------------------------------------------------------------------------------------------------	
public static function replace($to,$params = array(),$prefix = false){//replace
$fields=self::get(array('ds'=>$to));
$field=array();
foreach($fields as $i){$field[]=$i['Field'];}

	$q=array();
	foreach($params as $k=>$r){
	if(!$prefix || $prefix == substr($k,0,strlen($prefix)) && in_array($k,$field)){
	$q[] = '`'.$k."` = ?";$p[]=$r;	
	}
	}
	$q = implode(', ',$q);
	$q = "INSERT INTO `".$to.'` SET '.$q.' ON DUPLICATE KEY UPDATE '.$q;
	$req = self::$_option->prepare($q);
	$p =array_merge($p,$p);
	$req->execute($p);
	return self::$_option->lastInsertId();
	}
//-----------------------------------------------------------------------------------------------------	
public static function set(){//set
	}
//-----------------------------------------------------------------------------------------------------	
public static function update($to,$params = array(),$where=array(),$prefix = false){//update
$fields=self::get(array('ds'=>$to));
$field=array(); $par=array(); $p=array();
foreach($fields as $i){$field[]=$i['Field'];}
	foreach($params as $k=>$r){
	if(!$prefix || $prefix == substr($k,0,strlen($prefix))  && in_array($k,$field)){$q[] = '`'.$k."` = ?"; $p[]=$r;	}
								}
	foreach($where as $k=>$r){
	if(!$prefix || $prefix == substr($k,0,strlen($prefix))  && in_array($k,$field)){$par[] = '`'.$k."` = ?"; $p[]=$r;}
								}
	$q = "UPDATE `".$to.'` SET '.implode(', ',$q).' WHERE '.implode(' and ',$par);
    $req = self::$_option->prepare($q);
	return @$req->execute($p);

}
//-----------------------------------------------------------------------------------------------------
public function __construct($o=array('connect'=>array('error_reporting'=>1),'option'=>false)){
	  if(!array_key_exists('connect',$o)){$o=array('connect'=>$o);}

        if(!isset($o['connect']['dsn'])){
			if(!isset($o['connect']['port'])){$o['connect']['port']=3306;}
			if(isset($o['connect']['host']) && isset($o['connect']['port']) && isset($o['connect']['dbname'])){
				$o['connect']['dsn']='mysql:dbname='.$o['connect']['dbname'].';port='.$o['connect']['port'].';host='.$o['connect']['host'];
				}

			}
        try {
			if(isset($o['connect']['dsn'])){
			if(!isset($o['connect']['option'])){$o['connect']['option']=array(PDO::MYSQL_ATTR_INIT_COMMAND =>  "SET NAMES utf8",PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING);}	
            $this->_res = new PDO($o['connect']['dsn'], $o['connect']['user'], $o['connect']['password'],$o['connect']['option']);
            if(!isset(self::$_option)){self::$_option =	$this->_res;}
            }else{
			$this->_res = new PDO('sqlite:'.((isset($o['connect']['root']))?$o['connect']['root']:$_SERVER['DOCUMENT_ROOT']).'/'.((isset($o['connect']['dbname']))?$o['connect']['dbname']:'new.db'));	
			if(!isset(self::$_option)){self::$_option =	$this->_res;}
									}
									
						
									
        } catch (PDOException $e) {
						
			if(!isset($o['connect']['error_reporting'])){$o['connect']['error_reporting']=1;}

			self::error($e,$o['connect']['error_reporting']);
			}

			return get_class($this);
}
//-----------------------------------------------------------------------------------------------------

public function __call($method, $args) {
	$this->_hist=array($method,$args);
	return call_user_func_array(array($this->_res, $method), $args);
}
		
		}
?>
