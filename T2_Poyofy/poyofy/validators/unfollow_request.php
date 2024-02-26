<?php
    include("database_connection.php");
    require "../components/head.php";

    if ( (isset($_GET['username'])) && (!empty($_GET['username'])) ){
      //DEBERIA VERIFICAR SI EL USUARIO A DESSEGUIR EXISTE, AUNQUE ACTUALMENTE NO AFECTA BORRAR (DESSEGUIR) ALGO QUE NO EXISTE

      $username_seguido = $_GET['username'];
      $username_seguidor = $_SESSION["userName"];

      $sql = "DELETE FROM personas_siguiendo_personas WHERE username_seguido = ? AND username_seguidor = ?";
      $stmt = mysqli_stmt_init($conn);

      if (!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../community.php?error=sqlerror");
        exit();
      }

      else{
        mysqli_stmt_bind_param($stmt, "ss", $username_seguido, $username_seguidor);
        $result = mysqli_stmt_execute($stmt);
        
        if ($result){
          header("Location: ../community.php?success=unfollow");
          exit();
        }
        header("Location: ../community.php?error=sqlerror");
      }
    }

    else{
      header("Location: ../community.php?error=operacioninvalida");
      exit();
    }
?>