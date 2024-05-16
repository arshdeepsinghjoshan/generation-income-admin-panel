<div>
    <div class="position-relative">
        <span class="add_items" id="cart_count">0</span>
        <!-- open model box start -->
        <button id="myBtn" class="border-0 bg-transparent">
            <a href="#">
                <iconify-icon icon="mdi:cart-outline"></iconify-icon>
            </a>
        </button>
        <!-- The Modal -->

        <div id="myModal" class="modal_box">
            <!-- Modal content -->
            <div class="modal-content p-4">
                <span class="close">×</span>
                <div class="d-flex align-items-center justify-content-between mt-3 mb-5">
                    <h4 class="text-dark fw-bold">Shopping Cart</h4>
                </div>
                <div class="cartItems">
                    @foreach($model as $cartItem)
                    <div class="stat_data pb-3 mb-3 d-flex align-items-center ">
                        <img class="default-img me-3" src="{{ asset('uploads/products/'. !empty($cartItem->product) ? $cartItem->product->image : '' ) }}" alt="">
                        <div class="">
                            <p class="text-dark pb-0 mb-0">{{!empty($cartItem->product) ? $cartItem->product->name : ''}}</p>
                            <small class="cv-pdoduct-price">{{$cartItem->quantity}} &nbsp; ✕ &nbsp; ₹ {{$cartItem->unit_price}}</small>
                        </div>
                    </div>
                    @endforeach
                    <div class="dropdown-cart-total d-flex aling-items-center justify-content-between my-4">
                        <span class="text-dark fw-bold">SUBTOTAL:</span>
                        <span class="cart-total-price float-right text-dark fw-bold">₹ {{!empty($model)? $model->sum('total_price') : ''}}</span>
                    </div>
                </div>
                <div class="text-center">
                    <a class="default-btn d-block" href="{{url('cart')}}">View Cart</a>
                </div>
                <div class="text-center">
                    <a class="default-btn bg-dark d-block text-white" href="{{url('cart')}}">Check Out</a>
                </div>
            </div>
        </div>
    </div>
</div>