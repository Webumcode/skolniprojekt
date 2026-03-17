<?php
// Nastavení
$to = "webum@post.cz"; // Kam budou chodit maily
$subject_prefix = "Kontaktní formulář - Doupě géniů"; // Předmět emailu

// Funkce pro čištění vstupů
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Kontrola, jestli byl formulář odeslán
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Získání a vyčištění dat
    $name = isset($_POST['name']) ? clean_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? clean_input($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? clean_input($_POST['subject']) : '';
    $message = isset($_POST['message']) ? clean_input($_POST['message']) : '';
    
    // Validace
    $errors = array();
    
    if (empty($name)) {
        $errors[] = "Jméno je povinné";
    }
    
    if (empty($email)) {
        $errors[] = "E-mail je povinný";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Neplatný formát e-mailu";
    }
    
    if (empty($subject)) {
        $errors[] = "Předmět je povinný";
    }
    
    if (empty($message)) {
        $errors[] = "Zpráva je povinná";
    }
    
    // Pokud nejsou chyby, pošli email
    if (empty($errors)) {
        
        // Hlavičky emailu
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Tělo emailu (HTML)
        $email_content = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 10px 10px; }
                .field { margin-bottom: 20px; }
                .field-label { font-weight: bold; color: #3b82f6; margin-bottom: 5px; }
                .field-value { background: white; padding: 10px; border-radius: 5px; border: 1px solid #eee; }
                .footer { text-align: center; margin-top: 20px; color: #888; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Nová zpráva z webu Doupě géniů</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='field-label'>📝 Jméno:</div>
                        <div class='field-value'>" . $name . "</div>
                    </div>
                    
                    <div class='field'>
                        <div class='field-label'>📧 E-mail:</div>
                        <div class='field-value'>" . $email . "</div>
                    </div>
                    
                    <div class='field'>
                        <div class='field-label'>📌 Předmět:</div>
                        <div class='field-value'>" . $subject . "</div>
                    </div>
                    
                    <div class='field'>
                        <div class='field-label'>💬 Zpráva:</div>
                        <div class='field-value'>" . nl2br($message) . "</div>
                    </div>
                    
                    <div class='field'>
                        <div class='field-label'>🕐 Čas odeslání:</div>
                        <div class='field-value'>" . date('d.m.Y H:i:s') . "</div>
                    </div>
                </div>
                <div class='footer'>
                    Tento email byl odeslán z kontaktního formuláře na webu Doupě géniů.
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Odeslání emailu
        $full_subject = $subject_prefix . ": " . $subject;
        
        if (mail($to, $full_subject, $email_content, $headers)) {
            // Úspěch - přesměrování zpět s parametrem success
            header("Location: kontakt.html?status=success");
            exit();
        } else {
            // Chyba odesílání
            header("Location: kontakt.html?status=error&message=" . urlencode("Email se nepodařilo odeslat"));
            exit();
        }
        
    } else {
        // Chyby validace - přesměrování s chybovou hláškou
        $error_message = implode(", ", $errors);
        header("Location: kontakt.html?status=error&message=" . urlencode($error_message));
        exit();
    }
    
} else {
    // Pokud někdo přistoupí na PHP skript přímo bez POST
    header("Location: kontakt.html");
    exit();
}
?>
