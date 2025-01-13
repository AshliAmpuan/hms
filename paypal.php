<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AaQK2c3sE-7O-kRJnsvXZ-toVwFn59XKAN_20kutjnSKCnWDd1ukV20a0kEepSRorskGHvLEFkTVeyZE&currency=PHP&components=buttons&enable-funding=venmo"></script>
</head>
<body>
    <div id="paypal-button-container">

    </div>
    <script>
        paypal.Buttons().render('#paypal-button-container');
    </script>
</body>
</html>