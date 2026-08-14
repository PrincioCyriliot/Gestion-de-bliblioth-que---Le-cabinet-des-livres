<?php
session_start();//démarre la dession
session_unset();
session_destroy();//ferme la sessiom
header('Location: login.php');//redirection vers la page principale
exit;
