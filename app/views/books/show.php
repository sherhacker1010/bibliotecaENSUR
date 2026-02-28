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
                <h1 class="h3 mb-0 text-gray-800"><?php echo $data['book']['title']; ?></h1>
                <a href="<?php echo URLROOT; ?>/books/index" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <div class="row">
                <!-- Book Cover & Info -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <?php if($data['book']['cover_image']): ?>
                                <img src="<?php echo URLROOT; ?>/uploads/covers/<?php echo $data['book']['cover_image']; ?>" class="img-fluid rounded mb-3 shadow-sm" alt="Cover" style="max-height: 400px;">
                            <?php else: ?>
                                 <div class="bg-gray-200 text-secondary d-flex align-items-center justify-content-center rounded mb-3" style="height: 300px; background-color: #e9ecef;">
                                    <i class="bi bi-book display-1"></i>
                                </div>
                            <?php endif; ?>
                            
                            <h5 class="font-weight-bold text-dark"><?php echo $data['book']['title']; ?></h5>
                            <p class="text-muted mb-1"><?php echo $data['book']['author']; ?></p>
                            <span class="badge bg-primary"><?php echo $data['book']['genre']; ?></span>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="row text-center">
                                <div class="col-6 border-end">
                                    <span class="d-block small text-muted text-uppercase">Stock Total</span>
                                    <span class="h5 font-weight-bold text-dark"><?php echo $data['book']['stock']; ?></span>
                                </div>
                                <div class="col-6">
                                    <span class="d-block small text-muted text-uppercase">Estantería</span>
                                    <span class="h6 font-weight-bold text-dark"><?php echo $data['book']['shelf_name']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Descripción</h6>
                        </div>
                        <div class="card-body">
                            <?php echo $data['book']['description']; ?>
                        </div>
                    </div>
                </div>

                <!-- Copies Table -->
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Copias Físicas</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Cód. Único</th>
                                            <th>Estado</th>
                                            <th>QR</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($data['copies'] as $copy): ?>
                                        <tr>
                                            <td class="align-middle font-weight-bold"><?php echo $copy['unique_code']; ?></td>
                                            <td class="align-middle">
                                                <?php
                                                    $statusMap = [
                                                        'available' => 'Disponible',
                                                        'loaned' => 'Prestado',
                                                        'maintenance' => 'Mantenimiento',
                                                        'lost' => 'Perdido'
                                                    ];
                                                    $statusLabel = $statusMap[$copy['status']] ?? ucfirst($copy['status']);
                                                ?>
                                                <span class="badge bg-<?php echo ($copy['status'] == 'available') ? 'success' : 'danger'; ?> rounded-pill px-3">
                                                    <?php echo $statusLabel; ?>
                                                </span>
                                                <?php if($copy['status'] == 'loaned' && !empty($copy['active_loan'])): ?>
                                                    <div class="d-flex align-items-center mt-2">
                                                        <small class="text-muted me-2">Prestado a:</small>
                                                        <?php 
                                                            $borrowerImg = URLROOT . '/public/assets/img/default-user.png';
                                                            if(!empty($copy['active_loan']['user_image'])){
                                                                $borrowerPath = 'public/uploads/users/' . $copy['active_loan']['user_image'];
                                                                if(file_exists($borrowerPath)){
                                                                    $borrowerImg = URLROOT . '/' . $borrowerPath;
                                                                }
                                                            }
                                                        ?>
                                                        <img src="<?php echo $borrowerImg; ?>" class="rounded-circle me-1" width="25" height="25" style="object-fit: cover;" title="<?php echo $copy['active_loan']['user_name']; ?>">
                                                        <small class="fw-bold"><?php echo $copy['active_loan']['user_name']; ?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle text-center">
                                                <img src="<?php echo URLROOT; ?>/uploads/qrcodes/<?php echo $copy['unique_code']; ?>.png" width="40" alt="QR">
                                            </td>
                                            <td class="align-middle">
                                                <button class="btn btn-sm btn-outline-info shadow-sm me-1" onclick="printQR('<?php echo $copy['unique_code']; ?>')" title="Imprimir QR"><i class="bi bi-printer"></i></button>
                                                <a href="<?php echo URLROOT; ?>/books/history/<?php echo $copy['id']; ?>" class="btn btn-sm btn-outline-secondary shadow-sm" title="Ver Historial"><i class="bi bi-clock-history"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printQR(code){
    var win = window.open('', 'Print', 'height=400,width=600');
    win.document.write('<html><head><title>QR Code</title></head><body style="text-align:center; padding-top: 50px;">');
    win.document.write('<img src="<?php echo URLROOT; ?>/uploads/qrcodes/' + code + '.png" style="width:200px;">');
    win.document.write('<h2 style="font-family: monospace; margin-top: 10px;">' + code + '</h2>');
    win.document.write('</body></html>');
    win.document.close();
    win.focus();
    setTimeout(function(){ win.print(); win.close(); }, 1000);
}
</script>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
