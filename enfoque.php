<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: evaluacion.php");
    exit;
}
$nombre = $_POST['nombre'];
$edad = $_POST['edad'];
$genero = $_POST['genero'];
$peso = $_POST['peso'];
$altura = $_POST['altura'];
$imc = $_POST['imc'];
$estado = $_POST['estado'];
$metodo_pago = $_POST['metodo_pago'];
$referencia = $_POST['referencia'];
$monto_pago = $_POST['monto_pago'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>GlobalFit Index | Paso 3: Enfoque y Entrenador</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .container { max-width: 600px; background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin: auto; border: 1px solid #e2e8f0; }
        h2 { color: #0f172a; text-align: center; margin-bottom: 5px; font-size: 22px; }
        .sub-header { text-align: center; font-size: 13px; color: #64748b; margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #334155; font-size: 14px; }
        select, textarea { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 14px; background: #f8fafc; }
        textarea { resize: vertical; height: 80px; }
        button { background: #0284c7; color: #fff; border: none; padding: 12px; width: 100%; border-radius: 6px; font-size: 15px; cursor: pointer; font-weight: bold; transition: background 0.2s; margin-top: 15px; }
        button:hover { background: #0369a1; }
    </style>
</head>
<body>
<div class="container">
    <h2>GlobalFit Index</h2>
    <div class="sub-header">Paso 3: Enfoque, Actividad y Observaciones del Entrenador</div>

    <form action="resultado.php" method="POST">
        <!-- Mantener todos los datos anteriores ocultos -->
        <input type="hidden" name="nombre" value="<?= htmlspecialchars($nombre) ?>">
        <input type="hidden" name="edad" value="<?= $edad ?>">
        <input type="hidden" name="genero" value="<?= $genero ?>">
        <input type="hidden" name="peso" value="<?= $peso ?>">
        <input type="hidden" name="altura" value="<?= $altura ?>">
        <input type="hidden" name="imc" value="<?= $imc ?>">
        <input type="hidden" name="estado" value="<?= htmlspecialchars($estado) ?>">
        <input type="hidden" name="metodo_pago" value="<?= htmlspecialchars($metodo_pago) ?>">
        <input type="hidden" name="referencia" value="<?= htmlspecialchars($referencia) ?>">
        <input type="hidden" name="monto_pago" value="<?= $monto_pago ?>">

        <div class="form-group">
            <label>Enfoque de Referencia (Matiz Eutrófico):</label>
            <select name="subtipo">
                <option value="mantenimiento">Salud General y Mantenimiento</option>
                <option value="tonificacion" selected>Tonificación y Recomposición Activa</option>
            </select>
        </div>
        <div class="form-group">
            <label>Nivel de Actividad Habitual:</label>
            <select name="actividad">
                <option value="1.2">Sedentario (poco o sin ejercicio)</option>
                <option value="1.375" selected>Ligera (1-3 días/sem)</option>
                <option value="1.55">Moderada (3-5 días/sem)</option>
                <option value="1.725">Intensa (6-7 días/sem)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Observaciones y Notas del Entrenador:</label>
            <textarea name="notas_entrenador" placeholder="Ej. Movilidad articular adecuada. Enfocar pauta en soporte postural."></textarea>
        </div>

        <button type="submit">Generar Reporte Final y Rutina OMS →</button>
    </form>
</div>
</body>
</html>