<div class="premium-status-container">
    <div class="premium-info">
        <h3>Premium Status</h3>

        @if ($premium != null)
            <p>Your premium subscription will end on: <strong>{{ $premium->expirydate }}</strong></p>
        @else
            <p>You are not a premium user.</p>
        @endif
        <p>Enjoy exclusive benefits as a premium user:</p>
        <ul>
            <li>Your auction will have priority in searches.</li>
        </ul>
    </div>

    
    <div class="premium-purchase-form">
        <h4>{{$premium ? "Extend Your Premium Subscription" : "Premium Subscription"}}</h4>
        <form action="/user/{{Auth::id()}}/premium" method="POST">
            @csrf
            <label for="premium_duration">Select a plan:</label>
            <select id="premium_duration" name="duration" required>
                <option value="1">1 Month - $9.99</option>
                <option value="3">3 Months - $27.99</option>
                <option value="6">6 Months - $49.99</option>
            </select>
            <button type="submit" class="btn btn-primary">Buy Premium</button>
        </form>
    </div>
</div>