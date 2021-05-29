<?php

namespace App\Exports\Reports\Movement;

use Carbon\Carbon;
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
use App\Traits\Reports\Movement\GodownMovementTrait;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class GodownMovementExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    use Exportable;
    use GodownMovementTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        return $this->allGodownMovement($this->request, $this->request->account_id);
    }

    public function map($record): array
    {
        return [
            Date::dateTimeToExcel(Carbon::parse($record->date)),
            $record->transferType,
            $record->name,
            $record->lotNumber ? $record->lotNumber : '',
            $record->productName,
            $record->compoundUnit ? $record->compoundQuantity / 100 : '',
            $record->compoundUnit ? $record->compoundUnit . ' (' . $record->packing / 100 . ')' : '',
            number_format($record->quantity / 100, 2),
            $record->unit,
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Transfer type',
            'Godown',
            'Lot number',
            'Product',
            'C Quantity',
            'C Unit',
            'Quantity',
            'Unit'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }
}
