



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
            <h2>Artistas</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre de usuario</th>
                        <th># Canciones</th>
                        <th># Álbums</th>
                        <th># Seguidores</th>
                        <th># Seguidos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $query = "SELECT * FROM view_artistas";
                        $result_select = mysqli_query($conn, $query);
                        
                        while($row = mysqli_fetch_array($result_select)){
                            echo'
                            <tr>
                                <td><a href="profile.php?username='.$row["username"].'">'.$row["username"].'</a></td>
                                <td>'.$row["canciones_publicadas"].'</td>
                                <td>'.$row["albums_publicados"].'</td>
                                <td>'.$row["seguidores"].'</td>
                                <td>'.$row["seguidos"].'</td>';

                                if ($row["username"] != $_SESSION['userName']){
                                    if (!is_following_person($row["username"], $_SESSION['userName'])){
                                        echo'<td><a class="btn btn-primary" href="validators/follow_request.php?username='.$row["username"].'"><i class="fas fa-eye"></i></a></td>';
                                    }
                                    else{
                                        echo'<td><a class="btn btn-primary" href="validators/unfollow_request.php?username='.$row["username"].'"><i class="fas fa-eye-slash"></i></a></td>';
                                    }
                                }
                                else{
                                    echo'
                                    <td>
                                    <a class="btn btn-primary" href="profile.php"><i class="fas fa-user-edit"></i></a>
                                    <a class="btn btn-primary" href="validators/delete_profile_request.php"><i class="fas fa-trash-alt"></i></a>
                                    </td>';
                                }
                            echo'
                            </tr>
                            '; 
                        }
                    ?>
                </tbody>
            </table>

            <h2>Usuarios</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre de usuario</th>
                        <th># Playlists creadas</th>
                        <th># Seguidores</th>
                        <th># Seguidos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $query = "SELECT * FROM view_usuarios";
                        $result_select = mysqli_query($conn, $query);
                        
                        while($row = mysqli_fetch_array($result_select)){
                            echo'
                            <tr>
                            <td><a href="profile.php?username='.$row["username"].'">'.$row["username"].'</a></td>
                                <td>'.$row["playlists_creadas"].'</td>
                                <td>'.$row["seguidores"].'</td>
                                <td>'.$row["seguidos"].'</td>';

                                if ($row["username"] != $_SESSION['userName']){
                                    if (!is_following_person($row["username"], $_SESSION['userName'])){
                                        echo'<td><a class="btn btn-primary" href="validators/follow_request.php?username='.$row["username"].'"><i class="fas fa-eye"></i></a></td>';
                                    }
                                    else{
                                        echo'<td><a class="btn btn-primary" href="validators/unfollow_request.php?username='.$row["username"].'"><i class="fas fa-eye-slash"></i></a></td>';
                                    }
                                }
                                else{
                                    echo'<td>
                                    <a class="btn btn-primary" href="profile.php"><i class="fas fa-user-edit"></i></a>
                                    <a class="btn btn-primary" href="validators/delete_profile_request.php"><i class="fas fa-trash-alt"></i></a>
                                    </td>';
                                }
                            echo'
                            </tr>
                            ';
                        }
                    ?>
                </tbody>
            </table>
        </section>

        <?php
            require "components/footer.php";
        ?>
    </body>
</html>