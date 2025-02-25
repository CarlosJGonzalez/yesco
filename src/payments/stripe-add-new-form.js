"use strict";
/*V3*/
// Create a Stripe client.
//var stripe = Stripe('pk_test_JZojAoET6kjPNZM01qWnWfqV');
var stripe = Stripe('pk_live_O3kaN2xUHeHeCs6MILkRqJwp');

// Create an instance of Elements.
var elements = stripe.elements();

// Custom styling can be passed to options when creating an Element.
var style = {
  base: {
    color: '#32325d',
    lineHeight: '18px',
    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
    fontSmoothing: 'antialiased',
    fontSize: '16px',
    '::placeholder': {
      color: '#aab7c4'
    }
  },
  invalid: {
    color: '#fa755a',
    iconColor: '#fa755a'
  }
};

// Create an instance of the card Element.
var card = elements.create('card', {style: style});

// Add an instance of the card Element into the `card-element` <div>.
card.mount('#card-element');

// Get the value of the button that sends the information
var submitButton = document.getElementById('submitButtonAddCard');

// Get the value of Next Button on the step2
var step2NextButton = document.getElementById('step2-next');

// Get the modal form
var form = document.getElementById('payment-form');

// Get the button that closes the modal
//var closeBtnModal = document.getElementsByName("close-btn-modal")[0];
var closeBtnModal = document.getElementById("close-btn-modal-addcard");

// Handle real-time validation errors from the card Element.
card.addEventListener('change', function(event) {

  var displayError = document.getElementById('card-errors');
  if (event.error) {
    displayError.textContent = event.error.message;
    submitButton.disabled = true;
  } else {
    displayError.textContent = '';
    submitButton.disabled = false; 
  }

});

// Handle close button
closeBtnModal.addEventListener('click', function() {
	form.reset();
});

// Handle form submission.
form.addEventListener('submit', function(event) {
    event.preventDefault();
  
    // Gather additional customer data we may have collected in our form.
    var name = form.querySelector('#input-name');
    var email = form.querySelector('#input-email');
    var address1 = form.querySelector('#input-address');
    var address2 = form.querySelector('#input-address-2');
    var city = form.querySelector('#input-city');
    var state = form.querySelector('#input-state');
    var country = form.querySelector('#input-country');
    var additionalData = {
      name: name ? name.value : undefined,
      email: email ? email.value : undefined,
      address_line1: address1 ? address1.value : undefined,
      address_line2: address2 ? address2.value : undefined,
      address_city: city ? city.value : undefined,
      address_state: state ? state.value : undefined,
      address_country: country ? country.value : undefined,
    };
	
    stripe.createToken(card, additionalData).then(function(result) {
		//If the token has an error the submit and next buttons will never be enabled
		if (result.error) {
		  // Inform the user if there was an error.
		  var errorElement = document.getElementById('card-errors');
		  errorElement.textContent = result.error.message;
		  submitButton.disabled = true;
		  step2NextButton.disabled = true;
		} else {
      console.log(1);
		  // Send the token to your server.
		  stripeTokenHandler(result.token);
		  // Hide the form if any error was found 
		  $('#addNewCard').modal('hide');
		  // reset the form and clean the card object
		  form.reset();
		}
	});

});

// Submit the form with the token ID.
function stripeTokenHandler(token) {

  // Insert the token ID into the form so it gets submitted to the server
  var form = document.getElementById('payment-form');
  var hiddenInput = document.createElement('input');
  hiddenInput.setAttribute('type', 'hidden');
  hiddenInput.setAttribute('name', 'stripeToken');
  hiddenInput.setAttribute('value', token.id);
  form.appendChild(hiddenInput);

  //Send ajax request
    $.ajax({
        url: "/payments/xt_new_stripe_object.php",
        method: "POST",
        data: {customer_id: $("input[name='customer_id']").val(),owner_email: token.email, id:token.id,object:token.type, card:JSON.stringify(token.card)},
        cache: false,
        dataType: "html",
        success: function( html ) {
            console.log(html);
            if(html == ''){
               location.reload();
            }else{
                if($("input[name='customer_id']").val() == ''){
                    var resultHmtl = html.split("<br>"); 
                    $("select[name='payment-method']").replaceWith(resultHmtl[0]);
                    $("input[name='customer_id']").replaceWith(resultHmtl[1]);
                }else{
                    $("select[name='payment-method']").replaceWith(html);
                }

                step2NextButton.disabled = false;
            }
        }
    });
}