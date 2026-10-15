<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Customer;
use App\Models\OrderDetails;
use App\Models\Shipping;
use App\Models\Payment;
class OrderPlace extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $customer;
    public $orderDetails;
    public $shipping;

    /**
     * Create a new message instance.
     *
     * @param $order
     * @param $customer
     * @param $orderDetails
     */
    public function __construct($order)
    {
        $this->order = $order;
        $this->customer = Customer::find($order->customer_id);
        $this->shipping = Shipping::where('order_id',$order->id)->first();
        $this->orderDetails = OrderDetails::where('order_id',$order->id)->get();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Order Confirmation - '.config('app.name'))
                    ->view('emails.order_place_for_admin')
                    ->with([
                        'order' => $this->order,
                        'customer' => $this->customer,
                        'shipping' => $this->shipping,
                        'orderDetails' => $this->orderDetails,
                    ]);
    }
}
