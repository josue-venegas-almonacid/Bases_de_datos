<!-- SI ES ARTISTA NO PUEDE CREAR!!!
listo porque en create_album se puso esa restricción y este validador es post-->

<?php 
  if (isset($_POST['create-album'])){
    include("database_connection.php");
    require "../components/head.php";
    
    
    $nombre_album = $_POST['nombre_album'];
    $autor = $_SESSION["userName"];
    $id_cancion = $_POST['id_cancion'];
    $anno_publicacion = $_POST['fecha'];

    if (empty($nombre_album) || empty($id_cancion)){
      header("Location: ../albums.php?error=operacioninvalida");
      exit();
    }

    if (empty($anno_publicacion)) $anno_publicacion = 2020;

    
    //Compruebo que la canción a agregar en el álbum exista y sea del artista
    $sql = "SELECT * FROM canciones WHERE id_cancion = $id_cancion";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);

    if (!$row){
      header("Location: ../albums.php?error=nosong");
      exit();
    }

    $autor_cancion = $row['autor'];
    if ($autor_cancion != $autor){
      header("Location: ../albums.php?error=isnotowner");
      exit();
    }

    //Creo el álbum
    $create_album = "INSERT INTO albums(nombre_album, autor, anno_publicacion) VALUES ('$nombre_album', '$autor', '$anno_publicacion')";
    $result_create = mysqli_query($conn, $create_album);
    if (!$result_create){
      $error = mysqli_error($conn);
      header("Location: ../albums.php?error=$error");
      exit();
    }

    //Obtengo el id del álbum
    $get_id = "SELECT id_album FROM albums ORDER BY id_album DESC LIMIT 1";
    $result_id = mysqli_query($conn, $get_id);
    $row_id = mysqli_fetch_array($result_id);

    if (!$row_id){
      header("Location: ../albums.php?error=sqlerror");
      exit();
    }
    $id_album = $row_id['id_album'];

    //La agrego al álbum
    $add_song = "INSERT INTO `albums_incluyendo_canciones`(`id_album`, `id_cancion`) VALUES ('$id_album','$id_cancion')";
    $result_add = mysqli_query($conn, $add_song);

    if (!$result_add){
      header("Location: ../albums.php?error=sqlerror");
      exit();
    }
    header("Location: ../albums.php?success=create-album");
    exit();
  }

  //Si el usuario no apretó el botón para crear la lista, no puede entrar en esta página
  else{
    header("Location: ../albums.php?error=operacioninvalida");
    exit();
  }

?>