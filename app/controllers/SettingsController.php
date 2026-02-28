<?php
class SettingsController extends Controller {
    private $db;

    public function __construct(){
        AuthHelper::requireRole('admin');
        // Setting model will handle DB connection
    }

    public function index(){
        $settingModel = $this->model('Setting');
        $settings = $settingModel->getAllSettings();
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Update settings
            foreach($_POST as $key => $value){
                $settingModel->updateSetting($key, $value);
            }
            $data = [
                'settings' => $settingModel->getAllSettings(),
                'success' => 'Configuración actualizada correctamente.'
            ];
            $this->view('settings/index', $data);
        } else {
            $this->view('settings/index', ['settings' => $settings]);
        }
    }
}
