<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Order</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <h1>Data Order</h1>
    <button id="getOrders">Get Orders</button>
    <div id="orderList"></div>

    <script>
        $(document).ready(function() {
            $('#getOrders').click(function() {
                // Mengambil token dari localStorage (atau sessionStorage)
                var token = localStorage.getItem('jwtToken'); // Sesuaikan dengan cara Anda menyimpan token

                if (token) {
                    $.ajax({
                        url: 'http://localhost:82/pyid-asics/api/get_orders', // Ganti dengan URL endpoint yang benar
                        type: 'GET',
                        headers: {
                            'Authorization': token
                        },
                        dataType:'JSON',
                        success: function(response) {
                            if (response.status === 'success') {
                                var orders = response.data;
                                var orderList = $('#orderList');
                                orderList.empty();
                                $.each(orders, function(index, order) {
                                    orderList.append('<p>Order ID: ' + order.order_id + ', Product: ' + order.product + ', Quantity: ' + order.quantity + ', Price: ' + order.price + '</p>');
                                });
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Error: ' + xhr.responseText);
                        }
                    });
                } else {
                    alert('Token not found. Please log in.');
                }
            });
        });
    </script>
</body>

</html>