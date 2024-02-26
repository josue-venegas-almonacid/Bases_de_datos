<!--
  Archivo: logout_request.php
  Función: Validar el cierre de sesión del usuario
-->

<?php
session_start();
session_unset();
session_destroy();
header("Location: ../index.php?success=logout");
