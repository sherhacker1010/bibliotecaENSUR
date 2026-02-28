<?php
class Book extends Model {
    public function getAllBooks(){
        $sql = "SELECT books.*, shelves.name as shelf_name 
                FROM books 
                LEFT JOIN shelves ON books.shelf_id = shelves.id 
                ORDER BY shelves.name ASC, books.title ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchBooks($query = '', $shelf_id = null){
        $sql = "SELECT books.*, shelves.name as shelf_name 
                FROM books 
                LEFT JOIN shelves ON books.shelf_id = shelves.id 
                WHERE (books.title LIKE :query OR books.author LIKE :query)";
        
        if($shelf_id){
            $sql .= " AND books.shelf_id = :shelf_id";
        }
        
        $sql .= " ORDER BY books.title ASC"; // Flat list, sort by title

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':query', "%$query%");
        if($shelf_id){
            $stmt->bindValue(':shelf_id', $shelf_id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookById($id){
        $sql = "SELECT books.*, shelves.name as shelf_name 
                FROM books 
                LEFT JOIN shelves ON books.shelf_id = shelves.id 
                WHERE books.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createBook($data){
        // Transaction to ensure book and copies are created together
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO books (code, title, author, genre, shelf_id, description, cover_image, stock) 
                    VALUES (:code, :title, :author, :genre, :shelf_id, :description, :cover_image, :stock)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':code', $data['code']);
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':author', $data['author']);
            $stmt->bindValue(':genre', $data['genre']);
            $stmt->bindValue(':shelf_id', $data['shelf_id']);
            $stmt->bindValue(':description', $data['description']);
            $stmt->bindValue(':cover_image', $data['cover_image']);
            $stmt->bindValue(':stock', $data['stock']);
            $stmt->execute();
            
            $bookId = $this->db->lastInsertId();

            // Create Copies
            for($i = 1; $i <= $data['stock']; $i++){
                $uniqueCode = $data['code'] . '-' . $i; // Example: LIB-001-1
                // Generate QR for copy
                $qrPath = QRHelper::generate($uniqueCode, $uniqueCode); // Saves to uploads/qrcodes/LIB-001-1.png

                $sqlCopy = "INSERT INTO book_copies (book_id, unique_code, status) VALUES (:book_id, :unique_code, 'available')";
                $stmtCopy = $this->db->prepare($sqlCopy);
                $stmtCopy->bindValue(':book_id', $bookId);
                $stmtCopy->bindValue(':unique_code', $uniqueCode);
                $stmtCopy->execute();
            }

            $this->db->commit();
            return true;

        } catch(Exception $e){
            $this->db->rollBack();
            return false;
        }
    }

    public function updateBook($data){
        try {
            $sql = "UPDATE books SET 
                    code = :code, 
                    title = :title, 
                    author = :author, 
                    genre = :genre, 
                    shelf_id = :shelf_id, 
                    description = :description, 
                    stock = :stock";
            
            // Only update cover image if a new one is provided
            if(!empty($data['cover_image'])){
                $sql .= ", cover_image = :cover_image";
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $data['id']);
            $stmt->bindValue(':code', $data['code']);
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':author', $data['author']);
            $stmt->bindValue(':genre', $data['genre']);
            $stmt->bindValue(':shelf_id', $data['shelf_id']);
            $stmt->bindValue(':description', $data['description']);
            $stmt->bindValue(':stock', $data['stock']);
            
            if(!empty($data['cover_image'])){
                $stmt->bindValue(':cover_image', $data['cover_image']);
            }
            
            if($stmt->execute()){
                return true;
            } else {
                return false;
            }
        } catch(Exception $e){
            return false;
        }
    }

    public function getCopiesByBookId($bookId){
        $stmt = $this->db->prepare("SELECT * FROM book_copies WHERE book_id = :book_id");
        $stmt->bindValue(':book_id', $bookId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCopyDetails($copyId){
        $sql = "SELECT book_copies.unique_code, books.title, books.id as book_id 
                FROM book_copies 
                JOIN books ON book_copies.book_id = books.id 
                WHERE book_copies.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $copyId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAllCopiesWithDetails(){
        $sql = "SELECT book_copies.*, books.title, books.author 
                FROM book_copies 
                JOIN books ON book_copies.book_id = books.id 
                ORDER BY books.title ASC, book_copies.unique_code ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookByCopyCode($uniqueCode){
        $sql = "SELECT books.id, books.title, books.author, books.cover_image, books.stock, 
                       book_copies.unique_code, book_copies.status,
                       shelves.name as shelf_name
                FROM book_copies 
                JOIN books ON book_copies.book_id = books.id 
                LEFT JOIN shelves ON books.shelf_id = shelves.id
                WHERE book_copies.unique_code = :code";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':code', $uniqueCode);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
