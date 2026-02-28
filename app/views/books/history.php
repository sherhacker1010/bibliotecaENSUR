<?php require APPROOT . '/views/layouts/header.php'; ?>
<?php require APPROOT . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <?php require APPROOT . '/views/layouts/sidebar.php'; ?>
        </div>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-3">
             <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Historial del Libro</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?php echo URLROOT; ?>/books/show/<?php echo $data['copy']['book_id']; ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Libro
                    </a>
                </div>
            </div>

            <h4><?php echo $data['copy']['title']; ?> <small class="text-muted">(<?php echo $data['copy']['unique_code']; ?>)</small></h4>

            <div class="timeline mt-4">
                <?php if(empty($data['history'])): ?>
                    <div class="alert alert-info">No hay historial para esta copia.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush border-start border-3 ms-3">
                        <?php foreach($data['history'] as $event): ?>
                        <li class="list-group-item border-0 position-relative ps-4 mb-3">
                            <div class="position-absolute top-0 start-0 translate-middle bg-white border border-2 rounded-circle mt-3 ms-0" style="width: 1rem; height: 1rem;"></div>
                            
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        <?php 
                                            $userImg = URLROOT . '/public/assets/img/default-user.png';
                                            if(!empty($event['user_image'])){
                                                $path = 'public/uploads/users/' . $event['user_image'];
                                                if(file_exists($path)) $userImg = URLROOT . '/' . $path;
                                            }
                                        ?>
                                        <img src="<?php echo $userImg; ?>" class="rounded-circle me-2" width="30" height="30" style="object-fit: cover;">
                                        <?php echo $event['user_name']; ?> 
                                        <small class="text-muted fs-6">(<?php echo ucfirst($event['user_role']); ?>)</small>
                                    </h5>
                                    
                                    <p class="card-text mb-1"><i class="bi bi-calendar-event"></i> Prestado: <strong><?php echo $event['loan_date']; ?></strong></p>
                                    <p class="card-text mb-1"><i class="bi bi-calendar-check"></i> Vencimiento: <?php echo $event['due_date']; ?></p>
                                    
                                    <?php if(!empty($event['librarian_name'])): ?>
                                        <div class="mt-2 mb-2 d-flex align-items-center text-muted small">
                                            <i class="bi bi-person-badge me-1"></i> Prestado por: 
                                            <?php 
                                                $libImg = URLROOT . '/public/assets/img/default-user.png';
                                                if(!empty($event['librarian_image'])){
                                                    $path = 'public/uploads/users/' . $event['librarian_image'];
                                                    if(file_exists($path)) $libImg = URLROOT . '/' . $path;
                                                }
                                            ?>
                                            <img src="<?php echo $libImg; ?>" class="rounded-circle ms-1 me-1" width="20" height="20" style="object-fit: cover;">
                                            <?php echo $event['librarian_name']; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if($event['return_date']): ?>
                                        <p class="text-success fw-bold"><i class="bi bi-check-circle"></i> Devuelto: <?php echo $event['return_date']; ?></p>
                                    <?php else: ?>
                                        <p class="text-danger fw-bold"><i class="bi bi-clock-history"></i> No devuelto</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
