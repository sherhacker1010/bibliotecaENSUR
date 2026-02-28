<?php
class AuthController extends Controller {
    private $userModel;

    public function __construct(){
        $this->userModel = $this->model('User');
    }

    public function index(){
        $this->login();
    }

    public function login(){
        if(AuthHelper::isLoggedIn()){
            $this->redirect('dashboard/index');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'username_err' => '',
                'password_err' => ''
            ];

            // Validate Username
            if(empty($data['username'])){
                $data['username_err'] = 'Por favor ingrese el usuario';
            }

            // Validate Password
            if(empty($data['password'])){
                $data['password_err'] = 'Por favor ingrese la contraseña';
            }

            // Check for user/email
            $user = $this->userModel->findUserByUsername($data['username']);
            
            if($user){
                // User found
            } else {
                $data['username_err'] = 'Usuario no encontrado';
            }

            // Make sure errors are empty
            if(empty($data['username_err']) && empty($data['password_err'])){
                // Validated
                // Check and set logged in user
                if(password_verify($data['password'], $user['password'])){
                    // Create Session
                    $this->createUserSession($user);
                } else {
                    $data['password_err'] = 'Contraseña incorrecta';
                    $this->view('auth/login', $data);
                }
            } else {
                // Load view with errors
                $this->view('auth/login', $data);
            }

        } else {
            // Init data
            $data = [
                'username' => '',
                'password' => '',
                'username_err' => '',
                'password_err' => ''
            ];

            // Load view
            $this->view('auth/login', $data);
        }
    }

    public function createUserSession($user){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];
        $this->redirect('dashboard/index');
    }

    public function logout(){
        unset($_SESSION['user_id']);
        unset($_SESSION['user_username']);
        unset($_SESSION['user_role']);
        unset($_SESSION['user_name']);
        session_destroy();
        $this->redirect('auth/login');
    }
}
