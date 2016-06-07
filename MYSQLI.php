<?php
/**
* Обертка для драйвера MySQLI
* 
* LICENSE: MIT
* @copyright  Copyright (c) 2011-2016 Korotaev S.S.
* @version    0.0.1
* @since      File available since Release 0.0.1
*/
class _mysqli{
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
case 1:	echo $e->error;break;
case 2: @file_put_contents($_SERVER['DOCUMENT_ROOT'].'/MYSQLI_errors.log', $e->error, FILE_APPEND); break;   
						}
	}

//-----------------------------------------------------------------------------------------------------
public static function get($s,$p=array(),$n=true,$t=true){//get
$query=''; $result=false;
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
$q=self::query($s,$p);
	}	
$meta = $q->result_metadata(); 
    while ($field = $meta->fetch_field()) 
    { 
        $params[] = &$row[$field->name]; 
    } 

    call_user_func_array(array($q, 'bind_result'), $params); 
$i=array();
$c=array();
$nr=0;
    while ($q->fetch()) { 
        foreach($row as $key => $val) 
        { 
            $c[$nr][$key] = $val;
            $i[$nr][]=$val; 
        } 
         if(!$t){$result[$nr] = array_merge($c[$nr],$i[$nr]);}else{$result[$nr] =$c[$nr];} 
    $nr++;
    }

    
if(!$n){
	if(isset($result[0])){$result=$result[0];}
	}
return $result;
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
	//$req = self::$_option->prepare($q);
	//$req->execute($p);
	//return self::$_option->lastInsertId();
		$q=self::query($q,$p);
	return self::$_option->insert_id;
	}
	
//-----------------------------------------------------------------------------------------------------	
public static function count(){}
//-----------------------------------------------------------------------------------------------------
/*
public static function option($option=array('connect'=>array('error_reporting'=>1))){//option instruction
try { 
if(isset($option)){
if(isset($option['error'])){	
switch($option['error']){
case 0: $option['attr'][]=array(PDO::ATTR_ERRMODE,PDO::ERRMODE_SILENT); break;
case 1: $option['attr'][]=array(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING); break;
case 2: $option['attr'][]=array(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); break;	
						}
							}							
foreach($option['attr'] as $k=>$v){self::$_PDO->setAttribute($k, $v);}
				  } 
	}catch(PDOException $e) {
	self::error($e,$o['connect']['error_reporting']);	
		}
	} 
*/		   

//-----------------------------------------------------------------------------------------------------
public static function query($s,$p=array()){//query

if(!is_array($p)){$p = array($p);}

$q = self::$_option->prepare($s);
if (!$q){throw new Exception(self::$_option->error."\n\n".$s);}
if(count($p)>0){
$query='';
foreach($p as $v){
	if(is_int($v)){$query.='i';}
	if(is_double($v)){$query.='d';}
	if(is_string($v)){$query.='s';}

	}
if($query!=''){
$params[] =$query;
foreach($p as $v){
if(is_int($v)){$params[]=&$v;}
if(is_double($v)){$params[]=&$v;}
if(is_string($v)){$params[]=&$v;}
call_user_func_array(array($q,'bind_param'),$params);
				}
				}}
$q->execute();
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
	$p =array_merge($p,$p);
	$q=self::query($q,$p);
	return self::$_option->insert_id;
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
  //  $req = self::$_option->prepare($q);
	//$req->execute($p);
	$q=self::query($q,$p);
	return $q;

}
//-----------------------------------------------------------------------------------------------------
public function __construct($o=array('connect'=>array('error_reporting'=>1),'option'=>false)){
	  if(!array_key_exists('connect',$o)){$o=array('connect'=>$o);}
	
            $this->_res = new mysqli($o['connect']['host'], $o['connect']['user'], $o['connect']['password'],$o['connect']['dbname'],$o['connect']['port']);
            if(!isset(self::$_option)){self::$_option =	$this->_res;}
if ($this->_res->connect_errno) {
						
			if(!isset($o['connect']['error_reporting'])){$o['connect']['error_reporting']=1;}

			self::error($this->_res,$o['connect']['error_reporting']);
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
