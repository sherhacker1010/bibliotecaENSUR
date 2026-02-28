

<div class="library-container" id="library-container">
    <?php if(empty($data['books'])): ?>
        <div class="alert alert-info text-center w-100">
            <i class="bi bi-info-circle"></i> No se encontraron libros.
        </div>
    <?php else: ?>
        <div class="row g-3 g-md-4">
            <?php foreach($data['books'] as $book): ?>
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="book-item w-100" onclick="window.location.href='<?php echo URLROOT; ?>/books/show/<?php echo $book['id']; ?>'">
                        <div class="book-spine ratio ratio-3x4 position-relative">
                            <?php if(AuthHelper::hasRole(['admin', 'librarian'])): ?>
                                <a href="<?php echo URLROOT; ?>/books/edit/<?php echo $book['id']; ?>" class="position-absolute top-0 end-0 m-2 btn btn-sm btn-light text-primary z-2 shadow-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="event.stopPropagation();" title="Editar">
                                    <i class="bi bi-pencil-fill" style="font-size: 0.7rem;"></i>
                                </a>
                            <?php endif; ?>

                            <?php if($book['cover_image']): ?>
                                <img src="<?php echo URLROOT; ?>/uploads/covers/<?php echo $book['cover_image']; ?>" class="rounded-3 w-100 h-100 object-fit-cover shadow-sm" alt="<?php echo $book['title']; ?>">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-light text-secondary rounded-3 border">
                                    <div class="text-center p-2">
                                        <i class="bi bi-book fs-4 mb-2 d-block opacity-50"></i>
                                        <small class="fw-bold lh-sm d-block text-truncate-3"><?php echo $book['title']; ?></small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Simple Mobile-First metadata below the book -->
                        <div class="mt-2 text-center">
                            <div class="fw-bold text-dark small text-truncate"><?php echo $book['title']; ?></div>
                            <div class="text-muted extra-small text-truncate"><?php echo $book['author']; ?></div>
                        </div>

                        <!-- Tooltip with details (Hidden on mobile via CSS usually, or we can keep it for hover capable devices) -->
                        <div class="book-info-tooltip d-none d-md-block">
                            <div class="fw-bold mb-1"><?php echo $book['title']; ?></div>
                            <div class="small mb-1 text-light-50"><?php echo $book['author']; ?></div>
                            <div class="badge bg-<?php echo $book['stock'] > 0 ? 'success' : 'danger'; ?> mb-1">
                                <?php echo $book['stock'] > 0 ? 'Disp: ' . $book['stock'] : 'Agotado'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
