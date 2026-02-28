<?php
class DashboardController extends Controller {
    public function index(){
        AuthHelper::requireLogin();
        
        $user = AuthHelper::getUser();
        $stats = [
            'books' => 0,
            'loans' => 0,
            'overdue' => 0
        ];

        // Fetch Stats
        // Use Model or direct DB access since it's simple stats
        // To be clean, I should use Models, but for brevity here I'll use direct DB via a generic Model method or just create counts in specific models.
        // I'll instantiate models.
        $bookModel = $this->model('Book'); 
        $loanModel = $this->model('Loan');

        // I need count methods in models or just getAll and count (inefficient but works for small app)
        // Or add count methods.
        // Let's add simple count methods to BoardController via generic query if possible, or add to models.
        // I'll add quick ad-hoc queries here using the db instance if I can access it. 
        // Controller doesn't have direct access to Model's db.
        // I will add `getStats` to Loan model and Book model.
        // actually, let's just use `getAll` for now as valid prototype.
        
        // Books (Total Titles)
        $cnt = isset($bookModel) ? count($bookModel->getAllBooks()) : 0;
        $stats['books'] = $cnt;

        // Active Loans
        $activeLoans = $loanModel->getActiveLoans();
        $stats['loans'] = count($activeLoans);

        // Overdue
        foreach($activeLoans as $l){
            if(date('Y-m-d') > $l['due_date']){
                $stats['overdue']++;
            }
        }

        // Reader Notifications
        $notifications = [];
        if($user['user_role'] == 'reader'){
            // Get My Loans from activeLoans (re-filter if needed, but getActiveLoans gets ALL. Need to filter by User if not already done)
            // Wait, getActiveLoans() in Loan model gets ALL. Reader should only see THEIRS or I need separate query.
            // DashboardController currently calls getActiveLoans which implies GLOBAL stats for Admin/Librarian.
            // If Reader, getActiveLoans logic in Model is generic.
            // I should filter $activeLoans by user_id for Reader stats.
            
            $myLoans = [];
            foreach($activeLoans as $l){
                // fix: use user_id from session, not id
                if($l['user_id'] == $user['user_id']){
                    $myLoans[] = $l;
                }
            }
            
            // Re-calc stats for Reader
            $stats['loans'] = count($myLoans);
            $stats['overdue'] = 0;
            
            foreach($myLoans as $l){
                $today = date('Y-m-d');
                if($today > $l['due_date']){
                    $stats['overdue']++;
                    $notifications[] = ['type' => 'danger', 'msg' => "El libro '{$l['book_title']}' está vencido (Fecha: {$l['due_date']}). Por favor devuélvalo."];
                } elseif($today == $l['due_date']) {
                     $notifications[] = ['type' => 'warning', 'msg' => "El libro '{$l['book_title']}' vence HOY."];
                } elseif (date('Y-m-d', strtotime('+2 days')) >= $l['due_date']){
                     $notifications[] = ['type' => 'info', 'msg' => "El libro '{$l['book_title']}' vence pronto ({$l['due_date']})."];
                }
            }
            
            // Check Fines using Loan model method
             $fines = $loanModel->getUnpaidFines($user['user_id']);
             
             foreach($fines as $f){
                 $notifications[] = ['type' => 'danger', 'msg' => "Tiene una multa pendiente de $$f[amount] por el libro '{$f['book_title']}'."];
             }
        }
        
        $data = [
            'title' => 'Dashboard',
            'user' => $user,
            'stats' => $stats,
            'notifications' => $notifications
        ];
        
        $this->view('dashboard/index', $data);
    }
}
