@component('mail::message')

# Votre livraison est en cours !

Bonjour **{{ $client->nom }}**,

Nous vous informons que votre commande est en cours de livraison.

---

@component('mail::panel')
**Détails de votre commande**

-  **N° Vente :** {{ $vente->numero_vente }}
-  **Adresse :** {{ $livraison->adresse_livraison }}
-  **Date prévue :** {{ $livraison->date_livraison ? $livraison->date_livraison->format('d/m/Y') : 'À confirmer' }}
-  **Montant :** {{ number_format($vente->montant_total, 0, ',', ' ') }} F CFA
@endcomponent

---

**Produits commandés :**

@foreach($vente->details as $detail)
- {{ $detail->produit->nom }} × {{ $detail->quantite }} {{ $detail->produit->unite }}
@endforeach

---

@component('mail::button', ['url' => config('app.url'), 'color' => 'blue'])
Voir votre commande
@endcomponent

Pour toute question, contactez-nous.

Cordialement,
**L'équipe Actionnaire Construction**

@endcomponent