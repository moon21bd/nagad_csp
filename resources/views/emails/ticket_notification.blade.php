<!DOCTYPE html>
<html>
<head>
    <title>New Ticket Notification</title>
</head>
<body>
    <p>{{ $notificationData['message'] }}</p>
    <p><a href="{{ $notificationData['link'] }}">View Ticket</a></p>
</body>
</html>
