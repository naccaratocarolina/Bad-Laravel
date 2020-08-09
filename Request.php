<?php

class Request{

    public function __construct(array $arr){
        foreach($arr as $k =>$v){
            $this->$k = $v;
        }
    }
}