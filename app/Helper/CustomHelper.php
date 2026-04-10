<?php
use App\Models\CaseQuotation;
use App\Models\CdsRegulatoryBody;
use App\Models\ChatMessageRead;
use App\Models\DiscussionBoard;
use App\Models\Invoice;
use App\Models\KnowledgeBasePage;
use App\Models\Netbanking;
use App\Models\SiteSettings;
use App\Models\User;
use App\Models\WalletList;
use Carbon\Carbon;

use App\Models\StaffUser;
use App\Models\ProfessionalSubServices;
use App\Models\ApiKey;
use App\Models\EmailLog;
use App\Models\OtpVerify;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\RolePrevilege;
use Aws\S3\MultipartUploader;
use App\Models\HomeSettings;
use App\Models\GroupMessages;
use Illuminate\Support\Facades\Cache;
use Aws\Exception\MultipartUploadException;
use App\Models\SeoDetails;
use App\Models\UapProfessionals;
use App\Models\LevelTag;
use App\Models\UapLevelTag;
use App\Models\UapSitesScreenshot;
use App\Models\EvidenceComments;
use App\Models\CategoryLevels;
use App\Models\ReferenceUser;
use App\Models\Level;
use App\Models\UapProfessionalSites;
use App\Models\UapEvidences;
use GuzzleHttp\Client;
use App\Models\Country;
use App\Models\ChatMessage;
use App\Models\FeedFavourite;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use App\Models\CaseWithProfessionals;
use App\Models\FeedComments;
use App\Models\Roles;
use App\Models\Feeds;
use App\Models\SupportBonusPoint;
use App\Models\SupportAmounts;
use App\Models\PointEarn;
use App\Models\SupportBadge;
use Stripe\Stripe;
use Stripe\StripeClient;
use App\Models\UserSubscriptionHistory;
use App\Models\ChatNotification;
use App\Models\DiscussionCategory;
use App\Models\Module;
use Aws\S3\S3Client;
use App\Models\AwsFile;
use Illuminate\Support\Facades\File;
use App\Models\UserLoginActivity;
use App\Models\UserLocationAccessibility;
use App\Models\DocumentsFolder;
use App\Models\Forms;
use App\Models\Settings;
use App\Models\AppointmentBooking;
use App\Models\IpLog;
use App\Models\ProfessionalServices;
use App\Models\ServiceDocument;
use App\Models\UserPrivacySettingsLogs;
use App\Models\ModulePrivacyOptions;
use App\Models\ModulePrivacy;
use App\Models\UserConnection;
use App\Models\UserEarningsHistory;
use App\Models\FeedsConnection;
use App\Models\UserUtility;
use App\Models\ImmigrationServices;
use App\Models\MembershipPlanFeatureValue;
use App\Models\Ticket;
use App\Models\Cases;
use App\Models\Chat;
use App\Models\PaymentLinkParameter;
use Illuminate\Support\Facades\Crypt;

require dirname(__DIR__)."/../library/sendgrid/SendGridApi.php";

if(!function_exists("menuItems")){
    function menuItems(){
        
        $menuItems = [
            [
                'title' => 'Dashboard',
                'icon' => 'fa-regular fa-grid-2',
                'route' => 'panel',
                'menu-name' => 'dashboard',
                'url' => baseUrl('/'),
            ],
            [
                'title' => 'My Profile',
                'icon' => 'fa-regular fa-user-pen',
                'route' => 'panel.my-profile',
                'menu-name' => 'my-profile',
                'url' => baseUrl('/') . '/profile',
                'submenu' => [
                    [
                        'title' => 'Profile',
                        'icon' => 'list.png',
                        'menu-name' => 'profile',
                        'route'=>'panel.my-profile',
                        'privileges' => [
                            'route_prefix' => 'panel.my-profile',
                            'module' => 'panel.my-profile',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '/profile',
                    ],
                    [
                        'title' => 'Feeds',
                        'icon' => 'list.png',
                        'menu-name' => 'feeds',
                        'route'=>'panel.my-feeds.list',
                        'privileges' => [
                            'route_prefix' => 'panel.my-feeds',
                            'module' => 'professional-my-feeds',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '/my-feeds',
                        'submenu-name' => 'feeds',
                        'sub_submenu' => [
                            [
                                'title' => 'All Feeds ('.totalPostedFeed().')',
                                'menu-type' => '',
                                'url' => baseUrl('/') . '/my-feeds',
                                'icon' => '📄',
                                'menu-name' => 'group-messages',
                            ],
                            [
                                'title' => 'My Feeds ('.totalPostedFeed(auth()->user()->id).')',
                                'menu-type' => '',
                                'url' => baseUrl('/') . '/my-feeds/status/my-feeds',
                                'icon' => '📊',
                                'menu-name' => 'feeds',
                            ],[
                                'title' => 'Drafts ('.(draftFeeds(auth()->user()->id)??0).')',
                                'menu-type' => '',
                                'url' => baseUrl('/') . '/my-feeds/status/draft',
                                'icon' => '📝',
                                'menu-name' => 'feeds',
                            ],[
                                'title' => 'Scheduled ('.(scheduledFeed(auth()->user()->id)??0).')',
                                'menu-type' => '',
                                'url' => baseUrl('/') . '/my-feeds/status/scheduled',
                                'icon' => '📅',
                                'menu-name' => 'feeds',
                            ],[
                                'title' => 'Commented ('.(commentedFeed(auth()->user()->id)??0).')',
                                'menu-type' => '',
                                'icon' => '💬',
                                'url' => baseUrl('/') . '/my-feeds/status/commented',
                                'icon' => '💬',
                                'menu-name' => 'feeds',
                            ],[
                                'title' => 'Pinned ('.(pinnedFeeds(auth()->user()->id)??0).')',
                                'menu-type' => '',
                                'url' => baseUrl('/') . '/my-feeds/status/pinned',
                                'icon' => '📌',
                                'menu-name' => 'group-messages',
                            ],[
                                'title' => 'Favourite ('.(favoriteFeeds(auth()->user()->id)??0).')',
                                'menu-type' => '',
                                'url' => baseUrl('/') . '/my-feeds/status/favorites',
                                'icon' => '🌟',
                                'menu-name' => 'feeds',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'title' => 'My Services',
                'icon' => 'fa-regular fa-sliders',
                'menu-name' => 'manage-services',
                'route' => 'panel.manage-services.list',
                'privileges' => [
                'route_prefix' => 'panel.manage-services',
                    'module' => 'professional-manage-services',
                    'action' => 'list'
                ],
                'url' => baseUrl('/') . '/manage-services',
            ],
            [
                'title' => 'Associates',
                'icon' => 'fa-regular fa-sliders',
                'menu-name' => 'associates',
                'route' => 'panel.associates.list',
                'privileges' => [
                'route_prefix' => 'panel.associates',
                    'module' => 'professional-associates',
                    'action' => 'list'
                ],
                'url' => baseUrl('/') . '/associates',
            ],
            [
                'title' => 'Case Join Requests',
                'icon' => 'fa-regular fa-sliders',
                'menu-name' => 'case-join-requests',
                'route' => 'panel.case-join-requests.list',
                'privileges' => [
                'route_prefix' => 'panel.case-join-requests',
                    'module' => 'professional-case-join-requests',
                    'action' => 'list'
                ],
                'url' => baseUrl('/') . '/case-join-requests',
            ],
            [
                'title' => 'Accounts',
                'icon' => 'fa-regular fa fa-suitcase',
                'menu-name' => 'accounts',
                'submenu' => [
                    [
                        'title' => 'Roles',
                        'menu-name' => 'roles',
                        'route' => 'panel.roles.list',
                        'privileges' => [
                            'route_prefix' => 'panel.roles',
                            'module' => 'professional-roles',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '/roles',
                    ],
                    [
                        'title' => 'Role Previleges',
                        'route' => 'panel.role-privileges.list',
                        'menu-name' => 'role-previleges',
                        'url' => baseUrl('/') . '/role-privileges',
                        'privileges' => [
                            'route_prefix' => 'panel.role-privileges',
                            'module' => 'professional-module.role-privileges',
                            'action' => 'list'
                        ],
                    ],
                    [
                        'title' => "Active Staff's",
                        'url' => baseUrl('/') . '/staff',
                        'menu-name' => 'active-staff',
                        'route' => 'panel.staff.list',
                        'privileges' => [
                            'route_prefix' => 'panel.staff',
                            'module' => 'professional-staff',
                            'action' => 'list'
                        ],
                    ],
                ]
            ],
            [
                'title' => 'Cases',
                'icon' => 'fa-regular fa fa-file',
                'menu-name' => 'cases',
                'submenu' => [
                    [
                        'title' => 'Overview',
                        'icon' => 'list.png',
                        'menu-name' => 'cases-overview',
                        'route' => 'panel.case-with-professionals.overview',
                        'privileges' => [
                            'route_prefix' => 'panel.case-with-professionals',
                            'module' => 'professional-case-with-professionals',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '?tab=cases',
                    ],
                     [
                        'title' => 'Post Cases',
                        'url' => baseUrl('/') . '/cases',
                         'menu-name' => 'post-cases',
                         'route' => 'panel.cases.list',
                        'privileges' => [
                            'route_prefix' => 'panel.cases',
                            'module' => 'professional-cases',
                            'action' => 'list'
                        ],
                     ],
                    [
                        'title' => 'Case With Professionals',
                        'url' => baseUrl('/') . '/case-with-professionals',
                        'menu-name' => 'case-with-professionals',
                        'route' => 'panel.case-with-professionals.list',
                        'privileges' => [
                            'route_prefix' => 'panel.case-with-professionals',
                            'module' => 'professional-case-with-professionals',
                            'action' => 'list'
                        ],
                    ],
                    [
                        'title' => 'Predefined Case Stages',
                        'url' => baseUrl('/') . '/predefined-case-stages',
                        'menu-name' => 'predefined-case-stages',
                        'route' => 'panel.predefined-case-stages.list',
                        'privileges' => [
                            'route_prefix' => 'panel.predefined-case-stages',
                            'module' => 'professional-predefined-case-stages',
                            'action' => 'list'
                        ],
                    ],
                    [
                        'title' => 'Retainers',
                        'url' => baseUrl('/') . '/case-with-professionals/retainers',
                        'menu-name' => 'cases-retainers',
                        'route' => 'panel.case-with-professionals.retainers',
                        'privileges' => [
                            'route_prefix' => 'panel.retainers',
                            'module' => 'professional-retainers',
                            'action' => 'list'
                        ],
                    ],
                    [
                        'title' => 'Document Folders',
                        'menu-name' => 'document-folders',
                        'route' => 'panel.document-folders.list',
                        'privileges' => [
                            'route_prefix' => 'panel.document-folders',
                            'module' => 'professional-document-folders',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '/document-folders',
                    ],
                    [
                        'title' => 'Forms',
                        'route' => 'panel.forms.list',
                        'menu-name' => 'forms',
                        'privileges' => [
                            'route_prefix' => 'panel.forms',
                            'module' => 'professional-forms',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '/forms',
                    ],
                ],
            ],
            [
                'title' => 'Reviews',
                'icon' => 'fa-regular fa fa-star',
                'menu-name' => 'reviews',
                'submenu' => [
                    [
                        'title' => 'Overview',
                        'url' => baseUrl('/') . '?tab=review',
                        'menu-name' => 'review-overview',
                        'route' => 'panel.review-overview',
                        'privileges' => [
                            'route_prefix' => 'panel.review-overview',
                            'module' => 'professional-review-overview',
                            'action' => 'review-overview'
                        ],
                    ],
                    [
                        'title' => 'Send Invitations',
                        'url' => baseUrl('/') . '/reviews/send-invitations',
                        'menu-name' => 'send-invitations',
                        'route' => 'panel.send-invitations.list',
                        'privileges' => [
                            'route_prefix' => 'panel.send-invitations',
                            'module' => 'professional-send-invitations',
                            'action' => 'list'
                        ],
                    ],
                    // [
                    //     'title' => 'Review Invitations',
                    //     'url' => baseUrl('/') . '/reviews/review-invitations',
                    //     'menu-name' => 'review-invitations',
                    //     'route' => 'panel.review-invitations.list',
                    //     'privileges' => [
                    //         'route_prefix' => 'panel.review-invitations',
                    //         'module' => 'professional-review-invitations',
                    //         'action' => 'list'
                    //     ],
                    // ],
                    [
                        'title' => 'Reviews',
                        'menu-name' => 'review-received',
                        'url' => baseUrl('/') . '/reviews/review-received',
                        'route' => 'panel.review-received.list',
                        'privileges' => [
                            'route_prefix' => 'panel.review-received',
                            'module' => 'professional-review-received',
                            'action' => 'list'
                        ],
                    ],
                        [
                        'title' => 'Spam Reviews',
                        'menu-name' => 'spam-reviews',
                        'url' => baseUrl('/') . '/reviews/spam-reviews',
                        'route' => 'panel.spam-reviews.list',
                        'privileges' => [
                            'route_prefix' => 'panel.spam-reviews',
                            'module' => 'professional-spam-reviews',
                            'action' => 'list'
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Support Ticket',
                'icon' => 'fa-regular fa-credit-card',
                'route' => 'panel.tickets.list',
                'menu-name' => 'support-tickets',
                'url' => baseUrl('/') . '/tickets',
            ],
            [
                'title' => 'User Plan Feature',
                'icon' => 'fa-regular fa-credit-card',
                'route' => 'panel.user-plan-feature.list',
                'menu-name' => 'user-plan-feature',
                'url' => baseUrl('/') . '/user-plan-feature',
            ],
            [
                'title' => 'Transactions',
                'icon' => 'fa-regular fa-dollar',
                'menu-name' => 'transactions',
                'submenu' => [
                     [
                        'title' => 'Overview',
                        'icon' => 'list.png',
                        'menu-name' => 'transaction-overview',
                        'route' => 'panel.transaction-overview',
                        'privileges' => [
                            'route_prefix' => 'panel.transaction-overview',
                            'module' => 'professional-transaction-overview',
                            'action' => 'overview'
                        ],
                        'url' => baseUrl('/') . '?tab=transactions',
                    ],
                    [
                        'title' => 'Transaction History',
                        'icon' => 'list.png',
                        'menu-name' => 'transaction-history',
                        'route' => 'panel.transactions.history.list',
                        'privileges' => [
                            'route_prefix' => 'panel.transactions-history',
                            'module' => 'professional-transactions-history',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '/transactions/history',
                    ],
                    [
                        'title' => 'Receipts',
                        'icon' => 'list.png',
                        'menu-name' => 'transaction-receipts',
                        'route' => 'panel.transactions.receipts.list',
                        'privileges' => [
                            'route_prefix' => 'panel.transactions-receipts',
                            'module' => 'professional-transactions-receipts',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '/transactions/receipts',
                    ]
                ],
            ],
            [
                'title' => 'Earnings',
                'icon' => 'fa-regular fa-chart-column',
                'menu-name' => 'earnings',
                'submenu' => [
                     [
                        'title' => 'Overview',
                        'icon' => 'list.png',
                        'menu-name' => 'earning-overview',
                        'route'=>'panel.earning-overview',
                        'privileges' => [
                            'route_prefix' => 'panel.earning-overview',
                            'module' => 'professional-earning-overview',
                            'action' => 'overview'
                        ],
                        'url' => baseUrl('/') . '?tab=reports',
                    ],
                    [
                        'title' => 'Points Earn History',
                        'icon' => 'list.png',
                        'menu-name' => 'points-earn-history',
                        'route' => 'panel.points-earn-history.list',
                        'privileges' => [
                            'route_prefix' => 'panel.points-earn-history',
                            'module' => 'professional-points-earn-history',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '/earnings/points-earn-history',
                    ],
                    [
                        'title' => 'Global Invoices',
                        'menu-name' => 'global-invoices',
                        'route' => 'panel.invoices.list',
                        'privileges' => [
                            'route_prefix' => 'panel.invoices',
                            'module' => 'professional-invoices',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '/invoices',
                    ],
                    [
                        'title' => 'My Earning Reports',
                        'menu-name' => 'my-earning-reports',
                        'route' => 'panel.earning-report.list',
                        'privileges' => [
                            'route_prefix' => 'panel.earning-report',
                            'module' => 'professional-earning-report',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '/earning-report',
                    ],
                   
                ],
            ],
            [
                'title' => 'Plans',
                'icon' => 'fa-regular fa-chart-line-up',
                'menu-name' => 'plans',
                'submenu' => [
                    [
                        'title' => 'My Membership Plans',
                        'icon' => 'list.png',
                        'menu-name' => 'my-membership-plans',
                        'privileges' => [
                            'route_prefix' => 'panel.my-membership-plans',
                            'module' => 'professional-my-membership-plans',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '/my-membership-plans',
                    ],
                    [
                        
                        'title' => 'Membership Plans',
                        'icon' => 'fa-regular fa-address-card',
                        'menu-name' => 'membership-plans',
                        'route' => 'panel.membership-plans.list',
                        'privileges' => [
                            'route_prefix' => 'panel.membership-plans',
                            'module' => 'professional-membership-plans',
                            'action' => 'list'
                        ],
                        'url' => baseUrl('/') . '/membership-plans',
                    ],
                ]
            ],
           
            // [
            //     'title' => 'Earning Reports',
            //     'icon' => 'fa-regular fa-file-vector',
            //     'menu-name' => 'earning-reports',
            //     'route' => 'panel.earning-report',
            //     'url' => baseUrl('/') . '/earning-report',
            // ],
            // [
            //     'title' => 'Global Invoices',
            //     'icon' => 'fa-regular fa fa-file',
            //     'menu-name' => 'global-invoices',
            //     'privileges' => [
            //         'route_prefix' => 'panel.invoices',
            //         'module' => 'professional-invoices',
            //         'action' => 'list'
            //     ],
            //     'url' => baseUrl('/') . '/invoices',
            // ],
            
            
            // [
            //     'title' => 'Appointment Setting',
            //     'icon' => 'fa-regular fa-calendar',
            //     'route' => 'panel',
            //     'menu-name' => 'appointment-settting',
            //     'url' => baseUrl('/') . '/appointments/settings',
            // ],
            [
                'title' => 'Appointment System',
                'icon' => 'fa-regular fa-calendar',
                'menu-name' => 'appointment-system',
                'submenu' => [
                    [
                        'title' => 'Overview <span class="counter-badge appointment-overview">('.appointmentCounts(auth()->user()->getRelatedProfessionalId()).')</span>',
                         'url' => baseUrl('/') . '?tab=appointments',
                        'menu-name' => 'appointments-overview',
                        'route' => 'panel.appointments-overview',
                        'icon' => 'list.png',
                        'privileges' => [
                            'route_prefix' => 'panel.appointment-booking',
                            'module' => 'professional-appointment-booking',
                            'action' => 'overview'
                        ],
                    ],
                    [
                        'title' => 'Settings',
                        'url' => baseUrl('/') . '/appointments/settings',
                        'menu-name' => 'appointment-settings',
                        'route' => 'panel.appointment-settings',
                        'icon' => 'list.png',
                        'privileges' => [
                            'route_prefix' => 'panel.appointment-settings',
                            'module' => 'professional-appointment-settings',
                            'action' => 'list'
                        ],
                    ],
                    // [
                    //     'title' => 'Appointment Types',
                    //     'url' => baseUrl('/') . '/appointments/appointment-types',
                    //     'menu-name' => 'appointment-types',
                    //     'route' => 'panel.appointment-settings',
                    //     'privileges' => [
                    //         'route_prefix' => 'panel.appointment-types',
                    //         'module' => 'professional-appointment-types',
                    //         'action' => 'list'
                    //     ],
                    // ],
                    [
                        'title' => 'Booking Flow  <span class="counter-badge appointment-overview">('.appointmentBookingFlowCounts(auth()->user()->getRelatedProfessionalId()).')</span>',
                        'url' => baseUrl('/') . '/appointments/appointment-booking-flow',
                        'menu-name' => 'appointment-booking-flow',
                        'route'=>'panel.appointment-booking-flow.list',
                        'icon' => 'list.png',
                        'privileges' => [
                            'route_prefix' => 'panel.appointment-booking-flow',
                            'module' => 'professional-appointment-booking-flow',
                            'action' => 'list'
                        ],
                    ],
                    [
                        'title' => 'Bookings <span class="counter-badge appointment-count">('.appointmentCounts(auth()->user()->getRelatedProfessionalId()).')</span>',
                        'url' => baseUrl('/') . '/appointments/appointment-booking',
                        'menu-name' => 'appointment-bookings',
                        'route'=>'panel.appointment-booking.list',
                        'icon' => 'list.png',
                        'privileges' => [
                            'route_prefix' => 'panel.appointment-booking',
                            'module' => 'professional-appointment-booking',
                            'action' => 'list'
                        ],
                    ],
                    [
                        'title' => 'Create Appointment',
                        'url' => baseUrl('/') . '/appointments/appointment-booking/save-booking',
                        'menu-name' => 'book-an-appointment',
                        'route'=>'panel.appointment-booking.add',
                        'icon' => 'list.png',
                        'privileges' => [
                            'route_prefix' => 'panel.appointment-booking',
                            'module' => 'professional-appointment-booking',
                            'action' => 'add'
                        ],
                    ],
                    [
                        'title' => 'Calendar',
                        'url' => baseUrl('/') . '/appointments/appointment-booking/calendar',
                        'menu-name' => 'appointment-calendar',
                        'route'=>'panel.appointment-booking.view-calender',
                        'icon' => 'list.png',
                        'privileges' => [
                            'route_prefix' => 'panel.appointment-booking',
                            'module' => 'professional-appointment-booking',
                            'action' => 'view-calender'
                        ],
                    ],
                    // [
                    //     'title' => 'Block Dates',
                    //     'url' => baseUrl('/') . '/appointments/block-dates',
                    //     'menu-name' => 'block-dates',
                    //     'route' => 'panel.block-dates.list',
                    //     'privileges' => [
                    //         'route_prefix' => 'panel.block-dates',
                    //         'module' => 'professional-block-dates',
                    //         'action' => 'list'
                    //     ],
                    // ],
                ],
            ],
            [
                'title' => 'Connection',
                'icon' => 'fa-regular fa-messages',
                'menu-name' => 'connections',
                'submenu' => [
                    [
                        'title' => 'Connections',
                        'url' => baseUrl('/') . '/connections/connect',
                        'menu-name' => 'connect',
                        'route' => 'panel.connections.connect.list',
                        'privileges' => [
                            'route_prefix' => 'panel.connections.connect',
                            'module' => 'professional-connections-connect',
                            'action' => 'list'
                        ],
                    ],
                    [
                        'title' => 'Chat Invitations',
                        'url' => baseUrl('/') . '/connections/invitations',
                        'menu-name' => 'chat-invitations',
                        'route' => 'panel.chat-invitations.list',
                        'privileges' => [
                            'route_prefix' => 'panel.chat-invitations',
                            'module' => 'professional-chat-invitations',
                            'action' => 'list'
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Message',
                'icon' => 'fa-regular fa-messages',
                'menu-name' => 'message',
                'submenu' => [
                    [
                        'title' => 'Overview',
                        'icon' => 'list.png',
                        'menu-name' => 'message-overview',
                        'route' => 'panel.message-overview',
                        'privileges' => [
                              'route_prefix' => 'panel.message-centre',
                            'module' => 'professional-message-centre',
                            'action' => 'list'
                        ],
                         'url' => baseUrl('/') . '?tab=messages',
                    ],
                    [
                        'title' => 'Message Centre',
                        'icon' => 'list.png',
                        'url' => baseUrl('/') . '/individual-chats',
                        'route' => 'panel.individual-chats.list',
                        'menu-name' => 'message-centre',
                        'privileges' => [
                            'route_prefix' => 'panel.message-centre',
                            'module' => 'professional-message-centre',
                            'action' => 'list'
                        ],
                    ],
                    [
                        'title' => 'Group Messages',
                        'icon' => 'list.png',
                        'url' => baseUrl('/') . '/group-message',
                        'route' => 'panel.group.conversation',
                        'menu-name' => 'group-messages',
                        'privileges' => [
                            'route_prefix' => 'panel.group',
                            'module' => 'professionalgroup',
                            'action' => 'list'
                        ],
                    ],
                    [
                        'title' => 'All Groups ',
                        'menu-type' => '',
                        'url' => baseUrl('/') . '/group/groups-list',
                        'route' => 'panel.group.get-group-list',
                        'menu-name' => 'group-messages',
                        'submenu-name' => 'group-list',
                        'sub_submenu' => [
                            [
                                'title' => 'My Joined Groups',
                                'menu-type' => '',
                                'url' => baseUrl('/') . '/group/my-joined-group-list',
                                'menu-name' => 'group-messages',
                            ],
                            [
                                'title' => 'My Created Groups',
                                'menu-type' => '',
                                'url' => baseUrl('/') . '/group/my-created-group-list',
                                'menu-name' => 'group-messages',
                            ],[
                                'title' => 'Sent Requests',
                                'menu-type' => '',
                                'url' => baseUrl('/') . '/group/sent-request',
                                'menu-name' => 'group-messages',
                            ],[
                                'title' => 'Received Requests',
                                'menu-type' => '',
                                'url' => baseUrl('/') . '/group/received-request',
                                'menu-name' => 'group-messages',
                            ],[
                                'title' => 'Archived',
                                'menu-type' => '',
                                'url' => baseUrl('/') . '/group/received-request',
                                'menu-name' => 'group-messages',
                            ]
                        ]
                      
                    ],
                ],
            ],
            [
                'title' => 'Articles',
                'icon' => 'fa-regular fa-memo-pad',
                'route' => 'panel.articles.list',
                'menu-name' => 'articles',
                'privileges' => [
                    'route_prefix' => 'panel.articles',
                    'module' => 'professional-articles',
                    'action' => 'list'
                ],
                'url' => baseUrl('/') . '/articles',
            ],
            [
                'title' => 'All Threads',
                'icon' => 'fa-regular fa-rss',
                'menu-name' => 'all-threads',
                'route' => 'panel.manage-discussion-threads.index',
                'privileges' => [
                    'route_prefix' => 'panel.manage-discussion-threads',
                    'module' => 'professional-manage-discussion-threads',
                    'action' => 'list'
                ],
                'url' => baseUrl('/') . '/manage-discussion-threads',
                'submenu' => [
                    [
                        'title' => 'All Discussions <span class="counter-badge appointment-overview">('.totalAllDiscussion().')</span>',
                        'url' => baseUrl('/') . '/manage-discussion-threads',
                        'menu-name' => 'all-threads',
                        'icon' => '📑',
                        'route' => 'panel.manage-discussion-threads.index',
                        'privileges' => [
                            'route_prefix' => 'panel.manage-discussion-threads',
                            'module' => 'professional-manage-discussion-threads',
                            'action' => 'list'
                        ],
                    ],
                    [
                        'title' => 'My Discussions <span class="counter-badge appointment-overview">('.totalAllDiscussion(auth()->user()->id ?? 0).')</span>',
                        'url' => baseUrl('/') . '/manage-discussion-threads/my-threads',
                        'menu-name' => 'my-threads',
                        'icon' => '📊',
                        'route' => 'panel.manage-discussion-threads.my-threads',
                        'privileges' => [
                            'route_prefix' => 'panel.manage-discussion-threads',        
                            'module' => 'professional-manage-discussion-threads',
                            'action' => 'my-threads'
                        ],
                    ],
                    [   
                        'title' => 'Discussion Connections <span class="counter-badge appointment-overview">('.totalDiscussionConnected(auth()->user()->id ?? 0).')</span>',
                        'url' => baseUrl('/') . '/manage-discussion-threads/discussion-connections',
                        'menu-name' => 'discussion-connections',
                        'icon' => '💬',
                        'route' => 'panel.manage-discussion-threads.categories',
                        'privileges' => [
                            'route_pre  fix' => 'panel.manage-discussion-threads',
                            'module' => 'professional-manage-discussion-threads',
                            'action' => 'discussion-connections'
                        ],
                    ],
                    [
                        'title' => 'Saved Discussions <span class="counter-badge appointment-overview">('.totalSavedDiscussion(auth()->user()->id ?? 0).')</span>',
                        'url' => baseUrl('/') . '/manage-discussion-threads/saved-threads',
                        'menu-name' => 'saved-discussions',
                        'icon' => '📑',
                        'route' => 'panel.manage-discussion-threads.categories',
                        'privileges' => [   
                            'route_prefix' => 'panel.manage-discussion-threads',
                            'module' => 'professional-manage-discussion-threads',
                            'action' => 'saved-threads'
                        ],
                    ],
                    [
                        'title' => 'Pending Requests <span class="counter-badge appointment-overview">('.totalPendingRequest(auth()->user()->id ?? 0).')</span>',
                        'url' => baseUrl('/') . '/manage-discussion-threads/pending-threads',
                        'menu-name' => 'pending-threads',
                        'icon' => '',
                        'route' => 'panel.manage-discussion-threads.categories',
                        'privileges' => [   
                            'route_prefix' => 'panel.manage-discussion-threads',
                            'module' => 'professional-manage-discussion-threads',
                            'action' => 'pending-threads'
                        ],
                    ]
                ]
            ],
          
            [
                'title' => 'Settings',
                'icon' => 'fa-regular fa-gear',
                'route' => 'panel.settings',
                'menu-name' => 'settings',
                'submenu' => [
                    [
                        'title' => 'Security',
                        'menu-name' => 'security-settings',
                        'url' => baseUrl('/') . '/settings/security',
                            'route' => 'panel.settings.security.list',
                             'privileges' => [
                            'route_prefix' => 'panel.settings.security',
                            'module' => 'professional-settings-security',
                            'action' => 'list'
                        ],
                    ],
                    [
                        'title' => 'Messages',
                        'menu-name' => 'message-settings',
                        'route' => 'panel.message-settings.list',
                        'url' => baseUrl('/') . '/message-settings',
                           'privileges' => [
                            'route_prefix' => 'panel.message-settings',
                            'module' => 'professional-message-settings',
                            'action' => 'list'
                        ],
                    ],
                    // [
                    //     'title' => 'Discussions',
                    //     'menu-name' => 'discussion-settings',
                    //     'route' => 'panel.settings.discussion',
                    //     'url' => baseUrl('/') . '/settings/discussion',
                    //         'privileges' => [
                    //         'route_prefix' => 'panel.settings.discussion',
                    //         'module' => 'professional-settings-discussion',
                    //         'action' => 'list'
                    //     ],
                    // ],
                    [
                        'title' => 'Feeds',
                        'menu-name' => 'feeds-settings',
                        'route' => 'panel.settings.feeds.list',
                        'url' => baseUrl('/') . '/settings/feeds',
                            'privileges' => [
                            'route_prefix' => 'panel.settings.feeds',
                            'module' => 'professional-settings-feeds',
                            'action' => 'list'
                        ],
                    ],
                    // [
                    //     'title' => 'Privacy',
                    //     'menu-name' => 'privacy-settings',
                    //     'route' => 'panel.settings.privacy',
                    //     'url' => baseUrl('/') . '/settings/privacy',
                    //         'privileges' => [
                    //         'route_prefix' => 'panel.settings.privacy',
                    //         'module' => 'professional-settings-privacy',
                    //         'action' => 'list'
                    //     ],
                    // ],
                    [
                        'title' => 'Account',
                        'menu-name' => 'account-settings',
                        'route' => 'panel.settings.account.list',
                        'url' => baseUrl('/') . '/settings/account',
                            'privileges' => [
                            'route_prefix' => 'panel.settings.account',
                            'module' => 'professional-settings-account',
                            'action' => 'list'
                        ],
                    ],
                    // [
                    //     'title' => 'Payments',
                    //     'menu-name' => 'payment-settings',
                    //     'route' => 'panel.settings.payment.list',
                    //     'url' => baseUrl('/') . '/settings/payment',
                    //         'privileges' => [
                    //         'route_prefix' => 'panel.settings.payment',
                    //         'module' => 'professional-settings-payment',
                    //         'action' => 'list'
                    //     ],
                    // ],
                    // [
                    //     'title' => 'Reviews',
                    //     'menu-name' => 'reviews-settings',
                    //     'route' => 'panel.settings.reviews',
                    //     'url' => baseUrl('/') . '/settings/reviews',
                    //         'privileges' => [
                    //           'route_prefix' => 'panel.settings.reviews',
                    //         'module' => 'professional-settings-reviews',
                    //         'action' => 'list'
                    //     ],
                    // ],
                    // [
                    //     'title' => 'Trust Score',
                    //     'menu-name' => 'trust-score-settings',
                    //     'route' => 'panel.settings.trust-score',
                    //     'url' => baseUrl('/') . '/settings/trust-score',
                    //         'privileges' => [
                    //          'route_prefix' => 'panel.settings.trust-score',
                    //         'module' => 'professional-settings-trust-score',
                    //         'action' => 'list'
                    //     ],
                    // ],
                    // [
                    //     'title' => 'Teams',
                    //     'menu-name' => 'teams-settings',
                    //     'route' => 'panel.settings.teams',
                    //     'url' => baseUrl('/') . '/settings/teams',
                    //         'privileges' => [
                    //        'route_prefix' => 'panel.settings.teams',
                    //         'module' => 'professional-settings-teams',
                    //         'action' => 'list'
                    //     ],
                    // ],
                    // [
                    //     'title' => 'Appointments',
                    //     'menu-name' => 'appointment-settings',
                    //     'route' => 'panel.settings.appointment',
                    //     'url' => baseUrl('/') . '/settings/appointments',
                    //         'privileges' => [
                    //        'route_prefix' => 'panel.settings.appointment',
                    //         'module' => 'professional-settings-appointment',
                    //         'action' => 'list'
                    //     ],
                    // ],
                    // [
                    //     'title' => 'Cases',
                    //     'menu-name' => 'cases-settings',
                    //     'route' => 'panel.settings.cases',
                    //     'url' => baseUrl('/') . '/settings/cases',
                    //         'privileges' => [
                    //           'route_prefix' => 'panel.settings.cases',
                    //         'module' => 'professional-settings-cases',
                    //         'action' => 'list'
                    //     ],
                    // ],
                    // [
                    //     'title' => 'Notifications',
                    //     'menu-name' => 'notifications-settings',
                    //     'route' => 'panel.settings.notifications',
                    //     'url' => baseUrl('/') . '/settings/notifications',
                    //         'privileges' => [
                    //         'route_prefix' => 'panel.settings.notifications',
                    //         'module' => 'professional-settings-notifications',
                    //         'action' => 'list'
                    //     ],
                    // ],
                    // [
                    //     'title' => 'Connections',
                    //     'menu-name' => 'connections-settings',
                    //     'route' => 'panel.settings.connections.list',
                    //     'url' => baseUrl('/') . '/settings/connections',
                    //         'privileges' => [
                    //         'route_prefix' => 'panel.settings.connections',
                    //         'module' => 'professional-settings-connections',
                    //         'action' => 'list'
                    //     ],
                    // ]
                ]
            ]
            //  [
            //     'title' => 'Login Devices',
            //     'icon' => 'fa-solid fa-laptop-mobile',
            //     'menu-name' => 'login-devices',
            //     'route' => 'panel.deviceList',
            //     'url' => baseUrl('/') . '/profile/confirm-login/'.auth()->user()->unique_id,
            // ],
        ];

        return $menuItems;
    }
}
if(!function_exists("configData")){
    function configData(){
       $data = file_get_contents(storage_path('app/public/st.config'));
       return json_decode($data,true);
    }
}
if(!function_exists("dc")){
    function dc($key){
       $data = decrypt($key);
       return $data;
    }
}
if (! function_exists('randomNumber')) {
    function randomNumber($n=10) {
        $characters = '1123456789';
        $randomString = '';
        $randomString = substr(str_shuffle($characters), 0, $n);
        return $randomString;
    }
}

if (! function_exists('randomString')) {
    function randomString($n=20) {
        $randomString = Str::random($n);
        return $randomString;
    }
}

if(!function_exists("breadcrumbs")){
    function breadcrumbs() { 
        $segments = request()->segments();
        $role = array("user","professional","manager","investigator","data-analyst");
        
        if(in_array($segments[0],$role)){
            if(count($segments) > 2){
                $segment = $segments[1]."-".$segments[2];
            }else{
                $admin_arr = array("articles");
                if($segments[0] == 'admin' && in_array($segments[1],$admin_arr)){
                    $segment = $segments[0]."-".$segments[1];
                }else{
                    $segment = $segments[1];
                }
            }
            
        }else{
            $segment = $segments[0];
        }
        // dd($segment);
        if ($segment=="sub-service") {
            $segment="service";
        }elseif($segment=="free-assessment") {
            $segment="free_assessment";
        }
        $breadcrumb = Breadcrumbs::render($segment,$segments);
        
        return $breadcrumb;
    }
}



if (! function_exists('groupLastMessageId')) {
    function groupLastMessageId() {
        $lastMessage = GroupMessages::latest('id')->first(); 
        if($lastMessage){
            return $lastMessage->id;
        }else{
            return 0;
        }
    }
}
if (! function_exists('LastMessageId')) {
    function LastMessageId() {
        $lastMessage = ChatMessage::latest('id')->first(); 
        if($lastMessage){
            return $lastMessage->id;
        }else{
            return 0;
        }
    }
}
if (! function_exists('baseUrl')) {
    function baseUrl($url) {
        if(Auth::check()){
            $role = 'panel';
            if (strpos($url, '/') === 0) {
                $base_url = url($role.$url);
            }else{
                $base_url = url($role.'/'.$url);
            }
        }else{
            $base_url = url($url);
        }
        return $base_url;
    }
}

if (! function_exists('roleFolder')) {
    function roleFolder() {
        if(Auth::check()){
            $role = Auth::user()->role;
            if($role == 'staff'){
                $role = 'admin';
            }else{
                $role = str_replace("_","-",$role);
            }
        }else{
            $role = '';
        }
        return $role;
    }
}

if (! function_exists('cleanPhoneNumber')) {
    function cleanPhoneNumber($phone_number = '') {
        return str_replace([' ', '(', ')','-','{', '}'], '', $phone_number);
    }
}
if(!function_exists("makeBoldBetweenAsterisks")){

    function makeBoldBetweenAsterisks($message)
    {
        // Check if the message contains text between **
        $formattedMessage = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $message);

        return $formattedMessage;
    }

}
if(!function_exists("userInitial")){
    function userInitial($user) {
       $first_name = isset($user->first_name) ? substr($user->first_name, 0, 1) : '';
       $last_name = isset($user->last_name) ? substr($user->last_name, 0, 1) : '';
       $init = $first_name.$last_name;
       $init = strtoupper($init);

       return $init ?: 'NA'; 
    }
}

if(!function_exists("getUserRole")){
    function getUserRole($user_id) {
    $fetchRole=User::where('id',$user_id)->pluck('role')->first();
       return $fetchRole;
    }
}

if (! function_exists('dateFormat')) {
    function dateFormat($date,$format = "M d, Y") {
        $date = date($format,strtotime($date));
        return $date;
    }
}


if(!function_exists("fetchProfileImage")){
    function fetchProfileImage($unique_id = '', $size = 'r', $role = 'user'){

        // Fetch the user from the database using the unique ID
        $user = DB::table('users')->where("unique_id", $unique_id)->first();

        // Get the profile image and define the directory path based on the role
        $profile_image = $user->profile_image ?? '';
        $profile_dir = public_path("uploads/{$role}/{$unique_id}/profile/") . $profile_image;

        // Check if the profile image exists, else return default image URL
        if($profile_image == '' || !file_exists($profile_dir)){
            return url("assets/svg/browse.svg");
        }

        // Base URL for the original image
        $original = url("public/uploads/{$role}/{$unique_id}/profile/" . $profile_image);

        // Initialize the URL variable
        $url = '';

        // Determine which size of the image to return
        if($size == 'r'){
            $url = $original;
        } elseif($size == 'm') {
            if(file_exists(public_path("uploads/{$role}/{$unique_id}/profile/medium/") . $profile_image)){
                $url = url("public/uploads/{$role}/{$unique_id}/profile/medium/" . $profile_image);
            } else {
                $url = $original;
            }
        } elseif($size == 't') {
            if(file_exists(public_path("uploads/{$role}/{$unique_id}/profile/thumb/") . $profile_image)){
                $url = url("public/uploads/{$role}/{$unique_id}/profile/thumb/" . $profile_image);
            } else {
                $url = $original;
            }
        }

        // If no size is matched, return the original size
        if($url == ''){
            $url = $original;
        }

        return $url;
    }
}

if(!function_exists("fetchCompanyLogo")){
    function fetchCompanyLogo($company_logo){

        $role="professional";
        $size = 'r';
        // Get the profile image and define the directory path based on the role
        $profile_image = $company_logo ?? '';
        $profile_dir = public_path("uploads/{$role}/company_logo/") . $profile_image;

        // Check if the profile image exists, else return default image URL
        if($profile_image == '' || !file_exists($profile_dir)){
            return url("assets/svg/browse.svg");
        }

        // Base URL for the original image
        $original = url("public/uploads/{$role}/company_logo/" . $profile_image);

        // Initialize the URL variable
        $url = '';

        // Determine which size of the image to return
        if($size == 'r'){
            $url = $original;
        } elseif($size == 'm') {
            if(file_exists(public_path("uploads/{$role}/company_logo/medium/") . $profile_image)){
                $url = url("public/uploads/{$role}/company_logo/medium/" . $profile_image);
            } else {
                $url = $original;
            }
        } elseif($size == 't') {
            if(file_exists(public_path("uploads/{$role}/company_logo/thumb/") . $profile_image)){
                $url = url("public/uploads/{$role}/company_logo/thumb/" . $profile_image);
            } else {
                $url = $original;
            }
        }

        // If no size is matched, return the original size
        if($url == ''){
            $url = $original;
        }

        return $url;
    }
}

if (!function_exists('deleteRecordAndFolder')) {
    function deleteRecordAndFolder($unique_id = 0, $role = 'user')
    {
        // Validate input
        if (empty($unique_id) || empty($role)) {
            return false; // or throw an exception, or handle error appropriately
        }

        // Step 2: Remove the associated folder and its contents
        $fullPath = public_path('uploads/' . $role . '/' . $unique_id);

        if (File::exists($fullPath)) {
            File::deleteDirectory($fullPath);
        }

        return true;
    }
}




if (! function_exists('resizeImage')) {
    function resizeImage($source_url, $destination_url, $maxWidth, $maxHeight, $quality=80) {
        $imageDimensions = getimagesize($source_url);
        $imageWidth = $imageDimensions[0];
        $imageHeight = $imageDimensions[1];
        $imageSize['width'] = $imageWidth;
        $imageSize['height'] = $imageHeight;
        if($imageWidth > $maxWidth || $imageHeight > $maxHeight)
        {
            if ( $imageWidth > $imageHeight ) {
                $imageSize['height'] = floor(num: ($imageHeight/$imageWidth)*$maxWidth);
                $imageSize['width']  = $maxWidth;
            } else {
                $imageSize['width']  = floor(($imageWidth/$imageHeight)*$maxHeight);
                $imageSize['height'] = $maxHeight;
            }
        }

        $width = $imageSize['width'];
        $height = $imageSize['height'];

        $info = getimagesize($source_url);
        if ($info['mime'] == 'image/jpeg')
        $source = imagecreatefromjpeg($source_url);

        elseif ($info['mime'] == 'image/gif')
        $source = imagecreatefromgif($source_url);

        elseif ($info['mime'] == 'image/png')
        $source = imagecreatefrompng($source_url);

        $thumb = imagecreatetruecolor($width, $height);
        //$source = imagecreatefromjpeg($source_url);

        list($org_width, $org_height) = getimagesize($source_url);

        imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $org_width, $org_height);
        $filename = mt_rand(0,9999);

        imagejpeg($thumb, $destination_url);
        return $destination_url;
    }
}

if (!function_exists("str_slug")) {
    /**
     * Converts the given string into a slug format.
     * A slug is a simplified version of a string, typically used in URLs,
     * where spaces are replaced by dashes and the string is converted to lowercase.
     *
     * This function first checks if the `str_slug` function does not already exist,
     * to avoid redeclaration.
     *
     * @param string $string The input string to be converted into a slug.
     * @return string The slugified version of the input string.
     */
    function str_slug($string,$separator='-')
    {
        // Converts the string to a slug using Laravel's Str::slug helper
        $slug = \Illuminate\Support\Str::slug($string, $separator);

        return $slug;
    }
}

if (!function_exists("get_roles")) {
    function get_roles()
    {
        return $staff_roles = ["client","professional","manager","investigator","data-analyst" ];
    }
}

if (!function_exists("staff_roles")) {
    function staff_roles()
    {
        return $staff_roles = ["manager","investigator","data-analyst"];
    }
}

if(!function_exists("extraDetails")){
    function extraDetails(){
       $data = ['About','Ratings','Website Link' ,'Source Urls' ,'Address'];
       return $data;
    }
}
// if(!function_exists("apiKeys")){
//     function apiKeys($key){
//         $api_key = ApiKey::where("api_key",$key)->first();
//         $api_value = '';
//         if(!empty($api_key)){
//             $api_value = decryptVal($api_key->api_value);
//             // $api_value = $api_key->api_value;
//         }
//         return $api_value;
//     }
// }
function apiKeys($key){
   
    $api_key = ApiKey::where("api_key",$key)->first();
    $api_value = '';
    $stripe_keys = array("STRIPE_KEY","STRIPE_SECRET","STRIPE_WEBHOOK");
    $razorpay_keys = array("RAZORPAY_KEY_ID","RAZORPAY_KEY_SECRET","RAZOR_WEBHOOK_KEY");
        if(in_array($key,$stripe_keys)){
            $mode = getSetting("STRIPE_MODE");
            // $mode = Settings::where("meta_key","STRIPE_MODE")->first();
            if(($mode??'') != ''){
                $stripe_mode = $mode;
                if($stripe_mode == 'LIVE'){
                    $stripe_mode = decryptVal("LIVE_".$key);
                }elseif($stripe_mode == 'TEST'){
                    $stripe_mode = decryptVal("TEST_".$key);
                }else{
                    $stripe_mode = decryptVal($key);
                }
              
                $key = ApiKey::where("api_key",$stripe_mode)->first();
                $api_value =  decryptVal($key->api_value);
            }else{
                $api_value = decryptVal($api_key->api_value);
            }
        }else if(in_array($key,$razorpay_keys)){
            $mode = getSetting("RAZORPAY_MODE");
         
            // $mode = Settings::where("meta_key","STRIPE_MODE")->first();
            if(($mode??'') != ''){
                $razorpay_mode = $mode;
                if($razorpay_mode == 'LIVE'){
                    $razorpay_mode = decryptVal("LIVE_".$key);
                }elseif($razorpay_mode == 'TEST'){
                    $razorpay_mode = decryptVal("TEST_".$key);
                }else{
                    $razorpay_mode = decryptVal($key);
                }
               
                $key = ApiKey::where("api_key",$razorpay_mode)->first();
                $api_value =  decryptVal($key->api_value);
                
            }else{
                $api_value = decryptVal($api_key->api_value);
            }
        }else{
            $api_value = decryptVal($api_key->api_value);
        }
        
        // $api_value = $api_key->api_value;

    return $api_value;
}
if(!function_exists("sendEmailTo")){

function sendEmailTo(){
    return array("deepvirk27@gmail.com","uap@trustvisory.com");
}
}
if(!function_exists("adminNotification")){
    function adminNotification($email_for="",$unique_id="",$posted_by=""){
        $host = request()->getHost(); // Get the current host
        $isLocal = in_array($host, ['localhost', '127.0.0.1']);
        if ($isLocal) {
            \Log::info('admin email not send');
            return;
        }
        $get_emails=User::whereIn("role",['admin'])->get()->pluck('email');
        $mailData = ['email_for' => $email_for,'unique_id'=>$unique_id,'posted_by'=>$posted_by];
        $view = \View::make('emails.report_uap', $mailData);
        $message = $view->render();
        foreach(sendEmailTo() as $mail){
            $get_emails->push($mail);
        }
        foreach($get_emails as $mail){
               
                $parameter = [
                    'to' => $mail,
                    'to_name' => 'Trustvisory',
                    'message' => $message,
                    'subject' => $email_for,
                    'view' => 'emails.report_uap',
                    'data' => $mailData,
                ];
                sendMail($parameter);
        }
    }
}
if(!function_exists("companyName")){

function companyName(){
    return "TrustVisory";
}
}
if(!function_exists("sendMail")){

function sendMail($parameter){
       
    $object = new EmailLog();
    if(auth()->check()){
        $object->user_id = auth()->user()->id;
    }
    $object->mail_data = json_encode($parameter);
    $object->email = $parameter['to'];
    $object->save();
    // if(env('APP_ENV') == 'production'){
    //     $unique_id = $object->unique_id;
    //     $url = url('trigger/email/'.$unique_id);
    //     $cmd  = "curl --max-time 60 ";
    //     $cmd .= "'" . $url . "'";
    //     $cmd .= " > /dev/null 2>&1 &";
    //     exec($cmd, $output, $exit);
    //     return $exit == 0;
    // }else{
        triggerMail($parameter);
    // }
}
}



if(!function_exists("triggerMail")) {
    /**
     * Sends an email using the SendGrid API.
     *
     * @param array $parameter
     *  - 'from' (optional): The sender's email address.
     *  - 'from_name' (optional): The sender's name.
     *  - 'to': The recipient's email address.
     *  - 'to_name' (optional): The recipient's name.
     *  - 'subject': The subject of the email.
     *  - 'view': The Blade view to render the email content.
     *  - 'data' (optional): Data to pass to the Blade view for rendering.
     *
     * @return array
     *  - 'status': Boolean indicating success or failure.
     *  - 'message': Response message or error message.
     */
    function triggerMail($parameter) {

        // Set 'from' email if not provided
        if (!isset($parameter['from'])) {
            $parameter['from'] = apiKeys("sendgrid_from_email");
        }

        // Set 'from_name' if not provided
        if (!isset($parameter['from_name'])) {
            $parameter['from_name'] = companyName();
        }

        // Set 'to_name' to an empty string if not provided
        if (!isset($parameter['to_name'])) {
            $parameter['to_name'] = '';
        }

        try {
            // Prepare the data array for the view, if provided
            $data = array();
            if (isset($parameter['data'])) {
                $data = $parameter['data'];
            }
         //   dd($parameter['reply_to_email']);
            // Create a new instance of SendGridApi with the API key, from email, and sender name
            $replyTo = '';
            if(isset($parameter['reply_to_email'])){
                $replyTo = $parameter['reply_to_email'];
            }
            $domain = request()->getHost();
            $mailObj = new SendGridApi(apiKeys("sendgrid_key"),apiKeys("sendgrid_from_email"), $parameter['from_name'],$domain, $replyTo);


            // Render the email content using the specified view and data
            $content = View::make($parameter['view'], $data);
            $message = $content->render();

            // Extract necessary parameters for sending the email
            $to = $parameter['to'];
            $to_name = $parameter['to_name'];
            $subject = $parameter['subject'];
            $from = $parameter['from'];
            $from_name = $parameter['from_name'];
            
            $attachment = '';
			if(isset($parameter['attachment'])){
				$attachment = $parameter['attachment'];
			}
            if(isset($parameter['invoice_pdf'])){
				$attachment = $parameter['invoice_pdf'];
			}
            // Send the email via the SendGrid API
            $return = $mailObj->sendMail($to, $to_name, $subject, $message, $attachment);
            
            // Check if the email was sent successfully
            if ($return['status'] == 'success') {
                $response['status'] = true;
                $response['message'] = $return['message'];
            } else {
                // Handle failure response
                $response['status'] = false;
                $response['message'] = $return['message'];
            }

        } catch (Exception $e) {
            // Handle any exceptions during the email sending process
            $response['status'] = false;
            $response['message'] = $e->getMessage();
        }
        // Return the response with the status and message
        return $response;
    }


    if(!function_exists("capLetter")){
        function capLetter($name){
           return ucwords(strtolower($name));
        }
    }
    if(!function_exists("expFilter")){
        function expFilter(){
          $data = ['1 to 3','3 to 5','5 and above'];
          return $data;
        }
    }

}


if (!function_exists("checkSetting")) {

    /**
     * Retrieve a setting's value by its meta key.
     * 
     * This helper function fetches the value of a setting from the `settings` table based 
     * on the provided `meta_key`. If the key exists, it returns the corresponding `meta_value`; 
     * otherwise, it returns an empty string.
     *
     * @param string $key The key (meta_key) for which the setting value should be retrieved.
     * @return string The value associated with the provided key (meta_value), or an empty string if not found.
     */
    function checkSetting($key = '') {
        $setting = \DB::table("settings")->where("meta_key", $key)->first();
        $meta_value = '';
        if (!empty($setting)) {
            $meta_value = $setting->meta_value;
        }
        return $meta_value;
    }
}
if(!function_exists("pre")){
function pre($arr){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}
}

if (!function_exists("getImage")) {

    /**
     * Fetches an image from a specific table, folder, and based on a unique ID.
     * The function supports different image sizes (original, medium, thumb).
     *
     * @param string $table      The name of the database table to fetch data from.
     * @param string $unique_id  The unique identifier for the record in the table.
     * @param string $folderName The folder name where the images are stored.
     * @param string $fieldName  The column name in the table where the image filename is stored (default is 'image').
     * @param string $size       The size of the image to return ('r' for original, 'm' for medium, 't' for thumbnail).
     *                           Default is 'r' (original).
     *
     * @return string The URL of the image or the path to a default image if not found.
     */
    function getImage($table, $unique_id, $folderName, $fieldName = 'image', $size = 'r'){

        // Fetch the record from the database table using the unique ID
        $tableObj = DB::table($table)->where("unique_id", $unique_id)->first();

        // Get the image filename from the specified field
        $imageName = $tableObj->$fieldName ?? '';
        $imageDir = public_path("uploads/{$folderName}/{$unique_id}/") . $imageName;

        // Check if the image exists, else return default image URL
        if ($imageName == '' || !file_exists($imageDir)) {
            return "public/assets/svg/browse.svg";
        }

        // Base URL for the original image
        $original = "public/uploads/{$folderName}/{$unique_id}/" . $imageName;

        // Initialize the URL variable
        $url = '';

        // Determine which size of the image to return
        if ($size == 'r') {
            $url = $original;
        } elseif ($size == 'm') {
            // Check for medium-sized image
            if (file_exists(public_path("uploads/{$folderName}/{$unique_id}/medium/") . $imageName)) {
                $url = "public/uploads/{$folderName}/{$unique_id}/medium/" . $imageName;
            } else {
                $url = $original;
            }
        } elseif ($size == 't') {
            // Check for thumbnail-sized image
            if (file_exists(public_path("uploads/{$folderName}/{$unique_id}/thumb/") . $imageName)) {
                $url = "public/uploads/{$folderName}/{$unique_id}/thumb/" . $imageName;
            } else {
                $url = $original;
            }
        }

        // If no size is matched, return the original size
        if ($url == '') {
            $url = $original;
        }

        return $url;
    }
}

if (!function_exists('deleteFileAndFolder')) {
    /**
     * Deletes a record folder from the server.
     * 
     * This function deletes a folder and its contents based on the provided unique ID 
     * and folder name. It checks whether the folder exists and, if it does, deletes it.
     * 
     * @param string $uniqueId The unique identifier for the folder to be deleted.
     * @param string $folderName The name of the folder where the records are stored.
     * 
     * @return bool Returns true if the folder was deleted successfully, false otherwise.
     */
    function deleteFileAndFolder($uniqueId, $folderName)
    {
        // Check if both $uniqueId and $folderName are provided
        if (empty($uniqueId) || empty($folderName)) {
            return false; // Handle the case when parameters are missing or invalid
        }

        // Construct the full path to the folder
        $fullPath = public_path('uploads/' . $folderName . '/' . $uniqueId);

        // Check if the folder exists and delete the directory and its contents
        if (File::exists($fullPath)) {
            File::deleteDirectory($fullPath); // Deletes the directory and all its files
        }

        return true; // Return true to indicate successful deletion
    }

    // if (! function_exists('checkPrivilege')) {
    //     function checkPrivilege($module,$action,$role_id = '') {
    //         $role_id = auth()->user()->role;
    //         if($role_id != 'professional'){
               
    //             if($role_id != ''){
    //                 $check_access = RolePrevilege::where("role",$role_id)->where("module",$module)->where("action",$action)->count();
    //                 if($check_access > 0){
    //                     return true;
    //                 }else{
    //                     return false;
    //                 }
    //             }else{
    //                 return false;
    //             }
    //         }else{
    //             return true;
    //         }
           
    //     }
    // }
}
if (! function_exists('checkPrivilege')) {
    function checkPrivilege($parameter) {
        
        $role_id = auth()->user()->role;
        if($role_id != 'professional'){
            
            if($role_id != ''){
                $check_module =  Module::where('route_prefix',$parameter['route_prefix'])->where('panel','professional')->first();
               
                if(!empty($check_module)){
                    
                    $check_access = RolePrevilege::where("role",$role_id)->where("module",$check_module->slug)->where("action",$parameter['action'])->count();
                    
                    if($check_access > 0){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
                
            }else{
                return false;
            }
        }else{
            return true;
        }
        
    }
}
if(!function_exists("generateOtp")){
function generateOtp($n=6) { 
    $characters = '1123456789'; 
    $randomString = ''; 
    $randomString = substr(str_shuffle($characters), 0, $n);     
    return $randomString; 
} 
}
if(!function_exists("sendOtp")){
    function sendOtp($email,$template,$type="send_otp") { 
    
        $otp = generateOtp();
        $expiry_time = Carbon::now()->addMinutes(2);
        $resend_attempt = 0;
        if($type != "resend_otp"){
            OtpVerify::where('email',$email)->delete();
        }else{
        
            $get_otp = OtpVerify::where("email",$email)->latest()->first();
            $resend_attempt = $get_otp->resend_attempt??0;
            
            if(!empty($get_otp)){
                $resend_attempt = $resend_attempt + 1;
            }

        }
        $object = OtpVerify::updateOrCreate(['email'=>$email],['otp'=>$otp,'otp_expiry_time'=>$expiry_time,'resend_attempt' => $resend_attempt]);
        $mailData = ['otp' => $otp, 'token' => $object->unique_id];
        $view = \View::make($template, $mailData);
        $message = $view->render();
        
        \Session::put('temp_otp',$otp);
        $parameter = [
            'to' => $email,
            'to_name' => '',
            'message' => $message,
            'subject' => siteSetting('company_name').': OTP Verification',
            'view' => 'emails.otp-mail',
            'data' => $mailData,
        ];
        $mailRes = sendMail($parameter);
        
        return $object;
    }
}
if(!function_exists("getUserInfo")){
function getUserInfo($unique_id){
    $user = User::where("unique_id",$unique_id)->first();
    return $user;
}
}
if(!function_exists("generateUUID")){
function generateUUID(){
    $uuid = Str::uuid()->toString();
    return $uuid;
}
}
if (!function_exists("decryptVal")) {
    function decryptVal($value) {
        if (!empty($value)) {
            try {
                return decrypt($value);
            } catch (\Exception $e) {
                return $value;
            }
        }
        return $value;
    }
}
if(!function_exists("checkWebsiteStatus")){

function checkWebsiteStatus($url)
{
    try {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Referer' => 'https://google.com',
            'Accept-Language' => 'en-US,en;q=0.9'
        ])->timeout(60)->get($url);
        if ($response->successful()) {
            return 1;
        } elseif ($response->serverError()) {
            return 0;
        } elseif ($response->clientError()) {
            return 0;
        }
    } catch (\Exception $e) {
        return 0;
    }
}
}


if (! function_exists('knowledgeBaseUrl')) {
    function knowledgeBaseUrl($url = '') {
        $base = 'knowledgebase/page';
        if($url != ''){
            if (strpos($url, '/') === 0) {
                $base_url = url($base.$url);
            }else{
                $base_url = url($base.'/'.$url);
            }
        }else{
            $base_url = url($base);
        }
        return $base_url;
    }
}
if (! function_exists('getMediaName')) {
    function getMediaName($string) {
        $delimiter = ".";
        $position = strpos($string, $delimiter);
        return $substring = substr($string, 0, $position);
    }
}
if (! function_exists('getMediaExt')) {
    function getMediaExt($string) {
        $delimiter = ".";
        $position = strpos($string, $delimiter);
        return $substring = substr($string, $position + 1);
    }
}
if(!function_exists("generateDnsTxt")){

function generateDnsTxt() {
    return 'trustvisory-site-verification=' . bin2hex(random_bytes(24));
}
}
if(!function_exists("verifyTxtRecord")){

function verifyTxtRecord($domain, $expectedTxtRecord)
{
    // Fetch the DNS records for the domain, specifically TXT records
    $dnsRecords = dns_get_record($domain, DNS_TXT);
    if ($dnsRecords === false) {
        return false;
    }

    // Loop through each DNS TXT record
    foreach ($dnsRecords as $record) {
        if (isset($record['txt']) && $record['txt'] === $expectedTxtRecord) {
            return true;
        }
    }
    return false;
}
}

if(!function_exists("getBaseDomain")){
function getBaseDomain($url) {
    // Parse the URL to get the host part
    // $host = parse_url($url, PHP_URL_HOST);
    // Remove "www." if it exists
    $host = str_replace('www', '', $url);
    $host = str_replace('https://', '', $url);
    $host = str_replace('http://', '', $url);

    return $host;
}
}

if (!function_exists('saveSeoDetails')) {
    /**
     * Save or update SEO details.
     *
     * @param array $parameters
     * @return mixed
     */
    function saveSeoDetails(array $parameters)
    {
        try {
            $seoDetail = SeoDetails::where('reference_id', $parameters['reference_id'])
            ->where('module_type', $parameters['module_type'])
                ->first();
         
            if ($seoDetail) {
                $seoDetail->update($parameters);
            } else {
                $seoDetail = SeoDetails::create($parameters);
            }
            return $seoDetail;
        } catch (\Exception $e) {
            // Log any errors that occur during saving
            Log::error('Error saving SEO details: ' . $e->getMessage());

            return false; 
        }
    }
}

if (!function_exists('deleteSeoDetails')) {
    function deleteSeoDetails(array $parameters)
    {
        try {
            $seoDetail = SeoDetails::where('reference_id', $parameters['reference_id'])
                ->where('module_type', $parameters['module_type'])
                ->first();

            if ($seoDetail) {
                $seoDetail->delete();
                return true;
            } else {
                return false; 
            }
        } catch (\Exception $e) {
            Log::error('Error deleting SEO details: ' . $e->getMessage());
            return false;
        }
    }
}
if(!function_exists("supportAmount")){

function supportAmount(){
    $amounts = array("5","10","50","100");
    return $amounts;
}
}
if(!function_exists("currencySymbol")){

function currencySymbol($default = ''){
    if($default == ''){
        $currency = currency();
    }else{
        $currency = $default;
    }
    if($currency == 'CAD'){
        $currency_symbol = "$";
    }elseif($currency == 'USD'){
        $currency_symbol = "$";
    }elseif($currency == 'INR'){
        $currency_symbol = "₹";
    }else{
        $currency_symbol = "$";
    }
    return $currency_symbol;
}

}

function getCurrency($default = ''){
    if($default == '$'){
        $currency_symbol = "CAD";
    }elseif($default == '$'){
        $default = "USD";
    }elseif($default == '₹'){
        $currency_symbol = "INR";
    }else{
        $currency_symbol = "CAD";
    }
    return $currency_symbol;
}


if(!function_exists("currency")){

function currency(){
    
    $currency = "USD";
    try{

        $ip = request()->ip();
        // Default currency
        // Check IP and set currency based on location
        if ($ip !== '127.0.0.1') { // Avoid localhost IP
            $client = new Client();
            $ipresponse = $client->get("http://ipinfo.io/{$ip}/json");
            $locationData = json_decode($ipresponse->getBody()->getContents(), true);
            $country = $locationData['country'] ?? 'Unknown';
            // Extract data
            $currency = $locationData['country'] ?? '$';
            if ($country === 'CA') {
                $currency = 'CAD';
            }else{
                $currency = 'USD';
            }
        }
    } catch (Exception $e) {
        return '';
    }
    return $currency;
}
}
if(!function_exists("generateInvoiceId")){
    function generateInvoiceId() {
        // Query the last invoice ID from the database
        $lastInvoice = Invoice::orderBy('invoice_number', 'desc')->first();

        if ($lastInvoice) {
            // Extract the numeric part and increment it
            $lastId = intval($lastInvoice->invoice_number);
            $newId = $lastId + 1;
        } else {
            // Start from 10000 if no invoices exist
            $newId = 10001;
        }

        // Ensure the new ID is 5 digits
        return str_pad($newId, 5, '0', STR_PAD_LEFT);
    }
}
if(!function_exists("siteSetting")){

function siteSetting($key = ''){
    $site_setting = SiteSettings::first();
    if(!empty($site_setting)){
        if($key != ''){
            if(isset($site_setting->$key)){
                return $site_setting->$key;
            }else{
                return '';
            }
        }else{
            return $site_setting;
        }
    }else{
        return null;
    }
}
}
if(!function_exists("homeSettings")){

function homeSettings($key = ''){
    $site_setting = HomeSettings::first();
    if(!empty($site_setting)){
        if($key != ''){
            if(isset($site_setting->$key)){
                return $site_setting->$key;
            }else{
                return '';
            }
        }else{
            return $site_setting;
        }
    }else{
        return null;
    }
}
}

if (! function_exists('generateRandomString')) {
    function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
if (! function_exists('getRiskLevel')) {
    function getRiskLevel() {
       return $data = ['1','2','3','4','5'];
    }
}
if(!function_exists("bankList")){
    function bankList() { 
        $netbanking = Netbanking::get();
        return $netbanking;
    }
}

if(!function_exists("WalletList")){
    function WalletList() { 
        $wallet_list = WalletList::get();
        return $wallet_list;
    }
}
if (! function_exists('checkCategoryLevel')) {

function checkCategoryLevel($id)
{   
    $catg = CategoryLevels::where('unique_id',$id)->first();
    $uap = UapProfessionals::where('category_level_id',$catg->id)->first();

    if(!empty($uap)){
        return 'yes';
    } else{
        return 'no';
    }  
}
}
if (! function_exists('checkRefrenceUser')) {

function checkRefrenceUser($id){
    $ref = ReferenceUser::where('unique_id',$id)->first();
    $uap = UapProfessionals::where('reference_user_id',$ref->id)->first();

    if(!empty($uap)){
        return 'yes';
    } else{
        return 'no';
    }  
}
}

if (! function_exists('checkLevel')) {

function checkLevel($id){
    $level = Level::where('unique_id',$id)->first();
    $uap = LevelTag::where('level',$level->id)->first();

    if(!empty($uap)){
        return 'yes';
    } else{
        return 'no';
    }  
}
}
if (! function_exists('checkLevelTag')) {
function checkLevelTag($id){

    $levelTag = LevelTag::where('unique_id',$id)->first();
    $uap = UapLevelTag::where('level_tag_id',$levelTag->id)->first();

    if(!empty($uap)){
        return 'yes';
    } else{
        return 'no';
    }  
}
}
if (! function_exists('checkUapProfessionalsSites')) {
function checkUapProfessionalsSites($id){

    $site = UapProfessionalSites::where('unique_id',$id)->first();
    $uap = UapSitesScreenshot::where('uap_site_id',$site->id)->first();

    if(!empty($uap)){
        return 'yes';
    } else{
        return 'no';
    }  
}
}

if (! function_exists('checkEvidences')) {

function checkEvidences($id)
{
    $evidence = UapEvidences::where('unique_id',$id)->first();
    $uap = EvidenceComments::where('evidence_id',$evidence->id)->first();

    if(!empty($uap)){
        return 'yes';
    } else{
        return 'no';
    }  
}
}

if (! function_exists('getProfileUapLevelTag')) {

function getProfileUapLevelTag($level_tags)
{
    $data =   collect($level_tags)->pluck('level_tag')->toArray();
    $collection = collect($data);
    return $tagNames = $collection->pluck('tag_name')->toArray();
}
}
if (! function_exists('knowledgeBasePage')) {

function knowledgeBasePage($key = ''){
    $page = KnowledgeBasePage::first();
    if(!empty($page)){
        if($key != ''){
            if(isset($page->$key)){
                return $page->$key;
            }else{
                return '';
            }
        }else{
            return $page;
        }
    }else{
        return null;
    }
}
}

if (! function_exists('getUapFeedbackPer')) {
function getUapFeedbackPer($total,$part)
{
    if($total != 0){
        $percentage = ($part / $total) * 100;
        return $percentage. "%";
    }else{
        return "0%";
    }
   
}
}


if (!function_exists("getUserTimezone")) {
    function getUserTimezone()
    {
        try {
            // Use Laravel's request IP for production, fallback to IPify for localhost
            $ipAddress = request()->getHost() === 'localhost'
                ? Cache::remember('public_ip', 3600, function () {
                    try {
                        $response = Http::timeout(3)->get('https://api64.ipify.org?format=json');
                        return $response->json('ip', request()->ip());
                    } catch (\Exception $e) {
                        Log::warning("IPify failed: " . $e->getMessage());
                        return request()->ip(); // Fallback to request IP
                    }
                })
                : request()->ip();

            $cacheKey = "timezone_{$ipAddress}";

            // Cache location data and timezone
            return Cache::remember($cacheKey, 86400, function () use ($ipAddress) {
                try {
                    $response = Http::timeout(3)->get("http://ipinfo.io/{$ipAddress}/json");
                    $data = $response->json();

                    if (!empty($data['timezone'])) {
                        return $data['timezone'];
                    }

                    Log::info("No timezone found for IP: {$ipAddress}");
                    return '';
                } catch (\Exception $e) {
                    Log::warning("IPInfo failed for IP: {$ipAddress} - " . $e->getMessage());
                    return '';
                }
            });
        } catch (\Exception $e) {
            Log::critical("Unexpected error while fetching timezone: " . $e->getMessage());
            return 'Timezone not available';
        }
    }
}



if (!function_exists("isValidTimezone")) {
function isValidTimezone($timezone)
{
    return in_array($timezone, timezone_identifiers_list());
}
}

if (!function_exists("getUserIpAddress")) {
    function getUserIpAddress()
    {
        $forwarded = request()->header('X-Forwarded-For');
        if ($forwarded) {
            $ipAddress = explode(',', $forwarded)[0]; 
        } else {
            $ipAddress = request()->ip();
        }

        return trim($ipAddress);
    }
}

if(!function_exists("detectUserLocation")){
    function detectUserLocation()
    {
         $agent = new Agent();

        // IP detection
     $ip = getUserIpAddress();
    //   $ip = '2402:a00:152:1b39:305f:f87a:9279:eddb';
        $accessToken = apiKeys('IPINFO_KEY');
        $url = "https://ipinfo.io/{$ip}?token={$accessToken}";

        $locationData = @json_decode(file_get_contents($url));
        $loc = explode(',', $locationData->loc ?? '');

        // Device Info
        $platform = $agent->platform();
        $platformVersion = $agent->version($platform);
        $browser = $agent->browser();
        $browserVersion = $agent->version($browser);
        $deviceType = $agent->isMobile() ? 'Mobile' : 'Desktop';
        $deviceName = $agent->device();

         // Log to ip_logs table
        IpLog::create([
            'ip_address' => $ip, 
            'page_url' => url()->current(),
            'user_id' =>  auth()->id() ?? 0, 
        ]);
        return [
            'ip' => $ip,
            'platform' => $platform,
            'platform_version' => $platformVersion,
            'browser' => $browser,
            'browser_version' => $browserVersion,
            'device_type' => $deviceType,
            'device_name' => $deviceName,
            'city' => $locationData->city ?? '',
            'region' => $locationData->region ?? '',
            'country' => $locationData->country ?? '',
            'latitude' => $loc[0] ?? '',
            'longitude' => $loc[1] ?? '',
            'timezone' => $locationData->timezone ?? '',
            'org' => $locationData->org ?? '',
        ];

       

    }
}

if (!function_exists("detectUserCity")) {
    function detectUserCity()
    {
        //$ipAddress = "103.156.145.173";
        $ipAddress = request()->ip();
        $response = Http::get('https://api64.ipify.org?format=json');
        if ($response->successful()) {
            $ipAddress = $response->json('ip');
        } else {
            $ipAddress = request()->ip();
        }

        // Fetch location data based on IP address
        $client = new Client();
        $ipresponse = $client->get("http://ipinfo.io/{$ipAddress}/json");
        $locationData = json_decode($ipresponse->getBody()->getContents(), true);

        // Extract only city data
        $city = $locationData['city'] ?? 'Unknown';

        return $city;
    }
}

if (!function_exists("detectUserDevice")) {
    function detectUserDevice()
    {
        // Create an instance of Agent
        $agent = new Agent();

        // Detect device type
        $deviceType = $agent->isMobile() ? 'Mobile' : 'Desktop';

        return $deviceType;
    }
}

if(!function_exists("convertToUserTimezone")){
    function convertToUserTimezone($dateTime, $userTimezone = 'Asia/Kolkata')
    {
       $userTimezone = $userTimezone ?: 'Asia/Kolkata'; 
        return Carbon::parse($dateTime, 'UTC')->setTimezone($userTimezone);
    }
}
if (! function_exists('ownerType')) {

function ownerType(){
    $arr = array("Self Employed","Employed");
    return $arr;
}
}
if (! function_exists('companyType')) {
function companyType(){
   $arr = array("Sole proprietorship","Partnership","Private limited company","Limited liability partnership (LLP)","Limited Liability Company (LLC)","Other");
    return $arr;
}
}
if (! function_exists('licensePrefix')) {

function licensePrefix($id){
    $data = CdsRegulatoryBody::where("id",$id)->first();
    return $data->license_prefix??'';
}
}
if (! function_exists('generateTemplate')) {

function generateTemplate($template,$data){
    $keywords = emailKeywords();
    foreach($keywords as $keyword){
        if(isset($data[$keyword])){
            $template = str_replace("#".$keyword."#",$data[$keyword],$template);
        }else{
            $template = str_replace("#".$keyword."#",'',$template);
        }
    }
    return $template;
}
}
if (! function_exists('emailKeywords')) {

function emailKeywords(){
    $keywords = array("name","otp","custom_url","deviceType","city","company_name","status","email"
,"password","score","comment","professional_name","score","city","deviceType","title","comment",
"url","template_content","user_name");
    return $keywords;
}
}
if (!function_exists('compressImage')) {
    /**
     * Compress an image using GD library.
     *
     * @param string $source Path to the source image.
     * @param string $destination Path to save the compressed image.
     * @param int $quality Quality of compression (0-100 for JPEG, 0-9 for PNG).
     * @return bool True on success, false on failure.
     */
    function compressImage($source, $destination, $quality = 75)
    {
        // Get the mime type of the image
        $mime = mime_content_type($source);

        // Load the image based on mime type
        if ($mime === 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
            $result = imagejpeg($image, $destination, $quality);
        } elseif ($mime === 'image/png') {
            $image = imagecreatefrompng($source);
            // Adjust quality for PNG (0-9 scale)
            $result = imagepng($image, $destination, round($quality / 10));
        } else {
            // Unsupported format
            return false;
        }

        // Free up memory
        imagedestroy($image);

        return $result;
    }
}
if (!function_exists("uapPerPage")) {
function uapPerPage(){
    $per_pages = array("10","20","50");
    return $per_pages;
}
}

if (!function_exists("uapQueryString")) {
    function uapQueryString(){
        $data = ["critical_alerts" => 'fA','type' => 'fB','category_levels' => 'fC','risk_score' =>'fD'];
    }
}
if (!function_exists("countries")) {
    function countries(){
        $countries =  Country::get();
        return $countries;
    }
}

if (!function_exists("editorShortcode")) {
function editorShortcode(){
    $patterns = [
        'Evidence' => "shortcode_type_1",
        'Solutions' => "shortcode_type_1",
        'ClaimAlert' => "shortcode_type_1",
        'Keytakings' => "shortcode_type_1",
        'Facts' => "shortcode_type_1",
        'Disclaimer' => "shortcode_type_1",
        'Statement' => "shortcode_type_1",
        'BreakSection' => "shortcode_type_1",
        'Highlight' => "shortcode_type_1",

        'ImageGallery' => "shortcode_type_2",
        'ImageSingle' => "shortcode_type_2",
        
        'Reference' => "shortcode_type_3",

        'SpecialTitle1' => "shortcode_type_4",
        'SpecialTitle2' => "shortcode_type_4",

        'ReconsiderFees'  => "shortcode_type_5",
    ];

    return $patterns;
}
}

if (!function_exists("getRegion")) {
function getRegion($id)
{
   $country =  Country::where('id',$id)->first();
   return $country->name??'N/A';
}
}

if (!function_exists("getProfessionalExp")) {
function getProfessionalExp($start_date,$type)
{
    $startDate = Carbon::createFromFormat('Y-m-d', $start_date); // Example: July 4, 2024
    $currentDate = Carbon::now(); // Gets the current date

    $diffInMonths = $startDate->diffInMonths($currentDate); // Total months
    $years = floor($diffInMonths / 12); // Full years
    $remainingMonths = $diffInMonths % 12; // Remaining months

    $result = '';
    $types = '';
    if ($years < 1) {
        $result = $diffInMonths;
        $types = 'Months';
        // echo "Experience: $diffInMonths months"; // If less than 1 year, display only months
    } else {
        $result = $years + round($remainingMonths / 12, 1);
        $types = 'Years';
        // echo "Experience: $decimalExperience years";
    }
    if($type == 'date'){
        return $result;
    }else{
        return $types;
    }
    
}
}

if (!function_exists("generateQRCode")) {
function generateQRCode($unique_id,$uniqueId)
{
    $uniqueId = bin2hex(random_bytes(32));
    $writer = new PngWriter();
    $qrCode = new QrCode(
        data: $uniqueId,
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::Low,
        size: 300,
        margin: 10,
        roundBlockSizeMode: RoundBlockSizeMode::Margin,
        foregroundColor: new Color(0, 0, 0),
        backgroundColor: new Color(255, 255, 255)
    );

    // Path to logo
    // $logoPath = public_path('assets/logo.png');  // Path to the logo you want to overlay on the QR code
    $logoPath = public_path("assets/images/tv-barcode.png");
    // Create the logo
    // $logo = new Logo(
    //     path: $logoPath,
    //     resizeToWidth: 150,  // Resize logo width (adjust as needed)
    //     punchoutBackground: true  // Remove the background of the logo
    // );

    // Create the label
    // $label = new Label(
    //     text: 'Scan me for more info!',  // Custom text for the label
    //     textColor: new Color(255, 0, 0)  // Red text color
    // );

    // Write the QR code with logo and label
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // Define the path to the 'barcodes' directory
    $FolderPath = public_path('uploads/barcodes');

    // Check if the 'barcodes' directory exists, and create it if it doesn't
    if (!File::exists($FolderPath)) {
        File::makeDirectory($FolderPath, 0755, true); // 0755 permissions, true to create nested directories if needed
    }

    $outputPath = public_path('uploads/barcodes/'.$unique_id.'.png');
    // Save the final image
    // $outputPath = public_path('images/merged-qrcode-with-logo-label.png');
    // file_put_contents($outputPath, $result->getString()); 
    $qrCodeData = $result->getString();

    $mergedImagePath = public_path('uploads/barcodes/'.$unique_id.'.png');

    
    $staticImagePath = public_path("assets/images/tv-hor-barcode.png");
    
    mergeQRCodeWithImage($staticImagePath, $qrCodeData, $mergedImagePath,$unique_id);

    $staticVerticalImagePath = public_path("assets/images/tv-ver-barcode.png");
    mergeQRCodeWithVeticleImage($staticVerticalImagePath, $qrCodeData, $mergedImagePath,$unique_id);

    $url = url('uploads/barcodes/'.$unique_id.'.png');

    $removeImagePath = public_path('uploads/barcodes/'.$unique_id.'.png');
    if (File::exists($removeImagePath)) {
        File::delete($removeImagePath); 
    }

    return $url;
}
}
if (!function_exists("mergeQRCodeWithImage")) {
function mergeQRCodeWithImage($staticImagePath, $qrCode, $outputPath,$unique_id)
{
    $staticImage = imagecreatefrompng($staticImagePath);
    $qrCodeImage = imagecreatefromstring($qrCode);

    $desiredQRCodeWidth = 110;
    $desiredQRCodeHeight = 105;

    $qrCodeWithBackground = imagecreatetruecolor($desiredQRCodeWidth, $desiredQRCodeHeight);
    $white = imagecolorallocate($qrCodeWithBackground, 255, 255, 255);
    imagefilledrectangle($qrCodeWithBackground, 0, 0, $desiredQRCodeWidth, $desiredQRCodeHeight, $white);

    imagecopyresampled($qrCodeWithBackground, $qrCodeImage, 0, 0, 0, 0, $desiredQRCodeWidth, $desiredQRCodeHeight, imagesx($qrCodeImage), imagesy($qrCodeImage));

    $text = $unique_id;
    $fontSize = 5; // Built-in font size (1-5)
    $textColor = imagecolorallocate($staticImage, 255, 255, 255); // Black color
    $textX = 50; // X position of text
    $textY = 70; // Y position of text

    imagestring($staticImage, $fontSize, $textX, $textY, $text, $textColor);

    // Static image dimensions
    $staticWidth = imagesx($staticImage);
    $staticHeight = imagesy($staticImage);

    // Position QR code at the bottom center of the static image
    // $x = ($staticWidth - $desiredQRCodeWidth) / 2;
    // $y = $staticHeight - $desiredQRCodeHeight - 20;  // 20px margin

    $x = 180;
    $y  = 15;
   
    imagecopy($staticImage, $qrCodeWithBackground, $x, $y, 0, 0, $desiredQRCodeWidth, $desiredQRCodeHeight);

    
    // Save the merged image
    imagepng($staticImage, $outputPath);

    // media server upload
    $uploadPath = 'barcode/'.$unique_id.'/';
    $sourcePath = $outputPath;
    $newName = $unique_id.'-horizontal.png';
    $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $newName);
    
    // if (($api_response['status'] ?? '') === 'success') {
    //     // $path = public_path('images/example.jpg');
    //     $path = url('uploads/barcodes/'.$unique_id.'.png');
    //     if (file_exists($path)) {
    //         unlink($path);
    //     }
    // }
    // media servre upload
    // Clean up
    imagedestroy($staticImage);
    imagedestroy($qrCodeImage);
    imagedestroy($qrCodeWithBackground);
}
}

if (!function_exists("mergeQRCodeWithVeticleImage")) {
function mergeQRCodeWithVeticleImage($staticImagePath, $qrCode, $outputPath,$unique_id)
{
    $staticImage = imagecreatefrompng($staticImagePath);
    $qrCodeImage = imagecreatefromstring($qrCode);

    $desiredQRCodeWidth = 145;
    $desiredQRCodeHeight = 145;

    $qrCodeWithBackground = imagecreatetruecolor($desiredQRCodeWidth, $desiredQRCodeHeight);
    $white = imagecolorallocate($qrCodeWithBackground, 255, 255, 255);
    imagefilledrectangle($qrCodeWithBackground, 0, 0, $desiredQRCodeWidth, $desiredQRCodeHeight, $white);

    imagecopyresampled($qrCodeWithBackground, $qrCodeImage, 0, 0, 0, 0, $desiredQRCodeWidth, $desiredQRCodeHeight, imagesx($qrCodeImage), imagesy($qrCodeImage));

    $text = $unique_id;
    $fontSize = 5; // Built-in font size (1-5)
    $textColor = imagecolorallocate($staticImage, 255, 255, 255); // Black color
    $textX = 80; // X position of text
    $textY = 220; // Y position of text

    imagestring($staticImage, $fontSize, $textX, $textY, $text, $textColor);

    // Static image dimensions
    $staticWidth = imagesx($staticImage);
    $staticHeight = imagesy($staticImage);

    // Position QR code at the bottom center of the static image
    // $x = ($staticWidth - $desiredQRCodeWidth) / 2;
    // $y = $staticHeight - $desiredQRCodeHeight - 20;  // 20px margin

    $x = 55;
    $y  = 63;
   
    imagecopy($staticImage, $qrCodeWithBackground, $x, $y, 0, 0, $desiredQRCodeWidth, $desiredQRCodeHeight);

    
    // Save the merged image
    imagepng($staticImage, $outputPath);

    // media server upload
    $uploadPath = 'barcode/'.$unique_id.'/';
    $sourcePath = $outputPath;
    $newName = $unique_id.'-verticle.png';
    $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $newName);

    
    // media servre upload
    // Clean up
    imagedestroy($staticImage);
    imagedestroy($qrCodeImage);
    imagedestroy($qrCodeWithBackground);
}
}

if (!function_exists("discussionListData")) {
function discussionListData($type = "my", $search = "", $page = "")
{

    $query = DiscussionBoard::with(['user', 'category']);
    if ($type == "other") {
        $connection_ids = DiscussionBoard::where('added_by', auth()->user()->id)
        ->pluck('added_by') // Extract only 'added_by' values
        ->toArray();
    
    $query->whereNotIn('added_by', $connection_ids);

    } elseif ($type == "commented") {
        $commentedFeedIds = DiscussionBoard::where('added_by', auth()->user()->id)
            ->pluck('feed_id')
            ->toArray();

        $query->whereIn('id', $commentedFeedIds);


    } else {
        $query->where('added_by', auth()->user()->id);

    }
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('post', 'LIKE', "%{$search}%") // Search in feed post
                ->orWhereHas('user', function ($q) use ($search) { // Search in user name
                    $q->where('first_name', 'LIKE', "%{$search}%")
                    ;
                })
                ->orWhereHas('comments', function ($q) use ($search) { // Search in comments by the current user
                    $q->where('added_by', auth()->user()->id)
                        ->where('comment', 'LIKE', "%{$search}%");
                });
        });
    }

    $query->latest();
    $feedsData = $query->get();

    return $feedsData;

}
}
// if (!function_exists("formJsonSample")) {
//     function formJsonSample(){
//         $json = '[{"fields":"fieldGroups","groupFields":["605725753","917622672","940604318","405662139","314191809","294666146","822318960","860168273","453611488","859438251","284324323"],"settings":{"label":"Fields Groups","shortDesc":"","font_size":"32","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":233270132},{"fields":"textInput","settings":{"label":"Text Input","name":"fg_77617","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":605725753},{"fields":"numberInput","settings":{"label":"Number Input","name":"fg_65099","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":917622672},{"fields":"textarea","settings":{"label":"Text Editor","name":"fg_35296","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description","textLimit":"wordLimit","addLength":""},"index":940604318},{"fields":"emailInput","settings":{"label":"Email","name":"fg_89760","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":405662139},{"fields":"url","settings":{"label":"Link Input","name":"fg_25195","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":314191809},{"fields":"dropDown","settings":{"options":{"12864":"dropdown 1","39285":"dropdown 2"},"label":"Drop Down","name":"fg_47093","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":294666146},{"fields":"checkbox","settings":{"options":{"62299":"checkbox 1","79777":"checkbox 2"},"label":"Checkbox","name":"fg_64611","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":822318960},{"fields":"radio","settings":{"options":{"61376":"radio 1","83571":"radio 2"},"label":"Radio","name":"fg_11023","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":860168273},{"fields":"dateInput","settings":{"label":"Datepicker","name":"fg_80220","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":453611488},{"fields":"addressInput","settings":{"label":"Google Address","name":"fg_59283","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":859438251},{"fields":"fgDropzone","settings":{"label":"Document Upload","name":"fg_69256","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":284324323}]';
//         return json_decode($json,true);
//     }
// }

// if (!function_exists("formJsonSample")) {
//     function formJsonSample(){
//         $json = '[{"fields":"fieldGroups","groupFields":["605725753","917622672","940604318","405662139","314191809","294666146","822318960","860168273","453611488","859438251","284324323"],"settings":{"label":"Fields Groups","shortDesc":"","font_size":"32","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":233270132},{"fields":"textInput","settings":{"label":"Text Input","name":"fg_77617","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":605725753},{"fields":"numberInput","settings":{"label":"Number Input","name":"fg_65099","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":917622672},{"fields":"textarea","settings":{"label":"Text Editor","name":"fg_35296","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description","textLimit":"wordLimit","addLength":""},"index":940604318},{"fields":"emailInput","settings":{"label":"Email","name":"fg_89760","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":405662139},{"fields":"url","settings":{"label":"Link Input","name":"fg_25195","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":314191809},{"fields":"dropDown","settings":{"options":{"12864":"dropdown 1","39285":"dropdown 2"},"label":"Drop Down","name":"fg_47093","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":294666146},{"fields":"checkbox","settings":{"options":{"62299":"checkbox 1","79777":"checkbox 2"},"label":"Checkbox","name":"fg_64611","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":822318960},{"fields":"radio","settings":{"options":{"61376":"radio 1","83571":"radio 2"},"label":"Radio","name":"fg_11023","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":860168273},{"fields":"dateInput","settings":{"label":"Datepicker","name":"fg_80220","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":453611488},{"fields":"addressInput","settings":{"label":"Google Address","name":"fg_59283","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":859438251},{"fields":"fgDropzone","settings":{"label":"Document Upload","name":"fg_69256","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":284324323}]';
//         return json_decode($json,true);
//     }
// }

function getSampleFormJson(bool $withSteps = false): array
{
    $json = '[{"fields":"fieldGroups","groupFields":["605725753","917622672","940604318","405662139","314191809","294666146","822318960","860168273","453611488","859438251","284324323"],"settings":{"label":"Fields Groups","shortDesc":"","font_size":"32","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":233270132},{"fields":"textInput","settings":{"label":"Text Input","name":"fg_77617","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":605725753},{"fields":"numberInput","settings":{"label":"Number Input","name":"fg_65099","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":917622672},{"fields":"textarea","settings":{"label":"Text Editor","name":"fg_35296","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description","textLimit":"wordLimit","addLength":""},"index":940604318},{"fields":"emailInput","settings":{"label":"Email","name":"fg_89760","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":405662139},{"fields":"url","settings":{"label":"Link Input","name":"fg_25195","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":314191809},{"fields":"dropDown","settings":{"options":{"12864":"dropdown 1","39285":"dropdown 2"},"label":"Drop Down","name":"fg_47093","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":294666146},{"fields":"checkbox","settings":{"options":{"62299":"checkbox 1","79777":"checkbox 2"},"label":"Checkbox","name":"fg_64611","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":822318960},{"fields":"radio","settings":{"options":{"61376":"radio 1","83571":"radio 2"},"label":"Radio","name":"fg_11023","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":860168273},{"fields":"dateInput","settings":{"label":"Datepicker","name":"fg_80220","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":453611488},{"fields":"addressInput","settings":{"label":"Google Address","name":"fg_59283","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":859438251},{"fields":"fgDropzone","settings":{"label":"Document Upload","name":"fg_69256","shortDesc":"","placeholder":"","maxlength":"","stepHeading":"Step Heading","stepDescription":"Step Description"},"index":284324323}]';
    $fields = json_decode($json, true);
    if ($withSteps) {
        $step = 1;
        foreach ($fields as $order => &$field) {
            $field['step'] = (string)$step;
            $field['order'] = (string)$order;
        }
        unset($field);
    }
    return $fields;
}
if(!function_exists("getRoles")){
    function getRoles(){
        $currentUserId=auth()->user()->id;
        $roles= Roles::where('added_by',$currentUserId);
        if(auth()->user()->role!="professional"){
            $getProfessional=staffProfessionals(auth()->user()->id);
            $roles->orWhere('added_by',$getProfessional->added_by);
        }
       $fetchRoles= $roles->get();
       return $fetchRoles;
    }
}
if(!function_exists("getProfeilImage")){
    function getProfeilImage($profile_image,$id){
        $user = User::where('id',$id)->first();
        $dir = userDirUrl($profile_image);
        $name = strtoupper(substr($user->first_name, 0, 1)) .''.strtoupper(substr($user->last_name, 0, 1)) ;

        if ($profile_image != ''){
            return '<img id="showProfileImage" class="img-fluid cdsImg" src="'.$dir.'" alt="Profile Image">';
        }else{
            return '<div class="group-icon img-profile me-2" data-initial="'.$name.'"></div>';
        }
   
    }
}

if(!function_exists("staffProfessionals")){
    function staffProfessionals($user_id){
     $staffProfessional=  StaffUser::where('user_id',$user_id)->first();
        
        return $staffProfessional;
    }
}

if(!function_exists("fetchStaffOfSpecificProfessional")){
    function fetchStaffOfSpecificProfessional($professional_id){
     $staffOfProfessional=  StaffUser::visibleToUser(auth()->user()->id)->get()->pluck('user_id')->toArray();
        
        return $staffOfProfessional;
    }
}

if(!function_exists("fetchProfessionalCompanyName")){
    function fetchProfessionalCompanyName($user){
      
       if($user->role=="professional"){
        $companyName= $user->cdsCompanyDetail->company_name??'';
       }else{
        $professional= staffProfessionals($user->id);
        $professionalData= User::with('cdsCompanyDetail')->where('id',$professional->added_by)->first();
        $companyName= $professionalData->cdsCompanyDetail->company_name??'';
       }

        return $companyName;
    }
    
}

if(!function_exists("contFeedStatusData")){
    function contFeedStatusData($status){

        if($status == 'my-feed'){
            return $feed = Feeds::where('added_by',auth()->user()->id)
            ->orderBy('id', 'desc')
            ->count();
        }else if($status == 'all-feed'){
            return  $records = Feeds::with(['user', 'likes', 'comments' => function ($query) {
                $query->where('added_by', auth()->id());
            }])
                ->whereHas("user")
                ->count();
        }else if($status == 'draft'){
            return $feed = Feeds::where('added_by',auth()->user()->id)->where('status','draft')
            ->orderBy('id', 'desc')
            ->count();
        }
        else if($status == 'scheduled'){
            return $feed = Feeds::where('added_by',auth()->user()->id)->where('status','scheduled')
            ->orderBy('id', 'desc')
            ->count();
        }else if($status == 'commented'){
               return  Feeds::whereHas('comments', function ($q) { // Search in comments by the current user
                    $q->where('added_by', auth()->user()->id);
                })->count();
        }
        else if($status == 'pinned'){
            return $feed = Feeds::where('added_by',auth()->user()->id)->where('is_pin',1)
            ->orderBy('id', 'desc')
            ->count();
        }else if($status == 'favourite')
        {
            $favouriteFeedIds = FeedFavourite::where('user_id', auth()->user()->id)
            ->pluck('feed_id')
            ->toArray();
          
            return Feeds::whereIn('id', $favouriteFeedIds)->count();
        }
      

          
        
    }
    
}
if(!function_exists("getBetweenDates")){
    function getBetweenDates($startDate, $endDate)
    {
        $rangArray = [];
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);


        for ($currentDate = $startDate; $currentDate <= $endDate; $currentDate += (86400)) {                            

            $date = date('Y-m-d', $currentDate);
            $rangArray[] = $date;

        }
        return $rangArray;

    }
}

if(!function_exists("calculateTax")){
function calculateTax($taxPercent,$amount){
    $tax_amount = ($amount * $taxPercent)/100;
    return $tax_amount;
}
}

if(!function_exists("calculatePoints")){
function calculatePoints($amount, $currency) {
    
    // Fetch dynamic tiers from database
    $support_amounts = SupportAmounts::where("currency", $currency)
                                     ->where("status", 1)
                                     ->orderBy("amount", "asc")
                                     ->get();
  
    // Prepare an array of tiers
    $tiers = [];
    foreach ($support_amounts as $sa) {
        $tiers[$sa->amount] = $sa->points;
    }

    // If amount matches an exact tier, return the corresponding points
    if (isset($tiers[$amount])) {
        return $tiers[$amount];
    }

    // Convert associative array to indexed array for sorting
    $tierAmounts = array_keys($tiers);
    // Find the nearest lower and upper tiers
    $lowerTier = null;
    $upperTier = null;

    foreach ($tierAmounts as $tierAmount) {
        if ($tierAmount < $amount) {
            $lowerTier = $tierAmount;
        }
        if ($tierAmount > $amount) {
            $upperTier = $tierAmount;
            break;
        }
    }
    // echo "amount: ".$amount;
    // echo "<br>";
    // echo "lowerTier: ".$lowerTier;
    // echo "<br>";
    // echo "upperTier: ".$upperTier;
    // echo "<br>";
    // If no lower or upper tier exists, return a default (for edge cases)
    if ($lowerTier === null || $upperTier === null) {
        // echo $amount ."*". ($tiers[$tierAmounts[0]]  ."/". $tierAmounts[0]);
        // echo "<br>";
        // echo "<Br>";
        // echo "<hr />";
        return round($amount * ($tiers[$tierAmounts[0]] / $tierAmounts[0])); // Extrapolation for lowest tier
    }
    // echo "lowerTier: ".$lowerTier;
    // echo "<Br>";
    // echo "upperTier: ".$upperTier;
    // echo "<Br>";
    // pre($tiers);    
    // Apply interpolation formula to calculate points between tiers
    $pointsLower = $tiers[$lowerTier];
    $pointsUpper = $tiers[$upperTier];
    // echo "pointsLower: ".$pointsLower;
    // echo "<Br>";
    // echo "pointsUpper: ".$pointsUpper;
    // echo "<Br>";
    // echo $pointsLower." + "."((".$amount."-". $lowerTier.") / (".$upperTier. " - ". $lowerTier.")) *(". $pointsUpper.' - '.$pointsLower.")<br>";
    
    // echo "<Br>";
    // echo "<hr />";
    $interpolatedPoints = $pointsLower + (($amount - $lowerTier) / ($upperTier - $lowerTier)) * ($pointsUpper - $pointsLower);
    
    return round($interpolatedPoints);
}
}

if(!function_exists("calculateBonus")){
function calculateBonus($amount, $currency, $points) {
    $bonus_slabs = SupportBonusPoint::where("currency", $currency)
                             ->where("status", 1)
                             ->orderBy("amount", "asc")
                             ->get();
                             // Apply bonus percentage based on bonus slabs
    $bonusPercentage = 0;
    foreach ($bonus_slabs as $slab) {
        if ($amount >= $slab->amount) {
            $bonusPercentage = $slab->percent;
        }
    }

    // Calculate the bonus points
    $bonusPoints = ($bonusPercentage / 100) * $points;
    $totalPoints = $points + round($bonusPoints);
    $response['percentage'] = $bonusPercentage;
    $response['points'] =round($points);
    $response['bonusPoints'] =round($bonusPoints);
    $response['totalPoints'] = round($totalPoints);
    return $response;

}
}

if(!function_exists("pointEarns")){
function pointEarns($user_id){
    $points = PointEarn::where('user_id', $user_id)->sum('total_points');
    return $points??0;
}
}

if(!function_exists("totalSupportAmounts")){
function totalSupportAmounts($user_id){
    $total_amount = Invoice::where('user_id', $user_id)->sum('sub_total');
    return $total_amount??0;
}
}



// if(!function_exists("supportBadge")){
// function supportBadge($points,$return = 'name'){
//     $points = SupportBadge::where('points',"<=", $points)->orderBy("points","desc")->first();
//     if($return == 'name'){
//         return $points->badge_name??'';
//     }else{
//     return $points;
//     }
// }
// }

if(!function_exists("encryptVal")){
function encryptVal($value){
    if($value != ''){
        $value = encrypt($value);
    }
    return $value;
}
}

if(!function_exists("checkSubscriptionStatus")){
    function checkSubscriptionStatus($userId)
    {
        if (!$userId) {
            return null;
        }
        $subscription = UserSubscriptionHistory::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->first();

        if (!$subscription) {
            return null;
        }
        $stripe = new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));
        try {
            $stripeSubscription = $stripe->subscriptions->retrieve($subscription->stripe_subscription_id, []);

            if ($stripeSubscription->status === 'incomplete' || $stripeSubscription->status === 'unpaid')  {
                $subscriptionType = ucfirst($subscription->subscription_type);
                return "You have unpaid invoice(s) for your {$subscriptionType} Subscription. Please pay the outstanding amount to retain access to services.";
            }
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            \Log::error('Stripe Error: ' . $e->getMessage());
            return "Error retrieving subscription. Please contact support.";
        }

        return null;
    }
}

if (!function_exists('feedCommentCount')) {
  
    function feedCommentCount($feedId)
    {
        return FeedComments::where('feed_id', $feedId)->count() ?? 0;
    }
}
if(!function_exists("getChatNotification")){
    function getChatNotification(){
        return ChatNotification::where('user_id',auth()->user()->id)->where('is_read',0)->where('type','post_case')->orderBy('id','desc')->limit(25)->get();
    }
}
if(!function_exists("getChatNotificationTime")){
    function getChatNotificationTime($time){
        $createdAt = Carbon::parse($time);
        return $createdAt->diffForHumans(); // e.g., "3 minutes ago", "2 days ago"
    }
}

if(!function_exists("sendChatNotification")){
    function sendChatNotification($parameter){
        $chatNotification = new ChatNotification();
        $chatNotification->comment = "Professional ". auth()->user()->first_name." ".auth()->user()->last_name. " submited proposal";
        $chatNotification->type = 'case';
        $chatNotification->redirect_link = $parameter['case_id'];
        $chatNotification->is_read = 0;
        $chatNotification->user_id = $parameter['user_id'];
        $chatNotification->send_by = \Auth::user()->id;
        $chatNotification->save();
    }
}
if(!function_exists("generateQuotationNumber")){
    function generateQuotationNumber() {
        // Query the last invoice ID from the database
        $lastReceipt = CaseQuotation::orderBy('receipt_number', 'desc')->first();

        if ($lastReceipt) {
            // Extract the numeric part and increment it
            $lastId = intval($lastReceipt->receipt_number);
            $newId = $lastId + 1;
        } else {
            // Start from 10000 if no invoices exist
            $newId = 10001;
        }

        // Ensure the new ID is 5 digits
        return str_pad($newId, 5, '0', STR_PAD_LEFT);
    }
}


if(!function_exists("checkUserSecurity")){
    function checkUserSecurity($parameter){
        $apiData = [
            'user_id' => $parameter['user_id'],
            'email'   => $parameter['email'],
        ];
        
        if (isset($parameter['password'])) {
            $apiData['password'] = $parameter['password'];
        }
        $response = securityApi("check-user", $apiData);
        
        return $response;
}
}

if(!function_exists("countDiscussionType")){
    function countDiscussionType($type,$cateategoryId){
        $userId = auth()->user()->id;
        $query = DiscussionBoard::with(['user','category']);

        if($type == 'my'){
            $query->where('added_by',$userId);
        }else if($type == "connected"){
            $query->whereHas('comments', function ($q){ // Search in comments by the current user
                $q->where('added_by', auth()->user()->id);
            });
            $query->where(function($query) use ($userId) {
                $query->where('type', 'public')
                ->orWhere(function($query) use ($userId) {
                    $query->where('type', 'private')
                    ->whereHas('member', function($query) use ($userId) {
                        $query->where('member_id', $userId);
                    });
                })
                ->orWhere('allow_join_request', 1);
            });
        }else if($type == "pending"){
            $query->where(function($query) use ($userId) {
                $query->where(function($query) use ($userId) {
                    $query->where('type', 'private')
                    ->whereHas('member', function($query) use ($userId) {
                        $query->where('member_id', $userId);
                        $query->where('status','pending');
                    });
                });
            });
        }else if($type == "send_pending"){
            $query->where(function($query) use ($userId) {
                $query->where(function($query) use ($userId) {
                    $query->where('type', 'private')
                    ->whereHas('member', function($query) use ($userId) {
                        $query->where('member_id', $userId);
                        $query->where('status','pending');
                        $query->orWhere('added_by',$userId);
                    });
                });
            });
        }else if($type == "favourite"){
            $query->where('is_favourite',1);
        }
      
        $query->where(function($query) use ($userId) {
            $query->where('type', 'public')
            ->orWhere(function($query) use ($userId) {
                $query->where('type', 'private')
                ->whereHas('member', function($query) use ($userId) {
                    $query->where('member_id', $userId);
                });
            })
            ->orWhere('allow_join_request', 1);
        });
        if ($cateategoryId != null) {
            $query->where('category_id', $cateategoryId);  // Filter by category ID
        }
       $query->latest();

       return $discussionData=  $query->count();
    
    }
}
if(!function_exists("getDiscussionCountByCategory")){
    function getDiscussionCountByCategory($categoryUniqueId = null, $search = null)
    {
        $query = DiscussionBoard::with(['user', 'category']);
 
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('post', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('comments', function ($q) use ($search) {
                        $q->where('added_by', Auth::id())
                            ->where('comment', 'LIKE', "%{$search}%");
                    });
            });
        }
 
        if ($categoryUniqueId) {
            $category = DiscussionCategory::where('unique_id', $categoryUniqueId)->first();
            if ($category) {
                $query->where('category_id', $category->id);
                return $query->count();
            } else {
                return 0; // No such category
            }
        }
 
        return $query->count(); // Return total count if no category ID
    }
}

if (!function_exists('maskedUserName')) {
    function maskedUserName($uniqueId)
    {
        $user = User::where('unique_id', $uniqueId)->first();
 
        if (!$user) {
            return 'Unknown User';
        }
 
        $firstName = ucfirst($user->first_name);
        $lastInitial = strtoupper(substr($user->last_name, 0, 1)) . '***';
 
        return $firstName . ' ' . $lastInitial;
    }
}
if (!function_exists('countNotificationData')) {
    function countNotificationData($type)
    {
        if($type == "award_case"){
            return $count = ChatNotification::where('type','award_case')->where('user_id',auth()->user()->id)->where('is_read',0)->count();
        }else if($type == "accept_retain_agreement"){
            return $count = ChatNotification::where('type','accept_retain_agreement')->where('user_id',auth()->user()->id)->where('is_read',0)->count();
        }
       
    }
}

function supportBadge($points,$return = 'name'){
    $points = SupportBadge::where('points',"<=", $points)->orderBy("points","desc")->first();
    if($return == 'name'){
        return $points->badge_name??'';
    }elseif($return == 'image'){
        return ($points->badge_image??'') != ''?(otherFileDirUrl($points->badge_image,'t')):'';
    }else{
        return $points;
    }
}


function supportBadgePoints(){
    $totalMaxPoints = SupportBadge::max('points');
    $totalMinPoints = SupportBadge::min('points');

    $response['max_points'] = $totalMaxPoints;
    $response['min_points'] = $totalMinPoints;

    return $response;
}


function supportBadges(){
    $supportBadges = SupportBadge::orderBy('points','asc')->get();
    $badges = [];
    $points = '';
    foreach($supportBadges as $key => $value)
    {
        $badge_image = $value->badge_image;
        $badges []= ["name" => $value->badge_name, "min" => $key == 0 ? 1 :$points, "max" => $value->points, "color" => "gold", "badge_image"=>$badge_image,'weight'=>$value->weight];
        $points = $value->points;
    }
    return $badges;
}

function stripeIntent(){
    Stripe::setApiKey(apiKeys('STRIPE_SECRET'));
    $intent = \Stripe\SetupIntent::create([
        'usage' => 'off_session'  // Ensure that the user has a `stripe_id`
     ]);
     return $intent;
}

function isLocal(){
    $host = request()->getHost(); // Get the current host
    $isLocal = in_array($host, ['localhost', '127.0.0.1']);
    return $isLocal;
}

if(!function_exists("invoiceItemSubTotal")){
    function invoiceItemSubTotal($amount,$discount_type,$discount){
        if($discount_type == 'per'){
            $per =  ($amount * $discount) / 100;
            return $amount - $per;
        }else{
            return $amount - $discount;
        }
    }
}
if(!function_exists("invoiceTaxableAmount")){
    function invoiceTaxableAmount($amount,$tax){
        return $per =  ($amount * $tax) / 100;
    }
}

if(!function_exists("invoiceTax")){
    function invoiceTax($amount,$tax){
        $per =  ($amount * $tax) / 100;
        return $amount + $per;
    }
}


if(!function_exists("invoiceAdditionalDiscount")){
    function invoiceAdditionalDiscount($amount,$discount,$discount_type){
        if($discount_type == 'per'){
            $per =  ($amount * $discount) / 100;
            return $per;
        }else{
            return $discount;
        }
    }

    if(!function_exists("zipAndEncryptFolder")){
        function zipAndEncryptFolder($folderPath, $zipPath, $password){
        if (!is_dir($folderPath)) {
            return false;
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = glob("$folderPath/*");
            foreach ($files as $file) {
                if (is_file(filename: $file)) {
                    $relativeName = basename($file);
                    $zip->addFile($file, $relativeName);
                    $zip->setEncryptionName($relativeName, ZipArchive::EM_AES_256, $password);
                }
            }

            $zip->close();
            return true;
        }

        return false;
    }
    }

    if (!function_exists("awsFileDownloadFolder")) {

        function awsFileDownloadFolder($fileKey, $timestamp)
        {
            try {
                if ($fileKey != '') {
                    $s3 = new S3Client([
                        'region' => apiKeys('AWS_DEFAULT_REGION'),
                        'version' => 'latest',
                        'credentials' => [
                            'key'    => apiKeys('AWS_ACCESS_KEY_ID'),
                            'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
                        ],
                    ]);
            
                    $bucket = apiKeys('AWS_BUCKET');
            
                    // Get the object from S3
                    $result = $s3->getObject([
                        'Bucket' => $bucket,
                        'Key'    => $fileKey,
                    ]);
            
                    // Get file contents and filename
                    $fileContent = $result['Body']->getContents();
                    $filename = basename($fileKey);
            
                    // Define local save path
                    $localDir  = storage_path("app/public/aws-files/encrypted/{$timestamp}");
        
                    $localPath = $localDir . '/' . $filename;
            
                    // Make sure the directory exists
                    if (!File::exists($localDir)) {
                        File::makeDirectory($localDir, 0755, true);
                    }
    
                    // Save file locally
                    file_put_contents($localPath, $fileContent);
            
                    // Optional: return a success message or file path
                    
                     return  storage_path("app/public/aws-files/encrypted/" .$timestamp. $filename);
                     
                    
                } else {
                    return redirect()->back()->with("error", "File key is missing");
                }
            } catch (Exception $e) {
                // Handle any AWS or file system errors
                return redirect()->back()->with("error", 'Error while fetching file: ' . $e->getMessage());
            }
        }
    }


    if(!function_exists("sendEncryptionOtp")){
    function sendEncryptionOtp($email,$template,$type="send_otp") { 
       
        $otp = generateOtp();
        $expiry_time = Carbon::now()->addMinutes(2);
        $resend_attempt = 0;
        if($type != "resend_otp"){
            OtpVerify::where('email',$email)->delete();
        }else{
           
            $get_otp = OtpVerify::where("email",$email)->latest()->first();
            $resend_attempt = $get_otp->resend_attempt??0;
            
            if(!empty($get_otp)){
                $resend_attempt = $resend_attempt + 1;
            }
    
        }
        $object = OtpVerify::updateOrCreate(['email'=>$email],['otp'=>$otp,'otp_expiry_time'=>$expiry_time,'resend_attempt' => $resend_attempt]);
        $mailData = ['otp' => $otp, 'token' => $object->unique_id];
        $view = \View::make($template, $mailData);
        $message = $view->render();
        $parameter = [
            'to' => $email,
            'to_name' => '',
            'message' => $message,
            'subject' => 'Document OTP Verification',
            'view' => 'emails.encryption-otp-mail',
            'data' => $mailData,
        ];
        $mailRes = sendMail($parameter);
        return $object;
    }
    }

     if (!function_exists("storeLoginActivity")) {
    function storeLoginActivity($deviceInfo)
    {
        $userId = auth()->id();

        // Store in database
        UserLoginActivity::create([
            'user_id'      => $userId ?? '',
            'ip_address'   => $deviceInfo->ip ?? '',
            'log_info'     => json_encode($deviceInfo),
            'city'         => $deviceInfo->city ?? '',
            'state'        => $deviceInfo->region ?? '',
            'country'      => $deviceInfo->country ?? '',
            'device_type'  => $deviceInfo->device_type ?? '',
            'timezone'     => $deviceInfo->timezone ?? '',
            'device_name'  => $deviceInfo->device_name ?? '',
            'browser_name' => $deviceInfo->browser ?? '',
        ]);

        return response()->json(['message' => 'Login activity stored successfully']);
    }
}
    
    //     if(!function_exists("storeUserLocationAccessibility")){
    //   function storeUserLocationAccessibility($isSignup = false, $userId = null)
    // {
       
    //     $userId = $userId ?? auth()->id();
    //     $deviceInfo = detectUserLocation();
      
    //     UserLocationAccessibility::create([
    //         'user_id'     => $userId ?? '',
    //         'ip_address'  => $deviceInfo['ip'] ?? '',
    //         'log_info'    => json_encode($deviceInfo),
    //         'city'        => $deviceInfo['city'] ?? '',
    //         'state'       => $deviceInfo['region'] ?? '',
    //         'country'     => $deviceInfo['country'] ?? '',
    //         'device_type' => $deviceInfo['device_type'] ?? '',
    //         'timezone'    => $deviceInfo['timezone'] ?? '',
    //         'device_name'  => $deviceInfo['device_name'] ?? '',
    //         'browser_name'  => $deviceInfo['browser'] ?? '',
    //         'is_signup_location'=> $isSignup ? 1 : 0,
    //     ]);
    
    //     return response()->json(['message' => 'Login activity stored successfully']);
    // }
    // }  

       if(!function_exists("checkIfLocationMatches")){
     function checkIfLocationMatches($login, $userLocationAccess)
    {
 return $login->ip_address === $userLocationAccess->ip_address &&
           $login->city === $userLocationAccess->city &&
           $login->state === $userLocationAccess->state &&
           $login->country === $userLocationAccess->country &&
           $login->device_type === $userLocationAccess->device_type &&
           $login->device_name === $userLocationAccess->device_name &&
           $login->browser_name === $userLocationAccess->browser_name;
    }
}
   
     if(!function_exists("checkLocationMatches")){
     function checkLocationMatches($login, $userLocationAccess)
    {

 return $login->ip === $userLocationAccess->ip_address &&
           $login->city === $userLocationAccess->city &&
           $login->region === $userLocationAccess->state &&
           $login->country === $userLocationAccess->country &&
           $login->device_type === $userLocationAccess->device_type &&
           $login->device_name === $userLocationAccess->device_name &&
           $login->browser === $userLocationAccess->browser_name;
    }
}

}
 if(!function_exists("getDocumentFolders")){
     function getDocumentFolders($ids)
    {
        return DocumentsFolder::whereIn('id',$ids)->get();
    }
 }

  if(!function_exists("getForm")){
     function getForm($id)
    {
        return Forms::where('id',$id)->first();
    }
 }

if(!function_exists("getSetting")){
    function getSetting($key = ''){
        $site_setting = Settings::where('meta_key',$key)->first();
        if(!empty($site_setting)){
            if($key != ''){
                if(isset($site_setting->meta_value)){
                    return $site_setting->meta_value;
                }else{
                    return '';
                }
            }else{
                return $site_setting;
            }
        }else{
            return null;
        }
    }
}

function stripeCustomerExists($customer_id): bool
{
    try {
        \Stripe\Customer::retrieve($customer_id);
        return true;
    } catch (\Stripe\Exception\InvalidRequestException $e) {
        return false;
    }
}

if(!function_exists("totalProfessionalEarning")){
function totalProfessionalEarning($type, $id)
{
    $user = User::where('id',$id)->first();
    
    if($type == "appointment"){
        return UserEarningsHistory::where('earn_from', 'appointment_fees')
            ->whereHas('appointment', function ($q) use ($user) {
                $q->where('professional_id', $user->id)
                  ->where('payment_status', 'paid');
            })
            ->sum('user_earn_amount');
        // $record = AppointmentBooking::where('professional_id',$user->id)->where('payment_status','paid')->get()->pluck('id')->toArray();
        // return $records =  Invoice::with(['appointmentInvoice.client'])->whereIn('reference_id',$record)->where('invoice_type','appointment-booking')->get()->sum('total_amount');
    }else if($type == "case"){
         return UserEarningsHistory::where('earn_from', 'case_fees')
            ->whereHas('case', function ($q) use ($user) {
                $q->where('professional_id', $user->id)
                  ->where('payment_status', 'paid');
            })
            ->sum('user_earn_amount');
        // $record = CaseWithProfessionals::where('professional_id',$user->id)->where('payment_status','paid')->get()->pluck('id')->toArray();
        // return $records =  Invoice::with(['caseInvoice.client'])->whereIn('reference_id',$record)->where('invoice_type','professional-case')->get()->sum('total_amount');
  }else if($type == "general_invoice"){
       return UserEarningsHistory::where('earn_from', 'general_invoice_fees')
            ->whereHas('globalInvoice', function ($q) use ($user) {
                $q->where('added_by', $user->id)
                  ->where('payment_status', 'paid');
            })
            ->sum('user_earn_amount');
    }else if($type == "all"){
      
        // $record = AppointmentBooking::where('professional_id',$user->id)->where('payment_status','paid')->get()->pluck('id')->toArray();
        // $appointment =  Invoice::with(['appointmentInvoice.client'])->whereIn('reference_id',$record)->where('invoice_type','appointment-booking')->get()->sum('total_amount');

        // $case_record = CaseWithProfessionals::where('professional_id',$user->id)->where('payment_status','paid')->get()->pluck('id')->toArray();
        // $case =  Invoice::with(['caseInvoice.client'])->whereIn('reference_id',$case_record)->where('invoice_type','professional-case')->get()->sum('total_amount');

          $appointment = UserEarningsHistory::where('earn_from', 'appointment_fees')
            ->whereHas('appointment', function ($q) use ($user) {
                $q->where('professional_id', $user->id)
                  ->where('payment_status', 'paid');
            })
            ->sum('user_earn_amount');
           
        $case = UserEarningsHistory::where('earn_from', 'case_fees')
            ->whereHas('case', function ($q) use ($user) {
                $q->where('professional_id', $user->id)
                  ->where('payment_status', 'paid');
            })
            ->sum('user_earn_amount');

        $invoice = UserEarningsHistory::where('earn_from', 'general_invoice_fees')
            ->whereHas('globalInvoice', function ($q) use ($user) {
                $q->where('added_by', $user->id)
                  ->where('payment_status', 'paid');
            })
            ->sum('user_earn_amount');
             
        return $appointment + $case + $invoice;
    }
 
}
}

if(!function_exists("getProfileImage")){
    function getProfileImage($id){
        $user = User::where('unique_id',$id)->first();
        $dir = userDirUrl($user->profile_image);
        $name = strtoupper(substr($user->first_name, 0, 1)) .''.strtoupper(substr($user->last_name, 0, 1)) ;
        if ($user->profile_image != ''){
            return '<img id="showProfileImage" src="'.$dir.'" class="img-fluid cdsProfileimg" alt="Profile Image">';
        }else{
            $viewData['name'] = $user->first_name." ".$user->last_name;
            $viewData['role'] = $user->role;
            $viewData['size'] = 36;
            $avatar = view("components.user-avatar",$viewData)->render();
            return $avatar;
        }
   
    }
}
if (!function_exists("pageSubMenu")) {
    function pageSubMenu($parent_menu,$page_arr = [])
    {
        $menuItems = menuItems();
        $sub_menus = array();
        $parentTitle = '';
        foreach ($menuItems as $menu) {
            if ($menu['menu-name'] == $parent_menu) {
                $parentTitle = $menu['title'];
                if (isset($menu['submenu'])) {
                    $sub_menus = $menu['submenu'];
                }
            }
        }
        $viewData['parentTitle'] = $parentTitle;
        $viewData['sub_menus'] = $sub_menus;
        $viewData['page_arr'] = $page_arr;
        $view = view("components.page-horizontal-submenu", $viewData);
        $contents = $view->render();
        return $contents;
    }
}

if(!function_exists("checkSubservices")){
    function checkSubservices($service_id,$sub_service_type_id){
        $professional_service  = ProfessionalServices::where('unique_id',$service_id)->first();
        $services = ProfessionalSubServices::where('service_id',$professional_service->service_id)->where('sub_services_type_id',$sub_service_type_id)->where('professional_service_id',$professional_service->id)->where('user_id',auth()->user()->id)->first();
        if(!empty($services)){
            return $services->sub_services_type_id;
        }else{
            return 0;
        }
       
    }
}
if(!function_exists("getServiceDocument")){
    function getServiceDocument($document_id){
        
        $document = DocumentsFolder::whereIn('id',explode(',',$document_id))->get()->pluck('name')->toArray();
        
        if(!empty($document)){
            return implode(',',$document);
        }
    }
}
if(!function_exists("checkCaseWithProfessional")){
    function checkCaseWithProfessional($service_id,$sub_service_type_id,$user_id){
        
        $caseWithProfessionals = CaseWithProfessionals::where('professional_id',$user_id)->where('sub_service_id',$service_id)->where('service_type_id',$sub_service_type_id)->first();
        
        return $caseWithProfessionals;
    }
}

if(!function_exists("getPendingServiceConfiguration")){
    function getPendingServiceConfiguration($service_id){
        
        return $services = ProfessionalSubServices::where('service_id',$service_id)->where('user_id',auth()->user()->id)->where('status','pending')->count();
    }
}

function checkPrivacySettings($main_module, $module_action, $user_id,$page_url)
{
    
    $main_module_privacy = ModulePrivacy::where('slug', $main_module)->first();

    if (!empty($main_module_privacy)) {
        $module_privacy = ModulePrivacyOptions::with(['userPrivacys' => function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        }])->where('action_slug', $module_action)->first();
        // \Log::info($module_privacy);
        if (!empty($module_privacy)) {
            if ($module_privacy->userPrivacys) {
                $options = explode(',', $module_privacy->userPrivacys->privacy_option_value ?? '');
                return checkUserEligibleSettings($options,$user_id,auth()->user()->id);
                
            }else{
                return true;
            }
        }else{
            $userPrivacyLogs = new UserPrivacySettingsLogs;
            $userPrivacyLogs->module_privacy_name = $module_action;
            $userPrivacyLogs->page_url = $page_url;
            $userPrivacyLogs->added_by = auth()->user()->id;
            $userPrivacyLogs->save();
            return true;
            // return false;
        }

    }else{
        // return false;
        $userPrivacyLogs = new UserPrivacySettingsLogs;
        $userPrivacyLogs->module_privacy_name = $main_module;
        $userPrivacyLogs->page_url = $page_url;
        $userPrivacyLogs->added_by = auth()->user()->id;
        $userPrivacyLogs->save();
        return true;
    }

    

}

function checkUserEligibleSettings($options,$professional_id,$client_id)
{
    if (in_array('anyone', $options)) {
        return true;
    } elseif (in_array('connections', $options)) {

         $connection = UserConnection::where(function ($query) use ($client_id, $professional_id) {
                $query->where('user1_id', $client_id)
                    ->where('user2_id', $professional_id);
            })->orWhere(function ($query) use ($client_id, $professional_id) {
                $query->where('user1_id', $professional_id)
                    ->where('user2_id', $client_id);
            })->first(); 

            if (!empty($connection)) {
               
                return true;
            }else{
                
                return false;
            }
        // $connection = CaseWithProfessionals::where('professional_id',$professional_id)->where('client_id',$client_id)->first();
        // if (!empty($connection)) {
        //     return true;
        // }else{
        //     return false;
        // }
    } elseif(in_array('verified-users', $options)){
        $verified_user  = User::where('id',$client_id)->where('status','active')->first();
      
        if (!empty($verified_user)) {
            return true;
        }else{
            return false;
        }
    }else if(in_array('logged-in-user', $options)){
        if(auth()->check()){
            return true;
        }else{
            return false;
        }
    }else if(in_array('public', $options)){
        return true;
    }
    else if(in_array('enable', $options)){
        return true;
    }
    else if(in_array('disable', $options)){
        return false;
    }else if(in_array('me',$options))
    {   
        if($professional_id == auth()->user()->id){
            return true;
        } else{
            return false;
        }
    }else if(in_array('followers',$options)){
        $connected_user_list = FeedsConnection::where('user_id',$client_id)->where('connection_with',$professional_id)->where('connection_type','follow')->get();
      
        if($connected_user_list->isNotEmpty())
        {
            return true;
        }else{
            return false;
        }
    }else if(in_array('connections-of-connections',$options))
    {
        // Step 1: Get all users connected to $client_id
        $clientConnections = UserConnection::where('user1_id', $client_id)
            ->orWhere('user2_id', $client_id)
            ->get()
            ->flatMap(function ($conn) use ($client_id) {
                return $conn->user1_id == $client_id ? [$conn->user2_id] : [$conn->user1_id];
            })->toArray();

        // Step 2: Check if any of those are directly connected to $professional_id
        $secondDegreeConnection = UserConnection::where(function ($query) use ($professional_id) {
                $query->where('user1_id', $professional_id)
                    ->orWhere('user2_id', $professional_id);
            })
            ->where(function ($query) use ($clientConnections) {
                $query->whereIn('user1_id', $clientConnections)
                    ->orWhereIn('user2_id', $clientConnections);
            })
            ->first();
            $final = [];
            if(!empty($secondDegreeConnection)){
                $final = array_merge($clientConnections, [$secondDegreeConnection->user2_id]);
            }else{
                $final = $clientConnections;
            }
           
            if(!empty($final)){
                 return true;
            }else{
                return false;
            }
      

    }

    return false;
}



if(!function_exists('checkUserConnection'))
{
    function checkUserConnection($professional_id,$client_id,$connected_from)
    {
        $connection = UserConnection::where(function ($query) use ($professional_id,$client_id) {
                        $query->where('user1_id', $professional_id)
                            ->where('user2_id', $client_id);
                        })
                        ->orWhere(function ($query) use ($professional_id,$client_id) {
                            $query->where('user1_id', $client_id)
                            ->where('user2_id', $professional_id);
                        })->first();

        if(empty($connection)){
            $userConnection = new UserConnection;
            $userConnection->user1_id = $professional_id;
            $userConnection->user2_id = $client_id;
            $userConnection->connected_from = $connected_from;
            $userConnection->added_by = $client_id;
            $userConnection->save();
        }

    }
}


if(!function_exists('removeUserConnection'))
{
    function removeUserConnection($professional_id,$client_id)
    {
        $connection = UserConnection::where(function ($query) use ($professional_id,$client_id) {
                        $query->where('user1_id', $professional_id)
                            ->where('user2_id', $client_id);
                        })
                        ->orWhere(function ($query) use ($professional_id,$client_id) {
                            $query->where('user1_id', $client_id)
                            ->where('user2_id', $professional_id);
                        })->delete();

    }
}


if(!function_exists('checkUserPrivacy'))
{
    function checkUserPrivacy($user_id,$value)
    {
        if ($value == "connections") {
        //    $connection = UserConnection::where(function ($query) use ($user_id) {
        //         $query->where('user1_id', $user_id)
        //             ->orWhere('user2_id', $user_id);
        //     })->first(); 
        $id = auth()->user()->id;
         $connection = UserConnection::where(function ($query) use ($user_id,$id) {
                        $query->where('user1_id', $user_id)
                            ->where('user2_id', $id);
                        })
                        ->orWhere(function ($query) use ($user_id,$id) {
                            $query->where('user1_id', $id)
                            ->where('user2_id', $user_id);
                        })->first();


            if (!empty($connection)) {
               
                return true;
            }else{
                
                return false;
            }
        } else if($value == "logged-in-user"){
            if(auth()->check()){
                return true;
            }else{
                return false;
            }
        }else if($value == "public"){
            return true;
        }else if($value == "enable"){
            return true;
        }
        else if($value ==  "disable"){
            return false;
        }
        else if($value ==  "anyone"){
            return true;
        }
        else if($value ==  "yes"){
            return false;
        }else if($value == "no"){
            return true;
        }else if($value ==  "followers"){
            $connected_user_list = FeedsConnection::where('user_id',auth()->user()->id)->where('connection_with',$user_id)->where('connection_type','follow')->get();
      
            if($connected_user_list->isNotEmpty())
            {
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
}

if(!function_exists('commentEmojis'))
{
    function commentEmojis($key = ''){
        $emojis = [
            'thumbs_up' => [
                'emoji' => '👍',
                'name' => 'Thumbs up',
                'keyword' => 'thumbs_up',
            ],
            'glowing_star' => [
                'emoji' => '🌟',
                'name' => 'Glowing star',
                'keyword' => 'glowing_star',
            ],
            'hundred' => [
                'emoji' => '💯',
                'name' => '100',
                'keyword' => 'hundred',
            ],
            'fire' => [
                'emoji' => '🔥',
                'name' => 'Fire',
                'keyword' => 'fire',
            ],
            'raised_hands' => [
                'emoji' => '🙌',
                'name' => 'Raised hands',
                'keyword' => 'raised_hands',
            ],
            'clapping' => [
                'emoji' => '👏',
                'name' => 'Clapping',
                'keyword' => 'clapping',
            ],
            'target' => [
                'emoji' => '🎯',
                'name' => 'Target',
                'keyword' => 'target',
            ],
            'thinking_face' => [
                'emoji' => '🤔',
                'name' => 'Thinking face',
                'keyword' => 'thinking_face',
            ],
            'light_bulb' => [
                'emoji' => '💡',
                'name' => 'Light bulb',
                'keyword' => 'light_bulb',
            ],
            'smiling_face' => [
                'emoji' => '😊',
                'name' => 'Smiling face',
                'keyword' => 'smiling_face',
            ],
        ];
        if($key != ''){
            return $emojis[$key];
        }
        return $emojis;
    }
}

if (!function_exists('getSidebarStatus')) {
    function getSidebarStatus($userId = null) {
        if (!$userId && auth()->check()) {
            $userId = auth()->id();
        }
        if (!$userId) {
            return false; 
        }
        $record = UserUtility::where('user_id', $userId)->first();

        return $record?->status === 0; // true means collapsed
    }
}
   
if(!function_exists('checkPendingService')){
    function checkPendingService($service_id) {
        $count = ImmigrationServices::where('parent_service_id', $service_id)->count();
        if ($count == 0) {
            // No sub-services, nothing to select
            return false;
        }
        $user_count = ProfessionalServices::where('parent_service_id', $service_id)
            ->where('user_id', auth()->user()->id)
            ->count();

        // Pending if user has NOT selected all sub-services
        return $user_count < $count;
    }
}

if (!function_exists('handleUnauthorizedAccess')) {
    function handleUnauthorizedAccess($message = 'You are not authorized to perform this action.')
    {
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'status' => false,
                'message' => $message
            ], 403);
        }

        abort(403, $message);
    }
}

if (!function_exists('checkRecordOwnership')) {
    function checkRecordOwnership($record, $message = 'You are not authorized to perform this action.')
    {
        if (!$record) {
            return handleUnauthorizedAccess('Record not found.');
        }

        if (!$record->isEditableBy(auth()->id())) {
            return handleUnauthorizedAccess($message);
        }

        return true;
    }
}


// function generatePdfThumbnail(){
//     $maxWidth = 800;
//     $maxHeight = 800;
//     $imagick = new Imagick();
//     $imagick->setResolution(150, 150); // Set high resolution for better quality
//     $imagick->readImage("{$tempPdfPath}[0]"); // Read first page
//     $imagick->setImageFormat('jpg');
//     $imagick->setImageCompressionQuality(90); // Improve quality
//     $imagick->thumbnailImage($maxWidth, $maxHeight, true);
//     $imageBlob = $imagick->getImageBlob();
//     $base64 = base64_encode($imageBlob);
//     $imagick->clear();
//     $imagick->destroy();
//     $finalWidth = $imagick->getImageWidth();
//     $finalHeight = $imagick->getImageHeight();
// }

function checkSubscriptionFeature($featureKey) {
    $userId = auth()->id();
    
    // Get user's active subscription
    $subscription = \App\Models\UserSubscriptionHistory::where('user_id', $userId)
        ->where('subscription_type', 'membership')
        ->where('subscription_status', 'active')
        ->first();
        
    if (!$subscription) {
        return false; // No active subscription
    }
    
    // Check if plan allows this feature
    $configure = \App\Models\MembershipPlanFeatureValue::where('membership_plan_id', $subscription->membership_plans_plan_id)
        ->where('feature_key', $featureKey)
        ->first();
   
    if (!empty($configure)) {
        return $configure->value > 0; // Feature is available if value > 0
    }
    
    return false; // Feature not configured for this plan
}

if (!function_exists('getTicketCount')) {
    function getTicketCount($status,$type)
    {
        if($type == "status"){
            return Ticket::where('status',$status)->where('user_id',auth()->user()->id)->count();
        }else if($type == "priority"){
            return Ticket::where('priority',$status)->where('user_id',auth()->user()->id)->count();
        }else{
            return Ticket::where('category_id',$status)->where('user_id',auth()->user()->id)->count();
        }
       
    }
}

if (!function_exists('getCustomPopup')) {
    function getCustomPopup($viewData) {
        return view('components.custom-popup',$viewData)->render();
    }
}

if (! function_exists('str_limit')) {
    function str_limit($value, $limit = 100, $end = '...') {
        return Str::limit($value, $limit, $end);
    }
}

if (!function_exists('getUnreadCase')) {
    function getUnreadCase() {
        $userId = auth()->user()->id;
        return Cases::where('status','posted')->whereDoesntHave('ProfessionalCaseViewed', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->get()->count();
    }
}

if (!function_exists('getUnreadMessage')) {
    function getUnreadMessage() {
        $user_id = auth()->user()->id;
        $chat_message_read = ChatMessageRead::where("receiver_id",$user_id)->where("status","unread")->count();
        return $chat_message_read;
    }
}

function globalInvoiceUrl($unique_id)
{
    $invoice = Invoice::where('unique_id', $unique_id)->first();
    $invoiceId = $invoice->id;
    $matching = PaymentLinkParameter::get()->first(function ($record) use ($invoiceId) {
        return Crypt::decrypt($record->invoice_id) == $invoiceId;
    });

    if (!empty($matching)) {

        $url = clientTrustvisoryUrl().'/panel/payment-transaction/' . $matching->invoice_id . '/' . urlencode($matching->transaction_id) .
            '?utk=' . urlencode($matching->token) .
            '&uid=' . urlencode($matching->user_id) .
            '&signature=' . urlencode($matching->signature);

    } else {

        $url = clientTrustvisoryUrl().'/panel/payment-transaction/' . Crypt::encrypt($invoiceId) . '/' . urlencode(Crypt::encrypt($invoice->transaction_id)) .
            '?utk=' . Crypt::encrypt(0) .
            '&uid=' . Crypt::encrypt(0) .
            '&signature=' . Crypt::encrypt(0);

    }

    return $url;

}
require __DIR__."/DirectoryHelper.php";
require __DIR__."/CaseHelper.php";
require __DIR__."/ApiHelper.php";
require __DIR__."/AppointmentHelper.php";
require __DIR__."/FeedHelper.php";
require __DIR__."/DiscussionHelper.php";

?>