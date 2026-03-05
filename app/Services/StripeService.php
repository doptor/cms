<?php

namespace App\Services;

use App\Models\MarketplaceItem;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeService
{
    private string $secretKey;
    private string $webhookSecret;

    public function __construct()
    {
        $this->secretKey     = config('services.stripe.secret');
        $this->webhookSecret = config('services.stripe.webhook_secret');
    }

    /**
     * Create a Stripe Checkout Session for marketplace purchase.
     * Buyer pays → Stripe splits → 70% to seller, 30% platform.
     */
    public function createCheckoutSession(MarketplaceItem $item, User $buyer): array
    {
        if (empty($this->secretKey)) {
            return ['error' => 'Stripe not configured. Add STRIPE_SECRET to .env.'];
        }

        $seller = $item->author;

        // Ensure seller has a connected Stripe account
        if (!$seller->stripe_account_id) {
            return ['error' => 'Seller payment account not set up yet.'];
        }

        $priceInCents     = (int) ($item->price * 100);
        $platformFeeCents = (int) ($priceInCents * 0.30); // 30% platform

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ])->asForm()->post('https://api.stripe.com/v1/checkout/sessions', [
                'payment_method_types[]'        => 'card',
                'line_items[0][price_data][currency]'                       => 'usd',
                'line_items[0][price_data][unit_amount]'                    => $priceInCents,
                'line_items[0][price_data][product_data][name]'             => $item->name,
                'line_items[0][price_data][product_data][description]'      => $item->short_description,
                'line_items[0][quantity]'                                   => 1,
                'mode'                          => 'payment',
                'success_url'                   => route('marketplace.purchase.success', ['item' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'                    => route('marketplace.show', $item),
                'payment_intent_data[application_fee_amount]'               => $platformFeeCents,
                'payment_intent_data[transfer_data][destination]'           => $seller->stripe_account_id,
                'metadata[item_id]'             => $item->id,
                'metadata[buyer_id]'            => $buyer->id,
                'metadata[seller_id]'           => $seller->id,
            ]);

            if ($response->failed()) {
                Log::error('Stripe session error: ' . $response->body());
                return ['error' => 'Payment session creation failed.'];
            }

            $session = $response->json();
            return ['url' => $session['url'], 'session_id' => $session['id']];

        } catch (\Exception $e) {
            Log::error('Stripe exception: ' . $e->getMessage());
            return ['error' => 'Payment service unavailable.'];
        }
    }

    /**
     * Create Stripe Connect Onboarding link for sellers.
     * Sellers must connect their bank account to receive payouts.
     */
    public function createSellerOnboarding(User $seller): array
    {
        try {
            // Create Connect account if not exists
            if (!$seller->stripe_account_id) {
                $account = Http::withHeaders(['Authorization' => 'Bearer ' . $this->secretKey])
                    ->asForm()->post('https://api.stripe.com/v1/accounts', [
                        'type'                    => 'express',
                        'email'                   => $seller->email,
                        'capabilities[transfers][requested]'   => 'true',
                        'capabilities[card_payments][requested]' => 'true',
                    ])->json();

                $seller->update(['stripe_account_id' => $account['id']]);
            }

            // Create onboarding link
            $link = Http::withHeaders(['Authorization' => 'Bearer ' . $this->secretKey])
                ->asForm()->post('https://api.stripe.com/v1/account_links', [
                    'account'     => $seller->stripe_account_id,
                    'refresh_url' => route('settings.stripe.onboard'),
                    'return_url'  => route('settings.index'),
                    'type'        => 'account_onboarding',
                ])->json();

            return ['url' => $link['url']];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Handle Stripe webhook events.
     */
    public function handleWebhook(string $payload, string $signature): bool
    {
        // Verify webhook signature
        if (!$this->verifySignature($payload, $signature)) {
            Log::warning('Stripe webhook signature mismatch');
            return false;
        }

        $event = json_decode($payload, true);

        match ($event['type'] ?? '') {
            'checkout.session.completed' => $this->handlePurchaseSuccess($event['data']['object']),
            'account.updated'            => $this->handleSellerAccountUpdate($event['data']['object']),
            default                      => null,
        };

        return true;
    }

    private function handlePurchaseSuccess(array $session): void
    {
        $itemId  = $session['metadata']['item_id'] ?? null;
        $buyerId = $session['metadata']['buyer_id'] ?? null;

        if (!$itemId || !$buyerId) return;

        $item  = MarketplaceItem::find($itemId);
        $buyer = User::find($buyerId);

        if (!$item || !$buyer) return;

        // Record purchase
        \App\Models\MarketplacePurchase::create([
            'user_id'             => $buyer->id,
            'marketplace_item_id' => $item->id,
            'amount'              => $session['amount_total'] / 100,
            'stripe_session_id'   => $session['id'],
        ]);

        $item->increment('download_count');

        // Send confirmation email
        \Mail::to($buyer->email)->send(new \App\Mail\PurchaseConfirmation($buyer, $item));

        Log::info("Purchase complete: User #{$buyer->id} bought Item #{$item->id}");
    }

    private function handleSellerAccountUpdate(array $account): void
    {
        $seller = User::where('stripe_account_id', $account['id'])->first();
        if ($seller && $account['payouts_enabled']) {
            $seller->update(['stripe_payouts_enabled' => true]);
        }
    }

    private function verifySignature(string $payload, string $signature): bool
    {
        if (empty($this->webhookSecret)) return true; // Skip in dev

        $parts    = explode(',', $signature);
        $timestamp = str_replace('t=', '', $parts[0] ?? '');
        $sigHash  = str_replace('v1=', '', $parts[1] ?? '');

        $expectedSig = hash_hmac('sha256', "{$timestamp}.{$payload}", $this->webhookSecret);
        return hash_equals($expectedSig, $sigHash);
    }
}
