<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use App\Models\Listing;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AiService
{
    /**
     * Génère une description professionnelle pour une annonce.
     */
    public function generateDescription(array $data): string
    {
        $prompt = $this->buildDescriptionPrompt($data);

        // En production (Railway + config:cache), il faut lire la clé depuis la configuration
        // et non directement via env() dans un service Laravel.
        $apiKey = (string) config('services.openai.api_key', '');
        if (blank($apiKey) || str_contains($apiKey, 'your_openai_api_key')) {
            Log::warning('OpenAI API key missing or invalid in services.openai.api_key');
            return $this->buildFallbackDescription($data);
        }

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'max_tokens' => 600,
                'temperature' => 0.7,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un expert immobilier marocain. Tu rédiges des descriptions d\'annonces immobilières professionnelles, séduisantes et précises en français. Ton style est élégant, informatif et donne envie de visiter le bien.',
                    ],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            Log::error('OpenAI generateDescription error: ' . $e->getMessage());
            // Fallback : générer une description générique à partir des données
            return $this->buildFallbackDescription($data);
        }
    }

    /**
     * Analyse le prix d'une annonce par rapport au marché.
     */
    public function analyzePricing(Listing $listing): array
    {
        $cacheKey = "ai_pricing_{$listing->id}";

        return Cache::remember($cacheKey, 3600, function () use ($listing) {
            $marketData = $this->getMarketContext($listing);

            try {
                $response = OpenAI::chat()->create([
                    'model' => 'gpt-4o-mini',
                    'max_tokens' => 400,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un expert en évaluation immobilière au Maroc. Réponds uniquement en JSON valide.',
                        ],
                        [
                            'role' => 'user',
                            'content' => "Analyse ce bien immobilier marocain et donne une évaluation du prix en MAD:\n\n" .
                                "Type: {$listing->property_label}, Transaction: {$listing->transaction_label}\n" .
                                "Ville: {$listing->city}" . ($listing->zone ? ", Quartier: {$listing->zone}" : '') . "\n" .
                                "Surface: {$listing->surface} m², Pièces: {$listing->rooms}\n" .
                                "Prix demandé: {$listing->formatted_price}\n" .
                                "Équipements: " . $this->formatAmenities($listing) . "\n\n" .
                                $marketData . "\n\n" .
                                "Réponds en JSON: {\"evaluation\": \"juste|élevé|bas\", \"score\": 1-10, \"message\": \"...\", \"prix_estime_min\": number, \"prix_estime_max\": number}",
                        ],
                    ],
                ]);

                $content = $response->choices[0]->message->content;
                $clean = preg_replace('/```json|```/', '', $content);
                return json_decode(trim($clean), true) ?? $this->defaultPricingAnalysis();
            } catch (\Exception $e) {
                Log::error('OpenAI analyzePricing error: ' . $e->getMessage());
                return $this->defaultPricingAnalysis();
            }
        });
    }

    /**
     * Génère des suggestions de recherche personnalisées.
     */
    public function generateSearchSuggestions(string $query): array
    {
        $cacheKey = 'ai_search_' . md5($query);

        return Cache::remember($cacheKey, 1800, function () use ($query) {
            try {
                $response = OpenAI::chat()->create([
                    'model' => 'gpt-4o-mini',
                    'max_tokens' => 200,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un assistant de recherche immobilière au Maroc. Réponds uniquement en JSON valide avec un tableau de suggestions.',
                        ],
                        [
                            'role' => 'user',
                            'content' => "L'utilisateur recherche: \"{$query}\"\n\nDonne 5 suggestions de recherche pertinentes pour l'immobilier marocain.\nJSON: {\"suggestions\": [\"...\", \"...\", \"...\", \"...\", \"...\"]}",
                        ],
                    ],
                ]);

                $content = $response->choices[0]->message->content;
                $clean = preg_replace('/```json|```/', '', $content);
                $data = json_decode(trim($clean), true);
                return $data['suggestions'] ?? [];
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Modère une annonce (détecte fraude, contenu inapproprié).
     */
    public function moderateListing(Listing $listing): array
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'max_tokens' => 300,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu modères des annonces immobilières pour un site marocain. Détecte les contenus frauduleux, inappropriés, ou les prix aberrants. Réponds en JSON uniquement.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Modère cette annonce:\nTitre: {$listing->title}\nDescription: " . substr($listing->description, 0, 500) . "\nPrix: {$listing->formatted_price}\nVille: {$listing->city}\n\n" .
                            'JSON: {"approved": true|false, "risk_score": 0-100, "flags": [], "reason": "..."}',
                    ],
                ],
            ]);

            $content = $response->choices[0]->message->content;
            $clean = preg_replace('/```json|```/', '', $content);
            return json_decode(trim($clean), true) ?? ['approved' => true, 'risk_score' => 0, 'flags' => []];
        } catch (\Exception $e) {
            return ['approved' => true, 'risk_score' => 0, 'flags' => [], 'error' => true];
        }
    }

    /**
     * Suggère des biens similaires basés sur une annonce.
     */
    public function getSimilarListingsQuery(Listing $listing): array
    {
        return [
            'city' => $listing->city,
            'property_type' => $listing->property_type,
            'transaction_type' => $listing->transaction_type,
            'price_min' => $listing->price * 0.7,
            'price_max' => $listing->price * 1.3,
        ];
    }

    // ─── Méthodes privées ─────────────────────────────────────────────────────

    private function buildFallbackDescription(array $data): string
    {
        $type = ucfirst($data['property_type'] ?? 'bien');
        $transac = $data['transaction_type'] ?? 'vente';
        $city = $data['city'] ?? 'ville';
        $zone = !empty($data['zone']) ? " dans le quartier prisé de {$data['zone']}" : '';
        $surface = !empty($data['surface']) ? " d'une surface de {$data['surface']} m²" : '';
        $rooms = !empty($data['rooms']) ? ", comprenant {$data['rooms']} pièce(s)" : '';
        $action = in_array($transac, ['location', 'vacances']) ? 'louer' : 'acquérir';

        $amenities = array_filter([
            !empty($data['furnished']) ? 'meublé' : null,
            !empty($data['parking'])   ? 'avec parking' : null,
            !empty($data['elevator'])  ? 'avec ascenseur' : null,
            !empty($data['pool'])      ? 'avec piscine' : null,
            !empty($data['garden'])    ? 'avec jardin' : null,
            !empty($data['terrace'])   ? 'avec terrasse' : null,
            !empty($data['security'])  ? 'avec gardiennage' : null,
        ]);
        $amenitiesStr = !empty($amenities) ? ' ' . implode(', ', $amenities) . ',' : '';

        return "Découvrez ce superbe {$type} situé à {$city}{$zone}{$surface}{$rooms}."
            . " Ce bien exceptionnel{$amenitiesStr} vous séduira par son charme et ses prestations de qualité.\n\n"
            . "Baigné de lumière, il offre des volumes généreux et un agencement optimisé pour votre confort au quotidien."
            . " Son emplacement privilégié vous permet de profiter du calme tout en restant à proximité des commodités.\n\n"
            . "Une véritable opportunité à {$action} sur le marché immobilier marocain !"
            . " N'hésitez pas à nous contacter pour plus d'informations ou pour organiser une visite.";
    }

    private function buildDescriptionPrompt(array $data): string
    {
        $amenities = implode(', ', array_filter([
            !empty($data['furnished']) ? 'Meublé' : null,
            !empty($data['parking']) ? 'Parking' : null,
            !empty($data['elevator']) ? 'Ascenseur' : null,
            !empty($data['pool']) ? 'Piscine' : null,
            !empty($data['garden']) ? 'Jardin' : null,
            !empty($data['terrace']) ? 'Terrasse' : null,
            !empty($data['security']) ? 'Gardiennage' : null,
        ]));

        $title = $data['title'] ?? 'Annonce immobilière';
        $city = $data['city'] ?? 'Maroc';
        $zone = !empty($data['zone']) ? ", Quartier: {$data['zone']}" : '';
        $surface = !empty($data['surface']) ? $data['surface'] . ' m²' : 'non précisée';
        $rooms = !empty($data['rooms']) ? (string) $data['rooms'] : 'non précisées';
        $bathrooms = !empty($data['bathrooms']) ? ', Salles de bain: ' . $data['bathrooms'] : '';
        $price = !empty($data['price']) ? number_format((float) $data['price'], 0, ',', ' ') . ' MAD' : 'non communiqué';

        return "Rédige une description immobilière professionnelle et persuasive pour ce bien au Maroc. " .
            "La description doit s'appuyer sur le titre et le développer naturellement.\n\n" .
            "- Titre: {$title}\n" .
            "- Type: {$data['property_type']} ({$data['transaction_type']})\n" .
            "- Ville: {$city}{$zone}\n" .
            "- Surface: {$surface}\n" .
            "- Pièces: {$rooms}{$bathrooms}\n" .
            "- Prix indicatif: {$price}\n" .
            "- Équipements: " . ($amenities ?: 'Standard') . "\n\n" .
            "Consignes :\n" .
            "1. Rédige 2 à 3 paragraphes fluides en français naturel.\n" .
            "2. Commence par une phrase d'accroche cohérente avec le titre.\n" .
            "3. Mets en avant l'emplacement, le confort et les points forts du bien.\n" .
            "4. N'invente pas d'informations absentes.\n" .
            "5. Tu peux évoquer le positionnement du prix sans afficher le montant exact.\n" .
            "Commence directement par la description finale.";
    }

    private function formatAmenities(Listing $listing): string
    {
        $amenities = [];
        if ($listing->furnished) $amenities[] = 'Meublé';
        if ($listing->parking) $amenities[] = 'Parking';
        if ($listing->elevator) $amenities[] = 'Ascenseur';
        if ($listing->pool) $amenities[] = 'Piscine';
        if ($listing->garden) $amenities[] = 'Jardin';
        if ($listing->terrace) $amenities[] = 'Terrasse';
        if ($listing->security) $amenities[] = 'Gardiennage';
        return empty($amenities) ? 'Standard' : implode(', ', $amenities);
    }

    private function getMarketContext(Listing $listing): string
    {
        $avg = Listing::where('city', $listing->city)
            ->where('property_type', $listing->property_type)
            ->where('transaction_type', $listing->transaction_type)
            ->where('status', 'active')
            ->avg('price');

        if (!$avg) {
            return '';
        }

        return "Prix moyen du marché pour ce type de bien à {$listing->city}: " .
            number_format($avg, 0, ',', ' ') . " MAD";
    }

    private function defaultPricingAnalysis(): array
    {
        return [
            'evaluation' => 'juste',
            'score' => 5,
            'message' => 'Analyse non disponible pour le moment.',
            'prix_estime_min' => null,
            'prix_estime_max' => null,
        ];
    }
}
