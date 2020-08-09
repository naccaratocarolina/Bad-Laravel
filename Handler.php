<?php
require_once "Request.php";
require_once "Middlewares/Middleware.php";
class Handler{
    public $middlewares = [];
    protected $position = 0;
    protected $controllerMethod;

    public function __construct(array $arr,$cm){
        //var_dump($arr);
        $this->middlewares = $arr;
        $this->controllerMethod = $cm;
    }

    public function __invoke(Request $request){
        $function = $this->middlewares[$this->position]; 
        if(isset($function)){
            $this->position++;
            call_user_func(["Middleware\\".$function,"handle"],$request,$this);
        }else{
            call_user_func($this->controllerMethod,$request);
        };
    }
}