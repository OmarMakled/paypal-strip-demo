<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('app.name')}}</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <script>
        window.app = {
            paypal: {
                env: "{{config('services.paypal.env')}}"
            }
        }
    </script>
</head>

<body>
    <div class="container mt-5" id="app">
        <div class="jumbotron">
            <h1 class="display-4">PayPal Demo</h1>
            <pay-pal></pay-pal>
        </div>
    </div>
</body>

</html>
