<?php

namespace Tests\Browser;

use App\Models\Empleado;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CrearEmpleadoTestSistema extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_un_usuario_puede_crear_un_empleado_desde_la_interfaz()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/empleados')
                ->press('@btn-open-crear-empleado')
                ->waitFor('@modal-crear-empleado')
                ->type('@input-nombre', 'Carlos Mendoza')
                ->type('@input-correo', 'carlos@example.com')
                ->press('@btn-guardar')
                ->waitForText('Empleado creado exitosamente')
                ->assertSee('Carlos Mendoza');
        });
    }
}