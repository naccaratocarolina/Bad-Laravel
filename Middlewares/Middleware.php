<?php
namespace Middleware;
require_once "Request.php";
require_once "Handler.php";
interface Middleware{
    public function handle(\Request $request,\Handler $next);
} 