<?php
declare(strict_types=1);

// Muy importante: nada de salida antes de esto
ob_start();

require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../assets/fpdf186/fpdf.php';

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

        $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $txt);
        return $converted === false ? $txt : $converted;
    }

    // ===================== HEADER / FOOTER ===================== //

    public function Header(): void
    {
        // Degradado de fondo
        $this->SetFillColor(26, 35, 126);
        $this->Rect(0, 0, 210, 60, 'F');

        $this->SetFillColor(30, 55, 153);
        $this->Rect(0, 0, 210, 50, 'F');

        // Diseño decorativo
        $this->SetFillColor(63, 81, 181);
        $this->SetAlpha(0.3);
        $this->Circle(180, 20, 35);
        $this->Circle(200, 40, 25);
        $this->SetAlpha(1);

        // “Logo”
        $this->SetFillColor(255, 255, 255);
        $this->Circle(35, 25, 15, 'F');
        $this->SetTextColor(26, 35, 126);
        $this->SetFont('Arial', 'B', 24);
        $this->SetXY(25, 17);
        $this->Cell(20, 16, 'E', 0, 0, 'C');

        // Título
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

    public function Footer(): void
    {
        $this->SetY(-25);

        $this->SetDrawColor(63, 81, 181);
        $this->SetLineWidth(0.5);
        $this->Line(20, $this->GetY(), 190, $this->GetY());
        $this->Ln(3);

        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(85, 4, $this->enc('Sistema de Gestión Académica 2024'), 0, 0, 'L');
        $this->Cell(85, 4, $this->enc('Generado: ') . date('d/m/Y H:i'), 0, 0, 'R');
        $this->Ln(4);

        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 4, $this->enc('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    // ===================== COMPONENTES VISUALES ===================== //

    public function SectionTitle(string $title, string $prefix = ''): void
    {
        $this->Ln(5);

        $this->SetFillColor(63, 81, 181);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->Rect($x, $y, 170, 12, 'F');

        $this->SetFillColor(83, 109, 254);
        $this->Rect($x, $y, 170, 10, 'F');

        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 13);
        $texto = trim(($prefix !== '' ? $prefix . ' ' : '') . $title);
        $this->Cell(170, 10, $this->enc($texto), 0, 1, 'L');
        $this->Ln(3);
    }

    public function InfoRow(string $label, string $value, bool $last = false): void
    {
        $y = $this->GetY();

        // Fondo alternado (no crítico; solo estética)
        if (((int)$this->GetY()) % 20 > 10) {
            $this->SetFillColor(250, 250, 255);
        } else {
            $this->SetFillColor(255, 255, 255);
        }

        // Caja base (altura mínima)
        $this->Rect(20, $y, 170, 11, 'F');

        // Etiqueta
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(63, 81, 181);
        $this->SetXY(25, $y + 2);
        $this->Cell(55, 7, $this->enc($label), 0, 0, 'L');

        // Valor (permite varias líneas)
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(50, 50, 50);
        $this->SetXY(82, $y + 2);
        $this->MultiCell(103, 7, $this->enc($value), 0, 'L');

        // Línea divisoria
        if (!$last) {
            $this->SetDrawColor(220, 220, 230);
            $this->SetLineWidth(0.2);
            $this->Line(25, $this->GetY(), 185, $this->GetY());
        }
    }

    public function StatusBadge(string $status, array $color): void
    {
        $x = $this->GetX();
        $y = $this->GetY();

        $this->SetFillColor($color[0], $color[1], $color[2]);
        $this->RoundedRect($x, $y, 45, 8, 2, 'F');

        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 9);
        $this->SetXY($x, $y + 1);
        $this->Cell(45, 6, $this->enc($status), 0, 0, 'C');
    }

    public function RoundedRect($x, $y, $w, $h, $r, $style = ''): void
    {
        $k  = $this->k;
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
        $this->_out($style === 'F' ? 'f' : 'S');
    }

    private function _Arc($x1, $y1, $r, $a, $b): void
    {
        $x = $x1 + $r * cos(deg2rad($a));
        $y = $y1 + $r * sin(deg2rad($a));
        $this->_out(sprintf('%.2F %.2F l', $x * $this->k, ($this->h - $y) * $this->k));
    }

    public function Circle($x, $y, $r, $style = 'D'): void
    {
        $this->Ellipse($x, $y, $r, $r, $style);
    }

    public function Ellipse($x, $y, $rx, $ry, $style = 'D'): void
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
            $x * $k,         ($h - ($y - $ry)) * $k
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
            $style === 'F' ? 'f' : 's'
        ));
    }

    public function SetAlpha($alpha): void
    {
        // Simulación de transparencia (sin efecto real en FPDF estándar)
    }
}

// ===================== ENTRADA: ID DEL ESTUDIANTE ===================== //

$idEst = $_GET['id_est']
    ?? $_GET['ID_EST']
    ?? $_GET['id']
    ?? '';

$idEst = trim((string)$idEst);

// Modo de salida: I = inline (en el navegador), D = descarga
$modoSalida = (isset($_GET['download']) && $_GET['download'] === '1') ? 'D' : 'I';

// Si no viene parámetro, mostramos un PDF con aviso
if ($idEst === '') {
    $pdf = new PDF();
    $pdf->SetMargins(20, 20, 20);
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(200, 0, 0);
    $pdf->Cell(0, 10, $pdf->enc('No se proporcionó el identificador de estudiante.'), 0, 1, 'C');

    if (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output('I', 'reporte_estudiante.pdf');
    exit;
}

// ===================== CONSULTAS A BASE DE DATOS ===================== //

$db = Database::getInstance()->getConnection();

// Datos del estudiante
$sqlEst = "
    SELECT *
    FROM ESTUDIANTES
    WHERE ID_EST = :id_est
";
$stmtEst = $db->prepare($sqlEst);
$stmtEst->bindValue(':id_est', $idEst, PDO::PARAM_STR);
$stmtEst->execute();
$estudiante = $stmtEst->fetch(PDO::FETCH_ASSOC);

// Si no existe el estudiante
if (!$estudiante) {
    $pdf = new PDF();
    $pdf->SetMargins(20, 20, 20);
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(200, 0, 0);
    $pdf->Cell(0, 10, $pdf->enc('No se encontró el estudiante con ID: ' . $idEst), 0, 1, 'C');

    if (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output('I', 'reporte_estudiante.pdf');
    exit;
}

// Cursos en los que está inscrito
$sqlCursos = "
    SELECT 
        C.NOM_CUR,
        I.FEC_INS
    FROM INSCRIPCIONES I
    INNER JOIN CURSOS C ON I.ID_CUR_INS = C.ID_CUR
    WHERE I.ID_EST_INS = :id_est
    ORDER BY I.FEC_INS ASC, C.NOM_CUR ASC
";
$stmtCur = $db->prepare($sqlCursos);
$stmtCur->bindValue(':id_est', $idEst, PDO::PARAM_STR);
$stmtCur->execute();
$cursos = $stmtCur->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Para tiempo inscrito: tomamos la primera inscripción
$fechaPrimeraIns = null;
foreach ($cursos as $c) {
    if (!empty($c['FEC_INS'])) {
        $f = new DateTime($c['FEC_INS']);
        if ($fechaPrimeraIns === null || $f < $fechaPrimeraIns) {
            $fechaPrimeraIns = $f;
        }
    }
}

// ===================== GENERACIÓN DEL PDF ===================== //

$pdf = new PDF();
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(true, 25);
$pdf->AddPage();

// Tarjeta principal del estudiante
$yStart = $pdf->GetY();

$pdf->SetFillColor(255, 255, 255);
$pdf->RoundedRect(20, $yStart, 170, 35, 3, 'F');

$pdf->SetDrawColor(63, 81, 181);
$pdf->SetLineWidth(0.5);
$pdf->RoundedRect(20, $yStart, 170, 35, 3, 'D');

// Badge con ID
$pdf->SetXY(25, $yStart + 5);
$pdf->StatusBadge('ID: ' . $estudiante['ID_EST'], [76, 175, 80]);

// Nombre
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(26, 35, 126);
$pdf->SetXY(25, $yStart + 15);
$pdf->Cell(
    0,
    8,
    $pdf->enc($estudiante['NOM_EST'] . ' ' . $estudiante['APE_EST']),
    0,
    1
);

// Correo
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->SetX(25);
$pdf->Cell(0, 5, $pdf->enc('Correo electrónico: ' . $estudiante['COR_EST']), 0, 1);

$pdf->SetY($yStart + 35 + 8);

// ========== 1. DATOS DE CONTACTO ========== //
$pdf->SectionTitle('DATOS DE CONTACTO', '1.');

$pdf->InfoRow('Correo Electrónico:', (string)$estudiante['COR_EST']);
$pdf->InfoRow('Número Telefónico:', $estudiante['TEL_EST'] ?: 'No registrado', true);

// ========== 2. INFORMACIÓN ACADÉMICA ========== //
$pdf->SectionTitle('INFORMACIÓN ACADÉMICA', '2.');

// Fecha de registro en el sistema
$fechaCre = !empty($estudiante['FEC_CRE'])
    ? date('d/m/Y', strtotime($estudiante['FEC_CRE']))
    : 'No disponible';
$pdf->InfoRow('Fecha de Registro:', $fechaCre);

// Información de inscripciones
if (count($cursos) > 0) {
    $pdf->InfoRow('Total de Cursos Inscritos:', (string)count($cursos));

    // Construimos un listado tipo:
    // 1. Física (inscrito el 01/01/2025)
    // 2. Matemáticas (inscrito el 05/02/2025)
    $lineas = [];
    foreach ($cursos as $idx => $curso) {
        $nombre = $curso['NOM_CUR'] ?? 'Curso sin nombre';
        $fecIns = !empty($curso['FEC_INS'])
            ? date('d/m/Y', strtotime($curso['FEC_INS']))
            : 'Fecha no registrada';

        $lineas[] = ($idx + 1) . '. ' . $nombre . ' (inscrito el ' . $fecIns . ')';
    }
    $textoCursos = implode("\n", $lineas);

    $pdf->InfoRow('Cursos Inscritos:', $textoCursos, true);
} else {
    $pdf->InfoRow('Cursos Inscritos:', 'No registra inscripciones en cursos.', true);
}

// Resumen final
$pdf->Ln(5);
$pdf->SetFillColor(248, 249, 250);
$pdf->Rect(20, $pdf->GetY(), 170, 18, 'F');

$pdf->SetDrawColor(220, 220, 230);
$pdf->Rect(20, $pdf->GetY(), 170, 18, 'D');

$yStats = $pdf->GetY();

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->SetXY(25, $yStats + 4);
$pdf->Cell(0, 5, $pdf->enc('RESUMEN DEL ESTUDIANTE'), 0, 1);

$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(80, 80, 80);
$pdf->SetXY(25, $yStats + 10);

$textoResumen = 'Registrado en el sistema el ' . $fechaCre;

if ($fechaPrimeraIns instanceof DateTime) {
    $hoy  = new DateTime();
    $diff = $hoy->diff($fechaPrimeraIns);
    $dias = $diff->days;
    $textoResumen .= ' · Primera inscripción hace ' . $dias . ' días.';
}

$pdf->Cell(0, 4, $pdf->enc($textoResumen), 0, 1);

// ===================== SALIDA ===================== //

if (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output('I', 'reporte_estudiante.pdf');
exit;
