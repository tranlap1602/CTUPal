<?php
session_start();

// Hủy tất cả session
session_destroy();

// Chuyển hướng về trang đăng nhập
header('Location: login.php');
exit();
