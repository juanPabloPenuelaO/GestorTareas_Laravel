<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Empleado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PruebaNoFuncionalRendimientoCarga extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestData();
    }

    /**
     * Crear datos para las pruebas de rendimiento
     */
    private function createTestData()
    {
        Empleado::factory()->count(1000)->create();
    }

    /**
     * Prueba de rendimiento
     * 
     * @test
     * @group performance
     */
    public function test_search_performance_with_large_dataset()
    {
        $startTime = microtime(true);
        
        $keyword = 'test';
        $keyWord = '%' . $keyword . '%';
        
        $empleados = Empleado::latest()
            ->orWhere('nombre', 'LIKE', $keyWord)
            ->orWhere('correo', 'LIKE', $keyWord)
            ->paginate(10);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(500, $executionTime, 
            'La búsqueda debe completarse en menos de 500ms');
        
        $this->assertNotNull($empleados);
        $this->assertTrue($empleados->hasPages() || $empleados->count() > 0);
        
        // Log para análisis
        $this->logPerformanceMetric('search_execution_time', $executionTime);
    }

    /**
     * Prueba de rendimiento
     * 
     * @test
     * @group performance
     */
    public function test_pagination_queries_optimization()
    {
        DB::enableQueryLog();
        
        $keyword = 'empleado';
        $keyWord = '%' . $keyword . '%';
        
        $empleados = Empleado::latest()
            ->orWhere('nombre', 'LIKE', $keyWord)
            ->orWhere('correo', 'LIKE', $keyWord)
            ->paginate(10);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $this->assertLessThanOrEqual(3, $queryCount, 
            'El número de consultas debe ser mínimo para evitar problemas N+1');

        DB::disableQueryLog();
        
        $this->logPerformanceMetric('pagination_query_count', $queryCount);
    }

    /**
     * Prueba de rendimiento
     * 
     * @test
     * @group performance
     */
    public function test_memory_usage_during_pagination()
    {
        $memoryBefore = memory_get_usage(true);
        
        for ($page = 1; $page <= 5; $page++) {
            $empleados = Empleado::latest()->paginate(10, ['*'], 'page', $page);
            unset($empleados);
        }
        
        $memoryAfter = memory_get_usage(true);
        $memoryUsed = ($memoryAfter - $memoryBefore) / 1024 / 1024;

        $this->assertLessThan(10, $memoryUsed, 
            'El uso de memoria no debe exceder 10MB durante la paginación');
        
        $this->logPerformanceMetric('memory_usage_mb', $memoryUsed);
    }

    /**
     * Prueba de rendimiento
     * 
     * @test
     * @group performance
     */
    public function test_crud_operations_performance()
    {
        $operations = [];
        
        $startTime = microtime(true);
        $empleado = Empleado::create([
            'nombre' => $this->faker->name,
            'correo' => $this->faker->unique()->email,
        ]);
        $operations['create'] = (microtime(true) - $startTime) * 1000;

        $startTime = microtime(true);
        $found = Empleado::findOrFail($empleado->id);
        $operations['read'] = (microtime(true) - $startTime) * 1000;

        $startTime = microtime(true);
        $found->update([
            'nombre' => $this->faker->name,
            'correo' => $this->faker->unique()->email,
        ]);
        $operations['update'] = (microtime(true) - $startTime) * 1000;

        $startTime = microtime(true);
        $found->delete();
        $operations['delete'] = (microtime(true) - $startTime) * 1000;

        foreach ($operations as $operation => $time) {
            $this->assertLessThan(100, $time, 
                "La operación {$operation} debe completarse en menos de 100ms");
            $this->logPerformanceMetric("crud_{$operation}_time", $time);
        }
    }

    /**
     * Prueba de rendimiento
     * 
     * @test
     * @group performance
     */
    public function test_validation_performance()
    {
        $validationData = [
            'nombre' => '',
            'correo' => '',
        ];

        $startTime = microtime(true);
        
        for ($i = 0; $i < 100; $i++) {
            $validator = validator($validationData, [
                'nombre' => 'required',
                'correo' => 'required',
            ]);
            $validator->fails();
        }

        $executionTime = (microtime(true) - $startTime) * 1000;

        $this->assertLessThan(1000, $executionTime, 
            'Las validaciones múltiples deben completarse en menos de 1 segundo');
        
        $this->logPerformanceMetric('validation_batch_time', $executionTime);
    }

    /**
     * métricas de rendimiento
     */
    private function logPerformanceMetric(string $metric, float $value)
    {
        $timestamp = Carbon::now()->toISOString();
        $logEntry = "[PERFORMANCE] {$timestamp} - {$metric}: {$value}";
        
        error_log($logEntry);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
}

namespace Tests\Unit\Livewire;

use Tests\TestCase;
use App\Http\Livewire\Empleados;
use App\Models\Empleado;
use Livewire\Livewire;
use Livewire\Testing\TestableLivewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Session;

class EmpleadosLoadTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestData();
    }

    private function createTestData()
    {
        Empleado::factory()->count(500)->create();
    }

    /**
     * Prueba de carga
     * 
     * @test
     * @group load
     */
    public function test_component_renders_with_large_dataset()
    {
        $startTime = microtime(true);
        
        $component = Livewire::test(Empleados::class);
        
        $renderTime = (microtime(true) - $startTime) * 1000;

        $component->assertOk()
                 ->assertViewIs('livewire.empleados.view')
                 ->assertSee('Empleados :3');

        $this->assertLessThan(2000, $renderTime, 
            'El componente debe renderizarse en menos de 2 segundos');
        
        $this->logLoadMetric('initial_render_time', $renderTime);
    }

    /**
     * Prueba de carga
     * 
     * @test
     * @group load
     */
    public function test_concurrent_search_operations()
    {
        $searchTerms = ['empleado', 'test', 'admin', 'user', 'manager'];
        $results = [];

        foreach ($searchTerms as $term) {
            $startTime = microtime(true);
            
            $component = Livewire::test(Empleados::class)
                ->set('keyWord', $term)
                ->assertOk();
                
            $searchTime = (microtime(true) - $startTime) * 1000;
            $results[] = $searchTime;

            // Verificar que la búsqueda responde adecuadamente
            $this->assertLessThan(1000, $searchTime, 
                "La búsqueda para '{$term}' debe completarse en menos de 1 segundo");
        }

        $averageTime = array_sum($results) / count($results);
        $this->logLoadMetric('average_search_time', $averageTime);
    }

    /**
     * Prueba de carga
     * 
     * @test
     * @group load
     */
    public function test_consecutive_crud_operations()
    {
        $component = Livewire::test(Empleados::class);
        $operationTimes = [];

        for ($i = 0; $i < 10; $i++) {
            $startTime = microtime(true);
            
            $component->set('nombre', $this->faker->name)
                     ->set('correo', $this->faker->unique()->email)
                     ->call('store')
                     ->assertHasNoErrors();
                     
            $operationTimes['create'][] = (microtime(true) - $startTime) * 1000;
        }

        $empleados = Empleado::take(5)->get();

        foreach ($empleados as $empleado) {
            $startTime = microtime(true);
            
            $component->call('edit', $empleado->id)
                     ->set('nombre', $this->faker->name)
                     ->set('correo', $this->faker->unique()->email)
                     ->call('update')
                     ->assertHasNoErrors();
                     
            $operationTimes['update'][] = (microtime(true) - $startTime) * 1000;
        }

        foreach ($operationTimes as $operation => $times) {
            $averageTime = array_sum($times) / count($times);
            $this->assertLessThan(500, $averageTime, 
                "El promedio de operaciones {$operation} debe ser menor a 500ms");
            $this->logLoadMetric("average_{$operation}_time", $averageTime);
        }
    }

    /**
     * Prueba de carga
     * 
     * @test
     * @group load
     */
    public function test_pagination_under_load()
    {
        $component = Livewire::test(Empleados::class);
        $pageLoadTimes = [];

        for ($page = 1; $page <= 10; $page++) {
            $startTime = microtime(true);
            
            $component->set('page', $page)
                     ->assertOk();
                     
            $pageLoadTimes[] = (microtime(true) - $startTime) * 1000;
        }

        $averagePageLoad = array_sum($pageLoadTimes) / count($pageLoadTimes);
        $maxPageLoad = max($pageLoadTimes);

        $this->assertLessThan(800, $averagePageLoad, 
            'El tiempo promedio de carga de página debe ser menor a 800ms');
        $this->assertLessThan(1500, $maxPageLoad, 
            'El tiempo máximo de carga de página debe ser menor a 1.5 segundos');

        $this->logLoadMetric('average_page_load_time', $averagePageLoad);
        $this->logLoadMetric('max_page_load_time', $maxPageLoad);
    }

    /**
     * Prueba de carga: Manejo de sesión y estados
     * 
     * @test
     * @group load
     */
    public function test_session_and_state_management_under_load()
    {
        $component = Livewire::test(Empleados::class);
        
        $stateChanges = [
            ['updateMode', true],
            ['updateMode', false],
            ['keyWord', 'test'],
            ['keyWord', ''],
            ['nombre', 'Test Name'],
            ['correo', 'test@example.com'],
        ];

        $startTime = microtime(true);

        foreach ($stateChanges as [$property, $value]) {
            $component->set($property, $value);
        }

        $stateManagementTime = (microtime(true) - $startTime) * 1000;

        $this->assertLessThan(200, $stateManagementTime, 
            'El manejo de estados múltiples debe completarse en menos de 200ms');

        $component->assertSet('nombre', 'Test Name')
                 ->assertSet('correo', 'test@example.com');

        $this->logLoadMetric('state_management_time', $stateManagementTime);
    }

    /**
     * Prueba de carga
     * 
     * @test
     * @group load
     */
    public function test_mass_validation_performance()
    {
        $component = Livewire::test(Empleados::class);
        $validationTimes = [];

        $testCases = [
            ['nombre' => '', 'correo' => ''],
            ['nombre' => 'Test', 'correo' => ''],
            ['nombre' => '', 'correo' => 'test@example.com'],
            ['nombre' => 'Test', 'correo' => 'invalid-email'],
            ['nombre' => 'Valid Name', 'correo' => 'valid@example.com'],
        ];

        foreach ($testCases as $testData) {
            $startTime = microtime(true);
            
            $component->set('nombre', $testData['nombre'])
                     ->set('correo', $testData['correo'])
                     ->call('store');
                     
            $validationTimes[] = (microtime(true) - $startTime) * 1000;
        }

        $averageValidationTime = array_sum($validationTimes) / count($validationTimes);
        
        $this->assertLessThan(300, $averageValidationTime, 
            'El tiempo promedio de validación debe ser menor a 300ms');

        $this->logLoadMetric('average_validation_time', $averageValidationTime);
    }

    //  métricas de carga
    private function logLoadMetric(string $metric, float $value)
    {
        $timestamp = now()->toISOString();
        $logEntry = "[LOAD TEST] {$timestamp} - {$metric}: {$value}ms";
        
        // En un entorno real, esto podría enviarse a un sistema de monitoreo
        error_log($logEntry);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Limpiar sesiones y estados
        Session::flush();
        
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
}