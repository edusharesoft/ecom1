<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thanks! We have processing your order. From giftshopbd.com</title>
</head>
<body class="bg-white">
<div style="background:#ddd; width:100%;text-align:center !important">
<div style="background:#fff; width:90%;margin:0 auto !important">
     @php
     
        $order = App\Models\Order::where('id',$order_id)->with('orderdetails','payment','shipping','customer')->first();
       @endphp
    <!-- email template -->
    <table class="body-wrap" style="background:#fff; width: 100%; margin: 0;">
        <tbody style="background:#4DBC60;">
            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 25px; margin: 0;border:0">
                <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                   <h3 style="color:#fff;text-align:center;padding:20px 0">Your Order Number: #{{$order->invoice_id}}</h3>
                 </td>
            </tr>
        </tbody>
    </table>
    <table class="body-wrap" style="background:#fff; width: 100%;text-align:center;">
    <tbody>
        <tr style="text-align:center">
            <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
               <img src="https://giftshopbd.websolutionit.com/public/frontEnd/images/giftshopbd-logo.webp" style="width:180px;margin-top:15px">
             </td>
        </tr>
    </tbody>
</table>
    <table class="body-wrap" style="background:#fff; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;  font-size: 16px; width: 100%; margin: 0;padding:0 30px">
        <tbody style="background:#fff">
            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;border:0">
                <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; padding-top: 15px;">
                    Hi Dear <strong>{{$order->shipping?$order->shipping->name:''}}</strong>
                 </td>
            </tr>
            <tr>
                <td style="padding:30px 0;border:0">Thanks — we've successfully delivered your order ID #{{$order->invoice_id}}. Please follow these notes about your order.</td></br>
            </tr>
             <tr>
                <td style="padding:15px;border:2px solid #ddd">{!! $order->admin_note !!}</td></br>
            </tr>
        </tbody>
    </table>
</table>
    <table class="body-wrap" style="background:#fff; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;  font-size: 16px; width: 100%; margin: 0;padding:0 30px">
        <tbody style="background:#fff">
            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;border:0">
                <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; padding-top: 15px;">
                    *If you need any queries please send us a message with your order ID on our <a href="https://wa.link/zw696d" style="font-weight:700;color:blue">WhatsApp</a>
                 </td>
            </tr>
        </tbody>
    </table>
    
    <table style="padding:10px 30px;margin:0 auto;text-align:center !important">
        <tbody>
            <tr>
                <td>
                    <ul style="padding:0 !important">                       
        				<li style="list-style: none; margin: 0 5px; height: 40px; width: 40px; text-align: center; line-height: 40px; background: #4551F7; border-radius: 50px;float:left !important"><a href="https://www.facebook.com/websolutionitcom"><img src="https://giftshopbd.websolutionit.com/public/frontEnd/images/facebook-f-brands.png"  style="margin-top: 8px;height: 25px;" /></a></li>
        				                    
        				<li style="list-style: none; margin: 0 5px; height: 40px; width: 40px; text-align: center; line-height: 40px; background: #4DBC60; border-radius: 50px;float:left !important"><a href="https://wa.link/zw696d"><img src="https://giftshopbd.websolutionit.com/public/frontEnd/images/whatsapp-brands.png" style="margin-top: 8px;height: 25px;" /></a></li>
        			</ul>
    			</td>
			</tr>
        </tbody>
    </table>
     <table class="body-wrap" style="background:#fff; width: 100%; margin: 0;">
        <tbody style="background:#4DBC60;">
            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box;  margin: 0;border:0">
                <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; margin: 0;">
                   <p style="color:#fff;text-align:center;padding:20px 0;font-size:15px;letter-spacing:2px">@copyright {{date('Y')}} Giftshopbd</p>
                 </td>
            </tr>
        </tbody>
    </table>
    
</div>
</div>
</body>
</html>