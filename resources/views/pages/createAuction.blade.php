<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Create Auction Page') }}</title>

        <!-- Styles -->
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        
        <link href="{{ url('css/createAuction.css') }}" rel="stylesheet">
    
        <script type="text/javascript" src={{ url('js/createAuction.js') }} defer> </script>
    </head>
    <body>
        <main>
            @include('partials.authTopBar')

            <section class="main-information" id="main-information">
                <form action="{{ route('createAuction', ['userId' => $userId]) }}" method = "POST" id = "create-auction-form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required>
                        <button class="help-button">
                            <i class="fa-solid fa-question"></i>
                            <span class="tooltip">Provide a clear and descriptive title for your auction.</span>
                        </button>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required></textarea>
                        <button class="help-button">
                            <i class="fa-solid fa-question"></i>
                            <span class="tooltip">Describe the item in detail, including its condition and features.</span>
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <label for="photos">Upload Photos (Max: 8)</label>
                        <input type="file" id="photos" name="photos[]" accept="image/*" multiple required>
                        <div id="photo-preview" class="photo-preview"></div>
                    </div>                    

                    <div class="form-group">
                        <label for="state">State</label>
                        <select id="state" name="state" required>
                            <option value="Brand New">Brand New</option>
                            <option value="Like New">Like New</option>
                            <option value="Very Good">Very Good</option>
                            <option value="Good">Good</option>
                            <option value="Acceptable">Acceptable</option>
                        </select>
                        <button class="help-button">
                            <i class="fa-solid fa-question"></i>
                            <span class="tooltip">Select the item's condition, such as new, like new, or good.</span>
                        </button>
                    </div>

                    <div class="form-group">
                        <label for="state">Sub-Category</label>
                        <select name="subCategory" id="subCategorySelect">
                            @foreach ( $subCategories as $subCategory )
                                <option value="{{ $subCategory->subcategoryname }}">
                                    {{ $subCategory->subcategoryname }}
                                </option>
                            @endforeach
                        </select>
                        <button class="help-button">
                            <i class="fa-solid fa-question"></i>
                            <span class="tooltip">Select the appropriate sub-category for your item.</span>
                        </button>
                    </div>

                    <div class="form-group">
                        <label for="initValue">Initial Value</label>
                        <input type="number" id="initValue" name="initValue" required>
                        <button class="help-button">
                            <i class="fa-solid fa-question"></i>
                            <span class="tooltip">Set the starting price for your auction.</span>
                        </button>
                    </div>

                    <div class="form-group">
                        <div class="start-date">
                            <label for="start_datetime">Start Date & Time:</label>
                            <input type="datetime-local" id="start_datetime" name="start_datetime" value="{{ old('start_datetime', now()->format('Y-m-d\TH:i')) }}" required>
                            <button class="help-button">
                                <i class="fa-solid fa-question"></i>
                                <span class="tooltip">Set when the auction will start. Default is now.</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="end-date">
                            <label for="end_datetime">End Date & Time:</label>
                            <input type="datetime-local" id="end_datetime" name="end_datetime" value="{{ old('end_datetime', now()->addHours(1)->format('Y-m-d\TH:i')) }}" required>
                            <button class="help-button">
                                <i class="fa-solid fa-question"></i>
                                <span class="tooltip">Set when the auction will end. Ensure enough time for bidders.</span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="create-auction" id = "submit-button">Create Auction</button>
                </form>
                
                
            </section>    
    
            @include('partials.footer')
        </main>
    </body>
</html>