<?php
namespace Controllers;
require_once "DB.php";
require_once "Request.php";
require_once "Models/Book.php";
require_once "JsonResponse.php";
use Models\Book;
use Request;
use JsonResponse;

class BookController {
    static public function index (Request $request) {
        $books = Book::all();
        $headers = ["Accept" => "application/json"];
        response($books,200,$headers)->send();
    }

    static public function show (Request $request) {
        $book = Book::find($request->ISBN);
        $headers = ["Accept" => "application/json"];
        response($book, 200, $headers)->send();
    }

    static public function create (Request $request) {
        $book = Book::create($request);
        $headers = ["Accept" => "application/json"];
        response($book, 201, $headers)->send();
    }

    static public function update (Request $request) {
        $book = Book::update($request);
        $headers = ["Accept" => "application/json"];
        response($book, 200, $headers)->send();
    }

    static public function delete (Request $request) {
        $book = Book::delete($request);
        $headers = ["Accept" => "application/json"];
        response($book, 200, $headers)->send();
    }
}