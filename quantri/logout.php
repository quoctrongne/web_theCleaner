<?php

session_start();
unset($_SESSION["user"]);  //xoa session user
header("Location: login.php");

?>