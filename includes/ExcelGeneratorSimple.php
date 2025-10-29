<?php

class ExcelGeneratorSimple {
    
    public static function generarXLSX($data, $filename = 'export.xlsx') {
        // Validar datos
        if (empty($data) || !is_array($data)) {
            throw new Exception("No hay datos para exportar");
        }
        
        // Limpiar buffer de salida
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Generar archivo CSV como alternativa más confiable
        $csvContent = self::generarCSV($data);
        
        // Crear archivo temporal
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        file_put_contents($tempFile, $csvContent);
        
        // Verificar que el archivo se creó correctamente
        if (!file_exists($tempFile) || filesize($tempFile) == 0) {
            throw new Exception("El archivo no se generó correctamente");
        }
        
        // Enviar headers para Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tempFile));
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        
        // Enviar archivo
        readfile($tempFile);
        unlink($tempFile);
        exit;
    }
    
    private static function generarCSV($data) {
        $output = '';
        
        // BOM para UTF-8 (para que Excel reconozca correctamente los caracteres especiales)
        $output .= "\xEF\xBB\xBF";
        
        // Encabezados
        $headers = ['Fecha', 'Producto', 'Tipo', 'Cantidad', 'Precio Unitario', 'Total', 'Usuario', 'Referencia', 'Notas'];
        $output .= implode(',', array_map([self::class, 'escapeCSV'], $headers)) . "\n";
        
        // Datos
        foreach ($data as $row) {
            $fields = [
                $row['fecha'] ?? '',
                $row['producto_nombre'] ?? '',
                ucfirst($row['tipo'] ?? ''),
                $row['cantidad'] ?? '',
                number_format($row['precio_unitario'] ?? 0, 2),
                number_format(($row['cantidad'] ?? 0) * ($row['precio_unitario'] ?? 0), 2),
                $row['usuario_nombre'] ?? '',
                $row['referencia'] ?? '',
                $row['notas'] ?? ''
            ];
            
            $output .= implode(',', array_map([self::class, 'escapeCSV'], $fields)) . "\n";
        }
        
        return $output;
    }
    
    private static function escapeCSV($field) {
        // Escapar comillas dobles y envolver en comillas si contiene comas, saltos de línea o comillas
        if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
            return '"' . str_replace('"', '""', $field) . '"';
        }
        return $field;
    }
    
    public static function generarXLSXCompleto($data, $filename = 'export.xlsx') {
        // Intentar usar la versión completa primero
        try {
            require_once 'ExcelGenerator.php';
            ExcelGenerator::generarXLSX($data, $filename);
        } catch (Exception $e) {
            // Si falla, usar la versión simple
            self::generarXLSX($data, str_replace('.xlsx', '.csv', $filename));
        }
    }
}
?>
