<table class="table hector-table mb-0">
    <thead>
        <tr>
            <th>Client</th>
            <th>Type</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Adresse</th>
            <th class="text-center">Ventes</th>
            <th class="text-end">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($clients as $client)
        @php
            $initiale = strtoupper(substr($client->nom, 0, 1));
            $colors   = ['#F97316','#3B82F6','#10B981','#8B5CF6','#EF4444','#F59E0B'];
            $color    = $colors[ord($initiale) % count($colors)];
        @endphp
        <tr>
            {{-- Client --}}
            <td>
                <div class="d-flex align-items-center gap-3">
                    <div class="client-avatar"
                         style="background:{{ $color }}20;color:{{ $color }}">
                        {{ $initiale }}
                    </div>
                    <div>
                        <div class="fw-semibold" style="color:#111827">
                            {{ $client->nom }}
                        </div>
                        <div style="font-size:0.75rem;color:#9CA3AF">
                            Client #{{ $client->id }}
                            @if($client->exonere_tva)
                            · <span style="color:#6B7280">⚪ Exonéré TVA</span>
                            @endif
                        </div>
                    </div>
                </div>
            </td>

            {{-- Type --}}
            <td>
                @if($client->type == 'particulier')
                    <span class="badge-particulier">Particulier</span>
                @else
                    <span class="badge-entreprise">Entreprise</span>
                @endif
            </td>

            {{-- Téléphone --}}
            <td style="color:#374151">
                {{ $client->telephone ? '+237 '.$client->telephone : '—' }}
            </td>

            {{-- Email --}}
            <td style="color:#374151">
                {{ $client->email ?? '—' }}
            </td>

            {{-- Adresse --}}
            <td style="color:#9CA3AF;font-size:0.82rem">
                {{ Str::limit($client->adresse ?? '—', 25) }}
            </td>

            {{-- Ventes --}}
            <td class="text-center">
                <span style="background:#F3F4F6;color:#374151;
                             border-radius:20px;padding:3px 10px;
                             font-size:0.78rem;font-weight:600">
                    {{ $client->ventes_count }}
                </span>
            </td>

            {{-- Actions --}}
            <td>
                <div class="d-flex gap-1 justify-content-end">

                    {{-- Voir --}}
                    <a href="{{ route('clients.show', $client) }}"
                       class="btn-action view" title="Voir détail">
                        <i class="bi bi-eye"></i>
                    </a>

                    {{-- Modifier → ouvre la modal --}}
                    <button type="button"
                            class="btn-action edit"
                            title="Modifier"
                            onclick="ouvrirModalModification(
                                {{ $client->id }},
                                '{{ addslashes($client->nom) }}',
                                '{{ $client->type }}',
                                '{{ $client->telephone ?? '' }}',
                                '{{ $client->email ?? '' }}',
                                '{{ addslashes($client->adresse ?? '') }}',
                                {{ $client->exonere_tva ? 1 : 0 }},
                                '{{ $client->numero_exoneration ?? '' }}'
                            )">
                        <i class="bi bi-pencil"></i>
                    </button>

                    {{-- Supprimer → ouvre la modal --}}
                    <button type="button"
                            class="btn-action del"
                            title="Supprimer"
                            onclick="ouvrirModalSuppression(
                                {{ $client->id }},
                                '{{ addslashes($client->nom) }}'
                            )">
                        <i class="bi bi-trash"></i>
                    </button>

                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-5">
                <div>
                    <i class="bi bi-people"
                       style="font-size:2.5rem;display:block;
                              margin-bottom:12px;color:#D1D5DB"></i>
                    <div style="font-size:0.875rem;color:#9CA3AF">
                        Aucun client trouvé
                    </div>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>