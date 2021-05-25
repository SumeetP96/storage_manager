<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Traits\GodownTrait;
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

class GodownExport implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    use Exportable;
    use GodownTrait;

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
            $product->address,
            $product->contact_1,
            $product->contact_2,
            $product->email,
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
            'Address',
            'Contact 1',
            'Contact 2',
            'Email',
            'Remarks',
            'Updated at',
            'Created at'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle('D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}
