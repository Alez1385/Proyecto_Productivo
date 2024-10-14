<?php
session_start();


// Asegúrate de que esta línea esté al principio de tu archivo
include('scripts/conexion.php'); // Ajusta la ruta según sea necesario


// Consulta para obtener los slides del carrusel
$result = $conn->query("SELECT * FROM carousel ORDER BY order_index ASC");
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Corsacor</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900" rel="stylesheet">

    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">

    <link rel="stylesheet" href="css/aos.css">

    <link rel="stylesheet" href="css/ionicons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Librería jQuery para AJAX -->
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/inscripcion-handler.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">
	  
	  
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar ftco-navbar-light site-navbar-target" id="ftco-navbar">
	    <div class="container">
	      <a class="navbar-brand" href="#home-section" class="nav-link">Corsacor</a>
	      <button class="navbar-toggler js-fh5co-nav-toggle fh5co-nav-toggle" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="oi oi-menu"></span> Menu
	      </button>

	      <div class="collapse navbar-collapse" id="ftco-nav">
	        <ul class="navbar-nav nav ml-auto">
	          <li class="nav-item"><a href="#home-section" class="nav-link"><span>Inicio</span></a></li>
			  <li class="nav-item"><a href="#services-section" class="nav-link"><span>Cursos</span></a></li>
	          <li class="nav-item"><a href="#resume-section" class="nav-link"><span>Resumen</span></a></li>
	          <li class="nav-item"><a href="#contact-section" class="nav-link"><span>Contactos</span></a></li>
			  <li class="nav-item"><a href="/login/login.php" class="nav-link" target= "_blank"><span>Login</span></a></li>
	        </ul>
	      </div>
	    </div>
	  </nav>
    <!-- incio del carrousel -->
	  <section id="home-section" class="hero">
	  <div class="home-slider owl-carousel">
    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="slider-item">
        <div class="overlay"></div>
        <div class="container"> 
            <div class="row d-md-flex no-gutters slider-text align-items-end justify-content-end" data-scrollax-parent="true">
                <div class="one-third js-fullheight order-md-last img" style="border-radius: 9px; background-image:url(uploads/<?php echo $row['image']; ?>);">
                    <div style="border-radius: 9px; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5);"></div>
                    <div class="overlay"></div>
                </div>
                <div class="one-forth d-flex align-items-center ftco-animate" data-scrollax="properties: { translateY: '70%' }">
                    <div class="text">
						<br><br>
                        <span class="subheading">Inicio: <?= $row['fecha_curso_inicio'] ?> | Fin: <?= $row['fecha_curso_fin'] ?></span>
                        <h1 class="mb-4 mt-3"><?php echo $row['title']; ?></h1>
                        <h2 class="mb-4" style="color: #fff;"><?php echo $row['description']; ?></h2>
                        <p><a href="#" class="btn btn-primary py-3 px-4">Incribirse</a> <a href="#" class="btn btn-white btn-outline-white py-3 px-4">My works</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>	
    </section>

	<!-- HTML para mostrar todos los cursos en cuadros independientes -->
    <section class="ftco-section" id="services-section">
    	<div class="container">
    		<div class="row justify-content-center py-5 mt-5">
          <div class="col-md-12 heading-section text-center ftco-animate">
          	<h1 class="big big-2">Cursos </h1>
            <h2 class="mb-4">Cursos Activos</h2>
            <p>Aquí podrás encontrar todos nuestros cursos. Recuerda seleccionar uno para inscribirte.</p>
          </div>
        </div>
    		<div class="row">
        <div class="container">
<?php

$sql = "SELECT id_curso, nombre_curso, descripcion, nivel_educativo, duracion, icono FROM cursos";
$result = $conn->query($sql);
?>
 <div class="row">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4 text-center d-flex ftco-animate">';
                echo '  <div class="services-1">';
                echo '      <span class="icon course-icon">';
                if (!empty($row["icono"])) {
                    echo '          <img src="../../uploads/icons/' . htmlspecialchars($row["icono"]) . '" alt="Icono del curso" class="course-icon-img">';
                } else {
                    echo '          <i class="flaticon-analysis"></i>';
                }
                echo '      </span>';
                echo '      <div class="desc">';
                echo '          <h3 class="mb-5">' . htmlspecialchars($row["nombre_curso"]) . '</h3>';
                echo '          <p class="text-curso">Categoria: ' . htmlspecialchars($row["descripcion"]) . '</p>';
                echo '          <p class="text-curso">Nivel: ' . htmlspecialchars(ucfirst($row["nivel_educativo"])) . '</p>';
                echo '          <p class="text-curso">Duración: ' . htmlspecialchars($row["duracion"]) . ' semanas</p>';
                echo '          <a href="#" class="btn btn-primary inscribirse-btn" data-curso-id="' . htmlspecialchars($row["id_curso"]) . '">Inscribirse</a>';
                echo '      </div>';
                echo '  </div>';
                echo '</div>';
            }
        } else {
            echo '<p>No hay cursos disponibles actualmente.</p>';
        }
        ?>
    </div>
</div>
<?php
// Cerrar la conexión
$conn->close();
?>
</div>
    </section>

<!-- resumen de los cursos -->
<?php
// Incluir el archivo de conexión
include('scripts/conexion.php'); // Asegúrate de que aquí no se cierre $conn

// Consulta SQL para obtener todos los cursos de la tabla resume_cursos
$sql = "SELECT * FROM resume_cursos";
$result = $conn->query($sql);

// Verificar si la consulta fue exitosa
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>

<section class="ftco-section ftco-no-pb" id="resume-section">
    <div class="container">
        <div class="row justify-content-center pb-5">
            <div class="col-md-10 heading-section text-center ftco-animate">
                <h1 class="big big-2">Resumen</h1>
                <h2 class="mb-4">Resumen</h2>
                <p>Bienvenidos a todos los cursos del Colegio Sagrado Corazón de Jesús. ¡Prepárense para una experiencia de aprendizaje inolvidable!</p>
            </div>
        </div>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col-md-6">
                        <div class="resume-wrap ftco-animate ftco-animate-resume">
                            <span class="date"><?php echo htmlspecialchars($row['dia']); ?></span>
                            <h2><?php echo htmlspecialchars($row['nombre']); ?></h2>
                            <span class="position"><?php echo htmlspecialchars($row['lugar']); ?></span>
                            <p class="mt-4"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay cursos disponibles en este momento.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
// Verifica que $conn no haya sido cerrado antes, y ciérralo aquí si es necesario
if ($conn) {
    $conn->close(); // Solo cerrar si no se cerró antes
}
?>

    <section class="ftco-section contact-section ftco-no-pb" id="contact-section">
      <div class="container">
      	<div class="row justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section text-center ftco-animate">
            <h1 class="big big-2">Contacto</h1>
            <h2 class="mb-4">Contáctanos</h2>
            <p>Comunícate con nosotros. Aquí encontrarás el teléfono y correo de la Secretaría de Rectoría para resolver tus dudas.</p>
          </div>
        </div>

        <div class="row d-flex contact-info mb-5">
          <div class="col-md-6 col-lg-3 d-flex ftco-animate">
          	<div class="align-self-stretch box p-4 text-center">
          		<div class="icon d-flex align-items-center justify-content-center">
          			<span class="icon-map-signs"></span>
          		</div>
          		<h3 class="mb-4">Nombre Secretaria</h3>
	            <p>Edy Montañez</p>
	          </div>
          </div>
          <div class="col-md-6 col-lg-3 d-flex ftco-animate">
          	<div class="align-self-stretch box p-4 text-center">
          		<div class="icon d-flex align-items-center justify-content-center">
          			<span class="icon-phone2"></span>
          		</div>
          		<h3 class="mb-4">Numero de contacto</h3>
	            <p><a href="tel://1234567920">5503455 Ext. 101</a></p>
	          </div>
          </div>
          <div class="col-md-6 col-lg-3 d-flex ftco-animate">
          	<div class="align-self-stretch box p-4 text-center">
          		<div class="icon d-flex align-items-center justify-content-center">
          			<span class="icon-paper-plane"></span>
          		</div>
          		<h3 class="mb-4">Email </h3>
	            <p><a href="mailto:info@yoursite.com">secretaria.rectoria@corsaje.edu.co</a></p>
	          </div>
          </div>
          <div class="col-md-6 col-lg-3 d-flex ftco-animate">
          	<div class="align-self-stretch box p-4 text-center">
          		<div class="icon d-flex align-items-center justify-content-center">
          			<span class="icon-globe"></span>
          		</div>
          		<h3 class="mb-4">Sitio Web</h3>
	            <p><a href="https://www.corsaje.edu.co/" target="_blank">Corsaje</a></p>
	          </div>
          </div>
        </div>

      
    <footer class="ftco-footer ftco-section">
      <div class="container">
        <div class="row mb-5">
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">I.E. SAGRADO CORAZÓN DE JESÚS</h2>
              <p>JUNTOS CONSTRUIMOS EL CORSAJE QUE DESEAMOS</p>
              <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
          
                <li class="ftco-animate"><a href="https://www.facebook.com/corsajelasalle?_rdc=1&_rdr"><span class="icon-facebook"></span></a></li>
                <li class="ftco-animate"><a href="https://www.instagram.com/corsajelasalle/"><span class="icon-instagram"></span></a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4 ml-md-4">
              <h2 class="ftco-heading-2">Links</h2>
              <ul class="list-unstyled">
                <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Inicio</a></li>
                <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Acerca De</a></li>
                <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Cursos</a></li>
                <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Proyectos</a></li>
                <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Contáctanos</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
             <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">Servicios</h2>
              <ul class="list-unstyled">
                <li><a href="https://www.corsaje.edu.co/wp-content/uploads/2022/01/PLAN-DE-ESTUDIOS-2022..pdf"><span class="icon-long-arrow-right mr-2"></span>Plan Estudios</a></li>
                <li><a href="https://www.corsaje.edu.co/area-tecnica/"><span class="icon-long-arrow-right mr-2"></span>Area Tecnica</a></li>
                <li><a href="https://www.corsaje.edu.co/category/pastoral/"><span class="icon-long-arrow-right mr-2"></span>Pastoral</a></li>
                <li><a href="https://www.corsaje.edu.co/category/complementarias/"><span class="icon-long-arrow-right mr-2"></span>Actividades Complementarias</a></li>

              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
            	<h2 class="ftco-heading-2">Have a Questions?</h2>
            	<div class="block-23 mb-3">
	              <ul>
	                <li><span class="icon icon-map-marker"></span><span class="text">Calle 16 #3-60 La Playa, Cúcuta - Norte de Santander</span></li>
	                <li><a href="#"><span class="icon icon-phone"></span><span class="text">5503455</span></a></li>
	                <li><a href="#"><span class="icon icon-envelope"></span><span class="text"> corsaje@corsaje.edu.co </span></a></li>
	              </ul>
	            </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">

            <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
  Copyright &copy;<script>document.write(new Date().getFullYear());</script> I.E. SAGRADO CORAZÓN DE JESÚS <i class="icon-heart color-danger" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Capon y Camilo</a>
  <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
          </div>
        </div>
      </div>
    </footer>
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

  <script src="js/jquery.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/jquery.waypoints.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.magnific-popup.min.js"></script>
  <script src="js/aos.js"></script>
  <script src="js/jquery.animateNumber.min.js"></script>
  <script src="js/scrollax.min.js"></script>
  <script src="js/main.js"></script>
  <!-- JavaScript para manejar el modal y filtrar los cursos activos -->
 
    
  </body>
</html> 