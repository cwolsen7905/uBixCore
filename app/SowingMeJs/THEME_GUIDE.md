# Theme System Guide

This guide explains how to use the centralized theme system in Sowing.Me.

## Overview

The application uses CSS custom properties (variables) for theming, allowing easy switching between light and dark modes.

## Available CSS Variables

### Background Colors
- `--color-bg-primary`: Main background color (white in light, black in dark)
- `--color-bg-secondary`: Secondary background (light gray in light, dark gray in dark)
- `--color-bg-tertiary`: Tertiary background for nested elements
- `--color-bg-hover`: Background color for hover states

### Text Colors
- `--color-text-primary`: Primary text color
- `--color-text-secondary`: Secondary text (less prominent)
- `--color-text-tertiary`: Tertiary text (even less prominent)
- `--color-text-placeholder`: Placeholder text color

### Border Colors
- `--color-border-light`: Light borders
- `--color-border-medium`: Medium borders
- `--color-border-dark`: Darker borders (for emphasis)

### Accent Colors
- `--color-accent-primary`: Primary accent color (blue)
- `--color-accent-hover`: Accent hover state
- `--color-accent-light`: Light accent background

### Shadows
- `--shadow-sm`: Small shadow
- `--shadow-md`: Medium shadow
- `--shadow-lg`: Large shadow

## Usage Examples

### Basic Component Styling

Instead of hardcoding colors:
```css
/* ❌ Don't do this */
.my-component {
  background: #fff;
  color: #333;
  border: 1px solid #e5e5e5;
}
```

Use CSS variables:
```css
/* ✅ Do this */
.my-component {
  background: var(--color-bg-primary);
  color: var(--color-text-primary);
  border: 1px solid var(--color-border-light);
}
```

### Card Components
```css
.card {
  background: var(--color-bg-secondary);
  border: 1px solid var(--color-border-light);
  border-radius: 8px;
  box-shadow: var(--shadow-lg);
}

.card:hover {
  background: var(--color-bg-hover);
  border-color: var(--color-border-medium);
}
```

### Buttons
```css
/* Primary button */
.btn-primary {
  background: var(--color-accent-primary);
  color: #fff;
  border: none;
}

.btn-primary:hover {
  background: var(--color-accent-hover);
}

/* Secondary button */
.btn-secondary {
  background: var(--color-bg-primary);
  color: var(--color-text-primary);
  border: 1px solid var(--color-border-medium);
}

.btn-secondary:hover {
  background: var(--color-bg-hover);
  border-color: var(--color-border-dark);
}
```

### Text Elements
```css
.title {
  color: var(--color-text-primary);
}

.subtitle {
  color: var(--color-text-secondary);
}

.caption {
  color: var(--color-text-tertiary);
}

input::placeholder {
  color: var(--color-text-placeholder);
}
```

## Theme Switching

### Using the ThemeToggle Component

Import and use the ThemeToggle component anywhere in your app:

```svelte
<script>
  import ThemeToggle from '$lib/ThemeToggle.svelte';
</script>

<ThemeToggle />
```

### Programmatic Theme Control

```javascript
import { theme, toggleTheme } from '$lib/themeStore.js';

// Toggle theme
toggleTheme();

// Set specific theme
theme.set('dark');

// Subscribe to theme changes
theme.subscribe(value => {
  console.log('Current theme:', value);
});
```

## Migration Guide

To migrate existing components to use the theme system:

1. **Identify hardcoded colors** in your CSS
2. **Replace with appropriate CSS variables**:
   - `#fff`, `#ffffff`, `white` → `var(--color-bg-primary)`
   - `#f9f9f9`, `#f5f5f5` → `var(--color-bg-secondary)` or `var(--color-bg-hover)`
   - `#333` → `var(--color-text-primary)`
   - `#666`, `#888` → `var(--color-text-secondary)` or `var(--color-text-tertiary)`
   - `#e5e5e5`, `#ddd` → `var(--color-border-light)`
   - `#4a90e2` → `var(--color-accent-primary)`

3. **Test both themes** to ensure proper visibility

## Color Mapping Reference

### Light Theme → Dark Theme
| Element | Light | Dark |
|---------|-------|------|
| Primary BG | `#fff` | `#000` |
| Secondary BG | `#f9f9f9` | `#1a1a1a` |
| Tertiary BG | `#f0f0f0` | `#111` |
| Hover BG | `#f5f5f5` | `#333` |
| Primary Text | `#333` | `#fff` |
| Secondary Text | `#666` | `#ccc` |
| Tertiary Text | `#888` | `#999` |
| Light Border | `#e5e5e5` | `#333` |
| Medium Border | `#d0d0d0` | `#444` |

## Best Practices

1. **Always use CSS variables** for colors and shadows
2. **Test in both themes** before committing changes
3. **Avoid opacity tricks** that may not work in both themes
4. **Use semantic variable names** (e.g., `--color-text-primary` not `--color-dark-gray`)
5. **Keep consistency** across similar UI elements

## Adding the Theme Toggle to Your Component

To add theme switching to a sidebar or settings page:

```svelte
<script>
  import ThemeToggle from '$lib/ThemeToggle.svelte';
</script>

<div class="settings">
  <h2>Appearance</h2>
  <ThemeToggle />
</div>
```
