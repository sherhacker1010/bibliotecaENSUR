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
            <h1 class="h3 mb-4 text-gray-800">Devolución de Libros</h1>

            <?php if(!empty($data['error'])): ?>
                <div class="alert alert-danger shadow-sm border-left-danger"><?php echo $data['error']; ?></div>
            <?php endif; ?>
            <?php if(!empty($data['success'])): ?>
                <div class="alert alert-success shadow-sm border-left-success"><?php echo $data['success']; ?></div>
            <?php endif; ?>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning text-dark">Proceso de Devolución</h6>
                        </div>
                        <div class="card-body">
                            
                            <?php if(empty($data['loan'])): ?>
                            <!-- Step 1: Scan -->
                            <form action="<?php echo URLROOT; ?>/loans/returnPage" method="post">
                                <div class="mb-4 text-center">
                                    <i class="bi bi-journal-arrow-down display-1 text-gray-300"></i>
                                    <p class="text-muted mt-2">Escanee el código QR del libro o ingréselo manualmente para iniciar la devolución.</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Código del Libro (QR)</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text"><i class="bi bi-qr-code"></i></span>
                                        <input type="text" name="copy_code" id="copy_code" class="form-control" placeholder="Ej: LIB-001-C1" required autofocus>
                                        <button type="button" class="btn btn-primary" id="openScanner"><i class="bi bi-camera"></i> Escanear</button>
                                    </div>
                                </div>

                                <!-- Scanner Container -->
                                <div id="reader" style="width: 100%; display:none;" class="mb-3 border rounded"></div>

                                <button type="submit" class="btn btn-warning btn-lg w-100 shadow-sm text-dark font-weight-bold">
                                    <i class="bi bi-search"></i> Buscar Préstamo
                                </button>
                            </form>
                            
                            <?php else: ?>
                            <!-- Step 2: Confirm -->
                            <div class="alert alert-info border-left-info shadow-sm">
                                <i class="bi bi-info-circle-fill me-2"></i> Por favor verifique los datos antes de confirmar.
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="font-weight-bold text-dark"><?php echo $data['loan']['title']; ?></h5>
                                    <p class="text-muted mb-0">Código: <?php echo $data['loan']['unique_code']; ?></p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <h5 class="text-primary font-weight-bold"><?php echo $data['loan']['full_name']; ?></h5>
                                    <p class="text-muted mb-0">Lector</p>
                                </div>
                            </div>

                            <div class="table-responsive mb-4">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Fecha Vencimiento</th>
                                            <th>Días de Retraso</th>
                                            <th>Multa Estimada</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="align-middle"><?php echo $data['loan']['due_date']; ?></td>
                                            <td class="align-middle <?php echo ($data['days_over'] > 0) ? 'text-danger font-weight-bold' : 'text-success'; ?>">
                                                <?php echo $data['days_over']; ?> días
                                            </td>
                                            <td class="align-middle font-weight-bold text-dark">
                                                $<?php echo number_format($data['fine'] ?? 0, 2); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <form action="<?php echo URLROOT; ?>/loans/returnPage" method="post">
                                <input type="hidden" name="copy_code" value="<?php echo $data['loan']['unique_code'] ?? ($_POST['copy_code'] ?? ''); ?>">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="<?php echo URLROOT; ?>/loans/returnPage" class="btn btn-secondary me-md-2">Cancelar</a>
                                    <button type="submit" name="confirm_return" class="btn btn-success btn-lg shadow-sm">
                                        <i class="bi bi-check-lg"></i> Confirmar Devolución
                                    </button>
                                </div>
                            </form>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/public/assets/libs/html5-qrcode-master/minified/html5-qrcode.min.js"></script>
<script>
    if(document.getElementById('openScanner')){
        const html5QrCode = new Html5Qrcode("reader");
        const qrConfig = { fps: 10, qrbox: { width: 250, height: 250 } };
        
        document.getElementById('openScanner').addEventListener('click', () => {
            const readerDiv = document.getElementById('reader');
            if (readerDiv.style.display === 'none') {
                 readerDiv.style.display = 'block';
                 html5QrCode.start({ facingMode: "environment" }, qrConfig, onScanSuccess, onScanFailure)
                .catch(err => {
                    console.log("Error starting scanner", err);
                    alert("Error al iniciar cámara: " + err);
                });
            } else {
                html5QrCode.stop().then(() => {
                    readerDiv.style.display = 'none';
                }).catch(err => console.log(err));
            }
        });

        function onScanSuccess(decodedText, decodedResult) {
            document.getElementById('copy_code').value = decodedText;
            html5QrCode.stop().then(() => {
                document.getElementById('reader').style.display = 'none';
            });
        }

        function onScanFailure(error) {}
    }
</script>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
