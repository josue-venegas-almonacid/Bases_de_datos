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
    include("validators/is_following.php");

    $id_playlist = "";
    $id_cancion = "";
  
    if (isset($_GET["id_playlist"])) $id_playlist = $_GET["id_playlist"];
    if (isset($_GET["id_cancion"])) $id_cancion = $_GET["id_cancion"];

    echo'<section id="content">
      <h2>Agregar canción a una playlist</h2>
      <div class="card card-body">
        <form action="validators/add_song_to_playlist_request.php" method="POST">
          <div class="form-group"><p><strong>ID de la playlist: </strong><input type="text" class="form-control" name="id_playlist" value="'.$id_playlist.'" placeholder="ID de la playlist"></p></div>
          <div class="form-group"><p><strong>ID de la canción: </strong><input type="text" class="form-control" name="id_cancion" value="'.$id_cancion.'" placeholder="ID de la canción"></p></div>
          <button class="btn btn-success" name="add-song-to-playlist">Agregar</button>
        </form>
      </div>
      
      <div class="my_card">
        <p class="p_index"><a href="songs.php"><button type="submit" name="back">Volver</button></a></p>
      </div>';

      if (isset($_GET["id_playlist"])){
        //Muestro canciones de la comunidad
        echo'<h2>Canciones</h2>
                    <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Autor</th>
                            <th>Género</th>
                            <th>Duración</th>
                            <th>Año de publicación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>';
                        
                            $username = $_SESSION["userName"];
                            $query = "SELECT * FROM canciones";
                            $result_select = mysqli_query($conn, $query);
                            
                            while($row = mysqli_fetch_array($result_select)){
                                echo'
                                <tr>
                                    <td>'.$row["id_cancion"].'</td>
                                    <td><a href="song.php?id='.$row["id_cancion"].'">'.$row["nombre_cancion"].'</a></td>
                                    <td><a href="profile.php?username='.$row["autor"].'">'.$row["autor"].'</a></td>
                                    <td>'.$row["genero"].'</td>
                                    <td>'.$row["duracion"].'</td>
                                    <td>'.$row["anno_publicacion"].'</td>';
                                    
                                    //Si soy usuario puedo agregarla a mi playlist
                                    echo'<td><a class="btn btn-primary" href="add_song_to_playlist.php?id_cancion='.$row["id_cancion"].'&id_playlist='.$id_playlist.'"><i class="fas fa-plus-square"></a></td>';
                                echo'
                                </tr>';   
                            }
                    echo'</tbody>
                </table>';}

        if (isset($_GET["id_cancion"])){
        //Muestro mis playlists creadas
        echo'
        <h2>Mis playlists creadas</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th># Canciones</th>
                    <th># Seguidores</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>';
                
                    $query = "SELECT * FROM playlists WHERE creador = '".$_SESSION["userName"]."'";
                    $result_select = mysqli_query($conn, $query);
                    
                    while($row = mysqli_fetch_array($result_select)){
                        echo'
                        <tr>
                            <td>'.$row["id_playlist"].'</td>
                            <td><a href="playlist.php?id='.$row["id_playlist"].'">'.$row["nombre_playlist"].'</a></td>
                            <td>'.$row["canciones"].'</td>
                            <td>'.$row["seguidores"].'</td>
                            <td>
                                <a class="btn btn-primary" href="add_song_to_playlist.php?id_playlist='.$row["id_playlist"].'&id_cancion='.$id_cancion.'"><i class="fas fa-plus-square"></a></td>
                            </td>
                        </tr>';
                    }
                
            echo'</tbody>
        </table>';}


    echo'</section>';


    require "components/footer.php";
  ?>
</body>
