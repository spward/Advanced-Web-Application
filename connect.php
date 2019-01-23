<?php
/**
 * Created by PhpStorm.
 * User: jennis
 * Date: 03/12/2018
 * Time: 6:59 PM
 */
/* CREATE A CONNECTION TO THE SERVER */
try{
    $connString = "mysql:host=localhost;dbname=s18c409spward";
    $user = "s18c409spward";
    $pass = "s18c409spward!";
    $pdo = new PDO($connString,$user,$pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
    die( $e->getMessage() );
}

