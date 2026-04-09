<table class="table hector-table mb-0">
    <thead>
        <tr>
            <th>Facture</th>
            <th>Client</th>
            <th>Montant</th>
            <th>Mode</th>
            <th class="text-center">Statut Facture</th> {{-- Nouvelle colonne --}}
            <th>Date</th>
            <th>Référence</th>
            <th>Enregistré par</th>
            <th class="text-end">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($paiements as $paiement)
        @php
            $initiale = strtoupper(substr($paiement->facture->vente->client->nom ?? 'C', 0, 1));
            $colors   = ['#F97316','#3B82F6','#10B981','#8B5CF6','#EF4444','#F59E0B'];
            $color    = $colors[ord($initiale) % count($colors)];
            $modes    = [
                'especes'      => ['libelle' => 'Espèces',      'class' => 'mode-especes',      'icon' => '💵'],
                'mobile_money' => ['libelle' => 'Mobile Money', 'class' => 'mode-mobile_money', 'icon' => '📱'],
                'virement'     => ['libelle' => 'Virement',     'class' => 'mode-virement',     'icon' => '🏦'],
                'cheque'       => ['libelle' => 'Chèque',       'class' => 'mode-cheque',       'icon' => '📄'],
            ];
            $mode = $modes[$paiement->mode_paiement] ?? ['libelle' => $paiement->mode_paiement, 'class' => 'mode-especes', 'icon' => '💰'];
            
            // Calcul du statut pour l'affichage
            $statutFacture = $paiement->facture->statut;
            $reste = $paiement->facture->resteAPayer();
        @endphp
        <tr>
            {{-- Facture --}}
            <td>
                <span class="fw-semibold"
                      style="color:#111827;font-family:monospace;font-size:0.82rem">
                    {{ $paiement->facture->numero }}
                </span>
            </td>

            {{-- Client --}}
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div style="width:30px;height:30px;border-radius:50%;
                                background:{{ $color }}20;color:{{ $color }};
                                display:flex;align-items:center;justify-content:center;
                                font-weight:700;font-size:0.75rem;flex-shrink:0">
                        {{ $initiale }}
                    </div>
                    <span style="color:#111827;font-weight:500">
                        {{ $paiement->facture->vente->client->nom ?? 'Client Inconnu' }}
                    </span>
                </div>
            </td>

            {{-- Montant --}}
            <td>
                <span class="fw-bold" style="color:#10B981;font-size:0.9rem">
                    +{{ number_format($paiement->montant, 0, ',', ' ') }}
                    <span style="color:#9CA3AF;font-weight:400;font-size:0.78rem"> F</span>
                </span>
            </td>

            {{-- Mode --}}
            <td>
                <span class="mode-badge {{ $mode['class'] }}">
                    {{ $mode['icon'] }} {{ $mode['libelle'] }}
                </span>
            </td>

            {{-- NOUVEAU : Statut Facture --}}
            <td class="text-center">
                @if($statutFacture == 'payee')
                    <span class="statut-badge statut-payee" style="background:#DCFCE7; color:#15803D; padding:4px 8px; border-radius:6px; font-size:0.7rem; font-weight:700; text-transform:uppercase;">
                        <i class="bi bi-check-circle-fill me-1"></i>Soldée
                    </span>
                @elseif($statutFacture == 'partiellement_payee')
                    <span class="statut-badge statut-partiel" title="Reste à payer: {{ number_format($reste, 0, ',', ' ') }} F" 
                          style="background:#FEF9C3; color:#854D0E; padding:4px 8px; border-radius:6px; font-size:0.7rem; font-weight:700; text-transform:uppercase; cursor:help;">
                        <i class="bi bi-hourglass-split me-1"></i>Partiel
                    </span>
                @else
                    <span class="statut-badge statut-non_payee" style="background:#FEE2E2; color:#991B1B; padding:4px 8px; border-radius:6px; font-size:0.7rem; font-weight:700; text-transform:uppercase;">
                        Impayée
                    </span>
                @endif
            </td>

            {{-- Date --}}
            <td style="color:#9CA3AF;font-size:0.82rem">
                <i class="bi bi-calendar3 me-1"></i>
                {{ $paiement->date_paiement->format('d/m/Y') }}
            </td>

            {{-- Référence --}}
            <td>
                @if($paiement->reference)
                <span style="background:#F3F4F6;color:#374151;
                             border-radius:6px;padding:3px 8px;
                             font-size:0.75rem;font-family:monospace">
                    {{ $paiement->reference }}
                </span>
                @else
                <span style="color:#D1D5DB">—</span>
                @endif
            </td>

            {{-- Enregistré par --}}
            <td style="color:#9CA3AF;font-size:0.82rem">
                {{ $paiement->createdBy->prenom ?? '—' }}
            </td>

            {{-- Actions --}}
            <td>
                <div class="d-flex gap-1 justify-content-end">
                    <a href="{{ route('paiements.show', $paiement) }}"
                       class="btn-action view" title="Voir">
                        <i class="bi bi-eye"></i>
                    </a>
                    <form action="{{ route('paiements.destroy', $paiement) }}"
                          method="POST"
                          onsubmit="return confirm('Supprimer ce paiement ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-action del" title="Supprimer">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center py-5">
                <i class="bi bi-cash-coin"
                   style="font-size:2.5rem;display:block;
                          margin-bottom:12px;color:#D1D5DB"></i>
                <div style="font-size:0.875rem;color:#9CA3AF">
                    Aucun paiement enregistré
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>