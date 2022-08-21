<?php
$mysqli = new mysqli("localhost","root","","foros");
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
}
print_r(mysqli_set_charset($mysqli, "utf8mb4"));
if (!mysqli_set_charset($mysqli, "utf8mb4")) {
  printf("Error loading character set utf8mb4: %s\n", mysqli_error($mysqli));
  exit();
}