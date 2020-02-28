<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('app.name')}}</title>
    <script src="https://js.stripe.com/v3"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="jumbotron">
            <h1 class="display-4">Stripe Demo</h1>
            <form id="payment-form" class="sr-payment-form">
                <div class="sr-combo-inputs-row">
                    <div class="sr-input sr-card-element" id="card-element"></div>
                </div>
                <div class="sr-field-error" id="card-errors" role="alert"></div>
                <button id="submit" class="btn btn-block btn-info mt-4">
                    <div class="spinner hidden" id="spinner"></div>
                    <span id="button-text">Pay</span><span id="order-amount"></span>
                </button>
                <div class="sr-result hidden">
                  <pre>
                    <code></code>
                  </pre>
                </div>
            </form>
        </div>
    </div>
    <script src="/js/stripe.js"></script>
</body>

</html>
