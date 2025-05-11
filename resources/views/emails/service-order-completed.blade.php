<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ordem de Serviço Concluída</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #375A63;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }   
        .content {
            padding: 30px;
            color: #333;
        }
        .content h2 {
            margin-top: 0;
            font-size: 18px;
        }
        .details {
            margin-top: 20px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f8f8f8;
        }
        .details th, .details td {
            padding: 12px;
            text-align: left;
            font-size: 14px;
            border: 1px solid #ddd;
        }
        .details th {
            width: 35%;
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .footer {
            background-color: #f0f0f0;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #666;
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

            <div class="details">
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
            </div>

            <p>Agradecemos por confiar em nossos serviços. Caso tenha alguma dúvida ou feedback sobre o serviço realizado, não hesite em nos contatar.</p>

            <p>Atenciosamente,<br>Equipe de Suporte</p>
        </div>
        <div class="footer">
            Este é um e-mail automático. Por favor, não responda diretamente a esta mensagem.
        </div>
    </div>
</body>
</html>
