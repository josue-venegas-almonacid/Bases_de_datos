<?php
    include("database_connection.php");
    require "../components/head.php";

    if ( (isset($_GET['song'])) && (!empty($_GET['song'])) ){
      
      //DEBERIA VERIFICAR SI LA CANCION A LIKEAR EXISTE, PERO ESO LO VE LA FOREIGN KEY, YA QUE NO PERMITIRÁ KEYS QUE NO EXISTAN EN CANCIONES
      
      $id_cancion = $_GET['song'];
      $username = $_SESSION["userName"];

      //Verifico que sea un usuario el que quiera dar like
      $query = "SELECT * FROM usuarios WHERE username = '".$username."'";
      $result_select = mysqli_query($conn, $query);
      $row = mysqli_fetch_array($result_select);

      if (!$row){
        header("Location: ../songs.php?error=operacioninvalida");
        exit();
      }

      $sql = "INSERT INTO usuarios_gustando_canciones(username, id_cancion) VALUES (?,?)";
      $stmt = mysqli_stmt_init($conn);

      if (!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../songs.php?error=sqlerror");
        exit();
      }

      else{
        mysqli_stmt_bind_param($stmt, "ss", $username, $id_cancion);
        $result = mysqli_stmt_execute($stmt);

        if ($result){
          header("Location: ../songs.php?success=like-song");
          exit();
        }
        header("Location: ../songs.php?error=errorsql");
        exit();
      }
    }

    else{
      header("Location: ../playlists.php?error=operacioninvalida");
      exit();
    }
?>