<?php
    
    
  include("database_connection.php");
  require "../components/head.php";

  //CAMBIARLO A POST, ya que ahora es desde un formulario

  if (isset($_POST['add-song-to-album'])){
    
    //DEBERIA VERIFICAR SI LA CANCIÓN Y EL ALBUM EXISTEN, PERO ESO LO VE LA FOREIGN KEY, YA QUE NO PERMITIRÁ KEYS QUE NO EXISTAN
    
    $username = $_SESSION["userName"];
    $id_album = $_POST['id_album'];
    $id_cancion = $_POST["id_cancion"];

    //Verifico que el album sea del artista
    $query = "SELECT * FROM albums WHERE id_album = $id_album";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result);

    if( $row['autor'] != $username || empty($id_album)){
      header("Location: ../songs.php?error=operacioninvalida");
      exit();
    }

    //Verifico que la canción a agregar sea del artista
    $query2 = "SELECT * FROM canciones WHERE id_cancion = $id_cancion";
    $result2 = mysqli_query($conn, $query2);
    $row2 = mysqli_fetch_array($result2);

    if( $row2['autor'] != $username || empty($id_cancion)){
      header("Location: ../songs.php?error=operacioninvalida");
      exit();
    }

    $sql = "INSERT INTO albums_incluyendo_canciones (id_album, id_cancion) VALUES (?,?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)){
      header("Location: ../songs.php?error=sqlerror");
      exit();
    }

    else{
      mysqli_stmt_bind_param($stmt, "ss", $id_album, $id_cancion);
      $result = mysqli_stmt_execute($stmt);

      if ($result){
        header("Location: ../songs.php?success=add-song-to-album");
        exit();
      }
      header("Location: ../songs.php?error=sqlerror");
      exit();
    }
  }

  else{
    header("Location: ../songs.php?error=operacioninvalida");
    exit();
  }
?>