<!DOCTYPE html>

<html>

<head>

    <title>ParstateMail</title>

</head>

<body>

<h2>{{ $details['title'] }}</h2>

<p>{{ $details['message'] }}</p>

<p>{{ $details['ackowledge'] }}  </p>

<a href="{{ url($details['ackowledge_url']) }}">Übernehmen</a>

<h3>{{ $details['greetings'] }}</h3>

</body>

</html>
