



<!DOCTYPE html>

<html>
    <head>
    <?php
        require "components/head.php";
        //En caso de no haber una sesión iniciada, no puedo entrar en esta página
        if (!isset($_SESSION["userName"])){
            header("Location: index.php");
            exit();
        }
    ?>
    </head>

    <body>
        <?php
            require "components/body.php";
            include("validators/database_connection.php");
            include("validators/is_following.php");
        ?>

        <section id="content">
            <h2>Canción</h2>
            <?php
            if (isset($_GET['id'])){
                $id_cancion = $_GET['id'];

                $query = "SELECT * FROM canciones WHERE id_cancion = '$id_cancion'";  //SQL INJECTION ALERT
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_array($result);

                //Si no existe la canción, retorno a la página principal
                if (!$row){
                    header("Location: songs.php?error=nosong");
                    exit();
                }

                $nombre_cancion = $row['nombre_cancion'];
                $autor = $row['autor'];
                $genero = $row['genero'];
                $duracion = $row['duracion'];
                $cantidad_likes = $row['cantidad_likes'];
                $anno_publicacion = $row['anno_publicacion'];
                $username = $_SESSION["userName"];

                //Si yo soy el creador, puedo editar los campos
                if ($autor == $username){
                    echo'
                    <div class="card card-body">
                            <form action="validators/update_song_request.php?id_cancion='.$id_cancion.'" method="POST">
                                <div class="form-group"><p><strong>ID de la canción: </strong>'.$id_cancion.'</p></div>
                                <div class="form-group"><p><strong>Nombre de la canción: </strong><input type="text" class="form-control" name="nombre_cancion" value="'.$nombre_cancion.'" placeholder="Nombre de la canción"></p></div>
                                <div class="form-group"><p><strong>Autor: </strong>'.$autor.'</p></div>
                                <div class="form-group"><p><strong>Género: </strong><input type="text" class="form-control" name="genero" value="'.$genero.'" placeholder="Género"></p></div>
                                <div class="form-group"><p><strong>Duración (mm:ss): </strong><input type="text" class="form-control" name="duracion" value="'.$duracion.'" placeholder="Duración (mm:ss)"></p></div>
                                <div class="form-group"><p><strong># Likes: </strong>'.$cantidad_likes.'</p></div>
                                <div class="form-group"><p><strong>Año de publicación: </strong><input type="text" class="form-control" name="anno_publicacion" value="'.$anno_publicacion.'" placeholder="Año de publicación"></p></div>
                                <button class="btn btn-success" name="update-song">Actualizar</button>
                            </form>
                        </div>

                    <div class="my_card">
                        <p class="p_index"><a href="validators/delete_song_request.php?id='.$id_cancion.'><button type="submit" name="delete">Eliminar canción</button></a></p>
                    </div>
                    ';  
                }


                //En caso contrario, sólo veo los datos
                else{
                    echo'
                    <div class="card card-body">
                        <div class="form-group"><p><strong>ID de la canción: </strong>'.$id_cancion.'</p></div>
                        <div class="form-group"><p><strong>Nombre de la canción: </strong>'.$nombre_cancion.'</p></div>
                        <div class="form-group"><p><strong>Autor: </strong><a href="profile.php?username='.$autor.'">'.$autor.'</a></p></div>
                        <div class="form-group"><p><strong>Género: </strong>'.$genero.'</p></div>
                        <div class="form-group"><p><strong>Duración (hh:mm:ss): </strong>'.$duracion.'</p></div>
                        <div class="form-group"><p><strong>#Likes: </strong>'.$cantidad_likes.'</p></div>
                        <div class="form-group"><p><strong>Año de publicación: </strong>'.$anno_publicacion.'</p></div>';
                        
                        //Si soy usuario, puedo darle like
                        $query = "SELECT * FROM usuarios WHERE username = '$username'";
                        $result = mysqli_query($conn, $query);
                        $row2 = mysqli_fetch_array($result);

                        if ($row2){
                            if (!is_liking_song($row["id_cancion"], $username)){
                                echo'<td><a class="btn btn-primary" href="validators/like_request.php?song='.$row["id_cancion"].'"><i class="far fa-thumbs-up"></i></a>';
                            }
                            else{
                                echo'<td><a class="btn btn-primary" href="validators/unlike_request.php?song='.$row["id_cancion"].'"><i class="fas fa-thumbs-up"></i></a>';
                            }
                        }
                    echo'</div>';
                }

                
            }

            else{
                header("Location: songs.php?error=operacioninvalida");
            }
            ?>
            
        </section>

        <?php
            require "components/footer.php";
        ?>
    </body>
</html>