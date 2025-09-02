<?php

namespace App\Http\Controllers;

use App\Models\MarketingCampaign;
use App\Models\CampaignRecipient;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class EmailServiceController extends Controller
{
    /**
     * Track email open
     */
    public function track(Request $request, MarketingCampaign $campaign, CampaignRecipient $recipient): Response
    {
        $token = $request->get('token');
        $expectedToken = hash('sha256', $campaign->id . $recipient->id . config('app.key'));

        if ($token !== $expectedToken) {
            return response('Unauthorized', 401);
        }

        // Actualizar estado del destinatario
        if ($recipient->status === 'sent') {
            $recipient->update([
                'status' => 'opened',
                'opened_at' => now()
            ]);

            // Actualizar contadores de la campaña
            $campaign->increment('opened_count');

            // Registrar en logs
            EmailLog::create([
                'campaign_id' => $campaign->id,
                'recipient_id' => $recipient->id,
                'email' => $recipient->email,
                'subject' => $campaign->subject,
                'type' => 'campaign',
                'status' => 'opened',
                'opened_at' => now(),
            ]);
        }

        // Devolver un pixel transparente
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        return response($pixel, 200, ['Content-Type' => 'image/gif']);
    }

    /**
     * Track email click
     */
    public function trackClick(Request $request, MarketingCampaign $campaign, CampaignRecipient $recipient): Response
    {
        $token = $request->get('token');
        $expectedToken = hash('sha256', $campaign->id . $recipient->id . config('app.key'));

        if ($token !== $expectedToken) {
            return response('Unauthorized', 401);
        }

        // Actualizar estado del destinatario
        if (in_array($recipient->status, ['sent', 'opened'])) {
            $recipient->update([
                'status' => 'clicked',
                'clicked_at' => now()
            ]);

            // Actualizar contadores de la campaña
            $campaign->increment('clicked_count');

            // Registrar en logs
            EmailLog::create([
                'campaign_id' => $campaign->id,
                'recipient_id' => $recipient->id,
                'email' => $recipient->email,
                'subject' => $campaign->subject,
                'type' => 'campaign',
                'status' => 'clicked',
                'clicked_at' => now(),
            ]);
        }

        // Redirigir a la URL original
        $url = $request->get('url', '/');
        return redirect($url);
    }

    /**
     * Unsubscribe from emails
     */
    public function unsubscribe(Request $request, MarketingCampaign $campaign, CampaignRecipient $recipient): Response
    {
        $token = $request->get('token');
        $expectedToken = hash('sha256', 'unsubscribe_' . $campaign->id . $recipient->id . config('app.key'));

        if ($token !== $expectedToken) {
            return response('Unauthorized', 401);
        }

        // Actualizar estado del destinatario
        $recipient->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now()
        ]);

        // Actualizar contadores de la campaña
        $campaign->increment('unsubscribed_count');

        // Registrar en logs
        EmailLog::create([
            'campaign_id' => $campaign->id,
            'recipient_id' => $recipient->id,
            'email' => $recipient->email,
            'subject' => $campaign->subject,
            'type' => 'campaign',
            'status' => 'unsubscribed',
            'metadata' => ['unsubscribed_at' => now()],
        ]);

        return response('You have been successfully unsubscribed from our emails.', 200);
    }

    /**
     * Bounce handler
     */
    public function bounce(Request $request): Response
    {
        $data = $request->all();
        
        // Procesar bounce según el proveedor de email
        $email = $data['email'] ?? null;
        $campaignId = $data['campaign_id'] ?? null;
        $recipientId = $data['recipient_id'] ?? null;
        $bounceType = $data['bounce_type'] ?? 'hard';
        $reason = $data['reason'] ?? 'Unknown';

        if ($email && $campaignId && $recipientId) {
            $recipient = CampaignRecipient::where('campaign_id', $campaignId)
                ->where('id', $recipientId)
                ->where('email', $email)
                ->first();

            if ($recipient) {
                $recipient->update([
                    'status' => 'bounced',
                    'bounced_at' => now(),
                    'error_message' => $reason
                ]);

                // Actualizar contadores de la campaña
                $campaign = MarketingCampaign::find($campaignId);
                if ($campaign) {
                    $campaign->increment('bounced_count');
                }

                // Registrar en logs
                EmailLog::create([
                    'campaign_id' => $campaignId,
                    'recipient_id' => $recipientId,
                    'email' => $email,
                    'type' => 'campaign',
                    'status' => 'bounced',
                    'bounced_at' => now(),
                    'error_message' => $reason,
                    'metadata' => ['bounce_type' => $bounceType],
                ]);
            }
        }

        return response('OK', 200);
    }

    /**
     * Webhook para proveedores de email
     */
    public function webhook(Request $request): Response
    {
        $data = $request->all();
        $provider = $request->header('X-Provider', 'unknown');

        Log::info('Email webhook received', [
            'provider' => $provider,
            'data' => $data
        ]);

        // Procesar según el proveedor
        switch ($provider) {
            case 'mailgun':
                return $this->handleMailgunWebhook($data);
            case 'sendgrid':
                return $this->handleSendgridWebhook($data);
            case 'ses':
                return $this->handleSESWebhook($data);
            default:
                return response('Provider not supported', 400);
        }
    }

    /**
     * Handle Mailgun webhook
     */
    private function handleMailgunWebhook(array $data): Response
    {
        $event = $data['event-data']['event'] ?? null;
        $email = $data['event-data']['recipient'] ?? null;
        $campaignId = $data['event-data']['campaign-id'] ?? null;

        if ($event && $email) {
            $this->processEmailEvent($event, $email, $campaignId, $data);
        }

        return response('OK', 200);
    }

    /**
     * Handle SendGrid webhook
     */
    private function handleSendgridWebhook(array $data): Response
    {
        foreach ($data as $event) {
            $eventType = $event['event'] ?? null;
            $email = $event['email'] ?? null;
            $campaignId = $event['campaign_id'] ?? null;

            if ($eventType && $email) {
                $this->processEmailEvent($eventType, $email, $campaignId, $event);
            }
        }

        return response('OK', 200);
    }

    /**
     * Handle SES webhook
     */
    private function handleSESWebhook(array $data): Response
    {
        $event = $data['eventType'] ?? null;
        $email = $data['bounce']['bouncedRecipients'][0]['emailAddress'] ?? 
                 $data['complaint']['complainedRecipients'][0]['emailAddress'] ?? null;
        $campaignId = $data['campaign-id'] ?? null;

        if ($event && $email) {
            $this->processEmailEvent($event, $email, $campaignId, $data);
        }

        return response('OK', 200);
    }

    /**
     * Process email event
     */
    private function processEmailEvent(string $event, string $email, ?string $campaignId, array $data): void
    {
        $recipient = CampaignRecipient::where('email', $email);
        
        if ($campaignId) {
            $recipient = $recipient->where('campaign_id', $campaignId);
        }

        $recipient = $recipient->first();

        if (!$recipient) {
            return;
        }

        switch ($event) {
            case 'delivered':
                $recipient->update(['status' => 'delivered']);
                break;
            case 'opened':
                $recipient->update([
                    'status' => 'opened',
                    'opened_at' => now()
                ]);
                $recipient->campaign->increment('opened_count');
                break;
            case 'clicked':
                $recipient->update([
                    'status' => 'clicked',
                    'clicked_at' => now()
                ]);
                $recipient->campaign->increment('clicked_count');
                break;
            case 'bounced':
            case 'bounce':
                $recipient->update([
                    'status' => 'bounced',
                    'bounced_at' => now(),
                    'error_message' => $data['reason'] ?? 'Bounced'
                ]);
                $recipient->campaign->increment('bounced_count');
                break;
            case 'unsubscribed':
                $recipient->update([
                    'status' => 'unsubscribed',
                    'unsubscribed_at' => now()
                ]);
                $recipient->campaign->increment('unsubscribed_count');
                break;
        }

        // Registrar en logs
        EmailLog::create([
            'campaign_id' => $recipient->campaign_id,
            'recipient_id' => $recipient->id,
            'email' => $email,
            'type' => 'campaign',
            'status' => $event,
            'metadata' => $data,
        ]);
    }
}
