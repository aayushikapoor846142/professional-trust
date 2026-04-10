import './bootstrap';
import { AvatarGradient } from './avatarGradient';

// Initialize avatar gradient generator
window.AvatarGradient = AvatarGradient;

// Create global instance with custom options
window.avatarGradient = new AvatarGradient({
    saturation: 75,
    lightness: 50,
    angle: 135
});

// Auto-initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    window.avatarGradient.init();
});
