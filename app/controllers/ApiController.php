<?php
class ApiController extends Controller {
    private $userModel;
    private $bookModel;
    private $loanModel;

    public function __construct(){
        // CORS Headers
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, GET");
        
        $this->userModel = $this->model('User');
        $this->bookModel = $this->model('Book');
        $this->loanModel = $this->model('Loan');
    }

    public function login(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = json_decode(file_get_contents("php://input"), true);
            
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            $user = $this->userModel->findUserByUsername($username);

            if($user && password_verify($password, $user['password'])){
                // Token logic would go here (JWT). For now, return basic user info.
                unset($user['password']);
                $this->json(['status' => 'success', 'user' => $user]);
            } else {
                http_response_code(401);
                $this->json(['status' => 'error', 'message' => 'Credenciales inválidas']);
            }
        } else {
            http_response_code(405);
            $this->json(['status' => 'error', 'message' => 'Método no permitido']);
        }
    }

    public function books(){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $books = $this->bookModel->getAllBooks();
            $this->json(['status' => 'success', 'data' => $books]);
        }
    }

    public function loans(){
        // Require simple auth? Or public for now? 
        // Prompt: "Crear endpoints REST... api/loans"
        // I'll make it open for now or basic verification if headers sent.
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $loans = $this->loanModel->getActiveLoans();
            $this->json(['status' => 'success', 'data' => $loans]);
        }
    }
    public function search_users(){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $term = $_GET['term'] ?? '';
            $users = $this->userModel->searchUsers($term);
            // Return format compatible with jQuery UI Autocomplete if needed, or custom
            $data = [];
            foreach($users as $user){
                $data[] = [
                    'label' => $user['full_name'] . ' (' . $user['username'] . ')',
                    'value' => $user['username'], // We use username as the value to fill the input
                    'id' => $user['id']
                ];
            }
            $this->json($data);
        }
    }

    public function get_book_by_code(){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $code = $_GET['code'] ?? '';
            if(empty($code)){
                http_response_code(400);
                $this->json(['status' => 'error', 'message' => 'Código requerido']);
                return;
            }
            $book = $this->bookModel->getBookByCopyCode($code);
            if($book){
                $this->json(['status' => 'success', 'data' => $book]);
            } else {
                http_response_code(404);
                $this->json(['status' => 'error', 'message' => 'Libro no encontrado con ese código']);
            }
        }
    }
}
