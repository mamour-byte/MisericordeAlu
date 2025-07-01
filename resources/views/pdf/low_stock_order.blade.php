<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de commande - Stock bas</title>
    <style>
        @page {
            margin: 40px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 20px;
            margin: 0;
        }

        .shop-info, .footer {
            margin-bottom: 20px;
        }

        .shop-info p {
            margin: 0;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }

        .signature {
            margin-top: 60px;
            text-align: left;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bon de commande automatique</h1>
        <p><em>Produits en dessous du seuil de stock</em></p>
    </header>

    <div class="shop-info">
        <p><strong>Magasin :</strong> {{ $shop->name }}</p>
        <p><strong>Date :</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Produit</th>
                <th>Catégorie</th>
                <th>Quantité à commander</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td>{{ max($product->stock_min * 2 - $product->calculated_stock, 1) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <p><strong>Responsable :</strong> _____________________________</p>
    </div>

    <div class="footer">
        <p>Document généré automatiquement par le système le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>
