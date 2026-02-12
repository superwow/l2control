<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="{$vm_charset_type}">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{$vm_title}</title>
	<!-- Bootstrap 5 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
		  integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YcnS/1WR6zNx4JS2arFhfQVS8Ob/Gn7E06n" crossorigin="anonymous">
	<!-- Bootstrap Icons -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	<!-- Theme CSS -->
	<link href="./templates/l2moon/style.css?v=1" rel="stylesheet">
</head>
<body>
	<!-- Navbar -->
	<nav class="navbar navbar-expand-lg navbar-moon sticky-top">
		<div class="container">
			<a class="navbar-brand" href="https://l2moon.ru/">
				<i class="bi bi-moon-stars-fill me-2" style="color: var(--moon-accent);"></i>
				L2<span class="brand-accent">Moon</span>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav ms-auto">
					<li class="nav-item">
						<a class="nav-link" href="./{$session_id}">
							<i class="bi bi-house-door me-1"></i>{$vm_nav_home}
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="?action=server_stats{$session_url}">
							<i class="bi bi-bar-chart me-1"></i>{$vm_nav_stats}
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="?action=top_players{$session_url}">
							<i class="bi bi-trophy me-1"></i>{$vm_nav_top}
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="?action=world_map{$session_url}">
							<i class="bi bi-map me-1"></i>{$vm_nav_map}
						</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Main Content (wide) -->
	<div class="container">
		<div class="main-card main-card-wide fade-in-up">
			<div class="form-header">
				<h2>{$vm_title_page}</h2>
			</div>
