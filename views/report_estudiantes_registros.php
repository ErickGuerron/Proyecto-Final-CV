<?php
declare(strict_types=1);

ob_start(); // Evitar cualquier salida antes del PDF

require_once '../models/database.php';
require_once '../assets/fpdf186/fpdf.php';

class PDF extends FPDF
{
    private $totalEstudiantes = 0;
    private $totalDiasRegistro = 0;

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

    // ================= HEADER / FOOTER =================

    function Header()
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

        // Título principal (ahora orientado a fecha de registro)
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 22);
        $this->SetXY(45, 10);
        $this->Cell(0, 8, $this->enc('REPORTE DE ESTUDIANTES POR FECHA DE REGISTRO'), 0, 1);

        $this->SetFont('Arial', '', 11);
        $this->SetXY(45, 22);
        $this->Cell(0, 6, $this->enc('Listado de estudiantes registrados en el sistema por día'), 0, 1);

        // Línea decorativa
        $this->SetDrawColor(255, 193, 7);
        $this->SetLineWidth(1.2);
        $this->Line(45, 32, 200, 32);

        $this->Ln(20);
    }

    function Footer()
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
        $this->Cell(0, 4, $this->enc('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }

    // ================= FORMAS =================

    function Circle($x, $y, $r, $style = 'D')
    {
        $this->Ellipse($x, $y, $r, $r, $style);
    }

    function Ellipse($x, $y, $rx, $ry, $style = 'D')
    {
        $lx = 4 / 3 * (M_SQRT2 - 1) * $rx;
        $ly = 4 / 3 * (M_SQRT2 - 1) * $ry;
        $k  = $this->k;
        $h  = $this->h;

        $this->_out(sprintf(
            '%.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x + $rx) * $k,
            ($h - $y) * $k,
            ($x + $rx) * $k,
            ($h - ($y - $ly)) * $k,
            ($x + $lx) * $k,
            ($h - ($y - $ry)) * $k,
            $x * $k,
            ($h - ($y - $ry)) * $k
        ));
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x - $lx) * $k,
            ($h - ($y - $ry)) * $k,
            ($x - $rx) * $k,
            ($h - ($y - $ly)) * $k,
            ($x - $rx) * $k,
            ($h - $y) * $k
        ));
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x - $rx) * $k,
            ($h - ($y + $ly)) * $k,
            ($x - $lx) * $k,
            ($h - ($y + $ry)) * $k,
            $x * $k,
            ($h - ($y + $ry)) * $k
        ));
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c %s',
            ($x + $lx) * $k,
            ($h - ($y + $ry)) * $k,
            ($x + $rx) * $k,
            ($h - ($y + $ly)) * $k,
            ($x + $rx) * $k,
            ($h - $y) * $k,
            $style == 'F' ? 'f' : 's'
        ));
    }

    // ================= TABLA PRINCIPAL =================

    function TableHeader()
    {
        // Fondo del header de tabla
        $this->SetFillColor(63, 81, 181);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(63, 81, 181);
        $this->SetLineWidth(0.3);
        $this->SetFont('Arial', 'B', 10);

        // Cedula, Nombre, Apellido, Teléfono, Correo, Dirección, F. Nac, F. Registro
        $this->Cell(30, 10, $this->enc('Cédula'), 1, 0, 'C', true);
        $this->Cell(35, 10, $this->enc('Nombre'), 1, 0, 'L', true);
        $this->Cell(35, 10, $this->enc('Apellido'), 1, 0, 'L', true);
        $this->Cell(30, 10, $this->enc('Teléfono'), 1, 0, 'C', true);
        $this->Cell(55, 10, $this->enc('Correo Electrónico'), 1, 0, 'L', true);
        $this->Cell(55, 10, $this->enc('Dirección'), 1, 0, 'L', true);
        $this->Cell(27, 10, $this->enc('F. Nac.'), 1, 0, 'C', true);
        $this->Cell(30, 10, $this->enc('F. Registro'), 1, 0, 'C', true);
        $this->Ln();
    }

    function TableRow(array $data, int $index, ?string $fechaActual = null)
    {
        // Colores alternados
        if ($index % 2 == 0) {
            $this->SetFillColor(250, 250, 255);
        } else {
            $this->SetFillColor(255, 255, 255);
        }

        $this->SetTextColor(50, 50, 50);
        $this->SetDrawColor(220, 220, 230);
        $this->SetFont('Arial', '', 9);

        // Cédula
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(63, 81, 181);
        $this->Cell(30, 9, $data['ID_EST'], 1, 0, 'C', true);

        // Nombre
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(35, 9, $this->enc($data['NOM_EST']), 1, 0, 'L', true);

        // Apellido
        $this->Cell(35, 9, $this->enc($data['APE_EST']), 1, 0, 'L', true);

        // Teléfono
        $this->Cell(30, 9, $data['TEL_EST'] ?: 'No registrado', 1, 0, 'C', true);

        // Correo
        $this->SetFont('Arial', '', 8);
        $this->Cell(55, 9, $data['COR_EST'], 1, 0, 'L', true);

        // Dirección
        $this->SetFont('Arial', '', 8);
        $this->Cell(55, 9, $this->enc($data['DIR_EST']), 1, 0, 'L', true);

        // F. Nacimiento
        $this->SetFont('Arial', '', 8);
        $this->Cell(27, 9, date('d/m/Y', strtotime($data['FEC_NAC'])), 1, 0, 'C', true);

        // F. Registro
        $this->Cell(30, 9, date('d/m/Y', strtotime($data['FEC_CRE'])), 1, 0, 'C', true);

        $this->Ln();
    }

    // ================= BANNER / STATS =================

    function StatsCard(int $total_estudiantes, int $total_dias)
    {
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
        $this->Cell(0, 6, $this->enc('📊 RESUMEN POR FECHA DE REGISTRO'), 0, 1);

        // Línea divisoria
        $this->SetDrawColor(220, 220, 230);
        $this->Line(15, $y_start + 13, 282, $y_start + 13);

        // Estadística 1: Total de estudiantes
        $this->SetXY(40, $y_start + 16);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(76, 175, 80);
        $this->Cell(60, 8, (string)$total_estudiantes, 0, 0, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->SetXY(40, $y_start + 24);
        $this->Cell(60, 4, $this->enc('Total Estudiantes'), 0, 0, 'C');

        // Línea vertical divisoria
        $this->SetDrawColor(220, 220, 230);
        $this->Line(138.5, $y_start + 16, 138.5, $y_start + 28);

        // Estadística 2: Total de días con registros
        $this->SetXY(117, $y_start + 16);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(33, 150, 243);
        $this->Cell(60, 8, (string)$total_dias, 0, 0, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->SetXY(117, $y_start + 24);
        $this->Cell(60, 4, $this->enc('Días con registros'), 0, 0, 'C');

        // Línea vertical divisoria
        $this->Line(215.5, $y_start + 16, 215.5, $y_start + 28);

        // Estadística 3: Promedio por día
        $promedio = $total_dias > 0 ? number_format($total_estudiantes / $total_dias, 1) : 0;
        $this->SetXY(194, $y_start + 16);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(255, 152, 0);
        $this->Cell(60, 8, (string)$promedio, 0, 0, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->SetXY(194, $y_start + 24);
        $this->Cell(60, 4, $this->enc('Promedio de registros/día'), 0, 0, 'C');

        $this->SetY($y_start + 35);
    }

    function InfoBanner(?string $fechaFiltro = null)
    {
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

        if ($fechaFiltro) {
            $msg = 'Los datos corresponden a los estudiantes registrados el día ' .
                date('d/m/Y', strtotime($fechaFiltro));
        } else {
            $msg = 'Los datos mostrados corresponden a todos los estudiantes registrados en el sistema';
        }

        $this->Cell(0, 6, $this->enc($msg), 0, 1, 'L');

        $this->Ln(5);
    }

    // ================= SETTERS =================

    function setTotalEstudiantes(int $total): void
    {
        $this->totalEstudiantes = $total;
    }

    function setTotalDiasRegistro(int $total): void
    {
        $this->totalDiasRegistro = $total;
    }
}

// ======================================================
// LÓGICA DEL REPORTE POR FECHA DE REGISTRO
// ======================================================

$db = Database::getInstance()->getConnection();

// Filtro opcional por fecha ?fecha=YYYY-MM-DD
$fechaFiltro = $_GET['fecha'] ?? null;
if ($fechaFiltro !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFiltro)) {
    $fechaFiltro = null; // formateo inválido, se ignora
}

if ($fechaFiltro) {
    $sql = "
        SELECT 
            ID_EST,
            NOM_EST,
            APE_EST,
            TEL_EST,
            COR_EST,
            DIR_EST,
            FEC_NAC,
            FEC_CRE
        FROM ESTUDIANTES
        WHERE FEC_CRE = :fecha
        ORDER BY FEC_CRE ASC, APE_EST ASC, NOM_EST ASC;
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute([':fecha' => $fechaFiltro]);
} else {
    $sql = "
        SELECT 
            ID_EST,
            NOM_EST,
            APE_EST,
            TEL_EST,
            COR_EST,
            DIR_EST,
            FEC_NAC,
            FEC_CRE
        FROM ESTUDIANTES
        ORDER BY FEC_CRE ASC, APE_EST ASC, NOM_EST ASC;
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
}

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular estadísticas
$total_estudiantes = count($rows);
$fechas_unicas     = array_unique(array_column($rows, 'FEC_CRE'));
$total_dias        = count($fechas_unicas);

// Crear PDF en orientación horizontal
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->setTotalEstudiantes($total_estudiantes);
$pdf->setTotalDiasRegistro($total_dias);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 25);
$pdf->AddPage();

// Banner informativo (con o sin filtro)
$pdf->InfoBanner($fechaFiltro);

// Encabezado de la tabla
$pdf->TableHeader();

// Datos de la tabla
if ($total_estudiantes === 0) {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetTextColor(120, 120, 120);
    $pdf->Cell(0, 10, $pdf->enc('No se encontraron estudiantes para los criterios seleccionados.'), 1, 1, 'C');
} else {
    $index = 0;
    foreach ($rows as $row) {
        $pdf->TableRow($row, $index);
        $index++;
    }
}

// Card de estadísticas al final
$pdf->StatsCard($total_estudiantes, $total_dias);

// Limpiar cualquier salida previa y enviar el PDF
if (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output('I', 'reporte_estudiantes_registro.pdf');
exit;
