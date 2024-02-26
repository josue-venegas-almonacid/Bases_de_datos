<?php
    include("database_connection.php");
    require "../components/head.php";

    if ( (isset($_GET['playlist'])) && (!empty($_GET['playlist'])) ){
      
      //DEBERIA VERIFICAR SI LA PLAYLIST A SEGUIR EXISTE, PERO ESO LO VE LA FOREIGN KEY, YA QUE NO PERMITIRÁ KEYS QUE NO EXISTAN EN PLAYLISTS
      
      $playlist_seguida = $_GET['playlist'];
      $username_seguidor = $_SESSION["userName"];

      //Verifico que el usuario no quiere seguir una playlist suya
      $query = "SELECT * FROM playlists WHERE id_playlist = '".$playlist_seguida."'";
      $result_select = mysqli_query($conn, $query);
      $row = mysqli_fetch_array($result_select);

      if ($row['creador'] == $username_seguidor){
        header("Location: ../playlists.php?error=operacioninvalida");
        exit();
      }

      $sql = "INSERT INTO personas_siguiendo_playlists(id_playlist, username) VALUES (?,?)";
      $stmt = mysqli_stmt_init($conn);

      if (!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../playlists.php?error=sqlerror");
        exit();
      }

      else{
        mysqli_stmt_bind_param($stmt, "ss", $playlist_seguida, $username_seguidor);
        $result = mysqli_stmt_execute($stmt);

        if ($result){
          header("Location: ../playlists.php?success=follow-playlist");
          exit();
        }
        header("Location: ../playlists.php?error=sqlerror");
        exit();
      }
    }

    else{
      header("Location: ../playlists.php?error=operacioninvalida");
      exit();
    }
?>