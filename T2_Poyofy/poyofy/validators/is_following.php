<?php
    function is_following_person($tofollow, $follower) {
        include("database_connection.php");
        $query = "SELECT * FROM personas_siguiendo_personas WHERE username_seguido = '$tofollow' AND username_seguidor = '$follower'";      //SQL INJECTION ALERT
        $result_edit = mysqli_query($conn, $query);

        if (mysqli_num_rows($result_edit) == 1){
            return(true);
        }
        return(false);
    }

    function is_following_playlist($playlist, $follower) {
        include("database_connection.php");
        $query = "SELECT * FROM personas_siguiendo_playlists WHERE id_playlist = '$playlist' AND username = '$follower'";      //SQL INJECTION ALERT
        $result_edit = mysqli_query($conn, $query);

        if (mysqli_num_rows($result_edit) == 1){
            return(true);
        }
        return(false);
    }

    function is_liking_song($song, $liker) {
        include("database_connection.php");
        $query = "SELECT * FROM usuarios_gustando_canciones WHERE id_cancion = '$song' AND username = '$liker'";      //SQL INJECTION ALERT
        $result_edit = mysqli_query($conn, $query);

        if (mysqli_num_rows($result_edit) == 1){
            return(true);
        }
        return(false);
    }
?>