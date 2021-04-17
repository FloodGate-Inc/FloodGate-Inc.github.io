<?php
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $place = $_POST['place'];
  $subject = $_POST['subject'];
  $message = $_POST['message'];

  $to = "rajuubale@gmail.com";
  $headers = "From: info@emedica.in \r\n";
  $headers .= "Reply-To: info@emedica.in \r\n";

  $email_subject = $subject." - ".$name;

  $email_body = "New Message From Contact Form\r\n\r\n".$message."\r\n\r\n Name: ".$name."\r\n Email: ".$email."\r\n Phone: ".$phone."\r\n Place: ".$place;

  mail($to, $email_subject, $email_body, $headers);

  echo '<script type="text/javascript">
  var con = confirm("We appreciate your interest. A member of our team will get in contact with you within 24 hours.");
    window.open("index.html", "_self");
</script>';

  exit();

 ?>