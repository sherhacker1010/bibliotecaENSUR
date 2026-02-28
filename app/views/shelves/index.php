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
                <h1 class="h3 mb-0 text-gray-800">Estanterías</h1>
                <a href="<?php echo URLROOT; ?>/shelves/create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="bi bi-plus-lg text-white-50"></i> Nueva Estantería
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Listado de Estanterías</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['shelves'] as $shelf): ?>
                                <tr>
                                    <td><?php echo $shelf['id']; ?></td>
                                    <td><?php echo $shelf['name']; ?></td>
                                    <td><?php echo strip_tags($shelf['description']); ?></td>
                                    <td>
                                        <a href="<?php echo URLROOT; ?>/shelves/edit/<?php echo $shelf['id']; ?>" class="btn btn-sm btn-info text-white me-1"><i class="bi bi-pencil-square"></i></a>
                                        <form action="<?php echo URLROOT; ?>/shelves/delete/<?php echo $shelf['id']; ?>" method="post" class="d-inline" onsubmit="return confirm('¿Está seguro?');">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash-fill"></i></button>
                                        </form>
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

<?php require APPROOT . '/views/layouts/footer.php'; ?>
