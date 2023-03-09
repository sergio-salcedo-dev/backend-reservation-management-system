<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Appointment Confirmation</title>
</head>
<body>
<h1>Appointment Confirmation</h1>
<h2>Hello {{ $patientFullName }},</h2>
<p>Your appointment has been scheduled for:</p>
<ul>
    <li>Date: {{ $date }}</li>
    <li>Time: {{ $time }}</li>
    <li>Duration: {{ $duration }} h.</li>
    <li>Type: {{ $type }}</li>
</ul>
<p>Thank you for choosing our clinic. We look forward to seeing you soon!</p>
<p>Have a nice day.</p>
</body>
</html>
