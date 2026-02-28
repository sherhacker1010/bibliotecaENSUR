<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="d-flex">
    <!-- SidebarWrapper -->
    <div class="">
         <?php require APPROOT . '/views/layouts/sidebar.php'; ?>
    </div>

    <!-- Page Content Wrapper -->
    <div class="flex-grow-1" style="background-color: #f3f4f6;">
        <?php require APPROOT . '/views/layouts/navbar.php'; ?>
        
        <div class="container-fluid px-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                <!-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bi bi-download"></i> Generate Report</a> -->
            </div>

            <!-- Content Row -->
            <div class="row g-4 mb-4">
                <!-- Books Card -->
                <div class="col-xl-4 col-md-6">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Libros (Títulos)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $data['stats']['books']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-book fa-2x text-gray-300 fs-1 text-black-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loans Card -->
                <div class="col-xl-4 col-md-6">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Préstamos Activos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $data['stats']['loans']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-journal-check fs-1 text-black-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overdue Card -->
                <div class="col-xl-4 col-md-6">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Vencidos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $data['stats']['overdue']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-exclamation-circle fs-1 text-black-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Row -->
            <?php if(!empty($data['notifications'])): ?>
            <div class="row mb-4">
                <div class="col-12">
                     <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Notificaciones</h6>
                        </div>
                        <div class="card-body">
                             <?php foreach($data['notifications'] as $notif): ?>
                                <div class="alert alert-<?php echo $notif['type']; ?> shadow-sm border-left-<?php echo $notif['type']; ?>" role="alert">
                                    <i class="bi bi-bell-fill me-2"></i> <?php echo $notif['msg']; ?>
                                </div>
                             <?php endforeach; ?>
                        </div>
                     </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if(AuthHelper::hasRole('reader')): ?>
            <div class="card shadow mb-4">
                 <div class="card-body">
                    <h5 class="card-title text-primary">Bienvenido, lector</h5>
                    <p class="card-text">Desde aquí puedes consultar tu historial de préstamos, ver el catálogo y estar al tanto de tus fechas de devolución.</p>
                    <a href="<?php echo URLROOT; ?>/catalog/index" class="btn btn-outline-primary"><i class="bi bi-search"></i> Ir al Catálogo</a>
                 </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
