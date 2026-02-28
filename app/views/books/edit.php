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
            <h1 class="h3 mb-4 text-gray-800">Editar Libro</h1>
            
            <form action="<?php echo URLROOT; ?>/books/edit/<?php echo $data['id']; ?>" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-8">
                         <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Detalles del Libro</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Código</label>
                                        <input type="text" name="code" class="form-control <?php echo (!empty($data['code_err'])) ?? ''; ?>" value="<?php echo $data['code'] ?? ''; ?>" readonly required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Título</label>
                                        <input type="text" name="title" class="form-control" value="<?php echo $data['title'] ?? ''; ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Autor</label>
                                        <input type="text" name="author" class="form-control" value="<?php echo $data['author'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Género</label>
                                        <input type="text" name="genre" class="form-control" value="<?php echo $data['genre'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Estantería</label>
                                    <select name="shelf_id" class="form-select">
                                        <option value="">Seleccione...</option>
                                        <?php if(!empty($data['shelves'])): ?>
                                            <?php foreach($data['shelves'] as $shelf): ?>
                                                <option value="<?php echo $shelf['id']; ?>" <?php echo (($data['shelf_id'] ?? '') == $shelf['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $shelf['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stock Total</label>
                                    <input type="number" name="stock" class="form-control" min="1" value="<?php echo $data['stock'] ?? '1'; ?>" required>
                                    <small class="text-muted">Si aumenta el stock, se generarán nuevas copias. Si disminuye, asegúrese de que no haya préstamos activos.</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="description" id="summernote" class="form-control"><?php echo $data['description'] ?? ''; ?></textarea>
                                </div>
                            </div>
                         </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Portada</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 text-center">
                                    <?php if(!empty($data['current_cover'])): ?>
                                        <img src="<?php echo URLROOT; ?>/uploads/covers/<?php echo $data['current_cover']; ?>" class="img-fluid rounded mb-3" style="max-height: 200px;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center border rounded mb-3" style="height: 200px;">
                                            <i class="bi bi-image fs-1 text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                                    <small class="text-muted">Dejar vacío para mantener la actual.</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary btn-block w-100 mb-2 shadow-sm">
                                    <i class="bi bi-save"></i> Actualizar Libro
                                </button>
                                <a href="<?php echo URLROOT; ?>/books/index" class="btn btn-secondary btn-block w-100 shadow-sm">Cancelar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Activate Summernote -->
<script>
    document.addEventListener("DOMContentLoaded", function(){
        if(typeof $ !== 'undefined') {
            $('#summernote').summernote({
                placeholder: 'Descripción del libro...',
                tabsize: 2,
                height: 200
            });
        }
    });
</script>

<link href="<?php echo URLROOT; ?>/assets/libs/summernote/summernote-bs5.css" rel="stylesheet">
<script src="<?php echo URLROOT; ?>/assets/libs/summernote/summernote-bs5.js"></script>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
