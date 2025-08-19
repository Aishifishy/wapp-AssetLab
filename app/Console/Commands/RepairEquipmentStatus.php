<?php

namespace App\Console\Commands;

use App\Models\Equipment;
use App\Models\EquipmentRequest;
use Illuminate\Console\Command;

class RepairEquipmentStatus extends Command
{
    protected $signature = 'equipment:repair-status';
    protected $description = 'Repair equipment status inconsistencies';

    public function handle()
    {
        $this->info('Checking for equipment status inconsistencies...');

        // Find equipment marked as borrowed but no active checkouts
        $problematicBorrowed = Equipment::where('status', Equipment::STATUS_BORROWED)
            ->whereDoesntHave('borrowRequests', function($query) {
                $query->where('status', 'approved')
                      ->whereNotNull('checked_out_at')
                      ->whereNull('returned_at');
            })
            ->get();

        $this->info("Found {$problematicBorrowed->count()} equipment marked as borrowed with no active checkouts");

        foreach ($problematicBorrowed as $equipment) {
            $this->line("Fixing: {$equipment->name} (ID: {$equipment->id})");
            $equipment->update([
                'status' => Equipment::STATUS_AVAILABLE,
                'current_borrower_id' => null,
            ]);
        }

        // Find equipment marked as available but has active checkouts
        $problematicAvailable = Equipment::where('status', Equipment::STATUS_AVAILABLE)
            ->whereHas('borrowRequests', function($query) {
                $query->where('status', 'approved')
                      ->whereNotNull('checked_out_at')
                      ->whereNull('returned_at');
            })
            ->get();

        $this->info("Found {$problematicAvailable->count()} equipment marked as available with active checkouts");

        foreach ($problematicAvailable as $equipment) {
            $activeRequest = $equipment->borrowRequests()
                ->where('status', 'approved')
                ->whereNotNull('checked_out_at')
                ->whereNull('returned_at')
                ->first();

            if ($activeRequest) {
                $this->line("Fixing: {$equipment->name} (ID: {$equipment->id})");
                $equipment->update([
                    'status' => Equipment::STATUS_BORROWED,
                    'current_borrower_id' => $activeRequest->user_id,
                ]);
            }
        }

        $this->info('Equipment status repair completed!');
    }
}
