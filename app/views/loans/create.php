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
            <h1 class="h3 mb-4 text-gray-800">Registrar Préstamo</h1>

            <?php if(!empty($data['error'])): ?>
                <div class="alert alert-danger shadow-sm border-left-danger"><?php echo $data['error']; ?></div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                             <h6 class="m-0 font-weight-bold text-success">Nuevo Préstamo</h6>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo URLROOT; ?>/loans/create" method="post">
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Usuario (Lector)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" id="user_autocomplete" class="form-control" placeholder="Escriba el nombre completo del lector" required>
                                        <input type="hidden" name="username" id="username" value="<?php echo $data['username']; ?>">
                                    </div>
                                    <small class="text-muted">Busque por nombre completo. El sistema seleccionará el usuario.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Código del Libro (QR)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" name="copy_code" id="copy_code" class="form-control" placeholder="Escanee o escriba el código" value="<?php echo $data['copy_code']; ?>" required>
                                        <button type="button" class="btn btn-outline-primary" id="openScanner"><i class="bi bi-camera-video"></i> Live</button>
                                        <button type="button" class="btn btn-outline-secondary" id="openCameraUpload"><i class="bi bi-camera"></i> Foto</button>
                                        <button type="button" class="btn btn-outline-danger d-none" id="clearCode" title="Limpiar código"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                    <input type="file" id="qr-input-file" accept="image/*" capture="environment" hidden>
                                </div>

                                <!-- Scanner Container -->
                                <div id="reader" style="width: 100%; display:none;" class="mb-3 border rounded"></div>
                                <div id="upload-status" class="text-secondary small mt-1" style="display:none;">Procesando imagen...</div>

                                <!-- Book Preview Card (shown after scan) -->
                                <div id="book-preview" class="mb-3" style="display:none;">
                                    <div class="card border-0 shadow-soft overflow-hidden">
                                        <div class="d-flex align-items-center p-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);">
                                            <div id="preview-cover" class="me-3 flex-shrink-0">
                                                <!-- Cover image injected by JS -->
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-dark fs-5" id="preview-title"></div>
                                                <div class="text-muted small" id="preview-author"></div>
                                                <div class="mt-2">
                                                    <span class="badge rounded-pill bg-light text-muted border" id="preview-shelf"></span>
                                                    <span class="badge rounded-pill" id="preview-status"></span>
                                                </div>
                                            </div>
                                            <div class="ms-2">
                                                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm">
                                    <i class="bi bi-check-circle"></i> Registrar Préstamo
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-info">Instrucciones</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><i class="bi bi-1-circle me-2"></i> Verifique que el usuario esté registrado y no tenga sanciones.</li>
                                <li class="list-group-item"><i class="bi bi-2-circle me-2"></i> <strong>Opción Live:</strong> Escaneo en tiempo real (requiere HTTPS/SSL).</li>
                                <li class="list-group-item"><i class="bi bi-3-circle me-2"></i> <strong>Opción Foto:</strong> Tome una foto al QR si el modo Live falla.</li>
                                <li class="list-group-item"><i class="bi bi-4-circle me-2"></i> <strong>Manual:</strong> Ingrese el código manualmente (Ej: LIB-001-C1).</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/public/assets/libs/html5-qrcode-master/minified/html5-qrcode.min.js"></script>
<script>
    const html5QrCode = new Html5Qrcode("reader");
    const qrConfig = { fps: 10, qrbox: { width: 250, height: 250 } };
    
    // LIVE SCANNER MODE
    document.getElementById('openScanner').addEventListener('click', () => {
        const readerDiv = document.getElementById('reader');
        if (readerDiv.style.display === 'none') {
             readerDiv.style.display = 'block';
             // Stop any potential previous instance
             if(html5QrCode.isScanning) {
                 html5QrCode.stop().then(startLiveScan).catch(err => startLiveScan());
             } else {
                 startLiveScan();
             }
        } else {
            stopScanner();
        }
    });

    function startLiveScan(){
        html5QrCode.start({ facingMode: "environment" }, qrConfig, onScanSuccess, onScanFailure)
        .catch(err => {
            console.log("Error starting scanner", err);
            alert("No se pudo iniciar la cámara en vivo (Posible falta de SSL). Intente usar el botón 'Foto'.");
            document.getElementById('reader').style.display = 'none';
        });
    }

    function stopScanner(){
        html5QrCode.stop().then(() => {
            document.getElementById('reader').style.display = 'none';
        }).catch(err => {
            console.log(err);
            document.getElementById('reader').style.display = 'none';
        });
    }

    // FILE UPLOAD MODE (Backup for no SSL)
    document.getElementById('openCameraUpload').addEventListener('click', () => {
        // Stop live scanner if active to free resources
        if(html5QrCode.isScanning){
            stopScanner();
        }
        document.getElementById('qr-input-file').value = ''; // Reset input
        document.getElementById('qr-input-file').click();
    });

    document.getElementById('qr-input-file').addEventListener('change', e => {
        if (e.target.files.length == 0) return;
        
        const imageFile = e.target.files[0];
        const statusDiv = document.getElementById('upload-status');
        statusDiv.style.display = 'block';
        statusDiv.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando imagen...';

        // Resize image to avoid memory issues on mobile with large photos (12MP+)
        resizeImage(imageFile, 800).then(resizedBlob => {
            const resizedFile = new File([resizedBlob], "qr_resized.jpg", { type: "image/jpeg" });
            
            // Scan resized file
            html5QrCode.scanFile(resizedFile, false)
            .then(decodedText => {
                onScanSuccess(decodedText, null);
                statusDiv.style.display = 'none';
            })
            .catch(err => {
                console.error("Error scanning file", err);
                statusDiv.style.display = 'block';
                statusDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> No se encontró QR. <br><small>Intente enfocar mejor o acercarse más.</small></span>';
            });
        }).catch(err => {
            console.error("Error resizing", err);
            statusDiv.innerHTML = '<span class="text-danger">Error al procesar la imagen.</span>';
        });
    });

    // Helper to resize image
    function resizeImage(file, maxWidth) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    let width = img.width;
                    let height = img.height;
                    
                    if (width > maxWidth) {
                        height = Math.round(height * (maxWidth / width));
                        width = maxWidth;
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    canvas.toBlob(blob => {
                        resolve(blob);
                    }, 'image/jpeg', 0.9);
                };
                img.onerror = reject;
                img.src = event.target.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        const codeInput = document.getElementById('copy_code');
        codeInput.value = decodedText;
        
        // Lock the field
        codeInput.readOnly = true;
        codeInput.classList.add('bg-light');
        document.getElementById('clearCode').classList.remove('d-none');

        // Visual feedback
        const statusDiv = document.getElementById('upload-status');
        statusDiv.style.display = 'block';
        statusDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> ¡Código detectado! Buscando libro...</span>';

        if(html5QrCode.isScanning){
            stopScanner();
        }

        // Fetch book info from API
        fetchBookPreview(decodedText);
    }

    function fetchBookPreview(code) {
        const URLROOT = '<?php echo URLROOT; ?>';
        fetch(URLROOT + '/api/get_book_by_code?code=' + encodeURIComponent(code))
        .then(res => res.json())
        .then(data => {
            const statusDiv = document.getElementById('upload-status');
            if(data.status === 'success') {
                const book = data.data;
                // Populate preview
                document.getElementById('preview-title').textContent = book.title;
                document.getElementById('preview-author').textContent = book.author;
                document.getElementById('preview-shelf').textContent = book.shelf_name || 'Sin Estantería';
                
                // Status badge
                const statusBadge = document.getElementById('preview-status');
                if(book.status === 'available'){
                    statusBadge.className = 'badge rounded-pill bg-success bg-opacity-10 text-success';
                    statusBadge.textContent = 'Disponible';
                } else {
                    statusBadge.className = 'badge rounded-pill bg-danger bg-opacity-10 text-danger';
                    statusBadge.textContent = 'No Disponible';
                }

                // Cover image
                const coverDiv = document.getElementById('preview-cover');
                if(book.cover_image){
                    coverDiv.innerHTML = '<img src="' + URLROOT + '/uploads/covers/' + book.cover_image + '" class="rounded-3 shadow-sm" style="width: 70px; height: 95px; object-fit: cover;" alt="Portada">';
                } else {
                    coverDiv.innerHTML = '<div class="d-flex align-items-center justify-content-center bg-light rounded-3 border" style="width: 70px; height: 95px;"><i class="bi bi-book text-muted fs-3"></i></div>';
                }

                document.getElementById('book-preview').style.display = 'block';
                statusDiv.style.display = 'none';
            } else {
                statusDiv.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Código no encontrado en la base de datos.</span>';
            }
        })
        .catch(err => {
            console.error('Error fetching book', err);
            document.getElementById('upload-status').innerHTML = '<span class="text-danger"><i class="bi bi-wifi-off"></i> Error de conexión al buscar libro.</span>';
        });
    }

    // Clear / Reset code
    document.getElementById('clearCode').addEventListener('click', () => {
        const codeInput = document.getElementById('copy_code');
        codeInput.value = '';
        codeInput.readOnly = false;
        codeInput.classList.remove('bg-light');
        codeInput.focus();
        document.getElementById('clearCode').classList.add('d-none');
        document.getElementById('book-preview').style.display = 'none';
        document.getElementById('upload-status').style.display = 'none';
    });

    function onScanFailure(error) {
        // console.warn(`Code scan error = ${error}`);
    }
</script>



<?php require APPROOT . '/views/layouts/footer.php'; ?>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    $(function() {
        $("#user_autocomplete").autocomplete({
            source: "<?php echo URLROOT; ?>/api/search_users",
            minLength: 2,
            select: function(event, ui) {
                // Set the hidden input value to the username
                $("#username").val(ui.item.value);
                // Set the visible input to the full label
                $(this).val(ui.item.label);
                return false; // Prevent default behavior of setting value to just 'value'
            }
        });
    });
</script>
