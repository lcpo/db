<?php
/**
* Стартовый файл для в котором можно настроить подключение к базе данных
* так же файл с примерами работы
* 
* LICENSE: MIT
* @copyright  Copyright (c) 2011-2016 Korotaev S.S.
* @version    0.0.1
* @since      File available since Release 0.0.1
*/

include_once(dirname(__FILE__)."/DB.php");
/*
$o=array(
    'host'=>'localhost',
    'dbname'=>'testdb',
	'port'=>3306,
    'user' => 'root',
    'password' => '123test123',
    'charset'=>'utf8',
    //'driver'=>'mysqli'
    //'driver'=>'pdo_mysql'
    // 'driver'=>'mysql'
    );  
$db=DB::connect($o);
*/ 

/*
$out=DB::get("select * from products limit 2");
print_r($out);
echo"1<hr>";
*/

/*
$out=db::get("select * from accounts where login=?",array('test'),false,true);
print_r($out);
echo"1<hr>";
*/

/*
$out=db::get(array('s'=>'login','f'=>'accounts'));
print_r($out);
echo"2<hr>";

$out=db::get(array('s'=>'*','f'=>'accounts'));
print_r($out);
echo"3<hr>";
$out=db::get("accounts");
print_r($out);
echo"4<hr>";
$out=db::get("accounts where login!=''");
print_r($out);
echo"5<hr>";
$out=db::get(array('f'=>'accounts','w'=>' login!="" and lastServer>0 '));
print_r($out);
echo"6<hr>";
$out=db::get('accounts',array('*'=>'count(*),login','w'=>'lastServer>0'));
print_r($out);
echo"7<hr>";
$out=db::get('accounts',array('*'=>'count(*)','w'=>'lastServer>0 and login!=""'));
print_r($out);
echo"8<hr>";
$out=db::get("select * from accounts");
print_r($out);
echo"9<hr>";
$out1=db::get('describe accounts');
print_r($out1);
echo"10<hr>";
$out=db::get(array('ds'=>"accounts"));
print_r($out);
echo"11<hr>";

$out=db::get(array('sh'=>"login"));
print_r($out);
echo"12<hr>";

$out=$db->get(array('sh'=>"login"));
print_r($out);
echo"13<hr>";

$out=DB::get(array('sh'=>"login"));
print_r($out);
echo"14<hr>";
*/
