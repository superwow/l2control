		</div><!-- /.main-card -->
	</div><!-- /.container -->

	<!-- Footer -->
	<div class="footer-moon">
		<div class="container">
			<p class="mb-1">
				<a href="https://l2moon.ru/" class="me-3"><i class="bi bi-globe me-1"></i>L2Moon.ru</a>
				<a href="https://l2moon.ru/" class="me-3"><i class="bi bi-discord me-1"></i>Discord</a>
			</p>
			<p class="mb-0">&copy; 2007 &mdash; 2025 L2Moon. Lineage II &reg; NCsoft</p>
		</div>
	</div>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
			integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

	{if isset($image)}{literal}
	<script>
	function reloadImage(img) {
		document.images["L_image"].src = document.images["L_image"].src.split('?')[0] + "?" + new Date().getTime();
	}
	</script>
	{/literal}{/if}
</body>
</html>
