<?php

use App\Models\Cart;
use App\Models\User;

?>

<div>
    <div class="modal fade" id="pinCodeModal" tabindex="-1" role="dialog" aria-labelledby="pinCodeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="pinCodeModalLabel">Please fill in the details below</h4>

                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate>
                        <div class="form-row">
                            <div class="col-md-12 mb-3 ">
                                <label for="validationCustom05">Please enter your Zip code.</label>
                                <input type="text" id="typeahead-input" name="zipcode" maxlength="6" value="{{$_COOKIE['pin_code'] ?? ''}}" autocomplete="off" placeholder="Search" class="form-control" />
                                <div class="invalid-feedback">
                                    Please provide a valid zip.
                                </div>
                            </div>
                            <div class="col-md-12 mb-3 ">
                                <label for="validationCustom05">Shipping Method.</label>
                                <select name="shipping_method" class="form-control" id="shipping_method">
                                    <option value="">Select Shipping method</option>
                                    @foreach($shippingMethods as $key => $shippingMethod)
                                    <option value="{{$key}}" @if(isset($_COOKIE['shipping_method']) && $_COOKIE['shipping_method']==$key) selected @endif>{{$shippingMethod}}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-12 mb-3 warehouseCls" style="display: none;">
                                <label for="validationCustom05">Select Warehouse.</label>
                                <select name="warehouse" class="form-control" id="warehouse">
                                    <option value="">Select Warehouse</option>
                                </select>

                            </div>

                        </div>
                        <button class="btn btn-primary text-right" type="submit">Save</button>

                    </form>


                </div>

            </div>
        </div>
    </div>
</div>

<x-a-typeahead :model="''" :column="[
    [
        'id' =>'typeahead-input',
        'url'=>'pin-code/list'
    ],
    ]" />


<script>
    (function() {
        'use strict';

        function isCookieSet(cookieName) {
            return document.cookie.indexOf(cookieName + '=') >= 0;
        }

        function showModalIfCookieNotSet() {
            if (!isCookieSet('pin_code')) {
                if (!"<?= User::CheckAddress() ?>") {
                    // $('#pinCodeModal').modal('show');
                } else {
                    document.cookie = "pin_code=" + "<?= User::CheckAddress() ?>" + "; expires=Thu, 31 Dec 2099 12:00:00 UTC; path=/";
                    document.cookie = "shipping_method=" + "<?= (new Cart())::SHIPPING_METHOD_COURIER ?>" + "; expires=Thu, 31 Dec 2099 12:00:00 UTC; path=/";

                }
            }
        }

        function isValidPIN(pin) {
            var pinRegex = /^\d{6}$/;
            return pinRegex.test(pin);
        }
        window.addEventListener('load', function() {
            showModalIfCookieNotSet();
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.forEach.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        var pinCode = $('#typeahead-input').val();
                        var shipping_method = $('#shipping_method').val();
                        var warehouse = $('#warehouse').val();
                        event.preventDefault();
                        if (!isValidPIN(pinCode)) {
                            alert('Please enter a valid 6-digit PIN code.');
                            return;
                        }
                        if (!shipping_method) {
                            alert('Please select shipping method.');
                            return;
                        }
                        const isPickup = shipping_method === "<?= (new Cart())::SHIPPING_METHOD_PICKUP ?>";
                        if (isPickup) {
                            var warehouse = $('#warehouse').val();
                            if (!warehouse) {
                                alert('Please select warehouse.');
                                return;
                            }
                        }
                        document.cookie = "pin_code=" + pinCode + "; expires=Thu, 31 Dec 2099 12:00:00 UTC; path=/";
                        document.cookie = "shipping_method=" + shipping_method + "; expires=Thu, 31 Dec 2099 12:00:00 UTC; path=/";
                        document.cookie = "warehouse=" + warehouse + "; expires=Thu, 31 Dec 2099 12:00:00 UTC; path=/";

                        $('#pinCodeModal').modal('hide');


                        window.location.reload();


                       
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {

        var cookieWarehouse = "{{$_COOKIE['warehouse'] ?? ''}}";
        getWarehouse("{{$_COOKIE['shipping_method'] ?? 3}}", "{{$_COOKIE['pin_code'] ?? ''}}");

        $(document).on('change keyup', '#shipping_method, #typeahead-input', function() {
            var selectedValue = $("#shipping_method").val();
            var pincode = $('#typeahead-input').val();
            getWarehouse(selectedValue, pincode);
        });
        function getWarehouse(selectedValue, pincode) {
            const $warehouse = $('#warehouse');
            const $warehouseContainer = $('.warehouseCls');
            const isPickup = selectedValue === "<?= (new Cart())::SHIPPING_METHOD_PICKUP ?>";
            if (isPickup) {
                if (!shipping_method) {
                    alert('Please select shipping method.');
                    return;
                }
                $warehouseContainer.show();
                $.getJSON("{{ route('get-warehouse') }}", {
                        pincode: pincode
                    })
                    .done(function(response) {
                        $warehouse.empty().append('<option value="">Select warehouse</option>');
                        response.forEach(function(warehouse) {
                            const selected = (cookieWarehouse == warehouse.id) ? 'selected' : '';
                            $warehouse.append(`<option value="${warehouse.id}" ${selected}>${warehouse.name}, ${warehouse.address} </option>`);
                        });
                    })
                    .fail(function(xhr, status, error) {
                        console.error('Error fetching users');
                    });
            } else {
                $warehouse.empty();
                $warehouseContainer.hide();
            }
        }

    });
</script>