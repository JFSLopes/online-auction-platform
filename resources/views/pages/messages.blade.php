<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ url('css/messages.css') }}" rel="stylesheet">

    <script type="text/javascript" src={{ url('js/messages.js') }} defer> </script>

    <title>Messages</title>
</head>
<body>
    @include('partials.authTopBar')


    <div class="all-containers">
        <div class="sidebar">
            <h2>Chats</h2>
            <div class="sidebar-list">

                @foreach($messages as $message)
                    @php
                        if($message->senderid == Auth::id()){
                            $sender_id = Auth::id(); //Quem enviou foi o user
                        }else{
                            $sender_id = $message->authid; //Quem recebeu a mensagem foi o user
                        }

                        $auction = App\Models\Auction::find($message->auctionid);
                        $path = '/images/svg/auction.svg';
                        if ($auction != null){
                            $product = $auction->product;

                            if ($product != null){
                                $images = $product->getImages($product->productid);
                                if (!empty($images)){
                                    $path = $images[0];
                                }
                            }
                        }
                    @endphp
                    <div class="sidebar-item" data-auction-id="{{$message->auctionid}}">
                        <img src={{asset($path)}} alt="Lamp" class="sidebar-avatar">
                        <span class="sidebar-name">{{$message->title}}</span>
                        <span class="message-content">{{$message->content}}</span>
                        <span class="message-date">{{$message->sentdate}}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="messages-container">
            <div class="nav-bar">
                <a>Chat</a>
            </div>
            <div class="messages-area"></div>
            <div class="sender-area">
                <form action="{{ route('sendMessage', ['userId' => Auth::id()]) }}" method="POST" class="input-place" id="message-send-form">
                    @csrf
                    <input name="message" placeholder="Send a message." class="send-input" type="text" required>
                    <input type="hidden" name="auctionId" value="">
                    <button type="submit" class="send">
                        <img class="send-icon" src="{{ asset('images/png/send.png') }}" alt="Send">
                    </button>
                </form>
            </div>            
        </div>
    </div>
  @include('partials.footer')
</body>
</html>