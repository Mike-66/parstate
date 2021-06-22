<!DOCTYPE html>

<html>

<head>

    <title>ParstateAlarm</title>

</head>

<body>

<h2>{{ $details['title'] }}</h2>

<p>{{ $details['message'] }}</p>

<p>{{ $details['ackowledge'] }}  </p>

<a href="{{ url($details['ackowledge_url']) }}">Übernehmen</a>

</body>

</html>
