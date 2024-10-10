<?php

    include('../include/connection.php');
    session_start();

    if($_SESSION['role'] == 1 || $_SESSION['role'] == 2)
    {
        session_destroy();
        header('location: ../index.php');
    }

    if(!$_SESSION['loggedin']){

        session_destroy();

        header('location: ../index.php');
    }

