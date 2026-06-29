<?php
// PHP built-in server router — imita o RewriteRule do .htaccess
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // serve arquivos estáticos (css, js, imagens) diretamente
}
require __DIR__ . '/index.php';
