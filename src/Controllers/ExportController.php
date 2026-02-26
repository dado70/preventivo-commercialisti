<?php
/**
 * Preventivo Commercialisti - ExportController
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;
use App\Models\Preventivo;
use App\Models\Studio;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportController
{
    // ----------------------------------------------------------------
    // PDF via mPDF
    // ----------------------------------------------------------------

    /**
     * Genera e invia il PDF del preventivo come download.
     *
     * @param array $params  Deve contenere $params['id'] = id preventivo
     */
    public function pdf(array $params): void
    {
        Auth::requireLogin();

        $id         = (int)($params['id'] ?? 0);
        $preventivo = Preventivo::findById($id);

        if (!$preventivo) {
            Session::flash('error', 'Preventivo non trovato.');
            header('Location: ' . BASE_URL . '/preventivi');
            exit;
        }

        $voci   = Preventivo::getVoci($id);
        $studio = Studio::get();

        $html = $this->getHtmlTemplate($preventivo, $voci, $studio);

        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 15,
            'margin_right'  => 15,
            'margin_top'    => 20,
            'margin_bottom' => 20,
            'default_font'  => 'dejavusans',
        ]);

        $mpdf->SetTitle('Preventivo ' . ($preventivo['numero'] ?? ''));
        $mpdf->SetAuthor($studio['ragione_sociale'] ?? 'Studio');
        $mpdf->SetCreator('Preventivo Commercialisti');

        $mpdf->WriteHTML($html);
        $mpdf->Output('Preventivo_' . ($preventivo['numero'] ?? $id) . '.pdf', 'D');
        exit;
    }

    // ----------------------------------------------------------------
    // ODS via PhpSpreadsheet
    // ----------------------------------------------------------------

    /**
     * Genera e invia il file ODS del preventivo come download.
     *
     * @param array $params  Deve contenere $params['id'] = id preventivo
     */
    public function ods(array $params): void
    {
        Auth::requireLogin();

        $id         = (int)($params['id'] ?? 0);
        $preventivo = Preventivo::findById($id);

        if (!$preventivo) {
            Session::flash('error', 'Preventivo non trovato.');
            header('Location: ' . BASE_URL . '/preventivi');
            exit;
        }

        $voci   = Preventivo::getVoci($id);
        $studio = Studio::get();

        // ----- Calcolo totali -----
        [$lordo, $sc1Imp, $sc2Imp, $netto, $ivaImp, $totale] = $this->calcolaTotaliPreventivo($preventivo, $voci);

        // ----- Creazione Spreadsheet -----
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Preventivo');

        // Larghezze colonne
        $sheet->getColumnDimension('A')->setWidth(6);   // N.
        $sheet->getColumnDimension('B')->setWidth(12);  // Codice
        $sheet->getColumnDimension('C')->setWidth(42);  // Descrizione
        $sheet->getColumnDimension('D')->setWidth(14);  // Frequenza
        $sheet->getColumnDimension('E')->setWidth(10);  // Qta/Mesi
        $sheet->getColumnDimension('F')->setWidth(14);  // Importo unit.
        $sheet->getColumnDimension('G')->setWidth(14);  // Importo riga

        $row = 1;

        // ----- Intestazione Studio -----
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", $studio['ragione_sociale'] ?? '');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
        $row++;

        if (!empty($studio['indirizzo'])) {
            $sheet->mergeCells("A{$row}:D{$row}");
            $indirizzo = trim(
                ($studio['indirizzo'] ?? '') . ', ' .
                ($studio['cap'] ?? '') . ' ' .
                ($studio['citta'] ?? '') .
                (!empty($studio['provincia']) ? ' (' . $studio['provincia'] . ')' : '')
            );
            $sheet->setCellValue("A{$row}", $indirizzo);
            $row++;
        }

        if (!empty($studio['partita_iva'])) {
            $sheet->mergeCells("A{$row}:D{$row}");
            $sheet->setCellValue("A{$row}", 'P.IVA: ' . $studio['partita_iva']);
            $row++;
        }

        if (!empty($studio['pec'])) {
            $sheet->mergeCells("A{$row}:D{$row}");
            $sheet->setCellValue("A{$row}", 'PEC: ' . $studio['pec']);
            $row++;
        }

        $row++; // riga vuota

        // ----- Dati Cliente -----
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("A{$row}", 'SPETTABILE');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}")->getFont()->setColor(
            new \PhpOffice\PhpSpreadsheet\Style\Color('FF555555')
        );
        $row++;

        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", $preventivo['cliente_nome'] ?? '');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
        $row++;

        if (!empty($preventivo['cliente_indirizzo'])) {
            $sheet->mergeCells("A{$row}:D{$row}");
            $indirizzoCliente = trim(
                ($preventivo['cliente_indirizzo'] ?? '') . ', ' .
                ($preventivo['cliente_cap'] ?? '') . ' ' .
                ($preventivo['cliente_citta'] ?? '') .
                (!empty($preventivo['cliente_prov']) ? ' (' . $preventivo['cliente_prov'] . ')' : '')
            );
            $sheet->setCellValue("A{$row}", $indirizzoCliente);
            $row++;
        }

        if (!empty($preventivo['cliente_piva'])) {
            $sheet->mergeCells("A{$row}:D{$row}");
            $sheet->setCellValue("A{$row}", 'P.IVA: ' . $preventivo['cliente_piva']);
            $row++;
        }

        $row++; // riga vuota

        // ----- Titolo Preventivo -----
        $sheet->mergeCells("A{$row}:G{$row}");
        $titoloPrev = 'PREVENTIVO N. ' . ($preventivo['numero'] ?? '') .
                      ' del ' . $this->fmtData($preventivo['data_preventivo'] ?? null);
        $sheet->setCellValue("A{$row}", $titoloPrev);
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        if (!empty($preventivo['titolo'])) {
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->setCellValue("A{$row}", 'Oggetto: ' . $preventivo['titolo']);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        $row++; // riga vuota

        // ----- Intestazioni tabella voci -----
        $headerRow = $row;
        $headers   = ['N.', 'Codice', 'Descrizione', 'Frequenza', 'Qta/Mesi', 'Importo Unit.', 'Importo'];
        $cols      = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($headers as $ci => $hdr) {
            $sheet->setCellValue($cols[$ci] . $row, $hdr);
        }
        $sheet->getStyle("A{$headerRow}:G{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF343a40']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        // ----- Voci -----
        $freqLabels = [
            'una_tantum'    => 'Una Tantum',
            'mensile'       => 'Mensile',
            'trimestrale'   => 'Trimestrale',
            'semestrale'    => 'Semestrale',
            'annuale'       => 'Annuale',
            'a_prestazione' => 'A Prestazione',
        ];

        $firstVocRow = $row;
        foreach ($voci as $i => $voce) {
            $freq    = $voce['frequenza'] ?? '';
            $freqLbl = $freqLabels[$freq] ?? $freq;
            $qtaMesi = $freq === 'mensile'
                ? ($voce['mesi'] ?? 12) . ' mesi'
                : 'x' . number_format((float)($voce['quantita'] ?? 1), 0);

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $voce['codice'] ?? '');
            $sheet->setCellValue("C{$row}", $voce['descrizione'] ?? '');
            $sheet->setCellValue("D{$row}", $freqLbl);
            $sheet->setCellValue("E{$row}", $qtaMesi);
            $sheet->setCellValue("F{$row}", (float)($voce['importo_unitario'] ?? 0));
            $sheet->setCellValue("G{$row}", (float)($voce['importo_riga'] ?? 0));

            // Formattazione numerica
            $numFmt = '#.##0,00 €';
            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode($numFmt);
            $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode($numFmt);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Zebra striping
            if ($i % 2 === 1) {
                $sheet->getStyle("A{$row}:G{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8F9FA');
            }

            $row++;
        }
        $lastVocRow = $row - 1;

        // Bordi tabella
        if ($firstVocRow <= $lastVocRow) {
            $sheet->getStyle("A{$headerRow}:G{$lastVocRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFCCCCCC'],
                    ],
                ],
            ]);
        }

        $row++; // riga vuota

        // ----- Riepilogo importi -----
        $riepilogoItems = [
            ['Imponibile lordo',   $lordo,  false],
        ];
        if ((float)($preventivo['sconto1'] ?? 0) > 0) {
            $riepilogoItems[] = ['Sconto 1 (' . number_format((float)$preventivo['sconto1'], 2) . '%)', -$sc1Imp, false];
        }
        if ((float)($preventivo['sconto2'] ?? 0) > 0) {
            $riepilogoItems[] = ['Sconto 2 (' . number_format((float)$preventivo['sconto2'], 2) . '%)', -$sc2Imp, false];
        }
        $riepilogoItems[] = ['Imponibile netto',  $netto,   false];
        $riepilogoItems[] = ['IVA ' . number_format((float)($preventivo['iva_perc'] ?? ALIQUOTA_IVA), 0) . '%', $ivaImp, false];
        $riepilogoItems[] = ['TOTALE',             $totale,  true];

        foreach ($riepilogoItems as [$label, $importo, $isBold]) {
            $sheet->setCellValue("E{$row}", $label);
            $sheet->setCellValue("F{$row}", '');
            $sheet->setCellValue("G{$row}", (float)$importo);
            $sheet->getStyle("E{$row}")->getFont()->setBold($isBold);
            $sheet->getStyle("G{$row}")->getFont()->setBold($isBold);
            $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('#.##0,00 €');
            if ($isBold) {
                $sheet->getStyle("E{$row}:G{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFd7e8f8');
            }
            $row++;
        }

        $row++; // riga vuota

        // ----- Note per il cliente -----
        if (!empty($preventivo['note_cliente'])) {
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->setCellValue("A{$row}", 'Note:');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $row++;
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->setCellValue("A{$row}", $preventivo['note_cliente']);
            $sheet->getStyle("A{$row}")->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($row)->setRowHeight(60);
            $row++;
        }

        $row++;

        // ----- Disclaimer -----
        $sheet->mergeCells("A{$row}:G{$row}");
        $disclaimer = 'I presenti onorari sono basati sugli onorari consigliati ANC. '
                    . 'Agli importi si applica la scontistica indicata nel mandato professionale.';
        $sheet->setCellValue("A{$row}", $disclaimer);
        $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(8);
        $sheet->getStyle("A{$row}")->getFont()->getColor()->setARGB('FF888888');
        $sheet->getStyle("A{$row}")->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(30);
        $row++;

        // ----- Output -----
        $filename = 'Preventivo_' . ($preventivo['numero'] ?? $id) . '.ods';

        header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer = new Ods($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // ----------------------------------------------------------------
    // METODI PRIVATI
    // ----------------------------------------------------------------

    /**
     * Restituisce l'HTML completo del preventivo per mPDF.
     */
    private function getHtmlTemplate(array $preventivo, array $voci, array $studio): string
    {
        [$lordo, $sc1Imp, $sc2Imp, $netto, $ivaImp, $totale] = $this->calcolaTotaliPreventivo($preventivo, $voci);

        $sc1Perc = (float)($preventivo['sconto1'] ?? 0);
        $sc2Perc = (float)($preventivo['sconto2'] ?? 0);
        $ivaPerc = (float)($preventivo['iva_perc'] ?? ALIQUOTA_IVA);

        $freqLabels = [
            'una_tantum'    => 'Una Tantum',
            'mensile'       => 'Mensile',
            'trimestrale'   => 'Trimestrale',
            'semestrale'    => 'Semestrale',
            'annuale'       => 'Annuale',
            'a_prestazione' => 'A Prestazione',
        ];

        $noteCliente = !empty($preventivo['note_cliente'])
            ? '<div style="background:#f8f9fa;border-left:4px solid #ffc107;padding:10px 14px;margin-bottom:16px;font-size:12px;">'
              . '<strong>Note:</strong><br>'
              . nl2br(htmlspecialchars($preventivo['note_cliente'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'))
              . '</div>'
            : '';

        // Righe voci
        $vocHtml = '';
        foreach ($voci as $i => $voce) {
            $freq    = $voce['frequenza'] ?? '';
            $freqLbl = $freqLabels[$freq] ?? $freq;
            $qtaMesi = $freq === 'mensile'
                ? ($voce['mesi'] ?? 12) . ' mesi'
                : 'x' . number_format((float)($voce['quantita'] ?? 1), 0);
            $bg = $i % 2 === 1 ? '#f8f9fa' : '#ffffff';

            $noteVoce = !empty($voce['note'])
                ? '<br><small style="color:#888;font-style:italic;">' . htmlspecialchars($voce['note'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</small>'
                : '';

            $vocHtml .= '<tr style="background:' . $bg . ';">'
                . '<td style="text-align:center;color:#666;">' . ($i + 1) . '</td>'
                . '<td style="font-family:monospace;font-size:10px;">' . $this->he($voce['codice'] ?? '') . '</td>'
                . '<td>' . $this->he($voce['descrizione'] ?? '') . $noteVoce . '</td>'
                . '<td style="text-align:center;">' . $this->he($freqLbl) . '</td>'
                . '<td style="text-align:center;">' . $this->he($qtaMesi) . '</td>'
                . '<td style="text-align:right;">' . $this->fmtEuro($voce['importo_unitario'] ?? 0) . '</td>'
                . '<td style="text-align:right;font-weight:bold;">' . $this->fmtEuro($voce['importo_riga'] ?? 0) . '</td>'
                . '</tr>';
        }

        // Righe riepilogo
        $riepilogoHtml = '<tr>'
            . '<td style="text-align:right;padding:4px 8px;color:#555;" colspan="6">Imponibile lordo</td>'
            . '<td style="text-align:right;padding:4px 8px;">' . $this->fmtEuro($lordo) . '</td>'
            . '</tr>';

        if ($sc1Perc > 0) {
            $riepilogoHtml .= '<tr style="color:#c0392b;">'
                . '<td style="text-align:right;padding:4px 8px;" colspan="6">Sconto 1 (' . number_format($sc1Perc, 2) . '%)</td>'
                . '<td style="text-align:right;padding:4px 8px;">- ' . $this->fmtEuro($sc1Imp) . '</td>'
                . '</tr>';
        }
        if ($sc2Perc > 0) {
            $riepilogoHtml .= '<tr style="color:#c0392b;">'
                . '<td style="text-align:right;padding:4px 8px;" colspan="6">Sconto aggiuntivo (' . number_format($sc2Perc, 2) . '%)</td>'
                . '<td style="text-align:right;padding:4px 8px;">- ' . $this->fmtEuro($sc2Imp) . '</td>'
                . '</tr>';
        }

        $riepilogoHtml .= '<tr style="border-top:1px solid #dee2e6;">'
            . '<td style="text-align:right;padding:4px 8px;" colspan="6">Imponibile netto</td>'
            . '<td style="text-align:right;padding:4px 8px;">' . $this->fmtEuro($netto) . '</td>'
            . '</tr>'
            . '<tr>'
            . '<td style="text-align:right;padding:4px 8px;" colspan="6">IVA ' . number_format($ivaPerc, 0) . '%</td>'
            . '<td style="text-align:right;padding:4px 8px;">' . $this->fmtEuro($ivaImp) . '</td>'
            . '</tr>'
            . '<tr style="background:#d7e8f8;border-top:2px solid #aaa;">'
            . '<td style="text-align:right;padding:6px 8px;font-weight:bold;font-size:13px;" colspan="6">TOTALE</td>'
            . '<td style="text-align:right;padding:6px 8px;font-weight:bold;font-size:14px;">' . $this->fmtEuro($totale) . '</td>'
            . '</tr>';

        // Indirizzo studio
        $studioIndir = trim(
            ($studio['indirizzo'] ?? '') . ' - ' .
            ($studio['cap'] ?? '') . ' ' .
            ($studio['citta'] ?? '') .
            (!empty($studio['provincia']) ? ' (' . $studio['provincia'] . ')' : '')
        );

        // Indirizzo cliente
        $clienteIndir = trim(
            ($preventivo['cliente_indirizzo'] ?? '') . ' - ' .
            ($preventivo['cliente_cap'] ?? '') . ' ' .
            ($preventivo['cliente_citta'] ?? '') .
            (!empty($preventivo['cliente_prov']) ? ' (' . $preventivo['cliente_prov'] . ')' : '')
        );

        $dataScad = !empty($preventivo['data_scadenza'])
            ? ' &mdash; valido fino al ' . $this->fmtData($preventivo['data_scadenza'])
            : '';

        $oggettoHtml = !empty($preventivo['titolo'])
            ? '<div style="background:#f0f0f0;border-radius:4px;padding:8px 14px;margin-bottom:16px;font-size:12px;">'
              . '<strong>Oggetto:</strong> ' . $this->he($preventivo['titolo'])
              . '</div>'
            : '';

        $profNome = trim($preventivo['professionista_nome'] ?? '');

        $html = '<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; margin: 0; padding: 0; }
  table { width: 100%; border-collapse: collapse; }
  th, td { padding: 5px 8px; vertical-align: top; }
  .header-table td { border: none; }
  .studio-block { width: 50%; }
  .cliente-block { width: 50%; border-left: 3px solid #dee2e6; padding-left: 16px; }
  h1 { font-size: 18px; text-align: center; margin: 12px 0 4px; text-transform: uppercase; letter-spacing: .05em; }
  .subtitle { text-align: center; font-size: 13px; margin-bottom: 4px; }
  .anno-rif { text-align: center; color: #666; font-size: 10px; margin-bottom: 16px; }
  .voci-table th { background: #343a40; color: #fff; text-align: center; font-size: 10px; padding: 6px 8px; }
  .voci-table td { border: 1px solid #dee2e6; font-size: 10px; padding: 4px 8px; }
  .disclaimer { font-size: 9px; color: #888; font-style: italic; border-left: 3px solid #ffc107; padding: 6px 10px; margin-top: 14px; background: #fffdf0; }
  .footer-table td { border: none; font-size: 10px; color: #555; }
  hr { border: none; border-top: 1px solid #ccc; margin: 10px 0; }
  .firma-line { border-top: 1px solid #aaa; display: inline-block; width: 160px; margin-top: 30px; text-align: center; font-size: 9px; color: #888; }
</style>
</head>
<body>

<!-- INTESTAZIONE -->
<table class="header-table" style="margin-bottom:12px;">
  <tr>
    <td class="studio-block">
      <strong style="font-size:14px;">' . $this->he($studio['ragione_sociale'] ?? '') . '</strong><br>
      ' . $this->he($studioIndir) . '<br>
      ' . (!empty($studio['partita_iva']) ? 'P.IVA: <strong>' . $this->he($studio['partita_iva']) . '</strong><br>' : '') . '
      ' . (!empty($studio['telefono']) ? 'Tel: ' . $this->he($studio['telefono']) . '<br>' : '') . '
      ' . (!empty($studio['pec']) ? 'PEC: ' . $this->he($studio['pec']) . '<br>' : '') . '
      ' . (!empty($studio['ordine_professionale']) ? '<small style="color:#666;">Iscritto all\'' . $this->he($studio['ordine_professionale']) . (!empty($studio['n_iscrizione_ordine']) ? ' n. ' . $this->he($studio['n_iscrizione_ordine']) : '') . '</small>' : '') . '
    </td>
    <td class="cliente-block">
      <span style="font-size:9px;color:#666;text-transform:uppercase;">Spettabile</span><br>
      <strong style="font-size:13px;">' . $this->he($preventivo['cliente_nome'] ?? '') . '</strong><br>
      ' . ($clienteIndir !== ' -  ' ? $this->he($clienteIndir) . '<br>' : '') . '
      ' . (!empty($preventivo['cliente_piva']) ? 'P.IVA: <strong>' . $this->he($preventivo['cliente_piva']) . '</strong><br>' : '') . '
      ' . (!empty($preventivo['cliente_cf']) ? 'C.F.: ' . $this->he($preventivo['cliente_cf']) . '<br>' : '') . '
      ' . (!empty($preventivo['cliente_pec']) ? 'PEC: ' . $this->he($preventivo['cliente_pec']) . '<br>' : '') . '
    </td>
  </tr>
</table>

<hr>

<h1>Preventivo Onorari</h1>
<div class="subtitle">
  <strong>N. ' . $this->he($preventivo['numero'] ?? '') . '</strong>
  del ' . $this->fmtData($preventivo['data_preventivo'] ?? null) . $dataScad . '
</div>
' . (!empty($preventivo['anno_riferimento']) ? '<div class="anno-rif">Anno di riferimento: ' . (int)$preventivo['anno_riferimento'] . '</div>' : '<div class="anno-rif">&nbsp;</div>') . '

' . $oggettoHtml . '

<!-- TABELLA VOCI -->
<table class="voci-table" style="margin-bottom:0;">
  <thead>
    <tr>
      <th style="width:30px;">N.</th>
      <th style="width:70px;">Codice</th>
      <th>Descrizione</th>
      <th style="width:90px;">Frequenza</th>
      <th style="width:65px;">Qta/Mesi</th>
      <th style="width:90px;text-align:right;">Imp. Unit.</th>
      <th style="width:90px;text-align:right;">Importo</th>
    </tr>
  </thead>
  <tbody>
    ' . ($vocHtml ?: '<tr><td colspan="7" style="text-align:center;color:#888;padding:12px;">Nessuna voce presente.</td></tr>') . '
  </tbody>
</table>

<!-- RIEPILOGO -->
<table style="margin-top:0;">
  <tbody>
    ' . $riepilogoHtml . '
  </tbody>
</table>

<br>

' . $noteCliente . '

<div class="disclaimer">
  I presenti onorari sono basati sugli onorari consigliati ANC (Associazione Nazionale Commercialisti).
  Agli importi si applica la scontistica indicata nel mandato professionale.
  Il presente preventivo ha validit&agrave; indicata in calce alla data di emissione,
  salvo variazioni del listino o delle condizioni di mandato.
</div>

<hr style="margin-top:14px;">

<!-- FOOTER -->
<table class="footer-table" style="margin-top:10px;">
  <tr>
    <td style="width:55%;">
      <strong>' . $this->he($studio['ragione_sociale'] ?? '') . '</strong><br>
      ' . $this->he($studioIndir) . '<br>
      ' . (!empty($studio['partita_iva']) ? 'P.IVA: ' . $this->he($studio['partita_iva']) : '') . '
      ' . (!empty($studio['email']) ? '<br>' . $this->he($studio['email']) : '') . '
    </td>
    <td style="width:45%;text-align:right;">
      ' . ($profNome ? '<span style="font-size:10px;color:#555;">Il Professionista<br><strong>' . $this->he($profNome) . '</strong></span><br><br>' : '') . '
      <div class="firma-line">Firma</div>
      &nbsp;&nbsp;&nbsp;
      <div class="firma-line">Data e Timbro</div>
    </td>
  </tr>
</table>

<div style="text-align:center;font-size:8px;color:#aaa;margin-top:20px;">
  Preventivo n. ' . $this->he($preventivo['numero'] ?? '') . ' &mdash; emesso il ' . $this->fmtData($preventivo['data_preventivo'] ?? null) . '
</div>

</body>
</html>';

        return $html;
    }

    /**
     * Calcola imponibile lordo, sconti, netto, IVA e totale.
     * Restituisce array: [lordo, sc1Imp, sc2Imp, netto, ivaImp, totale]
     *
     * @return array{float, float, float, float, float, float}
     */
    private function calcolaTotaliPreventivo(array $preventivo, array $voci): array
    {
        // Usa i valori pre-calcolati dal DB se disponibili
        if (isset($preventivo['totale']) && $preventivo['totale'] !== null) {
            $lordo  = (float)($preventivo['imponibile_lordo'] ?? array_sum(array_column($voci, 'importo_riga')));
            $netto  = (float)($preventivo['imponibile_netto'] ?? 0);
            $ivaImp = (float)($preventivo['importo_iva'] ?? 0);
            $totale = (float)$preventivo['totale'];

            $sc1Perc = (float)($preventivo['sconto1'] ?? 0);
            $sc2Perc = (float)($preventivo['sconto2'] ?? 0);
            $sc1Imp  = round($lordo * $sc1Perc / 100, 2);
            $dopoSc1 = $lordo - $sc1Imp;
            $sc2Imp  = round($dopoSc1 * $sc2Perc / 100, 2);

            return [$lordo, $sc1Imp, $sc2Imp, $netto, $ivaImp, $totale];
        }

        // Calcola da zero
        $lordo   = round(array_sum(array_column($voci, 'importo_riga')), 2);
        $sc1Perc = (float)($preventivo['sconto1'] ?? 0);
        $sc2Perc = (float)($preventivo['sconto2'] ?? 0);
        $ivaPerc = (float)($preventivo['iva_perc'] ?? ALIQUOTA_IVA);

        $sc1Imp  = round($lordo * $sc1Perc / 100, 2);
        $dopoSc1 = $lordo - $sc1Imp;
        $sc2Imp  = round($dopoSc1 * $sc2Perc / 100, 2);
        $netto   = round($dopoSc1 - $sc2Imp, 2);
        $ivaImp  = round($netto * $ivaPerc / 100, 2);
        $totale  = round($netto + $ivaImp, 2);

        return [$lordo, $sc1Imp, $sc2Imp, $netto, $ivaImp, $totale];
    }

    /**
     * Escapa HTML.
     */
    private function he(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Formatta una data Y-m-d in d/m/Y.
     */
    private function fmtData(?string $d): string
    {
        if (!$d) return '—';
        try {
            return (new \DateTime($d))->format('d/m/Y');
        } catch (\Exception $e) {
            return $d;
        }
    }

    /**
     * Formatta un numero come valuta euro.
     */
    private function fmtEuro(float|int|string $n): string
    {
        return '&euro; ' . number_format((float)$n, 2, ',', '.');
    }
}
