
<div class="CDSPostCaseDetail-modal-content">
    <div class="CDSPostCaseDetail-modal-header">
        <h2 class="CDSPostCaseDetail-modal-title">Submit Your Proposal</h2>
        <div class="CDSPostCaseDetail-modal-header-actions">
            <button class="CDSPostCaseDetail-btn-ai" onclick="generateProposal()">
                <span>✨</span>
                Generate Proposal Via AI
            </button>
            <button class="CDSPostCaseDetail-modal-close" onclick="closeEditProposalModal()">×</button>
        </div>
    </div>
    
    <form id="proposalForm" class="js-validate mt-3 editProposalForm" action="{{ baseUrl('/cases/update-proposal') }}" method="POST"> 
        @csrf
        <!-- Sub Service Type -->
        <input type="hidden" name="case_id" value="{{$record->id }}">
        <input type="hidden" name="case_comment_id" value="{{$comment->id}}">
        <div class="CDSPostCaseDetail-form-group">
            @php
                $options = [];
                if(!empty($sub_services)){
                    $options = $sub_services->map(function($value) {
                        return [
                            'id' => $value->id,
                            'name' => ($value->subServiceTypes->name ?? '') . ' [$' . number_format($value->consultancy_fees ?? 0, 2) . ']',
                        ];
                    })->toArray();
                }
            @endphp
            @if(!empty($sub_services))
                {!! FormHelper::formSelect([
                    'name' => 'sub_service_type_id',
                    'required' => true,
                    'label' => 'Sub service type',
                    'options' => $options,
                    'value_column' => 'id',
                    'label_column' => 'name',
                    'is_multiple' => false,
                    'select_class' => 'CDSPostCaseDetail-form-select',
                    'selected' => $comment->sub_service_type_id ?? ''
                ]) !!}
            @else
                <div class="alert alert-danger m-0">Please add sub service types for this services. <a href="{{ baseUrl('/my-services') }}">Click Here</a></div>
            @endif
        </div>

        <!-- Enter Description -->
        <div class="CDSPostCaseDetail-form-group">
            {!! FormHelper::formTextarea(['name'=>"description",
                'id'=>"edit-description",
                'required'=>true,
                "label"=>"Enter Description",
                'class'=>"cds-texteditor",
                'textarea_class'=>"noval",
                'value' =>  html_entity_decode($comment->comments ?? '') 
            ]) !!}
        </div>

        <!-- Quotation Section -->
        <div class="CDSPostCaseDetail-quotation-section">
            <h3 class="CDSPostCaseDetail-section-title">Quotation</h3>
            @if($quotations->isNotEmpty())
                <div class="CDSPostCaseDetail-form-group">
                    <label class="CDSPostCaseDetail-form-label">Select Quotation</label>
                    {!! FormHelper::formSelect([
                        'name' => 'quotation_id',
                        'label' => '',
                        'class' => 'select2-input ga-country',
                        'options' => $quotations,
                        'value_column' => 'id',
                        'label_column' => 'quotation_title',
                        "events" => ["onchange=fetchQuotation(this)"],
                        'is_multiple' => false,
                        'selected' => $case_quotations->quotation_id ?? '',
                        'select_class' =>'CDSPostCaseDetail-form-select'
                    ]) !!}
                </div>
                <!-- Quotation Table -->
                <div class="CDSPostCaseDetail-quotation-table-wrapper">
                    <table class="CDSPostCaseDetail-quotation-table">
                        <thead>
                            <tr>
                                <th>PARTICULAR</th>
                                <th>AMOUNT</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="editQuotationItems">
                            @if(!empty($case_quotations))
                                @foreach($case_quotations->particulars as $value)
                                    <tr>
                                        {{-- Hidden ID --}}
                                        <input type="hidden" name="items[{{ $value->id }}][id]" value="{{ $value->id }}" />
                                        
                                        <td>
                                            <input type="text"
                                                class="CDSPostCaseDetail-table-input item-name"
                                                name="items[{{ $value->id }}][name]"
                                                placeholder="Enter item name"
                                                value="{{ $value->particular }}">
                                        </td>

                                        <td>
                                            <input type="number"
                                                class="CDSPostCaseDetail-table-input CDSPostCaseDetail-amount-input edit-item-amount"
                                                name="items[{{ $value->id }}][amount]"
                                                min="0"
                                                step="0.01"
                                                value="{{ $value->amount }}">
                                        </td>

                                        <td class="CDSPostCaseDetail-total-cell">
                                            <span class="sub-total-span"> {{ number_format($value->amount, 2) }}</span><input type="hidden" class="edit-row-sub-total form-control" name="items[{{ $value->id }}][row_sub_total]" value="{{ number_format($value->amount, 2) }}" disabled>
                                           
                                        </td>

                                        <td>
                                            <button type="button"
                                                    class="CDSPostCaseDetail-remove-btn invoice-remove-btn"
                                                    onclick="removeQuotationItem(this)">
                                                X
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            {{-- JavaScript will append dynamic rows here --}}
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <a href="javascript:void(0);"
                                            class="CDSPostCaseDetail-add-item-btn"
                                            id="edit-item-btn">
                                        + Add Items
                                        </a>
                                </td>
                            </tr>
                            <tr class="CDSPostCaseDetail-total-row">
                                <td colspan="2" style="text-align: right; font-weight: 600;">Total:</td>
                                <td colspan="2" style="font-weight: 700; font-size: 18px;"><span  id="edit-subtotal"> </span></td>
                              
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Hidden subtotal and total fields --}}
                    <div class="js-form-message">
                        <input type="hidden" name="sub_total" id="sub_total">
                    </div>
                    <div class="js-form-message">
                        <input type="hidden" name="total_amount" id="total_amount" class="edit-total-amount">
                    </div>

                    {{-- Error containers --}}
                    <div class="items_error_div text-danger"></div>
                    <div class="items_amount_error_div text-danger"></div>

                </div>

            @else
                <div class="text-dark text-center">
                    No quotation is available for the service. Please add quotation. <a href="{{ baseUrl('/quotations/add') }}">Click Here</a>
                </div>

            @endif
        </div>

        <!-- Form Actions -->
        <div class="CDSPostCaseDetail-form-actions">
            @if(!empty($sub_services) && $quotations->isNotEmpty())
            <button type="submit" class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-primary CDSPostCaseDetail-btn-save">
                Save
            </button>
            @else
                <div class="alert-danger alert">You cannot send the proposal due to missing data</div>
            @endif
        </div>
    </form>
</div>


    <script>

@if (!empty($case_quotations->quotation_id))
        $(document).ready(function() {
            var selectedRadio = $('input[name="quotation_id"][value="{{ $case_quotations->quotation_id }}"]');
            if (selectedRadio.length) {
                fetchQuotation(selectedRadio[0]);
            }
        });
@endif
    $(document).ready(function() {
        initEditor("edit-description");
         updateTotals();
          let subtotal = 0; 
        $(".editProposalForm").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("editProposalForm");
            if (!is_valid) {
                return false;
            }
            var formData = new FormData($(this)[0]);
            var url = $(".editProposalForm").attr('action');
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
        $("#edit-item-btn").click(function () {
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
          
            let rowCount = $("#editQuotationItems tr").length; // Get row count to make uniq
            let row = `<tr class="js-form-message">
                <td><input type="text" class="item-name form-control" name="items[${rowCount}][name]" placeholder="Enter item name"></td>
                <td><input type="number" name="items[${rowCount}][amount]" class="edit-item-amount form-control" min="0" value="0"></td>
                <td class="CDSPostCaseDetail-total-cell"><span class="sub-total-span"></span><input type="hidden" class="edit-row-sub-total form-control" name="items[${rowCount}][row_sub_total]" disabled></td>
                <td><button type="button" class="CDSPostCaseDetail-remove-btn invoice-remove-btn" onclick="removeQuotationItem(this)">
                                                X
                                            </button></td>
            </tr>`;
            $("#editQuotationItems").append(row);
        });

       
        $(document).on("blur", ".edit-item-amount", function () {
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
        row.find(".edit-row-sub-total").val(amount);
        row.find(".sub-total-span").html(amount);
    }

    function updateTotals() {
        let subtotals = 0;
        // $(".edit-row-sub-total").each(function () {
        //     subtotals += parseFloat($(this).val()) || 0;
        // });
        $(".edit-row-sub-total").each(function () {
            let val = $(this).is('input') ? $(this).val() : $(this).text();
            console.log("Row subtotal:", val);
            subtotals += parseFloat(val) || 0;
        });

        console.log(subtotals);
        let totalDiscount = parseFloat($("#total-discount-price").text()) || 0;
        let taxRate = parseFloat($(".invoice-tax").val()) || 0;
        let totalAfterDiscount = subtotals - totalDiscount;
        let taxAmount = (totalAfterDiscount * taxRate) / 100;
        let totalAmount = (totalAfterDiscount + taxAmount).toFixed(2);
        $("#edit-subtotal").text('$'+subtotals);
        $("#sub_total").val(subtotals);
        $("#total-amount").text(totalAmount);
        $(".edit-total-amount").val(subtotals);
    }

 
   

    function fetchQuotation(e){
        if($(e).val() != ''){
            $.ajax({
                url: '{{ baseUrl('cases/fetch-quotation') }}',
                type: "post",
                data: {
                    _token:csrf_token,
                    quotation_id:$(e).val()
                },
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
                    if (response.status == true) {
                        $("#editQuotationItems").html(response.contents);
                        setTimeout(() => {
                            updateTotals();
                        }, 800);
                    } else {
                        errorMessage(response.message);
                    }
                },
                error: function() {
                    internalError();
                }
            });
        }else{
            $("#editQuotationItems").html('');
        }
        
    }

  
</script>
