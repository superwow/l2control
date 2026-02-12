{include file="header.tpl"}

			<div class="mb-3">
				<h4><i class="bi bi-person-plus-fill me-2" style="color: var(--moon-gold);"></i>{$vm.new_account}</h4>
				<p class="text-muted small">{$vm.new_account_text}</p>
{if isset($image)}
				<p class="text-muted small"><i class="bi bi-info-circle me-1"></i>{$vm.image_control_desc}</p>
{/if}
			</div>

			<form name="create" method="POST" action="./{$session_id}">
{if isset($error)}
				<div class="error">{$error}</div>
{/if}
				<div class="mb-3">
					<label for="Luser" class="form-label">{$vm.account} <span style="color: var(--moon-danger);">*</span></label>
					<input type="text" class="form-control" id="Luser" name="Luser" value="{$vm.post_id}" autocomplete="off">
				</div>
				<div class="mb-3">
					<label for="Lpwd" class="form-label">{$vm.password} <span style="color: var(--moon-danger);">*</span></label>
					<input type="password" class="form-control" id="Lpwd" name="Lpwd" autocomplete="off">
				</div>
				<div class="mb-3">
					<label for="Lpwd2" class="form-label">{$vm.password2} <span style="color: var(--moon-danger);">*</span></label>
					<input type="password" class="form-control" id="Lpwd2" name="Lpwd2" autocomplete="off">
				</div>
				<div class="mb-3">
					<label for="Lemail" class="form-label">{$vm.email} <span style="color: var(--moon-danger);">*</span></label>
					<input type="email" class="form-control" id="Lemail" name="Lemail" value="{$vm.post_email}" autocomplete="off">
				</div>
{if isset($image)}
				<div class="mb-3 captcha">
					<label class="form-label">Captcha <span style="color: var(--moon-danger);">*</span></label>
					<div class="d-flex align-items-center gap-2">
						<img src="./img.php{$session_id}" id="L_image" onclick="reloadImage(this);" class="rounded">
						<input type="text" class="form-control" id="Limage" name="Limage" autocomplete="off"
							   placeholder="Code" style="max-width: 140px;">
					</div>
				</div>
{/if}
				<input type="hidden" name="action" value="registration">
				<hr>
				<div class="d-flex gap-2">
					<input type="button" onClick="document.location='./{$session_id}'"
						   class="btn btn-moon-outline" value="{$vm.return}">
					<button type="submit" class="btn btn-moon flex-grow-1">
						<i class="bi bi-person-check me-1"></i>{$vm.create_button}
					</button>
				</div>
			</form>

{include file="footer.tpl"}
