<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EquipmentRequest;
use App\Mail\EquipmentReturnReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendEquipmentReturnReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'equipment:send-return-reminders {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send return reminders for equipment that is due or overdue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $today = Carbon::today();
        
        // Get equipment that is due today or overdue (borrowed status, not returned)
        $overdueRequests = EquipmentRequest::where('status', 'borrowed')
            ->whereNull('returned_at')
            ->whereNotNull('checked_out_at')
            ->where('requested_until', '<', $today)
            ->with(['user', 'equipment'])
            ->get();
            
        // Get equipment due today (reminder)
        $dueTodayRequests = EquipmentRequest::where('status', 'borrowed')
            ->whereNull('returned_at')
            ->whereNotNull('checked_out_at')
            ->whereDate('requested_until', $today)
            ->with(['user', 'equipment'])
            ->get();

        $totalSent = 0;

        // Send overdue notifications
        foreach ($overdueRequests as $request) {
            $daysOverdue = $today->diffInDays(Carbon::parse($request->requested_until));
            
            if ($dryRun) {
                $this->info("Would send OVERDUE reminder for Equipment Request #{$request->id} to {$request->user->email} ({$daysOverdue} days overdue)");
            } else {
                Mail::to($request->user->email)->send(new EquipmentReturnReminder($request, $daysOverdue));
                $totalSent++;
            }
        }

        // Send due today notifications
        foreach ($dueTodayRequests as $request) {
            if ($dryRun) {
                $this->info("Would send DUE TODAY reminder for Equipment Request #{$request->id} to {$request->user->email}");
            } else {
                Mail::to($request->user->email)->send(new EquipmentReturnReminder($request, 0));
                $totalSent++;
            }
        }

        if ($dryRun) {
            $this->info("Dry run complete. Would have sent " . ($overdueRequests->count() + $dueTodayRequests->count()) . " reminder emails.");
        } else {
            $this->info("Sent {$totalSent} equipment return reminder emails.");
            $this->info("Overdue requests: {$overdueRequests->count()}");
            $this->info("Due today requests: {$dueTodayRequests->count()}");
        }

        return Command::SUCCESS;
    }
}
