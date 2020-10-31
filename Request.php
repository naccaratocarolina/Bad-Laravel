<?php

class Request{

    public function __construct(array $arr){
        //rota vai retornar um array associativo
        //e essa funcao vai transformar em um objeto do tipo request
        foreach($arr as $k => $v){
            $this->$k = $v;
        }
    }
}
// "name" => "Rodrigo"
// $request->name; ==> "Rodrigo"