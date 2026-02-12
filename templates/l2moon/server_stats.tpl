{include file="header_wide.tpl"}

			<!-- Stats Grid -->
			<div class="row g-3 mb-4">
				<div class="col-6 col-md-3">
					<div class="stat-card">
						<div class="stat-value">{$stats.total_accounts}</div>
						<div class="stat-label">{$vm.stat_accounts}</div>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="stat-card success">
						<div class="stat-value">
							<span class="online-indicator"></span>{$stats.online_players}
						</div>
						<div class="stat-label">{$vm.stat_online}</div>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="stat-card gold">
						<div class="stat-value">{$stats.total_characters}</div>
						<div class="stat-label">{$vm.stat_characters}</div>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="stat-card">
						<div class="stat-value">{$stats.total_clans}</div>
						<div class="stat-label">{$vm.stat_clans}</div>
					</div>
				</div>
			</div>

			<!-- Class Distribution -->
			<h5 class="mb-3"><i class="bi bi-pie-chart me-2" style="color: var(--moon-accent);"></i>{$vm.stat_class_distribution}</h5>
			<div class="table-responsive mb-4">
				<table class="table table-moon">
					<thead>
						<tr>
							<th>{$vm.stat_class_name}</th>
							<th>{$vm.stat_count}</th>
							<th style="width: 50%;">{$vm.stat_percent}</th>
						</tr>
					</thead>
					<tbody>
{section name=c loop=$class_stats}
						<tr>
							<td>
								<span class="badge badge-class">{$class_stats[c].name}</span>
							</td>
							<td>{$class_stats[c].count}</td>
							<td>
								<div class="progress progress-moon">
									<div class="progress-bar" style="width: {$class_stats[c].percent}%"></div>
								</div>
								<small class="text-muted">{$class_stats[c].percent}%</small>
							</td>
						</tr>
{/section}
					</tbody>
				</table>
			</div>

			<!-- Race Distribution -->
{if isset($race_stats)}
			<h5 class="mb-3"><i class="bi bi-people me-2" style="color: var(--moon-gold);"></i>{$vm.stat_race_distribution}</h5>
			<div class="row g-3 mb-4">
{section name=r loop=$race_stats}
				<div class="col-6 col-md-4">
					<div class="stat-card">
						<div class="stat-value" style="font-size: 1.5rem;">{$race_stats[r].count}</div>
						<div class="stat-label">{$race_stats[r].name}</div>
						<div class="progress progress-moon mt-2">
							<div class="progress-bar" style="width: {$race_stats[r].percent}%"></div>
						</div>
					</div>
				</div>
{/section}
			</div>
{/if}

			<div class="text-center">
				<a href="./{$session_id}" class="btn btn-moon-outline">
					<i class="bi bi-arrow-left me-1"></i>{$vm.return}
				</a>
			</div>

{include file="footer.tpl"}
