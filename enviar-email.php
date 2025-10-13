<?php
// Configurações SMTP
ini_set('SMTP', 'mail.hidealhidraulica.com.br');
ini_set('smtp_port', '465');
ini_set('sendmail_from', 'noreply@hidealhidraulica.com.br');

header('Content-Type: application/json; charset=utf-8');

// Verificar se é uma requisição POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Receber e sanitizar os dados do formulário
    $nome = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $telefone = strip_tags(trim($_POST["phone"]));
    $assunto = strip_tags(trim($_POST["subject"]));
    $mensagem = strip_tags(trim($_POST["message"]));
    
    // Validar os dados
    if (empty($nome) || empty($email) || empty($telefone) || empty($assunto) || empty($mensagem)) {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Por favor, preencha todos os campos obrigatórios."));
        exit;
    }
    
    // Validar e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Por favor, insira um e-mail válido."));
        exit;
    }
    
    // Configurar o destinatário
    $destinatario = "contato@hidealhidraulica.com.br";
    
    // Traduzir o assunto
    $assuntos = array(
        "orcamento" => "Solicitação de Orçamento",
        "manutencao" => "Agendamento de Manutenção",
        "pecas" => "Peças de Reposição",
        "duvidas" => "Dúvidas",
        "outros" => "Outros Assuntos"
    );
    
    $assunto_email = isset($assuntos[$assunto]) ? $assuntos[$assunto] : "Contato pelo Site";
    
    // Criar o corpo do e-mail
    $corpo_email = "Nova mensagem recebida pelo site Hideal Hidráulica\n\n";
    $corpo_email .= "Nome: $nome\n";
    $corpo_email .= "E-mail: $email\n";
    $corpo_email .= "Telefone: $telefone\n";
    $corpo_email .= "Assunto: $assunto_email\n\n";
    $corpo_email .= "Mensagem:\n$mensagem\n";
    
    // Criar o corpo do e-mail em HTML
    $corpo_html = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #2A77F3; color: white; padding: 20px; text-align: center; }
            .content { background-color: #f5f5f5; padding: 20px; }
            .info { background-color: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
            .label { font-weight: bold; color: #2A77F3; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Nova Mensagem - Hideal Hidráulica</h2>
            </div>
            <div class='content'>
                <div class='info'>
                    <p><span class='label'>Nome:</span> $nome</p>
                </div>
                <div class='info'>
                    <p><span class='label'>E-mail:</span> $email</p>
                </div>
                <div class='info'>
                    <p><span class='label'>Telefone:</span> $telefone</p>
                </div>
                <div class='info'>
                    <p><span class='label'>Assunto:</span> $assunto_email</p>
                </div>
                <div class='info'>
                    <p><span class='label'>Mensagem:</span></p>
                    <p>$mensagem</p>
                </div>
            </div>
            <div class='footer'>
                <p>Esta mensagem foi enviada através do formulário de contato do site Hideal Hidráulica</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Configurar os headers do e-mail
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Site Hideal <noreply@hidealhidraulica.com.br>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Enviar o e-mail
    if (mail($destinatario, "Contato Site - $assunto_email", $corpo_html, $headers)) {
        http_response_code(200);
        echo json_encode(array("success" => true, "message" => "Mensagem enviada com sucesso! Entraremos em contato em breve."));
    } else {
        http_response_code(500);
        echo json_encode(array("success" => false, "message" => "Erro ao enviar mensagem. Por favor, tente novamente ou entre em contato por telefone."));
    }
    
} else {
    http_response_code(403);
    echo json_encode(array("success" => false, "message" => "Método não permitido."));
}
?>
