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
            <h1 class="h3 mb-4 text-gray-800">Catálogo de Libros</h1>

            <!-- Search Bar -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group shadow-sm">
                        <input type="text" id="searchCatalog" class="form-control border-0" placeholder="Buscar por título o autor...">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4" id="catalogGrid">
                <?php foreach($data['books'] as $book): ?>
                <div class="col catalog-item">
                    <div class="card h-100 shadow-sm border-0 hover-lift">
                        <?php if($book['cover_image']): ?>
                            <img src="<?php echo URLROOT; ?>/uploads/covers/<?php echo $book['cover_image']; ?>" class="card-img-top" alt="Cover" style="height: 250px; object-fit: cover;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-gray-200" style="height: 250px; background-color: #eaecf4;">
                                <i class="bi bi-book display-4 text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h6 class="card-title font-weight-bold text-dark text-truncate" title="<?php echo $book['title']; ?>"><?php echo $book['title']; ?></h6>
                            <p class="card-text text-muted small mb-1"><i class="bi bi-person"></i> <?php echo $book['author']; ?></p>
                            <span class="badge bg-light text-dark border"><?php echo $book['genre']; ?></span>
                        </div>
                        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
                            <?php if($book['stock'] > 0): ?>
                                <span class="badge bg-success rounded-pill px-3">Disponible</span>
                            <?php else: ?>
                                <span class="badge bg-danger rounded-pill px-3">Agotado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple Client-side Search
    document.getElementById('searchCatalog').addEventListener('keyup', function(e){
        const term = e.target.value.toLowerCase();
        const items = document.querySelectorAll('.catalog-item');
        
        items.forEach(item => {
            const title = item.querySelector('.card-title').textContent.toLowerCase();
            const author = item.querySelector('.card-text').textContent.toLowerCase();
            if(title.includes(term) || author.includes(term)){
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
