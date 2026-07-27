<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CouponsExport implements FromCollection, WithHeadings
{
    protected $coupons;

    public function __construct($coupons)
    {
        $this->coupons = collect($coupons);
    }

    public function collection()
    {
        return $this->coupons->map(function ($coupon) {
            return [
                'Code'        => $coupon->code,
                'Type'        => $coupon->type ?? '-',
                'Value'       => $coupon->value ?? '-',
                'Expiry Date' => $coupon->expiry_date ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Code', 'Type', 'Discount', 'Expiry Date'];
    }
}

