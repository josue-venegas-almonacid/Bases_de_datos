<!doctype html>
<html lang="en">

<head>
  <?php
    require "components/head.php";
    //En caso de no haber una sesión iniciada, no puedo entrar en esta página
    if (!isset($_SESSION["userName"])){
      header("Location: index.php");
      exit();
    }
    //Si no soy usuario no puedo entrar en esta página
    include("validators/database_connection.php");
    $username = $_SESSION["userName"];
    $sql = "SELECT * FROM usuarios WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);

    if (!$row){
      header("Location: index.php?error=operacioninvalida");
      exit();
    }
  ?>
</head>

<!--En caso de haber una sesión iniciada, puedo entrar en esta página-->
<body>
  <?php
    require "components/body.php";  

  echo'<section id="content">
    <h2>Crear playlist</h2>
    <div class="card card-body">
      <form action="validators/create_playlist_request.php" method="POST">
        <div class="form-group"><p><strong>Nombre de la playlist: </strong><input type="text" class="form-control" name="nombre_playlist" placeholder="Nombre de la playlist"></p></div>
        <button class="btn btn-success" name="create-playlist">Crear</button>
      </form>
    </div>
    
    <div class="my_card">
        <p class="p_index"><a href="playlists.php"><button type="submit" name="back">Volver</button></a></p>
    </div>
  </section>';

    require "components/footer.php";
  ?>
</body>
