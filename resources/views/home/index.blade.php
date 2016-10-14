<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div id="app">
    <journey-history></journey-history>
</div>

<script type="application/javascript" src="{{ asset('js/app.js') }}"></script>
<script>
    window.Laravel = { csrfToken: '{{ csrf_token() }}' };
</script>
</body>