@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')

@section('case-container')
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
<div class="container">
    <div class="cds-ty-dashboard-box">
        <div class="cds-ty-dashboard-box-header">
        </div>
        <div class="cds-ty-dashboard-box-body">
            <div class="cds-invoices">
                <form id="form" class="js-validate mt-3" action="{{ baseUrl('case-with-professionals/invoices/save/' . $case->unique_id)}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-xl-6 col-md-6 col-lg-6">
                            {!! FormHelper::formInputText([ 
                                'label' => "Name", 
                                'readonly' => true, 
                                'value' =>$case->clients->first_name.' '.$case->clients->last_name,
                                ]) 
                            !!}
                        </div>
                        <div class="col-xl-6 col-md-6 col-lg-6">
                            <div class="cds-selectbox">
                                {!! FormHelper::formSelect([
                                'name' => 'currency',
                                'id' => 'currency',
                                'label' => 'Select Currency',
                                'class' => 'select2-input ga-country',
                                'required' => true,
                                'options' => FormHelper::supportCurrency(),
                                'value_column' => 'value',
                                'label_column' => 'label',
                                'selected' => '',
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
                              
                            ]) !!}                     
                        </div>
                        <div class="col-xl-6">
                            {!! FormHelper::formDatepicker([
                                'label' => 'Invoice Date',
                                'name' => 'invoice_date',
                                'id' => 'invoice_date',
                                'class' => 'select2-input ga-country',
                                'required' => true,
                            ]) !!}
                        </div>
                        <div class="col-xl-6">
                            {!! FormHelper::formDatepicker([
                                'label' => 'Due Date',
                                'name' => 'due_date',
                                'id' => 'due_date',
                                'class' => 'select2-input ga-country',
                                'required' => true,
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
                                </tbody>
                            </table>
                        
                            <br>
                            <a href="javascript:void(0);" id="add-item-btn" style="color: blue; text-decoration: none; font-weight: bold;">+ Add item</a>
                            <hr>
                            
                            <div class="items_error_div text-danger"></div>
                            <div class="items_amount_error_div text-danger"></div>
                        </div>
                        <div class="col-xl-12">
                            <div class="card p-4 shadow-sm">
                                <h5 class="mb-3 font20">💼 Invoice Summary</h5>

                                <div class="mb-3 row align-items-center">
                                    <label class="col-sm-4 fw-bold">Subtotal:</label>
                                    <div class="col-sm-8 py-2 py-md-0">
                                        <div class="form-control-plaintext">&#x24;<span id="subtotal">0</span></div>
                                    </div>
                                </div>

                                <div class="mb-3 row align-items-center">
                                    <label class="col-sm-4 fw-bold">Discount Type:</label>
                                    <div class="col-sm-8 py-2 py-md-0">
                                        <select class="total-discount-type form-select no-select2" name="total_discount_type">
                                            <option value="">Select Discount Type</option>
                                            <option value="per">In Percentage %</option>
                                            <option value="amount">Amount</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row align-items-center">
                                    <label class="col-sm-4 fw-bold">Total Discount:</label>
                                    <div class="col-sm-8 py-2 py-md-0 d-flex align-items-center">
                                        <div class="total-discount-div me-2"></div> <span class="me-2">📉</span> &#x24;<span id="total-discount-price">0</span>
                                    </div>
                                </div>

                                <div class="mb-3 row align-items-center">
                                    <label class="col-sm-4 fw-bold">Tax (%):</label>
                                    <div class="col-sm-8 py-2 py-md-0 d-flex align-items-center">
                                        <input type="text" name="tax" class="form-control invoice-tax" value="0">
                                        <div class="d-flex flex-shrink-0 ms-2">
                                            → &#x24;<span class="invoice-tax-result">0</span>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row align-items-center">
                                    <label class="col-sm-4 fw-bold">Total Amount:</label>
                                    <div class="col-sm-8 fw-bold fs-5 text-success">
                                        &#x24; <span id="total-amount">0</span>
                                    </div>
                                </div>

                                <!-- Hidden Inputs -->
                                <input type="hidden" name="sub_total" id="sub_total">
                                <input type="hidden" name="total_amount" id="total_amount">
                            </div>


                            {{--<div class="invoice-summary-grid">
                                <div class="label">Subtotal:</div>
                                <div class="value">&#x24;<span id="subtotal">0</span></div>

                                <div class="label">Discount:</div>
                                <div class="value">
                                    <select class="total-discount-type form-select no-select2" name="total_discount_type">
                                        <option value="">Select Discount Type</option>
                                        <option value="per">In Percentage %</option>
                                        <option value="amount">Amount</option>
                                    </select>
                                </div>
                                <div class="label">Total Discount:</div>
                                <div class="value">
                                    <div class="d-flex align-items-center">
                                        <div class="total-discount-div me-2"></div> &#x24;<span id="total-discount-price">0</span>
                                    </div>
                                </div>
                                <div class="label">Tax (%):</div>
                                <div class="value d-flex align-items-center gap-2">
                                    <input type="text" name="tax" class="form-control invoice-tax" value="0">
                                    <span class="invoice-tax-result">&#x24;0</span>
                                </div>
                                <div class="label">Total Amount:</div>
                                <div class="value">&#x24;<span id="total-amount">0</span></div>
                                <!-- Hidden inputs -->
                                <input type="hidden" name="sub_total" id="sub_total">
                                <input type="hidden" name="total_amount" id="total_amount">
                            </div>--}}
                        </div>
                        <div class="col-xl-12 col-md-12 col-lg-12 mt-3">
                            {!! FormHelper::formTextarea([
                                'name' => 'note_terms',
                                'id' => 'note_terms',
                                'label' => 'Note & Terms',
                                'textarea_class' => 'noval',
                                'rows' => 4,
                                'cols' => 50,
                              
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

@endsection
<!-- End Content -->
@section('javascript')
<script>
    // Initialize Invoice Date picker

   

    $(document).ready(function() {
      const invoicesDatePicker = CustomCalendarWidget.initialize("invoice_date", {
    inline: false,
    minDate: new Date(), // Today onward
    dateFormat: "Y-m-d",
    onDateSelect: function(selectedDateStr) {
        // Destroy and recreate due date picker with new minDate
        const dueDateInput = document.getElementById("due_date");
        const currentDueDate = dueDateInput.value;
        
        // Destroy existing instance
        const wrapper = document.querySelector('#due_date').nextElementSibling;
        if (wrapper && wrapper.classList.contains('CDSComponents-Calender-inline01-container')) {
            wrapper.remove();
        }
        
        // Reinitialize with new minDate
        dueDatePicker = CustomCalendarWidget.initialize("due_date", {
            inline: false,
            minDate: selectedDateStr,
            dateFormat: "Y-m-d",
            defaultDate: currentDueDate >= selectedDateStr ? currentDueDate : selectedDateStr
        });
        
        // Update value if current is invalid
        if (new Date(currentDueDate) < new Date(selectedDateStr)) {
            dueDateInput.value = selectedDateStr;
        }
    }
});

// Initialize Due Date picker
let dueDatePicker = CustomCalendarWidget.initialize("due_date", {
    inline: false,
    minDate: new Date(), // Today onward
    dateFormat: "Y-m-d"
});

// Set initial due date to today
document.getElementById("due_date").value = new Date().toISOString().split('T')[0];
         
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

                            if(key != 'items' || key != 'user_id'|| key!= 'invoice_date' || key != 'due_date' || key != 'bill_to' || key != 'bill_from' || key != 'total_amount'){
                                if(key == "total_amount"){
                                    let inputField = $("[name='" + key + "']");
                                    if (inputField.length > 0) {
                                        inputField.after(`<span class="invoice-error-message" style="color: red;">${value}</span>`);
                                    }
                                }else{
                                    let fieldName = key.replace(/\.(\d+)\./g, "[$1][").replace(/\.(\w+)/g, "[$1]");
                                    // Ensure proper closing brackets
                                    fieldName = fieldName.replace(/\]$/, '') + ']';

                                    let inputField = $("[name='" + fieldName + "']");
                                
                                    if (inputField.length > 0) {
                                        inputField.after(`<span class="invoice-error-message" style="color: red;">${value}</span>`);
                                    }
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
            let row = `<tr class="js-form-message">
                <td><input type="text" class="item-name form-control" name="items[${rowCount}][name]" placeholder="Enter item name"></td>
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