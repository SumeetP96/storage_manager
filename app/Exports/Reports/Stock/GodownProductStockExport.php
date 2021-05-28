<?php

namespace App\Exports\Reports\Stock;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use App\Traits\Reports\Stock\GodownProductStockTrait;

class GodownProductStockExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    use Exportable;
    use GodownProductStockTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        return $this->allGodownProductStock($this->request);
    }

    public function map($record): array
    {
        return [
            $record->godownName,
            $record->productLotNumber,
            $record->productName,
            $record->compoundUnit ? $record->compoundStock / 100 : '',
            $record->compoundUnit ? $record->compoundUnit . ' (' . $record->packing / 100 . ')' : '',
            number_format($record->currentStock / 100, 2),
            $record->productUnit,
        ];
    }

    public function headings(): array
    {
        return [
            'Godown',
            'Lot number',
            'Product',
            'C stock',
            'C Unit',
            'Stock',
            'Unit'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle('B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }
}
