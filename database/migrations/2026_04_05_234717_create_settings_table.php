<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, number, boolean, json, image
            $table->string('group')->default('general'); // general, contact, social, seo, payment, email
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // whether to expose via API
            $table->timestamps();
            
            $table->index(['group', 'key']);
        });

        // Insert default settings
        DB::table('settings')->insert([
            // General settings
            ['key' => 'site_name', 'value' => 'DarMaroc', 'type' => 'text', 'group' => 'general', 'label' => 'Nom du site', 'description' => 'Le nom affiché du site web', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_tagline', 'value' => 'Le spécialiste de l\'immobilier au Maroc', 'type' => 'text', 'group' => 'general', 'label' => 'Slogan', 'description' => 'Slogan affiché sur la page d\'accueil', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_description', 'value' => 'Découvrez les meilleures annonces immobilières au Maroc. Achat, vente et location de biens immobiliers.', 'type' => 'textarea', 'group' => 'general', 'label' => 'Description du site', 'description' => 'Description pour les moteurs de recherche', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_logo', 'value' => null, 'type' => 'image', 'group' => 'general', 'label' => 'Logo', 'description' => 'Logo du site (recommandé: 200x50px)', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_favicon', 'value' => null, 'type' => 'image', 'group' => 'general', 'label' => 'Favicon', 'description' => 'Icône du site (recommandé: 32x32px)', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'general', 'label' => 'Mode maintenance', 'description' => 'Activer pour mettre le site en maintenance', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            
            // Contact settings
            ['key' => 'contact_email', 'value' => 'contact@darmaroc.ma', 'type' => 'text', 'group' => 'contact', 'label' => 'Email de contact', 'description' => 'Adresse email principale', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_phone', 'value' => '+212 5XX-XXXXXX', 'type' => 'text', 'group' => 'contact', 'label' => 'Téléphone', 'description' => 'Numéro de téléphone principal', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_address', 'value' => 'Casablanca, Maroc', 'type' => 'textarea', 'group' => 'contact', 'label' => 'Adresse', 'description' => 'Adresse postale', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_whatsapp', 'value' => null, 'type' => 'text', 'group' => 'contact', 'label' => 'WhatsApp', 'description' => 'Numéro WhatsApp (avec code pays)', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            
            // Social media
            ['key' => 'social_facebook', 'value' => null, 'type' => 'text', 'group' => 'social', 'label' => 'Facebook', 'description' => 'URL de la page Facebook', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_instagram', 'value' => null, 'type' => 'text', 'group' => 'social', 'label' => 'Instagram', 'description' => 'URL du profil Instagram', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_twitter', 'value' => null, 'type' => 'text', 'group' => 'social', 'label' => 'Twitter/X', 'description' => 'URL du profil Twitter', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_linkedin', 'value' => null, 'type' => 'text', 'group' => 'social', 'label' => 'LinkedIn', 'description' => 'URL de la page LinkedIn', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_youtube', 'value' => null, 'type' => 'text', 'group' => 'social', 'label' => 'YouTube', 'description' => 'URL de la chaîne YouTube', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            
            // SEO settings
            ['key' => 'seo_meta_title', 'value' => 'DarMaroc - Annonces Immobilières au Maroc', 'type' => 'text', 'group' => 'seo', 'label' => 'Meta Title', 'description' => 'Titre par défaut pour les pages (max 60 caractères)', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'seo_meta_description', 'value' => 'Trouvez votre bien immobilier au Maroc. Des milliers d\'annonces de vente et location.', 'type' => 'textarea', 'group' => 'seo', 'label' => 'Meta Description', 'description' => 'Description par défaut (max 160 caractères)', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'seo_keywords', 'value' => 'immobilier, maroc, vente, location, maison, appartement, villa', 'type' => 'text', 'group' => 'seo', 'label' => 'Mots-clés SEO', 'description' => 'Mots-clés séparés par des virgules', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'seo_google_analytics', 'value' => null, 'type' => 'text', 'group' => 'seo', 'label' => 'Google Analytics ID', 'description' => 'Exemple: G-XXXXXXXXXX', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'seo_google_verification', 'value' => null, 'type' => 'text', 'group' => 'seo', 'label' => 'Google Search Console', 'description' => 'Code de vérification', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            
            // Payment settings
            ['key' => 'payment_currency', 'value' => 'MAD', 'type' => 'text', 'group' => 'payment', 'label' => 'Devise', 'description' => 'Code ISO de la devise (MAD, EUR, USD)', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'payment_stripe_key', 'value' => null, 'type' => 'text', 'group' => 'payment', 'label' => 'Clé publique Stripe', 'description' => 'Clé publique Stripe (pk_...)', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'payment_stripe_secret', 'value' => null, 'type' => 'text', 'group' => 'payment', 'label' => 'Clé secrète Stripe', 'description' => 'Clé secrète Stripe (sk_...)', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'payment_stripe_webhook', 'value' => null, 'type' => 'text', 'group' => 'payment', 'label' => 'Webhook Secret', 'description' => 'Secret du webhook Stripe', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            
            // Email settings
            ['key' => 'email_from_name', 'value' => 'DarMaroc', 'type' => 'text', 'group' => 'email', 'label' => 'Nom de l\'expéditeur', 'description' => 'Nom affiché dans les emails', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'email_from_address', 'value' => 'noreply@darmaroc.ma', 'type' => 'text', 'group' => 'email', 'label' => 'Adresse d\'envoi', 'description' => 'Adresse email d\'envoi', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'email_smtp_host', 'value' => null, 'type' => 'text', 'group' => 'email', 'label' => 'Serveur SMTP', 'description' => 'Hôte du serveur SMTP', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'email_smtp_port', 'value' => '587', 'type' => 'number', 'group' => 'email', 'label' => 'Port SMTP', 'description' => 'Port du serveur SMTP', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'email_smtp_username', 'value' => null, 'type' => 'text', 'group' => 'email', 'label' => 'Utilisateur SMTP', 'description' => 'Nom d\'utilisateur SMTP', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'email_smtp_password', 'value' => null, 'type' => 'text', 'group' => 'email', 'label' => 'Mot de passe SMTP', 'description' => 'Mot de passe SMTP', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            
            // Listing settings
            ['key' => 'listing_max_images', 'value' => '10', 'type' => 'number', 'group' => 'listing', 'label' => 'Max images par annonce', 'description' => 'Nombre maximum d\'images par annonce', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'listing_max_title_length', 'value' => '100', 'type' => 'number', 'group' => 'listing', 'label' => 'Longueur max titre', 'description' => 'Nombre maximum de caractères pour le titre', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'listing_max_description_length', 'value' => '5000', 'type' => 'number', 'group' => 'listing', 'label' => 'Longueur max description', 'description' => 'Nombre maximum de caractères pour la description', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'listing_require_approval', 'value' => '1', 'type' => 'boolean', 'group' => 'listing', 'label' => 'Approbation requise', 'description' => 'Les nouvelles annonces doivent être approuvées', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'listing_auto_expire_days', 'value' => '90', 'type' => 'number', 'group' => 'listing', 'label' => 'Expiration auto (jours)', 'description' => 'Nombre de jours avant expiration automatique', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
