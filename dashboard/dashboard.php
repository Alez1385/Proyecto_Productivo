<?php
session_start();
include('../scripts/conexion.php');

if (!isset($_SESSION['username'])) {
    header("Location: ../login/login.php");
    exit();
}

$username = $_SESSION['username'];
$sql = "SELECT * FROM usuario WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Consulta para obtener todos los usuarios
$sql_users = "SELECT * FROM usuario";
$usersResult = $conn->query($sql_users);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    setcookie('username', '', time() - 3600, "/", "", true, true);
    header("Location: ../login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <title>Dashboard</title>
</head>

<body>

    <div class="container">
        <!-- Sidebar Section -->
        <aside>
            <div class="toggle">
                <div class="logo">
                    <img src="images/logo.png" alt="Logo">
                    <h2>Asmr<span class="danger">Prog</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>

            <div class="sidebar">
                <a href="#">
                    <span class="material-icons-sharp">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">person_outline</span>
                    <h3>Users</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">receipt_long</span>
                    <h3>History</h3>
                </a>
                <a href="#" class="active">
                    <span class="material-icons-sharp">insights</span>
                    <h3>Analytics</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">mail_outline</span>
                    <h3>Tickets</h3>
                    <span class="message-count">27</span>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">inventory</span>
                    <h3>Sale List</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">report_gmailerrorred</span>
                    <h3>Reports</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">settings</span>
                    <h3>Settings</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">add</span>
                    <h3>New Login</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">logout</span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
        <!-- End of Sidebar Section -->

        <!-- Main Content -->
        <main>
            <h1>Analytics</h1>
            <!-- Analyses -->

            <!-- End of Analyses -->

            <!-- New Users Section -->
            <div class="new-users">
                <h2>New Users</h2>
                <div class="user-list">
                    <?php while ($row = $usersResult->fetch_assoc()) { ?>
                        <div class="user">
                            <img src="../uploads/<?php echo htmlspecialchars(basename($row['foto'])); ?>" alt="User Image">
                            <h2><?php echo htmlspecialchars($row['nombre'] . " " . $row['apellido']); ?></h2>
                            <p><?php echo htmlspecialchars($row['mail']); ?></p>
                        </div>
                    <?php } ?>
                    <div class="user">
                        <img src="images/plus.png" alt="Add User">
                        <h2>More</h2>
                        <p>New User</p>
                    </div>
                </div>
            </div>
            <!-- End of New Users Section -->

            <!-- Recent Orders Table -->
            <div class="recent-orders">
                <h2>Recent Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Course Number</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated here -->
                    </tbody>
                </table>
                <a href="#">Show All</a>
            </div>
            <!-- End of Recent Orders -->

        </main>
        <!-- End of Main Content -->

        <!-- Right Section -->
        <div class="right-section">
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">menu</span>
                </button>
                <div class="dark-mode">
                    <span class="material-icons-sharp active">light_mode</span>
                    <span class="material-icons-sharp">dark_mode</span>
                </div>

                <div class="profile">
                    <div class="info">
                        <p>Hey, <b><?php echo htmlspecialchars($user['nombre']); ?></b></p>
                        <small class="text-muted">
                            <?php
                            $tipo_usuario_id = $user['id_tipo_usuario'];
                            $sql_tipo_usuario = "SELECT nombre FROM tipo_usuario WHERE id_tipo_usuario = ?";
                            $stmt_tipo_usuario = $conn->prepare($sql_tipo_usuario);
                            $stmt_tipo_usuario->bind_param("i", $tipo_usuario_id);
                            $stmt_tipo_usuario->execute();
                            $result_tipo_usuario = $stmt_tipo_usuario->get_result();
                            $tipo_usuario = $result_tipo_usuario->fetch_assoc();
                            echo htmlspecialchars($tipo_usuario['nombre']);
                            $stmt_tipo_usuario->close();
                            ?>
                        </small>
                    </div>
                    <div class="profile-photo">
                        <img src="../uploads/<?php echo htmlspecialchars(basename($user['foto'])); ?>" alt="User Image">
                    </div>
                </div>
            </div>
            <!-- End of Nav -->

            <div class="user-profile">
                <div class="logo">
                    <img src="images/logo.png" alt="Logo">
                    <h2>AsmrProg</h2>
                    <p>Fullstack Web Developer</p>
                </div>
            </div>

            <div class="reminders">
                <div class="header">
                    <h2>Reminders</h2>
                    <span class="material-icons-sharp">notifications_none</span>
                </div>

                <div class="notification">
                    <div class="icon">
                        <span class="material-icons-sharp">volume_up</span>
                    </div>
                    <div class="content">
                        <div class="info">
                            <h3>Workshop</h3>
                            <small class="text_muted">08:00 AM - 12:00 PM</small>
                        </div>
                        <span class="material-icons-sharp">more_vert</span>
                    </div>
                </div>

                <div class="notification deactive">
                    <div class="icon">
                        <span class="material-icons-sharp">edit</span>
                    </div>
                    <div class="content">
                        <div class="info">
                            <h3>Workshop</h3>
                            <small class="text_muted">08:00 AM - 12:00 PM</small>
                        </div>
                        <span class="material-icons-sharp">more_vert</span>
                    </div>
                </div>

                <div class="notification add-reminder">
                    <div>
                        <span class="material-icons-sharp">add</span>
                        <h3>Add Reminder</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Right Section -->

    </div>

    <script src="scripts/orders.js"></script>
    <script src="scripts/index.js"></script>
</body>

</html>
