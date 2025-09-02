<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Notifications\AppointmentReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for upcoming appointments';

    /**
     * The reminder intervals in minutes.
     *
     * @var array
     */
    protected $reminderIntervals = [
        1440, // 24 hours before
        60,   // 1 hour before
        15,   // 15 minutes before
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Sending appointment reminders...');
        
        $count = 0;
        
        foreach ($this->reminderIntervals as $minutes) {
            $count += $this->sendRemindersForInterval($minutes);
        }
        
        $this->info("Sent {$count} appointment reminders.");
        
        return 0;
    }
    
    /**
     * Send reminders for a specific interval.
     *
     * @param  int  $minutes
     * @return int
     */
    protected function sendRemindersForInterval($minutes)
    {
        $now = Carbon::now();
        $future = Carbon::now()->addMinutes($minutes);
        
        // Find appointments that start within the next interval window (with 1 minute buffer)
        $appointments = Appointment::where('status', 'scheduled')
            ->where('start_date', '>', $now)
            ->where('start_date', '<=', $future->copy()->addMinutes(1))
            ->whereDoesntHave('reminders', function ($query) use ($minutes) {
                $query->where('minutes_before', $minutes);
            })
            ->with(['participants.participantable', 'createdBy'])
            ->get();
        
        $count = 0;
        
        foreach ($appointments as $appointment) {
            // Send to the appointment creator
            if ($appointment->createdBy) {
                $appointment->createdBy->notify(new AppointmentReminder($appointment, $minutes));
                $count++;
            }
            
            // Send to all participants
            foreach ($appointment->participants as $participant) {
                if ($participant->participantable && method_exists($participant->participantable, 'notify')) {
                    $participant->participantable->notify(new AppointmentReminder($appointment, $minutes));
                    $count++;
                }
            }
            
            // Record that we've sent a reminder for this appointment at this interval
            $appointment->reminders()->create([
                'minutes_before' => $minutes,
                'sent_at' => now(),
            ]);
        }
        
        return $count;
    }
}