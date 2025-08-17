<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href = "{{ url('css/addFunds.css')}}" rel = "stylesheet">
    <script type="text/javascript" src={{ url('js/addFunds.js') }} defer> </script>

    <title>{{ config('app.name', 'Add Funds Page') }}</title>
</head>

<body>
    <div class="insert-funds-container">
        <h1>Insert Funds</h1>
        <form action="{{ route('addFunds', ['userId' => Auth::id()]) }}" method="POST" id = "funds-form">
            @csrf
            <!-- Input for amount -->
            <div class="form-group">
                <label for="amount">Amount to Deposit:</label>
                <div class="display-side">
                    <input type="number" id="amount" name="amount" placeholder="Enter amount" required>
                    <span class="help-icon">
                        <i class="fa-solid fa-question"></i>
                        <span class="tooltip">Set the amount to deposit.</span>
                    </span>
                </div>
            </div>
            
            <!-- Payment method selection -->
                <div class="form-group">
                        <label for="payment-method">Payment Method:</label>
                    <div class="display-side">
                        <select id="payment-method" name="payment-method" required>
                            <option value="credit-card">Credit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank-transfer">Bank Transfer</option>
                        </select>
                        <span class="help-icon">
                            <i class="fa-solid fa-question"></i>
                            <span class="tooltip">Choose the payment method to use.</span>
                        </span>
                </div>
            </div>
            
            <!-- Submit button -->
            <button type="submit" class="submit-button">Insert Funds</button>
        </form>
    </div>
</body>
</html>