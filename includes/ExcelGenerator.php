<?php

class ExcelGenerator {
    
    public static function generarXLSX($data, $filename = 'export.xlsx') {
        // Validar datos
        if (empty($data) || !is_array($data)) {
            throw new Exception("No hay datos para exportar");
        }
        
        // Limpiar todos los buffers de salida
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Verificar que ZipArchive esté disponible
        if (!class_exists('ZipArchive')) {
            throw new Exception("La extensión ZipArchive no está disponible");
        }
        
        // Crear un archivo Excel real usando la estructura XLSX
        $zip = new ZipArchive();
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        
        // Eliminar el archivo temporal vacío y crear uno nuevo
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
        
        $result = $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($result !== TRUE) {
            $errorMessages = [
                ZipArchive::ER_OK => 'Sin errores',
                ZipArchive::ER_MULTIDISK => 'Error de disco múltiple',
                ZipArchive::ER_RENAME => 'Error de renombrado',
                ZipArchive::ER_CLOSE => 'Error al cerrar',
                ZipArchive::ER_SEEK => 'Error de búsqueda',
                ZipArchive::ER_READ => 'Error de lectura',
                ZipArchive::ER_WRITE => 'Error de escritura',
                ZipArchive::ER_CRC => 'Error de CRC',
                ZipArchive::ER_ZIPCLOSED => 'Archivo ZIP cerrado',
                ZipArchive::ER_NOENT => 'No existe el archivo',
                ZipArchive::ER_EXISTS => 'El archivo ya existe',
                ZipArchive::ER_OPEN => 'Error al abrir',
                ZipArchive::ER_TMPOPEN => 'Error al abrir archivo temporal',
                ZipArchive::ER_ZLIB => 'Error de Zlib',
                ZipArchive::ER_MEMORY => 'Error de memoria',
                ZipArchive::ER_CHANGED => 'Entrada modificada',
                ZipArchive::ER_COMPNOTSUPP => 'Compresión no soportada',
                ZipArchive::ER_EOF => 'Fin de archivo prematuro',
                ZipArchive::ER_INVAL => 'Argumento inválido',
                ZipArchive::ER_NOZIP => 'No es un archivo ZIP',
                ZipArchive::ER_INTERNAL => 'Error interno',
                ZipArchive::ER_INCONS => 'Inconsistencia en el archivo ZIP',
                ZipArchive::ER_REMOVE => 'No se puede eliminar el archivo',
                ZipArchive::ER_DELETED => 'Entrada eliminada'
            ];
            
            $errorMsg = isset($errorMessages[$result]) ? $errorMessages[$result] : "Error desconocido ($result)";
            throw new Exception("No se pudo crear el archivo Excel: " . $errorMsg);
        }
        
        // Crear estructura básica de XLSX
        self::crearEstructuraXLSX($zip, $data);
        
        $zip->close();
        
        // Verificar que el archivo se creó correctamente
        if (!file_exists($tempFile) || filesize($tempFile) == 0) {
            unlink($tempFile);
            throw new Exception("El archivo Excel no se generó correctamente");
        }
        
        // Asegurar que no hay output antes de enviar headers
        if (ob_get_level()) {
            ob_clean();
        }
        
        // Enviar headers correctos
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tempFile));
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        
        // Enviar archivo
        readfile($tempFile);
        unlink($tempFile);
        exit;
    }
    
    private static function crearEstructuraXLSX($zip, $data) {
        // [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);
        
        // _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);
        
        // xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Movimientos" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);
        
        // xl/_rels/workbook.xml.rels
        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        
        // xl/sharedStrings.xml
        $sharedStrings = self::generarSharedStrings($data);
        $zip->addFromString('xl/sharedStrings.xml', $sharedStrings);
        
        // xl/worksheets/sheet1.xml
        $worksheet = self::generarWorksheet($data);
        $zip->addFromString('xl/worksheets/sheet1.xml', $worksheet);
    }
    
    private static function generarSharedStrings($data) {
        $strings = [];
        $stringMap = [];
        
        // Procesar encabezados
        $headers = ['Fecha', 'Producto', 'Tipo', 'Cantidad', 'Precio Unitario', 'Total', 'Usuario', 'Referencia', 'Notas'];
        foreach ($headers as $header) {
            $strings[] = '<si><t>' . htmlspecialchars($header, ENT_XML1, 'UTF-8') . '</t></si>';
            $stringMap[$header] = count($strings) - 1;
        }
        
        // Procesar datos
        foreach ($data as $row) {
            $fields = [
                $row['fecha'] ?? '',
                $row['producto_nombre'] ?? '',
                ucfirst($row['tipo'] ?? ''),
                (string)($row['cantidad'] ?? ''),
                '$' . number_format($row['precio_unitario'] ?? 0, 2),
                '$' . number_format(($row['cantidad'] ?? 0) * ($row['precio_unitario'] ?? 0), 2),
                $row['usuario_nombre'] ?? '',
                $row['referencia'] ?? '',
                $row['notas'] ?? ''
            ];
            
            foreach ($fields as $field) {
                $strings[] = '<si><t>' . htmlspecialchars($field, ENT_XML1, 'UTF-8') . '</t></si>';
                $stringMap[$field] = count($strings) - 1;
            }
        }
        
        $totalCount = count($strings);
        $uniqueCount = count(array_unique($strings));
        
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . $totalCount . '" uniqueCount="' . $uniqueCount . '">';
        $xml .= implode('', $strings);
        $xml .= '</sst>';
        
        return $xml;
    }
    
    private static function generarWorksheet($data) {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheetData>';
        
        // Encabezados (fila 1)
        $xml .= '<row r="1">';
        $headers = ['Fecha', 'Producto', 'Tipo', 'Cantidad', 'Precio Unitario', 'Total', 'Usuario', 'Referencia', 'Notas'];
        for ($i = 0; $i < count($headers); $i++) {
            $col = self::numToCol($i + 1);
            $xml .= '<c r="' . $col . '1" t="s"><v>' . $i . '</v></c>';
        }
        $xml .= '</row>';
        
        // Datos
        $rowNum = 2;
        $stringIndex = count($headers); // Empezar después de los encabezados
        
        foreach ($data as $row) {
            $xml .= '<row r="' . $rowNum . '">';
            $fields = [
                $row['fecha'] ?? '',
                $row['producto_nombre'] ?? '',
                ucfirst($row['tipo'] ?? ''),
                (string)($row['cantidad'] ?? ''),
                '$' . number_format($row['precio_unitario'] ?? 0, 2),
                '$' . number_format(($row['cantidad'] ?? 0) * ($row['precio_unitario'] ?? 0), 2),
                $row['usuario_nombre'] ?? '',
                $row['referencia'] ?? '',
                $row['notas'] ?? ''
            ];
            
            for ($i = 0; $i < count($fields); $i++) {
                $col = self::numToCol($i + 1);
                $xml .= '<c r="' . $col . $rowNum . '" t="s"><v>' . $stringIndex . '</v></c>';
                $stringIndex++;
            }
            $xml .= '</row>';
            $rowNum++;
        }
        
        $xml .= '</sheetData></worksheet>';
        return $xml;
    }
    
    private static function numToCol($num) {
        $col = '';
        while ($num > 0) {
            $mod = ($num - 1) % 26;
            $col = chr(65 + $mod) . $col;
            $num = intval(($num - $mod) / 26);
        }
        return $col;
    }
}
?>
