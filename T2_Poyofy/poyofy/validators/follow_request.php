<?php
    
    
    include("database_connection.php");
    require "../components/head.php";

    if ( (isset($_GET['username'])) && (!empty($_GET['username'])) ){
      
      //DEBERIA VERIFICAR SI EL USUARIO A SEGUIR EXISTE, PERO ESO LO VE LA FOREIGN KEY, YA QUE NO PERMITIRÁ KEYS QUE NO EXISTAN EN PERSONAS
      
      $username_seguido = $_GET['username'];
      $username_seguidor = $_SESSION["userName"];

      //Verifico que el usuario no quiere seguirse a sí mismo
      if ($username_seguido == $username_seguidor){
        header("Location: ../community.php?error=operacioninvalida");
        exit();
      }

      $sql = "INSERT INTO personas_siguiendo_personas(username_seguido, username_seguidor) VALUES (?,?)";
      $stmt = mysqli_stmt_init($conn);

      if (!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../community.php?error=sqlerror");
        exit();
      }

      else{
        mysqli_stmt_bind_param($stmt, "ss", $username_seguido, $username_seguidor);
        $result = mysqli_stmt_execute($stmt);

        if ($result){
          header("Location: ../community.php?success=follow");
          exit();
        }
        header("Location: ../community.php?error=sqlerror");
        exit();
      }
    }

    else{
      header("Location: ../community.php?error=operacioninvalida");
      exit();
    }
?>