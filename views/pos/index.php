<h1 class="h3 mb-3">Point of Sale</h1>
<form method="post" action="<?= url('pos_checkout') ?>" id="pos-form">
<?= csrf_field() ?>
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Price type</label>
                    <select name="price_type" id="price-type" class="form-select" onchange="showCart()">
                        <option>Retail</option>
                        <option>Wholesale</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Payment</label>
                    <select name="payment_method" class="form-select">
                        <option>Cash</option>
                        <option>GCash</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Customer</label>
                    <input name="customer_name" class="form-control" placeholder="Walk-in">
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-7">
                    <select id="product-select" class="form-select">
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['product_id'] ?>"
                                    data-name="<?= e($p['product_name']) ?>"
                                    data-retail="<?= $p['retail_price'] ?>"
                                    data-wholesale="<?= $p['wholesale_price'] ?>"
                                    data-stock="<?= $p['stock'] ?>">
                                <?= e($p['product_name']) ?> (<?= $p['stock'] ?> left)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" id="qty-input" min="1" value="1" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-secondary w-100" onclick="addToCart()">Add</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="table-responsive">
            <table id="cart-table" class="table align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Product</th><th>Qty</th><th class="text-end">Unit price</th><th class="text-end">Subtotal</th><th></th></tr>
                </thead>
                <tbody id="cart-body"></tbody>
                <tfoot>
                    <tr><th colspan="3" class="text-end">Total</th><th id="cart-total" class="text-end">0.00</th><th></th></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <input type="hidden" name="cart" id="cart-input">
    <button type="submit" class="btn btn-success btn-lg">Checkout</button>
</form>

<script>
var cart = [];

function getPrice(item) {
    if (document.getElementById('price-type').value == 'Wholesale') {
        return item.wholesale;
    }
    return item.retail;
}

function showCart() {
    var body = document.getElementById('cart-body');
    var total = 0;
    body.innerHTML = '';

    for (var i = 0; i < cart.length; i++) {
        var item = cart[i];
        var price = getPrice(item);
        var subtotal = price * item.quantity;
        total = total + subtotal;

        var row = body.insertRow();
        row.insertCell().textContent = item.name;
        row.insertCell().textContent = item.quantity;

        var priceCell = row.insertCell();
        priceCell.className = 'text-end';
        priceCell.textContent = price.toFixed(2);

        var subtotalCell = row.insertCell();
        subtotalCell.className = 'text-end';
        subtotalCell.textContent = subtotal.toFixed(2);

        var buttonCell = row.insertCell();
        buttonCell.className = 'text-end';
        buttonCell.innerHTML = '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(' + i + ')">Remove</button>';
    }

    document.getElementById('cart-total').textContent = total.toFixed(2);
}

function addToCart() {
    var select = document.getElementById('product-select');
    var option = select.options[select.selectedIndex];
    if (!option) {
        return;
    }

    var qty = parseInt(document.getElementById('qty-input').value);
    var id = parseInt(option.value);
    var stock = parseInt(option.dataset.stock);

    if (!qty || qty < 1) {
        return;
    }

    for (var i = 0; i < cart.length; i++) {
        if (cart[i].product_id == id) {
            if (cart[i].quantity + qty > stock) {
                alert('Only ' + stock + ' in stock.');
                return;
            }
            cart[i].quantity = cart[i].quantity + qty;
            showCart();
            return;
        }
    }

    if (qty > stock) {
        alert('Only ' + stock + ' in stock.');
        return;
    }

    cart.push({
        product_id: id,
        name: option.dataset.name,
        retail: parseFloat(option.dataset.retail),
        wholesale: parseFloat(option.dataset.wholesale),
        quantity: qty
    });
    showCart();
}

function removeItem(index) {
    cart.splice(index, 1);
    showCart();
}

document.getElementById('pos-form').onsubmit = function () {
    if (cart.length == 0) {
        alert('Cart is empty.');
        return false;
    }
    document.getElementById('cart-input').value = JSON.stringify(cart);
};
</script>
