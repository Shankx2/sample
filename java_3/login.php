<?php
require_once "pdo.php";
require_once "util.php";
  session_start();
  if (isset($_POST['cancel'])){
    header("Location: index.php");
    return;
  }

  $salt = 'XyZzy12*_';
  if (isset($_POST['email']) && isset($_POST['pass'])){

      $check = hash('md5', $salt.$_POST['pass']);
      $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
      $stmt->execute(array(':em'=> $_POST['email'], ':pw'=> $check));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row !== false){
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        header("Location: index.php");
        return;
      }
      else{
        $_SESSION['error'] = "Incorrect password";
        header("Location: login.php");
        return;
      }


  }

?>

<!DOCTYPE html>
<html>
<head>
  <title>Min Wei's Login Page</title>
  <?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
  flashmessage();
?>
<form method="POST">
  <label for="nam">Email</label>
  <input type="text" name="email" id="email"><br/>
  <label for="id_1723">Password</label>
  <input type="text" name="pass" id="id_1723"><br/>
  <input type="submit" onclick = "return doValidate()" value="Log In">
  <input type = "submit" name = "cancel" value = "Cancel"> <br/>
</form>
<script>
  function doValidate() {
         console.log('Validating...');
         try {
             addr = document.getElementById('email').value;
             pw = document.getElementById('id_1723').value;
             console.log("Validating addr="+addr+" pw="+pw);
             if (pw == null || pw == "" || addr == null || addr == "") {
                 alert("Both fields must be filled out");
                 return false;
             }
             else if (addr.indexOf('@') == -1){
               alert("Invalid email address");
               return false;
             }
             return true;
         } catch(e) {
             return false;
         }
         return false;
     }
</script>
</div>
</body>
</html>
