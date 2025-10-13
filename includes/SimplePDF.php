<?php

class SimplePDF {
    
    public static function generarPDF($data, $filename = 'export.pdf') {
        // Crear un PDF real usando una implementación más simple
        $pdf = new BasicPDF();
        $pdf->generar($data, $filename);
    }
}

class BasicPDF {
    private $content = '';
    private $objects = [];
    private $objectCount = 0;
    
    public function generar($data, $filename) {
        $this->iniciarPDF();
        $this->agregarContenido($data);
        $this->finalizarPDF();
        $this->enviarPDF($filename);
    }
    
    private function iniciarPDF() {
        $this->content = "%PDF-1.4\n";
        $this->content .= "%âãÏÓ\n\n";
        
        // Objeto 1: Catalog
        $this->objects[1] = "<<\n/Type /Catalog\n/Pages 2 0 R\n>>";
        
        // Objeto 2: Pages
        $this->objects[2] = "<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>";
        
        // Objeto 3: Page
        $this->objects[3] = "<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n/Resources <<\n/Font <<\n/F1 4 0 R\n>>\n>>\n/Contents 5 0 R\n>>";
        
        // Objeto 4: Font
        $this->objects[4] = "<<\n/Type /Font\n/Subtype /Type1\n/BaseFont /Helvetica\n>>";
        
        $this->objectCount = 4;
    }
    
    private function agregarContenido($data) {
        $stream = "BT\n";
        $stream .= "/F1 16 Tf\n";
        $stream .= "50 750 Td\n";
        $stream .= "(REPORTE DE MOVIMIENTOS DE INVENTARIO) Tj\n";
        $stream .= "ET\n\n";
        
        $stream .= "BT\n";
        $stream .= "/F1 10 Tf\n";
        $stream .= "50 720 Td\n";
        $stream .= "(Fecha: " . date('d/m/Y H:i:s') . ") Tj\n";
        $stream .= "ET\n\n";
        
        $stream .= "BT\n";
        $stream .= "/F1 10 Tf\n";
        $stream .= "50 700 Td\n";
        $stream .= "(Total de movimientos: " . count($data) . ") Tj\n";
        $stream .= "ET\n\n";
        
        // Línea separadora
        $stream .= "50 680 m\n";
        $stream .= "562 680 l\n";
        $stream .= "S\n\n";
        
        // Encabezados
        $stream .= "BT\n";
        $stream .= "/F1 9 Tf\n";
        $stream .= "50 650 Td\n";
        $stream .= "(Fecha) Tj\n";
        $stream .= "100 0 Td\n";
        $stream .= "(Producto) Tj\n";
        $stream .= "150 0 Td\n";
        $stream .= "(Tipo) Tj\n";
        $stream .= "80 0 Td\n";
        $stream .= "(Cant.) Tj\n";
        $stream .= "60 0 Td\n";
        $stream .= "(Precio) Tj\n";
        $stream .= "80 0 Td\n";
        $stream .= "(Total) Tj\n";
        $stream .= "ET\n\n";
        
        // Línea debajo de encabezados
        $stream .= "50 640 m\n";
        $stream .= "562 640 l\n";
        $stream .= "S\n\n";
        
        // Datos
        $y = 620;
        $stream .= "BT\n";
        $stream .= "/F1 8 Tf\n";
        
        foreach ($data as $row) {
            if ($y < 50) break; // Evitar salirse de la página
            
            $total = $row['cantidad'] * $row['precio_unitario'];
            
            $stream .= "50 " . $y . " Td\n";
            $stream .= "(" . date('d/m/Y', strtotime($row['fecha'])) . ") Tj\n";
            $stream .= "100 0 Td\n";
            $stream .= "(" . substr($row['producto_nombre'], 0, 20) . ") Tj\n";
            $stream .= "150 0 Td\n";
            $stream .= "(" . ucfirst($row['tipo']) . ") Tj\n";
            $stream .= "80 0 Td\n";
            $stream .= "(" . $row['cantidad'] . ") Tj\n";
            $stream .= "60 0 Td\n";
            $stream .= "($" . number_format($row['precio_unitario'], 2) . ") Tj\n";
            $stream .= "80 0 Td\n";
            $stream .= "($" . number_format($total, 2) . ") Tj\n";
            $stream .= "ET\n\n";
            
            $y -= 15;
        }
        
        // Agregar el stream
        $this->objects[5] = "<<\n/Length " . strlen($stream) . "\n>>\nstream\n" . $stream . "\nendstream";
        $this->objectCount = 5;
    }
    
    private function finalizarPDF() {
        // Escribir todos los objetos
        $xrefOffset = strlen($this->content);
        
        foreach ($this->objects as $num => $obj) {
            $this->content .= $num . " 0 obj\n";
            $this->content .= $obj . "\n";
            $this->content .= "endobj\n\n";
        }
        
        // Xref table
        $this->content .= "xref\n";
        $this->content .= "0 " . ($this->objectCount + 1) . "\n";
        $this->content .= "0000000000 65535 f \n";
        
        for ($i = 1; $i <= $this->objectCount; $i++) {
            $this->content .= sprintf("%010d 00000 n \n", $xrefOffset);
            $xrefOffset += strlen($i . " 0 obj\n" . $this->objects[$i] . "\nendobj\n\n");
        }
        
        // Trailer
        $this->content .= "trailer\n";
        $this->content .= "<<\n";
        $this->content .= "/Size " . ($this->objectCount + 1) . "\n";
        $this->content .= "/Root 1 0 R\n";
        $this->content .= ">>\n";
        $this->content .= "startxref\n";
        $this->content .= strlen($this->content) . "\n";
        $this->content .= "%%EOF\n";
    }
    
    private function enviarPDF($filename) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($this->content));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $this->content;
        exit;
    }
}
?>
