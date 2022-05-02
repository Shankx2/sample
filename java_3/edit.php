<?php
session_start();
require_once "pdo.php";
require_once "util.php";
if (! isset($_SESSION['user_id']) ){
    die('ACCESS DENIED');
    return;
}

if (isset($_POST['cancel'])){
  header("Location: index.php");
  return;
}
if (!isset($_REQUEST['profile_id'])){
  $_SESSION['error'] = "Missing profile_id";
  header("Location: index.php");
  return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz AND user_id = :uid");
$stmt->execute(array(":xyz" => $_REQUEST['profile_id'], ":uid"=>$_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false){
  $_SESSION['error'] = "Could not load profile";
  header("Location: index.php");
  return;
}

if ( isset($_POST['save']) ){
    $msg = validateProfile();
    if (is_string($msg)){
      $_SESSION['error'] = $msg;
      header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
      return;
    }
    $msg = validatePos();
    if (is_string($msg)){
      $_SESSION['error'] = $msg;
      header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
      return;
    }
    $msg = validateEdu();
    if (is_string($msg)){
      $_SESSION['error'] = $msg;
      header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
      return;
    }



    $stmt = $pdo->prepare('UPDATE Profile SET
      first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
      WHERE profile_id = :pid AND user_id = :uid');

    $stmt->execute(array(
      ':fn' => $_POST['first_name'],
      ':ln' => $_POST['last_name'],
      ':em' => $_POST['email'],
      ':he' => $_POST['headline'],
      ':su' => $_POST['summary'],
      ':pid' => $_POST['profile_id'],
      ':uid'=>$_SESSION['user_id']
    )
    );

    //UPDATE position
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
    insertPosition($pdo,$_REQUEST['profile_id']);

    //update education
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
    insertEducation($pdo,$_REQUEST['profile_id']);


    $_SESSION['success'] = "Profile updated";
    header("Location: index.php");
    return;

}


$positions = loadpos($pdo,$_REQUEST['profile_id']);
$prof = loadpro($pdo,$_REQUEST['profile_id']);
$schools = loadedu($pdo,$_REQUEST['profile_id']);

?>


<!DOCTYPE html>
<html>
<head>
  <title>Min Wei's Profile Edit</title>
  <?php require_once "head.php" ?>
</head>
<body>
  <div class = "container">
    <h1>Editing Profile for UMSI</h1>
    <?php
        flashmessage();
    ?>
    <form method="post">
      <input type = "hidden" name = "profile_id" value = "<?= htmlentities($_GET['profile_id']); ?>"/>
      <p>First Name:<input type="text" name ="first_name" size="60" value="<?= $prof['first_name'] ?>"></p>
      <p>Last Name:<input type = "text" name = "last_name" size = "60" value="<?= $prof['last_name'] ?>"></p>
      <p>Email:<input type = "text" name = "email" size = "30" value="<?= $prof['email'] ?>"></p>
      <p>Headline:<br/>
        <input type = "text" name="headline" size = "80" value = "<?= $prof['headline'] ?>"></p>
      <p>Summary:<br/>
        <textarea name = "summary" rows = "8" cols = "80"><?= $prof['summary']; ?></textarea></p>


      <?php
      $countEdu = 0;
      echo ('<p>Education: <input type = "button" id = "addEdu" value = "+">'."\n");
      echo ('<div id = "edu_fields">'."\n");
      if (count($schools) > 0){
        foreach ($schools as $school) {
          $countEdu++;
          echo ('<div id = "edu'.$countEdu.'">'."\n");
          echo
'<p>Year: <input type = "text" name = "edu_year'.$countEdu.'" value = "'.$school['year'].'" />
<input type = "button" value = "-" onclick = "$(\'#edu'.$countEdu.'\').remove();return false;" > </p>
<p>School: <input type = "text" size = "80" name = "edu_school'.$countEdu.'" class = "school" value = "'.htmlentities($school['name']).'" />';
        echo "\n</div>\n";
        }
      }
      echo ("</div></p>\n");

        $pos = 0;
        echo ('<p>Position: <input type = "submit" id = "addPos" value = "+">'."\n");
        echo ('<div id = "position_fields">'."\n");
        foreach ($positions as $position) {
          $pos++;
          echo ('<div id = "position'.$pos.'">'."\n");
          echo('<p>Year: <input type = "text" name = "year'.$pos.'"');
          echo(' value = "'.$position['year'].'" />'."\n");
          echo('<input type = "button" value = "-" ');
          echo('onclick = "$(\'#position'.$pos.'\').remove();return false;">'."\n");
          echo("</p>\n");
          echo('<textarea name="desc'.$pos.'" rows = "8" cols = "80">'."\n");
          echo(htmlentities($position['description'])."\n");
          echo("\n</textarea>\n</div>\n");
        }
        echo("</div></p>\n");
      ?>
      <p>
      <input type = "submit" name = "save" value="Save"/>
      <input type = "submit" name = "cancel" value = "Cancel"/>
      </p>
    </form>


    <script>
      countPos = <?= $pos ?>;
      countEdu = <?= $countEdu ?>;
      $(document).ready(
        function(){
            window.console && console.log('Document ready called');
            $('#addPos').click(
              function(event){
                event.preventDefault();
                if (countPos >= 9){
                  alert("Maximum of nine position entries exceeded");
                  return;
                }
                countPos ++;
                window.console && console.log("Adding position " + countPos);
                $('#position_fields').append('<div id="position'+countPos+'">\n<p>Year: <input type="text" name="year'+countPos+'" value="">\n<input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"></p><textarea name="desc'+countPos+'" rows="8" cols="80"></textarea></div>');
              }
            );

            //addedu
            $("#addEdu").click(
							function(event)
							{
								event.preventDefault();
								if(countEdu >= 9)
								{
									alert("Maximum of nine educastion entries exceeded");
									return;
								}

								countEdu++;
								window.console && console.log("Adding education" + countEdu);

								$("#edu_fields").append(
									'<div id="edu' + countEdu + '">  \
										<p>  \
										    Year :  \
										    <input type = "text" name="edu_year' + countEdu + '" value = "" /> \
										    <input type="button" value="-"  \
										        onclick="$(\'#edu' + countEdu + '\').remove(); return false;">  \
										</p>  \
										<p>   \
											School :    \
											<input type = "text" size="80" name="edu_school' + countEdu + '" class="school" value="">   \
										</p>   \
									</div>'
								);

								$(".school").autocomplete(
									{source: "school.php"}
								);
							}
						);
						$(".school").autocomplete(
							{source: "school.php"}
						);
        }
      );
    </script>


  </div>
</body>
</html>
