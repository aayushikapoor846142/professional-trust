<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaseAgreement;
use App\Models\Forms;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use View;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\ProfessionalAssociateAgreement;

class CaseAgreementController extends Controller
{
    /**
     * Show the form for creating a new agreement for an associate
     *
     * @param string $associate_id
     * @return \Illuminate\View\View
     */
    public function create($associate_id)
    {
        try {
            // Get the associate
            $associate = User::where('unique_id', $associate_id)
                           ->where('role', 'associate')
                           ->first();

            if (!$associate) {
                abort(404, 'Associate not found');
            }

            // Get the default template (id = 1) from CaseAgreement table
            $template = CaseAgreement::where('slug','professional-agreement')->first();
            
            if (!$template) {
                // If no template found, create a default one
                $template = $this->createDefaultTemplate();
            }

            // Replace placeholders in template content for display
            $displayContent = $template->agreement_content;
            $displayContent = str_replace('{PROFESSIONAL_NAME}', auth()->user()->first_name . ' ' . auth()->user()->last_name, $displayContent);
            $displayContent = str_replace('{ASSOCIATE_NAME}', $associate->first_name . ' ' . $associate->last_name, $displayContent);
            $displayContent = str_replace('{CURRENT_DATE}', date('F d, Y'), $displayContent);
            $displayContent = str_replace('{AGREEMENT_ID}', $template->agreement_no, $displayContent);
            $displayContent = str_replace('{PLATFORM_FEES}', '$' . number_format($template->platform_fees, 2), $displayContent);
            // $displayContent = str_replace('{SHARING_FEES}', '0%', $displayContent); // Will be updated via JavaScript

            $viewData = [
                'pageTitle' => 'Create Agreement for ' . $associate->first_name . ' ' . $associate->last_name,
                'associate' => $associate,
                'template' => $template,
                'displayContent' => $displayContent
            ];

            return view('admin-panel.06-roles.associate.agreement.create', $viewData);

        } catch (\Exception $e) {
            abort(500, 'Error loading agreement form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created agreement
     *
     * @param \Illuminate\Http\Request $request
     * @param string $associate_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $unique_id)
    {
        try {
        $validator = Validator::make($request->all(), [
            'agreement_content' => 'required|string',
            'associate_id' => 'required|string',
            'sharing_fees_percentage' => 'required|numeric|min:0|max:100'
        ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            // Get the associate
            $associate = User::where('unique_id', $request->associate_id)
                           ->where('role', 'associate')
                           ->first();

            if (!$associate) {
                return response()->json([
                    'status' => false,
                    'message' => 'Associate not found'
                ]);
            }
            $template = CaseAgreement::where('unique_id',$unique_id)->first();
            // Create the agreement
            $agreement = new ProfessionalAssociateAgreement();
            $agreement->template_name = 'Agreement for ' . $associate->first_name . ' ' . $associate->last_name;
            $agreement->agreement_id = $template->id;
            $agreement->original_agreement = $template->agreement_content;
            $agreementContent = $request->agreement_content;
            $agreementContent = str_replace('{PROFESSIONAL_NAME}', auth()->user()->first_name . ' ' . auth()->user()->last_name, $agreementContent);
            
            $agreement->agreement = $agreementContent;
            $agreement->platform_fees = $template->platform_fees;
            $agreement->sharing_fees = $request->sharing_fees_percentage;
            $agreement->added_by = auth()->user()->id;
            $agreement->professional_id = auth()->user()->id;
            $agreement->associate_id = $associate->id;
            $agreement->save();

            return response()->json([
                    'status' => true,
                    'message' => 'Agreement created successfully and PDF generated',
                    'redirect_back' => baseUrl('associates'),
                    
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creating agreement: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate PDF for the agreement
     *
     * @param ProfessionalAssociateAgreement $agreement
     * @param User $associate
     * @param CaseAgreement $template
     * @return \Barryvdh\DomPDF\Facade\Pdf
     */
    private function generateAgreementPDF($agreement, $associate, $template)
    {
        // Prepare data for PDF view
        $pdfData = [
            'agreement' => $agreement,
            'associate' => $associate,
            'template' => $template,
            'professional' => auth()->user(),
            'generated_date' => now()->format('F d, Y'),
            'agreement_id' => $agreement->id
        ];

        // Generate PDF using the agreement content
        $pdf = PDF::loadView('admin-panel.06-roles.associate.agreement.pdf', $pdfData);
        
        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        return $pdf;
    }

    /**
     * Create a default template if none exists
     *
     * @return CaseAgreement
     */
    private function createDefaultTemplate()
    {
        $template = new CaseAgreement();
        $template->unique_id = CaseAgreement::generateUniqueId();
        $template->template_name = 'Default Associate Agreement Template';
        $template->agreement_content = 'This is a default agreement template. Please customize the content as needed.';
        $template->platform_fees = 0.00;
        $template->status = 'active';
        $template->created_by = Auth::user()->id;
        $template->updated_by = Auth::user()->id;
        $template->save();

        return $template;
    }

    public function view($id){

        $viewData['pageTitle'] = "View Agreement";
        $viewData['agreement'] = ProfessionalAssociateAgreement::where('unique_id',$id)->first();
        
        if ($viewData['agreement']) {
            // Load comments with replies
            $viewData['agreement']->load('comments.user', 'comments.allReplies.user');
        }
        
        return view('admin-panel.06-roles.associate.agreement.view', $viewData);
    }


    public function downloadPdf($id)
    {
        try {
            // Find the agreement by unique_id
            $agreement = ProfessionalAssociateAgreement::where('unique_id', $id)->first();
            
            if (!$agreement) {
                abort(404, 'Agreement not found');
            }

            // Validate agreement data
            if (empty($agreement->agreement)) {
                abort(400, 'Agreement content is empty');
            }

            // Get professional and associate details
            $professional = User::find($agreement->professional_id);
            $associate = User::find($agreement->associate_id);

            // Prepare data for PDF
            $data = [
                'agreement' => $agreement,
                'professional' => $professional,
                'associate' => $associate,
                'generated_at' => now()->format('F d, Y \a\t g:i A')
            ];

            // Generate PDF using the agreement content
            $pdf = Pdf::loadView('admin-panel.06-roles.associate.agreement.pdf-template', $data);
            
            // Set PDF options for better rendering
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial',
                'chroot' => public_path(),
                'tempDir' => storage_path('app/temp'),
                'dpi' => 150,
                'defaultMediaType' => 'screen',
                'isFontSubsettingEnabled' => true
            ]);

            // Generate filename with timestamp for uniqueness
            $filename = 'Professional_Agreement_' . $agreement->unique_id . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Clean filename for safe download
            $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

            // Return PDF for download without storing
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('PDF generation error: ' . $e->getMessage(), [
                'agreement_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Error generating PDF: ' . $e->getMessage());
        }
    }
}
