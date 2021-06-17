<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Traits\ProductTrait;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Shared\Date;
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

class ProductExport implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    use Exportable, ProductTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        return $this->allRecords($this->request);
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->alias,
            $product->unit,
            $product->compound_unit,
            $product->packing / 100,
            $product->remarks,
            Date::dateTimeToExcel(Carbon::parse($product->updated_at)),
            Date::dateTimeToExcel(Carbon::parse($product->created_at))
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Alias',
            'Unit',
            'Compound unit',
            'Packing',
            'Remarks',
            'Updated at',
            'Created at'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle('G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}
