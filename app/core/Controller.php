<?php
class Controller {
    // Load Model
    public function model($model){
        // Require model file
        require_once APPROOT . '/models/' . $model . '.php';
        // Instantiate model
        return new $model();
    }

    // Load View
    public function view($view, $data = []){
        // Check for view file
        if(file_exists(APPROOT . '/views/' . $view . '.php')){
            require_once APPROOT . '/views/' . $view . '.php';
        } else {
            // View does not exist
            die('View does not exist');
        }
    }

    // Return JSON response
    public function json($data){
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    // Redirect helper
    public function redirect($url){
        header('Location: ' . URLROOT . '/' . $url);
        exit;
    }
}
