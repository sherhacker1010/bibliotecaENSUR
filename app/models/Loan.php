<?php
class Loan extends Model {
    public function createLoan($data){
        $this->db->beginTransaction();
        try {
            // Check max books
            $sql = "SELECT COUNT(*) as count FROM loans WHERE user_id = :user_id AND status = 'active'";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $data['user_id']);
            $stmt->execute();
            $activeLoans = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Get Settings
            // Default max = 1
            $stmt = $this->db->query("SELECT setting_value FROM settings WHERE setting_key = 'max_books'");
            $maxBooks = $stmt->fetchColumn() ?: 1;

            if($activeLoans >= $maxBooks){
                throw new Exception("El usuario ha alcanzado el límite de préstamos ($maxBooks).");
            }

            // Check if user has fines? (Optional, but "Sistema de bloqueo automático")
            // Prompt: "Si lector tiene mora activa, no puede solicitar préstamo."
            $sql = "SELECT COUNT(*) as fines FROM fines WHERE loan_id IN (SELECT id FROM loans WHERE user_id = :user_id) AND status = 'unpaid'";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $data['user_id']);
            $stmt->execute();
            if($stmt->fetchColumn() > 0){
                throw new Exception("El usuario tiene multas pendientes.");
            }

            // Check Copy Status
            $stmt = $this->db->prepare("SELECT status FROM book_copies WHERE id = :copy_id");
            $stmt->bindValue(':copy_id', $data['copy_id']);
            $stmt->execute();
            $status = $stmt->fetchColumn();
            
            if($status != 'available'){
                throw new Exception("La copia no está disponible (Estado: $status).");
            }

            // Insert Loan
            $sql = "INSERT INTO loans (copy_id, user_id, librarian_id, loan_date, due_date, status) VALUES (:copy_id, :user_id, :librarian_id, CURDATE(), :due_date, 'active')";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':copy_id', $data['copy_id']);
            $stmt->bindValue(':user_id', $data['user_id']);
            $stmt->bindValue(':librarian_id', $data['librarian_id']);
            $stmt->bindValue(':due_date', $data['due_date']);
            $stmt->execute();

            // Update Copy Status
            $stmt = $this->db->prepare("UPDATE book_copies SET status = 'loaned' WHERE id = :copy_id");
            $stmt->bindValue(':copy_id', $data['copy_id']);
            $stmt->execute();

            // Update Book Stock (Optional? Book 'stock' usually means TOTAL copies, but sometimes 'Available' copies. 
            // My schema has 'stock' in 'books'. If I use 'stock' as AVAILABLE, update it. 
            // If 'stock' is TOTAL, don't update.
            // Prompt: "Control de Stock... Stock en tiempo real."
            // If I have 5 copies, stock is 5. If 1 loaned, available is 4.
            // I'll decrement stock in books table to mean "Available Stock".
            // Or I can count available copies dynamically.
            // Let's decrement for easier dashboard stats.
            $stmt = $this->db->prepare("UPDATE books SET stock = stock - 1 WHERE id = (SELECT book_id FROM book_copies WHERE id = :copy_id)");
             $stmt->bindValue(':copy_id', $data['copy_id']);
            $stmt->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return $e->getMessage();
        }
    }

    public function getActiveLoans(){
         $sql = "SELECT loans.*, 
                        users.full_name as user_name, 
                        users.username as user_username,
                        users.profile_image as user_image,
                        book_copies.unique_code,
                        books.title as book_title,
                        books.cover_image
                FROM loans
                JOIN users ON loans.user_id = users.id
                JOIN book_copies ON loans.copy_id = book_copies.id
                JOIN books ON book_copies.book_id = books.id
                WHERE loans.status = 'active'
                ORDER BY loans.due_date ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLoanLimitDays(){
        $stmt = $this->db->query("SELECT setting_value FROM settings WHERE setting_key = 'loan_days'");
        return $stmt->fetchColumn() ?: 3;
    }
    
    public function getFineAmount(){
        $stmt = $this->db->query("SELECT setting_value FROM settings WHERE setting_key = 'fine_per_day'");
        return $stmt->fetchColumn() ?: 1000;
    }

    public function findCopyByCode($code){
        $stmt = $this->db->prepare("SELECT * FROM book_copies WHERE unique_code = :code");
        $stmt->bindValue(':code', $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findActiveLoanByCopyCode($code){
        $sql = "SELECT loans.*, book_copies.id as real_copy_id, book_copies.unique_code, books.title, users.full_name 
                FROM loans 
                JOIN book_copies ON loans.copy_id = book_copies.id 
                JOIN books ON book_copies.book_id = books.id
                JOIN users ON loans.user_id = users.id
                WHERE book_copies.unique_code = :code AND loans.status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':code', $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function returnLoan($loanId, $copyId, $fineAmount = 0){
        $this->db->beginTransaction();
        try {
            // Update Loan
            $sql = "UPDATE loans SET return_date = CURDATE(), status = 'returned' WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $loanId);
            $stmt->execute();

            // Update Copy Status
            $stmt = $this->db->prepare("UPDATE book_copies SET status = 'available' WHERE id = :copy_id");
            $stmt->bindValue(':copy_id', $copyId);
            $stmt->execute();

            // Update Book Stock (+1 Available)
            $stmt = $this->db->prepare("UPDATE books SET stock = stock + 1 WHERE id = (SELECT book_id FROM book_copies WHERE id = :copy_id)");
            $stmt->bindValue(':copy_id', $copyId);
            $stmt->execute();

            // Insert Fine if exists
            if($fineAmount > 0){
                $sql = "INSERT INTO fines (loan_id, amount, status) VALUES (:loan_id, :amount, 'unpaid')";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':loan_id', $loanId);
                $stmt->bindValue(':amount', $fineAmount);
                $stmt->execute();
            }

            $this->db->commit();
            return true;
        } catch(Exception $e){
            $this->db->rollBack();
            return false;
        }
    }

    public function getHistoryByCopyId($copyId){
        $sql = "SELECT loans.*, 
                       users.full_name as user_name, 
                       users.role as user_role,
                       users.profile_image as user_image,
                       librarians.full_name as librarian_name,
                       librarians.profile_image as librarian_image
                FROM loans
                JOIN users ON loans.user_id = users.id
                LEFT JOIN users as librarians ON loans.librarian_id = librarians.id
                WHERE loans.copy_id = :copy_id
                ORDER BY loans.loan_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':copy_id', $copyId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUnpaidFines($userId){
        $sql = "SELECT fines.*, loans.due_date, books.title as book_title
                FROM fines
                JOIN loans ON fines.loan_id = loans.id
                JOIN book_copies ON loans.copy_id = book_copies.id
                JOIN books ON book_copies.book_id = books.id
                WHERE loans.user_id = :user_id AND fines.status = 'unpaid'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
