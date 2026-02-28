<?php
class UsersController extends Controller {
    private $userModel;

    public function __construct(){
        AuthHelper::requireRole('admin');
        $this->userModel = $this->model('User');
    }

    public function index(){
        $users = $this->userModel->getAllUsers();
        $data = [
            'users' => $users
        ];
        $this->view('users/index', $data);
    }

    public function create(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'full_name' => trim($_POST['full_name']),
                'email' => trim($_POST['email']),
                'role' => trim($_POST['role']),
                'profile_image' => 'default.png',
                'username_err' => '',
                'password_err' => ''
            ];

            // Handle Image Upload
            if(!empty($_POST['photo_base64'])){
                 $data['profile_image'] = $this->saveBase64Image($_POST['photo_base64'], $data['username']);
            } elseif(isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === 0){
                 $data['profile_image'] = $this->saveUploadedImage($_FILES['photo_file'], $data['username']);
            }

            if(empty($data['username'])){
                $data['username_err'] = 'Ingrese un usuario';
            } else {
                if($this->userModel->findUserByUsername($data['username'])){
                    $data['username_err'] = 'El usuario ya existe';
                }
            }

            if(empty($data['password'])){
                $data['password_err'] = 'Ingrese una contraseña';
            }

            if(empty($data['username_err']) && empty($data['password_err'])){
                if($this->userModel->createUser($data)){
                    $this->redirect('users/index');
                } else {
                    die('Algo salió mal');
                }
            } else {
                $this->view('users/create', $data);
            }
        } else {
            $data = [
                'username' => '',
                'password' => '',
                'full_name' => '',
                'email' => '',
                'role' => 'reader', // default
                'username_err' => '',
                'password_err' => ''
            ];
            $this->view('users/create', $data);
        }
    }

    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data = [
                'id' => $id,
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']), // Optional
                'full_name' => trim($_POST['full_name']),
                'email' => trim($_POST['email']),
                'role' => trim($_POST['role']),
                'username_err' => ''
            ];
            
            // Handle Image Upload
            if(!empty($_POST['photo_base64'])){
                 $data['profile_image'] = $this->saveBase64Image($_POST['photo_base64'], $data['username']);
            } elseif(isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === 0){
                 $data['profile_image'] = $this->saveUploadedImage($_FILES['photo_file'], $data['username']);
            }

            if(empty($data['username'])){
                $data['username_err'] = 'Ingrese un usuario';
            }

            if(empty($data['username_err'])){
                if($this->userModel->updateUser($data)){
                    $this->redirect('users/index');
                } else {
                    die('Algo salió mal');
                }
            } else {
                $this->view('users/edit', $data);
            }
        } else {
            $user = $this->userModel->getUserById($id);
            if(!$user){
                $this->redirect('users/index');
            }

            $data = [
                'id' => $user['id'],
                'username' => $user['username'],
                'password' => '',
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'profile_image' => $user['profile_image'] ?? 'default.png',
                'username_err' => '',
                'password_err' => ''
            ];
            $this->view('users/edit', $data);
        }
    }

    private function saveBase64Image($base64_string, $username) {
        $data = explode(',', $base64_string);
        $content = base64_decode($data[1]);
        $filename = $username . '_' . time() . '.png';
        $path = 'public/uploads/users/'; // Ensure this path exists
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        file_put_contents($path . $filename, $content);
        return $filename;
    }

    private function saveUploadedImage($file, $username) {
        $filename = $username . '_' . time() . '_' . $file['name'];
        $path = 'public/uploads/users/'; // Ensure this path exists
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        move_uploaded_file($file['tmp_name'], $path . $filename);
        return $filename;
    }

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->userModel->deleteUser($id)){
                $this->redirect('users/index');
            } else {
                die('Algo salió mal');
            }
        } else {
            $this->redirect('users/index');
        }
    }
}
