<?php
namespace Models;
require_once "Request.php";
require_once "Models/User.php";
require_once "DB.php";

use Request;
use Models\User;

class Auth {
    /**
     * Retorna o usuario autenticado, acessando o token guardado na header de
     * autenticadao e decodificando o usuario guardado na payload do token.
     *
     * @param $token
     * @return mixed
     */
    public function user() {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'];
        $token = explode(" ", $authHeader)[1];
        $payload = base64_decode(explode(".", $token)[1]);
        return json_decode($payload)->sub;
    }

    /**
     * Funcao que pega o email e password passados no request e verifica duas coisas:
     * Se existe um usuário registrado com esse email e se a senha digitada bate com
     * a senha armazenada no banco de dados desse usuário.
     *
     * @param $request
     * @return bool
     */
    public function attempt($request):bool {
        $user = User::find($request);
        if (isset($user) && $user->verifyPassword($request->password, $user->hash)) return true;
        return false;
    }

    /**
     * Funcao que verifica a autenticidade de um token.
     *
     * @param $token
     * @param string $secret
     * @return bool
     */
    public function verifyToken($token, $secret = 'NdRgUkXp2s5v8x/A?D(G+KbPeShVmYq3'):bool {
        // Acessa os tres elementos do token (Header, Payload e Signature)
        $tokenElements = explode(".", $token);
        $header = base64_decode($tokenElements[0]);
        $payload = base64_decode($tokenElements[1]);
        $signature = $tokenElements[2];

        // Verifica se o token esta expirado
        $exp = json_decode($payload)->exp;
        $isExpired = ($exp - time()) < 0;

        // Cria uma assinatura digital usando a header e payload extraidos
        $headerEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $payloadEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $data = $headerEncoded . "." . $payloadEncoded;
        $digitalSignature = hash_hmac("SHA256", $data, $secret, true);
        $digitalSignatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($digitalSignature));
        $isValid = ($signature === $digitalSignatureEncoded);

        // Se o token ja tiver expirado, ou a assinatura digital nao for confirmada, retorna falso
        if ($isExpired || !$isValid) return false;

        // Retorna true caso contrario
        else return true;
    }
}