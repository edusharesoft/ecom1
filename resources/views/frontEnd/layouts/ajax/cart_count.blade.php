@php
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal=str_replace(',','',$subtotal);
    $subtotal=str_replace('.00', '',$subtotal);
    view()->share('subtotal',$subtotal);
@endphp
<a href="{{route('customer.checkout')}}">
  <p class="margin-shopping">
  <i class="fa-solid fa-cart-shopping"></i>
  <span>{{Cart::instance('shopping')->count()}}</span>
  
  </p></a>
  <div class="cshort-summary">
    <ul>
    @foreach(Cart::instance('shopping')->content() as $key=>$value)
      <li><a href=""><img src="{{asset($value->options->image)}}" alt=""></a></li>
      <li><a href="">{{$value->name}}</a></li>
      <li>Qty: {{$value->qty}}</li>
      <li><p>৳{{$value->price}}</p><button class="remove-cart cart_remove" data-id="{{$value->rowId}}"><i data-feather="x"></i></button></li>
    @endforeach
    </ul>
    <p><strong>সর্বমোট : ৳{{$subtotal}}</strong></p>
    <a href="{{route('customer.checkout')}}" class="go_cart">  অর্ডার করুন </a>
  </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
<script>
  feather.replace()
</script>
<!-- cart js start -->
    <script>

      $('.cart_remove').on('click',function(){
        var id = $(this).data('id');   
        $("#loading").show();
        if(id){
          $.ajax({
             type:"GET",
             data:{'id':id},
             url:"{{route('cart.remove')}}",
             success:function(data){               
              if(data){
                $("#cartlist").html(data);
                $("#loading").hide();
                return cart_count()+cart_summary();
              }
             }
          });
         }  
       });

      function cart_count(){
          $.ajax({
             type:"GET",
             url:"{{route('cart.count')}}",
             success:function(data){               
              if(data){
                $("#cart-qty").html(data);
              }else{
                 $("#cart-qty").empty();
              }
             }
          }); 
       };
      function cart_summary(){
          $.ajax({
             type:"GET",
             url:"{{route('shipping.charge')}}",
             dataType: "html",
              success: function(response){
                  $('.cart-summary').html(response);
              }
          });
       };
    </script>
    <!-- cart js end -->