



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
            <?php
                //Sólo los artistas pueden crear canciones
                $query = "SELECT * FROM artistas WHERE username = '".$_SESSION["userName"]."'";
                $result_select = mysqli_query($conn, $query);
                $row = mysqli_fetch_array($result_select);

                if ($row){
                    echo'
                    <h2>Canciones <a class="btn btn-primary" href="create_song.php"><i class="fas fa-plus-square"></i></a></h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Autor</th>
                                <th>Género</th>
                                <th>Duración</th>
                                <th>Año de publicación</th>
                                <th># Likes</th>
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
                                    <td>'.$row["anno_publicacion"].'</td>
                                    <td>'.$row["cantidad_likes"].'</td>';
                                    
                                    //Si soy el creador puedo editar la canción, eliminarla o agregarla a un álbum
                                    if ($row["autor"] == $username){
                                        echo'<td><a class="btn btn-primary" href="song.php?id='.$row["id_cancion"].'"><i class="fas fa-pencil-alt"></i></a>';
                                        echo'<a class="btn btn-primary" href="validators/delete_song_request.php?id='.$row["id_cancion"].'"><i class="fas fa-trash-alt"></i></a>';
                                        echo'<a class="btn btn-primary" href="add_song_to_album.php?id_cancion='.$row["id_cancion"].'"><i class="fas fa-plus-square"></a></td>';
                                    }
                                echo'
                                </tr>';
                            }
                            
                        echo'</tbody>
                    </table>';
                }
                else{
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
                            <th># Likes</th>
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
                                    <td>'.$row["anno_publicacion"].'</td>
                                    <td>'.$row["cantidad_likes"].'</td>';
                                    
                                    //Si soy usuario puedo darle like o agregarla a mi playlist
                                    if (!is_liking_song($row["id_cancion"], $username)){
                                        echo'<td><a class="btn btn-primary" href="validators/like_request.php?song='.$row["id_cancion"].'"><i class="far fa-thumbs-up"></i></a>';
                                    }
                                    else{
                                        echo'<td><a class="btn btn-primary" href="validators/unlike_request.php?song='.$row["id_cancion"].'"><i class="fas fa-thumbs-up"></i></a>';
                                    }
                                    echo'<a class="btn btn-primary" href="add_song_to_playlist.php?id_cancion='.$row["id_cancion"].'"><i class="fas fa-plus-square"></a></td>';
                                echo'
                                </tr>';   
                            }
                    echo'</tbody>
                </table>';
                }
            ?>
        </section>

        <?php
            require "components/footer.php";
        ?>
    </body>
</html>