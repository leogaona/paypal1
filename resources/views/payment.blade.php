<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Ensures optimal rendering on mobile devices -->
  </head>

  <body>
    <!-- Include the PayPal JavaScript SDK; replace "test" with your own sandbox Business account app client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id={{env('PAYPAL_CLIENT_ID')}}&currency=USD"></script>

    <!-- Set up a container element for the button -->
    <div id="paypal-button-container"></div>

    <script>
      paypal.Buttons({

        // Sets up the transaction when a payment button is clicked
        createOrder: function(data, actions) {
          return actions.order.create({
            payer:{
              email_address: 'leogaona@gmail.com',
              name:{
                given_name:'Leonardo',
                surname: 'schmidt'
              },
              address:{
                address_line_1: "los laureles",
                address_line_2: "test",
                admin_area_1: "",
                admin_area_2: "",
                postal_code: "1010",
                country_code: "US"
              }
            },
            purchase_units: [{
              amount: {
                value: '77.44' //PRECIO: Can reference variables or functions. Example: `value: document.getElementById('...').value` 
              }
            }]
          });
        },

        // Finalize the transaction after payer approval
        onApprove: function(data, actions) {

          return fetch('/paypal/process/' + data.orderId, {
            method: 'post'
          })
          .then(res => res.json())
          .then(function(orderData){
            var errorDetail = Array.isArray(orderData.details) && orderData.details[0];

            if(errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED'){
              return actions.restart();
            }
            if(errorDetail){
              var msg = 'sorry , your transaction could not be processed';
              if(errorDetail.description) msg += '\n\n' + errorDetail.description; 
              if(orderData.debug_id) msg += ' (' + orderData.debug_id + ')';
              return alert(msg);
            }
          });

          /*
          return actions.order.capture().then(function(orderData) {
            // Successful capture! For dev/demo purposes:
                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                var transaction = orderData.purchase_units[0].payments.captures[0];
                alert('Transaction '+ transaction.status + ': ' + transaction.id + '\n\nSee console for all available details');

            // When ready to go live, remove the alert and show a success message within this page. For example:
            // var element = document.getElementById('paypal-button-container');
            // element.innerHTML = '';
            // element.innerHTML = '<h3>Thank you for your payment!</h3>';
            // Or go to another URL:  actions.redirect('thank_you.html');
          });
          */
        },
        onError: function (err) {
          // For example, redirect to a specific error page
          console.log(err)
          window.location.href = "/your-error-page-here";
        }
      }).render('#paypal-button-container');

    </script>
  </body>
</html>