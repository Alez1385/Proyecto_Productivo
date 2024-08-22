<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Profesor</title>
    <link rel="stylesheet" href="../../css/form.css">
</head>
<body>
    <div class="form-container">
        <h2>Registrar Profesor</h2>
        <form action="procesar_profesor.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" id="nombre" name="nombre" placeholder="Nombre" required>
            </div>

            <div class="form-group">
                <input type="text" id="apellido" name="apellido" placeholder="Apellido" required>
            </div>

            <div class="form-group">
                <input type="email" id="correo" name="correo" placeholder="Correo" required>
            </div>

            <div class="form-group">
                <input type="tel" id="telefono" name="telefono" placeholder="Teléfono" required>
            </div>

            <div class="form-group">
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" placeholder="Fecha de Nacimiento" required>
            </div>

            <div class="form-group">
                <input type="text" id="direccion" name="direccion" placeholder="Dirección" required>
            </div>

            <div class="form-group">
                <input type="file" id="foto" name="foto" accept="image/*" placeholder="Foto">
            </div>

            <div class="form-group">
                <input type="text" id="documento_identidad" name="documento_identidad" placeholder="Documento de Identidad" required>
            </div>

            <div class="form-group">
                <input type="text" id="nivel_educativo" name="nivel_educativo" placeholder="Nivel Educativo" required>
            </div>

            <div class="form-group">
                <textarea id="observaciones" name="observaciones" placeholder="Observaciones"></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>
        </form>
    </div>
</body>
</html>
