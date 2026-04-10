<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Exception;
use App\Models\ProfessionalJoiningRequest;

class AssociateService
{
    /**
     * Get professionals listing data with counts
     */
    public function getProfessionalsListingData($status = 'all')
    {
        try {
            $statusKeys = ['all','pending','accepted','rejected'];
            $appointmentsCount = [];
            
            foreach ($statusKeys as $key) {
                if ($key == 'all') {
                    $appointmentsCount['all'] = User::where('role', 'associate')
                        ->where('status', 'active')
                        ->count();
                }elseif($key == 'pending')
                {
                    $appointmentsCount['pending'] = ProfessionalJoiningRequest::where('professional_id',auth()->user()->id)->where('status',0)->count();
                } 
                elseif($key == 'accepted')
                {
                    $appointmentsCount['accepted'] = ProfessionalJoiningRequest::where('professional_id',auth()->user()->id)->where('status',1)->count();
                } 
                elseif($key == 'rejected')
                {
                    $appointmentsCount['rejected'] = ProfessionalJoiningRequest::where('professional_id',auth()->user()->id)->where('status',2)->count();
                } 
            }

            return [
                'success' => true,
                'data' => [
                    'appointmentsCount' => $appointmentsCount,
                    'pageTitle' => 'Associates',
                    'status' => $status
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error getting professionals listing data: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to retrieve professionals listing data'
            ];
        }
    }

    /**
     * Get professionals list via AJAX with search and filtering
     */
    public function getProfessionalsAjaxList(Request $request)
    {
        try {
            $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
            $sortDirection = $request->input('sort_direction', 'desc');
            $search = $request->input('search');
            $status = $request->input('status');

            $name = $request->name;
        $location = $request->location;
        $immigration_service_type = $request->immigration_service_type;
        $name_search = $request->name_search;
        $location_search = $request->location_search;
        $experience = $request->years_of_experience;
        $location_filter = $request->location_filter;
        $license_status = $request->license_status;
        $language = $request->language;
        $sort = $request->sort;
        
        $records = User::where('role','associate')->where('status','active');

            if ($status == 'accepted') {
                $professionalRequest = ProfessionalJoiningRequest::where('professional_id',auth()->user()->id)->where('status',1)->get()->pluck('associate_id')->toArray();
                $records->whereIn('id', $professionalRequest);
            }

            if ($status == 'pending') {
                $professionalRequest = ProfessionalJoiningRequest::where('professional_id',auth()->user()->id)->where('status',0)->get()->pluck('associate_id')->toArray();
                $records->whereIn('id', $professionalRequest);
            }

            if ($status == 'rejected') {
                $professionalRequest = ProfessionalJoiningRequest::where('professional_id',auth()->user()->id)->where('status',2)->get()->pluck('associate_id')->toArray();
                $records->whereIn('id', $professionalRequest);
            }

            $records = $records->paginate(5);
            $viewData = [
                'records' => $records,
                'current_page' => $records->currentPage() ?? 0,
                'last_page' => $records->lastPage() ?? 0,
                'next_page' => ($records->lastPage() ?? 0) != 0 ? ($records->currentPage() + 1) : 0
            ];

            $view = View::make('admin-panel.06-roles.associate.ajax-list', $viewData);
            $contents = $view->render();

            return [
                'success' => true,
                'data' => [
                    'contents' => $contents,
                    'last_page' => $records->lastPage(),
                    'current_page' => $records->currentPage(),
                    'total_records' => $records->total()
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error getting professionals AJAX list: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to retrieve professionals list'
            ];
        }
    }

    /**
     * Perform global search for professionals and companies
     */
    public function performGlobalSearch(Request $request)
    {
        try {
            $search = $request->search;
            $location = $request->location;

            $query = User::where('role', 'professional')
                ->where('status', 'active')
                ->whereHas('personalLocation', function ($q) use ($location) {
                    if ($location != '') {
                        $q->where(function ($subQuery) use ($location) {
                            $subQuery->where('city', 'LIKE', "%$location%")
                                ->orWhere('country', 'LIKE', "%$location%");
                        });
                    }
                })
                ->whereHas('cdsCompanyDetail')
                ->when($search != '', function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%$search%")
                            ->orWhere('last_name', 'LIKE', "%$search%");
                    });
                });

            $professionalTotalCount = (clone $query)->count();

            $professionals = $query->with(['personalLocation', 'cdsCompanyDetail'])
                ->orderBy('id', 'asc')
                ->limit(5)
                ->get()
                ->toArray();

            $count = count($professionals);

            $companiesQuery = CdsProfessionalCompany::whereHas('professional', fn ($q) => $q->where('status', 'active'))
                ->when($search !== '', function ($q) use ($search) {
                    $q->where('company_name', 'LIKE', "%{$search}%");
                })
                ->when($location !== '', function ($q) use ($location) {
                    $q->whereHas('professional.companyLocation', function ($q2) use ($location) {
                        $q2->where('city', 'LIKE', "%{$location}%")
                            ->orWhere('country', 'LIKE', "%{$location}%");
                    });
                });

            $companyTotalCount = (clone $companiesQuery)->count();

            $companies = $companiesQuery->with(['professional.companyLocation'])
                ->orderBy('id', 'asc')
                ->limit(5)
                ->get()
                ->toArray();

            $companies_count = count($companies);

            return [
                'success' => true,
                'data' => [
                    'professionals' => $professionals,
                    'companies' => $companies,
                    'professionalTotalCount' => $professionalTotalCount - $count,
                    'search' => $search,
                    'location' => $location,
                    'companyTotalCount' => $companyTotalCount - $companies_count,
                    'frontUrl' => mainTrustvisoryUrl()
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error performing global search: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to perform global search'
            ];
        }
    }

    /**
     * Remove connection invitation
     */
    public function removeConnection($invitation_id)
    {
        try {
            $checkInvite = ChatInvitation::where('unique_id', $invitation_id)->first();
            
            if (!$checkInvite) {
                return [
                    'success' => false,
                    'message' => 'Invitation not found'
                ];
            }

            if ($checkInvite->status == 0) {
                ChatInvitation::where('unique_id', $invitation_id)->delete();
                $user = User::where('email', $checkInvite->email)->first();
                
                if ($user) {
                    ChatRequest::where('sender_id', Auth::user()->id)
                        ->where('receiver_id', $user->id)
                        ->delete();
                    ChatRequest::where('receiver_id', Auth::user()->id)
                        ->where('sender_id', $user->id)
                        ->delete();
                    removeUserConnection($user->id, auth()->user()->id);
                }

                return [
                    'success' => true,
                    'message' => 'Invitation request is removed'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Cannot remove the invitation'
                ];
            }
        } catch (Exception $e) {
            Log::error('Error removing connection: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to remove connection'
            ];
        }
    }

    /**
     * Send connection request
     */
    public function sendConnection($user_id)
    {
        try {
            $user = User::where('id', $user_id)->first();
            $token = Str::random(64);
            $email = $user->email;
            
            $checkInvite = ChatInvitation::where('added_by', Auth::user()->id)
                ->where('email', $email)
                ->count();

            if ($checkInvite == 0) {
                if ($user) {
                    $checkInviteReverse = ChatInvitation::where('email', Auth::user()->email)
                        ->where('added_by', $user->id)
                        ->count();
                    
                    if ($checkInviteReverse == 0) {
                        ChatInvitation::create([
                            'email' => $email,
                            'token' => $token,
                            'added_by' => Auth::user()->id,
                        ]);

                        $chatRequest = ChatRequest::where('sender_id', Auth::user()->id)
                            ->where('receiver_id', $user->id)
                            ->count();
                        $chatRequestOther = ChatRequest::where('receiver_id', Auth::user()->id)
                            ->where('sender_id', $user->id)
                            ->count();
                        
                        if ($chatRequest < 1 && $chatRequestOther < 1) {
                            ChatRequest::create([
                                'unique_id' => randomNumber(),
                                'sender_id' => Auth::user()->id,
                                'receiver_id' => $user->id,
                                'is_accepted' => 0,
                            ]);
                        }

                        $sockett_data = [
                            'action' => 'new_chat_request',
                            'receiver_id' => $user->id,
                            'count' => chatReqstCount($user->id),
                        ];

                        initUserSocket($user->id, $sockett_data);

                        $mailData['professional_name'] = $user->first_name . ' ' . $user->last_name;
                        $mailData['sender_name'] = auth()->user()->first_name . ' ' . auth()->user()->last_name;
                        $mail_message = View::make('emails.chat_request_professional', $mailData);

                        $mailData['mail_message'] = $mail_message;
                        $parameter['to'] = $user->email;
                        $parameter['to_name'] = $user->first_name . ' ' . $user->last_name;
                        $parameter['message'] = $mail_message;
                        $parameter['subject'] = 'Received a Chat Request';
                        $parameter['view'] = 'emails.chat_request_professional';
                        $parameter['data'] = $mailData;
                        sendMail($parameter);

                        $arr_reply = [
                            'comment' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has sent you a connection request',
                            'type' => 'invite_request',
                            'redirect_link' => null,
                            'is_read' => 0,
                            'user_id' => $user->id ?? '',
                            'send_by' => auth()->user()->id ?? '',
                        ];
                        chatNotification(arr: $arr_reply);

                        return [
                            'success' => true,
                            'redirect_back' => baseUrl('connect'),
                            'message' => 'Connection Sent successfully'
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Connection Already Received'
                        ];
                    }
                } else {
                    ChatInvitation::create([
                        'email' => $email,
                        'token' => $token,
                        'added_by' => Auth::user()->id,
                    ]);

                    $mailData = [
                        'token' => $token,
                        'user' => Auth::user()->first_name . ' ' . Auth::user()->last_name
                    ];
                    $view = View::make('emails.chat-invitations', $mailData);
                    $message = $view->render();
                    $parameter = [
                        'to' => $email,
                        'message' => $message,
                        'subject' => 'Invitation for Chat',
                        'view' => 'emails.chat-invitations',
                        'data' => $mailData,
                    ];
                    sendMail($parameter);

                    return [
                        'success' => true,
                        'message' => 'Connection Sent successfully to a new user.'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Connection Already Sent'
                ];
            }
        } catch (Exception $e) {
            Log::error('Error sending connection: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send connection'
            ];
        }
    }

    /**
     * Follow back functionality
     */
    public function followBack(Request $request)
    {
        try {
            Follow::updateOrCreate([
                'follower_id' => $request->user_id,
                'followee_id' => auth()->user()->id,
            ]);

            FeedsConnection::updateOrCreate([
                'connection_with' => $request->user_id,
                'user_id' => auth()->user()->id,
                'connection_type' => 'follow',
            ], [
                'status' => 'active'
            ]);

            $arr_reply = [
                'comment' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has started following you now',
                'type' => 'invite_request',
                'redirect_link' => null,
                'is_read' => 0,
                'user_id' => $request->user_id ?? '',
                'send_by' => auth()->user()->id ?? '',
            ];
            chatNotification(arr: $arr_reply);

            checkUserConnection($request->user_id, auth()->user()->id, 'follow');

            return [
                'success' => true,
                'message' => 'You are now following'
            ];
        } catch (Exception $e) {
            Log::error('Error following back: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to follow back'
            ];
        }
    }

    /**
     * Remove connection/following
     */
    public function removeConnectionAndFollowing($user_id, $remove_connection)
    {
        try {
            $feed = FeedsConnection::where('user_id', auth()->user()->id)
                ->where('connection_with', $user_id)
                ->first();

            if (!empty($feed)) {
                DB::table('follows')->where('follower_id', $user_id)
                    ->where('followee_id', auth()->user()->id)
                    ->delete();
                $feed->delete();
                
                if ($remove_connection == 'yes') {
                    removeUserConnection($user_id, auth()->user()->id);
                }
            }

            return [
                'success' => true,
                'message' => 'Connection removed'
            ];
        } catch (Exception $e) {
            Log::error('Error removing connection: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to remove connection'
            ];
        }
    }

    /**
     * Get case with professionals data
     */
    public function getCaseWithProfessionalsData($id, $case_id = '')
    {
        try {
            $user = User::where('unique_id', $id)->first();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Professional not found'
                ];
            }

            $viewData = [
                'pageTitle' => 'Add Case',
                'professional_id' => $id,
                'services' => ImmigrationServices::with('subServices')
                    ->where('parent_service_id', '0')
                    ->whereHas('subServices')
                    ->get(),
                'last_saved' => ''
            ];

            if ($case_id != '') {
                $step = CaseWithProfessionals::where('unique_id', $case_id)->first();
                $viewData['case_id'] = $case_id;
                $viewData['completed_step'] = $step->completed_step;

                $professionalServices = ProfessionalSubServices::where('user_id', $step->professional_id)
                    ->where('service_id', $step->sub_service_id)
                    ->where('sub_services_type_id', $step->service_type_id)
                    ->first();

                $viewData['amount_to_pay'] = $professionalServices->consultancy_fees;
                $viewData['forms'] = Forms::where('id', $professionalServices->form_id)->first();
            } else {
                $viewData['case_id'] = '';
                $viewData['completed_step'] = 0;
                $viewData['amount_to_pay'] = 0;
                $viewData['forms'] = '';
            }

            return [
                'success' => true,
                'data' => $viewData
            ];
        } catch (Exception $e) {
            Log::error('Error getting case with professionals data: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to retrieve case data'
            ];
        }
    }

    /**
     * Get professional sub services
     */
    public function getProfessionalSubServices(Request $request)
    {
        try {
            $user = User::where('unique_id', $request->professional_id)->first();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Professional not found'
                ];
            }

            $ids = ProfessionalServices::where('user_id', $user->id)
                ->where('parent_service_id', $request->parent_service_id)
                ->get()
                ->pluck('service_id')
                ->toArray();
            
            $id = ProfessionalSubServices::whereIn('service_id', $ids)
                ->get()
                ->pluck('service_id')
                ->toArray();

            $subServices = ImmigrationServices::whereIn('id', $id)->get();

            return [
                'success' => true,
                'data' => $subServices
            ];
        } catch (Exception $e) {
            Log::error('Error getting professional sub services: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to retrieve sub services'
            ];
        }
    }

    /**
     * Get professional sub services types
     */
    public function getProfessionalSubServicesTypes(Request $request)
    {
        try {
            $user = User::where('unique_id', $request->professional_id)->first();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Professional not found'
                ];
            }

            $ids = ProfessionalSubServices::where('service_id', $request->service_id)
                ->where('user_id', $user->id)
                ->get()
                ->pluck('sub_services_type_id')
                ->toArray();

            $subServicesType = SubServicesTypes::whereIn('id', $ids)->get();

            return [
                'success' => true,
                'data' => $subServicesType
            ];
        } catch (Exception $e) {
            Log::error('Error getting professional sub services types: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to retrieve sub services types'
            ];
        }
    }

    /**
     * Save case with professionals
     */
    public function saveCaseWithProfessionals(Request $request)
    {
        try {
            if ($request->type == 'case_detail') {
                $validator = Validator::make($request->all(), [
                    'case_title' => 'required|max:255',
                    'parent_service_id' => 'required',
                    'case_description' => 'required',
                    'sub_service_id' => 'required',
                    'service_type_id' => 'required'
                ]);

                if ($validator->fails()) {
                    $error = $validator->errors()->toArray();
                    $errMsg = array();

                    foreach ($error as $key => $err) {
                        $errMsg[$key] = $err[0];
                    }

                    return [
                        'success' => false,
                        'message' => $errMsg
                    ];
                }

                $user = User::where('unique_id', $request->professional_id)->first();
                
                if (!$user) {
                    return [
                        'success' => false,
                        'message' => 'Professional not found'
                    ];
                }

                $professionalServices = ProfessionalSubServices::where('user_id', $user->id)
                    ->where('service_id', $request->sub_service_id)
                    ->where('sub_services_type_id', $request->service_type_id)
                    ->first();

                $status = 'draft';
                if ($professionalServices->form_id == '') {
                    if ($professionalServices->consultancy_fees == 0) {
                        $completed_step = 3;
                        $status = 'pending';
                    } else {
                        $completed_step = 2;
                    }
                } else {
                    $completed_step = 1;
                }

                $object = new CaseWithProfessionals();
                $object->case_title = $request->input('case_title');
                $object->case_description = htmlentities($request->input('case_description'));
                $object->parent_service_id = $request->input('parent_service_id');
                $object->sub_service_id = $request->input('sub_service_id');
                $object->service_type_id = $request->input('service_type_id');
                $object->professional_id = $user->id;
                $object->client_id = Auth::user()->id;
                $object->added_by = Auth::user()->id;
                $object->completed_step = $completed_step;
                $object->status = $status;
                $object->save();

                $professional_id = $user->id;
                checkUserConnection($professional_id, auth()->user()->id, 'case-with-professional');

                $response = [
                    'success' => true,
                    'message' => 'Case added successfully'
                ];

                if ($object->completed_step == 3) {
                    $response['redirect_back'] = baseUrl('professional-cases-success/' . $object->unique_id);
                } else {
                    $response['redirect_back'] = baseUrl('case-with-professionals/' . $request->professional_id . '/add/' . $object->unique_id);
                }

                return $response;
            } elseif ($request->type == 'assesment_form_detail') {
                $object = CaseWithProfessionals::where('unique_id', $request->case_id)->first();
                
                if (!$object) {
                    return [
                        'success' => false,
                        'message' => 'Case not found'
                    ];
                }

                $user = User::where('unique_id', $request->professional_id)->first();
                $professionalServices = ProfessionalSubServices::where('user_id', $user->id)
                    ->where('service_id', $object->sub_service_id)
                    ->where('sub_services_type_id', $object->service_type_id)
                    ->first();

                $status = 'draft';
                if ($professionalServices->consultancy_fees == 0) {
                    $completed_step = 3;
                    $status = 'pending';
                } else {
                    $completed_step = 2;
                }

                $object->form_json = $request->form_json;
                $object->form_reply_json = json_encode($request->input('fg_field'));
                $object->form_id = $request->form_id;
                $object->completed_step = $completed_step;
                $object->status = $status;
                $object->save();

                $response = [
                    'success' => true,
                    'message' => 'Case added successfully'
                ];

                if ($object->completed_step == 3) {
                    $response['redirect_back'] = baseUrl('professional-cases-success/' . $object->unique_id);
                } else {
                    $response['redirect_back'] = baseUrl('case-with-professionals/' . $request->professional_id . '/add/' . $object->unique_id);
                }

                return $response;
            }

            return [
                'success' => false,
                'message' => 'Invalid request type'
            ];
        } catch (Exception $e) {
            Log::error('Error saving case with professionals: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to save case'
            ];
        }
    }

    /**
     * Get case with professionals view data
     */
    public function getCaseWithProfessionalsViewData($id)
    {
        try {
            return [
                'success' => true,
                'data' => [
                    'pageTitle' => 'View Cases',
                    'professional_id' => $id
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error getting case view data: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to retrieve view data'
            ];
        }
    }

    /**
     * Get case with professionals AJAX list
     */
    public function getCaseWithProfessionalsAjaxList(Request $request)
    {
        try {
            $search = $request->input('search');
            $user = User::where('unique_id', $request->professional_id)->first();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Professional not found'
                ];
            }

            $user_id = $user->id;
            $records = CaseWithProfessionals::where(function ($query) use ($search, $user_id) {
                if ($search != '') {
                    $query->where('title', 'LIKE', '%' . $search . '%');
                }
                $query->where('professional_id', $user_id);
            })
                ->where('client_id', auth()->user()->id)
                ->orderBy('id', 'desc')
                ->paginate();

            $viewData = [
                'records' => $records,
                'total_records' => $records->total()
            ];

            $view = View::make('admin-panel.08-cases.professionals.view-ajax-list', $viewData);
            $contents = $view->render();

            return [
                'success' => true,
                'data' => [
                    'contents' => $contents,
                    'last_page' => $records->lastPage(),
                    'current_page' => $records->currentPage(),
                    'total_records' => $records->total(),
                    'professional_name' => $user->first_name . ' ' . $user->last_name
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error getting case AJAX list: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to retrieve case list'
            ];
        }
    }

    /**
     * Choose payment type for booking
     */
    public function choosePaymentTypeBooking(Request $request, $type, $professional_id, $appointment_booking_id, $amount_to_pay)
    {
        try {
            $viewData = [
                'pageTitle' => $type . ' Payment',
                'professional_id' => $professional_id,
                'appointment_booking_id' => $appointment_booking_id,
                'amount_to_pay' => $amount_to_pay
            ];

            if ($type == 'Stripe') {
                $view = View::make('admin-panel.03-appointments.appointment-system.appointment-booking.payment.stripe.stripe-payment', $viewData)->render();
            } elseif ($type == 'RazorPay') {
                $view = View::make('admin-panel.03-appointments.appointment-system.appointment-booking.payment.razorpay-payment', $viewData)->render();
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid payment type'
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'contents' => $view
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error choosing payment type for booking: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to load payment form'
            ];
        }
    }

    /**
     * Choose payment type for case
     */
    public function choosePaymentType(Request $request, $type, $professional_id, $case_id, $amount_to_pay)
    {
        try {
            $viewData = [
                'pageTitle' => $type . ' Payment',
                'professional_id' => $professional_id,
                'case_id' => $case_id,
                'amount_to_pay' => $amount_to_pay
            ];

            if ($type == 'Stripe') {
                $view = View::make('admin-panel.08-cases.professionals.stripe-payment', $viewData)->render();
            } elseif ($type == 'RazorPay') {
                $view = View::make('admin-panel.08-cases.professionals.razorpay-payment', $viewData)->render();
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid payment type'
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'contents' => $view
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error choosing payment type: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to load payment form'
            ];
        }
    }
} 