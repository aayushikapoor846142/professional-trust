@extends('admin-panel.layouts.app')

@section('content')
 <div class="ch-action">
                    <a href="{{ baseUrl('quotations') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                </div><div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
  <form id="form" class="js-validate mt-3" action="{{ baseUrl('/quotations/update/'.$record->unique_id) }}" method="POST">
                        @csrf
                        <div class="cds-ty-dashboard-box">
                            <div class="cds-ty-dashboard-box-body">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        {!! FormHelper::formInputText([
                                        'name'=>"quotation_title",
                                        'required'=>true,
                                        'id'=>"quotation_title",
                                        'value' => $record->quotation_title,
                                        "label"=>"Enter Quotation Title",
                                        ]) !!}
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-lg-6">
                                        {!! FormHelper::formSelect([
                                            'name' => 'currency',
                                            'label' => 'Select Currency',
                                            'class' => 'select2-input ga-country',
                                            'options' => FormHelper::supportCurrency(),
                                            'value_column' => 'value',
                                            'label_column' => 'label',
                                            "required"=>true,
                                            "selected"=>$record->currency,
                                            'is_multiple' => false
                                        ]) !!}
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-lg-6">
                                        {!! FormHelper::formSelect([
                                            'name' => 'service_id',
                                            'label' => 'Select Service',
                                            'class' => 'select2-input ga-country',
                                            'options' => $services,
                                            'value_column' => 'id',
                                            'label_column' => 'name',
                                            "required"=>true,
                                            "selected"=>$record->service_id,
                                            'is_multiple' => false
                                        ]) !!}
                                    </div>

                                    <div class="col-xl-12 mt-3 mb-3">
                                        <table class="table table-border">
                                            <thead>
                                                <tr style="background-color: #f8f9fa;">
                                                    <th>PARTICULAR</th>
                                                    <th>AMOUNT</th>
                                                    <th>Total</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="items-list">
                                                @foreach($record->particulars as $value)
                                                    <tr>
                                                        <input type="hidden" name="items[{{$value->id}}][id]" value="{{$value->id}}" />
                                                        <td><input type="text" class="item-name form-control" name="items[{{$value->id}}][name]" placeholder="Enter item name" value="{{$value->particular}}"></td>
                                                        <td><input type="number" name="items[{{$value->id}}][amount]" class="item-amount form-control" min="0" value="{{$value->amount}}"></td>
                                                        <td><input type="text" class="row-sub-total form-control" name="items[{{$value->id}}][row_sub_total]"  value="{{$value->amount}}" disabled>
                                                        </td>
                                                        <td><span class="invoice-remove-btn">X</span></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4">
                                                    <a href="javascript:void(0);" id="add-item-btn" style="color: blue; text-decoration: none; font-weight: bold;">+ Add item</a>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" class="text-end">
                                                        Total:
                                                    </th>
                                                    <td>
                                                        <span id="subtotal">0</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <div class="js-form-message">
                                            <input type="hidden" name="sub_total" id="sub_total">
                                        </div>
                                        <div class="js-form-message">
                                            <input type="hidden" name="total_amount" id="total_amount">
                                        </div>
                                        <div class="items_error_div text-danger"></div>
                                        <div class="items_amount_error_div text-danger"></div>
                                    </div>
                                </div>
                                <div class="text-start">
                                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
             
			</div>
	
	</div>
  </div>
</div>


@endsection
<!-- End Content -->
@section('javascript')
<script>
    $(document).ready(function() {
     
        updateTotals();
        let subtotal = 0; 
        $("#form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("form");
            if (!is_valid) {
                return false;
            }
            if($("#items-list > tr").length == 0){
                errorMessage("Add atleast one quotation summary");
                return false;
            }
            var formData = new FormData($(this)[0]);
            var url = $("#form").attr('action');
            $('.invoice-error-message').remove();
            $.ajax({
                url: url,
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        redirect(response.redirect_back);
                    } else {
                        
                        $.each(response.message, function (key, value) {

                            if(key != 'items' ){
                                let fieldName = key.replace(/\.(\d+)\./g, "[$1][").replace(/\.(\w+)/g, "[$1]");
                                // Ensure proper closing brackets
                                fieldName = fieldName.replace(/\]$/, '') + ']';

                                let inputField = $("[name='" + fieldName + "']");
                            
                                if (inputField.length > 0) {
                                    inputField.after(`<span class="invoice-error-message" style="color: red;">${value}</span>`);
                                }
                            }else{
                                errorMessage("Please add items");
                            }
                           
                        });

                        validation(response.message);
                    }
                },
                error: function() {
                    internalError();
                }
            });

        });
        // Add new item row with validation
        $("#add-item-btn").click(function () {
            let emptyField = false;
            $('.invoice-error-message').remove();
            // Check if any existing item name field is empty
            $(".item-name").each(function () {
                if ($(this).val().trim() === "") {
                    emptyField = true;
                    return false; // Break loop
                }
            });

            if (emptyField) {
                errorMessage("Please fill in all item names before adding a new one.");
                return;
            }
          
            let rowCount = $("#items-list tr").length; // Get row count to make uniq
            let row = `<tr class="js-form-message">
                <td><input type="text" class="item-name form-control" name="items[${rowCount}][name]" placeholder="Enter item name"></td>
                <td><input type="number" name="items[${rowCount}][amount]" class="item-amount form-control" min="0" value="0"></td>
                <td><input type="text" class="row-sub-total form-control" name="items[${rowCount}][row_sub_total]" disabled></td>
                <td><span class="invoice-remove-btn">X</span></td>
            </tr>`;
            $("#items-list").append(row);
        });

       
        $(document).on("blur", ".item-amount", function () {
            updateRowSubtotal($(this));
            updateTotals();
        });

        $(document).on("click", ".invoice-remove-btn", function () {
            $(this).closest("tr").remove();
            updateTotals();
        });

    });

    function updateRowSubtotal(element) {
        let row = element.closest("tr");
        let amount = parseFloat(element.val()) || 0;
        
        row.find(".row-sub-total").val(amount);
    }

    function updateTotals() {
        let subtotal = 0;
        $(".row-sub-total").each(function () {
            subtotal += parseFloat($(this).val()) || 0;
        });
        let totalDiscount = parseFloat($("#total-discount-price").text()) || 0;
        let taxRate = parseFloat($(".invoice-tax").val()) || 0;
        let totalAfterDiscount = subtotal - totalDiscount;
        let taxAmount = (totalAfterDiscount * taxRate) / 100;
        let totalAmount = (totalAfterDiscount + taxAmount).toFixed(2);
        $("#subtotal").text(subtotal);
        $("#sub_total").val(subtotal);
        $("#total-amount").text(totalAmount);
        $("#total_amount").val(totalAmount);
    }
</script>
@endsection