<?php
class ShelvesController extends Controller {
    private $shelfModel;

    public function __construct(){
        AuthHelper::requireRole(['admin', 'librarian']);
        $this->shelfModel = $this->model('Shelf');
    }

    public function index(){
        $shelves = $this->shelfModel->getAllShelves();
        $this->view('shelves/index', ['shelves' => $shelves]);
    }

    public function create(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']), // Summernote content is HTML, special chars filter might break it?
                // For simplified summernote, we might need RAW input for description if allowed, but safety first.
                // Assuming description is plain text or basic HTML. `filter_input_array` with `FILTER_SANITIZE_SPECIAL_CHARS` escapes HTML.
                // If Summernote is used, we need to allow HTML.
                // Let's use `$_POST['description']` directly but handle XSS on output or use a purifier.
                // For now, I'll stick to sanitized. If Summernote breaks, I'll switch to `FILTER_UNSAFE_RAW`.
                'name_err' => ''
            ];
            
            // Allow HTML for description if Summernote is used? User said "Summernote".
            // So we should NOT sanitize special chars for description here, but valid HTML.
            // I'll grab description raw.
            $data['description'] = $_POST['description']; 

            if(empty($data['name'])){
                $data['name_err'] = 'Ingrese un nombre';
            }

            if(empty($data['name_err'])){
                if($this->shelfModel->createShelf($data)){
                    $this->redirect('shelves/index');
                } else {
                    die('Algo salió mal');
                }
            } else {
                $this->view('shelves/create', $data);
            }
        } else {
            $data = [
                'name' => '',
                'description' => '',
                'name_err' => ''
            ];
            $this->view('shelves/create', $data);
        }
    }

    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data = [
                'id' => $id,
                'name' => trim($_POST['name']),
                'description' => $_POST['description'], // Allow HTML
                'name_err' => ''
            ];

            if(empty($data['name'])){
                $data['name_err'] = 'Ingrese un nombre';
            }

            if(empty($data['name_err'])){
                if($this->shelfModel->updateShelf($data)){
                    $this->redirect('shelves/index');
                } else {
                    die('Algo salió mal');
                }
            } else {
                $this->view('shelves/edit', $data);
            }
        } else {
            $shelf = $this->shelfModel->getShelfById($id);
            if(!$shelf){
                $this->redirect('shelves/index');
            }

            $data = [
                'id' => $shelf['id'],
                'name' => $shelf['name'],
                'description' => $shelf['description'],
                'name_err' => ''
            ];
            $this->view('shelves/edit', $data);
        }
    }

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->shelfModel->deleteShelf($id)){
                $this->redirect('shelves/index');
            } else {
                die('Algo salió mal');
            }
        } else {
            $this->redirect('shelves/index');
        }
    }
}
