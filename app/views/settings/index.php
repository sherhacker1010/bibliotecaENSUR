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
            <h1 class="h3 mb-4 text-gray-800">Configuración del Sistema</h1>

            <?php if(!empty($data['success'])): ?>
                <div class="alert alert-success shadow-sm border-left-success"><?php echo $data['success']; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Parámetros Generales</h6>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo URLROOT; ?>/settings/index" method="post">
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Días de Préstamo (Default)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-day"></i></span>
                                        <input type="number" name="loan_days" class="form-control" value="<?php echo $data['settings']['loan_days'] ?? 3; ?>" required>
                                    </div>
                                    <small class="text-muted">Días que dura un préstamo por defecto.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Multa por día ($ COP)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                        <input type="number" name="fine_per_day" class="form-control" value="<?php echo $data['settings']['fine_per_day'] ?? 1000; ?>" required>
                                    </div>
                                    <small class="text-muted">Valor a cobrar por cada día de retraso.</small>
                                </div>
                                
                                 <div class="mb-3">
                                    <label class="form-label font-weight-bold">Máximo de Libros por Usuario</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-files"></i></span>
                                        <input type="number" name="max_books" class="form-control" value="<?php echo $data['settings']['max_books'] ?? 1; ?>" required>
                                    </div>
                                    <small class="text-muted">Cuántos libros puede tener prestados simultáneamente.</small>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                    <i class="bi bi-save"></i> Guardar Configuración
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
