<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Peuple la base de données avec des données de démonstration.
     */
    public function run(): void
    {
        // ── Subscription Plans ──────────────────────────────────────
        $this->call(SubscriptionPlanSeeder::class);

        // ── Administrateur ──────────────────────────────────────────
        User::create([
            'name'              => 'Admin DarMaroc',
            'email'             => 'admin@darmaroc.ma',
            'phone'             => '+212 600 000 000',
            'role'              => 'admin',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);

        // ── Agents ──────────────────────────────────────────────────
        $agents = [];
        $agentData = [
            ['Ahmed El Fassi', 'ahmed@agent.ma', '+212 661 123 456'],
            ['Fatima Benali',  'fatima@agent.ma', '+212 662 234 567'],
            ['Karim Lahlou',   'karim@agent.ma', '+212 663 345 678'],
        ];

        foreach ($agentData as [$name, $email, $phone]) {
            $agents[] = User::create([
                'name'              => $name,
                'email'             => $email,
                'phone'             => $phone,
                'role'              => 'agent',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active'         => true,
            ]);
        }

        // ── Particuliers ─────────────────────────────────────────────
        $particuliers = [];
        for ($i = 1; $i <= 5; $i++) {
            $particuliers[] = User::create([
                'name'              => "Particulier Test $i",
                'email'             => "user$i@test.ma",
                'phone'             => "+212 66{$i} 000 00{$i}",
                'role'              => 'particulier',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active'         => true,
            ]);
        }

        // ── Annonces avec visuels réels ─────────────────────────────
        $listings = [
            [
                'user_id'          => $agents[0]->id,
                'title'            => 'Magnifique Villa avec piscine à Marrakech Palmeraie',
                'description'      => "Superbe villa de standing avec piscine privée au cœur de la Palmeraie de Marrakech. Cette propriété d'exception offre une vue imprenable sur l'Atlas. Elle comprend 5 chambres, 4 salles de bain, un salon marocain traditionnel et un jardin paysager de 1000 m². Idéale pour une résidence principale ou secondaire haut de gamme.",
                'price'            => 6500000,
                'surface'          => 450,
                'rooms'            => 5,
                'bathrooms'        => 4,
                'transaction_type' => 'vente',
                'property_type'    => 'villa',
                'city'             => 'Marrakech',
                'zone'             => 'Palmeraie',
                'latitude'         => 31.6620,
                'longitude'        => -7.9541,
                'furnished'        => true,
                'parking'          => true,
                'pool'             => true,
                'garden'           => true,
                'security'         => true,
                'status'           => 'active',
                'featured'         => true,
                'views'            => 342,
                'thumbnail'        => 'https://sspark.genspark.ai/cfimages?u1=wJFIlHlg8bxQkzLY2o3fGVIw%2BPD8OntN%2FVrbqu1S%2Bdu9iQqvhSavK6Oiy%2BgoGOuakaf%2BJhhn72DyCDH%2BGLpZaF%2FktlWg3bJHAdR12Klhq20AdG%2B9p9R4apw%2BzDfjKb09rwTaAGJzC1Jfs2SjRVBy2wpqF55t7IMKCjbn%2BA5FEMxEWNrS2zmAZgBe9%2BIKgxtL9w5Koh2kC75Q7WiJsgygRHR2nB7umxUtfNi8vaLdODyUTA%3D%3D&u2=3q%2FceohVzeTDR0j9&width=2560',
            ],
            [
                'user_id'          => $agents[0]->id,
                'title'            => 'Riad traditionnel rénové au cœur de la médina de Fès',
                'description'      => "Authentique riad du XIXe siècle entièrement restauré avec goût dans la médina de Fès. 4 suites décorées dans le style marocain traditionnel, patio central avec fontaine et zellige, terrasse panoramique. Parfait pour un hébergement de charme ou une maison d'hôtes.",
                'price'            => 3200000,
                'surface'          => 280,
                'rooms'            => 4,
                'bathrooms'        => 4,
                'transaction_type' => 'vente',
                'property_type'    => 'riad',
                'city'             => 'Fès',
                'zone'             => 'Médina',
                'latitude'         => 34.0639,
                'longitude'        => -4.9774,
                'furnished'        => true,
                'terrace'          => true,
                'status'           => 'active',
                'featured'         => true,
                'views'            => 218,
                'thumbnail'        => 'https://sspark.genspark.ai/cfimages?u1=3NWEopF%2B%2FFsmX55q6S%2F15V6Pi7Ga8n5B2tQuxLZHnEawo2nPW6YpsC108At73h%2FWN2bBjXa4bFz8POqn6E03BXsunk3cSyPvVzm9Y6rR1uWCFfrW2w5a0iMMQy769scCczFBX2y3c4%2Fk%2FB%2BFc%2BZxANd77yiOTLxUplMND%2BiCMRBD0q77j3XsMw%3D%3D&u2=PTc82B9hFzjoyDhW&width=2560',
            ],
            [
                'user_id'          => $agents[1]->id,
                'title'            => 'Appartement moderne 3 chambres à Casablanca Maarif',
                'description'      => "Bel appartement de 120 m² situé au 4ème étage d'une résidence sécurisée dans le quartier prisé de Maarif. Entièrement rénové avec des matériaux haut de gamme. Grande cuisine équipée, salon lumineux, 3 chambres dont une suite parentale, 2 terrasses.",
                'price'            => 1850000,
                'surface'          => 120,
                'rooms'            => 3,
                'bathrooms'        => 2,
                'floor'            => 4,
                'transaction_type' => 'vente',
                'property_type'    => 'appartement',
                'city'             => 'Casablanca',
                'zone'             => 'Maarif',
                'latitude'         => 33.5901,
                'longitude'        => -7.6323,
                'furnished'        => false,
                'parking'          => true,
                'elevator'         => true,
                'security'         => true,
                'status'           => 'active',
                'featured'         => true,
                'views'            => 156,
                'thumbnail'        => 'https://sspark.genspark.ai/cfimages?u1=EE%2Fh5U%2Bd3AIdx3jxeHZzkZ8w6L39PCVfbH7b%2F2ghv%2FsPbRs4qPdv%2Fn623vdMaxP%2BkPq%2BJTklwEivbyqmWcw66hOXNBuUrO6OSC%2BNiR9zEk1peGFSnf7og9QKSFP7b08YgNIoPlGEdVz18HCDc34roHEOX80OllomHSZJLYcDtzWKpkhZjg%3D%3D&u2=ZtrUZ3sUJi2rLR5u&width=2560',
            ],
            [
                'user_id'          => $agents[1]->id,
                'title'            => 'Appartement à louer meublé à Rabat Agdal',
                'description'      => "Appartement meublé de 85 m² dans une résidence moderne à Agdal. Entièrement équipé, climatisé, avec internet haut débit inclus. Idéal pour expatriés ou professionnels. 2 chambres, salon, cuisine équipée, salle de bain.",
                'price'            => 12000,
                'surface'          => 85,
                'rooms'            => 2,
                'bathrooms'        => 1,
                'floor'            => 2,
                'transaction_type' => 'location',
                'price_period'     => 'mois',
                'property_type'    => 'appartement',
                'city'             => 'Rabat',
                'zone'             => 'Agdal',
                'latitude'         => 33.9803,
                'longitude'        => -6.8498,
                'furnished'        => true,
                'parking'          => true,
                'elevator'         => true,
                'status'           => 'active',
                'views'            => 89,
                'thumbnail'        => 'https://sspark.genspark.ai/cfimages?u1=ZjH84mWNk5Nbp9vAPaLpaksj447S1BW%2Ba61Ff2a1qwLgXilBD%2BdQrCw5I6shiw6mhQTxAS5Ea62C2hMechLpQo3T2O6V%2FYrBVWbp7x7WmoAomYRS7Lm9Ej3q2latn1Y6lbyb%2BUo4yY4RmVqV7Qg0SjRwtsVHQcBWPZ7GtyMtyjmUFu70lw%3D%3D&u2=xt9McBsYBZS68NLg&width=2560',
            ],
            [
                'user_id'          => $agents[2]->id,
                'title'            => 'Terrain constructible 500 m² à Tanger Malabata',
                'description'      => "Terrain plat de 500 m² avec vue sur mer, idéalement situé dans la zone balnéaire de Malabata. Tous réseaux en limite de propriété. Permis de construire obtenu pour R+3. Excellent investissement.",
                'price'            => 1200000,
                'surface'          => 500,
                'transaction_type' => 'vente',
                'property_type'    => 'terrain',
                'city'             => 'Tanger',
                'zone'             => 'Malabata',
                'latitude'         => 35.7892,
                'longitude'        => -5.7741,
                'status'           => 'active',
                'views'            => 67,
                'thumbnail'        => 'https://sspark.genspark.ai/cfimages?u1=mpv8%2FFJtSIVDOjVLy5FMv%2Bu%2B6GAckJon4q8fVguhg2GO%2F0h6LiqWdpJJFjdvPVmJ0J6VcJlqzJHbjxob%2FV58WLUqxTKWL%2BId%2B864S6Z8hIcNI7tSkZb6HYOgQDpm%2FbPYATU1OjiM8Tcuw1RVfp4HQg%3D%3D&u2=VNv6Vbvsn%2B53IE0b&width=2560',
            ],
            [
                'user_id'          => $agents[2]->id,
                'title'            => 'Villa de vacances avec vue mer à Agadir',
                'description'      => "Splendide villa à louer pour vos vacances à Agadir avec vue imprenable sur l'Atlantique. 4 chambres, piscine privée, jardin, terrasse. À 5 min des meilleures plages. Disponible juillet-août.",
                'price'            => 8000,
                'surface'          => 200,
                'rooms'            => 4,
                'bathrooms'        => 3,
                'transaction_type' => 'vacances',
                'price_period'     => 'semaine',
                'property_type'    => 'villa',
                'city'             => 'Agadir',
                'zone'             => 'Founty',
                'latitude'         => 30.4369,
                'longitude'        => -9.5906,
                'furnished'        => true,
                'pool'             => true,
                'garden'           => true,
                'terrace'          => true,
                'status'           => 'active',
                'featured'         => true,
                'views'            => 203,
                'thumbnail'        => 'https://sspark.genspark.ai/cfimages?u1=rD91dV8Np1cbxM3NiTGf4isHguw6KNJ6Xi4UUKQhtgk4xcgJkVXDWaD%2FUakTdINMbUKwOiTeFn%2FuIVxBdwlw79p1JXES8OQRtvOozXpUJ07r7u%2Fjp9fth%2FSh&u2=W9ka9uTqeKKtzdBz&width=2560',
            ],
            [
                'user_id'          => $particuliers[0]->id,
                'title'            => 'Bureau 60 m² à louer à Casablanca Anfa',
                'description'      => "Local commercial idéal pour bureau ou cabinet médical. 60 m² au rez-de-chaussée d'une résidence premium à Anfa. Climatisation, fibre optique, parking inclus.",
                'price'            => 7500,
                'surface'          => 60,
                'transaction_type' => 'location',
                'price_period'     => 'mois',
                'property_type'    => 'bureau',
                'city'             => 'Casablanca',
                'zone'             => 'Anfa',
                'latitude'         => 33.5908,
                'longitude'        => -7.6497,
                'parking'          => true,
                'elevator'         => true,
                'security'         => true,
                'status'           => 'active',
                'views'            => 44,
                'thumbnail'        => 'https://sspark.genspark.ai/cfimages?u1=%2BhM2Z27yzKmiyz2eFIrYNYW2DQg16eVoxU2U0YkHI0db4WbpWbVoOHgPSfX%2BW%2FxHLXt%2BAzAFEufE2jBMpWDxamwmW7F3njWfjcfHwga97NA70wUr%2BcU3Z4jvUYCAtjZjeLRKQPnv%2F%2F0RuEPpytKFmeAxxlqyRrK3Tw%3D%3D&u2=pAhuvkxxgra7T7%2FI&width=2560',
            ],
            [
                'user_id'          => $particuliers[1]->id,
                'title'            => 'Appartement neuf F3 à Meknès résidence El Harti',
                'description'      => "Appartement neuf dans programme immobilier de standing. Livraison prévue T2 2025. 3 pièces, finitions premium, cuisine aménagée, parking souterrain, espace vert.",
                'price'            => 890000,
                'surface'          => 95,
                'rooms'            => 3,
                'bathrooms'        => 2,
                'transaction_type' => 'neuf',
                'property_type'    => 'appartement',
                'city'             => 'Meknès',
                'zone'             => 'El Harti',
                'latitude'         => 33.8922,
                'longitude'        => -5.5483,
                'parking'          => true,
                'elevator'         => true,
                'status'           => 'active',
                'views'            => 31,
                'thumbnail'        => 'https://sspark.genspark.ai/cfimages?u1=XjJyMwguH6iNaYjfzzzOaUdJhge9HbE3RlwTqhMoJmWLiCEVPJ8JHWT5au6OfAt0SEeB0RAmM%2FcWO0cRhXqqEMkK8XtEXqq2BB9VZZIXWuDEAyE1v05nRQzZHfrg%2B1QvqJliJ6V%2B9A6tDLqLQxh1SyzB&u2=kMMa5%2BwcHzcu2rey&width=2560',
            ],
            [
                'user_id'          => $particuliers[2]->id,
                'title'            => 'Maison 4 chambres à vendre à Kénitra',
                'description'      => "Belle maison familiale de 180 m² avec jardin privatif. 4 chambres, salon double, cuisine équipée, 2 salles de bain. Quartier calme et résidentiel.",
                'price'            => 1100000,
                'surface'          => 180,
                'rooms'            => 4,
                'bathrooms'        => 2,
                'transaction_type' => 'vente',
                'property_type'    => 'maison',
                'city'             => 'Kénitra',
                'zone'             => 'Centre',
                'latitude'         => 34.2575,
                'longitude'        => -6.5894,
                'garden'           => true,
                'parking'          => true,
                'status'           => 'pending',
                'views'            => 3,
                'thumbnail'        => 'https://sspark.genspark.ai/cfimages?u1=JxhtyYY8HUTFt776izvIj53feGOFLWbcSdlIZROVo4Nr37YHyKL8OU%2BoqQina60MzQIB28YrQ1b01JVxopcL6BaFasxsSwUjQgTCFJdfuvDfVfkhKE0XDwRKNHkUfS9CROZ9nrlB&u2=CuK7PyNBpPeJ7sNU&width=2560',
            ],
        ];

        foreach ($listings as $data) {
            $listing = Listing::create($data);

            if (!empty($data['thumbnail'])) {
                ListingImage::create([
                    'listing_id' => $listing->id,
                    'path'       => $data['thumbnail'],
                    'alt'        => $data['title'],
                    'order'      => 0,
                ]);
            }
        }

        // ── Quelques favoris ─────────────────────────────────────────
        $allListings = Listing::active()->take(5)->get();
        foreach ($allListings as $listing) {
            Favorite::create([
                'user_id'    => $particuliers[0]->id,
                'listing_id' => $listing->id,
            ]);
        }

        $this->command->info('✅ Base de données peuplée avec succès !');
        $this->command->table(['Rôle', 'Email', 'Mot de passe'], [
            ['Admin',       'admin@sarouty.ma', 'sarouty123'],
            ['Agent',       'ahmed@agent.ma',    'password'],
            ['Particulier', 'user1@test.ma',     'password'],
        ]);
    }
}
