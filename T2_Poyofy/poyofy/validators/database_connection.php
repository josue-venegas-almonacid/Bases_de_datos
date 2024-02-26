<!--
  Archivo: database_connection.php
  Función: Conectarse a la base de datos poyofy
-->

<?php
  $servername = "localhost";
  $dBUsername = "root";
  $dBPassword = "";
  $dBName = "poyofy";


  $conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);
  if (!$conn){
    die("Error. No se pudo conectar a la base de datos: ".mysqli_connect_error());
  }
