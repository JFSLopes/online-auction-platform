<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Unblock Requests</title>
    
    <link href="{{ url('css/unblockAdminPage.css') }}" rel="stylesheet">

    <script type="text/javascript" src={{ url('js/unblockRequest.js') }} defer></script>
</head>
<body>
    <div id="back-button">
        <button class="go-back" onclick="window.history.back()">Go Back</button>
    </div>
    <div class="main-content">
        <div class="container unblock-request">
            <div class="header">
                <h1>Unblock Requests</h1>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Reported By</th>
                        <th>User Reported</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usersWithReports as $user_report)
                        <tr>
                            <td>
                                <div class="reported-by">
                                    @php
                                        try {
                                            if ($user_report['report']->userreported != null) {
                                                @endphp
                                                    <div class="user-details">
                                                @php
                                                    $baseDirectory = base_path() . '/public/images/users/';
                                                    $imageFiles1 = glob($baseDirectory . $user_report['report']->userwhoreported . '.*');
                                                    $imagePath1 = "";
                                                    if (!empty($imageFiles1)) {
                                                        $imagePath1 = $imageFiles1[0];
                                                    }
                                                    $fileExtension = pathinfo($imagePath1, PATHINFO_EXTENSION);
                                                    $imageUrl1 = File::exists($imagePath1) ? asset('images/users/' . $user_report['report']->userwhoreported . '.' . $fileExtension) : asset('images/svg/user.svg');
                                                @endphp
                                                    <a href="{{route('profile', ['id' => $user_report['report']->userwhoreported])}}">
                                                        <img src="{{ asset($imageUrl1) }}" alt="User photo">
                                                        <p>{{ $user_report['report']->username }}</p>
                                                    </a>
                                                    </div>
                                                    <p class="reported-date">{{ $user_report['report']->date }}</p>
                                                    <p class="report">{{ $user_report['report']->content }}</p>
                                                    <a href="{{route('auction', ['id' => $user_report['report']->auctionid])}}">Link to auction</a>
                                                @php
                                            } else {
                                                @endphp
                                                    <p>No Report Info</p>
                                                @php
                                            }
                                        } catch (Exception $e) {
                                            @endphp
                                                <p>No Report Info</p>
                                            @php
                                        }
                                    @endphp
                                </div>
                            </td>
                            <td>
                                <div class="reported-user">
                                    <div class="user-details">
                                            @php
                                                $baseDirectory = base_path() . '/public/images/users/';
                                                $imageFiles2 = glob($baseDirectory . $user_report['user']->userid . '.*');
                                                $imagePath2 = "";

                                                if (!empty($imageFiles2)) {
                                                    $imagePath2 = $imageFiles2[0];
                                                }

                                                $fileExtension = pathinfo($imagePath2, PATHINFO_EXTENSION);
                                                $imageUrl2 = File::exists($imagePath2) ? asset('images/users/' . $user_report['user']->userid . '.' . $fileExtension) : asset('images/svg/user.svg');
                                            @endphp
                                            <a href="{{route('profile', ['id' => $user_report['user']->userid])}}">
                                                <img src="{{asset($imageUrl2)}}" alt="User photo">
                                                <p>{{$user_report['user']->username}}</p>
                                            </a>
                                    </div>
                                    <p class="request-date">{{\Carbon\Carbon::parse($user_report['user']->date)}}</p>
                                    <p class="request">{{$user_report['user']->content}}</p>
                                </div>
                            </td>
                            <td>
                                <div class="button-container">
                                    <button class="btn btn-accept" onclick="acceptRequest({{$user_report['user']->userid}})">Accept</button>
                                    <button class="btn btn-deny" onclick="denyRequest({{$user_report['user']->userid}})">Deny</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <div class="container report-request">
            <div class="header">
                <h1>Requests</h1>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Reported By</th>
                        <th>User Reported</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportsWaitingApproval as $report)
                        @if (!$report->isblocked)
                            <tr>
                                <td>
                                    <div class="reported-by">
                                        <div class="user-details">
                                            @php
                                                $baseDirectory = base_path() . '/public/images/users/';
                                                $imageFiles = glob($baseDirectory . $report->userwhoreported . '.*');
                                                $imagePath = "";
                                                if (!empty($imageFiles)) {
                                                    $imagePath = $imageFiles[0];
                                                }
                                                $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
                                                $imageUrl = File::exists($imagePath) ? asset('images/users/' . $report->userwhoreported . '.' . $fileExtension) : asset('images/svg/user.svg');

                                            @endphp
                                            <a href="{{route('profile', ['id' => $report->userwhoreported])}}">
                                                <img src="{{asset($imageUrl)}}" alt="User photo">
                                                <p>{{$report->username}}</p>
                                            </a>
                                        </div>
                                        <p class="request-date">{{$report->date}}</p>
                                        <p class="request">{{$report->content}}</p>
                                        <a href="{{route('auction', ['id' => $report->auctionid])}}">Link to auction</a>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-details">
                                        @php
                                            $baseDirectory = base_path() . '/public/images/users/';
                                            $imageFiles = glob($baseDirectory . $report->userreported . '.*');
                                            $imagePath = "";
                                            if (!empty($imageFiles)) {
                                                $imagePath = $imageFiles[0];
                                            }
                                            
                                            $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
                                            $imageUrl = File::exists($imagePath) ? asset('images/users/' . $report->userreported . '.' . $fileExtension) : asset('images/svg/user.svg');
                                        @endphp
                                        <a href="{{route('profile', ['id' => $report->userreported])}}">
                                            <img src="{{asset($imageUrl)}}" alt="User photo">
                                            <p>{{App\Models\Users::find($report->userreported)->username}}</p>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="button-container">
                                        <button class="btn btn-accept" onclick="acceptReport({{$report->userreported}})">Accept</button>
                                        <button class="btn btn-deny" onclick="denyReport({{$report->userreported}})">Deny</button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

