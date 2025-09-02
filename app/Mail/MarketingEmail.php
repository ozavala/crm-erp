<?php

namespace App\Mail;

use App\Models\MarketingCampaign;
use App\Models\CampaignRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MarketingEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $campaign;
    public $recipient;
    public $trackingUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(MarketingCampaign $campaign, CampaignRecipient $recipient)
    {
        $this->campaign = $campaign;
        $this->recipient = $recipient;
        $this->trackingUrl = route('email.track', [
            'campaign' => $campaign->id,
            'recipient' => $recipient->id,
            'token' => $this->generateTrackingToken($campaign, $recipient)
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->subject,
            from: config('mail.from.address'),
            replyTo: config('mail.from.address'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $content = $this->campaign->content;
        
        // Reemplazar variables en el contenido
        $content = $this->replaceVariables($content);
        
        // Agregar tracking pixel
        $htmlContent = $this->addTrackingPixel($content);

        return new Content(
            html: $htmlContent,
            text: $content,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Replace variables in content
     */
    private function replaceVariables(string $content): string
    {
        $variables = [
            '{{recipient_name}}' => $this->recipient->name ?? 'Valued Customer',
            '{{recipient_email}}' => $this->recipient->email,
            '{{campaign_name}}' => $this->campaign->name,
            '{{company_name}}' => config('app.name'),
            '{{unsubscribe_url}}' => route('email.unsubscribe', [
                'campaign' => $this->campaign->id,
                'recipient' => $this->recipient->id,
                'token' => $this->generateUnsubscribeToken($this->campaign, $this->recipient)
            ]),
            '{{tracking_url}}' => $this->trackingUrl,
        ];

        return str_replace(array_keys($variables), array_values($variables), $content);
    }

    /**
     * Add tracking pixel to HTML content
     */
    private function addTrackingPixel(string $content): string
    {
        $trackingPixel = '<img src="' . $this->trackingUrl . '" width="1" height="1" style="display:none;" />';
        
        // Insertar antes del cierre del body
        if (strpos($content, '</body>') !== false) {
            return str_replace('</body>', $trackingPixel . '</body>', $content);
        }
        
        return $content . $trackingPixel;
    }

    /**
     * Generate tracking token
     */
    private function generateTrackingToken(MarketingCampaign $campaign, CampaignRecipient $recipient): string
    {
        return hash('sha256', $campaign->id . $recipient->id . config('app.key'));
    }

    /**
     * Generate unsubscribe token
     */
    private function generateUnsubscribeToken(MarketingCampaign $campaign, CampaignRecipient $recipient): string
    {
        return hash('sha256', 'unsubscribe_' . $campaign->id . $recipient->id . config('app.key'));
    }
}
