<?php
class ReportsController extends Controller {
    private $bookModel;
    private $loanModel;

    public function __construct(){
        AuthHelper::requireRole(['admin', 'librarian']);
        $this->bookModel = $this->model('Book');
        $this->loanModel = $this->model('Loan');
    }

    public function index(){
        $books = $this->bookModel->getAllBooks();
        $this->view('reports/index', ['books' => $books]);
    }

    public function generate(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $type = $_POST['type'];
            $data = [];

            if($type == 'books'){
                $data = $this->bookModel->getAllBooks();
                $title = 'Inventario de Libros';
                $headers = ['ID', 'Código', 'Título', 'Autor', 'Género', 'Stock'];
                $fields = ['id', 'code', 'title', 'author', 'genre', 'stock'];
            } elseif ($type == 'loans') {
                $data = $this->loanModel->getActiveLoans(); // Maybe add 'all loans' method too?
                $title = 'Reporte de Préstamos Activos';
                $headers = ['ID', 'Libro', 'Usuario', 'Fecha Préstamo', 'Vencimiento', 'Estado'];
                $fields = ['id', 'book_title', 'user_username', 'loan_date', 'due_date', 'status'];
            }

            if(isset($_POST['export_excel'])){
                $this->exportExcel($data, $headers, $fields, $title);
            } else {
                $this->view('reports/view', ['data' => $data, 'headers' => $headers, 'fields' => $fields, 'title' => $title]);
            }
        }
    }

    private function exportExcel($data, $headers, $fields, $filename){
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename.csv\"");
        
        $output = fopen("php://output", "w");
        
        // Add BOM for UTF-8 to fix encoding in Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write headers
        fputcsv($output, $headers, ";"); // Use semicolon for Spanish Excel
        
        // Write data
        foreach($data as $row){
             $line = [];
             foreach($fields as $field){
                 // Handle specific Status translations if needed, or just raw data
                 $val = $row[$field] ?? '';
                 // Optional: Decode/Convert if needed, but BOM usually handles UTF-8 
                 $line[] = $val;
             }
             fputcsv($output, $line, ";");
        }
        
        fclose($output);
        exit;
    }

    public function qr_codes(){
        $copies = [];
        $title = 'Códigos QR - Todos los Libros';

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $scope = $_POST['scope'] ?? 'all';
            
            if($scope == 'all'){
                $copies = $this->bookModel->getAllCopiesWithDetails();
            } elseif($scope == 'single' && !empty($_POST['book_id'])){
                $book = $this->bookModel->getBookById($_POST['book_id']);
                $title = 'Códigos QR - ' . $book['title'];
                // Get copies for specific book manually since getCopiesByBookId doesn't return title
                // We can just append title to the result or use a modified query.
                // Re-using getCopiesByBookId and appending title to each is easier.
                $rawCopies = $this->bookModel->getCopiesByBookId($_POST['book_id']);
                foreach($rawCopies as $copy){
                    $copy['title'] = $book['title'];
                    $copies[] = $copy;
                }
            }
        } else {
             // Default to all or empty? Let's default to all for easy access via GET if needed, or empty.
             // But UI is via POST. If GET, redirect or show empty.
             // The loop in view will just be empty.
        }

        $this->view('reports/qr_codes', ['copies' => $copies, 'title' => $title]);
    }
}
