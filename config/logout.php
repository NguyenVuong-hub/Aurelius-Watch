<?php
session_start();
session_destroy();

header("Location: /AureliusWatch/index.php");
exit;
?>