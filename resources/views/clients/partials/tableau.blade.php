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
            $colors = ['#F97316','#3B82F6','#10B981','#8B5CF6','#EF4444','#F59E0B'];
            $color  = $colors[ord($initiale) % count($colors)];
        @endphp
        <tr>
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
                        </div>
                    </div>
                </div>
            </td>
            <td>
                @if($client->type == 'particulier')
                    <span class="badge-particulier">Particulier</span>
                @else
                    <span class="badge-entreprise">Entreprise</span>
                @endif
            </td>
            <td style="color:#374151">
                {{ $client->telephone ?? '—' }}
            </td>
            <td style="color:#374151">
                {{ $client->email ?? '—' }}
            </td>
            <td style="color:#9CA3AF;font-size:0.82rem">
                {{ Str::limit($client->adresse ?? '—', 25) }}
            </td>
            <td class="text-center">
                <span style="background:#F3F4F6;color:#374151;
                             border-radius:20px;padding:3px 10px;
                             font-size:0.78rem;font-weight:600">
                    {{ $client->ventes_count }}
                </span>
            </td>
            <td>
                <div class="d-flex gap-1 justify-content-end">
                    <a href="{{ route('clients.show', $client) }}"
                       class="btn-action view" title="Voir">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('clients.edit', $client) }}"
                       class="btn-action edit" title="Modifier">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('clients.destroy', $client) }}"
                          method="POST"
                          onsubmit="return confirm('Supprimer {{ $client->nom }} ?')">
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
            <td colspan="7" class="text-center py-5">
                <div style="color:#D1D5DB">
                    <i class="bi bi-people" style="font-size:2.5rem;display:block;margin-bottom:12px"></i>
                    <div style="font-size:0.875rem;color:#9CA3AF">Aucun client trouvé</div>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>