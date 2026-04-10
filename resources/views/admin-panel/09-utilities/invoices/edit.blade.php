@extends('admin-panel.layouts.app')

@section('content')
<style>       
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    .invoice-remove-btn {
        color: red;
        cursor: pointer;
        font-size: 14px;
        margin-left: 10px;
    }   
</style>
<div class="ch-action">
                    <a href="{{ baseUrl('invoices') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                </div><div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="cds-invoices">
                <form id="form" class="js-validate mt-3" action="{{ baseUrl('/invoices/update/'.$record->unique_id) }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="cds-selectbox">
                                {!! FormHelper::formSelect([
                                    'name' => 'user_id',
                                    'id' => 'user_id',
                                    'label' => 'Select User',
                                    'class' => 'select2-input ga-country',
                                    'required' => true,
                                    'options' => $users->map(function($user) {
                                        return [
                                            'value' => $user->id,
                                            'label' => $user->first_name . ' ' . $user->last_name . ' [' . $user->email . ']',
                                        ];
                                    })->toArray(),
                                    'value_column' => 'value',
                                    'label_column' => 'label',
                                    'selected' => $record->user_id ?? '',
                                    'is_multiple' => false
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 col-lg-6">
                            {!! FormHelper::formTextarea([
                                'name' => 'bill_to',
                                'id' => 'bill_to',
                                'label' => 'Bill To',
                                'textarea_class' => 'noval',
                                'rows' => 4,
                                'cols' => 50,
                                'required' => true,
                                'value' => $record->bill_to,
                                         
                            ]) !!}                     
                        </div>
                        <div class="col-xl-6 col-md-6 col-lg-6">
                            {!! FormHelper::formTextarea([
                                'name' => 'bill_from',
                                'id' => 'bill_from',
                                'label' => 'Bill From',
                                'textarea_class' => 'noval',
                                'value' => siteSetting("company_name").' '.siteSetting("company_address"),
                                'rows' => 4,
                                'cols' => 50,
                                'required' => true,
                                'value' => $record->bill_from,
                                          
                            ]) !!}                     
                        </div>
                        <div class="col-xl-6">
                            {!! FormHelper::formDatepicker([
                                'label' => 'Invoice Date',
                                'name' => 'invoice_date',
                                'id' => 'invoice_date',
                                'class' => 'select2-input ga-country',
                                'required' => true,
                                'value' => $record->invoice_date
                            ]) !!}
                        </div>
                        <div class="col-xl-6">
                            {!! FormHelper::formDatepicker([
                                'label' => 'Due Date',
                                'name' => 'due_date',
                                'id' => 'due_date',
                                'class' => 'select2-input ga-country',
                                'required' => true,
                                'value' => $record->due_date
                            ]) !!}
                        </div>
                        <div class="col-xl-12 mb-3">
                            <table class="cds-table">
                                <thead>
                                    <tr style="background-color: #f8f9fa;">
                                        <th>PARTICULAR</th>
                                        <th>AMOUNT</th>
                                        <th>Discount Type</th>
                                        <th>Discount</th>
                                        <th>SubTotal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="items-list">
                                    <!-- Dynamic items will be appended here -->
                                    @if($record->invoiceItems->isNotEmpty())
                                        @foreach($record->invoiceItems as $value)
                                        <tr>
                                            <input type="hidden" name="items[{{$value->id}}][id]" value="{{$value->id}}" />
                                            <td><input type="text" class="item-name form-control" name="items[{{$value->id}}][name]" placeholder="Enter item name" value="{{$value->particular}}" /></td>
                                            <td><input type="number" name="items[{{$value->id}}][amount]" class="item-amount form-control" min="0" value="{{$value->amount}}" /></td>
                                            <td>
                                                <div class="cds-offerbox">
                                                    <select class="form-control discount-type no-select2" name="items[{{$value->id}}][discount_type]">
                                                        <option value="">Select Discount Type</option>
                                                        <option value="per" {{$value->discount_type == 'per' ? 'selected' : ''}}>In Percentage %</option>
                                                        <option value="amount" {{$value->discount_type == 'amount' ? 'selected' : ''}}>Amount</option>
                                                    </select>
                                                    <input type="text" class="discount form-control" name="items[{{$value->id}}][discount]" value="{{$value->discount}}" placeholder="Enter text" />
                                                </div>
                                            </td>
                                            <td><input type="text" class="discount-price form-control" name="items[{{$value->id}}][discount]" placeholder="Enter Discount" value="{{$value->discount}}" disabled /></td>
                                            <td><input type="text" class="row-sub-total form-control" name="items[{{$value->id}}][row_sub_total]" value="{{$value->discount}}" disabled /></td>
                                            <td><span class="invoice-remove-btn">X</span></td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        
                            <br>
                            <a href="javascript:void(0);" id="add-item-btn" style="color: blue; text-decoration: none; font-weight: bold;">+ Add item</a>
                        
                            <div class="items_error_div text-danger"></div>
                            <div class="items_amount_error_div text-danger"></div>
                        </div>
                        <div class="col-xl-12">
                            <div class="card p-4 shadow-sm">
                                <h5 class="mb-3 font20">💼 Invoice Summary</h5>
                                <div class="mb-3 row align-items-center">
                                    <label class="col-sm-4 fw-bold">Subtotal:</label>
                                    <div class="col-sm-8 py-2 py-md-0">
                                        <div class="form-control-plaintext">&#x24;<span id="subtotal">{{$record->sub_total}}</span></div>
                                    </div>
                                </div>
                                <div class="mb-3 row align-items-center">
                                    <label class="col-sm-4 fw-bold">Discount Type:</label>
                                    <div class="col-sm-8 py-2 py-md-0">
                                        <select class="total-discount-type form-select no-select2" name="total_discount_type">
                                            <option value="">Select Discount Type</option>
                                            <option value="per" {{$record->discount_type == 'per' ? 'selected' : ''}}>In Percentage %</option>
                                            <option value="amount" {{$record->discount_type == 'amount' ? 'selected' : ''}}>Amount</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row align-items-center">
                                    <label class="col-sm-4 fw-bold">Total Discount:</label>
                                    <div class="col-sm-8 py-2 py-md-0 d-flex align-items-center">
                                        <div class="total-discount-div me-2">
                                            <input type="text" name="total_discount" name="discount" class="form-control total-discount" value="{{$record->discount}}">
                                        </div>
                                        <span class="me-2">📉</span> &#x24;<span id="total-discount-price">{{$record->discount}}</span>                                        
                                    </div>
                                </div>
                                <div class="mb-3 row align-items-center">
                                    <label class="col-sm-4 fw-bold">Tax (%):</label>
                                    <div class="col-sm-8 py-2 py-md-0 d-flex align-items-center">
                                        <input type="text" name="tax" class="form-control invoice-tax" value="{{$record->tax}}">
                                        <div class="d-flex flex-shrink-0 ms-2">
                                            → &#x24;<span class="invoice-tax-result">0</span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row align-items-center">
                                    <label class="col-sm-4 fw-bold">Total Amount:</label>
                                    <div class="col-sm-8 fw-bold fs-5 text-success">
                                        &#x24; <span id="total-amount">{{$record->total_amount}}</span>
                                    </div>
                                </div>
                                <!-- Hidden Inputs -->
                                <input type="hidden" name="sub_total" id="sub_total" value="{{$record->sub_total}}">
                                <input type="hidden" name="total_amount" id="total_amount" value="{{$record->total_amount}}">
                            </div>

                            <div style="float:right;">
                                <!-- <p><strong>Subtotal:</strong> ₹<span id="subtotal">{{$record->sub_total}}</span></p> -->
                                
                                <!-- <p><strong>Discount:</strong></p><select class="total-discount-type" name="total_discount_type"><option value="">Select Discount Type</option><option value="per" {{$record->discount_type == 'per' ? 'selected' : ''}}>In Percentage %</option><option value="amount" {{$record->discount_type == 'amount' ? 'selected' : ''}}>Amount</option></select> -->
                               
                                <!-- <div class="total-discount-div">
                                    <input type="text" name="total_discount" name="discount" class="form-control total-discount" value="{{$record->discount}}">
                                </div>
                                
                                <p><strong>Total Discount:</strong> ₹<span id="total-discount-price">{{$record->discount}}</span></p> -->
                                
                                <!-- <p><strong>Tax:</strong> %<input type="text" name="tax" class="invoice-tax" value="{{$record->tax}}"></p> -->
                                
                                <!-- <p><strong>Total Amount:</strong> ₹<span id="total-amount">{{$record->total_amount}}</span></p> -->
                                
                                <!-- <input type="hidden" name="sub_total" id="sub_total" value="{{$record->sub_total}}">
                                
                                <input type="hidden" name="total_amount" id="total_amount" value="{{$record->total_amount}}"> -->
                            </div>
                            <br>
                        </div>
                        <div class="col-xl-12 col-md-12 col-lg-12 mt-3">
                            {!! FormHelper::formTextarea([
                                'name' => 'note_terms',
                                'id' => 'note_terms',
                                'label' => 'Note & Terms',
                                'textarea_class' => 'noval',
                                'rows' => 4,
                                'cols' => 50,
                                'value' => $record->notes,
                
                            ]) !!}                     
                        </div>
                    </div>
                    <div class="text-start">
                        <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                    </div>
                </form>
            </div>
    
			</div>
	
	</div>
  </div>
</div>


@endsection
<!-- End Content -->
@section('javascript')
<script>
    $(document).ready(function() {
  
const invoiceInput = document.getElementById("invoice_date");
const dueDateInput = document.getElementById("due_date");

//  Use existing values from backend or fallback to today
const savedInvoiceDate = invoiceInput.value || new Date().toISOString().split('T')[0];
const savedDueDate = dueDateInput.value || new Date().toISOString().split('T')[0];

//  Initialize Invoice Date Picker
const invoicesDatePicker = CustomCalendarWidget.initialize("invoice_date", {
    inline: false,
    minDate: new Date(), // today onward
    dateFormat: "Y-m-d",
    defaultDate: savedInvoiceDate,
    onDateSelect: function(selectedDateStr) {
        const selectedDate = new Date(selectedDateStr);
        const currentDueDate = new Date(dueDateInput.value);

        // Remove wrapper (flatpickr or custom container)
        const wrapper = dueDateInput.nextElementSibling;
        if (wrapper && wrapper.classList.contains('CDSComponents-Calender-inline01-container')) {
            wrapper.remove();
        }

        // Re-init Due Date picker with updated minDate
        dueDatePicker = CustomCalendarWidget.initialize("due_date", {
            inline: false,
            minDate: selectedDateStr,
            dateFormat: "Y-m-d",
            defaultDate: currentDueDate >= selectedDate ? dueDateInput.value : selectedDateStr
        });

        // Auto-correct if invalid
        if (currentDueDate < selectedDate) {
            dueDateInput.value = selectedDateStr;
        }
    }
});

//  Initialize Due Date Picker on page load (edit mode support)
let validDueDate = new Date(savedDueDate) >= new Date(savedInvoiceDate) ? savedDueDate : savedInvoiceDate;

// Remove wrapper if already exists
const existingWrapper = dueDateInput.nextElementSibling;
if (existingWrapper && existingWrapper.classList.contains('CDSComponents-Calender-inline01-container')) {
    existingWrapper.remove();
}

// Setup Due Date Picker
let dueDatePicker = CustomCalendarWidget.initialize("due_date", {
    inline: false,
    minDate: savedInvoiceDate,
    dateFormat: "Y-m-d",
    defaultDate: validDueDate
});

// Fix input value if it's invalid
if (!dueDateInput.value || new Date(dueDateInput.value) < new Date(savedInvoiceDate)) {
    dueDateInput.value = savedInvoiceDate;
}

        let subtotal = 0; 
        $("#form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("form");
            if (!is_valid) {
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

                            if(key != 'items' || key != 'user_id'|| key!= 'invoice_date' || key != 'due_date' || key != 'bill_to' || key != 'bill_from'){
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
                error: function(xhr) {
                    internalError();
                                                 if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
            validation(xhr.responseJSON.message);
        } else {
            errorMessage('An unexpected error occurred. Please try again.');
        }
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
            let row = `<tr >
                <td><input type="hidden" name="items[${rowCount}][id]"/><input type="text" class="item-name form-control" name="items[${rowCount}][name]" placeholder="Enter item name"></td>
                <td><input type="number" name="items[${rowCount}][amount]" class="item-amount form-control" min="0" value="0"></td>
                <td>
                    <div class="cds-offerbox">
                        <select class="form-control discount-type" name="items[${rowCount}][discount_type]">
                            <option value="">Select Discount Type</option>
                            <option value="per">In Percentage %</option>
                            <option value="amount">Amount</option>
                        </select>
                    </div>
                </td>
                <td><input type="text" class="discount-price form-control" name="items[${rowCount}][discount]" placeholder="Enter Discount" disabled></td>
                <td><input type="text" class="row-sub-total form-control" name="items[${rowCount}][row_sub_total]" disabled></td>
                <td><span class="invoice-remove-btn">X</span></td>
            </tr>`;
            $("#items-list").append(row);
        });

        
    });

    $(document).ready(function () {
        $(document).on("blur", ".item-amount", function () {
            updateRowSubtotal($(this));
            updateTotals();
        });

        $(document).on("click", ".invoice-remove-btn", function () {
            $(this).closest("tr").remove();
            updateTotals();
        });

        $(document).on("change", ".discount-type", function () {
            handleDiscountTypeChange($(this));
        });

        $(document).on("blur", ".discount", function () {
            updateDiscount($(this));
            updateTotals();
        });

        $(document).on("change", ".total-discount-type", function () {
            handleTotalDiscountTypeChange($(this));
        });

        $(document).on("blur", ".total-discount", function () {
            updateTotalDiscount();
        });

        $(document).on("blur", ".invoice-tax", function () {
            updateTax();
        });
    });

    function updateRowSubtotal(element) {
        let row = element.closest("tr");
        let amount = parseFloat(element.val()) || 0;
        let discountType = row.find(".discount-type").val();
        let discountValue = parseFloat(row.find(".discount").val()) || 0;
        let discountAmount = (discountType === 'per') ? (amount * discountValue) / 100 : discountValue;
        
        row.find(".discount-price").val(discountAmount);
        row.find(".row-sub-total").val(amount - discountAmount);
    }

    function handleDiscountTypeChange(element) {
        let discountField = '<input type="text" class="discount form-control" name="discount" placeholder="Enter discount">';
        let td = element.parent();
        td.find(".discount").remove();
        td.append(discountField);
    }

    function updateDiscount(element) {
        let row = element.closest("tr");
        let amount = parseFloat(row.find(".item-amount").val()) || 0;
        let discountType = row.find(".discount-type").val();
        let discountValue = parseFloat(element.val()) || 0;
        let discountAmount = (discountType === 'per') ? (amount * discountValue) / 100 : discountValue;
        
        row.find(".discount-price").val(discountAmount);
        row.find(".row-sub-total").val(amount - discountAmount);
    }

    function handleTotalDiscountTypeChange(element) {
        let discountField = '<input type="text" name="total_discount" class="form-control total-discount" value="0">';
        $(".total-discount-div").html(discountField);
    }

    function updateTotalDiscount() {
        let discountType = $(".total-discount-type").val();
        let subtotal = parseFloat($("#subtotal").text()) || 0;
        let discountValue = parseFloat($(".total-discount").val()) || 0;
        let discountAmount = (discountType === 'per') ? (subtotal * discountValue) / 100 : discountValue;
        
        $("#total-discount-price").text(discountAmount);
        updateTotals();
    }

    function updateTax() {
        let taxRate = parseFloat($(".invoice-tax").val()) || 0;
        let subtotal = parseFloat($("#subtotal").text()) || 0;
        let discount = parseFloat($("#total-discount-price").text()) || 0;
        let totalAfterDiscount = subtotal - discount;
        let taxAmount = (totalAfterDiscount * taxRate) / 100;
        
        $(".invoice-tax-result").text(taxAmount);
        $("#total-amount").text(totalAfterDiscount + taxAmount);
        $("#total_amount").val(totalAfterDiscount + taxAmount);
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
        
        $("#subtotal").text(subtotal);
        $("#sub_total").val(subtotal);
        $("#total-amount").text(totalAfterDiscount + taxAmount);
        $("#total_amount").val(totalAfterDiscount + taxAmount);
    }

</script>
@endsection