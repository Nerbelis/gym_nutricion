<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>GlobalFit Index | Panel Principal</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; box-sizing: border-box; }
        .hero-container { max-width: 600px; width: 100%; background: #ffffff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); text-align: center; border: 1px solid #e2e8f0; }
        h2 { color: #0f172a; font-size: 24px; margin-bottom: 5px; }
        .hero-gancho { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .hero-gancho h3 { margin: 0 0 8px 0; color: #166534; font-size: 20px; }
        .hero-gancho p { margin: 0; color: #15803d; font-size: 15px; font-weight: 600; line-height: 1.4; }
        .img-preview { width: 100%; max-height: 220px; object-fit: cover; border-radius: 8px; margin: 15px 0; }
        .btn-iniciar { display: inline-block; background: #0284c7; color: #fff; text-decoration: none; padding: 14px 30px; border-radius: 6px; font-size: 16px; font-weight: bold; transition: background 0.2s; margin-top: 15px; width: 100%; box-sizing: border-box; }
        .btn-iniciar:hover { background: #0369a1; }
        
        /* Contenedor del QR General */
        .qr-dashboard { margin-top: 25px; background: #f8fafc; border: 1px dashed #cbd5e1; padding: 15px; border-radius: 8px; display: inline-block; }
        .qr-dashboard img { width: 130px; height: 130px; display: block; margin: auto; }
        .qr-dashboard p { font-size: 12px; color: #64748b; margin: 8px 0 0 0; font-weight: 600; }

        .footer-links { font-size: 12px; color: #64748b; margin-top: 20px; }
        .footer-links a { color: #0284c7; text-decoration: none; }
        .footer-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="hero-container">
    <h2>GlobalFit Index</h2>
    
    <!-- El Gancho -->
    <div class="hero-gancho">
        <h3>¿Tienes más de 50?</h3>
        <p>¿Ya evaluaste si eres normal eutrófico... qué tipo de eutrófico eres?</p>
    </div>

    <!-- Imagen ilustrativa -->
    <img src="https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?auto=format&fit=crop&w=600&q=80" alt="Adultos mayores activos entrenando" class="img-preview">

    <p style="color: #475569; font-size: 14px; margin-bottom: 20px;">
        Sistema automatizado de referencia antropométrica y cálculo de IMC para determinar tu estado eutrófico según parámetros de la OMS.
    </p>

    <a href="evaluacion.php" class="btn-iniciar">Comenzar Evaluación y Consulta</a>

    <!-- Código QR General en el Dashboard para escaneo rápido -->
    <div class="qr-dashboard">
        <!-- Puedes cambiar la URL del data= por el enlace definitivo de tu web en la nube -->
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://shop.nercovalch.uk/fitbalance/" alt="QR Acceso GlobalFit Index">
        <p>📱 Escanea para acceso rápido en tu móvil</p>
    </div>

    <div class="footer-links">
        <a href="terminos.php">Términos, Descargo de Responsabilidad y Criterios OMS</a>
    </div>
</div>

</body>
</html>