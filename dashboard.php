<?php
session_start();
require_once 'backend/auth.php';
requireAdmin();
$usuario = getUsuarioActual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Sistema QR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .topbar {
            background: rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            border-radius: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            font-size: 32px;
        }

        .btn-logout {
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .menu-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
        }

        .menu-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .menu-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .menu-description {
            font-size: 14px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="user-info">
            <div class="user-avatar">👤</div>
            <div>
                <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong><br>
                <small>Administrador</small>
            </div>
        </div>
        <button class="btn-logout" onclick="logout()">🚪 Cerrar Sesión</button>
    </div>

    <div class="container">
        <h1>🎯 Panel de Administración</h1>

        <div class="menu-grid">
            <a href="admin/crear_boleto.html" class="menu-card">
                <div class="menu-icon">✨</div>
                <div class="menu-title">Generar Boletos</div>
                <div class="menu-description">Crear nuevos boletos con QR</div>
            </a>

            <a href="frontend/scan.html" class="menu-card">
                <div class="menu-icon">📱</div>
                <div class="menu-title">Escanear Boletos</div>
                <div class="menu-description">Validar boletos en la entrada</div>
            </a>
        </div>
    </div>

    <script>
        function logout() {
            if (confirm('¿Cerrar sesión?')) {
                window.location.href = 'backend/logout.php';
            }
        }
    </script>
</body>
</html>
