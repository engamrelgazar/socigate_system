<!DOCTYPE html>
<html>
<head>
    <title>Login Details</title>
</head>
<body>
    <p>Dear {{ $details['user_name'] }},</p>
    <p>You have successfully logged in with the following details:</p>
    <ul>
        <li><strong>IP Address:</strong> {{ $details['ip_address'] }}</li>
        <li><strong>Login Time:</strong> {{ $details['login_time'] }}</li>
        <li><strong>Device:</strong> {{ $details['device'] }}</li>
        <li><strong>Platform:</strong> {{ $details['platform'] }}</li>
        <li><strong>Browser:</strong> {{ $details['browser'] }}</li>
        <li><strong>Country:</strong> {{$details['locationData']['country']}}</li>
        <li><strong>Region Name:</strong> {{$details['locationData']['regionName']}}</li>
        <li><strong>Isp:</strong> {{$details['locationData']['isp']}}</li>
    </ul>
    
    @if (isset($details['locationData']['lat']) && isset($details['locationData']['lon']))
        <div style="margin-top: 20px;">
            <p>You can view your location on the map by clicking the following link:</p>
            <a href="https://www.google.com/maps?q={{ $details['locationData']['lat'] }},{{ $details['locationData']['lon'] }}" target="_blank">
                View on Google Maps
            </a>
        </div>
    @else
        <p>Location information is not available.</p>
    @endif
    
    <p>If you did not authorize this login, please contact support immediately.</p>
</body>
</html>
