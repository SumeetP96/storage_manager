<?php

namespace App\Exports\Reports\Stock;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use App\Traits\Reports\Stock\LotStockTrait;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class LotStockExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    use Exportable;
    use LotStockTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return $this->allRecords($this->request);
    }

    public function map($record): array
    {
        return [
            $record->lotNumber ? $record->lotNumber : 'Unassigned',
            $record->compoundStock,
            $record->compoundUnit,
            number_format($record->stock / 100, 2),
            $record->unit,
        ];
    }

    public function headings(): array
    {
        return [
            'Lot number',
            'C stock',
            'C Unit',
            'Stock',
            'Unit'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }
}
