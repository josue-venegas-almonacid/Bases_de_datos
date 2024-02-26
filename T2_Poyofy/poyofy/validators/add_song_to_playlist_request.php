<?php
    
    
  include("database_connection.php");
  require "../components/head.php";

  //CAMBIARLO A POST, ya que ahora es desde un formulario

  if (isset($_POST['add-song-to-playlist'])){
    
    //DEBERIA VERIFICAR SI LA CANCIÓN Y LA PLAYLIST EXISTEN, PERO ESO LO VE LA FOREIGN KEY, YA QUE NO PERMITIRÁ KEYS QUE NO EXISTAN EN PERSONAS
    
    $username = $_SESSION["userName"];
    $id_playlist = $_POST['id_playlist'];
    $id_cancion = $_POST["id_cancion"];

    //Verifico que la playlist sea del usuario
    $query = "SELECT * FROM playlists WHERE id_playlist = $id_playlist";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result);

    if( $row['creador'] != $username || empty($id_cancion) || empty($id_playlist)){
      header("Location: ../songs.php?error=operacioninvalida");
      exit();
    }

    $sql = "INSERT INTO playlists_conteniendo_canciones (id_playlist, id_cancion) VALUES (?,?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)){
      header("Location: ../songs.php?error=sqlerror");
      exit();
    }

    else{
      mysqli_stmt_bind_param($stmt, "ss", $id_playlist, $id_cancion);
      $result = mysqli_stmt_execute($stmt);

      if ($result){
        header("Location: ../songs.php?success=add-song-to-playlist");
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