<?php
/**
* Обертка для драйвера MySQL
* 
* LICENSE: MIT
* @copyright  Copyright (c) 2011-2016 Korotaev S.S.
* @version    0.0.1
* @since      File available since Release 0.0.1
*/
class MYSQL{
public $connect_errno=0;
public $_res;
public function __construct($db){
if(isset($db['host']) && isset($db['user']) && isset($db['password']) && isset($db['dbname']) && isset($db['port'])){
$re = mysql_pconnect($db['host'].':'.$db['port'], $db['user'], $db['password']);
mysql_select_db($db['dbname'],$re);
if(isset($db['charset'])){
mysql_set_charset($db['charset'],$re);
}else{
//	$db['charset']='UTF-8';
//mysql_set_charset($db['charset'],$re);
}
$this->_res=$re;
	return $re;
}else{
	$this->connect_errno=1;
	return null;
	}
	
				}
	
	
public static function prepare($str=false,$params=false){
if($params!=false && count($params)>0){
 foreach($params as $key => $val) {
	  $val=str_replace(array('"','\'','`','?'), array('&quot;','&#039;','&#096;','&#063;'),$val);
	  $str=preg_replace("'\?'","'$val'",$str,1);
								 }
		return $str;						 
	  }
return str_replace('&#063;','?',$str);
										}

											
	
	}



class _mysql{
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
public static function error($e,$i=1){//error
switch($i){
case 0: break;
case 1:	echo $e->error;break;
case 2: @file_put_contents($_SERVER['DOCUMENT_ROOT'].'/MYSQL_errors.log', $e->error, FILE_APPEND); break;   
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
$q=self::query($s,$p);
	}	
 if($n){
	 $re=array();
	 if($t){
while ($a=mysql_fetch_assoc($q)){$re[]=$a;}			 
		 }else{
$nr=0;			 
while ($a=mysql_fetch_assoc($q)){
foreach($a as $k=>$i){$re[$nr][$k]=$i; $re[$nr][]=$i; }
$nr++;
}
	  }
        @$out=$re;
 }else{	 
	 if($t){
	$re=array();
	$re=mysql_fetch_assoc($q);
	@$out=$re;	 
		 }else{
	$re=array();
	$a=mysql_fetch_assoc($q);
	foreach($a as $k=>$i){$re[$k]=$i; $re[]=$i; }
	@$out=$re;
	  }

 }
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

public function __construct($o=array('connect'=>array('error_reporting'=>1),'option'=>false)){
	  if(!array_key_exists('connect',$o)){$o=array('connect'=>$o);}
	
            $this->_res = new MYSQL($o['connect']);
            if(!isset(self::$_option)){self::$_option =	$this->_res;}
if ($this->_res->connect_errno) {
						
			if(!isset($o['connect']['error_reporting'])){$o['connect']['error_reporting']=1;}

			self::error($this->_res,$o['connect']['error_reporting']);
			}

			return get_class($this);
}

//-----------------------------------------------------------------------------------------------------
public static function query($quer=false,$params=false){
    if($quer==false){
        return 0;
        exit;
    }else{
        if($params!=false){
            $quer=self::$_option->prepare($quer,$params);
        }
        return mysql_query($quer,self::$_option->_res);
    }
    
}
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
		$q=self::query($q,$p);
	return $q;
	}
	
//-----------------------------------------------------------------------------------------------------	
public static function count(){}
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
	return $q;
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

public function __call($method, $args) {
	$this->_hist=array($method,$args);
	return call_user_func_array(array($this->_res, $method), $args);
}
//-----------------------------------------------------------------------------------------------------
										

}

