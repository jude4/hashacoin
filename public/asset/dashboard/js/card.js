// Create a Stripe client.
var stripe = Stripe(ppxd);

// Create an instance of Elements.
var elements = stripe.elements(
    {
        fonts: [
            {
                cssSrc: 'https://fonts.googleapis.com/css?family=Montserrat:400,500',
            }
        ]
    }
);

var style = {
    base: {
        color: '#222221',
        fontFamily: '"Montserrat", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '15px',
        '::placeholder': {
            color: '#222221'
        }
    },
    invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
    }
};

// Create an instance of the card Element.
var card = elements.create('card', { style: style });

// Add an instance of the card Element into the `card-element` <div>.
card.mount('#card-element');

// Handle real-time validation errors from the card Element.
card.addEventListener('change', function (event) {
    var displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// Handle form submission.
var form = document.getElementById('payment-form');
form.addEventListener('submit', function (event) {
    event.preventDefault();

    stripe.createSource(card).then(function (result) {
        if (result.error) {
            // Inform the user if there was an error.
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = result.error.message;
        } else {
            // Send the token to your server.
            stripeSourceHandler(result.source);
        }
    });
});

// Submit the form with the source ID.
function stripeSourceHandler(source) {
    // Insert the source ID into the form so it gets submitted to the server
    var form = document.getElementById('payment-form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeSource');
    hiddenInput.setAttribute('value', source.id);
    form.appendChild(hiddenInput);
    // Submit the form
    form.submit();
    $('#payment-submit').html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', 'disabled');
}
