import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import animate from "tailwindcss-animate";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./Modules/**/resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            // ── Typography ────────────────────────────────────────
            fontFamily: {
                // Inter Tight sebagai font utama — menggantikan Inter biasa
                sans: ['"Inter Tight"', ...defaultTheme.fontFamily.sans],
            },

            // ── Colors (CSS variable-based, shadcn pattern) ───────
            colors: {
                border: "hsl(var(--border))",
                input: "hsl(var(--input))",
                ring: "hsl(var(--ring))",
                background: "hsl(var(--background))",
                foreground: "hsl(var(--foreground))",

                primary: {
                    DEFAULT: "hsl(var(--primary))",
                    foreground: "hsl(var(--primary-foreground))",
                    50: "#F5F3FF",
                    100: "#EBE9FE",
                    200: "#D4D0F6",
                    300: "#A89BFC",
                    400: "#8266F5",
                    500: "#6B4FF4", // The explicit primary 500 equivalent
                },
                secondary: {
                    DEFAULT: "hsl(var(--secondary))",
                    foreground: "hsl(var(--secondary-foreground))",
                },
                destructive: {
                    DEFAULT: "hsl(var(--destructive))",
                    foreground: "hsl(var(--destructive-foreground))",
                    50: "#FFF0ED",
                    200: "#FF6B4A",
                    300: "#D94425",
                },
                muted: {
                    DEFAULT: "hsl(var(--muted))",
                    foreground: "hsl(var(--muted-foreground))",
                },
                accent: {
                    DEFAULT: "hsl(var(--accent))",
                    foreground: "hsl(var(--accent-foreground))",
                },
                popover: {
                    DEFAULT: "hsl(var(--popover))",
                    foreground: "hsl(var(--popover-foreground))",
                },
                card: {
                    DEFAULT: "hsl(var(--card))",
                    foreground: "hsl(var(--card-foreground))",
                },

                // ── Extra palette dari design system Figma ────────
                sky: {
                    50: "#E8F4FF",
                    200: "#49BCFF",
                    300: "#1A8CD8",
                },
                success: {
                    50: "#E6FBF0",
                    200: "#34D889",
                    300: "#0D9F5F",
                },
                warning: {
                    50: "#FFF9E6",
                    200: "#FFD23F",
                    300: "#C6930A",
                },
                error: {
                    50: "#FFF0ED",
                    600: "#D94425",
                },

                // ── Greyscale override ────────────────────────────
                grey: {
                    0: "#F6F8FB",
                    25: "#F3F6F9",
                    50: "#EEF0F5",
                    100: "#DDE1E8",
                    200: "#C6CBD2",
                    300: "#A1ADB8",
                    400: "#788297",
                    500: "#606B80",
                    600: "#455064",
                    700: "#272B38",
                    800: "#1A1B25",
                    900: "#0D0D12",
                },
            },

            // ── Border Radius (shadcn pattern) ────────────────────
            borderRadius: {
                lg: "var(--radius)",
                md: "calc(var(--radius) - 2px)",
                sm: "calc(var(--radius) - 4px)",
            },

            // ── Shadows (from design system) ──────────────────────
            boxShadow: {
                xs: "0 1px 2px rgba(22, 22, 43, 0.04)",
                sm: "0 1px 3px rgba(22, 22, 43, 0.06), 0 1px 2px rgba(22, 22, 43, 0.04)",
                md: "0 4px 8px -2px rgba(22, 22, 43, 0.06), 0 2px 4px -2px rgba(22, 22, 43, 0.04)",
                lg: "0 12px 20px -4px rgba(22, 22, 43, 0.08), 0 4px 8px -4px rgba(22, 22, 43, 0.04)",
                xl: "0 20px 25px -5px rgba(22, 22, 43, 0.10), 0 8px 10px -6px rgba(22, 22, 43, 0.04)",
            },

            // ── Keyframes (shadcn animations) ─────────────────────
            keyframes: {
                "accordion-down": {
                    from: { height: "0" },
                    to: { height: "var(--radix-accordion-content-height)" },
                },
                "accordion-up": {
                    from: { height: "var(--radix-accordion-content-height)" },
                    to: { height: "0" },
                },
                "fade-in": {
                    from: { opacity: "0", transform: "translateY(8px)" },
                    to: { opacity: "1", transform: "translateY(0)" },
                },
            },
            animation: {
                "accordion-down": "accordion-down 0.2s ease-out",
                "accordion-up": "accordion-up 0.2s ease-out",
                "fade-in": "fade-in 0.3s ease-out both",
            },
        },
    },

    plugins: [forms, animate],
};
