<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            background: white;
        }

        /* ── En-tête ── */
        .header {
            background: #111827;
            color: white;
            padding: 24px 28px;
            margin-bottom: 24px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .header h1 {
            font-size: 20px;
            color: #F97316;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .header p { font-size: 10px; opacity: 0.6; }
        .header-numero {
            text-align: right;
        }
        .header-numero .label {
            font-size: 10px;
            opacity: 0.6;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-numero .numero {
            font-size: 18px;
            font-weight: bold;
            color: #F97316;
        }

        /* ── Statut ── */
        .badge {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-warning { background: #FFFBEB; color: #D97706;
                         border: 1px solid #FDE68A; }
        .badge-success { background: #F0FDF4; color: #10B981;
                         border: 1px solid #A7F3D0; }
        .badge-danger  { background: #FEF2F2; color: #EF4444;
                         border: 1px solid #FECACA; }

        /* ── Infos facture ── */
        .facture-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding: 14px 18px;
            background: #F9FAFB;
            border-radius: 8px;
            border: 1px solid #F3F4F6;
        }
        .facture-meta-item .label {
            font-size: 9px;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
        }
        .facture-meta-item .value {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
        }

        /* ── Parties ── */
        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 20px;
        }
        .partie {
            flex: 1;
            padding: 14px 16px;
            border-radius: 8px;
            border: 1px solid #F3F4F6;
        }
        .partie.emetteur { background: #F9FAFB; }
        .partie.client   { background: #FFF7ED;
                           border-color: #FED7AA; }
        .partie h4 {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 700;
        }
        .partie.emetteur h4 { color: #6B7280; }
        .partie.client h4   { color: #F97316; }
        .partie strong { font-size: 13px; color: #111827; }
        .partie p {
            font-size: 11px;
            color: #6B7280;
            margin-top: 3px;
        }

        /* ── TVA badge ── */
        .tva-badge {
            display: inline-block;
            margin-top: 6px;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
        }
        .tva-assujetti {
            background: #FFF7ED;
            color: #F97316;
            border: 1px solid #FED7AA;
        }
        .tva-exonere {
            background: #F3F4F6;
            color: #6B7280;
            border: 1px solid #E5E7EB;
        }

        /* ── Tableau produits ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead tr {
            background: #111827;
            color: white;
        }
        th {
            padding: 10px 12px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        tbody tr:nth-child(even) { background: #F9FAFB; }
        tbody tr:nth-child(odd)  { background: white; }
        td {
            padding: 9px 12px;
            border-bottom: 1px solid #F3F4F6;
            font-size: 11px;
            color: #374151;
        }
        .text-right  { text-align: right; }
        .text-center { text-align: center; }

        /* ── Totaux ── */
        .totaux-table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .totaux-table td {
            padding: 7px 12px;
            font-size: 11px;
            border: none;
            border-bottom: 1px solid #F3F4F6;
        }
        .totaux-table .label-col { color: #6B7280; }
        .totaux-table .value-col {
            text-align: right;
            font-weight: 600;
            color: #111827;
        }
        .totaux-table .tva-row td { color: #F97316; }
        .totaux-table .tva-row .value-col { color: #F97316; }
        .totaux-table .ttc-row td {
            background: #111827;
            color: white;
            font-size: 13px;
            font-weight: bold;
            border: none;
            padding: 10px 12px;
        }
        .totaux-table .ttc-row .value-col {
            color: #F97316;
            font-size: 15px;
        }
        .totaux-table .exonere-row td {
            color: #9CA3AF;
            font-style: italic;
        }

        /* ── Note ── */
        .note-bloc {
            background: #F9FAFB;
            border-left: 3px solid #F97316;
            padding: 10px 14px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 20px;
            font-size: 11px;
            color: #374151;
        }

        /* ── Paiements ── */
        .paiements-bloc {
            margin-bottom: 24px;
            padding: 14px 16px;
            background: #F0FDF4;
            border-radius: 8px;
            border: 1px solid #A7F3D0;
        }
        .paiements-titre {
            font-size: 10px;
            font-weight: 700;
            color: #065F46;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .paiement-ligne {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #374151;
            padding: 3px 0;
            border-bottom: 1px solid #D1FAE5;
        }
        .paiement-ligne:last-child { border-bottom: none; }

        /* ── Footer ── */
        .footer {
            margin-top: 30px;
            padding-top: 14px;
            border-top: 2px solid #F97316;
            text-align: center;
            font-size: 10px;
            color: #9CA3AF;
        }
        .footer strong { color: #F97316; }
    </style>
</head>
<body>

{{-- ── En-tête ── --}}
<div class="header">
    <div class="header-top">
        <div>
            <h1>Actionnaire Construction</h1>
            <p>Vente de Matériaux de Construction — Douala, Cameroun</p>
        </div>
        <div class="header-numero">
            <div class="label">Facture N°</div>
            <div class="numero">{{ $facture->numero }}</div>
        </div>
    </div>
</div>

{{-- ── Méta infos ── --}}
<div class="facture-meta">
    <div class="facture-meta-item">
        <div class="label">N° Vente</div>
        <div class="value">{{ $facture->vente->numero_vente }}</div>
    </div>
    <div class="facture-meta-item">
        <div class="label">Date d'émission</div>
        <div class="value">{{ $facture->created_at->format('d/m/Y') }}</div>
    </div>
    <div class="facture-meta-item">
        <div class="label">Statut</div>
        <div class="value">
            @php
                $badgeClass = $facture->statut == 'payee'
                    ? 'badge-success'
                    : ($facture->statut == 'annulee'
                        ? 'badge-danger'
                        : 'badge-warning');
            @endphp
            <span class="badge {{ $badgeClass }}">
                {{ strtoupper(str_replace('_', ' ', $facture->statut)) }}
            </span>
        </div>
    </div>
    <div class="facture-meta-item">
        <div class="label">TVA</div>
        <div class="value">
            @if($facture->tva_applicable)
                <span class="tva-badge tva-assujetti">🟠 TVA 19,25%</span>
            @else
                <span class="tva-badge tva-exonere">⚪ Exonéré</span>
            @endif
        </div>
    </div>
</div>

{{-- ── Émetteur / Client ── --}}
<div class="parties">
    <div class="partie emetteur">
        <h4>Émetteur</h4>
        <strong>Actionnaire Construction</strong>
        <p>Vente de Matériaux de Construction</p>
        <p>Douala, Cameroun</p>
    </div>
    <div class="partie client">
        <h4>Facturé à</h4>
        <strong>{{ $facture->vente->client->nom }}</strong>
        @if($facture->vente->client->telephone)
        <p>📞 +237 {{ $facture->vente->client->telephone }}</p>
        @endif
        @if($facture->vente->client->adresse)
        <p>📍 {{ $facture->vente->client->adresse }}</p>
        @endif
        @if($facture->vente->client->email)
        <p>✉️ {{ $facture->vente->client->email }}</p>
        @endif
        @if($facture->vente->client->exonere_tva)
        <span class="tva-badge tva-exonere" style="margin-top:6px">
            ⚪ Exonéré TVA
            @if($facture->vente->client->numero_exoneration)
                — N° {{ $facture->vente->client->numero_exoneration }}
            @endif
        </span>
        @endif
    </div>
</div>

{{-- ── Tableau produits ── --}}
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Désignation</th>
            <th class="text-center">Qté</th>
            <th class="text-center">Unité</th>
            <th class="text-right">Prix unit. HT</th>
            <th class="text-right">Sous-total HT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($facture->vente->details as $i => $detail)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $detail->produit->nom }}</strong></td>
            <td class="text-center">{{ $detail->quantite }}</td>
            <td class="text-center">{{ $detail->produit->unite }}</td>
            <td class="text-right">
                {{ number_format($detail->prix_unitaire, 0, ',', ' ') }} F
            </td>
            <td class="text-right">
                {{ number_format($detail->sous_total, 0, ',', ' ') }} F
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ── Totaux ── --}}
@php
    $montantHT  = $facture->montant_ht  ?? $facture->vente->montant_ht  ?? $facture->montant;
    $montantTVA = $facture->montant_tva ?? $facture->vente->montant_tva ?? 0;
    $montantTTC = $facture->montant;
    $tvaAppl    = $facture->tva_applicable ?? $facture->vente->tva_applicable ?? false;
@endphp

<table class="totaux-table">
    {{-- Sous-total HT --}}
    <tr>
        <td class="label-col">Sous-total HT</td>
        <td class="value-col">
            {{ number_format($montantHT, 0, ',', ' ') }} F
        </td>
    </tr>

    {{-- TVA --}}
    @if($tvaAppl)
    <tr class="tva-row">
        <td class="label-col">TVA (19,25%)</td>
        <td class="value-col">
            + {{ number_format($montantTVA, 0, ',', ' ') }} F
        </td>
    </tr>
    @else
    <tr class="exonere-row">
        <td class="label-col">TVA</td>
        <td class="value-col" style="color:#9CA3AF">Exonéré</td>
    </tr>
    @endif

    {{-- Total TTC --}}
    <tr class="ttc-row">
        <td class="label-col">TOTAL TTC</td>
        <td class="value-col">
            {{ number_format($montantTTC, 0, ',', ' ') }} F CFA
        </td>
    </tr>
</table>

{{-- ── Paiements effectués ── --}}
@if($facture->paiements->count() > 0)
<div class="paiements-bloc">
    <div class="paiements-titre">
        ✓ Paiements reçus
    </div>
    @foreach($facture->paiements as $paiement)
    <div class="paiement-ligne">
        <span>
            {{ $paiement->date_paiement->format('d/m/Y') }}
            — {{ $paiement->libelle_mode ?? $paiement->mode_paiement }}
            @if($paiement->reference)
                (Réf: {{ $paiement->reference }})
            @endif
        </span>
        <strong style="color:#10B981">
            {{ number_format($paiement->montant, 0, ',', ' ') }} F
        </strong>
    </div>
    @endforeach

    @if($facture->montant_restant > 0)
    <div class="paiement-ligne"
         style="border-top:1px solid #A7F3D0;margin-top:4px;padding-top:6px">
        <span style="color:#EF4444;font-weight:600">Reste à payer</span>
        <strong style="color:#EF4444">
            {{ number_format($facture->montant_restant, 0, ',', ' ') }} F
        </strong>
    </div>
    @endif
</div>
@endif

{{-- ── Note ── --}}
@if($facture->vente->note)
<div class="note-bloc">
    <strong>Note :</strong> {{ $facture->vente->note }}
</div>
@endif

{{-- ── Footer ── --}}
<div class="footer">
    <p>
        <strong>Actionnaire Construction</strong>
        — Vente de Matériaux de Construction — Douala, Cameroun
    </p>
    <p style="margin-top:4px">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </p>
</div>

</body>
</html>