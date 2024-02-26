



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
        ?>

        <section id="content">
            <?php
                //Sólo los artistas pueden crear albums
                $query = "SELECT * FROM artistas WHERE username = '".$_SESSION["userName"]."'";
                $result_select = mysqli_query($conn, $query);
                $row = mysqli_fetch_array($result_select);

                if ($row){
                    echo'
                    <h2>Álbums <a class="btn btn-primary" href="create_album.php"><i class="fas fa-plus-square"></i></a></h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Autor</th>
                                <th># Canciones</th>
                                <th>Año de publicación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>';
                            $query = "SELECT * FROM albums";
                            $result_select = mysqli_query($conn, $query);
                            
                            while($row = mysqli_fetch_array($result_select)){
                                echo'
                                <tr>
                                    <td>'.$row["id_album"].'</td>
                                    <td><a href="album.php?id='.$row["id_album"].'">'.$row["nombre_album"].'</a></td>
                                    <td><a href="profile.php?username='.$row["autor"].'">'.$row["autor"].'</a></td>
                                    <td>'.$row["canciones"].'</td>
                                    <td>'.$row["anno_publicacion"].'</td>';
                                    
                                    //Si soy el creador puedo editar el álbum o añadirle canciones
                                    if ($row["autor"] == $_SESSION["userName"]){
                                        echo'<td><a class="btn btn-primary" href="album.php?id='.$row["id_album"].'"><i class="fas fa-pencil-alt"></i></a>';
                                        echo'<a class="btn btn-primary" href="validators/delete_album_request.php?id='.$row["id_album"].'"><i class="fas fa-trash-alt"></i></a>';
                                        echo'<a class="btn btn-primary" href="add_song_to_album.php?id_album='.$row["id_album"].'"><i class="fas fa-plus-square"></a></td>';
                                    }
                                echo'
                                </tr>';
                            }
                        echo'</tbody>
                    </table>';
                }
                else{
                    echo'
                    <h2>Álbums</h2>                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Autor</th>
                                <th># Canciones</th>
                                <th>Año de publicación</th>
                            </tr>
                        </thead>
                        <tbody>';
                            $query = "SELECT * FROM albums";
                            $result_select = mysqli_query($conn, $query);
                            
                            while($row = mysqli_fetch_array($result_select)){
                                echo'
                                <tr>
                                    <td>'.$row["id_album"].'</td>
                                    <td><a href="album.php?id='.$row["id_album"].'">'.$row["nombre_album"].'</a></td>
                                    <td><a href="profile.php?username='.$row["autor"].'">'.$row["autor"].'</a></td>
                                    <td>'.$row["canciones"].'</td>
                                    <td>'.$row["anno_publicacion"].'</td>
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