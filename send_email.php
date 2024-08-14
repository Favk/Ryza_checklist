<?php
require 'path/to/PHPMailer.php';
require 'path/to/SMTP.php';
require 'path/to/Exception.php';
require('fpdf186/fpdf.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PDF extends FPDF {
    // Page header
    function Header() {
        // Logo
        $this->Image('images/ryza_llc.webp',10,6,30);
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30,10,'Checklist',1,0,'C');
        // Line break
        $this->Ln(20);
    }

    // Page footer
    function Footer() {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tasks'])) {
    $tasks = $_POST['tasks'];

    // Create instance of PDF class
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',12);
    
    // Add tasks to PDF
    foreach ($tasks as $task) {
        $pdf->Cell(0,10,$task,0,1);
    }

    // Save the PDF to a string
    $pdfOutput = $pdf->Output('S');

    // Setup PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@example.com'; // SMTP username
        $mail->Password = 'your_password'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('your_email@example.com', 'Janitorial Checklist');
        $mail->addAddress('recipient@example.com'); // Add a recipient

        // Attachments
        $mail->addStringAttachment($pdfOutput, 'tasks.pdf');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Checklist PDF';
        $mail->Body = 'Please find the attached PDF containing the checked tasks.';

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo 'No tasks were checked.';
}
?>
