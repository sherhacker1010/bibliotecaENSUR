<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITENAME; ?></title>
    <!-- Assets Local -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/libs/bootstrap-icons/bootstrap-icons.css">
    <!-- Custom CSS (Inline for now or new file) -->
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Modern Mobile App Palette */
            --primary-color: #6366f1; /* Indigo 500 */
            --primary-dark: #4f46e5;
            --secondary-color: #ec4899; /* Pink 500 */
            --success-color: #10b981; /* Emerald 500 */
            --warning-color: #f59e0b; /* Amber 500 */
            --danger-color: #ef4444; /* Red 500 */
            --dark-color: #1f2937; /* Gray 800 */
            --light-color: #f9fafb; /* Gray 50 */
            --text-color: #374151; /* Gray 700 */
            --text-muted: #9ca3af;
            
            --sidebar-bg: #1e1e2d; /* Deep Blue/Black */
            --sidebar-width: 260px;
            --navbar-height: 64px;
            
            --card-radius: 20px;
            --btn-radius: 50px;
            --input-radius: 12px;
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.08);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            color: var(--text-color);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* =========================================
           SOFT UI COMPONENT OVERRIDES
           ========================================= */
        
        /* Cards: Soft & Round */
        .card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft) !important;
            background: #fff;
            transition: transform 0.2s ease;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.03);
            padding: 1.25rem 1.5rem;
        }
        
        .card-header h6 {
            color: var(--primary-color);
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        /* Buttons: Pills & Gradients */
        .btn {
            border-radius: var(--btn-radius);
            padding: 0.5rem 1.2rem;
            font-weight: 500;
            letter-spacing: 0.3px;
            box-shadow: var(--shadow-sm);
            border: none;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }

        /* Inputs: Modern & Gray */
        .form-control {
            border-radius: var(--input-radius);
            background-color: #f9fafb;
            border: 1px solid transparent;
            padding: 0.75rem 1rem;
            color: var(--dark-color);
        }
        
        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        /* =========================================
           MOBILE FIRST LAYOUT
           ========================================= */

        /* 1. SIDEBAR (Modern Dark Drawer) */
        .sidebar {
            background-color: var(--sidebar-bg);
            width: var(--sidebar-width);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: calc(var(--sidebar-width) * -1);
            z-index: 1060; 
            transition: cubic-bezier(0.4, 0, 0.2, 1) 0.3s; /* Smooth stick */
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar.toggled {
            left: 0 !important;
            box-shadow: 100px 0 100px rgba(0,0,0,0.5); /* Backdrop effect simulation */
        }

        /* Sidebar Nav */
        .sidebar .nav-link {
            color: #aeb7c0;
            padding: 16px 25px;
            font-weight: 500;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            border-left: 3px solid transparent;
            margin-bottom: 2px;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.03);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.15) 0%, rgba(0,0,0,0) 100%);
            border-left-color: var(--primary-color);
        }

        .sidebar .nav-link i {
            margin-right: 15px;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            color: var(--primary-color) opacity(0.8);
        }
        .sidebar .nav-link.active i {
            color: var(--primary-color);
        }

        /* 2. NAVBAR (Clean White App Bar) */
        .navbar-custom {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px); /* Glass effect */
            height: var(--navbar-height);
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 1040;
            box-shadow: 0 1px 0 0 rgba(0,0,0,0.05); /* Divider only */
            padding: 0;
        }

        .navbar-custom .container-fluid {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
        }
        
        .navbar-custom .navbar-nav {
            flex-direction: row;
            gap: 15px;
            align-items: center;
        }

        /* 3. CONTENT AREA */
        main, .container-fluid {
            padding-top: 25px;
            padding-bottom: 40px;
        }

        /* =========================================
           DESKTOP OVERRIDES (>= 769px)
           ========================================= */
        @media (min-width: 769px) {
            .sidebar {
                left: 0;
            }
            
            body {
                padding-left: var(--sidebar-width);
            }

            .navbar-custom {
                position: fixed;
                left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
            
            main, .container-fluid {
                padding-top: calc(var(--navbar-height) + 30px);
            }
        }

        /* =========================================
           MOBILE ENHANCEMENTS & COMPONENTS
           ========================================= */
        
        /* Typography Scale */
        h1.h3 { font-size: 1.5rem; letter-spacing: -0.5px; color: var(--dark-color); }
        
        /* Floating Action Button (FAB) - Gradient */
        .fab-container {
            position: fixed;
            bottom: 30px;
            right: 25px;
            z-index: 1070;
        }
        
        .fab-btn {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3); /* Colored shadow */
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: none;
            font-size: 1.6rem;
            text-decoration: none;
        }
        
        .fab-btn:active {
            transform: scale(0.9);
        }

        /* Mobile Labels */
        .mobile-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
        }
        
        /* List Item Style for Mobile Cards */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.25rem;
            }
        }

        /* Book Item Styles (Preserved but Modernized) */
        .book-item {
            width: 140px; 
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            cursor: pointer;
            z-index: 1;
        }
        .book-item:hover {
            transform: translateY(-8px) scale(1.05);
            z-index: 10;
        }
        .book-spine {
            height: 190px; /* Taller */
            width: 100%;
            border-radius: 8px; /* More rounded */
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
            background-color: #f1f5f9;
        }
        .book-spine img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>
