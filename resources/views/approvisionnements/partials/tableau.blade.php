<table class="table hector-table mb-0">
    <thead>
        <tr>
            <th>N° Appro</th>
            <th>Fournisseur</th>
            <th>Responsable</th>
            <th>Date</th>
            <th>Montant</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($appros as $appro)
        @php
            $initiale = strtoupper(substr($appro->fournisseur->nom, 0, 1));
            $colors   = ['#F97316','#3B82F6','#10B981','#8B5CF6','#EF4444','#F59E0B'];
            $color    = $colors[ord($initiale) % count($colors)];
        @endphp
        <tr>
            {{-- N° Appro --}}
            <td>
                <span class="fw-semibold"
                      style="color:#111827;font-family:monospace;font-size:0.82rem">
                    {{ $appro->numero }}
                </span>
            </td>

            {{-- Fournisseur --}}
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div style="width:30px;height:30px;border-radius:50%;
                                background:{{ $color }}20;color:{{ $color }};
                                display:flex;align-items:center;justify-content:center;
                                font-weight:700;font-size:0.75rem;flex-shrink:0">
                        {{ $initiale }}
                    </div>
                    <div>
                        <div style="color:#111827;font-weight:500">
                            {{ $appro->fournisseur->nom }}
                        </div>
                        <div style="font-size:0.72rem;color:#9CA3AF">
                            {{ $appro->details->count() }} produit{{ $appro->details->count() > 1 ? 's' : '' }}
                        </div>
                    </div>
                </div>
            </td>

            {{-- Responsable --}}
            <td>
                <div style="font-size:0.82rem;color:#374151">
                    {{ $appro->user->prenom ?? '—' }}
                </div>
            </td>

            {{-- Date --}}
            <td style="color:#9CA3AF;font-size:0.82rem">
                <i class="bi bi-calendar3 me-1"></i>
                {{ $appro->date_appro->format('d/m/Y') }}
            </td>

            {{-- Montant --}}
            <td>
                <span class="fw-semibold" style="color:#111827">
                    {{ number_format($appro->montant_total, 0, ',', ' ') }}
                    <span style="color:#9CA3AF;font-weight:400;font-size:0.78rem"> F</span>
                </span>
            </td>

            {{-- Statut --}}
            <td>
                <span class="statut-badge statut-{{ $appro->statut }}">
                    @if($appro->statut == 'en_attente') ⏳ En attente
                    @elseif($appro->statut == 'recu')   ✓ Reçu
                    @else                               ✕ Annulé
                    @endif
                </span>
            </td>

            {{-- Actions --}}
            <td>
                <div class="d-flex gap-1 justify-content-end align-items-center">
                    <a href="{{ route('approvisionnements.show', $appro) }}"
                       class="btn-action view" title="Voir">
                        <i class="bi bi-eye"></i>
                    </a>
                    @if(!$appro->isRecu() && !$appro->isAnnule())
                    <form action="{{ route('approvisionnements.statut', $appro) }}"
                          method="POST">
                        @csrf @method('PATCH')
                        <select name="statut" class="select-statut"
                                onchange="this.form.submit()">
                            <option value="en_attente"
                                {{ $appro->statut=='en_attente' ? 'selected':'' }}>
                                En attente
                            </option>
                            <option value="recu">✅ Marquer reçu</option>
                            <option value="annule">❌ Annuler</option>
                        </select>
                    </form>
                    <form action="{{ route('approvisionnements.destroy', $appro) }}"
                          method="POST"
                          onsubmit="return confirm('Supprimer cet approvisionnement ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-action del" title="Supprimer">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-5">
                <i class="bi bi-inbox"
                   style="font-size:2.5rem;display:block;
                          margin-bottom:12px;color:#D1D5DB"></i>
                <div style="font-size:0.875rem;color:#9CA3AF">
                    Aucun approvisionnement trouvé
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Pagination --}}