<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITENAME; ?> - Login</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background: white;
            padding: 2.5rem 2rem 1rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
            background: white;
        }
        .brand-icon-container {
            width: 70px;
            height: 70px;
            background: #f8f9fc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: #4e73df;
            font-size: 2rem;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
        }
        .form-control-lg {
            font-size: 0.95rem;
            padding: 0.8rem 1rem;
            border-left: none;
        }
        .input-group-text {
            border-right: none;
            background-color: #f8f9fa;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
        .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            border-radius: 0.375rem;
        }
        .input-group:focus-within .form-control, 
        .input-group:focus-within .input-group-text {
            border-color: #86b7fe;
        }
        .btn-login {
            background-color: #4e73df;
            border-color: #4e73df;
            padding: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="container d-flex justify-content-center">
        <div class="card login-card animate__animated animate__fadeInUp">
            <div class="login-header">
                <div class="brand-icon-container">
                    <i class="bi bi-book-half"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Bienvenido</h4>
                <p class="text-muted small mb-0">Ingresa tus credenciales</p>
            </div>
            
            <div class="login-body pt-0">
                <form action="<?php echo URLROOT; ?>/auth/login" method="post">
                    
                    <?php if(!empty($data['username_err']) || !empty($data['password_err'])): ?>
                        <div class="alert alert-danger py-2 mb-4 small rounded-3 border-0 bg-danger text-white text-center shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Credenciales incorrectas
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="form-label small text-muted">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" class="form-control form-control-lg" value="<?php echo $data['username']; ?>" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-muted">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control form-control-lg" value="<?php echo $data['password']; ?>" required>
                        </div>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-primary btn-block btn-login text-white">
                            INICIAR SESIÓN
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-light border-0 py-3">
                <small class="text-muted opacity-75">Sistema de Biblioteca &copy; <?php echo date('Y'); ?></small>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
