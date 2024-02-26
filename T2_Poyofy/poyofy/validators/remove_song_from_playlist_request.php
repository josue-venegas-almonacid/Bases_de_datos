<?php
    include("database_connection.php");
    require "../components/head.php";

    if ( (isset($_GET['song'])) && (!empty($_GET['song'])) && (isset($_GET['playlist'])) && (!empty($_GET['playlist'])) ){
      //DEBERIA VERIFICAR SI LA CANCION A DESVINCULAR EXISTE, AUNQUE ACTUALMENTE NO AFECTA BORRAR ALGO QUE NO EXISTE

      $id_cancion = $_GET['song'];
      $id_playlist = $_GET['playlist'];

      //Verificar que la playlist sea propiedad del usuario actual
      $query = "SELECT creador FROM playlists WHERE id_playlist = '$id_playlist'";
      $result = mysqli_query($conn, $query);
      $row = mysqli_fetch_array($result);

      if ($row['creador'] != $_SESSION["userName"]){
        header("Location: ../songs.php?error=operacioninvalida");
        exit();
      }

      $sql = "DELETE FROM playlists_conteniendo_canciones WHERE id_playlist = ? AND id_cancion = ?";
      $stmt = mysqli_stmt_init($conn);

      if (!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../songs.php?error=sqlerror");
        exit();
      }

      else{
        mysqli_stmt_bind_param($stmt, "ss", $id_playlist, $id_cancion);
        $result = mysqli_stmt_execute($stmt);
        
        if ($result){
          header("Location: ../songs.php?success=unfollow");
          exit();
        }
        header("Location: ../songs.php?error=sqlerror");
      }
    }

    else{
      header("Location: ../songs.php?error=operacioninvalida");
      exit();
    }
?>