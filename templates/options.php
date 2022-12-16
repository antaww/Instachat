<?php
declare(strict_types=1);

use Controllers\Users\GetUser;
use Models\Background;
use Models\Color;
use Models\FontSize;
use Controllers\Users\GetUser;

$user_controller = new GetUser();

$title = 'Options';
$css[] = 'options.css';
$js = ['options.js', 'tabbed-menu.js', 'blocked-masked-list.js'];

ob_start();
global $connected_user, $blocked_users, $blocked_words;
require_once 'components/toolbar.php';
?>
	<main class="options">
		<div class="title">
			<h1>Paramètres</h1>
		</div>

		<div class="options-container">
			<div class="options-choices-container">
				<?php
				$options = [
					'Informations du compte' => 'Afficher les informations de votre compte (Ex: nom d\'utilisateur, adresse e-mail...)',
					'Changer de mot de passe' => 'Changer de mot de passe à tout moment.',
					'Masqués et bloqués' => 'Gérer les comptes et mots que vous avez bloqués ou masqués.',
					'Affichage' => "Gérer la taille de police et l'arrière-plan.",
					'Ressources supplémentaires' => 'Informations utiles sur les divers produits du service Instachat.',
				];

				foreach ($options as $option => $description) { ?>
					<div class="option">
						<div class="option-title">
							<h2><?= $option ?></h2>
							<p class="subtitle"><?= $description ?></p>
						</div>
						<span class="material-symbols-outlined arrow">navigate_next</span>
					</div>
					<?php
				} ?>

				<div class="option">
					<div class="option-title">
						<h2 class="danger">Supprimer le compte</h2>
					</div>
					<span class="material-symbols-outlined arrow">navigate_next</span>
				</div>
			</div>

			<div class="options-form">
				<form action="/update-user-information" class="options-group active" method="post">
					<label>Nom d'utilisateur
						<input
							autocomplete="username"
							maxlength="20"
							minlength="2"
							name="username"
							pattern="[\_\-a-zA-Z0-9]{2,20}"
							placeholder="Nom d'utilisateur"
							required
							type="text"
							value="<?= htmlspecialchars($connected_user->username) ?>"
						>
					</label>
					<label>Nom Affiché
						<input
							autocomplete="name"
							maxlength="30"
							minlength="2"
							name="display-name"
							pattern="[\_\-a-zA-Z0-9]{2,30}"
							placeholder="<?= htmlspecialchars($connected_user->username) ?>"
							type="text"
							value="<?= htmlspecialchars($connected_user->display_name ?? '') ?>"
						>
					</label>
					<label>Email
						<input
							autocomplete="email"
							maxlength="320"
							minlength="5"
							name="email"
							placeholder="Email"
							required
							type="email"
							value="<?= htmlspecialchars($connected_user->email) ?>"
						>
					</label>

					<h3>Création du compte</h3>
					<p class="subtitle">
						<?php
						format_date_time_full($connected_user->createdAt) ?>
					</p>

					<h3>Sexe</h3>
					<p class="subtitle">
						<?= ucwords($connected_user->gender) ?>
					</p>

					<h3>Date de naissance</h3>
					<p class="subtitle">
						<?php
						format_date_time($connected_user->birthDate) ?>
					</p>
					<button type="submit">Enregistrer</button>
				</form>

				<form action="/update-password" class="options-group" method="post">
					<input autocomplete="username" name="username" type="hidden" value="<?= htmlspecialchars($connected_user->username) ?>">

					<label>Ancien mot de passe
						<input autocomplete="current-password"
						       maxlength="64"
						       name="old-password"
						       minlength="4"
						       placeholder="Mot de passe"
						       required
						       type="password">
					</label>
					<label>Nouveau mot de passe
						<input autocomplete="new-password"
						       maxlength="64"
						       minlength="4"
						       id="new-password"
						       name="new-password"
						       placeholder="Nouveau mot de passe"
						       required
						       type="password">
					</label>
					<label>Confirmer le nouveau mot de passe
						<input autocomplete="new-password"
						       maxlength="64"
						       minlength="4"
						       id="confirm-password"
						       name="confirm-password"
						       placeholder="Confirmer nouveau mot de passe"
						       required
						       type="password">
					</label>

					<button type="submit">Confirmer</button>
				</form>

				<div class="options-group">
					<form class="block-word" action="/block-word" method="post">
						<label>Masquer un mot
							<input autocomplete="off"
							       maxlength="100"
							       minlength="2"
							       id="block-word"
							       name="blocked-word"
							       placeholder="Masquer un mot"
							       required
							       type="text">
						</label>
						<button type="submit">Confirmer</button>
					</form>
					<div class="tabbed-menu">
						<div class="selected tab masked" onclick="showTab(tab1)"><p>Mots masqués</p></div>
						<div class="tab blocked" onclick="showTab(tab2)"><p>Comptes bloqués</p></div>
					</div>
					<div class="content">
						<div class="masked-word-list">
							<?php
							if (count($blocked_words) > 0) {
								foreach ($blocked_words as $name => $value) {
									?>
									<div class="blocked-word">
										<p><?= htmlspecialchars($value->blockedWord) ?></p>
										<form action="/unblock-word" method="post">
											<input type="hidden" name="blocked-word" value="<?= htmlspecialchars($value->blockedWord) ?>">
											<button type="submit" class="material-symbols-outlined cancel" title="Débloquer cet utilisateur">close</button>
										</form>
									</div>
									<?php
								}
							} else {
								?>
								<h2 class="empty-list">Aucun mot masqué</h2>
								<?php
							}
							?>
						</div>
						<div class="blocked-user-list hidden">
							<?php
							if (count($blocked_users) > 0) {
								foreach ($blocked_users as $name => $value) {
									$blocked_user = $user_controller->execute($value->blockedId);
									?>
									<div class="blocked-user">
										<a href="/profile/<?= $blocked_user->username ?>">
											<img src="<?= $blocked_user->displayAvatar() ?>" alt="Avatar de <?= $blocked_user->username ?>">
											<div class="blocked_user_text">
												<p class="username"><?= htmlspecialchars($blocked_user->username) ?></p>
												<p class="date">Bloqué depuis <?php
													format_date_time_diff($value->blockedDate, 'le ') ?></p>
											</div>
										</a>
										<form action="/unblock-user" method="post">
											<input name="blocked_id" type="hidden" value="<?= $blocked_user->id ?>">
											<input name="redirect" type="hidden" value="/options">
											<button type="submit" class="material-symbols-outlined cancel" title="Débloquer cet utilisateur">close</button>
										</form>
									</div>
									<?php
								}
							} else {
								?>
								<h2 class="empty-list">Aucun utilisateur bloqué</h2>
								<?php
							}
							?>
						</div>
					</div>
				</div>

				<form action="/update-preferences" class="options-group" method="post">
					<h3>Police</h3>
					<div class="font-options">
						<p>aA</p>
						<div class="font-size">
							<input type="range" min="1" max="4" name="font-size" step="1" value="<?= $connected_user->fontSize->value ?>" list="font-steps">
							<div class="font-size-rounds">
								<?php
								foreach (FontSize::cases() as $font) {
									$selected = $font === $connected_user->fontSize ? ' selected' : '';
									echo <<<HTML
										<span class="font-size-round$selected"></span>
									HTML;
								}
								?>
							</div>
						</div>
						<p>aA</p>
					</div>

					<h3>Couleurs</h3>
					<div class="color-options">
						<?php
						$count = count(Color::cases());
						for ($i = 0; $i < $count; $i++) {
							$color = Color::cases()[$i];
							$selected = $color === $connected_user->color ? ' checked' : '';
							$name = $color->lowercaseName();
							echo <<<HTML
								<input id="$name" hidden name="color" type="radio" value="$i"$selected>
								<label class="$name" for="$name"></label>
							HTML;
						}
						?>
					</div>

					<h3>Arrière-plan</h3>
					<div class="background-options">
						<?php
						foreach (Background::cases() as $index => $background) {
							$is_current = $background === $connected_user->background;
							$selected = $is_current ? ' selected' : '';
							$checked = $is_current ? ' checked' : '';
							$name = strtolower($background->name);
							$french_name = Background::frenchName($background);
							echo <<<HTML
							<div class="background-option $name">
								<label>$french_name
									<input class="$name$selected" id="$name" name="background" type="radio" value="$index"$checked>
								</label>
							</div>
							HTML;
						}
						?>
					</div>

					<button type="submit">Enregistrer</button>
				</form>

				<div class="options-group">
					<h3>Liste des développeurs</h3>
					<ul class="dev-list">
						<?php

						class Dev {
							public function __construct(
								public string $name,
								public string $role,
								public string $github
							) {}
						}

						$devs = [
							new Dev('Pierre ROY', 'Lead Développeur', 'Ayfri'),
							new Dev('Antoine PIZZETTA', 'Développeur', 'antaww'),
							new Dev('Olivier MISTRAL', 'Développeur', 'Bahsiik'),
						];

						foreach ($devs as $dev) {
							echo <<<HTML
							<li class="dev">
								<img alt="$dev->name" class="dev-avatar" src="https://avatars.githubusercontent.com/$dev->github">
								<div class="dev-info">
									<h4>$dev->name</h4>
									<p class="subtitle">$dev->role</p>
								</div>
								<a class="avatar-github" href="https://github.com/$dev->github" target="_blank">
									<i class="fab fa-github"></i>
								</a>
							</li>
							HTML;
						}
						?>
					</ul>
				</div>

				<form action="/delete?type=user" class="options-group" method="post">
					<h3>Supprimer mon compte</h3>
					<p class="subtitle">Cette action est irreversible et supprimera tous vos chats, réactions et
						commentaires et vous déconnectera
						instantanément.</p>
					<p class="subtitle">Toutes vos informations seront supprimées et irrécupérables et votre
						@nomdutilisateur sera disponible pour tout le
						monde.</p>
					<label>Confirmer mot de passe
						<input autocomplete="current-password" name="password" maxlength="64" minlength="4" placeholder="Mot de passe"
						       required type="password">
					</label>
					<button type="submit" class="delete-account">Supprimer mon compte</button>
				</form>
			</div>
		</div>
	</main>
<?php
$content = ob_get_clean();
require_once 'layout.php';
