<?php

use App\Events\TaskCreated;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    
    Route::middleware(['check.project.participant'])->group(function () {
        Route::get('/project/{project}', function (Project $project) {
            $project->load(('tasks'));
            $currentUser = auth()->user();

            return Inertia::render('Projects', [
                'project' => $project,
                'currentUser' => $currentUser
            ]);
        });

        Route::get('api/projects/{project}', function (Project $project) {
            return $project->tasks->pluck('body');
        });

        Route::post('api/projects/{project}/tasks', function (Project $project, Request $request) {
            $task = $project->tasks()->create(['body' => $request['body']]);

            event(new TaskCreated($task));
        });
    });

    // Route::get('/tasks', function () {
    //     $tasks = Task::latest()->pluck('body');
    //     return Inertia::render('Tasks', [
    //         'tasks' => $tasks
    //     ]);
    // });


    // Route::post('/tasks', function (Request $request) {
    //     $task = Task::forceCreate(['body' => $request['body']]);

    //     event(new TaskCreated($task));
    // });
});
