<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceOrder;
use App\Models\User;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServiceOrdersExport;

class ReportController extends Controller
{
    public function form()
    {
        $tecnicos = User::where('role', 'technician')->get();
        return view('reports.form', compact('tecnicos'));
    }

    public function generatePdf(Request $request)
    {
        $orders = $this->filterOrders($request);
        $pdf = PDF::loadView('reports.pdf', compact('orders'));
        return $pdf->download('relatorio-os.pdf');
    }

    public function generateExcel(Request $request)
    {
        $orders = $this->filterOrders($request);
        return Excel::download(new ServiceOrdersExport($orders), 'relatorio-os.xlsx');
    }

    private function filterOrders(Request $request)
    {
        $query = ServiceOrder::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }
        return $query->with(['technician', 'client'])->orderBy('created_at', 'desc')->get();
    }
}
