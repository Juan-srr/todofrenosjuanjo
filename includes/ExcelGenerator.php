<?php

class ExcelGenerator {
    
    public static function generarXLSX($data, $filename = 'export.xlsx') {
        // Crear un archivo Excel real usando la estructura XLSX
        $zip = new ZipArchive();
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        
        if ($zip->open($tempFile, ZipArchive::CREATE) !== TRUE) {
            throw new Exception("No se pudo crear el archivo Excel");
        }
        
        // Crear estructura básica de XLSX
        self::crearEstructuraXLSX($zip, $data);
        
        $zip->close();
        
        // Enviar archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
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
        $stringIndex = 0;
        $stringMap = [];
        
        // Procesar encabezados
        $headers = ['Fecha', 'Producto', 'Tipo', 'Cantidad', 'Precio Unitario', 'Total', 'Usuario', 'Referencia', 'Notas'];
        foreach ($headers as $header) {
            $strings[] = '<si><t>' . htmlspecialchars($header) . '</t></si>';
            $stringMap[$header] = $stringIndex++;
        }
        
        // Procesar datos
        foreach ($data as $row) {
            $fields = [
                $row['fecha'],
                $row['producto_nombre'],
                ucfirst($row['tipo']),
                $row['cantidad'],
                '$' . number_format($row['precio_unitario'], 2),
                '$' . number_format($row['cantidad'] * $row['precio_unitario'], 2),
                $row['usuario_nombre'],
                $row['referencia'],
                $row['notas']
            ];
            
            foreach ($fields as $field) {
                $strings[] = '<si><t>' . htmlspecialchars($field) . '</t></si>';
                $stringMap[$field] = $stringIndex++;
            }
        }
        
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">';
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
        foreach ($data as $row) {
            $xml .= '<row r="' . $rowNum . '">';
            $fields = [
                $row['fecha'],
                $row['producto_nombre'],
                ucfirst($row['tipo']),
                $row['cantidad'],
                '$' . number_format($row['precio_unitario'], 2),
                '$' . number_format($row['cantidad'] * $row['precio_unitario'], 2),
                $row['usuario_nombre'],
                $row['referencia'],
                $row['notas']
            ];
            
            for ($i = 0; $i < count($fields); $i++) {
                $col = self::numToCol($i + 1);
                $xml .= '<c r="' . $col . $rowNum . '" t="s"><v>' . ($i + count($headers)) . '</v></c>';
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
