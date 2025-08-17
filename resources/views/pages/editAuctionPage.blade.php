<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href = "{{ url('css/editAuction.css')}}" rel = "stylesheet">
        <title>BidIt Edit Auction</title>
    </head>
    <body>
        @include('partials/authTopBar')
        <section class="edit-auction">
            <div class="edit-auction-section">
                <h2>Edit Auction Details</h2>
                <form id="editAuctionForm" action="{{ route('updateAuction', ['auctionId' => $auction->auctionid, 'userId' => Auth::id()]) }}" method="POST">
                    @csrf
                    <!-- Title -->
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" value="{{$product->title}}" required>
                        <span class="help-icon">
                            <i class="fa-solid fa-question"></i>
                            <span class="tooltip">Provide a clear and descriptive title for your auction.</span>
                        </span>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="4" required>{{$product->description}}</textarea>
                        <span class="help-icon">
                            <i class="fa-solid fa-question"></i>
                            <span class="tooltip">Describe the item in detail, including its condition and features.</span>
                        </span>
                    </div>

                    <!-- Subcategory -->
                    <div class="form-group">
                        <label for="subcategory">Subcategory:</label>
                        <select id="subcategory" name="subcategory" required>
                        @foreach($subcats as $subcat)
                            <option value="{{ $subcat->subcategoryname }}">
                                {{ $subcat->subcategoryname }}
                            </option>
                        @endforeach
                        </select>
                        <span class="help-icon">
                            <i class="fa-solid fa-question"></i>
                            <span class="tooltip">Select the appropriate sub-category for your item.</span>
                        </span>
                    </div>

                    <!-- State -->
                    <div class="form-group">
                        <label for="condition">Condition:</label>
                        <select id="condition" name="condition" required>
                        @foreach($conditions as $condition)
                            <option value="{{ $condition }}">{{ $condition }}</option>
                        @endforeach
                        </select>
                        <span class="help-icon">
                            <i class="fa-solid fa-question"></i>
                            <span class="tooltip">Select the item's condition, such as new, like new, or good.</span>
                        </span>
                    </div>

                    <!-- Initial Value -->
                    <div class="form-group">
                        <label for="init_value">Initial Value (€):</label>
                        <input type="number" id="init_value" name="init_value" value="{{$auction->initvalue}}" min="0" step="0.01" required>
                        <span class="help-icon">
                            <i class="fa-solid fa-question"></i>
                            <span class="tooltip">Set the starting price for your auction.</span>
                        </span>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="edit-auction-button">Save Changes</button>
                </form>
            </div>
        </section>
        @include('partials/footer')
    </body>
</html>