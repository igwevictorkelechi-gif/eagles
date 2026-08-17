import app from "../_server/app";
import type { Env } from "../_server/types";

// Cloudflare Pages Functions catch-all: routes every /api/* request through Hono.
export const onRequest: PagesFunction<Env> = (context) => {
  return app.fetch(context.request, context.env, context as unknown as ExecutionContext);
};
