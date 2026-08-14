import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  // output: "export", -- Using next start instead (Node.js on cPanel)
  output: process.env.NEXT_OUTPUT_MODE === "standalone" ? "standalone" : undefined,
  compress: true,
  poweredByHeader: false,
  reactStrictMode: true,

  // Turbopack configuration (Next.js 16+)
  turbopack: {
    root: process.cwd(),
    // Enable persistent caching for faster rebuilds
    resolveExtensions: [".tsx", ".ts", ".jsx", ".js"],
  },

  // Development optimizations
  devIndicators: {
    position: "bottom-right",
  },

  // Fast refresh settings
  onDemandEntries: {
    // Period (in ms) where the server will keep pages in memory
    maxInactiveAge: 60 * 1000,
    // Number of pages that should be kept simultaneously without being disposed
    pagesBufferLength: 5,
  },

  // TypeScript config
  typescript: {
    ignoreBuildErrors: false,
    tsconfigPath: "./tsconfig.json",
  },

  // Image optimization
  images: {
    unoptimized: process.env.NODE_ENV === "development",
    formats: ["image/avif", "image/webp"],
    minimumCacheTTL: 60 * 60 * 24,
  },

  // Experimental features for better DX
  experimental: {
    // Optimize package imports for common libraries
    optimizePackageImports: [
      "lucide-react",
      "@radix-ui/react-icons",
      "recharts",
      "date-fns",
    ],
    // Enable React Compiler when ready
    // reactCompiler: true,
  },

  // Logging for debugging
  logging: {
    fetches: {
      fullUrl: true,
    },
  },

  // Redirect old URLs to new paths
  async redirects() {
    return [
      {
        source: "/admin/analytics/grade-configuration",
        destination: "/admin/evaluation-setup/grade-configuration",
        permanent: true,
      },
    ];
  },

  // Webpack custom config (only used when not using Turbopack)
  webpack: (config, { dev, isServer }) => {
    if (dev && !isServer) {
      config.optimization = {
        ...config.optimization,
        removeAvailableModules: false,
        removeEmptyChunks: false,
        splitChunks: false,
      };
    }
    return config;
  },
};

export default nextConfig;
