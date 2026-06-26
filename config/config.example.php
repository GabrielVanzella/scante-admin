<?php
// Copie este arquivo para config.php e preencha com seus valores

// Configurações gerais
define('APP_NAME', 'ScanTE Admin');
define('APP_URL',  'http://localhost:8099');  // URL base do painel
define('APP_VERSION', '1.0.0');

// Banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'scante_admin');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Sessão
define('SESSION_NAME', 'scante_session');
define('SESSION_LIFETIME', 7200); // 2 horas

// Chave secreta para tokens da API (altere para uma string aleatória longa)
define('API_SECRET', 'MUDE_PARA_UMA_CHAVE_SECRETA_FORTE');

// Mercado Pago
define('MP_ACCESS_TOKEN', '');  // Access token do Mercado Pago
define('MP_WEBHOOK_SECRET', ''); // Secret do webhook do Mercado Pago
