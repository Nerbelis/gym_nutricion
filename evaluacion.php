<?php
$calculado = false;
$nombre = $edad = $genero = $peso = $altura = "";
$imc = 0;
$es_eutrofico = false;
$estado_nutricional = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calcular_imc'])) {
    $nombre    = trim($_POST['nombre']);
    $edad      = intval($_POST['edad']);
    $genero    = $_POST['genero'];
    $peso      = floatval($_POST['peso']);
    $altura_cm = intval($_POST['altura']);

    $altura_m = $altura_cm / 100;
    $imc = $peso / ($altura_m * $altura_m);

    if ($imc >= 18.5 && $imc <= 24.9) {
        $es_eutrofico = true;
        $estado_nutricional = "Normal Eutrófico";
    } else {
        if ($imc < 18.5) {
            $estado_nutricional = "Bajo peso";
        } elseif ($imc >= 25.0 && $imc <= 29.9) {
            $estado_nutricional = "Sobrepeso Leve";
        } else {
            $estado_nutricional = "Obesidad";
        }
    }
    $calculado = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NercoFit| Paso 1: Verificación de Eutrofia</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .container { max-width: 600px; background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin: auto; border: 1px solid #e2e8f0; }
        h2 { color: #0f172a; text-align: center; margin-bottom: 5px; font-size: 22px; }
        .sub-header { text-align: center; font-size: 13px; color: #64748b; margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #334155; font-size: 14px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 14px; background: #f8fafc; }
        button { background: #0284c7; color: #fff; border: none; padding: 12px; width: 100%; border-radius: 6px; font-size: 15px; cursor: pointer; font-weight: bold; transition: background 0.2s; margin-top: 15px; }
        button:hover { background: #0369a1; }
        .imc-box-exito { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .imc-box-exito h3 { margin: 0 0 8px 0; color: #166534; font-size: 18px; }
        .imc-box-exito p { margin: 0; color: #15803d; font-size: 14px; line-height: 1.5; }
        .imc-box-alerta { background: #fef2f2; border: 1px solid #fecaca; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .imc-box-alerta h3 { margin: 0 0 8px 0; color: #991b1b; font-size: 18px; }
        .imc-box-alerta p { margin: 0; color: #b91c1c; font-size: 14px; line-height: 1.5; }
        .btn-reiniciar { display: inline-block; background: #64748b; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: bold; margin-top: 15px; text-align: center; }
        .back-link { display: block; text-align: center; margin-top: 15px; font-size: 13px; color: #64748b; text-decoration: none; }
        .back-link:hover { color: #0284c7; }
    </style>
</head>
<body>
<div class="container">
    <h2>NercoFit</h2>
    <div class="sub-header">Paso 1: Verificación de Perfil Eutrófico (50+)</div>

    <?php if (!$calculado): ?>
        <form method="POST">
            <div class="form-group">
                <label>Nombre del Socio / Usuario:</label>
                <input type="text" name="nombre" required placeholder="Ej. Maria Suarez">
            </div>
            <div class="form-group">
                <label>Edad (años):</label>
                <input type="number" name="edad" required placeholder="Ej. 50">
            </div>
            <div class="form-group">
                <label>Género:</label>
                <select name="genero">
                    <option value="femenino">Femenino</option>
                    <option value="masculino">Masculino</option>
                </select>
            </div>
            <div class="form-group">
                <label>Peso (kg):</label>
                <input type="number" step="0.1" name="peso" required placeholder="Ej. 71.1">
            </div>
            <div class="form-group">
                <label>Altura (cm):</label>
                <input type="number" name="altura" required placeholder="Ej. 173">
            </div>

            <button type="submit" name="calcular_imc">Verificar Perfil Eutrófico</button>
        </form>
    <?php else: ?>
        <?php if ($es_eutrofico): ?>
            <div class="imc-box-exito">
                <h3>✓ Perfil Verificado: Normal Eutrófico</h3>
                <p>
                    <strong><?= htmlspecialchars($nombre) ?></strong>, tu IMC es de <strong><?= round($imc, 2) ?></strong> (<?= $estado_nutricional ?>).<br>
                    Cumples con el perfil óptimo de la evaluación para mayores de 50 años.
                </p>
            </div>

            <!-- Redirige al Paso 2 (Pago) pasando los datos de forma segura -->
            <form action="pago.php" method="POST">
                <input type="hidden" name="nombre" value="<?= htmlspecialchars($nombre) ?>">
                <input type="hidden" name="edad" value="<?= $edad ?>">
                <input type="hidden" name="genero" value="<?= $genero ?>">
                <input type="hidden" name="peso" value="<?= $peso ?>">
                <input type="hidden" name="altura" value="<?= $_POST['altura'] ?>">
                <input type="hidden" name="imc" value="<?= round($imc, 2) ?>">
                <input type="hidden" name="estado" value="<?= $estado_nutricional ?>">

                <button type="submit" style="background: #16a34a;">Continuar al Módulo de Pago →</button>
            </form>
        <?php else: ?>
            <div class="imc-box-alerta">
                <h3>Perfil No Compatible</h3>
                <p>
                    <strong><?= htmlspecialchars($nombre) ?></strong>, tu IMC es de <strong><?= round($imc, 2) ?></strong> (<?= $estado_nutricional ?>).<br><br>
                    ⚠️ <em>Esta evaluación está diseñada exclusivamente para personas en rango de Normal Eutrófico. No es posible continuar con el proceso regular.</em>
                </p>
            </div>
            <div style="text-align: center;">
                <a href="evaluacion.php" class="btn-reiniciar">Intentar de nuevo</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <a href="index.php" class="back-link">← Volver al Inicio</a>
</div>
</body>
</html>