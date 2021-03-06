<?php
if(isset($_POST['submit'])){
  require('dbHandler.php');
  $username = $_POST['username'];
  $passWord = $_POST['passWord'];
  $pwdHash = password_hash($passWord, PASSWORD_DEFAULT);
// check if the fields are empty
  if(empty($username) && empty($passWord)){
    header('Location: ../adminLogin.php?error=emptyfields');
    exit();
  }

  else if(!empty($username) && empty($passWord)){
    header('Location: ../adminLogin.php?passwordRequired');
    exit();
  }
  else{
    $query = 'SELECT * FROM adminUsers WHERE username=?';
    // initialize the prepared statement
    $preparedStmt = mysqli_stmt_init($connectToDb);


    // run the SQL and prepared statement
    // runs query string in the DB and checks for errors
    if(!mysqli_stmt_prepare($preparedStmt, $query)){
      header('Location: ../adminLogin.php?error=sqlerror');
      exit();
    }
    else{
      // if no errors in the SQL query
      mysqli_stmt_bind_param($preparedStmt, "s", $username);
      // now execute the binded prepared statement
      mysqli_stmt_execute($preparedStmt);

      // get results from the DB
      $results = mysqli_stmt_get_result($preparedStmt);
      // check to see if we got results from the DB

      // fetch data from the result and place results in an ASSOCIATIVE ARRAY
      // which is the format that can be worked with in PHP
      if($row = mysqli_fetch_assoc($results)){
        // grab the password from the user, hash it and see if it matches with the pwd that the user tried to login with
        $checkPwd = password_verify($passWord, $row['passWord']);

        if($checkPwd == false){
          header('Location: ../adminLogin.php?error=wrongpassword');
          exit();
        }
        else if($checkPwd == true){
          // CHECK IF THE USER'S NAME IS ALSO IN ADMIN TABLE.

          // IF IT IS THEN GO TO ADMIN PAGE, OTHERWISE START SESSION AS CUSTOMER USER
          session_start(); // start a session to remember the user on the SERVER
          $_SESSION['username']=$row['username'];
          header('Location: ../admindashboard2/template/adminDash2.php');
          exit();
        }
        else{
          // if the user entered a password that is not a string
          header('Location: ../adminLogin.php?error=wrongpassword');
          exit();
        }
      }
      else{
        // if no results were matched in the DB
        header('Location: ../adminLogin.php?error=nouser');
        exit();
      }

    }
  }
}
else{
  header('Location: ../homepage.php');
  exit();
}


?>
