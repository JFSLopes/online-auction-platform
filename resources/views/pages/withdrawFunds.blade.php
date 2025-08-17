<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link href = "{{ url('css/addFunds.css')}}" rel = "stylesheet">
    <script type="text/javascript" src={{ url('js/withdrawFundsPage.js') }} defer> </script>

    <title>{{ config('app.name', 'Add Funds Page') }}</title>
</head>

<body>
    <div class="insert-funds-container">
        <h1>Withdraw Funds</h1>
        <form action="{{ route('withdrawFunds', ['userId' => Auth::id()]) }}" method="POST" id = "funds-form">
            @csrf
            <!-- Input for amount -->
            <div class="form-group">
                <label for="amount">Amount to Withdraw:</label>
                <input type="number" id="amount" name="amount" placeholder="Enter amount" required>
            </div>
            
            <button type="submit" class="submit-button" id = "submit-button">Withdraw Funds</button>
        </form>
    </div>
</body>
</html>