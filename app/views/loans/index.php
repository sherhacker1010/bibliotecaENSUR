<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="">
         <?php require APPROOT . '/views/layouts/sidebar.php'; ?>
    </div>

    <!-- Content -->
    <div class="flex-grow-1" style="background-color: #f3f4f6;">
        <?php require APPROOT . '/views/layouts/navbar.php'; ?>
        
        <div class="container-fluid px-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Préstamos Activos</h1>
                <a href="<?php echo URLROOT; ?>/loans/create" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                    <i class="bi bi-journal-plus text-white-50"></i> Nuevo Préstamo
                </a>
            </div>

            <!-- Mobile: Create Loan FAB -->
            <div class="fab-container d-lg-none">
                <a href="<?php echo URLROOT; ?>/loans/create" class="fab-btn">
                    <i class="bi bi-plus-lg"></i>
                </a>
            </div>

            <!-- Unified Responsive Grid -->
            <div class="row g-3">
                <?php foreach($data['loans'] as $loan): ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-soft loan-card position-relative overflow-hidden">
                        <!-- Status Strip -->
                        <div class="position-absolute top-0 start-0 h-100" style="width: 6px; background-color: <?php echo (date('Y-m-d') > $loan['due_date']) ? 'var(--danger-color)' : 'var(--success-color)'; ?>;"></div>
                        
                        <div class="card-body ps-4">
                            <!-- Header: Book Info with Cover -->
                            <div class="d-flex align-items-center mb-3">
                                <?php 
                                    // Base placeholder
                                    $placeholder = "https://placehold.co/40x60?text=No+Cover";
                                    $bookImg = $placeholder;

                                    if(!empty($loan['cover_image'])){
                                        // Image path is relative to the project root (index.php), not public
                                        $bookImg = URLROOT . '/uploads/covers/' . $loan['cover_image'];
                                    } 
                                ?>
                                <img src="<?php echo $bookImg; ?>" 
                                     onerror="this.onerror=null; this.src='<?php echo $placeholder; ?>';"
                                     class="rounded shadow-sm me-3" 
                                     style="width: 40px; height: 60px; object-fit: cover;">
                                <div>
                                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1rem;">
                                        <?php echo $loan['book_title']; ?>
                                    </h5>
                                    <span class="badge bg-light text-muted border"><?php echo $loan['unique_code']; ?></span>
                                </div>
                            </div>

                            <!-- User Info -->
                            <div class="d-flex align-items-center mb-3 p-2 rounded-3" style="background-color: var(--light-color);">
                                <?php 
                                    $imgSrc = (!empty($loan['user_image']) && $loan['user_image'] != 'default.png' && file_exists(dirname(APPROOT) . '/public/uploads/users/' . $loan['user_image'])) 
                                        ? URLROOT . '/public/uploads/users/' . $loan['user_image'] 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($loan['user_name']) . '&background=random';
                                ?>
                                <img src="<?php echo $imgSrc; ?>" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?php echo $loan['user_name']; ?></div>
                                    <div class="small text-muted">@<?php echo $loan['user_username']; ?></div>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="row mb-4">
                                <div class="col-6">
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem;">Prestado</small>
                                    <div class="fw-bold text-dark"><?php echo date('d M', strtotime($loan['loan_date'])); ?></div>
                                </div>
                                <div class="col-6">
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem;">Vence</small>
                                    <div class="fw-bold <?php echo (date('Y-m-d') > $loan['due_date']) ? 'text-danger' : 'text-success'; ?>">
                                        <?php echo date('d M', strtotime($loan['due_date'])); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center">
                                <?php if(date('Y-m-d') > $loan['due_date']): ?>
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3">Vencido</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3">Activo</span>
                                <?php endif; ?>

                                <a href="<?php echo URLROOT; ?>/loans/returnPage" class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm text-dark fw-bold">
                                    <i class="bi bi-arrow-return-left me-1"></i> Devolver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if(empty($data['loans'])): ?>
                <div class="col-12">
                    <div class="alert alert-light text-center shadow-sm border-0 py-5">
                        <i class="bi bi-journal-check fs-1 text-muted mb-3 d-block"></i>
                        <h4 class="text-muted">No hay préstamos activos</h4>
                        <p class="text-muted small">Los libros prestados aparecerán aquí.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
