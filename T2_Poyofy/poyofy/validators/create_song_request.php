<!-- SI ES ARTISTA NO PUEDE CREAR PLAYLISTS!!!-->

<?php
  if (isset($_POST['create-song'])){
    include("database_connection.php");
    require "../components/head.php";
    
    
    $nombre_cancion = $_POST['nombre_canción'];
    $autor = $_SESSION["userName"];
    $genero = $_POST['género'];
    $duracion = $_POST['duración'];
    $anno_publicacion = $_POST['fecha'];

    if (empty($anno_publicacion)) $anno_publicacion = 2020;

    $sql = "INSERT INTO canciones(nombre_cancion, autor, genero, duracion, anno_publicacion) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)){
      header("Location: ../songs.php?error=sqlerror");
      exit();
    }

    else{
        mysqli_stmt_bind_param($stmt, "sssss", $nombre_cancion, $autor, $genero, $duracion, $anno_publicacion);
        $result = mysqli_stmt_execute($stmt);

        if ($result){
          header("Location: ../songs.php?success=create-song");
          exit();
        }
        header("Location: ../songs.php?error=sqlerror");
        exit();
    }
  }
    
  //Si el usuario no apretó el botón para crear la lista, no puede entrar en esta página
  else{
    header("Location: ../songs.php?error=operacioninvalida");
    exit();
  }
?>