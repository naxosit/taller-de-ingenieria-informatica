<?php
session_start();
session_destroy();
header("Location: Cliente/Index.php");
exit();
?>