<?php
    include("database_connection.php");
    require "../components/head.php";

    if ( (isset($_GET['song'])) && (!empty($_GET['song'])) ){
      //DEBERIA VERIFICAR SI LA CANCION A DESLIKEAR EXISTE, AUNQUE ACTUALMENTE NO AFECTA BORRAR ALGO QUE NO EXISTE

      $id_cancion = $_GET['song'];
      $username = $_SESSION["userName"];

      $sql = "DELETE FROM usuarios_gustando_canciones WHERE id_cancion = ? AND username = ?";
      $stmt = mysqli_stmt_init($conn);

      if (!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../playlists.php?error=sqlerror");
        exit();
      }

      else{
        mysqli_stmt_bind_param($stmt, "ss", $id_cancion, $username);
        $result = mysqli_stmt_execute($stmt);
        
        if ($result){
          header("Location: ../songs.php?success=unlike-song");
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