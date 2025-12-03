<?php
require __DIR__ . "/../app/core/Mailer.php";

$result = Mailer::sendMail(
    "anyemail@example.com", 
    "Test Mail", 
    "<p>Dit is een testmail via Mailtrap.</p>"
);

if ($result) {
    echo "Mail verstuurd!";
} else {
    echo "Mail NIET verstuurd!";
}
