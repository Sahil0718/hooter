import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// --- Real-time: Laravel Echo + Pusher setup and popup helper ---
window.Pusher = Pusher;

// Initialize Echo only if Pusher env vars are present
const pushKey = import.meta.env.VITE_PUSHER_APP_KEY || import.meta.env.VITE_PUSHER_KEY || null;
if (pushKey) {
	try {
		window.Echo = new Echo({
			broadcaster: 'pusher',
			key: pushKey,
			cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || import.meta.env.VITE_PUSHER_CLUSTER || 'mt1',
			forceTLS: true,
		});

		// Listen to public 'hoots' channel
		window.Echo.channel('hoots')
			.listen('HootCreated', (e) => {
				if (e && e.hoot) {
					showRightSlidePopup(e.hoot);
				}
			});
	} catch (e) {
		// fail gracefully
		// console.warn('Echo/Pusher init failed', e);
	}
}

/**
 * Create and show a right-slide popup notification for a hoot.
 * @param {Object} hoot
 */
function showRightSlidePopup(hoot) {
	if (!hoot) return;

	const container = document.createElement('div');
	container.className = 'popup-notify';
	container.innerHTML = `
		<div class="title">${escapeHtml(hoot.user?.name ?? 'Anonymous')}</div>
		<div class="message">${escapeHtml(hoot.message)}</div>
	`;
	document.body.appendChild(container);

	// trigger animation
	requestAnimationFrame(() => container.classList.add('show'));

	// auto-dismiss after 5s
	setTimeout(() => {
		container.classList.remove('show');
		container.addEventListener('transitionend', () => container.remove(), { once: true });
	}, 5000);
}

function escapeHtml(unsafe) {
	if (!unsafe) return '';
	return String(unsafe)
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/"/g, '&quot;')
		.replace(/'/g, '&#039;');
}
