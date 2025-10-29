<?php

class PDFGenerator {
    
    public static function generarPDF($data, $filename = 'export.pdf') {
        // Usar la implementación simple de PDF
        require_once 'includes/SimplePDF.php';
        SimplePDF::generarPDF($data, $filename);
    }
}
?>