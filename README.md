# db
library includes classes for working with MySQL,MySQLI,PDO

##Using
###Сonnection
```php
include_once(dirname(__FILE__)."/path_to_db/db/DB.php");
$o=array(
    'host'=>'Your host database',
    'dbname'=>'Your database',
	'port'=>3306, //Your port database
    'user' => 'Your user database',
    'password' => 'Your password database',
    'charset'=>'utf8', //Your charset database
    'driver'=>'pdo_mysql' //Use driver (Default pdo_mysql)
    );  
$db=DB::connect($o);
```
###Retrieving data
