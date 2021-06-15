@extends('layouts.forpaypal')

@section('content')

  {{-- <script
      src="https://www.paypal.com/sdk/js?client-id=ARGLmf8jH1zL4I5v5lERZbesX4xp10IX-y1SI55QvRiG3IA4CFgW7N0INMMXAaixFimBroxP8FXmjr7t"> // Required. Replace SB_CLIENT_ID with your sandbox client ID.
  </script> --}}
  <div class="content">
    <div id="paypal-button"></div>

  </div>

  <script src="https://www.paypalobjects.com/api/checkout.js"></script>
  <script>
  paypal.Button.render({
    env: 'sandbox',
    style: {
      size: 'large',
      color: 'gold',
      shape: 'pill'
    },
    payment: function(data, actions){
      return actions.request.post('/laravel/TokTokBackEnd/public/api/paypal/create-payment')
      .then(function (res){
        console.log(res);
        return res.id;
      });
    },
    onAuthorize: function(data, actions){
      return actions.request.post('/laravel/TokTokBackEnd/public/api/paypal/execute-payment', {
        paymentID: data.paymentID,
        payerID: data.payerID
      })
      .then(function (res){
        console.log(res);
        alert('Payment Done');

      });
    }
  }, '#paypal-button');
  </script>
    {{-- <div id="paypal-button-container"></div> --}}

    {{-- <script> --}}
      {{-- // paypal.Buttons({
        // env: 'sandbox', //Or production
        // // Add the paymen Callback
        // payment: function(data, actions){
        //   //Make a request to the server
        //   return actions.request.post('/api/paypal/create-payment')
        //   .then(function(res){
        //     //return res.id from the response
        //     return res.id;
        //   });
        // },
        // createOrder: function(data, actions) {
        //   // This function sets up the details of the transaction, including the amount and line item details.
        //   return actions.order.create({
        //     purchase_units: [{
        //       amount: {
        //         value: '5.01'
        //       }
        //     }]
        //   });
        // },
      //   onApprove: function(data) {
      //     return fetch('/api/paypal/capture-paypal-transaction', {
      //       headers: {
      //         'content-type': 'application/json'
      //       },
      //       body: JSON.stringify({
      //         orderID: data.orderID
      //       })
      //     }).then(function(res) {
      //       return res.json();
      //     }).then(function(details) {
      //       alert('Transaction funds captured from ' + details.payer_given_name);
      //     })
      //   }
      // }).render('#paypal-button-container'); --}}
    {{-- </script> --}}
@endsection
