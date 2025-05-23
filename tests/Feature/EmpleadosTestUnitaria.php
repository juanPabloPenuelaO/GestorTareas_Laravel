<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Empleado;
use Livewire\Livewire;

class EmpleadosTestUnitaria extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crear_un_empleado()
    {
        Livewire::test('App\Http\Livewire\Empleados')
            ->set('nombre', 'Juanito pelaes')
            ->set('correo', 'juanpe@gmail.com.com')
            ->call('store');

        $this->assertTrue(Empleado::where('correo', 'juanpe@gmail.com.com')->exists());
    }

    /** @test */
    public function puede_actualizar_un_empleado()
    {
        $empleado = Empleado::factory()->create([
            'nombre' => 'Luis',
            'correo' => 'luis@example.com'
        ]);

        Livewire::test('App\Http\Livewire\Empleados')
            ->call('edit', $empleado->id)
            ->set('nombre', 'Luis Actualizado')
            ->set('correo', 'luisnuevo@example.com')
            ->call('update');

        $this->assertDatabaseHas('empleados', ['nombre' => 'Luis Actualizado', 'correo' => 'luisnuevo@example.com']);
    }

    /** @test */
    public function puede_eliminar_un_empleado()
    {
        $empleado = Empleado::factory()->create();

        Livewire::test('App\Http\Livewire\Empleados')
            ->call('destroy', $empleado->id);

        $this->assertDatabaseMissing('empleados', ['id' => $empleado->id]);
    }
}
