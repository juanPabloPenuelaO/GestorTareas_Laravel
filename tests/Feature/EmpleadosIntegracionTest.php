<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Empleado;
use Livewire\Livewire;

class EmpleadosIntegracionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function se_puede_crear_y_recuperar_un_empleado_via_livewire()
    {
        Livewire::test('App\Http\Livewire\Empleados')
            ->set('nombre', 'Ana Torres')
            ->set('correo', 'anator@gmail.com')
            ->call('store');

        $empleado = Empleado::where('correo', 'anator@gmail.com')->first();

        $this->assertNotNull($empleado);
        $this->assertEquals('Ana Torres', $empleado->nombre);
    }
}
