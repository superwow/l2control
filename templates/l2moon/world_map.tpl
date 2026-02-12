{include file="header_wide.tpl"}

			<h4 class="mb-3"><i class="bi bi-map-fill me-2" style="color: var(--moon-accent);"></i>{$vm.map_title}</h4>
			<p class="text-muted small">{$vm.map_desc}</p>

{if isset($error)}
			<div class="error">{$error}</div>
{/if}

			<!-- Towns Grid -->
			<div class="row g-3 mb-4">
{section name=t loop=$towns}
				<div class="col-6 col-md-4 col-lg-3">
					<div class="stat-card" style="text-align: left; padding: 1rem;">
						<div class="d-flex align-items-center mb-2">
							<i class="bi bi-geo-alt-fill me-2" style="color: var(--moon-accent);"></i>
							<strong style="font-size: 0.9rem;">{$towns[t].name}</strong>
						</div>
						<div class="small text-muted">
							<div>X: {$towns[t].x}</div>
							<div>Y: {$towns[t].y}</div>
							<div>Z: {$towns[t].z}</div>
						</div>
{if isset($towns[t].players_count)}
						<div class="mt-2">
							<span class="badge badge-online"><span class="online-indicator"></span>{$towns[t].players_count} {$vm.map_players}</span>
						</div>
{/if}
					</div>
				</div>
{/section}
			</div>

			<!-- Map Legend -->
			<div class="p-3 rounded" style="background: rgba(0,0,0,0.2); border: 1px solid var(--moon-border);">
				<h6 class="mb-2"><i class="bi bi-info-circle me-1"></i>{$vm.map_legend}</h6>
				<div class="row g-2 small">
					<div class="col-auto">
						<span class="badge badge-online me-1"><span class="online-indicator"></span></span> {$vm.map_legend_town}
					</div>
					<div class="col-auto">
						<i class="bi bi-geo-alt-fill me-1" style="color: var(--moon-accent);"></i> {$vm.map_legend_location}
					</div>
				</div>
			</div>

			<div class="text-center mt-3">
				<a href="./{$session_id}" class="btn btn-moon-outline">
					<i class="bi bi-arrow-left me-1"></i>{$vm.return}
				</a>
			</div>

{include file="footer.tpl"}
