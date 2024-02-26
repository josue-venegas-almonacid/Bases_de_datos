<?php
    include("database_connection.php");
    require "../components/head.php";

    if ( (isset($_GET['id_cancion'])) && (!empty($_GET['id_cancion'])) && (isset($_GET['id_album'])) && (!empty($_GET['id_album'])) ){
      //DEBERIA VERIFICAR SI LA CANCION A DESVINCULAR EXISTE, AUNQUE ACTUALMENTE NO AFECTA BORRAR ALGO QUE NO EXISTE

      $id_cancion = $_GET['id_cancion'];
      $id_album = $_GET['id_album'];

      //Verificar que el album sea propiedad del usuario actual
      $query = "SELECT autor FROM albums WHERE id_album = '$id_album'";
      $result = mysqli_query($conn, $query);
      $row = mysqli_fetch_array($result);

      if ($row['autor'] != $_SESSION["userName"]){
        header("Location: ../songs.php?error=operacioninvalida");
        exit();
      }

      //Verificar que el album tenga más de 1 canción. Un álbum no puede quedar vacío
      $canciones = "SELECT * FROM albums_incluyendo_canciones WHERE id_album = ?";
      $stmt = mysqli_stmt_init($conn);

      if (!mysqli_stmt_prepare($stmt, $canciones)){
        header("Location: ../signup.php?error=sqlerror");
        exit();
      }

      else{
        mysqli_stmt_bind_param($stmt, "s", $id_album);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultCheck = mysqli_stmt_num_rows($stmt);

        if ($resultCheck == 1){
          header("Location: ../songs.php?error=operacioninvalida");
          exit();
        }
      }


      $sql = "DELETE FROM albums_incluyendo_canciones WHERE id_album = ? AND id_cancion = ?";
      $stmt = mysqli_stmt_init($conn);

      if (!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../songs.php?error=sqlerror");
        exit();
      }

      else{
        mysqli_stmt_bind_param($stmt, "ss", $id_album, $id_cancion);
        $result = mysqli_stmt_execute($stmt);
        
        if ($result){
          header("Location: ../songs.php?success=remove-song-from-album");
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