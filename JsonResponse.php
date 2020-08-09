<?php

class JsonResponse {
    private string $data;
    private array $headers;
    private int $status_code;

    public function __construct($data,$status_code,$headers){
        $this->data = json_encode($data);
        $this->status_code = $status_code;
        $this->headers = $headers;

    }

    public function send(){
        http_response_code($this->status_code);
        foreach ($this->headers as $key => $value) {
            $h1 = trim($key);
            $h2 = trim($value);
            header($h1.": ".$h2);
        }
        header("Content-type: application/json");
        echo $this->data;
    }

}

function response($data,$status_code=200,$headers=[]): JsonResponse{
    $response = new JsonResponse($data,$status_code,$headers);
    return $response;
}

?>