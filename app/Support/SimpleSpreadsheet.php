<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class SimpleSpreadsheet
{
    public function readUploadedFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: '');

        return match ($extension) {
            'csv' => $this->readCsv(file_get_contents($file->getRealPath()) ?: ''),
            'xlsx' => $this->readXlsx($file->getRealPath()),
            'xls' => $this->readLegacySpreadsheet($file->getRealPath()),
            default => throw new RuntimeException('Unsupported file type.'),
        };
    }

    public function readCsv(string $contents): array
    {
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $contents);
        rewind($handle);

        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            if ($row === [null] || $row === false) {
                continue;
            }

            $rows[] = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $row);
        }

        fclose($handle);

        if ($rows === []) {
            return ['headers' => [], 'rows' => []];
        }

        $headers = array_shift($rows);

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    public function readRemoteCsv(string $contents): array
    {
        return $this->readCsv($contents);
    }

    public function buildXlsx(array $headers, array $rows): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('ZipArchive is not available.');
        }

        $allRows = array_merge([$headers], $rows);
        $sharedStrings = [];
        $sharedStringIndex = [];

        foreach ($allRows as $row) {
            foreach ($row as $value) {
                if ($this->isNumeric($value)) {
                    continue;
                }

                $string = (string) $value;
                if (! array_key_exists($string, $sharedStringIndex)) {
                    $sharedStringIndex[$string] = count($sharedStrings);
                    $sharedStrings[] = $string;
                }
            }
        }

        $sheetRows = [];
        foreach ($allRows as $rowIndex => $row) {
            $cells = [];

            foreach (array_values($row) as $columnIndex => $value) {
                $cellRef = $this->columnLetter($columnIndex + 1) . ($rowIndex + 1);

                if ($this->isNumeric($value)) {
                    $cells[] = '<c r="' . $cellRef . '"><v>' . $this->escapeXml((string) $value) . '</v></c>';
                } else {
                    $stringIndex = $sharedStringIndex[(string) $value] ?? 0;
                    $cells[] = '<c r="' . $cellRef . '" t="s"><v>' . $stringIndex . '</v></c>';
                }
            }

            $sheetRows[] = '<row r="' . ($rowIndex + 1) . '">' . implode('', $cells) . '</row>';
        }

        $sharedStringsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">'
            . implode('', array_map(fn ($string) => '<si><t>' . $this->escapeXml($string) . '</t></si>', $sharedStrings))
            . '</sst>';

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'
            . implode('', $sheetRows)
            . '</sheetData></worksheet>';

        $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets></workbook>';

        $workbookRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';

        $rootRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';

        $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
            . '<borders count="1"><border/></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
            . '</styleSheet>';

        $contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';

        $tempFile = tempnam(sys_get_temp_dir(), 'crm-xlsx-');
        $zip = new ZipArchive();

        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create spreadsheet file.');
        }

        $zip->addFromString('[Content_Types].xml', $contentTypesXml);
        $zip->addFromString('_rels/.rels', $rootRelsXml);
        $zip->addFromString('xl/workbook.xml', $workbookXml);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRelsXml);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->addFromString('xl/sharedStrings.xml', $sharedStringsXml);
        $zip->addFromString('xl/styles.xml', $stylesXml);
        $zip->close();

        $binary = file_get_contents($tempFile) ?: '';
        @unlink($tempFile);

        return $binary;
    }

    protected function readXlsx(string $path): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('ZipArchive is not available.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Unable to open XLSX file.');
        }

        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = simplexml_load_string($sharedStringsXml);
            if ($xml instanceof SimpleXMLElement) {
                $xml->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                foreach ($xml->xpath('//main:si') ?: [] as $item) {
                    $sharedStrings[] = trim((string) implode('', $item->xpath('.//main:t') ?: []));
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new RuntimeException('Worksheet data not found in XLSX file.');
        }

        $xml = simplexml_load_string($sheetXml);
        if (! $xml instanceof SimpleXMLElement) {
            throw new RuntimeException('Unable to parse XLSX worksheet.');
        }

        $xml->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rows = [];

        foreach ($xml->xpath('//main:sheetData/main:row') ?: [] as $row) {
            $parsedRow = [];

            foreach ($row->xpath('./main:c') ?: [] as $cell) {
                $ref = (string) $cell['r'];
                $columnIndex = $this->columnIndexFromReference($ref);
                $type = (string) $cell['t'];
                $value = (string) ($cell->v ?? '');

                if ($type === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) ($cell->is->t ?? '');
                }

                $parsedRow[$columnIndex] = trim((string) $value);
            }

            if ($parsedRow === []) {
                continue;
            }

            ksort($parsedRow);
            $rows[] = array_values($parsedRow);
        }

        if ($rows === []) {
            return ['headers' => [], 'rows' => []];
        }

        $headers = array_shift($rows);

        return ['headers' => $headers, 'rows' => $rows];
    }

    protected function readLegacySpreadsheet(string $path): array
    {
        $contents = file_get_contents($path) ?: '';

        if (str_contains($contents, '<table')) {
            return $this->readHtmlTable($contents);
        }

        if ($this->looksLikeTextTable($contents)) {
            return $this->readDelimitedText($contents);
        }

        throw new RuntimeException('Legacy XLS import is not supported for binary files. Please save the sheet as XLSX or CSV.');
    }

    protected function readHtmlTable(string $contents): array
    {
        preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $contents, $rowMatches);
        $rows = [];

        foreach ($rowMatches[1] as $rowHtml) {
            preg_match_all('/<t[dh][^>]*>(.*?)<\/t[dh]>/is', $rowHtml, $cellMatches);
            if (empty($cellMatches[1])) {
                continue;
            }

            $rows[] = array_map(
                fn ($cell) => trim(html_entity_decode(strip_tags($cell), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                $cellMatches[1]
            );
        }

        if ($rows === []) {
            return ['headers' => [], 'rows' => []];
        }

        $headers = array_shift($rows);

        return ['headers' => $headers, 'rows' => $rows];
    }

    protected function readDelimitedText(string $contents): array
    {
        $lines = preg_split("/\r\n|\n|\r/", trim($contents)) ?: [];
        $rows = array_map(fn ($line) => array_map('trim', preg_split('/\t|,/', $line) ?: []), $lines);

        if ($rows === []) {
            return ['headers' => [], 'rows' => []];
        }

        $headers = array_shift($rows);

        return ['headers' => $headers, 'rows' => $rows];
    }

    protected function looksLikeTextTable(string $contents): bool
    {
        $sample = substr($contents, 0, 256);

        return preg_match('/[\t,\n\r]/', $sample) === 1 && preg_match('/[\x00-\x08]/', $sample) !== 1;
    }

    protected function columnLetter(int $index): string
    {
        $letters = '';

        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $index = intdiv($index - $mod - 1, 26);
        }

        return $letters;
    }

    protected function columnIndexFromReference(string $reference): int
    {
        preg_match('/^[A-Z]+/', strtoupper($reference), $matches);
        $letters = $matches[0] ?? 'A';
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return max(0, $index - 1);
    }

    protected function isNumeric(mixed $value): bool
    {
        return is_numeric($value) && ! preg_match('/^0\d+/', (string) $value);
    }

    protected function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
