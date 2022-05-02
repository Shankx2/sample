<?php
  require_once "pdo.php";
  session_start();
  if (isset($_POST['cancel'])){
    header("Location: index.php");
    return;
  }

  if (isset($_POST['delete'])){
    $cmt = "DELETE FROM Profile WHERE profile_id = :profile_id";
    $stmt = $pdo->prepare($cmt);
    $stmt->execute(array(':profile_id' => $_POST['profile_id']));
    $_SESSION['success'] = 'Profile deleted';
    header("Location: index.php");
    return;
  }

  $stmt = $pdo->prepare("SELECT first_name,last_name,profile_id FROM Profile where profile_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
  <?php require_once "head.php" ?>
  <title>Min Wei's Profile Deleting</title>
</head>
<body>

  <div class="container">
    <h1>Deleteing Profile</h1>
    <p>First Name: <?= htmlentities($row['first_name']) ?></p>
    <p>Last Name: <?= htmlentities($row['last_name']) ?></p>
    <form method="post">
    <input type="hidden" name="profile_id" value="<?= htmlentities($row['profile_id']) ?>">
    <input type="submit" value="Delete" name="delete">
    <input type="submit" value = "Cancel" name = "cancel">
    </form>
  </div>
</body>
</html>
