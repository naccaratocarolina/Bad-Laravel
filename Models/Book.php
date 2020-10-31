<?php
namespace Models;
require_once "DB.php";

class Book {
    public string $isbn; // Chave primaria - International Standard Book Number
    public string $title;
    public string $email;
    private string $password;
    public int $aid; // Foreign Key que referencia autores

    public function __construct(){
        unset($this->password);
    }

    static public function all():array {
        $pdo = \DB::connect();
        $stm = $pdo->prepare("SELECT b.ISBN, b.title, b.email, a.name, a.surname, b.aid FROM books AS b INNER JOIN authors AS a ON b.aid = a.aid");
        $stm->execute();
        $books = $stm->fetchAll(\PDO::FETCH_ASSOC);
        return $books;
    }

    static public function find($ISBN):Book {
        $pdo = \DB::connect();
        $stm = $pdo->prepare("SELECT b.ISBN, b.title, b.email, a.name, a.surname, b.aid FROM books AS b INNER JOIN authors AS a ON b.aid = a.aid WHERE ISBN=?");
        $stm->setFetchMode(\PDO::FETCH_CLASS, 'Models\Book');
        $stm->execute([$ISBN]);
        $book = $stm->fetch();
        $stm->closeCursor();
        return $book;
    }

    static public function create(\Request $request):Book {
        $pdo = \DB::connect();
        $stm = $pdo->prepare("INSERT INTO books (`ISBN`,`title`,`email`,`password`, `aid`) VALUES (?, ?, ?, ?, ?)");
        $stm->execute([$request->ISBN, $request->title, $request->email, $request->password, $request->aid]);
        $stm->closeCursor();
        return self::find($request->ISBN);
    }

    static public function update (\Request $request) {
        $pdo = \DB::connect();
        $query = "UPDATE books SET "; // vamos definir quais colunas atualizar em funcao do request
        $arr = [];
        $parameters = [];

        // Se o request tiver title
        if ($request->title) {
            array_push($parameters, '`title`=? ');
            array_push($arr, $request->title);
        }

        // Se o request tiver email
        if($request->email){
            array_push($parameters,'`email`=? ');
            array_push($arr,$request->email);
        }

        // Se o request tiver password
        if($request->password){
            array_push($parameters,'`password`=? ');
            array_push($arr,$request->password);
        }

        // Pega as colunas que serao atualizadas e procura o livro usando WHERE ISBN = $request->ISBN
        $query .= implode(',',$parameters) . 'where `ISBN`=?';
        array_push($arr,$request->ISBN);
        $stm = $pdo->prepare($query);
        // $arr carrega as informacoes a serem atualizadas
        $stm->execute($arr);
        return self::find($request->ISBN);
    }

    static public function delete(\Request $request):int {
        $pdo = \DB::connect();
        $stm = $pdo->prepare("DELETE FROM books WHERE ISBN=?");
        $stm->execute([$request->ISBN]);
        return $stm->rowCount();
    }
}