<?php

use App\Http\Controllers\ReviewController;
use App\Models\Task;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', static function () {
    return view('index');
});

Route::get('/tasks', static function () {
    return view('tasks.index', [
        'tasks' => Task::latest()->paginate()
    ]);
})->name('tasks.index');

Route::view('/tasks/create', 'tasks.create')
    ->name('tasks.create');

Route::get('/tasks/{task}/edit', static function (Task $task) {
    return view('tasks.edit', [
        'task' => $task
    ]);
})->name('tasks.edit');

Route::get('/tasks/{task}', static function (Task $task) {
    return view('tasks.show', [
        'task' => $task
    ]);
})->name('tasks.show');

Route::post('/tasks', static function (\App\Http\Requests\TaskRequest $request) {
    $task = Task::create($request->validated());

    return redirect()->route('tasks.show', ['task' => $task->id])
        ->with('success', 'Task created successfully!');
})->name('tasks.store');

Route::put('/tasks/{task}', static function (Task $task, \App\Http\Requests\TaskRequest $request) {
    $task->update($request->validated());

    return redirect()->route('tasks.show', ['task' => $task->id])
        ->with('success', 'Task updated successfully!');
})->name('tasks.update');

Route::delete('/tasks/{task}', static function (Task $task) {
    $task->delete();

    return redirect()->route('tasks.index')
        ->with('success', 'Task deleted successfully!');
})->name('tasks.destroy');

Route::fallback(static function () {
    return 'Still got somewhere! Fallback route works.';
});

Route::put('/tasks/{task}/toggle-complete', static function (Task $task) {
    $task->toggleComplete();
    return redirect()->back()->with('success', 'Task marked as completed!');
})->name('tasks.toggle-complete');


Route::resource('books', App\Http\Controllers\BookController::class)->only(['index', 'show']);

//Route::resource('books.reviews', \App\Http\Controllers\ReviewController::class)
//    ->scoped(['review' => 'book'])
//    ->only(['create', 'store']);

Route::get('books/{book}/reviews/create', [ReviewController::class, 'create'])
    ->name('books.reviews.create');

Route::post('books/{book}/reviews', [ReviewController::class, 'store'])
    ->name('books.reviews.store')
    ->middleware('throttle:reviews');
