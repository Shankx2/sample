<?php
session_start();
require_once "pdo.php";
require_once "util.php";



  $stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $profile_id = $row['profile_id'];
  $fn = $row['first_name'];
  $ln = $row['last_name'];
  $em = $row['email'];
  $he = $row['headline'];
  $su = $row['summary'];
  $positions = loadpos($pdo,$_REQUEST['profile_id']);
  $schools = loadedu($pdo,$_REQUEST['profile_id']);
?>

<!DOCTYPE html>
<html>
<head>
  <?php require_once "head.php" ?>
  <title>Min Wei's Profile View</title>
</head>
<body>
  <div class = "container">
  <h1>Profile information</h1>


  <p>First Name: <?= $fn; ?> </p>
  <p>Last Name: <?= $ln;?> </p>
  <p>Email: <?= $em; ?> </p>
  <p>Headline:</br> <?= $he; ?> </p>
  <p>Summary:</br> <?= $su; ?> </p>
  <p>
    Education
  <ul>
    <?php
      foreach ($schools as $school) {
        echo('<li>'.$school['year'].': '.$school['name'].'</li>');

      }

    ?>
  </ul>
  </p>
  <p>
    Position
  <ul>
    <?php
      foreach ($positions as $position) {
        echo('<li>'.$position['year'].': '.$position['description'].'</li>');

      }

    ?>
  </ul>
  </p>
  <p>
    <a href="index.php">Done</a>
  </p>
</body>

</html>
