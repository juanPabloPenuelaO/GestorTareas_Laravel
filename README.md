# Pruebas Funcionales y No Funcionales - Laravel

Se entregan las debidas pruebas diseñadas para el módulo del CRUD de empleados desarrollado en Laravel. Se incluyen pruebas **funcionales** y **no funcionales**, ejecutables mediante Artisan y Dusk.

---

## 🔧 Requisitos

- PHP
- Laravel
- Composer
- Laravel Dusk
- Navegador Chrome (para Dusk)

---

## 📂 Ejecución de pruebas

PRUEBAS FUNCIONALES:

prueba unitaria:php artisan test tests/Feature/EmpleadosTestUnitaria.php

prueba de integracion:php artisan test --filter EmpleadosIntegracionTest

prueba de sistema: php artisan dusk tests/Browser/CrearEmpleadoTestSistema.php



PRUEBAS NO FUNCIONALES:

-prueba de rendimiento: php artisan test --group=performance

-prueba de seguridad: php artisan test tests/Feature/PruebaNoFuncionalSeguridad.php

-prueba de rendimiento y de carga: php artisan test --group=load


