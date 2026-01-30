<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CameraController extends Controller
{
    private $uploadPath = '/var/www/smarthome-gateway/storage/uploads/';
    private $influxHost = 'localhost';
    private $influxPort = '8086';
    private $influxDB = 'smarthome';
    private $influxMeasurement = 'motion_log';

    /**
     * Handle semua request (upload, API, view)
     */
    public function index(Request $request)
    {
        // Buat folder uploads jika belum ada
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }

        // 1. HANDLE UPLOAD DARI ESP32 (POST tanpa parameter api)
        if ($request->isMethod('post') && !$request->has('api')) {
            return $this->handleUpload($request);
        }

        // 2. CEK KONEKSI ESP32 (GET ?check)
        if ($request->has('check')) {
            return response('CCTV Server Ready!', 200);
        }

        // 3. API UNTUK AJAX (GET ?api=check)
        if ($request->has('api') && $request->get('api') === 'check') {
            return $this->getStatus();
        }

        // 4. TAMPILKAN WEB INTERFACE
        return view('camera.index');
    }

    /**
     * Handle upload gambar dari ESP32
     */
    private function handleUpload(Request $request)
    {
        $filename = $request->header('X-Filename', 'unknown_image.jpg');
        $photoNum = $request->header('X-Photo-Number', '0');
        
        // Ambil raw binary data
        $imageData = $request->getContent();
        
        $destination = $this->uploadPath . $filename;
        
        if (file_put_contents($destination, $imageData)) {
            Log::info("📥 Menerima: $filename (Ukuran: " . strlen($imageData) . " bytes)");
            return response("Sukses Upload Foto #$photoNum: $filename", 200);
        } else {
            Log::error("✗ Gagal menyimpan $filename");
            return response("Gagal menyimpan file.", 500);
        }
    }

    /**
     * Get status untuk API (AJAX)
     */
    private function getStatus()
    {
        $latestImage = $this->getLatestImage();
        
        $response = [
            'has_motion' => false,
            'image' => null,
            'image_name' => null,
            'timestamp' => null,
            'message' => null
        ];

        if ($latestImage) {
            $imagePath = $this->uploadPath . $latestImage;
            $imageTime = filemtime($imagePath);
            
            $response['image'] = url('camera/image/' . $latestImage);
            $response['image_time'] = $imageTime;
            $response['has_motion'] = true;
            $response['timestamp'] = date('Y-m-d H:i:s', $imageTime);
            $response['message'] = 'Gerakan Terdeteksi';
            $response['image_name'] = $latestImage;
        }

        return response()->json($response);
    }

    /**
     * Get gambar terakhir
     */
    private function getLatestImage()
    {
        if (!is_dir($this->uploadPath)) {
            return null;
        }

        $files = glob($this->uploadPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        if (empty($files)) {
            return null;
        }

        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        return basename($files[0]);
    }

    /**
     * Serve gambar
     */
    public function serveImage($filename)
    {
        $path = $this->uploadPath . basename($filename);
        
        if (file_exists($path)) {
            return response()->file($path, [
                'Content-Type' => 'image/jpeg',
            ]);
        }
        
        abort(404, 'Image not found');
    }
}
