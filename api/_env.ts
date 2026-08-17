// Deploy-time configuration injection.
//
// In git this stays EMPTY. Runtime configuration should come from real
// environment variables (set in the Vercel dashboard: DATABASE_URL, JWT_SECRET).
// When deploying via the file-push flow (which cannot set env vars), these
// values are overwritten in the deploy payload only — never committed here.
export const INJECTED: { DATABASE_URL?: string; JWT_SECRET?: string } = {};
