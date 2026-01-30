use App\Http\Controllers\InfluxController;
use App\Http\Controllers\CommandController;

Route::get('/device/{device}', [InfluxController::class, 'getDeviceData']);
Route::post('/command', [CommandController::class, 'sendCommand']);
