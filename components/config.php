<?php
    $server="localhost";
    $host="root";
    $clave="";
    $bd="foros";
try {
    $connect=mysqli_connect($server,$host,$clave,$bd);
} catch (mysqli_sql_exception $e) {
    die('Connected failed: '.$e->getMessage());
}
    

?>