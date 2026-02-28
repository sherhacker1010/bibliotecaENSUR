<?php
class Setting extends Model {
    public function getAllSettings(){
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM settings");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // Returns [key => value] array
    }

    public function updateSetting($key, $value){
        $stmt = $this->db->prepare("UPDATE settings SET setting_value = :value WHERE setting_key = :key");
        $stmt->bindValue(':value', $value);
        $stmt->bindValue(':key', $key);
        return $stmt->execute();
    }
}
