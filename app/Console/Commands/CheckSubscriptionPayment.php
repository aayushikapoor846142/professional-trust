<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscriptionHistory;
use Stripe\StripeClient;
use Stripe\Stripe;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class CheckSubscriptionPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-subscription-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
  {
        $stripe = new StripeClient(apiKeys('STRIPE_SECRET'));
        $currentTimestamp = Carbon::now()->timestamp;
        // Fetch all active subscriptions
        $subscriptions = UserSubscriptionHistory::where('subscription_status', 'active')->get();

        foreach ($subscriptions as $subscription) {
            try {
                $upcomingInvoice = $stripe->invoices->upcoming([
                    'customer' => $subscription->user->stripe_id,
                ]);
                // Log::info( $upcomingInvoice->period_end);
                
                // exit();
                if ($currentTimestamp > $upcomingInvoice->period_end) {
                    Log::info("Timestamp {$upcomingInvoice->period_end} has passed today's timestamp {$currentTimestamp}.");
                    // Proceed to check subscription status
                    if ($upcomingInvoice->status !== 'paid') {
                        // Mark subscription as inactive
                        $subscription->subscription_status = 'un-paid';
                        $subscription->save();
                        Log::info("Subscription ID {$subscription->id} marked as inactive.");
                            // $this->info("Subscription ID {$subscription->id} marked as inactive.");
                    }
                } else {
                    Log::info("Timestamp {$upcomingInvoice->period_end} has not yet passed. No action taken.");
                    exit();
                }
            } catch (\Exception $e) {
                
                $this->error("Error for Subscription ID {$subscription->id}: " . $e->getMessage());
            }
        }

        return 0;
    }
}
