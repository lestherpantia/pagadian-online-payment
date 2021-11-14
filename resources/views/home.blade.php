</<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<form action="{{ route('payment') }}" method="post">


    <h3>HELLOOOOOOOOOOOO USRE!!</h3>

    <div class="box">
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <label>Hello Enter your amount</label>
        <input type="text" name="amount">
        <button type="submit">Pay!</button>
    </div>

</form>
</body>
</html>
