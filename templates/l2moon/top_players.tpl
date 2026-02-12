{include file="header_wide.tpl"}

			<h4 class="mb-3"><i class="bi bi-trophy-fill me-2" style="color: var(--moon-gold);"></i>{$vm.top_title}</h4>
			<p class="text-muted small">{$vm.top_desc}</p>

{if isset($error)}
			<div class="error">{$error}</div>
{/if}

			<!-- Top Players Table -->
			<div class="table-responsive">
				<table class="table table-moon">
					<thead>
						<tr>
							<th style="width: 60px;">#</th>
							<th>{$vm.top_name}</th>
							<th>{$vm.top_level}</th>
							<th>{$vm.top_class}</th>
							<th class="d-none d-md-table-cell">{$vm.top_clan}</th>
							<th>{$vm.top_status}</th>
						</tr>
					</thead>
					<tbody>
{section name=p loop=$players}
						<tr>
							<td>
{if $players[p].rank == 1}
								<span class="rank-1"><i class="bi bi-trophy-fill"></i> 1</span>
{elseif $players[p].rank == 2}
								<span class="rank-2"><i class="bi bi-award-fill"></i> 2</span>
{elseif $players[p].rank == 3}
								<span class="rank-3"><i class="bi bi-award"></i> 3</span>
{else}
								{$players[p].rank}
{/if}
							</td>
							<td>
								<strong>{$players[p].char_name}</strong>
							</td>
							<td>
								<span class="badge badge-moon">{$players[p].level}</span>
							</td>
							<td>
								<span class="badge badge-class">{$players[p].class_name}</span>
							</td>
							<td class="d-none d-md-table-cell">
{if $players[p].clan_name}
								{$players[p].clan_name}
{else}
								<span class="text-muted">&mdash;</span>
{/if}
							</td>
							<td>
{if $players[p].online}
								<span class="badge badge-online"><span class="online-indicator"></span>Online</span>
{else}
								<span class="badge badge-offline">Offline</span>
{/if}
							</td>
						</tr>
{/section}
					</tbody>
				</table>
			</div>

{if isset($pvp_players)}
			<hr>
			<h5 class="mb-3"><i class="bi bi-lightning-fill me-2" style="color: var(--moon-danger);"></i>{$vm.top_pvp_title}</h5>
			<div class="table-responsive">
				<table class="table table-moon">
					<thead>
						<tr>
							<th style="width: 60px;">#</th>
							<th>{$vm.top_name}</th>
							<th>PvP</th>
							<th>PK</th>
							<th>{$vm.top_class}</th>
						</tr>
					</thead>
					<tbody>
{section name=v loop=$pvp_players}
						<tr>
							<td>
{if $pvp_players[v].rank == 1}
								<span class="rank-1"><i class="bi bi-trophy-fill"></i> 1</span>
{elseif $pvp_players[v].rank == 2}
								<span class="rank-2"><i class="bi bi-award-fill"></i> 2</span>
{elseif $pvp_players[v].rank == 3}
								<span class="rank-3"><i class="bi bi-award"></i> 3</span>
{else}
								{$pvp_players[v].rank}
{/if}
							</td>
							<td><strong>{$pvp_players[v].char_name}</strong></td>
							<td><span class="badge badge-moon">{$pvp_players[v].pvpkills}</span></td>
							<td><span style="color: var(--moon-danger);">{$pvp_players[v].pkkills}</span></td>
							<td><span class="badge badge-class">{$pvp_players[v].class_name}</span></td>
						</tr>
{/section}
					</tbody>
				</table>
			</div>
{/if}

			<div class="text-center mt-3">
				<a href="./{$session_id}" class="btn btn-moon-outline">
					<i class="bi bi-arrow-left me-1"></i>{$vm.return}
				</a>
			</div>

{include file="footer.tpl"}
