<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../assets/fpdf186/fpdf.php';

class PDF extends FPDF
{
    private int $totalEstudiantes = 0;
    private int $totalCursos = 0;

    // Anchos de columnas (DEBEN coincidir en header y filas)
    private float $colWidthId        = 12.0;
    private float $colWidthCurso     = 70.0;
    private float $colWidthEstudiante= 70.0;
    private float $colWidthCorreo    = 70.0;
    private float $colWidthTelefono  = 55.0;

    public function setTotalEstudiantes(int $total): void
    {
        $this->totalEstudiantes = $total;
    }

    public function setTotalCursos(int $total): void
    {
        $this->totalCursos = $total;
    }

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

    /**
     * Ajusta un texto (UTF-8) para que quepa en un ancho de celda en mm.
     * Si no cabe, lo recorta y agrega "..." al final.
     */
    private function fitTextToWidth(?string $text, float $cellWidthMm): string
    {
        $text = (string)($text ?? '');
        $ellipsis = '...';
        $maxWidth = $cellWidthMm - 2; // pequeño margen dentro de la celda

        // Texto completo
        $encoded = $this->enc($text);
        if ($this->GetStringWidth($encoded) <= $maxWidth) {
            return $encoded;
        }

        // Intentar con recorte progresivo
        if (function_exists('mb_strlen')) {
            $len = mb_strlen($text, 'UTF-8');
            while ($len > 0) {
                $candidate = mb_substr($text, 0, $len, 'UTF-8') . $ellipsis;
                $encodedCandidate = $this->enc($candidate);
                if ($this->GetStringWidth($encodedCandidate) <= $maxWidth) {
                    return $encodedCandidate;
                }
                $len--;
            }
        } else {
            $len = strlen($text);
            while ($len > 0) {
                $candidate = substr($text, 0, $len) . $ellipsis;
                $encodedCandidate = $this->enc($candidate);
                if ($this->GetStringWidth($encodedCandidate) <= $maxWidth) {
                    return $encodedCandidate;
                }
                $len--;
            }
        }

        // Si nada cabe, devolver solo "..."
        return $this->enc($ellipsis);
    }

    // ------------------ HEADER / FOOTER ------------------ //

    function Header(): void
    {
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
        $this->Cell(0, 8, $this->enc('REPORTE DE ESTUDIANTES POR CURSO'), 0, 1);

        $this->SetFont('Arial', '', 11);
        $this->SetXY(45, 22);
        $this->Cell(0, 6, $this->enc('Listado completo de inscripciones académicas'), 0, 1);

        // Línea decorativa
        $this->SetDrawColor(255, 193, 7);
        $this->SetLineWidth(1.2);
        $this->Line(45, 32, 160, 32);

        $this->Ln(20);
    }

    function Footer(): void
    {
        $this->SetY(-20);

        // Línea superior
        $this->SetDrawColor(63, 81, 181);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 287, $this->GetY());
        $this->Ln(3);

        // Información del footer
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);

        $this->Cell(135, 4, $this->enc('Sistema de Gestión Académica © 2024'), 0, 0, 'L');
        $this->Cell(135, 4, $this->enc('Generado: ') . date('d/m/Y H:i:s'), 0, 0, 'R');
        $this->Ln(4);

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 4, $this->enc('Página ') . $this->PageNo() . $this->enc(' de {nb}'), 0, 0, 'C');
    }

    // ------------------ FORMAS ------------------ //

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

    // ------------------ TABLA ------------------ //

    function TableHeader(): void
    {
        // Fondo del header de tabla
        $this->SetFillColor(63, 81, 181);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(63, 81, 181);
        $this->SetLineWidth(0.3);
        $this->SetFont('Arial', 'B', 10);

        $this->Cell($this->colWidthId,         10, $this->enc('ID'),                  1, 0, 'C', true);
        $this->Cell($this->colWidthCurso,      10, $this->enc('Curso'),               1, 0, 'L', true);
        $this->Cell($this->colWidthEstudiante, 10, $this->enc('Estudiante'),          1, 0, 'L', true);
        $this->Cell($this->colWidthCorreo,     10, $this->enc('Correo Electrónico'),  1, 0, 'L', true);
        $this->Cell($this->colWidthTelefono,   10, $this->enc('Teléfono'),            1, 0, 'C', true);
        $this->Ln();
    }

    function TableRow(array $data, int $index): void
    {
        // Fondo alternado
        if ($index % 2 === 0) {
            $this->SetFillColor(250, 250, 255);
        } else {
            $this->SetFillColor(255, 255, 255);
        }

        $this->SetDrawColor(220, 220, 230);
        $this->SetLineWidth(0.2);

        // --- ID ---
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(63, 81, 181);
        $idText = $this->fitTextToWidth((string)$data['ID_EST'], $this->colWidthId);
        $this->Cell($this->colWidthId, 9, $idText, 1, 0, 'C', true);

        // --- Curso ---
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(26, 35, 126);
        $cursoText = $this->fitTextToWidth((string)$data['CURSO'], $this->colWidthCurso);
        $this->Cell($this->colWidthCurso, 9, $cursoText, 1, 0, 'L', true);

        // --- Estudiante ---
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(50, 50, 50);
        $estText = $this->fitTextToWidth((string)$data['ESTUDIANTE'], $this->colWidthEstudiante);
        $this->Cell($this->colWidthEstudiante, 9, $estText, 1, 0, 'L', true);

        // --- Correo ---
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(70, 70, 70);
        $correoText = $this->fitTextToWidth((string)$data['COR_EST'], $this->colWidthCorreo);
        $this->Cell($this->colWidthCorreo, 9, $correoText, 1, 0, 'L', true);

        // --- Teléfono ---
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(50, 50, 50);
        $telefono = $data['TEL_EST'] ?? '';
        if ($telefono === null || $telefono === '') {
            $telefono = 'No registrado';
        }
        $telText = $this->fitTextToWidth((string)$telefono, $this->colWidthTelefono);
        $this->Cell($this->colWidthTelefono, 9, $telText, 1, 0, 'C', true);

        $this->Ln();
    }

    // ------------------ RESUMEN / BANNER ------------------ //

    function StatsCard(int $totalEstudiantes, int $totalCursos): void
    {
        $this->Ln(8);

        $yStart = $this->GetY();

        // Card de estadísticas
        $this->SetFillColor(248, 249, 250);
        $this->Rect(10, $yStart, 277, 30, 'F');

        $this->SetDrawColor(63, 81, 181);
        $this->SetLineWidth(0.5);
        $this->Rect(10, $yStart, 277, 30, 'D');

        // Título de estadísticas
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(63, 81, 181);
        $this->SetXY(15, $yStart + 5);
        $this->Cell(0, 6, $this->enc('📊 RESUMEN ESTADÍSTICO'), 0, 1);

        // Línea divisoria
        $this->SetDrawColor(220, 220, 230);
        $this->Line(15, $yStart + 13, 282, $yStart + 13);

        // Total estudiantes
        $this->SetXY(40, $yStart + 16);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(76, 175, 80);
        $this->Cell(60, 8, (string)$totalEstudiantes, 0, 0, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->SetXY(40, $yStart + 24);
        $this->Cell(60, 4, $this->enc('Total Estudiantes'), 0, 0, 'C');

        // Línea vertical
        $this->SetDrawColor(220, 220, 230);
        $this->Line(138.5, $yStart + 16, 138.5, $yStart + 28);

        // Total cursos
        $this->SetXY(117, $yStart + 16);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(33, 150, 243);
        $this->Cell(60, 8, (string)$totalCursos, 0, 0, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->SetXY(117, $yStart + 24);
        $this->Cell(60, 4, $this->enc('Cursos Activos'), 0, 0, 'C');

        // Línea vertical
        $this->Line(215.5, $yStart + 16, 215.5, $yStart + 28);

        // Promedio estudiantes por curso
        $promedio = $totalCursos > 0
            ? number_format($totalEstudiantes / $totalCursos, 1, ',', '')
            : '0';

        $this->SetXY(194, $yStart + 16);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(255, 152, 0);
        $this->Cell(60, 8, (string)$promedio, 0, 0, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->SetXY(194, $yStart + 24);
        $this->Cell(60, 4, $this->enc('Promedio por Curso'), 0, 0, 'C');

        $this->SetY($yStart + 35);
    }

    function InfoBanner(): void
    {
        $this->Ln(3);

        $y = $this->GetY();

        // Banner informativo
        $this->SetFillColor(232, 245, 233);
        $this->Rect(10, $y, 277, 12, 'F');

        $this->SetDrawColor(76, 175, 80);
        $this->SetLineWidth(0.3);
        $this->Rect(10, $y, 277, 12, 'D');

        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(56, 142, 60);
        $this->SetXY(15, $y + 3);
        $this->Cell(
            0,
            6,
            $this->enc('Los datos mostrados corresponden a todas las inscripciones activas en el sistema'),
            0,
            1,
            'L'
        );

        $this->Ln(5);
    }
}

// ------------------ CONSULTA A BD ------------------ //

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
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Estadísticas
$totalEstudiantes = count($rows);
$cursosUnicos     = $rows ? array_unique(array_column($rows, 'CURSO')) : [];
$totalCursos      = count($cursosUnicos);

// ------------------ GENERACIÓN DEL PDF ------------------ //

$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->setTotalEstudiantes($totalEstudiantes);
$pdf->setTotalCursos($totalCursos);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 25);
$pdf->AddPage();

// Banner informativo
$pdf->InfoBanner();

// Encabezado de tabla
$pdf->TableHeader();

// Filas
foreach ($rows as $index => $row) {
    $pdf->TableRow($row, (int)$index);
}

// Card de estadísticas
$pdf->StatsCard($totalEstudiantes, $totalCursos);

$pdf->Output('I', 'reporte_estudiantes_cursos.pdf');
exit;
