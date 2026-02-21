@php
    $prices = $productPrices ?? [];
@endphp
<div id="order-total-preview" class="card mb-3 bg-light">
    <div class="card-body py-3">
        <div class="d-flex align-items-center gap-2">
            <strong class="text-muted">Aperçu du total :</strong>
            <span id="order-total-value" class="fs-5 fw-bold text-primary">0 FCFA</span>
            <small id="order-total-detail" class="text-muted"></small>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const prices = @json($prices);

    function formatNumber(n) {
        return new Intl.NumberFormat('fr-FR').format(n);
    }

    function getProductIds() {
        const select = document.querySelector('select[name*="products"]') 
            || document.querySelector('[data-relation-target="select"]');
        if (!select) return [];
        return Array.from(select.selectedOptions || select.options)
            .filter(opt => opt.selected && opt.value)
            .map(opt => opt.value);
    }

    function getQuantities() {
        const input = document.querySelector('input[name*="quantities"]');
        if (!input || !input.value.trim()) return [];
        return input.value.split(',').map(s => parseFloat(s.trim()) || 0);
    }

    function getRemise() {
        const input = document.querySelector('input[name*="remise"]');
        return input ? (parseFloat(input.value) || 0) : 0;
    }

    function updatePreview() {
        const productIds = getProductIds();
        const quantities = getQuantities();
        const remise = getRemise();

        let subtotal = 0;
        const details = [];

        for (let i = 0; i < productIds.length; i++) {
            const id = productIds[i];
            const qty = quantities[i] ?? 0;
            const price = prices[id] ?? 0;
            subtotal += qty * price;
            if (qty > 0 && price > 0) {
                details.push(`${qty} × ${formatNumber(price)}`);
            }
        }

        const total = Math.max(0, subtotal - remise);

        const valueEl = document.getElementById('order-total-value');
        const detailEl = document.getElementById('order-total-detail');

        if (valueEl) valueEl.textContent = formatNumber(total) + ' FCFA';

        if (detailEl) {
            if (subtotal > 0) {
                let txt = 'Sous-total : ' + formatNumber(subtotal) + ' FCFA';
                if (remise > 0) txt += ' − Remise : ' + formatNumber(remise) + ' FCFA';
                detailEl.textContent = '(' + txt + ')';
            } else {
                detailEl.textContent = '';
            }
        }
    }

    function attachListeners() {
        const productsSelect = document.querySelector('select[name*="products"]') 
            || document.querySelector('[data-relation-target="select"]');
        const quantitiesInput = document.querySelector('input[name*="quantities"]');
        const remiseInput = document.querySelector('input[name*="remise"]');

        [productsSelect, quantitiesInput, remiseInput].forEach(el => {
            if (el && !el.dataset.previewBound) {
                el.dataset.previewBound = '1';
                el.addEventListener('change', updatePreview);
                el.addEventListener('input', updatePreview);
            }
        });

        updatePreview();
    }

    attachListeners();

    document.addEventListener('change', function(e) {
        if (e.target.matches('select[name*="products"], [data-relation-target="select"]')) updatePreview();
    });

    setTimeout(attachListeners, 500);
    setTimeout(attachListeners, 1500);
});
</script>
