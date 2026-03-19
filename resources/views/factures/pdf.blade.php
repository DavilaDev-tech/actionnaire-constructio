<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }

        .header { background: #1a3c5e; color: white; padding: 20px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #e8a020; }
        .header p  { font-size: 11px; opacity: 0.8; }

        .facture-info { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-warning { background: #ffc107; color: #000; }
        .badge-success { background: #198754; color: #fff; }
        .badge-danger  { background: #dc3545; color: #fff; }

        .parties { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .partie h4 { color: #1a3c5e; font-size: 11px; text-transform: uppercase; margin-bottom: 5px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead { background: #1a3c5e; color: white; }
        th, td { padding: 8px 10px; border: 1px solid #dee2e6; }
        th { font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #1a3c5e; color: white; font-weight: bold; font-size: 14px; }

        .footer { margin-top: 30px; padding-top: 15px;
                  border-top: 2px solid #e8a020; text-align: center;
                  font-size: 10px; color: #666; }
    </style>
</head>
<body>

    <!-- En-tête -->
    <div class="header">
        <h1>Actionnaire Construction</h1>
        <p>Vente de Matériaux de Construction</p>
    </div>

    <!-- Infos facture -->
    <div class="facture-info">
        <div>
            <h2>FACTURE N° {{ $facture->numero }}</h2>
            <p>Vente N° : {{ $facture->vente->numero_vente }}</p>
            <p>Date : {{ $facture->created_at->format('d/m/Y') }}</p>
        </div>
        <div>
            @php
                $badgeClass = $facture->statut == 'payee' ? 'badge-success' :
                              ($facture->statut == 'annulee' ? 'badge-danger' : 'badge-warning');
            @endphp
            <span class="badge {{ $badgeClass }}">
                {{ strtoupper(str_replace('_', ' ', $facture->statut)) }}
            </span>
        </div>
    </div>

    <!-- Parties -->
    <div class="parties">
        <div class="partie">
            <h4>Émetteur</h4>
            <strong>Actionnaire Construction</strong><br>
            Matériaux de Construction<br>
            Cameroun
        </div>
        <div class="partie">
            <h4>Facturé à</h4>
            <strong>{{ $facture->vente->client->nom }}</strong><br>
            {{ $facture->vente->client->telephone }}<br>
            {{ $facture->vente->client->adresse }}
        </div>
    </div>

    <!-- Tableau produits -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Désignation</th>
                <th class="text-center">Qté</th>
                <th class="text-right">Prix unit.</th>
                <th class="text-right">Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facture->vente->details as $i => $detail)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $detail->produit->nom }}</td>
                <td class="text-center">
                    {{ $detail->quantite }} {{ $detail->produit->unite }}
                </td>
                <td class="text-right">
                    {{ number_format($detail->prix_unitaire, 0, ',', ' ') }} F
                </td>
                <td class="text-right">
                    {{ number_format($detail->sous_total, 0, ',', ' ') }} F
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL GÉNÉRAL</td>
                <td class="text-right">
                    {{ number_format($facture->montant, 0, ',', ' ') }} F CFA
                </td>
            </tr>
        </tfoot>
    </table>

    @if($facture->vente->note)
    <p><strong>Note :</strong> {{ $facture->vente->note }}</p>
    @endif

    <!-- Pied de page -->
    <div class="footer">
        <p>Merci pour votre confiance — Actionnaire Construction</p>
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

</body>
</html>