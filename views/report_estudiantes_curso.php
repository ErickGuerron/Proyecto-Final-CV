<?php
require_once '../models/database.php';
require_once '../assets/fpdf186/fpdf.php';

class PDF extends FPDF {
    private $totalEstudiantes = 0;
    private $totalCursos = 0;
    
    function Header() {
        // Fondo del header con degradado simulado
        $this->SetFillColor(26, 35, 126);
        $this->Rect(0, 0, 297, 50, 'F');
        
        $this->SetFillColor(30, 55, 153);
        $this->Rect(0, 0, 297, 42, 'F');
        
        // Elementos decorativos
        $this->SetFillColor(63, 81, 181);
        $this->Circle(270, 18, 30);
        $this->Circle(285, 35, 20);
        
        // Logo
        $this->SetFillColor(255, 255, 255);
        $this->Circle(25, 20, 12, 'F');
        $this->SetTextColor(26, 35, 126);
        $this->SetFont('Arial', 'B', 18);
        $this->SetXY(17, 14);
        $this->Cell(16, 12, 'EC', 0, 0, 'C');
        
        // Título principal
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 22);
        $this->SetXY(45, 10);
        $this->Cell(0, 8, utf8_decode('REPORTE DE ESTUDIANTES POR CURSO'), 0, 1);
        
        $this->SetFont('Arial', '', 11);
        $this->SetXY(45, 22);
        $this->Cell(0, 6, utf8_decode('Listado completo de inscripciones académicas'), 0, 1);
        
        // Línea decorativa
        $this->SetDrawColor(255, 193, 7);
        $this->SetLineWidth(1.2);
        $this->Line(45, 32, 160, 32);
        
        $this->Ln(20);
    }
    
    function Footer() {
        $this->SetY(-20);
        
        // Línea superior
        $this->SetDrawColor(63, 81, 181);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 287, $this->GetY());
        $this->Ln(3);
        
        // Información del footer
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        
        $this->Cell(135, 4, utf8_decode('Sistema de Gestión Académica © 2024'), 0, 0, 'L');
        $this->Cell(135, 4, utf8_decode('Generado: ') . date('d/m/Y H:i:s'), 0, 0, 'R');
        $this->Ln(4);
        
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 4, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }
    
    function Circle($x, $y, $r, $style='D') {
        $this->Ellipse($x, $y, $r, $r, $style);
    }
    
    function Ellipse($x, $y, $rx, $ry, $style='D') {
        $lx = 4/3*(M_SQRT2-1)*$rx;
        $ly = 4/3*(M_SQRT2-1)*$ry;
        $k = $this->k;
        $h = $this->h;
        
        $this->_out(sprintf('%.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x+$rx)*$k, ($h-$y)*$k,
            ($x+$rx)*$k, ($h-($y-$ly))*$k,
            ($x+$lx)*$k, ($h-($y-$ry))*$k,
            $x*$k, ($h-($y-$ry))*$k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x-$lx)*$k, ($h-($y-$ry))*$k,
            ($x-$rx)*$k, ($h-($y-$ly))*$k,
            ($x-$rx)*$k, ($h-$y)*$k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x-$rx)*$k, ($h-($y+$ly))*$k,
            ($x-$lx)*$k, ($h-($y+$ry))*$k,
            $x*$k, ($h-($y+$ry))*$k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c %s',
            ($x+$lx)*$k, ($h-($y+$ry))*$k,
            ($x+$rx)*$k, ($h-($y+$ly))*$k,
            ($x+$rx)*$k, ($h-$y)*$k,
            $style=='F' ? 'f' : 's'));
    }
    
    function TableHeader() {
        // Fondo del header de tabla
        $this->SetFillColor(63, 81, 181);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(63, 81, 181);
        $this->SetLineWidth(0.3);
        $this->SetFont('Arial', 'B', 10);
        
        // Encabezados
        $this->Cell(12, 10, 'ID', 1, 0, 'C', true);
        $this->Cell(70, 10, utf8_decode('Curso'), 1, 0, 'L', true);
        $this->Cell(70, 10, utf8_decode('Estudiante'), 1, 0, 'L', true);
        $this->Cell(70, 10, utf8_decode('Correo Electrónico'), 1, 0, 'L', true);
        $this->Cell(55, 10, utf8_decode('Teléfono'), 1, 0, 'C', true);
        $this->Ln();
    }
    
    function TableRow($data, $index) {
        // Colores alternados
        if ($index % 2 == 0) {
            $this->SetFillColor(250, 250, 255);
        } else {
            $this->SetFillColor(255, 255, 255);
        }
        
        $this->SetTextColor(50, 50, 50);
        $this->SetDrawColor(220, 220, 230);
        $this->SetFont('Arial', '', 9);
        
        // ID
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(63, 81, 181);
        $this->Cell(12, 9, str_pad($data['ID_EST'], 3, '0', STR_PAD_LEFT), 1, 0, 'C', true);
        
        // Curso
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(26, 35, 126);
        $this->Cell(70, 9, utf8_decode($data['CURSO']), 1, 0, 'L', true);
        
        // Estudiante
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(70, 9, utf8_decode($data['ESTUDIANTE']), 1, 0, 'L', true);
        
        // Email
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(70, 70, 70);
        $this->Cell(70, 9, $data['COR_EST'], 1, 0, 'L', true);
        
        // Teléfono
        $this->SetFont('Arial', '', 9);
        $this->Cell(55, 9, $data['TEL_EST'] ?: 'No registrado', 1, 0, 'C', true);
        
        $this->Ln();
    }
    
    function StatsCard($total_estudiantes, $total_cursos) {
        $this->Ln(8);
        
        // Card de estadísticas
        $this->SetFillColor(248, 249, 250);
        $this->Rect(10, $this->GetY(), 277, 30, 'F');
        
        $this->SetDrawColor(63, 81, 181);
        $this->SetLineWidth(0.5);
        $this->Rect(10, $this->GetY(), 277, 30, 'D');
        
        $y_start = $this->GetY();
        
        // Título de estadísticas
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(63, 81, 181);
        $this->SetXY(15, $y_start + 5);
        $this->Cell(0, 6, utf8_decode('📊 RESUMEN ESTADÍSTICO'), 0, 1);
        
        // Línea divisoria
        $this->SetDrawColor(220, 220, 230);
        $this->Line(15, $y_start + 13, 282, $y_start + 13);
        
        // Estadística 1: Total de estudiantes
        $this->SetXY(40, $y_start + 16);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(76, 175, 80);
        $this->Cell(60, 8, $total_estudiantes, 0, 0, 'C');
        
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->SetXY(40, $y_start + 24);
        $this->Cell(60, 4, utf8_decode('Total Estudiantes'), 0, 0, 'C');
        
        // Línea vertical divisoria
        $this->SetDrawColor(220, 220, 230);
        $this->Line(138.5, $y_start + 16, 138.5, $y_start + 28);
        
        // Estadística 2: Total de cursos
        $this->SetXY(117, $y_start + 16);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(33, 150, 243);
        $this->Cell(60, 8, $total_cursos, 0, 0, 'C');
        
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->SetXY(117, $y_start + 24);
        $this->Cell(60, 4, utf8_decode('Cursos Activos'), 0, 0, 'C');
        
        // Línea vertical divisoria
        $this->Line(215.5, $y_start + 16, 215.5, $y_start + 28);
        
        // Estadística 3: Promedio
        $promedio = $total_cursos > 0 ? number_format($total_estudiantes / $total_cursos, 1) : 0;
        $this->SetXY(194, $y_start + 16);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(255, 152, 0);
        $this->Cell(60, 8, $promedio, 0, 0, 'C');
        
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->SetXY(194, $y_start + 24);
        $this->Cell(60, 4, utf8_decode('Promedio por Curso'), 0, 0, 'C');
        
        $this->SetY($y_start + 35);
    }
    
    function InfoBanner() {
        $this->Ln(3);
        
        // Banner informativo
        $this->SetFillColor(232, 245, 233);
        $this->Rect(10, $this->GetY(), 277, 12, 'F');
        
        $this->SetDrawColor(76, 175, 80);
        $this->SetLineWidth(0.3);
        $this->Rect(10, $this->GetY(), 277, 12, 'D');
        
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(56, 142, 60);
        $this->SetXY(15, $this->GetY() + 3);
        $this->Cell(0, 6, utf8_decode('Los datos mostrados corresponden a todas las inscripciones activas en el sistema'), 0, 1, 'L');
        
        $this->Ln(5);
    }
    
    function setTotalEstudiantes($total) {
        $this->totalEstudiantes = $total;
    }
    
    function setTotalCursos($total) {
        $this->totalCursos = $total;
    }
}

$db = Database::getInstance()->getConnection();

$sql = "
SELECT 
    C.NOM_CUR AS CURSO,
    E.ID_EST,
    CONCAT(E.NOM_EST, ' ', E.APE_EST) AS ESTUDIANTE,
    E.COR_EST,
    E.TEL_EST
FROM INSCRIPCIONES I
INNER JOIN ESTUDIANTES E ON I.ID_EST_INS = E.ID_EST
INNER JOIN CURSOS C ON I.ID_CUR_INS = C.ID_CUR
ORDER BY C.NOM_CUR ASC, E.APE_EST ASC;
";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular estadísticas
$total_estudiantes = count($rows);
$cursos_unicos = array_unique(array_column($rows, 'CURSO'));
$total_cursos = count($cursos_unicos);

// Crear PDF en orientación horizontal
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->setTotalEstudiantes($total_estudiantes);
$pdf->setTotalCursos($total_cursos);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 25);
$pdf->AddPage();

// Banner informativo
$pdf->InfoBanner();

// Encabezado de la tabla
$pdf->TableHeader();

// Datos de la tabla
$index = 0;
foreach ($rows as $row) {
    $pdf->TableRow($row, $index);
    $index++;
}

// Card de estadísticas al final
$pdf->StatsCard($total_estudiantes, $total_cursos);

$pdf->Output();
?>