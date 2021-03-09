<!DOCTYPE html>
<html lang="en">
<head>
  <title>Checkout</title>

  <meta charset="utf-8">
  <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="">

  <!-- Google Fonts -->
  <link href='http://fonts.googleapis.com/css?family=Questrial:400%7CMontserrat:300,400,700,700i' rel='stylesheet'>

  <!-- Css -->
  <link rel="stylesheet" href="{{ asset('pay/css/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('pay/css/font-icons.css') }}" />
  <link rel="stylesheet" href="{{ asset('pay/css/style.css') }}" />
  <link rel="stylesheet" href="{{ asset('pay/css/color.css') }}" />


</head>

<body>

  <!-- Preloader -->
  <div class="loader-mask">
    <div class="loader">
      <div></div>
    </div>
  </div>

  

  <main class="main oh" id="main">

    <!-- Page Title -->
    <section class="page-title text-center">
      <div class="container">
        <h1 class=" heading page-title__title">checkout</h1>
      </div>
    </section> <!-- end page title -->


    <!-- Checkout -->
    <section class="section-wrap checkout">
      <div class="container relative">
        <div class="row">

          <div class="ecommerce col">

 
            <form name="checkout" class="checkout ecommerce-checkout row" style="justify-content: center" action="/checkout/order" method="POST">
              @csrf 
              <!-- Your Order -->
              <div class="col-lg-5">
                <div class="order-review-wrap ecommerce-checkout-review-order" id="order_review">
                  <h2 class="uppercase">Your Order</h2>
                  <table class="table shop_table ecommerce-checkout-review-order-table">
                    <tbody>
                      <tr>
                        <th>測試付費<span class="count"> x 1</span></th>
                        <td>
                          <span class="amount">$100</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <div id="payment" class="ecommerce-checkout-payment">
                    <h2 class="uppercase">Payment Method</h2>
                    <ul class="payment_methods methods">

                    

                      {{-- <li class="">
                        <input id="payment_method_cheque" type="radio" class="input-radio" name="payment_method" value="cheque">
                        <label for="payment_method_cheque">Cheque payment</label>
                        <div class="payment_box payment_method_cheque">
                          <p>Please send your cheque to Store Name, Store Street, Store Town, Store State / County, Store Postcode.</p>
                        </div>
                      </li> --}}
                      
                      <li class="payment_method_ecpay">
                        <input id="payment_method_ecpay" type="radio" class="input-radio" name="pay_way" value="ecpay" checked="checked">
                        <label for="payment_method_ecpay">ECPAY(綠界付款)</label>
                        {{-- <img src="img/shop/paypal.png" alt=""> --}}
                        <div class="payment_box payment_method_ecpay">
                          <p>綠界科技Ecpay是第三方支付領導品牌,提供金流、物流、電子發票、 跨境電商、資安聯防一站購足服務</p>
                        </div>
                      </li>
                      
                      <li class="payment_method_opay">
                        <input id="payment_method_opay" type="radio" class="input-radio" name="pay_way" value="opay" >
                        <label for="payment_method_opay">OPAY(歐付寶付款)</label>
                        {{-- <img src="img/shop/paypal.png" alt=""> --}}
                        <div class="payment_box payment_method_opay">
                          <p>歐付寶opay是第三方支付領導品牌,提供金流、物流、電子發票、 跨境電商、資安聯防一站購足服務</p>
                        </div>
                      </li>
        

                    </ul>
                    <div class="form-row place-order">
                      <input type="submit" name="ecommerce_checkout_place_order" class="btn btn-lg btn-color btn-button" id="place_order" value="Place order">
                    </div>
                  </div>
                </div>
              </div> <!-- end order review -->
            </form>

          </div> <!-- end ecommerce -->

        </div> <!-- end row -->
      </div> <!-- end container -->
    </section> <!-- end checkout -->


  </main> <!-- end main-wrapper -->

  <!-- jQuery Scripts -->
  <script type="text/javascript" src="{{ asset('pay/js/jquery.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('pay/js/bootstrap.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('pay/js/easing.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('pay/js/jquery.magnific-popup.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('pay/js/owl-carousel.min.js') }}"></script>  
  <script type="text/javascript" src="{{ asset('pay/js/flickity.pkgd.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('pay/js/modernizr.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('pay/js/scripts.js') }}"></script>
    
</body>
</html>