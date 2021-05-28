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
use App\Traits\Reports\Stock\ProductLotStockTrait;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ProductLotStockExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    use Exportable;
    use ProductLotStockTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        return $this->allProductLotStock($this->request);
    }

    public function map($record): array
    {
        return [
            $record->lotNumber ? $record->lotNumber : 'Unassigned',
            $record->name,
            $record->compoundUnit ? $record->compoundStock : '',
            $record->compoundUnit ? $record->compoundUnit . ' (' . $record->packing / 100 . ')' : '',
            number_format($record->currentStock / 100, 2),
            $record->productUnit,
        ];
    }

    public function headings(): array
    {
        return [
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
            'A' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }
}
