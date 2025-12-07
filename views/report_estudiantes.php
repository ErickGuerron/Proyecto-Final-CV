<?php
declare(strict_types=1);

// Muy importante: NADA de HTML o espacios antes de esta línea
ob_start();

require_once '../models/database.php';
require_once '../assets/fpdf186/fpdf.php';

class PDF extends FPDF
{
    /**
     * Normaliza texto UTF-8 a ISO-8859-1 para FPDF evitando utf8_decode().
     */
    public function enc(?string $txt): string
    {
        if ($txt === null) {
            return '';
        }

        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($txt, 'ISO-8859-1', 'UTF-8');
        }

        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $txt);
    }

    function Header()
    {
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
        $this->Cell(0, 8, $this->enc('REPORTE ACADÉMICO'), 0, 1);

        $this->SetFont('Arial', '', 12);
        $this->SetXY(60, 26);
        $this->Cell(0, 6, $this->enc('Información detallada de estudiantes registrados'), 0, 1);

        $this->SetDrawColor(255, 193, 7);
        $this->SetLineWidth(1.5);
        $this->Line(60, 35, 140, 35);

        $this->Ln(25);
    }

    function Footer()
    {
        $this->SetY(-25);

        // Línea superior
        $this->SetDrawColor(63, 81, 181);
        $this->SetLineWidth(0.5);
        $this->Line(20, $this->GetY(), 190, $this->GetY());
        $this->Ln(3);

        // Información del footer
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(85, 4, $this->enc('Sistema de Gestión Académica 2024'), 0, 0, 'L');
        $this->Cell(85, 4, $this->enc('Generado: ') . date('d/m/Y H:i'), 0, 0, 'R');
        $this->Ln(4);

        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 4, $this->enc('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    function SectionTitle(string $title, string $icon = ''): void
    {
        $this->Ln(5);

        // Caja de título con gradiente
        $this->SetFillColor(63, 81, 181);
        $this->Rect($this->GetX(), $this->GetY(), 170, 12, 'F');

        $this->SetFillColor(83, 109, 254);
        $this->Rect($this->GetX(), $this->GetY(), 170, 10, 'F');

        // Texto del título
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(170, 10, $this->enc($icon . '  ' . $title), 0, 1, 'L');
        $this->Ln(3);
    }

    function InfoRow(string $label, string $value, bool $last = false): void
    {
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
        $this->Cell(55, 7, $this->enc($label), 0, 0, 'L');

        // Valor
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(50, 50, 50);
        $this->SetX(82);
        $this->MultiCell(103, 7, $this->enc($value), 0, 'L');

        // Línea divisoria sutil
        if (!$last) {
            $this->SetDrawColor(220, 220, 230);
            $this->SetLineWidth(0.2);
            $this->Line(25, $this->GetY(), 185, $this->GetY());
        }
    }

    function StatusBadge(string $status, array $color): void
    {
        $x = $this->GetX();
        $y = $this->GetY();

        // Fondo del badge
        $this->SetFillColor($color[0], $color[1], $color[2]);
        $this->RoundedRect($x, $y, 45, 8, 2, 'F');

        // Texto del badge
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 9);
        $this->SetXY($x, $y + 1);
        $this->Cell(45, 6, $this->enc($status), 0, 0, 'C');
    }

    function RoundedRect($x, $y, $w, $h, $r, $style = ''): void
    {
        $k = $this->k;
        $hp = $this->h;

        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        $this->_Arc($xc + $r * 0.4, $yc - $r * 0.4, $r * 0.4, 0, 90);
        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $r * 0.4, $yc + $r * 0.4, $r * 0.4, 270, 360);
        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $r * 0.4, $yc + $r * 0.4, $r * 0.4, 180, 270);
        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $x * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $r * 0.4, $yc - $r * 0.4, $r * 0.4, 90, 180);
        $this->_out($style == 'F' ? 'f' : 'S');
    }

    function _Arc($x1, $y1, $r, $a, $b): void
    {
        $x = $x1 + $r * cos(deg2rad($a));
        $y = $y1 + $r * sin(deg2rad($a));
        $this->_out(sprintf('%.2F %.2F l', $x * $this->k, ($this->h - $y) * $this->k));
    }

    function Circle($x, $y, $r, $style = 'D'): void
    {
        $this->Ellipse($x, $y, $r, $r, $style);
    }

    function Ellipse($x, $y, $rx, $ry, $style = 'D'): void
    {
        $lx = 4 / 3 * (M_SQRT2 - 1) * $rx;
        $ly = 4 / 3 * (M_SQRT2 - 1) * $ry;
        $k  = $this->k;
        $h  = $this->h;

        $this->_out(sprintf(
            '%.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x + $rx) * $k, ($h - $y) * $k,
            ($x + $rx) * $k, ($h - ($y - $ly)) * $k,
            ($x + $lx) * $k, ($h - ($y - $ry)) * $k,
            $x * $k,       ($h - ($y - $ry)) * $k
        ));
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x - $lx) * $k, ($h - ($y - $ry)) * $k,
            ($x - $rx) * $k, ($h - ($y - $ly)) * $k,
            ($x - $rx) * $k, ($h - $y) * $k
        ));
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x - $rx) * $k, ($h - ($y + $ly)) * $k,
            ($x - $lx) * $k, ($h - ($y + $ry)) * $k,
            $x * $k,         ($h - ($y + $ry)) * $k
        ));
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c %s',
            ($x + $lx) * $k, ($h - ($y + $ry)) * $k,
            ($x + $rx) * $k, ($h - ($y + $ly)) * $k,
            ($x + $rx) * $k, ($h - $y) * $k,
            $style == 'F' ? 'f' : 's'
        ));
    }

    function SetAlpha($alpha): void
    {
        // Simulación de transparencia (sin efecto real en FPDF estándar)
    }
}

// =====================
//  CONSULTA DE DATOS
// =====================
$db = Database::getInstance()->getConnection();

$sql = "
    SELECT 
        E.*,
        C.NOM_CUR,
        I.FEC_INS
    FROM ESTUDIANTES E
    LEFT JOIN INSCRIPCIONES I ON E.ID_EST = I.ID_EST_INS
    LEFT JOIN CURSOS C        ON I.ID_CUR_INS = C.ID_CUR
    ORDER BY E.FEC_CRE ASC, E.APE_EST ASC, E.NOM_EST ASC
";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    // Evitar romper el PDF: puedes manejarlo con una página vacía
    $rows = [];
}

$pdf = new PDF();
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(true, 25);
$pdf->AddPage();

foreach ($rows as $index => $data) {

    if ($pdf->GetY() > 210) {
        $pdf->AddPage();
    }

    if ($index > 0) {
        $pdf->Ln(8);
    }

    $y_start = $pdf->GetY();

    // Tarjeta principal
    $pdf->SetFillColor(255, 255, 255);
    $pdf->RoundedRect(20, $y_start, 170, 35, 3, 'F');

    $pdf->SetDrawColor(63, 81, 181);
    $pdf->SetLineWidth(0.5);
    $pdf->RoundedRect(20, $y_start, 170, 35, 3, 'D');

    // Badge ID
    $pdf->SetXY(25, $y_start + 5);
    $pdf->StatusBadge('ID: ' . str_pad((string)$data['ID_EST'], 4, '0', STR_PAD_LEFT), [76, 175, 80]);

    // Nombre
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(26, 35, 126);
    $pdf->SetXY(25, $y_start + 15);
    $pdf->Cell(0, 8, $pdf->enc($data['NOM_EST'] . ' ' . $data['APE_EST']), 0, 1);

    // Correo
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->SetX(25);
    $pdf->Cell(0, 5, $pdf->enc('✉ ' . $data['COR_EST']), 0, 1);

    $pdf->SetY($y_start + 35 + 8);

    // DATOS DE CONTACTO
    $pdf->SectionTitle('DATOS DE CONTACTO', '📞');
    $pdf->InfoRow('Correo Electrónico:', (string)$data['COR_EST']);
    $pdf->InfoRow('Número Telefónico:', $data['TEL_EST'] ?: 'No registrado', true);

    // INFORMACIÓN ACADÉMICA
    $pdf->SectionTitle('INFORMACIÓN ACADÉMICA', '🎓');

    $cursoNombre = $data['NOM_CUR'] ?: 'Sin curso asignado';
    $pdf->InfoRow('Curso Inscrito:', $cursoNombre);

    $fechaIns = $data['FEC_INS'] ? date('d/m/Y', strtotime($data['FEC_INS'])) : 'No disponible';
    $pdf->InfoRow('Fecha de Inscripción:', $fechaIns);

    if ($data['FEC_INS']) {
        $fecha_ins  = new DateTime($data['FEC_INS']);
        $hoy        = new DateTime();
        $diferencia = $hoy->diff($fecha_ins);
        $dias       = $diferencia->days;
        $pdf->InfoRow('Tiempo Inscrito:', $dias . ' días', true);
    } else {
        $pdf->InfoRow('Tiempo Inscrito:', 'N/A', true);
    }

    // Resumen
    $pdf->Ln(5);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->Rect(20, $pdf->GetY(), 170, 18, 'F');

    $pdf->SetDrawColor(220, 220, 230);
    $pdf->Rect(20, $pdf->GetY(), 170, 18, 'D');

    $y_stats = $pdf->GetY();

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->SetXY(25, $y_stats + 4);
    $pdf->Cell(0, 5, $pdf->enc('RESUMEN DEL ESTUDIANTE'), 0, 1);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(25, $y_stats + 10);
    $textoResumen = 'Registrado en el sistema el ' . date('d/m/Y', strtotime($data['FEC_CRE']));
    $pdf->Cell(0, 4, $pdf->enc($textoResumen), 0, 1);
}

// Limpiar *cualquier* salida previa y enviar el PDF
if (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output('I', 'reporte_estudiantes.pdf');
exit;
