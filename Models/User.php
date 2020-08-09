<?php
namespace Models;
require_once "DB.php";

class User{
    public int $id;
    public string $name;
    public string $email;
    //private string $password;
    
    public function __construct(){
        unset($this->password);  
    }

    static public function all():array{
        $pdo = \DB::connect();
        $stm = $pdo->prepare("Select `id`,`name`,`email` from user");
        $stm->execute();
        $users = $stm->fetchAll(\PDO::FETCH_ASSOC);
        return $users;
    }

    static public function find($id):User{
        $pdo = \DB::connect();
        $stm = $pdo->prepare("Select * from user where id=?");
        $stm->setFetchMode(\PDO::FETCH_CLASS, 'Models\User');
        $stm->execute([$id]);
        $user = $stm->fetch();
        $stm->closeCursor();
        return $user;
    }

    static public function update(\Request $request):User{
        $pdo = \DB::connect();
        $query = "UPDATE user SET " ;
        $arr = [];
        $parameters = [];
        if($request->name){
            array_push($parameters,'`name`=? ');
            array_push($arr,$request->name);
        }
        if($request->email){
            array_push($parameters,'`email`=? ');
            array_push($arr,$request->email);
        }
        $query .= implode(',',$parameters) . 'where `id`=?';
        array_push($arr,$request->id);
        $stm = $pdo->prepare($query);
        $stm->execute($arr);
        return self::find($request->id);
    }

    static public function delete(\Request $request):int{
        $pdo = \DB::connect();
        $stm = $pdo->prepare("Delete from user where id=?");
        $stm->execute([$request->id]);
        //$stm->closeCursor();
        return $stm->rowCount();

    }

    static public function create(\Request $request):User{
        $pdo = \DB::connect();
        $stm = $pdo->prepare("INSERT INTO user (`name`,`email`,`password`) VALUES (?,?,?)");
        $stm->execute([$request->name,$request->email,$request->password]);
        $stm->closeCursor();
        return self::find($pdo->lastInsertId());
    }

}