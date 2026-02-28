<?php
class BooksController extends Controller {
    private $bookModel;
    private $shelfModel;

    public function __construct(){
        AuthHelper::requireLogin(); // Readers can view but not create
        $this->bookModel = $this->model('Book');
        $this->shelfModel = $this->model('Shelf');
    }

    public function index(){
        $books = $this->bookModel->searchBooks(); // Fetch all by default using the new search logic
        $shelves = $this->shelfModel->getAllShelves();
        $this->view('books/index', ['books' => $books, 'shelves' => $shelves]);
    }

    public function search(){
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $shelf_id = isset($_GET['shelf_id']) && $_GET['shelf_id'] !== '' ? $_GET['shelf_id'] : null;
        
        $books = $this->bookModel->searchBooks($query, $shelf_id);
        
        // Return partial view for AJAX
        $data = ['books' => $books];
        require_once APPROOT . '/views/books/partials/shelf_list.php';
    }

    public function create(){
        AuthHelper::requireRole(['admin', 'librarian']);

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'code' => trim($_POST['code']),
                'title' => trim($_POST['title']),
                'author' => trim($_POST['author']),
                'genre' => trim($_POST['genre']),
                'shelf_id' => trim($_POST['shelf_id']),
                'description' => $_POST['description'], // HTML allowed
                'stock' => trim($_POST['stock']),
                'cover_image' => '',
                'code_err' => '',
                'title_err' => '',
                'stock_err' => ''
            ];

            // Validate
            if(empty($data['code'])) $data['code_err'] = 'Ingrese el código';
            if(empty($data['title'])) $data['title_err'] = 'Ingrese el título';
            if(empty($data['stock'])) $data['stock_err'] = 'Ingrese el stock inicial';

            // Handle File Upload
            if(isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === 0){
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $fileName = $_FILES['cover_image']['name'];
                $fileTmp = $_FILES['cover_image']['tmp_name'];
                $fileParts = explode('.', $fileName);
                $fileExt = strtolower(end($fileParts));

                if(in_array($fileExt, $allowed)){
                    $newName = uniqid() . '.' . $fileExt;
                    $dest = 'uploads/covers/' . $newName;
                    if(!file_exists('uploads/covers')){
                        mkdir('uploads/covers', 0777, true);
                    }
                    if(move_uploaded_file($fileTmp, $dest)){
                        $data['cover_image'] = $newName;
                    }
                }
            }

            if(empty($data['code_err']) && empty($data['title_err'])){
                if($this->bookModel->createBook($data)){
                    $this->redirect('books/index');
                } else {
                    die('Error al crear libro');
                }
            } else {
                $shelves = $this->shelfModel->getAllShelves();
                $data['shelves'] = $shelves;
                $this->view('books/create', $data);
            }

        } else {
            $shelves = $this->shelfModel->getAllShelves();
            // Generate random 6-char alphanumeric code
            $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomCode = substr(str_shuffle($chars), 0, 6);
            
            $data = [
                'code' => $randomCode,
                'title' => '',
                'author' => '',
                'genre' => '',
                'shelf_id' => '',
                'description' => '',
                'stock' => '1',
                'cover_image' => '',
                'shelves' => $shelves
            ];
            $this->view('books/create', $data);
        }

    }

    public function edit($id){
        AuthHelper::requireRole(['admin', 'librarian']);

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Process form
            $data = [
                'id' => $id,
                'code' => trim($_POST['code']),
                'title' => trim($_POST['title']),
                'author' => trim($_POST['author']),
                'genre' => trim($_POST['genre']),
                'shelf_id' => trim($_POST['shelf_id']),
                'description' => $_POST['description'],
                'stock' => trim($_POST['stock']),
                'cover_image' => '',
                'code_err' => '',
                'title_err' => '',
                'stock_err' => ''
            ];

            // Validate
            if(empty($data['code'])) $data['code_err'] = 'Ingrese el código';
            if(empty($data['title'])) $data['title_err'] = 'Ingrese el título';
            if(empty($data['stock'])) $data['stock_err'] = 'Ingrese el stock';

            // Handle File Upload
            if(isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === 0){
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $fileName = $_FILES['cover_image']['name'];
                $fileTmp = $_FILES['cover_image']['tmp_name'];
                $fileParts = explode('.', $fileName);
                $fileExt = strtolower(end($fileParts));

                if(in_array($fileExt, $allowed)){
                    $newName = uniqid() . '.' . $fileExt;
                    $dest = 'uploads/covers/' . $newName;
                    if(!file_exists('uploads/covers')){
                        mkdir('uploads/covers', 0777, true);
                    }
                    if(move_uploaded_file($fileTmp, $dest)){
                        $data['cover_image'] = $newName;
                    }
                }
            }

            if(empty($data['code_err']) && empty($data['title_err'])){
                if($this->bookModel->updateBook($data)){
                    $this->redirect('books/index');
                } else {
                    die('Error al actualizar libro');
                }
            } else {
                $shelves = $this->shelfModel->getAllShelves();
                $data['shelves'] = $shelves;
                $this->view('books/edit', $data);
            }

        } else {
            // Get existing book data
            $book = $this->bookModel->getBookById($id);
            if(!$book){
                $this->redirect('books/index');
            }

            $shelves = $this->shelfModel->getAllShelves();
            
            $data = [
                'id' => $id,
                'code' => $book['code'],
                'title' => $book['title'],
                'author' => $book['author'],
                'genre' => $book['genre'],
                'shelf_id' => $book['shelf_id'],
                'description' => $book['description'],
                'stock' => $book['stock'],
                'current_cover' => $book['cover_image'],
                'shelves' => $shelves,
                'code_err' => '',
                'title_err' => '',
                'stock_err' => ''
            ];
            
            $this->view('books/edit', $data);
        }
    }

    public function show($id){
        $book = $this->bookModel->getBookById($id);
        $copies = $this->bookModel->getCopiesByBookId($id);
        
        // Fetch active loans to map to copies
        $loanModel = $this->model('Loan');
        $activeLoans = $loanModel->getActiveLoans();
        
        // Map loans to copies
        foreach($copies as &$copy){
            $copy['active_loan'] = null;
            if($copy['status'] == 'loaned'){
                foreach($activeLoans as $loan){
                    if($loan['copy_id'] == $copy['id']){
                        $copy['active_loan'] = $loan;
                        break;
                    }
                }
            }
        }
        
        if(!$book){
            $this->redirect('books/index');
        } else {
            $this->view('books/show', ['book' => $book, 'copies' => $copies]);
        }
    }

    public function history($copyId){
        $loanModel = $this->model('Loan');
        $history = $loanModel->getHistoryByCopyId($copyId);
        
        $copyInfo = $this->bookModel->getCopyDetails($copyId);
        
        $this->view('books/history', ['history' => $history, 'copy' => $copyInfo]);
    }
}
