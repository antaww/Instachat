import FetchFeed from "./fetch-feed.js";
import {showPopup} from "./popup.js";
import {updateCharacterCount} from "./RGB.js";

document.addEventListener('DOMContentLoaded', () => {
	const postId = document.querySelector('article.post-container').dataset.postId;
	const fetchFeed = new FetchFeed(`/comments?post-id=${postId}&offset=`, document.querySelector('.comments'));

	const commentChatArea = document.querySelector('.comment-chat-area');
	const commentCharacterCount = document.querySelector('.comment-chat-count');
	const commentChatButton = document.querySelector('.comment-chat-btn');

	fetchFeed.addScripts(elements => twemoji.parse(elements));

	document.addEventListener('click', async e => {
		const target = e.target;
		if (target.closest('.comment-share')) {
			const link = target.closest('.comment-share').dataset.link;
			await copyToClipboard(window.location.origin + link);
		}

		if (target.closest('.comment-replies.action-btn')) {
			console.log('replies');
			replyButton(target.closest('.comment-replies.action-btn'));
		}
	});

	function updateChatButton() {
		if (inputIsValid(commentChatArea)) commentChatButton.removeAttribute('disabled');
		else commentChatButton.setAttribute('disabled', '');
	}

	function inputIsValid(commentChatArea) {
		const regex = /^(?=.*[A-Za-z0-9]{2,})[\s\S]*$/;
		const text = commentChatArea.value.trim();
		const hasTwoAlphanumericChars = regex.test(text);

		if (!hasTwoAlphanumericChars) return false;

		return commentChatArea.value.length >= 2;
	}

	commentChatArea.addEventListener('input', () => {
		updateCharacterCount(commentChatArea.value, commentCharacterCount, 120);
		updateChatButton()
	});
});

/**
 * @param button {HTMLButtonElement}
 */
function replyButton(button) {
	const commentId = button.closest('form').querySelector('input[name="comment-id"]').value;
	const replyUsername = button.closest('.comment-container').querySelector('.comment-username').textContent;
	const reply = document.querySelector('.create-comment-reply');
	reply.innerHTML = '';

	const replySpan = document.createElement('span');
	replySpan.classList.add('subtitle');
	replySpan.innerHTML = `Répondre à <a class="create-comment-reply-author" href="#comment-${commentId}">${replyUsername}</a>`;

	const deleteSymbol = document.createElement('span');
	deleteSymbol.classList.add('material-symbols-outlined');
	deleteSymbol.classList.add('chat-delete-image-symbol');
	deleteSymbol.textContent = 'close';
	deleteSymbol.addEventListener('click', () => reply.innerHTML = '');

	const replyInput = document.createElement('input');
	replyInput.type = 'hidden';
	replyInput.name = 'reply-to';
	replyInput.value = commentId;
	reply.append(replySpan, deleteSymbol, replyInput);

	const commentChatArea = document.querySelector('.comment-chat-area');
	commentChatArea.focus();
}

async function copyToClipboard(text) {
	await navigator.clipboard.writeText(text);
	showPopup("Lien copié !");
}
