<?php
class CatalogController extends Controller {
    private $bookModel;

    public function __construct(){
        $this->bookModel = $this->model('Book');
    }

    public function index(){
        // Public access or logged in? Usually public.
        // Assuming AuthHelper::requireLogin() if we want it private.
        AuthHelper::requireLogin();
        
        $books = $this->bookModel->getAllBooks();
        $this->view('catalog/index', ['books' => $books]);
    }
}
