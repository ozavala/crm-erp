<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use App\Models\MarketingCampaign;
use App\Models\CampaignRecipient;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;

class MarketingDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear plantilla de email
        $template = EmailTemplate::create([
            'name' => 'Welcome Offer',
            'subject' => '¡Bienvenido a nuestra comunidad, {{recipient_name}}!',
            'content' => '<h1>Hola {{recipient_name}}</h1><p>Gracias por unirte a {{company_name}}. Aprovecha tu oferta exclusiva: <b>20% de descuento</b> en tu primera compra.</p><p><a href="https://erp-demo.com">Ir a la tienda</a></p><p><a href="{{unsubscribe_url}}">Darse de baja</a></p>',
            'html_content' => null,
            'type' => 'promotional',
            'variables' => json_encode(['recipient_name', 'company_name', 'unsubscribe_url']),
            'settings' => null,
            'is_active' => true,
            'created_by' => User::first()->id ?? 1,
        ]);

        // Crear campaña de marketing
        $campaign = MarketingCampaign::create([
            'name' => 'Campaña de Bienvenida',
            'description' => 'Campaña automática para nuevos clientes y leads.',
            'subject' => $template->subject,
            'content' => $template->content,
            'status' => 'draft',
            'type' => 'promotional',
            'email_template_id' => $template->id,
            'created_by' => User::first()->id ?? 1,
            'scheduled_at' => null,
            'sent_at' => null,
            'total_recipients' => 0,
            'sent_count' => 0,
            'opened_count' => 0,
            'clicked_count' => 0,
            'bounced_count' => 0,
            'unsubscribed_count' => 0,
            'target_audience' => json_encode(['customers' => true, 'leads' => true]),
            'settings' => null,
        ]);

        // Seleccionar clientes y leads existentes
        $customers = Customer::inRandomOrder()->limit(5)->get();
        $leads = Lead::inRandomOrder()->limit(5)->get();

        // Crear destinatarios para la campaña
        foreach ($customers as $customer) {
            CampaignRecipient::create([
                'campaign_id' => $campaign->id,
                'customer_id' => $customer->id,
                'lead_id' => null,
                'email' => $customer->email,
                'name' => $customer->name,
                'status' => 'pending',
            ]);
        }
        foreach ($leads as $lead) {
            CampaignRecipient::create([
                'campaign_id' => $campaign->id,
                'customer_id' => null,
                'lead_id' => $lead->id,
                'email' => $lead->email,
                'name' => $lead->name,
                'status' => 'pending',
            ]);
        }

        // Actualizar total de destinatarios
        $campaign->update(['total_recipients' => $campaign->recipients()->count()]);
    }
}
