<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Etiqueta {{ $encomienda->code }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .shipping-label {
            width: 148mm;
            height: 105mm;
            border: 1px solid #95a5a6;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
        }

        .header {
            background-color: white;
            padding: 2px 2px 2px 2px;
            border-bottom: 1px solid #95a5a6;
            text-align: center;
            position: relative;
        }

        .company-name {
            color: #1b914c;
            font-size: 26px;
            font-weight: bold;
            margin: 0;
            padding: 2px 0 5px 0;
        }


        .priority-mail {
            background-color: #e74c3c;
            color: white;
            padding: 2px 10px 5px 10px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #95a5a6;
        }

        .table-content {
            width: 100%;
            border-bottom: 1px solid #95a5a6;
        }

        .section-title {
            color: #95a5a6;
            margin: 2px 0 5px 0;
            font-weight: bold;
        }

        .details-text {
            margin: 0;
            line-height: 1.4;
            padding-top: 2px;
        }

        .package-info {
            padding: 2px 10px 10px 10px;
            background-color: #f9f9f9;
            border-bottom: 1px solid #95a5a6;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-gap: 10px;
        }

        .package-details {
            color: #95a5a6;
            margin: 2px 0 5px 0;
        }

        .package-value {
            color: #333;
            font-weight: bold;
        }

        .tracking-row {
            padding: 2px 10px 8px 10px;
            background-color: #f5f5f5;
            border-bottom: 1px solid #95a5a6;
        }

        .tracking-number {
            font-weight: bold;
            margin: 2px 0 0 0;
        }

        .barcode {
            text-align: center;
            padding: 2px 0 10px 0;
            background-color: white;
        }

        .barcode img {
            max-height: 80px;
            width: auto;
            margin-top: 2px;
        }

        .handle-with-care {
            color: #95a5a6;
            font-size: 14px;
            margin-right: 5px;
            padding-top: 2px;
        }

        .fragile {
            color: #95a5a6;
            font-size: 14px;
            padding-top: 2px;
        }

        .icon {
            height: 30px;
            width: 30px;
            margin: 2px 5px 0 5px;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="shipping-label">
        <div class="header">
            <h1 class="company-name">BRAYAN BRUSH EIRL</h1>
        </div>
        <div class="priority-mail">{{ $encomienda->sucursal_destinatario->name }}</div>

        <table class="table-content">
            <tr>
                <td>
                    <p class="section-title">PARA:</p>
                    <p class="details-text">
                        {{ $encomienda->destinatario->name }}<br>
                        {{ $encomienda->destinatario->address }}<br>
                        {{ $encomienda->destinatario->code }}
                    </p>

                </td>
                <td>
                    <p class="section-title">DE:</p>
                    <p class="details-text">
                        {{ $encomienda->remitente->name }}<br>
                        {{ $encomienda->remitente->address }}<br>
                        {{ $encomienda->remitente->code }}
                    </p>
                </td>
            </tr>
        </table>


        <div class="package-info">
            <div>
                <p class="package-details">NÚMERO DE LOTE: <span class="package-value">{{ $encomienda->code }}</span>
                </p>
                <p class="package-details">NÚMERO REF: <span class="package-value">{{ $encomienda->id }}</span></p>
            </div>
            <div>
                <p class="package-details">FECHA DE ENVIO: <span class="package-value">{{ date('d/m/Y') }}</span></p>
                <p class="package-details">PESO: <span
                        class="package-value">{{ $encomienda->paquetes->sum('peso') ?? 'N/A' }} kg</span>
                </p>
            </div>
        </div>

        <div class="tracking-row">
            <p class="tracking-number">Seguimiento: {{ $encomienda->code }}</p>
        </div>

        <div class="barcode">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/14/Codigo_QR.svg/100px-Codigo_QR.svg.png?20080824194905"
                alt="Código QR">
        </div>
    </div>
</body>

</html>
