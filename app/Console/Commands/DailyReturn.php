<?php

namespace App\Console\Commands;

use App\Models\SubscribedPlan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class DailyReturn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-return';

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
        try {
            $subscribedPlanModel =  SubscribedPlan::findActive()->whereColumn('roi_count', '!=', 'roi_complete_count')->get();
            foreach ($subscribedPlanModel as $subscribedPlan) {
                DB::beginTransaction();

                if ($subscribedPlan->roi_complete_count <= $subscribedPlan->roi_count) {
                    $subscribedPlan->roi_complete_count += 1;
                    $userIncome = $subscribedPlan->roi_count / $subscribedPlan->subscriptionPlan->price;
                    $subscribedPlan->save();


                    $adminWallet = Wallet::where('created_by_id', 1)->first();
                    $adminWallet->balance -= $userIncome;
                    $adminWallet->save();

                    WalletTransaction::add($userIncome, $adminWallet->id, $subscribedPlan->createdBy->name . ' in TRANSACTION ROI income generator. And debit your account ( Plan:-' . $subscribedPlan->subscriptionPlan->title . ', Type:-' . $subscribedPlan->subscriptionPlan->getDurationType() . ')', WalletTransaction::TRANSACTION_ROI);

                    $userWallet = Wallet::where('created_by_id', $subscribedPlan->createdBy->id)->first();
                    $userWallet->balance += $userIncome;
                    $userWallet->save();

                    WalletTransaction::add($userIncome, $userWallet->id, $subscribedPlan->createdBy->name . ' in TRANSACTION ROI income generator. And credit your account ( Plan:-' . $subscribedPlan->subscriptionPlan->title . ', Type:-' . $subscribedPlan->subscriptionPlan->getDurationType() . ')', WalletTransaction::TRANSACTION_ROI, WalletTransaction::TYPE_CREDIT);
                }
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
