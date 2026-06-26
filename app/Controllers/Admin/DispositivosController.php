<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Dispositivo;

class DispositivosController extends Controller {

    public function index(): void {
        Auth::requireAdmin();
        $model = new Dispositivo();
        $this->view('admin.dispositivos.index', [
            'dispositivos' => $model->listar(),
            'stats'        => $model->estatisticas(),
        ], 'admin');
    }
}
