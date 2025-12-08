<?php
declare(strict_types=1);

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
        $this->SetFillColor(26, 35, 126);
        $this->Rect(0, 0, 210, 55, 'F');

        $this->SetFillColor(30, 55, 153);
        $this->Rect(0, 0, 210, 47, 'F');

        $this->SetFillColor(63, 81, 181);
        $this->Circle(185, 20, 28);
        $this->Circle(200, 38, 18);

        $this->SetFillColor(255, 255, 255);
        $this->Circle(30, 22, 13, 'F');
        $this->SetTextColor(26, 35, 126);
        $this->SetFont('Arial', 'B', 20);
        $this->SetXY(21, 15);
        // Emoji se degrada a un carácter soportado según la fuente
        $this->Cell(18, 14, $this->enc('📊'), 0, 0, 'C');

        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 22);
        $this->SetXY(50, 12);
        $this->Cell(0, 8, $this->enc('ANÁLISIS ESTADÍSTICO'), 0, 1);

        $this->SetFont('Arial', '', 11);
        $this->SetXY(50, 24);
        $this->Cell(0, 6, $this->enc('Distribución de Estudiantes por Curso'), 0, 1);

        // Línea decorativa dorada
        $this->SetDrawColor(255, 193, 7);
        $this->SetLineWidth(1.2);
        $this->Line(50, 34, 155, 34);

        $this->Ln(25);
    }

    function Footer()
    {
        $this->SetY(-20);

        $this->SetDrawColor(63, 81, 181);
        $this->SetLineWidth(0.5);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        $this->Ln(3);

        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);

        $this->Cell(85, 4, $this->enc('Sistema de Gestión Académica © 2024'), 0, 0, 'L');
        $this->Cell(85, 4, $this->enc('Generado: ') . date('d/m/Y H:i:s'), 0, 0, 'R');
        $this->Ln(4);

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 4, $this->enc('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    function Circle($x, $y, $r, $style = 'D')
    {
        $this->Ellipse($x, $y, $r, $r, $style);
    }

    function Ellipse($x, $y, $rx, $ry, $style = 'D')
    {
        $lx = 4 / 3 * (M_SQRT2 - 1) * $rx;
        $ly = 4 / 3 * (M_SQRT2 - 1) * $ry;
        $k = $this->k;
        $h = $this->h;

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

    function DrawModernBarChart(array $data, int $max)
    {
        // Si no hay datos, no dibujamos nada
        if ($max <= 0 || empty($data)) {
            return 0;
        }

        // Configuración del gráfico
        $chartX = 20;
        $chartY = 70;
        $chartWidth = 170;
        $chartHeight = 120;
        $barWidth = 25;
        $spacing = 8;

        // Fondo del área del gráfico
        $this->SetFillColor(248, 249, 250);
        $this->Rect($chartX - 5, $chartY - 5, $chartWidth + 10, $chartHeight + 45, 'F');

        // Borde del área del gráfico
        $this->SetDrawColor(220, 220, 230);
        $this->SetLineWidth(0.3);
        $this->Rect($chartX - 5, $chartY - 5, $chartWidth + 10, $chartHeight + 45, 'D');

        // Líneas de cuadrícula horizontales
        $this->SetDrawColor(230, 230, 240);
        $this->SetLineWidth(0.2);
        for ($i = 0; $i <= 5; $i++) {
            $gridY = $chartY + ($chartHeight * $i / 5);
            $this->Line($chartX, $gridY, $chartX + $chartWidth, $gridY);

            // Etiquetas del eje Y
            $value = round($max - ($max * $i / 5));
            $this->SetFont('Arial', '', 7);
            $this->SetTextColor(120, 120, 120);
            $this->SetXY($chartX - 12, $gridY - 2);
            $this->Cell(10, 4, (string) $value, 0, 0, 'R');
        }

        // Eje X (línea base)
        $this->SetDrawColor(100, 100, 100);
        $this->SetLineWidth(0.5);
        $this->Line($chartX, $chartY + $chartHeight, $chartX + $chartWidth, $chartY + $chartHeight);

        // Eje Y
        $this->Line($chartX, $chartY, $chartX, $chartY + $chartHeight);

        // Colores para las barras (paleta vibrante)
        $colors = [
            [76, 175, 80],   // Verde
            [33, 150, 243],  // Azul
            [255, 152, 0],   // Naranja
            [156, 39, 176],  // Púrpura
            [244, 67, 54],   // Rojo
            [0, 188, 212],   // Cyan
            [255, 235, 59],  // Amarillo
            [121, 85, 72],   // Marrón
        ];

        $x = $chartX + 10;
        $colorIndex = 0;
        $totalSum = 0;

        foreach ($data as $row) {
            $totalSum += (int) $row['TOTAL'];
        }
        if ($totalSum === 0) {
            return 0;
        }

        foreach ($data as $row) {
            $valor = (int) $row['TOTAL'];
            $barHeight = ($valor / $max) * $chartHeight;
            $barY = $chartY + $chartHeight - $barHeight;

            // Color de la barra
            $color = $colors[$colorIndex % count($colors)];

            // Sombra de la barra
            $this->SetFillColor(
                max(0, $color[0] - 20),
                max(0, $color[1] - 20),
                max(0, $color[2] - 20)
            );
            $this->Rect($x + 1, $barY + 1, $barWidth, $barHeight, 'F');

            // Barra principal
            $this->SetFillColor($color[0], $color[1], $color[2]);
            $this->Rect($x, $barY, $barWidth, $barHeight, 'F');

            // Borde de la barra
            $this->SetDrawColor(
                max(0, $color[0] - 30),
                max(0, $color[1] - 30),
                max(0, $color[2] - 30)
            );
            $this->SetLineWidth(0.5);
            $this->Rect($x, $barY, $barWidth, $barHeight, 'D');

            // Valor encima de la barra
            $this->SetFont('Arial', 'B', 11);
            $this->SetTextColor(50, 50, 50);
            $this->SetXY($x, $barY - 7);
            $this->Cell($barWidth, 5, (string) $valor, 0, 0, 'C');

            // Nombre del curso
            $this->SetFont('Arial', 'B', 8);
            $this->SetTextColor(70, 70, 70);
            $this->SetXY($x - 2, $chartY + $chartHeight + 3);

            $nombre = $this->enc($row['NOM_CUR']);
            if (strlen($nombre) > 16) {
                $nombre = substr($nombre, 0, 14) . '..';
            }

            $this->Cell($barWidth + 4, 5, $nombre, 0, 0, 'C');

            // Porcentaje debajo
            $percentage = ($valor / $totalSum) * 100;
            $this->SetFont('Arial', '', 7);
            $this->SetTextColor(100, 100, 100);
            $this->SetXY($x - 2, $chartY + $chartHeight + 8);
            $this->Cell($barWidth + 4, 4, number_format($percentage, 1) . '%', 0, 0, 'C');

            $x += $barWidth + $spacing;
            $colorIndex++;
        }

        return $totalSum;
    }

    function StatsCards(array $data, int $max, int $total)
    {
        $y = 210;

        // Card 1: Total de estudiantes
        $this->SetFillColor(76, 175, 80);
        $this->RoundedRect(20, $y, 52, 28, 2, 'F');

        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 20);
        $this->SetXY(20, $y + 5);
        $this->Cell(52, 8, (string) $total, 0, 0, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetXY(20, $y + 16);
        $this->Cell(52, 5, $this->enc('Total'), 0, 0, 'C');
        $this->SetXY(20, $y + 21);
        $this->Cell(52, 4, $this->enc('Estudiantes'), 0, 0, 'C');

        // Card 2: Total de cursos
        $this->SetFillColor(33, 150, 243);
        $this->RoundedRect(78, $y, 52, 28, 2, 'F');

        $this->SetFont('Arial', 'B', 20);
        $this->SetXY(78, $y + 5);
        $this->Cell(52, 8, (string) count($data), 0, 0, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetXY(78, $y + 16);
        $this->Cell(52, 5, $this->enc('Total'), 0, 0, 'C');
        $this->SetXY(78, $y + 21);
        $this->Cell(52, 4, $this->enc('Cursos'), 0, 0, 'C');

        // Card 3: Máximo por curso
        $this->SetFillColor(255, 152, 0);
        $this->RoundedRect(136, $y, 54, 28, 2, 'F');

        $this->SetFont('Arial', 'B', 20);
        $this->SetXY(136, $y + 5);
        $this->Cell(54, 8, (string) $max, 0, 0, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetXY(136, $y + 16);
        $this->Cell(54, 5, $this->enc('Máximo por'), 0, 0, 'C');
        $this->SetXY(136, $y + 21);
        $this->Cell(54, 4, $this->enc('Curso'), 0, 0, 'C');
    }

    function RoundedRect($x, $y, $w, $h, $r, $style = '')
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

    function _Arc($x1, $y1, $r, $a, $b)
    {
        $d = $b - $a;
        $x = $x1 + $r * cos(deg2rad($a));
        $y = $y1 + $r * sin(deg2rad($a));
        $this->_out(sprintf('%.2F %.2F l', $x * $this->k, ($this->h - $y) * $this->k));
    }
}

// =============================
//      LÓGICA DEL REPORTE
// =============================

$db = Database::getInstance()->getConnection();

$sql = "
    SELECT 
        C.NOM_CUR,
        COUNT(*) AS TOTAL
    FROM INSCRIPCIONES I
    INNER JOIN CURSOS C ON I.ID_CUR_INS = C.ID_CUR
    GROUP BY C.ID_CUR, C.NOM_CUR
    ORDER BY TOTAL DESC;
";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular máximo y total
$max = 0;
foreach ($rows as $r) {
    $valor = (int) $r['TOTAL'];
    if ($valor > $max) {
        $max = $valor;
    }
}

$pdf = new PDF();
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(63, 81, 181);
$pdf->Cell(0, 8, $pdf->enc('📈 Gráfico de Barras Comparativo'), 0, 1, 'L');

$pdf->SetDrawColor(63, 81, 181);
$pdf->SetLineWidth(0.6);
$pdf->Line(15, $pdf->GetY(), 75, $pdf->GetY());
$pdf->Ln(5);

if (empty($rows)) {
    $pdf->SetFont('Arial', 'I', 11);
    $pdf->SetTextColor(120, 120, 120);
    $pdf->Cell(0, 10, $pdf->enc('No existen inscripciones registradas para generar el gráfico.'), 0, 1, 'C');
} else {
    $totalSum = $pdf->DrawModernBarChart($rows, $max);
    $pdf->StatsCards($rows, $max, $totalSum);
}

$pdf->Output();
exit;
