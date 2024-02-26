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
    //Si no soy artista no puedo entrar en esta página
    include("validators/database_connection.php");
    $username = $_SESSION["userName"];
    $sql = "SELECT * FROM artistas WHERE username = '$username'";
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
    <h2>Crear canción</h2>
    <div class="card card-body">
      <form action="validators/create_song_request.php" method="POST">
        <div class="form-group"><p><strong>Nombre de la canción: </strong><input type="text" class="form-control" name="nombre_canción" placeholder="Nombre de la canción"></p></div>
        <div class="form-group"><p><strong>Género: </strong><input type="text" class="form-control" name="género" placeholder="Género"></p></div>
        <div class="form-group"><p><strong>Duración (mm:ss): </strong><input type="text" class="form-control" name="duración" placeholder="Duración (mm:ss)"></p></div>
        <div class="form-group"><p><strong>Año de publicación: </strong><input type="text" class="form-control" name="fecha" placeholder="Año de publicación"></p></div>
        <button class="btn btn-success" name="create-song">Crear</button>
      </form>
    </div>
    
    <div class="my_card">
        <p class="p_index"><a href="songs.php"><button type="submit" name="back">Volver</button></a></p>
      </div>
  </section>';

    require "components/footer.php";
  ?>
</body>
