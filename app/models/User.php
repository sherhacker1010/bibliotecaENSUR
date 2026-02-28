<?php
class User extends Model {
    public function findUserByUsername($username){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserById($id){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function searchUsers($term){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE full_name LIKE :term OR username LIKE :term LIMIT 10");
        $stmt->bindValue(':term', '%' . $term . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CRUD Methods
    public function getAllUsers(){
        $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser($data){
        $stmt = $this->db->prepare("INSERT INTO users (username, password, role, full_name, email, profile_image) VALUES (:username, :password, :role, :full_name, :email, :profile_image)");
        $stmt->bindValue(':username', $data['username']);
        $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $stmt->bindValue(':role', $data['role']);
        $stmt->bindValue(':full_name', $data['full_name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':profile_image', $data['profile_image'] ?? 'default.png');
        return $stmt->execute();
    }

    public function updateUser($data){
        $sql = "UPDATE users SET username = :username, role = :role, full_name = :full_name, email = :email";
        
        if(!empty($data['password'])){
             $sql .= ", password = :password";
        }
        if(!empty($data['profile_image'])){
             $sql .= ", profile_image = :profile_image";
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':username', $data['username']);
        if(!empty($data['password'])){
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        }
        $stmt->bindValue(':role', $data['role']);
        $stmt->bindValue(':full_name', $data['full_name']);
        $stmt->bindValue(':email', $data['email']);
        if(!empty($data['profile_image'])){
            $stmt->bindValue(':profile_image', $data['profile_image']);
        }
        $stmt->bindValue(':id', $data['id']);
        return $stmt->execute();
    }

    public function deleteUser($id){
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
