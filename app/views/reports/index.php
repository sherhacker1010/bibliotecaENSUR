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
            <h1 class="h3 mb-4 text-gray-800">Generador de Reportes</h1>
            
            <div class="row">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Seleccionar Reporte</h6>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo URLROOT; ?>/reports/generate" method="post" target="_blank">
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Tipo de Reporte</label>
                                    <select name="type" class="form-select">
                                        <option value="books">Inventario de Libros</option>
                                        <option value="loans">Préstamos Activos y Vencidos</option>
                                        <!-- Add more report types here in future -->
                                    </select>
                                    <small class="text-muted">Seleccione el tipo de datos que desea visualizar o exportar.</small>
                                </div>
                                
                                <hr>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" name="view" class="btn btn-primary btn-lg shadow-sm">
                                        <i class="bi bi-file-earmark-pdf"></i> Ver Reporte / Imprimir PDF
                                    </button>
                                    <button type="submit" name="export_excel" class="btn btn-success btn-lg shadow-sm">
                                        <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-info">Información</h6>
                        </div>
                        <div class="card-body">
                            <p>Utilice esta sección para generar reportes del estado actual de la biblioteca.</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><i class="bi bi-book me-2 text-primary"></i> <strong>Inventario de Libros:</strong> Lista todos los libros, su stock y ubicación.</li>
                                <li class="list-group-item"><i class="bi bi-clock-history me-2 text-warning"></i> <strong>Préstamos:</strong> Muestra quién tiene libros prestados y fechas de vencimiento.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">Imprimir Códigos QR</h6>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo URLROOT; ?>/reports/qr_codes" method="post" target="_blank">
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Alcance</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="scope" id="scope_all" value="all" checked onchange="toggleBookSelect(false)">
                                        <label class="form-check-label" for="scope_all">
                                            Todos los Libros
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="scope" id="scope_single" value="single" onchange="toggleBookSelect(true)">
                                        <label class="form-check-label" for="scope_single">
                                            Libro Individual
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3" id="book_select_container" style="display:none;">
                                    <label class="form-label">Seleccionar Libro</label>
                                    <select name="book_id" class="form-select">
                                        <option value="">-- Seleccione un libro --</option>
                                        <?php if(!empty($data['books'])): ?>
                                            <?php foreach($data['books'] as $book): ?>
                                                <option value="<?php echo $book['id']; ?>"><?php echo $book['title']; ?> (<?php echo $book['code']; ?>)</option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm">
                                    <i class="bi bi-qr-code"></i> Generar QRs para Imprimir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            function toggleBookSelect(show) {
                document.getElementById('book_select_container').style.display = show ? 'block' : 'none';
            }
            </script>
            
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
