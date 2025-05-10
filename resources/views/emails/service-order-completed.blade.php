<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ordem de Serviço Concluída</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #4A6572;
            color: #fff;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 5px 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Ordem de Serviço Concluída</h1>
        </div>
        <div class="content">
            <h2>Olá {{ $serviceOrder->client->name }},</h2>
            <p>Temos o prazer de informar que sua ordem de serviço foi concluída com sucesso!</p>
            
            <h3>Detalhes da Ordem de Serviço:</h3>
            <table>
                <tr>
                    <th>Número da OS:</th>
                    <td>#{{ $serviceOrder->id }}</td>
                </tr>
                <tr>
                    <th>Título:</th>
                    <td>{{ $serviceOrder->title }}</td>
                </tr>
                <tr>
                    <th>Descrição:</th>
                    <td>{{ $serviceOrder->description }}</td>
                </tr>
                <tr>
                    <th>Técnico Responsável:</th>
                    <td>{{ $serviceOrder->technician->name }}</td>
                </tr>
                <tr>
                    <th>Data de Conclusão:</th>
                    <td>{{ $serviceOrder->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
            
            <p>Agradecemos por confiar em nossos serviços. Caso tenha alguma dúvida ou feedback sobre o serviço realizado, não hesite em nos contatar.</p>
            
            <p>Atenciosamente,<br>
            Equipe de Suporte</p>
        </div>
        <div class="footer">
            <p>Este é um e-mail automático. Por favor, não responda diretamente a esta mensagem.</p>
        </div>
    </div>
</body>
</html>
