<?php
    include("database_connection.php");
    require "../components/head.php";

    if ( (isset($_GET['playlist'])) && (!empty($_GET['playlist'])) ){
      //DEBERIA VERIFICAR SI EL USUARIO A DESSEGUIR EXISTE, AUNQUE ACTUALMENTE NO AFECTA BORRAR (desseguir) ALGO QUE NO EXISTE

      $id_playlist = $_GET['playlist'];
      $username_seguidor = $_SESSION["userName"];

      $sql = "DELETE FROM personas_siguiendo_playlists WHERE id_playlist = ? AND username = ?";
      $stmt = mysqli_stmt_init($conn);

      if (!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../playlists.php?error=sqlerror");
        exit();
      }

      else{
        mysqli_stmt_bind_param($stmt, "ss", $id_playlist, $username_seguidor);
        $result = mysqli_stmt_execute($stmt);
        
        if ($result){
          header("Location: ../playlists.php?success=unfollow-playlist");
          exit();
        }
        header("Location: ../playlists.php?error=sqlerror");
      }
    }

    else{
      header("Location: ../playlists.php?error=operacioninvalida");
      exit();
    }
?>