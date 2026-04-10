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
<div class="container-fluid">
    
   <div class="ch-action">
            <a href="{{ baseUrl('invoices') }}" class="CdsTYButton-btn-primary">
            <i class="fa-left fa-solid me-1" aria-hidden="true"></i>
            Back
            </a>
        </div>
    
    <div class="cds-ty-dashboard-box">
        <div class="cds-ty-dashboard-box-header">
        </div>
        <div class="cds-ty-dashboard-box-body">
            <form id="form" class="js-validate mt-3" action="{{ baseUrl('/invoices/update/'.$record->unique_id) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-lg-12">
                        <div class="js-form-message">
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">Select User</option>
                            @foreach($users as $role => $roleUsers)
                                <optgroup label="{{ ucfirst($role) }}">
                                    @foreach($roleUsers as $user)
                                        <option value="{{ $user->id }}" {{ $record->user_id == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6 col-lg-6 mt-3">
                        {!! FormHelper::formTextarea([
                            'name' => 'bill_to',
                            'id' => 'bill_to',
                            'label' => 'Bill To',
                            'textarea_class' => 'noval',
                            'rows' => 4,
                            'cols' => 50,
                            'required' => true,
                            'value' => $record->bill_to
                        ]) !!}                     
                    </div>
                    <div class="col-xl-6 col-md-6 col-lg-6 mt-3">
                        {!! FormHelper::formTextarea([
                            'name' => 'bill_from',
                            'id' => 'bill_from',
                            'label' => 'Bill From',
                            'textarea_class' => 'noval',
                            'value' => siteSetting("company_name").' '.siteSetting("company_address"),
                            'rows' => 4,
                            'cols' => 50,
                            'required' => true,
                            'value' => $record->bill_from
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
                    <div class="col-xl-12 mt-3 mb-3">
                        <table>
                            <thead>
                                <tr style="background-color: #f8f9fa;">
                                    <th>PARTICULAR</th>
                                    <th>AMOUNT</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="items-list">
                                <!-- Dynamic items will be appended here -->
                                @if($record->invoiceItems->isNotEmpty())
                                    @foreach($record->invoiceItems as $value)
                                    <tr>
                                        <input type="hidden" name="items[{{$value->id}}][id]" value="{{$value->id}}" />
                                        <td><input type="text" class="item-name form-control" name="items[{{$value->id}}][name]" placeholder="Enter item name" value="{{$value->particular}}"></td>
                                        <td><input type="number" name="items[{{$value->id}}][amount]" class="item-amount form-control" min="0" value="{{$value->amount}}"></td>
                                        <td><span class="invoice-remove-btn">X</span></td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                      
                        <br>
                        <a href="javascript:void(0);" id="add-item-btn" style="color: blue; text-decoration: none; font-weight: bold;">+ Add item</a>
                        <hr>
                        <p><strong>Subtotal:</strong> ₹<span id="subtotal">{{$record->sub_total}}</span></p>
                        <p><strong>Total Amount:</strong> ₹<span id="total-amount">{{$record->total_amount}}</span></p>
                        <input type="hidden" name="sub_total" id="sub_total" value="{{$record->sub_total}}">
                        <input type="hidden" name="total_amount" id="total_amount" value="{{$record->total_amount}}">
                        <br>
                        <div class="items_error_div text-danger"></div>
                        <div class="items_amount_error_div text-danger"></div>
                    </div>
                    <div class="col-xl-12 col-md-12 col-lg-12 mt-3">
                        {!! FormHelper::formTextarea([
                            'name' => 'note_terms',
                            'id' => 'note_terms',
                            'label' => 'Note & Terms',
                            'textarea_class' => 'noval',
                            'rows' => 4,
                            'cols' => 50,
                            'value' => $record->notes
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

@endsection
<!-- End Content -->
@section('javascript')
<script>
    $(document).ready(function() {
        initDatePicker('invoice_date');
        initDatePicker('due_date');
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
            let row = `<tr >
                <td><input type="hidden" name="items[${rowCount}][id]"/><input type="text" class="item-name form-control" name="items[${rowCount}][name]" placeholder="Enter item name"></td>
                <td><input type="number" name="items[${rowCount}][amount]" class="item-amount form-control" min="0" value="0"></td>
                <td><span class="invoice-remove-btn">X</span></td>
            </tr>`;
            $("#items-list").append(row);
        });

        // Update subtotal on amount change
        $(document).on("blur", ".item-amount", function () {
            updateSubtotal();
        });

        // Remove item and update subtotal
        $(document).on("click", ".invoice-remove-btn", function () {
            $(this).closest("tr").remove();
            updateSubtotal();
        });

        // Function to update subtotal
        function updateSubtotal() {
            subtotal = 0;
            $(".item-amount").each(function () {
                let amount = parseFloat($(this).val()) || 0;
                subtotal += amount;
            });
            $("#subtotal").text(subtotal);
            $("#total-amount").text(subtotal);

            $("#sub_total").val(subtotal);
            $("#total_amount").val(subtotal);
        }

    });
</script>
@endsection