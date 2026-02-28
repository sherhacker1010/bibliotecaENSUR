<?php
class QRHelper {
    public static function generate($data, $filename){
        // Ensure directory exists
        $path = 'uploads/qrcodes/';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $filePath = $path . $filename . '.png';
        
        // Use external API to generate QR
        // API: https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=Example
        $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($data);

        // Get image content
        $imageContent = file_get_contents($apiUrl);

        if($imageContent){
            file_put_contents($filePath, $imageContent);
            return $filePath;
        }

        return false;
    }
}
