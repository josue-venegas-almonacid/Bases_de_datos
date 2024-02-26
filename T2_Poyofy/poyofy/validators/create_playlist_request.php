<!-- SI ES ARTISTA NO PUEDE CREAR PLAYLISTS!!!-->

<?php
  if (isset($_POST['create-playlist'])){
    include("database_connection.php");
    require "../components/head.php";
    
    
    $nombre_playlist = $_POST['nombre_playlist'];
    $creador = $_SESSION["userName"];

    $sql = "INSERT INTO playlists(nombre_playlist, creador) VALUES (?, ?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)){
      header("Location: ../playlists.php?error=sqlerror");
      exit();
    }

    else{
      mysqli_stmt_bind_param($stmt, "ss", $nombre_playlist, $creador);
      $result = mysqli_stmt_execute($stmt);

      if ($result){
          header("Location: ../playlists.php?success=create-playlist");
          exit();
      }
      header("Location: ../playlists.php?error=sqlerror");
      exit();
    }
  }
    
  //Si el usuario no apretó el botón para crear la lista, no puede entrar en esta página
  else{
    header("Location: ../playlists.php?error=operacioninvalida");
    exit();
  }
?>