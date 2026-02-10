</div>
    <div class="footer">
        Copyleft 2007 - 2025
    </div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	{if isset($image)}{literal}<script>
	function reloadImage(img) {
		document.images["L_image"].src=document.images["L_image"].src+"?"+new Date();
	}
	</script>{/literal}{/if}
</body>
</html>
