<div class="sidebar d-flex flex-column flex-shrink-0 p-3 text-white">
    <a href="<?php echo URLROOT; ?>/dashboard/index" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none p-2">
        <i class="bi bi-book-half fs-4 me-2"></i>
        <span class="fs-4 fw-bold">Biblioteca ENSUR</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="<?php echo URLROOT; ?>/dashboard/index" class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'dashboard') !== false) ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </li>
        
        <?php if(AuthHelper::hasRole('admin')): ?>
        <li>
            <a href="<?php echo URLROOT; ?>/users/index" class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'users') !== false) ? 'active' : ''; ?>">
                <i class="bi bi-people"></i>
                Usuarios
            </a>
        </li>
        <?php endif; ?>

        <?php if(AuthHelper::hasRole('admin') || AuthHelper::hasRole('librarian')): ?>
        <li>
            <a href="<?php echo URLROOT; ?>/books/index" class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'books') !== false) ? 'active' : ''; ?>">
                <i class="bi bi-book"></i>
                Libros
            </a>
        </li>
        <li>
            <a href="<?php echo URLROOT; ?>/shelves/index" class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'shelves') !== false) ? 'active' : ''; ?>">
                <i class="bi bi-archive"></i>
                Estanterías
            </a>
        </li>
        <li>
            <a href="<?php echo URLROOT; ?>/loans/index" class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'loans') !== false) ? 'active' : ''; ?>">
                <i class="bi bi-journal-arrow-up"></i>
                Préstamos
            </a>
        </li>
        <li>
            <a href="<?php echo URLROOT; ?>/loans/returnPage" class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'loans/returnPage') !== false) ? 'active' : ''; ?>">
                <i class="bi bi-journal-arrow-down"></i>
                Devoluciones
            </a>
        </li>
        <li>
            <a href="<?php echo URLROOT; ?>/reports/index" class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'reports') !== false) ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-bar-graph"></i>
                Reportes
            </a>
        </li>
        <?php endif; ?>
        
        <?php if(AuthHelper::hasRole('admin')): ?>
        <li>
            <a href="<?php echo URLROOT; ?>/settings/index" class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'settings') !== false) ? 'active' : ''; ?>">
                <i class="bi bi-gear"></i>
                Configuración
            </a>
        </li>
        <?php endif; ?>
        
        <li>
            <a href="<?php echo URLROOT; ?>/catalog/index" class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'catalog') !== false) ? 'active' : ''; ?>">
                <i class="bi bi-search"></i>
                Catálogo
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="rounded-circle bg-primary d-flex justify-content-center align-items-center me-2" style="width: 32px; height: 32px;">
                <i class="bi bi-person-fill"></i>
            </div>
            <strong><?php echo $_SESSION['user_name'] ?? 'Usuario'; ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <!-- <li><a class="dropdown-item" href="#">Profile</a></li> -->
            <!-- <li><hr class="dropdown-divider"></li> -->
            <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/auth/logout">Cerrar Sesión</a></li>
        </ul>
    </div>
</div>
