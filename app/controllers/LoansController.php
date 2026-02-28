<?php
class LoansController extends Controller {
    private $loanModel;
    private $userModel;
    private $bookModel; // Need to find Copy ID by Code

    public function __construct(){
        AuthHelper::requireRole(['admin', 'librarian']);
        $this->loanModel = $this->model('Loan');
        $this->userModel = $this->model('User');
        // Simple way to get Book/Copy model or use direct queries in Loan model helpers if needed.
        // I'll use a new helper method in Loan model or Book model to find copy by code.
    }

    public function index(){
        $loans = $this->loanModel->getActiveLoans();
        $this->view('loans/index', ['loans' => $loans]);
    }

    public function create(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'username' => trim($_POST['username']), // Reader
                'copy_code' => trim($_POST['copy_code']), // QR or Manual
                'error' => ''
            ];

            // 1. Find User
            $user = $this->userModel->findUserByUsername($data['username']);
            if(!$user){
                $data['error'] = 'Usuario no encontrado';
                $this->view('loans/create', $data);
                return;
            }
            if($user['role'] != 'reader'){
                 // Optional restriction: Can admins borrow? Usually yes. Prompt says "Reader... Ver dashboard".
                 // But system roles limit loaning? "ADIM.. NO puede registrar préstamos" (register *for others*? or borrow?)
                 // Roles: LIBRARIAN -> Register loans. ADMIN -> Config. 
                 // READER -> Receives loans.
                 // So we are loaning TO a user.
            }

            // 2. Find Copy ID
            // Reuse DB instance or add method to Book model
            // I'll add a quick query here or in Book model. Let's assume Book model has `getCopyByCode`.
            // I'll add it to Book model in next step if missed, or use raw DB in Loan model?
            // Cleanest is Loan model helper: `getCopyIdByCode`.
            // Wait, I can't edit Book model in this turn easily without multi-replace.
            // I'll add `getCopyByCode` to `Loan` model or just `Book` model method logic.
            // Actually, I'll use a `Book` model method and load it.
            $bookModel = $this->model('Book');
            // Check if I added `getCopyByCode` to Book? No.
            // I'll add a method in `Loan` model `findCopyByCode($code)` for convenience.
            $copy = $this->loanModel->findCopyByCode($data['copy_code']);
            
            if(!$copy){
                 $data['error'] = 'Código de libro no encontrado';
                 $this->view('loans/create', $data);
                 return;
            }

            // 3. Calculate Due Date
            $days = $this->loanModel->getLoanLimitDays();
            $dueDate = date('Y-m-d', strtotime("+$days days"));

            // 4. Create Loan
            $loanData = [
                'copy_id' => $copy['id'],
                'user_id' => $user['id'],
                'librarian_id' => $_SESSION['user_id'],
                'due_date' => $dueDate
            ];

            $result = $this->loanModel->createLoan($loanData);

            if($result === true){
                $this->redirect('loans/index');
            } else {
                $data['error'] = $result; // Error message from Exception
                $this->view('loans/create', $data);
            }

        } else {
            $data = [
                'username' => '',
                'copy_code' => '',
                'error' => ''
            ];
            $this->view('loans/create', $data);
        }
    }

    public function returnPage(){
        $data = ['error' => '', 'loan' => null];
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $code = trim($_POST['copy_code']);
            $loan = $this->loanModel->findActiveLoanByCopyCode($code);
            
            if($loan){
                // Calculate Fine
                $dueDate = new DateTime($loan['due_date']);
                $today = new DateTime();
                $fine = 0;
                $daysOver = 0;
                
                if($today > $dueDate){
                    $diff = $today->diff($dueDate);
                    $daysOver = $diff->days;
                    $finePerDay = $this->loanModel->getFineAmount();
                    $fine = $daysOver * $finePerDay;
                }
                
                $data['loan'] = $loan;
                $data['fine'] = $fine;
                $data['days_over'] = $daysOver;
                
                // If confirmed in a separate step or same form?
                // Let's say if 'confirm_return' is set
                if(isset($_POST['confirm_return'])){
                     if($this->loanModel->returnLoan($loan['id'], $loan['real_copy_id'], $fine)){
                         // Redirect with success
                         // Maybe show simple success page
                         $data['success'] = 'Libro devuelto correctamente.';
                         $data['loan'] = null; // Clear
                     } else {
                         $data['error'] = 'Error al procesar devolución.';
                     }
                }
                
            } else {
                $data['error'] = 'No se encontró préstamo activo para este código.';
            }
        }
        
        $this->view('loans/return', $data);
    }
}
