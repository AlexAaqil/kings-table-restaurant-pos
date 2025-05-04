<!DOCTYPE html>
<html>
<head>
    <title>M-Pesa Payment</title>
</head>
<body>
    <h1>Make a Payment</h1>

    @if ($errors->any())
        <p style="color:red;">{{ $errors->first('payment') }}</p>
    @endif

    @if (session('status'))
        <p style="color: green;">{{ session('status') }}</p>
    @endif

    <form method="POST" action="/pay">
        @csrf
        <label>Phone Number:</label><br>
        <input type="text" name="phone" placeholder="07XXXXXXXX"><br><br>

        <label>Amount:</label><br>
        <input type="number" name="amount"><br><br>

        <button type="submit">Pay with M-Pesa</button>
    </form>
</body>
</html>
