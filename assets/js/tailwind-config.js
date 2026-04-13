/**
 * Admin Panel - Global Configuration
 * Tailwind CSS Configuration for MediFlow Admin
 */

// Tailwind Configuration
if (typeof tailwind !== 'undefined') {
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    background: "#f7f9fb",
                    "surface-container-high": "#e6e8ea",
                    "on-error": "#ffffff",
                    "surface-dim": "#d8dadc",
                    "surface-container-lowest": "#ffffff",
                    error: "#ba1a1a",
                    "on-tertiary": "#ffffff",
                    "primary-fixed": "#d6e3ff",
                    "on-surface-variant": "#424752",
                    "inverse-surface": "#2d3133",
                    "surface-container-highest": "#e0e3e5",
                    "on-primary-fixed-variant": "#00468c",
                    "on-error-container": "#93000a",
                    "on-primary-container": "#dae5ff",
                    "on-secondary-container": "#475c80",
                    outline: "#727783",
                    primary: "#004d99",
                    "surface-bright": "#f7f9fb",
                    "on-secondary-fixed": "#021b3c",
                    "surface-variant": "#e0e3e5",
                    "primary-container": "#1565c0",
                    "on-primary-fixed": "#001b3d",
                    secondary: "#4a5f83",
                    "on-tertiary-fixed-variant": "#005049",
                    "error-container": "#ffdad6",
                    "surface-tint": "#005db7",
                    "surface-container": "#eceef0",
                    "tertiary-container": "#00736a",
                    "primary-fixed-dim": "#a9c7ff",
                    "outline-variant": "#c2c6d4",
                    "inverse-on-surface": "#eff1f3",
                    "on-tertiary-container": "#87f8ea",
                    surface: "#f7f9fb",
                    "on-primary": "#ffffff",
                    "on-surface": "#191c1e",
                    tertiary: "#005851",
                    "inverse-primary": "#a9c7ff",
                    "on-secondary": "#ffffff",
                    "tertiary-fixed": "#84f5e8",
                    "tertiary-fixed-dim": "#66d9cc",
                    "secondary-container": "#c0d5ff",
                    "on-background": "#191c1e",
                    "secondary-fixed": "#d6e3ff",
                    "on-secondary-fixed-variant": "#32476a",
                    "surface-container-low": "#f2f4f6",
                    "on-tertiary-fixed": "#00201d",
                    "secondary-fixed-dim": "#b2c7f1"
                },
                borderRadius: {
                    DEFAULT: "0.25rem",
                    lg: "0.5rem",
                    xl: "0.75rem",
                    full: "9999px"
                },
                fontFamily: {
                    headline: ["Manrope"],
                    body: ["Inter"],
                    label: ["Inter"]
                }
            }
        }
    };
}
