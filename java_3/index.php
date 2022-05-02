<?php
  require_once "pdo.php";
  require_once "util.php";
  session_start();
?>


<!DOCTYPE html>
<html>
<head>
  <title> Min Wei's Resume Registry </title>
  <?php require_once "head.php"; ?>
</head>
<body>

<div class="container">
  <h1>Min Wei's Resume Registry</h1>
  <?php
    flashmessage();
    if (!isset($_SESSION['user_id']) ){
      echo ("<p>".'<a href="login.php">'."Please log in"."</a>");
      echo "</p>";
    }
    else{
      echo ("<p>".'<a href="logout.php">'."Logout"."</a>");
    }
    $sql = "SELECT first_name,last_name,headline,profile_id FROM Profile";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    echo('<table border="1">'."\n");
    $ct = 0;
    while ($row1 = $stmt->fetch(PDO::FETCH_ASSOC)){
          if ($ct === 0){
            if (isset($_SESSION['user_id'])){
              echo('<tr><th>'.'Name</th><th>'.'Headline</th><th>'.'Action</th></tr>'."\n");
            }
            else{
              echo('<tr><th>'.'Name</th><th>'.'Headline</th></tr>'."\n");
            }
          }
          echo "<tr><td>";
          echo ('<a href="view.php?profile_id='.$row1['profile_id'].'">');
          echo htmlentities($row1['first_name'])." ".htmlentities($row1['last_name']);
          echo('</a>');

          echo("</td><td>");
          echo(htmlentities($row1['headline']));
          echo("</td>");
          if (isset($_SESSION['user_id'])){
            echo("<td>");
            echo('<a href="edit.php?profile_id='.$row1['profile_id'].'">Edit</a> ');
            echo('<a href="delete.php?profile_id='.$row1['profile_id'].'">Delete</a>');
            echo("</td>");
          }
          echo("</tr>\n");
          $ct += 1;
    }
    echo ('</table>');
    if (isset($_SESSION['user_id'])){
      echo ("<p>");
      echo('<a href="add.php?user_id='.$_SESSION['user_id'].'" >'."Add New Entry".'</a>'.'</p>');
    }


  ?>
</div>
</body>
</html>
