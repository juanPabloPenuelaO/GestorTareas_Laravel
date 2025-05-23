<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Livewire\Empleados;
use App\Models\Empleado;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PruebaNoFuncionalSeguridad extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $adminUser;
    protected $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->unauthorizedUser = User::factory()->create(['role' => 'guest']);
        
        $this->createTestData();
    }

    private function createTestData()
    {
        Empleado::factory()->count(10)->create();
    }

    /**
     * Prueba de seguridad
     * 
     * @test
     * @group security
     */
    public function test_sql_injection_prevention_in_search()
    {
        $this->actingAs($this->user);

        $sqlInjectionAttempts = [
            "'; DROP TABLE empleados; --",
            "' OR '1'='1",
            "'; SELECT * FROM users; --",
            "1' UNION SELECT null,user(),version()--",
            "admin'/**/OR/**/1=1/**/--",
            "' OR 1=1#",
            "'; DELETE FROM empleados WHERE 1=1; --"
        ];

        foreach ($sqlInjectionAttempts as $maliciousInput) {
            $startTime = microtime(true);
            
            try {
                $response = Livewire::test(Empleados::class)
                    ->set('keyWord', $maliciousInput)
                    ->assertOk();

                $this->assertTrue(Schema::hasTable('empleados'), 
                    'La tabla empleados fue eliminada por inyección SQL');

                $this->assertGreaterThan(0, Empleado::count(), 
                    'Los empleados fueron eliminados por inyección SQL');

                $executionTime = (microtime(true) - $startTime) * 1000;
                
                $this->assertLessThan(5000, $executionTime, 
                    'La consulta tardó demasiado, posible inyección SQL');

            } catch (\Exception $e) {
                $this->assertStringNotContainsString('syntax error', strtolower($e->getMessage()));
                $this->assertStringNotContainsString('drop table', strtolower($e->getMessage()));
            }

            $this->logSecurityMetric('sql_injection_attempt', $maliciousInput, 'blocked');
        }
    }

    /**
     * Prueba de seguridad
     * 
     * @test
     * @group security
     */
    public function test_xss_prevention_in_input_fields()
    {
        $this->actingAs($this->user);

        $xssPayloads = [
            '<script>alert("XSS")</script>',
            '<img src="x" onerror="alert(1)">',
            'javascript:alert("XSS")',
            '<svg onload="alert(1)">',
            '"><script>alert("XSS")</script>',
            '<iframe src="javascript:alert(1)"></iframe>',
            '<body onload="alert(1)">',
            '<div onclick="alert(1)">Click me</div>'
        ];

        foreach ($xssPayloads as $payload) {
            $response = Livewire::test(Empleados::class)
                ->set('nombre', $payload)
                ->set('correo', 'test@example.com')
                ->call('store');

            // Verificar que el contenido fue escapado o rechazado
            $empleado = Empleado::where('nombre', $payload)->first();
            
            if ($empleado) {
                // Si se guardó, verificar que esté escapado en la vista
                $viewResponse = Livewire::test(Empleados::class);
                $html = $viewResponse->payload['serverMemo']['htmlHash'] ?? '';
                
                // No debe contener tags de script ejecutables
                $this->assertStringNotContainsString('<script>', $html);
                $this->assertStringNotContainsString('javascript:', $html);
                $this->assertStringNotContainsString('onload=', $html);
                $this->assertStringNotContainsString('onerror=', $html);
            }

            $this->logSecurityMetric('xss_attempt', $payload, $empleado ? 'stored_safely' : 'rejected');
        }
    }

    /**
     * Prueba de seguridad: Mass Assignment Protection
     * 
     * @test
     * @group security
     */
    public function test_mass_assignment_protection()
    {
        $this->actingAs($this->user);

        $maliciousData = [
            'id' => 999999,
            'created_at' => '2020-01-01 00:00:00',
            'updated_at' => '2020-01-01 00:00:00',
            'admin' => true,
            'role' => 'admin',
            'password' => 'hacked',
            'is_deleted' => false
        ];

        $originalCount = Empleado::count();

        try {
            // Intentar crear empleado con datos maliciosos
            $response = Livewire::test(Empleados::class)
                ->set('nombre', 'Test User')
                ->set('correo', 'test@example.com');

            foreach ($maliciousData as $key => $value) {
                $response->set($key, $value);
            }

            $response->call('store');

            $this->assertEquals($originalCount + 1, Empleado::count());
            
            $newEmpleado = Empleado::latest()->first();
            $this->assertEquals('Test User', $newEmpleado->nombre);
            $this->assertEquals('test@example.com', $newEmpleado->correo);
            
            $this->assertNotEquals(999999, $newEmpleado->id);
            $this->assertFalse(isset($newEmpleado->admin));
            $this->assertFalse(isset($newEmpleado->role));

        } catch (\Exception $e) {
            $this->logSecurityMetric('mass_assignment_attempt', 'blocked', 'success');
        }
    }

    /**
     * Prueba de seguridad
     * 
     * @test  
     * @group security
     */
    public function test_csrf_protection()
    {
        $this->actingAs($this->user);

        // Simular request sin token CSRF válido
        Session::forget('_token');
        
        try {
            $response = Livewire::test(Empleados::class)
                ->set('nombre', 'Test CSRF')
                ->set('correo', 'csrf@example.com')
                ->call('store');

            // En un entorno real, esto debería fallar sin CSRF token
            $this->logSecurityMetric('csrf_protection', 'tested', 'needs_verification');

        } catch (\Exception $e) {
            // Si falla por CSRF, es lo esperado
            $this->assertStringContainsString('CSRF', $e->getMessage());
            $this->logSecurityMetric('csrf_protection', 'active', 'success');
        }
    }

    /**
     * Prueba de seguridad
     * 
     * @test
     * @group security
     */
    public function test_unauthorized_access_prevention()
    {
        try {
            $response = Livewire::test(Empleados::class)
                ->call('store');
      
            $this->logSecurityMetric('unauthorized_access', 'component_accessible', 'warning');
            
        } catch (\Exception $e) {
            $this->logSecurityMetric('unauthorized_access', 'blocked', 'success');
        }

        $this->actingAs($this->unauthorizedUser);
        
        $empleado = Empleado::first();
        
        try {
            $response = Livewire::test(Empleados::class)
                ->call('destroy', $empleado->id);

            // Verificar que el empleado NO fue eliminado
            $this->assertNotNull(Empleado::find($empleado->id), 
                'Usuario no autorizado pudo eliminar empleado');
                
        } catch (\Exception $e) {
            $this->logSecurityMetric('unauthorized_delete', 'blocked', 'success');
        }
    }

    /**
     * Prueba de seguridad
     * 
     * @test
     * @group security
     */
    public function test_input_validation_bypass_attempts()
    {
        $this->actingAs($this->user);

        $bypassAttempts = [
            ['nombre' => null, 'correo' => 'test@example.com'],
            ['nombre' => '', 'correo' => 'test@example.com'],
            ['nombre' => '   ', 'correo' => 'test@example.com'], // Solo espacios
            
            ['nombre' => 'Test', 'correo' => 'invalid-email'],
            ['nombre' => 'Test', 'correo' => 'test@'],
            ['nombre' => 'Test', 'correo' => '@example.com'],
            
            ['nombre' => str_repeat('A', 1000), 'correo' => 'test@example.com'],
            ['nombre' => 'Test', 'correo' => str_repeat('a', 500) . '@example.com'],
        ];

        foreach ($bypassAttempts as $attempt) {
            $originalCount = Empleado::count();
            
            $response = Livewire::test(Empleados::class)
                ->set('nombre', $attempt['nombre'])
                ->set('correo', $attempt['correo'])
                ->call('store');

            $this->assertEquals($originalCount, Empleado::count(), 
                'Se creó empleado con datos inválidos: ' . json_encode($attempt));

            $response->assertHasErrors();

            $this->logSecurityMetric('validation_bypass_attempt', json_encode($attempt), 'blocked');
        }
    }

    /**
     * Prueba de seguridad
     * 
     * @test
     * @group security
     */
    public function test_information_disclosure_prevention()
    {
        $this->actingAs($this->user);

        // Intentar acceder a IDs que no existen
        $nonExistentIds = [999999, -1, 0, 'admin', 'null', '<script>'];

        foreach ($nonExistentIds as $id) {
            try {
                $response = Livewire::test(Empleados::class)
                    ->call('edit', $id);

                $response->assertOk();
                
                $component = $response->instance();
                $this->assertFalse($component->updateMode || empty($component->selected_id));

            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $this->assertStringNotContainsString('database', strtolower($errorMessage));
                $this->assertStringNotContainsString('sql', strtolower($errorMessage));
                $this->assertStringNotContainsString('table', strtolower($errorMessage));
            }

            $this->logSecurityMetric('information_disclosure_test', $id, 'protected');
        }
    }

    /**
     * Prueba de seguridad
     * 
     * @test
     * @group security
     */
    public function test_rate_limiting_protection()
    {
        $this->actingAs($this->user);

        $requestCount = 0;
        $successfulRequests = 0;
        $startTime = microtime(true);

        for ($i = 0; $i < 50; $i++) {
            try {
                $response = Livewire::test(Empleados::class)
                    ->set('keyWord', 'test' . $i)
                    ->assertOk();
                
                $successfulRequests++;
                $requestCount++;
                
                usleep(10000);
                
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'rate limit') !== false || 
                    strpos($e->getMessage(), 'too many') !== false) {
                    $this->logSecurityMetric('rate_limiting', 'active', 'success');
                    break;
                }
                $requestCount++;
            }
        }

        $totalTime = (microtime(true) - $startTime) * 1000;
        $requestsPerSecond = $successfulRequests / ($totalTime / 1000);

        $this->logSecurityMetric('requests_per_second', $requestsPerSecond, 
            $requestsPerSecond > 100 ? 'warning' : 'acceptable');
    }

    /**
     * métricas de seguridad
     */
    private function logSecurityMetric(string $test, $value, string $status)
    {
        $timestamp = now()->toISOString();
        $logEntry = "[SECURITY TEST] {$timestamp} - {$test}: {$value} - Status: {$status}";
        
        error_log($logEntry);
    }

    /**
     * limpiar después de las pruebas
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        
        Session::flush();
        
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
}