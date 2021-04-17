<?php
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $place = $_POST['place'];
    $position = $_POST['position'];
    

    if(isset($_POST['submit'])){
      $file_name       = $_FILES['resume']['name'];  
      $file_temp_name  = $_FILES['resume']['tmp_name'];  
      if(isset($name) and !empty($name)){
          $location = 'uploads/';      
          if(move_uploaded_file($file_temp_name, $location.$file_name)){
            $file = $location.$file_name;
            $file_size = filesize($file);
            $handle = fopen($file, "r");
            $content = fread($handle, $file_size);
            fclose($handle);
          
            $content = chunk_split(base64_encode($content));
            $uid = md5(uniqid(time()));
            $filename = basename($file);
          
            $eol = PHP_EOL;
          
            $to = "hr@emedica.in";
          
            $header = "From: info@emedica.in <info@emedica.in>".$eol;
            $header .= "Reply-To: info@emedica.in".$eol;
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"";
          
            $email_subject = "New Candidate Registration for ".$position;
            
            $message = "--".$uid.$eol;
            $message .= "Content-Type: text/html; charset=ISO-8859-1".$eol;
            $message .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
            $message .= "Candidate Registration<br><hr>Name: ".$name."<br>Position: ".$position."<br>Email: ".$email."<br>Phone: ".$phone."<br>Place: ".$place.$eol;
            $message .= "--".$uid.$eol;
            $message .= "Content-Type: application/pdf; name=\"".$file_name."\"".$eol;
            $message .= "Content-Transfer-Encoding: base64".$eol;
            $message .= "Content-Disposition: attachment; filename=\"".$file_name."\"".$eol;
            $message .= $content.$eol;
            $message .= "--".$uid."--";
          
            mail($to, $email_subject, $message, $header);
          }
      } else {
          echo 'You should select a file to upload !!';
      }
  }

  unlink($location.$file_name);
  

    echo '<script type="text/javascript">
    var con = confirm("We appreciate your interest. A member of our team will get in contact with you within 24 hours.");
      window.open("index.html", "_self");
  </script>';

    exit();
?>