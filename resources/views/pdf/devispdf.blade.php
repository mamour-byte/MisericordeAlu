<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture n° {{ $numero_facture }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        .company, .client {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .company {
            float: left;
        }
        .client {
            float: right;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table th, table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
        }
        table th {
            background-color: #f0f0f0;
        }
        .totals {
            margin-top: 20px;
            width: 100%;
            border: 1px solid #999;
            border-collapse: collapse;
        }
        .totals td {
            padding: 8px;
            border: 1px solid #999;
        }
        .totals .label {
            text-align: right;
            width: 80%;
            background-color: #f9f9f9;
        }
        .totals .value {
            text-align: right;
            width: 20%;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #7f8c8d;
            text-align: center;
        }
        .clearfix {
            clear: both;
            margin-bottom: 20px;
        }
        .tva-status {
            margin-top: 10px;
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>

    <h1>{{$type_document}}</h1>
    <h2>N° {{ $numero_facture }}</h2>

    <div class="company">
        <strong>Misericorde Alu</strong><br>
        CONGO Brazzaville<br>
        Tél: +224 069 692 911 / 044 692 909 <br>
        Tél: 050 375 263 <br>
        Email: contact@misericorde-alu.com
    </div>

    <div class="client">
        {{ $client_nom }}<br>
        {{ $client_adresse }}<br>
        {{ $client_telephone }}<br>
        {{ $client_email }}<br>
    </div>

    <div class="clearfix"></div>

    <p><strong>Date de {{$type_document}} :</strong> {{ $date_facture }}<br>
    <!-- <p><strong>Date d' écheance :</strong> {{ $date_echeance }}<br> -->

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Longeur</th>
                <th>Largeur</th>
                <th>Total (F CFA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($produits as $produit)
                <tr>
                    <td>{{ $produit['nom'] }}</td>
                    <td>{{ $produit['quantity'] }}</td>
                    <td>{{ $produit['longueur'] }}</td>
                    <td>{{ $produit['largeur'] }}</td>
                    <td>{{ number_format($produit['total_ligne'], 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    

    <table class="totals">
        <!-- <tr>
            <td class="label"><strong>Sous-total (HT)</strong></td>
            <td class="value">{{ number_format($subtotal, 2, ',', ' ') }} F CFA</td>
        </tr>
        <tr>
            <td class="label"><strong>TVA ({{ $taxRate }}%)</strong></td>
            <td class="value">{{ number_format($taxAmount, 2, ',', ' ') }} F CFA</td>
        </tr> -->
        <tr>
            <td class="label"><strong>Total</strong></td>
            <td class="value"><strong>{{ number_format($totalAmount, 2, ',', ' ') }} F CFA</strong></td>
        </tr>
    </table>

    <!-- <p class="tva-status">{{ $tva_status }}</p> -->

    <footer>
        <div class="footer">
            Misericorde Alu - CONGO Brazzaville -<br>
            Tél: +224 069 692 911 / 044 692 909  - Email: contact@misericorde-alu.com <br>
            Merci pour votre confiance.
        </div>
    </footer>

</body>
</html>
