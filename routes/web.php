<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Proyectos;
use App\Http\Livewire\Tareas;
use App\Http\Livewire\Comentarios;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::view('empleados', 'livewire.empleados.index')->middleware('auth');
Route::view('proyectos', 'livewire.proyectos.index')->middleware('auth');
Route::view('tareas', 'livewire.tareas.index')->middleware('auth');
Route::view('comentarios', 'livewire.comentarios.index')->middleware('auth');