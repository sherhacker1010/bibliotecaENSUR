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
            <h1 class="h3 mb-4 text-gray-800">Crear Usuario</h1>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Usuario</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/users/create" method="post" enctype="multipart/form-data" id="createUserForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Usuario</label>
                                    <input type="text" name="username" class="form-control <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['username']; ?>">
                                    <span class="invalid-feedback"><?php echo $data['username_err']; ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Contraseña</label>
                                    <input type="password" name="password" class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
                                    <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nombre Completo</label>
                                    <input type="text" name="full_name" class="form-control" value="<?php echo $data['full_name']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo $data['email']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Rol</label>
                                    <select name="role" class="form-select">
                                        <option value="reader" <?php echo ($data['role'] == 'reader') ? 'selected' : ''; ?>>Lector</option>
                                        <option value="librarian" <?php echo ($data['role'] == 'librarian') ? 'selected' : ''; ?>>Bibliotecario</option>
                                        <option value="admin" <?php echo ($data['role'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Foto de Perfil</label>
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <div id="camera-container" class="mb-2" style="display:none;">
                                            <video id="video" width="100%" height="auto" autoplay></video>
                                            <canvas id="canvas" style="display:none;"></canvas>
                                        </div>
                                        <div id="preview-container" class="mb-2">
                                            <img id="photo-preview" src="<?php echo URLROOT; ?>/public/assets/img/default-user.png" class="img-thumbnail" style="max-width: 200px; display:none;">
                                        </div>
                                        
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary" id="start-camera"><i class="bi bi-camera"></i> Usar Cámara</button>
                                            <button type="button" class="btn btn-outline-success" id="take-photo" style="display:none;"><i class="bi bi-camera-fill"></i> Capturar</button>
                                            <button type="button" class="btn btn-outline-secondary" id="upload-photo-btn"><i class="bi bi-upload"></i> Subir Archivo</button>
                                        </div>
                                        
                                        <input type="file" name="photo_file" id="photo_file" class="form-control mt-2" style="display:none;" accept="image/*">
                                        <input type="hidden" name="photo_base64" id="photo_base64">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary shadow-sm w-100">
                                <i class="bi bi-save"></i> Guardar Usuario
                            </button>
                            <a href="<?php echo URLROOT; ?>/users/index" class="btn btn-secondary shadow-sm mt-2 w-100">Cancelar</a>
                        </div>
                    </form>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const startButton = document.getElementById('start-camera');
    const captureButton = document.getElementById('take-photo');
    const uploadButton = document.getElementById('upload-photo-btn');
    const fileInput = document.getElementById('photo_file');
    const photoPreview = document.getElementById('photo-preview');
    const photoBase64 = document.getElementById('photo_base64');
    const cameraContainer = document.getElementById('camera-container');

    startButton.addEventListener('click', async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            video.srcObject = stream;
            cameraContainer.style.display = 'block';
            captureButton.style.display = 'inline-block';
            startButton.style.display = 'none';
            photoPreview.style.display = 'none';
        } catch (err) {
            alert("Error al acceder a la cámara: " + err);
        }
    });

    captureButton.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const dataURL = canvas.toDataURL('image/png');
        photoBase64.value = dataURL;
        photoPreview.src = dataURL;
        photoPreview.style.display = 'block';
        
        // Stop stream
        const stream = video.srcObject;
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        video.srcObject = null;
        
        cameraContainer.style.display = 'none';
        captureButton.style.display = 'none';
        startButton.innerText = "Retomar Foto";
        startButton.style.display = 'inline-block';
    });

    uploadButton.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                photoPreview.src = e.target.result;
                photoPreview.style.display = 'block';
                photoBase64.value = ''; // Clear base64 if uploading file
            }
            reader.readAsDataURL(file);
        }
    });
</script>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
