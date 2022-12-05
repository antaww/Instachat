<?php

use Model\Background;
use Model\Color;
use Model\FontSize;

$title = 'Options';
$css[] = 'options.css';
$js = ['options.js', 'tabbed-menu.js'];

ob_start();
global $connected_user;
require_once('toolbar.php');
?>
	<div class="options">
		<div class="title">
			<h1>Paramètres</h1>
		</div>

		<div class="options-container">
			<div class="options-choices-container">
				<?php
				$options = [
					'Informations du compte' => 'Afficher les informations de votre compte, comme votre numéro de téléphone et votre adresse email.',
					'Changer de mot de passe' => 'Changer de mot de passe à tout moment.',
					'Masquer et bloquer' => 'Gérer les comptes et mots que vous avez bloqués ou masqués.',
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
				<?php } ?>

				<div class="option">
					<div class="option-title">
						<h2 class="danger">Supprimer le compte</h2>
					</div>
					<span class="material-symbols-outlined arrow">navigate_next</span>
				</div>
			</div>

			<div class="options-form">
				<form action="/options" class="options-group active" data-option-number="0" method="post">
					<label>Nom d'utilisateur
						<input
							autocomplete="username"
							maxlength="20"
							minlength="2"
							pattern="[\_\-a-zA-Z0-9]{2,20}"
							placeholder="Nom d'utilisateur"
							required
							type="text"
							value="<?= $connected_user->username ?>"
						>
					</label>
					<label>Nom Affiché
						<input
							autocomplete="name"
							maxlength="48"
							minlength="2"
							pattern="[\_\-a-zA-Z0-9]{2,48}"
							placeholder="Nom Affiché"
							required
							type="text"
							value="<?= $connected_user->getDisplayOrUsername() ?>"
						>
					</label>
					<label>Email
						<input
							autocomplete="email"
							maxlength="320"
							minlength="5"
							placeholder="Email"
							required
							type="email"
							value="<?= $connected_user->email ?>"
						>
					</label>

					<?php
					$date_formatter = IntlDateFormatter::create(
						'fr_FR',
						IntlDateFormatter::FULL,
						IntlDateFormatter::FULL,
						'Europe/Paris',
						IntlDateFormatter::GREGORIAN,
						'd MMMM yyyy à H:mm:ss'
					);

					$date_only_formatter = IntlDateFormatter::create(
						'fr_FR',
						IntlDateFormatter::FULL,
						IntlDateFormatter::FULL,
						'Europe/Paris',
						IntlDateFormatter::GREGORIAN,
						'd MMMM yyyy'
					);
					?>

					<h3>Création du compte</h3>
					<p class="subtitle">
						<?= $date_formatter->format($connected_user->created_at) ?>
					</p>

					<h3>Sexe</h3>
					<p class="subtitle">
						<?= ucwords($connected_user->gender) ?>
					</p>

					<h3>Date de naissance</h3>
					<p class="subtitle">
						<?= $date_only_formatter->format($connected_user->birth_date) ?>
					</p>

					<button type="submit">Enregistrer</button>
				</form>

				<form action="/options" class="options-group" data-option-number="1" method="post">
					<input type="hidden" autocomplete="username" name="username" value="<?= $connected_user->username ?>">

					<label>Ancien mot de passe
						<input autocomplete="current-password" maxlength="64" minlength="8" placeholder="Mot de passe" required type="password">
					</label>
					<label>Nouveau mot de passe
						<input autocomplete="new-password" maxlength="64" minlength="8" placeholder="Nouveau mot de passe" required type="password">
					</label>
					<label>Confirmer le nouveau mot de passe
						<input autocomplete="new-password" maxlength="64" minlength="8" placeholder="Confirmer nouveau mot de passe" required type="password">
					</label>

					<button type="submit">Confirmer</button>
				</form>

				<div class="options-group" data-option-number="2">
					<div class="tabbed-menu">
						<div class="selected tab"><p>Masqués</p></div>
						<div class="tab"><p>Bloqués</p></div>
					</div>
					<div class="content">
						<form action="/mask" class="masked-words" method="post">
							<!-- TODO -->
						</form>
						<form action="/block" class="blocked-users" method="post">
							<!-- TODO -->
						</form>
					</div>
				</div>

				<form action="/options" class="options-group" data-option-number="3" method="post">
					<h3>Police</h3>
					<div class="font-options">
						<p>aA</p>
						<div class="font-size">
							<input type="range" min="1" max="2" step="0.333" value="1" list="font-steps">
							<div class="font-size-rounds">
								<?php
								foreach (FontSize::cases() as $font) {
									$selected = $font === $connected_user->font_size ? ' selected' : '';
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
						foreach (Color::cases() as $index => $color) {
							$selected = $color === $connected_user->color ? ' selected' : '';
							$name = strtolower($color->name);
							echo <<<HTML
								<input id="$name" hidden name="color" type="radio" value="$index">
								<label class="$name$selected" for="$name"></label>
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

				<div class="options-group" data-option-number="4">
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
							new Dev("Pierre ROY", "Lead Développeur", "Ayfri"),
							new Dev("Antoine PIZZETTA", "Développeur", "antaww"),
							new Dev("Olivier MISTRAL", "Développeur", "Bahsiik"),
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

				<form action="/delete" class="options-group" data-option-number="5" method="post">
					<h3>Supprimer mon compte</h3>
					<p class="subtitle">Cette action est irreversible et supprimera tous vos chats, réactions et commentaires et vous déconnectera
						instantanément.</p>
					<p class="subtitle">Toutes vos informations seront supprimées et irrécupérables et votre @nomdutilisateur sera disponible pour tout le
						monde.</p>
					<label>Confirmer mot de passe
						<input autocomplete="current-password" maxlength="64" minlength="8" placeholder="Mot de passe" required type="password">
					</label>
					<button class="delete-account">Supprimer mon compte</button>
				</form>
			</div>
		</div>
	</div>
<?php
$content = ob_get_clean();
require_once('layout.php');