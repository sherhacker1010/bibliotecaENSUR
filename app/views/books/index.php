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
                <h1 class="h3 mb-0 text-gray-800">Libros</h1>
                <?php if(AuthHelper::hasRole(['admin', 'librarian'])): ?>
                <a href="<?php echo URLROOT; ?>/books/create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="bi bi-plus-lg text-white-50"></i> Nuevo Libro
                </a>
                <?php endif; ?>
            </div>

            <!-- Search Bar Placeholder -->
            <div class="row mb-4">
                <div class="col-md-6">
                     <div class="input-group">
                        <input type="text" id="searchBooks" class="form-control bg-white border-0 small" placeholder="Buscar por título, autor..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Filter Sidebar -->
                <div class="col-md-3 mb-4">
                    <div class="list-group shadow-sm">
                        <button type="button" class="list-group-item list-group-item-action active" data-shelf-id="" onclick="filterShelf(this, '')">
                            <i class="bi bi-collection-fill me-2"></i> Todos los Libros
                        </button>
                        <?php foreach($data['shelves'] as $shelf): ?>
                        <button type="button" class="list-group-item list-group-item-action" data-shelf-id="<?php echo $shelf['id']; ?>" onclick="filterShelf(this, '<?php echo $shelf['id']; ?>')">
                            <i class="bi bi-journal-bookmark me-2"></i> <?php echo $shelf['name']; ?>
                        </button>
                        <?php endforeach; ?>
                        <button type="button" class="list-group-item list-group-item-action text-muted" data-shelf-id="none" onclick="filterShelf(this, 'none')"> <!-- Optional: Handle books with no shelf explicitly if needed, or just include in all -->
                             <i class="bi bi-question-circle me-2"></i> Sin Estantería
                        </button>
                    </div>
                </div>

                <!-- Books Grid -->
                <div class="col-md-9">
                    <div id="shelf-results">
                        <?php require APPROOT . '/views/books/partials/shelf_list.php'; ?>
                    </div>
                </div>
            </div>

            <script>
                let currentShelf = '';

                function filterShelf(btn, shelfId) {
                    // Update active state
                    document.querySelectorAll('.list-group-item').forEach(el => el.classList.remove('active'));
                    btn.classList.add('active');
                    
                    currentShelf = shelfId;
                    performSearch();
                }

                document.getElementById('searchBooks').addEventListener('keyup', function() {
                    performSearch();
                });

                function performSearch() {
                    let query = document.getElementById('searchBooks').value;
                    let url = '<?php echo URLROOT; ?>/books/search?q=' + encodeURIComponent(query);
                    
                    if(currentShelf) {
                        url += '&shelf_id=' + encodeURIComponent(currentShelf);
                    }
                    
                    fetch(url)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('shelf-results').innerHTML = data;
                    })
                    .catch(error => console.error('Error:', error));
                }
            </script>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
