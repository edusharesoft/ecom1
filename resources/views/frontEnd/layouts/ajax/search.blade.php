@if($products)

<div class="search_product">
		<ul>
		@foreach($products as $value)
		<a href="{{route('product',$value->slug)}}">
			<li>
					<div class="search_img">
						<img src="{{asset($value->image?$value->image->image:'')}}" alt="{{$value->name}}">
					</div>
					<div class="search_content">
						<p class="name">{{$value->name}}</p>                 
						<p  class="price">৳{{$value->new_price}} @if($value->old_price)<del>৳{{$value->old_price}}</del>@endif</p>
					</div>
			</li>
		</a>
		@endforeach
	</ul>
</div>
@endif