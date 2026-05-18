import './bootstrap';
import Alpine from "alpinejs";

window.Alpine = Alpine;
Alpine.start();

// Livewire v4 manages Alpine.js completely (bundles + starts it).
// Do NOT import Alpine here — it causes "multiple instances of Alpine" errors.
// Register Alpine components in resources/js/alpine-components.js loaded via @vite.

