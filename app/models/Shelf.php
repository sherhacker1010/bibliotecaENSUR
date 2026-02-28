<?php
class Shelf extends Model {
    public function getAllShelves(){
        $stmt = $this->db->query("SELECT * FROM shelves ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getShelfById($id){
        $stmt = $this->db->prepare("SELECT * FROM shelves WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createShelf($data){
        $stmt = $this->db->prepare("INSERT INTO shelves (name, description) VALUES (:name, :description)");
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':description', $data['description']);
        return $stmt->execute();
    }

    public function updateShelf($data){
        $stmt = $this->db->prepare("UPDATE shelves SET name = :name, description = :description WHERE id = :id");
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':id', $data['id']);
        return $stmt->execute();
    }

    public function deleteShelf($id){
        $stmt = $this->db->prepare("DELETE FROM shelves WHERE id = :id");
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
