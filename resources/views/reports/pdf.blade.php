<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Ordens de Serviço</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px;}
        th, td { border: 1px solid #ccc; padding: 4px; text-align: left;}
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Relatório de Ordens de Serviço</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Status</th>
                <th>Técnico</th>
                <th>Cliente</th>
                <th>Data Criação</th>
                <th>Data Conclusão</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td>{{ $order->technician->name ?? '-' }}</td>
                <td>{{ $order->client->name ?? '-' }}</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td>{{ $order->status == 'concluida' ? $order->updated_at->format('d/m/Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
