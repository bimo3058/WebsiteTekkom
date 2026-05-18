import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import animate from "tailwindcss-animate";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                    50: "#EBEDF6",
                    100: "#D0D6E9",
                    200: "#AAB4D5",
                    300: "#808FBE",
                    400: "#5D6DA2",
                    500: "#2A3A7C", // Warna Utama Navy Blue
                },
                secondary: {
                    DEFAULT: "hsl(var(--secondary))",
                    foreground: "hsl(var(--secondary-foreground))",
                },
                destructive: {
                    DEFAULT: "hsl(var(--destructive))",
                    foreground: "hsl(var(--destructive-foreground))",
                    50: "#FADAE1",
                    200: "#95122B",
                    300: "#710E21",
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
                    50: "#D1F0F9",
                    100: "#33CFFF",
                    200: "#106A97",
                    300: "#0C4D6E",
                },
                success: {
                    50: "#DDF2EE",
                    100: "#40C4AA",
                    200: "#287F6E",
                    300: "#174E43",
                },
                warning: {
                    50: "#F9ECCB",
                    100: "#FFBD4C",
                    200: "#956321",
                    300: "#5B3D1E",
                },
                error: {
                    50: "#FADAE1",
                    100: "#DF1C41",
                    200: "#95122B",
                    300: "#710E21",
                },

                // ── Greyscale override ────────────────────────────
                grey: {
                    0: "#F8F9FB",
                    25: "#F6F8FA",
                    50: "#ECEFF3",
                    100: "#DFE1E6",
                    200: "#C1C7CF",
                    300: "#A4ABB8",
                    400: "#808897",
                    500: "#666D80",
                    600: "#353849",
                    700: "#272835",
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
