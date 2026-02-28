<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="">
         <?php require APPROOT . '/views/layouts/sidebar.php'; ?>
    </div>

    <!-- Content -->
    <div class="flex-grow-1" style="background-color: #f3f4f6;">
        <?php require APPROOT . '/views/layouts/navbar.php'; ?>
        
        <div class="container-fluid px-4 py-4">
            <!-- Header & Actions -->
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between mb-4 gap-3">
                <h1 class="h3 mb-0 text-gray-800 fw-bold">Gestión de Usuarios</h1>
                <div class="d-flex gap-2">
                    <div class="input-group shadow-sm" style="max-width: 300px;">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="userSearch" class="form-control border-start-0 ps-0" placeholder="Buscar usuario..." autocomplete="off">
                    </div>
                    <a href="<?php echo URLROOT; ?>/users/create" class="btn btn-primary shadow-sm px-3 d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus-fill"></i> <span class="d-none d-md-inline">Nuevo</span>
                    </a>
                </div>
            </div>

            <!-- Users Table Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="usersTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-4 py-3 text-secondary text-uppercase small fw-bold">Usuario</th>
                                    <th class="border-0 px-4 py-3 text-secondary text-uppercase small fw-bold">Rol</th>
                                    <th class="border-0 px-4 py-3 text-secondary text-uppercase small fw-bold text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['users'] as $user): ?>
                                <tr class="border-bottom-custom">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="position-relative">
                                                <?php 
                                                $hasImage = false;
                                                $imgSrc = '';
                                                if(!empty($user['profile_image']) && $user['profile_image'] !== 'default.png'){
                                                    $path = 'public/uploads/users/' . $user['profile_image'];
                                                    if(file_exists($path)){
                                                        $imgSrc = URLROOT . '/' . $path;
                                                        $hasImage = true;
                                                    }
                                                }
                                                
                                                if($hasImage): 
                                                ?>
                                                    <img src="<?php echo $imgSrc; ?>" class="rounded-circle border shadow-sm" style="width: 45px; height: 45px; min-width: 45px; object-fit: cover; aspect-ratio: 1/1;">
                                                <?php else: 
                                                    // Generate a consistent color based on user's name
                                                    $idx = hexdec(substr(md5($user['full_name']), 0, 6));
                                                    // Array of pastel/soft material colors
                                                    $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#6366f1', '#14b8a6'];
                                                    $bgColor = $colors[$idx % count($colors)];
                                                    $initial = strtoupper(mb_substr($user['full_name'], 0, 1));
                                                ?>
                                                    <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center text-white fw-bold border border-white" 
                                                         style="width: 45px; height: 45px; min-width: 45px; font-size: 1.2rem; background-color: <?php echo $bgColor; ?>;">
                                                        <?php echo $initial; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="position-absolute bottom-0 end-0 p-1 bg-<?php echo ($user['role'] == 'admin') ? 'danger' : (($user['role'] == 'librarian') ? 'warning' : 'success'); ?> border border-2 border-white rounded-circle"></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark user-name"><?php echo $user['full_name']; ?></h6>
                                                <small class="text-muted d-block user-email">@<?php echo $user['username']; ?> &bull; <?php echo $user['email']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge rounded-pill px-3 py-2 fw-normal user-role-badge" 
                                              style="background-color: rgba(<?php echo ($user['role'] == 'admin') ? '239, 68, 68' : (($user['role'] == 'librarian') ? '245, 158, 11' : '16, 185, 129'); ?>, 0.1); 
                                                     color: <?php echo ($user['role'] == 'admin') ? 'var(--danger-color)' : (($user['role'] == 'librarian') ? 'var(--warning-color)' : 'var(--success-color)'); ?>;">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a href="<?php echo URLROOT; ?>/users/edit/<?php echo $user['id']; ?>" class="btn btn-outline-primary btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center p-0" style="width: 32px; height: 32px; flex-shrink: 0;" title="Editar">
                                                <i class="bi bi-pencil-fill" style="font-size: 0.85rem;"></i>
                                            </a>
                                            <?php if($user['username'] !== 'admin' && $user['username'] !== 'Admin'): ?>
                                            <form action="<?php echo URLROOT; ?>/users/delete/<?php echo $user['id']; ?>" method="post" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar a este usuario?');">
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center p-0" style="width: 32px; height: 32px; flex-shrink: 0;" title="Eliminar">
                                                    <i class="bi bi-trash-fill" style="font-size: 0.85rem;"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <!-- Empty State (Hidden by default) -->
                        <div id="noResults" class="text-center py-5 d-none">
                            <i class="bi bi-search text-muted display-6 mb-3"></i>
                            <h5 class="text-muted">No se encontraron usuarios</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Table Styling */
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.015);
}
.border-bottom-custom {
    border-bottom: 1px solid #f0f0f0;
}
.border-bottom-custom:last-child {
    border-bottom: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearch');
    const table = document.getElementById('usersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    const noResults = document.getElementById('noResults');

    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        let visibleCount = 0;

        for (let i = 0; i < rows.length; i++) {
            const nameCol = rows[i].querySelector('.user-name');
            const emailCol = rows[i].querySelector('.user-email');
            
            if (nameCol || emailCol) {
                const nameText = nameCol.textContent || nameCol.innerText;
                const emailText = emailCol.textContent || emailCol.innerText;
                
                if (nameText.toLowerCase().indexOf(filter) > -1 || emailText.toLowerCase().indexOf(filter) > -1) {
                    rows[i].style.display = "";
                    visibleCount++;
                } else {
                    rows[i].style.display = "none";
                }
            }
        }

        if (visibleCount === 0) {
            noResults.classList.remove('d-none');
            table.classList.add('d-none');
        } else {
            noResults.classList.add('d-none');
            table.classList.remove('d-none');
        }
    });
});
</script>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
