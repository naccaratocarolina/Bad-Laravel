<?php
namespace Models;
require_once "Request.php";
use Request;

require_once "DB.php";

class User{
    public int $id;
    public string $name;
    public string $email;
    private string $password;
    public string $hash;

    public function __construct(){
        unset($this->password);
    }

    /**
     * Retorna todos os usuários existentes no banco de dados.
     *
     * @return array
     */
    static public function all():array{
        $pdo = \DB::connect();
        $stm = $pdo->prepare("Select `id`,`name`,`email` from user");
        $stm->execute();
        $users = $stm->fetchAll(\PDO::FETCH_ASSOC);
        return $users;
    }

    /**
     * Faz uma busca em funcao do parametro dado no request, a fim de encontrar o usuário correspondente.
     *
     * @param Request $request
     * @return User
     */
    static public function find(\Request $request):User {
        $pdo = \DB::connect();

        $filter = "";
        $str = "";

        // Se a busca for em funcao do id
        if ($request->id) {
            $filter .= "id";
            $str .= $request->id;
        }

        // Se a busca for em funcao do nome
        else if ($request->name) {
            $filter .= "name";
            $str .= $request->name;
        }

        // Se a busca for em funcao do email
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

    /**
     * Cria um novo usuário no banco de dados.
     *
     * @param Request $request
     * @return User
     */
    static public function create(\Request $request):User{
        $pdo = \DB::connect();
        $stm = $pdo->prepare("INSERT INTO user (`name`,`email`,`hash`) VALUES (?,?,?)");
        $password = password_hash($request->password, PASSWORD_DEFAULT);
        $stm->execute([$request->name, $request->email, $password]);
        $stm->closeCursor();
        return self::find($request);
    }

    /**
     * Atualiza um usuário já existente em funcao dos parametros dados no request.
     *
     * @param Request $request
     * @return User
     */
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

    /**
     * Deleta um usuário do banco de dados.
     *
     * @param Request $request
     * @return int
     */
    static public function delete(\Request $request):int{
        $pdo = \DB::connect();
        $stm = $pdo->prepare("Delete from user where id=?");
        $stm->execute([$request->id]);
        $stm->closeCursor();
        return $stm->rowCount();

    }

    /**
     * Gera um JWT para determinado usuário, respeitando a estrutura desses tokens.
     * JWT = Base64Url(Header) + Base64Url(Payload) + Base64Url(Signature)
     *
     * @param string $secret
     * @return string
     */
    public function createToken($secret = 'NdRgUkXp2s5v8x/A?D(G+KbPeShVmYq3') {
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
        $digitalSignature = hash_hmac("SHA256", $data, $secret, true);
        $digitalSignatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($digitalSignature));

        return $headerEncoded . "." . $payloadEncoded . "." . $digitalSignatureEncoded;
    }

    /**
     * Funcao que verifica se a senha digitada pelo usuário é igual a senha registrada no BD do mesmo.
     *
     * @param $typedPassword
     * @param $hash
     * @return bool
     */
    public function verifyPassword($typedPassword, $hash) {
        return password_verify($typedPassword, $hash);
    }
}
