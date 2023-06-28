<?php include 'components/header.php'; ?>

<div class="card-container">
  <?php
  $jsonData = file_get_contents('assets/data.json');
  $shoes = json_decode($jsonData, true);

  $counter = 0;
  foreach ($shoes as $shoe) {    
    echo '<div class="card">';
    echo '<img src="' . $shoe['image'] . '" alt="' . $shoe['name'] . '">';
    echo '<h3>' . $shoe['name'] . '</h3>';
    echo '<div class="card-details">';
    echo '<p class="price">₹ ' . $shoe['price'] . '</p>';
    echo '<button class="add-button" onclick="addToTotal(\'' . $shoe['name'] . '\', ' . $shoe['price'] . ')">+ ADD</button>';
    echo '</div>';
    echo '</div>';
  }
  ?>
</div>
<hr>
<div class="cart">
    <h1>Cart</h1><i class="fa-regular fa-cart-shopping-fast"></i>
    <table id="cart-table" style="display: none;">
    <thead>
        <tr>
        <th>Product Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total Price</th>
        <th>Remove</th>
        </tr>
    </thead>
    <tbody id="cart-table-body"></tbody>
    </table>
    <h3 id="empty-cart-message" style="display: block;">Nothing to show here!!!</h3>
</div>

<hr>

<div class="checkout-detils">
    <h1>Billing</h1>
    <h3><div id="total" class="total">Total: 0T</div></h3>
    <button class="checkout-btn"><a id="checkout-btn" href="https://www.checkout.com/" target=”_blank”>Checkout</a></button>
    <button class="reset-btn" id="reset-btn" onclick="resetTotal()">Empty Cart</button>
</div>


<script>
  var total = 0;
  var cart = {};

  function addToTotal(productName, price) {
    if (cart.hasOwnProperty(productName)) {
      cart[productName].quantity += 1;
      cart[productName].totalPrice += price;
    }
    else {
      cart[productName] = {
        price: price,
        quantity: 1,
        totalPrice: price
      };
    }

    total += price;
    updateTotalDisplay();
    updateItemsTable();
    saveDataToStorage();
  }

  function updateTotalDisplay() {
    document.getElementById("total").textContent = "Total: ₹" + total;

    if (total === 0) {
      document.getElementById("cart-table").style.display = "none";
      document.getElementById("empty-cart-message").style.display = "block";
    }
    else {
      document.getElementById("cart-table").style.display = "table";
      document.getElementById("empty-cart-message").style.display = "none";
    }
  }

  function updateItemsTable() {
    var tableBody = document.getElementById("cart-table-body");
    tableBody.innerHTML = "";

    for (var productName in cart) {
      if (cart.hasOwnProperty(productName)) {
        var item = cart[productName];
        var row = document.createElement("tr");

        var productNameCell = document.createElement("td");
        productNameCell.textContent = productName;
        row.appendChild(productNameCell);

        var priceCell = document.createElement("td");
        priceCell.textContent = "₹" + item.price;
        row.appendChild(priceCell);

        var quantityCell = document.createElement("td");
        quantityCell.textContent = item.quantity;
        row.appendChild(quantityCell);

        var totalPriceCell = document.createElement("td");
        totalPriceCell.textContent = "₹" + item.totalPrice;
        row.appendChild(totalPriceCell);

        var removeCell = document.createElement("td");
        var removeButton = document.createElement("button");
        removeButton.textContent = "Remove";
        removeButton.className = "remove-btn";
        removeButton.onclick = (function(productName) {
          return function() {
            removeFromTotal(productName);
          };
        })(productName);
        removeCell.appendChild(removeButton);
        row.appendChild(removeCell);

        tableBody.appendChild(row);
      }
    }
  }

  function removeFromTotal(productName) {
    if (cart.hasOwnProperty(productName)) {
      var price = cart[productName].price;
      var quantity = cart[productName].quantity;

      if (quantity > 1) {
        cart[productName].quantity -= 1;
        cart[productName].totalPrice -= price;
      }
      else {
        delete cart[productName];
      }

      total -= price;
      updateTotalDisplay();
      updateItemsTable();
      saveDataToStorage();
    }
  }

  function saveDataToStorage() {
    localStorage.setItem("total", total);
    localStorage.setItem("cart", JSON.stringify(cart));
  }

  window.addEventListener("DOMContentLoaded", function() {
    total = parseInt(localStorage.getItem("total")) || 0;
    cart = JSON.parse(localStorage.getItem("cart")) || {};
    updateTotalDisplay();
    updateItemsTable();
  });

  function resetTotal() {
    total = 0;
    cart = {};
    updateTotalDisplay();
    updateItemsTable();
    saveDataToStorage();
  }
</script>

<?php include 'components/footer.php'; ?>
