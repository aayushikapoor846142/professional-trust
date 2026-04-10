// Avatar Gradient Generator for Laravel
export class AvatarGradient {
    constructor(options = {}) {
        this.options = {
            saturation: options.saturation || 70,
            lightness: options.lightness || 50,
            angle: options.angle || 135,
            ...options
        };

        // Predefined beautiful gradients - Updated to match TrustVisory theme
        this.gradientPalette = [
            ['#667eea', '#764ba2'], // TrustVisory Purple
            ['#8b5cf6', '#a855f7'], // Deep Purple
            ['#6366f1', '#4f46e5'], // Indigo
            ['#7c3aed', '#6d28d9'], // Violet
            ['#5b21b6', '#7c3aed'], // Dark Purple
            ['#4c1d95', '#5b21b6'], // Very Dark Purple
            ['#3730a3', '#4338ca'], // Dark Indigo
            ['#1e40af', '#3b82f6'], // Professional Blue
            ['#0f766e', '#14b8a6'], // Teal
            ['#047857', '#10b981'], // Emerald
            ['#7f1d1d', '#dc2626'], // Dark Red
            ['#92400e', '#f59e0b'], // Amber
            ['#1f2937', '#374151'], // Slate
            ['#374151', '#6b7280'], // Gray
            ['#581c87', '#7c3aed'], // Purple to Violet
            ['#312e81', '#4338ca']  // Indigo to Blue
        ];

        // Department/Role based colors
        this.roleColors = {
            'admin': { primary: '#3b82f6', secondary: '#60a5fa' },
            'doctor': { primary: '#10b981', secondary: '#34d399' },
            'nurse': { primary: '#8b5cf6', secondary: '#a78bfa' },
            'staff': { primary: '#f59e0b', secondary: '#fbbf24' },
            'patient': { primary: '#06b6d4', secondary: '#22d3ee' },
            'default': { primary: '#6b7280', secondary: '#9ca3af' }
        };
    }

    // Generate hash from string with better distribution
    hashCode(str) {
        let hash = 0;
        if (str.length === 0) return hash;
        
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32-bit integer
        }
        return Math.abs(hash);
    }

    // Get initials from name
    getInitials(name) {
        return name
            .split(' ')
            .map(word => word[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    }

    // Method 1: Generate gradient from name with better color variation
    fromName(name) {
        const hash = this.hashCode(name);
        const initials = this.getInitials(name);
        
        // Use initials to create more variation
        const char1 = initials.charCodeAt(0) || 65; // Default to 'A' if empty
        const char2 = initials.charCodeAt(1) || 65;
        
        // Create distinct but professional hues based on characters and hash
        // Focus on purple, indigo, blue, teal ranges for TrustVisory theme
        const baseHues = [240, 250, 260, 270, 280, 290, 200, 210, 220, 180, 190];
        const hue1 = (baseHues[char1 % baseHues.length] + hash) % 360;
        const hue2 = (baseHues[char2 % baseHues.length] + hash + 180) % 360;
        
        // More muted, professional saturation and lightness values
        const sat1 = 45 + (hash % 25); // 45-70%
        const sat2 = 40 + ((hash >> 8) % 30); // 40-70%
        const light1 = 35 + (hash % 20); // 35-55%
        const light2 = 30 + ((hash >> 16) % 25); // 30-55%
        
        const color1 = `hsl(${hue1}, ${sat1}%, ${light1}%)`;
        const color2 = `hsl(${hue2}, ${sat2}%, ${light2}%)`;
        
        return `linear-gradient(${this.options.angle}deg, ${color1}, ${color2})`;
    }

    // Method 2: Get gradient from predefined palette with better distribution
    fromPalette(identifier) {
        const hash = this.hashCode(identifier);
        const index = Math.abs(hash) % this.gradientPalette.length;
        const [color1, color2] = this.gradientPalette[index];
        
        // Add slight rotation for more variation
        const rotation = (hash % 60) - 30;
        return `linear-gradient(${this.options.angle + rotation}deg, ${color1}, ${color2})`;
    }

    // Method 3: Get gradient by role with name-based variation
    fromRole(role, name = '') {
        const colors = this.roleColors[role.toLowerCase()] || this.roleColors.default;

        // Add significant variation based on name if provided
        if (name) {
            const hash = this.hashCode(name);
            const rotation = (hash % 60) - 30;
            const satVariation = (hash % 20) - 10;
            const lightVariation = (hash % 15) - 7;
            
            // Create slightly modified colors based on name
            const color1 = this.adjustColor(colors.primary, satVariation, lightVariation);
            const color2 = this.adjustColor(colors.secondary, -satVariation, -lightVariation);
            
            return `linear-gradient(${this.options.angle + rotation}deg, ${color1}, ${color2})`;
        }

        return `linear-gradient(${this.options.angle}deg, ${colors.primary}, ${colors.secondary})`;
    }

    // Helper method to adjust color saturation and lightness
    adjustColor(hexColor, satAdjust, lightAdjust) {
        // Convert hex to HSL, adjust, then convert back
        const rgb = this.hexToRgb(hexColor);
        const hsl = this.rgbToHsl(rgb.r, rgb.g, rgb.b);
        
        const newSat = Math.max(0, Math.min(100, hsl.s + satAdjust));
        const newLight = Math.max(0, Math.min(100, hsl.l + lightAdjust));
        
        return `hsl(${hsl.h}, ${newSat}%, ${newLight}%)`;
    }

    // Convert hex to RGB
    hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    // Convert RGB to HSL
    rgbToHsl(r, g, b) {
        r /= 255;
        g /= 255;
        b /= 255;

        const max = Math.max(r, g, b);
        const min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;

        if (max === min) {
            h = s = 0; // achromatic
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }

        return {
            h: h * 360,
            s: s * 100,
            l: l * 100
        };
    }

    // Method 4: Generate completely unique colors based on initials
    fromInitials(initials) {
        if (!initials || initials.length === 0) {
            initials = 'U'; // Default for unknown
        }
        
        const hash = this.hashCode(initials);
        const char1 = initials.charCodeAt(0);
        const char2 = initials.length > 1 ? initials.charCodeAt(1) : char1;
        
        // Use character codes to generate distinct but professional colors
        // Focus on purple, indigo, blue, teal, and slate ranges for TrustVisory theme
        const baseHues = [240, 250, 260, 270, 280, 290, 200, 210, 220, 180, 190]; // Purple, Blue, Teal ranges
        const hue1 = baseHues[char1 % baseHues.length];
        const hue2 = baseHues[char2 % baseHues.length];
        
        // More muted, professional saturation and lightness values
        const sat1 = 45 + (hash % 25); // 45-70% (more muted)
        const sat2 = 40 + ((hash >> 8) % 30); // 40-70% (more muted)
        const light1 = 35 + (hash % 20); // 35-55% (darker, more professional)
        const light2 = 30 + ((hash >> 16) % 25); // 30-55% (darker, more professional)
        
        const color1 = `hsl(${hue1}, ${sat1}%, ${light1}%)`;
        const color2 = `hsl(${hue2}, ${sat2}%, ${light2}%)`;
        
        return `linear-gradient(${this.options.angle}deg, ${color1}, ${color2})`;
    }

    // Create avatar element with improved color generation
    createAvatar(name, options = {}) {
        const {
            size = 56,
            fontSize = size * 0.4,
            borderRadius = 100,
            className = 'avatar',
            role = null,
            method = 'initials' // New option: 'initials', 'name', 'palette', 'role'
        } = options;

        const initials = this.getInitials(name);
        let gradient;
        
        switch (method) {
            case 'initials':
                gradient = this.fromInitials(initials);
                break;
            case 'name':
                gradient = this.fromName(name);
                break;
            case 'palette':
                gradient = this.fromPalette(name);
                break;
            case 'role':
                gradient = this.fromRole(role, name);
                break;
            default:
                gradient = this.fromInitials(initials);
        }

        const div = document.createElement('div');
        div.className = className;
        div.textContent = initials;
        div.style.cssText = `
            width: ${size}px;
            height: ${size}px;
            background: ${gradient};
            border-radius: ${borderRadius}px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: ${fontSize}px;
            user-select: none;
        `;

        return div;
    }

    // Initialize avatars on page load
    init(selector = '[data-avatar]') {
        document.querySelectorAll(selector).forEach(element => {
            const name = element.dataset.avatarName || element.textContent || 'User';
            const role = element.dataset.avatarRole || null;
            const size = parseInt(element.dataset.avatarSize) || 56;
            const method = element.dataset.avatarMethod || 'initials'; // Default to initials method
            const avatar = this.createAvatar(name, { role, size, method });
            element.innerHTML = '';
            element.appendChild(avatar);
        });
    }
}

// Auto-initialize if DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.avatarGradient = new AvatarGradient();
        window.avatarGradient.init();
    });
} else {
    window.avatarGradient = new AvatarGradient();
    window.avatarGradient.init();
}
