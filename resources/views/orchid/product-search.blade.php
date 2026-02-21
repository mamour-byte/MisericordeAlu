<form method="GET" action="" class="mb-4">
    <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Rechercher un produit..." value="{{ request('q') }}">
        <button class="btn btn-primary" type="submit">Rechercher</button>
    </div>
</form>