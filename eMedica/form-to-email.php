<?php
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $place = $_POST['place'];
  $openings = $_POST['openings'];


    $cv = pathinfo($_FILES['cv']['name']);
    $ext = $cv['extension']; // get the extension of the file
    $newname = "cv.".$ext; 

    $target = 'uploads/'.$newname;
    move_uploaded_file( $_FILES['cv']['tmp_name'], $target);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $email = new PHPMailer();
    $email->SetFrom('applynow@emedica.in', 'Apply Now');
    $email->Subject   = $openings.' - New Candidate Registration';
    $email->Body      = 'Name: '.$name.'/n'.'Email: '.$email.'/n'.'Phone: '.$phone.'\n'.'Place: '.$place.'\n'.'Position: '.$openings;
    $email->AddAddress( 'nirmalnishant645@gmail.com' );

    $file_to_attach = 'uploads/';

    $email->AddAttachment( $file_to_attach , $newname );

    return $email->Send();

?>