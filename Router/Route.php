<?php
namespace Router;
require_once 'Controllers/UserController.php';
require_once 'Controllers/BookController.php';
require_once 'Controllers/AuthController.php';
require_once 'Request.php';
require_once 'Middlewares/IsPalmeira.php';
require_once 'Middlewares/AuthMiddleware.php';
require_once 'Handler.php';
require_once "Middlewares/CORS.php";

class Route{
    private static $get_routes = [];
    private static $post_routes = [];
    private static $middlewares = ["CORS"];//, "AuthMiddleware", "IsPalmeira"];

    static public function get(string $url, string $controllerMethod, array $middlewares = []){
        self::$get_routes[$url] = ["controllerMethod" => $controllerMethod, "middlewares" => $middlewares];
    }
    
    static public function post(string $url, string $controllerMethod, array $middlewares = []) {
        self::$post_routes[$url] = ["controllerMethod" => $controllerMethod, "middlewares" => $middlewares];
    }

    static public function handle(){
        $url = $_SERVER["REQUEST_URI"];
        $path = parse_url($url, PHP_URL_PATH);
        switch($_SERVER["REQUEST_METHOD"]) {
            case "GET":
                if(!isset(self::$get_routes[$path])){
                    http_response_code(404);
                    echo 'NOT FOUND';
                    die();    
                }
                $function = explode("@",self::$get_routes[$path]["controllerMethod"]);
                $request = new \Request($_GET);

                // Aplica as route middlewares, caso elas existam na chamada da rota
                $routeMiddlewares = self::$middlewares;
                if(isset(self::$get_routes[$path]["middlewares"])) {
                    foreach (self::$get_routes[$path]["middlewares"] as $middleware) {
                        array_push($routeMiddlewares, $middleware);
                    }
                }

                $handler = new \Handler($routeMiddlewares, $function);
                $handler($request);
                break;
            case "POST":
                if(!isset(self::$post_routes[$path])){
                    http_response_code(404);
                    echo 'NOT FOUND';
                    die();    
                }
                $function = explode("@",self::$post_routes[$path]["controllerMethod"]);
                $request = new \Request($_POST);

                // Aplica as route middlewares, caso elas existam na chamada da rota
                $routeMiddlewares = self::$middlewares;
                if(isset(self::$post_routes[$path]["middlewares"])) {
                    foreach (self::$post_routes[$path]["middlewares"] as $middleware) {
                        array_push($routeMiddlewares, $middleware);
                    }
                }

                $handler = new \Handler($routeMiddlewares,$function);
                $handler($request);
                break;
            case "OPTIONS":
                $request = new \Request($_POST);
                $handler = new \Handler(self::$middlewares,function(){});
                $handler($request);
                http_response_code(204);
                break;
            default: 
                http_response_code(405);
                throw new \Exception("Method Not Suported");
                die();
        }
    }
};
