@include('backEnd.order.cart_table_rows')
<script>
    function cart_content(){
           $.ajax({
             type:"GET",
             url:"{{route('admin.order.cart_content')}}",
             dataType: "html",
             success: function(cartinfo){
               $('#cartTable').html(cartinfo)
             }
          });
      }
      function cart_details(){
           $.ajax({
             type:"GET",
             url:"{{route('admin.order.cart_details')}}",
             dataType: "html",
             success: function(cartinfo){
               $('#cart_details').html(cartinfo)
             }
          });
      }
    $(document).on("click", ".cart_increment", function(e){
        e.preventDefault();
        var id = $(this).data("id");
        var qty = $(this).val();
        if(id){
              $.ajax({
               cache: false,
               data:{'id':id,'qty':qty},
               type:"GET",
               url:"{{route('admin.order.cart_increment')}}",
               dataType: "json",
            success: function(){
                cart_content();
                cart_details();
            }
          });
        }
   });
    $(document).on("click", ".cart_decrement", function(e){
        e.preventDefault();
        var id = $(this).data("id");
        var qty = $(this).val();
        if(id){
              $.ajax({
               cache: false,
               type:"GET",
               data:{'id':id,'qty':qty},
               url:"{{route('admin.order.cart_decrement')}}",
               dataType: "json",
            success: function(){
                cart_content();
                cart_details();
            }
          });
        }
   });
    $(document).on("click", ".cart_remove", function(e){
        e.preventDefault();
        var id = $(this).data("id");
        if(id){
              $.ajax({
               cache: false,
               type:"GET",
               data:{'id':id},
               url:"{{route('admin.order.cart_remove')}}",
               dataType: "json",
              success: function(){
                cart_content();
                cart_details();
            }
          });
        }
   });
   $(document).on("change", ".cart-size-selector", function(){
        var rowId = $(this).data('id');
        var $row = $(this).closest('tr');
        var sizeId = $(this).val();
        var colorId = $row.find('.cart-color-selector').val() || '';
        $.ajax({
           cache: false,
           type:"GET",
           data:{id:rowId, size_id:sizeId, color_id:colorId},
           url:"{{ route('admin.order.cart.update') }}",
           dataType: "json",
           success: function(){
            cart_content();
            cart_details();
          }
        });
   });
   $(document).on("change", ".cart-color-selector", function(){
        var rowId = $(this).data('id');
        var $row = $(this).closest('tr');
        var colorId = $(this).val();
        var sizeId = $row.find('.cart-size-selector').val() || '';
        $.ajax({
           cache: false,
           type:"GET",
           data:{id:rowId, size_id:sizeId, color_id:colorId},
           url:"{{ route('admin.order.cart.update') }}",
           dataType: "json",
           success: function(){
            cart_content();
            cart_details();
          }
        });
   });
</script>
