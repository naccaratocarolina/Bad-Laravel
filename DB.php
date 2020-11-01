<?php

class DB{

    private static $pdo;
    private function __construct()
    {
        try{
            self::$pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=PSI", "root", "");
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    static public function connect(){
        if(self::$pdo === null){
            new self;
        }
        return self::$pdo;
    }
}
