<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ServiceOrdersExport implements FromCollection, WithHeadings
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        return $this->orders->map(function($order) {
            return [
                $order->id,
                $order->status,
                $order->technician->name ?? '-',
                $order->client->name ?? '-',
                $order->created_at->format('d/m/Y'),
                $order->status == 'concluida' ? $order->updated_at->format('d/m/Y') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID', 'Status', 'Técnico', 'Cliente', 'Data Criação', 'Data Conclusão'
        ];
    }
}
