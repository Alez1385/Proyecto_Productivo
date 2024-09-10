<?php
// Include database connection
require_once '../../scripts/conexion.php';

// Function to log errors
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, '../../logs/error.log');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $categoryId = isset($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $categoryName = isset($_POST['categoria']) ? $_POST['categoria'] : '';

    try {
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO categorias (nombre_categoria) VALUES (?)");
            $stmt->bind_param("s", $categoryName);
            $stmt->execute();
            $stmt->close();
            echo "<p>Category added successfully.</p>";
        } elseif ($action === 'edit' && $categoryId) {
            $stmt = $conn->prepare("UPDATE categorias SET nombre_categoria = ? WHERE id_categoria = ?");
            $stmt->bind_param("si", $categoryName, $categoryId);
            $stmt->execute();
            $stmt->close();
            echo "<p>Category updated successfully.</p>";
        } elseif ($action === 'delete' && $categoryId) {
            $stmt = $conn->prepare("DELETE FROM categorias WHERE id_categoria = ?");
            $stmt->bind_param("i", $categoryId);
            $stmt->execute();
            $stmt->close();
            echo "<p>Category deleted successfully.</p>";
        } else {
            echo "<p>Invalid action.</p>";
        }
    } catch (Exception $e) {
        logError("Error modifying category: " . $e->getMessage());
        echo "<p>Error occurred: " . $e->getMessage() . "</p>";
    }
}

// Retrieve categories for display
$sql = "SELECT * FROM categorias";
$categories = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Categorías</title>
    <link rel="stylesheet" href="css/categoria_curso.css">
</head>

<body>
    <div class="dashboard-container">
        <header class="header">
            <h1>Modificar Categorías</h1>
        </header>
        <section class="content">
            <!-- Formulario para agregar/editar categoría -->
            <form method="post" action="modificar_categorias.php">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id_categoria" id="categoryId">
                <label for="categoryName">nombre_categoria de Categoría:</label>
                <input type="text" id="categoryName" name="categoria" required>
                <button type="submit" class="submit-btn">Guardar</button>
            </form>

            <!-- Lista de categorías existentes -->
            <h2>Categorías Existentes</h2>
            <ul id="categoryList">
                <?php while ($row = $categories->fetch_assoc()) { ?>
                    <li>
                        <?php echo $row["nombre_categoria"]; ?>
                        <button onclick="editCategory(<?php echo $row["id_categoria"]; ?>, '<?php echo $row["nombre_categoria"]; ?>')">Editar</button>
                        <button onclick="deleteCategory(<?php echo $row["id_categoria"]; ?>)">Eliminar</button>
                    </li>
                <?php } ?>
            </ul>
        </section>
    </div>
    <script>
        function editCategory(id, name) {
            document.getElementById('formAction').value = 'edit';
            document.getElementById('categoryId').value = id;
            document.getElementById('categoryName').value = name;
        }

        function deleteCategory(id) {
            if (confirm("Are you sure you want to delete this category?")) {
                const form = document.createElement('form');
                form.method = 'post';
                form.action = 'modificar_categorias.php';

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                form.appendChild(actionInput);

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id_categoria';
                idInput.value = id;
                form.appendChild(idInput);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>
