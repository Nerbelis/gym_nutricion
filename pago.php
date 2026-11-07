<?php
// Función para cargar el archivo .env de forma directa y sencilla
function cargarEntorno($ruta) {
    if (!file_exists($ruta)) {
        return;
    }
    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        if (strpos(trim($linea), '#') === 0) {
            continue;
        }
        $partes = explode('=', $linea, 2);
        if (count($partes) === 2) {
            $nombre = trim($partes[0]);
            // Limpiamos espacios y comillas dobles o simples que pueda tener el valor
            $valor = trim(trim($partes[1]), "\"'");
            
            if (!array_key_exists($nombre, $_SERVER) && !array_key_exists($nombre, $_ENV)) {
                putenv($nombre . '=' . $valor);
                $_ENV[$nombre] = $valor;
                $_SERVER[$nombre] = $valor;
            }
        }
    }
}

// Cargar el .env de forma directa
cargarEntorno('.env');


// Detección automática de entorno (Local vs Producción)
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    // Credenciales para tu laptop (Local)
    $host = 'localhost';
    $port = '3308'; // Puerto por defecto local
    $db = 'gym_nutricion_db';
    $user = 'root'; // Usuario local por defecto
    $pass = '';     // Sin contraseña en local
} else {
    // Credenciales para DigitalOcean (Producción)(Puerto 3308, Base de datos gym_nutricion_db)
    $host = 'localhost';
    $port = '3308';
    $db = 'gym_nutricion_db';
    $user = 'nerco_user';
    $pass = 'NercoSegura2026*';
}


try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexion: " . $e->getMessage());
}

$pago_enviado = false;
$nombre = $edad = $genero = $peso = $altura = $imc = $estado = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['enviar_pago'])) {
        $nombre     = trim($_POST['nombre'] ?? '');
        $edad       = intval($_POST['edad'] ?? 0);
        $genero     = filter_var($_POST['genero'] ?? 'femenino', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $peso       = floatval($_POST['peso'] ?? 0);
        $altura     = floatval($_POST['altura'] ?? 0);
        $imc        = floatval($_POST['imc'] ?? 0);
        $estado     = filter_var($_POST['estado'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $metodo     = filter_var($_POST['metodo_pago'] ?? 'pago_movil', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $referencia = trim($_POST['referencia'] ?? '');
        $monto      = floatval($_POST['monto_pago'] ?? 0);

        // 1. Guardar cliente temporalmente en la base de datos
        $stmtCli = $pdo->prepare("INSERT INTO clientes_evaluacion (nombre, edad, genero, peso, altura, imc, estado_nutricional, porcentaje_grasa, tmb, tdee, notas_entrenador) VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, 0, 'Pendiente de aprobacion')");
        $stmtCli->execute([$nombre, $edad, $genero, $peso, $altura, $imc, $estado]);
        $cliente_id = $pdo->lastInsertId();

        // 2. Guardar pago con estatus 'pendiente'
        $stmtPago = $pdo->prepare("INSERT INTO pagos_suscripcion (cliente_id, metodo_pago, referencia_transaccion, monto, moneda, estado_pago) VALUES (?, ?, ?, ?, 'VES', 'pendiente')");
        $stmtPago->execute([$cliente_id, $metodo, $referencia, $monto]);

    
      // 3. OBTENER CREDENCIALES DE FORMA SEGURA DESDE EL .ENV
        $token   = $_ENV['TELEGRAM_BOT_TOKEN'] ?? getenv('TELEGRAM_BOT_TOKEN');
        $chat_id = $_ENV['TELEGRAM_CHAT_ID'] ?? getenv('TELEGRAM_CHAT_ID');

        $url_aprobacion = "https://gym.nercovalch.uk/enfoque.php?id=" . $cliente_id;

        $mensaje  = "NUEVO PAGO PENDIENTE DE VALIDACION - NercoFit\n\n";
        $mensaje .= "Socio: " . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . " (" . $edad . " anos)\n";
        $mensaje .= "Metodo: Pago Movil (Banco de Venezuela)\n";
        $mensaje .= "Referencia: " . htmlspecialchars($referencia, ENT_QUOTES, 'UTF-8') . "\n";
        $mensaje .= "Monto: " . $monto . " VES\n\n";
        $mensaje .= "Datos de Pago Movil:\n- Banco: Venezuela\n- Telefono: 0424-6163113\n- Cedula: 11.282.487\n\n";
        $mensaje .= "Por favor verifique en su banco. Si el dinero esta disponible, haz clic en el siguiente enlace para aprobar:\n";
        $mensaje .= $url_aprobacion;

        $data = [
            'chat_id' => $chat_id,
            'text' => $mensaje
        ];

       $ch = curl_init("https://api.telegram.org/bot" . trim($token) . "/sendMessage");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$respuesta_telegram = curl_exec($ch);
$error_curl = curl_error($ch);
curl_close($ch);

// Depuración temporal para ver si Telegram responde o hay error
if ($error_curl) {
    die("Error de cURL: " . $error_curl);
}

$resultado_json = json_decode($respuesta_telegram, true);
if (!$resultado_json || !isset($resultado_json['ok']) || $resultado_json['ok'] !== true) {
    die("Error devuelto por Telegram: " . $respuesta_telegram);
}

$pago_enviado = true;
    } else {
        $nombre = $_POST['nombre'] ?? '';
        $edad = $_POST['edad'] ?? '';
        $genero = $_POST['genero'] ?? '';
        $peso = $_POST['peso'] ?? '';
        $altura = $_POST['altura'] ?? '';
        $imc = $_POST['imc'] ?? '';
        $estado = $_POST['estado'] ?? '';
    }
} else {
    header("Location: evaluacion.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NercoFit | Esperando Confirmacion de Pago</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .container { max-width: 600px; background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin: auto; border: 1px solid #e2e8f0; }
        h2 { color: #0f172a; text-align: center; margin-bottom: 5px; font-size: 22px; }
        .sub-header { text-align: center; font-size: 13px; color: #64748b; margin-bottom: 25px; }
        .datos-pago { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; color: #166534; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #334155; font-size: 14px; }
        input { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 14px; background: #f8fafc; }
        button { background: #0284c7; color: #fff; border: none; padding: 12px; width: 100%; border-radius: 6px; font-size: 15px; cursor: pointer; font-weight: bold; transition: background 0.2s; margin-top: 15px; }
        button:hover { background: #0369a1; }
        .stop-box { background: #fffbeb; border: 1px solid #fde68a; padding: 25px; border-radius: 8px; text-align: center; color: #92400e; }
        .stop-box h3 { margin-top: 0; color: #b45309; font-size: 20px; }
        .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #d97706; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 15px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
<div class="container">
    <h2>NercoFit</h2>
    <div class="sub-header">Modulo de Validacion de Pago Movil</div>

    <?php if (!$pago_enviado): ?>
        <div class="datos-pago">
            <strong>Datos para realizar su Pago Movil:</strong><br>
            • <strong>Banco:</strong> Banco de Venezuela (0102)<br>
            • <strong>Telefono:</strong> 0424-6163113<br>
            • <strong>Cedula:</strong> 11.282.487<br>
            • <strong>Monto sugerido:</strong> 2000.00 VES
        </div>

        <form method="POST">
            <input type="hidden" name="nombre" value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="edad" value="<?= $edad ?>">
            <input type="hidden" name="genero" value="<?= htmlspecialchars($genero, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="peso" value="<?= $peso ?>">
            <input type="hidden" name="altura" value="<?= $altura ?>">
            <input type="hidden" name="imc" value="<?= $imc ?>">
            <input type="hidden" name="estado" value="<?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="metodo_pago" value="pago_movil">

            <div class="form-group">
                <label>Numero de Referencia Bancaria (Ultimos digitos):</label>
                <input type="text" name="referencia" required placeholder="Ej. 482910">
            </div>
            <div class="form-group">
                <label>Monto exacto pagado (VES):</label>
                <input type="number" step="0.01" name="monto_pago" required value="2000">
            </div>

            <button type="submit" name="enviar_pago">Reportar Pago y Solicitar Autorizacion</button>
        </form>
    <?php else: ?>
        <div class="stop-box">
            <div class="spinner"></div>
            <h3>Estatus: Esperando Autorizacion</h3>
            <p>
                Hemos registrado tu referencia de pago y se ha enviado una alerta de validacion a nuestro sistema de administracion por Telegram.<br><br>
                <strong>Por favor, espera unos instantes.</strong> Una vez que verifiques la transacción y hagas clic en el enlace que te llegó a Telegram, el sistema continuará de inmediato.
            </p>
            <p style="font-size: 12px; color: #b45309; margin-top: 15px;">
                Ref. Registrada: <code><?= htmlspecialchars($_POST['referencia'] ?? '', ENT_QUOTES, 'UTF-8') ?></code>
            </p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>