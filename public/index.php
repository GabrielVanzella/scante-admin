<?php
declare(strict_types=1);

// Autoload simples (sem Composer)
spl_autoload_register(function (string $class) {
    $base = __DIR__ . '/../';
    $file = $base . str_replace(['App\\', '\\'], ['app/', '/'], $class) . '.php';
    if (file_exists($file)) require_once $file;
});

// Configurações
require_once __DIR__ . '/../config/config.php';

// Sessão
session_name(SESSION_NAME);
session_set_cookie_params(['lifetime' => SESSION_LIFETIME, 'httponly' => true, 'samesite' => 'Lax']);
session_start();

// Rotas
use App\Core\Router;

$router = new Router();

// Auth
$router->get('/login',  'AuthController', 'login');
$router->post('/login', 'AuthController', 'login');
$router->get('/logout', 'AuthController', 'logout');

// Admin
$router->get( '/admin',                             'Admin/DashboardController', 'index');
$router->get( '/admin/empresas',                    'Admin/EmpresasController',  'index');
$router->get( '/admin/empresas/criar',              'Admin/EmpresasController',  'criar');
$router->post('/admin/empresas/criar',              'Admin/EmpresasController',  'criar');
$router->get( '/admin/empresas/{id}',               'Admin/EmpresasController',  'ver');
$router->get( '/admin/empresas/{id}/editar',        'Admin/EmpresasController',  'editar');
$router->post('/admin/empresas/{id}/editar',        'Admin/EmpresasController',  'editar');
$router->get( '/admin/empresas/{id}/excluir',       'Admin/EmpresasController',  'excluir');
$router->get( '/admin/manual',                      'Admin/ManualController',    'index');
$router->get( '/admin/licencas',                    'Admin/LicencasController',  'index');
$router->post('/admin/licencas/gerar',              'Admin/LicencasController',  'gerar');
$router->get( '/admin/licencas/{id}',               'Admin/LicencasController',  'ver');
$router->post('/admin/licencas/{id}/revogar',       'Admin/LicencasController',  'revogar');
$router->post('/admin/licencas/{id}/reativar',      'Admin/LicencasController',  'reativar');
$router->post('/admin/licencas/{id}/estender',      'Admin/LicencasController',  'estender');
$router->post('/admin/licencas/{id}/alterar-tipo',  'Admin/LicencasController',  'alterarTipo');
$router->post('/admin/licencas/{id}/transferir',    'Admin/LicencasController',  'transferir');

// Empresa
$router->get( '/empresa',                                      'Empresa/DashboardController', 'index');
$router->get( '/empresa/licencas',                             'Empresa/LicencasController',  'index');
$router->get( '/empresa/licencas/{id}',                        'Empresa/LicencasController',  'ver');
$router->post('/empresa/licencas/{id}/transferir',             'Empresa/LicencasController',  'solicitarTransferencia');

// API REST (app Android)
$router->post('/api/licenca/validar',               'Api/LicencaController',      'validar');
$router->post('/api/webhook/mercadopago',           'Api/LicencaController',      'webhookMercadoPago');
$router->post('/api/dispositivo/ping',              'Api/DispositivoController',  'ping');

// Admin — Dispositivos
$router->get( '/admin/dispositivos',                'Admin/DispositivosController', 'index');

// Redirecionar raiz para login
$router->get('/', 'AuthController', 'login');

// Despachar
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
