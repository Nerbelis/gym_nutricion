<?php
// Configuración de conexión segura (Puerto 3308, Base de datos gym_nutricion_db)
$host = 'localhost';
$port = '3308';
$db   = 'gym_nutricion_db';
$user = 'root';
$pass = '';

try {
    // Conexión blindada con PDO y manejo de excepciones
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de configuración en la Base de Datos segura: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: evaluacion.php");
    exit;
}

// Sanitización y recepción segura de variables (Security Baseline)
$nombre           = trim($_POST['nombre'] ?? '');
$edad             = intval($_POST['edad'] ?? 0);
$genero           = filter_var($_POST['genero'] ?? 'femenino', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$peso             = floatval($_POST['peso'] ?? 0);       
$altura_cm        = intval($_POST['altura'] ?? 0);       
$actividad        = floatval($_POST['actividad'] ?? 1.375);  
$subtipo          = filter_var($_POST['subtipo'] ?? 'tonificacion', FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
$notas_entrenador = trim($_POST['notas_entrenador'] ?? ''); 
$metodo_pago      = filter_var($_POST['metodo_pago'] ?? 'pago_movil', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$referencia       = trim($_POST['referencia'] ?? '');
$monto_pago       = floatval($_POST['monto_pago'] ?? 0);
$estado           = filter_var($_POST['estado'] ?? 'Normal Eutrófico', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// 1. Cálculo de IMC de respaldo
$altura_m = $altura_cm / 100;
$imc = ($altura_m > 0) ? ($peso / ($altura_m * $altura_m)) : 0;

// 2. % Grasa (Deurenberg)
$constante_sexo = ($genero === 'masculino') ? 16.2 : 5.4;
$porcentaje_grasa = (1.20 * $imc) + (0.23 * $edad) - $constante_sexo;

// 3. Metabolismo (Harris-Benedict revisada)
if ($genero === 'masculino') {
    $tmb = 88.362 + (13.397 * $peso) + (4.799 * $altura_cm) - (5.677 * $edad);
} else {
    $tmb = 447.593 + (9.247 * $peso) + (3.098 * $altura_cm) - (4.330 * $edad);
}
$tdee = $tmb * $actividad;

// Guardar de forma segura usando Consultas Preparadas (Prepared Statements contra SQL Injection)
$stmt = $pdo->prepare("INSERT INTO clientes_evaluacion (nombre, edad, genero, peso, altura, imc, estado_nutricional, porcentaje_grasa, tmb, tdee, notas_entrenador) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $nombre, $edad, $genero, $peso, $altura_cm, 
    round($imc, 2), $estado, round($porcentaje_grasa, 2), 
    round($tmb), round($tdee), $notas_entrenador
]);
$cliente_id = $pdo->lastInsertId();

$moneda_pago = ($metodo_pago === 'pago_movil') ? 'VES' : 'USD';
$stmtPago = $pdo->prepare("INSERT INTO pagos_suscripcion (cliente_id, metodo_pago, referencia_transaccion, monto, moneda, estado_pago) VALUES (?, ?, ?, ?, ?, 'aprobado')");
$stmtPago->execute([$cliente_id, $metodo_pago, $referencia, $monto_pago, $moneda_pago]);

// Definir perfil de rutina según el enfoque seleccionado
$perfil_rutina = ($subtipo === 'tonificacion') ? 'Eutrófico Tonificación / Recomposición' : 'Eutrófico General / Longevidad';

$stmtRutina = $pdo->prepare("SELECT * FROM rutinas_fuerza WHERE tipo_perfil = ? LIMIT 1");
$stmtRutina->execute([$perfil_rutina]);
$rutina_asignada = $stmtRutina->fetch(PDO::FETCH_ASSOC);

if (!$rutina_asignada) {
    $stmtRutinaDef = $pdo->query("SELECT * FROM rutinas_fuerza LIMIT 1");
    $rutina_asignada = $stmtRutinaDef->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>GlobalFit Index | Reporte y Consulta</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .container { max-width: 700px; background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin: auto; border: 1px solid #e2e8f0; }
        h2 { color: #0f172a; text-align: center; margin-bottom: 20px; font-size: 22px; }
        .resultado-box { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 25px; border-radius: 8px; }
        .resultado-box h3 { color: #0f172a; margin-top: 0; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; width: 100%; font-size: 16px; }
        .badge-eutrofico { background: #dcfce7; color: #166534; padding: 3px 8px; border-radius: 4px; font-weight: bold; }
        .rutina-box { margin-top: 20px; background: #ffffff; border-left: 4px solid #0284c7; padding: 15px 20px; border-radius: 4px; width: 100%; border: 1px solid #e2e8f0; box-sizing: border-box; }
        .rutina-box h4 { margin: 0 0 10px 0; color: #0f172a; font-size: 15px; }
        .notas-box { margin-top: 15px; background: #fef9c3; border-left: 4px solid #ca8a04; padding: 12px 15px; border-radius: 4px; color: #713f12; width: 100%; box-sizing: border-box; font-size: 13px; }
        .pago-box { margin-top: 15px; background: #e0f2fe; border-left: 4px solid #0284c7; padding: 12px 15px; border-radius: 4px; color: #0369a1; width: 100%; box-sizing: border-box; font-size: 13px; }
        .legal-section { margin-top: 25px; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 12px; line-height: 1.4; text-align: center; }
        .legal-section a { color: #0284c7; text-decoration: none; }
        .legal-section a:hover { text-decoration: underline; }
        .btn-nueva { display: block; text-align: center; background: #0284c7; color: #fff; text-decoration: none; padding: 12px; border-radius: 6px; font-size: 15px; font-weight: bold; margin-top: 25px; }
        .btn-nueva:hover { background: #0369a1; }
        pre { white-space: pre-wrap; font-family: inherit; margin: 0; color: #334155; line-height: 1.5; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <h2>GlobalFit Index | Reporte y Consulta</h2>

    <div class="resultado-box">
        <h3>Diagnóstico Antropométrico para <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></h3>
        
        <div style="font-size: 14px; line-height: 1.6;">
            <p><strong>Edad:</strong> <?= $edad ?> años | <strong>Género:</strong> <?= ucfirst(htmlspecialchars($genero, ENT_QUOTES, 'UTF-8')) ?></p>
            <p><strong>IMC:</strong> <?= round($imc, 2) ?></p>
            <p><strong>Estado Nutricional:</strong> <span class="badge-eutrofico"><?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?></span></p>
            <p><strong>% Grasa Estimado:</strong> <?= round($porcentaje_grasa, 2) ?>%</p>
            <p><strong>Tasa Metabólica Basal (TMB):</strong> <?= round($tmb) ?> kcal</p>
            <p><strong>Gasto Diario (TDEE):</strong> <?= round($tdee) ?> kcal</p>
        </div>

        <div class="pago-box">
            <strong>💳 Transacción Registrada:</strong> Método: <strong><?= strtoupper(str_replace('_', ' ', htmlspecialchars($metodo_pago, ENT_QUOTES, 'UTF-8'))) ?></strong> | Monto: <strong><?= $monto_pago ?> <?= $moneda_pago ?></strong><br>
            Referencia: <code><?= htmlspecialchars($referencia, ENT_QUOTES, 'UTF-8') ?></code>
        </div>

        <div style="margin-top: 15px; padding: 12px; background: #fff; border-radius: 6px; border: 1px solid #cbd5e1; width: 100%; box-sizing: border-box; font-size: 13px;">
            <strong>Nota de Orientación y Referencia:</strong><br>
            A tus <?= $edad ?> años, mantener un consumo energético estimado en <strong><?= round($tdee) ?> kcal diarias</strong> bajo el enfoque seleccionado permite optimizar la composición corporal y preservar la masa muscular activa.
        </div>

        <?php if (!empty($notas_entrenador)): ?>
            <div class="notas-box">
                <strong>📝 Observaciones Registradas del Entrenador:</strong><br>
                <?= nl2br(htmlspecialchars($notas_entrenador, ENT_QUOTES, 'UTF-8')) ?>
            </div>
        <?php endif; ?>

        <?php if ($rutina_asignada): ?>
            <div class="rutina-box">
                <h4>💪 Pautas Sugeridas en Fuerza Funcional y Vitalidad: <?= htmlspecialchars($rutina_asignada['nombre_rutina'], ENT_QUOTES, 'UTF-8') ?></h4>
                <p><strong>Frecuencia sugerida:</strong> <?= intval($rutina_asignada['frecuencia_semanal']) ?> días por semana</p>
                <hr style="border:0; border-top:1px solid #e2e8f0; margin: 8px 0;">
                <pre><?= htmlspecialchars($rutina_asignada['descripcion'], ENT_QUOTES, 'UTF-8') ?></pre>
            </div>
        <?php endif; ?>
    </div>

    <div class="legal-section">
        <strong>⚠️ Descargo de Responsabilidad Informativa:</strong> Sistema de referencia basado en normativas de la OMS, Deurenberg y Harris-Benedict.<br>
        <a href="terminos.php" target="_blank">Leer Términos, Condiciones y Criterios Oficiales completos</a>
    </div>

    <a href="index.php" class="btn-nueva">Realizar Nueva Evaluación</a>
</div>
</body>
</html>