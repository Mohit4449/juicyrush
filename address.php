<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Save Delivery Address</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #218838;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Enter Delivery Address</h2>
        <form action="process_order.php" method="POST">

            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" required>

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" maxlength="10" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="address">Street Address</label>
            <textarea id="address" name="address" rows="3" required></textarea>

            <label for="city">City</label>
            <input type="text" id="city" name="city" required>

            <label for="state">State</label>
            <input type="text" id="state" name="state" required>

            <label for="pincode">Pincode</label>
            <input type="text" id="pincode" name="pincode" maxlength="6" required>

            <label for="address_type">Address Type</label>
            <select id="address_type" name="address_type" required>
                <option value="Home">Home</option>
                <option value="Office">Office</option>
                <option value="Other">Other</option>
            </select>

            <button type="submit">Save Address</button>
        </form>
    </div>


    <script>
        document.getElementById("addressForm").addEventListener("submit", function(e) {
            e.preventDefault();

            const data = {
                first_name: document.getElementById("first_name").value,
                last_name: document.getElementById("last_name").value,
                address: document.getElementById("address").value,
                city: document.getElementById("city").value,
                state: document.getElementById("state").value,
                email: document.getElementById("email").value,
                address_type: document.getElementById("address_type").value
            };

            fetch("process_order.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(res => alert(res.message || res.error));
        });
    </script>
</body>

</html>