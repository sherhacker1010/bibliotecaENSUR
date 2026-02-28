<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?> - <?php echo SITENAME; ?></title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/libs/bootstrap-icons/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .report-header {
            background-color: white;
            border-bottom: 2px solid #4e73df;
            margin-bottom: 30px;
            padding: 20px 0;
        }
        .report-title {
            font-weight: 700;
            color: #4e73df;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .table thead th {
            background-color: #4e73df;
            color: white;
            font-weight: 500;
            border: none;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(78, 115, 223, 0.05);
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #dee2e6;
        }
        .btn-print {
            background-color: #4e73df;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn-print:hover {
            background-color: #2e59d9;
            color: white;
        }

        @media print {
            body {
                background-color: white;
            }
            .no-print {
                display: none !important;
            }
            .report-header {
                border-bottom: 2px solid black;
            }
            .table thead th {
                background-color: #ddd !important;
                color: black !important;
                border-bottom: 1px solid black;
            }
            .table-striped tbody tr:nth-of-type(odd) {
                background-color: #f2f2f2 !important;
            }
            a {
                text-decoration: none;
                color: black;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Action Buttons -->
        <div class="d-flex justify-content-end mb-3 no-print container">
            <button onclick="window.print()" class="btn btn-print shadow-sm me-2">
                <i class="bi bi-printer-fill"></i> Imprimir
            </button>
            <button onclick="window.close()" class="btn btn-secondary shadow-sm">
                <i class="bi bi-x-circle"></i> Cerrar
            </button>
        </div>

        <!-- Report Content -->
        <div class="container bg-white p-5 shadow-sm rounded">
            <div class="report-header d-flex justify-content-between align-items-end">
                <div>
                    <h5 class="text-muted mb-0"><?php echo SITENAME; ?></h5>
                    <h2 class="report-title mb-0"><?php echo $data['title']; ?></h2>
                </div>
                <div class="text-end">
                    <p class="mb-0 text-muted">Fecha de Generación</p>
                    <h5 class="fw-bold"><?php echo date('d/m/Y H:i'); ?></h5>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <?php foreach($data['headers'] as $header): ?>
                                <th class="text-center"><?php echo $header; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['data'])): ?>
                            <tr>
                                <td colspan="<?php echo count($data['headers']); ?>" class="text-center py-4 text-muted">
                                    No hay datos disponibles para este reporte.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['data'] as $row): ?>
                            <tr>
                                <?php foreach($data['fields'] as $field): ?>
                                    <td class="text-center">
                                        <?php 
                                            // Basic formatting based on content
                                            $val = $row[$field] ?? '-';
                                            if($field == 'status'){
                                                $statusMap = [
                                                    'active' => '<span class="badge bg-success">Activo</span>',
                                                    'returned' => '<span class="badge bg-secondary">Devuelto</span>',
                                                    'overdue' => '<span class="badge bg-danger">Vencido</span>'
                                                ];
                                                echo $statusMap[$val] ?? $val;
                                            } else {
                                                echo $val;
                                            }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-5 pt-3 border-top text-center text-muted">
                <small>Reporte generado automáticamente por el sistema de gestión bibliotecaria.</small>
            </div>
        </div>
    </div>
</body>
</html>
