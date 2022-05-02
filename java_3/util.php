<?php
  require_once "pdo.php";
  function flashmessage(){
    if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
    else if ( isset($_SESSION['success']) ) {
        echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
        unset($_SESSION['success']);
    }

  }
function validateProfile(){
  if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1){
    return "All fields are required";

  }
  if (!strpos($_POST['email'],"@")){
    return  "Email address must contain @";
  }
  return true;
}
function validateEdu()
{
  for ($i = 1; $i <= 9;$i++ ){
    if (!isset($_POST['edu_year'.$i])) continue;
    if (!isset($_POST['edu_school'.$i])) continue;
    $year = $_POST['year'.$i];
    $school = $_POST['edu_school'.$i];
    if (strlen($year) == 0 || strlen($school) == 0){
      return "All fields are required";
    }
    if (! is_numeric($year)){
      return "Education year must be numeric";
    }
  }
  return true;
}
function validatePos(){
  for ($i = 1; $i <= 9;$i++ ){
    if (!isset($_POST['year'.$i])) continue;
    if (!isset($_POST['desc'.$i])) continue;
    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];
    if (strlen($year) == 0 || strlen($desc) == 0){
      return "All fields are required";
    }
    if (! is_numeric($year)){
      return "Position year must be numeric";
    }
  }
  return true;
}

function loadpos($pdo,$profile_id){
  $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY `rank`');
  $stmt->execute(array( ':prof' => $profile_id));
  $position = array();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $position[] = $row;
  }
  return $position;

}
function loadpro($pdo,$profile_id){
  $stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :prof');
  $stmt->execute(array(':prof'=>$profile_id));

  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  return $row;
}

function loadedu($pdo,$profile_id){
  $stmt = $pdo->prepare('SELECT year,name FROM Education JOIN Institution on Education.institution_id = Institution.institution_id WHERE profile_id = :prof ORDER BY `rank`');
  $stmt->execute(array(':prof'=>$profile_id));

  $edu = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $edu;
}


function insertPosition($pdo,$profile_id){
  $rank = 1;
  for ($i = 1; $i <= 9; $i++) {
      if (! isset($_POST['year'.$i]) ) {
          continue;
      }
      if (! isset($_POST['desc'.$i]) ) {
          continue;
      }
      $year = $_POST["year" . $i];
      $desc = $_POST["desc" . $i];
      $stmt = $pdo->prepare(
          'INSERT INTO Position
          (profile_id, `rank`, year, description)
          VALUES ( :pid, :rank, :year, :desc)'
      );
      $stmt->execute(
          array(
          ':pid' => $profile_id,
          ':rank' => $rank,
          ':year' => $year,
          ':desc' => $desc)
      );
      $rank++;
  }
}
function insertEducation($pdo, $profile_id){
  $rank = 1;
  for ($i = 1; $i <= 9; $i++) {
      if (! isset($_POST['edu_year'.$i]) ) {
          continue;
      }
      if (! isset($_POST['edu_school'.$i]) ) {
          continue;
      }
      $year = $_POST["edu_year".$i];
      $school = $_POST["edu_school".$i];

      $institution_id = false;
      $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
      $stmt->execute(array(':name'=> $school ));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row !== false) $institution_id = $row['institution_id'];

      if ($institution_id === false){
        $stmt = $pdo->prepare(
            'INSERT INTO Institution
            (name)
            VALUES ( :name)'
        );
        $stmt->execute(array(':name' => $school));
        $institution_id = $pdo->lastInsertId();

      }
      $stmt = $pdo->prepare('INSERT INTO Education (profile_id, institution_id ,`rank`, year)
                            VALUES (:pid, :iid, :ran, :year)');
      $stmt->execute(array(
        ':pid'=> $profile_id,
        ':ran' => $rank,
        'year' => $year,
        ':iid' => $institution_id
      ));
      $rank++;
  }
}
