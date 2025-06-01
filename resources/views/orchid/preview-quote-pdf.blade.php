{{-- resources/views/orchid/preview-quote-pdf.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <title>Aperçu du Devis PDF</title>
</head>
<body>
    <h2>Aperçu du Devis PDF</h2>

    <iframe src="{{ route('preview-quote-pdf.pdf', ['id' => request()->id ?? request()->get('OrderId')]) }}" width="90%" height="800px"></iframe>
</body>
</html>
