<?php
namespace Models;
require_once "Request.php";
use Request;

require_once "DB.php";

class User{
    public int $id;
    public string $name;
    public string $email;
    private string $secretKey = "NdRgUkXp2s5v8x/A?D(G+KbPeShVmYq3";
    private string $password;
    public string $hash;

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

    static public function find(\Request $request):User {
        $pdo = \DB::connect();

        $filter = "";
        $str = "";

        if ($request->id) {
            $filter .= "id";
            $str .= $request->id;
        }

        else if ($request->name) {
            $filter .= "name";
            $str .= $request->name;
        }

        else if ($request->email) {
            $filter .= "email";
            $str .= $request->email;
        }

        $query = "SELECT * FROM user WHERE " . $filter . "=?";

        $stm = $pdo->prepare($query);
        $stm->setFetchMode(\PDO::FETCH_CLASS, 'Models\User');
        $stm->execute([$str]);
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
        return self::find($request);
    }

    static public function delete(\Request $request):int{
        $pdo = \DB::connect();
        $stm = $pdo->prepare("Delete from user where id=?");
        $stm->execute([$request->id]);
        $stm->closeCursor();
        return $stm->rowCount();

    }

    static public function create(\Request $request):User{
        $pdo = \DB::connect();
        $stm = $pdo->prepare("INSERT INTO user (`name`,`email`,`hash`) VALUES (?,?,?)");
        $password = password_hash($request->password, PASSWORD_DEFAULT);
        $stm->execute([$request->name, $request->email, $password]);
        $stm->closeCursor();
        return self::find($request);
    }

    public function createToken() {
        // JWT = Base64Url(Header) + Base64Url(Payload) + Base64Url(Signature)
        // Header
        $header = json_encode([
            "alg" => "HS256", // algorithm
            "typ" => "JWT" //type
        ], true);
        $headerEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        // Payload
        $payload = json_encode([
            "sub" => json_encode([
                        "id" => $this->id,
                        "name" => $this->name,
                        "email" => $this->email
                    ]), // subject
            "iat" => time(), // timestamp
            "exp" => time() * (7 * 24 * 60 * 60) //expiration time (1 semana)
        ], true);
        $payloadEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Signature
        $data = $headerEncoded . "." . $payloadEncoded;
        $digitalSignature = hash_hmac("SHA256", $data, $this->secretKey, true);
        $digitalSignatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($digitalSignature));

        return $headerEncoded . "." . $payloadEncoded . "." . $digitalSignatureEncoded;
    }

    public function verifyPassword($typedPassword, $hash) {
        return password_verify($typedPassword, $hash);
    }
}