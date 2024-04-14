@push('styles')
<style type="text/css">
    .StripeElement {
      box-sizing: border-box;
      height: 40px;
      padding: 10px 12px;
      border: 1px solid transparent;
      border-radius: 4px;
      background-color: white;
      box-shadow: 0 1px 3px 0 #e6ebf1;
      -webkit-transition: box-shadow 150ms ease;
      transition: box-shadow 150ms ease;
    }
    .StripeElement--focus {
      box-shadow: 0 1px 3px 0 #cfd7df;
    }
    .StripeElement--invalid {
      border-color: #fa755a;
    }
    .StripeElement--webkit-autofill {
      background-color: #fefde5 !important;
    }
</style>
@endpush
<!-- <label class="mt-3" for="card-element">
    Card details:
</label>

<div id="cardElement">
    <div id="example3-card-number"></div>
    <div id="example3-card-expiry"></div>
    <div id="example3-card-cvc"></div>
</div> -->
  <div class="group">
    <label>
      <span>Card number</span>
      <div id="card-number-element" class="field"></div>
    </label>
    <label>
      <span>Expiry date</span>
      <div id="card-expiry-element" class="field"></div>
    </label>
    <label>
      <span>CVC</span>
      <div id="card-cvc-element" class="field"></div>
    </label>
  </div>


<!-- <small class="form-text text-muted" id="cardErrors" role="alert"></small> -->

<input type="hidden" name="payment_method" id="paymentMethod">

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>

  var style = {
    base: {
      iconColor: '#666EE8',
      color: '#31325F',
      lineHeight: '40px',
      fontWeight: 300,
      fontFamily: 'Helvetica Neue',
      fontSize: '15px',

      '::placeholder': {
        color: '#CFD7E0',
      },
    },
  };

  const stripe = Stripe('{{ config('services.stripe.key') }}');

  const elements = stripe.elements({ locale: 'en' });

  // const cardElement = elements.create('card',{
  //       iconStyle: 'solid',
  // });
  // cardElement.mount('#cardElement');

  var cardNumberElement = elements.create('cardNumber', {
  style: style,
  placeholder: 'Custom card number placeholder',
});
cardNumberElement.mount('#card-number-element');

var cardExpiryElement = elements.create('cardExpiry', {
  style: style,
  placeholder: 'Custom expiry date placeholder',
});
cardExpiryElement.mount('#card-expiry-element');

var cardCvcElement = elements.create('cardCvc', {
  style: style,
  placeholder: 'Custom CVC placeholder',
});
cardCvcElement.mount('#card-cvc-element');

</script>

<script>
    const form = document.getElementById('paymentForm');
    const payButton = document.getElementById('payButton');
    payButton.addEventListener('click', async(e) => {
            document.querySelector('.cs_loader').style.display = 'block';
            this.disabled = true;
            e.preventDefault();
            const { paymentMethod, error } = await stripe.createPaymentMethod(
                'card', cardNumberElement, {
                    billing_details: {
                        "name": "{{$user['name']}}",
                        "email":"{{$user['email']}}"
                    }
                }
            );
            document.querySelector('.cs_loader').style.display = 'none';
            this.disabled = false;
            if (error) {
                const displayError = document.getElementById('cardErrors');
                displayError.textContent = error.message;
            } else {
                const tokenInput = document.getElementById('paymentMethod');
                tokenInput.value = paymentMethod.id;
                form.submit();
            }
        
    });
</script>
@endpush