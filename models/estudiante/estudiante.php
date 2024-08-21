<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="../../css/form.css">
</head>
<body>
    <div class="form-container">
        <form class="course-form" action="scripts/register_student.php" method="post">
            <h2>Student Registration</h2>

            <div class="form-group">
                <input type="text" placeholder="First Name" name="nombre" required>
            </div>

            <div class="form-group">
                <input type="text" placeholder="Last Name" name="apellido" required>
            </div>

            <div class="form-group">
                <input type="email" placeholder="Email" name="correo" required>
            </div>

            <div class="form-group">
                <input type="tel" placeholder="Phone Number" name="telefono" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
            </div>

            <div class="form-group">
                <input type="date" placeholder="Birth Date" name="fecha_nacimiento" required>
            </div>

            <div class="form-group">
                <input type="text" placeholder="Address" name="direccion" required>
            </div>

            <div class="form-group">
                <select name="genero" required>
                    <option value="" disabled selected>Gender</option>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                    <option value="O">Other</option>
                </select>
            </div>

            <div class="form-group">
                <input type="date" placeholder="Registration Date" name="fecha_registro" required>
            </div>

            <div class="form-group">
                <select name="estado" required>
                    <option value="" disabled selected>Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <input type="text" placeholder="ID Document" name="documento_identidad" required>
            </div>

            <div class="form-group">
                <select name="nivel_educativo" required>
                    <option value="" disabled selected>Education Level</option>
                    <option value="primary">Primary</option>
                    <option value="secondary">Secondary</option>
                    <option value="tertiary">Tertiary</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <textarea placeholder="Observations" name="observaciones" rows="4"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Register Course</button>
        </form>
    </div>
</body>
</html>
