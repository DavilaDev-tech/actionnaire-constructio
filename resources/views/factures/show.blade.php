@extends('layouts.app')
@section('title', 'Détail Facture')
@section('page-title', 'Détail Facture')

@push('styles')
<style>
    .facture-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #F3F4F6;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .hector-table thead th {
        background: #1F2937 !important;
        color: white !important;
        font-size: 0.78rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
        padding: 12px 16px !important;
        border: none !important;
    }
    .hector-table tbody td {
        padding: 13px 16px !important;
        border-bottom: 1px solid #F9FAFB !important;
        border-top: none !important;
        font-size: 0.875rem !important;
        vertical-align: middle !important;
    }
    .hector-table tbody tr:hover td { background: #FFFBF5 !important; }
    .hector-table tbody tr:last-child td { border-bottom: none !important; }
    .total-ligne {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #F3F4F6;
        font-size: 0.875rem;
    }
    .total-ligne:last-child { border-bottom: none; }
    .statut-badge {
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 0.78rem;
        font-weight: 600;
        display: inline-block;
    }
    .paiement-ligne {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 16px;
        border-bottom: 1px solid #F0FDF4;
        font-size: 0.82rem;
    }
    .paiement-ligne:last-child { border-bottom: none; }
</style>
@endpush

@section('content')

{{-- ── En-tête page ── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold" style="color:#111827">
            Facture {{ $facture->numero }}
        </h4>
        <p style="color:#9CA3AF;font-size:0.875rem;margin:0">
            Vente {{ $facture->vente->numero_vente }}
            — {{ $facture->created_at->format('d/m/Y') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('factures.index') }}"
           class="btn btn-sm"
           style="background:white;border:1px solid #E5E7EB;color:#374151;
                  border-radius:8px;font-size:0.85rem;padding:8px 16px">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
        <a href="{{ route('factures.telecharger', $facture) }}"
           class="btn btn-sm"
           style="background:#F0FDF4;border:1px solid #A7F3D0;color:#10B981;
                  border-radius:8px;font-size:0.85rem;padding:8px 16px;
                  font-weight:600">
            <i class="bi bi-download me-1"></i> Télécharger PDF
        </a>
        @if($facture->statut == 'non_payee')
        <form action="{{ route('factures.marquer-payee', $facture) }}"
              method="POST">
            @csrf @method('PATCH')
            <button type="submit"
                    style="background:linear-gradient(135deg,#F97316,#EA580C);
                           color:white;border:none;border-radius:8px;
                           font-size:0.85rem;padding:8px 16px;font-weight:600;
                           cursor:pointer">
                <i class="bi bi-check-circle me-1"></i> Marquer payée
            </button>
        </form>
        @endif
    </div>
</div>

<div class="row g-4">

    {{-- ── Colonne principale ── --}}
    <div class="col-md-8">

        {{-- En-tête facture --}}
        <div class="facture-card mb-4">
            <div style="background:linear-gradient(135deg,#111827,#1F2937);
                        padding:24px 28px">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div style="font-size:1.3rem;font-weight:800;
                                    color:#F97316;margin-bottom:3px">
                            Actionnaire Construction
                        </div>
                        <div style="font-size:0.82rem;color:rgba(255,255,255,0.5)">
                            Vente de Matériaux de Construction — Douala, Cameroun
                        </div>
                    </div>
                    <div style="text-align:right">
                        <div style="font-size:0.7rem;color:rgba(255,255,255,0.4);
                                    text-transform:uppercase;letter-spacing:1px">
                            Facture N°
                        </div>
                        <div style="font-size:1.4rem;font-weight:800;color:#F97316">
                            {{ $facture->numero }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Méta infos --}}
            <div class="d-flex justify-content-between align-items-center px-4 py-3"
                 style="background:#F9FAFB;border-bottom:1px solid #F3F4F6;
                        flex-wrap:wrap;gap:16px">
                <div>
                    <div style="font-size:0.7rem;color:#9CA3AF;text-transform:uppercase;
                                letter-spacing:0.8px">Date</div>
                    <div style="font-size:0.875rem;font-weight:600;color:#111827">
                        {{ $facture->created_at->format('d/m/Y') }}
                    </div>
                </div>
                <div>
                    <div style="font-size:0.7rem;color:#9CA3AF;text-transform:uppercase;
                                letter-spacing:0.8px">N° Vente</div>
                    <div style="font-size:0.875rem;font-weight:600;color:#111827;
                                font-family:monospace">
                        {{ $facture->vente->numero_vente }}
                    </div>
                </div>
                <div>
                    <div style="font-size:0.7rem;color:#9CA3AF;text-transform:uppercase;
                                letter-spacing:0.8px">Vendeur</div>
                    <div style="font-size:0.875rem;font-weight:600;color:#111827">
                        {{ $facture->vente->user->prenom }}
                        {{ $facture->vente->user->nom }}
                    </div>
                </div>
                <div>
                    <div style="font-size:0.7rem;color:#9CA3AF;text-transform:uppercase;
                                letter-spacing:0.8px">Statut</div>
                    @php
                        $statutStyle = match($facture->statut) {
                            'payee'   => 'background:#F0FDF4;color:#10B981;border:1px solid #A7F3D0',
                            'annulee' => 'background:#FEF2F2;color:#EF4444;border:1px solid #FECACA',
                            default   => 'background:#FFFBEB;color:#D97706;border:1px solid #FDE68A',
                        };
                    @endphp
                    <span class="statut-badge" style="{{ $statutStyle }}">
                        {{ ucfirst(str_replace('_', ' ', $facture->statut)) }}
                    </span>
                </div>
                <div>
                    <div style="font-size:0.7rem;color:#9CA3AF;text-transform:uppercase;
                                letter-spacing:0.8px">TVA</div>
                    @if($facture->tva_applicable)
                    <span style="background:#FFF7ED;color:#F97316;border:1px solid #FED7AA;
                                 border-radius:20px;padding:3px 10px;font-size:0.72rem;
                                 font-weight:600">
                        🟠 19,25%
                    </span>
                    @else
                    <span style="background:#F3F4F6;color:#6B7280;border:1px solid #E5E7EB;
                                 border-radius:20px;padding:3px 10px;font-size:0.72rem;
                                 font-weight:600">
                        ⚪ Exonéré
                    </span>
                    @endif
                </div>
            </div>

            {{-- Emetteur / Client --}}
            <div class="row g-0">
                <div class="col-md-6 px-4 py-3"
                     style="border-right:1px solid #F3F4F6">
                    <div style="font-size:0.7rem;color:#9CA3AF;text-transform:uppercase;
                                letter-spacing:0.8px;margin-bottom:8px">
                        Émetteur
                    </div>
                    <div style="font-weight:700;color:#111827;font-size:0.95rem">
                        Actionnaire Construction
                    </div>
                    <div style="font-size:0.82rem;color:#6B7280;margin-top:3px">
                        Matériaux de Construction
                    </div>
                    <div style="font-size:0.82rem;color:#6B7280">
                        Douala, Cameroun
                    </div>
                </div>
                <div class="col-md-6 px-4 py-3">
                    <div style="font-size:0.7rem;color:#F97316;text-transform:uppercase;
                                letter-spacing:0.8px;margin-bottom:8px;font-weight:700">
                        Facturé à
                    </div>
                    <div style="font-weight:700;color:#111827;font-size:0.95rem">
                        {{ $facture->vente->client->nom }}
                    </div>
                    @if($facture->vente->client->telephone)
                    <div style="font-size:0.82rem;color:#6B7280;margin-top:3px">
                        📞 +237 {{ $facture->vente->client->telephone }}
                    </div>
                    @endif
                    @if($facture->vente->client->adresse)
                    <div style="font-size:0.82rem;color:#6B7280">
                        📍 {{ $facture->vente->client->adresse }}
                    </div>
                    @endif
                    @if($facture->vente->client->exonere_tva)
                    <span style="background:#F3F4F6;color:#6B7280;border:1px solid #E5E7EB;
                                 border-radius:20px;padding:2px 8px;font-size:0.7rem;
                                 font-weight:600;display:inline-block;margin-top:5px">
                        ⚪ Exonéré TVA
                        @if($facture->vente->client->numero_exoneration)
                            — {{ $facture->vente->client->numero_exoneration }}
                        @endif
                    </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tableau produits --}}
        <div class="facture-card">
            <div class="px-4 py-3" style="border-bottom:1px solid #F9FAFB">
                <span class="fw-semibold" style="color:#111827;font-size:0.9rem">
                    <i class="bi bi-box-seam me-2" style="color:#F97316"></i>
                    Détail des produits
                </span>
            </div>
            <div class="table-responsive">
                <table class="table hector-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Désignation</th>
                            <th class="text-center">Qté</th>
                            <th class="text-center">Unité</th>
                            <th class="text-end">Prix unit. HT</th>
                            <th class="text-end">Sous-total HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facture->vente->details as $i => $detail)
                        <tr>
                            <td style="color:#9CA3AF">{{ $i + 1 }}</td>
                            <td>
                                <span class="fw-semibold" style="color:#111827">
                                    {{ $detail->produit->nom }}
                                </span>
                            </td>
                            <td class="text-center">{{ $detail->quantite }}</td>
                            <td class="text-center" style="color:#9CA3AF">
                                {{ $detail->produit->unite }}
                            </td>
                            <td class="text-end">
                                {{ number_format($detail->prix_unitaire, 0, ',', ' ') }} F
                            </td>
                            <td class="text-end fw-semibold" style="color:#111827">
                                {{ number_format($detail->sous_total, 0, ',', ' ') }} F
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totaux --}}
            @php
                $montantHT  = $facture->montant_ht ?? $facture->montant;
                $montantTVA = $facture->montant_tva ?? 0;
                $montantTTC = $facture->montant;
                $tvaAppl    = $facture->tva_applicable ?? false;
            @endphp
            <div class="px-4 py-3" style="border-top:1px solid #F3F4F6">
                <div style="max-width:320px;margin-left:auto">

                    {{-- HT --}}
                    <div class="total-ligne">
                        <span style="color:#6B7280">Sous-total HT</span>
                        <span class="fw-semibold" style="color:#111827">
                            {{ number_format($montantHT, 0, ',', ' ') }} F
                        </span>
                    </div>

                    {{-- TVA --}}
                    @if($tvaAppl)
                    <div class="total-ligne">
                        <span style="color:#F97316">TVA (19,25%)</span>
                        <span class="fw-semibold" style="color:#F97316">
                            + {{ number_format($montantTVA, 0, ',', ' ') }} F
                        </span>
                    </div>
                    @else
                    <div class="total-ligne">
                        <span style="color:#9CA3AF">TVA</span>
                        <span style="color:#9CA3AF;font-size:0.82rem">Exonéré</span>
                    </div>
                    @endif

                    {{-- TTC --}}
                    <div style="background:#111827;border-radius:8px;
                                padding:12px 16px;margin-top:8px;
                                display:flex;justify-content:space-between;
                                align-items:center">
                        <span style="color:white;font-weight:700;font-size:0.95rem">
                            TOTAL TTC
                        </span>
                        <span style="color:#F97316;font-weight:800;font-size:1.2rem">
                            {{ number_format($montantTTC, 0, ',', ' ') }} F CFA
                        </span>
                    </div>

                </div>
            </div>

            {{-- Note --}}
            @if($facture->vente->note)
            <div class="px-4 pb-4">
                <div style="background:#F9FAFB;border-left:3px solid #F97316;
                            border-radius:0 8px 8px 0;padding:10px 14px;
                            font-size:0.875rem;color:#374151">
                    <strong>Note :</strong> {{ $facture->vente->note }}
                </div>
            </div>
            @endif

        </div>

    </div>

    {{-- ── Colonne droite ── --}}
    <div class="col-md-4">

        {{-- Statut paiement --}}
        <div class="facture-card mb-3">
            <div class="px-4 py-3" style="border-bottom:1px solid #F9FAFB">
                <span class="fw-semibold" style="color:#111827;font-size:0.9rem">
                    <i class="bi bi-cash-coin me-2" style="color:#F97316"></i>
                    Suivi paiement
                </span>
            </div>
            <div class="px-4 py-3">

                {{-- Barre progression --}}
                @php
                    $montantPaye  = $facture->montant_paye ?? 0;
                    $montantReste = $facture->montant_restant ?? $facture->montant;
                    $pct = $facture->montant > 0
                        ? min(100, round(($montantPaye / $facture->montant) * 100))
                        : 0;
                @endphp

                <div class="d-flex justify-content-between mb-1">
                    <span style="font-size:0.78rem;color:#6B7280">Progression</span>
                    <span style="font-size:0.78rem;font-weight:700;
                                 color:{{ $pct == 100 ? '#10B981' : '#F97316' }}">
                        {{ $pct }}%
                    </span>
                </div>
                <div style="height:6px;background:#F3F4F6;border-radius:3px;
                            overflow:hidden;margin-bottom:16px">
                    <div style="height:100%;width:{{ $pct }}%;border-radius:3px;
                                background:{{ $pct == 100
                                    ? 'linear-gradient(90deg,#10B981,#059669)'
                                    : 'linear-gradient(90deg,#F97316,#EA580C)' }};
                                transition:width 0.5s ease">
                    </div>
                </div>

                {{-- Montants --}}
                <div style="display:flex;justify-content:space-between;
                            margin-bottom:8px">
                    <span style="font-size:0.82rem;color:#6B7280">Total facture</span>
                    <span style="font-size:0.82rem;font-weight:600;color:#111827">
                        {{ number_format($facture->montant, 0, ',', ' ') }} F
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;
                            margin-bottom:8px">
                    <span style="font-size:0.82rem;color:#10B981">Déjà payé</span>
                    <span style="font-size:0.82rem;font-weight:600;color:#10B981">
                        {{ number_format($montantPaye, 0, ',', ' ') }} F
                    </span>
                </div>
                @if($montantReste > 0)
                <div style="display:flex;justify-content:space-between;
                            padding-top:8px;border-top:1px solid #F3F4F6">
                    <span style="font-size:0.82rem;color:#EF4444;font-weight:600">
                        Reste à payer
                    </span>
                    <span style="font-size:0.82rem;font-weight:700;color:#EF4444">
                        {{ number_format($montantReste, 0, ',', ' ') }} F
                    </span>
                </div>
                @else
                <div style="text-align:center;padding:8px;background:#F0FDF4;
                            border-radius:8px;margin-top:8px">
                    <i class="bi bi-check-circle-fill" style="color:#10B981"></i>
                    <span style="font-size:0.82rem;color:#10B981;font-weight:600;
                                 margin-left:6px">
                        Facture soldée
                    </span>
                </div>
                @endif

            </div>
        </div>

        {{-- Historique paiements --}}
        @if($facture->paiements->count() > 0)
        <div class="facture-card mb-3">
            <div class="px-4 py-3" style="border-bottom:1px solid #F9FAFB">
                <span class="fw-semibold" style="color:#111827;font-size:0.9rem">
                    <i class="bi bi-clock-history me-2" style="color:#10B981"></i>
                    Paiements reçus
                    <span style="background:#F0FDF4;color:#10B981;border:1px solid #A7F3D0;
                                 border-radius:20px;padding:1px 8px;font-size:0.7rem;
                                 font-weight:700;margin-left:6px">
                        {{ $facture->paiements->count() }}
                    </span>
                </span>
            </div>
            @foreach($facture->paiements as $paiement)
            @php
                $modesCfg = [
                    'especes'      => ['libelle'=>'💵 Espèces',      'color'=>'#10B981'],
                    'mobile_money' => ['libelle'=>'📱 Mobile Money', 'color'=>'#3B82F6'],
                    'virement'     => ['libelle'=>'🏦 Virement',     'color'=>'#8B5CF6'],
                    'cheque'       => ['libelle'=>'📄 Chèque',       'color'=>'#D97706'],
                ];
                $modeCfg = $modesCfg[$paiement->mode_paiement]
                    ?? ['libelle'=>$paiement->mode_paiement,'color'=>'#6B7280'];
            @endphp
            <div class="paiement-ligne">
                <div>
                    <div style="font-size:0.82rem;font-weight:500;color:#374151">
                        {{ $modeCfg['libelle'] }}
                    </div>
                    <div style="font-size:0.75rem;color:#9CA3AF">
                        {{ $paiement->date_paiement->format('d/m/Y') }}
                        @if($paiement->reference)
                            — Réf: {{ $paiement->reference }}
                        @endif
                    </div>
                </div>
                <span style="font-weight:700;color:#10B981;font-size:0.9rem">
                    +{{ number_format($paiement->montant, 0, ',', ' ') }} F
                </span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Lien vers paiement --}}
        @if($facture->statut == 'non_payee')
        <a href="{{ route('paiements.create') }}?facture_id={{ $facture->id }}"
           style="display:block;background:linear-gradient(135deg,#F97316,#EA580C);
                  color:white;border:none;border-radius:10px;padding:12px;
                  text-align:center;font-weight:600;font-size:0.875rem;
                  text-decoration:none;box-shadow:0 2px 8px rgba(249,115,22,0.3)">
            <i class="bi bi-plus-circle me-2"></i>
            Enregistrer un paiement
        </a>
        @endif

    </div>
</div>

@endsection