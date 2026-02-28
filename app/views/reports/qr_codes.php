<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-print-none d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $data['title']; ?></h1>
        <button onclick="window.print()" class="btn btn-primary shadow-sm"><i class="bi bi-printer"></i> Imprimir</button>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4" id="qr-grid">
        <?php if(empty($data['copies'])): ?>
            <div class="col-12">
                <div class="alert alert-warning">No se encontraron copias para generar códigos QR.</div>
            </div>
        <?php else: ?>
            <?php foreach($data['copies'] as $copy): ?>
                <div class="col text-center page-break-inside-avoid">
                    <div class="card h-100 border-2 dashed-border">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <h6 class="card-title fw-bold mb-2 text-truncate w-100" title="<?php echo $copy['title']; ?>">
                                <?php echo $copy['title']; ?>
                            </h6>
                            <img src="<?php echo URLROOT; ?>/uploads/qrcodes/<?php echo $copy['unique_code']; ?>.png" 
                                 alt="QR Code" 
                                 class="img-fluid mb-2" 
                                 style="width: 120px; height: 120px;">
                            <div class="badge bg-light text-dark border font-monospace">
                                <?php echo $copy['unique_code']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    @media print {
        @page {
            margin: 1cm;
            size: auto;
        }

        body {
            background-color: white !important;
            margin: 0;
            padding: 0;
        }

        /* Hide everything that is not the QR grid */
        body > *:not(.container-fluid), 
        .sidebar, 
        .navbar, 
        header, 
        footer, 
        .btn, 
        .d-print-none, 
        h1 {
            display: none !important;
        }
        
        /* Reset container constraints */
        .container-fluid {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        #qr-grid {
            display: flex !important;
            flex-wrap: wrap;
            width: 100% !important;
            margin: 0 !important;
        }

        /* Card styling for print */
        .col {
            flex: 0 0 33.33%;
            max-width: 33.33%;
            padding: 5px; /* Reduced spacing */
        }

        .card {
            border: 1px dashed #000 !important; /* Dashed is accurate for cutting */
            box-shadow: none !important;
            break-inside: avoid;
            page-break-inside: avoid;
            margin: 0;
            padding: 10px;
            height: auto !important;
            min-height: 160px; /* Ensure uniform height */
        }
        
        h6.card-title {
            font-size: 10pt;
            white-space: normal; /* Allow wrapping */
            text-align: center;
            margin-bottom: 5px !important;
            max-height: 2.4em;
            overflow: hidden;
        }

        /* Ensure images print clearly */
        img {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            width: 80px !important; /* Smaller QR to fit better */
            height: 80px !important;
        }
        
        .badge {
            font-size: 8pt;
            border: 1px solid #ccc;
            padding: 2px 5px;
            color: #000 !important;
            background: transparent !important;
        }
    }
</style>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
