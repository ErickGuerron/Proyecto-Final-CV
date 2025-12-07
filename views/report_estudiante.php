<?php
require_once '../models/database.php';
require_once '../assets/fpdf186/fpdf.php';

class PDF extends FPDF {
    function Header() {
        // Degradado simulado con rectángulos
        $this->SetFillColor(26, 35, 126);
        $this->Rect(0, 0, 210, 60, 'F');
        
        $this->SetFillColor(30, 55, 153);
        $this->Rect(0, 0, 210, 50, 'F');
        
        // Diseño geométrico decorativo
        $this->SetFillColor(63, 81, 181);
        $this->SetAlpha(0.3);
        $this->Circle(180, 20, 35);
        $this->Circle(200, 40, 25);
        $this->SetAlpha(1);
        
        $this->SetFillColor(255, 255, 255);
        $this->Circle(35, 25, 15, 'F');
        $this->SetTextColor(26, 35, 126);
        $this->SetFont('Arial', 'B', 24);
        $this->SetXY(25, 17);
        $this->Cell(20, 16, 'E', 0, 0, 'C');
        
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 24);
        $this->SetXY(60, 15);
        $this->Cell(0, 8, utf8_decode('REPORTE ACADÉMICO'), 0, 1);
        
        $this->SetFont('Arial', '', 12);
        $this->SetXY(60, 26);
        $this->Cell(0, 6, utf8_decode('Información Detallada del Estudiante'), 0, 1);
        
        $this->SetDrawColor(255, 193, 7);
        $this->SetLineWidth(1.5);
        $this->Line(60, 35, 140, 35);
        
        $this->Ln(25);
    }
    
    function Footer() {
        $this->SetY(-25);
        
        // Línea superior
        $this->SetDrawColor(63, 81, 181);
        $this->SetLineWidth(0.5);
        $this->Line(20, $this->GetY(), 190, $this->GetY());
        $this->Ln(3);
        
        // Información del footer
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(85, 4, utf8_decode('Sistema de Gestión Académica 2024'), 0, 0, 'L');
        $this->Cell(85, 4, utf8_decode('Generado: ') . date('d/m/Y H:i'), 0, 0, 'R');
        $this->Ln(4);
        
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 4, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
    
    function SectionTitle($title, $icon = '') {
        $this->Ln(5);
        
        // Caja de título con gradiente
        $this->SetFillColor(63, 81, 181);
        $this->Rect($this->GetX(), $this->GetY(), 170, 12, 'F');
        
        $this->SetFillColor(83, 109, 254);
        $this->Rect($this->GetX(), $this->GetY(), 170, 10, 'F');
        
        // Texto del título
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(170, 10, $icon . '  ' . utf8_decode($title), 0, 1, 'L');
        $this->Ln(3);
    }
    
    function InfoRow($label, $value, $last = false) {
        $y = $this->GetY();
        
        // Fondo alternado
        if ($this->GetY() % 20 > 10) {
            $this->SetFillColor(250, 250, 255);
        } else {
            $this->SetFillColor(255, 255, 255);
        }
        
        $this->Rect(20, $y, 170, 11, 'F');
        
        // Etiqueta
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(63, 81, 181);
        $this->SetXY(25, $y + 2);
        $this->Cell(55, 7, utf8_decode($label), 0, 0, 'L');
        
        // Valor
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(50, 50, 50);
        $this->SetX(82);
        $this->MultiCell(103, 7, utf8_decode($value), 0, 'L');
        
        // Línea divisoria sutil
        if (!$last) {
            $this->SetDrawColor(220, 220, 230);
            $this->SetLineWidth(0.2);
            $this->Line(25, $this->GetY(), 185, $this->GetY());
        }
    }
    
    function StatusBadge($status, $color) {
        $x = $this->GetX();
        $y = $this->GetY();
        
        // Fondo del badge
        $this->SetFillColor($color[0], $color[1], $color[2]);
        $this->RoundedRect($x, $y, 45, 8, 2, 'F');
        
        // Texto del badge
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 9);
        $this->SetXY($x, $y + 1);
        $this->Cell(45, 6, utf8_decode($status), 0, 0, 'C');
    }
    
    function RoundedRect($x, $y, $w, $h, $r, $style = '') {
        $k = $this->k;
        $hp = $this->h;
        
        $this->_out(sprintf('%.2F %.2F m', ($x+$r)*$k, ($hp-$y)*$k));
        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k, ($hp-$y)*$k));
        $this->_Arc($xc+$r*0.4, $yc-$r*0.4, $r*0.4, 0, 90);
        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k, ($hp-$yc)*$k));
        $this->_Arc($xc+$r*0.4, $yc+$r*0.4, $r*0.4, 270, 360);
        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k, ($hp-($y+$h))*$k));
        $this->_Arc($xc-$r*0.4, $yc+$r*0.4, $r*0.4, 180, 270);
        $xc = $x+$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $x*$k, ($hp-$yc)*$k));
        $this->_Arc($xc-$r*0.4, $yc-$r*0.4, $r*0.4, 90, 180);
        $this->_out($style == 'F' ? 'f' : 'S');
    }
    
    function _Arc($x1, $y1, $r, $a, $b) {
        $d = $b - $a;
        $x = $x1 + $r * cos(deg2rad($a));
        $y = $y1 + $r * sin(deg2rad($a));
        $this->_out(sprintf('%.2F %.2F l', $x*$this->k, ($this->h-$y)*$this->k));
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
    
    function SetAlpha($alpha) {
        // Esta función simula transparencia (FPDF no lo soporta nativamente)
    }
}

$db = Database::getInstance()->getConnection();

$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) {
    die("Falta parámetro: id");
}

$sql = "
SELECT 
    E.*,
    C.NOM_CUR,
    I.FEC_INS
FROM ESTUDIANTES E
LEFT JOIN INSCRIPCIONES I ON E.ID_EST = I.ID_EST_INS
LEFT JOIN CURSOS C ON I.ID_CUR_INS = C.ID_CUR
WHERE E.ID_EST = :id
";

$stmt = $db->prepare($sql);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Estudiante no encontrado");
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(true, 25);


$pdf->SetFillColor(255, 255, 255);
$pdf->RoundedRect(20, $pdf->GetY(), 170, 35, 3, 'F');

$pdf->SetDrawColor(63, 81, 181);
$pdf->SetLineWidth(0.5);
$pdf->RoundedRect(20, $pdf->GetY(), 170, 35, 3, 'D');

$y_start = $pdf->GetY();

$pdf->SetXY(25, $y_start + 5);
$pdf->StatusBadge('ID: ' . str_pad($data['ID_EST'], 4, '0', STR_PAD_LEFT), [76, 175, 80]);

$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(26, 35, 126);
$pdf->SetXY(25, $y_start + 15);
$pdf->Cell(0, 8, utf8_decode($data['NOM_EST'] . ' ' . $data['APE_EST']), 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->SetX(25);
$pdf->Cell(0, 5, utf8_decode('✉ ' . $data['COR_EST']), 0, 1);

$pdf->SetY($y_start + 35 + 8);


$pdf->SectionTitle('DATOS DE CONTACTO', '📞');

$pdf->InfoRow('Correo Electrónico:', $data['COR_EST']);
$pdf->InfoRow('Número Telefónico:', $data['TEL_EST'] ?: 'No registrado', true);

$pdf->SectionTitle('INFORMACIÓN ACADÉMICA', '🎓');

$cursoNombre = $data['NOM_CUR'] ?: 'Sin curso asignado';
$pdf->InfoRow('Curso Inscrito:', $cursoNombre);

$fechaIns = $data['FEC_INS'] ? date('d/m/Y', strtotime($data['FEC_INS'])) : 'No disponible';
$pdf->InfoRow('Fecha de Inscripción:', $fechaIns);

if ($data['FEC_INS']) {
    $fecha_ins = new DateTime($data['FEC_INS']);
    $hoy = new DateTime();
    $diferencia = $hoy->diff($fecha_ins);
    $dias = $diferencia->days;
    $pdf->InfoRow('Tiempo Inscrito:', $dias . ' días', true);
} else {
    $pdf->InfoRow('Tiempo Inscrito:', 'N/A', true);
}

$pdf->Ln(8);
$pdf->SetFillColor(248, 249, 250);
$pdf->Rect(20, $pdf->GetY(), 170, 25, 'F');

$pdf->SetDrawColor(220, 220, 230);
$pdf->Rect(20, $pdf->GetY(), 170, 25, 'D');

$y_stats = $pdf->GetY();

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->SetXY(30, $y_stats + 5);
$pdf->Cell(0, 5, utf8_decode('RESUMEN ESTADÍSTICO'), 0, 1);

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(63, 81, 181);
$pdf->SetXY(30, $y_stats + 12);
$pdf->Cell(50, 8, '1', 0, 0, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->SetXY(30, $y_stats + 19);
$pdf->Cell(50, 4, 'Curso Activo', 0, 0, 'C');

$pdf->Output();
?>