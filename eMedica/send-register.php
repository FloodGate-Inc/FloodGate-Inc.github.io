<?php
  $name = $_POST['name'];
  $email = $_POST['email'];
  $address1 = $_POST['address1'];
  $address2 = ($_POST['address2'] != null ? $_POST['address2'] : 'NONE');
  $city = $_POST['city'];
  $state = $_POST['state'];
  $pincode = $_POST['pincode'];
  $phone = $_POST['phone'];
  $remarks = ($_POST['remarks'] != null ? $_POST['remarks'] : 'NONE');

  $to = "info.emedica@gmail.com";
  $headers = "From: info@emedica.in \r\n";
  $headers .= "Reply-To: info@emedica.in \r\n";

  $email_subject = "New Distributor Registration - ".$name;

  $email_body = "New Distributor Registration\r\n\r\n\r\n Name: ".$name."\r\n Email: ".$email."\r\n Phone: ".$phone."\r\n Address Line 1: ".$address1."\r\n Address Line 2: ".$address2."\r\n City: ".$city."\r\n State: ".$state."\r\n Pin Code: ".$pincode."\r\n Remarks: ".$remarks;

  mail($to, $email_subject, $email_body, $headers);

  echo '<script type="text/javascript">
  var con = confirm("We appreciate your interest. A member of our team will get in contact with you within 24 hours.");
    window.open("index.html", "_self");
</script>';

  exit();

 ?>